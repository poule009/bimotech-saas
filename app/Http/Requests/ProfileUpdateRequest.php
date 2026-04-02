<?php

namespace App\Http\Requests;

use App\Models\User;
// use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * SÉCURITÉ — Ce FormRequest ne valide QUE name et email.
     *
     * ProfileController::update() fait $user->fill($request->validated())
     * puis $user->save(). Comme $fillable ne contient plus `role` ni
     * `agency_id`, un attaquant qui injecterait ces champs dans la requête
     * ne pourrait pas les modifier — mais par défense en profondeur,
     * on ne valide ici que ce qu'on veut réellement modifier.
     *
     * NE PAS ajouter role, agency_id, password ici.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'  => 'Le nom est obligatoire.',
            'email.required' => "L'adresse email est obligatoire.",
            'email.unique'   => 'Cet email est déjà utilisé par un autre compte.',
            'email.email'    => "L'adresse email n'est pas valide.",
        ];
    }
}