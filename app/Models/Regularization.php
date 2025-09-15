<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Regularization extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'reference',
        'label',
        'warehouse_id',
        'regularization_date',
        'type',
        'status',
        'total_value',
        'created_by',
        'validated_by',
        'validated_at',
        'reason',
        'notes',
    ];

    protected $casts = [
        'regularization_date' => 'date',
        'total_value' => 'decimal:2',
        'validated_at' => 'datetime',
    ];

    // Relations
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function validatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function regularizationDetails(): HasMany
    {
        return $this->hasMany(RegularizationDetail::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class, 'related_regularization_id');
    }

    // Accesseurs
    protected function typeLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => match($this->type) {
                'ajustement_positif' => 'Ajustement positif',
                'ajustement_negatif' => 'Ajustement négatif',
                'correction_erreur' => 'Correction d\'erreur',
                'perte' => 'Perte',
                'vol' => 'Vol',
                'deterioration' => 'Détérioration',
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
                'annule' => 'Annulé',
                default => 'Non défini',
            }
        );
    }

    protected function isValidated(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->status === 'valide',
        );
    }

    protected function totalQuantityAdjusted(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->regularizationDetails()->sum('quantity_adjusted'),
        );
    }

    protected function totalProductsCount(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->regularizationDetails()->count(),
        );
    }

    protected function isPositive(): Attribute
    {
        return Attribute::make(
            get: fn () => in_array($this->type, ['ajustement_positif', 'correction_erreur']),
        );
    }

    protected function isNegative(): Attribute
    {
        return Attribute::make(
            get: fn () => in_array($this->type, ['ajustement_negatif', 'perte', 'vol', 'deterioration']),
        );
    }

    // Scopes
    public function scopeByWarehouse($query, $warehouseId)
    {
        return $query->where('warehouse_id', $warehouseId);
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
        return $query->whereBetween('regularization_date', [$startDate, $endDate]);
    }

    public function scopeValidated($query)
    {
        return $query->where('status', 'valide');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'brouillon');
    }

    public function scopePositiveAdjustments($query)
    {
        return $query->whereIn('type', ['ajustement_positif', 'correction_erreur']);
    }

    public function scopeNegativeAdjustments($query)
    {
        return $query->whereIn('type', ['ajustement_negatif', 'perte', 'vol', 'deterioration']);
    }

    // Méthodes utilitaires
    public function calculateTotals(): void
    {
        $totalValue = 0;

        foreach ($this->regularizationDetails as $detail) {
            $totalValue += abs($detail->total_cost);
        }

        $this->total_value = $totalValue;
        $this->save();
    }

    public function canBeValidated(): bool
    {
        return $this->status === 'brouillon' && 
               $this->regularizationDetails()->exists();
    }

    public function canBeModified(): bool
    {
        return $this->status === 'brouillon';
    }

    public function canBeCancelled(): bool
    {
        return $this->status !== 'annule';
    }

    public function validate(User $user): bool
    {
        if (!$this->canBeValidated()) {
            return false;
        }

        $this->status = 'valide';
        $this->validated_by = $user->id;
        $this->validated_at = now();

        // Créer les mouvements de stock pour chaque ajustement
        $this->createStockMovements();

        return $this->save();
    }

    protected function createStockMovements(): void
    {
        foreach ($this->regularizationDetails as $detail) {
            $movementType = $detail->quantity_adjusted > 0 ? 'entree' : 'sortie';
            $reason = match($this->type) {
                'ajustement_positif' => 'ajustement_positif',
                'ajustement_negatif' => 'ajustement_negatif',
                'correction_erreur' => $detail->quantity_adjusted > 0 ? 'ajustement_positif' : 'ajustement_negatif',
                'perte' => 'perte',
                'vol' => 'vol',
                'deterioration' => 'perime',
                default => 'regularisation',
            };

            StockMovement::create([
                'reference' => 'REG-' . $this->reference . '-' . $detail->id,
                'product_id' => $detail->product_id,
                'warehouse_id' => $this->warehouse_id,
                'type' => $movementType,
                'reason' => $reason,
                'quantity' => abs($detail->quantity_adjusted),
                'stock_before' => $detail->stock_before,
                'stock_after' => $detail->stock_after,
                'unit_cost' => $detail->unit_cost,
                'total_cost' => abs($detail->total_cost),
                'movement_date' => $this->regularization_date,
                'movement_time' => now()->format('H:i:s'),
                'created_by' => $this->validated_by,
                'notes' => "Régularisation {$this->reference}: {$this->reason}",
            ]);

            // Mettre à jour le stock du produit
            $product = $detail->product;
            $product->current_stock = $detail->stock_after;
            $product->save();

            // Mettre à jour le stock dans l'entrepôt
            $warehouseStock = WarehouseStock::where('warehouse_id', $this->warehouse_id)
                                           ->where('product_id', $detail->product_id)
                                           ->first();
            
            if ($warehouseStock) {
                $warehouseStock->quantity = $detail->stock_after;
                $warehouseStock->save();
            }
        }
    }

    public function cancel(): bool
    {
        if (!$this->canBeCancelled()) {
            return false;
        }

        $this->status = 'annule';
        return $this->save();
    }

    public static function generateReference(): string
    {
        $date = now()->format('Ymd');
        $count = self::whereDate('created_at', now())->count() + 1;
        
        return sprintf('REG-%s-%04d', $date, $count);
    }

    public function addProduct($productId, $quantityAdjusted, $reason = null): RegularizationDetail
    {
        $product = Product::findOrFail($productId);
        $warehouseStock = WarehouseStock::where('warehouse_id', $this->warehouse_id)
                                       ->where('product_id', $productId)
                                       ->first();

        $currentStock = $warehouseStock ? $warehouseStock->quantity : 0;
        $newStock = $currentStock + $quantityAdjusted;

        return RegularizationDetail::create([
            'regularization_id' => $this->id,
            'product_id' => $productId,
            'quantity_adjusted' => $quantityAdjusted,
            'stock_before' => $currentStock,
            'stock_after' => $newStock,
            'unit_cost' => $product->purchase_price,
            'total_cost' => $quantityAdjusted * $product->purchase_price,
            'notes' => $reason,
        ]);
    }

    public function getPositiveAdjustments()
    {
        return $this->regularizationDetails()
                   ->where('quantity_adjusted', '>', 0)
                   ->with('product')
                   ->get();
    }

    public function getNegativeAdjustments()
    {
        return $this->regularizationDetails()
                   ->where('quantity_adjusted', '<', 0)
                   ->with('product')
                   ->get();
    }
}