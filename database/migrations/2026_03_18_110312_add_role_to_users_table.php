<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Ajoute la colonne `role` et `agency_id` à la table users.
 *
 * ✅ LACUNE C5 CORRIGÉE — La migration précédente avait un up() vide.
 *
 * Rôles disponibles :
 *   superadmin — accès plateforme global (sans agency_id)
 *   admin      — admin d'une agence
 *   proprietaire, locataire — utilisateurs métier
 *
 * IMPORTANT : Si cette migration a déjà tourné avec le up() vide,
 * vérifiez d'abord que la colonne n'existe PAS avant de la relancer :
 *   php artisan migrate:status
 * Si elle est marquée "Ran", faites :
 *   php artisan migrate:rollback --step=1
 *   php artisan migrate
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            // Role — valeur par défaut 'locataire' pour protéger les comptes existants
            if (! Schema::hasColumn('users', 'role')) {
                $table->enum('role', ['superadmin', 'admin', 'proprietaire', 'locataire'])
                      ->default('locataire')
                      ->after('email');
            }

            // Téléphone et adresse (profil de base)
            if (! Schema::hasColumn('users', 'telephone')) {
                $table->string('telephone', 30)->nullable()->after('role');
            }

            if (! Schema::hasColumn('users', 'adresse')) {
                $table->text('adresse')->nullable()->after('telephone');
            }

            // Soft deletes pour conserver l'historique
            if (! Schema::hasColumn('users', 'deleted_at')) {
                $table->softDeletes()->after('updated_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            foreach (['role', 'telephone', 'adresse', 'deleted_at'] as $col) {
                if (Schema::hasColumn('users', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};