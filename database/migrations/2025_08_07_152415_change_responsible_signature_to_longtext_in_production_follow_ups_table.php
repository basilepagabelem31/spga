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
        Schema::table('production_follow_ups', function (Blueprint $table) {
            // Change le type de la colonne en `longText`.
            $table->longText('responsible_signature')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('production_follow_ups', function (Blueprint $table) {
            // Revenir au type de colonne d'origine si nécessaire (optionnel).
            $table->string('responsible_signature')->nullable()->change();
        });
    }
};