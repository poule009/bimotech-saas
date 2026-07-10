<?php

namespace App\Http\Requests;

use App\Models\ChargeAgence;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class StoreChargeAgenceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('isAdmin');
    }

    public function rules(): array
    {
        return [
            'libelle'     => ['required', 'string', 'max:255'],
            'montant'     => ['required', 'numeric', 'min:0'],
            'categorie'   => ['required', 'in:' . implode(',', array_keys(ChargeAgence::CATEGORIES))],
            'recurrente'  => ['sometimes', 'boolean'],
            'date_charge' => ['required', 'date'],
            'prestataire' => ['nullable', 'string', 'max:255'],
            'notes'       => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'libelle.required'     => 'Le libellé est obligatoire.',
            'montant.required'     => 'Le montant est obligatoire.',
            'montant.min'          => 'Le montant doit être positif.',
            'categorie.required'   => 'La catégorie est obligatoire.',
            'date_charge.required' => 'La date est obligatoire.',
        ];
    }
}
