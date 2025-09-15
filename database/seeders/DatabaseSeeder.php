<?php
// 📊 DATABASE SEEDER PRINCIPAL
// database/seeders/DatabaseSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

// 🔧 Seeders globaux
use Database\Seeders\SystemSettingSeeder;

// 👤 Auth & permissions
use Database\Seeders\RoleSeeder;
use Database\Seeders\UserSeeder;

// 📑 Données de base
use Database\Seeders\FamilySeeder;
use Database\Seeders\SupplierSeeder;
use Database\Seeders\WarehouseSeeder;

// 📦 Données métier
use Database\Seeders\ProductSeeder;
use Database\Seeders\CustomerSeeder;

// ⚠️ Optionnels / transactionnels
// use Database\Seeders\StockSeeder;
// use Database\Seeders\SalesSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Liste des seeders principaux à exécuter.
     */
    private const BASE_SEEDERS = [
        // 1️⃣ Paramètres globaux
        SystemSettingSeeder::class,

        // 2️⃣ Rôles Spatie
        RoleSeeder::class,

        // 3️⃣ Utilisateurs
        UserSeeder::class,

        // 4️⃣ Données de référence
        FamilySeeder::class,
        SupplierSeeder::class,
        WarehouseSeeder::class,

        // 5️⃣ Données métier
        ProductSeeder::class,
        CustomerSeeder::class,
    ];

    public function run(): void
    {
        // 1️⃣ Seeders principaux
        $this->call(self::BASE_SEEDERS);

        // 2️⃣ Seeders transactionnels (optionnels en dev)
        // $this->call([
        //     StockSeeder::class,
        //     SalesSeeder::class,
        // ]);
    }
}
