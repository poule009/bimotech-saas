<?php

/*
|--------------------------------------------------------------------------
| Catalogue des règles fiscales & de leurs sources — TVA
|--------------------------------------------------------------------------
|
| Source de vérité UNIQUE pour la traçabilité des règles fiscales codées
| dans App\Services\FiscalService et App\Services\TvaAgenceService.
|
| Ce fichier est lu par Database\Seeders\ReglesFiscalesSeeder qui persiste
| chaque entrée dans la table `regles_fiscales` (App\Models\RegleFiscale).
| La donnée existe donc EN BASE — elle servira plus tard à construire une
| page « Sources » côté interface où l'utilisateur pourra cliquer et vérifier
| lui-même d'où vient chaque chiffre.
|
| Règle impérative : ne rien inventer. Chaque entrée ci-dessous provient
| uniquement du brief fiscal validé. Un cas non couvert ne doit PAS être
| deviné — il doit être signalé, pas ajouté ici « au jugé ».
|
| Champ `statut` :
|   - 'confirme'     : vérifié par une source externe indépendante
|   - 'non_verifie'  : PLAUSIBLE mais non confirmé (document interne uniquement)
|
| Chaque entrée `sources` est une liste de { libelle, url } (url nullable
| quand la règle découle d'un principe général sans page dédiée).
|
*/

return [

    // Date à laquelle ce catalogue a été compilé / vérifié pour la dernière fois.
    'date_verification' => '2026-07-11',

    'regles' => [

        // ── 1. Assujettissement du loyer à la TVA ────────────────────────────
        'tva_loyer_assujettissement' => [
            'categorie'   => 'tva',
            'titre'       => 'Assujettissement du loyer à la TVA',
            'description' => "Habitation non meublée → exonérée (0%). "
                . "Habitation meublée → 18%. Commercial → 18%. Mixte → 18%. "
                . "La TVA est due sur les locations meublées ou à usage professionnel ; "
                . "exonération pour le non-meublé à usage d'habitation.",
            'statut'  => 'confirme',
            'sources' => [
                [
                    'libelle' => 'Immoplus Sablux — Note sur les obligations fiscales des propriétaires',
                    'url'     => 'https://immoplussablux.com/article/note_sur_les_obligations_fiscales_des_proprietaires_des_biens_immobiliers',
                ],
            ],
            'note' => null,
        ],

        // ── 2. Taux TVA standard ─────────────────────────────────────────────
        'tva_taux_standard' => [
            'categorie'   => 'tva',
            'titre'       => 'Taux TVA standard',
            'description' => "Taux standard de 18% appliqué à tous les cas assujettis "
                . "de la gestion locative (loyer meublé/commercial/mixte, charges forfait, "
                . "commission agence, honoraires).",
            'statut'  => 'confirme',
            'sources' => [
                [
                    'libelle' => 'Eurocham Sénégal — La fiscalité des entreprises sénégalaises',
                    'url'     => 'https://www.eurocham.sn/la-fiscalite-des-entreprises-senegalaises/',
                ],
            ],
            'note' => null,
        ],

        // ── 2 bis. Taux réduit 10% — NON APPLICABLE ──────────────────────────
        'tva_taux_reduit_non_applicable' => [
            'categorie'   => 'tva',
            'titre'       => 'Taux réduit 10% — hors périmètre gestion locative',
            'description' => "Le taux réduit de 10% est réservé à l'hôtellerie/restauration "
                . "agréées. Sans lien avec la gestion locative : il ne doit JAMAIS être "
                . "utilisé dans ce moteur. Seul le taux 18% est codé.",
            'statut'  => 'confirme',
            'sources' => [
                [
                    'libelle' => 'Eurocham Sénégal — La fiscalité des entreprises sénégalaises',
                    'url'     => 'https://www.eurocham.sn/la-fiscalite-des-entreprises-senegalaises/',
                ],
            ],
            'note' => null,
        ],

        // ── 3. Assiette de la TVA sur le loyer ───────────────────────────────
        'tva_loyer_assiette' => [
            'categorie'   => 'tva',
            'titre'       => 'Assiette de la TVA sur le loyer',
            'description' => "assiette_tva_loyer = loyer_HT + TOM ; "
                . "tva_loyer = assiette × taux ; loyer_TTC = loyer_HT + tva_loyer. "
                . "Exclus de l'assiette : charges récupérables et commission de l'agence.",
            'statut'  => 'non_verifie',
            'sources' => [
                [
                    'libelle' => 'Document interne — Référentiel fiscal Bimotech (06/05/2026)',
                    'url'     => null,
                ],
            ],
            'note' => "RÈGLE NON VÉRIFIÉE PAR SOURCE OFFICIELLE INDÉPENDANTE. "
                . "PLAUSIBLE mais NON CONFIRMÉ. À confirmer avec un fiscaliste avant "
                . "audit ou contrôle fiscal réel.",
        ],

        // ── 4. TVA sur les charges locatives ─────────────────────────────────
        'tva_charges' => [
            'categorie'   => 'tva',
            'titre'       => 'TVA sur les charges locatives',
            'description' => "Débours purs (refacturés à l'identique, sans marge, factures "
                . "au nom du locataire) → 0%. Forfait mensuel fixe → 18%.",
            'statut'  => 'non_verifie',
            'sources' => [
                [
                    'libelle' => 'Document interne — Référentiel fiscal Bimotech (06/05/2026)',
                    'url'     => null,
                ],
            ],
            'note' => "RÈGLE NON VÉRIFIÉE PAR SOURCE OFFICIELLE INDÉPENDANTE. "
                . "PLAUSIBLE mais NON CONFIRMÉ. À confirmer avec un fiscaliste avant "
                . "audit ou contrôle fiscal réel.",
        ],

        // ── 5. TVA sur la commission d'agence ────────────────────────────────
        'tva_commission' => [
            'categorie'   => 'tva',
            'titre'       => "TVA sur la commission d'agence",
            'description' => "commission_HT = loyer_HT × taux_commission ; "
                . "tva_commission = commission_HT × 18% ; "
                . "commission_TTC = commission_HT + tva_commission. "
                . "Le taux de commission lui-même est libre (champ du modèle Bien), "
                . "non codé en dur.",
            'statut'  => 'confirme',
            'sources' => [
                [
                    'libelle' => 'Règle générale TVA sur prestations de service (Eurocham Sénégal)',
                    'url'     => 'https://www.eurocham.sn/la-fiscalite-des-entreprises-senegalaises/',
                ],
            ],
            'note' => "Raisonnablement solide : découle de la règle générale confirmée sur "
                . "les prestations de service, pas d'une source spécifique à l'immobilier.",
        ],

        // ── 6. Déclaration et versement mensuel de la TVA ────────────────────
        'tva_declaration_mensuelle' => [
            'categorie'   => 'tva',
            'titre'       => 'Déclaration et versement mensuel de la TVA',
            'description' => "tva_nette_due = tva_collectee - tva_deductible - credit_entrant. "
                . "Si > 0 → versement dû avant le 15 du mois suivant. "
                . "Si < 0 → reporté au mois suivant comme credit_entrant.",
            'statut'  => 'confirme',
            'sources' => [
                [
                    'libelle' => 'CGI Sénégal — Art. 370 (déclaration mensuelle TVA)',
                    'url'     => 'https://kof-experts.sn/wp-content/uploads/2024/04/CGI-annote-Janvier-2023.pdf',
                ],
            ],
            'note' => null,
        ],

        // ── 7. La CGF ne dispense PAS de la TVA ──────────────────────────────
        'tva_independante_du_regime_cgf' => [
            'categorie'   => 'tva',
            'titre'       => 'La CGF ne dispense pas de la TVA',
            'description' => "Le régime CGF (Contribution Globale Foncière) remplace uniquement "
                . "l'IRPP et la CFPB — jamais la TVA. Le moteur TVA s'applique indépendamment "
                . "du régime fiscal du propriétaire (CGF, IRPP, etc.). Ne jamais désactiver "
                . "le calcul TVA sous prétexte qu'un propriétaire est en CGF.",
            'statut'  => 'confirme',
            'sources' => [
                [
                    'libelle' => 'Article 74 du CGI (texte officiel annoté)',
                    'url'     => 'https://kof-experts.sn/wp-content/uploads/2024/04/CGI-annote-Janvier-2023.pdf',
                ],
                [
                    'libelle' => 'Investissement Immo Afrique — Fiscalité immobilière au Sénégal (2025)',
                    'url'     => 'https://investissementimmoafrique.com/blog/fiscalite-immobiliere-au-senegal/',
                ],
            ],
            'note' => null,
        ],

    ],
];
