<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

// ⚡ Ajout des imports manquants
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\Sale;
use App\Models\DeliveryNote;
use App\Models\Inventory;
use App\Models\User;

class StockMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference',
        'product_id',
        'warehouse_id',
        'type',
        'reason',
        'quantity',
        'stock_before',
        'stock_after',
        'unit_cost',
        'total_cost',
        'movement_date',
        'movement_time',
        'related_sale_id',
        'related_delivery_note_id',
        'related_inventory_id',
        'created_by',
        'notes',
    ];

    protected $casts = [
        'unit_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'movement_date' => 'date',
        'movement_time' => 'time',
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

    public function relatedSale(): BelongsTo
    {
        return $this->belongsTo(Sale::class, 'related_sale_id');
    }

    public function relatedDeliveryNote(): BelongsTo
    {
        return $this->belongsTo(DeliveryNote::class, 'related_delivery_note_id');
    }

    public function relatedInventory(): BelongsTo
    {
        return $this->belongsTo(Inventory::class, 'related_inventory_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Accesseurs
    protected function typeLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => match($this->type) {
                'entree' => 'Entrée',
                'sortie' => 'Sortie',
                'transfert' => 'Transfert',
                'ajustement' => 'Ajustement',
                'inventaire' => 'Inventaire',
                'regularisation' => 'Régularisation',
                default => 'Non défini',
            }
        );
    }

    protected function reasonLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => match($this->reason) {
                'achat' => 'Achat',
                'vente' => 'Vente',
                'transfert_entrant' => 'Transfert entrant',
                'transfert_sortant' => 'Transfert sortant',
                'retour_client' => 'Retour client',
                'retour_fournisseur' => 'Retour fournisseur',
                'ajustement_positif' => 'Ajustement positif',
                'ajustement_negatif' => 'Ajustement négatif',
                'inventaire' => 'Inventaire',
                'regularisation' => 'Régularisation',
                'perte' => 'Perte',
                'vol' => 'Vol',
                'perime' => 'Périmé',
                default => 'Autre',
            }
        );
    }

    protected function isEntry(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->type === 'entree',
        );
    }

    protected function isExit(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->type === 'sortie',
        );
    }

    protected function isAdjustment(): Attribute
    {
        return Attribute::make(
            get: fn () => in_array($this->type, ['ajustement', 'inventaire', 'regularisation']),
        );
    }

    protected function impactAmount(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->isEntry ? $this->quantity : -$this->quantity,
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

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByReason($query, $reason)
    {
        return $query->where('reason', $reason);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('movement_date', [$startDate, $endDate]);
    }

    public function scopeEntries($query)
    {
        return $query->where('type', 'entree');
    }

    public function scopeExits($query)
    {
        return $query->where('type', 'sortie');
    }

    public function scopeAdjustments($query)
    {
        return $query->whereIn('type', ['ajustement', 'inventaire', 'regularisation']);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('movement_date', '>=', now()->subDays($days));
    }

    // Méthodes utilitaires
    public static function createEntry($data): self
    {
        return self::create(array_merge($data, [
            'type' => 'entree',
            'reference' => self::generateReference('entree'),
        ]));
    }

    public static function createExit($data): self
    {
        return self::create(array_merge($data, [
            'type' => 'sortie',
            'reference' => self::generateReference('sortie'),
        ]));
    }

    public static function createAdjustment($data): self
    {
        return self::create(array_merge($data, [
            'type' => 'ajustement',
            'reference' => self::generateReference('ajustement'),
        ]));
    }

    public static function generateReference($type = 'movement'): string
    {
        $prefix = match($type) {
            'entree' => 'ENT',
            'sortie' => 'SOR',
            'transfert' => 'TRF',
            'ajustement' => 'AJU',
            'inventaire' => 'INV',
            'regularisation' => 'REG',
            default => 'MOV',
        };

        $date = now()->format('Ymd');
        $count = self::whereDate('created_at', now())
                    ->where('type', $type)
                    ->count() + 1;

        return sprintf('%s-%s-%04d', $prefix, $date, $count);
    }

    public function getMovementDescription(): string
    {
        $direction = $this->isEntry ? '+' : '-';
        return sprintf(
            '%s%d %s - %s (%s)',
            $direction,
            $this->quantity,
            $this->product->unit ?? 'unité(s)',
            $this->reasonLabel,
            $this->movement_date->format('d/m/Y')
        );
    }

    public function hasRelatedDocument(): bool
    {
        return !is_null($this->related_sale_id) || 
               !is_null($this->related_delivery_note_id) || 
               !is_null($this->related_inventory_id);
    }

    public function getRelatedDocument(): ?Model
    {
        if ($this->related_sale_id) {
            return $this->relatedSale;
        }
        
        if ($this->related_delivery_note_id) {
            return $this->relatedDeliveryNote;
        }
        
        if ($this->related_inventory_id) {
            return $this->relatedInventory;
        }
        
        return null;
    }

    public function isValidMovement(): bool
    {
        $expectedStockAfter = $this->stock_before + $this->impactAmount;
        return $this->stock_after === $expectedStockAfter;
    }
}
