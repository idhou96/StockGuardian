<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Inventory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'reference',
        'label',
        'warehouse_id',
        'inventory_date',
        'status',
        'created_by',
        'validated_by',
        'validated_at',
        'notes',
        'theoretical_value',
        'physical_value',
        'variance_value',
    ];

    protected $casts = [
        'inventory_date' => 'date',
        'validated_at' => 'datetime',
        'theoretical_value' => 'decimal:2',
        'physical_value' => 'decimal:2',
        'variance_value' => 'decimal:2',
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

    public function inventoryDetails(): HasMany
    {
        return $this->hasMany(InventoryDetail::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class, 'related_inventory_id');
    }

    // Accesseurs
    protected function statusLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => match($this->status) {
                'en_cours' => 'En cours',
                'termine' => 'Terminé',
                'valide' => 'Validé',
                default => 'Non défini',
            }
        );
    }

    protected function variancePercentage(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->theoretical_value > 0 ? 
                ($this->variance_value / $this->theoretical_value) * 100 : 
                0,
        );
    }

    protected function hasVariances(): Attribute
    {
        return Attribute::make(
            get: fn () => abs($this->variance_value) > 0.01,
        );
    }

    protected function totalProductsCount(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->inventoryDetails()->count(),
        );
    }

    protected function countedProductsCount(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->inventoryDetails()->where('physical_quantity', '>', 0)->count(),
        );
    }

    protected function completionPercentage(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->total_products_count > 0 ? 
                ($this->counted_products_count / $this->total_products_count) * 100 : 
                0,
        );
    }

    // Scopes
    public function scopeByWarehouse($query, $warehouseId)
    {
        return $query->where('warehouse_id', $warehouseId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('inventory_date', [$startDate, $endDate]);
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'en_cours');
    }

    public function scopeCompleted($query)
    {
        return $query->whereIn('status', ['termine', 'valide']);
    }

    public function scopeWithVariances($query)
    {
        return $query->where('variance_value', '!=', 0);
    }

    // Méthodes utilitaires
    public function initializeInventory(): void
    {
        // Créer les détails d'inventaire pour tous les produits en stock
        $warehouseStocks = $this->warehouse->warehouseStocks()->with('product')->get();

        foreach ($warehouseStocks as $stock) {
            InventoryDetail::create([
                'inventory_id' => $this->id,
                'product_id' => $stock->product_id,
                'theoretical_quantity' => $stock->quantity,
                'physical_quantity' => 0, // À remplir lors du comptage
                'variance_quantity' => 0,
                'unit_price' => $stock->product->purchase_price,
                'theoretical_value' => $stock->quantity * $stock->product->purchase_price,
                'physical_value' => 0,
                'variance_value' => 0,
            ]);
        }

        $this->calculateTotals();
    }

    public function calculateTotals(): void
    {
        $theoreticalValue = 0;
        $physicalValue = 0;

        foreach ($this->inventoryDetails as $detail) {
            $theoreticalValue += $detail->theoretical_value;
            $physicalValue += $detail->physical_value;
        }

        $this->theoretical_value = $theoreticalValue;
        $this->physical_value = $physicalValue;
        $this->variance_value = $physicalValue - $theoreticalValue;

        $this->save();
    }

    public function canBeValidated(): bool
    {
        return $this->status === 'termine' && $this->inventoryDetails()->exists();
    }

    public function validate(User $user): bool
    {
        if (!$this->canBeValidated()) {
            return false;
        }

        $this->status = 'valide';
        $this->validated_by = $user->id;
        $this->validated_at = now();

        // Appliquer les ajustements de stock
        $this->applyStockAdjustments();

        return $this->save();
    }

    protected function applyStockAdjustments(): void
    {
        foreach ($this->inventoryDetails as $detail) {
            if ($detail->variance_quantity != 0) {
                // Créer un mouvement de stock pour l'ajustement
                $movementType = $detail->variance_quantity > 0 ? 'entree' : 'sortie';
                $reason = $detail->variance_quantity > 0 ? 'ajustement_positif' : 'ajustement_negatif';

                StockMovement::create([
                    'reference' => 'INV-' . $this->reference . '-' . $detail->id,
                    'product_id' => $detail->product_id,
                    'warehouse_id' => $this->warehouse_id,
                    'type' => $movementType,
                    'reason' => $reason,
                    'quantity' => abs($detail->variance_quantity),
                    'stock_before' => $detail->theoretical_quantity,
                    'stock_after' => $detail->physical_quantity,
                    'unit_cost' => $detail->unit_price,
                    'total_cost' => abs($detail->variance_value),
                    'movement_date' => $this->inventory_date,
                    'movement_time' => now()->format('H:i:s'),
                    'related_inventory_id' => $this->id,
                    'created_by' => $this->validated_by,
                    'notes' => "Ajustement inventaire {$this->reference}",
                ]);

                // Mettre à jour le stock réel
                $detail->product->current_stock = $detail->physical_quantity;
                $detail->product->save();

                // Mettre à jour le stock dans l'entrepôt
                $warehouseStock = WarehouseStock::where('warehouse_id', $this->warehouse_id)
                                               ->where('product_id', $detail->product_id)
                                               ->first();
                if ($warehouseStock) {
                    $warehouseStock->quantity = $detail->physical_quantity;
                    $warehouseStock->save();
                }
            }
        }
    }

    public function complete(): bool
    {
        if ($this->status !== 'en_cours') {
            return false;
        }

        $this->status = 'termine';
        $this->calculateTotals();

        return $this->save();
    }

    public function getVarianceProducts()
    {
        return $this->inventoryDetails()
                   ->where('variance_quantity', '!=', 0)
                   ->with('product')
                   ->get();
    }

    public function getPositiveVariances()
    {
        return $this->inventoryDetails()
                   ->where('variance_quantity', '>', 0)
                   ->with('product')
                   ->get();
    }

    public function getNegativeVariances()
    {
        return $this->inventoryDetails()
                   ->where('variance_quantity', '<', 0)
                   ->with('product')
                   ->get();
    }
}