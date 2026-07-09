@extends('layouts.app')

@section('title', 'Propriétaires')
@section('page-title', 'Propriétaires')
@section('page-subtitle', $stats['total'] . ' propriétaire' . ($stats['total'] > 1 ? 's' : ''))

@section('topbar-actions')
    <a href="{{ route('admin.users.create', 'proprietaire') }}"
       class="hidden sm:inline-flex items-center gap-1.5 bg-teal text-paper px-4 py-2.5 rounded-[10px] text-[13.5px] font-bold hover:bg-teal-deep transition-colors">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
        Nouveau propriétaire
    </a>
@endsection

@section('content')
<div class="max-w-[1180px]">

    @if(session('success'))
        <div class="mb-5 rounded-lg bg-green/10 border border-green/25 px-4 py-3 text-[13px] text-green">{{ session('success') }}</div>
    @endif

    {{-- Barre d'outils : recherche + filtres --}}
    <div class="flex flex-col sm:flex-row sm:items-center gap-3 mb-5">
        <form method="GET" class="flex-1 max-w-[360px]">
            @if(request('type'))<input type="hidden" name="type" value="{{ request('type') }}">@endif
            <div class="flex items-center gap-2.5 bg-white border border-line rounded-[11px] px-4 py-2.5">
                <svg class="w-4 h-4 text-muted shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                <input type="text" name="q" value="{{ request('q') }}"
                       placeholder="Rechercher un nom, un téléphone, un NINEA…"
                       class="w-full bg-transparent outline-none text-[14px] text-ink placeholder:text-muted">
            </div>
        </form>

        @php
            $chips = [
                'Tous'         => null,
                'Particuliers' => 'particulier',
                'Entreprises'  => 'entreprise',
            ];
        @endphp
        <div class="flex items-center gap-2">
            @foreach($chips as $label => $val)
                @php $isActive = request('type') === $val || (is_null($val) && ! request('type')); @endphp
                <a href="{{ route('admin.users.proprietaires', array_filter(['q' => request('q'), 'type' => $val])) }}"
                   @class([
                       'text-[13px] font-bold rounded-full px-4 py-2 border transition-colors',
                       'bg-teal text-paper border-teal' => $isActive,
                       'bg-white text-muted border-line hover:border-teal' => ! $isActive,
                   ])>{{ $label }}</a>
            @endforeach
        </div>
    </div>

    {{-- Tableau --}}
    @if($proprietaires->isEmpty())
        <div class="bg-white border border-line rounded-xl py-16 px-6 text-center">
            <div class="w-12 h-12 bg-paper-dim rounded-xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6 text-muted" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            </div>
            @if(request('q') || request('type'))
                <div class="font-display font-semibold text-[16px] mb-1.5">Aucun résultat</div>
                <p class="text-[13.5px] text-muted mb-5">Aucun propriétaire ne correspond à votre recherche.</p>
                <a href="{{ route('admin.users.proprietaires') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-line text-ink/70 font-bold text-[13px] rounded-[10px] hover:border-teal transition-colors">Effacer les filtres</a>
            @else
                <div class="font-display font-semibold text-[16px] mb-1.5">Aucun propriétaire</div>
                <p class="text-[13.5px] text-muted mb-5">Ajoutez le premier propriétaire de votre agence.</p>
                <a href="{{ route('admin.users.create', 'proprietaire') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-teal text-paper font-bold text-[13px] rounded-[10px] hover:bg-teal-deep transition-colors">+ Nouveau propriétaire</a>
            @endif
        </div>
    @else
        <div class="bg-white border border-line rounded-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-paper-dim border-b border-line text-left text-[12px] uppercase tracking-wide text-muted font-bold">
                            <th class="px-5 py-4">Propriétaire</th>
                            <th class="px-5 py-4">Type</th>
                            <th class="px-5 py-4">Téléphone</th>
                            <th class="px-5 py-4">Biens liés</th>
                            <th class="px-5 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($proprietaires as $item)
                            @php
                                $u          = $item['user'];
                                $profil     = $u->proprietaire;
                                $entreprise = (bool) ($profil?->est_personne_morale_is);
                            @endphp
                            <tr class="border-b border-paper-dim last:border-0 hover:bg-[#FBF9F3] transition-colors">
                                <td class="px-5 py-4">
                                    <a href="{{ route('admin.users.show', $u) }}" class="flex items-center gap-3 group">
                                        <span @class([
                                            'w-[42px] h-[42px] rounded-[11px] flex items-center justify-center font-bold text-[14px] shrink-0',
                                            'bg-gold text-teal-deep' => $entreprise,
                                            'bg-teal text-paper' => ! $entreprise,
                                        ])>{{ mb_strtoupper(mb_substr($u->name, 0, 2)) }}</span>
                                        <span class="min-w-0">
                                            <span class="block font-bold text-[15px] text-ink truncate group-hover:text-teal transition-colors">{{ $u->name }}</span>
                                            <span class="block text-[12.5px] text-muted truncate">
                                                {{ $entreprise && $profil?->ninea ? 'NINEA '.$profil->ninea : ($u->email ?? 'Aucun email') }}
                                            </span>
                                        </span>
                                    </a>
                                </td>
                                <td class="px-5 py-4">
                                    @if($entreprise)
                                        <span class="text-[12px] font-bold px-3 py-1.5 rounded-full bg-gold/15 text-gold inline-block">Entreprise</span>
                                    @else
                                        <span class="text-[12px] font-bold px-3 py-1.5 rounded-full bg-green/10 text-green inline-block">Particulier</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-[14px] text-ink/80">{{ $u->telephone ?? '—' }}</td>
                                <td class="px-5 py-4 text-[14px] text-ink/80">{{ $item['nb_biens'] }} bien{{ $item['nb_biens'] > 1 ? 's' : '' }}</td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('admin.users.show', $u) }}" title="Voir la fiche"
                                           class="w-8 h-8 flex items-center justify-center rounded-lg text-muted hover:text-teal hover:bg-paper-dim transition-colors">
                                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                        </a>
                                        <a href="{{ route('admin.users.edit', $u) }}" title="Modifier"
                                           class="w-8 h-8 flex items-center justify-center rounded-lg text-muted hover:text-teal hover:bg-paper-dim transition-colors">
                                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.12 2.12 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection
