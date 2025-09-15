<?php
// bootstrap/app.php - Configuration Laravel 11+

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        
        /*
        |--------------------------------------------------------------------------
        | MIDDLEWARE PERSONNALISÉS STOCKGUARDIAN
        |--------------------------------------------------------------------------
        */
        
        // Enregistrement des middleware personnalisés
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
            'permission' => \App\Http\Middleware\CheckPermission::class,
            'warehouse.access' => \App\Http\Middleware\CheckWarehouseAccess::class,
            'api.internal' => \App\Http\Middleware\InternalApiMiddleware::class,
            'audit.log' => \App\Http\Middleware\AuditLogMiddleware::class,
        ]);

        /*
        |--------------------------------------------------------------------------
        | GROUPES DE MIDDLEWARE
        |--------------------------------------------------------------------------
        */
        
        // Middleware pour les routes web avec audit
        $middleware->web(append: [
            \App\Http\Middleware\AuditLogMiddleware::class,
        ]);

        // Middleware pour les API internes
        $middleware->group('api.internal', [
            'throttle:60,1',
            \App\Http\Middleware\InternalApiMiddleware::class,
        ]);

        // Middleware pour les administrateurs
        $middleware->group('admin', [
            'auth:sanctum',
            'role:administrateur',
        ]);

        // Middleware pour la gestion des stocks
        $middleware->group('stock', [
            'auth:sanctum',
            'role:administrateur,magasinier,responsable_achats',
            'warehouse.access',
        ]);

        // Middleware pour les ventes
        $middleware->group('sales', [
            'auth:sanctum',
            'role:administrateur,responsable_commercial,vendeur,caissiere',
        ]);

        /*
        |--------------------------------------------------------------------------
        | MIDDLEWARE GLOBAUX
        |--------------------------------------------------------------------------
        */
        
        // Middleware globaux pour toutes les requêtes
        $middleware->append([
            \Illuminate\Http\Middleware\TrustProxies::class,
            \Illuminate\Http\Middleware\HandleCors::class,
        ]);

        /*
        |--------------------------------------------------------------------------
        | PRIORITÉS DES MIDDLEWARE
        |--------------------------------------------------------------------------
        */
        
        $middleware->priority([
            \Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests::class,
            \Illuminate\Cookie\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Contracts\Auth\Middleware\AuthenticatesRequests::class,
            \Illuminate\Routing\Middleware\ThrottleRequests::class,
            \Illuminate\Routing\Middleware\ThrottleRequestsWithRedis::class,
            \Illuminate\Contracts\Session\Middleware\AuthenticatesSessions::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \Illuminate\Auth\Middleware\Authorize::class,
            \App\Http\Middleware\CheckRole::class,
            \App\Http\Middleware\CheckPermission::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions) {
        /*
        |--------------------------------------------------------------------------
        | GESTION DES EXCEPTIONS PERSONNALISÉES
        |--------------------------------------------------------------------------
        */
        
        // Gestion des erreurs d'autorisation
        $exceptions->render(function (\Illuminate\Auth\Access\AuthorizationException $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'message' => 'Accès non autorisé.',
                    'error' => 'insufficient_permissions'
                ], 403);
            }
            
            return redirect()->route('dashboard')->with('error', 'Vous n\'avez pas les permissions nécessaires.');
        });

        // Gestion des erreurs de rôle
        $exceptions->render(function (\App\Exceptions\RoleException $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'message' => $e->getMessage(),
                    'error' => 'role_required'
                ], 403);
            }
            
            return redirect()->route('dashboard')->with('error', $e->getMessage());
        });

        // Gestion des erreurs de stock
        $exceptions->render(function (\App\Exceptions\InsufficientStockException $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'message' => $e->getMessage(),
                    'error' => 'insufficient_stock',
                    'available_stock' => $e->getAvailableStock()
                ], 422);
            }
            
            return back()->with('error', $e->getMessage());
        });

    })->create();
