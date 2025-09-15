<?php
// 🏢 WAREHOUSE SEEDER CORRIGÉ
// database/seeders/WarehouseSeeder.php

namespace Database\Seeders;

use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class WarehouseSeeder extends Seeder
{
    public function run(): void
    {
        $warehouses = [
            [
                'name' => 'Dépôt Principal',
                'code' => 'MAIN',
                'address' => 'Zone Industrielle Yopougon, Abidjan',
                'manager_name' => 'M. Ouattara Ibrahim',
                'contact_phone' => '+225 21 47 85 96',
                'city' => 'Abidjan',
                'type' => 'principal',
                'is_active' => true,
            ],
            [
                'name' => 'Dépôt Cocody',
                'code' => 'COC',
                'address' => '2 Plateaux, Vallon, Cocody',
                'manager_name' => 'Mme Koné Adjoua',
                'contact_phone' => '+225 21 22 45 67',
                'city' => 'Abidjan',
                'type' => 'secondaire',
                'is_active' => true,
            ],
            [
                'name' => 'Dépôt Marcory',
                'code' => 'MAR',
                'address' => 'Marcory Zone 4, près du marché',
                'manager_name' => 'M. Diabate Sekou',
                'contact_phone' => '+225 21 35 78 45',
                'city' => 'Abidjan',
                'type' => 'secondaire',
                'is_active' => true,
            ],
            [
                'name' => 'Magasin Plateau',
                'code' => 'PLT',
                'address' => 'Plateau, Avenue Houphouët-Boigny',
                'manager_name' => 'Mlle Bamba Grace',
                'contact_phone' => '+225 21 20 15 34',
                'city' => 'Abidjan',
                'type' => 'reserve',
                'is_active' => true,
            ],
        ];

        foreach ($warehouses as $warehouse) {
            Warehouse::updateOrCreate(
                ['code' => $warehouse['code']],
                $warehouse
            );
        }

        $this->command->info('🏬 Entrepôts créés avec succès !');
    }
}
