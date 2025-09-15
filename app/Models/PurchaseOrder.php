<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;

class PurchaseOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'reference',
        'supplier_id',
        'warehouse_id',
        'order_date',
        'expected_delivery_date',
        'status',
        'total_ht',
        'total_tax',
        'total_ttc',
        'created_by',
        'notes',
        'sent_electronically',
        'sent_at',
    ];

    protected $casts = [
        'order_date' => 'date',
        'expected_delivery_date' => 'date',
        'total_ht' => 'decimal:2',
        'total_tax' => 'decimal:2',
        'total_ttc' => 'decimal:2',
        'sent_electronically' => 'boolean',
        'sent_at' => 'datetime',
    ];

    // Relations
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function purchaseOrderDetails(): HasMany
    {
        return $this->hasMany(PurchaseOrderDetail::class);
    }

    public function deliveryNotes(): HasMany
    {
        return $this->hasMany(DeliveryNote::class);
    }

    // Accesseurs
    protected function statusLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => match($this->status) {
                'brouillon' => 'Brouillon',
                'envoye' => 'Envoyé',
                'confirme' => 'Confirmé',
                'partiellement_livre' => 'Partiellement livré',
                'livre' => 'Livré',
                'annule' => 'Annulé',
                default => 'Non défini',
            }
        );
    }

    protected function isDelivered(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->status === 'livre',
        );
    }

    protected function isPartiallyDelivered(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->status === 'partiellement_livre',
        );
    }

    protected function totalQuantityOrdered(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->purchaseOrderDetails()->sum('quantity_ordered'),
        );
    }

    protected function totalQuantityReceived(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->purchaseOrderDetails()->sum('quantity_received'),
        );
    }

    protected function deliveryPercentage(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->total_quantity_ordered > 0 ? 
                ($this->total_quantity_received / $this->total_quantity_ordered) * 100 : 
                0,
        );
    }

    // Scopes
    public function scopeBySupplier($query, $supplierId)
    {
        return $query->where('supplier_id', $supplierId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('order_date', [$startDate, $endDate]);
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', ['envoye', 'confirme', 'partiellement_livre']);
    }

    public function scopeOverdue($query)
    {
        return $query->where('expected_delivery_date', '<', now())
                    ->whereIn('status', ['envoye', 'confirme', 'partiellement_livre']);
    }

    // Méthodes utilitaires
    public function calculateTotals(): void
    {
        $totalHt = 0;
        $totalTax = 0;

        foreach ($this->purchaseOrderDetails as $detail) {
            $totalHt += $detail->total_ht;
            $totalTax += $detail->total_tax;
        }

        $this->total_ht = $totalHt;
        $this->total_tax = $totalTax;
        $this->total_ttc = $totalHt + $totalTax;
    }

    public function canBeModified(): bool
    {
        return in_array($this->status, ['brouillon']);
    }

    public function canBeSent(): bool
    {
        return $this->status === 'brouillon' && $this->purchaseOrderDetails()->exists();
    }

    public function canBeCancelled(): bool
    {
        return !in_array($this->status, ['livre', 'annule']);
    }

    public function send(): bool
    {
        if (!$this->canBeSent()) {
            return false;
        }

        $this->status = 'envoye';
        $this->sent_at = now();
        
        return $this->save();
    }

    public function confirm(): bool
    {
        if ($this->status !== 'envoye') {
            return false;
        }

        $this->status = 'confirme';
        return $this->save();
    }

    public function cancel(): bool
    {
        if (!$this->canBeCancelled()) {
            return false;
        }

        $this->status = 'annule';
        return $this->save();
    }

    public function updateDeliveryStatus(): void
    {
        $totalOrdered = $this->total_quantity_ordered;
        $totalReceived = $this->total_quantity_received;

        if ($totalReceived == 0) {
            $this->status = 'confirme';
        } elseif ($totalReceived >= $totalOrdered) {
            $this->status = 'livre';
        } else {
            $this->status = 'partiellement_livre';
        }

        $this->save();
    }

    public function isOverdue(): bool
    {
        return $this->expected_delivery_date && 
               $this->expected_delivery_date->isPast() && 
               !$this->is_delivered;
    }

    public function getDaysOverdue(): int
    {
        if (!$this->isOverdue()) {
            return 0;
        }

        return now()->diffInDays($this->expected_delivery_date);
    }
}