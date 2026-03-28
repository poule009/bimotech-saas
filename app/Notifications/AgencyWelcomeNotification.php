<?php

namespace App\Notifications;

use App\Models\Agency;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AgencyWelcomeNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Agency $agency,
        public string $plainPassword,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $verificationUrl = \Illuminate\Support\Facades\URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id'   => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );

        return (new MailMessage)
            ->subject("Bienvenue sur BIMO-Tech — Confirmez votre email")
            ->greeting("Bonjour {$notifiable->name},")
            ->line("Votre espace agence **{$this->agency->name}** a été créé avec succès sur la plateforme BIMO-Tech.")
            ->line('---')
            ->line('**Vos informations de connexion :**')
            ->line("• Email : {$notifiable->email}")
            ->line("• Mot de passe : {$this->plainPassword}")
            ->line('---')
            ->action('✅ Confirmer mon email', $verificationUrl)
            ->line('Ce lien expire dans **60 minutes**.')
            ->line('---')
            ->line('Pour des raisons de sécurité, nous vous recommandons de changer votre mot de passe dès votre première connexion.')
            ->line('Si vous avez des questions, contactez-nous à **support@bimotech.sn**.')
            ->salutation('Cordialement, — L\'équipe BIMO-Tech');
    }
}