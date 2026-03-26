<?php

namespace Database\Seeders;

use App\Models\Bien;
use App\Models\Contrat;
use App\Models\Locataire;
use App\Models\Paiement;
use App\Models\Proprietaire;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. ADMIN ──────────────────────────────────────────────────────────
        $admin = User::updateOrCreate(
            ['email' => 'admin@bimotech.sn'],
            [
                'name'      => 'Administrateur BIMO',
                'password'  => Hash::make('Admin@2025!'),
                'role'      => 'admin',
                'telephone' => '+221 33 800 00 01',
                'adresse'   => 'Plateau, Dakar',
            ]
        );

        // ── 2. PROPRIÉTAIRES ──────────────────────────────────────────────────
        $proprietairesData = [
            [
                'user' => [
                    'name'      => 'Moussa Diallo',
                    'email'     => 'moussa.diallo@bimotech.sn',
                    'password'  => Hash::make('Proprio@2025!'),
                    'role'      => 'proprietaire',
                    'telephone' => '+221 77 100 20 30',
                    'adresse'   => 'Almadies, Dakar',
                ],
                'profil' => [
                    'cni'                   => 'SN-1234567',
                    'date_naissance'        => '1975-06-15',
                    'genre'                 => 'homme',
                    'quartier'              => 'Almadies',
                    'ville'                 => 'Dakar',
                    'mode_paiement_prefere' => 'virement',
                    'banque'                => 'CBAO',
                    'numero_wave'           => '+221 77 100 20 30',
                ],
            ],
            [
                'user' => [
                    'name'      => 'Aïssatou Ndiaye',
                    'email'     => 'aissatou.ndiaye@bimotech.sn',
                    'password'  => Hash::make('Proprio@2025!'),
                    'role'      => 'proprietaire',
                    'telephone' => '+221 76 200 30 40',
                    'adresse'   => 'Mermoz, Dakar',
                ],
                'profil' => [
                    'cni'                   => 'SN-2345678',
                    'date_naissance'        => '1968-03-22',
                    'genre'                 => 'femme',
                    'quartier'              => 'Mermoz',
                    'ville'                 => 'Dakar',
                    'mode_paiement_prefere' => 'mobile_money',
                    'numero_om'             => '+221 76 200 30 40',
                ],
            ],
        ];

        $proprietaires = [];
        foreach ($proprietairesData as $data) {
            $user = User::updateOrCreate(['email' => $data['user']['email']], $data['user']);
            $profil = Proprietaire::updateOrCreate(
                ['user_id' => $user->id],
                array_merge($data['profil'], ['user_id' => $user->id])
            );
            $proprietaires[] = ['user' => $user, 'profil' => $profil];
        }

        // ── 3. LOCATAIRES ─────────────────────────────────────────────────────
        $locatairesData = [
            [
                'user' => [
                    'name' => 'Fatou Sow', 'email' => 'fatou.sow@bimotech.sn',
                    'password' => Hash::make('Locataire@2025!'), 'role' => 'locataire',
                    'telephone' => '+221 77 300 10 20', 'adresse' => 'Médina, Dakar',
                ],
                'profil' => [
                    'cni' => 'SN-3456789', 'profession' => 'Enseignante',
                    'employeur' => 'Ministère de l\'Éducation', 'revenu_mensuel' => 350000,
                    'quartier' => 'Médina', 'ville' => 'Dakar',
                    'contact_urgence_nom' => 'Ibrahima Sow',
                    'contact_urgence_tel' => '+221 77 400 10 20',
                    'contact_urgence_lien' => 'père', 'cni_verified' => true,
                    'justif_revenus_fourni' => true,
                ],
            ],
            [
                'user' => [
                    'name' => 'Omar Ba', 'email' => 'omar.ba@bimotech.sn',
                    'password' => Hash::make('Locataire@2025!'), 'role' => 'locataire',
                    'telephone' => '+221 76 400 20 30', 'adresse' => 'Grand Dakar',
                ],
                'profil' => [
                    'cni' => 'SN-4567890', 'profession' => 'Ingénieur Informatique',
                    'employeur' => 'Sonatel', 'revenu_mensuel' => 750000,
                    'quartier' => 'Grand Dakar', 'ville' => 'Dakar',
                    'contact_urgence_nom' => 'Mariama Ba',
                    'contact_urgence_tel' => '+221 76 500 20 30',
                    'contact_urgence_lien' => 'mère', 'cni_verified' => true,
                    'justif_revenus_fourni' => true,
                ],
            ],
            [
                'user' => [
                    'name' => 'Aminata Fall', 'email' => 'aminata.fall@bimotech.sn',
                    'password' => Hash::make('Locataire@2025!'), 'role' => 'locataire',
                    'telephone' => '+221 70 500 30 40', 'adresse' => 'Parcelles Assainies',
                ],
                'profil' => [
                    'cni' => 'SN-5678901', 'profession' => 'Commerçante',
                    'employeur' => 'Indépendante', 'revenu_mensuel' => 500000,
                    'quartier' => 'Parcelles Assainies', 'ville' => 'Dakar',
                    'contact_urgence_nom' => 'Cheikh Fall',
                    'contact_urgence_tel' => '+221 70 600 30 40',
                    'contact_urgence_lien' => 'frère', 'cni_verified' => true,
                    'justif_revenus_fourni' => false,
                ],
            ],
            [
                'user' => [
                    'name' => 'Ibrahima Thiam', 'email' => 'ibrahima.thiam@bimotech.sn',
                    'password' => Hash::make('Locataire@2025!'), 'role' => 'locataire',
                    'telephone' => '+221 77 600 40 50', 'adresse' => 'HLM, Dakar',
                ],
                'profil' => [
                    'cni' => 'SN-6789012', 'profession' => 'Médecin',
                    'employeur' => 'Hôpital Principal de Dakar', 'revenu_mensuel' => 900000,
                    'quartier' => 'HLM', 'ville' => 'Dakar',
                    'contact_urgence_nom' => 'Rokhaya Thiam',
                    'contact_urgence_tel' => '+221 77 700 40 50',
                    'contact_urgence_lien' => 'conjoint', 'cni_verified' => true,
                    'justif_revenus_fourni' => true,
                ],
            ],
            [
                'user' => [
                    'name' => 'Rokhaya Diop', 'email' => 'rokhaya.diop@bimotech.sn',
                    'password' => Hash::make('Locataire@2025!'), 'role' => 'locataire',
                    'telephone' => '+221 76 700 50 60', 'adresse' => 'Guédiawaye',
                ],
                'profil' => [
                    'cni' => 'SN-7890123', 'profession' => 'Fonctionnaire',
                    'employeur' => 'Mairie de Dakar', 'revenu_mensuel' => 280000,
                    'quartier' => 'Guédiawaye', 'ville' => 'Dakar',
                    'contact_urgence_nom' => 'Pape Diop',
                    'contact_urgence_tel' => '+221 76 800 50 60',
                    'contact_urgence_lien' => 'père', 'cni_verified' => false,
                    'justif_revenus_fourni' => true,
                ],
            ],
        ];

        $locataires = [];
        foreach ($locatairesData as $data) {
            $user = User::updateOrCreate(['email' => $data['user']['email']], $data['user']);
            $profil = Locataire::updateOrCreate(
                ['user_id' => $user->id],
                array_merge($data['profil'], ['user_id' => $user->id])
            );
            $locataires[] = ['user' => $user, 'profil' => $profil];
        }

        // ── 4. BIENS ──────────────────────────────────────────────────────────
        $biens = [
            Bien::updateOrCreate(['reference' => 'BIEN-2025-001'], [
                'proprietaire_id' => $proprietaires[0]['user']->id,
                'type' => 'Appartement', 'adresse' => '25 Rue de Thiong',
                'ville' => 'Dakar', 'surface_m2' => 85, 'nombre_pieces' => 3,
                'loyer_mensuel' => 250000, 'taux_commission' => 10.00, 'statut' => 'loue',
                'description' => 'Appartement F3 climatisé, 2ème étage.',
            ]),
            Bien::updateOrCreate(['reference' => 'BIEN-2025-002'], [
                'proprietaire_id' => $proprietaires[0]['user']->id,
                'type' => 'Villa', 'adresse' => '12 Rue des Almadies',
                'ville' => 'Dakar', 'surface_m2' => 200, 'nombre_pieces' => 6,
                'loyer_mensuel' => 600000, 'taux_commission' => 8.00, 'statut' => 'loue',
                'description' => 'Villa avec piscine et jardin.',
            ]),
            Bien::updateOrCreate(['reference' => 'BIEN-2025-003'], [
                'proprietaire_id' => $proprietaires[1]['user']->id,
                'type' => 'Studio', 'adresse' => '5 Rue Carnot',
                'ville' => 'Dakar', 'surface_m2' => 35, 'nombre_pieces' => 1,
                'loyer_mensuel' => 120000, 'taux_commission' => 12.00, 'statut' => 'loue',
                'description' => 'Studio meublé centre-ville.',
            ]),
            Bien::updateOrCreate(['reference' => 'BIEN-2025-004'], [
                'proprietaire_id' => $proprietaires[1]['user']->id,
                'type' => 'Bureau', 'adresse' => '18 Avenue Léopold S. Senghor',
                'ville' => 'Dakar', 'surface_m2' => 60, 'nombre_pieces' => 3,
                'loyer_mensuel' => 350000, 'taux_commission' => 10.00, 'statut' => 'disponible',
                'description' => 'Bureau climatisé, parking inclus.',
            ]),
        ];

        // ── 5. CONTRATS ───────────────────────────────────────────────────────────
$contratsConfig = [
    [$biens[0], $locataires[0]['user'], 3],
    [$biens[1], $locataires[1]['user'], 6],
    [$biens[2], $locataires[2]['user'], 1],
];

$contrats = [];
foreach ($contratsConfig as [$bien, $locUser, $mois]) {
    $contrats[] = Contrat::updateOrCreate(
        ['bien_id' => $bien->id, 'locataire_id' => $locUser->id],
        [
            'date_debut'        => Carbon::now()->subMonths($mois)->startOfMonth(),
            'loyer_contractuel' => $bien->loyer_mensuel,  // ← champ obligatoire
            'caution'           => $bien->loyer_mensuel,
            'statut'            => 'actif',
            'observations'      => 'Contrat de bail habitation. Préavis 1 mois.',
        ]
    );
}

// ── 6. PAIEMENTS ──────────────────────────────────────────────────────────
foreach ($contrats as $contrat) {
    // ✅ On récupère le bien directement depuis le contrat — pas depuis l'array
    $bien  = Bien::find($contrat->bien_id);
    $debut = Carbon::parse($contrat->date_debut);
    $moisCount = (int) $debut->diffInMonths(now());

    for ($i = 0; $i <= $moisCount; $i++) {
        $periode = $debut->copy()->addMonths($i)->startOfMonth();
        if ($periode->isAfter(now())) break;

        $calcul = Paiement::calculerMontants(
            (float) $contrat->loyer_contractuel,
            (float) $bien->taux_commission
        );

        Paiement::updateOrCreate(
            ['contrat_id' => $contrat->id, 'periode' => $periode->toDateString()],
            [
                'montant_encaisse'         => $contrat->loyer_contractuel,
                'mode_paiement'            => 'virement',
                'taux_commission_applique' => $bien->taux_commission,
                'commission_agence'        => $calcul['commission_ht'],
                'tva_commission'           => $calcul['tva'],
                'commission_ttc'           => $calcul['commission_ttc'],
                'net_proprietaire'         => $calcul['net_proprietaire'],
                'caution_percue'           => $i === 0 ? $contrat->caution : 0,
                'est_premier_paiement'     => $i === 0,
                'date_paiement'            => $periode->copy()->addDays(2)->toDateString(),
                'reference_paiement'       => 'QUITT-'.$contrat->id.'-'.$periode->format('Ym').'-'.str_pad($i+1, 3, '0', STR_PAD_LEFT),
                'statut'                   => 'valide',
            ]
        );
    }
}
    }
}