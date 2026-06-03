@extends('layouts.app')
@section('header', 'Tableau de bord')

@section('content')

<div class="space-y-4 md:space-y-6">

    {{-- ═══ EN-TÊTE ═══ --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <h1 class="font-display font-extrabold text-xl md:text-2xl text-bimo-navy tracking-tight leading-tight">
                Tableau de bord
            </h1>
            <p class="font-body text-sm text-bimo-navy/50 mt-1">
                Bilan de {{ now()->translatedFormat('F Y') }} · {{ $currentAgency->name ?? 'Votre agence' }}
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            {{-- Lien portail --}}
            <button onclick="copierLienPortail()" id="btn-portail"
                    class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-bimo-navy/15 rounded-[9px]
                           font-body text-sm text-bimo-navy/60 hover:text-bimo-navy hover:border-bimo-gold
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
                          {{ $periode === $val
                             ? 'bg-white text-bimo-navy shadow-sm'
                             : 'text-bimo-navy/50 hover:text-bimo-navy' }}">
                    {{ $lbl }}
                </a>
                @endforeach
            </div>

            {{-- Export PDF --}}
            <a href="{{ route('admin.rapports.financier.export-pdf') }}"
               class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-bimo-navy/15 rounded-[9px]
                      font-body text-sm text-bimo-navy/60 hover:text-bimo-navy hover:border-bimo-navy/30
                      transition-all duration-150">
                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/>
                </svg>
                <span class="hidden sm:inline">Exporter PDF</span>
            </a>
        </div>
    </div>

    {{-- ═══ ALERTES ═══ --}}

    {{-- Biens invisibles portail --}}
    @if(($nbBiensInvisibles ?? 0) > 0)
    <div class="flex items-center justify-between gap-3 bg-amber-50 border border-amber-200 rounded-[12px] px-4 py-3 flex-wrap">
        <div class="flex items-center gap-3">
            <svg class="w-4 h-4 text-amber-600 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                <polyline points="9 22 9 12 15 12 15 22"/>
            </svg>
            <span class="font-body text-sm text-amber-800">
                <strong>{{ $nbBiensInvisibles }} bien{{ $nbBiensInvisibles > 1 ? 's' : '' }}</strong>
                disponible{{ $nbBiensInvisibles > 1 ? 's' : '' }} invisible{{ $nbBiensInvisibles > 1 ? 's' : '' }} sur le portail — informations incomplètes.
            </span>
        </div>
        <a href="{{ route('admin.biens.index', ['statut' => 'disponible']) }}"
           class="font-body font-semibold text-sm text-amber-600 hover:text-amber-800 transition-colors duration-150 whitespace-nowrap">
            Voir les biens →
        </a>
    </div>
    @endif

    {{-- Limite unités --}}
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
           class="font-body font-semibold text-sm text-bimo-gold hover:text-bimo-navy transition-colors duration-150 whitespace-nowrap">
            Voir les plans →
        </a>
    </div>
    @endif

    {{-- ═══ ACTIONS RAPIDES ═══ --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">

        {{-- Impayés --}}
        <a href="{{ route('admin.impayes.index') }}"
           class="flex items-center gap-4 p-4 bg-white rounded-[14px] border transition-all duration-150 hover:-translate-y-0.5 hover:shadow-md
                  {{ $nb_impayes_mois > 0 ? 'border-bimo-red/30 bg-bimo-red/[3%]' : 'border-bimo-navy/10' }}">
            <div class="w-10 h-10 rounded-[10px] flex items-center justify-center flex-shrink-0
                        {{ $nb_impayes_mois > 0 ? 'bg-bimo-red/10' : 'bg-bimo-navy/5' }}">
                <svg class="w-5 h-5 {{ $nb_impayes_mois > 0 ? 'text-bimo-red' : 'text-bimo-navy/30' }}"
                     viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
            </div>
            <div>
                <div class="font-display font-extrabold text-2xl {{ $nb_impayes_mois > 0 ? 'text-bimo-red' : 'text-bimo-navy' }} leading-none">
                    {{ $nb_impayes_mois }}
                </div>
                <div class="font-body text-xs text-bimo-navy/50 mt-1">Impayé{{ $nb_impayes_mois > 1 ? 's' : '' }} ce mois</div>
            </div>
        </a>

        {{-- Enregistrer paiement --}}
        <a href="{{ route('admin.paiements.create') }}"
           class="flex items-center gap-4 p-4 bg-white rounded-[14px] border border-bimo-navy/10
                  transition-all duration-150 hover:-translate-y-0.5 hover:shadow-md">
            <div class="w-10 h-10 rounded-[10px] bg-bimo-gold/10 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-bimo-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
            </div>
            <div>
                <div class="font-body font-semibold text-sm text-bimo-navy">Enregistrer un paiement</div>
                <div class="font-body text-xs text-bimo-navy/40 mt-0.5">Saisir le loyer d'un locataire</div>
            </div>
        </a>

        {{-- Nouveau contrat --}}
        <a href="{{ route('admin.contrats.create') }}"
           class="flex items-center gap-4 p-4 bg-white rounded-[14px] border border-bimo-navy/10
                  transition-all duration-150 hover:-translate-y-0.5 hover:shadow-md">
            <div class="w-10 h-10 rounded-[10px] bg-bimo-navy/5 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-bimo-navy/50" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/>
                </svg>
            </div>
            <div>
                <div class="font-body font-semibold text-sm text-bimo-navy">Nouveau contrat</div>
                <div class="font-body text-xs text-bimo-navy/40 mt-0.5">Créer un bail locatif</div>
            </div>
        </a>
    </div>

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
        {{-- Barre de progression colorée --}}
        <div class="h-1 bg-bimo-bg2">
            <div class="h-full bg-bimo-gold transition-all duration-500 rounded-full"
                 style="width: {{ $pct }}%"></div>
        </div>

        <div class="p-5">
            {{-- Header --}}
            <div class="flex items-start justify-between gap-3 mb-4">
                <div>
                    <div class="font-display font-bold text-sm text-bimo-navy">
                        @if($allDone) Votre agence est prête ! 🎉
                        @else Bienvenue sur Renlio — Commencez ici
                        @endif
                    </div>
                    <div class="font-body text-xs text-bimo-navy/50 mt-0.5">
                        @if($allDone) Toutes les étapes sont complètes. Votre agence est opérationnelle.
                        @else {{ $done }}/{{ $total }} étapes complètes · {{ $pct }}% de configuration
                        @endif
                    </div>
                </div>
                <form method="POST" action="{{ route('admin.onboarding.dismiss') }}" class="flex-shrink-0">
                    @csrf
                    <button type="submit"
                            class="w-7 h-7 flex items-center justify-center rounded-[6px] text-bimo-navy/30
                                   hover:text-bimo-navy hover:bg-bimo-bg transition-all duration-150 text-lg leading-none">
                        ×
                    </button>
                </form>
            </div>

            {{-- Étapes --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                @foreach($steps as $step)
                <div class="flex items-start gap-3 p-3.5 rounded-[10px] border transition-colors duration-150
                            {{ $step['done'] ? 'bg-bimo-gold/[5%] border-bimo-gold/20' : 'bg-bimo-bg border-bimo-navy/10' }}">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5
                                {{ $step['done'] ? 'bg-bimo-gold' : 'bg-bimo-navy/10' }}">
                        @if($step['done'])
                            <svg class="w-3.5 h-3.5 text-bimo-navy" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <polyline points="20 6 9 17 4 12"/>
                            </svg>
                        @else
                            <span class="font-display font-bold text-xs text-bimo-navy/30">{{ $loop->iteration }}</span>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="font-body font-semibold text-xs {{ $step['done'] ? 'text-bimo-gold' : 'text-bimo-navy' }} mb-0.5">
                            {{ $step['label'] }}
                        </div>
                        <div class="font-body text-[10px] text-bimo-navy/40 mb-2">{{ $step['sub'] }}</div>
                        @if(!$step['done'])
                        <a href="{{ $step['url'] }}"
                           class="font-body font-semibold text-[11px] text-bimo-gold hover:text-bimo-navy transition-colors duration-150">
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

    {{-- ═══ KPI GRID ═══ --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">

        {{-- Loyers encaissés — highlighted --}}
        <div class="bg-bimo-gold/[8%] rounded-[14px] border border-bimo-gold/25 p-4">
            <div class="w-9 h-9 rounded-[9px] bg-bimo-gold/20 flex items-center justify-center mb-3">
                <svg class="w-4 h-4 text-bimo-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/>
                </svg>
            </div>
            <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-gold/70 mb-1">Loyers encaissés</div>
            <div class="font-display font-extrabold text-xl text-bimo-gold leading-none">
                {{ number_format($statsMois['loyers'], 0, ',', ' ') }}
                <span class="font-body font-normal text-sm text-bimo-gold/60">F</span>
            </div>
            <div class="font-body text-[10.5px] text-bimo-gold/60 mt-1.5">{{ $statsMois['nb_payes'] }} paiements {{ $periodeLabel }}</div>
        </div>

        {{-- Taux d'occupation --}}
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-4">
            <div class="w-9 h-9 rounded-[9px] bg-bimo-navy/5 flex items-center justify-center mb-3">
                <svg class="w-4 h-4 text-bimo-navy/40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
                    <rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
                </svg>
            </div>
            <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-navy/50 mb-1">Taux d'occupation</div>
            <div class="font-display font-extrabold text-xl text-bimo-navy leading-none">
                {{ $stats['taux_occupation'] }}
                <span class="font-body font-normal text-sm text-bimo-navy/40">%</span>
            </div>
            <div class="font-body text-[10.5px] text-bimo-navy/40 mt-1.5">{{ $stats['nb_biens_loues'] }} / {{ $stats['nb_biens'] }} biens loués</div>
        </div>

        {{-- Impayés ce mois --}}
        <div class="bg-white rounded-[14px] border {{ $nb_impayes_mois > 0 ? 'border-bimo-red/30' : 'border-bimo-navy/10' }} p-4">
            <div class="w-9 h-9 rounded-[9px] {{ $nb_impayes_mois > 0 ? 'bg-bimo-red/10' : 'bg-bimo-navy/5' }} flex items-center justify-center mb-3">
                <svg class="w-4 h-4 {{ $nb_impayes_mois > 0 ? 'text-bimo-red' : 'text-bimo-navy/30' }}"
                     viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
            </div>
            <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-navy/50 mb-1">Impayés ce mois</div>
            <div class="font-display font-extrabold text-xl {{ $nb_impayes_mois > 0 ? 'text-bimo-red' : 'text-bimo-navy' }} leading-none">
                {{ $nb_impayes_mois }}
                <span class="font-body font-normal text-sm text-bimo-navy/40">contrats</span>
            </div>
            <div class="font-body text-[10.5px] mt-1.5 {{ $nb_impayes_mois > 0 ? 'text-bimo-red/70' : 'text-bimo-navy/40' }}">
                {{ number_format($montant_du_mois, 0, ',', ' ') }} F à recouvrer
            </div>
        </div>

        {{-- Commission agence --}}
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-4">
            <div class="w-9 h-9 rounded-[9px] bg-bimo-navy/5 flex items-center justify-center mb-3">
                <svg class="w-4 h-4 text-bimo-navy/40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/>
                </svg>
            </div>
            <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-navy/50 mb-1">Commission agence</div>
            <div class="font-display font-extrabold text-xl text-bimo-navy leading-none">
                {{ number_format($statsMois['commissions'], 0, ',', ' ') }}
                <span class="font-body font-normal text-sm text-bimo-navy/40">F</span>
            </div>
            <div class="font-body text-[10.5px] text-bimo-navy/40 mt-1.5">TTC · {{ $stats['nb_contrats'] }} contrats actifs</div>
        </div>
    </div>

    {{-- ═══ BILAN DU MOIS (carte sombre) ═══ --}}
    <div class="bg-bimo-navy rounded-[14px] p-5 md:p-7 relative overflow-hidden">
        {{-- Halo décoratif --}}
        <div class="absolute top-0 right-0 w-64 h-64 rounded-full opacity-[0.06]"
             style="background: radial-gradient(circle, #C9A84C 0%, transparent 70%); transform: translate(30%, -30%)">
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-0 relative z-10">
            <div class="md:pr-7 md:border-r md:border-white/10">
                <div class="font-body font-medium text-[10px] uppercase tracking-widest text-white/40 mb-2">Attendu ce mois</div>
                <div class="font-display font-extrabold text-2xl md:text-3xl text-white leading-none">
                    {{ number_format($bilanMois['attendu'], 0, ',', ' ') }}
                    <span class="font-body font-light text-sm text-white/30">F</span>
                </div>
                <div class="inline-flex items-center gap-1.5 mt-2.5 px-2.5 py-1 rounded-full bg-white/[6%] border border-white/10">
                    <span class="font-body text-[11px] text-white/40">{{ $stats['nb_contrats'] }} contrats actifs</span>
                </div>
            </div>

            <div class="md:px-7 md:border-r md:border-white/10">
                <div class="font-body font-medium text-[10px] uppercase tracking-widest text-white/40 mb-2">Encaissé</div>
                <div class="font-display font-extrabold text-2xl md:text-3xl text-bimo-gold leading-none">
                    {{ number_format($bilanMois['encaisse'], 0, ',', ' ') }}
                    <span class="font-body font-light text-sm text-bimo-gold/50">F</span>
                </div>
                <div class="inline-flex items-center gap-1.5 mt-2.5 px-2.5 py-1 rounded-full bg-bimo-gold/10 border border-bimo-gold/20">
                    <span class="font-body text-[11px] text-bimo-gold">
                        ✓ {{ $bilanMois['attendu'] > 0 ? round(($bilanMois['encaisse'] / $bilanMois['attendu']) * 100) : 0 }}% de recouvrement
                    </span>
                </div>
            </div>

            <div class="md:pl-7">
                <div class="font-body font-medium text-[10px] uppercase tracking-widest text-white/40 mb-2">Reliquat</div>
                <div class="font-display font-extrabold text-2xl md:text-3xl leading-none
                            {{ $bilanMois['a_recouvrer'] > 0 ? 'text-bimo-red' : 'text-white' }}">
                    {{ number_format($bilanMois['a_recouvrer'], 0, ',', ' ') }}
                    <span class="font-body font-light text-sm opacity-50">F</span>
                </div>
                @if($bilanMois['a_recouvrer'] > 0)
                <div class="inline-flex items-center gap-1.5 mt-2.5 px-2.5 py-1 rounded-full bg-bimo-red/10 border border-bimo-red/20">
                    <span class="font-body text-[11px] text-bimo-red">⚠ {{ $nb_impayes_mois }} contrats en défaut</span>
                </div>
                @else
                <div class="inline-flex items-center gap-1.5 mt-2.5 px-2.5 py-1 rounded-full bg-bimo-gold/10 border border-bimo-gold/20">
                    <span class="font-body text-[11px] text-bimo-gold">✓ Recouvrement complet</span>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ═══ GRAPHIQUES LIGNE 1 ═══ --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

        {{-- Évolution loyers --}}
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-bimo-navy/[5%]">
                <div>
                    <div class="font-display font-bold text-sm text-bimo-navy">Évolution des loyers</div>
                    <div class="font-body text-[11px] text-bimo-navy/40 mt-0.5">6 derniers mois</div>
                </div>
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-1.5 font-body text-[11px] text-bimo-navy/40">
                        <div class="w-2.5 h-2.5 rounded-sm bg-bimo-gold"></div> Loyers
                    </div>
                    <div class="flex items-center gap-1.5 font-body text-[11px] text-bimo-navy/40">
                        <div class="w-2.5 h-2.5 rounded-sm bg-bimo-navy/20"></div> Commission
                    </div>
                </div>
            </div>
            <div class="px-5 pt-4 pb-2 flex items-baseline justify-between">
                <div>
                    <div class="font-display font-extrabold text-xl text-bimo-navy">
                        {{ number_format($statsMois['loyers'], 0, ',', ' ') }} F
                    </div>
                    <div class="font-body text-[11px] text-bimo-navy/40 mt-0.5">Encaissés ce mois</div>
                </div>
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-body font-medium bg-bimo-gold/10 border border-bimo-gold/20 text-bimo-gold">
                    <span class="w-1.5 h-1.5 rounded-full bg-bimo-gold"></span>Ce mois
                </span>
            </div>
            <div class="relative h-48 px-5 pb-5">
                @if($loyersParMois->isEmpty())
                <div class="flex flex-col items-center justify-center h-full text-bimo-navy/30 gap-2">
                    <svg class="w-8 h-8 opacity-40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
                    </svg>
                    <span class="font-body text-sm">Aucune donnée disponible</span>
                </div>
                @else
                <canvas id="chartLoyers"></canvas>
                @endif
            </div>
        </div>

        {{-- Répartition biens --}}
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-bimo-navy/[5%]">
                <div class="font-display font-bold text-sm text-bimo-navy">Répartition des biens</div>
                <span class="font-body text-[11px] text-bimo-navy/40">{{ $stats['nb_biens'] }} biens au total</span>
            </div>
            <div class="relative h-44 flex items-center justify-center px-5 pt-5">
                @if($repartitionBiens->isEmpty())
                <div class="flex flex-col items-center justify-center h-full text-bimo-navy/30 gap-2">
                    <svg class="w-8 h-8 opacity-40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>
                    </svg>
                    <span class="font-body text-sm">Aucun bien enregistré</span>
                </div>
                @else
                <canvas id="chartTypes"></canvas>
                @endif
            </div>
            @php
                $typeColors = ['appartement'=>'#C9A84C','villa'=>'#1B4F6B','studio'=>'#163F56','bureau'=>'#8B7A3D','commerce'=>'#E8C99A','terrain'=>'#D4C5A9'];
                $typeLabels = ['appartement'=>'Appartements','villa'=>'Villas','studio'=>'Studios','bureau'=>'Bureaux','commerce'=>'Commerces','terrain'=>'Terrains'];
            @endphp
            <div class="flex flex-wrap gap-x-4 gap-y-2 px-5 pb-4 mt-2">
                @foreach($repartitionBiens as $type => $count)
                <div class="flex items-center gap-1.5 font-body text-xs text-bimo-navy/50">
                    <div class="w-2 h-2 rounded-sm flex-shrink-0" style="background: {{ $typeColors[$type] ?? '#9ca3af' }}"></div>
                    {{ $typeLabels[$type] ?? ucfirst($type) }}
                    <span class="font-semibold text-bimo-navy">{{ $count }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ═══ GRAPHIQUES LIGNE 2 ═══ --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        {{-- Net propriétaires --}}
        <div class="lg:col-span-2 bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-bimo-navy/[5%]">
                <div class="font-display font-bold text-sm text-bimo-navy">Loyers reversés par propriétaire</div>
                <a href="{{ route('admin.rapports.financier') }}"
                   class="font-body text-xs text-bimo-navy/40 hover:text-bimo-gold transition-colors duration-150">
                    Rapport détaillé →
                </a>
            </div>
            <div class="font-body text-[11px] text-bimo-navy/40 px-5 pt-3">Net après commission · 12 derniers mois</div>
            <div class="relative h-52 px-5 pb-5 pt-2">
                @if(count($netParProprietaire) === 0)
                <div class="flex flex-col items-center justify-center h-full text-bimo-navy/30 gap-2">
                    <svg class="w-8 h-8 opacity-40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/>
                    </svg>
                    <span class="font-body text-sm">Aucun paiement enregistré</span>
                </div>
                @else
                <canvas id="chartProprio"></canvas>
                @endif
            </div>
        </div>

        {{-- Statut paiements --}}
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
            <div class="px-5 py-4 border-b border-bimo-navy/[5%]">
                <div class="font-display font-bold text-sm text-bimo-navy">Statut paiements</div>
            </div>
            <div class="relative h-36 flex items-center justify-center px-5 pt-4">
                @if($statsMois['nb_payes'] === 0 && $nb_impayes_mois === 0)
                <div class="flex flex-col items-center justify-center h-full text-bimo-navy/30 gap-2 text-center">
                    <svg class="w-7 h-7 opacity-40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/>
                    </svg>
                    <span class="font-body text-xs">Aucun contrat actif</span>
                </div>
                @else
                <canvas id="chartStatuts"></canvas>
                @endif
            </div>
            <div class="px-5 pb-5 pt-2 space-y-2.5">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2 font-body text-xs text-bimo-navy/50">
                        <div class="w-2 h-2 rounded-sm bg-bimo-gold"></div>Validés
                    </div>
                    <span class="font-display font-bold text-sm text-bimo-navy">{{ $statsMois['nb_payes'] }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2 font-body text-xs text-bimo-navy/50">
                        <div class="w-2 h-2 rounded-sm bg-bimo-red"></div>Impayés
                    </div>
                    <span class="font-display font-bold text-sm text-bimo-red">{{ $nb_impayes_mois }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══ TABLE + IMPAYÉS ═══ --}}
    <div class="grid grid-cols-1 lg:grid-cols-[1fr_360px] gap-4">

        {{-- Derniers paiements --}}
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-bimo-navy/[5%]">
                <div class="font-display font-bold text-sm text-bimo-navy">Derniers paiements</div>
                <a href="{{ route('admin.paiements.index') }}"
                   class="font-body text-xs text-bimo-navy/40 hover:text-bimo-gold transition-colors duration-150">
                    Voir tout →
                </a>
            </div>

            {{-- Mobile : cards --}}
            <div class="md:hidden divide-y divide-bimo-navy/[5%]">
                @forelse($derniersPaiements as $p)
                <div class="px-5 py-3.5">
                    <div class="flex items-center justify-between mb-1">
                        <span class="font-body text-[11px] text-bimo-navy/40 uppercase tracking-widest">
                            {{ $p->reference_paiement }}
                        </span>
                        <span class="font-display font-bold text-sm text-bimo-gold">
                            {{ number_format($p->montant_encaisse, 0, ',', ' ') }} F
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="font-body font-medium text-xs text-bimo-navy">{{ $p->contrat?->bien?->reference ?? '—' }}</div>
                            <div class="font-body text-[11px] text-bimo-navy/40">{{ $p->contrat?->locataire?->name ?? '—' }}</div>
                        </div>
                        @if($p->statut === 'valide')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-body font-medium bg-bimo-gold/10 border border-bimo-gold/20 text-bimo-gold">Validé</span>
                        @elseif($p->statut === 'annule')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-body font-medium bg-bimo-red/10 border border-bimo-red/20 text-bimo-red">Annulé</span>
                        @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-body font-medium bg-bimo-navy/10 border border-bimo-navy/15 text-bimo-navy/60">Attente</span>
                        @endif
                    </div>
                </div>
                @empty
                <div class="px-5 py-10 text-center font-body text-sm text-bimo-navy/30">
                    Aucun paiement enregistré ce mois
                </div>
                @endforelse
            </div>

            {{-- Desktop : table --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-bimo-navy/[5%] bg-bimo-bg">
                            <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40">Référence</th>
                            <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40">Bien / Locataire</th>
                            <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40">Mode</th>
                            <th class="px-5 py-3 text-right font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40">Montant</th>
                            <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40">Statut</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-bimo-navy/[5%]">
                        @forelse($derniersPaiements as $p)
                        <tr class="hover:bg-bimo-bg transition-colors duration-100">
                            <td class="px-5 py-3.5 font-body text-[11px] text-bimo-navy/40 uppercase tracking-widest">
                                {{ $p->reference_paiement }}
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="font-body font-medium text-sm text-bimo-navy">{{ $p->contrat?->bien?->reference ?? '—' }}</div>
                                <div class="font-body text-xs text-bimo-navy/40">{{ $p->contrat?->locataire?->name ?? '—' }}</div>
                            </td>
                            <td class="px-5 py-3.5 font-body text-xs text-bimo-navy/50">
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
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-body font-medium bg-bimo-navy/10 border border-bimo-navy/15 text-bimo-navy/60">Attente</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-5 py-10 text-center font-body text-sm text-bimo-navy/30">
                                Aucun paiement enregistré ce mois
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Colonne droite --}}
        <div class="flex flex-col gap-4">

            {{-- Impayés urgents --}}
            <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
                <div class="flex items-center justify-between px-5 py-4 border-b border-bimo-navy/[5%]">
                    <div class="font-display font-bold text-sm text-bimo-navy">Impayés urgents</div>
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
                        <div class="font-body font-medium text-sm text-bimo-navy truncate">{{ $c->bien?->reference ?? '—' }}</div>
                        <div class="font-body text-xs text-bimo-navy/40 truncate">{{ $c->locataire?->name ?? '—' }}</div>
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

                {{-- Taux de recouvrement --}}
                <div class="px-5 py-4 border-t border-bimo-navy/[5%] bg-bimo-bg">
                    @php $taux = $bilanMois['attendu'] > 0 ? round(($bilanMois['encaisse'] / $bilanMois['attendu']) * 100) : 0; @endphp
                    <div class="flex items-center justify-between mb-2">
                        <span class="font-body font-medium text-xs text-bimo-navy/50">Taux de recouvrement</span>
                        <span class="font-display font-bold text-sm text-bimo-gold">{{ $taux }}%</span>
                    </div>
                    <div class="h-1.5 bg-bimo-navy/10 rounded-full overflow-hidden">
                        <div class="h-full bg-bimo-gold rounded-full transition-all duration-700"
                             style="width: {{ $taux }}%"></div>
                    </div>
                    <div class="flex justify-between mt-1.5">
                        <span class="font-body text-[10px] text-bimo-navy/30">{{ number_format($bilanMois['encaisse'], 0, ',', ' ') }} F encaissés</span>
                        <span class="font-body text-[10px] text-bimo-navy/30">{{ number_format($bilanMois['attendu'], 0, ',', ' ') }} F attendus</span>
                    </div>
                </div>
            </div>

            {{-- Contrats à renouveler --}}
            @if($contrats_a_renouveler->isNotEmpty())
            <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
                <div class="px-5 py-4 border-b border-bimo-navy/[5%]">
                    <div class="font-display font-bold text-sm text-bimo-navy">À renouveler bientôt</div>
                </div>
                @foreach($contrats_a_renouveler as $c)
                <div class="flex items-center gap-3 px-5 py-3.5 border-b border-bimo-navy/[5%] last:border-0 hover:bg-bimo-bg transition-colors duration-100">
                    <div class="w-10 h-10 rounded-[9px] bg-bimo-gold/[8%] border border-bimo-gold/20 flex flex-col items-center justify-center flex-shrink-0">
                        <div class="font-display font-extrabold text-sm text-bimo-gold leading-none">
                            {{ \Carbon\Carbon::parse($c->date_fin)->diffInDays(now()) }}
                        </div>
                        <div class="font-body text-[8px] text-bimo-gold/60 uppercase tracking-wider">jours</div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="font-body font-medium text-sm text-bimo-navy truncate">{{ $c->bien?->reference ?? '—' }}</div>
                        <div class="font-body text-xs text-bimo-navy/40 truncate">{{ $c->locataire?->name ?? '—' }}</div>
                    </div>
                    <div class="font-body text-xs text-bimo-navy/40 whitespace-nowrap">
                        {{ \Carbon\Carbon::parse($c->date_fin)->format('d/m/Y') }}
                    </div>
                </div>
                @endforeach
            </div>
            @endif

        </div>{{-- fin colonne droite --}}
    </div>

</div>

{{-- WhatsApp support --}}
<a href="https://wa.me/+221781318176?text={{ urlencode('Bonjour, j\'ai besoin d\'aide avec Renlio 👋') }}"
   target="_blank" rel="noopener noreferrer"
   class="fixed right-4 z-50 group
          bottom-[84px] lg:bottom-7 lg:right-7"
   title="Contacter le support">
    <div class="relative flex items-center">
        {{-- Label hover --}}
        <div class="absolute right-full mr-3 bg-bimo-navy text-white font-body text-xs font-medium px-3 py-1.5 rounded-[8px]
                    whitespace-nowrap opacity-0 translate-x-2 group-hover:opacity-100 group-hover:translate-x-0
                    transition-all duration-150 pointer-events-none border border-white/10">
            Contacter le support
        </div>
        {{-- Bouton --}}
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
Chart.defaults.font.family = "'DM Sans', sans-serif";
Chart.defaults.color = 'rgba(27,79,107,0.4)';

const GOLD   = '#C9A84C';
const NAVY   = '#1B4F6B';
const RED    = '#EF4444';
const GOLD20 = 'rgba(201,168,76,0.2)';

const loyersData = @json($loyersParMois->pluck('total'));
const commData   = @json($loyersParMois->pluck('commission'));
const moisLabels = @json($loyersParMois->pluck('mois'));

const tip = {
    backgroundColor: NAVY,
    titleColor: '#fff',
    bodyColor: 'rgba(255,255,255,0.6)',
    borderColor: 'rgba(255,255,255,0.1)',
    borderWidth: 1,
    padding: 10,
    cornerRadius: 8,
};

// ── 1. Évolution loyers ────────────────────────────────────────────
const elLoyers = document.getElementById('chartLoyers');
if (elLoyers) {
    const ctx = elLoyers.getContext('2d');
    const gradGold = ctx.createLinearGradient(0, 0, 0, 180);
    gradGold.addColorStop(0, 'rgba(201,168,76,0.2)');
    gradGold.addColorStop(1, 'rgba(201,168,76,0)');
    const gradNavy = ctx.createLinearGradient(0, 0, 0, 180);
    gradNavy.addColorStop(0, 'rgba(27,79,107,0.1)');
    gradNavy.addColorStop(1, 'rgba(27,79,107,0)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: moisLabels,
            datasets: [
                { label: 'Loyers', data: loyersData, borderColor: GOLD, backgroundColor: gradGold, borderWidth: 2.5, pointBackgroundColor: GOLD, pointBorderColor: '#fff', pointBorderWidth: 2, pointRadius: 4, pointHoverRadius: 6, fill: true, tension: 0.4 },
                { label: 'Commission', data: commData, borderColor: 'rgba(27,79,107,0.3)', backgroundColor: gradNavy, borderWidth: 2, pointBackgroundColor: 'rgba(27,79,107,0.3)', pointBorderColor: '#fff', pointBorderWidth: 2, pointRadius: 3, pointHoverRadius: 5, fill: true, tension: 0.4 }
            ]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { ...tip, callbacks: { label: c => ' ' + Number(c.parsed.y).toLocaleString('fr-FR') + ' F' } } },
            scales: {
                x: { grid: { display: false }, border: { display: false }, ticks: { font: { size: 11 } } },
                y: { grid: { color: 'rgba(27,79,107,0.05)', drawTicks: false }, border: { display: false }, ticks: { font: { size: 10 }, callback: v => v >= 1000000 ? (v/1000000).toFixed(1) + 'M' : (v/1000) + 'k' } }
            }
        }
    });
}

// ── 2. Répartition biens ───────────────────────────────────────────
@php
    $tcJs = []; $tlJs = []; $tnJs = [];
    foreach ($repartitionBiens as $t => $n) {
        $tlJs[] = $typeLabels[$t] ?? ucfirst($t);
        $tnJs[] = (int) $n;
        $tcJs[] = $typeColors[$t] ?? '#9ca3af';
    }
@endphp
const elTypes = document.getElementById('chartTypes');
if (elTypes) {
    new Chart(elTypes, {
        type: 'doughnut',
        data: { labels: @json($tlJs), datasets: [{ data: @json($tnJs), backgroundColor: @json($tcJs), borderColor: '#fff', borderWidth: 3, hoverOffset: 6 }] },
        options: { responsive: true, maintainAspectRatio: false, cutout: '72%', plugins: { legend: { display: false }, tooltip: { ...tip, callbacks: { label: c => ' ' + c.label + ' : ' + c.parsed } } } }
    });
}

// ── 3. Net par propriétaire ────────────────────────────────────────
const npl = @json(collect($netParProprietaire)->pluck('proprietaire'));
const npd = @json(collect($netParProprietaire)->pluck('net_total'));
const elProprio = document.getElementById('chartProprio');
if (elProprio) {
    new Chart(elProprio, {
        type: 'bar',
        data: {
            labels: npl,
            datasets: [{ label: 'Net reversé (F)', data: npd, backgroundColor: npl.map((_, i) => `rgba(27,79,107,${Math.max(0.9 - i * 0.12, 0.25)})`), borderRadius: 6, borderSkipped: false }]
        },
        options: {
            indexAxis: 'y', responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { ...tip, callbacks: { label: c => ' Net : ' + Number(c.parsed.x).toLocaleString('fr-FR') + ' F' } } },
            scales: {
                x: { grid: { color: 'rgba(27,79,107,0.05)', drawTicks: false }, border: { display: false }, ticks: { font: { size: 10 }, callback: v => v >= 1000000 ? (v/1000000).toFixed(1) + 'M' : (v/1000) + 'k' } },
                y: { grid: { display: false }, border: { display: false }, ticks: { font: { size: 11 }, color: NAVY, callback: (_, i) => npl[i]?.split(' ')[0] ?? '' } }
            }
        }
    });
}

// ── 4. Statuts paiements ───────────────────────────────────────────
const elStatuts = document.getElementById('chartStatuts');
if (elStatuts) {
    new Chart(elStatuts, {
        type: 'doughnut',
        data: { labels: ['Validés', 'Impayés'], datasets: [{ data: [{{ $statsMois['nb_payes'] }}, {{ max(0, $nb_impayes_mois) }}], backgroundColor: [GOLD, RED], borderColor: '#fff', borderWidth: 3, hoverOffset: 4 }] },
        options: { responsive: true, maintainAspectRatio: false, cutout: '68%', plugins: { legend: { display: false }, tooltip: { ...tip } } }
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
