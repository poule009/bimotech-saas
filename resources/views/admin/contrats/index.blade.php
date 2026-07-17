@extends('layouts.app')

@php
    $fmt = fn ($n) => number_format((float) $n, 0, ',', ' ');
    $echeanceInfo = function ($contrat) {
        if ($contrat->statut === 'resilié') return ['Résilié', 'bg-paper-dim text-muted'];
        if ($contrat->statut === 'expiré')  return ['Expiré', 'bg-error/10 text-error'];
        if ($contrat->date_fin) {
            $j = (int) now()->startOfDay()->diffInDays(\Carbon\Carbon::parse($contrat->date_fin)->startOfDay(), false);
            if ($j >= 0 && $j <= 30) return ['Échéance ' . $j . 'j', 'bg-gold/15 text-gold'];
        }
        return ['Actif', 'bg-green/10 text-green'];
    };
@endphp

@section('title', 'Contrats')
@section('page-title', 'Contrats')
@section('page-subtitle', $stats['total'] . ' contrat' . ($stats['total'] > 1 ? 's' : ''))

@section('topbar-actions')
    <a href="{{ route('admin.contrats.create') }}"
       class="hidden sm:inline-flex items-center gap-1.5 bg-teal text-paper px-4 py-2.5 rounded-[11px] text-[13.5px] font-bold hover:bg-teal-deep transition-colors">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
        Nouveau contrat
    </a>
@endsection

@section('content')
<div class="max-w-[1180px]">

    @if(session('success'))
        <div class="mb-5 rounded-lg bg-green/10 border border-green/25 px-4 py-3 text-[13px] text-green">{{ session('success') }}</div>
    @endif

    {{-- Barre d'outils --}}
    <div class="flex flex-col sm:flex-row sm:items-center gap-3 mb-5">
        <form method="GET" class="flex-1 max-w-[340px]">
            @foreach(['filter','echeance'] as $k)@if(request($k))<input type="hidden" name="{{ $k }}" value="{{ request($k) }}">@endif @endforeach
            <div class="flex items-center gap-2.5 bg-white border border-line rounded-[11px] px-4 py-2.5">
                <svg class="w-4 h-4 text-muted shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Rechercher un bien, un locataire…"
                       class="w-full bg-transparent outline-none text-[14px] text-ink placeholder:text-muted">
            </div>
        </form>
        <div class="flex items-center gap-2 flex-wrap">
            @php $q = request('q'); @endphp
            <a href="{{ route('admin.contrats.index', array_filter(['q' => $q])) }}"
               @class(['text-[13px] font-bold rounded-full px-4 py-2 border transition-colors','bg-teal text-paper border-teal'=>!request('filter')&&!request('echeance'),'bg-white text-muted border-line hover:border-teal'=>request('filter')||request('echeance')])>Tous</a>
            <a href="{{ route('admin.contrats.index', array_filter(['q' => $q, 'filter' => 'actifs'])) }}"
               @class(['text-[13px] font-bold rounded-full px-4 py-2 border transition-colors','bg-teal text-paper border-teal'=>request('filter')==='actifs','bg-white text-muted border-line hover:border-teal'=>request('filter')!=='actifs'])>Actifs</a>
            <a href="{{ route('admin.contrats.index', array_filter(['q' => $q, 'echeance' => '1'])) }}"
               @class(['text-[13px] font-bold rounded-full px-4 py-2 border flex items-center gap-1.5 transition-colors','bg-gold text-teal-deep border-gold'=>request('echeance'),'bg-white text-muted border-line hover:border-gold'=>!request('echeance')])>⏱ Bientôt échus <span class="opacity-80 text-[11px]">{{ $stats['bientot'] }}</span></a>
        </div>
    </div>

    @if($contrats->isEmpty())
        <div class="bg-white border border-line rounded-2xl py-16 px-6 text-center">
            <div class="font-display font-semibold text-[16px] mb-1.5">Aucun contrat</div>
            <p class="text-[13.5px] text-muted mb-5">@if(request('q')||request('filter')||request('echeance'))Aucun contrat ne correspond à votre recherche.@else Créez le premier contrat de votre agence.@endif</p>
            @if(request('q')||request('filter')||request('echeance'))
                <a href="{{ route('admin.contrats.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-line text-ink/70 font-bold text-[13px] rounded-[10px] hover:border-teal transition-colors">Effacer les filtres</a>
            @else
                <a href="{{ route('admin.contrats.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-teal text-paper font-bold text-[13px] rounded-[10px] hover:bg-teal-deep transition-colors">+ Nouveau contrat</a>
            @endif
        </div>
    @else
        <div class="bg-white border border-line rounded-2xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-paper-dim border-b border-line text-left text-[12px] uppercase tracking-wide text-muted font-bold">
                            <th class="px-5 py-4">Bien</th><th class="px-5 py-4">Locataire</th><th class="px-5 py-4">Loyer</th><th class="px-5 py-4">Échéance</th><th class="px-5 py-4">Statut</th><th class="px-5 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($contrats as $contrat)
                            @php [$stLabel, $stClass] = $echeanceInfo($contrat); @endphp
                            <tr class="border-b border-paper-dim last:border-0 hover:bg-[#FBF9F3] transition-colors">
                                <td class="px-5 py-4">
                                    <a href="{{ route('admin.contrats.show', $contrat) }}" class="flex items-center gap-3 group">
                                        <span class="w-[42px] h-[42px] rounded-[11px] bg-teal text-paper flex items-center justify-center shrink-0">
                                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21h18M5 21V7l7-4 7 4v14M10 21v-5h4v5"/></svg>
                                        </span>
                                        <span class="min-w-0"><span class="block font-bold text-[15px] truncate group-hover:text-teal">{{ $contrat->bien->reference ?? '—' }}</span><span class="block text-[12.5px] text-muted truncate">{{ $contrat->bien->ville ?? '' }}</span></span>
                                    </a>
                                </td>
                                <td class="px-5 py-4 text-[14px] text-ink/80">{{ $contrat->locataire->name ?? '—' }}</td>
                                <td class="px-5 py-4 text-[14px] font-semibold">{{ $fmt($contrat->loyer_contractuel) }} F</td>
                                <td class="px-5 py-4 text-[14px] text-ink/80">{{ $contrat->date_fin ? \Carbon\Carbon::parse($contrat->date_fin)->locale('fr')->isoFormat('D MMM Y') : 'Indéterminée' }}</td>
                                <td class="px-5 py-4"><span class="inline-flex items-center gap-1.5 text-[12px] font-bold px-3 py-1.5 rounded-full {{ $stClass }}"><span class="w-[7px] h-[7px] rounded-full bg-current"></span> {{ $stLabel }}</span></td>
                                <td class="px-5 py-4">
                                    <x-row-actions :show="route('admin.contrats.show', $contrat)" :edit="route('admin.contrats.edit', $contrat)" />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @if($contrats->hasPages())<div class="mt-6">{{ $contrats->links() }}</div>@endif
    @endif
</div>
@endsection
