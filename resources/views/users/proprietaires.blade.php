@extends('layouts.app')
@section('header', 'Propriétaires')

@section('content')
<div class="space-y-4 md:space-y-6">

    {{-- En-tête --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <h1 class="font-display font-extrabold text-xl md:text-2xl text-bimo-navy tracking-tight leading-tight">Propriétaires</h1>
            <p class="font-body text-sm text-bimo-navy/50 mt-1">{{ $stats['total'] }} propriétaire(s) enregistré(s)</p>
        </div>
        <a href="{{ route('admin.users.create', 'proprietaire') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-[var(--ac)] text-white
                  font-display font-bold text-sm rounded-[10px] hover:opacity-90 transition-opacity duration-150 self-start">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Nouveau propriétaire
        </a>
    </div>

    {{-- KPIs --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <div class="bg-bimo-gold/[8%] rounded-[14px] border border-bimo-gold/25 p-4">
            <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-gold/70 mb-1">Total propriétaires</div>
            <div class="font-display font-extrabold text-2xl text-bimo-gold leading-none">{{ $stats['total'] }}</div>
            <div class="font-body text-[10.5px] text-bimo-gold/60 mt-1.5">Enregistrés dans l'agence</div>
        </div>
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-4">
            <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-navy/50 mb-1">Biens gérés</div>
            <div class="font-display font-extrabold text-2xl text-bimo-navy leading-none">{{ $stats['total_biens'] }}</div>
            <div class="font-body text-[10.5px] text-bimo-navy/40 mt-1.5">{{ $stats['biens_loues'] }} loué(s) · {{ $stats['total_biens'] - $stats['biens_loues'] }} disponible(s)</div>
        </div>
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-4">
            <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-navy/50 mb-1">Taux d'occupation</div>
            @php $taux = $stats['total_biens'] > 0 ? round($stats['biens_loues']/$stats['total_biens']*100) : 0; @endphp
            <div class="font-display font-extrabold text-2xl leading-none {{ $taux >= 80 ? 'text-bimo-gold' : 'text-bimo-navy' }}">{{ $taux }}%</div>
            <div class="font-body text-[10.5px] text-bimo-navy/40 mt-1.5">Biens avec locataire actif</div>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
        <div class="flex flex-wrap items-center justify-between gap-3 px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
            <span class="font-display font-bold text-sm text-bimo-navy">Liste des propriétaires</span>
            <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-bimo-navy/30 pointer-events-none"
                     viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input type="text" placeholder="Rechercher..." id="search-input" oninput="filterTable(this.value)"
                       class="pl-9 pr-3 py-2 bg-white border border-bimo-navy/15 rounded-[9px] font-body text-sm text-bimo-navy
                              placeholder:text-bimo-navy/30 focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15
                              transition-all duration-150 w-52">
            </div>
        </div>

        {{-- Mobile cards --}}
        <div class="md:hidden divide-y divide-bimo-navy/[5%]" id="mobile-list">
            @forelse($proprietaires as $user)
            @php $profil = $user->proprietaire; @endphp
            <div class="px-5 py-4" data-search="{{ strtolower($user->name . ' ' . $user->email . ' ' . $user->telephone) }}">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-[10px] flex items-center justify-center font-display font-bold text-sm text-bimo-navy flex-shrink-0"
                         style="background: var(--ac)">
                        {{ mb_strtoupper(mb_substr($user->name, 0, 2)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="font-body font-semibold text-sm text-bimo-navy truncate">{{ $user->name }}</div>
                        <div class="font-body text-xs text-bimo-navy/50">{{ $user->email }}</div>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-body font-medium
                                 {{ $user->biens_count > 0 ? 'bg-bimo-gold/10 border border-bimo-gold/20 text-bimo-gold' : 'bg-bimo-navy/10 border border-bimo-navy/15 text-bimo-navy/60' }}">
                        {{ $user->biens_count }} bien(s)
                    </span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="font-body text-xs text-bimo-navy/50">
                        @if($profil?->ville) {{ $profil->ville }} · @endif
                        Depuis {{ $user->created_at->format('M Y') }}
                    </div>
                    <div class="flex items-center gap-1.5">
                        <a href="{{ route('admin.users.show', $user) }}"
                           class="w-7 h-7 flex items-center justify-center border border-bimo-navy/10 rounded-[6px] text-bimo-navy/40 hover:text-bimo-navy hover:border-bimo-navy/30 transition-all duration-150">
                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </a>
                        <a href="{{ route('admin.users.edit', $user) }}"
                           class="w-7 h-7 flex items-center justify-center border border-bimo-navy/10 rounded-[6px] text-bimo-navy/40 hover:text-bimo-navy hover:border-bimo-navy/30 transition-all duration-150">
                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <div class="px-5 py-16 text-center">
                <div class="text-4xl mb-3">🏢</div>
                <div class="font-display font-bold text-base text-bimo-navy mb-2">Aucun propriétaire enregistré</div>
                <p class="font-body text-sm text-bimo-navy/50 mb-5">Commencez par ajouter le premier propriétaire de votre agence.</p>
                <a href="{{ route('admin.users.create', 'proprietaire') }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-[var(--ac)] text-white font-display font-bold text-sm rounded-[10px] hover:opacity-90 transition-opacity duration-150">
                    + Ajouter un propriétaire
                </a>
            </div>
            @endforelse
        </div>

        {{-- Desktop table --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-sm" id="dt-proprio">
                <thead>
                    <tr class="border-b border-bimo-navy/[5%] bg-bimo-bg">
                        <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40">Propriétaire</th>
                        <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40">Contact</th>
                        <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widests text-bimo-navy/40">Ville</th>
                        <th class="px-5 py-3 text-center font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40">Biens</th>
                        <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40">Paiement</th>
                        <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40">NINEA</th>
                        <th class="px-5 py-3 text-center font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-bimo-navy/[5%]">
                    @forelse($proprietaires as $user)
                    @php
                        $profil = $user->proprietaire;
                        $modeIcons  = ['virement'=>'🏦','wave'=>'📱','orange_money'=>'🟠','especes'=>'💵','cheque'=>'📝','mobile_money'=>'📲'];
                        $modeLabels = ['virement'=>'Virement','wave'=>'Wave','orange_money'=>'Orange Money','especes'=>'Espèces','cheque'=>'Chèque','mobile_money'=>'Mobile Money'];
                        $mode = $profil?->mode_paiement_prefere ?? 'virement';
                    @endphp
                    <tr class="hover:bg-bimo-bg transition-colors duration-100">
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-[9px] flex items-center justify-center font-display font-bold text-sm text-bimo-navy flex-shrink-0"
                                     style="background: var(--ac)">
                                    {{ mb_strtoupper(mb_substr($user->name, 0, 2)) }}
                                </div>
                                <div>
                                    <div class="font-body font-semibold text-sm text-bimo-navy">{{ $user->name }}</div>
                                    <div class="font-body text-[11px] text-bimo-navy/40">Depuis {{ $user->created_at->format('M Y') }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3.5">
                            <div class="font-body text-sm text-bimo-navy">{{ $user->email }}</div>
                            @if($user->telephone)
                            <div class="font-body text-xs text-bimo-navy/50 mt-0.5">{{ $user->telephone }}</div>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 font-body text-sm text-bimo-navy/70">{{ $profil?->ville ?? '—' }}</td>
                        <td class="px-5 py-3.5 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-body font-medium
                                         {{ $user->biens_count > 0 ? 'bg-bimo-gold/10 border border-bimo-gold/20 text-bimo-gold' : 'bg-bimo-navy/10 border border-bimo-navy/15 text-bimo-navy/50' }}">
                                {{ $user->biens_count > 0 ? $user->biens_count . ' bien(s)' : 'Aucun' }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5">
                            @if($mode)
                            <div class="flex items-center gap-1.5 font-body text-sm text-bimo-navy/70">
                                <span>{{ $modeIcons[$mode] ?? '💳' }}</span>
                                {{ $modeLabels[$mode] ?? ucfirst($mode) }}
                            </div>
                            @else
                            <span class="font-body text-sm text-bimo-navy/30">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5">
                            @if($profil?->ninea)
                            <span class="font-body text-[11px] bg-bimo-bg px-2 py-1 rounded-[5px] text-bimo-navy/60">{{ $profil->ninea }}</span>
                            @else
                            <span class="font-body text-sm text-bimo-navy/30">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5">
                            <div class="flex items-center justify-center gap-1.5">
                                <a href="{{ route('admin.users.show', $user) }}"
                                   class="w-7 h-7 flex items-center justify-center border border-bimo-navy/10 rounded-[6px] text-bimo-navy/40 hover:text-bimo-navy hover:border-bimo-navy/30 transition-all duration-150"
                                   title="Voir">
                                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                </a>
                                <a href="{{ route('admin.users.edit', $user) }}"
                                   class="w-7 h-7 flex items-center justify-center border border-bimo-navy/10 rounded-[6px] text-bimo-navy/40 hover:text-bimo-navy hover:border-bimo-navy/30 transition-all duration-150"
                                   title="Modifier">
                                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                </a>
                                @if(config('features.fiscalite'))
                                <a href="{{ route('admin.bilans-fiscaux.show', [$user, 'annee' => now()->year]) }}"
                                   class="w-7 h-7 flex items-center justify-center border border-bimo-navy/10 rounded-[6px] text-bimo-navy/40 hover:text-bimo-gold hover:border-bimo-gold/30 transition-all duration-150"
                                   title="Bilan fiscal">
                                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-5 py-16 text-center">
                            <div class="text-4xl mb-3">🏢</div>
                            <div class="font-display font-bold text-base text-bimo-navy mb-2">Aucun propriétaire enregistré</div>
                            <p class="font-body text-sm text-bimo-navy/50 mb-5">Commencez par ajouter le premier propriétaire.</p>
                            <a href="{{ route('admin.users.create', 'proprietaire') }}"
                               class="inline-flex items-center gap-2 px-5 py-2.5 bg-[var(--ac)] text-white font-display font-bold text-sm rounded-[10px] hover:opacity-90 transition-opacity duration-150">
                                + Ajouter un propriétaire
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($proprietaires->hasPages())
        <div class="px-5 py-3.5 border-t border-bimo-navy/[5%] bg-bimo-bg">
            {{ $proprietaires->links() }}
        </div>
        @endif
    </div>

</div>

@push('scripts')
<script>
function filterTable(q) {
    q = q.toLowerCase();
    // Desktop
    document.querySelectorAll('#dt-proprio tbody tr').forEach(tr => {
        tr.style.display = tr.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
    // Mobile
    document.querySelectorAll('#mobile-list [data-search]').forEach(el => {
        el.style.display = el.dataset.search.includes(q) ? '' : 'none';
    });
}
</script>
@endpush

@endsection
