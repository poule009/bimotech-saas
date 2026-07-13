<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\CalendrierFiscalService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
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
     * Vue globale Fiscalité — consomme l'agrégation CalendrierFiscalService (aucun
     * recalcul ici). Regroupe les échéances par urgence (retard / 7 j / 30 j / plus
     * tard) et calcule les tuiles de résumé (montants agence exclus car sans montant).
     */
    public function index(CalendrierFiscalService $service): View
    {
        $today    = Carbon::now()->timezone('Africa/Dakar')->startOfDay();
        /** @var \App\Models\User $authUser */
        $authUser = auth()->user();

        // Toute l'année à venir + les échéances en retard encore dues.
        $echeances = $service->echeancesAVenir($authUser->agency_id, 365, $today, true);

        $groupes = ['late' => [], 'soon' => [], 'month' => [], 'later' => []];
        $resume  = ['nb_retard' => 0, 'somme_retard' => 0, 'somme_7j' => 0, 'somme_30j' => 0, 'total_annee' => 0];
        $j7  = $today->copy()->addDays(7);
        $j30 = $today->copy()->addDays(30);

        foreach ($echeances as $e) {
            $montant = (int) ($e['montant'] ?? 0); // lignes agence = null → 0 (exclues des sommes)
            $resume['total_annee'] += $montant;

            $d = $e['date_limite'] ? Carbon::parse($e['date_limite']) : null;
            if ($d === null) {
                $groupes['later'][] = $e;                      // IS agence (date à confirmer)
            } elseif ($d->lt($today)) {
                $groupes['late'][] = $e;
                $resume['nb_retard']++;
                $resume['somme_retard'] += $montant;
            } elseif ($d->lte($j7)) {
                $groupes['soon'][] = $e;
                $resume['somme_7j']  += $montant;
                $resume['somme_30j'] += $montant;
            } elseif ($d->lte($j30)) {
                $groupes['month'][] = $e;
                $resume['somme_30j'] += $montant;
            } else {
                $groupes['later'][] = $e;
            }
        }

        return view('admin.echeances-fiscales.index', compact('groupes', 'resume', 'today', 'echeances'));
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
