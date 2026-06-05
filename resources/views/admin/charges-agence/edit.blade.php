@extends('layouts.app')
@section('header', 'Modifier charge')

@section('content')
<div class="max-w-2xl mx-auto">
    <form method="POST" action="{{ route('admin.charges-agence.update', $charge) }}" class="space-y-6">
        @csrf @method('PUT')
        <div class="flex items-center gap-3 mb-2">
            <a href="{{ route('admin.charges-agence.index') }}" class="text-bimo-text/40 hover:text-bimo-text transition-colors duration-150">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
            </a>
            <h1 class="font-display font-extrabold text-xl text-bimo-text tracking-tight">Modifier la charge</h1>
        </div>

        <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-5 space-y-4">

            <div class="space-y-1.5">
                <label for="libelle" class="block font-body font-medium text-sm text-bimo-text">Libellé <span class="text-bimo-red">*</span></label>
                <input id="libelle" name="libelle" type="text" value="{{ old('libelle', $charge->libelle) }}"
                       class="w-full px-4 py-3 rounded-[10px] bg-white border font-body text-sm text-bimo-text placeholder:text-bimo-text/35 focus:outline-none focus:ring-2 transition-all duration-150 @error('libelle') border-bimo-red focus:border-bimo-red focus:ring-bimo-red/15 @else border-bimo-navy/20 focus:border-bimo-gold focus:ring-bimo-gold/15 @enderror">
                @error('libelle')<p class="font-body text-xs text-bimo-red">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label for="montant" class="block font-body font-medium text-sm text-bimo-text">Montant (FCFA) <span class="text-bimo-red">*</span></label>
                    <input id="montant" name="montant" type="number" min="0" step="1" value="{{ old('montant', $charge->montant) }}"
                           class="w-full px-4 py-3 rounded-[10px] bg-white border font-body text-sm text-bimo-text focus:outline-none focus:ring-2 transition-all duration-150 @error('montant') border-bimo-red focus:border-bimo-red focus:ring-bimo-red/15 @else border-bimo-navy/20 focus:border-bimo-gold focus:ring-bimo-gold/15 @enderror">
                    @error('montant')<p class="font-body text-xs text-bimo-red">{{ $message }}</p>@enderror
                </div>
                <div class="space-y-1.5">
                    <label for="date_charge" class="block font-body font-medium text-sm text-bimo-text">Date <span class="text-bimo-red">*</span></label>
                    <input id="date_charge" name="date_charge" type="date" value="{{ old('date_charge', $charge->date_charge->format('Y-m-d')) }}"
                           class="w-full px-4 py-3 rounded-[10px] bg-white border font-body text-sm text-bimo-text focus:outline-none focus:ring-2 transition-all duration-150 border-bimo-navy/20 focus:border-bimo-gold focus:ring-bimo-gold/15">
                </div>
            </div>

            <div class="space-y-1.5">
                <label for="categorie" class="block font-body font-medium text-sm text-bimo-text">Catégorie <span class="text-bimo-red">*</span></label>
                <select id="categorie" name="categorie" class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150 appearance-none cursor-pointer">
                    @foreach(\App\Models\ChargeAgence::CATEGORIES as $key => $label)
                    <option value="{{ $key }}" {{ old('categorie', $charge->categorie) === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="space-y-1.5">
                <label for="prestataire" class="block font-body font-medium text-sm text-bimo-text">Prestataire <span class="font-light text-bimo-text/40 ml-1">(optionnel)</span></label>
                <input id="prestataire" name="prestataire" type="text" value="{{ old('prestataire', $charge->prestataire) }}"
                       class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
            </div>

            <div class="space-y-1.5">
                <label for="notes" class="block font-body font-medium text-sm text-bimo-text">Notes</label>
                <textarea id="notes" name="notes" rows="3"
                          class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150 resize-none">{{ old('notes', $charge->notes) }}</textarea>
            </div>
        </div>

        <div class="sticky bottom-0 bg-white/95 backdrop-blur-sm border-t border-bimo-navy/10 px-0 py-3 flex justify-end gap-3">
            <a href="{{ route('admin.charges-agence.index') }}"
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
