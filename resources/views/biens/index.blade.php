@extends('layouts.app')
@section('header', 'Biens immobiliers')

@section('content')
<div class="space-y-4 md:space-y-6">

    {{-- ═══ EN-TÊTE ═══ --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <h1 class="font-display font-extrabold text-xl md:text-2xl text-bimo-navy tracking-tight leading-tight">
                Biens immobiliers
            </h1>
            <p class="font-body text-sm text-bimo-navy/50 mt-1">
                {{ $biens->total() }} bien(s) enregistré(s)
            </p>
        </div>
        @can('create', App\Models\Bien::class)
        <a href="{{ route('admin.biens.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-[var(--ac)] text-white
                  font-display font-bold text-sm rounded-[10px]
                  hover:opacity-90 transition-opacity duration-150 self-start">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Nouveau bien
        </a>
        @endcan
    </div>

    {{-- ═══ KPIs ═══ --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-4
                    border-t-2 border-t-[var(--ac)]">
            <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-navy/50 mb-1">Total biens</div>
            <div class="font-display font-extrabold text-2xl text-bimo-navy leading-none">
                {{ $biens->total() }}
            </div>
        </div>
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-4 border-t-2 border-t-bimo-gold">
            <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-navy/50 mb-1">Loués</div>
            <div class="font-display font-extrabold text-2xl text-bimo-gold leading-none">
                {{ $biens->getCollection()->where('statut','loue')->count() }}
            </div>
        </div>
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-4 border-t-2 border-t-bimo-navy">
            <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-navy/50 mb-1">Disponibles</div>
            <div class="font-display font-extrabold text-2xl text-bimo-navy leading-none">
                {{ $biens->getCollection()->where('statut','disponible')->count() }}
            </div>
        </div>
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-4 border-t-2 border-t-bimo-navy/30">
            <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-navy/50 mb-1">En travaux</div>
            <div class="font-display font-extrabold text-2xl text-bimo-navy/40 leading-none">
                {{ $biens->getCollection()->where('statut','en_travaux')->count() }}
            </div>
        </div>
    </div>

    {{-- ═══ FILTRES ═══ --}}
    <form method="GET" class="flex flex-wrap gap-2 items-center">
        {{-- Recherche --}}
        <div class="relative flex-1 min-w-[200px] max-w-xs">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-bimo-navy/30 pointer-events-none"
                 viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            <input type="text" name="q" value="{{ request('q') }}"
                   placeholder="Référence, adresse, ville, propriétaire…"
                   class="w-full pl-9 pr-3 py-2 bg-white border border-bimo-navy/15 rounded-[9px]
                          font-body text-sm text-bimo-navy placeholder:text-bimo-navy/30
                          focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15
                          transition-all duration-150">
        </div>

        <select name="statut" onchange="this.form.submit()"
                class="px-3 py-2 bg-white border border-bimo-navy/15 rounded-[9px]
                       font-body text-sm text-bimo-navy cursor-pointer
                       focus:outline-none focus:border-bimo-gold transition-all duration-150">
            <option value="">Tous les statuts</option>
            <option value="disponible" @selected(request('statut')==='disponible')>Disponible</option>
            <option value="loue"       @selected(request('statut')==='loue')>Loué</option>
            <option value="en_travaux" @selected(request('statut')==='en_travaux')>En travaux</option>
            <option value="archive"    @selected(request('statut')==='archive')>Archivé</option>
        </select>

        <select name="type" onchange="this.form.submit()"
                class="px-3 py-2 bg-white border border-bimo-navy/15 rounded-[9px]
                       font-body text-sm text-bimo-navy cursor-pointer
                       focus:outline-none focus:border-bimo-gold transition-all duration-150">
            <option value="">Tous les types</option>
            @foreach(\App\Models\Bien::TYPES as $val => $lbl)
            <option value="{{ $val }}" @selected(request('type')===$val)>{{ $lbl }}</option>
            @endforeach
        </select>

        <button type="submit"
                class="px-4 py-2 bg-[var(--ac)] text-white font-display font-bold text-sm
                       rounded-[9px] hover:opacity-90 transition-opacity duration-150">
            Rechercher
        </button>

        @if(request()->hasAny(['statut','type','q']))
        <a href="{{ route('admin.biens.index') }}"
           class="px-3 py-2 bg-white border border-bimo-navy/15 rounded-[9px]
                  font-body text-sm text-bimo-navy/50 hover:text-bimo-navy hover:border-bimo-navy/30
                  transition-all duration-150">
            Effacer
        </a>
        @endif
    </form>

    {{-- ═══ CONTENU ═══ --}}
    @if($biens->isEmpty())
    <div class="bg-white rounded-[14px] border border-bimo-navy/10 py-16 px-6 text-center">
        <div class="w-12 h-12 bg-bimo-navy/5 rounded-[12px] flex items-center justify-center mx-auto mb-4">
            <svg class="w-6 h-6 text-bimo-navy/30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                <polyline points="9 22 9 12 15 12 15 22"/>
            </svg>
        </div>
        <div class="font-display font-bold text-base text-bimo-navy mb-2">Aucun bien enregistré</div>
        <p class="font-body text-sm text-bimo-navy/50 mb-5">
            @if(request()->hasAny(['statut','type','q']))
                Aucun résultat pour ces filtres.
            @else
                Commencez par ajouter votre premier bien immobilier.
            @endif
        </p>
        <a href="{{ route('admin.biens.create') }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 bg-[var(--ac)] text-white
                  font-display font-bold text-sm rounded-[10px] hover:opacity-90 transition-opacity duration-150">
            + Ajouter un bien
        </a>
    </div>

    @else

    {{-- Grille de cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
        @foreach($biens as $bien)
        @php
            $photo = $bien->photos?->firstWhere('est_principale', true) ?? $bien->photos?->first();
            $badgeClass = match($bien->statut) {
                'loue'       => 'bg-bimo-navy/10 border-bimo-navy/20 text-bimo-navy',
                'disponible' => 'bg-bimo-gold/10 border-bimo-gold/20 text-bimo-gold',
                'en_travaux' => 'bg-bimo-navy/5 border-bimo-navy/10 text-bimo-navy/50',
                default      => 'bg-bimo-navy/5 border-bimo-navy/10 text-bimo-navy/40',
            };
        @endphp

        <div class="flex flex-col bg-white rounded-[14px] border border-bimo-navy/10
                    hover:border-bimo-gold/40 hover:shadow-gold-sm hover:-translate-y-0.5
                    transition-all duration-150 overflow-hidden group">

            {{-- Photo --}}
            <div class="h-44 bg-bimo-bg2 overflow-hidden flex items-center justify-center relative">
                @if($photo)
                    <img src="{{ asset('storage/'.$photo->chemin) }}"
                         alt="{{ $bien->titre_fallback }}"
                         class="w-full h-full object-cover group-hover:scale-[1.02] transition-transform duration-300">
                @else
                    <svg class="w-10 h-10 text-bimo-navy/20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <rect x="3" y="3" width="18" height="18" rx="2"/>
                        <circle cx="8.5" cy="8.5" r="1.5"/>
                        <polyline points="21 15 16 10 5 21"/>
                    </svg>
                @endif
                {{-- Badge statut sur la photo --}}
                <div class="absolute top-3 right-3">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-body font-medium border {{ $badgeClass }} backdrop-blur-sm bg-opacity-90">
                        {{ $bien->statut_label }}
                    </span>
                </div>
            </div>

            {{-- Corps --}}
            <div class="p-4 flex flex-col flex-1">
                {{-- Référence + type --}}
                <div class="flex items-center justify-between mb-2">
                    <span class="font-body text-[10px] text-bimo-navy/40 uppercase tracking-widest">
                        {{ $bien->reference }}
                    </span>
                    <span class="font-body text-[11px] font-medium text-bimo-navy/50">
                        {{ $bien->type_label }}
                    </span>
                </div>

                {{-- Titre --}}
                <h3 class="font-display font-bold text-sm text-bimo-navy mb-1 leading-tight">
                    {{ $bien->titre ?? $bien->adresse }}
                </h3>
                <p class="font-body text-xs text-bimo-navy/50 mb-3">
                    @if($bien->quartier) {{ $bien->quartier }}, @endif{{ $bien->ville }}
                </p>

                {{-- Pied de carte --}}
                <div class="flex items-center justify-between pt-3 border-t border-bimo-navy/[5%] mt-auto">
                    <div>
                        <div class="font-display font-bold text-base text-[var(--ac)] leading-none">
                            {{ number_format($bien->loyer_hors_charges, 0, ',', ' ') }}
                            <span class="font-body font-normal text-xs text-bimo-navy/40">F/mois</span>
                        </div>
                        @if($bien->contratActif)
                        <div class="font-body text-[11px] text-bimo-navy/50 mt-0.5">
                            {{ $bien->contratActif->locataire?->name ?? '—' }}
                        </div>
                        @endif
                    </div>
                    <a href="{{ route('admin.biens.show', $bien) }}"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 border border-bimo-navy/10
                              rounded-[8px] font-body font-medium text-xs text-bimo-navy/60
                              hover:text-bimo-navy hover:border-bimo-gold hover:text-bimo-gold
                              transition-all duration-150">
                        Voir
                        <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="9 18 15 12 9 6"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    @if($biens->hasPages())
    <div class="flex items-center justify-center gap-1.5 mt-2">
        @if(!$biens->onFirstPage())
        <a href="{{ $biens->previousPageUrl() }}"
           class="w-8 h-8 flex items-center justify-center border border-bimo-navy/10 rounded-[7px]
                  text-bimo-navy/40 hover:text-bimo-navy hover:border-bimo-navy/30 transition-all duration-150">
            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
        </a>
        @endif

        @foreach($biens->getUrlRange(max(1,$biens->currentPage()-2), min($biens->lastPage(),$biens->currentPage()+2)) as $page => $url)
        <a href="{{ $url }}"
           class="w-8 h-8 flex items-center justify-center rounded-[7px] font-body text-sm transition-all duration-150
                  {{ $page === $biens->currentPage()
                     ? 'bg-bimo-navy text-white border border-bimo-navy'
                     : 'border border-bimo-navy/10 text-bimo-navy/60 hover:text-bimo-navy hover:border-bimo-navy/30' }}">
            {{ $page }}
        </a>
        @endforeach

        @if($biens->hasMorePages())
        <a href="{{ $biens->nextPageUrl() }}"
           class="w-8 h-8 flex items-center justify-center border border-bimo-navy/10 rounded-[7px]
                  text-bimo-navy/40 hover:text-bimo-navy hover:border-bimo-navy/30 transition-all duration-150">
            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
        </a>
        @endif
    </div>
    @endif

    @endif

</div>
@endsection
