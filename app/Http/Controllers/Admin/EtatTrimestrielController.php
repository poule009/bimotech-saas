<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EtatTrimestrielDownload;
use App\Models\Paiement;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class EtatTrimestrielController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('check.feature:fiscalite'),
        ];
    }

    // ── Helpers statiques (publics pour les tests) ────────────────────────────

    public static function trimestreFromMois(int $mois): int
    {
        return (int) ceil($mois / 3);
    }

    public static function dateLimiteTrimestre(int $trimestre, int $annee): Carbon
    {
        return match ($trimestre) {
            1 => Carbon::create($annee, 4,  15)->startOfDay(),
            2 => Carbon::create($annee, 7,  15)->startOfDay(),
            3 => Carbon::create($annee, 10, 15)->startOfDay(),
            4 => Carbon::create($annee + 1, 1, 15)->startOfDay(),
        };
    }

    private static function periodeDebutTrimestre(int $trimestre, int $annee): Carbon
    {
        return Carbon::create($annee, ($trimestre - 1) * 3 + 1, 1)->startOfDay();
    }

    private static function periodeFinTrimestre(int $trimestre, int $annee): Carbon
    {
        return Carbon::create($annee, $trimestre * 3)->endOfMonth()->endOfDay();
    }

    private static function statutTrimestre(int $trimestre, int $annee, bool $telecharge): string
    {
        if ($telecharge) {
            return 'telecharge';
        }

        $now    = now()->timezone('Africa/Dakar');
        $debut  = self::periodeDebutTrimestre($trimestre, $annee);
        $fin    = self::periodeFinTrimestre($trimestre, $annee);
        $limite = self::dateLimiteTrimestre($trimestre, $annee);

        if ($now->lt($debut)) {
            return 'avenir';
        }

        if ($now->lte($fin)) {
            return 'en_cours';
        }

        // Trimestre terminé : en retard si la date limite est dépassée
        if ($now->gt($limite)) {
            return 'en_retard';
        }

        return 'a_deposer';
    }

    private static function labelMoisTrimestre(int $trimestre): string
    {
        return match ($trimestre) {
            1 => 'Jan — Mar',
            2 => 'Avr — Juin',
            3 => 'Juil — Sep',
            4 => 'Oct — Déc',
        };
    }

    // ── Requête de base : paiements BRS du trimestre, bailleurs personnes physiques ──

    private function paiementsQueryBuilder(int $agencyId, int $trimestre, int $annee): Builder
    {
        $debut = self::periodeDebutTrimestre($trimestre, $annee);
        $fin   = self::periodeFinTrimestre($trimestre, $annee);

        return Paiement::where('agency_id', $agencyId)
            ->where('brs_amount', '>', 0)
            ->whereBetween('periode', [$debut->format('Y-m-d'), $fin->format('Y-m-d')])
            ->whereHas('contrat.bien.proprietaire', function (Builder $q) {
                // Exclure les bailleurs personnes morales (IS) — Art. 200 §5 CGI SN.
                // B1 : on utilise est_personne_morale_is (source unique), aligné sur le
                // moteur et l'UI. L'ancien champ est_personne_morale (mort) est supprimé.
                $q->where(function (Builder $q) {
                    $q->whereDoesntHave('proprietaire')
                      ->orWhereHas('proprietaire', function (Builder $q) {
                          $q->where(function (Builder $q) {
                              $q->where('est_personne_morale_is', false)
                                ->orWhereNull('est_personne_morale_is');
                          });
                      });
                });
            });
    }

    // ── Actions ────────────────────────────────────────────────────────────────

    public function index(Request $request): View
    {
        $agency            = auth()->user()->agency;
        $annee             = (int) $request->query('annee', now()->year);
        $anneeCreation     = $agency->created_at?->year ?? now()->year;
        $anneesDisponibles = range(now()->year, min($anneeCreation, now()->year));

        $trimestres = [];

        for ($t = 1; $t <= 4; $t++) {
            $debut  = self::periodeDebutTrimestre($t, $annee);
            $fin    = self::periodeFinTrimestre($t, $annee);
            $limite = self::dateLimiteTrimestre($t, $annee);

            $paiements   = $this->paiementsQueryBuilder($agency->id, $t, $annee)
                               ->with('contrat.bien')
                               ->get();

            $totalBrs    = $paiements->sum('brs_amount');
            $nbBailleurs = $paiements
                ->map(fn ($p) => $p->contrat?->bien?->proprietaire_id)
                ->filter()
                ->unique()
                ->count();

            $download = EtatTrimestrielDownload::where('agency_id', $agency->id)
                ->where('trimestre', $t)
                ->where('annee', $annee)
                ->latest('downloaded_at')
                ->first();

            $trimestres[] = [
                'numero'       => $t,
                'label'        => "T{$t} {$annee}",
                'mois_label'   => self::labelMoisTrimestre($t),
                'date_debut'   => $debut,
                'date_fin'     => $fin,
                'date_limite'  => $limite,
                'total_brs'    => $totalBrs,
                'nb_bailleurs' => $nbBailleurs,
                'statut'       => self::statutTrimestre($t, $annee, (bool) $download),
                'download'     => $download,
            ];
        }

        return view('admin.etats-trimestriels.index', compact('trimestres', 'annee', 'anneesDisponibles'));
    }

    public function show(int $annee, int $trimestre): View
    {
        $agency     = auth()->user()->agency;
        $paiements  = $this->paiementsQueryBuilder($agency->id, $trimestre, $annee)
                         ->with('contrat.bien.proprietaire.proprietaire')
                         ->get();

        $lignes     = self::regroupParBailleur($paiements, $trimestre, $annee);
        $totalBrs   = $lignes->sum('brs_retenu');
        $totalNet   = $lignes->sum('loyers_verses');
        $dateLimite = self::dateLimiteTrimestre($trimestre, $annee);

        return view('admin.etats-trimestriels.show', compact(
            'lignes', 'totalBrs', 'totalNet', 'trimestre', 'annee', 'dateLimite', 'agency'
        ));
    }

    public function exportPdf(int $annee, int $trimestre)
    {
        $agency     = auth()->user()->agency;
        $paiements  = $this->paiementsQueryBuilder($agency->id, $trimestre, $annee)
                         ->with('contrat.bien.proprietaire.proprietaire')
                         ->get();

        $lignes     = self::regroupParBailleur($paiements, $trimestre, $annee);
        $totalBrs   = $lignes->sum('brs_retenu');
        $totalNet   = $lignes->sum('loyers_verses');
        $dateLimite = self::dateLimiteTrimestre($trimestre, $annee);

        $pdf = Pdf::loadView('admin.etats-trimestriels.pdf.etat', compact(
            'lignes', 'totalBrs', 'totalNet', 'trimestre', 'annee', 'dateLimite', 'agency'
        ))->setPaper('a4');

        $this->enregistrerTelechargement($agency->id, $trimestre, $annee, 'pdf');

        $slug     = str($agency->name)->slug();
        $filename = "etat-brs-T{$trimestre}-{$annee}-{$slug}.pdf";

        return $pdf->download($filename);
    }

    public function exportCsv(int $annee, int $trimestre)
    {
        $agency     = auth()->user()->agency;
        $paiements  = $this->paiementsQueryBuilder($agency->id, $trimestre, $annee)
                         ->with('contrat.bien.proprietaire.proprietaire')
                         ->get();

        $lignes = self::regroupParBailleur($paiements, $trimestre, $annee);
        $this->enregistrerTelechargement($agency->id, $trimestre, $annee, 'csv');

        $slug     = str($agency->name)->slug();
        $filename = "etat-brs-T{$trimestre}-{$annee}-{$slug}.csv";

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($lignes) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF"); // BOM UTF-8 pour Excel Windows
            fputcsv($handle, ['Nom', 'Prénom', 'NINEA', 'Adresse', 'Loyers versés (FCFA)', 'BRS retenu (FCFA)', 'Période'], ';');

            foreach ($lignes as $ligne) {
                fputcsv($handle, [
                    $ligne['nom'],
                    $ligne['prenom'],
                    $ligne['ninea'] ?? '',
                    $ligne['adresse'] ?? '',
                    number_format((float) $ligne['loyers_verses'], 0, ',', ' '),
                    number_format((float) $ligne['brs_retenu'], 0, ',', ' '),
                    $ligne['periode_label'],
                ], ';');
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    // ── Regroupement par bailleur (public static pour les tests) ─────────────

    public static function regroupParBailleur(Collection $paiements, int $trimestre, int $annee): Collection
    {
        $debut        = self::periodeDebutTrimestre($trimestre, $annee);
        $fin          = self::periodeFinTrimestre($trimestre, $annee);
        $periodeLabel = $debut->translatedFormat('F Y') . ' — ' . $fin->translatedFormat('F Y');

        return $paiements
            ->groupBy(fn ($p) => $p->contrat?->bien?->proprietaire_id)
            ->filter(fn ($_, $userId) => $userId !== null)
            ->map(function (Collection $group) use ($periodeLabel) {
                $user    = $group->first()->contrat?->bien?->proprietaire;
                $profile = $user?->proprietaire;

                // Séparation prénom / nom : dernier mot = nom, reste = prénom (convention sénégalaise)
                $name      = $user?->name ?? '';
                $lastSpace = strrpos($name, ' ');
                $prenom    = $lastSpace !== false ? substr($name, 0, $lastSpace) : '';
                $nom       = $lastSpace !== false ? substr($name, $lastSpace + 1) : $name;

                return [
                    'nom'               => $nom ?: $name,
                    'prenom'            => $prenom,
                    'nom_complet'       => $name ?: '—',
                    'email'             => $user?->email ?? '',
                    'telephone'         => $user?->telephone ?? '',
                    'ninea'             => $profile?->ninea,
                    'cni'               => $profile?->cni,
                    'date_naissance'    => $profile?->date_naissance,
                    'adresse'           => $profile?->adresse_domicile ?? $user?->adresse ?? '',
                    'loyers_verses'     => $group->sum(fn ($p) => (float) ($p->net_a_verser_proprietaire ?? $p->net_proprietaire ?? 0)),
                    'brs_retenu'        => $group->sum(fn ($p) => (float) ($p->brs_amount ?? 0)),
                    'nb_paiements'      => $group->count(),
                    'periode_label'     => $periodeLabel,
                    'has_warning_ninea' => empty($profile?->ninea),
                ];
            })
            ->values();
    }

    // ── Tracking téléchargements ──────────────────────────────────────────────

    private function enregistrerTelechargement(int $agencyId, int $trimestre, int $annee, string $type): void
    {
        EtatTrimestrielDownload::create([
            'agency_id'     => $agencyId,
            'trimestre'     => $trimestre,
            'annee'         => $annee,
            'type'          => $type,
            'downloaded_at' => now(),
            'downloaded_by' => auth()->id(),
        ]);
    }
}
