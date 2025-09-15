<?php

namespace App\Http\Controllers;

use App\Models\StockMovement;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockMovementController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->model = StockMovement::class;
        $this->modelName = 'Mouvement de stock';
        $this->viewPath = 'stock-movements';
    }

    /**
     * Display a listing of the resource.
     * Afficher la liste des mouvements de stock
     */
    public function index(Request $request)
    {
        if (!$this->checkRoles(['administrateur', 'magasinier', 'responsable_achats'])) {
            return $this->unauthorizedResponse();
        }

        $query = StockMovement::with(['product', 'warehouse', 'createdBy']);

        // Appliquer les filtres
        $this->applyStockMovementFilters($query, $request);

        // Tri et pagination
        $pagination = $this->getPaginationData($request);
        
        $movements = $query->orderBy($pagination['sortBy'], $pagination['sortDirection'])
                          ->paginate($pagination['perPage']);

        // Statistiques
        $stats = $this->getStockMovementStats();

        $products = Product::active()->get();
        $warehouses = Warehouse::active()->get();

        return view('stock-movements.index', compact('movements', 'stats', 'products', 'warehouses'));
    }

    /**
     * Show the form for creating a new resource.
     * Afficher le formulaire de création d'entrée de stock
     */
    public function create()
    {
        return $this->createEntry();
    }

    /**
     * Afficher le formulaire de création d'entrée de stock
     */
    public function createEntry()
    {
        if (!$this->checkRoles(['administrateur', 'magasinier'])) {
            return $this->unauthorizedResponse();
        }

        $products = Product::active()->get();
        $warehouses = Warehouse::active()->get();

        return view('stock-movements.create-entry', compact('products', 'warehouses'));
    }

    /**
     * Afficher le formulaire de création de sortie de stock
     */
    public function createExit()
    {
        if (!$this->checkRoles(['administrateur', 'magasinier'])) {
            return $this->unauthorizedResponse();
        }

        $products = Product::active()->get();
        $warehouses = Warehouse::active()->get();

        return view('stock-movements.create-exit', compact('products', 'warehouses'));
    }

    /**
     * Store a newly created resource in storage.
     * Enregistrer un nouveau mouvement (entrée par défaut)
     */
    public function store(Request $request)
    {
        return $this->storeEntry($request);
    }

    /**
     * Enregistrer une entrée de stock
     */
    public function storeEntry(Request $request)
    {
        if (!$this->checkRoles(['administrateur', 'magasinier'])) {
            return $this->unauthorizedResponse();
        }

        $validated = $this->validateStockEntry($request);

        try {
            DB::beginTransaction();

            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                $warehouse = Warehouse::findOrFail($validated['warehouse_id']);

                // Obtenir le stock actuel
                $warehouseStock = WarehouseStock::firstOrCreate([
                    'warehouse_id' => $warehouse->id,
                    'product_id' => $product->id,
                ]);

                $stockBefore = $warehouseStock->quantity;
                $stockAfter = $stockBefore + $item['quantity'];

                // Créer le mouvement d'entrée
                $movement = StockMovement::create([
                    'reference' => StockMovement::generateReference('entree'),
                    'product_id' => $product->id,
                    'warehouse_id' => $warehouse->id,
                    'type' => 'entree',
                    'reason' => $validated['reason'],
                    'quantity' => $item['quantity'],
                    'stock_before' => $stockBefore,
                    'stock_after' => $stockAfter,
                    'unit_cost' => $item['unit_cost'],
                    'total_cost' => $item['quantity'] * $item['unit_cost'],
                    'movement_date' => $validated['movement_date'],
                    'movement_time' => $validated['movement_time'] ?? now()->format('H:i:s'),
                    'created_by' => auth()->id(),
                    'notes' => $validated['notes'],
                ]);

                // Mettre à jour le stock
                $warehouseStock->updateQuantity($item['quantity'], 'add');

                // Mettre à jour le prix d'achat si c'est une réception
                if ($validated['reason'] === 'reception_commande' && $item['unit_cost'] > 0) {
                    $product->update(['purchase_price' => $item['unit_cost']]);
                }
            }

            DB::commit();

            $this->logActivity('create', $movement, [], [], "Entrée de stock - {$validated['reason']}");

            if ($request->expectsJson()) {
                return $this->successResponse(null, 'Entrée de stock enregistrée avec succès');
            }

            return $this->redirectWithSuccess('stock-movements.index', 'Entrée de stock enregistrée avec succès');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleDatabaseError($e, 'enregistrement de l\'entrée de stock');
        }
    }

    /**
     * Enregistrer une sortie de stock
     */
    public function storeExit(Request $request)
    {
        if (!$this->checkRoles(['administrateur', 'magasinier'])) {
            return $this->unauthorizedResponse();
        }

        $validated = $this->validateStockExit($request);

        try {
            DB::beginTransaction();

            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                $warehouse = Warehouse::findOrFail($validated['warehouse_id']);

                // Vérifier le stock disponible
                $warehouseStock = WarehouseStock::where([
                    'warehouse_id' => $warehouse->id,
                    'product_id' => $product->id,
                ])->first();

                if (!$warehouseStock || $warehouseStock->quantity < $item['quantity']) {
                    throw new \Exception("Stock insuffisant pour le produit {$product->name}");
                }

                $stockBefore = $warehouseStock->quantity;
                $stockAfter = $stockBefore - $item['quantity'];

                // Créer le mouvement de sortie
                $movement = StockMovement::create([
                    'reference' => StockMovement::generateReference('sortie'),
                    'product_id' => $product->id,
                    'warehouse_id' => $warehouse->id,
                    'type' => 'sortie',
                    'reason' => $validated['reason'],
                    'quantity' => $item['quantity'],
                    'stock_before' => $stockBefore,
                    'stock_after' => $stockAfter,
                    'unit_cost' => $product->purchase_price,
                    'total_cost' => $item['quantity'] * $product->purchase_price,
                    'movement_date' => $validated['movement_date'],
                    'movement_time' => $validated['movement_time'] ?? now()->format('H:i:s'),
                    'created_by' => auth()->id(),
                    'notes' => $validated['notes'],
                ]);

                // Mettre à jour le stock
                $warehouseStock->updateQuantity($item['quantity'], 'subtract');
            }

            DB::commit();

            $this->logActivity('create', $movement, [], [], "Sortie de stock - {$validated['reason']}");

            if ($request->expectsJson()) {
                return $this->successResponse(null, 'Sortie de stock enregistrée avec succès');
            }

            return $this->redirectWithSuccess('stock-movements.index', 'Sortie de stock enregistrée avec succès');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleDatabaseError($e, 'enregistrement de la sortie de stock');
        }
    }

    /**
     * Display the specified resource.
     * Afficher les détails d'un mouvement
     */
    public function show(StockMovement $stockMovement)
    {
        if (!$this->checkRoles(['administrateur', 'magasinier', 'responsable_achats'])) {
            return $this->unauthorizedResponse();
        }

        $stockMovement->load(['product', 'warehouse', 'createdBy']);

        $this->logActivity('view', $stockMovement, [], [], "Consultation du mouvement {$stockMovement->reference}");

        return view('stock-movements.show', compact('stockMovement'));
    }

    /**
     * Show the form for editing the specified resource.
     * Les mouvements de stock ne sont généralement pas modifiables
     * Cette méthode redirige vers la vue des détails
     */
    public function edit(StockMovement $stockMovement)
    {
        return redirect()->route('stock-movements.show', $stockMovement)
                        ->with('info', 'Les mouvements de stock ne peuvent pas être modifiés pour des raisons d\'intégrité');
    }

    /**
     * Update the specified resource in storage.
     * Les mouvements de stock ne sont pas modifiables
     */
    public function update(Request $request, StockMovement $stockMovement)
    {
        return $this->errorResponse('Les mouvements de stock ne peuvent pas être modifiés');
    }

    /**
     * Remove the specified resource from storage.
     * Annuler un mouvement de stock
     */
    public function destroy(StockMovement $stockMovement)
    {
        return $this->cancel($stockMovement);
    }

    /**
     * Annuler un mouvement de stock
     */
    public function cancel(StockMovement $stockMovement)
    {
        if (!$this->checkRoles(['administrateur'])) {
            return $this->unauthorizedResponse();
        }

        if (!$stockMovement->canBeCancelled()) {
            return $this->errorResponse('Ce mouvement ne peut pas être annulé');
        }

        try {
            DB::beginTransaction();

            // Créer le mouvement inverse
            $reverseMovement = $stockMovement->createReverseMovement();

            // Mettre à jour le stock
            $warehouseStock = WarehouseStock::where([
                'warehouse_id' => $stockMovement->warehouse_id,
                'product_id' => $stockMovement->product_id,
            ])->first();

            if ($warehouseStock) {
                if ($stockMovement->type === 'entree') {
                    $warehouseStock->updateQuantity($stockMovement->quantity, 'subtract');
                } else {
                    $warehouseStock->updateQuantity($stockMovement->quantity, 'add');
                }
            }

            DB::commit();

            $this->logActivity('cancel', $stockMovement, [], [], 
                "Annulation du mouvement {$stockMovement->reference}");

            if (request()->expectsJson()) {
                return $this->successResponse($reverseMovement, 'Mouvement annulé avec succès');
            }

            return $this->backWithSuccess('Mouvement annulé avec succès');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleDatabaseError($e, 'annulation du mouvement');
        }
    }

    /**
     * Rapport des mouvements de stock
     */
    public function report(Request $request)
    {
        if (!$this->checkRoles(['administrateur', 'magasinier', 'responsable_achats'])) {
            return $this->unauthorizedResponse();
        }

        $query = StockMovement::with(['product.family', 'warehouse']);

        // Filtres pour le rapport
        $this->applyReportFilters($query, $request);

        $movements = $query->orderBy('movement_date', 'desc')
                          ->orderBy('movement_time', 'desc')
                          ->paginate(100);

        $warehouses = Warehouse::active()->get();
        $products = Product::active()->get();

        return view('stock-movements.report', compact('movements', 'warehouses', 'products'));
    }

    /**
     * Méthodes utilitaires privées
     */
    private function validateStockEntry(Request $request)
    {
        return $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'reason' => 'required|in:reception_commande,ajustement_inventaire,retour_client,transfert_entrant,autre',
            'movement_date' => 'required|date',
            'movement_time' => 'nullable|date_format:H:i:s',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_cost' => 'required|numeric|min:0',
        ]);
    }

    private function validateStockExit(Request $request)
    {
        return $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'reason' => 'required|in:vente,ajustement_inventaire,perte,peremption,transfert_sortant,autre',
            'movement_date' => 'required|date',
            'movement_time' => 'nullable|date_format:H:i:s',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);
    }

    private function applyStockMovementFilters($query, Request $request)
    {
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                  ->orWhereHas('product', function ($pq) use ($search) {
                      $pq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($type = $request->get('type')) {
            $query->where('type', $type);
        }

        if ($reason = $request->get('reason')) {
            $query->where('reason', $reason);
        }

        if ($productId = $request->get('product_id')) {
            $query->where('product_id', $productId);
        }

        if ($warehouseId = $request->get('warehouse_id')) {
            $query->where('warehouse_id', $warehouseId);
        }

        if ($dateFrom = $request->get('date_from')) {
            $query->whereDate('movement_date', '>=', $dateFrom);
        }

        if ($dateTo = $request->get('date_to')) {
            $query->whereDate('movement_date', '<=', $dateTo);
        }
    }

    private function applyReportFilters($query, Request $request)
    {
        $this->applyStockMovementFilters($query, $request);

        // Filtres supplémentaires pour les rapports
        if ($familyId = $request->get('family_id')) {
            $query->whereHas('product', function ($q) use ($familyId) {
                $q->where('family_id', $familyId);
            });
        }
    }

    private function getStockMovementStats()
    {
        $today = now()->toDateString();
        $thisMonth = now()->format('Y-m');
        
        return [
            'total_movements' => StockMovement::count(),
            'entries_today' => StockMovement::where('type', 'entree')->whereDate('movement_date', $today)->count(),
            'exits_today' => StockMovement::where('type', 'sortie')->whereDate('movement_date', $today)->count(),
            'entries_this_month' => StockMovement::where('type', 'entree')
                                                ->whereRaw('DATE_FORMAT(movement_date, "%Y-%m") = ?', [$thisMonth])
                                                ->count(),
            'exits_this_month' => StockMovement::where('type', 'sortie')
                                               ->whereRaw('DATE_FORMAT(movement_date, "%Y-%m") = ?', [$thisMonth])
                                               ->count(),
            'total_value_entries_month' => StockMovement::where('type', 'entree')
                                                       ->whereRaw('DATE_FORMAT(movement_date, "%Y-%m") = ?', [$thisMonth])
                                                       ->sum('total_cost'),
            'total_value_exits_month' => StockMovement::where('type', 'sortie')
                                                     ->whereRaw('DATE_FORMAT(movement_date, "%Y-%m") = ?', [$thisMonth])
                                                     ->sum('total_cost'),
        ];
    }
}