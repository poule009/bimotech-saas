@extends('layouts.superadmin')

@section('title', 'Dashboard')

@php
    $fmt = fn ($n) => number_format((float) $n, 0, ',', ' ');

    // MRR compact pour la carte KPI (1 070 000 → « 1,07M »).
    $mrrCompact = $stats['mrr'] >= 1000000
        ? rtrim(rtrim(number_format($stats['mrr'] / 1000000, 2, ',', ' '), '0'), ',') . 'M'
        : $fmt($stats['mrr']);

    $g       = $stats['mrr_growth'];
    $gAbs    = number_format(abs($g), 1, ',', ' ');
    $gClass  = $g > 0 ? 'text-green' : ($g < 0 ? 'text-error' : 'text-muted');
    $gArrow  = $g > 0 ? '↑' : ($g < 0 ? '↓' : '→');

    // Styles de sévérité des alertes (barre + pastille d'icône).
    $sev = [
        'urgent' => ['bar' => 'bg-error',  'icon' => 'bg-error/10 text-error'],
        'warn'   => ['bar' => 'bg-amber',  'icon' => 'bg-amber/10 text-amber'],
        'info'   => ['bar' => 'bg-teal',   'icon' => 'bg-teal/10 text-teal'],
    ];
@endphp

@section('content')
<div class="max-w-[1240px] mx-auto">

    {{-- ─────────── En-tête ─────────── --}}
    <div class="flex items-start justify-between gap-4 mb-1">
        <div>
            <div class="text-[12px] font-semibold tracking-[0.12em] uppercase text-gold">Vue d'ensemble</div>
            <h1 class="font-display font-medium text-[30px] md:text-[32px] text-ink mt-1">Bonjour {{ \Illuminate\Support\Str::of(auth()->user()->name ?? '')->explode(' ')->first() }}</h1>
        </div>
        <div class="hidden sm:block text-[12.5px] text-muted bg-paper border border-line px-3 py-1.5 rounded-full whitespace-nowrap">
            {{ \Illuminate\Support\Str::ucfirst($dateStr) }}
        </div>
    </div>
    <p class="text-[14.5px] text-muted mb-7">Voici l'état de la plateforme et ce qui demande ton attention aujourd'hui.</p>

    {{-- ─────────── KPI ─────────── --}}
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-3.5 mb-8">
        {{-- Agences actives --}}
        <div class="bg-white border border-line rounded-xl p-[18px]">
            <div class="text-[11.5px] font-semibold uppercase tracking-wide text-muted mb-2.5">Agences actives</div>
            <div class="font-display font-medium text-[28px] leading-none text-ink">{{ $fmt($stats['agences_actives']) }}</div>
            <div class="mt-2.5 text-[12px] font-semibold {{ $stats['agences_nouvelles'] > 0 ? 'text-green' : 'text-muted' }}">
                {{ $stats['agences_nouvelles'] > 0 ? '↑ +' . $stats['agences_nouvelles'] . ' ce mois' : 'stable ce mois' }}
            </div>
        </div>

        {{-- MRR --}}
        <div class="bg-white border border-line rounded-xl p-[18px]">
            <div class="text-[11.5px] font-semibold uppercase tracking-wide text-muted mb-2.5">MRR</div>
            <div class="font-display font-medium text-[28px] leading-none text-ink">{{ $mrrCompact }}</div>
            <div class="mt-2.5 text-[12px] font-semibold {{ $gClass }}">{{ $gArrow }} {{ $gAbs }} %</div>
        </div>

        {{-- En essai gratuit --}}
        <div class="bg-white border rounded-xl p-[18px] {{ $stats['essais_bientot'] > 0 ? 'border-[#E3C7A0]' : 'border-line' }}">
            <div class="text-[11.5px] font-semibold uppercase tracking-wide text-muted mb-2.5">En essai gratuit</div>
            <div class="font-display font-medium text-[28px] leading-none {{ $stats['essais_bientot'] > 0 ? 'text-gold' : 'text-ink' }}">{{ $fmt($stats['en_essai']) }}</div>
            <div class="mt-2.5 text-[12px] font-semibold text-muted">
                {{ $stats['essais_bientot'] > 0 ? $stats['essais_bientot'] . ' expirent bientôt' : 'aucun proche de l\'échéance' }}
            </div>
        </div>

        {{-- Suspendues / impayées --}}
        <div class="bg-white border rounded-xl p-[18px] {{ $stats['suspendues'] > 0 ? 'border-error/40' : 'border-line' }}">
            <div class="text-[11.5px] font-semibold uppercase tracking-wide text-muted mb-2.5">Suspendues / impayées</div>
            <div class="font-display font-medium text-[28px] leading-none {{ $stats['suspendues'] > 0 ? 'text-error' : 'text-ink' }}">{{ $fmt($stats['suspendues']) }}</div>
            <div class="mt-2.5 text-[12px] font-semibold text-muted">à surveiller</div>
        </div>

        {{-- Croissance MRR --}}
        <div class="bg-white border border-line rounded-xl p-[18px] col-span-2 lg:col-span-1">
            <div class="text-[11.5px] font-semibold uppercase tracking-wide text-muted mb-2.5">Croissance MRR</div>
            <div class="font-display font-medium text-[28px] leading-none {{ $gClass }}">{{ $g >= 0 ? '+' : '−' }}{{ $gAbs }} %</div>
            <div class="mt-2.5 text-[12px] font-semibold text-muted">vs mois dernier</div>
        </div>
    </div>

    {{-- ─────────── Alertes + Courbe ─────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-[1.15fr_0.85fr] gap-5 mb-6 items-start">

        {{-- À traiter aujourd'hui --}}
        <section class="bg-white border border-line rounded-xl overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-paper-dim">
                <div class="font-display font-medium text-[18px] text-ink flex items-center gap-2.5">
                    À traiter aujourd'hui
                    @if($alertes->isNotEmpty())
                        <span class="bg-error text-white text-[11.5px] font-bold px-2 py-0.5 rounded-full">{{ $alertes->count() }}</span>
                    @endif
                </div>
            </div>

            @if($alertes->isEmpty())
                <div class="px-5 py-8 text-center text-[13px] text-muted">
                    <div class="text-2xl mb-1">✓</div>
                    Rien d'urgent aujourd'hui — la plateforme est saine.
                </div>
            @else
                <ul class="p-1.5">
                    @foreach($alertes as $a)
                        @php $s = $sev[$a['severite']] ?? $sev['info']; @endphp
                        <li class="flex items-center gap-3.5 px-3.5 py-3 rounded-lg hover:bg-paper-dim/60 transition-colors">
                            <div class="w-1 self-stretch rounded {{ $s['bar'] }} shrink-0"></div>
                            <div class="w-[34px] h-[34px] rounded-[9px] flex items-center justify-center shrink-0 text-[15px] {{ $s['icon'] }}">{{ $a['icone'] }}</div>
                            <div class="flex-1 min-w-0">
                                <div class="text-[14px] font-semibold text-ink truncate">{{ $a['titre'] }}</div>
                                <div class="text-[12.5px] text-muted truncate">{{ $a['sous_titre'] }}</div>
                            </div>
                            <a href="{{ $a['action_url'] }}" @if($a['externe']) target="_blank" rel="noopener" @endif
                               @class([
                                   'text-[12.5px] font-semibold px-3 py-1.5 rounded-lg border whitespace-nowrap transition-colors shrink-0',
                                   'bg-teal border-teal text-paper hover:bg-teal-deep' => $a['action_primary'],
                                   'border-teal text-teal hover:bg-teal hover:text-paper' => ! $a['action_primary'],
                               ])>
                                {{ $a['action_label'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>

         {{-- Évolution MRR --}}
        <section class="bg-white border border-line rounded-xl overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-paper-dim">
                <div class="font-display font-medium text-[18px] text-ink">Évolution MRR</div>
                <div class="text-[11px] font-semibold uppercase tracking-wide text-muted">12 mois</div>
            </div>
            <div class="px-5 pt-2 pb-5">
                <div class="flex items-baseline gap-2.5 py-3">
                    <span class="font-display font-medium text-[28px] text-ink">{{ $fmt($stats['mrr']) }} FCFA</span>
                    <span class="text-[13px] font-semibold {{ $gClass }}">{{ $gArrow }} {{ $gAbs }} %</span>
                </div>

                @php
                    $vals = $chartMrr->values();
                    $n    = $vals->count();
                    $max  = max($vals->max() ?: 0, 1);
                    $xy = $vals->map(function ($v, $i) use ($n, $max) {
                        $x = $n > 1 ? round($i / ($n - 1) * 320, 1) : 0;
                        $y = round(120 - ($v / $max) * 104, 1); // 12 (haut) → 120 (bas)
                        return $x . ',' . $y;
                    });
                    $poly    = $xy->implode(' ');
                    $lastXY  = explode(',', $xy->last() ?? '320,120');
                @endphp

                <svg class="w-full h-[150px] block mt-1.5" viewBox="0 0 320 130" preserveAspectRatio="none">
                    <defs>
                        <linearGradient id="mrrFill" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#B8892B" stop-opacity="0.28"/>
                            <stop offset="100%" stop-color="#B8892B" stop-opacity="0"/>
                        </linearGradient>
                    </defs>
                    @if($n > 1)
                        <polygon fill="url(#mrrFill)" points="{{ $poly }} 320,130 0,130"/>
                        <polyline fill="none" stroke="#1B3A3F" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" points="{{ $poly }}"/>
                        <circle cx="{{ $lastXY[0] }}" cy="{{ $lastXY[1] }}" r="4" fill="#B8892B"/>
                    @else
                        <line x1="0" y1="120" x2="320" y2="120" stroke="#D8CFB8" stroke-width="1.5" stroke-dasharray="4 4"/>
                    @endif
                </svg>

                <div class="flex justify-between text-[10.5px] text-muted mt-1.5 px-0.5">
                    @foreach($chartLabels as $i => $label)
                        @if($i % 2 === 0 || $i === $chartLabels->count() - 1)
                            <span>{{ \Illuminate\Support\Str::ucfirst($label) }}</span>
                        @endif
                    @endforeach
                </div>
            </div>
        </section>
    </div>

    {{-- ─────────── Activité + Agences à risque ─────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

        {{-- Activité récente --}}
        <section class="bg-white border border-line rounded-xl overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-paper-dim">
                <div class="font-display font-medium text-[18px] text-ink">Activité récente</div>
                <a href="{{ route('superadmin.activity-logs.index') }}" class="text-[12.5px] font-semibold text-teal border-b border-gold pb-px">Journal complet</a>
            </div>
            @if($activites->isEmpty())
                <div class="px-5 py-8 text-center text-[13px] text-muted">Aucune activité récente.</div>
            @else
                <ul class="py-1.5">
                    @foreach($activites as $act)
                        <li class="flex gap-3 px-4 py-2.5 border-b border-paper-dim last:border-0">
                            <div class="w-[7px] h-[7px] rounded-full bg-gold mt-1.5 shrink-0"></div>
                            <div class="text-[13.5px] leading-snug text-ink min-w-0">
                                {{ $act['description'] }}
                                @if($act['agence'])
                                    <span class="text-muted"> — {{ $act['agence'] }}</span>
                                @endif
                            </div>
                            <div class="text-[11.5px] text-muted whitespace-nowrap ml-auto pl-2.5 font-medium tabular-nums">{{ $act['temps'] }}</div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>

        {{-- Agences à risque --}}
        <section class="bg-white border border-line rounded-xl overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-paper-dim">
                <div class="font-display font-medium text-[18px] text-ink">Agences à risque</div>
                <a href="{{ route('superadmin.agencies.index') }}" class="text-[12.5px] font-semibold text-teal border-b border-gold pb-px">Voir Agences</a>
            </div>
            @if($risque->isEmpty())
                <div class="px-5 py-8 text-center text-[13px] text-muted">Aucune agence à risque. 🎉</div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr class="text-left text-[11px] uppercase tracking-wide text-muted font-semibold">
                                <th class="px-4 py-2.5 border-b border-paper-dim">Agence</th>
                                <th class="px-4 py-2.5 border-b border-paper-dim">Plan</th>
                                <th class="px-4 py-2.5 border-b border-paper-dim">Statut</th>
                                <th class="px-4 py-2.5 border-b border-paper-dim"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($risque as $r)
                                @php $bord = $loop->last ? '' : 'border-b border-paper-dim'; @endphp
                                <tr class="text-[13.5px]">
                                    <td class="px-4 py-3 {{ $bord }} font-semibold text-ink">{{ $r['agence']->name }}</td>
                                    <td class="px-4 py-3 {{ $bord }} text-muted">{{ $r['plan'] }}</td>
                                    <td class="px-4 py-3 {{ $bord }}">
                                        <span @class([
                                            'text-[11px] font-semibold px-2.5 py-1 rounded-full inline-block',
                                            'bg-error/10 text-error' => $r['type'] === 'late',
                                            'bg-amber/10 text-amber'  => $r['type'] === 'idle',
                                        ])>{{ $r['motif'] }}</span>
                                    </td>
                                    <td class="px-4 py-3 {{ $bord }}">
                                        <a href="{{ route('superadmin.agencies.show', $r['agence']) }}" class="text-[12px] font-semibold text-teal border-b border-gold pb-px">Gérer</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    </div>
</div>
@endsection
