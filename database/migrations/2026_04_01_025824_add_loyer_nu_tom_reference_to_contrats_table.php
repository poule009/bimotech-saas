<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Ajoute à la table `contrats` les colonnes nécessaires
 * pour alimenter la quittance conforme sénégalaise :
 *
 *  - loyer_nu         : loyer hors charges et hors TOM (base de commission)
 *  - tom_amount       : Taxe sur les Ordures Ménagères (part locataire)
 *  - reference_bail   : référence bail saisie manuellement par l'agence
 *
 * Note : charges_mensuelles existe déjà en base (migration 2026_04_10_000003).
 * loyer_contractuel devient = loyer_nu + charges_mensuelles + tom_amount.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contrats', function (Blueprint $table) {

            // Loyer nu : loyer hors charges et hors TOM
            // C'est sur cette base que la commission agence est calculée
            $table->decimal('loyer_nu', 12, 2)
                ->default(0)
                ->after('loyer_contractuel')
                ->comment('Loyer hors charges et hors TOM — base de calcul commission');

            // TOM — Taxe sur les Ordures Ménagères (municipalités sénégalaises)
            $table->decimal('tom_amount', 10, 2)
                ->default(0)
                ->comment('Taxe sur les Ordures Ménagères — part locataire');

            // Référence bail saisie manuellement par l'agence
            // Si null → référence générée automatiquement (BIMO-YYYY-NNN)
            $table->string('reference_bail', 60)
                ->nullable()
                ->after('observations')
                ->comment('Référence officielle du bail — prioritaire sur la générée');

            $table->index('reference_bail');
        });

        // Rétro-compatibilité : pour les contrats existants,
        // loyer_nu = loyer_contractuel (pas de charges séparées avant)
        DB::statement('UPDATE contrats SET loyer_nu = loyer_contractuel WHERE loyer_nu = 0');
    }

    public function down(): void
    {
        Schema::table('contrats', function (Blueprint $table) {
            $table->dropIndex(['reference_bail']);
            $table->dropColumn(['loyer_nu', 'tom_amount', 'reference_bail']);
        });
    }
};
