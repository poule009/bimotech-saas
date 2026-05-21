<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        // ── 1. biens : visible_portail + slug ─────────────────────────────
        // Garde idempotente : les colonnes ont pu être créées partiellement
        // si une exécution précédente a échoué après le Schema::table().
        Schema::table('biens', function (Blueprint $table) {
            if (! Schema::hasColumn('biens', 'visible_portail')) {
                $table->boolean('visible_portail')->default(true)->after('statut');
            }
            if (! Schema::hasColumn('biens', 'slug')) {
                $table->string('slug')->nullable()->unique()->after('visible_portail');
            }
        });

        // ── 2. agencies : whatsapp ─────────────────────────────────────────
        Schema::table('agencies', function (Blueprint $table) {
            if (! Schema::hasColumn('agencies', 'whatsapp')) {
                $table->string('whatsapp', 20)->nullable()->after('telephone');
            }
        });

        // ── 3. Index composite contrats(bien_id, statut) ───────────────────
        // Vérifie l'existence avant création — l'index peut déjà exister.
        $indexExiste = collect(
            DB::select("SHOW INDEX FROM contrats WHERE Key_name = 'contrats_bien_id_statut_index'")
        )->isNotEmpty();

        if (! $indexExiste) {
            Schema::table('contrats', function (Blueprint $table) {
                $table->index(['bien_id', 'statut'], 'contrats_bien_id_statut_index');
            });
        }

        // ── 4. Rattrapage slugs agences (filet de sécurité) ───────────────
        // agencies.slug existe depuis la migration de création (non-nullable).
        // Ce bloc ne touchera rien si toutes les agences ont déjà un slug.
        // slug absent de $fillable → DB::table() directement, pas Eloquent.
        DB::table('agencies')
            ->whereNull('slug')
            ->orderBy('id')
            ->each(function (object $agency) {
                DB::table('agencies')
                    ->where('id', $agency->id)
                    ->update([
                        'slug' => Str::slug($agency->name) . '-' . $agency->id,
                    ]);
            });
    }

    public function down(): void
    {
        Schema::table('contrats', function (Blueprint $table) {
            $table->dropIndex('contrats_bien_id_statut_index');
        });

        Schema::table('agencies', function (Blueprint $table) {
            $table->dropColumn('whatsapp');
        });

        Schema::table('biens', function (Blueprint $table) {
            $table->dropColumn(['visible_portail', 'slug']);
        });
    }
};
