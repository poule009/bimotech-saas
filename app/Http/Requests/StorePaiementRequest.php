<?php

namespace App\Http\Requests;

use App\Models\Paiement;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StorePaiementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check()
            && Auth::user()->role === 'admin'
            && Auth::user()->email === 'admin@bimotech.sn';
    }

    public function rules(): array
    {
        return [
            'contrat_id'           => ['required', 'exists:contrats,id'],
            'periode'              => [
                'required',
                'date_format:Y-m',
                // ── Règle anti-doublon ──────────────────────────────────
                function ($attribute, $value, $fail) {
                    $contratId   = $this->input('contrat_id');
                    $periodeDate = Carbon::createFromFormat('Y-m', $value)->startOfMonth();

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
            'montant_encaisse'     => ['required', 'numeric', 'gt:0'],
            'mode_paiement'        => ['required', 'in:especes,virement,mobile_money,cheque'],
            'caution_percue'       => ['nullable', 'numeric', 'min:0'],
            'est_premier_paiement' => ['boolean'],
            'date_paiement'        => ['required', 'date', 'before_or_equal:today'],
            'notes'                => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'montant_encaisse.gt'  => 'Le montant doit être supérieur à zéro.',
            'periode.date_format'  => 'Format attendu : AAAA-MM (ex: 2025-01).',
            'contrat_id.exists'    => 'Ce contrat n\'existe pas.',
        ];
    }
}