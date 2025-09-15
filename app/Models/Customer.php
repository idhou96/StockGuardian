<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'first_name',
        'phone',
        'email',
        'address',
        'city',
        'country',
        'category',
        'tracking_mode',
        'credit_limit',
        'current_balance',
        'insurance_number',
        'insurance_company',
        'coverage_percentage',
        'is_active',
    ];

    protected $casts = [
        'credit_limit' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'coverage_percentage' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Relations
    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
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
    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn () => trim("{$this->first_name} {$this->name}"),
        );
    }

    protected function fullAddress(): Attribute
    {
        return Attribute::make(
            get: fn () => trim("{$this->address}, {$this->city}, {$this->country}", ', '),
        );
    }

    protected function categoryLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => match($this->category) {
                'particulier' => 'Particulier',
                'groupe' => 'Groupe',
                'assurance' => 'Assurance',
                'depot' => 'Dépôt',
                default => 'Non défini',
            }
        );
    }

    protected function availableCredit(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->credit_limit - $this->current_balance,
        );
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeWithOutstandingBalance($query)
    {
        return $query->where('current_balance', '>', 0);
    }

    public function scopeOverCreditLimit($query)
    {
        return $query->whereColumn('current_balance', '>', 'credit_limit');
    }

    // Méthodes utilitaires
    public function getTotalSalesAmount()
    {
        return $this->sales()->where('status', 'validee')->sum('total_ttc');
    }

    public function getTotalPaidAmount()
    {
        return $this->payments()->where('status', 'valide')->sum('amount');
    }

    public function isOverCreditLimit(): bool
    {
        return $this->current_balance > $this->credit_limit;
    }

    public function hasInsurance(): bool
    {
        return $this->category === 'assurance' && !empty($this->insurance_number);
    }

    public function calculateInsuranceAmount($amount): float
    {
        if (!$this->hasInsurance()) {
            return 0;
        }
        return $amount * ($this->coverage_percentage / 100);
    }

    public function calculatePatientAmount($amount): float
    {
        if (!$this->hasInsurance()) {
            return $amount;
        }
        return $amount - $this->calculateInsuranceAmount($amount);
    }
}