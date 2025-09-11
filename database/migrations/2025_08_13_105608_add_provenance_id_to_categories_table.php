<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('categories', function (Blueprint $table) {
            // provenance_id est nul pour les catégories générales (administrateurs)
            $table->unsignedBigInteger('provenance_id')->nullable()->after('description');
            // Relation avec la table partners si vous le souhaitez
            $table->foreign('provenance_id')->references('id')->on('partners')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropForeign(['provenance_id']);
            $table->dropColumn('provenance_id');
        });
    }
};
