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
        Schema::create('delivery_routes', function (Blueprint $table) {
            $table->id(); // Clé primaire auto-incrémentée (UNSIGNED BIGINT)
            $table->date('delivery_date'); // Date de la tournée
            $table->foreignId('driver_id')->constrained('users')->onDelete('cascade'); // Clé étrangère vers users (chauffeur), suppression en cascade
            $table->text('vehicle_info')->nullable(); // Informations sur le véhicule, peut être nul
            $table->enum('status', ['planifiée', 'en_cours', 'terminée', 'annulée'])->default('planifiée'); // Statut, par défaut planifiée
            $table->json('temporary_deliverers')->nullable(); // Livreurs partenaires temporaires (JSON), peut être nul
            $table->timestamps(); // Colonnes created_at et updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_routes');
    }
};
