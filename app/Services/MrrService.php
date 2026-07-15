<?php

namespace App\Services;

use App\Models\Agency;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Calcul du MRR (revenu récurrent mensuel) de la plateforme.
 *
 * Source unique de la logique MRR — utilisée par le dashboard Super Admin ET
 * par la commande de snapshot, pour éviter toute divergence de règle (tarifs,
 * exclusion Legacy…). Ne jamais recoder ce calcul ailleurs.
 */
class MrrService
{
    /**
     * MRR (équivalent mensuel) des abonnements PAYANTS qui couvraient `$date`.
     * Plans Legacy exclus (clients test / historiques, non facturés).
     *
     * @param  Collection<int,Agency>|null  $agences  agences pré-chargées avec la
     *                                                relation `subscription` (passer la collection évite une requête).
     */
    public function at(Carbon $date, ?Collection $agences = null): int
    {
        $agences ??= Agency::with('subscription')->get();

        return (int) $agences->sum(function (Agency $a) use ($date) {
            $s = $a->subscription;

            if (! $s || ! in_array($s->plan_niveau, ['starter', 'pro', 'agence'], true)) {
                return 0;
            }
            if (! $s->date_debut_abonnement || ! $s->date_fin_abonnement) {
                return 0;
            }
            if ($s->date_debut_abonnement->gt($date) || $s->date_fin_abonnement->lt($date)) {
                return 0;
            }

            // Montant réellement facturé sur ce cycle (snapshot), et NON le tarif
            // courant du plan : le Super Admin peut changer les tarifs à chaud, ce
            // qui ferait autrement bouger le MRR passé des agences déjà engagées.
            return $s->mrrEquivalent();
        });
    }

    /** MRR courant (équivalent mensuel maintenant). */
    public function current(?Collection $agences = null): int
    {
        return $this->at(now(), $agences);
    }
}
