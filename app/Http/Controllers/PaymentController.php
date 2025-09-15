<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\Invoice;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PaymentController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->model = Payment::class;
        $this->modelName = 'Paiement';
        $this->viewPath = 'payments';
    }

    /**
     * Afficher la liste des paiements
     */
    public function index(Request $request)
    {
        if (!$this->checkRoles(['administrateur', 'responsable_commercial', 'comptable', 'caissiere'])) {
            return $this->unauthorizedResponse();
        }

        $query = Payment::with(['customer', 'supplier', 'invoice', 'sale', 'createdBy']);

        // Appliquer les filtres
        $this->applyPaymentFilters($query, $request);

        // Tri et pagination
        $pagination = $this->getPaginationData($request);
        
        $payments = $query->orderBy($pagination['sortBy'], $pagination['sortDirection'])
                         ->paginate($pagination['perPage']);

        // Statistiques
        $stats = $this->getPaymentStats();

        $customers = Customer::active()->get();
        $suppliers = Supplier::active()->get();

        return view('payments.index', compact('payments', 'stats', 'customers', 'suppliers'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        if (!$this->checkRoles(['administrateur', 'responsable_commercial', 'comptable', 'caissiere'])) {
            return $this->unauthorizedResponse();
        }

        $customers = Customer::active()->get();
        $suppliers = Supplier::active()->get();
        $unpaidInvoices = Invoice::unpaid()->with('customer')->get();
        $unpaidSales = Sale::unpaid()->with('customer')->get();

        return view('payments.create', compact('customers', 'suppliers', 'unpaidInvoices', 'unpaidSales'));
    }

    /**
     * Enregistrer un nouveau paiement
     */
    public function store(Request $request)
    {
        if (!$this->checkRoles(['administrateur', 'responsable_commercial', 'comptable', 'caissiere'])) {
            return $this->unauthorizedResponse();
        }

        $validated = $this->validatePayment($request);

        try {
            DB::beginTransaction();

            $payment = Payment::create([
                'reference' => Payment::generateReference($validated['type']),
                'customer_id' => $validated['customer_id'] ?? null,
                'supplier_id' => $validated['supplier_id'] ?? null,
                'invoice_id' => $validated['invoice_id'] ?? null,
                'sale_id' => $validated['sale_id'] ?? null,
                'payment_date' => $validated['payment_date'],
                'amount' => $validated['amount'],
                'type' => $validated['type'],
                'method' => $validated['method'],
                'reference_number' => $validated['reference_number'],
                'status' => $validated['validate_immediately'] ? 'valide' : 'en_attente',
                'created_by' => auth()->id(),
                'notes' => $validated['notes'],
            ]);

            // Valider automatiquement si demandé
            if ($validated['validate_immediately']) {
                $payment->validate();
            }

            DB::commit();

            $this->logActivity('create', $payment, [], $payment->toArray(), "Création du paiement {$payment->reference}");

            if ($request->expectsJson()) {
                return $this->successResponse($payment, 'Paiement créé avec succès');
            }

            return $this->redirectWithSuccess('payments.index', 'Paiement créé avec succès');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleDatabaseError($e, 'création du paiement');
        }
    }

    /**
     * Afficher les détails d'un paiement
     */
    public function show(Payment $payment)
    {
        if (!$this->checkRoles(['administrateur', 'responsable_commercial', 'comptable', 'caissiere'])) {
            return $this->unauthorizedResponse();
        }

        $payment->load([
            'customer',
            'supplier',
            'invoice.customer',
            'sale.customer',
            'createdBy'
        ]);

        $this->logActivity('view', $payment, [], [], "Consultation du paiement {$payment->reference}");

        return view('payments.show', compact('payment'));
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(Payment $payment)
    {
        if (!$this->checkRoles(['administrateur', 'responsable_commercial', 'comptable'])) {
            return $this->unauthorizedResponse();
        }

        if ($payment->status === 'valide') {
            return $this->backWithError('Un paiement validé ne peut plus être modifié');
        }

        $customers = Customer::active()->get();
        $suppliers = Supplier::active()->get();

        return view('payments.edit', compact('payment', 'customers', 'suppliers'));
    }

    /**
     * Mettre à jour un paiement
     */
    public function update(Request $request, Payment $payment)
    {
        if (!$this->checkRoles(['administrateur', 'responsable_commercial', 'comptable'])) {
            return $this->unauthorizedResponse();
        }

        if ($payment->status === 'valide') {
            return $this->backWithError('Un paiement validé ne peut plus être modifié');
        }

        $validated = $this->validatePayment($request);
        $oldValues = $payment->toArray();

        try {
            DB::beginTransaction();

            $payment->update([
                'customer_id' => $validated['customer_id'] ?? null,
                'supplier_id' => $validated['supplier_id'] ?? null,
                'invoice_id' => $validated['invoice_id'] ?? null,
                'sale_id' => $validated['sale_id'] ?? null,
                'payment_date' => $validated['payment_date'],
                'amount' => $validated['amount'],
                'type' => $validated['type'],
                'method' => $validated['method'],
                'reference_number' => $validated['reference_number'],
                'notes' => $validated['notes'],
            ]);

            DB::commit();

            $this->logActivity('update', $payment, $oldValues, $payment->toArray(), "Modification du paiement {$payment->reference}");

            if ($request->expectsJson()) {
                return $this->successResponse($payment, 'Paiement modifié avec succès');
            }

            return $this->redirectWithSuccess('payments.index', 'Paiement modifié avec succès');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleDatabaseError($e, 'modification du paiement');
        }
    }

    /**
     * Valider un paiement
     */
    public function validate(Payment $payment)
    {
        if (!$this->checkRoles(['administrateur', 'responsable_commercial', 'comptable'])) {
            return $this->unauthorizedResponse();
        }

        if (!$payment->canBeValidated()) {
            return $this->errorResponse('Ce paiement ne peut pas être validé');
        }

        try {
            DB::beginTransaction();

            $payment->validate();

            DB::commit();

            $this->logActivity('validate', $payment, [], [], "Validation du paiement {$payment->reference}");

            if (request()->expectsJson()) {
                return $this->successResponse($payment, 'Paiement validé avec succès');
            }

            return $this->backWithSuccess('Paiement validé avec succès');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleDatabaseError($e, 'validation du paiement');
        }
    }

    /**
     * Rejeter un paiement
     */
    public function reject(Payment $payment)
    {
        if (!$this->checkRoles(['administrateur', 'responsable_commercial', 'comptable'])) {
            return $this->unauthorizedResponse();
        }

        if (!$payment->canBeRejected()) {
            return $this->errorResponse('Ce paiement ne peut pas être rejeté');
        }

        try {
            DB::beginTransaction();

            $payment->reject();

            DB::commit();

            $this->logActivity('reject', $payment, [], [], "Rejet du paiement {$payment->reference}");

            if (request()->expectsJson()) {
                return $this->successResponse($payment, 'Paiement rejeté');
            }

            return $this->backWithSuccess('Paiement rejeté');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleDatabaseError($e, 'rejet du paiement');
        }
    }

    /**
     * Annuler un paiement
     */
    public function cancel(Payment $payment)
    {
        if (!$this->checkRoles(['administrateur'])) {
            return $this->unauthorizedResponse();
        }

        if (!$payment->canBeCancelled()) {
            return $this->errorResponse('Ce paiement ne peut pas être annulé');
        }

        try {
            DB::beginTransaction();

            $payment->cancel();

            DB::commit();

            $this->logActivity('cancel', $payment, [], [], "Annulation du paiement {$payment->reference}");

            if (request()->expectsJson()) {
                return $this->successResponse($payment, 'Paiement annulé');
            }

            return $this->backWithSuccess('Paiement annulé');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleDatabaseError($e, 'annulation du paiement');
        }
    }

    /**
     * Journal de caisse
     */
    public function cashJournal(Request $request)
    {
        if (!$this->checkRoles(['administrateur', 'comptable', 'caissiere'])) {
            return $this->unauthorizedResponse();
        }

        $date = $request->get('date', Carbon::today()->toDateString());
        $cashierId = $request->get('cashier_id');

        $query = Payment::whereDate('payment_date', $date)
                       ->where('method', 'especes')
                       ->where('status', 'valide')
                       ->with(['customer', 'supplier', 'invoice', 'sale', 'createdBy']);

        // Filtrer par caissier si spécifié ou si pas admin
        if ($cashierId || !auth()->user()->hasAnyRole(['administrateur', 'comptable'])) {
            $query->where('created_by', $cashierId ?? auth()->id());
        }

        $payments = $query->orderBy('created_at', 'asc')->get();

        // Calculs du journal
        $totalEncaissements = $payments->where('type', 'encaissement')->sum('amount');
        $totalDecaissements = $payments->where('type', 'decaissement')->sum('amount');
        $soldeCaisse = $totalEncaissements - $totalDecaissements;

        $stats = compact('totalEncaissements', 'totalDecaissements', 'soldeCaisse');

        $cashiers = \App\Models\User::whereIn('role', ['caissiere', 'vendeur', 'responsable_commercial'])
                                   ->active()
                                   ->get();

        return view('payments.cash-journal', compact('payments', 'stats', 'date', 'cashiers', 'cashierId'));
    }

    /**
     * Rapprochement bancaire
     */
    public function bankReconciliation(Request $request)
    {
        if (!$this->checkRoles(['administrateur', 'comptable'])) {
            return $this->unauthorizedResponse();
        }

        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->toDateString());
        $method = $request->get('method', 'virement');

        $payments = Payment::whereBetween('payment_date', [$startDate, $endDate])
                          ->where('method', $method)
                          ->where('status', 'valide')
                          ->with(['customer', 'supplier', 'invoice', 'sale'])
                          ->orderBy('payment_date', 'asc')
                          ->get();

        $totalAmount = $payments->sum('amount');
        $encaissements = $payments->where('type', 'encaissement')->sum('amount');
        $decaissements = $payments->where('type', 'decaissement')->sum('amount');

        $stats = compact('totalAmount', 'encaissements', 'decaissements');

        return view('payments.bank-reconciliation', compact('payments', 'stats', 'startDate', 'endDate', 'method'));
    }

    /**
     * Supprimer un paiement
     */
    public function destroy(Payment $payment)
    {
        if (!$this->checkRoles(['administrateur'])) {
            return $this->unauthorizedResponse();
        }

        if ($payment->status === 'valide') {
            return $this->errorResponse('Un paiement validé ne peut pas être supprimé');
        }

        try {
            DB::beginTransaction();

            $paymentReference = $payment->reference;
            $payment->delete();

            DB::commit();

            $this->logActivity('delete', $payment, $payment->toArray(), [], "Suppression du paiement {$paymentReference}");

            if (request()->expectsJson()) {
                return $this->successResponse(null, 'Paiement supprimé avec succès');
            }

            return $this->redirectWithSuccess('payments.index', 'Paiement supprimé avec succès');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleDatabaseError($e, 'suppression du paiement');
        }
    }

    /**
     * Exporter les paiements
     */
    public function export(Request $request)
    {
        if (!$this->checkPermission('export_payments')) {
            return $this->unauthorizedResponse();
        }

        $query = Payment::with(['customer', 'supplier', 'invoice', 'sale', 'createdBy']);
        $this->applyPaymentFilters($query, $request);

        $payments = $query->get();

        $headers = [
            'Référence', 'Date', 'Type', 'Méthode', 'Montant', 'Client/Fournisseur',
            'Facture', 'Vente', 'Numéro référence', 'Statut', 'Créé par', 'Notes'
        ];

        $data = $payments->map(function($payment) {
            return [
                $payment->reference,
                $payment->payment_date->format('d/m/Y'),
                $payment->type_label,
                $payment->method_label,
                $payment->amount,
                $payment->customer?->full_name ?? $payment->supplier?->name ?? '',
                $payment->invoice?->invoice_number ?? '',
                $payment->sale?->reference ?? '',
                $payment->reference_number,
                $payment->status_label,
                $payment->createdBy->name,
                $payment->notes,
            ];
        });

        $this->logActivity('export', null, [], [], 'Export de la liste des paiements');

        return $this->exportToCsv($data, 'paiements', $headers);
    }

    // Méthodes privées utilitaires

    private function applyPaymentFilters($query, Request $request)
    {
        // Recherche par référence
        if ($search = $request->get('search')) {
            $query->where(function($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                  ->orWhere('reference_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($subQ) use ($search) {
                      $subQ->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('supplier', function($subQ) use ($search) {
                      $subQ->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filtres par dates
        $dateFilters = $this->getDateFilters($request);
        if ($dateFilters['startDate']) {
            $query->where('payment_date', '>=', $dateFilters['startDate']);
        }
        if ($dateFilters['endDate']) {
            $query->where('payment_date', '<=', $dateFilters['endDate']);
        }

        // Filtres par type, méthode, statut
        foreach (['type', 'method', 'status', 'customer_id', 'supplier_id'] as $filter) {
            if ($value = $request->get($filter)) {
                $query->where($filter, $value);
            }
        }

        // Filtres spéciaux
        switch ($request->get('filter')) {
            case 'encaissements':
                $query->encaissements();
                break;
            case 'decaissements':
                $query->decaissements();
                break;
            case 'valid':
                $query->valid();
                break;
            case 'pending':
                $query->pending();
                break;
            case 'today':
                $query->whereDate('payment_date', Carbon::today());
                break;
            case 'this_week':
                $query->whereBetween('payment_date', [
                    Carbon::now()->startOfWeek(),
                    Carbon::now()->endOfWeek()
                ]);
                break;
            case 'this_month':
                $query->whereMonth('payment_date', Carbon::now()->month)
                      ->whereYear('payment_date', Carbon::now()->year);
                break;
        }
    }

    private function validatePayment(Request $request)
    {
        $rules = [
            'type' => 'required|in:encaissement,decaissement,recouvrement',
            'customer_id' => 'nullable|exists:customers,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'invoice_id' => 'nullable|exists:invoices,id',
            'sale_id' => 'nullable|exists:sales,id',
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'method' => 'required|in:especes,cheque,virement,carte_bancaire,mobile_money',
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500',
            'validate_immediately' => 'boolean',
        ];

        // Validation conditionnelle : au moins un client ou fournisseur
        $rules['customer_id'] .= '|required_without:supplier_id';
        $rules['supplier_id'] .= '|required_without:customer_id';

        return $request->validate($rules, [
            'type.required' => 'Le type de paiement est obligatoire',
            'payment_date.required' => 'La date de paiement est obligatoire',
            'amount.required' => 'Le montant est obligatoire',
            'amount.min' => 'Le montant doit être supérieur à 0',
            'method.required' => 'La méthode de paiement est obligatoire',
            'customer_id.required_without' => 'Un client ou un fournisseur est requis',
            'supplier_id.required_without' => 'Un client ou un fournisseur est requis',
        ]);
    }

    private function getPaymentStats(): array
    {
        return [
            'total' => Payment::count(),
            'valid' => Payment::valid()->count(),
            'pending' => Payment::pending()->count(),
            'encaissements' => Payment::encaissements()->valid()->sum('amount'),
            'decaissements' => Payment::decaissements()->valid()->sum('amount'),
            'today_amount' => Payment::whereDate('payment_date', Carbon::today())->valid()->sum('amount'),
            'this_month' => Payment::whereMonth('payment_date', Carbon::now()->month)
                                  ->whereYear('payment_date', Carbon::now()->year)
                                  ->valid()
                                  ->sum('amount'),
            'cash_today' => Payment::whereDate('payment_date', Carbon::today())
                                  ->where('method', 'especes')
                                  ->valid()
                                  ->sum('amount'),
        ];
    }
}