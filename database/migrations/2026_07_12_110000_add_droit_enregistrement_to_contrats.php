<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Module fiscal — Droits d'enregistrement du bail (DGID).
 *
 * Tracker AU NIVEAU CONTRAT (calculé par ContratObserver depuis les champs du
 * contrat) : montant des droits, timbre (2 000 F × nombre de feuilles), statut
 * du calcul (confirme / estimation), date limite (signature + 1 mois) et suivi
 * « effectué ». Réutilise les champs existants taux_enregistrement_dgid (override
 * de taux) et enregistrement_exonere (exonération). Ne touche pas au snapshot
 * DGID du premier paiement (dgid_* sur paiements).
 *
 * R6 (référentiel interne « plafond 12 mois ») NON CONFIRMÉ : pour tout bail
 * > 12 mois le montant est une ESTIMATION (statut = 'estimation'), le
 * fractionnement triennal (Art. 510) pouvant porter la base jusqu'à 36 mois.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contrats', function (Blueprint $table) {
            $table->decimal('droit_enreg_montant', 15, 2)->nullable()->after('taux_enregistrement_dgid');
            $table->decimal('droit_enreg_timbre', 12, 2)->nullable()->after('droit_enreg_montant');
            $table->unsignedSmallInteger('droit_enreg_nombre_feuilles')->default(2)->after('droit_enreg_timbre');
            $table->string('droit_enreg_statut_calcul', 20)->nullable()->after('droit_enreg_nombre_feuilles'); // confirme | estimation
            $table->date('droit_enreg_date_limite')->nullable()->after('droit_enreg_statut_calcul');
            $table->boolean('droit_enreg_renouvelable')->default(true)->after('droit_enreg_date_limite');
            $table->boolean('droit_enreg_effectue')->default(false)->after('droit_enreg_renouvelable');
            $table->date('droit_enreg_date_effectue')->nullable()->after('droit_enreg_effectue');
        });
    }

    public function down(): void
    {
        Schema::table('contrats', function (Blueprint $table) {
            $table->dropColumn([
                'droit_enreg_montant', 'droit_enreg_timbre', 'droit_enreg_nombre_feuilles',
                'droit_enreg_statut_calcul', 'droit_enreg_date_limite', 'droit_enreg_renouvelable',
                'droit_enreg_effectue', 'droit_enreg_date_effectue',
            ]);
        });
    }
};
