<?php
// 💳 PAYMENT FACTORY
// database/factories/PaymentFactory.php

namespace Database\Factories;

use App\Models\Payment;
use App\Models\Invoice;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        // Sélection aléatoire d'une facture ou d'une vente
        $invoiceIds = Invoice::pluck('id')->toArray();
        $saleIds = Sale::pluck('id')->toArray();
        
        // Déterminer le montant basé sur la facture ou vente si disponible
        $relatedInvoice = $invoiceIds ? Invoice::find($this->faker->randomElement($invoiceIds)) : null;
        $amount = $relatedInvoice ? $relatedInvoice->total_amount : $this->faker->randomFloat(2, 1000, 100000);
        
        return [
            'payment_number' => 'PAY-' . date('Y') . '-' . str_pad($this->faker->unique()->numberBetween(1, 99999), 5, '0', STR_PAD_LEFT),
            'invoice_id' => $this->faker->optional(0.7)->randomElement($invoiceIds),
            'sale_id' => $this->faker->optional(0.3)->randomElement($saleIds),
            'user_id' => User::inRandomOrder()->first()->id ?? User::factory(),
            'payment_date' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'amount' => $amount,
            'payment_method' => $this->faker->randomElement(['cash', 'bank_transfer', 'card', 'mobile_money', 'check']),
            'reference' => $this->faker->optional(0.6)->regexify('[A-Z0-9]{10}'),
            'status' => $this->faker->randomElement(['pending', 'completed', 'failed', 'cancelled']),
            'notes' => $this->faker->optional()->text(100),
        ];
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'failed',
        ]);
    }
}
