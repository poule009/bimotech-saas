<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Module Super Admin « Équipe interne ».
 *
 * Trois briques :
 *  1. agencies.amenee_par — le collaborateur (compte super-admin) qui a apporté
 *     l'agence. Champ INTERNE au back-office, jamais exposé à l'inscription
 *     publique ni à l'agence. Nullable = « Non attribué ».
 *  2. Colonnes super-admin sur users — distinguent l'admin PRINCIPAL (accès total)
 *     des COLLABORATEURS à accès restreint, portent leurs permissions (4 toggles),
 *     leur taux de commission (configurable par collaborateur) et la révocation
 *     d'accès (qui coupe la connexion SANS détacher les agences).
 *  3. commission_snapshots — historique mensuel des commissions, en lecture seule
 *     (jamais de modification rétroactive), même pattern que mrr_snapshots.
 */
return new class extends Migration
{
    public function up(): void
    {
        // 1. Périmètre d'agences ────────────────────────────────────────────
        Schema::table('agencies', function (Blueprint $table) {
            if (! Schema::hasColumn('agencies', 'amenee_par')) {
                // nullOnDelete : si le compte collaborateur est supprimé, l'agence
                // repasse « Non attribué » plutôt que de laisser une FK orpheline.
                $table->foreignId('amenee_par')
                    ->nullable()
                    ->after('actif')
                    ->constrained('users')
                    ->nullOnDelete();
            }
        });

        // 2. Colonnes super-admin sur users ─────────────────────────────────
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'sa_est_principal')) {
                $table->boolean('sa_est_principal')->default(false)->after('is_owner');
            }
            if (! Schema::hasColumn('users', 'sa_taux_commission')) {
                // Pourcentage (ex. 35.00). Configurable par collaborateur — pas une
                // constante globale (un futur accord pourrait différer).
                $table->decimal('sa_taux_commission', 5, 2)->nullable()->after('sa_est_principal');
            }
            if (! Schema::hasColumn('users', 'sa_permissions')) {
                // Les 4 toggles d'un collaborateur restreint (voir User::SA_PERMISSIONS).
                $table->json('sa_permissions')->nullable()->after('sa_taux_commission');
            }
            if (! Schema::hasColumn('users', 'sa_acces_revoque_at')) {
                // Révocation d'accès : coupe la connexion super-admin, SANS toucher
                // amenee_par (l'historique de commission reste attaché).
                $table->timestamp('sa_acces_revoque_at')->nullable()->after('sa_permissions');
            }
        });

        // Backfill : les comptes super-admin existants deviennent « principal »
        // (accès total implicite). Les collaborateurs seront créés en principal=false.
        DB::table('users')->where('role', 'superadmin')->update(['sa_est_principal' => true]);

        // 3. Historique mensuel des commissions ─────────────────────────────
        if (! Schema::hasTable('commission_snapshots')) {
            Schema::create('commission_snapshots', function (Blueprint $table) {
                $table->id();
                $table->foreignId('collaborateur_id')->constrained('users')->cascadeOnDelete();
                $table->date('mois'); // 1er du mois capturé
                $table->unsignedInteger('nb_agences');
                $table->unsignedBigInteger('mrr_total');   // FCFA
                $table->decimal('taux', 5, 2);             // taux figé du mois
                $table->unsignedBigInteger('commission');  // FCFA, figé à la capture
                $table->timestamps();

                // Un seul point d'historique par collaborateur et par mois.
                $table->unique(['collaborateur_id', 'mois']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('commission_snapshots');

        Schema::table('users', function (Blueprint $table) {
            foreach (['sa_est_principal', 'sa_taux_commission', 'sa_permissions', 'sa_acces_revoque_at'] as $col) {
                if (Schema::hasColumn('users', $col)) {
                    $table->dropColumn($col);
                }
            }
        });

        Schema::table('agencies', function (Blueprint $table) {
            if (Schema::hasColumn('agencies', 'amenee_par')) {
                $table->dropForeign(['amenee_par']);
                $table->dropColumn('amenee_par');
            }
        });
    }
};
