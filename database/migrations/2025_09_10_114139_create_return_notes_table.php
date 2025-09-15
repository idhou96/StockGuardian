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
        Schema::create('return_notes', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('supplier_id')->constrained('suppliers')->onDelete('cascade');
            $table->foreignId('warehouse_id')->constrained('warehouses')->onDelete('cascade');
            $table->foreignId('delivery_note_id')->nullable()->constrained('delivery_notes')->onDelete('set null');
            $table->date('return_date');
            $table->enum('reason', [
                'produit_defectueux',
                'produit_perime',
                'erreur_livraison',
                'surplus_commande',
                'autre'
            ]);
            $table->enum('status', [
                'prepare',
                'envoye',
                'accepte',
                'refuse',
                'partiellement_accepte'
            ])->default('prepare');
            $table->decimal('total_ht', 15, 2)->default(0);
            $table->decimal('total_tax', 15, 2)->default(0);
            $table->decimal('total_ttc', 15, 2)->default(0);
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
        Schema::dropIfExists('return_notes');
    }
};