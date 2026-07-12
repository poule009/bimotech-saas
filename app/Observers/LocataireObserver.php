<?php

namespace App\Observers;

use App\Models\Locataire;

/**
 * LocataireObserver — Maintient la cohérence fiscale des contrats
 * quand le profil d'un locataire est modifié.
 *
 * RÈGLE ANTI-BOUCLE :
 *  1. On vérifie wasChanged() AVANT d'agir → sortie immédiate si rien de fiscal n'a changé
 *  2. On utilise withoutEvents() pour la mise à jour des contrats → évite de déclencher
 *     ContratObserver::updated() et LogsActivity::updated sur chaque contrat mis à jour
 *  3. On utilise whereIn() + update() en masse → une seule requête SQL, pas de foreach
 *
 * IMMUTABILITÉ COMPTABLE :
 *  Les paiements EXISTANTS ne sont jamais modifiés par cet Observer.
 *  Seuls les contrats (paramètres pour paiements FUTURS) sont mis à jour.
 */
class LocataireObserver
{
    /**
     * Déclenché après la mise à jour d'un locataire.
     *
     * CORRECTIF B2 : cet observer NE touche PLUS contrat.brs_applicable.
     *  - L'APPLICABILITÉ de la BRS dépend du BAILLEUR (personne physique),
     *    jamais du locataire (Art. 201 §2 CGI SN). La propager depuis
     *    est_entreprise était le bug B2 (côté locataire).
     *  - Le taux_brs_override (cascade niveau 2) est lu EN DIRECT par
     *    FiscalContext::fromContrat à chaque calcul → aucune propagation
     *    vers les contrats n'est nécessaire.
     * Il ne reste donc qu'une trace d'audit, sans aucune mutation de contrat.
     */
    public function updated(Locataire $locataire): void
    {
        // Ne tracer que si un champ fiscal du locataire a changé.
        $champsFiscaux = ['est_entreprise', 'taux_brs_override', 'type_locataire'];
        if (empty(array_intersect($champsFiscaux, array_keys($locataire->getChanges())))) {
            return;
        }

        // Trace d'audit uniquement (aucun impact sur brs_applicable des contrats).
        \App\Models\ActivityLog::create([
            'user_id'     => \Illuminate\Support\Facades\Auth::id(),
            'agency_id'   => $locataire->user?->agency_id,
            'action'      => 'updated',
            'description' => sprintf(
                'Locataire #%s : profil fiscal modifié → %s (taux BRS override: %s)',
                $locataire->user_id,
                $locataire->est_entreprise ? 'Entreprise' : 'Particulier',
                $locataire->taux_brs_override !== null ? $locataire->taux_brs_override . '%' : 'aucun'
            ),
            'model_type'  => Locataire::class,
            'model_id'    => $locataire->id,
            'ip_address'  => request()?->ip(),
        ]);
    }
}