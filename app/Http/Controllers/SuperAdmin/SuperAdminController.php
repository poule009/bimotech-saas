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
use App\Support\PasswordPolicy;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SuperAdminController extends Controller
{
    // ✅ CORRECTION M3 : 11 requêtes → 3 requêtes grâce aux selectRaw groupés
    public function dashboard(): View
    {
        // 1 requête pour les compteurs d'entités
        $statsEntites = DB::selectOne('
            SELECT
                (SELECT COUNT(*) FROM agencies)                          AS nb_agences,
                (SELECT COUNT(*) FROM agencies WHERE actif = 1)         AS nb_agences_actives,
                (SELECT COUNT(*) FROM users WHERE deleted_at IS NULL)   AS nb_users,
                (SELECT COUNT(*) FROM biens WHERE deleted_at IS NULL)   AS nb_biens,
                (SELECT COUNT(*) FROM contrats WHERE statut = "actif")  AS nb_contrats
        ');

        // 1 requête pour les totaux paiements
        $statsPaiements = Paiement::withoutGlobalScopes()
            ->where('statut', 'valide')
            ->selectRaw('
                COALESCE(SUM(montant_encaisse), 0) AS total_loyers,
                COALESCE(SUM(commission_ttc), 0)   AS total_commissions
            ')
            ->first();

        // 1 requête pour les abonnements
        $statsAbonnements = Subscription::selectRaw('
            SUM(CASE WHEN statut = "essai"  THEN 1 ELSE 0 END)                        AS nb_essai,
            SUM(CASE WHEN statut = "actif"  THEN 1 ELSE 0 END)                        AS nb_actifs,
            SUM(CASE WHEN statut = "expiré" THEN 1 ELSE 0 END)                        AS nb_expires,
            COALESCE(SUM(CASE WHEN statut = "actif" THEN montant_paye ELSE 0 END), 0) AS revenus
        ')->first();

        $stats = [
            'nb_agences'            => (int)   ($statsEntites->nb_agences            ?? 0),
            'nb_agences_actives'    => (int)   ($statsEntites->nb_agences_actives    ?? 0),
            'nb_users'              => (int)   ($statsEntites->nb_users              ?? 0),
            'nb_biens'              => (int)   ($statsEntites->nb_biens              ?? 0),
            'nb_contrats'           => (int)   ($statsEntites->nb_contrats           ?? 0),
            'total_loyers'          => (float) ($statsPaiements->total_loyers        ?? 0),
            'total_commissions'     => (float) ($statsPaiements->total_commissions   ?? 0),
            'nb_essai'              => (int)   ($statsAbonnements->nb_essai          ?? 0),
            'nb_abonnements_actifs' => (int)   ($statsAbonnements->nb_actifs         ?? 0),
            'nb_expires'            => (int)   ($statsAbonnements->nb_expires        ?? 0),
            'revenus_abonnements'   => (float) ($statsAbonnements->revenus           ?? 0),
        ];

        // 1 requête groupée pour tous les totaux paiements → plus de N+1
        $paiementsTotaux = Paiement::withoutGlobalScopes()
            ->where('statut', 'valide')
            ->selectRaw('
                agency_id,
                COALESCE(SUM(montant_encaisse), 0) AS total_loyers,
                COALESCE(SUM(commission_ttc), 0)   AS total_commissions
            ')
            ->groupBy('agency_id')
            ->get()
            ->keyBy('agency_id');

        $agences = Agency::withCount([
                'users',
                'biens',
                'contrats' => fn($q) => $q->where('statut', 'actif'),
            ])
            ->with(['users:id,agency_id,role', 'subscription'])
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($agency) use ($paiementsTotaux) {
                $p = $paiementsTotaux->get($agency->id);

                $agency->total_loyers      = (float) ($p->total_loyers      ?? 0);
                $agency->total_commissions = (float) ($p->total_commissions ?? 0);
                $agency->nb_admins         = $agency->users->where('role', 'admin')->count();

                return $agency;
            });

        return view('superadmin.dashboard', compact('stats', 'agences'));
    }

    public function toggleActif(Agency $agency): RedirectResponse
    {
        // `actif` est intentionnellement absent de Agency::$fillable.
        // On passe par assignation directe + save() pour contourner
        // la protection mass-assignment de façon explicite.
        $agency->actif = ! $agency->actif;
        $agency->save();
        $statut = $agency->actif ? 'activée' : 'désactivée';

        return redirect()
            ->route('superadmin.dashboard')
            ->with('success', "L'agence {$agency->name} a été {$statut}.");
    }

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

        $p = Paiement::withoutGlobalScopes()
            ->where('agency_id', $agency->id)
            ->where('statut', 'valide')
            ->selectRaw('
                COALESCE(SUM(montant_encaisse), 0) AS total_loyers,
                COALESCE(SUM(commission_ttc), 0)   AS total_commissions
            ')
            ->first();

        $stats = [
            'nb_users'          => $users->count(),
            'nb_proprietaires'  => $users->where('role', 'proprietaire')->count(),
            'nb_locataires'     => $users->where('role', 'locataire')->count(),
            'nb_biens'          => $biens->count(),
            'nb_biens_loues'    => $biens->where('statut', 'loue')->count(),
            'nb_contrats'       => Contrat::withoutGlobalScopes()
                                    ->where('agency_id', $agency->id)
                                    ->where('statut', 'actif')->count(),
            'total_loyers'      => (float) ($p->total_loyers      ?? 0),
            'total_commissions' => (float) ($p->total_commissions ?? 0),
        ];

        return view('superadmin.agency-detail', compact(
            'agency', 'users', 'biens', 'stats', 'subscription'
        ));
    }

    // ✅ CORRECTION M4 : paginate(50) au lieu de get() + CASE WHEN au lieu de FIELD()
    public function subscriptions(): View
    {
        $statsRaw = Subscription::selectRaw('
            SUM(CASE WHEN statut = "essai"  THEN 1 ELSE 0 END)                        AS nb_essai,
            SUM(CASE WHEN statut = "actif"  THEN 1 ELSE 0 END)                        AS nb_actifs,
            SUM(CASE WHEN statut = "expiré" THEN 1 ELSE 0 END)                        AS nb_expires,
            COALESCE(SUM(CASE WHEN statut = "actif" THEN montant_paye ELSE 0 END), 0) AS revenus_total,
            COALESCE(SUM(CASE WHEN statut = "actif" THEN
                CASE plan
                    WHEN "mensuel"     THEN 25000
                    WHEN "trimestriel" THEN 25000
                    WHEN "semestriel"  THEN 25000
                    WHEN "annuel"      THEN 20000
                    ELSE 0
                END
            ELSE 0 END), 0) AS revenus_mensuel_equiv
        ')->first();

        $stats = [
            'nb_essai'              => (int)   ($statsRaw->nb_essai              ?? 0),
            'nb_actifs'             => (int)   ($statsRaw->nb_actifs             ?? 0),
            'nb_expires'            => (int)   ($statsRaw->nb_expires            ?? 0),
            'revenus_total'         => (float) ($statsRaw->revenus_total         ?? 0),
            'revenus_mensuel_equiv' => (float) ($statsRaw->revenus_mensuel_equiv ?? 0),
        ];

        $subscriptions = Subscription::with('agency:id,name,email,actif')
            ->orderByRaw("
                CASE statut
                    WHEN 'essai'  THEN 1
                    WHEN 'actif'  THEN 2
                    WHEN 'expiré' THEN 3
                    WHEN 'annulé' THEN 4
                    ELSE 5
                END
            ")
            ->orderBy('date_fin_essai')
            ->paginate(50);

        return view('superadmin.subscriptions', compact('subscriptions', 'stats'));
    }

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

        $subscription->activer($request->plan, 'MANUEL-SUPERADMIN-' . now()->format('YmdHis'));

        return redirect()
            ->route('superadmin.subscriptions')
            ->with('success', "Abonnement {$request->plan} activé pour {$agency->name}.");
    }

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

    public function createAgency(): View
    {
        return view('superadmin.create-agency');
    }

    // ✅ CORRECTION H3 : PasswordPolicy::rules() au lieu de Password::min(8)
    public function storeAgency(Request $request): RedirectResponse
    {
        $request->validate([
            'agency_name'      => ['required', 'string', 'min:2', 'max:100'],
            'agency_email'     => ['required', 'email', 'max:255', 'unique:agencies,email'],
            'agency_telephone' => ['nullable', 'string', 'max:20'],
            'agency_adresse'   => ['nullable', 'string', 'max:255'],
            'admin_name'       => ['required', 'string', 'min:2', 'max:100'],
            'admin_email'      => ['required', 'email', 'max:255', 'unique:users,email'],
            'admin_password'   => ['required', 'confirmed', PasswordPolicy::rules()],
        ]);

        try {
            DB::transaction(function () use ($request) {
                // `slug` et `actif` sont intentionnellement absents de Agency::$fillable.
                // On utilise l'assignation directe de propriétés (pas create()) pour
                // contourner la protection mass-assignment de façon explicite et documentée.
                $agency            = new Agency();
                $agency->name      = $request->agency_name;
                $agency->email     = $request->agency_email;
                $agency->telephone = $request->agency_telephone;
                $agency->adresse   = $request->agency_adresse;
                $agency->slug      = Str::slug($request->agency_name) . '-' . Str::random(6);
                $agency->actif     = true;
                $agency->save();

                $admin            = new User();
                $admin->name      = $request->admin_name;
                $admin->email     = $request->admin_email;
                $admin->password  = bcrypt($request->admin_password);
                $admin->role      = 'admin';
                $admin->agency_id = $agency->id;
                $admin->email_verified_at = now();
                $admin->save();

                Subscription::create([
                    'agency_id'        => $agency->id,
                    'statut'           => 'essai',
                    'date_debut_essai' => now(),
                    'date_fin_essai'   => now()->addDays(30),
                ]);

                try {
                    $admin->notify(new AgencyWelcomeNotification($agency, $request->admin_password));
                } catch (\Throwable $e) {
                    Log::warning('Email de bienvenue non envoyé', ['error' => $e->getMessage()]);
                }
            });

            return redirect()
                ->route('superadmin.dashboard')
                ->with('success', "Agence {$request->agency_name} créée avec succès.");

        } catch (\Throwable $e) {
            Log::error('Erreur création agence', ['error' => $e->getMessage()]);
            return back()->withInput()->withErrors(['general' => 'Une erreur est survenue.']);
        }
    }
}