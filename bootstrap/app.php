<?php

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
        | Middleware Personnalisés pour StockGuardian
        |--------------------------------------------------------------------------
        */
        
        // Enregistrement des alias de middleware personnalisés
        $middleware->alias([
            // Middleware de rôles et permissions
            'role' => \App\Http\Middleware\CheckRole::class,
            'permission' => \App\Http\Middleware\CheckPermission::class,
            'role.redirect' => \App\Http\Middleware\RedirectBasedOnRole::class,
            
            // Middleware de sécurité
            'check.user.status' => \App\Http\Middleware\CheckUserStatus::class,
            'audit.log' => \App\Http\Middleware\AuditLogMiddleware::class,
            'internal.api' => \App\Http\Middleware\InternalApiMiddleware::class,
            
            // Middleware de gestion des entrepôts
            'warehouse.access' => \App\Http\Middleware\CheckWarehouseAccess::class,
            
            // Middleware de maintenance
            'maintenance.mode' => \App\Http\Middleware\CheckMaintenanceMode::class,
            
            // Middleware de session sécurisée
            'secure.session' => \App\Http\Middleware\SecureSessionMiddleware::class,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Middleware Globaux
        |--------------------------------------------------------------------------
        */
        
        // Middleware appliqués à toutes les requêtes
        $middleware->append([
            // Middleware de sécurité de base
            \App\Http\Middleware\TrustHosts::class,
            \App\Http\Middleware\TrustProxies::class,
            \Illuminate\Http\Middleware\HandleCors::class,
            \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
            \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
            \App\Http\Middleware\TrimStrings::class,
            \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Groupes de Middleware Web
        |--------------------------------------------------------------------------
        */
        
        // Middleware pour les routes web avec gestion de session
        $middleware->web(append: [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            
            // Middleware personnalisés pour StockGuardian
            'audit.log', // Log toutes les activités
            'maintenance.mode', // Vérifier le mode maintenance
        ]);

        /*
        |--------------------------------------------------------------------------
        | Groupes de Middleware API
        |--------------------------------------------------------------------------
        */
        
        // Middleware pour les routes API
        $middleware->api(append: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            \Illuminate\Routing\Middleware\ThrottleRequests::class.':api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            
            // Middleware API personnalisés
            'internal.api', // Vérification API interne
        ]);

        /*
        |--------------------------------------------------------------------------
        | Middleware d'Authentification Étendu
        |--------------------------------------------------------------------------
        */
        
        // Middleware pour les routes authentifiées avec redirection par rôle
        $middleware->group('auth.extended', [
            'auth',
            'verified',
            'check.user.status', // Vérifier si l'utilisateur est actif
            'role.redirect', // Redirection automatique par rôle
            'secure.session', // Sécurité session renforcée
        ]);

        /*
        |--------------------------------------------------------------------------
        | Middleware d'Administration
        |--------------------------------------------------------------------------
        */
        
        // Middleware pour les routes d'administration
        $middleware->group('admin', [
            'auth',
            'verified',
            'role:administrateur',
            'audit.log',
            'secure.session',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Middleware de Point de Vente
        |--------------------------------------------------------------------------
        */
        
        // Middleware pour le point de vente
        $middleware->group('pos', [
            'auth',
            'verified',
            'role:vendeur|caissiere|responsable_commercial',
            'warehouse.access',
            'secure.session',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Middleware de Gestion Stock
        |--------------------------------------------------------------------------
        */
        
        // Middleware pour la gestion de stock
        $middleware->group('stock', [
            'auth',
            'verified',
            'role:magasinier|administrateur',
            'warehouse.access',
            'audit.log',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Priorité des Middleware
        |--------------------------------------------------------------------------
        */
        
        // Définir la priorité d'exécution des middleware
        $middleware->priority([
            \App\Http\Middleware\TrustHosts::class,
            \App\Http\Middleware\TrustProxies::class,
            \Illuminate\Http\Middleware\HandleCors::class,
            \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
            \App\Http\Middleware\CheckMaintenanceMode::class,
            \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
            \App\Http\Middleware\TrimStrings::class,
            \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Routing\Middleware\ThrottleRequests::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Auth\Middleware\Authenticate::class,
            \Illuminate\Auth\Middleware\Authorize::class,
            \App\Http\Middleware\CheckUserStatus::class,
            \App\Http\Middleware\CheckRole::class,
            \App\Http\Middleware\CheckPermission::class,
            \App\Http\Middleware\RedirectBasedOnRole::class,
            \App\Http\Middleware\CheckWarehouseAccess::class,
            \App\Http\Middleware\AuditLogMiddleware::class,
            \App\Http\Middleware\SecureSessionMiddleware::class,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Middleware Conditionnels
        |--------------------------------------------------------------------------
        */
        
        // Middleware appliqués conditionnellement selon l'environnement
        if (app()->environment('production')) {
            $middleware->append([
                \App\Http\Middleware\ForceHttps::class,
                \App\Http\Middleware\SecurityHeaders::class,
            ]);
        }

        if (app()->environment(['local', 'testing'])) {
            $middleware->append([
                \App\Http\Middleware\DebugBarMiddleware::class,
            ]);
        }
    })
    ->withExceptions(function (Exceptions $exceptions) {
        
        /*
        |--------------------------------------------------------------------------
        | Gestion d'Exceptions Personnalisée pour StockGuardian
        |--------------------------------------------------------------------------
        */
        
        // Gestion des erreurs d'authentification
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Non authentifié.',
                    'redirect' => route('login'),
                ], 401);
            }

            return redirect()->guest(route('login'))
                           ->with('error', 'Vous devez être connecté pour accéder à cette page.');
        });

        // Gestion des erreurs d'autorisation
        $exceptions->render(function (\Illuminate\Auth\Access\AuthorizationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Action non autorisée.',
                    'redirect' => \App\Providers\RouteServiceProvider::redirectTo(),
                ], 403);
            }

            $redirectUrl = \App\Providers\RouteServiceProvider::redirectTo();
            
            return redirect($redirectUrl)
                         ->with('error', 'Vous n\'avez pas les permissions nécessaires pour cette action.');
        });

        // Gestion des erreurs de validation
        $exceptions->render(function (\Illuminate\Validation\ValidationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Données invalides.',
                    'errors' => $e->errors(),
                ], 422);
            }

            return redirect()->back()
                           ->withErrors($e->errors())
                           ->withInput();
        });

        // Gestion des erreurs de rate limiting
        $exceptions->render(function (\Illuminate\Http\Exceptions\ThrottleRequestsException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Trop de tentatives. Réessayez plus tard.',
                    'retry_after' => $e->getHeaders()['Retry-After'] ?? 60,
                ], 429);
            }

            return redirect()->back()
                           ->with('error', 'Trop de tentatives. Veuillez patienter avant de réessayer.');
        });

        // Gestion des erreurs 404
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Ressource non trouvée.',
                ], 404);
            }

            return response()->view('errors.404', [], 404);
        });

        // Gestion des erreurs 500
        $exceptions->render(function (\Throwable $e, $request) {
            if (app()->environment('production') && !($e instanceof \Illuminate\Validation\ValidationException)) {
                
                // Log l'erreur avec plus de détails
                \Log::error('Application Error', [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'user_id' => auth()->id(),
                    'url' => $request->fullUrl(),
                    'method' => $request->method(),
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'trace' => $e->getTraceAsString(),
                ]);

                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Une erreur interne s\'est produite.',
                        'support' => 'Contactez le support technique si le problème persiste.',
                    ], 500);
                }

                return response()->view('errors.500', [], 500);
            }
        });

        /*
        |--------------------------------------------------------------------------
        | Reporting d'Exceptions Personnalisé
        |--------------------------------------------------------------------------
        */
        
        // Ne pas reporter certaines exceptions
        $exceptions->dontReport([
            \Illuminate\Auth\AuthenticationException::class,
            \Illuminate\Auth\Access\AuthorizationException::class,
            \Symfony\Component\HttpKernel\Exception\HttpException::class,
            \Illuminate\Database\Eloquent\ModelNotFoundException::class,
            \Illuminate\Validation\ValidationException::class,
        ]);

        // Reporter avec contexte supplémentaire
        $exceptions->reportable(function (\Throwable $e) {
            if (app()->bound('sentry')) {
                app('sentry')->captureException($e);
            }
        });
    })
    ->create();