<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

class DeliveryNoteDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'delivery_note_id',
        'product_id',
        'quantity_delivered',
        'quantity_accepted',
        'unit_price',
        'discount_percentage',
        'tax_rate',
        'total_ht',
        'total_tax',
        'total_ttc',
        'expiry_date',
        'batch_number',
        'notes',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'total_ht' => 'decimal:2',
        'total_tax' => 'decimal:2',
        'total_ttc' => 'decimal:2',
        'expiry_date' => 'date',
    ];

    // Relations
    public function deliveryNote(): BelongsTo
    {
        return $this->belongsTo(DeliveryNote::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // Accesseurs
    protected function quantityRejected(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->quantity_delivered - $this->quantity_accepted,
        );
    }

    protected function acceptancePercentage(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->quantity_delivered > 0 ? 
                ($this->quantity_accepted / $this->quantity_delivered) * 100 : 
                0,
        );
    }

    protected function isFullyAccepted(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->quantity_accepted >= $this->quantity_delivered,
        );
    }

    protected function isPartiallyAccepted(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->quantity_accepted > 0 && $this->quantity_accepted < $this->quantity_delivered,
        );
    }

    protected function isRejected(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->quantity_accepted == 0,
        );
    }

    // Méthodes utilitaires
    public function calculateTotals(): void
    {
        // Utiliser la quantité acceptée pour les calculs
        $subtotal = $this->quantity_accepted * $this->unit_price;
        
        // Calcul de la remise
        $discountAmount = $subtotal * ($this->discount_percentage / 100);
        
        // Total HT après remise
        $this->total_ht = $subtotal - $discountAmount;
        
        // Calcul de la TVA
        $this->total_tax = $this->total_ht * ($this->tax_rate / 100);
        
        // Total TTC
        $this->total_ttc = $this->total_ht + $this->total_tax;
    }

    public function acceptQuantity($quantity): bool
    {
        if ($quantity < 0 || $quantity > $this->quantity_delivered) {
            return false;
        }

        $this->quantity_accepted = $quantity;
        $this->calculateTotals();
        
        return $this->save();
    }

    public function acceptAll(): bool
    {
        return $this->acceptQuantity($this->quantity_delivered);
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
}