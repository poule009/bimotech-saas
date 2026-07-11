<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Module Abonnement — déclaration de paiement manuelle.
 *
 * L'agence déclare son virement/Wave/OM avec un justificatif OBLIGATOIRE ; un admin
 * BIMO-tech confirme ou rejette (avec motif). D'où deux colonnes :
 *  - justificatif : chemin du reçu uploadé (requis à la déclaration)
 *  - motif_rejet  : raison affichée à l'agence en cas de rejet (jamais de rejet muet)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscription_payments', function (Blueprint $table) {
            if (! Schema::hasColumn('subscription_payments', 'justificatif')) {
                $table->string('justificatif')->nullable()->after('reference');
            }
            if (! Schema::hasColumn('subscription_payments', 'motif_rejet')) {
                $table->string('motif_rejet')->nullable()->after('notes');
            }
            // Niveau souscrit déclaré (starter|pro|agence). La colonne `plan` existante
            // est un enum de cycle (mensuel|annuel) et ne peut pas le stocker.
            if (! Schema::hasColumn('subscription_payments', 'plan_niveau')) {
                $table->string('plan_niveau', 20)->nullable()->after('plan');
            }
        });

        // Élargit `statut` (ancien enum restreint) en string pour accueillir les
        // statuts du flux manuel : en_attente | confirme | rejete.
        Schema::table('subscription_payments', function (Blueprint $table) {
            $table->string('statut', 20)->default('en_attente')->change();
        });
    }

    public function down(): void
    {
        Schema::table('subscription_payments', function (Blueprint $table) {
            foreach (['justificatif', 'motif_rejet', 'plan_niveau'] as $col) {
                if (Schema::hasColumn('subscription_payments', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
