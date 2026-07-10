<?php

namespace App\Http\Controllers;

use App\Models\Bien;
use App\Models\DepenseGestion;
use App\Models\Paiement;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DepenseGestionController extends Controller
{
    /**
     * Ajout d'une dépense pour le compte d'un propriétaire (module Comptabilité).
     *
     * Fourche :
     *   - « liée à un bien »  → rattachée au dernier paiement du bien
     *                            (déduite via net_final_bailleur, mécanisme existant).
     *   - « directe »          → rattachée au propriétaire (paiement_id null).
     *
     * Justificatif OBLIGATOIRE : c'est de l'argent d'un tiers qui est débité.
     */
    public function storeForProprietaire(Request $request, User $proprietaire): RedirectResponse
    {
        $this->authorize('isAdmin');

        $agencyId = Auth::user()->agency_id;
        abort_unless($proprietaire->agency_id === $agencyId && $proprietaire->role === 'proprietaire', 404);

        $data = $request->validate([
            'type'         => ['required', 'in:bien,direct'],
            'bien_id'      => ['required_if:type,bien', 'nullable', 'integer'],
            'libelle'      => ['required', 'string', 'max:255'],
            'montant'      => ['required', 'numeric', 'min:1', 'max:99999999'],
            'categorie'    => ['required', 'in:' . implode(',', array_keys(DepenseGestion::CATEGORIES))],
            'date_depense' => ['required', 'date', 'before_or_equal:today'],
            'prestataire'  => ['nullable', 'string', 'max:255'],
            'notes'        => ['nullable', 'string', 'max:1000'],
            'justificatif' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:5120'],
        ], [
            'justificatif.required' => 'Le justificatif est obligatoire pour toute dépense déduite de l\'argent d\'un propriétaire.',
        ]);

        $commun = [
            'agency_id'         => $agencyId,
            'libelle'           => $data['libelle'],
            'montant'           => $data['montant'],
            'categorie'         => $data['categorie'],
            'date_depense'      => $data['date_depense'],
            'prestataire'       => $data['prestataire'] ?? null,
            'notes'             => $data['notes'] ?? null,
            'justificatif_path' => $request->file('justificatif')->store('justificatifs/depenses', 'public'),
        ];

        if ($data['type'] === 'bien') {
            // Le bien doit appartenir au propriétaire et à l'agence
            $bien = Bien::where('id', $data['bien_id'])
                ->where('agency_id', $agencyId)
                ->where('proprietaire_id', $proprietaire->id)
                ->first();
            abort_unless($bien, 422);

            // Dernier paiement encaissé du bien → la dépense s'y rattache
            $paiement = Paiement::withoutGlobalScopes()
                ->where('agency_id', $agencyId)
                ->where('statut', 'valide')
                ->whereHas('contrat', fn($q) => $q->where('bien_id', $bien->id))
                ->orderByDesc('date_paiement')
                ->first();

            if (! $paiement) {
                Storage::disk('public')->delete($commun['justificatif_path']);
                return back()
                    ->withInput()
                    ->with('error', 'Ce bien n\'a aucun loyer encaissé : impossible d\'y imputer une dépense. Utilisez « directement au propriétaire ».');
            }

            DepenseGestion::create($commun + ['paiement_id' => $paiement->id]);
        } else {
            DepenseGestion::create($commun + ['proprietaire_id' => $proprietaire->id]);
        }

        return redirect()
            ->route('admin.reversements.compte-mandant', $proprietaire)
            ->with('success', 'Dépense enregistrée.');
    }

    /**
     * Suppression d'une dépense (retire aussi le justificatif du disque).
     */
    public function destroyDepense(DepenseGestion $depense): RedirectResponse
    {
        $this->authorize('isAdmin');
        abort_unless($depense->agency_id === Auth::user()->agency_id, 403);

        if ($depense->justificatif_path) {
            Storage::disk('public')->delete($depense->justificatif_path);
        }

        $proprietaireId = $depense->proprietaire_id
            ?? $depense->paiement?->contrat?->bien?->proprietaire_id;

        $depense->delete();

        return $proprietaireId
            ? redirect()->route('admin.reversements.compte-mandant', $proprietaireId)->with('success', 'Dépense supprimée.')
            : back()->with('success', 'Dépense supprimée.');
    }
}
