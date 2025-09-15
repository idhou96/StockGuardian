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
        Schema::create('return_note_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('return_note_id')->constrained('return_notes')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->integer('quantity_returned');
            $table->integer('quantity_accepted')->default(0);
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_ht', 15, 2);
            $table->decimal('total_tax', 15, 2);
            $table->decimal('total_ttc', 15, 2);
            $table->date('expiry_date')->nullable();
            $table->string('batch_number')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('return_note_details');
    }
};