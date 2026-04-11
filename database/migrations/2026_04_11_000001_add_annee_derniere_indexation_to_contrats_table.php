<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Ajoute annee_derniere_indexation sur la table contrats.
 *
 * Utilisé par la commande loyers:indexation pour savoir si un contrat
 * a déjà été revalorisé cette année et éviter une double indexation.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contrats', function (Blueprint $table) {
            $table->unsignedSmallInteger('annee_derniere_indexation')
                  ->nullable()
                  ->default(null)
                  ->after('indexation_annuelle')
                  ->comment('Dernière année où le loyer a été revalorisé via loyers:indexation');
        });
    }

    public function down(): void
    {
        Schema::table('contrats', function (Blueprint $table) {
            $table->dropColumn('annee_derniere_indexation');
        });
    }
};
