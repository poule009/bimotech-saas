<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tva_declarations_agence', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agency_id')->constrained('agencies')->cascadeOnDelete();
            $table->unsignedTinyInteger('mois');   // 1-12
            $table->unsignedSmallInteger('annee');

            // TVA collectée (calculée depuis paiements — Art. 369 CGI SN)
            $table->decimal('tva_commissions', 15, 2)->default(0);
            $table->decimal('tva_loyers_commerciaux', 15, 2)->default(0);
            $table->decimal('tva_charges_forfait', 15, 2)->default(0);
            $table->decimal('tva_honoraires', 15, 2)->default(0);
            $table->decimal('total_tva_collectee', 15, 2)->default(0);

            // TVA déductible (saisie manuelle par l'agence)
            $table->decimal('tva_achats_fournitures', 15, 2)->default(0);
            $table->decimal('tva_loyer_bureau', 15, 2)->default(0);
            $table->decimal('tva_autres_deductible', 15, 2)->default(0);
            $table->decimal('total_tva_deductible', 15, 2)->default(0);

            // Résultat (Art. 370 CGI SN)
            $table->decimal('credit_reporte_entrant', 15, 2)->default(0);
            $table->decimal('tva_nette_due', 15, 2)->default(0);
            $table->decimal('credit_reporte_sortant', 15, 2)->default(0);

            // Statut workflow
            $table->enum('statut', ['brouillon', 'validee', 'deposee'])->default('brouillon');
            $table->timestamp('deposee_le')->nullable();
            $table->foreignId('deposee_par')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->unique(['agency_id', 'mois', 'annee']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tva_declarations_agence');
    }
};
