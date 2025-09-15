<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

class InventoryDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'inventory_id',
        'product_id',
        'theoretical_quantity',
        'physical_quantity',
        'variance_quantity',
        'unit_price',
        'theoretical_value',
        'physical_value',
        'variance_value',
        'notes',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'theoretical_value' => 'decimal:2',
        'physical_value' => 'decimal:2',
        'variance_value' => 'decimal:2',
    ];

    // Relations
    public function inventory(): BelongsTo
    {
        return $this->belongsTo(Inventory::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // Accesseurs
    protected function variancePercentage(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->theoretical_quantity > 0 ? 
                ($this->variance_quantity / $this->theoretical_quantity) * 100 : 
                0,
        );
    }

    protected function hasVariance(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->variance_quantity != 0,
        );
    }

    protected function varianceType(): Attribute
    {
        return Attribute::make(
            get: fn () => match(true) {
                $this->variance_quantity > 0 => 'surplus',
                $this->variance_quantity < 0 => 'shortage',
                default => 'none',
            }
        );
    }

    protected function varianceTypeLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => match($this->variance_type) {
                'surplus' => 'Surplus',
                'shortage' => 'Manquant',
                'none' => 'Aucun écart',
                default => 'Indéterminé',
            }
        );
    }

    protected function isCounted(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->physical_quantity !== null,
        );
    }

    // Méthodes utilitaires
    public function updatePhysicalQuantity($quantity): void
    {
        $this->physical_quantity = max(0, $quantity);
        $this->calculateVariance();
    }

    public function calculateVariance(): void
    {
        $this->variance_quantity = $this->physical_quantity - $this->theoretical_quantity;
        $this->physical_value = $this->physical_quantity * $this->unit_price;
        $this->variance_value = $this->physical_value - $this->theoretical_value;
        
        $this->save();
    }

    public function markAsCounted($quantity, $notes = null): bool
    {
        $this->updatePhysicalQuantity($quantity);
        
        if ($notes) {
            $this->notes = $notes;
        }
        
        return $this->save();
    }

    public function hasSignificantVariance($threshold = 5): bool
    {
        return abs($this->variance_percentage) > $threshold;
    }

    public function getVarianceDescription(): string
    {
        if (!$this->has_variance) {
            return 'Aucun écart';
        }

        $type = $this->variance_quantity > 0 ? 'surplus' : 'manquant';
        $quantity = abs($this->variance_quantity);
        $percentage = abs($this->variance_percentage);

        return sprintf(
            '%s de %d unité(s) (%.1f%%)',
            ucfirst($type),
            $quantity,
            $percentage
        );
    }

    public function requiresAdjustment(): bool
    {
        return $this->has_variance && $this->inventory->status === 'valide';
    }
}