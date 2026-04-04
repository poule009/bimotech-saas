<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Ajoute les champs fiscaux et DGID à la table `contrats`.
 *
 * RÈGLES D'ASSIETTE TVA (Art. 355-359 CGI Sénégal) :
 *  - Bail habitation nu    → loyer_assujetti_tva = false (0% TVA sur loyer)
 *  - Bail commercial       → loyer_assujetti_tva = true  (18% TVA sur loyer HT)
 *  - Location meublée      → loyer_assujetti_tva = true  (activité commerciale)
 *  - Bail saisonnier       → loyer_assujetti_tva = true
 *
 * ENREGISTREMENT DGID (Art. 442 CGI Sénégal) :
 *  - Tout bail doit être enregistré dans les 2 mois suivant la signature
 *  - Droits : 1% loyer annuel (habitation) | 2% loyer annuel (commercial)
 *  - Sanction : nullité opposable aux tiers, amendes fiscales
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contrats', function (Blueprint $table) {

            // ══ BLOC FISCAL TVA LOYER ═════════════════════════════════════════

            // Calculé automatiquement par ContratObserver selon type_bail + meuble
            // Surchargeable manuellement pour cas particuliers
            $table->boolean('loyer_assujetti_tva')
                  ->default(false)
                  ->after('type_bail')
                  ->comment('TVA 18% sur loyer ? Auto selon type_bail+meuble, surchargeable');

            // 0.00 pour habitation, 18.00 pour commercial/meublé/saisonnier
            $table->decimal('taux_tva_loyer', 5, 2)
                  ->default(0.00)
                  ->after('loyer_assujetti_tva')
                  ->comment('Taux TVA loyer en % (0 ou 18). Surchargeable si convention');

            // ══ BLOC BRS ══════════════════════════════════════════════════════

            // Calculé automatiquement par ContratObserver selon locataire.est_entreprise
            $table->boolean('brs_applicable')
                  ->default(false)
                  ->after('taux_tva_loyer')
                  ->comment('Retenue à la source applicable ? Auto depuis locataire.est_entreprise');

            // Taux BRS propre à CE contrat (priorité max dans la cascade)
            // NULL → utilise locataire.taux_brs_override → si null → 15% légal
            $table->decimal('taux_brs_manuel', 5, 2)
                  ->nullable()
                  ->after('brs_applicable')
                  ->comment('Taux BRS spécifique à ce contrat. Null = cascade locataire → légal');

            // ══ BLOC ENREGISTREMENT DGID ══════════════════════════════════════

            // Date réelle d'enregistrement aux Impôts et Domaines
            $table->date('date_enregistrement_dgid')
                  ->nullable()
                  ->after('observations')
                  ->comment('Date d\'enregistrement à la DGID (Art. 442 CGI SN)');

            // Numéro de la quittance fiscale délivrée par la DGID
            $table->string('numero_quittance_dgid', 60)
                  ->nullable()
                  ->after('date_enregistrement_dgid')
                  ->comment('N° quittance droits d\'enregistrement DGID');

            // Montant effectivement payé à la DGID
            $table->decimal('montant_droit_de_bail', 12, 2)
                  ->nullable()
                  ->after('numero_quittance_dgid')
                  ->comment('Droits payés à la DGID : 1% (habitation) ou 2% (commercial) × loyer annuel');

            // Exonération légale (baux publics, diplomatiques, etc.)
            $table->boolean('enregistrement_exonere')
                  ->default(false)
                  ->after('montant_droit_de_bail')
                  ->comment('Exonéré de l\'obligation d\'enregistrement (baux publics, diplomatiques...)');
        });

        // ── Rétro-calcul pour les contrats existants ─────────────────────────
        // Les anciens contrats d'habitation ne sont pas assujettis → default false ✓
        // Les anciens contrats commerciaux auraient dû être assujettis → on les marque
        DB::statement("
            UPDATE contrats
            SET loyer_assujetti_tva = true,
                taux_tva_loyer = 18.00
            WHERE type_bail IN ('commercial', 'mixte', 'saisonnier')
        ");

        // Pour les biens meublés en bail d'habitation → aussi assujettis
        DB::statement("
            UPDATE contrats c
            INNER JOIN biens b ON b.id = c.bien_id
            SET c.loyer_assujetti_tva = true,
                c.taux_tva_loyer = 18.00
            WHERE c.type_bail = 'habitation'
            AND b.meuble = true
            AND c.loyer_assujetti_tva = false
        ");
    }

    public function down(): void
    {
        Schema::table('contrats', function (Blueprint $table) {
            $table->dropColumn([
                'loyer_assujetti_tva',
                'taux_tva_loyer',
                'brs_applicable',
                'taux_brs_manuel',
                'date_enregistrement_dgid',
                'numero_quittance_dgid',
                'montant_droit_de_bail',
                'enregistrement_exonere',
            ]);
        });
    }
};