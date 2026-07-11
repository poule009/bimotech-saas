<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Module Mon équipe — forçage du changement de mot de passe temporaire.
 *
 * Un collaborateur invité reçoit un mot de passe défini par l'admin ; ce drapeau
 * l'oblige à en choisir un nouveau à sa première connexion (middleware
 * ForcePasswordChange). Il sert aussi d'indicateur « En attente » (invité mais
 * jamais connecté) tant qu'il n'a pas changé son mot de passe.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'must_change_password')) {
                $table->boolean('must_change_password')->default(false)->after('password');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'must_change_password')) {
                $table->dropColumn('must_change_password');
            }
        });
    }
};
