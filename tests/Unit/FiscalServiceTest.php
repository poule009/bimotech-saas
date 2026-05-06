<?php

namespace Tests\Unit;

use App\Services\FiscalContext;
use App\Services\FiscalService;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * FiscalServiceTest — Vérifie les règles BRS et TVA du moteur fiscal.
 *
 * Références légales testées :
 *  - BRS seuil 150 000 F   : CGI Art. 200 §4
 *  - BRS taux 5%           : CGI Art. 201 §3
 *  - Override prime seuil  : priorité contractuelle sur le droit commun
 *  - TVA commission 18%    : CGI Art. 369
 *  - TVA loyer habitation  : CGI Art. 355 (exonération)
 *  - TVA loyer commercial  : CGI Art. 355 (assujettissement)
 */
class FiscalServiceTest extends TestCase
{
    // ── Constructeur FiscalContext minimal réutilisable ──────────────────────

    private function makeCtx(array $overrides = []): FiscalContext
    {
        $defaults = [
            'loyerNu'              => 200_000.0,
            'chargesAmount'        => 0.0,
            'tomAmount'            => 0.0,
            'typeBail'             => 'habitation',
            'estMeuble'            => false,
            'brsApplicable'        => true,
            'tauxCommission'       => 10.0,
            'tauxTvaCommission'    => 18.0,
            'tauxTvaLoyerOverride' => null,
            'tauxBrsContrat'       => null,
            'tauxBrsLocataire'     => null,
        ];

        $p = array_merge($defaults, $overrides);

        return new FiscalContext(
            loyerNu:              $p['loyerNu'],
            chargesAmount:        $p['chargesAmount'],
            tomAmount:            $p['tomAmount'],
            typeBail:             $p['typeBail'],
            estMeuble:            $p['estMeuble'],
            brsApplicable:        $p['brsApplicable'],
            tauxCommission:       $p['tauxCommission'],
            tauxTvaCommission:    $p['tauxTvaCommission'],
            tauxTvaLoyerOverride: $p['tauxTvaLoyerOverride'],
            tauxBrsContrat:       $p['tauxBrsContrat'],
            tauxBrsLocataire:     $p['tauxBrsLocataire'],
        );
    }

    // ════════════════════════════════════════════════════════════════════════
    // BRS — Seuil Art. 200 §4 CGI SN
    // ════════════════════════════════════════════════════════════════════════

    #[Test]
    public function test_brs_non_applicable_sous_seuil_150k(): void
    {
        // Loyer 100 000 F < seuil 150 000 F — brsApplicable=true dans le context
        // mais sans override → la retenue ne doit pas s'appliquer (Art. 200 §4)
        $ctx    = $this->makeCtx(['loyerNu' => 100_000.0, 'brsApplicable' => true]);
        $result = FiscalService::calculer($ctx);

        $this->assertEquals(0.0, $result->brsAmount, 'BRS doit être 0 si loyer mensuel < 150 000 F sans override');
        $this->assertFalse($result->brsApplicable, 'brsApplicable doit être false après application du seuil légal');
    }

    #[Test]
    public function test_brs_applicable_au_dessus_seuil(): void
    {
        // Loyer 200 000 F > seuil → BRS = 5% × 200 000 = 10 000 F
        $ctx    = $this->makeCtx(['loyerNu' => 200_000.0, 'brsApplicable' => true]);
        $result = FiscalService::calculer($ctx);

        $this->assertEquals(10_000.0, $result->brsAmount, 'BRS doit être 5% × 200 000 = 10 000 F');
        $this->assertTrue($result->brsApplicable);
        $this->assertEquals(5.0, $result->tauxBrsApplique);
    }

    #[Test]
    public function test_brs_override_prime_sur_seuil(): void
    {
        // Loyer 80 000 F < seuil MAIS override contrat 5% → BRS = 5% × 80 000 = 4 000 F
        // L'override manuel prime sur le seuil légal (cas contractuel validé DGI)
        $ctx    = $this->makeCtx([
            'loyerNu'        => 80_000.0,
            'brsApplicable'  => true,
            'tauxBrsContrat' => 5.0,
        ]);
        $result = FiscalService::calculer($ctx);

        $this->assertEquals(4_000.0, $result->brsAmount, "Override 5% doit primer sur le seuil — BRS = 4 000 F");
        $this->assertTrue($result->brsApplicable);
    }

    // ════════════════════════════════════════════════════════════════════════
    // TVA Commission — CGI Art. 369
    // ════════════════════════════════════════════════════════════════════════

    #[Test]
    public function test_tva_commission_18_pourcent(): void
    {
        // Commission HT = 10% × 500 000 = 50 000 F → TVA = 18% × 50 000 = 9 000 F
        $ctx    = $this->makeCtx(['loyerNu' => 500_000.0, 'brsApplicable' => false]);
        $result = FiscalService::calculer($ctx);

        $this->assertEquals(50_000.0, $result->commissionHt,   'Commission HT = 10% × 500 000 = 50 000 F');
        $this->assertEquals(9_000.0,  $result->tvaCommission,  'TVA commission = 18% × 50 000 = 9 000 F');
    }

    // ════════════════════════════════════════════════════════════════════════
    // TVA Loyer — CGI Art. 355
    // ════════════════════════════════════════════════════════════════════════

    #[Test]
    public function test_loyer_habitation_nu_exonere_tva(): void
    {
        // Bail habitation non meublé → exonéré TVA (Art. 355 CGI SN)
        $ctx    = $this->makeCtx([
            'typeBail'   => 'habitation',
            'estMeuble'  => false,
            'brsApplicable' => false,
        ]);
        $result = FiscalService::calculer($ctx);

        $this->assertEquals(0.0, $result->tvaLoyer,        'TVA loyer = 0 pour habitation non meublée');
        $this->assertFalse($result->loyerAssujetti,         'loyerAssujetti = false pour habitation non meublée');
    }

    #[Test]
    public function test_loyer_commercial_assujetti_tva(): void
    {
        // Bail commercial → TVA 18% obligatoire (Art. 355 CGI SN)
        $loyerNu = 300_000.0;
        $ctx     = $this->makeCtx([
            'loyerNu'      => $loyerNu,
            'typeBail'     => 'commercial',
            'brsApplicable' => false,
        ]);
        $result = FiscalService::calculer($ctx);

        $this->assertTrue($result->loyerAssujetti,  'loyerAssujetti = true pour bail commercial');
        $this->assertEquals(
            round($loyerNu * 0.18, 2),
            $result->tvaLoyer,
            'TVA loyer = 18% × loyer HT pour bail commercial'
        );
    }
}
