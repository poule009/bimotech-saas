<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Audit fiscal C3 + C5 + R5 : nouveaux agrégats dans le bilan annuel.
 *
 * C3 — net_a_verser_total : le KPI "Net reversé" affichait net_proprietaire_total
 *      (avant BRS) alors que le montant réellement viré au propriétaire est
 *      net_a_verser_proprietaire (après BRS). Pour un propriétaire commercial,
 *      l'écart = brs_retenu_total, soit 15% des loyers TTC — erreur grave.
 *
 * C5 — tom_total : TOM inclus dans net_proprietaire mais non suivi séparément.
 *      Nécessaire pour avertir le propriétaire qu'il doit reverser cette taxe
 *      à la municipalité (circuit TOM → agence → propriétaire → mairie).
 *
 * R5 — tva_charges_total : TVA collectée sur les charges forfaitaires
 *      (baux commercial/mixte). Manquait dans le bilan pour réconciliation DGI.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('bilans_fiscaux_proprietaires')) {
            return;
        }

        Schema::table('bilans_fiscaux_proprietaires', function (Blueprint $table) {
            if (!Schema::hasColumn('bilans_fiscaux_proprietaires', 'net_a_verser_total')) {
                $table->decimal('net_a_verser_total', 12, 2)
                      ->default(0)
                      ->after('net_proprietaire_total')
                      ->comment('Somme de net_a_verser_proprietaire sur tous les paiements (après BRS). Montant effectivement viré.');
            }

            if (!Schema::hasColumn('bilans_fiscaux_proprietaires', 'tva_charges_total')) {
                $table->decimal('tva_charges_total', 12, 2)
                      ->default(0)
                      ->after('tva_loyer_collectee')
                      ->comment('TVA collectée sur charges forfaitaires (baux commercial/mixte) — à reverser DGI.');
            }

            if (!Schema::hasColumn('bilans_fiscaux_proprietaires', 'tom_total')) {
                $table->decimal('tom_total', 12, 2)
                      ->default(0)
                      ->after('tva_charges_total')
                      ->comment('TOM annuel collecté — à reverser à la municipalité compétente.');
            }
        });
    }

    public function down(): void
    {
        $cols = array_filter(
            ['net_a_verser_total', 'tva_charges_total', 'tom_total'],
            fn($c) => Schema::hasColumn('bilans_fiscaux_proprietaires', $c)
        );

        if ($cols) {
            Schema::table('bilans_fiscaux_proprietaires', fn (Blueprint $table) => $table->dropColumn(array_values($cols)));
        }
    }
};
