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
        Schema::table('proprietaires', function (Blueprint $table) {
            $table->boolean('est_personne_morale_is')
                  ->default(false)
                  ->after('assujetti_tva')
                  ->comment('Bailleur personne morale IS (SCI, SARL...) → BRS non applicable — Art. 201 §2 CGI SN');

            $table->string('forme_juridique_bailleur', 30)
                  ->nullable()
                  ->after('est_personne_morale_is')
                  ->comment('sci | sarl | sa | ong | autre — affiché sur la fiche bailleur');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proprietaires', function (Blueprint $table) {
            $table->dropColumn(['est_personne_morale_is', 'forme_juridique_bailleur']);
        });
    }
};
