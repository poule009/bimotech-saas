@extends('layouts.app')
@section('header', 'Mon équipe')

@php
$roleLabels = \Database\Seeders\PermissionsSeeder::ROLE_LABELS;
@endphp

@section('content')
<div class="space-y-4 md:space-y-6">

    {{-- En-tête --}}
    <div class="flex items-start justify-between gap-4">
        <div>
            <h1 class="font-display font-extrabold text-xl md:text-2xl text-bimo-text tracking-tight leading-tight">Mon équipe</h1>
            <p class="font-body text-sm text-bimo-text/50 mt-1">
                {{ $nbActuels }} collaborateur{{ $nbActuels > 1 ? 's' : '' }}
                @if($limiteMax !== null) sur {{ $limiteMax }} autorisés @else — illimité (plan Agence) @endif
            </p>
        </div>
        @if($peutAjouter)
        <a href="{{ route('admin.equipe.create') }}"
           class="flex-shrink-0 inline-flex items-center gap-2 bg-[var(--ac)] text-white font-display font-bold text-sm px-4 py-2.5 rounded-[10px] hover:opacity-90 transition-opacity duration-150">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            <span class="hidden sm:inline">Inviter</span>
        </a>
        @else
        <div class="flex-shrink-0 inline-flex items-center gap-2 bg-bimo-navy/5 text-bimo-text/40 font-display font-bold text-sm px-4 py-2.5 rounded-[10px] cursor-not-allowed">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
            Limite atteinte
        </div>
        @endif
    </div>

    {{-- Info permissions --}}
    <div class="bg-bimo-gold/[6%] border border-bimo-gold/20 rounded-[14px] px-5 py-4 flex items-start gap-3">
        <svg class="w-5 h-5 text-bimo-gold flex-shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
        </svg>
        <p class="font-body text-sm text-bimo-text/70 leading-relaxed">
            Chaque collaborateur reçoit le profil <strong class="text-bimo-text font-medium">Gestionnaire</strong> par défaut.
            Cliquez sur <strong class="text-bimo-text font-medium">Permissions</strong> pour ajuster finement ce qu'il peut faire.
        </p>
    </div>

    @if(!$peutAjouter && $limiteMax !== null)
    <div class="bg-bimo-gold/[8%] border border-bimo-gold/25 rounded-[14px] px-5 py-4 flex items-start gap-3">
        <svg class="w-5 h-5 text-bimo-gold flex-shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
        </svg>
        <div>
            <p class="font-body font-medium text-sm text-bimo-text">Limite de {{ $limiteMax }} collaborateurs atteinte</p>
            <p class="font-body text-xs text-bimo-text/50 mt-0.5">Passez au plan supérieur pour ajouter davantage de membres.</p>
        </div>
    </div>
    @endif

    {{-- Table desktop --}}
    <div class="hidden md:block bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-bimo-navy/10 bg-bimo-bg2">
                    <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/50">Nom</th>
                    <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/50">Email</th>
                    <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/50">Profil d'accès</th>
                    <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/50">Membre depuis</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-bimo-navy/[5%]">
                @foreach($collaborateurs as $collab)
                @php
                    $roleName  = $collab->roles->first()?->name;
                    $roleLabel = $roleLabels[$roleName] ?? 'Gestionnaire';
                @endphp
                <tr class="hover:bg-bimo-bg transition-colors duration-100">
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-[8px] bg-[var(--ac)]/10 flex items-center justify-center flex-shrink-0">
                                <span class="font-display font-bold text-xs text-[var(--ac)]">{{ mb_strtoupper(mb_substr($collab->name, 0, 2)) }}</span>
                            </div>
                            <div>
                                <p class="font-body font-medium text-sm text-bimo-text">{{ $collab->name }}</p>
                                @if($collab->telephone)
                                <p class="font-body text-xs text-bimo-text/40">{{ $collab->telephone }}</p>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-4 font-body text-sm text-bimo-text/70">{{ $collab->email }}</td>
                    <td class="px-5 py-4">
                        @if($collab->is_owner)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-body font-medium bg-[var(--ac)]/10 border border-[var(--ac)]/20 text-[var(--ac)]">
                            Directeur
                        </span>
                        @else
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-body font-medium bg-bimo-navy/8 border border-bimo-navy/15 text-bimo-text/70">
                            <svg class="w-3 h-3 text-bimo-text/40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/>
                            </svg>
                            {{ $roleLabel }}
                        </span>
                        @endif
                    </td>
                    <td class="px-5 py-4 font-body text-sm text-bimo-text/50">
                        {{ $collab->created_at->translatedFormat('d M Y') }}
                    </td>
                    <td class="px-5 py-4">
                        <div class="flex items-center justify-end gap-2">
                            @if(!$collab->is_owner)
                            <a href="{{ route('admin.equipe.permissions', $collab) }}"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-[8px] bg-bimo-navy/5 text-bimo-text/70 hover:bg-[var(--ac)]/10 hover:text-[var(--ac)] border border-bimo-navy/10 font-body text-xs transition-all duration-150">
                                <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/>
                                </svg>
                                Permissions
                            </a>
                            @endif
                            @if(!$collab->is_owner && $collab->id !== auth()->id())
                            <form method="POST" action="{{ route('admin.equipe.destroy', $collab) }}" x-data
                                  @submit.prevent="if(confirm('Retirer {{ $collab->name }} de l\'équipe ?')) $el.submit()">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-[8px] bg-bimo-red/10 text-bimo-red hover:bg-bimo-red/20 border border-bimo-red/20 font-body text-xs transition-all duration-150">
                                    <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M3 6h18M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a1 1 0 011-1h4a1 1 0 011 1v2"/>
                                    </svg>
                                    Retirer
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Cards mobile --}}
    <div class="md:hidden space-y-3">
        @foreach($collaborateurs as $collab)
        @php $roleLabel = $roleLabels[$collab->roles->first()?->name] ?? 'Gestionnaire'; @endphp
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-4">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-[9px] bg-[var(--ac)]/10 flex items-center justify-center flex-shrink-0">
                        <span class="font-display font-bold text-sm text-[var(--ac)]">{{ mb_strtoupper(mb_substr($collab->name, 0, 2)) }}</span>
                    </div>
                    <div>
                        <p class="font-body font-medium text-sm text-bimo-text">{{ $collab->name }}</p>
                        <p class="font-body text-xs text-bimo-text/40">{{ $collab->email }}</p>
                    </div>
                </div>
                @if($collab->is_owner)
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-body font-medium bg-[var(--ac)]/10 text-[var(--ac)]">Directeur</span>
                @else
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-body font-medium bg-bimo-navy/10 text-bimo-text/60">{{ $roleLabel }}</span>
                @endif
            </div>
            @if(!$collab->is_owner)
            <div class="flex gap-2">
                <a href="{{ route('admin.equipe.permissions', $collab) }}"
                   class="flex-1 py-2 rounded-[9px] bg-bimo-navy/5 text-bimo-text/70 border border-bimo-navy/10 font-body font-medium text-xs text-center hover:bg-[var(--ac)]/10 hover:text-[var(--ac)] transition-all duration-150">
                    Permissions
                </a>
                @if($collab->id !== auth()->id())
                <form method="POST" action="{{ route('admin.equipe.destroy', $collab) }}" x-data
                      @submit.prevent="if(confirm('Retirer {{ $collab->name }} ?')) $el.submit()">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="px-4 py-2 rounded-[9px] bg-bimo-red/10 text-bimo-red border border-bimo-red/20 font-body font-medium text-xs hover:bg-bimo-red/20 transition-all duration-150">
                        Retirer
                    </button>
                </form>
                @endif
            </div>
            @endif
        </div>
        @endforeach
    </div>

</div>
@endsection
