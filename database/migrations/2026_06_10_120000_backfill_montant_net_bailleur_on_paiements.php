<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * BACKFILL — paiements.montant_net_bailleur
 *
 * La colonne `montant_net_bailleur` a été ajoutée le 12/04/2026 avec `->default(0)`
 * SANS mise à jour des paiements existants (migration 2026_04_12_000002). Tout paiement
 * antérieur conserve donc la valeur 0, alors que `net_a_verser_proprietaire` porte le
 * vrai montant.
 *
 * Conséquence : tous les calculs qui s'appuient sur `montant_net_bailleur` sous-estiment
 * le dû bailleur sur l'historique :
 *   - ComptabiliteService::soldesMandants()  (vue liste des soldes)
 *   - Paiement::getNetFinalBailleurAttribute() (= montant_net_bailleur − dépenses)
 *   - BailleurController (relevés/rapports PDF)
 *
 * Ce backfill reconstruit la valeur avec la MÊME formule qu'à l'écriture
 * (cf. FiscalService::calculer, étape 8) :
 *
 *   montant_net_bailleur = net_a_verser_proprietaire
 *                          + caution_montant   (si la caution est remise au bailleur)
 *                          + 0                 (si l'agence garde la caution en séquestre)
 *
 * Prédicat de sécurité : on ne touche QUE les lignes manifestement non backfillées
 * (montant_net_bailleur = 0 alors que net_a_verser_proprietaire ≠ 0). Une ligne écrite
 * après la migration aura toujours montant_net_bailleur ≠ 0 si net_a_verser ≠ 0 →
 * idempotent, rejouable sans risque de double comptage.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            UPDATE paiements p
            JOIN contrats c ON c.id = p.contrat_id
            SET p.montant_net_bailleur =
                    p.net_a_verser_proprietaire
                  + CASE
                        WHEN COALESCE(c.caution_gardee_par_agence, 0) = 0
                            THEN COALESCE(p.caution_montant, 0)
                        ELSE 0
                    END
            WHERE p.montant_net_bailleur = 0
              AND p.net_a_verser_proprietaire <> 0
        ");
    }

    public function down(): void
    {
        // Irréversible : la valeur d'origine (0) résultait d'un défaut de migration,
        // pas d'une donnée métier. Aucun rollback pertinent.
    }
};
