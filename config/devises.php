<?php

/**
 * Référentiel des devises — formatage monétaire Bimmo.
 *
 * `decimales` suit la norme ISO 4217 : le franc CFA (XOF/XAF) n'a AUCUNE
 * subdivision en circulation, afficher « 150 000,00 FCFA » sur une quittance
 * serait une précision qui n'existe pas. À l'inverse le dirham ou le naira en
 * ont deux.
 *
 * ⚠ Ne concerne que l'AFFICHAGE. Le stockage en base reste sur 2 décimales pour
 * TOUTES les devises : les calculs intermédiaires (BRS 5 %, TVA 18 %, prorata au
 * jour) produisent des décimales, et les tronquer ferait dériver les cumuls
 * annuels. On arrondit à l'affichage, jamais à l'enregistrement.
 *
 * `position` : 'apres' → « 150 000 FCFA » | 'avant' → « $150,000.00 ».
 *
 * Utilisé par la directive Blade @money (voir AppServiceProvider).
 *
 * ⚠ ÉTAPE 1 — ce référentiel est posé mais pas encore branché : @money formate
 * toujours en FCFA en dur. Le raccordement est l'étape 2 du chantier
 * d'internationalisation (53 appels à number_format() à faire converger).
 */

return [

    'XOF' => ['nom' => 'Franc CFA (UEMOA)',  'symbole' => 'FCFA', 'decimales' => 0, 'position' => 'apres'],
    'XAF' => ['nom' => 'Franc CFA (CEMAC)',  'symbole' => 'FCFA', 'decimales' => 0, 'position' => 'apres'],
    'MAD' => ['nom' => 'Dirham marocain',    'symbole' => 'MAD',  'decimales' => 2, 'position' => 'apres'],
    'TND' => ['nom' => 'Dinar tunisien',     'symbole' => 'TND',  'decimales' => 3, 'position' => 'apres'],
    'DZD' => ['nom' => 'Dinar algérien',     'symbole' => 'DZD',  'decimales' => 2, 'position' => 'apres'],
    'EGP' => ['nom' => 'Livre égyptienne',   'symbole' => 'EGP',  'decimales' => 2, 'position' => 'apres'],
    'NGN' => ['nom' => 'Naira',              'symbole' => '₦',    'decimales' => 2, 'position' => 'avant'],
    'GHS' => ['nom' => 'Cedi ghanéen',       'symbole' => 'GH₵',  'decimales' => 2, 'position' => 'avant'],
    'GNF' => ['nom' => 'Franc guinéen',      'symbole' => 'GNF',  'decimales' => 0, 'position' => 'apres'],
    'GMD' => ['nom' => 'Dalasi',             'symbole' => 'GMD',  'decimales' => 2, 'position' => 'apres'],
    'MRU' => ['nom' => 'Ouguiya',            'symbole' => 'MRU',  'decimales' => 2, 'position' => 'apres'],
    'CVE' => ['nom' => 'Escudo cap-verdien', 'symbole' => 'CVE',  'decimales' => 2, 'position' => 'apres'],
    'CDF' => ['nom' => 'Franc congolais',    'symbole' => 'CDF',  'decimales' => 2, 'position' => 'apres'],
    'DJF' => ['nom' => 'Franc djiboutien',   'symbole' => 'DJF',  'decimales' => 0, 'position' => 'apres'],
    'KES' => ['nom' => 'Shilling kényan',    'symbole' => 'KSh',  'decimales' => 2, 'position' => 'avant'],
    'TZS' => ['nom' => 'Shilling tanzanien', 'symbole' => 'TSh',  'decimales' => 2, 'position' => 'avant'],
    'UGX' => ['nom' => 'Shilling ougandais', 'symbole' => 'USh',  'decimales' => 0, 'position' => 'avant'],
    'RWF' => ['nom' => 'Franc rwandais',     'symbole' => 'RWF',  'decimales' => 0, 'position' => 'apres'],
    'BIF' => ['nom' => 'Franc burundais',    'symbole' => 'BIF',  'decimales' => 0, 'position' => 'apres'],
    'KMF' => ['nom' => 'Franc comorien',     'symbole' => 'KMF',  'decimales' => 0, 'position' => 'apres'],
    'MGA' => ['nom' => 'Ariary',             'symbole' => 'MGA',  'decimales' => 2, 'position' => 'apres'],
    'MUR' => ['nom' => 'Roupie mauricienne', 'symbole' => 'Rs',   'decimales' => 2, 'position' => 'avant'],
    'SCR' => ['nom' => 'Roupie seychelloise','symbole' => 'SCR',  'decimales' => 2, 'position' => 'apres'],
    'ZAR' => ['nom' => 'Rand',               'symbole' => 'R',    'decimales' => 2, 'position' => 'avant'],
    'EUR' => ['nom' => 'Euro',               'symbole' => '€',    'decimales' => 2, 'position' => 'apres'],
    'USD' => ['nom' => 'Dollar américain',   'symbole' => '$',    'decimales' => 2, 'position' => 'avant'],

];
