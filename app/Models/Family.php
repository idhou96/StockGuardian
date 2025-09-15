<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Family extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relations
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeWithProductsCount($query)
    {
        return $query->withCount('products');
    }

    // Méthodes utilitaires
    public function getProductsCountAttribute()
    {
        return $this->products()->count();
    }

    public function getTotalStockValueAttribute()
    {
        return $this->products()->sum(function ($product) {
            return $product->current_stock * $product->purchase_price;
        });
    }
}