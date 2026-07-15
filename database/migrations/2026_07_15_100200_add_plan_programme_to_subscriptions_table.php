<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Downgrade différé : un passage à un plan inférieur ne prend effet qu'au
 * prochain cycle de facturation (pas de remboursement au prorata).
 *
 * Il faut donc mémoriser le plan visé sans toucher au plan courant. La date
 * d'effet n'est pas stockée : c'est toujours `date_fin_abonnement`, seule
 * source de vérité du cycle — la dupliquer créerait deux dates à resynchroniser.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->string('plan_niveau_prochain', 20)->nullable()->after('plan_niveau');
        });
    }

    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn('plan_niveau_prochain');
        });
    }
};
