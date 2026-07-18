<?php

namespace App\Support;

use App\Models\RegleFiscale;
use App\Services\FiscalService;

/**
 * RegleFiscaleCatalogue — pont LECTURE SEULE entre le référentiel documentaire
 * (table regles_fiscales) et les valeurs réellement appliquées par le moteur
 * (constantes de FiscalService).
 *
 * Choix d'architecture (v1 documentaire, cf. brief « Règles fiscales ») :
 * la valeur affichée dans l'admin n'est JAMAIS stockée ni éditable — elle est
 * dérivée ici des constantes du moteur, à la clé (`cle`) de la règle. Source de
 * vérité unique = FiscalService : aucun risque de dérive entre l'admin et le
 * calcul, et aucune modification du noyau fiscal (lourdement testé).
 *
 * Rendre la valeur éditable (l'admin pilotant le moteur) est le chemin
 * « pleine édition » explicitement écarté en v1 : il impliquerait que
 * FiscalService lise ses taux depuis la table.
 */
class RegleFiscaleCatalogue
{
    /**
     * Regroupement des catégories techniques en familles de filtre
     * (décision validée : CFPB+TEOM fusionnés, IS+CEL fusionnés).
     */
    public const GROUPES = [
        'brs'                  => 'BRS',
        'irpp'                 => 'IRPP',
        'cgf'                  => 'CGF',
        'cfpb_teom'            => 'CFPB / TEOM',
        'droits_enregistrement' => 'Droits d\'enregistrement',
        'tva'                  => 'TVA',
        'agence'               => 'Agence (IS / CEL)',
    ];

    /** Mappe une catégorie technique de la règle vers son groupe de filtre. */
    public static function groupe(string $categorie): string
    {
        return match ($categorie) {
            'cfpb', 'teom' => 'cfpb_teom',
            'is', 'cel'    => 'agence',
            default        => $categorie,
        };
    }

    /**
     * Valeur actuellement appliquée par le moteur, formatée pour l'affichage.
     * Null → pas de valeur scalaire (règle purement qualitative / calendaire).
     */
    public static function valeur(RegleFiscale $regle): ?string
    {
        $pct  = fn (float $d) => rtrim(rtrim(number_format($d, 1, ',', ' '), '0'), ',') . ' %';
        $fcfa = fn (float $n) => number_format($n, 0, ',', ' ') . ' FCFA';

        // Taux marginal le plus élevé du barème IRPP (dernière tranche).
        $tranchesIrpp = FiscalService::IRPP_TRANCHES;
        $derniere     = end($tranchesIrpp);
        $topIrpp      = (float) $derniere['taux'];

        return match ($regle->cle) {
            // ── TVA ──
            'tva_taux_standard', 'tva_commission' => $pct(FiscalService::TVA_TAUX),
            'tva_loyer_assujettissement'          => '0 % ou ' . $pct(FiscalService::TVA_TAUX) . ' selon usage',
            'tva_taux_reduit_non_applicable'      => '10 % — jamais appliqué',
            'tva_loyer_assiette'                  => 'Loyer HT + TOM',
            'tva_charges'                         => '0 % (débours) / ' . $pct(FiscalService::TVA_TAUX) . ' (forfait)',

            // ── BRS ──
            'brs_taux_assiette' => $pct(FiscalService::BRS_TAUX_LEGAL)
                . ' · seuil ' . $fcfa(FiscalService::BRS_SEUIL_MENSUEL) . '/mois',
            'brs_cascade_taux'  => $pct(FiscalService::BRS_TAUX_LEGAL) . ' (défaut légal)',

            // ── Droits d'enregistrement ──
            'DE-01' => $pct(FiscalService::DGID_TAUX_HABITATION),
            'DE-02' => $fcfa(FiscalService::DGID_TIMBRE_FISCAL) . ' / feuille',
            'DE-03' => 'Signature + 1 mois',
            'DE-06' => 'Plafond base 12 mois',
            'DE-07' => '5 % — hors périmètre',

            // ── IRPP ──
            'IR-01' => $pct(FiscalService::ABATTEMENT_IRPP * 100),
            'IR-02' => '7 tranches · 0 % → ' . $pct($topIrpp),
            'IR-03' => '10 % / part — hors périmètre',
            'IR-04' => 'Avant le 1er mars',

            // ── CGF ──
            'CGF-01' => '≤ ' . $fcfa(FiscalService::CGF_SEUIL) . '/an',
            'CGF-03' => '1/12 · 1,5/12 · 2/12 · plancher ' . $fcfa(FiscalService::CGF_PLANCHER),
            'CGF-04' => 'Avant le 1er février',
            'CGF-05' => '1 ou 3 versements',

            // ── CFPB ──
            'CFPB-01' => $pct(FiscalService::CFPB_TAUX * 100) . ' de la valeur locative',
            'CFPB-02' => '40 % — non appliqué',
            'CFPB-05', 'TEOM-03' => 'Avant le 31 janvier',
            'CFPB-06' => 'Propriétaire au 1er janvier',

            // ── TEOM ──
            'TEOM-01' => $pct(FiscalService::TEOM_TAUX_DAKAR) . ' (Dakar) / '
                . $pct(FiscalService::TEOM_TAUX_AUTRE) . ' (autres)',
            'TEOM-02' => '= valeur locative CFPB',

            // ── Agence (IS / CEL) ──
            'CEL-01' => 'Rappel — 31 janvier',
            'CEL-02' => 'Rappel — 30 avril',

            default => null,
        };
    }

    /**
     * Barème détaillé (lecture seule) affiché dans la fiche pour les règles à
     * plusieurs tranches — IRPP (IR-02) et CGF (CGF-03). Retourne des lignes
     * { seuil_bas, seuil_haut, taux } prêtes à afficher, ou null.
     */
    public static function bareme(RegleFiscale $regle): ?array
    {
        if ($regle->cle === 'IR-02') {
            $rows = [];
            foreach (FiscalService::IRPP_TRANCHES as $t) {
                $rows[] = [
                    'bas'  => number_format($t['min'], 0, ',', ' '),
                    'haut' => $t['max'] === PHP_INT_MAX ? '∞' : number_format($t['max'], 0, ',', ' '),
                    'taux' => $t['taux'] . ' %',
                ];
            }

            return ['titre' => 'Barème progressif IRPP (7 tranches)', 'unite' => 'FCFA / an', 'lignes' => $rows];
        }

        if ($regle->cle === 'CGF-03') {
            $rows = [];
            $bas  = 0; // le seuil bas d'une tranche = seuil haut de la précédente
            foreach (FiscalService::CGF_BAREME as $t) {
                $haut  = $t['max'] ?? PHP_INT_MAX;
                $rows[] = [
                    'bas'  => number_format($bas, 0, ',', ' '),
                    'haut' => $haut === PHP_INT_MAX ? '∞' : number_format($haut, 0, ',', ' '),
                    'taux' => self::formatFractionCgf($t),
                ];
                $bas = $haut;
            }

            return ['titre' => 'Barème CGF (fraction de mois de loyer)', 'unite' => 'Loyer brut / an', 'lignes' => $rows];
        }

        return null;
    }

    /**
     * Où la règle intervient dans le moteur — information de lecture seule,
     * pour mesurer l'impact avant modification.
     */
    public static function utiliseeDans(RegleFiscale $regle): string
    {
        return match ($regle->categorie) {
            'tva'                   => 'Moteur TVA — quittances, déclarations TVA mensuelles.',
            'brs'                   => 'Moteur BRS — versement au propriétaire, états trimestriels/annuels.',
            'droits_enregistrement' => 'Calcul des droits d\'enregistrement du bail (fiche contrat).',
            'irpp'                  => 'Bilans fiscaux propriétaire — IRPP sur revenus fonciers.',
            'cgf'                   => 'Bilans fiscaux propriétaire — Contribution Globale Foncière.',
            'cfpb'                  => 'Estimation CFPB (fiche bien / propriétaire).',
            'teom'                  => 'Estimation TEOM (fiche bien / propriétaire).',
            'is', 'cel'             => 'Calendrier fiscal agence — rappel d\'échéance uniquement (aucun montant).',
            default                 => 'Moteur fiscal.',
        };
    }

    /** Formate une entrée de CGF_BAREME en fraction lisible (ex. « 1,5/12 »). */
    private static function formatFractionCgf(array $tranche): string
    {
        // Barème exprimé en fraction de mois de loyer annuel ('fraction' = nb de mois).
        if (isset($tranche['fraction']) && is_numeric($tranche['fraction'])) {
            $frac = rtrim(rtrim(number_format((float) $tranche['fraction'], 2, ',', ' '), '0'), ',');

            return $frac . '/12';
        }

        return is_string($tranche['label'] ?? null) ? $tranche['label'] : '—';
    }
}
