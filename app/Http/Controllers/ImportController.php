<?php

namespace App\Http\Controllers;

use App\Imports\BiensImport;
use App\Imports\LocatairesImport;
use App\Imports\ProprietairesImport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class ImportController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('check.feature:import_excel'),
        ];
    }

    /**
     * Configuration centralisée des trois imports supportés.
     * Évite la duplication de code entre proprietaires / locataires / biens.
     */
    private const IMPORTS = [
        'proprietaires' => [
            'class' => ProprietairesImport::class,
            'label' => 'propriétaire(s)',
        ],
        'locataires' => [
            'class' => LocatairesImport::class,
            'label' => 'locataire(s)',
        ],
        'biens' => [
            'class' => BiensImport::class,
            'label' => 'bien(s)',
        ],
    ];

    public function index()
    {
        $this->authorize('isAdmin');
        return view('admin.import.index');
    }

    public function proprietaires(Request $request): RedirectResponse
    {
        return $this->handleImport($request, 'proprietaires');
    }

    public function locataires(Request $request): RedirectResponse
    {
        return $this->handleImport($request, 'locataires');
    }

    public function biens(Request $request): RedirectResponse
    {
        return $this->handleImport($request, 'biens');
    }

    // ── Méthode mutualisée ───────────────────────────────────────────────
    // Une seule source de vérité pour validation, exécution et gestion d'erreur.

    private function handleImport(Request $request, string $type): RedirectResponse
    {
        $this->authorize('isAdmin');

        // IMP2-004/005/006 — CORRECTION :
        // Le validateur mimes:csv n'accepte que text/csv, application/csv, etc.
        // Or PHP (finfo) détecte les fichiers CSV comme text/plain → tous les CSV sont rejetés.
        // On valide par extension de fichier (fiable) et on délègue la validation du contenu
        // à maatwebsite/excel (qui lèvera une exception si le format est invalide).
        $request->validate([
            'fichier' => [
                'required', 'file', 'max:5120',
                function ($attribute, $value, $fail) {
                    $ext = strtolower($value->getClientOriginalExtension());
                    if (! in_array($ext, ['xlsx', 'xls', 'csv'])) {
                        $fail('Format accepté : xlsx, xls ou csv.');
                    }
                },
            ],
        ], [
            'fichier.required' => 'Veuillez sélectionner un fichier.',
            'fichier.file'     => 'Le fichier transmis est invalide.',
            'fichier.max'      => 'Le fichier ne doit pas dépasser 5 Mo.',
        ]);

        $config   = self::IMPORTS[$type];
        $importer = new ($config['class'])(Auth::user()->agency_id);

        // IMP2-007 — Transaction globale (all-or-nothing) :
        // Si une seule ligne échoue, toutes les insertions sont annulées.
        // Les DB::transaction() internes (par ligne) fonctionnent comme des SAVEPOINTs
        // à l'intérieur de cette transaction externe, ce qui préserve l'atomicité par ligne.
        DB::beginTransaction();

        try {
            Excel::import($importer, $request->file('fichier'));
        } catch (Throwable $e) {
            DB::rollBack();

            Log::error('Import Excel échoué', [
                'type'      => $type,
                'agency_id' => Auth::user()->agency_id,
                'filename'  => $request->file('fichier')?->getClientOriginalName(),
                'message'   => $e->getMessage(),
            ]);

            return back()
                ->with('import_type',  $type)
                ->with('import_error', "Le fichier n'a pas pu être lu. Vérifiez qu'il respecte le modèle fourni puis réessayez.");
        }

        // Si des erreurs de ligne → rollback complet (aucune donnée partielle en base)
        if (! empty($importer->errors)) {
            DB::rollBack();

            return back()
                ->with('import_created',     0)
                ->with('import_skipped',     $importer->skipped)
                ->with('import_errors',      $importer->errors)
                ->with('import_type',        $type)
                ->with('import_rolled_back', true);
        }

        DB::commit();

        return back()
            ->with('import_created', $importer->created)
            ->with('import_skipped', 0)
            ->with('import_errors',  [])
            ->with('import_type',    $type);
    }

    // ── Téléchargement des modèles CSV ───────────────────────────────────

    public function templateProprietaires(): StreamedResponse
    {
        $this->authorize('isAdmin');
        return $this->streamCsv('modele_proprietaires.csv', [
            ['nom_complet', 'email', 'telephone', 'genre', 'cni', 'date_naissance', 'nationalite',
             'ville', 'quartier', 'mode_paiement', 'banque', 'numero_compte',
             'numero_wave', 'numero_om', 'ninea'],
            ['Mamadou Diallo', 'mamadou@exemple.com', '77 000 00 01', 'M', 'SN-12345678',
             '1980-05-15', 'Sénégalaise', 'Dakar', 'Plateau', 'virement',
             'CBAO', 'SN000000001', '', '', '1234567890123'],
        ]);
    }

    public function templateLocataires(): StreamedResponse
    {
        $this->authorize('isAdmin');
        return $this->streamCsv('modele_locataires.csv', [
            ['nom_complet', 'email', 'telephone', 'genre', 'cni', 'date_naissance', 'nationalite',
             'ville', 'quartier', 'profession', 'employeur', 'revenu_mensuel',
             'contact_urgence_nom', 'contact_urgence_tel', 'contact_urgence_lien',
             'type_locataire', 'nom_entreprise', 'ninea_locataire', 'rccm_locataire'],
            ['Fatou Ndiaye', 'fatou@exemple.com', '78 000 00 02', 'F', 'SN-87654321',
             '1990-03-20', 'Sénégalaise', 'Dakar', 'Mermoz', 'Ingénieure', 'Sonatel', '400000',
             'Ibrahima Ndiaye', '77 111 22 33', 'Frère',
             'particulier', '', '', ''],
        ]);
    }

    public function templateBiens(): StreamedResponse
    {
        $this->authorize('isAdmin');
        return $this->streamCsv('modele_biens.csv', [
            ['titre', 'type', 'adresse', 'ville', 'quartier', 'commune',
             'surface_m2', 'nombre_pieces', 'meuble', 'loyer_mensuel',
             'taux_commission', 'statut', 'description', 'proprietaire_email'],
            ['Appartement F3 Plateau', 'appartement', '12 Rue Carnot', 'Dakar', 'Plateau', 'Dakar-Plateau',
             '75', '3', 'non', '250000',
             '10', 'disponible', 'Beau F3 lumineux', 'mamadou@exemple.com'],
        ]);
    }

    /**
     * Stream un CSV au navigateur.
     *
     *  - BOM UTF-8 ajouté en tête : Excel ouvre alors le fichier en UTF-8 et les
     *    accents (é, à, ç) s'affichent correctement.
     *  - Séparateur point-virgule : c'est celui attendu par Excel en environnement
     *    francophone (la virgule sert de séparateur décimal).
     *  - Streamé via streamDownload pour ne pas charger tout le CSV en mémoire.
     */
    private function streamCsv(string $filename, array $rows): StreamedResponse
    {
        return response()->streamDownload(function () use ($rows) {
            $handle = fopen('php://output', 'w');

            // BOM UTF-8 — indispensable pour qu'Excel affiche les accents.
            fwrite($handle, "\xEF\xBB\xBF");

            foreach ($rows as $row) {
                fputcsv($handle, $row, ';');
            }

            fclose($handle);
        }, $filename, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Cache-Control'       => 'no-store, no-cache, must-revalidate',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}