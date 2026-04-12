<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Dépenses de gestion — Niveau 5 du moteur BIMO-tech.
 *
 * Enregistre les frais engagés par l'agence pour le compte du propriétaire
 * (réparations, interventions, factures prestataires tiers).
 *
 * Principe d'isolation locataire/propriétaire :
 *   - Ces dépenses N'affectent JAMAIS montant_encaisse ni montant_net_locataire.
 *   - Elles sont déduites UNIQUEMENT du montant à reverser au bailleur.
 *   - net_final_bailleur = paiement.montant_net_bailleur - SUM(depenses_gestion.montant)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('depenses_gestion', function (Blueprint $table) {
            $table->id();

            // ── Isolation multi-tenant ────────────────────────────────────
            $table->foreignId('agency_id')
                  ->constrained('agencies')
                  ->onDelete('cascade');

            // ── Liens métier ──────────────────────────────────────────────
            // paiement_id : dépense rattachée à un mois précis de gestion
            $table->foreignId('paiement_id')
                  ->constrained('paiements')
                  ->onDelete('cascade');

            // ── Description de la dépense ─────────────────────────────────
            $table->string('libelle');               // Ex: "Facture Plombier Moussa"
            $table->decimal('montant', 10, 2);       // Montant TTC en FCFA

            $table->enum('categorie', [
                'plomberie',
                'electricite',
                'peinture',
                'menuiserie',
                'gardiennage',
                'nettoyage',
                'frais_notaire',
                'autre',
            ])->default('autre');

            $table->date('date_depense');            // Date de la facture / intervention

            // ── Prestataire ───────────────────────────────────────────────
            $table->string('prestataire')->nullable();       // "Moussa Diallo Plomberie"
            $table->string('justificatif_path')->nullable(); // Chemin vers la facture scannée

            // ── Notes libres ──────────────────────────────────────────────
            $table->text('notes')->nullable();

            $table->timestamps();

            // ── Index de performance ──────────────────────────────────────
            $table->index(['paiement_id', 'agency_id'], 'idx_depenses_paiement_agency');
            $table->index(['agency_id', 'date_depense'], 'idx_depenses_agency_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('depenses_gestion');
    }
};
