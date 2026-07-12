<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Audit BRS — B3 : dispense explicite de retenue à la source, AU NIVEAU DU PROPRIÉTAIRE.
 *
 * Un bailleur personne physique dont le loyer atteint le seuil (≥ 150 000 F) est
 * normalement soumis à la BRS. S'il justifie d'une dispense (attestation DGID),
 * l'agence ne doit pas retenir. Le statut appartient à la PERSONNE (comme
 * assujetti_tva), pas au bien : un propriétaire à 5 biens ne coche qu'une fois.
 *
 * Décision produit interne (pas une exigence légale de forme) : défaut false
 * (retenue par défaut, cf. règle confirmée) ; l'agence active la dispense au vu
 * du justificatif.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('proprietaires', function (Blueprint $table) {
            $table->boolean('brs_dispense')->default(false)->after('est_personne_morale_is');
        });
    }

    public function down(): void
    {
        Schema::table('proprietaires', function (Blueprint $table) {
            $table->dropColumn('brs_dispense');
        });
    }
};
