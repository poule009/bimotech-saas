<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\CollaborateurInvitationNotification;
use App\Support\PasswordPolicy;
use App\Support\TeamAccess;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class EquipeController extends Controller
{
    public function index(): View
    {
        $this->authorize('voirEquipe');
        $agencyId = Auth::user()->agency_id;

        $collaborateurs = User::where('agency_id', $agencyId)
            ->where('role', 'admin')
            ->with('permissions')
            ->orderByDesc('is_owner')
            ->orderBy('name')
            ->get()
            ->map(function (User $u) {
                // Preset dérivé + statut pour l'affichage (badge + pastille).
                $u->preset_key    = $u->is_owner ? 'administrateur' : TeamAccess::detecterPreset(TeamAccess::niveauxUtilisateur($u));
                $u->est_en_attente = ! $u->is_owner && $u->must_change_password;
                return $u;
            });

        $limiteMax   = $this->limiteAdmins();
        $nbActuels   = $collaborateurs->count();
        $peutAjouter = $limiteMax === null || $nbActuels < $limiteMax;
        $peutGerer   = Auth::user()->can('gererEquipe');

        return view('equipe.index', compact(
            'collaborateurs', 'limiteMax', 'nbActuels', 'peutAjouter', 'peutGerer'
        ));
    }

    public function create(): View|RedirectResponse
    {
        $this->authorize('gererEquipe');

        if (! $this->peutAjouterCollaborateur()) {
            return redirect()->route('admin.equipe.index')
                ->with('error', 'Limite de comptes atteinte pour votre plan. Passez au plan supérieur pour en ajouter davantage.');
        }

        return view('equipe.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('gererEquipe');

        if (! $this->peutAjouterCollaborateur()) {
            return redirect()->route('admin.equipe.index')
                ->with('error', 'Limite de comptes atteinte pour votre plan.');
        }

        $agencyId = Auth::user()->agency_id;

        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'email'       => ['required', 'email', 'max:255', Rule::unique('users', 'email')->whereNull('deleted_at')],
            'telephone'   => ['nullable', 'string', 'max:20'],
            'password'    => ['required', PasswordPolicy::rules()],
            'preset_role' => ['required', 'string', Rule::in(array_keys(TeamAccess::PRESETS))],
        ], [
            'email.unique'       => 'Cet email est déjà utilisé par un compte actif.',
            'name.required'      => 'Le nom est obligatoire.',
            'email.required'     => "L'email est obligatoire.",
            'password.required'  => 'Le mot de passe temporaire est obligatoire.',
            'preset_role.required' => 'Choisissez un rôle de départ.',
        ]);

        // La validation ci-dessus n'a bloqué que les emails ACTIFs (whereNull deleted_at).
        // Un email peut encore appartenir à un compte SOFT-DELETED : on ne le réutilise
        // que s'il s'agit d'un collaborateur (role=admin) révoqué de CETTE agence — sinon
        // on refuse proprement (évite de convertir un ancien locataire en admin, et évite
        // la violation de l'index unique global sur users.email → 500).
        $trashed = User::onlyTrashed()->where('email', $validated['email'])->first();

        if ($trashed) {
            if ($trashed->agency_id === $agencyId && $trashed->role === 'admin') {
                $user = $trashed;
                $user->restore();
            } else {
                return back()->withInput()
                    ->withErrors(['email' => 'Cet email est déjà associé à un compte existant.']);
            }
        } else {
            $user = new User();
        }

        $user->name              = $validated['name'];
        $user->email             = $validated['email'];
        $user->telephone         = $validated['telephone'] ?? null;
        $user->password          = Hash::make($validated['password']);
        $user->must_change_password = true;
        $user->role              = 'admin';
        $user->is_owner          = false;
        $user->agency_id         = $agencyId;
        $user->email_verified_at = now();
        $user->save();

        // Applique le preset choisi → permissions fines.
        $user->syncRoles([]);
        $user->syncPermissions(TeamAccess::expand(TeamAccess::presetLevels($validated['preset_role'])));

        try {
            $user->notify(new CollaborateurInvitationNotification(
                agency:    Auth::user()->agency,
                invitePar: Auth::user()->name,
            ));
        } catch (\Throwable $e) {
            Log::warning('Email invitation collaborateur non envoyé', [
                'user_id' => $user->id, 'error' => $e->getMessage(),
            ]);
        }

        return redirect()
            ->route('admin.equipe.index')
            ->with('success', "{$user->name} a été ajouté à votre équipe.");
    }

    public function editPermissions(User $user): View
    {
        $this->authorize('gererEquipe');
        $this->assertModifiable($user);

        $levels = TeamAccess::niveauxUtilisateur($user);

        return view('equipe.permissions', [
            'user'    => $user,
            'modules' => TeamAccess::MODULES,
            'levels'  => $levels,
            'preset'  => TeamAccess::detecterPreset($levels),
            'presets' => TeamAccess::PRESET_LABELS,
        ]);
    }

    public function updatePermissions(Request $request, User $user): RedirectResponse
    {
        $this->authorize('gererEquipe');
        $this->assertModifiable($user);

        // Un preset explicite (bouton « réappliquer ») prime ; sinon on lit la matrice.
        $preset = $request->input('preset_role');
        if ($preset && array_key_exists($preset, TeamAccess::PRESETS)) {
            $levels = TeamAccess::presetLevels($preset);
        } else {
            $levels = TeamAccess::normaliserNiveaux($request->input('niveaux', []));
        }

        $user->syncRoles([]);
        $user->syncPermissions(TeamAccess::expand($levels));

        return redirect()
            ->route('admin.equipe.permissions', $user)
            ->with('success', 'Accès de ' . $user->name . ' mis à jour.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->authorize('gererEquipe');

        if ($user->agency_id !== Auth::user()->agency_id || $user->role !== 'admin') {
            abort(403);
        }
        if ($user->is_owner) {
            return redirect()->route('admin.equipe.index')
                ->with('error', "Impossible de révoquer le directeur de l'agence.");
        }
        if ($user->id === Auth::id()) {
            return redirect()->route('admin.equipe.index')
                ->with('error', 'Vous ne pouvez pas révoquer votre propre accès.');
        }

        $nom = $user->name;
        // Soft-delete : coupe la session à la requête suivante (Laravel re-récupère
        // l'utilisateur en base à chaque requête, SoftDeletes l'exclut → logout).
        $user->delete();

        return redirect()
            ->route('admin.equipe.index')
            ->with('success', "L'accès de {$nom} a été révoqué.");
    }

    // ── Gardes ────────────────────────────────────────────────────────────────

    /** Cible modifiable : même agence, collaborateur (admin non-directeur), jamais soi-même. */
    private function assertModifiable(User $user): void
    {
        $auth = Auth::user();
        abort_if(
            $user->agency_id !== $auth->agency_id
            || $user->role !== 'admin'
            || $user->is_owner
            || $user->id === $auth->id,   // anti-escalade : on n'édite pas ses propres droits
            403
        );
    }

    // ── Limites de plan ─────────────────────────────────────────────────────────

    private function limiteAdmins(): ?int
    {
        $planNiveau = Auth::user()->agency?->subscription?->plan_niveau ?? 'starter';
        $effectif   = config("plans.niveau_effectif.{$planNiveau}", 'starter');
        return config("plans.nb_admins_max.{$effectif}");
    }

    private function peutAjouterCollaborateur(): bool
    {
        $limite = $this->limiteAdmins();
        if ($limite === null) return true;

        $nbActuels = User::where('agency_id', Auth::user()->agency_id)
            ->where('role', 'admin')
            ->count();

        return $nbActuels < $limite;
    }
}
