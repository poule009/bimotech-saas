<?php

namespace App\Console\Commands;

use App\Models\Contrat;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * IndexationAnnuelleLoyers — Revalorisation annuelle des loyers selon le taux contractuel.
 *
 * Référence légale : Loi 81-18 (encadrement des loyers au Sénégal).
 * Le taux d'indexation est défini par contrat dans le champ indexation_annuelle (%).
 *
 * Idempotence : un contrat ne peut être indexé qu'une seule fois par année civile.
 * Le champ annee_derniere_indexation trace l'année du dernier revalorisation.
 *
 * Usage :
 *   php artisan loyers:indexation              # année courante
 *   php artisan loyers:indexation --annee=2026
 *   php artisan loyers:indexation --force       # ré-indexe même si déjà fait cette année
 */
class IndexationAnnuelleLoyers extends Command
{
    protected $signature = 'loyers:indexation
                            {--annee=  : Année d\'indexation (défaut : année courante)}
                            {--force   : Ré-indexe même si déjà effectué pour cette année}
                            {--dry-run : Affiche les changements sans les appliquer}';

    protected $description = 'Revalorisation annuelle des loyers (indexation_annuelle % sur loyer_nu)';

    public function handle(): int
    {
        $annee  = (int) ($this->option('annee') ?: now()->year);
        $force  = (bool) $this->option('force');
        $dryRun = (bool) $this->option('dry-run');

        $this->info("Indexation annuelle des loyers — année {$annee}");
        if ($dryRun) {
            $this->warn("  [DRY-RUN] Aucune modification ne sera effectuée.");
        }
        $this->newLine();

        // Contrats actifs avec un taux d'indexation > 0
        $query = Contrat::where('statut', 'actif')
            ->where('indexation_annuelle', '>', 0)
            ->with(['bien:id,agency_id,loyer_mensuel']);

        if (! $force) {
            // Exclure les contrats déjà indexés cette année
            $query->where(function ($q) use ($annee) {
                $q->whereNull('annee_derniere_indexation')
                  ->orWhere('annee_derniere_indexation', '<', $annee);
            });
        }

        $contrats = $query->get();
        $total    = $contrats->count();

        if ($total === 0) {
            $this->info("Aucun contrat à indexer.");
            return self::SUCCESS;
        }

        $this->info("Contrats à indexer : {$total}");
        $this->newLine();

        $updated = 0;
        $errors  = 0;

        foreach ($contrats as $contrat) {
            try {
                $ancienLoyer  = (float) $contrat->loyer_nu;
                $taux         = (float) $contrat->indexation_annuelle;
                $nouveauLoyer = round($ancienLoyer * (1 + $taux / 100), 2);
                $delta        = $nouveauLoyer - $ancienLoyer;

                // Recalcul du loyer contractuel total
                $charges           = (float) ($contrat->charges_mensuelles ?? 0);
                $tom               = (float) ($contrat->tom_amount ?? 0);
                $nouveauContractuel = round($nouveauLoyer + $charges + $tom, 2);

                $this->line(sprintf(
                    "  Contrat #%d — %s FCFA → %s FCFA (+%s%% = +%s FCFA)",
                    $contrat->id,
                    number_format($ancienLoyer, 0, ',', ' '),
                    number_format($nouveauLoyer, 0, ',', ' '),
                    $taux,
                    number_format($delta, 0, ',', ' ')
                ));

                if ($dryRun) {
                    continue;
                }

                DB::transaction(function () use ($contrat, $nouveauLoyer, $nouveauContractuel, $annee) {
                    // Mettre à jour le contrat
                    $contrat->update([
                        'loyer_nu'                   => $nouveauLoyer,
                        'loyer_contractuel'          => $nouveauContractuel,
                        'annee_derniere_indexation'  => $annee,
                    ]);

                    // Synchroniser bien.loyer_mensuel
                    if ($contrat->bien) {
                        $contrat->bien->update([
                            'loyer_mensuel' => $nouveauLoyer,
                        ]);
                    }
                });

                $updated++;

            } catch (\Throwable $e) {
                $errors++;
                $this->error("  ❌ Contrat #{$contrat->id} : {$e->getMessage()}");
                Log::error('Erreur indexation loyer', [
                    'contrat_id' => $contrat->id,
                    'error'      => $e->getMessage(),
                ]);
            }
        }

        $this->newLine();

        if ($dryRun) {
            $this->info("  [DRY-RUN] {$total} contrat(s) auraient été indexés. Aucune modification enregistrée.");
        } else {
            $this->info("✅ Loyers mis à jour : {$updated}");
            if ($errors > 0) {
                $this->warn("⚠️  Erreurs          : {$errors}");
            }

            Log::info("Indexation annuelle terminée", [
                'annee'   => $annee,
                'updated' => $updated,
                'errors'  => $errors,
            ]);
        }

        return $errors > 0 ? self::FAILURE : self::SUCCESS;
    }
}
