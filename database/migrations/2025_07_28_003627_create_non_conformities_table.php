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
        Schema::create('non_conformities', function (Blueprint $table) {
            $table->id(); // Clé primaire auto-incrémentée (UNSIGNED BIGINT)
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade'); // Clé étrangère vers products, suppression en cascade
            $table->foreignId('quality_control_id')->constrained('quality_controls')->onDelete('cascade'); // Clé étrangère vers quality_controls, suppression en cascade
            $table->text('description')->nullable(); // Description de la non-conformité, peut être nulle
            $table->enum('status', ['en attente de décision', 'rejeté', 'reconditionné'])->default('en attente de décision'); // Statut, par défaut en attente de décision
            $table->foreignId('decision_taken_by')->nullable()->constrained('users')->onDelete('set null'); // ID de l'utilisateur ayant pris la décision, peut être nul, mis à null si l'utilisateur est supprimé
            $table->timestamp('decision_date')->nullable(); // Date de la décision, peut être nulle
            $table->timestamps(); // Colonnes created_at et updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('non_conformities');
    }
};
