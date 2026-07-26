<?php

namespace Tests\Browser;

use App\Models\Agency;
use App\Models\Subscription;
use App\Models\User;
use App\Support\Pays;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

/**
 * Smoke tests — Authentification BimoTech Immo
 *
 * Prérequis :
 *   1. php artisan migrate --env=dusk.local
 *   2. php artisan serve --env=dusk.local     (terminal séparé)
 *   3. php artisan dusk tests/Browser/LoginTest.php
 */
class LoginTest extends DuskTestCase
{
    private const TEST_EMAIL    = 'admin@dusk.test';
    private const TEST_PASSWORD = 'DuskPassword123!';

    // ── Setup / Teardown ─────────────────────────────────────────────────

    protected function setUp(): void
    {
        parent::setUp();
        $this->cleanupTestData();
    }

    protected function tearDown(): void
    {
        $this->cleanupTestData();
        parent::tearDown();
    }

    private function cleanupTestData(): void
    {
        User::where('email', self::TEST_EMAIL)->forceDelete();
        Agency::where('email', 'agence@dusk.test')->each(function ($a) {
            Subscription::where('agency_id', $a->id)->forceDelete();
            $a->forceDelete();
        });
    }

    private function createTestAdmin(): User
    {
        $agency = new Agency();
        $agency->name   = 'Agence Dusk Test';
        $agency->email  = 'agence@dusk.test';
        $agency->slug   = 'agence-dusk-' . Str::random(6);
        // `pays` est NOT NULL sans default (cf. migration add_pays_and_devise_to_agencies) :
        // l'omettre ferait échouer l'insert. Hors $fillable → assignation directe.
        $agency->pays   = Pays::DEFAUT;
        $agency->devise = Pays::devise(Pays::DEFAUT);
        $agency->actif  = true;
        $agency->save();

        Subscription::create([
            'agency_id'      => $agency->id,
            'statut'         => 'essai',
            'date_fin_essai' => now()->addDays(30),
        ]);

        $user = new User();
        $user->name              = 'Admin Dusk';
        $user->email             = self::TEST_EMAIL;
        $user->password          = Hash::make(self::TEST_PASSWORD);
        $user->email_verified_at = now();
        $user->role              = 'admin';
        $user->agency_id         = $agency->id;
        $user->save();

        return $user;
    }

    // ── Tests ─────────────────────────────────────────────────────────────

    /**
     * SMOKE-001 : La page de login s'affiche correctement.
     * → Vérifie les éléments clés sans requête authentifiée.
     */
    public function test_login_page_displays_correctly(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->assertPresent('#email')
                ->assertPresent('#password')
                ->assertPresent('#submit-btn')
                ->assertSee('Se connecter');
        });
    }

    /**
     * SMOKE-002 : Un admin authentifié atteint le dashboard.
     *
     * Utilise loginAs() (standard Dusk) qui bypasse le formulaire HTML
     * et évite les erreurs CSRF liées au driver de session local.
     * → Vérifie que le routing auth + middleware (CheckSubscription,
     *   isAdmin, verified) fonctionnent correctement.
     */
    public function test_admin_can_access_dashboard(): void
    {
        $admin = $this->createTestAdmin();

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin);

            // Snapshot intermédiaire pour diagnostiquer où on atterrit après loginAs
            $browser->screenshot('after-loginAs');

            $browser->visit('/admin/dashboard')
                ->screenshot('after-dashboard-visit')
                ->assertPathIs('/admin/dashboard');
        });
    }

    /**
     * SMOKE-003 : Un utilisateur non-authentifié est redirigé vers /login.
     * → Vérifie que les routes protégées rejettent les requêtes anonymes.
     */
    public function test_unauthenticated_user_is_redirected_to_login(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/admin/dashboard')
                ->assertPathIs('/login');
        });
    }
}
