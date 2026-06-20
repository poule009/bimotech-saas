<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChargeAgence;
use App\Models\DepenseGestion;
use App\Models\Paiement;
use App\Models\ReversementProprietaire;
use App\Models\User;
use App\Services\ComptabiliteService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ComptabiliteController extends Controller implements HasMiddleware
{
    public function __construct(private ComptabiliteService $comptabiliteService) {}

    public static function middleware(): array
    {
        return [
            new Middleware('check.feature:comptabilite', only: ['index']),
        ];
    }

    /**
     * Module Comptabilité — page unique à 3 onglets (Vue d'ensemble / Propriétaires / Agence).
     * Réutilise ComptabiliteService : aucun calcul fiscal ici.
     */
    public function index(Request $request): View
    {
        $agencyId = Auth::user()->agency_id;
        $annee    = (int) $request->get('annee', now()->year);
        $mois     = (int) $request->get('mois', now()->month);
        $periode  = sprintf('%04d-%02d', $annee, $mois);

        // ── Vue d'ensemble : trésorerie + résultat du mois ──
        $tresorerie = $this->comptabiliteService->tresorerie($agencyId, $annee, $mois);
        $resultat   = $this->comptabiliteService->compteResultat($agencyId, $annee, $mois);

        // ── Onglet Propriétaires : une ligne par propriétaire actif ce mois ──
        $proprioActifsIds = Paiement::withoutGlobalScopes()
            ->where('agency_id', $agencyId)
            ->where('statut', 'valide')
            ->whereYear('date_paiement', $annee)
            ->whereMonth('date_paiement', $mois)
            ->with('contrat.bien:id,proprietaire_id')
            ->get()
            ->pluck('contrat.bien.proprietaire_id')
            ->filter()
            ->unique();

        $proprietaires = User::whereIn('id', $proprioActifsIds)
            ->where('agency_id', $agencyId)
            ->orderBy('name')
            ->get();

        $lignesProprietaires = $proprietaires->map(function (User $prop) use ($agencyId, $periode) {
            return [
                'proprietaire' => $prop,
                'compte'       => $this->comptabiliteService->compteMandant($agencyId, $prop->id, $periode),
            ];
        });

        $proprietairesAPayer = $lignesProprietaires->filter(fn($l) => $l['compte']['solde_restant'] > 0);
        $totalAPayer         = (float) $proprietairesAPayer->sum(fn($l) => $l['compte']['solde_restant']);

        // ── Onglet Agence : dépenses de l'agence du mois ──
        $chargesAgence = ChargeAgence::where('agency_id', $agencyId)
            ->where('periode', $periode)
            ->orderByDesc('date_charge')
            ->get();

        $pourcentageGarde = $resultat['revenus_total_ht'] > 0
            ? (int) round($resultat['resultat_net'] / $resultat['revenus_total_ht'] * 100)
            : 0;

        // ── Vue d'ensemble : dernières opérations (loyers reçus + dépenses agence) ──
        $derniersPaiements = Paiement::withoutGlobalScopes()
            ->where('agency_id', $agencyId)
            ->where('statut', 'valide')
            ->whereYear('date_paiement', $annee)
            ->whereMonth('date_paiement', $mois)
            ->with('contrat.bien:id,titre,reference')
            ->orderByDesc('date_paiement')
            ->limit(8)
            ->get()
            ->map(fn(Paiement $p) => [
                'date'    => $p->date_paiement,
                'libelle' => $p->contrat?->bien?->titre ?: ('Bien ' . ($p->contrat?->bien?->reference ?? '')),
                'type'    => 'Loyer reçu',
                'montant' => (float) $p->montant_encaisse,
                'sens'    => 'in',
            ]);

        $dernieresOperations = $chargesAgence->map(fn(ChargeAgence $c) => [
                'date'    => $c->date_charge,
                'libelle' => $c->libelle,
                'type'    => 'Dépense agence',
                'montant' => (float) $c->montant,
                'sens'    => 'out',
            ])
            ->concat($derniersPaiements)
            ->sortByDesc('date')
            ->take(8)
            ->values();

        // ── Formulaire « ajouter une dépense » : paiements imputables à un propriétaire ──
        $paiementsImputables = Paiement::withoutGlobalScopes()
            ->where('agency_id', $agencyId)
            ->where('statut', 'valide')
            ->whereYear('date_paiement', $annee)
            ->whereMonth('date_paiement', $mois)
            ->with(['contrat.bien:id,titre,reference,proprietaire_id', 'contrat.bien.proprietaire:id,name'])
            ->orderByDesc('date_paiement')
            ->get()
            ->map(fn(Paiement $p) => [
                'id'    => $p->id,
                'label' => ($p->contrat?->bien?->proprietaire?->name ?? 'Propriétaire')
                            . ' — ' . ($p->contrat?->bien?->titre ?: ('Bien ' . ($p->contrat?->bien?->reference ?? '')))
                            . ' (' . optional($p->date_paiement)->format('d/m/Y') . ')',
            ]);

        $categoriesAgence  = ChargeAgence::CATEGORIES;
        $categoriesProprio = DepenseGestion::CATEGORIES;
        $modesPaiement     = ReversementProprietaire::MODES_PAIEMENT;

        return view('admin.comptabilite.index', compact(
            'annee', 'mois', 'periode',
            'tresorerie', 'resultat',
            'lignesProprietaires', 'proprietairesAPayer', 'totalAPayer',
            'chargesAgence', 'pourcentageGarde',
            'dernieresOperations',
            'paiementsImputables', 'categoriesAgence', 'categoriesProprio', 'modesPaiement'
        ));
    }
}
