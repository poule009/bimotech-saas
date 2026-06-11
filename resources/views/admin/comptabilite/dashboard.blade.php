@extends('layouts.app')
@section('header', 'Comptabilité')

@section('content')
@php
    // ── Étapes de la « routine du mois » (langage simple, non-comptables) ──
    $loyersOk   = $nbContratsActifs > 0 && $nbLoyersPayes >= $nbContratsActifs;
    $proprioOk  = $nbProprietairesAPayer === 0;
    $resultat_net = $resultat['resultat_net'];
@endphp
<div class="space-y-4 md:space-y-6">

    {{-- En-tête --}}
    <div class="flex items-start justify-between gap-4">
        <div>
            <h1 class="font-display font-extrabold text-xl md:text-2xl text-bimo-text tracking-tight leading-tight">Comptabilité</h1>
            <p class="font-body text-sm text-bimo-text/50 mt-1">Votre argent ce mois — {{ now()->locale('fr')->translatedFormat('F Y') }}</p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('admin.charges-agence.create') }}"
               class="inline-flex items-center gap-2 bg-white border border-bimo-navy/15 text-bimo-text/70 hover:text-bimo-text hover:border-bimo-navy/30 font-display font-bold text-sm px-4 py-2.5 rounded-[10px] transition-all duration-150">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                <span class="hidden sm:inline">Noter une dépense</span>
            </a>
            <a href="{{ route('admin.reversements.create') }}"
               class="inline-flex items-center gap-2 bg-[var(--ac)] text-white font-display font-bold text-sm px-4 py-2.5 rounded-[10px] hover:opacity-90 transition-opacity duration-150">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="17 1 21 5 17 9"/><path d="M3 11V9a4 4 0 0 1 4-4h14"/><polyline points="7 23 3 19 7 15"/><path d="M21 13v2a4 4 0 0 1-4 4H3"/></svg>
                <span class="hidden sm:inline">Payer un propriétaire</span>
            </a>
        </div>
    </div>

    {{-- ═══ ROUTINE DU MOIS — le fil conducteur, en français simple ═══ --}}
    <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
        <div class="px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
            <h2 class="font-display font-bold text-sm text-bimo-text">Ce que vous avez à faire ce mois</h2>
            <p class="font-body text-xs text-bimo-text/50 mt-0.5">Suivez ces étapes dans l'ordre — le reste se calcule tout seul.</p>
        </div>
        <div class="divide-y divide-bimo-navy/[5%]">

            {{-- Étape 1 — Encaisser les loyers --}}
            <div class="flex items-start gap-4 px-5 py-4">
                <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5 {{ $loyersOk ? 'bg-bimo-gold/15' : 'bg-bimo-navy/[7%]' }}">
                    @if($loyersOk)
                    <svg class="w-4 h-4 text-bimo-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    @else
                    <span class="font-display font-bold text-sm text-bimo-text/50">1</span>
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-display font-bold text-sm text-bimo-text">Encaisser les loyers du mois</p>
                    <p class="font-body text-xs text-bimo-text/50 mt-0.5">Notez chaque loyer reçu — la quittance se génère automatiquement.</p>
                </div>
                <div class="text-right flex-shrink-0">
                    <p class="font-display font-bold text-sm {{ $loyersOk ? 'text-bimo-gold' : 'text-bimo-text' }}">{{ $nbLoyersPayes }}/{{ $nbContratsActifs }}</p>
                    @if($loyersOk)
                    <p class="font-body text-[11px] text-bimo-gold/70">tous encaissés ✓</p>
                    @else
                    <a href="{{ route('admin.impayes.index') }}" class="font-body text-[11px] font-semibold text-[var(--ac)] hover:underline">Voir les impayés →</a>
                    @endif
                </div>
            </div>

            {{-- Étape 2 — Noter les dépenses --}}
            <div class="flex items-start gap-4 px-5 py-4">
                <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5 {{ $nbDepensesMois > 0 ? 'bg-bimo-gold/15' : 'bg-bimo-navy/[7%]' }}">
                    @if($nbDepensesMois > 0)
                    <svg class="w-4 h-4 text-bimo-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    @else
                    <span class="font-display font-bold text-sm text-bimo-text/50">2</span>
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-display font-bold text-sm text-bimo-text">Noter les dépenses de l'agence</p>
                    <p class="font-body text-xs text-bimo-text/50 mt-0.5">Loyer du bureau, salaires, électricité, internet…</p>
                </div>
                <div class="text-right flex-shrink-0">
                    <p class="font-display font-bold text-sm text-bimo-text">{{ $nbDepensesMois }}</p>
                    <a href="{{ route('admin.charges-agence.create') }}" class="font-body text-[11px] font-semibold text-[var(--ac)] hover:underline">+ Ajouter</a>
                </div>
            </div>

            {{-- Étape 3 — Payer les propriétaires (l'étape clé) --}}
            <div class="flex items-start gap-4 px-5 py-4 {{ $proprioOk ? '' : 'bg-bimo-red/[3%]' }}">
                <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5 {{ $proprioOk ? 'bg-bimo-gold/15' : 'bg-bimo-red/15' }}">
                    @if($proprioOk)
                    <svg class="w-4 h-4 text-bimo-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    @else
                    <span class="font-display font-bold text-sm text-bimo-red">3</span>
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-display font-bold text-sm text-bimo-text">Payer les propriétaires</p>
                    <p class="font-body text-xs text-bimo-text/50 mt-0.5">Versez à chaque propriétaire sa part des loyers, après votre commission.</p>
                </div>
                <div class="text-right flex-shrink-0">
                    @if($proprioOk)
                    <p class="font-display font-bold text-sm text-bimo-gold">À jour ✓</p>
                    <p class="font-body text-[11px] text-bimo-gold/70">personne à payer</p>
                    @else
                    <p class="font-display font-bold text-sm text-bimo-red">{{ number_format($totalAPayer, 0, ',', ' ') }} FCFA</p>
                    <p class="font-body text-[11px] text-bimo-red/70">{{ $nbProprietairesAPayer }} propriétaire{{ $nbProprietairesAPayer > 1 ? 's' : '' }} à payer ↓</p>
                    @endif
                </div>
            </div>

            {{-- Étape 4 — Voir ce qu'il reste --}}
            <div class="flex items-start gap-4 px-5 py-4">
                <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5 bg-bimo-navy/[7%]">
                    <svg class="w-4 h-4 text-bimo-text/50" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/><circle cx="12" cy="12" r="3"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-display font-bold text-sm text-bimo-text">Voir ce qu'il vous reste</p>
                    <p class="font-body text-xs text-bimo-text/50 mt-0.5">Vos revenus moins vos dépenses, pour ce mois.</p>
                </div>
                <div class="text-right flex-shrink-0">
                    <p class="font-display font-bold text-sm {{ $resultat_net >= 0 ? 'text-bimo-text' : 'text-bimo-red' }}">{{ ($resultat_net >= 0 ? '+' : '') . number_format($resultat_net, 0, ',', ' ') }} FCFA</p>
                    <a href="{{ route('admin.comptabilite.compte-resultat') }}" class="font-body text-[11px] font-semibold text-[var(--ac)] hover:underline">Voir le détail →</a>
                </div>
            </div>

        </div>
    </div>

    {{-- À PAYER AUX PROPRIÉTAIRES — l'action concrète, juste après la routine --}}
    @if($proprietairesEnAttente->isNotEmpty())
    <div class="bg-white rounded-[14px] border border-bimo-red/20 overflow-hidden">
        <div class="px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-red/[4%] flex items-center justify-between">
            <div>
                <span class="font-display font-bold text-sm text-bimo-text">À payer aux propriétaires</span>
                <p class="font-body text-xs text-bimo-text/50 mt-0.5">Leur part des loyers que vous n'avez pas encore versée.</p>
            </div>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-body font-medium bg-bimo-red/10 border border-bimo-red/20 text-bimo-red flex-shrink-0">{{ $proprietairesEnAttente->count() }}</span>
        </div>
        <div class="divide-y divide-bimo-navy/[5%]">
            @foreach($proprietairesEnAttente->sortByDesc('solde') as $prop)
            <div class="px-5 py-3.5 flex items-center justify-between gap-4">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-8 h-8 rounded-full bg-bimo-navy/10 flex items-center justify-center font-display font-bold text-xs text-bimo-text/60 flex-shrink-0">
                        {{ strtoupper(substr($prop->name, 0, 1)) }}
                    </div>
                    <p class="font-body font-medium text-sm text-bimo-text truncate">{{ $prop->name }}</p>
                </div>
                <div class="flex items-center gap-3 flex-shrink-0">
                    <p class="font-display font-bold text-sm text-bimo-red">{{ number_format($prop->solde, 0, ',', ' ') }} FCFA</p>
                    <a href="{{ route('admin.reversements.compte-mandant', $prop) }}"
                       class="inline-flex items-center px-3 py-1.5 bg-[var(--ac)] text-white rounded-[8px] font-body font-semibold text-xs hover:opacity-90 transition-opacity duration-150">
                        Payer
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ═══ POUR ALLER PLUS LOIN (reporting) ═══ --}}
    <div class="pt-2">
        <p class="font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/35 mb-3 px-1">Vue d'ensemble</p>

        {{-- Chiffres clés du mois --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">
            <div class="bg-bimo-gold/[8%] rounded-[14px] border border-bimo-gold/25 p-4">
                <p class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-gold/70 mb-2">Revenus de l'agence</p>
                <p class="font-display font-extrabold text-xl text-bimo-gold">{{ number_format($resultat['revenus_total_ht'], 0, ',', ' ') }}</p>
                <p class="font-body font-light text-[10.5px] text-bimo-gold/60 mt-1">FCFA — vos commissions</p>
            </div>
            <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-4">
                <p class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-text/50 mb-2">Dépenses</p>
                <p class="font-display font-extrabold text-xl text-bimo-text">{{ number_format($resultat['charges_total'], 0, ',', ' ') }}</p>
                <p class="font-body font-light text-[10.5px] text-bimo-text/40 mt-1">FCFA ce mois</p>
            </div>
            <div class="bg-white rounded-[14px] border {{ $resultat_net >= 0 ? 'border-bimo-navy/10' : 'border-bimo-red/20' }} p-4">
                <p class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-text/50 mb-2">Ce qu'il reste</p>
                <p class="font-display font-extrabold text-xl {{ $resultat_net >= 0 ? 'text-bimo-text' : 'text-bimo-red' }}">
                    {{ ($resultat_net >= 0 ? '+' : '') . number_format($resultat_net, 0, ',', ' ') }}
                </p>
                <p class="font-body font-light text-[10.5px] text-bimo-text/40 mt-1">FCFA — bénéfice</p>
            </div>
            <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-4">
                <p class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-text/50 mb-2">À payer aux propriétaires</p>
                <p class="font-display font-extrabold text-xl text-bimo-text">{{ number_format($soldesMandants->sum('solde'), 0, ',', ' ') }}</p>
                <p class="font-body font-light text-[10.5px] text-bimo-text/40 mt-1">FCFA au total</p>
            </div>
        </div>
    </div>

    {{-- Revenus vs Dépenses 6 mois --}}
    <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-5">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-display font-bold text-sm text-bimo-text">Évolution sur 6 mois</h2>
            <div class="flex items-center gap-4 font-body text-[10px] uppercase tracking-widest text-bimo-text/40">
                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-bimo-gold inline-block"></span>Revenus</span>
                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-bimo-navy/30 inline-block"></span>Dépenses</span>
            </div>
        </div>
        @php $max = $sixMois->max(fn($x) => max($x['revenus'], $x['charges'])) ?: 1; @endphp
        <div class="grid grid-cols-6 gap-2 items-end h-32">
            @foreach($sixMois as $m)
            <div class="flex flex-col items-center gap-1">
                <div class="w-full flex items-end gap-0.5 h-24">
                    <div class="flex-1 rounded-t-[3px] bg-bimo-gold/70 transition-all duration-300"
                         style="height: {{ max(4, round($m['revenus'] / $max * 100)) }}%"></div>
                    <div class="flex-1 rounded-t-[3px] bg-bimo-navy/20 transition-all duration-300"
                         style="height: {{ max(4, round($m['charges'] / $max * 100)) }}%"></div>
                </div>
                <span class="font-body text-[9.5px] uppercase tracking-widest text-bimo-text/30">{{ $m['mois'] }}</span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Raccourcis (libellés en clair) --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
        <a href="{{ route('admin.comptabilite.compte-resultat') }}"
           class="bg-white rounded-[14px] border border-bimo-navy/10 p-5 flex items-center gap-4 hover:border-bimo-navy/25 transition-colors duration-150">
            <div class="w-10 h-10 rounded-[10px] bg-bimo-navy/[5%] flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-bimo-text/60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
            </div>
            <div>
                <p class="font-display font-bold text-sm text-bimo-text">Mes revenus &amp; dépenses</p>
                <p class="font-body text-xs text-bimo-text/40 mt-0.5">Le détail, mois par mois</p>
            </div>
        </a>
        <a href="{{ route('admin.charges-agence.index') }}"
           class="bg-white rounded-[14px] border border-bimo-navy/10 p-5 flex items-center gap-4 hover:border-bimo-navy/25 transition-colors duration-150">
            <div class="w-10 h-10 rounded-[10px] bg-bimo-navy/[5%] flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-bimo-text/60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            </div>
            <div>
                <p class="font-display font-bold text-sm text-bimo-text">Dépenses de l'agence</p>
                <p class="font-body text-xs text-bimo-text/40 mt-0.5">Loyer, salaires, factures…</p>
            </div>
        </a>
        <a href="{{ route('admin.reversements.index') }}"
           class="bg-white rounded-[14px] border border-bimo-navy/10 p-5 flex items-center gap-4 hover:border-bimo-navy/25 transition-colors duration-150">
            <div class="w-10 h-10 rounded-[10px] bg-bimo-navy/[5%] flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-bimo-text/60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><polyline points="17 1 21 5 17 9"/><path d="M3 11V9a4 4 0 0 1 4-4h14"/><polyline points="7 23 3 19 7 15"/><path d="M21 13v2a4 4 0 0 1-4 4H3"/></svg>
            </div>
            <div>
                <p class="font-display font-bold text-sm text-bimo-text">Payer les propriétaires</p>
                <p class="font-body text-xs text-bimo-text/40 mt-0.5">Versements et historique</p>
            </div>
        </a>
    </div>

</div>
@endsection
