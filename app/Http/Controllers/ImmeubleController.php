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

        $hasUnites    = $request->input('avec_unites') === '1';
        $mode         = $request->input('mode_numerotation', 'simple');
        $avecRdc      = $request->boolean('avec_rdc');
        $rdcDifferent = $hasUnites && $mode === 'etage' && $avecRdc && $request->boolean('rdc_different');

        $rules = [
            'proprietaire_id' => ['required', 'exists:users,id'],
            'nom'             => ['required', 'string', 'max:255'],
            'adresse'         => ['required', 'string', 'max:255'],
            'ville'           => ['required', 'string', 'max:100'],
            'description'     => ['nullable', 'string'],
        ];

        if ($hasUnites) {
            $rules['type_unite']        = ['required', Rule::in(array_keys(Bien::TYPES))];
            $rules['loyer_par_unite']   = ['required', 'numeric', 'min:0'];
            $rules['taux_commission']   = ['nullable', 'numeric', 'min:0', 'max:30'];
            $rules['mode_numerotation'] = ['nullable', Rule::in(['simple', 'etage'])];
            $rules['charges_par_unite'] = ['nullable', 'numeric', 'min:0'];
            $rules['caution_par_unite'] = ['nullable', 'numeric', 'min:0'];

            if ($mode === 'etage') {
                $rules['nombre_etages']     = ['required', 'integer', 'min:0', 'max:99'];
                $rules['unites_par_niveau'] = ['required', 'integer', 'min:1', 'max:26'];

                if ($rdcDifferent) {
                    $rules['rdc_type']  = ['required', Rule::in(array_keys(Bien::TYPES))];
                    $rules['rdc_loyer'] = ['required', 'numeric', 'min:0'];
                }
            } else {
                $rules['nombre_unites'] = ['required', 'integer', 'min:1', 'max:999'];
            }
        }

        $validated = $request->validate($rules, [
            'proprietaire_id.required'   => 'Veuillez sélectionner un propriétaire.',
            'nom.required'               => "Le nom de l'immeuble est obligatoire.",
            'adresse.required'           => "L'adresse est obligatoire.",
            'ville.required'             => 'La ville est obligatoire.',
            'nombre_unites.required'     => "Le nombre d'appartements est obligatoire.",
            'nombre_etages.required'     => "Le nombre d'étages est obligatoire.",
            'unites_par_niveau.required' => "Le nombre d'appartements par étage est obligatoire.",
            'type_unite.required'        => 'Le type des appartements est obligatoire.',
            'loyer_par_unite.required'   => 'Le loyer par appartement est obligatoire.',
            'rdc_type.required'          => 'Le type du RDC est obligatoire.',
            'rdc_loyer.required'         => 'Le loyer du RDC est obligatoire.',
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

        // ── Nombre d'unités demandées ───────────────────────────────────────
        $nombreDemandeUnites = 0;
        if ($hasUnites) {
            if ($mode === 'etage') {
                $totalNiveaux        = (int) ($validated['nombre_etages'] ?? 0) + ($avecRdc ? 1 : 0);
                $nombreDemandeUnites = $totalNiveaux * (int) ($validated['unites_par_niveau'] ?? 1);
            } else {
                $nombreDemandeUnites = (int) ($validated['nombre_unites'] ?? 0);
            }
        }

        // ── Vérification limite selon plan ──────────────────────────────────
        if ($nombreDemandeUnites > 0) {
            $agency     = Auth::user()->agency;
            $planNiveau = $agency?->subscription?->plan_niveau ?? 'legacy';

            $limiteUnites = match ($planNiveau) {
                'starter'       => 15,
                'pro', 'legacy' => 50,
                'agence'        => null,
                default         => 50,
            };

            if ($agency && $limiteUnites !== null) {
                $nbActuelles = $agency->nbUnitesActives();

                if ($nbActuelles + $nombreDemandeUnites > $limiteUnites) {
                    [$planSuivant, $limiteSuivante] = match ($planNiveau) {
                        'starter' => ['Pro', '50 unités'],
                        default   => ['Agence', 'illimité'],
                    };

                    return redirect()
                        ->back()
                        ->with('upgrade_required', [
                            'plan_actuel'     => config('plans.labels.' . $planNiveau, 'Pro'),
                            'nb_unites'       => $nbActuelles,
                            'limite'          => $limiteUnites,
                            'plan_suivant'    => $planSuivant,
                            'limite_suivante' => $limiteSuivante,
                        ])
                        ->withInput();
                }
            }
        }

        [$immeuble, $nbCreees] = DB::transaction(function () use ($validated, $agencyId, $hasUnites, $mode, $avecRdc, $rdcDifferent) {
            $nombreNiveaux = null;
            if ($hasUnites && $mode === 'etage') {
                $nombreNiveaux = (int) ($validated['nombre_etages'] ?? 0) + ($avecRdc ? 1 : 0);
            } elseif ($hasUnites) {
                $nombreNiveaux = 1;
            }

            $immeuble = Immeuble::create([
                'agency_id'       => $agencyId,
                'proprietaire_id' => $validated['proprietaire_id'],
                'nom'             => $validated['nom'],
                'adresse'         => $validated['adresse'],
                'ville'           => $validated['ville'],
                'nombre_niveaux'  => $nombreNiveaux,
                'description'     => $validated['description'] ?? null,
            ]);

            if (! $hasUnites) {
                return [$immeuble, 0];
            }

            $nom     = $validated['nom'];
            $taux    = (float) ($validated['taux_commission'] ?? 10);
            $charges = (float) ($validated['charges_par_unite'] ?? 0);
            $caution = ($validated['caution_par_unite'] ?? null) ?: null;

            // Construction des unités : [{titre, type, loyer}]
            $biens = [];

            if ($mode === 'etage') {
                $nbEtages        = (int) ($validated['nombre_etages'] ?? 0);
                $unitesParNiveau = (int) ($validated['unites_par_niveau'] ?? 1);

                $niveaux = [];
                if ($avecRdc) $niveaux[] = ['label' => 'RDC', 'rdc' => true];
                for ($i = 1; $i <= $nbEtages; $i++) {
                    $niveaux[] = ['label' => $i === 1 ? '1er étage' : "{$i}ème étage", 'rdc' => false];
                }

                foreach ($niveaux as $niv) {
                    $bType  = ($niv['rdc'] && $rdcDifferent) ? $validated['rdc_type']  : $validated['type_unite'];
                    $bLoyer = ($niv['rdc'] && $rdcDifferent) ? $validated['rdc_loyer'] : $validated['loyer_par_unite'];

                    if ($unitesParNiveau === 1) {
                        $biens[] = ['titre' => $nom . ' — ' . $niv['label'], 'type' => $bType, 'loyer' => $bLoyer];
                    } else {
                        for ($j = 0; $j < $unitesParNiveau; $j++) {
                            $biens[] = [
                                'titre' => $nom . ' — ' . $niv['label'] . ' ' . chr(65 + $j),
                                'type'  => $bType,
                                'loyer' => $bLoyer,
                            ];
                        }
                    }
                }
            } else {
                $nb = (int) ($validated['nombre_unites'] ?? 0);
                for ($i = 1; $i <= $nb; $i++) {
                    $biens[] = [
                        'titre' => $nom . ' — Appt ' . $i,
                        'type'  => $validated['type_unite'],
                        'loyer' => $validated['loyer_par_unite'],
                    ];
                }
            }

            foreach ($biens as $b) {
                Bien::create([
                    'agency_id'       => $agencyId,
                    'immeuble_id'     => $immeuble->id,
                    'proprietaire_id' => $validated['proprietaire_id'],
                    'reference'       => Bien::generateReference($agencyId),
                    'titre'           => $b['titre'],
                    'type'            => $b['type'],
                    'adresse'         => $validated['adresse'],
                    'ville'           => $validated['ville'],
                    'loyer_mensuel'   => $b['loyer'],
                    'charges'         => $charges,
                    'caution'         => $caution,
                    'taux_commission' => $taux,
                    'statut'          => 'disponible',
                ]);
            }

            return [$immeuble, count($biens)];
        });

        return redirect()
            ->route('admin.immeubles.show', $immeuble)
            ->with('success', $nbCreees > 0
                ? "Immeuble créé avec {$nbCreees} appartement(s)."
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
            return redirect()
                ->route('admin.immeubles.show', $immeuble)
                ->with('error', 'Impossible de supprimer cet immeuble : des unités ont un contrat actif.');
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
