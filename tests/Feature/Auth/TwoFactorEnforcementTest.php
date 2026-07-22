<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Support\PlatformSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Vérifie que le réglage plateforme « 2FA obligatoire » est réellement APPLIQUÉ
 * aux comptes super-admin par le middleware Require2FA (le toggle n'était pas câblé).
 *
 * NB : le TestCase de base neutralise le réglage par défaut ; chaque test fixe
 * explicitement la valeur qu'il veut vérifier.
 */
class TwoFactorEnforcementTest extends TestCase
{
    use RefreshDatabase;

    private function superAdmin(array $attrs = []): User
    {
        return User::factory()->createOne(array_merge([
            'role'             => 'superadmin',
            'sa_est_principal' => true,
        ], $attrs));
    }

    #[Test]
    public function superadmin_sans_2fa_est_force_vers_le_setup_quand_obligatoire(): void
    {
        app(PlatformSettings::class)->set('securite_2fa_obligatoire', true);

        $this->actingAs($this->superAdmin())
            ->get(route('superadmin.dashboard'))
            ->assertRedirect(route('superadmin.2fa.setup'));
    }

    #[Test]
    public function superadmin_sans_2fa_passe_quand_le_2fa_nest_pas_obligatoire(): void
    {
        app(PlatformSettings::class)->set('securite_2fa_obligatoire', false);

        $this->actingAs($this->superAdmin())
            ->get(route('superadmin.dashboard'))
            ->assertOk();
    }

    #[Test]
    public function la_route_de_setup_reste_accessible_meme_sans_2fa(): void
    {
        // Sans exemption, forcer le setup bouclerait (le setup est dans le groupe require2fa).
        app(PlatformSettings::class)->set('securite_2fa_obligatoire', true);

        $this->actingAs($this->superAdmin())
            ->get(route('superadmin.2fa.setup'))
            ->assertOk();
    }

    #[Test]
    public function superadmin_avec_2fa_active_mais_session_non_verifiee_va_au_challenge(): void
    {
        $user = $this->superAdmin([
            'two_factor_secret'       => 'BASE32SECRET234',
            'two_factor_confirmed_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('superadmin.dashboard'))
            ->assertRedirect(route('superadmin.2fa.challenge'));
    }
}
