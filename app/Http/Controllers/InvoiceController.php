<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Customer;
use App\Models\Sale;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InvoiceController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->model = Invoice::class;
        $this->modelName = 'Facture';
        $this->viewPath = 'invoices';
    }

    /**
     * Afficher la liste des factures
     */
    public function index(Request $request)
    {
        if (!$this->checkRoles(['administrateur', 'responsable_commercial', 'comptable', 'caissiere'])) {
            return $this->unauthorizedResponse();
        }

        $query = Invoice::with(['customer', 'sale', 'createdBy', 'payments']);

        // Appliquer les filtres
        $this->applyInvoiceFilters($query, $request);

        // Tri et pagination
        $pagination = $this->getPaginationData($request);
        
        $invoices = $query->orderBy($pagination['sortBy'], $pagination['sortDirection'])
                         ->paginate($pagination['perPage']);

        // Statistiques
        $stats = $this->getInvoiceStats();

        $customers = Customer::active()->get();

        return view('invoices.index', compact('invoices', 'stats', 'customers'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        if (!$this->checkRoles(['administrateur', 'responsable_commercial', 'comptable'])) {
            return $this->unauthorizedResponse();
        }

        $customers = Customer::active()->get();
        $sales = Sale::whereDoesntHave('invoice')
                    ->where('status', 'validee')
                    ->with(['customer', 'warehouse'])
                    ->orderBy('sale_date', 'desc')
                    ->get();

        return view('invoices.create', compact('customers', 'sales'));
    }

    /**
     * Créer une facture depuis une vente
     */
    public function createFromSale(Sale $sale)
    {
        if (!$this->checkRoles(['administrateur', 'responsable_commercial', 'comptable'])) {
            return $this->unauthorizedResponse();
        }

        if ($sale->invoice) {
            return $this->backWithError('Cette vente a déjà une facture associée');
        }

        try {
            DB::beginTransaction();

            $invoice = new Invoice();
            $invoice->generateFromSale($sale);
            $invoice->invoice_date = now()->toDateString();
            $invoice->due_date = now()->addDays(30)->toDateString();
            $invoice->status = 'brouillon';
            $invoice->created_by = auth()->id();
            $invoice->save();

            DB::commit();

            $this->logActivity('create', $invoice, [], $invoice->toArray(), "Création facture {$invoice->invoice_number} depuis vente {$sale->reference}");

            if (request()->expectsJson()) {
                return $this->successResponse($invoice, 'Facture créée depuis la vente avec succès');
            }

            return redirect()->route('invoices.show', $invoice)
                           ->with('success', 'Facture créée depuis la vente avec succès');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleDatabaseError($e, 'création de la facture');
        }
    }

    /**
     * Enregistrer une nouvelle facture
     */
    public function store(Request $request)
    {
        if (!$this->checkRoles(['administrateur', 'responsable_commercial', 'comptable'])) {
            return $this->unauthorizedResponse();
        }

        $validated = $this->validateInvoice($request);

        try {
            DB::beginTransaction();

            $invoice = Invoice::create([
                'invoice_number' => $this->generateInvoiceNumber(),
                'customer_id' => $validated['customer_id'],
                'sale_id' => $validated['sale_id'] ?? null,
                'invoice_date' => $validated['invoice_date'],
                'due_date' => $validated['due_date'],
                'type' => $validated['type'] ?? 'facture',
                'status' => 'brouillon',
                'total_ht' => $validated['total_ht'],
                'total_tax' => $validated['total_tax'],
                'total_ttc' => $validated['total_ttc'],
                'amount_due' => $validated['total_ttc'],
                'created_by' => auth()->id(),
                'notes' => $validated['notes'],
            ]);

            // Envoyer automatiquement si demandé
            if ($validated['send_immediately'] ?? false) {
                $invoice->send();
            }

            DB::commit();

            $this->logActivity('create', $invoice, [], $invoice->toArray(), "Création de la facture {$invoice->invoice_number}");

            if ($request->expectsJson()) {
                return $this->successResponse($invoice, 'Facture créée avec succès');
            }

            return redirect()->route('invoices.show', $invoice)
                           ->with('success', 'Facture créée avec succès');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleDatabaseError($e, 'création de la facture');
        }
    }

    /**
     * Afficher les détails d'une facture
     */
    public function show(Invoice $invoice)
    {
        if (!$this->checkRoles(['administrateur', 'responsable_commercial', 'comptable', 'caissiere'])) {
            return $this->unauthorizedResponse();
        }

        $invoice->load([
            'customer',
            'sale.saleDetails.product',
            'createdBy',
            'payments.createdBy',
            'creditNotes'
        ]);

        $this->logActivity('view', $invoice, [], [], "Consultation de la facture {$invoice->invoice_number}");

        return view('invoices.show', compact('invoice'));
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(Invoice $invoice)
    {
        if (!$this->checkRoles(['administrateur', 'responsable_commercial', 'comptable'])) {
            return $this->unauthorizedResponse();
        }

        if (!$invoice->canBeModified()) {
            return $this->backWithError('Cette facture ne peut plus être modifiée');
        }

        $customers = Customer::active()->get();

        return view('invoices.edit', compact('invoice', 'customers'));
    }

    /**
     * Mettre à jour une facture
     */
    public function update(Request $request, Invoice $invoice)
    {
        if (!$this->checkRoles(['administrateur', 'responsable_commercial', 'comptable'])) {
            return $this->unauthorizedResponse();
        }

        if (!$invoice->canBeModified()) {
            return $this->backWithError('Cette facture ne peut plus être modifiée');
        }

        $validated = $this->validateInvoice($request, $invoice->id);
        $oldValues = $invoice->toArray();

        try {
            DB::beginTransaction();

            $invoice->update([
                'customer_id' => $validated['customer_id'],
                'invoice_date' => $validated['invoice_date'],
                'due_date' => $validated['due_date'],
                'type' => $validated['type'],
                'total_ht' => $validated['total_ht'],
                'total_tax' => $validated['total_tax'],
                'total_ttc' => $validated['total_ttc'],
                'amount_due' => $validated['total_ttc'] - $invoice->amount_paid,
                'notes' => $validated['notes'],
            ]);

            DB::commit();

            $this->logActivity('update', $invoice, $oldValues, $invoice->toArray(), "Modification de la facture {$invoice->invoice_number}");

            if ($request->expectsJson()) {
                return $this->successResponse($invoice, 'Facture modifiée avec succès');
            }

            return $this->redirectWithSuccess('invoices.index', 'Facture modifiée avec succès');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleDatabaseError($e, 'modification de la facture');
        }
    }

    /**
     * Envoyer une facture au client
     */
    public function send(Invoice $invoice)
    {
        if (!$this->checkRoles(['administrateur', 'responsable_commercial', 'comptable'])) {
            return $this->unauthorizedResponse();
        }

        if ($invoice->status !== 'brouillon') {
            return $this->errorResponse('Cette facture ne peut pas être envoyée');
        }

        try {
            DB::beginTransaction();

            $invoice->send();

            DB::commit();

            $this->logActivity('send', $invoice, [], [], "Envoi de la facture {$invoice->invoice_number}");

            if (request()->expectsJson()) {
                return $this->successResponse($invoice, 'Facture envoyée avec succès');
            }

            return $this->backWithSuccess('Facture envoyée avec succès');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleDatabaseError($e, 'envoi de la facture');
        }
    }

    /**
     * Annuler une facture
     */
    public function cancel(Invoice $invoice)
    {
        if (!$this->checkRoles(['administrateur', 'responsable_commercial'])) {
            return $this->unauthorizedResponse();
        }

        if (!$invoice->canBeCancelled()) {
            return $this->errorResponse('Cette facture ne peut pas être annulée');
        }

        try {
            DB::beginTransaction();

            $invoice->cancel();

            DB::commit();

            $this->logActivity('cancel', $invoice, [], [], "Annulation de la facture {$invoice->invoice_number}");

            if (request()->expectsJson()) {
                return $this->successResponse($invoice, 'Facture annulée');
            }

            return $this->backWithSuccess('Facture annulée');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleDatabaseError($e, 'annulation de la facture');
        }
    }

    /**
     * Ajouter un paiement à une facture
     */
    public function addPayment(Request $request, Invoice $invoice)
    {
        if (!$this->checkRoles(['administrateur', 'responsable_commercial', 'comptable', 'caissiere'])) {
            return $this->unauthorizedResponse();
        }

        $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . $invoice->remaining_balance,
            'payment_method' => 'required|in:especes,cheque,virement,carte_bancaire,mobile_money',
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            $payment = $invoice->addPayment(
                $request->amount,
                $request->payment_method,
                auth()->user()
            );

            DB::commit();

            $this->logActivity('create', $payment, [], $payment->toArray(), "Paiement de {$request->amount} FCFA ajouté à la facture {$invoice->invoice_number}");

            if ($request->expectsJson()) {
                return $this->successResponse([
                    'payment' => $payment,
                    'invoice' => $invoice->fresh(),
                ], 'Paiement ajouté avec succès');
            }

            return $this->backWithSuccess('Paiement ajouté avec succès');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleDatabaseError($e, 'ajout du paiement');
        }
    }

    /**
     * Imprimer une facture
     */
    public function print(Invoice $invoice)
    {
        if (!$this->checkRoles(['administrateur', 'responsable_commercial', 'comptable', 'caissiere'])) {
            return $this->unauthorizedResponse();
        }

        $invoice->load([
            'customer',
            'sale.saleDetails.product',
            'createdBy'
        ]);

        $this->logActivity('view', $invoice, [], [], "Impression de la facture {$invoice->invoice_number}");

        return view('invoices.print', compact('invoice'));
    }

    /**
     * Relancer les factures impayées
     */
    public function reminder(Request $request)
    {
        if (!$this->checkRoles(['administrateur', 'responsable_commercial', 'comptable'])) {
            return $this->unauthorizedResponse();
        }

        $request->validate([
            'days_overdue' => 'nullable|integer|min:0',
            'customer_id' => 'nullable|exists:customers,id',
        ]);

        $query = Invoice::overdue();

        if ($customerId = $request->customer_id) {
            $query->where('customer_id', $customerId);
        }

        if ($daysOverdue = $request->days_overdue) {
            $query->where('due_date', '<', Carbon::now()->subDays($daysOverdue));
        }

        $overdueInvoices = $query->with(['customer'])->get();

        // Ici on pourrait ajouter la logique d'envoi d'emails de relance
        // Pour l'instant, on simule juste l'action

        $count = $overdueInvoices->count();

        $this->logActivity('send', null, [], [], "Relance de {$count} facture(s) impayée(s)");

        if ($request->expectsJson()) {
            return $this->successResponse([
                'count' => $count,
                'invoices' => $overdueInvoices->pluck('invoice_number'),
            ], "Relance envoyée pour {$count} facture(s)");
        }

        return $this->backWithSuccess("Relance envoyée pour {$count} facture(s)");
    }

    /**
     * Supprimer une facture
     */
    public function destroy(Invoice $invoice)
    {
        if (!$this->checkRoles(['administrateur'])) {
            return $this->unauthorizedResponse();
        }

        if (!$invoice->canBeCancelled()) {
            return $this->errorResponse('Cette facture ne peut pas être supprimée');
        }

        try {
            DB::beginTransaction();

            $invoiceNumber = $invoice->invoice_number;
            $invoice->delete();

            DB::commit();

            $this->logActivity('delete', $invoice, $invoice->toArray(), [], "Suppression de la facture {$invoiceNumber}");

            if (request()->expectsJson()) {
                return $this->successResponse(null, 'Facture supprimée avec succès');
            }

            return $this->redirectWithSuccess('invoices.index', 'Facture supprimée avec succès');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleDatabaseError($e, 'suppression de la facture');
        }
    }

    /**
     * Exporter les factures
     */
    public function export(Request $request)
    {
        if (!$this->checkPermission('export_invoices')) {
            return $this->unauthorizedResponse();
        }

        $query = Invoice::with(['customer', 'createdBy']);
        $this->applyInvoiceFilters($query, $request);

        $invoices = $query->get();

        $headers = [
            'Numéro facture', 'Date facture', 'Date échéance', 'Client', 'Type',
            'Statut', 'Total HT', 'TVA', 'Total TTC', 'Montant payé',
            'Solde', 'Jours de retard', 'Créé par'
        ];

        $data = $invoices->map(function($invoice) {
            return [
                $invoice->invoice_number,
                $invoice->invoice_date->format('d/m/Y'),
                $invoice->due_date->format('d/m/Y'),
                $invoice->customer->full_name,
                $invoice->type_label,
                $invoice->status_label,
                $invoice->total_ht,
                $invoice->total_tax,
                $invoice->total_ttc,
                $invoice->amount_paid,
                $invoice->remaining_balance,
                $invoice->days_overdue,
                $invoice->createdBy->name,
            ];
        });

        $this->logActivity('export', null, [], [], 'Export de la liste des factures');

        return $this->exportToCsv($data, 'factures', $headers);
    }

    // Méthodes privées utilitaires

    private function applyInvoiceFilters($query, Request $request)
    {
        // Recherche par numéro de facture ou nom client
        if ($search = $request->get('search')) {
            $query->where(function($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($subQ) use ($search) {
                      $subQ->where('name', 'like', "%{$search}%")
                           ->orWhere('first_name', 'like', "%{$search}%");
                  });
            });
        }

        // Filtres par dates
        $dateFilters = $this->getDateFilters($request);
        if ($dateFilters['startDate']) {
            $query->where('invoice_date', '>=', $dateFilters['startDate']);
        }
        if ($dateFilters['endDate']) {
            $query->where('invoice_date', '<=', $dateFilters['endDate']);
        }

        // Filtres par client, type, statut
        foreach (['customer_id', 'type', 'status'] as $filter) {
            if ($value = $request->get($filter)) {
                $query->where($filter, $value);
            }
        }

        // Filtres spéciaux
        switch ($request->get('filter')) {
            case 'paid':
                $query->paid();
                break;
            case 'unpaid':
                $query->unpaid();
                break;
            case 'overdue':
                $query->overdue();
                break;
            case 'due_soon':
                $query->dueSoon();
                break;
            case 'this_month':
                $query->whereMonth('invoice_date', Carbon::now()->month)
                      ->whereYear('invoice_date', Carbon::now()->year);
                break;
        }
    }

    private function validateInvoice(Request $request, $invoiceId = null)
    {
        return $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'sale_id' => 'nullable|exists:sales,id',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after:invoice_date',
            'type' => 'required|in:facture,proforma,avoir',
            'total_ht' => 'required|numeric|min:0',
            'total_tax' => 'required|numeric|min:0',
            'total_ttc' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:500',
            'send_immediately' => 'boolean',
        ], [
            'customer_id.required' => 'Le client est obligatoire',
            'invoice_date.required' => 'La date de facture est obligatoire',
            'due_date.required' => 'La date d\'échéance est obligatoire',
            'due_date.after' => 'La date d\'échéance doit être postérieure à la date de facture',
            'type.required' => 'Le type de facture est obligatoire',
            'total_ht.required' => 'Le montant HT est obligatoire',
            'total_tax.required' => 'Le montant de TVA est obligatoire',
            'total_ttc.required' => 'Le montant TTC est obligatoire',
        ]);
    }

    private function generateInvoiceNumber(): string
    {
        $year = Carbon::now()->format('Y');
        $month = Carbon::now()->format('m');
        
        $count = Invoice::whereYear('invoice_date', $year)
                       ->whereMonth('invoice_date', $month)
                       ->count() + 1;
        
        return sprintf('FAC-%s%s-%04d', $year, $month, $count);
    }

    private function getInvoiceStats(): array
    {
        return [
            'total' => Invoice::count(),
            'draft' => Invoice::where('status', 'brouillon')->count(),
            'sent' => Invoice::where('status', 'envoyee')->count(),
            'paid' => Invoice::paid()->count(),
            'unpaid' => Invoice::unpaid()->count(),
            'overdue' => Invoice::overdue()->count(),
            'total_amount' => Invoice::sum('total_ttc'),
            'paid_amount' => Invoice::sum('amount_paid'),
            'unpaid_amount' => Invoice::unpaid()->sum('amount_due'),
            'overdue_amount' => Invoice::overdue()->sum('amount_due'),
            'this_month' => Invoice::whereMonth('invoice_date', Carbon::now()->month)
                                  ->whereYear('invoice_date', Carbon::now()->year)
                                  ->count(),
        ];
    }
}