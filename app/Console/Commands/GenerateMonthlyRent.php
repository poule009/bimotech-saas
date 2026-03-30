<?php

namespace App\Console\Commands;

use App\Models\Contrat;
use App\Models\Paiement;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GenerateMonthlyRent extends Command
{
    protected $signature = 'app:generate-monthly-rent';
    protected $description = 'Génère les lignes de paiements impayés (unpaid) pour les contrats actifs chaque 1er du mois';

    public function handle(): int
    {
        $periode = Carbon::now()->startOfMonth();

        $contratsActifs = Contrat::query()
            ->where('statut', 'actif')
            ->where(function ($q) use ($periode) {
                $q->whereNull('date_fin')
                  ->orWhereDate('date_fin', '>=', $periode->toDateString());
            })
            ->get();

        $this->info("Contrats actifs trouvés: {$contratsActifs->count()}");

        $created = 0;
        $skipped = 0;
        $errors = 0;

        foreach ($contratsActifs as $contrat) {
            try {
                DB::transaction(function () use ($contrat, $periode, &$created, &$skipped) {
                    $existe = Paiement::query()
                        ->where('contrat_id', $contrat->id)
                        ->whereYear('periode', $periode->year)
                        ->whereMonth('periode', $periode->month)
                        ->exists();

                    if ($existe) {
                        $skipped++;
                        return;
                    }

                    $tauxCommission = (float) ($contrat->taux_commission ?? 0);
                    $montantLoyer = (float) $contrat->loyer_contractuel;

                    $calc = Paiement::calculerMontants($montantLoyer, $tauxCommission);

                    Paiement::create([
                        'agency_id'                 => $contrat->agency_id,
                        'contrat_id'                => $contrat->id,
                        'periode'                   => $periode->toDateString(),
                        'montant_encaisse'          => $montantLoyer,
                        'mode_paiement'             => 'virement',
                        'taux_commission_applique'  => $calc['taux_commission_applique'],
                        'commission_agence'         => $calc['commission_agence'],
                        'tva_commission'            => $calc['tva_commission'],
                        'commission_ttc'            => $calc['commission_ttc'],
                        'net_proprietaire'          => $calc['net_proprietaire'],
                        'caution_percue'            => 0,
                        'est_premier_paiement'      => false,
                        'date_paiement'             => $periode->toDateString(),
                        'reference_paiement'        => null,
                        'statut'                    => 'unpaid',
                        'notes'                     => 'Généré automatiquement par app:generate-monthly-rent',
                    ]);

                    $created++;
                });
            } catch (\Throwable $e) {
                $errors++;
                Log::error('Erreur génération loyer mensuel', [
                    'contrat_id' => $contrat->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->newLine();
        $this->info("✅ Paiements créés : {$created}");
        $this->line("⏭️ Ignorés (déjà existants) : {$skipped}");
        if ($errors > 0) {
            $this->warn("⚠️ Erreurs : {$errors}");
        }

        return self::SUCCESS;
    }
}
