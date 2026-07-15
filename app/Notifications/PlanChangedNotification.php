<?php

namespace App\Notifications;

use App\Models\Agency;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Informe le directeur d'agence d'un changement de plan décidé côté BIMO-tech.
 *
 * Envoyée par email (canal Resend en place) : le changement a un impact
 * financier, il faut une trace écrite côté client — notamment le montant du
 * prorata, qui peut être contesté.
 */
class PlanChangedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Agency $agency,
        public string $nouveauPlan,
        public string $type,        // essai|upgrade|downgrade
        public int $prorata,
        public ?Carbon $effet,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $fmt = fn (int $n) => number_format($n, 0, ',', ' ');

        $mail = (new MailMessage)
            ->greeting("Bonjour {$notifiable->name},");

        if ($this->type === 'essai') {
            // Rien n'est facturé pendant l'essai : ne pas parler de prorata ni
            // d'effet sur un cycle en cours, il n'y en a pas.
            return $mail
                ->subject("Votre essai est configuré sur le plan {$this->nouveauPlan}")
                ->line("Votre période d'essai pour l'agence **{$this->agency->name}** est désormais configurée sur le plan **{$this->nouveauPlan}**.")
                ->line('Les limites de ce plan sont déjà actives, et c\'est son tarif qui vous sera proposé à la fin de votre essai.')
                ->line("**Rien ne vous est facturé aujourd'hui** — votre essai se poursuit normalement.")
                ->line('Une question ? Répondez à cet email ou écrivez-nous à **support@bimotech.sn**.')
                ->salutation("Cordialement, — L'équipe BIMO-tech");
        }

        if ($this->type === 'upgrade') {
            $mail->subject("Votre plan passe en {$this->nouveauPlan}")
                ->line("Le plan de votre agence **{$this->agency->name}** est passé en **{$this->nouveauPlan}**, avec effet immédiat.")
                ->line('Les nouvelles limites de votre plan sont déjà actives.');

            if ($this->prorata > 0) {
                $mail->line("Un complément de **{$fmt($this->prorata)} FCFA** est dû pour la période restante de votre cycle en cours. Il correspond à la différence de tarif entre les deux plans, calculée au prorata des jours restants — vous ne payez pas deux fois la même période.")
                    ->action('Déclarer votre paiement', url('/subscription/declarer'));
            } else {
                $mail->line('Aucun complément ne vous est facturé pour le cycle en cours.');
            }
        } else {
            $dateEffet = $this->effet?->locale('fr')->isoFormat('D MMMM Y');

            $mail->subject("Votre plan passera en {$this->nouveauPlan}")
                ->line("Le plan de votre agence **{$this->agency->name}** passera en **{$this->nouveauPlan}**"
                    .($dateEffet ? " le **{$dateEffet}**" : ' à votre prochain cycle de facturation').'.')
                ->line("D'ici là, vous conservez votre plan actuel et toutes ses limites — vous avez déjà réglé cette période, elle vous reste acquise.");
        }

        return $mail
            ->line('Une question sur ce changement ? Répondez à cet email ou écrivez-nous à **support@bimotech.sn**.')
            ->salutation("Cordialement, — L'équipe BIMO-tech");
    }
}
