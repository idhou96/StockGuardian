<?php

namespace App\Http\Controllers;

use App\Models\Regularization;
use App\Models\RegularizationDetail;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RegularizationController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->model = Regularization::class;
        $this->modelName = 'Régularisation';
        $this->viewPath = 'regularizations';
    }

    /**
     * Afficher la liste des régularisations
     */
    public function index(Request $request)
    {
        if (!$this->checkRoles(['administrateur', 'magasinier'])) {
            return $this->unauthorizedResponse();
        }

        $query = Regularization::with(['warehouse', 'createdBy'])->withCount('regularizationDetails');

        // Appliquer les filtres
        $this->applyRegularizationFilters($query, $request);

        // Tri et pagination
        $pagination = $this->getPaginationData($request);
        
        $regularizations = $query->orderBy($pagination['sortBy'], $pagination['sortDirection'])
                                ->paginate($pagination['perPage']);

        // Statistiques
        $stats = $this->getRegularizationStats();

        $warehouses = Warehouse::active()->get();

        return view('regularizations.index', compact('regularizations', 'stats', 'warehouses'));
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

        return view('regularizations.create', compact('warehouses'));
    }

    /**
     * Enregistrer une nouvelle régularisation
     */
    public function store(Request $request)
    {
        if (!$this->checkRoles(['administrateur', 'magasinier'])) {
            return $this->unauthorizedResponse();
        }

        $validated = $this->validateRegularization($request);

        try {
            DB::beginTransaction();

            // Créer la régularisation
            $regularization = Regularization::create([
                'reference' => $this->generateRegularizationReference(),
                'warehouse_id' => $validated['warehouse_id'],
                'regularization_date' => $validated['regularization_date'],
                'type' => $validated['type'],
                'reason' => $validated['reason'],
                'status' => 'brouillon',
                'created_by' => auth()->id(),
                'notes' => $validated['notes'],
            ]);

            // Ajouter les détails de régularisation
            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                
                // Obtenir le stock actuel
                $warehouseStock = WarehouseStock::where([
                    'warehouse_id' => $validated['warehouse_id'],
                    'product_id' => $product->id,
                ])->first();

                $currentStock = $warehouseStock ? $warehouseStock->quantity : 0;
                $difference = $item['new_quantity'] - $currentStock;

                if ($difference != 0) {
                    RegularizationDetail::create([
                        'regularization_id' => $regularization->id,
                        'product_id' => $product->id,
                        'current_quantity' => $currentStock,
                        'new_quantity' => $item['new_quantity'],
                        'difference' => $difference,
                        'unit_cost' => $product->purchase_price,
                        'total_cost' => abs($difference) * $product->purchase_price,
                        'reason' => $item['reason'] ?? $validated['reason'],
                    ]);
                }
            }

            // Valider automatiquement si demandé
            if ($validated['validate_immediately'] ?? false) {
                $regularization->validate();
            }

            DB::commit();

            $this->logActivity('create', $regularization, [], $regularization->toArray(), 
                "Création de la régularisation {$regularization->reference}");

            if ($request->expectsJson()) {
                return $this->successResponse($regularization->load('regularizationDetails.product'), 
                    'Régularisation créée avec succès');
            }

            return redirect()->route('regularizations.show', $regularization)
                           ->with('success', 'Régularisation créée avec succès');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleDatabaseError($e, 'création de la régularisation');
        }
    }

    /**
     * Afficher les détails d'une régularisation
     */
    public function show(Regularization $regularization)
    {
        if (!$this->checkRoles(['administrateur', 'magasinier'])) {
            return $this->unauthorizedResponse();
        }

        $regularization->load([
            'warehouse',
            'createdBy',
            'regularizationDetails.product',
            'stockMovements'
        ]);

        $this->logActivity('view', $regularization, [], [], 
            "Consultation de la régularisation {$regularization->reference}");

        return view('regularizations.show', compact('regularization'));
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(Regularization $regularization)
    {
        if (!$this->checkRoles(['administrateur', 'magasinier'])) {
            return $this->unauthorizedResponse();
        }

        if (!$regularization->canBeModified()) {
            return $this->backWithError('Cette régularisation ne peut plus être modifiée');
        }

        $warehouses = Warehouse::active()->get();
        
        $regularization->load('regularizationDetails.product');

        return view('regularizations.edit', compact('regularization', 'warehouses'));
    }

    /**
     * Mettre à jour une régularisation
     */
    public function update(Request $request, Regularization $regularization)
    {
        if (!$this->checkRoles(['administrateur', 'magasinier'])) {
            return $this->unauthorizedResponse();
        }

        if (!$regularization->canBeModified()) {
            return $this->backWithError('Cette régularisation ne peut plus être modifiée');
        }

        $validated = $this->validateRegularization($request);
        $oldValues = $regularization->toArray();

        try {
            DB::beginTransaction();

            // Mettre à jour la régularisation
            $regularization->update([
                'warehouse_id' => $validated['warehouse_id'],
                'regularization_date' => $validated['regularization_date'],
                'type' => $validated['type'],
                'reason' => $validated['reason'],
                'notes' => $validated['notes'],
            ]);

            // Supprimer les anciens détails
            $regularization->regularizationDetails()->delete();

            // Ajouter les nouveaux détails
            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                
                $warehouseStock = WarehouseStock::where([
                    'warehouse_id' => $validated['warehouse_id'],
                    'product_id' => $product->id,
                ])->first();

                $currentStock = $warehouseStock ? $warehouseStock->quantity : 0;
                $difference = $item['new_quantity'] - $currentStock;

                if ($difference != 0) {
                    RegularizationDetail::create([
                        'regularization_id' => $regularization->id,
                        'product_id' => $product->id,
                        'current_quantity' => $currentStock,
                        'new_quantity' => $item['new_quantity'],
                        'difference' => $difference,
                        'unit_cost' => $product->purchase_price,
                        'total_cost' => abs($difference) * $product->purchase_price,
                        'reason' => $item['reason'] ?? $validated['reason'],
                    ]);
                }
            }

            DB::commit();

            $this->logActivity('update', $regularization, $oldValues, $regularization->toArray(), 
                "Modification de la régularisation {$regularization->reference}");

            if ($request->expectsJson()) {
                return $this->successResponse($regularization, 'Régularisation modifiée avec succès');
            }

            return $this->redirectWithSuccess('regularizations.index', 'Régularisation modifiée avec succès');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleDatabaseError($e, 'modification de la régularisation');
        }
    }

    /**
     * Valider une régularisation (appliquer les ajustements)
     */
    public function validate(Regularization $regularization)
    {
        if (!$this->checkRoles(['administrateur', 'magasinier'])) {
            return $this->unauthorizedResponse();
        }

        if (!$regularization->canBeValidated()) {
            return $this->errorResponse('Cette régularisation ne peut pas être validée');
        }

        try {
            DB::beginTransaction();

            // Appliquer chaque détail de régularisation
            foreach ($regularization->regularizationDetails as $detail) {
                if ($detail->difference != 0) {
                    // Créer le mouvement de stock correspondant
                    $movementType = $detail->difference > 0 ? 'entree' : 'sortie';
                    $movementReason = $movementType === 'entree' ? 'ajustement_positif' : 'ajustement_negatif';

                    $movement = StockMovement::create([
                        'reference' => StockMovement::generateReference('regularisation'),
                        'product_id' => $detail->product_id,
                        'warehouse_id' => $regularization->warehouse_id,
                        'type' => $movementType,
                        'reason' => $movementReason,
                        'quantity' => abs($detail->difference),
                        'stock_before' => $detail->current_quantity,
                        'stock_after' => $detail->new_quantity,
                        'unit_cost' => $detail->unit_cost,
                        'total_cost' => $detail->total_cost,
                        'movement_date' => $regularization->regularization_date,
                        'movement_time' => now()->format('H:i:s'),
                        'created_by' => auth()->id(),
                        'notes' => "Régularisation {$regularization->reference} - {$detail->reason}",
                        'regularization_id' => $regularization->id,
                    ]);

                    // Mettre à jour le stock
                    $warehouseStock = WarehouseStock::firstOrCreate([
                        'warehouse_id' => $regularization->warehouse_id,
                        'product_id' => $detail->product_id,
                    ]);

                    $warehouseStock->quantity = $detail->new_quantity;
                    $warehouseStock->save();
                }
            }

            // Changer le statut de la régularisation
            $regularization->update([
                'status' => 'validee',
                'validated_at' => now(),
                'validated_by' => auth()->id(),
            ]);

            DB::commit();

            $this->logActivity('validate', $regularization, [], [], 
                "Validation de la régularisation {$regularization->reference}");

            if (request()->expectsJson()) {
                return $this->successResponse($regularization, 'Régularisation validée avec succès');
            }

            return $this->backWithSuccess('Régularisation validée avec succès');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleDatabaseError($e, 'validation de la régularisation');
        }
    }

    /**
     * Annuler une régularisation
     */
    public function cancel(Regularization $regularization)
    {
        if (!$this->checkRoles(['administrateur'])) {
            return $this->unauthorizedResponse();
        }

        if ($regularization->status === 'validee') {
            return $this->errorResponse('Une régularisation validée ne peut pas être annulée');
        }

        try {
            DB::beginTransaction();

            $regularization->update(['status' => 'annulee']);

            DB::commit();

            $this->logActivity('cancel', $regularization, [], [], 
                "Annulation de la régularisation {$regularization->reference}");

            if (request()->expectsJson()) {
                return $this->successResponse($regularization, 'Régularisation annulée');
            }

            return $this->backWithSuccess('Régularisation annulée');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleDatabaseError($e, 'annulation de la régularisation');
        }
    }

    /**
     * Supprimer une régularisation
     */
    public function destroy(Regularization $regularization)
    {
        if (!$this->checkRoles(['administrateur'])) {
            return $this->unauthorizedResponse();
        }

        if ($regularization->status === 'validee') {
            return $this->errorResponse('Une régularisation validée ne peut pas être supprimée');
        }

        try {
            DB::beginTransaction();

            $reference = $regularization->reference;
            
            // Supprimer les détails
            $regularization->regularizationDetails()->delete();
            
            // Supprimer la régularisation
            $regularization->delete();

            DB::commit();

            $this->logActivity('delete', $regularization, $regularization->toArray(), [], 
                "Suppression de la régularisation {$reference}");

            if (request()->expectsJson()) {
                return $this->successResponse(null, 'Régularisation supprimée avec succès');
            }

            return $this->redirectWithSuccess('regularizations.index', 'Régularisation supprimée avec succès');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleDatabaseError($e, 'suppression de la régularisation');
        }
    }

    /**
     * Méthodes utilitaires privées
     */
    private function validateRegularization(Request $request)
    {
        return $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'regularization_date' => 'required|date',
            'type' => 'required|in:inventaire,ajustement,correction,perte,peremption',
            'reason' => 'required|string|max:255',
            'notes' => 'nullable|string|max:1000',
            'validate_immediately' => 'boolean',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.new_quantity' => 'required|integer|min:0',
            'items.*.reason' => 'nullable|string|max:255',
        ]);
    }

    private function generateRegularizationReference()
    {
        $prefix = 'REG';
        $year = date('Y');
        $month = date('m');
        
        $lastRegularization = Regularization::whereYear('created_at', $year)
                                          ->whereMonth('created_at', $month)
                                          ->orderBy('id', 'desc')
                                          ->first();
        
        $number = $lastRegularization ? (int)substr($lastRegularization->reference, -4) + 1 : 1;
        
        return $prefix . $year . $month . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    private function applyRegularizationFilters($query, Request $request)
    {
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                  ->orWhere('reason', 'like', "%{$search}%");
            });
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($type = $request->get('type')) {
            $query->where('type', $type);
        }

        if ($warehouseId = $request->get('warehouse_id')) {
            $query->where('warehouse_id', $warehouseId);
        }

        if ($dateFrom = $request->get('date_from')) {
            $query->whereDate('regularization_date', '>=', $dateFrom);
        }

        if ($dateTo = $request->get('date_to')) {
            $query->whereDate('regularization_date', '<=', $dateTo);
        }
    }

    private function getRegularizationStats()
    {
        $thisMonth = now()->format('Y-m');
        
        return [
            'total_regularizations' => Regularization::count(),
            'pending_regularizations' => Regularization::where('status', 'brouillon')->count(),
            'validated_regularizations' => Regularization::where('status', 'validee')->count(),
            'regularizations_this_month' => Regularization::whereRaw('DATE_FORMAT(regularization_date, "%Y-%m") = ?', [$thisMonth])->count(),
            'positive_adjustments_month' => RegularizationDetail::whereHas('regularization', function($q) use ($thisMonth) {
                $q->whereRaw('DATE_FORMAT(regularization_date, "%Y-%m") = ?', [$thisMonth])
                  ->where('status', 'validee');
            })->where('difference', '>', 0)->sum('difference'),
            'negative_adjustments_month' => RegularizationDetail::whereHas('regularization', function($q) use ($thisMonth) {
                $q->whereRaw('DATE_FORMAT(regularization_date, "%Y-%m") = ?', [$thisMonth])
                  ->where('status', 'validee');
            })->where('difference', '<', 0)->sum('difference'),
        ];
    }
}