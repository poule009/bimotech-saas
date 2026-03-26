<?php

namespace App\Notifications;

use App\Models\Paiement;
use App\Services\NombreEnLettres;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class QuittanceLocataireNotification extends Notification
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
        $paiement->load('contrat.bien.proprietaire', 'contrat.locataire');

        $periode = \Carbon\Carbon::parse($paiement->periode)->translatedFormat('F Y');

        // Génère le PDF en mémoire
        $data = [
            'paiement'         => $paiement,
            'contrat'          => $paiement->contrat,
            'bien'             => $paiement->contrat->bien,
            'proprietaire'     => $paiement->contrat->bien->proprietaire,
            'locataire'        => $paiement->contrat->locataire,
            'montantEnLettres' => NombreEnLettres::convertir($paiement->montant_encaisse),
            'netEnLettres'     => NombreEnLettres::convertir($paiement->net_proprietaire),
            'agence' => [
                'nom'       => 'BIMO-Tech Immobilier',
                'adresse'   => 'Dakar, Sénégal',
                'telephone' => '+221 33 000 00 00',
                'email'     => 'contact@bimotech.sn',
                'ninea'     => 'NINEA : 00000000000',
            ],
        ];

        $pdf = Pdf::loadView('paiements.pdf.quittance', $data)
            ->setPaper('a4', 'portrait')
            ->setOption('defaultFont', 'DejaVu Sans');

        $nomFichier = 'quittance-' . $paiement->reference_paiement . '.pdf';

        return (new MailMessage)
            ->subject("Quittance de loyer — {$periode}")
            ->greeting("Bonjour {$notifiable->name},")
            ->line("Votre paiement de loyer pour **{$periode}** a bien été enregistré.")
            ->line("**Bien loué :** {$paiement->contrat->bien->reference} — {$paiement->contrat->bien->adresse}")
            ->line("**Montant :** " . number_format($paiement->montant_encaisse, 0, ',', ' ') . " FCFA")
            ->line("**Mode de règlement :** " . ucfirst(str_replace('_', ' ', $paiement->mode_paiement)))
            ->line("Votre quittance officielle est jointe à cet email.")
            ->attachData($pdf->output(), $nomFichier, [
                'mime' => 'application/pdf',
            ])
            ->salutation("Cordialement, l'équipe BIMO-Tech Immobilier");
    }
}