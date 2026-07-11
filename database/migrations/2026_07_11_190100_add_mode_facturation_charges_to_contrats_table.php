<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Ajoute contrats.mode_facturation_charges — précise, quand des charges existent,
 * si elles sont refacturées à l'identique sans marge (débours, exonéré de TVA)
 * ou facturées en forfait fixe (assujetti à 18%).
 *
 * Distinct de « charges incluses/non incluses » (charges_mensuelles = 0 ou non).
 * Pilote charges_assujetties_tva : forfait → true (18%), débours → false (0%).
 * Traçabilité de la règle : regles_fiscales, clé 'tva_charges' (NON VÉRIFIÉ).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contrats', function (Blueprint $table) {
            // null = non précisé (pas de charges, ou héritage de l'ancienne logique par type de bail)
            $table->string('mode_facturation_charges', 20)->nullable()->after('charges_assujetties_tva');
        });
    }

    public function down(): void
    {
        Schema::table('contrats', function (Blueprint $table) {
            $table->dropColumn('mode_facturation_charges');
        });
    }
};
