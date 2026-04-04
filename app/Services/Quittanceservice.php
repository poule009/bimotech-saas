<?php

namespace App\Services;

use App\Models\Paiement;
use App\Models\Quittance;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

/**
 * QuittanceService — Génération des quittances de loyer (PDF légal SN).
 *
 * Une quittance est générée UNIQUEMENT après validation d'un paiement.
 * Elle est immuable une fois créée (document légal opposable).
 *
 * Numérotation : QT-{AGENCY_ID}-{ANNEE}-{SEQUENCE}
 * Exemple      : QT-04-2025-0087
 *
 * Le PDF est généré via DomPDF (contraintes CSS : display:table, DejaVu Sans).
 */
class QuittanceService
{
    // ─── Génération ─────────────────────────────────────────────────────────

    /**
     * Crée et persiste une quittance après validation d'un paiement.
     * Opération atomique : si le PDF échoue, la quittance n'est pas créée.
     *
     * @param  Paiement $paiement  Doit être au statut 'valide'
     * @return Quittance
     * @throws \LogicException      Si le paiement n'est pas validé
     * @throws \RuntimeException    Si une quittance existe déjà pour ce paiement
     */
    public function generer(Paiement $paiement): Quittance
    {
        if ($paiement->statut !== 'valide') {
            throw new \LogicException(
                "Impossible de générer une quittance pour un paiement non validé (ID: {$paiement->id})."
            );
        }

        if ($paiement->quittance()->exists()) {
            throw new \RuntimeException(
                "Une quittance existe déjà pour ce paiement (ID: {$paiement->id})."
            );
        }

        return DB::transaction(function () use ($paiement) {
            $paiement->load(['contrat.bien.proprietaire', 'contrat.locataire', 'contrat.bien.agency']);

            $numero    = $this->genererNumero($paiement->agency_id);
            $quittance = Quittance::create([
                'agency_id'     => $paiement->agency_id,
                'paiement_id'   => $paiement->id,
                'contrat_id'    => $paiement->contrat_id,
                'numero'        => $numero,
                'date_emission' => now()->toDateString(),
                'mois_concerne' => $paiement->mois_concerne,
                'generee_par'   => Auth::id(),
            ]);

            return $quittance;
        });
    }

    /**
     * Génère le PDF DomPDF d'une quittance existante.
     * Le PDF n'est pas stocké sur disque — il est streamé à la volée.
     *
     * @param  Quittance $quittance
     * @return \Barryvdh\DomPDF\PDF
     */
    public function genererPdf(Quittance $quittance): \Barryvdh\DomPDF\PDF
    {
        $quittance->load([
            'paiement',
            'contrat.bien',
            'contrat.locataire',
            'contrat.bien.proprietaire',
            'contrat.bien.agency',
        ]);

        $donnees = $this->preparerDonneesPdf($quittance);

        return Pdf::loadView('pdf.quittance', $donnees)
            ->setPaper('A4')
            ->setOption('isPhpEnabled', false)
            ->setOption('isRemoteEnabled', false)
            ->setOption('defaultFont', 'dejavu sans');
    }

    /**
     * Retourne le nom de fichier standardisé pour le téléchargement.
     *
     * @param  Quittance $quittance
     * @return string  ex: "Quittance_QT-04-2025-0087_2025-06.pdf"
     */
    public function nomFichier(Quittance $quittance): string
    {
        return sprintf(
            'Quittance_%s_%s.pdf',
            $quittance->numero,
            $quittance->mois_concerne
        );
    }

    // ─── Requêtes ───────────────────────────────────────────────────────────

    /**
     * Vérifie si une quittance existe pour un paiement donné.
     */
    public function existePourPaiement(Paiement $paiement): bool
    {
        return $paiement->quittance()->exists();
    }

    /**
     * Retourne les quittances d'un locataire pour une période donnée.
     *
     * @param  int  $locataireId
     * @param  int  $annee
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getQuittancesLocataire(int $locataireId, int $annee)
    {
        return Quittance::with('paiement')
            ->whereHas('contrat', fn($q) => $q->where('locataire_id', $locataireId))
            ->where('mois_concerne', 'like', $annee . '-%')
            ->orderBy('mois_concerne', 'desc')
            ->get();
    }

    // ─── Numérotation ───────────────────────────────────────────────────────

    /**
     * Génère un numéro de quittance unique et séquentiel par agence et par année.
     * Format : QT-{agencyId}-{YYYY}-{sequence 4 chiffres}
     *
     * Utilise un verrou pessimiste (lockForUpdate) pour éviter les doublons
     * en cas de requêtes concurrentes.
     *
     * @param  int $agencyId
     * @return string
     */
    private function genererNumero(int $agencyId): string
    {
        $annee = now()->year;
        $prefixe = sprintf('QT-%02d-%d-', $agencyId, $annee);

        // Verrou pessimiste sur la dernière quittance de l'agence cette année
        $derniere = Quittance::where('agency_id', $agencyId)
            ->where('numero', 'like', $prefixe . '%')
            ->lockForUpdate()
            ->orderByDesc('numero')
            ->first();

        $sequence = 1;
        if ($derniere) {
            // Extrait le numéro de séquence de la fin du numéro
            $sequence = (int) substr($derniere->numero, strrpos($derniere->numero, '-') + 1) + 1;
        }

        return $prefixe . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Prépare le tableau de données pour la vue Blade du PDF.
     *
     * @param  Quittance $quittance
     * @return array
     */
    private function preparerDonneesPdf(Quittance $quittance): array
    {
        $paiement  = $quittance->paiement;
        $contrat   = $quittance->contrat;
        $bien      = $contrat->bien;
        $agency    = $bien->agency;
        $locataire = $contrat->locataire;
        $proprietaire = $bien->proprietaire;

        // Formatage du mois en français (ex: "Juin 2025")
        $moisFormate = Carbon::createFromFormat('Y-m', $quittance->mois_concerne)
            ->locale('fr_SN')
            ->isoFormat('MMMM YYYY');

        return [
            'quittance'    => $quittance,
            'paiement'     => $paiement,
            'contrat'      => $contrat,
            'bien'         => $bien,
            'agency'       => $agency,
            'locataire'    => $locataire,
            'proprietaire' => $proprietaire,
            'mois_formate' => ucfirst($moisFormate),
            'date_emission_formatee' => Carbon::parse($quittance->date_emission)
                ->locale('fr_SN')
                ->isoFormat('D MMMM YYYY'),
        ];
    }
}