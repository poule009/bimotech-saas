<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Vitrine publique par agence (BimoPortail v2) — champs de réglage.
 *
 * - agencies.slogan        : phrase d'accroche affichée dans le hero de la vitrine.
 *                            Vit dans l'écran Paramètres agence existant (réglage one-shot).
 * - biens.est_en_vedette   : le bien apparaît dans la section « Biens en vedette ».
 *                            Simple case à cocher du formulaire bien. Filet de sécurité côté
 *                            vitrine : si aucun bien n'est coché, on affiche les plus récents.
 *
 * Gardes idempotentes (hasColumn) — cohérent avec la migration add_portail_fields.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('agencies', function (Blueprint $table) {
            if (! Schema::hasColumn('agencies', 'slogan')) {
                $table->string('slogan', 160)->nullable()->after('whatsapp');
            }
        });

        Schema::table('biens', function (Blueprint $table) {
            if (! Schema::hasColumn('biens', 'est_en_vedette')) {
                $table->boolean('est_en_vedette')->default(false)->after('visible_portail');
            }
        });
    }

    public function down(): void
    {
        Schema::table('agencies', function (Blueprint $table) {
            $table->dropColumn('slogan');
        });

        Schema::table('biens', function (Blueprint $table) {
            $table->dropColumn('est_en_vedette');
        });
    }
};
