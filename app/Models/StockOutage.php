<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

class StockOutage extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'warehouse_id',
        'outage_date',
        'expected_restock_date',
        'actual_restock_date',
        'status',
        'quantity_needed',
        'quantity_ordered',
        'reported_by',
        'notes',
    ];

    protected $casts = [
        'outage_date' => 'date',
        'expected_restock_date' => 'date',
        'actual_restock_date' => 'date',
    ];

    // Relations
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function reportedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    // Accesseurs
    protected function statusLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => match($this->status) {
                'en_rupture' => 'En rupture',
                'commande_passee' => 'Commande passée',
                'en_cours_livraison' => 'En cours de livraison',
                'resolu' => 'Résolu',
                default => 'Non défini',
            }
        );
    }

    protected function isResolved(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->status === 'resolu',
        );
    }

    protected function isActive(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->status !== 'resolu',
        );
    }

    protected function daysInOutage(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->outage_date ? 
                ($this->actual_restock_date ?? Carbon::now())->diffInDays($this->outage_date) : 
                0,
        );
    }

    protected function daysUntilExpectedRestock(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->expected_restock_date && !$this->is_resolved ? 
                $this->expected_restock_date->diffInDays(Carbon::now(), false) : 
                null,
        );
    }

    protected function isOverdue(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->expected_restock_date && 
                $this->expected_restock_date->isPast() && 
                !$this->is_resolved,
        );
    }

    protected function daysOverdue(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->is_overdue ? 
                Carbon::now()->diffInDays($this->expected_restock_date) : 
                0,
        );
    }

    protected function quantityShortfall(): Attribute
    {
        return Attribute::make(
            get: fn () => max(0, $this->quantity_needed - $this->quantity_ordered),
        );
    }

    protected function orderCompletionPercentage(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->quantity_needed > 0 ? 
                ($this->quantity_ordered / $this->quantity_needed) * 100 : 
                0,
        );
    }

    // Scopes
    public function scopeByProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    public function scopeByWarehouse($query, $warehouseId)
    {
        return $query->where('warehouse_id', $warehouseId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeActive($query)
    {
        return $query->where('status', '!=', 'resolu');
    }

    public function scopeResolved($query)
    {
        return $query->where('status', 'resolu');
    }

    public function scopeOverdue($query)
    {
        return $query->where('expected_restock_date', '<', Carbon::now())
                    ->where('status', '!=', 'resolu');
    }

    public function scopeCritical($query)
    {
        return $query->where('quantity_needed', '>', 0)
                    ->where('quantity_ordered', 0);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('outage_date', [$startDate, $endDate]);
    }

    // Méthodes utilitaires
    public function updateStatus($newStatus): bool
    {
        if (!in_array($newStatus, ['en_rupture', 'commande_passee', 'en_cours_livraison', 'resolu'])) {
            return false;
        }

        $this->status = $newStatus;

        // Marquer la date de résolution si applicable
        if ($newStatus === 'resolu' && !$this->actual_restock_date) {
            $this->actual_restock_date = Carbon::now();
        }

        return $this->save();
    }

    public function markAsOrderPlaced($quantityOrdered, $expectedDate = null): bool
    {
        $this->quantity_ordered = $quantityOrdered;
        $this->status = 'commande_passee';
        
        if ($expectedDate) {
            $this->expected_restock_date = $expectedDate;
        }

        return $this->save();
    }

    public function markAsInDelivery(): bool
    {
        if ($this->status !== 'commande_passee') {
            return false;
        }

        $this->status = 'en_cours_livraison';
        return $this->save();
    }

    public function resolve($actualRestockDate = null): bool
    {
        $this->status = 'resolu';
        $this->actual_restock_date = $actualRestockDate ?? Carbon::now();
        
        return $this->save();
    }

    public function updateQuantityNeeded($newQuantity): bool
    {
        if ($newQuantity < 0) {
            return false;
        }

        $this->quantity_needed = $newQuantity;
        return $this->save();
    }

    public function addNotes($additionalNotes): bool
    {
        $this->notes = $this->notes ? $this->notes . "\n" . $additionalNotes : $additionalNotes;
        return $this->save();
    }

    public function getPriorityLevel(): string
    {
        // Calcul de la priorité basé sur plusieurs facteurs
        $score = 0;

        // Durée de la rupture
        if ($this->days_in_outage > 30) $score += 3;
        elseif ($this->days_in_outage > 14) $score += 2;
        elseif ($this->days_in_outage > 7) $score += 1;

        // Quantité nécessaire vs commandée
        if ($this->quantity_shortfall > 0) $score += 2;

        // Retard sur la date prévue
        if ($this->is_overdue) {
            if ($this->days_overdue > 14) $score += 3;
            elseif ($this->days_overdue > 7) $score += 2;
            else $score += 1;
        }

        // Importance du produit (basé sur les ventes récentes)
        $recentSales = $this->product->saleDetails()
                                   ->whereHas('sale', function($q) {
                                       $q->where('sale_date', '>=', Carbon::now()->subMonth());
                                   })
                                   ->sum('quantity');
        
        if ($recentSales > 50) $score += 2;
        elseif ($recentSales > 20) $score += 1;

        return match(true) {
            $score >= 6 => 'critique',
            $score >= 4 => 'haute',
            $score >= 2 => 'moyenne',
            default => 'basse',
        };
    }

    public function getPriorityColor(): string
    {
        return match($this->getPriorityLevel()) {
            'critique' => 'red',
            'haute' => 'orange', 
            'moyenne' => 'yellow',
            'basse' => 'green',
            default => 'gray',
        };
    }

    public function canBeResolved(): bool
    {
        return $this->status !== 'resolu';
    }

    public function getEstimatedRestockDate(): ?Carbon
    {
        if ($this->expected_restock_date) {
            return $this->expected_restock_date;
        }

        // Estimation basée sur le délai moyen des fournisseurs
        if ($this->product->supplier) {
            $averageDelay = $this->product->supplier->payment_terms_days ?? 30;
            return Carbon::now()->addDays($averageDelay);
        }

        return null;
    }
}