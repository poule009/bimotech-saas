<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * L'inscription individuelle est désactivée (SaaS multi-agences).
 * Les agences s'inscrivent via /register/agency.
 * Ces tests vérifient que /register retourne bien 404.
 */
class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_returns_404(): void
    {
        $this->get('/register')->assertNotFound();
    }

    public function test_registration_post_returns_404(): void
    {
        $this->post('/register', [
            'name'                  => 'Test User',
            'email'                 => 'test@example.com',
            'password'              => 'password',
            'password_confirmation' => 'password',
        ])->assertNotFound();
    }
}
