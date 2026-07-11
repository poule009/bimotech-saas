<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Permissions spatie — doit être en premier (les users en dépendent)
        $this->call([
            PermissionsSeeder::class,
        ]);

        // 1 bis. Catalogue des règles fiscales & sources (référentiel documentaire,
        //        indépendant de toute agence — idempotent par `cle`)
        $this->call([
            ReglesFiscalesSeeder::class,
        ]);

        // 2. SuperAdminSeeder EN PREMIER — indépendant de toute agence (agency_id = null)
        //    Utilise updateOrCreate → idempotent même après migrate:fresh
        $this->call([
            SuperAdminSeeder::class,
        ]);

        // 3. AgencySeeder — crée l'agence de démonstration
        //    Tous les autres seeders dépendent de l'agence créée ici
        $this->call([
            AgencySeeder::class,
        ]);
    }
}
