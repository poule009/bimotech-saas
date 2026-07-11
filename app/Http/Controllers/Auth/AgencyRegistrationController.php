<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Agency;
use App\Models\Subscription;
use App\Models\User;
// use App\Notifications\AgencyWelcomeNotification;
use App\Support\PasswordPolicy;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AgencyRegistrationController extends Controller
{
    public function create(): View
    {
        return view('auth.register-agency');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'agency_name'    => ['required', 'string', 'min:2', 'max:100'],
            'admin_name'     => ['required', 'string', 'min:2', 'max:100'],
            'admin_email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'admin_password' => ['required', 'confirmed', PasswordPolicy::rules()],
            'cgu'            => ['required', 'accepted'],
        ], [
            'agency_name.required'     => "Le nom de l'agence est obligatoire.",
            'agency_name.min'          => "Le nom de l'agence doit contenir au moins 2 caractères.",
            'admin_name.required'      => "Votre nom est obligatoire.",
            'admin_email.required'     => "L'email est obligatoire.",
            'admin_email.email'        => "L'email n'est pas valide.",
            'admin_email.unique'       => "Cet email est déjà utilisé.",
            'admin_password.required'  => "Le mot de passe est obligatoire.",
            'admin_password.confirmed' => "Les deux mots de passe ne correspondent pas.",
            'admin_password.mixed'     => "Le mot de passe doit contenir majuscule et minuscule.",
            'admin_password.numbers'   => "Le mot de passe doit contenir au moins un chiffre.",
            'admin_password.symbols'   => "Le mot de passe doit contenir un caractère spécial (@, !, #…).",
            'cgu.accepted'             => "Vous devez accepter les conditions d'utilisation.",
        ]);

        try {
            $admin = DB::transaction(function () use ($request) {
                $agency            = new Agency();
                $agency->name      = $request->agency_name;
                $agency->email     = $request->admin_email;
                $agency->slug      = Str::slug($request->agency_name) . '-' . Str::random(6);
                $agency->actif     = true;
                $agency->save();

                $admin            = new User();
                $admin->name      = $request->admin_name;
                $admin->email     = $request->admin_email;
                $admin->password  = \Illuminate\Support\Facades\Hash::make($request->admin_password);
                $admin->role      = 'admin';
                $admin->is_owner  = true;
                $admin->agency_id = $agency->id;
                $admin->save();

                Subscription::create([
                    'agency_id'        => $agency->id,
                    'statut'           => 'essai',
                    'plan_niveau'      => 'starter',   // plan d'entrée par défaut (20 biens / 2 comptes)
                    'date_debut_essai' => now(),
                    'date_fin_essai'   => now()->addDays(30),
                ]);

                return $admin;
            });

            event(new Registered($admin));
            Auth::login($admin);

            return redirect()->route('redirect.home');

        } catch (\Throwable $e) {
            Log::error('Erreur inscription agence', ['error' => $e->getMessage()]);
            return back()->withInput()->withErrors([
                'general' => 'Une erreur est survenue. Veuillez réessayer.',
            ]);
        }
    }
}