<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Lots d'import — pilote le flux « upload → aperçu → confirmation → annulation ».
 *
 * Un lot est créé à l'upload avec les lignes déjà parsées et normalisées (colonne
 * `rows`, JSON) et leur verdict individuel. L'aperçu affiche ces lignes ; le commit
 * insère UNIQUEMENT les lignes valides depuis ce JSON (aperçu == ce qui est inséré,
 * déterministe). Le lot reste ensuite en historique et peut être annulé en bloc.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('import_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agency_id')->constrained('agencies')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->enum('type', ['proprietaires', 'biens', 'locataires', 'contrats']);
            $table->enum('statut', ['preview', 'committed', 'annule'])->default('preview');

            $table->string('original_filename')->nullable();

            // Lignes parsées + verdict (valid|duplicate|error) + valeurs résolues.
            $table->json('rows')->nullable();
            // Map des codes générés au commit (pour le fichier de retour).
            $table->json('codes')->nullable();

            // Compteurs figés au commit.
            $table->unsignedInteger('nb_total')->default(0);
            $table->unsignedInteger('nb_valides')->default(0);
            $table->unsignedInteger('nb_erreurs')->default(0);
            $table->unsignedInteger('nb_doublons')->default(0);
            $table->unsignedInteger('nb_crees')->default(0);

            $table->timestamp('committed_at')->nullable();
            $table->timestamp('annule_at')->nullable();

            $table->timestamps();

            $table->index(['agency_id', 'type', 'statut']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('import_batches');
    }
};
