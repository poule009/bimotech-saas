<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Historique mensuel du MRR (revenu récurrent mensuel) de la plateforme.
     *
     * Une ligne par mois (clé `mois` = 1er du mois). Alimentée quotidiennement
     * par la commande `mrr:snapshot` : le mois courant se rafraîchit chaque jour,
     * les mois passés restent figés sur leur dernière valeur (≈ fin de mois).
     * Sert à tracer la VRAIE courbe MRR du dashboard Super Admin, là où la
     * reconstruction à partir des abonnements actuels serait approximative.
     */
    public function up(): void
    {
        Schema::create('mrr_snapshots', function (Blueprint $table) {
            $table->id();
            $table->date('mois')->unique();                     // 1er du mois représenté
            $table->unsignedInteger('mrr');                     // MRR équivalent mensuel (FCFA)
            $table->unsignedInteger('agences_actives')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mrr_snapshots');
    }
};
