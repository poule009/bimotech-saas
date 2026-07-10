@extends('layouts.app')

@php
    $fmt = fn ($n) => number_format((float) $n, 0, ',', ' ');
    $statutPill = [
        'loue'       => ['Loué', 'bg-green/10 text-green'],
        'disponible' => ['Vacant', 'bg-gold/15 text-gold'],
        'en_travaux' => ['En travaux', 'bg-error/10 text-error'],
    ];
    $unites   = $immeuble->biens;
    $total    = $unites->count();
    $loues    = $unites->where('statut', 'loue')->count();
    $vacants  = $unites->where('statut', 'disponible')->count();
    $taux     = $total > 0 ? round($loues / $total * 100) : 0;
    $revenus  = $unites->where('statut', 'loue')->sum('loyer_mensuel');
@endphp

@section('title', $immeuble->nom)
@section('page-title', 'Fiche immeuble')
@section('page-subtitle')
    <a href="{{ route('admin.biens.index') }}" class="text-teal font-semibold hover:underline">Biens</a>
    <span class="text-muted"> / {{ $immeuble->nom }}</span>
@endsection

@section('content')
<div class="max-w-[1000px]">

    @if(session('success'))
        <div class="mb-5 rounded-lg bg-green/10 border border-green/25 px-4 py-3 text-[13px] text-green">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-5 rounded-lg bg-error/10 border border-error/25 px-4 py-3 text-[13px] text-error">{{ session('error') }}</div>
    @endif

    {{-- En-tête --}}
    <div class="bg-white border border-line rounded-2xl overflow-hidden mb-5">
        <div class="h-[150px] bg-teal-deep flex items-end p-6">
            <div class="text-paper">
                <h2 class="font-display font-semibold text-[24px] mb-1 flex items-center gap-2"><x-icon name="building" size="22" /> {{ $immeuble->nom }}</h2>
                <div class="text-[13.5px] text-paper/80">{{ $immeuble->ville }} · Immeuble · {{ $total }} unité{{ $total > 1 ? 's' : '' }}</div>
            </div>
        </div>
        <div class="px-6 py-4 flex flex-wrap items-center gap-3">
            <span class="text-[12.5px] font-bold px-3.5 py-1.5 rounded-full {{ $loues >= $total && $total > 0 ? 'bg-green/10 text-green' : 'bg-teal/10 text-teal' }}">{{ $loues }}/{{ $total }} loués</span>
            <div class="flex items-center gap-2.5 ml-auto">
                <a href="{{ route('admin.immeubles.edit', $immeuble) }}" class="px-5 py-2.5 rounded-[10px] border-[1.5px] border-line bg-white text-ink text-[14px] font-bold hover:border-teal transition-colors">Modifier</a>
                <a href="{{ route('admin.biens.create', ['immeuble_id' => $immeuble->id]) }}" class="px-5 py-2.5 rounded-[10px] bg-teal text-paper text-[14px] font-bold hover:bg-teal-deep transition-colors whitespace-nowrap">+ Ajouter un appartement</a>
            </div>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white border border-line rounded-xl p-4">
            <div class="text-[11.5px] uppercase tracking-wide text-muted font-bold mb-1.5">Taux d'occupation</div>
            <div class="font-display font-semibold text-[22px] text-green">{{ $taux }}%</div>
        </div>
        <div class="bg-white border border-line rounded-xl p-4">
            <div class="text-[11.5px] uppercase tracking-wide text-muted font-bold mb-1.5">Revenus mensuels</div>
            <div class="font-display font-semibold text-[22px]">{{ $fmt($revenus) }} <span class="text-[13px] font-body text-muted">F</span></div>
        </div>
        <div class="bg-white border border-line rounded-xl p-4">
            <div class="text-[11.5px] uppercase tracking-wide text-muted font-bold mb-1.5">Unités vacantes</div>
            <div class="font-display font-semibold text-[22px] text-gold">{{ $vacants }}</div>
        </div>
        <div class="bg-white border border-line rounded-xl p-4">
            <div class="text-[11.5px] uppercase tracking-wide text-muted font-bold mb-1.5">Propriétaire</div>
            <a href="{{ route('admin.users.show', $immeuble->proprietaire) }}" class="font-display font-semibold text-[16px] text-teal hover:underline">{{ $immeuble->proprietaire->name ?? '—' }}</a>
        </div>
    </div>

    {{-- Unités --}}
    <div class="f-card">
        <div class="flex items-center justify-between mb-4">
            <h3 class="f-card-title mb-0">Appartements <span class="text-muted font-body font-normal text-[15px]">· {{ $total }}</span></h3>
        </div>
        @if($total === 0)
            <div class="py-10 text-center">
                <p class="text-[13.5px] text-muted mb-4">Aucun appartement dans cet immeuble.</p>
                <a href="{{ route('admin.biens.create', ['immeuble_id' => $immeuble->id]) }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-teal text-paper font-bold text-[13px] rounded-[10px] hover:bg-teal-deep transition-colors">+ Ajouter le premier appartement</a>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($unites as $u)
                    @php [$stLabel, $stClass] = $statutPill[$u->statut] ?? [ucfirst($u->statut), 'bg-paper-dim text-muted']; @endphp
                    <a href="{{ route('admin.biens.show', $u) }}" class="block p-4 border rounded-xl transition-colors {{ $u->statut === 'loue' ? 'bg-green/[0.06] border-green/25' : ($u->statut === 'disponible' ? 'bg-gold/[0.08] border-gold/25' : 'bg-white border-line') }} hover:border-teal">
                        <div class="flex items-center justify-between gap-2 mb-1">
                            <div class="font-bold text-[14px] truncate">{{ $u->titre ?: $u->reference }}</div>
                            <span class="text-[10.5px] font-bold px-2 py-0.5 rounded-full shrink-0 {{ $stClass }}">{{ $stLabel }}</span>
                        </div>
                        <div class="text-[12px] text-muted truncate">
                            @if($u->contratActif)
                                {{ $u->contratActif->locataire->name ?? 'Locataire' }} · {{ $fmt($u->loyer_mensuel) }} F
                            @else
                                {{ $fmt($u->loyer_mensuel) }} F/mois
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
