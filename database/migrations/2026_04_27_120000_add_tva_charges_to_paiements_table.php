<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Audit fiscal C1 : TVA sur charges (baux commercial/meublé).
 *
 * La TVA sur charges était calculée dans FiscalService mais ni stockée en DB
 * ni affichée dans les vues. Cela créait une incohérence arithmétique dans
 * les quittances : la somme des lignes affichées ≠ total encaissé.
 *
 * CONTEXTE :
 *   Pour les baux commercial/mixte où chargesAssujettiesATva = true,
 *   les charges récupérables sont assujetties à TVA 18% (prestation de service).
 *   charges_ttc = charges_amount + tva_charges
 *   montant_encaisse inclut charges_ttc (pas seulement charges_amount).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('paiements', function (Blueprint $table) {
            $table->decimal('tva_charges', 12, 2)
                  ->default(0)
                  ->after('charges_amount')
                  ->comment('TVA 18% sur charges si bail commercial/mixte et chargesAssujettiesATva=true. 0 pour habitation.');

            $table->decimal('charges_ttc', 12, 2)
                  ->default(0)
                  ->after('tva_charges')
                  ->comment('charges_amount + tva_charges. = charges_amount pour habitation (tva_charges = 0).');
        });

        // Rétro-calcul : charges_ttc = charges_amount pour tous les paiements existants
        // (tva_charges reste à 0 pour les anciens enregistrements — valeur conservative)
        DB::statement('UPDATE paiements SET charges_ttc = charges_amount WHERE charges_ttc = 0');
    }

    public function down(): void
    {
        Schema::table('paiements', function (Blueprint $table) {
            $table->dropColumn(['tva_charges', 'charges_ttc']);
        });
    }
};
