<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Log;

class AuditLogMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Logger les actions importantes après la réponse
        if (auth()->check() && $this->shouldLog($request, $response)) {
            try {
                $this->logActivity($request, $response);
            } catch (\Exception $e) {
                // Ne pas faire échouer la requête si le log échoue
                Log::error('Erreur lors du logging d\'activité', [
                    'error' => $e->getMessage(),
                    'user_id' => auth()->id(),
                    'route' => $request->route()?->getName()
                ]);
            }
        }

        return $response;
    }

    /**
     * Déterminer si l'action doit être loggée
     */
    private function shouldLog(Request $request, Response $response): bool
    {
        // Ne pas logger certaines routes
        $excludedRoutes = [
            'api.*',
            'livewire.*',
            '*.ajax',
            'dashboard.stats',
            'notifications.*'
        ];

        $routeName = $request->route()?->getName();
        
        foreach ($excludedRoutes as $pattern) {
            if ($routeName && fnmatch($pattern, $routeName)) {
                return false;
            }
        }

        // Logger seulement les actions de modification et les erreurs
        $methodsToLog = ['POST', 'PUT', 'PATCH', 'DELETE'];
        $statusCodesToLog = [400, 401, 403, 404, 422, 500];

        return in_array($request->method(), $methodsToLog) || 
               in_array($response->getStatusCode(), $statusCodesToLog);
    }

    /**
     * Créer l'entrée de log d'activité
     */
    private function logActivity(Request $request, Response $response): void
    {
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => $this->getAction($request, $response),
            'model_type' => $this->getModelType($request),
            'model_id' => $this->getModelId($request),
            'changes' => $this->getChanges($request),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'route' => $request->route()?->getName(),
            'method' => $request->method(),
            'status_code' => $response->getStatusCode(),
            'created_at' => now(),
        ]);
    }

    /**
     * Déterminer l'action effectuée
     */
    private function getAction(Request $request, Response $response): string
    {
        $method = $request->method();
        $statusCode = $response->getStatusCode();
        $route = $request->route()?->getName() ?? 'unknown';
        
        if ($statusCode >= 400) {
            return "error_{$statusCode} - {$route}";
        }

        return match($method) {
            'POST' => 'created',
            'PUT', 'PATCH' => 'updated', 
            'DELETE' => 'deleted',
            default => 'accessed'
        } . " - {$route}";
    }

    /**
     * Extraire le type de modèle depuis la route
     */
    private function getModelType(Request $request): ?string
    {
        $route = $request->route();
        if (!$route) return null;

        $routeName = $route->getName();
        
        $modelMappings = [
            'products' => 'Product',
            'customers' => 'Customer', 
            'suppliers' => 'Supplier',
            'sales' => 'Sale',
            'purchase-orders' => 'PurchaseOrder',
            'warehouses' => 'Warehouse',
            'inventories' => 'Inventory',
            'stock-movements' => 'StockMovement',
            'users' => 'User',
        ];

        foreach ($modelMappings as $route => $model) {
            if (str_contains($routeName, $route)) {
                return "App\\Models\\{$model}";
            }
        }

        return null;
    }

    /**
     * Extraire l'ID du modèle depuis les paramètres de route
     */
    private function getModelId(Request $request): ?int
    {
        $route = $request->route();
        if (!$route) return null;

        // Chercher l'ID dans les paramètres de route
        foreach ($route->parameters() as $param) {
            if (is_object($param) && method_exists($param, 'getKey')) {
                return $param->getKey();
            }
            if (is_numeric($param)) {
                return (int) $param;
            }
        }

        return null;
    }

    /**
     * Extraire les changements depuis la requête
     */
    private function getChanges(Request $request): ?array
    {
        // Ne capturer que les champs importants, pas les mots de passe
        $data = $request->except(['password', 'password_confirmation', '_token', '_method']);
        
        return empty($data) ? null : $data;
    }
}
