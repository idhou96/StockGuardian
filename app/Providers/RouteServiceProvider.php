<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
<<<<<<< HEAD
     * Path vers la page d'accueil de l'application.
     * Sera utilisé par Laravel pour les redirections après authentification.
=======
     * The path to your application's "home" route.
>>>>>>> 2022c95 (essaie commit)
     */
    public const HOME = '/dashboard';

    /**
<<<<<<< HEAD
     * Configuration des routes de l'application.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // Rate limiting pour les tentatives de connexion
        RateLimiter::for('login', function (Request $request) {
            $key = $request->ip();
            return [
                Limit::perMinute(5)->by($key),
                Limit::perMinute(3)->by($key.$request->input('email')),
            ];
        });
=======
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        // DÉFINITION DE TOUS LES RATE LIMITERS NÉCESSAIRES
        $this->configureRateLimiting();
>>>>>>> 2022c95 (essaie commit)

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
<<<<<<< HEAD

            Route::middleware('web')
                ->group(base_path('routes/auth.php'));
=======
>>>>>>> 2022c95 (essaie commit)
        });
    }

    /**
<<<<<<< HEAD
     * Retourne l'URL de redirection par défaut selon le rôle de l'utilisateur.
     * Utilisé après connexion réussie.
     */
    public static function redirectTo(): string
    {
        // Si l'utilisateur n'est pas connecté, rediriger vers login
        if (!auth()->check()) {
            return '/login';
        }

        $user = auth()->user();

        // Redirection intelligente selon le rôle principal de l'utilisateur
        return match (true) {
            $user->hasRole('administrateur') => '/dashboard/admin',
            $user->hasRole('responsable_commercial') => '/dashboard/manager',
            $user->hasRole(['vendeur', 'caissiere']) => '/dashboard/sales',
            $user->hasRole('magasinier') => '/dashboard/stock',
            $user->hasRole('responsable_achats') => '/dashboard/purchases',
            $user->hasRole('comptable') => '/dashboard/accounting',
            default => '/dashboard/general', // Pour invite/stagiaire et autres
        };
    }

    /**
     * Retourne l'URL de redirection spécifique pour un rôle donné.
     * Utile pour les redirections conditionnelles.
     */
    public static function getRedirectUrlForRole(string $role): string
    {
        return match ($role) {
            'administrateur' => '/dashboard/admin',
            'responsable_commercial' => '/dashboard/manager',
            'vendeur' => '/dashboard/sales',
            'caissiere' => '/dashboard/sales',
            'magasinier' => '/dashboard/stock',
            'responsable_achats' => '/dashboard/purchases',
            'comptable' => '/dashboard/accounting',
            'invite' => '/dashboard/general',
            'stagiaire' => '/dashboard/general',
            default => '/dashboard/general',
        };
    }

    /**
     * Détermine si l'utilisateur a accès à une route spécifique selon son rôle.
     */
    public static function hasAccessToRoute(string $routeName): bool
    {
        if (!auth()->check()) {
            return false;
        }

        $user = auth()->user();

        // Mapping des routes et rôles autorisés
        $routePermissions = [
            'dashboard.admin' => ['administrateur'],
            'dashboard.manager' => ['responsable_commercial'],
            'dashboard.sales' => ['vendeur', 'caissiere', 'responsable_commercial'],
            'dashboard.stock' => ['magasinier'],
            'dashboard.purchases' => ['responsable_achats'],
            'dashboard.accounting' => ['comptable'],
            'dashboard.general' => ['invite', 'stagiaire'],
        ];

        if (!isset($routePermissions[$routeName])) {
            return false;
        }

        return $user->hasAnyRole($routePermissions[$routeName]);
    }

    /**
     * Retourne la route de dashboard appropriée pour l'utilisateur connecté.
     */
    public static function getDashboardRouteName(): string
    {
        if (!auth()->check()) {
            return 'login';
        }

        $user = auth()->user();

        return match (true) {
            $user->hasRole('administrateur') => 'dashboard.admin',
            $user->hasRole('responsable_commercial') => 'dashboard.manager',
            $user->hasRole(['vendeur', 'caissiere']) => 'dashboard.sales',
            $user->hasRole('magasinier') => 'dashboard.stock',
            $user->hasRole('responsable_achats') => 'dashboard.purchases',
            $user->hasRole('comptable') => 'dashboard.accounting',
            default => 'dashboard.general',
        };
=======
     * Configuration des Rate Limiters
     */
    protected function configureRateLimiting(): void
    {
        // Rate limiter pour l'API
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // CORRECTION: Rate limiter 'login' - OBLIGATOIRE
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by($request->email.$request->ip());
        });

        // Rate limiter pour les actions administratives
        RateLimiter::for('admin', function (Request $request) {
            return Limit::perMinute(100)->by($request->user()?->id ?: $request->ip());
        });

        // Rate limiter pour les actions sensibles
        RateLimiter::for('sensitive', function (Request $request) {
            return Limit::perMinute(10)->by($request->user()?->id ?: $request->ip());
        });

        // Rate limiter pour les requêtes globales
        RateLimiter::for('global', function (Request $request) {
            return Limit::perMinute(1000)->by($request->ip());
        });
    }

    /**
     * Redirection après authentification selon le rôle
     */
    public static function redirectTo($user = null): string
    {
        // Si aucun utilisateur fourni, utiliser l'utilisateur connecté
        if (!$user) {
            $user = auth()->user();
        }

        // Si pas d'utilisateur connecté, retour dashboard par défaut
        if (!$user) {
            return '/dashboard';
        }

        // Si l'utilisateur a des rôles Spatie
        if (method_exists($user, 'getRoleNames') && $user->getRoleNames()->count() > 0) {
            $role = $user->getRoleNames()->first();
            
            return match ($role) {
                'Administrateur' => '/dashboard/admin',
                'administrateur' => '/dashboard/admin',
                'Responsable Commercial' => '/dashboard/commercial',
                'responsable_commercial' => '/dashboard/commercial',
                'Vendeur' => '/dashboard/vendeur',
                'vendeur' => '/dashboard/vendeur',
                'Magasinier' => '/dashboard/magasinier',
                'magasinier' => '/dashboard/magasinier',
                'Responsable Achats' => '/dashboard/achats',
                'responsable_achats' => '/dashboard/achats',
                'Comptable' => '/dashboard/comptable',
                'comptable' => '/dashboard/comptable',
                'Caissière' => '/pos', // Accès direct au point de vente
                'caissiere' => '/pos',
                'Invité/Stagiaire' => '/dashboard/invite',
                'invite' => '/dashboard/invite',
                'stagiaire' => '/dashboard/invite',
                default => '/dashboard'
            };
        }

        // Si l'utilisateur a un champ 'role' simple
        if (isset($user->role)) {
            return match (strtolower($user->role)) {
                'administrateur', 'admin' => '/dashboard/admin',
                'responsable_commercial', 'manager' => '/dashboard/commercial',
                'vendeur', 'sales' => '/dashboard/vendeur',
                'magasinier', 'warehouse' => '/dashboard/magasinier',
                'responsable_achats', 'purchases' => '/dashboard/achats',
                'comptable', 'accounting' => '/dashboard/comptable',
                'caissiere', 'cashier' => '/pos',
                'invite', 'stagiaire', 'guest' => '/dashboard/invite',
                default => '/dashboard'
            };
        }

        // Dashboard par défaut
        return '/dashboard';
    }

    /**
     * Obtenir le nom du rôle de l'utilisateur
     */
    public static function getUserRole($user = null): ?string
    {
        if (!$user) {
            $user = auth()->user();
        }

        if (!$user) {
            return null;
        }

        // Si Spatie Permissions est disponible
        if (method_exists($user, 'getRoleNames')) {
            return $user->getRoleNames()->first();
        }

        // Si l'utilisateur a un champ 'role' simple
        return $user->role ?? null;
    }

    /**
     * Vérifier si l'utilisateur a un rôle spécifique
     */
    public static function userHasRole($user, $role): bool
    {
        if (method_exists($user, 'hasRole')) {
            return $user->hasRole($role);
        }

        return strtolower($user->role ?? '') === strtolower($role);
>>>>>>> 2022c95 (essaie commit)
    }
}