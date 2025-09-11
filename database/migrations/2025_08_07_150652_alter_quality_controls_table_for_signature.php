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
        Schema::table('quality_controls', function (Blueprint $table) {
            // Change le type de la colonne en LONGTEXT.
            // La méthode 'change()' est nécessaire pour modifier une colonne existante.
            $table->longText('responsible_signature_qc')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quality_controls', function (Blueprint $table) {
            // Revertit la colonne en VARCHAR(255).
            // Attention : cela tronquera les données qui dépassent 255 caractères.
            $table->string('responsible_signature_qc', 255)->nullable()->change();
        });
    }
};