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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id(); // Clé primaire auto-incrémentée (UNSIGNED BIGINT)
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade'); // Clé étrangère vers orders, suppression en cascade
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade'); // Clé étrangère vers products, suppression en cascade
            $table->decimal('quantity', 8, 2); // Quantité commandée du produit
            $table->string('sale_unit_at_order'); // Unité de vente au moment de la commande
            $table->decimal('unit_price_at_order', 8, 2); // Prix unitaire du produit au moment de la commande
            $table->timestamps(); // Colonnes created_at et updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
