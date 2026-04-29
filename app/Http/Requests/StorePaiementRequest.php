<?php

namespace App\Http\Requests;

use App\Models\Contrat;
use App\Models\Paiement;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class StorePaiementRequest extends FormRequest
{
    /**
     * Tout admin d'agence (role = 'admin') ou superadmin peut soumettre ce formulaire.
     * L'ancien check hardcodé sur l'email 'admin@bimotech.sn' était un bug
     * qui bloquait tous les autres admins d'agence avec un 403.
     */
    public function authorize(): bool
    {
        return Gate::allows('isAdmin');
    }

    public function rules(): array
    {
        return [
            'contrat_id'           => [
                'required', 'exists:contrats,id',
                function ($attribute, $value, $fail) {
                    $contrat = Contrat::withoutGlobalScopes()->find($value);
                    if (! $contrat) {
                        $fail('Ce contrat est introuvable.');
                        return;
                    }
                    if ((float)($contrat->loyer_nu ?? 0) <= 0) {
                        $fail('Le contrat sélectionné a un loyer nu à zéro. Corrigez le contrat avant d\'enregistrer un paiement.');
                    }
                    if ($contrat->statut !== 'actif') {
                        $fail('Seul un contrat actif peut recevoir un paiement.');
                    }
                },
            ],
            'periode'              => [
                'required',
                'date',  // accepte Y-m, Y-m-d et ISO (formulaire + tests)
                // ── Règle anti-doublon ──────────────────────────────────
                function ($attribute, $value, $fail) {
                    $contratId   = $this->input('contrat_id');
                    $periodeDate = Carbon::parse($value)->startOfMonth();

                    $existe = Paiement::where('contrat_id', $contratId)
                        ->whereYear('periode',  $periodeDate->year)
                        ->whereMonth('periode', $periodeDate->month)
                        ->where('statut', '!=', 'annule')
                        ->exists();

                    if ($existe) {
                        $fail("Un paiement validé existe déjà pour {$periodeDate->translatedFormat('F Y')} sur ce contrat.");
                    }
                },
            ],
            // montant_encaisse est calculé par FiscalService côté serveur — non soumis par le formulaire
            'mode_paiement'        => ['required', 'in:' . implode(',', array_keys(\App\Http\Controllers\PaiementController::MODES_PAIEMENT))],
            'caution_percue'       => ['nullable', 'numeric', 'min:0'],
            'date_paiement'        => ['required', 'date', 'before_or_equal:today'],
            'notes'                => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'montant_encaisse.gt'  => 'Le montant doit être supérieur à zéro.',
            'montant_encaisse.max' => 'Le montant ne peut pas dépasser 999 999 999 FCFA.',
            'periode.date_format'  => 'Format attendu : AAAA-MM (ex: 2025-01).',
            'contrat_id.exists'    => 'Ce contrat n\'existe pas.',
        ];
    }
}