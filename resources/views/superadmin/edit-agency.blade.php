@extends('layouts.app')
@section('header', 'Modifier l\'agence')

@section('content')
<div class="max-w-2xl">

    <nav class="flex items-center gap-2 font-body text-sm text-bimo-text/40 mb-5">
        <a href="{{ route('superadmin.dashboard') }}" class="hover:text-bimo-text transition-colors duration-150">Agences</a>
        <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
        <a href="{{ route('superadmin.agencies.show', $agency) }}" class="hover:text-bimo-text transition-colors duration-150">{{ $agency->name }}</a>
        <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
        <span class="text-bimo-text font-semibold">Modifier</span>
    </nav>

    <form method="POST" action="{{ route('superadmin.agencies.update', $agency) }}" class="space-y-4">
        @csrf @method('PATCH')

        <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
            <div class="flex items-center gap-3 px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
                <div class="w-8 h-8 rounded-[8px] bg-bimo-gold/15 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-bimo-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                </div>
                <span class="font-display font-bold text-sm text-bimo-text">Informations de l'agence</span>
            </div>
            <div class="px-5 py-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="block font-body font-medium text-sm text-bimo-text" for="name">Nom de l'agence <span class="text-bimo-red">*</span></label>
                        <input type="text" id="name" name="name" value="{{ old('name', $agency->name) }}" required
                               class="w-full px-4 py-3 rounded-[10px] bg-white border font-body text-sm text-bimo-text focus:outline-none focus:ring-2 transition-all duration-150 @error('name') border-bimo-red focus:border-bimo-red focus:ring-bimo-red/15 @else border-bimo-navy/20 focus:border-bimo-gold focus:ring-bimo-gold/15 @enderror">
                        @error('name')<p class="font-body text-xs text-bimo-red">{{ $message }}</p>@enderror
                    </div>
                    <div class="space-y-1.5">
                        <label class="block font-body font-medium text-sm text-bimo-text" for="email">Email <span class="text-bimo-red">*</span></label>
                        <input type="email" id="email" name="email" value="{{ old('email', $agency->email) }}" required
                               class="w-full px-4 py-3 rounded-[10px] bg-white border font-body text-sm text-bimo-text focus:outline-none focus:ring-2 transition-all duration-150 @error('email') border-bimo-red focus:border-bimo-red focus:ring-bimo-red/15 @else border-bimo-navy/20 focus:border-bimo-gold focus:ring-bimo-gold/15 @enderror">
                        @error('email')<p class="font-body text-xs text-bimo-red">{{ $message }}</p>@enderror
                    </div>
                    <div class="space-y-1.5">
                        <label class="block font-body font-medium text-sm text-bimo-text" for="telephone">Téléphone <span class="font-light text-bimo-text/40">(optionnel)</span></label>
                        <input type="text" id="telephone" name="telephone" value="{{ old('telephone', $agency->telephone) }}" placeholder="+221 77 000 00 00"
                               class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text placeholder:text-bimo-text/35 focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block font-body font-medium text-sm text-bimo-text" for="adresse">Adresse <span class="font-light text-bimo-text/40">(optionnel)</span></label>
                        <input type="text" id="adresse" name="adresse" value="{{ old('adresse', $agency->adresse) }}" placeholder="Rue 10, Dakar"
                               class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text placeholder:text-bimo-text/35 focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block font-body font-medium text-sm text-bimo-text" for="taux_tva">Taux TVA (%) <span class="font-light text-bimo-text/40">(optionnel)</span></label>
                        <input type="number" id="taux_tva" name="taux_tva" min="0" max="100" step="0.01" value="{{ old('taux_tva', $agency->taux_tva) }}" placeholder="18"
                               class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text placeholder:text-bimo-text/35 focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                        @error('taux_tva')<p class="font-body text-xs text-bimo-red">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div class="mt-4 flex items-start gap-2 bg-bimo-gold/[6%] border border-bimo-gold/20 rounded-[8px] px-4 py-3">
                    <svg class="w-4 h-4 text-bimo-gold flex-shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                    <p class="font-body text-xs text-bimo-gold/80">Le slug, la couleur, le logo et la signature ne sont modifiables que par l'admin de l'agence depuis ses paramètres.</p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-[var(--ac)] text-white font-display font-bold text-sm rounded-[10px] hover:opacity-90 transition-opacity duration-150">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                Enregistrer les modifications
            </button>
            <a href="{{ route('superadmin.agencies.show', $agency) }}"
               class="inline-flex items-center px-5 py-2.5 rounded-[10px] border border-bimo-navy/15 font-body text-sm text-bimo-text/60 hover:text-bimo-text hover:border-bimo-navy/30 transition-all duration-150">
                Annuler
            </a>
        </div>

    </form>
</div>
@endsection
