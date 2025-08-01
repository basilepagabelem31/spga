<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Role; // N'oubliez pas d'importer le modèle Role

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Assurez-vous que le rôle 'admin_principal' ou 'client' existe avant de l'utiliser
        // Il est recommandé d'exécuter RoleSeeder avant UserFactory/UserSeeder si vous en avez un.
        $adminRole = Role::where('name', 'admin_principal')->first();
        $clientRole = Role::where('name', 'client')->first();

        // Définir un rôle par défaut si aucun n'est trouvé (pour éviter les erreurs)
        $defaultRoleId = $adminRole ? $adminRole->id : ($clientRole ? $clientRole->id : null);

        // Si aucun rôle n'est trouvé, vous devrez peut-être créer un rôle minimal ou ajuster vos seeders.
        if (is_null($defaultRoleId)) {
            // Loguer une erreur ou créer un rôle temporaire si la base de données est vide
            // Pour le moment, nous allons simplement utiliser 1 comme ID de rôle si rien n'est trouvé,
            // mais assurez-vous que votre RoleSeeder est bien exécuté.
            $defaultRoleId = 1;
        }

        return [
            'name' => fake()->lastName(), // Nom de famille
            'first_name' => fake()->firstName(), // <-- Ajouté
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'phone_number' => fake()->phoneNumber(), // Optionnel, si votre migration le permet
            'address' => fake()->address(), // Optionnel, si votre migration le permet
            'role_id' => $defaultRoleId, // <-- Ajouté, attribue un rôle par défaut
            'is_active' => true, // <-- Ajouté, les utilisateurs de seeders sont souvent actifs par défaut
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}