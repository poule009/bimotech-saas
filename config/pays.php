<?php

/**
 * Référentiel des pays — internationalisation Bimmo.
 *
 * ARCHITECTURE (décision 26 juil. 2026) :
 *
 *  1. Le STOCKAGE est générique : `agencies.pays` est un CHAR(2) libre (ISO 3166-1
 *     alpha-2). Aucun enum en base, aucune liste figée dans une migration. Ajouter
 *     un pays = ajouter une ligne dans ce fichier, sans migration ni déploiement DB.
 *
 *  2. La SÉLECTION est verrouillée : seuls les codes listés dans `ouverts` sont
 *     proposés à l'inscription et acceptés par la validation. C'est le garde-fou
 *     qui rend impossible la création accidentelle d'une agence dont le pays n'est
 *     pas réellement supporté (documents fiscaux, devise, mentions légales).
 *
 *     → Tant que le socle générique hors Sénégal n'est pas livré, `ouverts` ne
 *       contient que 'SN'. On ouvre un pays quand il est prêt, un par un.
 *
 *  3. Le pays porte une devise PAR DÉFAUT, pas une devise définitive. `agencies.devise`
 *     est une colonne distincte : un pays peut changer de monnaie, et certains pays
 *     en utilisent plusieurs de fait. Le pays pré-remplit la devise à la création,
 *     il ne la contraint pas ensuite.
 *
 * Voir config/devises.php pour le formatage monétaire (symbole, décimales).
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Pays ouverts à l'inscription
    |--------------------------------------------------------------------------
    |
    | Codes ISO alpha-2 réellement supportés par le produit à ce jour.
    | Un pays n'entre ici QUE lorsque son parcours est complet : devise correcte,
    | documents sans référence légale étrangère, moyens de paiement pertinents.
    |
    */

    'ouverts' => ['SN'],

    /*
    |--------------------------------------------------------------------------
    | Pays de référence
    |--------------------------------------------------------------------------
    |
    | Catalogue des pays connus du système, avec leur devise par défaut.
    | Y figurer ne signifie PAS être supporté — seul `ouverts` le décide.
    | Ce catalogue sert aux libellés d'affichage et au pré-remplissage.
    |
    */

    'liste' => [
        'DZ' => ['nom' => 'Algérie',              'devise' => 'DZD'],
        'BJ' => ['nom' => 'Bénin',                'devise' => 'XOF'],
        'BF' => ['nom' => 'Burkina Faso',         'devise' => 'XOF'],
        'BI' => ['nom' => 'Burundi',              'devise' => 'BIF'],
        'CM' => ['nom' => 'Cameroun',             'devise' => 'XAF'],
        'CV' => ['nom' => 'Cap-Vert',             'devise' => 'CVE'],
        'CF' => ['nom' => 'Centrafrique',         'devise' => 'XAF'],
        'KM' => ['nom' => 'Comores',              'devise' => 'KMF'],
        'CG' => ['nom' => 'Congo',                'devise' => 'XAF'],
        'CD' => ['nom' => 'Congo (RDC)',          'devise' => 'CDF'],
        'CI' => ['nom' => "Côte d'Ivoire",        'devise' => 'XOF'],
        'DJ' => ['nom' => 'Djibouti',             'devise' => 'DJF'],
        'EG' => ['nom' => 'Égypte',               'devise' => 'EGP'],
        'FR' => ['nom' => 'France',               'devise' => 'EUR'],
        'GA' => ['nom' => 'Gabon',                'devise' => 'XAF'],
        'GM' => ['nom' => 'Gambie',               'devise' => 'GMD'],
        'GH' => ['nom' => 'Ghana',                'devise' => 'GHS'],
        'GN' => ['nom' => 'Guinée',               'devise' => 'GNF'],
        'GQ' => ['nom' => 'Guinée équatoriale',   'devise' => 'XAF'],
        'GW' => ['nom' => 'Guinée-Bissau',        'devise' => 'XOF'],
        'KE' => ['nom' => 'Kenya',                'devise' => 'KES'],
        'MG' => ['nom' => 'Madagascar',           'devise' => 'MGA'],
        'ML' => ['nom' => 'Mali',                 'devise' => 'XOF'],
        'MA' => ['nom' => 'Maroc',                'devise' => 'MAD'],
        'MU' => ['nom' => 'Maurice',              'devise' => 'MUR'],
        'MR' => ['nom' => 'Mauritanie',           'devise' => 'MRU'],
        'NE' => ['nom' => 'Niger',                'devise' => 'XOF'],
        'NG' => ['nom' => 'Nigeria',              'devise' => 'NGN'],
        'UG' => ['nom' => 'Ouganda',              'devise' => 'UGX'],
        'RW' => ['nom' => 'Rwanda',               'devise' => 'RWF'],
        'SN' => ['nom' => 'Sénégal',              'devise' => 'XOF'],
        'SC' => ['nom' => 'Seychelles',           'devise' => 'SCR'],
        'TZ' => ['nom' => 'Tanzanie',             'devise' => 'TZS'],
        'TD' => ['nom' => 'Tchad',                'devise' => 'XAF'],
        'TG' => ['nom' => 'Togo',                 'devise' => 'XOF'],
        'TN' => ['nom' => 'Tunisie',              'devise' => 'TND'],
        'ZA' => ['nom' => 'Afrique du Sud',       'devise' => 'ZAR'],
    ],

];
