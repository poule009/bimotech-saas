<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ajoute le NINEA (identifiant fiscal) et le flag d'onboarding terminé
     * à la table agencies.
     */
    public function up(): void
    {
        Schema::table('agencies', function (Blueprint $table) {
            // NINEA : Numéro d'Identification National des Entreprises et Associations (Sénégal)
            $table->string('ninea', 30)->nullable()->after('adresse');

            // Flag persistant : true = checklist d'onboarding masquée définitivement
            $table->boolean('onboarding_completed')->default(false)->after('ninea');
        });
    }

    public function down(): void
    {
        Schema::table('agencies', function (Blueprint $table) {
            $table->dropColumn(['ninea', 'onboarding_completed']);
        });
    }
};
