<?php

namespace App\Http\Controllers;

use App\Models\Bien;
use App\Models\Contrat;
use App\Models\Locataire;
use App\Models\Paiement;
use App\Models\Proprietaire;
use App\Models\User;
use App\Services\BailleurPortfolioService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Support\PasswordPolicy;

class UserController extends Controller
{
    use AuthorizesRequests;

    public function __construct(private readonly BailleurPortfolioService $portfolioService)
    {
    }

    // ─────────────────────────────────────────────────────────────────────
    // HELPER PRIVÉ — Vérification appartenance cross-agence
    // ─────────────────────────────────────────────────────────────────────

    private function verifierAppartenance(User $user): void
    {
        /** @var \App\Models\User $authUser */
        $authUser = Auth::user();

        if ($authUser->isSuperAdmin()) {
            return;
        }

        if ($user->agency_id !== $authUser->agency_id) {
            abort(403, 'Accès refusé — cet utilisateur n\'appartient pas à votre agence.');
        }

        if (! in_array($user->role, ['proprietaire', 'locataire'])) {
            abort(404);
        }
    }

    // ─────────────────────────────────────────────────────────────────────
    // LISTE PROPRIÉTAIRES
    // ─────────────────────────────────────────────────────────────────────

    public function proprietaires()
    {
        $this->authorize('isAdmin');

        $agencyId = Auth::user()->agency_id;

        // Données financières via service (indexées par user_id)
        $portfolios = $this->portfolioService->getPortfolioIndex($agencyId)
            ->keyBy(fn($p) => $p['user']->id);

        // Tous les propriétaires de l'agence (y compris ceux sans biens)
        $usersQuery = User::where('role', 'proprietaire')
            ->where('agency_id', $agencyId)
            ->select(['id', 'agency_id', 'name', 'email', 'telephone', 'created_at'])
            ->with(['proprietaire:user_id,ville,ninea,mode_paiement_prefere']);

        if (($q = trim((string) request('q'))) !== '') {
            $usersQuery->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('telephone', 'like', "%{$q}%")
                    ->orWhereHas('proprietaire', fn ($p) => $p
                        ->where('ville', 'like', "%{$q}%")
                        ->orWhere('ninea', 'like', "%{$q}%"));
            });
        }

        $users = $usersQuery->orderBy('name')->get();

        // Fusion identité + financier
        $proprietaires = $users->map(function (User $user) use ($portfolios) {
            $p = $portfolios->get($user->id);
            return [
                'user'              => $user,
                'nb_biens'          => $p['nb_biens'] ?? 0,
                'nb_biens_loues'    => $p['nb_biens_loues'] ?? 0,
                'total_loyers'      => $p['total_loyers'] ?? 0,
                'total_commissions' => $p['total_commissions'] ?? 0,
                'total_depenses'    => $p['total_depenses'] ?? 0,
                'net_final'         => $p['net_final'] ?? 0,
                'nb_paiements'      => $p['nb_paiements'] ?? 0,
            ];
        });

        $stats = [
            'total'            => User::where('role', 'proprietaire')->where('agency_id', $agencyId)->count(),
            'total_biens'      => $portfolios->sum('nb_biens'),
            'biens_loues'      => $portfolios->sum('nb_biens_loues'),
            'total_loyers'     => $portfolios->sum('total_loyers'),
            'total_net'        => $portfolios->sum('net_final'),
        ];

        return view('users.proprietaires', compact('proprietaires', 'stats'));
    }

    // ─────────────────────────────────────────────────────────────────────
    // LISTE LOCATAIRES
    // ─────────────────────────────────────────────────────────────────────

    public function locataires()
    {
        $this->authorize('isAdmin');

        $agencyId = Auth::user()->agency_id;

        /**
         * PERFORMANCE — Eager loading sélectif :
         *
         * Avant : ->with('contrats.bien') chargeait TOUTES les colonnes de contrats
         * ET de biens pour chaque locataire, juste pour afficher la référence.
         *
         * Après : on charge uniquement les colonnes nécessaires à l'affichage,
         * avec withCount pour le nombre de contrats.
         */
        $locataires = User::where('role', 'locataire')
            ->where('agency_id', $agencyId)
            ->select(['id', 'agency_id', 'name', 'email', 'telephone', 'created_at'])
            ->with([
                'contrats' => fn($q) => $q
                    ->where('statut', 'actif')
                    ->select(['id', 'locataire_id', 'bien_id', 'statut', 'loyer_contractuel'])
                    ->with(['bien:id,reference,adresse,ville']),
                'locataire:user_id,est_entreprise,profession,employeur,revenu_mensuel',
            ])
            ->withCount([
                'contrats',
                'contrats as contrats_actifs_count' => fn($q) => $q->where('statut', 'actif'),
            ])
            ->orderBy('name')
            ->paginate(15);

        $stats = [
            'total'        => User::where('role', 'locataire')->where('agency_id', $agencyId)->count(),
            'actifs'       => User::where('role', 'locataire')
                                  ->where('agency_id', $agencyId)
                                  ->whereHas('contrats', fn($q) => $q->where('statut', 'actif'))
                                  ->count(),
            'sans_contrat' => User::where('role', 'locataire')
                                  ->where('agency_id', $agencyId)
                                  ->whereDoesntHave('contrats', fn($q) => $q->where('statut', 'actif'))
                                  ->count(),
        ];

        return view('users.locataires', compact('locataires', 'stats'));
    }

    // ─────────────────────────────────────────────────────────────────────
    // FORMULAIRE CRÉATION
    // ─────────────────────────────────────────────────────────────────────

    public function create(string $role)
    {
        $this->authorize('isAdmin');

        if (! in_array($role, ['proprietaire', 'locataire'])) {
            abort(404);
        }

        return view('users.create', compact('role'));
    }

    // ─────────────────────────────────────────────────────────────────────
    // ENREGISTREMENT
    // ─────────────────────────────────────────────────────────────────────

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('isAdmin');

        $role = $request->input('role');

        $validated = $request->validate([
            'role'      => ['required', 'in:proprietaire,locataire'],
            'name'      => ['required', 'string', 'max:255'],
            'email'     => $role === 'proprietaire'
                ? ['nullable', 'email', 'unique:users,email', 'max:255']
                : ['required', 'email', 'unique:users,email', 'max:255'],
            'telephone' => ['nullable', 'string', 'max:20'],
            'adresse'   => ['nullable', 'string', 'max:255'],
            'password'  => $role === 'proprietaire'
                ? ['nullable', 'required_with:email', 'confirmed', 'min:8']
                : ['required', 'confirmed', PasswordPolicy::rules()],
            // ── Identité commune ──────────────────────────────────────────
            'cni'            => ['nullable', 'string', 'max:20'],
            'date_naissance' => ['nullable', 'date'],
            'genre'          => ['nullable', 'in:M,F'],
            'nationalite'    => ['nullable', 'string', 'max:100'],
            'ville'          => ['nullable', 'string', 'max:100'],
            'quartier'       => ['nullable', 'string', 'max:100'],
            // ── Propriétaire ──────────────────────────────────────────────
            'mode_paiement_prefere' => ['nullable', 'in:especes,virement,wave,orange_money,free_money,cheque,mobile_money'],
            'banque'                => ['nullable', 'string', 'max:100'],
            'numero_compte'         => ['nullable', 'string', 'max:50'],
            'numero_wave'           => ['nullable', 'string', 'max:20'],
            'numero_om'             => ['nullable', 'string', 'max:20'],
            'ninea'                 => ['nullable', 'string', 'max:20'],
            // ── Locataire ─────────────────────────────────────────────────
            'type_locataire'   => ['nullable', 'in:particulier,entreprise,association,ambassade,ong'],
            'est_entreprise'   => ['nullable', 'boolean'],
            'nom_entreprise'   => ['nullable', 'string', 'max:255'],
            'ninea_locataire'  => ['nullable', 'string', 'max:30'],
            'rccm_locataire'   => ['nullable', 'string', 'max:60'],
            'taux_brs_override'=> ['nullable', 'numeric', 'min:0', 'max:20'],
            'profession'            => ['nullable', 'string', 'max:100'],
            'employeur'             => ['nullable', 'string', 'max:100'],
            'revenu_mensuel'        => ['nullable', 'numeric', 'min:0'],
            'contact_urgence_nom'   => ['nullable', 'string', 'max:100'],
            'contact_urgence_tel'   => ['nullable', 'string', 'max:20'],
            'contact_urgence_lien'  => ['nullable', 'string', 'max:50'],
        ], [
            'email.unique'             => 'Cet email est déjà utilisé par un autre compte.',
            'name.required'            => 'Le nom complet est obligatoire.',
            'genre.in'                 => 'Genre invalide.',
            'mode_paiement_prefere.in' => 'Mode de paiement invalide.',
            'type_locataire.in'        => 'Type de locataire invalide.',
        ]);

        // agency_id forcé côté serveur — jamais depuis le formulaire
        $user = DB::transaction(function () use ($validated, $request) {
            $user                    = new User();
            $user->name              = $validated['name'];
            $user->email             = $validated['email'] ?? null;
            $user->telephone         = $validated['telephone'] ?? null;
            $user->adresse           = $validated['adresse'] ?? null;
            $user->password          = ! empty($validated['password'])
                ? Hash::make($validated['password'])
                : Hash::make(\Illuminate\Support\Str::random(32));
            $user->role              = $validated['role'];
            $user->agency_id         = Auth::user()->agency_id;
            $user->email_verified_at = ! empty($validated['email']) ? now() : null;
            $user->save();

            if ($validated['role'] === 'proprietaire') {
                Proprietaire::create([
                    'user_id'               => $user->id,
                    'cni'                   => $validated['cni'] ?? null,
                    'date_naissance'        => $validated['date_naissance'] ?? null,
                    'genre'                 => $validated['genre'] ?? null,
                    'nationalite'           => $validated['nationalite'] ?? 'Sénégalaise',
                    'ville'                 => $validated['ville'] ?? 'Dakar',
                    'quartier'              => $validated['quartier'] ?? null,
                    'mode_paiement_prefere' => $validated['mode_paiement_prefere'] ?? 'virement',
                    'banque'                => $validated['banque'] ?? null,
                    'numero_compte'         => $validated['numero_compte'] ?? null,
                    'numero_wave'           => $validated['numero_wave'] ?? null,
                    'numero_om'             => $validated['numero_om'] ?? null,
                    'ninea'                 => $validated['ninea'] ?? null,
                ]);
            } else {
                Locataire::create([
                    'user_id'              => $user->id,
                    'cni'                  => $validated['cni'] ?? null,
                    'date_naissance'       => $validated['date_naissance'] ?? null,
                    'genre'                => $validated['genre'] ?? null,
                    'nationalite'          => $validated['nationalite'] ?? 'Sénégalaise',
                    'ville'                => $validated['ville'] ?? 'Dakar',
                    'quartier'             => $validated['quartier'] ?? null,
                    'type_locataire'       => $validated['type_locataire'] ?? 'particulier',
                    'est_entreprise'       => filter_var($request->input('est_entreprise'), FILTER_VALIDATE_BOOLEAN),
                    'nom_entreprise'       => $validated['nom_entreprise'] ?? null,
                    'ninea_locataire'      => $validated['ninea_locataire'] ?? null,
                    'rccm_locataire'       => $validated['rccm_locataire'] ?? null,
                    'taux_brs_override'    => $validated['taux_brs_override'] ?? null,
                    'profession'           => $validated['profession'] ?? null,
                    'employeur'            => $validated['employeur'] ?? null,
                    'revenu_mensuel'       => $validated['revenu_mensuel'] ?? null,
                    'contact_urgence_nom'  => $validated['contact_urgence_nom'] ?? null,
                    'contact_urgence_tel'  => $validated['contact_urgence_tel'] ?? null,
                    'contact_urgence_lien' => $validated['contact_urgence_lien'] ?? null,
                ]);
            }

            return $user;
        });

        if ($validated['role'] === 'proprietaire') {
            return redirect()
                ->route('admin.users.proprietaires')
                ->with('success', "Propriétaire {$user->name} créé ✓");
        }

        return redirect()
            ->route('admin.users.locataires')
            ->with('success', "Locataire {$user->name} créé ✓");
    }

    // ─────────────────────────────────────────────────────────────────────
    // FICHE DÉTAILLÉE
    // ─────────────────────────────────────────────────────────────────────

    public function show(User $user)
    {
        $this->authorize('isAdmin');
        $this->verifierAppartenance($user);

        $stats = [];

        if ($user->isProprietaire()) {
            // On récupère les IDs des contrats via une sous-requête
            $contratIds = Contrat::whereHas(
                'bien', fn($q) => $q->where('proprietaire_id', $user->id)
            )->pluck('id');

            /**
             * PERFORMANCE — Agrégats SQL groupés :
             * Une seule requête pour toutes les sommes au lieu de 5 count/sum séparés.
             */
            $aggr = Paiement::whereIn('contrat_id', $contratIds)
                ->where('statut', 'valide')
                ->selectRaw('
                    COALESCE(SUM(montant_encaisse), 0)          AS total_loyers,
                    COALESCE(SUM(net_a_verser_proprietaire), 0) AS total_net,
                    COALESCE(SUM(commission_ttc), 0)            AS total_commission,
                    COUNT(*)                                     AS nb_paiements
                ')
                ->first();

            $stats = [
                'nb_biens'         => Bien::where('proprietaire_id', $user->id)->count(),
                'nb_biens_loues'   => Bien::where('proprietaire_id', $user->id)->where('statut', 'loue')->count(),
                'total_loyers'     => (float) $aggr->total_loyers,
                'total_net'        => (float) $aggr->total_net,
                'total_commission' => (float) $aggr->total_commission,
                'nb_paiements'     => (int)   $aggr->nb_paiements,
            ];

            $biens = Bien::where('proprietaire_id', $user->id)
                ->select(['id', 'agency_id', 'proprietaire_id', 'reference', 'type', 'adresse', 'ville', 'statut', 'loyer_mensuel'])
                ->with([
                    'contratActif:id,bien_id,locataire_id,statut,loyer_contractuel,date_debut',
                    'contratActif.locataire:id,name',
                ])
                ->orderByDesc('created_at')
                ->paginate(5);

            $paiements = Paiement::whereIn('contrat_id', $contratIds)
                ->where('statut', 'valide')
                ->select(['id', 'agency_id', 'contrat_id', 'periode', 'montant_encaisse', 'net_proprietaire', 'net_a_verser_proprietaire', 'mode_paiement', 'date_paiement', 'reference_paiement'])
                ->with(['contrat:id,bien_id', 'contrat.bien:id,reference'])
                ->orderByDesc('date_paiement')
                ->paginate(10);

            $locatairesActifs = Contrat::whereIn('id', $contratIds)
                ->where('statut', 'actif')
                ->select(['id', 'bien_id', 'locataire_id', 'loyer_contractuel', 'date_debut'])
                ->with([
                    'locataire:id,name,email,telephone',
                    'bien:id,reference,adresse,ville',
                ])
                ->get();

            return view('users.show', compact(
                'user', 'biens', 'stats', 'paiements', 'locatairesActifs'
            ));
        }

        if ($user->isLocataire()) {
            $contrat = Contrat::where('locataire_id', $user->id)
                ->where('statut', 'actif')
                ->select(['id', 'bien_id', 'locataire_id', 'statut', 'loyer_contractuel', 'date_debut', 'date_fin'])
                ->with(['bien:id,reference,adresse,ville,type'])
                ->first();

            if ($contrat) {
                $aggrLoc = Paiement::where('contrat_id', $contrat->id)
                    ->where('statut', 'valide')
                    ->selectRaw('
                        COALESCE(SUM(montant_encaisse), 0) AS total_paye,
                        COUNT(*)                           AS nb_paiements
                    ')
                    ->first();

                $stats = [
                    'contrat_actif' => $contrat,
                    'nb_paiements'  => (int)   $aggrLoc->nb_paiements,
                    'total_paye'    => (float) $aggrLoc->total_paye,
                ];
            }
        }

        return view('users.show', compact('user', 'stats'));
    }

    // ─────────────────────────────────────────────────────────────────────
    // FORMULAIRE ÉDITION
    // ─────────────────────────────────────────────────────────────────────

    public function edit(User $user)
    {
        $this->authorize('isAdmin');
        $this->verifierAppartenance($user);

        $user->load('proprietaire', 'locataire');

        return view('users.edit', compact('user'));
    }

    // ─────────────────────────────────────────────────────────────────────
    // MISE À JOUR
    // ─────────────────────────────────────────────────────────────────────

     public function update(Request $request, User $user): RedirectResponse
{
    $this->authorize('isAdmin');
    $this->verifierAppartenance($user);

    // ── Champs User communs ───────────────────────────────────────────
    $validated = $request->validate([
        'name'      => ['required', 'string', 'max:255'],
        'email'     => ['required', 'email', 'unique:users,email,' . $user->id],
        'telephone' => ['nullable', 'string', 'max:30'],
        'adresse'   => ['nullable', 'string', 'max:255'],
    ], [
        'email.unique'  => 'Cet email est déjà utilisé par un autre compte.',
        'name.required' => 'Le nom complet est obligatoire.',
    ]);

    DB::transaction(function () use ($user, $validated, $request) {
        $user->update($validated);

    // ── Profil PROPRIÉTAIRE ───────────────────────────────────────────
    if ($user->isProprietaire() && $user->proprietaire) {
        $profilData = $request->validate([
            'cni'                   => ['nullable', 'string', 'max:20'],
            'date_naissance'        => ['nullable', 'date'],
            'genre'                 => ['nullable', 'in:M,F'],
            'nationalite'           => ['nullable', 'string', 'max:50'],
            'telephone_secondaire'  => ['nullable', 'string', 'max:30'],
            'adresse_domicile'      => ['nullable', 'string', 'max:255'],
            'ville'                 => ['nullable', 'string', 'max:100'],
            'quartier'              => ['nullable', 'string', 'max:100'],
            'mode_paiement_prefere' => ['nullable', 'in:especes,virement,wave,orange_money,free_money,cheque,mobile_money'],
            'banque'                => ['nullable', 'string', 'max:100'],
            'numero_compte'         => ['nullable', 'string', 'max:50'],
            'numero_wave'           => ['nullable', 'string', 'max:20'],
            'numero_om'             => ['nullable', 'string', 'max:20'],
            'ninea'                 => ['nullable', 'string', 'max:20'],
            'assujetti_tva'         => ['nullable', 'boolean'],
        ]);

        $profilData['assujetti_tva'] = $request->boolean('assujetti_tva');
        $user->proprietaire->update($profilData);
    }

    // ── Profil LOCATAIRE ─────────────────────────────────────────────
    if ($user->isLocataire() && $user->locataire) {
        $profilData = $request->validate([
            'cni'                  => ['nullable', 'string', 'max:20'],
            'date_naissance'       => ['nullable', 'date'],
            'genre'                => ['nullable', 'in:M,F'],
            'nationalite'          => ['nullable', 'string', 'max:50'],
            'ville'                => ['nullable', 'string', 'max:100'],
            'quartier'             => ['nullable', 'string', 'max:100'],
            'profession'           => ['nullable', 'string', 'max:100'],
            'employeur'            => ['nullable', 'string', 'max:150'],
            'revenu_mensuel'       => ['nullable', 'numeric', 'min:0'],
            'contact_urgence_nom'  => ['nullable', 'string', 'max:150'],
            'contact_urgence_tel'  => ['nullable', 'string', 'max:20'],
            'contact_urgence_lien' => ['nullable', 'string', 'max:50'],
            // Fiscal
            'type_locataire'       => ['nullable', 'in:particulier,entreprise,association,ambassade,ong'],
            'nom_entreprise'       => ['nullable', 'string', 'max:150'],
            'ninea_locataire'      => ['nullable', 'string', 'max:30'],
            'rccm_locataire'       => ['nullable', 'string', 'max:60'],
            'taux_brs_override'    => ['nullable', 'numeric', 'min:0', 'max:20'],
        ]);

        // est_entreprise déduit du type_locataire
        $profilData['est_entreprise'] = in_array(
            $profilData['type_locataire'] ?? 'particulier',
            ['entreprise', 'association']
        );

        // Si pas entreprise → effacer les infos entreprise
        if (! $profilData['est_entreprise']) {
            $profilData['nom_entreprise']    = null;
            $profilData['ninea_locataire']   = null;
            $profilData['rccm_locataire']    = null;
            $profilData['taux_brs_override'] = null;
        }

        $user->locataire->update($profilData);
        // LocataireObserver::updated() se déclenche automatiquement
        // si est_entreprise a changé → propage BRS aux contrats actifs
    }
    }); // fin DB::transaction

    return redirect()
        ->route('admin.users.show', $user)
        ->with('success', 'Profil mis à jour ✓');
}

    // ─────────────────────────────────────────────────────────────────────
    // SUPPRESSION
    // ─────────────────────────────────────────────────────────────────────

    public function destroy(User $user): RedirectResponse
    {
        $this->authorize('isAdmin');
        $this->verifierAppartenance($user);

        if ($user->isLocataire() && $user->contrats()->where('statut', 'actif')->exists()) {
            return back()->withErrors([
                'general' => 'Impossible de supprimer un locataire avec un contrat actif.',
            ]);
        }

        if ($user->isProprietaire() && $user->biens()->whereIn('statut', ['loue', 'disponible', 'en_travaux'])->exists()) {
            return back()->withErrors([
                'general' => 'Impossible de supprimer un propriétaire avec des biens actifs. Archivez tous ses biens avant de continuer.',
            ]);
        }

        $user->delete();

        return back()->with('success', 'Utilisateur supprimé ✓');
    }
}