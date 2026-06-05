@extends('layouts.app')
@section('header', 'Mon équipe')

@section('content')
<div class="space-y-4 md:space-y-6">

    {{-- En-tête --}}
    <div class="flex items-start justify-between gap-4">
        <div>
            <h1 class="font-display font-extrabold text-xl md:text-2xl text-bimo-text tracking-tight leading-tight">
                Mon équipe
            </h1>
            <p class="font-body text-sm text-bimo-text/50 mt-1">
                {{ $nbActuels }} collaborateur{{ $nbActuels > 1 ? 's' : '' }}
                @if($limiteMax !== null)
                    sur {{ $limiteMax }} autorisés
                @else
                    — illimité (plan Agence)
                @endif
            </p>
        </div>
        @if($peutAjouter)
        <a href="{{ route('admin.equipe.create') }}"
           class="flex-shrink-0 inline-flex items-center gap-2 bg-[var(--ac)] text-white
                  font-display font-bold text-sm px-4 py-2.5 rounded-[10px]
                  hover:opacity-90 transition-opacity duration-150">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            <span class="hidden sm:inline">Inviter</span>
        </a>
        @else
        <div class="flex-shrink-0 inline-flex items-center gap-2 bg-bimo-navy/5 text-bimo-text/40
                    font-display font-bold text-sm px-4 py-2.5 rounded-[10px] cursor-not-allowed">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/>
            </svg>
            Limite atteinte
        </div>
        @endif
    </div>

    {{-- Explication --}}
    <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-5" x-data="{ open: false }">
        <button @click="open = !open"
                class="w-full flex items-center justify-between gap-3 text-left">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-[9px] bg-bimo-navy/5 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-bimo-text/50" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                </div>
                <span class="font-display font-semibold text-sm text-bimo-text">Comment fonctionne la gestion d'équipe ?</span>
            </div>
            <svg class="w-4 h-4 text-bimo-text/30 flex-shrink-0 transition-transform duration-150"
                 :class="open ? 'rotate-180' : ''"
                 viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="6 9 12 15 18 9"/>
            </svg>
        </button>

        <div x-show="open" x-transition:enter="transition duration-150"
             x-transition:enter-start="opacity-0 -translate-y-1"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="mt-4 space-y-4">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">

                {{-- Ce que peut faire un collaborateur --}}
                <div class="bg-bimo-bg rounded-[10px] p-4">
                    <p class="font-display font-semibold text-xs text-bimo-text uppercase tracking-widest mb-3">
                        ✓ Ce qu'un collaborateur peut faire
                    </p>
                    <ul class="space-y-1.5">
                        @foreach([
                            'Consulter et gérer les biens',
                            'Créer et modifier les contrats de bail',
                            'Enregistrer les paiements et générer les quittances',
                            'Gérer les locataires et propriétaires',
                            'Accéder aux rapports financiers',
                            'Consulter les impayés',
                        ] as $item)
                        <li class="flex items-start gap-2">
                            <svg class="w-3.5 h-3.5 text-bimo-gold flex-shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <polyline points="20 6 9 17 4 12"/>
                            </svg>
                            <span class="font-body text-xs text-bimo-text/70">{{ $item }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Ce qui est réservé au directeur --}}
                <div class="bg-bimo-bg rounded-[10px] p-4">
                    <p class="font-display font-semibold text-xs text-bimo-text uppercase tracking-widest mb-3">
                        ✗ Réservé au directeur uniquement
                    </p>
                    <ul class="space-y-1.5">
                        @foreach([
                            'Modifier les paramètres de l\'agence (logo, couleurs, NINEA…)',
                            'Gérer l\'équipe (inviter ou retirer des collaborateurs)',
                            'Gérer l\'abonnement et le plan',
                            'Importer des données Excel',
                        ] as $item)
                        <li class="flex items-start gap-2">
                            <svg class="w-3.5 h-3.5 text-bimo-red flex-shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                            </svg>
                            <span class="font-body text-xs text-bimo-text/70">{{ $item }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>

            </div>

            <p class="font-body text-xs text-bimo-text/40 leading-relaxed">
                Un collaborateur retiré ne peut plus se connecter. Ses actions passées restent enregistrées dans le journal d'activité.
            </p>
        </div>
    </div>

    {{-- Alerte limite plan --}}
    @if(!$peutAjouter && $limiteMax !== null)
    <div class="bg-bimo-gold/[8%] border border-bimo-gold/25 rounded-[14px] px-5 py-4 flex items-start gap-3">
        <svg class="w-5 h-5 text-bimo-gold flex-shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
        </svg>
        <div>
            <p class="font-body font-medium text-sm text-bimo-text">Limite de {{ $limiteMax }} collaborateurs atteinte</p>
            <p class="font-body text-xs text-bimo-text/50 mt-0.5">
                Passez au plan supérieur pour ajouter davantage de membres à votre équipe.
            </p>
        </div>
    </div>
    @endif

    {{-- Liste desktop --}}
    <div class="hidden md:block bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-bimo-navy/10 bg-bimo-bg2">
                    <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/50">Nom</th>
                    <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/50">Email</th>
                    <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/50">Rôle</th>
                    <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/50">Membre depuis</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-bimo-navy/[5%]">
                @foreach($collaborateurs as $collab)
                <tr class="hover:bg-bimo-bg transition-colors duration-100">
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-[8px] bg-[var(--ac)]/10 flex items-center justify-center flex-shrink-0">
                                <span class="font-display font-bold text-xs text-[var(--ac)]">
                                    {{ strtoupper(substr($collab->name, 0, 1)) }}
                                </span>
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
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-body font-medium
                                     bg-[var(--ac)]/10 border border-[var(--ac)]/20 text-[var(--ac)]">
                            Directeur
                        </span>
                        @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-body font-medium
                                     bg-bimo-navy/10 border border-bimo-navy/15 text-bimo-text/70">
                            Collaborateur
                        </span>
                        @endif
                    </td>
                    <td class="px-5 py-4 font-body text-sm text-bimo-text/50">
                        {{ $collab->created_at->translatedFormat('d M Y') }}
                    </td>
                    <td class="px-5 py-4 text-right">
                        @if(!$collab->is_owner && $collab->id !== auth()->id())
                        <form method="POST" action="{{ route('admin.equipe.destroy', $collab) }}"
                              x-data
                              @submit.prevent="if(confirm('Retirer {{ $collab->name }} de l\'équipe ?')) $el.submit()">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-[8px]
                                           bg-bimo-red/10 text-bimo-red hover:bg-bimo-red/20 border border-bimo-red/20
                                           font-body text-xs transition-all duration-150">
                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M3 6h18M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a1 1 0 011-1h4a1 1 0 011 1v2"/>
                                </svg>
                                Retirer
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Cards mobile --}}
    <div class="md:hidden space-y-3">
        @foreach($collaborateurs as $collab)
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-4">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-[9px] bg-[var(--ac)]/10 flex items-center justify-center flex-shrink-0">
                        <span class="font-display font-bold text-sm text-[var(--ac)]">
                            {{ strtoupper(substr($collab->name, 0, 1)) }}
                        </span>
                    </div>
                    <div>
                        <p class="font-body font-medium text-sm text-bimo-text">{{ $collab->name }}</p>
                        <p class="font-body text-xs text-bimo-text/40">{{ $collab->email }}</p>
                    </div>
                </div>
                @if($collab->is_owner)
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-body font-medium
                             bg-[var(--ac)]/10 text-[var(--ac)]">Directeur</span>
                @else
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-body font-medium
                             bg-bimo-navy/10 text-bimo-text/60">Collaborateur</span>
                @endif
            </div>
            @if(!$collab->is_owner && $collab->id !== auth()->id())
            <form method="POST" action="{{ route('admin.equipe.destroy', $collab) }}"
                  x-data
                  @submit.prevent="if(confirm('Retirer {{ $collab->name }} ?')) $el.submit()">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="w-full py-2 rounded-[9px] bg-bimo-red/10 text-bimo-red border border-bimo-red/20
                               font-body font-medium text-xs hover:bg-bimo-red/20 transition-all duration-150">
                    Retirer de l'équipe
                </button>
            </form>
            @endif
        </div>
        @endforeach
    </div>

</div>
@endsection
