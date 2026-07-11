<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionsSeeder extends Seeder
{
    // Toutes les permissions de la plateforme, groupées par module
    public const PERMISSIONS = [
        'biens'          => ['biens.lire', 'biens.creer', 'biens.modifier', 'biens.supprimer'],
        'contrats'       => ['contrats.lire', 'contrats.creer', 'contrats.modifier', 'contrats.supprimer'],
        'paiements'      => ['paiements.lire', 'paiements.creer', 'paiements.valider', 'paiements.annuler'],
        'locataires'     => ['locataires.lire', 'locataires.creer', 'locataires.modifier'],
        'proprietaires'  => ['proprietaires.lire', 'proprietaires.creer', 'proprietaires.modifier'],
        'immeubles'      => ['immeubles.lire', 'immeubles.creer', 'immeubles.modifier'],
        'impayes'        => ['impayes.lire', 'impayes.relance'],
        'rapports'       => ['rapports.lire'],
        'fiscal'         => ['fiscal.lire', 'fiscal.modifier'],
        'comptabilite'   => ['comptabilite.lire', 'comptabilite.modifier'],
        'logs'           => ['logs.lire'],
        'equipe'         => ['equipe.lire', 'equipe.gerer'],
    ];

    // Rôles prédéfinis avec leurs permissions
    public const ROLES = [
        'gestionnaire' => [
            'biens.lire', 'biens.creer', 'biens.modifier', 'biens.supprimer',
            'contrats.lire', 'contrats.creer', 'contrats.modifier', 'contrats.supprimer',
            'paiements.lire', 'paiements.creer', 'paiements.valider', 'paiements.annuler',
            'locataires.lire', 'locataires.creer', 'locataires.modifier',
            'proprietaires.lire', 'proprietaires.creer', 'proprietaires.modifier',
            'immeubles.lire', 'immeubles.creer', 'immeubles.modifier',
            'impayes.lire', 'impayes.relance',
            'rapports.lire',
            'fiscal.lire',
            'comptabilite.lire',
            'logs.lire',
        ],
        'comptable' => [
            'biens.lire',
            'contrats.lire',
            'paiements.lire', 'paiements.creer', 'paiements.valider', 'paiements.annuler',
            'locataires.lire',
            'proprietaires.lire',
            'immeubles.lire',
            'impayes.lire',
            'rapports.lire',
            'fiscal.lire',
            'comptabilite.lire', 'comptabilite.modifier',
        ],
        'fiscaliste' => [
            'biens.lire',
            'contrats.lire',
            'paiements.lire',
            'locataires.lire',
            'proprietaires.lire',
            'rapports.lire',
            'fiscal.lire', 'fiscal.modifier',
            'comptabilite.lire',
        ],
        'lecture_seule' => [
            'biens.lire',
            'contrats.lire',
            'paiements.lire',
            'locataires.lire',
            'proprietaires.lire',
            'immeubles.lire',
            'impayes.lire',
            'rapports.lire',
            'fiscal.lire',
            'comptabilite.lire',
            'logs.lire',
        ],
    ];

    // Labels FR affichés dans l'interface
    public const ROLE_LABELS = [
        'gestionnaire' => 'Gestionnaire',
        'comptable'    => 'Comptable',
        'fiscaliste'   => 'Fiscaliste',
        'lecture_seule'=> 'Lecture seule',
    ];

    public const MODULE_LABELS = [
        'biens'         => 'Biens',
        'contrats'      => 'Contrats',
        'paiements'     => 'Paiements',
        'locataires'    => 'Locataires',
        'proprietaires' => 'Propriétaires',
        'immeubles'     => 'Immeubles',
        'impayes'       => 'Impayés',
        'rapports'      => 'Rapports',
        'fiscal'        => 'Module fiscal',
        'comptabilite'  => 'Comptabilité',
        'logs'          => 'Journal d\'activité',
    ];

    public const PERMISSION_LABELS = [
        'lire'     => 'Lire',
        'creer'    => 'Créer',
        'modifier' => 'Modifier',
        'supprimer'=> 'Supprimer',
        'valider'  => 'Valider',
        'annuler'  => 'Annuler',
        'relance'  => 'Relancer',
    ];

    public function run(): void
    {
        // Créer toutes les permissions
        $allPerms = collect(self::PERMISSIONS)->flatten();
        foreach ($allPerms as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // Créer les rôles avec leurs permissions
        foreach (self::ROLES as $roleName => $permNames) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $role->syncPermissions($permNames);
        }
    }
}
