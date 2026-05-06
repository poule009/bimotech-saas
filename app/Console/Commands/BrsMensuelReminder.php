<?php

namespace App\Console\Commands;

use App\Models\Paiement;
use App\Models\User;
use App\Notifications\BrsMensuelNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * BrsMensuelReminder — Rappel du versement mensuel BRS à la DGI.
 *
 * Obligation : Art. 200 §4 CGI SN — l'agence verse la BRS retenue au plus tard
 * le 15 du mois M+1. Cette commande envoie un rappel à l'admin de chaque agence
 * concernée dans la fenêtre J-7 à J-0 (du 8 au 15 du mois courant).
 */
class BrsMensuelReminder extends Command
{
    protected $signature   = 'brs:mensuel-reminder';
    protected $description = 'Rappel mensuel versement BRS DGI (avant le 15 du mois)';

    public function handle(): int
    {
        $aujourd_hui = now()->timezone('Africa/Dakar')->startOfDay();
        $jourCourant = (int) $aujourd_hui->day;

        // Rappel uniquement entre le 1er et le 15 du mois (fenêtre de versement)
        if ($jourCourant < 1 || $jourCourant > 15) {
            $this->line("Hors fenêtre de rappel (jour {$jourCourant} > 15). Aucun envoi.");
            return self::SUCCESS;
        }

        $dateLimite    = $aujourd_hui->copy()->setDay(15)->endOfDay();
        $joursRestants = (int) $aujourd_hui->diffInDays($dateLimite, false);

        // N'envoyer que dans les 7 derniers jours avant l'échéance
        if ($joursRestants > 7 || $joursRestants < 0) {
            $this->line("J-{$joursRestants} avant le 15 — pas de rappel aujourd'hui.");
            return self::SUCCESS;
        }

        $moisPrecedent = $aujourd_hui->copy()->subMonth();
        $moisConcerne  = (int) $moisPrecedent->month;
        $anneeConcerne = (int) $moisPrecedent->year;
        $dateLimiteStr = $aujourd_hui->copy()->setDay(15)
            ->locale('fr')
            ->translatedFormat('d F Y');

        $this->info("Rappels BRS mensuel — paiements {$moisPrecedent->format('m/Y')} — échéance {$dateLimiteStr} (J-{$joursRestants})");
        $this->newLine();

        $paiementsParAgence = Paiement::query()
            ->whereMonth('periode', $moisConcerne)
            ->whereYear('periode', $anneeConcerne)
            ->where('brs_amount', '>', 0)
            ->where('statut', 'valide')
            ->with('contrat.bien')
            ->get()
            ->groupBy('agency_id');

        if ($paiementsParAgence->isEmpty()) {
            $this->line('Aucun paiement BRS retenu le mois précédent. Aucun envoi.');
            return self::SUCCESS;
        }

        $logger  = Log::build([
            'driver' => 'single',
            'path'   => storage_path('logs/brs-mensuel.log'),
        ]);
        $envoyes = 0;

        foreach ($paiementsParAgence as $agencyId => $paiements) {
            $totalBrsDu = (float) $paiements->sum('brs_amount');

            $nombreBailleurs = $paiements
                ->map(fn($p) => $p->contrat?->bien?->proprietaire_id)
                ->filter()
                ->unique()
                ->count();

            $admin = User::where('agency_id', $agencyId)
                ->where('role', 'admin')
                ->whereNotNull('email')
                ->first();

            if (! $admin) {
                $this->warn("  Agence #{$agencyId} : aucun admin trouvé — ignorée");
                continue;
            }

            try {
                $admin->notify(new BrsMensuelNotification(
                    moisConcerne:    $moisConcerne,
                    anneeConcerne:   $anneeConcerne,
                    totalBrsDu:      $totalBrsDu,
                    nombreBailleurs: $nombreBailleurs,
                    dateLimite:      $dateLimiteStr,
                    joursRestants:   $joursRestants,
                ));

                $logger->info('BRS mensuel envoyé', [
                    'agency_id'      => $agencyId,
                    'admin_id'       => $admin->id,
                    'mois'           => "{$moisConcerne}/{$anneeConcerne}",
                    'total_brs'      => $totalBrsDu,
                    'nb_bailleurs'   => $nombreBailleurs,
                    'jours_restants' => $joursRestants,
                    'date_envoi'     => $aujourd_hui->toDateString(),
                ]);

                $montantFmt = number_format($totalBrsDu, 0, ',', ' ');
                $this->line("  ✅ Agence #{$agencyId} (admin #{$admin->id}) — {$montantFmt} FCFA — {$nombreBailleurs} bailleur(s)");
                $envoyes++;
            } catch (\Throwable $e) {
                $this->warn("  ⚠ Agence #{$agencyId} : {$e->getMessage()}");
                Log::warning('Rappel BRS mensuel non envoyé', [
                    'agency_id' => $agencyId,
                    'error'     => $e->getMessage(),
                ]);
            }
        }

        $this->newLine();
        $this->info("Rappels BRS mensuel envoyés : {$envoyes}");

        return self::SUCCESS;
    }
}
