<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\InventoryDetail;
use App\Models\Warehouse;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InventoryController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->model = Inventory::class;
        $this->modelName = 'Inventaire';
        $this->viewPath = 'inventories';
    }

    /**
     * Afficher la liste des inventaires
     */
    public function index(Request $request)
    {
        if (!$this->checkRoles(['administrateur', 'magasinier'])) {
            return $this->unauthorizedResponse();
        }

        $query = Inventory::with(['warehouse', 'createdBy', 'validatedBy']);

        // Appliquer les filtres
        $this->applyInventoryFilters($query, $request);

        // Tri et pagination
        $pagination = $this->getPaginationData($request);
        
        $inventories = $query->orderBy($pagination['sortBy'], $pagination['sortDirection'])
                           ->paginate($pagination['perPage']);

        // Statistiques
        $stats = $this->getInventoryStats();

        $warehouses = Warehouse::active()->get();

        return view('inventories.index', compact('inventories', 'stats', 'warehouses'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        if (!$this->checkRoles(['administrateur', 'magasinier'])) {
            return $this->unauthorizedResponse();
        }

        $warehouses = Warehouse::active()->get();

        return view('inventories.create', compact('warehouses'));
    }

    /**
     * Créer un nouvel inventaire
     */
    public function store(Request $request)
    {
        if (!$this->checkRoles(['administrateur', 'magasinier'])) {
            return $this->unauthorizedResponse();
        }

        $validated = $this->validateInventory($request);

        try {
            DB::beginTransaction();

            // Générer la référence
            $reference = $this->generateInventoryReference($validated['warehouse_id']);

            $inventory = Inventory::create([
                'reference' => $reference,
                'label' => $validated['label'],
                'warehouse_id' => $validated['warehouse_id'],
                'inventory_date' => $validated['inventory_date'] ?? now()->toDateString(),
                'status' => 'en_cours',
                'created_by' => auth()->id(),
                'notes' => $validated['notes'],
            ]);

            // Initialiser l'inventaire avec tous les produits en stock
            $inventory->initializeInventory();

            DB::commit();

            $this->logActivity('create', $inventory, [], $inventory->toArray(), "Création de l'inventaire {$inventory->reference}");

            if ($request->expectsJson()) {
                return $this->successResponse($inventory, 'Inventaire créé avec succès');
            }

            return redirect()->route('inventories.show', $inventory)
                           ->with('success', 'Inventaire créé avec succès');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleDatabaseError($e, 'création de l\'inventaire');
        }
    }

    /**
     * Afficher les détails d'un inventaire
     */
    public function show(Inventory $inventory)
    {
        if (!$this->checkRoles(['administrateur', 'magasinier'])) {
            return $this->unauthorizedResponse();
        }

        $inventory->load([
            'warehouse',
            'createdBy',
            'validatedBy',
            'inventoryDetails.product.family'
        ]);

        // Statistiques de l'inventaire
        $stats = $this->getInventoryDetailStats($inventory);

        // Filtres pour les détails
        $filter = request('filter', 'all');
        $search = request('search', '');

        $detailsQuery = $inventory->inventoryDetails()
                                 ->with(['product.family']);

        // Appliquer les filtres
        if ($search) {
            $detailsQuery->whereHas('product', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        switch ($filter) {
            case 'counted':
                $detailsQuery->where('physical_quantity', '>', 0);
                break;
            case 'not_counted':
                $detailsQuery->where('physical_quantity', 0);
                break;
            case 'variances':
                $detailsQuery->where('variance_quantity', '!=', 0);
                break;
            case 'positive_variances':
                $detailsQuery->where('variance_quantity', '>', 0);
                break;
            case 'negative_variances':
                $detailsQuery->where('variance_quantity', '<', 0);
                break;
        }

        $details = $detailsQuery->paginate(50);

        $this->logActivity('view', $inventory, [], [], "Consultation de l'inventaire {$inventory->reference}");

        return view('inventories.show', compact('inventory', 'stats', 'details', 'filter', 'search'));
    }

    /**
     * Mettre à jour une quantité comptée
     */
    public function updateCount(Request $request, Inventory $inventory, InventoryDetail $detail)
    {
        if (!$this->checkRoles(['administrateur', 'magasinier'])) {
            return $this->unauthorizedResponse();
        }

        if ($inventory->status !== 'en_cours') {
            return $this->errorResponse('Cet inventaire ne peut plus être modifié');
        }

        $request->validate([
            'physical_quantity' => 'required|integer|min:0',
            'notes' => 'nullable|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            $oldQuantity = $detail->physical_quantity;
            $detail->updatePhysicalQuantity($request->physical_quantity);
            
            if ($request->notes) {
                $detail->notes = $request->notes;
                $detail->save();
            }

            // Recalculer les totaux de l'inventaire
            $inventory->calculateTotals();

            DB::commit();

            $this->logActivity('update', $detail, 
                ['physical_quantity' => $oldQuantity], 
                ['physical_quantity' => $detail->physical_quantity], 
                "Mise à jour quantité comptée pour {$detail->product->name}"
            );

            if ($request->expectsJson()) {
                return $this->successResponse([
                    'detail' => $detail->fresh(),
                    'inventory' => $inventory->fresh(),
                ], 'Quantité mise à jour avec succès');
            }

            return $this->backWithSuccess('Quantité mise à jour avec succès');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleDatabaseError($e, 'mise à jour de la quantité');
        }
    }

    /**
     * Terminer un inventaire
     */
    public function complete(Inventory $inventory)
    {
        if (!$this->checkRoles(['administrateur', 'magasinier'])) {
            return $this->unauthorizedResponse();
        }

        if ($inventory->status !== 'en_cours') {
            return $this->errorResponse('Cet inventaire ne peut pas être terminé');
        }

        try {
            DB::beginTransaction();

            $inventory->complete();

            DB::commit();

            $this->logActivity('update', $inventory, 
                ['status' => 'en_cours'], 
                ['status' => 'termine'], 
                "Fin de l'inventaire {$inventory->reference}"
            );

            if (request()->expectsJson()) {
                return $this->successResponse($inventory, 'Inventaire terminé avec succès');
            }

            return $this->backWithSuccess('Inventaire terminé avec succès');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleDatabaseError($e, 'finalisation de l\'inventaire');
        }
    }

    /**
     * Valider un inventaire et appliquer les ajustements
     */
    public function validate(Inventory $inventory)
    {
        if (!$this->checkRoles(['administrateur'])) {
            return $this->unauthorizedResponse('Seul un administrateur peut valider un inventaire');
        }

        if (!$inventory->canBeValidated()) {
            return $this->errorResponse('Cet inventaire ne peut pas être validé');
        }

        try {
            DB::beginTransaction();

            $inventory->validate(auth()->user());

            DB::commit();

            $this->logActivity('validate', $inventory, [], [], "Validation de l'inventaire {$inventory->reference}");

            if (request()->expectsJson()) {
                return $this->successResponse($inventory, 'Inventaire validé et ajustements appliqués');
            }

            return $this->backWithSuccess('Inventaire validé et ajustements appliqués');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleDatabaseError($e, 'validation de l\'inventaire');
        }
    }

    /**
     * Imprimer les fiches d'inventaire
     */
    public function printSheets(Inventory $inventory)
    {
        if (!$this->checkRoles(['administrateur', 'magasinier'])) {
            return $this->unauthorizedResponse();
        }

        $inventory->load(['warehouse', 'inventoryDetails.product.family']);

        $this->logActivity('view', $inventory, [], [], "Impression fiches inventaire {$inventory->reference}");

        return view('inventories.print-sheets', compact('inventory'));
    }

    /**
     * Imprimer le rapport des écarts
     */
    public function printVariances(Inventory $inventory)
    {
        if (!$this->checkRoles(['administrateur', 'magasinier'])) {
            return $this->unauthorizedResponse();
        }

        $inventory->load(['warehouse', 'createdBy', 'validatedBy']);
        
        $variances = $inventory->getVarianceProducts();

        $this->logActivity('view', $inventory, [], [], "Impression rapport écarts {$inventory->reference}");

        return view('inventories.print-variances', compact('inventory', 'variances'));
    }

    /**
     * Imprimer le récapitulatif d'inventaire
     */
    public function printSummary(Inventory $inventory)
    {
        if (!$this->checkRoles(['administrateur', 'magasinier'])) {
            return $this->unauthorizedResponse();
        }

        $inventory->load(['warehouse', 'createdBy', 'validatedBy']);
        
        $stats = $this->getInventoryDetailStats($inventory);

        $this->logActivity('view', $inventory, [], [], "Impression récapitulatif inventaire {$inventory->reference}");

        return view('inventories.print-summary', compact('inventory', 'stats'));
    }

    /**
     * Supprimer un inventaire
     */
    public function destroy(Inventory $inventory)
    {
        if (!$this->checkRoles(['administrateur'])) {
            return $this->unauthorizedResponse();
        }

        if ($inventory->status === 'valide') {
            return $this->errorResponse('Impossible de supprimer un inventaire validé');
        }

        try {
            DB::beginTransaction();

            $inventoryReference = $inventory->reference;
            
            // Supprimer les détails
            $inventory->inventoryDetails()->delete();
            
            // Supprimer l'inventaire
            $inventory->delete();

            DB::commit();

            $this->logActivity('delete', $inventory, $inventory->toArray(), [], "Suppression de l'inventaire {$inventoryReference}");

            if (request()->expectsJson()) {
                return $this->successResponse(null, 'Inventaire supprimé avec succès');
            }

            return $this->redirectWithSuccess('inventories.index', 'Inventaire supprimé avec succès');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleDatabaseError($e, 'suppression de l\'inventaire');
        }
    }

    /**
     * Compter tous les produits d'un coup (import)
     */
    public function bulkUpdate(Request $request, Inventory $inventory)
    {
        if (!$this->checkRoles(['administrateur', 'magasinier'])) {
            return $this->unauthorizedResponse();
        }

        if ($inventory->status !== 'en_cours') {
            return $this->errorResponse('Cet inventaire ne peut plus être modifié');
        }

        $request->validate([
            'updates' => 'required|array',
            'updates.*.detail_id' => 'required|exists:inventory_details,id',
            'updates.*.physical_quantity' => 'required|integer|min:0',
            'updates.*.notes' => 'nullable|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            $updatedCount = 0;
            
            foreach ($request->updates as $update) {
                $detail = InventoryDetail::where('id', $update['detail_id'])
                                       ->where('inventory_id', $inventory->id)
                                       ->first();
                
                if ($detail) {
                    $detail->updatePhysicalQuantity($update['physical_quantity']);
                    if (!empty($update['notes'])) {
                        $detail->notes = $update['notes'];
                        $detail->save();
                    }
                    $updatedCount++;
                }
            }

            // Recalculer les totaux
            $inventory->calculateTotals();

            DB::commit();

            $this->logActivity('update', $inventory, [], [], "Mise à jour en lot de {$updatedCount} produits dans l'inventaire {$inventory->reference}");

            return $this->successResponse([
                'updated_count' => $updatedCount,
                'inventory' => $inventory->fresh(),
            ], "{$updatedCount} produit(s) mis à jour avec succès");

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleDatabaseError($e, 'mise à jour en lot');
        }
    }

    // Méthodes privées utilitaires

    private function applyInventoryFilters($query, Request $request)
    {
        // Recherche par libellé ou référence
        if ($search = $request->get('search')) {
            $query->where(function($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                  ->orWhere('label', 'like', "%{$search}%");
            });
        }

        // Filtres par entrepôt
        if ($warehouseId = $request->get('warehouse_id')) {
            $query->where('warehouse_id', $warehouseId);
        }

        // Filtres par statut
        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        // Filtres par dates
        $dateFilters = $this->getDateFilters($request);
        if ($dateFilters['startDate']) {
            $query->where('inventory_date', '>=', $dateFilters['startDate']);
        }
        if ($dateFilters['endDate']) {
            $query->where('inventory_date', '<=', $dateFilters['endDate']);
        }

        // Filtres spéciaux
        switch ($request->get('filter')) {
            case 'in_progress':
                $query->inProgress();
                break;
            case 'completed':
                $query->completed();
                break;
            case 'with_variances':
                $query->withVariances();
                break;
            case 'this_month':
                $query->whereMonth('inventory_date', Carbon::now()->month)
                      ->whereYear('inventory_date', Carbon::now()->year);
                break;
        }
    }

    private function validateInventory(Request $request)
    {
        return $request->validate([
            'label' => 'required|string|max:255',
            'warehouse_id' => 'required|exists:warehouses,id',
            'inventory_date' => 'nullable|date',
            'notes' => 'nullable|string|max:500',
        ], [
            'label.required' => 'Le libellé est obligatoire',
            'warehouse_id.required' => 'L\'entrepôt est obligatoire',
        ]);
    }

    private function generateInventoryReference($warehouseId): string
    {
        $warehouse = Warehouse::find($warehouseId);
        $warehouseCode = $warehouse ? substr($warehouse->code, 0, 3) : 'INV';
        $date = now()->format('Ymd');
        $count = Inventory::whereDate('created_at', now())
                         ->where('warehouse_id', $warehouseId)
                         ->count() + 1;
        
        return sprintf('%s-INV-%s-%03d', strtoupper($warehouseCode), $date, $count);
    }

    private function getInventoryStats(): array
    {
        return [
            'total' => Inventory::count(),
            'in_progress' => Inventory::inProgress()->count(),
            'completed' => Inventory::where('status', 'termine')->count(),
            'validated' => Inventory::where('status', 'valide')->count(),
            'with_variances' => Inventory::withVariances()->count(),
            'this_month' => Inventory::whereMonth('inventory_date', Carbon::now()->month)
                                   ->whereYear('inventory_date', Carbon::now()->year)
                                   ->count(),
        ];
    }

    private function getInventoryDetailStats(Inventory $inventory): array
    {
        $details = $inventory->inventoryDetails;
        
        return [
            'total_products' => $details->count(),
            'counted_products' => $details->where('physical_quantity', '>', 0)->count(),
            'not_counted_products' => $details->where('physical_quantity', 0)->count(),
            'products_with_variance' => $details->where('variance_quantity', '!=', 0)->count(),
            'positive_variances' => $details->where('variance_quantity', '>', 0)->count(),
            'negative_variances' => $details->where('variance_quantity', '<', 0)->count(),
            'completion_percentage' => $inventory->completion_percentage,
            'theoretical_value' => $inventory->theoretical_value,
            'physical_value' => $inventory->physical_value,
            'variance_value' => $inventory->variance_value,
            'variance_percentage' => $inventory->variance_percentage,
        ];
    }
}