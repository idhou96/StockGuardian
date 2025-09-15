<?php
// 📦 SEEDERS ADDITIONNELS
// database/seeders/ProductSeeder.php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Family;
use App\Models\Supplier;
use App\Models\ActivePrinciple;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder 
{
    public function run(): void
    {
        // Vérifier qu'il existe des familles et fournisseurs,
        // sinon en créer quelques-uns
        if (Family::count() === 0) {
            Family::factory(5)->create();
        }

        if (Supplier::count() === 0) {
            Supplier::factory(5)->create();
        }

        // Créer des principes actifs si aucun n'existe
        if (ActivePrinciple::count() === 0) {
            ActivePrinciple::factory(20)->create();
        }

        // Créer des produits
        Product::factory(100)->create()->each(function ($product) {
            // Associer famille et fournisseur aléatoires si non défini
            if (!$product->family_id) {
                $product->family_id = Family::inRandomOrder()->first()->id;
                $product->save();
            }

            if (!$product->supplier_id) {
                $product->supplier_id = Supplier::inRandomOrder()->first()->id;
                $product->save();
            }

            // Associer aléatoirement 1 à 3 principes actifs
            $principles = ActivePrinciple::inRandomOrder()->take(rand(1, 3))->pluck('id');
            $product->activePrinciples()->syncWithoutDetaching($principles);
        });
    }
}
