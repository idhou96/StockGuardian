<?php
// ⚙️ SYSTEM SETTING SEEDER COMPLET
// database/seeders/SystemSettingSeeder.php

namespace Database\Seeders;

use App\Models\SystemSetting;
use Illuminate\Database\Seeder;

class SystemSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            [
                'key' => 'company_name',
                'value' => 'StockGuardian Pharmacie',
                'type' => 'string',
                'description' => 'Nom de l\'entreprise',
                'group' => 'general',
            ],
            [
                'key' => 'company_address',
                'value' => 'Abidjan, Côte d\'Ivoire',
                'type' => 'string',
                'description' => 'Adresse de l\'entreprise',
                'group' => 'general',
            ],
            [
                'key' => 'company_phone',
                'value' => '+225 01 02 03 04 05',
                'type' => 'string',
                'description' => 'Numéro de téléphone principal',
                'group' => 'general',
            ],
            [
                'key' => 'company_email',
                'value' => 'contact@stockguardian.ci',
                'type' => 'string',
                'description' => 'Adresse e-mail de contact',
                'group' => 'general',
            ],
            [
                'key' => 'default_tax_rate',
                'value' => '18',
                'type' => 'decimal',
                'description' => 'Taux de TVA par défaut (%)',
                'group' => 'sales',
            ],
            [
                'key' => 'low_stock_threshold',
                'value' => '10',
                'type' => 'integer',
                'description' => 'Seuil minimum de stock pour alertes',
                'group' => 'inventory',
            ],
            [
                'key' => 'expiry_alert_days',
                'value' => '90',
                'type' => 'integer',
                'description' => 'Nombre de jours avant expiration pour alerte',
                'group' => 'inventory',
            ],
            [
                'key' => 'auto_reorder',
                'value' => 'false',
                'type' => 'boolean',
                'description' => 'Activer la commande automatique',
                'group' => 'inventory',
            ],
            [
                'key' => 'backup_frequency',
                'value' => 'daily',
                'type' => 'string',
                'description' => 'Fréquence des sauvegardes automatiques',
                'group' => 'general',
            ],
            [
                'key' => 'currency',
                'value' => 'FCFA',
                'type' => 'string',
                'description' => 'Devise utilisée par le système',
                'group' => 'general',
            ],
        ];

        foreach ($settings as $setting) {
            SystemSetting::updateOrCreate(
                ['key' => $setting['key']], // Évite les doublons si déjà présent
                $setting
            );
        }
    }
}
