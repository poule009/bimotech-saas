@extends('layouts.app')
@section('header', 'Bilan mensuel')

@section('content')

@php
    $prevMois  = $mois == 1  ? 12 : $mois - 1;
    $prevAnnee = $mois == 1  ? $annee - 1 : $annee;
    $nextMois  = $mois == 12 ? 1  : $mois + 1;
    $nextAnnee = $mois == 12 ? $annee + 1 : $annee;
    $isFutur   = $nextAnnee > now()->year || ($nextAnnee == now()->year && $nextMois > now()->month);
    $maxLoyer  = $evolution->max('total_loyers') ?: 1;
    $modeLabels = ['especes'=>'Espèces','virement'=>'Virement','cheque'=>'Chèque','wave'=>'Wave','orange_money'=>'Orange Money','free_money'=>'Free Money','e_money'=>'E-Money'];
@endphp

<div class="space-y-4 md:space-y-5">

    {{-- En-tête --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <h1 class="font-display font-extrabold text-xl md:text-2xl text-bimo-text tracking-tight leading-tight">Rapport financier</h1>
            <p class="font-body text-sm text-bimo-text/50 mt-1">
                {{ $debutMois->translatedFormat('F Y') }}
                · {{ $kpiMois['nb_paiements'] }} paiement(s) validé(s)
                @if($biensImpayes->count() > 0)
                · <span class="text-bimo-red font-semibold">{{ $biensImpayes->count() }} impayé(s)</span>
                @endif
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            {{-- Navigation mois --}}
            <div class="flex items-center gap-1">
                <a href="{{ route('admin.rapports.financier', ['mois' => $prevMois, 'annee' => $prevAnnee]) }}"
                   class="w-8 h-8 flex items-center justify-center border border-bimo-navy/15 rounded-[7px] text-bimo-text/50 hover:text-bimo-text hover:border-bimo-gold transition-all duration-150">
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                </a>
                <div class="font-display font-semibold text-sm text-bimo-text min-w-[140px] text-center px-2">{{ $debutMois->translatedFormat('F Y') }}</div>
                @if(!$isFutur)
                <a href="{{ route('admin.rapports.financier', ['mois' => $nextMois, 'annee' => $nextAnnee]) }}"
                   class="w-8 h-8 flex items-center justify-center border border-bimo-navy/15 rounded-[7px] text-bimo-text/50 hover:text-bimo-text hover:border-bimo-gold transition-all duration-150">
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                </a>
                @else
                <span class="w-8 h-8 flex items-center justify-center border border-bimo-navy/10 rounded-[7px] text-bimo-text/20 cursor-not-allowed">
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                </span>
                @endif
            </div>

            {{-- Sélecteur année --}}
            @if($anneesDisponibles->count() > 1)
            <form method="GET" class="flex items-center">
                <input type="hidden" name="mois" value="{{ $mois }}">
                <select name="annee" onchange="this.form.submit()"
                        class="px-3 py-2 bg-white border border-bimo-navy/15 rounded-[9px] font-body text-sm text-bimo-text cursor-pointer
                               focus:outline-none focus:border-bimo-gold transition-all duration-150">
                    @foreach($anneesDisponibles as $a)
                    <option value="{{ $a }}" {{ $a == $annee ? 'selected' : '' }}>{{ $a }}</option>
                    @endforeach
                </select>
            </form>
            @endif

            {{-- Export PDF --}}
            <a href="{{ route('admin.rapports.financier.export-pdf', ['mois' => $mois, 'annee' => $annee]) }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-bimo-navy text-white font-display font-bold text-sm rounded-[10px] hover:bg-bimo-navy-dk transition-colors duration-150">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                Exporter PDF
            </a>
        </div>
    </div>

    {{-- Graphique 6 mois --}}
    @if($evolution->count() > 0)
    <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-5">
        <div class="flex items-center justify-between flex-wrap gap-3 mb-4">
            <div>
                <div class="font-display font-bold text-sm text-bimo-text">Évolution sur 6 mois</div>
                <div class="font-body text-xs text-bimo-text/40 mt-0.5">Loyers encaissés et commissions</div>
            </div>
            <div class="flex items-center gap-4 font-body text-xs text-bimo-text/50">
                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-[3px] bg-bimo-gold inline-block"></span>Loyers</span>
                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-[3px] bg-bimo-navy/15 inline-block"></span>Commission</span>
            </div>
        </div>
        <div class="flex items-end gap-2 h-28">
            @foreach($evolution as $e)
            @php
                $hLoyer = max(4, round(($e->total_loyers / $maxLoyer) * 112));
                $hComm  = max(2, round(($e->total_commission / $maxLoyer) * 112));
                $label  = \Carbon\Carbon::createFromFormat('Y-m', $e->mois_label)->translatedFormat('M');
                $isCur  = $e->mois_label === $debutMois->format('Y-m');
            @endphp
            <div class="flex-1 flex flex-col items-center gap-1" title="{{ number_format($e->total_loyers, 0, ',', ' ') }} F">
                <div class="w-full flex items-end gap-0.5" style="height:112px">
                    <div class="flex-1 rounded-t-[3px] min-h-[2px] transition-opacity duration-150 hover:opacity-80"
                         style="height:{{ $hLoyer }}px; background: {{ $isCur ? '#1B4F6B' : '#C9A84C' }}"></div>
                    <div class="flex-1 rounded-t-[3px] min-h-[2px] transition-opacity duration-150 hover:opacity-80"
                         style="height:{{ $hComm }}px; background: {{ $isCur ? '#C9A84C' : 'rgba(27,79,107,0.15)' }}"></div>
                </div>
                <div class="font-body text-[9px] text-center {{ $isCur ? 'text-bimo-text font-bold' : 'text-bimo-text/40' }}">{{ $label }}</div>
            </div>
            @endforeach
        </div>
        <div class="font-body text-[10px] text-bimo-text/30 text-right mt-1">Max : {{ number_format($maxLoyer, 0, ',', ' ') }} F</div>
    </div>
    @endif

    {{-- KPIs --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3">
        @php
            $kpis = [
                ['Loyers encaissés',  number_format($kpiMois['total_loyers'], 0, ',', ' '), 'FCFA · '.$kpiMois['nb_paiements'].' paiements', 'border-t-bimo-gold', 'text-bimo-gold'],
                ['Net propriétaires', number_format($kpiMois['total_net_proprio'], 0, ',', ' '), 'FCFA à reverser', 'border-t-bimo-navy', 'text-bimo-text'],
                ['Commission HT',     number_format($kpiMois['total_commission'], 0, ',', ' '), 'FCFA agence', 'border-t-bimo-navy', 'text-bimo-text'],
                ['TVA commission',    number_format($kpiMois['total_tva'], 0, ',', ' '), 'FCFA (18%)', 'border-t-bimo-navy', 'text-bimo-text'],
                ['Commission TTC',    number_format($kpiMois['total_ttc'], 0, ',', ' '), 'FCFA total agence', 'border-t-bimo-navy-dk', 'text-bimo-text'],
            ];
        @endphp
        @foreach($kpis as [$lbl, $val, $sub, $topClass, $valClass])
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-4 border-t-2 {{ $topClass }}">
            <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-text/50 mb-1">{{ $lbl }}</div>
            <div class="font-display font-extrabold text-xl {{ $valClass }} leading-none">{{ $val }}</div>
            <div class="font-body text-[10.5px] text-bimo-text/40 mt-1.5">{{ $sub }}</div>
        </div>
        @endforeach
    </div>

    {{-- Stats générales --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
        @foreach([
            ['Biens total',    $statsGenerales['nb_biens']],
            ['Biens loués',    $statsGenerales['nb_biens_loues']],
            ['Contrats actifs',$statsGenerales['nb_contrats']],
            ['Propriétaires',  $statsGenerales['nb_proprietaires']],
            ['Locataires',     $statsGenerales['nb_locataires']],
        ] as [$lbl, $val])
        <div class="bg-bimo-bg rounded-[10px] border border-bimo-navy/10 p-3 text-center">
            <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-text/40 mb-1">{{ $lbl }}</div>
            <div class="font-display font-bold text-lg text-bimo-text leading-none">{{ $val }}</div>
        </div>
        @endforeach
        {{-- Taux d'occupation --}}
        @php $taux = $statsGenerales['taux_occupation']; @endphp
        <div class="bg-bimo-bg rounded-[10px] border border-bimo-navy/10 p-3">
            <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-text/40 mb-1">Taux d'occupation</div>
            <div class="font-display font-bold text-lg leading-none {{ $taux >= 80 ? 'text-bimo-gold' : ($taux >= 50 ? 'text-amber-500' : 'text-bimo-red') }}">{{ $taux }}%</div>
            <div class="h-1.5 bg-bimo-navy/10 rounded-full overflow-hidden mt-2">
                <div class="h-full rounded-full" style="width:{{ $taux }}%; background: {{ $taux >= 80 ? '#C9A84C' : ($taux >= 50 ? '#f59e0b' : '#EF4444') }}"></div>
            </div>
        </div>
    </div>

    {{-- Par propriétaire --}}
    @if($parProprietaire->count() > 0)
    <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
            <span class="font-display font-bold text-sm text-bimo-text">Récapitulatif par propriétaire</span>
            <span class="font-body text-xs text-bimo-text/40">{{ $parProprietaire->count() }} propriétaire(s)</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-bimo-navy/[5%] bg-bimo-bg">
                        <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Propriétaire</th>
                        <th class="px-5 py-3 text-center font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Paiements</th>
                        <th class="px-5 py-3 text-right font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Total encaissé</th>
                        <th class="px-5 py-3 text-right font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Commission TTC</th>
                        <th class="px-5 py-3 text-right font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Net reversé</th>
                        <th class="px-5 py-3 text-right font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Part</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-bimo-navy/[5%]">
                    @foreach($parProprietaire as $nom => $data)
                    @php $part = $kpiMois['total_loyers'] > 0 ? round(($data['total_encaisse'] / $kpiMois['total_loyers']) * 100, 1) : 0; @endphp
                    <tr class="hover:bg-bimo-bg transition-colors duration-100">
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-2.5">
                                <div class="w-7 h-7 rounded-[7px] bg-bimo-gold/15 flex items-center justify-center font-display font-bold text-[10px] text-bimo-gold flex-shrink-0">
                                    {{ mb_strtoupper(mb_substr($nom, 0, 2)) }}
                                </div>
                                <span class="font-body font-medium text-sm text-bimo-text">{{ $nom }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full bg-bimo-navy/10 border border-bimo-navy/15 font-body text-[11px] text-bimo-text/60">{{ $data['nb_paiements'] }}</span>
                        </td>
                        <td class="px-5 py-3.5 text-right font-display font-bold text-sm text-bimo-gold">{{ number_format($data['total_encaisse'], 0, ',', ' ') }} F</td>
                        <td class="px-5 py-3.5 text-right font-body text-xs text-bimo-text/60">{{ number_format($data['total_commission'], 0, ',', ' ') }} F</td>
                        <td class="px-5 py-3.5 text-right font-body font-semibold text-sm text-bimo-text">{{ number_format($data['total_net'], 0, ',', ' ') }} F</td>
                        <td class="px-5 py-3.5 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <span class="font-body text-[11px] text-bimo-text/40">{{ $part }}%</span>
                                <div class="w-10 h-1 bg-bimo-navy/10 rounded-full overflow-hidden">
                                    <div class="h-full bg-bimo-gold rounded-full" style="width:{{ $part }}%"></div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-bimo-bg2 font-semibold border-t border-bimo-navy/10">
                        <td class="px-5 py-3.5 font-body font-semibold text-sm text-bimo-text">Total</td>
                        <td class="px-5 py-3.5 text-center font-body text-sm text-bimo-text">{{ $kpiMois['nb_paiements'] }}</td>
                        <td class="px-5 py-3.5 text-right font-display font-bold text-sm text-bimo-gold">{{ number_format($kpiMois['total_loyers'], 0, ',', ' ') }} F</td>
                        <td class="px-5 py-3.5 text-right font-body text-xs text-bimo-text/60">{{ number_format($kpiMois['total_ttc'], 0, ',', ' ') }} F</td>
                        <td class="px-5 py-3.5 text-right font-body font-semibold text-sm text-bimo-text">{{ number_format($kpiMois['total_net_proprio'], 0, ',', ' ') }} F</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    @endif

    {{-- Détail paiements --}}
    <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
            <span class="font-display font-bold text-sm text-bimo-text">Détail des paiements</span>
            <span class="font-body text-xs text-bimo-text/40">{{ $paiementsMois->total() }} paiement(s)</span>
        </div>

        @if($paiementsMois->isEmpty())
        <div class="px-5 py-16 text-center">
            <div class="w-12 h-12 bg-bimo-navy/5 rounded-[12px] flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6 text-bimo-text/30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
            </div>
            <div class="font-display font-bold text-base text-bimo-text mb-2">Aucun paiement ce mois</div>
            <p class="font-body text-sm text-bimo-text/50 mb-5">Aucun paiement validé pour {{ $debutMois->translatedFormat('F Y') }}.</p>
            <a href="{{ route('admin.paiements.create') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-[var(--ac)] text-white font-display font-bold text-sm rounded-[10px] hover:opacity-90 transition-opacity duration-150">
                + Enregistrer un paiement
            </a>
        </div>
        @else

        {{-- Mobile --}}
        <div class="md:hidden divide-y divide-bimo-navy/[5%]">
            @foreach($paiementsMois as $p)
            <div class="px-5 py-3.5 flex items-center justify-between gap-3">
                <div>
                    <div class="font-body font-medium text-sm text-bimo-text">{{ $p->contrat?->bien?->reference ?? '—' }}</div>
                    <div class="font-body text-xs text-bimo-text/50">{{ $p->contrat?->locataire?->name ?? '—' }}</div>
                </div>
                <div class="text-right">
                    <div class="font-display font-bold text-sm text-bimo-gold">{{ number_format($p->montant_encaisse ?? 0, 0, ',', ' ') }} F</div>
                    <div class="font-body text-xs text-bimo-text/40">{{ $modeLabels[$p->mode_paiement] ?? $p->mode_paiement }}</div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Desktop --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-bimo-navy/[5%] bg-bimo-bg">
                        <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Référence</th>
                        <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Bien</th>
                        <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Locataire</th>
                        <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Propriétaire</th>
                        <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Mode</th>
                        <th class="px-5 py-3 text-right font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Loyer nu</th>
                        <th class="px-5 py-3 text-right font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Comm. TTC</th>
                        <th class="px-5 py-3 text-right font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Net proprio</th>
                        <th class="px-5 py-3 text-center font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">PDF</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-bimo-navy/[5%]">
                    @foreach($paiementsMois as $p)
                    @php $proprio = $p->contrat?->bien?->proprietaire; @endphp
                    <tr class="hover:bg-bimo-bg transition-colors duration-100">
                        <td class="px-5 py-3.5 font-body text-[11px] text-bimo-text/40 uppercase tracking-widest whitespace-nowrap">{{ $p->reference_paiement }}</td>
                        <td class="px-5 py-3.5">
                            <div class="font-body font-medium text-sm text-bimo-text">{{ $p->contrat?->bien?->reference ?? '—' }}</div>
                            <div class="font-body text-xs text-bimo-text/40">{{ $p->contrat?->bien?->ville }}</div>
                        </td>
                        <td class="px-5 py-3.5 font-body text-xs text-bimo-text/70">{{ $p->contrat?->locataire?->name ?? '—' }}</td>
                        <td class="px-5 py-3.5">
                            @if($proprio)
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-[5px] bg-bimo-gold/15 flex items-center justify-center font-body font-bold text-[9px] text-bimo-gold flex-shrink-0">{{ mb_strtoupper(mb_substr($proprio->name,0,2)) }}</div>
                                <span class="font-body text-xs text-bimo-text/60">{{ $proprio->name }}</span>
                            </div>
                            @else
                            <span class="font-body text-xs text-bimo-text/30">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5">
                            @php
                                $modeClasses = ['especes'=>'bg-bimo-navy/10 text-bimo-text/60','virement'=>'bg-bimo-navy/10 text-bimo-text/60','cheque'=>'bg-bimo-navy/10 text-bimo-text/60','wave'=>'bg-amber-50 text-amber-700','orange_money'=>'bg-orange-50 text-orange-700','free_money'=>'bg-pink-50 text-pink-700','e_money'=>'bg-bimo-gold/10 text-bimo-gold'];
                            @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-body font-bold {{ $modeClasses[$p->mode_paiement] ?? 'bg-bimo-navy/10 text-bimo-text/60' }}">
                                {{ $modeLabels[$p->mode_paiement] ?? $p->mode_paiement }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-right font-body font-semibold text-sm text-bimo-text whitespace-nowrap">{{ number_format($p->loyer_nu ?? 0, 0, ',', ' ') }} F</td>
                        <td class="px-5 py-3.5 text-right font-body text-xs text-bimo-text/50 whitespace-nowrap">{{ number_format($p->commission_ttc ?? 0, 0, ',', ' ') }} F</td>
                        <td class="px-5 py-3.5 text-right font-body font-semibold text-sm text-bimo-text whitespace-nowrap">{{ number_format($p->net_proprietaire ?? 0, 0, ',', ' ') }} F</td>
                        <td class="px-5 py-3.5 text-center">
                            <a href="{{ route('admin.paiements.pdf', $p) }}" target="_blank"
                               class="w-7 h-7 inline-flex items-center justify-center border border-bimo-navy/10 rounded-[6px] text-bimo-text/40 hover:text-bimo-gold hover:border-bimo-gold/30 transition-all duration-150">
                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($paiementsMois->hasPages())
        <div class="flex items-center justify-between px-5 py-3.5 border-t border-bimo-navy/[5%] bg-bimo-bg">
            <span class="font-body text-xs text-bimo-text/40">{{ $paiementsMois->firstItem() }}–{{ $paiementsMois->lastItem() }} sur {{ $paiementsMois->total() }}</span>
            <div class="flex items-center gap-1">
                @if($paiementsMois->onFirstPage())
                <span class="w-7 h-7 flex items-center justify-center border border-bimo-navy/10 rounded-[6px] text-bimo-text/20 cursor-not-allowed"><svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg></span>
                @else
                <a href="{{ $paiementsMois->previousPageUrl() }}" class="w-7 h-7 flex items-center justify-center border border-bimo-navy/10 rounded-[6px] text-bimo-text/40 hover:text-bimo-text hover:border-bimo-navy/30 transition-all duration-150"><svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg></a>
                @endif
                @foreach($paiementsMois->getUrlRange(max(1,$paiementsMois->currentPage()-2), min($paiementsMois->lastPage(),$paiementsMois->currentPage()+2)) as $page => $url)
                <a href="{{ $url }}" class="w-7 h-7 flex items-center justify-center rounded-[6px] font-body text-xs transition-all duration-150 {{ $page == $paiementsMois->currentPage() ? 'bg-bimo-navy text-white border border-bimo-navy' : 'border border-bimo-navy/10 text-bimo-text/60 hover:text-bimo-text hover:border-bimo-navy/30' }}">{{ $page }}</a>
                @endforeach
                @if($paiementsMois->hasMorePages())
                <a href="{{ $paiementsMois->nextPageUrl() }}" class="w-7 h-7 flex items-center justify-center border border-bimo-navy/10 rounded-[6px] text-bimo-text/40 hover:text-bimo-text hover:border-bimo-navy/30 transition-all duration-150"><svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg></a>
                @else
                <span class="w-7 h-7 flex items-center justify-center border border-bimo-navy/10 rounded-[6px] text-bimo-text/20 cursor-not-allowed"><svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg></span>
                @endif
            </div>
        </div>
        @endif
        @endif
    </div>

    {{-- Impayés — bandeau résumé --}}
    @if(isset($biensImpayes) && $biensImpayes->count() > 0)
    <a href="{{ route('admin.impayes.index', ['mois' => $mois, 'annee' => $annee]) }}"
       class="flex items-center justify-between gap-4 bg-bimo-red/[5%] border border-bimo-red/20 rounded-[12px] px-5 py-4 hover:bg-bimo-red/[8%] transition-all duration-150">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-[10px] bg-bimo-red/10 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-bimo-red" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            </div>
            <div>
                <div class="font-display font-bold text-sm text-bimo-red">{{ $biensImpayes->count() }} impayé(s) ce mois</div>
                <div class="font-body text-xs text-bimo-text/50 mt-0.5">{{ number_format($biensImpayes->sum('loyer_contractuel'), 0, ',', ' ') }} F non encaissés</div>
            </div>
        </div>
        <div class="flex items-center gap-1.5 font-body font-semibold text-sm text-bimo-red flex-shrink-0">
            Gérer les relances
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
        </div>
    </a>
    @else
    <div class="flex items-center gap-3 bg-bimo-gold/[5%] border border-bimo-gold/20 rounded-[12px] px-4 py-3.5">
        <svg class="w-5 h-5 text-bimo-gold flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
        <span class="font-body font-medium text-sm text-bimo-gold">Aucun impayé ce mois — Taux de recouvrement 100 %</span>
    </div>
    @endif

</div>
@endsection
