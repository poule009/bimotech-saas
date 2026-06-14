@extends('layouts.app')
@section('header', 'Paiements')

@section('content')
<div class="space-y-4 md:space-y-6">

    {{-- ═══ EN-TÊTE ═══ --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <h1 class="font-display font-extrabold text-xl md:text-2xl text-bimo-text tracking-tight leading-tight">
                Paiements
            </h1>
            <p class="font-body text-sm text-bimo-text/50 mt-1">
                {{ now()->translatedFormat('F Y') }} — {{ $stats['nb_payes'] }} paiement(s) validé(s)
            </p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.paiements.export-csv', request()->query()) }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-bimo-navy/15
                      rounded-[10px] font-body font-medium text-sm text-bimo-text/60
                      hover:text-bimo-text hover:border-bimo-navy/30 transition-all duration-150"
               title="Exporter les paiements filtrés en CSV">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/>
                    <polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/>
                </svg>
                <span class="hidden sm:inline">Exporter CSV</span>
            </a>
            <a href="{{ route('admin.paiements.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-[var(--ac)] text-white
                      font-display font-bold text-sm rounded-[10px]
                      hover:opacity-90 transition-opacity duration-150">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Nouveau paiement
            </a>
        </div>
    </div>

    {{-- ═══ KPIs ═══ --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">

        <div class="bg-bimo-gold/[8%] rounded-[14px] border border-bimo-gold/25 p-4">
            <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-gold/70 mb-1">Loyers encaissés</div>
            <div class="font-display font-extrabold text-xl text-bimo-gold leading-none">
                {{ number_format($stats['total_loyers'], 0, ',', ' ') }}
                <span class="font-body font-normal text-sm text-bimo-gold/50">F</span>
            </div>
            <div class="font-body text-[10.5px] text-bimo-gold/60 mt-1.5">FCFA ce mois</div>
        </div>

        <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-4">
            <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-text/50 mb-1">Net propriétaires</div>
            <div class="font-display font-extrabold text-xl text-bimo-text leading-none">
                {{ number_format($stats['total_net'], 0, ',', ' ') }}
                <span class="font-body font-normal text-sm text-bimo-text/40">F</span>
            </div>
            <div class="font-body text-[10.5px] text-bimo-text/40 mt-1.5">FCFA à reverser</div>
        </div>

        <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-4">
            <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-text/50 mb-1">Commissions TTC</div>
            <div class="font-display font-extrabold text-xl text-bimo-text leading-none">
                {{ number_format($stats['total_commissions'], 0, ',', ' ') }}
                <span class="font-body font-normal text-sm text-bimo-text/40">F</span>
            </div>
            <div class="font-body text-[10.5px] text-bimo-text/40 mt-1.5">FCFA agence</div>
        </div>

        <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-4">
            <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-text/50 mb-1">Paiements validés</div>
            <div class="font-display font-extrabold text-xl text-bimo-text leading-none">
                {{ $stats['nb_payes'] }}
            </div>
            <div class="font-body text-[10.5px] text-bimo-text/40 mt-1.5">Ce mois</div>
        </div>
    </div>

    {{-- ═══ FILTRES ═══ --}}
    <form method="GET" class="flex flex-wrap gap-2 items-center">
        <input type="month" name="mois" value="{{ request('mois') }}"
               onchange="this.form.submit()"
               class="px-3 py-2 bg-white border border-bimo-navy/15 rounded-[9px]
                      font-body text-sm text-bimo-text focus:outline-none focus:border-bimo-gold
                      focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">

        <select name="statut" onchange="this.form.submit()"
                class="px-3 py-2 bg-white border border-bimo-navy/15 rounded-[9px]
                       font-body text-sm text-bimo-text cursor-pointer
                       focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15
                       transition-all duration-150">
            <option value="">Tous les statuts</option>
            <option value="valide"  @selected(request('statut')==='valide')>Validé</option>
            <option value="annule"  @selected(request('statut')==='annule')>Annulé</option>
        </select>

        @if(request()->hasAny(['mois','statut','contrat_id']))
        <a href="{{ route('admin.paiements.index') }}"
           class="px-3 py-2 bg-white border border-bimo-navy/15 rounded-[9px]
                  font-body text-sm text-bimo-text/50 hover:text-bimo-text hover:border-bimo-navy/30
                  transition-all duration-150">
            Effacer les filtres
        </a>
        @endif
    </form>

    {{-- ═══ CONTENU ═══ --}}
    @if($paiements->isEmpty())
    {{-- Empty state --}}
    <div class="bg-white rounded-[14px] border border-bimo-navy/10 py-16 px-6 text-center">
        <div class="w-12 h-12 bg-bimo-gold/10 border border-bimo-gold/20 rounded-[12px] flex items-center justify-center mx-auto mb-4">
            <svg class="w-6 h-6 text-bimo-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/>
            </svg>
        </div>
        <div class="font-display font-bold text-base text-bimo-text mb-2">Aucun paiement trouvé</div>
        <p class="font-body text-sm text-bimo-text/50 mb-5">
            @if(request()->hasAny(['mois','statut']))
                Aucun résultat pour ces filtres.
            @else
                Enregistrez le premier paiement de loyer.
            @endif
        </p>
        <a href="{{ route('admin.paiements.create') }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 bg-[var(--ac)] text-white
                  font-display font-bold text-sm rounded-[10px] hover:opacity-90 transition-opacity duration-150">
            + Enregistrer un paiement
        </a>
    </div>

    @else

    {{-- Mobile : cards --}}
    <div class="md:hidden space-y-3">
        @foreach($paiements as $p)
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
            {{-- Header card --}}
            <div class="flex items-center justify-between px-4 py-3 bg-bimo-bg2 border-b border-bimo-navy/[5%]">
                <div class="flex items-center gap-2">
                    <span class="font-body text-[10px] text-bimo-text/40 uppercase tracking-widest">{{ $p->reference_paiement }}</span>
                    <button onclick="copyRef('{{ $p->reference_paiement }}', this)"
                            class="w-5 h-5 flex items-center justify-center border border-bimo-navy/15 rounded-[4px]
                                   text-bimo-text/30 hover:text-bimo-text hover:border-bimo-navy/30 transition-all duration-150">
                        <svg class="w-2.5 h-2.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="9" y="9" width="13" height="13" rx="2"/>
                            <path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/>
                        </svg>
                    </button>
                </div>
                @if($p->statut === 'valide')
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-body font-medium bg-bimo-gold/10 border border-bimo-gold/20 text-bimo-gold">Validé</span>
                @elseif($p->statut === 'annule')
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-body font-medium bg-bimo-red/10 border border-bimo-red/20 text-bimo-red">Annulé</span>
                @else
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-body font-medium bg-bimo-navy/10 border border-bimo-navy/15 text-bimo-text/60">Attente</span>
                @endif
            </div>

            <div class="px-4 py-3.5 space-y-2.5">
                {{-- Bien / locataire --}}
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <div class="font-body font-semibold text-sm text-bimo-text">{{ $p->contrat?->bien?->reference ?? '—' }}</div>
                        <div class="font-body text-xs text-bimo-text/50">{{ $p->contrat?->locataire?->name ?? '—' }}</div>
                        @php $proprio = $p->contrat?->bien?->proprietaire; @endphp
                        @if($proprio)
                        <a href="{{ route('admin.bailleurs.show', $proprio->id) }}"
                           class="font-body text-[10px] text-bimo-gold hover:text-bimo-text transition-colors duration-150">
                            ↗ {{ $proprio->name }}
                        </a>
                        @endif
                    </div>
                    <div class="text-right flex-shrink-0">
                        <div class="font-display font-bold text-base text-bimo-gold">{{ number_format($p->montant_encaisse, 0, ',', ' ') }} FCFA</div>
                        <div class="font-body text-[10px] text-bimo-text/40 mt-0.5">
                            {{ \Carbon\Carbon::parse($p->periode)->translatedFormat('M Y') }}
                        </div>
                    </div>
                </div>

                {{-- Détails financiers --}}
                <div class="grid grid-cols-3 gap-2 pt-2 border-t border-bimo-navy/[5%]">
                    <div>
                        <div class="font-body text-[9.5px] uppercase tracking-widest text-bimo-text/40 mb-0.5">Commission</div>
                        <div class="font-body font-medium text-xs text-bimo-text/70">{{ number_format($p->commission_ttc ?? 0, 0, ',', ' ') }} FCFA</div>
                    </div>
                    <div>
                        <div class="font-body text-[9.5px] uppercase tracking-widest text-bimo-text/40 mb-0.5">Net proprio</div>
                        <div class="font-body font-medium text-xs text-bimo-text">{{ number_format($p->net_a_verser_proprietaire ?? $p->net_proprietaire ?? 0, 0, ',', ' ') }} FCFA</div>
                    </div>
                    <div>
                        <div class="font-body text-[9.5px] uppercase tracking-widest text-bimo-text/40 mb-0.5">Mode</div>
                        <div class="font-body text-xs text-bimo-text/60">{{ \App\Http\Controllers\PaiementController::MODES_PAIEMENT[$p->mode_paiement] ?? $p->mode_paiement }}</div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-end gap-2 pt-1">
                    <a href="{{ route('admin.paiements.show', $p) }}"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-bimo-bg border border-bimo-navy/10
                              rounded-[7px] font-body text-xs text-bimo-text/60 hover:text-bimo-text transition-all duration-150">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        Voir
                    </a>
                    <a href="{{ route('admin.paiements.pdf', $p) }}" target="_blank"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-bimo-bg border border-bimo-navy/10
                              rounded-[7px] font-body text-xs text-bimo-text/60 hover:text-bimo-text transition-all duration-150">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                        PDF
                    </a>
                    @if($p->statut === 'valide')
                    <form method="POST" action="{{ route('admin.paiements.annuler', $p) }}"
                          data-confirm="Le paiement {{ $p->reference_paiement }} sera annulé définitivement."
                          data-confirm-title="Annuler ce paiement ?"
                          data-confirm-ok="Oui, annuler"
                          data-confirm-color="#d97706"
                          data-confirm-icon-bg="rgba(217,119,6,0.1)">
                        @csrf @method('PATCH')
                        <button type="submit"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-bimo-red/10
                                       border border-bimo-red/20 rounded-[7px] font-body text-xs text-bimo-red
                                       hover:bg-bimo-red/20 transition-all duration-150">
                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                            Annuler
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Desktop : table --}}
    <div class="hidden md:block bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm" data-sort-id="paiements-table">
                <thead>
                    <tr class="border-b border-bimo-navy/[5%] bg-bimo-bg">
                        <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Référence</th>
                        <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40 cursor-pointer hover:text-bimo-gold transition-colors duration-150" data-sort="1">Bien / Locataire</th>
                        <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40 cursor-pointer hover:text-bimo-gold transition-colors duration-150" data-sort="2" data-sort-type="date">Période</th>
                        <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40 cursor-pointer hover:text-bimo-gold transition-colors duration-150" data-sort="3" data-sort-type="date">Date</th>
                        <th class="px-5 py-3 text-right font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40 cursor-pointer hover:text-bimo-gold transition-colors duration-150" data-sort="4" data-sort-type="num">Montant</th>
                        <th class="px-5 py-3 text-right font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40 cursor-pointer hover:text-bimo-gold transition-colors duration-150" data-sort="5" data-sort-type="num">Commission</th>
                        <th class="px-5 py-3 text-right font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40 cursor-pointer hover:text-bimo-gold transition-colors duration-150" data-sort="6" data-sort-type="num">Net proprio</th>
                        <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Mode</th>
                        <th class="px-5 py-3 text-center font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Statut</th>
                        <th class="px-5 py-3 text-center font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-bimo-navy/[5%]">
                    @foreach($paiements as $p)
                    <tr class="hover:bg-bimo-bg2 transition-colors duration-100">

                        {{-- Référence --}}
                        <td class="px-5 py-3.5 whitespace-nowrap">
                            <div class="flex items-center gap-1.5">
                                <span class="font-body text-[11px] text-bimo-text/40 uppercase tracking-widest">{{ $p->reference_paiement }}</span>
                                <button onclick="copyRef('{{ $p->reference_paiement }}', this)"
                                        class="w-5 h-5 flex items-center justify-center border border-bimo-navy/10 rounded-[4px]
                                               text-bimo-text/30 hover:text-bimo-text hover:border-bimo-navy/30 transition-all duration-150">
                                    <svg class="w-2.5 h-2.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="9" y="9" width="13" height="13" rx="2"/>
                                        <path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/>
                                    </svg>
                                </button>
                            </div>
                        </td>

                        {{-- Bien / locataire --}}
                        <td class="px-5 py-3.5">
                            <div class="font-body font-medium text-sm text-bimo-text">{{ $p->contrat?->bien?->reference ?? '—' }}</div>
                            <div class="font-body text-xs text-bimo-text/50">{{ $p->contrat?->locataire?->name ?? '—' }}</div>
                            @php $proprio = $p->contrat?->bien?->proprietaire; @endphp
                            @if($proprio)
                            <a href="{{ route('admin.bailleurs.show', $proprio->id) }}"
                               class="font-body text-[10px] text-bimo-gold hover:text-bimo-text transition-colors duration-150">
                                ↗ {{ $proprio->name }}
                            </a>
                            @endif
                        </td>

                        {{-- Période --}}
                        <td class="px-5 py-3.5">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-display font-semibold bg-bimo-gold/10 border border-bimo-gold/20 text-bimo-gold">
                                {{ \Carbon\Carbon::parse($p->periode)->translatedFormat('M Y') }}
                            </span>
                        </td>

                        {{-- Date --}}
                        <td class="px-5 py-3.5 font-body text-xs text-bimo-text/50">
                            {{ $p->date_paiement ? \Carbon\Carbon::parse($p->date_paiement)->format('d/m/Y') : '—' }}
                        </td>

                        {{-- Montant --}}
                        <td class="px-5 py-3.5 text-right">
                            <span class="font-display font-bold text-sm text-bimo-gold">
                                {{ number_format($p->montant_encaisse, 0, ',', ' ') }} F
                            </span>
                        </td>

                        {{-- Commission --}}
                        <td class="px-5 py-3.5 text-right font-body text-xs text-bimo-text/50">
                            {{ number_format($p->commission_ttc ?? 0, 0, ',', ' ') }} F
                        </td>

                        {{-- Net proprio --}}
                        <td class="px-5 py-3.5 text-right font-body font-semibold text-sm text-bimo-text">
                            {{ number_format($p->net_a_verser_proprietaire ?? $p->net_proprietaire ?? 0, 0, ',', ' ') }} F
                        </td>

                        {{-- Mode --}}
                        <td class="px-5 py-3.5 font-body text-xs text-bimo-text/50">
                            {{ \App\Http\Controllers\PaiementController::MODES_PAIEMENT[$p->mode_paiement] ?? $p->mode_paiement }}
                        </td>

                        {{-- Statut --}}
                        <td class="px-5 py-3.5 text-center">
                            @if($p->statut === 'valide')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-body font-medium bg-bimo-gold/10 border border-bimo-gold/20 text-bimo-gold">Validé</span>
                            @elseif($p->statut === 'annule')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-body font-medium bg-bimo-red/10 border border-bimo-red/20 text-bimo-red">Annulé</span>
                            @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-body font-medium bg-bimo-navy/10 border border-bimo-navy/15 text-bimo-text/60">Attente</span>
                            @endif
                        </td>

                        {{-- Actions --}}
                        <td class="px-5 py-3.5">
                            <div class="flex items-center justify-center gap-1.5">
                                <a href="{{ route('admin.paiements.show', $p) }}"
                                   class="w-9 h-9 flex items-center justify-center border border-bimo-navy/10 rounded-[6px]
                                          text-bimo-text/40 hover:text-bimo-text hover:border-bimo-navy/30 transition-all duration-150"
                                   title="Voir">
                                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                </a>
                                <a href="{{ route('admin.paiements.pdf', $p) }}" target="_blank"
                                   class="w-9 h-9 flex items-center justify-center border border-bimo-navy/10 rounded-[6px]
                                          text-bimo-text/40 hover:text-bimo-text hover:border-bimo-navy/30 transition-all duration-150"
                                   title="PDF">
                                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                </a>
                                @if($p->statut === 'valide')
                                <form method="POST" action="{{ route('admin.paiements.annuler', $p) }}"
                                      data-confirm="Le paiement {{ $p->reference_paiement }} sera annulé définitivement."
                                      data-confirm-title="Annuler ce paiement ?"
                                      data-confirm-ok="Oui, annuler"
                                      data-confirm-color="#d97706"
                                      data-confirm-icon-bg="rgba(217,119,6,0.1)">
                                    @csrf @method('PATCH')
                                    <button type="submit"
                                            class="w-9 h-9 flex items-center justify-center border border-bimo-red/20 rounded-[6px]
                                                   text-bimo-red/60 hover:text-bimo-red hover:border-bimo-red/40 hover:bg-bimo-red/5
                                                   transition-all duration-150"
                                            title="Annuler">
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
        @if($paiements->hasPages())
        <div class="flex items-center justify-between px-5 py-3.5 border-t border-bimo-navy/[5%] bg-bimo-bg">
            <span class="font-body text-xs text-bimo-text/40">
                {{ $paiements->firstItem() }}–{{ $paiements->lastItem() }} sur {{ $paiements->total() }}
            </span>
            <div class="flex items-center gap-1">
                @if(!$paiements->onFirstPage())
                <a href="{{ $paiements->previousPageUrl() }}"
                   class="w-9 h-9 flex items-center justify-center border border-bimo-navy/10 rounded-[6px]
                          text-bimo-text/40 hover:text-bimo-text hover:border-bimo-navy/30 transition-all duration-150">
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                </a>
                @endif
                @foreach($paiements->getUrlRange(max(1,$paiements->currentPage()-2), min($paiements->lastPage(),$paiements->currentPage()+2)) as $page => $url)
                <a href="{{ $url }}"
                   class="w-9 h-9 flex items-center justify-center rounded-[6px] font-body text-xs transition-all duration-150
                          {{ $page === $paiements->currentPage()
                             ? 'bg-bimo-navy text-white border border-bimo-navy'
                             : 'border border-bimo-navy/10 text-bimo-text/60 hover:text-bimo-text hover:border-bimo-navy/30' }}">
                    {{ $page }}
                </a>
                @endforeach
                @if($paiements->hasMorePages())
                <a href="{{ $paiements->nextPageUrl() }}"
                   class="w-9 h-9 flex items-center justify-center border border-bimo-navy/10 rounded-[6px]
                          text-bimo-text/40 hover:text-bimo-text hover:border-bimo-navy/30 transition-all duration-150">
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
