<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BilanFiscalProprietaire;
use App\Models\Contrat;
use App\Models\TvaDeclaration;
use App\Models\User;
use App\Services\FiscalService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class FiscalDashboardController extends Controller implements HasMiddleware
{
    use AuthorizesRequests;

    public static function middleware(): array
    {
        return [
            new Middleware('check.feature:fiscalite'),
        ];
    }

    public function dashboard(): View
    {
        $this->authorize('isAdmin');

        $agencyId = Auth::user()->agency_id;
        $annee    = now()->year;

        // ── KPIs bilans propriétaires ─────────────────────────────────────
        $bilans = BilanFiscalProprietaire::where('agency_id', $agencyId)
            ->where('annee', $annee)
            ->get();

        $nbProprietaires = User::where('agency_id', $agencyId)
            ->where('role', 'proprietaire')
            ->count();

        $kpiIrpp            = $bilans->sum('irpp_estime');
        $kpiBrs             = $bilans->sum('brs_retenu_total');
        $kpiEconomie        = $bilans->sum('economie_potentielle');
        $nbBilansCalcules   = $bilans->count();
        $nbBilansManquants  = $nbProprietaires - $nbBilansCalcules;

        // ── TVA du mois ───────────────────────────────────────────────────
        $tvaMois = TvaDeclaration::where('agency_id', $agencyId)
            ->where('annee', now()->year)
            ->where('mois', now()->month)
            ->first();

        // ── Déclarations TVA en retard ────────────────────────────────────
        $declarationsEnRetard = TvaDeclaration::where('agency_id', $agencyId)
            ->where('statut', '!=', 'deposee')
            ->get()
            ->filter(fn($d) => $d->est_en_retard)
            ->count();

        // ── Contrats expirant dans 30 jours ──────────────────────────────
        $contratsExpirant = Contrat::where('agency_id', $agencyId)
            ->where('statut', 'actif')
            ->whereNotNull('date_fin')
            ->whereBetween('date_fin', [now(), now()->addDays(30)])
            ->count();

        // ── Tableau propriétaires avec résumé fiscal ──────────────────────
        $proprietaires = User::where('agency_id', $agencyId)
            ->where('role', 'proprietaire')
            ->select(['id', 'name', 'email'])
            ->orderBy('name')
            ->get()
            ->map(function ($prop) use ($bilans) {
                $bilan = $bilans->firstWhere('proprietaire_id', $prop->id);
                $prop->bilan           = $bilan;
                $prop->irpp            = $bilan?->irpp_estime ?? 0;
                $prop->cgf             = $bilan?->cgf_montant ?? 0;
                $prop->regime          = $bilan?->regime_recommande;
                $prop->revenus         = $bilan?->revenus_bruts_total ?? 0;
                $prop->bilan_calcule   = $bilan !== null;
                return $prop;
            });

        return view('admin.fiscal.dashboard', compact(
            'annee', 'kpiIrpp', 'kpiBrs', 'kpiEconomie',
            'nbBilansCalcules', 'nbBilansManquants', 'nbProprietaires',
            'tvaMois', 'declarationsEnRetard', 'contratsExpirant',
            'proprietaires'
        ));
    }

    public function simuler(Request $request): View
    {
        $this->authorize('isAdmin');

        $resultat = null;

        if ($request->filled('loyer_ht')) {
            $loyerHt         = (float) $request->input('loyer_ht', 0);
            $charges         = (float) $request->input('charges', 0);
            $moisOccupes     = (int)   $request->input('mois_occupes', 12);
            $tauxCommission  = (float) $request->input('taux_commission', 10);
            $tauxTom         = (float) $request->input('taux_tom', 0);
            $typeBail        = $request->input('type_bail', 'habitation');
            $estPersonneMorale = (bool) $request->input('est_personne_morale', false);

            $svc = new FiscalService();
            // projeterBilanAnnuel attend des taux en décimal (0.10 = 10%)
            $projection = $svc->projeterBilanAnnuel($loyerHt, $charges, $moisOccupes, $tauxTom / 100, $tauxCommission / 100);

            $revenus = $projection['loyer_brut_annuel'];
            $cgf     = FiscalService::calculerCGF($revenus);
            $irpp    = FiscalService::calculerIRPP($revenus * 0.7);
            $irppDetail = FiscalService::calculerIRPPDetail($revenus * 0.7);
            $regimes = FiscalService::comparerRegimes($revenus, $irpp);

            // Personne physique → taux BRS légal (5%) ; personne morale IS → 0.
            // (Avant : tauxBrs(false) renvoyait toujours 0 — le simulateur affichait 0.)
            $tauxBrs = $estPersonneMorale ? 0 : FiscalService::BRS_TAUX_LEGAL;
            $brsAnnuel = $projection['net_proprietaire_annuel'] * ($tauxBrs / 100);

            $tvaApplicable = FiscalService::loyerEstAssujetti($typeBail, false);
            $tvaAnnuelle   = $tvaApplicable ? $projection['loyer_brut_annuel'] * 0.18 : 0;

            $resultat = [
                'projection'      => $projection,
                'revenus'         => $revenus,
                'base_imposable'  => $revenus * 0.7,
                'irpp'            => $irpp,
                'irpp_detail'     => $irppDetail,
                'cgf'             => $cgf,
                'regimes'         => $regimes,
                'brs_annuel'      => $brsAnnuel,
                'tva_annuelle'    => $tvaAnnuelle,
                'tva_applicable'  => $tvaApplicable,
            ];
        }

        return view('admin.fiscal.simulation', compact('resultat'));
    }
}
