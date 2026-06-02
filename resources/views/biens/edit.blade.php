@extends('layouts.app')
@section('header', 'Biens › Modifier')

@section('content')

{{-- Breadcrumb --}}
<div class="flex items-center gap-2 font-body text-sm text-bimo-navy/40 mb-5">
    <a href="{{ route('admin.biens.index') }}" class="hover:text-bimo-navy transition-colors duration-150">Biens</a>
    <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
    <a href="{{ route('admin.biens.show', $bien) }}" class="hover:text-bimo-navy transition-colors duration-150">{{ $bien->reference }}</a>
    <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
    <span class="text-bimo-navy font-medium">Modifier</span>
</div>

{{-- En-tête --}}
<div class="flex items-center justify-between gap-3 flex-wrap mb-5">
    <div>
        <h1 class="font-display font-extrabold text-xl md:text-2xl text-bimo-navy tracking-tight">Modifier le bien</h1>
        <p class="font-body text-sm text-bimo-navy/50 mt-1">La référence ne peut pas être modifiée.</p>
    </div>
    <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-bimo-gold/10 border border-bimo-gold/25 rounded-[7px]
                font-display font-semibold text-sm text-bimo-gold">
        <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
        </svg>
        {{ $bien->reference }}
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-[1fr_300px] gap-5 items-start">

    {{-- ═══ COLONNE GAUCHE ═══ --}}
    <div class="space-y-4">

        {{-- FORMULAIRE PRINCIPAL --}}
        <form method="POST" action="{{ route('admin.biens.update', $bien) }}" id="form-edit">
        @csrf @method('PUT')

            {{-- PROPRIÉTAIRE --}}
            <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden mb-4">
                <div class="flex items-center gap-3 px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
                    <div class="w-8 h-8 rounded-[8px] bg-bimo-gold/15 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-bimo-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    </div>
                    <span class="font-display font-bold text-sm text-bimo-navy">Propriétaire</span>
                </div>
                <div class="px-5 py-5">
                    <div class="space-y-1.5">
                        <label class="block font-body font-medium text-sm text-bimo-navy">Propriétaire <span class="text-bimo-red">*</span></label>
                        <select name="proprietaire_id"
                                class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-navy
                                       focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15
                                       transition-all duration-150 cursor-pointer">
                            @foreach($proprietaires as $p)
                            <option value="{{ $p->id }}" {{ old('proprietaire_id', $bien->proprietaire_id) == $p->id ? 'selected':'' }}>
                                {{ $p->name }} — {{ $p->email }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- INFORMATIONS GÉNÉRALES --}}
            <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden mb-4">
                <div class="flex items-center gap-3 px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
                    <div class="w-8 h-8 rounded-[8px] bg-bimo-navy/10 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-bimo-navy/50" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    </div>
                    <span class="font-display font-bold text-sm text-bimo-navy">Informations générales</span>
                </div>
                <div class="px-5 py-5 space-y-4">

                    {{-- Type + Statut --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-1.5">
                            <label class="block font-body font-medium text-sm text-bimo-navy">Type <span class="text-bimo-red">*</span></label>
                            <select name="type"
                                    class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-navy
                                           focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15
                                           transition-all duration-150 cursor-pointer">
                                @foreach(\App\Models\Bien::TYPES as $val => $label)
                                <option value="{{ $val }}" {{ old('type', $bien->type) === $val ? 'selected':'' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label class="block font-body font-medium text-sm text-bimo-navy">Statut <span class="text-bimo-red">*</span></label>
                            <select name="statut"
                                    class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-navy
                                           focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15
                                           transition-all duration-150 cursor-pointer">
                                @foreach(\App\Models\Bien::STATUTS as $val => $label)
                                @if($val !== 'archive')
                                <option value="{{ $val }}" {{ old('statut', $bien->statut) === $val ? 'selected':'' }}>{{ $label }}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Titre --}}
                    <div class="space-y-1.5">
                        <label class="block font-body font-medium text-sm text-bimo-navy">
                            Titre <span class="font-normal text-bimo-navy/40 text-xs ml-1">(optionnel)</span>
                        </label>
                        <input type="text" name="titre" value="{{ old('titre', $bien->titre) }}"
                               placeholder="Ex: Villa F4 avec piscine — Almadies"
                               class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-navy
                                      placeholder:text-bimo-navy/30 focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15
                                      transition-all duration-150">
                    </div>

                    {{-- Surface + Pièces --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-1.5">
                            <label class="block font-body font-medium text-sm text-bimo-navy">Surface (m²)</label>
                            <input type="number" name="surface_m2" value="{{ old('surface_m2', $bien->surface_m2) }}" min="1" step="0.5"
                                   class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-navy
                                          focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                        </div>
                        <div class="space-y-1.5">
                            <label class="block font-body font-medium text-sm text-bimo-navy">Nombre de pièces</label>
                            <input type="number" name="nombre_pieces" value="{{ old('nombre_pieces', $bien->nombre_pieces) }}" min="1"
                                   class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-navy
                                          focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                        </div>
                    </div>

                    {{-- Chambres + SDB --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-1.5">
                            <label class="block font-body font-medium text-sm text-bimo-navy">Chambres</label>
                            <input type="number" name="nombre_chambres" value="{{ old('nombre_chambres', $bien->nombre_chambres) }}" min="0"
                                   class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-navy
                                          focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                        </div>
                        <div class="space-y-1.5">
                            <label class="block font-body font-medium text-sm text-bimo-navy">Salles de bain</label>
                            <input type="number" name="nombre_sdb" value="{{ old('nombre_sdb', $bien->nombre_sdb) }}" min="0"
                                   class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-navy
                                          focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                        </div>
                    </div>

                    {{-- Checkboxes --}}
                    <div class="grid grid-cols-3 gap-3">
                        <label class="flex items-center gap-2.5 cursor-pointer p-3 border border-bimo-navy/10 rounded-[10px] hover:border-bimo-gold/40 transition-all duration-150">
                            <input type="checkbox" name="parking" value="1" {{ old('parking', $bien->parking) ? 'checked':'' }}
                                   class="w-4 h-4 rounded border-bimo-navy/20 cursor-pointer"
                                   style="accent-color: var(--ac)">
                            <span class="font-body font-medium text-sm text-bimo-navy">Parking</span>
                        </label>
                        <label class="flex items-center gap-2.5 cursor-pointer p-3 border border-bimo-navy/10 rounded-[10px] hover:border-bimo-gold/40 transition-all duration-150">
                            <input type="checkbox" name="climatise" value="1" {{ old('climatise', $bien->climatise) ? 'checked':'' }}
                                   class="w-4 h-4 rounded border-bimo-navy/20 cursor-pointer"
                                   style="accent-color: var(--ac)">
                            <span class="font-body font-medium text-sm text-bimo-navy">Climatisé</span>
                        </label>
                        <label class="flex items-center gap-2.5 cursor-pointer p-3 border border-bimo-navy/10 rounded-[10px] hover:border-bimo-gold/40 transition-all duration-150">
                            <input type="checkbox" name="meuble" value="1" {{ old('meuble', $bien->meuble) ? 'checked':'' }}
                                   class="w-4 h-4 rounded border-bimo-navy/20 cursor-pointer"
                                   style="accent-color: var(--ac)">
                            <span class="font-body font-medium text-sm text-bimo-navy">Meublé</span>
                        </label>
                    </div>

                    {{-- Étage --}}
                    <div class="space-y-1.5">
                        <label class="block font-body font-medium text-sm text-bimo-navy">
                            Étage <span class="font-normal text-bimo-navy/40 text-xs ml-1">(optionnel)</span>
                        </label>
                        <input type="number" name="etage" value="{{ old('etage', $bien->etage) }}" min="-1" max="50"
                               class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-navy
                                      focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                    </div>

                    {{-- Aménités --}}
                    <div class="space-y-1.5">
                        <label class="block font-body font-medium text-sm text-bimo-navy">
                            Aménités <span class="font-normal text-bimo-navy/40 text-xs ml-1">(optionnel)</span>
                        </label>
                        <textarea name="amenites" rows="2" placeholder="Vue mer, Gardiennage, Fibre optique…"
                                  class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-navy
                                         placeholder:text-bimo-navy/30 focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15
                                         transition-all duration-150 resize-y">{{ old('amenites', is_array($bien->amenites) ? implode(', ', $bien->amenites) : $bien->amenites) }}</textarea>
                        <p class="font-body text-[11px] text-bimo-navy/30">Séparés par des virgules</p>
                    </div>

                    {{-- Visible portail --}}
                    <label class="flex items-center gap-3 cursor-pointer p-3.5 border border-bimo-navy/10 rounded-[10px] hover:border-bimo-gold/40 transition-all duration-150">
                        <input type="checkbox" name="visible_portail" value="1"
                               {{ old('visible_portail', $bien->visible_portail ?? true) ? 'checked':'' }}
                               class="w-4 h-4 rounded border-bimo-navy/20 cursor-pointer flex-shrink-0"
                               style="accent-color: var(--ac)">
                        <div>
                            <div class="font-body font-medium text-sm text-bimo-navy">Visible sur le portail public</div>
                            <div class="font-body text-xs text-bimo-navy/40">Décocher pour masquer ce bien du portail</div>
                        </div>
                    </label>
                </div>
            </div>

            {{-- LOCALISATION --}}
            <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden mb-4">
                <div class="flex items-center gap-3 px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
                    <div class="w-8 h-8 rounded-[8px] bg-bimo-gold/10 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-bimo-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    </div>
                    <span class="font-display font-bold text-sm text-bimo-navy">Localisation</span>
                </div>
                <div class="px-5 py-5 space-y-4">
                    <div class="space-y-1.5">
                        <label class="block font-body font-medium text-sm text-bimo-navy">Adresse <span class="text-bimo-red">*</span></label>
                        <input type="text" name="adresse" value="{{ old('adresse', $bien->adresse) }}"
                               class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-navy
                                      focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-1.5">
                            <label class="block font-body font-medium text-sm text-bimo-navy">Quartier</label>
                            <input type="text" name="quartier" value="{{ old('quartier', $bien->quartier) }}"
                                   class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-navy
                                          focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                        </div>
                        <div class="space-y-1.5">
                            <label class="block font-body font-medium text-sm text-bimo-navy">Commune</label>
                            <input type="text" name="commune" value="{{ old('commune', $bien->commune) }}"
                                   class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-navy
                                          focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                        </div>
                    </div>
                    <div class="space-y-1.5">
                        <label class="block font-body font-medium text-sm text-bimo-navy">Ville <span class="text-bimo-red">*</span></label>
                        <input type="text" name="ville" value="{{ old('ville', $bien->ville) }}"
                               class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-navy
                                      focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                    </div>
                </div>
            </div>

            {{-- FINANCIER --}}
            <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden mb-4">
                <div class="flex items-center gap-3 px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
                    <div class="w-8 h-8 rounded-[8px] bg-bimo-gold/15 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-bimo-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
                    </div>
                    <span class="font-display font-bold text-sm text-bimo-navy">Informations financières</span>
                </div>
                <div class="px-5 py-5">
                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-1.5">
                            <label class="block font-body font-medium text-sm text-bimo-navy">Loyer mensuel (FCFA) <span class="text-bimo-red">*</span></label>
                            <input type="number" name="loyer_mensuel"
                                   value="{{ old('loyer_mensuel', $bien->loyer_mensuel) }}" min="0" step="500"
                                   class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-navy
                                          focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                        </div>
                        <div class="space-y-1.5">
                            <label class="block font-body font-medium text-sm text-bimo-navy">Taux commission (%)</label>
                            <input type="number" name="taux_commission"
                                   value="{{ old('taux_commission', $bien->taux_commission ?? 10) }}" min="0" max="30" step="0.5"
                                   class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-navy
                                          focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                        </div>
                    </div>
                </div>
            </div>

            {{-- DESCRIPTION --}}
            <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
                <div class="flex items-center gap-3 px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
                    <div class="w-8 h-8 rounded-[8px] bg-bimo-navy/5 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-bimo-navy/40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                    </div>
                    <span class="font-display font-bold text-sm text-bimo-navy">Description</span>
                </div>
                <div class="px-5 py-5">
                    <textarea name="description" rows="4" placeholder="Description du bien, équipements…"
                              class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-navy
                                     placeholder:text-bimo-navy/30 focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15
                                     transition-all duration-150 resize-y">{{ old('description', $bien->description) }}</textarea>
                </div>
                {{-- Submit --}}
                <div class="sticky bottom-0 flex items-center justify-end gap-3 px-5 py-4
                            bg-white/95 backdrop-blur-sm border-t border-bimo-navy/[5%]">
                    <a href="{{ route('admin.biens.show', $bien) }}"
                       class="px-5 py-2.5 border border-bimo-navy/15 rounded-[10px]
                              font-body text-sm text-bimo-navy/60 hover:text-bimo-navy hover:border-bimo-navy/30
                              transition-all duration-150">
                        Annuler
                    </a>
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-[var(--ac)] text-white
                                   font-display font-bold text-sm rounded-[10px]
                                   hover:opacity-90 transition-opacity duration-150">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                        Enregistrer les modifications
                    </button>
                </div>
            </div>

        </form>

        {{-- GESTION DES PHOTOS --}}
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
            <div class="flex items-center gap-3 px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
                <div class="w-8 h-8 rounded-[8px] bg-bimo-navy/5 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-bimo-navy/40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/>
                    </svg>
                </div>
                <span class="font-display font-bold text-sm text-bimo-navy">
                    Photos @if($bien->photos->count() > 0)<span class="font-body font-normal text-bimo-navy/40 text-xs ml-1">({{ $bien->photos->count() }})</span>@endif
                </span>
            </div>
            <div class="px-5 py-5">

                {{-- Photos existantes --}}
                @if($bien->photos->count() > 0)
                <div class="grid grid-cols-[repeat(auto-fill,minmax(110px,1fr))] gap-3 mb-4">
                    @foreach($bien->photos as $photo)
                    <div class="relative rounded-[10px] overflow-hidden border-2 transition-colors duration-150 group
                                {{ $photo->est_principale ? 'border-bimo-gold' : 'border-bimo-navy/10' }}">
                        <img src="{{ asset('storage/'.$photo->chemin) }}"
                             alt="" class="w-full h-24 object-cover">
                        @if($photo->est_principale)
                        <span class="absolute top-1.5 left-1.5 bg-bimo-gold text-bimo-navy font-body font-bold text-[9px] px-1.5 py-0.5 rounded-[3px]">
                            Principale
                        </span>
                        @endif
                        {{-- Actions hover --}}
                        <div class="absolute inset-x-0 bottom-0 flex gap-1 p-1.5 bg-bimo-navy/70
                                    opacity-0 group-hover:opacity-100 transition-opacity duration-150">
                            @if(!$photo->est_principale)
                            <form method="POST"
                                  action="{{ route('admin.biens.photos.principale', [$bien, $photo]) }}"
                                  class="flex-1">
                                @csrf @method('PATCH')
                                <button type="submit"
                                        class="w-full py-1 rounded-[3px] font-body font-bold text-[9px]
                                               bg-bimo-gold/90 text-bimo-navy cursor-pointer">
                                    ★ Principale
                                </button>
                            </form>
                            @endif
                            <form method="POST"
                                  action="{{ route('admin.biens.photos.destroy', [$bien, $photo]) }}"
                                  class="flex-1"
                                  data-confirm="Supprimer cette photo définitivement ?"
                                  data-confirm-title="Supprimer la photo ?"
                                  data-confirm-ok="Supprimer"
                                  data-confirm-color="#EF4444"
                                  data-confirm-icon-bg="rgba(239,68,68,0.1)">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="w-full py-1 rounded-[3px] font-body font-bold text-[9px]
                                               bg-bimo-red/90 text-white cursor-pointer">
                                    ✕ Sup.
                                </button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif

                {{-- Ajout photos --}}
                <form method="POST"
                      action="{{ route('admin.biens.photos.store', $bien) }}"
                      enctype="multipart/form-data">
                    @csrf

                    <div id="drop-zone-edit"
                         class="border-2 border-dashed border-bimo-navy/15 rounded-[10px] p-6 text-center cursor-pointer
                                transition-all duration-150 bg-bimo-bg"
                         onclick="document.getElementById('photos-edit-input').click()"
                         ondragover="event.preventDefault();this.style.borderColor='#C9A84C';this.style.background='#fffbeb'"
                         ondragleave="this.style.borderColor='';this.style.background=''"
                         ondrop="handleDropEdit(event)">
                        <svg class="w-7 h-7 text-bimo-navy/20 mx-auto mb-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/>
                            <polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/>
                        </svg>
                        <p class="font-body text-sm text-bimo-navy/50 mb-0.5">Ajouter des photos</p>
                        <p class="font-body text-xs text-bimo-navy/30">JPG, PNG, WEBP — max 3 Mo</p>
                    </div>

                    <input type="file" id="photos-edit-input" name="photos[]"
                           multiple accept="image/jpeg,image/png,image/webp"
                           style="display:none" onchange="previewEdit(this.files)">

                    <div id="preview-edit"
                         style="display:none;grid-template-columns:repeat(auto-fill,minmax(90px,1fr));gap:8px;margin-top:12px">
                    </div>

                    <div id="btn-upload-edit" style="display:none" class="mt-3">
                        <button type="submit"
                                class="w-full flex items-center justify-center gap-2 px-5 py-2.5
                                       bg-bimo-navy text-white font-display font-bold text-sm rounded-[10px]
                                       hover:bg-bimo-navy-dk transition-colors duration-150">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/>
                                <polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/>
                            </svg>
                            Uploader les photos
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>{{-- fin colonne gauche --}}

    {{-- ═══ COLONNE DROITE — INFO SIDEBAR ═══ --}}
    <div class="lg:sticky lg:top-6">
        <div class="bg-bimo-navy rounded-[14px] overflow-hidden">
            <div class="px-5 py-4 border-b border-white/[7%]">
                <div class="font-display font-bold text-sm text-white">Bien actuel</div>
            </div>
            <div class="px-5 py-2 divide-y divide-white/[6%]">
                @php
                    $rows = [
                        ['Référence',  $bien->reference, 'font-display font-semibold'],
                        ['Type',       $bien->type_label, ''],
                        ['Statut',     $bien->statut_label, ''],
                        ['Loyer',      number_format($bien->loyer_mensuel, 0, ',', ' ') . ' F', 'text-bimo-gold font-semibold'],
                        ['Commission', ($bien->taux_commission ?? 10) . ' %', ''],
                        ['Surface',    $bien->surface_m2 ? $bien->surface_m2 . ' m²' : '—', ''],
                        ['Pièces',     $bien->nombre_pieces ?? '—', ''],
                        ['Meublé',     $bien->meuble ? 'Oui' : 'Non', ''],
                        ['Créé le',    $bien->created_at?->format('d/m/Y') ?? '—', ''],
                    ];
                @endphp
                @foreach($rows as [$lbl, $val, $valClass])
                <div class="flex items-center justify-between py-2.5">
                    <span class="font-body text-xs text-white/40">{{ $lbl }}</span>
                    <span class="font-body text-xs text-white/80 {{ $valClass }}">{{ $val }}</span>
                </div>
                @endforeach
            </div>

            @if($bien->contratActif)
            <div class="px-5 py-4 border-t border-white/[7%]">
                <div class="font-body font-medium text-[9px] uppercase tracking-widest text-white/25 mb-3">Contrat actif</div>
                <div class="font-body text-sm text-white/80 mb-1">
                    {{ $bien->contratActif->locataire?->name ?? '—' }}
                </div>
                <div class="font-body text-xs text-white/35">
                    Depuis {{ $bien->contratActif->date_debut?->format('d/m/Y') }}
                </div>
                <a href="{{ route('admin.contrats.show', $bien->contratActif) }}"
                   class="inline-flex items-center gap-1.5 mt-3 font-body text-xs text-bimo-gold hover:text-white transition-colors duration-150">
                    Voir le contrat →
                </a>
            </div>
            @endif
        </div>
    </div>

</div>

@push('scripts')
<script>
function previewEdit(files) {
    const grid = document.getElementById('preview-edit');
    const btn  = document.getElementById('btn-upload-edit');
    grid.innerHTML = '';
    if (!files || !files.length) { grid.style.display = 'none'; btn.style.display = 'none'; return; }
    grid.style.display = 'grid';
    btn.style.display  = 'block';
    [...files].forEach(file => {
        const reader = new FileReader();
        reader.onload = e => {
            const div = document.createElement('div');
            div.style.cssText = 'position:relative;border-radius:8px;overflow:hidden;height:80px;border:1px solid rgba(27,79,107,0.1)';
            div.innerHTML = `<img src="${e.target.result}" style="width:100%;height:100%;object-fit:cover">`;
            grid.appendChild(div);
        };
        reader.readAsDataURL(file);
    });
    document.getElementById('drop-zone-edit').style.borderColor = '#C9A84C';
    document.getElementById('drop-zone-edit').style.background  = '#fffbeb';
}

function handleDropEdit(e) {
    e.preventDefault();
    document.getElementById('drop-zone-edit').style.borderColor = '';
    document.getElementById('drop-zone-edit').style.background  = '';
    const input = document.getElementById('photos-edit-input');
    try { input.files = e.dataTransfer.files; } catch(err) {}
    previewEdit(e.dataTransfer.files);
}
</script>
@endpush

@endsection
