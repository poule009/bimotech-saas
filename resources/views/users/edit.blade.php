@extends('layouts.app')
@section('header', ($user->isProprietaire() ? 'Propriétaires' : 'Locataires') . ' › Modifier')

@section('content')

{{-- Breadcrumb --}}
<div class="flex items-center gap-2 font-body text-sm text-bimo-text/40 mb-5">
    @if($user->isProprietaire())
        <a href="{{ route('admin.users.proprietaires') }}" class="hover:text-bimo-text transition-colors duration-150">Propriétaires</a>
    @else
        <a href="{{ route('admin.users.locataires') }}" class="hover:text-bimo-text transition-colors duration-150">Locataires</a>
    @endif
    <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
    <a href="{{ route('admin.users.show', $user) }}" class="hover:text-bimo-text transition-colors duration-150">{{ $user->name }}</a>
    <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
    <span class="text-bimo-text font-medium">Modifier</span>
</div>

<div class="flex items-center justify-between gap-3 flex-wrap mb-5">
    <div>
        <h1 class="font-display font-extrabold text-xl md:text-2xl text-bimo-text tracking-tight">Modifier le profil</h1>
        <p class="font-body text-sm text-bimo-text/50 mt-1">{{ $user->name }} · {{ $user->email }}</p>
    </div>
    <span class="inline-flex items-center px-3 py-1.5 rounded-[7px] border font-body font-semibold text-xs
                 {{ $user->isProprietaire() ? 'bg-bimo-gold/10 border-bimo-gold/25 text-bimo-gold' : 'bg-bimo-navy/10 border-bimo-navy/15 text-bimo-text/70' }}">
        {{ $user->isProprietaire() ? 'Propriétaire' : 'Locataire' }}
    </span>
</div>

<form method="POST" action="{{ route('admin.users.update', $user) }}">
@csrf @method('PATCH')

<div class="grid grid-cols-1 lg:grid-cols-[1fr_280px] gap-5 items-start">

    {{-- COLONNE GAUCHE --}}
    <div class="space-y-4">

        {{-- Informations générales --}}
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
            <div class="flex items-center gap-3 px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
                <div class="w-8 h-8 rounded-[8px] bg-bimo-gold/15 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-bimo-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                </div>
                <span class="font-display font-bold text-sm text-bimo-text">Informations générales</span>
            </div>
            <div class="px-5 py-5 space-y-4">
                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1.5">
                        <label class="block font-body font-medium text-sm text-bimo-text">Nom complet <span class="text-bimo-red">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                               class="w-full px-4 py-3 rounded-[10px] bg-white border font-body text-sm text-bimo-text
                                      focus:outline-none focus:ring-2 transition-all duration-150
                                      @error('name') border-bimo-red focus:border-bimo-red focus:ring-bimo-red/15
                                      @else border-bimo-navy/20 focus:border-bimo-gold focus:ring-bimo-gold/15 @enderror">
                        @error('name')<p class="mt-1 font-body text-xs text-bimo-red">{{ $message }}</p>@enderror
                    </div>
                    <div class="space-y-1.5">
                        <label class="block font-body font-medium text-sm text-bimo-text">Email <span class="text-bimo-red">*</span></label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                               class="w-full px-4 py-3 rounded-[10px] bg-white border font-body text-sm text-bimo-text
                                      focus:outline-none focus:ring-2 transition-all duration-150
                                      @error('email') border-bimo-red focus:border-bimo-red focus:ring-bimo-red/15
                                      @else border-bimo-navy/20 focus:border-bimo-gold focus:ring-bimo-gold/15 @enderror">
                        @error('email')<p class="mt-1 font-body text-xs text-bimo-red">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1.5">
                        <label class="block font-body font-medium text-sm text-bimo-text">Téléphone</label>
                        <input type="text" name="telephone" value="{{ old('telephone', $user->telephone) }}" placeholder="+221 7X XXX XX XX"
                               class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                      placeholder:text-bimo-text/30 focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block font-body font-medium text-sm text-bimo-text">Adresse</label>
                        <input type="text" name="adresse" value="{{ old('adresse', $user->adresse) }}" placeholder="Adresse complète"
                               class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                      placeholder:text-bimo-text/30 focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                    </div>
                </div>
            </div>
        </div>

        {{-- PROFIL PROPRIÉTAIRE --}}
        @if($user->isProprietaire())

        <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
            <div class="flex items-center gap-3 px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
                <div class="w-8 h-8 rounded-[8px] bg-bimo-navy/10 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-bimo-text/50" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
                </div>
                <span class="font-display font-bold text-sm text-bimo-text">Profil propriétaire</span>
            </div>
            <div class="px-5 py-5 space-y-4">
                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1.5">
                        <label class="block font-body font-medium text-sm text-bimo-text">CNI / Passeport</label>
                        <input type="text" name="cni" value="{{ old('cni', $user->proprietaire?->cni) }}"
                               class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                      focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block font-body font-medium text-sm text-bimo-text">NINEA</label>
                        <input type="text" name="ninea" value="{{ old('ninea', $user->proprietaire?->ninea) }}" placeholder="Ex: 123456789"
                               class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                      placeholder:text-bimo-text/30 focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1.5">
                        <label class="block font-body font-medium text-sm text-bimo-text">Date de naissance</label>
                        <input type="date" name="date_naissance" value="{{ old('date_naissance', $user->proprietaire?->date_naissance?->format('Y-m-d')) }}"
                               class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                      focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block font-body font-medium text-sm text-bimo-text">Genre</label>
                        <select name="genre"
                                class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text cursor-pointer
                                       focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                            <option value="">— Choisir —</option>
                            <option value="M" {{ old('genre', $user->proprietaire?->genre) === 'M' ? 'selected':'' }}>Homme</option>
                            <option value="F" {{ old('genre', $user->proprietaire?->genre) === 'F' ? 'selected':'' }}>Femme</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1.5">
                        <label class="block font-body font-medium text-sm text-bimo-text">Ville</label>
                        <input type="text" name="ville" value="{{ old('ville', $user->proprietaire?->ville ?? 'Dakar') }}"
                               class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                      focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block font-body font-medium text-sm text-bimo-text">Quartier</label>
                        <input type="text" name="quartier" value="{{ old('quartier', $user->proprietaire?->quartier) }}"
                               class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                      focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
            <div class="flex items-center gap-3 px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
                <div class="w-8 h-8 rounded-[8px] bg-bimo-navy/5 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-bimo-text/40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                </div>
                <span class="font-display font-bold text-sm text-bimo-text">Coordonnées bancaires</span>
            </div>
            <div class="px-5 py-5 space-y-4">
                <div class="space-y-1.5">
                    <label class="block font-body font-medium text-sm text-bimo-text">Mode de paiement préféré</label>
                    <select name="mode_paiement_prefere"
                            class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text cursor-pointer
                                   focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                        @foreach(['especes'=>'Espèces','virement'=>'Virement bancaire','wave'=>'Wave','orange_money'=>'Orange Money','free_money'=>'Free Money','cheque'=>'Chèque'] as $val => $label)
                        <option value="{{ $val }}" {{ old('mode_paiement_prefere', $user->proprietaire?->mode_paiement_prefere) === $val ? 'selected':'' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1.5">
                        <label class="block font-body font-medium text-sm text-bimo-text">Banque</label>
                        <input type="text" name="banque" value="{{ old('banque', $user->proprietaire?->banque) }}" placeholder="CBAO, Ecobank..."
                               class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                      placeholder:text-bimo-text/30 focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block font-body font-medium text-sm text-bimo-text">Numéro Wave</label>
                        <input type="text" name="numero_wave" value="{{ old('numero_wave', $user->proprietaire?->numero_wave) }}" placeholder="+221 7X XXX XX XX"
                               class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                      placeholder:text-bimo-text/30 focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                    </div>
                </div>
                <div class="space-y-1.5">
                    <label class="block font-body font-medium text-sm text-bimo-text">Numéro Orange Money</label>
                    <input type="text" name="numero_om" value="{{ old('numero_om', $user->proprietaire?->numero_om) }}" placeholder="+221 7X XXX XX XX"
                           class="w-full max-w-xs px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                  placeholder:text-bimo-text/30 focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                </div>
            </div>
        </div>

        {{-- PROFIL LOCATAIRE --}}
        @elseif($user->isLocataire())

        <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
            <div class="flex items-center gap-3 px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
                <div class="w-8 h-8 rounded-[8px] bg-bimo-navy/10 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-bimo-text/50" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                </div>
                <span class="font-display font-bold text-sm text-bimo-text">Profil locataire</span>
            </div>
            <div class="px-5 py-5 space-y-4">
                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1.5">
                        <label class="block font-body font-medium text-sm text-bimo-text">CNI / Passeport</label>
                        <input type="text" name="cni" value="{{ old('cni', $user->locataire?->cni) }}"
                               class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                      focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block font-body font-medium text-sm text-bimo-text">Date de naissance</label>
                        <input type="date" name="date_naissance" value="{{ old('date_naissance', $user->locataire?->date_naissance?->format('Y-m-d')) }}"
                               class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                      focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1.5">
                        <label class="block font-body font-medium text-sm text-bimo-text">Profession</label>
                        <input type="text" name="profession" value="{{ old('profession', $user->locataire?->profession) }}"
                               class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                      focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block font-body font-medium text-sm text-bimo-text">Employeur</label>
                        <input type="text" name="employeur" value="{{ old('employeur', $user->locataire?->employeur) }}"
                               class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                      focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1.5">
                        <label class="block font-body font-medium text-sm text-bimo-text">Revenu mensuel (FCFA)</label>
                        <input type="number" name="revenu_mensuel" value="{{ old('revenu_mensuel', $user->locataire?->revenu_mensuel) }}" min="0" step="500"
                               class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                      focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block font-body font-medium text-sm text-bimo-text">Genre</label>
                        <select name="genre"
                                class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text cursor-pointer
                                       focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                            <option value="">— Choisir —</option>
                            <option value="M" {{ old('genre', $user->locataire?->genre) === 'M' ? 'selected':'' }}>Homme</option>
                            <option value="F" {{ old('genre', $user->locataire?->genre) === 'F' ? 'selected':'' }}>Femme</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
            <div class="flex items-center gap-3 px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
                <div class="w-8 h-8 rounded-[8px] bg-bimo-navy/5 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-bimo-text/40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
                </div>
                <span class="font-display font-bold text-sm text-bimo-text">
                    Contact d'urgence <span class="font-normal text-bimo-text/40 text-xs ml-1">(optionnel)</span>
                </span>
            </div>
            <div class="px-5 py-5 space-y-4">
                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1.5">
                        <label class="block font-body font-medium text-sm text-bimo-text">Nom</label>
                        <input type="text" name="contact_urgence_nom" value="{{ old('contact_urgence_nom', $user->locataire?->contact_urgence_nom) }}"
                               class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                      focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block font-body font-medium text-sm text-bimo-text">Téléphone</label>
                        <input type="text" name="contact_urgence_tel" value="{{ old('contact_urgence_tel', $user->locataire?->contact_urgence_tel) }}"
                               class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                      focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                    </div>
                </div>
                <div class="space-y-1.5">
                    <label class="block font-body font-medium text-sm text-bimo-text">Lien de parenté</label>
                    <input type="text" name="contact_urgence_lien" value="{{ old('contact_urgence_lien', $user->locataire?->contact_urgence_lien) }}"
                           placeholder="Ex: Père, Mère, Époux(se)..."
                           class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                  placeholder:text-bimo-text/30 focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                </div>
            </div>
        </div>

        @if(config('features.fiscalite'))
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
            <div class="flex items-center gap-3 px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
                <div class="w-8 h-8 rounded-[8px] bg-amber-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-amber-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </div>
                <span class="font-display font-bold text-sm text-bimo-text">Statut fiscal</span>
            </div>
            <div class="px-5 py-5">
                @include('admin.users._section-type-locataire', ['user' => $user])
            </div>
        </div>
        @endif

        @endif

        {{-- Submit --}}
        <div class="sticky bottom-0 flex items-center justify-end gap-3 py-4
                    bg-bimo-bg/95 backdrop-blur-sm border-t border-bimo-navy/10">
            <a href="{{ route('admin.users.show', $user) }}"
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

    </div>{{-- fin colonne gauche --}}

    {{-- COLONNE DROITE --}}
    <div class="lg:sticky lg:top-6">
        <div class="bg-bimo-navy rounded-[14px] overflow-hidden">
            <div class="px-5 py-4 border-b border-white/[7%]">
                <div class="font-display font-bold text-sm text-white">Profil actuel</div>
            </div>
            <div class="px-5 py-5">
                {{-- Avatar --}}
                <div class="flex flex-col items-center pb-4 border-b border-white/10">
                    <div class="w-14 h-14 rounded-full flex items-center justify-center mb-3 font-display font-bold text-xl text-bimo-gold
                                bg-bimo-gold/15 border-2 border-bimo-gold/30">
                        {{ mb_strtoupper(mb_substr($user->name, 0, 2)) }}
                    </div>
                    <div class="font-body font-semibold text-sm text-white">{{ $user->name }}</div>
                    <div class="font-body text-xs text-white/40 mt-0.5">{{ $user->email }}</div>
                </div>
                {{-- Infos --}}
                <div class="pt-3 divide-y divide-white/[6%]">
                    @php
                        $sideRows = [
                            ['Rôle', $user->isProprietaire() ? 'Propriétaire' : 'Locataire'],
                        ];
                        if ($user->telephone) $sideRows[] = ['Téléphone', $user->telephone];
                        $sideRows[] = ['Membre depuis', $user->created_at?->format('d/m/Y') ?? '—'];
                        if ($user->isProprietaire()) {
                            $sideRows[] = ['Biens', (string) $user->biens()->count()];
                        } else {
                            $sideRows[] = ['Contrats', (string) $user->contrats()->count()];
                        }
                    @endphp
                    @foreach($sideRows as [$lbl, $val])
                    <div class="flex items-center justify-between py-2.5">
                        <span class="font-body text-xs text-white/40">{{ $lbl }}</span>
                        <span class="font-body text-xs text-white/70">{{ $val }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

</div>
</form>

@endsection
