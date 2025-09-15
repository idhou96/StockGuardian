<?php
// 📊 WAREHOUSE STOCK FACTORY
// database/factories/WarehouseStockFactory.php

namespace Database\Factories;

use App\Models\WarehouseStock;
use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

class WarehouseStockFactory extends Factory
{
    protected $model = WarehouseStock::class;

    public function definition(): array
    {
        $quantity = $this->faker->numberBetween(0, 500);
        $reserved = $this->faker->numberBetween(0, min($quantity, 50));
        $available = $quantity - $reserved;

        return [
            'product_id' => Product::factory(),
            'warehouse_id' => Warehouse::factory(),
            'quantity' => $quantity,
            'reserved_quantity' => $reserved,
            'available_quantity' => $available,
            'last_updated' => $this->faker->dateTimeBetween('-30 days', 'now'),
        ];
    }

    public function lowStock(): static
    {
        return $this->state(function (array $attributes) {
            $quantity = rand(1, 10);
            $reserved = rand(0, min($quantity, 5));
            $available = $quantity - $reserved;

            return [
                'quantity' => $quantity,
                'reserved_quantity' => $reserved,
                'available_quantity' => $available,
            ];
        });
    }

    public function outOfStock(): static
    {
        return $this->state(function () {
            return [
                'quantity' => 0,
                'reserved_quantity' => 0,
                'available_quantity' => 0,
            ];
        });
    }
}
