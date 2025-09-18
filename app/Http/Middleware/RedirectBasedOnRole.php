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
            $redirectUrl = RouteServiceProvider::redirectTo($user);
            
            // Éviter les redirections en boucle
            if ($request->url() !== url($redirectUrl)) {
                return redirect($redirectUrl);
            }
        }

        // Vérifier si l'utilisateur a accès à la route demandée
        if ($currentRoute && !$this->userHasAccessToRoute($user, $currentRoute)) {
            $redirectUrl = RouteServiceProvider::redirectTo($user);
            return redirect($redirectUrl)
                ->with('error', 'Vous n\'avez pas accès à cette section.');
        }

        // Vérifier si l'utilisateur est actif (si la propriété existe)
        if (isset($user->is_active) && !$user->is_active) {
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
     * CETTE MÉTHODE EST MAINTENANT DANS CE MIDDLEWARE (pas dans RouteServiceProvider)
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
            'dashboard',
        ];

        if (in_array($routeName, $publicRoutes)) {
            return true;
        }

        // Routes dashboard spécifiques
        $dashboardRoutes = [
            'dashboard.admin' => ['administrateur', 'Administrateur'],
            'dashboard.commercial' => ['responsable_commercial', 'Responsable Commercial'],
            'dashboard.vendeur' => ['vendeur', 'caissiere', 'responsable_commercial', 'Vendeur', 'Caissière'],
            'dashboard.magasinier' => ['magasinier', 'Magasinier'],
            'dashboard.achats' => ['responsable_achats', 'Responsable Achats'],
            'dashboard.comptable' => ['comptable', 'Comptable'],
            'dashboard.invite' => ['invite', 'stagiaire', 'Invité/Stagiaire'],
        ];

        if (isset($dashboardRoutes[$routeName])) {
            return $this->userHasAnyRole($user, $dashboardRoutes[$routeName]);
        }

        // Permissions basées sur les modules
        $modulePermissions = [
            'products.' => ['manage_products', 'view_products'],
            'product-families.' => ['manage_products'],
            'active-principles.' => ['manage_products'],
            'sales.' => ['manage_sales', 'view_sales'],
            'pos.' => ['pos_access', 'manage_sales'],
            'stock-movements.' => ['manage_stock', 'view_stock'],
            'inventories.' => ['manage_inventory', 'view_inventory'],
            'stock-regularizations.' => ['manage_inventory'],
            'warehouses.' => ['manage_inventory', 'view_inventory'],
            'purchase-orders.' => ['manage_purchases', 'view_purchases'],
            'delivery-notes.' => ['manage_purchases'],
            'return-notes.' => ['manage_purchases'],
            'suppliers.' => ['manage_suppliers', 'view_suppliers'],
            'customers.' => ['manage_customers', 'view_customers'],
            'reports.' => ['view_reports'],
            'users.' => ['manage_users'],
            'roles.' => ['manage_users'],
            'permissions.' => ['manage_users'],
            'settings.' => ['manage_settings'],
            'system-settings.' => ['manage_settings'],
            'maintenance.' => ['manage_settings'],
            'backups.' => ['manage_settings'],
            'logs.' => ['view_logs'],
            'activity.' => ['view_logs'],
            'alerts.' => ['view_alerts'],
        ];

        // Vérifier les permissions par module
        foreach ($modulePermissions as $routePrefix => $permissions) {
            if (str_starts_with($routeName, $routePrefix)) {
                foreach ($permissions as $permission) {
                    if ($this->userHasPermission($user, $permission)) {
                        return true;
                    }
                }
                return false;
            }
        }

        // Administrateur a accès à tout
        if ($this->userHasRole($user, 'administrateur') || $this->userHasRole($user, 'Administrateur')) {
            return true;
        }

        // Par défaut, permettre l'accès
        return true;
    }

    /**
     * Vérifier si l'utilisateur a au moins un des rôles
     */
    private function userHasAnyRole($user, array $roles): bool
    {
        foreach ($roles as $role) {
            if ($this->userHasRole($user, $role)) {
                return true;
            }
        }
        return false;
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

        return false;
    }

    /**
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
            'administrateur' => ['*'],
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
    }
}