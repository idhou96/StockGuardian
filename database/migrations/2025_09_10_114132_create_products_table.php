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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            
            // Identifiants
            $table->string('code')->unique();
            $table->string('barcode')->nullable()->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('family_id')->nullable()->constrained('families')->onDelete('set null');
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->onDelete('set null');

            // Prix
            $table->decimal('purchase_price', 10, 2)->default(0);
            $table->decimal('selling_price', 10, 2)->default(0);
            $table->decimal('wholesale_price', 10, 2)->default(0);
            $table->decimal('margin_percentage', 5, 2)->default(0);

            // TVA
            $table->decimal('tax_rate', 5, 2)->default(18.00);
            $table->boolean('apply_tax')->default(true);

            // Stock
            $table->integer('current_stock')->default(0);
            $table->integer('minimum_stock')->default(0);
            $table->integer('maximum_stock')->default(0);
            $table->integer('reorder_point')->default(0);
            $table->integer('security_stock')->default(0);

            // Unité et caractéristiques physiques
            $table->string('unit')->default('Boîte');
            $table->decimal('weight', 8, 2)->nullable();
            $table->decimal('volume', 8, 2)->nullable();

            // Dates et lots
            $table->date('expiry_date')->nullable();
            $table->integer('expiry_alert_days')->nullable();
            $table->string('batch_number')->nullable();
            $table->boolean('batch_tracking')->default(false);

            // Propriétés spéciales
            $table->boolean('is_dangerous')->default(false);
            $table->boolean('is_pharmaceutical')->default(true);
            $table->boolean('is_consumable')->default(true);
            $table->boolean('is_mixed')->default(false);
            $table->boolean('prescription_required')->default(false);

            // Codes géographiques et autres
            $table->string('geographic_code')->default('HALL 1');

            // Remises
            $table->decimal('discount_percentage', 5, 2)->default(0);
            $table->decimal('wholesale_discount', 5, 2)->default(0);

            // Images et notes
            $table->string('image')->nullable();
            $table->text('notes')->nullable();

            // Statut
            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes(); // deleted_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
