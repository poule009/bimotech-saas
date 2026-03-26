<?php

namespace App\Http\Controllers;

use App\Models\Contrat;
use App\Models\Paiement;
use App\Notifications\RelanceImpayeNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ImpayeController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('isAdmin');

        // Mois à vérifier (défaut : mois en cours)
        $mois   = (int) $request->input('mois',  now()->month);
        $annee  = (int) $request->input('annee', now()->year);
        $periode = Carbon::create($annee, $mois, 1)->startOfMonth();

        // Tous les contrats actifs
        $contrats = Contrat::where('statut', 'actif')
            ->with('bien.proprietaire', 'locataire', 'paiements')
            ->get();

        // Sépare payés / impayés
        $impayes  = collect();
        $payes    = collect();

        foreach ($contrats as $contrat) {
            $paiementMois = $contrat->paiements
                ->filter(fn($p) => $p->statut !== 'annule')
                ->first(function ($p) use ($periode) {
                    return Carbon::parse($p->periode)->format('Y-m') === $periode->format('Y-m');
                });

            if ($paiementMois) {
                $payes->push([
                    'contrat'   => $contrat,
                    'paiement'  => $paiementMois,
                    'retard'    => false,
                ]);
            } else {
                $joursRetard = $periode->copy()->addDays(5)->diffInDays(now(), false);
                $impayes->push([
                    'contrat'     => $contrat,
                    'paiement'    => null,
                    'jours_retard'=> max(0, (int) $joursRetard),
                    'montant_du'  => $contrat->loyer_contractuel,
                ]);
            }
        }

        // Trie les impayés par retard décroissant
        $impayes = $impayes->sortByDesc('jours_retard');

        $stats = [
            'nb_impayes'       => $impayes->count(),
            'nb_payes'         => $payes->count(),
            'montant_du'       => $impayes->sum('montant_du'),
            'taux_recouvrement'=> $contrats->count() > 0
                ? round(($payes->count() / $contrats->count()) * 100, 1)
                : 0,
        ];

        // Historique des relances (stocké en notes de contrat)
        return view('impayes.index', compact(
            'impayes', 'payes', 'stats',
            'mois', 'annee', 'periode'
        ));
    }

    public function relance(Request $request, Contrat $contrat)
    {
        $this->authorize('isAdmin');

        $mois   = (int) $request->input('mois',  now()->month);
        $annee  = (int) $request->input('annee', now()->year);
        $periode = Carbon::create($annee, $mois, 1)->startOfMonth();

        $contrat->load('bien', 'locataire');

        try {
            $contrat->locataire->notify(
                new RelanceImpayeNotification($contrat, $periode)
            );

            // Note dans les observations du contrat
            $note = "\n[Relance envoyée le " . now()->format('d/m/Y à H:i') .
                    " pour " . $periode->translatedFormat('F Y') . "]";

            $contrat->update([
                'observations' => $contrat->observations . $note
            ]);

            return back()->with('success',
                "Relance envoyée à {$contrat->locataire->name} ✓"
            );

        } catch (\Throwable $e) {
            Log::error('Erreur relance impayé', ['error' => $e->getMessage()]);
            return back()->withErrors([
                'general' => 'Erreur lors de l\'envoi de la relance.'
            ]);
        }
    }
}