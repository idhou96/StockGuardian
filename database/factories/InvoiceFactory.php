<?php
// 🧾 INVOICE SEEDER
// database/seeders/InvoiceSeeder.php

namespace Database\Seeders;

use App\Models\Invoice;
use App\Models\Customer;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Database\Seeder;

class InvoiceSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $customers = Customer::all();

        if ($customers->isEmpty()) {
            $customers = Customer::factory(50)->create();
        }

        if ($users->isEmpty()) {
            $users = User::factory(10)->create();
        }

        // Créer 100 factures
        for ($i = 0; $i < 100; $i++) {
            $customer = $customers->random();
            $user = $users->random();

            // Générer la facture
            $invoice = Invoice::factory()->for($customer)
                                        ->for($user)
                                        ->create();

            // Assigner un statut aléatoire
            $statusOptions = ['draft', 'sent', 'paid', 'overdue'];
            $status = $statusOptions[array_rand($statusOptions)];

            switch ($status) {
                case 'paid':
                    $invoice->paid();
                    break;
                case 'overdue':
                    $invoice->overdue();
                    break;
                default:
                    $invoice->update(['status' => $status]);
            }

            // Associer éventuellement une vente existante ou nouvelle
            if (rand(0, 1)) {
                $saleId = Sale::inRandomOrder()->first()?->id ?? Sale::factory()->create()->id;
                $invoice->update(['sale_id' => $saleId]);
            }
        }
    }
}
