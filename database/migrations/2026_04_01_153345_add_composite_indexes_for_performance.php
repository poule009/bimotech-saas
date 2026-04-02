<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Complète les index de performance manquants sur toutes les tables.
 *
 * La migration 2026_03_23 posait des index simples (statut, periode...).
 * Cette migration ajoute les index COMPOSITES qui font la vraie différence
 * sur un SaaS multi-agences : chaque requête filtre par agency_id EN PREMIER,
 * puis par statut — l'ordre des colonnes dans l'index composite est crucial.
 *
 * Principe : mettre la colonne la plus sélective (agency_id) en tête.
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── USERS ─────────────────────────────────────────────────────────
        Schema::table('users', function (Blueprint $table) {
            // Toutes les listes de proprietaires/locataires filtrent par agency_id + role
            $table->index(['agency_id', 'role'], 'users_agency_role_idx');

            // Recherche par email (login, vérif unicité)
            // email est déjà UNIQUE — pas besoin d'un index supplémentaire
            // mais on ajoute agency_id + deleted_at pour les soft deletes
            $table->index(['agency_id', 'deleted_at'], 'users_agency_deleted_idx');
        });

        // ── BIENS ─────────────────────────────────────────────────────────
        Schema::table('biens', function (Blueprint $table) {
            // Filtre principal : agence + statut (liste des biens disponibles/loués)
            $table->index(['agency_id', 'statut'], 'biens_agency_statut_idx');

            // Filtre propriétaire + statut (dashboard proprio)
            $table->index(['agency_id', 'proprietaire_id', 'statut'], 'biens_agency_proprio_statut_idx');

            // Recherche par référence (formulaires, recherche rapide)
            $table->index('reference', 'biens_reference_idx');

            // Biens non supprimés (SoftDeletes)
            $table->index(['agency_id', 'deleted_at'], 'biens_agency_deleted_idx');
        });

        // ── CONTRATS ──────────────────────────────────────────────────────
        Schema::table('contrats', function (Blueprint $table) {
            // Requête la plus fréquente : tous les contrats actifs d'une agence
            $table->index(['agency_id', 'statut'], 'contrats_agency_statut_idx');

            // Contrats actifs d'un locataire (dashboard locataire)
            $table->index(['agency_id', 'locataire_id', 'statut'], 'contrats_agency_locataire_statut_idx');

            // Contrats actifs sur un bien (vérif doublon avant création)
            $table->index(['agency_id', 'bien_id', 'statut'], 'contrats_agency_bien_statut_idx');

            // Contrats qui expirent bientôt (alertes renouvellement)
            $table->index(['statut', 'date_fin'], 'contrats_statut_date_fin_idx');

            // Référence bail (recherche, affichage quittance)
            $table->index('reference_bail', 'contrats_reference_bail_idx');
        });

        // ── PAIEMENTS ─────────────────────────────────────────────────────
        Schema::table('paiements', function (Blueprint $table) {
            // Requête de base : paiements valides d'une agence
            $table->index(['agency_id', 'statut'], 'paiements_agency_statut_idx');

            // Rapport financier : paiements valides d'une agence sur une période
            $table->index(['agency_id', 'statut', 'date_paiement'], 'paiements_agency_statut_date_idx');

            // Dashboard locataire : paiements d'un contrat
            $table->index(['agency_id', 'contrat_id', 'statut'], 'paiements_agency_contrat_statut_idx');

            // Référence bail sur les paiements (affichage quittance)
            $table->index('reference_bail', 'paiements_reference_bail_idx');
        });

        // ── PROPRIETAIRES ─────────────────────────────────────────────────
        Schema::table('proprietaires', function (Blueprint $table) {
            // Lookup par user_id (relation Eloquent)
            $table->index('user_id', 'proprietaires_user_id_idx');
        });

        // ── LOCATAIRES ────────────────────────────────────────────────────
        Schema::table('locataires', function (Blueprint $table) {
            $table->index('user_id', 'locataires_user_id_idx');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_agency_role_idx');
            $table->dropIndex('users_agency_deleted_idx');
        });

        Schema::table('biens', function (Blueprint $table) {
            $table->dropIndex('biens_agency_statut_idx');
            $table->dropIndex('biens_agency_proprio_statut_idx');
            $table->dropIndex('biens_reference_idx');
            $table->dropIndex('biens_agency_deleted_idx');
        });

        Schema::table('contrats', function (Blueprint $table) {
            $table->dropIndex('contrats_agency_statut_idx');
            $table->dropIndex('contrats_agency_locataire_statut_idx');
            $table->dropIndex('contrats_agency_bien_statut_idx');
            $table->dropIndex('contrats_statut_date_fin_idx');
            $table->dropIndex('contrats_reference_bail_idx');
        });

        Schema::table('paiements', function (Blueprint $table) {
            $table->dropIndex('paiements_agency_statut_idx');
            $table->dropIndex('paiements_agency_statut_date_idx');
            $table->dropIndex('paiements_agency_contrat_statut_idx');
            $table->dropIndex('paiements_reference_bail_idx');
        });

        Schema::table('proprietaires', function (Blueprint $table) {
            $table->dropIndex('proprietaires_user_id_idx');
        });

        Schema::table('locataires', function (Blueprint $table) {
            $table->dropIndex('locataires_user_id_idx');
        });
    }
};