<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

class ReturnNoteDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'return_note_id',
        'product_id',
        'quantity_returned',
        'quantity_accepted',
        'unit_price',
        'total_ht',
        'total_tax',
        'total_ttc',
        'expiry_date',
        'batch_number',
        'notes',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'total_ht' => 'decimal:2',
        'total_tax' => 'decimal:2',
        'total_ttc' => 'decimal:2',
        'expiry_date' => 'date',
    ];

    // Relations
    public function returnNote(): BelongsTo
    {
        return $this->belongsTo(ReturnNote::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // Accesseurs
    protected function quantityRejected(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->quantity_returned - $this->quantity_accepted,
        );
    }

    protected function acceptancePercentage(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->quantity_returned > 0 ? 
                ($this->quantity_accepted / $this->quantity_returned) * 100 : 
                0,
        );
    }

    protected function isFullyAccepted(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->quantity_accepted >= $this->quantity_returned,
        );
    }

    protected function isPartiallyAccepted(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->quantity_accepted > 0 && $this->quantity_accepted < $this->quantity_returned,
        );
    }

    protected function isRejected(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->quantity_accepted == 0,
        );
    }

    protected function acceptedValue(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->quantity_accepted * $this->unit_price * 1.18, // Avec TVA
        );
    }

    protected function rejectedValue(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->quantity_rejected * $this->unit_price * 1.18, // Avec TVA
        );
    }

    // Méthodes utilitaires
    public function calculateTotals(): void
    {
        // Calculs basés sur la quantité retournée
        $this->total_ht = $this->quantity_returned * $this->unit_price;
        $this->total_tax = $this->total_ht * 0.18; // 18% TVA
        $this->total_ttc = $this->total_ht + $this->total_tax;
        
        $this->save();
    }

    public function acceptQuantity($quantity): bool
    {
        if ($quantity < 0 || $quantity > $this->quantity_returned) {
            return false;
        }

        $this->quantity_accepted = $quantity;
        return $this->save();
    }

    public function acceptAll(): bool
    {
        return $this->acceptQuantity($this->quantity_returned);
    }

    public function rejectAll(): bool
    {
        return $this->acceptQuantity(0);
    }

    public function updateBatchInfo($batchNumber, $expiryDate = null): bool
    {
        $this->batch_number = $batchNumber;
        if ($expiryDate) {
            $this->expiry_date = $expiryDate;
        }
        
        return $this->save();
    }

    public function getReturnReason(): string
    {
        // Déterminer la raison du retour basée sur les informations disponibles
        if ($this->expiry_date && $this->expiry_date->isPast()) {
            return 'Produit périmé';
        }

        return $this->returnNote->reason_label;
    }

    public function canBeModified(): bool
    {
        return $this->returnNote->canBeModified();
    }

    public function getStatusLabel(): string
    {
        return match(true) {
            $this->is_fully_accepted => 'Accepté',
            $this->is_partially_accepted => 'Partiellement accepté',
            $this->is_rejected => 'Refusé',
            default => 'En attente',
        };
    }

    public function getStatusColor(): string
    {
        return match(true) {
            $this->is_fully_accepted => 'green',
            $this->is_partially_accepted => 'yellow',
            $this->is_rejected => 'red',
            default => 'gray',
        };
    }
}