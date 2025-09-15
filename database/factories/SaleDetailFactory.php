<?php
// 🛍️ SALE DETAIL FACTORY
// database/factories/SaleDetailFactory.php

namespace Database\Factories;

use App\Models\SaleDetail;
use App\Models\Sale;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class SaleDetailFactory extends Factory
{
    protected $model = SaleDetail::class;

    public function definition(): array
    {
        $quantity = $this->faker->numberBetween(1, 10);
        $unitPrice = $this->faker->randomFloat(2, 500, 25000);
        $subtotal = round($quantity * $unitPrice, 2);
        $discountPercent = $this->faker->randomFloat(2, 0, 10);
        $discountAmount = round($subtotal * ($discountPercent / 100), 2);
        $total = round($subtotal - $discountAmount, 2);

        return [
            'sale_id' => Sale::factory(),
            'product_id' => Product::factory(),
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'discount_percentage' => $discountPercent,
            'discount_amount' => $discountAmount,
            'subtotal' => $subtotal,
            'total' => $total,
            'batch_number' => $this->faker->optional(0.6)->regexify('[A-Z0-9]{8}'),
            'expiry_date' => $this->faker->optional(0.6)->dateTimeBetween('now', '+2 years'),
        ];
    }
}
