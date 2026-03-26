<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('locataires', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Identité
            $table->string('cni', 20)->nullable();
            $table->date('date_naissance')->nullable();
            $table->enum('genre', ['homme', 'femme'])->nullable();
            $table->string('nationalite')->default('Sénégalaise');

            // Situation
            $table->string('profession')->nullable();
            $table->string('employeur')->nullable();
            $table->decimal('revenu_mensuel', 10, 2)->nullable();

            // Contact urgence
            $table->string('contact_urgence_nom')->nullable();
            $table->string('contact_urgence_tel', 20)->nullable();
            $table->string('contact_urgence_lien')->nullable(); // père, mère, conjoint...

            // Adresse précédente
            $table->string('adresse_precedente')->nullable();
            $table->string('ville')->default('Dakar');
            $table->string('quartier')->nullable();

            // Documents
            $table->boolean('cni_verified')->default(false);
            $table->boolean('justif_revenus_fourni')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('locataires');
    }
};