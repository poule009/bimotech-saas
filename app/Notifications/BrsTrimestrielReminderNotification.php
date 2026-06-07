<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * BrsTrimestrielReminderNotification — Rappels de dépôt de l'état trimestriel BRS.
 *
 * Art. 200 §5 CGI Sénégal — Échéances trimestrielles :
 *   T1 (Jan-Mar) → avant le 15 avril
 *   T2 (Avr-Jun) → avant le 15 juillet
 *   T3 (Jul-Sep) → avant le 15 octobre
 *   T4 (Oct-Déc) → avant le 15 janvier N+1
 *
 * Envoyé aux utilisateurs admin de l'agence à J-7 et J-3 avant chaque échéance.
 */
class BrsTrimestrielReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly int    $trimestre,
        public readonly int    $annee,
        public readonly string $dateEcheance,
        public readonly int    $joursRestants,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $triMois = match ($this->trimestre) {
            1 => 'Janvier — Mars',
            2 => 'Avril — Juin',
            3 => 'Juillet — Septembre',
            4 => 'Octobre — Décembre',
        };

        return (new MailMessage)
            ->subject("⚠️ Rappel — État BRS T{$this->trimestre}/{$this->annee} à déposer dans {$this->joursRestants} jours")
            ->greeting("Bonjour {$notifiable->name},")
            ->line("Il vous reste **{$this->joursRestants} jour(s)** pour déposer l'**état trimestriel BRS T{$this->trimestre} {$this->annee}** avant le **{$this->dateEcheance}**.")
            ->line("")
            ->line("### Ce que vous devez déposer")
            ->line("L'état trimestriel des retenues à la source sur loyers versés aux bailleurs personnes physiques pour la période **{$triMois} {$this->annee}** (Art. 200 §5 CGI Sénégal). Cet état liste chaque bailleur (nom, NINEA, adresse, loyers versés, BRS retenu).")
            ->action(
                "Télécharger l'état BRS T{$this->trimestre}/{$this->annee}",
                url("/admin/etats-trimestriels/{$this->annee}/{$this->trimestre}")
            )
            ->line("---")
            ->line("*Rendez-vous dans Bimotech → États trimestriels pour générer le PDF ou CSV à remettre à votre Centre des Services Fiscaux (CSF).*")
            ->salutation("Cordialement, l'équipe Bimotech-SaaS");
    }
}
