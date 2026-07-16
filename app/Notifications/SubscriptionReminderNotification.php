<?php

namespace App\Notifications;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionReminderNotification extends Notification implements ShouldQueue
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

        $mail = (new MailMessage)
            ->subject("{$urgence} — Votre {$typeAcces} expire dans {$this->joursRestants} jour(s)")
            ->greeting("Bonjour {$notifiable->name},")
            ->line("Nous vous informons que votre **{$typeAcces}** pour l'agence **{$agency->name}** expire dans **{$this->joursRestants} jour(s)**, le **{$expiration->format('d/m/Y')}**.")
            ->line("Après cette date, votre accès à BIMO-Tech sera automatiquement suspendu.")
            ->action('Choisir un abonnement maintenant', url('/subscription'))
            ->line('---')
            ->line('**Nos offres d\'abonnement (en FCFA) :**');

        // Grille lue en base (table `plans`) : elle était recopiée en dur ici et
        // annonçait encore des cycles trimestriel/semestriel supprimés en mai 2026,
        // à des tarifs qui n'ont jamais existé dans l'app.
        foreach (app(\App\Services\PlanService::class)->souscriptibles() as $plan) {
            $mail->line(sprintf(
                '• %s : %s FCFA / mois — %s FCFA / an',
                $plan->libelle_public,
                number_format($plan->prix_mensuel, 0, ',', ' '),
                number_format($plan->prix_annuel, 0, ',', ' '),
            ));
        }

        return $mail
            ->line('---')
            ->line('Pour souscrire, contactez-nous à **support@bimotech.sn** ou au **+221 33 800 00 01**.')
            ->salutation('Cordialement, — L\'équipe BIMO-Tech');
    }
}