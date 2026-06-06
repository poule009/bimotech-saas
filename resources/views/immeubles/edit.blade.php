@extends('layouts.app')
@section('header', 'Immeubles › ' . $immeuble->nom . ' › Modifier')

@section('content')

<div class="flex items-center gap-2 font-body text-sm text-bimo-text/40 mb-5">
    <a href="{{ route('admin.immeubles.index') }}" class="hover:text-bimo-text transition-colors duration-150">Immeubles</a>
    <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
    <a href="{{ route('admin.immeubles.show', $immeuble) }}" class="hover:text-bimo-text transition-colors duration-150">{{ $immeuble->nom }}</a>
    <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
    <span class="text-bimo-text font-medium">Modifier</span>
</div>

<div class="mb-5">
    <h1 class="font-display font-extrabold text-xl md:text-2xl text-bimo-text tracking-tight">Modifier l'immeuble</h1>
    <p class="font-body text-sm text-bimo-text/50 mt-1">{{ $immeuble->nom }}</p>
</div>

<form method="POST" action="{{ route('admin.immeubles.update', $immeuble) }}">
@csrf @method('PATCH')

<div class="grid grid-cols-1 lg:grid-cols-[1fr_300px] gap-5 items-start">

    {{-- Colonne gauche --}}
    <div class="space-y-4">

        {{-- Propriétaire --}}
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
            <div class="flex items-center gap-3 px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
                <div class="w-8 h-8 rounded-[8px] bg-bimo-gold/15 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-bimo-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                </div>
                <span class="font-display font-bold text-sm text-bimo-text">Propriétaire</span>
            </div>
            <div class="px-5 py-5">
                <div class="space-y-1.5">
                    <label class="block font-body font-medium text-sm text-bimo-text">Propriétaire <span class="text-bimo-red">*</span></label>
                    <select name="proprietaire_id"
                            class="w-full px-4 py-3 rounded-[10px] bg-white border font-body text-sm text-bimo-text cursor-pointer
                                   focus:outline-none focus:ring-2 transition-all duration-150
                                   @error('proprietaire_id') border-bimo-red focus:border-bimo-red focus:ring-bimo-red/15
                                   @else border-bimo-navy/20 focus:border-bimo-gold focus:ring-bimo-gold/15 @enderror">
                        <option value="">— Sélectionner —</option>
                        @foreach($proprietaires as $p)
                        <option value="{{ $p->id }}" {{ old('proprietaire_id', $immeuble->proprietaire_id) == $p->id ? 'selected':'' }}>
                            {{ $p->name }} — {{ $p->email }}
                        </option>
                        @endforeach
                    </select>
                    @error('proprietaire_id')<p class="mt-1 font-body text-xs text-bimo-red">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        {{-- Informations --}}
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
            <div class="flex items-center gap-3 px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
                <div class="w-8 h-8 rounded-[8px] bg-bimo-navy/10 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-bimo-text/50" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="18" rx="2"/><line x1="2" y1="9" x2="22" y2="9"/><line x1="12" y1="3" x2="12" y2="21"/></svg>
                </div>
                <span class="font-display font-bold text-sm text-bimo-text">Informations générales</span>
            </div>
            <div class="px-5 py-5 space-y-4">
                <div class="space-y-1.5">
                    <label class="block font-body font-medium text-sm text-bimo-text">Nom de l'immeuble <span class="text-bimo-red">*</span></label>
                    <input type="text" name="nom" value="{{ old('nom', $immeuble->nom) }}"
                           class="w-full px-4 py-3 rounded-[10px] bg-white border font-body text-sm text-bimo-text
                                  focus:outline-none focus:ring-2 transition-all duration-150
                                  @error('nom') border-bimo-red focus:border-bimo-red focus:ring-bimo-red/15
                                  @else border-bimo-navy/20 focus:border-bimo-gold focus:ring-bimo-gold/15 @enderror">
                    @error('nom')<p class="mt-1 font-body text-xs text-bimo-red">{{ $message }}</p>@enderror
                </div>
                <div class="space-y-1.5">
                    <label class="block font-body font-medium text-sm text-bimo-text">
                        Nombre de niveaux <span class="font-normal text-bimo-text/40 text-xs ml-1">(optionnel)</span>
                    </label>
                    <input type="number" name="nombre_niveaux" value="{{ old('nombre_niveaux', $immeuble->nombre_niveaux) }}" min="1" max="99"
                           class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                  focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                </div>
            </div>
        </div>

        {{-- Localisation --}}
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
            <div class="flex items-center gap-3 px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
                <div class="w-8 h-8 rounded-[8px] bg-bimo-gold/10 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-bimo-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                </div>
                <span class="font-display font-bold text-sm text-bimo-text">Localisation</span>
            </div>
            <div class="px-5 py-5 space-y-4">
                <div class="space-y-1.5">
                    <label class="block font-body font-medium text-sm text-bimo-text">Adresse <span class="text-bimo-red">*</span></label>
                    <input type="text" name="adresse" value="{{ old('adresse', $immeuble->adresse) }}"
                           class="w-full px-4 py-3 rounded-[10px] bg-white border font-body text-sm text-bimo-text
                                  focus:outline-none focus:ring-2 transition-all duration-150
                                  @error('adresse') border-bimo-red focus:border-bimo-red focus:ring-bimo-red/15
                                  @else border-bimo-navy/20 focus:border-bimo-gold focus:ring-bimo-gold/15 @enderror">
                    @error('adresse')<p class="mt-1 font-body text-xs text-bimo-red">{{ $message }}</p>@enderror
                </div>
                <div class="space-y-1.5">
                    <label class="block font-body font-medium text-sm text-bimo-text">Ville <span class="text-bimo-red">*</span></label>
                    <input type="text" name="ville" value="{{ old('ville', $immeuble->ville) }}"
                           class="w-full px-4 py-3 rounded-[10px] bg-white border font-body text-sm text-bimo-text
                                  focus:outline-none focus:ring-2 transition-all duration-150
                                  @error('ville') border-bimo-red focus:border-bimo-red focus:ring-bimo-red/15
                                  @else border-bimo-navy/20 focus:border-bimo-gold focus:ring-bimo-gold/15 @enderror">
                    @error('ville')<p class="mt-1 font-body text-xs text-bimo-red">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        {{-- Mise à jour des appartements en masse --}}
        @if($immeuble->biens()->exists())
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
            <div class="flex items-center gap-3 px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
                <div class="w-8 h-8 rounded-[8px] bg-bimo-navy/10 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-bimo-text/50" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                </div>
                <div>
                    <span class="font-display font-bold text-sm text-bimo-text">Appartements</span>
                    <p class="font-body text-xs text-bimo-text/40 mt-0.5">
                        {{ $immeuble->biens()->count() }} unité(s) — laisser vide pour ne pas modifier
                    </p>
                </div>
            </div>
            <div class="px-5 py-5 space-y-4">
                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1.5">
                        <label class="block font-body font-medium text-sm text-bimo-text">
                            Loyer (FCFA) <span class="font-normal text-bimo-text/40 text-xs">(optionnel)</span>
                        </label>
                        <input type="number" name="loyer_par_unite"
                               value="{{ old('loyer_par_unite') }}" min="0" step="500"
                               placeholder="Ex : 150 000"
                               class="w-full px-4 py-3 rounded-[10px] bg-white border font-body text-sm text-bimo-text
                                      placeholder:text-bimo-text/30 focus:outline-none focus:ring-2 transition-all duration-150
                                      @error('loyer_par_unite') border-bimo-red focus:border-bimo-red focus:ring-bimo-red/15
                                      @else border-bimo-navy/20 focus:border-bimo-gold focus:ring-bimo-gold/15 @enderror">
                        @error('loyer_par_unite')<p class="mt-1 font-body text-xs text-bimo-red">{{ $message }}</p>@enderror
                    </div>
                    <div class="space-y-1.5">
                        <label class="block font-body font-medium text-sm text-bimo-text">
                            Commission agence (%) <span class="font-normal text-bimo-text/40 text-xs">(optionnel)</span>
                        </label>
                        <input type="number" name="taux_commission"
                               value="{{ old('taux_commission') }}" min="0" max="30" step="0.5"
                               placeholder="Ex : 10"
                               class="w-full px-4 py-3 rounded-[10px] bg-white border font-body text-sm text-bimo-text
                                      placeholder:text-bimo-text/30 focus:outline-none focus:ring-2 transition-all duration-150
                                      @error('taux_commission') border-bimo-red focus:border-bimo-red focus:ring-bimo-red/15
                                      @else border-bimo-navy/20 focus:border-bimo-gold focus:ring-bimo-gold/15 @enderror">
                        @error('taux_commission')<p class="mt-1 font-body text-xs text-bimo-red">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1.5">
                        <label class="block font-body font-medium text-sm text-bimo-text">
                            Charges (FCFA/mois) <span class="font-normal text-bimo-text/40 text-xs">(optionnel)</span>
                        </label>
                        <input type="number" name="charges_par_unite"
                               value="{{ old('charges_par_unite') }}" min="0" step="500"
                               placeholder="Ex : 10 000"
                               class="w-full px-4 py-3 rounded-[10px] bg-white border font-body text-sm text-bimo-text
                                      placeholder:text-bimo-text/30 focus:outline-none focus:ring-2 transition-all duration-150
                                      border-bimo-navy/20 focus:border-bimo-gold focus:ring-bimo-gold/15">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block font-body font-medium text-sm text-bimo-text">
                            Caution (FCFA) <span class="font-normal text-bimo-text/40 text-xs">(optionnel)</span>
                        </label>
                        <input type="number" name="caution_par_unite"
                               value="{{ old('caution_par_unite') }}" min="0" step="500"
                               placeholder="Ex : 300 000"
                               class="w-full px-4 py-3 rounded-[10px] bg-white border font-body text-sm text-bimo-text
                                      placeholder:text-bimo-text/30 focus:outline-none focus:ring-2 transition-all duration-150
                                      border-bimo-navy/20 focus:border-bimo-gold focus:ring-bimo-gold/15">
                    </div>
                </div>
                <p class="font-body text-xs text-bimo-text/40 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    Les champs renseignés s'appliquent à tous les {{ $immeuble->biens()->count() }} appartements. Laisser vide = pas de changement.
                </p>
            </div>
        </div>
        @endif

        {{-- Description + submit --}}
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
            <div class="flex items-center gap-3 px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
                <div class="w-8 h-8 rounded-[8px] bg-bimo-navy/5 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-bimo-text/40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                </div>
                <span class="font-display font-bold text-sm text-bimo-text">
                    Description <span class="font-normal text-bimo-text/40 text-xs ml-1">(optionnel)</span>
                </span>
            </div>
            <div class="px-5 py-5">
                <textarea name="description" rows="4"
                          class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                 focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15
                                 transition-all duration-150 resize-y">{{ old('description', $immeuble->description) }}</textarea>
            </div>
            <div class="sticky bottom-0 flex items-center justify-end gap-3 px-5 py-4
                        bg-white/95 backdrop-blur-sm border-t border-bimo-navy/[5%]">
                <a href="{{ route('admin.immeubles.show', $immeuble) }}"
                   class="px-5 py-2.5 border border-bimo-navy/15 rounded-[10px] font-body text-sm text-bimo-text/60 hover:text-bimo-text hover:border-bimo-navy/30 transition-all duration-150">
                    Annuler
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-[var(--ac)] text-white font-display font-bold text-sm rounded-[10px] hover:opacity-90 transition-opacity duration-150">
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    Enregistrer
                </button>
            </div>
        </div>

    </div>

    {{-- Colonne droite --}}
    <div class="lg:sticky lg:top-6">
        <div class="bg-bimo-navy rounded-[14px] overflow-hidden">
            <div class="px-5 py-4 border-b border-white/[7%]">
                <div class="font-display font-bold text-sm text-white">Récapitulatif</div>
            </div>
            <div class="px-5 py-2 divide-y divide-white/[6%]">
                @foreach([
                    ['Créé le',           $immeuble->created_at?->format('d/m/Y') ?? '—', ''],
                    ['Unités',            (string) $immeuble->biens()->count(), ''],
                    ['Sous contrat',      (string) $immeuble->biens()->whereHas('contratActif')->count(), 'text-bimo-gold font-semibold'],
                ] as [$lbl, $val, $cls])
                <div class="flex items-center justify-between py-2.5">
                    <span class="font-body text-xs text-white/40">{{ $lbl }}</span>
                    <span class="font-body text-xs text-white/70 {{ $cls }}">{{ $val }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

</div>
</form>
@endsection
