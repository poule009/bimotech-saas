@extends('layouts.superadmin')

@section('title', 'Équipe interne')

@php
    $fmt = fn ($n) => number_format((float) $n, 0, ',', ' ');

    $tempsRelatif = function (?\Carbon\Carbon $d) {
        if (! $d) return 'Jamais connecté';
        if ($d->isToday())     return "Aujourd'hui, ".$d->format('H:i');
        if ($d->isYesterday()) return 'Hier, '.$d->format('H:i');
        return $d->locale('fr')->isoFormat('D MMM Y');
    };
@endphp

@section('content')
<div class="max-w-[1000px] mx-auto">

    @if(session('success'))
        <div class="mb-5 rounded-lg bg-green/10 border border-green/25 px-4 py-3 text-[13px] text-green">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-5 rounded-lg bg-error/10 border border-error/25 px-4 py-3 text-[13px] text-error">{{ session('error') }}</div>
    @endif

    {{-- ─────────── En-tête ─────────── --}}
    <div class="flex items-start justify-between gap-4 mb-1">
        <div>
            <div class="text-[12px] font-semibold tracking-[0.12em] uppercase text-gold">Gestion plateforme</div>
            <h1 class="font-display font-medium text-[30px] md:text-[32px] text-ink mt-1">Équipe interne</h1>
        </div>
        <a href="{{ route('superadmin.equipe.create') }}"
           class="shrink-0 text-[13.5px] font-semibold px-4 py-2.5 rounded-lg bg-teal text-paper hover:bg-teal-deep transition-colors whitespace-nowrap">
            + Inviter un collaborateur
        </a>
    </div>
    <p class="text-[14.5px] text-muted mb-7">Les comptes ayant accès au Super Admin, et ce qu'ils peuvent voir.</p>

    {{-- ─────────── Admin principal ─────────── --}}
    <div class="bg-white border border-line rounded-2xl p-5 md:px-6 mb-4">
        <div class="flex items-center gap-4 flex-wrap">
            <div class="w-12 h-12 rounded-xl bg-teal-deep text-gold-soft flex items-center justify-center font-display font-semibold text-[17px] shrink-0">
                {{ \Illuminate\Support\Str::of($principal->name)->substr(0, 1)->upper() }}
            </div>
            <div class="flex-1 min-w-0">
                <div class="font-display font-medium text-[18px] text-ink">{{ $principal->name }}</div>
                <div class="text-[12.5px] text-muted mt-0.5">{{ $principal->email }}</div>
            </div>
            <span class="text-[11px] font-bold uppercase tracking-wide px-2.5 py-1 rounded-full bg-teal-deep text-white">Admin principal</span>
            <div class="text-[12px] text-muted">Accès total à la plateforme</div>
        </div>
    </div>

    {{-- ─────────── Collaborateurs ─────────── --}}
    @forelse($collaborateurs as $row)
        @php $c = $row['user']; @endphp
        <div class="bg-white border rounded-2xl p-5 md:px-6 mb-4 {{ $row['revoque'] ? 'border-error/30' : 'border-line' }}">

            {{-- En-tête de la carte --}}
            <div class="flex items-center gap-4 flex-wrap">
                <div class="w-12 h-12 rounded-xl bg-teal-deep text-gold-soft flex items-center justify-center font-display font-semibold text-[17px] shrink-0 {{ $row['revoque'] ? 'opacity-60' : '' }}">
                    {{ \Illuminate\Support\Str::of($c->name)->substr(0, 1)->upper() }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="font-display font-medium text-[18px] text-ink">{{ $c->name }}</div>
                    <div class="text-[12.5px] text-muted mt-0.5">{{ $c->email }}</div>
                </div>
                @if($row['revoque'])
                    <span class="text-[11px] font-bold uppercase tracking-wide px-2.5 py-1 rounded-full bg-error/10 text-error">Accès révoqué</span>
                @else
                    <span class="text-[11px] font-bold uppercase tracking-wide px-2.5 py-1 rounded-full bg-gold/15 text-gold-deep">Accès restreint</span>
                @endif
                <div class="text-[12px] text-muted text-right min-w-[130px]">
                    {{ $row['revoque'] ? 'Connexion coupée' : 'Dernière activité : '.$tempsRelatif($row['derniere']) }}
                </div>
            </div>

            {{-- Actions (3 actions distinctes) --}}
            <div class="flex flex-wrap gap-2 mt-4">
                @if(! $row['revoque'])
                    <form method="POST" action="{{ route('superadmin.equipe.voir-comme', $c) }}">
                        @csrf
                        <button type="submit" class="text-[12.5px] font-semibold px-3.5 py-2 rounded-lg border border-gold text-gold-deep hover:bg-gold/10 transition-colors flex items-center gap-1.5">
                            <svg class="w-[15px] h-[15px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                            Voir comme {{ \Illuminate\Support\Str::of($c->name)->explode(' ')->first() }}
                        </button>
                    </form>
                @endif
                <a href="{{ route('superadmin.equipe.reattribuer.edit', $c) }}"
                   class="text-[12.5px] font-semibold px-3.5 py-2 rounded-lg border border-line text-ink hover:bg-paper-dim transition-colors">
                    Réattribuer ses agences
                </a>
                @if($row['revoque'])
                    <form method="POST" action="{{ route('superadmin.equipe.restaurer', $c) }}">
                        @csrf
                        <button type="submit" class="text-[12.5px] font-semibold px-3.5 py-2 rounded-lg border border-green/40 text-green hover:bg-green/10 transition-colors">
                            Rétablir l'accès
                        </button>
                    </form>
                @else
                    <form method="POST" action="{{ route('superadmin.equipe.revoquer', $c) }}"
                          x-data="confirmForm" x-on:submit="submit"
                          data-confirm="Révoquer l'accès de {{ $c->name }} ? Ses agences resteront attribuées (historique de commission).">
                        @csrf
                        <button type="submit" class="text-[12.5px] font-semibold px-3.5 py-2 rounded-lg border border-error/30 text-error hover:bg-error/10 transition-colors">
                            Révoquer l'accès
                        </button>
                    </form>
                @endif
            </div>

            {{-- Permissions (4 toggles) — chaque bascule est un mini-formulaire CSP-safe --}}
            <div class="flex flex-wrap gap-x-6 gap-y-3 mt-5">
                @foreach($permissionsMeta as $cle => $label)
                    @php $on = $c->saPermission($cle); @endphp
                    <form method="POST" action="{{ route('superadmin.equipe.permission', $c) }}" class="flex items-center gap-2.5">
                        @csrf
                        <input type="hidden" name="permission" value="{{ $cle }}">
                        <button type="submit" aria-pressed="{{ $on ? 'true' : 'false' }}"
                                class="relative w-[34px] h-[19px] rounded-full shrink-0 transition-colors {{ $on ? 'bg-green' : 'bg-paper-dim' }}">
                            <span class="absolute top-[2px] w-[15px] h-[15px] rounded-full bg-white transition-all {{ $on ? 'left-[17px]' : 'left-[2px]' }}"></span>
                        </button>
                        <span class="text-[12.5px] text-ink">{{ $label }}</span>
                    </form>
                @endforeach
            </div>

            {{-- Chiffres + taux + lien historique --}}
            <div class="flex flex-wrap items-end gap-x-8 gap-y-4 mt-5 pt-5 border-t border-paper-dim">
                <div>
                    <div class="text-[11px] font-semibold uppercase tracking-wide text-muted mb-1">Agences attribuées</div>
                    <div class="font-mono font-semibold text-[15px] text-ink tabular-nums">{{ $fmt($row['nb_agences']) }}</div>
                </div>
                <div>
                    <div class="text-[11px] font-semibold uppercase tracking-wide text-muted mb-1">MRR total de ses agences</div>
                    <div class="font-mono font-semibold text-[15px] text-ink tabular-nums">{{ $fmt($row['mrr']) }} FCFA</div>
                </div>
                <div>
                    <div class="text-[11px] font-semibold uppercase tracking-wide text-muted mb-1">Commission ({{ rtrim(rtrim(number_format($row['taux'], 2, ',', ''), '0'), ',') }} %) — ce mois</div>
                    <div class="font-mono font-semibold text-[15px] text-gold tabular-nums">{{ $fmt($row['commission']) }} FCFA</div>
                </div>
                <div>
                    <div class="text-[11px] font-semibold uppercase tracking-wide text-muted mb-1">Taux de commission</div>
                    <form method="POST" action="{{ route('superadmin.equipe.taux', $c) }}" class="flex items-center gap-1.5">
                        @csrf
                        @method('PATCH')
                        <input type="number" step="0.01" min="0" max="100" name="taux_commission"
                               value="{{ rtrim(rtrim(number_format($row['taux'], 2, '.', ''), '0'), '.') }}"
                               class="w-[68px] bg-paper border border-line rounded-lg px-2 py-1.5 text-[13px] font-medium text-ink tabular-nums">
                        <span class="text-[12.5px] text-muted">%</span>
                        <button type="submit" class="text-[12px] font-semibold text-teal border-b border-gold pb-px">OK</button>
                    </form>
                </div>
                <div class="flex-1 flex justify-end min-w-[180px]">
                    <a href="{{ route('superadmin.equipe.commissions', $c) }}" class="text-[12.5px] font-semibold text-teal border-b border-gold pb-px">
                        Voir l'historique des commissions →
                    </a>
                </div>
            </div>
        </div>
    @empty
        <div class="bg-white border border-dashed border-line rounded-2xl px-6 py-8 text-center text-[13.5px] text-muted">
            Pas d'autre collaborateur pour l'instant — cliquez sur « Inviter un collaborateur » quand vous en aurez besoin.
        </div>
    @endforelse

    {{-- Note explicative révocation vs réattribution --}}
    <p class="text-[12px] text-muted mt-4 leading-relaxed flex gap-2">
        <span class="text-gold shrink-0">ℹ</span>
        Révoquer l'accès coupe uniquement la connexion — les agences restent attribuées au collaborateur pour l'historique de commission, jusqu'à ce que vous les réattribuiez manuellement.
    </p>
</div>
@endsection
