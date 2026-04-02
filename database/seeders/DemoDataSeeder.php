<?php

namespace Database\Seeders;

use App\Models\Agency;
use App\Models\Bien;
use App\Models\Contrat;
use App\Models\Locataire;
use App\Models\Paiement;
use App\Models\Proprietaire;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // ── Récupère l'agence existante ───────────────────────────────────
        $agency = Agency::where('slug', 'bimo-tech')->first();

        if (! $agency) {
            $this->command->error('Agence "bimo-tech" introuvable. Lancez d\'abord AgencySeeder.');
            return;
        }

        $agencyId = $agency->id;

        $this->command->info("🏢 Agence : {$agency->name} (ID: {$agencyId})");
        $this->command->newLine();

        // ─────────────────────────────────────────────────────────────────
        // DONNÉES SÉNÉGALAISES RÉALISTES
        // ─────────────────────────────────────────────────────────────────

        $nomsProprietaires = [
            ['name' => 'Moussa Diallo',      'email' => 'moussa.diallo@demo.sn',      'tel' => '+221771002030'],
            ['name' => 'Aminata Ndiaye',     'email' => 'aminata.ndiaye@demo.sn',     'tel' => '+221772003040'],
            ['name' => 'Ibrahima Sow',       'email' => 'ibrahima.sow@demo.sn',       'tel' => '+221773004050'],
            ['name' => 'Fatou Diop',         'email' => 'fatou.diop@demo.sn',         'tel' => '+221774005060'],
            ['name' => 'Ousmane Ba',         'email' => 'ousmane.ba@demo.sn',         'tel' => '+221775006070'],
            ['name' => 'Mariama Fall',       'email' => 'mariama.fall@demo.sn',       'tel' => '+221776007080'],
            ['name' => 'Cheikh Mbaye',       'email' => 'cheikh.mbaye@demo.sn',       'tel' => '+221777008090'],
            ['name' => 'Rokhaya Sarr',       'email' => 'rokhaya.sarr@demo.sn',       'tel' => '+221778009010'],
            ['name' => 'Abdoulaye Gueye',    'email' => 'abdoulaye.gueye@demo.sn',    'tel' => '+221779001020'],
            ['name' => 'Khady Thiongane',    'email' => 'khady.thiongane@demo.sn',    'tel' => '+221770002030'],
        ];

        $nomsLocataires = [
            ['name' => 'Souleymane Diouf',   'email' => 'souleymane.diouf@demo.sn',   'tel' => '+221701010101'],
            ['name' => 'Aissatou Cisse',     'email' => 'aissatou.cisse@demo.sn',     'tel' => '+221702020202'],
            ['name' => 'Pape Ngor Diagne',   'email' => 'pape.diagne@demo.sn',        'tel' => '+221703030303'],
            ['name' => 'Ndéye Fatou Toure',  'email' => 'ndeye.toure@demo.sn',        'tel' => '+221704040404'],
            ['name' => 'Babacar Faye',       'email' => 'babacar.faye@demo.sn',       'tel' => '+221705050505'],
            ['name' => 'Coumba Diallo',      'email' => 'coumba.diallo@demo.sn',      'tel' => '+221706060606'],
            ['name' => 'Seydou Kane',        'email' => 'seydou.kane@demo.sn',        'tel' => '+221707070707'],
            ['name' => 'Bineta Mbodj',       'email' => 'bineta.mbodj@demo.sn',       'tel' => '+221708080808'],
            ['name' => 'Mamadou Lamine Ba',  'email' => 'mamadou.ba@demo.sn',         'tel' => '+221709090909'],
            ['name' => 'Yacine Niang',       'email' => 'yacine.niang@demo.sn',       'tel' => '+221700100100'],
            ['name' => 'Alioune Badara Sy',  'email' => 'alioune.sy@demo.sn',         'tel' => '+221711111111'],
            ['name' => 'Mame Diarra Thiam',  'email' => 'mame.thiam@demo.sn',         'tel' => '+221712121212'],
            ['name' => 'Oumar Ly',           'email' => 'oumar.ly@demo.sn',           'tel' => '+221713131313'],
            ['name' => 'Adja Ndiaye',        'email' => 'adja.ndiaye@demo.sn',        'tel' => '+221714141414'],
            ['name' => 'Thierno Balde',      'email' => 'thierno.balde@demo.sn',      'tel' => '+221715151515'],
            ['name' => 'Rama Cisse',         'email' => 'rama.cisse@demo.sn',         'tel' => '+221716161616'],
            ['name' => 'Demba Diop',         'email' => 'demba.diop@demo.sn',         'tel' => '+221717171717'],
            ['name' => 'Awa Sarr',           'email' => 'awa.sarr@demo.sn',           'tel' => '+221718181818'],
            ['name' => 'Lamine Diallo',      'email' => 'lamine.diallo@demo.sn',      'tel' => '+221719191919'],
            ['name' => 'Nafi Gaye',          'email' => 'nafi.gaye@demo.sn',          'tel' => '+221720202020'],
            ['name' => 'Serigne Mbaye',      'email' => 'serigne.mbaye@demo.sn',      'tel' => '+221721212121'],
            ['name' => 'Astou Dieye',        'email' => 'astou.dieye@demo.sn',        'tel' => '+221722222222'],
            ['name' => 'Fallou Ngom',        'email' => 'fallou.ngom@demo.sn',        'tel' => '+221723232323'],
            ['name' => 'Ndéye Seck',         'email' => 'ndeye.seck@demo.sn',         'tel' => '+221724242424'],
            ['name' => 'Maguette Diallo',    'email' => 'maguette.diallo@demo.sn',    'tel' => '+221725252525'],
            ['name' => 'Ibou Badji',         'email' => 'ibou.badji@demo.sn',         'tel' => '+221726262626'],
            ['name' => 'Fatoumata Kouyate',  'email' => 'fatoumata.kouyate@demo.sn',  'tel' => '+221727272727'],
        ];

        // Données des 30 biens
        $biensDonnees = [
            // Propriétaire 1 — Moussa Diallo (3 biens)
            ['type' => 'villa',        'adresse' => '15 Rue Pasteur',          'ville' => 'Dakar',     'quartier' => 'Plateau',      'loyer' => 450000, 'commission' => 10],
            ['type' => 'appartement',  'adresse' => '8 Cité Keur Gorgui',      'ville' => 'Dakar',     'quartier' => 'Keur Gorgui',  'loyer' => 280000, 'commission' => 10],
            ['type' => 'studio',       'adresse' => '3 Rue de Cambérène',      'ville' => 'Dakar',     'quartier' => 'Cambérène',    'loyer' => 120000, 'commission' => 10],
            // Propriétaire 2 — Aminata Ndiaye (3 biens)
            ['type' => 'appartement',  'adresse' => '22 Rue Blaise Diagne',    'ville' => 'Dakar',     'quartier' => 'Médina',       'loyer' => 200000, 'commission' => 8],
            ['type' => 'villa',        'adresse' => '5 Allée des Baobabs',     'ville' => 'Thiès',     'quartier' => 'Thiès-Nord',   'loyer' => 350000, 'commission' => 8],
            ['type' => 'bureau',       'adresse' => '18 Avenue Faidherbe',     'ville' => 'Dakar',     'quartier' => 'Plateau',      'loyer' => 500000, 'commission' => 8],
            // Propriétaire 3 — Ibrahima Sow (3 biens)
            ['type' => 'maison',       'adresse' => '7 Rue 10 Médina',         'ville' => 'Dakar',     'quartier' => 'Médina',       'loyer' => 180000, 'commission' => 10],
            ['type' => 'appartement',  'adresse' => '12 Cité Fadia',           'ville' => 'Dakar',     'quartier' => 'Fadia',        'loyer' => 250000, 'commission' => 10],
            ['type' => 'studio',       'adresse' => '4 Rue de Ouakam',         'ville' => 'Dakar',     'quartier' => 'Ouakam',       'loyer' => 100000, 'commission' => 10],
            // Propriétaire 4 — Fatou Diop (3 biens)
            ['type' => 'villa',        'adresse' => '9 Corniche Ouest',        'ville' => 'Dakar',     'quartier' => 'Almadies',     'loyer' => 650000, 'commission' => 12],
            ['type' => 'appartement',  'adresse' => '33 Rue Moussé Diop',      'ville' => 'Dakar',     'quartier' => 'Médina',       'loyer' => 220000, 'commission' => 10],
            ['type' => 'chambre',      'adresse' => '6 Rue Vincens',           'ville' => 'Dakar',     'quartier' => 'Gueule Tapée', 'loyer' => 75000,  'commission' => 10],
            // Propriétaire 5 — Ousmane Ba (3 biens)
            ['type' => 'appartement',  'adresse' => '25 Cité Liberté',         'ville' => 'Dakar',     'quartier' => 'Liberté 6',    'loyer' => 300000, 'commission' => 10],
            ['type' => 'local_commercial', 'adresse' => '14 Marché Sandaga',   'ville' => 'Dakar',     'quartier' => 'Plateau',      'loyer' => 400000, 'commission' => 10],
            ['type' => 'studio',       'adresse' => '2 Rue de la Gare',        'ville' => 'Thiès',     'quartier' => 'Centre-ville', 'loyer' => 80000,  'commission' => 8],
            // Propriétaire 6 — Mariama Fall (3 biens)
            ['type' => 'villa',        'adresse' => '11 Cité Keur Massar',     'ville' => 'Dakar',     'quartier' => 'Keur Massar',  'loyer' => 280000, 'commission' => 10],
            ['type' => 'appartement',  'adresse' => '17 Rue Tolbiac',          'ville' => 'Dakar',     'quartier' => 'Sacré-Cœur',   'loyer' => 320000, 'commission' => 10],
            ['type' => 'maison',       'adresse' => '5 Rue Galandou Diouf',    'ville' => 'Saint-Louis','quartier' => 'Centre',      'loyer' => 150000, 'commission' => 8],
            // Propriétaire 7 — Cheikh Mbaye (3 biens)
            ['type' => 'appartement',  'adresse' => '8 Cité Sipres',           'ville' => 'Dakar',     'quartier' => 'Sipres',       'loyer' => 260000, 'commission' => 10],
            ['type' => 'bureau',       'adresse' => '20 Avenue Cheikh A. Diop','ville' => 'Dakar',     'quartier' => 'Mermoz',       'loyer' => 450000, 'commission' => 10],
            ['type' => 'studio',       'adresse' => '1 Rue de Mbour',          'ville' => 'Mbour',     'quartier' => 'Centre',       'loyer' => 90000,  'commission' => 8],
            // Propriétaire 8 — Rokhaya Sarr (3 biens)
            ['type' => 'villa',        'adresse' => '30 Cité des Eaux',        'ville' => 'Dakar',     'quartier' => 'Point-E',      'loyer' => 550000, 'commission' => 12],
            ['type' => 'appartement',  'adresse' => '6 Rue Wagane Diouf',      'ville' => 'Dakar',     'quartier' => 'HLM',          'loyer' => 190000, 'commission' => 10],
            ['type' => 'chambre',      'adresse' => '3 Rue Carnot',            'ville' => 'Kaolack',   'quartier' => 'Médina',       'loyer' => 60000,  'commission' => 8],
            // Propriétaire 9 — Abdoulaye Gueye (3 biens)
            ['type' => 'appartement',  'adresse' => '14 Cité Tobago',          'ville' => 'Dakar',     'quartier' => 'Tobago',       'loyer' => 240000, 'commission' => 10],
            ['type' => 'villa',        'adresse' => '7 Rue Dial Diop',         'ville' => 'Dakar',     'quartier' => 'Dieuppeul',    'loyer' => 380000, 'commission' => 10],
            ['type' => 'studio',       'adresse' => '9 Rue de Ziguinchor',     'ville' => 'Ziguinchor','quartier' => 'Centre',       'loyer' => 70000,  'commission' => 8],
            // Propriétaire 10 — Khady Thiongane (3 biens)
            ['type' => 'appartement',  'adresse' => '21 Cité Dakar Dem Dikk',  'ville' => 'Dakar',     'quartier' => 'Parcelles',    'loyer' => 210000, 'commission' => 10],
            ['type' => 'maison',       'adresse' => '4 Rue de Rufisque',       'ville' => 'Rufisque',  'quartier' => 'Centre',       'loyer' => 130000, 'commission' => 8],
            ['type' => 'villa',        'adresse' => '16 Saly Portudal',        'ville' => 'Mbour',     'quartier' => 'Saly',         'loyer' => 500000, 'commission' => 10],
        ];

        // ─────────────────────────────────────────────────────────────────
        // CRÉATION DES PROPRIÉTAIRES
        // ─────────────────────────────────────────────────────────────────

        $this->command->info('👤 Création des propriétaires...');
        $proprietaires = [];

        foreach ($nomsProprietaires as $data) {
            $user = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name'              => $data['name'],
                    'password'          => Hash::make('password'),
                    'role'              => 'proprietaire',
                    'agency_id'         => $agencyId,
                    'telephone'         => $data['tel'],
                    'adresse'           => 'Dakar, Sénégal',
                    'email_verified_at' => now(),
                ]
            );

            Proprietaire::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'ville'                 => 'Dakar',
                    'mode_paiement_prefere' => collect(['virement', 'wave', 'orange_money'])->random(),
                    'genre'                 => str_contains($data['name'], 'Aminata') || str_contains($data['name'], 'Fatou') || str_contains($data['name'], 'Mariama') || str_contains($data['name'], 'Rokhaya') || str_contains($data['name'], 'Khady') ? 'F' : 'M',
                ]
            );

            $proprietaires[] = $user;
            $this->command->line("  ✅ {$user->name}");
        }

        // ─────────────────────────────────────────────────────────────────
        // CRÉATION DES LOCATAIRES
        // ─────────────────────────────────────────────────────────────────

        $this->command->newLine();
        $this->command->info('🏠 Création des locataires...');
        $locataires = [];

        foreach ($nomsLocataires as $data) {
            $user = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name'              => $data['name'],
                    'password'          => Hash::make('password'),
                    'role'              => 'locataire',
                    'agency_id'         => $agencyId,
                    'telephone'         => $data['tel'],
                    'email_verified_at' => now(),
                ]
            );

            Locataire::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'ville'    => collect(['Dakar', 'Thiès', 'Mbour', 'Saint-Louis'])->random(),
                    'genre'    => in_array(explode(' ', $data['name'])[0], ['Aissatou', 'Ndéye', 'Coumba', 'Bineta', 'Yacine', 'Mame', 'Adja', 'Rama', 'Awa', 'Nafi', 'Astou', 'Ndéye', 'Maguette', 'Fatoumata']) ? 'F' : 'M',
                    'profession' => collect(['Fonctionnaire', 'Commerçant', 'Enseignant', 'Ingénieur', 'Médecin', 'Infirmier', 'Chauffeur', 'Entrepreneur'])->random(),
                ]
            );

            $locataires[] = $user;
            $this->command->line("  ✅ {$user->name}");
        }

        // ─────────────────────────────────────────────────────────────────
        // CRÉATION DES BIENS
        // ─────────────────────────────────────────────────────────────────

        $this->command->newLine();
        $this->command->info('🏢 Création des biens...');
        $biens = [];
        $count = 0;

        foreach ($biensDonnees as $index => $donnee) {
            // 3 biens par propriétaire
            $proprietaireIndex = intdiv($index, 3);
            $proprietaire      = $proprietaires[$proprietaireIndex];

            $count++;
            $reference = sprintf(
                'BIEN-%s-%s-%s',
                now()->year,
                str_pad($count, 4, '0', STR_PAD_LEFT),
                strtoupper(substr(md5($donnee['adresse']), 0, 4))
            );

            // 27 premiers biens loués (pour les 27 contrats), 3 derniers disponibles
            $statut = $count <= 27 ? 'loue' : 'disponible';

            $bien = Bien::updateOrCreate(
                ['reference' => $reference],
                [
                    'agency_id'       => $agencyId,
                    'proprietaire_id' => $proprietaire->id,
                    'type'            => $donnee['type'],
                    'adresse'         => $donnee['adresse'],
                    'ville'           => $donnee['ville'],
                    'quartier'        => $donnee['quartier'] ?? null,
                    'loyer_mensuel'   => $donnee['loyer'],
                    'taux_commission' => $donnee['commission'],
                    'statut'          => $statut,
                    'surface_m2'      => match($donnee['type']) {
                        'studio'  => rand(25, 40),
                        'chambre' => rand(15, 25),
                        'appartement' => rand(60, 120),
                        'villa'   => rand(150, 300),
                        'maison'  => rand(80, 180),
                        'bureau'  => rand(40, 100),
                        'local_commercial' => rand(30, 80),
                        default   => rand(50, 100),
                    },
                    'nombre_pieces'   => match($donnee['type']) {
                        'studio', 'chambre' => 1,
                        'appartement' => rand(2, 4),
                        'villa', 'maison' => rand(4, 7),
                        default => rand(1, 3),
                    },
                    'meuble'          => (bool) rand(0, 1),
                    'description'     => ucfirst($donnee['type']) . ' situé à ' . $donnee['adresse'] . ', ' . $donnee['ville'] . '. Loyer : ' . number_format($donnee['loyer'], 0, ',', ' ') . ' FCFA/mois.',
                ]
            );

            $biens[] = $bien;
            $this->command->line("  ✅ {$reference} — {$donnee['type']} à {$donnee['ville']} ({$donnee['loyer']} FCFA)");
        }

        // ─────────────────────────────────────────────────────────────────
        // CRÉATION DES CONTRATS ET PAIEMENTS (27 contrats actifs)
        // ─────────────────────────────────────────────────────────────────

        $this->command->newLine();
        $this->command->info('📄 Création des contrats et paiements...');

        for ($i = 0; $i < 27; $i++) {
            $bien      = $biens[$i];
            $locataire = $locataires[$i];

            $dateDebut = now()->startOfMonth()->subMonths(rand(2, 8));

            $referenceBail = sprintf('BIMO-%s-%s', now()->year, str_pad($i + 1, 5, '0', STR_PAD_LEFT));

            $loyerNu          = (float) $bien->loyer_mensuel;
            $chargesMensuelles = rand(0, 1) ? round($loyerNu * 0.1 / 5000) * 5000 : 0;
            $loyerContractuel  = $loyerNu + $chargesMensuelles;

            $contrat = Contrat::updateOrCreate(
                ['bien_id' => $bien->id, 'locataire_id' => $locataire->id],
                [
                    'agency_id'           => $agencyId,
                    'date_debut'          => $dateDebut->toDateString(),
                    'date_fin'            => rand(0, 3) === 0
                        ? $dateDebut->copy()->addYear()->toDateString()
                        : null,
                    'loyer_nu'            => $loyerNu,
                    'loyer_contractuel'   => $loyerContractuel,
                    'charges_mensuelles'  => $chargesMensuelles,
                    'caution'             => $loyerNu,
                    'statut'              => 'actif',
                    'type_bail'           => in_array($bien->type, ['bureau', 'local_commercial']) ? 'commercial' : 'habitation',
                    'taux_commission_applique' => $bien->taux_commission,
                    'frais_agence'        => $loyerNu,
                    'nombre_mois_caution' => 1,
                    'reference_bail'      => $referenceBail,
                    'observations'        => 'Contrat de bail conforme loi n° 81-18 du 25 juin 1981.',
                ]
            );

            // ── Paiements pour chaque mois depuis le début du contrat ──
            $moisTotal = $dateDebut->diffInMonths(now());
            $tauxCommission = (float) $bien->taux_commission;

            for ($m = 0; $m <= $moisTotal; $m++) {
                $periode = $dateDebut->copy()->addMonths($m)->startOfMonth();

                // Dernier mois : 30% de chance de ne pas avoir payé (impayé)
                if ($m === $moisTotal && rand(1, 10) <= 3) {
                    continue;
                }

                $reference = sprintf(
                    'QUITT-%s-%s-%s',
                    $contrat->id,
                    $periode->format('Ym'),
                    str_pad($m + 1, 4, '0', STR_PAD_LEFT)
                );

                $calcul = Paiement::calculerMontants(
                    loyerNu:        $loyerNu,
                    tauxCommission: $tauxCommission,
                    chargesAmount:  $chargesMensuelles,
                );

                $estPremier = $m === 0;

                $paiement = Paiement::updateOrCreate(
                    ['reference_paiement' => $reference],
                    [
                        'agency_id'                => $agencyId,
                        'contrat_id'               => $contrat->id,
                        'periode'                  => $periode->toDateString(),
                        'loyer_nu'                 => $calcul['loyer_nu'],
                        'charges_amount'           => $calcul['charges_amount'],
                        'tom_amount'               => 0,
                        'montant_encaisse'         => $calcul['montant_encaisse'],
                        'mode_paiement'            => collect(['virement', 'wave', 'orange_money', 'especes'])->random(),
                        'taux_commission_applique' => $tauxCommission,
                        'commission_agence'        => $calcul['commission_ht'],
                        'tva_commission'           => $calcul['tva'],
                        'commission_ttc'           => $calcul['commission_ttc'],
                        'net_proprietaire'         => $calcul['net_proprietaire'],
                        'caution_percue'           => $estPremier ? $loyerNu : 0,
                        'est_premier_paiement'     => $estPremier,
                        'date_paiement'            => $periode->copy()->addDays(rand(1, 5))->toDateString(),
                        'reference_paiement'       => $reference,
                        'reference_bail'           => $referenceBail,
                        'statut'                   => 'valide',
                        'notes'                    => $estPremier ? 'Premier loyer + caution encaissés.' : null,
                    ]
                );
            }

            $this->command->line("  ✅ Contrat {$referenceBail} — {$locataire->name} → {$bien->reference}");
        }

        // ─────────────────────────────────────────────────────────────────
        // RÉSUMÉ FINAL
        // ─────────────────────────────────────────────────────────────────

        $this->command->newLine();
        $this->command->info('════════════════════════════════════════════════');
        $this->command->info('  ✅ Données de démo créées avec succès !');
        $this->command->info('════════════════════════════════════════════════');
        $this->command->table(
            ['Type', 'Quantité'],
            [
                ['Propriétaires', count($proprietaires)],
                ['Locataires',    count($locataires)],
                ['Biens',         count($biens)],
                ['Biens loués',   27],
                ['Biens disponibles', 3],
                ['Contrats actifs', 27],
                ['Paiements générés', Paiement::where('agency_id', $agencyId)->count()],
            ]
        );
        $this->command->newLine();
        $this->command->line('  🔑 Mot de passe de tous les comptes : <fg=yellow>password</>');
        $this->command->newLine();
    }
}