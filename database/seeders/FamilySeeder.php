<?php
// 🏷️ FAMILY SEEDER COMPLET
// database/seeders/FamilySeeder.php

namespace Database\Seeders;

use App\Models\Family;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class FamilySeeder extends Seeder
{
    public function run(): void
    {
        $families = [
            ['name' => 'Médicaments génériques', 'code' => 'GENERIC', 'description' => 'Médicaments génériques de toutes spécialités'],
            ['name' => 'Médicaments de marque', 'code' => 'BRAND', 'description' => 'Médicaments de marque et princeps'],
            ['name' => 'Suppléments nutritionnels', 'code' => 'SUPPL', 'description' => 'Vitamines, minéraux et compléments alimentaires'],
            ['name' => 'Produits cosmétiques', 'code' => 'COSMET', 'description' => 'Cosmétiques et produits de beauté'],
            ['name' => 'Matériel médical', 'code' => 'MEDICAL', 'description' => 'Dispositifs et matériel médical'],
            ['name' => 'Hygiène et soins', 'code' => 'HYGIENE', 'description' => 'Produits d\'hygiène corporelle'],
            ['name' => 'Produits vétérinaires', 'code' => 'VETERIN', 'description' => 'Médicaments et produits vétérinaires'],
            ['name' => 'Homéopathie', 'code' => 'HOMEO', 'description' => 'Produits homéopathiques'],
            ['name' => 'Parapharmacie', 'code' => 'PARAPH', 'description' => 'Produits de parapharmacie'],
            ['name' => 'Dispositifs médicaux', 'code' => 'DEVICE', 'description' => 'Dispositifs médicaux et équipements'],
        ];

        foreach ($families as $family) {
            Family::updateOrCreate(
                ['code' => $family['code']], // clé unique
                array_merge($family, [
                    'is_active'   => true,
                    'created_at'  => Carbon::now(),
                    'updated_at'  => Carbon::now(),
                ])
            );
        }

        // Créer quelques familles supplémentaires avec factory
        Family::factory(5)->create();
    }
}
