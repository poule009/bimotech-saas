<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Corrige deux ENUM dans la table `proprietaires` :
 *
 * 1. `mode_paiement_prefere` : ajoute wave, orange_money, free_money, mobile_money
 *    (l'ancienne migration ne les avait pas — cause du bug "Données tronquées")
 *
 * 2. `genre` : passe de 'homme'/'femme' à 'M'/'F'
 *    (cohérence avec la validation du contrôleur et les autres tables)
 */
return new class extends Migration
{
    public function up(): void
    {
        // 1. Étendre l'ENUM mode_paiement_prefere
        DB::statement("
            ALTER TABLE proprietaires
            MODIFY COLUMN mode_paiement_prefere
            ENUM('especes','virement','cheque','mobile_money','wave','orange_money','free_money')
            NOT NULL DEFAULT 'virement'
        ");

        // 2. Corriger l'ENUM genre : 'homme'/'femme' → 'M'/'F'
        // D'abord convertir les données existantes
        DB::statement("UPDATE proprietaires SET genre = 'M' WHERE genre = 'homme'");
        DB::statement("UPDATE proprietaires SET genre = 'F' WHERE genre = 'femme'");

        // Puis modifier l'ENUM
        DB::statement("
            ALTER TABLE proprietaires
            MODIFY COLUMN genre ENUM('M','F') NULL
        ");
    }

    public function down(): void
    {
        // Remettre genre en 'homme'/'femme'
        DB::statement("UPDATE proprietaires SET genre = 'homme' WHERE genre = 'M'");
        DB::statement("UPDATE proprietaires SET genre = 'femme' WHERE genre = 'F'");

        DB::statement("
            ALTER TABLE proprietaires
            MODIFY COLUMN genre ENUM('homme','femme') NULL
        ");

        // Remettre l'ancien ENUM mode_paiement_prefere
        DB::statement("
            UPDATE proprietaires
            SET mode_paiement_prefere = 'mobile_money'
            WHERE mode_paiement_prefere IN ('wave','orange_money','free_money')
        ");

        DB::statement("
            ALTER TABLE proprietaires
            MODIFY COLUMN mode_paiement_prefere
            ENUM('especes','virement','mobile_money','cheque')
            NOT NULL DEFAULT 'virement'
        ");
    }
};