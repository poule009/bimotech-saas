<?php

namespace App\Console\Commands;

use App\Models\Agency;
use App\Models\MrrSnapshot;
use App\Services\MrrService;
use Illuminate\Console\Command;

/**
 * Capture le MRR du mois courant dans `mrr_snapshots` (idempotent).
 *
 * Planifiée quotidiennement : le mois courant se rafraîchit chaque jour, si
 * bien que la ligne d'un mois passé garde sa dernière valeur (≈ fin de mois).
 */
class CaptureMrrSnapshot extends Command
{
    protected $signature = 'mrr:snapshot';

    protected $description = 'Capture le MRR (équivalent mensuel) du mois courant dans mrr_snapshots';

    public function handle(MrrService $mrr): int
    {
        $agences = Agency::with('subscription')->get();
        $mois = now()->startOfMonth()->toDateString();

        $snapshot = MrrSnapshot::updateOrCreate(
            ['mois' => $mois],
            [
                'mrr' => $mrr->current($agences),
                'agences_actives' => $agences->where('actif', true)->count(),
            ]
        );

        $this->info("Snapshot MRR {$mois} : {$snapshot->mrr} FCFA/mois — {$snapshot->agences_actives} agences actives.");

        return self::SUCCESS;
    }
}
