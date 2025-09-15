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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('first_name')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->default('Côte d\'Ivoire');

            // Catégories de clients
            $table->enum('category', ['particulier','groupe','assurance','depot'])->default('particulier');

            // Mode de suivi
            $table->enum('tracking_mode', ['global','by_member'])->default('global');

            // Limites financières
            $table->decimal('credit_limit', 15, 2)->default(0);
            $table->decimal('current_balance', 15, 2)->default(0);

            // Informations d'assurance
            $table->string('insurance_number')->nullable();
            $table->string('insurance_company')->nullable();
            $table->decimal('coverage_percentage', 5, 2)->default(0);

            // Informations supplémentaires pour le seeder
            $table->integer('loyalty_points')->default(0);
            $table->date('birth_date')->nullable();
            $table->text('notes')->nullable();

            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
