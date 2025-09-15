<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use App\Models\WarehouseStock;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Family;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WarehouseController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->model = Warehouse::class;
        $this->modelName = 'Entrepôt';
        $this->viewPath = 'warehouses';
    }

    /**
     * Afficher la liste des entrepôts
     */
    public function index(Request $request)
    {
        if (!$this->checkRoles(['administrateur', 'magasinier', 'responsable_achats'])) {
            return $this->unauthorizedResponse();
        }

        $query = Warehouse::withCount(['warehouseStocks']);

        // Appliquer les filtres
        $this->applyWarehouseFilters($query, $request);

        // Tri et pagination
        $pagination = $this->getPaginationData($request);
        
        $warehouses = $query->orderBy($pagination['sortBy'], $pagination['sortDirection'])
                           ->paginate($pagination['perPage']);

        // Statistiques
        $stats = $this->getWarehouseStats();

        return view('warehouses.index', compact('warehouses', 'stats'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        if (!$this->checkRoles(['administrateur'])) {
            return $this->unauthorizedResponse();
        }

        return view('warehouses.create');
    }

    /**
     * Enregistrer un nouvel entrepôt
     */
    public function store(Request $request)
    {
        if (!$this->checkRoles(['administrateur'])) {
            return $this->unauthorizedResponse();
        }

        $validated = $this->validateWarehouse($request);

        try {
            DB::beginTransaction();

            $warehouse = Warehouse::create($validated);

            DB::commit();

            $this->logActivity('create', $warehouse, [], $warehouse->toArray(), "Création de l'entrepôt {$warehouse->name}");

            if ($request->expectsJson()) {
                return $this->successResponse($warehouse, 'Entrepôt créé avec succès');
            }

            return $this->redirectWithSuccess('warehouses.index', 'Entrepôt créé avec succès');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleDatabaseError($e, 'création de l\'entrepôt');
        }
    }

    /**
     * Afficher les détails d'un entrepôt
     */
    public function show(Warehouse $warehouse)
    {
        if (!$this->checkRoles(['administrateur', 'magasinier', 'responsable_achats'])) {
            return $this->unauthorizedResponse();
        }

        // Statistiques de l'entrepôt
        $stats = $this->getWarehouseDetailStats($warehouse);

        // Produits en stock faible
        $lowStockProducts = $warehouse->getLowStockProducts();

        // Produits en rupture
        $outOfStockProducts = $warehouse->getOutOfStockProducts();

        // Mouvements de stock récents
        $recentMovements = $warehouse->stockMovements()
                                   ->with(['product', 'createdBy'])
                                   ->orderBy('created_at', 'desc')
                                   ->limit(20)
                                   ->get();

        $this->logActivity('view', $warehouse, [], [], "Consultation de l'entrepôt {$warehouse->name}");

        return view('warehouses.show', compact('warehouse', 'stats', 'lowStockProducts', 'outOfStockProducts', 'recentMovements'));
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(Warehouse $warehouse)
    {
        if (!$this->checkRoles(['administrateur'])) {
            return $this->unauthorizedResponse();
        }

        return view('warehouses.edit', compact('warehouse'));
    }

    /**
     * Mettre à jour un entrepôt
     */
    public function update(Request $request, Warehouse $warehouse)
    {
        if (!$this->checkRoles(['administrateur'])) {
            return $this->unauthorizedResponse();
        }

        $validated = $this->validateWarehouse($request, $warehouse->id);
        $oldValues = $warehouse->toArray();

        try {
            DB::beginTransaction();

            $warehouse->update($validated);

            DB::commit();

            $this->logActivity('update', $warehouse, $oldValues, $warehouse->toArray(), "Modification de l'entrepôt {$warehouse->name}");

            if ($request->expectsJson()) {
                return $this->successResponse($warehouse, 'Entrepôt modifié avec succès');
            }

            return $this->redirectWithSuccess('warehouses.index', 'Entrepôt modifié avec succès');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleDatabaseError($e, 'modification de l\'entrepôt');
        }
    }

    /**
     * Stock de l'entrepôt
     */
    public function stock(Warehouse $warehouse, Request $request)
    {
        if (!$this->checkRoles(['administrateur', 'magasinier', 'responsable_achats'])) {
            return $this->unauthorizedResponse();
        }

        $query = $warehouse->warehouseStocks()->with(['product.family']);

        // Filtres
        $this->applyStockFilters($query, $request);

        $stocks = $query->paginate(50);

        return view('warehouses.stock', compact('warehouse', 'stocks'));
    }

    /**
     * Transférer du stock entre entrepôts
     */
    public function transfer(Request $request)
    {
        if (!$this->checkRoles(['administrateur', 'magasinier'])) {
            return $this->unauthorizedResponse();
        }

        if ($request->isMethod('GET')) {
            $warehouses = Warehouse::active()->get();
            $products = Product::active()->with('warehouseStocks')->get();

            return view('warehouses.transfer', compact('warehouses', 'products'));
        }

        $validated = $request->validate([
            'from_warehouse_id' => 'required|exists:warehouses,id',
            'to_warehouse_id' => 'required|exists:warehouses,id|different:from_warehouse_id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'reason' => 'nullable|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            $product = Product::findOrFail($validated['product_id']);
            $fromWarehouse = Warehouse::findOrFail($validated['from_warehouse_id']);
            $toWarehouse = Warehouse::findOrFail($validated['to_warehouse_id']);

            // Vérifier le stock disponible dans l'entrepôt source
            $sourceStock = WarehouseStock::where('warehouse_id', $fromWarehouse->id)
                                       ->where('product_id', $product->id)
                                       ->first();

            if (!$sourceStock || $sourceStock->quantity < $validated['quantity']) {
                return $this->errorResponse('Stock insuffisant dans l\'entrepôt source');
            }

            // Créer le mouvement de sortie
            $exitMovement = StockMovement::create([
                'reference' => StockMovement::generateReference('transfert'),
                'product_id' => $product->id,
                'warehouse_id' => $fromWarehouse->id,
                'type' => 'sortie',
                'reason' => 'transfert_sortant',
                'quantity' => $validated['quantity'],
                'stock_before' => $sourceStock->quantity,
                'stock_after' => $sourceStock->quantity - $validated['quantity'],
                'unit_cost' => $product->purchase_price,
                'total_cost' => $validated['quantity'] * $product->purchase_price,
                'movement_date' => now()->toDateString(),
                'movement_time' => now()->format('H:i:s'),
                'created_by' => auth()->id(),
                'notes' => $validated['reason'] ?? "Transfert vers {$toWarehouse->name}",
            ]);

            // Créer le mouvement d'entrée
            $entryMovement = StockMovement::create([
                'reference' => StockMovement::generateReference('transfert'),
                'product_id' => $product->id,
                'warehouse_id' => $toWarehouse->id,
                'type' => 'entree',
                'reason' => 'transfert_entrant',
                'quantity' => $validated['quantity'],
                'stock_before' => $toWarehouse->getProductStock($product),
                'stock_after' => $toWarehouse->getProductStock($product) + $validated['quantity'],
                'unit_cost' => $product->purchase_price,
                'total_cost' => $validated['quantity'] * $product->purchase_price,
                'movement_date' => now()->toDateString(),
                'movement_time' => now()->format('H:i:s'),
                'created_by' => auth()->id(),
                'notes' => $validated['reason'] ?? "Transfert depuis {$fromWarehouse->name}",
            ]);

            // Mettre à jour les stocks des entrepôts
            $sourceStock->updateQuantity($validated['quantity'], 'subtract');

            $targetStock = WarehouseStock::firstOrCreate([
                'warehouse_id' => $toWarehouse->id,
                'product_id' => $product->id,
            ]);
            $targetStock->updateQuantity($validated['quantity'], 'add');

            DB::commit();

            $this->logActivity('create', $exitMovement, [], [], 
                "Transfert de {$validated['quantity']} {$product->name} de {$fromWarehouse->name} vers {$toWarehouse->name}"
            );

            if ($request->expectsJson()) {
                return $this->successResponse([
                    'exit_movement' => $exitMovement,
                    'entry_movement' => $entryMovement,
                ], 'Transfert effectué avec succès');
            }

            return $this->backWithSuccess('Transfert effectué avec succès');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleDatabaseError($e, 'transfert de stock');
        }
    }

    /**
     * Supprimer un entrepôt
     */
    public function destroy(Warehouse $warehouse)
    {
        if (!$this->checkRoles(['administrateur'])) {
            return $this->unauthorizedResponse();
        }

        try {
            // Vérifier si l'entrepôt peut être supprimé
            if ($warehouse->warehouseStocks()->where('quantity', '>', 0)->exists()) {
                return $this->errorResponse('Impossible de supprimer cet entrepôt car il contient encore du stock');
            }

            if ($warehouse->sales()->exists() || $warehouse->purchaseOrders()->exists()) {
                return $this->errorResponse('Impossible de supprimer cet entrepôt car il a des ventes ou commandes associées');
            }

            DB::beginTransaction();

            $warehouseName = $warehouse->name;
            
            // Supprimer les stocks vides
            $warehouse->warehouseStocks()->delete();
            
            // Supprimer l'entrepôt
            $warehouse->delete();

            DB::commit();

            $this->logActivity('delete', $warehouse, $warehouse->toArray(), [], "Suppression de l'entrepôt {$warehouseName}");

            if (request()->expectsJson()) {
                return $this->successResponse(null, 'Entrepôt supprimé avec succès');
            }

            return $this->redirectWithSuccess('warehouses.index', 'Entrepôt supprimé avec succès');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleDatabaseError($e, 'suppression de l\'entrepôt');
        }
    }

    /**
     * Rapport de stock par entrepôt
     */
    public function stockReport(Warehouse $warehouse, Request $request)
    {
        if (!$this->checkRoles(['administrateur', 'magasinier', 'responsable_achats'])) {
            return $this->unauthorizedResponse();
        }

        $query = $warehouse->warehouseStocks()
                          ->with(['product.family', 'product.supplier']);

        // Filtres pour le rapport
        if ($filter = $request->get('filter')) {
            switch ($filter) {
                case 'low_stock':
                    $query->lowStock();
                    break;
                case 'out_of_stock':
                    $query->outOfStock();
                    break;
                case 'overstock':
                    $query->overstock();
                    break;
            }
        }

        if ($familyId = $request->get('family_id')) {
            $query->whereHas('product', function ($q) use ($familyId) {
                $q->where('family_id', $familyId);
            });
        }

        $stocks = $query->paginate(100);
        $families = Family::active()->get();

        return view('warehouses.stock-report', compact('warehouse', 'stocks', 'families'));
    }

    /**
     * Méthodes utilitaires privées
     */
    private function validateWarehouse(Request $request, $warehouseId = null)
    {
        $rules = [
            'name' => 'required|string|max:255|unique:warehouses,name,' . $warehouseId,
            'code' => 'required|string|max:20|unique:warehouses,code,' . $warehouseId,
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'manager_name' => 'nullable|string|max:255',
            'manager_phone' => 'nullable|string|max:20',
            'is_active' => 'boolean',
            'notes' => 'nullable|string|max:1000',
        ];

        return $request->validate($rules);
    }

    private function applyWarehouseFilters($query, Request $request)
    {
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%");
            });
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        if ($city = $request->get('city')) {
            $query->where('city', $city);
        }
    }

    private function applyStockFilters($query, Request $request)
    {
        if ($search = $request->get('search')) {
            $query->whereHas('product', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($familyId = $request->get('family_id')) {
            $query->whereHas('product', function ($q) use ($familyId) {
                $q->where('family_id', $familyId);
            });
        }

        if ($filter = $request->get('stock_filter')) {
            switch ($filter) {
                case 'low_stock':
                    $query->lowStock();
                    break;
                case 'out_of_stock':
                    $query->outOfStock();
                    break;
            }
        }
    }

    private function getWarehouseStats()
    {
        return [
            'total_warehouses' => Warehouse::count(),
            'active_warehouses' => Warehouse::active()->count(),
            'total_products_in_stock' => WarehouseStock::sum('quantity'),
            'low_stock_products' => WarehouseStock::lowStock()->count(),
            'out_of_stock_products' => WarehouseStock::outOfStock()->count(),
        ];
    }

    private function getWarehouseDetailStats(Warehouse $warehouse)
    {
        $stocks = $warehouse->warehouseStocks();
        
        return [
            'total_products' => $stocks->count(),
            'total_quantity' => $stocks->sum('quantity'),
            'total_value' => $stocks->with('product')->get()->sum(function ($stock) {
                return $stock->quantity * $stock->product->purchase_price;
            }),
            'low_stock_count' => $stocks->lowStock()->count(),
            'out_of_stock_count' => $stocks->outOfStock()->count(),
        ];
    }
}