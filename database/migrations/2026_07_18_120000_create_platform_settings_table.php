<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Réglages globaux de la plateforme (module Super Admin « Paramètres système »).
 *
 * Table clé/valeur volontairement minimale : quelques réglages plateforme
 * (infos support, mode maintenance, options de sécurité). La valeur est stockée
 * en JSON pour rester agnostique du type (bool, int, string). La source de vérité
 * applicative est le service App\Support\PlatformSettings, qui porte les défauts.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('platform_settings', function (Blueprint $table) {
            $table->id();
            $table->string('cle')->unique();
            $table->json('valeur')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('platform_settings');
    }
};
