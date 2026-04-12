<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Ajoute les colonnes de frais de dossier et caution à la table `paiements`.
 *
 * NOUVEAU FLUX PREMIER VERSEMENT :
 *
 *  montant_encaisse           ← loyer TTC + charges + TOM (mensuel récurrent)
 *  + frais_agence_ttc         ← honoraires agence à la signature (one-shot)
 *  + caution_montant          ← dépôt de garantie contractuel (non taxable)
 *  = total_encaissement_initial ← total réellement versé lors de l'entrée
 *
 *  frais_agence_ht            ← base HT des honoraires
 *  + tva_frais_agence (18%)   ← TVA sur honoraires (Art. 357 CGI SN)
 *  = frais_agence_ttc
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('paiements', function (Blueprint $table) {
            $table->decimal('frais_agence_ht', 12, 2)
                  ->default(0)
                  ->after('caution_percue')
                  ->comment('Frais de dossier HT — honoraires agence perçus à la signature');

            $table->decimal('tva_frais_agence', 12, 2)
                  ->default(0)
                  ->after('frais_agence_ht')
                  ->comment('TVA 18% sur frais_agence_ht (Art. 357 CGI SN)');

            $table->decimal('frais_agence_ttc', 12, 2)
                  ->default(0)
                  ->after('tva_frais_agence')
                  ->comment('frais_agence_ht + tva_frais_agence');

            $table->decimal('caution_montant', 12, 2)
                  ->default(0)
                  ->after('frais_agence_ttc')
                  ->comment('Dépôt de garantie contractuel (non taxable — restitué à la sortie)');

            $table->decimal('total_encaissement_initial', 12, 2)
                  ->default(0)
                  ->after('caution_montant')
                  ->comment('Total premier versement : montant_encaisse + frais_agence_ttc + caution_montant');
        });
    }

    public function down(): void
    {
        Schema::table('paiements', function (Blueprint $table) {
            $table->dropColumn([
                'frais_agence_ht',
                'tva_frais_agence',
                'frais_agence_ttc',
                'caution_montant',
                'total_encaissement_initial',
            ]);
        });
    }
};
