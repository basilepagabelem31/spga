<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role; // N'oubliez pas d'importer le modèle Role

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Vérifier si le rôle existe avant de le créer pour éviter les doublons
        if (!Role::where('name', 'admin_principal')->exists()) {
            Role::create([
                'name' => 'admin_principal',
                'description' => 'Administrateur principal du système.'
            ]);
        }
        if (!Role::where('name', 'superviseur_commercial')->exists()) {
            Role::create([
                'name' => 'superviseur_commercial',
                'description' => 'Superviseur des opérations commerciales.'
            ]);
        }
        if (!Role::where('name', 'superviseur_production')->exists()) {
            Role::create([
                'name' => 'superviseur_production',
                'description' => 'Superviseur des opérations de production.'
            ]);
        }
        if (!Role::where('name', 'client')->exists()) { // <-- Ajoutez celui-ci
            Role::create([
                'name' => 'client',
                'description' => 'Utilisateur client de la plateforme.'
            ]);
        }
        if (!Role::where('name', 'partenaire_strategique')->exists()) {
            Role::create([
                'name' => 'partenaire_strategique',
                'description' => 'Partenaire stratégique de SPGA-SARL.'
            ]);
        }
        if (!Role::where('name', 'chauffeur')->exists()) {
            Role::create([
                'name' => 'chauffeur',
                'description' => 'Chauffeur pour les livraisons.'
            ]);
        }
    }
}