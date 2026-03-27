<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Agency;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AgencyRegistrationController extends Controller
{
    // ── Affiche le formulaire d'inscription ───────────────────────────────

    public function create(): View
    {
        return view('auth.register-agency');
    }

    // ── Traite le formulaire d'inscription ────────────────────────────────

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            // Infos de l'agence
            'agency_name'      => ['required', 'string', 'min:2', 'max:100'],
            'agency_email'     => ['required', 'email', 'max:255', 'unique:agencies,email'],
            'agency_telephone' => ['nullable', 'string', 'max:20'],
            'agency_adresse'   => ['nullable', 'string', 'max:255'],

            // Infos de l'admin de l'agence
            'admin_name'       => ['required', 'string', 'min:2', 'max:100'],
            'admin_email'      => ['required', 'email', 'max:255', 'unique:users,email'],
            'admin_password'   => [
                'required',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],

            // Conditions d'utilisation
            'cgu' => ['required', 'accepted'],

        ], [
            // ── Messages agence ───────────────────────────────────────────
            'agency_name.required'     => "Le nom de l'agence est obligatoire.",
            'agency_name.min'          => "Le nom de l'agence doit contenir au moins 2 caractères.",
            'agency_name.max'          => "Le nom de l'agence ne peut pas dépasser 100 caractères.",
            'agency_email.required'    => "L'email de l'agence est obligatoire.",
            'agency_email.email'       => "L'email de l'agence n'est pas valide.",
            'agency_email.unique'      => "Cet email est déjà utilisé par une autre agence.",

            // ── Messages admin ────────────────────────────────────────────
            'admin_name.required'      => "Le nom de l'administrateur est obligatoire.",
            'admin_name.min'           => "Le nom doit contenir au moins 2 caractères.",
            'admin_email.required'     => "L'email de connexion est obligatoire.",
            'admin_email.email'        => "L'email de connexion n'est pas valide.",
            'admin_email.unique'       => "Cet email est déjà utilisé par un autre compte.",

            // ── Messages mot de passe (remplacement complet en français) ──
            'admin_password.required'  => "Le mot de passe est obligatoire.",
            'admin_password.confirmed' => "Les deux mots de passe ne correspondent pas.",
            'admin_password.min'       => "Le mot de passe doit contenir au moins 8 caractères.",
            'admin_password.mixed'     => "Le mot de passe doit contenir au moins une majuscule et une minuscule.",
            'admin_password.numbers'   => "Le mot de passe doit contenir au moins un chiffre.",
            'admin_password.symbols'   => "Le mot de passe doit contenir au moins un caractère spécial (ex: @, !, #).",

            // ── CGU ───────────────────────────────────────────────────────
            'cgu.required'             => "Vous devez accepter les conditions d'utilisation.",
            'cgu.accepted'             => "Vous devez accepter les conditions d'utilisation.",
        ]);

        // On utilise une transaction pour garantir que si une étape échoue,
        // rien n'est enregistré en base (ni l'agence, ni l'admin)
        $result = DB::transaction(function () use ($request) {

            // 1. Créer l'agence
            $agency = Agency::create([
                'name'      => $request->agency_name,
                'slug'      => $this->generateUniqueSlug($request->agency_name),
                'email'     => $request->agency_email,
                'telephone' => $request->agency_telephone,
                'adresse'   => $request->agency_adresse,
                'taux_tva'  => 18.00,
                'actif'     => true,
            ]);

            // 2. Créer l'admin rattaché à cette agence
            $admin = User::create([
                'agency_id' => $agency->id,
                'name'      => $request->admin_name,
                'email'     => $request->admin_email,
                'password'  => bcrypt($request->admin_password),
                'role'      => 'admin',
            ]);

            return ['agency' => $agency, 'admin' => $admin];
        });

        // 3. Connecter l'admin automatiquement après inscription
        Auth::login($result['admin']);

        // 4. Déclencher l'événement Laravel standard (utile pour les notifications)
        event(new Registered($result['admin']));

        // 5. Rediriger vers le dashboard admin de sa nouvelle agence
        return redirect()
            ->route('admin.dashboard')
            ->with('success', "Bienvenue ! Votre agence {$result['agency']->name} est prête.");
    }

    // ── Génère un slug unique à partir du nom de l'agence ─────────────────

    private function generateUniqueSlug(string $name): string
    {
        $baseSlug = Str::slug($name);
        $slug     = $baseSlug;
        $counter  = 1;

        while (Agency::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}
// ```

// ---

// Retourne sur le formulaire et reteste. Les messages d'erreur doivent maintenant apparaître en français. Un mot de passe valide ressemble à ceci :
// ```
// MonAgence@2025