<?php
namespace Database\Factories;

use App\Models\Inventory;
use App\Models\Warehouse;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class InventoryFactory extends Factory
{
    protected $model = Inventory::class;

    public function definition(): array
    {
        $totalItems = $this->faker->numberBetween(50, 500);

        return [
            'inventory_number' => 'INV-' . date('Y') . '-' . str_pad($this->faker->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            'warehouse_id' => Warehouse::factory(),
            'user_id' => User::factory(),
            'inventory_date' => $this->faker->dateTimeBetween('-3 months', 'now'),
            'type' => $this->faker->randomElement(['full', 'partial', 'cycle']),
            'status' => $this->faker->randomElement(['draft', 'in_progress', 'completed', 'validated']),
            'total_items' => $totalItems,
            'checked_items' => $this->faker->numberBetween(0, $totalItems),
            'discrepancies' => $this->faker->numberBetween(0, 20),
            'notes' => $this->faker->optional()->text(200),
        ];
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'checked_items' => $attributes['total_items'],
        ]);
    }
}
