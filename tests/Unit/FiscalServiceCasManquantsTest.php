<?php

namespace Tests\Unit;

use App\Services\FiscalContext;
use App\Services\FiscalService;
use Carbon\Carbon;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Cas critiques non couverts par FiscalServiceTest.php existant.
 *
 * Références légales :
 *  - Prorata          : jours occupés / jours du mois
 *  - TOM + TVA        : CGI art. 364 §2a — TOM inclus dans l'assiette TVA
 *  - Charges TVA      : CGI art. 364 + 369 — forfait bail commercial → TVA obligatoire
 *  - DGID             : CGI art. 464 B + 472 IV.6 — 2% × (loyer × durée) + timbre
 *  - Premier paiement : frais agence HT + TVA + caution
 *  - Caution séquestre: CGI — agence garde la caution → exclue du net bailleur
 *  - BRS personne morale: Art. 201 §2 — PM IS → pas de BRS
 */
class FiscalServiceCasManquantsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['features.fiscalite' => true]);
    }

    private function ctx(array $params): FiscalContext
    {
        return new FiscalContext(
            loyerNu:              $params['loyerNu']              ?? 200_000.0,
            chargesAmount:        $params['chargesAmount']        ?? 0.0,
            tomAmount:            $params['tomAmount']            ?? 0.0,
            typeBail:             $params['typeBail']             ?? 'habitation',
            estMeuble:            $params['estMeuble']            ?? false,
            brsApplicable:        $params['brsApplicable']        ?? false,
            tauxCommission:       $params['tauxCommission']       ?? 10.0,
            tauxTvaCommission:    $params['tauxTvaCommission']    ?? 18.0,
            tauxTvaLoyerOverride: $params['tauxTvaLoyerOverride'] ?? null,
            tauxBrsContrat:       $params['tauxBrsContrat']       ?? null,
            tauxBrsLocataire:     $params['tauxBrsLocataire']     ?? null,
            dateDebutOccupation:  $params['dateDebutOccupation']  ?? null,
            dateFinPeriode:       $params['dateFinPeriode']       ?? null,
            fraisAgenceHt:        $params['fraisAgenceHt']        ?? 0.0,
            cautionMontant:       $params['cautionMontant']       ?? 0.0,
            chargesAssujettiesATva: $params['chargesAssujettiesATva'] ?? false,
            cautionGardeeParAgence: $params['cautionGardeeParAgence'] ?? false,
            avecDgid:             $params['avecDgid']             ?? false,
            enregistrementExonere: $params['enregistrementExonere'] ?? false,
            loyerMensuelDgid:     $params['loyerMensuelDgid']     ?? 0.0,
            dureeMoisDgid:        $params['dureeMoisDgid']        ?? 12,
        );
    }

    // ════════════════════════════════════════════════════════════════════════
    // PRORATA TEMPOREL
    // ════════════════════════════════════════════════════════════════════════

    #[Test]
    public function prorata_entree_le_16_avril_donne_coefficient_15_sur_30(): void
    {
        // Entrée le 16 avril → 15 jours occupés (16→30) / 30 jours = 0.5
        $debut = Carbon::create(2025, 4, 16);
        $fin   = Carbon::create(2025, 4, 30)->endOfDay();

        $ctx = $this->ctx([
            'loyerNu'             => 200_000.0,
            'dateDebutOccupation' => $debut,
            'dateFinPeriode'      => $fin,
        ]);

        $coeff = $ctx->coefficientProrata();
        $this->assertEqualsWithDelta(0.5, $coeff, 0.01, 'Entrée le 16 avril = 15/30 = 0.5');

        $result = FiscalService::calculer($ctx);
        $this->assertEqualsWithDelta(100_000.0, $result->loyerHt, 1.0,
            'Loyer proratisé = 200 000 × 0.5 = 100 000 F');
    }

    #[Test]
    public function prorata_entree_le_1er_donne_coefficient_1(): void
    {
        // Entrée le 1er → mois complet, coefficient = 1.0
        $ctx = $this->ctx(['loyerNu' => 200_000.0]);
        $this->assertEquals(1.0, $ctx->coefficientProrata());

        $result = FiscalService::calculer($ctx);
        $this->assertEquals(200_000.0, $result->loyerHt);
    }

    #[Test]
    public function prorata_date_debut_posterieure_a_fin_leve_exception(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $ctx = $this->ctx([
            'dateDebutOccupation' => Carbon::create(2025, 5, 1),
            'dateFinPeriode'      => Carbon::create(2025, 4, 30),
        ]);
        $ctx->coefficientProrata();
    }

    #[Test]
    public function prorata_loyer_charges_tom_proratises_ensemble(): void
    {
        // Entrée le 16 → coeff 0.5 — les 3 composantes doivent être proratisées
        $debut = Carbon::create(2025, 4, 16);
        $fin   = Carbon::create(2025, 4, 30)->endOfDay();

        $ctx = $this->ctx([
            'loyerNu'             => 200_000.0,
            'chargesAmount'       => 20_000.0,
            'tomAmount'           => 5_000.0,
            'dateDebutOccupation' => $debut,
            'dateFinPeriode'      => $fin,
        ]);

        $result = FiscalService::calculer($ctx);

        $this->assertEqualsWithDelta(100_000.0, $result->loyerHt,      1.0, 'Loyer proratisé');
        $this->assertEqualsWithDelta(10_000.0,  $result->chargesAmount, 1.0, 'Charges proratisées');
        $this->assertEqualsWithDelta(2_500.0,   $result->tomAmount,     1.0, 'TOM proratisé');
    }

    // ════════════════════════════════════════════════════════════════════════
    // TOM DANS L'ASSIETTE TVA — CGI art. 364 §2a
    // ════════════════════════════════════════════════════════════════════════

    #[Test]
    public function tva_loyer_commercial_inclut_tom_dans_assiette(): void
    {
        // Art. 364 §2a : TVA = (loyer_ht + TOM) × 18%
        // Loyer = 200 000, TOM = 10 000 → assiette TVA = 210 000
        $ctx = $this->ctx([
            'loyerNu'  => 200_000.0,
            'tomAmount' => 10_000.0,
            'typeBail' => 'commercial',
        ]);

        $result = FiscalService::calculer($ctx);

        $tvaAttendue = round((200_000 + 10_000) * 0.18, 2); // 37 800
        $this->assertEqualsWithDelta($tvaAttendue, $result->tvaLoyer, 1.0,
            'TVA doit être calculée sur loyer_ht + TOM (Art. 364 §2a)');
    }

    #[Test]
    public function tva_habitation_tom_non_inclus_dans_tva(): void
    {
        // Habitation non meublée → TVA loyer = 0 ; TOM présent mais pas de TVA
        $ctx = $this->ctx([
            'loyerNu'  => 200_000.0,
            'tomAmount' => 10_000.0,
            'typeBail' => 'habitation',
            'estMeuble' => false,
        ]);

        $result = FiscalService::calculer($ctx);

        $this->assertEquals(0.0, $result->tvaLoyer, 'Pas de TVA loyer en habitation non meublée');
        $this->assertEquals(10_000.0, $result->tomAmount, 'TOM présent dans le résultat');
    }

    // ════════════════════════════════════════════════════════════════════════
    // CHARGES ASSUJETTIES TVA — CGI art. 364 + 369
    // ════════════════════════════════════════════════════════════════════════

    #[Test]
    public function charges_commercial_assujetties_tva_18_pourcent(): void
    {
        // Bail commercial avec charges en forfait → TVA 18% sur les charges
        $ctx = $this->ctx([
            'loyerNu'               => 300_000.0,
            'chargesAmount'         => 50_000.0,
            'typeBail'              => 'commercial',
            'chargesAssujettiesATva' => true,
            'tauxTvaLoyerOverride'  => 18.0, // force TVA pour simplifier le test
        ]);

        $result = FiscalService::calculer($ctx);

        $tvaChargesAttendue = round(50_000 * 0.18, 2); // 9 000
        $this->assertEqualsWithDelta($tvaChargesAttendue, $result->tvaCharges, 1.0,
            'TVA charges = 18% × 50 000 = 9 000 F');
        $this->assertEqualsWithDelta(59_000.0, $result->chargesTtc, 1.0,
            'Charges TTC = 50 000 + 9 000 = 59 000 F');
    }

    #[Test]
    public function charges_habitation_non_assujetties_tva(): void
    {
        // Bail habitation → charges en débours → pas de TVA
        $ctx = $this->ctx([
            'loyerNu'               => 200_000.0,
            'chargesAmount'         => 30_000.0,
            'typeBail'              => 'habitation',
            'chargesAssujettiesATva' => false,
        ]);

        $result = FiscalService::calculer($ctx);

        $this->assertEquals(0.0, $result->tvaCharges, 'Pas de TVA sur charges en habitation');
        $this->assertEquals(30_000.0, $result->chargesTtc, 'Charges TTC = montant HT si pas de TVA');
    }

    // ════════════════════════════════════════════════════════════════════════
    // PREMIER PAIEMENT — Frais agence + caution
    // ════════════════════════════════════════════════════════════════════════

    #[Test]
    public function premier_paiement_avec_frais_agence_et_caution(): void
    {
        // Premier paiement : frais agence HT = 50 000 → TVA = 9 000 → TTC = 59 000
        // Caution = 200 000 → total entrée = loyer + frais TTC + caution
        $ctx = $this->ctx([
            'loyerNu'       => 200_000.0,
            'fraisAgenceHt' => 50_000.0,
            'cautionMontant' => 200_000.0,
        ]);

        $result = FiscalService::calculer($ctx);

        $this->assertEquals(50_000.0, $result->fraisAgenceHt);
        $this->assertEqualsWithDelta(9_000.0,  $result->tvaFraisAgence, 1.0, 'TVA frais = 18% × 50 000 = 9 000');
        $this->assertEqualsWithDelta(59_000.0, $result->fraisAgenceTtc, 1.0, 'Frais TTC = 59 000');
        $this->assertEquals(200_000.0, $result->cautionMontant);

        // Total entrée = montant_encaisse + frais_ttc + caution
        $totalAttendu = $result->montantEncaisse + 59_000.0 + 200_000.0;
        $this->assertEqualsWithDelta($totalAttendu, $result->totalEncaissementInitial, 1.0);
    }

    #[Test]
    public function paiement_recurrent_zero_frais_caution(): void
    {
        // Paiement récurrent → frais et caution doivent être 0
        $ctx = $this->ctx([
            'loyerNu'        => 200_000.0,
            'fraisAgenceHt'  => 0.0,
            'cautionMontant' => 0.0,
        ]);

        $result = FiscalService::calculer($ctx);

        $this->assertEquals(0.0, $result->fraisAgenceHt);
        $this->assertEquals(0.0, $result->tvaFraisAgence);
        $this->assertEquals(0.0, $result->cautionMontant);
        $this->assertEquals($result->montantEncaisse, $result->totalEncaissementInitial);
    }

    // ════════════════════════════════════════════════════════════════════════
    // CAUTION GARDÉE EN SÉQUESTRE PAR L'AGENCE
    // ════════════════════════════════════════════════════════════════════════

    #[Test]
    public function caution_gardee_par_agence_exclue_du_net_bailleur(): void
    {
        $ctx = $this->ctx([
            'loyerNu'                => 200_000.0,
            'cautionMontant'         => 200_000.0,
            'cautionGardeeParAgence' => true,
        ]);

        $result = FiscalService::calculer($ctx);

        // netBailleur = netAVerser uniquement (caution exclue)
        $this->assertEqualsWithDelta(
            $result->netAVerserProprietaire,
            $result->netBailleur,
            1.0,
            'Caution en séquestre → exclue du versement bailleur'
        );
    }

    #[Test]
    public function caution_remise_au_proprietaire_incluse_dans_net_bailleur(): void
    {
        $ctx = $this->ctx([
            'loyerNu'                => 200_000.0,
            'cautionMontant'         => 200_000.0,
            'cautionGardeeParAgence' => false, // caution remise au bailleur
        ]);

        $result = FiscalService::calculer($ctx);

        $this->assertEqualsWithDelta(
            $result->netAVerserProprietaire + 200_000.0,
            $result->netBailleur,
            1.0,
            'Caution remise → incluse dans net bailleur'
        );
    }

    // ════════════════════════════════════════════════════════════════════════
    // DROITS DGID — CGI art. 464 B + 472 IV.6
    // ════════════════════════════════════════════════════════════════════════

    #[Test]
    public function dgid_loyer_250k_12_mois_taux_2_pourcent(): void
    {
        // Scénario documenté dans FiscalService::calculerDroitsBail() :
        // loyer=250 000, durée=12, taux=2% → assiette=3 000 000, droits=60 000, total=62 000
        $result = FiscalService::calculerDroitsBail(250_000, 12, 2.0, 2_000);

        $this->assertEquals(3_000_000.0, $result['base_annuelle']);
        $this->assertEquals(60_000.0,    $result['droits_enregistrement']);
        $this->assertEquals(2_000.0,     $result['timbre_fiscal']);
        $this->assertEquals(62_000.0,    $result['total_dgid']);
    }

    #[Test]
    public function dgid_premier_paiement_avec_calcul_integre(): void
    {
        // Premier paiement avec DGID actif → les droits sont calculés
        $ctx = $this->ctx([
            'loyerNu'        => 300_000.0,
            'avecDgid'       => true,
            'loyerMensuelDgid' => 300_000.0,
            'dureeMoisDgid'  => 12,
        ]);

        $result = FiscalService::calculer($ctx);

        // 300 000 × 12 × 2% = 72 000 + 2 000 timbre = 74 000
        $this->assertGreaterThan(0.0, $result->dgidTotal, 'DGID doit être > 0 au premier paiement');
        $this->assertEquals(2_000.0, $result->dgidTimbreFiscal, 'Timbre fiscal = 2 000 F');
    }

    #[Test]
    public function dgid_exonere_retourne_zero(): void
    {
        $ctx = $this->ctx([
            'loyerNu'              => 300_000.0,
            'avecDgid'             => true,
            'enregistrementExonere' => true, // exonéré
            'loyerMensuelDgid'     => 300_000.0,
            'dureeMoisDgid'        => 12,
        ]);

        $result = FiscalService::calculer($ctx);

        $this->assertEquals(0.0, $result->dgidTotal,              'DGID = 0 si exonéré');
        $this->assertEquals(0.0, $result->dgidDroitsEnregistrement);
        $this->assertEquals(0.0, $result->dgidTimbreFiscal);
    }

    #[Test]
    public function dgid_paiement_recurrent_toujours_zero(): void
    {
        // Paiements récurrents → avecDgid=false → DGID = 0
        $ctx = $this->ctx([
            'loyerNu'  => 300_000.0,
            'avecDgid' => false,
        ]);

        $result = FiscalService::calculer($ctx);

        $this->assertEquals(0.0, $result->dgidTotal);
        $this->assertEquals(0.0, $result->dgidDroitsEnregistrement);
    }

    #[Test]
    public function dgid_parametre_invalide_leve_exception(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        FiscalService::calculerDroitsBail(-100_000, 12, 2.0);
    }

    // ════════════════════════════════════════════════════════════════════════
    // BRS — PERSONNE MORALE (Art. 201 §2 CGI SN)
    // ════════════════════════════════════════════════════════════════════════

    #[Test]
    public function brs_non_applicable_si_bailleur_personne_morale(): void
    {
        // Art. 201 §2 : BRS ne s'applique PAS si bailleur est personne morale IS
        $ctx = $this->ctx([
            'loyerNu'      => 500_000.0,
            'typeBail'     => 'commercial',
            'brsApplicable' => false, // PM IS → pas de BRS
        ]);

        $result = FiscalService::calculer($ctx);

        $this->assertEquals(0.0,   $result->brsAmount);
        $this->assertFalse($result->brsApplicable);
        $this->assertEquals(0.0,   $result->tauxBrsApplique);
        $this->assertEquals('commercial', $result->regimeFiscal, 'Pas de _avec_brs si PM');
    }

    // ════════════════════════════════════════════════════════════════════════
    // CGF — Contribution Globale Foncière (Art. 77-94 CGI SN)
    // ════════════════════════════════════════════════════════════════════════

    #[Test]
    public function cgf_tranche_1_revenus_inferieurs_2m(): void
    {
        // Tranche 4% : revenus entre 0 et 2 000 000
        $result = FiscalService::calculerCGF(1_500_000);

        $this->assertTrue($result['applicable']);
        $this->assertEquals(4.0,    $result['taux_applique']);
        $this->assertEqualsWithDelta(60_000.0, $result['montant'], 1.0,
            '1 500 000 × 4% = 60 000 F');
    }

    #[Test]
    public function cgf_non_applicable_au_dessus_30m(): void
    {
        $result = FiscalService::calculerCGF(35_000_000);

        $this->assertFalse($result['applicable']);
        $this->assertEquals(0.0, $result['montant']);
    }

    #[Test]
    public function cgf_tranche_3_revenus_entre_5m_et_10m(): void
    {
        $result = FiscalService::calculerCGF(7_000_000);

        $this->assertTrue($result['applicable']);
        $this->assertEquals(9.0, $result['taux_applique'],
            'Tranche 9% pour revenus entre 5 001 000 et 10 000 000 F');
    }

    // ════════════════════════════════════════════════════════════════════════
    // INVARIANTS COMPTABLES — Cohérence globale du calcul
    // ════════════════════════════════════════════════════════════════════════

    #[Test]
    public function invariant_net_proprio_egal_encaisse_moins_commission(): void
    {
        // Règle absolue : net_proprietaire = montant_encaisse - commission_ttc
        foreach (['habitation', 'commercial', 'mixte'] as $type) {
            $ctx = $this->ctx([
                'loyerNu'  => 350_000.0,
                'typeBail' => $type,
            ]);
            $result = FiscalService::calculer($ctx);

            $this->assertEqualsWithDelta(
                $result->montantEncaisse - $result->commissionTtc,
                $result->netProprietaire,
                1.0,
                "Invariant net_proprio pour bail {$type}"
            );
        }
    }

    #[Test]
    public function invariant_net_a_verser_egal_net_proprio_moins_brs(): void
    {
        // Règle absolue : net_a_verser = net_proprietaire - brs_amount
        $ctx = $this->ctx([
            'loyerNu'      => 300_000.0,
            'typeBail'     => 'commercial',
            'brsApplicable' => true,
        ]);

        $result = FiscalService::calculer($ctx);

        $this->assertEqualsWithDelta(
            $result->netProprietaire - $result->brsAmount,
            $result->netAVerserProprietaire,
            1.0,
            'net_a_verser = net_proprietaire - brs_amount'
        );
    }

    #[Test]
    public function invariant_commission_calculee_sur_loyer_ht_uniquement(): void
    {
        // Commission = taux × loyer_ht UNIQUEMENT (pas sur TVA, charges, TOM)
        $loyerHt   = 300_000.0;
        $charges   = 50_000.0;
        $tom       = 10_000.0;
        $taux      = 10.0;

        $ctx = $this->ctx([
            'loyerNu'      => $loyerHt,
            'chargesAmount' => $charges,
            'tomAmount'     => $tom,
            'typeBail'      => 'habitation',
        ]);

        $result = FiscalService::calculer($ctx);

        $commissionAttendue = round($loyerHt * $taux / 100, 2); // 30 000
        $this->assertEqualsWithDelta($commissionAttendue, $result->commissionHt, 1.0,
            'Commission = taux × loyer_ht uniquement (pas sur charges ni TOM)');
    }
}
