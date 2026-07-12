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

        // ═══════════════════════════════════════════════════════════════════
        // BRS — Retenue à la source sur les loyers
        // ═══════════════════════════════════════════════════════════════════

        // ── Taux & assiette de la BRS ────────────────────────────────────────
        'brs_taux_assiette' => [
            'categorie'   => 'brs',
            'titre'       => 'Taux et assiette de la BRS',
            'description' => "Bailleur personne physique, loyer mensuel HT ≥ 150 000 F, "
                . "non dispensé → BRS = loyer_HT × 5%. Bailleur personne morale (IS) → 0 "
                . "quel que soit le montant. Loyer < 150 000 F → 0 par défaut (dispense "
                . "légale), sauf override manuel. Assiette = loyer HT BRUT (loyer nu) — "
                . "jamais le TTC, jamais TOM ni charges (assiette distincte de la TVA).",
            'statut'  => 'confirme',
            'sources' => [
                [
                    'libelle' => 'Immoplus Sablux — Note sur les obligations fiscales des propriétaires',
                    'url'     => 'https://immoplussablux.com/article/note_sur_les_obligations_fiscales_des_proprietaires_des_biens_immobiliers',
                ],
                [
                    'libelle' => 'OpenFisca Sénégal — Système socio-fiscal sénégalais (wiki)',
                    'url'     => 'https://github.com/openfisca/openfisca-senegal/wiki/Système-socio-fiscal-sénégalais',
                ],
            ],
            'note' => 'Deux sources indépendantes concordantes. Assiette BRS ≠ assiette TVA : '
                . 'deux moteurs, deux assiettes distinctes.',
        ],

        // ── Qui retient, qui est dispensé ────────────────────────────────────
        'brs_qui_retient' => [
            'categorie'   => 'brs',
            'titre'       => 'Retenue par l\'agence, déduite du versement bailleur',
            'description' => "L'agence immobilière opère la BRS à la place du locataire — "
                . "le locataire n'a jamais à s'en occuper quand il passe par une agence. "
                . "Le montant retenu est DÉDUIT du versement fait au propriétaire, JAMAIS "
                . "ajouté à la charge du locataire.",
            'statut'  => 'confirme',
            'sources' => [
                [
                    'libelle' => 'Immoplus Sablux — Note sur les obligations fiscales des propriétaires',
                    'url'     => 'https://immoplussablux.com/article/note_sur_les_obligations_fiscales_des_proprietaires_des_biens_immobiliers',
                ],
            ],
            'note' => null,
        ],

        // ── Obligations déclaratives (3 niveaux) ─────────────────────────────
        'brs_obligations_declaratives' => [
            'categorie'   => 'brs',
            'titre'       => 'Obligations déclaratives BRS (mensuel / trimestriel / annuel)',
            'description' => "Versement mensuel : avant le 15 du mois suivant (total des BRS "
                . "retenues le mois précédent). État trimestriel : 15 avr (T1), 15 juil (T2), "
                . "15 oct (T3), 15 jan N+1 (T4) — identité du bénéficiaire (nom, adresse, NINEA), "
                . "montant versé, période, impôt retenu. État annuel récapitulatif : 31 janvier "
                . "de l'année suivante (tous les versements à des tiers personnes physiques). "
                . "Option (≤ 20 000 F/mois de retenues) : déclaration trimestrielle possible au "
                . "lieu de mensuelle — facilité facultative, le mensuel reste toujours valide.",
            'statut'  => 'confirme',
            'sources' => [
                [
                    'libelle' => 'Plaquette officielle DGID — e-services',
                    'url'     => 'https://fr.scribd.com/document/744350860/PLAQUETTE-E-SERVICE-VERSO-1-3',
                ],
                [
                    'libelle' => 'MCE Sénégal — Déclarations fiscales',
                    'url'     => 'https://mcesenegal.com/espace-createur/aspects-juridiques-fiscaux-et-sociaux/declarations-fiscales/',
                ],
            ],
            'note' => 'Option trimestrielle sous 20 000 F : non implémentée pour l\'instant '
                . '(facilité optionnelle, pas une obligation).',
        ],

        // ── Cascade de priorité du taux — DÉCISION PRODUIT ───────────────────
        'brs_cascade_taux' => [
            'categorie'   => 'brs',
            'titre'       => 'Cascade de priorité du taux BRS (décision produit)',
            'description' => "Priorité du taux appliqué : 1) taux override du contrat "
                . "(taux_brs_manuel) ; 2) taux override du locataire (taux_brs_override) ; "
                . "3) taux légal par défaut 5%. Cette cascade sert à gérer des cas particuliers "
                . "négociés.",
            'statut'  => 'decision_produit',
            'sources' => [
                [
                    'libelle' => 'Décision produit interne Bimotech — pas une exigence légale DGID',
                    'url'     => null,
                ],
            ],
            'note' => 'N\'existe dans aucun texte fiscal : logique produit propre à Bimotech, '
                . 'documentée comme telle (ne pas chercher à la « vérifier »).',
        ],

        // ═══════════════════════════════════════════════════════════════════
        // DROITS D'ENREGISTREMENT DU BAIL (DGID)
        // ═══════════════════════════════════════════════════════════════════

        'DE-01' => [
            'categorie'   => 'droits_enregistrement',
            'titre'       => 'Taux 2% des droits d\'enregistrement (baux à durée limitée)',
            'description' => "Baux à durée limitée (habitation ou commercial, aucune distinction) : "
                . "droits = 2% de l'assiette. Assiette = loyer annuel + charges. "
                . "Réf. légale : Art. 510 et 472-IV-6 CGI.",
            'statut'  => 'confirme_officiel',
            'sources' => [['libelle' => 'DGID — impotsetdomaines.gouv.sn', 'url' => 'https://www.impotsetdomaines.gouv.sn/']],
            'note' => null,
            'date_verification' => '2026-07-12',
        ],
        'DE-02' => [
            'categorie'   => 'droits_enregistrement',
            'titre'       => 'Timbre fiscal 2 000 F par feuille',
            'description' => "Timbre fixe de 2 000 FCFA par feuille de l'acte. Le nombre de "
                . "feuilles est saisi par l'utilisateur (défaut 2) — jamais déduit du PDF "
                . "(une feuille ≠ une page, recto-verso possible).",
            'statut'  => 'confirme_officiel',
            'sources' => [['libelle' => 'DGID — impotsetdomaines.gouv.sn', 'url' => 'https://www.impotsetdomaines.gouv.sn/']],
            'note' => null,
            'date_verification' => '2026-07-12',
        ],
        'DE-03' => [
            'categorie'   => 'droits_enregistrement',
            'titre'       => 'Délai d\'enregistrement : 1 mois après signature',
            'description' => "Le bail doit être enregistré dans le mois suivant la signature. "
                . "Date limite retenue (interprétation prudente) = date de signature + 1 mois.",
            'statut'  => 'confirme_source_privee',
            'sources' => [
                ['libelle' => 'Cabinet ADAT — dsbimmo.sn (2022)', 'url' => 'https://dsbimmo.sn/'],
                ['libelle' => 'keurcity.com', 'url' => 'https://keurcity.com/'],
            ],
            'note' => 'Légère nuance de formulation entre les deux sources → retenu signature + 1 mois.',
            'date_verification' => '2026-07-12',
        ],
        'DE-04' => [
            'categorie'   => 'droits_enregistrement',
            'titre'       => 'Prorata si bail < 1 an non renouvelable',
            'description' => "Si la durée du bail est inférieure à un an ET non renouvelable, "
                . "les droits sont calculés sur la durée réelle du bail (prorata mensuel).",
            'statut'  => 'confirme_source_privee',
            'sources' => [['libelle' => 'Cabinet Audifisc — audifiscsn.com', 'url' => 'https://audifiscsn.com/']],
            'note' => null,
            'date_verification' => '2026-07-12',
        ],
        'DE-05' => [
            'categorie'   => 'droits_enregistrement',
            'titre'       => 'Fractionnement triennal des baux > 3 ans (Art. 510)',
            'description' => "Le droit est fractionné en autant de paiements que de périodes "
                . "triennales ; seul le droit de la 1re période est dû à l'enregistrement, les "
                . "suivants au début de chaque nouvelle période triennale.",
            'statut'  => 'confirme_source_privee',
            'sources' => [
                ['libelle' => 'Audifisc — audifiscsn.com', 'url' => 'https://audifiscsn.com/'],
                ['libelle' => 'ADAT — dsbimmo.sn', 'url' => 'https://dsbimmo.sn/'],
            ],
            'note' => 'Texte brut de l\'Art. 510 non vérifié directement (PDF CGI illisible). '
                . 'Conséquence : pour un bail > 12 mois, le montant affiché est une ESTIMATION.',
            'date_verification' => '2026-07-12',
        ],
        'DE-06' => [
            'categorie'   => 'droits_enregistrement',
            'titre'       => 'Plafond base 12 mois (référentiel interne)',
            'description' => "Le référentiel interne plafonne la base de calcul à 12 mois de loyer. "
                . "Aucune source externe ne le confirme (les sources parlent de périodes "
                . "TRIENNALES, base potentiellement jusqu'à 36 mois). NE PAS présenter le montant "
                . "d'un bail > 12 mois comme définitif → statut 'estimation'.",
            'statut'  => 'non_verifie',
            'sources' => [['libelle' => 'Référentiel interne Bimotech (06/05/2026)', 'url' => null]],
            'note' => 'NON CONFIRMÉ. Utilisé uniquement comme base d\'ESTIMATION, jamais comme montant définitif.',
            'date_verification' => null,
        ],
        'DE-07' => [
            'categorie'   => 'droits_enregistrement',
            'titre'       => 'Taux 5% des baux à durée illimitée (Art. 472-II-8)',
            'description' => "Les baux à durée illimitée seraient taxés à 5%. HORS PÉRIMÈTRE : "
                . "les contrats gérés dans l'app sont à durée limitée. Non implémenté, "
                . "conservé ici pour traçabilité.",
            'statut'  => 'hors_perimetre',
            'sources' => [['libelle' => 'Cabinet ADAT — dsbimmo.sn', 'url' => 'https://dsbimmo.sn/']],
            'note' => 'Non implémenté (durée illimitée hors périmètre v1).',
            'date_verification' => '2026-07-12',
        ],

        // ═══════════════════════════════════════════════════════════════════
        // IRPP sur revenus fonciers (Propriétaires personnes physiques)
        // ═══════════════════════════════════════════════════════════════════

        'IR-01' => [
            'categorie'   => 'irpp',
            'titre'       => 'Abattement forfaitaire 30% sur les revenus fonciers bruts',
            'description' => "Un abattement de 30% s'applique sur le loyer annuel brut AVANT "
                . "le barème progressif. Base imposable = loyers × (1 − 30%).",
            'statut'  => 'confirme_source_privee',
            'sources' => [['libelle' => 'Guides fiscaux spécialisés Afrique francophone', 'url' => null]],
            'note' => 'Sources privées concordantes (pas de texte gouv.sn direct).',
            'date_verification' => '2026-07-12',
        ],
        'IR-02' => [
            'categorie'   => 'irpp',
            'titre'       => 'Barème progressif IRPP — 7 tranches (0% à 43%)',
            'description' => "Barème par tranche marginale : 0–630 000 → 0% ; 630 001–1 500 000 "
                . "→ 20% ; 1 500 001–4 000 000 → 30% ; 4 000 001–8 000 000 → 35% ; "
                . "8 000 001–13 500 000 → 37% ; 13 500 001–50 000 000 → 40% ; "
                . "au-delà de 50 000 000 → 43%. Application marginale, pas en taux unique.",
            'statut'  => 'confirme_source_privee',
            'sources' => [
                ['libelle' => 'Cabinet fiscal panafricain', 'url' => null],
                ['libelle' => 'tradingeconomics.com', 'url' => 'https://tradingeconomics.com/senegal/personal-income-tax-rate'],
            ],
            'note' => 'Les 40% et 43% coexistent à des paliers différents (ce n\'était pas un conflit : '
                . 'certaines sources tronquaient à 40%). PDF officiel DGID (IR-FLYERS.pdf) non extractible '
                . '→ repasser en confirme_officiel si accès au texte CGI brut.',
            'date_verification' => '2026-07-12',
        ],
        'IR-03' => [
            'categorie'   => 'irpp',
            'titre'       => 'Réduction pour charges de famille (10%/part)',
            'description' => "10% par part fiscale supplémentaire dans le système IRPP général. "
                . "HORS PÉRIMÈTRE : l'app ne gère aucune donnée de situation familiale / "
                . "quotient familial des propriétaires. Non demandé, non appliqué.",
            'statut'  => 'hors_perimetre',
            'sources' => [['libelle' => 'Référentiel général IRPP', 'url' => null]],
            'note' => 'Aucun champ quotient familial créé. Entrée conservée pour éviter qu\'un futur '
                . 'brief ne « redécouvre » la règle sans décision explicite.',
            'date_verification' => '2026-07-12',
        ],
        'IR-04' => [
            'categorie'   => 'irpp',
            'titre'       => 'Déclaration IRPP avant le 1er mars',
            'description' => "Déclaration des revenus non soumis à retenue à la source (dont revenus "
                . "locatifs) avant le 1er mars de chaque année. La source privée évoquant le 30 avril "
                . "est explicitement ÉCARTÉE (la source officielle prime).",
            'statut'  => 'confirme_officiel',
            'sources' => [['libelle' => 'DGID — Déclarer ses revenus (impotsetdomaines.gouv.sn)', 'url' => 'https://www.impotsetdomaines.gouv.sn/']],
            'note' => 'Le 30 avril (source privée) est écarté au profit du 1er mars (officiel), '
                . 'comme la CGF au 1er février prime sur le doc interne.',
            'date_verification' => '2026-07-12',
        ],

    ],
];
