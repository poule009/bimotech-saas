<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Audit TVA — F2 (version sûre) : les indicateurs `assujetti_tva` (agence + propriétaire)
 * pilotent désormais réellement le calcul (FiscalService).
 *
 * PRINCIPE DE PRUDENCE : `assujetti = false` par défaut. On ne facture JAMAIS une TVA
 * qui n'existe pas légalement. Tant qu'une agence n'a pas explicitement confirmé le
 * statut TVA (toggle coché), aucune TVA n'est appliquée — ni sur le loyer/charges
 * (côté propriétaire), ni sur la commission/frais (côté agence).
 *
 * On backfille donc les lignes existantes à false et on garde le défaut à false.
 * Une fiche non confirmée affiche « Statut TVA non confirmé — à vérifier ».
 */
return new class extends Migration
{
    public function up(): void
    {
        // Défaut explicite → false (opt-in)
        Schema::table('agencies', function (Blueprint $table) {
            $table->boolean('assujetti_tva')->default(false)->change();
        });
        Schema::table('proprietaires', function (Blueprint $table) {
            $table->boolean('assujetti_tva')->default(false)->change();
        });

        // Backfill sûr : tout le monde non assujetti jusqu'à confirmation explicite.
        DB::table('agencies')->update(['assujetti_tva' => false]);
        DB::table('proprietaires')->update(['assujetti_tva' => false]);
    }

    public function down(): void
    {
        // Rien à restaurer : le défaut d'origine était déjà false.
    }
};
