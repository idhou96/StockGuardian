<?php
// 💰 SALE FACTORY
// database/factories/SaleFactory.php

namespace Database\Factories;

use App\Models\Sale;
use App\Models\Customer;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

class SaleFactory extends Factory
{
    protected $model = Sale::class;

    public function definition(): array
    {
        $subtotal = round($this->faker->randomFloat(2, 1000, 100000), 2);
        $discountPercent = round($this->faker->randomFloat(2, 0, 15), 2);
        $discountAmount = round($subtotal * ($discountPercent / 100), 2);
        $taxAmount = round(($subtotal - $discountAmount) * 0.18, 2); // TVA 18%
        $total = round($subtotal - $discountAmount + $taxAmount, 2);

        $status = $this->faker->randomElement(['pending', 'completed', 'cancelled']);
        $paidAmount = match($status) {
            'completed' => $total,
            'pending' => 0,
            'cancelled' => 0,
        };

        return [
            'sale_number' => 'VNT-' . date('Y') . '-' . str_pad($this->faker->unique()->numberBetween(1, 99999), 5, '0', STR_PAD_LEFT),
            'customer_id' => Customer::inRandomOrder()->first()?->id,
            'user_id' => User::factory(),
            'warehouse_id' => Warehouse::factory(),
            'sale_date' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'subtotal' => $subtotal,
            'discount_percentage' => $discountPercent,
            'discount_amount' => $discountAmount,
            'tax_amount' => $taxAmount,
            'total_amount' => $total,
            'paid_amount' => $paidAmount,
            'payment_method' => $this->faker->randomElement(['cash', 'card', 'mobile', 'credit']),
            'status' => $status,
            'notes' => $this->faker->optional()->text(100),
        ];
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'paid_amount' => $attributes['total_amount'],
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'paid_amount' => 0,
        ]);
    }
}
