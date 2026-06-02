@extends('layouts.app')
@section('header', 'Immeubles')

@section('content')
<div class="space-y-4 md:space-y-6">

    {{-- En-tête --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <h1 class="font-display font-extrabold text-xl md:text-2xl text-bimo-navy tracking-tight leading-tight">Immeubles</h1>
            <p class="font-body text-sm text-bimo-navy/50 mt-1">{{ $immeubles->total() }} immeuble(s) enregistré(s)</p>
        </div>
        @can('isStaff')
        <a href="{{ route('admin.immeubles.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-[var(--ac)] text-white
                  font-display font-bold text-sm rounded-[10px] hover:opacity-90 transition-opacity duration-150 self-start">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Nouvel immeuble
        </a>
        @endcan
    </div>

    {{-- Contenu --}}
    @if($immeubles->isEmpty())
    <div class="bg-white rounded-[14px] border border-bimo-navy/10 py-16 px-6 text-center">
        <div class="w-12 h-12 bg-bimo-navy/5 rounded-[12px] flex items-center justify-center mx-auto mb-4">
            <svg class="w-6 h-6 text-bimo-navy/30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <rect x="2" y="3" width="20" height="18" rx="2"/>
                <line x1="2" y1="9" x2="22" y2="9"/><line x1="12" y1="3" x2="12" y2="21"/>
            </svg>
        </div>
        <div class="font-display font-bold text-base text-bimo-navy mb-2">Aucun immeuble enregistré</div>
        <p class="font-body text-sm text-bimo-navy/50 mb-5">Commencez par ajouter votre premier immeuble.</p>
        <a href="{{ route('admin.immeubles.create') }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 bg-[var(--ac)] text-white
                  font-display font-bold text-sm rounded-[10px] hover:opacity-90 transition-opacity duration-150">
            + Ajouter un immeuble
        </a>
    </div>

    @else
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
        @foreach($immeubles as $immeuble)
        <div class="flex flex-col bg-white rounded-[14px] border border-bimo-navy/10
                    hover:border-bimo-gold/40 hover:shadow-gold-sm hover:-translate-y-0.5
                    transition-all duration-150 overflow-hidden group">

            {{-- Barre accent --}}
            <div class="h-1.5 bg-bimo-navy"></div>

            <div class="p-5 flex flex-col flex-1">
                {{-- Nom + icône --}}
                <div class="flex items-start gap-3 mb-4">
                    <div class="w-10 h-10 rounded-[10px] bg-bimo-navy/5 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-bimo-navy/40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <rect x="2" y="3" width="20" height="18" rx="2"/>
                            <line x1="2" y1="9" x2="22" y2="9"/><line x1="12" y1="3" x2="12" y2="21"/>
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <h3 class="font-display font-bold text-sm text-bimo-navy truncate leading-tight">{{ $immeuble->nom }}</h3>
                        <p class="font-body text-xs text-bimo-navy/50 mt-0.5">{{ $immeuble->adresse }}, {{ $immeuble->ville }}</p>
                    </div>
                </div>

                {{-- Badges --}}
                <div class="flex flex-wrap gap-2 mb-4">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full border border-bimo-navy/15 bg-bimo-navy/5 text-[11px] font-body font-medium text-bimo-navy/70">
                        <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
                        {{ $immeuble->biens_count }} unité(s)
                    </span>
                    @if($immeuble->nombre_niveaux)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full border border-bimo-navy/10 bg-bimo-navy/[4%] text-[11px] font-body font-medium text-bimo-navy/60">
                        {{ $immeuble->nombre_niveaux }} niveau(x)
                    </span>
                    @endif
                </div>

                {{-- Pied --}}
                <div class="flex items-center justify-between pt-3 border-t border-bimo-navy/[5%] mt-auto">
                    <span class="font-body text-xs text-bimo-navy/50">{{ $immeuble->proprietaire?->name ?? '—' }}</span>
                    <a href="{{ route('admin.immeubles.show', $immeuble) }}"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 border border-bimo-navy/10 rounded-[8px]
                              font-body font-medium text-xs text-bimo-navy/60
                              hover:text-bimo-navy hover:border-bimo-gold hover:text-bimo-gold
                              transition-all duration-150">
                        Voir
                        <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    @if($immeubles->hasPages())
    <div class="flex items-center justify-center gap-1.5">
        @if(!$immeubles->onFirstPage())
        <a href="{{ $immeubles->previousPageUrl() }}"
           class="w-8 h-8 flex items-center justify-center border border-bimo-navy/10 rounded-[7px] text-bimo-navy/40 hover:text-bimo-navy hover:border-bimo-navy/30 transition-all duration-150">
            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
        </a>
        @endif
        @foreach($immeubles->getUrlRange(max(1,$immeubles->currentPage()-2), min($immeubles->lastPage(),$immeubles->currentPage()+2)) as $page => $url)
        <a href="{{ $url }}"
           class="w-8 h-8 flex items-center justify-center rounded-[7px] font-body text-sm transition-all duration-150
                  {{ $page === $immeubles->currentPage() ? 'bg-bimo-navy text-white border border-bimo-navy' : 'border border-bimo-navy/10 text-bimo-navy/60 hover:text-bimo-navy hover:border-bimo-navy/30' }}">
            {{ $page }}
        </a>
        @endforeach
        @if($immeubles->hasMorePages())
        <a href="{{ $immeubles->nextPageUrl() }}"
           class="w-8 h-8 flex items-center justify-center border border-bimo-navy/10 rounded-[7px] text-bimo-navy/40 hover:text-bimo-navy hover:border-bimo-navy/30 transition-all duration-150">
            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
        </a>
        @endif
    </div>
    @endif
    @endif

</div>
@endsection
