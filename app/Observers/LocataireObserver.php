<?php

namespace App\Observers;

use App\Models\Contrat;
use App\Models\Locataire;
use App\Services\FiscalService;

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
     * Propagule les changements fiscaux aux contrats actifs.
     */
    public function updated(Locataire $locataire): void
    {
        // ── GARDE : ne déclencher la cascade QUE si un champ fiscal a changé ──
        $champsQuiTriggerent = ['est_entreprise', 'taux_brs_override', 'type_locataire'];
        $changed = array_intersect($champsQuiTriggerent, array_keys($locataire->getChanges()));

        if (empty($changed)) {
            return; // Rien de fiscal n'a changé → sortie immédiate, 0 requête
        }

        // ── Calcul du nouveau taux BRS par défaut pour ce locataire ──────────
        $nouveauTauxBrsDefaut = FiscalService::tauxBrs(
            estEntreprise:    (bool)  $locataire->est_entreprise,
            overrideLocataire: $locataire->taux_brs_override ? (float) $locataire->taux_brs_override : null,
        );

        // ── Mise à jour des contrats actifs EN MASSE avec withoutEvents() ─────
        // withoutEvents() → n'active ni ContratObserver ni LogsActivity sur les contrats
        // update() en masse → une seule requête SQL, pas de N×save()
        Contrat::withoutEvents(function () use ($locataire, $nouveauTauxBrsDefaut) {
            Contrat::where('locataire_id', $locataire->user_id)
                   ->where('statut', 'actif')
                   // Ne mettre à jour taux_brs que si pas d'override contrat spécifique
                   ->whereNull('taux_brs_manuel')
                   ->update([
                       'brs_applicable' => (bool) $locataire->est_entreprise,
                       // taux_brs_manuel reste null → FiscalService utilisera la cascade
                   ]);
        });

        // ── Log métier explicite ──────────────────────────────────────────────
        // On logue NOUS-MÊMES plutôt que de laisser LogsActivity le faire N fois
        \App\Models\ActivityLog::create([
            'user_id' => \Illuminate\Support\Facades\Auth::id(),
            'agency_id'   => $locataire->user?->agency_id,
            'action'      => 'updated',
            'description' => sprintf(
                'Locataire #%s : statut fiscal modifié → %s (BRS: %s%%)',
                $locataire->user_id,
                $locataire->est_entreprise ? 'Entreprise' : 'Particulier',
                $nouveauTauxBrsDefaut
            ),
            'model_type'  => Locataire::class,
            'model_id'    => $locataire->id,
            'ip_address'  => request()?->ip(),
        ]);
    }
}