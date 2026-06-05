@extends('layouts.app')
@section('header', 'Mon espace')

@section('content')

@php
    $modes = ['especes'=>'Espèces','virement'=>'Virement','cheque'=>'Chèque','wave'=>'Wave','orange_money'=>'Orange Money','free_money'=>'Free Money','e_money'=>'E-Money'];
@endphp

<div class="space-y-4 md:space-y-5">

    {{-- Greeting --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <h1 class="font-display font-extrabold text-xl md:text-2xl text-bimo-text tracking-tight">
                Bonjour, {{ explode(' ', auth()->user()->name)[0] }} 👋
            </h1>
            <p class="font-body text-sm text-bimo-text/50 mt-1">{{ now()->translatedFormat('l d F Y') }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
            @if($contrat)
            <a href="{{ route('locataire.contrat.show', $contrat) }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-bimo-navy text-bimo-gold border border-bimo-gold/30 font-display font-bold text-sm rounded-[10px] hover:bg-bimo-navy-dk transition-colors duration-150">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                Mon contrat
            </a>
            @endif
            <a href="{{ route('locataire.paiements') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-bimo-navy/15 text-bimo-text/60 font-body text-sm rounded-[10px] hover:text-bimo-text hover:border-bimo-navy/30 transition-all duration-150">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
                Mes paiements
            </a>
        </div>
    </div>

    @if(!$contrat)
    {{-- Pas de contrat --}}
    @php $agenceContact = auth()->user()->agency; @endphp
    <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
        <div class="px-6 py-12 text-center border-b border-bimo-navy/[5%]">
            <div class="w-14 h-14 bg-bimo-gold/10 border border-bimo-gold/20 rounded-[14px] flex items-center justify-center mx-auto mb-4">
                <svg class="w-7 h-7 text-bimo-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            </div>
            <div class="font-display font-bold text-base text-bimo-text mb-2">Aucun contrat actif</div>
            <p class="font-body text-sm text-bimo-text/50">Votre contrat de bail n'est pas encore configuré.</p>
        </div>
        <div class="px-6 py-5 bg-bimo-bg">
            <div class="font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40 mb-3">Contacter votre agence</div>
            <div class="font-display font-bold text-base text-bimo-text mb-3">{{ $agenceContact?->name ?? 'Votre agence' }}</div>
            <div class="space-y-2.5">
                @if($agenceContact?->telephone)
                <a href="tel:{{ preg_replace('/[^0-9+]/','',$agenceContact->telephone) }}"
                   class="flex items-center gap-3 font-body text-sm text-bimo-text">
                    <span class="w-7 h-7 bg-bimo-gold/10 rounded-[7px] flex items-center justify-center flex-shrink-0">
                        <svg class="w-3.5 h-3.5 text-bimo-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 9.81"/></svg>
                    </span>
                    {{ $agenceContact->telephone }}
                </a>
                @endif
                @if($agenceContact?->email)
                <a href="mailto:{{ $agenceContact->email }}"
                   class="flex items-center gap-3 font-body text-sm text-bimo-text/60">
                    <span class="w-7 h-7 bg-bimo-navy/5 rounded-[7px] flex items-center justify-center flex-shrink-0">
                        <svg class="w-3.5 h-3.5 text-bimo-text/40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                    </span>
                    {{ $agenceContact->email }}
                </a>
                @endif
            </div>
        </div>
    </div>

    @else
    @php
        $bien         = $contrat->bien;
        $proprietaire = $bien?->proprietaire ?? null;
        $agency       = auth()->user()->agency;
        $debut        = \Carbon\Carbon::parse($contrat->date_debut);
        $fin          = $contrat->date_fin ? \Carbon\Carbon::parse($contrat->date_fin) : null;
        $dureeProgress = null;
        $joursRestants = null;
        if ($fin) {
            $total         = max(1, $debut->diffInDays($fin));
            $ecoule        = min($total, $debut->diffInDays(now()));
            $dureeProgress = round(($ecoule / $total) * 100);
            $joursRestants = now()->diffInDays($fin, false);
        }
    @endphp

    {{-- Prochain loyer --}}
    @if($prochainePeriode)
    <div class="flex items-center justify-between gap-4 bg-bimo-gold/[6%] border border-bimo-gold/25 rounded-[14px] px-5 py-4 flex-wrap">
        <div>
            <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-gold/70 mb-1">Prochain loyer à régler</div>
            <div class="font-display font-bold text-lg text-bimo-gold leading-tight">{{ $prochainePeriode->translatedFormat('F Y') }}</div>
            <div class="font-body text-xs text-bimo-gold/60 mt-0.5">
                @if($dernierPaiement) Dernier paiement : {{ \Carbon\Carbon::parse($dernierPaiement->periode)->translatedFormat('F Y') }}
                @else Premier paiement du bail @endif
            </div>
        </div>
        <div class="text-right flex-shrink-0">
            <div class="font-display font-extrabold text-2xl text-bimo-gold leading-none">{{ number_format($contrat->loyer_contractuel, 0, ',', ' ') }}</div>
            <div class="font-body text-xs text-bimo-gold/60 mt-0.5">FCFA / mois</div>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-[1fr_280px] gap-5 items-start">

        {{-- Colonne gauche --}}
        <div class="space-y-4">

            {{-- Hero logement --}}
            <div class="bg-bimo-navy rounded-[14px] p-5 md:p-7 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-48 h-48 rounded-full opacity-[0.06]"
                     style="background: radial-gradient(circle, #C9A84C 0%, transparent 70%); transform: translate(30%, -30%)"></div>
                <div class="relative z-10">
                    <div class="font-body font-medium text-[9px] uppercase tracking-widests text-white/30 mb-2">Mon logement</div>
                    <div class="font-display font-extrabold text-2xl text-white leading-tight mb-1">{{ $bien?->reference }}</div>
                    <div class="font-body text-sm text-white/50 mb-1">
                        {{ \App\Models\Bien::TYPES[$bien?->type] ?? $bien?->type }}
                        @if($bien?->meuble) · Meublé @endif
                        @if($bien?->surface_m2) · {{ $bien->surface_m2 }} m² @endif
                        @if($bien?->nombre_pieces) · {{ $bien->nombre_pieces }} pièces @endif
                    </div>
                    <div class="flex items-center gap-1.5 font-body text-xs text-white/40">
                        <svg class="w-3 h-3 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        {{ $bien?->adresse }}@if($bien?->quartier), {{ $bien->quartier }}@endif, {{ $bien?->ville }}
                    </div>
                    <div class="grid grid-cols-3 gap-0 mt-5 pt-4 border-t border-white/[8%]">
                        @foreach([
                            ['Loyer mensuel', number_format($contrat->loyer_contractuel,0,',','').' F', 'text-bimo-gold'],
                            ['Début du bail', $debut->format('d/m/Y'), 'text-white'],
                            ['Caution versée', number_format($contrat->caution,0,',','').' F', 'text-white'],
                        ] as $i => [$lbl, $val, $cls])
                        <div class="{{ $i > 0 ? 'pl-4 border-l border-white/[8%]' : '' }} {{ $i < 2 ? 'pr-4' : '' }}">
                            <div class="font-body font-medium text-[9px] uppercase tracking-widest text-white/30 mb-1.5">{{ $lbl }}</div>
                            <div class="font-display font-bold text-sm {{ $cls }} leading-none">{{ $val }}</div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Détails du bail --}}
            <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
                <div class="flex items-center justify-between px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
                    <span class="font-display font-bold text-sm text-bimo-text">Détails du bail</span>
                    <span class="font-body text-[11px] text-bimo-text/40">{{ $contrat->reference_bail ?? 'BAIL-'.$contrat->id }}</span>
                </div>
                <div class="px-5 py-5 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <div class="font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40 mb-1">Type de bail</div>
                            <div class="font-body font-medium text-sm text-bimo-text">{{ \App\Models\Contrat::TYPES_BAIL[$contrat->type_bail] ?? ucfirst($contrat->type_bail) }}</div>
                        </div>
                        <div>
                            <div class="font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40 mb-1">Statut</div>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="w-2 h-2 rounded-full bg-bimo-gold"></span>
                                <span class="font-body font-medium text-sm text-bimo-gold">Actif</span>
                            </div>
                        </div>
                        <div>
                            <div class="font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40 mb-1">Date de début</div>
                            <div class="font-body font-medium text-sm text-bimo-text">{{ $debut->format('d/m/Y') }}</div>
                            <div class="font-body text-xs text-bimo-text/40">{{ $debut->diffForHumans() }}</div>
                        </div>
                        <div>
                            <div class="font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40 mb-1">Date de fin</div>
                            @if($fin)
                            <div class="font-body font-medium text-sm text-bimo-text">{{ $fin->format('d/m/Y') }}</div>
                            @if($joursRestants !== null && $joursRestants > 0)
                            <div class="font-body text-xs text-bimo-text/40">{{ $joursRestants }} jours restants</div>
                            @elseif($joursRestants !== null && $joursRestants <= 0)
                            <div class="font-body text-xs text-bimo-red">Bail échu</div>
                            @endif
                            @else
                            <div class="font-body text-sm text-bimo-text">Durée indéterminée</div>
                            @endif
                        </div>
                        @if($contrat->charges_mensuelles)
                        <div>
                            <div class="font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40 mb-1">Charges mensuelles</div>
                            <div class="font-body font-medium text-sm text-bimo-text">{{ number_format($contrat->charges_mensuelles,0,',','') }} F</div>
                        </div>
                        @endif
                        @if($contrat->tom_amount)
                        <div>
                            <div class="font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40 mb-1">TOM</div>
                            <div class="font-body font-medium text-sm text-bimo-text">{{ number_format($contrat->tom_amount,0,',','') }} F</div>
                        </div>
                        @endif
                    </div>

                    @if($fin && $dureeProgress !== null)
                    <div class="pt-4 border-t border-bimo-navy/[5%]">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-body text-xs text-bimo-text/50">Progression du bail</span>
                            <span class="font-body font-semibold text-xs text-bimo-gold">{{ $dureeProgress }}%</span>
                        </div>
                        <div class="h-1.5 bg-bimo-navy/10 rounded-full overflow-hidden">
                            <div class="h-full bg-bimo-gold rounded-full" style="width:{{ $dureeProgress }}%"></div>
                        </div>
                        <div class="flex items-center justify-between mt-1.5">
                            <span class="font-body text-[10px] text-bimo-text/30">{{ $debut->format('d/m/Y') }}</span>
                            <span class="font-body text-[10px] text-bimo-text/30">{{ $fin->format('d/m/Y') }}</span>
                        </div>
                    </div>
                    @endif

                    @if($contrat->garant_nom)
                    <div class="pt-4 border-t border-bimo-navy/[5%]">
                        <div class="px-4 py-3 bg-bimo-bg border-l-4 border-bimo-navy/20 rounded-r-[9px]">
                            <div class="font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40 mb-1">Garant</div>
                            <div class="font-body font-medium text-sm text-bimo-text">{{ $contrat->garant_nom }}</div>
                            @if($contrat->garant_telephone)
                            <div class="font-body text-xs text-bimo-text/50 mt-0.5">{{ $contrat->garant_telephone }}</div>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Historique paiements --}}
            <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
                <div class="flex items-center justify-between px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
                    <span class="font-display font-bold text-sm text-bimo-text">Mes derniers paiements</span>
                    <a href="{{ route('locataire.paiements') }}" class="font-body text-xs text-bimo-text/40 hover:text-bimo-gold transition-colors duration-150">Voir tout →</a>
                </div>
                @if($paiements->isEmpty())
                <div class="px-5 py-10 text-center font-body text-sm text-bimo-text/30">Aucun paiement enregistré pour l'instant.</div>
                @else
                {{-- Mobile --}}
                <div class="md:hidden divide-y divide-bimo-navy/[5%]">
                    @foreach($paiements->take(6) as $p)
                    <div class="px-5 py-3.5 flex items-center justify-between gap-3">
                        <div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-display font-semibold bg-bimo-gold/10 border border-bimo-gold/20 text-bimo-gold">
                                {{ \Carbon\Carbon::parse($p->periode)->translatedFormat('M Y') }}
                            </span>
                            <div class="font-body text-xs text-bimo-text/40 mt-1">{{ \Carbon\Carbon::parse($p->date_paiement)->format('d/m/Y') }}</div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="font-display font-bold text-sm text-bimo-gold">{{ number_format($p->montant_encaisse,0,',','') }} F</span>
                            <a href="{{ route('locataire.paiements.pdf', $p) }}" target="_blank"
                               class="w-7 h-7 flex items-center justify-center border border-bimo-navy/10 rounded-[6px] text-bimo-text/40 hover:text-bimo-gold hover:border-bimo-gold/30 transition-all duration-150">
                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
                {{-- Desktop --}}
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-bimo-navy/[5%] bg-bimo-bg">
                                <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Référence</th>
                                <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Période</th>
                                <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Date</th>
                                <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Mode</th>
                                <th class="px-5 py-3 text-right font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Montant</th>
                                <th class="px-5 py-3 text-center font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Quittance</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-bimo-navy/[5%]">
                            @foreach($paiements->take(6) as $p)
                            <tr class="hover:bg-bimo-bg transition-colors duration-100">
                                <td class="px-5 py-3.5 font-body text-[11px] text-bimo-text/40 uppercase tracking-widest">{{ $p->reference_paiement }}</td>
                                <td class="px-5 py-3.5">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-display font-semibold bg-bimo-gold/10 border border-bimo-gold/20 text-bimo-gold">
                                        {{ \Carbon\Carbon::parse($p->periode)->translatedFormat('M Y') }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 font-body text-xs text-bimo-text/60">{{ \Carbon\Carbon::parse($p->date_paiement)->format('d/m/Y') }}</td>
                                <td class="px-5 py-3.5 font-body text-xs text-bimo-text/60">{{ $modes[$p->mode_paiement] ?? $p->mode_paiement }}</td>
                                <td class="px-5 py-3.5 text-right font-display font-bold text-sm text-bimo-gold">{{ number_format($p->montant_encaisse,0,',','') }} F</td>
                                <td class="px-5 py-3.5 text-center">
                                    <a href="{{ route('locataire.paiements.pdf', $p) }}" target="_blank"
                                       class="w-7 h-7 inline-flex items-center justify-center border border-bimo-navy/10 rounded-[6px] text-bimo-text/40 hover:text-bimo-gold hover:border-bimo-gold/30 transition-all duration-150">
                                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
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

        {{-- Colonne droite --}}
        <div class="lg:sticky lg:top-6 space-y-3">

            {{-- KPIs --}}
            <div class="grid grid-cols-2 gap-3">
                <div class="bg-bimo-gold/[8%] rounded-[14px] border border-bimo-gold/25 p-4">
                    <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-gold/70 mb-1">Total payé</div>
                    <div class="font-display font-extrabold text-xl text-bimo-gold leading-none">{{ number_format($stats['total_paye'],0,',','') }}</div>
                    <div class="font-body text-[10.5px] text-bimo-gold/60 mt-1.5">F CFA versés</div>
                </div>
                <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-4">
                    <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-text/50 mb-1">Paiements</div>
                    <div class="font-display font-extrabold text-xl text-bimo-text leading-none">{{ $stats['nb_paiements'] }}</div>
                    <div class="font-body text-[10.5px] text-bimo-text/40 mt-1.5">Quittances dispo</div>
                </div>
            </div>

            @if($fin && $joursRestants !== null && $joursRestants > 0)
            <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-4">
                <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-text/50 mb-1">Jours restants</div>
                <div class="font-display font-extrabold text-xl text-bimo-text leading-none">{{ $joursRestants }}</div>
                <div class="font-body text-[10.5px] text-bimo-text/40 mt-1.5">Fin le {{ $fin->format('d/m/Y') }}</div>
            </div>
            @endif

            {{-- Propriétaire --}}
            @if($proprietaire)
            <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
                <div class="px-4 py-3.5 border-b border-bimo-navy/[5%] bg-bimo-bg2">
                    <span class="font-display font-bold text-sm text-bimo-text">Propriétaire</span>
                </div>
                <div class="px-4 py-4 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-[10px] bg-bimo-gold/15 flex items-center justify-center font-display font-bold text-sm text-bimo-gold flex-shrink-0">
                        {{ mb_strtoupper(mb_substr($proprietaire->name,0,2)) }}
                    </div>
                    <div>
                        <div class="font-body font-semibold text-sm text-bimo-text">{{ $proprietaire->name }}</div>
                        @if($proprietaire->telephone)
                        <a href="tel:{{ $proprietaire->telephone }}" class="font-body text-xs text-bimo-text/50 hover:text-bimo-text transition-colors duration-150">{{ $proprietaire->telephone }}</a>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            {{-- Agence --}}
            @if($agency)
            <div class="bg-bimo-navy rounded-[14px] overflow-hidden">
                <div class="px-4 py-3.5 border-b border-white/[7%]">
                    <span class="font-display font-bold text-sm text-white">Votre agence</span>
                </div>
                <div class="px-4 py-4 space-y-2.5">
                    <div class="font-display font-bold text-sm text-white mb-3">{{ $agency->name }}</div>
                    @if($agency->telephone)
                    <div class="flex items-center gap-2.5">
                        <svg class="w-3.5 h-3.5 text-white/30 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07"/></svg>
                        <a href="tel:{{ $agency->telephone }}" class="font-body text-xs text-white/60 hover:text-white transition-colors duration-150">{{ $agency->telephone }}</a>
                    </div>
                    @endif
                    @if($agency->email)
                    <div class="flex items-center gap-2.5">
                        <svg class="w-3.5 h-3.5 text-white/30 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4"/><polyline points="22,6 12,13 2,6"/></svg>
                        <a href="mailto:{{ $agency->email }}" class="font-body text-xs text-bimo-gold hover:text-white transition-colors duration-150">{{ $agency->email }}</a>
                    </div>
                    @endif
                    @if($agency->adresse)
                    <div class="flex items-center gap-2.5">
                        <svg class="w-3.5 h-3.5 text-white/30 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        <span class="font-body text-xs text-white/50">{{ $agency->adresse }}</span>
                    </div>
                    @endif
                    @if($agency->telephone)
                    @php
                        $tel = preg_replace('/\s+|-/', '', $agency->telephone);
                        if (!str_starts_with($tel,'+') && !str_starts_with($tel,'221')) $tel = '221'.ltrim($tel,'0');
                        $tel = ltrim($tel,'+');
                        $locataireName = $contrat?->locataire?->name ?? auth()->user()->name;
                        $msgWa = "Bonjour {$agency->name}, je suis {$locataireName}, locataire du bien {$bien->reference}. Je souhaite vous contacter.";
                    @endphp
                    <a href="https://wa.me/{{ $tel }}?text={{ urlencode($msgWa) }}" target="_blank"
                       class="flex items-center justify-center gap-2 mt-2 px-4 py-2.5 bg-[#25D366] text-white rounded-[9px] font-body font-semibold text-sm hover:opacity-90 transition-opacity duration-150">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.124.558 4.118 1.532 5.847L.057 23.492a.5.5 0 00.614.65l5.82-1.527A11.945 11.945 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22a9.944 9.944 0 01-5.091-1.396l-.361-.216-3.754.984.999-3.648-.237-.374A9.944 9.944 0 012 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/></svg>
                        Contacter par WhatsApp
                    </a>
                    @endif
                </div>
            </div>
            @endif

        </div>
    </div>
    @endif

</div>
@endsection
