<?php

namespace App\Notifications;

use App\Models\Contrat;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RelanceImpayeNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Contrat $contrat,
        public Carbon $periode
    ) {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $mois = $this->periode->locale('fr')->translatedFormat('F Y');
        $bienReference = $this->contrat->bien?->reference ?? 'N/A';
        $montant = (float) ($this->contrat->loyer_contractuel ?? 0);

        return (new MailMessage)
            ->subject("Relance de loyer impayé - {$mois}")
            ->greeting("Bonjour {$notifiable->name},")
            ->line("Nous vous rappelons que le loyer du mois de {$mois} est actuellement impayé.")
            ->line("Bien concerné : {$bienReference}")
            ->line('Montant dû : ' . number_format($montant, 0, ',', ' ') . ' FCFA')
            ->line('Merci de régulariser la situation dans les meilleurs délais.')
            ->salutation('Cordialement, ' . ($this->contrat->bien?->agency?->name ?? 'Votre agence immobilière'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'contrat_id' => $this->contrat->id,
            'periode' => $this->periode->format('Y-m'),
            'type' => 'relance_impaye',
        ];
    }
}
