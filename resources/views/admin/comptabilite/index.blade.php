@extends('layouts.app')
@section('header', 'Comptabilité')

@section('content')
@php
    $fmt = fn($n) => number_format((float) $n, 0, ',', ' ');
    $totalCaisse = $partProprietaires + $partAgence;
    $pctProprio  = $totalCaisse > 0 ? round($partProprietaires / $totalCaisse * 100) : 0;
    $pctAgence   = 100 - $pctProprio;
    $resultatNet = $resultat['resultat_net'];
@endphp

<div x-data="expenseForm"
     data-owner-action-base="{{ route('admin.paiements.depenses.store', ['paiement' => '__PID__']) }}"
     data-selected-class="border-[var(--ac)] bg-bimo-bg2"
     data-unselected-class="border-bimo-navy/15 hover:border-bimo-navy/30 bg-white"
     data-open-init="{{ $errors->any() ? '1' : '' }}">
<div class="space-y-4 md:space-y-6">

    {{-- ═══ En-tête ═══ --}}
    <div class="flex items-start justify-between gap-4 flex-wrap">
        <div>
            <h1 class="font-display font-extrabold text-xl md:text-2xl text-bimo-text tracking-tight leading-tight">Argent de l'agence</h1>
            <p class="font-body text-sm text-bimo-text/50 mt-1">{{ \Carbon\Carbon::createFromDate($annee, $mois, 1)->locale('fr')->translatedFormat('F Y') }} — loyers, commissions, reversements et dépenses</p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('admin.rapports.financier') }}"
               class="inline-flex items-center gap-2 bg-white border border-bimo-navy/15 text-bimo-text/70 hover:text-bimo-text hover:border-bimo-navy/30 font-display font-bold text-sm px-4 py-2.5 rounded-[10px] transition-all duration-150">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                <span class="hidden sm:inline">Télécharger un récap</span>
            </a>
            <button type="button" @click="toggle"
                    class="inline-flex items-center gap-2 bg-[var(--ac)] text-white font-display font-bold text-sm px-4 py-2.5 rounded-[10px] hover:opacity-90 transition-opacity duration-150">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Ajouter une dépense
            </button>
        </div>
    </div>

    {{-- ═══ Formulaire nouvelle dépense ═══ --}}
    <div x-show="open" x-cloak
         x-transition:enter="transition duration-150"
         x-transition:enter-start="opacity-0 -translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="bg-white rounded-[14px] border border-bimo-navy/10 p-5 md:p-6 space-y-5">
        <div>
            <h2 class="font-display font-bold text-base text-bimo-text">Ajouter une dépense</h2>
            <p class="font-body text-sm text-bimo-text/50 mt-1">D'abord, choisissez qui paie cette dépense — ça change où elle est enregistrée.</p>
        </div>

        {{-- Choix : propriétaire ou agence --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <button type="button" @click="chooseOwner" :class="ownerCardClass"
                    class="text-left border rounded-[12px] p-4 transition-all duration-150">
                <p class="font-display font-bold text-sm text-bimo-text mb-1">C'est pour un propriétaire</p>
                <p class="font-body text-xs text-bimo-text/50 leading-relaxed">Ex : réparation, taxe foncière. Sera retirée de son loyer avant le prochain reversement.</p>
            </button>
            <button type="button" @click="chooseAgency" :class="agencyCardClass"
                    class="text-left border rounded-[12px] p-4 transition-all duration-150">
                <p class="font-display font-bold text-sm text-bimo-text mb-1">C'est pour l'agence</p>
                <p class="font-body text-xs text-bimo-text/50 leading-relaxed">Ex : salaire, loyer du bureau, logiciel. Sort de l'argent de l'agence, pas de celui des propriétaires.</p>
            </button>
        </div>

        {{-- ── Form A : dépense propriétaire (DepenseGestion, rattachée à un paiement) ── --}}
        <form x-show="isOwner" :action="ownerFormAction" method="POST" class="space-y-4">
            @csrf
            @if($paiementsImputables->isEmpty())
            <p class="font-body text-sm text-bimo-text/50 bg-bimo-bg2 rounded-[10px] p-4">Aucun loyer encaissé ce mois auquel rattacher une dépense propriétaire. Encaissez un loyer d'abord.</p>
            @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1.5 md:col-span-2">
                    <label for="paiement_id" class="block font-body font-medium text-sm text-bimo-text">Sur quel loyer encaissé ? <span class="text-bimo-red">*</span></label>
                    <select id="paiement_id" x-model="paiementId" required
                            class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150 appearance-none cursor-pointer">
                        <option value="">— Choisir le propriétaire / bien —</option>
                        @foreach($paiementsImputables as $p)
                        <option value="{{ $p['id'] }}">{{ $p['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="space-y-1.5">
                    <label for="o_categorie" class="block font-body font-medium text-sm text-bimo-text">Type de dépense <span class="text-bimo-red">*</span></label>
                    <select id="o_categorie" name="categorie" required
                            class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150 appearance-none cursor-pointer">
                        @foreach($categoriesProprio as $key => $label)
                        <option value="{{ $key }}" @selected(old('categorie') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="space-y-1.5">
                    <label for="o_montant" class="block font-body font-medium text-sm text-bimo-text">Montant (FCFA) <span class="text-bimo-red">*</span></label>
                    <input id="o_montant" name="montant" type="number" min="1" step="1" inputmode="numeric" value="{{ old('montant') }}" placeholder="ex : 40000"
                           class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text placeholder:text-bimo-navy/35 focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                </div>
                <div class="space-y-1.5">
                    <label for="o_libelle" class="block font-body font-medium text-sm text-bimo-text">Intitulé <span class="text-bimo-red">*</span></label>
                    <input id="o_libelle" name="libelle" type="text" maxlength="255" value="{{ old('libelle') }}" placeholder="ex : Réparation fuite d'eau"
                           class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text placeholder:text-bimo-navy/35 focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                </div>
                <div class="space-y-1.5">
                    <label for="o_date" class="block font-body font-medium text-sm text-bimo-text">Date <span class="text-bimo-red">*</span></label>
                    <input id="o_date" name="date_depense" type="date" max="{{ now()->format('Y-m-d') }}" value="{{ old('date_depense', now()->format('Y-m-d')) }}"
                           class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                </div>
            </div>
            <div class="flex items-center gap-3">
                <button type="submit"
                        class="inline-flex items-center gap-2 bg-[var(--ac)] text-white font-display font-bold text-sm px-5 py-2.5 rounded-[10px] hover:opacity-90 transition-opacity duration-150">
                    Enregistrer la dépense
                </button>
                <button type="button" @click="hide"
                        class="inline-flex items-center px-5 py-2.5 rounded-[10px] border border-bimo-navy/15 font-body text-sm text-bimo-text/60 hover:text-bimo-text hover:border-bimo-navy/30 transition-all duration-150">Annuler</button>
            </div>
            @endif
        </form>

        {{-- ── Form B : dépense agence (ChargeAgence) ── --}}
        <form x-show="isAgency" action="{{ route('admin.charges-agence.store') }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label for="a_categorie" class="block font-body font-medium text-sm text-bimo-text">Type de dépense <span class="text-bimo-red">*</span></label>
                    <select id="a_categorie" name="categorie" required
                            class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150 appearance-none cursor-pointer">
                        @foreach($categoriesAgence as $key => $label)
                        <option value="{{ $key }}" @selected(old('categorie') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="space-y-1.5">
                    <label for="a_montant" class="block font-body font-medium text-sm text-bimo-text">Montant (FCFA) <span class="text-bimo-red">*</span></label>
                    <input id="a_montant" name="montant" type="number" min="0" step="1" inputmode="numeric" value="{{ old('montant') }}" placeholder="ex : 75000"
                           class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text placeholder:text-bimo-navy/35 focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                </div>
                <div class="space-y-1.5">
                    <label for="a_libelle" class="block font-body font-medium text-sm text-bimo-text">Intitulé <span class="text-bimo-red">*</span></label>
                    <input id="a_libelle" name="libelle" type="text" maxlength="255" value="{{ old('libelle') }}" placeholder="ex : Loyer bureau Parcelles"
                           class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text placeholder:text-bimo-navy/35 focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                </div>
                <div class="space-y-1.5">
                    <label for="a_date" class="block font-body font-medium text-sm text-bimo-text">Date <span class="text-bimo-red">*</span></label>
                    <input id="a_date" name="date_charge" type="date" value="{{ old('date_charge', now()->format('Y-m-d')) }}"
                           class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                </div>
            </div>
            <div class="flex items-center gap-3">
                <button type="submit"
                        class="inline-flex items-center gap-2 bg-[var(--ac)] text-white font-display font-bold text-sm px-5 py-2.5 rounded-[10px] hover:opacity-90 transition-opacity duration-150">
                    Enregistrer la dépense
                </button>
                <button type="button" @click="hide"
                        class="inline-flex items-center px-5 py-2.5 rounded-[10px] border border-bimo-navy/15 font-body text-sm text-bimo-text/60 hover:text-bimo-text hover:border-bimo-navy/30 transition-all duration-150">Annuler</button>
            </div>
        </form>
    </div>

    {{-- ═══ Sous-nav + panneaux ═══ --}}
    <div x-data="comptaTabs" data-default="apercu"
         data-active-class="bg-bimo-navy text-white"
         data-inactive-class="text-bimo-text/60 hover:text-bimo-text">

        <div class="inline-flex gap-1 bg-white border border-bimo-navy/10 rounded-[12px] p-1">
            <button type="button" @click="selectApercu"  :class="apercuClass"  class="px-4 py-2 rounded-[9px] font-display font-semibold text-sm transition-colors duration-150">Vue d'ensemble</button>
            <button type="button" @click="selectProprio" :class="proprioClass" class="px-4 py-2 rounded-[9px] font-display font-semibold text-sm transition-colors duration-150">Propriétaires</button>
            <button type="button" @click="selectAgence"  :class="agenceClass"  class="px-4 py-2 rounded-[9px] font-display font-semibold text-sm transition-colors duration-150">Agence</button>
        </div>

        {{-- ───────────── VUE D'ENSEMBLE ───────────── --}}
        <div x-show="showApercu" x-cloak class="mt-5 space-y-4 md:space-y-6">

            {{-- Hero : argent en caisse réparti --}}
            <div class="bg-white rounded-[16px] border border-bimo-navy/10 p-5 md:p-6">
                <p class="font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/50 mb-1">Argent en caisse</p>
                <p class="font-display font-extrabold text-3xl md:text-[2.4rem] text-bimo-navy leading-none mb-5">{{ $fmt($totalCaisse) }} <span class="text-lg font-bold text-bimo-text/40">FCFA</span></p>
                <div class="flex h-3.5 rounded-full overflow-hidden bg-bimo-navy/10 mb-4">
                    <div class="bg-bimo-navy" style="width: {{ $pctProprio }}%"></div>
                    <div class="bg-[var(--ac)]" style="width: {{ $pctAgence }}%"></div>
                </div>
                <div class="flex flex-wrap gap-x-8 gap-y-3">
                    <div class="flex items-center gap-2.5">
                        <span class="w-2.5 h-2.5 rounded-[3px] bg-bimo-navy flex-shrink-0"></span>
                        <div>
                            <p class="font-body text-xs text-bimo-text/50">À reverser aux propriétaires · {{ $pctProprio }}%</p>
                            <p class="font-display font-bold text-base text-bimo-text">{{ $fmt($partProprietaires) }} FCFA</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <span class="w-2.5 h-2.5 rounded-[3px] bg-[var(--ac)] flex-shrink-0"></span>
                        <div>
                            <p class="font-body text-xs text-bimo-text/50">Disponible pour l'agence · {{ $pctAgence }}%</p>
                            <p class="font-display font-bold text-base text-bimo-text">{{ $fmt($partAgence) }} FCFA</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- KPIs --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">
                <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-4">
                    <p class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-text/50 mb-2">Loyers reçus ce mois</p>
                    <p class="font-display font-extrabold text-xl text-bimo-text">{{ $fmt($tresorerie['encaisse_locataires']) }}</p>
                    <p class="font-body font-light text-[10.5px] text-bimo-text/40 mt-1">pour le compte des propriétaires</p>
                </div>
                <div class="bg-bimo-gold/[8%] rounded-[14px] border border-bimo-gold/25 p-4">
                    <p class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-gold mb-2">Notre commission</p>
                    <p class="font-display font-extrabold text-xl text-bimo-gold">{{ $fmt($resultat['revenus_total_ht']) }}</p>
                    <p class="font-body font-light text-[10.5px] text-bimo-gold/70 mt-1">ce que l'agence gagne</p>
                </div>
                <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-4">
                    <p class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-text/50 mb-2">Nos dépenses</p>
                    <p class="font-display font-extrabold text-xl text-bimo-text">{{ $fmt($resultat['charges_total']) }}</p>
                    <p class="font-body font-light text-[10.5px] text-bimo-text/40 mt-1">salaires, bureau, outils…</p>
                </div>
                <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-4">
                    <p class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-text/50 mb-2">Reste à donner</p>
                    <p class="font-display font-extrabold text-xl text-bimo-text">{{ $proprietairesAPayer->count() }} propriétaire{{ $proprietairesAPayer->count() > 1 ? 's' : '' }}</p>
                    <p class="font-body font-light text-[10.5px] text-bimo-text/40 mt-1">{{ $fmt($totalAPayer) }} FCFA à payer</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                {{-- Dernières opérations --}}
                <div class="lg:col-span-2 bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
                    <div class="px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
                        <h2 class="font-display font-bold text-sm text-bimo-text">Dernières opérations</h2>
                    </div>
                    @if($dernieresOperations->isEmpty())
                    <p class="px-5 py-8 text-center font-body text-sm text-bimo-text/40">Aucune opération ce mois.</p>
                    @else
                    <div class="divide-y divide-bimo-navy/[5%]">
                        @foreach($dernieresOperations as $op)
                        <div class="px-5 py-3.5 flex items-center justify-between gap-4">
                            <div class="min-w-0">
                                <p class="font-body font-medium text-sm text-bimo-text truncate">{{ $op['libelle'] }}</p>
                                <p class="font-body text-[11px] text-bimo-text/40">{{ optional($op['date'])->format('d/m') }} · {{ $op['type'] }}</p>
                            </div>
                            <p class="font-display font-bold text-sm flex-shrink-0 {{ $op['sens'] === 'out' ? 'text-bimo-red' : 'text-bimo-text' }}">
                                {{ $op['sens'] === 'out' ? '−' : '+' }}{{ $fmt($op['montant']) }}
                            </p>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>

                {{-- Prochains paiements aux propriétaires --}}
                <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
                    <div class="px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2 flex items-center justify-between">
                        <h2 class="font-display font-bold text-sm text-bimo-text">À payer aux propriétaires</h2>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-body font-medium bg-bimo-navy/10 border border-bimo-navy/15 text-bimo-text/70">{{ $proprietairesAPayer->count() }}</span>
                    </div>
                    @if($proprietairesAPayer->isEmpty())
                    <p class="px-5 py-8 text-center font-body text-sm text-bimo-text/40">Tout le monde est à jour ✓</p>
                    @else
                    <div class="divide-y divide-bimo-navy/[5%]">
                        @foreach($proprietairesAPayer->sortByDesc(fn($l) => $l['compte']['solde_restant']) as $l)
                        <div class="px-5 py-3.5 flex items-center justify-between gap-3">
                            <p class="font-body font-medium text-sm text-bimo-text truncate">{{ $l['proprietaire']->name }}</p>
                            <p class="font-display font-bold text-sm text-bimo-navy flex-shrink-0">{{ $fmt($l['compte']['solde_restant']) }}</p>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ───────────── PROPRIÉTAIRES ───────────── --}}
        <div x-show="showProprio" x-cloak class="mt-5 space-y-4 md:space-y-6">
            <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
                <div class="px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
                    <h2 class="font-display font-bold text-sm text-bimo-text">Ce qu'on doit à chaque propriétaire</h2>
                    <p class="font-body text-xs text-bimo-text/50 mt-0.5">Montants du mois affiché. Cliquez « Détail » pour la répartition par bien.</p>
                </div>

                @if($lignesProprietaires->isEmpty())
                <p class="px-5 py-10 text-center font-body text-sm text-bimo-text/40">Aucun loyer encaissé ce mois.</p>
                @else

                {{-- Desktop : tableau --}}
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-bimo-navy/10 bg-bimo-bg2/50">
                                <th class="px-4 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/50">Propriétaire</th>
                                <th class="px-4 py-3 text-right font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/50">Loyers reçus</th>
                                <th class="px-4 py-3 text-right font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/50">Commission</th>
                                <th class="px-4 py-3 text-right font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/50">Dépenses</th>
                                <th class="px-4 py-3 text-right font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/50">À lui donner</th>
                                <th class="px-4 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/50">Statut</th>
                                <th class="px-4 py-3"></th>
                            </tr>
                        </thead>
                        @foreach($lignesProprietaires as $l)
                        @php $c = $l['compte']; $aPayer = $c['solde_restant'] > 0; @endphp
                        <tbody x-data="detailToggle">
                            <tr class="border-b border-bimo-navy/[5%] hover:bg-bimo-bg transition-colors duration-100">
                                <td class="px-4 py-3.5 font-body font-medium text-bimo-text">{{ $l['proprietaire']->name }}<span class="block font-light text-[11px] text-bimo-text/40">{{ $c['nb_biens'] }} bien{{ $c['nb_biens'] > 1 ? 's' : '' }}</span></td>
                                <td class="px-4 py-3.5 text-right font-display tabular-nums text-bimo-text">{{ $fmt($c['loyers_encaisses']) }}</td>
                                <td class="px-4 py-3.5 text-right font-display tabular-nums text-bimo-text/60">−{{ $fmt($c['commissions_deduites']) }}</td>
                                <td class="px-4 py-3.5 text-right font-display tabular-nums {{ $c['depenses_avancees'] > 0 ? 'text-bimo-red' : 'text-bimo-text/40' }}">{{ $c['depenses_avancees'] > 0 ? '−' . $fmt($c['depenses_avancees']) : '—' }}</td>
                                <td class="px-4 py-3.5 text-right font-display font-bold tabular-nums text-bimo-navy">{{ $fmt($c['solde_restant']) }}</td>
                                <td class="px-4 py-3.5">
                                    @if($aPayer)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-body font-medium bg-bimo-navy/10 border border-bimo-navy/15 text-bimo-text/70">À reverser</span>
                                    @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-body font-medium bg-bimo-gold/10 border border-bimo-gold/20 text-bimo-gold">Payé</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 text-right whitespace-nowrap">
                                    <button type="button" @click="toggle" class="inline-flex items-center px-3 py-1.5 rounded-[8px] border border-bimo-navy/15 font-body font-medium text-xs text-bimo-text/70 hover:border-bimo-navy/30 hover:text-bimo-text transition-all duration-150">Détail</button>
                                    @if($aPayer)
                                    <a href="{{ route('admin.reversements.compte-mandant', ['proprietaire' => $l['proprietaire']->id, 'periode' => $periode]) }}"
                                       class="inline-flex items-center px-3 py-1.5 ml-1 rounded-[8px] bg-[var(--ac)] text-white font-body font-semibold text-xs hover:opacity-90 transition-opacity duration-150">Payer</a>
                                    @endif
                                </td>
                            </tr>
                            <tr x-show="open" x-cloak>
                                <td colspan="7" class="px-4 py-4 bg-bimo-bg2/40">
                                    <p class="font-body font-semibold text-xs text-bimo-navy mb-2">Détail par bien — {{ $l['proprietaire']->name }}</p>
                                    <div class="rounded-[10px] border border-bimo-navy/10 overflow-hidden bg-white">
                                        <table class="w-full text-[13px]">
                                            <thead>
                                                <tr class="bg-bimo-bg2/60">
                                                    <th class="px-3 py-2 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/50">Bien</th>
                                                    <th class="px-3 py-2 text-right font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/50">Loyer encaissé</th>
                                                    <th class="px-3 py-2 text-right font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/50">Dépense</th>
                                                    <th class="px-3 py-2 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/50">Motif</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-bimo-navy/[5%]">
                                                @foreach($c['paiements'] as $p)
                                                @php $dep = $p->depenses->sum('montant'); @endphp
                                                <tr>
                                                    <td class="px-3 py-2 font-body text-bimo-text">{{ $p->contrat?->bien?->titre ?: ('Bien ' . ($p->contrat?->bien?->reference ?? '')) }}</td>
                                                    <td class="px-3 py-2 text-right font-display tabular-nums text-bimo-text">{{ $fmt($p->montant_encaisse) }}</td>
                                                    <td class="px-3 py-2 text-right font-display tabular-nums {{ $dep > 0 ? 'text-bimo-red' : 'text-bimo-text/40' }}">{{ $dep > 0 ? '−' . $fmt($dep) : '—' }}</td>
                                                    <td class="px-3 py-2 font-body text-bimo-text/60">{{ $p->depenses->pluck('libelle')->filter()->join(', ') ?: '—' }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                        @endforeach
                    </table>
                </div>

                {{-- Mobile : cards --}}
                <div class="md:hidden divide-y divide-bimo-navy/[5%]">
                    @foreach($lignesProprietaires as $l)
                    @php $c = $l['compte']; $aPayer = $c['solde_restant'] > 0; @endphp
                    <div x-data="detailToggle" class="p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="font-body font-semibold text-sm text-bimo-text truncate">{{ $l['proprietaire']->name }}</p>
                                <p class="font-body font-light text-[11px] text-bimo-text/40">{{ $c['nb_biens'] }} bien{{ $c['nb_biens'] > 1 ? 's' : '' }}</p>
                            </div>
                            @if($aPayer)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-body font-medium bg-bimo-navy/10 border border-bimo-navy/15 text-bimo-text/70 flex-shrink-0">À reverser</span>
                            @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-body font-medium bg-bimo-gold/10 border border-bimo-gold/20 text-bimo-gold flex-shrink-0">Payé</span>
                            @endif
                        </div>
                        <div class="grid grid-cols-3 gap-2 mt-3 text-center">
                            <div><p class="font-body text-[9.5px] uppercase tracking-widest text-bimo-text/40">Loyers</p><p class="font-display font-bold text-sm text-bimo-text mt-0.5">{{ $fmt($c['loyers_encaisses']) }}</p></div>
                            <div><p class="font-body text-[9.5px] uppercase tracking-widest text-bimo-text/40">Dépenses</p><p class="font-display font-bold text-sm {{ $c['depenses_avancees'] > 0 ? 'text-bimo-red' : 'text-bimo-text/40' }} mt-0.5">{{ $c['depenses_avancees'] > 0 ? $fmt($c['depenses_avancees']) : '—' }}</p></div>
                            <div><p class="font-body text-[9.5px] uppercase tracking-widest text-bimo-text/40">À donner</p><p class="font-display font-extrabold text-sm text-bimo-navy mt-0.5">{{ $fmt($c['solde_restant']) }}</p></div>
                        </div>
                        <div class="flex items-center gap-2 mt-3">
                            <button type="button" @click="toggle" class="flex-1 min-h-[44px] inline-flex items-center justify-center rounded-[10px] border border-bimo-navy/15 font-body font-medium text-sm text-bimo-text/70">Détail</button>
                            @if($aPayer)
                            <a href="{{ route('admin.reversements.compte-mandant', ['proprietaire' => $l['proprietaire']->id, 'periode' => $periode]) }}"
                               class="flex-1 min-h-[44px] inline-flex items-center justify-center rounded-[10px] bg-[var(--ac)] text-white font-display font-bold text-sm">Payer</a>
                            @endif
                        </div>
                        <div x-show="open" x-cloak class="mt-3 space-y-2">
                            @foreach($c['paiements'] as $p)
                            @php $dep = $p->depenses->sum('montant'); @endphp
                            <div class="rounded-[10px] bg-bimo-bg2/50 p-3">
                                <p class="font-body font-medium text-sm text-bimo-text">{{ $p->contrat?->bien?->titre ?: ('Bien ' . ($p->contrat?->bien?->reference ?? '')) }}</p>
                                <div class="flex justify-between mt-1 font-body text-xs text-bimo-text/60">
                                    <span>Loyer : {{ $fmt($p->montant_encaisse) }}</span>
                                    <span class="{{ $dep > 0 ? 'text-bimo-red' : '' }}">Dépense : {{ $dep > 0 ? $fmt($dep) : '—' }}</span>
                                </div>
                                @if($p->depenses->pluck('libelle')->filter()->isNotEmpty())
                                <p class="font-body text-[11px] text-bimo-text/40 mt-1">{{ $p->depenses->pluck('libelle')->filter()->join(', ') }}</p>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>

        {{-- ───────────── AGENCE ───────────── --}}
        <div x-show="showAgence" x-cloak class="mt-5 space-y-4 md:space-y-6">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">
                <div class="bg-bimo-gold/[8%] rounded-[14px] border border-bimo-gold/25 p-4">
                    <p class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-gold mb-2">Argent gagné</p>
                    <p class="font-display font-extrabold text-xl text-bimo-gold">{{ $fmt($resultat['revenus_total_ht']) }}</p>
                    <p class="font-body font-light text-[10.5px] text-bimo-gold/70 mt-1">nos commissions</p>
                </div>
                <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-4">
                    <p class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-text/50 mb-2">Nos dépenses</p>
                    <p class="font-display font-extrabold text-xl text-bimo-text">{{ $fmt($resultat['charges_total']) }}</p>
                    <p class="font-body font-light text-[10.5px] text-bimo-text/40 mt-1">{{ $chargesAgence->count() }} dépense{{ $chargesAgence->count() > 1 ? 's' : '' }} ce mois</p>
                </div>
                <div class="bg-white rounded-[14px] border {{ $resultatNet >= 0 ? 'border-bimo-navy/10' : 'border-bimo-red/20' }} p-4">
                    <p class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-text/50 mb-2">Ce qui nous reste</p>
                    <p class="font-display font-extrabold text-xl {{ $resultatNet >= 0 ? 'text-bimo-text' : 'text-bimo-red' }}">{{ ($resultatNet >= 0 ? '+' : '') . $fmt($resultatNet) }}</p>
                    <p class="font-body font-light text-[10.5px] text-bimo-text/40 mt-1">avant impôts</p>
                </div>
                <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-4">
                    <p class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-text/50 mb-2">Pourcentage gardé</p>
                    <p class="font-display font-extrabold text-xl text-bimo-text">{{ $pourcentageGarde }}%</p>
                    <p class="font-body font-light text-[10.5px] text-bimo-text/40 mt-1">de ce qu'on gagne</p>
                </div>
            </div>

            <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
                <div class="px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
                    <h2 class="font-display font-bold text-sm text-bimo-text">Dépenses de l'agence</h2>
                </div>
                @if($chargesAgence->isEmpty())
                <p class="px-5 py-10 text-center font-body text-sm text-bimo-text/40">Aucune dépense ce mois.</p>
                @else
                {{-- Desktop --}}
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-bimo-navy/10 bg-bimo-bg2/50">
                                <th class="px-4 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/50">Dépense</th>
                                <th class="px-4 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/50">Type</th>
                                <th class="px-4 py-3 text-right font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/50">Montant</th>
                                <th class="px-4 py-3 text-right font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/50">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-bimo-navy/[5%]">
                            @foreach($chargesAgence as $charge)
                            <tr class="hover:bg-bimo-bg transition-colors duration-100">
                                <td class="px-4 py-3.5 font-body text-bimo-text">{{ $charge->libelle }}</td>
                                <td class="px-4 py-3.5 font-body text-bimo-text/60">{{ $charge->categorie_libelle }}</td>
                                <td class="px-4 py-3.5 text-right font-display font-bold tabular-nums text-bimo-text">{{ $fmt($charge->montant) }}</td>
                                <td class="px-4 py-3.5 text-right font-body text-bimo-text/50">{{ optional($charge->date_charge)->format('d/m/Y') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{-- Mobile --}}
                <div class="md:hidden divide-y divide-bimo-navy/[5%]">
                    @foreach($chargesAgence as $charge)
                    <div class="p-4 flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <p class="font-body font-medium text-sm text-bimo-text truncate">{{ $charge->libelle }}</p>
                            <p class="font-body text-[11px] text-bimo-text/40">{{ $charge->categorie_label }} · {{ optional($charge->date_charge)->format('d/m/Y') }}</p>
                        </div>
                        <p class="font-display font-bold text-sm text-bimo-text flex-shrink-0">{{ $fmt($charge->montant) }}</p>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>

    </div>
</div>
</div>
@endsection
