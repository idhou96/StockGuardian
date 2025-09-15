<?php
// database/seeders/WarehouseSeeder.php

namespace Database\Seeders;

use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class WarehouseSeeder extends Seeder
{
    public function run(): void
    {
        // Créer le dépôt principal
        Warehouse::factory()->main()->create();

        // Créer d'autres entrepôts secondaires
        Warehouse::factory(5)->create();
    }
}
