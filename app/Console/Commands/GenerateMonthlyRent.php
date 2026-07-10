<?php

namespace App\Console\Commands;

use App\Models\Contrat;
use App\Services\QuittanceGenerator;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GenerateMonthlyRent extends Command
{
    protected $signature   = 'rent:generate {--mois= : Mois au format YYYY-MM (défaut: mois courant)}';
    protected $description = 'Génère les paiements mensuels (statut unpaid) pour tous les contrats actifs';

    public function handle(): int
    {
        $moisArg = $this->option('mois');
        $periode = $moisArg
            ? Carbon::createFromFormat('Y-m', $moisArg)->startOfMonth()
            : now()->startOfMonth();

        $this->info("Génération des loyers pour : {$periode->format('F Y')}");
        $this->newLine();

        // ── Auto-expiration des contrats dont date_fin est dépassée ─────────
        // Évite de générer des loyers fantômes sur des baux techniquement terminés.
        $expires = Contrat::where('statut', 'actif')
            ->whereNotNull('date_fin')
            ->where('date_fin', '<', $periode->startOfMonth())
            ->update(['statut' => 'expiré']);

        if ($expires > 0) {
            $this->warn("⚠️  {$expires} contrat(s) expiré(s) mis à jour automatiquement.");
        }

        // Compter avant pour affichage
        $total = Contrat::where('statut', 'actif')->count();
        $this->info("Contrats actifs trouvés : {$total}");

        $created = 0;
        $skipped = 0;
        $errors  = 0;

        // ── CHUNK(100) au lieu de get() ─────────────────────────────────
        // Traite 100 contrats à la fois, libère la mémoire entre chaque lot.
        // Évite les OOM sur les grosses agences (500+ contrats).
        // Les eager loads couvrent tous les champs nécessaires au FiscalService.
        $generator = app(QuittanceGenerator::class);

        Contrat::where('statut', 'actif')
            ->with(QuittanceGenerator::RELATIONS)
            ->chunk(100, function ($contrats) use (
                $periode, $generator, &$created, &$skipped, &$errors
            ) {
                foreach ($contrats as $contrat) {
                    try {
                        $paiement = $generator->genererPourContrat($contrat, $periode, 'rent:generate');

                        if ($paiement === null) {
                            $skipped++;
                            continue;
                        }

                        $created++;
                        $this->line("  ✅ Contrat #{$contrat->id} — {$paiement->montant_encaisse} FCFA");

                    } catch (\Throwable $e) {
                        $errors++;
                        $this->error("  ❌ Contrat #{$contrat->id} : {$e->getMessage()}");
                        Log::error('Erreur génération loyer mensuel', [
                            'contrat_id' => $contrat->id,
                            'error'      => $e->getMessage(),
                            'trace'      => $e->getTraceAsString(),
                        ]);
                    }
                }
            });

        $this->newLine();
        $this->info("✅ Paiements créés    : {$created}");
        $this->line("⏭️  Ignorés (existants) : {$skipped}");

        if ($errors > 0) {
            $this->warn("⚠️  Erreurs            : {$errors}");
        }

        return self::SUCCESS;
    }
}