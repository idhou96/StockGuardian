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
     * 
     * Ce middleware peut être utilisé pour rediriger automatiquement
     * les utilisateurs vers leur dashboard approprié selon leur rôle.
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
            $redirectUrl = RouteServiceProvider::redirectTo();
            
            // Éviter les redirections en boucle
            if ($request->url() !== url($redirectUrl)) {
                return redirect($redirectUrl);
            }
        }

        // Vérifier si l'utilisateur a accès à la route demandée
        if ($currentRoute && !$this->userHasAccessToRoute($user, $currentRoute)) {
            // Rediriger vers le dashboard approprié avec un message d'erreur
            $redirectUrl = RouteServiceProvider::redirectTo();
            
            return redirect($redirectUrl)
                ->with('error', 'Vous n\'avez pas accès à cette section.');
        }

        // Vérifier si l'utilisateur est actif
        if (!$user->is_active) {
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
        ];

        if (in_array($routeName, $publicRoutes)) {
            return true;
        }

        // Routes dashboard spécifiques
        $dashboardRoutes = [
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
        }

        // Permissions basées sur les modules
        $modulePermissions = [
            // Produits
            'products.' => ['manage_products', 'view_products'],
            'product-families.' => ['manage_products'],
            'active-principles.' => ['manage_products'],
            
            // Ventes
            'sales.' => ['manage_sales', 'view_sales'],
            
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
                    if ($user->hasPermission($permission)) {
                        return true;
                    }
                }
                return false;
            }
        }

        // Administrateur a accès à tout
        if ($user->hasRole('administrateur')) {
            return true;
        }

        // Par défaut, refuser l'accès aux routes non définies
        return false;
    }

    /**
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

        return false;
    }

    /**
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
    }
}