@extends('layouts.app')
@section('header', 'Contrats › Nouveau')

@section('content')

{{-- Breadcrumb --}}
<div class="flex items-center gap-2 font-body text-sm text-bimo-text/40 mb-5">
    <a href="{{ route('admin.contrats.index') }}" class="hover:text-bimo-text transition-colors duration-150">Contrats</a>
    <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
    <span class="text-bimo-text font-medium">{{ $fromContrat ? 'Renouveler' : 'Nouveau contrat' }}</span>
</div>

<div class="mb-5">
    <h1 class="font-display font-extrabold text-xl md:text-2xl text-bimo-text tracking-tight">
        @if($fromContrat) Renouveler le contrat @else Créer un contrat de bail @endif
    </h1>
    <p class="font-body text-sm text-bimo-text/50 mt-1">
        @if($fromContrat)
            Renouvellement de {{ $fromContrat->reference_bail ?? 'BAIL-'.$fromContrat->id }} — données pré-remplies, ajustez la durée.
        @else
            Le loyer contractuel = loyer nu + charges + TOM.
        @endif
    </p>
</div>

{{-- Bannière renouvellement --}}
@if($fromContrat)
<div class="flex items-center gap-3 bg-bimo-gold/[8%] border border-bimo-gold/25 rounded-[12px] px-4 py-3 mb-5">
    <svg class="w-4 h-4 text-bimo-gold flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M23 4v6h-6"/><path d="M20.49 15a9 9 0 11-2.12-9.36L23 10"/></svg>
    <p class="font-body text-sm text-bimo-gold">
        Renouvellement — vérifiez les montants et définissez la nouvelle durée. Le locataire et le bien restent inchangés.
    </p>
</div>
@endif

<form method="POST" action="{{ route('admin.contrats.store') }}" id="form-contrat">
@csrf

<div class="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-5 items-start">

    {{-- ═══ COLONNE GAUCHE ═══ --}}
    <div class="space-y-4">

        {{-- BIEN & LOCATAIRE --}}
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
            <div class="flex items-center gap-3 px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
                <div class="w-8 h-8 rounded-[8px] bg-bimo-gold/15 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-bimo-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                </div>
                <span class="font-display font-bold text-sm text-bimo-text">Bien & Locataire</span>
            </div>
            <div class="px-5 py-5 space-y-4">

                {{-- Bien --}}
                <div class="space-y-1.5">
                    <label class="block font-body font-medium text-sm text-bimo-text">Bien à louer <span class="text-bimo-red">*</span></label>
                    <select name="bien_id" id="bien_id" onchange="chargerInfosBien(this)"
                            class="w-full px-4 py-3 rounded-[10px] bg-white border font-body text-sm text-bimo-text
                                   focus:outline-none focus:ring-2 transition-all duration-150 cursor-pointer
                                   @error('bien_id') border-bimo-red focus:border-bimo-red focus:ring-bimo-red/15
                                   @else border-bimo-navy/20 focus:border-bimo-gold focus:ring-bimo-gold/15 @enderror">
                        <option value="">— Sélectionner un bien disponible —</option>
                        @foreach($biens as $bien)
                        <option value="{{ $bien->id }}"
                                data-loyer="{{ $bien->loyer_mensuel }}"
                                data-commission="{{ $bien->taux_commission }}"
                                data-proprio="{{ $bien->proprietaire?->name }}"
                                data-meuble="{{ $bien->meuble ? '1':'0' }}"
                                data-type="{{ $bien->type }}"
                                {{ old('bien_id', $bienPreselectionne?->id) == $bien->id ? 'selected':'' }}>
                            {{ $bien->reference }} — {{ \App\Models\Bien::TYPES[$bien->type] ?? $bien->type }} — {{ $bien->adresse }}, {{ $bien->ville }}
                        </option>
                        @endforeach
                    </select>
                    @error('bien_id')<p class="mt-1 font-body text-xs text-bimo-red">{{ $message }}</p>@enderror
                    <div id="info-proprio" style="display:none"
                         class="mt-2 px-3 py-2 bg-bimo-bg border border-bimo-navy/10 rounded-[8px] font-body text-xs text-bimo-text/60">
                    </div>
                </div>

                {{-- Locataire --}}
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between">
                        <label class="block font-body font-medium text-sm text-bimo-text">Locataire <span class="text-bimo-red">*</span></label>
                        <button type="button" onclick="ouvrirModalLocataire()"
                                class="inline-flex items-center gap-1.5 px-3 py-1 bg-bimo-navy/10 rounded-[6px]
                                       font-body font-semibold text-xs text-bimo-text hover:bg-bimo-navy hover:text-white transition-all duration-150">
                            <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                            Nouveau locataire
                        </button>
                    </div>
                    @php $locPreselId = old('locataire_id', $fromContrat?->locataire_id); @endphp
                    <select name="locataire_id" id="locataire_id"
                            class="w-full px-4 py-3 rounded-[10px] bg-white border font-body text-sm text-bimo-text
                                   focus:outline-none focus:ring-2 transition-all duration-150 cursor-pointer
                                   @error('locataire_id') border-bimo-red focus:border-bimo-red focus:ring-bimo-red/15
                                   @else border-bimo-navy/20 focus:border-bimo-gold focus:ring-bimo-gold/15 @enderror">
                        <option value="">— Sélectionner un locataire —</option>
                        @foreach($locataires as $loc)
                        <option value="{{ $loc->id }}" {{ $locPreselId == $loc->id ? 'selected':'' }}>
                            {{ $loc->name }} — {{ $loc->email }}
                        </option>
                        @endforeach
                    </select>
                    @error('locataire_id')<p class="mt-1 font-body text-xs text-bimo-red">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        {{-- DURÉE & TYPE --}}
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
            <div class="flex items-center gap-3 px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
                <div class="w-8 h-8 rounded-[8px] bg-bimo-navy/10 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-bimo-text/50" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                </div>
                <span class="font-display font-bold text-sm text-bimo-text">Durée & Type de bail</span>
            </div>
            <div class="px-5 py-5 space-y-4">
                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1.5">
                        <label class="block font-body font-medium text-sm text-bimo-text">Date de début <span class="text-bimo-red">*</span></label>
                        @php
                            $defaultDebut = $fromContrat?->date_fin
                                ? \Carbon\Carbon::parse($fromContrat->date_fin)->addDay()->format('Y-m-d')
                                : now()->format('Y-m-d');
                        @endphp
                        <input type="date" name="date_debut" id="date_debut"
                               value="{{ old('date_debut', $defaultDebut) }}"
                               class="w-full px-4 py-3 rounded-[10px] bg-white border font-body text-sm text-bimo-text
                                      focus:outline-none focus:ring-2 transition-all duration-150
                                      @error('date_debut') border-bimo-red focus:border-bimo-red focus:ring-bimo-red/15
                                      @else border-bimo-navy/20 focus:border-bimo-gold focus:ring-bimo-gold/15 @enderror">
                        @error('date_debut')<p class="mt-1 font-body text-xs text-bimo-red">{{ $message }}</p>@enderror
                    </div>
                    <div class="space-y-1.5">
                        <label class="block font-body font-medium text-sm text-bimo-text">
                            Date de fin <span class="font-normal text-bimo-text/40 text-xs ml-1">(optionnel)</span>
                        </label>
                        <input type="date" name="date_fin" value="{{ old('date_fin') }}"
                               class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                      focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                        <p class="font-body text-[11px] text-bimo-text/30">Laisser vide = contrat ouvert</p>
                    </div>
                </div>
                <div class="space-y-1.5">
                    <label class="block font-body font-medium text-sm text-bimo-text">Type de bail <span class="text-bimo-red">*</span></label>
                    <select name="type_bail" id="type_bail" onchange="mettreAJourRecap()"
                            class="w-full px-4 py-3 rounded-[10px] bg-white border font-body text-sm text-bimo-text cursor-pointer
                                   focus:outline-none focus:ring-2 transition-all duration-150
                                   @error('type_bail') border-bimo-red focus:border-bimo-red focus:ring-bimo-red/15
                                   @else border-bimo-navy/20 focus:border-bimo-gold focus:ring-bimo-gold/15 @enderror">
                        @foreach($typesBail as $val => $label)
                        <option value="{{ $val }}" {{ old('type_bail', $fromContrat?->type_bail ?? 'habitation') === $val ? 'selected':'' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('type_bail')<p class="mt-1 font-body text-xs text-bimo-red">{{ $message }}</p>@enderror
                </div>
                <div class="space-y-1.5">
                    <label class="block font-body font-medium text-sm text-bimo-text">
                        Référence bail <span class="font-normal text-bimo-text/40 text-xs ml-1">(optionnel)</span>
                    </label>
                    <input type="text" name="reference_bail" value="{{ old('reference_bail') }}"
                           placeholder="Ex: BAIL-2024-001 (générée auto si vide)"
                           class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                  placeholder:text-bimo-text/30 focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                </div>
            </div>
        </div>

        {{-- LOYER --}}
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
            <div class="flex items-center gap-3 px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
                <div class="w-8 h-8 rounded-[8px] bg-bimo-gold/15 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-bimo-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
                </div>
                <span class="font-display font-bold text-sm text-bimo-text">Ventilation du loyer</span>
            </div>
            <div class="px-5 py-5 space-y-4">
                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1.5">
                        <label class="block font-body font-medium text-sm text-bimo-text">Loyer nu (FCFA) <span class="text-bimo-red">*</span></label>
                        <input type="number" name="loyer_nu" id="loyer_nu"
                               value="{{ old('loyer_nu', $fromContrat?->loyer_nu ?? $bienPreselectionne?->loyer_mensuel) }}"
                               min="0" step="500" oninput="mettreAJourRecap()"
                               class="w-full px-4 py-3 rounded-[10px] bg-white border font-body text-sm text-bimo-text
                                      focus:outline-none focus:ring-2 transition-all duration-150
                                      @error('loyer_nu') border-bimo-red focus:border-bimo-red focus:ring-bimo-red/15
                                      @else border-bimo-navy/20 focus:border-bimo-gold focus:ring-bimo-gold/15 @enderror">
                        <p class="font-body text-[11px] text-bimo-text/30">Hors charges et TOM</p>
                        @error('loyer_nu')<p class="font-body text-xs text-bimo-red">{{ $message }}</p>@enderror
                    </div>
                    <div class="space-y-1.5">
                        <label class="block font-body font-medium text-sm text-bimo-text">Charges mensuelles</label>
                        <input type="number" name="charges_mensuelles" id="charges_mensuelles"
                               value="{{ old('charges_mensuelles', 0) }}" min="0" step="500" oninput="mettreAJourRecap()"
                               class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                      focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1.5">
                        <label class="block font-body font-medium text-sm text-bimo-text">TOM <span class="font-normal text-bimo-text/40 text-xs">(Taxe ordures ménagères)</span></label>
                        <input type="number" name="tom_amount" id="tom_amount"
                               value="{{ old('tom_amount', 0) }}" min="0" step="100" oninput="mettreAJourRecap()"
                               class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                      focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block font-body font-medium text-sm text-bimo-text">Indexation annuelle (%)</label>
                        <input type="number" name="indexation_annuelle"
                               value="{{ old('indexation_annuelle', 0) }}" min="0" max="20" step="0.5"
                               class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                      focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                    </div>
                </div>
            </div>
        </div>

        {{-- CAUTION & FRAIS --}}
        @php $avecCaution = old('avec_caution', '1') === '1'; @endphp
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-[8px] bg-bimo-navy/10 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-bimo-text/50" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    </div>
                    <span class="font-display font-bold text-sm text-bimo-text">Caution & Frais</span>
                </div>
                {{-- Toggle caution --}}
                <div class="flex items-center gap-1 bg-bimo-bg2 border border-bimo-navy/10 rounded-[8px] p-1">
                    <button type="button" id="btn-avec-caution" onclick="toggleCaution(true)"
                            class="px-3 py-1.5 rounded-[6px] font-body font-semibold text-xs transition-all duration-150"
                            style="background: {{ $avecCaution ? '#1B4F6B' : 'transparent' }}; color: {{ $avecCaution ? '#fff' : '#8E9BAA' }}">
                        Avec caution
                    </button>
                    <button type="button" id="btn-sans-caution" onclick="toggleCaution(false)"
                            class="px-3 py-1.5 rounded-[6px] font-body font-semibold text-xs transition-all duration-150"
                            style="background: {{ !$avecCaution ? '#1B4F6B' : 'transparent' }}; color: {{ !$avecCaution ? '#fff' : '#8E9BAA' }}">
                        Sans caution
                    </button>
                </div>
            </div>
            <input type="hidden" name="avec_caution" id="avec_caution" value="{{ old('avec_caution', '1') }}">
            <div class="px-5 py-5 space-y-4">
                <div id="bloc-caution" style="{{ $avecCaution ? '' : 'display:none' }}">
                    <div class="grid grid-cols-2 gap-3 mb-4">
                        <div class="space-y-1.5">
                            <label class="block font-body font-medium text-sm text-bimo-text">Caution (FCFA)</label>
                            <input type="number" name="caution" id="caution"
                                   value="{{ old('caution') }}" min="0" step="500"
                                   class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                          focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                            @error('caution')<p class="mt-1 font-body text-xs text-bimo-red">{{ $message }}</p>@enderror
                        </div>
                        <div class="space-y-1.5">
                            <label class="block font-body font-medium text-sm text-bimo-text">Nombre de mois</label>
                            <select name="nombre_mois_caution" id="nombre_mois_caution" onchange="calcCaution()"
                                    class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text cursor-pointer
                                           focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                                @foreach([1,2,3,6] as $n)
                                <option value="{{ $n }}" {{ old('nombre_mois_caution', 1) == $n ? 'selected':'' }}>{{ $n }} mois</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="space-y-1.5">
                    <label class="block font-body font-medium text-sm text-bimo-text">Frais d'agence HT (FCFA)</label>
                    <input type="number" name="frais_agence"
                           value="{{ old('frais_agence', 0) }}" min="0" step="500"
                           class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                  focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                    <p class="font-body text-[11px] text-bimo-text/30">Honoraires HT · TVA 18% ajoutée auto · Standard : 1 mois de loyer nu</p>
                </div>
            </div>
        </div>

        {{-- FISCAL (plan Agence) --}}
        @if(config('features.fiscalite'))
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
            <div class="flex items-center gap-3 px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
                <div class="w-8 h-8 rounded-[8px] bg-amber-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-amber-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </div>
                <span class="font-display font-bold text-sm text-bimo-text">Paramètres fiscaux</span>
            </div>
            <div class="px-5 py-5">
                @include('admin.contrats._section-fiscal', ['contrat' => null])
            </div>
        </div>
        @endif

        {{-- GARANT --}}
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
            <div class="flex items-center gap-3 px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
                <div class="w-8 h-8 rounded-[8px] bg-bimo-navy/5 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-bimo-text/40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
                </div>
                <span class="font-display font-bold text-sm text-bimo-text">
                    Garant <span class="font-normal text-bimo-text/40 text-xs ml-1">(optionnel)</span>
                </span>
            </div>
            <div class="px-5 py-5 space-y-4">
                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1.5">
                        <label class="block font-body font-medium text-sm text-bimo-text">Nom du garant</label>
                        <input type="text" name="garant_nom" value="{{ old('garant_nom') }}" placeholder="Prénom Nom"
                               class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                      placeholder:text-bimo-text/30 focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block font-body font-medium text-sm text-bimo-text">Téléphone</label>
                        <input type="text" name="garant_telephone" value="{{ old('garant_telephone') }}" placeholder="+221 7X XXX XX XX"
                               class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                      placeholder:text-bimo-text/30 focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1.5">
                        <label class="block font-body font-medium text-sm text-bimo-text">Adresse</label>
                        <input type="text" name="garant_adresse" value="{{ old('garant_adresse') }}" placeholder="Adresse complète"
                               class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                      placeholder:text-bimo-text/30 focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block font-body font-medium text-sm text-bimo-text">CNI / Pièce d'identité</label>
                        <input type="text" name="garant_cni" value="{{ old('garant_cni') }}" placeholder="N° CNI ou passeport"
                               class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                      placeholder:text-bimo-text/30 focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                    </div>
                </div>
            </div>
        </div>

        {{-- OBSERVATIONS --}}
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
            <div class="flex items-center gap-3 px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
                <div class="w-8 h-8 rounded-[8px] bg-bimo-navy/5 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-bimo-text/40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                </div>
                <span class="font-display font-bold text-sm text-bimo-text">
                    Observations <span class="font-normal text-bimo-text/40 text-xs ml-1">(optionnel)</span>
                </span>
            </div>
            <div class="px-5 py-5 space-y-4">
                <textarea name="observations" rows="3" placeholder="État des lieux, observations générales…"
                          class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                 placeholder:text-bimo-text/30 focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15
                                 transition-all duration-150 resize-y">{{ old('observations') }}</textarea>

                <div class="space-y-1.5">
                    <label class="block font-body font-medium text-sm text-bimo-text">
                        Clauses particulières
                        <span class="font-normal text-bimo-text/40 text-xs ml-1">(spécifiques à ce bail)</span>
                    </label>
                    <textarea name="clauses_particulieres" rows="5"
                              placeholder="Ex : interdiction d'animaux, travaux autorisés, conditions de résiliation anticipée…"
                              class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                     placeholder:text-bimo-text/30 focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15
                                     transition-all duration-150 resize-y">{{ old('clauses_particulieres') }}</textarea>
                    @if(auth()->user()->agency?->modele_contrat)
                    <p class="flex items-center gap-1.5 font-body text-xs text-bimo-gold">
                        <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                        Modèle de clauses agence configuré — inclus automatiquement dans le bail PDF
                    </p>
                    @else
                    <p class="flex items-center gap-1.5 font-body text-xs text-bimo-text/40">
                        <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        <a href="{{ route('admin.agency.settings') }}" class="text-[var(--ac)] hover:text-bimo-text transition-colors duration-150">
                            Configurer votre modèle de clauses agence →
                        </a>
                    </p>
                    @endif
                </div>
            </div>

            {{-- Submit --}}
            <div class="sticky bottom-0 flex items-center justify-end gap-3 px-5 py-4
                        bg-white/95 backdrop-blur-sm border-t border-bimo-navy/[5%]">
                <a href="{{ route('admin.contrats.index') }}"
                   class="px-5 py-2.5 border border-bimo-navy/15 rounded-[10px]
                          font-body text-sm text-bimo-text/60 hover:text-bimo-text hover:border-bimo-navy/30 transition-all duration-150">
                    Annuler
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-[var(--ac)] text-white
                               font-display font-bold text-sm rounded-[10px]
                               hover:opacity-90 transition-opacity duration-150">
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    Créer le contrat
                </button>
            </div>
        </div>

    </div>{{-- fin colonne gauche --}}

    {{-- ═══ COLONNE DROITE : RÉCAPITULATIF ═══ --}}
    <div class="lg:sticky lg:top-6">
        <div class="bg-bimo-navy rounded-[14px] overflow-hidden">
            <div class="px-5 py-4 border-b border-white/[7%]">
                <div class="font-display font-bold text-sm text-white">Récapitulatif</div>
            </div>
            <div class="px-5 py-4 space-y-0">

                <div class="font-body font-medium text-[9px] uppercase tracking-widest text-white/25 mb-2">Loyer</div>
                @foreach([
                    ['rp-loyer-nu',    'Loyer nu',        'text-white'],
                    ['rp-charges',     '+ Charges HT',    'text-white'],
                    ['rp-tom',         '+ TOM',           'text-white'],
                    ['rp-loyer-total', '= Total encaissé', 'text-bimo-gold font-semibold'],
                ] as [$id, $lbl, $cls])
                <div class="flex items-center justify-between py-2 border-b border-white/[6%]">
                    <span class="font-body text-xs text-white/40">{{ $lbl }}</span>
                    <span class="font-display font-semibold text-xs {{ $cls }}" id="{{ $id }}">— F</span>
                </div>
                @endforeach

                {{-- TVA loyer (conditionnel) --}}
                <div id="row-tva-loyer" style="display:none" class="flex items-center justify-between py-2 border-b border-white/[6%]">
                    <span class="font-body text-xs text-white/40">+ TVA loyer 18% <span class="opacity-60">(Art.354)</span></span>
                    <span class="font-display font-semibold text-xs text-amber-400" id="rp-tva-loyer">— F</span>
                </div>
                <div id="row-tva-charges" style="display:none" class="flex items-center justify-between py-2 border-b border-white/[6%]">
                    <span class="font-body text-xs text-white/40">+ TVA charges 18%</span>
                    <span class="font-display font-semibold text-xs text-amber-400" id="rp-tva-charges">— F</span>
                </div>

                <div class="my-3 border-t border-white/[7%]"></div>
                <div class="font-body font-medium text-[9px] uppercase tracking-widest text-white/25 mb-2">Commission</div>
                @foreach([
                    ['rp-comm-ht',  'Commission HT',    'text-bimo-gold'],
                    ['rp-tva-comm', 'TVA 18% (Art.357)', 'text-white'],
                    ['rp-comm-ttc', 'Commission TTC',   'text-bimo-gold'],
                ] as [$id, $lbl, $cls])
                <div class="flex items-center justify-between py-2 border-b border-white/[6%]">
                    <span class="font-body text-xs text-white/40">{{ $lbl }}</span>
                    <span class="font-display font-semibold text-xs {{ $cls }}" id="{{ $id }}">— F</span>
                </div>
                @endforeach

                {{-- BRS (conditionnel) --}}
                <div id="row-brs" style="display:none" class="flex items-center justify-between py-2 border-b border-white/[6%]">
                    <span class="font-body text-xs text-white/40">− BRS 5% <span class="opacity-60">(Art. 201)</span></span>
                    <span class="font-display font-semibold text-xs text-bimo-red" id="rp-brs">— F</span>
                </div>

                {{-- Net total --}}
                <div class="mt-3 p-3.5 bg-bimo-gold/10 border border-bimo-gold/20 rounded-[9px]">
                    <div class="font-body font-medium text-[9px] uppercase tracking-widest text-bimo-gold/60 mb-1">Net à verser propriétaire</div>
                    <div class="font-display font-extrabold text-xl text-bimo-gold" id="rp-net">— FCFA</div>
                </div>

                {{-- Signature --}}
                <div class="mt-4 pt-4 border-t border-white/[7%]">
                    <div class="font-body font-medium text-[9px] uppercase tracking-widest text-white/25 mb-2">À la signature</div>
                    <div class="flex items-center justify-between py-1.5">
                        <span class="font-body text-xs text-white/40">Caution</span>
                        <span class="font-display font-semibold text-xs text-white" id="rp-caution">— F</span>
                    </div>
                    <div class="flex items-center justify-between py-1.5">
                        <span class="font-body text-xs text-white/40">Taux commission</span>
                        <span class="font-display font-semibold text-xs text-white" id="rp-taux-comm">— %</span>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>
</form>

{{-- MODAL NOUVEAU LOCATAIRE --}}
<div id="modal-locataire"
     class="fixed inset-0 bg-bimo-navy/50 backdrop-blur-sm z-[200] items-center justify-center p-4"
     style="display:none">
    <div class="bg-white rounded-[20px] w-full max-w-sm shadow-xl p-6">
        <h2 class="font-display font-bold text-base text-bimo-text mb-4">Créer un nouveau locataire</h2>
        <div id="modal-error" style="display:none"
             class="flex items-start gap-2 bg-bimo-red/[5%] border border-bimo-red/20 rounded-[10px] px-4 py-3 mb-4 font-body text-sm text-bimo-red">
        </div>
        <div class="space-y-3">
            <div class="space-y-1.5">
                <label class="block font-body font-medium text-sm text-bimo-text">Nom complet <span class="text-bimo-red">*</span></label>
                <input type="text" id="loc-name" placeholder="Prénom Nom"
                       class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                              placeholder:text-bimo-text/30 focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
            </div>
            <div class="space-y-1.5">
                <label class="block font-body font-medium text-sm text-bimo-text">Email <span class="text-bimo-red">*</span></label>
                <input type="email" id="loc-email" placeholder="email@exemple.com"
                       class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                              placeholder:text-bimo-text/30 focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
            </div>
            <div class="space-y-1.5">
                <label class="block font-body font-medium text-sm text-bimo-text">Téléphone</label>
                <input type="text" id="loc-tel" placeholder="+221 7X XXX XX XX"
                       class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                              placeholder:text-bimo-text/30 focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
            </div>
            <div class="space-y-1.5">
                <label class="block font-body font-medium text-sm text-bimo-text">Mot de passe <span class="text-bimo-red">*</span></label>
                <input type="password" id="loc-pwd" placeholder="Min. 8 caractères"
                       class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                              placeholder:text-bimo-text/30 focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
            </div>
        </div>
        <div class="flex justify-end gap-3 mt-5">
            <button type="button" onclick="fermerModalLocataire()"
                    class="px-4 py-2.5 border border-bimo-navy/15 rounded-[10px] font-body text-sm text-bimo-text/60 hover:text-bimo-text hover:border-bimo-navy/30 transition-all duration-150">
                Annuler
            </button>
            <button type="button" onclick="creerLocataire()"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-[var(--ac)] text-white font-display font-bold text-sm rounded-[10px] hover:opacity-90 transition-opacity duration-150">
                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                Créer et sélectionner
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
const NAVY = '#1B4F6B';
const MUTED = '#8E9BAA';

function toggleCaution(avec) {
    document.getElementById('avec_caution').value = avec ? '1' : '0';
    document.getElementById('bloc-caution').style.display = avec ? '' : 'none';
    document.getElementById('btn-avec-caution').style.background  = avec  ? NAVY : 'transparent';
    document.getElementById('btn-avec-caution').style.color        = avec  ? '#fff' : MUTED;
    document.getElementById('btn-sans-caution').style.background  = !avec ? NAVY : 'transparent';
    document.getElementById('btn-sans-caution').style.color        = !avec ? '#fff' : MUTED;
    if (!avec) document.getElementById('caution').value = '';
}

const biensData = @json($biens->keyBy('id'));
function fmt(n) { return Math.round(n).toLocaleString('fr-FR') + ' F'; }

function chargerInfosBien(select) {
    const opt = select.options[select.selectedIndex];
    if (!opt.value) { document.getElementById('info-proprio').style.display = 'none'; mettreAJourRecap(); return; }
    const loyer = parseFloat(opt.dataset.loyer) || 0;
    const commission = parseFloat(opt.dataset.commission) || 10;
    const proprio = opt.dataset.proprio || '—';
    const meuble = opt.dataset.meuble === '1';
    const type = opt.dataset.type;
    document.getElementById('loyer_nu').value = loyer;
    const typeBailSelect = document.getElementById('type_bail');
    if (type === 'bureau' || type === 'commerce') typeBailSelect.value = 'commercial';
    else if (meuble) typeBailSelect.value = 'habitation';
    const nbMois = parseInt(document.getElementById('nombre_mois_caution').value) || 1;
    document.getElementById('caution').value = Math.round(loyer * nbMois);
    const tvaCheckbox = document.getElementById('loyer_assujetti_tva');
    if (tvaCheckbox) {
        const typeBailVal = typeBailSelect.value;
        tvaCheckbox.checked = (typeBailVal === 'commercial' || typeBailVal === 'mixte') || (meuble && (typeBailVal === 'habitation' || typeBailVal === 'saisonnier'));
        if (typeof updateFiscalBadge === 'function') updateFiscalBadge();
    }
    const infoProprio = document.getElementById('info-proprio');
    infoProprio.style.display = 'block';
    infoProprio.innerHTML = `<strong>Propriétaire :</strong> ${proprio} · <strong>Commission :</strong> ${commission}%`;
    mettreAJourRecap();
}

function calcCaution() {
    const loyer = parseFloat(document.getElementById('loyer_nu').value) || 0;
    const nbMois = parseInt(document.getElementById('nombre_mois_caution').value) || 1;
    document.getElementById('caution').value = Math.round(loyer * nbMois);
    mettreAJourRecap();
}

function mettreAJourRecap() {
    const loyerNu = parseFloat(document.getElementById('loyer_nu').value) || 0;
    const charges = parseFloat(document.getElementById('charges_mensuelles').value) || 0;
    const tom = parseFloat(document.getElementById('tom_amount').value) || 0;
    const caution = parseFloat(document.getElementById('caution').value) || 0;
    const tvaChecked = document.getElementById('loyer_assujetti_tva')?.checked ?? false;
    const brsChecked = document.getElementById('brs_applicable')?.checked ?? false;
    const chargesForait = document.getElementById('charges_assujetties_tva')?.checked ?? false;
    const bienSelect = document.getElementById('bien_id');
    const opt = bienSelect.options[bienSelect.selectedIndex];
    const tauxComm = parseFloat(opt?.dataset?.commission || 10);
    const tvaLoyer = tvaChecked ? Math.round((loyerNu + tom) * 0.18) : 0;
    const loyerTtc = loyerNu + tvaLoyer;
    const tvaCharges = chargesForait ? Math.round(charges * (tvaChecked ? 0.18 : 0)) : 0;
    const chargesTtc = charges + tvaCharges;
    const montantEncaisse = loyerTtc + chargesTtc + tom;
    const commHt = Math.round(loyerNu * tauxComm / 100);
    const tvaComm = Math.round(commHt * 0.18);
    const commTtc = commHt + tvaComm;
    const netProprio = montantEncaisse - commTtc;
    const brsAmount = brsChecked ? Math.round(loyerNu * 0.05) : 0;
    const netAVerser = netProprio - brsAmount;

    document.getElementById('rp-loyer-nu').textContent = fmt(loyerNu);
    document.getElementById('rp-charges').textContent = fmt(charges);
    document.getElementById('rp-tom').textContent = fmt(tom);
    const rowTvaCharges = document.getElementById('row-tva-charges');
    if (rowTvaCharges) rowTvaCharges.style.display = (chargesForait && tvaCharges > 0) ? 'flex' : 'none';
    const elTvaCharges = document.getElementById('rp-tva-charges');
    if (elTvaCharges) elTvaCharges.textContent = fmt(tvaCharges);
    const rowTva = document.getElementById('row-tva-loyer');
    if (rowTva) rowTva.style.display = tvaChecked ? 'flex' : 'none';
    const elTvaLoyer = document.getElementById('rp-tva-loyer');
    if (elTvaLoyer) elTvaLoyer.textContent = fmt(tvaLoyer);
    document.getElementById('rp-loyer-total').textContent = fmt(montantEncaisse);
    document.getElementById('rp-comm-ht').textContent = fmt(commHt);
    document.getElementById('rp-tva-comm').textContent = fmt(tvaComm);
    document.getElementById('rp-comm-ttc').textContent = fmt(commTtc);
    const rowBrs = document.getElementById('row-brs');
    if (rowBrs) rowBrs.style.display = brsChecked ? 'flex' : 'none';
    const elBrs = document.getElementById('rp-brs');
    if (elBrs) elBrs.textContent = fmt(brsAmount);
    document.getElementById('rp-net').textContent = fmt(netAVerser) + ' CFA';
    document.getElementById('rp-caution').textContent = fmt(caution);
    document.getElementById('rp-taux-comm').textContent = tauxComm + ' %';
    if (typeof verifierAlerteBrsCommercial === 'function') verifierAlerteBrsCommercial();
}

function ouvrirModalLocataire() {
    document.getElementById('modal-locataire').style.display = 'flex';
    document.getElementById('loc-name').focus();
}
function fermerModalLocataire() {
    document.getElementById('modal-locataire').style.display = 'none';
    document.getElementById('modal-error').style.display = 'none';
    ['loc-name','loc-email','loc-tel','loc-pwd'].forEach(id => document.getElementById(id).value = '');
}
async function creerLocataire() {
    const name = document.getElementById('loc-name').value.trim();
    const email = document.getElementById('loc-email').value.trim();
    const tel = document.getElementById('loc-tel').value.trim();
    const pwd = document.getElementById('loc-pwd').value;
    const errDiv = document.getElementById('modal-error');
    errDiv.style.display = 'none';
    if (!name || !email || !pwd) {
        errDiv.style.display = 'flex'; errDiv.textContent = 'Nom, email et mot de passe sont obligatoires.'; return;
    }
    const submitBtn = document.querySelector('#modal-locataire button[onclick="creerLocataire()"]');
    const originalHtml = submitBtn.innerHTML;
    submitBtn.disabled = true;
    try {
        const response = await fetch('{{ route('admin.contrats.locataire-rapide') }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            body: JSON.stringify({ name, email, telephone: tel, password: pwd }),
        });
        const data = await response.json();
        if (!response.ok || !data.success) {
            errDiv.style.display = 'flex'; errDiv.textContent = data.message || 'Erreur lors de la création.';
            submitBtn.disabled = false; submitBtn.innerHTML = originalHtml; return;
        }
        const select = document.getElementById('locataire_id');
        const option = new Option(`${data.name} — ${email}`, data.id, true, true);
        select.add(option); select.value = data.id;
        fermerModalLocataire();
    } catch(e) {
        errDiv.style.display = 'flex'; errDiv.textContent = 'Erreur réseau. Veuillez réessayer.';
        submitBtn.disabled = false; submitBtn.innerHTML = originalHtml;
    }
}
document.getElementById('modal-locataire').addEventListener('click', function(e) {
    if (e.target === this) fermerModalLocataire();
});

mettreAJourRecap();
@if($bienPreselectionne)
    chargerInfosBien(document.getElementById('bien_id'));
@endif
</script>
@endpush

@endsection
