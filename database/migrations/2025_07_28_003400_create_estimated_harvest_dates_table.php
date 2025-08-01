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
        Schema::create('estimated_harvest_dates', function (Blueprint $table) {
            $table->id(); // Clé primaire auto-incrémentée (UNSIGNED BIGINT)
            $table->foreignId('production_follow_up_id')->constrained('production_follow_ups')->onDelete('cascade'); // Clé étrangère vers production_follow_ups, suppression en cascade
            $table->string('speculation_name'); // Nom de la spéculation
            $table->date('estimated_date'); // Date estimée de récolte
            $table->timestamps(); // Colonnes created_at et updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estimated_harvest_dates');
    }
};
