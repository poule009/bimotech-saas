<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Paiements — colonnes les plus filtrées
        Schema::table('paiements', function (Blueprint $table) {
            $table->index('statut');
            $table->index('periode');
            $table->index('date_paiement');
            $table->index(['contrat_id', 'statut']);
            $table->index(['contrat_id', 'periode']);
        });

        // Contrats
        Schema::table('contrats', function (Blueprint $table) {
            $table->index('statut');
            $table->index(['bien_id', 'statut']);
            $table->index(['locataire_id', 'statut']);
        });

        // Biens
        Schema::table('biens', function (Blueprint $table) {
            $table->index('statut');
            $table->index('proprietaire_id');
        });

        // Users
        Schema::table('users', function (Blueprint $table) {
            $table->index('role');
        });
    }

    public function down(): void
    {
        Schema::table('paiements', function (Blueprint $table) {
            $table->dropIndex(['statut']);
            $table->dropIndex(['periode']);
            $table->dropIndex(['date_paiement']);
            $table->dropIndex(['contrat_id', 'statut']);
            $table->dropIndex(['contrat_id', 'periode']);
        });

        Schema::table('contrats', function (Blueprint $table) {
            $table->dropIndex(['statut']);
            $table->dropIndex(['bien_id', 'statut']);
            $table->dropIndex(['locataire_id', 'statut']);
        });

        Schema::table('biens', function (Blueprint $table) {
            $table->dropIndex(['statut']);
            $table->dropIndex(['proprietaire_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role']);
        });
    }
};