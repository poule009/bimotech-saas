@extends('layouts.app')
@section('header', 'Mes paiements')

@section('content')

@php
$modes = ['especes'=>'Espèces','virement'=>'Virement','cheque'=>'Chèque','wave'=>'Wave','orange_money'=>'Orange Money','free_money'=>'Free Money','e_money'=>'E-Money'];
@endphp

<div class="space-y-4">

    {{-- En-tête --}}
    <div class="flex flex-col gap-1">
        <nav class="flex items-center gap-2 font-body text-sm text-bimo-text/40">
            <a href="{{ route('locataire.dashboard') }}" class="hover:text-bimo-text transition-colors duration-150">Mon espace</a>
            <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            <span class="text-bimo-text font-medium">Mes paiements</span>
        </nav>
        <h1 class="font-display font-extrabold text-xl md:text-2xl text-bimo-text tracking-tight">Historique des paiements</h1>
        <p class="font-body text-sm text-bimo-text/50">Tous vos loyers validés, avec téléchargement de quittance.</p>
    </div>

    {{-- Card liste --}}
    <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2 gap-3 flex-wrap">
            <span class="font-display font-bold text-sm text-bimo-text">Paiements validés</span>
            <div class="flex items-center gap-3">
                @if($anneesDisponibles->count() > 1)
                <form method="GET" action="{{ route('locataire.paiements') }}" class="flex items-center gap-2">
                    <label class="font-body text-xs text-bimo-text/40 whitespace-nowrap">Année :</label>
                    <select name="annee" onchange="this.form.submit()"
                            class="px-3 py-1.5 border border-bimo-navy/15 rounded-[7px] font-body text-xs text-bimo-text bg-white focus:outline-none focus:border-bimo-gold cursor-pointer">
                        <option value="">Toutes</option>
                        @foreach($anneesDisponibles as $a)
                        <option value="{{ $a }}" {{ $annee == $a ? 'selected' : '' }}>{{ $a }}</option>
                        @endforeach
                    </select>
                </form>
                @endif
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-body font-medium bg-bimo-navy/10 border border-bimo-navy/15 text-bimo-text/70">
                    {{ $paiements->total() }} au total
                </span>
            </div>
        </div>

        @if($paiements->isEmpty())
        <div class="px-5 py-16 text-center">
            <div class="w-12 h-12 bg-bimo-navy/[5%] rounded-[12px] flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6 text-bimo-text/30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
            </div>
            <div class="font-display font-bold text-sm text-bimo-text mb-2">Aucun paiement enregistré</div>
            <p class="font-body text-sm text-bimo-text/40">Vos loyers validés apparaîtront ici avec les quittances téléchargeables.</p>
        </div>
        @else

        {{-- Mobile cards --}}
        <div class="md:hidden divide-y divide-bimo-navy/[5%]">
            @foreach($paiements as $p)
            <div class="px-4 py-3.5 flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-display font-semibold bg-bimo-gold/10 border border-bimo-gold/20 text-bimo-gold">
                        {{ \Carbon\Carbon::parse($p->periode)->translatedFormat('F Y') }}
                    </span>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="font-body text-xs text-bimo-text/40">{{ \Carbon\Carbon::parse($p->date_paiement)->format('d/m/Y') }}</span>
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-body font-medium bg-bimo-navy/[5%] text-bimo-text/50">{{ $modes[$p->mode_paiement] ?? $p->mode_paiement }}</span>
                    </div>
                </div>
                <div class="flex items-center gap-2 flex-shrink-0">
                    <span class="font-display font-bold text-sm text-bimo-gold">{{ number_format($p->montant_encaisse,0,',','') }}&nbsp;F</span>
                    <a href="{{ route('locataire.paiements.pdf', $p) }}" target="_blank"
                       class="w-8 h-8 flex items-center justify-center border border-bimo-navy/10 rounded-[7px] text-bimo-text/40 hover:text-bimo-gold hover:border-bimo-gold/30 transition-all duration-150"
                       title="Télécharger la quittance PDF">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><polyline points="9 15 12 18 15 15"/></svg>
                    </a>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Desktop table --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-bimo-navy/[5%] bg-bimo-bg">
                        <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Référence</th>
                        <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Période</th>
                        <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Date de paiement</th>
                        <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Mode</th>
                        <th class="px-5 py-3 text-right font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Montant encaissé</th>
                        <th class="px-5 py-3 text-center font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Quittance</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-bimo-navy/[5%]">
                    @foreach($paiements as $p)
                    <tr class="hover:bg-bimo-bg transition-colors duration-100">
                        <td class="px-5 py-3.5 font-body text-[11px] text-bimo-text/40 uppercase tracking-widest">{{ $p->reference_paiement ?? '—' }}</td>
                        <td class="px-5 py-3.5">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-display font-semibold bg-bimo-gold/10 border border-bimo-gold/20 text-bimo-gold">
                                {{ \Carbon\Carbon::parse($p->periode)->translatedFormat('F Y') }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 font-body text-xs text-bimo-text/60">{{ \Carbon\Carbon::parse($p->date_paiement)->isoFormat('D MMM YYYY') }}</td>
                        <td class="px-5 py-3.5">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-body font-medium bg-bimo-navy/10 border border-bimo-navy/15 text-bimo-text/70">{{ $modes[$p->mode_paiement] ?? $p->mode_paiement }}</span>
                        </td>
                        <td class="px-5 py-3.5 text-right font-display font-bold text-sm text-bimo-gold">{{ number_format($p->montant_encaisse, 0, ',', ' ') }} FCFA</td>
                        <td class="px-5 py-3.5 text-center">
                            <a href="{{ route('locataire.paiements.pdf', $p) }}" target="_blank"
                               class="w-9 h-9 inline-flex items-center justify-center border border-bimo-navy/10 rounded-[6px] text-bimo-text/40 hover:text-bimo-gold hover:border-bimo-gold/30 transition-all duration-150"
                               title="Télécharger la quittance PDF">
                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><polyline points="9 15 12 18 15 15"/></svg>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($paiements->hasPages())
        <div class="flex items-center justify-between px-5 py-3.5 border-t border-bimo-navy/[5%] flex-wrap gap-3">
            <span class="font-body text-xs text-bimo-text/40">Page {{ $paiements->currentPage() }} / {{ $paiements->lastPage() }}</span>
            <div class="flex items-center gap-1">
                <a href="{{ $paiements->previousPageUrl() ?? '#' }}"
                   class="w-8 h-8 inline-flex items-center justify-center border border-bimo-navy/15 rounded-[7px] font-body text-xs text-bimo-text/50 hover:border-bimo-gold hover:text-bimo-gold transition-all duration-150 {{ $paiements->onFirstPage() ? 'opacity-40 pointer-events-none' : '' }}">
                    <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
                </a>
                @foreach($paiements->getUrlRange(max(1,$paiements->currentPage()-2), min($paiements->lastPage(),$paiements->currentPage()+2)) as $page => $url)
                <a href="{{ $url }}"
                   class="w-8 h-8 inline-flex items-center justify-center border rounded-[7px] font-body text-xs transition-all duration-150 {{ $page == $paiements->currentPage() ? 'bg-[var(--ac)] border-[var(--ac)] text-white font-bold' : 'border-bimo-navy/15 text-bimo-text/50 hover:border-bimo-gold hover:text-bimo-gold' }}">
                    {{ $page }}
                </a>
                @endforeach
                <a href="{{ $paiements->nextPageUrl() ?? '#' }}"
                   class="w-8 h-8 inline-flex items-center justify-center border border-bimo-navy/15 rounded-[7px] font-body text-xs text-bimo-text/50 hover:border-bimo-gold hover:text-bimo-gold transition-all duration-150 {{ !$paiements->hasMorePages() ? 'opacity-40 pointer-events-none' : '' }}">
                    <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                </a>
            </div>
        </div>
        @endif

        @endif
    </div>

</div>
@endsection
