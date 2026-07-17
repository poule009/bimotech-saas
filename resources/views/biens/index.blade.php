@extends('layouts.app')

@php
    $statutPill = [
        'loue'       => ['Loué', 'bg-green/10 text-green'],
        'disponible' => ['Vacant', 'bg-gold/15 text-gold'],
        'en_travaux' => ['En travaux', 'bg-error/10 text-error'],
        'archive'    => ['Archivé', 'bg-paper-dim text-muted'],
    ];
    $occ = function ($loues, $total) {
        if ($total === 0)        return ['Vide', 'bg-gold/15 text-gold'];
        if ($loues === 0)        return ['Vacant', 'bg-gold/15 text-gold'];
        if ($loues >= $total)    return ['Complet', 'bg-green/10 text-green'];
        return [$loues.'/'.$total.' loués', 'bg-teal/10 text-teal'];
    };
@endphp

@section('title', 'Biens')
@section('page-title', 'Biens')
@section('page-subtitle', $counts['total'] . ' bien' . ($counts['total'] > 1 ? 's' : '') . ' au total')

@section('topbar-actions')
    @if(config('features.immeubles', true))
        <a href="{{ route('admin.immeubles.create') }}"
           class="hidden sm:inline-flex items-center gap-1.5 bg-white border border-line text-ink px-4 py-2.5 rounded-[11px] text-[13.5px] font-bold hover:border-teal transition-colors">
            <x-icon name="building" size="15" /> Immeuble
        </a>
    @endif
    <a href="{{ route('admin.biens.create') }}"
       class="hidden sm:inline-flex items-center gap-1.5 bg-teal text-paper px-4 py-2.5 rounded-[11px] text-[13.5px] font-bold hover:bg-teal-deep transition-colors">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
        Nouveau bien
    </a>
@endsection

@section('content')
<div class="max-w-[1180px]" x-data="viewToggle">

    @if(session('success'))
        <div class="mb-5 rounded-lg bg-green/10 border border-green/25 px-4 py-3 text-[13px] text-green">{{ session('success') }}</div>
    @endif

    {{-- Barre d'outils --}}
    <div class="flex flex-wrap items-center gap-3 mb-6">
        <form method="GET" class="flex-1 min-w-[220px] max-w-[340px]">
            @if($filter)<input type="hidden" name="filter" value="{{ $filter }}">@endif
            <div class="flex items-center gap-2.5 bg-white border border-line rounded-[11px] px-4 py-2.5">
                <svg class="w-4 h-4 text-muted shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                <input type="text" name="q" value="{{ $q }}" placeholder="Rechercher un bien, une adresse…"
                       class="w-full bg-transparent outline-none text-[14px] text-ink placeholder:text-muted">
            </div>
        </form>

        @php
            $chips = ['Tous' => null, 'Biens simples' => 'simples', 'Immeubles' => 'immeubles'];
            $chipCounts = ['Tous' => $counts['total'], 'Biens simples' => $counts['simples'], 'Immeubles' => $counts['immeubles']];
        @endphp
        <div class="flex items-center gap-2 flex-wrap">
            @foreach($chips as $label => $val)
                @php $isActive = $filter === $val || (is_null($val) && ! $filter); @endphp
                <a href="{{ route('admin.biens.index', array_filter(['q' => $q, 'filter' => $val])) }}"
                   @class([
                       'text-[13px] font-bold rounded-full px-4 py-2 border flex items-center gap-1.5 transition-colors',
                       'bg-teal text-paper border-teal' => $isActive,
                       'bg-white text-muted border-line hover:border-teal' => ! $isActive,
                   ])>{{ $label }} <span class="opacity-70 text-[11px]">{{ $chipCounts[$label] }}</span></a>
            @endforeach
        </div>

        {{-- Bascule grille / liste --}}
        <div class="flex gap-0.5 bg-white border border-line rounded-[10px] p-[3px] ml-auto">
            <button type="button" x-on:click="setGrid" x-bind:class="gridBtnClass" class="w-9 h-8 rounded-[7px] flex items-center justify-center transition-colors" aria-label="Vue grille">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
            </button>
            <button type="button" x-on:click="setList" x-bind:class="listBtnClass" class="w-9 h-8 rounded-[7px] flex items-center justify-center transition-colors" aria-label="Vue liste">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/></svg>
            </button>
        </div>
    </div>

    @if($biensSimples->isEmpty() && $immeubles->isEmpty())
        <div class="bg-white border border-line rounded-2xl py-16 px-6 text-center">
            <div class="w-12 h-12 bg-paper-dim rounded-xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6 text-muted" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 21h18M5 21V7l7-4 7 4v14M9 21v-6h6v6"/></svg>
            </div>
            @if($q || $filter)
                <div class="font-display font-semibold text-[16px] mb-1.5">Aucun résultat</div>
                <p class="text-[13.5px] text-muted mb-5">Aucun bien ne correspond à votre recherche.</p>
                <a href="{{ route('admin.biens.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-line text-ink/70 font-bold text-[13px] rounded-[10px] hover:border-teal transition-colors">Effacer les filtres</a>
            @else
                <div class="font-display font-semibold text-[16px] mb-1.5">Aucun bien</div>
                <p class="text-[13.5px] text-muted mb-5">Ajoutez le premier bien de votre agence.</p>
                <a href="{{ route('admin.biens.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-teal text-paper font-bold text-[13px] rounded-[10px] hover:bg-teal-deep transition-colors">+ Nouveau bien</a>
            @endif
        </div>
    @else

    {{-- ══════════ VUE GRILLE ══════════ --}}
    <div x-show="isGrid" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
        {{-- Immeubles --}}
        @foreach($immeubles as $im)
            @php [$occLabel, $occClass] = $occ((int) $im->loues_count, (int) $im->biens_count); @endphp
            <a href="{{ route('admin.immeubles.show', $im) }}" class="bg-white border border-line rounded-2xl overflow-hidden hover:-translate-y-0.5 hover:shadow-md transition-all">
                <div class="h-[120px] bg-teal-deep relative flex items-center justify-center">
                    <svg class="w-9 h-9 text-paper/80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M3 21h18M6 21V4a1 1 0 011-1h10a1 1 0 011 1v17M9 7h1M14 7h1M9 11h1M14 11h1M9 15h1M14 15h1"/></svg>
                    <span class="absolute top-3 left-3 bg-teal-deep/70 text-white text-[11px] font-bold px-2.5 py-1 rounded-full inline-flex items-center gap-1"><x-icon name="building" size="11" /> Immeuble · {{ $im->biens_count }} unité{{ $im->biens_count > 1 ? 's' : '' }}</span>
                    <span class="absolute top-3 right-3 bg-gold text-teal-deep text-[11px] font-extrabold px-2.5 py-1 rounded-full">{{ $occLabel }}</span>
                </div>
                <div class="p-[16px_18px_18px]">
                    <div class="font-bold text-[15.5px] mb-0.5 truncate">{{ $im->nom }}</div>
                    <div class="text-[12.5px] text-muted mb-3 truncate">{{ $im->ville }}</div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-1.5 text-[12px] text-muted min-w-0">
                            <span class="w-5 h-5 rounded bg-paper-dim text-teal flex items-center justify-center text-[9px] font-bold shrink-0">{{ mb_strtoupper(mb_substr($im->proprietaire->name ?? '—', 0, 2)) }}</span>
                            <span class="truncate">{{ $im->proprietaire->name ?? '—' }}</span>
                        </div>
                    </div>
                </div>
            </a>
        @endforeach

        {{-- Biens simples --}}
        @foreach($biensSimples as $bien)
            @php [$stLabel, $stClass] = $statutPill[$bien->statut] ?? [ucfirst($bien->statut), 'bg-paper-dim text-muted']; @endphp
            <a href="{{ route('admin.biens.show', $bien) }}" class="bg-white border border-line rounded-2xl overflow-hidden hover:-translate-y-0.5 hover:shadow-md transition-all">
                @php $cover = $bien->photo_couverture; @endphp
                <div class="h-[150px] relative flex items-center justify-center overflow-hidden {{ $cover ? 'bg-paper-dim' : 'bg-teal' }}">
                    @if($cover)
                        <img src="{{ $cover->url }}" alt="{{ $bien->titre_fallback }}" loading="lazy"
                             class="absolute inset-0 w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/45 via-transparent to-transparent"></div>
                    @else
                        <svg class="w-9 h-9 text-paper/80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M3 21h18M5 21V7l7-4 7 4v14M10 21v-5h4v5"/></svg>
                    @endif
                    <span class="absolute top-3 left-3 bg-teal-deep/70 text-white text-[11px] font-bold px-2.5 py-1 rounded-full backdrop-blur-sm">{{ \App\Models\Bien::TYPES[$bien->type] ?? ucfirst($bien->type) }}</span>
                </div>
                <div class="p-[16px_18px_18px]">
                    <div class="font-bold text-[15.5px] mb-0.5 truncate">{{ $bien->titre_fallback }}</div>
                    <div class="text-[12.5px] text-muted mb-3 truncate">{{ $bien->quartier ? $bien->quartier.', ' : '' }}{{ $bien->ville }}<span class="text-muted/70"> · Réf. {{ $bien->reference }}</span></div>
                    <div class="flex items-center justify-between gap-2">
                        <div class="flex items-center gap-1.5 text-[12px] text-muted min-w-0">
                            <span class="w-5 h-5 rounded bg-paper-dim text-teal flex items-center justify-center text-[9px] font-bold shrink-0">{{ mb_strtoupper(mb_substr($bien->proprietaire->name ?? '—', 0, 2)) }}</span>
                            <span class="truncate">{{ $bien->proprietaire->name ?? '—' }}</span>
                        </div>
                        <span class="text-[11px] font-bold px-2.5 py-1 rounded-full shrink-0 {{ $stClass }}">{{ $stLabel }}</span>
                    </div>
                </div>
            </a>
        @endforeach
    </div>

    {{-- ══════════ VUE LISTE ══════════ --}}
    <div x-show="isList" x-cloak class="bg-white border border-line rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-paper-dim border-b border-line text-left text-[12px] uppercase tracking-wide text-muted font-bold">
                        <th class="px-5 py-4">Bien</th><th class="px-5 py-4">Type</th><th class="px-5 py-4">Propriétaire</th><th class="px-5 py-4">Statut</th><th class="px-5 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($immeubles as $im)
                        @php [$occLabel, $occClass] = $occ((int) $im->loues_count, (int) $im->biens_count); @endphp
                        <tr class="border-b border-paper-dim last:border-0 hover:bg-[#FBF9F3] transition-colors">
                            <td class="px-5 py-4">
                                <a href="{{ route('admin.immeubles.show', $im) }}" class="flex items-center gap-3 group">
                                    <span class="w-[42px] h-[42px] rounded-[10px] bg-teal-deep text-paper flex items-center justify-center shrink-0"><x-icon name="building" size="20" /></span>
                                    <span class="min-w-0"><span class="block font-bold text-[15px] truncate group-hover:text-teal">{{ $im->nom }}</span><span class="block text-[12.5px] text-muted truncate">{{ $im->ville }}</span></span>
                                </a>
                            </td>
                            <td class="px-5 py-4"><span class="text-[12px] font-bold px-3 py-1.5 rounded-full bg-paper-dim text-teal">Immeuble · {{ $im->biens_count }} unité{{ $im->biens_count > 1 ? 's' : '' }}</span></td>
                            <td class="px-5 py-4 text-[14px] text-ink/80">{{ $im->proprietaire->name ?? '—' }}</td>
                            <td class="px-5 py-4"><span class="text-[11px] font-bold px-3 py-1.5 rounded-full {{ $occClass }}">{{ $occLabel }}</span></td>
                            <td class="px-5 py-4">
                                <x-row-actions :show="route('admin.immeubles.show', $im)" :edit="route('admin.immeubles.edit', $im)" />
                            </td>
                        </tr>
                    @endforeach
                    @foreach($biensSimples as $bien)
                        @php [$stLabel, $stClass] = $statutPill[$bien->statut] ?? [ucfirst($bien->statut), 'bg-paper-dim text-muted']; @endphp
                        <tr class="border-b border-paper-dim last:border-0 hover:bg-[#FBF9F3] transition-colors">
                            <td class="px-5 py-4">
                                @php $cover = $bien->photo_couverture; @endphp
                                <a href="{{ route('admin.biens.show', $bien) }}" class="flex items-center gap-3 group">
                                    @if($cover)
                                        <img src="{{ $cover->url }}" alt="" loading="lazy"
                                             class="w-[42px] h-[42px] rounded-[10px] object-cover shrink-0 border border-line">
                                    @else
                                        <span class="w-[42px] h-[42px] rounded-[10px] bg-teal text-paper flex items-center justify-center shrink-0">
                                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21h18M5 21V7l7-4 7 4v14M10 21v-5h4v5"/></svg>
                                        </span>
                                    @endif
                                    <span class="min-w-0"><span class="block font-bold text-[15px] truncate group-hover:text-teal">{{ $bien->titre_fallback }}</span><span class="block text-[12.5px] text-muted truncate">{{ $bien->quartier ? $bien->quartier.', ' : '' }}{{ $bien->ville }}<span class="text-muted/70"> · Réf. {{ $bien->reference }}</span></span></span>
                                </a>
                            </td>
                            <td class="px-5 py-4"><span class="text-[12px] font-bold px-3 py-1.5 rounded-full bg-paper-dim text-teal">{{ \App\Models\Bien::TYPES[$bien->type] ?? ucfirst($bien->type) }}</span></td>
                            <td class="px-5 py-4 text-[14px] text-ink/80">{{ $bien->proprietaire->name ?? '—' }}</td>
                            <td class="px-5 py-4"><span class="text-[11px] font-bold px-3 py-1.5 rounded-full {{ $stClass }}">{{ $stLabel }}</span></td>
                            <td class="px-5 py-4">
                                <x-row-actions :show="route('admin.biens.show', $bien)" :edit="route('admin.biens.edit', $bien)" />
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @if($biensSimples instanceof \Illuminate\Contracts\Pagination\Paginator && $biensSimples->hasPages())
        <div class="mt-6">{{ $biensSimples->links() }}</div>
    @endif
    @endif
</div>
@endsection
