<?php

namespace App\Services;

use App\Models\Bien;
use App\Models\Contrat;
use App\Models\Paiement;
use App\Models\Proprietaire;
use App\Models\User;
use Carbon\Carbon;

/**
 * CalendrierFiscalService — Agrégation des échéances fiscales à venir.
 *
 * Module d'AGRÉGATION : ne calcule AUCUNE nouvelle règle fiscale, il relie les
 * données déjà produites par les modules TVA, BRS, Droits d'enregistrement, IRPP,
 * CGF, CFPB, TEOM + les 3 échéances agence (IS, CEL-VL, CEL-VA, sans montant).
 *
 * Répond à : « Qu'est-ce qui est dû, par qui, à quelle date, dans l'horizon donné ? »
 * croisé sur tous les Propriétaires / Biens / Contrats de l'agence.
 *
 * Sortie : liste d'items { type, libelle, proprietaire, proprietaire_id,
 *   date_limite (Y-m-d|null), montant (int|null), statut_calcul (string|null) }
 * triée par date croissante (dates nulles en dernier).
 *
 * Deux points d'entrée :
 *  - echeancesAVenir()      → vue globale (toutes entités), items « légers ».
 *  - echeancesProprietaire() → fiche d'UN propriétaire, items ENRICHIS d'un
 *    registre de calcul ligne par ligne ('bien', 'detail') pour l'affichage
 *    vérifiable. Les DEUX passent par les mêmes helpers → montants identiques.
 */
class CalendrierFiscalService
{
    /**
     * Lectures scope-proof (indépendant du contexte Auth).
     *
     * @param bool $inclureRetard  true → inclut aussi les échéances DÉPASSÉES (date < ref)
     *   encore dues (ex. un bail non enregistré en retard). Utile pour la vue globale
     *   « En retard ». false (défaut) → strictement à venir dans l'horizon.
     */
    public function echeancesAVenir(int $agencyId, int $horizonJours = 30, ?Carbon $reference = null, bool $inclureRetard = false): array
    {
        $ref   = ($reference ?? now())->copy()->startOfDay();
        $fin   = $ref->copy()->addDays($horizonJours)->endOfDay();
        $annee = (int) $ref->year;

        $items = [];

        // ── Échéances liées aux Propriétaires ────────────────────────────────
        $proprietaires = User::withoutGlobalScopes()
            ->where('agency_id', $agencyId)
            ->where('role', 'proprietaire')
            ->get(['id', 'name', 'agency_id']);

        foreach ($proprietaires as $prop) {
            $profil = Proprietaire::withoutGlobalScopes()->where('user_id', $prop->id)->first();

            foreach ($this->itemsProprietaire($prop, $profil, $ref, $annee, $agencyId, false) as $item) {
                $items[] = $item;
            }
        }

        // ── Droits d'enregistrement — par Contrat non encore enregistré ──────
        foreach ($this->contratsNonEnregistres($agencyId) as $contrat) {
            $items[] = $this->itemDroits($contrat, false);
        }

        // ── Filtrage sur l'horizon (échéances entités) ───────────────────────
        $items = array_values(array_filter($items, function (array $e) use ($ref, $fin, $inclureRetard) {
            if ($e['date_limite'] === null) {
                return false; // les entités ont toujours une date
            }
            $d = Carbon::parse($e['date_limite']);
            // Borne haute toujours ; borne basse (aujourd'hui) sauf si on veut les retards.
            return $d->lte($fin) && ($inclureRetard || $d->gte($ref));
        }));

        // ── Échéances AGENCE (toujours affichées, une fois par an, sans montant) ──
        foreach ($this->echeancesAgence($ref) as $agence) {
            $items[] = $agence;
        }

        return $this->trierParDate($items);
    }

    /**
     * Échéances ENRICHIES d'un seul propriétaire — pour la fiche fiscale (écran 2).
     *
     * Chaque item porte en plus 'bien' (libellé) et 'detail' (registre de calcul :
     * lignes base/taux/abattement, ligne de résultat, note d'estimation). Réutilise
     * exactement les mêmes helpers que echeancesAVenir → aucun risque de divergence
     * de montant entre la liste globale et la fiche.
     *
     * Périmètre : toutes les échéances de ce propriétaire dans l'année à venir,
     * retards compris (les échéances agence ne concernent pas un propriétaire).
     */
    public function echeancesProprietaire(int $agencyId, int $proprietaireId, ?Carbon $reference = null): array
    {
        $ref   = ($reference ?? now())->copy()->startOfDay();
        $annee = (int) $ref->year;

        $prop = User::withoutGlobalScopes()
            ->where('agency_id', $agencyId)
            ->where('role', 'proprietaire')
            ->where('id', $proprietaireId)
            ->first(['id', 'name', 'agency_id']);

        if (! $prop) {
            return [];
        }

        $profil = Proprietaire::withoutGlobalScopes()->where('user_id', $prop->id)->first();

        $items = $this->itemsProprietaire($prop, $profil, $ref, $annee, $agencyId, true);

        foreach ($this->contratsNonEnregistres($agencyId, $proprietaireId) as $contrat) {
            $items[] = $this->itemDroits($contrat, true);
        }

        // Horizon 1 an, retards inclus (pas de borne basse).
        $fin   = $ref->copy()->addDays(365)->endOfDay();
        $items = array_values(array_filter(
            $items,
            fn (array $e) => $e['date_limite'] !== null && Carbon::parse($e['date_limite'])->lte($fin)
        ));

        return $this->trierParDate($items);
    }

    // ═══════════════════════════════════════════════════════════════════════
    // CONSTRUCTION DES ÉCHÉANCES (partagée global ↔ fiche via $avecDetail)
    // ═══════════════════════════════════════════════════════════════════════

    /**
     * Toutes les échéances propres à UN propriétaire (hors droits d'enregistrement,
     * portés par les contrats). Si $avecDetail : chaque item est enrichi du registre
     * de calcul. Sinon : items « légers » (comportement historique inchangé).
     */
    private function itemsProprietaire(User $prop, ?Proprietaire $profil, Carbon $ref, int $annee, int $agencyId, bool $avecDetail): array
    {
        $items = [];

        $assujettiTva = (bool) ($profil?->assujetti_tva ?? false);
        $estMorale    = (bool) ($profil?->est_personne_morale_is ?? false);
        $brsDispense  = (bool) ($profil?->brs_dispense ?? false);
        $cgfCouvre    = $profil ? $profil->cgfCouvre($annee) : false;

        // ── TVA — déclaration mensuelle (15 du mois), montant déjà calculé (module TVA).
        if ($assujettiTva) {
            $date    = $this->prochainJourFixe(15, $ref);
            $mois    = $date->copy()->subMonthNoOverflow();
            $montant = $this->sommeMois($prop->id, $agencyId, $mois, ['tva_loyer', 'tva_charges']);

            $items[] = $this->item('tva', 'TVA — déclaration mensuelle', $prop, $date, $montant, 'confirme',
                $avecDetail ? $this->detailTva($prop->id, $agencyId, $mois, $montant) : []);
        }

        // ── BRS — mensuelle + récap annuel (bailleur personne physique non dispensé).
        if (! $estMorale && ! $brsDispense) {
            $dateMens = $this->prochainJourFixe(15, $ref);
            $moisBrs  = $dateMens->copy()->subMonthNoOverflow();
            $montMens = $this->sommeMois($prop->id, $agencyId, $moisBrs, ['brs_amount']);
            $items[]  = $this->item('brs_mensuel', 'BRS — déclaration mensuelle', $prop, $dateMens, $montMens, 'confirme',
                $avecDetail ? $this->detailBrsMensuel($moisBrs, $montMens) : []);

            $dateAnn  = $this->prochaineDate(1, 31, $ref); // 31 janvier
            $anneeBrs = (int) $dateAnn->copy()->subYear()->year;
            $montAnn  = $this->sommeAnnee($prop->id, $agencyId, $anneeBrs, ['brs_amount']);
            $items[]  = $this->item('brs_annuel', 'BRS — récapitulatif annuel', $prop, $dateAnn, $montAnn, 'confirme',
                $avecDetail ? $this->detailBrsAnnuel($anneeBrs, $montAnn) : []);
        }

        if ($cgfCouvre) {
            // ── CGF — déclaration (1er février) + versement(s) selon le mode.
            $montantCgf = (int) ($profil->cgf_montant ?? 0);
            $items[] = $this->item('cgf_declaration', 'CGF — déclaration', $prop, $this->prochaineDate(2, 1, $ref), $montantCgf, 'confirme',
                $avecDetail ? $this->detailCgf($profil, $montantCgf) : []);

            foreach (($profil->cgf_echeances ?? []) as $ech) {
                if (empty($ech['date'])) {
                    continue;
                }
                $montVers = (int) round($ech['montant'] ?? 0);
                $items[] = $this->item(
                    'cgf_versement',
                    'CGF — versement (' . ($ech['libelle'] ?? 'échéance') . ')',
                    $prop,
                    Carbon::parse($ech['date'])->startOfDay(),
                    $montVers,
                    'confirme',
                    $avecDetail ? $this->detailCgfVersement($montVers, $montantCgf) : []
                );
            }
        } else {
            // ── Hors CGF : IRPP foncier (Particulier) + CFPB+TEOM par bien loué.
            if (! $estMorale) {
                $irpp = FiscalService::estimerIrppFoncier($prop->id, $annee, $agencyId);
                $montIrpp = (int) round($irpp['montant_estime'] ?? 0);
                $items[] = $this->item('irpp', 'IRPP foncier — déclaration', $prop, $this->prochaineDate(3, 1, $ref), $montIrpp,
                    $irpp['statut_calcul'] ?? 'perimetre_partiel',
                    $avecDetail ? $this->detailIrpp($irpp, $montIrpp) : []);
            }

            $biens = Bien::withoutGlobalScopes()
                ->where('proprietaire_id', $prop->id)
                ->where('statut', 'loue')
                ->get(['id', 'titre', 'reference', 'cfpb_valeur_locative_estimee', 'cfpb_montant_estime', 'teom_taux_applique', 'teom_montant_estime']);

            foreach ($biens as $bien) {
                // Regroupement OBLIGATOIRE CFPB + TEOM sur une seule ligne (même bien, même date, même base).
                $montant   = (int) $bien->cfpb_montant_estime + (int) $bien->teom_montant_estime;
                $bienLabel = $bien->titre ?: $bien->reference;
                $items[]   = $this->item('cfpb_teom', 'CFPB + TEOM — Bien : ' . $bienLabel, $prop, $this->prochaineDate(1, 31, $ref), $montant,
                    'estimation_structurelle',
                    $avecDetail ? $this->detailCfpbTeom($bien, $montant, $bienLabel) : []);
            }
        }

        return $items;
    }

    /** Contrats de l'agence non enregistrés (optionnellement filtrés sur un propriétaire). */
    private function contratsNonEnregistres(int $agencyId, ?int $proprietaireId = null)
    {
        $query = Contrat::withoutGlobalScopes()
            ->where('agency_id', $agencyId)
            ->where('droit_enreg_effectue', false)
            ->whereNotNull('droit_enreg_date_limite')
            ->with(['bien:id,proprietaire_id,titre,reference', 'bien.proprietaire:id,name']);

        if ($proprietaireId !== null) {
            $query->whereHas('bien', fn ($q) => $q->where('proprietaire_id', $proprietaireId));
        }

        return $query->get();
    }

    /** Échéance « droits d'enregistrement » d'un contrat (enrichie si $avecDetail). */
    private function itemDroits(Contrat $contrat, bool $avecDetail): array
    {
        $prop      = $contrat->bien?->proprietaire;
        $bienLabel = $contrat->bien?->titre ?: $contrat->bien?->reference;
        $montant   = (int) round($contrat->droit_enreg_total);

        return $this->item(
            'droit_enregistrement',
            'Enregistrement du bail — ' . ($bienLabel ?: 'bien'),
            $prop,
            $contrat->droit_enreg_date_limite?->copy()->startOfDay(),
            $montant,
            $contrat->droit_enreg_statut_calcul ?? 'confirme',
            $avecDetail ? $this->detailDroits($contrat, $montant, $bienLabel) : []
        );
    }

    // ═══════════════════════════════════════════════════════════════════════
    // REGISTRES DE CALCUL (affichage vérifiable — écran 2 uniquement)
    // ═══════════════════════════════════════════════════════════════════════

    private function detailTva(int $propId, int $agencyId, Carbon $mois, int $montant): array
    {
        $tvaLoyer   = $this->sommeMois($propId, $agencyId, $mois, ['tva_loyer']);
        $tvaCharges = $this->sommeMois($propId, $agencyId, $mois, ['tva_charges']);

        return [
            'bien'   => null,
            'detail' => [
                'lignes' => [
                    ['label' => 'TVA collectée sur les loyers — ' . $this->moisLabel($mois), 'valeur' => $this->fmt($tvaLoyer)],
                    ['label' => 'TVA collectée sur les charges forfaitaires', 'valeur' => $this->fmt($tvaCharges)],
                ],
                'resultat' => ['label' => 'TVA à reverser à la DGID', 'montant' => $montant],
                'note'     => "TVA au taux de 18 % (CGI art. 369) collectée pour le compte de l'État sur les loyers et charges du mois. À déclarer et reverser avant le 15.",
            ],
        ];
    }

    private function detailBrsMensuel(Carbon $mois, int $montant): array
    {
        // Assiette reconstituée pour l'affichage : base × 5 % = retenue (cohérent par construction).
        $base = $montant > 0 ? (int) round($montant / (FiscalService::BRS_TAUX_LEGAL / 100)) : 0;

        return [
            'bien'   => null,
            'detail' => [
                'lignes' => [
                    ['label' => 'Loyers bruts soumis à la retenue — ' . $this->moisLabel($mois), 'valeur' => $this->fmt($base)],
                    ['label' => 'Taux de retenue à la source (CGI art. 201 §3)', 'valeur' => $this->pct(FiscalService::BRS_TAUX_LEGAL)],
                ],
                'resultat' => ['label' => $this->fmt($base) . ' × ' . $this->pct(FiscalService::BRS_TAUX_LEGAL), 'montant' => $montant],
                'note'     => 'Retenue prélevée sur le loyer et reversée mensuellement à la DGID pour le compte du propriétaire. Non prélevée sur les loyers mensuels inférieurs à 150 000 F (CGI art. 200 §4).',
            ],
        ];
    }

    private function detailBrsAnnuel(int $annee, int $montant): array
    {
        return [
            'bien'   => null,
            'detail' => [
                'lignes' => [
                    ['label' => 'Retenues BRS cumulées sur l\'année ' . $annee, 'valeur' => $this->fmt($montant)],
                ],
                'resultat' => ['label' => 'Récapitulatif annuel des retenues', 'montant' => $montant],
                'note'     => 'Récapitulatif des retenues déjà déclarées mois par mois, à transmettre à la DGID avant le 31 janvier.',
            ],
        ];
    }

    private function detailCgf(?Proprietaire $profil, int $montant): array
    {
        $revenu = (float) ($profil?->cgf_revenu_brut_prevu ?? 0);
        $cgf    = FiscalService::calculerCGF($revenu);

        $lignes = [
            ['label' => 'Loyer brut prévisionnel de l\'année', 'valeur' => $this->fmt((int) round($revenu))],
            ['label' => 'Barème appliqué', 'valeur' => $cgf['fraction_label'] ?? '—'],
        ];
        if (! empty($cgf['plancher_applique'])) {
            $lignes[] = ['label' => 'Plancher légal appliqué', 'valeur' => $this->fmt(FiscalService::CGF_PLANCHER)];
        }

        return [
            'bien'   => null,
            'detail' => [
                'lignes'   => $lignes,
                'resultat' => ['label' => 'CGF due (régime synthétique, Art. 75)', 'montant' => $montant],
                'note'     => 'Régime optionnel qui remplace l\'IRPP foncier, l\'IMF et la CFPB — pas la TVA. Bornes du barème (12M / 18M) à recouper avec le texte officiel de l\'Art. 75.',
            ],
        ];
    }

    private function detailCgfVersement(int $montantVersement, int $montantTotal): array
    {
        return [
            'bien'   => null,
            'detail' => [
                'lignes' => [
                    ['label' => 'CGF totale due sur l\'année', 'valeur' => $this->fmt($montantTotal)],
                ],
                'resultat' => ['label' => 'Montant de ce versement', 'montant' => $montantVersement],
                'note'     => 'Versement échelonné de la CGF (option 3 versements : fin février · fin avril · fin juin).',
            ],
        ];
    }

    private function detailIrpp(array $irpp, int $montant): array
    {
        $revenu     = (float) ($irpp['revenu_brut_annuel'] ?? 0);
        $abattement = round($revenu * FiscalService::ABATTEMENT_IRPP, 2);
        $base       = (float) ($irpp['base_apres_abattement'] ?? 0);

        $lignes = [
            ['label' => 'Revenus fonciers bruts annuels (loyers gérés)', 'valeur' => $this->fmt((int) round($revenu))],
            ['label' => 'Abattement forfaitaire de 30 % (CGI art. 68 §c)', 'valeur' => '− ' . $this->fmt((int) round($abattement))],
            ['label' => 'Revenu net imposable', 'valeur' => $this->fmt((int) round($base))],
        ];

        foreach (($irpp['detail'] ?? []) as $t) {
            if (($t['impot'] ?? 0) > 0) {
                $lignes[] = [
                    'label' => 'Barème — ' . $this->pct((float) $t['taux']) . ' sur ' . $this->fmt((int) round($t['assiette'])),
                    'valeur' => $this->fmt((int) round($t['impot'])),
                ];
            }
        }

        return [
            'bien'   => null,
            'detail' => [
                'lignes'   => $lignes,
                'resultat' => ['label' => 'IRPP estimé après barème progressif', 'montant' => $montant],
                'note'     => 'Estimation sur le seul périmètre des loyers gérés dans l\'application et sans option CGF (seuil 30M F, Art. 75). L\'IRPP réel dépend de l\'ensemble des revenus et de la situation familiale du propriétaire — à confirmer auprès de la DGID.',
            ],
        ];
    }

    private function detailCfpbTeom(Bien $bien, int $montant, string $bienLabel): array
    {
        $valeurLocative = (int) $bien->cfpb_valeur_locative_estimee;
        $cfpb           = (int) $bien->cfpb_montant_estime;
        $teom           = (int) $bien->teom_montant_estime;
        $teomTaux       = (float) ($bien->teom_taux_applique ?? FiscalService::TEOM_TAUX_AUTRE);

        return [
            'bien'   => $bienLabel,
            'detail' => [
                'lignes' => [
                    ['label' => 'Valeur locative estimée (loyer annuel de référence)', 'valeur' => $this->fmt($valeurLocative)],
                    ['label' => 'CFPB — ' . $this->pct(FiscalService::CFPB_TAUX * 100) . ' de la valeur locative', 'valeur' => $this->fmt($cfpb)],
                    ['label' => 'TEOM — ' . $this->pct($teomTaux) . ' de la valeur locative', 'valeur' => $this->fmt($teom)],
                ],
                'resultat' => ['label' => $this->fmt($cfpb) . ' + ' . $this->fmt($teom), 'montant' => $montant],
                'note'     => 'Estimation structurelle : la valeur locative cadastrale officielle, fixée par la DGID, n\'est pas connue de l\'application et peut différer sensiblement du loyer réel. Déclaration avant le 31 janvier.',
            ],
        ];
    }

    private function detailDroits(Contrat $contrat, int $montant, ?string $bienLabel): array
    {
        $droits   = (int) round((float) ($contrat->droit_enreg_montant ?? 0));
        $timbre   = (int) round((float) ($contrat->droit_enreg_timbre ?? 0));
        $feuilles = (int) ($contrat->droit_enreg_nombre_feuilles ?? 2);
        // Assiette reconstituée : droits = assiette × 2 % → assiette = droits / 2 %.
        $base = $droits > 0 ? (int) round($droits / (FiscalService::DGID_TAUX_HABITATION / 100)) : 0;

        return [
            'bien'   => $bienLabel,
            'detail' => [
                'lignes' => [
                    ['label' => 'Assiette (loyer + charges sur la durée du bail)', 'valeur' => $this->fmt($base)],
                    ['label' => 'Taux d\'enregistrement (CGI art. 472 IV.6)', 'valeur' => $this->pct(FiscalService::DGID_TAUX_HABITATION)],
                    ['label' => 'Timbre fiscal (' . $feuilles . ' feuille' . ($feuilles > 1 ? 's' : '') . ' × 2 000 F)', 'valeur' => $this->fmt($timbre)],
                ],
                'resultat' => ['label' => '(' . $this->fmt($base) . ' × ' . $this->pct(FiscalService::DGID_TAUX_HABITATION) . ') + timbre', 'montant' => $montant],
                'note'     => 'Dû une seule fois, à la signature du bail ou lors de son renouvellement.',
            ],
        ];
    }

    // ═══════════════════════════════════════════════════════════════════════
    // ÉCHÉANCES AGENCE + HELPERS INTERNES
    // ═══════════════════════════════════════════════════════════════════════

    /** Les 3 échéances de l'agence — rappel calendrier SANS montant (jamais calculé). */
    private function echeancesAgence(Carbon $ref): array
    {
        return [
            [
                'type'          => 'is_agence',
                'libelle'       => 'Déclaration IS de l\'agence — à traiter avec votre comptable.',
                'proprietaire'  => null,
                'proprietaire_id' => null,
                'date_limite'   => null, // date dépend de la clôture d'exercice (non vérifiée)
                'montant'       => null,
                'statut_calcul' => 'non_verifie',
            ],
            [
                'type'          => 'cel_vl_agence',
                'libelle'       => 'Déclaration CEL (valeur locative) de l\'agence — à traiter avec votre comptable.',
                'proprietaire'  => null,
                'proprietaire_id' => null,
                'date_limite'   => $this->prochaineDate(1, 31, $ref)->format('Y-m-d'), // 31 janvier
                'montant'       => null,
                'statut_calcul' => null,
            ],
            [
                'type'          => 'cel_va_agence',
                'libelle'       => 'Déclaration CEL (valeur ajoutée) de l\'agence — à traiter avec votre comptable.',
                'proprietaire'  => null,
                'proprietaire_id' => null,
                'date_limite'   => $this->prochaineDate(4, 30, $ref)->format('Y-m-d'), // 30 avril
                'montant'       => null,
                'statut_calcul' => null,
            ],
        ];
    }

    /** Construit un item d'échéance normalisé (avec registre de calcul optionnel via $extra). */
    private function item(string $type, string $libelle, ?User $prop, ?Carbon $date, ?int $montant, ?string $statut, array $extra = []): array
    {
        return array_merge([
            'type'            => $type,
            'libelle'         => $libelle,
            'proprietaire'    => $prop?->name,
            'proprietaire_id' => $prop?->id,
            'date_limite'     => $date?->format('Y-m-d'),
            'montant'         => $montant,
            'statut_calcul'   => $statut,
        ], $extra);
    }

    /** Tri par date croissante (dates nulles en dernier). */
    private function trierParDate(array $items): array
    {
        usort($items, function (array $a, array $b) {
            if ($a['date_limite'] === $b['date_limite']) {
                return 0;
            }
            if ($a['date_limite'] === null) {
                return 1;
            }
            if ($b['date_limite'] === null) {
                return -1;
            }
            return strcmp($a['date_limite'], $b['date_limite']);
        });

        return $items;
    }

    /** Prochaine occurrence d'une date fixe (mois/jour) ≥ référence. */
    private function prochaineDate(int $mois, int $jour, Carbon $ref): Carbon
    {
        $candidate = Carbon::create($ref->year, $mois, $jour)->startOfDay();
        if ($candidate->lt($ref)) {
            $candidate = Carbon::create($ref->year + 1, $mois, $jour)->startOfDay();
        }
        return $candidate;
    }

    /** Prochaine occurrence d'un jour fixe du mois (ex. le 15) ≥ référence. */
    private function prochainJourFixe(int $jour, Carbon $ref): Carbon
    {
        $candidate = $ref->copy()->day($jour)->startOfDay();
        if ($candidate->lt($ref)) {
            $candidate = $ref->copy()->addMonthNoOverflow()->day($jour)->startOfDay();
        }
        return $candidate;
    }

    /** Somme de champs paiements (valides) d'un propriétaire sur un mois donné. */
    private function sommeMois(int $propId, int $agencyId, Carbon $mois, array $champs): int
    {
        return (int) round($this->requeteSomme($propId, $agencyId, $champs)
            ->whereYear('paiements.periode', $mois->year)
            ->whereMonth('paiements.periode', $mois->month)
            ->value('total'));
    }

    /** Somme de champs paiements (valides) d'un propriétaire sur une année. */
    private function sommeAnnee(int $propId, int $agencyId, int $annee, array $champs): int
    {
        return (int) round($this->requeteSomme($propId, $agencyId, $champs)
            ->whereYear('paiements.periode', $annee)
            ->value('total'));
    }

    /** Requête de base (scope-proof) pour sommer des champs de paiements valides. */
    private function requeteSomme(int $propId, int $agencyId, array $champs)
    {
        $expr = implode(' + ', array_map(fn ($c) => "COALESCE(paiements.$c, 0)", $champs));

        return Paiement::query()
            ->join('contrats', 'contrats.id', '=', 'paiements.contrat_id')
            ->join('biens', 'biens.id', '=', 'contrats.bien_id')
            ->where('biens.proprietaire_id', $propId)
            ->where('paiements.agency_id', $agencyId)
            ->where('paiements.statut', 'valide')
            ->selectRaw("COALESCE(SUM($expr), 0) as total");
    }

    // ── Formatage (registres de calcul) ─────────────────────────────────────

    /** Montant entier → « 45 000 FCFA ». */
    private function fmt(int $n): string
    {
        return number_format($n, 0, ',', ' ') . ' FCFA';
    }

    /** Taux → « 5 % » / « 3,6 % » (sans décimale superflue). */
    private function pct(float $taux): string
    {
        $s = rtrim(rtrim(number_format($taux, 1, ',', ' '), '0'), ',');
        return $s . ' %';
    }

    /** Mois lisible → « juillet 2026 ». */
    private function moisLabel(Carbon $mois): string
    {
        return $mois->locale('fr')->isoFormat('MMMM Y');
    }
}
