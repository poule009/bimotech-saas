@extends('layouts.app')
@section('header', 'Plateforme')

@section('content')

@php
$expirantBientot = $agences->filter(function($a) {
    $sub = $a->subscription;
    if (!$sub) return false;
    $date = $sub->statut === 'essai' ? $sub->date_fin_essai : $sub->date_fin_abonnement;
    return $date && \Carbon\Carbon::parse($date)->diffInDays(now(), false) >= -7 && \Carbon\Carbon::parse($date)->isFuture();
});
@endphp

<div class="space-y-4 md:space-y-5">

    {{-- Alerte expiration --}}
    @if($expirantBientot->count() > 0)
    <div class="flex items-start gap-3 bg-bimo-gold/[8%] border border-bimo-gold/25 rounded-[10px] px-4 py-3">
        <svg class="w-4 h-4 text-bimo-gold flex-shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
        <p class="font-body text-sm text-bimo-gold"><strong>{{ $expirantBientot->count() }} agence(s)</strong> expirent dans moins de 7 jours : {{ $expirantBientot->pluck('name')->join(', ') }}</p>
    </div>
    @endif

    {{-- KPIs --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        @foreach([
            ['Agences', $stats['nb_agences'], $stats['nb_agences_actives'].' actives'],
            ['Abonnements actifs', $stats['nb_abonnements_actifs'], $stats['nb_essai'].' en essai'],
            ['Biens gérés', $stats['nb_biens'], $stats['nb_contrats'].' contrats actifs'],
            ['Utilisateurs', $stats['nb_users'], 'Tous rôles confondus'],
        ] as [$lbl, $val, $sub])
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-4">
            <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-text/50 mb-1.5">{{ $lbl }}</div>
            <div class="font-display font-extrabold text-2xl text-bimo-text leading-none">{{ $val }}</div>
            <div class="font-body text-[10.5px] text-bimo-text/40 mt-1.5">{{ $sub }}</div>
        </div>
        @endforeach
    </div>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <div class="bg-bimo-gold/[8%] rounded-[14px] border border-bimo-gold/25 p-4 col-span-1">
            <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-gold/70 mb-1.5">Loyers encaissés</div>
            <div class="font-display font-extrabold text-lg text-bimo-gold leading-none">{{ number_format($stats['total_loyers'],0,',','') }}</div>
            <div class="font-body text-[10.5px] text-bimo-gold/60 mt-1.5">FCFA — toutes agences</div>
        </div>
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-4">
            <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-text/50 mb-1.5">Commissions plateforme</div>
            <div class="font-display font-extrabold text-lg text-bimo-text leading-none">{{ number_format($stats['total_commissions'],0,',','') }}</div>
            <div class="font-body text-[10.5px] text-bimo-text/40 mt-1.5">FCFA TTC cumulés</div>
        </div>
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-4">
            <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-text/50 mb-1.5">Revenus abonnements</div>
            <div class="font-display font-extrabold text-lg text-bimo-text leading-none">{{ number_format($stats['revenus_abonnements'],0,',','') }}</div>
            <div class="font-body text-[10.5px] text-bimo-text/40 mt-1.5">FCFA encaissés</div>
        </div>
        <div class="bg-white rounded-[14px] border {{ $stats['nb_expires'] > 0 ? 'border-bimo-red/20' : 'border-bimo-navy/10' }} p-4">
            <div class="font-body font-medium text-[9.5px] uppercase tracking-widest {{ $stats['nb_expires'] > 0 ? 'text-bimo-red/70' : 'text-bimo-text/50' }} mb-1.5">Expirés</div>
            <div class="font-display font-extrabold text-2xl {{ $stats['nb_expires'] > 0 ? 'text-bimo-red' : 'text-bimo-text' }} leading-none">{{ $stats['nb_expires'] }}</div>
            <div class="font-body text-[10.5px] {{ $stats['nb_expires'] > 0 ? 'text-bimo-red/60' : 'text-bimo-text/40' }} mt-1.5">{{ $stats['nb_expires'] > 0 ? 'À relancer' : 'Aucun' }}</div>
        </div>
    </div>

    {{-- Graphique 12 mois --}}
    <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2 flex-wrap gap-2">
            <div>
                <span class="font-display font-bold text-sm text-bimo-text">Évolution sur 12 mois</span>
                <div class="font-body text-xs text-bimo-text/40 mt-0.5">Revenus abonnements &amp; nouvelles agences</div>
            </div>
            <div class="flex items-center gap-4 font-body text-xs text-bimo-text/40">
                <span class="flex items-center gap-1.5"><span class="w-3 h-0.5 bg-bimo-gold inline-block rounded"></span>Revenus (F)</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 bg-bimo-gold/15 border border-bimo-gold/20 inline-block rounded-sm"></span>Nouvelles agences</span>
            </div>
        </div>
        <div class="px-5 py-5" style="position:relative;height:240px">
            <canvas id="sa-chart"></canvas>
        </div>
    </div>

    {{-- Table agences --}}
    <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
            <div>
                <span class="font-display font-bold text-sm text-bimo-text">Toutes les agences</span>
                <div class="font-body text-xs text-bimo-text/40 mt-0.5"><span id="sa-count">{{ $agences->count() }}</span> agence(s)</div>
            </div>
            <a href="{{ route('superadmin.agencies.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-[var(--ac)] text-white font-display font-bold text-sm rounded-[10px] hover:opacity-90 transition-opacity duration-150">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Nouvelle agence
            </a>
        </div>

        {{-- Barre filtre --}}
        <div class="flex items-center gap-2 px-5 py-3 border-b border-bimo-navy/[5%] flex-wrap">
            <div class="relative flex-1 min-w-[180px] max-w-xs">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-bimo-text/30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" id="sa-search" placeholder="Rechercher une agence…"
                       class="w-full pl-9 pr-3 py-2 border border-bimo-navy/15 rounded-[8px] font-body text-sm text-bimo-text bg-bimo-bg focus:outline-none focus:border-bimo-gold focus:bg-white transition-all duration-150"
                       oninput="saFilter()">
            </div>
            @foreach([['tous','Tous'],['essai','Essai'],['actif','Actifs'],['expire','Expirés'],['suspendu','Suspendus']] as [$val,$lbl])
            <button class="filter-tab px-3.5 py-1.5 rounded-full font-body font-medium text-xs border border-bimo-navy/15 text-bimo-text/50 hover:border-bimo-gold hover:text-bimo-gold transition-all duration-150 {{ $val === 'tous' ? 'bg-bimo-navy text-white border-bimo-navy' : '' }}"
                    data-filter="{{ $val }}" onclick="saSetFilter(this,'{{ $val }}')">{{ $lbl }}</button>
            @endforeach
        </div>

        {{-- Mobile --}}
        <div class="md:hidden divide-y divide-bimo-navy/[5%]" id="sa-mobile-list">
            @forelse($agences as $agence)
            @php
                $sub = $agence->subscription;
                $date = $sub ? ($sub->statut === 'essai' ? $sub->date_fin_essai : $sub->date_fin_abonnement) : null;
                $jours = $date ? (int)\Carbon\Carbon::parse($date)->diffInDays(now(), false) * -1 : null;
                $filterStatut = !$agence->actif ? 'suspendu' : ($sub ? $sub->statut : 'aucun');
            @endphp
            <div class="px-4 py-3.5 {{ !$agence->actif ? 'opacity-50' : '' }}" data-name="{{ strtolower($agence->name) }} {{ strtolower($agence->email) }}" data-statut="{{ $filterStatut }}">
                <div class="flex items-center justify-between gap-2 mb-1">
                    <a href="{{ route('superadmin.agencies.show', $agence) }}" class="font-body font-semibold text-sm text-bimo-text">{{ $agence->name }}</a>
                    @if($agence->actif)
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-body font-semibold bg-bimo-gold/10 border border-bimo-gold/20 text-bimo-gold">Active</span>
                    @else
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-body font-semibold bg-bimo-red/10 border border-bimo-red/20 text-bimo-red">Suspendue</span>
                    @endif
                </div>
                <div class="font-body text-xs text-bimo-text/40">{{ $agence->email }}</div>
            </div>
            @empty
            <div class="px-5 py-10 text-center font-body text-sm text-bimo-text/30">Aucune agence.</div>
            @endforelse
        </div>

        {{-- Desktop --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-sm" id="sa-table">
                <thead>
                    <tr class="border-b border-bimo-navy/[5%] bg-bimo-bg">
                        <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Agence</th>
                        <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Abonnement</th>
                        <th class="px-5 py-3 text-center font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Statut</th>
                        <th class="px-5 py-3 text-center font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Biens</th>
                        <th class="px-5 py-3 text-center font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Contrats</th>
                        <th class="px-5 py-3 text-right font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Loyers</th>
                        <th class="px-5 py-3 text-right font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Commissions</th>
                        <th class="px-5 py-3 text-center font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Depuis</th>
                        <th class="px-5 py-3 text-center font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-bimo-navy/[5%]">
                    @forelse($agences as $agence)
                    @php
                        $sub = $agence->subscription;
                        $date = $sub ? ($sub->statut === 'essai' ? $sub->date_fin_essai : $sub->date_fin_abonnement) : null;
                        $jours = $date ? (int)\Carbon\Carbon::parse($date)->diffInDays(now(), false) * -1 : null;
                        $filterStatut = !$agence->actif ? 'suspendu' : ($sub ? $sub->statut : 'aucun');
                    @endphp
                    <tr class="hover:bg-bimo-bg transition-colors duration-100 {{ !$agence->actif ? 'opacity-50' : '' }}"
                        data-name="{{ strtolower($agence->name) }} {{ strtolower($agence->email) }}"
                        data-statut="{{ $filterStatut }}">
                        <td class="px-5 py-3.5">
                            <a href="{{ route('superadmin.agencies.show', $agence) }}" class="font-body font-semibold text-sm text-bimo-text hover:text-bimo-gold transition-colors duration-150">{{ $agence->name }}</a>
                            <div class="font-body text-[11px] text-bimo-text/40 mt-0.5">{{ $agence->email }}</div>
                        </td>
                        <td class="px-5 py-3.5">
                            @if($sub)
                                @if($sub->statut === 'essai')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-body font-semibold bg-bimo-gold/10 border border-bimo-gold/20 text-bimo-gold"><span class="w-1.5 h-1.5 rounded-full bg-bimo-gold mr-1"></span>Essai</span>
                                @elseif($sub->statut === 'actif')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-body font-semibold bg-bimo-navy/10 border border-bimo-navy/15 text-bimo-text/70"><span class="w-1.5 h-1.5 rounded-full bg-bimo-navy/50 mr-1"></span>{{ ucfirst($sub->plan ?? 'Actif') }}</span>
                                @elseif($sub->statut === 'expiré')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-body font-semibold bg-bimo-red/10 border border-bimo-red/20 text-bimo-red"><span class="w-1.5 h-1.5 rounded-full bg-bimo-red mr-1"></span>Expiré</span>
                                @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-body font-medium bg-bimo-navy/[5%] text-bimo-text/50">{{ $sub->statut }}</span>
                                @endif
                                @if($jours !== null)
                                <div class="font-body text-[10px] mt-1 {{ $jours <= 7 ? 'text-bimo-red' : ($jours <= 30 ? 'text-bimo-gold' : 'text-bimo-text/30') }}">
                                    {{ $jours >= 0 ? $jours.' j restants' : abs($jours).' j dépassés' }}
                                </div>
                                @endif
                            @else
                                <span class="font-body text-xs text-bimo-text/30">Aucun</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            @if($agence->actif)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-body font-semibold bg-bimo-navy/10 border border-bimo-navy/15 text-bimo-text/70"><span class="w-1.5 h-1.5 rounded-full bg-bimo-navy/50 mr-1"></span>Active</span>
                            @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-body font-semibold bg-bimo-red/10 border border-bimo-red/20 text-bimo-red"><span class="w-1.5 h-1.5 rounded-full bg-bimo-red mr-1"></span>Suspendue</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-center font-body text-sm text-bimo-text/60">{{ $agence->biens_count }}</td>
                        <td class="px-5 py-3.5 text-center font-body text-sm text-bimo-text/60">{{ $agence->contrats_count }}</td>
                        <td class="px-5 py-3.5 text-right font-display font-semibold text-sm text-bimo-text/70">{{ number_format($agence->total_loyers,0,',','') }}</td>
                        <td class="px-5 py-3.5 text-right font-display font-bold text-sm text-bimo-gold">{{ number_format($agence->total_commissions,0,',','') }}</td>
                        <td class="px-5 py-3.5 text-center font-body text-xs text-bimo-text/40">{{ $agence->created_at->format('d/m/Y') }}</td>
                        <td class="px-5 py-3.5 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('superadmin.agencies.show', $agence) }}"
                                   class="inline-flex items-center px-3 py-1.5 border border-bimo-navy/15 rounded-[7px] font-body text-xs text-bimo-text/60 hover:border-bimo-navy/30 hover:text-bimo-text transition-all duration-150">
                                    Détail
                                </a>
                                <form method="POST" action="{{ route('superadmin.agencies.toggle', $agence) }}"
                                      data-confirm="{{ $agence->actif ? 'Suspendre '.$agence->name.' ?' : 'Activer '.$agence->name.' ?' }}">
                                    @csrf @method('PATCH')
                                    <button type="submit"
                                            class="inline-flex items-center px-3 py-1.5 rounded-[7px] font-body text-xs border transition-all duration-150 {{ $agence->actif ? 'border-bimo-red/20 bg-bimo-red/[5%] text-bimo-red hover:bg-bimo-red/10' : 'border-bimo-navy/15 text-bimo-text/60 hover:border-bimo-navy/30 hover:text-bimo-text' }}">
                                        {{ $agence->actif ? 'Suspendre' : 'Activer' }}
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-5 py-12 text-center font-body text-sm text-bimo-text/30">
                            Aucune agence.
                            <a href="{{ route('superadmin.agencies.create') }}" class="text-bimo-gold hover:text-bimo-text ml-1 transition-colors duration-150">Créer la première →</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div id="sa-empty-filter" class="hidden px-5 py-12 text-center font-body text-sm text-bimo-text/30">
            Aucune agence ne correspond à la recherche.
        </div>
    </div>

</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js"></script>
<script>
(function(){
    new Chart(document.getElementById('sa-chart'), {
        data:{
            labels:@json($chartLabels),
            datasets:[
                {type:'line',label:'Revenus (F)',data:@json($chartRevenus),borderColor:'var(--ac)',backgroundColor:'rgba(201,168,76,.08)',borderWidth:2,pointRadius:3,fill:true,tension:.35,yAxisID:'yRev'},
                {type:'bar',label:'Nouvelles agences',data:@json($chartAgences),backgroundColor:'rgba(201,168,76,.12)',borderColor:'rgba(201,168,76,.3)',borderWidth:1,borderRadius:4,yAxisID:'yAgc'}
            ]
        },
        options:{
            responsive:true,maintainAspectRatio:false,
            interaction:{mode:'index',intersect:false},
            plugins:{legend:{display:false},tooltip:{callbacks:{label:function(c){return c.dataset.yAxisID==='yRev'?' '+c.parsed.y.toLocaleString('fr-FR')+' F':' '+c.parsed.y+' agence(s)'}}}},
            scales:{
                x:{grid:{display:false},ticks:{font:{size:10},color:'rgba(27,79,107,.4)'}},
                yRev:{position:'left',grid:{color:'rgba(27,79,107,.05)'},ticks:{font:{size:10},color:'rgba(27,79,107,.4)',callback:function(v){return v>=1000?(v/1000).toFixed(0)+'k':v}}},
                yAgc:{position:'right',grid:{display:false},ticks:{font:{size:10},color:'rgba(27,79,107,.4)',stepSize:1},min:0}
            }
        }
    });
})();

var saCurrentFilter = 'tous';
function saSetFilter(btn, filter) {
    saCurrentFilter = filter;
    document.querySelectorAll('.filter-tab').forEach(function(b) {
        b.classList.remove('bg-bimo-navy','text-white','border-bimo-navy');
        b.classList.add('text-bimo-text/50','border-bimo-navy/15');
    });
    btn.classList.add('bg-bimo-navy','text-white','border-bimo-navy');
    btn.classList.remove('text-bimo-text/50','border-bimo-navy/15');
    saFilter();
}
function saFilter() {
    var q = (document.getElementById('sa-search').value || '').toLowerCase().trim();
    var rows = document.querySelectorAll('#sa-table tbody tr[data-name], #sa-mobile-list [data-name]');
    var visible = 0;
    rows.forEach(function(row) {
        var nameMatch = !q || row.dataset.name.includes(q);
        var statut = row.dataset.statut;
        var filterMatch = saCurrentFilter === 'tous'
            || (saCurrentFilter === 'expire' && statut === 'expiré')
            || (saCurrentFilter === 'suspendu' && statut === 'suspendu')
            || statut === saCurrentFilter;
        var show = nameMatch && filterMatch;
        row.style.display = show ? '' : 'none';
        if (show) visible++;
    });
    document.getElementById('sa-count').textContent = visible;
    var emptyEl = document.getElementById('sa-empty-filter');
    if (emptyEl) emptyEl.style.display = (visible === 0 && rows.length > 0) ? 'block' : 'none';
}
</script>
@endpush

@endsection
