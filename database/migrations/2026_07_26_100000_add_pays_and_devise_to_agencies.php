<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Internationalisation — étape 1 : pays et devise déclarés par agence.
 *
 * - agencies.pays   : code ISO 3166-1 alpha-2 (CHAR 2). Donnée DÉCLARATIVE, choisie
 *                     à l'inscription. Jamais déduite d'une IP, d'un indicatif
 *                     téléphonique ni d'une adresse : une décision qui commande le
 *                     régime fiscal et le contenu des documents ne se devine pas.
 * - agencies.devise : code ISO 4217 (CHAR 3). Colonne DISTINCTE du pays — le pays
 *                     ne fait que la pré-remplir à la création (config/pays.php).
 *
 * ── Pourquoi aucun `default()` au niveau du schéma ─────────────────────────────
 * Un default 'SN' ferait hériter silencieusement du régime fiscal sénégalais à
 * toute agence créée par un chemin qui oublie de renseigner le champ (seeder,
 * script, import). On veut au contraire que l'oubli échoue bruyamment.
 * D'où la séquence en trois temps ci-dessous : colonne nullable → backfill des
 * lignes existantes → passage en NOT NULL sans default.
 *
 * ── Le backfill 'SN' / 'XOF' ───────────────────────────────────────────────────
 * Ce n'est pas une supposition : à la date de cette migration, 100 % des agences
 * en base sont sénégalaises (produit conçu et vendu exclusivement au Sénégal,
 * `ville` par défaut à Dakar, moteur fiscal CGI SN). Le backfill enregistre ce
 * fait, il ne le devine pas.
 *
 * AUCUN COMPORTEMENT NE CHANGE avec cette migration : la donnée est posée, rien
 * ne la lit encore. Le conditionnement fiscal est l'étape 4 du chantier.
 *
 * Gardes idempotentes (hasColumn) — cohérent avec add_vitrine_fields / add_portail_fields.
 */
return new class extends Migration
{
    public function up(): void
    {
        // 1. Création en nullable — indispensable : les lignes existantes n'ont
        //    pas encore de valeur et la colonne n'a volontairement pas de default.
        Schema::table('agencies', function (Blueprint $table) {
            if (! Schema::hasColumn('agencies', 'pays')) {
                $table->char('pays', 2)->nullable()->after('ville');
            }

            if (! Schema::hasColumn('agencies', 'devise')) {
                $table->char('devise', 3)->nullable()->after('pays');
            }
        });

        // 2. Backfill des agences existantes — toutes sénégalaises (cf. docblock).
        DB::table('agencies')->whereNull('pays')->update(['pays' => 'SN']);
        DB::table('agencies')->whereNull('devise')->update(['devise' => 'XOF']);

        // 3. Verrouillage en NOT NULL, toujours sans default : à partir d'ici,
        //    créer une agence sans pays lève une erreur SQL au lieu de produire
        //    silencieusement une agence au régime fiscal indéterminé.
        Schema::table('agencies', function (Blueprint $table) {
            $table->char('pays', 2)->nullable(false)->change();
            $table->char('devise', 3)->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('agencies', function (Blueprint $table) {
            if (Schema::hasColumn('agencies', 'pays')) {
                $table->dropColumn('pays');
            }

            if (Schema::hasColumn('agencies', 'devise')) {
                $table->dropColumn('devise');
            }
        });
    }
};
