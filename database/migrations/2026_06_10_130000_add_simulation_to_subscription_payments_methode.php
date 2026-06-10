<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Ajoute 'simulation' à l'ENUM subscription_payments.methode.
 *
 * Bug : PaymentService::initierPaiement() enregistre un paiement avec
 * `methode => 'simulation'` en mode simulation (cf. PaymentService ligne ~259),
 * mais l'ENUM d'origine ne listait que paytech/paydunya/wave/orange_money/
 * virement/manuel. En MySQL strict, l'insertion échoue (« Data truncated for
 * column 'methode' ») → 500 sur tout paiement en mode simulation.
 *
 * On aligne le schéma sur le code en ajoutant la valeur 'simulation'.
 */
return new class extends Migration
{
    private const VALEURS = "'paytech','paydunya','wave','orange_money','virement','manuel','simulation'";
    private const VALEURS_ORIGINE = "'paytech','paydunya','wave','orange_money','virement','manuel'";

    public function up(): void
    {
        DB::statement(
            "ALTER TABLE `subscription_payments` MODIFY `methode` ENUM(" . self::VALEURS . ") NOT NULL DEFAULT 'manuel'"
        );
    }

    public function down(): void
    {
        // Repli vers 'manuel' avant de retirer la valeur, sinon ALTER échoue
        // sur d'éventuelles lignes 'simulation'.
        DB::statement("UPDATE `subscription_payments` SET `methode` = 'manuel' WHERE `methode` = 'simulation'");
        DB::statement(
            "ALTER TABLE `subscription_payments` MODIFY `methode` ENUM(" . self::VALEURS_ORIGINE . ") NOT NULL DEFAULT 'manuel'"
        );
    }
};
