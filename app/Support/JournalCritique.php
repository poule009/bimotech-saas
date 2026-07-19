<?php

namespace App\Support;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Builder;

/**
 * Vue « Journal d'activité critique » (module Super Admin « Paramètres système »).
 *
 * Ce module NE journalise RIEN lui-même : il LIT la table activity_logs déjà
 * alimentée par les autres modules, filtrée sur un jeu restreint de codes
 * d'action jugés critiques. La sévérité (haute/moyenne/basse) est DÉRIVÉE ici du
 * code d'action — pas de colonne dédiée, donc aucune migration ni backfill, et
 * cohérent avec la nature « lecture seule » du journal.
 *
 * ⚠️ La colonne activity_logs.action est un string(20) : tout nouveau code
 * critique doit tenir en ≤ 20 caractères.
 */
class JournalCritique
{
    /**
     * Codes d'action critiques → sévérité.
     * Alimentés par : SuperAdminController (agences, impersonation, règles),
     * EquipeInterneController (collaborateurs), PlanChangeService (plans),
     * ParametresController (réglages système).
     *
     * @var array<string,string> action => 'haute'|'moyenne'|'basse'
     */
    public const ACTIONS = [
        // Agences
        'agence_suspendue'    => 'haute',
        'agence_reactivee'    => 'moyenne',
        // Équipe interne (accès collaborateurs)
        'equipe_revoque'      => 'haute',
        'equipe_restaure'     => 'moyenne',
        'equipe_reattrib'     => 'moyenne',
        'equipe_invite'       => 'basse',
        // Abonnements / plans
        'downgrade_programme' => 'moyenne',
        'downgrade_annule'    => 'basse',
        'upgrade'             => 'basse',
        'plan_essai_change'   => 'basse',
        // Référentiel fiscal
        'regle_modifiee'      => 'moyenne',
        // Paramètres système eux-mêmes
        'params_modifies'     => 'haute',
    ];

    /** Sévérités ordonnées (du plus grave au moins grave) → libellé. */
    public const SEVERITES = [
        'haute'   => 'Haute',
        'moyenne' => 'Moyenne',
        'basse'   => 'Basse',
    ];

    /** Classes Tailwind de la pastille de sévérité (cohérentes avec la charte SA). */
    public const SEVERITE_PASTILLE = [
        'haute'   => 'bg-error',
        'moyenne' => 'bg-gold',
        'basse'   => 'bg-teal',
    ];

    /** Sévérité d'une entrée (défaut : basse si code inconnu). */
    public static function severite(ActivityLog $log): string
    {
        return self::ACTIONS[$log->action] ?? 'basse';
    }

    /** Codes d'action correspondant à une sévérité donnée. @return array<int,string> */
    public static function actionsPourSeverite(string $severite): array
    {
        return array_keys(array_filter(self::ACTIONS, fn ($s) => $s === $severite));
    }

    /**
     * Requête de base du journal critique : toutes les entrées dont le code
     * d'action figure dans self::ACTIONS, les plus récentes d'abord.
     */
    public static function query(): Builder
    {
        return ActivityLog::query()
            ->whereIn('action', array_keys(self::ACTIONS))
            ->latest();
    }
}
