<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBienRequest;
use App\Http\Requests\UpdateBienRequest;
use App\Models\Agency;
use App\Models\Bien;
use App\Models\Immeuble;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BienController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request): View
    {
        $this->authorize('isStaff');

        // Liste unifiée : biens simples (standalone) + immeubles-conteneurs (avec occupation).
        // Les unités d'un immeuble ne s'affichent PAS ici — elles sont visibles dans la fiche immeuble.
        $filter = in_array($request->input('filter'), ['simples', 'immeubles'], true)
            ? $request->input('filter')
            : null;
        $q = trim((string) $request->input('q', ''));

        // ── Biens simples ──────────────────────────────────────────────────
        $biensSimples = collect();
        if ($filter !== 'immeubles') {
            $bq = Bien::standalone()
                ->select(['id', 'agency_id', 'proprietaire_id', 'immeuble_id', 'reference',
                          'titre', 'type', 'adresse', 'quartier', 'ville', 'statut', 'loyer_mensuel'])
                ->with('proprietaire:id,name');

            if ($q !== '') {
                $bq->where(function ($sub) use ($q) {
                    $sub->where('reference', 'like', "%{$q}%")
                        ->orWhere('titre', 'like', "%{$q}%")
                        ->orWhere('adresse', 'like', "%{$q}%")
                        ->orWhere('ville', 'like', "%{$q}%")
                        ->orWhere('quartier', 'like', "%{$q}%")
                        ->orWhereHas('proprietaire', fn ($p) => $p->where('name', 'like', "%{$q}%"));
                });
            }
            $biensSimples = $bq->latest()->paginate(24)->withQueryString();
        }

        // ── Immeubles avec occupation ──────────────────────────────────────
        $immeubles = collect();
        if ($filter !== 'simples') {
            $iq = Immeuble::select(['id', 'agency_id', 'proprietaire_id', 'nom', 'adresse', 'ville', 'nombre_niveaux'])
                ->with('proprietaire:id,name')
                ->withCount([
                    'biens',
                    'biens as loues_count' => fn ($x) => $x->where('statut', 'loue'),
                ]);

            if ($q !== '') {
                $iq->where(function ($sub) use ($q) {
                    $sub->where('nom', 'like', "%{$q}%")
                        ->orWhere('adresse', 'like', "%{$q}%")
                        ->orWhere('ville', 'like', "%{$q}%")
                        ->orWhereHas('proprietaire', fn ($p) => $p->where('name', 'like', "%{$q}%"));
                });
            }
            $immeubles = $iq->orderBy('nom')->get();
        }

        $counts = [
            'simples'   => Bien::standalone()->count(),
            'immeubles' => Immeuble::count(),
        ];
        $counts['total'] = $counts['simples'] + $counts['immeubles'];

        return view('biens.index', compact('biensSimples', 'immeubles', 'filter', 'q', 'counts'));
    }

    /**
     * Recherche JSON des biens disponibles — composant « Rechercher-ou-Créer »
     * du formulaire Contrat. `fill` = loyer de référence pour pré-remplir le loyer.
     */
    public function searchDisponibles(Request $request)
    {
        $this->authorize('isStaff');

        $agencyId = Auth::user()->agency_id;
        $q = trim((string) $request->query('q'));

        $biens = Bien::where('agency_id', $agencyId)
            ->where('statut', 'disponible')
            ->when($q !== '', fn ($query) => $query->where(fn ($s) => $s
                ->where('reference', 'like', "%{$q}%")
                ->orWhere('titre', 'like', "%{$q}%")
                ->orWhere('adresse', 'like', "%{$q}%")
                ->orWhere('ville', 'like', "%{$q}%")))
            ->with('proprietaire:id,name')
            ->orderBy('reference')
            ->limit(8)
            ->get();

        return response()->json($biens->map(fn (Bien $b) => [
            'id'       => $b->id,
            'name'     => $b->titre ?: $b->reference,
            'sub'      => 'Propriétaire : ' . ($b->proprietaire->name ?? '—'),
            'initials' => mb_strtoupper(mb_substr($b->reference, 0, 2)),
            'fill'     => (int) $b->loyer_mensuel,
            // Champs fiscaux exposés au formulaire Contrat (pré-remplissage TOM + aperçu TVA)
            'tom'      => (float) ($b->tom_mensuelle ?? 0),
            'meuble'   => (bool) $b->meuble,
        ]));
    }

    public function show(Bien $bien): View
    {
        $this->authorize('isStaff');

        $bien->load([
            'proprietaire:id,name,email,telephone,adresse',
            'contratActif.locataire:id,name,email,telephone',
            'contrats' => fn($q) => $q->latest()->limit(5)->with('locataire:id,name'),
            'photos',
        ]);

        $paiements = $bien->contratActif
            ? \App\Models\Paiement::where('contrat_id', $bien->contratActif->id)
                ->where('statut', 'valide')
                ->select([
                    'id', 'contrat_id', 'periode', 'date_paiement',
                    'montant_encaisse', 'net_proprietaire', 'net_a_verser_proprietaire',
                    'commission_ttc', 'mode_paiement', 'statut',
                ])
                ->orderByDesc('periode')
                ->limit(10)
                ->get()
            : collect();

        $raisonsAbsence = [];
        if ($bien->statut === 'disponible' && ! $bien->contratActif) {
            if (empty($bien->titre))    $raisonsAbsence[] = 'un titre';
            if (empty($bien->quartier)) $raisonsAbsence[] = 'un quartier';
            if (! $bien->photos->where('est_principale', true)->count())
                $raisonsAbsence[] = 'une photo principale';
            if (! ($bien->visible_portail ?? true))
                $raisonsAbsence[] = 'l\'activation portail (actuellement masqué manuellement)';
        }

        // ── CFPB estimée (Art. 283-294) — estimation STRUCTURELLE ────────────
        // Masquée si le propriétaire a opté pour la CGF sur l'année en cours
        // (exclusion mutuelle — la CGF regroupe déjà la CFPB). On lit le profil
        // sans global scope (le bien appartient déjà à l'agence courante).
        $annee            = now()->year;
        $profilProprio    = \App\Models\Proprietaire::withoutGlobalScopes()
            ->where('user_id', $bien->proprietaire_id)->first();
        $cfpbCouvertParCgf = $profilProprio && $profilProprio->cgfCouvre($annee);
        $cfpb = [
            'valeur_locative' => (int) $bien->cfpb_valeur_locative_estimee,
            'montant'         => (int) $bien->cfpb_montant_estime,
            'statut'          => $bien->cfpb_statut_calcul ?? \App\Services\FiscalService::CFPB_STATUT,
            // TEOM — même assiette, taux communal (affichée à côté de la CFPB).
            'teom_montant'    => (int) $bien->teom_montant_estime,
            'teom_taux'       => (float) $bien->teom_taux_applique,
        ];

        return view('biens.show', compact(
            'bien', 'paiements', 'raisonsAbsence', 'cfpb', 'cfpbCouvertParCgf', 'annee'
        ));
    }

    public function create(Request $request): View
    {
        $this->authorize('isStaff');

        $proprietaires = User::where('role', 'proprietaire')
            ->where('agency_id', Auth::user()->agency_id)
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        $immeubles = Immeuble::orderBy('nom')->get(['id', 'nom', 'ville', 'proprietaire_id']);

        $immeubleSelectionne = $request->filled('immeuble_id')
            ? $immeubles->firstWhere('id', (int) $request->immeuble_id)
            : null;

        return view('biens.create', compact('proprietaires', 'immeubles', 'immeubleSelectionne'));
    }

    public function store(StoreBienRequest $request): RedirectResponse
    {
        // authorize() et rules() gérés par StoreBienRequest
        $validated = $request->validated();

        $agencyId = Auth::user()->agency_id;

        // ── Limite d'unités selon le plan (source unique : Agency::limiteUnites) ──
        $agency       = Auth::user()->agency;
        $planNiveau   = $agency?->subscription?->plan_niveau ?? 'legacy';
        $limiteUnites = $agency?->limiteUnites();

        // Vérifier que le propriétaire appartient à l'agence courante
        $proprioValide = \App\Models\User::where('id', $validated['proprietaire_id'])
            ->where('agency_id', $agencyId)
            ->where('role', 'proprietaire')
            ->exists();

        if (! $proprioValide) {
            return back()
                ->withErrors(['proprietaire_id' => 'Ce propriétaire n\'appartient pas à votre agence.'])
                ->withInput();
        }

        $validated['agency_id']       = $agencyId;
        $validated['statut']          = 'disponible';
        $validated['reference']       = $this->genererReference();
        $validated['meuble']          = $request->boolean('meuble');
        $validated['parking']         = $request->boolean('parking');
        $validated['climatise']       = $request->boolean('climatise');
        $validated['tom_mensuelle']   = (float) ($validated['tom_mensuelle'] ?? 0);
        $validated['taux_commission'] = $validated['taux_commission'] ?? 10;
        $validated['amenites']        = $request->filled('amenites')
            ? array_values(array_filter(array_map('trim', explode(',', $request->amenites))))
            : null;

        // Contrôle du quota et insertion ATOMIQUES : un verrou sur la ligne agence
        // sérialise les créations concurrentes (évite qu'une double soumission
        // simultanée dépasse la limite du plan — TOCTOU).
        $nbUnites = 0;
        $bien = DB::transaction(function () use ($agency, $limiteUnites, $validated, &$nbUnites) {
            if ($agency && $limiteUnites !== null) {
                Agency::whereKey($agency->id)->lockForUpdate()->first();
                $nbUnites = $agency->nbUnitesActives();
                if ($nbUnites >= $limiteUnites) {
                    return null; // quota atteint → aucune création
                }
            }
            return Bien::create($validated);
        });

        if ($bien === null) {
            [$planSuivant, $limiteSuivante] = match ($planNiveau) {
                'starter' => ['Pro', '50 unités'],
                default   => ['Agence', 'illimité'],
            };

            return redirect()
                ->route('admin.biens.create')
                ->with('upgrade_required', [
                    'plan_actuel'     => config('plans.labels.' . $planNiveau, 'Pro'),
                    'nb_unites'       => $nbUnites,
                    'limite'          => $limiteUnites,
                    'plan_suivant'    => $planSuivant,
                    'limite_suivante' => $limiteSuivante,
                ])
                ->withInput();
        }

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $index => $photo) {
                \App\Models\BienPhoto::create([
                    'bien_id'        => $bien->id,
                    'chemin'         => $photo->store('biens/' . $bien->id, 'public'),
                    'nom_original'   => $photo->getClientOriginalName(),
                    'est_principale' => $index === 0,
                    'ordre'          => $index + 1,
                ]);
            }
        }

        return redirect()
            ->route('admin.biens.show', $bien)
            ->with('success', 'Bien créé avec succès.');
    }

    public function edit(Bien $bien): View
    {
        $this->authorize('isStaff');

        $proprietaires = User::where('role', 'proprietaire')
            ->where('agency_id', Auth::user()->agency_id)
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        return view('biens.edit', compact('bien', 'proprietaires'));
    }

    public function update(UpdateBienRequest $request, Bien $bien): RedirectResponse
    {
        // authorize() et rules() gérés par UpdateBienRequest
        $validated = $request->validated();

        // Vérification IDOR : empêche de rattacher ce bien à un propriétaire d'une autre agence
        $proprioValide = \App\Models\User::where('id', $validated['proprietaire_id'])
            ->where('agency_id', Auth::user()->agency_id)
            ->where('role', 'proprietaire')
            ->exists();

        if (! $proprioValide) {
            return back()
                ->withErrors(['proprietaire_id' => 'Ce propriétaire n\'appartient pas à votre agence.'])
                ->withInput();
        }

        // Bloquer le changement de statut si un contrat actif est en cours.
        // Le bien ne peut retrouver 'disponible' ou 'en_travaux' qu'après résiliation du contrat.
        $bien->loadMissing('contratActif');
        if ($bien->contratActif && $validated['statut'] !== 'loue') {
            return back()
                ->withErrors(['statut' => 'Ce bien est loué (contrat actif). Résiliez d\'abord le contrat avant de changer le statut.'])
                ->withInput();
        }

        $validated['meuble']          = $request->boolean('meuble');
        $validated['parking']         = $request->boolean('parking');
        $validated['climatise']       = $request->boolean('climatise');
        $validated['tom_mensuelle']   = (float) ($validated['tom_mensuelle'] ?? 0);
        $validated['visible_portail'] = $request->boolean('visible_portail');
        $validated['amenites']        = $request->filled('amenites')
            ? array_values(array_filter(array_map('trim', explode(',', $request->amenites))))
            : null;
        $bien->update($validated);

        return redirect()
            ->route('admin.biens.show', $bien)
            ->with('success', 'Bien mis à jour avec succès.');
    }

    public function destroy(Bien $bien): RedirectResponse
    {
        $this->authorize('isStaff');

        if ($bien->contratActif) {
            return redirect()
                ->route('admin.biens.show', $bien)
                ->with('error', 'Impossible de supprimer ce bien : un contrat actif est en cours.');
        }

        $bien->statut = 'archive';
        $bien->save();
        $bien->delete();

        return redirect()
            ->route('admin.biens.index')
            ->with('success', 'Bien archivé avec succès.');
    }

    private function genererReference(): string
    {
        return Bien::generateReference(Auth::user()->agency_id);
    }
}