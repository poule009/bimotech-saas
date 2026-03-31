<?php

namespace App\Http\Controllers;

use App\Models\Bien;
use App\Models\Contrat;
use App\Models\Locataire;
use App\Models\Paiement;
use App\Models\Proprietaire;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    // ── Liste des propriétaires ───────────────────────────────────────────
    public function proprietaires()
    {
        $this->authorize('isAdmin');
        $proprietaires = User::where('role', 'proprietaire')
            ->with('biens')
            ->withCount('biens')
            ->orderBy('name')
            ->paginate(15);

        $stats = [
            'total'        => User::where('role', 'proprietaire')->count(),
            'total_biens'  => \App\Models\Bien::count(),
            'biens_loues'  => \App\Models\Bien::where('statut', 'loue')->count(),
        ];

        return view('users.proprietaires', compact('proprietaires', 'stats'));
    }

    // ── Liste des locataires ──────────────────────────────────────────────
    public function locataires()
    {
        $this->authorize('isAdmin');
        $locataires = User::where('role', 'locataire')
            ->with('contrats.bien')
            ->orderBy('name')
            ->paginate(15);

        $stats = [
            'total'         => User::where('role', 'locataire')->count(),
            'actifs'        => User::where('role', 'locataire')
                ->whereHas('contrats', fn($q) => $q->where('statut', 'actif'))
                ->count(),
            'sans_contrat'  => User::where('role', 'locataire')
                ->whereDoesntHave('contrats', fn($q) => $q->where('statut', 'actif'))
                ->count(),
        ];

        return view('users.locataires', compact('locataires', 'stats'));
    }

    // ── Formulaire création ───────────────────────────────────────────────
    public function create(string $role)
    {
        $this->authorize('isAdmin');
        if (! in_array($role, ['proprietaire', 'locataire'])) {
            abort(404);
        }

        return view('users.create', compact('role'));
    }

    // ── Enregistrement ────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $this->authorize('isAdmin');
        $validated = $request->validate([
            'role'      => ['required', 'in:proprietaire,locataire'],
            'name'      => ['required', 'string', 'max:255'],
            'email'     => ['required', 'email', 'unique:users,email'],
            'telephone' => ['nullable', 'string', 'max:20'],
            'adresse'   => ['nullable', 'string', 'max:255'],
            'password'  => ['required', 'confirmed', Password::min(8)],

            // Champs profil communs
            'cni'            => ['nullable', 'string', 'max:20'],
            'date_naissance' => ['nullable', 'date'],
            'genre'          => ['nullable', 'in:M,F'],
            'ville'          => ['nullable', 'string', 'max:100'],
            'quartier'       => ['nullable', 'string', 'max:100'],

            // Champs propriétaire
            'mode_paiement_prefere' => ['nullable', 'in:especes,virement,wave,orange_money,free_money,cheque,mobile_money'],
            'banque'                => ['nullable', 'string', 'max:100'],
            'numero_wave'           => ['nullable', 'string', 'max:20'],
            'numero_om'             => ['nullable', 'string', 'max:20'],
            'ninea'                 => ['nullable', 'string', 'max:20'],

            // Champs locataire
            'profession'            => ['nullable', 'string', 'max:100'],
            'employeur'             => ['nullable', 'string', 'max:100'],
            'revenu_mensuel'        => ['nullable', 'numeric', 'min:0'],
            'contact_urgence_nom'   => ['nullable', 'string', 'max:100'],
            'contact_urgence_tel'   => ['nullable', 'string', 'max:20'],
            'contact_urgence_lien'  => ['nullable', 'string', 'max:50'],
        ]);

        // Créer le compte User (auto-vérifié : créé par l'admin, pas d'auto-inscription)
        $user = User::create([
            'name'              => $validated['name'],
            'email'             => $validated['email'],
            'telephone'         => $validated['telephone'] ?? null,
            'adresse'           => $validated['adresse'] ?? null,
            'password'          => Hash::make($validated['password']),
            'role'              => $validated['role'],
            'email_verified_at' => now(),
        ]);

        // Créer le profil selon le rôle
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

        if ($validated['role'] === 'locataire') {
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
    }

    // ── Fiche détaillée d'un propriétaire ────────────────────────────────
    public function show(User $user)
    {
        $this->authorize('isAdmin');

        // BUG 9 FIX : vérification cross-agence robuste.
        // User n'a pas d'AgencyScope → vérification manuelle obligatoire.
        // On utilise isSuperAdmin() (méthode du modèle) plutôt qu'une comparaison
        // de chaîne fragile, et Auth::user() plutôt que le FQCN inline.
        /** @var \App\Models\User $currentUser */
        $currentUser = Auth::user();
        if (! $currentUser->isSuperAdmin() && $currentUser->agency_id !== $user->agency_id) {
            abort(403, 'Cet utilisateur n\'appartient pas à votre agence.');
        }

    // Charge toutes les relations nécessaires
   $biens = Bien::where('proprietaire_id', $user->id)
    ->with(['contrats' => fn($q) => $q->where('statut', 'actif')
        ->with('locataire')])
    ->paginate(5);

$contratIds = Contrat::whereHas('bien', fn($q)
    => $q->where('proprietaire_id', $user->id)
)->pluck('id');

$paiements = Paiement::whereIn('contrat_id', $contratIds)
    ->where('statut', 'valide')
    ->with('contrat.bien', 'contrat.locataire')
    ->orderByDesc('date_paiement')
    ->paginate(10);

$stats = [
    'nb_biens'       => Bien::where('proprietaire_id', $user->id)->count(),
    'nb_biens_loues' => Bien::where('proprietaire_id', $user->id)
                            ->where('statut', 'loue')->count(),
    'nb_locataires'  => Contrat::whereIn('id', $contratIds)
                            ->where('statut', 'actif')
                            ->distinct('locataire_id')->count(),
    'total_loyers'   => Paiement::whereIn('contrat_id', $contratIds)
                            ->where('statut', 'valide')->sum('montant_encaisse'),
    'total_net'      => Paiement::whereIn('contrat_id', $contratIds)
                            ->where('statut', 'valide')->sum('net_proprietaire'),
    'total_commission'=> Paiement::whereIn('contrat_id', $contratIds)
                            ->where('statut', 'valide')->sum('commission_ttc'),
    'nb_paiements'   => Paiement::whereIn('contrat_id', $contratIds)
                            ->where('statut', 'valide')->count(),
];

$locatairesActifs = Contrat::whereIn('id', $contratIds)
    ->where('statut', 'actif')
    ->with('locataire', 'bien')
    ->get()
    ->map(fn($c) => [
        'contrat'   => $c,
        'locataire' => $c->locataire,
        'bien'      => $c->bien,
    ]);

return view('users.show', compact(
    'user', 'biens', 'stats', 'paiements', 'locatairesActifs'
));
}

    // ── Formulaire édition ────────────────────────────────────────────────
    public function edit(User $user)
    {
        $this->authorize('isAdmin');

        if (! in_array($user->role, ['proprietaire', 'locataire'])) {
            abort(404);
        }

        return view('users.edit', compact('user'));
    }

    // ── Mise à jour ───────────────────────────────────────────────────────
    public function update(Request $request, User $user)
    {
        $this->authorize('isAdmin');

        if (! in_array($user->role, ['proprietaire', 'locataire'])) {
            abort(404);
        }

        $validated = $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'email'     => ['required', 'email', 'unique:users,email,' . $user->id],
            'telephone' => ['nullable', 'string', 'max:30'],
            'adresse'   => ['nullable', 'string', 'max:255'],
        ]);

        $user->update($validated);

        // Mise à jour du profil selon le rôle
        if ($user->role === 'proprietaire' && $user->proprietaire) {
            $profilData = $request->validate([
                'cni'                   => ['nullable', 'string', 'max:20'],
                'date_naissance'        => ['nullable', 'date'],
                'genre'                 => ['nullable', 'in:M,F'],
                'nationalite'           => ['nullable', 'string', 'max:50'],
                'telephone_secondaire'  => ['nullable', 'string', 'max:30'],
                'adresse_domicile'      => ['nullable', 'string', 'max:255'],
                'ville'                 => ['nullable', 'string', 'max:100'],
                'quartier'              => ['nullable', 'string', 'max:100'],
                'mode_paiement_prefere' => ['nullable', 'in:especes,virement,wave,orange_money,free_money,cheque'],
                'banque'                => ['nullable', 'string', 'max:100'],
                'numero_compte'         => ['nullable', 'string', 'max:50'],
                'numero_wave'           => ['nullable', 'string', 'max:20'],
                'numero_om'             => ['nullable', 'string', 'max:20'],
                'ninea'                 => ['nullable', 'string', 'max:20'],
                'assujetti_tva'         => ['boolean'],
            ]);
            $user->proprietaire->update($profilData);
        }

        if ($user->role === 'locataire' && $user->locataire) {
            $profilData = $request->validate([
                'cni'                   => ['nullable', 'string', 'max:20'],
                'date_naissance'        => ['nullable', 'date'],
                'genre'                 => ['nullable', 'in:M,F'],
                'nationalite'           => ['nullable', 'string', 'max:50'],
                'profession'            => ['nullable', 'string', 'max:100'],
                'employeur'             => ['nullable', 'string', 'max:150'],
                'revenu_mensuel'        => ['nullable', 'numeric', 'min:0'],
                'ville'                 => ['nullable', 'string', 'max:100'],
                'quartier'              => ['nullable', 'string', 'max:100'],
            ]);
            $user->locataire->update($profilData);
        }

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', 'Utilisateur mis à jour ✓');
    }

    // ── Suppression ───────────────────────────────────────────────────────
    public function destroy(User $user)
    {
        $this->authorize('isAdmin');
        // Impossible de supprimer si contrat actif
        if ($user->contrats()->where('statut', 'actif')->exists()) {
            return back()->withErrors([
                'general' => 'Impossible de supprimer un locataire avec un contrat actif.'
            ]);
        }

        if ($user->biens()->where('statut', 'loue')->exists()) {
            return back()->withErrors([
                'general' => 'Impossible de supprimer un propriétaire avec des biens loués.'
            ]);
        }

        $user->delete(); // SoftDelete

        return back()->with('success', 'Utilisateur supprimé ✓');
    }
}