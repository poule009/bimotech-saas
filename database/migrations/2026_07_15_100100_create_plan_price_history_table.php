<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Trace d'audit des modifications de tarif/limite d'un plan.
 *
 * Sert à justifier auprès d'une agence le tarif qui lui a été appliqué :
 * on peut rejouer quel prix était en vigueur à la date de sa souscription.
 * Une ligne par champ modifié, écrite par PlanService::update().
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plan_price_history', function (Blueprint $table) {
            $table->id();

            $table->foreignId('plan_id')->constrained('plans')->cascadeOnDelete();

            // Qui a fait le changement. nullOnDelete : on garde la trace même si
            // le compte superadmin est supprimé plus tard.
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('champ', 30);            // prix_mensuel|prix_annuel|limite_unites|limite_admins
            $table->string('ancienne_valeur', 20)->nullable(); // string : « null » = illimité
            $table->string('nouvelle_valeur', 20)->nullable();

            $table->timestamps();

            $table->index(['plan_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plan_price_history');
    }
};
