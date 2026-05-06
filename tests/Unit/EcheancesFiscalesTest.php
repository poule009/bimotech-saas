<?php

namespace Tests\Unit;

use App\Http\Controllers\Admin\EcheancesFiscalesController;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class EcheancesFiscalesTest extends TestCase
{
    // ── Helpers ─────────────────────────────────────────────────────────────

    private function labels(array $echeances): array
    {
        return array_column($echeances, 'label');
    }

    private function findByLabel(array $echeances, string $label): ?array
    {
        foreach ($echeances as $e) {
            if ($e['label'] === $label) {
                return $e;
            }
        }
        return null;
    }

    // ── Tests CEL ────────────────────────────────────────────────────────────

    public function test_cel_vl_dans_echeances_toutes_agences(): void
    {
        // Une EI doit quand même avoir CEL-VL — les agences immo relèvent de la CEL sans exception
        $echeances = EcheancesFiscalesController::buildEcheances('ei', Carbon::parse('2026-01-05'));

        $this->assertContains('CEL-VL (Valeur Locative)', $this->labels($echeances));
    }

    // ── Tests IS / forme juridique ───────────────────────────────────────────

    public function test_is_absent_pour_entreprise_individuelle(): void
    {
        $echeances = EcheancesFiscalesController::buildEcheances('ei', Carbon::parse('2026-01-05'));
        $labels    = $this->labels($echeances);

        $this->assertNotContains('IS — 1er acompte',  $labels);
        $this->assertNotContains('IS — 2ème acompte', $labels);
        $this->assertNotContains('IS — solde + IMF',  $labels);
    }

    public function test_is_present_pour_sarl(): void
    {
        $echeances = EcheancesFiscalesController::buildEcheances('sarl', Carbon::parse('2026-01-05'));
        $labels    = $this->labels($echeances);

        $this->assertContains('IS — 1er acompte',  $labels);
        $this->assertContains('IS — 2ème acompte', $labels);
        $this->assertContains('IS — solde + IMF',  $labels);
    }

    // ── Tests statuts ────────────────────────────────────────────────────────

    public function test_statut_urgent_dans_7_jours(): void
    {
        // 2026-01-25 → CEL-VL le 31 jan → 6 jours → urgent
        $echeances = EcheancesFiscalesController::buildEcheances('sarl', Carbon::parse('2026-01-25'));
        $celVl     = $this->findByLabel($echeances, 'CEL-VL (Valeur Locative)');

        $this->assertNotNull($celVl, 'CEL-VL doit être présente');
        $this->assertSame('urgent', $celVl['statut']);
    }

    public function test_statut_bientot_dans_30_jours(): void
    {
        // 2026-01-05 → CEL-VL le 31 jan → 26 jours → bientot
        $echeances = EcheancesFiscalesController::buildEcheances('sarl', Carbon::parse('2026-01-05'));
        $celVl     = $this->findByLabel($echeances, 'CEL-VL (Valeur Locative)');

        $this->assertNotNull($celVl, 'CEL-VL doit être présente');
        $this->assertSame('bientot', $celVl['statut']);
    }

    public function test_statut_a_venir_au_dela_30_jours(): void
    {
        // 2025-11-01 → CEL-VL jan 2025 passée → prochain = 2026-01-31 → 91 jours → a_venir
        $echeances = EcheancesFiscalesController::buildEcheances('sarl', Carbon::parse('2025-11-01'));
        $celVl     = $this->findByLabel($echeances, 'CEL-VL (Valeur Locative)');

        $this->assertNotNull($celVl, 'CEL-VL doit être présente');
        $this->assertSame('a_venir', $celVl['statut']);
        $this->assertSame('2026-01-31', $celVl['date']->format('Y-m-d'));
    }
}
