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
        Schema::table('production_follow_ups', function (Blueprint $table) {
            $table->string('address')->nullable()->after('village');           // Adresse complète
            $table->decimal('latitude', 10, 7)->nullable()->after('address');  // Latitude GPS
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude'); // Longitude GPS
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('production_follow_ups', function (Blueprint $table) {
            $table->dropColumn(['address', 'latitude', 'longitude']);
        });
    }
};
