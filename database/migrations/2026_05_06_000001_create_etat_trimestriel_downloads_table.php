<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Champ requis par l'Art. 200 §5 CGI SN : distinguer bailleurs personnes physiques
        // des personnes morales (IS) qui sont exclues de l'état trimestriel
        Schema::table('proprietaires', function (Blueprint $table) {
            $table->boolean('est_personne_morale')->default(false)->after('ninea');
        });

        Schema::create('etat_trimestriel_downloads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agency_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('trimestre'); // 1 à 4
            $table->unsignedSmallInteger('annee');
            $table->string('type', 10)->default('pdf'); // pdf | csv
            $table->timestamp('downloaded_at');
            $table->foreignId('downloaded_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['agency_id', 'annee', 'trimestre']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('etat_trimestriel_downloads');

        Schema::table('proprietaires', function (Blueprint $table) {
            $table->dropColumn('est_personne_morale');
        });
    }
};
