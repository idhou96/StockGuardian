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
        Schema::create('inventory_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_id')->constrained('inventories')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->integer('theoretical_quantity')->default(0);
            $table->integer('physical_quantity')->default(0);
            $table->integer('variance_quantity')->default(0);
            $table->decimal('unit_price', 10, 2)->default(0);
            $table->decimal('theoretical_value', 15, 2)->default(0);
            $table->decimal('physical_value', 15, 2)->default(0);
            $table->decimal('variance_value', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->unique(['inventory_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_details');
    }
};