<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('paiements', function (Blueprint $table) {
            // TVA sur la commission agence (18% au Sénégal)
            $table->decimal('tva_commission', 10, 2)->default(0)->after('commission_agence');
            $table->decimal('commission_ttc', 10, 2)->default(0)->after('tva_commission');

            // Gestion des cautions
            $table->decimal('caution_percue', 10, 2)->default(0)->after('commission_ttc');
            $table->boolean('est_premier_paiement')->default(false)->after('caution_percue');

            // Statut du paiement
            $table->enum('statut', ['valide', 'en_attente', 'annule'])->default('valide')->after('est_premier_paiement');
        });
    }

    public function down(): void
    {
        Schema::table('paiements', function (Blueprint $table) {
            $table->dropColumn([
                'tva_commission', 'commission_ttc',
                'caution_percue', 'est_premier_paiement', 'statut'
            ]);
        });
    }
};