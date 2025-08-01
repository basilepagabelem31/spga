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
        Schema::create('quality_controls', function (Blueprint $table) {
            $table->id(); // Clé primaire auto-incrémentée (UNSIGNED BIGINT)
            $table->timestamp('control_date'); // Date du contrôle
            $table->foreignId('controller_id')->constrained('users')->onDelete('cascade'); // Clé étrangère vers users (contrôleur), suppression en cascade
            $table->string('production_unit')->nullable(); // Unité de production, peut être nulle
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade'); // Clé étrangère vers products, suppression en cascade
            $table->string('lot_reference')->nullable(); // Code de traçabilité, peut être nul
            $table->enum('control_type', ['Visuel', 'Physico-chimique', 'Microbiologique', 'Poids', 'Température'])->nullable(); // Type de contrôle, peut être nul
            $table->string('method_used')->nullable(); // Méthode utilisée, peut être nulle
            $table->enum('control_result', ['Conforme', 'Non conforme', 'À réévaluer']); // Résultat du contrôle
            $table->text('observed_non_conformities')->nullable(); // Non-conformités observées, peut être nul
            $table->text('proposed_corrective_actions')->nullable(); // Actions correctives proposées, peut être nul
            $table->string('responsible_signature_qc')->nullable(); // Signature du responsable du contrôle qualité, peut être nul
            $table->timestamps(); // Colonnes created_at et updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quality_controls');
    }
};
