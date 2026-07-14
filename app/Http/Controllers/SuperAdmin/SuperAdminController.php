<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Agency;
use App\Models\AgencyFeatureOverride;
use App\Models\Bien;
use App\Models\Contrat;
use App\Models\Paiement;
use App\Models\Subscription;
use App\Models\SubscriptionPayment;
use App\Models\User;
use App\Notifications\AgencyWelcomeNotification;
use App\Notifications\PasswordResetByAdminNotification;
use App\Support\PasswordPolicy;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SuperAdminController extends Controller
{
    /**
     * Dashboard « santé de la plateforme » — vue actionnable (todo-list du jour).
     *
     * Choix arrêtés avec le fondateur :
     *  - KPI calculés en temps réel (échelle actuelle : quelques dizaines d'agences).
     *  - MRR = équivalent mensuel des abonnements payants ACTIFS, plans Legacy EXCLUS.
     *  - « Inactivité » = ancienneté de la dernière action tracée dans activity_logs.
     *  - Actions des alertes = liens WhatsApp (wa.me) pré-remplis (modèle manuel de l'app).
     */
    public function dashboard(): View
    {
        $now = now();
        $startMonth = $now->copy()->startOfMonth();
        $endLastMonth = $startMonth->copy()->subSecond();
        $seuilInactif = 14; // jours sans action → agence inactive

        // ── Agences (+ abonnement, compteur d'unités actives, sans N+1) ──────
        $agences = Agency::with('subscription')
            ->withCount(['biens as nb_unites' => fn ($q) => $q->where('statut', '!=', 'archive')])
            ->orderByDesc('created_at')
            ->get();

        // Dernière action par agence (durable, source = activity_logs).
        $derniereActivite = ActivityLog::selectRaw('agency_id, MAX(created_at) AS last_at')
            ->groupBy('agency_id')
            ->pluck('last_at', 'agency_id')
            ->map(fn ($d) => Carbon::parse($d));

        // ── MRR (équivalent mensuel) à une date donnée, Legacy exclu ─────────
        // Un abonnement compte s'il était payant et couvrait la date visée.
        $mrrAt = function (Carbon $date) use ($agences): int {
            return $agences->sum(function ($a) use ($date) {
                $s = $a->subscription;
                if (! $s || ! in_array($s->plan_niveau, ['starter', 'pro', 'agence'], true)) {
                    return 0;
                }
                if (! $s->date_debut_abonnement || ! $s->date_fin_abonnement) {
                    return 0;
                }
                if ($s->date_debut_abonnement->gt($date) || $s->date_fin_abonnement->lt($date)) {
                    return 0;
                }
                $tarif = Subscription::TARIFS[$s->plan_niveau][$s->plan] ?? null;
                if ($tarif === null) {
                    return 0;
                }

                return $s->plan === 'annuel' ? intdiv($tarif, 12) : $tarif;
            });
        };

        $mrr = $mrrAt($now);
        $mrrPrev = $mrrAt($endLastMonth);
        $mrrGrowth = $mrrPrev > 0
            ? round(($mrr - $mrrPrev) / $mrrPrev * 100, 1)
            : ($mrr > 0 ? 100.0 : 0.0);

        // ── KPI ──────────────────────────────────────────────────────────────
        $agencesActives = $agences->where('actif', true)->count();
        $agencesNouvellesM = $agences->filter(fn ($a) => $a->created_at && $a->created_at->gte($startMonth))->count();

        $enEssai = $agences->filter(fn ($a) => $a->subscription?->estEnEssai())->count();
        $essaisBientot = $agences->filter(fn ($a) => $a->subscription?->estEnEssai()
            && $a->subscription->joursRestantsEssai() <= 7);

        $suspendues = $agences->filter(fn ($a) => ! $a->actif || $a->subscription?->estSuspendu())->count();

        $stats = [
            'agences_actives' => $agencesActives,
            'agences_nouvelles' => $agencesNouvellesM,
            'mrr' => $mrr,
            'mrr_growth' => $mrrGrowth,
            'en_essai' => $enEssai,
            'essais_bientot' => $essaisBientot->count(),
            'suspendues' => $suspendues,
        ];

        // ── Alertes « À traiter aujourd'hui » (par ordre de priorité) ────────
        $alertes = collect();

        // 1. URGENT — abonnement payant échu (grâce/suspendu = impayé)
        foreach ($agences as $a) {
            $s = $a->subscription;
            if ($s && $s->statut === 'actif' && in_array($s->etatEffectif(), ['grace', 'suspendu'], true)) {
                $jours = $s->date_fin_abonnement ? (int) $s->date_fin_abonnement->diffInDays($now) : null;
                $alertes->push([
                    'severite' => 'urgent',
                    'icone' => '!',
                    'titre' => 'Paiement en retard — '.$a->name,
                    'sous_titre' => $this->planLabel($s)
                        .($jours !== null ? ' · échéance dépassée depuis '.$jours.' j' : ''),
                    'action_label' => 'Voir détails',
                    'action_url' => route('superadmin.agencies.show', $a),
                    'action_primary' => true,
                    'externe' => false,
                ]);
            }
        }

        // 2. URGENT — déclarations de paiement à vérifier (back-office manuel)
        $enAttente = SubscriptionPayment::with('agency:id,name')
            ->where('statut', SubscriptionPayment::STATUT_EN_ATTENTE)
            ->get();
        if ($enAttente->isNotEmpty()) {
            $alertes->push([
                'severite' => 'urgent',
                'icone' => '!',
                'titre' => $enAttente->count().' déclaration'.($enAttente->count() > 1 ? 's' : '').' de paiement à vérifier',
                'sous_titre' => $enAttente->take(3)->map(fn ($p) => $p->agency?->name)->filter()->implode(', '),
                'action_label' => 'Vérifier',
                'action_url' => route('superadmin.paiements.attente'),
                'action_primary' => true,
                'externe' => false,
            ]);
        }

        // 3. WARNING — essais qui expirent sous 7 jours (relance WhatsApp)
        foreach ($essaisBientot as $a) {
            $jr = $a->subscription->joursRestantsEssai();
            $alertes->push([
                'severite' => 'warn',
                'icone' => '⏳',
                'titre' => 'Essai expire '.($jr <= 0 ? "aujourd'hui" : 'dans '.$jr.' j').' — '.$a->name,
                'sous_titre' => 'Relancer avant la fin de la période gratuite',
                'action_label' => 'Envoyer une relance',
                'action_url' => $this->waLink($a, 'Bonjour, votre essai gratuit de Bimmo se termine bientôt. Souhaitez-vous activer votre abonnement ?'),
                'action_primary' => false,
                'externe' => true,
            ]);
        }

        // 4. INFO — agence inactive depuis 14 jours et plus
        foreach ($agences as $a) {
            if (! $a->actif) {
                continue;
            }
            $ref = $derniereActivite->get($a->id) ?? $a->created_at;
            if (! $ref) {
                continue;
            }
            $jours = (int) $ref->diffInDays($now);
            if ($jours >= $seuilInactif) {
                $alertes->push([
                    'severite' => 'info',
                    'icone' => '☾',
                    'titre' => 'Agence inactive depuis '.$jours.' jours — '.$a->name,
                    'sous_titre' => 'Dernière action le '.$ref->locale('fr')->isoFormat('D MMM Y'),
                    'action_label' => 'Contacter',
                    'action_url' => $this->waLink($a, "Bonjour, nous avons remarqué que vous n'avez pas utilisé Bimmo récemment. Pouvons-nous vous aider ?"),
                    'action_primary' => false,
                    'externe' => true,
                ]);
            }
        }

        // 5. WARNING — limite d'unités bientôt atteinte (>= 90 % du plan)
        foreach ($agences as $a) {
            $limite = $a->limiteUnites();
            if ($limite === null || $limite === 0) {
                continue;
            }
            if ($a->nb_unites / $limite >= 0.9) {
                $alertes->push([
                    'severite' => 'warn',
                    'icone' => '△',
                    'titre' => "Limite d'unités bientôt atteinte — ".$a->name,
                    'sous_titre' => $a->nb_unites.'/'.$limite.' biens (plan '.$this->planLabel($a->subscription).')',
                    'action_label' => 'Suggérer un upgrade',
                    'action_url' => route('superadmin.agencies.show', $a),
                    'action_primary' => false,
                    'externe' => false,
                ]);
            }
        }

        // ── Courbe MRR sur 12 mois (équivalent mensuel, fin de mois) ─────────
        // Ancrage sur le 1ᵉʳ du mois + subMonthsNoOverflow : évite le débordement
        // de subMonths() les jours 29-31 (ex. 31 mars − 1 mois → 3 mars) qui
        // fausserait les libellés de mois selon la date de consultation.
        $chartLabels = collect();
        $chartMrr = collect();
        $ancreMois = $now->copy()->startOfMonth();
        foreach (range(11, 0) as $i) {
            $mois = $ancreMois->copy()->subMonthsNoOverflow($i);
            $point = $i === 0 ? $now : $mois->copy()->endOfMonth();
            $chartLabels->push($mois->locale('fr')->isoFormat('MMM'));
            $chartMrr->push($mrrAt($point));
        }

        // ── Activité récente : événements niveau PLATEFORME (brief) ──────────
        // Le journal cross-agency contient surtout des événements internes aux
        // agences (biens, baux, quittances) qui n'ont pas leur place ici. On ne
        // retient que ce qui concerne la plateforme : cycle de vie des agences,
        // création de comptes, impersonations.
        $activites = ActivityLog::with(['user:id,name', 'agency:id,name'])
            ->where(function ($q) {
                $q->where('action', 'impersonate')
                    ->orWhere('model_type', Agency::class)
                    ->orWhere(fn ($q2) => $q2->where('model_type', User::class)->where('action', 'created'));
            })
            ->latest()
            ->limit(6)
            ->get()
            ->map(fn ($log) => [
                'description' => $log->description,
                'agence' => $log->agency?->name,
                'temps' => $this->tempsRelatif($log->created_at),
            ]);

        // ── Agences à risque (max 5, priorité retard > inactive > essai) ─────
        $risque = collect();
        foreach ($agences as $a) {
            $s = $a->subscription;
            $enRetard = ! $a->actif
                || ($s && $s->statut === 'actif' && in_array($s->etatEffectif(), ['grace', 'suspendu'], true));
            if ($enRetard) {
                $risque->push([
                    'agence' => $a,
                    'plan' => $this->planLabel($s),
                    'motif' => $a->actif ? 'Paiement en retard' : 'Suspendue',
                    'type' => 'late',
                    'prio' => 1,
                ]);

                continue;
            }
            $ref = $derniereActivite->get($a->id) ?? $a->created_at;
            if ($a->actif && $ref && (int) $ref->diffInDays($now) >= $seuilInactif) {
                $risque->push(['agence' => $a, 'plan' => $this->planLabel($s), 'motif' => 'Inactive '.(int) $ref->diffInDays($now).'j', 'type' => 'idle', 'prio' => 2]);

                continue;
            }
            if ($s?->estEnEssai() && $s->joursRestantsEssai() <= 7) {
                $jr = $s->joursRestantsEssai();
                $risque->push(['agence' => $a, 'plan' => 'Essai', 'motif' => 'Essai expire J-'.max(0, $jr), 'type' => 'idle', 'prio' => 3]);
            }
        }
        $risque = $risque->sortBy('prio')->take(5)->values();

        return view('superadmin.dashboard', [
            'stats' => $stats,
            'alertes' => $alertes,
            'chartLabels' => $chartLabels,
            'chartMrr' => $chartMrr,
            'activites' => $activites,
            'risque' => $risque,
            'dateStr' => $now->locale('fr')->isoFormat('dddd D MMMM Y'),
        ]);
    }

    /** Page « à venir » pour les sections Super Admin pas encore construites. */
    public function aVenir(string $section): View
    {
        $titres = [
            'agences' => 'Agences',
            'abonnements' => 'Abonnements & facturation',
            'support' => 'Support / Debug',
            'regles-fiscales' => 'Règles fiscales',
            'equipe' => 'Équipe interne',
            'parametres' => 'Paramètres système',
        ];

        abort_unless(array_key_exists($section, $titres), 404);

        return view('superadmin.a-venir', [
            'section' => $section,
            'titre' => $titres[$section],
        ]);
    }

    /** Libellé de plan lisible pour une souscription (« Essai », « Pro », « — »). */
    private function planLabel(?Subscription $s): string
    {
        if (! $s) {
            return '—';
        }
        if ($s->estEnEssai()) {
            return 'Essai';
        }

        return $s->plan_niveau
            ? config('plans.labels.'.$s->plan_niveau, ucfirst($s->plan_niveau))
            : '—';
    }

    /** Lien WhatsApp pré-rempli vers le contact d'une agence (null si pas de numéro). */
    private function waLink(Agency $agency, string $message): string
    {
        $digits = preg_replace('/\D/', '', (string) ($agency->whatsapp ?: $agency->telephone));
        if ($digits === '') {
            // Pas de numéro connu → fiche agence en repli (le superadmin y trouve le contact).
            return route('superadmin.agencies.show', $agency);
        }
        // Numéro local sénégalais (9 chiffres) → préfixer l'indicatif +221.
        if (strlen($digits) === 9) {
            $digits = '221'.$digits;
        }

        return 'https://wa.me/'.$digits.'?text='.rawurlencode($message);
    }

    /** Horodatage relatif façon maquette : « 09:14 » / « Hier, 17:40 » / « 12 juil. ». */
    private function tempsRelatif(?Carbon $date): string
    {
        if (! $date) {
            return '';
        }
        if ($date->isToday()) {
            return $date->format('H:i');
        }
        if ($date->isYesterday()) {
            return 'Hier, '.$date->format('H:i');
        }

        return $date->locale('fr')->isoFormat('D MMM');
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
            'nb_users' => $users->count(),
            'nb_proprietaires' => $users->where('role', 'proprietaire')->count(),
            'nb_locataires' => $users->where('role', 'locataire')->count(),
            'nb_biens' => $biens->count(),
            'nb_biens_loues' => $biens->where('statut', 'loue')->count(),
            'nb_contrats' => Contrat::withoutGlobalScopes()
                ->where('agency_id', $agency->id)
                ->where('statut', 'actif')->count(),
            'total_loyers' => (float) ($p->total_loyers ?? 0),
            'total_commissions' => (float) ($p->total_commissions ?? 0),
        ];

        $overrides = $agency->featureOverrides()->get()->keyBy('feature');

        return view('superadmin.agency-detail', compact(
            'agency', 'users', 'biens', 'stats', 'subscription', 'overrides'
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
                    WHEN "mensuel" THEN
                        CASE plan_niveau
                            WHEN "starter" THEN 25000
                            WHEN "agence"  THEN 90000
                            ELSE 50000
                        END
                    WHEN "annuel" THEN
                        CASE plan_niveau
                            WHEN "starter" THEN FLOOR(199000/12)
                            WHEN "agence"  THEN FLOOR(699000/12)
                            ELSE FLOOR(399000/12)
                        END
                    ELSE 0
                END
            ELSE 0 END), 0) AS revenus_mensuel_equiv
        ')->first();

        $stats = [
            'nb_essai' => (int) ($statsRaw->nb_essai ?? 0),
            'nb_actifs' => (int) ($statsRaw->nb_actifs ?? 0),
            'nb_expires' => (int) ($statsRaw->nb_expires ?? 0),
            'revenus_total' => (float) ($statsRaw->revenus_total ?? 0),
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
            'plan' => ['required', 'in:mensuel,annuel'],
            'plan_niveau' => ['nullable', 'in:starter,pro,agence'],
        ]);

        $subscription = $agency->subscription;

        if (! $subscription) {
            $subscription = Subscription::create([
                'agency_id' => $agency->id,
                'statut' => 'essai',
                'date_debut_essai' => now(),
                'date_fin_essai' => now()->addDays(30),
            ]);
        }

        $planNiveau = $request->input('plan_niveau', 'pro');
        $limitesParPlan = config('plans.nb_unites_max');
        $limiteNouveau = $limitesParPlan[$planNiveau] ?? $limitesParPlan['pro'];

        $nbUnites = $agency->nbUnitesActives();
        if ($limiteNouveau !== null && $nbUnites > $limiteNouveau) {
            return redirect()
                ->back()
                ->with('error', "Impossible : {$agency->name} gère {$nbUnites} biens, "
                    ."mais le plan {$planNiveau} n'en autorise que {$limiteNouveau}. "
                    ."Archivez les biens excédentaires d'abord.");
        }

        $subscription->activer($request->plan, 'MANUEL-SUPERADMIN-'.now()->format('YmdHis'), 'manuel', $planNiveau);

        $niveauLabel = config("plans.labels.{$planNiveau}", ucfirst($planNiveau));

        return redirect()
            ->route('superadmin.subscriptions')
            ->with('success', "Abonnement {$request->plan} ({$niveauLabel}) activé pour {$agency->name}.");
    }

    public function reinitialiserEssai(Agency $agency): RedirectResponse
    {
        $subscription = $agency->subscription;

        if (! $subscription) {
            Subscription::create([
                'agency_id' => $agency->id,
                'statut' => 'essai',
                'date_debut_essai' => now(),
                'date_fin_essai' => now()->addDays(30),
            ]);
        } else {
            $subscription->update([
                'statut' => 'essai',
                'date_debut_essai' => now(),
                'date_fin_essai' => now()->addDays(30),
                'plan' => null,
                'montant_paye' => null,
                'date_debut_abonnement' => null,
                'date_fin_abonnement' => null,
            ]);
        }

        return redirect()
            ->route('superadmin.subscriptions')
            ->with('success', "Essai de 30 jours réinitialisé pour {$agency->name}.");
    }

    public function editAgency(Agency $agency): View
    {
        return view('superadmin.edit-agency', compact('agency'));
    }

    public function updateAgency(Request $request, Agency $agency): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:100'],
            'email' => ['required', 'email', 'max:255', Rule::unique('agencies', 'email')->ignore($agency->id)],
            'telephone' => ['nullable', 'string', 'max:20'],
            'adresse' => ['nullable', 'string', 'max:255'],
            'taux_tva' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        $agency->update($request->only('name', 'email', 'telephone', 'adresse', 'taux_tva'));

        return redirect()
            ->route('superadmin.agencies.show', $agency)
            ->with('success', "Agence {$agency->name} mise à jour avec succès.");
    }

    public function resetUserPassword(Agency $agency, int $userId): RedirectResponse
    {
        /** @var User $user */
        $user = User::withoutGlobalScopes()->withTrashed()->findOrFail($userId);
        abort_unless($user->agency_id === $agency->id, 403);

        // Invalide l'ancien mot de passe sans en générer un en clair.
        // Un lien de réinitialisation est envoyé par email — le MDP temporaire
        // ne transite jamais dans le HTML, les logs ou les outils de monitoring.
        $user->password = Hash::make(Str::random(32));
        $user->save();

        $emailEnvoye = false;
        try {
            $user->notify(new PasswordResetByAdminNotification);
            $emailEnvoye = true;
        } catch (\Throwable $e) {
            Log::warning('Email réinitialisation MDP non envoyé', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }

        $message = $emailEnvoye
            ? "Mot de passe réinitialisé pour {$user->name}. Un lien de définition de mot de passe a été envoyé par email."
            : "Mot de passe réinitialisé pour {$user->name}. L'email n'a pas pu être envoyé — demandez à l'utilisateur de cliquer sur \"Mot de passe oublié\" sur la page de connexion.";

        return redirect()
            ->route('superadmin.agencies.show', $agency)
            ->with('success', $message);
    }

    public function toggleUser(Agency $agency, int $userId): RedirectResponse
    {
        /** @var User $user */
        $user = User::withoutGlobalScopes()->withTrashed()->findOrFail($userId);
        abort_unless($user->agency_id === $agency->id, 403);
        abort_if($user->isSuperAdmin(), 403);

        if ($user->trashed()) {
            $user->restore();
            $statut = 'réactivé';
        } else {
            $user->delete();
            $statut = 'désactivé';
        }

        return redirect()
            ->route('superadmin.agencies.show', $agency)
            ->with('success', "Utilisateur {$user->name} {$statut}.");
    }

    public function impersonate(User $user): RedirectResponse
    {
        abort_if($user->isSuperAdmin(), 403, "Impossible d'impersonner un super-admin.");

        // Audit : tracer le démarrage de l'impersonation.
        // Auth::id() est encore le superadmin réel avant le Auth::login ci-dessous.
        ActivityLog::create([
            'user_id' => Auth::id(),
            'agency_id' => $user->agency_id,
            'action' => 'impersonate',
            'description' => "Impersonation démarrée : {$user->name} (#{$user->id}, {$user->role})",
            'model_type' => User::class,
            'model_id' => $user->id,
            'ip_address' => request()?->ip(),
        ]);

        session(['impersonating_id' => Auth::id()]);
        Auth::login($user);

        $redirect = match ($user->role) {
            'admin' => route('admin.dashboard'),
            'proprietaire' => route('proprietaire.dashboard'),
            'locataire' => route('locataire.dashboard'),
            default => route('dashboard'),
        };

        return redirect($redirect);
    }

    public function stopImpersonation(): RedirectResponse
    {
        // Sécurité : cette route ne doit être active que pendant une impersonation réelle.
        // Sans ce garde, n'importe quel utilisateur authentifié peut appeler la route.
        if (! Session::has('impersonating_id')) {
            abort(403, 'Aucune impersonation active.');
        }

        $superAdminId = Session::pull('impersonating_id');

        if (! $superAdminId) {
            return redirect()->route('superadmin.dashboard');
        }

        $superAdmin = User::withoutGlobalScopes()->find($superAdminId);

        if (! $superAdmin || ! $superAdmin->isSuperAdmin()) {
            Auth::logout();

            return redirect()->route('login');
        }

        Auth::login($superAdmin);

        return redirect()
            ->route('superadmin.dashboard')
            ->with('success', 'Impersonation terminée. Vous êtes de retour en tant que Super Admin.');
    }

    public function toggleFeature(Agency $agency, string $feature): RedirectResponse
    {
        abort_unless(array_key_exists($feature, config('plans.features', [])), 404);

        $override = AgencyFeatureOverride::where('agency_id', $agency->id)
            ->where('feature', $feature)
            ->first();

        $planNiveau = $agency->subscription?->plan_niveau ?? 'starter';
        $hierarchy = config('plans.hierarchy', ['starter', 'pro', 'agence']);
        $niveauEffectif = config('plans.niveau_effectif')[$planNiveau] ?? 'starter';
        $niveauRequis = config('plans.features')[$feature];
        $enabledByPlan = array_search($niveauEffectif, $hierarchy) >= array_search($niveauRequis, $hierarchy);

        if ($override) {
            // Cycler : override → retirer l'override (revenir au plan)
            $override->delete();
            $msg = "Override retiré pour « {$feature} » — comportement plan restauré.";
        } else {
            // Créer l'override inverse du plan actuel
            AgencyFeatureOverride::create([
                'agency_id' => $agency->id,
                'feature' => $feature,
                'enabled' => ! $enabledByPlan,
            ]);
            $etat = $enabledByPlan ? 'désactivée' : 'activée';
            $msg = "Feature « {$feature} » {$etat} pour {$agency->name}.";
        }

        return back()->with('success', $msg);
    }

    public function removeFeatureOverride(Agency $agency, string $feature): RedirectResponse
    {
        abort_unless(array_key_exists($feature, config('plans.features', [])), 404);

        AgencyFeatureOverride::where('agency_id', $agency->id)
            ->where('feature', $feature)
            ->delete();

        return back()->with('success', "Override supprimé — plan appliqué pour « {$feature} ».");
    }

    public function createAgency(): View
    {
        return view('superadmin.create-agency');
    }

    // ✅ CORRECTION H3 : PasswordPolicy::rules() au lieu de Password::min(8)
    public function storeAgency(Request $request): RedirectResponse
    {
        $request->validate([
            'agency_name' => ['required', 'string', 'min:2', 'max:100'],
            'agency_email' => ['required', 'email', 'max:255', 'unique:agencies,email'],
            'agency_telephone' => ['nullable', 'string', 'max:20'],
            'agency_adresse' => ['nullable', 'string', 'max:255'],
            'admin_name' => ['required', 'string', 'min:2', 'max:100'],
            'admin_email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'admin_password' => ['required', 'confirmed', PasswordPolicy::rules()],
        ]);

        try {
            DB::transaction(function () use ($request) {
                // `slug` et `actif` sont intentionnellement absents de Agency::$fillable.
                // On utilise l'assignation directe de propriétés (pas create()) pour
                // contourner la protection mass-assignment de façon explicite et documentée.
                $agency = new Agency;
                $agency->name = $request->agency_name;
                $agency->email = $request->agency_email;
                $agency->telephone = $request->agency_telephone;
                $agency->adresse = $request->agency_adresse;
                $agency->slug = Str::slug($request->agency_name).'-'.Str::random(6);
                $agency->actif = true;
                $agency->save();

                $admin = new User;
                $admin->name = $request->admin_name;
                $admin->email = $request->admin_email;
                $admin->password = Hash::make($request->admin_password);
                $admin->role = 'admin';
                $admin->is_owner = true;
                $admin->agency_id = $agency->id;
                $admin->email_verified_at = now();
                $admin->save();

                Subscription::create([
                    'agency_id' => $agency->id,
                    'statut' => 'essai',
                    'date_debut_essai' => now(),
                    'date_fin_essai' => now()->addDays(30),
                ]);

                try {
                    $admin->notify(new AgencyWelcomeNotification($agency));
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

    // ── Validation des déclarations de paiement manuelles ────────────────────

    /** Liste des paiements déclarés en attente de vérification. */
    public function paiementsAttente(): View
    {
        $paiements = SubscriptionPayment::with('agency:id,name,email')
            ->where('statut', SubscriptionPayment::STATUT_EN_ATTENTE)
            ->orderBy('created_at')
            ->get();

        return view('superadmin.paiements-attente', compact('paiements'));
    }

    /** Confirme un paiement → active/renouvelle l'abonnement de l'agence. */
    public function confirmerPaiement(SubscriptionPayment $payment): RedirectResponse
    {
        abort_unless($payment->statut === SubscriptionPayment::STATUT_EN_ATTENTE, 422, 'Ce paiement a déjà été traité.');

        DB::transaction(function () use ($payment) {
            $subscription = Subscription::where('id', $payment->subscription_id)->lockForUpdate()->firstOrFail();

            // Le niveau souscrit est dans payment->plan_niveau (starter|pro|agence) ; cycle mensuel.
            $res = $subscription->activerAbonnement($payment->plan ?: 'mensuel', $payment->plan_niveau ?? 'pro');

            $payment->update([
                'statut' => SubscriptionPayment::STATUT_CONFIRME,
                'periode_debut' => $res['debut'],
                'periode_fin' => $res['fin'],
            ]);
        });

        return back()->with('success', "Paiement confirmé — l'abonnement de {$payment->agency?->name} est réactivé.");
    }

    /** Rejette un paiement avec un motif obligatoire (affiché à l'agence). */
    public function rejeterPaiement(Request $request, SubscriptionPayment $payment): RedirectResponse
    {
        abort_unless($payment->statut === SubscriptionPayment::STATUT_EN_ATTENTE, 422, 'Ce paiement a déjà été traité.');

        $validated = $request->validate([
            'motif_rejet' => ['required', 'string', 'max:255'],
        ], [
            'motif_rejet.required' => 'Un motif de rejet est obligatoire (il sera affiché à l\'agence).',
        ]);

        $payment->update([
            'statut' => SubscriptionPayment::STATUT_REJETE,
            'motif_rejet' => $validated['motif_rejet'],
        ]);

        return back()->with('success', 'Paiement rejeté — le motif sera affiché à l\'agence.');
    }
}
