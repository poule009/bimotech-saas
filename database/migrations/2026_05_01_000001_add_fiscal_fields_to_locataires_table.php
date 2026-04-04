<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Ajoute les champs fiscaux à la table `locataires`.
 *
 * RÈGLE DE SÉCURITÉ : toutes les colonnes sont nullable ou ont un default.
 * Aucune colonne NOT NULL sans valeur par défaut → zéro risque sur les données existantes.
 *
 * CAS D'USAGE :
 *  - est_entreprise = true  → BRS 15% automatique sur les paiements
 *  - taux_brs_override      → taux BRS personnalisé (conventions fiscales, 5%, 10%...)
 *  - ninea_locataire        → identifie l'entreprise locataire pour la quittance
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('locataires', function (Blueprint $table) {

            // ── Type juridique du locataire ───────────────────────────────────
            // Détermine si la BRS (Retenue à la Source) s'applique.
            // Règle : personne morale/entreprise → BRS 15% obligatoire (Art. 196bis CGI SN)
            $table->boolean('est_entreprise')
                  ->default(false)
                  ->after('contact_urgence_lien')
                  ->comment('Personne morale ? → BRS 15% sur loyer (Art. 196bis CGI SN)');

            // Libellé complet du type pour affichage et audit
            $table->string('type_locataire', 30)
                  ->default('particulier')
                  ->after('est_entreprise')
                  ->comment('particulier | entreprise | association | ambassade | ong');

            // ── Identification fiscale de l'entreprise locataire ──────────────
            $table->string('ninea_locataire', 30)
                  ->nullable()
                  ->after('type_locataire')
                  ->comment('NINEA de l\'entreprise locataire — apparaît sur la quittance');

            $table->string('rccm_locataire', 60)
                  ->nullable()
                  ->after('ninea_locataire')
                  ->comment('Registre de commerce si entreprise');

            $table->string('nom_entreprise', 150)
                  ->nullable()
                  ->after('rccm_locataire')
                  ->comment('Raison sociale si personne morale (distinct du nom du représentant)');

            // ── Taux BRS personnalisé ─────────────────────────────────────────
            // NULL = on applique le taux légal (15% si entreprise, 0% si particulier)
            // Valeur saisie = override pour ce locataire sur tous ses contrats
            // Le contrat peut encore overrider avec taux_brs_manuel (priorité supérieure)
            $table->decimal('taux_brs_override', 5, 2)
                  ->nullable()
                  ->after('nom_entreprise')
                  ->comment('Taux BRS spécial (null = 15% légal si entreprise). Ex: 5% convention');
        });
    }

    public function down(): void
    {
        Schema::table('locataires', function (Blueprint $table) {
            $table->dropColumn([
                'est_entreprise',
                'type_locataire',
                'ninea_locataire',
                'rccm_locataire',
                'nom_entreprise',
                'taux_brs_override',
            ]);
        });
    }
};