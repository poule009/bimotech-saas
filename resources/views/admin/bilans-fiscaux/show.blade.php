@extends('layouts.app')

@php
    $fmt = fn ($n) => number_format((float) $n, 0, ',', ' ') . ' F';
@endphp

@section('title', 'Bilan fiscal — ' . $proprietaire->name)
@section('page-title', 'Bilan fiscal ' . $annee)
@section('page-subtitle')
    <a href="{{ route('admin.bilans-fiscaux.index', ['annee' => $annee]) }}" class="text-teal font-semibold hover:underline">Bilans fiscaux</a>
    <span class="text-muted"> / {{ $proprietaire->name }}</span>
@endsection

@section('content')
<div class="max-w-[1000px] space-y-5">

    {{-- Identité --}}
    <div class="f-card">
        <h3 class="f-card-title">{{ $proprietaire->name }}</h3>
        <p class="f-card-sub">{{ $proprietaire->email ?? $proprietaire->telephone ?? '' }} — Exercice {{ $annee }}</p>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-2 text-[13px]">
            <div><div class="text-[11.5px] uppercase tracking-wide text-muted font-bold mb-1">Biens gérés</div><div class="font-semibold">{{ (int) ($bilan->nb_biens_geres ?? 0) }}</div></div>
            <div><div class="text-[11.5px] uppercase tracking-wide text-muted font-bold mb-1">Paiements</div><div class="font-semibold">{{ (int) ($bilan->nb_paiements ?? 0) }}</div></div>
        </div>
    </div>

    {{-- Revenus & impôts --}}
    <div class="f-card">
        <h3 class="f-card-title">Revenus & imposition</h3>
        <table class="w-full text-[14px]">
            <tbody>
                <tr class="border-b border-paper-dim"><td class="py-2 text-muted">Revenus bruts (loyers + charges)</td><td class="py-2 text-right font-semibold">{{ $fmt($bilan->revenus_bruts_total ?? 0) }}</td></tr>
                <tr class="border-b border-paper-dim"><td class="py-2 text-muted">Abattement forfaitaire 30 %</td><td class="py-2 text-right">− {{ $fmt($bilan->abattement_forfaitaire_30 ?? 0) }}</td></tr>
                <tr class="border-b border-paper-dim"><td class="py-2 text-muted">Base imposable</td><td class="py-2 text-right font-semibold">{{ $fmt($bilan->base_imposable ?? 0) }}</td></tr>
                <tr class="border-b border-paper-dim"><td class="py-2 text-muted">IRPP estimé</td><td class="py-2 text-right font-semibold text-teal">{{ $fmt($bilan->irpp_estime ?? 0) }}</td></tr>
                <tr class="border-b border-paper-dim"><td class="py-2 text-muted">CFPB estimée <span class="text-[11px]">(indicative)</span></td><td class="py-2 text-right">{{ $fmt($bilan->cfpb_estimee ?? 0) }}</td></tr>
            </tbody>
        </table>
        @if(isset($regimes['message']))
            <div class="mt-3 rounded-lg bg-gold/15 text-teal-deep px-4 py-3 text-[12.5px]">{{ $regimes['message'] }}</div>
        @endif
    </div>

    {{-- Taxes collectées --}}
    <div class="f-card">
        <h3 class="f-card-title">Taxes & retenues</h3>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-[13px]">
            <div><div class="text-[11.5px] uppercase tracking-wide text-muted font-bold mb-1">TVA loyer collectée</div><div class="font-semibold">{{ $fmt($bilan->tva_loyer_collectee ?? 0) }}</div></div>
            <div><div class="text-[11.5px] uppercase tracking-wide text-muted font-bold mb-1">BRS retenu</div><div class="font-semibold">{{ $fmt($bilan->brs_retenu_total ?? 0) }}</div></div>
            <div><div class="text-[11.5px] uppercase tracking-wide text-muted font-bold mb-1">Commissions agence HT</div><div class="font-semibold">{{ $fmt($bilan->commissions_agence_ht ?? 0) }}</div></div>
            <div><div class="text-[11.5px] uppercase tracking-wide text-muted font-bold mb-1">Net versé au propriétaire</div><div class="font-semibold text-gold">{{ $fmt($bilan->net_a_verser_total ?? 0) }}</div></div>
        </div>
    </div>

    <p class="text-[11.5px] text-muted">Montants indicatifs calculés depuis les paiements validés. À confirmer avec un expert-comptable avant déclaration officielle.</p>
</div>
@endsection
