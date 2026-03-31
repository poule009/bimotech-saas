<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Ajoute les opérateurs mobile money sénégalais à l'enum mode_paiement :
 *  - wave         : Wave (le plus utilisé au Sénégal)
 *  - orange_money : Orange Money
 *  - free_money   : Free Money (Expresso)
 *
 * L'ancienne valeur 'mobile_money' est conservée pour la rétrocompatibilité.
 */
return new class extends Migration
{
    public function up(): void
    {
        // MySQL : modifier un ENUM nécessite de redéfinir toutes les valeurs
        DB::statement("
            ALTER TABLE paiements
            MODIFY COLUMN mode_paiement ENUM(
                'especes',
                'virement',
                'cheque',
                'mobile_money',
                'wave',
                'orange_money',
                'free_money'
            ) NOT NULL DEFAULT 'especes'
        ");
    }

    public function down(): void
    {
        // Remettre l'ancien enum (les lignes avec wave/orange_money/free_money
        // seront converties en 'mobile_money' pour éviter les erreurs)
        DB::statement("
            UPDATE paiements
            SET mode_paiement = 'mobile_money'
            WHERE mode_paiement IN ('wave', 'orange_money', 'free_money')
        ");

        DB::statement("
            ALTER TABLE paiements
            MODIFY COLUMN mode_paiement ENUM(
                'especes',
                'virement',
                'cheque',
                'mobile_money'
            ) NOT NULL DEFAULT 'especes'
        ");
    }
};
