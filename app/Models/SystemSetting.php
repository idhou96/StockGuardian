<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Cache;

class SystemSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'description',
        'is_public',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    // Accesseurs
    protected function formattedValue(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->castValue($this->value),
        );
    }

    protected function groupLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => match($this->group) {
                'general' => 'Général',
                'inventory' => 'Inventaire',
                'sales' => 'Ventes',
                'purchases' => 'Achats',
                'customers' => 'Clients',
                'suppliers' => 'Fournisseurs',
                'reports' => 'Rapports',
                'notifications' => 'Notifications',
                'security' => 'Sécurité',
                'appearance' => 'Apparence',
                'integrations' => 'Intégrations',
                default => ucfirst($this->group),
            }
        );
    }

    protected function typeLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => match($this->type) {
                'string' => 'Texte',
                'integer' => 'Nombre entier',
                'float' => 'Nombre décimal',
                'boolean' => 'Oui/Non',
                'json' => 'JSON',
                'array' => 'Tableau',
                'email' => 'Email',
                'url' => 'URL',
                'color' => 'Couleur',
                'date' => 'Date',
                'datetime' => 'Date et heure',
                default => ucfirst($this->type),
            }
        );
    }

    // Scopes
    public function scopeByGroup($query, $group)
    {
        return $query->where('group', $group);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopePrivate($query)
    {
        return $query->where('is_public', false);
    }

    // Méthodes utilitaires
    protected function castValue($value)
    {
        if (is_null($value)) {
            return null;
        }

        return match($this->type) {
            'integer' => (int) $value,
            'float' => (float) $value,
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'json', 'array' => json_decode($value, true),
            'date' => \Carbon\Carbon::parse($value)->toDateString(),
            'datetime' => \Carbon\Carbon::parse($value)->toDateTimeString(),
            default => $value,
        };
    }

    public function setValue($value): bool
    {
        $this->value = $this->prepareValue($value);
        $saved = $this->save();

        if ($saved) {
            $this->clearCache();
        }

        return $saved;
    }

    protected function prepareValue($value): string
    {
        return match($this->type) {
            'boolean' => $value ? '1' : '0',
            'json', 'array' => json_encode($value),
            'date' => \Carbon\Carbon::parse($value)->toDateString(),
            'datetime' => \Carbon\Carbon::parse($value)->toDateTimeString(),
            default => (string) $value,
        };
    }

    protected function clearCache(): void
    {
        Cache::forget("system_setting_{$this->key}");
        Cache::forget("system_settings_group_{$this->group}");
        Cache::forget('system_settings_all');
        Cache::forget('system_settings_public');
    }

    // Méthodes statiques pour accéder aux paramètres
    public static function get(string $key, $default = null)
    {
        return Cache::remember("system_setting_{$key}", 3600, function() use ($key, $default) {
            $setting = self::where('key', $key)->first();
            return $setting ? $setting->formatted_value : $default;
        });
    }

    public static function set(string $key, $value, string $type = 'string'): bool
    {
        $setting = self::firstOrCreate(['key' => $key], [
            'type' => $type,
            'group' => 'general',
        ]);

        return $setting->setValue($value);
    }

    public static function getByGroup(string $group): array
    {
        return Cache::remember("system_settings_group_{$group}", 3600, function() use ($group) {
            return self::where('group', $group)
                      ->get()
                      ->pluck('formatted_value', 'key')
                      ->toArray();
        });
    }

    public static function getAll(): array
    {
        return Cache::remember('system_settings_all', 3600, function() {
            return self::all()->pluck('formatted_value', 'key')->toArray();
        });
    }

    public static function getPublic(): array
    {
        return Cache::remember('system_settings_public', 3600, function() {
            return self::where('is_public', true)
                      ->get()
                      ->pluck('formatted_value', 'key')
                      ->toArray();
        });
    }

    public static function has(string $key): bool
    {
        return self::where('key', $key)->exists();
    }

    public static function remove(string $key): bool
    {
        $setting = self::where('key', $key)->first();
        
        if ($setting) {
            $setting->clearCache();
            return $setting->delete();
        }

        return false;
    }

    public static function clearAllCache(): void
    {
        $keys = self::pluck('key');
        
        foreach ($keys as $key) {
            Cache::forget("system_setting_{$key}");
        }

        $groups = self::distinct()->pluck('group');
        foreach ($groups as $group) {
            Cache::forget("system_settings_group_{$group}");
        }

        Cache::forget('system_settings_all');
        Cache::forget('system_settings_public');
    }

    // Paramètres par défaut du système
    public static function getDefaultSettings(): array
    {
        return [
            // Général
            'app_name' => ['value' => 'StockGuardian', 'type' => 'string', 'group' => 'general', 'description' => 'Nom de l\'application'],
            'app_logo' => ['value' => '', 'type' => 'string', 'group' => 'general', 'description' => 'Logo de l\'application'],
            'company_name' => ['value' => '', 'type' => 'string', 'group' => 'general', 'description' => 'Nom de l\'entreprise'],
            'company_address' => ['value' => '', 'type' => 'string', 'group' => 'general', 'description' => 'Adresse de l\'entreprise'],
            'company_phone' => ['value' => '', 'type' => 'string', 'group' => 'general', 'description' => 'Téléphone de l\'entreprise'],
            'company_email' => ['value' => '', 'type' => 'email', 'group' => 'general', 'description' => 'Email de l\'entreprise'],
            'default_currency' => ['value' => 'FCFA', 'type' => 'string', 'group' => 'general', 'description' => 'Devise par défaut'],
            'default_tax_rate' => ['value' => '18.00', 'type' => 'float', 'group' => 'general', 'description' => 'Taux de TVA par défaut (%)'],

            // Inventaire
            'auto_update_stock' => ['value' => '1', 'type' => 'boolean', 'group' => 'inventory', 'description' => 'Mise à jour automatique du stock'],
            'low_stock_threshold' => ['value' => '10', 'type' => 'integer', 'group' => 'inventory', 'description' => 'Seuil d\'alerte stock faible'],
            'enable_batch_tracking' => ['value' => '1', 'type' => 'boolean', 'group' => 'inventory', 'description' => 'Suivi des lots'],
            'enable_expiry_tracking' => ['value' => '1', 'type' => 'boolean', 'group' => 'inventory', 'description' => 'Suivi des dates d\'expiration'],
            'expiry_alert_days' => ['value' => '30', 'type' => 'integer', 'group' => 'inventory', 'description' => 'Alerte expiration (jours avant)'],

            // Ventes
            'require_customer_for_sales' => ['value' => '0', 'type' => 'boolean', 'group' => 'sales', 'description' => 'Client obligatoire pour les ventes'],
            'auto_generate_invoice' => ['value' => '0', 'type' => 'boolean', 'group' => 'sales', 'description' => 'Génération automatique des factures'],
            'default_payment_method' => ['value' => 'especes', 'type' => 'string', 'group' => 'sales', 'description' => 'Mode de paiement par défaut'],
            'allow_negative_stock' => ['value' => '0', 'type' => 'boolean', 'group' => 'sales', 'description' => 'Autoriser stock négatif'],

            // Achats
            'auto_update_cost_price' => ['value' => '1', 'type' => 'boolean', 'group' => 'purchases', 'description' => 'MAJ auto du prix d\'achat'],
            'require_po_for_delivery' => ['value' => '0', 'type' => 'boolean', 'group' => 'purchases', 'description' => 'Commande obligatoire pour réception'],

            // Notifications
            'enable_email_notifications' => ['value' => '1', 'type' => 'boolean', 'group' => 'notifications', 'description' => 'Notifications par email'],
            'low_stock_notifications' => ['value' => '1', 'type' => 'boolean', 'group' => 'notifications', 'description' => 'Alertes stock faible'],
            'expiry_notifications' => ['value' => '1', 'type' => 'boolean', 'group' => 'notifications', 'description' => 'Alertes expiration'],

            // Sécurité
            'session_timeout' => ['value' => '120', 'type' => 'integer', 'group' => 'security', 'description' => 'Timeout session (minutes)'],
            'password_min_length' => ['value' => '8', 'type' => 'integer', 'group' => 'security', 'description' => 'Longueur min. mot de passe'],
            'enable_activity_log' => ['value' => '1', 'type' => 'boolean', 'group' => 'security', 'description' => 'Journal d\'activité'],
        ];
    }

    public static function seedDefaults(): void
    {
        $defaults = self::getDefaultSettings();

        foreach ($defaults as $key => $config) {
            self::firstOrCreate(['key' => $key], [
                'value' => $config['value'],
                'type' => $config['type'],
                'group' => $config['group'],
                'description' => $config['description'],
                'is_public' => in_array($key, ['app_name', 'company_name', 'default_currency']),
            ]);
        }
    }
}