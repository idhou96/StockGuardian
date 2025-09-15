<?php

namespace Database\Seeders;

use App\Models\SystemSetting;
use Illuminate\Database\Seeder;

/**
 * ⚙️ SYSTEM SETTING SEEDER
 * Injecte les paramètres système par défaut dans la table system_settings.
 */
class SystemSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // Informations entreprise
            ['key' => 'company_name',        'value' => 'StockGuardian Pharmacie',     'type' => 'string',  'group' => 'general',       'description' => 'Nom de la société'],
            ['key' => 'company_address',     'value' => 'Abidjan, Plateau - Côte d\'Ivoire', 'type' => 'string', 'group' => 'general', 'description' => 'Adresse de la société'],
            ['key' => 'company_phone',       'value' => '+225 21 20 30 40 50',        'type' => 'string',  'group' => 'general',       'description' => 'Téléphone de la société'],
            ['key' => 'company_email',       'value' => 'contact@stockguardian.ci',   'type' => 'string',  'group' => 'general',       'description' => 'Email de la société'],
            ['key' => 'company_website',     'value' => 'www.stockguardian.ci',       'type' => 'string',  'group' => 'general',       'description' => 'Site web de la société'],

            // Configuration fiscale
            ['key' => 'default_tax_rate',    'value' => '18',                         'type' => 'decimal', 'group' => 'sales',         'description' => 'Taux de TVA par défaut (%)'],
            ['key' => 'currency',            'value' => 'FCFA',                       'type' => 'string',  'group' => 'general',       'description' => 'Devise par défaut'],
            ['key' => 'currency_symbol',     'value' => 'F CFA',                      'type' => 'string',  'group' => 'general',       'description' => 'Symbole de la devise'],

            // Configuration stock
            ['key' => 'low_stock_threshold', 'value' => '10',                         'type' => 'integer', 'group' => 'inventory',     'description' => 'Seuil stock faible'],
            ['key' => 'expiry_alert_days',   'value' => '90',                         'type' => 'integer', 'group' => 'inventory',     'description' => 'Alerte expiration (jours)'],
            ['key' => 'auto_reorder',        'value' => 'false',                      'type' => 'boolean', 'group' => 'inventory',     'description' => 'Réapprovisionnement automatique'],
            ['key' => 'batch_tracking',      'value' => 'true',                       'type' => 'boolean', 'group' => 'inventory',     'description' => 'Suivi des lots activé'],

            // Configuration ventes
            ['key' => 'invoice_prefix',      'value' => 'FACT',                       'type' => 'string',  'group' => 'sales',         'description' => 'Préfixe numéro facture'],
            ['key' => 'sale_prefix',         'value' => 'VNT',                        'type' => 'string',  'group' => 'sales',         'description' => 'Préfixe numéro vente'],
            ['key' => 'receipt_footer',      'value' => 'Merci de votre visite !',    'type' => 'string',  'group' => 'sales',         'description' => 'Pied de page ticket'],

            // Configuration système
            ['key' => 'backup_frequency',    'value' => 'daily',                      'type' => 'string',  'group' => 'system',        'description' => 'Fréquence sauvegarde'],
            ['key' => 'session_timeout',     'value' => '120',                        'type' => 'integer', 'group' => 'system',        'description' => 'Timeout session (minutes)'],
            ['key' => 'max_login_attempts',  'value' => '5',                          'type' => 'integer', 'group' => 'system',        'description' => 'Tentatives connexion max'],

            // Notifications
            ['key' => 'email_notifications', 'value' => 'true',                       'type' => 'boolean', 'group' => 'notifications', 'description' => 'Notifications email'],
            ['key' => 'sms_notifications',   'value' => 'false',                      'type' => 'boolean', 'group' => 'notifications', 'description' => 'Notifications SMS'],
            ['key' => 'stock_alert_email',   'value' => 'stock@stockguardian.ci',     'type' => 'string',  'group' => 'notifications', 'description' => 'Email alertes stock'],
        ];

        foreach ($settings as $setting) {
            SystemSetting::updateOrCreate(
                ['key' => $setting['key']],  // clé unique
                $setting                     // données à insérer/mettre à jour
            );
        }
    }
}
