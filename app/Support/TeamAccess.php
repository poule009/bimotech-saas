<?php

namespace App\Support;

use App\Models\User;

/**
 * Traduction entre le modèle « 3 niveaux par module » de l'UI Mon équipe
 * (Aucun / Consulter / Modifier) et les permissions fines Spatie qui appliquent
 * réellement les droits dans l'app (biens.lire, comptabilite.modifier…).
 *
 * Les 7 modules affichés (fidèles à la maquette) replient en coulisse les groupes
 * secondaires (immeubles, impayés, fiscal, rapports, logs) sous leur module parent
 * le plus proche — invisibles pour l'utilisateur.
 */
class TeamAccess
{
    /**
     * Modules affichés dans la matrice, dans l'ordre. Pour chacun :
     *  - view : permissions accordées au niveau « Consulter »
     *  - full : permissions accordées au niveau « Modifier » (inclut view)
     */
    public const MODULES = [
        'proprietaires' => [
            'label' => 'Propriétaires', 'icon' => 'users', 'sensitive' => false,
            'view'  => ['proprietaires.lire'],
            'full'  => ['proprietaires.lire', 'proprietaires.creer', 'proprietaires.modifier'],
        ],
        'biens' => [
            'label' => 'Biens', 'icon' => 'home', 'sensitive' => false,
            'view'  => ['biens.lire', 'immeubles.lire'],
            'full'  => ['biens.lire', 'biens.creer', 'biens.modifier', 'biens.supprimer',
                        'immeubles.lire', 'immeubles.creer', 'immeubles.modifier'],
        ],
        'locataires' => [
            'label' => 'Locataires', 'icon' => 'user', 'sensitive' => false,
            'view'  => ['locataires.lire'],
            'full'  => ['locataires.lire', 'locataires.creer', 'locataires.modifier'],
        ],
        'contrats' => [
            'label' => 'Contrats', 'icon' => 'file-text', 'sensitive' => false,
            'view'  => ['contrats.lire'],
            'full'  => ['contrats.lire', 'contrats.creer', 'contrats.modifier', 'contrats.supprimer'],
        ],
        'quittances' => [
            'label' => 'Quittances', 'icon' => 'receipt', 'sensitive' => false,
            'view'  => ['paiements.lire', 'impayes.lire'],
            'full'  => ['paiements.lire', 'paiements.creer', 'paiements.valider', 'paiements.annuler',
                        'impayes.lire', 'impayes.relance'],
        ],
        'comptabilite' => [
            'label' => 'Comptabilité', 'icon' => 'wallet', 'sensitive' => true,
            'warn'  => "Accès à l'argent des propriétaires — fermé par défaut",
            'view'  => ['comptabilite.lire', 'fiscal.lire', 'rapports.lire', 'logs.lire'],
            'full'  => ['comptabilite.lire', 'comptabilite.modifier', 'fiscal.lire', 'fiscal.modifier',
                        'rapports.lire', 'logs.lire'],
        ],
        'equipe' => [
            'label' => 'Mon équipe', 'icon' => 'users', 'sensitive' => false,
            'view'  => ['equipe.lire'],
            'full'  => ['equipe.lire', 'equipe.gerer'],
        ],
    ];

    /** Presets du brief → niveau par module. */
    public const PRESETS = [
        'administrateur' => [
            'proprietaires' => 'full', 'biens' => 'full', 'locataires' => 'full',
            'contrats' => 'full', 'quittances' => 'full', 'comptabilite' => 'full', 'equipe' => 'full',
        ],
        'secretaire' => [
            'proprietaires' => 'full', 'biens' => 'full', 'locataires' => 'full',
            'contrats' => 'view', 'quittances' => 'full', 'comptabilite' => 'none', 'equipe' => 'none',
        ],
        'personnalise' => [
            'proprietaires' => 'none', 'biens' => 'none', 'locataires' => 'none',
            'contrats' => 'none', 'quittances' => 'none', 'comptabilite' => 'none', 'equipe' => 'none',
        ],
    ];

    public const PRESET_LABELS = [
        'administrateur' => 'Administrateur',
        'secretaire'     => 'Secrétaire',
        'personnalise'   => 'Personnalisé',
    ];

    public const NIVEAUX = ['none' => 'Aucun', 'view' => 'Consulter', 'full' => 'Modifier'];

    /** Toutes les permissions gérées par la matrice (univers à synchroniser). */
    public static function toutesPermissions(): array
    {
        $all = [];
        foreach (self::MODULES as $mod) {
            $all = array_merge($all, $mod['full']);
        }
        return array_values(array_unique($all));
    }

    /** [module => 'none'|'view'|'full'] → liste plate de permissions. */
    public static function expand(array $levels): array
    {
        $perms = [];
        foreach ($levels as $module => $level) {
            if (! isset(self::MODULES[$module])) continue;
            if ($level === 'full') {
                $perms = array_merge($perms, self::MODULES[$module]['full']);
            } elseif ($level === 'view') {
                $perms = array_merge($perms, self::MODULES[$module]['view']);
            }
        }
        return array_values(array_unique($perms));
    }

    /** Niveaux d'un preset (copie défensive). */
    public static function presetLevels(string $preset): array
    {
        return self::PRESETS[$preset] ?? self::PRESETS['personnalise'];
    }

    /** Niveau effectif d'un module pour un utilisateur, d'après ses permissions. */
    public static function niveauModule(array $userPerms, string $module): string
    {
        $conf = self::MODULES[$module] ?? null;
        if (! $conf) return 'none';

        $set = array_flip($userPerms);
        $hasAll = fn (array $needed) => ! array_diff($needed, array_keys($set));

        if ($hasAll($conf['full']))  return 'full';
        if ($hasAll($conf['view']))  return 'view';
        return 'none';
    }

    /** [module => niveau] pour un utilisateur. */
    public static function niveauxUtilisateur(User $user): array
    {
        $userPerms = $user->getAllPermissions()->pluck('name')->all();
        $levels = [];
        foreach (array_keys(self::MODULES) as $module) {
            $levels[$module] = self::niveauModule($userPerms, $module);
        }
        return $levels;
    }

    /** Détermine quel preset correspond aux niveaux donnés, sinon 'personnalise'. */
    public static function detecterPreset(array $levels): string
    {
        foreach (['administrateur', 'secretaire'] as $preset) {
            if (self::PRESETS[$preset] === $levels) {
                return $preset;
            }
        }
        return 'personnalise';
    }

    /** Normalise une entrée de formulaire [module=>niveau] sur les modules connus. */
    public static function normaliserNiveaux(array $input): array
    {
        $levels = [];
        foreach (array_keys(self::MODULES) as $module) {
            $niveau = $input[$module] ?? 'none';
            $levels[$module] = in_array($niveau, ['none', 'view', 'full'], true) ? $niveau : 'none';
        }
        return $levels;
    }
}
