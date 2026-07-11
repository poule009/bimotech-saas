<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Table `regles_fiscales` — traçabilité des règles fiscales et de leurs sources.
 *
 * Chaque règle codée dans le moteur fiscal (FiscalService, TvaAgenceService)
 * est associée ici à : sa description, son statut (confirmé / à vérifier),
 * la ou les sources (libellé + URL) et la date de dernière vérification.
 *
 * Alimentée par ReglesFiscalesSeeder depuis config/fiscal_sources.php.
 * Destinée à alimenter plus tard une page « Sources » côté interface.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('regles_fiscales', function (Blueprint $table) {
            $table->id();

            // Clé stable référencée dans le code (ex: 'tva_loyer_assiette').
            $table->string('cle')->unique();

            // Regroupement fonctionnel (ex: 'tva', plus tard 'brs', 'irpp'...).
            $table->string('categorie')->default('tva')->index();

            $table->string('titre');
            $table->text('description');

            // 'confirme' = vérifié par source externe indépendante
            // 'non_verifie' = plausible mais non confirmé (document interne)
            $table->enum('statut', ['confirme', 'non_verifie'])->default('non_verifie')->index();

            // Liste des sources : [{ libelle, url }, ...] — url nullable.
            $table->json('sources');

            // Réserve / nuance (ex: « raisonnablement solide », mise en garde audit).
            $table->text('note')->nullable();

            // Date de dernière vérification de la règle.
            $table->date('date_verification')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('regles_fiscales');
    }
};
