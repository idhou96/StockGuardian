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
     * Path vers la page d'accueil de l'application.
     * Sera utilisé par Laravel pour les redirections après authentification.
     */
    public const HOME = '/dashboard';

    /**
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

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));

            Route::middleware('web')
                ->group(base_path('routes/auth.php'));
        });
    }

    /**
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
    }
}