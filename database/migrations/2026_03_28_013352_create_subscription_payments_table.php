<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('subscription_id')
                  ->constrained('subscriptions')
                  ->onDelete('cascade');

            $table->foreignId('agency_id')
                  ->constrained('agencies')
                  ->onDelete('cascade');

            // ── Détails du paiement ───────────────────────────────────────
            $table->enum('plan', [
                'mensuel',
                'trimestriel',
                'semestriel',
                'annuel',
            ]);

            $table->decimal('montant', 10, 2);

            $table->enum('statut', [
                'en_attente',
                'payé',
                'échoué',
                'remboursé',
            ])->default('payé');

            // ── Référence paiement ────────────────────────────────────────
            $table->string('reference')->nullable(); // référence PayDunya ou MANUEL
            $table->enum('methode', [
                'paydunya',
                'wave',
                'orange_money',
                'virement',
                'manuel',
            ])->default('manuel');

            // ── Période couverte ──────────────────────────────────────────
            $table->timestamp('periode_debut')->nullable();
            $table->timestamp('periode_fin')->nullable();

            // ── Notes ─────────────────────────────────────────────────────
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_payments');
    }
};