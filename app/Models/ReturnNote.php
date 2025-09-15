<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;

class ReturnNote extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'reference',
        'supplier_id',
        'warehouse_id',
        'delivery_note_id',
        'return_date',
        'reason',
        'status',
        'total_ht',
        'total_tax',
        'total_ttc',
        'created_by',
        'notes',
    ];

    protected $casts = [
        'return_date' => 'date',
        'total_ht' => 'decimal:2',
        'total_tax' => 'decimal:2',
        'total_ttc' => 'decimal:2',
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

    public function deliveryNote(): BelongsTo
    {
        return $this->belongsTo(DeliveryNote::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function returnNoteDetails(): HasMany
    {
        return $this->hasMany(ReturnNoteDetail::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class, 'related_return_note_id');
    }

    public function creditNotes(): HasMany
    {
        return $this->hasMany(CreditNote::class);
    }

    // Accesseurs
    protected function reasonLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => match($this->reason) {
                'produit_defectueux' => 'Produit défectueux',
                'produit_perime' => 'Produit périmé',
                'erreur_livraison' => 'Erreur de livraison',
                'surplus_commande' => 'Surplus de commande',
                'autre' => 'Autre raison',
                default => 'Non défini',
            }
        );
    }

    protected function statusLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => match($this->status) {
                'prepare' => 'Préparé',
                'envoye' => 'Envoyé',
                'accepte' => 'Accepté',
                'refuse' => 'Refusé',
                'partiellement_accepte' => 'Partiellement accepté',
                default => 'Non défini',
            }
        );
    }

    protected function isAccepted(): Attribute
    {
        return Attribute::make(
            get: fn () => in_array($this->status, ['accepte', 'partiellement_accepte']),
        );
    }

    protected function isRejected(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->status === 'refuse',
        );
    }

    protected function isPending(): Attribute
    {
        return Attribute::make(
            get: fn () => in_array($this->status, ['prepare', 'envoye']),
        );
    }

    protected function totalQuantityReturned(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->returnNoteDetails()->sum('quantity_returned'),
        );
    }

    protected function totalQuantityAccepted(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->returnNoteDetails()->sum('quantity_accepted'),
        );
    }

    protected function acceptancePercentage(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->total_quantity_returned > 0 ? 
                ($this->total_quantity_accepted / $this->total_quantity_returned) * 100 : 
                0,
        );
    }

    // Scopes
    public function scopeBySupplier($query, $supplierId)
    {
        return $query->where('supplier_id', $supplierId);
    }

    public function scopeByWarehouse($query, $warehouseId)
    {
        return $query->where('warehouse_id', $warehouseId);
    }

    public function scopeByReason($query, $reason)
    {
        return $query->where('reason', $reason);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('return_date', [$startDate, $endDate]);
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', ['prepare', 'envoye']);
    }

    public function scopeAccepted($query)
    {
        return $query->whereIn('status', ['accepte', 'partiellement_accepte']);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'refuse');
    }

    // Méthodes utilitaires
    public function calculateTotals(): void
    {
        $totalHt = 0;
        $totalTax = 0;

        foreach ($this->returnNoteDetails as $detail) {
            $totalHt += $detail->total_ht;
            $totalTax += $detail->total_tax;
        }

        $this->total_ht = $totalHt;
        $this->total_tax = $totalTax;
        $this->total_ttc = $totalHt + $totalTax;
    }

    public function canBeModified(): bool
    {
        return $this->status === 'prepare';
    }

    public function canBeSent(): bool
    {
        return $this->status === 'prepare' && $this->returnNoteDetails()->exists();
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['prepare', 'envoye']);
    }

    public function send(): bool
    {
        if (!$this->canBeSent()) {
            return false;
        }

        $this->status = 'envoye';
        
        // Créer les mouvements de stock de sortie
        $this->createStockMovements();
        
        return $this->save();
    }

    protected function createStockMovements(): void
    {
        foreach ($this->returnNoteDetails as $detail) {
            StockMovement::create([
                'reference' => 'RET-' . $this->reference . '-' . $detail->id,
                'product_id' => $detail->product_id,
                'warehouse_id' => $this->warehouse_id,
                'type' => 'sortie',
                'reason' => 'retour_fournisseur',
                'quantity' => $detail->quantity_returned,
                'stock_before' => $detail->product->current_stock,
                'stock_after' => $detail->product->current_stock - $detail->quantity_returned,
                'unit_cost' => $detail->unit_price,
                'total_cost' => $detail->quantity_returned * $detail->unit_price,
                'movement_date' => $this->return_date,
                'movement_time' => now()->format('H:i:s'),
                'created_by' => $this->created_by,
                'notes' => "Retour fournisseur {$this->reference}: {$this->reason_label}",
            ]);

            // Mettre à jour le stock du produit
            $detail->product->updateStock($detail->quantity_returned, 'subtract');
            
            // Mettre à jour le stock dans l'entrepôt
            $warehouseStock = WarehouseStock::where('warehouse_id', $this->warehouse_id)
                                           ->where('product_id', $detail->product_id)
                                           ->first();
            if ($warehouseStock) {
                $warehouseStock->updateQuantity($detail->quantity_returned, 'subtract');
            }
        }
    }

    public function accept(): bool
    {
        if ($this->status !== 'envoye') {
            return false;
        }

        // Vérifier si tous les produits sont acceptés
        $allAccepted = $this->returnNoteDetails()
                           ->where('quantity_accepted', '>', 0)
                           ->count() === $this->returnNoteDetails()->count();

        $this->status = $allAccepted ? 'accepte' : 'partiellement_accepte';
        
        // Créer un avoir si accepté
        if ($this->is_accepted) {
            $this->createCreditNote();
        }

        return $this->save();
    }

    protected function createCreditNote(): void
    {
        $creditNote = CreditNote::create([
            'reference' => CreditNote::generateReference(),
            'supplier_id' => $this->supplier_id,
            'return_note_id' => $this->id,
            'credit_note_date' => $this->return_date,
            'type' => 'retour_fournisseur',
            'status' => 'valide',
            'total_ht' => $this->total_ht,
            'total_tax' => $this->total_tax,
            'total_ttc' => $this->total_ttc,
            'created_by' => $this->created_by,
            'reason' => "Avoir pour retour {$this->reference}",
        ]);
    }

    public function reject(): bool
    {
        if ($this->status !== 'envoye') {
            return false;
        }

        $this->status = 'refuse';
        
        // Annuler les mouvements de stock en créant des mouvements inverses
        $this->reverseStockMovements();
        
        return $this->save();
    }

    protected function reverseStockMovements(): void
    {
        foreach ($this->returnNoteDetails as $detail) {
            StockMovement::create([
                'reference' => 'REV-RET-' . $this->reference . '-' . $detail->id,
                'product_id' => $detail->product_id,
                'warehouse_id' => $this->warehouse_id,
                'type' => 'entree',
                'reason' => 'retour_fournisseur',
                'quantity' => $detail->quantity_returned,
                'stock_before' => $detail->product->current_stock,
                'stock_after' => $detail->product->current_stock + $detail->quantity_returned,
                'unit_cost' => $detail->unit_price,
                'total_cost' => $detail->quantity_returned * $detail->unit_price,
                'movement_date' => now()->toDateString(),
                'movement_time' => now()->format('H:i:s'),
                'created_by' => auth()->id(),
                'notes' => "Annulation retour {$this->reference} (refusé par fournisseur)",
            ]);

            // Remettre le stock
            $detail->product->updateStock($detail->quantity_returned, 'add');
            
            $warehouseStock = WarehouseStock::where('warehouse_id', $this->warehouse_id)
                                           ->where('product_id', $detail->product_id)
                                           ->first();
            if ($warehouseStock) {
                $warehouseStock->updateQuantity($detail->quantity_returned, 'add');
            }
        }
    }

    public static function generateReference(): string
    {
        $date = now()->format('Ymd');
        $count = self::whereDate('created_at', now())->count() + 1;
        
        return sprintf('RET-%s-%04d', $date, $count);
    }

    public function addProduct($productId, $quantity, $unitPrice, $reason = null): ReturnNoteDetail
    {
        return ReturnNoteDetail::create([
            'return_note_id' => $this->id,
            'product_id' => $productId,
            'quantity_returned' => $quantity,
            'quantity_accepted' => 0,
            'unit_price' => $unitPrice,
            'total_ht' => $quantity * $unitPrice,
            'total_tax' => ($quantity * $unitPrice) * 0.18, // 18% TVA par défaut
            'total_ttc' => ($quantity * $unitPrice) * 1.18,
            'notes' => $reason,
        ]);
    }

    public function updateAcceptanceStatus(): void
    {
        $totalReturned = $this->total_quantity_returned;
        $totalAccepted = $this->total_quantity_accepted;

        if ($totalAccepted == 0) {
            $this->status = 'refuse';
        } elseif ($totalAccepted >= $totalReturned) {
            $this->status = 'accepte';
        } else {
            $this->status = 'partiellement_accepte';
        }

        $this->save();
    }
}