@extends('layouts.app')
@section('header', 'Contrats › Modifier')

@section('content')

{{-- Breadcrumb --}}
<div class="flex items-center gap-2 font-body text-sm text-bimo-text/40 mb-5">
    <a href="{{ route('admin.contrats.index') }}" class="hover:text-bimo-text transition-colors duration-150">Contrats</a>
    <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
    <a href="{{ route('admin.contrats.show', $contrat) }}" class="hover:text-bimo-text transition-colors duration-150">
        {{ $contrat->reference_bail ?? 'Contrat #'.$contrat->id }}
    </a>
    <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
    <span class="text-bimo-text font-medium">Modifier</span>
</div>

<div class="flex items-center justify-between gap-3 flex-wrap mb-5">
    <div>
        <h1 class="font-display font-extrabold text-xl md:text-2xl text-bimo-text tracking-tight">Modifier le contrat</h1>
        <p class="font-body text-sm text-bimo-text/50 mt-1">Le bien et le locataire ne peuvent pas être changés.</p>
    </div>
    <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-bimo-gold/10 border border-bimo-gold/25 rounded-[7px]
                font-display font-semibold text-sm text-bimo-gold">
        {{ $contrat->reference_bail ?? 'BAIL-'.$contrat->id }}
    </div>
</div>

{{-- Bannière info --}}
<div class="flex items-center gap-3 bg-bimo-gold/[8%] border border-bimo-gold/25 rounded-[12px] px-4 py-3 mb-5">
    <svg class="w-4 h-4 text-bimo-gold flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
    </svg>
    <p class="font-body text-sm text-bimo-gold">
        Toute modification du loyer prendra effet sur les prochains paiements. Les paiements déjà validés ne seront pas recalculés.
    </p>
</div>

<form method="POST" action="{{ route('admin.contrats.update', $contrat) }}">
@csrf @method('PUT')

<div class="grid grid-cols-1 lg:grid-cols-[1fr_300px] gap-5 items-start">

    {{-- COLONNE GAUCHE --}}
    <div class="space-y-4">

        {{-- Bien & Locataire (lecture seule) --}}
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
            <div class="flex items-center gap-3 px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
                <div class="w-8 h-8 rounded-[8px] bg-bimo-gold/15 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-bimo-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                </div>
                <span class="font-display font-bold text-sm text-bimo-text">Bien & Locataire</span>
            </div>
            <div class="px-5 py-5 grid grid-cols-1 md:grid-cols-2 gap-3">
                <div class="space-y-1.5">
                    <label class="block font-body font-medium text-sm text-bimo-text/50">Bien (non modifiable)</label>
                    <input type="text" readonly
                           value="{{ $contrat->bien?->reference }} — {{ $contrat->bien?->adresse }}, {{ $contrat->bien?->ville }}"
                           class="w-full px-4 py-3 rounded-[10px] bg-bimo-bg border border-bimo-navy/10 font-body text-sm text-bimo-text/50 cursor-not-allowed">
                </div>
                <div class="space-y-1.5">
                    <label class="block font-body font-medium text-sm text-bimo-text/50">Locataire (non modifiable)</label>
                    <input type="text" readonly value="{{ $contrat->locataire?->name }}"
                           class="w-full px-4 py-3 rounded-[10px] bg-bimo-bg border border-bimo-navy/10 font-body text-sm text-bimo-text/50 cursor-not-allowed">
                </div>
            </div>
        </div>

        {{-- Durée & Type --}}
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
                        <input type="date" name="date_debut"
                               value="{{ old('date_debut', $contrat->date_debut?->format('Y-m-d')) }}"
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
                        <input type="date" name="date_fin"
                               value="{{ old('date_fin', $contrat->date_fin?->format('Y-m-d')) }}"
                               class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                      focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                        <p class="font-body text-[11px] text-bimo-text/30">Laisser vide = contrat ouvert</p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1.5">
                        <label class="block font-body font-medium text-sm text-bimo-text">Type de bail <span class="text-bimo-red">*</span></label>
                        <select name="type_bail"
                                class="w-full px-4 py-3 rounded-[10px] bg-white border font-body text-sm text-bimo-text cursor-pointer
                                       focus:outline-none focus:ring-2 transition-all duration-150
                                       @error('type_bail') border-bimo-red focus:border-bimo-red focus:ring-bimo-red/15
                                       @else border-bimo-navy/20 focus:border-bimo-gold focus:ring-bimo-gold/15 @enderror">
                            @foreach($typesBail as $val => $label)
                            <option value="{{ $val }}" {{ old('type_bail', $contrat->type_bail) === $val ? 'selected':'' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('type_bail')<p class="mt-1 font-body text-xs text-bimo-red">{{ $message }}</p>@enderror
                    </div>
                    <div class="space-y-1.5">
                        <label class="block font-body font-medium text-sm text-bimo-text">Référence bail</label>
                        <input type="text" name="reference_bail"
                               value="{{ old('reference_bail', $contrat->reference_bail) }}"
                               placeholder="Ex: BAIL-2024-001"
                               class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                      placeholder:text-bimo-text/30 focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                    </div>
                </div>
            </div>
        </div>

        {{-- Loyer --}}
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
                               value="{{ old('loyer_nu', $contrat->loyer_nu) }}" min="0" step="500" oninput="mettreAJourRecap()"
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
                               value="{{ old('charges_mensuelles', $contrat->charges_mensuelles ?? 0) }}" min="0" step="500" oninput="mettreAJourRecap()"
                               class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                      focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1.5">
                        <label class="block font-body font-medium text-sm text-bimo-text">TOM <span class="font-normal text-bimo-text/40 text-xs">(Taxe ordures)</span></label>
                        <input type="number" name="tom_amount" id="tom_amount"
                               value="{{ old('tom_amount', $contrat->tom_amount ?? 0) }}" min="0" step="100" oninput="mettreAJourRecap()"
                               class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                      focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block font-body font-medium text-sm text-bimo-text">Indexation annuelle (%)</label>
                        <input type="number" name="indexation_annuelle"
                               value="{{ old('indexation_annuelle', $contrat->indexation_annuelle ?? 0) }}" min="0" max="20" step="0.5"
                               class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                      focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                    </div>
                </div>
            </div>
        </div>

        {{-- Caution & Frais --}}
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
            <div class="flex items-center gap-3 px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
                <div class="w-8 h-8 rounded-[8px] bg-bimo-navy/5 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-bimo-text/40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </div>
                <span class="font-display font-bold text-sm text-bimo-text">Caution & Frais</span>
            </div>
            <div class="px-5 py-5 space-y-4">
                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1.5">
                        <label class="block font-body font-medium text-sm text-bimo-text">Caution (FCFA) <span class="text-bimo-red">*</span></label>
                        <input type="number" name="caution"
                               value="{{ old('caution', $contrat->caution) }}" min="0" step="500"
                               class="w-full px-4 py-3 rounded-[10px] bg-white border font-body text-sm text-bimo-text
                                      focus:outline-none focus:ring-2 transition-all duration-150
                                      @error('caution') border-bimo-red focus:border-bimo-red focus:ring-bimo-red/15
                                      @else border-bimo-navy/20 focus:border-bimo-gold focus:ring-bimo-gold/15 @enderror">
                        @error('caution')<p class="mt-1 font-body text-xs text-bimo-red">{{ $message }}</p>@enderror
                    </div>
                    <div class="space-y-1.5">
                        <label class="block font-body font-medium text-sm text-bimo-text">Nombre de mois</label>
                        <select name="nombre_mois_caution"
                                class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text cursor-pointer
                                       focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                            @foreach([1,2,3,6] as $n)
                            <option value="{{ $n }}" {{ old('nombre_mois_caution', $contrat->nombre_mois_caution ?? 1) == $n ? 'selected':'' }}>{{ $n }} mois</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="space-y-1.5">
                    <label class="block font-body font-medium text-sm text-bimo-text">Frais d'agence HT (FCFA)</label>
                    <input type="number" name="frais_agence"
                           value="{{ old('frais_agence', $contrat->frais_agence ?? 0) }}" min="0" step="500"
                           class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                  focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                    <p class="font-body text-[11px] text-bimo-text/30">Honoraires HT · TVA 18% ajoutée auto au premier paiement</p>
                </div>
            </div>
        </div>

        {{-- Fiscal --}}
        @if(config('features.fiscalite'))
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
            <div class="flex items-center gap-3 px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
                <div class="w-8 h-8 rounded-[8px] bg-amber-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-amber-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </div>
                <span class="font-display font-bold text-sm text-bimo-text">Paramètres fiscaux</span>
            </div>
            <div class="px-5 py-5">
                @include('admin.contrats._section-fiscal', ['contrat' => $contrat])
            </div>
        </div>
        @endif

        {{-- Garant --}}
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
            <div class="flex items-center gap-3 px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
                <div class="w-8 h-8 rounded-[8px] bg-bimo-navy/5 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-bimo-text/40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
                </div>
                <span class="font-display font-bold text-sm text-bimo-text">
                    Garant <span class="font-normal text-bimo-text/40 text-xs ml-1">(optionnel)</span>
                </span>
            </div>
            <div class="px-5 py-5 space-y-4">
                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1.5">
                        <label class="block font-body font-medium text-sm text-bimo-text">Nom du garant</label>
                        <input type="text" name="garant_nom" value="{{ old('garant_nom', $contrat->garant_nom) }}"
                               class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                      focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block font-body font-medium text-sm text-bimo-text">Téléphone</label>
                        <input type="text" name="garant_telephone" value="{{ old('garant_telephone', $contrat->garant_telephone) }}"
                               class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                      focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1.5">
                        <label class="block font-body font-medium text-sm text-bimo-text">Adresse</label>
                        <input type="text" name="garant_adresse" value="{{ old('garant_adresse', $contrat->garant_adresse) }}"
                               class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                      focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block font-body font-medium text-sm text-bimo-text">CNI / Pièce d'identité</label>
                        <input type="text" name="garant_cni" value="{{ old('garant_cni', $contrat->garant_cni) }}"
                               placeholder="N° CNI ou passeport"
                               class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                      placeholder:text-bimo-text/30 focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                    </div>
                </div>
            </div>
        </div>

        {{-- Observations --}}
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
            <div class="flex items-center gap-3 px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
                <div class="w-8 h-8 rounded-[8px] bg-bimo-navy/5 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-bimo-text/40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                </div>
                <span class="font-display font-bold text-sm text-bimo-text">Observations</span>
            </div>
            <div class="px-5 py-5 space-y-4">
                <textarea name="observations" rows="3"
                          class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                 focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15
                                 transition-all duration-150 resize-y">{{ old('observations', $contrat->observations) }}</textarea>

                <div class="space-y-1.5">
                    <label class="block font-body font-medium text-sm text-bimo-text">
                        Clauses particulières
                        <span class="font-normal text-bimo-text/40 text-xs ml-1">Conditions spécifiques à ce bail uniquement</span>
                    </label>
                    <textarea name="clauses_particulieres" rows="5"
                              placeholder="Ex : Le locataire bénéficie d'une place de parking désignée n°3…"
                              class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                     placeholder:text-bimo-text/30 focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15
                                     transition-all duration-150 resize-y">{{ old('clauses_particulieres', $contrat->clauses_particulieres) }}</textarea>
                    <p class="font-body text-[11px] text-bimo-text/30">Ces clauses s'ajouteront aux clauses générales de l'agence dans le bail PDF.</p>
                </div>
            </div>

            {{-- Submit --}}
            <div class="sticky bottom-0 flex items-center justify-end gap-3 px-5 py-4
                        bg-white/95 backdrop-blur-sm border-t border-bimo-navy/[5%]">
                <a href="{{ route('admin.contrats.show', $contrat) }}"
                   class="px-5 py-2.5 border border-bimo-navy/15 rounded-[10px]
                          font-body text-sm text-bimo-text/60 hover:text-bimo-text hover:border-bimo-navy/30 transition-all duration-150">
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

    </div>{{-- fin colonne gauche --}}

    {{-- COLONNE DROITE --}}
    <div class="lg:sticky lg:top-6 space-y-4">

        {{-- Contrat actuel --}}
        <div class="bg-bimo-navy rounded-[14px] overflow-hidden">
            <div class="px-5 py-4 border-b border-white/[7%]">
                <div class="font-display font-bold text-sm text-white">Contrat actuel</div>
            </div>
            <div class="px-5 py-2 divide-y divide-white/[6%]">
                @php
                    $currentRows = [
                        ['Statut',         \App\Models\Contrat::STATUTS[$contrat->statut] ?? $contrat->statut, ''],
                        ['Loyer contractuel', number_format($contrat->loyer_contractuel, 0, ',', ' ') . ' F', 'text-bimo-gold font-semibold'],
                        ['Loyer nu',       number_format($contrat->loyer_nu, 0, ',', ' ') . ' F', ''],
                        ['Charges',        number_format($contrat->charges_mensuelles ?? 0, 0, ',', ' ') . ' F', ''],
                        ['TOM',            number_format($contrat->tom_amount ?? 0, 0, ',', ' ') . ' F', ''],
                        ['Caution',        number_format($contrat->caution, 0, ',', ' ') . ' F', ''],
                        ['Créé le',        $contrat->created_at?->format('d/m/Y') ?? '—', ''],
                    ];
                @endphp
                @foreach($currentRows as [$lbl, $val, $cls])
                <div class="flex items-center justify-between py-2.5">
                    <span class="font-body text-xs text-white/40">{{ $lbl }}</span>
                    <span class="font-body text-xs text-white/70 {{ $cls }}">{{ $val }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Nouveau récap live --}}
        <div class="bg-bimo-navy rounded-[14px] overflow-hidden">
            <div class="px-5 py-4 border-b border-white/[7%]">
                <div class="font-display font-bold text-sm text-white">Nouveau récapitulatif</div>
            </div>
            <div class="px-5 py-2 divide-y divide-white/[6%]">
                @foreach([
                    ['rp-loyer-nu', 'Loyer nu',     'text-white'],
                    ['rp-charges',  '+ Charges',    'text-white'],
                    ['rp-tom',      '+ TOM',        'text-white'],
                    ['rp-total',    '= Total',      'text-bimo-gold font-semibold'],
                ] as [$id, $lbl, $cls])
                <div class="flex items-center justify-between py-2.5">
                    <span class="font-body text-xs text-white/40">{{ $lbl }}</span>
                    <span class="font-display font-semibold text-xs {{ $cls }}" id="{{ $id }}">— F</span>
                </div>
                @endforeach
            </div>
            <div class="px-5 pb-4">
                <div class="mt-2 p-3.5 bg-bimo-gold/10 border border-bimo-gold/20 rounded-[9px]">
                    <div class="font-body font-medium text-[9px] uppercase tracking-widest text-bimo-gold/60 mb-1">Commission HT</div>
                    <div class="font-display font-extrabold text-lg text-bimo-gold" id="rp-comm">— F</div>
                </div>
            </div>
        </div>

    </div>

</div>
</form>

@push('scripts')
<script>
const tauxComm = {{ $contrat->bien?->taux_commission ?? 10 }};
function fmt(n) { return Math.round(n).toLocaleString('fr-FR') + ' F'; }
function mettreAJourRecap() {
    const loyerNu = parseFloat(document.getElementById('loyer_nu').value) || 0;
    const charges = parseFloat(document.getElementById('charges_mensuelles').value) || 0;
    const tom     = parseFloat(document.getElementById('tom_amount').value) || 0;
    const total   = loyerNu + charges + tom;
    const commHt  = Math.round(loyerNu * tauxComm / 100);
    document.getElementById('rp-loyer-nu').textContent = fmt(loyerNu);
    document.getElementById('rp-charges').textContent  = fmt(charges);
    document.getElementById('rp-tom').textContent      = fmt(tom);
    document.getElementById('rp-total').textContent    = fmt(total);
    document.getElementById('rp-comm').textContent     = fmt(commHt);
}
mettreAJourRecap();
</script>
@endpush

@endsection
