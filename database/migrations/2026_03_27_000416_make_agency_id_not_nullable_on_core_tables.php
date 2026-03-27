<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Maintenant que toutes les lignes ont un agency_id,
        // on peut rendre la colonne obligatoire (NOT NULL)

        Schema::table('biens', function (Blueprint $table) {
            $table->foreignId('agency_id')->nullable(false)->change();
        });

        Schema::table('contrats', function (Blueprint $table) {
            $table->foreignId('agency_id')->nullable(false)->change();
        });

        Schema::table('paiements', function (Blueprint $table) {
            $table->foreignId('agency_id')->nullable(false)->change();
        });

        // Pour users : on garde nullable car le superadmin n'a pas d'agence
        // Pas de changement sur la table users
    }

    public function down(): void
    {
        Schema::table('biens', function (Blueprint $table) {
            $table->foreignId('agency_id')->nullable()->change();
        });

        Schema::table('contrats', function (Blueprint $table) {
            $table->foreignId('agency_id')->nullable()->change();
        });

        Schema::table('paiements', function (Blueprint $table) {
            $table->foreignId('agency_id')->nullable()->change();
        });
    }
};