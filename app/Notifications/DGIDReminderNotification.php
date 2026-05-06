<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * DGIDReminderNotification — Rappel des échéances fiscales DGID pour les propriétaires.
 *
 * Échéances fiscales sénégalaises concernées :
 *  - 31 janvier  : État annuel récapitulatif BRS — liste nominative des retenues N (CGI art. 200 §5)
 *  - 30 avril    : Déclaration IRPP revenus locatifs (CGI art. 173 + abattement art. 68 §c)
 *  - 30 septembre: Paiement CFPB (CGI art. 283-294 — assiette = valeur locative cadastrale)
 */
class DGIDReminderNotification extends Notification
{
    use Queueable;

    public function __construct(
        public readonly string $typeEcheance,  // 'brs_annuel' | 'irpp' | 'cfpb'
        public readonly string $dateEcheance,  // ex: "31 janvier 2026"
        public readonly int    $joursRestants,
        public readonly int    $annee,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $infos = $this->infosByType();

        return (new MailMessage)
            ->subject("⚠️ Rappel fiscal DGID — {$infos['titre']} ({$this->joursRestants} jours)")
            ->greeting("Bonjour {$notifiable->name},")
            ->line("Il vous reste **{$this->joursRestants} jours** pour effectuer votre **{$infos['titre']}** avant le **{$this->dateEcheance}**.")
            ->line("")
            ->line("### Ce que vous devez déclarer")
            ->line($infos['description'])
            ->line("")
            ->line("### Comment préparer votre dossier")
            ->line("Rendez-vous dans BIMO-Tech → **Bilans Fiscaux** pour télécharger votre récapitulatif {$this->annee} et préparer votre déclaration.")
            ->action('Consulter mon bilan fiscal', url('/'))
            ->line("---")
            ->line("*Ce rappel est automatique. Rapprochez-vous de votre comptable ou de la DGID pour toute question sur votre situation fiscale personnelle.*")
            ->salutation("Cordialement, l'équipe BIMO-Tech Immobilier");
    }

    private function infosByType(): array
    {
        return match ($this->typeEcheance) {
            'brs_annuel' => [
                'titre'       => 'État annuel récapitulatif BRS (Retenue à la source)',
                'description' => "Vous devez remettre à la DGID l'état annuel nominatif des retenues BRS effectuées sur les loyers {$this->annee}. Cet état liste chaque propriétaire, son NINEA, les loyers versés et la BRS retenue. Référence : CGI art. 200 §5.",
            ],
            'irpp' => [
                'titre'       => 'Déclaration IRPP — Revenus locatifs',
                'description' => "Vous devez déclarer vos revenus locatifs {$this->annee} à la DGID. L'impôt est calculé sur la base imposable après abattement forfaitaire de 30% (Art. 68 §c). Référence : CGI art. 173.",
            ],
            'cfpb' => [
                'titre'       => 'Contribution Foncière des Propriétés Bâties (CFPB)',
                'description' => "La CFPB est due sur la valeur locative cadastrale de vos biens (Art. 290-291). Taux : 5% (Art. 294). Référence : CGI art. 283-294.",
            ],
            default => [
                'titre'       => 'Échéance fiscale',
                'description' => "Une échéance fiscale approche. Consultez votre bilan BIMO-Tech pour les détails.",
            ],
        };
    }
}
