<?php
namespace Database\Factories;

use App\Models\StockMovement;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\User;
use App\Models\Sale;
use App\Models\PurchaseOrder;
use App\Models\Inventory;
use Illuminate\Database\Eloquent\Factories\Factory;

class StockMovementFactory extends Factory
{
    protected $model = StockMovement::class;

    public function definition(): array
    {
        $types = ['in', 'out', 'transfer', 'adjustment'];
        $type = $this->faker->randomElement($types);
        $quantity = $this->faker->numberBetween(1, 100);

        $reasons = [
            'in' => ['purchase', 'return', 'adjustment', 'transfer_in'],
            'out' => ['sale', 'wastage', 'return', 'transfer_out', 'expired'],
            'transfer' => ['restock', 'warehouse_change'],
            'adjustment' => ['inventory', 'correction', 'damage']
        ];

        $referenceType = $this->faker->randomElement(['Sale', 'PurchaseOrder', 'Inventory', 'Manual']);
        $referenceId = match($referenceType) {
            'Sale' => Sale::inRandomOrder()->first()?->id ?? 1,
            'PurchaseOrder' => PurchaseOrder::inRandomOrder()->first()?->id ?? 1,
            'Inventory' => Inventory::inRandomOrder()->first()?->id ?? 1,
            default => $this->faker->numberBetween(1, 1000),
        };

        $unitCost = round($this->faker->randomFloat(2, 500, 20000), 2);
        $qty = $type === 'out' ? -$quantity : $quantity;

        return [
            'product_id' => Product::factory(),
            'warehouse_id' => Warehouse::factory(),
            'type' => $type,
            'quantity' => $qty,
            'reason' => $this->faker->randomElement($reasons[$type]),
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'user_id' => User::factory(),
            'batch_number' => $this->faker->optional(0.5)->regexify('[A-Z0-9]{8}'),
            'expiry_date' => $this->faker->optional(0.5)->dateTimeBetween('+1 month', '+2 years'),
            'unit_cost' => $unitCost,
            'total_cost' => abs($qty) * $unitCost,
            'notes' => $this->faker->optional()->text(100),
            'movement_date' => $this->faker->dateTimeBetween('-6 months', 'now'),
        ];
    }

    public function incoming(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'in',
            'quantity' => abs($attributes['quantity']),
            'reason' => $this->faker->randomElement(['purchase', 'return', 'adjustment']),
        ]);
    }

    public function outgoing(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'out',
            'quantity' => -abs($attributes['quantity']),
            'reason' => $this->faker->randomElement(['sale', 'wastage', 'expired']),
        ]);
    }
}
