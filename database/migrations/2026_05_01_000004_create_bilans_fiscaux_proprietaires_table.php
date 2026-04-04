<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Crée la table `bilans_fiscaux_proprietaires`.
 *
 * Cette table stocke le bilan fiscal annuel calculé pour chaque propriétaire.
 * Elle permet à l'agence de fournir à chaque propriétaire un récapitulatif
 * exploitable pour sa déclaration DGI de revenus fonciers.
 *
 * RÈGLES FISCALES APPLIQUÉES (CGI Sénégal) :
 *  - Abattement forfaitaire 30% sur revenus bruts (Art. 58-62)
 *  - Barème IRPP progressif sur la base imposable (Art. 65)
 *  - CFPB : Contribution Foncière des Propriétés Bâties (Art. 95-110)
 *  - TVA collectée à reverser DGI (loyers commerciaux)
 *  - BRS retenu par locataires entreprises (à déclarer par le locataire)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bilans_fiscaux_proprietaires', function (Blueprint $table) {
            $table->id();

            // Clés étrangères
            $table->foreignId('agency_id')
                  ->constrained('agencies')
                  ->onDelete('cascade');

            $table->foreignId('proprietaire_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            // Année fiscale (ex: 2025)
            $table->smallInteger('annee')->unsigned();

            // ── REVENUS BRUTS ─────────────────────────────────────────────
            $table->decimal('revenus_bruts_loyers', 15, 2)
                  ->default(0)
                  ->comment('Somme des loyers_ht annuels perçus par le propriétaire');

            $table->decimal('revenus_bruts_charges', 15, 2)
                  ->default(0)
                  ->comment('Somme des charges refacturées aux locataires');

            $table->decimal('revenus_bruts_total', 15, 2)
                  ->default(0)
                  ->comment('Total loyers + charges encaissés');

            // ── ABATTEMENT 30% (Art. 58 CGI SN) ──────────────────────────
            $table->decimal('abattement_forfaitaire_30', 15, 2)
                  ->default(0)
                  ->comment('30% × revenus_bruts_loyers (art.58 — hors charges)');

            $table->decimal('base_imposable', 15, 2)
                  ->default(0)
                  ->comment('revenus_bruts_loyers × 70% = base pour IRPP');

            // ── IRPP ESTIMÉ (Art. 65 CGI SN) ─────────────────────────────
            $table->decimal('irpp_estime', 12, 2)
                  ->default(0)
                  ->comment('Impôt estimé selon barème IRPP progressif sénégalais');

            // ── CFPB (Art. 95-110 CGI SN) ────────────────────────────────
            $table->decimal('cfpb_estimee', 12, 2)
                  ->default(0)
                  ->comment('Contribution Foncière des Propriétés Bâties estimée');

            // ── TVA LOYER COLLECTÉE ───────────────────────────────────────
            $table->decimal('tva_loyer_collectee', 12, 2)
                  ->default(0)
                  ->comment('TVA 18% collectée sur loyers commerciaux/meublés — à reverser DGI');

            // ── BRS RETENU PAR LES LOCATAIRES ────────────────────────────
            $table->decimal('brs_retenu_total', 12, 2)
                  ->default(0)
                  ->comment('BRS total retenu par les locataires entreprises sur l\'année');

            // ── COMMISSIONS AGENCE ────────────────────────────────────────
            $table->decimal('commissions_agence_ht', 12, 2)
                  ->default(0)
                  ->comment('Total commissions HT déduites par l\'agence');

            $table->decimal('tva_commissions', 12, 2)
                  ->default(0)
                  ->comment('TVA sur commissions agence (à déduire pour le propriétaire assujetti)');

            // ── NETS ──────────────────────────────────────────────────────
            $table->decimal('net_proprietaire_total', 15, 2)
                  ->default(0)
                  ->comment('Net total reversé au propriétaire sur l\'année');

            $table->integer('nb_paiements')
                  ->default(0);

            $table->integer('nb_biens_geres')
                  ->default(0);

            // Métadonnées
            $table->timestamp('calcule_le')->useCurrent();
            $table->timestamps();

            // Contrainte unicité : un seul bilan par propriétaire par année par agence
            $table->unique(['agency_id', 'proprietaire_id', 'annee'], 'bilan_unique_proprio_annee');

            // Index pour les requêtes fréquentes
            $table->index(['agency_id', 'annee'], 'bilan_agency_annee_idx');
            $table->index(['proprietaire_id', 'annee'], 'bilan_proprio_annee_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bilans_fiscaux_proprietaires');
    }
};