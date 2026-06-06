@extends('layouts.app')
@section('header', 'Biens › Nouveau')

@section('content')

{{-- Modal limite atteinte --}}
@if(session('upgrade_required'))
@php $up = session('upgrade_required'); @endphp
<div id="modal-limite"
     class="fixed inset-0 bg-bimo-navy/65 z-[1000] flex items-center justify-center p-5">
    <div class="bg-white rounded-[20px] p-8 max-w-sm w-full shadow-xl">
        <div class="w-12 h-12 rounded-[12px] bg-amber-50 flex items-center justify-center mb-4">
            <svg class="w-6 h-6 text-amber-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
        </div>
        <h2 class="font-display font-bold text-lg text-bimo-text mb-3">Limite atteinte</h2>
        <p class="font-body text-sm text-bimo-text/70 leading-relaxed mb-6">
            Vous gérez déjà <strong>{{ $up['nb_unites'] }} unités sur {{ $up['limite'] }}</strong>
            autorisées en plan <strong>{{ $up['plan_actuel'] }}</strong>.<br><br>
            Le plan <strong>{{ $up['plan_suivant'] }}</strong> vous permet d'en gérer
            jusqu'à <strong>{{ $up['limite_suivante'] }}</strong>.
        </p>
        <div class="flex gap-3">
            <a href="{{ route('subscription.index') }}"
               class="flex-1 text-center px-4 py-2.5 bg-bimo-navy text-white font-display font-bold text-sm rounded-[9px] hover:bg-bimo-navy-dk transition-colors duration-150">
                Voir le plan {{ $up['plan_suivant'] }}
            </a>
            <button onclick="document.getElementById('modal-limite').style.display='none'"
                    class="flex-1 px-4 py-2.5 bg-bimo-bg text-bimo-text/60 font-body text-sm rounded-[9px] hover:bg-bimo-bg2 transition-colors duration-150">
                Pas maintenant
            </button>
        </div>
    </div>
</div>
@endif

<div class="mb-5">
    <div class="flex items-center gap-2 font-body text-sm text-bimo-text/40 mb-3">
        <a href="{{ route('admin.biens.index') }}" class="hover:text-bimo-text transition-colors duration-150">Biens</a>
        <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
        <span class="text-bimo-text font-medium">Nouveau bien</span>
    </div>
    <h1 class="font-display font-extrabold text-xl md:text-2xl text-bimo-text tracking-tight">Ajouter un bien</h1>
    <p class="font-body text-sm text-bimo-text/50 mt-1">La référence est générée automatiquement.</p>
</div>

<form method="POST" action="{{ route('admin.biens.store') }}" id="form-bien" enctype="multipart/form-data">
@csrf

<div class="grid grid-cols-1 lg:grid-cols-[1fr_340px] gap-5 items-start">

    {{-- ═══ COLONNE GAUCHE ═══ --}}
    <div class="space-y-4">

        {{-- PROPRIÉTAIRE & IMMEUBLE --}}
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
            <div class="flex items-center gap-3 px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
                <div class="w-8 h-8 rounded-[8px] bg-bimo-gold/15 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-bimo-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                </div>
                <span class="font-display font-bold text-sm text-bimo-text">Propriétaire</span>
            </div>
            <div class="px-5 py-5 space-y-4">
                <div class="space-y-1.5">
                    <label class="block font-body font-medium text-sm text-bimo-text">
                        Propriétaire <span class="text-bimo-red">*</span>
                    </label>
                    <select name="proprietaire_id"
                            class="w-full px-4 py-3 rounded-[10px] bg-white border font-body text-sm text-bimo-text
                                   focus:outline-none focus:ring-2 transition-all duration-150 cursor-pointer
                                   @error('proprietaire_id') border-bimo-red focus:border-bimo-red focus:ring-bimo-red/15
                                   @else border-bimo-navy/20 focus:border-bimo-gold focus:ring-bimo-gold/15 @enderror">
                        <option value="">— Sélectionner —</option>
                        @foreach($proprietaires as $p)
                        <option value="{{ $p->id }}" {{ old('proprietaire_id', $immeubleSelectionne?->proprietaire_id) == $p->id ? 'selected':'' }}>
                            {{ $p->name }} — {{ $p->email }}
                        </option>
                        @endforeach
                    </select>
                    @error('proprietaire_id')<p class="mt-1 font-body text-xs text-bimo-red">{{ $message }}</p>@enderror
                </div>

                <div class="space-y-1.5">
                    <label class="block font-body font-medium text-sm text-bimo-text">
                        Immeuble
                        <span class="font-normal text-bimo-text/40 text-xs ml-1">(optionnel — laisser vide pour un bien standalone)</span>
                    </label>
                    <select name="immeuble_id"
                            class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                   focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15
                                   transition-all duration-150 cursor-pointer">
                        <option value="">— Bien standalone (aucun immeuble) —</option>
                        @foreach($immeubles as $imm)
                        <option value="{{ $imm->id }}" {{ old('immeuble_id', $immeubleSelectionne?->id) == $imm->id ? 'selected':'' }}>
                            {{ $imm->nom }} — {{ $imm->ville }}
                        </option>
                        @endforeach
                    </select>
                    @if($immeubleSelectionne)
                    <p class="flex items-center gap-1.5 font-body text-xs text-bimo-gold">
                        <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                        Unité de « {{ $immeubleSelectionne->nom }} » pré-sélectionnée
                    </p>
                    @endif
                </div>
            </div>
        </div>

        {{-- INFORMATIONS GÉNÉRALES --}}
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
            <div class="flex items-center gap-3 px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
                <div class="w-8 h-8 rounded-[8px] bg-bimo-navy/10 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-bimo-text/50" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                </div>
                <span class="font-display font-bold text-sm text-bimo-text">Informations générales</span>
            </div>
            <div class="px-5 py-5 space-y-4">

                {{-- Type --}}
                <div class="space-y-1.5">
                    <label class="block font-body font-medium text-sm text-bimo-text">Type <span class="text-bimo-red">*</span></label>
                    <select name="type"
                            class="w-full px-4 py-3 rounded-[10px] bg-white border font-body text-sm text-bimo-text
                                   focus:outline-none focus:ring-2 transition-all duration-150 cursor-pointer
                                   @error('type') border-bimo-red focus:border-bimo-red focus:ring-bimo-red/15
                                   @else border-bimo-navy/20 focus:border-bimo-gold focus:ring-bimo-gold/15 @enderror">
                        <option value="">— Choisir —</option>
                        @foreach(\App\Models\Bien::TYPES as $val => $label)
                        <option value="{{ $val }}" {{ old('type') === $val ? 'selected':'' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('type')<p class="mt-1 font-body text-xs text-bimo-red">{{ $message }}</p>@enderror
                    <p class="font-body text-xs text-bimo-text/40">Pour un bâtiment avec plusieurs appartements, utilisez la section <a href="{{ route('admin.immeubles.create') }}" class="text-bimo-gold hover:underline">Immeubles</a>.</p>
                </div>

                {{-- SECTION BIEN --}}
                <div class="space-y-4">

                    <div class="space-y-1.5">
                        <label class="block font-body font-medium text-sm text-bimo-text">
                            Titre
                            <span class="font-normal text-bimo-text/40 text-xs ml-1">(optionnel)</span>
                        </label>
                        <input type="text" name="titre" value="{{ old('titre') }}"
                               placeholder="Ex: Villa F4 avec piscine — Almadies"
                               class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                      placeholder:text-bimo-text/30 focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15
                                      transition-all duration-150">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-1.5">
                            <label class="block font-body font-medium text-sm text-bimo-text">Surface (m²)</label>
                            <input type="number" name="surface_m2" value="{{ old('surface_m2') }}" min="1" step="0.5"
                                   class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                          focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                        </div>
                        <div class="space-y-1.5">
                            <label class="block font-body font-medium text-sm text-bimo-text">Nombre de pièces</label>
                            <input type="number" name="nombre_pieces" value="{{ old('nombre_pieces') }}" min="1"
                                   class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                          focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-1.5">
                            <label class="block font-body font-medium text-sm text-bimo-text">Chambres</label>
                            <input type="number" name="nombre_chambres" value="{{ old('nombre_chambres') }}" min="0"
                                   class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                          focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                        </div>
                        <div class="space-y-1.5">
                            <label class="block font-body font-medium text-sm text-bimo-text">Salles de bain</label>
                            <input type="number" name="nombre_sdb" value="{{ old('nombre_sdb') }}" min="0"
                                   class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                          focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-3">
                        <label class="flex items-center gap-2.5 cursor-pointer p-3 border border-bimo-navy/10 rounded-[10px] hover:border-bimo-gold/40 transition-all duration-150">
                            <input type="checkbox" name="parking" value="1" {{ old('parking') ? 'checked':'' }}
                                   class="w-4 h-4 rounded border-bimo-navy/20 cursor-pointer focus:ring-bimo-gold/20 focus:ring-2"
                                   style="accent-color: var(--ac)">
                            <span class="font-body font-medium text-sm text-bimo-text">Parking</span>
                        </label>
                        <label class="flex items-center gap-2.5 cursor-pointer p-3 border border-bimo-navy/10 rounded-[10px] hover:border-bimo-gold/40 transition-all duration-150">
                            <input type="checkbox" name="climatise" value="1" {{ old('climatise') ? 'checked':'' }}
                                   class="w-4 h-4 rounded border-bimo-navy/20 cursor-pointer focus:ring-bimo-gold/20 focus:ring-2"
                                   style="accent-color: var(--ac)">
                            <span class="font-body font-medium text-sm text-bimo-text">Climatisé</span>
                        </label>
                        <label class="flex items-center gap-2.5 cursor-pointer p-3 border border-bimo-navy/10 rounded-[10px] hover:border-bimo-gold/40 transition-all duration-150">
                            <input type="checkbox" name="meuble" value="1" {{ old('meuble') ? 'checked':'' }}
                                   class="w-4 h-4 rounded border-bimo-navy/20 cursor-pointer focus:ring-bimo-gold/20 focus:ring-2"
                                   style="accent-color: var(--ac)">
                            <span class="font-body font-medium text-sm text-bimo-text">Meublé</span>
                        </label>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block font-body font-medium text-sm text-bimo-text">
                            Étage
                            <span class="font-normal text-bimo-text/40 text-xs ml-1">(optionnel)</span>
                        </label>
                        <input type="number" name="etage" value="{{ old('etage') }}" min="-1" max="50"
                               class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                      focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                    </div>

                    <div class="space-y-1.5">
                        <label class="block font-body font-medium text-sm text-bimo-text">
                            Aménités
                            <span class="font-normal text-bimo-text/40 text-xs ml-1">(optionnel)</span>
                        </label>
                        <textarea name="amenites" rows="2" placeholder="Vue mer, Gardiennage, Fibre optique…"
                                  class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                         placeholder:text-bimo-text/30 focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15
                                         transition-all duration-150 resize-y">{{ old('amenites') }}</textarea>
                        <p class="font-body text-[11px] text-bimo-text/30">Séparés par des virgules</p>
                    </div>
                </div>

            </div>
        </div>

        {{-- LOCALISATION --}}
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
                    <input type="text" name="adresse" value="{{ old('adresse') }}" placeholder="Rue, numéro"
                           class="w-full px-4 py-3 rounded-[10px] bg-white border font-body text-sm text-bimo-text
                                  placeholder:text-bimo-text/30 focus:outline-none focus:ring-2 transition-all duration-150
                                  @error('adresse') border-bimo-red focus:border-bimo-red focus:ring-bimo-red/15
                                  @else border-bimo-navy/20 focus:border-bimo-gold focus:ring-bimo-gold/15 @enderror">
                    @error('adresse')<p class="mt-1 font-body text-xs text-bimo-red">{{ $message }}</p>@enderror
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1.5">
                        <label class="block font-body font-medium text-sm text-bimo-text">Quartier</label>
                        <input type="text" name="quartier" value="{{ old('quartier') }}" placeholder="Ex: Almadies"
                               class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                      placeholder:text-bimo-text/30 focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block font-body font-medium text-sm text-bimo-text">Commune</label>
                        <input type="text" name="commune" value="{{ old('commune') }}" placeholder="Ex: Dakar Plateau"
                               class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                      placeholder:text-bimo-text/30 focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                    </div>
                </div>
                <div class="space-y-1.5">
                    <label class="block font-body font-medium text-sm text-bimo-text">Ville <span class="text-bimo-red">*</span></label>
                    <input type="text" name="ville" value="{{ old('ville', 'Dakar') }}" placeholder="Ex: Dakar"
                           class="w-full px-4 py-3 rounded-[10px] bg-white border font-body text-sm text-bimo-text
                                  placeholder:text-bimo-text/30 focus:outline-none focus:ring-2 transition-all duration-150
                                  @error('ville') border-bimo-red focus:border-bimo-red focus:ring-bimo-red/15
                                  @else border-bimo-navy/20 focus:border-bimo-gold focus:ring-bimo-gold/15 @enderror">
                    @error('ville')<p class="mt-1 font-body text-xs text-bimo-red">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        {{-- FINANCIER --}}
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden" id="card-financier">
            <div class="flex items-center gap-3 px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
                <div class="w-8 h-8 rounded-[8px] bg-bimo-gold/15 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-bimo-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
                </div>
                <span class="font-display font-bold text-sm text-bimo-text">Informations financières</span>
            </div>
            <div class="px-5 py-5">
                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1.5">
                        <label class="block font-body font-medium text-sm text-bimo-text">Loyer mensuel (FCFA) <span class="text-bimo-red">*</span></label>
                        <input type="number" name="loyer_mensuel" id="loyer_mensuel"
                               value="{{ old('loyer_mensuel') }}" min="0" step="500"
                               oninput="calcRecap()"
                               class="w-full px-4 py-3 rounded-[10px] bg-white border font-body text-sm text-bimo-text
                                      focus:outline-none focus:ring-2 transition-all duration-150
                                      @error('loyer_mensuel') border-bimo-red focus:border-bimo-red focus:ring-bimo-red/15
                                      @else border-bimo-navy/20 focus:border-bimo-gold focus:ring-bimo-gold/15 @enderror">
                        @error('loyer_mensuel')<p class="mt-1 font-body text-xs text-bimo-red">{{ $message }}</p>@enderror
                    </div>
                    <div class="space-y-1.5">
                        <label class="block font-body font-medium text-sm text-bimo-text">Taux commission (%) <span class="text-bimo-red">*</span></label>
                        <input type="number" name="taux_commission" id="taux_commission"
                               value="{{ old('taux_commission', 10) }}" min="0" max="30" step="0.5"
                               oninput="calcRecap()"
                               class="w-full px-4 py-3 rounded-[10px] bg-white border font-body text-sm text-bimo-text
                                      focus:outline-none focus:ring-2 transition-all duration-150
                                      @error('taux_commission') border-bimo-red focus:border-bimo-red focus:ring-bimo-red/15
                                      @else border-bimo-navy/20 focus:border-bimo-gold focus:ring-bimo-gold/15 @enderror">
                        @error('taux_commission')<p class="mt-1 font-body text-xs text-bimo-red">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- PHOTOS --}}
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
            <div class="flex items-center gap-3 px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
                <div class="w-8 h-8 rounded-[8px] bg-bimo-navy/5 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-bimo-text/40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/>
                    </svg>
                </div>
                <span class="font-display font-bold text-sm text-bimo-text">
                    Photos <span class="font-normal text-bimo-text/40 text-xs ml-1">(optionnel)</span>
                </span>
            </div>
            <div class="px-5 py-5">
                <div id="drop-zone"
                     class="border-2 border-dashed border-bimo-navy/15 rounded-[10px] p-7 text-center cursor-pointer transition-all duration-150 bg-bimo-bg"
                     onclick="document.getElementById('photos-input').click()"
                     ondragover="event.preventDefault();this.style.borderColor='#C9A84C';this.style.background='#fffbeb'"
                     ondragleave="this.style.borderColor='';this.style.background=''"
                     ondrop="handleDrop(event)">
                    <svg class="w-8 h-8 text-bimo-text/20 mx-auto mb-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/>
                        <polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/>
                    </svg>
                    <p class="font-body text-sm text-bimo-text/50 mb-1">Cliquez ou glissez vos photos ici</p>
                    <p class="font-body text-xs text-bimo-text/30">JPG, PNG, WEBP — max 3 Mo par photo</p>
                </div>

                <input type="file" id="photos-input" name="photos[]"
                       multiple accept="image/jpeg,image/png,image/webp"
                       style="display:none" onchange="previewPhotos(this.files)">

                <div id="preview-grid"
                     style="display:none;grid-template-columns:repeat(auto-fill,minmax(100px,1fr));gap:10px;margin-top:14px">
                </div>

                <p class="font-body text-[11px] text-bimo-text/30 mt-3">
                    La première photo sélectionnée sera la photo principale.
                </p>
            </div>
        </div>

        {{-- DESCRIPTION --}}
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
                          placeholder="Description du bien, équipements, état général…"
                          class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                 placeholder:text-bimo-text/30 focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15
                                 transition-all duration-150 resize-y">{{ old('description') }}</textarea>
            </div>

            {{-- Barre submit --}}
            <div class="sticky bottom-0 flex items-center justify-end gap-3 px-5 py-4
                        bg-white/95 backdrop-blur-sm border-t border-bimo-navy/[5%]">
                <a href="{{ route('admin.biens.index') }}"
                   class="px-5 py-2.5 border border-bimo-navy/15 rounded-[10px]
                          font-body text-sm text-bimo-text/60 hover:text-bimo-text hover:border-bimo-navy/30
                          transition-all duration-150">
                    Annuler
                </a>
                <button type="submit" id="btn-submit"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-[var(--ac)] text-white
                               font-display font-bold text-sm rounded-[10px]
                               hover:opacity-90 transition-opacity duration-150">
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    <span id="btn-submit-label">Créer le bien</span>
                </button>
            </div>
        </div>

    </div>{{-- fin colonne gauche --}}

    {{-- ═══ COLONNE DROITE : RÉCAPITULATIF FISCAL ═══ --}}
    <div class="lg:sticky lg:top-6">
        <div class="bg-bimo-navy rounded-[14px] overflow-hidden">
            <div class="px-5 py-4 border-b border-white/[7%]">
                <div class="font-display font-bold text-sm text-white">Récapitulatif fiscal</div>
            </div>
            <div class="px-5 py-4 space-y-0">
                <div class="flex items-center justify-between py-2.5 border-b border-white/[6%]">
                    <span class="font-body text-xs text-white/45">Loyer mensuel</span>
                    <span class="font-display font-semibold text-xs text-white" id="rp-loyer">— F</span>
                </div>
                <div class="flex items-center justify-between py-2.5 border-b border-white/[6%]">
                    <span class="font-body text-xs text-white/45">Commission HT</span>
                    <span class="font-display font-semibold text-xs text-bimo-gold" id="rp-comm">— F</span>
                </div>
                <div class="flex items-center justify-between py-2.5 border-b border-white/[6%]">
                    <span class="font-body text-xs text-white/45">TVA commission (18%)</span>
                    <span class="font-display font-semibold text-xs text-white" id="rp-tva">— F</span>
                </div>
                <div class="flex items-center justify-between py-2.5">
                    <span class="font-body text-xs text-white/45">Commission TTC</span>
                    <span class="font-display font-semibold text-xs text-bimo-gold" id="rp-comm-ttc">— F</span>
                </div>

                <div class="mt-3 p-3.5 bg-bimo-gold/10 border border-bimo-gold/20 rounded-[9px]">
                    <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-gold/60 mb-1">Net propriétaire</div>
                    <div class="font-display font-extrabold text-xl text-bimo-gold" id="rp-net">— FCFA</div>
                </div>

                <div class="mt-4 pt-4 border-t border-white/[7%]">
                    <div class="font-body font-medium text-[9px] uppercase tracking-widest text-white/25 mb-2.5">Rappel fiscal</div>
                    <div class="font-body text-[11px] text-white/35 leading-relaxed">
                        Commission HT × 18% = TVA (Art. 357 CGI SN)<br>
                        TVA loyer 18% si bail commercial/meublé<br>
                        BRS 5% si locataire entreprise (Art. 201 CGI SN)
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
</form>

@push('scripts')
<script>
function fmt(n) { return Math.round(n).toLocaleString('fr-FR') + ' F'; }

function calcRecap() {
    const loyer = parseFloat(document.getElementById('loyer_mensuel')?.value) || 0;
    const taux  = parseFloat(document.getElementById('taux_commission')?.value) || 0;
    const commHt = Math.round(loyer * taux / 100);
    const tva    = Math.round(commHt * 0.18);
    const ttc    = commHt + tva;
    const net    = loyer - ttc;
    document.getElementById('rp-loyer').textContent    = fmt(loyer);
    document.getElementById('rp-comm').textContent     = fmt(commHt);
    document.getElementById('rp-tva').textContent      = fmt(tva);
    document.getElementById('rp-comm-ttc').textContent = fmt(ttc);
    document.getElementById('rp-net').textContent      = fmt(net) + ' FCFA';
}

document.addEventListener('DOMContentLoaded', function () {

function previewPhotos(files) {
    const grid = document.getElementById('preview-grid');
    grid.innerHTML = '';
    if (!files || !files.length) { grid.style.display = 'none'; return; }
    grid.style.display = 'grid';
    [...files].forEach((file, i) => {
        const reader = new FileReader();
        reader.onload = e => {
            const div = document.createElement('div');
            div.style.cssText = 'position:relative;border-radius:8px;overflow:hidden;height:90px;border:1px solid rgba(27,79,107,0.1)';
            div.innerHTML = `<img src="${e.target.result}" style="width:100%;height:100%;object-fit:cover">
                ${i === 0 ? '<span style="position:absolute;top:4px;left:4px;background:#C9A84C;color:#1B4F6B;font-size:9px;font-weight:700;padding:2px 6px;border-radius:4px;font-family:Syne,sans-serif">Principale</span>' : ''}`;
            grid.appendChild(div);
        };
        reader.readAsDataURL(file);
    });
    document.getElementById('drop-zone').style.borderColor = '#C9A84C';
    document.getElementById('drop-zone').style.background  = '#fffbeb';
}

function handleDrop(e) {
    e.preventDefault();
    document.getElementById('drop-zone').style.borderColor = '';
    document.getElementById('drop-zone').style.background  = '';
    const input = document.getElementById('photos-input');
    try { input.files = e.dataTransfer.files; } catch(err) {}
    previewPhotos(e.dataTransfer.files);
}

calcRecap();
</script>
@endpush

@endsection
