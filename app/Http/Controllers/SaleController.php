<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Warehouse;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SaleController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->model = Sale::class;
        $this->modelName = 'Vente';
        $this->viewPath = 'sales';
    }

    /**
     * Afficher la liste des ventes
     */
    public function index(Request $request)
    {
        if (!$this->checkRoles(['administrateur', 'responsable_commercial', 'vendeur', 'caissiere', 'comptable'])) {
            return $this->unauthorizedResponse();
        }

        $query = Sale::with(['customer', 'warehouse', 'cashier', 'saleDetails.product']);

        // Filtrer par utilisateur si pas administrateur
        if (!auth()->user()->hasRole('administrateur') && !auth()->user()->hasRole('responsable_commercial') && !auth()->user()->hasRole('comptable')) {
            $query->where('cashier_id', auth()->id());
        }

        // Appliquer les filtres
        $this->applySaleFilters($query, $request);

        // Tri et pagination
        $pagination = $this->getPaginationData($request);
        
        $sales = $query->orderBy($pagination['sortBy'], $pagination['sortDirection'])
                      ->paginate($pagination['perPage']);

        // Statistiques
        $stats = $this->getSalesStats($request);

        $customers = Customer::active()->get();
        $warehouses = Warehouse::active()->get();

        return view('sales.index', compact('sales', 'stats', 'customers', 'warehouses'));
    }

    /**
     * Interface de vente (caisse)
     */
    public function create()
    {
        if (!$this->checkRoles(['administrateur', 'responsable_commercial', 'vendeur', 'caissiere'])) {
            return $this->unauthorizedResponse();
        }

        $customers = Customer::active()->get();
        $warehouses = Warehouse::active()->get();
        $defaultWarehouse = $warehouses->first();

        return view('sales.create', compact('customers', 'warehouses', 'defaultWarehouse'));
    }

    /**
     * Enregistrer une nouvelle vente
     */
    public function store(Request $request)
    {
        if (!$this->checkRoles(['administrateur', 'responsable_commercial', 'vendeur', 'caissiere'])) {
            return $this->unauthorizedResponse();
        }

        $validated = $this->validateSale($request);

        try {
            DB::beginTransaction();

            // Créer la vente
            $sale = Sale::create([
                'reference' => $this->generateSaleReference(),
                'ticket_number' => $this->generateTicketNumber(),
                'customer_id' => $validated['customer_id'],
                'warehouse_id' => $validated['warehouse_id'],
                'sale_date' => now()->toDateString(),
                'sale_time' => now()->format('H:i:s'),
                'type' => $validated['type'] ?? 'caisse',
                'status' => 'en_cours',
                'payment_method' => $validated['payment_method'] ?? 'especes',
                'cashier_id' => auth()->id(),
                'customer_name' => $validated['customer_name'],
                'customer_phone' => $validated['customer_phone'],
                'notes' => $validated['notes'],
            ]);

            // Ajouter les détails de vente
            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);

                // Vérifier la disponibilité du stock
                if (!$product->canBeSold($item['quantity'])) {
                    throw new \Exception("Stock insuffisant pour le produit {$product->name}");
                }

                $saleDetail = SaleDetail::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'discount_percentage' => $item['discount_percentage'] ?? 0,
                    'tax_rate' => $product->tax_rate,
                ]);

                $saleDetail->calculateTotals();
                $saleDetail->save();

                // Mettre à jour le stock si la vente est validée immédiatement
                if ($validated['validate_immediately'] ?? false) {
                    $this->updateProductStock($product, $item['quantity'], $validated['warehouse_id'], $sale);
                }
            }

            // Calculer les totaux de la vente
            $sale->calculateTotals();

            // Gérer le paiement si fourni
            if ($validated['amount_paid'] ?? 0 > 0) {
                $sale->addPayment($validated['amount_paid'], $validated['payment_method']);
                
                if ($validated['amount_paid'] > $sale->total_ttc) {
                    $sale->change_given = $validated['amount_paid'] - $sale->total_ttc;
                }
            }

            // Valider automatiquement si demandé
            if ($validated['validate_immediately'] ?? false) {
                $sale->status = 'validee';
            }

            $sale->save();

            DB::commit();

            $this->logActivity('create', $sale, [], $sale->toArray(), "Création de la vente {$sale->reference}");

            if ($request->expectsJson()) {
                return $this->successResponse($sale->load('saleDetails.product'), 'Vente créée avec succès');
            }

            return $this->redirectWithSuccess('sales.show', 'Vente créée avec succès')->with('sale_id', $sale->id);

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleDatabaseError($e, 'création de la vente');
        }
    }

    /**
     * Afficher les détails d'une vente
     */
    public function show(Sale $sale)
    {
        if (!$this->checkRoles(['administrateur', 'responsable_commercial', 'vendeur', 'caissiere', 'comptable'])) {
            return $this->unauthorizedResponse();
        }

        // Vérifier si l'utilisateur peut voir cette vente
        if (!auth()->user()->hasAnyRole(['administrateur', 'responsable_commercial', 'comptable']) && 
            $sale->cashier_id !== auth()->id()) {
            return $this->unauthorizedResponse('Vous ne pouvez consulter que vos propres ventes');
        }

        $sale->load([
            'customer', 
            'warehouse', 
            'cashier', 
            'saleDetails.product',
            'payments',
            'invoice'
        ]);

        $this->logActivity('view', $sale, [], [], "Consultation de la vente {$sale->reference}");

        return view('sales.show', compact('sale'));
    }

    /**
     * Valider une vente
     */
    public function validate(Sale $sale)
    {
        if (!$this->checkRoles(['administrateur', 'responsable_commercial', 'vendeur', 'caissiere'])) {
            return $this->unauthorizedResponse();
        }

        if ($sale->status !== 'en_cours') {
            return $this->errorResponse('Cette vente ne peut plus être validée');
        }

        try {
            DB::beginTransaction();

            // Mettre à jour les stocks pour chaque produit
            foreach ($sale->saleDetails as $detail) {
                $this->updateProductStock($detail->product, $detail->quantity, $sale->warehouse_id, $sale);
            }

            $sale->status = 'validee';
            $sale->save();

            DB::commit();

            $this->logActivity('validate', $sale, [], [], "Validation de la vente {$sale->reference}");

            if (request()->expectsJson()) {
                return $this->successResponse($sale, 'Vente validée avec succès');
            }

            return $this->backWithSuccess('Vente validée avec succès');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleDatabaseError($e, 'validation de la vente');
        }
    }

    /**
     * Annuler une vente
     */
    public function cancel(Sale $sale)
    {
        if (!$this->checkRoles(['administrateur', 'responsable_commercial'])) {
            return $this->unauthorizedResponse();
        }

        if (!$sale->canBeCancelled()) {
            return $this->errorResponse('Cette vente ne peut pas être annulée');
        }

        try {
            DB::beginTransaction();

            // Remettre les stocks si la vente était validée
            if ($sale->status === 'validee') {
                foreach ($sale->saleDetails as $detail) {
                    $this->restoreProductStock($detail->product, $detail->quantity, $sale->warehouse_id, $sale);
                }
            }

            $sale->cancel();

            DB::commit();

            $this->logActivity('cancel', $sale, [], [], "Annulation de la vente {$sale->reference}");

            if (request()->expectsJson()) {
                return $this->successResponse($sale, 'Vente annulée avec succès');
            }

            return $this->backWithSuccess('Vente annulée avec succès');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleDatabaseError($e, 'annulation de la vente');
        }
    }

    /**
     * Ajouter un paiement à une vente
     */
    public function addPayment(Request $request, Sale $sale)
    {
        if (!$this->checkRoles(['administrateur', 'responsable_commercial', 'vendeur', 'caissiere'])) {
            return $this->unauthorizedResponse();
        }

        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:especes,cheque,virement,carte,assurance',
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            // Créer le paiement
            $payment = \App\Models\Payment::create([
                'reference' => \App\Models\Payment::generateReference('encaissement'),
                'customer_id' => $sale->customer_id,
                'sale_id' => $sale->id,
                'payment_date' => now()->toDateString(),
                'amount' => $request->amount,
                'type' => 'encaissement',
                'method' => $request->payment_method,
                'reference_number' => $request->reference_number,
                'status' => 'valide',
                'created_by' => auth()->id(),
                'notes' => $request->notes,
            ]);

            // Mettre à jour la vente
            $sale->addPayment($request->amount, $request->payment_method);

            DB::commit();

            $this->logActivity('create', $payment, [], $payment->toArray(), "Paiement ajouté à la vente {$sale->reference}");

            if ($request->expectsJson()) {
                return $this->successResponse([
                    'payment' => $payment,
                    'sale' => $sale->fresh(),
                ], 'Paiement ajouté avec succès');
            }

            return $this->backWithSuccess('Paiement ajouté avec succès');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleDatabaseError($e, 'ajout du paiement');
        }
    }

    /**
     * Imprimer le ticket de vente
     */
    public function printTicket(Sale $sale)
    {
        if (!$this->checkRoles(['administrateur', 'responsable_commercial', 'vendeur', 'caissiere'])) {
            return $this->unauthorizedResponse();
        }

        $sale->load(['customer', 'warehouse', 'cashier', 'saleDetails.product']);

        $this->logActivity('view', $sale, [], [], "Impression ticket vente {$sale->reference}");

        return view('sales.ticket', compact('sale'));
    }

    /**
     * Exporter les ventes
     */
    public function export(Request $request)
    {
        if (!$this->checkPermission('export_sales')) {
            return $this->unauthorizedResponse();
        }

        $query = Sale::with(['customer', 'warehouse', 'cashier']);
        $this->applySaleFilters($query, $request);

        $sales = $query->get();

        $headers = [
            'Référence', 'Date', 'Heure', 'Client', 'Caissier', 'Entrepôt',
            'Type', 'Statut', 'Total HT', 'TVA', 'Total TTC',
            'Montant payé', 'Solde', 'Mode de paiement'
        ];

        $data = $sales->map(function($sale) {
            return [
                $sale->reference,
                $sale->sale_date->format('d/m/Y'),
                $sale->sale_time,
                $sale->customer?->full_name ?? $sale->customer_name ?? 'Client direct',
                $sale->cashier->name,
                $sale->warehouse->name,
                $sale->type_label,
                $sale->status_label,
                $sale->total_ht,
                $sale->total_tax,
                $sale->total_ttc,
                $sale->amount_paid,
                $sale->amount_due,
                $sale->payment_method_label,
            ];
        });

        $this->logActivity('export', null, [], [], 'Export de la liste des ventes');

        return $this->exportToCsv($data, 'ventes', $headers);
    }

    // Méthodes privées utilitaires

    private function applySaleFilters($query, Request $request)
    {
        // Recherche par référence ou client
        if ($search = $request->get('search')) {
            $query->where(function($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($subQ) use ($search) {
                      $subQ->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filtres par dates
        $dateFilters = $this->getDateFilters($request);
        if ($dateFilters['startDate']) {
            $query->where('sale_date', '>=', $dateFilters['startDate']);
        }
        if ($dateFilters['endDate']) {
            $query->where('sale_date', '<=', $dateFilters['endDate']);
        }

        // Filtres par statut, type, etc.
        foreach (['customer_id', 'warehouse_id', 'type', 'status', 'payment_method'] as $filter) {
            if ($value = $request->get($filter)) {
                $query->where($filter, $value);
            }
        }

        // Filtres par caissier
        if ($cashierId = $request->get('cashier_id')) {
            $query->where('cashier_id', $cashierId);
        }

        // Filtres spéciaux
        switch ($request->get('filter')) {
            case 'today':
                $query->whereDate('sale_date', Carbon::today());
                break;
            case 'this_week':
                $query->whereBetween('sale_date', [
                    Carbon::now()->startOfWeek(),
                    Carbon::now()->endOfWeek()
                ]);
                break;
            case 'this_month':
                $query->whereMonth('sale_date', Carbon::now()->month)
                      ->whereYear('sale_date', Carbon::now()->year);
                break;
            case 'paid':
                $query->paid();
                break;
            case 'unpaid':
                $query->unpaid();
                break;
            case 'partially_paid':
                $query->partiallyPaid();
                break;
        }
    }

    private function validateSale(Request $request)
    {
        return $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'customer_name' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'type' => 'in:caisse,vente_differee,proforma,assurance,depot',
            'payment_method' => 'in:especes,cheque,virement,carte,credit,assurance',
            'amount_paid' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:500',
            'validate_immediately' => 'boolean',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount_percentage' => 'nullable|numeric|min:0|max:100',
        ], [
            'warehouse_id.required' => 'L\'entrepôt est obligatoire',
            'items.required' => 'Au moins un produit doit être ajouté',
            'items.*.product_id.required' => 'Le produit est obligatoire',
            'items.*.quantity.required' => 'La quantité est obligatoire',
            'items.*.quantity.min' => 'La quantité doit être supérieure à 0',
            'items.*.unit_price.required' => 'Le prix unitaire est obligatoire',
        ]);
    }

    private function updateProductStock(Product $product, int $quantity, int $warehouseId, Sale $sale)
    {
        // Créer le mouvement de stock
        StockMovement::create([
            'reference' => StockMovement::generateReference('sortie'),
            'product_id' => $product->id,
            'warehouse_id' => $warehouseId,
            'type' => 'sortie',
            'reason' => 'vente',
            'quantity' => $quantity,
            'stock_before' => $product->current_stock,
            'stock_after' => $product->current_stock - $quantity,
            'unit_cost' => $product->purchase_price,
            'total_cost' => $quantity * $product->purchase_price,
            'movement_date' => now()->toDateString(),
            'movement_time' => now()->format('H:i:s'),
            'related_sale_id' => $sale->id,
            'created_by' => auth()->id(),
            'notes' => "Vente {$sale->reference}",
        ]);

        // Mettre à jour le stock du produit
        $product->updateStock($quantity, 'subtract');

        // Mettre à jour le stock dans l'entrepôt
        $warehouseStock = \App\Models\WarehouseStock::where('warehouse_id', $warehouseId)
                                                   ->where('product_id', $product->id)
                                                   ->first();
        if ($warehouseStock) {
            $warehouseStock->updateQuantity($quantity, 'subtract');
        }
    }

    private function restoreProductStock(Product $product, int $quantity, int $warehouseId, Sale $sale)
    {
        // Créer le mouvement de stock de restauration
        StockMovement::create([
            'reference' => StockMovement::generateReference('entree'),
            'product_id' => $product->id,
            'warehouse_id' => $warehouseId,
            'type' => 'entree',
            'reason' => 'retour_client',
            'quantity' => $quantity,
            'stock_before' => $product->current_stock,
            'stock_after' => $product->current_stock + $quantity,
            'unit_cost' => $product->purchase_price,
            'total_cost' => $quantity * $product->purchase_price,
            'movement_date' => now()->toDateString(),
            'movement_time' => now()->format('H:i:s'),
            'related_sale_id' => $sale->id,
            'created_by' => auth()->id(),
            'notes' => "Annulation vente {$sale->reference}",
        ]);

        // Restaurer le stock du produit
        $product->updateStock($quantity, 'add');

        // Restaurer le stock dans l'entrepôt
        $warehouseStock = \App\Models\WarehouseStock::where('warehouse_id', $warehouseId)
                                                   ->where('product_id', $product->id)
                                                   ->first();
        if ($warehouseStock) {
            $warehouseStock->updateQuantity($quantity, 'add');
        }
    }

    private function getSalesStats(Request $request): array
    {
        $query = Sale::query();
        $this->applySaleFilters($query, $request);

        $baseStats = [
            'total_sales' => $query->count(),
            'total_amount' => $query->sum('total_ttc'),
            'paid_amount' => $query->sum('amount_paid'),
            'pending_amount' => $query->sum('amount_due'),
        ];

        // Stats par statut
        $statusStats = Sale::selectRaw('status, COUNT(*) as count, SUM(total_ttc) as amount')
                          ->groupBy('status')
                          ->get()
                          ->keyBy('status')
                          ->map(function($item) {
                              return [
                                  'count' => $item->count,
                                  'amount' => $item->amount
                              ];
                          })
                          ->toArray();

        // Stats du jour
        $todayStats = [
            'today_sales' => Sale::whereDate('sale_date', Carbon::today())->count(),
            'today_amount' => Sale::whereDate('sale_date', Carbon::today())->sum('total_ttc'),
        ];

        return array_merge($baseStats, $statusStats, $todayStats);
    }

    private function generateSaleReference(): string
    {
        $date = now()->format('Ymd');
        $count = Sale::whereDate('created_at', now())->count() + 1;
        
        return sprintf('VTE-%s-%04d', $date, $count);
    }

    private function generateTicketNumber(): string
    {
        $date = now()->format('Ymd');
        $count = Sale::whereDate('created_at', now())->count() + 1;
        
        return sprintf('T%s%04d', $date, $count);
    }
}