<?php
// 👥 CUSTOMER SEEDER COMPLET
// database/seeders/CustomerSeeder.php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        // ---------------------------------------------
        // Clients aléatoires génériques
        // ---------------------------------------------
        Customer::factory(50)->create();

        // ---------------------------------------------
        // Clients entreprises (category = 'groupe' ou 'assurance')
        // ---------------------------------------------
        Customer::factory(15)
            ->state(fn (array $attributes) => [
                'category' => 'groupe',
            ])
            ->create();

        // ---------------------------------------------
        // Clients individuels (category = 'particulier')
        // ---------------------------------------------
        Customer::factory(35)
            ->state(fn (array $attributes) => [
                'category' => 'particulier',
            ])
            ->create();
    }
}
