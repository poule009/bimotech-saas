@extends('layouts.app')
@section('header', 'Immeubles › Nouveau')

@section('content')

{{-- Modal limite --}}
@if(session('upgrade_required'))
@php $up = session('upgrade_required'); @endphp
<div id="modal-limite" class="fixed inset-0 bg-bimo-navy/65 z-[1000] flex items-center justify-center p-5">
    <div class="bg-white rounded-[20px] p-8 max-w-sm w-full shadow-xl">
        <div class="w-12 h-12 rounded-[12px] bg-amber-50 flex items-center justify-center mb-4">
            <svg class="w-6 h-6 text-amber-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        </div>
        <h2 class="font-display font-bold text-lg text-bimo-text mb-3">Limite atteinte</h2>
        <p class="font-body text-sm text-bimo-text/70 leading-relaxed mb-6">
            Vous gérez déjà <strong>{{ $up['nb_unites'] }} unités sur {{ $up['limite'] }}</strong>
            autorisées en plan <strong>{{ $up['plan_actuel'] }}</strong>.<br><br>
            Le plan <strong>{{ $up['plan_suivant'] }}</strong> permet d'en gérer jusqu'à <strong>{{ $up['limite_suivante'] }}</strong>.
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

<div class="flex items-center gap-2 font-body text-sm text-bimo-text/40 mb-5">
    <a href="{{ route('admin.immeubles.index') }}" class="hover:text-bimo-text transition-colors duration-150">Immeubles</a>
    <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
    <span class="text-bimo-text font-medium">Nouvel immeuble</span>
</div>
<div class="mb-5">
    <h1 class="font-display font-extrabold text-xl md:text-2xl text-bimo-text tracking-tight">Ajouter un immeuble</h1>
    <p class="font-body text-sm text-bimo-text/50 mt-1">Un immeuble regroupe plusieurs appartements, studios ou bureaux sous une même adresse.</p>
</div>

<form method="POST" action="{{ route('admin.immeubles.store') }}" id="form-immeuble">
@csrf

<div class="grid grid-cols-1 lg:grid-cols-[1fr_340px] gap-5 items-start">

    {{-- ═══ COLONNE GAUCHE ═══ --}}
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
                        <option value="{{ $p->id }}" {{ old('proprietaire_id') == $p->id ? 'selected' : '' }}>
                            {{ $p->name }} — {{ $p->email }}
                        </option>
                        @endforeach
                    </select>
                    @error('proprietaire_id')<p class="mt-1 font-body text-xs text-bimo-red">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        {{-- Informations générales --}}
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
                    <input type="text" name="nom" id="nom-immeuble" value="{{ old('nom') }}"
                           placeholder="Ex: Résidence Fann, Immeuble Plateau…"
                           oninput="updatePreview()"
                           class="w-full px-4 py-3 rounded-[10px] bg-white border font-body text-sm text-bimo-text
                                  placeholder:text-bimo-text/30 focus:outline-none focus:ring-2 transition-all duration-150
                                  @error('nom') border-bimo-red focus:border-bimo-red focus:ring-bimo-red/15
                                  @else border-bimo-navy/20 focus:border-bimo-gold focus:ring-bimo-gold/15 @enderror">
                    @error('nom')<p class="mt-1 font-body text-xs text-bimo-red">{{ $message }}</p>@enderror
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
                    <input type="text" name="adresse" value="{{ old('adresse') }}" placeholder="Rue, numéro"
                           class="w-full px-4 py-3 rounded-[10px] bg-white border font-body text-sm text-bimo-text
                                  placeholder:text-bimo-text/30 focus:outline-none focus:ring-2 transition-all duration-150
                                  @error('adresse') border-bimo-red focus:border-bimo-red focus:ring-bimo-red/15
                                  @else border-bimo-navy/20 focus:border-bimo-gold focus:ring-bimo-gold/15 @enderror">
                    @error('adresse')<p class="mt-1 font-body text-xs text-bimo-red">{{ $message }}</p>@enderror
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

        {{-- Création des appartements --}}
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden"
             x-data="{ avecUnites: {{ old('avec_unites') === '1' ? 'true' : 'false' }} }">
            <input type="hidden" name="avec_unites" x-bind:value="avecUnites ? '1' : '0'">
            <div class="flex items-center justify-between px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-[8px] bg-bimo-navy/10 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-bimo-text/50" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    </div>
                    <div>
                        <span class="font-display font-bold text-sm text-bimo-text">Créer les appartements maintenant</span>
                        <p class="font-body text-xs text-bimo-text/40 mt-0.5">Optionnel — vous pouvez les ajouter depuis la fiche immeuble</p>
                    </div>
                </div>
                <button type="button" @click="avecUnites = !avecUnites; _avecUnites = avecUnites; updatePreview()"
                        :class="avecUnites ? 'bg-[var(--ac)]' : 'bg-bimo-navy/20'"
                        class="relative w-11 h-6 rounded-full transition-colors duration-200 flex-shrink-0">
                    <span :class="avecUnites ? 'translate-x-5' : 'translate-x-1'"
                          class="absolute top-1 w-4 h-4 rounded-full bg-white shadow transition-transform duration-200"></span>
                </button>
            </div>

            <div x-show="avecUnites"
                 x-transition:enter="transition duration-150"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 class="px-5 py-5 space-y-5">

                {{-- Mode --}}
                <div class="space-y-2">
                    <label class="block font-body font-medium text-sm text-bimo-text">Comment sont organisés les appartements ?</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label id="card-simple"
                               class="flex items-start gap-3 cursor-pointer p-4 border-2 rounded-[12px] transition-all duration-150 border-[var(--ac)] bg-[var(--ac)]/[4%]">
                            <input type="radio" name="mode_numerotation" value="simple"
                                   {{ old('mode_numerotation', 'simple') !== 'etage' ? 'checked' : '' }}
                                   onchange="setMode('simple')"
                                   class="mt-0.5 flex-shrink-0 accent-[var(--ac)]">
                            <div>
                                <div class="font-display font-bold text-sm text-bimo-text">Sans distinction d'étage</div>
                                <div class="font-body text-xs text-bimo-text/50 mt-0.5 leading-relaxed">Appartements numérotés simplement.</div>
                            </div>
                        </label>
                        <label id="card-etage"
                               class="flex items-start gap-3 cursor-pointer p-4 border-2 border-bimo-navy/15 rounded-[12px] transition-all duration-150">
                            <input type="radio" name="mode_numerotation" value="etage"
                                   {{ old('mode_numerotation') === 'etage' ? 'checked' : '' }}
                                   onchange="setMode('etage')"
                                   class="mt-0.5 flex-shrink-0 accent-[var(--ac)]">
                            <div>
                                <div class="font-display font-bold text-sm text-bimo-text">Par étage</div>
                                <div class="font-body text-xs text-bimo-text/50 mt-0.5 leading-relaxed">RDC, 1er étage, 2ème étage…</div>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- MODE SIMPLE --}}
                <div id="bloc-simple" class="{{ old('mode_numerotation') === 'etage' ? 'hidden' : '' }} space-y-4">
                    <div class="space-y-1.5">
                        <label class="block font-body font-medium text-sm text-bimo-text">Nombre d'appartements <span class="text-bimo-red">*</span></label>
                        <input type="number" name="nombre_unites" id="nombre_unites"
                               value="{{ old('nombre_unites', 1) }}" min="1" max="999"
                               oninput="updatePreview()"
                               class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                      focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                        @error('nombre_unites')<p class="mt-1 font-body text-xs text-bimo-red">{{ $message }}</p>@enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-1.5">
                            <label class="block font-body font-medium text-sm text-bimo-text">Type <span class="text-bimo-red">*</span></label>
                            <select name="type_unite"
                                    class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                           focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150 cursor-pointer">
                                <option value="">— Choisir —</option>
                                @foreach(\App\Models\Bien::TYPES as $val => $label)
                                <option value="{{ $val }}" {{ old('type_unite') === $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('type_unite')<p class="mt-1 font-body text-xs text-bimo-red">{{ $message }}</p>@enderror
                        </div>
                        <div class="space-y-1.5">
                            <label class="block font-body font-medium text-sm text-bimo-text">Loyer (FCFA) <span class="text-bimo-red">*</span></label>
                            <input type="number" name="loyer_par_unite" id="loyer_par_unite"
                                   value="{{ old('loyer_par_unite') }}" min="0" step="500"
                                   oninput="updatePreview()"
                                   class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                          focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                            @error('loyer_par_unite')<p class="mt-1 font-body text-xs text-bimo-red">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-1.5">
                            <label class="block font-body font-medium text-sm text-bimo-text">
                                Charges (FCFA/mois) <span class="font-normal text-bimo-text/40 text-xs">(optionnel)</span>
                            </label>
                            <input type="number" name="charges_par_unite" id="charges_par_unite"
                                   value="{{ old('charges_par_unite', 0) }}" min="0" step="500"
                                   oninput="updatePreview()"
                                   class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                          focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                        </div>
                        <div class="space-y-1.5">
                            <label class="block font-body font-medium text-sm text-bimo-text">
                                Caution (FCFA) <span class="font-normal text-bimo-text/40 text-xs">(optionnel)</span>
                            </label>
                            <input type="number" name="caution_par_unite"
                                   value="{{ old('caution_par_unite') }}" min="0" step="500"
                                   class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                          focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                        </div>
                    </div>
                </div>

                {{-- MODE PAR ÉTAGE --}}
                <div id="bloc-etage" class="{{ old('mode_numerotation') === 'etage' ? '' : 'hidden' }} space-y-4">

                    {{-- RDC --}}
                    <label class="flex items-center gap-3 cursor-pointer p-4 border border-bimo-navy/10 rounded-[12px] hover:border-[var(--ac)]/40 transition-all duration-150">
                        <input type="checkbox" name="avec_rdc" id="avec_rdc" value="1"
                               {{ old('avec_unites') === null || old('avec_rdc') ? 'checked' : '' }}
                               onchange="onRdcChange()"
                               class="w-4 h-4 rounded flex-shrink-0"
                               style="accent-color: var(--ac)">
                        <div>
                            <div class="font-body font-semibold text-sm text-bimo-text">L'immeuble a un rez-de-chaussée (RDC)</div>
                            <div class="font-body text-xs text-bimo-text/40 mt-0.5">Le RDC compte comme un niveau à part entière</div>
                        </div>
                    </label>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-1.5">
                            <label class="block font-body font-medium text-sm text-bimo-text">Étages au-dessus du RDC <span class="text-bimo-red">*</span></label>
                            <input type="number" name="nombre_etages" id="nombre_etages"
                                   value="{{ old('nombre_etages', 2) }}" min="0" max="99"
                                   oninput="updatePreview()"
                                   class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                          focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                            <p class="font-body text-[11px] text-bimo-text/35">0 si immeuble RDC seul</p>
                            @error('nombre_etages')<p class="font-body text-xs text-bimo-red">{{ $message }}</p>@enderror
                        </div>
                        <div class="space-y-1.5">
                            <label class="block font-body font-medium text-sm text-bimo-text">Appartements par étage <span class="text-bimo-red">*</span></label>
                            <input type="number" name="unites_par_niveau" id="unites_par_niveau"
                                   value="{{ old('unites_par_niveau', 2) }}" min="1" max="26"
                                   oninput="updatePreview()"
                                   class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                          focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                            <p class="font-body text-[11px] text-bimo-text/35">Max 26 (A à Z)</p>
                            @error('unites_par_niveau')<p class="font-body text-xs text-bimo-red">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    {{-- ★ AMÉLIORATION 5 — RDC type/loyer différent --}}
                    <div id="bloc-rdc-opts" class="{{ (old('avec_unites') === '1' && old('avec_rdc')) || old('avec_unites') === null ? '' : 'hidden' }}">
                        <label class="flex items-center gap-2.5 cursor-pointer p-3 rounded-[10px] border border-dashed border-bimo-navy/20 hover:border-[var(--ac)]/50 transition-all duration-150">
                            <input type="checkbox" name="rdc_different" id="rdc_different" value="1"
                                   {{ old('rdc_different') ? 'checked' : '' }}
                                   onchange="onRdcDifferentChange(); updatePreview()"
                                   class="w-4 h-4 rounded flex-shrink-0"
                                   style="accent-color: var(--ac)">
                            <div class="font-body text-sm text-bimo-text/70">Le RDC a un <strong>type / loyer différent</strong> des autres étages</div>
                        </label>

                        <div id="bloc-rdc-override" class="{{ old('rdc_different') ? '' : 'hidden' }} mt-3 pl-4 border-l-2 border-[var(--ac)]/30 space-y-3">
                            <p class="font-body font-medium text-xs uppercase tracking-widest text-bimo-text/50 pt-1">RDC uniquement</p>
                            <div class="grid grid-cols-2 gap-3">
                                <div class="space-y-1.5">
                                    <label class="block font-body font-medium text-sm text-bimo-text">Type RDC <span class="text-bimo-red">*</span></label>
                                    <select name="rdc_type" id="rdc_type"
                                            onchange="updatePreview()"
                                            class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                                   focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150 cursor-pointer">
                                        <option value="">— Choisir —</option>
                                        @foreach(\App\Models\Bien::TYPES as $val => $label)
                                        <option value="{{ $val }}" {{ old('rdc_type') === $val ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('rdc_type')<p class="mt-1 font-body text-xs text-bimo-red">{{ $message }}</p>@enderror
                                </div>
                                <div class="space-y-1.5">
                                    <label class="block font-body font-medium text-sm text-bimo-text">Loyer RDC (FCFA) <span class="text-bimo-red">*</span></label>
                                    <input type="number" name="rdc_loyer" id="rdc_loyer"
                                           value="{{ old('rdc_loyer') }}" min="0" step="500"
                                           oninput="updatePreview()"
                                           class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                                  focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                                    @error('rdc_loyer')<p class="mt-1 font-body text-xs text-bimo-red">{{ $message }}</p>@enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Global type + loyer pour les étages (hors RDC si override actif) --}}
                    <div class="space-y-3">
                        <p id="label-global-hint" class="font-body text-xs text-bimo-text/40">
                            {{ old('rdc_different') ? 'Pour les étages (hors RDC)' : 'Pour tous les appartements' }}
                        </p>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="space-y-1.5">
                                <label class="block font-body font-medium text-sm text-bimo-text">Type <span class="text-bimo-red">*</span></label>
                                <select name="type_unite" id="type_unite_etage"
                                        class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                               focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150 cursor-pointer">
                                    <option value="">— Choisir —</option>
                                    @foreach(\App\Models\Bien::TYPES as $val => $label)
                                    <option value="{{ $val }}" {{ old('type_unite') === $val ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('type_unite')<p class="mt-1 font-body text-xs text-bimo-red">{{ $message }}</p>@enderror
                            </div>
                            <div class="space-y-1.5">
                                <label class="block font-body font-medium text-sm text-bimo-text">Loyer (FCFA) <span class="text-bimo-red">*</span></label>
                                <input type="number" name="loyer_par_unite" id="loyer_par_unite_etage"
                                       value="{{ old('loyer_par_unite') }}" min="0" step="500"
                                       oninput="updatePreview()"
                                       class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                              focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                                @error('loyer_par_unite')<p class="mt-1 font-body text-xs text-bimo-red">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </div>

                    {{-- Charges + Caution (globales pour le mode étage) --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-1.5">
                            <label class="block font-body font-medium text-sm text-bimo-text">
                                Charges (FCFA/mois) <span class="font-normal text-bimo-text/40 text-xs">(optionnel)</span>
                            </label>
                            <input type="number" name="charges_par_unite" id="charges_par_unite_etage"
                                   value="{{ old('charges_par_unite', 0) }}" min="0" step="500"
                                   oninput="updatePreview()"
                                   class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                          focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                        </div>
                        <div class="space-y-1.5">
                            <label class="block font-body font-medium text-sm text-bimo-text">
                                Caution (FCFA) <span class="font-normal text-bimo-text/40 text-xs">(optionnel)</span>
                            </label>
                            <input type="number" name="caution_par_unite" id="caution_par_unite_etage"
                                   value="{{ old('caution_par_unite') }}" min="0" step="500"
                                   class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                          focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                        </div>
                    </div>
                </div>

                {{-- Commission (commune aux deux modes) --}}
                <div class="space-y-1.5">
                    <label class="block font-body font-medium text-sm text-bimo-text">
                        Commission agence (%) <span class="font-normal text-bimo-text/40 text-xs ml-1">(défaut 10%)</span>
                    </label>
                    <input type="number" name="taux_commission" id="taux_commission"
                           value="{{ old('taux_commission', 10) }}" min="0" max="30" step="0.5"
                           oninput="updatePreview()"
                           class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                  focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                </div>

            </div>
        </div>

        {{-- ★ AMÉLIORATION 3 — Aperçu mobile (entre appartements et description) --}}
        <div id="card-apercu" class="lg:hidden hidden bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
                <span class="font-display font-bold text-sm text-bimo-text">Aperçu</span>
                <span id="apercu-count"
                      class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-body font-medium
                             bg-[var(--ac)]/10 border border-[var(--ac)]/20 text-[var(--ac)]">0 bien</span>
            </div>
            <div class="overflow-y-auto max-h-64"><div id="apercu-list"></div></div>
            <div id="apercu-fiscal" class="hidden px-5 py-4 border-t border-bimo-navy/[5%] bg-bimo-gold/[4%]">
                <div class="flex justify-between mb-1.5">
                    <span class="font-body text-xs text-bimo-text/50">Loyer total / mois</span>
                    <span class="font-display font-semibold text-xs text-bimo-gold" id="ap-loyer-total">—</span>
                </div>
                <div class="flex justify-between">
                    <span class="font-body text-xs text-bimo-text/50">Net proprio total</span>
                    <span class="font-display font-semibold text-xs text-bimo-text" id="ap-net-total">—</span>
                </div>
            </div>
        </div>

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
                <textarea name="description" rows="3"
                          placeholder="Équipements communs, gardien, ascenseur, parking collectif…"
                          class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                 placeholder:text-bimo-text/30 focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15
                                 transition-all duration-150 resize-y">{{ old('description') }}</textarea>
            </div>
            <div class="sticky bottom-0 flex items-center justify-end gap-3 px-5 py-4
                        bg-white/95 backdrop-blur-sm border-t border-bimo-navy/[5%]">
                <a href="{{ route('admin.immeubles.index') }}"
                   class="px-5 py-2.5 border border-bimo-navy/15 rounded-[10px] font-body text-sm text-bimo-text/60
                          hover:text-bimo-text hover:border-bimo-navy/30 transition-all duration-150">
                    Annuler
                </a>
                <button type="submit" id="submit-btn"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-[var(--ac)] text-white
                               font-display font-bold text-sm rounded-[10px] hover:opacity-90 transition-all duration-150">
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    <span id="btn-label">Créer l'immeuble</span>
                </button>
            </div>
        </div>

    </div>

    {{-- ═══ COLONNE DROITE ═══ --}}
    <div class="lg:sticky lg:top-6 space-y-4">

        {{-- Guide contextuel --}}
        <div class="bg-bimo-navy rounded-[14px] overflow-hidden">
            <div class="px-5 py-4 border-b border-white/[7%]">
                <div class="font-display font-bold text-sm text-white">Immeuble ou bien seul ?</div>
            </div>
            <div class="px-5 py-4 space-y-3">
                <div class="flex items-start gap-3 p-3.5 bg-bimo-gold/10 border border-bimo-gold/20 rounded-[10px]">
                    <svg class="w-4 h-4 text-bimo-gold flex-shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="18" rx="2"/><line x1="2" y1="9" x2="22" y2="9"/><line x1="12" y1="3" x2="12" y2="21"/></svg>
                    <div>
                        <div class="font-body font-semibold text-xs text-bimo-gold mb-1">Immeuble (ici)</div>
                        <div class="font-body text-[11px] text-bimo-gold/70 leading-relaxed">Bâtiment avec plusieurs appartements ou bureaux.</div>
                    </div>
                </div>
                <div class="flex items-start gap-3 p-3.5 bg-white/[5%] border border-white/10 rounded-[10px]">
                    <svg class="w-4 h-4 text-white/50 flex-shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    <div>
                        <div class="font-body font-semibold text-xs text-white/70 mb-1">Bien seul → section Biens</div>
                        <div class="font-body text-[11px] text-white/40 leading-relaxed">Villa, studio isolé, terrain, local commercial seul.</div>
                    </div>
                </div>
                <div class="flex items-start gap-3 p-3.5 bg-white/[5%] border border-white/10 rounded-[10px]">
                    <svg class="w-4 h-4 text-white/50 flex-shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><path d="M9 22V12h6v10"/><rect x="2" y="3" width="5" height="5"/></svg>
                    <div>
                        <div class="font-body font-semibold text-xs text-white/70 mb-1">Commerce + appartements</div>
                        <div class="font-body text-[11px] text-white/40 leading-relaxed">Mode "Par étage" → cocher "Le RDC a un type différent".</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ★ AMÉLIORATION 3 — Aperçu desktop --}}
        <div id="card-apercu-d" class="hidden bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
                <span class="font-display font-bold text-sm text-bimo-text">Aperçu</span>
                <span id="apercu-count-d"
                      class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-body font-medium
                             bg-[var(--ac)]/10 border border-[var(--ac)]/20 text-[var(--ac)]">0 bien</span>
            </div>
            <div class="overflow-y-auto max-h-72"><div id="apercu-list-d"></div></div>
            <div id="apercu-fiscal-d" class="hidden px-5 py-4 border-t border-bimo-navy/[5%] bg-bimo-gold/[4%]">
                <div class="flex justify-between mb-1.5">
                    <span class="font-body text-xs text-bimo-text/50">Loyer total / mois</span>
                    <span class="font-display font-semibold text-xs text-bimo-gold" id="ap-loyer-total-d">—</span>
                </div>
                <div class="flex justify-between">
                    <span class="font-body text-xs text-bimo-text/50">Net proprio total</span>
                    <span class="font-display font-semibold text-xs text-bimo-text" id="ap-net-total-d">—</span>
                </div>
            </div>
        </div>

    </div>

</div>
</form>

@push('scripts')
<script>
let currentMode = '{{ old('mode_numerotation', 'simple') }}';
let _avecUnites = {{ old('avec_unites') === '1' ? 'true' : 'false' }};

// ── Mode ------------------------------------------------------------------

function setMode(mode) {
    currentMode = mode;
    document.getElementById('bloc-simple').className = (mode === 'simple' ? '' : 'hidden ') + 'space-y-4';
    document.getElementById('bloc-etage').className  = (mode === 'etage'  ? '' : 'hidden ') + 'space-y-4';

    const cSimple = document.getElementById('card-simple');
    const cEtage  = document.getElementById('card-etage');
    if (mode === 'simple') {
        cSimple.classList.add('border-[var(--ac)]', 'bg-[var(--ac)]/[4%]');
        cSimple.classList.remove('border-bimo-navy/15');
        cEtage.classList.remove('border-[var(--ac)]', 'bg-[var(--ac)]/[4%]');
        cEtage.classList.add('border-bimo-navy/15');
    } else {
        cEtage.classList.add('border-[var(--ac)]', 'bg-[var(--ac)]/[4%]');
        cEtage.classList.remove('border-bimo-navy/15');
        cSimple.classList.remove('border-[var(--ac)]', 'bg-[var(--ac)]/[4%]');
        cSimple.classList.add('border-bimo-navy/15');
    }

    // Désactiver le bloc caché pour éviter la soumission de champs en double
    document.querySelectorAll('#bloc-simple input, #bloc-simple select').forEach(el => { el.disabled = (mode === 'etage'); });
    document.querySelectorAll('#bloc-etage input, #bloc-etage select').forEach(el => { el.disabled = (mode === 'simple'); });

    updatePreview();
}

// ── RDC callbacks --------------------------------------------------------

function onRdcChange() {
    const avecRdc = document.getElementById('avec_rdc')?.checked;
    const blocOpts = document.getElementById('bloc-rdc-opts');
    if (blocOpts) blocOpts.classList.toggle('hidden', !avecRdc);
    if (!avecRdc) {
        const cb = document.getElementById('rdc_different');
        if (cb) cb.checked = false;
        document.getElementById('bloc-rdc-override')?.classList.add('hidden');
        const hint = document.getElementById('label-global-hint');
        if (hint) hint.textContent = 'Pour tous les appartements';
    }
    updatePreview();
}

function onRdcDifferentChange() {
    const diff = document.getElementById('rdc_different')?.checked;
    const bloc = document.getElementById('bloc-rdc-override');
    if (bloc) bloc.classList.toggle('hidden', !diff);
    const hint = document.getElementById('label-global-hint');
    if (hint) hint.textContent = diff ? 'Pour les étages (hors RDC)' : 'Pour tous les appartements';
}

// ── Génération des unités ------------------------------------------------

function generateUnits() {
    const nom = document.getElementById('nom-immeuble')?.value.trim() || 'Immeuble';

    if (currentMode === 'simple') {
        const nb    = parseInt(document.getElementById('nombre_unites')?.value) || 0;
        const loyer = parseFloat(document.getElementById('loyer_par_unite')?.value) || 0;
        return Array.from({ length: nb }, (_, i) => ({ name: nom + ' — Appt ' + (i + 1), loyer }));
    }

    const avecRdc      = document.getElementById('avec_rdc')?.checked ?? true;
    const nbEtages     = parseInt(document.getElementById('nombre_etages')?.value) || 0;
    const apptParEtage = Math.min(parseInt(document.getElementById('unites_par_niveau')?.value) || 1, 26);
    const globalLoyer  = parseFloat(document.getElementById('loyer_par_unite_etage')?.value) || 0;
    const rdcDiff      = document.getElementById('rdc_different')?.checked ?? false;
    const rdcLoyer     = rdcDiff
        ? (parseFloat(document.getElementById('rdc_loyer')?.value) || globalLoyer)
        : globalLoyer;

    const floors = [];
    if (avecRdc) floors.push({ label: 'RDC', loyer: rdcLoyer });
    for (let i = 1; i <= nbEtages; i++) {
        floors.push({ label: i === 1 ? '1er étage' : i + 'ème étage', loyer: globalLoyer });
    }

    return floors.flatMap(floor =>
        apptParEtage === 1
            ? [{ name: nom + ' — ' + floor.label, loyer: floor.loyer }]
            : Array.from({ length: apptParEtage }, (_, j) => ({
                  name:  nom + ' — ' + floor.label + ' ' + String.fromCharCode(65 + j),
                  loyer: floor.loyer,
              }))
    );
}

function fmt(n) { return Math.round(n).toLocaleString('fr-FR') + ' F'; }

// ── Rendu d'un panneau aperçu -------------------------------------------

function renderApercuTo(sfx, units) {
    const id    = sfx ? '-' + sfx : '';
    const card  = document.getElementById('card-apercu' + id);
    if (!card) return;

    const count  = document.getElementById('apercu-count' + id);
    const list   = document.getElementById('apercu-list' + id);
    const fiscal = document.getElementById('apercu-fiscal' + id);
    const lTotal = document.getElementById('ap-loyer-total' + id);
    const nTotal = document.getElementById('ap-net-total' + id);
    const taux   = parseFloat(document.getElementById('taux_commission')?.value) || 10;

    if (!_avecUnites) {
        card.classList.add('hidden');
        return;
    }

    // ★ AMÉLIORATION 1 — Avertissement zéro bien
    if (units.length === 0) {
        card.classList.remove('hidden');
        if (count) {
            count.textContent = '0 bien';
            count.style.cssText = 'background:#fffbeb;border-color:#fde68a;color:#92400e';
        }
        if (list) list.innerHTML = `
            <div class="px-5 py-7 text-center">
                <svg class="w-8 h-8 mx-auto mb-3" style="color:#d97706" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
                </svg>
                <p class="font-body text-sm font-medium" style="color:#92400e">Aucun appartement ne sera créé</p>
                <p class="font-body text-xs text-bimo-text/50 mt-1">Cochez "RDC" ou indiquez des étages au-dessus.</p>
            </div>
        `;
        if (fiscal) fiscal.classList.add('hidden');
        return;
    }

    card.classList.remove('hidden');
    if (count) {
        count.textContent = units.length + ' bien' + (units.length > 1 ? 's' : '');
        count.style.cssText = '';
    }

    if (list) {
        list.innerHTML = units.map((u, i) => `
            <div class="flex items-center justify-between px-5 py-3 ${i % 2 !== 0 ? 'bg-bimo-bg/50' : ''}">
                <span class="font-body text-sm text-bimo-text truncate pr-2">${u.name}</span>
                ${u.loyer ? `<span class="font-display font-semibold text-xs text-bimo-gold flex-shrink-0">${fmt(u.loyer)}</span>` : ''}
            </div>
        `).join('');
    }

    const totalLoyer = units.reduce((s, u) => s + (u.loyer || 0), 0);
    if (fiscal) {
        if (totalLoyer > 0) {
            fiscal.classList.remove('hidden');
            const commTtc = Math.round(totalLoyer * taux / 100 * 1.18);
            if (lTotal) lTotal.textContent = fmt(totalLoyer);
            if (nTotal) nTotal.textContent = fmt(totalLoyer - commTtc);
        } else {
            fiscal.classList.add('hidden');
        }
    }
}

// ── Mise à jour globale --------------------------------------------------

function updatePreview() {
    const units = generateUnits();

    renderApercuTo('', units);   // mobile (dans la colonne gauche)
    renderApercuTo('d', units);  // desktop (colonne droite)

    const btn = document.getElementById('btn-label');
    const submitBtn = document.getElementById('submit-btn');

    const zeroWhenActive = _avecUnites && currentMode === 'etage' && units.length === 0;
    if (submitBtn) {
        submitBtn.disabled = zeroWhenActive;
        submitBtn.classList.toggle('opacity-50', zeroWhenActive);
        submitBtn.classList.toggle('cursor-not-allowed', zeroWhenActive);
    }

    if (btn) {
        if (!_avecUnites || units.length === 0) {
            btn.textContent = "Créer l'immeuble";
        } else if (units.length === 1) {
            btn.textContent = "Créer l'immeuble et 1 appartement";
        } else {
            btn.textContent = `Créer l'immeuble et ses ${units.length} appartements`;
        }
    }
}

// ── Init -----------------------------------------------------------------
setMode(currentMode);
onRdcDifferentChange(); // sync label global hint selon old()
updatePreview();
</script>
@endpush

@endsection
