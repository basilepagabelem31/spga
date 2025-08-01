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
        // Votre table 'users' personnalisée
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // Clé primaire auto-incrémentée (UNSIGNED BIGINT)
            $table->string('name'); // Nom
            $table->string('first_name'); // Prénom (ajouté dans votre version)
            $table->string('email')->unique(); // Adresse email, unique
            $table->string('password'); // Mot de passe haché
            $table->string('phone_number')->nullable(); // Numéro de téléphone, peut être nul (ajouté dans votre version)
            $table->string('address')->nullable(); // Adresse de l'utilisateur, peut être nulle (ajouté dans votre version)
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade'); // Clé étrangère vers roles, suppression en cascade (ajouté dans votre version)
            $table->boolean('is_active')->default(true); // Booléen pour valider les comptes, par défaut vrai (ajouté dans votre version)
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps(); // Colonnes created_at et updated_at
        });

        // La table 'password_reset_tokens' par défaut de Laravel
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // La table 'sessions' par défaut de Laravel
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};