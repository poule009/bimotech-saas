@extends('layouts.app')

@php
    $fmt = fn ($n) => number_format((float) $n, 0, ',', ' ');
    $wa  = function ($tel) {
        $d = preg_replace('/\D/', '', (string) $tel);
        if ($d !== '' && ! str_starts_with($d, '221')) $d = '221' . ltrim($d, '0');
        return $d !== '' ? 'https://wa.me/' . $d : null;
    };
    $sev = [
        'crit' => ['dot' => 'bg-crit',  'border' => 'border-crit',  'pill' => 'bg-crit/10 text-crit'],
        'late' => ['dot' => 'bg-error', 'border' => 'border-error', 'pill' => 'bg-error/10 text-error'],
        'mid'  => ['dot' => 'bg-amber', 'border' => 'border-amber', 'pill' => 'bg-amber/10 text-amber'],
        'soon' => ['dot' => 'bg-gold',  'border' => 'border-gold',  'pill' => 'bg-gold/15 text-gold'],
    ];
    $totalRetard = collect($buckets)->sum(fn ($b) => $b['items']->count());
@endphp

@section('title', 'Quittances')
@section('page-title', 'Paiements & Quittances')
@section('page-subtitle', "Marquer un paiement ici met à jour le contrat correspondant — une seule source, deux vues.")

@section('content')
<div class="max-w-[1180px]">

    @if(session('success'))
        <div class="mb-5 rounded-lg bg-green/10 border border-green/25 px-4 py-3 text-[13px] text-green">{{ session('success') }}</div>
    @endif
    @if(session('info'))
        <div class="mb-5 rounded-lg bg-teal/10 border border-teal/25 px-4 py-3 text-[13px] text-teal">{{ session('info') }}</div>
    @endif

    {{-- KPIs --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white border border-line rounded-xl p-5">
            <div class="text-[12px] text-muted font-bold mb-2.5">Attendu ce mois</div>
            <div class="font-display font-semibold text-[24px]">{{ $fmt($kpis['attendu']) }} <span class="text-[14px] text-muted font-body">F</span></div>
            <div class="text-[11.5px] text-muted mt-1">{{ $kpis['nb_actifs'] }} contrat{{ $kpis['nb_actifs'] > 1 ? 's' : '' }} actif{{ $kpis['nb_actifs'] > 1 ? 's' : '' }}</div>
        </div>
        <div class="bg-white border border-line rounded-xl p-5">
            <div class="text-[12px] text-muted font-bold mb-2.5">Encaissé</div>
            <div class="font-display font-semibold text-[24px] text-green">{{ $fmt($kpis['encaisse']) }} <span class="text-[14px] text-muted font-body">F</span></div>
            <div class="text-[11.5px] text-muted mt-1">{{ $kpis['nb_payes'] }} quittance{{ $kpis['nb_payes'] > 1 ? 's' : '' }} payée{{ $kpis['nb_payes'] > 1 ? 's' : '' }}</div>
        </div>
        <div class="bg-white border border-line rounded-xl p-5">
            <div class="text-[12px] text-muted font-bold mb-2.5">En retard</div>
            <div class="font-display font-semibold text-[24px] text-error">{{ $fmt($kpis['en_retard']) }} <span class="text-[14px] text-muted font-body">F</span></div>
            <div class="text-[11.5px] text-muted mt-1">{{ $kpis['nb_retard'] }} locataire{{ $kpis['nb_retard'] > 1 ? 's' : '' }} concerné{{ $kpis['nb_retard'] > 1 ? 's' : '' }}</div>
        </div>
        <div class="bg-white border border-line rounded-xl p-5">
            <div class="text-[12px] text-muted font-bold mb-2.5">Retards critiques</div>
            <div class="font-display font-semibold text-[24px] text-crit">{{ $kpis['critiques'] }}</div>
            <div class="text-[11.5px] text-muted mt-1">30 jours et plus</div>
        </div>
    </div>

    {{-- Barre d'outils --}}
    <div class="flex flex-col sm:flex-row sm:items-center gap-3 mb-6">
        <form method="GET" class="flex-1 max-w-[320px]">
            @if($filter)<input type="hidden" name="filter" value="{{ $filter }}">@endif
            <div class="flex items-center gap-2.5 bg-white border border-line rounded-[11px] px-4 py-2.5">
                <svg class="w-4 h-4 text-muted shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                <input type="text" name="q" value="{{ $q }}" placeholder="Rechercher un locataire, un bien…" class="w-full bg-transparent outline-none text-[14px] text-ink placeholder:text-muted">
            </div>
        </form>
        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('admin.paiements.index', array_filter(['q' => $q])) }}" @class(['text-[13px] font-bold rounded-full px-4 py-2 border transition-colors','bg-teal text-paper border-teal'=>!$filter,'bg-white text-muted border-line hover:border-teal'=>$filter])>Tous</a>
            <a href="{{ route('admin.paiements.index', array_filter(['q' => $q, 'filter' => 'retard'])) }}" @class(['text-[13px] font-bold rounded-full px-4 py-2 border flex items-center gap-1.5 transition-colors','bg-teal text-paper border-teal'=>$filter==='retard','bg-white text-muted border-line hover:border-teal'=>$filter!=='retard'])>En retard <span class="opacity-70 text-[11px]">{{ $totalRetard }}</span></a>
            <a href="{{ route('admin.paiements.index', array_filter(['q' => $q, 'filter' => 'payees'])) }}" @class(['text-[13px] font-bold rounded-full px-4 py-2 border flex items-center gap-1.5 transition-colors','bg-teal text-paper border-teal'=>$filter==='payees','bg-white text-muted border-line hover:border-teal'=>$filter!=='payees'])>Payées <span class="opacity-70 text-[11px]">{{ $payes->count() }}</span></a>
        </div>
    </div>

    {{-- Groupes de retard --}}
    @if($filter !== 'payees')
        @php $rienEnRetard = $totalRetard === 0; @endphp
        @if($rienEnRetard)
            <div class="bg-white border border-line rounded-2xl py-12 text-center text-muted text-[14px] mb-4 flex items-center justify-center gap-2"><x-icon name="check-circle" size="18" class="text-green" /> Aucun retard en ce moment.</div>
        @endif
        @foreach($buckets as $key => $bucket)
            @continue($bucket['items']->isEmpty())
            <div class="mb-5" x-data="severityGroup" data-open="true">
                <div class="flex items-center gap-3 py-3 cursor-pointer" x-on:click="toggle">
                    <span class="w-3 h-3 rounded-full shrink-0 {{ $sev[$key]['dot'] }}"></span>
                    <span class="font-display font-semibold text-[16px]">{{ $bucket['titre'] }}</span>
                    <span class="text-[12.5px] text-muted font-semibold">{{ $bucket['items']->count() }} quittance{{ $bucket['items']->count() > 1 ? 's' : '' }}</span>
                    <svg x-bind:class="chevClass" class="ml-auto w-4 h-4 text-muted transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>
                </div>
                <div x-show="open" class="space-y-2.5">
                    @foreach($bucket['items'] as $p)
                        @php $loc = $p->contrat?->locataire; $bien = $p->contrat?->bien; $waLink = $wa($loc?->telephone); @endphp
                        <div class="flex flex-wrap items-center gap-4 p-4 bg-white rounded-xl border-l-4 {{ $sev[$key]['border'] }} border-y border-r border-line">
                            <span class="w-[42px] h-[42px] rounded-[11px] bg-teal text-paper flex items-center justify-center text-[13px] font-bold shrink-0">{{ mb_strtoupper(mb_substr($loc?->name ?? '?', 0, 2)) }}</span>
                            <div class="flex-1 min-w-[140px]">
                                <div class="font-bold text-[14.5px] truncate">{{ $loc?->name ?? 'Locataire' }}</div>
                                <div class="text-[12.5px] text-muted truncate">{{ $bien?->titre ?: $bien?->reference }}</div>
                            </div>
                            <div class="text-[13px] text-muted w-[90px]">{{ \Carbon\Carbon::parse($p->periode)->locale('fr')->isoFormat('MMM Y') }}</div>
                            <div class="font-bold text-[14.5px] w-[100px]">{{ $fmt($p->montant_encaisse) }} F</div>
                            <span class="inline-flex items-center gap-1.5 text-[12px] font-bold px-3 py-1.5 rounded-full whitespace-nowrap {{ $sev[$key]['pill'] }}"><span class="w-1.5 h-1.5 rounded-full bg-current"></span> Retard {{ $p->jours_retard }}j</span>
                            <div class="ml-auto flex items-center gap-2 shrink-0">
                                <form method="POST" action="{{ route('admin.paiements.marquer-paye', $p) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="text-[12.5px] font-bold px-3.5 py-2 rounded-lg bg-teal text-paper hover:bg-teal-deep transition-colors whitespace-nowrap">Marquer payé</button>
                                </form>
                                @if($waLink)
                                    <a href="{{ $waLink }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 text-[12.5px] font-bold px-3.5 py-2 rounded-lg bg-paper-dim text-muted hover:text-ink transition-colors"><x-icon-whatsapp /> Relancer</a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    @endif

    {{-- Groupe payées --}}
    @if($filter !== 'retard' && $payes->isNotEmpty())
        <div class="mb-5" x-data="severityGroup" data-open="{{ $filter === 'payees' ? 'true' : 'false' }}">
            <div class="flex items-center gap-3 py-3 cursor-pointer" x-on:click="toggle">
                <span class="w-3 h-3 rounded-full shrink-0 bg-green"></span>
                <span class="font-display font-semibold text-[16px]">À jour</span>
                <span class="text-[12.5px] text-muted font-semibold">{{ $payes->count() }} quittance{{ $payes->count() > 1 ? 's' : '' }}</span>
                <svg x-bind:class="chevClass" class="ml-auto w-4 h-4 text-muted transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>
            </div>
            <div x-show="open" x-cloak class="space-y-2.5">
                @foreach($payes as $p)
                    @php $loc = $p->contrat?->locataire; $bien = $p->contrat?->bien; $waLink = $wa($loc?->telephone); @endphp
                    <div class="flex flex-wrap items-center gap-4 p-4 bg-white rounded-xl border-l-4 border-green border-y border-r border-line">
                        <span class="w-[42px] h-[42px] rounded-[11px] bg-teal text-paper flex items-center justify-center text-[13px] font-bold shrink-0">{{ mb_strtoupper(mb_substr($loc?->name ?? '?', 0, 2)) }}</span>
                        <div class="flex-1 min-w-[140px]">
                            <div class="font-bold text-[14.5px] truncate">{{ $loc?->name ?? 'Locataire' }}</div>
                            <div class="text-[12.5px] text-muted truncate">{{ $bien?->titre ?: $bien?->reference }}</div>
                        </div>
                        <div class="text-[13px] text-muted w-[90px]">{{ \Carbon\Carbon::parse($p->periode)->locale('fr')->isoFormat('MMM Y') }}</div>
                        <div class="font-bold text-[14.5px] w-[100px]">{{ $fmt($p->montant_encaisse) }} F</div>
                        <span class="inline-flex items-center gap-1.5 text-[12px] font-bold px-3 py-1.5 rounded-full bg-green/10 text-green whitespace-nowrap"><span class="w-1.5 h-1.5 rounded-full bg-current"></span> Payé{{ $p->date_paiement ? ' le '.\Carbon\Carbon::parse($p->date_paiement)->isoFormat('D/MM') : '' }}</span>
                        <div class="ml-auto flex items-center gap-2 shrink-0">
                            @if(Route::has('admin.paiements.pdf'))
                                <a href="{{ route('admin.paiements.pdf', $p) }}" class="text-[12.5px] font-bold px-3.5 py-2 rounded-lg bg-white border border-line text-ink hover:border-teal transition-colors">PDF</a>
                            @endif
                            @if($waLink)
                                <a href="{{ $waLink }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 text-[12.5px] font-bold px-3.5 py-2 rounded-lg bg-[#25D366] text-white hover:opacity-90 transition-opacity"><x-icon-whatsapp /> Envoyer</a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <p class="text-center text-[12px] text-muted mt-8 pt-5 border-t border-dashed border-line">Marquer une quittance « payée » ici met aussi à jour l'historique visible sur la fiche du contrat — une seule source de données, deux vues.</p>
</div>
@endsection
