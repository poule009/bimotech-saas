<?php

namespace App\Services;

use App\Models\Agency;
use App\Models\CommissionSnapshot;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * Commissions des collaborateurs (module « Équipe interne »).
 *
 * Base de calcul (brief) : MRR ACTUEL des agences attribuées au collaborateur,
 * indépendamment du statut de paiement du mois courant — une agence en échec de
 * paiement compte quand même, car mrrEquivalent() s'appuie sur le montant réellement
 * facturé au dernier cycle (snapshot), sans fenêtre de dates.
 *
 * Formule : commission = taux_commission × Σ(MRR des agences où amenee_par = collaborateur)
 * Le taux est propre à chaque collaborateur (pas de constante globale).
 */
class CommissionService
{
    /**
     * Ligne live de commission d'un collaborateur (mois courant, non figé).
     *
     * @return array{nb_agences:int, mrr_total:int, taux:float, commission:int}
     */
    public function actuelle(User $collaborateur): array
    {
        $agences = $this->agencesDe($collaborateur);

        $mrr = (int) $agences->sum(function (Agency $a) {
            $s = $a->subscription;
            // Même règle d'assiette que MrrService : plans payants uniquement, Legacy exclu.
            if (! $s || ! in_array($s->plan_niveau, ['starter', 'pro', 'agence'], true)) {
                return 0;
            }

            return $s->mrrEquivalent();
        });

        $taux = $collaborateur->tauxCommission();

        return [
            'nb_agences'  => $agences->count(),
            'mrr_total'   => $mrr,
            'taux'        => $taux,
            'commission'  => (int) round($mrr * $taux / 100),
        ];
    }

    /**
     * Agences attribuées à un collaborateur, hors scope de périmètre (calcul plateforme).
     *
     * @return Collection<int,Agency>
     */
    public function agencesDe(User $collaborateur): Collection
    {
        return Agency::sansPerimetre()
            ->where('amenee_par', $collaborateur->id)
            ->with('subscription')
            ->get();
    }

    /**
     * Capture le point d'historique du mois courant pour un collaborateur (idempotent).
     * Un mois déjà figé n'est PAS écrasé rétroactivement : on ne rafraîchit que le mois
     * en cours, exactement comme le snapshot MRR.
     */
    public function capturer(User $collaborateur, ?\Carbon\Carbon $mois = null): CommissionSnapshot
    {
        $mois = ($mois ?? now())->copy()->startOfMonth();
        $ligne = $this->actuelle($collaborateur);

        return CommissionSnapshot::updateOrCreate(
            ['collaborateur_id' => $collaborateur->id, 'mois' => $mois->toDateString()],
            [
                'nb_agences' => $ligne['nb_agences'],
                'mrr_total'  => $ligne['mrr_total'],
                'taux'       => $ligne['taux'],
                'commission' => $ligne['commission'],
            ]
        );
    }

    /**
     * Historique mensuel d'un collaborateur (le plus récent d'abord), avec le mois
     * courant calculé en direct s'il n'a pas encore été capturé.
     *
     * @return Collection<int,array{mois:\Carbon\Carbon, nb_agences:int, mrr_total:int, taux:float, commission:int, fige:bool}>
     */
    public function historique(User $collaborateur): Collection
    {
        $snapshots = CommissionSnapshot::where('collaborateur_id', $collaborateur->id)
            ->orderByDesc('mois')
            ->get();

        $moisCourant = now()->startOfMonth();
        $aDejaCourant = $snapshots->contains(fn (CommissionSnapshot $s) => $s->mois->isSameMonth($moisCourant));

        $lignes = $snapshots->map(fn (CommissionSnapshot $s) => [
            'mois'       => $s->mois,
            'nb_agences' => $s->nb_agences,
            'mrr_total'  => $s->mrr_total,
            'taux'       => (float) $s->taux,
            'commission' => $s->commission,
            'fige'       => true,
        ]);

        // Mois courant live en tête s'il n'a pas encore de snapshot figé.
        if (! $aDejaCourant) {
            $live = $this->actuelle($collaborateur);
            $lignes = $lignes->prepend([
                'mois'       => $moisCourant,
                'nb_agences' => $live['nb_agences'],
                'mrr_total'  => $live['mrr_total'],
                'taux'       => $live['taux'],
                'commission' => $live['commission'],
                'fige'       => false,
            ]);
        }

        return $lignes->values();
    }
}
