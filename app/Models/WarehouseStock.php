<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

class WarehouseStock extends Model
{
    use HasFactory;

    protected $fillable = [
        'warehouse_id',
        'product_id',
        'quantity',
        'minimum_stock',
        'maximum_stock',
        'last_entry_date',
        'last_exit_date',
    ];

    protected $casts = [
        'last_entry_date' => 'date',
        'last_exit_date' => 'date',
    ];

    // Relations
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // Accesseurs
    protected function stockValue(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->quantity * $this->product->purchase_price,
        );
    }

    protected function isLowStock(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->quantity <= $this->minimum_stock,
        );
    }

    protected function isOutOfStock(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->quantity <= 0,
        );
    }

    protected function stockStatus(): Attribute
    {
        return Attribute::make(
            get: fn () => match(true) {
                $this->quantity <= 0 => 'out_of_stock',
                $this->quantity <= $this->minimum_stock => 'low_stock',
                $this->quantity >= $this->maximum_stock => 'overstock',
                default => 'normal',
            }
        );
    }

    protected function stockStatusLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => match($this->stock_status) {
                'out_of_stock' => 'Rupture de stock',
                'low_stock' => 'Stock faible',
                'overstock' => 'Surstock',
                'normal' => 'Stock normal',
                default => 'Indéterminé',
            }
        );
    }

    // Scopes
    public function scopeLowStock($query)
    {
        return $query->whereColumn('quantity', '<=', 'minimum_stock');
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('quantity', '<=', 0);
    }

    public function scopeOverstock($query)
    {
        return $query->whereColumn('quantity', '>=', 'maximum_stock');
    }

    public function scopeInWarehouse($query, $warehouseId)
    {
        return $query->where('warehouse_id', $warehouseId);
    }

    public function scopeForProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    // Méthodes utilitaires
    public function updateQuantity($quantity, $operation = 'add'): bool
    {
        if ($operation === 'subtract') {
            if ($this->quantity < $quantity) {
                return false; // Stock insuffisant
            }
            $this->quantity -= $quantity;
            $this->last_exit_date = now()->toDateString();
        } else {
            $this->quantity += $quantity;
            $this->last_entry_date = now()->toDateString();
        }
        
        return $this->save();
    }

    public function canFulfillOrder($quantity): bool
    {
        return $this->quantity >= $quantity;
    }

    public function getReorderQuantity(): int
    {
        if ($this->quantity > $this->minimum_stock) {
            return 0;
        }
        
        return $this->maximum_stock - $this->quantity;
    }

    public function daysSinceLastMovement(): ?int
    {
        $lastMovement = max($this->last_entry_date, $this->last_exit_date);
        
        if (!$lastMovement) {
            return null;
        }
        
        return now()->diffInDays($lastMovement);
    }
}