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
        Schema::create('stock_outages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('warehouse_id')->constrained('warehouses')->onDelete('cascade');
            $table->date('outage_date');
            $table->date('expected_restock_date')->nullable();
            $table->date('actual_restock_date')->nullable();
            $table->enum('status', [
                'en_rupture',
                'commande_passee',
                'en_cours_livraison',
                'resolu'
            ])->default('en_rupture');
            $table->integer('quantity_needed')->default(0);
            $table->integer('quantity_ordered')->default(0);
            $table->foreignId('reported_by')->constrained('users')->onDelete('cascade');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->unique(['product_id', 'warehouse_id', 'outage_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_outages');
    }
};