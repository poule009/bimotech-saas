<?php

namespace App\Http\Requests;

use App\Models\Bien;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBienRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Bien::class);
    }

    public function rules(): array
    {
        return [
            'proprietaire_id' => ['required', 'exists:users,id'],
            'immeuble_id'     => ['nullable', 'exists:immeubles,id'],
            'titre'           => ['nullable', 'string', 'max:255'],
            'type'            => ['required', Rule::in(array_keys(Bien::TYPES))],
            'adresse'         => ['required', 'string', 'max:255'],
            'quartier'        => ['nullable', 'string', 'max:100'],
            'commune'         => ['nullable', 'string', 'max:100'],
            'ville'           => ['required', 'string', 'max:100'],
            'surface_m2'      => ['nullable', 'numeric', 'min:1'],
            'nombre_pieces'   => ['nullable', 'integer', 'min:1'],
            'nombre_chambres' => ['nullable', 'integer', 'min:0', 'max:99'],
            'nombre_sdb'      => ['nullable', 'integer', 'min:0', 'max:99'],
            'parking'         => ['nullable', 'boolean'],
            'climatise'       => ['nullable', 'boolean'],
            'etage'           => ['nullable', 'integer', 'min:-1', 'max:50'],
            'amenites'        => ['nullable', 'string', 'max:500'],
            'loyer_mensuel'   => ['required', 'numeric', 'min:1000'],
            'taux_commission' => ['nullable', 'numeric', 'min:0', 'max:30'],
            'meuble'          => ['nullable', 'boolean'],
            'description'     => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'proprietaire_id.required' => 'Veuillez sélectionner un propriétaire.',
            'type.required'            => 'Le type de bien est obligatoire.',
            'type.in'                  => 'Le type sélectionné est invalide.',
            'adresse.required'         => "L'adresse est obligatoire.",
            'ville.required'           => 'La ville est obligatoire.',
            'loyer_mensuel.required'   => 'Le loyer est obligatoire.',
            'loyer_mensuel.min'        => "Le loyer doit être d'au moins 1 000 FCFA.",
        ];
    }
}
