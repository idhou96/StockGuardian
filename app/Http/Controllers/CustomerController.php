<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Sale;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CustomerController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->model = Customer::class;
        $this->modelName = 'Client';
        $this->viewPath = 'customers';
    }

    /**
     * Afficher la liste des clients
     */
    public function index(Request $request)
    {
        if (!$this->checkRoles(['administrateur', 'responsable_commercial', 'vendeur', 'comptable'])) {
            return $this->unauthorizedResponse();
        }

        $query = Customer::query();

        // Appliquer les filtres
        $this->applyCustomerFilters($query, $request);

        // Tri et pagination
        $pagination = $this->getPaginationData($request);
        
        $customers = $query->orderBy($pagination['sortBy'], $pagination['sortDirection'])
                          ->paginate($pagination['perPage']);

        // Statistiques
        $stats = $this->getCustomerStats();

        return view('customers.index', compact('customers', 'stats'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        if (!$this->checkRoles(['administrateur', 'responsable_commercial', 'vendeur'])) {
            return $this->unauthorizedResponse();
        }

        return view('customers.create');
    }

    /**
     * Enregistrer un nouveau client
     */
    public function store(Request $request)
    {
        if (!$this->checkRoles(['administrateur', 'responsable_commercial', 'vendeur'])) {
            return $this->unauthorizedResponse();
        }

        $validated = $this->validateCustomer($request);

        try {
            DB::beginTransaction();

            $customer = Customer::create($validated);

            DB::commit();

            $this->logActivity('create', $customer, [], $customer->toArray(), "Création du client {$customer->full_name}");

            if ($request->expectsJson()) {
                return $this->successResponse($customer, 'Client créé avec succès');
            }

            return $this->redirectWithSuccess('customers.index', 'Client créé avec succès');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleDatabaseError($e, 'création du client');
        }
    }

    /**
     * Afficher les détails d'un client
     */
    public function show(Customer $customer)
    {
        if (!$this->checkRoles(['administrateur', 'responsable_commercial', 'vendeur', 'comptable'])) {
            return $this->unauthorizedResponse();
        }

        // Charger les relations
        $customer->load(['sales', 'invoices', 'payments']);

        // Statistiques du client
        $stats = $this->getCustomerDetailStats($customer);

        // Historique des ventes récentes
        $recentSales = $customer->sales()
                               ->with(['saleDetails.product'])
                               ->orderBy('sale_date', 'desc')
                               ->limit(10)
                               ->get();

        // Factures impayées
        $unpaidInvoices = $customer->invoices()
                                  ->unpaid()
                                  ->orderBy('due_date', 'asc')
                                  ->get();

        // Paiements récents
        $recentPayments = $customer->payments()
                                  ->with('createdBy')
                                  ->orderBy('payment_date', 'desc')
                                  ->limit(10)
                                  ->get();

        $this->logActivity('view', $customer, [], [], "Consultation du client {$customer->full_name}");

        return view('customers.show', compact('customer', 'stats', 'recentSales', 'unpaidInvoices', 'recentPayments'));
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(Customer $customer)
    {
        if (!$this->checkRoles(['administrateur', 'responsable_commercial', 'vendeur'])) {
            return $this->unauthorizedResponse();
        }

        return view('customers.edit', compact('customer'));
    }

    /**
     * Mettre à jour un client
     */
    public function update(Request $request, Customer $customer)
    {
        if (!$this->checkRoles(['administrateur', 'responsable_commercial', 'vendeur'])) {
            return $this->unauthorizedResponse();
        }

        $validated = $this->validateCustomer($request, $customer->id);
        $oldValues = $customer->toArray();

        try {
            DB::beginTransaction();

            $customer->update($validated);

            DB::commit();

            $this->logActivity('update', $customer, $oldValues, $customer->toArray(), "Modification du client {$customer->full_name}");

            if ($request->expectsJson()) {
                return $this->successResponse($customer, 'Client modifié avec succès');
            }

            return $this->redirectWithSuccess('customers.index', 'Client modifié avec succès');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleDatabaseError($e, 'modification du client');
        }
    }

    /**
     * Supprimer un client
     */
    public function destroy(Customer $customer)
    {
        if (!$this->checkRoles(['administrateur'])) {
            return $this->unauthorizedResponse();
        }

        try {
            // Vérifier si le client peut être supprimé
            if ($customer->sales()->exists() || $customer->invoices()->exists()) {
                return $this->errorResponse('Impossible de supprimer ce client car il a des ventes ou factures associées');
            }

            $customerName = $customer->full_name;
            $customer->delete();

            $this->logActivity('delete', $customer, $customer->toArray(), [], "Suppression du client {$customerName}");

            if (request()->expectsJson()) {
                return $this->successResponse(null, 'Client supprimé avec succès');
            }

            return $this->redirectWithSuccess('customers.index', 'Client supprimé avec succès');

        } catch (\Exception $e) {
            return $this->handleDatabaseError($e, 'suppression du client');
        }
    }

    /**
     * Recherche de clients (pour AJAX)
     */
    public function search(Request $request)
    {
        $search = $request->get('q', '');
        $limit = $request->get('limit', 10);

        $customers = Customer::where(function($query) use ($search) {
                               $query->where('name', 'like', "%{$search}%")
                                     ->orWhere('first_name', 'like', "%{$search}%")
                                     ->orWhere('phone', 'like', "%{$search}%")
                                     ->orWhere('code', 'like', "%{$search}%");
                           })
                           ->active()
                           ->limit($limit)
                           ->get();

        return $this->successResponse($customers->map(function($customer) {
            return [
                'id' => $customer->id,
                'code' => $customer->code,
                'full_name' => $customer->full_name,
                'phone' => $customer->phone,
                'email' => $customer->email,
                'category' => $customer->category_label,
                'current_balance' => $customer->current_balance,
                'credit_limit' => $customer->credit_limit,
                'available_credit' => $customer->available_credit,
            ];
        }));
    }

    /**
     * Compte client - Factures et paiements
     */
    public function account(Customer $customer)
    {
        if (!$this->checkRoles(['administrateur', 'responsable_commercial', 'comptable'])) {
            return $this->unauthorizedResponse();
        }

        // Factures du client avec détails
        $invoices = $customer->invoices()
                            ->with(['payments'])
                            ->orderBy('invoice_date', 'desc')
                            ->paginate(20);

        // Paiements du client
        $payments = $customer->payments()
                            ->with(['invoice', 'createdBy'])
                            ->orderBy('payment_date', 'desc')
                            ->paginate(20);

        // Résumé du compte
        $accountSummary = [
            'total_invoiced' => $customer->invoices()->sum('total_ttc'),
            'total_paid' => $customer->payments()->where('status', 'valide')->sum('amount'),
            'current_balance' => $customer->current_balance,
            'credit_limit' => $customer->credit_limit,
            'available_credit' => $customer->available_credit,
            'overdue_amount' => $customer->invoices()->overdue()->sum('amount_due'),
        ];

        return view('customers.account', compact('customer', 'invoices', 'payments', 'accountSummary'));
    }

    /**
     * Ajouter un paiement au compte client
     */
    public function addPayment(Request $request, Customer $customer)
    {
        if (!$this->checkRoles(['administrateur', 'responsable_commercial', 'comptable'])) {
            return $this->unauthorizedResponse();
        }

        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:especes,cheque,virement,carte_bancaire,mobile_money',
            'reference_number' => 'nullable|string|max:100',
            'invoice_id' => 'nullable|exists:invoices,id',
            'notes' => 'nullable|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            // Vérifier si la facture appartient au client
            if ($request->invoice_id) {
                $invoice = Invoice::where('id', $request->invoice_id)
                                 ->where('customer_id', $customer->id)
                                 ->first();
                if (!$invoice) {
                    return $this->errorResponse('Facture non trouvée pour ce client');
                }
            }

            // Créer le paiement
            $payment = Payment::create([
                'reference' => Payment::generateReference('encaissement'),
                'customer_id' => $customer->id,
                'invoice_id' => $request->invoice_id,
                'payment_date' => now()->toDateString(),
                'amount' => $request->amount,
                'type' => 'encaissement',
                'method' => $request->payment_method,
                'reference_number' => $request->reference_number,
                'status' => 'valide',
                'created_by' => auth()->id(),
                'notes' => $request->notes,
            ]);

            // Mettre à jour le solde du client
            $customer->current_balance -= $request->amount;
            $customer->save();

            // Mettre à jour la facture si spécifiée
            if (isset($invoice)) {
                $invoice->updatePaymentStatus();
            }

            DB::commit();

            $this->logActivity('create', $payment, [], $payment->toArray(), "Paiement de {$request->amount} FCFA ajouté au compte de {$customer->full_name}");

            if ($request->expectsJson()) {
                return $this->successResponse([
                    'payment' => $payment,
                    'customer' => $customer->fresh(),
                ], 'Paiement ajouté avec succès');
            }

            return $this->backWithSuccess('Paiement ajouté avec succès');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleDatabaseError($e, 'ajout du paiement');
        }
    }

    /**
     * Relevé de compte client
     */
    public function statement(Request $request, Customer $customer)
    {
        if (!$this->checkRoles(['administrateur', 'responsable_commercial', 'comptable'])) {
            return $this->unauthorizedResponse();
        }

        $startDate = $request->get('start_date', Carbon::now()->subMonth()->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->toDateString());

        // Transactions du client (ventes et paiements)
        $transactions = collect();

        // Ajouter les ventes
        $sales = $customer->sales()
                         ->whereBetween('sale_date', [$startDate, $endDate])
                         ->get()
                         ->map(function($sale) {
                             return [
                                 'date' => $sale->sale_date,
                                 'type' => 'vente',
                                 'reference' => $sale->reference,
                                 'description' => "Vente {$sale->reference}",
                                 'debit' => $sale->total_ttc,
                                 'credit' => 0,
                                 'balance' => null,
                             ];
                         });

        // Ajouter les paiements
        $payments = $customer->payments()
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
        $transactions = $transactions->merge($sales)
                                    ->merge($payments)
                                    ->sortBy('date');

        // Calculer les soldes
        $runningBalance = $customer->current_balance;
        foreach ($transactions as &$transaction) {
            $runningBalance += $transaction['debit'] - $transaction['credit'];
            $transaction['balance'] = $runningBalance;
        }

        $this->logActivity('view', $customer, [], [], "Consultation relevé de compte {$customer->full_name}");

        return view('customers.statement', compact('customer', 'transactions', 'startDate', 'endDate'));
    }

    /**
     * Exporter les clients
     */
    public function export(Request $request)
    {
        if (!$this->checkPermission('export_customers')) {
            return $this->unauthorizedResponse();
        }

        $query = Customer::query();
        $this->applyCustomerFilters($query, $request);

        $customers = $query->get();

        $headers = [
            'Code', 'Nom', 'Prénom', 'Téléphone', 'Email', 'Adresse',
            'Ville', 'Pays', 'Catégorie', 'Limite crédit', 'Solde actuel',
            'Compagnie assurance', 'Taux couverture', 'Statut'
        ];

        $data = $customers->map(function($customer) {
            return [
                $customer->code,
                $customer->name,
                $customer->first_name,
                $customer->phone,
                $customer->email,
                $customer->address,
                $customer->city,
                $customer->country,
                $customer->category_label,
                $customer->credit_limit,
                $customer->current_balance,
                $customer->insurance_company,
                $customer->coverage_percentage . '%',
                $customer->is_active ? 'Actif' : 'Inactif',
            ];
        });

        $this->logActivity('export', null, [], [], 'Export de la liste des clients');

        return $this->exportToCsv($data, 'clients', $headers);
    }

    // Méthodes privées utilitaires

    private function applyCustomerFilters($query, Request $request)
    {
        // Recherche textuelle
        if ($search = $request->get('search')) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        // Filtres par catégorie
        if ($category = $request->get('category')) {
            $query->byCategory($category);
        }

        // Filtres spéciaux
        switch ($request->get('filter')) {
            case 'active':
                $query->active();
                break;
            case 'inactive':
                $query->where('is_active', false);
                break;
            case 'with_balance':
                $query->withOutstandingBalance();
                break;
            case 'over_limit':
                $query->overCreditLimit();
                break;
        }
    }

    private function validateCustomer(Request $request, $customerId = null)
    {
        return $request->validate([
            'code' => 'required|string|max:50|unique:customers,code' . ($customerId ? ",$customerId" : ''),
            'name' => 'required|string|max:255',
            'first_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'country' => 'required|string|max:100',
            'category' => 'required|in:particulier,groupe,assurance,depot',
            'tracking_mode' => 'required|in:global,by_member',
            'credit_limit' => 'required|numeric|min:0',
            'insurance_number' => 'nullable|string|max:100',
            'insurance_company' => 'nullable|string|max:255',
            'coverage_percentage' => 'nullable|numeric|min:0|max:100',
            'is_active' => 'boolean',
        ], [
            'code.required' => 'Le code client est obligatoire',
            'code.unique' => 'Ce code client existe déjà',
            'name.required' => 'Le nom est obligatoire',
            'category.required' => 'La catégorie est obligatoire',
            'tracking_mode.required' => 'Le mode de suivi est obligatoire',
            'credit_limit.required' => 'La limite de crédit est obligatoire',
            'country.required' => 'Le pays est obligatoire',
        ]);
    }

    private function getCustomerStats(): array
    {
        return [
            'total' => Customer::count(),
            'active' => Customer::active()->count(),
            'with_balance' => Customer::withOutstandingBalance()->count(),
            'over_limit' => Customer::overCreditLimit()->count(),
            'particuliers' => Customer::byCategory('particulier')->count(),
            'groupes' => Customer::byCategory('groupe')->count(),
            'assurances' => Customer::byCategory('assurance')->count(),
            'depots' => Customer::byCategory('depot')->count(),
            'total_balance' => Customer::sum('current_balance'),
            'total_credit_limit' => Customer::sum('credit_limit'),
        ];
    }

    private function getCustomerDetailStats(Customer $customer): array
    {
        $thirtyDaysAgo = Carbon::now()->subDays(30);
        
        return [
            'total_sales' => $customer->getTotalSalesAmount(),
            'total_paid' => $customer->getTotalPaidAmount(),
            'sales_count' => $customer->sales()->count(),
            'invoices_count' => $customer->invoices()->count(),
            'overdue_invoices' => $customer->invoices()->overdue()->count(),
            'last_sale_date' => $customer->sales()->max('sale_date'),
            'last_payment_date' => $customer->payments()->max('payment_date'),
            'sales_last_30_days' => $customer->sales()
                ->where('sale_date', '>=', $thirtyDaysAgo)
                ->sum('total_ttc'),
            'average_sale_amount' => $customer->sales()->count() > 0 ? 
                $customer->getTotalSalesAmount() / $customer->sales()->count() : 0,
        ];
    }
}