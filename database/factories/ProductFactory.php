<?php
// 📦 PRODUCT FACTORY  
// database/factories/ProductFactory.php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Family;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $products = [
            'Doliprane 1000mg', 'Ibuprofène 400mg', 'Amoxicilline 500mg', 
            'Paracétamol 500mg', 'Aspirine 100mg', 'Vitamine C 1000mg',
            'Calcium + D3', 'Oméprazole 20mg', 'Simvastatine 20mg',
            'Metformine 850mg', 'Losartan 50mg', 'Amlodipine 5mg'
        ];

        $price = $this->faker->randomFloat(2, 500, 50000);

        // Assurer que maximum_stock est supérieur à minimum_stock
        $minStock = $this->faker->numberBetween(5, 50);
        $maxStock = $this->faker->numberBetween($minStock + 50, $minStock + 1000);

        return [
            'name' => $this->faker->randomElement($products) . ' - ' . $this->faker->randomElement(['Boîte 30cp', 'Boîte 20cp', 'Flacon 100ml', 'Tube 50g']),
            'code' => strtoupper($this->faker->unique()->lexify('PRD??????')),
            'barcode' => $this->faker->unique()->ean13(),
            'description' => $this->faker->text(200),
            'family_id' => Family::factory(),
            'supplier_id' => Supplier::factory(),
            'purchase_price' => $price,
            'selling_price' => $price * $this->faker->randomFloat(2, 1.2, 2.5),
            'margin_percentage' => round((($price * $this->faker->randomFloat(2, 1.2, 2.5) - $price) / $price) * 100, 2),
            'tax_rate' => $this->faker->randomElement([0, 5.5, 10, 20]),
            'minimum_stock' => $minStock,
            'maximum_stock' => $maxStock,
            'reorder_point' => $this->faker->numberBetween($minStock, $maxStock / 2),
            'unit' => $this->faker->randomElement(['pièce', 'boîte', 'flacon', 'tube', 'sachet']),
            'weight' => $this->faker->randomFloat(2, 0.1, 2.0),
            'volume' => $this->faker->randomFloat(2, 0.05, 1.0),
            'expiry_alert_days' => $this->faker->numberBetween(30, 180),
            'batch_tracking' => $this->faker->boolean(70),
            'prescription_required' => $this->faker->boolean(40),
            'is_active' => $this->faker->boolean(90),
            'image' => $this->faker->optional()->imageUrl(300, 300, 'medicine', true),
            'notes' => $this->faker->optional()->text(150),
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    public function prescription(): static
    {
        return $this->state(fn (array $attributes) => [
            'prescription_required' => true,
        ]);
    }

    public function otc(): static // Over The Counter
    {
        return $this->state(fn (array $attributes) => [
            'prescription_required' => false,
        ]);
    }
}
