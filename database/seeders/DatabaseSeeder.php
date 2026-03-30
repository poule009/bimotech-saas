<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. SuperAdminSeeder EN PREMIER — indépendant de toute agence (agency_id = null)
        //    Utilise updateOrCreate → idempotent même après migrate:fresh
        $this->call([
            SuperAdminSeeder::class,
        ]);

        // 2. AgencySeeder — crée l'agence de démonstration
        //    Tous les autres seeders dépendent de l'agence créée ici
        $this->call([
            AgencySeeder::class,
        ]);
    }
}
