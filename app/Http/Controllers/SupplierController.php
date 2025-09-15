<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\DeliveryNote;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SupplierController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->model = Supplier::class;
        $this->modelName = 'Fournisseur';
        $this->viewPath = 'suppliers';
    }

    /**
     * Afficher la liste des fournisseurs
     */
    public function index(Request $request)
    {
        if (!$this->checkRoles(['administrateur', 'responsable_achats', 'magasinier'])) {
            return $this->unauthorizedResponse();
        }

        $query = Supplier::query();

        // Appliquer les filtres
        $this->applySupplierFilters($query, $request);

        // Tri et pagination
        $pagination = $this->getPaginationData($request);
        
        $suppliers = $query->orderBy($pagination['sortBy'], $pagination['sortDirection'])
                          ->paginate($pagination['perPage']);

        // Statistiques
        $stats = $this->getSupplierStats();

        return view('suppliers.index', compact('suppliers', 'stats'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        if (!$this->checkRoles(['administrateur', 'responsable_achats'])) {
            return $this->unauthorizedResponse();
        }

        return view('suppliers.create');
    }

    /**
     * Enregistrer un nouveau fournisseur
     */
    public function store(Request $request)
    {
        if (!$this->checkRoles(['administrateur', 'responsable_achats'])) {
            return $this->unauthorizedResponse();
        }

        $validated = $this->validateSupplier($request);

        try {
            DB::beginTransaction();

            $supplier = Supplier::create($validated);

            DB::commit();

            $this->logActivity('create', $supplier, [], $supplier->toArray(), "Création du fournisseur {$supplier->name}");

            if ($request->expectsJson()) {
                return $this->successResponse($supplier, 'Fournisseur créé avec succès');
            }

            return $this->redirectWithSuccess('suppliers.index', 'Fournisseur créé avec succès');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleDatabaseError($e, 'création du fournisseur');
        }
    }

    /**
     * Afficher les détails d'un fournisseur
     */
    public function show(Supplier $supplier)
    {
        if (!$this->checkRoles(['administrateur', 'responsable_achats', 'magasinier'])) {
            return $this->unauthorizedResponse();
        }

        // Charger les relations
        $supplier->load(['products', 'purchaseOrders', 'deliveryNotes', 'payments']);

        // Statistiques du fournisseur
        $stats = $this->getSupplierDetailStats($supplier);

        // Commandes récentes
        $recentOrders = $supplier->purchaseOrders()
                                ->with(['warehouse'])
                                ->orderBy('order_date', 'desc')
                                ->limit(10)
                                ->get();

        // Livraisons récentes
        $recentDeliveries = $supplier->deliveryNotes()
                                   ->with(['warehouse'])
                                   ->orderBy('delivery_date', 'desc')
                                   ->limit(10)
                                   ->get();

        // Paiements récents
        $recentPayments = $supplier->payments()
                                 ->with('createdBy')
                                 ->orderBy('payment_date', 'desc')
                                 ->limit(10)
                                 ->get();

        $this->logActivity('view', $supplier, [], [], "Consultation du fournisseur {$supplier->name}");

        return view('suppliers.show', compact('supplier', 'stats', 'recentOrders', 'recentDeliveries', 'recentPayments'));
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(Supplier $supplier)
    {
        if (!$this->checkRoles(['administrateur', 'responsable_achats'])) {
            return $this->unauthorizedResponse();
        }

        return view('suppliers.edit', compact('supplier'));
    }

    /**
     * Mettre à jour un fournisseur
     */
    public function update(Request $request, Supplier $supplier)
    {
        if (!$this->checkRoles(['administrateur', 'responsable_achats'])) {
            return $this->unauthorizedResponse();
        }

        $validated = $this->validateSupplier($request, $supplier->id);
        $oldValues = $supplier->toArray();

        try {
            DB::beginTransaction();

            $supplier->update($validated);

            DB::commit();

            $this->logActivity('update', $supplier, $oldValues, $supplier->toArray(), "Modification du fournisseur {$supplier->name}");

            if ($request->expectsJson()) {
                return $this->successResponse($supplier, 'Fournisseur modifié avec succès');
            }

            return $this->redirectWithSuccess('suppliers.index', 'Fournisseur modifié avec succès');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleDatabaseError($e, 'modification du fournisseur');
        }
    }

    /**
     * Supprimer un fournisseur
     */
    public function destroy(Supplier $supplier)
    {
        if (!$this->checkRoles(['administrateur'])) {
            return $this->unauthorizedResponse();
        }

        try {
            // Vérifier si le fournisseur peut être supprimé
            if ($supplier->products()->exists() || $supplier->purchaseOrders()->exists()) {
                return $this->errorResponse('Impossible de supprimer ce fournisseur car il a des produits ou commandes associés');
            }

            $supplierName = $supplier->name;
            $supplier->delete();

            $this->logActivity('delete', $supplier, $supplier->toArray(), [], "Suppression du fournisseur {$supplierName}");

            if (request()->expectsJson()) {
                return $this->successResponse(null, 'Fournisseur supprimé avec succès');
            }

            return $this->redirectWithSuccess('suppliers.index', 'Fournisseur supprimé avec succès');

        } catch (\Exception $e) {
            return $this->handleDatabaseError($e, 'suppression du fournisseur');
        }
    }

    /**
     * Recherche de fournisseurs (pour AJAX)
     */
    public function search(Request $request)
    {
        $search = $request->get('q', '');
        $limit = $request->get('limit', 10);

        $suppliers = Supplier::where(function($query) use ($search) {
                               $query->where('name', 'like', "%{$search}%")
                                     ->orWhere('code', 'like', "%{$search}%")
                                     ->orWhere('contact_person', 'like', "%{$search}%");
                           })
                           ->active()
                           ->limit($limit)
                           ->get();

        return $this->successResponse($suppliers->map(function($supplier) {
            return [
                'id' => $supplier->id,
                'code' => $supplier->code,
                'name' => $supplier->name,
                'contact_person' => $supplier->contact_person,
                'phone' => $supplier->phone,
                'email' => $supplier->email,
                'payment_terms_days' => $supplier->payment_terms_days,
                'products_count' => $supplier->products_count,
            ];
        }));
    }

    /**
     * Liste des produits d'un fournisseur
     */
    public function products(Supplier $supplier, Request $request)
    {
        if (!$this->checkRoles(['administrateur', 'responsable_achats', 'magasinier'])) {
            return $this->unauthorizedResponse();
        }

        $query = $supplier->products()->with(['family']);

        // Recherche
        if ($search = $request->get('search')) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        // Filtres
        if ($familyId = $request->get('family_id')) {
            $query->where('family_id', $familyId);
        }

        if ($filter = $request->get('filter')) {
            switch ($filter) {
                case 'active':
                    $query->active();
                    break;
                case 'low_stock':
                    $query->lowStock();
                    break;
                case 'out_of_stock':
                    $query->outOfStock();
                    break;
            }
        }

        $products = $query->paginate(20);

        return view('suppliers.products', compact('supplier', 'products'));
    }

    /**
     * Historique des commandes d'un fournisseur
     */
    public function orders(Supplier $supplier, Request $request)
    {
        if (!$this->checkRoles(['administrateur', 'responsable_achats'])) {
            return $this->unauthorizedResponse();
        }

        $query = $supplier->purchaseOrders()->with(['warehouse', 'createdBy']);

        // Filtres par dates
        $dateFilters = $this->getDateFilters($request);
        if ($dateFilters['startDate']) {
            $query->where('order_date', '>=', $dateFilters['startDate']);
        }
        if ($dateFilters['endDate']) {
            $query->where('order_date', '<=', $dateFilters['endDate']);
        }

        // Filtres par statut
        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $orders = $query->orderBy('order_date', 'desc')->paginate(20);

        // Statistiques des commandes
        $orderStats = [
            'total_orders' => $supplier->purchaseOrders()->count(),
            'pending_orders' => $supplier->purchaseOrders()->pending()->count(),
            'total_amount' => $supplier->purchaseOrders()->sum('total_ttc'),
            'average_order_value' => $supplier->purchaseOrders()->avg('total_ttc'),
        ];

        return view('suppliers.orders', compact('supplier', 'orders', 'orderStats'));
    }

    /**
     * Relevé de compte fournisseur
     */
    public function statement(Request $request, Supplier $supplier)
    {
        if (!$this->checkRoles(['administrateur', 'responsable_achats', 'comptable'])) {
            return $this->unauthorizedResponse();
        }

        $startDate = $request->get('start_date', Carbon::now()->subMonth()->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->toDateString());

        // Transactions du fournisseur (commandes et paiements)
        $transactions = collect();

        // Ajouter les commandes
        $orders = $supplier->purchaseOrders()
                          ->whereBetween('order_date', [$startDate, $endDate])
                          ->whereIn('status', ['confirme', 'livre'])
                          ->get()
                          ->map(function($order) {
                              return [
                                  'date' => $order->order_date,
                                  'type' => 'commande',
                                  'reference' => $order->reference,
                                  'description' => "Commande {$order->reference}",
                                  'debit' => $order->total_ttc,
                                  'credit' => 0,
                                  'balance' => null,
                              ];
                          });

        // Ajouter les paiements
        $payments = $supplier->payments()
                            ->whereBetween('payment_date', [$startDate, $endDate])
                            ->where('status', 'valide')
                            ->get()
                            ->map(function($payment) {
                                return [
                                    'date' => $payment->payment_date,
                                    'type' => 'paiement',
                                    'reference' => $payment->reference,
                                    'description' => "Paiement {$payment->method_label}",
                                    'debit' => 0,
                                    'credit' => $payment->amount,
                                    'balance' => null,
                                ];
                            });

        // Fusionner et trier par date
        $transactions = $transactions->merge($orders)
                                    ->merge($payments)
                                    ->sortBy('date');

        // Calculer les soldes
        $runningBalance = $supplier->getTotalOutstandingAmount() - $supplier->getTotalPaidAmount();
        foreach ($transactions as &$transaction) {
            $runningBalance += $transaction['debit'] - $transaction['credit'];
            $transaction['balance'] = $runningBalance;
        }

        $this->logActivity('view', $supplier, [], [], "Consultation relevé de compte {$supplier->name}");

        return view('suppliers.statement', compact('supplier', 'transactions', 'startDate', 'endDate'));
    }

    /**
     * Exporter les fournisseurs
     */
    public function export(Request $request)
    {
        if (!$this->checkPermission('export_suppliers')) {
            return $this->unauthorizedResponse();
        }

        $query = Supplier::query();
        $this->applySupplierFilters($query, $request);

        $suppliers = $query->get();

        $headers = [
            'Code', 'Nom', 'Contact', 'Téléphone', 'Email', 'Adresse',
            'Ville', 'Pays', 'Limite crédit', 'Délai paiement (jours)',
            'Nb produits', 'Statut'
        ];

        $data = $suppliers->map(function($supplier) {
            return [
                $supplier->code,
                $supplier->name,
                $supplier->contact_person,
                $supplier->phone,
                $supplier->email,
                $supplier->address,
                $supplier->city,
                $supplier->country,
                $supplier->credit_limit,
                $supplier->payment_terms_days,
                $supplier->products()->count(),
                $supplier->is_active ? 'Actif' : 'Inactif',
            ];
        });

        $this->logActivity('export', null, [], [], 'Export de la liste des fournisseurs');

        return $this->exportToCsv($data, 'fournisseurs', $headers);
    }

    // Méthodes privées utilitaires

    private function applySupplierFilters($query, Request $request)
    {
        // Recherche textuelle
        if ($search = $request->get('search')) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filtres spéciaux
        switch ($request->get('filter')) {
            case 'active':
                $query->active();
                break;
            case 'inactive':
                $query->where('is_active', false);
                break;
            case 'with_orders':
                $query->withOutstandingOrders();
                break;
        }
    }

    private function validateSupplier(Request $request, $supplierId = null)
    {
        return $request->validate([
            'code' => 'required|string|max:50|unique:suppliers,code' . ($supplierId ? ",$supplierId" : ''),
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'country' => 'required|string|max:100',
            'credit_limit' => 'required|numeric|min:0',
            'payment_terms_days' => 'required|integer|min:0|max:365',
            'is_active' => 'boolean',
        ], [
            'code.required' => 'Le code fournisseur est obligatoire',
            'code.unique' => 'Ce code fournisseur existe déjà',
            'name.required' => 'Le nom est obligatoire',
            'country.required' => 'Le pays est obligatoire',
            'credit_limit.required' => 'La limite de crédit est obligatoire',
            'payment_terms_days.required' => 'Le délai de paiement est obligatoire',
        ]);
    }

    private function getSupplierStats(): array
    {
        return [
            'total' => Supplier::count(),
            'active' => Supplier::active()->count(),
            'with_orders' => Supplier::withOutstandingOrders()->count(),
            'total_products' => Product::whereNotNull('supplier_id')->count(),
            'total_outstanding' => PurchaseOrder::pending()->sum('total_ttc'),
        ];
    }

    private function getSupplierDetailStats(Supplier $supplier): array
    {
        $thirtyDaysAgo = Carbon::now()->subDays(30);
        
        return [
            'total_products' => $supplier->products()->count(),
            'active_products' => $supplier->products()->active()->count(),
            'total_orders' => $supplier->purchaseOrders()->count(),
            'pending_orders' => $supplier->purchaseOrders()->pending()->count(),
            'total_deliveries' => $supplier->deliveryNotes()->count(),
            'total_amount_ordered' => $supplier->purchaseOrders()->sum('total_ttc'),
            'total_amount_paid' => $supplier->payments()->where('status', 'valide')->sum('amount'),
            'outstanding_amount' => $supplier->getTotalOutstandingAmount(),
            'orders_last_30_days' => $supplier->purchaseOrders()
                ->where('order_date', '>=', $thirtyDaysAgo)
                ->count(),
            'deliveries_last_30_days' => $supplier->deliveryNotes()
                ->where('delivery_date', '>=', $thirtyDaysAgo)
                ->count(),
            'average_order_value' => $supplier->purchaseOrders()->avg('total_ttc'),
            'last_order_date' => $supplier->purchaseOrders()->max('order_date'),
            'last_delivery_date' => $supplier->deliveryNotes()->max('delivery_date'),
        ];
    }
}