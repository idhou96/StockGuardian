<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

class Invoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'invoice_number',
        'customer_id',
        'sale_id',
        'invoice_date',
        'due_date',
        'type',
        'status',
        'total_ht',
        'total_tax',
        'total_ttc',
        'amount_paid',
        'amount_due',
        'created_by',
        'notes',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'total_ht' => 'decimal:2',
        'total_tax' => 'decimal:2',
        'total_ttc' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'amount_due' => 'decimal:2',
    ];

    // Relations
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function creditNotes(): HasMany
    {
        return $this->hasMany(CreditNote::class);
    }

    // Accesseurs
    protected function typeLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => match($this->type) {
                'facture' => 'Facture',
                'proforma' => 'Facture Proforma',
                'avoir' => 'Avoir',
                default => 'Non défini',
            }
        );
    }

    protected function statusLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => match($this->status) {
                'brouillon' => 'Brouillon',
                'envoyee' => 'Envoyée',
                'partiellement_payee' => 'Partiellement payée',
                'payee' => 'Payée',
                'en_retard' => 'En retard',
                'annulee' => 'Annulée',
                default => 'Non défini',
            }
        );
    }

    protected function remainingBalance(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->total_ttc - $this->amount_paid,
        );
    }

    protected function isPaid(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->amount_paid >= $this->total_ttc,
        );
    }

    protected function isPartiallyPaid(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->amount_paid > 0 && $this->amount_paid < $this->total_ttc,
        );
    }

    protected function isOverdue(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->due_date && 
                $this->due_date->isPast() && 
                !$this->is_paid && 
                $this->status !== 'annulee',
        );
    }

    protected function daysOverdue(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->is_overdue ? 
                Carbon::now()->diffInDays($this->due_date) : 
                0,
        );
    }

    protected function daysToDue(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->due_date ? 
                $this->due_date->diffInDays(Carbon::now(), false) : 
                null,
        );
    }

    // Scopes
    public function scopeByCustomer($query, $customerId)
    {
        return $query->where('customer_id', $customerId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('invoice_date', [$startDate, $endDate]);
    }

    public function scopePaid($query)
    {
        return $query->whereColumn('amount_paid', '>=', 'total_ttc');
    }

    public function scopeUnpaid($query)
    {
        return $query->whereColumn('amount_paid', '<', 'total_ttc')
                    ->where('status', '!=', 'annulee');
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', Carbon::now())
                    ->whereColumn('amount_paid', '<', 'total_ttc')
                    ->where('status', '!=', 'annulee');
    }

    public function scopeDueSoon($query, $days = 7)
    {
        return $query->whereBetween('due_date', [
            Carbon::now(),
            Carbon::now()->addDays($days)
        ])->whereColumn('amount_paid', '<', 'total_ttc')
        ->where('status', '!=', 'annulee');
    }

    // Méthodes utilitaires
    public function updatePaymentStatus(): void
    {
        $totalPaid = $this->payments()->where('status', 'valide')->sum('amount');
        $this->amount_paid = $totalPaid;
        $this->amount_due = $this->total_ttc - $totalPaid;

        // Mettre à jour le statut
        if ($this->amount_paid >= $this->total_ttc) {
            $this->status = 'payee';
        } elseif ($this->amount_paid > 0) {
            $this->status = 'partiellement_payee';
        } elseif ($this->is_overdue) {
            $this->status = 'en_retard';
        } else {
            $this->status = 'envoyee';
        }

        $this->save();
    }

    public function addPayment($amount, $method = 'especes', $user = null): Payment
    {
        $payment = Payment::create([
            'reference' => 'PAY-' . $this->invoice_number . '-' . now()->format('YmdHis'),
            'customer_id' => $this->customer_id,
            'invoice_id' => $this->id,
            'payment_date' => Carbon::now()->toDateString(),
            'amount' => min($amount, $this->remaining_balance),
            'type' => 'encaissement',
            'method' => $method,
            'status' => 'valide',
            'created_by' => $user ? $user->id : auth()->id(),
        ]);

        $this->updatePaymentStatus();
        
        return $payment;
    }

    public function canBeModified(): bool
    {
        return in_array($this->status, ['brouillon']) && $this->amount_paid == 0;
    }

    public function canBeCancelled(): bool
    {
        return $this->status !== 'annulee' && $this->amount_paid == 0;
    }

    public function cancel(): bool
    {
        if (!$this->canBeCancelled()) {
            return false;
        }

        $this->status = 'annulee';
        return $this->save();
    }

    public function send(): bool
    {
        if ($this->status !== 'brouillon') {
            return false;
        }

        $this->status = 'envoyee';
        return $this->save();
    }

    public function generateFromSale(Sale $sale): void
    {
        $this->sale_id = $sale->id;
        $this->customer_id = $sale->customer_id;
        $this->total_ht = $sale->total_ht;
        $this->total_tax = $sale->total_tax;
        $this->total_ttc = $sale->total_ttc;
        $this->amount_due = $sale->total_ttc;
        
        // Générer le numéro de facture
        $this->invoice_number = $this->generateInvoiceNumber();
    }

    protected function generateInvoiceNumber(): string
    {
        $year = Carbon::now()->format('Y');
        $month = Carbon::now()->format('m');
        
        // Compter les factures du mois
        $count = self::whereYear('invoice_date', $year)
                    ->whereMonth('invoice_date', $month)
                    ->count() + 1;
        
        return sprintf('FAC-%s%s-%04d', $year, $month, $count);
    }

    public function getTotalCreditNotes(): float
    {
        return $this->creditNotes()->where('status', 'valide')->sum('total_ttc');
    }

    public function getNetAmount(): float
    {
        return $this->total_ttc - $this->getTotalCreditNotes();
    }
}