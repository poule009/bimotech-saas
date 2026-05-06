<?php

namespace App\Services;

use App\Models\Paiement;
use App\Models\TvaDeclaration;
use Illuminate\Support\Facades\Log;

class TvaAgenceService
{
    /**
     * Agrège la TVA collectée depuis la table paiements pour un mois donné.
     *
     * @return array{
     *   tva_commissions: float,
     *   tva_loyers_commerciaux: float,
     *   tva_charges_forfait: float,
     *   tva_honoraires: float,
     *   total_tva_collectee: float,
     *   nombre_paiements: int,
     *   detail_par_contrat: array
     * }
     */
    public function calculerTvaCollectee(int $agencyId, int $mois, int $annee): array
    {
        $paiements = Paiement::with(['contrat.locataire', 'contrat.bien'])
            ->where('agency_id', $agencyId)
            ->whereYear('date_paiement', $annee)
            ->whereMonth('date_paiement', $mois)
            ->where('statut', '!=', 'annule')
            ->get();

        $tvaCommissions       = (float) $paiements->sum('tva_commission');
        $tvaLoyersCommerciaux = (float) $paiements->sum('tva_loyer');
        $tvaChargesForfait    = (float) $paiements->sum('tva_charges');
        $tvaHonoraires        = (float) $paiements->sum('tva_frais_agence');
        $total                = $tvaCommissions + $tvaLoyersCommerciaux + $tvaChargesForfait + $tvaHonoraires;

        $detail = $paiements->map(function ($p) {
            $bien      = $p->contrat?->bien;
            $locataire = $p->contrat?->locataire;

            return [
                'id'             => $p->id,
                'reference'      => $p->reference_paiement ?? "PAY-{$p->id}",
                'locataire'      => $locataire?->name ?? '—',
                'bien'           => $bien?->reference ?? $bien?->titre ?? $bien?->adresse ?? '—',
                'type_bail'      => $p->contrat?->type_bail ?? '—',
                'periode'        => $p->periode,
                'date_paiement'  => $p->date_paiement,
                'loyer_ht'       => (float) ($p->loyer_ht ?? $p->loyer_nu ?? 0),
                'commission_ht'  => (float) ($p->commission_agence ?? 0),
                'tva_commission' => (float) ($p->tva_commission ?? 0),
                'tva_loyer'      => (float) ($p->tva_loyer ?? 0),
                'tva_charges'    => (float) ($p->tva_charges ?? 0),
                'tva_frais'      => (float) ($p->tva_frais_agence ?? 0),
            ];
        })->values()->toArray();

        return [
            'tva_commissions'        => round($tvaCommissions, 2),
            'tva_loyers_commerciaux' => round($tvaLoyersCommerciaux, 2),
            'tva_charges_forfait'    => round($tvaChargesForfait, 2),
            'tva_honoraires'         => round($tvaHonoraires, 2),
            'total_tva_collectee'    => round($total, 2),
            'nombre_paiements'       => $paiements->count(),
            'detail_par_contrat'     => $detail,
        ];
    }

    /**
     * Crée ou met à jour le brouillon de la déclaration TVA du mois donné.
     * - Ne touche pas les champs TVA déductible.
     * - Injecte le crédit reporté du mois précédent (si non encore défini).
     * - Ne modifie pas une déclaration déjà déposée.
     */
    public function creerOuMettreAJour(int $agencyId, int $mois, int $annee): TvaDeclaration
    {
        $tvaData = $this->calculerTvaCollectee($agencyId, $mois, $annee);

        $declaration = TvaDeclaration::firstOrNew([
            'agency_id' => $agencyId,
            'mois'      => $mois,
            'annee'     => $annee,
        ]);

        if ($declaration->exists && $declaration->statut === 'deposee') {
            return $declaration;
        }

        $estNouveau = ! $declaration->exists;

        $declaration->fill([
            'tva_commissions'        => $tvaData['tva_commissions'],
            'tva_loyers_commerciaux' => $tvaData['tva_loyers_commerciaux'],
            'tva_charges_forfait'    => $tvaData['tva_charges_forfait'],
            'tva_honoraires'         => $tvaData['tva_honoraires'],
            'total_tva_collectee'    => $tvaData['total_tva_collectee'],
        ]);

        // Injecter le crédit du mois précédent uniquement à la création
        if ($estNouveau) {
            $declaration->credit_reporte_entrant = $this->creditMoisPrecedent($agencyId, $mois, $annee);
        }

        $declaration->save();
        $declaration->calculerTvaNette();

        return $declaration->fresh();
    }

    /** Récupère le crédit_reporte_sortant de la déclaration du mois précédent. */
    private function creditMoisPrecedent(int $agencyId, int $mois, int $annee): float
    {
        $moisPrecedent  = $mois === 1 ? 12 : $mois - 1;
        $anneePrecedente = $mois === 1 ? $annee - 1 : $annee;

        $decl = TvaDeclaration::where('agency_id', $agencyId)
            ->where('mois', $moisPrecedent)
            ->where('annee', $anneePrecedente)
            ->first();

        return (float) ($decl?->credit_reporte_sortant ?? 0);
    }
}
