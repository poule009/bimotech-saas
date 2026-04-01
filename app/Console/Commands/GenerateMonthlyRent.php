<?php

namespace App\Console\Commands;

use App\Models\Contrat;
use App\Models\Paiement;
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

        $contrats = Contrat::where('statut', 'actif')
            ->with('bien', 'locataire')
            ->get();

        $this->info("Contrats actifs trouvés : {$contrats->count()}");

        $created = 0;
        $skipped = 0;
        $errors  = 0;

        foreach ($contrats as $contrat) {
            try {
                // Vérifier doublon
                $existe = Paiement::where('contrat_id', $contrat->id)
                    ->whereYear('periode', $periode->year)
                    ->whereMonth('periode', $periode->month)
                    ->where('statut', '!=', 'annule')
                    ->exists();

                if ($existe) {
                    $skipped++;
                    continue;
                }

                // Taux de commission depuis le bien
                $tauxCommission = (float) ($contrat->bien->taux_commission ?? 0);

                // ── Ventilation du loyer depuis le contrat ────────────────
                // loyer_nu_effectif est l'accesseur défini sur le modèle Contrat
                // qui gère la rétro-compatibilité (loyer_nu ou loyer_contractuel - charges - tom)
                $loyerNu       = (float) $contrat->loyer_nu_effectif;
                $chargesAmount = (float) ($contrat->charges_mensuelles ?? 0);
                $tomAmount     = (float) ($contrat->tom_amount ?? 0);

                // Calcul via la méthode statique du modèle Paiement
                $calcul = Paiement::calculerMontants(
                    loyerNu:        $loyerNu,
                    tauxCommission: $tauxCommission,
                    chargesAmount:  $chargesAmount,
                    tomAmount:      $tomAmount,
                );

                Paiement::create([
                    'agency_id'                => $contrat->agency_id,
                    'contrat_id'               => $contrat->id,
                    'periode'                  => $periode->toDateString(),
                    // Ventilation
                    'loyer_nu'                 => $calcul['loyer_nu'],
                    'charges_amount'           => $calcul['charges_amount'],
                    'tom_amount'               => $calcul['tom_amount'],
                    'montant_encaisse'         => $calcul['montant_encaisse'],
                    // Commission
                    'mode_paiement'            => 'virement',
                    'taux_commission_applique' => $tauxCommission,
                    'commission_agence'        => $calcul['commission_ht'],
                    'tva_commission'           => $calcul['tva'],
                    'commission_ttc'           => $calcul['commission_ttc'],
                    'net_proprietaire'         => $calcul['net_proprietaire'],
                    // Référence bail depuis le contrat
                    'reference_bail'           => $contrat->reference_bail_affichee,
                    // Divers
                    'caution_percue'           => 0,
                    'est_premier_paiement'     => false,
                    'date_paiement'            => $periode->toDateString(),
                    'reference_paiement'       => null,
                    'statut'                   => 'unpaid',
                    'notes'                    => 'Généré automatiquement par rent:generate le ' . now()->format('d/m/Y'),
                ]);

                $created++;
                $this->line("  ✅ Contrat #{$contrat->id} — {$contrat->locataire->name} — {$calcul['montant_encaisse']} FCFA");

            } catch (\Throwable $e) {
                $errors++;
                $this->error("  ❌ Contrat #{$contrat->id} : {$e->getMessage()}");
                Log::error('Erreur génération loyer mensuel', [
                    'contrat_id' => $contrat->id,
                    'error'      => $e->getMessage(),
                ]);
            }
        }

        $this->newLine();
        $this->info("✅ Paiements créés   : {$created}");
        $this->line("⏭️  Ignorés (existants): {$skipped}");

        if ($errors > 0) {
            $this->warn("⚠️  Erreurs           : {$errors}");
        }

        return self::SUCCESS;
    }
}