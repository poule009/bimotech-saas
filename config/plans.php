<?php

return [

    // Du plus bas au plus élevé
    'hierarchy' => ['starter', 'pro', 'agence'],

    // 'legacy' = mêmes accès que 'pro' (clients existants)
    'niveau_effectif' => [
        'starter' => 'starter',
        'pro'     => 'pro',
        'agence'  => 'agence',
        'legacy'  => 'pro',
    ],

    // Plan minimum requis par feature (clés utilisées dans les routes)
    'features' => [
        'immeubles'           => 'pro',
        'rapports_pdf'        => 'pro',
        'export_csv'          => 'pro',
        'releve_bailleur_pdf' => 'pro',
        'recherche_globale'   => 'pro',
        'import_excel'        => 'pro',
        'contrat_formel_pdf'  => 'pro',
        'fiscalite'           => 'agence',
        'bilans_fiscaux'      => 'agence',
        'logs_activite'       => 'agence',
    ],

    // Labels affichés dans les messages utilisateur
    'labels' => [
        'starter' => 'Starter',
        'pro'     => 'Pro',
        'agence'  => 'Agence',
        'legacy'  => 'Pro',
    ],

];
