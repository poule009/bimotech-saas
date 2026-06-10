@extends('layouts.app')
@section('header', 'Propriétaires')

@section('content')
<div class="space-y-4 md:space-y-6">

    {{-- En-tête --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <h1 class="font-display font-extrabold text-xl md:text-2xl text-bimo-text tracking-tight leading-tight">Propriétaires</h1>
            <p class="font-body text-sm text-bimo-text/50 mt-1">{{ $stats['total'] }} propriétaire(s) — Exercice {{ now()->year }}</p>
        </div>
        <a href="{{ route('admin.users.create', 'proprietaire') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-[var(--ac)] text-white
                  font-display font-bold text-sm rounded-[10px] hover:opacity-90 transition-opacity duration-150 self-start">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Nouveau propriétaire
        </a>
    </div>

    {{-- KPIs globaux --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-4">
            <p class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-text/50 mb-1">Propriétaires</p>
            <p class="font-display font-extrabold text-xl text-bimo-text leading-none">{{ $stats['total'] }}</p>
            <p class="font-body text-[10.5px] text-bimo-text/40 mt-1">{{ $stats['biens_loues'] }}/{{ $stats['total_biens'] }} biens loués</p>
        </div>
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-4">
            <p class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-text/50 mb-1">Biens gérés</p>
            <p class="font-display font-extrabold text-xl text-bimo-text leading-none">{{ $stats['total_biens'] }}</p>
            @php $taux = $stats['total_biens'] > 0 ? round($stats['biens_loues'] / $stats['total_biens'] * 100) : 0; @endphp
            <p class="font-body text-[10.5px] text-bimo-text/40 mt-1">{{ $taux }}% d'occupation</p>
        </div>
        <div class="bg-bimo-gold/[8%] rounded-[14px] border border-bimo-gold/25 p-4">
            <p class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-gold/70 mb-1">Loyers encaissés</p>
            <p class="font-display font-extrabold text-xl text-bimo-gold leading-none">{{ number_format($stats['total_loyers'], 0, ',', ' ') }}</p>
            <p class="font-body text-[10.5px] text-bimo-gold/60 mt-1">FCFA — {{ now()->year }}</p>
        </div>
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-4">
            <p class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-text/50 mb-1">Net à reverser</p>
            <p class="font-display font-extrabold text-xl text-bimo-text leading-none">{{ number_format($stats['total_net'], 0, ',', ' ') }}</p>
            <p class="font-body text-[10.5px] text-bimo-text/40 mt-1">FCFA total</p>
        </div>
    </div>

    {{-- Liste --}}
    @if($proprietaires->isEmpty())
    <div class="bg-white rounded-[14px] border border-bimo-navy/10 py-16 px-6 text-center">
        <div class="w-12 h-12 bg-bimo-navy/5 rounded-[12px] flex items-center justify-center mx-auto mb-4">
            <svg class="w-6 h-6 text-bimo-text/30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/>
            </svg>
        </div>
        <div class="font-display font-bold text-base text-bimo-text mb-2">Aucun propriétaire enregistré</div>
        <p class="font-body text-sm text-bimo-text/50 mb-5">Commencez par ajouter le premier propriétaire de votre agence.</p>
        <a href="{{ route('admin.users.create', 'proprietaire') }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 bg-[var(--ac)] text-white font-display font-bold text-sm rounded-[10px] hover:opacity-90 transition-opacity duration-150">
            + Ajouter un propriétaire
        </a>
    </div>

    @else
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @foreach($proprietaires as $item)
        @php
            $u     = $item['user'];
            $profil = $u->proprietaire;
            $modeIcons  = ['virement'=>'🏦','wave'=>'📱','orange_money'=>'🟠','especes'=>'💵','cheque'=>'📝','mobile_money'=>'📲'];
            $modeLabels = ['virement'=>'Virement','wave'=>'Wave','orange_money'=>'Orange Money','especes'=>'Espèces','cheque'=>'Chèque','mobile_money'=>'Mobile Money'];
            $mode = $profil?->mode_paiement_prefere ?? 'virement';
        @endphp

        <div class="flex flex-col bg-white rounded-[14px] border border-bimo-navy/10 hover:border-bimo-navy/25 transition-all duration-150 overflow-hidden">

            {{-- En-tête identité --}}
            <div class="bg-bimo-navy px-5 py-4 flex items-center gap-3">
                <div class="w-10 h-10 rounded-[9px] flex items-center justify-center flex-shrink-0
                            font-display font-bold text-sm text-white bg-white/15">
                    {{ mb_strtoupper(mb_substr($u->name, 0, 2)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="font-display font-bold text-sm text-white truncate leading-tight">{{ $u->name }}</div>
                    <div class="font-body text-[11px] text-white/40 mt-0.5 truncate">{{ $u->email ?? $u->telephone ?? 'Aucun contact' }}</div>
                </div>
                @if($u->telephone && $u->email)
                <div class="font-body text-[11px] text-white/30 text-right hidden sm:block flex-shrink-0">{{ $u->telephone }}</div>
                @endif
            </div>

            {{-- Détails identité --}}
            <div class="px-5 py-3 border-b border-bimo-navy/[5%] flex items-center gap-4 flex-wrap">
                @if($profil?->ville)
                <span class="font-body text-xs text-bimo-text/50 flex items-center gap-1">
                    <svg class="w-3 h-3 text-bimo-text/30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    {{ $profil->ville }}
                </span>
                @endif
                @if($profil?->ninea)
                <span class="font-body text-xs text-bimo-text/50 flex items-center gap-1">
                    <svg class="w-3 h-3 text-bimo-text/30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v16"/></svg>
                    {{ $profil->ninea }}
                </span>
                @endif
                <span class="font-body text-xs text-bimo-text/50 flex items-center gap-1">
                    <span>{{ $modeIcons[$mode] ?? '💳' }}</span>
                    {{ $modeLabels[$mode] ?? ucfirst($mode) }}
                </span>
            </div>

            {{-- Activité biens --}}
            <div class="grid grid-cols-3 border-b border-bimo-navy/[5%]">
                <div class="px-4 py-3 text-center border-r border-bimo-navy/[5%]">
                    <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-text/40 mb-1">Biens</div>
                    <div class="font-display font-bold text-sm text-bimo-text">{{ $item['nb_biens'] }}</div>
                </div>
                <div class="px-4 py-3 text-center border-r border-bimo-navy/[5%]">
                    <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-text/40 mb-1">Loués</div>
                    <div class="font-display font-bold text-sm {{ $item['nb_biens_loues'] > 0 ? 'text-bimo-gold' : 'text-bimo-text/30' }}">{{ $item['nb_biens_loues'] }}</div>
                </div>
                <div class="px-4 py-3 text-center">
                    <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-text/40 mb-1">Paiements</div>
                    <div class="font-display font-bold text-sm text-bimo-text">{{ $item['nb_paiements'] }}</div>
                </div>
            </div>

            {{-- Résumé financier --}}
            <div class="px-5 py-3.5 flex-1 space-y-1.5">
                <div class="flex items-center justify-between">
                    <span class="font-body text-xs text-bimo-text/50">Loyers encaissés</span>
                    <span class="font-display font-semibold text-sm text-bimo-gold">{{ number_format($item['total_loyers'], 0, ',', ' ') }} FCFA</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="font-body text-xs text-bimo-text/50">− Commissions TTC</span>
                    <span class="font-body text-xs text-bimo-text/60">{{ number_format($item['total_commissions'], 0, ',', ' ') }} FCFA</span>
                </div>
                @if($item['total_depenses'] > 0)
                <div class="flex items-center justify-between">
                    <span class="font-body text-xs text-bimo-text/50">− Dépenses gestion</span>
                    <span class="font-body text-xs text-bimo-red">{{ number_format($item['total_depenses'], 0, ',', ' ') }} FCFA</span>
                </div>
                @endif
            </div>

            {{-- Net + actions --}}
            <div class="mx-4 mb-4 px-4 py-3 bg-bimo-navy rounded-[10px] flex items-center justify-between gap-3">
                <div>
                    <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-white/40 mb-0.5">Net à reverser</div>
                    <div class="font-display font-extrabold text-lg text-white">{{ number_format($item['net_final'], 0, ',', ' ') }} <span class="font-body font-normal text-sm text-white/40">F</span></div>
                </div>
                <div class="flex items-center gap-1.5 flex-shrink-0">
                    <a href="{{ route('admin.users.show', $u) }}"
                       class="w-8 h-8 flex items-center justify-center rounded-[7px] bg-white/10 hover:bg-white/20 text-white transition-all duration-150"
                       title="Fiche propriétaire">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </a>
                    <a href="{{ route('admin.bailleurs.show', $u->id) }}"
                       class="w-8 h-8 flex items-center justify-center rounded-[7px] bg-white/10 hover:bg-white/20 text-white transition-all duration-150"
                       title="Portefeuille détaillé">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v16"/></svg>
                    </a>
                    <a href="{{ route('admin.users.edit', $u) }}"
                       class="w-8 h-8 flex items-center justify-center rounded-[7px] bg-white/10 hover:bg-white/20 text-white transition-all duration-150"
                       title="Modifier">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    </a>
                </div>
            </div>

        </div>
        @endforeach
    </div>
    @endif

</div>
@endsection
