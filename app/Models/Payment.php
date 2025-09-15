<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'reference',
        'customer_id',
        'supplier_id',
        'invoice_id',
        'sale_id',
        'payment_date',
        'amount',
        'type',
        'method',
        'reference_number',
        'status',
        'created_by',
        'notes',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
    ];

    // Relations
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Accesseurs
    protected function typeLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => match($this->type) {
                'encaissement' => 'Encaissement',
                'decaissement' => 'Décaissement',
                'recouvrement' => 'Recouvrement',
                default => 'Non défini',
            }
        );
    }

    protected function methodLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => match($this->method) {
                'especes' => 'Espèces',
                'cheque' => 'Chèque',
                'virement' => 'Virement',
                'carte_bancaire' => 'Carte bancaire',
                'mobile_money' => 'Mobile Money',
                default => 'Non défini',
            }
        );
    }

    protected function statusLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => match($this->status) {
                'en_attente' => 'En attente',
                'valide' => 'Validé',
                'rejete' => 'Rejeté',
                'annule' => 'Annulé',
                default => 'Non défini',
            }
        );
    }

    protected function isValid(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->status === 'valide',
        );
    }

    protected function isPending(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->status === 'en_attente',
        );
    }

    protected function isRejected(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->status === 'rejete',
        );
    }

    // Scopes
    public function scopeByCustomer($query, $customerId)
    {
        return $query->where('customer_id', $customerId);
    }

    public function scopeBySupplier($query, $supplierId)
    {
        return $query->where('supplier_id', $supplierId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByMethod($query, $method)
    {
        return $query->where('method', $method);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('payment_date', [$startDate, $endDate]);
    }

    public function scopeValid($query)
    {
        return $query->where('status', 'valide');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'en_attente');
    }

    public function scopeEncaissements($query)
    {
        return $query->where('type', 'encaissement');
    }

    public function scopeDecaissements($query)
    {
        return $query->where('type', 'decaissement');
    }

    // Méthodes utilitaires
    public function canBeValidated(): bool
    {
        return $this->status === 'en_attente';
    }

    public function canBeRejected(): bool
    {
        return in_array($this->status, ['en_attente', 'valide']);
    }

    public function canBeCancelled(): bool
    {
        return $this->status !== 'annule';
    }

    public function validate(): bool
    {
        if (!$this->canBeValidated()) {
            return false;
        }

        $this->status = 'valide';
        $saved = $this->save();

        if ($saved) {
            // Mettre à jour le solde du client/fournisseur
            $this->updateEntityBalance();
            
            // Mettre à jour le statut de la facture si applicable
            if ($this->invoice) {
                $this->invoice->updatePaymentStatus();
            }
            
            // Mettre à jour le statut de la vente si applicable
            if ($this->sale) {
                $this->sale->updateAmountDue();
            }
        }

        return $saved;
    }

    public function reject(): bool
    {
        if (!$this->canBeRejected()) {
            return false;
        }

        $oldStatus = $this->status;
        $this->status = 'rejete';
        $saved = $this->save();

        if ($saved && $oldStatus === 'valide') {
            // Annuler l'impact sur le solde
            $this->reverseEntityBalance();
            
            // Mettre à jour les statuts
            if ($this->invoice) {
                $this->invoice->updatePaymentStatus();
            }
            
            if ($this->sale) {
                $this->sale->updateAmountDue();
            }
        }

        return $saved;
    }

    public function cancel(): bool
    {
        if (!$this->canBeCancelled()) {
            return false;
        }

        $oldStatus = $this->status;
        $this->status = 'annule';
        $saved = $this->save();

        if ($saved && $oldStatus === 'valide') {
            // Annuler l'impact sur le solde
            $this->reverseEntityBalance();
        }

        return $saved;
    }

    protected function updateEntityBalance(): void
    {
        if ($this->customer_id) {
            $customer = $this->customer;
            if ($this->type === 'encaissement') {
                $customer->current_balance -= $this->amount;
            } else {
                $customer->current_balance += $this->amount;
            }
            $customer->save();
        }

        if ($this->supplier_id) {
            // Logique pour les fournisseurs si nécessaire
        }
    }

    protected function reverseEntityBalance(): void
    {
        if ($this->customer_id) {
            $customer = $this->customer;
            if ($this->type === 'encaissement') {
                $customer->current_balance += $this->amount;
            } else {
                $customer->current_balance -= $this->amount;
            }
            $customer->save();
        }

        if ($this->supplier_id) {
            // Logique pour les fournisseurs si nécessaire
        }
    }

    public static function generateReference($type = 'encaissement'): string
    {
        $prefix = match($type) {
            'encaissement' => 'ENC',
            'decaissement' => 'DEC',
            'recouvrement' => 'REC',
            default => 'PAY',
        };

        $date = Carbon::now()->format('Ymd');
        $count = self::whereDate('created_at', Carbon::now())
                    ->where('type', $type)
                    ->count() + 1;

        return sprintf('%s-%s-%04d', $prefix, $date, $count);
    }

    public function isForCustomer(): bool
    {
        return !is_null($this->customer_id);
    }

    public function isForSupplier(): bool
    {
        return !is_null($this->supplier_id);
    }
}