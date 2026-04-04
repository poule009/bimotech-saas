<?php

namespace App\Services;

use InvalidArgumentException;

/**
 * FiscalService — Moteur de calcul fiscal pour le marché sénégalais.
 *
 * Références légales :
 *  - TVA 18%         : Code Général des Impôts (CGI), article 357
 *  - TOM             : Taxe sur les Opérations Mobilières (variable selon commune)
 *  - Commission HT   : Base de facturation de l'agence hors taxes
 *  - Loi 81-18       : Encadrement des loyers au Sénégal
 *  - NINEA           : Numéro d'Identification Nationale des Entreprises et Associations
 *
 * IMPORTANT : Toutes les méthodes sont pures (pas d'effets de bord, pas d'I/O).
 * Elles reçoivent des scalaires et retournent des tableaux de résultats.
 * Cela les rend 100% testables unitairement sans base de données.
 */
class FiscalService
{
    // ─── Constantes fiscales ────────────────────────────────────────────────

    public const TVA_TAUX          = 0.18;   // 18% — CGI art. 357
    public const TOM_TAUX_DEFAUT   = 0.05;   // 5% (Dakar) — variable selon commune
    public const COMMISSION_TAUX   = 0.10;   // 10% du loyer brut (standard marché SN)

    // Tranches loi 81-18 (plafonds loyer mensuel en FCFA selon surface)
    // Ces plafonds sont indicatifs et à ajuster selon les arrêtés en vigueur
    public const LOI_8118_TRANCHES = [
        ['surface_max' => 60,   'loyer_max' => 150_000],
        ['surface_max' => 100,  'loyer_max' => 300_000],
        ['surface_max' => 150,  'loyer_max' => 500_000],
        ['surface_max' => null, 'loyer_max' => null],    // Au-delà : libre
    ];

    // ─── API principale ─────────────────────────────────────────────────────

    /**
     * Calcule la décomposition fiscale complète d'un loyer mensuel.
     *
     * @param  float  $loyerHorsCharges   Loyer de base HT (FCFA)
     * @param  float  $charges            Charges mensuelles (FCFA)
     * @param  float  $tauxTom            Taux TOM local (défaut 5%)
     * @param  float  $tauxCommission     Taux commission agence (défaut 10%)
     * @return array  Décomposition complète avec tous les montants
     */
    public function calculerDecompositionLoyer(
        float $loyerHorsCharges,
        float $charges = 0,
        float $tauxTom = self::TOM_TAUX_DEFAUT,
        float $tauxCommission = self::COMMISSION_TAUX
    ): array {
        $this->validerMontant($loyerHorsCharges, 'loyer_hors_charges');
        $this->validerMontant($charges, 'charges');
        $this->validerTaux($tauxTom, 'taux_tom');
        $this->validerTaux($tauxCommission, 'taux_commission');

        $loyerBrut       = $loyerHorsCharges + $charges;
        $commissionHt    = round($loyerHorsCharges * $tauxCommission);
        $tva             = round($commissionHt * self::TVA_TAUX);
        $commissionTtc   = $commissionHt + $tva;
        $tom             = round($loyerHorsCharges * $tauxTom);
        $netProprietaire = $loyerHorsCharges - $commissionHt - $tom;

        return [
            // Montants bruts
            'loyer_hors_charges'  => round($loyerHorsCharges),
            'charges'             => round($charges),
            'loyer_brut'          => round($loyerBrut),

            // Commission agence
            'commission_taux'     => $tauxCommission,
            'commission_ht'       => $commissionHt,
            'tva_taux'            => self::TVA_TAUX,
            'tva_montant'         => $tva,
            'commission_ttc'      => $commissionTtc,

            // Taxe sur les Opérations Mobilières
            'tom_taux'            => $tauxTom,
            'tom_montant'         => $tom,

            // Ce que reçoit réellement le propriétaire
            'net_proprietaire'    => $netProprietaire,

            // Ce que paie le locataire
            'total_locataire'     => round($loyerBrut),

            // Ratios pour les rapports
            'ratio_commission'    => $loyerHorsCharges > 0
                ? round(($commissionTtc / $loyerHorsCharges) * 100, 2)
                : 0,
        ];
    }

    /**
     * Calcule le dépôt de garantie selon la loi 81-18.
     * Plafond légal : 2 mois de loyer hors charges.
     *
     * @param  float  $loyerHorsCharges
     * @param  int    $moisDemandes      Nombre de mois souhaités (1 ou 2)
     * @return array
     */
    public function calculerDepotGarantie(float $loyerHorsCharges, int $moisDemandes = 2): array
    {
        $this->validerMontant($loyerHorsCharges, 'loyer_hors_charges');

        $moisLegal = min($moisDemandes, 2); // Loi 81-18 : maximum 2 mois
        $montant   = round($loyerHorsCharges * $moisLegal);

        return [
            'mois_demandes'    => $moisDemandes,
            'mois_appliques'   => $moisLegal,
            'montant'          => $montant,
            'conforme_loi8118' => $moisDemandes <= 2,
            'avertissement'    => $moisDemandes > 2
                ? 'La loi 81-18 plafonne le dépôt de garantie à 2 mois de loyer hors charges.'
                : null,
        ];
    }

    /**
     * Vérifie la conformité d'un loyer avec la loi 81-18.
     *
     * @param  float  $loyerHorsCharges
     * @param  float  $surface           Surface en m²
     * @param  string $type              Type de bien (appartement, villa, etc.)
     * @return array
     */
    public function verifierConformiteLoi8118(
        float $loyerHorsCharges,
        float $surface,
        string $type = 'appartement'
    ): array {
        // Les locaux commerciaux et bureaux ne sont pas soumis à la loi 81-18
        $typesExclus = ['bureau', 'commerce', 'terrain'];
        if (in_array($type, $typesExclus)) {
            return [
                'soumis_loi8118' => false,
                'conforme'       => true,
                'motif'          => "Les biens de type '{$type}' ne sont pas soumis à la loi 81-18.",
                'loyer_max'      => null,
            ];
        }

        $loyerMax = null;
        foreach (self::LOI_8118_TRANCHES as $tranche) {
            if ($tranche['surface_max'] === null || $surface <= $tranche['surface_max']) {
                $loyerMax = $tranche['loyer_max'];
                break;
            }
        }

        // Surface très grande : loyer libre
        if ($loyerMax === null) {
            return [
                'soumis_loi8118' => true,
                'conforme'       => true,
                'motif'          => 'Surface > 150 m² : loyer libre (hors plafond loi 81-18).',
                'loyer_max'      => null,
            ];
        }

        $conforme = $loyerHorsCharges <= $loyerMax;

        return [
            'soumis_loi8118' => true,
            'conforme'       => $conforme,
            'loyer_propose'  => round($loyerHorsCharges),
            'loyer_max'      => $loyerMax,
            'ecart'          => $conforme ? 0 : round($loyerHorsCharges - $loyerMax),
            'motif'          => $conforme
                ? "Loyer conforme (≤ {$loyerMax} FCFA pour {$surface} m²)."
                : "Loyer non conforme : dépasse le plafond de " . number_format($loyerMax, 0, ',', ' ') . " FCFA pour {$surface} m².",
        ];
    }

    /**
     * Calcule les frais d'état des lieux.
     * Standard marché sénégalais : 1 mois de loyer HT partagé 50/50.
     *
     * @param  float $loyerHorsCharges
     * @return array
     */
    public function calculerFraisEtatDesLieux(float $loyerHorsCharges): array
    {
        $total              = round($loyerHorsCharges);
        $partLocataire      = round($total / 2);
        $partProprietaire   = $total - $partLocataire; // Évite les arrondis impairs

        return [
            'total'             => $total,
            'part_locataire'    => $partLocataire,
            'part_proprietaire' => $partProprietaire,
        ];
    }

    /**
     * Génère le récapitulatif financier annuel pour les rapports.
     *
     * @param  float  $loyerHorsCharges
     * @param  float  $charges
     * @param  int    $moisOccupes       Nombre de mois effectivement occupés
     * @param  float  $tauxTom
     * @param  float  $tauxCommission
     * @return array
     */
    public function calculerBilanAnnuel(
        float $loyerHorsCharges,
        float $charges = 0,
        int   $moisOccupes = 12,
        float $tauxTom = self::TOM_TAUX_DEFAUT,
        float $tauxCommission = self::COMMISSION_TAUX
    ): array {
        $mensuel = $this->calculerDecompositionLoyer(
            $loyerHorsCharges,
            $charges,
            $tauxTom,
            $tauxCommission
        );

        $multiplicateur = max(0, min(12, $moisOccupes));

        return [
            'mois_occupes'           => $multiplicateur,
            'taux_occupation'        => round(($multiplicateur / 12) * 100, 1),

            // Annualisations
            'loyer_brut_annuel'      => $mensuel['loyer_brut'] * $multiplicateur,
            'commission_ht_annuel'   => $mensuel['commission_ht'] * $multiplicateur,
            'tva_annuel'             => $mensuel['tva_montant'] * $multiplicateur,
            'commission_ttc_annuel'  => $mensuel['commission_ttc'] * $multiplicateur,
            'tom_annuel'             => $mensuel['tom_montant'] * $multiplicateur,
            'net_proprietaire_annuel'=> $mensuel['net_proprietaire'] * $multiplicateur,

            // Détail mensuel inclus
            'mensuel'                => $mensuel,
        ];
    }

    // ─── Méthodes utilitaires ───────────────────────────────────────────────

    /**
     * Formate un montant en FCFA avec séparateurs de milliers.
     */
    public function formaterFCFA(float $montant): string
    {
        return number_format(round($montant), 0, ',', ' ') . ' FCFA';
    }

    /**
     * Formate un taux en pourcentage.
     */
    public function formaterTaux(float $taux): string
    {
        return number_format($taux * 100, 0) . '%';
    }

    // ─── Validations internes ───────────────────────────────────────────────

    private function validerMontant(float $valeur, string $champ): void
    {
        if ($valeur < 0) {
            throw new InvalidArgumentException(
                "Le champ '{$champ}' ne peut pas être négatif. Valeur reçue : {$valeur}"
            );
        }
    }

    private function validerTaux(float $taux, string $champ): void
    {
        if ($taux < 0 || $taux > 1) {
            throw new InvalidArgumentException(
                "Le taux '{$champ}' doit être compris entre 0 et 1. Valeur reçue : {$taux}"
            );
        }
    }
}