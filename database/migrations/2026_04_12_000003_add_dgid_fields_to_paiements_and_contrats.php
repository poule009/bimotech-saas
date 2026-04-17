<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Droits d'enregistrement DGID — CGI SN art. 442
 *
 * RÈGLE FISCALE :
 *   Assiette   = loyer_mensuel (loyer_nu + charges) × durée_mois
 *   Droits     = Assiette × taux_enregistrement%
 *   Total DGID = Droits + Timbre fiscal (fixe 2 000 FCFA)
 *
 * Taux légaux par défaut (CGI SN art. 442) :
 *   Bail d'habitation → 1%
 *   Bail commercial   → 2%
 *   Contrat custom    → taux_enregistrement_dgid (override)
 *
 * SÉCURITÉ D'AFFICHAGE :
 *   - dgid_* dans paiements : non nuls UNIQUEMENT au premier paiement.
 *   - Toutes les quittances récurrentes ont dgid_total = 0 → section cachée.
 *   - Ces frais ne modifient PAS montant_encaisse ni net_locataire.
 *     Ils sont une obligation fiscale séparée, payée directement à la DGID.
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── Snapshot DGID sur le premier paiement ────────────────────────────
        Schema::table('paiements', function (Blueprint $table) {
            $table->decimal('dgid_droits_enregistrement', 12, 2)
                ->default(0)
                ->after('montant_net_bailleur')
                ->comment('Droits d\'enregistrement = assiette × taux% (non nul uniquement premier paiement)');

            $table->decimal('dgid_timbre_fiscal', 12, 2)
                ->default(0)
                ->after('dgid_droits_enregistrement')
                ->comment('Timbre fiscal fixe (défaut 2 000 FCFA — CGI SN)');

            $table->decimal('dgid_total', 12, 2)
                ->default(0)
                ->after('dgid_timbre_fiscal')
                ->comment('Total DGID = droits_enregistrement + timbre_fiscal');
        });

        // ── Taux d'enregistrement override sur le contrat ────────────────────
        // null → FiscalService applique le taux légal (1% habitation, 2% commercial)
        Schema::table('contrats', function (Blueprint $table) {
            $table->decimal('taux_enregistrement_dgid', 5, 2)
                ->nullable()
                ->comment('Override taux enregistrement DGID en %. null = légal (1% hab / 2% commercial)');
        });
    }

    public function down(): void
    {
        Schema::table('paiements', function (Blueprint $table) {
            $table->dropColumn(['dgid_droits_enregistrement', 'dgid_timbre_fiscal', 'dgid_total']);
        });

        Schema::table('contrats', function (Blueprint $table) {
            $table->dropColumn('taux_enregistrement_dgid');
        });
    }
};
