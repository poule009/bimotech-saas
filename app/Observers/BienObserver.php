<?php

namespace App\Observers;

use App\Models\Bien;
use App\Services\FiscalService;

/**
 * BienObserver — Dérive automatiquement l'estimation CFPB d'un Bien.
 *
 * La CFPB estimée = loyer_mensuel × 12 × 5% (ESTIMATION STRUCTURELLE — la valeur
 * locative cadastrale réelle est inconnue de l'app). Recalculée à chaque
 * création/modification à partir de loyer_mensuel : aucune saisie manuelle.
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
    }
}
