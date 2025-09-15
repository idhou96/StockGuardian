<?php

namespace App\Http\Controllers;

use App\Models\DeliveryNote;
use App\Models\DeliveryNoteDetail;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDetail;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeliveryNoteController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->model = DeliveryNote::class;
        $this->modelName = 'Bon de livraison';
        $this->viewPath = 'delivery-notes';
    }

    /**
     * Afficher la liste des bons de livraison
     */
    public function index(Request $request)
    {
        if (!$this->checkRoles(['administrateur', 'responsable_achats', 'magasinier'])) {
            return $this->unauthorizedResponse();
        }

        $query = DeliveryNote::with(['purchaseOrder.supplier', 'warehouse', 'createdBy']);

        // Appliquer les filtres
        $this->applyDeliveryNoteFilters($query, $request);

        // Tri et pagination
        $pagination = $this->getPaginationData($request);
        
        $deliveryNotes = $query->orderBy($pagination['sortBy'], $pagination['sortDirection'])
                              ->paginate($pagination['perPage']);

        // Statistiques
        $stats = $this->getDeliveryNoteStats();

        $warehouses = Warehouse::active()->get();

        return view('delivery-notes.index', compact('deliveryNotes', 'stats', 'warehouses'));
    }

    /**
     * Afficher le formulaire de création à partir d'une commande
     */
    public function createFromOrder(PurchaseOrder $purchaseOrder)
    {
        if (!$this->checkRoles(['administrateur', 'responsable_achats', 'magasinier'])) {
            return $this->unauthorizedResponse();
        }

        if ($purchaseOrder->status !== 'confirme') {
            return $this->backWithError('Seules les commandes confirmées peuvent générer des bons de livraison');
        }

        $purchaseOrder->load(['supplier', 'warehouse', 'purchaseOrderDetails.product']);

        return view('delivery-notes.create-from-order', compact('purchaseOrder'));
    }

    /**
     * Afficher le formulaire de création libre
     */
    public function create()
    {
        if (!$this->checkRoles(['administrateur', 'magasinier'])) {
            return $this->unauthorizedResponse();
        }

        $warehouses = Warehouse::active()->get();
        $purchaseOrders = PurchaseOrder::where('status', 'confirme')->with('supplier')->get();

        return view('delivery-notes.create', compact('warehouses', 'purchaseOrders'));
    }

    /**
     * Enregistrer un nouveau bon de livraison
     */
    public function store(Request $request)
    {
        if (!$this->checkRoles(['administrateur', 'responsable_achats', 'magasinier'])) {
            return $this->unauthorizedResponse();
        }

        $validated = $this->validateDeliveryNote($request);

        try {
            DB::beginTransaction();

            // Créer le bon de livraison
            $deliveryNote = DeliveryNote::create([
                'reference' => $this->generateDeliveryNoteReference(),
                'purchase_order_id' => $validated['purchase_order_id'] ?? null,
                'warehouse_id' => $validated['warehouse_id'],
                'delivery_date' => $validated['delivery_date'],
                'received_date' => $validated['received_date'] ?? $validated['delivery_date'],
                'delivery_person' => $validated['delivery_person'],
                'status' => 'en_attente',
                'created_by' => auth()->id(),
                'notes' => $validated['notes'],
            ]);

            // Ajouter les détails
            foreach ($validated['items'] as $item) {
                DeliveryNoteDetail::create([
                    'delivery_note_id' => $deliveryNote->id,
                    'product_id' => $item['product_id'],
                    'quantity_ordered' => $item['quantity_ordered'],
                    'quantity_delivered' => $item['quantity_delivered'],
                    'quantity_received' => 0, // Sera mis à jour lors de la réception
                    'unit_price' => $item['unit_price'],
                    'expiry_date' => $item['expiry_date'] ?? null,
                    'batch_number' => $item['batch_number'] ?? null,
                ]);
            }

            // Calculer les totaux
            $deliveryNote->calculateTotals();

            DB::commit();

            $this->logActivity('create', $deliveryNote, [], $deliveryNote->toArray(), 
                "Création du bon de livraison {$deliveryNote->reference}");

            if ($request->expectsJson()) {
                return $this->successResponse($deliveryNote->load('deliveryNoteDetails.product'), 
                    'Bon de livraison créé avec succès');
            }

            return redirect()->route('delivery-notes.show', $deliveryNote)
                           ->with('success', 'Bon de livraison créé avec succès');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleDatabaseError($e, 'création du bon de livraison');
        }
    }

    /**
     * Enregistrer un bon de livraison à partir d'une commande
     */
    public function storeFromOrder(Request $request, PurchaseOrder $purchaseOrder)
    {
        if (!$this->checkRoles(['administrateur', 'responsable_achats', 'magasinier'])) {
            return $this->unauthorizedResponse();
        }

        $validated = $this->validateDeliveryNoteFromOrder($request);

        try {
            DB::beginTransaction();

            // Créer le bon de livraison
            $deliveryNote = DeliveryNote::create([
                'reference' => $this->generateDeliveryNoteReference(),
                'purchase_order_id' => $purchaseOrder->id,
                'warehouse_id' => $purchaseOrder->warehouse_id,
                'delivery_date' => $validated['delivery_date'],
                'received_date' => $validated['received_date'] ?? $validated['delivery_date'],
                'delivery_person' => $validated['delivery_person'],
                'status' => 'en_attente',
                'created_by' => auth()->id(),
                'notes' => $validated['notes'],
            ]);

            // Ajouter les détails basés sur la commande
            foreach ($validated['items'] as $item) {
                $orderDetail = PurchaseOrderDetail::findOrFail($item['order_detail_id']);

                DeliveryNoteDetail::create([
                    'delivery_note_id' => $deliveryNote->id,
                    'product_id' => $orderDetail->product_id,
                    'purchase_order_detail_id' => $orderDetail->id,
                    'quantity_ordered' => $orderDetail->quantity_ordered,
                    'quantity_delivered' => $item['quantity_delivered'],
                    'quantity_received' => 0,
                    'unit_price' => $orderDetail->unit_price,
                    'expiry_date' => $item['expiry_date'] ?? null,
                    'batch_number' => $item['batch_number'] ?? null,
                ]);
            }

            $deliveryNote->calculateTotals();

            DB::commit();

            $this->logActivity('create', $deliveryNote, [], $deliveryNote->toArray(), 
                "Création du bon de livraison {$deliveryNote->reference} depuis la commande {$purchaseOrder->reference}");

            if ($request->expectsJson()) {
                return $this->successResponse($deliveryNote, 'Bon de livraison créé avec succès');
            }

            return redirect()->route('delivery-notes.show', $deliveryNote)
                           ->with('success', 'Bon de livraison créé avec succès');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleDatabaseError($e, 'création du bon de livraison');
        }
    }

    /**
     * Afficher les détails d'un bon de livraison
     */
    public function show(DeliveryNote $deliveryNote)
    {
        if (!$this->checkRoles(['administrateur', 'responsable_achats', 'magasinier'])) {
            return $this->unauthorizedResponse();
        }

        $deliveryNote->load([
            'purchaseOrder.supplier',
            'warehouse',
            'createdBy',
            'deliveryNoteDetails.product',
            'stockMovements'
        ]);

        $this->logActivity('view', $deliveryNote, [], [], 
            "Consultation du bon de livraison {$deliveryNote->reference}");

        return view('delivery-notes.show', compact('deliveryNote'));
    }

    /**
     * Recevoir partiellement ou totalement une livraison
     */
    public function receive(Request $request, DeliveryNote $deliveryNote)
    {
        if (!$this->checkRoles(['administrateur', 'magasinier'])) {
            return $this->unauthorizedResponse();
        }

        if ($deliveryNote->status === 'recu_complet') {
            return $this->errorResponse('Cette livraison a déjà été entièrement reçue');
        }

        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.detail_id' => 'required|exists:delivery_note_details,id',
            'items.*.quantity_received' => 'required|integer|min:0',
            'items.*.expiry_date' => 'nullable|date',
            'items.*.batch_number' => 'nullable|string|max:50',
            'reception_notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $hasPartialReception = false;
            $allItemsReceived = true;

            foreach ($validated['items'] as $item) {
                $detail = DeliveryNoteDetail::findOrFail($item['detail_id']);
                
                if ($item['quantity_received'] > 0) {
                    // Vérifier que la quantité reçue ne dépasse pas la quantité livrée
                    $totalAlreadyReceived = $detail->quantity_received;
                    $maxReceivable = $detail->quantity_delivered - $totalAlreadyReceived;
                    
                    if ($item['quantity_received'] > $maxReceivable) {
                        throw new \Exception("Quantité reçue trop importante pour le produit {$detail->product->name}");
                    }

                    // Mettre à jour le détail
                    $detail->update([
                        'quantity_received' => $totalAlreadyReceived + $item['quantity_received'],
                        'expiry_date' => $item['expiry_date'] ?? $detail->expiry_date,
                        'batch_number' => $item['batch_number'] ?? $detail->batch_number,
                    ]);

                    // Créer le mouvement de stock
                    $movement = StockMovement::create([
                        'reference' => StockMovement::generateReference('reception'),
                        'product_id' => $detail->product_id,
                        'warehouse_id' => $deliveryNote->warehouse_id,
                        'type' => 'entree',
                        'reason' => 'reception_commande',
                        'quantity' => $item['quantity_received'],
                        'stock_before' => 0, // Sera calculé
                        'stock_after' => 0,  // Sera calculé
                        'unit_cost' => $detail->unit_price,
                        'total_cost' => $item['quantity_received'] * $detail->unit_price,
                        'movement_date' => now()->toDateString(),
                        'movement_time' => now()->format('H:i:s'),
                        'created_by' => auth()->id(),
                        'notes' => "Réception BL {$deliveryNote->reference}",
                        'delivery_note_id' => $deliveryNote->id,
                    ]);

                    // Mettre à jour le stock
                    $warehouseStock = WarehouseStock::firstOrCreate([
                        'warehouse_id' => $deliveryNote->warehouse_id,
                        'product_id' => $detail->product_id,
                    ]);

                    $movement->stock_before = $warehouseStock->quantity;
                    $warehouseStock->updateQuantity($item['quantity_received'], 'add');
                    $movement->stock_after = $warehouseStock->quantity;
                    $movement->save();

                    $hasPartialReception = true;
                }

                // Vérifier si cet article est entièrement reçu
                if ($detail->quantity_received < $detail->quantity_delivered) {
                    $allItemsReceived = false;
                }
            }

            // Mettre à jour le statut du bon de livraison
            if ($hasPartialReception) {
                $newStatus = $allItemsReceived ? 'recu_complet' : 'recu_partiel';
                $deliveryNote->update([
                    'status' => $newStatus,
                    'reception_notes' => $validated['reception_notes'],
                ]);

                // Si commande associée, mettre à jour son statut
                if ($deliveryNote->purchaseOrder) {
                    $deliveryNote->purchaseOrder->updateDeliveryStatus();
                }
            }

            DB::commit();

            $this->logActivity('receive', $deliveryNote, [], [], 
                "Réception " . ($allItemsReceived ? 'complète' : 'partielle') . 
                " du bon de livraison {$deliveryNote->reference}");

            if ($request->expectsJson()) {
                return $this->successResponse($deliveryNote, 'Réception enregistrée avec succès');
            }

            return $this->backWithSuccess('Réception enregistrée avec succès');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleDatabaseError($e, 'réception de la livraison');
        }
    }

    /**
     * Annuler un bon de livraison
     */
    public function cancel(DeliveryNote $deliveryNote)
    {
        if (!$this->checkRoles(['administrateur'])) {
            return $this->unauthorizedResponse();
        }

        if ($deliveryNote->status !== 'en_attente') {
            return $this->errorResponse('Seuls les bons de livraison en attente peuvent être annulés');
        }

        try {
            DB::beginTransaction();

            $deliveryNote->update(['status' => 'annule']);

            DB::commit();

            $this->logActivity('cancel', $deliveryNote, [], [], 
                "Annulation du bon de livraison {$deliveryNote->reference}");

            if (request()->expectsJson()) {
                return $this->successResponse($deliveryNote, 'Bon de livraison annulé');
            }

            return $this->backWithSuccess('Bon de livraison annulé');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleDatabaseError($e, 'annulation du bon de livraison');
        }
    }

    /**
     * Méthodes utilitaires privées
     */
    private function validateDeliveryNote(Request $request)
    {
        return $request->validate([
            'purchase_order_id' => 'nullable|exists:purchase_orders,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'delivery_date' => 'required|date',
            'received_date' => 'nullable|date',
            'delivery_person' => 'required|string|max:255',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity_ordered' => 'required|integer|min:1',
            'items.*.quantity_delivered' => 'required|integer|min:0',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.expiry_date' => 'nullable|date',
            'items.*.batch_number' => 'nullable|string|max:50',
        ]);
    }

    private function validateDeliveryNoteFromOrder(Request $request)
    {
        return $request->validate([
            'delivery_date' => 'required|date',
            'received_date' => 'nullable|date',
            'delivery_person' => 'required|string|max:255',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.order_detail_id' => 'required|exists:purchase_order_details,id',
            'items.*.quantity_delivered' => 'required|integer|min:0',
            'items.*.expiry_date' => 'nullable|date',
            'items.*.batch_number' => 'nullable|string|max:50',
        ]);
    }

    private function generateDeliveryNoteReference()
    {
        $prefix = 'BL';
        $year = date('Y');
        $month = date('m');
        
        $lastDeliveryNote = DeliveryNote::whereYear('created_at', $year)
                                      ->whereMonth('created_at', $month)
                                      ->orderBy('id', 'desc')
                                      ->first();
        
        $number = $lastDeliveryNote ? (int)substr($lastDeliveryNote->reference, -4) + 1 : 1;
        
        return $prefix . $year . $month . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    private function applyDeliveryNoteFilters($query, Request $request)
    {
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                  ->orWhere('delivery_person', 'like', "%{$search}%")
                  ->orWhereHas('purchaseOrder', function ($pq) use ($search) {
                      $pq->where('reference', 'like', "%{$search}%");
                  });
            });
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($warehouseId = $request->get('warehouse_id')) {
            $query->where('warehouse_id', $warehouseId);
        }

        if ($dateFrom = $request->get('date_from')) {
            $query->whereDate('delivery_date', '>=', $dateFrom);
        }

        if ($dateTo = $request->get('date_to')) {
            $query->whereDate('delivery_date', '<=', $dateTo);
        }
    }

    private function getDeliveryNoteStats()
    {
        $today = now()->toDateString();
        $thisMonth = now()->format('Y-m');
        
        return [
            'total_delivery_notes' => DeliveryNote::count(),
            'pending_deliveries' => DeliveryNote::where('status', 'en_attente')->count(),
            'partial_receptions' => DeliveryNote::where('status', 'recu_partiel')->count(),
            'complete_receptions' => DeliveryNote::where('status', 'recu_complet')->count(),
            'deliveries_today' => DeliveryNote::whereDate('delivery_date', $today)->count(),
            'deliveries_this_month' => DeliveryNote::whereRaw('DATE_FORMAT(delivery_date, "%Y-%m") = ?', [$thisMonth])->count(),
        ];
    }
}