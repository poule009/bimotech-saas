<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
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

    public function index(): View
    {
        $today          = Carbon::now()->timezone('Africa/Dakar')->startOfDay();
        /** @var \App\Models\User $authUser */
        $authUser       = auth()->user();
        $agency         = $authUser->agency;
        $formeJuridique = $agency->forme_juridique ?? 'sarl';

        $echeances = self::buildEcheances($formeJuridique, $today);

        // Résolution des noms de route en URLs (séparée de buildEcheances pour les tests unitaires)
        foreach ($echeances as &$e) {
            $e['lien'] = $e['lien_route'] ? route($e['lien_route']) : null;
        }
        unset($e);

        $echeancesUrgentes = array_values(array_filter(
            $echeances,
            fn($e) => in_array($e['statut'], ['urgent', 'bientot'])
        ));

        // ── Baux à enregistrer : contrats non enregistrés dont la date limite
        // approche (≤ 7 j) ou est dépassée (Droits d'enregistrement DGID, §5.2).
        $bauxAEnregistrer = \App\Models\Contrat::where('agency_id', $authUser->agency_id)
            ->where('statut', 'actif')
            ->where('enregistrement_exonere', false)
            ->where('droit_enreg_effectue', false)
            ->whereNotNull('droit_enreg_date_limite')
            ->whereDate('droit_enreg_date_limite', '<=', $today->copy()->addDays(7)->toDateString())
            ->with(['bien:id,reference', 'locataire:id,name'])
            ->orderBy('droit_enreg_date_limite')
            ->get()
            ->map(fn ($c) => [
                'contrat'     => $c,
                'nom'         => $c->locataire->name ?? ($c->bien->reference ?? 'Contrat #' . $c->id),
                'date_limite' => $c->droit_enreg_date_limite,
                'en_retard'   => $c->droit_enreg_date_limite->isPast(),
                'total'       => $c->droit_enreg_total,
            ]);

        return view('admin.echeances-fiscales.index', compact(
            'echeances', 'echeancesUrgentes', 'today', 'bauxAEnregistrer'
        ));
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
            ['mois' => 4, 'jour' => 30, 'label' => 'IRPP / CGF',               'type' => 'Déclaration', 'lien_route' => 'admin.bilans-fiscaux.index',     'lien_label' => 'Bilans fisc.',    'visible' => true],
            ['mois' => 6, 'jour' => 15, 'label' => 'IS — solde + IMF',         'type' => 'Paiement',    'lien_route' => null,                            'lien_label' => 'Info',            'visible' => $isSociete],
            ['mois' => 9, 'jour' => 30, 'label' => 'CFPB',                     'type' => 'Émission',    'lien_route' => null,                            'lien_label' => 'Info admin',      'visible' => true],
            ['mois' => 9, 'jour' => 30, 'label' => 'TEOM',                     'type' => 'Émission',    'lien_route' => null,                            'lien_label' => 'Info admin',      'visible' => true],
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
