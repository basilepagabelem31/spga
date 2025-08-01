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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id(); // Clé primaire auto-incrémentée (UNSIGNED BIGINT)
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Clé étrangère vers users, suppression en cascade
            $table->string('action'); // Description de l'action effectuée
            $table->string('table_name')->nullable(); // Table affectée par l'action, peut être nulle
            $table->unsignedBigInteger('record_id')->nullable(); // ID de l'enregistrement affecté, peut être nul
            $table->json('old_values')->nullable(); // Données avant modification (JSON), peut être nul
            $table->json('new_values')->nullable(); // Données après modification (JSON), peut être nul
            $table->string('ip_address')->nullable(); // Adresse IP, peut être nulle
            $table->text('user_agent')->nullable(); // Agent utilisateur, peut être nul
            $table->timestamps(); // Colonnes created_at et updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
