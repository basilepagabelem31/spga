<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use Illuminate\Support\Facades\Schema;

class RoleSeeder extends Seeder
{
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        Role::truncate();

        $roles = [
            [
                'name' => 'admin_principal',
                'description' => 'Administrateur principal du système.'
            ],
            [
                'name' => 'superviseur_commercial',
                'description' => 'Superviseur des opérations commerciales.'
            ],
            [
                'name' => 'superviseur_production',
                'description' => 'Superviseur des opérations de production.'
            ],
            [
                'name' => 'chauffeur',
                'description' => 'Chauffeur pour les livraisons.'
            ],
            [
                'name' => 'partenaire',
                'description' => 'Partenaire (producteur, grossiste, fournisseur) de SPGA-SARL.'
            ],
            [
                'name' => 'client',
                'description' => 'Utilisateur client de la plateforme.'
            ],
        ];

        foreach ($roles as $roleData) {
            Role::firstOrCreate(['name' => $roleData['name']], $roleData);
        }
        
        Schema::enableForeignKeyConstraints();
    }
}