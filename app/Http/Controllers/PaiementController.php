<?php

namespace App\Http\Controllers;

use App\Models\Contrat;
use App\Models\Paiement;
use App\Services\FiscalService;
use App\Services\PaiementService;
use App\Services\QuittanceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

/**
 * PaiementController — Enregistrement et validation des paiements de loyer.
 *
 * Ce contrôleur est un dispatcher : il reçoit la requête, autorise,
 * délègue la logique aux Services, et retourne la réponse.
 *
 * AUCUN calcul fiscal ici. Tout est dans FiscalService / PaiementService.
 */
class PaiementController extends Controller
{
    public function __construct(
        private readonly FiscalService    $fiscalService,
        private readonly PaiementService  $paiementService,
        private readonly QuittanceService $quittanceService
    ) {}

    /**
     * Liste des paiements d'un contrat.
     * GET /contrats/{contrat}/paiements
     */
    public function index(Contrat $contrat): View
    {
        $this->authorize('view', $contrat);

        $paiements = $contrat->paiements()
            ->with('quittance')
            ->orderBy('date_echeance')
            ->get();

        // Calcul du récapitulatif fiscal pour l'affichage
        $decomposition = $this->fiscalService->calculerDecompositionLoyer(
            $contrat->loyer_hors_charges,
            $contrat->charges ?? 0
        );

        return view('paiements.index', compact('contrat', 'paiements', 'decomposition'));
    }

    /**
     * Formulaire de saisie d'un paiement.
     * GET /paiements/{paiement}/encaisser
     */
    public function encaisserForm(Paiement $paiement): View
    {
        $this->authorize('create', Paiement::class);

        $paiement->load('contrat.bien', 'contrat.locataire');

        $decomposition = $this->fiscalService->calculerDecompositionLoyer(
            $paiement->loyer_hors_charges,
            $paiement->charges ?? 0
        );

        return view('paiements.encaisser', compact('paiement', 'decomposition'));
    }

    /**
     * Enregistrement du paiement effectif.
     * POST /paiements/{paiement}/encaisser
     */
    public function encaisser(Request $request, Paiement $paiement): RedirectResponse
    {
        $this->authorize('valider', $paiement);

        $validated = $request->validate([
            'montant_recu'   => ['required', 'numeric', 'min:1'],
            'date_reglement' => ['required', 'date', 'before_or_equal:today'],
            'mode_paiement'  => ['required', 'in:especes,virement,wave,orange_money,cheque'],
            'commentaire'    => ['nullable', 'string', 'max:500'],
        ]);

        $paiementValide = $this->paiementService->enregistrerReglement(
            paiement:      $paiement,
            montantRecu:   (float) $validated['montant_recu'],
            dateReglement: $validated['date_reglement'],
            modePaiement:  $validated['mode_paiement']
        );

        // Génération automatique de la quittance après validation
        $quittance = $this->quittanceService->generer($paiementValide);

        return redirect()
            ->route('contrats.show', $paiement->contrat_id)
            ->with('success', "Paiement enregistré. Quittance #{$quittance->numero} générée.");
    }

    /**
     * Téléchargement d'une quittance PDF.
     * GET /paiements/{paiement}/quittance
     */
    public function telechargerQuittance(Paiement $paiement): Response
    {
        $this->authorize('telechargerQuittance', $paiement);

        if (! $this->quittanceService->existePourPaiement($paiement)) {
            abort(404, 'Aucune quittance disponible pour ce paiement.');
        }

        $quittance = $paiement->quittance;
        $pdf       = $this->quittanceService->genererPdf($quittance);
        $nomFichier = $this->quittanceService->nomFichier($quittance);

        return $pdf->download($nomFichier);
    }

    /**
     * Tableau de bord des impayés.
     * GET /paiements/impayes
     */
    public function impayes(): View
    {
        $this->authorize('admin-agence');

        $agencyId = Auth::user()->agency_id;

        $impayes  = $this->paiementService->getImpayes($agencyId);
        $prochain = $this->paiementService->getEcheancesProchaines($agencyId, 7);
        $bilan    = $this->paiementService->getTableauDeBordMensuel(
            $agencyId,
            now()->year,
            now()->month
        );

        return view('paiements.impayes', compact('impayes', 'prochain', 'bilan'));
    }
}