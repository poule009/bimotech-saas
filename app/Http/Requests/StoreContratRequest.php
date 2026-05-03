<?php

namespace App\Http\Requests;

use App\Models\Bien;
use App\Models\Contrat;
use App\Models\User;
use App\Services\FiscalService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

/**
 * StoreContratRequest — Validation de la création d'un contrat.
 *
 * Regroupe toutes les règles métier et les messages d'erreur qui étaient
 * précédemment dispersés dans ContratController::store(), conformément
 * au principe de responsabilité unique (SRP).
 */
class StoreContratRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Contrat::class);
    }

    public function rules(): array
    {
        $agencyId = Auth::id() ? Auth::user()->agency_id : null;

        return [
            'bien_id' => [
                'required',
                'exists:biens,id',
                function ($attr, $value, $fail) use ($agencyId) {
                    $bien = Bien::withoutGlobalScopes()->find($value);
                    if (! $bien || $bien->agency_id !== $agencyId) {
                        $fail('Ce bien n\'appartient pas à votre agence.');
                    }
                },
            ],
            'locataire_id' => [
                'required',
                'exists:users,id',
                function ($attr, $value, $fail) use ($agencyId) {
                    $loc = User::find($value);
                    if (! $loc || $loc->agency_id !== $agencyId || $loc->role !== 'locataire') {
                        $fail('Ce locataire n\'appartient pas à votre agence.');
                        return;
                    }
                    if (\App\Models\Contrat::where('locataire_id', $value)->where('statut', 'actif')->exists()) {
                        $fail('Ce locataire a déjà un contrat actif. Un locataire ne peut avoir qu\'un seul contrat actif à la fois.');
                    }
                },
            ],
            'date_debut'          => ['required', 'date'],
            'date_fin'            => ['nullable', 'date', 'after:date_debut'],
            'loyer_nu'            => [
                'required', 'numeric', 'min:1',
                function ($attr, $value, $fail) {
                    $bien = Bien::withoutGlobalScopes()->find($this->input('bien_id'));
                    $surface = $bien?->surface_m2;
                    $check = FiscalService::verifierLoi8118((float) $value, $surface ? (int) $surface : null);
                    if (!$check['conforme'] && $check['plafond']) {
                        $fail("Loyer {$value} F dépasse le plafond Loi 81-18 ({$check['plafond']} F pour {$surface} m²). Vérifiez avant de continuer.");
                    }
                },
            ],
            'charges_mensuelles'  => ['nullable', 'numeric', 'min:0'],
            'tom_amount'          => ['nullable', 'numeric', 'min:0'],
            'avec_caution'        => ['nullable', 'boolean'],
            'caution'             => ['nullable', 'numeric', 'min:0'],
            'type_bail'           => ['required', 'in:habitation,commercial,mixte,saisonnier'],
            'frais_agence'        => ['nullable', 'numeric', 'min:0'],
            'indexation_annuelle' => ['nullable', 'numeric', 'min:0', 'max:20'],
            'nombre_mois_caution' => ['nullable', 'integer', 'min:1', 'max:6'],
            'garant_nom'          => ['nullable', 'string', 'max:150'],
            'garant_telephone'    => ['nullable', 'string', 'max:20'],
            'garant_adresse'      => ['nullable', 'string', 'max:255'],
            'garant_cni'          => ['nullable', 'string', 'max:30'],
            'reference_bail'      => [
                'nullable', 'string', 'max:60',
                // Unicité par agence : deux contrats ne peuvent pas avoir la même référence
                Rule::unique('contrats', 'reference_bail')
                    ->where('agency_id', $agencyId)
                    ->whereNull('deleted_at'),
            ],
            'observations'          => ['nullable', 'string', 'max:1000'],
            'clauses_particulieres' => ['nullable', 'string', 'max:5000'],
            // ── Fiscal ────────────────────────────────────────────────────────
            // M1 : bail commercial/mixte → TVA loyer obligatoirement vraie (Art. 355 CGI SN)
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
            'bien_id.required'      => 'Veuillez sélectionner un bien.',
            'locataire_id.required' => 'Veuillez sélectionner un locataire.',
            'date_debut.required'   => 'La date de début est obligatoire.',
            'date_fin.after'        => 'La date de fin doit être postérieure à la date de début.',
            'loyer_nu.required'     => 'Le loyer nu est obligatoire.',
            'loyer_nu.min'          => 'Le loyer nu doit être supérieur à 0.',
            'caution.required'      => 'Le montant de la caution est obligatoire.',
            'type_bail.required'    => 'Veuillez choisir un type de bail.',
            'type_bail.in'          => 'Le type de bail sélectionné est invalide.',
        ];
    }
}
