<?php

namespace App\Notifications;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionReminderNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Subscription $subscription,
        public int $joursRestants,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $agency      = $this->subscription->agency;
        $expiration  = $this->subscription->estEnEssai()
            ? $this->subscription->date_fin_essai
            : $this->subscription->date_fin_abonnement;

        $typeAcces = $this->subscription->estEnEssai()
            ? "période d'essai"
            : "abonnement";

        $urgence = $this->joursRestants === 1 ? '🚨 Dernière chance' : '⚠️ Rappel important';

        return (new MailMessage)
            ->subject("{$urgence} — Votre {$typeAcces} expire dans {$this->joursRestants} jour(s)")
            ->greeting("Bonjour {$notifiable->name},")
            ->line("Nous vous informons que votre **{$typeAcces}** pour l'agence **{$agency->name}** expire dans **{$this->joursRestants} jour(s)**, le **{$expiration->format('d/m/Y')}**.")
            ->line("Après cette date, votre accès à BIMO-Tech sera automatiquement suspendu.")
            ->action('Choisir un abonnement maintenant', url('/subscription'))
            ->line('---')
            ->line('**Nos offres d\'abonnement (en FCFA) :**')
            ->line('• Mensuel : 25 000 FCFA / mois')
            ->line('• Trimestriel : 67 500 FCFA / 3 mois (−10%)')
            ->line('• Semestriel : 127 500 FCFA / 6 mois (−15%)')
            ->line('• Annuel : 240 000 FCFA / an (−20%)')
            ->line('---')
            ->line('Pour souscrire, contactez-nous à **support@bimotech.sn** ou au **+221 33 800 00 01**.')
            ->salutation('Cordialement, — L\'équipe BIMO-Tech');
    }
}