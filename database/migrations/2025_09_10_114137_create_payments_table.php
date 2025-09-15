<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->onDelete('cascade');
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->onDelete('cascade');
            $table->foreignId('invoice_id')->nullable()->constrained('invoices')->onDelete('set null');
            $table->foreignId('sale_id')->nullable()->constrained('sales')->onDelete('set null');
            $table->date('payment_date');
            $table->decimal('amount', 15, 2);
            $table->enum('type', [
                'encaissement',
                'decaissement',
                'recouvrement'
            ])->default('encaissement');
            $table->enum('method', [
                'especes',
                'cheque',
                'virement',
                'carte_bancaire',
                'mobile_money'
            ])->default('especes');
            $table->string('reference_number')->nullable(); // Numéro chèque, virement, etc.
            $table->enum('status', [
                'en_attente',
                'valide',
                'rejete',
                'annule'
            ])->default('valide');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};