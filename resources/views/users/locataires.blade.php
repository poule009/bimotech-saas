@extends('layouts.app')
@section('header', 'Locataires')

@section('content')
<div class="space-y-4 md:space-y-6">

    {{-- En-tête --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <h1 class="font-display font-extrabold text-xl md:text-2xl text-bimo-text tracking-tight leading-tight">Locataires</h1>
            <p class="font-body text-sm text-bimo-text/50 mt-1">{{ $stats['total'] }} locataire(s) enregistré(s)</p>
        </div>
        <a href="{{ route('admin.users.create', 'locataire') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-[var(--ac)] text-white
                  font-display font-bold text-sm rounded-[10px] hover:opacity-90 transition-opacity duration-150 self-start">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Nouveau locataire
        </a>
    </div>

    {{-- KPIs --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="bg-bimo-gold/[8%] rounded-[14px] border border-bimo-gold/25 p-4">
            <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-gold/70 mb-1">Total locataires</div>
            <div class="font-display font-extrabold text-2xl text-bimo-gold leading-none">{{ $stats['total'] }}</div>
            <div class="font-body text-[10.5px] text-bimo-gold/60 mt-1.5">Enregistrés dans l'agence</div>
        </div>
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-4">
            <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-text/50 mb-1">Avec contrat actif</div>
            <div class="font-display font-extrabold text-2xl text-bimo-text leading-none">{{ $stats['actifs'] }}</div>
            <div class="font-body text-[10.5px] text-bimo-text/40 mt-1.5">Loyer en cours</div>
        </div>
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-4">
            <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-text/50 mb-1">Taux d'occupation</div>
            <div class="font-display font-extrabold text-2xl text-bimo-text leading-none">
                {{ $stats['total'] > 0 ? round($stats['actifs']/$stats['total']*100) : 0 }}%
            </div>
            <div class="font-body text-[10.5px] text-bimo-text/40 mt-1.5">Locataires actifs</div>
        </div>
        <div class="bg-white rounded-[14px] border {{ $stats['sans_contrat'] > 0 ? 'border-bimo-red/20' : 'border-bimo-navy/10' }} p-4">
            <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-text/50 mb-1">Sans contrat</div>
            <div class="font-display font-extrabold text-2xl {{ $stats['sans_contrat'] > 0 ? 'text-bimo-red' : 'text-bimo-text' }} leading-none">{{ $stats['sans_contrat'] }}</div>
            <div class="font-body text-[10.5px] text-bimo-text/40 mt-1.5">À reloger ou archiver</div>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
        <div class="flex flex-wrap items-center justify-between gap-3 px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
            <div class="flex flex-wrap items-center gap-2">
                <h2 class="font-display font-bold text-sm text-bimo-text">Liste des locataires</h2>
                {{-- Filtre tabs --}}
                <div class="flex gap-1 bg-bimo-bg border border-bimo-navy/10 p-1 rounded-[8px]">
                    <button onclick="filterStatus('all', this)" data-tab="all"
                            class="filter-tab px-3 py-1 rounded-[6px] font-body font-medium text-xs transition-all duration-150 bg-bimo-navy text-white">
                        Tous
                    </button>
                    <button onclick="filterStatus('actif', this)" data-tab="actif"
                            class="filter-tab px-3 py-1 rounded-[6px] font-body font-medium text-xs transition-all duration-150 text-bimo-text/50 hover:text-bimo-text">
                        Avec contrat
                    </button>
                    <button onclick="filterStatus('sans', this)" data-tab="sans"
                            class="filter-tab px-3 py-1 rounded-[6px] font-body font-medium text-xs transition-all duration-150 text-bimo-text/50 hover:text-bimo-text">
                        Sans contrat
                    </button>
                </div>
            </div>
            <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-bimo-text/30 pointer-events-none"
                     viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input type="text" placeholder="Rechercher..." id="search-loc" oninput="searchTable(this.value)"
                       class="pl-9 pr-3 py-2 bg-white border border-bimo-navy/15 rounded-[9px] font-body text-sm text-bimo-text
                              placeholder:text-bimo-text/30 focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15
                              transition-all duration-150 w-48">
            </div>
        </div>

        {{-- Mobile cards --}}
        <div class="md:hidden divide-y divide-bimo-navy/[5%]">
            @forelse($locataires as $idx => $user)
            @php
                $contratActif = $user->contrats->first();
                $aContrat = (bool) $contratActif;
                $profil = $user->locataire ?? null;
                $telDigits = preg_replace('/[^0-9+]/', '', $user->telephone ?? '');
                if ($telDigits && !str_starts_with($telDigits, '+') && !str_starts_with($telDigits, '221')) {
                    $telDigits = '221' . ltrim($telDigits, '0');
                }
                $telDigits = ltrim($telDigits, '+');
                $waMsg = urlencode("Bonjour {$user->name}, nous vous contactons concernant votre location. Cordialement.");
                $waLink = $telDigits ? "https://wa.me/{$telDigits}?text={$waMsg}" : null;
            @endphp
            <div class="px-5 py-4" data-status="{{ $aContrat ? 'actif' : 'sans' }}" data-search="{{ strtolower($user->name . ' ' . $user->email . ' ' . $user->telephone) }}">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-[10px] flex items-center justify-center font-display font-bold text-sm text-white flex-shrink-0 bg-bimo-navy">
                        {{ mb_strtoupper(mb_substr($user->name, 0, 2)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="font-body font-semibold text-sm text-bimo-text truncate">{{ $user->name }}</div>
                        <div class="font-body text-xs text-bimo-text/50">{{ $user->email }}</div>
                    </div>
                    @if($aContrat)
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full border border-bimo-gold/20 bg-bimo-gold/10 text-[11px] font-body font-medium text-bimo-gold flex-shrink-0">
                        <span class="w-1.5 h-1.5 rounded-full bg-bimo-gold"></span>Actif
                    </span>
                    @else
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full border border-bimo-navy/15 bg-bimo-navy/5 text-[11px] font-body font-medium text-bimo-text/50 flex-shrink-0">Sans contrat</span>
                    @endif
                </div>
                @if($contratActif)
                <div class="bg-bimo-bg border border-bimo-navy/10 rounded-[8px] px-3 py-2 mb-3">
                    <div class="font-body font-semibold text-xs text-bimo-text">{{ $contratActif->bien->reference ?? '—' }}</div>
                    <div class="font-body text-[11px] text-bimo-text/50">{{ $contratActif->bien->adresse ?? '' }}, {{ $contratActif->bien->ville ?? '' }}</div>
                    <div class="font-display font-bold text-xs text-bimo-gold mt-0.5">{{ number_format($contratActif->loyer_contractuel, 0, ',', ' ') }} F/mois</div>
                </div>
                @endif
                <div class="flex items-center justify-end gap-1.5">
                    <a href="{{ route('admin.users.show', $user) }}"
                       class="w-9 h-9 flex items-center justify-center border border-bimo-navy/10 rounded-[6px] text-bimo-text/40 hover:text-bimo-text hover:border-bimo-navy/30 transition-all duration-150">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </a>
                    <a href="{{ route('admin.users.edit', $user) }}"
                       class="w-9 h-9 flex items-center justify-center border border-bimo-navy/10 rounded-[6px] text-bimo-text/40 hover:text-bimo-text hover:border-bimo-navy/30 transition-all duration-150">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    </a>
                    @if($waLink)
                    <a href="{{ $waLink }}" target="_blank"
                       class="w-9 h-9 flex items-center justify-center border border-bimo-navy/10 rounded-[6px] text-bimo-text/40 hover:border-[#25D366] hover:text-[#25D366] transition-all duration-150">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    </a>
                    @endif
                </div>
            </div>
            @empty
            <div class="px-5 py-16 text-center">
                <div class="text-4xl mb-3">👤</div>
                <div class="font-display font-bold text-base text-bimo-text mb-2">Aucun locataire enregistré</div>
                <p class="font-body text-sm text-bimo-text/50 mb-5">Ajoutez votre premier locataire pour commencer.</p>
                <a href="{{ route('admin.users.create', 'locataire') }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-[var(--ac)] text-white font-display font-bold text-sm rounded-[10px] hover:opacity-90 transition-opacity duration-150">
                    + Ajouter un locataire
                </a>
            </div>
            @endforelse
        </div>

        {{-- Desktop table --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-sm" id="dt-loc">
                <thead>
                    <tr class="border-b border-bimo-navy/[5%] bg-bimo-bg">
                        <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Locataire</th>
                        <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Contact</th>
                        <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Situation</th>
                        <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Contrat actif</th>
                        <th class="px-5 py-3 text-center font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Statut</th>
                        <th class="px-5 py-3 text-center font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-bimo-navy/[5%]">
                    @forelse($locataires as $idx => $user)
                    @php
                        $contratActif = $user->contrats->first();
                        $aContrat = (bool) $contratActif;
                        $profil = $user->locataire ?? null;
                        $telDigits = preg_replace('/[^0-9+]/', '', $user->telephone ?? '');
                        if ($telDigits && !str_starts_with($telDigits, '+') && !str_starts_with($telDigits, '221')) {
                            $telDigits = '221' . ltrim($telDigits, '0');
                        }
                        $telDigits = ltrim($telDigits, '+');
                        $waMsg = urlencode("Bonjour {$user->name}, nous vous contactons concernant votre location. Cordialement.");
                        $waLink = $telDigits ? "https://wa.me/{$telDigits}?text={$waMsg}" : null;
                        $estEntreprise = (bool) ($profil?->est_entreprise ?? false);
                    @endphp
                    <tr class="hover:bg-bimo-bg2 transition-colors duration-100" data-status="{{ $aContrat ? 'actif' : 'sans' }}">
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-[9px] flex items-center justify-center font-display font-bold text-sm text-white flex-shrink-0 bg-bimo-navy">
                                    {{ mb_strtoupper(mb_substr($user->name, 0, 2)) }}
                                </div>
                                <div>
                                    <div class="font-body font-semibold text-sm text-bimo-text">{{ $user->name }}</div>
                                    <div class="flex items-center gap-2 mt-0.5">
                                        @if($estEntreprise)
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded-[4px] text-[9.5px] font-body font-bold bg-bimo-navy/10 text-bimo-text/60">🏢 Entreprise</span>
                                        @endif
                                        <span class="font-body text-[10px] text-bimo-text/40">Depuis {{ $user->created_at->format('M Y') }}</span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3.5">
                            <div class="font-body text-sm text-bimo-text">{{ $user->email }}</div>
                            @if($user->telephone)
                            <div class="font-body text-xs text-bimo-text/50 mt-0.5">{{ $user->telephone }}</div>
                            @endif
                        </td>
                        <td class="px-5 py-3.5">
                            @if($profil?->profession)
                            <div class="font-body font-medium text-sm text-bimo-text">{{ $profil->profession }}</div>
                            @endif
                            @if($profil?->employeur)
                            <div class="font-body text-xs text-bimo-text/50">{{ $profil->employeur }}</div>
                            @endif
                            @if(!$profil?->profession && !$profil?->employeur)
                            <span class="font-body text-sm text-bimo-text/30">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5">
                            @if($contratActif)
                            <div class="bg-bimo-bg border border-bimo-navy/10 rounded-[8px] px-3 py-2">
                                <div class="font-body font-semibold text-xs text-bimo-text">{{ $contratActif->bien->reference ?? '—' }}</div>
                                <div class="font-body text-[10px] text-bimo-text/50 mt-0.5">{{ $contratActif->bien->adresse ?? '' }}, {{ $contratActif->bien->ville ?? '' }}</div>
                                <div class="font-display font-bold text-xs text-bimo-gold mt-0.5">{{ number_format($contratActif->loyer_contractuel, 0, ',', ' ') }} F/mois</div>
                            </div>
                            @else
                            <span class="font-body text-sm text-bimo-text/30">Pas de contrat actif</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            @if($aContrat)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full border border-bimo-gold/20 bg-bimo-gold/10 text-[11px] font-body font-medium text-bimo-gold">
                                <span class="w-1.5 h-1.5 rounded-full bg-bimo-gold"></span>Actif
                            </span>
                            @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full border border-bimo-navy/15 bg-bimo-navy/5 text-[11px] font-body font-medium text-bimo-text/50">Sans contrat</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5">
                            <div class="flex items-center justify-center gap-1.5">
                                <a href="{{ route('admin.users.show', $user) }}"
                                   class="w-9 h-9 flex items-center justify-center border border-bimo-navy/10 rounded-[6px] text-bimo-text/40 hover:text-bimo-text hover:border-bimo-navy/30 transition-all duration-150"
                                   title="Voir">
                                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                </a>
                                <a href="{{ route('admin.users.edit', $user) }}"
                                   class="w-9 h-9 flex items-center justify-center border border-bimo-navy/10 rounded-[6px] text-bimo-text/40 hover:text-bimo-text hover:border-bimo-navy/30 transition-all duration-150"
                                   title="Modifier">
                                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                </a>
                                @if($waLink)
                                <a href="{{ $waLink }}" target="_blank"
                                   class="w-9 h-9 flex items-center justify-center border border-bimo-navy/10 rounded-[6px] text-bimo-text/40 hover:border-[#25D366] hover:text-[#25D366] transition-all duration-150"
                                   title="WhatsApp">
                                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                </a>
                                @endif
                                @if($contratActif)
                                <a href="{{ route('admin.paiements.create') }}?contrat_id={{ $contratActif->id }}"
                                   class="w-9 h-9 flex items-center justify-center border border-bimo-gold/30 rounded-[6px] text-bimo-gold/60 hover:text-bimo-gold hover:border-bimo-gold/60 hover:bg-bimo-gold/5 transition-all duration-150"
                                   title="Enregistrer paiement">
                                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-16 text-center">
                            <div class="text-4xl mb-3">👤</div>
                            <div class="font-display font-bold text-base text-bimo-text mb-2">Aucun locataire enregistré</div>
                            <p class="font-body text-sm text-bimo-text/50 mb-5">Ajoutez votre premier locataire pour commencer à gérer les locations.</p>
                            <a href="{{ route('admin.users.create', 'locataire') }}"
                               class="inline-flex items-center gap-2 px-5 py-2.5 bg-[var(--ac)] text-white font-display font-bold text-sm rounded-[10px] hover:opacity-90 transition-opacity duration-150">
                                + Ajouter un locataire
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($locataires->hasPages())
        <div class="px-5 py-3.5 border-t border-bimo-navy/[5%] bg-bimo-bg">
            {{ $locataires->links() }}
        </div>
        @endif
    </div>

</div>

@push('scripts')
<script>
function filterStatus(status, btn) {
    document.querySelectorAll('.filter-tab').forEach(t => {
        t.classList.remove('bg-bimo-navy','text-white');
        t.classList.add('text-bimo-text/50');
    });
    btn.classList.add('bg-bimo-navy','text-white');
    btn.classList.remove('text-bimo-text/50');
    // Desktop
    document.querySelectorAll('#dt-loc tbody tr[data-status]').forEach(tr => {
        tr.style.display = (status === 'all' || tr.dataset.status === status) ? '' : 'none';
    });
    // Mobile
    document.querySelectorAll('[data-status]').forEach(el => {
        if (el.tagName !== 'TR') {
            el.style.display = (status === 'all' || el.dataset.status === status) ? '' : 'none';
        }
    });
}
function searchTable(q) {
    q = q.toLowerCase();
    document.querySelectorAll('#dt-loc tbody tr[data-status]').forEach(tr => {
        tr.style.display = tr.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
    document.querySelectorAll('[data-search]').forEach(el => {
        el.style.display = el.dataset.search.includes(q) ? '' : 'none';
    });
}
</script>
@endpush

@endsection
