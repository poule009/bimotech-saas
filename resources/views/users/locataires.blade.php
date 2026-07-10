@extends('layouts.app')

@section('title', 'Locataires')
@section('page-title', 'Locataires')
@section('page-subtitle', $stats['total'] . ' locataire' . ($stats['total'] > 1 ? 's' : ''))

@section('topbar-actions')
    <a href="{{ route('admin.users.create', 'locataire') }}"
       class="hidden sm:inline-flex items-center gap-1.5 bg-teal text-paper px-4 py-2.5 rounded-[11px] text-[13.5px] font-bold hover:bg-teal-deep transition-colors">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
        Nouveau locataire
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
            @foreach(['type','retard'] as $keep)@if(request($keep))<input type="hidden" name="{{ $keep }}" value="{{ request($keep) }}">@endif @endforeach
            <div class="flex items-center gap-2.5 bg-white border border-line rounded-[11px] px-4 py-2.5">
                <svg class="w-4 h-4 text-muted shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Rechercher un nom, un téléphone…"
                       class="w-full bg-transparent outline-none text-[14px] text-ink placeholder:text-muted">
            </div>
        </form>

        <div class="flex items-center gap-2 flex-wrap">
            @php
                $q = request('q');
                $chips = [
                    ['Tous', [], ! request('type') && ! request('retard')],
                    ['Particuliers', ['type' => 'particulier'], request('type') === 'particulier'],
                    ['Bureaux', ['type' => 'entreprise'], request('type') === 'entreprise'],
                ];
            @endphp
            @foreach($chips as [$label, $params, $active])
                <a href="{{ route('admin.users.locataires', array_filter(array_merge(['q' => $q], $params))) }}"
                   @class([
                       'text-[13px] font-bold rounded-full px-4 py-2 border transition-colors',
                       'bg-teal text-paper border-teal' => $active,
                       'bg-white text-muted border-line hover:border-teal' => ! $active,
                   ])>{{ $label }}</a>
            @endforeach
            <a href="{{ route('admin.users.locataires', array_filter(['q' => $q, 'retard' => '1'])) }}"
               @class([
                   'text-[13px] font-bold rounded-full px-4 py-2 border flex items-center gap-1.5 transition-colors',
                   'bg-error text-white border-error' => request('retard'),
                   'bg-white text-error border-line hover:border-error' => ! request('retard'),
               ])><span class="w-1.5 h-1.5 rounded-full bg-current"></span> En retard <span class="opacity-80 text-[11px]">{{ $stats['en_retard'] }}</span></a>
        </div>
    </div>

    @if($locataires->isEmpty())
        <div class="bg-white border border-line rounded-2xl py-16 px-6 text-center">
            <div class="font-display font-semibold text-[16px] mb-1.5">Aucun locataire</div>
            <p class="text-[13.5px] text-muted mb-5">
                @if(request('q') || request('type') || request('retard'))Aucun locataire ne correspond à votre recherche.@else Les locataires se créent généralement à la signature d'un contrat.@endif
            </p>
            @if(request('q') || request('type') || request('retard'))
                <a href="{{ route('admin.users.locataires') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-line text-ink/70 font-bold text-[13px] rounded-[10px] hover:border-teal transition-colors">Effacer les filtres</a>
            @endif
        </div>
    @else
        <div class="bg-white border border-line rounded-2xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-paper-dim border-b border-line text-left text-[12px] uppercase tracking-wide text-muted font-bold">
                            <th class="px-5 py-4">Locataire</th><th class="px-5 py-4">Type</th><th class="px-5 py-4">Bien loué</th><th class="px-5 py-4">Paiement</th><th class="px-5 py-4"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($locataires as $u)
                            @php $entreprise = (bool) ($u->locataire?->est_entreprise); @endphp
                            <tr class="border-b border-paper-dim last:border-0 hover:bg-[#FBF9F3] transition-colors">
                                <td class="px-5 py-4">
                                    <a href="{{ route('admin.users.show', $u) }}" class="flex items-center gap-3 group">
                                        <span @class(['w-[42px] h-[42px] rounded-[11px] flex items-center justify-center font-bold text-[14px] shrink-0','bg-gold text-teal-deep'=>$entreprise,'bg-teal text-paper'=>!$entreprise])>{{ mb_strtoupper(mb_substr($u->name, 0, 2)) }}</span>
                                        <span class="min-w-0"><span class="block font-bold text-[15px] truncate group-hover:text-teal">{{ $u->name }}</span><span class="block text-[12.5px] text-muted truncate">{{ $u->telephone ?? $u->email ?? '—' }}</span></span>
                                    </a>
                                </td>
                                <td class="px-5 py-4">
                                    @if($entreprise)<span class="text-[12px] font-bold px-3 py-1.5 rounded-full bg-gold/15 text-gold">Bureau</span>
                                    @else<span class="text-[12px] font-bold px-3 py-1.5 rounded-full bg-green/10 text-green">Particulier</span>@endif
                                </td>
                                <td class="px-5 py-4 text-[14px] text-ink/80">{{ $u->pay_bien ?? '—' }}</td>
                                <td class="px-5 py-4">
                                    @if($u->pay_status === 'retard')
                                        <span class="inline-flex items-center gap-1.5 text-[12px] font-bold px-3 py-1.5 rounded-full bg-error/10 text-error"><span class="w-[7px] h-[7px] rounded-full bg-current"></span> Retard {{ $u->pay_jours }}j</span>
                                    @elseif($u->pay_status === 'ok')
                                        <span class="inline-flex items-center gap-1.5 text-[12px] font-bold px-3 py-1.5 rounded-full bg-green/10 text-green"><span class="w-[7px] h-[7px] rounded-full bg-current"></span> À jour</span>
                                    @else
                                        <span class="text-[12px] font-bold px-3 py-1.5 rounded-full bg-paper-dim text-muted">Sans contrat</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <a href="{{ route('admin.users.show', $u) }}" class="text-[13px] font-bold text-teal hover:underline">Ouvrir</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @if($locataires->hasPages())<div class="mt-6">{{ $locataires->links() }}</div>@endif
    @endif
</div>
@endsection
