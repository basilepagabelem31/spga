<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    public function run()


       
    {

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        User::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $users = [
            // Admin Principal
            [
                'name' => 'Admin Principal 1',
                'first_name' => 'Admin1',
                'email' => 'admin_principal1@example.com',
                'password' => Hash::make('password123'),
                'role_name' => 'admin_principal',
            ],
            [
                'name' => 'Admin Principal 2',
                'first_name' => 'Admin2',
                'email' => 'admin_principal2@example.com',
                'password' => Hash::make('password123'),
                'role_name' => 'admin_principal',
            ],

            // Superviseur Commercial
            [
                'name' => 'Superviseur Commercial 1',
                'first_name' => 'Superviseur1',
                'email' => 'superviseur_commercial1@example.com',
                'password' => Hash::make('password123'),
                'role_name' => 'superviseur_commercial',
            ],
            [
                'name' => 'Superviseur Commercial 2',
                'first_name' => 'Superviseur2',
                'email' => 'superviseur_commercial2@example.com',
                'password' => Hash::make('password123'),
                'role_name' => 'superviseur_commercial',
            ],

            // Superviseur Production
            [
                'name' => 'Superviseur Production 1',
                'first_name' => 'SuperviseurProd1',
                'email' => 'superviseur_production1@example.com',
                'password' => Hash::make('password123'),
                'role_name' => 'superviseur_production',
            ],
            [
                'name' => 'Superviseur Production 2',
                'first_name' => 'SuperviseurProd2',
                'email' => 'superviseur_production2@example.com',
                'password' => Hash::make('password123'),
                'role_name' => 'superviseur_production',
            ],

            // Partenaire
            [
                'name' => 'Partenaire 1',
                'first_name' => 'Partenaire1',
                'email' => 'partenaire1@example.com',
                'password' => Hash::make('password123'),
                'role_name' => 'partenaire',
            ],
            [
                'name' => 'Partenaire 2',
                'first_name' => 'Partenaire2',
                'email' => 'partenaire2@example.com',
                'password' => Hash::make('password123'),
                'role_name' => 'partenaire',
            ],

            // Client
            [
                'name' => 'Client 1',
                'first_name' => 'Client1',
                'email' => 'client1@example.com',
                'password' => Hash::make('password123'),
                'role_name' => 'client',
            ],
            [
                'name' => 'Client 2',
                'first_name' => 'Client2',
                'email' => 'client2@example.com',
                'password' => Hash::make('password123'),
                'role_name' => 'client',
            ],

            // Chauffeur
            [
                'name' => 'Chauffeur 1',
                'first_name' => 'Chauffeur1',
                'email' => 'chauffeur1@example.com',
                'password' => Hash::make('password123'),
                'role_name' => 'chauffeur',
            ],
            [
                'name' => 'Chauffeur 2',
                'first_name' => 'Chauffeur2',
                'email' => 'chauffeur2@example.com',
                'password' => Hash::make('password123'),
                'role_name' => 'chauffeur',
            ],
        ];

        foreach ($users as $userData) {
            $role = Role::where('name', $userData['role_name'])->first();

            if (!$role) {
                continue;
            }

            User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'first_name' => $userData['first_name'],
                    'password' => $userData['password'],
                    'role_id' => $role->id,
                ]
            );
        }
    }
}
