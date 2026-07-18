<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\CommissionService;
use Illuminate\Console\Command;

/**
 * Capture la commission du mois courant pour chaque collaborateur super-admin
 * dans `commission_snapshots` (idempotent).
 *
 * Planifiée quotidiennement, comme mrr:snapshot : le mois courant se rafraîchit
 * chaque jour, et la ligne d'un mois passé garde sa dernière valeur (≈ fin de mois),
 * ce qui constitue l'historique en lecture seule (jamais recalculé rétroactivement).
 */
class CaptureCommissionSnapshot extends Command
{
    protected $signature = 'commissions:snapshot';

    protected $description = 'Capture la commission du mois courant de chaque collaborateur super-admin';

    public function handle(CommissionService $commissions): int
    {
        $collaborateurs = User::collaborateursSa()->get();

        if ($collaborateurs->isEmpty()) {
            $this->info('Aucun collaborateur super-admin — rien à capturer.');

            return self::SUCCESS;
        }

        foreach ($collaborateurs as $collaborateur) {
            $snap = $commissions->capturer($collaborateur);
            $this->info("{$collaborateur->name} : {$snap->nb_agences} agence(s), commission {$snap->commission} FCFA.");
        }

        return self::SUCCESS;
    }
}
