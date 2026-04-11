<?php

namespace App\Notifications;

use App\Models\Agency;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Email d'onboarding automatique envoyé à J+1, J+7 et J+25 de l'essai gratuit.
 *
 * @param int $step  1 | 7 | 25
 */
class OnboardingNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Agency $agency,
        public int $step,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return match ($this->step) {
            1  => $this->mailJ1($notifiable),
            7  => $this->mailJ7($notifiable),
            25 => $this->mailJ25($notifiable),
        };
    }

    // ── J+1 : Premier bien ────────────────────────────────────────────────

    private function mailJ1(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Bienvenue sur BimoTech — Par où commencer ?")
            ->greeting("Bonjour {$notifiable->name},")
            ->line("Votre espace **{$this->agency->name}** est prêt. Voici les 3 premières étapes pour bien démarrer :")
            ->line('---')
            ->line('**1. Ajoutez votre premier bien** → Biens > Nouveau bien')
            ->line('**2. Créez un locataire** → Locataires > Nouveau locataire')
            ->line('**3. Rédigez un contrat** → Contrats > Nouveau contrat')
            ->action('Accéder à mon espace', url('/dashboard'))
            ->line('---')
            ->line('Besoin d\'aide ? Répondez directement à cet email ou contactez-nous sur **support@bimotech.sn**.')
            ->salutation('L\'équipe BimoTech');
    }

    // ── J+7 : Activation fonctionnalités ─────────────────────────────────

    private function mailJ7(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Une semaine avec BimoTech — avez-vous tout exploré ?")
            ->greeting("Bonjour {$notifiable->name},")
            ->line("Cela fait une semaine que vous êtes sur BimoTech. Voici ce que vous pouvez faire si ce n'est pas encore fait :")
            ->line('---')
            ->line('**Encaissez votre premier loyer** — générez la quittance PDF en un clic.')
            ->line('**Activez les espaces locataires** — vos locataires accèdent à leurs quittances sans vous appeler.')
            ->line('**Donnez accès à vos propriétaires** — ils suivent leurs revenus en temps réel.')
            ->line('**Vérifiez la conformité fiscale** — TVA 18%, BRS, TOM calculés automatiquement selon le CGI.')
            ->action('Voir mon tableau de bord', url('/dashboard'))
            ->line('---')
            ->line('Il vous reste **23 jours d\'essai gratuit**. Profitez-en.')
            ->salutation('L\'équipe BimoTech');
    }

    // ── J+25 : Conversion essai → abonnement ─────────────────────────────

    private function mailJ25(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("⏳ Il vous reste 5 jours d'essai — continuez sans interruption")
            ->greeting("Bonjour {$notifiable->name},")
            ->line("Votre essai gratuit de **{$this->agency->name}** se termine dans **5 jours**.")
            ->line("Pour continuer à gérer vos biens, contrats et paiements sans interruption, choisissez votre abonnement maintenant.")
            ->line('---')
            ->line('**Offres disponibles (en FCFA) :**')
            ->line('• **Mensuel** : 25 000 FCFA / mois')
            ->line('• **Trimestriel** : 67 500 FCFA / 3 mois *(−10%)*')
            ->line('• **Semestriel** : 127 500 FCFA / 6 mois *(−15%)*')
            ->line('• **Annuel** : 240 000 FCFA / an *(−20%)*')
            ->action('Choisir mon abonnement', url('/subscription'))
            ->line('---')
            ->line('Une question avant de vous abonner ? Répondez à cet email — on vous répond sous 2h.')
            ->salutation('L\'équipe BimoTech');
    }
}
