@extends('layouts.superadmin')

@section('title', 'Historique des commissions')

@php $fmt = fn ($n) => number_format((float) $n, 0, ',', ' '); @endphp

@section('content')
<div class="max-w-[900px] mx-auto">

    {{-- En-tête --}}
    <div class="flex items-start justify-between gap-4 mb-6">
        <div>
            <a href="{{ route('superadmin.equipe.index') }}" class="text-[13px] font-semibold text-teal hover:underline inline-flex items-center gap-1.5 mb-3">← Équipe interne</a>
            <div class="text-[12px] font-semibold tracking-[0.12em] uppercase text-gold">Rémunération</div>
            <h1 class="font-display font-medium text-[30px] text-ink mt-1">Commissions de {{ $collaborateur->name }}</h1>
            <p class="text-[14.5px] text-muted mt-1.5">Vue mensuelle en lecture seule — les mois passés sont figés et servent de justificatif.</p>
        </div>
        <div class="flex gap-2 shrink-0">
            <a href="{{ route('superadmin.equipe.commissions.csv', $collaborateur) }}" class="text-[12.5px] font-semibold px-3.5 py-2 rounded-lg border border-line text-ink hover:bg-paper-dim transition-colors">Export CSV</a>
            <a href="{{ route('superadmin.equipe.commissions.pdf', $collaborateur) }}" class="text-[12.5px] font-semibold px-3.5 py-2 rounded-lg border border-line text-ink hover:bg-paper-dim transition-colors">Export PDF</a>
        </div>
    </div>

    <section class="bg-white border border-line rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="text-left text-[11px] uppercase tracking-wide text-muted font-semibold">
                        <th class="px-4 py-3 border-b border-paper-dim">Mois</th>
                        <th class="px-4 py-3 border-b border-paper-dim text-right">Agences</th>
                        <th class="px-4 py-3 border-b border-paper-dim text-right">MRR total</th>
                        <th class="px-4 py-3 border-b border-paper-dim text-right">Taux</th>
                        <th class="px-4 py-3 border-b border-paper-dim text-right">Commission</th>
                        <th class="px-4 py-3 border-b border-paper-dim"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($lignes as $l)
                        <tr class="text-[13.5px]">
                            <td class="px-4 py-3 border-b border-paper-dim font-semibold text-ink">{{ \Illuminate\Support\Str::ucfirst($l['mois']->locale('fr')->isoFormat('MMMM Y')) }}</td>
                            <td class="px-4 py-3 border-b border-paper-dim text-right tabular-nums text-muted">{{ $fmt($l['nb_agences']) }}</td>
                            <td class="px-4 py-3 border-b border-paper-dim text-right tabular-nums text-ink">{{ $fmt($l['mrr_total']) }} F</td>
                            <td class="px-4 py-3 border-b border-paper-dim text-right tabular-nums text-muted">{{ rtrim(rtrim(number_format($l['taux'], 2, ',', ''), '0'), ',') }} %</td>
                            <td class="px-4 py-3 border-b border-paper-dim text-right tabular-nums font-semibold text-gold">{{ $fmt($l['commission']) }} F</td>
                            <td class="px-4 py-3 border-b border-paper-dim text-right">
                                @if($l['fige'])
                                    <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full bg-paper-dim text-muted">Figé</span>
                                @else
                                    <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full bg-gold/15 text-gold-deep">En cours</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-8 text-center text-[13px] text-muted">Aucun historique de commission pour l'instant.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <p class="text-[12px] text-muted mt-4 leading-relaxed flex gap-2">
        <span class="text-gold shrink-0">ℹ</span>
        La base de calcul est le MRR actuel des agences attribuées — une agence en échec de paiement compte quand même. Le taux est propre à ce collaborateur.
    </p>
</div>
@endsection
