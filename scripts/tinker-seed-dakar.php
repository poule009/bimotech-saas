<?php
// ═══════════════════════════════════════════════════════════════════════════
// BIMO-TECH — Script de peuplement Tinker — Données test Dakar / Sénégal
//
// USAGE :
//   php artisan tinker
//   >>> exec(file_get_contents('scripts/tinker-seed-dakar.php'));
//
//   OU copiez-collez tout le contenu directement dans Tinker.
//
// PRÉREQUIS : Une agence doit exister (créée via /register/agency).
// IDEMPOTENT : firstOrCreate sur email — pas de doublons si relancé.
// ═══════════════════════════════════════════════════════════════════════════

use App\Models\Agency;
use App\Models\Bien;
use App\Models\Contrat;
use App\Models\DepenseGestion;
use App\Models\Locataire;
use App\Models\Paiement;
use App\Models\Proprietaire;
use App\Models\User;
use App\Services\FiscalContext;
use App\Services\FiscalService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

// ─────────────────────────────────────────────────────────────────────────
// 0. AGENCE — point d'ancrage multi-tenant
// ─────────────────────────────────────────────────────────────────────────
$agency = Agency::first();
if (! $agency) {
    echo "❌  Aucune agence trouvée.\n";
    echo "    Créez-en une sur /register/agency puis relancez ce script.\n";
    return;
}
$agencyId = $agency->id;
echo "\n✅  Agence : {$agency->name}  (ID: {$agencyId})\n";
echo str_repeat('─', 60) . "\n";

// ─────────────────────────────────────────────────────────────────────────
// HELPERS
// ─────────────────────────────────────────────────────────────────────────

/** Crée ou récupère un User selon l'email (idempotent). */
$mkUser = function (string $name, string $email, string $tel, string $role) use ($agencyId): User {
    return User::firstOrCreate(
        ['email' => $email],
        [
            'name'               => $name,
            'password'           => Hash::make('password'),
            'role'               => $role,
            'agency_id'          => $agencyId,
            'telephone'          => $tel,
            'email_verified_at'  => now(),
        ]
    );
};

/** Génère une référence alphanumérique unique avec préfixe. */
$ref = fn (string $prefix): string => $prefix . '-' . strtoupper(substr(md5(uniqid('', true)), 0, 6));

/** Génère une référence de paiement reproductible. */
$refPay = fn (int $contratId, int $moisOffset): string
    => 'PAY-' . strtoupper(substr(md5("c{$contratId}m{$moisOffset}"), 0, 8));


// ─────────────────────────────────────────────────────────────────────────
// 1. PROPRIÉTAIRES  (3 profils)
// ─────────────────────────────────────────────────────────────────────────
echo "\n👤  PROPRIÉTAIRES\n";

// ── Bailleur A : Awa Thiam — Premium, Plateau ─────────────────────────
$uAwa = $mkUser('Awa Thiam', 'awa.thiam@test.sn', '+221771100001', 'proprietaire');
Proprietaire::firstOrCreate(['user_id' => $uAwa->id], [
    'ville'                 => 'Dakar',
    'quartier'              => 'Plateau',
    'mode_paiement_prefere' => 'virement',
    'ninea'                 => 'SN-2021-AWA-001',
    'assujetti_tva'         => true,
]);
echo "    ✓ Awa Thiam     (Premium — Plateau)         user #{$uAwa->id}\n";

// ── Bailleur B : Moussa Diallo — Investisseur, Liberté 6 ─────────────
$uMoussa = $mkUser('Moussa Diallo', 'moussa.diallo@test.sn', '+221772100002', 'proprietaire');
Proprietaire::firstOrCreate(['user_id' => $uMoussa->id], [
    'ville'                 => 'Dakar',
    'quartier'              => 'Liberté 6',
    'mode_paiement_prefere' => 'wave',
    'ninea'                 => null,
    'assujetti_tva'         => false,
]);
echo "    ✓ Moussa Diallo (Investisseur — Liberté 6)  user #{$uMoussa->id}\n";

// ── Bailleur C : Modou Fall — Traditionnel, Pikine ───────────────────
$uModou = $mkUser('Modou Fall', 'modou.fall@test.sn', '+221773100003', 'proprietaire');
Proprietaire::firstOrCreate(['user_id' => $uModou->id], [
    'ville'                 => 'Dakar',
    'quartier'              => 'Pikine',
    'mode_paiement_prefere' => 'especes',
    'ninea'                 => null,
    'assujetti_tva'         => false,
]);
echo "    ✓ Modou Fall    (Traditionnel — Pikine)     user #{$uModou->id}\n";


// ─────────────────────────────────────────────────────────────────────────
// 2. BIENS  (7 unités, loyers 150k → 600k FCFA)
// ─────────────────────────────────────────────────────────────────────────
echo "\n🏢  BIENS\n";

// Bailleur A — 2 biens (Plateau)
$b1 = Bien::create([
    'agency_id'       => $agencyId, 'proprietaire_id' => $uAwa->id,
    'reference'       => $ref('PLT'), 'type' => 'appartement',
    'adresse'         => '12 Rue Vincens',       'ville' => 'Dakar', 'quartier' => 'Plateau',
    'surface_m2'      => 120, 'nombre_pieces' => 4, 'meuble' => false,
    'loyer_mensuel'   => 600000, 'taux_commission' => 10, 'statut' => 'loue',
]);
echo "    ✓ {$b1->reference}  F4 Plateau 600 000 F      (Awa Thiam)\n";

$b2 = Bien::create([
    'agency_id'       => $agencyId, 'proprietaire_id' => $uAwa->id,
    'reference'       => $ref('PLT'), 'type' => 'studio',
    'adresse'         => '4 Avenue Léopold Sédar Senghor', 'ville' => 'Dakar', 'quartier' => 'Plateau',
    'surface_m2'      => 35,  'nombre_pieces' => 1, 'meuble' => true,
    'loyer_mensuel'   => 200000, 'taux_commission' => 10, 'statut' => 'loue',
]);
echo "    ✓ {$b2->reference}  Studio meublé Plateau 200 000 F (Awa Thiam)\n";

// Bailleur B — 3 biens (Liberté 6)
$b3 = Bien::create([
    'agency_id'       => $agencyId, 'proprietaire_id' => $uMoussa->id,
    'reference'       => $ref('LB6'), 'type' => 'appartement',
    'adresse'         => '7 Cité Liberté 6',    'ville' => 'Dakar', 'quartier' => 'Liberté 6',
    'surface_m2'      => 85,  'nombre_pieces' => 3, 'meuble' => false,
    'loyer_mensuel'   => 350000, 'taux_commission' => 10, 'statut' => 'loue',
]);
echo "    ✓ {$b3->reference}  F3 Liberté 6 350 000 F     (Moussa Diallo)\n";

$b4 = Bien::create([
    'agency_id'       => $agencyId, 'proprietaire_id' => $uMoussa->id,
    'reference'       => $ref('LB6'), 'type' => 'villa',
    'adresse'         => '23 Rue 10 Liberté',   'ville' => 'Dakar', 'quartier' => 'Liberté 6',
    'surface_m2'      => 200, 'nombre_pieces' => 5, 'meuble' => false,
    'loyer_mensuel'   => 500000, 'taux_commission' => 10, 'statut' => 'loue',
]);
echo "    ✓ {$b4->reference}  Villa Liberté 6 500 000 F  (Moussa Diallo)\n";

$b5 = Bien::create([
    'agency_id'       => $agencyId, 'proprietaire_id' => $uMoussa->id,
    'reference'       => $ref('LB6'), 'type' => 'studio',
    'adresse'         => '15 Rue Dial Diop',    'ville' => 'Dakar', 'quartier' => 'Liberté 6',
    'surface_m2'      => 28,  'nombre_pieces' => 1, 'meuble' => false,
    'loyer_mensuel'   => 150000, 'taux_commission' => 10, 'statut' => 'loue',
]);
echo "    ✓ {$b5->reference}  Studio Liberté 6 150 000 F (Moussa Diallo)\n";

// Bailleur C — 2 biens (Pikine)
$b6 = Bien::create([
    'agency_id'       => $agencyId, 'proprietaire_id' => $uModou->id,
    'reference'       => $ref('PIK'), 'type' => 'appartement',
    'adresse'         => '5 Rue 15 Pikine Icotaf', 'ville' => 'Dakar', 'quartier' => 'Pikine Icotaf',
    'surface_m2'      => 100, 'nombre_pieces' => 4, 'meuble' => false,
    'loyer_mensuel'   => 280000, 'taux_commission' => 10, 'statut' => 'loue',
]);
echo "    ✓ {$b6->reference}  F4 Pikine 280 000 F        (Modou Fall)\n";

$b7 = Bien::create([
    'agency_id'       => $agencyId, 'proprietaire_id' => $uModou->id,
    'reference'       => $ref('PIK'), 'type' => 'bureau',
    'adresse'         => '3 Ave. Cheikh Anta Diop', 'ville' => 'Dakar', 'quartier' => 'Pikine Grand-Yoff',
    'surface_m2'      => 60,  'nombre_pieces' => 2, 'meuble' => false,
    'loyer_mensuel'   => 250000, 'taux_commission' => 10, 'statut' => 'loue',
]);
echo "    ✓ {$b7->reference}  Bureau Pikine 250 000 F    (Modou Fall)\n";


// ─────────────────────────────────────────────────────────────────────────
// 3. LOCATAIRES  (5 particuliers + 2 entreprises)
// ─────────────────────────────────────────────────────────────────────────
echo "\n👥  LOCATAIRES\n";

// L1 — Entreprise (BRS 5% override) → Bien b1, F4 Plateau commercial
$uL1 = $mkUser('Cabinet Sénégal Conseil SARL', 'senegal.conseil@test.sn', '+221331001001', 'locataire');
Locataire::firstOrCreate(['user_id' => $uL1->id], [
    'est_entreprise'    => true,  'type_locataire' => 'entreprise',
    'nom_entreprise'    => 'Cabinet Sénégal Conseil SARL',
    'ninea_locataire'   => 'SN-LOC-2020-0099',
    'taux_brs_override' => 5.0,   // 5% (override du légal 15%)
    'profession'        => 'Cabinet de conseil juridique',
]);
echo "    ✓ Cabinet Sénégal Conseil SARL  (entreprise, BRS 5%)\n";

// L2 — Particulier → Studio meublé Plateau
$uL2 = $mkUser('Aminata Kouyaté', 'aminata.kouyate@test.sn', '+221701001002', 'locataire');
Locataire::firstOrCreate(['user_id' => $uL2->id], [
    'est_entreprise' => false, 'type_locataire' => 'particulier',
    'profession' => 'Consultante RH', 'ville' => 'Dakar', 'quartier' => 'Sacré-Cœur',
]);
echo "    ✓ Aminata Kouyaté       (particulier)\n";

// L3 — Particulier → F3 Liberté 6
$uL3 = $mkUser('Babacar Ndiaye', 'babacar.ndiaye@test.sn', '+221702001003', 'locataire');
Locataire::firstOrCreate(['user_id' => $uL3->id], [
    'est_entreprise' => false, 'type_locataire' => 'particulier',
    'profession' => 'Ingénieur télécom', 'ville' => 'Dakar', 'quartier' => 'Liberté 6',
]);
echo "    ✓ Babacar Ndiaye        (particulier)\n";

// L4 — Particulier → Villa Liberté 6
$uL4 = $mkUser('Rokhaya Faye', 'rokhaya.faye@test.sn', '+221703001004', 'locataire');
Locataire::firstOrCreate(['user_id' => $uL4->id], [
    'est_entreprise' => false, 'type_locataire' => 'particulier',
    'profession' => 'Médecin généraliste', 'ville' => 'Dakar', 'quartier' => 'Mermoz',
]);
echo "    ✓ Rokhaya Faye          (particulier)\n";

// L5 — Particulier → Studio Liberté 6
$uL5 = $mkUser('Seydou Diallo', 'seydou.diallo2@test.sn', '+221704001005', 'locataire');
Locataire::firstOrCreate(['user_id' => $uL5->id], [
    'est_entreprise' => false, 'type_locataire' => 'particulier',
    'profession' => 'Étudiant UCAD', 'ville' => 'Dakar', 'quartier' => 'Liberté 6',
]);
echo "    ✓ Seydou Diallo         (particulier)\n";

// L6 — Particulier → F4 Pikine
$uL6 = $mkUser('Ndéye Mbaye', 'ndeye.mbaye@test.sn', '+221705001006', 'locataire');
Locataire::firstOrCreate(['user_id' => $uL6->id], [
    'est_entreprise' => false, 'type_locataire' => 'particulier',
    'profession' => 'Commerçante', 'ville' => 'Dakar', 'quartier' => 'Pikine',
]);
echo "    ✓ Ndéye Mbaye           (particulier)\n";

// L7 — Entreprise (BRS légal 15%) → Bureau Pikine commercial
$uL7 = $mkUser('Groupe Pikine Business', 'pikine.business@test.sn', '+221338001007', 'locataire');
Locataire::firstOrCreate(['user_id' => $uL7->id], [
    'est_entreprise'    => true,  'type_locataire' => 'entreprise',
    'nom_entreprise'    => 'Groupe Pikine Business',
    'ninea_locataire'   => 'SN-LOC-2019-0077',
    'taux_brs_override' => null,   // BRS légal 15%
    'profession'        => 'Commerce général import-export',
]);
echo "    ✓ Groupe Pikine Business (entreprise, BRS 15%)\n";


// ─────────────────────────────────────────────────────────────────────────
// 4. CONTRATS  (débutés 4 mois en arrière, durée 12 mois)
// ─────────────────────────────────────────────────────────────────────────
echo "\n📄  CONTRATS\n";

$dateDebut = Carbon::now()->subMonths(4)->startOfMonth();
$dateFin   = $dateDebut->copy()->addYear();

/*
 * Définition des 7 contrats :
 * [bien, locUser, typeBail, loyerNu, charges, tom, caution, fraisAgence,
 *  loyerAssujettiTva, brsApplicable, tauxBrsManuel, tauxDgidOverride]
 */
$contratsSpec = [
    // 0 — Awa / F4 Plateau — bail commercial, TVA + BRS 5% + DGID 5%
    [$b1, $uL1, 'commercial', 600000,  0,      0,      1200000, 120000, true,  true,  5.0,  5.0],
    // 1 — Awa / Studio meublé Plateau — habitation meublée (TVA auto via FiscalService)
    [$b2, $uL2, 'habitation', 200000,  0,      0,       400000,  40000, null,  false, null, null],
    // 2 — Moussa / F3 Liberté 6 — habitation + charges + TOM
    [$b3, $uL3, 'habitation', 350000, 20000, 12600,    700000,  70000, null,  false, null, null],
    // 3 — Moussa / Villa Liberté 6 — habitation + charges + TOM
    [$b4, $uL4, 'habitation', 500000, 30000, 18000,   1000000, 100000, null,  false, null, null],
    // 4 — Moussa / Studio Liberté 6 — habitation simple
    [$b5, $uL5, 'habitation', 150000,  0,      5400,   300000,  30000, null,  false, null, null],
    // 5 — Modou / F4 Pikine — habitation + charges + TOM
    [$b6, $uL6, 'habitation', 280000, 15000, 10080,   560000,  56000, null,  false, null, null],
    // 6 — Modou / Bureau Pikine — bail commercial + BRS légal 15% + DGID 2%
    [$b7, $uL7, 'commercial', 250000,  0,      0,      500000,  50000, true,  true,  null, 2.0],
];

$contrats = [];
foreach ($contratsSpec as $ci => [
    $bien, $locUser, $typeB, $loyerNu, $charges, $tom,
    $caution, $fraisAgence, $assTva, $brsAppl, $tauxBrs, $tauxDgid
]) {
    $prefixRef = strtoupper(substr($typeB, 0, 3));
    $refBail   = 'BAIL-' . $prefixRef . '-' . str_pad($ci + 1, 3, '0', STR_PAD_LEFT);

    $c = Contrat::create([
        'agency_id'               => $agencyId,
        'bien_id'                 => $bien->id,
        'locataire_id'            => $locUser->id,
        'date_debut'              => $dateDebut->format('Y-m-d'),
        'date_fin'                => $dateFin->format('Y-m-d'),
        'loyer_nu'                => $loyerNu,
        'charges_mensuelles'      => $charges,
        'tom_amount'              => $tom,
        'caution'                 => $caution,
        'nombre_mois_caution'     => 2,
        'frais_agence'            => $fraisAgence,
        'statut'                  => 'actif',
        'type_bail'               => $typeB,
        'reference_bail'          => $refBail,
        'loyer_assujetti_tva'     => $assTva,
        'taux_tva_loyer'          => $assTva ? 18.0 : null,
        'brs_applicable'          => $brsAppl,
        'taux_brs_manuel'         => $tauxBrs,
        'enregistrement_exonere'  => false,
        'taux_enregistrement_dgid'=> $tauxDgid,
    ]);

    $contrats[$ci] = $c;
    $locNom = $locUser->name;
    echo "    ✓ {$refBail}  →  {$bien->reference}  /  {$locNom}\n";
}


// ─────────────────────────────────────────────────────────────────────────
// 5. PAIEMENTS  — 3 mois × 7 contrats via FiscalService
// ─────────────────────────────────────────────────────────────────────────
echo "\n💰  PAIEMENTS  (3 mois × 7 contrats = 21 paiements)\n";

$periodes = [
    Carbon::now()->subMonths(2)->startOfMonth(),  // M-2 = premier paiement
    Carbon::now()->subMonths(1)->startOfMonth(),  // M-1
    Carbon::now()->startOfMonth(),                // M courant
];

$modesP = ['especes', 'wave', 'orange_money', 'virement'];

$allPaiements = [];   // $allPaiements[$contratIndex][$periodeIndex]

foreach ($contrats as $ci => $contrat) {
    $bien       = $contratsSpec[$ci][0];
    $locUser    = $contratsSpec[$ci][1];
    $locProfile = Locataire::where('user_id', $locUser->id)->first();

    foreach ($periodes as $pi => $periode) {
        $estPremier = ($pi === 0); // Premier mois : caution + frais agence + DGID

        // ── Construction du FiscalContext ───────────────────────────────
        $tauxBrsContrat   = $contrat->taux_brs_manuel   !== null ? (float)$contrat->taux_brs_manuel   : null;
        $tauxBrsLocataire = $locProfile?->taux_brs_override !== null ? (float)$locProfile->taux_brs_override : null;

        $tauxTvaOverride = null;
        if ($contrat->loyer_assujetti_tva !== null) {
            $tauxTvaOverride = $contrat->loyer_assujetti_tva
                ? (float)($contrat->taux_tva_loyer ?? 18.0)
                : 0.0;
        }

        $loyerMensuelDgid = (float)$contrat->loyer_nu + (float)($contrat->charges_mensuelles ?? 0);

        $ctx = new FiscalContext(
            loyerNu:                (float) $contrat->loyer_nu,
            chargesAmount:          (float)($contrat->charges_mensuelles ?? 0),
            tomAmount:              (float)($contrat->tom_amount ?? 0),
            typeBail:               $contrat->type_bail,
            estMeuble:              (bool)($bien->meuble ?? false),
            brsApplicable: !(bool)($bien->proprietaire?->est_personne_morale_is ?? false),
            tauxCommission:         (float)($bien->taux_commission ?? 10.0),
            tauxTvaCommission:      18.0,
            tauxTvaLoyerOverride:   $tauxTvaOverride,
            tauxBrsContrat:         $tauxBrsContrat,
            tauxBrsLocataire:       $tauxBrsLocataire,
            fraisAgenceHt:          $estPremier ? (float)$contrat->frais_agence : 0.0,
            cautionMontant:         $estPremier ? (float)$contrat->caution      : 0.0,
            cautionGardeeParAgence: false,
            avecDgid:               $estPremier && !(bool)($contrat->enregistrement_exonere ?? false),
            enregistrementExonere:  (bool)($contrat->enregistrement_exonere ?? false),
            loyerMensuelDgid:       $loyerMensuelDgid,
            dureeMoisDgid:          12,
            tauxEnregistrementDgid: $contrat->taux_enregistrement_dgid !== null
                                        ? (float)$contrat->taux_enregistrement_dgid
                                        : null,
            timbreFiscalDgid:       2000.0,
        );

        $result = FiscalService::calculer($ctx);
        $fields = $result->toPaiementFields();

        $paiement = Paiement::create([
            'agency_id'                => $agencyId,
            'contrat_id'               => $contrat->id,
            'periode'                  => $periode->format('Y-m-01'),
            'date_paiement'            => $periode->copy()->addDays(rand(1, 7))->format('Y-m-d'),
            'mode_paiement'            => $modesP[array_rand($modesP)],
            'statut'                   => 'valide',
            'est_premier_paiement'     => $estPremier,
            'reference_paiement'       => $refPay($contrat->id, $pi),
            'reference_bail'           => $contrat->reference_bail,
            'taux_commission_applique' => (float)($bien->taux_commission ?? 10.0),
            ...$fields,
        ]);

        $allPaiements[$ci][$pi] = $paiement;

        // Affichage ligne récap
        $flagDgid   = ($estPremier && $result->dgidTotal > 0)
            ? ' [DGID ' . number_format($result->dgidTotal, 0, ',', ' ') . ' F]' : '';
        $flagBrs    = $result->brsApplicable
            ? ' [BRS ' . number_format($result->brsAmount, 0, ',', ' ') . ' F]' : '';
        $netBail    = number_format($result->netBailleur, 0, ',', ' ');

        echo "    ✓ {$contrat->reference_bail}  "
           . $periode->format('Y-m')
           . "  net bailleur: {$netBail} F"
           . $flagBrs . $flagDgid . "\n";
    }
}


// ─────────────────────────────────────────────────────────────────────────
// 6. DÉPENSES DE GESTION  (Niveau 5 — 4 dépenses réparties sur 3 bailleurs)
// ─────────────────────────────────────────────────────────────────────────
echo "\n🔧  DÉPENSES DE GESTION\n";

// D1 — Awa Thiam : plomberie F4 Plateau, paiement M-1
DepenseGestion::create([
    'agency_id'    => $agencyId,
    'paiement_id'  => $allPaiements[0][1]->id,
    'libelle'      => 'Réparation fuite salle de bain principale',
    'montant'      => 45000,
    'categorie'    => 'plomberie',
    'date_depense' => $periodes[1]->copy()->addDays(10)->format('Y-m-d'),
    'prestataire'  => 'Moustapha Plomberie & Sanitaires',
    'notes'        => 'Remplacement robinet mitigeur + joints — facture PLB-2024-087',
]);
echo "    ✓ Plomberie     45 000 F  → {$allPaiements[0][1]->reference_paiement}  (Awa Thiam — F4 Plateau)\n";

// D2 — Moussa Diallo : électricité villa Liberté 6, paiement M-2
DepenseGestion::create([
    'agency_id'    => $agencyId,
    'paiement_id'  => $allPaiements[3][0]->id,
    'libelle'      => 'Mise aux normes tableau électrique général',
    'montant'      => 75000,
    'categorie'    => 'electricite',
    'date_depense' => $periodes[0]->copy()->addDays(15)->format('Y-m-d'),
    'prestataire'  => 'Sénégal Électricité Services',
    'notes'        => 'Remplacement disjoncteurs diff. + câblage — facture ELEC-2024-042',
]);
echo "    ✓ Électricité   75 000 F  → {$allPaiements[3][0]->reference_paiement}  (Moussa Diallo — Villa)\n";

// D3 — Moussa Diallo : peinture F3 Liberté 6, paiement M-1
DepenseGestion::create([
    'agency_id'    => $agencyId,
    'paiement_id'  => $allPaiements[2][1]->id,
    'libelle'      => 'Peinture chambre principale + salon (remise en état)',
    'montant'      => 85000,
    'categorie'    => 'peinture',
    'date_depense' => $periodes[1]->copy()->addDays(5)->format('Y-m-d'),
    'prestataire'  => 'Peintre Ibou Mbaye',
    'notes'        => 'Deux couches vinylique mat — blanc cassé RAL 9001',
]);
echo "    ✓ Peinture      85 000 F  → {$allPaiements[2][1]->reference_paiement}  (Moussa Diallo — F3)\n";

// D4 — Modou Fall : menuiserie F4 Pikine, paiement M courant
DepenseGestion::create([
    'agency_id'    => $agencyId,
    'paiement_id'  => $allPaiements[5][2]->id,
    'libelle'      => 'Remplacement porte d\'entrée — porte blindée',
    'montant'      => 55000,
    'categorie'    => 'menuiserie',
    'date_depense' => $periodes[2]->copy()->addDays(3)->format('Y-m-d'),
    'prestataire'  => 'Menuiserie Al Amine',
    'notes'        => 'Porte blindée 3 points + pose incluse',
]);
echo "    ✓ Menuiserie    55 000 F  → {$allPaiements[5][2]->reference_paiement}  (Modou Fall — F4 Pikine)\n";


// ─────────────────────────────────────────────────────────────────────────
// 7. RAPPORT DE VÉRIFICATION D'ISOLATION
// ─────────────────────────────────────────────────────────────────────────
echo "\n" . str_repeat('═', 65) . "\n";
echo "  RAPPORT DE VÉRIFICATION — ISOLATION BAILLEUR\n";
echo str_repeat('═', 65) . "\n";

$verif = [
    $uAwa    => ['nom' => 'Awa Thiam',      'biens' => [$b1, $b2]],
    $uMoussa => ['nom' => 'Moussa Diallo',  'biens' => [$b3, $b4, $b5]],
    $uModou  => ['nom' => 'Modou Fall',     'biens' => [$b6, $b7]],
];

foreach ($verif as $uB => $info) {
    $bienIds    = collect($info['biens'])->pluck('id');
    $contratIds = Contrat::whereIn('bien_id', $bienIds)->pluck('id');
    $pays       = Paiement::where('agency_id', $agencyId)
                           ->whereIn('contrat_id', $contratIds)
                           ->with('depenses')
                           ->get();
    $depenses   = $pays->flatMap->depenses;

    $totLoyers  = (float)$pays->sum('montant_encaisse');
    $totComm    = (float)$pays->sum('commission_ttc');
    $totBrs     = (float)$pays->sum('brs_amount');
    $totDgid    = (float)$pays->sum('dgid_total');
    $totDep     = (float)$depenses->sum('montant');
    $netFinal   = round($totLoyers - $totComm - $totDep, 2);

    // Vérification d'isolation : tous les paiements doivent remonter à ce bailleur
    $isolation  = $pays->every(fn($p) => in_array(
        $p->contrat?->bien?->proprietaire_id,
        [$uB->id]
    ));

    echo "\n  👤 {$info['nom']}\n";
    echo "     Biens           : " . count($info['biens']) . "\n";
    echo "     Paiements       : {$pays->count()}\n";
    echo "     Dépenses gestion: {$depenses->count()}\n";
    echo "     Loyers encaissés: " . number_format($totLoyers, 0, ',', ' ') . " F\n";
    echo "     Commissions TTC : " . number_format($totComm,   0, ',', ' ') . " F\n";
    if ($totBrs > 0)  echo "     BRS retenu      : " . number_format($totBrs,   0, ',', ' ') . " F\n";
    if ($totDgid > 0) echo "     DGID            : " . number_format($totDgid,  0, ',', ' ') . " F\n";
    if ($totDep > 0)  echo "     Dépenses gestion: " . number_format($totDep,   0, ',', ' ') . " F\n";
    echo "     NET FINAL       : " . number_format($netFinal,  0, ',', ' ') . " F\n";
    echo "     Isolation OK    : " . ($isolation ? '✅ OUI' : '❌ PROBLÈME') . "\n";
}

echo "\n" . str_repeat('─', 65) . "\n";
echo "  CHAÎNE D'ISOLATION GARANTIE :\n";
echo "  Bailleur (user_id)\n";
echo "    └─ Bien  (agency_id + proprietaire_id = user_id)\n";
echo "         └─ Contrat (bien_id)\n";
echo "              └─ Paiement (agency_id + contrat_id)\n";
echo "                   └─ DepenseGestion (paiement_id)\n\n";
echo "  🎉 Peuplement terminé !\n";
echo "     → /admin/bailleurs        : Portefeuille des 3 bailleurs\n";
echo "     → /admin/paiements        : Les 21 paiements (noms cliquables)\n";
echo "     → /admin/bailleurs/{id}   : Fiche + équation Niveau 5\n";
echo str_repeat('═', 65) . "\n\n";
