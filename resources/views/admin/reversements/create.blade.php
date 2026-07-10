@extends('layouts.app')

@php $fmt = fn ($n) => number_format((float) $n, 0, ',', ' '); @endphp

@section('title', 'Nouveau reversement')
@section('page-title', 'Enregistrer un reversement')
@section('page-subtitle')
    <a href="{{ route('admin.comptabilite.index') }}" class="text-teal font-semibold hover:underline">Comptabilité</a>
    <span class="text-muted"> / Nouveau reversement</span>
@endsection

@section('content')
<div class="max-w-[560px]">

    @if($errors->any())
        <div class="mb-5 rounded-lg bg-error/10 border border-error/25 px-4 py-3 text-[13px] text-error">
            @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
        </div>
    @endif

    <div class="f-card">
        <form method="POST" action="{{ route('admin.reversements.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="f-label" for="proprietaire_id">Propriétaire</label>
                <select class="f-select" id="proprietaire_id" name="proprietaire_id" required>
                    <option value="">Sélectionner…</option>
                    @foreach($proprietaires as $p)
                        <option value="{{ $p->id }}" @selected(optional($proprietaireSelectionne)->id === $p->id)>{{ $p->name }}</option>
                    @endforeach
                </select>
                @if($soldeMandant !== null)
                    <p class="text-[12px] text-muted mt-1.5">Solde en cours : <strong class="text-ink">{{ $fmt($soldeMandant) }} F</strong></p>
                @endif
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="f-label" for="montant">Montant (FCFA)</label>
                    <input class="f-input" id="montant" name="montant" type="number" min="1" step="1" value="{{ old('montant', $soldeMandant ? (int) $soldeMandant : '') }}" required>
                </div>
                <div>
                    <label class="f-label" for="date_reversement">Date</label>
                    <input class="f-input" id="date_reversement" name="date_reversement" type="date" value="{{ now()->toDateString() }}" required>
                </div>
            </div>
            <div>
                <label class="f-label" for="mode_paiement">Mode de paiement</label>
                <select class="f-select" id="mode_paiement" name="mode_paiement" required>
                    @foreach(\App\Models\ReversementProprietaire::MODES_PAIEMENT as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="f-label" for="reference">Référence (facultatif)</label>
                <input class="f-input" id="reference" name="reference" type="text" placeholder="N° de transfert">
            </div>
            <button type="submit" class="btn-primary w-full">Enregistrer le reversement</button>
        </form>
    </div>

</div>
@endsection
