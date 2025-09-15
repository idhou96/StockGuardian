<?php
namespace Database\Factories;

use App\Models\InventoryDetail;
use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class InventoryDetailFactory extends Factory
{
    protected $model = InventoryDetail::class;

    public function definition(): array
    {
        $systemQuantity = $this->faker->numberBetween(0, 200);
        $physicalQuantity = $systemQuantity + $this->faker->numberBetween(-20, 20);
        $variance = $physicalQuantity - $systemQuantity;
        $unitCost = $this->faker->randomFloat(2, 500, 25000);

        return [
            'inventory_id' => Inventory::factory(),
            'product_id' => Product::factory(),
            'system_quantity' => $systemQuantity,
            'physical_quantity' => $physicalQuantity,
            'variance' => $variance,
            'unit_cost' => $unitCost,
            'variance_value' => $variance * $unitCost,
            'batch_number' => $this->faker->optional(0.5)->regexify('[A-Z0-9]{8}'),
            'expiry_date' => $this->faker->optional(0.5)->dateTimeBetween('+1 month', '+2 years'),
            'notes' => $this->faker->optional()->text(100),
        ];
    }

    public function noVariance(): static
    {
        return $this->state(fn (array $attributes) => [
            'physical_quantity' => $attributes['system_quantity'],
            'variance' => 0,
            'variance_value' => 0,
        ]);
    }
}
