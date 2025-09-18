<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            // RATE LIMITERS
            RateLimiter::for('api', fn(Request $request) => Limit::perMinute(60)->by($request->user()?->id ?: $request->ip()));
            RateLimiter::for('login', fn(Request $request) => Limit::perMinute(5)->by($request->email.$request->ip()));
            RateLimiter::for('global', fn(Request $request) => Limit::perMinute(1000)->by($request->ip()));
        }
    )
    ->withMiddleware(function (Middleware $middleware) {

        // ALIAS MIDDLEWARES DE BASE SEULEMENT
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
            'permission' => \App\Http\Middleware\CheckPermission::class,
        ]);

        // GROUPES DE MIDDLEWARES SANS REFERENCES AUX MIDDLEWARES MANQUANTS
        $middleware->group('web', [
            \Illuminate\Cookie\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);

        $middleware->group('api', [
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \Illuminate\Routing\Middleware\ThrottleRequests::class.':api',
        ]);

        // GROUPE AUTH.EXTENDED SANS LES MIDDLEWARES PROBLEMATIQUES
        $middleware->group('auth.extended', [
            'auth:web',
        ]);

        // GROUPES SIMPLIFIES
        $middleware->group('admin', [
            'auth:web',
            'role:administrateur',
        ]);

        $middleware->group('pos', [
            'auth:web',
            'role:vendeur,caissiere,responsable_commercial',
        ]);

        $middleware->group('stock', [
            'auth:web',
            'role:magasinier,administrateur',
        ]);

        $middleware->group('purchases', [
            'auth:web',
            'role:responsable_achats,administrateur',
        ]);

        $middleware->group('accounting', [
            'auth:web',
            'role:comptable,administrateur',
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions) {
        
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Non authentifié.'], 401);
            }
            return redirect()->guest(route('login'));
        });

    })
    ->create();