<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Warehouse extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'description',
        'manager_name',
        'contact_phone',
        'address',
        'city',
        'type',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relations
    public function warehouseStocks(): HasMany
    {
        return $this->hasMany(WarehouseStock::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function deliveryNotes(): HasMany
    {
        return $this->hasMany(DeliveryNote::class);
    }

    public function inventories(): HasMany
    {
        return $this->hasMany(Inventory::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function regularizations(): HasMany
    {
        return $this->hasMany(Regularization::class);
    }

    public function stockOutages(): HasMany
    {
        return $this->hasMany(StockOutage::class);
    }

    public function returnNotes(): HasMany
    {
        return $this->hasMany(ReturnNote::class);
    }

    // Accesseurs
    protected function typeLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => match($this->type) {
                'principal' => 'Principal',
                'secondaire' => 'Secondaire',
                'reserve' => 'Réserve',
                default => 'Non défini',
            }
        );
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopePrincipal($query)
    {
        return $query->where('type', 'principal');
    }

    // Méthodes utilitaires
    public function getTotalStockValue()
    {
        return $this->warehouseStocks()->with('product')->get()->sum(function ($stock) {
            return $stock->quantity * $stock->product->purchase_price;
        });
    }

    public function getTotalProductsCount()
    {
        return $this->warehouseStocks()->sum('quantity');
    }

    public function getUniqueProductsCount()
    {
        return $this->warehouseStocks()->count();
    }

    public function getLowStockProducts()
    {
        return $this->warehouseStocks()
                   ->whereColumn('quantity', '<=', 'minimum_stock')
                   ->with('product')
                   ->get();
    }

    public function getOutOfStockProducts()
    {
        return $this->warehouseStocks()
                   ->where('quantity', 0)
                   ->with('product')
                   ->get();
    }

    public function hasProduct(Product $product): bool
    {
        return $this->warehouseStocks()->where('product_id', $product->id)->exists();
    }

    public function getProductStock(Product $product): int
    {
        $stock = $this->warehouseStocks()->where('product_id', $product->id)->first();
        return $stock ? $stock->quantity : 0;
    }
}