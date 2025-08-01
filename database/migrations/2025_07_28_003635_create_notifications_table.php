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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id(); // Clé primaire auto-incrémentée (UNSIGNED BIGINT)
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade'); // ID de l'utilisateur recevant la notification, peut être nul, suppression en cascade
            $table->string('type'); // Type de notification
            $table->text('message'); // Contenu de la notification
            $table->timestamp('read_at')->nullable(); // Timestamp quand la notification a été lue, peut être nulle
            $table->timestamps(); // Colonnes created_at et updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
