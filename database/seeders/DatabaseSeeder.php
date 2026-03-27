<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // AgencySeeder doit toujours tourner EN PREMIER
        // car tous les autres seeders dépendent de l'agence créée
        $this->call([
            AgencySeeder::class,
        ]);
    }
}