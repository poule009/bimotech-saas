<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Module Import de données — support des codes de liaison et du suivi par lot.
 *
 * 1. code_import (P-0001 / B-0001 / L-0001) : identifiant de liaison SÉQUENTIEL
 *    par agence, généré au commit d'un import. Persistant (l'agence télécharge ses
 *    codes, quitte l'app, remplit le fichier suivant hors-ligne, revient l'importer).
 *    Unicité par agence garantie à la génération (requête verrouillée scopée agence)
 *    plutôt que par contrainte DB : proprietaires/locataires n'ont pas d'agency_id
 *    (ils sont scopés via users.agency_id). Index simple pour les recherches.
 *
 * 2. import_batch_id : rattache chaque enregistrement au lot qui l'a créé, pour
 *    permettre l'annulation en bloc d'un import (tant qu'aucun dépendant n'existe).
 */
return new class extends Migration
{
    public function up(): void
    {
        // code_import + batch sur les 3 entités portant un code
        foreach (['proprietaires', 'biens', 'locataires'] as $t) {
            Schema::table($t, function (Blueprint $table) use ($t) {
                if (! Schema::hasColumn($t, 'code_import')) {
                    $table->string('code_import', 12)->nullable()->index();
                }
                if (! Schema::hasColumn($t, 'import_batch_id')) {
                    $table->foreignId('import_batch_id')->nullable()
                          ->constrained('import_batches')->nullOnDelete();
                }
            });
        }

        // Les contrats ne portent pas de code (aucune étape ne les référence),
        // mais gardent le rattachement au lot pour l'annulation.
        Schema::table('contrats', function (Blueprint $table) {
            if (! Schema::hasColumn('contrats', 'import_batch_id')) {
                $table->foreignId('import_batch_id')->nullable()
                      ->constrained('import_batches')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        foreach (['proprietaires', 'biens', 'locataires'] as $t) {
            Schema::table($t, function (Blueprint $table) use ($t) {
                if (Schema::hasColumn($t, 'import_batch_id')) {
                    $table->dropForeign([$t . '_import_batch_id_foreign']);
                    $table->dropColumn('import_batch_id');
                }
                if (Schema::hasColumn($t, 'code_import')) {
                    $table->dropColumn('code_import');
                }
            });
        }

        Schema::table('contrats', function (Blueprint $table) {
            if (Schema::hasColumn('contrats', 'import_batch_id')) {
                $table->dropForeign(['contrats_import_batch_id_foreign']);
                $table->dropColumn('import_batch_id');
            }
        });
    }
};
