<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TvaMensuelleReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly int    $moisDeclaration,
        public readonly int    $anneeDeclaration,
        public readonly string $dateEcheance,
        public readonly float  $tvaNetteDue,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $periodeLabel = Carbon::create($this->anneeDeclaration, $this->moisDeclaration, 1)
            ->locale('fr')
            ->translatedFormat('F Y');

        $montantFormate = number_format($this->tvaNetteDue, 0, ',', ' ') . ' FCFA';

        return (new MailMessage)
            ->subject("Rappel TVA — Déclaration {$periodeLabel} à déposer avant le {$this->dateEcheance}")
            ->greeting('Bonjour,')
            ->line("Votre déclaration TVA de **{$periodeLabel}** est à déposer avant le **{$this->dateEcheance}**.")
            ->when($this->tvaNetteDue > 0, fn($mail) => $mail
                ->line("Montant TVA nette due : **{$montantFormate}**")
            )
            ->action('Accéder à la déclaration TVA', url('/admin/tva-agence'))
            ->line('Art. 370 CGI SN — Déclaration mensuelle obligatoire.')
            ->salutation('Bimotech — Gestion fiscale agence');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'             => 'tva_mensuelle',
            'mois_declaration' => $this->moisDeclaration,
            'annee_declaration' => $this->anneeDeclaration,
            'date_echeance'    => $this->dateEcheance,
            'tva_nette_due'    => $this->tvaNetteDue,
        ];
    }
}
