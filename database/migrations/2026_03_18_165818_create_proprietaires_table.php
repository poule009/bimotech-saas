<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proprietaires', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Identité
            $table->string('cni', 20)->nullable();
            $table->date('date_naissance')->nullable();
            $table->enum('genre', ['homme', 'femme'])->nullable();
            $table->string('nationalite')->default('Sénégalaise');

            // Contact
            $table->string('telephone_secondaire', 20)->nullable();
            $table->string('adresse_domicile')->nullable();
            $table->string('ville')->default('Dakar');
            $table->string('quartier')->nullable();

            // Infos paiement
            $table->enum('mode_paiement_prefere', [
                'especes', 'virement', 'mobile_money', 'cheque'
            ])->default('virement');
            $table->string('banque')->nullable();
            $table->string('numero_compte')->nullable();
            $table->string('numero_wave')->nullable();   // Wave Sénégal
            $table->string('numero_om')->nullable();     // Orange Money

            // Fiscal
            $table->string('ninea', 20)->nullable();
            $table->boolean('assujetti_tva')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proprietaires');
    }
};