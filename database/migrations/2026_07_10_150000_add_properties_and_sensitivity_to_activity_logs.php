<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Journal d'activité — deux ajouts pour la valeur de preuve :
 *
 *  - properties  : capture l'avant/après des champs modifiés (diff lisible)
 *                  { champ: { label, old, new } }.
 *  - is_sensitive: fige, AU MOMENT DES FAITS, le caractère sensible d'une action
 *                  (suppression, loyer d'un contrat actif, quittance payée modifiée).
 *                  Stocké et non recalculé → immuable, cohérent avec le rôle de preuve.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            if (! Schema::hasColumn('activity_logs', 'properties')) {
                $table->json('properties')->nullable()->after('description');
            }
            if (! Schema::hasColumn('activity_logs', 'is_sensitive')) {
                $table->boolean('is_sensitive')->default(false)->after('action');
                $table->index(['agency_id', 'is_sensitive', 'created_at']);
            }
        });
    }

    public function down(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            if (Schema::hasColumn('activity_logs', 'is_sensitive')) {
                $table->dropIndex(['agency_id', 'is_sensitive', 'created_at']);
                $table->dropColumn('is_sensitive');
            }
            if (Schema::hasColumn('activity_logs', 'properties')) {
                $table->dropColumn('properties');
            }
        });
    }
};
