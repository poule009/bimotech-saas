<?php

namespace App\Notifications;

use App\Models\Paiement;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaiementProprietaireNotification extends Notification
{
    use Queueable;

    public function __construct(public Paiement $paiement) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $paiement = $this->paiement;
        $periode  = \Carbon\Carbon::parse($paiement->periode)->translatedFormat('F Y');

        return (new MailMessage)
            ->subject("Loyer encaissé — {$periode}")
            ->greeting("Bonjour {$notifiable->name},")
            ->line("Le loyer de votre bien **{$paiement->contrat->bien->reference}** a été encaissé pour **{$periode}**.")
            ->line("**Loyer brut :** " . number_format($paiement->montant_encaisse, 0, ',', ' ') . " FCFA")
            ->line("**Commission BIMO-Tech TTC :** - " . number_format($paiement->commission_ttc, 0, ',', ' ') . " FCFA")
            ->line("**Net à recevoir :** " . number_format($paiement->net_proprietaire, 0, ',', ' ') . " FCFA")
            ->line("Locataire : {$paiement->contrat->locataire->name}")
            ->salutation("Cordialement, l'équipe BIMO-Tech Immobilier");
    }
}