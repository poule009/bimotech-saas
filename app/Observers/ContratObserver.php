<?php

namespace App\Observers;

use App\Models\Contrat;
use App\Services\FiscalService;

/**
 * ContratObserver — Calcule automatiquement les champs fiscaux
 * d'un contrat à la création et à la modification.
 *
 * RÈGLE ANTI-BOUCLE :
 *  - On utilise saving() (avant sauvegarde) plutôt que saved() (après)
 *  - saving() modifie l'objet en mémoire AVANT le SQL → 0 requête supplémentaire
 *  - Aucun save()/update() appelé depuis cet Observer → 0 risque de boucle
 *
 * RÈGLE DE PRIORITÉ :
 *  Si l'admin a explicitement saisi loyer_assujetti_tva = true/false via le formulaire,
 *  on respecte sa saisie. L'Observer ne s'active que si la valeur n'est pas encore définie
 *  ou si type_bail/meuble a changé.
 */
class ContratObserver
{
    /**
     * Déclenché AVANT la sauvegarde (create ou update).
     * Calcule les champs fiscaux en mémoire — aucune requête SQL supplémentaire.
     */
    public function saving(Contrat $contrat): void
    {
        // ── Calcul loyer_assujetti_tva et taux_tva_loyer ────────────────────
        // On recalcule si :
        //  - C'est une création (id null)
        //  - type_bail a changé
        //  - Le bien associé a changé (bien_id dirty)
        $doitRecalculerTva = ! $contrat->exists
            || $contrat->isDirty('type_bail')
            || $contrat->isDirty('bien_id');

        if ($doitRecalculerTva) {
            // Charger le bien si pas déjà en mémoire
            $bien = $contrat->bien ?? ($contrat->bien_id ? \App\Models\Bien::find($contrat->bien_id) : null);
            $estMeuble = (bool) ($bien?->meuble ?? false);

            $assujetti = FiscalService::loyerEstAssujetti(
                $contrat->type_bail ?? 'habitation',
                $estMeuble
            );

            // Ne pas écraser si l'admin a explicitement modifié ces champs
            // (isDirty après que l'Observer ait été attaché = valeur vient du formulaire)
            if (! $contrat->isDirty('loyer_assujetti_tva')) {
                $contrat->loyer_assujetti_tva = $assujetti;
                $contrat->taux_tva_loyer      = $assujetti ? 18.0 : 0.0;
            }
        }

        // ── Calcul brs_applicable depuis le locataire ────────────────────────
        $doitRecalculerBrs = ! $contrat->exists
            || $contrat->isDirty('locataire_id');

        if ($doitRecalculerBrs && ! $contrat->isDirty('brs_applicable')) {
            // Charger le locataire si pas en mémoire
            $locataireUser  = $contrat->locataire
                              ?? ($contrat->locataire_id ? \App\Models\User::find($contrat->locataire_id) : null);
            $locataireProfil = $locataireUser?->locataire;

            $contrat->brs_applicable = (bool) ($locataireProfil?->est_entreprise ?? false);
        }

        // ── Auto-calcul loyer_contractuel ────────────────────────────────────
        // Reprend la logique existante de Contrat::booted() mais gère la TVA loyer
        if ($contrat->loyer_nu && ! $contrat->isDirty('loyer_contractuel')) {
            $loyerNu    = (float) $contrat->loyer_nu;
            $tvaLoyer   = $contrat->loyer_assujetti_tva
                            ? round($loyerNu * ($contrat->taux_tva_loyer ?? 18.0) / 100, 2)
                            : 0.0;
            $charges    = (float) ($contrat->charges_mensuelles ?? 0);
            $tom        = (float) ($contrat->tom_amount ?? 0);

            $contrat->loyer_contractuel = round($loyerNu + $tvaLoyer + $charges + $tom, 2);
        }
    }
}