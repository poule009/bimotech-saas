<?php

namespace App\Http\Controllers;

use App\Models\Contrat;
use App\Notifications\RelanceImpayeNotification;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ImpayeController extends Controller
{
    use AuthorizesRequests;

    // ─────────────────────────────────────────────────────────────────────
    // LISTE DES IMPAYÉS
    // ─────────────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $this->authorize('isAdmin');

        $mois    = (int) $request->input('mois',  now()->month);
        $annee   = (int) $request->input('annee', now()->year);
        $periode = Carbon::create($annee, $mois, 1)->startOfMonth();

        /**
         * PERFORMANCE — select() sur les relations eager loadées :
         *
         * Avant : ->with('bien.proprietaire', 'locataire', 'paiements')
         * chargeait TOUTES les colonnes de chaque table.
         *
         * Après : select() sélectif sur chaque relation.
         * - bien : on affiche référence, adresse, ville → pas besoin de description, surface_m2...
         * - locataire : on affiche name, email, telephone → pas besoin de password, remember_token...
         * - paiements : filtré sur le mois + colonnes minimales pour la logique
         */
        $contrats = Contrat::where('statut', 'actif')
            ->select([
                'id', 'agency_id', 'bien_id', 'locataire_id',
                'statut', 'loyer_contractuel', 'date_debut',
            ])
            ->with([
                'bien:id,agency_id,reference,adresse,ville,statut',
                'bien.proprietaire:id,name,telephone',
                'locataire:id,name,email,telephone',
                'paiements' => fn($q) => $q
                    ->select([
                        'id', 'contrat_id', 'agency_id',
                        'periode', 'statut', 'montant_encaisse',
                        'mode_paiement', 'date_paiement', 'reference_paiement',
                    ])
                    ->whereYear('periode', $annee)
                    ->whereMonth('periode', $mois)
                    ->where('statut', '!=', 'annule'),
            ])
            ->get();

        $impayes = collect();
        $payes   = collect();

        foreach ($contrats as $contrat) {
            $paiementMois = $contrat->paiements
                ->filter(fn($p) => $p->statut !== 'annule')
                ->first(fn($p) => Carbon::parse($p->periode)->format('Y-m') === $periode->format('Y-m'));

            if ($paiementMois) {
                $payes->push([
                    'contrat'  => $contrat,
                    'paiement' => $paiementMois,
                ]);
            } else {
                $joursRetard = $periode->copy()->addDays(5)->diffInDays(now(), false);
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

        return view('impayes.index', compact(
            'impayes', 'payes', 'stats',
            'mois', 'annee', 'periode'
        ));
    }

    // ─────────────────────────────────────────────────────────────────────
    // ENVOI RELANCE
    // ─────────────────────────────────────────────────────────────────────

    public function relance(Request $request, Contrat $contrat): RedirectResponse
    {
        $this->authorize('isAdmin');

        $mois    = (int) $request->input('mois',  now()->month);
        $annee   = (int) $request->input('annee', now()->year);
        $periode = Carbon::create($annee, $mois, 1)->startOfMonth();

        // Charge uniquement ce qui est nécessaire à la notification
        $contrat->load([
            'bien:id,reference,adresse,ville',
            'locataire:id,name,email',
        ]);

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