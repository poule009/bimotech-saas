<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Enrichit la table `paiements` pour la conformité fiscale complète.
 *
 * NOUVEAU MODÈLE DE CALCUL :
 *
 *  Loyer HT (loyer_ht)
 *  + TVA loyer 18% (tva_loyer)         ← si bail commercial/meublé
 *  = Loyer TTC (loyer_ttc)
 *  + Charges (charges_amount)           ← JAMAIS taxées
 *  + TOM (tom_amount)                   ← JAMAIS taxée
 *  = TOTAL ENCAISSÉ (montant_encaisse)
 *
 *  Commission HT sur loyer_ht
 *  + TVA commission 18%
 *  = Commission TTC
 *
 *  NET PROPRIÉTAIRE = montant_encaisse - commission_ttc
 *
 *  BRS (15% × loyer_ttc)               ← si locataire entreprise
 *
 *  NET À VERSER = net_proprietaire - brs_amount
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('paiements', function (Blueprint $table) {

            // ── VENTILATION TVA LOYER ────────────────────────────────────────
            // Distinct de loyer_nu (alias rétro-compat) pour la clarté comptable

            $table->decimal('loyer_ht', 12, 2)
                  ->default(0)
                  ->after('loyer_nu')
                  ->comment('Loyer hors TVA — assiette BRS et commission. = loyer_nu pour habitation');

            $table->decimal('tva_loyer', 12, 2)
                  ->default(0)
                  ->after('loyer_ht')
                  ->comment('TVA 18% sur loyer HT — 0 pour bail habitation non meublé');

            $table->decimal('loyer_ttc', 12, 2)
                  ->default(0)
                  ->after('tva_loyer')
                  ->comment('loyer_ht + tva_loyer. = loyer_ht pour habitation');

            // ── BRS — RETENUE À LA SOURCE ────────────────────────────────────

            $table->decimal('brs_amount', 12, 2)
                  ->default(0)
                  ->after('net_proprietaire')
                  ->comment('BRS retenue par le locataire entreprise (Art. 196bis CGI SN)');

            $table->decimal('taux_brs_applique', 5, 2)
                  ->default(0)
                  ->after('brs_amount')
                  ->comment('Taux BRS figé à la création (0, 5 ou 15%). Immutable après validation');

            // ── NET À VERSER ─────────────────────────────────────────────────

            $table->decimal('net_a_verser_proprietaire', 12, 2)
                  ->default(0)
                  ->after('taux_brs_applique')
                  ->comment('net_proprietaire - brs_amount. Montant effectivement viré au propriétaire');

            // ── SNAPSHOT FISCAL ──────────────────────────────────────────────
            // Photographie de tous les paramètres fiscaux au moment du paiement.
            // Garantit que la quittance reste exacte même si les taux changent après.
            $table->json('regime_fiscal_snapshot')
                  ->nullable()
                  ->after('net_a_verser_proprietaire')
                  ->comment('Snapshot JSON du FiscalResult — immuable après validation');
        });

        // ── Rétro-calcul pour les paiements existants ────────────────────────
        // Pour les habitation/particulier : loyer_ht = loyer_nu, loyer_ttc = loyer_nu
        //                                  brs = 0, net_a_verser = net_proprietaire
        DB::statement('
            UPDATE paiements
            SET loyer_ht                   = loyer_nu,
                tva_loyer                  = 0,
                loyer_ttc                  = loyer_nu,
                brs_amount                 = 0,
                taux_brs_applique          = 0,
                net_a_verser_proprietaire  = net_proprietaire
            WHERE loyer_ht = 0
            AND statut IN ("valide", "annule")
        ');
    }

    public function down(): void
    {
        Schema::table('paiements', function (Blueprint $table) {
            $table->dropColumn([
                'loyer_ht',
                'tva_loyer',
                'loyer_ttc',
                'brs_amount',
                'taux_brs_applique',
                'net_a_verser_proprietaire',
                'regime_fiscal_snapshot',
            ]);
        });
    }
};
