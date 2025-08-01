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
        Schema::create('orders', function (Blueprint $table) {
            $table->id(); // Clé primaire auto-incrémentée (UNSIGNED BIGINT)
            $table->foreignId('client_id')->constrained('users')->onDelete('cascade'); // Clé étrangère vers users (clients), suppression en cascade
            $table->string('order_code')->unique(); // Code de commande généré automatiquement, unique
            $table->timestamp('order_date'); // Date de la commande
            $table->string('desired_delivery_date')->nullable(); // Date ou plage horaire de livraison souhaitée, peut être nulle
            $table->text('delivery_location')->nullable(); // Lieu de livraison souhaité, peut être nul
            $table->string('geolocation')->nullable(); // Coordonnées géolocalisation, peut être nulle
            $table->enum('delivery_mode', ['standard_72h', 'express_6_12h'])->default('standard_72h'); // Mode de livraison, par défaut standard_72h
            $table->enum('payment_mode', ['paiement_mobile', 'paiement_a_la_livraison', 'virement_bancaire']); // Mode de paiement
            $table->enum('status', ['En attente de validation', 'Validée', 'En préparation', 'En livraison', 'Livrée', 'Annulée'])->default('En attente de validation'); // Statut de la commande, par défaut En attente de validation
            $table->decimal('total_amount', 10, 2); // Montant total de la commande
            $table->text('notes')->nullable(); // Notes, peut être nul
            $table->foreignId('validated_by')->nullable()->constrained('users')->onDelete('set null'); // ID de l'utilisateur qui a validé, peut être nul, mis à null si l'utilisateur est supprimé
            $table->timestamps(); // Colonnes created_at et updated_at
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
