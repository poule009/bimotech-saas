<?php

namespace App\Http\Controllers;

use App\Models\Bien;
use App\Models\Contrat;
use App\Models\Locataire;
use App\Models\Paiement;
use App\Models\Proprietaire;
use App\Models\User;
use App\Services\BailleurPortfolioService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    use AuthorizesRequests;

    public function __construct(private readonly BailleurPortfolioService $portfolioService)
    {
    }

    // ─────────────────────────────────────────────────────────────────────
    // HELPER PRIVÉ — Vérification appartenance cross-agence
    // ─────────────────────────────────────────────────────────────────────

    private function verifierAppartenance(User $user): void
    {
        /** @var \App\Models\User $authUser */
        $authUser = Auth::user();

        if ($authUser->isSuperAdmin()) {
            return;
        }

        if ($user->agency_id !== $authUser->agency_id) {
            abort(403, 'Accès refusé — cet utilisateur n\'appartient pas à votre agence.');
        }

        if (! in_array($user->role, ['proprietaire', 'locataire'])) {
            abort(404);
        }
    }

    // ─────────────────────────────────────────────────────────────────────
    // LISTE PROPRIÉTAIRES
    // ─────────────────────────────────────────────────────────────────────

    public function proprietaires()
    {
        $this->authorize('isAdmin');

        $agencyId = Auth::user()->agency_id;

        // Données financières via service (indexées par user_id)
        $portfolios = $this->portfolioService->getPortfolioIndex($agencyId)
            ->keyBy(fn($p) => $p['user']->id);

        // Tous les propriétaires de l'agence (y compris ceux sans biens)
        $usersQuery = User::where('role', 'proprietaire')
            ->where('agency_id', $agencyId)
            ->select(['id', 'agency_id', 'name', 'email', 'telephone', 'created_at'])
            ->with(['proprietaire:user_id,ville,ninea,mode_paiement_prefere,est_personne_morale_is']);

        if (($q = trim((string) request('q'))) !== '') {
            $usersQuery->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('telephone', 'like', "%{$q}%")
                    ->orWhereHas('proprietaire', fn ($p) => $p
                        ->where('ville', 'like', "%{$q}%")
                        ->orWhere('ninea', 'like', "%{$q}%"));
            });
        }

        // Filtre par type (particulier / entreprise) — colonne est_personne_morale_is
        if (in_array(request('type'), ['particulier', 'entreprise'], true)) {
            $estMorale = request('type') === 'entreprise';
            $usersQuery->whereHas('proprietaire', function ($p) use ($estMorale) {
                $estMorale
                    ? $p->where('est_personne_morale_is', true)
                    : $p->where(fn ($x) => $x->where('est_personne_morale_is', false)->orWhereNull('est_personne_morale_is'));
            });
        }

        $users = $usersQuery->orderBy('name')->get();

        // Fusion identité + financier
        $proprietaires = $users->map(function (User $user) use ($portfolios) {
            $p = $portfolios->get($user->id);
            return [
                'user'              => $user,
                'nb_biens'          => $p['nb_biens'] ?? 0,
                'nb_biens_loues'    => $p['nb_biens_loues'] ?? 0,
                'total_loyers'      => $p['total_loyers'] ?? 0,
                'total_commissions' => $p['total_commissions'] ?? 0,
                'total_depenses'    => $p['total_depenses'] ?? 0,
                'net_final'         => $p['net_final'] ?? 0,
                'nb_paiements'      => $p['nb_paiements'] ?? 0,
            ];
        });

        $stats = [
            'total'            => User::where('role', 'proprietaire')->where('agency_id', $agencyId)->count(),
            'total_biens'      => $portfolios->sum('nb_biens'),
            'biens_loues'      => $portfolios->sum('nb_biens_loues'),
            'total_loyers'     => $portfolios->sum('total_loyers'),
            'total_net'        => $portfolios->sum('net_final'),
        ];

        return view('users.proprietaires', compact('proprietaires', 'stats'));
    }

    // ─────────────────────────────────────────────────────────────────────
    // LISTE LOCATAIRES
    // ─────────────────────────────────────────────────────────────────────

    public function locataires(Request $request)
    {
        $this->authorize('isAdmin');

        $agencyId = Auth::user()->agency_id;

        /**
         * PERFORMANCE — Eager loading sélectif :
         *
         * Avant : ->with('contrats.bien') chargeait TOUTES les colonnes de contrats
         * ET de biens pour chaque locataire, juste pour afficher la référence.
         *
         * Après : on charge uniquement les colonnes nécessaires à l'affichage,
         * avec withCount pour le nombre de contrats.
         */
        $query = User::where('role', 'locataire')
            ->where('agency_id', $agencyId)
            ->select(['id', 'agency_id', 'name', 'email', 'telephone', 'created_at'])
            ->with([
                'contrats' => fn($q) => $q
                    ->where('statut', 'actif')
                    ->select(['id', 'locataire_id', 'bien_id', 'statut', 'loyer_contractuel'])
                    ->with(['bien:id,reference,adresse,ville']),
                'locataire:user_id,est_entreprise,profession,employeur,revenu_mensuel',
            ])
            ->withCount([
                'contrats',
                'contrats as contrats_actifs_count' => fn($q) => $q->where('statut', 'actif'),
            ]);

        if (($q = trim((string) $request->input('q'))) !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('telephone', 'like', "%{$q}%")
                    ->orWhereHas('locataire', fn ($l) => $l
                        ->where('profession', 'like', "%{$q}%")
                        ->orWhere('employeur', 'like', "%{$q}%"));
            });
        }

        $periode = now()->startOfMonth();

        if ($request->input('statut') === 'actif') {
            $query->whereHas('contrats', fn ($c) => $c->where('statut', 'actif'));
        } elseif ($request->input('statut') === 'sans') {
            $query->whereDoesntHave('contrats', fn ($c) => $c->where('statut', 'actif'));
        }

        // Filtre par type (particulier / entreprise-bureau)
        if (in_array($request->input('type'), ['particulier', 'entreprise'], true)) {
            $estEnt = $request->input('type') === 'entreprise';
            $query->whereHas('locataire', fn ($l) => $estEnt
                ? $l->where('est_entreprise', true)
                : $l->where(fn ($x) => $x->where('est_entreprise', false)->orWhereNull('est_entreprise')));
        }

        // Filtre « en retard » : contrat actif sans paiement validé pour la période courante.
        if ($request->boolean('retard')) {
            $query->whereHas('contrats', function ($c) use ($periode) {
                $c->where('statut', 'actif')
                  ->whereDoesntHave('paiements', fn ($p) => $p->where('statut', 'valide')
                      ->whereYear('periode', $periode->year)->whereMonth('periode', $periode->month));
            });
        }

        $locataires = $query->orderBy('name')->paginate(15)->withQueryString();

        // ── Statut de paiement CALCULÉ (jamais stocké) ────────────────────
        // Même logique qu'ImpayeController : 5 jours de grâce après le début de période.
        $grace       = $periode->copy()->addDays(5);
        $joursRetard = (int) $grace->diffInDays(now(), false);
        $contratIds  = $locataires->getCollection()->flatMap(fn ($u) => $u->contrats->pluck('id'))->unique()->values();
        $paidIds     = $contratIds->isEmpty() ? collect() : Paiement::whereIn('contrat_id', $contratIds)
            ->where('statut', 'valide')
            ->whereYear('periode', $periode->year)
            ->whereMonth('periode', $periode->month)
            ->pluck('contrat_id')->unique();

        foreach ($locataires->getCollection() as $u) {
            $actif = $u->contrats->firstWhere('statut', 'actif');
            if (! $actif) {
                $u->pay_status = 'aucun'; $u->pay_jours = 0; $u->pay_bien = null;
                continue;
            }
            $u->pay_bien = $actif->bien?->reference ?? $actif->bien?->ville;
            if ($paidIds->contains($actif->id) || $joursRetard <= 0) {
                $u->pay_status = 'ok'; $u->pay_jours = 0;
            } else {
                $u->pay_status = 'retard'; $u->pay_jours = $joursRetard;
            }
        }

        $baseCount = fn () => User::where('role', 'locataire')->where('agency_id', $agencyId);

        $stats = [
            'total'        => $baseCount()->count(),
            'actifs'       => $baseCount()->whereHas('contrats', fn ($q) => $q->where('statut', 'actif'))->count(),
            'sans_contrat' => $baseCount()->whereDoesntHave('contrats', fn ($q) => $q->where('statut', 'actif'))->count(),
            'en_retard'    => $baseCount()->whereHas('contrats', fn ($c) => $c->where('statut', 'actif')
                                  ->whereDoesntHave('paiements', fn ($p) => $p->where('statut', 'valide')
                                      ->whereYear('periode', $periode->year)->whereMonth('periode', $periode->month)))->count(),
        ];

        return view('users.locataires', compact('locataires', 'stats'));
    }

    // ─────────────────────────────────────────────────────────────────────
    // FORMULAIRE CRÉATION
    // ─────────────────────────────────────────────────────────────────────

    public function create(string $role)
    {
        $this->authorize('isAdmin');

        if (! in_array($role, ['proprietaire', 'locataire'])) {
            abort(404);
        }

        return view('users.create', compact('role'));
    }

    // ─────────────────────────────────────────────────────────────────────
    // ENREGISTREMENT
    // ─────────────────────────────────────────────────────────────────────

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('isAdmin');

        $validated = $request->validate([
            'role'      => ['required', 'in:proprietaire,locataire'],
            'name'      => ['required', 'string', 'max:255'],
            'email'     => ['nullable', 'email', 'unique:users,email', 'max:255'],
            'telephone' => ['nullable', 'string', 'max:20'],
            'adresse'   => ['nullable', 'string', 'max:255'],
            'password'  => ['nullable', 'confirmed', 'min:8'],
            // ── Identité commune ──────────────────────────────────────────
            'cni'            => ['nullable', 'string', 'max:20'],
            'date_naissance' => ['nullable', 'date'],
            'genre'          => ['nullable', 'in:M,F'],
            'nationalite'    => ['nullable', 'string', 'max:100'],
            'ville'          => ['nullable', 'string', 'max:100'],
            'quartier'       => ['nullable', 'string', 'max:100'],
            // ── Propriétaire ──────────────────────────────────────────────
            'mode_paiement_prefere' => ['nullable', 'in:especes,virement,wave,orange_money,free_money,cheque,mobile_money'],
            'banque'                => ['nullable', 'string', 'max:100'],
            'numero_compte'         => ['nullable', 'string', 'max:50'],
            'numero_wave'           => ['nullable', 'string', 'max:20'],
            'numero_om'             => ['nullable', 'string', 'max:20'],
            'ninea'                 => ['nullable', 'string', 'max:20'],
            'est_personne_morale_is'=> ['nullable', 'boolean'],
            'assujetti_tva'         => ['nullable', 'boolean'],
            'brs_dispense'          => ['nullable', 'boolean'],
            // ── Locataire ─────────────────────────────────────────────────
            'type_locataire'   => ['nullable', 'in:particulier,entreprise,association,ambassade,ong'],
            'est_entreprise'   => ['nullable', 'boolean'],
            'nom_entreprise'   => ['nullable', 'string', 'max:255'],
            'ninea_locataire'  => ['nullable', 'string', 'max:30'],
            'rccm_locataire'   => ['nullable', 'string', 'max:60'],
            'taux_brs_override'=> ['nullable', 'numeric', 'min:0', 'max:20'],
            'profession'            => ['nullable', 'string', 'max:100'],
            'employeur'             => ['nullable', 'string', 'max:100'],
            'revenu_mensuel'        => ['nullable', 'numeric', 'min:0'],
            'contact_urgence_nom'   => ['nullable', 'string', 'max:100'],
            'contact_urgence_tel'   => ['nullable', 'string', 'max:20'],
            'contact_urgence_lien'  => ['nullable', 'string', 'max:50'],
            // ── Pièce d'identité (import de fichier) ───────────────────────
            'piece_identite'        => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:5120'],
        ], [
            'email.unique'             => 'Cet email est déjà utilisé par un autre compte.',
            'name.required'            => 'Le nom complet est obligatoire.',
            'genre.in'                 => 'Genre invalide.',
            'mode_paiement_prefere.in' => 'Mode de paiement invalide.',
            'type_locataire.in'        => 'Type de locataire invalide.',
            'piece_identite.mimes'     => 'Formats acceptés pour la pièce : JPG, PNG, WEBP ou PDF.',
            'piece_identite.max'       => 'La pièce ne doit pas dépasser 5 Mo.',
        ]);

        // agency_id forcé côté serveur — jamais depuis le formulaire
        $user = DB::transaction(function () use ($validated, $request) {
            $user                    = new User();
            $user->name              = $validated['name'];
            $user->email             = $validated['email'] ?? null;
            $user->telephone         = $validated['telephone'] ?? null;
            $user->adresse           = $validated['adresse'] ?? null;
            $user->password          = ! empty($validated['password'])
                ? Hash::make($validated['password'])
                : Hash::make(\Illuminate\Support\Str::random(32));
            $user->role              = $validated['role'];
            $user->agency_id         = Auth::user()->agency_id;
            $user->email_verified_at = ! empty($validated['email']) ? now() : null;
            $user->save();

            // Pièce d'identité importée — stockée sous pieces_identite/{user_id}
            // sur le disque « public » (une seule pièce par profil à la création).
            $piecePath = $request->hasFile('piece_identite')
                ? $request->file('piece_identite')->store('pieces_identite/' . $user->id, 'public')
                : null;

            if ($validated['role'] === 'proprietaire') {
                Proprietaire::create([
                    'user_id'               => $user->id,
                    'cni'                   => $validated['cni'] ?? null,
                    'date_naissance'        => $validated['date_naissance'] ?? null,
                    'genre'                 => $validated['genre'] ?? null,
                    'nationalite'           => $validated['nationalite'] ?? 'Sénégalaise',
                    'ville'                 => $validated['ville'] ?? 'Dakar',
                    'quartier'              => $validated['quartier'] ?? null,
                    'piece_identite_path'   => $piecePath,
                    'mode_paiement_prefere' => $validated['mode_paiement_prefere'] ?? 'virement',
                    'banque'                => $validated['banque'] ?? null,
                    'numero_compte'         => $validated['numero_compte'] ?? null,
                    'numero_wave'           => $validated['numero_wave'] ?? null,
                    'numero_om'             => $validated['numero_om'] ?? null,
                    'ninea'                 => $validated['ninea'] ?? null,
                    'est_personne_morale_is'=> filter_var($request->input('est_personne_morale_is'), FILTER_VALIDATE_BOOLEAN),
                    'assujetti_tva'         => $request->boolean('assujetti_tva'),
                    'brs_dispense'          => $request->boolean('brs_dispense'),
                ]);
            } else {
                Locataire::create([
                    'user_id'              => $user->id,
                    'cni'                  => $validated['cni'] ?? null,
                    'date_naissance'       => $validated['date_naissance'] ?? null,
                    'genre'                => $validated['genre'] ?? null,
                    'nationalite'          => $validated['nationalite'] ?? 'Sénégalaise',
                    'ville'                => $validated['ville'] ?? 'Dakar',
                    'quartier'             => $validated['quartier'] ?? null,
                    'piece_identite_path'  => $piecePath,
                    'type_locataire'       => $validated['type_locataire'] ?? 'particulier',
                    'est_entreprise'       => filter_var($request->input('est_entreprise'), FILTER_VALIDATE_BOOLEAN),
                    'nom_entreprise'       => $validated['nom_entreprise'] ?? null,
                    'ninea_locataire'      => $validated['ninea_locataire'] ?? null,
                    'rccm_locataire'       => $validated['rccm_locataire'] ?? null,
                    'taux_brs_override'    => $validated['taux_brs_override'] ?? null,
                    'profession'           => $validated['profession'] ?? null,
                    'employeur'            => $validated['employeur'] ?? null,
                    'revenu_mensuel'       => $validated['revenu_mensuel'] ?? null,
                    'contact_urgence_nom'  => $validated['contact_urgence_nom'] ?? null,
                    'contact_urgence_tel'  => $validated['contact_urgence_tel'] ?? null,
                    'contact_urgence_lien' => $validated['contact_urgence_lien'] ?? null,
                ]);
            }

            return $user;
        });

        if ($validated['role'] === 'proprietaire') {
            return redirect()
                ->route('admin.users.proprietaires')
                ->with('success', "Propriétaire {$user->name} créé ✓");
        }

        return redirect()
            ->route('admin.users.locataires')
            ->with('success', "Locataire {$user->name} créé ✓");
    }

    // ─────────────────────────────────────────────────────────────────────
    // FICHE DÉTAILLÉE
    // ─────────────────────────────────────────────────────────────────────

    public function show(User $user)
    {
        $this->authorize('isAdmin');
        $this->verifierAppartenance($user);

        $stats = [];

        if ($user->isProprietaire()) {
            // On récupère les IDs des contrats via une sous-requête
            $contratIds = Contrat::whereHas(
                'bien', fn($q) => $q->where('proprietaire_id', $user->id)
            )->pluck('id');

            /**
             * PERFORMANCE — Agrégats SQL groupés :
             * Une seule requête pour toutes les sommes au lieu de 5 count/sum séparés.
             */
            $aggr = Paiement::whereIn('contrat_id', $contratIds)
                ->where('statut', 'valide')
                ->selectRaw('
                    COALESCE(SUM(montant_encaisse), 0)          AS total_loyers,
                    COALESCE(SUM(net_a_verser_proprietaire), 0) AS total_net,
                    COALESCE(SUM(commission_ttc), 0)            AS total_commission,
                    COUNT(*)                                     AS nb_paiements
                ')
                ->first();

            $stats = [
                'nb_biens'         => Bien::where('proprietaire_id', $user->id)->count(),
                'nb_biens_loues'   => Bien::where('proprietaire_id', $user->id)->where('statut', 'loue')->count(),
                'total_loyers'     => (float) $aggr->total_loyers,
                'total_net'        => (float) $aggr->total_net,
                'total_commission' => (float) $aggr->total_commission,
                'nb_paiements'     => (int)   $aggr->nb_paiements,
            ];

            $biens = Bien::where('proprietaire_id', $user->id)
                ->select(['id', 'agency_id', 'proprietaire_id', 'reference', 'type', 'adresse', 'ville', 'statut', 'loyer_mensuel'])
                ->with([
                    'contratActif:id,bien_id,locataire_id,statut,loyer_contractuel,date_debut',
                    'contratActif.locataire:id,name',
                ])
                ->orderByDesc('created_at')
                ->paginate(5);

            $paiements = Paiement::whereIn('contrat_id', $contratIds)
                ->where('statut', 'valide')
                ->select(['id', 'agency_id', 'contrat_id', 'periode', 'montant_encaisse', 'net_proprietaire', 'net_a_verser_proprietaire', 'mode_paiement', 'date_paiement', 'reference_paiement'])
                ->with(['contrat:id,bien_id', 'contrat.bien:id,reference'])
                ->orderByDesc('date_paiement')
                ->paginate(10);

            $locatairesActifs = Contrat::whereIn('id', $contratIds)
                ->where('statut', 'actif')
                ->select(['id', 'bien_id', 'locataire_id', 'loyer_contractuel', 'date_debut'])
                ->with([
                    'locataire:id,name,email,telephone',
                    'bien:id,reference,adresse,ville',
                ])
                ->get();

            // ── Estimation IRPP foncier — Propriétaires PARTICULIERS uniquement ──
            // Les personnes morales relèvent de l'IS (pas de l'IRPP). On lit le profil
            // sans global scope (le propriétaire appartient déjà à l'agence de l'admin).
            $profilProprio  = \App\Models\Proprietaire::withoutGlobalScopes()
                ->where('user_id', $user->id)->first();
            $estParticulier = $profilProprio && ! $profilProprio->est_personne_morale_is;
            $annee          = now()->year;

            // ── Option CGF (régime synthétique optionnel — Art. 75) ──────────────
            // Réservée aux Particuliers. Si active pour l'année en cours, elle COUVRE
            // et masque l'IRPP-foncier + la CFPB (exclusion mutuelle — CGF-02).
            $cgfInfo    = ($estParticulier && $profilProprio) ? [
                'active'            => $profilProprio->cgf_active,
                'annee'             => $profilProprio->cgf_annee,
                'revenu_prevu'      => $profilProprio->cgf_revenu_brut_prevu,
                'montant'           => $profilProprio->cgf_montant,
                'mode_paiement'     => $profilProprio->cgf_mode_paiement,
                'echeances'         => $profilProprio->cgf_echeances ?? [],
                'declaration_avant' => sprintf('%04d-02-01', $profilProprio->cgf_annee ?: $annee),
            ] : null;
            $cgfCouvre  = $profilProprio && $profilProprio->cgfCouvre($annee);

            // IRPP masqué si la CGF couvre l'année en cours (données sous-jacentes intactes).
            $irppEstimation = ($estParticulier && ! $cgfCouvre)
                ? \App\Services\FiscalService::estimerIrppFoncier($user->id, $annee, $user->agency_id)
                : null;

            // ── CFPB agrégée (§5.2) — somme des estimations par bien ─────────────
            // S'applique à TOUS les propriétaires (y compris personnes morales).
            // Masquée uniquement si la CGF couvre l'année (elle regroupe la CFPB).
            $cfpbTotal = $cgfCouvre
                ? null
                : (int) Bien::where('proprietaire_id', $user->id)->sum('cfpb_montant_estime');

            return view('users.show', compact(
                'user', 'biens', 'stats', 'paiements', 'locatairesActifs',
                'irppEstimation', 'estParticulier', 'cgfInfo', 'cgfCouvre', 'annee', 'cfpbTotal'
            ));
        }

        if ($user->isLocataire()) {
            $user->load('locataire');

            // Tous les contrats (actif + historique) — garant inclus (colonnes complètes).
            $contrats = Contrat::where('locataire_id', $user->id)
                ->with('bien:id,reference,adresse,ville,type')
                ->orderByDesc('date_debut')
                ->get();

            $contratActif = $contrats->firstWhere('statut', 'actif');

            // ── Statut de paiement CALCULÉ (jamais stocké) ────────────────
            $periode = now()->startOfMonth();
            $paie    = ['etat' => 'aucun', 'jours' => 0, 'periode' => $periode];

            if ($contratActif) {
                $paye = Paiement::where('contrat_id', $contratActif->id)
                    ->where('statut', 'valide')
                    ->whereYear('periode', $periode->year)
                    ->whereMonth('periode', $periode->month)
                    ->exists();

                $joursRetard = (int) $periode->copy()->addDays(5)->diffInDays(now(), false);

                $paie = ($paye || $joursRetard <= 0)
                    ? ['etat' => 'ok', 'jours' => 0, 'periode' => $periode]
                    : ['etat' => 'retard', 'jours' => $joursRetard, 'periode' => $periode];

                $aggr = Paiement::where('contrat_id', $contratActif->id)
                    ->where('statut', 'valide')
                    ->selectRaw('COALESCE(SUM(montant_encaisse), 0) AS total_paye, COUNT(*) AS nb_paiements')
                    ->first();

                $stats = [
                    'nb_paiements' => (int)   ($aggr->nb_paiements ?? 0),
                    'total_paye'   => (float) ($aggr->total_paye ?? 0),
                ];
            }

            return view('users.show', compact('user', 'contrats', 'contratActif', 'paie', 'stats'));
        }

        return view('users.show', compact('user', 'stats'));
    }

    // ─────────────────────────────────────────────────────────────────────
    // OPTION CGF (régime synthétique optionnel — Art. 75 CGI SN)
    // ─────────────────────────────────────────────────────────────────────

    /**
     * Exerce l'option CGF pour un propriétaire particulier, une année donnée.
     *
     * Le revenu brut PRÉVISIONNEL est saisi manuellement (loyers attendus de
     * l'année à venir — distinct des données Comptabilité réelles). Au-delà de
     * 30 000 000 F : inéligible, option bloquée (CGF-01). Le montant et
     * l'échéancier sont calculés puis persistés (CGF-03/CGF-05).
     */
    public function cgfOption(Request $request, User $user): RedirectResponse
    {
        $this->authorize('isAdmin');
        $this->verifierAppartenance($user);

        abort_unless($user->isProprietaire(), 404);

        $profil = \App\Models\Proprietaire::withoutGlobalScopes()
            ->where('user_id', $user->id)->first();

        // Réservé aux Particuliers : une personne morale relève de l'IS, pas de la CGF.
        abort_if(! $profil || $profil->est_personne_morale_is, 403,
            'La CGF est réservée aux propriétaires particuliers (personnes physiques).');

        $validated = $request->validate([
            'cgf_annee'             => ['required', 'integer', 'min:2020', 'max:2100'],
            'cgf_revenu_brut_prevu' => ['required', 'integer', 'min:0'],
            'cgf_mode_paiement'     => ['required', 'in:unique,trois_versements'],
        ], [
            'cgf_revenu_brut_prevu.required' => 'Le loyer brut prévisionnel est obligatoire.',
            'cgf_mode_paiement.in'           => 'Mode de paiement invalide.',
        ]);

        $revenu = (int) $validated['cgf_revenu_brut_prevu'];

        // CGF-01 : seuil d'éligibilité 30 000 000 F.
        if ($revenu > \App\Services\FiscalService::CGF_SEUIL) {
            return back()->withInput()->with('cgf_error',
                'Loyer brut prévisionnel supérieur à 30 000 000 F : ce propriétaire n\'est pas '
                . 'éligible à la CGF. Il relève du régime réel (IRPP + CFPB).');
        }

        $calcul    = \App\Services\FiscalService::calculerCGF((float) $revenu);
        $echeances = \App\Services\FiscalService::calculerEcheancierCgf(
            $calcul['montant'], $validated['cgf_mode_paiement'], (int) $validated['cgf_annee']
        );

        $profil->update([
            'cgf_active'            => true,
            'cgf_annee'             => (int) $validated['cgf_annee'],
            'cgf_revenu_brut_prevu' => $revenu,
            'cgf_montant'           => (int) $calcul['montant'],
            'cgf_mode_paiement'     => $validated['cgf_mode_paiement'],
            'cgf_echeances'         => $echeances,
        ]);

        return back()->with('success', sprintf(
            'Option CGF %d enregistrée : %s F (%s). L\'IRPP foncier et la CFPB de cette année '
            . 'sont désormais couverts par la CGF.',
            $validated['cgf_annee'],
            number_format($calcul['montant'], 0, ',', ' '),
            $calcul['fraction_label']
        ));
    }

    /** Révoque l'option CGF (retour au régime réel IRPP + CFPB). */
    public function cgfDesactiver(User $user): RedirectResponse
    {
        $this->authorize('isAdmin');
        $this->verifierAppartenance($user);

        abort_unless($user->isProprietaire(), 404);

        $profil = \App\Models\Proprietaire::withoutGlobalScopes()
            ->where('user_id', $user->id)->first();

        if ($profil) {
            $profil->update([
                'cgf_active'            => false,
                'cgf_annee'             => null,
                'cgf_revenu_brut_prevu' => null,
                'cgf_montant'           => null,
                'cgf_mode_paiement'     => null,
                'cgf_echeances'         => null,
            ]);
        }

        return back()->with('success', 'Option CGF retirée. Le propriétaire est de nouveau au régime réel (IRPP + CFPB).');
    }

    // ─────────────────────────────────────────────────────────────────────
    // FORMULAIRE ÉDITION
    // ─────────────────────────────────────────────────────────────────────

    public function edit(User $user)
    {
        $this->authorize('isAdmin');
        $this->verifierAppartenance($user);

        $user->load('proprietaire', 'locataire');

        return view('users.edit', compact('user'));
    }

    // ─────────────────────────────────────────────────────────────────────
    // MISE À JOUR
    // ─────────────────────────────────────────────────────────────────────

     public function update(Request $request, User $user): RedirectResponse
{
    $this->authorize('isAdmin');
    $this->verifierAppartenance($user);

    // ── Champs User communs ───────────────────────────────────────────
    $validated = $request->validate([
        'name'      => ['required', 'string', 'max:255'],
        'email'     => ['nullable', 'email', 'unique:users,email,' . $user->id],
        'telephone' => ['nullable', 'string', 'max:30'],
        'adresse'   => ['nullable', 'string', 'max:255'],
        'piece_identite' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:5120'],
    ], [
        'email.unique'         => 'Cet email est déjà utilisé par un autre compte.',
        'name.required'        => 'Le nom complet est obligatoire.',
        'piece_identite.mimes' => 'Formats acceptés pour la pièce : JPG, PNG, WEBP ou PDF.',
        'piece_identite.max'   => 'La pièce ne doit pas dépasser 5 Mo.',
    ]);

    DB::transaction(function () use ($user, $validated, $request) {
        $user->update($validated);

    // ── Pièce d'identité — remplacement optionnel ─────────────────────
    // Un nouvel upload remplace l'ancien fichier (supprimé du disque).
    $profil       = $user->isProprietaire() ? $user->proprietaire : $user->locataire;
    $newPiecePath = null;
    if ($request->hasFile('piece_identite') && $profil) {
        if ($profil->piece_identite_path) {
            Storage::disk('public')->delete($profil->piece_identite_path);
        }
        $newPiecePath = $request->file('piece_identite')->store('pieces_identite/' . $user->id, 'public');
    }

    // ── Profil PROPRIÉTAIRE ───────────────────────────────────────────
    if ($user->isProprietaire() && $user->proprietaire) {
        $profilData = $request->validate([
            'cni'                   => ['nullable', 'string', 'max:20'],
            'date_naissance'        => ['nullable', 'date'],
            'genre'                 => ['nullable', 'in:M,F'],
            'nationalite'           => ['nullable', 'string', 'max:50'],
            'telephone_secondaire'  => ['nullable', 'string', 'max:30'],
            'adresse_domicile'      => ['nullable', 'string', 'max:255'],
            'ville'                 => ['nullable', 'string', 'max:100'],
            'quartier'              => ['nullable', 'string', 'max:100'],
            'mode_paiement_prefere' => ['nullable', 'in:especes,virement,wave,orange_money,free_money,cheque,mobile_money'],
            'banque'                => ['nullable', 'string', 'max:100'],
            'numero_compte'         => ['nullable', 'string', 'max:50'],
            'numero_wave'           => ['nullable', 'string', 'max:20'],
            'numero_om'             => ['nullable', 'string', 'max:20'],
            'ninea'                 => ['nullable', 'string', 'max:20'],
            'assujetti_tva'         => ['nullable', 'boolean'],
            'est_personne_morale_is'=> ['nullable', 'boolean'],
            'brs_dispense'          => ['nullable', 'boolean'],
        ]);

        $profilData['assujetti_tva']          = $request->boolean('assujetti_tva');
        $profilData['est_personne_morale_is'] = $request->boolean('est_personne_morale_is');
        $profilData['brs_dispense']           = $request->boolean('brs_dispense');
        if ($newPiecePath) {
            $profilData['piece_identite_path'] = $newPiecePath;
        }
        $user->proprietaire->update($profilData);
    }

    // ── Profil LOCATAIRE ─────────────────────────────────────────────
    if ($user->isLocataire() && $user->locataire) {
        $profilData = $request->validate([
            'cni'                  => ['nullable', 'string', 'max:20'],
            'date_naissance'       => ['nullable', 'date'],
            'genre'                => ['nullable', 'in:M,F'],
            'nationalite'          => ['nullable', 'string', 'max:50'],
            'ville'                => ['nullable', 'string', 'max:100'],
            'quartier'             => ['nullable', 'string', 'max:100'],
            'profession'           => ['nullable', 'string', 'max:100'],
            'employeur'            => ['nullable', 'string', 'max:150'],
            'revenu_mensuel'       => ['nullable', 'numeric', 'min:0'],
            'contact_urgence_nom'  => ['nullable', 'string', 'max:150'],
            'contact_urgence_tel'  => ['nullable', 'string', 'max:20'],
            'contact_urgence_lien' => ['nullable', 'string', 'max:50'],
            // Fiscal
            'type_locataire'       => ['nullable', 'in:particulier,entreprise,association,ambassade,ong'],
            'nom_entreprise'       => ['nullable', 'string', 'max:150'],
            'ninea_locataire'      => ['nullable', 'string', 'max:30'],
            'rccm_locataire'       => ['nullable', 'string', 'max:60'],
            'taux_brs_override'    => ['nullable', 'numeric', 'min:0', 'max:20'],
        ]);

        // est_entreprise déduit du type_locataire
        $profilData['est_entreprise'] = in_array(
            $profilData['type_locataire'] ?? 'particulier',
            ['entreprise', 'association']
        );

        // Si pas entreprise → effacer les infos entreprise
        if (! $profilData['est_entreprise']) {
            $profilData['nom_entreprise']    = null;
            $profilData['ninea_locataire']   = null;
            $profilData['rccm_locataire']    = null;
            $profilData['taux_brs_override'] = null;
        }

        if ($newPiecePath) {
            $profilData['piece_identite_path'] = $newPiecePath;
        }

        $user->locataire->update($profilData);
        // NB : le taux_brs_override du locataire est lu en direct par le moteur
        // (cascade niveau 2) ; l'applicabilité de la BRS dépend du bailleur, pas
        // du locataire → aucune propagation vers les contrats (correctif B2).
    }
    }); // fin DB::transaction

    return redirect()
        ->route('admin.users.show', $user)
        ->with('success', 'Profil mis à jour ✓');
}

    // ─────────────────────────────────────────────────────────────────────
    // SUPPRESSION
    // ─────────────────────────────────────────────────────────────────────

    public function destroy(User $user): RedirectResponse
    {
        $this->authorize('isAdmin');
        $this->verifierAppartenance($user);

        if ($user->isLocataire() && $user->contrats()->where('statut', 'actif')->exists()) {
            return back()->withErrors([
                'general' => 'Impossible de supprimer un locataire avec un contrat actif.',
            ]);
        }

        if ($user->isProprietaire() && $user->biens()->whereIn('statut', ['loue', 'disponible', 'en_travaux'])->exists()) {
            return back()->withErrors([
                'general' => 'Impossible de supprimer un propriétaire avec des biens actifs. Archivez tous ses biens avant de continuer.',
            ]);
        }

        $user->delete();

        return back()->with('success', 'Utilisateur supprimé ✓');
    }

    // ─────────────────────────────────────────────────────────────────────
    // COMPOSANT « RECHERCHER-OU-CRÉER » — recherche + création rapide (JSON)
    // ─────────────────────────────────────────────────────────────────────

    public function proprietaireSearch(Request $request)
    {
        $this->authorize('isAdmin');

        $agencyId = Auth::user()->agency_id;
        $q = trim((string) $request->query('q'));

        $users = User::where('role', 'proprietaire')
            ->where('agency_id', $agencyId)
            ->when($q !== '', function ($query) use ($q) {
                $query->where(fn ($s) => $s
                    ->where('name', 'like', "%{$q}%")
                    ->orWhere('telephone', 'like', "%{$q}%"));
            })
            ->withCount('biens')
            ->orderBy('name')
            ->limit(8)
            ->get();

        return response()->json(
            $users->map(fn (User $u) => [
                'id'        => $u->id,
                'name'      => $u->name,
                'telephone' => $u->telephone,
                'sub'       => trim(($u->telephone ? $u->telephone.' · ' : '').$u->biens_count.' bien'.($u->biens_count > 1 ? 's' : '')),
                'initials'  => mb_strtoupper(mb_substr($u->name, 0, 2)),
            ])
        );
    }

    public function proprietaireQuickStore(Request $request)
    {
        $this->authorize('isAdmin');

        $data = $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'telephone' => ['nullable', 'string', 'max:30'],
        ], [
            'name.required' => 'Le nom est obligatoire.',
        ]);

        $agencyId = Auth::user()->agency_id;

        // Anti-doublon : nom identique (insensible casse/espaces) dans l'agence.
        // Évite de créer deux fois le même propriétaire depuis le champ inline.
        $existing = User::where('role', 'proprietaire')
            ->where('agency_id', $agencyId)
            ->whereRaw('LOWER(TRIM(name)) = ?', [mb_strtolower(trim($data['name']))])
            ->withCount('biens')
            ->first();

        if ($existing) {
            return response()->json([
                'id'        => $existing->id,
                'name'      => $existing->name,
                'telephone' => $existing->telephone,
                'sub'       => 'Déjà existant · '.$existing->biens_count.' bien'.($existing->biens_count > 1 ? 's' : ''),
                'initials'  => mb_strtoupper(mb_substr($existing->name, 0, 2)),
                'duplicate' => true,
            ]);
        }

        $user = DB::transaction(function () use ($data, $agencyId) {
            $u            = new User();
            $u->name      = $data['name'];
            $u->telephone = $data['telephone'] ?? null;
            $u->role      = 'proprietaire';
            $u->agency_id = $agencyId;
            $u->password  = Hash::make(\Illuminate\Support\Str::random(32));
            $u->save();

            Proprietaire::create([
                'user_id'               => $u->id,
                'nationalite'           => 'Sénégalaise',
                'ville'                 => 'Dakar',
                'mode_paiement_prefere' => 'virement',
            ]);

            return $u;
        });

        return response()->json([
            'id'        => $user->id,
            'name'      => $user->name,
            'telephone' => $user->telephone,
            'sub'       => "Créé à l'instant · 0 bien",
            'initials'  => mb_strtoupper(mb_substr($user->name, 0, 2)),
        ], 201);
    }

    public function locataireSearch(Request $request)
    {
        $this->authorize('isAdmin');

        $agencyId = Auth::user()->agency_id;
        $q = trim((string) $request->query('q'));

        $users = User::where('role', 'locataire')
            ->where('agency_id', $agencyId)
            // Seulement les locataires « libres » (sans contrat actif) — la validation
            // refuse de toute façon un locataire déjà engagé.
            ->whereDoesntHave('contrats', fn ($c) => $c->where('statut', 'actif'))
            ->when($q !== '', fn ($query) => $query->where(fn ($s) => $s
                ->where('name', 'like', "%{$q}%")
                ->orWhere('telephone', 'like', "%{$q}%")))
            ->orderBy('name')
            ->limit(8)
            ->get();

        return response()->json(
            $users->map(fn (User $u) => [
                'id'       => $u->id,
                'name'     => $u->name,
                'sub'      => $u->telephone ?? $u->email ?? '',
                'initials' => mb_strtoupper(mb_substr($u->name, 0, 2)),
            ])
        );
    }

    public function locataireQuickStore(Request $request)
    {
        $this->authorize('isAdmin');

        $data = $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'telephone' => ['nullable', 'string', 'max:30'],
        ], ['name.required' => 'Le nom est obligatoire.']);

        $agencyId = Auth::user()->agency_id;

        $existing = User::where('role', 'locataire')
            ->where('agency_id', $agencyId)
            ->whereRaw('LOWER(TRIM(name)) = ?', [mb_strtolower(trim($data['name']))])
            ->first();

        if ($existing) {
            return response()->json([
                'id'        => $existing->id,
                'name'      => $existing->name,
                'sub'       => $existing->telephone ?? 'Déjà existant',
                'initials'  => mb_strtoupper(mb_substr($existing->name, 0, 2)),
                'duplicate' => true,
            ]);
        }

        $user = DB::transaction(function () use ($data, $agencyId) {
            $u            = new User();
            $u->name      = $data['name'];
            $u->telephone = $data['telephone'] ?? null;
            $u->role      = 'locataire';
            $u->agency_id = $agencyId;
            $u->password  = Hash::make(\Illuminate\Support\Str::random(32));
            $u->save();

            Locataire::create([
                'user_id'        => $u->id,
                'type_locataire' => 'particulier',
                'est_entreprise' => false,
            ]);

            return $u;
        });

        return response()->json([
            'id'        => $user->id,
            'name'      => $user->name,
            'sub'       => "Créé à l'instant",
            'initials'  => mb_strtoupper(mb_substr($user->name, 0, 2)),
        ], 201);
    }
}