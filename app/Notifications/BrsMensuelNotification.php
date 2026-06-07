<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * BrsMensuelNotification — Rappel de versement mensuel BRS à la DGI.
 *
 * Envoyée à l'admin de l'agence dans la fenêtre J-7 avant le 15 du mois.
 * Référence légale : CGI SN art. 200 §4.
 */
class BrsMensuelNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly int    $moisConcerne,
        public readonly int    $anneeConcerne,
        public readonly float  $totalBrsDu,
        public readonly int    $nombreBailleurs,
        public readonly string $dateLimite,
        public readonly int    $joursRestants,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $montantFmt = number_format($this->totalBrsDu, 0, ',', ' ');
        $moisLabel  = Carbon::create($this->anneeConcerne, $this->moisConcerne, 1)
            ->locale('fr')
            ->translatedFormat('F Y');
        $urgence = $this->joursRestants <= 3 ? '🚨' : '⚠️';

        return (new MailMessage)
            ->subject("{$urgence} BRS à verser avant le {$this->dateLimite} — {$montantFmt} FCFA")
            ->greeting("Bonjour {$notifiable->name},")
            ->line("**Obligation mensuelle BRS — Art. 200 §4 CGI SN.** Il vous reste **{$this->joursRestants} jour(s)** pour verser la retenue à la source au Centre des Services Fiscaux.")
            ->line("")
            ->line("### Montant à verser à la DGI")
            ->line("**{$montantFmt} FCFA** — BRS retenue sur les loyers de **{$moisLabel}** ({$this->nombreBailleurs} propriétaire(s) concerné(s)).")
            ->line("")
            ->line("### Procédure de versement")
            ->line("Présentez-vous au **Centre des Services Fiscaux** de votre ressort territorial avec :")
            ->line("- Le formulaire de déclaration BRS (disponible au guichet ou sur dgid.gouv.sn)")
            ->line("- Les quittances de loyer du mois de {$moisLabel}")
            ->line("- Le règlement du montant exact : **{$montantFmt} FCFA** (chèque ou virement)")
            ->line("")
            ->line("### ⚠️ Pénalité de retard")
            ->line("Tout versement après le **{$this->dateLimite}** expose votre agence à des **majorations et pénalités** prévues par le CGI SN. Ne laissez pas passer cette échéance.")
            ->action('Vérifier le détail des paiements BRS', url('/paiements'))
            ->line("---")
            ->line("*Ce rappel concerne uniquement le versement mensuel (Art. 200 §4). L'état annuel récapitulatif (Art. 200 §5) est dû au 31 janvier de l'année suivante.*")
            ->salutation("Cordialement, l'équipe BIMO-Tech Immobilier");
    }
}
