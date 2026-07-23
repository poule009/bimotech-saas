<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Page d'accueil publique — la section « site web intelligent » (vitrine par
 * agence) doit être présentée entre le démarrage et les tarifs.
 */
class WelcomePageTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function laccueil_presente_la_section_site_web_intelligent(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('son site web — intelligent', false)
            ->assertSee('Toujours à jour, automatiquement', false)
            ->assertSee('agences/bimo-tech-yHw9By', false)   // lien « Voir un exemple réel »
            ->assertSee('Visible sur Google', false);
    }
}
