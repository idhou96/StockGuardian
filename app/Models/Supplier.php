<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Supplier extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'contact_person',
        'phone',
        'email',
        'address',
        'city',
        'country',
        'credit_limit',
        'payment_terms_days',
        'is_active',
    ];

    protected $casts = [
        'credit_limit' => 'decimal:2',
        'payment_terms_days' => 'integer',
        'is_active' => 'boolean',
    ];

    // Relations
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function deliveryNotes(): HasMany
    {
        return $this->hasMany(DeliveryNote::class);
    }

    public function returnNotes(): HasMany
    {
        return $this->hasMany(ReturnNote::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function creditNotes(): HasMany
    {
        return $this->hasMany(CreditNote::class);
    }

    // Accesseurs
    protected function fullAddress(): Attribute
    {
        return Attribute::make(
            get: fn () => trim("{$this->address}, {$this->city}, {$this->country}", ', '),
        );
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeWithOutstandingOrders($query)
    {
        return $query->whereHas('purchaseOrders', function ($q) {
            $q->whereIn('status', ['envoye', 'confirme', 'partiellement_livre']);
        });
    }

    // Méthodes utilitaires
    public function getTotalOutstandingAmount()
    {
        return $this->purchaseOrders()
                   ->whereIn('status', ['envoye', 'confirme', 'partiellement_livre'])
                   ->sum('total_ttc');
    }

    public function getTotalPaidAmount()
    {
        return $this->payments()->where('status', 'valide')->sum('amount');
    }

    public function getProductsCountAttribute()
    {
        return $this->products()->count();
    }
}