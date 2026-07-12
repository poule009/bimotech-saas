<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Audit BRS — B1 : suppression du champ mort `proprietaires.est_personne_morale`.
 *
 * Deux colonnes redondantes coexistaient :
 *   - est_personne_morale     (06/05, jamais fillable → jamais renseignée)
 *   - est_personne_morale_is  (10/05, utilisée par le moteur et l'UI)
 * L'état trimestriel lisait l'ancien champ (toujours false). Source unique = _is.
 *
 * Backfill défensif avant drop : si une ligne avait est_personne_morale=true
 * sans est_personne_morale_is, on la reporte pour ne rien perdre.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('proprietaires', 'est_personne_morale')) {
            // Report défensif de l'ancien champ vers la source unique.
            DB::table('proprietaires')
                ->where('est_personne_morale', true)
                ->update(['est_personne_morale_is' => true]);

            Schema::table('proprietaires', function (Blueprint $table) {
                $table->dropColumn('est_personne_morale');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('proprietaires', 'est_personne_morale')) {
            Schema::table('proprietaires', function (Blueprint $table) {
                $table->boolean('est_personne_morale')->default(false)->after('ninea');
            });
        }
    }
};
