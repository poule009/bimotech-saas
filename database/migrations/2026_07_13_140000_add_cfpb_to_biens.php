<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * CFPB (Contribution Foncière des Propriétés Bâties) au niveau du Bien.
 *
 * La CFPB est liée à l'immeuble (pas au propriétaire, contrairement à CGF/IRPP).
 * L'app ne connaît pas la valeur locative CADASTRALE réelle → ESTIMATION
 * STRUCTURELLE dérivée du loyer de référence (loyer_mensuel × 12 × 5%).
 * Champs recalculés automatiquement par BienObserver (aucune saisie manuelle).
 *
 * Réf. brief CFPB §4 / regles_fiscales CFPB-01..CFPB-06.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('biens', function (Blueprint $table) {
            $table->unsignedBigInteger('cfpb_valeur_locative_estimee')->nullable()->after('loyer_mensuel'); // FCFA, = loyer_mensuel × 12
            $table->unsignedBigInteger('cfpb_montant_estime')->nullable()->after('cfpb_valeur_locative_estimee'); // FCFA, = valeur × 5%
            $table->string('cfpb_statut_calcul')->default('estimation_structurelle')->after('cfpb_montant_estime');
        });

        // Backfill des biens existants (même formule que BienObserver / FiscalService).
        DB::statement("
            UPDATE biens
            SET cfpb_valeur_locative_estimee = ROUND(COALESCE(loyer_mensuel, 0) * 12),
                cfpb_montant_estime          = ROUND(COALESCE(loyer_mensuel, 0) * 12 * 0.05),
                cfpb_statut_calcul           = 'estimation_structurelle'
        ");
    }

    public function down(): void
    {
        Schema::table('biens', function (Blueprint $table) {
            $table->dropColumn([
                'cfpb_valeur_locative_estimee',
                'cfpb_montant_estime',
                'cfpb_statut_calcul',
            ]);
        });
    }
};
