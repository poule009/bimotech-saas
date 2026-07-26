<?php

namespace App\Console\Commands;

use App\Models\Agency;
use Database\Seeders\DemoDataSeeder;
use Illuminate\Console\Command;

class SeedDemoData extends Command
{
    protected $signature = 'demo:seed
        {--agency= : Slug ou ID de l\'agence cible (facultatif si une seule agence existe)}
        {--force : Confirme l\'exécution en production}';

    protected $description = 'Génère des données fictives (propriétaires, locataires, biens, contrats, paiements) pour une agence — usage démo/tests.';

    public function handle(): int
    {
        // Garde-fou production : exige --force explicite.
        if (app()->environment('production') && ! $this->option('force')) {
            $this->error('Environnement de production détecté. Relancez avec --force pour confirmer.');
            $this->line('  php artisan demo:seed --agency=<slug-ou-id> --force');
            return self::FAILURE;
        }

        $agency = $this->resolveAgency();
        if (! $agency) {
            return self::FAILURE;
        }

        $this->warn('⚠️  Les comptes de démo créés utilisent le mot de passe « password ».');
        $this->info("🏢 Agence cible : {$agency->name} (#{$agency->id}, slug: {$agency->slug})");
        $this->newLine();

        // Passe les paramètres au seeder puis l'exécute.
        DemoDataSeeder::$targetAgencyId  = $agency->id;
        DemoDataSeeder::$allowProduction = (bool) $this->option('force');

        $this->call('db:seed', [
            '--class' => DemoDataSeeder::class,
            '--force' => true, // évite la question interactive « run in production? » de db:seed
        ]);

        return self::SUCCESS;
    }

    /** Résout l'agence depuis --agency (slug ou id), ou l'unique agence existante. */
    private function resolveAgency(): ?Agency
    {
        $ref = $this->option('agency');

        if ($ref !== null && $ref !== '') {
            $agency = Agency::sansPerimetre()
                ->where('slug', $ref)
                ->when(is_numeric($ref), fn ($q) => $q->orWhere('id', (int) $ref))
                ->first();

            if (! $agency) {
                $this->error("Aucune agence ne correspond à « {$ref} ».");
            }

            return $agency;
        }

        $agencies = Agency::sansPerimetre()->get();

        if ($agencies->count() === 1) {
            return $agencies->first();
        }

        if ($agencies->isEmpty()) {
            $this->error('Aucune agence en base. Créez d\'abord votre agence.');
            return null;
        }

        $this->error('Plusieurs agences existent — précisez --agency=<slug-ou-id> :');
        foreach ($agencies as $a) {
            $this->line("  - #{$a->id}  {$a->slug}  ({$a->name})");
        }

        return null;
    }
}
