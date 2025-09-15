<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

class SaleDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'product_id',
        'quantity',
        'unit_price',
        'discount_percentage',
        'discount_amount',
        'tax_rate',
        'total_ht',
        'total_tax',
        'total_ttc',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'total_ht' => 'decimal:2',
        'total_tax' => 'decimal:2',
        'total_ttc' => 'decimal:2',
    ];

    // Relations
    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // Accesseurs
    protected function subtotal(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->quantity * $this->unit_price,
        );
    }

    protected function marginAmount(): Attribute
    {
        return Attribute::make(
            get: fn () => ($this->unit_price - $this->product->purchase_price) * $this->quantity,
        );
    }

    protected function marginPercentage(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->product->purchase_price > 0 ? 
                (($this->unit_price - $this->product->purchase_price) / $this->product->purchase_price) * 100 : 
                0,
        );
    }

    // Méthodes utilitaires
    public function calculateTotals(): void
    {
        // Sous-total avant remise
        $subtotal = $this->quantity * $this->unit_price;
        
        // Calcul de la remise
        if ($this->discount_percentage > 0) {
            $this->discount_amount = $subtotal * ($this->discount_percentage / 100);
        }
        
        // Total HT après remise
        $this->total_ht = $subtotal - $this->discount_amount;
        
        // Calcul de la TVA
        $this->total_tax = $this->total_ht * ($this->tax_rate / 100);
        
        // Total TTC
        $this->total_ttc = $this->total_ht + $this->total_tax;
    }

    public function applyDiscount($percentage): void
    {
        $this->discount_percentage = $percentage;
        $this->calculateTotals();
    }

    public function updateQuantity($newQuantity): bool
    {
        if ($newQuantity <= 0) {
            return false;
        }

        $this->quantity = $newQuantity;
        $this->calculateTotals();
        
        return $this->save();
    }

    public function updateUnitPrice($newPrice): bool
    {
        if ($newPrice < 0) {
            return false;
        }

        $this->unit_price = $newPrice;
        $this->calculateTotals();
        
        return $this->save();
    }

    public function getProfit(): float
    {
        return $this->margin_amount;
    }

    public function getProfitPercentage(): float
    {
        return $this->margin_percentage;
    }
}