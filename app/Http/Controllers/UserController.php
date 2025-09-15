<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Carbon\Carbon;

class UserController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->model = User::class;
        $this->modelName = 'Utilisateur';
        $this->viewPath = 'users';
    }

    /**
     * Afficher la liste des utilisateurs
     */
    public function index(Request $request)
    {
        if (!$this->checkRoles(['administrateur'])) {
            return $this->unauthorizedResponse();
        }

        $query = User::query();

        // Appliquer les filtres
        $this->applyUserFilters($query, $request);

        // Tri et pagination
        $pagination = $this->getPaginationData($request);
        
        $users = $query->orderBy($pagination['sortBy'], $pagination['sortDirection'])
                      ->paginate($pagination['perPage']);

        // Statistiques
        $stats = $this->getUserStats();

        return view('users.index', compact('users', 'stats'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        if (!$this->checkRoles(['administrateur'])) {
            return $this->unauthorizedResponse();
        }

        $roles = $this->getRoleOptions();
        $permissions = $this->getPermissionOptions();

        return view('users.create', compact('roles', 'permissions'));
    }

    /**
     * Enregistrer un nouvel utilisateur
     */
    public function store(Request $request)
    {
        if (!$this->checkRoles(['administrateur'])) {
            return $this->unauthorizedResponse();
        }

        $validated = $this->validateUser($request);

        try {
            DB::beginTransaction();

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'phone' => $validated['phone'],
                'address' => $validated['address'],
                'role' => $validated['role'],
                'is_active' => $validated['is_active'] ?? true,
                'permissions' => $validated['permissions'] ?? [],
            ]);

            DB::commit();

            $this->logActivity('create', $user, [], $user->toArray(), "Création de l'utilisateur {$user->name}");

            if ($request->expectsJson()) {
                return $this->successResponse($user, 'Utilisateur créé avec succès');
            }

            return $this->redirectWithSuccess('users.index', 'Utilisateur créé avec succès');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleDatabaseError($e, 'création de l\'utilisateur');
        }
    }

    /**
     * Afficher les détails d'un utilisateur
     */
    public function show(User $user)
    {
        if (!$this->checkRoles(['administrateur'])) {
            return $this->unauthorizedResponse();
        }

        // Statistiques de l'utilisateur
        $stats = $this->getUserDetailStats($user);

        // Activités récentes
        $recentActivities = $user->activityLogs()
                                ->orderBy('created_at', 'desc')
                                ->limit(20)
                                ->get();

        // Ventes récentes (si applicable)
        $recentSales = [];
        if ($user->canSell()) {
            $recentSales = $user->sales()
                               ->with(['customer', 'warehouse'])
                               ->orderBy('sale_date', 'desc')
                               ->limit(10)
                               ->get();
        }

        // Inventaires créés (si applicable)
        $recentInventories = [];
        if ($user->canManageStock()) {
            $recentInventories = $user->inventoriesCreated()
                                     ->with('warehouse')
                                     ->orderBy('created_at', 'desc')
                                     ->limit(10)
                                     ->get();
        }

        $this->logActivity('view', $user, [], [], "Consultation de l'utilisateur {$user->name}");

        return view('users.show', compact('user', 'stats', 'recentActivities', 'recentSales', 'recentInventories'));
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(User $user)
    {
        if (!$this->checkRoles(['administrateur'])) {
            return $this->unauthorizedResponse();
        }

        // Un utilisateur ne peut pas modifier son propre rôle
        if ($user->id === auth()->id() && request()->has('role')) {
            return $this->backWithError('Vous ne pouvez pas modifier votre propre rôle');
        }

        $roles = $this->getRoleOptions();
        $permissions = $this->getPermissionOptions();

        return view('users.edit', compact('user', 'roles', 'permissions'));
    }

    /**
     * Mettre à jour un utilisateur
     */
    public function update(Request $request, User $user)
    {
        if (!$this->checkRoles(['administrateur'])) {
            return $this->unauthorizedResponse();
        }

        // Un utilisateur ne peut pas modifier son propre rôle
        if ($user->id === auth()->id() && $request->has('role') && $request->role !== $user->role) {
            return $this->backWithError('Vous ne pouvez pas modifier votre propre rôle');
        }

        $validated = $this->validateUser($request, $user->id);
        $oldValues = $user->toArray();

        try {
            DB::beginTransaction();

            $updateData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'address' => $validated['address'],
                'role' => $validated['role'],
                'is_active' => $validated['is_active'] ?? true,
                'permissions' => $validated['permissions'] ?? [],
            ];

            // Mettre à jour le mot de passe seulement s'il est fourni
            if (!empty($validated['password'])) {
                $updateData['password'] = Hash::make($validated['password']);
            }

            $user->update($updateData);

            DB::commit();

            $this->logActivity('update', $user, $oldValues, $user->toArray(), "Modification de l'utilisateur {$user->name}");

            if ($request->expectsJson()) {
                return $this->successResponse($user, 'Utilisateur modifié avec succès');
            }

            return $this->redirectWithSuccess('users.index', 'Utilisateur modifié avec succès');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleDatabaseError($e, 'modification de l\'utilisateur');
        }
    }

    /**
     * Activer/Désactiver un utilisateur
     */
    public function toggleStatus(User $user)
    {
        if (!$this->checkRoles(['administrateur'])) {
            return $this->unauthorizedResponse();
        }

        // Un utilisateur ne peut pas se désactiver lui-même
        if ($user->id === auth()->id()) {
            return $this->errorResponse('Vous ne pouvez pas désactiver votre propre compte');
        }

        try {
            DB::beginTransaction();

            $oldStatus = $user->is_active;
            $user->is_active = !$user->is_active;
            $user->save();

            $action = $user->is_active ? 'activé' : 'désactivé';

            DB::commit();

            $this->logActivity('update', $user, 
                ['is_active' => $oldStatus], 
                ['is_active' => $user->is_active], 
                "Utilisateur {$user->name} {$action}"
            );

            if (request()->expectsJson()) {
                return $this->successResponse($user, "Utilisateur {$action} avec succès");
            }

            return $this->backWithSuccess("Utilisateur {$action} avec succès");

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleDatabaseError($e, 'changement de statut de l\'utilisateur');
        }
    }

    /**
     * Réinitialiser le mot de passe d'un utilisateur
     */
    public function resetPassword(Request $request, User $user)
    {
        if (!$this->checkRoles(['administrateur'])) {
            return $this->unauthorizedResponse();
        }

        $request->validate([
            'new_password' => ['required', 'confirmed', Password::min(8)],
        ]);

        try {
            DB::beginTransaction();

            $user->update([
                'password' => Hash::make($request->new_password),
            ]);

            DB::commit();

            $this->logActivity('update', $user, [], [], "Réinitialisation du mot de passe de {$user->name}");

            if ($request->expectsJson()) {
                return $this->successResponse(null, 'Mot de passe réinitialisé avec succès');
            }

            return $this->backWithSuccess('Mot de passe réinitialisé avec succès');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleDatabaseError($e, 'réinitialisation du mot de passe');
        }
    }

    /**
     * Profil de l'utilisateur connecté
     */
    public function profile()
    {
        $user = auth()->user();
        $stats = $this->getUserDetailStats($user);

        return view('users.profile', compact('user', 'stats'));
    }

    /**
     * Mettre à jour le profil de l'utilisateur connecté
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $oldValues = $user->toArray();
            $user->update($validated);

            DB::commit();

            $this->logActivity('update', $user, $oldValues, $user->toArray(), "Mise à jour du profil");

            if ($request->expectsJson()) {
                return $this->successResponse($user, 'Profil mis à jour avec succès');
            }

            return $this->backWithSuccess('Profil mis à jour avec succès');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleDatabaseError($e, 'mise à jour du profil');
        }
    }

    /**
     * Changer le mot de passe de l'utilisateur connecté
     */
    public function changePassword(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'current_password' => 'required',
            'new_password' => ['required', 'confirmed', Password::min(8)],
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return $this->backWithError('Le mot de passe actuel est incorrect');
        }

        try {
            DB::beginTransaction();

            $user->update([
                'password' => Hash::make($request->new_password),
            ]);

            DB::commit();

            $this->logActivity('update', $user, [], [], "Changement de mot de passe");

            if ($request->expectsJson()) {
                return $this->successResponse(null, 'Mot de passe modifié avec succès');
            }

            return $this->backWithSuccess('Mot de passe modifié avec succès');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleDatabaseError($e, 'changement de mot de passe');
        }
    }

    /**
     * Supprimer un utilisateur
     */
    public function destroy(User $user)
    {
        if (!$this->checkRoles(['administrateur'])) {
            return $this->unauthorizedResponse();
        }

        // Un utilisateur ne peut pas se supprimer lui-même
        if ($user->id === auth()->id()) {
            return $this->errorResponse('Vous ne pouvez pas supprimer votre propre compte');
        }

        try {
            // Vérifier si l'utilisateur peut être supprimé
            if ($user->sales()->exists() || $user->inventoriesCreated()->exists()) {
                return $this->errorResponse('Impossible de supprimer cet utilisateur car il a des données associées');
            }

            DB::beginTransaction();

            $userName = $user->name;
            $user->delete();

            DB::commit();

            $this->logActivity('delete', $user, $user->toArray(), [], "Suppression de l'utilisateur {$userName}");

            if (request()->expectsJson()) {
                return $this->successResponse(null, 'Utilisateur supprimé avec succès');
            }

            return $this->redirectWithSuccess('users.index', 'Utilisateur supprimé avec succès');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleDatabaseError($e, 'suppression de l\'utilisateur');
        }
    }

    /**
     * Historique d'activité d'un utilisateur
     */
    public function activities(User $user, Request $request)
    {
        if (!$this->checkRoles(['administrateur'])) {
            return $this->unauthorizedResponse();
        }

        $query = $user->activityLogs();

        // Filtres
        if ($action = $request->get('action')) {
            $query->where('action', $action);
        }

        if ($modelType = $request->get('model_type')) {
            $query->where('model_type', $modelType);
        }

        // Filtres par dates
        $dateFilters = $this->getDateFilters($request);
        if ($dateFilters['startDate']) {
            $query->where('created_at', '>=', $dateFilters['startDate']);
        }
        if ($dateFilters['endDate']) {
            $query->where('created_at', '<=', $dateFilters['endDate']);
        }

        $activities = $query->orderBy('created_at', 'desc')->paginate(50);

        return view('users.activities', compact('user', 'activities'));
    }

    /**
     * Exporter les utilisateurs
     */
    public function export(Request $request)
    {
        if (!$this->checkPermission('export_users')) {
            return $this->unauthorizedResponse();
        }

        $query = User::query();
        $this->applyUserFilters($query, $request);

        $users = $query->get();

        $headers = [
            'Nom', 'Email', 'Téléphone', 'Adresse', 'Rôle',
            'Statut', 'Dernière connexion', 'Date création'
        ];

        $data = $users->map(function($user) {
            return [
                $user->name,
                $user->email,
                $user->phone,
                $user->address,
                $user->role_label,
                $user->is_active ? 'Actif' : 'Inactif',
                $user->last_login_at?->format('d/m/Y H:i') ?? 'Jamais',
                $user->created_at->format('d/m/Y'),
            ];
        });

        $this->logActivity('export', null, [], [], 'Export de la liste des utilisateurs');

        return $this->exportToCsv($data, 'utilisateurs', $headers);
    }

    // Méthodes privées utilitaires

    private function applyUserFilters($query, Request $request)
    {
        // Recherche textuelle
        if ($search = $request->get('search')) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filtres par rôle
        if ($role = $request->get('role')) {
            $query->byRole($role);
        }

        // Filtres spéciaux
        switch ($request->get('filter')) {
            case 'active':
                $query->active();
                break;
            case 'inactive':
                $query->where('is_active', false);
                break;
            case 'recent_login':
                $query->where('last_login_at', '>=', Carbon::now()->subDays(7));
                break;
            case 'never_logged':
                $query->whereNull('last_login_at');
                break;
        }
    }

    private function validateUser(Request $request, $userId = null)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email' . ($userId ? ",$userId" : ''),
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'role' => 'required|in:administrateur,responsable_commercial,vendeur,magasinier,responsable_achats,comptable,caissiere,invite',
            'is_active' => 'boolean',
            'permissions' => 'nullable|array',
        ];

        // Mot de passe obligatoire à la création, optionnel à la modification
        if ($userId) {
            $rules['password'] = ['nullable', 'confirmed', Password::min(8)];
        } else {
            $rules['password'] = ['required', 'confirmed', Password::min(8)];
        }

        return $request->validate($rules, [
            'name.required' => 'Le nom est obligatoire',
            'email.required' => 'L\'email est obligatoire',
            'email.unique' => 'Cet email est déjà utilisé',
            'role.required' => 'Le rôle est obligatoire',
            'password.required' => 'Le mot de passe est obligatoire',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas',
        ]);
    }

    private function getRoleOptions(): array
    {
        return [
            'administrateur' => 'Administrateur',
            'responsable_commercial' => 'Responsable Commercial',
            'vendeur' => 'Vendeur/Agent Commercial',
            'magasinier' => 'Magasinier',
            'responsable_achats' => 'Responsable Achats',
            'comptable' => 'Comptable',
            'caissiere' => 'Caissière',
            'invite' => 'Invité/Stagiaire',
        ];
    }

    private function getPermissionOptions(): array
    {
        return [
            'view_dashboard' => 'Voir le tableau de bord',
            'manage_products' => 'Gérer les produits',
            'manage_stock' => 'Gérer le stock',
            'manage_sales' => 'Gérer les ventes',
            'manage_purchases' => 'Gérer les achats',
            'manage_customers' => 'Gérer les clients',
            'manage_suppliers' => 'Gérer les fournisseurs',
            'manage_invoices' => 'Gérer les factures',
            'manage_payments' => 'Gérer les paiements',
            'view_reports' => 'Voir les rapports',
            'export_data' => 'Exporter les données',
            'manage_users' => 'Gérer les utilisateurs',
            'manage_settings' => 'Gérer les paramètres',
        ];
    }

    private function getUserStats(): array
    {
        return [
            'total' => User::count(),
            'active' => User::active()->count(),
            'inactive' => User::where('is_active', false)->count(),
            'administrators' => User::byRole('administrateur')->count(),
            'sales_staff' => User::whereIn('role', ['responsable_commercial', 'vendeur', 'caissiere'])->count(),
            'warehouse_staff' => User::whereIn('role', ['magasinier', 'responsable_achats'])->count(),
            'recent_login' => User::where('last_login_at', '>=', Carbon::now()->subDays(7))->count(),
            'never_logged' => User::whereNull('last_login_at')->count(),
        ];
    }

    private function getUserDetailStats(User $user): array
    {
        $stats = [
            'total_activities' => $user->activityLogs()->count(),
            'activities_last_30_days' => $user->activityLogs()
                ->where('created_at', '>=', Carbon::now()->subDays(30))
                ->count(),
        ];

        // Statistiques spécifiques selon le rôle
        if ($user->canSell()) {
            $stats['total_sales'] = $user->sales()->count();
            $stats['sales_amount'] = $user->sales()->sum('total_ttc');
            $stats['sales_last_30_days'] = $user->sales()
                ->where('sale_date', '>=', Carbon::now()->subDays(30))
                ->count();
        }

        if ($user->canManageStock()) {
            $stats['inventories_created'] = $user->inventoriesCreated()->count();
            $stats['inventories_validated'] = $user->inventoriesValidated()->count();
        }

        if ($user->hasAnyRole(['administrateur', 'responsable_achats'])) {
            $stats['purchase_orders'] = $user->purchaseOrders()->count();
        }

        return $stats;
    }
}