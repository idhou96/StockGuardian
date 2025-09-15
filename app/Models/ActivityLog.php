<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'description',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    // -------------------------------
    // Relations
    // -------------------------------
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function model()
    {
        return $this->morphTo('model', 'model_type', 'model_id');
    }

    // -------------------------------
    // Accessors / Attributes
    // -------------------------------
    protected function actionLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => match($this->action) {
                'create' => 'Création',
                'update' => 'Modification',
                'delete' => 'Suppression',
                'restore' => 'Restauration',
                'login' => 'Connexion',
                'logout' => 'Déconnexion',
                'view' => 'Consultation',
                'export' => 'Export',
                'import' => 'Import',
                'validate' => 'Validation',
                'cancel' => 'Annulation',
                'send' => 'Envoi',
                'receive' => 'Réception',
                'approve' => 'Approbation',
                'reject' => 'Rejet',
                default => ucfirst($this->action),
            }
        );
    }

    protected function modelName(): Attribute
    {
        return Attribute::make(
            get: fn () => match($this->model_type) {
                'App\\Models\\Product' => 'Produit',
                'App\\Models\\Sale' => 'Vente',
                'App\\Models\\Customer' => 'Client',
                'App\\Models\\Supplier' => 'Fournisseur',
                'App\\Models\\PurchaseOrder' => 'Commande d\'achat',
                'App\\Models\\Invoice' => 'Facture',
                'App\\Models\\Payment' => 'Paiement',
                'App\\Models\\Inventory' => 'Inventaire',
                'App\\Models\\StockMovement' => 'Mouvement de stock',
                'App\\Models\\User' => 'Utilisateur',
                default => class_basename($this->model_type ?? ''),
            }
        );
    }

    protected function timeAgo(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->created_at?->diffForHumans(),
        );
    }

    protected function hasActivityChanges(): Attribute
    {
        return Attribute::make(
            get: fn () => !empty($this->old_values) || !empty($this->new_values),
        );
    }

    // -------------------------------
    // Scopes
    // -------------------------------
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    public function scopeByModel($query, $modelType)
    {
        return $query->where('model_type', $modelType);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', Carbon::now()->subDays($days));
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', Carbon::today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('created_at', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek()
        ]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', Carbon::now()->month)
                    ->whereYear('created_at', Carbon::now()->year);
    }

    public function scopeSystemActions($query)
    {
        return $query->whereIn('action', ['login', 'logout', 'export', 'import']);
    }

    public function scopeDataActions($query)
    {
        return $query->whereIn('action', ['create', 'update', 'delete', 'restore']);
    }

    public function scopeBusinessActions($query)
    {
        return $query->whereIn('action', ['validate', 'cancel', 'send', 'receive', 'approve', 'reject']);
    }

    // -------------------------------
    // Méthodes utilitaires
    // -------------------------------
    public static function logActivity(
        string $action,
        $model = null,
        array $oldValues = [],
        array $newValues = [],
        string $description = null,
        $user = null
    ): self {
        $user = $user ?? auth()->user();

        return self::create([
            'user_id' => $user?->id,
            'action' => $action,
            'model_type' => $model ? get_class($model) : null,
            'model_id' => $model?->id,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'description' => $description ?? self::generateDescription($action, $model, $user),
        ]);
    }

    protected static function generateDescription(string $action, $model = null, $user = null): string
    {
        $userName = $user?->name ?? 'Système';
        $actionLabel = match($action) {
            'create' => 'a créé',
            'update' => 'a modifié',
            'delete' => 'a supprimé',
            'restore' => 'a restauré',
            'login' => 's\'est connecté',
            'logout' => 's\'est déconnecté',
            'view' => 'a consulté',
            'export' => 'a exporté',
            'import' => 'a importé',
            'validate' => 'a validé',
            'cancel' => 'a annulé',
            'send' => 'a envoyé',
            'receive' => 'a reçu',
            'approve' => 'a approuvé',
            'reject' => 'a rejeté',
            default => "a effectué l'action : {$action} sur",
        };

        if ($model) {
            $modelName = match(get_class($model)) {
                'App\\Models\\Product' => 'le produit',
                'App\\Models\\Sale' => 'la vente',
                'App\\Models\\Customer' => 'le client',
                'App\\Models\\Supplier' => 'le fournisseur',
                'App\\Models\\PurchaseOrder' => 'la commande d\'achat',
                'App\\Models\\Invoice' => 'la facture',
                'App\\Models\\Payment' => 'le paiement',
                'App\\Models\\Inventory' => 'l\'inventaire',
                'App\\Models\\StockMovement' => 'le mouvement de stock',
                'App\\Models\\User' => 'l\'utilisateur',
                default => class_basename(get_class($model)),
            };

            $identifier = $model->name ?? $model->reference ?? $model->code ?? $model->id ?? '';
            return "{$userName} {$actionLabel} {$modelName} {$identifier}";
        }

        return "{$userName} {$actionLabel}";
    }

    // -------------------------------
    // Gestion des changements
    // -------------------------------
    public function getChangedFields(): array
    {
        if (!$this->hasActivityChanges) {
            return [];
        }

        $changes = [];
        $oldValues = $this->old_values ?? [];
        $newValues = $this->new_values ?? [];

        $allFields = array_unique(array_merge(array_keys($oldValues), array_keys($newValues)));

        foreach ($allFields as $field) {
            $oldValue = $oldValues[$field] ?? null;
            $newValue = $newValues[$field] ?? null;

            if ($oldValue !== $newValue) {
                $changes[$field] = [
                    'old' => $oldValue,
                    'new' => $newValue,
                ];
            }
        }

        return $changes;
    }

    public function getFormattedChanges(): array
    {
        $changes = $this->getChangedFields();
        $formatted = [];

        foreach ($changes as $field => $values) {
            $fieldLabel = $this->getFieldLabel($field);
            $oldValue = $this->formatValue($values['old']);
            $newValue = $this->formatValue($values['new']);

            $formatted[] = [
                'field' => $fieldLabel,
                'old' => $oldValue,
                'new' => $newValue,
                'change' => "{$fieldLabel}: {$oldValue} → {$newValue}",
            ];
        }

        return $formatted;
    }

    protected function getFieldLabel(string $field): string
    {
        return match($field) {
            'name' => 'Nom',
            'email' => 'Email',
            'phone' => 'Téléphone',
            'address' => 'Adresse',
            'status' => 'Statut',
            'total_ttc' => 'Total TTC',
            'total_ht' => 'Total HT',
            'quantity' => 'Quantité',
            'price' => 'Prix',
            'current_stock' => 'Stock actuel',
            'is_active' => 'Actif',
            'created_at' => 'Date de création',
            'updated_at' => 'Date de modification',
            default => ucfirst(str_replace('_', ' ', $field)),
        };
    }

    protected function formatValue($value): string
    {
        if (is_null($value)) return 'Vide';
        if (is_bool($value)) return $value ? 'Oui' : 'Non';
        if (is_array($value)) return json_encode($value);
        if (is_numeric($value) && $value > 1000000000000) {
            return Carbon::createFromTimestamp($value)->format('d/m/Y H:i:s');
        }
        return (string) $value;
    }

    // -------------------------------
    // Infos navigateur et importance
    // -------------------------------
    public function getBrowserInfo(): array
    {
        $userAgent = $this->user_agent;

        if (!$userAgent) return ['browser' => 'Inconnu', 'os' => 'Inconnu'];

        $browser = 'Inconnu';
        if (str_contains($userAgent, 'Chrome')) $browser = 'Chrome';
        elseif (str_contains($userAgent, 'Firefox')) $browser = 'Firefox';
        elseif (str_contains($userAgent, 'Safari')) $browser = 'Safari';
        elseif (str_contains($userAgent, 'Edge')) $browser = 'Edge';

        $os = 'Inconnu';
        if (str_contains($userAgent, 'Windows')) $os = 'Windows';
        elseif (str_contains($userAgent, 'Mac')) $os = 'macOS';
        elseif (str_contains($userAgent, 'Linux')) $os = 'Linux';
        elseif (str_contains($userAgent, 'Android')) $os = 'Android';
        elseif (str_contains($userAgent, 'iOS')) $os = 'iOS';

        return compact('browser', 'os');
    }

    public function isImportantAction(): bool
    {
        return in_array($this->action, [
            'delete', 'validate', 'cancel', 'approve', 'reject', 'login', 'logout'
        ]);
    }

    public function isSensitiveAction(): bool
    {
        return in_array($this->action, [
            'delete', 'export', 'login', 'logout'
        ]) || str_contains($this->model_type ?? '', 'User');
    }

    public function getIcon(): string
    {
        return match($this->action) {
            'create' => 'plus-circle',
            'update' => 'edit',
            'delete' => 'trash-2',
            'restore' => 'rotate-ccw',
            'login' => 'log-in',
            'logout' => 'log-out',
            'view' => 'eye',
            'export' => 'download',
            'import' => 'upload',
            'validate' => 'check-circle',
            'cancel' => 'x-circle',
            'send' => 'send',
            'receive' => 'inbox',
            'approve' => 'thumbs-up',
            'reject' => 'thumbs-down',
            default => 'activity',
        };
    }

    public function getColor(): string
    {
        return match($this->action) {
            'create' => 'green',
            'update' => 'blue',
            'delete' => 'red',
            'restore' => 'yellow',
            'login' => 'green',
            'logout' => 'gray',
            'validate', 'approve' => 'green',
            'cancel', 'reject' => 'red',
            'send' => 'blue',
            'receive' => 'purple',
            default => 'gray',
        };
    }
}
