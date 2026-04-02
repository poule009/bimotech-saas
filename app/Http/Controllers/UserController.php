<?php

namespace App\Http\Controllers;

use App\Models\Bien;
use App\Models\Contrat;
use App\Models\Locataire;
use App\Models\Paiement;
use App\Models\Proprietaire;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    use AuthorizesRequests;

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

        /**
         * PERFORMANCE — select() + withCount :
         *
         * Avant : ->with('biens') chargeait TOUS les biens (toutes leurs colonnes)
         * juste pour afficher le nombre dans la liste.
         *
         * Après : withCount('biens') fait un COUNT(*) SQL en sous-requête.
         * Plus besoin de charger les objets Bien en mémoire.
         *
         * select() : on ne charge que les colonnes affichées dans la liste.
         */
        $proprietaires = User::where('role', 'proprietaire')
            ->where('agency_id', $agencyId)
            ->select(['id', 'agency_id', 'name', 'email', 'telephone', 'created_at'])
            ->with(['proprietaire:user_id,ville,ninea,mode_paiement_prefere'])
            ->withCount('biens')
            ->orderBy('name')
            ->paginate(15);

        // Agrégats SQL — pas de get() en mémoire
        $stats = [
            'total'       => User::where('role', 'proprietaire')->where('agency_id', $agencyId)->count(),
            'total_biens' => Bien::count(),
            'biens_loues' => Bien::where('statut', 'loue')->count(),
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

        $validated = $request->validate([
            'role'      => ['required', 'in:proprietaire,locataire'],
            'name'      => ['required', 'string', 'max:255'],
            'email'     => ['required', 'email', 'unique:users,email'],
            'telephone' => ['nullable', 'string', 'max:20'],
            'adresse'   => ['nullable', 'string', 'max:255'],
            'password'  => ['required', 'confirmed', Password::min(8)],
            'cni'            => ['nullable', 'string', 'max:20'],
            'date_naissance' => ['nullable', 'date'],
            'genre'          => ['nullable', 'in:M,F'],
            'ville'          => ['nullable', 'string', 'max:100'],
            'quartier'       => ['nullable', 'string', 'max:100'],
            'mode_paiement_prefere' => ['nullable', 'in:especes,virement,wave,orange_money,free_money,cheque,mobile_money'],
            'banque'                => ['nullable', 'string', 'max:100'],
            'numero_wave'           => ['nullable', 'string', 'max:20'],
            'numero_om'             => ['nullable', 'string', 'max:20'],
            'ninea'                 => ['nullable', 'string', 'max:20'],
            'profession'            => ['nullable', 'string', 'max:100'],
            'employeur'             => ['nullable', 'string', 'max:100'],
            'revenu_mensuel'        => ['nullable', 'numeric', 'min:0'],
            'contact_urgence_nom'   => ['nullable', 'string', 'max:100'],
            'contact_urgence_tel'   => ['nullable', 'string', 'max:20'],
            'contact_urgence_lien'  => ['nullable', 'string', 'max:50'],
        ], [
            'email.unique'   => 'Cet email est déjà utilisé par un autre compte.',
            'name.required'  => 'Le nom complet est obligatoire.',
            'genre.in'       => 'Genre invalide (M ou F).',
            'mode_paiement_prefere.in' => 'Mode de paiement invalide.',
        ]);

        // agency_id forcé côté serveur — jamais depuis le formulaire
        $user           = new User();
        $user->name     = $validated['name'];
        $user->email    = $validated['email'];
        $user->telephone = $validated['telephone'] ?? null;
        $user->adresse  = $validated['adresse'] ?? null;
        $user->password = Hash::make($validated['password']);
        $user->role     = $validated['role'];
        $user->agency_id         = Auth::user()->agency_id;
        $user->email_verified_at = now();
        $user->save();

        if ($validated['role'] === 'proprietaire') {
            Proprietaire::create([
                'user_id'               => $user->id,
                'cni'                   => $validated['cni'] ?? null,
                'date_naissance'        => $validated['date_naissance'] ?? null,
                'genre'                 => $validated['genre'] ?? null,
                'ville'                 => $validated['ville'] ?? 'Dakar',
                'quartier'              => $validated['quartier'] ?? null,
                'mode_paiement_prefere' => $validated['mode_paiement_prefere'] ?? 'virement',
                'banque'                => $validated['banque'] ?? null,
                'numero_wave'           => $validated['numero_wave'] ?? null,
                'numero_om'             => $validated['numero_om'] ?? null,
                'ninea'                 => $validated['ninea'] ?? null,
            ]);

            return redirect()
                ->route('admin.users.proprietaires')
                ->with('success', "Propriétaire {$user->name} créé ✓");
        }

        Locataire::create([
            'user_id'              => $user->id,
            'cni'                  => $validated['cni'] ?? null,
            'date_naissance'       => $validated['date_naissance'] ?? null,
            'genre'                => $validated['genre'] ?? null,
            'ville'                => $validated['ville'] ?? 'Dakar',
            'quartier'             => $validated['quartier'] ?? null,
            'profession'           => $validated['profession'] ?? null,
            'employeur'            => $validated['employeur'] ?? null,
            'revenu_mensuel'       => $validated['revenu_mensuel'] ?? null,
            'contact_urgence_nom'  => $validated['contact_urgence_nom'] ?? null,
            'contact_urgence_tel'  => $validated['contact_urgence_tel'] ?? null,
            'contact_urgence_lien' => $validated['contact_urgence_lien'] ?? null,
        ]);

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
                    COALESCE(SUM(montant_encaisse), 0) AS total_loyers,
                    COALESCE(SUM(net_proprietaire), 0) AS total_net,
                    COALESCE(SUM(commission_ttc), 0)   AS total_commission,
                    COUNT(*)                            AS nb_paiements
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
                ->select(['id', 'agency_id', 'contrat_id', 'periode', 'montant_encaisse', 'net_proprietaire', 'mode_paiement', 'date_paiement', 'reference_paiement'])
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

        $validated = $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'email'     => ['required', 'email', 'unique:users,email,' . $user->id],
            'telephone' => ['nullable', 'string', 'max:30'],
            'adresse'   => ['nullable', 'string', 'max:255'],
        ], [
            'email.unique'  => 'Cet email est déjà utilisé par un autre compte.',
            'name.required' => 'Le nom complet est obligatoire.',
        ]);

        $user->update($validated);

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
                'assujetti_tva'         => ['boolean'],
            ]);
            $user->proprietaire->update($profilData);
        }

        if ($user->isLocataire() && $user->locataire) {
            $profilData = $request->validate([
                'cni'            => ['nullable', 'string', 'max:20'],
                'date_naissance' => ['nullable', 'date'],
                'genre'          => ['nullable', 'in:M,F'],
                'nationalite'    => ['nullable', 'string', 'max:50'],
                'profession'     => ['nullable', 'string', 'max:100'],
                'employeur'      => ['nullable', 'string', 'max:150'],
                'revenu_mensuel' => ['nullable', 'numeric', 'min:0'],
                'ville'          => ['nullable', 'string', 'max:100'],
                'quartier'       => ['nullable', 'string', 'max:100'],
            ]);
            $user->locataire->update($profilData);
        }

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', 'Utilisateur mis à jour ✓');
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

        if ($user->isProprietaire() && $user->biens()->where('statut', 'loue')->exists()) {
            return back()->withErrors([
                'general' => 'Impossible de supprimer un propriétaire avec des biens loués.',
            ]);
        }

        $user->delete();

        return back()->with('success', 'Utilisateur supprimé ✓');
    }
}