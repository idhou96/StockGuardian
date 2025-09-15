<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Supplier;
use Faker\Factory as Faker;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        $faker->unique(true); // Réinitialise la mémoire des valeurs uniques

        // Nombre de fournisseurs à créer
        $count = 20;

        for ($i = 0; $i < $count; $i++) {
            Supplier::firstOrCreate(
                ['code' => 'SUP' . $faker->numerify('######')], // code unique
                [
                    'name' => $faker->company,
                    'email' => $faker->unique()->safeEmail,
                    'phone' => $faker->phoneNumber,
                    'address' => $faker->address,
                    'contact_person' => $faker->name,
                    'payment_terms' => $faker->randomElement(['30 jours', '60 jours', '90 jours']),
                    'credit_limit' => $faker->randomFloat(2, 1000, 50000),
                    'rating' => $faker->randomElement(['A', 'B', 'C']),
                    'is_active' => $faker->boolean(90),
                    'tax_number' => $faker->numerify('############'),
                ]
            );
        }
    }
}
