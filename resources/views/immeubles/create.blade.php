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
        <h2 class="font-display font-bold text-lg text-bimo-navy mb-3">Limite atteinte</h2>
        <p class="font-body text-sm text-bimo-navy/70 leading-relaxed mb-6">
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
                    class="flex-1 px-4 py-2.5 bg-bimo-bg text-bimo-navy/60 font-body text-sm rounded-[9px] hover:bg-bimo-bg2 transition-colors duration-150">
                Pas maintenant
            </button>
        </div>
    </div>
</div>
@endif

<div class="flex items-center gap-2 font-body text-sm text-bimo-navy/40 mb-5">
    <a href="{{ route('admin.immeubles.index') }}" class="hover:text-bimo-navy transition-colors duration-150">Immeubles</a>
    <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
    <span class="text-bimo-navy font-medium">Nouvel immeuble</span>
</div>
<div class="mb-5">
    <h1 class="font-display font-extrabold text-xl md:text-2xl text-bimo-navy tracking-tight">Ajouter un immeuble</h1>
    <p class="font-body text-sm text-bimo-navy/50 mt-1">Les unités seront ajoutées depuis la fiche immeuble après création.</p>
</div>

<form method="POST" action="{{ route('admin.immeubles.store') }}">
@csrf

<div class="grid grid-cols-1 lg:grid-cols-[1fr_300px] gap-5 items-start">

    {{-- Colonne gauche --}}
    <div class="space-y-4">

        {{-- Propriétaire --}}
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
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
                            class="w-full px-4 py-3 rounded-[10px] bg-white border font-body text-sm text-bimo-navy cursor-pointer
                                   focus:outline-none focus:ring-2 transition-all duration-150
                                   @error('proprietaire_id') border-bimo-red focus:border-bimo-red focus:ring-bimo-red/15
                                   @else border-bimo-navy/20 focus:border-bimo-gold focus:ring-bimo-gold/15 @enderror">
                        <option value="">— Sélectionner —</option>
                        @foreach($proprietaires as $p)
                        <option value="{{ $p->id }}" {{ old('proprietaire_id') == $p->id ? 'selected':'' }}>
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
                    <svg class="w-4 h-4 text-bimo-navy/50" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="18" rx="2"/><line x1="2" y1="9" x2="22" y2="9"/><line x1="12" y1="3" x2="12" y2="21"/></svg>
                </div>
                <span class="font-display font-bold text-sm text-bimo-navy">Informations générales</span>
            </div>
            <div class="px-5 py-5 space-y-4">
                <div class="space-y-1.5">
                    <label class="block font-body font-medium text-sm text-bimo-navy">Nom de l'immeuble <span class="text-bimo-red">*</span></label>
                    <input type="text" name="nom" value="{{ old('nom') }}" placeholder="Ex: Immeuble Fann Résidence"
                           class="w-full px-4 py-3 rounded-[10px] bg-white border font-body text-sm text-bimo-navy
                                  placeholder:text-bimo-navy/30 focus:outline-none focus:ring-2 transition-all duration-150
                                  @error('nom') border-bimo-red focus:border-bimo-red focus:ring-bimo-red/15
                                  @else border-bimo-navy/20 focus:border-bimo-gold focus:ring-bimo-gold/15 @enderror">
                    @error('nom')<p class="mt-1 font-body text-xs text-bimo-red">{{ $message }}</p>@enderror
                </div>
                <div class="space-y-1.5">
                    <label class="block font-body font-medium text-sm text-bimo-navy">
                        Nombre de niveaux <span class="font-normal text-bimo-navy/40 text-xs ml-1">(optionnel)</span>
                    </label>
                    <input type="number" name="nombre_niveaux" value="{{ old('nombre_niveaux') }}" min="1" max="99" placeholder="Ex: 5"
                           class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-navy
                                  placeholder:text-bimo-navy/30 focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                </div>
            </div>
        </div>

        {{-- Localisation --}}
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
            <div class="flex items-center gap-3 px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
                <div class="w-8 h-8 rounded-[8px] bg-bimo-gold/10 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-bimo-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                </div>
                <span class="font-display font-bold text-sm text-bimo-navy">Localisation</span>
            </div>
            <div class="px-5 py-5 space-y-4">
                <div class="space-y-1.5">
                    <label class="block font-body font-medium text-sm text-bimo-navy">Adresse <span class="text-bimo-red">*</span></label>
                    <input type="text" name="adresse" value="{{ old('adresse') }}" placeholder="Rue, numéro"
                           class="w-full px-4 py-3 rounded-[10px] bg-white border font-body text-sm text-bimo-navy
                                  placeholder:text-bimo-navy/30 focus:outline-none focus:ring-2 transition-all duration-150
                                  @error('adresse') border-bimo-red focus:border-bimo-red focus:ring-bimo-red/15
                                  @else border-bimo-navy/20 focus:border-bimo-gold focus:ring-bimo-gold/15 @enderror">
                    @error('adresse')<p class="mt-1 font-body text-xs text-bimo-red">{{ $message }}</p>@enderror
                </div>
                <div class="space-y-1.5">
                    <label class="block font-body font-medium text-sm text-bimo-navy">Ville <span class="text-bimo-red">*</span></label>
                    <input type="text" name="ville" value="{{ old('ville', 'Dakar') }}" placeholder="Ex: Dakar"
                           class="w-full px-4 py-3 rounded-[10px] bg-white border font-body text-sm text-bimo-navy
                                  placeholder:text-bimo-navy/30 focus:outline-none focus:ring-2 transition-all duration-150
                                  @error('ville') border-bimo-red focus:border-bimo-red focus:ring-bimo-red/15
                                  @else border-bimo-navy/20 focus:border-bimo-gold focus:ring-bimo-gold/15 @enderror">
                    @error('ville')<p class="mt-1 font-body text-xs text-bimo-red">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        {{-- Description + submit --}}
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
            <div class="flex items-center gap-3 px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
                <div class="w-8 h-8 rounded-[8px] bg-bimo-navy/5 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-bimo-navy/40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                </div>
                <span class="font-display font-bold text-sm text-bimo-navy">
                    Description <span class="font-normal text-bimo-navy/40 text-xs ml-1">(optionnel)</span>
                </span>
            </div>
            <div class="px-5 py-5">
                <textarea name="description" rows="4" placeholder="Description de l'immeuble, équipements communs, gardien…"
                          class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-navy
                                 placeholder:text-bimo-navy/30 focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15
                                 transition-all duration-150 resize-y">{{ old('description') }}</textarea>
            </div>
            <div class="sticky bottom-0 flex items-center justify-end gap-3 px-5 py-4
                        bg-white/95 backdrop-blur-sm border-t border-bimo-navy/[5%]">
                <a href="{{ route('admin.immeubles.index') }}"
                   class="px-5 py-2.5 border border-bimo-navy/15 rounded-[10px] font-body text-sm text-bimo-navy/60 hover:text-bimo-navy hover:border-bimo-navy/30 transition-all duration-150">
                    Annuler
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-[var(--ac)] text-white font-display font-bold text-sm rounded-[10px] hover:opacity-90 transition-opacity duration-150">
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    Créer l'immeuble
                </button>
            </div>
        </div>

    </div>

    {{-- Colonne droite --}}
    <div class="lg:sticky lg:top-6">
        <div class="bg-bimo-navy rounded-[14px] overflow-hidden">
            <div class="px-5 py-4 border-b border-white/[7%]">
                <div class="font-display font-bold text-sm text-white">À savoir</div>
            </div>
            <div class="px-5 py-4">
                <p class="font-body text-xs text-white/40 leading-relaxed mb-4">
                    Un immeuble est un regroupement logique d'unités locatives (appartements, bureaux, commerces).
                </p>
                <div class="space-y-0 divide-y divide-white/[6%]">
                    @foreach([
                        ['Unités',     'Ajoutées depuis la fiche'],
                        ['Contrats',   '1 par unité'],
                        ['Standalone', 'Bien sans immeuble'],
                    ] as [$lbl, $val])
                    <div class="flex items-center justify-between py-2.5">
                        <span class="font-body text-xs text-white/40">{{ $lbl }}</span>
                        <span class="font-display font-semibold text-xs text-white">{{ $val }}</span>
                    </div>
                    @endforeach
                </div>
                <div class="mt-4 p-3.5 bg-bimo-gold/10 border border-bimo-gold/20 rounded-[9px]">
                    <p class="font-body text-[11px] text-bimo-gold/80 leading-relaxed">
                        Après création, ouvrez la fiche immeuble et utilisez « Ajouter une unité » pour lier des biens ou en créer de nouveaux.
                    </p>
                </div>
            </div>
        </div>
    </div>

</div>
</form>
@endsection
