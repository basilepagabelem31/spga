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
        // 1. Mettre à jour les valeurs non valides ou incorrectes.
        // Utilisation de la fonction DATE() de MySQL pour gérer la conversion
        // des chaînes de caractères au format DATETIME en DATE.
        DB::table('orders')->update([
            'desired_delivery_date' => DB::raw("CASE
                WHEN desired_delivery_date IS NULL OR desired_delivery_date = '' THEN NULL
                ELSE DATE(desired_delivery_date)
            END"),
        ]);
        
        // 2. Modifier la colonne pour qu'elle accepte les dates et soit nullable
        Schema::table('orders', function (Blueprint $table) {
            $table->date('desired_delivery_date')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('desired_delivery_date')->nullable()->change();
        });
    }
};