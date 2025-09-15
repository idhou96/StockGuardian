<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

class PurchaseOrderDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_order_id',
        'product_id',
        'quantity_ordered',
        'quantity_received',
        'unit_price',
        'discount_percentage',
        'tax_rate',
        'total_ht',
        'total_tax',
        'total_ttc',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'total_ht' => 'decimal:2',
        'total_tax' => 'decimal:2',
        'total_ttc' => 'decimal:2',
    ];

    // Relations
    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // Accesseurs
    protected function quantityPending(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->quantity_ordered - $this->quantity_received,
        );
    }

    protected function deliveryPercentage(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->quantity_ordered > 0 ? 
                ($this->quantity_received / $this->quantity_ordered) * 100 : 
                0,
        );
    }

    protected function isFullyDelivered(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->quantity_received >= $this->quantity_ordered,
        );
    }

    protected function isPartiallyDelivered(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->quantity_received > 0 && $this->quantity_received < $this->quantity_ordered,
        );
    }

    // Méthodes utilitaires
    public function calculateTotals(): void
    {
        // Sous-total avant remise
        $subtotal = $this->quantity_ordered * $this->unit_price;
        
        // Calcul de la remise
        $discountAmount = $subtotal * ($this->discount_percentage / 100);
        
        // Total HT après remise
        $this->total_ht = $subtotal - $discountAmount;
        
        // Calcul de la TVA
        $this->total_tax = $this->total_ht * ($this->tax_rate / 100);
        
        // Total TTC
        $this->total_ttc = $this->total_ht + $this->total_tax;
    }

    public function receiveQuantity($quantity): bool
    {
        if ($quantity <= 0 || $quantity > $this->quantity_pending) {
            return false;
        }

        $this->quantity_received += $quantity;
        return $this->save();
    }

    public function canReceiveMore(): bool
    {
        return $this->quantity_pending > 0;
    }
}