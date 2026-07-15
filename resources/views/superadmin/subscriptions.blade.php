@extends('layouts.superadmin')

@section('title', 'Abonnements & facturation')

@php
    $fmt = fn ($n) => number_format((float) $n, 0, ',', ' ');

    // Statut d'un abonnement → [libellé, classe de badge].
    $statutChip = function (\App\Models\Subscription $s) {
        return match ($s->statut) {
            'essai'  => ['Essai',   'bg-gold/10 text-gold'],
            'actif'  => ['Actif',   'bg-green/10 text-green'],
            'expiré' => ['Expiré',  'bg-error/10 text-error'],
            'annulé' => ['Annulé',  'bg-paper-dim text-muted'],
            default  => [ucfirst($s->statut ?? '—'), 'bg-paper-dim text-muted'],
        };
    };

    $planLabel = function (\App\Models\Subscription $s) {
        if ($s->statut === 'essai') return 'Essai';
        if (! $s->plan_niveau) return '—';
        $niveau = config('plans.labels.'.$s->plan_niveau, ucfirst($s->plan_niveau));
        $cycle = $s->plan ? (\App\Models\Subscription::LABELS[$s->plan] ?? $s->plan) : null;
        return $cycle ? $niveau.' · '.$cycle : $niveau;
    };
@endphp

@section('content')
<div class="max-w-[1240px] mx-auto">

    {{-- En-tête --}}
    <div class="mb-6">
        <div class="text-[12px] font-semibold tracking-[0.12em] uppercase text-gold">Gestion plateforme</div>
        <h1 class="font-display font-medium text-[30px] md:text-[32px] text-ink mt-1">Abonnements &amp; facturation</h1>
        <p class="text-[14.5px] text-muted mt-1.5">Vue d'ensemble des abonnements de toutes les agences.</p>
    </div>

    @if(session('success'))
        <div class="mb-5 rounded-lg bg-green/10 border border-green/25 px-4 py-3 text-[13px] text-green">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-5 rounded-lg bg-error/10 border border-error/25 px-4 py-3 text-[13px] text-error">{{ session('error') }}</div>
    @endif

    {{-- ─────────── KPI ─────────── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 mb-7">
        <div class="bg-white border border-line rounded-xl p-[18px]">
            <div class="text-[11.5px] font-semibold uppercase tracking-wide text-muted mb-2.5">MRR abonnements</div>
            <div class="font-display font-medium text-[26px] leading-none text-ink">{{ $fmt($stats['revenus_mensuel_equiv']) }}</div>
            <div class="mt-2.5 text-[12px] font-semibold text-muted">équivalent mensuel · FCFA</div>
        </div>
        <div class="bg-white border border-line rounded-xl p-[18px]">
            <div class="text-[11.5px] font-semibold uppercase tracking-wide text-muted mb-2.5">Abonnements actifs</div>
            <div class="font-display font-medium text-[26px] leading-none text-green">{{ $fmt($stats['nb_actifs']) }}</div>
            <div class="mt-2.5 text-[12px] font-semibold text-muted">payants en cours</div>
        </div>
        <div class="bg-white border border-line rounded-xl p-[18px]">
            <div class="text-[11.5px] font-semibold uppercase tracking-wide text-muted mb-2.5">En essai</div>
            <div class="font-display font-medium text-[26px] leading-none text-gold">{{ $fmt($stats['nb_essai']) }}</div>
            <div class="mt-2.5 text-[12px] font-semibold text-muted">périodes gratuites</div>
        </div>
        <div class="bg-white border border-line rounded-xl p-[18px]">
            <div class="text-[11.5px] font-semibold uppercase tracking-wide text-muted mb-2.5">Expirés</div>
            <div class="font-display font-medium text-[26px] leading-none {{ $stats['nb_expires'] > 0 ? 'text-error' : 'text-ink' }}">{{ $fmt($stats['nb_expires']) }}</div>
            <div class="mt-2.5 text-[12px] font-semibold text-muted">à relancer</div>
        </div>
    </div>

    {{-- ─────────── Tableau ─────────── --}}
    <section class="bg-white border border-line rounded-xl overflow-hidden">
        @if($subscriptions->isEmpty())
            <div class="px-5 py-14 text-center text-[13.5px] text-muted">Aucun abonnement enregistré.</div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="text-left text-[11px] uppercase tracking-wide text-muted font-semibold bg-paper-dim">
                        <th class="px-4 py-3">Agence</th>
                        <th class="px-4 py-3">Statut</th>
                        <th class="px-4 py-3">Plan</th>
                        <th class="px-4 py-3">Montant</th>
                        <th class="px-4 py-3 whitespace-nowrap">Échéance</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($subscriptions as $s)
                        @php
                            $chip = $statutChip($s);
                            $bord = $loop->last ? '' : 'border-b border-paper-dim';
                            $echeance = $s->statut === 'essai' ? $s->date_fin_essai : $s->date_fin_abonnement;
                        @endphp
                        <tr class="text-[13.8px] hover:bg-paper/60 transition-colors">
                            <td class="px-4 py-3.5 {{ $bord }}">
                                <div class="font-semibold text-ink">{{ $s->agency?->name ?? 'Agence #'.$s->agency_id }}</div>
                                <div class="text-[12px] text-muted">{{ $s->agency?->email }}</div>
                            </td>
                            <td class="px-4 py-3.5 {{ $bord }}">
                                <span class="text-[11.5px] font-semibold px-2.5 py-1 rounded-full inline-block {{ $chip[1] }}">{{ $chip[0] }}</span>
                                @unless($s->agency?->actif ?? true)
                                    <span class="text-[11px] font-semibold px-2 py-1 rounded-full bg-error/10 text-error ml-1">Désactivée</span>
                                @endunless
                            </td>
                            <td class="px-4 py-3.5 {{ $bord }} text-ink">{{ $planLabel($s) }}</td>
                            <td class="px-4 py-3.5 {{ $bord }}">
                                <span class="tabular-nums font-semibold {{ $s->montant_paye ? 'text-ink' : 'text-muted' }}">{{ $s->montant_paye ? $fmt($s->montant_paye).' F' : '—' }}</span>
                            </td>
                            <td class="px-4 py-3.5 {{ $bord }} text-muted whitespace-nowrap">{{ $echeance?->locale('fr')->isoFormat('D MMM Y') ?? '—' }}</td>
                            <td class="px-4 py-3.5 {{ $bord }} text-right">
                                @if($s->agency)
                                    <a href="{{ route('superadmin.agencies.show', $s->agency) }}" class="text-[12px] font-semibold text-teal border-b border-gold pb-px">Gérer</a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($subscriptions->hasPages())
            <div class="px-5 py-3.5 border-t border-paper-dim">
                {{ $subscriptions->links() }}
            </div>
        @endif
        @endif
    </section>
</div>
@endsection
