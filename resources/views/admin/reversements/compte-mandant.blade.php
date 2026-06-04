@extends('layouts.app')
@section('header', 'Compte mandant')

@section('content')
<div class="space-y-4 md:space-y-6">

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.reversements.index') }}" class="text-bimo-navy/40 hover:text-bimo-navy transition-colors duration-150">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
            </a>
            <div>
                <h1 class="font-display font-extrabold text-xl text-bimo-navy tracking-tight leading-tight">{{ $proprietaire->name }}</h1>
                <p class="font-body text-sm text-bimo-navy/50 mt-0.5">Compte mandant</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <form method="GET">
                <select name="periode" onchange="this.form.submit()" class="px-3 py-2 rounded-[8px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-navy focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150 appearance-none cursor-pointer">
                    <option value="">Toutes les périodes</option>
                    @foreach($periodes as $p)
                    <option value="{{ $p }}" {{ $periode === $p ? 'selected' : '' }}>{{ \Carbon\Carbon::createFromFormat('Y-m', $p)->locale('fr')->translatedFormat('F Y') }}</option>
                    @endforeach
                </select>
            </form>
            <a href="{{ route('admin.reversements.create', ['proprietaire_id' => $proprietaire->id]) }}"
               class="inline-flex items-center gap-2 bg-[var(--ac)] text-white font-display font-bold text-sm px-4 py-2 rounded-[10px] hover:opacity-90 transition-opacity duration-150">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="17 1 21 5 17 9"/><path d="M3 11V9a4 4 0 0 1 4-4h14"/><polyline points="7 23 3 19 7 15"/><path d="M21 13v2a4 4 0 0 1-4 4H3"/></svg>
                Reverser
            </a>
        </div>
    </div>

    {{-- Relevé de compte --}}
    <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
        <div class="px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
            <span class="font-display font-bold text-sm text-bimo-navy">Relevé de compte</span>
        </div>
        <div class="divide-y divide-bimo-navy/[5%]">
            <div class="px-5 py-4 flex items-center justify-between">
                <p class="font-body text-sm text-bimo-navy/70">Loyers encaissés</p>
                <p class="font-display font-bold text-sm text-bimo-gold">+{{ number_format($compte['loyers_encaisses'], 0, ',', ' ') }} F</p>
            </div>
            <div class="px-5 py-4 flex items-center justify-between">
                <p class="font-body text-sm text-bimo-navy/70">Commission agence déduite</p>
                <p class="font-display font-bold text-sm text-bimo-navy/60">-{{ number_format($compte['commissions_deduites'], 0, ',', ' ') }} F</p>
            </div>
            <div class="px-5 py-4 flex items-center justify-between">
                <p class="font-body text-sm text-bimo-navy/70">BRS retenu (5%)</p>
                <p class="font-display font-bold text-sm text-bimo-navy/60">-{{ number_format($compte['brs_retenu'], 0, ',', ' ') }} F</p>
            </div>
            @if($compte['depenses_avancees'] > 0)
            <div class="px-5 py-4 flex items-center justify-between">
                <p class="font-body text-sm text-bimo-navy/70">Dépenses avancées (réparations...)</p>
                <p class="font-display font-bold text-sm text-bimo-navy/60">-{{ number_format($compte['depenses_avancees'], 0, ',', ' ') }} F</p>
            </div>
            @endif
            <div class="px-5 py-4 flex items-center justify-between bg-bimo-navy/[3%]">
                <p class="font-display font-bold text-sm text-bimo-navy">Net dû</p>
                <p class="font-display font-extrabold text-base text-bimo-navy">{{ number_format($compte['net_du'], 0, ',', ' ') }} F</p>
            </div>
            <div class="px-5 py-4 flex items-center justify-between">
                <p class="font-body text-sm text-bimo-navy/70">Reversements effectués</p>
                <p class="font-display font-bold text-sm text-bimo-navy/60">-{{ number_format($compte['reversements_effectues'], 0, ',', ' ') }} F</p>
            </div>
            <div class="px-5 py-4 flex items-center justify-between {{ $compte['solde_restant'] > 0 ? 'bg-bimo-red/[5%]' : 'bg-bimo-gold/[5%]' }}">
                <p class="font-display font-bold text-sm {{ $compte['solde_restant'] > 0 ? 'text-bimo-red' : 'text-bimo-navy' }}">Solde restant dû</p>
                <p class="font-display font-extrabold text-xl {{ $compte['solde_restant'] > 0 ? 'text-bimo-red' : 'text-bimo-gold' }}">
                    {{ number_format($compte['solde_restant'], 0, ',', ' ') }} F
                </p>
            </div>
        </div>
    </div>

    {{-- Historique reversements --}}
    @if($reversements->isNotEmpty())
    <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
        <div class="px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
            <span class="font-display font-bold text-sm text-bimo-navy">Reversements effectués</span>
        </div>
        <div class="divide-y divide-bimo-navy/[5%]">
            @foreach($reversements as $rev)
            <div class="px-5 py-3.5 flex items-center justify-between gap-4">
                <div>
                    <p class="font-body text-sm text-bimo-navy">{{ $rev->date_reversement->format('d/m/Y') }}</p>
                    <div class="flex items-center gap-2 mt-0.5">
                        <span class="inline-flex items-center px-2 py-0 rounded text-[10px] font-body font-medium bg-bimo-navy/10 border border-bimo-navy/15 text-bimo-navy/60">{{ $rev->mode_paiement_libelle }}</span>
                        @if($rev->reference)<span class="font-body text-[10px] text-bimo-navy/30">{{ $rev->reference }}</span>@endif
                        @if($rev->periode_debut)<span class="font-body text-[10px] text-bimo-navy/30">{{ $rev->periode_debut }}{{ $rev->periode_fin && $rev->periode_fin !== $rev->periode_debut ? ' → '.$rev->periode_fin : '' }}</span>@endif
                    </div>
                </div>
                <p class="font-display font-bold text-sm text-bimo-gold flex-shrink-0">{{ number_format($rev->montant, 0, ',', ' ') }} F</p>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Paiements détail --}}
    @if($compte['paiements']->isNotEmpty())
    <div class="hidden md:block bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
        <div class="px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
            <span class="font-display font-bold text-sm text-bimo-navy">Détail des paiements</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-bimo-navy/[5%] bg-bimo-bg">
                        <th class="px-4 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40">Période</th>
                        <th class="px-4 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40">Bien</th>
                        <th class="px-4 py-3 text-right font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40">Encaissé</th>
                        <th class="px-4 py-3 text-right font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40">Commission</th>
                        <th class="px-4 py-3 text-right font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40">Dépenses</th>
                        <th class="px-4 py-3 text-right font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40">Net final</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-bimo-navy/[5%]">
                    @foreach($compte['paiements'] as $p)
                    <tr class="hover:bg-bimo-bg transition-colors duration-100">
                        <td class="px-4 py-3 font-body text-xs text-bimo-navy/60">{{ $p->periode?->format('m/Y') ?? '—' }}</td>
                        <td class="px-4 py-3 font-body text-xs text-bimo-navy/70">{{ $p->contrat?->bien?->titre_fallback ?? '—' }}</td>
                        <td class="px-4 py-3 text-right font-display font-semibold text-xs text-bimo-navy">{{ number_format($p->montant_encaisse, 0, ',', ' ') }} F</td>
                        <td class="px-4 py-3 text-right font-body text-xs text-bimo-navy/50">{{ number_format($p->commission_ttc, 0, ',', ' ') }} F</td>
                        <td class="px-4 py-3 text-right font-body text-xs text-bimo-navy/50">{{ number_format($p->total_depenses, 0, ',', ' ') }} F</td>
                        <td class="px-4 py-3 text-right font-display font-bold text-xs text-bimo-gold">{{ number_format($p->net_final_bailleur, 0, ',', ' ') }} F</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>
@endsection
