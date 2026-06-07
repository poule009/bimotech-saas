<?php

namespace App\Notifications;

use App\Models\Agency;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CollaborateurInvitationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly Agency $agency,
        private readonly string $invitePar,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $loginUrl = route('login');

        return (new MailMessage)
            ->subject("Vous êtes invité à rejoindre {$this->agency->name} sur BIMO-Tech")
            ->greeting("Bonjour {$notifiable->name},")
            ->line("{$this->invitePar} vous a ajouté comme collaborateur de l'agence **{$this->agency->name}** sur BIMO-Tech Immo.")
            ->line("Vous pouvez vous connecter dès maintenant avec votre adresse email **{$notifiable->email}** et le mot de passe communiqué par votre directeur.")
            ->action('Se connecter', $loginUrl)
            ->line('En tant que collaborateur, vous aurez accès aux biens, contrats, paiements et quittances de l\'agence.')
            ->line('Pour toute question, contactez directement votre directeur.')
            ->salutation("Bienvenue dans l'équipe ! — BIMO-Tech");
    }
}
