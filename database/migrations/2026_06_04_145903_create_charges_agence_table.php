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
        Schema::create('charges_agence', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agency_id')->constrained()->onDelete('cascade');
            $table->string('libelle');
            $table->decimal('montant', 12, 2);
            $table->enum('categorie', [
                'salaires', 'loyer_bureau', 'telephone', 'eau_electricite',
                'carburant', 'fournitures', 'publicite', 'assurance', 'autre',
            ]);
            $table->date('date_charge');
            $table->string('periode', 7); // format Y-m ex: 2026-06
            $table->string('prestataire')->nullable();
            $table->string('justificatif_path')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['agency_id', 'periode']);
            $table->index(['agency_id', 'date_charge']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('charges_agence');
    }
};
