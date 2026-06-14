@extends('layouts.app')
@section('header', 'Contrats')

@section('content')
<div class="space-y-4 md:space-y-6">

    {{-- ═══ EN-TÊTE ═══ --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <h1 class="font-display font-extrabold text-xl md:text-2xl text-bimo-text tracking-tight leading-tight">
                Contrats de bail
            </h1>
            <p class="font-body text-sm text-bimo-text/50 mt-1">
                {{ $stats['total'] }} contrat(s) au total
            </p>
        </div>
        <a href="{{ route('admin.contrats.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-[var(--ac)] text-white
                  font-display font-bold text-sm rounded-[10px]
                  hover:opacity-90 transition-opacity duration-150 self-start">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Nouveau contrat
        </a>
    </div>

    {{-- ═══ KPIs ═══ --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-4 border-t-2 border-t-bimo-navy">
            <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-text/50 mb-1">Total</div>
            <div class="font-display font-extrabold text-2xl text-bimo-text leading-none">{{ $stats['total'] }}</div>
        </div>
        <div class="bg-bimo-gold/[8%] rounded-[14px] border border-bimo-gold/25 p-4">
            <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-gold/70 mb-1">Actifs</div>
            <div class="font-display font-extrabold text-2xl text-bimo-gold leading-none">{{ $stats['actifs'] }}</div>
        </div>
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-4">
            <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-text/50 mb-1">Résiliés</div>
            <div class="font-display font-extrabold text-2xl text-bimo-text/50 leading-none">{{ $stats['resilies'] }}</div>
        </div>
        <div class="bg-white rounded-[14px] border border-bimo-red/20 p-4">
            <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-text/50 mb-1">Expirés</div>
            <div class="font-display font-extrabold text-2xl text-bimo-red leading-none">{{ $stats['expires'] }}</div>
        </div>
    </div>

    {{-- ═══ FILTRES ═══ --}}
    <form method="GET" class="flex flex-wrap gap-2 items-center">
        <input type="text" name="q" value="{{ request('q') }}"
               placeholder="Référence, locataire, bien…"
               class="px-3 py-2 bg-white border border-bimo-navy/15 rounded-[9px]
                      font-body text-sm text-bimo-text placeholder:text-bimo-text/30
                      focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15
                      transition-all duration-150 w-52">

        <select name="statut" onchange="this.form.submit()"
                class="px-3 py-2 bg-white border border-bimo-navy/15 rounded-[9px]
                       font-body text-sm text-bimo-text cursor-pointer
                       focus:outline-none focus:border-bimo-gold transition-all duration-150">
            <option value="">Tous les statuts</option>
            @foreach(\App\Models\Contrat::STATUTS as $val => $label)
                <option value="{{ $val }}" @selected(request('statut') === $val)>{{ $label }}</option>
            @endforeach
        </select>

        <select name="type_bail" onchange="this.form.submit()"
                class="px-3 py-2 bg-white border border-bimo-navy/15 rounded-[9px]
                       font-body text-sm text-bimo-text cursor-pointer
                       focus:outline-none focus:border-bimo-gold transition-all duration-150">
            <option value="">Tous les types</option>
            @foreach(\App\Models\Contrat::TYPES_BAIL as $val => $label)
                <option value="{{ $val }}" @selected(request('type_bail') === $val)>{{ $label }}</option>
            @endforeach
        </select>

        <button type="submit"
                class="px-4 py-2 bg-[var(--ac)] text-white font-display font-bold text-sm
                       rounded-[9px] hover:opacity-90 transition-opacity duration-150">
            Rechercher
        </button>

        @if(request()->hasAny(['q','statut','type_bail']))
        <a href="{{ route('admin.contrats.index') }}"
           class="px-3 py-2 bg-white border border-bimo-navy/15 rounded-[9px]
                  font-body text-sm text-bimo-text/50 hover:text-bimo-text hover:border-bimo-navy/30
                  transition-all duration-150">
            Effacer
        </a>
        @endif
    </form>

    {{-- ═══ CONTENU ═══ --}}
    @if($contrats->isEmpty())
    <div class="bg-white rounded-[14px] border border-bimo-navy/10 py-16 px-6 text-center">
        <div class="w-12 h-12 bg-bimo-gold/10 border border-bimo-gold/20 rounded-[12px] flex items-center justify-center mx-auto mb-4">
            <svg class="w-6 h-6 text-bimo-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                <polyline points="14 2 14 8 20 8"/>
            </svg>
        </div>
        <div class="font-display font-bold text-base text-bimo-text mb-2">Aucun contrat trouvé</div>
        <p class="font-body text-sm text-bimo-text/50 mb-5">
            @if(request()->hasAny(['q','statut','type_bail']))
                Aucun résultat pour ces filtres.
            @else
                Créez votre premier contrat de bail.
            @endif
        </p>
        <a href="{{ route('admin.contrats.create') }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 bg-[var(--ac)] text-white
                  font-display font-bold text-sm rounded-[10px] hover:opacity-90 transition-opacity duration-150">
            + Nouveau contrat
        </a>
    </div>

    @else

    {{-- Mobile : cards --}}
    <div class="md:hidden space-y-3">
        @foreach($contrats as $contrat)
        @php
            $badgeClass = match($contrat->statut) {
                'actif'   => 'bg-bimo-gold/10 border-bimo-gold/20 text-bimo-gold',
                'resilié' => 'bg-bimo-red/10 border-bimo-red/20 text-bimo-red',
                default   => 'bg-bimo-navy/10 border-bimo-navy/15 text-bimo-text/60',
            };
        @endphp
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
            <div class="flex items-center justify-between px-4 py-3 bg-bimo-bg2 border-b border-bimo-navy/[5%]">
                <div class="flex items-center gap-2">
                    <span class="font-body text-[10px] text-bimo-text/40 uppercase tracking-widest">{{ $contrat->reference_bail_affichee }}</span>
                    <button onclick="copyRef('{{ $contrat->reference_bail_affichee }}', this)"
                            class="w-5 h-5 flex items-center justify-center border border-bimo-navy/15 rounded-[4px] text-bimo-text/30 hover:text-bimo-text transition-all duration-150">
                        <svg class="w-2.5 h-2.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/></svg>
                    </button>
                </div>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full border text-[11px] font-body font-medium {{ $badgeClass }}">
                    <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                    {{ \App\Models\Contrat::STATUTS[$contrat->statut] ?? $contrat->statut }}
                </span>
            </div>
            <div class="px-4 py-3.5 space-y-2.5">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <div class="font-body font-semibold text-sm text-bimo-text">{{ $contrat->locataire?->name ?? '—' }}</div>
                        <div class="font-body text-xs text-bimo-text/50">{{ $contrat->bien?->reference ?? '—' }} · {{ $contrat->bien?->ville }}</div>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <div class="font-display font-bold text-base text-bimo-gold">{{ number_format($contrat->loyer_contractuel, 0, ',', ' ') }} FCFA</div>
                        <div class="font-body text-[10px] text-bimo-text/40 mt-0.5">
                            {{ \App\Models\Contrat::TYPES_BAIL[$contrat->type_bail] ?? $contrat->type_bail }}
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-2 border-t border-bimo-navy/[5%]">
                    <div class="font-body text-xs text-bimo-text/50">
                        {{ $contrat->date_debut?->format('d/m/Y') }} →
                        {{ $contrat->date_fin?->format('d/m/Y') ?? 'Ouvert' }}
                        @if($contrat->date_fin && $contrat->statut === 'actif')
                            @php $jr = now()->diffInDays($contrat->date_fin, false); @endphp
                            @if($jr <= 30 && $jr >= 0)
                                <span class="text-bimo-gold font-semibold ml-1">⚠ {{ $jr }}j</span>
                            @endif
                        @endif
                    </div>
                    <div class="flex items-center gap-1.5">
                        <a href="{{ route('admin.contrats.show', $contrat) }}"
                           class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-bimo-bg border border-bimo-navy/10 rounded-[7px] font-body text-xs text-bimo-text/60 hover:text-bimo-text transition-all duration-150">
                            Voir
                        </a>
                        @if($contrat->statut === 'actif')
                        <a href="{{ route('admin.paiements.create', ['contrat_id' => $contrat->id]) }}"
                           class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-bimo-gold/10 border border-bimo-gold/20 rounded-[7px] font-body text-xs text-bimo-gold hover:bg-bimo-gold/20 transition-all duration-150">
                            + Paiement
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Desktop : table --}}
    <div class="hidden md:block bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm" data-sort-id="contrats-table">
                <thead>
                    <tr class="border-b border-bimo-navy/[5%] bg-bimo-bg">
                        <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40 cursor-pointer" data-sort="0">Référence</th>
                        <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40 cursor-pointer" data-sort="1">Bien</th>
                        <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40 cursor-pointer" data-sort="2">Locataire</th>
                        <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Type bail</th>
                        <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40 cursor-pointer" data-sort="4" data-sort-type="date">Début</th>
                        <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40 cursor-pointer" data-sort="5" data-sort-type="date">Fin</th>
                        <th class="px-5 py-3 text-right font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40 cursor-pointer" data-sort="6" data-sort-type="num">Loyer</th>
                        <th class="px-5 py-3 text-center font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Statut</th>
                        <th class="px-5 py-3 text-center font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-bimo-navy/[5%]">
                    @foreach($contrats as $contrat)
                    @php
                        $badgeClass = match($contrat->statut) {
                            'actif'   => 'bg-bimo-gold/10 border-bimo-gold/20 text-bimo-gold',
                            'resilié' => 'bg-bimo-red/10 border-bimo-red/20 text-bimo-red',
                            default   => 'bg-bimo-navy/10 border-bimo-navy/15 text-bimo-text/60',
                        };
                    @endphp
                    <tr class="hover:bg-bimo-bg2 transition-colors duration-100">

                        {{-- Référence --}}
                        <td class="px-5 py-3.5 whitespace-nowrap">
                            <div class="flex items-center gap-1.5">
                                <span class="font-body text-[11px] text-bimo-text/40 uppercase tracking-widest">{{ $contrat->reference_bail_affichee }}</span>
                                <button onclick="copyRef('{{ $contrat->reference_bail_affichee }}', this)"
                                        class="w-5 h-5 flex items-center justify-center border border-bimo-navy/10 rounded-[4px] text-bimo-text/30 hover:text-bimo-text hover:border-bimo-navy/30 transition-all duration-150">
                                    <svg class="w-2.5 h-2.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/></svg>
                                </button>
                            </div>
                        </td>

                        {{-- Bien --}}
                        <td class="px-5 py-3.5">
                            <div class="font-body font-medium text-sm text-bimo-text">{{ $contrat->bien?->reference ?? '—' }}</div>
                            <div class="font-body text-xs text-bimo-text/50">{{ $contrat->bien?->ville }}</div>
                        </td>

                        {{-- Locataire --}}
                        <td class="px-5 py-3.5">
                            <div class="font-body text-sm text-bimo-text">{{ $contrat->locataire?->name ?? '—' }}</div>
                            <div class="font-body text-xs text-bimo-text/50">{{ $contrat->locataire?->email }}</div>
                        </td>

                        {{-- Type bail --}}
                        <td class="px-5 py-3.5">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-body font-medium bg-bimo-navy/10 border border-bimo-navy/15 text-bimo-text/60">
                                {{ \App\Models\Contrat::TYPES_BAIL[$contrat->type_bail] ?? $contrat->type_bail }}
                            </span>
                        </td>

                        {{-- Début --}}
                        <td class="px-5 py-3.5 font-body text-xs text-bimo-text/60">
                            {{ $contrat->date_debut?->format('d/m/Y') }}
                        </td>

                        {{-- Fin --}}
                        <td class="px-5 py-3.5 font-body text-xs text-bimo-text/60">
                            {{ $contrat->date_fin?->format('d/m/Y') ?? 'Ouvert' }}
                            @if($contrat->date_fin && $contrat->statut === 'actif')
                                @php $jr = now()->diffInDays($contrat->date_fin, false); @endphp
                                @if($jr <= 30 && $jr >= 0)
                                    <div class="font-body text-[10px] text-bimo-gold font-semibold mt-0.5">⚠ {{ $jr }}j restants</div>
                                @endif
                            @endif
                        </td>

                        {{-- Loyer --}}
                        <td class="px-5 py-3.5 text-right font-display font-bold text-sm text-bimo-gold">
                            {{ number_format($contrat->loyer_contractuel, 0, ',', ' ') }} F
                        </td>

                        {{-- Statut --}}
                        <td class="px-5 py-3.5 text-center">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full border text-[11px] font-body font-medium {{ $badgeClass }}">
                                <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                                {{ \App\Models\Contrat::STATUTS[$contrat->statut] ?? $contrat->statut }}
                            </span>
                        </td>

                        {{-- Actions --}}
                        <td class="px-5 py-3.5">
                            <div class="flex items-center justify-center gap-1.5">
                                <a href="{{ route('admin.contrats.show', $contrat) }}"
                                   class="w-9 h-9 flex items-center justify-center border border-bimo-navy/10 rounded-[6px] text-bimo-text/40 hover:text-bimo-text hover:border-bimo-navy/30 transition-all duration-150"
                                   title="Voir">
                                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                </a>
                                <a href="{{ route('admin.contrats.bail-formel-pdf', $contrat) }}" target="_blank"
                                   class="w-9 h-9 flex items-center justify-center border border-bimo-navy/10 rounded-[6px] text-bimo-text/40 hover:text-bimo-gold hover:border-bimo-gold/30 transition-all duration-150"
                                   title="Bail formel PDF">
                                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                                </a>
                                @if($contrat->statut === 'actif')
                                <a href="{{ route('admin.paiements.create', ['contrat_id' => $contrat->id]) }}"
                                   class="w-7 h-7 flex items-center justify-center bg-bimo-navy border border-bimo-navy rounded-[6px] text-white hover:bg-bimo-navy-dk transition-all duration-150"
                                   title="Enregistrer un paiement">
                                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                </a>
                                <a href="{{ route('admin.contrats.edit', $contrat) }}"
                                   class="w-9 h-9 flex items-center justify-center border border-bimo-navy/10 rounded-[6px] text-bimo-text/40 hover:text-bimo-text hover:border-bimo-navy/30 transition-all duration-150"
                                   title="Modifier">
                                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                </a>
                                <form method="POST" action="{{ route('admin.contrats.destroy', $contrat) }}"
                                      data-confirm="Le contrat {{ $contrat->reference_bail ?? 'BAIL-'.$contrat->id }} sera résilié et le bien repassera en Disponible."
                                      data-confirm-title="Résilier ce contrat ?"
                                      data-confirm-ok="Oui, résilier">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="w-9 h-9 flex items-center justify-center border border-bimo-red/20 rounded-[6px] text-bimo-red/60 hover:text-bimo-red hover:border-bimo-red/40 hover:bg-bimo-red/5 transition-all duration-150"
                                            title="Résilier">
                                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
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

        {{-- Pagination --}}
        @if($contrats->hasPages())
        <div class="flex items-center justify-between px-5 py-3.5 border-t border-bimo-navy/[5%] bg-bimo-bg">
            <span class="font-body text-xs text-bimo-text/40">
                {{ $contrats->firstItem() }}–{{ $contrats->lastItem() }} sur {{ $contrats->total() }}
            </span>
            <div class="flex items-center gap-1">
                @if(!$contrats->onFirstPage())
                <a href="{{ $contrats->previousPageUrl() }}"
                   class="w-9 h-9 flex items-center justify-center border border-bimo-navy/10 rounded-[6px] text-bimo-text/40 hover:text-bimo-text hover:border-bimo-navy/30 transition-all duration-150">
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                </a>
                @endif
                @foreach($contrats->getUrlRange(max(1,$contrats->currentPage()-2), min($contrats->lastPage(),$contrats->currentPage()+2)) as $page => $url)
                <a href="{{ $url }}"
                   class="w-9 h-9 flex items-center justify-center rounded-[6px] font-body text-xs transition-all duration-150
                          {{ $page === $contrats->currentPage() ? 'bg-bimo-navy text-white border border-bimo-navy' : 'border border-bimo-navy/10 text-bimo-text/60 hover:text-bimo-text hover:border-bimo-navy/30' }}">
                    {{ $page }}
                </a>
                @endforeach
                @if($contrats->hasMorePages())
                <a href="{{ $contrats->nextPageUrl() }}"
                   class="w-9 h-9 flex items-center justify-center border border-bimo-navy/10 rounded-[6px] text-bimo-text/40 hover:text-bimo-text hover:border-bimo-navy/30 transition-all duration-150">
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                </a>
                @endif
            </div>
        </div>
        @endif
    </div>
    @endif

</div>
@endsection
