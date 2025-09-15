<?php
// 🧪 ACTIVE PRINCIPLE FACTORY  
// database/factories/ActivePrincipleFactory.php

namespace Database\Factories;

use App\Models\ActivePrinciple;
use Illuminate\Database\Eloquent\Factories\Factory;

class ActivePrincipleFactory extends Factory
{
    protected $model = ActivePrinciple::class;

    public function definition(): array
    {
        $principles = [
            'Paracétamol', 'Ibuprofène', 'Amoxicilline', 'Aspirine', 'Doliprane',
            'Vitamine C', 'Vitamine D3', 'Calcium', 'Magnésium', 'Zinc',
            'Oméprazole', 'Simvastatine', 'Atorvastatine', 'Metformine', 'Lisinopril',
            'Amlodipine', 'Losartan', 'Enalapril', 'Hydrochlorothiazide', 'Furosémide'
        ];

        return [
            'name' => $this->faker->randomElement($principles), // enlever unique() si +20 entrées
            'description' => $this->faker->text(150),
            'dosage_form' => $this->faker->randomElement(['Comprimé', 'Gélule', 'Sirop', 'Injectable', 'Pommade', 'Suppositoire']),
            'therapeutic_class' => $this->faker->randomElement(['Analgésique', 'Anti-inflammatoire', 'Antibiotique', 'Vitamine', 'Cardiovasculaire']),
            'is_active' => $this->faker->boolean(95),
        ];
    }
}
