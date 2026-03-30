<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WeeklyPaymentsSummaryNotification extends Notification
{
    use Queueable;

    public function __construct(
        public array $summary,
        public Carbon $startDate,
        public Carbon $endDate
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $totalMontant = number_format((float) ($this->summary['total_montant'] ?? 0), 0, ',', ' ');
        $totalNet = number_format((float) ($this->summary['total_net'] ?? 0), 0, ',', ' ');
        $totalCommission = number_format((float) ($this->summary['total_commission'] ?? 0), 0, ',', ' ');
        $nombrePaiements = (int) ($this->summary['total_paiements'] ?? 0);
        $nombreAgences = (int) ($this->summary['total_agences'] ?? 0);

        return (new MailMessage)
            ->subject('Rapport Hebdomadaire Paiements — Plateforme')
            ->greeting("Bonjour {$notifiable->name},")
            ->line('Voici le résumé hebdomadaire des paiements encaissés sur toute la plateforme.')
            ->line('**Période :** du ' . $this->startDate->format('d/m/Y') . ' au ' . $this->endDate->format('d/m/Y'))
            ->line("**Nombre de paiements validés :** {$nombrePaiements}")
            ->line("**Nombre d'agences actives sur la période :** {$nombreAgences}")
            ->line("**Montant total encaissé :** {$totalMontant} FCFA")
            ->line("**Commission totale :** {$totalCommission} FCFA")
            ->line("**Net propriétaire total :** {$totalNet} FCFA")
            ->salutation('Cordialement, Système BIMO-Tech');
    }
}
