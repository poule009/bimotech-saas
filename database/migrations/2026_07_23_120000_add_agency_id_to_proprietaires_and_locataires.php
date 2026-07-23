<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Dénormalise agency_id sur proprietaires et locataires.
 *
 * Avant : l'isolation multi-tenant passait par une sous-requête sur users
 * (whereHas('user')). Après : une colonne agency_id locale indexée, filtrée
 * directement — comme tous les autres modèles tenant (HasAgencyScope).
 *
 * L'agence d'un profil = celle de son user (un user n'appartient qu'à une agence
 * et n'en change jamais). Le backfill la recopie ; un hook `creating` sur les
 * modèles la maintient pour les nouveaux profils.
 *
 * Colonne laissée NULLABLE (pas de contrainte NOT NULL) : robustesse migration,
 * et le scope est fail-closed (un agency_id nul ne matche aucune agence).
 */
return new class extends Migration
{
    public function up(): void
    {
        foreach (['proprietaires', 'locataires'] as $table) {
            if (! Schema::hasColumn($table, 'agency_id')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->unsignedBigInteger('agency_id')->nullable()->after('user_id');
                    $t->index('agency_id');
                });
            }

            // Backfill depuis l'user (sous-requête corrélée — portable MySQL/SQLite).
            DB::statement(
                "update {$table} set agency_id = "
                . "(select agency_id from users where users.id = {$table}.user_id) "
                . "where agency_id is null"
            );
        }
    }

    public function down(): void
    {
        foreach (['proprietaires', 'locataires'] as $table) {
            if (Schema::hasColumn($table, 'agency_id')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->dropIndex(['agency_id']); // nom dérivé : {table}_agency_id_index
                    $t->dropColumn('agency_id');
                });
            }
        }
    }
};
