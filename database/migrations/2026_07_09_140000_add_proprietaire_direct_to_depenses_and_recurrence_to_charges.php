<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Module Comptabilité — deux ajustements de schéma :
 *
 * 1. depenses_gestion : permettre une dépense « directement au propriétaire »
 *    (sans bien précis). paiement_id devient nullable, proprietaire_id est ajouté.
 *    L'invariant d'isolation est préservé : ces dépenses ne touchent JAMAIS
 *    montant_encaisse — elles ne réduisent que le montant à reverser au bailleur.
 *
 * 2. charges_agence : flag `recurrente` pour distinguer les charges fixes
 *    (loyer bureau, salaires…) des dépenses occasionnelles.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('depenses_gestion', function (Blueprint $table) {
            $table->dropForeign(['paiement_id']);
        });

        Schema::table('depenses_gestion', function (Blueprint $table) {
            // paiement_id : nullable désormais (dépense directe = pas de paiement)
            $table->unsignedBigInteger('paiement_id')->nullable()->change();
            $table->foreign('paiement_id')->references('id')->on('paiements')->onDelete('cascade');

            // Propriétaire visé quand la dépense n'est liée à aucun bien/mois
            if (! Schema::hasColumn('depenses_gestion', 'proprietaire_id')) {
                $table->foreignId('proprietaire_id')->nullable()->after('paiement_id')
                      ->constrained('users')->nullOnDelete();
            }
        });

        Schema::table('charges_agence', function (Blueprint $table) {
            if (! Schema::hasColumn('charges_agence', 'recurrente')) {
                $table->boolean('recurrente')->default(false)->after('categorie');
            }
        });
    }

    public function down(): void
    {
        Schema::table('depenses_gestion', function (Blueprint $table) {
            if (Schema::hasColumn('depenses_gestion', 'proprietaire_id')) {
                $table->dropForeign(['proprietaire_id']);
                $table->dropColumn('proprietaire_id');
            }
        });

        Schema::table('charges_agence', function (Blueprint $table) {
            if (Schema::hasColumn('charges_agence', 'recurrente')) {
                $table->dropColumn('recurrente');
            }
        });
    }
};
