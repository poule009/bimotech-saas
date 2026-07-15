@extends('layouts.superadmin')

@section('title', 'Agences')

@php
    $fmt = fn ($n) => number_format((float) $n, 0, ',', ' ');

    // Initiales de l'avatar agence (2 lettres max).
    $initials = fn ($name) => \Illuminate\Support\Str::upper(
        \Illuminate\Support\Str::of($name)->explode(' ')->filter()
            ->map(fn ($w) => \Illuminate\Support\Str::substr($w, 0, 1))->take(2)->implode('')
    );

    // Libellé relatif d'une dernière activité (« Aujourd'hui » / « Hier » / « 9 juil. »).
    $activiteStr = function (?\Carbon\Carbon $d) {
        if (! $d) return '—';
        if ($d->isToday()) return "Aujourd'hui";
        if ($d->isYesterday()) return 'Hier';
        return $d->locale('fr')->isoFormat('D MMM');
    };

    // Styles de badge de statut par variante.
    $chip = [
        'green' => 'bg-green/10 text-green',
        'gold'  => 'bg-gold/10 text-gold',
        'red'   => 'bg-error/10 text-error',
    ];

    $statutOptions = ['tous' => 'Statut : Tous', 'actif' => 'Actifs', 'essai' => 'En essai', 'suspendu' => 'Suspendus'];
    $planOptions   = ['tous' => 'Plan : Tous', 'starter' => 'Starter', 'pro' => 'Pro', 'agence' => 'Agence', 'legacy' => 'Legacy'];
@endphp

@section('content')
<div class="max-w-[1240px] mx-auto">

    {{-- ─────────── En-tête ─────────── --}}
    <div class="flex items-start justify-between gap-4 mb-1">
        <div>
            <div class="text-[12px] font-semibold tracking-[0.12em] uppercase text-gold">Gestion plateforme</div>
            <h1 class="font-display font-medium text-[30px] md:text-[32px] text-ink mt-1">Agences</h1>
        </div>
        <a href="{{ route('superadmin.agencies.create') }}"
           class="hidden sm:flex items-center gap-2 bg-teal text-paper text-[13.5px] font-semibold px-4 py-2.5 rounded-lg hover:bg-teal-deep transition-colors shrink-0">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
            Ajouter une agence
        </a>
    </div>
    <p class="text-[14.5px] text-muted mb-6">
        {{ $fmt($repartition['actif'] ?? 0) }} active{{ ($repartition['actif'] ?? 0) > 1 ? 's' : '' }},
        {{ $fmt($repartition['essai'] ?? 0) }} en essai,
        {{ $fmt($repartition['suspendu'] ?? 0) }} suspendue{{ ($repartition['suspendu'] ?? 0) > 1 ? 's' : '' }}.
    </p>

    {{-- ─────────── Barre de recherche + filtres ─────────── --}}
    <form method="GET" action="{{ route('superadmin.agencies.index') }}" x-data="agencyFilters"
          class="flex items-center gap-3 mb-4 flex-wrap">
        <label class="flex items-center gap-2 bg-white border border-line rounded-lg px-3 py-2.5 flex-1 min-w-[220px] max-w-[340px]">
            <svg class="w-4 h-4 text-muted shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
            <input type="text" name="q" value="{{ $q }}" x-on:input="search"
                   placeholder="Rechercher une agence…" autocomplete="off"
                   class="border-0 outline-none bg-transparent text-[13.5px] w-full text-ink placeholder:text-muted">
        </label>

        <select name="statut" x-on:change="apply"
                class="bg-white border border-line rounded-lg px-3 py-2.5 text-[13px] font-medium text-ink cursor-pointer">
            @foreach($statutOptions as $val => $label)
                <option value="{{ $val }}" @selected($statutFiltre === $val)>{{ $label }}</option>
            @endforeach
        </select>

        <select name="plan" x-on:change="apply"
                class="bg-white border border-line rounded-lg px-3 py-2.5 text-[13px] font-medium text-ink cursor-pointer">
            @foreach($planOptions as $val => $label)
                <option value="{{ $val }}" @selected($planFiltre === $val)>{{ $label }}</option>
            @endforeach
        </select>

        <div class="flex-1"></div>
        <span class="text-[12.5px] text-muted font-medium whitespace-nowrap">{{ $fmt($total) }} agences au total</span>
        <noscript><button type="submit" class="text-[13px] font-semibold text-teal">Filtrer</button></noscript>
    </form>

    {{-- ─────────── Tableau ─────────── --}}
    <section class="bg-white border border-line rounded-xl overflow-hidden">
        @if($paginator->isEmpty())
            <div class="px-5 py-14 text-center text-[13.5px] text-muted">
                Aucune agence ne correspond à ces critères.
            </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="text-left text-[11px] uppercase tracking-wide text-muted font-semibold bg-paper-dim">
                        <th class="px-4 py-3">Agence</th>
                        <th class="px-4 py-3">Plan</th>
                        <th class="px-4 py-3">Statut</th>
                        <th class="px-4 py-3">Usage</th>
                        <th class="px-4 py-3">MRR</th>
                        <th class="px-4 py-3 whitespace-nowrap">Inscrite le</th>
                        <th class="px-4 py-3 whitespace-nowrap">Dernière activité</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($paginator as $r)
                        @php
                            $a = $r['agency'];
                            $bord = $loop->last ? '' : 'border-b border-paper-dim';
                            $limite = $r['limite'];
                            $pct = ($limite && $limite > 0) ? min(100, (int) round($r['nb_unites'] / $limite * 100)) : 0;
                            $usageHigh = $pct >= 80;
                        @endphp
                        <tr class="relative text-[13.8px] hover:bg-paper/60 transition-colors">
                            {{-- Agence --}}
                            <td class="px-4 py-3.5 {{ $bord }}">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-[9px] bg-teal-deep text-gold-soft flex items-center justify-center font-display font-semibold text-[13px] shrink-0">{{ $initials($a->name) }}</div>
                                    <div class="min-w-0">
                                        <div class="font-semibold text-ink truncate">{{ $a->name }}</div>
                                        <div class="text-[12px] text-muted truncate">{{ $a->adresse ?: $a->email }}</div>
                                    </div>
                                </div>
                            </td>
                            {{-- Plan --}}
                            <td class="px-4 py-3.5 {{ $bord }}">
                                <span class="text-[11.5px] font-semibold px-2.5 py-1 rounded-full bg-paper-dim text-teal-deep inline-block">{{ $r['plan'] }}</span>
                            </td>
                            {{-- Statut --}}
                            <td class="px-4 py-3.5 {{ $bord }}">
                                <span class="text-[11.5px] font-semibold px-2.5 py-1 rounded-full inline-flex items-center gap-1.5 {{ $chip[$r['statut']['variant']] }}">
                                    <span class="w-1.5 h-1.5 rounded-full bg-current"></span>{{ $r['statut']['label'] }}
                                </span>
                            </td>
                            {{-- Usage --}}
                            <td class="px-4 py-3.5 {{ $bord }}">
                                @if($limite === null)
                                    <span class="text-[12px] text-muted font-medium tabular-nums">Illimité</span>
                                @else
                                    <div class="flex items-center gap-2 min-w-[110px]">
                                        <div class="flex-1 h-1.5 bg-paper-dim rounded-full overflow-hidden">
                                            <div class="h-full rounded-full {{ $usageHigh ? 'bg-gold' : 'bg-teal' }}" style="width: {{ $pct }}%"></div>
                                        </div>
                                        <span class="text-[11.5px] text-muted tabular-nums whitespace-nowrap">{{ $r['nb_unites'] }}/{{ $limite }}</span>
                                    </div>
                                @endif
                            </td>
                            {{-- MRR --}}
                            <td class="px-4 py-3.5 {{ $bord }}">
                                <span class="font-semibold tabular-nums text-[13px] {{ $r['mrr'] > 0 ? 'text-ink' : 'text-muted' }}">{{ $r['mrr'] > 0 ? $fmt($r['mrr']) : '—' }}</span>
                            </td>
                            {{-- Inscrite le --}}
                            <td class="px-4 py-3.5 {{ $bord }} text-muted whitespace-nowrap">{{ $r['inscrite']?->locale('fr')->isoFormat('D MMM Y') ?? '—' }}</td>
                            {{-- Dernière activité --}}
                            <td class="px-4 py-3.5 {{ $bord }} text-muted whitespace-nowrap">{{ $activiteStr($r['derniere']) }}</td>
                            {{-- Action (lien étiré : toute la ligne mène à la fiche) --}}
                            <td class="px-4 py-3.5 {{ $bord }} text-right">
                                <a href="{{ route('superadmin.agencies.show', $a) }}"
                                   class="text-[12px] font-semibold text-teal border-b border-gold pb-px before:content-[''] before:absolute before:inset-0">Gérer</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($paginator->hasPages())
            <div class="flex items-center justify-between gap-4 px-5 py-3.5 border-t border-paper-dim text-[12.5px] text-muted flex-wrap">
                <span>Affichage de {{ $fmt($paginator->firstItem()) }} à {{ $fmt($paginator->lastItem()) }} sur {{ $fmt($paginator->total()) }} agences</span>
                <div class="flex items-center gap-1.5">
                    @if($paginator->onFirstPage())
                        <span class="w-8 h-8 rounded-lg border border-line flex items-center justify-center text-muted/40">‹</span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" class="w-8 h-8 rounded-lg border border-line bg-white flex items-center justify-center font-semibold hover:border-gold">‹</a>
                    @endif
                    @foreach($paginator->getUrlRange(1, $paginator->lastPage()) as $page => $url)
                        @if($page == $paginator->currentPage())
                            <span class="w-8 h-8 rounded-lg bg-teal-deep text-paper flex items-center justify-center font-semibold">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="w-8 h-8 rounded-lg border border-line bg-white flex items-center justify-center font-semibold hover:border-gold">{{ $page }}</a>
                        @endif
                    @endforeach
                    @if($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" class="w-8 h-8 rounded-lg border border-line bg-white flex items-center justify-center font-semibold hover:border-gold">›</a>
                    @else
                        <span class="w-8 h-8 rounded-lg border border-line flex items-center justify-center text-muted/40">›</span>
                    @endif
                </div>
            </div>
        @endif
        @endif
    </section>
</div>
@endsection
