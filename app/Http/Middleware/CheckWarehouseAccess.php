<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckWarehouseAccess
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        $warehouseId = $this->getWarehouseId($request);

        // Administrateur a accès à tous les dépôts
        if ($user->role === 'administrateur') {
            return $next($request);
        }

        // Vérifier l'accès au dépôt pour les autres rôles
        if ($warehouseId && !$user->hasWarehouseAccess($warehouseId)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Accès non autorisé à ce dépôt.',
                    'warehouse_id' => $warehouseId
                ], 403);
            }
            
            abort(403, 'Accès non autorisé à ce dépôt.');
        }

        return $next($request);
    }

    /**
     * Extraire l'ID du dépôt depuis la requête
     */
    private function getWarehouseId(Request $request): ?int
    {
        // Depuis les paramètres de route
        if ($warehouse = $request->route('warehouse')) {
            return is_object($warehouse) ? $warehouse->id : $warehouse;
        }

        // Depuis les paramètres de requête
        return $request->input('warehouse_id') ?? $request->input('warehouse');
    }
}