<?php
// 📋 PURCHASE ORDER DETAIL FACTORY
// database/factories/PurchaseOrderDetailFactory.php

namespace Database\Factories;

use App\Models\PurchaseOrderDetail;
use App\Models\PurchaseOrder;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class PurchaseOrderDetailFactory extends Factory
{
    protected $model = PurchaseOrderDetail::class;

    public function definition(): array
    {
        $quantityOrdered = $this->faker->numberBetween(10, 200);
        $quantityReceived = $this->faker->numberBetween(0, $quantityOrdered); // ne peut dépasser la quantité commandée
        $unitPrice = $this->faker->randomFloat(2, 300, 15000);

        $subtotal = round($quantityOrdered * $unitPrice, 2);
        $discountPercent = $this->faker->randomFloat(2, 0, 8);
        $discountAmount = round($subtotal * ($discountPercent / 100), 2);
        $total = round($subtotal - $discountAmount, 2);

        return [
            'purchase_order_id' => PurchaseOrder::factory(),
            'product_id' => Product::factory(),
            'quantity_ordered' => $quantityOrdered,
            'quantity_received' => $quantityReceived,
            'unit_price' => $unitPrice,
            'discount_percentage' => $discountPercent,
            'discount_amount' => $discountAmount,
            'subtotal' => $subtotal,
            'total' => $total,
        ];
    }
}
