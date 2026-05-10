<?php

namespace App\Http\Controllers;

use App\Imports\BiensImport;
use App\Imports\LocatairesImport;
use App\Imports\ProprietairesImport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class ImportController extends Controller
{
    public function index()
    {
        $this->authorize('isAdmin');
        return view('admin.import.index');
    }

    public function proprietaires(Request $request): RedirectResponse
    {
        $this->authorize('isAdmin');

        $request->validate([
            'fichier' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:5120'],
        ], [
            'fichier.required' => 'Veuillez sélectionner un fichier.',
            'fichier.mimes'    => 'Format accepté : xlsx, xls ou csv.',
            'fichier.max'      => 'Le fichier ne doit pas dépasser 5 Mo.',
        ]);

        $import = new ProprietairesImport(Auth::user()->agency_id);
        Excel::import($import, $request->file('fichier'));

        return back()
            ->with('import_created', $import->created)
            ->with('import_skipped', $import->skipped)
            ->with('import_errors',  $import->errors)
            ->with('import_type',    'proprietaires');
    }

    public function locataires(Request $request): RedirectResponse
    {
        $this->authorize('isAdmin');

        $request->validate([
            'fichier' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:5120'],
        ], [
            'fichier.required' => 'Veuillez sélectionner un fichier.',
            'fichier.mimes'    => 'Format accepté : xlsx, xls ou csv.',
            'fichier.max'      => 'Le fichier ne doit pas dépasser 5 Mo.',
        ]);

        $import = new LocatairesImport(Auth::user()->agency_id);
        Excel::import($import, $request->file('fichier'));

        return back()
            ->with('import_created', $import->created)
            ->with('import_skipped', $import->skipped)
            ->with('import_errors',  $import->errors)
            ->with('import_type',    'locataires');
    }

    public function biens(Request $request): RedirectResponse
    {
        $this->authorize('isAdmin');

        $request->validate([
            'fichier' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:5120'],
        ], [
            'fichier.required' => 'Veuillez sélectionner un fichier.',
            'fichier.mimes'    => 'Format accepté : xlsx, xls ou csv.',
            'fichier.max'      => 'Le fichier ne doit pas dépasser 5 Mo.',
        ]);

        $import = new BiensImport(Auth::user()->agency_id);
        Excel::import($import, $request->file('fichier'));

        return back()
            ->with('import_created', $import->created)
            ->with('import_skipped', $import->skipped)
            ->with('import_errors',  $import->errors)
            ->with('import_type',    'biens');
    }

    // ── Téléchargement des modèles Excel ────────────────────────────────────

    public function templateProprietaires()
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

    public function templateLocataires()
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

    public function templateBiens()
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

    private function streamCsv(string $filename, array $rows)
    {
        $handle = fopen('php://temp', 'r+');
        foreach ($rows as $row) {
            fputcsv($handle, $row, ',');
        }
        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return response($content, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
