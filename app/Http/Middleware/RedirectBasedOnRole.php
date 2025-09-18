<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectBasedOnRole
{
    /**
     * Gérer une demande entrante et rediriger selon le rôle.
<<<<<<< HEAD
     * 
     * Ce middleware peut être utilisé pour rediriger automatiquement
     * les utilisateurs vers leur dashboard approprié selon leur rôle.
=======
>>>>>>> 2022c95 (essaie commit)
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Vérifier si l'utilisateur est connecté
        if (!auth()->check()) {
            return $next($request);
        }

        $user = auth()->user();
        $currentRoute = $request->route()?->getName();

        // Si l'utilisateur accède à une route dashboard générique, 
        // le rediriger vers son dashboard spécifique
        if ($currentRoute === 'dashboard' || $currentRoute === 'home') {
<<<<<<< HEAD
            $redirectUrl = RouteServiceProvider::redirectTo();
=======
            $redirectUrl = RouteServiceProvider::redirectTo($user);
>>>>>>> 2022c95 (essaie commit)
            
            // Éviter les redirections en boucle
            if ($request->url() !== url($redirectUrl)) {
                return redirect($redirectUrl);
            }
        }

        // Vérifier si l'utilisateur a accès à la route demandée
        if ($currentRoute && !$this->userHasAccessToRoute($user, $currentRoute)) {
            // Rediriger vers le dashboard approprié avec un message d'erreur
<<<<<<< HEAD
            $redirectUrl = RouteServiceProvider::redirectTo();
=======
            $redirectUrl = RouteServiceProvider::redirectTo($user);
>>>>>>> 2022c95 (essaie commit)
            
            return redirect($redirectUrl)
                ->with('error', 'Vous n\'avez pas accès à cette section.');
        }

<<<<<<< HEAD
        // Vérifier si l'utilisateur est actif
        if (!$user->is_active) {
=======
        // Vérifier si l'utilisateur est actif (si la propriété existe)
        if (isset($user->is_active) && !$user->is_active) {
>>>>>>> 2022c95 (essaie commit)
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            return redirect('/login')
                ->with('error', 'Votre compte a été désactivé. Contactez l\'administrateur.');
        }

        return $next($request);
    }

    /**
     * Vérifier si l'utilisateur a accès à une route spécifique.
     */
    private function userHasAccessToRoute($user, string $routeName): bool
    {
        // Routes accessibles à tous les utilisateurs connectés
        $publicRoutes = [
            'profile.show',
            'profile.update',
            'profile.preferences',
            'profile.avatar',
            'profile.password.update',
            'notifications.index',
            'notifications.mark-as-read',
            'help.index',
            'help.section',
            'help.faq',
            'logout',
<<<<<<< HEAD
=======
            'dashboard', // Route dashboard générique
>>>>>>> 2022c95 (essaie commit)
        ];

        if (in_array($routeName, $publicRoutes)) {
            return true;
        }

        // Routes dashboard spécifiques
        $dashboardRoutes = [
<<<<<<< HEAD
            'dashboard.admin' => ['administrateur'],
            'dashboard.manager' => ['responsable_commercial'],
            'dashboard.sales' => ['vendeur', 'caissiere', 'responsable_commercial'],
            'dashboard.stock' => ['magasinier'],
            'dashboard.purchases' => ['responsable_achats'],
            'dashboard.accounting' => ['comptable'],
            'dashboard.general' => ['invite', 'stagiaire'],
        ];

        if (isset($dashboardRoutes[$routeName])) {
            return $user->hasAnyRole($dashboardRoutes[$routeName]);
=======
            'dashboard.admin' => ['administrateur', 'Administrateur'],
            'dashboard.manager' => ['responsable_commercial', 'Responsable Commercial'],
            'dashboard.sales' => ['vendeur', 'caissiere', 'responsable_commercial', 'Vendeur', 'Caissière'],
            'dashboard.stock' => ['magasinier', 'Magasinier'],
            'dashboard.purchases' => ['responsable_achats', 'Responsable Achats'],
            'dashboard.accounting' => ['comptable', 'Comptable'],
            'dashboard.general' => ['invite', 'stagiaire', 'Invité/Stagiaire'],
        ];

        if (isset($dashboardRoutes[$routeName])) {
            return $this->userHasAnyRole($user, $dashboardRoutes[$routeName]);
>>>>>>> 2022c95 (essaie commit)
        }

        // Permissions basées sur les modules
        $modulePermissions = [
            // Produits
            'products.' => ['manage_products', 'view_products'],
            'product-families.' => ['manage_products'],
            'active-principles.' => ['manage_products'],
            
            // Ventes
            'sales.' => ['manage_sales', 'view_sales'],
<<<<<<< HEAD
=======
            'pos.' => ['pos_access', 'manage_sales'],
>>>>>>> 2022c95 (essaie commit)
            
            // Stock
            'stock-movements.' => ['manage_stock', 'view_stock'],
            'inventories.' => ['manage_inventory', 'view_inventory'],
            'stock-regularizations.' => ['manage_inventory'],
            'warehouses.' => ['manage_inventory', 'view_inventory'],
            
            // Achats
            'purchase-orders.' => ['manage_purchases', 'view_purchases'],
            'delivery-notes.' => ['manage_purchases'],
            'return-notes.' => ['manage_purchases'],
            'suppliers.' => ['manage_suppliers', 'view_suppliers'],
            
            // Clients
            'customers.' => ['manage_customers', 'view_customers'],
            
            // Rapports
            'reports.' => ['view_reports'],
            
            // Administration
            'users.' => ['manage_users'],
            'roles.' => ['manage_users'],
            'permissions.' => ['manage_users'],
            'settings.' => ['manage_settings'],
            'system-settings.' => ['manage_settings'],
            
            // Maintenance
            'maintenance.' => ['manage_settings'],
            'backups.' => ['manage_settings'],
            'logs.' => ['view_logs'],
            'activity.' => ['view_logs'],
            
            // Alertes
            'alerts.' => ['view_alerts'],
        ];

        // Vérifier les permissions par module
        foreach ($modulePermissions as $routePrefix => $permissions) {
            if (str_starts_with($routeName, $routePrefix)) {
                foreach ($permissions as $permission) {
<<<<<<< HEAD
                    if ($user->hasPermission($permission)) {
=======
                    if ($this->userHasPermission($user, $permission)) {
>>>>>>> 2022c95 (essaie commit)
                        return true;
                    }
                }
                return false;
            }
        }

        // Administrateur a accès à tout
<<<<<<< HEAD
        if ($user->hasRole('administrateur')) {
            return true;
        }

        // Par défaut, refuser l'accès aux routes non définies
=======
        if ($this->userHasRole($user, 'administrateur') || $this->userHasRole($user, 'Administrateur')) {
            return true;
        }

        // Par défaut, permettre l'accès (peut être ajusté selon les besoins)
        return true;
    }

    /**
     * Vérifier si l'utilisateur a un rôle spécifique
     */
    private function userHasRole($user, string $role): bool
    {
        // Avec Spatie Permissions
        if (method_exists($user, 'hasRole')) {
            return $user->hasRole($role);
        }

        // Avec un champ role simple
        if (isset($user->role)) {
            return strtolower($user->role) === strtolower($role);
        }

>>>>>>> 2022c95 (essaie commit)
        return false;
    }

    /**
<<<<<<< HEAD
     * Vérifier si une route nécessite des permissions spéciales.
     */
    private function requiresSpecialPermission(string $routeName): bool
    {
        $specialRoutes = [
            'settings.',
            'system-settings.',
            'maintenance.',
            'backups.',
            'users.',
            'roles.',
            'permissions.',
        ];

        foreach ($specialRoutes as $route) {
            if (str_starts_with($routeName, $route)) {
                return true;
            }
        }

=======
     * Vérifier si l'utilisateur a au moins un des rôles
     */
    private function userHasAnyRole($user, array $roles): bool
    {
        foreach ($roles as $role) {
            if ($this->userHasRole($user, $role)) {
                return true;
            }
        }
>>>>>>> 2022c95 (essaie commit)
        return false;
    }

    /**
<<<<<<< HEAD
     * Obtenir un message d'erreur approprié selon la route.
     */
    private function getAccessDeniedMessage(string $routeName): string
    {
        if (str_starts_with($routeName, 'users.') || str_starts_with($routeName, 'roles.')) {
            return 'Accès réservé aux administrateurs.';
        }

        if (str_starts_with($routeName, 'settings.') || str_starts_with($routeName, 'maintenance.')) {
            return 'Vous n\'avez pas les permissions pour accéder aux paramètres système.';
        }

        if (str_starts_with($routeName, 'reports.')) {
            return 'Vous n\'avez pas accès aux rapports.';
        }

        return 'Vous n\'avez pas les permissions nécessaires pour accéder à cette section.';
=======
     * Vérifier si l'utilisateur a une permission
     */
    private function userHasPermission($user, string $permission): bool
    {
        // Avec Spatie Permissions
        if (method_exists($user, 'hasPermissionTo')) {
            return $user->hasPermissionTo($permission);
        }

        // Logique personnalisée selon les rôles
        $rolePermissions = [
            'administrateur' => ['*'], // Toutes permissions
            'Administrateur' => ['*'],
            'responsable_commercial' => ['manage_sales', 'view_sales', 'manage_customers', 'view_customers', 'view_reports'],
            'vendeur' => ['manage_sales', 'view_sales', 'manage_customers', 'view_customers', 'pos_access'],
            'magasinier' => ['manage_stock', 'view_stock', 'manage_inventory', 'view_inventory', 'manage_products'],
            'responsable_achats' => ['manage_purchases', 'view_purchases', 'manage_suppliers', 'view_suppliers'],
            'comptable' => ['view_reports', 'manage_customers', 'view_customers', 'view_sales'],
            'caissiere' => ['pos_access', 'manage_customers'],
        ];

        $userRole = strtolower($user->role ?? '');
        $userPermissions = $rolePermissions[$userRole] ?? [];

        return in_array('*', $userPermissions) || in_array($permission, $userPermissions);
>>>>>>> 2022c95 (essaie commit)
    }
}