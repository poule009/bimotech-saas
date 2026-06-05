@extends('layouts.app')
@section('header', 'Comptabilité')

@section('content')
<div class="space-y-4 md:space-y-6">

    {{-- En-tête --}}
    <div class="flex items-start justify-between gap-4">
        <div>
            <h1 class="font-display font-extrabold text-xl md:text-2xl text-bimo-text tracking-tight leading-tight">Comptabilité</h1>
            <p class="font-body text-sm text-bimo-text/50 mt-1">{{ now()->locale('fr')->translatedFormat('F Y') }}</p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('admin.charges-agence.create') }}"
               class="inline-flex items-center gap-2 bg-bimo-navy hover:bg-bimo-navy-dk text-white font-display font-bold text-sm px-4 py-2.5 rounded-[10px] transition-colors duration-150">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                <span class="hidden sm:inline">Charge</span>
            </a>
            <a href="{{ route('admin.reversements.create') }}"
               class="inline-flex items-center gap-2 bg-[var(--ac)] text-white font-display font-bold text-sm px-4 py-2.5 rounded-[10px] hover:opacity-90 transition-opacity duration-150">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="17 1 21 5 17 9"/><path d="M3 11V9a4 4 0 0 1 4-4h14"/><polyline points="7 23 3 19 7 15"/><path d="M21 13v2a4 4 0 0 1-4 4H3"/></svg>
                <span class="hidden sm:inline">Reversement</span>
            </a>
        </div>
    </div>

    {{-- KPIs du mois --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">
        <div class="bg-bimo-gold/[8%] rounded-[14px] border border-bimo-gold/25 p-4">
            <p class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-gold/70 mb-2">Revenus du mois</p>
            <p class="font-display font-extrabold text-xl text-bimo-gold">{{ number_format($resultat['revenus_total_ht'], 0, ',', ' ') }}</p>
            <p class="font-body font-light text-[10.5px] text-bimo-gold/60 mt-1">FCFA HT</p>
        </div>
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-4">
            <p class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-text/50 mb-2">Charges du mois</p>
            <p class="font-display font-extrabold text-xl text-bimo-text">{{ number_format($resultat['charges_total'], 0, ',', ' ') }}</p>
            <p class="font-body font-light text-[10.5px] text-bimo-text/40 mt-1">FCFA</p>
        </div>
        <div class="bg-white rounded-[14px] border {{ $resultat['resultat_net'] >= 0 ? 'border-bimo-navy/10' : 'border-bimo-red/20' }} p-4">
            <p class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-text/50 mb-2">Résultat net</p>
            <p class="font-display font-extrabold text-xl {{ $resultat['resultat_net'] >= 0 ? 'text-bimo-text' : 'text-bimo-red' }}">
                {{ ($resultat['resultat_net'] >= 0 ? '+' : '') . number_format($resultat['resultat_net'], 0, ',', ' ') }}
            </p>
            <p class="font-body font-light text-[10.5px] text-bimo-text/40 mt-1">FCFA</p>
        </div>
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-4">
            <p class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-text/50 mb-2">Dû aux propriétaires</p>
            <p class="font-display font-extrabold text-xl text-bimo-text">{{ number_format($soldesMandants->sum('solde'), 0, ',', ' ') }}</p>
            <p class="font-body font-light text-[10.5px] text-bimo-text/40 mt-1">FCFA total</p>
        </div>
    </div>

    {{-- Revenus vs Charges 6 mois --}}
    <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-5">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-display font-bold text-sm text-bimo-text">Évolution 6 derniers mois</h2>
            <div class="flex items-center gap-4 font-body text-[10px] uppercase tracking-widest text-bimo-text/40">
                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-bimo-gold inline-block"></span>Revenus</span>
                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-bimo-navy/30 inline-block"></span>Charges</span>
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
                <span class="font-body text-[9px] uppercase tracking-widest text-bimo-text/30">{{ $m['mois'] }}</span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Raccourcis --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
        <a href="{{ route('admin.comptabilite.compte-resultat') }}"
           class="bg-white rounded-[14px] border border-bimo-navy/10 p-5 flex items-center gap-4 hover:border-bimo-navy/25 transition-colors duration-150">
            <div class="w-10 h-10 rounded-[10px] bg-bimo-navy/[5%] flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-bimo-text/60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
            </div>
            <div>
                <p class="font-display font-bold text-sm text-bimo-text">Compte de résultat</p>
                <p class="font-body text-xs text-bimo-text/40 mt-0.5">Revenus, charges, résultat net</p>
            </div>
        </a>
        <a href="{{ route('admin.charges-agence.index') }}"
           class="bg-white rounded-[14px] border border-bimo-navy/10 p-5 flex items-center gap-4 hover:border-bimo-navy/25 transition-colors duration-150">
            <div class="w-10 h-10 rounded-[10px] bg-bimo-navy/[5%] flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-bimo-text/60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            </div>
            <div>
                <p class="font-display font-bold text-sm text-bimo-text">Charges agence</p>
                <p class="font-body text-xs text-bimo-text/40 mt-0.5">Salaires, loyer, téléphone...</p>
            </div>
        </a>
        <a href="{{ route('admin.reversements.index') }}"
           class="bg-white rounded-[14px] border border-bimo-navy/10 p-5 flex items-center gap-4 hover:border-bimo-navy/25 transition-colors duration-150">
            <div class="w-10 h-10 rounded-[10px] bg-bimo-navy/[5%] flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-bimo-text/60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><polyline points="17 1 21 5 17 9"/><path d="M3 11V9a4 4 0 0 1 4-4h14"/><polyline points="7 23 3 19 7 15"/><path d="M21 13v2a4 4 0 0 1-4 4H3"/></svg>
            </div>
            <div>
                <p class="font-display font-bold text-sm text-bimo-text">Reversements</p>
                <p class="font-body text-xs text-bimo-text/40 mt-0.5">Comptes mandants propriétaires</p>
            </div>
        </a>
    </div>

    {{-- Propriétaires avec solde en attente --}}
    @if($proprietairesEnAttente->isNotEmpty())
    <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
        <div class="px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2 flex items-center justify-between">
            <span class="font-display font-bold text-sm text-bimo-text">Soldes en attente de reversement</span>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-body font-medium bg-bimo-red/10 border border-bimo-red/20 text-bimo-red">{{ $proprietairesEnAttente->count() }}</span>
        </div>
        <div class="divide-y divide-bimo-navy/[5%]">
            @foreach($proprietairesEnAttente->sortByDesc('solde') as $prop)
            <div class="px-5 py-3.5 flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-bimo-navy/10 flex items-center justify-center font-display font-bold text-xs text-bimo-text/60 flex-shrink-0">
                        {{ strtoupper(substr($prop->name, 0, 1)) }}
                    </div>
                    <p class="font-body font-medium text-sm text-bimo-text">{{ $prop->name }}</p>
                </div>
                <div class="flex items-center gap-3">
                    <p class="font-display font-bold text-sm text-bimo-red">{{ number_format($prop->solde, 0, ',', ' ') }} F</p>
                    <a href="{{ route('admin.reversements.compte-mandant', $prop) }}"
                       class="inline-flex items-center px-3 py-1 border border-bimo-navy/15 rounded-[7px] font-body text-xs text-bimo-text/60 hover:border-bimo-navy/30 hover:text-bimo-text transition-all duration-150">
                        Voir
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div>
@endsection
