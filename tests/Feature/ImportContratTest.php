<?php

namespace Tests\Feature;

use App\Models\Agency;
use App\Models\Bien;
use App\Models\Contrat;
use App\Models\ImportBatch;
use App\Models\Locataire;
use App\Models\User;
use App\Services\Import\CodeSequencer;
use App\Services\Import\Handlers\ContratHandler;
use App\Services\Import\ImportConflictException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Import de contrats — garde-fous d'invariant « 1 contrat actif par bien / par locataire »,
 * que la saisie manuelle impose (StoreContratRequest) et que l'import doit respecter aussi.
 */
class ImportContratTest extends TestCase
{
    use RefreshDatabase;

    private Agency $agency;

    protected function setUp(): void
    {
        parent::setUp();
        $this->agency = Agency::factory()->create(['actif' => true]);
    }

    private function bien(string $code): Bien
    {
        $proprio = User::factory()->create(['role' => 'proprietaire', 'agency_id' => $this->agency->id]);

        return Bien::factory()->create([
            'agency_id'       => $this->agency->id,
            'proprietaire_id' => $proprio->id,
            'statut'          => 'disponible',
            'code_import'     => $code,
            'loyer_mensuel'   => 300000,
        ]);
    }

    private function locataire(string $code): User
    {
        $user = User::factory()->create(['role' => 'locataire', 'agency_id' => $this->agency->id]);
        Locataire::create(['user_id' => $user->id, 'code_import' => $code, 'type_locataire' => 'particulier']);

        return $user;
    }

    #[Test]
    public function limport_rejette_un_locataire_deja_sous_contrat_actif(): void
    {
        $bienLibre  = $this->bien('B-LIBRE');
        $bienAutre  = $this->bien('B-AUTRE');
        $locataire  = $this->locataire('L-1');

        // Le locataire a déjà un bail actif sur un autre bien.
        Contrat::factory()->create([
            'agency_id'    => $this->agency->id,
            'bien_id'      => $bienAutre->id,
            'locataire_id' => $locataire->id,
            'statut'       => 'actif',
        ]);

        $handler = new ContratHandler($this->agency->id);
        $ctx = [];
        $verdict = $handler->validate([
            'code_bien' => 'B-LIBRE', 'code_locataire' => 'L-1',
            'loyer' => '300000', 'date_debut' => '2025-01-01',
        ], 2, $ctx);

        $this->assertSame('error', $verdict['status']);
        $this->assertStringContainsString('contrat actif', $verdict['message']);
    }

    #[Test]
    public function limport_rejette_le_meme_locataire_deux_fois_dans_le_fichier(): void
    {
        $this->bien('B-LIBRE');
        $this->bien('B-LIBRE2');
        $this->locataire('L-1'); // libre

        $handler = new ContratHandler($this->agency->id);
        $ctx = [];

        $v1 = $handler->validate(['code_bien' => 'B-LIBRE',  'code_locataire' => 'L-1', 'loyer' => '300000', 'date_debut' => '2025-01-01'], 2, $ctx);
        $v2 = $handler->validate(['code_bien' => 'B-LIBRE2', 'code_locataire' => 'L-1', 'loyer' => '300000', 'date_debut' => '2025-01-01'], 3, $ctx);

        $this->assertSame('valid', $v1['status']);
        $this->assertSame('error', $v2['status']);
        $this->assertStringContainsString('contrat actif', $v2['message']);
    }

    #[Test]
    public function le_commit_annule_si_le_bien_a_ete_loue_entre_temps(): void
    {
        $bienLibre = $this->bien('B-LIBRE');
        $locataire = $this->locataire('L-1');
        $autreLoc  = $this->locataire('L-2');

        // Aperçu validé quand le bien était libre → data prête au commit.
        $data = [
            'bien_id'      => $bienLibre->id,
            'locataire_id' => $locataire->id,
            'loyer_nu'     => 300000,
            'date_debut'   => '2025-01-01',
        ];

        // ENTRE-TEMPS : le bien est loué normalement (autre locataire).
        Contrat::factory()->create([
            'agency_id'    => $this->agency->id,
            'bien_id'      => $bienLibre->id,
            'locataire_id' => $autreLoc->id,
            'statut'       => 'actif',
        ]);

        $batch = ImportBatch::create([
            'agency_id' => $this->agency->id, 'type' => 'contrats', 'statut' => 'preview',
            'rows' => [], 'nb_total' => 1,
        ]);

        $handler  = new ContratHandler($this->agency->id);
        $sequence = 1;

        $this->expectException(ImportConflictException::class);
        $handler->create($data, $batch, app(CodeSequencer::class), $sequence);
    }
}
