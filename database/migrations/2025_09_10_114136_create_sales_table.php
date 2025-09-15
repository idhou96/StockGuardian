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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->string('ticket_number')->unique();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->onDelete('set null');
            $table->foreignId('warehouse_id')->constrained('warehouses')->onDelete('cascade');
            $table->date('sale_date');
            $table->time('sale_time');
            $table->enum('type', [
                'caisse',
                'vente_differee',
                'proforma',
                'assurance',
                'depot'
            ])->default('caisse');
            $table->enum('status', [
                'en_cours',
                'validee',
                'partiellement_payee',
                'payee',
                'annulee'
            ])->default('en_cours');
            $table->decimal('total_ht', 15, 2)->default(0);
            $table->decimal('total_tax', 15, 2)->default(0);
            $table->decimal('total_ttc', 15, 2)->default(0);
            $table->decimal('total_discount', 15, 2)->default(0);
            $table->decimal('amount_paid', 15, 2)->default(0);
            $table->decimal('amount_due', 15, 2)->default(0);
            $table->decimal('change_given', 15, 2)->default(0);
            $table->enum('payment_method', [
                'especes',
                'cheque', 
                'virement',
                'carte',
                'credit',
                'assurance'
            ])->default('especes');
            $table->foreignId('cashier_id')->constrained('users')->onDelete('cascade');
            $table->string('customer_name')->nullable();
            $table->string('customer_phone')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes(); // crée la colonne deleted_at

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};