<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDetail;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PurchaseOrderController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->model = PurchaseOrder::class;
        $this->modelName = 'Commande d\'achat';
        $this->viewPath = 'purchase-orders';
    }

    /**
     * Afficher la liste des commandes d'achat
     */
    public function index(Request $request)
    {
        if (!$this->checkRoles(['administrateur', 'responsable_achats', 'magasinier'])) {
            return $this->unauthorizedResponse();
        }

        $query = PurchaseOrder::with(['supplier', 'warehouse', 'createdBy']);

        // Appliquer les filtres
        $this->applyPurchaseOrderFilters($query, $request);

        // Tri et pagination
        $pagination = $this->getPaginationData($request);
        
        $orders = $query->orderBy($pagination['sortBy'], $pagination['sortDirection'])
                       ->paginate($pagination['perPage']);

        // Statistiques
        $stats = $this->getPurchaseOrderStats();

        $suppliers = Supplier::active()->get();
        $warehouses = Warehouse::active()->get();

        return view('purchase-orders.index', compact('orders', 'stats', 'suppliers', 'warehouses'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        if (!$this->checkRoles(['administrateur', 'responsable_achats'])) {
            return $this->unauthorizedResponse();
        }

        $suppliers = Supplier::active()->get();
        $warehouses = Warehouse::active()->get();

        return view('purchase-orders.create', compact('suppliers', 'warehouses'));
    }

    /**
     * Enregistrer une nouvelle commande
     */
    public function store(Request $request)
    {
        if (!$this->checkRoles(['administrateur', 'responsable_achats'])) {
            return $this->unauthorizedResponse();
        }

        $validated = $this->validatePurchaseOrder($request);

        try {
            DB::beginTransaction();

            // Créer la commande
            $order = PurchaseOrder::create([
                'reference' => $this->generatePurchaseOrderReference(),
                'supplier_id' => $validated['supplier_id'],
                'warehouse_id' => $validated['warehouse_id'],
                'order_date' => $validated['order_date'] ?? now()->toDateString(),
                'expected_delivery_date' => $validated['expected_delivery_date'],
                'status' => 'brouillon',
                'created_by' => auth()->id(),
                'notes' => $validated['notes'],
            ]);

            // Ajouter les détails de commande
            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);

                $orderDetail = PurchaseOrderDetail::create([
                    'purchase_order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity_ordered' => $item['quantity_ordered'],
                    'unit_price' => $item['unit_price'],
                    'discount_percentage' => $item['discount_percentage'] ?? 0,
                    'tax_rate' => $product->tax_rate,
                ]);

                $orderDetail->calculateTotals();
                $orderDetail->save();
            }

            // Calculer les totaux de la commande
            $order->calculateTotals();

            // Envoyer automatiquement si demandé
            if ($validated['send_immediately'] ?? false) {
                $order->send();
            }

            DB::commit();

            $this->logActivity('create', $order, [], $order->toArray(), "Création de la commande {$order->reference}");

            if ($request->expectsJson()) {
                return $this->successResponse($order->load('purchaseOrderDetails.product'), 'Commande créée avec succès');
            }

            return redirect()->route('purchase-orders.show', $order)
                           ->with('success', 'Commande créée avec succès');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleDatabaseError($e, 'création de la commande');
        }
    }

    /**
     * Afficher les détails d'une commande
     */
    public function show(PurchaseOrder $purchaseOrder)
    {
        if (!$this->checkRoles(['administrateur', 'responsable_achats', 'magasinier'])) {
            return $this->unauthorizedResponse();
        }

        $purchaseOrder->load([
            'supplier',
            'warehouse',
            'createdBy',
            'purchaseOrderDetails.product',
            'deliveryNotes'
        ]);

        $this->logActivity('view', $purchaseOrder, [], [], "Consultation de la commande {$purchaseOrder->reference}");

        return view('purchase-orders.show', compact('purchaseOrder'));
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(PurchaseOrder $purchaseOrder)
    {
        if (!$this->checkRoles(['administrateur', 'responsable_achats'])) {
            return $this->unauthorizedResponse();
        }

        if (!$purchaseOrder->canBeModified()) {
            return $this->backWithError('Cette commande ne peut plus être modifiée');
        }

        $suppliers = Supplier::active()->get();
        $warehouses = Warehouse::active()->get();
        
        $purchaseOrder->load('purchaseOrderDetails.product');

        return view('purchase-orders.edit', compact('purchaseOrder', 'suppliers', 'warehouses'));
    }

    /**
     * Mettre à jour une commande
     */
    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        if (!$this->checkRoles(['administrateur', 'responsable_achats'])) {
            return $this->unauthorizedResponse();
        }

        if (!$purchaseOrder->canBeModified()) {
            return $this->backWithError('Cette commande ne peut plus être modifiée');
        }

        $validated = $this->validatePurchaseOrder($request);
        $oldValues = $purchaseOrder->toArray();

        try {
            DB::beginTransaction();

            // Mettre à jour la commande
            $purchaseOrder->update([
                'supplier_id' => $validated['supplier_id'],
                'warehouse_id' => $validated['warehouse_id'],
                'order_date' => $validated['order_date'],
                'expected_delivery_date' => $validated['expected_delivery_date'],
                'notes' => $validated['notes'],
            ]);

            // Supprimer les anciens détails
            $purchaseOrder->purchaseOrderDetails()->delete();

            // Ajouter les nouveaux détails
            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);

                $orderDetail = PurchaseOrderDetail::create([
                    'purchase_order_id' => $purchaseOrder->id,
                    'product_id' => $item['product_id'],
                    'quantity_ordered' => $item['quantity_ordered'],
                    'unit_price' => $item['unit_price'],
                    'discount_percentage' => $item['discount_percentage'] ?? 0,
                    'tax_rate' => $product->tax_rate,
                ]);

                $orderDetail->calculateTotals();
                $orderDetail->save();
            }

            // Recalculer les totaux
            $purchaseOrder->calculateTotals();

            DB::commit();

            $this->logActivity('update', $purchaseOrder, $oldValues, $purchaseOrder->toArray(), "Modification de la commande {$purchaseOrder->reference}");

            if ($request->expectsJson()) {
                return $this->successResponse($purchaseOrder, 'Commande modifiée avec succès');
            }

            return $this->redirectWithSuccess('purchase-orders.index', 'Commande modifiée avec succès');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleDatabaseError($e, 'modification de la commande');
        }
    }

    /**
     * Envoyer une commande au fournisseur
     */
    public function send(PurchaseOrder $purchaseOrder)
    {
        if (!$this->checkRoles(['administrateur', 'responsable_achats'])) {
            return $this->unauthorizedResponse();
        }

        if (!$purchaseOrder->canBeSent()) {
            return $this->errorResponse('Cette commande ne peut pas être envoyée');
        }

        try {
            DB::beginTransaction();

            $purchaseOrder->send();

            DB::commit();

            $this->logActivity('send', $purchaseOrder, [], [], "Envoi de la commande {$purchaseOrder->reference}");

            if (request()->expectsJson()) {
                return $this->successResponse($purchaseOrder, 'Commande envoyée avec succès');
            }

            return $this->backWithSuccess('Commande envoyée avec succès');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleDatabaseError($e, 'envoi de la commande');
        }
    }

    /**
     * Confirmer une commande (réception de confirmation fournisseur)
     */
    public function confirm(PurchaseOrder $purchaseOrder)
    {
        if (!$this->checkRoles(['administrateur', 'responsable_achats'])) {
            return $this->unauthorizedResponse();
        }

        if ($purchaseOrder->status !== 'envoye') {
            return $this->errorResponse('Cette commande ne peut pas être confirmée');
        }

        try {
            DB::beginTransaction();

            $purchaseOrder->confirm();

            DB::commit();

            $this->logActivity('update', $purchaseOrder, [], [], "Confirmation de la commande {$purchaseOrder->reference}");

            if (request()->expectsJson()) {
                return $this->successResponse($purchaseOrder, 'Commande confirmée');
            }

            return $this->backWithSuccess('Commande confirmée');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleDatabaseError($e, 'confirmation de la commande');
        }
    }

    /**
     * Annuler une commande
     */
    public function cancel(PurchaseOrder $purchaseOrder)
    {
        if (!$this->checkRoles(['administrateur', 'responsable_achats'])) {
            return $this->unauthorizedResponse();
        }

        if (!$purchaseOrder->canBeCancelled()) {
            return $this->errorResponse('Cette commande ne peut pas être annulée');
        }

        try {
            DB::beginTransaction();

            $oldStatus = $purchaseOrder->status;
            $purchaseOrder->cancel();

            DB::commit();

            $this->logActivity('update', $purchaseOrder, ['status' => $oldStatus], ['status' => 'annule'], 
                "Annulation de la commande {$purchaseOrder->reference}");

            if (request()->expectsJson()) {
                return $this->successResponse($purchaseOrder, 'Commande annulée');
            }

            return $this->backWithSuccess('Commande annulée');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleDatabaseError($e, 'annulation de la commande');
        }
    }

    /**
     * Clôturer une commande
     */
    public function close(PurchaseOrder $purchaseOrder)
    {
        if (!$this->checkRoles(['administrateur', 'responsable_achats'])) {
            return $this->unauthorizedResponse();
        }

        if (!$purchaseOrder->canBeClosed()) {
            return $this->errorResponse('Cette commande ne peut pas être clôturée');
        }

        try {
            DB::beginTransaction();

            $purchaseOrder->close();

            DB::commit();

            $this->logActivity('update', $purchaseOrder, [], [], "Clôture de la commande {$purchaseOrder->reference}");

            if (request()->expectsJson()) {
                return $this->successResponse($purchaseOrder, 'Commande clôturée');
            }

            return $this->backWithSuccess('Commande clôturée');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleDatabaseError($e, 'clôture de la commande');
        }
    }

    /**
     * Dupliquer une commande
     */
    public function duplicate(PurchaseOrder $purchaseOrder)
    {
        if (!$this->checkRoles(['administrateur', 'responsable_achats'])) {
            return $this->unauthorizedResponse();
        }

        try {
            DB::beginTransaction();

            $newOrder = $purchaseOrder->duplicate();

            DB::commit();

            $this->logActivity('create', $newOrder, [], [], 
                "Duplication de la commande {$purchaseOrder->reference} vers {$newOrder->reference}");

            if (request()->expectsJson()) {
                return $this->successResponse($newOrder, 'Commande dupliquée avec succès');
            }

            return redirect()->route('purchase-orders.show', $newOrder)
                           ->with('success', 'Commande dupliquée avec succès');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleDatabaseError($e, 'duplication de la commande');
        }
    }

    /**
     * Générer PDF de la commande
     */
    public function generatePdf(PurchaseOrder $purchaseOrder)
    {
        if (!$this->checkRoles(['administrateur', 'responsable_achats', 'magasinier'])) {
            return $this->unauthorizedResponse();
        }

        $purchaseOrder->load(['supplier', 'warehouse', 'purchaseOrderDetails.product']);

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('purchase-orders.pdf', compact('purchaseOrder'));
        
        $fileName = "commande_{$purchaseOrder->reference}.pdf";
        
        $this->logActivity('export', $purchaseOrder, [], [], 
            "Export PDF de la commande {$purchaseOrder->reference}");

        return $pdf->download($fileName);
    }

    /**
     * Supprimer une commande
     */
    public function destroy(PurchaseOrder $purchaseOrder)
    {
        if (!$this->checkRoles(['administrateur'])) {
            return $this->unauthorizedResponse();
        }

        if (!$purchaseOrder->canBeDeleted()) {
            return $this->errorResponse('Cette commande ne peut pas être supprimée');
        }

        try {
            DB::beginTransaction();

            $reference = $purchaseOrder->reference;
            
            // Supprimer les détails
            $purchaseOrder->purchaseOrderDetails()->delete();
            
            // Supprimer la commande
            $purchaseOrder->delete();

            DB::commit();

            $this->logActivity('delete', $purchaseOrder, $purchaseOrder->toArray(), [], 
                "Suppression de la commande {$reference}");

            if (request()->expectsJson()) {
                return $this->successResponse(null, 'Commande supprimée avec succès');
            }

            return $this->redirectWithSuccess('purchase-orders.index', 'Commande supprimée avec succès');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleDatabaseError($e, 'suppression de la commande');
        }
    }

    /**
     * Méthodes utilitaires privées
     */
    private function validatePurchaseOrder(Request $request)
    {
        return $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'order_date' => 'nullable|date',
            'expected_delivery_date' => 'nullable|date|after:order_date',
            'notes' => 'nullable|string|max:1000',
            'send_immediately' => 'boolean',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity_ordered' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount_percentage' => 'nullable|numeric|min:0|max:100',
        ]);
    }

    private function generatePurchaseOrderReference()
    {
        $prefix = 'CMD';
        $year = date('Y');
        $month = date('m');
        
        $lastOrder = PurchaseOrder::whereYear('created_at', $year)
                                 ->whereMonth('created_at', $month)
                                 ->orderBy('id', 'desc')
                                 ->first();
        
        $number = $lastOrder ? (int)substr($lastOrder->reference, -4) + 1 : 1;
        
        return $prefix . $year . $month . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    private function applyPurchaseOrderFilters($query, Request $request)
    {
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                  ->orWhereHas('supplier', function ($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($supplierId = $request->get('supplier_id')) {
            $query->where('supplier_id', $supplierId);
        }

        if ($warehouseId = $request->get('warehouse_id')) {
            $query->where('warehouse_id', $warehouseId);
        }

        if ($dateFrom = $request->get('date_from')) {
            $query->whereDate('order_date', '>=', $dateFrom);
        }

        if ($dateTo = $request->get('date_to')) {
            $query->whereDate('order_date', '<=', $dateTo);
        }
    }

    private function getPurchaseOrderStats()
    {
        $today = now()->toDateString();
        $thisMonth = now()->format('Y-m');
        
        return [
            'total_orders' => PurchaseOrder::count(),
            'pending_orders' => PurchaseOrder::whereIn('status', ['brouillon', 'envoye', 'confirme'])->count(),
            'orders_today' => PurchaseOrder::whereDate('order_date', $today)->count(),
            'orders_this_month' => PurchaseOrder::whereRaw('DATE_FORMAT(order_date, "%Y-%m") = ?', [$thisMonth])->count(),
            'total_amount_this_month' => PurchaseOrder::whereRaw('DATE_FORMAT(order_date, "%Y-%m") = ?', [$thisMonth])
                                                    ->sum('total_amount'),
        ];
    }
}