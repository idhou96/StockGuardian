<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Family;
use App\Models\Supplier;
use App\Models\ActivePrinciple;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProductController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->model = Product::class;
        $this->modelName = 'Produit';
        $this->viewPath = 'products';
    }

    /**
     * Afficher la liste des produits
     */
    public function index(Request $request)
    {
        if (!$this->checkRoles(['administrateur', 'magasinier', 'responsable_achats', 'vendeur', 'caissiere'])) {
            return $this->unauthorizedResponse();
        }

        $query = Product::with(['family', 'supplier', 'activePrinciples']);

        // Filtres
        $this->applyFilters($query, $request);

        // Tri et pagination
        $pagination = $this->getPaginationData($request);
        
        $products = $query->orderBy($pagination['sortBy'], $pagination['sortDirection'])
                         ->paginate($pagination['perPage']);

        // Statistiques pour les cartes
        $stats = $this->getProductStats();

        $families = Family::active()->get();
        $suppliers = Supplier::active()->get();

        return view('products.index', compact('products', 'stats', 'families', 'suppliers'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        if (!$this->checkRoles(['administrateur', 'magasinier', 'responsable_achats'])) {
            return $this->unauthorizedResponse();
        }

        $families = Family::active()->get();
        $suppliers = Supplier::active()->get();
        $activePrinciples = ActivePrinciple::active()->get();

        return view('products.create', compact('families', 'suppliers', 'activePrinciples'));
    }

    /**
     * Enregistrer un nouveau produit
     */
    public function store(Request $request)
    {
        if (!$this->checkRoles(['administrateur', 'magasinier', 'responsable_achats'])) {
            return $this->unauthorizedResponse();
        }

        $validated = $this->validateProduct($request);

        try {
            DB::beginTransaction();

            $product = Product::create($validated);

            // Gérer les principes actifs
            if ($request->has('active_principles')) {
                $this->syncActivePrinciples($product, $request->active_principles);
            }

            // Calculer automatiquement le prix de vente si marge définie
            if ($validated['margin_percentage'] > 0) {
                $product->calculateSalePriceFromMargin();
                $product->save();
            }

            DB::commit();

            $this->logActivity('create', $product, [], $product->toArray(), "Création du produit {$product->name}");

            if ($request->expectsJson()) {
                return $this->successResponse($product, 'Produit créé avec succès');
            }

            return $this->redirectWithSuccess('products.index', 'Produit créé avec succès');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleDatabaseError($e, 'création du produit');
        }
    }

    /**
     * Afficher les détails d'un produit
     */
    public function show(Product $product)
    {
        if (!$this->checkRoles(['administrateur', 'magasinier', 'responsable_achats', 'vendeur', 'caissiere'])) {
            return $this->unauthorizedResponse();
        }

        $product->load(['family', 'supplier', 'activePrinciples', 'warehouseStocks.warehouse']);

        // Statistiques du produit
        $stats = $this->getProductDetailStats($product);

        // Mouvements de stock récents
        $recentMovements = $product->stockMovements()
                                 ->with(['warehouse', 'createdBy'])
                                 ->orderBy('created_at', 'desc')
                                 ->limit(20)
                                 ->get();

        $this->logActivity('view', $product, [], [], "Consultation du produit {$product->name}");

        return view('products.show', compact('product', 'stats', 'recentMovements'));
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(Product $product)
    {
        if (!$this->checkRoles(['administrateur', 'magasinier', 'responsable_achats'])) {
            return $this->unauthorizedResponse();
        }

        $families = Family::active()->get();
        $suppliers = Supplier::active()->get();
        $activePrinciples = ActivePrinciple::active()->get();
        
        $product->load('activePrinciples');

        return view('products.edit', compact('product', 'families', 'suppliers', 'activePrinciples'));
    }

    /**
     * Mettre à jour un produit
     */
    public function update(Request $request, Product $product)
    {
        if (!$this->checkRoles(['administrateur', 'magasinier', 'responsable_achats'])) {
            return $this->unauthorizedResponse();
        }

        $validated = $this->validateProduct($request, $product->id);
        $oldValues = $product->toArray();

        try {
            DB::beginTransaction();

            $product->update($validated);

            // Gérer les principes actifs
            if ($request->has('active_principles')) {
                $this->syncActivePrinciples($product, $request->active_principles);
            }

            // Recalculer les prix si nécessaire
            if ($request->has('margin_percentage') && $validated['margin_percentage'] > 0) {
                $product->calculateSalePriceFromMargin();
            } elseif ($request->has('sale_price')) {
                $product->calculateMarginFromSalePrice();
            }

            $product->save();

            DB::commit();

            $this->logActivity('update', $product, $oldValues, $product->toArray(), "Modification du produit {$product->name}");

            if ($request->expectsJson()) {
                return $this->successResponse($product, 'Produit modifié avec succès');
            }

            return $this->redirectWithSuccess('products.index', 'Produit modifié avec succès');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleDatabaseError($e, 'modification du produit');
        }
    }

    /**
     * Supprimer un produit
     */
    public function destroy(Product $product)
    {
        if (!$this->checkRoles(['administrateur'])) {
            return $this->unauthorizedResponse();
        }

        try {
            // Vérifier si le produit peut être supprimé
            if ($product->saleDetails()->exists() || $product->purchaseOrderDetails()->exists()) {
                return $this->errorResponse('Impossible de supprimer ce produit car il est utilisé dans des ventes ou commandes');
            }

            $productName = $product->name;
            $product->delete();

            $this->logActivity('delete', $product, $product->toArray(), [], "Suppression du produit {$productName}");

            if (request()->expectsJson()) {
                return $this->successResponse(null, 'Produit supprimé avec succès');
            }

            return $this->redirectWithSuccess('products.index', 'Produit supprimé avec succès');

        } catch (\Exception $e) {
            return $this->handleDatabaseError($e, 'suppression du produit');
        }
    }

    /**
     * Recherche de produits (pour AJAX)
     */
    public function search(Request $request)
    {
        $search = $request->get('q', '');
        $limit = $request->get('limit', 10);

        $products = Product::search($search)
                          ->active()
                          ->with(['family', 'supplier'])
                          ->limit($limit)
                          ->get();

        return $this->successResponse($products->map(function($product) {
            return [
                'id' => $product->id,
                'code' => $product->code,
                'name' => $product->name,
                'family' => $product->family?->name,
                'supplier' => $product->supplier?->name,
                'sale_price' => $product->sale_price,
                'current_stock' => $product->current_stock,
                'unit' => $product->unit,
                'is_low_stock' => $product->is_low_stock,
                'is_out_of_stock' => $product->is_out_of_stock,
            ];
        }));
    }

    /**
     * Ajuster le stock d'un produit
     */
    public function adjustStock(Request $request, Product $product)
    {
        if (!$this->checkRoles(['administrateur', 'magasinier'])) {
            return $this->unauthorizedResponse();
        }

        $request->validate([
            'adjustment_type' => 'required|in:add,subtract,set',
            'quantity' => 'required|integer|min:0',
            'reason' => 'required|string|max:255',
            'warehouse_id' => 'required|exists:warehouses,id',
        ]);

        try {
            DB::beginTransaction();

            $oldStock = $product->current_stock;
            $adjustment = $request->quantity;
            
            $newStock = match($request->adjustment_type) {
                'add' => $oldStock + $adjustment,
                'subtract' => max(0, $oldStock - $adjustment),
                'set' => $adjustment,
            };

            $actualAdjustment = $newStock - $oldStock;

            if ($actualAdjustment != 0) {
                // Créer le mouvement de stock
                \App\Models\StockMovement::create([
                    'reference' => \App\Models\StockMovement::generateReference('ajustement'),
                    'product_id' => $product->id,
                    'warehouse_id' => $request->warehouse_id,
                    'type' => $actualAdjustment > 0 ? 'entree' : 'sortie',
                    'reason' => 'ajustement_' . ($actualAdjustment > 0 ? 'positif' : 'negatif'),
                    'quantity' => abs($actualAdjustment),
                    'stock_before' => $oldStock,
                    'stock_after' => $newStock,
                    'unit_cost' => $product->purchase_price,
                    'total_cost' => abs($actualAdjustment) * $product->purchase_price,
                    'movement_date' => now()->toDateString(),
                    'movement_time' => now()->format('H:i:s'),
                    'created_by' => auth()->id(),
                    'notes' => $request->reason,
                ]);

                // Mettre à jour le stock du produit
                $product->current_stock = $newStock;
                $product->save();

                // Mettre à jour le stock dans l'entrepôt
                $warehouseStock = \App\Models\WarehouseStock::firstOrCreate([
                    'warehouse_id' => $request->warehouse_id,
                    'product_id' => $product->id,
                ]);
                $warehouseStock->quantity = $newStock;
                $warehouseStock->save();
            }

            DB::commit();

            $this->logActivity('update', $product, ['current_stock' => $oldStock], ['current_stock' => $newStock], "Ajustement stock: {$request->reason}");

            return $this->successResponse([
                'old_stock' => $oldStock,
                'new_stock' => $newStock,
                'adjustment' => $actualAdjustment,
            ], 'Stock ajusté avec succès');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleDatabaseError($e, 'ajustement du stock');
        }
    }

    /**
     * Exporter les produits
     */
    public function export(Request $request)
    {
        if (!$this->checkPermission('export_products')) {
            return $this->unauthorizedResponse();
        }

        $query = Product::with(['family', 'supplier']);
        $this->applyFilters($query, $request);

        $products = $query->get();

        $headers = [
            'Code', 'Nom', 'Description', 'Famille', 'Fournisseur',
            'Prix d\'achat', 'Prix de vente', 'Stock actuel', 'Stock minimum',
            'Stock maximum', 'Unité', 'Statut'
        ];

        $data = $products->map(function($product) {
            return [
                $product->code,
                $product->name,
                $product->description,
                $product->family?->name ?? '',
                $product->supplier?->name ?? '',
                $product->purchase_price,
                $product->sale_price,
                $product->current_stock,
                $product->minimum_stock,
                $product->maximum_stock,
                $product->unit,
                $product->is_active ? 'Actif' : 'Inactif',
            ];
        });

        $this->logActivity('export', null, [], [], 'Export de la liste des produits');

        return $this->exportToCsv($data, 'produits', $headers);
    }

    // Méthodes utilitaires privées

    private function applyFilters($query, Request $request)
    {
        // Recherche textuelle
        if ($search = $request->get('search')) {
            $query->search($search);
        }

        // Filtres par famille
        if ($familyId = $request->get('family_id')) {
            $query->where('family_id', $familyId);
        }

        // Filtres par fournisseur
        if ($supplierId = $request->get('supplier_id')) {
            $query->where('supplier_id', $supplierId);
        }

        // Filtres spéciaux
        switch ($request->get('filter')) {
            case 'active':
                $query->active();
                break;
            case 'inactive':
                $query->where('is_active', false);
                break;
            case 'in_stock':
                $query->inStock();
                break;
            case 'out_of_stock':
                $query->outOfStock();
                break;
            case 'low_stock':
                $query->lowStock();
                break;
            case 'expired':
                $query->expired();
                break;
            case 'expiring_soon':
                $query->expiringSoon();
                break;
            case 'pharmaceutical':
                $query->pharmaceutical();
                break;
            case 'dangerous':
                $query->dangerous();
                break;
        }

        // Filtres par prix
        if ($minPrice = $request->get('min_price')) {
            $query->where('sale_price', '>=', $minPrice);
        }
        if ($maxPrice = $request->get('max_price')) {
            $query->where('sale_price', '<=', $maxPrice);
        }

        // Filtres par stock
        if ($minStock = $request->get('min_stock')) {
            $query->where('current_stock', '>=', $minStock);
        }
        if ($maxStock = $request->get('max_stock')) {
            $query->where('current_stock', '<=', $maxStock);
        }
    }

    private function validateProduct(Request $request, $productId = null)
    {
        $rules = [
            'code' => 'required|string|max:50|unique:products,code' . ($productId ? ",$productId" : ''),
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'family_id' => 'nullable|exists:families,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'purchase_price' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
            'wholesale_price' => 'nullable|numeric|min:0',
            'margin_percentage' => 'nullable|numeric|min:0|max:1000',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'apply_tax' => 'boolean',
            'current_stock' => 'required|integer|min:0',
            'minimum_stock' => 'required|integer|min:0',
            'maximum_stock' => 'required|integer|min:0',
            'security_stock' => 'nullable|integer|min:0',
            'is_dangerous' => 'boolean',
            'is_pharmaceutical' => 'boolean',
            'is_consumable' => 'boolean',
            'is_mixed' => 'boolean',
            'expiry_date' => 'nullable|date|after:today',
            'batch_number' => 'nullable|string|max:100',
            'geographic_code' => 'required|string|max:50',
            'unit' => 'required|string|max:20',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'wholesale_discount' => 'nullable|numeric|min:0|max:100',
            'barcode' => 'nullable|string|max:50|unique:products,barcode' . ($productId ? ",$productId" : ''),
            'is_active' => 'boolean',
        ];

        $messages = [
            'code.required' => 'Le code produit est obligatoire',
            'code.unique' => 'Ce code produit existe déjà',
            'name.required' => 'Le nom du produit est obligatoire',
            'purchase_price.required' => 'Le prix d\'achat est obligatoire',
            'sale_price.required' => 'Le prix de vente est obligatoire',
            'current_stock.required' => 'Le stock actuel est obligatoire',
            'minimum_stock.required' => 'Le stock minimum est obligatoire',
            'maximum_stock.required' => 'Le stock maximum est obligatoire',
            'geographic_code.required' => 'L\'emplacement est obligatoire',
            'unit.required' => 'L\'unité de mesure est obligatoire',
            'barcode.unique' => 'Ce code-barres existe déjà',
        ];

        return $this->validateData($request, $rules, $messages);
    }

    private function syncActivePrinciples(Product $product, array $activePrinciples)
    {
        $syncData = [];
        foreach ($activePrinciples as $principleData) {
            $syncData[$principleData['id']] = ['dosage' => $principleData['dosage'] ?? null];
        }
        $product->activePrinciples()->sync($syncData);
    }

    private function getProductStats(): array
    {
        return [
            'total' => Product::count(),
            'active' => Product::active()->count(),
            'in_stock' => Product::inStock()->count(),
            'low_stock' => Product::lowStock()->count(),
            'out_of_stock' => Product::outOfStock()->count(),
            'expired' => Product::expired()->count(),
            'expiring_soon' => Product::expiringSoon()->count(),
            'total_value' => Product::sum(DB::raw('current_stock * purchase_price')),
        ];
    }

    private function getProductDetailStats(Product $product): array
    {
        $thirtyDaysAgo = Carbon::now()->subDays(30);
        
        return [
            'total_sales_quantity' => $product->getTotalSalesQuantity(),
            'total_sales_amount' => $product->getTotalSalesAmount(),
            'average_sale_price' => $product->getAverageSalePrice(),
            'sales_last_30_days' => $product->saleDetails()
                ->whereHas('sale', function($q) use ($thirtyDaysAgo) {
                    $q->where('sale_date', '>=', $thirtyDaysAgo);
                })
                ->sum('quantity'),
            'stock_value' => $product->stock_value,
            'margin_amount' => $product->margin_amount,
            'days_until_expiry' => $product->getDaysUntilExpiry(),
            'needs_reorder' => $product->needsReorder(),
            'reorder_quantity' => $product->getReorderQuantity(),
        ];
    }
}