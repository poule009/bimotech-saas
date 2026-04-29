<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BilanFiscalProprietaire;
use App\Models\User;
use App\Services\FiscalService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * BilanFiscalController — Gestion des bilans fiscaux annuels par propriétaire.
 *
 * Permet à l'agence de :
 *  1. Lister les bilans de tous ses propriétaires (par année)
 *  2. Calculer/recalculer un bilan annuel
 *  3. Afficher le détail d'un bilan avec abattement 30%, IRPP, CFPB
 *  4. Exporter le bilan en PDF pour transmission DGI
 */
class BilanFiscalController extends Controller
{
    use AuthorizesRequests;

    // ─────────────────────────────────────────────────────────────────────
    // LISTE — Bilans de tous les propriétaires pour une année
    // ─────────────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $this->authorize('isAdmin');

        $user     = Auth::user();
        $agencyId = $user->agency_id;
        $annee    = max(2000, min(2100, (int) $request->input('annee', now()->year)));

        // Propriétaires de l'agence — paginés pour éviter de charger des milliers
        // de lignes en mémoire sur des agences avec un grand portefeuille.
        // La vue doit appeler {{ $proprietaires->appends(request()->query())->links() }}
        // pour conserver le filtre 'annee' dans les liens de pagination.
        $proprietaires = User::where('agency_id', $agencyId)
            ->where('role', 'proprietaire')
            ->select(['id', 'name', 'email', 'telephone'])
            ->orderBy('name')
            ->paginate(30);

        // Bilans existants pour cette année
        $bilans = BilanFiscalProprietaire::where('agency_id', $agencyId)
            ->where('annee', $annee)
            ->get()
            ->keyBy('proprietaire_id');

        // Années disponibles (depuis les paiements)
        $anneesDisponibles = \App\Models\Paiement::where('statut', 'valide')
            ->selectRaw('YEAR(date_paiement) as annee')
            ->groupBy('annee')
            ->orderByDesc('annee')
            ->pluck('annee');

        return view('admin.bilans-fiscaux.index', compact(
            'proprietaires', 'bilans', 'annee', 'anneesDisponibles'
        ));
    }

    // ─────────────────────────────────────────────────────────────────────
    // CALCUL — Génère ou recalcule le bilan d'un propriétaire
    // ─────────────────────────────────────────────────────────────────────

    public function calculate(Request $request, User $proprietaire)
    {
        $this->authorize('isAdmin');

        $agencyId = Auth::user()->agency_id;

        // Vérification IDOR : le route model binding injecte n'importe quel User.
        // Sans ce contrôle, un admin d'une agence A pourrait déclencher le calcul
        // fiscal d'un propriétaire appartenant à une agence B.
        abort_if(
            $proprietaire->agency_id !== $agencyId || $proprietaire->role !== 'proprietaire',
            403,
            'Ce propriétaire n\'appartient pas à votre agence.'
        );

        $annee = (int) $request->input('annee', now()->year);

        $data = FiscalService::calculerBilanAnnuel($proprietaire->id, $annee, $agencyId);

        BilanFiscalProprietaire::updateOrCreate(
            [
                'agency_id'        => $agencyId,
                'proprietaire_id'  => $proprietaire->id,
                'annee'            => $annee,
            ],
            $data
        );

        return redirect()
            ->route('admin.bilans-fiscaux.show', [$proprietaire, 'annee' => $annee])
            ->with('success', "Bilan {$annee} calculé pour {$proprietaire->name} ✓");
    }

    // ─────────────────────────────────────────────────────────────────────
    // DÉTAIL — Affiche le bilan complet d'un propriétaire
    // ─────────────────────────────────────────────────────────────────────

    public function show(Request $request, User $proprietaire)
    {
        $this->authorize('isAdmin');

        $agencyId = Auth::user()->agency_id;

        // Vérification IDOR identique à calculate()
        abort_if(
            $proprietaire->agency_id !== $agencyId || $proprietaire->role !== 'proprietaire',
            403,
            'Ce propriétaire n\'appartient pas à votre agence.'
        );

        $annee = (int) $request->input('annee', now()->year);

        $bilan = BilanFiscalProprietaire::where('agency_id', $agencyId)
            ->where('proprietaire_id', $proprietaire->id)
            ->where('annee', $annee)
            ->first();

        // Si pas encore calculé, on le calcule à la volée
        if (! $bilan) {
            $data  = FiscalService::calculerBilanAnnuel($proprietaire->id, $annee, $agencyId);
            $bilan = BilanFiscalProprietaire::updateOrCreate(
                ['agency_id' => $agencyId, 'proprietaire_id' => $proprietaire->id, 'annee' => $annee],
                $data
            );
        }

        // Détail des paiements de l'année pour ce propriétaire
        $paiements = \App\Models\Paiement::query()
            ->join('contrats', 'contrats.id', '=', 'paiements.contrat_id')
            ->join('biens', 'biens.id', '=', 'contrats.bien_id')
            ->where('biens.proprietaire_id', $proprietaire->id)
            ->where('paiements.agency_id', $agencyId)
            ->where('paiements.statut', 'valide')
            ->whereYear('paiements.date_paiement', $annee)
            ->select('paiements.*', 'biens.reference as bien_reference', 'biens.meuble as bien_meuble', 'contrats.type_bail')
            ->with(['contrat.locataire:id,name'])
            ->orderBy('paiements.date_paiement')
            ->get();

        $anneesDisponibles = \App\Models\Paiement::query()
            ->join('contrats', 'contrats.id', '=', 'paiements.contrat_id')
            ->join('biens', 'biens.id', '=', 'contrats.bien_id')
            ->where('biens.proprietaire_id', $proprietaire->id)
            ->where('paiements.agency_id', $agencyId)
            ->where('paiements.statut', 'valide')
            ->selectRaw('YEAR(paiements.date_paiement) as annee')
            ->groupBy('annee')
            ->orderByDesc('annee')
            ->pluck('annee');

        return view('admin.bilans-fiscaux.show', compact(
            'proprietaire', 'bilan', 'annee', 'paiements', 'anneesDisponibles'
        ));
    }

    // ─────────────────────────────────────────────────────────────────────
    // EXPORT PDF — Bilan fiscal pour transmission DGI
    // ─────────────────────────────────────────────────────────────────────

    public function exportPdf(Request $request, User $proprietaire)
    {
        $this->authorize('isAdmin');

        $agencyId = Auth::user()->agency_id;

        // Vérification IDOR identique à calculate() et show()
        abort_if(
            $proprietaire->agency_id !== $agencyId || $proprietaire->role !== 'proprietaire',
            403,
            'Ce propriétaire n\'appartient pas à votre agence.'
        );

        $annee = (int) $request->input('annee', now()->year);

        $bilan = BilanFiscalProprietaire::where('agency_id', $agencyId)
            ->where('proprietaire_id', $proprietaire->id)
            ->where('annee', $annee)
            ->firstOrFail();

        $agency = Auth::user()->agency;

        $pdf = Pdf::loadView('admin.bilans-fiscaux.pdf.bilan', compact('bilan', 'proprietaire', 'agency', 'annee'))
            ->setPaper('a4', 'portrait')
            ->setOption('defaultFont', 'DejaVu Sans')
            ->setOption('dpi', 96)
            ->setOption('isRemoteEnabled', false);

        $filename = sprintf('bilan-fiscal-%s-%d.pdf', $proprietaire->id, $annee);

        return $pdf->download($filename);
    }
}