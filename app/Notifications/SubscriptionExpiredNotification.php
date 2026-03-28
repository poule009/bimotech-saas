<?php

namespace App\Notifications;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionExpiredNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Subscription $subscription,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $agency    = $this->subscription->agency;
        $typeAcces = $this->subscription->statut === 'essai'
            ? "période d'essai"
            : "abonnement";

        return (new MailMessage)
            ->subject("❌ Votre accès BIMO-Tech est suspendu — {$agency->name}")
            ->greeting("Bonjour {$notifiable->name},")
            ->line("Votre **{$typeAcces}** pour l'agence **{$agency->name}** a expiré.")
            ->line("Votre accès à la plateforme BIMO-Tech est maintenant **suspendu**.")
            ->action('Réactiver mon accès', url('/subscription'))
            ->line('---')
            ->line('**Vos données sont en sécurité.** Elles sont conservées et seront accessibles dès la réactivation de votre abonnement.')
            ->line('Pour souscrire, contactez-nous à **support@bimotech.sn** ou au **+221 33 800 00 01**.')
            ->salutation('Cordialement, — L\'équipe BIMO-Tech');
    }
}