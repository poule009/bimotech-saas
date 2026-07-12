<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Pièce d'identité importée (CNI, passeport ou registre de commerce).
 *
 * Chemin relatif sur le disque « public » (ex. pieces_identite/42/xxxx.pdf).
 * Une seule pièce par profil — l'upload remplace la précédente.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('proprietaires', function (Blueprint $table) {
            if (! Schema::hasColumn('proprietaires', 'piece_identite_path')) {
                $table->string('piece_identite_path')->nullable()->after('quartier');
            }
        });

        Schema::table('locataires', function (Blueprint $table) {
            if (! Schema::hasColumn('locataires', 'piece_identite_path')) {
                $table->string('piece_identite_path')->nullable()->after('quartier');
            }
        });
    }

    public function down(): void
    {
        Schema::table('proprietaires', function (Blueprint $table) {
            if (Schema::hasColumn('proprietaires', 'piece_identite_path')) {
                $table->dropColumn('piece_identite_path');
            }
        });

        Schema::table('locataires', function (Blueprint $table) {
            if (Schema::hasColumn('locataires', 'piece_identite_path')) {
                $table->dropColumn('piece_identite_path');
            }
        });
    }
};
