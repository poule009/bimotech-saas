<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
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
class DGIDReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly string $typeEcheance,  // 'brs_annuel'|'irpp'|'cfpb'|'cel_vl'|'cel_va'|'is_acompte_1'|'is_acompte_2'|'is_solde'
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
                'titre'       => 'Déclaration IRPP/CGF — Revenus locatifs',
                'description' => "Si vos revenus locatifs {$this->annee} sont inférieurs à 30 000 000 FCFA, vous relevez du régime **CGF** (déclaration prévisionnelle N, Art. 77-94 CGI SN) ou du régime réel **IRPP** (déclaration revenus N-1 après abattement 30%, Art. 68 §c). Consultez votre bilan Bimotech pour savoir quel régime est le plus avantageux pour vous. Référence : CGI Art. 77-94 (CGF) et Art. 173 (IRPP).",
            ],
            'cfpb' => [
                'titre'       => 'Contribution Foncière des Propriétés Bâties (CFPB)',
                'description' => "La CFPB est due sur la valeur locative cadastrale de vos biens (Art. 290-291). Taux : 5% (Art. 294). Référence : CGI art. 283-294.",
            ],
            'cel_vl' => [
                'titre'       => 'Déclaration CEL-VL (Valeur Locative)',
                'description' => "Vous devez déclarer la valeur locative de vos locaux professionnels à la DGID avant le 31 janvier. Formulaire disponible au Centre des Services Fiscaux. Référence : Art. 320-338 CGI SN. Note : les agences immobilières sont exclues de la CGU et relèvent obligatoirement de la CEL.",
            ],
            'cel_va' => [
                'titre'       => 'Déclaration CEL-VA (Valeur Ajoutée)',
                'description' => "La CEL-VA est à déposer simultanément avec votre liasse fiscale IS avant le 30 avril. Assiette : valeur ajoutée de l'exercice N-1. Référence : Art. 320-338 CGI SN.",
            ],
            'is_acompte_1' => [
                'titre'       => '1er acompte IS à verser',
                'description' => "Le premier tiers de votre IS N-1 est dû avant le 15 février. Montant = IS {$this->annee} / 3. Si vous ne connaissez pas votre IS N-1, rapprochez-vous de votre comptable. Référence : Art. 36-37 CGI SN.",
            ],
            'is_acompte_2' => [
                'titre'       => '2ème acompte IS + dépôt liasse fiscale',
                'description' => "Deux obligations simultanées avant le 30 avril : (1) Versement du 2ème tiers de votre IS N-1. (2) Dépôt de votre liasse fiscale (résultats exercice {$this->annee}). (3) Dépôt simultané CEL-VA. Référence : Art. 36-37 CGI SN.",
            ],
            'is_solde' => [
                'titre'       => 'Solde IS + régularisation IMF',
                'description' => "Le solde IS est dû avant le 15 juin : IS réel {$this->annee} - acomptes versés = solde à payer. Si IS réel < IMF (0,5% CA HT, min 500 000 FCFA, max 5 000 000 FCFA), l'IMF est due à la place. Référence : Art. 37 CGI SN.",
            ],
            default => [
                'titre'       => 'Échéance fiscale',
                'description' => "Une échéance fiscale approche. Consultez votre bilan BIMO-Tech pour les détails.",
            ],
        };
    }
}
