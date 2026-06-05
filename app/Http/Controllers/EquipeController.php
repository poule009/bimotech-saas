<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\CollaborateurInvitationNotification;
use App\Support\PasswordPolicy;
use Database\Seeders\PermissionsSeeder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;
use Illuminate\View\View;

class EquipeController extends Controller
{
    public function index(): View
    {
        $agencyId = Auth::user()->agency_id;

        $collaborateurs = User::where('agency_id', $agencyId)
            ->where('role', 'admin')
            ->with('roles')
            ->select(['id', 'name', 'email', 'telephone', 'is_owner', 'created_at', 'email_verified_at'])
            ->orderByDesc('is_owner')
            ->orderBy('name')
            ->get();

        $limiteMax   = $this->limiteAdmins();
        $nbActuels   = $collaborateurs->count();
        $peutAjouter = $limiteMax === null || $nbActuels < $limiteMax;

        return view('equipe.index', compact('collaborateurs', 'limiteMax', 'nbActuels', 'peutAjouter'));
    }

    public function create(): View|RedirectResponse
    {
        if (! $this->peutAjouterCollaborateur()) {
            return redirect()->route('admin.equipe.index')
                ->with('error', 'Limite de collaborateurs atteinte pour votre plan. Passez au plan supérieur pour en ajouter davantage.');
        }

        return view('equipe.create');
    }

    public function store(Request $request): RedirectResponse
    {
        if (! $this->peutAjouterCollaborateur()) {
            return redirect()->route('admin.equipe.index')
                ->with('error', 'Limite de collaborateurs atteinte pour votre plan.');
        }

        $validated = $request->validate([
            'name'       => ['required', 'string', 'max:255'],
            'email'      => ['required', 'email', 'unique:users,email', 'max:255'],
            'telephone'  => ['nullable', 'string', 'max:20'],
            'password'   => ['required', 'confirmed', PasswordPolicy::rules()],
            'preset_role'=> ['nullable', 'string', 'in:' . implode(',', array_keys(PermissionsSeeder::ROLES))],
        ], [
            'email.unique'   => 'Cet email est déjà utilisé par un autre compte.',
            'name.required'  => 'Le nom est obligatoire.',
            'email.required' => "L'email est obligatoire.",
        ]);

        $user                    = new User();
        $user->name              = $validated['name'];
        $user->email             = $validated['email'];
        $user->telephone         = $validated['telephone'] ?? null;
        $user->password          = Hash::make($validated['password']);
        $user->role              = 'admin';
        $user->is_owner          = false;
        $user->agency_id         = Auth::user()->agency_id;
        $user->email_verified_at = now();
        $user->save();

        // Appliquer le rôle prédéfini si choisi
        if (! empty($validated['preset_role'])) {
            $user->assignRole($validated['preset_role']);
        } else {
            // Gestionnaire par défaut
            $user->assignRole('gestionnaire');
        }

        try {
            if ($user->email) {
                $user->notify(new CollaborateurInvitationNotification(
                    agency:    Auth::user()->agency,
                    invitePar: Auth::user()->name,
                ));
            }
        } catch (\Throwable $e) {
            Log::warning('Email invitation collaborateur non envoyé', [
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
            ]);
        }

        return redirect()
            ->route('admin.equipe.index')
            ->with('success', "{$user->name} a été ajouté à votre équipe.");
    }

    public function editPermissions(User $user): View
    {
        $authUser = Auth::user();

        if ($user->agency_id !== $authUser->agency_id || $user->role !== 'admin' || $user->is_owner) {
            abort(403);
        }

        $permissions   = PermissionsSeeder::PERMISSIONS;
        $userPermNames = $user->getAllPermissions()->pluck('name')->toArray();
        $userRoleName  = $user->roles->first()?->name;
        $presetRoles   = PermissionsSeeder::ROLES;
        $roleLabels    = PermissionsSeeder::ROLE_LABELS;
        $moduleLabels  = PermissionsSeeder::MODULE_LABELS;
        $permLabels    = PermissionsSeeder::PERMISSION_LABELS;

        return view('equipe.permissions', compact(
            'user', 'permissions', 'userPermNames', 'userRoleName',
            'presetRoles', 'roleLabels', 'moduleLabels', 'permLabels'
        ));
    }

    public function updatePermissions(Request $request, User $user): RedirectResponse
    {
        $authUser = Auth::user();

        if ($user->agency_id !== $authUser->agency_id || $user->role !== 'admin' || $user->is_owner) {
            abort(403);
        }

        // Appliquer un rôle prédéfini si demandé
        if ($request->filled('preset_role') && array_key_exists($request->preset_role, PermissionsSeeder::ROLES)) {
            $user->syncRoles([$request->preset_role]);
            $user->syncPermissions(PermissionsSeeder::ROLES[$request->preset_role]);

            return redirect()
                ->route('admin.equipe.permissions', $user)
                ->with('success', 'Profil "' . PermissionsSeeder::ROLE_LABELS[$request->preset_role] . '" appliqué à ' . $user->name . '.');
        }

        // Sinon mise à jour manuelle des permissions cochées
        $allPerms   = collect(PermissionsSeeder::PERMISSIONS)->flatten()->toArray();
        $checked    = $request->input('permissions', []);
        $toSync     = array_intersect($checked, $allPerms);

        $user->syncRoles([]);
        $user->syncPermissions($toSync);

        return redirect()
            ->route('admin.equipe.permissions', $user)
            ->with('success', 'Permissions de ' . $user->name . ' mises à jour.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $authUser = Auth::user();

        if ($user->agency_id !== $authUser->agency_id || $user->role !== 'admin') {
            abort(403);
        }

        if ($user->is_owner) {
            return redirect()->route('admin.equipe.index')
                ->with('error', 'Impossible de supprimer le directeur de l\'agence.');
        }

        if ($user->id === $authUser->id) {
            return redirect()->route('admin.equipe.index')
                ->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        $nom = $user->name;
        $user->delete();

        return redirect()
            ->route('admin.equipe.index')
            ->with('success', "{$nom} a été retiré de l'équipe.");
    }

    // ── Helpers privés ────────────────────────────────────────────────────────

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
