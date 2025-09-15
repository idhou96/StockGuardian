<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'barcode',
        'name',
        'description',
        'family_id',
        'supplier_id',
        'purchase_price',
        'sale_price',
        'wholesale_price',
        'margin_percentage',
        'tax_rate',
        'apply_tax',
        'current_stock',
        'minimum_stock',
        'maximum_stock',
        'security_stock',
        'is_dangerous',
        'is_pharmaceutical',
        'is_consumable',
        'is_mixed',
        'expiry_date',
        'batch_number',
        'geographic_code',
        'unit',
        'discount_percentage',
        'wholesale_discount',
        'is_active',
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'wholesale_price' => 'decimal:2',
        'margin_percentage' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'wholesale_discount' => 'decimal:2',
        'apply_tax' => 'boolean',
        'is_dangerous' => 'boolean',
        'is_pharmaceutical' => 'boolean',
        'is_consumable' => 'boolean',
        'is_mixed' => 'boolean',
        'is_active' => 'boolean',
        'expiry_date' => 'date',
    ];

    // Relations
    public function family(): BelongsTo
    {
        return $this->belongsTo(Family::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function activePrinciples(): BelongsToMany
    {
        return $this->belongsToMany(ActivePrinciple::class, 'product_active_principle')
                    ->withPivot('dosage')
                    ->withTimestamps();
    }

    public function warehouseStocks(): HasMany
    {
        return $this->hasMany(WarehouseStock::class);
    }

    public function saleDetails(): HasMany
    {
        return $this->hasMany(SaleDetail::class);
    }

    public function purchaseOrderDetails(): HasMany
    {
        return $this->hasMany(PurchaseOrderDetail::class);
    }

    public function deliveryNoteDetails(): HasMany
    {
        return $this->hasMany(DeliveryNoteDetail::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function inventoryDetails(): HasMany
    {
        return $this->hasMany(InventoryDetail::class);
    }

    public function regularizationDetails(): HasMany
    {
        return $this->hasMany(RegularizationDetail::class);
    }

    public function stockOutages(): HasMany
    {
        return $this->hasMany(StockOutage::class);
    }

    public function returnNoteDetails(): HasMany
    {
        return $this->hasMany(ReturnNoteDetail::class);
    }

    // Accesseurs
    protected function salePriceWithTax(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->apply_tax ? 
                $this->sale_price * (1 + $this->tax_rate / 100) : 
                $this->sale_price,
        );
    }

    protected function purchasePriceWithTax(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->apply_tax ? 
                $this->purchase_price * (1 + $this->tax_rate / 100) : 
                $this->purchase_price,
        );
    }

    protected function marginAmount(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->sale_price - $this->purchase_price,
        );
    }

    protected function stockValue(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->current_stock * $this->purchase_price,
        );
    }

    protected function isExpired(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->expiry_date && $this->expiry_date->isPast(),
        );
    }

    protected function isExpiringSoon(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->expiry_date && 
                $this->expiry_date->isBefore(Carbon::now()->addDays(30)),
        );
    }

    protected function isLowStock(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->current_stock <= $this->minimum_stock,
        );
    }

    protected function isOutOfStock(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->current_stock <= 0,
        );
    }

    // Mutateurs
    protected function marginPercentage(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => $value,
            get: fn () => $this->purchase_price > 0 ? 
                (($this->sale_price - $this->purchase_price) / $this->purchase_price) * 100 : 
                0,
        );
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('current_stock', '>', 0);
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('current_stock', '<=', 0);
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('current_stock', '<=', 'minimum_stock');
    }

    public function scopeExpired($query)
    {
        return $query->whereNotNull('expiry_date')
                    ->where('expiry_date', '<', Carbon::now());
    }

    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->whereNotNull('expiry_date')
                    ->where('expiry_date', '<', Carbon::now()->addDays($days))
                    ->where('expiry_date', '>=', Carbon::now());
    }

    public function scopeByFamily($query, $familyId)
    {
        return $query->where('family_id', $familyId);
    }

    public function scopeBySupplier($query, $supplierId)
    {
        return $query->where('supplier_id', $supplierId);
    }

    public function scopePharmaceutical($query)
    {
        return $query->where('is_pharmaceutical', true);
    }

    public function scopeDangerous($query)
    {
        return $query->where('is_dangerous', true);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('code', 'like', "%{$search}%")
              ->orWhere('barcode', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
        });
    }

    // Méthodes utilitaires
    public function calculateSalePriceFromMargin(): void
    {
        if ($this->margin_percentage > 0) {
            $this->sale_price = $this->purchase_price * (1 + $this->margin_percentage / 100);
        }
    }

    public function calculateMarginFromSalePrice(): void
    {
        if ($this->purchase_price > 0) {
            $this->margin_percentage = (($this->sale_price - $this->purchase_price) / $this->purchase_price) * 100;
        }
    }

    public function updateStock($quantity, $operation = 'add'): bool
    {
        if ($operation === 'subtract') {
            if ($this->current_stock < $quantity) {
                return false; // Stock insuffisant
            }
            $this->current_stock -= $quantity;
        } else {
            $this->current_stock += $quantity;
        }
        
        return $this->save();
    }

    public function getStockInWarehouse(Warehouse $warehouse): int
    {
        $stock = $this->warehouseStocks()->where('warehouse_id', $warehouse->id)->first();
        return $stock ? $stock->quantity : 0;
    }

    public function getTotalSalesQuantity(): int
    {
        return $this->saleDetails()->sum('quantity');
    }

    public function getTotalSalesAmount(): float
    {
        return $this->saleDetails()->sum('total_ttc');
    }

    public function getAverageSalePrice(): float
    {
        $totalAmount = $this->saleDetails()->sum('total_ht');
        $totalQuantity = $this->saleDetails()->sum('quantity');
        
        return $totalQuantity > 0 ? $totalAmount / $totalQuantity : 0;
    }

    public function getDaysUntilExpiry(): ?int
    {
        if (!$this->expiry_date) {
            return null;
        }
        
        return Carbon::now()->diffInDays($this->expiry_date, false);
    }

    public function canBeSold($quantity = 1): bool
    {
        return $this->is_active && $this->current_stock >= $quantity && !$this->is_expired;
    }

    public function needsReorder(): bool
    {
        return $this->current_stock <= $this->minimum_stock;
    }

    public function getReorderQuantity(): int
    {
        if (!$this->needsReorder()) {
            return 0;
        }
        
        return $this->maximum_stock - $this->current_stock;
    }

    public function addActivePrinciple($activePrincipleId, $dosage = null): void
    {
        $this->activePrinciples()->attach($activePrincipleId, ['dosage' => $dosage]);
    }

    public function removeActivePrinciple($activePrincipleId): void
    {
        $this->activePrinciples()->detach($activePrincipleId);
    }
}