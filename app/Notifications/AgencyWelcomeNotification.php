<?php

namespace App\Notifications;

use App\Models\Agency;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Password;

class AgencyWelcomeNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Agency $agency,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        // Génère un token de réinitialisation valable 60 min — jamais le mot de passe en clair
        $resetToken = Password::createToken($notifiable);
        $resetUrl   = url(route('password.reset', [
            'token' => $resetToken,
            'email' => $notifiable->getEmailForVerification(),
        ], false));

        return (new MailMessage)
            ->subject("Bienvenue sur BIMO-Tech — Définissez votre mot de passe")
            ->greeting("Bonjour {$notifiable->name},")
            ->line("Votre espace agence **{$this->agency->name}** a été créé avec succès sur la plateforme BIMO-Tech.")
            ->line('---')
            ->line('**Vos informations de connexion :**')
            ->line("• Email : {$notifiable->email}")
            ->line("• Cliquez sur le bouton ci-dessous pour définir votre mot de passe.")
            ->line('---')
            ->action('Définir mon mot de passe', $resetUrl)
            ->line('Ce lien expire dans **60 minutes**. Passé ce délai, contactez votre administrateur de plateforme.')
            ->line('Si vous avez des questions, contactez-nous à **support@bimotech.sn**.')
            ->salutation('Cordialement, — L\'équipe BIMO-Tech');
    }
}