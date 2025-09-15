<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InternalApiMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Vérifier que la requête provient de l'application elle-même
        if (!$request->ajax() && !$request->wantsJson()) {
            return response()->json([
                'message' => 'Accès API non autorisé.',
                'error' => 'invalid_request_type'
            ], 403);
        }

        // Vérifier l'authentification pour les API internes
        if (!auth()->check()) {
            return response()->json([
                'message' => 'Authentification requise.',
                'error' => 'unauthenticated'
            ], 401);
        }

        // Ajouter des headers de sécurité
        $response = $next($request);
        
        if ($response instanceof \Illuminate\Http\JsonResponse) {
            $response->headers->set('X-API-Type', 'internal');
            $response->headers->set('Cache-Control', 'no-cache, private');
        }

        return $response;
    }
}
