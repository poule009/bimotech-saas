<?php

namespace App\Http\Requests;

use App\Models\ReversementProprietaire;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class StoreReversementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('isAdmin');
    }

    public function rules(): array
    {
        return [
            'proprietaire_id'  => [
                'required',
                'exists:users,id',
                function ($attr, $value, $fail) {
                    $user = \App\Models\User::find($value);
                    if (! $user || $user->agency_id !== Auth::user()->agency_id) {
                        $fail('Ce propriétaire n\'appartient pas à votre agence.');
                    }
                    if ($user && $user->role !== 'proprietaire') {
                        $fail('L\'utilisateur sélectionné n\'est pas un propriétaire.');
                    }
                },
            ],
            'montant'          => ['required', 'numeric', 'min:1'],
            'date_reversement' => ['required', 'date'],
            'mode_paiement'    => ['required', 'in:' . implode(',', array_keys(ReversementProprietaire::MODES_PAIEMENT))],
            'reference'        => ['nullable', 'string', 'max:255'],
            'periode_debut'    => ['nullable', 'string', 'regex:/^\d{4}-\d{2}$/', 'required_with:periode_fin'],
            'periode_fin'      => [
                'nullable', 'string', 'regex:/^\d{4}-\d{2}$/', 'required_with:periode_debut',
                function ($attr, $value, $fail) {
                    if ($value && $this->input('periode_debut') && $value < $this->input('periode_debut')) {
                        $fail('La période de fin doit être postérieure ou égale à la période de début.');
                    }
                },
            ],
            'notes'            => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'proprietaire_id.required'  => 'Sélectionnez un propriétaire.',
            'montant.required'          => 'Le montant est obligatoire.',
            'montant.min'               => 'Le montant doit être supérieur à 0.',
            'date_reversement.required' => 'La date de reversement est obligatoire.',
            'mode_paiement.required'    => 'Le mode de paiement est obligatoire.',
        ];
    }
}
