<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActivePrinciple extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relations
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_active_principle')
                    ->withPivot('dosage')
                    ->withTimestamps();
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
}