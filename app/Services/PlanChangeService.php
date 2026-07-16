<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\SubscriptionPayment;
use Illuminate\Support\Facades\DB;

/**
 * Changement de plan d'une agence — LOGIQUE CENTRALISÉE.
 *
 * Règles arrêtées avec le fondateur :
 *  - UPGRADE   : effet immédiat, montant proratisé sur le reste du cycle en cours.
 *  - DOWNGRADE : effet au prochain cycle, aucun remboursement au prorata.
 *  - Legacy n'est ni une source ni une cible de ce mécanisme (plan figé).
 *
 * ⚠️ Le cycle (`date_fin_abonnement`) n'est JAMAIS déplacé par un changement de
 * plan : un upgrade ne doit pas offrir un mois supplémentaire ni raccourcir la
 * période déjà payée. Seul un renouvellement (activerAbonnement) bouge les dates.
 */
class PlanChangeService
{
    public function __construct(private PlanService $plans) {}

    /**
     * Un changement de plan est-il possible pour cet abonnement ?
     *
     * Legacy est figé : y toucher romprait l'engagement pris avec ces clients beta.
     * En dehors de ça, un abonnement actif ET un essai en cours sont recevables —
     * un essai n'a rien payé, il n'y a donc rien à proratiser, mais fixer son plan
     * détermine ses limites et le tarif qui lui sera facturé à la souscription.
     */
    public function peutChanger(Subscription $subscription): bool
    {
        return $subscription->plan_niveau !== Subscription::PLAN_LEGACY
            && ($subscription->estActif() || $subscription->estEnEssai());
    }

    /**
     * Montant proratisé dû pour un upgrade, sur les jours restants du cycle.
     *
     * On facture la DIFFÉRENCE entre le nouveau plan et l'ancien sur la période
     * restante : l'agence a déjà payé l'ancien plan jusqu'à l'échéance, lui
     * refacturer le nouveau plan en entier la ferait payer deux fois.
     */
    public function prorata(Subscription $subscription, Plan $nouveau): int
    {
        $fin = $subscription->date_fin_abonnement;
        $debut = $subscription->date_debut_abonnement;

        if (! $fin || ! $debut || now()->gte($fin)) {
            return 0;
        }

        $totalJours = max(1, (int) round($debut->diffInDays($fin)));
        $joursRestants = max(0, (int) round(now()->diffInDays($fin)));

        $cycle = $subscription->plan ?: 'mensuel';

        // Prix courant du nouveau plan (c'est une NOUVELLE souscription partielle),
        // mais montant DÉJÀ PAYÉ pour l'ancien (snapshot) : si le tarif de l'ancien
        // plan a changé depuis, l'agence ne doit être créditée que de ce qu'elle a
        // réellement versé.
        $prixNouveau = $nouveau->prix($cycle);
        $prixAncien  = (int) round((float) $subscription->montant_paye);

        $difference = $prixNouveau - $prixAncien;

        if ($difference <= 0) {
            return 0;
        }

        return (int) round($difference * $joursRestants / $totalJours);
    }

    /**
     * Applique un changement de plan.
     *
     * @return array{type:string,montant:int,effet:?\Carbon\Carbon}
     */
    public function changer(Subscription $subscription, string $niveauCible, ?int $userId): array
    {
        $nouveau = $this->plans->find($niveauCible);

        abort_if(! $nouveau || ! $nouveau->souscriptible, 422, 'Ce plan n\'est pas sélectionnable.');
        abort_if(! $this->peutChanger($subscription), 422, 'Le plan de cette agence ne peut pas être changé.');
        abort_if($subscription->plan_niveau === $niveauCible, 422, 'Cette agence est déjà sur le plan '.$nouveau->libelle.'.');

        $ancien = $subscription->plan_niveau;
        $estUpgrade = $this->plans->estUpgrade($ancien, $niveauCible);
        $enEssai = $subscription->estEnEssai();

        return DB::transaction(function () use ($subscription, $nouveau, $ancien, $niveauCible, $estUpgrade, $enEssai, $userId) {
            // En essai, il n'y a ni cycle ni montant versé : les deux sens
            // s'appliquent tout de suite. Programmer un downgrade « au prochain
            // cycle » n'aurait aucun sens ici — il n'y a pas de date d'échéance.
            if ($enEssai) {
                return $this->appliquerEnEssai($subscription, $nouveau, $ancien, $userId);
            }

            if ($estUpgrade) {
                return $this->appliquerUpgrade($subscription, $nouveau, $ancien, $userId);
            }

            return $this->programmerDowngrade($subscription, $nouveau, $ancien, $niveauCible, $userId);
        });
    }

    /**
     * Essai : on fixe simplement le plan visé. Pas de prorata ni de ligne de
     * paiement — rien n'a été versé. `montant_paye` reste nul : c'est
     * l'activation de l'abonnement qui figera le tarif du premier cycle.
     */
    private function appliquerEnEssai(Subscription $subscription, Plan $nouveau, ?string $ancien, ?int $userId): array
    {
        $subscription->update([
            'plan_niveau'          => $nouveau->niveau,
            'plan_niveau_prochain' => null,
        ]);

        $this->logger($subscription, $ancien, $nouveau->niveau, 'plan_essai_change', $userId, 0);

        return ['type' => 'essai', 'montant' => 0, 'effet' => now()];
    }

    /** Upgrade : immédiat, prorata facturé sur le reste du cycle. */
    private function appliquerUpgrade(Subscription $subscription, Plan $nouveau, ?string $ancien, ?int $userId): array
    {
        $montant = $this->prorata($subscription, $nouveau);

        $subscription->update([
            'plan_niveau'          => $nouveau->niveau,
            // Un upgrade annule un downgrade programmé qui n'a pas encore pris effet.
            'plan_niveau_prochain' => null,
            // Le cycle suivant sera facturé au tarif du nouveau plan.
            'montant_paye'         => $nouveau->prix($subscription->plan ?: 'mensuel'),
        ]);

        // Le prorata est un montant DÛ, pas encaissé : il entre dans le flux de
        // validation manuelle comme n'importe quelle autre déclaration. Ne jamais
        // l'inscrire « confirmé » — l'argent n'est pas arrivé.
        if ($montant > 0) {
            SubscriptionPayment::create([
                'subscription_id' => $subscription->id,
                'agency_id'       => $subscription->agency_id,
                'plan'            => $subscription->plan ?: 'mensuel',
                'plan_niveau'     => $nouveau->niveau,
                'montant'         => $montant,
                'statut'          => SubscriptionPayment::STATUT_EN_ATTENTE,
                'methode'         => 'manuel',
                'reference'       => 'PRORATA-'.now()->format('YmdHis'),
                'periode_debut'   => now(),
                'periode_fin'     => $subscription->date_fin_abonnement,
                'notes'           => 'Prorata upgrade '.$this->plans->find($ancien)?->libelle.' → '.$nouveau->libelle,
            ]);
        }

        $this->logger($subscription, $ancien, $nouveau->niveau, 'upgrade', $userId, $montant);

        return ['type' => 'upgrade', 'montant' => $montant, 'effet' => now()];
    }

    /** Downgrade : mémorisé, appliqué au prochain cycle, sans remboursement. */
    private function programmerDowngrade(Subscription $subscription, Plan $nouveau, ?string $ancien, string $niveauCible, ?int $userId): array
    {
        $subscription->update(['plan_niveau_prochain' => $niveauCible]);

        $this->logger($subscription, $ancien, $niveauCible, 'downgrade_programme', $userId, 0);

        return ['type' => 'downgrade', 'montant' => 0, 'effet' => $subscription->date_fin_abonnement];
    }

    /** Annule un downgrade qui n'a pas encore pris effet. */
    public function annulerDowngrade(Subscription $subscription, ?int $userId): void
    {
        if (! $subscription->plan_niveau_prochain) {
            return;
        }

        $cible = $subscription->plan_niveau_prochain;
        $subscription->update(['plan_niveau_prochain' => null]);

        $this->logger($subscription, $cible, $subscription->plan_niveau, 'downgrade_annule', $userId, 0);
    }

    /**
     * Trace le changement dans le journal d'activité.
     *
     * Écrit directement plutôt que via le trait LogsActivity du modèle : on veut
     * l'agence CIBLE dans agency_id (et non celle du superadmin, qui n'en a pas)
     * et un libellé métier plutôt qu'un diff de colonnes.
     */
    private function logger(Subscription $subscription, ?string $ancien, ?string $nouveau, string $action, ?int $userId, int $montant): void
    {
        ActivityLog::create([
            'agency_id'   => $subscription->agency_id,
            'user_id'     => $userId,
            'action'      => $action,
            'model_type'  => Subscription::class,
            'model_id'    => $subscription->id,
            'description' => sprintf(
                'Plan %s → %s%s',
                $this->plans->find($ancien)?->libelle ?? '—',
                $this->plans->find($nouveau)?->libelle ?? '—',
                $montant > 0 ? ' (prorata '.number_format($montant, 0, ',', ' ').' F)' : ''
            ),
            'properties'  => [
                'ancien_plan'  => $ancien,
                'nouveau_plan' => $nouveau,
                'prorata'      => $montant,
            ],
        ]);
    }
}
