<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('agency_id')
                  ->unique() // Une seule subscription active par agence
                  ->constrained('agencies')
                  ->onDelete('cascade');

            // ── Statut global ─────────────────────────────────────────────
            $table->enum('statut', [
                'essai',      // Période d'essai en cours
                'actif',      // Abonnement payé et actif
                'expiré',     // Essai ou abonnement expiré
                'annulé',     // Annulé manuellement
            ])->default('essai');

            // ── Période d'essai ───────────────────────────────────────────
            $table->timestamp('date_debut_essai')->nullable();
            $table->timestamp('date_fin_essai')->nullable();   // +30 jours

            // ── Abonnement payé ───────────────────────────────────────────
            $table->enum('plan', [
                'mensuel',
                'trimestriel',
                'semestriel',
                'annuel',
            ])->nullable();

            $table->decimal('montant_paye', 10, 2)->nullable();
            $table->timestamp('date_debut_abonnement')->nullable();
            $table->timestamp('date_fin_abonnement')->nullable();

            // ── Référence paiement PayDunya (pour plus tard) ──────────────
            $table->string('reference_paydunya')->nullable();

            // ── Rappels envoyés ───────────────────────────────────────────
            $table->boolean('rappel_7j_envoye')->default(false);
            $table->boolean('rappel_1j_envoye')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};