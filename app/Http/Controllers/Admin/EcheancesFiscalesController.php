<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bien;
use App\Models\User;
use App\Services\CalendrierFiscalService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class EcheancesFiscalesController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('check.feature:fiscalite'),
        ];
    }

    /**
     * Écran 1 — Liste des propriétaires. Consomme l'agrégation CalendrierFiscalService
     * (aucun recalcul ici). Les 4 tuiles restent globales ; le reste regroupe les
     * échéances par propriétaire (statut, montant dû sous 30 j, nb biens/échéances).
     */
    public function index(CalendrierFiscalService $service): View
    {
        $today    = Carbon::now()->timezone('Africa/Dakar')->startOfDay();
        /** @var \App\Models\User $authUser */
        $authUser = auth()->user();
        $agencyId = (int) $authUser->agency_id;

        // Toute l'année à venir + les échéances en retard encore dues.
        $echeances = collect($service->echeancesAVenir($agencyId, 365, $today, true));

        // ── Tuiles de résumé (GLOBAL, logique inchangée) ────────────────────
        $resume = ['nb_retard' => 0, 'somme_retard' => 0, 'somme_7j' => 0, 'somme_30j' => 0, 'total_annee' => 0];
        $j7  = $today->copy()->addDays(7);
        $j30 = $today->copy()->addDays(30);

        foreach ($echeances as $e) {
            $montant = (int) ($e['montant'] ?? 0); // lignes agence = null → 0 (exclues des sommes)
            $resume['total_annee'] += $montant;

            $d = $e['date_limite'] ? Carbon::parse($e['date_limite']) : null;
            if ($d === null) {
                continue; // IS agence (date à confirmer) — pas de montant
            } elseif ($d->lt($today)) {
                $resume['nb_retard']++;
                $resume['somme_retard'] += $montant;
            } elseif ($d->lte($j7)) {
                $resume['somme_7j']  += $montant;
                $resume['somme_30j'] += $montant;
            } elseif ($d->lte($j30)) {
                $resume['somme_30j'] += $montant;
            }
        }

        // ── Regroupement par propriétaire (les lignes agence n'en ont pas) ──
        $parProp = $echeances->filter(fn ($e) => $e['proprietaire_id'] !== null)->groupBy('proprietaire_id');

        $users = User::where('agency_id', $agencyId)
            ->where('role', 'proprietaire')
            ->orderBy('name')
            ->get(['id', 'name']);

        $nbBiens = Bien::withoutGlobalScopes()
            ->where('agency_id', $agencyId)
            ->selectRaw('proprietaire_id, COUNT(*) as c')
            ->groupBy('proprietaire_id')
            ->pluck('c', 'proprietaire_id');

        $proprietaires = $users
            ->map(fn (User $u) => $this->statsProprietaire($u, $parProp->get($u->id, collect()), (int) ($nbBiens[$u->id] ?? 0), $today, $j30))
            // Un propriétaire apparaît s'il a au moins un bien OU au moins une échéance.
            ->filter(fn (array $p) => $p['nb_biens'] > 0 || $p['nb_a_venir'] > 0 || $p['nb_retard'] > 0)
            ->values();

        return view('admin.echeances-fiscales.index', compact('proprietaires', 'resume', 'today'));
    }

    /**
     * Écran 2 — Fiche fiscale d'un propriétaire. Toutes ses taxes regroupées, avec
     * le registre de calcul déplié (base → taux → résultat) fourni par le service.
     */
    public function proprietaire(User $proprietaire, CalendrierFiscalService $service): View
    {
        /** @var \App\Models\User $authUser */
        $authUser = auth()->user();
        $agencyId = (int) $authUser->agency_id;

        abort_if($proprietaire->agency_id !== $agencyId || $proprietaire->role !== 'proprietaire', 404);

        $today     = Carbon::now()->timezone('Africa/Dakar')->startOfDay();
        $echeances = collect($service->echeancesProprietaire($agencyId, $proprietaire->id, $today));
        $j30       = $today->copy()->addDays(30);

        $montant30 = (int) $echeances
            ->filter(fn ($e) => $e['date_limite'] && Carbon::parse($e['date_limite'])->between($today, $j30))
            ->sum(fn ($e) => (int) ($e['montant'] ?? 0));

        // Points de calcul à confirmer = échéances portant un badge « Estimation ».
        $nbEstimations = $echeances
            ->filter(fn ($e) => ! in_array($e['statut_calcul'] ?? null, ['confirme', null], true))
            ->count();

        $nbBiens = Bien::withoutGlobalScopes()
            ->where('agency_id', $agencyId)
            ->where('proprietaire_id', $proprietaire->id)
            ->count();

        return view('admin.echeances-fiscales.proprietaire', compact(
            'proprietaire', 'echeances', 'today', 'montant30', 'nbEstimations', 'nbBiens'
        ));
    }

    /**
     * Statistiques d'un propriétaire pour la carte de l'écran 1.
     *
     * @param  \Illuminate\Support\Collection<int, array>  $echs  Échéances du propriétaire.
     */
    private function statsProprietaire(User $u, Collection $echs, int $nbBiens, Carbon $today, Carbon $j30): array
    {
        // Le statut de triage ne reflète que ce qui est réellement DÛ (montant > 0) :
        // une déclaration structurelle à 0 F (BRS/IRPP sans loyer encaissé) laisse le
        // propriétaire « À jour ». Les vraies dates de dépôt restent portées par la
        // fiche (écran 2) et les rappels automatiques.
        $du     = $echs->filter(fn ($e) => (int) ($e['montant'] ?? 0) > 0);
        $retard = $du->filter(fn ($e) => $e['date_limite'] && Carbon::parse($e['date_limite'])->lt($today));
        $aVenir = $du->filter(fn ($e) => $e['date_limite'] && Carbon::parse($e['date_limite'])->gte($today));

        $montant30 = (int) $echs
            ->filter(fn ($e) => $e['date_limite'] && Carbon::parse($e['date_limite'])->between($today, $j30))
            ->sum(fn ($e) => (int) ($e['montant'] ?? 0));

        $statut = 'clear';
        $jours  = null;
        if ($retard->isNotEmpty()) {
            $statut = 'late';
        } elseif ($aVenir->isNotEmpty()) {
            $statut = 'upcoming';
            $jours  = (int) $aVenir->map(fn ($e) => (int) $today->diffInDays(Carbon::parse($e['date_limite'])))->min();
        }

        return [
            'id'          => $u->id,
            'name'        => $u->name,
            'nb_biens'    => $nbBiens,
            'nb_a_venir'  => $aVenir->count(),
            'nb_retard'   => $retard->count(),
            'montant_30j' => $montant30,
            'statut'      => $statut,
            'jours'       => $jours,
        ];
    }

    /**
     * Calendrier agrégé des échéances à venir (JSON) — croise Propriétaires / Biens /
     * Contrats de l'agence + 3 échéances agence. Backend/agrégation : la sortie visuelle
     * définitive viendra avec le reste du front. Paramètre ?horizon=jours (défaut 30).
     */
    public function calendrier(Request $request, CalendrierFiscalService $service): JsonResponse
    {
        /** @var \App\Models\User $authUser */
        $authUser = auth()->user();

        $horizon = (int) $request->query('horizon', 30);
        $horizon = max(1, min($horizon, 366));

        return response()->json([
            'horizon_jours' => $horizon,
            'genere_le'     => Carbon::now()->timezone('Africa/Dakar')->toDateString(),
            'echeances'     => $service->echeancesAVenir($authUser->agency_id, $horizon),
        ]);
    }

    /**
     * Construit la liste complète des échéances pour une agence.
     * Méthode publique et statique pour faciliter les tests unitaires.
     */
    public static function buildEcheances(string $formeJuridique, Carbon $today): array
    {
        $annee     = $today->year;
        $isSociete = in_array($formeJuridique, ['sarl', 'sa', 'sas']);

        // Échéances annuelles fixes — 'lien_route' = nom de route Laravel (résolu dans index(), pas ici)
        $definitions = [
            ['mois' => 1, 'jour' => 31, 'label' => 'CEL-VL (Valeur Locative)', 'type' => 'Déclaration', 'lien_route' => null,                            'lien_label' => 'Formulaire DGID', 'visible' => true],
            ['mois' => 1, 'jour' => 31, 'label' => 'BRS annuel',               'type' => 'Déclaration', 'lien_route' => 'admin.etats-trimestriels.index', 'lien_label' => 'États trim.',     'visible' => true],
            ['mois' => 2, 'jour' => 15, 'label' => 'IS — 1er acompte',         'type' => 'Paiement',    'lien_route' => null,                            'lien_label' => 'Info',            'visible' => $isSociete],
            ['mois' => 4, 'jour' => 30, 'label' => 'CEL-VA (Valeur Ajoutée)',  'type' => 'Déclaration', 'lien_route' => null,                            'lien_label' => 'Info',            'visible' => true],
            ['mois' => 4, 'jour' => 30, 'label' => 'IS — 2ème acompte',        'type' => 'Paiement',    'lien_route' => null,                            'lien_label' => 'Info',            'visible' => $isSociete],
            // IRPP revenus fonciers : 1er mars (source officielle gouv.sn — écarte le 30 avril
            // de la source privée). CGF : 1er février (texte officiel). Réf. regles_fiscales IR-04.
            ['mois' => 3, 'jour' => 1,  'label' => 'IRPP (revenus fonciers)',  'type' => 'Déclaration', 'lien_route' => 'admin.bilans-fiscaux.index',     'lien_label' => 'Bilans fisc.',    'visible' => true],
            ['mois' => 2, 'jour' => 1,  'label' => 'CGF',                      'type' => 'Déclaration', 'lien_route' => 'admin.bilans-fiscaux.index',     'lien_label' => 'Bilans fisc.',    'visible' => true],
            ['mois' => 6, 'jour' => 15, 'label' => 'IS — solde + IMF',         'type' => 'Paiement',    'lien_route' => null,                            'lien_label' => 'Info',            'visible' => $isSociete],
            // CFPB : déclaration avant le 31 janvier (brief CFPB R5 / regles_fiscales CFPB-05).
            ['mois' => 1, 'jour' => 31, 'label' => 'CFPB (propriétés bâties)', 'type' => 'Déclaration', 'lien_route' => 'admin.users.proprietaires',      'lien_label' => 'Propriétaires',   'visible' => true],
            // TEOM : déclarée dans les mêmes conditions que la CFPB → 31 janvier (brief TEOM R3 / TEOM-03).
            ['mois' => 1, 'jour' => 31, 'label' => 'TEOM (ordures ménagères)',  'type' => 'Déclaration', 'lien_route' => 'admin.users.proprietaires',      'lien_label' => 'Propriétaires',   'visible' => true],
        ];

        $echeances = [];

        foreach ($definitions as $def) {
            if (! $def['visible']) {
                continue;
            }

            $date = Carbon::create($annee, $def['mois'], $def['jour'])->endOfDay();

            // Si la date de cette année est passée, reporter à l'année suivante
            if ($date->lt($today)) {
                $date = Carbon::create($annee + 1, $def['mois'], $def['jour'])->endOfDay();
            }

            $echeances[] = [
                'mois_num'   => $def['mois'],
                'jour'       => $def['jour'],
                'date'       => $date,
                'label'      => $def['label'],
                'type'       => $def['type'],
                'statut'     => self::calculerStatut($date, $today),
                'lien_route' => $def['lien_route'],
                'lien'       => null, // résolu dans index()
                'lien_label' => $def['lien_label'],
                'recurrent'  => false,
            ];
        }

        // TVA mensuelle — prochaine occurrence le 15 du mois
        $dateTva = Carbon::create($annee, $today->month, 15)->endOfDay();
        if ($dateTva->lt($today)) {
            $dateTva = $dateTva->copy()->addMonth();
        }
        $echeances[] = [
            'mois_num'   => 0,
            'jour'       => 15,
            'date'       => $dateTva,
            'label'      => 'TVA mensuelle',
            'type'       => 'Déclaration',
            'statut'     => self::calculerStatut($dateTva, $today),
            'lien_route' => 'admin.tva-agence.index',
            'lien'       => null,
            'lien_label' => 'Déclarations TVA',
            'recurrent'  => true,
        ];

        // BRS mensuel (versement de la retenue)
        $echeances[] = [
            'mois_num'   => 0,
            'jour'       => 15,
            'date'       => null,
            'label'      => 'BRS mensuel',
            'type'       => 'Paiement',
            'statut'     => 'recurrent',
            'lien_route' => 'admin.etats-trimestriels.index',
            'lien'       => null,
            'lien_label' => 'États trimestriels',
            'recurrent'  => true,
        ];

        // BRS trimestriel (déclaration)
        $echeances[] = [
            'mois_num'   => 0,
            'jour'       => 15,
            'date'       => null,
            'label'      => 'BRS trimestriel',
            'type'       => 'Déclaration',
            'statut'     => 'recurrent',
            'lien_route' => 'admin.etats-trimestriels.index',
            'lien'       => null,
            'lien_label' => 'États trimestriels',
            'recurrent'  => true,
        ];

        // CFPE — hors périmètre Bimotech
        $echeances[] = [
            'mois_num'   => 99,
            'jour'       => null,
            'date'       => null,
            'label'      => 'CFPE',
            'type'       => 'Mensuel',
            'statut'     => 'hors_app',
            'lien_route' => null,
            'lien'       => null,
            'lien_label' => 'Géré hors Bimotech (paie)',
            'recurrent'  => true,
        ];

        // Tri chronologique : récurrents (mois_num=0) après les fixes, CFPE en dernier
        usort($echeances, function ($a, $b) {
            if ($a['mois_num'] !== $b['mois_num']) {
                return $a['mois_num'] <=> $b['mois_num'];
            }
            return ($a['jour'] ?? 99) <=> ($b['jour'] ?? 99);
        });

        return $echeances;
    }

    public static function calculerStatut(Carbon $date, Carbon $today): string
    {
        if ($date->lt($today)) {
            return 'passee';
        }
        $jours = (int) $today->diffInDays($date);
        if ($jours <= 7) {
            return 'urgent';
        }
        if ($jours <= 30) {
            return 'bientot';
        }
        return 'a_venir';
    }
}
