<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Étape 1: Mettre à jour la casse des données existantes
        DB::table('delivery_routes')->where('status', 'planifiée')->update(['status' => 'Planifiée']);
        DB::table('delivery_routes')->where('status', 'en_cours')->update(['status' => 'En cours']);
        DB::table('delivery_routes')->where('status', 'terminée')->update(['status' => 'Terminée']);
        DB::table('delivery_routes')->where('status', 'annulée')->update(['status' => 'Annulée']);

        // Étape 2: Modifier la colonne pour qu'elle puisse prendre les nouvelles valeurs d'ENUM
        Schema::table('delivery_routes', function (Blueprint $table) {
            $table->string('status', 20)->default('Planifiée')->change();
        });

        // Étape 3: Définir les nouvelles valeurs de l'ENUM avec la bonne casse
        DB::statement("ALTER TABLE delivery_routes CHANGE COLUMN status status ENUM('Planifiée', 'En cours', 'Terminée', 'Annulée') NOT NULL DEFAULT 'Planifiée'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Étape 1: Revenir à l'ancien ENUM
        DB::statement("ALTER TABLE delivery_routes CHANGE COLUMN status status ENUM('planifiée', 'en_cours', 'terminée', 'annulée') NOT NULL DEFAULT 'planifiée'");

        // Étape 2: Revenir à la casse originale
        DB::table('delivery_routes')->where('status', 'Planifiée')->update(['status' => 'planifiée']);
        DB::table('delivery_routes')->where('status', 'En cours')->update(['status' => 'en_cours']);
        DB::table('delivery_routes')->where('status', 'Terminée')->update(['status' => 'terminée']);
        DB::table('delivery_routes')->where('status', 'Annulée')->update(['status' => 'annulée']);
    }
};