<?php

namespace Database\Factories;

use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

class SupplierFactory extends Factory
{
    protected $model = Supplier::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company,
            'email' => $this->faker->unique()->companyEmail,
            'phone' => $this->faker->phoneNumber,
            'address' => $this->faker->address,
            'contact_person' => $this->faker->name,
            'payment_terms' => $this->faker->randomElement(['30 jours', '45 jours', '60 jours']),
            'credit_limit' => $this->faker->numberBetween(1000000, 5000000),
            'rating' => $this->faker->randomElement(['A', 'B', 'C']),
            'is_active' => true,
            'tax_number' => $this->faker->unique()->numerify('#############'),
            'code' => 'SUP' . $this->faker->unique()->numberBetween(1000, 9999),
        ];
    }

    public function active()
    {
        return $this->state(fn () => ['is_active' => true]);
    }

    public function inactive()
    {
        return $this->state(fn () => ['is_active' => false]);
    }

    public function premium()
    {
        return $this->state(fn () => ['rating' => 'A', 'credit_limit' => 5000000]);
    }
}
