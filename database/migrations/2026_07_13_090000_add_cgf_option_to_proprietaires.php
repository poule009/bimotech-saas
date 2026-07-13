<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Option CGF (Contribution Globale Foncière) au niveau du Propriétaire.
 *
 * Régime OPTIONNEL synthétique (Art. 75 CGI SN) exercé explicitement par le
 * propriétaire personne physique pour une année donnée, sur un loyer brut
 * PRÉVISIONNEL saisi manuellement (distinct des données Comptabilité réelles).
 * Si actif pour l'année N : masque les encarts IRPP-foncier et CFPB de la même
 * année (exclusion mutuelle — CGF-02), sans supprimer les données sous-jacentes.
 *
 * Réf. brief CGF §4 / regles_fiscales CGF-01..CGF-05.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('proprietaires', function (Blueprint $table) {
            $table->boolean('cgf_active')->default(false)->after('brs_dispense');
            $table->unsignedSmallInteger('cgf_annee')->nullable()->after('cgf_active');
            $table->unsignedBigInteger('cgf_revenu_brut_prevu')->nullable()->after('cgf_annee'); // FCFA, saisie manuelle
            $table->unsignedBigInteger('cgf_montant')->nullable()->after('cgf_revenu_brut_prevu'); // FCFA, calculé
            $table->enum('cgf_mode_paiement', ['unique', 'trois_versements'])->nullable()->after('cgf_montant');
            $table->json('cgf_echeances')->nullable()->after('cgf_mode_paiement'); // [{rang,libelle,date,montant}]
        });
    }

    public function down(): void
    {
        Schema::table('proprietaires', function (Blueprint $table) {
            $table->dropColumn([
                'cgf_active', 'cgf_annee', 'cgf_revenu_brut_prevu',
                'cgf_montant', 'cgf_mode_paiement', 'cgf_echeances',
            ]);
        });
    }
};
