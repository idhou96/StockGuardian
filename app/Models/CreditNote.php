<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

class CreditNote extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'reference',
        'customer_id',
        'supplier_id',
        'invoice_id',
        'return_note_id',
        'credit_note_date',
        'type',
        'status',
        'total_ht',
        'total_tax',
        'total_ttc',
        'created_by',
        'reason',
        'notes',
    ];

    protected $casts = [
        'credit_note_date' => 'date',
        'total_ht' => 'decimal:2',
        'total_tax' => 'decimal:2',
        'total_ttc' => 'decimal:2',
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

    public function returnNote(): BelongsTo
    {
        return $this->belongsTo(ReturnNote::class);
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
                'retour_client' => 'Retour client',
                'retour_fournisseur' => 'Retour fournisseur',
                'remise' => 'Remise commerciale',
                'erreur_facturation' => 'Erreur de facturation',
                'autre' => 'Autre',
                default => 'Non défini',
            }
        );
    }

    protected function statusLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => match($this->status) {
                'brouillon' => 'Brouillon',
                'valide' => 'Validé',
                'applique' => 'Appliqué',
                'annule' => 'Annulé',
                default => 'Non défini',
            }
        );
    }

    protected function isValidated(): Attribute
    {
        return Attribute::make(
            get: fn () => in_array($this->status, ['valide', 'applique']),
        );
    }

    protected function isApplied(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->status === 'applique',
        );
    }

    protected function isCancelled(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->status === 'annule',
        );
    }

    protected function isForCustomer(): Attribute
    {
        return Attribute::make(
            get: fn () => !is_null($this->customer_id),
        );
    }

    protected function isForSupplier(): Attribute
    {
        return Attribute::make(
            get: fn () => !is_null($this->supplier_id),
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

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('credit_note_date', [$startDate, $endDate]);
    }

    public function scopeValidated($query)
    {
        return $query->whereIn('status', ['valide', 'applique']);
    }

    public function scopeApplied($query)
    {
        return $query->where('status', 'applique');
    }

    public function scopeForCustomers($query)
    {
        return $query->whereNotNull('customer_id');
    }

    public function scopeForSuppliers($query)
    {
        return $query->whereNotNull('supplier_id');
    }

    // Méthodes utilitaires
    public function canBeModified(): bool
    {
        return $this->status === 'brouillon';
    }

    public function canBeValidated(): bool
    {
        return $this->status === 'brouillon';
    }

    public function canBeApplied(): bool
    {
        return $this->status === 'valide';
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
        return $this->save();
    }

    public function apply(): bool
    {
        if (!$this->canBeApplied()) {
            return false;
        }

        $this->status = 'applique';
        
        // Appliquer l'avoir selon le contexte
        if ($this->is_for_customer && $this->customer) {
            $this->applyToCustomer();
        } elseif ($this->is_for_supplier && $this->supplier) {
            $this->applyToSupplier();
        }

        return $this->save();
    }

    protected function applyToCustomer(): void
    {
        // Réduire le solde du client
        $this->customer->current_balance -= $this->total_ttc;
        $this->customer->save();

        // Si lié à une facture, mettre à jour son statut
        if ($this->invoice) {
            $this->invoice->updatePaymentStatus();
        }
    }

    protected function applyToSupplier(): void
    {
        // Logique pour les fournisseurs (réduction de dette, etc.)
        // À implémenter selon les besoins métier
    }

    public function cancel(): bool
    {
        if (!$this->canBeCancelled()) {
            return false;
        }

        $oldStatus = $this->status;
        $this->status = 'annule';
        
        // Si l'avoir était appliqué, annuler son application
        if ($oldStatus === 'applique') {
            $this->reverseApplication();
        }

        return $this->save();
    }

    protected function reverseApplication(): void
    {
        if ($this->is_for_customer && $this->customer) {
            // Remettre le montant sur le solde du client
            $this->customer->current_balance += $this->total_ttc;
            $this->customer->save();

            if ($this->invoice) {
                $this->invoice->updatePaymentStatus();
            }
        }
    }

    public static function generateReference(): string
    {
        $date = Carbon::now()->format('Ymd');
        $count = self::whereDate('created_at', Carbon::now())->count() + 1;
        
        return sprintf('AV-%s-%04d', $date, $count);
    }

    public static function createFromReturn(ReturnNote $returnNote): self
    {
        return self::create([
            'reference' => self::generateReference(),
            'supplier_id' => $returnNote->supplier_id,
            'return_note_id' => $returnNote->id,
            'credit_note_date' => $returnNote->return_date,
            'type' => 'retour_fournisseur',
            'status' => 'valide',
            'total_ht' => $returnNote->total_ht,
            'total_tax' => $returnNote->total_tax,
            'total_ttc' => $returnNote->total_ttc,
            'created_by' => auth()->id(),
            'reason' => "Avoir automatique pour retour {$returnNote->reference}",
        ]);
    }

    public static function createFromInvoiceCorrection(Invoice $invoice, $amount, $reason): self
    {
        return self::create([
            'reference' => self::generateReference(),
            'customer_id' => $invoice->customer_id,
            'invoice_id' => $invoice->id,
            'credit_note_date' => Carbon::now(),
            'type' => 'erreur_facturation',
            'status' => 'brouillon',
            'total_ht' => $amount / 1.18,
            'total_tax' => $amount - ($amount / 1.18),
            'total_ttc' => $amount,
            'created_by' => auth()->id(),
            'reason' => $reason,
        ]);
    }

    public function getRelatedDocument(): ?Model
    {
        if ($this->invoice) {
            return $this->invoice;
        }
        
        if ($this->return_note) {
            return $this->return_note;
        }
        
        return null;
    }

    public function getRelatedDocumentType(): ?string
    {
        if ($this->invoice) {
            return 'facture';
        }
        
        if ($this->return_note) {
            return 'bon_retour';
        }
        
        return null;
    }

    public function getEntity(): ?Model
    {
        return $this->customer ?? $this->supplier;
    }

    public function getEntityType(): ?string
    {
        if ($this->customer) {
            return 'client';
        }
        
        if ($this->supplier) {
            return 'fournisseur';
        }
        
        return null;
    }
}