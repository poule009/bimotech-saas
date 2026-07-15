<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Table `plans` — SOURCE UNIQUE des tarifs et limites de plan.
 *
 * Remplace les valeurs jusqu'ici figées en dur à trois endroits divergents :
 * Subscription::TARIFS, le SQL de SuperAdminController::subscriptions(), et
 * config/plans.php (nb_unites_max / nb_admins_max / labels).
 *
 * Les valeurs seedées ici reprennent EXACTEMENT les tarifs qui étaient en
 * production (25 000 / 50 000 / 90 000) — cette migration ne change aucun prix.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();

            // Clé métier stable, référencée par subscriptions.plan_niveau.
            $table->string('niveau', 20)->unique(); // starter|pro|agence|legacy
            // Nom interne (back-office) : « Legacy » se nomme Legacy côté Super Admin…
            $table->string('libelle', 50);
            // …mais reste présenté « Pro » à l'agence, qui n'a jamais eu à connaître
            // ce plan technique. Reprend config('plans.labels') à l'identique.
            $table->string('libelle_public', 50);

            $table->unsignedInteger('prix_mensuel')->default(0);
            $table->unsignedInteger('prix_annuel')->default(0);

            // null = illimité (convention déjà en place dans config/plans.php).
            $table->unsignedInteger('limite_unites')->nullable();
            $table->unsignedInteger('limite_admins')->nullable();

            // Legacy : plan figé, non souscriptible, non éditable, exclu du MRR.
            $table->boolean('verrouille')->default(false);
            // Sélectionnable dans le changement de plan / la souscription publique.
            $table->boolean('souscriptible')->default(true);

            $table->unsignedTinyInteger('ordre')->default(0);
            $table->timestamps();
        });

        // Reprise à l'identique de Subscription::TARIFS + config/plans.php.
        DB::table('plans')->insert([
            [
                'niveau' => 'starter', 'libelle' => 'Starter', 'libelle_public' => 'Starter',
                'prix_mensuel' => 25000, 'prix_annuel' => 199000,
                'limite_unites' => 15, 'limite_admins' => 2,
                'verrouille' => false, 'souscriptible' => true, 'ordre' => 1,
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'niveau' => 'pro', 'libelle' => 'Pro', 'libelle_public' => 'Pro',
                'prix_mensuel' => 50000, 'prix_annuel' => 399000,
                'limite_unites' => 50, 'limite_admins' => 5,
                'verrouille' => false, 'souscriptible' => true, 'ordre' => 2,
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'niveau' => 'agence', 'libelle' => 'Agence', 'libelle_public' => 'Agence',
                'prix_mensuel' => 90000, 'prix_annuel' => 699000,
                'limite_unites' => null, 'limite_admins' => null,
                'verrouille' => false, 'souscriptible' => true, 'ordre' => 3,
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                // Legacy n'a jamais eu de tarif (absent de TARIFS) : clients beta
                // historiques, accès équivalent Pro, jamais facturés → hors MRR.
                // Présenté « Pro » à l'agence, comme le faisait config('plans.labels').
                'niveau' => 'legacy', 'libelle' => 'Legacy', 'libelle_public' => 'Pro',
                'prix_mensuel' => 0, 'prix_annuel' => 0,
                'limite_unites' => 50, 'limite_admins' => 5,
                'verrouille' => true, 'souscriptible' => false, 'ordre' => 4,
                'created_at' => now(), 'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
