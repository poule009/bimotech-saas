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

    // ── Changement de modèle économique (juillet 2026) ───────────────────────
    // TOUTES les fonctionnalités sont désormais disponibles sur TOUS les plans.
    // Seuls les compteurs (nb de biens, nb de comptes équipe) varient selon le plan.
    // Cette map est volontairement vide : PlanFeatureService::canAccess() renvoie
    // alors `true` pour toute feature, ce qui neutralise les middleware check.feature
    // sans avoir à les retirer route par route. Ne PAS repeupler cette map — ce serait
    // reverrouiller des fonctionnalités par plan, contraire au modèle actuel.
    'features' => [],

    // Nombre max de collaborateurs admins par agence (directeur inclus)
    'nb_admins_max' => [
        'starter' => 2,
        'pro'     => 5,
        'agence'  => null, // illimité
        'legacy'  => 5,
    ],

    // Nombre max d'unités (biens actifs) par agence selon le plan
    'nb_unites_max' => [
        'starter' => 15,
        'pro'     => 50,
        'agence'  => null, // illimité
        'legacy'  => 50,
    ],

    // Labels affichés dans les messages utilisateur
    'labels' => [
        'starter' => 'Starter',
        'pro'     => 'Pro',
        'agence'  => 'Agence',
        'legacy'  => 'Pro',
    ],

];
