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
        // Attention : ALTER TABLE pour ENUM n'est pas géré directement par Blueprint, on utilise DB::statement
        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('En attente de validation', 'Validée', 'En préparation', 'En livraison', 'Livrée', 'Annulée', 'Terminée') DEFAULT 'En attente de validation'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // On remet l'ENUM d'origine
        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('En attente de validation', 'Validée', 'En préparation', 'En livraison', 'Livrée', 'Annulée') DEFAULT 'En attente de validation'");
    }
};
