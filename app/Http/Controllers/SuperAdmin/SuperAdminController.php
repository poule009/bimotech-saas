<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Agency;
use App\Models\Bien;
use App\Models\Contrat;
use App\Models\Paiement;
use App\Models\Subscription;
use App\Models\User;
use App\Notifications\AgencyWelcomeNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class SuperAdminController extends Controller
{
    // ── Dashboard global ──────────────────────────────────────────────────

    public function dashboard(): View
    {
        $stats = [
            'nb_agences'            => Agency::count(),
            'nb_agences_actives'    => Agency::where('actif', true)->count(),
            'nb_users'              => User::withoutGlobalScopes()->count(),
            'nb_biens'              => Bien::withoutGlobalScopes()->count(),
            'nb_contrats'           => Contrat::withoutGlobalScopes()
                                         ->where('statut', 'actif')->count(),
            'total_loyers'          => Paiement::withoutGlobalScopes()
                                         ->where('statut', 'valide')
                                         ->sum('montant_encaisse'),
            'total_commissions'     => Paiement::withoutGlobalScopes()
                                         ->where('statut', 'valide')
                                         ->sum('commission_ttc'),
            'nb_essai'              => Subscription::where('statut', 'essai')->count(),
            'nb_abonnements_actifs' => Subscription::where('statut', 'actif')->count(),
            'nb_expires'            => Subscription::where('statut', 'expiré')->count(),
            'revenus_abonnements'   => Subscription::where('statut', 'actif')->sum('montant_paye'),
        ];

        $agences = Agency::withCount([
                'users',
                'biens',
                'contrats' => fn($q) => $q->where('statut', 'actif'),
            ])
            ->with(['users:id,agency_id,role', 'subscription'])
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($agency) {
                $agency->total_loyers = Paiement::withoutGlobalScopes()
                    ->where('agency_id', $agency->id)
                    ->where('statut', 'valide')
                    ->sum('montant_encaisse');

                $agency->total_commissions = Paiement::withoutGlobalScopes()
                    ->where('agency_id', $agency->id)
                    ->where('statut', 'valide')
                    ->sum('commission_ttc');

                $agency->nb_admins = $agency->users
                    ->where('role', 'admin')->count();

                return $agency;
            });

        return view('superadmin.dashboard', compact('stats', 'agences'));
    }

    // ── Activer / Désactiver une agence ───────────────────────────────────

    public function toggleActif(Agency $agency): RedirectResponse
    {
        $agency->update(['actif' => ! $agency->actif]);
        $statut = $agency->actif ? 'activée' : 'désactivée';

        return redirect()
            ->route('superadmin.dashboard')
            ->with('success', "L'agence {$agency->name} a été {$statut} avec succès.");
    }

    // ── Détail d'une agence ───────────────────────────────────────────────

    public function showAgency(Agency $agency): View
    {
        $users = User::withoutGlobalScopes()
            ->where('agency_id', $agency->id)
            ->orderBy('role')
            ->get();

        $biens = Bien::withoutGlobalScopes()
            ->where('agency_id', $agency->id)
            ->with('proprietaire')
            ->get();

        $subscription = $agency->subscription;

        $stats = [
            'nb_users'          => $users->count(),
            'nb_proprietaires'  => $users->where('role', 'proprietaire')->count(),
            'nb_locataires'     => $users->where('role', 'locataire')->count(),
            'nb_biens'          => $biens->count(),
            'nb_biens_loues'    => $biens->where('statut', 'loue')->count(),
            'nb_contrats'       => Contrat::withoutGlobalScopes()
                                    ->where('agency_id', $agency->id)
                                    ->where('statut', 'actif')->count(),
            'total_loyers'      => Paiement::withoutGlobalScopes()
                                    ->where('agency_id', $agency->id)
                                    ->where('statut', 'valide')
                                    ->sum('montant_encaisse'),
            'total_commissions' => Paiement::withoutGlobalScopes()
                                    ->where('agency_id', $agency->id)
                                    ->where('statut', 'valide')
                                    ->sum('commission_ttc'),
        ];

        return view('superadmin.agency-detail', compact(
            'agency', 'users', 'biens', 'stats', 'subscription'
        ));
    }

    // ── Dashboard abonnements ─────────────────────────────────────────────

    public function subscriptions(): View
    {
        $subscriptions = Subscription::with('agency')
            ->orderByRaw("FIELD(statut, 'essai', 'actif', 'expiré', 'annulé')")
            ->orderBy('date_fin_essai')
            ->get();

        $stats = [
            'nb_essai'              => $subscriptions->where('statut', 'essai')->count(),
            'nb_actifs'             => $subscriptions->where('statut', 'actif')->count(),
            'nb_expires'            => $subscriptions->where('statut', 'expiré')->count(),
            'revenus_total'         => $subscriptions->where('statut', 'actif')->sum('montant_paye'),
            'revenus_mensuel_equiv' => $subscriptions->where('statut', 'actif')->sum(function ($sub) {
                return match($sub->plan) {
                    'mensuel'     => 25000,
                    'trimestriel' => 25000,
                    'semestriel'  => 25000,
                    'annuel'      => 20000,
                    default       => 0,
                };
            }),
        ];

        return view('superadmin.subscriptions', compact('subscriptions', 'stats'));
    }

    // ── Activer manuellement un abonnement ────────────────────────────────

    public function activerAbonnement(Request $request, Agency $agency): RedirectResponse
    {
        $request->validate([
            'plan' => ['required', 'in:mensuel,trimestriel,semestriel,annuel'],
        ]);

        $subscription = $agency->subscription;

        if (! $subscription) {
            $subscription = Subscription::create([
                'agency_id'        => $agency->id,
                'statut'           => 'essai',
                'date_debut_essai' => now(),
                'date_fin_essai'   => now()->addDays(30),
            ]);
        }

        $subscription->activer(
            $request->plan,
            'MANUEL-SUPERADMIN-' . now()->format('YmdHis')
        );

        return redirect()
            ->route('superadmin.subscriptions')
            ->with('success', "Abonnement {$request->plan} activé pour {$agency->name}.");
    }

    // ── Réinitialiser l'essai d'une agence ───────────────────────────────

    public function reinitialiserEssai(Agency $agency): RedirectResponse
    {
        $subscription = $agency->subscription;

        if (! $subscription) {
            Subscription::create([
                'agency_id'        => $agency->id,
                'statut'           => 'essai',
                'date_debut_essai' => now(),
                'date_fin_essai'   => now()->addDays(30),
            ]);
        } else {
            $subscription->update([
                'statut'                => 'essai',
                'date_debut_essai'      => now(),
                'date_fin_essai'        => now()->addDays(30),
                'plan'                  => null,
                'montant_paye'          => null,
                'date_debut_abonnement' => null,
                'date_fin_abonnement'   => null,
            ]);
        }

        return redirect()
            ->route('superadmin.subscriptions')
            ->with('success', "Essai de 30 jours réinitialisé pour {$agency->name}.");
    }

    // ── Créer une agence (formulaire) ─────────────────────────────────────

    public function createAgency(): View
    {
        return view('superadmin.create-agency');
    }

    // ── Créer une agence (traitement) ─────────────────────────────────────

    public function storeAgency(Request $request): RedirectResponse
    {
        $request->validate([
            'agency_name'      => ['required', 'string', 'min:2', 'max:100'],
            'agency_email'     => ['required', 'email', 'max:255', 'unique:agencies,email'],
            'agency_telephone' => ['nullable', 'string', 'max:20'],
            'agency_adresse'   => ['nullable', 'string', 'max:255'],
            'admin_name'       => ['required', 'string', 'min:2', 'max:100'],
            'admin_email'      => ['required', 'email', 'max:255', 'unique:users,email'],
            'admin_password'   => ['required', 'confirmed', Password::min(8)],
        ], [
            'agency_name.required'     => "Le nom de l'agence est obligatoire.",
            'agency_name.min'          => "Le nom doit contenir au moins 2 caractères.",
            'agency_email.required'    => "L'email de l'agence est obligatoire.",
            'agency_email.email'       => "L'email de l'agence n'est pas valide.",
            'agency_email.unique'      => "Cet email est déjà utilisé par une autre agence.",
            'admin_name.required'      => "Le nom de l'administrateur est obligatoire.",
            'admin_email.required'     => "L'email de connexion est obligatoire.",
            'admin_email.email'        => "L'email de connexion n'est pas valide.",
            'admin_email.unique'       => "Cet email est déjà utilisé par un autre compte.",
            'admin_password.required'  => "Le mot de passe est obligatoire.",
            'admin_password.confirmed' => "Les deux mots de passe ne correspondent pas.",
            'admin_password.min'       => "Le mot de passe doit contenir au moins 8 caractères.",
        ]);

        $plainPassword = $request->admin_password;

        $result = DB::transaction(function () use ($request) {

            $slug = Str::slug($request->agency_name);
            $base = $slug;
            $i    = 1;
            while (Agency::where('slug', $slug)->exists()) {
                $slug = $base . '-' . $i++;
            }

            $agency = Agency::create([
                'name'      => $request->agency_name,
                'slug'      => $slug,
                'email'     => $request->agency_email,
                'telephone' => $request->agency_telephone,
                'adresse'   => $request->agency_adresse,
                'taux_tva'  => 18.00,
                'actif'     => true,
            ]);

            $admin = User::create([
                'agency_id'         => $agency->id,
                'name'              => $request->admin_name,
                'email'             => $request->admin_email,
                'password'          => bcrypt($request->admin_password),
                'role'              => 'admin',
                'email_verified_at' => now(),
            ]);

            Subscription::create([
                'agency_id'        => $agency->id,
                'statut'           => 'essai',
                'date_debut_essai' => now(),
                'date_fin_essai'   => now()->addDays(30),
            ]);

            return ['agency' => $agency, 'admin' => $admin];
        });

        // Envoyer l'email de bienvenue à l'admin de la nouvelle agence
        // Le SuperAdmin reste connecté — on n'appelle PAS Auth::login()
        try {
            $result['admin']->notify(
                new AgencyWelcomeNotification($result['agency'], $plainPassword)
            );
        } catch (\Exception $e) {
            Log::error('Email de bienvenue non envoyé : ' . $e->getMessage());
        }

        return redirect()
            ->route('superadmin.dashboard')
            ->with('success', "✅ Agence « {$result['agency']->name} » créée avec succès. L'admin ({$result['admin']->email}) a reçu ses identifiants par email.");
    }
}
