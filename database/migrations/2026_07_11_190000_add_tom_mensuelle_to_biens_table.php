<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Ajoute biens.tom_mensuelle — Taxe sur les Ordures Ménagères facturée
 * mensuellement au locataire (FCFA, optionnel, défaut 0).
 *
 * Sert de valeur de référence : pré-remplit Contrat.tom_amount à la création
 * d'un bail. Entre dans l'assiette TVA du loyer (loyer_HT + TOM) via FiscalService.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('biens', function (Blueprint $table) {
            $table->decimal('tom_mensuelle', 15, 2)->default(0)->after('charges');
        });
    }

    public function down(): void
    {
        Schema::table('biens', function (Blueprint $table) {
            $table->dropColumn('tom_mensuelle');
        });
    }
};
