@extends('layouts.app')
@section('header', 'Compte de résultat')

@section('content')
<div class="space-y-4 md:space-y-6">

    {{-- En-tête + filtres --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="font-display font-extrabold text-xl md:text-2xl text-bimo-text tracking-tight leading-tight">Compte de résultat</h1>
            <p class="font-body text-sm text-bimo-text/50 mt-1">{{ $mois ? \Carbon\Carbon::create($annee, $mois)->locale('fr')->translatedFormat('F Y') : "Année $annee" }}</p>
        </div>
        <form method="GET" class="flex items-center gap-2 flex-wrap">
            <select name="annee" onchange="this.form.submit()" class="px-3 py-2 rounded-[8px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150 appearance-none cursor-pointer">
                @foreach($annees as $a)
                <option value="{{ $a }}" {{ $a == $annee ? 'selected' : '' }}>{{ $a }}</option>
                @endforeach
            </select>
            <select name="mois" onchange="this.form.submit()" class="px-3 py-2 rounded-[8px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150 appearance-none cursor-pointer">
                <option value="">Toute l'année</option>
                @foreach(range(1,12) as $m)
                <option value="{{ $m }}" {{ $m == $mois ? 'selected' : '' }}>{{ \Carbon\Carbon::create($annee, $m)->locale('fr')->translatedFormat('F') }}</option>
                @endforeach
            </select>
        </form>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        {{-- Résultat principal --}}
        <div class="lg:col-span-2 space-y-4">

            {{-- Revenus --}}
            <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
                <div class="px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
                    <span class="font-display font-bold text-sm text-bimo-text">Revenus</span>
                </div>
                <div class="divide-y divide-bimo-navy/[5%]">
                    <div class="px-5 py-3.5 flex items-center justify-between">
                        <p class="font-body text-sm text-bimo-text/70">Commissions de gestion (HT)</p>
                        <p class="font-display font-bold text-sm text-bimo-gold">+{{ number_format($resultat['commissions_ht'], 0, ',', ' ') }} F</p>
                    </div>
                    @if($resultat['frais_entree_ht'] > 0)
                    <div class="px-5 py-3.5 flex items-center justify-between">
                        <p class="font-body text-sm text-bimo-text/70">Frais d'entrée dossiers (HT)</p>
                        <p class="font-display font-bold text-sm text-bimo-gold">+{{ number_format($resultat['frais_entree_ht'], 0, ',', ' ') }} F</p>
                    </div>
                    @endif
                    <div class="px-5 py-3.5 flex items-center justify-between bg-bimo-gold/[5%]">
                        <p class="font-display font-bold text-sm text-bimo-text">Total revenus HT</p>
                        <p class="font-display font-extrabold text-base text-bimo-gold">+{{ number_format($resultat['revenus_total_ht'], 0, ',', ' ') }} F</p>
                    </div>
                </div>
            </div>

            {{-- Charges --}}
            <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
                <div class="px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2 flex items-center justify-between">
                    <span class="font-display font-bold text-sm text-bimo-text">Charges</span>
                    <a href="{{ route('admin.charges-agence.create') }}" class="inline-flex items-center gap-1 font-body text-xs text-bimo-text/50 hover:text-bimo-text transition-colors duration-150">
                        <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        Ajouter
                    </a>
                </div>
                @if(empty($resultat['charges_par_categorie']) && $resultat['tva_nette_due'] == 0)
                <div class="px-5 py-10 text-center font-body text-sm text-bimo-text/30">Aucune charge saisie pour cette période.</div>
                @else
                <div class="divide-y divide-bimo-navy/[5%]">
                    @foreach($resultat['charges_par_categorie'] as $cat => $montant)
                    <div class="px-5 py-3.5 flex items-center justify-between">
                        <p class="font-body text-sm text-bimo-text/70">{{ \App\Models\ChargeAgence::CATEGORIES[$cat] ?? ucfirst($cat) }}</p>
                        <p class="font-display font-bold text-sm text-bimo-text/70">-{{ number_format($montant, 0, ',', ' ') }} F</p>
                    </div>
                    @endforeach
                    @if($resultat['tva_nette_due'] > 0)
                    <div class="px-5 py-3.5 flex items-center justify-between">
                        <div>
                            <p class="font-body text-sm text-bimo-text/70">TVA nette due (DGI)</p>
                            <p class="font-body text-[10px] text-bimo-text/30 mt-0.5">Depuis module Fiscalité — TVA collectée − TVA déductible</p>
                        </div>
                        <p class="font-display font-bold text-sm text-bimo-text/70">-{{ number_format($resultat['tva_nette_due'], 0, ',', ' ') }} F</p>
                    </div>
                    @endif
                    <div class="px-5 py-3.5 flex items-center justify-between bg-bimo-navy/[3%]">
                        <p class="font-display font-bold text-sm text-bimo-text">Total charges</p>
                        <p class="font-display font-extrabold text-base text-bimo-text">-{{ number_format($resultat['charges_total'], 0, ',', ' ') }} F</p>
                    </div>
                </div>
                @endif
            </div>

            {{-- Résultat net --}}
            <div class="rounded-[14px] border-2 p-5 {{ $resultat['resultat_net'] >= 0 ? 'bg-bimo-gold/[5%] border-bimo-gold/30' : 'bg-bimo-red/[5%] border-bimo-red/30' }}">
                <div class="flex items-center justify-between">
                    <p class="font-display font-bold text-base {{ $resultat['resultat_net'] >= 0 ? 'text-bimo-text' : 'text-bimo-red' }}">Résultat net</p>
                    <p class="font-display font-extrabold text-2xl {{ $resultat['resultat_net'] >= 0 ? 'text-bimo-gold' : 'text-bimo-red' }}">
                        {{ ($resultat['resultat_net'] >= 0 ? '+' : '') . number_format($resultat['resultat_net'], 0, ',', ' ') }} F
                    </p>
                </div>
            </div>

            {{-- Détail mensuel si vue annuelle --}}
            @if(!$mois && !empty($resultat['par_mois']))
            <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
                <div class="px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
                    <span class="font-display font-bold text-sm text-bimo-text">Détail mensuel</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-bimo-navy/[5%] bg-bimo-bg">
                                <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Mois</th>
                                <th class="px-5 py-3 text-right font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Revenus</th>
                                <th class="px-5 py-3 text-right font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Charges</th>
                                <th class="px-5 py-3 text-right font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Résultat</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-bimo-navy/[5%]">
                            @foreach($resultat['par_mois'] as $periode => $data)
                            @if($data['revenus'] > 0 || $data['charges'] > 0)
                            <tr class="hover:bg-bimo-bg transition-colors duration-100">
                                <td class="px-5 py-3 font-body text-sm text-bimo-text">{{ \Carbon\Carbon::createFromFormat('Y-m', $periode)->locale('fr')->translatedFormat('F Y') }}</td>
                                <td class="px-5 py-3 text-right font-display font-semibold text-sm text-bimo-gold">{{ number_format($data['revenus'], 0, ',', ' ') }} F</td>
                                <td class="px-5 py-3 text-right font-display font-semibold text-sm text-bimo-text/60">{{ number_format($data['charges'], 0, ',', ' ') }} F</td>
                                <td class="px-5 py-3 text-right font-display font-bold text-sm {{ $data['resultat'] >= 0 ? 'text-bimo-gold' : 'text-bimo-red' }}">
                                    {{ ($data['resultat'] >= 0 ? '+' : '') . number_format($data['resultat'], 0, ',', ' ') }} F
                                </td>
                            </tr>
                            @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>

        {{-- Sidebar résumé --}}
        <div class="space-y-3">
            <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-5">
                <p class="font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40 mb-4">Résumé</p>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <p class="font-body text-sm text-bimo-text/60">Revenus HT</p>
                        <p class="font-display font-bold text-sm text-bimo-gold">{{ number_format($resultat['revenus_total_ht'], 0, ',', ' ') }} F</p>
                    </div>
                    <div class="flex justify-between items-center">
                        <p class="font-body text-sm text-bimo-text/60">Charges</p>
                        <p class="font-display font-bold text-sm text-bimo-text">{{ number_format($resultat['charges_total'], 0, ',', ' ') }} F</p>
                    </div>
                    <div class="border-t border-bimo-navy/10 pt-3 flex justify-between items-center">
                        <p class="font-display font-bold text-sm text-bimo-text">Résultat net</p>
                        <p class="font-display font-extrabold text-base {{ $resultat['resultat_net'] >= 0 ? 'text-bimo-gold' : 'text-bimo-red' }}">
                            {{ number_format($resultat['resultat_net'], 0, ',', ' ') }} F
                        </p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-5">
                <p class="font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40 mb-3">TVA collectée</p>
                <p class="font-display font-bold text-lg text-bimo-text">{{ number_format($resultat['tva_commissions'] + $resultat['tva_frais_entree'], 0, ',', ' ') }} F</p>
                <p class="font-body text-xs text-bimo-text/40 mt-1">À déclarer à la DGI</p>
            </div>
            <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-5">
                <p class="font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40 mb-2">Paiements traités</p>
                <p class="font-display font-extrabold text-2xl text-bimo-text">{{ $resultat['nb_paiements'] }}</p>
            </div>
        </div>
    </div>

</div>
@endsection
