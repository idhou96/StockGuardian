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
        Schema::create('active_principles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('dosage_form')->nullable();          // Forme galénique (sirop, injectable, etc.)
            $table->string('therapeutic_class')->nullable();    // Classe thérapeutique
            $table->boolean('is_active')->default(true);        // Actif ou non
            $table->timestamps();
            $table->softDeletes();                              // Colonne deleted_at nullable
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('active_principles');
    }
};
