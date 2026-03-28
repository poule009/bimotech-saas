<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Agency;
use App\Models\Subscription;
use App\Models\User;
use App\Notifications\AgencyWelcomeNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
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
            'agency_name'      => ['required', 'string', 'min:2', 'max:100'],
            'agency_email'     => ['required', 'email', 'max:255', 'unique:agencies,email'],
            'agency_telephone' => ['nullable', 'string', 'max:20'],
            'agency_adresse'   => ['nullable', 'string', 'max:255'],
            'admin_name'       => ['required', 'string', 'min:2', 'max:100'],
            'admin_email'      => ['required', 'email', 'max:255', 'unique:users,email'],
            'admin_password'   => [
                'required',
                'confirmed',
                Password::min(8)->mixedCase()->numbers()->symbols(),
            ],
            'cgu' => ['required', 'accepted'],
        ], [
            'agency_name.required'     => "Le nom de l'agence est obligatoire.",
            'agency_name.min'          => "Le nom de l'agence doit contenir au moins 2 caractères.",
            'agency_email.required'    => "L'email de l'agence est obligatoire.",
            'agency_email.email'       => "L'email de l'agence n'est pas valide.",
            'agency_email.unique'      => "Cet email est déjà utilisé par une autre agence.",
            'admin_name.required'      => "Le nom de l'administrateur est obligatoire.",
            'admin_email.required'     => "L'email de connexion est obligatoire.",
            'admin_email.email'        => "L'email de connexion n'est pas valide.",
            'admin_email.unique'       => "Cet email est déjà utilisé par un autre compte.",
            'admin_password.required'  => "Le mot de passe est obligatoire.",
            'admin_password.confirmed' => "Les deux mots de passe ne correspondent pas.",
            'admin_password.mixed'     => "Le mot de passe doit contenir au moins une majuscule et une minuscule.",
            'admin_password.numbers'   => "Le mot de passe doit contenir au moins un chiffre.",
            'admin_password.symbols'   => "Le mot de passe doit contenir au moins un caractère spécial (ex: @, !, #).",
            'cgu.accepted'             => "Vous devez accepter les conditions d'utilisation.",
        ]);

        $plainPassword = $request->admin_password;

        $result = DB::transaction(function () use ($request) {

            $agency = Agency::create([
                'name'      => $request->agency_name,
                'slug'      => $this->generateUniqueSlug($request->agency_name),
                'email'     => $request->agency_email,
                'telephone' => $request->agency_telephone,
                'adresse'   => $request->agency_adresse,
                'taux_tva'  => 18.00,
                'actif'     => true,
            ]);

            $admin = User::create([
                'agency_id' => $agency->id,
                'name'      => $request->admin_name,
                'email'     => $request->admin_email,
                'password'  => bcrypt($request->admin_password),
                'role'      => 'admin',
                // email_verified_at est null → email non vérifié
            ]);

            Subscription::create([
                'agency_id'        => $agency->id,
                'statut'           => 'essai',
                'date_debut_essai' => now(),
                'date_fin_essai'   => now()->addDays(30),
            ]);

            return ['agency' => $agency, 'admin' => $admin];
        });

        // Connecter l'admin
        Auth::login($result['admin']);

        // Déclencher l'événement Registered — envoie l'email de vérification
        // ET notre email de bienvenue personnalisé
        event(new Registered($result['admin']));

        // Envoyer notre email de bienvenue avec mot de passe + lien vérification
        try {
            $result['admin']->notify(
                new AgencyWelcomeNotification($result['agency'], $plainPassword)
            );
        } catch (\Exception $e) {
            Log::error('Email de bienvenue non envoyé : ' . $e->getMessage());
        }

        // Rediriger vers la page de vérification email
        return redirect()
            ->route('verification.notice')
            ->with('success', "Bienvenue ! Votre essai gratuit de 30 jours commence aujourd'hui. Vérifiez votre email pour activer votre compte.");
    }

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