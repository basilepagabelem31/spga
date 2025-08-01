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
        Schema::create('production_follow_ups', function (Blueprint $table) {
            $table->id(); // Clé primaire auto-incrémentée (UNSIGNED BIGINT)
            $table->string('production_site'); // Unité de production
            $table->string('commune')->nullable(); // Nom de la commune, peut être nul
            $table->string('village')->nullable(); // Nom du village, peut être nul
            $table->string('producer_name')->nullable(); // Nom complet du producteur, peut être nul
            $table->string('technical_agent_name')->nullable(); // Nom complet de l'agent technique, peut être nul
            $table->date('follow_up_date'); // Date du suivi
            $table->string('culture_name'); // Nom de la culture
            $table->string('cultivated_variety')->nullable(); // Variété cultivée, peut être nulle
            $table->date('sowing_planting_date')->nullable(); // Date de semis / plantation, peut être nulle
            $table->decimal('cultivated_surface', 10, 2)->nullable(); // Surface cultivée, peut être nulle
            $table->enum('production_type', ['Conventionnel', 'Biologique', 'Agroécologie']); // Type de production
            $table->string('development_stage')->nullable(); // Stade de développement, peut être nul
            $table->text('works_performed')->nullable(); // Travaux réalisés, peut être nul
            $table->text('technical_observations')->nullable(); // Observations techniques, peut être nul
            $table->text('recommended_interventions')->nullable(); // Interventions recommandées, peut être nul
            $table->string('responsible_signature')->nullable(); // Nom du responsable, peut être nul
            $table->timestamps(); // Colonnes created_at et updated_at
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_follow_ups');
    }
};
