@extends('layouts.app')
@section('header', 'Portefeuille Bailleurs › ' . $user->name)

@section('content')

@php
    $moisPdf = $mois ?? ($paiements->isNotEmpty()
        ? (int)\Carbon\Carbon::parse($paiements->first()->periode)->format('n')
        : now()->month);
    $profil = $user->proprietaire;
@endphp

<div class="space-y-4 md:space-y-5">

    {{-- Breadcrumb + Actions PDF --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
        <div class="flex items-center gap-2 font-body text-sm text-bimo-text/40">
            <a href="{{ route('admin.bailleurs.index') }}" class="hover:text-bimo-text transition-colors duration-150">Portefeuille Bailleurs</a>
            <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            <span class="text-bimo-text font-semibold">{{ $user->name }}</span>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.bailleurs.export-pdf', [$user->id, 'annee' => $annee, 'mois' => $moisPdf]) }}" target="_blank"
               class="inline-flex items-center gap-2 px-3 py-2 bg-bimo-navy text-bimo-gold border border-bimo-gold/30 rounded-[9px] font-body font-semibold text-xs hover:bg-bimo-navy-dk transition-colors duration-150">
                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                Rapport mensuel
            </a>
            <a href="{{ route('admin.bailleurs.export-pdf', [$user->id, 'annee' => $annee]) }}" target="_blank"
               class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-bimo-navy/15 rounded-[9px] font-body font-semibold text-xs text-bimo-text/60 hover:text-bimo-text hover:border-bimo-navy/30 transition-all duration-150">
                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                Rapport annuel {{ $annee }}
            </a>
            <a href="{{ route('admin.bailleurs.releve-pdf', [$user->id, 'annee' => $annee, 'mois' => $moisPdf]) }}" target="_blank"
               class="inline-flex items-center gap-2 px-3 py-2 bg-bimo-gold/10 border border-bimo-gold/25 rounded-[9px] font-body font-semibold text-xs text-bimo-gold hover:bg-bimo-gold/20 transition-all duration-150">
                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                Relevé propriétaire
            </a>
        </div>
    </div>

    {{-- Hero --}}
    <div class="bg-bimo-navy rounded-[14px] p-5 md:p-7 flex flex-col sm:flex-row sm:items-center justify-between gap-5 relative overflow-hidden border border-white/[6%]">
        <div class="absolute top-0 right-0 w-48 h-48 rounded-full opacity-[0.06]"
             style="background: radial-gradient(circle, var(--ac) 0%, transparent 70%); transform: translate(30%, -30%)"></div>
        <div class="flex items-center gap-4 relative z-10">
            <div class="w-14 h-14 rounded-full flex items-center justify-center flex-shrink-0 font-display font-extrabold text-2xl text-bimo-gold bg-bimo-gold/12 border-2 border-bimo-gold/35">
                {{ mb_strtoupper(mb_substr($user->name, 0, 2)) }}
            </div>
            <div>
                <div class="font-display font-extrabold text-xl text-white leading-tight mb-1">{{ $user->name }}</div>
                <div class="font-body text-xs text-white/30 leading-relaxed">
                    @if($user->email)<span class="text-white/50">{{ $user->email }}</span><br>@endif
                    @if($user->telephone){{ $user->telephone }}<br>@endif
                    @if($profil?->ninea)NINEA : <span class="text-white/50">{{ $profil->ninea }}</span>@endif
                </div>
            </div>
        </div>
        <div class="flex gap-3 flex-wrap relative z-10">
            @foreach([
                ['Loyers encaissés', number_format($dashboard['total_loyers'], 0, ',', ' '), 'FCFA — '.$annee, 'text-bimo-gold'],
                ['Net à reverser',   number_format($dashboard['net_final'], 0, ',', ' '),   'FCFA net', 'text-white'],
                ['Biens loués',      $dashboard['nb_biens_loues'].'/'.$dashboard['nb_biens'], $dashboard['nb_paiements'].' paiement(s)', 'text-white'],
            ] as [$lbl, $val, $sub, $cls])
            <div class="bg-white/[4%] border border-white/[8%] rounded-[12px] px-4 py-3 text-center min-w-[110px]">
                <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-white/30 mb-1.5">{{ $lbl }}</div>
                <div class="font-display font-bold text-lg leading-none {{ $cls }}">{{ $val }}</div>
                <div class="font-body text-[10px] text-white/25 mt-1">{{ $sub }}</div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Filtre période --}}
    <form method="GET" class="flex flex-wrap items-center gap-2">
        <select name="annee" onchange="this.form.submit()"
                class="px-3 py-2 bg-white border border-bimo-navy/15 rounded-[9px] font-body text-sm text-bimo-text cursor-pointer
                       focus:outline-none focus:border-bimo-gold transition-all duration-150">
            @foreach($anneesDisponibles as $a)
            <option value="{{ $a }}" @selected($a == $annee)>{{ $a }}</option>
            @endforeach
        </select>
        <select name="mois" onchange="this.form.submit()"
                class="px-3 py-2 bg-white border border-bimo-navy/15 rounded-[9px] font-body text-sm text-bimo-text cursor-pointer
                       focus:outline-none focus:border-bimo-gold transition-all duration-150">
            <option value="">Toute l'année</option>
            @foreach(range(1,12) as $m)
            <option value="{{ $m }}" @selected($m == $mois)>
                {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
            </option>
            @endforeach
        </select>
        @if($mois)
        <a href="{{ route('admin.bailleurs.show', $user->id) }}?annee={{ $annee }}"
           class="px-3 py-2 bg-white border border-bimo-navy/15 rounded-[9px] font-body text-sm text-bimo-text/50 hover:text-bimo-text transition-all duration-150">
            Voir toute l'année
        </a>
        @endif
    </form>

    {{-- Onglets --}}
    <div class="flex gap-0.5 p-1 bg-bimo-bg2 border border-bimo-navy/10 rounded-[12px] w-fit">
        <button class="hub-tab px-4 py-2 rounded-[9px] font-body font-semibold text-sm transition-all duration-150 flex items-center gap-2 bg-white text-bimo-text shadow-sm"
                onclick="switchTab('synthese', this)">
            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
            Synthèse
        </button>
        <button class="hub-tab px-4 py-2 rounded-[9px] font-body font-medium text-sm text-bimo-text/50 hover:text-bimo-text transition-all duration-150 flex items-center gap-2"
                onclick="switchTab('biens', this)">
            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            Biens &amp; Locataires
            <span class="w-5 h-5 rounded-full bg-bimo-navy/10 text-bimo-text/60 text-[10px] font-bold flex items-center justify-center">{{ $biens->count() }}</span>
        </button>
        <button class="hub-tab px-4 py-2 rounded-[9px] font-body font-medium text-sm text-bimo-text/50 hover:text-bimo-text transition-all duration-150 flex items-center gap-2"
                onclick="switchTab('paiements', this)">
            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
            Historique
            <span class="w-5 h-5 rounded-full bg-bimo-navy/10 text-bimo-text/60 text-[10px] font-bold flex items-center justify-center">{{ $paiements->count() }}</span>
        </button>
        @if($dashboard['total_brs'] > 0 || $dashboard['total_dgid'] > 0)
        <button class="hub-tab px-4 py-2 rounded-[9px] font-body font-medium text-sm text-bimo-text/50 hover:text-bimo-text transition-all duration-150 flex items-center gap-2"
                onclick="switchTab('fiscal', this)">
            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/></svg>
            Fiscal
        </button>
        @endif
    </div>

    {{-- ═══ PANEL 1 — SYNTHÈSE ═══ --}}
    <div id="tab-synthese" class="hub-panel">

        <div class="grid grid-cols-1 lg:grid-cols-[1fr_300px] gap-5 items-start">

            <div class="space-y-4">
                {{-- Équation financière --}}
                <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-4 flex flex-wrap items-center gap-3">
                    <div class="text-center px-4 py-3 rounded-[10px] bg-bimo-gold/10 border border-bimo-gold/20 min-w-[110px]">
                        <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-gold/60 mb-1">Loyers encaissés</div>
                        <div class="font-display font-bold text-sm text-bimo-gold">{{ number_format($dashboard['total_loyers'], 0, ',', ' ') }} FCFA</div>
                    </div>
                    <span class="font-display font-bold text-xl text-bimo-text/30">−</span>
                    <div class="text-center px-4 py-3 rounded-[10px] bg-bimo-navy/5 border border-bimo-navy/10 min-w-[110px]">
                        <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-text/40 mb-1">Commissions TTC</div>
                        <div class="font-display font-bold text-sm text-bimo-text">{{ number_format($dashboard['total_commissions'], 0, ',', ' ') }} FCFA</div>
                    </div>
                    @if(($dashboard['total_brs'] ?? 0) > 0)
                    <span class="font-display font-bold text-xl text-bimo-text/30">−</span>
                    <div class="text-center px-4 py-3 rounded-[10px] bg-bimo-red/10 border border-bimo-red/20 min-w-[110px]">
                        <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-red/60 mb-1">BRS retenu</div>
                        <div class="font-display font-bold text-sm text-bimo-red">{{ number_format($dashboard['total_brs'], 0, ',', ' ') }} FCFA</div>
                    </div>
                    @endif
                    @if($dashboard['total_depenses'] > 0)
                    <span class="font-display font-bold text-xl text-bimo-text/30">−</span>
                    <div class="text-center px-4 py-3 rounded-[10px] bg-bimo-red/10 border border-bimo-red/20 min-w-[110px]">
                        <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-red/60 mb-1">Dépenses</div>
                        <div class="font-display font-bold text-sm text-bimo-red">{{ number_format($dashboard['total_depenses'], 0, ',', ' ') }} FCFA</div>
                    </div>
                    @endif
                    <span class="font-display font-bold text-xl text-bimo-text">=</span>
                    <div class="text-center px-5 py-3.5 rounded-[10px] bg-bimo-navy min-w-[120px]">
                        <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-gold/60 mb-1">Net à verser</div>
                        <div class="font-display font-bold text-lg text-bimo-gold">{{ number_format($dashboard['net_final'], 0, ',', ' ') }} FCFA</div>
                    </div>
                </div>

                {{-- Aperçu biens --}}
                <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
                    <div class="flex items-center justify-between px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-[8px] bg-bimo-navy/5 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-bimo-text/40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                            </div>
                            <span class="font-display font-bold text-sm text-bimo-text">Aperçu Biens</span>
                        </div>
                        <button onclick="switchTab('biens', document.querySelectorAll('.hub-tab')[1])"
                                class="font-body text-xs text-bimo-gold hover:text-bimo-text transition-colors duration-150">Voir tout →</button>
                    </div>
                    @foreach($biens->take(3) as $bien)
                    @php $bs = match($bien->statut) { 'loue' => 'bg-bimo-gold/10 border-bimo-gold/20 text-bimo-gold', 'disponible' => 'bg-bimo-navy/10 border-bimo-navy/15 text-bimo-text/60', default => 'bg-bimo-navy/5 border-bimo-navy/10 text-bimo-text/40' }; @endphp
                    <div class="flex items-center gap-3 px-5 py-3.5 border-b border-bimo-navy/[5%] last:border-0">
                        <div class="flex-1 min-w-0">
                            <a href="{{ route('admin.biens.show', $bien) }}" class="font-body font-semibold text-sm text-bimo-text hover:text-bimo-gold transition-colors duration-150">{{ $bien->reference }}</a>
                            <div class="font-body text-xs text-bimo-text/50">{{ $bien->adresse }}{{ $bien->ville ? ', '.$bien->ville : '' }}</div>
                        </div>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full border text-[11px] font-body font-medium {{ $bs }}">
                            <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                            {{ \App\Models\Bien::STATUTS[$bien->statut] ?? $bien->statut }}
                        </span>
                        @if($bien->contratActif)
                        <div class="text-right flex-shrink-0">
                            <div class="font-display font-bold text-sm text-bimo-text">{{ number_format($bien->loyer_mensuel, 0, ',', ' ') }} FCFA</div>
                            <div class="font-body text-[10px] text-bimo-text/40">/mois</div>
                        </div>
                        @endif
                    </div>
                    @endforeach
                    @if($biens->count() > 3)
                    <div class="px-5 py-3 text-center font-body text-xs text-bimo-text/40">
                        + {{ $biens->count() - 3 }} autre(s) bien(s) —
                        <button onclick="switchTab('biens', document.querySelectorAll('.hub-tab')[1])" class="text-bimo-gold hover:text-bimo-text transition-colors duration-150 font-medium">voir tous</button>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Résumé dark --}}
            <div class="lg:sticky lg:top-6">
                <div class="bg-bimo-navy rounded-[14px] overflow-hidden">
                    <div class="flex items-center justify-between px-5 py-4 border-b border-white/[7%]">
                        <div class="font-display font-bold text-sm text-white">Résumé financier</div>
                        <span class="font-body text-[11px] text-white/25">{{ $annee }}{{ $mois ? ' / M'.str_pad($mois,2,'0',STR_PAD_LEFT) : '' }}</span>
                    </div>
                    <div class="px-5 py-2 divide-y divide-white/[5%]">
                        <div class="flex justify-between py-2.5"><span class="font-body text-xs text-white/35">Paiements</span><span class="font-body text-xs text-white/70">{{ $dashboard['nb_paiements'] }}</span></div>
                        <div class="flex justify-between py-2.5"><span class="font-body text-xs text-white/35">Biens loués</span><span class="font-body text-xs text-white/70">{{ $dashboard['nb_biens_loues'] }} / {{ $dashboard['nb_biens'] }}</span></div>
                        <div class="flex justify-between py-2.5"><span class="font-body text-xs text-white/35">Loyers encaissés</span><span class="font-display font-semibold text-xs text-bimo-gold">{{ number_format($dashboard['total_loyers'], 0, ',', ' ') }} FCFA</span></div>
                        <div class="flex justify-between py-2.5"><span class="font-body text-xs text-white/35">Commissions TTC</span><span class="font-display font-semibold text-xs text-bimo-red">− {{ number_format($dashboard['total_commissions'], 0, ',', ' ') }} FCFA</span></div>
                        @if($dashboard['total_depenses'] > 0)
                        <div class="flex justify-between py-2.5"><span class="font-body text-xs text-white/35">Dépenses gestion</span><span class="font-display font-semibold text-xs text-bimo-red">− {{ number_format($dashboard['total_depenses'], 0, ',', ' ') }} FCFA</span></div>
                        @endif
                        @if($dashboard['total_brs'] > 0)
                        <div class="flex justify-between py-2.5"><span class="font-body text-xs text-bimo-red/70">BRS retenu</span><span class="font-display font-semibold text-xs text-bimo-red">{{ number_format($dashboard['total_brs'], 0, ',', ' ') }} FCFA</span></div>
                        @endif
                        @if($dashboard['total_dgid'] > 0)
                        <div class="flex justify-between py-2.5"><span class="font-body text-xs text-white/35">DGID enreg.</span><span class="font-display font-semibold text-xs text-white/70">{{ number_format($dashboard['total_dgid'], 0, ',', ' ') }} FCFA</span></div>
                        @endif
                    </div>
                    <div class="px-5 pb-4">
                        <div class="p-3.5 bg-bimo-gold/10 border border-bimo-gold/20 rounded-[9px] mb-3">
                            <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-gold/60 mb-1">Montant final à verser</div>
                            <div class="font-display font-extrabold text-xl text-bimo-gold">{{ number_format($dashboard['net_final'], 0, ',', ' ') }} FCFA</div>
                        </div>
                        <a href="{{ route('admin.bailleurs.export-pdf', [$user->id, 'annee' => $annee, 'mois' => $moisPdf]) }}" target="_blank"
                           class="flex items-center justify-center gap-2 px-4 py-2.5 border border-white/10 rounded-[9px] font-body text-xs text-bimo-gold hover:text-white hover:border-white/20 transition-all duration-150 mb-2">
                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                            Rapport mensuel PDF
                        </a>
                        <a href="{{ route('admin.bailleurs.export-pdf', [$user->id, 'annee' => $annee]) }}" target="_blank"
                           class="flex items-center justify-center gap-2 px-4 py-2.5 border border-white/10 rounded-[9px] font-body text-xs text-white/50 hover:text-white hover:border-white/20 transition-all duration-150">
                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                            Rapport annuel {{ $annee }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══ PANEL 2 — BIENS & LOCATAIRES ═══ --}}
    <div id="tab-biens" class="hub-panel" style="display:none">
        @if($biens->isEmpty())
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 py-16 px-6 text-center">
            <svg class="w-10 h-10 text-bimo-text/15 mx-auto mb-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
            <div class="font-display font-bold text-base text-bimo-text mb-2">Aucun bien associé</div>
            <p class="font-body text-sm text-bimo-text/50 mb-5">Ajoutez des biens à ce propriétaire depuis la gestion des biens.</p>
            <a href="{{ route('admin.biens.create') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-[var(--ac)] text-white font-display font-bold text-sm rounded-[10px] hover:opacity-90 transition-opacity duration-150">
                + Créer un bien
            </a>
        </div>
        @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($biens as $bien)
            @php $bs = match($bien->statut) { 'loue' => 'bg-bimo-gold/10 border-bimo-gold/20 text-bimo-gold', 'disponible' => 'bg-bimo-navy/10 border-bimo-navy/15 text-bimo-text/60', default => 'bg-bimo-navy/5 border-bimo-navy/10 text-bimo-text/40' }; @endphp
            <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden hover:border-bimo-gold/30 hover:shadow-gold-sm transition-all duration-150">
                {{-- Header --}}
                <div class="flex items-center gap-3 px-5 py-4 border-b border-bimo-navy/[5%]">
                    <div class="flex-1 min-w-0">
                        <a href="{{ route('admin.biens.show', $bien) }}" class="font-display font-bold text-sm text-bimo-text hover:text-bimo-gold transition-colors duration-150">{{ $bien->reference }}</a>
                        <div class="font-body text-xs text-bimo-text/50 mt-0.5">
                            {{ $bien->adresse }}{{ $bien->ville ? ', '.$bien->ville : '' }}
                            @if($bien->surface_m2) · {{ $bien->surface_m2 }} m² @endif
                        </div>
                    </div>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full border text-[11px] font-body font-medium flex-shrink-0 {{ $bs }}">
                        <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                        {{ \App\Models\Bien::STATUTS[$bien->statut] ?? $bien->statut }}
                    </span>
                    @if($bien->contratActif)
                    <div class="text-right flex-shrink-0">
                        <div class="font-display font-bold text-sm text-bimo-text">{{ number_format($bien->loyer_mensuel, 0, ',', ' ') }} FCFA</div>
                        <div class="font-body text-[10px] text-bimo-text/40">/mois</div>
                    </div>
                    @endif
                </div>

                {{-- Locataire --}}
                @if($bien->contratActif?->locataire)
                <div class="flex items-center gap-3 px-5 py-3 bg-bimo-bg">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 font-body font-bold text-sm text-bimo-text bg-bimo-navy/10 border border-bimo-navy/20">
                        {{ mb_strtoupper(mb_substr($bien->contratActif->locataire->name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="font-body font-semibold text-sm text-bimo-text truncate">{{ $bien->contratActif->locataire->name }}</div>
                        <div class="font-body text-xs text-bimo-text/50">
                            Locataire actif
                            @if($bien->contratActif->date_debut) · depuis {{ \Carbon\Carbon::parse($bien->contratActif->date_debut)->translatedFormat('M Y') }} @endif
                        </div>
                    </div>
                    <a href="{{ route('admin.contrats.show', $bien->contratActif) }}"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 border border-bimo-navy/10 rounded-[7px] font-body text-xs text-bimo-text/60 hover:text-bimo-text hover:border-bimo-navy/30 transition-all duration-150 flex-shrink-0">
                        <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                        Contrat
                    </a>
                </div>
                @else
                <div class="flex items-center gap-3 px-5 py-3 bg-bimo-bg">
                    <svg class="w-4 h-4 text-bimo-text/25 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    <span class="font-body text-xs text-bimo-text/40 flex-1">Aucun locataire actif</span>
                    <a href="{{ route('admin.contrats.create') }}?bien_id={{ $bien->id }}"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-bimo-gold/10 border border-bimo-gold/20 rounded-[7px] font-body text-xs text-bimo-gold hover:bg-bimo-gold/20 transition-all duration-150 flex-shrink-0">
                        + Assigner
                    </a>
                </div>
                @endif
            </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- ═══ PANEL 3 — HISTORIQUE PAIEMENTS ═══ --}}
    <div id="tab-paiements" class="hub-panel" style="display:none">
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-[8px] bg-bimo-navy/5 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-bimo-text/40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                    </div>
                    <span class="font-display font-bold text-sm text-bimo-text">
                        Paiements validés — {{ $annee }}{{ $mois ? ' / '.str_pad($mois,2,'0',STR_PAD_LEFT) : '' }}
                    </span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-bimo-navy/10 font-body text-[10px] text-bimo-text/60">{{ $paiements->count() }}</span>
                </div>
                @if($paiements->count() > 0)
                <div class="font-body text-xs text-bimo-text/50">
                    Total : <span class="font-display font-bold text-bimo-gold">{{ number_format($dashboard['total_loyers'], 0, ',', ' ') }} FCFA</span>
                </div>
                @endif
            </div>

            @if($paiements->isEmpty())
            <div class="px-5 py-16 text-center font-body text-sm text-bimo-text/30">
                <svg class="w-8 h-8 text-bimo-text/15 mx-auto mb-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                Aucun paiement validé sur cette période.
            </div>
            @else

            {{-- Mobile --}}
            <div class="md:hidden divide-y divide-bimo-navy/[5%]">
                @foreach($paiements as $p)
                @php $depMois = (float) $p->depenses->sum('montant'); $netBailleur = (float)($p->montant_net_bailleur ?? $p->net_a_verser_proprietaire ?? 0); $netFinalLigne = round($netBailleur - $depMois, 2); @endphp
                <div class="px-5 py-3.5 flex items-center justify-between gap-3">
                    <div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-display font-semibold bg-bimo-gold/10 border border-bimo-gold/20 text-bimo-gold">{{ \Carbon\Carbon::parse($p->periode)->translatedFormat('M Y') }}</span>
                        <div class="font-body text-xs text-bimo-text/50 mt-1">{{ $p->contrat?->bien?->reference ?? '—' }}</div>
                    </div>
                    <div class="text-right">
                        <div class="font-display font-bold text-sm text-bimo-gold">{{ number_format($p->montant_encaisse, 0, ',', ' ') }} FCFA</div>
                        <div class="font-body text-xs text-bimo-text/40">Net: {{ number_format($netFinalLigne, 0, ',', ' ') }} FCFA</div>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Desktop --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-bimo-navy/[5%] bg-bimo-bg">
                            <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Période</th>
                            <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Bien</th>
                            <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Locataire</th>
                            <th class="px-5 py-3 text-right font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Loyer encaissé</th>
                            <th class="px-5 py-3 text-right font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Commission TTC</th>
                            <th class="px-5 py-3 text-right font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Net final</th>
                            <th class="px-5 py-3 text-center font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Voir</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-bimo-navy/[5%]">
                        @foreach($paiements as $p)
                        @php $depMois = (float) $p->depenses->sum('montant'); $netBailleur = (float)($p->montant_net_bailleur ?? $p->net_a_verser_proprietaire ?? 0); $netFinalLigne = round($netBailleur - $depMois, 2); @endphp
                        <tr class="hover:bg-bimo-bg transition-colors duration-100">
                            <td class="px-5 py-3.5">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-display font-semibold bg-bimo-gold/10 border border-bimo-gold/20 text-bimo-gold">
                                    {{ \Carbon\Carbon::parse($p->periode)->translatedFormat('M Y') }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 font-body font-medium text-sm text-bimo-text">{{ $p->contrat?->bien?->reference ?? '—' }}</td>
                            <td class="px-5 py-3.5 font-body text-xs text-bimo-text/60">{{ $p->contrat?->locataire?->name ?? '—' }}</td>
                            <td class="px-5 py-3.5 text-right font-display font-bold text-sm text-bimo-gold">{{ number_format($p->montant_encaisse, 0, ',', ' ') }} FCFA</td>
                            <td class="px-5 py-3.5 text-right font-body text-xs text-bimo-text/50">{{ number_format($p->commission_ttc ?? 0, 0, ',', ' ') }} FCFA</td>
                            <td class="px-5 py-3.5 text-right font-display font-bold text-sm text-bimo-text">{{ number_format($netFinalLigne, 0, ',', ' ') }} FCFA</td>
                            <td class="px-5 py-3.5 text-center">
                                <a href="{{ route('admin.paiements.show', $p) }}"
                                   class="w-9 h-9 inline-flex items-center justify-center border border-bimo-navy/10 rounded-[6px] text-bimo-text/40 hover:text-bimo-gold hover:border-bimo-gold/30 transition-all duration-150">
                                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>

    {{-- ═══ PANEL 4 — FISCAL ═══ --}}
    @if($dashboard['total_brs'] > 0 || $dashboard['total_dgid'] > 0)
    <div id="tab-fiscal" class="hub-panel" style="display:none">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @if($dashboard['total_brs'] > 0)
            <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-5">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 rounded-[8px] bg-bimo-red/10 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-bimo-red" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    </div>
                    <span class="font-display font-bold text-sm text-bimo-text">BRS — Retenue à la source</span>
                </div>
                <div class="space-y-2.5">
                    <div class="flex justify-between"><span class="font-body text-xs text-bimo-text/50">Total BRS retenu</span><span class="font-display font-bold text-sm text-bimo-red">{{ number_format($dashboard['total_brs'], 0, ',', ' ') }} FCFA</span></div>
                    <div class="flex justify-between"><span class="font-body text-xs text-bimo-text/50">Exercice</span><span class="font-body text-xs text-bimo-text">{{ $annee }}</span></div>
                </div>
                <div class="mt-4 p-3 bg-bimo-red/[4%] border border-bimo-red/15 rounded-[8px] font-body text-[11px] text-bimo-text/50 leading-relaxed">
                    La retenue BRS est déduite avant reversement au propriétaire conformément à la réglementation fiscale en vigueur.
                </div>
            </div>
            @endif
            @if($dashboard['total_dgid'] > 0)
            <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-5">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 rounded-[8px] bg-bimo-navy/10 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-bimo-navy" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                    </div>
                    <span class="font-display font-bold text-sm text-bimo-text">DGID — Droits d'enregistrement</span>
                </div>
                <div class="space-y-2.5">
                    <div class="flex justify-between"><span class="font-body text-xs text-bimo-text/50">Total DGID</span><span class="font-display font-bold text-sm text-bimo-navy">{{ number_format($dashboard['total_dgid'], 0, ',', ' ') }} FCFA</span></div>
                    <div class="flex justify-between"><span class="font-body text-xs text-bimo-text/50">Exercice</span><span class="font-body text-xs text-bimo-text">{{ $annee }}</span></div>
                </div>
                <div class="mt-4 p-3 bg-bimo-navy/[6%] border border-bimo-navy/15 rounded-[8px] font-body text-[11px] text-bimo-text/50 leading-relaxed">
                    Droits d'enregistrement des contrats de bail auprès de la DGID — Direction Générale des Impôts et Domaines.
                </div>
            </div>
            @endif
        </div>
    </div>
    @endif

</div>

@push('scripts')
<script>
function switchTab(tabId, btn) {
    document.querySelectorAll('.hub-tab').forEach(t => {
        t.classList.remove('bg-white','text-bimo-text','shadow-sm');
        t.classList.add('text-bimo-text/50');
    });
    document.querySelectorAll('.hub-panel').forEach(p => p.style.display = 'none');
    if (btn) {
        btn.classList.add('bg-white','text-bimo-text','shadow-sm');
        btn.classList.remove('text-bimo-text/50');
    }
    const panel = document.getElementById('tab-' + tabId);
    if (panel) panel.style.display = 'block';
}
</script>
@endpush

@endsection
