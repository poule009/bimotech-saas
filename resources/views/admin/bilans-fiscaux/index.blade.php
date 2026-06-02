@extends('layouts.app')
@section('header', 'Bilans fiscaux')

@section('content')

@php
$totalBrut   = $bilans->sum('revenus_bruts_total');
$totalIrpp   = $bilans->sum('irpp_estime');
$totalTvaCol = $bilans->sum('tva_loyer_collectee');
$totalBrs    = $bilans->sum('brs_retenu_total');
@endphp

<div class="space-y-4 md:space-y-5">

    {{-- En-tête --}}
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
        <div>
            <h1 class="font-display font-extrabold text-xl md:text-2xl text-bimo-navy tracking-tight">Bilans fiscaux propriétaires</h1>
            <p class="font-body text-sm text-bimo-navy/50 mt-1">Abattement 30% · IRPP · CFPB · TVA collectée · BRS — Année {{ $annee }}</p>
        </div>
        <div class="flex-shrink-0 bg-bimo-gold/[6%] border border-bimo-gold/20 rounded-[10px] px-4 py-3 text-right">
            <div class="font-body font-medium text-xs text-bimo-gold/70">⚠ Estimation fiscale</div>
            <div class="font-body text-xs text-bimo-navy/50 mt-0.5">À vérifier avec un comptable / DGI</div>
        </div>
    </div>

    {{-- Filtre année --}}
    <form method="GET" class="flex items-center gap-3 bg-white rounded-[12px] border border-bimo-navy/10 px-5 py-3.5">
        <span class="font-body font-medium text-xs text-bimo-navy/50 whitespace-nowrap">Année fiscale :</span>
        <select name="annee"
                class="px-3 py-2 border border-bimo-navy/15 rounded-[8px] font-body text-sm text-bimo-navy bg-bimo-bg focus:outline-none focus:border-bimo-gold cursor-pointer transition-all duration-150">
            @foreach($anneesDisponibles as $a)
            <option value="{{ $a }}" {{ $annee == $a ? 'selected':'' }}>{{ $a }}</option>
            @endforeach
        </select>
        <button type="submit"
                class="inline-flex items-center px-4 py-2 bg-bimo-navy text-white font-display font-bold text-sm rounded-[9px] hover:bg-bimo-navy-dk transition-colors duration-150">
            Afficher
        </button>
    </form>

    {{-- KPIs --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <div class="bg-bimo-gold/[8%] rounded-[14px] border border-bimo-gold/25 p-4">
            <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-gold/70 mb-1.5">Revenus bruts totaux {{ $annee }}</div>
            <div class="font-display font-extrabold text-xl text-bimo-gold leading-none">{{ number_format($totalBrut,0,',','') }}<span class="font-body text-xs text-bimo-gold/60 ml-1">F</span></div>
            <div class="font-body text-[10.5px] text-bimo-gold/60 mt-1.5">{{ $proprietaires->count() }} propriétaire(s)</div>
        </div>
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-4">
            <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-navy/50 mb-1.5">TVA loyer collectée</div>
            <div class="font-display font-extrabold text-xl text-bimo-gold leading-none">{{ number_format($totalTvaCol,0,',','') }}<span class="font-body text-xs text-bimo-navy/40 ml-1">F</span></div>
            <div class="font-body text-[10.5px] text-bimo-navy/40 mt-1.5">À reverser à la DGI</div>
        </div>
        <div class="bg-white rounded-[14px] border {{ $totalBrs > 0 ? 'border-bimo-red/20' : 'border-bimo-navy/10' }} p-4">
            <div class="font-body font-medium text-[9.5px] uppercase tracking-widest {{ $totalBrs > 0 ? 'text-bimo-red/70' : 'text-bimo-navy/50' }} mb-1.5">BRS total retenu</div>
            <div class="font-display font-extrabold text-xl {{ $totalBrs > 0 ? 'text-bimo-red' : 'text-bimo-navy' }} leading-none">{{ number_format($totalBrs,0,',','') }}<span class="font-body text-xs text-bimo-navy/40 ml-1">F</span></div>
            <div class="font-body text-[10.5px] text-bimo-navy/40 mt-1.5">Par les locataires entreprises</div>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
            <span class="font-display font-bold text-sm text-bimo-navy">Propriétaires — Bilan {{ $annee }}</span>
            <span class="font-body text-xs text-bimo-navy/40">{{ $proprietaires->count() }} propriétaire(s)</span>
        </div>
        {{-- Mobile --}}
        <div class="md:hidden divide-y divide-bimo-navy/[5%]">
            @foreach($proprietaires as $proprio)
            @php $bilan = $bilans->get($proprio->id); @endphp
            <div class="px-4 py-3.5">
                <div class="flex items-center justify-between gap-2 mb-1">
                    <div>
                        <div class="font-body font-semibold text-sm text-bimo-navy">{{ $proprio->name }}</div>
                        <div class="font-body text-xs text-bimo-navy/40">{{ $proprio->email }}</div>
                    </div>
                    @if($bilan)
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-body font-semibold bg-bimo-navy/10 border border-bimo-navy/15 text-bimo-navy/70">Calculé</span>
                    @else
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-body font-semibold bg-bimo-gold/10 border border-bimo-gold/20 text-bimo-gold">Non calculé</span>
                    @endif
                </div>
                @if($bilan)
                <div class="flex items-center gap-3 mt-1.5">
                    <span class="font-body text-xs text-bimo-navy/50">Brut : <strong class="text-bimo-gold">{{ number_format($bilan->revenus_bruts_loyers,0,',','') }} F</strong></span>
                    <span class="font-body text-xs text-bimo-red/70">IRPP : {{ number_format($bilan->irpp_estime,0,',','') }} F</span>
                </div>
                @endif
                <div class="flex items-center gap-2 mt-2">
                    <form method="POST" action="{{ route('admin.bilans-fiscaux.calculate', $proprio) }}">
                        @csrf
                        <input type="hidden" name="annee" value="{{ $annee }}">
                        <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 border border-bimo-navy/15 rounded-[7px] font-body text-xs text-bimo-navy/60 hover:border-bimo-gold hover:text-bimo-gold transition-all duration-150 cursor-pointer">
                            <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 11-2.12-9.36L23 10"/></svg>
                            {{ $bilan ? 'Recalc.' : 'Calculer' }}
                        </button>
                    </form>
                    @if($bilan)
                    <a href="{{ route('admin.bilans-fiscaux.show', [$proprio, 'annee' => $annee]) }}" class="inline-flex items-center gap-1 px-3 py-1.5 border border-bimo-navy/15 rounded-[7px] font-body text-xs text-bimo-navy/60 hover:border-bimo-gold hover:text-bimo-gold transition-all duration-150">
                        Voir
                    </a>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        {{-- Desktop --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-bimo-navy/[5%] bg-bimo-bg">
                        <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40">Propriétaire</th>
                        <th class="px-5 py-3 text-right font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40">Revenus bruts</th>
                        <th class="px-5 py-3 text-right font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40">Base imposable (70%)</th>
                        <th class="px-5 py-3 text-right font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40">IRPP estimé</th>
                        <th class="px-5 py-3 text-center font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40">Régime conseillé</th>
                        <th class="px-5 py-3 text-right font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40">TVA collectée</th>
                        <th class="px-5 py-3 text-right font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40">BRS retenu</th>
                        <th class="px-5 py-3 text-center font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40">Statut</th>
                        <th class="px-5 py-3 text-center font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-bimo-navy/[5%]">
                    @foreach($proprietaires as $proprio)
                    @php $bilan = $bilans->get($proprio->id); @endphp
                    <tr class="hover:bg-bimo-bg transition-colors duration-100">
                        <td class="px-5 py-3.5">
                            <div class="font-body font-semibold text-sm text-bimo-navy">{{ $proprio->name }}</div>
                            <div class="font-body text-xs text-bimo-navy/40">{{ $proprio->email }}</div>
                        </td>
                        <td class="px-5 py-3.5 text-right font-display font-semibold text-sm text-bimo-navy/70">
                            @if($bilan) {{ number_format($bilan->revenus_bruts_loyers,0,',','') }} F @else <span class="text-bimo-navy/20">—</span> @endif
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            @if($bilan)
                            <div class="font-display font-semibold text-sm text-bimo-navy/70">{{ number_format($bilan->base_imposable,0,',','') }} F</div>
                            <div class="font-body text-[10px] text-bimo-navy/30">après abattement 30%</div>
                            @else <span class="text-bimo-navy/20">—</span> @endif
                        </td>
                        <td class="px-5 py-3.5 text-right font-display font-bold text-sm text-bimo-red">
                            @if($bilan) {{ number_format($bilan->irpp_estime,0,',','') }} F @else <span class="text-bimo-navy/20">—</span> @endif
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            @if($bilan)
                            @php
                                $rc = \App\Services\FiscalService::comparerRegimes((float)$bilan->revenus_bruts_total,(float)$bilan->irpp_estime)['regime_recommande'];
                            @endphp
                            @if($rc === 'cgf')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-body font-semibold bg-bimo-navy/10 border border-bimo-navy/15 text-bimo-navy/70">CGF</span>
                            <div class="font-body text-[10px] text-bimo-navy/30 mt-0.5">Art. 80 CGI SN</div>
                            @elseif($rc === 'irpp')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-body font-semibold bg-bimo-gold/10 border border-bimo-gold/20 text-bimo-gold">IRPP</span>
                            <div class="font-body text-[10px] text-bimo-navy/30 mt-0.5">Art. 65 CGI SN</div>
                            @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-body font-medium bg-bimo-navy/[5%] text-bimo-navy/40">N/A &gt;30M</span>
                            @endif
                            @else <span class="text-bimo-navy/20">—</span> @endif
                        </td>
                        <td class="px-5 py-3.5 text-right font-display font-semibold text-sm text-bimo-gold">
                            @if($bilan && $bilan->tva_loyer_collectee > 0) {{ number_format($bilan->tva_loyer_collectee,0,',','') }} F
                            @elseif($bilan) <span class="text-bimo-navy/20">—</span>
                            @else <span class="text-bimo-navy/20">—</span> @endif
                        </td>
                        <td class="px-5 py-3.5 text-right font-display font-bold text-sm text-bimo-red">
                            @if($bilan && $bilan->brs_retenu_total > 0) {{ number_format($bilan->brs_retenu_total,0,',','') }} F
                            @elseif($bilan) <span class="text-bimo-navy/20">—</span>
                            @else <span class="text-bimo-navy/20">—</span> @endif
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            @if($bilan)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-body font-semibold bg-bimo-navy/10 border border-bimo-navy/15 text-bimo-navy/70">Calculé</span>
                            <div class="font-body text-[10px] text-bimo-navy/30 mt-0.5">{{ $bilan->calcule_le->format('d/m/Y') }}</div>
                            @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-body font-semibold bg-bimo-gold/10 border border-bimo-gold/20 text-bimo-gold">Non calculé</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <form method="POST" action="{{ route('admin.bilans-fiscaux.calculate', $proprio) }}">
                                    @csrf
                                    <input type="hidden" name="annee" value="{{ $annee }}">
                                    <button type="submit" title="{{ $bilan ? 'Recalculer' : 'Calculer' }}"
                                            class="w-7 h-7 inline-flex items-center justify-center border border-bimo-navy/15 rounded-[6px] text-bimo-navy/40 hover:border-bimo-gold hover:text-bimo-gold transition-all duration-150 cursor-pointer">
                                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 11-2.12-9.36L23 10"/></svg>
                                    </button>
                                </form>
                                @if($bilan)
                                <a href="{{ route('admin.bilans-fiscaux.show', [$proprio, 'annee' => $annee]) }}"
                                   class="w-7 h-7 inline-flex items-center justify-center border border-bimo-navy/15 rounded-[6px] text-bimo-navy/40 hover:border-bimo-gold hover:text-bimo-gold transition-all duration-150">
                                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                </a>
                                <a href="{{ route('admin.bilans-fiscaux.pdf', [$proprio, 'annee' => $annee]) }}" target="_blank"
                                   class="w-7 h-7 inline-flex items-center justify-center border border-bimo-navy/15 rounded-[6px] text-bimo-navy/40 hover:border-bimo-gold hover:text-bimo-gold transition-all duration-150">
                                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="flex items-start gap-2 bg-bimo-gold/[6%] border border-bimo-gold/20 rounded-[10px] px-4 py-3">
        <svg class="w-4 h-4 text-bimo-gold flex-shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
        <p class="font-body text-xs text-bimo-gold/80 leading-relaxed">
            <strong>Note :</strong> Ces bilans sont des <strong>estimations</strong> basées sur les paiements enregistrés.
            L'IRPP est calculé selon le barème progressif sénégalais (Art. 65 CGI SN) après abattement forfaitaire 30% (Art. 58 CGI SN).
            La CFPB est une estimation indicative — assiette réelle = valeur locative cadastrale fixée par la DGID (Art. 290-291 CGI SN).
            <strong>Consultez un comptable ou la DGI avant toute déclaration.</strong>
        </p>
    </div>

</div>
@endsection
