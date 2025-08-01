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
        Schema::create('stocks', function (Blueprint $table) {
            $table->id(); // Clé primaire auto-incrémentée (UNSIGNED BIGINT)
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade'); // Clé étrangère vers products, suppression en cascade
            $table->decimal('quantity', 8, 2); // Quantité actuelle en stock
            $table->enum('movement_type', ['entrée', 'sortie', 'future_recolte']); // Type de mouvement
            $table->string('reference_id')->nullable(); // Référence au mouvement, peut être nul
            $table->decimal('alert_threshold', 8, 2)->nullable(); // Seuil d'alerte pour stock bas, peut être nul
            $table->timestamp('movement_date')->nullable(); // Date du mouvement, peut être nulle
            $table->timestamps(); // Colonnes created_at et updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};
