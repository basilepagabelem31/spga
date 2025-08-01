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
        Schema::create('contracts', function (Blueprint $table) {
            $table->id(); // Clé primaire auto-incrémentée (UNSIGNED BIGINT)
            $table->foreignId('partner_id')->constrained('partners')->onDelete('cascade'); // Clé étrangère vers partners, suppression en cascade
            $table->string('title'); // Titre du contrat
            $table->string('file_path')->nullable(); // Chemin vers le document du contrat, peut être nul
            $table->date('start_date'); // Date de début du contrat
            $table->date('end_date')->nullable(); // Date de fin du contrat, peut être nulle
            $table->text('description')->nullable(); // Description du contrat, peut être nulle
            $table->timestamps(); // Colonnes created_at et updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
