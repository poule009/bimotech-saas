<?php

namespace App\Http\Controllers;

use App\Exports\ImportCodesExport;
use App\Exports\ImportTemplateExport;
use App\Models\ImportBatch;
use App\Services\Import\ImportManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;

class ImportController extends Controller implements HasMiddleware
{
    public function __construct(private ImportManager $manager) {}

    public static function middleware(): array
    {
        return [new Middleware('check.feature:import_excel')];
    }

    private const TYPES = ['proprietaires', 'biens', 'locataires', 'contrats'];

    // ── Écran principal (stepper) ────────────────────────────────────────────

    public function index(Request $request)
    {
        $this->authorize('isAdmin');
        $agencyId = Auth::user()->agency_id;

        $etat = $this->manager->etat($agencyId);

        // Aperçu en attente par type (persisté en statut preview).
        $previews = [];
        $meta     = [];
        foreach (self::TYPES as $type) {
            $previews[$type] = ImportBatch::where('agency_id', $agencyId)
                ->where('type', $type)
                ->where('statut', 'preview')
                ->latest()
                ->first();

            $handler = $this->manager->handler($type, $agencyId);
            $meta[$type] = ['colonnes' => $handler->previewColumns()];
        }

        // Étape active : demandée, sinon première étape déverrouillée non terminée.
        $current = $request->query('step');
        if (! in_array($current, self::TYPES, true) || ! $etat[$current]['unlocked']) {
            $current = collect(self::TYPES)->first(
                fn ($t) => $etat[$t]['unlocked'] && ! $etat[$t]['done']
            ) ?? 'proprietaires';
        }

        return view('admin.import.index', compact('etat', 'previews', 'meta', 'current'));
    }

    // ── Étape 1 : aperçu (upload sans écriture) ──────────────────────────────

    public function preview(Request $request, string $type): RedirectResponse
    {
        $this->authorize('isAdmin');
        abort_unless(in_array($type, self::TYPES, true), 404);

        $request->validate([
            'fichier' => [
                'required', 'file', 'max:5120',
                function ($attribute, $value, $fail) {
                    $ext = strtolower($value->getClientOriginalExtension());
                    if (! in_array($ext, ['xlsx', 'xls', 'csv'], true)) {
                        $fail('Format accepté : xlsx, xls ou csv.');
                    }
                },
            ],
        ], [
            'fichier.required' => 'Veuillez sélectionner un fichier.',
            'fichier.max'      => 'Le fichier ne doit pas dépasser 5 Mo.',
        ]);

        try {
            $this->manager->preview($type, $request->file('fichier'), Auth::user()->agency_id);
        } catch (Throwable $e) {
            Log::error('Aperçu import échoué', ['type' => $type, 'message' => $e->getMessage()]);
            return back()
                ->with('import_step', $type)
                ->with('import_error', "Le fichier n'a pas pu être lu. Vérifiez qu'il respecte le modèle fourni.");
        }

        return redirect()->route('admin.import.index', ['step' => $type]);
    }

    // ── Retirer l'aperçu en attente ──────────────────────────────────────────

    public function discard(string $type): RedirectResponse
    {
        $this->authorize('isAdmin');
        abort_unless(in_array($type, self::TYPES, true), 404);

        ImportBatch::where('agency_id', Auth::user()->agency_id)
            ->where('type', $type)
            ->where('statut', 'preview')
            ->delete();

        return redirect()->route('admin.import.index', ['step' => $type]);
    }

    // ── Étape 2 : confirmation (commit) ──────────────────────────────────────

    public function commit(ImportBatch $batch): RedirectResponse
    {
        $this->authorize('isAdmin');

        $this->manager->commit($batch);

        return redirect()
            ->route('admin.import.index', ['step' => $batch->type])
            ->with('import_done', $batch->type);
    }

    // ── Annulation d'un lot ──────────────────────────────────────────────────

    public function undo(ImportBatch $batch): RedirectResponse
    {
        $this->authorize('isAdmin');

        $raison = $this->manager->raisonBlocageAnnulation($batch);
        if ($raison !== null) {
            return back()->with('import_error', $raison);
        }

        $this->manager->undo($batch);

        return back()->with('import_success', "Import « {$batch->type_label} » annulé — les données ont été retirées.");
    }

    // ── Historique des imports ───────────────────────────────────────────────

    public function historique()
    {
        $this->authorize('isAdmin');

        $batches = ImportBatch::where('statut', '!=', 'preview')
            ->latest()
            ->paginate(20);

        // Annulabilité calculée pour l'affichage.
        $annulables = [];
        foreach ($batches as $b) {
            $annulables[$b->id] = $b->statut === 'committed' ? $this->manager->annulable($b) : false;
        }

        return view('admin.import.historique', compact('batches', 'annulables'));
    }

    // ── Téléchargements ──────────────────────────────────────────────────────

    public function template(string $type): BinaryFileResponse
    {
        $this->authorize('isAdmin');
        abort_unless(in_array($type, self::TYPES, true), 404);

        $handler = $this->manager->handler($type, Auth::user()->agency_id);

        return Excel::download(
            new ImportTemplateExport($handler->templateHeaders(), $handler->templateSample()),
            'modele_' . $type . '.xlsx'
        );
    }

    public function codes(ImportBatch $batch): BinaryFileResponse
    {
        $this->authorize('isAdmin');
        abort_unless($batch->statut === 'committed' && ! empty($batch->codes), 404);

        return Excel::download(
            new ImportCodesExport($batch->codes),
            'codes_' . $batch->type . '_' . $batch->id . '.xlsx'
        );
    }
}
