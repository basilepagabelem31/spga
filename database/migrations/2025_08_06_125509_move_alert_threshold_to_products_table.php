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
        // 1. Ajouter la colonne alert_threshold à la table products
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('alert_threshold', 8, 2)->nullable()->after('current_stock_quantity');
        });

        // 2. Supprimer la colonne alert_threshold de la table stocks
        Schema::table('stocks', function (Blueprint $table) {
            // Vérifiez si la colonne existe avant de la supprimer pour éviter des erreurs si elle a déjà été supprimée manuellement
            if (Schema::hasColumn('stocks', 'alert_threshold')) {
                $table->dropColumn('alert_threshold');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Remettre la colonne alert_threshold sur la table stocks
        Schema::table('stocks', function (Blueprint $table) {
            $table->decimal('alert_threshold', 8, 2)->nullable();
        });

        // 2. Supprimer la colonne alert_threshold de la table products
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'alert_threshold')) {
                $table->dropColumn('alert_threshold');
            }
        });
    }
};
