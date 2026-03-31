<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Ajoute les champs manquants à la table biens :
 *  - quartier  : quartier du bien (ex: Plateau, Almadies, Mermoz...)
 *  - commune   : commune administrative (ex: Dakar-Plateau, Guédiawaye...)
 *  - meuble    : bien meublé ou non (booléen)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('biens', function (Blueprint $table) {
            $table->string('quartier', 100)->nullable()->after('ville');
            $table->string('commune', 100)->nullable()->after('quartier');
            $table->boolean('meuble')->default(false)->after('nombre_pieces');
        });
    }

    public function down(): void
    {
        Schema::table('biens', function (Blueprint $table) {
            $table->dropColumn(['quartier', 'commune', 'meuble']);
        });
    }
};
