<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;

// 🔑 Import du trait HasRoles
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles; // <-- ajoute HasRoles ici

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'role',
        'is_active',
        'permissions',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'password' => 'hashed',
        'permissions' => 'array',
        'is_active' => 'boolean',
    ];

    // Relations
    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class, 'cashier_id');
    }

    public function inventoriesCreated(): HasMany
    {
        return $this->hasMany(Inventory::class, 'created_by');
    }

    public function inventoriesValidated(): HasMany
    {
        return $this->hasMany(Inventory::class, 'validated_by');
    }

    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class, 'created_by');
    }

    public function deliveryNotesReceived(): HasMany
    {
        return $this->hasMany(DeliveryNote::class, 'received_by');
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class, 'created_by');
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    // Accesseurs
    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->name,
        );
    }

    protected function roleLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => match($this->role) {
                'administrateur' => 'Administrateur',
                'responsable_commercial' => 'Responsable Commercial',
                'vendeur' => 'Vendeur/Agent Commercial',
                'magasinier' => 'Magasinier',
                'responsable_achats' => 'Responsable Achats',
                'comptable' => 'Comptable',
                'caissiere' => 'Caissière',
                'invite' => 'Invité/Stagiaire',
                default => 'Non défini',
            }
        );
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    // Méthodes utilitaires
    public function hasRole($role): bool
    {
        return $this->role === $role;
    }

    public function hasAnyRole(array $roles): bool
    {
        return in_array($this->role, $roles);
    }

    public function hasPermission($permission): bool
    {
        $permissions = $this->permissions ?? [];
        return in_array($permission, $permissions);
    }

    public function canManageStock(): bool
    {
        return $this->hasAnyRole(['administrateur', 'magasinier', 'responsable_achats']);
    }

    public function canSell(): bool
    {
        return $this->hasAnyRole(['administrateur', 'responsable_commercial', 'vendeur', 'caissiere']);
    }

    public function canViewFinancials(): bool
    {
        return $this->hasAnyRole(['administrateur', 'responsable_commercial', 'comptable']);
    }

    public function canManageUsers(): bool
    {
        return $this->hasRole('administrateur');
    }
}
