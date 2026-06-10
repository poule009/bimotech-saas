@extends('layouts.app')
@section('header', 'Relances & impayés')

@section('content')
<div class="space-y-4 md:space-y-5">

    {{-- En-tête --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <h1 class="font-display font-extrabold text-xl md:text-2xl text-bimo-text tracking-tight leading-tight">
                Relances & impayés
            </h1>
            <p class="font-body text-sm text-bimo-text/50 mt-1">
                {{ $periode->translatedFormat('F Y') }}
                · {{ $stats['nb_impayes'] }} impayé(s) sur {{ $stats['nb_impayes'] + $stats['nb_payes'] }} contrats actifs
            </p>
            <a href="{{ route('admin.rapports.financier', ['mois' => $mois, 'annee' => $annee]) }}"
               class="inline-flex items-center gap-1 font-body text-xs text-bimo-text/40 hover:text-bimo-text transition-colors duration-150 mt-1">
                <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                Voir le bilan mensuel
            </a>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            {{-- Export Excel --}}
            <a href="{{ route('admin.impayes.export', ['mois' => $mois, 'annee' => $annee]) }}"
               id="btn-export-excel"
               onclick="lancerExport(this)"
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-bimo-navy/15 text-bimo-text/60
                      font-body font-medium text-sm rounded-[10px] hover:text-bimo-text hover:border-bimo-navy/30
                      transition-all duration-150">
                <svg class="excel-icon w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <line x1="12" y1="18" x2="12" y2="12"/>
                    <polyline points="9 15 12 18 15 15"/>
                </svg>
                <span class="excel-label">Exporter Excel</span>
            </a>

            {{-- Navigation mois --}}
            @php
                $prevMois  = $mois == 1  ? 12 : $mois - 1;
                $prevAnnee = $mois == 1  ? $annee - 1 : $annee;
                $nextMois  = $mois == 12 ? 1  : $mois + 1;
                $nextAnnee = $mois == 12 ? $annee + 1 : $annee;
            @endphp
            <div class="flex items-center gap-1">
                <a href="{{ route('admin.impayes.index', ['mois' => $prevMois, 'annee' => $prevAnnee]) }}"
                   class="w-8 h-8 flex items-center justify-center border border-bimo-navy/15 rounded-[7px]
                          text-bimo-text/50 hover:text-bimo-text hover:border-bimo-gold transition-all duration-150">
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                </a>
                <div class="font-display font-semibold text-sm text-bimo-text min-w-[130px] text-center px-2">
                    {{ $periode->translatedFormat('F Y') }}
                </div>
                @if($nextAnnee < now()->year || ($nextAnnee == now()->year && $nextMois <= now()->month))
                <a href="{{ route('admin.impayes.index', ['mois' => $nextMois, 'annee' => $nextAnnee]) }}"
                   class="w-8 h-8 flex items-center justify-center border border-bimo-navy/15 rounded-[7px]
                          text-bimo-text/50 hover:text-bimo-text hover:border-bimo-gold transition-all duration-150">
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                </a>
                @else
                <span class="w-8 h-8 flex items-center justify-center border border-bimo-navy/10 rounded-[7px] text-bimo-text/20 cursor-not-allowed">
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                </span>
                @endif
            </div>
        </div>
    </div>

    {{-- KPIs --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="bg-white rounded-[14px] border border-bimo-red/20 p-4 border-t-2 border-t-bimo-red">
            <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-text/50 mb-1">Impayés</div>
            <div class="font-display font-extrabold text-2xl text-bimo-red leading-none">{{ $stats['nb_impayes'] }}</div>
            <div class="font-body text-[10.5px] text-bimo-text/40 mt-1.5">Contrats sans paiement</div>
        </div>
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-4 border-t-2 border-t-bimo-gold">
            <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-text/50 mb-1">Payés</div>
            <div class="font-display font-extrabold text-2xl text-bimo-gold leading-none">{{ $stats['nb_payes'] }}</div>
            <div class="font-body text-[10.5px] text-bimo-text/40 mt-1.5">Paiements validés</div>
        </div>
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-4">
            <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-text/50 mb-1">Montant dû</div>
            <div class="font-display font-extrabold text-xl text-bimo-red leading-none">
                {{ number_format($stats['montant_du'], 0, ',', ' ') }}
                <span class="font-body font-normal text-sm text-bimo-red/50">F</span>
            </div>
            <div class="font-body text-[10.5px] text-bimo-text/40 mt-1.5">FCFA à recouvrer</div>
        </div>
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-4">
            <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-text/50 mb-1">Taux recouvrement</div>
            @php $taux = $stats['taux_recouvrement']; @endphp
            <div class="font-display font-extrabold text-2xl leading-none
                        {{ $taux >= 80 ? 'text-bimo-gold' : ($taux >= 50 ? 'text-bimo-navy' : 'text-bimo-red') }}">
                {{ $taux }}%
            </div>
            <div class="font-body text-[10.5px] text-bimo-text/40 mt-1.5">Ce mois</div>
        </div>
    </div>

    {{-- Barre de recouvrement --}}
    <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-4">
        <div class="flex items-center justify-between mb-2">
            <span class="font-body font-medium text-sm text-bimo-text">Taux de recouvrement</span>
            <span class="font-display font-bold text-sm
                         {{ $taux >= 80 ? 'text-bimo-gold' : ($taux >= 50 ? 'text-bimo-navy' : 'text-bimo-red') }}">
                {{ $taux }}%
            </span>
        </div>
        <div class="h-2.5 bg-bimo-navy/10 rounded-full overflow-hidden">
            <div class="h-full rounded-full transition-all duration-500 {{ $taux >= 80 ? 'bg-bimo-gold' : ($taux >= 50 ? 'bg-bimo-navy' : 'bg-bimo-red') }}"
                 style="width:{{ $taux }}%">
            </div>
        </div>
        <div class="flex items-center justify-between mt-1.5">
            <span class="font-body text-[10px] text-bimo-text/40">{{ $stats['nb_payes'] }} payés</span>
            <span class="font-body text-[10px] text-bimo-text/40">{{ $stats['nb_impayes'] }} impayés</span>
        </div>
    </div>

    {{-- ═══ IMPAYÉS ═══ --}}
    @if($impayes->isNotEmpty())
    <div class="bg-white rounded-[14px] border border-bimo-red/20 overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-bimo-red/10 bg-bimo-red/[3%]">
            <div class="flex items-center gap-2 font-display font-bold text-sm text-bimo-red">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                Impayés — {{ $impayes->count() }} contrat(s)
            </div>
            <span class="font-body text-[11px] text-bimo-text/40">Triés par retard décroissant</span>
        </div>

        {{-- Mobile cards --}}
        <div class="md:hidden divide-y divide-bimo-navy/[5%]">
            @foreach($impayes as $item)
            @php
                $jr = $item['jours_retard'];
                $loc = $item['contrat']->locataire;
                $tel = $loc?->telephone ?? null;
                $telClean = $tel ? preg_replace('/[^0-9]/', '', $tel) : null;
                if ($telClean && strlen($telClean) === 9) $telClean = '221' . $telClean;
                $waMsg = rawurlencode("Bonjour " . ($loc?->name ?? '') . ", votre loyer de " . number_format($item['montant_du'], 0, ',', ' ') . " FCFA pour " . $periode->translatedFormat('F Y') . " n'a pas encore été reçu. Merci de régulariser.");
                $urgClass = $jr > 15 ? 'bg-bimo-red/10 border-bimo-red/20 text-bimo-red' : ($jr > 7 ? 'bg-amber-50 border-amber-200 text-amber-700' : 'bg-bimo-navy/10 border-bimo-navy/15 text-bimo-text/60');
            @endphp
            <div class="px-5 py-4 {{ $jr > 15 ? 'bg-bimo-red/[2%]' : '' }}">
                <div class="flex items-start justify-between gap-3 mb-3">
                    <div>
                        <div class="font-body font-semibold text-sm text-bimo-text">{{ $item['contrat']->bien?->reference ?? '—' }}</div>
                        <div class="font-body text-xs text-bimo-text/50">{{ $loc?->name ?? '—' }}</div>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <div class="font-display font-bold text-base text-bimo-red">{{ number_format($item['montant_du'], 0, ',', ' ') }} FCFA</div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full border text-[10px] font-body font-medium {{ $urgClass }}">
                            {{ $jr > 15 ? '🔴 Haute' : ($jr > 7 ? '🟡 Moyenne' : '⚪ Faible') }}
                        </span>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-1.5">
                    <button onclick="ouvrirPaiementRapide({{ $item['contrat']->id }}, '{{ addslashes($loc?->name ?? '—') }}', '{{ addslashes($item['contrat']->bien?->reference ?? '—') }}', {{ $item['montant_du'] }}, '{{ $mois }}/{{ $annee }}')"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-bimo-gold/10 border border-bimo-gold/20 rounded-[7px] font-body text-xs text-bimo-gold hover:bg-bimo-gold/20 transition-all duration-150">
                        + Paiement
                    </button>
                    @if($telClean)
                    <a href="https://wa.me/{{ $telClean }}?text={{ $waMsg }}" target="_blank"
                       class="w-7 h-7 flex items-center justify-center rounded-[6px] border"
                       style="background:#25D366;border-color:#25D366;color:#fff">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    </a>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        {{-- Desktop table --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-bimo-navy/[5%] bg-bimo-bg">
                        <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Bien</th>
                        <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Locataire</th>
                        <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Propriétaire</th>
                        <th class="px-5 py-3 text-right font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Loyer dû</th>
                        <th class="px-5 py-3 text-center font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Retard</th>
                        <th class="px-5 py-3 text-center font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Urgence</th>
                        <th class="px-5 py-3 text-center font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-bimo-navy/[5%]">
                    @foreach($impayes as $item)
                    @php
                        $jr = $item['jours_retard'];
                        $loc = $item['contrat']->locataire;
                        $tel = $loc?->telephone ?? null;
                        $telClean = $tel ? preg_replace('/[^0-9]/', '', $tel) : null;
                        if ($telClean && strlen($telClean) === 9) $telClean = '221' . $telClean;
                        $waMsg = rawurlencode("Bonjour " . ($loc?->name ?? '') . ", votre loyer de " . number_format($item['montant_du'], 0, ',', ' ') . " FCFA pour " . $periode->translatedFormat('F Y') . " n'a pas encore été reçu. Merci de régulariser.");
                    @endphp
                    <tr class="transition-colors duration-100 {{ $jr > 15 ? 'bg-bimo-red/[2%] hover:bg-bimo-red/[3%]' : 'hover:bg-bimo-bg' }}">
                        <td class="px-5 py-3.5">
                            <div class="font-body font-medium text-sm text-bimo-text">{{ $item['contrat']->bien?->reference ?? '—' }}</div>
                            <div class="font-body text-xs text-bimo-text/50">{{ $item['contrat']->bien?->adresse }}, {{ $item['contrat']->bien?->ville }}</div>
                        </td>
                        <td class="px-5 py-3.5">
                            <div class="font-body text-sm text-bimo-text">{{ $loc?->name ?? '—' }}</div>
                            @if($tel)
                            <a href="tel:{{ $telClean ?? $tel }}"
                               class="inline-flex items-center gap-1 font-body text-[11px] text-bimo-text/50 hover:text-bimo-text transition-colors duration-150">
                                <svg class="w-2.5 h-2.5 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 9.81 19.79 19.79 0 01.07 1.18 2 2 0 012 0h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.09 7.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/></svg>
                                {{ $tel }}
                            </a>
                            @else
                            <div class="font-body text-[11px] text-bimo-text/40">{{ $loc?->email ?? '' }}</div>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 font-body text-xs text-bimo-text/60">{{ $item['contrat']->bien?->proprietaire?->name ?? '—' }}</td>
                        <td class="px-5 py-3.5 text-right font-display font-bold text-sm text-bimo-red">
                            {{ number_format($item['montant_du'], 0, ',', ' ') }} F
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            @if($jr > 0)
                            <span class="font-display font-bold text-sm {{ $jr > 15 ? 'text-bimo-red' : ($jr > 7 ? 'text-amber-500' : 'text-bimo-text/50') }}">
                                {{ $jr }}j
                            </span>
                            @else
                            <span class="font-body text-xs text-bimo-text/30">Ce mois</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            @if($jr > 15)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full border border-bimo-red/20 bg-bimo-red/10 text-[11px] font-body font-medium text-bimo-red">🔴 Haute</span>
                            @elseif($jr > 7)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full border border-amber-200 bg-amber-50 text-[11px] font-body font-medium text-amber-700">🟡 Moyenne</span>
                            @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full border border-bimo-navy/15 bg-bimo-navy/5 text-[11px] font-body font-medium text-bimo-text/50">⚪ Faible</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5">
                            <div class="flex items-center justify-center gap-1.5 flex-wrap">
                                <button type="button"
                                        onclick="ouvrirPaiementRapide({{ $item['contrat']->id }}, '{{ addslashes($loc?->name ?? '—') }}', '{{ addslashes($item['contrat']->bien?->reference ?? '—') }}', {{ $item['montant_du'] }}, '{{ $mois }}/{{ $annee }}')"
                                        class="w-9 h-9 flex items-center justify-center border border-bimo-gold/30 rounded-[6px] text-bimo-gold/60 hover:text-bimo-gold hover:border-bimo-gold/60 hover:bg-bimo-gold/5 transition-all duration-150"
                                        title="Saisir le paiement rapidement">
                                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                </button>
                                @if($telClean)
                                <a href="https://wa.me/{{ $telClean }}?text={{ $waMsg }}" target="_blank"
                                   class="w-7 h-7 flex items-center justify-center rounded-[6px] border transition-all duration-150"
                                   style="background:#25D366;border-color:#25D366;color:#fff"
                                   title="WhatsApp">
                                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                </a>
                                @endif
                                <a href="{{ route('admin.contrats.show', $item['contrat']) }}"
                                   class="w-9 h-9 flex items-center justify-center border border-bimo-navy/10 rounded-[6px] text-bimo-text/40 hover:text-bimo-text hover:border-bimo-navy/30 transition-all duration-150"
                                   title="Voir le contrat">
                                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                </a>
                                <form method="POST" action="{{ route('admin.impayes.relance', $item['contrat']) }}"
                                      data-confirm="Un email de relance sera envoyé à {{ $item['contrat']->locataire?->name ?? 'ce locataire' }}."
                                      data-confirm-title="Envoyer une relance ?"
                                      data-confirm-ok="Envoyer"
                                      data-confirm-color="#d97706"
                                      data-confirm-icon-bg="rgba(217,119,6,0.1)">
                                    @csrf
                                    <input type="hidden" name="mois" value="{{ $mois }}">
                                    <input type="hidden" name="annee" value="{{ $annee }}">
                                    <button type="submit"
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1.5 bg-amber-50 border border-amber-200 rounded-[6px] font-body font-semibold text-[11px] text-amber-700 hover:bg-amber-100 transition-all duration-150"
                                            title="Envoyer relance email">
                                        <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                                        Relancer
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @else
    {{-- Aucun impayé --}}
    <div class="flex flex-col items-center justify-center py-12 px-6 bg-bimo-gold/[5%] border border-bimo-gold/20 rounded-[14px] text-center">
        <svg class="w-10 h-10 text-bimo-gold mb-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
        <div class="font-display font-bold text-base text-bimo-gold mb-1">Aucun impayé ce mois !</div>
        <p class="font-body text-sm text-bimo-text/50">Tous les loyers de {{ $periode->translatedFormat('F Y') }} ont été réglés.</p>
    </div>
    @endif

    {{-- ═══ PAYÉS ═══ --}}
    @if($payes->isNotEmpty())
    <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
        <div class="flex items-center gap-2 px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
            <svg class="w-4 h-4 text-bimo-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
            <span class="font-display font-bold text-sm text-bimo-text">Payés — {{ $payes->count() }} contrat(s)</span>
        </div>

        {{-- Mobile --}}
        <div class="md:hidden divide-y divide-bimo-navy/[5%]">
            @foreach($payes as $item)
            <div class="px-5 py-3.5 flex items-center justify-between gap-3">
                <div>
                    <div class="font-body font-semibold text-sm text-bimo-text">{{ $item['contrat']->bien?->reference ?? '—' }}</div>
                    <div class="font-body text-xs text-bimo-text/50">{{ $item['contrat']->locataire?->name ?? '—' }}</div>
                </div>
                <div class="text-right">
                    <div class="font-display font-bold text-sm text-bimo-gold">{{ number_format($item['paiement']->montant_encaisse, 0, ',', ' ') }} FCFA</div>
                    <div class="font-body text-[10px] text-bimo-text/40">{{ $item['paiement']->date_paiement ? \Carbon\Carbon::parse($item['paiement']->date_paiement)->format('d/m/Y') : '—' }}</div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Desktop --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-bimo-navy/[5%] bg-bimo-bg">
                        <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Bien</th>
                        <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Locataire</th>
                        <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Date paiement</th>
                        <th class="px-5 py-3 text-right font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Montant</th>
                        <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Mode</th>
                        <th class="px-5 py-3 text-center font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-bimo-navy/[5%]">
                    @foreach($payes as $item)
                    @php
                        $modes = ['especes'=>'Espèces','virement'=>'Virement','cheque'=>'Chèque','wave'=>'Wave','orange_money'=>'Orange Money','free_money'=>'Free Money','e_money'=>'E-Money'];
                    @endphp
                    <tr class="hover:bg-bimo-bg transition-colors duration-100">
                        <td class="px-5 py-3.5">
                            <div class="font-body font-medium text-sm text-bimo-text">{{ $item['contrat']->bien?->reference ?? '—' }}</div>
                            <div class="font-body text-xs text-bimo-text/50">{{ $item['contrat']->bien?->ville }}</div>
                        </td>
                        <td class="px-5 py-3.5 font-body text-sm text-bimo-text">{{ $item['contrat']->locataire?->name ?? '—' }}</td>
                        <td class="px-5 py-3.5 font-body text-xs text-bimo-text/60">
                            {{ $item['paiement']->date_paiement ? \Carbon\Carbon::parse($item['paiement']->date_paiement)->format('d/m/Y') : '—' }}
                        </td>
                        <td class="px-5 py-3.5 text-right font-display font-bold text-sm text-bimo-gold">
                            {{ number_format($item['paiement']->montant_encaisse, 0, ',', ' ') }} F
                        </td>
                        <td class="px-5 py-3.5 font-body text-xs text-bimo-text/60">
                            {{ $modes[$item['paiement']->mode_paiement] ?? $item['paiement']->mode_paiement }}
                        </td>
                        <td class="px-5 py-3.5">
                            <div class="flex items-center justify-center gap-1.5">
                                <a href="{{ route('admin.paiements.show', $item['paiement']) }}"
                                   class="w-9 h-9 flex items-center justify-center border border-bimo-navy/10 rounded-[6px] text-bimo-text/40 hover:text-bimo-text hover:border-bimo-navy/30 transition-all duration-150"
                                   title="Voir">
                                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                </a>
                                <a href="{{ route('admin.paiements.pdf', $item['paiement']) }}" target="_blank"
                                   class="w-9 h-9 flex items-center justify-center border border-bimo-navy/10 rounded-[6px] text-bimo-text/40 hover:text-bimo-gold hover:border-bimo-gold/30 transition-all duration-150"
                                   title="PDF">
                                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>

{{-- MODALE PAIEMENT RAPIDE --}}
<div id="modal-paiement-rapide"
     class="fixed inset-0 bg-bimo-navy/50 backdrop-blur-sm z-[200] items-center justify-center p-4"
     style="display:none">
    <div class="bg-white rounded-[20px] w-full max-w-sm shadow-xl p-6">
        <div class="flex items-start justify-between mb-5">
            <div>
                <div class="font-display font-bold text-base text-bimo-text" id="pr-title">Enregistrer un paiement</div>
                <div class="font-body text-xs text-bimo-text/50 mt-0.5" id="pr-sub"></div>
            </div>
            <button onclick="fermerPaiementRapide()"
                    class="w-7 h-7 flex items-center justify-center text-bimo-text/30 hover:text-bimo-text transition-colors duration-150 text-lg leading-none">
                ×
            </button>
        </div>

        <form method="POST" action="{{ route('admin.paiements.store') }}" id="form-paiement-rapide" class="space-y-3">
            @csrf
            <input type="hidden" name="contrat_id" id="pr-contrat-id">
            <input type="hidden" name="statut" value="valide">

            <div class="grid grid-cols-2 gap-3">
                <div class="space-y-1.5">
                    <label class="block font-body font-medium text-sm text-bimo-text">Période <span class="text-bimo-red">*</span></label>
                    <input type="month" name="periode" id="pr-periode" value="{{ now()->format('Y-m') }}" required
                           class="w-full px-3 py-2.5 rounded-[9px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                  focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                </div>
                <div class="space-y-1.5">
                    <label class="block font-body font-medium text-sm text-bimo-text">Date <span class="text-bimo-red">*</span></label>
                    <input type="date" name="date_paiement" id="pr-date" value="{{ now()->format('Y-m-d') }}" required
                           class="w-full px-3 py-2.5 rounded-[9px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                  focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div class="space-y-1.5">
                    <label class="block font-body font-medium text-sm text-bimo-text">Montant (FCFA) <span class="text-bimo-red">*</span></label>
                    <input type="number" name="montant_encaisse" id="pr-montant" min="0" step="500" required
                           class="w-full px-3 py-2.5 rounded-[9px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                  focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                    <p class="font-body text-[11px] text-bimo-text/30" id="pr-montant-hint"></p>
                </div>
                <div class="space-y-1.5">
                    <label class="block font-body font-medium text-sm text-bimo-text">Mode <span class="text-bimo-red">*</span></label>
                    <select name="mode_paiement" required
                            class="w-full px-3 py-2.5 rounded-[9px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text cursor-pointer
                                   focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                        <option value="especes">Espèces</option>
                        <option value="wave">Wave</option>
                        <option value="orange_money">Orange Money</option>
                        <option value="virement">Virement</option>
                        <option value="cheque">Chèque</option>
                        <option value="free_money">Free Money</option>
                        <option value="e_money">E-Money</option>
                    </select>
                </div>
            </div>

            <div class="space-y-1.5">
                <label class="block font-body font-medium text-sm text-bimo-text">
                    Notes <span class="font-normal text-bimo-text/40 text-xs ml-1">(optionnel)</span>
                </label>
                <textarea name="notes" rows="2" placeholder="Observations…"
                          class="w-full px-3 py-2.5 rounded-[9px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                 placeholder:text-bimo-text/30 focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15
                                 transition-all duration-150 resize-none"></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-1">
                <button type="button" onclick="fermerPaiementRapide()"
                        class="px-4 py-2.5 border border-bimo-navy/15 rounded-[10px] font-body text-sm text-bimo-text/60 hover:text-bimo-text transition-all duration-150">
                    Annuler
                </button>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-[var(--ac)] text-white font-display font-bold text-sm rounded-[10px] hover:opacity-90 transition-opacity duration-150">
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    Valider le paiement
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function lancerExport(btn) {
    btn.classList.add('loading');
    setTimeout(() => btn.classList.remove('loading'), 8000);
}

function ouvrirPaiementRapide(contratId, locataire, bien, montant, periode) {
    document.getElementById('pr-contrat-id').value      = contratId;
    document.getElementById('pr-title').textContent     = 'Paiement — ' + bien;
    document.getElementById('pr-sub').textContent       = locataire + ' · ' + periode;
    document.getElementById('pr-montant').value         = montant;
    document.getElementById('pr-montant-hint').textContent = 'Loyer attendu : ' + Number(montant).toLocaleString('fr-FR') + ' F';
    var parts = periode.split('/');
    if (parts.length === 2) document.getElementById('pr-periode').value = parts[1] + '-' + parts[0].padStart(2,'0');
    var modal = document.getElementById('modal-paiement-rapide');
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
    document.getElementById('pr-montant').focus();
}

function fermerPaiementRapide() {
    document.getElementById('modal-paiement-rapide').style.display = 'none';
    document.body.style.overflow = '';
}

document.getElementById('modal-paiement-rapide').addEventListener('click', function(e) {
    if (e.target === this) fermerPaiementRapide();
});
document.addEventListener('keydown', e => { if (e.key === 'Escape') fermerPaiementRapide(); });
</script>
@endpush

@endsection
