<?php
// 👥 CUSTOMER FACTORY
// database/factories/CustomerFactory.php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition(): array
    {
        $categories = ['particulier', 'groupe', 'assurance', 'depot'];
        $category = $this->faker->randomElement($categories);
        
        return [
            'code' => strtoupper($this->faker->unique()->lexify('CUST?????')),
            'name' => $category === 'particulier' ? $this->faker->name() : $this->faker->company(),
            'first_name' => $category === 'particulier' ? $this->faker->firstName() : null,
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            'city' => $this->faker->city(),
            'country' => 'Côte d\'Ivoire',
            'category' => $category,   // Remplacement de 'type' par 'category'
            'tracking_mode' => $this->faker->randomElement(['global', 'by_member']),
            'credit_limit' => $this->faker->randomFloat(2, 0, 50000),
            'current_balance' => $this->faker->randomFloat(2, 0, 50000),
            'insurance_number' => $category !== 'particulier' ? $this->faker->optional()->numerify('INS#######') : null,
            'insurance_company' => $category !== 'particulier' ? $this->faker->optional()->company() : null,
            'coverage_percentage' => $category !== 'particulier' ? $this->faker->randomFloat(2, 0, 100) : 0,
            'is_active' => $this->faker->boolean(90),
            'notes' => $this->faker->optional()->text(150),
        ];
    }

    public function individual(): static
    {
        return $this->state(fn(array $attributes) => [
            'category' => 'particulier',
            'name' => $this->faker->name(),
            'first_name' => $this->faker->firstName(),
            'insurance_number' => null,
            'insurance_company' => null,
            'coverage_percentage' => 0,
        ]);
    }

    public function corporate(): static
    {
        return $this->state(fn(array $attributes) => [
            'category' => 'groupe',
            'name' => $this->faker->company(),
            'first_name' => null,
            'insurance_number' => $this->faker->regexify('[0-9]{13}'),
            'insurance_company' => $this->faker->company(),
            'coverage_percentage' => $this->faker->randomFloat(2, 50, 100),
        ]);
    }
}
