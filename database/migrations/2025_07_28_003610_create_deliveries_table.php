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
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id(); // Clé primaire auto-incrémentée (UNSIGNED BIGINT)
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade'); // Clé étrangère vers orders, suppression en cascade
            $table->foreignId('delivery_route_id')->constrained('delivery_routes')->onDelete('cascade'); // Clé étrangère vers delivery_routes, suppression en cascade
            $table->enum('status', ['En cours', 'Terminée', 'Annulée'])->default('En cours'); // Statut, par défaut En cours
            $table->enum('delivery_proof_type', ['bouton_confirmation', 'signature_numerique', 'photo', 'bordereau_signe'])->nullable(); // Type de preuve de livraison, peut être nul
            $table->text('delivery_proof_data')->nullable(); // Données de la preuve, peut être nul
            $table->string('recipient_name')->nullable(); // Nom du réceptionnaire, peut être nul
            $table->text('recipient_signature')->nullable(); // Signature numérique du réceptionnaire, peut être nulle
            $table->text('delivery_person_signature')->nullable(); // Signature numérique du livreur, peut être nulle
            $table->timestamp('delivered_at')->nullable(); // Date et heure de livraison effective, peut être nulle
            $table->text('notes')->nullable(); // Notes, peut être nul
            $table->timestamps(); // Colonnes created_at et updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};
