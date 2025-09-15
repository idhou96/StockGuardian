<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

class RegularizationDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'regularization_id',
        'product_id',
        'quantity_adjusted',
        'stock_before',
        'stock_after',
        'unit_cost',
        'total_cost',
        'notes',
    ];

    protected $casts = [
        'unit_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
    ];

    // Relations
    public function regularization(): BelongsTo
    {
        return $this->belongsTo(Regularization::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // Accesseurs
    protected function adjustmentType(): Attribute
    {
        return Attribute::make(
            get: fn () => match(true) {
                $this->quantity_adjusted > 0 => 'increase',
                $this->quantity_adjusted < 0 => 'decrease',
                default => 'none',
            }
        );
    }

    protected function adjustmentTypeLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => match($this->adjustment_type) {
                'increase' => 'Augmentation',
                'decrease' => 'Diminution',
                'none' => 'Aucun ajustement',
                default => 'Indéterminé',
            }
        );
    }

    protected function isPositiveAdjustment(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->quantity_adjusted > 0,
        );
    }

    protected function isNegativeAdjustment(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->quantity_adjusted < 0,
        );
    }

    protected function absoluteQuantity(): Attribute
    {
        return Attribute::make(
            get: fn () => abs($this->quantity_adjusted),
        );
    }

    protected function absoluteCost(): Attribute
    {
        return Attribute::make(
            get: fn () => abs($this->total_cost),
        );
    }

    protected function stockVariancePercentage(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->stock_before > 0 ? 
                ($this->quantity_adjusted / $this->stock_before) * 100 : 
                0,
        );
    }

    // Méthodes utilitaires
    public function calculateTotalCost(): void
    {
        $this->total_cost = $this->quantity_adjusted * $this->unit_cost;
        $this->save();
    }

    public function updateAdjustment($newQuantity): bool
    {
        if (!$this->regularization->canBeModified()) {
            return false;
        }

        $this->quantity_adjusted = $newQuantity;
        $this->stock_after = $this->stock_before + $newQuantity;
        $this->calculateTotalCost();

        return $this->save();
    }

    public function getAdjustmentDescription(): string
    {
        $direction = $this->quantity_adjusted > 0 ? '+' : '';
        $type = $this->is_positive_adjustment ? 'ajout' : 'retrait';
        
        return sprintf(
            '%s%d %s (%s de %s)',
            $direction,
            $this->quantity_adjusted,
            $this->product->unit ?? 'unité(s)',
            ucfirst($type),
            $this->product->name
        );
    }

    public function getImpactDescription(): string
    {
        return sprintf(
            'Stock: %d → %d (%+d) | Valeur: %s FCFA',
            $this->stock_before,
            $this->stock_after,
            $this->quantity_adjusted,
            number_format($this->absolute_cost, 0, ',', ' ')
        );
    }

    public function isSignificantAdjustment($threshold = 10): bool
    {
        return abs($this->stock_variance_percentage) > $threshold;
    }

    public function requiresJustification(): bool
    {
        // Les ajustements importants ou négatifs nécessitent une justification
        return $this->isSignificantAdjustment(20) || 
               $this->is_negative_adjustment || 
               empty($this->notes);
    }
}