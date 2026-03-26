<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
      Schema::create('paiements', function (Blueprint $table) {
    $table->id();
    $table->foreignId('contrat_id')->constrained('contrats')->onDelete('restrict');
    $table->date('periode'); // mois concerné: 2025-01-01
    $table->decimal('montant_encaisse', 10, 2);
    $table->enum('mode_paiement', ['especes', 'virement', 'mobile_money', 'cheque']);
    $table->decimal('taux_commission_applique', 5, 2); // snapshot du taux du jour
    $table->decimal('commission_agence', 10, 2); // calculé automatiquement
    $table->decimal('net_proprietaire', 10, 2);  // montant_encaisse - commission
    $table->date('date_paiement');
    $table->string('reference_paiement')->nullable(); // numéro de reçu
    $table->text('notes')->nullable();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paiements');
    }
};
