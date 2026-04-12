<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Ajoute les colonnes de nets consolidés à `paiements`
 * et la politique de caution à `contrats`.
 *
 * NOUVEAUX CHAMPS :
 *
 * paiements.montant_net_locataire
 *   = total_encaissement_initial - brs_amount
 *   Montant effectivement viré par le locataire (après retenue BRS à la source).
 *
 * paiements.montant_net_bailleur
 *   = net_a_verser_proprietaire + caution_montant  (si caution remise au bailleur)
 *   = net_a_verser_proprietaire                    (si caution gardée par l'agence)
 *   Montant total reversé au bailleur lors du premier paiement.
 *
 * contrats.caution_gardee_par_agence
 *   false (défaut) → la caution est remise au bailleur (incluse dans montant_net_bailleur)
 *   true           → l'agence conserve la caution en séquestre (exclue du versement bailleur)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('paiements', function (Blueprint $table) {
            $table->decimal('montant_net_locataire', 12, 2)
                  ->default(0)
                  ->after('total_encaissement_initial')
                  ->comment('Net locataire = total_encaissement_initial - brs_amount (après retenue BRS)');

            $table->decimal('montant_net_bailleur', 12, 2)
                  ->default(0)
                  ->after('montant_net_locataire')
                  ->comment('Net bailleur = net_a_verser_proprietaire [+ caution si remise au bailleur]');
        });

        Schema::table('contrats', function (Blueprint $table) {
            $table->boolean('caution_gardee_par_agence')
                  ->default(false)
                  ->after('nombre_mois_caution')
                  ->comment('true = l\'agence garde la caution en séquestre (non reversée au bailleur)');
        });
    }

    public function down(): void
    {
        Schema::table('paiements', function (Blueprint $table) {
            $table->dropColumn(['montant_net_locataire', 'montant_net_bailleur']);
        });

        Schema::table('contrats', function (Blueprint $table) {
            $table->dropColumn('caution_gardee_par_agence');
        });
    }
};
