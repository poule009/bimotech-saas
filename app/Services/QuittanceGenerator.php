<?php

namespace App\Services;

use App\Models\Contrat;
use App\Models\Paiement;
use Carbon\Carbon;

/**
 * Génération d'une quittance (Paiement) pour un contrat sur une période donnée.
 *
 * Source unique de la logique : utilisée à la fois par la commande rent:generate
 * (batch mensuel) et à la création d'un contrat (pour que la ligne existe tout de
 * suite et que Quittances = Dashboard, sans attendre le prochain batch).
 */
class QuittanceGenerator
{
    /** Relations nécessaires au calcul fiscal. */
    public const RELATIONS = [
        'bien:id,agency_id,proprietaire_id,taux_commission,meuble,type',
        'bien.proprietaire:id',
        // Profil propriétaire : assujettissement TVA (F2) + personne morale IS (BRS, F4)
        'bien.proprietaire.proprietaire:user_id,assujetti_tva,est_personne_morale_is',
        // Agence : assujettissement TVA (F2) pour la TVA commission/frais
        'agency:id,assujetti_tva',
        'locataire:id,name',
        'locataire.locataire:user_id,est_entreprise,taux_brs_override',
    ];

    /**
     * Génère la quittance du contrat pour la période (statut unpaid).
     * Idempotent : retourne null si une quittance non annulée existe déjà.
     *
     * @throws \Throwable si le calcul fiscal échoue (à gérer par l'appelant).
     */
    public function genererPourContrat(Contrat $contrat, Carbon $periode, string $source = 'système'): ?Paiement
    {
        $existe = Paiement::withoutGlobalScopes()
            ->where('contrat_id', $contrat->id)
            ->whereYear('periode', $periode->year)
            ->whereMonth('periode', $periode->month)
            ->where('statut', '!=', 'annule')
            ->exists();

        if ($existe) {
            return null;
        }

        // Les relations doivent être chargées pour FiscalContext.
        if (! $contrat->relationLoaded('bien') || ! $contrat->relationLoaded('locataire')) {
            $contrat->load(self::RELATIONS);
        }

        $ctx    = FiscalContext::fromContrat($contrat);
        $result = FiscalService::calculer($ctx);

        return Paiement::create([
            'agency_id'                 => $contrat->agency_id,
            'contrat_id'                => $contrat->id,
            'periode'                   => $periode->toDateString(),
            // Ventilation loyer
            'loyer_ht'                  => $result->loyerHt,
            'tva_loyer'                 => $result->tvaLoyer,
            'loyer_ttc'                 => $result->loyerTtc,
            'loyer_nu'                  => $result->loyerHt,
            'charges_amount'            => $result->chargesAmount,
            'tva_charges'               => $result->tvaCharges,
            'charges_ttc'               => $result->chargesTtc,
            'tom_amount'                => $result->tomAmount,
            'montant_encaisse'          => $result->montantEncaisse,
            // Commission
            'mode_paiement'             => 'virement',
            'taux_commission_applique'  => $ctx->tauxCommission,
            'commission_agence'         => $result->commissionHt,
            'tva_commission'            => $result->tvaCommission,
            'commission_ttc'            => $result->commissionTtc,
            // Nets
            'net_proprietaire'          => $result->netProprietaire,
            'brs_amount'                => $result->brsAmount,
            'taux_brs_applique'         => $result->tauxBrsApplique,
            'net_a_verser_proprietaire' => $result->netAVerserProprietaire,
            // Colonne canonique lue par la compta (= net + caution si remise ; identique
            // à netAVerser pour un loyer mensuel). Même source que FiscalResult::toPaiementFields.
            'montant_net_bailleur'      => $result->netBailleur,
            // Snapshot fiscal (le cast 'array' du modèle sérialise — ne pas json_encode ici)
            'regime_fiscal_snapshot'    => $result->toArray(),
            // Divers
            'reference_bail'            => $contrat->reference_bail_affichee,
            'caution_percue'            => 0,
            'est_premier_paiement'      => false,
            'date_paiement'             => $periode->toDateString(),
            'reference_paiement'        => null,
            'statut'                    => 'unpaid',
            'notes'                     => 'Généré (' . $source . ') le ' . now()->format('d/m/Y H:i'),
        ]);
    }
}
