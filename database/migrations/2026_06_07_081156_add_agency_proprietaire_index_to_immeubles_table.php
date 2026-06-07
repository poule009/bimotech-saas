<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('immeubles', function (Blueprint $table) {
            // Filtre principal du dashboard : liste des immeubles d'une agence
            // par propriétaire — agency_id en tête (colonne la plus sélective)
            $table->index(['agency_id', 'proprietaire_id'], 'immeubles_agency_proprietaire_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('immeubles', function (Blueprint $table) {
            $table->dropIndex('immeubles_agency_proprietaire_idx');
        });
    }
};
