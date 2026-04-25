<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Modèle de clauses de l'agence (écrit une fois, appliqué à tous les contrats)
        Schema::table('agencies', function (Blueprint $table) {
            $table->longText('modele_contrat')->nullable()->after('signature_path');
        });

        // Clauses particulières par contrat (conditions spécifiques à ce bail)
        Schema::table('contrats', function (Blueprint $table) {
            $table->text('clauses_particulieres')->nullable()->after('observations');
        });
    }

    public function down(): void
    {
        Schema::table('agencies', function (Blueprint $table) {
            $table->dropColumn('modele_contrat');
        });
        Schema::table('contrats', function (Blueprint $table) {
            $table->dropColumn('clauses_particulieres');
        });
    }
};
