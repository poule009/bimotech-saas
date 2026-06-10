@extends('layouts.app')
@section('header', 'Trésorerie')

@section('content')
<div class="space-y-4 md:space-y-6">

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="font-display font-extrabold text-xl md:text-2xl text-bimo-text tracking-tight leading-tight">Trésorerie</h1>
            <p class="font-body text-sm text-bimo-text/50 mt-1">{{ $tresorerie['mois'] }}</p>
        </div>
        <form method="GET" class="flex items-center gap-2">
            <select name="annee" onchange="this.form.submit()" class="px-3 py-2 rounded-[8px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150 appearance-none cursor-pointer">
                @foreach(range(now()->year, now()->year - 2) as $a)
                <option value="{{ $a }}" {{ $a == $annee ? 'selected' : '' }}>{{ $a }}</option>
                @endforeach
            </select>
            <select name="mois" onchange="this.form.submit()" class="px-3 py-2 rounded-[8px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150 appearance-none cursor-pointer">
                @foreach(range(1,12) as $m)
                <option value="{{ $m }}" {{ $m == $mois ? 'selected' : '' }}>{{ \Carbon\Carbon::create($annee, $m)->locale('fr')->translatedFormat('F') }}</option>
                @endforeach
            </select>
        </form>
    </div>

    {{-- Flux du mois --}}
    <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
        <div class="px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
            <span class="font-display font-bold text-sm text-bimo-text">Flux de trésorerie — {{ $tresorerie['mois'] }}</span>
        </div>
        <div class="divide-y divide-bimo-navy/[5%]">
            <div class="px-5 py-4 flex items-center justify-between">
                <div>
                    <p class="font-body font-medium text-sm text-bimo-text">Encaissé des locataires</p>
                    <p class="font-body text-xs text-bimo-text/40 mt-0.5">Loyers + charges reçus</p>
                </div>
                <p class="font-display font-bold text-base text-bimo-gold">+{{ number_format($tresorerie['encaisse_locataires'], 0, ',', ' ') }} FCFA</p>
            </div>
            <div class="px-5 py-4 flex items-center justify-between">
                <div>
                    <p class="font-body font-medium text-sm text-bimo-text">Reversé aux propriétaires</p>
                    <p class="font-body text-xs text-bimo-text/40 mt-0.5">Nets reversés ce mois</p>
                </div>
                <p class="font-display font-bold text-base text-bimo-text/70">-{{ number_format($tresorerie['reverse_proprietaires'], 0, ',', ' ') }} FCFA</p>
            </div>
            <div class="px-5 py-4 flex items-center justify-between">
                <div>
                    <p class="font-body font-medium text-sm text-bimo-text">Charges agence payées</p>
                    <p class="font-body text-xs text-bimo-text/40 mt-0.5">Salaires, loyer, etc.</p>
                </div>
                <p class="font-display font-bold text-base text-bimo-text/70">-{{ number_format($tresorerie['charges_agence'], 0, ',', ' ') }} FCFA</p>
            </div>
            <div class="px-5 py-4 flex items-center justify-between bg-bimo-gold/[5%]">
                <div>
                    <p class="font-display font-bold text-sm text-bimo-text">Disponible agence</p>
                    <p class="font-body text-xs text-bimo-text/40 mt-0.5">Après reversements et charges</p>
                </div>
                <p class="font-display font-extrabold text-xl {{ $tresorerie['disponible_agence'] >= 0 ? 'text-bimo-gold' : 'text-bimo-red' }}">
                    {{ ($tresorerie['disponible_agence'] >= 0 ? '+' : '') . number_format($tresorerie['disponible_agence'], 0, ',', ' ') }} F
                </p>
            </div>
        </div>
    </div>

    {{-- Solde mandant total --}}
    <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40 mb-1">Solde total dû aux propriétaires</p>
                <p class="font-body text-xs text-bimo-text/30">Cumul de tous les nets non encore reversés</p>
            </div>
            <p class="font-display font-extrabold text-2xl text-bimo-text">{{ number_format($tresorerie['solde_mandant_total'], 0, ',', ' ') }} FCFA</p>
        </div>
    </div>

</div>
@endsection
