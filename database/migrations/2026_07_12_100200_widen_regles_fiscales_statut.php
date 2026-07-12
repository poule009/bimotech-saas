<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Audit BRS — B4 : le champ statut de regles_fiscales passe d'ENUM à string.
 *
 * Motif : certaines règles (ex. cascade de priorité du taux BRS) sont des
 * DÉCISIONS PRODUIT internes, ni « confirmées par source externe » ni
 * « non vérifiées fiscalement ». On introduit un statut 'decision_produit'.
 * Une simple string évite d'avoir à ré-altérer un enum à chaque nouveau statut.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('regles_fiscales', function (Blueprint $table) {
            $table->string('statut', 30)->default('non_verifie')->change();
        });
    }

    public function down(): void
    {
        Schema::table('regles_fiscales', function (Blueprint $table) {
            $table->enum('statut', ['confirme', 'non_verifie'])->default('non_verifie')->change();
        });
    }
};
