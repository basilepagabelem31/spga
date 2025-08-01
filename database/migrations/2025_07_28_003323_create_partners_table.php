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
        Schema::create('partners', function (Blueprint $table) {
            $table->id(); // Clé primaire auto-incrémentée (UNSIGNED BIGINT)
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade'); // Clé étrangère vers users, peut être nulle, suppression en cascade
            $table->string('establishment_name'); // Nom de l'établissement
            $table->string('contact_name')->nullable(); // Nom et prénom du contact, peut être nul
            $table->string('function')->nullable(); // Fonction, peut être nulle
            $table->string('phone')->nullable(); // Téléphone, peut être nul
            $table->string('email')->nullable(); // Email, peut être nul
            $table->string('locality_region')->nullable(); // Localité/Région, peut être nulle
            $table->enum('type', ['Producteur individuel', 'Coopérative agricole/maraîchère', 'Ferme partenaire']); // Type de partenaire
            $table->integer('years_of_experience')->nullable(); // Nombre d'années d'expérience, peut être nul
            $table->timestamps(); // Colonnes created_at et updated_at
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partners');
    }
};
