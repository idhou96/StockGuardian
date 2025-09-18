<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMaintenanceMode
{
    public function handle(Request $request, Closure $next): Response
    {
        // Vérifier si l'application est en mode maintenance
        if (app()->isDownForMaintenance()) {
            // Seuls les admins peuvent accéder
            if (!auth()->check() || strtolower(auth()->user()->role ?? '') !== 'administrateur') {
                return response()->view('maintenance', [], 503);
            }
        }

        return $next($request);
    }
}
