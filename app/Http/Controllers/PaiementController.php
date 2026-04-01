<?php

namespace App\Http\Controllers;

use App\Models\Contrat;
use App\Models\Paiement;
use App\Notifications\PaiementProprietaireNotification;
use App\Notifications\QuittanceLocataireNotification;
use App\Services\NombreEnLettres;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PaiementController extends Controller
{
    use AuthorizesRequests;

    // ─────────────────────────────────────────────────────────────────────
    // LISTE
    // ─────────────────────────────────────────────────────────────────────

    public function index()
    {
        $this->authorize('viewAny', Paiement::class);

        $paiements = Paiement::with('contrat.bien', 'contrat.locataire')
            ->orderByDesc('date_paiement')
            ->paginate(20);

        return view('admin.paiements.index', compact('paiements'));
    }

    // ─────────────────────────────────────────────────────────────────────
    // FORMULAIRE CRÉATION
    // ─────────────────────────────────────────────────────────────────────

    public function create(Request $request)
    {
        $this->authorize('create', Paiement::class);

        $contrats = Contrat::where('statut', 'actif')
            ->with('bien', 'locataire')
            ->orderBy('created_at', 'desc')
            ->get();

        $contratPreselectionne = $request->has('contrat_id')
            ? Contrat::with('bien', 'locataire')->find($request->contrat_id)
            : null;

        return view('paiements.create', compact('contrats', 'contratPreselectionne'));
    }

    // ─────────────────────────────────────────────────────────────────────
    // ENREGISTREMENT
    // ─────────────────────────────────────────────────────────────────────

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Paiement::class);

        $request->validate([
            'contrat_id'           => ['required', 'exists:contrats,id'],
            'periode'              => ['required', 'date_format:Y-m'],
            'loyer_nu'             => ['required', 'numeric', 'min:1'],
            'charges_amount'       => ['nullable', 'numeric', 'min:0'],
            'tom_amount'           => ['nullable', 'numeric', 'min:0'],
            'reference_bail'       => ['nullable', 'string', 'max:60'],
            'mode_paiement'        => ['required', 'in:' . implode(',', array_keys(Paiement::MODES_PAIEMENT))],
            'date_paiement'        => ['required', 'date'],
            'caution_percue'       => ['nullable', 'numeric', 'min:0'],
            'est_premier_paiement' => ['boolean'],
            'notes'                => ['nullable', 'string', 'max:500'],
        ]);

        $contrat = Contrat::with('bien.proprietaire', 'locataire')
            ->findOrFail($request->contrat_id);

        if ($contrat->agency_id !== Auth::user()->agency_id) {
            abort(403, 'Ce contrat n\'appartient pas à votre agence.');
        }

        $periodeDate = Carbon::createFromFormat('Y-m', $request->periode)->startOfMonth();

        $doublon = Paiement::where('contrat_id', $contrat->id)
            ->where('statut', '!=', 'annule')
            ->whereYear('periode', $periodeDate->year)
            ->whereMonth('periode', $periodeDate->month)
            ->exists();

        if ($doublon) {
            return back()->withInput()->withErrors([
                'periode' => 'Un paiement valide existe déjà pour ce contrat sur cette période.',
            ]);
        }

        $caution         = (float) ($request->caution_percue ?? 0);
        $estPremierPaiem = (bool)  ($request->est_premier_paiement ?? false);

        if ($caution > 0 && ! $estPremierPaiem) {
            return back()->withInput()->withErrors([
                'caution_percue' => 'Cochez "Premier paiement" pour enregistrer une caution.',
            ]);
        }

        $loyerNu        = (float) $request->loyer_nu;
        $chargesAmount  = (float) ($request->charges_amount ?? 0);
        $tomAmount      = (float) ($request->tom_amount ?? 0);
        $tauxCommission = (float) $contrat->bien->taux_commission;

        $calcul = Paiement::calculerMontants(
            loyerNu:        $loyerNu,
            tauxCommission: $tauxCommission,
            chargesAmount:  $chargesAmount,
            tomAmount:      $tomAmount,
        );

        $referenceQuittance = sprintf(
            'QUITT-%s-%s-%s',
            $contrat->id,
            $periodeDate->format('Ym'),
            now()->format('His')
        );

        $referenceBail = ! empty($request->reference_bail)
            ? trim($request->reference_bail)
            : $contrat->reference_bail_affichee;

        try {
            $paiement = DB::transaction(function () use (
                $request, $contrat, $periodeDate,
                $calcul, $caution, $estPremierPaiem,
                $referenceQuittance, $referenceBail, $tauxCommission
            ) {
                $paiement = Paiement::create([
                    'contrat_id'               => $contrat->id,
                    'periode'                  => $periodeDate->toDateString(),
                    'loyer_nu'                 => $calcul['loyer_nu'],
                    'charges_amount'           => $calcul['charges_amount'],
                    'tom_amount'               => $calcul['tom_amount'],
                    'montant_encaisse'         => $calcul['montant_encaisse'],
                    'mode_paiement'            => $request->mode_paiement,
                    'taux_commission_applique' => $tauxCommission,
                    'commission_agence'        => $calcul['commission_ht'],
                    'tva_commission'           => $calcul['tva'],
                    'commission_ttc'           => $calcul['commission_ttc'],
                    'net_proprietaire'         => $calcul['net_proprietaire'],
                    'caution_percue'           => $caution,
                    'est_premier_paiement'     => $estPremierPaiem,
                    'date_paiement'            => $request->date_paiement,
                    'reference_paiement'       => $referenceQuittance,
                    'reference_bail'           => $referenceBail,
                    'statut'                   => 'valide',
                    'notes'                    => $request->notes,
                ]);

                $contrat->load('bien.proprietaire', 'locataire');
                $paiement->load('contrat.bien.proprietaire', 'contrat.locataire');

                $agency  = $paiement->agency ?? Auth::user()->agency;
                $pdfData = self::buildPdfData($paiement, $agency);

                $pdf = Pdf::loadView('paiements.pdf.quittance', $pdfData)
                    ->setPaper('a4', 'portrait')
                    ->setOption('defaultFont', 'DejaVu Sans')
                    ->setOption('dpi', 150);

                $receiptPath = sprintf(
                    'receipts/%s/%s/%s/quittance-%s.pdf',
                    $paiement->agency_id ?? 'global',
                    now()->format('Y'),
                    now()->format('m'),
                    $referenceQuittance
                );

                Storage::disk('private')->put($receiptPath, $pdf->output());
                $paiement->update(['receipt_path' => $receiptPath]);

                try {
                    $contrat->locataire->notify(new QuittanceLocataireNotification($paiement));
                } catch (\Throwable $e) {
                    Log::warning('Email locataire non envoyé', ['error' => $e->getMessage()]);
                }

                try {
                    $contrat->bien->proprietaire->notify(new PaiementProprietaireNotification($paiement));
                } catch (\Throwable $e) {
                    Log::warning('Email propriétaire non envoyé', ['error' => $e->getMessage()]);
                }

                return $paiement;
            });

            return redirect()
                ->route('admin.paiements.show', $paiement)
                ->with('success', "Paiement enregistré ✓  Réf : {$referenceQuittance}");

        } catch (\Throwable $e) {
            Log::error('Erreur création paiement', ['error' => $e->getMessage()]);
            return back()->withInput()->withErrors([
                'general' => 'Une erreur est survenue. Veuillez réessayer.',
            ]);
        }
    }

    // ─────────────────────────────────────────────────────────────────────
    // DÉTAIL
    // ─────────────────────────────────────────────────────────────────────

    public function show(Paiement $paiement)
    {
        $this->authorize('view', $paiement);
        $paiement->load('contrat.bien.proprietaire', 'contrat.locataire');
        return view('paiements.show', compact('paiement'));
    }

    // ─────────────────────────────────────────────────────────────────────
    // TÉLÉCHARGEMENT PDF
    // ─────────────────────────────────────────────────────────────────────

    public function downloadPDF(Paiement $paiement): BinaryFileResponse|RedirectResponse
    {
        $this->authorize('downloadPdf', $paiement);

        $fileName = 'quittance-' . $paiement->reference_paiement . '.pdf';

        if (
            ! empty($paiement->receipt_path)
            && Storage::disk('private')->exists($paiement->receipt_path)
        ) {
            return response()->download(
                Storage::disk('private')->path($paiement->receipt_path),
                $fileName
            );
        }

        $paiement->load('contrat.bien.proprietaire', 'contrat.locataire');
        $agency  = $paiement->agency ?? Auth::user()->agency;
        $pdfData = self::buildPdfData($paiement, $agency);

        $pdf = Pdf::loadView('paiements.pdf.quittance', $pdfData)
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
        $paiement->update(['receipt_path' => $receiptPath]);

        return response()->download(
            Storage::disk('private')->path($receiptPath),
            $fileName
        );
    }

    // ─────────────────────────────────────────────────────────────────────
    // ANNULATION
    // ─────────────────────────────────────────────────────────────────────

    public function annuler(Request $request, Paiement $paiement): RedirectResponse
    {
        $this->authorize('update', $paiement);

        if ($paiement->statut === 'annule') {
            return back()->withErrors(['general' => 'Ce paiement est déjà annulé.']);
        }

        $paiement->update([
            'statut' => 'annule',
            'notes'  => $paiement->notes
                . "\n[Annulé le " . now()->format('d/m/Y')
                . ' — ' . ($request->motif ?? 'Sans motif') . ']',
        ]);

        return back()->with('success', 'Paiement annulé.');
    }

    // ─────────────────────────────────────────────────────────────────────
    // DERNIÈRE PÉRIODE (AJAX)
    // ─────────────────────────────────────────────────────────────────────

    public function dernierePeriode(Contrat $contrat)
    {
        $dernier = Paiement::where('contrat_id', $contrat->id)
            ->where('statut', '!=', 'annule')
            ->orderByDesc('periode')
            ->first();

        $prochaine = $dernier
            ? Carbon::parse($dernier->periode)->addMonth()->format('Y-m')
            : Carbon::parse($contrat->date_debut)->format('Y-m');

        return response()->json([
            'prochaine_periode' => $prochaine,
            'loyer_nu'          => $contrat->loyer_nu_effectif,
            'charges'           => (float) ($contrat->charges_mensuelles ?? 0),
            'tom'               => (float) ($contrat->tom_amount ?? 0),
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────
    // MES PAIEMENTS (locataire)
    // ─────────────────────────────────────────────────────────────────────

    public function mesPaiements()
    {
        $this->authorize('isLocataire');

        $user    = Auth::user();
        $contrat = Contrat::where('locataire_id', $user->id)
                     ->where('statut', 'actif')
                     ->first();

        $paiements = $contrat
            ? Paiement::where('contrat_id', $contrat->id)
                ->where('statut', 'valide')
                ->orderByDesc('periode')
                ->get()
            : collect();

        return view('locataire.paiements', compact('paiements', 'contrat'));
    }

    // ─────────────────────────────────────────────────────────────────────
    // HELPER PRIVÉ : construction données PDF
    // ─────────────────────────────────────────────────────────────────────

    private static function buildPdfData(Paiement $paiement, mixed $agency): array
    {
        $contrat      = $paiement->contrat;
        $bien         = $contrat->bien;
        $proprietaire = $bien->proprietaire;
        $locataire    = $contrat->locataire;

        $loyerNu       = (float) ($paiement->loyer_nu ?? $paiement->montant_encaisse);
        $chargesAmount = (float) ($paiement->charges_amount ?? 0);
        $tomAmount     = (float) ($paiement->tom_amount ?? 0);
        $totalEncaisse = (float) $paiement->montant_encaisse;

        return [
            'paiement'     => $paiement,
            'contrat'      => $contrat,
            'bien'         => $bien,
            'proprietaire' => $proprietaire,
            'locataire'    => $locataire,

            'loyer_nu'       => $loyerNu,
            'charges_amount' => $chargesAmount,
            'tom_amount'     => $tomAmount,
            'total_encaisse' => $totalEncaisse,

            'montantEnLettres' => NombreEnLettres::convertir($totalEncaisse),
            'loyerNuEnLettres' => NombreEnLettres::convertir($loyerNu),
            'netEnLettres'     => NombreEnLettres::convertir((float) $paiement->net_proprietaire),

            'referenceBail' => $paiement->reference_bail
                ?? $contrat->reference_bail_affichee,

            'agence' => [
                'nom'       => $agency?->name       ?? 'Agence Immobilière',
                'adresse'   => $agency?->adresse    ?? 'Dakar, Sénégal',
                'telephone' => $agency?->telephone  ?? '',
                'email'     => $agency?->email      ?? '',
                'ninea'     => $agency?->ninea      ? 'NINEA : ' . $agency->ninea : '',
                'rccm'      => $agency?->rccm       ?? '',
                'logo_path' => $agency?->logo_path  ?? null,
                'couleur'   => $agency?->couleur_primaire ?? '#1E3A5F',
            ],
        ];
    }
}