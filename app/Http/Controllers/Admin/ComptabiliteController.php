<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChargeAgence;
use App\Models\ReversementProprietaire;
use App\Services\ComptabiliteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ComptabiliteController extends Controller
{
    public function __construct(private ComptabiliteService $comptabiliteService) {}

    public function dashboard(Request $request): View
    {
        $agencyId = Auth::user()->agency_id;
        $annee    = (int) $request->get('annee', now()->year);
        $mois     = (int) $request->get('mois', now()->month);

        $resultat       = $this->comptabiliteService->compteResultat($agencyId, $annee, $mois);
        $soldesMandants = $this->comptabiliteService->soldesMandants($agencyId);
        $sixMois        = $this->comptabiliteService->grapheSixMois($agencyId);

        // Propriétaires avec solde en attente
        $proprietairesIds = $soldesMandants->where('solde', '>', 0)->pluck('proprietaire_id');
        $proprietairesEnAttente = \App\Models\User::whereIn('id', $proprietairesIds)->get()
            ->map(function ($u) use ($soldesMandants) {
                $u->solde = $soldesMandants->firstWhere('proprietaire_id', $u->id)['solde'] ?? 0;
                return $u;
            });

        return view('admin.comptabilite.dashboard', compact(
            'resultat', 'soldesMandants', 'sixMois',
            'proprietairesEnAttente', 'annee', 'mois'
        ));
    }

    public function compteResultat(Request $request): View
    {
        $agencyId = Auth::user()->agency_id;
        $annee    = (int) $request->get('annee', now()->year);
        $mois     = $request->get('mois') ? (int) $request->get('mois') : null;

        $resultat = $this->comptabiliteService->compteResultat($agencyId, $annee, $mois);

        $annees = range(now()->year, max(now()->year - 3, 2024));

        return view('admin.comptabilite.compte-resultat', compact('resultat', 'annee', 'mois', 'annees'));
    }

    public function tresorerie(Request $request): View
    {
        $agencyId = Auth::user()->agency_id;
        $annee    = (int) $request->get('annee', now()->year);
        $mois     = (int) $request->get('mois', now()->month);

        $tresorerie = $this->comptabiliteService->tresorerie($agencyId, $annee, $mois);

        return view('admin.comptabilite.tresorerie', compact('tresorerie', 'annee', 'mois'));
    }
}
