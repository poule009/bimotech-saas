<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Historique des modifications d'une règle fiscale (module Super Admin).
 *
 * Chaque enregistrement d'un champ modifié depuis la fiche règle crée une
 * ligne ici — jamais d'écrasement silencieux (brief « Règles fiscales »).
 * L'historique est en LECTURE SEULE côté v1 : pas de restauration d'une valeur
 * antérieure (hors périmètre v1).
 *
 * Une ligne = un champ modifié lors d'un enregistrement (champ, ancienne_valeur,
 * nouvelle_valeur), rattachée à la règle et à l'admin auteur.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('regle_fiscale_historiques', function (Blueprint $table) {
            $table->id();

            $table->foreignId('regle_fiscale_id')
                ->constrained('regles_fiscales')
                ->cascadeOnDelete();

            // Auteur de la modification. Nullable : l'admin peut être supprimé
            // sans faire disparaître la trace (on garde l'historique).
            $table->foreignId('admin_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Nom lisible de l'auteur figé au moment de la modif (survit à la
            // suppression du compte).
            $table->string('admin_nom')->nullable();

            // Clé technique du champ modifié (statut, titre, description, note,
            // date_verification, sources).
            $table->string('champ');

            $table->text('ancienne_valeur')->nullable();
            $table->text('nouvelle_valeur')->nullable();

            $table->timestamps();

            $table->index(['regle_fiscale_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('regle_fiscale_historiques');
    }
};
