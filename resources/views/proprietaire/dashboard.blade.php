@extends('layouts.app')
@section('header', 'Mon espace propriétaire')

@section('content')

@php
$tauxOccupation = $stats['nb_biens'] > 0
    ? round(($stats['nb_biens_loues'] / $stats['nb_biens']) * 100)
    : 0;
@endphp

<div class="space-y-4 md:space-y-5">

    {{-- Greeting --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <h1 class="font-display font-extrabold text-xl md:text-2xl text-bimo-text tracking-tight">
                Bonjour, {{ explode(' ', auth()->user()->name)[0] }} 👋
            </h1>
            <p class="font-body text-sm text-bimo-text/50 mt-1">{{ now()->translatedFormat('l d F Y') }} · Aperçu de votre patrimoine</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('proprietaire.releve-pdf') }}" target="_blank"
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-bimo-gold/[8%] border border-bimo-gold/25 text-bimo-gold font-display font-bold text-sm rounded-[10px] hover:bg-bimo-gold/15 transition-all duration-150">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><polyline points="9 15 12 18 15 15"/></svg>
                Mon relevé PDF
            </a>
            <a href="{{ route('admin.biens.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-bimo-navy/15 text-bimo-text/60 font-body text-sm rounded-[10px] hover:text-bimo-text hover:border-bimo-navy/30 transition-all duration-150">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
                Mes biens
            </a>
        </div>
    </div>

    {{-- KPIs --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="bg-bimo-gold/[8%] rounded-[14px] border border-bimo-gold/25 p-4">
            <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-gold/70 mb-1.5">Total loyers encaissés</div>
            <div class="font-display font-extrabold text-xl text-bimo-gold leading-none">{{ number_format($stats['total_loyers'],0,',','') }}<span class="font-body text-xs text-bimo-gold/60 ml-1">F</span></div>
            <div class="font-body text-[10.5px] text-bimo-gold/60 mt-1.5">{{ $stats['nb_paiements'] }} paiements validés</div>
        </div>
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-4">
            <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-text/50 mb-1.5">Net reversé</div>
            <div class="font-display font-extrabold text-xl text-bimo-text leading-none">{{ number_format($stats['total_net'],0,',','') }}<span class="font-body text-xs text-bimo-text/40 ml-1">F</span></div>
            <div class="font-body text-[10.5px] text-bimo-text/40 mt-1.5">Votre revenu net total</div>
        </div>
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-4">
            <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-text/50 mb-1.5">Biens loués</div>
            <div class="font-display font-extrabold text-xl text-bimo-text leading-none">{{ $stats['nb_biens_loues'] }}<span class="font-body text-sm text-bimo-text/30 ml-1">/ {{ $stats['nb_biens'] }}</span></div>
            <div class="font-body text-[10.5px] text-bimo-text/40 mt-1.5">{{ $tauxOccupation }}% d'occupation</div>
        </div>
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-4">
            <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-text/50 mb-1.5">Caution totale</div>
            <div class="font-display font-extrabold text-xl text-bimo-text leading-none">{{ number_format($stats['caution'],0,',','') }}<span class="font-body text-xs text-bimo-text/40 ml-1">F</span></div>
            <div class="font-body text-[10.5px] text-bimo-text/40 mt-1.5">Dépôts de garantie</div>
        </div>
    </div>

    {{-- Bilan dark --}}
    <div class="bg-bimo-navy rounded-[14px] p-5 md:p-7 grid grid-cols-1 md:grid-cols-3 gap-5 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-48 h-48 rounded-full opacity-[0.06]"
             style="background:radial-gradient(circle,#C9A84C 0%,transparent 70%);transform:translate(30%,-30%)"></div>
        @foreach([
            ['Total encaissé brut', number_format($stats['total_loyers'],0,',',''), 'Depuis le début', 'text-bimo-gold'],
            ['Commission agence (TTC)', number_format($stats['total_commission'],0,',',''), 'Déduites automatiquement', 'text-white'],
            ['Net reversé propriétaire', number_format($stats['total_net'],0,',',''), 'Votre revenu net cumulé', 'text-white'],
        ] as $i => [$lbl, $val, $sub, $cls])
        <div class="relative z-10 {{ $i > 0 ? 'md:pl-7 md:border-l md:border-white/[7%]' : '' }}">
            <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-white/40 mb-2">{{ $lbl }}</div>
            <div class="font-display font-extrabold text-2xl {{ $cls }} leading-none">{{ $val }}<span class="font-body text-sm text-white/30 ml-1">F</span></div>
            <div class="font-body text-xs text-white/30 mt-1.5">{{ $sub }}</div>
        </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-[1fr_280px] gap-5 items-start">

        {{-- Colonne gauche --}}
        <div class="space-y-4">

            {{-- Mes biens --}}
            <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
                <div class="flex items-center justify-between px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
                    <span class="font-display font-bold text-sm text-bimo-text">Mes biens ({{ $biens->total() }})</span>
                    <a href="{{ route('admin.biens.index') }}" class="flex items-center gap-1 font-body text-xs text-bimo-text/40 hover:text-bimo-gold transition-colors duration-150">
                        Voir tout <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                    </a>
                </div>

                @if($biens->isEmpty())
                <div class="px-5 py-10 text-center font-body text-sm text-bimo-text/30">Aucun bien associé à votre compte.</div>
                @else
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 p-4">
                    @foreach($biens as $bien)
                    @php
                        $sp = match($bien->statut) { 'loue'=>['bg-bimo-gold/10 border-bimo-gold/20 text-bimo-gold','Loué'], 'disponible'=>['bg-bimo-navy/10 border-bimo-navy/15 text-bimo-text/70','Disponible'], default=>['bg-bimo-navy/10 border-bimo-navy/15 text-bimo-text/50','En travaux'] };
                        $photo = $bien->photos?->firstWhere('est_principale', true) ?? $bien->photos?->first();
                    @endphp
                    <a href="{{ route('admin.biens.show', $bien) }}" class="block border border-bimo-navy/10 rounded-[10px] overflow-hidden hover:shadow-md transition-shadow duration-150">
                        <div class="h-24 bg-bimo-bg2 relative overflow-hidden">
                            @if($photo)
                            <img src="{{ Storage::url($photo->chemin) }}" alt="{{ $bien->reference }}" class="w-full h-full object-cover" loading="lazy">
                            @else
                            <div class="w-full h-full flex items-center justify-center">
                                <svg class="w-8 h-8 text-bimo-gold/30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
                            </div>
                            @endif
                            <span class="absolute top-2 left-2 inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-body font-semibold border {{ $sp[0] }}">{{ $sp[1] }}</span>
                        </div>
                        <div class="px-3 py-2.5">
                            <div class="font-display font-bold text-xs text-bimo-text">{{ $bien->reference }}</div>
                            <div class="font-body text-[11px] text-bimo-text/50 truncate mt-0.5">{{ $bien->adresse }}, {{ $bien->ville }}</div>
                            <div class="font-display font-bold text-sm text-bimo-gold mt-1.5">{{ number_format($bien->loyer_mensuel,0,',','') }} F/mois</div>
                        </div>
                    </a>
                    @endforeach
                </div>

                @if($biens->hasPages())
                <div class="flex items-center justify-between px-5 py-3 border-t border-bimo-navy/[5%]">
                    <span class="font-body text-xs text-bimo-text/40">{{ $biens->firstItem() }}–{{ $biens->lastItem() }} sur {{ $biens->total() }}</span>
                    <div class="flex items-center gap-1">
                        <a href="{{ $biens->previousPageUrl() ?? '#' }}" class="w-7 h-7 inline-flex items-center justify-center border border-bimo-navy/15 rounded-[6px] text-bimo-text/40 hover:border-bimo-gold hover:text-bimo-gold transition-all duration-150 {{ $biens->onFirstPage() ? 'opacity-40 pointer-events-none' : '' }}">
                            <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                        </a>
                        <a href="{{ $biens->nextPageUrl() ?? '#' }}" class="w-7 h-7 inline-flex items-center justify-center border border-bimo-navy/15 rounded-[6px] text-bimo-text/40 hover:border-bimo-gold hover:text-bimo-gold transition-all duration-150 {{ !$biens->hasMorePages() ? 'opacity-40 pointer-events-none' : '' }}">
                            <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                        </a>
                    </div>
                </div>
                @endif
                @endif
            </div>

            {{-- Graphique net mensuel --}}
            @if($loyersParMois->isNotEmpty())
            <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
                <div class="flex items-center justify-between px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
                    <span class="font-display font-bold text-sm text-bimo-text">Net reversé par mois</span>
                    <span class="font-body text-xs text-bimo-text/40">12 derniers mois · après commission</span>
                </div>
                <div class="px-5 py-5" style="height:200px;position:relative">
                    <canvas id="chartNetMensuel"></canvas>
                </div>
            </div>
            @push('scripts')
            <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js"></script>
            <script>
            (function(){
                var ctx = document.getElementById('chartNetMensuel').getContext('2d');
                var grad = ctx.createLinearGradient(0,0,0,160);
                grad.addColorStop(0,'rgba(201,168,76,.15)');
                grad.addColorStop(1,'rgba(201,168,76,.00)');
                new Chart(ctx,{
                    type:'line',
                    data:{
                        labels:@json($loyersParMois->pluck('mois')),
                        datasets:[{
                            label:'Net reversé',
                            data:@json($loyersParMois->pluck('net')),
                            borderColor:'#C9A84C',
                            backgroundColor:grad,
                            borderWidth:2.5,
                            pointBackgroundColor:'#C9A84C',
                            pointBorderColor:'#fff',
                            pointBorderWidth:2,
                            pointRadius:4,
                            fill:true,
                            tension:0.4
                        }]
                    },
                    options:{
                        responsive:true,
                        maintainAspectRatio:false,
                        plugins:{
                            legend:{display:false},
                            tooltip:{
                                backgroundColor:'#1B4F6B',
                                titleColor:'#fff',
                                bodyColor:'rgba(255,255,255,.6)',
                                padding:10,
                                cornerRadius:8,
                                callbacks:{label:function(c){return ' '+Number(c.parsed.y).toLocaleString('fr-FR')+' FCFA';}}
                            }
                        },
                        scales:{
                            x:{grid:{display:false},border:{display:false},ticks:{font:{size:11},color:'rgba(27,79,107,.4)'}},
                            y:{grid:{color:'rgba(27,79,107,.05)'},border:{display:false},ticks:{font:{size:10},color:'rgba(27,79,107,.4)',callback:function(v){return v>=1000000?(v/1000000).toFixed(1)+'M':(v/1000)+'k';}}}
                        }
                    }
                });
            })();
            </script>
            @endpush
            @endif

            {{-- Derniers versements --}}
            <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
                <div class="flex items-center justify-between px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
                    <span class="font-display font-bold text-sm text-bimo-text">Derniers versements</span>
                    <span class="font-body text-xs text-bimo-text/40">Net reversé après commission</span>
                </div>
                @if($paiements->isEmpty())
                <div class="px-5 py-10 text-center font-body text-sm text-bimo-text/30">Aucun paiement enregistré pour l'instant.</div>
                @else
                {{-- Mobile --}}
                <div class="md:hidden divide-y divide-bimo-navy/[5%]">
                    @foreach($paiements as $p)
                    <div class="px-4 py-3.5">
                        <div class="flex items-center justify-between gap-3 mb-1">
                            <span class="font-display font-bold text-sm text-bimo-text">{{ $p->contrat?->bien?->reference ?? '—' }}</span>
                            <span class="font-display font-bold text-sm text-bimo-gold">{{ number_format($p->net_proprietaire,0,',','') }} F</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-display font-semibold bg-bimo-gold/10 border border-bimo-gold/20 text-bimo-gold">{{ \Carbon\Carbon::parse($p->periode)->translatedFormat('M Y') }}</span>
                            <span class="font-body text-[11px] text-bimo-text/40">{{ $p->contrat?->locataire?->name }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
                {{-- Desktop --}}
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-bimo-navy/[5%] bg-bimo-bg">
                                <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Bien</th>
                                <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Locataire</th>
                                <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Période</th>
                                <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Date</th>
                                <th class="px-5 py-3 text-right font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Loyer brut</th>
                                <th class="px-5 py-3 text-right font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Net reversé</th>
                                <th class="px-5 py-3 text-center font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Doc</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-bimo-navy/[5%]">
                            @foreach($paiements as $p)
                            <tr class="hover:bg-bimo-bg transition-colors duration-100">
                                <td class="px-5 py-3.5 font-body font-medium text-sm text-bimo-text">{{ $p->contrat?->bien?->reference ?? '—' }}</td>
                                <td class="px-5 py-3.5 font-body text-xs text-bimo-text/60">{{ $p->contrat?->locataire?->name ?? '—' }}</td>
                                <td class="px-5 py-3.5">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-display font-semibold bg-bimo-gold/10 border border-bimo-gold/20 text-bimo-gold">{{ \Carbon\Carbon::parse($p->periode)->translatedFormat('M Y') }}</span>
                                </td>
                                <td class="px-5 py-3.5 font-body text-xs text-bimo-text/60">{{ \Carbon\Carbon::parse($p->date_paiement)->format('d/m/Y') }}</td>
                                <td class="px-5 py-3.5 text-right font-display font-semibold text-sm text-bimo-text/70">{{ number_format($p->montant_encaisse,0,',','') }} F</td>
                                <td class="px-5 py-3.5 text-right font-display font-bold text-sm text-bimo-gold">{{ number_format($p->net_proprietaire,0,',','') }} F</td>
                                <td class="px-5 py-3.5 text-center">
                                    <a href="{{ route('proprietaire.paiements.pdf', $p) }}" target="_blank"
                                       class="w-7 h-7 inline-flex items-center justify-center border border-bimo-navy/10 rounded-[6px] text-bimo-text/40 hover:text-bimo-gold hover:border-bimo-gold/30 transition-all duration-150">
                                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($paiements->hasPages())
                <div class="flex items-center justify-between px-5 py-3.5 border-t border-bimo-navy/[5%]">
                    <span class="font-body text-xs text-bimo-text/40">Page {{ $paiements->currentPage() }} / {{ $paiements->lastPage() }}</span>
                    <div class="flex items-center gap-1">
                        <a href="{{ $paiements->previousPageUrl() ?? '#' }}" class="w-7 h-7 inline-flex items-center justify-center border border-bimo-navy/15 rounded-[6px] text-bimo-text/40 hover:border-bimo-gold hover:text-bimo-gold transition-all duration-150 {{ $paiements->onFirstPage() ? 'opacity-40 pointer-events-none' : '' }}">
                            <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                        </a>
                        @foreach($paiements->getUrlRange(max(1,$paiements->currentPage()-1),min($paiements->lastPage(),$paiements->currentPage()+1)) as $pg => $url)
                        <a href="{{ $url }}" class="w-7 h-7 inline-flex items-center justify-center border rounded-[6px] font-body text-xs transition-all duration-150 {{ $pg === $paiements->currentPage() ? 'bg-[var(--ac)] border-[var(--ac)] text-white font-bold' : 'border-bimo-navy/15 text-bimo-text/50 hover:border-bimo-gold hover:text-bimo-gold' }}">{{ $pg }}</a>
                        @endforeach
                        <a href="{{ $paiements->nextPageUrl() ?? '#' }}" class="w-7 h-7 inline-flex items-center justify-center border border-bimo-navy/15 rounded-[6px] text-bimo-text/40 hover:border-bimo-gold hover:text-bimo-gold transition-all duration-150 {{ !$paiements->hasMorePages() ? 'opacity-40 pointer-events-none' : '' }}">
                            <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                        </a>
                    </div>
                </div>
                @endif
                @endif
            </div>

        </div>

        {{-- Colonne droite --}}
        <div class="lg:sticky lg:top-6 space-y-3">

            {{-- Occupation ring --}}
            <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
                <div class="px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
                    <span class="font-display font-bold text-sm text-bimo-text">Taux d'occupation</span>
                </div>
                @php
                    $circumference = 2 * M_PI * 32;
                    $dash = ($tauxOccupation / 100) * $circumference;
                    $ringColor = $tauxOccupation >= 80 ? '#C9A84C' : ($tauxOccupation >= 50 ? '#C9A84C' : '#EF4444');
                @endphp
                <div class="flex flex-col items-center py-5 gap-2">
                    <div class="relative" style="width:80px;height:80px">
                        <svg width="80" height="80" style="transform:rotate(-90deg)">
                            <circle cx="40" cy="40" r="32" fill="none" stroke="rgba(27,79,107,.08)" stroke-width="8"/>
                            <circle cx="40" cy="40" r="32" fill="none" stroke="{{ $ringColor }}"
                                    stroke-width="8" stroke-linecap="round"
                                    stroke-dasharray="{{ $dash }} {{ $circumference }}" stroke-dashoffset="0"/>
                        </svg>
                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                            <span class="font-display font-extrabold text-base leading-none" style="color:{{ $ringColor }}">{{ $tauxOccupation }}%</span>
                            <span class="font-body text-[9px] text-bimo-text/40 mt-0.5">occupé</span>
                        </div>
                    </div>
                    <div class="font-body font-medium text-sm text-bimo-text text-center">{{ $stats['nb_biens_loues'] }} loué(s) sur {{ $stats['nb_biens'] }} bien(s)</div>
                    <div class="font-body text-xs text-bimo-text/40 text-center">
                        @if($stats['nb_biens'] - $stats['nb_biens_loues'] > 0)
                            {{ $stats['nb_biens'] - $stats['nb_biens_loues'] }} bien(s) disponible(s)
                        @else
                            Parc immobilier pleinement loué ✓
                        @endif
                    </div>
                </div>
            </div>

            {{-- KPI minis --}}
            <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-4">
                <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-text/50 mb-1">Net reversé (all time)</div>
                <div class="font-display font-extrabold text-xl text-bimo-text leading-none">{{ number_format($stats['total_net'],0,',','') }}<span class="font-body text-xs text-bimo-text/40 ml-1">F</span></div>
            </div>

            <div class="bg-bimo-gold/[8%] rounded-[14px] border border-bimo-gold/25 p-4">
                <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-gold/70 mb-1">Caution totale</div>
                <div class="font-display font-extrabold text-xl text-bimo-gold leading-none">{{ number_format($stats['caution'],0,',','') }}<span class="font-body text-xs text-bimo-gold/60 ml-1">F</span></div>
                <div class="font-body text-[10.5px] text-bimo-gold/60 mt-1">Dépôts de garantie détenus</div>
            </div>

            @if($stats['dernier_paiement'])
            <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-4">
                <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-text/50 mb-1">Dernier versement</div>
                <div class="font-display font-extrabold text-lg text-bimo-text leading-none">{{ number_format($stats['dernier_paiement']->net_proprietaire,0,',','') }}<span class="font-body text-xs text-bimo-text/40 ml-1">F</span></div>
                <div class="font-body text-[10.5px] text-bimo-text/40 mt-1">{{ \Carbon\Carbon::parse($stats['dernier_paiement']->date_paiement)->format('d/m/Y') }}</div>
            </div>
            @endif

            {{-- Agence --}}
            @if($currentAgency)
            <div class="bg-bimo-navy rounded-[14px] overflow-hidden">
                <div class="px-4 py-3.5 border-b border-white/[7%]">
                    <span class="font-display font-bold text-sm text-white">Votre agence</span>
                </div>
                <div class="px-4 py-4 space-y-2.5">
                    <div class="font-display font-bold text-sm text-white mb-2">{{ $currentAgency->name }}</div>
                    @if($currentAgency->telephone)
                    <div class="flex items-center gap-2.5">
                        <svg class="w-3.5 h-3.5 text-white/30 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07"/></svg>
                        <a href="tel:{{ $currentAgency->telephone }}" class="font-body text-xs text-white/60 hover:text-white transition-colors duration-150">{{ $currentAgency->telephone }}</a>
                    </div>
                    @endif
                    @if($currentAgency->email)
                    <div class="flex items-center gap-2.5">
                        <svg class="w-3.5 h-3.5 text-white/30 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4"/><polyline points="22,6 12,13 2,6"/></svg>
                        <a href="mailto:{{ $currentAgency->email }}" class="font-body text-xs text-bimo-gold hover:text-white transition-colors duration-150">{{ $currentAgency->email }}</a>
                    </div>
                    @endif
                    @if($currentAgency->adresse)
                    <div class="flex items-center gap-2.5">
                        <svg class="w-3.5 h-3.5 text-white/30 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        <span class="font-body text-xs text-white/50">{{ $currentAgency->adresse }}</span>
                    </div>
                    @endif
                    @if($currentAgency->telephone)
                    @php
                        $tel = preg_replace('/\s+|-/', '', $currentAgency->telephone);
                        if (!str_starts_with($tel,'+') && !str_starts_with($tel,'221')) $tel = '221'.ltrim($tel,'0');
                        $tel = ltrim($tel,'+');
                        $msgWa = "Bonjour {$currentAgency->name}, je suis ".auth()->user()->name.", propriétaire géré par votre agence. Je souhaite vous contacter.";
                    @endphp
                    <a href="https://wa.me/{{ $tel }}?text={{ urlencode($msgWa) }}" target="_blank"
                       class="flex items-center justify-center gap-2 mt-2 px-4 py-2.5 bg-[#25D366] text-white rounded-[9px] font-body font-semibold text-sm hover:opacity-90 transition-opacity duration-150">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.124.558 4.118 1.532 5.847L.057 23.492a.5.5 0 00.614.65l5.82-1.527A11.945 11.945 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22a9.944 9.944 0 01-5.091-1.396l-.361-.216-3.754.984.999-3.648-.237-.374A9.944 9.944 0 012 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/></svg>
                        Contacter par WhatsApp
                    </a>
                    @endif
                </div>
            </div>
            @endif

        </div>
    </div>

</div>
@endsection
