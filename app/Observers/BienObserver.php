<?php

namespace App\Observers;

use App\Models\Bien;
use App\Services\FiscalService;

/**
 * BienObserver — Dérive automatiquement les estimations CFPB et TEOM d'un Bien.
 *
 * La CFPB estimée = loyer_mensuel × 12 × 5% (ESTIMATION STRUCTURELLE — la valeur
 * locative cadastrale réelle est inconnue de l'app). La TEOM réutilise la même
 * valeur locative × taux commune (3,6% Dakar / 3% ailleurs). Recalculées à chaque
 * création/modification à partir de loyer_mensuel/ville : aucune saisie manuelle.
 *
 * RÈGLE ANTI-BOUCLE : on utilise saving() (avant SQL, en mémoire) — aucun
 * save()/update() appelé ici, donc 0 requête supplémentaire et 0 récursion.
 *
 * Enregistré dans AppServiceProvider::boot() (comme ContratObserver).
 */
class BienObserver
{
    public function saving(Bien $bien): void
    {
        $cfpb = FiscalService::estimerCfpbBien((float) $bien->loyer_mensuel);

        $bien->cfpb_valeur_locative_estimee = $cfpb['valeur_locative_estimee'];
        $bien->cfpb_montant_estime          = $cfpb['montant_estime'];
        $bien->cfpb_statut_calcul           = $cfpb['statut_calcul'];

        // TEOM — même assiette (valeur locative CFPB réutilisée), taux selon la commune.
        $teom = FiscalService::estimerTeomBien(
            $cfpb['valeur_locative_estimee'],
            FiscalService::bienEstADakar($bien->ville)
        );

        $bien->teom_taux_applique  = $teom['taux_applique'];
        $bien->teom_montant_estime = $teom['montant_estime'];
        $bien->teom_statut_calcul  = $teom['statut_calcul'];
    }
}
