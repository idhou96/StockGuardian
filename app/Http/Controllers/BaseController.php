<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\ActivityLog;

abstract class BaseController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    protected $model;
    protected $modelName;
    protected $viewPath;

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Réponse JSON standardisée pour succès
     */
    protected function successResponse($data = null, string $message = 'Opération réussie', int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $status);
    }

    /**
     * Réponse JSON standardisée pour erreur
     */
    protected function errorResponse(string $message = 'Une erreur est survenue', $errors = null, int $status = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors
        ], $status);
    }

    /**
     * Redirection avec message de succès
     */
    protected function redirectWithSuccess(string $route, string $message = 'Opération réussie')
    {
        return redirect()->route($route)->with('success', $message);
    }

    /**
     * Redirection avec message d'erreur
     */
    protected function redirectWithError(string $route, string $message = 'Une erreur est survenue')
    {
        return redirect()->route($route)->with('error', $message);
    }

    /**
     * Retour à la page précédente avec message de succès
     */
    protected function backWithSuccess(string $message = 'Opération réussie')
    {
        return redirect()->back()->with('success', $message);
    }

    /**
     * Retour à la page précédente avec message d'erreur
     */
    protected function backWithError(string $message = 'Une erreur est survenue')
    {
        return redirect()->back()->with('error', $message);
    }

    /**
     * Logger une activité
     */
    protected function logActivity(string $action, $model = null, array $oldValues = [], array $newValues = [], string $description = null)
    {
        ActivityLog::logActivity($action, $model, $oldValues, $newValues, $description);
    }

    /**
     * Vérifier les permissions pour une action
     */
    protected function checkPermission(string $permission): bool
    {
        $user = auth()->user();
        
        if (!$user) {
            return false;
        }

        // Administrateur a tous les droits
        if ($user->hasRole('administrateur')) {
            return true;
        }

        return $user->hasPermission($permission);
    }

    /**
     * Vérifier les rôles autorisés
     */
    protected function checkRoles(array $roles): bool
    {
        $user = auth()->user();
        
        if (!$user) {
            return false;
        }

        return $user->hasAnyRole($roles);
    }

    /**
     * Réponse d'autorisation refusée
     */
    protected function unauthorizedResponse(string $message = 'Action non autorisée')
    {
        if (request()->expectsJson()) {
            return $this->errorResponse($message, null, 403);
        }

        return redirect()->back()->with('error', $message);
    }

    /**
     * Pagination personnalisée
     */
    protected function getPaginationData(Request $request, int $defaultPerPage = 15): array
    {
        return [
            'perPage' => $request->get('per_page', $defaultPerPage),
            'page' => $request->get('page', 1),
            'search' => $request->get('search', ''),
            'sortBy' => $request->get('sort_by', 'created_at'),
            'sortDirection' => $request->get('sort_direction', 'desc'),
        ];
    }

    /**
     * Filtres de dates
     */
    protected function getDateFilters(Request $request): array
    {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        if ($startDate) {
            $startDate = \Carbon\Carbon::parse($startDate)->startOfDay();
        }

        if ($endDate) {
            $endDate = \Carbon\Carbon::parse($endDate)->endOfDay();
        }

        return compact('startDate', 'endDate');
    }

    /**
     * Exporter des données en CSV
     */
    protected function exportToCsv($data, string $filename, array $headers = []): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $filename = $filename . '_' . now()->format('Y-m-d_H-i-s') . '.csv';

        return response()->streamDownload(function() use ($data, $headers) {
            $handle = fopen('php://output', 'w');
            
            // BOM pour Excel
            fwrite($handle, "\xEF\xBB\xBF");
            
            // En-têtes
            if (!empty($headers)) {
                fputcsv($handle, $headers);
            }

            // Données
            foreach ($data as $row) {
                fputcsv($handle, is_array($row) ? $row : $row->toArray());
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Valider les données avec des règles personnalisées
     */
    protected function validateData(Request $request, array $rules, array $messages = []): array
    {
        return $request->validate($rules, $messages);
    }

    /**
     * Gestion des erreurs de base de données
     */
    protected function handleDatabaseError(\Exception $e, string $action = 'opération')
    {
        \Log::error("Erreur lors de l'{$action}: " . $e->getMessage());

        $message = match(true) {
            str_contains($e->getMessage(), 'FOREIGN KEY') => 'Impossible de supprimer cet élément car il est utilisé ailleurs.',
            str_contains($e->getMessage(), 'Duplicate entry') => 'Cet élément existe déjà.',
            str_contains($e->getMessage(), 'Data too long') => 'Les données saisies sont trop longues.',
            default => "Erreur lors de l'{$action}. Veuillez réessayer.",
        };

        if (request()->expectsJson()) {
            return $this->errorResponse($message);
        }

        return $this->backWithError($message);
    }
}