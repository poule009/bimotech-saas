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
            $bien = $contrat->bien ?? ($contrat->bien_id
                ? \App\Models\Bien::withoutGlobalScopes()->find($contrat->bien_id)
                : null);
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

        // ── charges_assujetties_tva depuis mode_facturation_charges ──────────
        // Règle NON VÉRIFIÉE (regles_fiscales, clé 'tva_charges') :
        //   forfait → 18% (charges_assujetties_tva = true)
        //   debours → 0%  (charges_assujetties_tva = false)
        // Ne s'active que si le mode est renseigné ET que charges_assujetties_tva
        // n'a pas été fixé explicitement dans la même opération (priorité à la saisie).
        if ($contrat->mode_facturation_charges !== null
            && ! $contrat->isDirty('charges_assujetties_tva')) {
            $contrat->charges_assujetties_tva = $contrat->mode_facturation_charges === 'forfait';
        }

        // ── brs_applicable depuis le PROPRIÉTAIRE (bailleur) ─────────────────
        // B2 (bug corrigé) : la BRS s'applique quand le BAILLEUR est une personne
        // physique (Art. 201 §2 CGI SN) — JAMAIS selon le statut du locataire.
        // On dérive donc depuis le profil propriétaire du bien.
        $doitRecalculerBrs = ! $contrat->exists
            || $contrat->isDirty('bien_id');

        if ($doitRecalculerBrs && ! $contrat->isDirty('brs_applicable')) {
            $bienBrs = $contrat->bien ?? ($contrat->bien_id
                ? \App\Models\Bien::withoutGlobalScopes()->find($contrat->bien_id)
                : null);
            // Profil propriétaire : User->proprietaire (HasOne), lu SANS global scope
            // (sinon le scope agency le filtre sous session authentifiée → null).
            $profilProprio = $bienBrs?->proprietaire?->proprietaire
                ?? ($bienBrs?->proprietaire_id
                    ? \App\Models\Proprietaire::withoutGlobalScopes()
                        ->where('user_id', $bienBrs->proprietaire_id)
                        ->first()
                    : null);
            // BRS applicable si personne physique (défaut) ET pas de dispense (B3).
            $estMorale = (bool) ($profilProprio?->est_personne_morale_is ?? false);
            $dispense  = (bool) ($profilProprio?->brs_dispense ?? false);
            $contrat->brs_applicable = ! $estMorale && ! $dispense;
        }

        // ── Droits d'enregistrement DGID (tracker contrat) ───────────────────
        // Calculé DEPUIS LES CHAMPS DU CONTRAT (pas de dépendance locataire/bailleur
        // → aucun risque du type B2). Recalcule quand une entrée change.
        $doitRecalculerDgid = ! $contrat->exists
            || $contrat->isDirty(['loyer_nu', 'charges_mensuelles', 'date_debut', 'date_fin',
                'droit_enreg_nombre_feuilles', 'droit_enreg_renouvelable',
                'taux_enregistrement_dgid', 'enregistrement_exonere']);

        if ($doitRecalculerDgid) {
            $baseMensuelle = (float) ($contrat->loyer_nu ?? 0) + (float) ($contrat->charges_mensuelles ?? 0);
            $feuilles      = max(1, (int) ($contrat->droit_enreg_nombre_feuilles ?? 2));
            $timbre        = FiscalService::DGID_TIMBRE_FISCAL * $feuilles;

            // Durée du bail en mois (date_fin nulle = bail à durée indéterminée → base 12).
            $dureeMois = ($contrat->date_debut && $contrat->date_fin)
                ? max(1, (int) $contrat->date_debut->diffInMonths($contrat->date_fin))
                : 12;

            // Base et statut selon la durée (R4/R5/R6) :
            //  - > 12 mois  → base 12 mois, ESTIMATION (fractionnement triennal non confirmé)
            //  - = 12 mois ou ≤ 12 renouvelable → base 12 mois, confirmé (cas standard SN)
            //  - < 12 mois non renouvelable → prorata sur la durée réelle, confirmé
            if ($dureeMois > 12) {
                $baseMois = 12;
                $statut   = 'estimation';
            } elseif ($dureeMois === 12 || (bool) ($contrat->droit_enreg_renouvelable ?? true)) {
                $baseMois = 12;
                $statut   = 'confirme';
            } else {
                $baseMois = $dureeMois;
                $statut   = 'confirme';
            }

            $exonere = (bool) ($contrat->enregistrement_exonere ?? false);
            $taux    = $contrat->taux_enregistrement_dgid !== null
                ? (float) $contrat->taux_enregistrement_dgid
                : FiscalService::DGID_TAUX_HABITATION; // 2% — Art. 472 IV.6

            $contrat->droit_enreg_montant       = $exonere ? 0.0 : round($baseMensuelle * $baseMois * ($taux / 100), 2);
            $contrat->droit_enreg_timbre        = $exonere ? 0.0 : round($timbre, 2);
            $contrat->droit_enreg_statut_calcul = $statut;
            $contrat->droit_enreg_date_limite   = $contrat->date_debut
                ? $contrat->date_debut->copy()->addMonth()->toDateString()
                : null;
        }

        // ── Auto-calcul loyer_contractuel ────────────────────────────────────
        // loyer_contractuel = loyer_nu + charges + tom (SANS TVA)
        // La TVA est calculée dynamiquement par FiscalService à chaque paiement.
        // L'inclure ici créerait une incohérence avec ContratController et l'assiette DGID.
        if ($contrat->loyer_nu && ! $contrat->isDirty('loyer_contractuel')) {
            $loyerNu = (float) $contrat->loyer_nu;
            $charges = (float) ($contrat->charges_mensuelles ?? 0);
            $tom     = (float) ($contrat->tom_amount ?? 0);

            $contrat->loyer_contractuel = round($loyerNu + $charges + $tom, 2);
        }
    }
}