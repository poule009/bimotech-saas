@extends('layouts.app')
@section('header', 'Tableau de bord')

@section('content')

<div class="space-y-5 md:space-y-7">

    {{-- ═══ EN-TÊTE ═══ --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <h1 class="font-display font-extrabold text-2xl md:text-3xl text-bimo-text tracking-tight leading-tight">
                Bonjour, {{ Str::before(auth()->user()->name, ' ') ?: auth()->user()->name }} 
            </h1>
            <p class="font-body text-sm text-bimo-text/50 mt-1">
                {{ now()->translatedFormat('l d F Y') }} · {{ $currentAgency->name ?? 'Votre agence' }}
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            {{-- Lien portail --}}
            <button onclick="copierLienPortail()" id="btn-portail"
                    class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-bimo-navy/15 rounded-[9px]
                           font-body text-sm text-bimo-text/60 hover:text-bimo-text hover:border-bimo-gold
                           transition-all duration-150">
                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M10 13a5 5 0 007.54.54l3-3a5 5 0 00-7.07-7.07l-1.72 1.71"/>
                    <path d="M14 11a5 5 0 00-7.54-.54l-3 3a5 5 0 007.07 7.07l1.71-1.71"/>
                </svg>
                <span>Mon portail</span>
            </button>

            {{-- Tabs période --}}
            <div class="flex gap-0.5 p-1 bg-bimo-bg2 rounded-[10px]">
                @foreach(['mois' => 'Ce mois', 'trimestre' => 'Trimestre', 'annee' => 'Année'] as $val => $lbl)
                <a href="{{ route('admin.dashboard', ['periode' => $val]) }}"
                   class="px-3 py-1.5 rounded-[8px] font-body font-medium text-xs transition-all duration-150
                          {{ $periode === $val ? 'bg-white text-bimo-text shadow-sm' : 'text-bimo-text/50 hover:text-bimo-text' }}">
                    {{ $lbl }}
                </a>
                @endforeach
            </div>

            {{-- Export PDF --}}
            <a href="{{ route('admin.rapports.financier.export-pdf') }}"
               class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-bimo-navy/15 rounded-[9px]
                      font-body text-sm text-bimo-text/60 hover:text-bimo-text hover:border-bimo-navy/30
                      transition-all duration-150">
                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/>
                </svg>
                <span class="hidden sm:inline">Exporter PDF</span>
            </a>
        </div>
    </div>

    {{-- ═══ ALERTES ═══ --}}

    @if(($nbBiensInvisibles ?? 0) > 0)
    <div class="flex items-center justify-between gap-3 bg-amber-50 border border-amber-200 rounded-[12px] px-4 py-3 flex-wrap">
        <div class="flex items-center gap-3">
            <svg class="w-4 h-4 text-amber-600 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>
            </svg>
            <span class="font-body text-sm text-amber-800">
                <strong>{{ $nbBiensInvisibles }} bien{{ $nbBiensInvisibles > 1 ? 's' : '' }}</strong>
                disponible{{ $nbBiensInvisibles > 1 ? 's' : '' }} invisible{{ $nbBiensInvisibles > 1 ? 's' : '' }} sur le portail — informations incomplètes.
            </span>
        </div>
        <a href="{{ route('admin.biens.index', ['statut' => 'disponible']) }}"
           class="font-body font-semibold text-sm text-amber-700 hover:text-amber-900 transition-colors duration-150 whitespace-nowrap">
            Voir les biens →
        </a>
    </div>
    @endif

    @if($pourcentageUnites !== null && $pourcentageUnites >= 80)
    <div class="flex items-center justify-between gap-3 bg-bimo-gold/[8%] border border-bimo-gold/25 rounded-[12px] px-4 py-3 flex-wrap">
        <div class="flex items-center gap-3">
            <svg class="w-4 h-4 text-bimo-gold flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            <span class="font-body text-sm text-bimo-gold">
                Vous avez <strong>{{ $nbUnites }} unités sur {{ $limiteUnites }}</strong> — vous approchez de votre limite {{ $planLabel }}.
            </span>
        </div>
        <a href="{{ route('subscription.index') }}"
           class="font-body font-semibold text-sm text-bimo-gold hover:text-bimo-text transition-colors duration-150 whitespace-nowrap">
            Voir les plans →
        </a>
    </div>
    @endif

    {{-- ═══ ONBOARDING ═══ --}}
    @if($onboarding !== null)
    @php
        $steps = [
            ['done' => $onboarding['settings_ok'],  'label' => 'Configurer votre agence',        'sub' => 'Téléphone, adresse, logo',             'url' => route('admin.agency.settings'),   'cta' => 'Configurer →'],
            ['done' => $onboarding['has_biens'],     'label' => 'Ajouter votre premier bien',     'sub' => 'Appartement, villa, bureau…',          'url' => route('admin.biens.create'),      'cta' => 'Ajouter un bien →'],
            ['done' => $onboarding['has_contrats'],  'label' => 'Créer un contrat de bail',       'sub' => 'Associer un bien à un locataire',      'url' => route('admin.contrats.create'),   'cta' => 'Créer un contrat →'],
            ['done' => $onboarding['has_paiements'], 'label' => 'Enregistrer le premier paiement','sub' => 'Valider le premier loyer encaissé',     'url' => route('admin.paiements.create'),  'cta' => 'Saisir un paiement →'],
        ];
        $done    = collect($steps)->where('done', true)->count();
        $total   = count($steps);
        $pct     = round(($done / $total) * 100);
        $allDone = $done === $total;
    @endphp
    <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
        <div class="h-1 bg-bimo-bg2">
            <div class="h-full bg-bimo-gold transition-all duration-500 rounded-full" style="width: {{ $pct }}%"></div>
        </div>
        <div class="p-5">
            <div class="flex items-start justify-between gap-3 mb-4">
                <div>
                    <div class="font-display font-bold text-sm text-bimo-text">
                        @if($allDone) Votre agence est prête ! 🎉
                        @else Bienvenue sur bee — Commencez ici
                        @endif
                    </div>
                    <div class="font-body text-xs text-bimo-text/50 mt-0.5">
                        @if($allDone) Toutes les étapes sont complètes.
                        @else {{ $done }}/{{ $total }} étapes complètes · {{ $pct }}% de configuration
                        @endif
                    </div>
                </div>
                <form method="POST" action="{{ route('admin.onboarding.dismiss') }}" class="flex-shrink-0">
                    @csrf
                    <button type="submit"
                            class="w-7 h-7 flex items-center justify-center rounded-[6px] text-bimo-text/30
                                   hover:text-bimo-text hover:bg-bimo-bg transition-all duration-150 text-lg leading-none">
                        ×
                    </button>
                </form>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                @foreach($steps as $step)
                <div class="flex items-start gap-3 p-3.5 rounded-[10px] border transition-colors duration-150
                            {{ $step['done'] ? 'bg-bimo-gold/[5%] border-bimo-gold/20' : 'bg-bimo-bg border-bimo-navy/10' }}">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5
                                {{ $step['done'] ? 'bg-bimo-gold' : 'bg-bimo-navy/10' }}">
                        @if($step['done'])
                        <svg class="w-3.5 h-3.5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        @else
                        <span class="font-display font-bold text-xs text-bimo-text/30">{{ $loop->iteration }}</span>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="font-body font-semibold text-xs {{ $step['done'] ? 'text-bimo-gold' : 'text-bimo-text' }} mb-0.5">
                            {{ $step['label'] }}
                        </div>
                        <div class="font-body text-[10px] text-bimo-text/40 mb-2">{{ $step['sub'] }}</div>
                        @if(!$step['done'])
                        <a href="{{ $step['url'] }}"
                           class="font-body font-semibold text-[11px] text-bimo-gold hover:text-bimo-text transition-colors duration-150">
                            {{ $step['cta'] }}
                        </a>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- ═══ 5 KPIs ═══ --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 md:gap-4">

        {{-- 1. Loyers encaissés — highlighted --}}
        <div class="bg-bimo-gold/[8%] rounded-[14px] border border-bimo-gold/25 p-5 lg:p-6">
            <div class="flex items-start justify-between mb-4">
                <div class="w-10 h-10 rounded-[10px] bg-bimo-gold/20 flex items-center justify-center">
                    <svg class="w-4 h-4 text-bimo-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/>
                    </svg>
                </div>
                @if($delta['loyers'] !== null)
                <span class="inline-flex items-center gap-0.5 font-body font-semibold text-[11px]
                             {{ $delta['loyers'] >= 0 ? 'text-bimo-gold' : 'text-bimo-red' }}">
                    @if($delta['loyers'] >= 0)
                    <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="18 15 12 9 6 15"/></svg>
                    @else
                    <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                    @endif
                    {{ abs($delta['loyers']) }}%
                </span>
                @endif
            </div>
            <div class="font-display font-extrabold text-2xl lg:text-3xl text-bimo-gold leading-none">
                {{ number_format($statsMois['loyers'], 0, ',', ' ') }}
                <span class="font-body font-normal text-sm text-bimo-gold/60">F</span>
            </div>
            <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-gold/70 mt-2">Loyers encaissés</div>
            <div class="font-body text-[10px] text-bimo-text/30 mt-0.5">
                @if($delta['loyers'] !== null) vs mois dernier
                @else {{ $statsMois['nb_payes'] }} paiements
                @endif
            </div>
        </div>

        {{-- 2. Impayés ce mois --}}
        <a href="{{ route('admin.impayes.index') }}"
           class="block rounded-[14px] border p-5 lg:p-6 transition-all duration-150
                  {{ $nb_impayes_mois > 0
                     ? 'bg-bimo-red/[5%] border-bimo-red/25 hover:border-bimo-red/40'
                     : 'bg-white border-bimo-navy/10 hover:border-bimo-navy/25' }}">
            <div class="flex items-start justify-between mb-4">
                <div class="w-10 h-10 rounded-[10px] flex items-center justify-center
                            {{ $nb_impayes_mois > 0 ? 'bg-bimo-red/10' : 'bg-bimo-navy/5' }}">
                    <svg class="w-4 h-4 {{ $nb_impayes_mois > 0 ? 'text-bimo-red' : 'text-bimo-text/30' }}"
                         viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                </div>
                @if($delta['impayes'] !== null)
                <span class="inline-flex items-center gap-0.5 font-body font-semibold text-[11px]
                             {{ $delta['impayes'] <= 0 ? 'text-bimo-gold' : 'text-bimo-red' }}">
                    @if($delta['impayes'] > 0)
                    <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="18 15 12 9 6 15"/></svg>
                    +{{ $delta['impayes'] }}
                    @elseif($delta['impayes'] < 0)
                    <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                    {{ $delta['impayes'] }}
                    @else
                    — stable
                    @endif
                </span>
                @endif
            </div>
            <div class="font-display font-extrabold text-2xl lg:text-3xl leading-none
                        {{ $nb_impayes_mois > 0 ? 'text-bimo-red' : 'text-bimo-text' }}">
                {{ $nb_impayes_mois }}
                <span class="font-body font-normal text-sm text-bimo-text/40">contrats</span>
            </div>
            <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-text/50 mt-2">Impayés ce mois</div>
            <div class="font-body text-[10px] text-bimo-text/30 mt-0.5">
                @if($delta['impayes'] !== null) vs mois dernier
                @else Cliquer pour relancer
                @endif
            </div>
        </a>

        {{-- 3. Taux d'occupation --}}
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-5 lg:p-6">
            <div class="flex items-start justify-between mb-4">
                <div class="w-10 h-10 rounded-[10px] bg-bimo-navy/5 flex items-center justify-center">
                    <svg class="w-4 h-4 text-bimo-text/40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
                        <rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
                    </svg>
                </div>
            </div>
            <div class="font-display font-extrabold text-2xl lg:text-3xl text-bimo-text leading-none">
                {{ $stats['taux_occupation'] }}<span class="font-body font-normal text-sm text-bimo-text/40">%</span>
            </div>
            <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-text/50 mt-2">Occupation</div>
            <div class="font-body text-[10px] text-bimo-text/30 mt-0.5">{{ $stats['nb_biens_loues'] }} / {{ $stats['nb_biens'] }} biens loués</div>
        </div>

        {{-- 4. Contrats actifs --}}
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-5 lg:p-6">
            <div class="flex items-start justify-between mb-4">
                <div class="w-10 h-10 rounded-[10px] bg-bimo-navy/5 flex items-center justify-center">
                    <svg class="w-4 h-4 text-bimo-text/40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/>
                        <line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/>
                    </svg>
                </div>
            </div>
            <div class="font-display font-extrabold text-2xl lg:text-3xl text-bimo-text leading-none">
                {{ $stats['nb_contrats'] }}
                <span class="font-body font-normal text-sm text-bimo-text/40">baux</span>
            </div>
            <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-text/50 mt-2">Contrats actifs</div>
            <div class="font-body text-[10px] text-bimo-text/30 mt-0.5">{{ $stats['nb_locataires'] }} locataires · {{ $stats['nb_proprietaires'] }} propriétaires</div>
        </div>

        {{-- 5. Commission agence --}}
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-5 lg:p-6 col-span-2 sm:col-span-1">
            <div class="flex items-start justify-between mb-4">
                <div class="w-10 h-10 rounded-[10px] bg-bimo-navy/5 flex items-center justify-center">
                    <svg class="w-4 h-4 text-bimo-text/40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/>
                    </svg>
                </div>
                @if($delta['commissions'] !== null)
                <span class="inline-flex items-center gap-0.5 font-body font-semibold text-[11px]
                             {{ $delta['commissions'] >= 0 ? 'text-bimo-gold' : 'text-bimo-red' }}">
                    @if($delta['commissions'] >= 0)
                    <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="18 15 12 9 6 15"/></svg>
                    @else
                    <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                    @endif
                    {{ abs($delta['commissions']) }}%
                </span>
                @endif
            </div>
            <div class="font-display font-extrabold text-2xl lg:text-3xl text-bimo-text leading-none">
                {{ number_format($statsMois['commissions'], 0, ',', ' ') }}
                <span class="font-body font-normal text-sm text-bimo-text/40">F</span>
            </div>
            <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-text/50 mt-2">Commission TTC</div>
            <div class="font-body text-[10px] text-bimo-text/30 mt-0.5">
                @if($delta['commissions'] !== null) vs mois dernier
                @else {{ $periodeLabel }}
                @endif
            </div>
        </div>

    </div>

    {{-- ═══ BOUTONS D'ACTION ═══ --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 md:gap-4">

        {{-- Enregistrer un paiement — action principale --}}
        <a href="{{ route('admin.paiements.create') }}"
           class="flex items-center gap-4 p-5 bg-[var(--ac)] text-white rounded-[14px]
                  hover:opacity-90 active:opacity-95 transition-all duration-150 group min-h-[72px]">
            <div class="w-10 h-10 rounded-[10px] bg-white/15 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="1" y="4" width="22" height="16" rx="2" ry="2"/>
                    <line x1="1" y1="10" x2="23" y2="10"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <div class="font-display font-bold text-sm">Enregistrer un paiement</div>
                <div class="font-body text-xs opacity-70 mt-0.5 truncate">Saisir le loyer d'un locataire</div>
            </div>
            <svg class="w-4 h-4 opacity-50 group-hover:opacity-100 group-hover:translate-x-0.5 transition-all duration-150 flex-shrink-0"
                 viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="9 18 15 12 9 6"/>
            </svg>
        </a>

        {{-- Nouveau contrat --}}
        <a href="{{ route('admin.contrats.create') }}"
           class="flex items-center gap-4 p-5 bg-white rounded-[14px] border border-bimo-navy/10
                  hover:border-bimo-navy/25 hover:shadow-sm transition-all duration-150 group min-h-[72px]">
            <div class="w-10 h-10 rounded-[10px] bg-bimo-navy/[6%] flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-bimo-text/50" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <div class="font-display font-bold text-sm text-bimo-text">Nouveau contrat</div>
                <div class="font-body text-xs text-bimo-text/40 mt-0.5 truncate">Créer un bail locatif</div>
            </div>
            <svg class="w-4 h-4 text-bimo-text/20 group-hover:text-bimo-text/50 transition-colors duration-150 flex-shrink-0"
                 viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="9 18 15 12 9 6"/>
            </svg>
        </a>

        {{-- Ajouter un bien --}}
        <a href="{{ route('admin.biens.create') }}"
           class="flex items-center gap-4 p-5 bg-white rounded-[14px] border border-bimo-navy/10
                  hover:border-bimo-navy/25 hover:shadow-sm transition-all duration-150 group min-h-[72px]">
            <div class="w-10 h-10 rounded-[10px] bg-bimo-navy/[6%] flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-bimo-text/50" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <div class="font-display font-bold text-sm text-bimo-text">Ajouter un bien</div>
                <div class="font-body text-xs text-bimo-text/40 mt-0.5 truncate">Appartement, villa, bureau…</div>
            </div>
            <svg class="w-4 h-4 text-bimo-text/20 group-hover:text-bimo-text/50 transition-colors duration-150 flex-shrink-0"
                 viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="9 18 15 12 9 6"/>
            </svg>
        </a>

    </div>

    {{-- ═══ BILAN DU MOIS (carte sombre) ═══ --}}
    <div class="bg-bimo-navy rounded-[14px] p-5 md:p-7 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 rounded-full opacity-[0.06]"
             style="background: radial-gradient(circle, var(--ac) 0%, transparent 70%); transform: translate(30%, -30%)">
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-0 relative z-10">
            <div class="md:pr-7 md:border-r md:border-white/10">
                <div class="font-body font-medium text-[10px] uppercase tracking-widest text-white/40 mb-2">Attendu {{ $periodeLabel }}</div>
                <div class="font-display font-extrabold text-2xl md:text-3xl text-white leading-none">
                    {{ number_format($bilanMois['attendu'], 0, ',', ' ') }}
                    <span class="font-body font-light text-sm text-white/30">F</span>
                </div>
                <div class="inline-flex items-center gap-1.5 mt-2.5 px-2.5 py-1 rounded-full bg-white/[6%] border border-white/10">
                    <span class="font-body text-[11px] text-white/40">{{ $stats['nb_contrats'] }} contrats actifs</span>
                </div>
            </div>

            @php
                $tauxRecouvrement = $bilanMois['attendu'] > 0
                    ? min(100, round(($bilanMois['encaisse'] / $bilanMois['attendu']) * 100))
                    : 0;
            @endphp

            <div class="md:px-7 md:border-r md:border-white/10">
                <div class="font-body font-medium text-[10px] uppercase tracking-widest text-white/40 mb-2">Encaissé</div>
                <div class="font-display font-extrabold text-2xl md:text-3xl text-white leading-none">
                    {{ number_format($bilanMois['encaisse'], 0, ',', ' ') }}
                    <span class="font-body font-light text-sm text-white/50">F</span>
                </div>
                <div class="inline-flex items-center gap-1.5 mt-2.5 px-2.5 py-1 rounded-full bg-white/10 border border-white/20">
                    <span class="font-body font-medium text-[11px] text-white/90">
                        ✓ {{ $tauxRecouvrement }}% de recouvrement
                    </span>
                </div>
            </div>

            <div class="md:pl-7">
                @php $reliquat = max(0, $bilanMois['attendu'] - $bilanMois['encaisse']); @endphp
                <div class="font-body font-medium text-[10px] uppercase tracking-widest text-white/40 mb-2">Reliquat</div>
                <div class="font-display font-extrabold text-2xl md:text-3xl leading-none text-white">
                    {{ number_format($reliquat, 0, ',', ' ') }}
                    <span class="font-body font-light text-sm opacity-50">F</span>
                </div>
                @if($reliquat > 0)
                <div class="inline-flex items-center gap-1.5 mt-2.5 px-2.5 py-1 rounded-full bg-white/15 border border-white/25">
                    <span class="font-body font-medium text-[11px] text-white">⚠ {{ $nb_impayes_mois }} contrats en défaut</span>
                </div>
                @else
                <div class="inline-flex items-center gap-1.5 mt-2.5 px-2.5 py-1 rounded-full bg-white/10 border border-white/20">
                    <span class="font-body font-medium text-[11px] text-white/90">✓ Tout encaissé</span>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ═══ GRAPHIQUES : encaissements + occupation ═══ --}}
    <div class="grid grid-cols-1 lg:grid-cols-[3fr_2fr] gap-4">

        {{-- Graphique évolution loyers --}}
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
            <div class="flex items-center justify-between px-6 py-5 border-b border-bimo-navy/[5%]">
                <div>
                    <div class="font-display font-bold text-sm text-bimo-text">Encaissements mensuels</div>
                    <div class="font-body text-[11px] text-bimo-text/40 mt-0.5">12 derniers mois</div>
                </div>
                <div class="font-display font-bold text-sm text-bimo-gold">
                    {{ number_format($statsMois['loyers'], 0, ',', ' ') }} F
                    <span class="font-body font-normal text-xs text-bimo-text/40">ce mois</span>
                </div>
            </div>
            <div class="relative h-64 px-6 py-5">
                @if($loyersParMois->isEmpty())
                <div class="flex flex-col items-center justify-center h-full text-bimo-text/30 gap-2">
                    <svg class="w-8 h-8 opacity-40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/>
                    </svg>
                    <span class="font-body text-sm">Aucune donnée disponible</span>
                </div>
                @else
                <canvas id="chartLoyers"></canvas>
                @endif
            </div>
        </div>

        {{-- Donut : occupation par statut --}}
        @php
            $nbLoue    = (int) ($biensParStatut['loue']       ?? 0);
            $nbDispo   = (int) ($biensParStatut['disponible'] ?? 0);
            $nbTravaux = (int) ($biensParStatut['en_travaux'] ?? 0);
            $nbTotalBiens = $nbLoue + $nbDispo + $nbTravaux;
        @endphp
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden flex flex-col">
            <div class="px-6 py-5 border-b border-bimo-navy/[5%]">
                <div class="font-display font-bold text-sm text-bimo-text">Occupation par statut</div>
            </div>

            {{-- Canvas avec total centré --}}
            <div class="flex-1 flex items-center justify-center px-6 py-4">
                @if($nbTotalBiens === 0)
                <div class="text-center text-bimo-text/30">
                    <span class="font-body text-sm">Aucun bien</span>
                </div>
                @else
                <div class="relative w-full" style="height:180px">
                    <canvas id="chartOccupation"></canvas>
                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                        <div class="font-display font-extrabold text-2xl text-bimo-text leading-none">{{ $nbTotalBiens }}</div>
                        <div class="font-body text-[10px] text-bimo-text/40 uppercase tracking-widest mt-1 text-center leading-tight">Unités<br>gérées</div>
                    </div>
                </div>
                @endif
            </div>

            {{-- Légende --}}
            <div class="px-6 py-4 space-y-2.5 border-t border-bimo-navy/[5%]">
                @foreach([
                    ['label' => 'Occupés',    'count' => $nbLoue,    'dot' => 'bg-bimo-navy'],
                    ['label' => 'Vacants',    'count' => $nbDispo,   'dot' => 'bg-bimo-gold'],
                    ['label' => 'En travaux', 'count' => $nbTravaux, 'dot' => 'bg-bimo-navy/30'],
                ] as $item)
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full flex-shrink-0 {{ $item['dot'] }}"></span>
                        <span class="font-body text-sm text-bimo-text">{{ $item['label'] }}</span>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <span class="font-display font-bold text-sm text-bimo-text">{{ $item['count'] }}</span>
                        <span class="font-body text-xs text-bimo-text/40 w-10 text-right">
                            ({{ $nbTotalBiens > 0 ? round($item['count'] / $nbTotalBiens * 100) : 0 }}%)
                        </span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

    </div>

    {{-- ═══ IMPAYÉS URGENTS + RENOUVELLEMENTS ═══ --}}
    <div class="grid grid-cols-1 {{ $contrats_a_renouveler->isNotEmpty() ? 'lg:grid-cols-2' : '' }} gap-4">

        {{-- Impayés urgents --}}
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-bimo-navy/[5%]">
                <div class="font-display font-bold text-base text-bimo-text">Impayés urgents</div>
                <a href="{{ route('admin.impayes.index') }}"
                   class="font-body text-xs text-bimo-red/70 hover:text-bimo-red transition-colors duration-150">
                    Relancer →
                </a>
            </div>

            @forelse($impayes_urgents as $c)
            <div class="flex items-center gap-3 px-5 py-3.5 border-b border-bimo-navy/[5%] last:border-0 hover:bg-bimo-bg transition-colors duration-100">
                <div class="w-9 h-9 rounded-[8px] bg-bimo-red/10 flex items-center justify-center flex-shrink-0 font-display font-bold text-xs text-bimo-red">
                    {{ mb_strtoupper(mb_substr($c->locataire?->name ?? 'X', 0, 2)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="font-body font-medium text-sm text-bimo-text truncate">{{ $c->bien?->reference ?? '—' }}</div>
                    <div class="font-body text-xs text-bimo-text/40 truncate">{{ $c->locataire?->name ?? '—' }}</div>
                </div>
                <div class="font-display font-bold text-sm text-bimo-red whitespace-nowrap">
                    {{ number_format($c->loyer_contractuel, 0, ',', ' ') }} F
                </div>
            </div>
            @empty
            <div class="px-5 py-6 text-center">
                <svg class="w-6 h-6 text-bimo-gold mx-auto mb-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
                <p class="font-body text-sm text-bimo-gold">Aucun impayé ce mois</p>
            </div>
            @endforelse

            @php $taux = $bilanMois['attendu'] > 0 ? min(100, round(($bilanMois['encaisse'] / $bilanMois['attendu']) * 100)) : 0; @endphp
            <div class="px-5 py-4 border-t border-bimo-navy/[5%] bg-bimo-bg">
                <div class="flex items-center justify-between mb-2">
                    <span class="font-body font-medium text-xs text-bimo-text/50">Taux de recouvrement</span>
                    <span class="font-display font-bold text-sm text-bimo-gold">{{ $taux }}%</span>
                </div>
                <div class="h-1.5 bg-bimo-navy/10 rounded-full overflow-hidden">
                    <div class="h-full bg-bimo-gold rounded-full transition-all duration-700" style="width: {{ $taux }}%"></div>
                </div>
            </div>
        </div>

        {{-- Contrats à renouveler --}}
        @if($contrats_a_renouveler->isNotEmpty())
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
            <div class="px-5 py-4 border-b border-bimo-navy/[5%]">
                <div class="font-display font-bold text-sm text-bimo-text">À renouveler bientôt</div>
            </div>
            @foreach($contrats_a_renouveler as $c)
            <div class="flex items-center gap-3 px-5 py-3.5 border-b border-bimo-navy/[5%] last:border-0 hover:bg-bimo-bg transition-colors duration-100">
                <div class="w-10 h-10 rounded-[9px] bg-bimo-gold/[8%] border border-bimo-gold/20 flex flex-col items-center justify-center flex-shrink-0">
                    <div class="font-display font-extrabold text-sm text-bimo-gold leading-none">
                        {{ \Carbon\Carbon::parse($c->date_fin)->diffInDays(now()) }}
                    </div>
                    <div class="font-body text-[9.5px] text-bimo-gold/60 uppercase tracking-wider">jours</div>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="font-body font-medium text-sm text-bimo-text truncate">{{ $c->bien?->reference ?? '—' }}</div>
                    <div class="font-body text-xs text-bimo-text/40 truncate">{{ $c->locataire?->name ?? '—' }}</div>
                </div>
                <div class="font-body text-xs text-bimo-text/40 whitespace-nowrap">
                    {{ \Carbon\Carbon::parse($c->date_fin)->format('d/m/Y') }}
                </div>
            </div>
            @endforeach
        </div>
        @endif

    </div>

    {{-- ═══ DERNIERS PAIEMENTS ═══ --}}
    <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-5 border-b border-bimo-navy/[5%]">
            <div class="font-display font-bold text-base text-bimo-text">Derniers paiements</div>
            <a href="{{ route('admin.paiements.index') }}"
               class="font-body text-xs text-bimo-text/40 hover:text-bimo-gold transition-colors duration-150">
                Voir tout →
            </a>
        </div>

        {{-- Mobile : cards --}}
        <div class="md:hidden divide-y divide-bimo-navy/[5%]">
            @forelse($derniersPaiements as $p)
            <div class="px-5 py-3.5">
                <div class="flex items-center justify-between mb-1">
                    <span class="font-body text-[11px] text-bimo-text/40 uppercase tracking-widest">{{ $p->reference_paiement }}</span>
                    <span class="font-display font-bold text-sm text-bimo-gold">{{ number_format($p->montant_encaisse, 0, ',', ' ') }} FCFA</span>
                </div>
                <div class="flex items-center justify-between">
                    @php
                        $locNameM = $p->contrat?->locataire?->name ?? '';
                        $partsM = explode(' ', trim($locNameM));
                        $initialsM = strtoupper(mb_substr($partsM[0] ?? '', 0, 1)) . strtoupper(mb_substr($partsM[1] ?? '', 0, 1));
                        $palM = ['bg-bimo-navy/15 text-bimo-navy','bg-bimo-gold/15 text-bimo-gold','bg-bimo-red/10 text-bimo-red','bg-bimo-navy/25 text-bimo-navy'];
                        $classM = $palM[abs(crc32($locNameM)) % 4];
                    @endphp
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-full {{ $classM }} flex items-center justify-center font-display font-bold text-[10px] flex-shrink-0">
                            {{ $initialsM ?: '??' }}
                        </div>
                        <div>
                            <div class="font-body font-medium text-xs text-bimo-text">{{ $p->contrat?->bien?->reference ?? '—' }}</div>
                            <div class="font-body text-[11px] text-bimo-text/40">{{ $locNameM ?: '—' }}</div>
                        </div>
                    </div>
                    @if($p->statut === 'valide')
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-body font-medium bg-bimo-gold/10 border border-bimo-gold/20 text-bimo-gold">Validé</span>
                    @elseif($p->statut === 'annule')
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-body font-medium bg-bimo-red/10 border border-bimo-red/20 text-bimo-red">Annulé</span>
                    @else
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-body font-medium bg-bimo-navy/10 border border-bimo-navy/15 text-bimo-text/60">Attente</span>
                    @endif
                </div>
            </div>
            @empty
            <div class="px-5 py-10 text-center font-body text-sm text-bimo-text/30">
                Aucun paiement enregistré ce mois
            </div>
            @endforelse
        </div>

        {{-- Desktop : table --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-bimo-navy/[5%] bg-bimo-bg">
                        <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Référence</th>
                        <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Bien / Locataire</th>
                        <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Mode</th>
                        <th class="px-5 py-3 text-right font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Montant</th>
                        <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Statut</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-bimo-navy/[5%]">
                    @forelse($derniersPaiements as $p)
                    <tr class="hover:bg-bimo-bg transition-colors duration-100">
                        <td class="px-5 py-3.5 font-body text-[11px] text-bimo-text/40 uppercase tracking-widest">
                            {{ $p->reference_paiement }}
                        </td>
                        <td class="px-5 py-3.5">
                            @php
                                $locName = $p->contrat?->locataire?->name ?? '';
                                $parts = explode(' ', trim($locName));
                                $initials = strtoupper(mb_substr($parts[0] ?? '', 0, 1)) . strtoupper(mb_substr($parts[1] ?? '', 0, 1));
                                $avatarPalette = ['bg-bimo-navy/15 text-bimo-navy','bg-bimo-gold/15 text-bimo-gold','bg-bimo-red/10 text-bimo-red','bg-bimo-navy/25 text-bimo-navy'];
                                $avatarClass = $avatarPalette[abs(crc32($locName)) % 4];
                            @endphp
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full {{ $avatarClass }} flex items-center justify-center font-display font-bold text-xs flex-shrink-0">
                                    {{ $initials ?: '??' }}
                                </div>
                                <div>
                                    <div class="font-body font-medium text-sm text-bimo-text">{{ $p->contrat?->bien?->reference ?? '—' }}</div>
                                    <div class="font-body text-xs text-bimo-text/40">{{ $locName ?: '—' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3.5 font-body text-xs text-bimo-text/50">
                            {{ \App\Models\Paiement::MODES_PAIEMENT[$p->mode_paiement] ?? $p->mode_paiement }}
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            <span class="font-display font-bold text-sm text-bimo-gold">
                                {{ number_format($p->montant_encaisse, 0, ',', ' ') }} F
                            </span>
                        </td>
                        <td class="px-5 py-3.5">
                            @if($p->statut === 'valide')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-body font-medium bg-bimo-gold/10 border border-bimo-gold/20 text-bimo-gold">Validé</span>
                            @elseif($p->statut === 'annule')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-body font-medium bg-bimo-red/10 border border-bimo-red/20 text-bimo-red">Annulé</span>
                            @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-body font-medium bg-bimo-navy/10 border border-bimo-navy/15 text-bimo-text/60">Attente</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-5 py-10 text-center font-body text-sm text-bimo-text/30">
                            Aucun paiement enregistré ce mois
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- WhatsApp support --}}
<a href="https://wa.me/+221781318176?text={{ urlencode('Bonjour, j\'ai besoin d\'aide avec bee 👋') }}"
   target="_blank" rel="noopener noreferrer"
   class="fixed right-4 z-50 group bottom-[84px] lg:bottom-7 lg:right-7"
   title="Contacter le support">
    <div class="relative flex items-center">
        <div class="absolute right-full mr-3 bg-bimo-navy text-white font-body text-xs font-medium px-3 py-1.5 rounded-[8px]
                    whitespace-nowrap opacity-0 translate-x-2 group-hover:opacity-100 group-hover:translate-x-0
                    transition-all duration-150 pointer-events-none border border-white/10">
            Contacter le support
        </div>
        <div class="w-12 h-12 rounded-full flex items-center justify-center
                    shadow-[0_4px_16px_rgba(37,211,102,0.4)]
                    group-hover:shadow-[0_6px_22px_rgba(37,211,102,0.55)]
                    group-hover:scale-105 transition-all duration-200"
             style="background: #25D366">
            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="white">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
            </svg>
        </div>
    </div>
</a>

@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js"></script>
<script>
Chart.defaults.font.family = "'Poppins', sans-serif";
Chart.defaults.color = 'rgba(17,17,17,0.4)';

const GOLD   = '#6B7280';
const NAVY   = '#A60F1C';
const RED    = '#A60F1C';
const BRAND_RED = '#A60F1C'; // rouge vif du graphique encaissements (ajustable)

const loyersData = @json($loyersParMois->pluck('total'));
const moisLabels = @json($loyersParMois->pluck('mois'));

const moisNoms = ['Jan','Fév','Mar','Avr','Mai','Jun','Jul','Aoû','Sep','Oct','Nov','Déc'];
const labels = moisLabels.map(m => moisNoms[parseInt(m.split('-')[1]) - 1]);

const tip = {
    backgroundColor: NAVY,
    titleColor: '#fff',
    bodyColor: 'rgba(255,255,255,0.6)',
    borderColor: 'rgba(255,255,255,0.1)',
    borderWidth: 1,
    padding: 10,
    cornerRadius: 8,
};

// ── Graphique encaissements (bar chart) ────────────────────────────
const elLoyers = document.getElementById('chartLoyers');
if (elLoyers) {
    new Chart(elLoyers, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Loyers',
                data: loyersData,
                backgroundColor: BRAND_RED,
                borderRadius: 5,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    ...tip,
                    callbacks: { label: c => ' ' + Number(c.parsed.y).toLocaleString('fr-FR') + ' FCFA' }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    border: { display: false },
                    ticks: { font: { size: 11 } }
                },
                y: {
                    grid: { color: 'rgba(17,17,17,0.05)', drawTicks: false },
                    border: { display: false },
                    ticks: {
                        font: { size: 10 },
                        callback: v => v >= 1000000
                            ? (v / 1000000).toFixed(1) + 'M'
                            : (v >= 1000 ? Math.round(v / 1000) + 'k' : v)
                    }
                }
            }
        }
    });
}

// ── Donut occupation par statut ───────────────────────────────────
const elOccupation = document.getElementById('chartOccupation');
if (elOccupation) {
    new Chart(elOccupation, {
        type: 'doughnut',
        data: {
            labels: ['Occupés', 'Vacants', 'En travaux'],
            datasets: [{
                data: [
                    {{ (int) ($biensParStatut['loue'] ?? 0) }},
                    {{ (int) ($biensParStatut['disponible'] ?? 0) }},
                    {{ (int) ($biensParStatut['en_travaux'] ?? 0) }}
                ],
                backgroundColor: [NAVY, GOLD, 'rgba(123,30,58,0.25)'],
                borderWidth: 0,
                hoverOffset: 6,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '72%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    ...tip,
                    callbacks: { label: c => ' ' + c.label + ' : ' + c.parsed + ' biens' }
                }
            }
        }
    });
}

// ── Copier lien portail ────────────────────────────────────────────
function copierLienPortail() {
    const url = '{{ route('portail.index') }}?agence={{ $currentAgency?->slug ?? '' }}';
    const btn = document.getElementById('btn-portail');
    navigator.clipboard.writeText(url).then(() => {
        const orig = btn.innerHTML;
        btn.innerHTML = '<svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg><span>Copié !</span>';
        btn.classList.add('text-bimo-gold', 'border-bimo-gold/30');
        setTimeout(() => { btn.innerHTML = orig; btn.classList.remove('text-bimo-gold', 'border-bimo-gold/30'); }, 2000);
    });
}
</script>
@endpush
