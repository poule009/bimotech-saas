@extends('layouts.app')

@php
    $initiales = fn ($nom) => collect(explode(' ', trim($nom)))
        ->filter()->take(2)->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))->implode('');

    $badge = [
        'administrateur' => ['bg-gold/15 text-gold', 'Administrateur'],
        'secretaire'     => ['bg-teal/10 text-teal', 'Secrétaire'],
        'personnalise'   => ['bg-ink/8 text-ink/70', 'Personnalisé'],
    ];

    $illimite = $limiteMax === null;
    $pct = $illimite ? 100 : ($limiteMax > 0 ? min(100, round($nbActuels / $limiteMax * 100)) : 0);
@endphp

@section('title', 'Mon équipe')
@section('page-title', 'Mon équipe')
@section('page-subtitle', 'Donnez accès à votre équipe, avec exactement ce dont chacun a besoin.')

@section('content')
<div class="max-w-[860px]">

    @if(session('success'))
        <div class="mb-5 rounded-lg bg-green/10 border border-green/25 px-4 py-3 text-[13px] text-green flex items-center gap-2"><x-icon name="check-circle" size="16" /> {{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-5 rounded-lg bg-error/10 border border-error/25 px-4 py-3 text-[13px] text-error flex items-center gap-2"><x-icon name="alert-triangle" size="16" /> {{ session('error') }}</div>
    @endif

    {{-- En-tête + action --}}
    <div class="flex items-center justify-between gap-3 mb-6">
        <div></div>
        @if($peutGerer)
            @if($peutAjouter)
                <a href="{{ route('admin.equipe.create') }}" class="bg-teal hover:bg-teal-deep text-white px-5 py-3 rounded-[11px] text-[14px] font-bold flex items-center gap-1.5">
                    <x-icon name="plus" size="16" /> Inviter un membre
                </a>
            @else
                <span class="bg-line text-muted px-5 py-3 rounded-[11px] text-[14px] font-bold cursor-not-allowed flex items-center gap-1.5" title="Limite de comptes atteinte">
                    <x-icon name="plus" size="16" /> Inviter un membre
                </span>
            @endif
        @endif
    </div>

    {{-- Barre d'usage --}}
    <div class="bg-white border border-line rounded-[14px] px-6 py-5 mb-6 flex items-center gap-6 flex-wrap">
        <div class="flex-1 min-w-[200px]">
            <div class="text-[13.5px] font-bold mb-2">
                @if($illimite)
                    {{ $nbActuels }} compte{{ $nbActuels > 1 ? 's' : '' }} utilisé{{ $nbActuels > 1 ? 's' : '' }}
                @else
                    {{ $nbActuels }} compte{{ $nbActuels > 1 ? 's' : '' }} utilisé{{ $nbActuels > 1 ? 's' : '' }} sur {{ $limiteMax }}
                @endif
            </div>
            <div class="h-2 bg-paper-dim rounded-full overflow-hidden max-w-[280px]">
                <div class="h-full rounded-full {{ !$illimite && $nbActuels >= $limiteMax ? 'bg-amber' : 'bg-teal' }}" style="width: {{ $pct }}%"></div>
            </div>
        </div>
        <div class="text-right">
            <div class="font-display font-semibold text-[20px] leading-none">
                {{ $nbActuels }}<span class="text-[13px] text-muted font-body font-normal">/{{ $illimite ? '∞' : $limiteMax }}</span>
            </div>
            @if(!$illimite && !$peutAjouter)
                <a href="{{ route('subscription.upgrade-required') }}" class="text-[12.5px] font-bold text-gold hover:underline mt-1 inline-block">Passer au plan supérieur →</a>
            @else
                <div class="text-[12px] text-muted mt-1">Comptes collaborateurs</div>
            @endif
        </div>
    </div>

    {{-- Message limite atteinte --}}
    @if($peutGerer && !$peutAjouter && !$illimite)
        <div class="mb-5 rounded-lg bg-gold/10 border border-gold/25 px-4 py-3 text-[13px] text-gold flex items-center gap-2">
            <x-icon name="info" size="16" /> Vous avez atteint la limite de comptes de votre plan. Passez à un plan supérieur pour inviter davantage de collaborateurs.
        </div>
    @endif

    {{-- Liste --}}
    <div class="space-y-3">
        @foreach($collaborateurs as $membre)
            @php
                $b = $badge[$membre->preset_key] ?? $badge['personnalise'];
                $cliquable = $peutGerer && ! $membre->is_owner;
            @endphp
            <a @if($cliquable) href="{{ route('admin.equipe.permissions', $membre) }}" @endif
               @class([
                   'bg-white border border-line rounded-[14px] px-5 py-4 flex items-center gap-4',
                   'hover:shadow-md hover:-translate-y-px transition' => $cliquable,
                   'cursor-default' => ! $cliquable,
               ])>
                <div class="w-[46px] h-[46px] rounded-xl bg-teal text-white flex items-center justify-center font-bold text-[15px] shrink-0">{{ $initiales($membre->name) }}</div>
                <div class="flex-1 min-w-0">
                    <div class="font-bold text-[15px] flex items-center gap-2">
                        {{ $membre->name }}
                        @if($membre->is_owner)<span class="text-[10.5px] font-bold px-2 py-0.5 rounded-full bg-teal-deep text-paper">Directeur</span>@endif
                    </div>
                    <div class="text-[12.5px] text-muted mt-0.5 truncate">{{ $membre->email }}@if($membre->telephone) · {{ $membre->telephone }}@endif</div>
                </div>
                <span class="text-[11.5px] font-bold px-3 py-1.5 rounded-full whitespace-nowrap {{ $b[0] }}">{{ $b[1] }}</span>
                @if($membre->est_en_attente)
                    <span class="text-[11px] font-bold px-2.5 py-1 rounded-full bg-amber/15 text-amber whitespace-nowrap">En attente</span>
                @else
                    <span class="text-[11px] font-bold px-2.5 py-1 rounded-full bg-green/12 text-green whitespace-nowrap">Actif</span>
                @endif
                @if($cliquable)<x-icon name="arrow-up" size="15" class="text-muted rotate-90" />@endif
            </a>
        @endforeach
    </div>

</div>
@endsection
