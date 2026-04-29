<?php

namespace App\Http\Controllers;

use App\Models\Bien;
use App\Models\Immeuble;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ImmeubleController extends Controller
{
    use AuthorizesRequests;

    public function index(): View
    {
        $this->authorize('isStaff');

        $immeubles = Immeuble::with(['proprietaire:id,name,email'])
            ->withCount('biens')
            ->latest()
            ->paginate(12);

        return view('immeubles.index', compact('immeubles'));
    }

    public function show(Immeuble $immeuble): View
    {
        $this->authorize('isStaff');

        $immeuble->load([
            'proprietaire:id,name,email,telephone',
            'biens' => fn($q) => $q->with('contratActif.locataire:id,name,telephone')->orderBy('reference'),
        ]);

        return view('immeubles.show', compact('immeuble'));
    }

    public function create(): View
    {
        $this->authorize('isStaff');

        $proprietaires = User::where('role', 'proprietaire')
            ->where('agency_id', Auth::user()->agency_id)
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        return view('immeubles.create', compact('proprietaires'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('isStaff');

        $hasUnites = $request->filled('nombre_unites');

        $rules = [
            'proprietaire_id' => ['required', 'exists:users,id'],
            'nom'             => ['required', 'string', 'max:255'],
            'adresse'         => ['required', 'string', 'max:255'],
            'ville'           => ['required', 'string', 'max:100'],
            'nombre_niveaux'  => ['nullable', 'integer', 'min:1', 'max:99'],
            'description'     => ['nullable', 'string'],
        ];

        if ($hasUnites) {
            $rules['type_unite']        = ['required', Rule::in(array_keys(Bien::TYPES))];
            $rules['loyer_par_unite']   = ['required', 'numeric', 'min:1000'];
            $rules['taux_commission']   = ['nullable', 'numeric', 'min:0', 'max:30'];
            $rules['mode_numerotation'] = ['nullable', Rule::in(['simple', 'etage'])];

            if ($request->input('mode_numerotation') === 'etage') {
                $rules['nombre_niveaux']    = ['required', 'integer', 'min:1', 'max:99'];
                $rules['unites_par_niveau'] = ['required', 'integer', 'min:1', 'max:99'];
            } else {
                $rules['nombre_unites'] = ['required', 'integer', 'min:1', 'max:999'];
            }
        }

        $validated = $request->validate($rules, [
            'proprietaire_id.required' => 'Veuillez sélectionner un propriétaire.',
            'nom.required'             => "Le nom de l'immeuble est obligatoire.",
            'adresse.required'         => "L'adresse est obligatoire.",
            'ville.required'           => 'La ville est obligatoire.',
            'nombre_unites.required'   => "Le nombre d'unités est obligatoire.",
            'type_unite.required'      => 'Le type des unités est obligatoire.',
            'loyer_par_unite.required' => 'Le loyer par unité est obligatoire.',
        ]);

        $agencyId = Auth::user()->agency_id;

        $proprioValide = User::where('id', $validated['proprietaire_id'])
            ->where('agency_id', $agencyId)
            ->where('role', 'proprietaire')
            ->exists();

        if (! $proprioValide) {
            return back()
                ->withErrors(['proprietaire_id' => "Ce propriétaire n'appartient pas à votre agence."])
                ->withInput();
        }

        [$immeuble, $nbCreees] = DB::transaction(function () use ($validated, $agencyId, $hasUnites, $request) {
            $immeuble = Immeuble::create([
                'agency_id'       => $agencyId,
                'proprietaire_id' => $validated['proprietaire_id'],
                'nom'             => $validated['nom'],
                'adresse'         => $validated['adresse'],
                'ville'           => $validated['ville'],
                'nombre_niveaux'  => $validated['nombre_niveaux'] ?? null,
                'description'     => $validated['description'] ?? null,
            ]);

            if (! $hasUnites) {
                return [$immeuble, 0];
            }

            $taux = $validated['taux_commission'] ?? 10;
            $nom  = $validated['nom'];
            $mode = $request->input('mode_numerotation', 'simple');

            $titres = [];

            if ($mode === 'etage') {
                $niveaux         = (int) ($validated['nombre_niveaux'] ?? 1);
                $unitesParNiveau = (int) ($validated['unites_par_niveau'] ?? 1);
                for ($etage = 0; $etage < $niveaux; $etage++) {
                    $prefixe = $etage === 0 ? '0' : (string) $etage;
                    for ($porte = 1; $porte <= $unitesParNiveau; $porte++) {
                        $titres[] = $nom . ' — Appt ' . $prefixe . str_pad($porte, 2, '0', STR_PAD_LEFT);
                    }
                }
            } else {
                $nb = (int) $validated['nombre_unites'];
                for ($i = 1; $i <= $nb; $i++) {
                    $titres[] = $nom . ' — Appt ' . str_pad($i, 2, '0', STR_PAD_LEFT);
                }
            }

            foreach ($titres as $titre) {
                Bien::create([
                    'agency_id'       => $agencyId,
                    'immeuble_id'     => $immeuble->id,
                    'proprietaire_id' => $validated['proprietaire_id'],
                    'reference'       => Bien::generateReference($agencyId),
                    'titre'           => $titre,
                    'type'            => $validated['type_unite'],
                    'adresse'         => $validated['adresse'],
                    'ville'           => $validated['ville'],
                    'loyer_mensuel'   => $validated['loyer_par_unite'],
                    'taux_commission' => $taux,
                    'statut'          => 'disponible',
                ]);
            }

            return [$immeuble, count($titres)];
        });

        return redirect()
            ->route('admin.immeubles.show', $immeuble)
            ->with('success', $nbCreees > 0
                ? "Immeuble créé avec {$nbCreees} unité(s) liées."
                : 'Immeuble créé avec succès.');
    }

    public function edit(Immeuble $immeuble): View
    {
        $this->authorize('isStaff');

        $proprietaires = User::where('role', 'proprietaire')
            ->where('agency_id', Auth::user()->agency_id)
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        return view('immeubles.edit', compact('immeuble', 'proprietaires'));
    }

    public function update(Request $request, Immeuble $immeuble): RedirectResponse
    {
        $this->authorize('isStaff');

        $validated = $request->validate([
            'proprietaire_id' => ['required', 'exists:users,id'],
            'nom'             => ['required', 'string', 'max:255'],
            'adresse'         => ['required', 'string', 'max:255'],
            'ville'           => ['required', 'string', 'max:100'],
            'nombre_niveaux'  => ['nullable', 'integer', 'min:1', 'max:99'],
            'description'     => ['nullable', 'string'],
        ], [
            'nom.required'     => "Le nom de l'immeuble est obligatoire.",
            'adresse.required' => "L'adresse est obligatoire.",
            'ville.required'   => 'La ville est obligatoire.',
        ]);

        $proprioValide = User::where('id', $validated['proprietaire_id'])
            ->where('agency_id', Auth::user()->agency_id)
            ->where('role', 'proprietaire')
            ->exists();

        if (! $proprioValide) {
            return back()
                ->withErrors(['proprietaire_id' => "Ce propriétaire n'appartient pas à votre agence."])
                ->withInput();
        }

        $immeuble->update($validated);

        return redirect()
            ->route('admin.immeubles.show', $immeuble)
            ->with('success', 'Immeuble mis à jour avec succès.');
    }

    public function destroy(Immeuble $immeuble): RedirectResponse
    {
        $this->authorize('isStaff');

        if ($immeuble->biens()->whereHas('contratActif')->exists()) {
            return back()->withErrors([
                'general' => 'Impossible de supprimer un immeuble avec des unités sous contrat actif.',
            ]);
        }

        DB::transaction(function () use ($immeuble) {
            // Archiver et soft-delete les biens liés sans contrat actif
            // pour éviter les immeuble_id orphelins après le soft-delete de l'immeuble.
            $immeuble->biens()->each(function (Bien $bien) {
                $bien->statut = 'archive';
                $bien->save();
                $bien->delete();
            });

            $immeuble->delete();
        });

        return redirect()
            ->route('admin.immeubles.index')
            ->with('success', 'Immeuble et ses unités archivés avec succès.');
    }
}
