<?php

namespace Tests\Feature;

use App\Mail\ContactMail;
use App\Mail\DemoRequestMail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * PublicFormulairesTest — ContactController + DemoController.
 *
 * Ces deux routes sont publiques (pas d'authentification requise).
 * On vérifie la validation des champs et l'envoi du mail via Mail::fake().
 */
class PublicFormulairesTest extends TestCase
{
    use RefreshDatabase;

    // ═══════════════════════════════════════════════════════════════════════
    // ContactController
    // ═══════════════════════════════════════════════════════════════════════

    private function payloadContactValide(): array
    {
        return [
            'prenom'    => 'Fatou',
            'nom'       => 'Sarr',
            'agence'    => 'Agence Immo Dakar',
            'email'     => 'fatou.sarr@test.sn',
            'telephone' => '+221 77 111 22 33',
            'objet'     => 'demo',
            'message'   => 'Bonjour, je souhaite une démonstration du logiciel.',
        ];
    }

    #[Test]
    public function contact_envoie_mail_avec_donnees_valides()
    {
        Mail::fake();

        $this->post(route('contact.send'), $this->payloadContactValide())
             ->assertRedirect()
             ->assertSessionHas('success');

        Mail::assertSent(ContactMail::class, function ($mail) {
            return $mail->hasTo('contact@bimotech.sn');
        });
    }

    #[Test]
    public function contact_echoue_sans_prenom()
    {
        Mail::fake();
        $payload = $this->payloadContactValide();
        unset($payload['prenom']);

        $this->post(route('contact.send'), $payload)
             ->assertSessionHasErrors('prenom');

        Mail::assertNothingSent();
    }

    #[Test]
    public function contact_echoue_sans_nom()
    {
        Mail::fake();
        $payload = $this->payloadContactValide();
        unset($payload['nom']);

        $this->post(route('contact.send'), $payload)
             ->assertSessionHasErrors('nom');

        Mail::assertNothingSent();
    }

    #[Test]
    public function contact_echoue_avec_email_invalide()
    {
        Mail::fake();
        $payload = $this->payloadContactValide();
        $payload['email'] = 'pas-un-email';

        $this->post(route('contact.send'), $payload)
             ->assertSessionHasErrors('email');

        Mail::assertNothingSent();
    }

    #[Test]
    public function contact_echoue_avec_objet_invalide()
    {
        Mail::fake();
        $payload = $this->payloadContactValide();
        $payload['objet'] = 'inconnu';

        $this->post(route('contact.send'), $payload)
             ->assertSessionHasErrors('objet');

        Mail::assertNothingSent();
    }

    #[Test]
    public function contact_echoue_si_message_manquant()
    {
        Mail::fake();
        $payload = $this->payloadContactValide();
        unset($payload['message']);

        $this->post(route('contact.send'), $payload)
             ->assertSessionHasErrors('message');

        Mail::assertNothingSent();
    }

    #[Test]
    public function contact_echoue_si_message_trop_long()
    {
        Mail::fake();
        $payload = $this->payloadContactValide();
        $payload['message'] = str_repeat('x', 2001);

        $this->post(route('contact.send'), $payload)
             ->assertSessionHasErrors('message');

        Mail::assertNothingSent();
    }

    #[Test]
    public function tous_les_objets_valides_sont_acceptes()
    {
        $objetsValides = ['demo', 'tarif', 'technique', 'reseau', 'autre'];

        foreach ($objetsValides as $objet) {
            Mail::fake();
            $payload = $this->payloadContactValide();
            $payload['objet'] = $objet;

            $this->post(route('contact.send'), $payload)
                 ->assertRedirect()
                 ->assertSessionHas('success');
        }
    }

    // ═══════════════════════════════════════════════════════════════════════
    // DemoController
    // ═══════════════════════════════════════════════════════════════════════

    private function payloadDemoValide(): array
    {
        return [
            'prenom'    => 'Ibrahima',
            'nom'       => 'Fall',
            'agence'    => 'Cabinet Fall Immo',
            'telephone' => '+221 76 200 00 00',
            'email'     => 'ibrahima.fall@test.sn',
            'nb_biens'  => '20-50',
            'ville'     => 'Dakar',
        ];
    }

    #[Test]
    public function demo_envoie_mail_avec_donnees_valides()
    {
        Mail::fake();

        $this->post(route('demo.send'), $this->payloadDemoValide())
             ->assertRedirect()
             ->assertSessionHas('success');

        Mail::assertSent(DemoRequestMail::class, function ($mail) {
            return $mail->hasTo('contact@bimotech.sn');
        });
    }

    #[Test]
    public function demo_echoue_sans_prenom()
    {
        Mail::fake();
        $payload = $this->payloadDemoValide();
        unset($payload['prenom']);

        $this->post(route('demo.send'), $payload)
             ->assertSessionHasErrors('prenom');

        Mail::assertNothingSent();
    }

    #[Test]
    public function demo_echoue_sans_telephone()
    {
        Mail::fake();
        $payload = $this->payloadDemoValide();
        unset($payload['telephone']);

        $this->post(route('demo.send'), $payload)
             ->assertSessionHasErrors('telephone');

        Mail::assertNothingSent();
    }

    #[Test]
    public function demo_echoue_avec_email_invalide()
    {
        Mail::fake();
        $payload = $this->payloadDemoValide();
        $payload['email'] = 'invalide';

        $this->post(route('demo.send'), $payload)
             ->assertSessionHasErrors('email');

        Mail::assertNothingSent();
    }

    #[Test]
    public function demo_accepte_sans_nb_biens_ni_ville()
    {
        Mail::fake();
        $payload = $this->payloadDemoValide();
        unset($payload['nb_biens'], $payload['ville']);

        $this->post(route('demo.send'), $payload)
             ->assertRedirect()
             ->assertSessionHas('success');
    }
}
