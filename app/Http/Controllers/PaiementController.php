<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaiementRequest;
use App\Models\Contrat;
use App\Models\Paiement;
use App\Notifications\PaiementProprietaireNotification;
use App\Notifications\QuittanceLocataireNotification;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PaiementController extends Controller
{
    // ── Liste des paiements ───────────────────────────────────────────────────

    public function index(Request $request)
    {
        $this->authorize('viewAny', Paiement::class);

        $paiements = Paiement::with('contrat.bien', 'contrat.locataire')
            ->orderByDesc('date_paiement')
            ->paginate(15);

        return view('paiements.index', compact('paiements'));
    }

    // ── Formulaire de saisie ──────────────────────────────────────────────────

    public function create(Request $request)
    {
        $this->authorize('isAdmin');

        $contrats = Contrat::with('bien', 'locataire')
            ->where('statut', 'actif')
            ->orderByDesc('created_at')
            ->get();

        $contratPreselectionne = $request->has('contrat_id')
            ? Contrat::find($request->contrat_id)
            : null;

        return view('paiements.create', compact('contrats', 'contratPreselectionne'));
    }

    // ── Enregistrement d'un paiement ──────────────────────────────────────────

    public function store(StorePaiementRequest $request)
    {
        // BUG 5 FIX : authorize() manquant — défense en profondeur.
        // La route est sous isAdmin mais le contrôleur doit aussi vérifier.
        $this->authorize('create', Paiement::class);

        $contrat = Contrat::with('bien')->findOrFail($request->contrat_id);

        // Garde 1 : Contrat doit être actif
        if ($contrat->statut !== 'actif') {
            return back()->withInput()->withErrors([
                'contrat_id' => 'Ce contrat n\'est plus actif.'
            ]);
        }

        // Garde 2 : Anti-doublon
        $periodeDate = Carbon::createFromFormat('Y-m', $request->periode)->startOfMonth();

        $doublonExiste = Paiement::where('contrat_id', $contrat->id)
            ->whereYear('periode', $periodeDate->year)
            ->whereMonth('periode', $periodeDate->month)
            ->where('statut', '!=', 'annule')
            ->exists();

        if ($doublonExiste) {
            return back()->withInput()->withErrors([
                'periode' => "Un paiement existe déjà pour {$periodeDate->translatedFormat('F Y')} sur ce contrat."
            ]);
        }

        // Calcul des montants avec TVA
        $montant        = (float) $request->montant_encaisse;
        $tauxCommission = (float) $contrat->bien->taux_commission;
        $calcul         = Paiement::calculerMontants($montant, $tauxCommission);

        // Gestion caution
        $caution         = (float) ($request->caution_percue ?? 0);
        $estPremierPaiem = (bool)  ($request->est_premier_paiement ?? false);

        if ($caution > 0 && ! $estPremierPaiem) {
            return back()->withInput()->withErrors([
                'caution_percue' => 'Cochez "Premier paiement" pour enregistrer une caution.'
            ]);
        }

        // Génération de la référence
        $reference = 'QUITT-' . $contrat->id . '-' . $periodeDate->format('Ym') . '-' . now()->format('His');

        try {
            $paiement = DB::transaction(function () use (
                $request, $contrat, $periodeDate, $montant,
                $tauxCommission, $calcul, $caution, $estPremierPaiem, $reference
            ) {
                $paiement = Paiement::create([
                    'contrat_id'               => $contrat->id,
                    'periode'                  => $periodeDate->toDateString(),
                    'montant_encaisse'         => $montant,
                    'mode_paiement'            => $request->mode_paiement,
                    'taux_commission_applique' => $tauxCommission,
                    'commission_agence'        => $calcul['commission_ht'],
                    'tva_commission'           => $calcul['tva'],
                    'commission_ttc'           => $calcul['commission_ttc'],
                    'net_proprietaire'         => $calcul['net_proprietaire'],
                    'caution_percue'           => $caution,
                    'est_premier_paiement'     => $estPremierPaiem,
                    'date_paiement'            => $request->date_paiement,
                    'reference_paiement'       => $reference,
                    'statut'                   => 'valide',
                    'notes'                    => $request->notes,
                ]);

                // Chargement des relations pour génération PDF + emails
                $contrat->load('bien.proprietaire', 'locataire');
                $paiement->load('contrat.bien.proprietaire', 'contrat.locataire');

                // Générer la quittance PDF UNE FOIS et la stocker sur le disk private
                $montantEnLettres = \App\Services\NombreEnLettres::convertir($paiement->montant_encaisse);
                $netEnLettres     = \App\Services\NombreEnLettres::convertir($paiement->net_proprietaire);

                $data = [
                    'paiement'         => $paiement,
                    'contrat'          => $paiement->contrat,
                    'bien'             => $paiement->contrat->bien,
                    'proprietaire'     => $paiement->contrat->bien->proprietaire,
                    'locataire'        => $paiement->contrat->locataire,
                    'montantEnLettres' => $montantEnLettres,
                    'netEnLettres'     => $netEnLettres,
                    'agence' => [
                        'nom'       => 'BIMO-Tech Immobilier',
                        'adresse'   => 'Dakar, Sénégal',
                        'telephone' => '+221 33 000 00 00',
                        'email'     => 'contact@bimotech.sn',
                        'ninea'     => 'NINEA : 00000000000',
                    ],
                ];

                $pdf = Pdf::loadView('paiements.pdf.quittance', $data)
                    ->setPaper('a4', 'portrait')
                    ->setOption('defaultFont', 'DejaVu Sans')
                    ->setOption('dpi', 150);

                $receiptPath = sprintf(
                    'receipts/%s/%s/%s/quittance-%s.pdf',
                    $paiement->agency_id ?? 'global',
                    now()->format('Y'),
                    now()->format('m'),
                    $paiement->reference_paiement
                );

                Storage::disk('private')->put($receiptPath, $pdf->output());

                $paiement->update([
                    'receipt_path' => $receiptPath,
                ]);

                // Email au locataire avec quittance PDF en pièce jointe
                try {
                    $contrat->locataire->notify(
                        new QuittanceLocataireNotification($paiement)
                    );
                } catch (\Throwable $e) {
                    Log::warning('Email locataire non envoyé', [
                        'error' => $e->getMessage(),
                        'paiement_id' => $paiement->id ?? null,
                        'contrat_id' => $contrat->id ?? null,
                        'agency_id' => \Illuminate\Support\Facades\Auth::user()?->agency_id,
                        'target_user_id' => $contrat->locataire->id ?? null,
                    ]);
                }

                // Email au propriétaire avec récapitulatif
                try {
                    $contrat->bien->proprietaire->notify(
                        new PaiementProprietaireNotification($paiement)
                    );
                } catch (\Throwable $e) {
                    Log::warning('Email propriétaire non envoyé', [
                        'error' => $e->getMessage(),
                        'paiement_id' => $paiement->id ?? null,
                        'contrat_id' => $contrat->id ?? null,
                        'agency_id' => \Illuminate\Support\Facades\Auth::user()?->agency_id,
                        'target_user_id' => $contrat->bien->proprietaire->id ?? null,
                    ]);
                }

                return $paiement;
            });

            return redirect()
                ->route('admin.paiements.show', $paiement)
                ->with('success', "Paiement enregistré ✓  Réf : {$reference}");

        } catch (\Throwable $e) {
            Log::error('Erreur création paiement', ['error' => $e->getMessage()]);
            return back()->withInput()->withErrors([
                'general' => 'Une erreur est survenue. Veuillez réessayer.'
            ]);
        }
    }

    // ── Détail d'un paiement ──────────────────────────────────────────────────

    public function show(Paiement $paiement)
    {
        $this->authorize('view', $paiement);

        $paiement->load('contrat.bien.proprietaire', 'contrat.locataire');

        return view('paiements.show', compact('paiement'));
    }

    // ── Génération de la quittance PDF ────────────────────────────────────────

    public function downloadPDF(Paiement $paiement)
    {
        $this->authorize('downloadPdf', $paiement);

        $fileName = 'quittance-' . $paiement->reference_paiement . '.pdf';

        if (! empty($paiement->receipt_path) && Storage::disk('private')->exists($paiement->receipt_path)) {
            return response()->download(
                Storage::disk('private')->path($paiement->receipt_path),
                $fileName
            );
        }

        // Fallback de robustesse: si le fichier stocké est absent, régénérer puis persister
        $paiement->load('contrat.bien.proprietaire', 'contrat.locataire');

        $montantEnLettres = \App\Services\NombreEnLettres::convertir($paiement->montant_encaisse);
        $netEnLettres     = \App\Services\NombreEnLettres::convertir($paiement->net_proprietaire);

        $data = [
            'paiement'         => $paiement,
            'contrat'          => $paiement->contrat,
            'bien'             => $paiement->contrat->bien,
            'proprietaire'     => $paiement->contrat->bien->proprietaire,
            'locataire'        => $paiement->contrat->locataire,
            'montantEnLettres' => $montantEnLettres,
            'netEnLettres'     => $netEnLettres,
            'agence' => [
                'nom'       => 'BIMO-Tech Immobilier',
                'adresse'   => 'Dakar, Sénégal',
                'telephone' => '+221 33 000 00 00',
                'email'     => 'contact@bimotech.sn',
                'ninea'     => 'NINEA : 00000000000',
            ],
        ];

        $pdf = Pdf::loadView('paiements.pdf.quittance', $data)
            ->setPaper('a4', 'portrait')
            ->setOption('defaultFont', 'DejaVu Sans')
            ->setOption('dpi', 150);

        $receiptPath = sprintf(
            'receipts/%s/%s/%s/quittance-%s.pdf',
            $paiement->agency_id ?? 'global',
            now()->format('Y'),
            now()->format('m'),
            $paiement->reference_paiement
        );

        Storage::disk('private')->put($receiptPath, $pdf->output());

        $paiement->update([
            'receipt_path' => $receiptPath,
        ]);

        return response()->download(
            Storage::disk('private')->path($receiptPath),
            $fileName
        );
    }

    // ── Annulation d'un paiement ──────────────────────────────────────────────

    public function annuler(Request $request, Paiement $paiement)
    {
        $this->authorize('update', $paiement);

        if ($paiement->statut === 'annule') {
            return back()->withErrors(['general' => 'Ce paiement est déjà annulé.']);
        }

        $paiement->update([
            'statut' => 'annule',
            'notes'  => $paiement->notes . "\n[Annulé le " . now()->format('d/m/Y') . " — " . ($request->motif ?? 'Sans motif') . "]",
        ]);

        return back()->with('success', 'Paiement annulé.');
    }

    // ── TÂCHE 2 : Saisie prédictive — Dernier mois payé + suggestion ─────────
    // Endpoint AJAX : GET admin/paiements/dernier-periode/{contrat}
    // Retourne JSON : { periode_suggeree, label, dernier_paye, loyer_contractuel }

    public function dernierePeriode(Contrat $contrat)
    {
        $this->authorize('isAdmin');

        // Dernier paiement valide (non annulé) pour ce contrat, trié par période
        $dernierPaiement = Paiement::where('contrat_id', $contrat->id)
            ->where('statut', '!=', 'annule')
            ->orderByDesc('periode')
            ->first();

        if ($dernierPaiement) {
            // On propose le mois suivant le dernier payé
            $prochaineMois = Carbon::parse($dernierPaiement->periode)->addMonth();
            $dernierLabel  = Carbon::parse($dernierPaiement->periode)
                ->locale('fr')
                ->translatedFormat('F Y');
        } else {
            // Aucun paiement : on propose le mois de début du contrat
            $prochaineMois = Carbon::parse($contrat->date_debut)->startOfMonth();
            $dernierLabel  = null;
        }

        return response()->json([
            // Format attendu par l'input[type=month] : "YYYY-MM"
            'periode_suggeree'  => $prochaineMois->format('Y-m'),
            // Label lisible pour l'affichage dans l'interface
            'label'             => $prochaineMois->locale('fr')->translatedFormat('F Y'),
            // Dernier mois payé (null si aucun paiement)
            'dernier_paye'      => $dernierLabel,
            // Loyer contractuel pour pré-remplir le montant
            'loyer_contractuel' => $contrat->loyer_contractuel,
        ]);
    }

    // ── Paiements du locataire connecté ───────────────────────────────────────

    public function mesPaiements()
    {
        // BUG 6 FIX : authorize() manquant — s'assure que seul un locataire
        // peut accéder à cette méthode, en complément du middleware de route.
        $this->authorize('isLocataire');

        $user = Auth::user();

        $contratIds = Contrat::where('locataire_id', $user->id)->pluck('id');

        $paiements = Paiement::whereIn('contrat_id', $contratIds)
            ->where('statut', 'valide')
            ->with('contrat.bien')
            ->orderByDesc('periode')
            ->paginate(12);

        return view('paiements.index', compact('paiements'));
    }
}