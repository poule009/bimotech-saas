<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;


/**
 * Migration : Enrichissement de la table `paiements` pour conformité
 * quittance sénégalaise (TOM, charges, référence bail éditable, loyer nu).
 *
 * TOM = Taxe sur les Ordures Ménagères (taxe municipale sénégalaise)
 * Loyer nu = loyer hors charges hors TOM (base de la commission agence)
 *
 * Lancez : php artisan migrate
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('paiements', function (Blueprint $table) {

            // ── Ventilation du loyer ──────────────────────────────────────
            // Loyer nu = montant_encaisse - charges_amount - tom_amount
            // C'est sur le loyer nu que s'applique la commission agence
            $table->decimal('loyer_nu', 12, 2)
                  ->default(0)
                  ->after('montant_encaisse')
                  ->comment('Loyer hors charges et hors TOM — base de commission');

            // Charges locatives mensuelles (eau, électricité, gardiennage…)
            $table->decimal('charges_amount', 12, 2)
                  ->default(0)
                  ->after('loyer_nu')
                  ->comment('Charges récupérables sur ce paiement');

            // TOM — Taxe sur les Ordures Ménagères (taxe municipale sénégalaise)
            // Répartie entre bailleur et locataire selon l'art. 8 loi bail sénégal
            $table->decimal('tom_amount', 10, 2)
                  ->default(0)
                  ->after('charges_amount')
                  ->comment('Taxe sur les Ordures Ménagères — part locataire');

            // ── Référence bail éditable ───────────────────────────────────
            // Permet à l'agence de saisir manuellement la référence du bail
            // (ex: numéro d'un contrat notarié, référence propre à l'agence)
            // Si null → on génère BAIL-{contrat_id} à l'affichage
            $table->string('reference_bail', 60)
                  ->nullable()
                  ->after('reference_paiement')
                  ->comment('Référence bail saisie manuellement — prioritaire sur la générée');

            // ── Index pour les recherches ─────────────────────────────────
            $table->index('reference_bail');
        });

        // ── Rétro-calcul des lignes existantes ────────────────────────────
        // Pour les paiements déjà en base : loyer_nu = montant_encaisse
        // (avant il n'y avait pas de séparation charges/TOM)
        DB::statement('UPDATE paiements SET loyer_nu = montant_encaisse WHERE loyer_nu = 0');
    }

    public function down(): void
    {
        Schema::table('paiements', function (Blueprint $table) {
            $table->dropIndex(['reference_bail']);
            $table->dropColumn([
                'loyer_nu',
                'charges_amount',
                'tom_amount',
                'reference_bail',
            ]);
        });
    }
};