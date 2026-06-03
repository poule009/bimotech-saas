<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Agency;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Throwable $e) {
            Log::warning('Google OAuth callback échoué', ['error' => $e->getMessage()]);
            return redirect()->route('agency.register')
                ->withErrors(['google' => 'Connexion Google échouée. Veuillez réessayer.']);
        }

        // Compte existant avec ce google_id → connexion directe
        $user = User::where('google_id', $googleUser->getId())->first();

        if (! $user) {
            // Compte existant avec cet email → lier le google_id et connecter
            $user = User::where('email', $googleUser->getEmail())->first();
            if ($user) {
                $user->google_id = $googleUser->getId();
                $user->save();
            }
        }

        if ($user) {
            Auth::login($user, true);
            request()->session()->regenerate();
            return redirect()->route('redirect.home');
        }

        // Nouvel utilisateur → stocker en session et demander le nom d'agence
        session([
            'google_registration' => [
                'name'      => $googleUser->getName(),
                'email'     => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
            ],
        ]);

        return redirect()->route('agency.register.google.complete');
    }

    public function showComplete(): \Illuminate\View\View|RedirectResponse
    {
        if (! session('google_registration')) {
            return redirect()->route('agency.register');
        }

        return view('auth.register-google-complete', [
            'googleName' => session('google_registration.name'),
        ]);
    }

    public function storeComplete(Request $request): RedirectResponse
    {
        $googleData = session('google_registration');

        if (! $googleData) {
            return redirect()->route('agency.register');
        }

        $request->validate([
            'agency_name' => ['required', 'string', 'min:2', 'max:100'],
            'cgu'         => ['required', 'accepted'],
        ], [
            'agency_name.required' => "Le nom de l'agence est obligatoire.",
            'agency_name.min'      => "Le nom de l'agence doit contenir au moins 2 caractères.",
            'cgu.accepted'         => "Vous devez accepter les conditions d'utilisation.",
        ]);

        // Vérifier que l'email n'est pas déjà pris (race condition)
        if (User::where('email', $googleData['email'])->exists()) {
            session()->forget('google_registration');
            return redirect()->route('login')
                ->withErrors(['email' => 'Un compte existe déjà avec cette adresse Google. Connectez-vous.']);
        }

        try {
            $admin = DB::transaction(function () use ($request, $googleData) {
                $agency        = new Agency();
                $agency->name  = $request->agency_name;
                $agency->email = $googleData['email'];
                $agency->slug  = Str::slug($request->agency_name) . '-' . Str::random(6);
                $agency->actif = true;
                $agency->save();

                $admin                    = new User();
                $admin->name              = $googleData['name'];
                $admin->email             = $googleData['email'];
                $admin->google_id         = $googleData['google_id'];
                $admin->password          = Hash::make(Str::random(32));
                $admin->email_verified_at = now();
                $admin->role              = 'admin';
                $admin->is_owner          = true;
                $admin->agency_id         = $agency->id;
                $admin->save();

                Subscription::create([
                    'agency_id'        => $agency->id,
                    'statut'           => 'essai',
                    'date_debut_essai' => now(),
                    'date_fin_essai'   => now()->addDays(30),
                ]);

                return $admin;
            });

            session()->forget('google_registration');
            Auth::login($admin, true);
            request()->session()->regenerate();

            return redirect()->route('redirect.home');

        } catch (\Throwable $e) {
            Log::error('Erreur inscription Google', ['error' => $e->getMessage()]);
            return back()->withErrors(['general' => 'Une erreur est survenue. Veuillez réessayer.']);
        }
    }
}
