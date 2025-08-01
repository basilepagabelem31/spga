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
            $table->id(); // Clé primaire auto-incrémentée (UNSIGNED BIGINT)
            $table->string('name'); // Nom du produit
            $table->text('description')->nullable(); // Description, peut être nulle
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade'); // Clé étrangère vers categories, suppression en cascade
            $table->enum('provenance_type', ['ferme_propre', 'producteur_partenaire']); // Type de provenance
            $table->unsignedBigInteger('provenance_id')->nullable(); // Clé étrangère vers farms ou partners, peut être nulle
            $table->enum('production_mode', ['bio', 'agroécologie', 'conventionnel']); // Mode de production
            $table->string('packaging_format')->nullable(); // Format d'emballage, peut être nul
            $table->decimal('min_order_quantity', 8, 2)->default(0); // Quantité minimale de commande, par défaut 0
            $table->decimal('unit_price', 8, 2); // Prix unitaire
            $table->string('sale_unit'); // Unité de vente
            $table->string('image')->nullable(); // Chemin vers l'image du produit, peut être nul
            $table->enum('status', ['disponible', 'indisponible'])->default('disponible'); // Statut du produit, par défaut disponible
            $table->json('payment_modalities')->nullable(); // Modalités de paiement (JSON), peut être nul
            $table->decimal('estimated_harvest_quantity', 8, 2)->nullable(); // Quantité estimée pour futures récoltes, peut être nulle
            $table->string('estimated_harvest_period')->nullable(); // Période de disponibilité estimée, peut être nulle
            $table->timestamps(); // Colonnes created_at et updated_at
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