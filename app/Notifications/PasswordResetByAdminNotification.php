<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Password;

class PasswordResetByAdminNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $resetToken = Password::createToken($notifiable);
        $resetUrl   = url(route('password.reset', [
            'token' => $resetToken,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->subject('BIMO-Tech — Réinitialisation de votre mot de passe')
            ->greeting("Bonjour {$notifiable->name},")
            ->line("L'administrateur de la plateforme a réinitialisé votre mot de passe.")
            ->line('Cliquez sur le bouton ci-dessous pour définir un nouveau mot de passe.')
            ->action('Définir mon mot de passe', $resetUrl)
            ->line('Ce lien expire dans **60 minutes**.')
            ->line("Si vous n'attendiez pas cette action, contactez votre agence immédiatement.")
            ->salutation('Cordialement, — L\'équipe BIMO-Tech');
    }
}
