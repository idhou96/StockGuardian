<?php

namespace Database\Factories;

use App\Models\Family;
use Illuminate\Database\Eloquent\Factories\Factory;

class FamilyFactory extends Factory
{
    protected $model = Family::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word(),
            'code' => $this->generateUniqueCode(),
            'description' => $this->faker->sentence(10),
            'is_active' => $this->faker->boolean(90),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Génère un code unique pour la famille
     */
    private function generateUniqueCode(): string
    {
        do {
            $code = strtoupper($this->faker->lexify('FAM???')); // FAM + 3 lettres aléatoires
        } while (Family::where('code', $code)->exists());

        return $code;
    }
}
