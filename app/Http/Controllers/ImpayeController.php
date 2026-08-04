<?php

namespace App\Http\Controllers;

use App\Exports\ImpayesExport;
use App\Models\Contrat;
use App\Models\Paiement;
use App\Notifications\RelanceImpayeNotification;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class ImpayeController extends Controller
{
    use AuthorizesRequests;

    // ─────────────────────────────────────────────────────────────────────
    // LISTE DES IMPAYÉS
    // ─────────────────────────────────────────────────────────────────────

    public function index(Request $request): RedirectResponse
    {
        $this->authorize('isStaff');

        // L'écran de suivi des retards/impayés vit désormais dans PaiementController
        // (vue paiements.index, avec buckets de retard et filtre statut correct).
        // Cet ancien index n'avait plus de vue dédiée : on redirige pour éviter une
        // page cassée et surtout un double comptage (l'ancienne logique classait un
        // loyer 'unpaid' comme « payé »). Export Excel conservé ci-dessous.
        return redirect()->route('admin.paiements.index');
    }

    // ─────────────────────────────────────────────────────────────────────
    // EXPORT EXCEL
    // ─────────────────────────────────────────────────────────────────────

    public function export(Request $request)
    {
        $this->authorize('isStaff');

        $mois    = max(1, min(12,   (int) $request->input('mois',  now()->month)));
        $annee   = max(2000, min(2100, (int) $request->input('annee', now()->year)));
        $periode = Carbon::create($annee, $mois, 1)->startOfMonth();

        if ($periode->isAfter(now()->endOfMonth())) {
            $periode = now()->startOfMonth();
            $mois    = $periode->month;
            $annee   = $periode->year;
        }

        $contrats = Contrat::where('statut', 'actif')
            ->select(['id', 'agency_id', 'bien_id', 'locataire_id', 'statut', 'loyer_contractuel', 'date_debut'])
            ->with([
                'bien:id,agency_id,reference,adresse,ville,statut',
                'bien.proprietaire:id,name,telephone',
                'locataire:id,name,email,telephone',
                // Seul un paiement VALIDÉ atteste que le loyer est payé. Une ligne
                // générée non encaissée ('unpaid'/'en_attente') reste un impayé.
                'paiements' => fn($q) => $q
                    ->select(['id', 'contrat_id', 'agency_id', 'periode', 'statut', 'montant_encaisse', 'mode_paiement', 'date_paiement'])
                    ->whereYear('periode', $annee)
                    ->whereMonth('periode', $mois)
                    ->where('statut', 'valide'),
            ])
            ->get();

        $impayes = collect();
        $payes   = collect();

        foreach ($contrats as $contrat) {
            $paiementMois = $contrat->paiements->first();
            if ($paiementMois) {
                $payes->push(['contrat' => $contrat, 'paiement' => $paiementMois]);
            } else {
                $joursRetard = Paiement::joursRetardPour($periode);
                $impayes->push([
                    'contrat'      => $contrat,
                    'paiement'     => null,
                    'jours_retard' => max(0, (int) $joursRetard),
                    'montant_du'   => $contrat->loyer_contractuel,
                ]);
            }
        }

        $impayes = $impayes->sortByDesc('jours_retard');

        $stats = [
            'nb_impayes'        => $impayes->count(),
            'nb_payes'          => $payes->count(),
            'montant_du'        => $impayes->sum('montant_du'),
            'taux_recouvrement' => $contrats->count() > 0
                ? round(($payes->count() / $contrats->count()) * 100, 1)
                : 0,
        ];

        $agencyName = auth()->user()->agency?->name ?? config('app.name', 'Bimmo');
        $filename   = 'impayes_' . $periode->format('Y-m') . '_' . now()->format('Ymd') . '.xlsx';

        return Excel::download(
            new ImpayesExport($impayes, $payes, $stats, $periode, $agencyName),
            $filename,
            \Maatwebsite\Excel\Excel::XLSX
        );
    }

    // ─────────────────────────────────────────────────────────────────────
    // ENVOI RELANCE
    // ─────────────────────────────────────────────────────────────────────

    public function relance(Request $request, Contrat $contrat): RedirectResponse
    {
        $this->authorize('isStaff');

        $mois    = max(1, min(12,   (int) $request->input('mois',  now()->month)));
        $annee   = max(2000, min(2100, (int) $request->input('annee', now()->year)));
        $periode = Carbon::create($annee, $mois, 1)->startOfMonth();

        // Charge uniquement ce qui est nécessaire à la notification
        $contrat->load([
            'bien:id,reference,adresse,ville',
            'locataire:id,name,email',
        ]);

        if (! $contrat->locataire || ! $contrat->locataire->email) {
            return back()->withErrors([
                'general' => 'Impossible d\'envoyer la relance : locataire sans adresse email.',
            ]);
        }

        try {
            $contrat->locataire->notify(
                new RelanceImpayeNotification($contrat, $periode)
            );

            $note = sprintf(
                "\n[Relance envoyée le %s pour %s]",
                now()->format('d/m/Y à H:i'),
                $periode->translatedFormat('F Y')
            );

            $contrat->update([
                'observations' => ($contrat->observations ?? '') . $note,
            ]);

            return back()->with('success', "Relance envoyée à {$contrat->locataire->name} ✓");

        } catch (\Throwable $e) {
            Log::error('Erreur relance impayé', [
                'contrat_id' => $contrat->id,
                'error'      => $e->getMessage(),
            ]);

            return back()->withErrors([
                'general' => 'Erreur lors de l\'envoi de la relance. Vérifiez la configuration email.',
            ]);
        }
    }
}