<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Ajoute les champs manquants à la table contrats :
 *  - type_bail              : type de bail (habitation, commercial, mixte, saisonnier)
 *  - frais_agence           : frais d'agence perçus à la signature (FCFA)
 *  - charges_mensuelles     : charges locatives mensuelles (eau, électricité, gardiennage...)
 *  - indexation_annuelle    : taux d'indexation annuelle du loyer (%)
 *  - nombre_mois_caution    : nombre de mois de caution (1, 2 ou 3 mois)
 *  - garant_nom             : nom complet du garant (si applicable)
 *  - garant_telephone       : téléphone du garant
 *  - garant_adresse         : adresse du garant
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contrats', function (Blueprint $table) {
            $table->enum('type_bail', ['habitation', 'commercial', 'mixte', 'saisonnier'])
                  ->default('habitation')
                  ->after('statut');

            $table->decimal('frais_agence', 12, 2)
                  ->default(0)
                  ->after('caution');

            $table->decimal('charges_mensuelles', 12, 2)
                  ->default(0)
                  ->after('frais_agence');

            $table->decimal('indexation_annuelle', 5, 2)
                  ->default(0)
                  ->after('charges_mensuelles');

            $table->unsignedTinyInteger('nombre_mois_caution')
                  ->default(1)
                  ->after('indexation_annuelle');

            $table->string('garant_nom', 150)->nullable()->after('nombre_mois_caution');
            $table->string('garant_telephone', 20)->nullable()->after('garant_nom');
            $table->string('garant_adresse', 255)->nullable()->after('garant_telephone');
        });
    }

    public function down(): void
    {
        Schema::table('contrats', function (Blueprint $table) {
            $table->dropColumn([
                'type_bail',
                'frais_agence',
                'charges_mensuelles',
                'indexation_annuelle',
                'nombre_mois_caution',
                'garant_nom',
                'garant_telephone',
                'garant_adresse',
            ]);
        });
    }
};
