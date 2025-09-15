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
        Schema::create('regularizations', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->string('label');
            $table->foreignId('warehouse_id')->constrained('warehouses')->onDelete('cascade');
            $table->date('regularization_date');
            $table->enum('type', [
                'ajustement_positif',
                'ajustement_negatif',
                'correction_erreur',
                'perte',
                'vol',
                'deterioration'
            ]);
            $table->enum('status', [
                'brouillon',
                'valide',
                'annule'
            ])->default('brouillon');
            $table->decimal('total_value', 15, 2)->default(0);
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('validated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('validated_at')->nullable();
            $table->text('reason')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('regularizations');
    }
};