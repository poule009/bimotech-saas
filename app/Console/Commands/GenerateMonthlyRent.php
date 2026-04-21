<?php

namespace App\Console\Commands;

use App\Models\Contrat;
use App\Models\Paiement;
use App\Services\FiscalService;
use App\Services\FiscalContext;
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
        Contrat::where('statut', 'actif')
            ->with([
                'bien:id,agency_id,proprietaire_id,taux_commission,meuble,type',
                'locataire:id,name',
                'locataire.locataire:user_id,est_entreprise,taux_brs_override',
            ])
            ->chunk(100, function ($contrats) use (
                $periode, &$created, &$skipped, &$errors
            ) {
                foreach ($contrats as $contrat) {
                    try {
                        // ── Vérification doublon ──────────────────────────
                        $existe = Paiement::where('contrat_id', $contrat->id)
                            ->whereYear('periode', $periode->year)
                            ->whereMonth('periode', $periode->month)
                            ->where('statut', '!=', 'annule')
                            ->exists();

                        if ($existe) {
                            $skipped++;
                            continue;
                        }

                        // ── Calcul fiscal via FiscalService ───────────────
                        $ctx = FiscalContext::fromContrat($contrat);
                        $result = FiscalService::calculer($ctx);

                        Paiement::create([
                            'agency_id'                    => $contrat->agency_id,
                            'contrat_id'                   => $contrat->id,
                            'periode'                      => $periode->toDateString(),
                            // Ventilation loyer
                            'loyer_ht'                     => $result->loyerHt,
                            'tva_loyer'                    => $result->tvaLoyer,
                            'loyer_ttc'                    => $result->loyerTtc,
                            'loyer_nu'                     => $result->loyerHt,  // alias rétro-compat
                            'charges_amount'               => $result->chargesAmount,
                            'tom_amount'                   => $result->tomAmount,
                            'montant_encaisse'             => $result->montantEncaisse,
                            // Commission
                            'mode_paiement'                => 'virement',
                            'taux_commission_applique'     => $ctx->tauxCommission,
                            'commission_agence'            => $result->commissionHt,
                            'tva_commission'               => $result->tvaCommission,
                            'commission_ttc'               => $result->commissionTtc,
                            // Nets
                            'net_proprietaire'             => $result->netProprietaire,
                            'brs_amount'                   => $result->brsAmount,
                            'taux_brs_applique'            => $result->tauxBrsApplique,
                            'net_a_verser_proprietaire'    => $result->netAVerserProprietaire,
                            // Snapshot fiscal
                            'regime_fiscal_snapshot'       => json_encode($result->toArray()),
                            // Divers
                            'reference_bail'               => $contrat->reference_bail_affichee,
                            'caution_percue'               => 0,
                            'est_premier_paiement'         => false,
                            'date_paiement'                => $periode->toDateString(),
                            'reference_paiement'           => null,
                            'statut'                       => 'unpaid',
                            'notes'                        => 'Généré par rent:generate le ' . now()->format('d/m/Y H:i'),
                        ]);

                        $created++;
                        $this->line("  ✅ Contrat #{$contrat->id} — {$result->montantEncaisse} FCFA");

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