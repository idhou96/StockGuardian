<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;

class DeliveryNote extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'reference',
        'supplier_reference',
        'supplier_id',
        'warehouse_id',
        'purchase_order_id',
        'delivery_date',
        'status',
        'total_ht',
        'total_tax',
        'total_ttc',
        'received_by',
        'validated_by',
        'validated_at',
        'notes',
    ];

    protected $casts = [
        'delivery_date' => 'date',
        'total_ht' => 'decimal:2',
        'total_tax' => 'decimal:2',
        'total_ttc' => 'decimal:2',
        'validated_at' => 'datetime',
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

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function validatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function deliveryNoteDetails(): HasMany
    {
        return $this->hasMany(DeliveryNoteDetail::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class, 'related_delivery_note_id');
    }

    // Accesseurs
    protected function statusLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => match($this->status) {
                'en_attente' => 'En attente',
                'partiellement_recu' => 'Partiellement reçu',
                'recu_complet' => 'Reçu complet',
                'valide' => 'Validé',
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

    protected function totalQuantityDelivered(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->deliveryNoteDetails()->sum('quantity_delivered'),
        );
    }

    protected function totalQuantityAccepted(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->deliveryNoteDetails()->sum('quantity_accepted'),
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
        return $query->whereBetween('delivery_date', [$startDate, $endDate]);
    }

    public function scopePendingValidation($query)
    {
        return $query->whereIn('status', ['en_attente', 'partiellement_recu', 'recu_complet']);
    }

    public function scopeValidated($query)
    {
        return $query->where('status', 'valide');
    }

    // Méthodes utilitaires
    public function calculateTotals(): void
    {
        $totalHt = 0;
        $totalTax = 0;

        foreach ($this->deliveryNoteDetails as $detail) {
            $totalHt += $detail->total_ht;
            $totalTax += $detail->total_tax;
        }

        $this->total_ht = $totalHt;
        $this->total_tax = $totalTax;
        $this->total_ttc = $totalHt + $totalTax;
    }

    public function canBeValidated(): bool
    {
        return in_array($this->status, ['en_attente', 'partiellement_recu', 'recu_complet']) 
               && $this->deliveryNoteDetails()->exists();
    }

    public function validate(User $user): bool
    {
        if (!$this->canBeValidated()) {
            return false;
        }

        $this->status = 'valide';
        $this->validated_by = $user->id;
        $this->validated_at = now();
        
        // Créer les mouvements de stock pour chaque produit accepté
        $this->createStockMovements();
        
        // Mettre à jour le statut de la commande d'achat si elle existe
        if ($this->purchaseOrder) {
            $this->updatePurchaseOrderStatus();
        }

        return $this->save();
    }

    protected function createStockMovements(): void
    {
        foreach ($this->deliveryNoteDetails as $detail) {
            if ($detail->quantity_accepted > 0) {
                StockMovement::create([
                    'reference' => 'ENT-' . $this->reference . '-' . $detail->id,
                    'product_id' => $detail->product_id,
                    'warehouse_id' => $this->warehouse_id,
                    'type' => 'entree',
                    'reason' => 'achat',
                    'quantity' => $detail->quantity_accepted,
                    'stock_before' => $detail->product->current_stock,
                    'stock_after' => $detail->product->current_stock + $detail->quantity_accepted,
                    'unit_cost' => $detail->unit_price,
                    'total_cost' => $detail->unit_price * $detail->quantity_accepted,
                    'movement_date' => $this->delivery_date,
                    'movement_time' => now()->format('H:i:s'),
                    'related_delivery_note_id' => $this->id,
                    'created_by' => $this->validated_by,
                ]);

                // Mettre à jour le stock du produit
                $detail->product->updateStock($detail->quantity_accepted, 'add');
                
                // Mettre à jour le stock dans l'entrepôt
                $warehouseStock = WarehouseStock::firstOrCreate([
                    'warehouse_id' => $this->warehouse_id,
                    'product_id' => $detail->product_id,
                ]);
                $warehouseStock->updateQuantity($detail->quantity_accepted, 'add');
            }
        }
    }

    protected function updatePurchaseOrderStatus(): void
    {
        foreach ($this->deliveryNoteDetails as $detail) {
            $orderDetail = $this->purchaseOrder->purchaseOrderDetails()
                              ->where('product_id', $detail->product_id)
                              ->first();
            
            if ($orderDetail) {
                $orderDetail->receiveQuantity($detail->quantity_accepted);
            }
        }

        $this->purchaseOrder->updateDeliveryStatus();
    }

    public function updateReceiptStatus(): void
    {
        $totalDelivered = $this->total_quantity_delivered;
        $totalAccepted = $this->total_quantity_accepted;

        if ($totalAccepted == 0) {
            $this->status = 'en_attente';
        } elseif ($totalAccepted >= $totalDelivered) {
            $this->status = 'recu_complet';
        } else {
            $this->status = 'partiellement_recu';
        }

        $this->save();
    }
}