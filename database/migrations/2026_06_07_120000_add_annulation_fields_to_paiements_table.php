<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Annulation de paiement — colonnes d'audit.
 *
 * PaiementController::annuler() écrit annule_le / annule_par lors de
 * l'annulation d'un paiement valide. Ces colonnes étaient déclarées dans
 * le modèle (fillable, casts, relation annulePar) mais absentes du schéma,
 * provoquant une erreur SQL "Unknown column" (500) à la première annulation.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('paiements', function (Blueprint $table) {
            $table->timestamp('annule_le')->nullable();
            $table->foreignId('annule_par')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('paiements', function (Blueprint $table) {
            $table->dropForeign(['annule_par']);
            $table->dropColumn(['annule_le', 'annule_par']);
        });
    }
};
