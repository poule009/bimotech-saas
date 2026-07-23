<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Agency;
use App\Models\AgencyFeatureOverride;
use App\Models\Bien;
use App\Models\Contrat;
use App\Models\ImpersonationSession;
use App\Models\MrrSnapshot;
use App\Models\Paiement;
use App\Models\Plan;
use App\Models\PlanPriceHistory;
use App\Models\RegleFiscale;
use App\Models\RegleFiscaleHistorique;
use App\Models\Subscription;
use App\Models\SubscriptionPayment;
use App\Models\User;
use App\Notifications\AgencyWelcomeNotification;
use App\Notifications\PasswordResetByAdminNotification;
use App\Notifications\PlanChangedNotification;
use App\Services\MrrService;
use App\Services\PlanChangeService;
use App\Services\PlanService;
use App\Support\PasswordPolicy;
use App\Support\RegleFiscaleCatalogue;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpFoundation\StreamedResponse;
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

        // Contexte de périmètre (module Équipe interne) : null = plateforme complète
        // (admin principal), sinon on borne toutes les données à un collaborateur.
        $ctx = app(\App\Support\SuperAdminContext::class);
        $perimId = $ctx->perimetreAdminId();

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

        // ── MRR : logique centralisée dans App\Services\MrrService ───────────
        // On PRÉFÈRE les snapshots réels déjà capturés (mrr:snapshot) ; à défaut
        // on reconstruit depuis les abonnements actuels (approximatif, jamais vide).
        $mrrService = app(MrrService::class);

        // Snapshots des 12 derniers mois, indexés par 'Y-m-d' du 1er du mois.
        // Les snapshots MrrSnapshot sont PLATEFORME (toutes agences confondues) : en
        // contexte restreint on les ignore et on reconstruit chaque mois à partir des
        // agences scopées ($agences), sinon un collaborateur verrait le MRR global.
        $moisCourbe = collect(range(11, 0))
            ->map(fn ($i) => $startMonth->copy()->subMonthsNoOverflow($i));
        $snapshots = $perimId
            ? collect()
            : MrrSnapshot::whereIn('mois', $moisCourbe->map->toDateString())
                ->get()
                ->keyBy(fn ($s) => $s->mois->toDateString());

        $mrr = $mrrService->at($now, $agences);
        // Croissance vs mois dernier : snapshot réel si disponible, sinon reconstruction.
        $mrrPrev = $snapshots->get($endLastMonth->copy()->startOfMonth()->toDateString())?->mrr
            ?? $mrrService->at($endLastMonth, $agences);
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
        // Concerne la facturation PLATEFORME (non scopée par périmètre) : on ne
        // l'affiche qu'aux comptes qui ont accès à la facturation, sinon un
        // collaborateur restreint verrait un décompte hors de son périmètre et
        // cliquerait vers un écran qui lui est interdit (403).
        $enAttente = $ctx->peutVoirSection('facturation')
            ? SubscriptionPayment::with('agency:id,name')
                ->where('statut', SubscriptionPayment::STATUT_EN_ATTENTE)
                ->get()
            : collect();
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

        // ── Courbe MRR sur 12 mois ───────────────────────────────────────────
        // Mois courant : valeur live. Mois passés : snapshot réel si capturé,
        // sinon reconstruction (fin de mois). $moisCourbe est déjà anti-débordement
        // (ancrage 1er du mois + subMonthsNoOverflow).
        $chartLabels = collect();
        $chartMrr = collect();
        foreach ($moisCourbe as $mois) {
            $chartLabels->push($mois->locale('fr')->isoFormat('MMM'));

            if ($mois->isSameMonth($now)) {
                $chartMrr->push($mrr);
            } else {
                $chartMrr->push(
                    $snapshots->get($mois->toDateString())?->mrr
                        ?? $mrrService->at($mois->copy()->endOfMonth(), $agences)
                );
            }
        }

        // ── Activité récente : événements niveau PLATEFORME (brief) ──────────
        // Le journal cross-agency contient surtout des événements internes aux
        // agences (biens, baux, quittances) qui n'ont pas leur place ici. On ne
        // retient que ce qui concerne la plateforme : cycle de vie des agences,
        // création de comptes, impersonations.
        // Asymétrie de visibilité : un collaborateur restreint ne voit que l'activité
        // de SES agences (le périmètre plafonne déjà $agences). Les événements plateforme
        // sans agence (gouvernance de l'équipe) et ceux des autres agences sont exclus.
        // $perimId est résolu en tête de méthode.
        $activites = ActivityLog::with(['user:id,name', 'agency:id,name'])
            ->where(function ($q) {
                $q->where('action', 'impersonate')
                    ->orWhere('model_type', Agency::class)
                    ->orWhere(fn ($q2) => $q2->where('model_type', User::class)->where('action', 'created'));
            })
            ->when($perimId, fn ($q) => $q->whereIn('agency_id', $agences->pluck('id'))
                ->whereNotIn('action', ['impersonate', 'impersonate_revoked']))
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

    /**
     * Liste des agences clientes (recherche + filtres statut/plan, pagination serveur).
     *
     * Choix de conception :
     *  - Le statut affiché est DÉRIVÉ (Subscription::etatEffectif) → source unique de
     *    vérité, jamais recodée en SQL. On charge donc les agences (quelques dizaines à
     *    l'échelle actuelle) + relation `subscription`, on calcule le statut en PHP, puis
     *    on filtre et on pagine manuellement via LengthAwarePaginator. La vue n'affiche
     *    toujours qu'une page (pagination « serveur » au sens rendu partiel).
     *  - Recherche : ≥ 3 caractères (débounce 300 ms côté client) sur le nom d'agence.
     */
    public function indexAgencies(Request $request): View
    {
        $now = now();
        $q = trim((string) $request->get('q', ''));
        $statutFiltre = $request->get('statut', 'tous'); // tous|actif|essai|suspendu
        $planFiltre = $request->get('plan', 'tous');      // tous|starter|pro|agence|legacy

        $agences = Agency::with('subscription')
            ->withCount(['biens as nb_unites' => fn ($qb) => $qb->where('statut', '!=', 'archive')])
            ->orderByDesc('created_at')
            ->get();

        $derniereActivite = ActivityLog::selectRaw('agency_id, MAX(created_at) AS last_at')
            ->groupBy('agency_id')
            ->pluck('last_at', 'agency_id')
            ->map(fn ($d) => Carbon::parse($d));

        $mrrService = app(MrrService::class);

        $rows = $agences->map(function (Agency $a) use ($derniereActivite, $mrrService, $now) {
            return [
                'agency' => $a,
                'statut' => $this->statutAgence($a),
                'plan' => $this->planLabel($a->subscription),
                'plan_niveau' => $a->subscription?->plan_niveau ?: 'legacy',
                'nb_unites' => (int) $a->nb_unites,
                'limite' => $a->limiteUnites(),
                'mrr' => $mrrService->at($now, collect([$a])),
                'inscrite' => $a->created_at,
                'derniere' => $derniereActivite->get($a->id),
            ];
        });

        // ── Compteurs globaux (sous-titre) — avant filtrage ──────────────────
        $repartition = $rows->countBy(fn ($r) => $r['statut']['bucket']);

        // ── Filtres cumulables ───────────────────────────────────────────────
        if (mb_strlen($q) >= 3) {
            $needle = mb_strtolower($q);
            $rows = $rows->filter(fn ($r) => str_contains(mb_strtolower($r['agency']->name), $needle));
        }
        if (in_array($statutFiltre, ['actif', 'essai', 'suspendu'], true)) {
            $rows = $rows->filter(fn ($r) => $r['statut']['bucket'] === $statutFiltre);
        }
        if (in_array($planFiltre, ['starter', 'pro', 'agence', 'legacy'], true)) {
            $rows = $rows->filter(fn ($r) => $r['plan_niveau'] === $planFiltre);
        }
        $rows = $rows->values();

        // ── Pagination manuelle (10 lignes / page) ───────────────────────────
        $perPage = 10;
        $page = max(1, (int) $request->get('page', 1));
        $paginator = new LengthAwarePaginator(
            $rows->slice(($page - 1) * $perPage, $perPage)->values(),
            $rows->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('superadmin.agencies-list', [
            'paginator' => $paginator,
            'repartition' => $repartition,
            'total' => $agences->count(),
            'q' => $q,
            'statutFiltre' => $statutFiltre,
            'planFiltre' => $planFiltre,
        ]);
    }

    /**
     * Statut dérivé d'une agence pour l'affichage (badge liste + en-tête fiche).
     * Le sous-libellé distingue explicitement la cause d'une suspension
     * (paiement en retard vs désactivation manuelle vs essai échu).
     *
     * @return array{bucket:string,label:string,variant:string}
     *         bucket ∈ actif|essai|suspendu ; variant ∈ green|gold|red
     */
    private function statutAgence(Agency $a): array
    {
        if (! $a->actif) {
            return ['bucket' => 'suspendu', 'label' => 'Suspendue', 'variant' => 'red'];
        }

        $s = $a->subscription;
        if (! $s) {
            return ['bucket' => 'actif', 'label' => 'Actif', 'variant' => 'green'];
        }

        return match ($s->etatEffectif()) {
            Subscription::ETAT_ESSAI => ['bucket' => 'essai', 'label' => 'Essai — J-'.$s->joursRestantsEssai(), 'variant' => 'gold'],
            Subscription::ETAT_ACTIF => ['bucket' => 'actif', 'label' => 'Actif', 'variant' => 'green'],
            Subscription::ETAT_GRACE => $s->statut === 'essai'
                ? ['bucket' => 'suspendu', 'label' => 'Essai expiré', 'variant' => 'red']
                : ['bucket' => 'suspendu', 'label' => 'Paiement en retard', 'variant' => 'red'],
            Subscription::ETAT_SUSPENDU => $s->statut === 'essai'
                ? ['bucket' => 'suspendu', 'label' => 'Essai échu', 'variant' => 'red']
                : ['bucket' => 'suspendu', 'label' => 'Suspendu — impayé', 'variant' => 'red'],
            default => ['bucket' => 'actif', 'label' => 'Actif', 'variant' => 'green'],
        };
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

        // libelle (et non libelle_public) : côté back-office, Legacy se nomme Legacy.
        return $s->plan_niveau
            ? (app(PlanService::class)->find($s->plan_niveau)?->libelle ?? ucfirst($s->plan_niveau))
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

    /**
     * Suspend / réactive une agence (bascule `actif`).
     *
     * Réactiver rend simplement l'accès : l'abonnement et son plan n'ont pas été
     * touchés par la suspension, l'agence retrouve donc son plan précédent sans
     * qu'on ait à le restaurer.
     */
    public function toggleActif(Agency $agency): RedirectResponse
    {
        // `actif` est intentionnellement absent de Agency::$fillable.
        // On passe par assignation directe + save() pour contourner
        // la protection mass-assignment de façon explicite.
        $agency->actif = ! $agency->actif;
        $agency->save();
        $statut = $agency->actif ? 'réactivée' : 'suspendue';

        // Couper ou rendre l'accès d'une agence est une action à fort impact :
        // elle doit laisser une trace nominative dans le journal.
        ActivityLog::create([
            'agency_id'    => $agency->id,
            'user_id'      => Auth::id(),
            'action'       => $agency->actif ? 'agence_reactivee' : 'agence_suspendue',
            'model_type'   => Agency::class,
            'model_id'     => $agency->id,
            'description'  => "Agence {$statut}",
            'is_sensitive' => true,
        ]);

        return redirect()
            ->route('superadmin.agencies.show', $agency)
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

        // « Amenée par » (module Équipe interne) — visible côté Super Admin uniquement.
        // Édition réservée à l'admin PRINCIPAL, et jamais en mode « Voir comme » : en
        // observation, on réplique la vue réelle du collaborateur (lecture seule), qui
        // ne réattribue jamais lui-même une agence.
        $agency->load('ameneePar:id,name');
        $peutEditerApporteur = Auth::user()->estSuperAdminPrincipal()
            && ! app(\App\Support\SuperAdminContext::class)->estRestreint();
        // Comptes attribuables : principal + collaborateurs (choix réservé au principal).
        $apporteurs = $peutEditerApporteur
            ? User::where('role', 'superadmin')->orderByDesc('sa_est_principal')->orderBy('name')->get(['id', 'name', 'sa_est_principal'])
            : collect();

        $p = Paiement::withoutGlobalScopes()
            ->where('agency_id', $agency->id)
            ->where('statut', 'valide')
            ->selectRaw('
                COALESCE(SUM(montant_encaisse), 0) AS total_loyers,
                COALESCE(SUM(commission_ttc), 0)   AS total_commissions
            ')
            ->first();

        $nbAdmins = $users->where('role', 'admin')->count();

        $stats = [
            'nb_users' => $users->count(),
            'nb_admins' => $nbAdmins,
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

        // Limites du plan (source unique : table `plans`) pour les jauges d'usage.
        $limites = [
            'unites' => $agency->limiteUnites(),
            'admins' => $agency->limiteAdmins(),
        ];

        // Historique des paiements d'abonnement (le plus récent d'abord).
        $paiements = SubscriptionPayment::withoutGlobalScopes()
            ->where('agency_id', $agency->id)
            ->latest('created_at')
            ->limit(12)
            ->get();

        // Journal de l'agence (onglet Activité) — hors consultations ('viewed').
        // En contexte restreint, on masque aussi les traces d'impersonation (support
        // de l'admin principal) : asymétrie de visibilité du module Équipe interne.
        $activites = ActivityLog::with('user:id,name')
            ->where('agency_id', $agency->id)
            ->where('action', '!=', 'viewed')
            ->when(
                app(\App\Support\SuperAdminContext::class)->estRestreint(),
                fn ($q) => $q->whereNotIn('action', ['impersonate', 'impersonate_revoked'])
            )
            ->latest()
            ->limit(15)
            ->get();

        // Compte à impersonner : le directeur (is_owner), sinon un admin.
        $adminCible = $users->firstWhere('is_owner', true)
            ?? $users->firstWhere('role', 'admin');

        // ── Changement de plan (onglet Abonnement) ───────────────────────
        $planService = app(PlanService::class);

        return view('superadmin.agency-detail', compact(
            'agency', 'users', 'biens', 'stats', 'subscription',
            'limites', 'paiements', 'activites', 'adminCible',
            'apporteurs', 'peutEditerApporteur'
        ) + [
            'statut' => $this->statutAgence($agency),
            // Legacy exclu : plan figé, non sélectionnable (souscriptibles() le filtre).
            'plansDisponibles' => $planService->souscriptibles(),
            'peutChangerPlan'  => $subscription
                ? app(PlanChangeService::class)->peutChanger($subscription)
                : false,
            'planProchain' => $subscription?->plan_niveau_prochain
                ? ($planService->find($subscription->plan_niveau_prochain)?->libelle ?? $subscription->plan_niveau_prochain)
                : null,
        ]);
    }

    // ✅ CORRECTION M4 : paginate(50) au lieu de get() + CASE WHEN au lieu de FIELD()
    /**
     * Écran « Abonnements & facturation » — la liste des TRANSACTIONS, toutes
     * agences confondues.
     *
     * Cet écran a absorbé deux vues qui se recoupaient : l'ancienne liste
     * d'abonnements (1 ligne par agence — le même contenu est dans la fiche
     * agence) et « Paiements en attente ». Le flux de validation manuelle
     * (Confirmer / Rejeter), seul flux d'encaissement réel aujourd'hui, vit
     * désormais dans le filtre « En attente » de cette page.
     */
    public function facturation(Request $request): View
    {
        [$debut, $fin] = $this->periodeFacturation($request);

        $filtres = [
            'statut'  => $request->query('statut'),
            'plan'    => $request->query('plan'),
            'periode' => $request->query('periode', 'mois'),
            'du'      => $debut->toDateString(),
            'au'      => $fin->toDateString(),
        ];

        $paiements = $this->requeteFacturation($request, $debut, $fin)
            ->with('agency:id,name,email')
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('superadmin.facturation', [
            'paiements' => $paiements,
            'stats'     => $this->statsFacturation($debut, $fin),
            'filtres'   => $filtres,
            'plans'     => app(PlanService::class)->all(),
            'periodeLabel' => $this->periodeLabel($filtres['periode'], $debut, $fin),
        ]);
    }

    /**
     * Fenêtre de dates de l'écran facturation.
     * Défaut « mois en cours », cohérent avec le KPI « Encaissé ce mois ».
     */
    private function periodeFacturation(Request $request): array
    {
        $periode = $request->query('periode', 'mois');

        if ($periode === 'perso') {
            $debut = $this->dateOuNull($request->query('du')) ?? now()->startOfMonth();
            $fin   = $this->dateOuNull($request->query('au')) ?? now();

            // Bornes inversées par l'utilisateur → on les remet dans l'ordre
            // plutôt que de renvoyer une liste vide sans explication.
            if ($debut->gt($fin)) {
                [$debut, $fin] = [$fin, $debut];
            }

            return [$debut->startOfDay(), $fin->endOfDay()];
        }

        return match ($periode) {
            '30j' => [now()->subDays(30)->startOfDay(), now()->endOfDay()],
            'tout' => [Carbon::createFromTimestamp(0), now()->endOfDay()],
            default => [now()->startOfMonth(), now()->endOfMonth()],
        };
    }

    private function dateOuNull(?string $valeur): ?Carbon
    {
        if (! $valeur) {
            return null;
        }

        try {
            return Carbon::parse($valeur);
        } catch (\Exception) {
            return null; // saisie libre invalide → on retombe sur le défaut
        }
    }

    private function periodeLabel(string $periode, Carbon $debut, Carbon $fin): string
    {
        return match ($periode) {
            '30j'  => '30 derniers jours',
            'tout' => 'Tout l\'historique',
            'perso' => $debut->locale('fr')->isoFormat('D MMM Y').' → '.$fin->locale('fr')->isoFormat('D MMM Y'),
            default => 'Ce mois',
        };
    }

    /** Requête de base partagée par la liste et l'export CSV (mêmes filtres). */
    private function requeteFacturation(Request $request, Carbon $debut, Carbon $fin)
    {
        $query = SubscriptionPayment::query()->whereBetween('created_at', [$debut, $fin]);

        if ($statut = $request->query('statut')) {
            // Passe par les buckets : 'confirme' doit aussi remonter les anciens 'payé'.
            $query->whereIn('statut', SubscriptionPayment::statutsDuBucket($statut));
        }

        if ($plan = $request->query('plan')) {
            $query->where('plan_niveau', $plan);
        }

        return $query;
    }

    /** KPI de l'en-tête, calculés sur la période affichée. */
    private function statsFacturation(Carbon $debut, Carbon $fin): array
    {
        $confirmes = SubscriptionPayment::BUCKETS['confirme'];
        $rejetes   = SubscriptionPayment::BUCKETS['rejete'];

        // Une seule requête agrégée par groupe plutôt qu'un sum() puis un count()
        // sur le même builder — moins d'aller-retours et pas de réutilisation de
        // builder déjà exécuté.
        $surPeriode = fn (array $statuts) => SubscriptionPayment::whereBetween('created_at', [$debut, $fin])
            ->whereIn('statut', $statuts)
            ->selectRaw('COALESCE(SUM(montant), 0) AS total, COUNT(*) AS nb, COUNT(DISTINCT agency_id) AS nb_agences')
            ->first();

        $encaisse = $surPeriode($confirmes);
        $echecs   = $surPeriode($rejetes);

        // Taux de réussite : toujours sur 30 jours glissants, indépendamment du
        // filtre de période — c'est un indicateur de santé du canal de paiement,
        // pas une lecture de la période sélectionnée (libellé du KPI explicite).
        $fenetre = [now()->subDays(30), now()];
        $trente = SubscriptionPayment::whereBetween('created_at', $fenetre)
            ->whereIn('statut', array_merge($confirmes, $rejetes))
            ->selectRaw('COUNT(*) AS total, SUM(CASE WHEN statut IN (?, ?) THEN 1 ELSE 0 END) AS reussis', $confirmes)
            ->first();

        $totalTraites = (int) ($trente->total ?? 0);

        return [
            'encaisse'        => (float) ($encaisse->total ?? 0),
            'nb_encaisses'    => (int) ($encaisse->nb ?? 0),
            'montant_echecs'  => (float) ($echecs->total ?? 0),
            'nb_agences_echec' => (int) ($echecs->nb_agences ?? 0),
            'nb_attente'      => SubscriptionPayment::duBucket('en_attente')->count(),
            // L'écran Facturation est une vue PLATEFORME (« facturation globale ») :
            // le MRR doit rester global, jamais réduit au périmètre d'un collaborateur.
            // On passe donc une collection d'agences hors scope de périmètre.
            'mrr'             => app(MrrService::class)->current(Agency::sansPerimetre()->with('subscription')->get()),
            // Pas de paiement traité sur la fenêtre → « — » plutôt qu'un 0 % trompeur.
            'taux_reussite'   => $totalTraites > 0
                ? (int) round((int) ($trente->reussis ?? 0) / $totalTraites * 100)
                : null,
        ];
    }

    /** Export CSV de la liste filtrée (mêmes filtres que l'écran). */
    public function exportFacturation(Request $request): StreamedResponse
    {
        [$debut, $fin] = $this->periodeFacturation($request);

        $paiements = $this->requeteFacturation($request, $debut, $fin)
            ->with('agency:id,name,email')
            ->orderByDesc('created_at')
            ->get();

        $plans = app(PlanService::class);

        $colonnes = ['Agence', 'Email', 'Plan', 'Cycle', 'Montant (F)', 'Méthode', 'Date', 'Statut', 'Référence', 'Période début', 'Période fin'];

        return response()->streamDownload(function () use ($paiements, $colonnes, $plans) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF"); // BOM UTF-8 pour Excel
            fputcsv($handle, $colonnes, ';');

            foreach ($paiements as $p) {
                fputcsv($handle, [
                    $p->agency?->name ?? 'Agence #'.$p->agency_id,
                    $p->agency?->email ?? '',
                    $plans->find($p->plan_niveau)?->libelle ?? $p->plan_niveau,
                    Subscription::LABELS[$p->plan] ?? $p->plan,
                    (int) $p->montant,
                    SubscriptionPayment::METHODE_LABELS[$p->methode] ?? $p->methode,
                    $p->created_at?->format('d/m/Y'),
                    $p->statut_label,
                    $p->reference ?? '',
                    $p->periode_debut?->format('d/m/Y') ?? '',
                    $p->periode_fin?->format('d/m/Y') ?? '',
                ], ';');
            }

            fclose($handle);
        }, 'facturation-'.now()->format('Y-m-d').'.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Reçu PDF d'un paiement encaissé, généré à la volée depuis les données.
     * Le montant vient du snapshot de la ligne de paiement : il reste exact même
     * si le tarif du plan a changé depuis.
     */
    public function recuPaiement(SubscriptionPayment $payment)
    {
        abort_unless($payment->estConfirme(), 404, 'Aucun reçu : ce paiement n\'a pas été encaissé.');

        $payment->load('agency');

        $pdf = Pdf::loadView('superadmin.pdf.recu', [
            'payment'   => $payment,
            'planLabel' => app(PlanService::class)->find($payment->plan_niveau)?->libelle ?? $payment->plan_niveau,
        ])
            ->setPaper('a4', 'portrait')
            ->setOption('defaultFont', 'DejaVu Sans')
            ->setOption('dpi', 96)
            ->setOption('isRemoteEnabled', false);

        return $pdf->download('recu-'.$payment->id.'-'.$payment->created_at->format('Y-m-d').'.pdf');
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
        $limiteNouveau = app(PlanService::class)->limiteUnites($planNiveau);

        $nbUnites = $agency->nbUnitesActives();
        if ($limiteNouveau !== null && $nbUnites > $limiteNouveau) {
            return redirect()
                ->back()
                ->with('error', "Impossible : {$agency->name} gère {$nbUnites} biens, "
                    ."mais le plan {$planNiveau} n'en autorise que {$limiteNouveau}. "
                    ."Archivez les biens excédentaires d'abord.");
        }

        $subscription->activer($request->plan, 'MANUEL-SUPERADMIN-'.now()->format('YmdHis'), 'manuel', $planNiveau);

        $niveauLabel = app(PlanService::class)->find($planNiveau)?->libelle ?? ucfirst($planNiveau);

        return redirect()
            ->route('superadmin.agencies.show', $agency)
            ->with('success', "Abonnement {$request->plan} ({$niveauLabel}) activé pour {$agency->name}.");
    }

    /**
     * Change le plan d'une agence depuis sa fiche.
     * Upgrade → immédiat + prorata ; downgrade → au prochain cycle. Voir
     * PlanChangeService, qui porte la règle (et le log d'activité).
     */
    public function changerPlan(Request $request, Agency $agency): RedirectResponse
    {
        $souscriptibles = app(PlanService::class)->souscriptibles()->keys()->all();

        $valide = $request->validate([
            // Legacy est volontairement absent de la liste : plan figé, non sélectionnable.
            'plan_niveau' => ['required', Rule::in($souscriptibles)],
        ], [
            'plan_niveau.in' => 'Ce plan n\'est pas sélectionnable.',
        ]);

        $subscription = $agency->subscription;

        if (! $subscription) {
            return back()->with('error', "{$agency->name} n'a pas d'abonnement — activez-en un d'abord.");
        }

        $cible = $valide['plan_niveau'];
        $changer = app(PlanChangeService::class);

        if (! $changer->peutChanger($subscription)) {
            return back()->with('error', "Le plan de {$agency->name} ne peut pas être changé : il faut un essai en cours ou un abonnement actif, et hors Legacy.");
        }

        if ($subscription->plan_niveau === $cible) {
            return back()->with('error', "{$agency->name} est déjà sur ce plan.");
        }

        // Un downgrade sous la limite du plan cible casserait l'agence à
        // l'échéance (biens au-delà du quota) : on refuse tant qu'elle n'a pas
        // archivé, plutôt que de programmer une bombe à retardement.
        $limiteCible = app(PlanService::class)->limiteUnites($cible);
        $nbUnites = $agency->nbUnitesActives();

        if ($limiteCible !== null && $nbUnites > $limiteCible) {
            $labelCible = app(PlanService::class)->find($cible)?->libelle ?? $cible;

            return back()->with('error', "Impossible : {$agency->name} gère {$nbUnites} biens, "
                ."mais le plan {$labelCible} n'en autorise que {$limiteCible}. "
                ."Archivez les biens excédentaires d'abord.");
        }

        $res = $changer->changer($subscription, $cible, Auth::id());

        $this->notifierChangementPlan($agency, $res, $cible);

        $labelCible = app(PlanService::class)->find($cible)?->libelle ?? $cible;

        $message = match ($res['type']) {
            'essai' => "{$agency->name} est en essai sur le plan {$labelCible} — ses limites sont à jour. "
                ."Ce tarif sera appliqué à la souscription ; rien n'est facturé maintenant.",
            'upgrade' => "{$agency->name} est passée en {$labelCible} — effet immédiat."
                .($res['montant'] > 0
                    ? ' Prorata de '.number_format($res['montant'], 0, ',', ' ').' F ajouté aux paiements en attente.'
                    : ' Aucun prorata à facturer.'),
            default => "Passage en {$labelCible} programmé pour {$agency->name} au "
                .($res['effet']?->locale('fr')->isoFormat('D MMM Y') ?? 'prochain cycle').' (pas de remboursement au prorata).',
        };

        return back()->with('success', $message);
    }

    /** Annule un downgrade programmé qui n'a pas encore pris effet. */
    public function annulerDowngrade(Agency $agency): RedirectResponse
    {
        $subscription = $agency->subscription;

        if (! $subscription?->plan_niveau_prochain) {
            return back()->with('error', 'Aucun changement de plan programmé pour cette agence.');
        }

        app(PlanChangeService::class)->annulerDowngrade($subscription, Auth::id());

        return back()->with('success', "Changement de plan annulé — {$agency->name} reste sur son plan actuel.");
    }

    /** Prévient le directeur de l'agence par email (canal Resend déjà en place). */
    private function notifierChangementPlan(Agency $agency, array $res, string $cible): void
    {
        $directeur = $agency->users()->where('is_owner', true)->first();

        if (! $directeur) {
            return;
        }

        // Un envoi qui échoue ne doit pas annuler un changement de plan déjà
        // acté en base : on trace et on continue.
        try {
            $directeur->notify(new PlanChangedNotification(
                $agency,
                app(PlanService::class)->label($cible),
                $res['type'],
                $res['montant'],
                $res['effet'],
            ));
        } catch (\Throwable $e) {
            Log::error('Notification changement de plan non envoyée', [
                'agency_id' => $agency->id,
                'error'     => $e->getMessage(),
            ]);
        }
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
                'plan_niveau_prochain' => null, // repartir en essai périme le downgrade programmé
                'montant_paye' => null,
                'date_debut_abonnement' => null,
                'date_fin_abonnement' => null,
            ]);
        }

        return redirect()
            ->route('superadmin.agencies.show', $agency)
            ->with('success', "Essai de 30 jours réinitialisé pour {$agency->name}.");
    }

    /**
     * Met à jour le champ « Amenée par » d'une agence (module Équipe interne).
     * Réservé à l'admin principal — un collaborateur ne réattribue jamais lui-même.
     * amenee_par est hors $fillable (privilège plateforme) → assignation directe.
     */
    public function updateAmeneePar(Request $request, Agency $agency): RedirectResponse
    {
        // Principal uniquement, et jamais en mode « Voir comme » (vue d'observation
        // en lecture seule) : la réattribution est une action de gouvernance.
        abort_unless(
            Auth::user()->estSuperAdminPrincipal()
            && ! app(\App\Support\SuperAdminContext::class)->estRestreint(),
            403,
            'Seul l\'administrateur principal peut attribuer une agence.'
        );

        $superAdminIds = User::where('role', 'superadmin')->pluck('id')->all();

        $valide = $request->validate([
            'amenee_par' => ['nullable', Rule::in(array_map('strval', $superAdminIds))],
        ], [
            'amenee_par.in' => 'Ce compte n\'est pas un compte Super Admin valide.',
        ]);

        $agency->amenee_par = ($valide['amenee_par'] ?? '') === '' ? null : (int) $valide['amenee_par'];
        $agency->save();

        $nom = $agency->amenee_par ? (User::find($agency->amenee_par)?->name ?? 'un collaborateur') : 'Non attribué';

        return redirect()
            ->route('superadmin.agencies.show', $agency)
            ->with('success', "« Amenée par » de {$agency->name} : {$nom}.");
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

        // Périmètre (module Équipe interne) : un collaborateur restreint ne peut
        // impersonner que dans ses agences, et seulement si son toggle le permet.
        $ctx = app(\App\Support\SuperAdminContext::class);
        if ($ctx->estRestreint()) {
            abort_unless(Auth::user()->saPermission('impersonation'), 403,
                "L'impersonation ne fait pas partie de votre périmètre.");
            abort_unless(in_array($user->agency_id, $ctx->perimetreAgencyIds() ?? [], true), 403,
                'Cette agence ne fait pas partie de votre périmètre.');
        }

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

        // Journalise la session pour la supervision (Support / Debug) : sessions
        // actives, historique, et coupure à distance possible par un autre admin.
        $impersonation = ImpersonationSession::create([
            'admin_id'    => Auth::id(),
            'user_id'     => $user->id,
            'agency_id'   => $user->agency_id,
            // Snapshots lisibles même si l'admin ou l'agence est supprimé plus tard.
            'admin_name'  => Auth::user()?->name,
            'agency_name' => $user->agency?->name,
            'started_at'  => now(),
            'ip_address'  => request()?->ip(),
        ]);

        session([
            'impersonating_id'        => Auth::id(),
            'impersonation_session_id' => $impersonation->id,
        ]);
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
        $sessionId    = Session::pull('impersonation_session_id');

        // Clôture normale de la session tracée (sortie volontaire de l'admin).
        // On ne touche pas une session déjà terminée (ex. coupée à distance).
        if ($sessionId) {
            ImpersonationSession::whereKey($sessionId)
                ->whereNull('ended_at')
                ->update([
                    'ended_at'   => now(),
                    'ended_by'   => $superAdminId,
                    'end_reason' => 'normal',
                ]);
        }

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

    /**
     * Support / Debug — vue centralisée :
     *  - recherche rapide d'agence (redirige vers la fiche si un seul résultat clair),
     *  - sessions d'impersonation en cours (coupables à distance),
     *  - historique paginé de toutes les sessions passées.
     */
    public function support(Request $request): View|RedirectResponse
    {
        // ── Recherche rapide (niveau agence uniquement, v1) ──────────────────
        $q = trim((string) $request->query('q', ''));
        $resultats = collect();

        if ($q !== '') {
            $terme = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $q);
            $resultats = Agency::query()
                ->where(function ($sub) use ($terme) {
                    $sub->where('name', 'like', "%{$terme}%")
                        ->orWhere('email', 'like', "%{$terme}%")
                        ->orWhere('telephone', 'like', "%{$terme}%");
                })
                ->orderBy('name')
                ->limit(10)
                ->get(['id', 'name', 'email', 'telephone']);

            // Un seul résultat clair → on va droit à la fiche, pas de liste inutile.
            if ($resultats->count() === 1) {
                return redirect()->route('superadmin.agencies.show', $resultats->first());
            }
        }

        // ── Asymétrie de visibilité (module Équipe interne) ──────────────────
        // Un collaborateur restreint (ou le principal en « Voir comme ») ne voit QUE
        // ses propres sessions d'impersonation — jamais celles de l'admin principal
        // ou d'un autre collaborateur. Périmètre null (principal) = tout.
        $perimId = app(\App\Support\SuperAdminContext::class)->perimetreAdminId();

        // ── Sessions actives ─────────────────────────────────────────────────
        $actives = ImpersonationSession::active()
            ->when($perimId, fn ($q) => $q->where('admin_id', $perimId))
            ->with(['admin:id,name', 'agency:id,name'])
            ->orderByDesc('started_at')
            ->get();

        // ── Historique (sessions terminées), filtrable ───────────────────────
        [$debut, $fin] = $this->periodeSupport($request);

        $adminId = $request->query('admin');
        $periode = $request->query('periode', '30j');

        $historique = ImpersonationSession::query()
            ->whereNotNull('ended_at')
            ->whereBetween('started_at', [$debut, $fin])
            ->when($perimId, fn ($query) => $query->where('admin_id', $perimId))
            ->when($adminId, fn ($query) => $query->where('admin_id', (int) $adminId))
            ->with(['admin:id,name', 'agency:id,name'])
            ->orderByDesc('started_at')
            ->paginate(15)
            ->withQueryString();

        // Admins ayant déjà lancé au moins une impersonation → alimente le filtre.
        // Restreint : le collaborateur ne se voit filtrer que sur lui-même.
        $adminIds = ImpersonationSession::query()
            ->whereNotNull('admin_id')
            ->when($perimId, fn ($q) => $q->where('admin_id', $perimId))
            ->distinct()
            ->pluck('admin_id');
        $admins = User::withoutGlobalScopes()
            ->whereIn('id', $adminIds)
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('superadmin.support', [
            'actives'    => $actives,
            'historique' => $historique,
            'admins'     => $admins,
            'filtres'    => [
                'q'       => $q,
                'admin'   => $adminId ? (int) $adminId : null,
                'periode' => $periode,
                'du'      => $debut->toDateString(),
                'au'      => $fin->toDateString(),
            ],
            'resultats' => $resultats,
        ]);
    }

    /** Fenêtre de dates de l'historique Support (défaut : 30 derniers jours). */
    private function periodeSupport(Request $request): array
    {
        $periode = $request->query('periode', '30j');

        if ($periode === 'perso') {
            $debut = $this->dateOuNull($request->query('du')) ?? now()->subDays(30);
            $fin   = $this->dateOuNull($request->query('au')) ?? now();

            if ($debut->gt($fin)) {
                [$debut, $fin] = [$fin, $debut];
            }

            return [$debut->startOfDay(), $fin->endOfDay()];
        }

        return match ($periode) {
            '7j'   => [now()->subDays(7)->startOfDay(), now()->endOfDay()],
            'tout' => [Carbon::createFromTimestamp(0), now()->endOfDay()],
            default => [now()->subDays(30)->startOfDay(), now()->endOfDay()],
        };
    }

    /**
     * Coupe à distance la session d'impersonation d'un collègue.
     *
     * On pose seulement `ended_at` + `end_reason='revoked'` : la déconnexion réelle
     * est appliquée par EnforceImpersonationRevocation au prochain hit de l'admin
     * concerné, avec un bandeau immédiat de notification.
     */
    public function terminateImpersonation(ImpersonationSession $session): RedirectResponse
    {
        // Clôture atomique : `whereNull('ended_at')` évite d'écraser une session
        // déjà fermée entre-temps (sortie normale, autre coupure concurrente).
        $ferme = ImpersonationSession::whereKey($session->id)
            ->whereNull('ended_at')
            ->update([
                'ended_at'   => now(),
                'ended_by'   => Auth::id(),
                'end_reason' => 'revoked',
            ]);

        if (! $ferme) {
            return back()->with('error', 'Cette session est déjà terminée.');
        }

        $nomAdmin = $session->admin?->name ?? $session->admin_name;

        ActivityLog::create([
            'user_id'     => Auth::id(),
            'agency_id'   => $session->agency_id,
            'action'      => 'impersonate_revoked',
            'description' => "Session d'impersonation coupée à distance : {$nomAdmin} sur ".($session->agency?->name ?? $session->agency_name),
            'model_type'  => ImpersonationSession::class,
            'model_id'    => $session->id,
            'ip_address'  => request()?->ip(),
        ]);

        return back()->with('success', "Session terminée. {$nomAdmin} sera déconnecté de l'agence à sa prochaine action.");
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
        $this->assertPrincipal();

        return view('superadmin.create-agency');
    }

    /**
     * Créer une agence est une action de GOUVERNANCE (comme Équipe interne ou
     * Paramètres) : réservée à l'admin principal. Un collaborateur restreint gère
     * ses agences apportées mais n'en crée pas de nouvelles (elles naîtraient
     * d'ailleurs « Non attribué », hors de son périmètre).
     */
    private function assertPrincipal(): void
    {
        abort_unless(
            Auth::user()?->estSuperAdminPrincipal() === true,
            403,
            'Seul l\'administrateur principal peut créer une agence.'
        );
    }

    // ✅ CORRECTION H3 : PasswordPolicy::rules() au lieu de Password::min(8)
    public function storeAgency(Request $request): RedirectResponse
    {
        $this->assertPrincipal();

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
    // ─────────────────────────────────────────────────────────────────────
    // CONFIGURATION DES PLANS
    // ─────────────────────────────────────────────────────────────────────

    /** Écran « Configuration des plans » — prix et limites, une carte par plan. */
    public function configPlans(): View
    {
        $plans = app(PlanService::class)->all();

        // Nombre d'agences par plan, pour le lien « Voir les X agences sur ce plan ».
        $compteurs = Subscription::selectRaw('plan_niveau, COUNT(*) AS total')
            ->whereNotNull('plan_niveau')
            ->groupBy('plan_niveau')
            ->pluck('total', 'plan_niveau');

        return view('superadmin.config-plans', [
            'plans'     => $plans,
            'compteurs' => $compteurs,
            // Piste d'audit : quel tarif était en vigueur, quand, et par qui changé.
            'historique' => PlanPriceHistory::with(['plan:id,libelle', 'user:id,name'])
                ->latest()
                ->limit(8)
                ->get(),
        ]);
    }

    /**
     * Enregistre un plan (sauvegarde indépendante, une carte à la fois).
     *
     * ⚠️ RÈGLE MÉTIER : ne touche QUE les futures souscriptions et les
     * renouvellements. Les agences déjà engagées gardent leur tarif jusqu'à leur
     * prochain cycle — garanti par le snapshot `montant_paye`, figé à
     * l'encaissement et jamais relu depuis le plan.
     */
    public function updatePlan(Request $request, Plan $plan): RedirectResponse
    {
        abort_if($plan->verrouille, 403, 'Le plan '.$plan->libelle.' est verrouillé.');

        $valide = $request->validate([
            'prix_mensuel'  => ['required', 'integer', 'min:0', 'max:10000000'],
            'prix_annuel'   => ['required', 'integer', 'min:0', 'max:100000000'],
            // Champ vide = illimité (convention de l'app, cohérente avec la colonne nullable).
            'limite_unites' => ['nullable', 'integer', 'min:1', 'max:100000'],
            'limite_admins' => ['nullable', 'integer', 'min:1', 'max:10000'],
        ], [], [
            'prix_mensuel'  => 'prix mensuel',
            'prix_annuel'   => 'prix annuel',
            'limite_unites' => 'limite de biens',
            'limite_admins' => "limite d'utilisateurs",
        ]);

        $service = app(PlanService::class);
        $changes = $service->update($plan, $valide, Auth::id());

        if ($changes === 0) {
            return back()->with('success', "Aucune modification sur le plan {$plan->libelle}.");
        }

        return back()->with('success', "Plan {$plan->libelle} enregistré — appliqué aux nouvelles souscriptions et aux prochains renouvellements.");
    }

    // ─────────────────────────────────────────────────────────────────────
    // PAIEMENTS
    // ─────────────────────────────────────────────────────────────────────

    /**
     * Ancien écran dédié aux paiements à valider — absorbé par la facturation.
     * Route conservée en redirection : elle est en circulation dans des liens et
     * d'anciens signets, et la casser ferait perdre l'accès au flux de validation.
     */
    public function paiementsAttente(): RedirectResponse
    {
        return redirect()->route('superadmin.facturation', ['statut' => 'en_attente', 'periode' => 'tout']);
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

    // ── Règles fiscales ─────────────────────────────────────────────────────

    /**
     * Liste des règles fiscales (KPIs + recherche + filtres statut/catégorie).
     *
     * Admin DOCUMENTAIRE (v1) : la valeur appliquée par le moteur est affichée
     * en lecture seule (dérivée des constantes de FiscalService via
     * RegleFiscaleCatalogue) — seules les métadonnées de traçabilité sont
     * éditables. Le noyau fiscal (lourdement testé) n'est pas touché.
     */
    public function reglesFiscales(Request $request): View
    {
        $q             = trim((string) $request->get('q', ''));
        $statutFiltre  = $request->get('statut', 'tous');   // tous|confirme|non_verifie
        $groupeFiltre  = $request->get('groupe', 'tous');   // tous|brs|irpp|cgf|cfpb_teom|...

        $toutes = RegleFiscale::orderBy('categorie')->orderBy('cle')->get();

        // KPIs sur l'ensemble (indépendants des filtres).
        $nbConfirmees = $toutes->where('est_confirmee', true)->count();
        $nbAVerifier  = $toutes->where('statut', 'non_verifie')->count();
        $derniereMaj  = RegleFiscaleHistorique::max('created_at')
            ?? $toutes->max('updated_at');

        // Filtrage en mémoire (quelques dizaines de règles).
        $regles = $toutes->filter(function (RegleFiscale $r) use ($q, $statutFiltre, $groupeFiltre) {
            if ($q !== '' && ! \Illuminate\Support\Str::contains(
                \Illuminate\Support\Str::lower($r->titre . ' ' . $r->description),
                \Illuminate\Support\Str::lower($q)
            )) {
                return false;
            }

            if ($statutFiltre === 'confirme' && ! $r->est_confirmee) {
                return false;
            }
            if ($statutFiltre === 'non_verifie' && $r->statut !== 'non_verifie') {
                return false;
            }

            if ($groupeFiltre !== 'tous' && RegleFiscaleCatalogue::groupe($r->categorie) !== $groupeFiltre) {
                return false;
            }

            return true;
        })->values();

        return view('superadmin.regles-fiscales.index', [
            'regles'        => $regles,
            'total'         => $toutes->count(),
            'nbConfirmees'  => $nbConfirmees,
            'nbAVerifier'   => $nbAVerifier,
            'derniereMaj'   => $derniereMaj ? Carbon::parse($derniereMaj) : null,
            'q'             => $q,
            'statutFiltre'  => $statutFiltre,
            'groupeFiltre'  => $groupeFiltre,
            'groupes'       => RegleFiscaleCatalogue::GROUPES,
        ]);
    }

    /** Fiche détail d'une règle fiscale (formulaire + « utilisée dans » + historique). */
    public function showRegleFiscale(RegleFiscale $regle): View
    {
        return view('superadmin.regles-fiscales.show', [
            'regle'        => $regle,
            'valeur'       => RegleFiscaleCatalogue::valeur($regle),
            'bareme'       => RegleFiscaleCatalogue::bareme($regle),
            'utiliseeDans' => RegleFiscaleCatalogue::utiliseeDans($regle),
            'statuts'      => RegleFiscale::STATUTS,
            'historiques'  => $regle->historiques()->with('admin')->latest()->get(),
        ]);
    }

    /**
     * Enregistre les modifications d'une règle (documentaire).
     *
     * La VALEUR appliquée n'est pas éditable ici (dérivée du moteur) : on ne
     * modifie que titre, statut, description, note, date de vérification et
     * sources. Chaque champ modifié crée une ligne d'historique — jamais
     * d'écrasement silencieux (brief).
     */
    public function updateRegleFiscale(Request $request, RegleFiscale $regle): RedirectResponse
    {
        $validated = $request->validate([
            'titre'             => ['required', 'string', 'max:255'],
            'statut'            => ['required', 'string', \Illuminate\Validation\Rule::in(array_keys(RegleFiscale::STATUTS))],
            'description'       => ['required', 'string'],
            'note'              => ['nullable', 'string'],
            'date_verification' => ['nullable', 'date'],
            'sources'                => ['nullable', 'array'],
            'sources.*.libelle'      => ['nullable', 'string', 'max:255'],
            'sources.*.url'          => ['nullable', 'string', 'url', 'max:2048'],
        ], [], [
            'sources.*.url' => 'lien source',
        ]);

        // Normalise les sources : on ne garde que les lignes avec un libellé.
        $sources = collect($validated['sources'] ?? [])
            ->map(fn ($s) => [
                'libelle' => trim((string) ($s['libelle'] ?? '')),
                'url'     => trim((string) ($s['url'] ?? '')) ?: null,
            ])
            ->filter(fn ($s) => $s['libelle'] !== '')
            ->values()
            ->all();

        $nouvellesValeurs = [
            'titre'             => $validated['titre'],
            'statut'            => $validated['statut'],
            'description'       => $validated['description'],
            'note'              => $validated['note'] ?? null,
            'date_verification' => $validated['date_verification'] ?? null,
            'sources'           => $sources,
        ];

        // Diff champ par champ pour l'historique (pas d'écrasement silencieux).
        $auteur  = Auth::user();
        $changes = [];
        foreach ($nouvellesValeurs as $champ => $nouvelle) {
            $ancienne = $regle->{$champ};

            if ($champ === 'sources') {
                $ancienneStr = $this->sourcesEnTexte($ancienne ?? []);
                $nouvelleStr = $this->sourcesEnTexte($nouvelle);
            } elseif ($champ === 'date_verification') {
                $ancienneStr = optional($ancienne)->format('Y-m-d');
                $nouvelleStr = $nouvelle ?: null;
            } else {
                $ancienneStr = $ancienne;
                $nouvelleStr = $nouvelle;
            }

            if ((string) $ancienneStr !== (string) $nouvelleStr) {
                $changes[] = [
                    'champ'           => $champ,
                    'ancienne_valeur' => $ancienneStr,
                    'nouvelle_valeur' => $nouvelleStr,
                ];
            }
        }

        if (empty($changes)) {
            return back()->with('info', 'Aucune modification à enregistrer.');
        }

        DB::transaction(function () use ($regle, $nouvellesValeurs, $changes, $auteur) {
            $regle->update($nouvellesValeurs);

            foreach ($changes as $c) {
                RegleFiscaleHistorique::create([
                    'regle_fiscale_id' => $regle->id,
                    'admin_id'         => $auteur?->id,
                    'admin_nom'        => $auteur?->name,
                    'champ'            => $c['champ'],
                    'ancienne_valeur'  => $c['ancienne_valeur'],
                    'nouvelle_valeur'  => $c['nouvelle_valeur'],
                ]);
            }

            // Trace aussi une entrée dans le journal d'activité : la modification d'une
            // règle fiscale est un événement critique lu par « Paramètres système ».
            // (L'historique détaillé champ par champ reste dans regle_fiscale_historiques.)
            $labels  = ['titre' => 'titre', 'statut' => 'statut', 'description' => 'description',
                        'note' => 'note', 'date_verification' => 'date de vérification', 'sources' => 'sources'];
            $champs   = array_map(fn ($c) => $labels[$c['champ']] ?? $c['champ'], $changes);
            ActivityLog::create([
                'user_id'      => $auteur?->id,
                'agency_id'    => null,
                'action'       => 'regle_modifiee',
                'is_sensitive' => true,
                'description'  => "Règle fiscale « {$regle->titre} » modifiée (".implode(', ', $champs).')',
                'model_type'   => RegleFiscale::class,
                'model_id'     => $regle->id,
                'ip_address'   => request()?->ip(),
            ]);
        });

        return redirect()
            ->route('superadmin.regles.show', $regle)
            ->with('success', 'Règle mise à jour — la modification s\'applique aux calculs futurs.');
    }

    /** Résumé lisible d'une liste de sources pour l'historique. */
    private function sourcesEnTexte(array $sources): string
    {
        return collect($sources)
            ->map(fn ($s) => trim(($s['libelle'] ?? '') . ($s['url'] ? ' (' . $s['url'] . ')' : '')))
            ->filter()
            ->implode(' · ');
    }
}
