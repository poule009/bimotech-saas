<?php

namespace App\Http\Requests;

use App\Models\Contrat;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

/**
 * UpdateContratRequest — Validation de la mise à jour d'un contrat.
 *
 * Extrait de ContratController::update() pour respecter le SRP.
 * Le contrat cible est injecté par route model binding ; authorize()
 * délègue à ContratPolicy::update() pour vérifier l'ownership.
 */
class UpdateContratRequest extends FormRequest
{
    public function authorize(): bool
    {
        $contrat = $this->route('contrat');
        return $contrat instanceof Contrat && $this->user()->can('update', $contrat);
    }

    public function rules(): array
    {
        $agencyId = Auth::id() ? Auth::user()->agency_id : null;

        return [
            'date_fin'            => ['nullable', 'date', 'after:date_debut'],
            'loyer_nu'            => ['required', 'numeric', 'min:1'],
            'charges_mensuelles'  => ['nullable', 'numeric', 'min:0'],
            'tom_amount'          => ['nullable', 'numeric', 'min:0'],
            'caution'             => ['required', 'numeric', 'min:0'],
            'type_bail'           => ['required', 'in:habitation,commercial,mixte,saisonnier'],
            'frais_agence'        => ['nullable', 'numeric', 'min:0'],
            'indexation_annuelle' => ['nullable', 'numeric', 'min:0', 'max:20'],
            'nombre_mois_caution' => ['nullable', 'integer', 'min:1', 'max:6'],
            'garant_nom'          => ['nullable', 'string', 'max:150'],
            'garant_telephone'    => ['nullable', 'string', 'max:20'],
            'garant_adresse'      => ['nullable', 'string', 'max:255'],
            'reference_bail'      => [
                'nullable', 'string', 'max:60',
                // Unicité par agence, en ignorant le contrat en cours de modification
                Rule::unique('contrats', 'reference_bail')
                    ->where('agency_id', $agencyId)
                    ->whereNull('deleted_at')
                    ->ignore($this->route('contrat')?->id),
            ],
            'observations'          => ['nullable', 'string', 'max:1000'],
            'clauses_particulieres' => ['nullable', 'string', 'max:5000'],
            'locataire_id'        => [
                'nullable',
                'exists:users,id',
                function ($attr, $value, $fail) use ($agencyId) {
                    if (empty($value)) {
                        return;
                    }
                    $loc = User::find($value);
                    if (! $loc || $loc->agency_id !== $agencyId || $loc->role !== 'locataire') {
                        $fail('Ce locataire n\'appartient pas à votre agence.');
                    }
                },
            ],
            // ── Fiscal ────────────────────────────────────────────────────────
            'loyer_assujetti_tva'      => [
                'nullable', 'boolean',
                function ($attr, $value, $fail) {
                    if ($value === false || $value === '0' || $value === 0) {
                        $type = $this->input('type_bail');
                        if (in_array($type, ['commercial', 'mixte'], true)) {
                            $fail('Un bail commercial ou mixte est obligatoirement assujetti à la TVA (Art. 355 CGI SN). Vous ne pouvez pas le désactiver.');
                        }
                    }
                },
            ],
            'taux_tva_loyer'           => ['nullable', 'numeric', 'min:0', 'max:20'],
            'brs_applicable'           => ['nullable', 'boolean'],
            'taux_brs_manuel'          => ['nullable', 'numeric', 'min:0', 'max:20'],
            'charges_assujetties_tva'  => ['nullable', 'boolean'],
            // ── DGID ──────────────────────────────────────────────────────────
            'date_enregistrement_dgid' => ['nullable', 'date'],
            'numero_quittance_dgid'    => ['nullable', 'string', 'max:60'],
            'montant_droit_de_bail'    => ['nullable', 'numeric', 'min:0'],
            'enregistrement_exonere'   => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'date_fin.after'     => 'La date de fin doit être postérieure à la date de début.',
            'loyer_nu.required'  => 'Le loyer nu est obligatoire.',
            'loyer_nu.min'       => 'Le loyer nu doit être supérieur à 0.',
            'caution.required'   => 'Le montant de la caution est obligatoire.',
            'type_bail.required' => 'Veuillez choisir un type de bail.',
            'type_bail.in'       => 'Le type de bail sélectionné est invalide.',
        ];
    }
}
