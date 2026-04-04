<?php

namespace App\Http\Controllers;

use App\Models\Contrat;
use App\Models\Paiement;
use App\Notifications\PaiementProprietaireNotification;
use App\Notifications\QuittanceLocataireNotification;
use App\Services\FiscalContext;
use App\Services\FiscalService;
use App\Services\NombreEnLettres;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
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

    public function index(Request $request)
    {
        $this->authorize('viewAny', Paiement::class);

        $paiements = Paiement::select([
                'id', 'agency_id', 'contrat_id',
                'periode', 'montant_encaisse', 'commission_ttc', 'net_proprietaire',
                'mode_paiement', 'date_paiement', 'statut', 'reference_paiement',
            ])
            ->with([
                'contrat:id,bien_id,locataire_id',
                'contrat.bien:id,reference,adresse,ville',
                'contrat.locataire:id,name',
            ])
            ->orderByDesc('date_paiement')
            ->paginate(20);

        return view('paiements.index', compact('paiements'));
    }

    // ─────────────────────────────────────────────────────────────────────
    // FORMULAIRE CRÉATION
    // ─────────────────────────────────────────────────────────────────────

    public function create(Request $request)
    {
        $this->authorize('create', Paiement::class);

        $contrats = Contrat::where('statut', 'actif')
            ->select([
                'id', 'agency_id', 'bien_id', 'locataire_id',
                'loyer_contractuel', 'loyer_nu', 'charges_mensuelles',
                'tom_amount', 'date_debut', 'reference_bail',
                'type_bail', 'loyer_assujetti_tva', 'brs_applicable',
            ])
            ->with([
                'bien:id,agency_id,reference,adresse,ville,taux_commission,loyer_mensuel,meuble,type',
                'locataire:id,name',
                'locataire.locataire:user_id,est_entreprise,taux_brs_override',
            ])
            ->orderByDesc('created_at')
            ->get();

        $contratPreselectionne = $request->has('contrat_id')
            ? Contrat::with([
                'bien:id,reference,taux_commission,loyer_mensuel,meuble,type',
                'locataire:id,name',
                'locataire.locataire:user_id,est_entreprise,taux_brs_override',
              ])->find($request->contrat_id)
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
            'contrat_id'           => ['required', 'integer', 'exists:contrats,id'],
            'periode'              => ['required', 'date_format:Y-m'],
            'loyer_nu'             => ['required', 'numeric', 'min:1'],
            'charges_amount'       => ['nullable', 'numeric', 'min:0'],
            'tom_amount'           => ['nullable', 'numeric', 'min:0'],
            'mode_paiement'        => ['required', 'string', 'in:' . implode(',', array_keys(Paiement::MODES_PAIEMENT))],
            'date_paiement'        => ['required', 'date'],
            'caution_percue'       => ['nullable', 'numeric', 'min:0'],
            'est_premier_paiement' => ['nullable', 'boolean'],
            'reference_bail'       => ['nullable', 'string', 'max:60'],
            'notes'                => ['nullable', 'string', 'max:500'],
        ]);

        /** @var \App\Models\Contrat $contrat */
        $contrat = Contrat::with([
            'bien:id,agency_id,proprietaire_id,taux_commission,meuble,type',
            'locataire:id,name,email,telephone',
            'locataire.locataire:user_id,est_entreprise,taux_brs_override,nom_entreprise',
        ])->findOrFail($request->contrat_id);

        $this->authorize('view', $contrat);

        $periodeDate = Carbon::createFromFormat('Y-m', $request->periode)->startOfMonth();

        // Vérification doublon
        $doublon = Paiement::where('contrat_id', $contrat->id)
            ->whereYear('periode', $periodeDate->year)
            ->whereMonth('periode', $periodeDate->month)
            ->where('statut', '!=', 'annule')
            ->exists();

        if ($doublon) {
            return back()->withInput()->withErrors([
                'periode' => 'Un paiement existe déjà pour cette période.',
            ]);
        }

        $caution         = (float) ($request->caution_percue ?? 0);
        $estPremierPaiem = (bool)  ($request->est_premier_paiement ?? false);

        if ($caution > 0 && ! $estPremierPaiem) {
            return back()->withInput()->withErrors([
                'caution_percue' => 'Cochez "Premier paiement" pour enregistrer une caution.',
            ]);
        }

        // ── Calcul fiscal via FiscalService ──────────────────────────────
        $ctx = FiscalContext::fromContrat($contrat);

        // Si l'admin a saisi un loyer différent (indexation), reconstruire le contexte
        $loyerNuRequest = (float) $request->loyer_nu;
        if (abs($loyerNuRequest - $ctx->loyerNu) > 0.01) {
            $ctx = new FiscalContext(
                loyerNu:                $loyerNuRequest,
                chargesAmount:          (float) ($request->charges_amount ?? $ctx->chargesAmount),
                tomAmount:              (float) ($request->tom_amount ?? $ctx->tomAmount),
                typeBail:               $contrat->type_bail ?? 'habitation',
                estMeuble:              (bool) ($contrat->bien->meuble ?? false),
                tauxCommission:         (float) $contrat->bien->taux_commission,
                locataireEstEntreprise: $ctx->locataireEstEntreprise,
                tauxBrsLocataire:       $ctx->tauxBrsLocataire,
                tauxBrsContrat:         $ctx->tauxBrsContrat,
            );
        }

        $result = FiscalService::calculer($ctx);

        $referenceQuittance = sprintf(
            'QUITT-%s-%s-%s',
            $contrat->id,
            $periodeDate->format('Ym'),
            now()->format('His')
        );

        $referenceBail = ! empty($request->reference_bail)
            ? trim($request->reference_bail)
            : $contrat->reference_bail_affichee;

        // ╔══════════════════════════════════════════════════════════════╗
        // ║  TRANSACTION DB PURE — uniquement la ligne paiement        ║
        // ║  PDF généré EN DEHORS pour éviter rollback si DomPDF plante║
        // ╚══════════════════════════════════════════════════════════════╝
        try {
            $paiement = DB::transaction(function () use (
                $request, $contrat, $periodeDate, $result, $ctx,
                $caution, $estPremierPaiem, $referenceQuittance, $referenceBail
            ) {
                $paiement = new Paiement();
                $paiement->contrat_id                = $contrat->id;
                $paiement->periode                   = $periodeDate->toDateString();
                // Ventilation loyer
                $paiement->loyer_ht                  = $result->loyerHt;
                $paiement->tva_loyer                 = $result->tvaLoyer;
                $paiement->loyer_ttc                 = $result->loyerTtc;
                $paiement->loyer_nu                  = $result->loyerHt; // alias rétro-compat
                $paiement->charges_amount            = $result->chargesAmount;
                $paiement->tom_amount                = $result->tomAmount;
                $paiement->montant_encaisse          = $result->montantEncaisse;
                // Commission
                $paiement->mode_paiement             = $request->mode_paiement;
                $paiement->taux_commission_applique  = $ctx->tauxCommission;
                $paiement->commission_agence         = $result->commissionHt;
                $paiement->tva_commission            = $result->tvaCommission;
                $paiement->commission_ttc            = $result->commissionTtc;
                // Nets
                $paiement->net_proprietaire          = $result->netProprietaire;
                $paiement->brs_amount                = $result->brsAmount;
                $paiement->taux_brs_applique         = $result->tauxBrsApplique;
                $paiement->net_a_verser_proprietaire = $result->netAVerserProprietaire;
                // Snapshot fiscal immuable
                $paiement->regime_fiscal_snapshot    = $result->toArray();
                // Divers
                $paiement->caution_percue            = $caution;
                $paiement->est_premier_paiement      = $estPremierPaiem;
                $paiement->date_paiement             = $request->date_paiement;
                $paiement->reference_paiement        = $referenceQuittance;
                $paiement->reference_bail            = $referenceBail;
                $paiement->statut                    = 'valide';
                $paiement->notes                     = $request->notes;
                $paiement->save();

                return $paiement;
            });

        } catch (\Throwable $e) {
            Log::error('Erreur création paiement (DB)', [
                'error'      => $e->getMessage(),
                'contrat_id' => $contrat->id,
            ]);
            return back()->withInput()->withErrors([
                'general' => 'Erreur lors de l\'enregistrement. Veuillez réessayer.',
            ]);
        }

        // ╔══════════════════════════════════════════════════════════════╗
        // ║  PDF & NOTIFICATIONS — EN DEHORS de la transaction         ║
        // ║  Un échec ici ne rollback PAS le paiement sauvegardé.      ║
        // ╚══════════════════════════════════════════════════════════════╝
        try {
            $contrat->load('bien.proprietaire', 'locataire');
            $paiement->load('contrat.bien.proprietaire', 'contrat.locataire');

            $agency  = $paiement->agency ?? Auth::user()->agency;
            $pdfData = self::buildPdfData($paiement, $agency);

            $pdf = Pdf::loadView('paiements.pdf.quittance', $pdfData)
                ->setPaper('a4', 'portrait')
                ->setOption('defaultFont', 'DejaVu Sans')
                ->setOption('dpi', 96)
                ->setOption('isRemoteEnabled', false)
                ->setOption('isFontSubsettingEnabled', true);

            $receiptPath = sprintf(
                'receipts/%s/%s/%s/quittance-%s.pdf',
                $paiement->agency_id ?? 'global',
                now()->format('Y'),
                now()->format('m'),
                $referenceQuittance
            );

            Storage::disk('private')->put($receiptPath, $pdf->output());
            $paiement->receipt_path = $receiptPath;
            $paiement->saveQuietly(); // ne déclenche pas LogsActivity

        } catch (\Throwable $e) {
            Log::warning('PDF non généré — sera régénéré au prochain accès', [
                'paiement_id' => $paiement->id,
                'error'       => $e->getMessage(),
            ]);
        }

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

        return redirect()
            ->route('admin.paiements.show', $paiement)
            ->with('success', "Paiement enregistré ✓  Réf : {$referenceQuittance}");
    }

    // ─────────────────────────────────────────────────────────────────────
    // DÉTAIL
    // ─────────────────────────────────────────────────────────────────────

    public function show(Paiement $paiement)
    {
        $this->authorize('view', $paiement);

        $paiement->load([
            'contrat.bien.proprietaire:id,name,telephone,adresse',
            'contrat.locataire:id,name,email,telephone',
            'contrat.locataire.locataire:user_id,est_entreprise,nom_entreprise,ninea_locataire',
        ]);

        return view('paiements.show', compact('paiement'));
    }

    // ─────────────────────────────────────────────────────────────────────
    // APERÇU FISCAL TEMPS RÉEL (AJAX)
    // ─────────────────────────────────────────────────────────────────────

    /**
     * Retourne la ventilation fiscale en JSON pour l'aperçu dans le formulaire create.
     * Route : GET admin/paiements/fiscal-preview/{contrat}
     */
    public function fiscalPreview(Request $request, Contrat $contrat): JsonResponse
    {
        $contrat->load([
            'bien:id,taux_commission,meuble,type',
            'locataire.locataire:user_id,est_entreprise,taux_brs_override',
        ]);

        $ctx = FiscalContext::fromContrat($contrat);

        if ($request->filled('loyer_nu')) {
            $ctx = new FiscalContext(
                loyerNu:                (float) $request->loyer_nu,
                chargesAmount:          (float) ($request->charges ?? $ctx->chargesAmount),
                tomAmount:              (float) ($request->tom ?? $ctx->tomAmount),
                typeBail:               $contrat->type_bail ?? 'habitation',
                estMeuble:              (bool) ($contrat->bien->meuble ?? false),
                tauxCommission:         (float) $contrat->bien->taux_commission,
                locataireEstEntreprise: $ctx->locataireEstEntreprise,
                tauxBrsLocataire:       $ctx->tauxBrsLocataire,
                tauxBrsContrat:         $ctx->tauxBrsContrat,
            );
        }

        return response()->json(FiscalService::calculer($ctx)->toArray());
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

        // Fallback — régénération à la demande
        $paiement->load('contrat.bien.proprietaire', 'contrat.locataire');
        $agency  = $paiement->agency ?? Auth::user()->agency;
        $pdfData = self::buildPdfData($paiement, $agency);

        $pdf = Pdf::loadView('paiements.pdf.quittance', $pdfData)
            ->setPaper('a4', 'portrait')
            ->setOption('defaultFont', 'DejaVu Sans')
            ->setOption('dpi', 96)
            ->setOption('isRemoteEnabled', false)
            ->setOption('isFontSubsettingEnabled', true);

        $receiptPath = sprintf(
            'receipts/%s/%s/%s/quittance-%s.pdf',
            $paiement->agency_id ?? 'global',
            now()->format('Y'),
            now()->format('m'),
            $paiement->reference_paiement
        );

        Storage::disk('private')->put($receiptPath, $pdf->output());
        $paiement->receipt_path = $receiptPath;
        $paiement->saveQuietly();

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

        $paiement->statut = 'annule';
        $paiement->notes  = ($paiement->notes ?? '')
            . "\n[Annulé le " . now()->format('d/m/Y')
            . ' — ' . ($request->motif ?? 'Sans motif') . ']';
        $paiement->save();

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
            ->select(['id', 'contrat_id', 'periode'])
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
                     ->select(['id', 'bien_id', 'locataire_id', 'statut', 'loyer_contractuel'])
                     ->first();

        $paiements = $contrat
            ? Paiement::where('contrat_id', $contrat->id)
                ->where('statut', 'valide')
                ->select([
                    'id', 'contrat_id', 'agency_id',
                    'periode', 'montant_encaisse', 'mode_paiement',
                    'date_paiement', 'reference_paiement', 'statut',
                ])
                ->orderByDesc('periode')
                ->get()
            : collect();

        return view('locataire.paiements', compact('paiements', 'contrat'));
    }

    // ─────────────────────────────────────────────────────────────────────
    // HELPER PRIVÉ — Construction données PDF
    // ─────────────────────────────────────────────────────────────────────

    private static function buildPdfData(Paiement $paiement, mixed $agency): array
    {
        $contrat      = $paiement->contrat;
        $bien         = $contrat->bien;
        $proprietaire = $bien->proprietaire;
        $locataire    = $contrat->locataire;

        $snapshot = $paiement->regime_fiscal_snapshot;

        return [
            'paiement'       => $paiement,
            'contrat'        => $contrat,
            'bien'           => $bien,
            'proprietaire'   => $proprietaire,
            'locataire'      => $locataire,

            // Ventilation loyer (avec fallbacks pour anciens paiements)
            'loyer_ht'       => (float) ($paiement->loyer_ht ?? $paiement->loyer_nu),
            'tva_loyer'      => (float) ($paiement->tva_loyer ?? 0),
            'loyer_ttc'      => (float) ($paiement->loyer_ttc ?? $paiement->loyer_nu),
            'loyer_nu'       => (float) ($paiement->loyer_ht ?? $paiement->loyer_nu), // compat
            'charges_amount' => (float) ($paiement->charges_amount ?? 0),
            'tom_amount'     => (float) ($paiement->tom_amount ?? 0),
            'total_encaisse' => (float) $paiement->montant_encaisse,

            // BRS
            'brs_amount'        => (float) ($paiement->brs_amount ?? 0),
            'brs_applicable'    => ($paiement->brs_amount ?? 0) > 0,
            'taux_brs_applique' => (float) ($paiement->taux_brs_applique ?? 0),

            // Nets
            'net_a_verser' => (float) ($paiement->net_a_verser_proprietaire ?? $paiement->net_proprietaire),

            // Régime fiscal
            'regime_fiscal'   => $snapshot['regime_fiscal'] ?? $paiement->regime_fiscal_label,
            'loyer_assujetti' => ($paiement->tva_loyer ?? 0) > 0,

            // Montants en lettres
            'montantEnLettres'    => NombreEnLettres::convertir((float) $paiement->montant_encaisse),
            'loyerNuEnLettres'    => NombreEnLettres::convertir((float) ($paiement->loyer_ht ?? $paiement->loyer_nu)),
            'netEnLettres'        => NombreEnLettres::convertir((float) $paiement->net_proprietaire),
            'netAVerserEnLettres' => NombreEnLettres::convertir((float) ($paiement->net_a_verser_proprietaire ?? $paiement->net_proprietaire)),

            'referenceBail' => $paiement->reference_bail_affichee,

            'agence' => [
                'nom'       => $agency?->name            ?? 'Agence Immobilière',
                'adresse'   => $agency?->adresse         ?? 'Dakar, Sénégal',
                'telephone' => $agency?->telephone       ?? '',
                'email'     => $agency?->email           ?? '',
                'ninea'     => $agency?->ninea ? 'NINEA : ' . $agency->ninea : '',
                'rccm'      => $agency?->rccm            ?? '',
                'logo_path' => $agency?->logo_path       ?? null,
                'couleur'   => $agency?->couleur_primaire ?? '#0d1117',
            ],
        ];
    }
}