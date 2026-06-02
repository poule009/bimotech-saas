@extends('layouts.app')
@section('header', 'Portefeuille Bailleurs')

@section('content')

@php
    $totLoyers   = $bailleurs->sum('total_loyers');
    $totComm     = $bailleurs->sum('total_commissions');
    $totDepenses = $bailleurs->sum('total_depenses');
    $totNet      = $bailleurs->sum('net_final');
@endphp

<div class="space-y-4 md:space-y-6">

    {{-- ═══ EN-TÊTE ═══ --}}
    <div>
        <h1 class="font-display font-extrabold text-xl md:text-2xl text-bimo-navy tracking-tight leading-tight">
            Portefeuille Bailleurs
        </h1>
        <p class="font-body text-sm text-bimo-navy/50 mt-1">
            {{ $bailleurs->count() }} propriétaire(s) — Exercice {{ now()->year }}
        </p>
    </div>

    {{-- ═══ KPIs GLOBAUX ═══ --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">

        <div class="bg-bimo-gold/[8%] rounded-[14px] border border-bimo-gold/25 p-4">
            <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-gold/70 mb-1">Loyers encaissés</div>
            <div class="font-display font-extrabold text-xl text-bimo-gold leading-none">
                {{ number_format($totLoyers, 0, ',', ' ') }}
                <span class="font-body font-normal text-sm text-bimo-gold/50">F</span>
            </div>
            <div class="font-body text-[10.5px] text-bimo-gold/60 mt-1.5">FCFA — {{ now()->year }}</div>
        </div>

        <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-4">
            <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-navy/50 mb-1">Commissions TTC</div>
            <div class="font-display font-extrabold text-xl text-bimo-navy leading-none">
                {{ number_format($totComm, 0, ',', ' ') }}
                <span class="font-body font-normal text-sm text-bimo-navy/40">F</span>
            </div>
            <div class="font-body text-[10.5px] text-bimo-navy/40 mt-1.5">FCFA agence</div>
        </div>

        <div class="bg-white rounded-[14px] border border-bimo-red/20 p-4">
            <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-navy/50 mb-1">Dépenses gestion</div>
            <div class="font-display font-extrabold text-xl text-bimo-red leading-none">
                {{ number_format($totDepenses, 0, ',', ' ') }}
                <span class="font-body font-normal text-sm text-bimo-red/40">F</span>
            </div>
            <div class="font-body text-[10.5px] text-bimo-navy/40 mt-1.5">FCFA travaux & frais</div>
        </div>

        <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-4">
            <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-navy/50 mb-1">Net total à reverser</div>
            <div class="font-display font-extrabold text-xl text-bimo-navy leading-none">
                {{ number_format($totNet, 0, ',', ' ') }}
                <span class="font-body font-normal text-sm text-bimo-navy/40">F</span>
            </div>
            <div class="font-body text-[10.5px] text-bimo-navy/40 mt-1.5">FCFA aux bailleurs</div>
        </div>
    </div>

    {{-- ═══ CARDS BAILLEURS ═══ --}}
    @if($bailleurs->isEmpty())
    <div class="bg-white rounded-[14px] border border-bimo-navy/10 py-16 px-6 text-center">
        <div class="w-12 h-12 bg-bimo-navy/5 rounded-[12px] flex items-center justify-center mx-auto mb-4">
            <svg class="w-6 h-6 text-bimo-navy/30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/>
            </svg>
        </div>
        <div class="font-display font-bold text-base text-bimo-navy mb-2">Aucun bailleur trouvé</div>
        <p class="font-body text-sm text-bimo-navy/50">
            Ajoutez des biens avec des propriétaires pour les voir apparaître ici.
        </p>
    </div>

    @else
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @foreach($bailleurs as $b)
        @php $u = $b['user']; @endphp

        <a href="{{ route('admin.bailleurs.show', $u->id) }}"
           class="flex flex-col bg-white rounded-[14px] border border-bimo-navy/10
                  hover:border-bimo-gold/40 hover:shadow-gold-sm
                  transition-all duration-150 overflow-hidden group">

            {{-- En-tête sombre --}}
            <div class="bg-bimo-navy px-5 py-4 flex items-center gap-3">
                <div class="w-10 h-10 rounded-[9px] flex items-center justify-center flex-shrink-0
                            font-display font-bold text-sm text-bimo-gold
                            bg-bimo-gold/15 border border-bimo-gold/30">
                    {{ mb_strtoupper(mb_substr($u->name, 0, 2)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="font-display font-bold text-sm text-white truncate leading-tight">
                        {{ $u->name }}
                    </div>
                    <div class="font-body text-[11px] text-white/35 mt-0.5 truncate">
                        {{ $u->email ?? $u->telephone ?? 'Aucun contact' }}
                    </div>
                </div>
                @if($u->telephone && $u->email)
                <div class="flex-shrink-0 font-body text-[11px] text-white/30 text-right hidden sm:block">
                    {{ $u->telephone }}
                </div>
                @endif
                <svg class="w-4 h-4 text-white/20 group-hover:text-bimo-gold flex-shrink-0 transition-colors duration-150"
                     viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="9 18 15 12 9 6"/>
                </svg>
            </div>

            {{-- KPIs biens --}}
            <div class="grid grid-cols-3 border-b border-bimo-navy/[5%]">
                <div class="px-4 py-3 text-center border-r border-bimo-navy/[5%]">
                    <div class="font-body font-medium text-[9px] uppercase tracking-widest text-bimo-navy/40 mb-1">Biens</div>
                    <div class="font-display font-bold text-sm text-bimo-navy">{{ $b['nb_biens'] }}</div>
                </div>
                <div class="px-4 py-3 text-center border-r border-bimo-navy/[5%]">
                    <div class="font-body font-medium text-[9px] uppercase tracking-widest text-bimo-navy/40 mb-1">Loués</div>
                    <div class="font-display font-bold text-sm text-bimo-gold">{{ $b['nb_biens_loues'] }}</div>
                </div>
                <div class="px-4 py-3 text-center">
                    <div class="font-body font-medium text-[9px] uppercase tracking-widest text-bimo-navy/40 mb-1">Paiements</div>
                    <div class="font-display font-bold text-sm text-bimo-navy">{{ $b['nb_paiements'] }}</div>
                </div>
            </div>

            {{-- Résumé financier --}}
            <div class="px-5 py-3.5 flex-1 space-y-2">
                <div class="flex items-center justify-between">
                    <span class="font-body text-xs text-bimo-navy/50">Loyers encaissés</span>
                    <span class="font-display font-bold text-sm text-bimo-gold">
                        {{ number_format($b['total_loyers'], 0, ',', ' ') }} F
                    </span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="font-body text-xs text-bimo-navy/50">− Commissions TTC</span>
                    <span class="font-body font-medium text-xs text-bimo-navy/60">
                        {{ number_format($b['total_commissions'], 0, ',', ' ') }} F
                    </span>
                </div>
                @if($b['total_depenses'] > 0)
                <div class="flex items-center justify-between">
                    <span class="font-body text-xs text-bimo-navy/50">− Dépenses gestion</span>
                    <span class="font-body font-medium text-xs text-bimo-red">
                        {{ number_format($b['total_depenses'], 0, ',', ' ') }} F
                    </span>
                </div>
                @endif
            </div>

            {{-- Net final --}}
            <div class="mx-4 mb-4 px-4 py-3 bg-bimo-navy rounded-[10px] border border-bimo-gold/20
                        flex items-center justify-between">
                <div>
                    <div class="font-body font-medium text-[9px] uppercase tracking-widest text-bimo-gold/50 mb-0.5">
                        Net à reverser
                    </div>
                    <div class="font-body text-[10px] text-white/25">{{ now()->year }} — tous mois</div>
                </div>
                <div class="font-display font-extrabold text-lg text-bimo-gold">
                    {{ number_format($b['net_final'], 0, ',', ' ') }} F
                </div>
            </div>

        </a>
        @endforeach
    </div>
    @endif

</div>
@endsection
