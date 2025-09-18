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
     */
    public const HOME = '/dashboard';

    /**
     * Configuration des routes de l'application.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();

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
     * Configuration des Rate Limiters
     */
    protected function configureRateLimiting(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by($request->email.$request->ip());
        });

        RateLimiter::for('admin', function (Request $request) {
            return Limit::perMinute(100)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('sensitive', function (Request $request) {
            return Limit::perMinute(10)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('global', function (Request $request) {
            return Limit::perMinute(1000)->by($request->ip());
        });
    }

    /**
     * Redirection après authentification selon le rôle
     * CORRIGÉ pour correspondre aux routes définies dans web.php
     */
    public static function redirectTo($user = null): string
    {
        if (!$user) {
            $user = auth()->user();
        }

        if (!$user) {
            return '/dashboard';
        }

        // Avec Spatie Permission
        if (method_exists($user, 'getRoleNames') && $user->getRoleNames()->count() > 0) {
            $role = $user->getRoleNames()->first();
            return match ($role) {
                'Administrateur', 'administrateur' => '/dashboard/admin',
                'Responsable Commercial', 'responsable_commercial' => '/dashboard/manager', // CORRIGÉ
                'Vendeur', 'vendeur' => '/dashboard/sales', // CORRIGÉ
                'Magasinier', 'magasinier' => '/dashboard/stock', // CORRIGÉ
                'Responsable Achats', 'responsable_achats' => '/dashboard/purchases', // CORRIGÉ
                'Comptable', 'comptable' => '/dashboard/accounting', // CORRIGÉ
                'Caissière', 'caissiere' => '/dashboard/sales', // CORRIGÉ - même que vendeur
                'Invité/Stagiaire', 'invite', 'stagiaire' => '/dashboard/general', // CORRIGÉ
                default => '/dashboard'
            };
        }

        // Avec champ role simple
        if (isset($user->role)) {
            return match (strtolower($user->role)) {
                'administrateur', 'admin' => '/dashboard/admin',
                'responsable_commercial', 'manager' => '/dashboard/manager',
                'vendeur', 'sales' => '/dashboard/sales',
                'magasinier', 'warehouse' => '/dashboard/stock',
                'responsable_achats', 'purchases' => '/dashboard/purchases',
                'comptable', 'accounting' => '/dashboard/accounting',
                'caissiere', 'cashier' => '/dashboard/sales',
                'invite', 'stagiaire', 'guest' => '/dashboard/general',
                default => '/dashboard'
            };
        }

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

        if (method_exists($user, 'getRoleNames')) {
            return $user->getRoleNames()->first();
        }

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
    }
}