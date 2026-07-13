<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * TEOM (Taxe d'Enlèvement des Ordures Ménagères) au niveau du Bien.
 *
 * Même assiette que la CFPB (valeur locative estimée, réutilisée), même badge
 * estimation_structurelle. Taux selon la commune : 3,6% à Dakar, 3% ailleurs.
 * Champs recalculés automatiquement par BienObserver (aucune saisie manuelle).
 *
 * Réf. brief TEOM §1 / regles_fiscales TEOM-01..TEOM-03.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('biens', function (Blueprint $table) {
            $table->decimal('teom_taux_applique', 3, 1)->nullable()->after('cfpb_statut_calcul'); // 3.6 ou 3.0
            $table->unsignedBigInteger('teom_montant_estime')->nullable()->after('teom_taux_applique'); // FCFA
            $table->string('teom_statut_calcul')->default('estimation_structurelle')->after('teom_montant_estime');
        });

        // Backfill : réutilise cfpb_valeur_locative_estimee, taux selon la ville (Dakar → 3,6%).
        DB::statement("
            UPDATE biens
            SET teom_taux_applique  = CASE WHEN LOWER(COALESCE(ville, '')) LIKE '%dakar%' THEN 3.6 ELSE 3.0 END,
                teom_montant_estime = ROUND(
                    COALESCE(cfpb_valeur_locative_estimee, 0)
                    * (CASE WHEN LOWER(COALESCE(ville, '')) LIKE '%dakar%' THEN 3.6 ELSE 3.0 END) / 100
                ),
                teom_statut_calcul  = 'estimation_structurelle'
        ");
    }

    public function down(): void
    {
        Schema::table('biens', function (Blueprint $table) {
            $table->dropColumn([
                'teom_taux_applique',
                'teom_montant_estime',
                'teom_statut_calcul',
            ]);
        });
    }
};
