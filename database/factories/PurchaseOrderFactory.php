<?php
// 🛒 PURCHASE ORDER FACTORY
// database/factories/PurchaseOrderFactory.php

namespace Database\Factories;

use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

class PurchaseOrderFactory extends Factory
{
    protected $model = PurchaseOrder::class;

    public function definition(): array
    {
        $subtotal = $this->faker->randomFloat(2, 50000, 2000000);
        $discountPercent = $this->faker->randomFloat(2, 0, 10);
        $discountAmount = round($subtotal * ($discountPercent / 100), 2);
        $taxAmount = round(($subtotal - $discountAmount) * 0.18, 2);
        $total = round($subtotal - $discountAmount + $taxAmount, 2);

        $orderDate = $this->faker->dateTimeBetween('-3 months', 'now');

        return [
            'order_number' => 'CMD-' . date('Y') . '-' . str_pad($this->faker->unique()->numberBetween(1, 99999), 5, '0', STR_PAD_LEFT),
            'supplier_id' => Supplier::factory(),
            'user_id' => User::factory(),
            'warehouse_id' => Warehouse::factory(),
            'order_date' => $orderDate,
            'expected_delivery_date' => $this->faker->dateTimeBetween($orderDate, '+2 months'),
            'subtotal' => $subtotal,
            'discount_percentage' => $discountPercent,
            'discount_amount' => $discountAmount,
            'tax_amount' => $taxAmount,
            'total_amount' => $total,
            'status' => $this->faker->randomElement(['draft', 'sent', 'confirmed', 'partial', 'received', 'cancelled']),
            'notes' => $this->faker->optional()->text(200),
        ];
    }

    public function draft(): static
    {
        return $this->state(fn () => ['status' => 'draft']);
    }

    public function sent(): static
    {
        return $this->state(fn () => ['status' => 'sent']);
    }

    public function confirmed(): static
    {
        return $this->state(fn () => ['status' => 'confirmed']);
    }

    public function partial(): static
    {
        return $this->state(fn () => ['status' => 'partial']);
    }

    public function received(): static
    {
        return $this->state(fn () => ['status' => 'received']);
    }

    public function cancelled(): static
    {
        return $this->state(fn () => ['status' => 'cancelled']);
    }
}
