<?php

namespace App\Http\Controllers;

use App\Models\ReturnNote;
use App\Models\ReturnNoteDetail;
use App\Models\Sale;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use App\Models\StockMovement;
use App\Models\CreditNote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReturnNoteController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->model = ReturnNote::class;
        $this->modelName = 'Bon de retour';
        $this->viewPath = 'return-notes';
    }

    /**
     * Afficher la liste des bons de retour
     */
    public function index(Request $request)
    {
        if (!$this->checkRoles(['administrateur', 'responsable_commercial', 'vendeur', 'magasinier'])) {
            return $this->unauthorizedResponse();
        }

        $query = ReturnNote::with(['customer', 'supplier', 'sale', 'warehouse', 'createdBy']);

        // Appliquer les filtres
        $this->applyReturnNoteFilters($query, $request);

        // Tri et pagination
        $pagination = $this->getPaginationData($request);
        
        $returnNotes = $query->orderBy($pagination['sortBy'], $pagination['sortDirection'])
                            ->paginate($pagination['perPage']);

        // Statistiques
        $stats = $this->getReturnNoteStats();

        $customers = Customer::active()->get();
        $suppliers = Supplier::active()->get();
        $warehouses = Warehouse::active()->get();

        return view('return-notes.index', compact('returnNotes', 'stats', 'customers', 'suppliers', 'warehouses'));
    }

    /**
     * Afficher le formulaire de création de retour client
     */
    public function createCustomerReturn()
    {
        if (!$this->checkRoles(['administrateur', 'responsable_commercial', 'vendeur'])) {
            return $this->unauthorizedResponse();
        }

        $customers = Customer::active()->get();
        $warehouses = Warehouse::active()->get();
        $sales = Sale::where('status', 'finalisee')->with('customer')->orderBy('created_at', 'desc')->limit(50)->get();

        return view('return-notes.create-customer-return', compact('customers', 'warehouses', 'sales'));
    }

    /**
     * Afficher le formulaire de création de retour fournisseur
     */
    public function createSupplierReturn()
    {
        if (!$this->checkRoles(['administrateur', 'responsable_achats', 'magasinier'])) {
            return $this->unauthorizedResponse();
        }

        $suppliers = Supplier::active()->get();
        $warehouses = Warehouse::active()->get();

        return view('return-notes.create-supplier-return', compact('suppliers', 'warehouses'));
    }

    /**
     * Enregistrer un nouveau bon de retour client
     */
    public function storeCustomerReturn(Request $request)
    {
        if (!$this->checkRoles(['administrateur', 'responsable_commercial', 'vendeur'])) {
            return $this->unauthorizedResponse();
        }

        $validated = $this->validateCustomerReturn($request);

        try {
            DB::beginTransaction();

            // Créer le bon de retour
            $returnNote = ReturnNote::create([
                'reference' => $this->generateReturnNoteReference('client'),
                'type' => 'client',
                'customer_id' => $validated['customer_id'],
                'sale_id' => $validated['sale_id'] ?? null,
                'warehouse_id' => $validated['warehouse_id'],
                'return_date' => $validated['return_date'],
                'reason' => $validated['reason'],
                'status' => 'en_attente',
                'created_by' => auth()->id(),
                'notes' => $validated['notes'],
            ]);

            // Ajouter les détails de retour
            foreach ($validated['items'] as $item) {
                ReturnNoteDetail::create([
                    'return_note_id' => $returnNote->id,
                    'product_id' => $item['product_id'],
                    'quantity_returned' => $item['quantity_returned'],
                    'unit_price' => $item['unit_price'],
                    'total_amount' => $item['quantity_returned'] * $item['unit_price'],
                    'reason' => $item['reason'] ?? $validated['reason'],
                    'condition' => $item['condition'] ?? 'bon_etat',
                ]);
            }

            // Calculer les totaux
            $returnNote->calculateTotals();

            DB::commit();

            $this->logActivity('create', $returnNote, [], $returnNote->toArray(), 
                "Création du bon de retour client {$returnNote->reference}");

            if ($request->expectsJson()) {
                return $this->successResponse($returnNote->load('returnNoteDetails.product'), 
                    'Bon de retour client créé avec succès');
            }

            return redirect()->route('return-notes.show', $returnNote)
                           ->with('success', 'Bon de retour client créé avec succès');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleDatabaseError($e, 'création du bon de retour client');
        }
    }

    /**
     * Enregistrer un nouveau bon de retour fournisseur
     */
    public function storeSupplierReturn(Request $request)
    {
        if (!$this->checkRoles(['administrateur', 'responsable_achats', 'magasinier'])) {
            return $this->unauthorizedResponse();
        }

        $validated = $this->validateSupplierReturn($request);

        try {
            DB::beginTransaction();

            // Créer le bon de retour
            $returnNote = ReturnNote::create([
                'reference' => $this->generateReturnNoteReference('fournisseur'),
                'type' => 'fournisseur',
                'supplier_id' => $validated['supplier_id'],
                'warehouse_id' => $validated['warehouse_id'],
                'return_date' => $validated['return_date'],
                'reason' => $validated['reason'],
                'status' => 'en_attente',
                'created_by' => auth()->id(),
                'notes' => $validated['notes'],
            ]);

            // Ajouter les détails
            foreach ($validated['items'] as $item) {
                ReturnNoteDetail::create([
                    'return_note_id' => $returnNote->id,
                    'product_id' => $item['product_id'],
                    'quantity_returned' => $item['quantity_returned'],
                    'unit_price' => $item['unit_price'],
                    'total_amount' => $item['quantity_returned'] * $item['unit_price'],
                    'reason' => $item['reason'] ?? $validated['reason'],
                    'condition' => $item['condition'] ?? 'defectueux',
                ]);
            }

            $returnNote->calculateTotals();

            DB::commit();

            $this->logActivity('create', $returnNote, [], $returnNote->toArray(), 
                "Création du bon de retour fournisseur {$returnNote->reference}");

            if ($request->expectsJson()) {
                return $this->successResponse($returnNote, 'Bon de retour fournisseur créé avec succès');
            }

            return redirect()->route('return-notes.show', $returnNote)
                           ->with('success', 'Bon de retour fournisseur créé avec succès');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleDatabaseError($e, 'création du bon de retour fournisseur');
        }
    }

    /**
     * Afficher les détails d'un bon de retour
     */
    public function show(ReturnNote $returnNote)
    {
        if (!$this->checkRoles(['administrateur', 'responsable_commercial', 'vendeur', 'responsable_achats', 'magasinier'])) {
            return $this->unauthorizedResponse();
        }

        $returnNote->load([
            'customer',
            'supplier', 
            'sale',
            'warehouse',
            'createdBy',
            'returnNoteDetails.product',
            'stockMovements',
            'creditNotes'
        ]);

        $this->logActivity('view', $returnNote, [], [], 
            "Consultation du bon de retour {$returnNote->reference}");

        return view('return-notes.show', compact('returnNote'));
    }

    /**
     * Valider un bon de retour (mise en stock ou sortie de stock)
     */
    public function validate(Request $request, ReturnNote $returnNote)
    {
        if (!$this->checkRoles(['administrateur', 'magasinier'])) {
            return $this->unauthorizedResponse();
        }

        if ($returnNote->status !== 'en_attente') {
            return $this->errorResponse('Ce bon de retour ne peut pas être validé');
        }

        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.detail_id' => 'required|exists:return_note_details,id',
            'items.*.action' => 'required|in:remettre_stock,detruire,retour_fournisseur',
            'items.*.quantity_accepted' => 'required|integer|min:0',
            'validation_notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            foreach ($validated['items'] as $item) {
                $detail = ReturnNoteDetail::findOrFail($item['detail_id']);
                
                if ($item['quantity_accepted'] > 0) {
                    // Vérifier que la quantité acceptée ne dépasse pas la quantité retournée
                    if ($item['quantity_accepted'] > $detail->quantity_returned) {
                        throw new \Exception("Quantité acceptée supérieure à la quantité retournée pour le produit {$detail->product->name}");
                    }

                    // Mettre à jour le détail
                    $detail->update([
                        'quantity_accepted' => $item['quantity_accepted'],
                        'action_taken' => $item['action'],
                    ]);

                    // Créer le mouvement de stock selon l'action
                    $this->createStockMovementForReturn($returnNote, $detail, $item);
                }
            }

            // Mettre à jour le statut
            $returnNote->update([
                'status' => 'valide',
                'validated_at' => now(),
                'validated_by' => auth()->id(),
                'validation_notes' => $validated['validation_notes'],
            ]);

            // Créer un avoir si nécessaire (pour les retours clients)
            if ($returnNote->type === 'client' && $returnNote->shouldCreateCreditNote()) {
                $this->createCreditNoteForReturn($returnNote);
            }

            DB::commit();

            $this->logActivity('validate', $returnNote, [], [], 
                "Validation du bon de retour {$returnNote->reference}");

            if ($request->expectsJson()) {
                return $this->successResponse($returnNote, 'Bon de retour validé avec succès');
            }

            return $this->backWithSuccess('Bon de retour validé avec succès');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleDatabaseError($e, 'validation du bon de retour');
        }
    }

    /**
     * Rejeter un bon de retour
     */
    public function reject(Request $request, ReturnNote $returnNote)
    {
        if (!$this->checkRoles(['administrateur', 'responsable_commercial'])) {
            return $this->unauthorizedResponse();
        }

        if ($returnNote->status !== 'en_attente') {
            return $this->errorResponse('Ce bon de retour ne peut pas être rejeté');
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $returnNote->update([
                'status' => 'rejete',
                'rejection_reason' => $validated['rejection_reason'],
                'rejected_at' => now(),
                'rejected_by' => auth()->id(),
            ]);

            DB::commit();

            $this->logActivity('reject', $returnNote, [], [], 
                "Rejet du bon de retour {$returnNote->reference} - {$validated['rejection_reason']}");

            if ($request->expectsJson()) {
                return $this->successResponse($returnNote, 'Bon de retour rejeté');
            }

            return $this->backWithSuccess('Bon de retour rejeté');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleDatabaseError($e, 'rejet du bon de retour');
        }
    }

    /**
     * Supprimer un bon de retour
     */
    public function destroy(ReturnNote $returnNote)
    {
        if (!$this->checkRoles(['administrateur'])) {
            return $this->unauthorizedResponse();
        }

        if ($returnNote->status === 'valide') {
            return $this->errorResponse('Un bon de retour validé ne peut pas être supprimé');
        }

        try {
            DB::beginTransaction();

            $reference = $returnNote->reference;
            
            // Supprimer les détails
            $returnNote->returnNoteDetails()->delete();
            
            // Supprimer le bon de retour
            $returnNote->delete();

            DB::commit();

            $this->logActivity('delete', $returnNote, $returnNote->toArray(), [], 
                "Suppression du bon de retour {$reference}");

            if (request()->expectsJson()) {
                return $this->successResponse(null, 'Bon de retour supprimé avec succès');
            }

            return $this->redirectWithSuccess('return-notes.index', 'Bon de retour supprimé avec succès');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleDatabaseError($e, 'suppression du bon de retour');
        }
    }

    /**
     * Méthodes utilitaires privées
     */
    private function validateCustomerReturn(Request $request)
    {
        return $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'sale_id' => 'nullable|exists:sales,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'return_date' => 'required|date',
            'reason' => 'required|in:defaut_produit,erreur_commande,changement_avis,expiration,autre',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity_returned' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.reason' => 'nullable|string|max:255',
            'items.*.condition' => 'nullable|in:bon_etat,endommage,expire',
        ]);
    }

    private function validateSupplierReturn(Request $request)
    {
        return $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'return_date' => 'required|date',
            'reason' => 'required|in:defaut_produit,non_conforme,expire,erreur_livraison,autre',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity_returned' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.reason' => 'nullable|string|max:255',
            'items.*.condition' => 'nullable|in:defectueux,expire,non_conforme',
        ]);
    }

    private function generateReturnNoteReference($type)
    {
        $prefix = $type === 'client' ? 'RC' : 'RF';
        $year = date('Y');
        $month = date('m');
        
        $lastReturnNote = ReturnNote::where('type', $type)
                                  ->whereYear('created_at', $year)
                                  ->whereMonth('created_at', $month)
                                  ->orderBy('id', 'desc')
                                  ->first();
        
        $number = $lastReturnNote ? (int)substr($lastReturnNote->reference, -4) + 1 : 1;
        
        return $prefix . $year . $month . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    private function createStockMovementForReturn(ReturnNote $returnNote, ReturnNoteDetail $detail, array $item)
    {
        $movementType = null;
        $reason = null;

        switch ($item['action']) {
            case 'remettre_stock':
                $movementType = 'entree';
                $reason = 'retour_client_stock';
                break;
            case 'detruire':
                // Pas de mouvement de stock pour destruction
                return;
            case 'retour_fournisseur':
                $movementType = 'sortie';
                $reason = 'retour_fournisseur';
                break;
        }

        if ($movementType) {
            // Obtenir le stock actuel
            $warehouseStock = WarehouseStock::firstOrCreate([
                'warehouse_id' => $returnNote->warehouse_id,
                'product_id' => $detail->product_id,
            ]);

            $stockBefore = $warehouseStock->quantity;
            $stockAfter = $movementType === 'entree' 
                ? $stockBefore + $item['quantity_accepted']
                : $stockBefore - $item['quantity_accepted'];

            // Créer le mouvement
            StockMovement::create([
                'reference' => StockMovement::generateReference('retour'),
                'product_id' => $detail->product_id,
                'warehouse_id' => $returnNote->warehouse_id,
                'type' => $movementType,
                'reason' => $reason,
                'quantity' => $item['quantity_accepted'],
                'stock_before' => $stockBefore,
                'stock_after' => $stockAfter,
                'unit_cost' => $detail->unit_price,
                'total_cost' => $item['quantity_accepted'] * $detail->unit_price,
                'movement_date' => now()->toDateString(),
                'movement_time' => now()->format('H:i:s'),
                'created_by' => auth()->id(),
                'notes' => "Retour {$returnNote->reference} - Action: {$item['action']}",
                'return_note_id' => $returnNote->id,
            ]);

            // Mettre à jour le stock
            if ($movementType === 'entree') {
                $warehouseStock->updateQuantity($item['quantity_accepted'], 'add');
            } else {
                $warehouseStock->updateQuantity($item['quantity_accepted'], 'subtract');
            }
        }
    }

    private function createCreditNoteForReturn(ReturnNote $returnNote)
    {
        if ($returnNote->type !== 'client' || !$returnNote->customer_id) {
            return;
        }

        $totalAmount = $returnNote->returnNoteDetails()
                                 ->where('quantity_accepted', '>', 0)
                                 ->sum(DB::raw('quantity_accepted * unit_price'));

        if ($totalAmount > 0) {
            CreditNote::create([
                'reference' => CreditNote::generateReference(),
                'customer_id' => $returnNote->customer_id,
                'return_note_id' => $returnNote->id,
                'sale_id' => $returnNote->sale_id,
                'amount' => $totalAmount,
                'reason' => 'retour_marchandise',
                'status' => 'valide',
                'issue_date' => now()->toDateString(),
                'created_by' => auth()->id(),
                'notes' => "Avoir automatique pour retour {$returnNote->reference}",
            ]);
        }
    }

    private function applyReturnNoteFilters($query, Request $request)
    {
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($cq) use ($search) {
                      $cq->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('supplier', function ($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($type = $request->get('type')) {
            $query->where('type', $type);
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($reason = $request->get('reason')) {
            $query->where('reason', $reason);
        }

        if ($customerId = $request->get('customer_id')) {
            $query->where('customer_id', $customerId);
        }

        if ($supplierId = $request->get('supplier_id')) {
            $query->where('supplier_id', $supplierId);
        }

        if ($warehouseId = $request->get('warehouse_id')) {
            $query->where('warehouse_id', $warehouseId);
        }

        if ($dateFrom = $request->get('date_from')) {
            $query->whereDate('return_date', '>=', $dateFrom);
        }

        if ($dateTo = $request->get('date_to')) {
            $query->whereDate('return_date', '<=', $dateTo);
        }
    }

    private function getReturnNoteStats()
    {
        $today = now()->toDateString();
        $thisMonth = now()->format('Y-m');
        
        return [
            'total_returns' => ReturnNote::count(),
            'customer_returns' => ReturnNote::where('type', 'client')->count(),
            'supplier_returns' => ReturnNote::where('type', 'fournisseur')->count(),
            'pending_returns' => ReturnNote::where('status', 'en_attente')->count(),
            'validated_returns' => ReturnNote::where('status', 'valide')->count(),
            'returns_today' => ReturnNote::whereDate('return_date', $today)->count(),
            'returns_this_month' => ReturnNote::whereRaw('DATE_FORMAT(return_date, "%Y-%m") = ?', [$thisMonth])->count(),
            'total_amount_this_month' => ReturnNote::whereRaw('DATE_FORMAT(return_date, "%Y-%m") = ?', [$thisMonth])
                                                  ->sum('total_amount'),
        ];
    }
}