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
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->string('label');
            $table->foreignId('warehouse_id')->constrained('warehouses')->onDelete('cascade');
            $table->date('inventory_date');
            $table->enum('status', ['en_cours', 'termine', 'valide'])->default('en_cours');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('validated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('validated_at')->nullable();
            $table->text('notes')->nullable();
            $table->decimal('theoretical_value', 15, 2)->default(0);
            $table->decimal('physical_value', 15, 2)->default(0);
            $table->decimal('variance_value', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};