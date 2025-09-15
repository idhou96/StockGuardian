<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Sale extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'reference',
        'ticket_number',
        'customer_id',
        'warehouse_id',
        'sale_date',
        'sale_time',
        'type',
        'status',
        'total_ht',
        'total_tax',
        'total_ttc',
        'total_discount',
        'amount_paid',
        'amount_due',
        'change_given',
        'payment_method',
        'cashier_id',
        'customer_name',
        'customer_phone',
        'notes',
    ];

    protected $casts = [
        'sale_date' => 'date',
        'sale_time' => 'time',
        'total_ht' => 'decimal:2',
        'total_tax' => 'decimal:2',
        'total_ttc' => 'decimal:2',
        'total_discount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'amount_due' => 'decimal:2',
        'change_given' => 'decimal:2',
    ];

    // Relations
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function saleDetails(): HasMany
    {
        return $this->hasMany(SaleDetail::class);
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class, 'related_sale_id');
    }

    // Accesseurs
    protected function typeLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => match($this->type) {
                'caisse' => 'Vente Caisse',
                'vente_differee' => 'Vente Différée',
                'proforma' => 'Facture Proforma',
                'assurance' => 'Vente Assurance',
                'depot' => 'Vente Dépôt',
                default => 'Non défini',
            }
        );
    }

    protected function statusLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => match($this->status) {
                'en_cours' => 'En cours',
                'validee' => 'Validée',
                'partiellement_payee' => 'Partiellement payée',
                'payee' => 'Payée',
                'annulee' => 'Annulée',
                default => 'Non défini',
            }
        );
    }

    protected function paymentMethodLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => match($this->payment_method) {
                'especes' => 'Espèces',
                'cheque' => 'Chèque',
                'virement' => 'Virement',
                'carte' => 'Carte bancaire',
                'credit' => 'Crédit',
                'assurance' => 'Assurance',
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

    // Scopes
    public function scopeByDate($query, $date)
    {
        return $query->whereDate('sale_date', $date);
    }

    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('sale_date', [$startDate, $endDate]);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByWarehouse($query, $warehouseId)
    {
        return $query->where('warehouse_id', $warehouseId);
    }

    public function scopeByCashier($query, $cashierId)
    {
        return $query->where('cashier_id', $cashierId);
    }

    public function scopePaid($query)
    {
        return $query->whereColumn('amount_paid', '>=', 'total_ttc');
    }

    public function scopeUnpaid($query)
    {
        return $query->whereColumn('amount_paid', '<', 'total_ttc');
    }

    public function scopePartiallyPaid($query)
    {
        return $query->where('amount_paid', '>', 0)
                    ->whereColumn('amount_paid', '<', 'total_ttc');
    }

    // Méthodes utilitaires
    public function calculateTotals(): void
    {
        $totalHt = 0;
        $totalTax = 0;
        $totalDiscount = 0;

        foreach ($this->saleDetails as $detail) {
            $totalHt += $detail->total_ht;
            $totalTax += $detail->total_tax;
            $totalDiscount += $detail->discount_amount;
        }

        $this->total_ht = $totalHt;
        $this->total_tax = $totalTax;
        $this->total_ttc = $totalHt + $totalTax;
        $this->total_discount = $totalDiscount;
        
        $this->updateAmountDue();
    }

    public function updateAmountDue(): void
    {
        $this->amount_due = $this->total_ttc - $this->amount_paid;
        
        // Mettre à jour le statut selon le paiement
        if ($this->amount_paid >= $this->total_ttc) {
            $this->status = 'payee';
        } elseif ($this->amount_paid > 0) {
            $this->status = 'partiellement_payee';
        } elseif ($this->status === 'payee' || $this->status === 'partiellement_payee') {
            $this->status = 'validee';
        }
    }

    public function addPayment($amount, $method = 'especes'): bool
    {
        if ($amount <= 0 || $this->is_paid) {
            return false;
        }

        $this->amount_paid += $amount;
        
        if ($this->amount_paid > $this->total_ttc) {
            $this->change_given = $this->amount_paid - $this->total_ttc;
            $this->amount_paid = $this->total_ttc;
        }

        $this->payment_method = $method;
        $this->updateAmountDue();
        
        return $this->save();
    }

    public function canBeModified(): bool
    {
        return in_array($this->status, ['en_cours', 'validee']);
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

    public function getItemsCount(): int
    {
        return $this->saleDetails()->sum('quantity');
    }

    public function getTotalMargin(): float
    {
        return $this->saleDetails()->with('product')->get()->sum(function ($detail) {
            return ($detail->unit_price - $detail->product->purchase_price) * $detail->quantity;
        });
    }
}