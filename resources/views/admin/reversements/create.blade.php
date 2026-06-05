@extends('layouts.app')
@section('header', 'Nouveau reversement')

@section('content')
<div class="max-w-2xl mx-auto">
    <form method="POST" action="{{ route('admin.reversements.store') }}" class="space-y-6">
        @csrf
        <div class="flex items-center gap-3 mb-2">
            <a href="{{ route('admin.reversements.index') }}" class="text-bimo-text/40 hover:text-bimo-text transition-colors duration-150">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
            </a>
            <h1 class="font-display font-extrabold text-xl text-bimo-text tracking-tight">Nouveau reversement</h1>
        </div>

        @if($soldeMandant !== null)
        <div class="bg-bimo-gold/[8%] border border-bimo-gold/25 rounded-[14px] p-4 flex items-center justify-between">
            <div>
                <p class="font-body font-medium text-sm text-bimo-text">Solde dû à {{ $proprietaireSelectionne?->name }}</p>
                <p class="font-body text-xs text-bimo-text/50 mt-0.5">Net à reverser calculé</p>
            </div>
            <p class="font-display font-extrabold text-xl text-bimo-gold">{{ number_format($soldeMandant, 0, ',', ' ') }} F</p>
        </div>
        @endif

        <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-5 space-y-4">

            <div class="space-y-1.5">
                <label for="proprietaire_id" class="block font-body font-medium text-sm text-bimo-text">Propriétaire <span class="text-bimo-red">*</span></label>
                <select id="proprietaire_id" name="proprietaire_id" onchange="this.form.submit()"
                        class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150 appearance-none cursor-pointer @error('proprietaire_id') border-bimo-red @enderror">
                    <option value="">Sélectionner un propriétaire</option>
                    @foreach($proprietaires as $prop)
                    <option value="{{ $prop->id }}" {{ old('proprietaire_id', $proprietaireSelectionne?->id) == $prop->id ? 'selected' : '' }}>{{ $prop->name }}</option>
                    @endforeach
                </select>
                @error('proprietaire_id')<p class="font-body text-xs text-bimo-red">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label for="montant" class="block font-body font-medium text-sm text-bimo-text">Montant (FCFA) <span class="text-bimo-red">*</span></label>
                    <input id="montant" name="montant" type="number" min="1" step="1"
                           value="{{ old('montant', $soldeMandant > 0 ? intval($soldeMandant) : '') }}"
                           class="w-full px-4 py-3 rounded-[10px] bg-white border font-body text-sm text-bimo-text focus:outline-none focus:ring-2 transition-all duration-150 @error('montant') border-bimo-red focus:border-bimo-red focus:ring-bimo-red/15 @else border-bimo-navy/20 focus:border-bimo-gold focus:ring-bimo-gold/15 @enderror">
                    @error('montant')<p class="font-body text-xs text-bimo-red">{{ $message }}</p>@enderror
                </div>
                <div class="space-y-1.5">
                    <label for="date_reversement" class="block font-body font-medium text-sm text-bimo-text">Date <span class="text-bimo-red">*</span></label>
                    <input id="date_reversement" name="date_reversement" type="date" value="{{ old('date_reversement', now()->format('Y-m-d')) }}"
                           class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                </div>
            </div>

            <div class="space-y-1.5">
                <label for="mode_paiement" class="block font-body font-medium text-sm text-bimo-text">Mode de paiement <span class="text-bimo-red">*</span></label>
                <select id="mode_paiement" name="mode_paiement" class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150 appearance-none cursor-pointer @error('mode_paiement') border-bimo-red @enderror">
                    <option value="">Sélectionner</option>
                    @foreach(\App\Models\ReversementProprietaire::MODES_PAIEMENT as $key => $label)
                    <option value="{{ $key }}" {{ old('mode_paiement') === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @error('mode_paiement')<p class="font-body text-xs text-bimo-red">{{ $message }}</p>@enderror
            </div>

            <div class="space-y-1.5">
                <label for="reference" class="block font-body font-medium text-sm text-bimo-text">Référence <span class="font-light text-bimo-text/40 ml-1">(optionnel)</span></label>
                <input id="reference" name="reference" type="text" value="{{ old('reference') }}" placeholder="N° virement, ref Wave..."
                       class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text placeholder:text-bimo-text/35 focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label for="periode_debut" class="block font-body font-medium text-sm text-bimo-text">Période du <span class="font-light text-bimo-text/40 ml-1">(optionnel)</span></label>
                    <input id="periode_debut" name="periode_debut" type="month" value="{{ old('periode_debut') }}"
                           class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                </div>
                <div class="space-y-1.5">
                    <label for="periode_fin" class="block font-body font-medium text-sm text-bimo-text">Au</label>
                    <input id="periode_fin" name="periode_fin" type="month" value="{{ old('periode_fin') }}"
                           class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                </div>
            </div>

            <div class="space-y-1.5">
                <label for="notes" class="block font-body font-medium text-sm text-bimo-text">Notes <span class="font-light text-bimo-text/40 ml-1">(optionnel)</span></label>
                <textarea id="notes" name="notes" rows="2"
                          class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text placeholder:text-bimo-text/35 focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150 resize-none">{{ old('notes') }}</textarea>
            </div>
        </div>

        <div class="sticky bottom-0 bg-white/95 backdrop-blur-sm border-t border-bimo-navy/10 px-0 py-3 flex justify-end gap-3">
            <a href="{{ route('admin.reversements.index') }}"
               class="inline-flex items-center px-5 py-2.5 rounded-[10px] border border-bimo-navy/15 font-body text-sm text-bimo-text/60 hover:text-bimo-text hover:border-bimo-navy/30 transition-all duration-150">
                Annuler
            </a>
            <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-[10px] bg-[var(--ac)] text-white font-display font-bold text-sm hover:opacity-90 transition-opacity duration-150">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                Enregistrer
            </button>
        </div>
    </form>
</div>
@endsection
