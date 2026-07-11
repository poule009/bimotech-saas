@extends('layouts.app')

@php
    $fmt = fn ($n) => number_format((float) $n, 0, ',', ' ') . ' F';
@endphp

@section('title', 'Mon espace')
@section('page-title', 'Mon espace locataire')
@section('page-subtitle')
    <span class="text-muted">Votre logement et vos paiements</span>
@endsection

@section('content')
<div class="max-w-[900px] space-y-5">

    @if($contrat)
        {{-- Logement --}}
        <div class="f-card">
            <h3 class="f-card-title">Mon logement</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-[14px]">
                <div><div class="text-[11.5px] uppercase tracking-wide text-muted font-bold mb-1">Bien</div><div class="font-semibold">{{ $contrat->bien->reference ?? '—' }}</div></div>
                <div><div class="text-[11.5px] uppercase tracking-wide text-muted font-bold mb-1">Adresse</div><div class="font-semibold">{{ trim(($contrat->bien->adresse ?? '') . ' ' . ($contrat->bien->ville ?? '')) ?: '—' }}</div></div>
                <div><div class="text-[11.5px] uppercase tracking-wide text-muted font-bold mb-1">Loyer mensuel</div><div class="font-semibold">{{ $fmt($contrat->loyer_contractuel ?? $contrat->loyer_nu ?? 0) }}</div></div>
                <div><div class="text-[11.5px] uppercase tracking-wide text-muted font-bold mb-1">Prochaine échéance</div><div class="font-semibold">{{ $prochainePeriode ? $prochainePeriode->locale('fr')->translatedFormat('F Y') : '—' }}</div></div>
            </div>
        </div>

        {{-- Récap paiements --}}
        <div class="f-card">
            <h3 class="f-card-title">Mes paiements</h3>
            <div class="grid grid-cols-2 gap-4 text-[14px]">
                <div><div class="text-[11.5px] uppercase tracking-wide text-muted font-bold mb-1">Total payé</div><div class="font-semibold text-teal">{{ $fmt($stats['total_paye'] ?? 0) }}</div></div>
                <div><div class="text-[11.5px] uppercase tracking-wide text-muted font-bold mb-1">Quittances</div><div class="font-semibold">{{ (int) ($stats['nb_paiements'] ?? 0) }}</div></div>
            </div>

            @if($paiements && $paiements->count())
                <table class="w-full text-[13.5px] mt-4">
                    <thead><tr class="text-left text-[12px] uppercase tracking-wide text-muted border-b border-line">
                        <th class="py-2">Période</th><th class="py-2 text-right">Montant</th><th class="py-2 text-right">Statut</th>
                    </tr></thead>
                    <tbody>
                        @foreach($paiements as $p)
                            <tr class="border-b border-paper-dim">
                                <td class="py-2">{{ \Carbon\Carbon::parse($p->periode)->locale('fr')->translatedFormat('F Y') }}</td>
                                <td class="py-2 text-right font-semibold">{{ $fmt($p->montant_encaisse ?? 0) }}</td>
                                <td class="py-2 text-right">{{ $p->statut_label ?? $p->statut }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    @else
        <div class="f-card">
            <h3 class="f-card-title">Aucun contrat actif</h3>
            <p class="f-card-sub">Aucun bail actif n'est associé à votre compte pour le moment. Contactez votre agence si vous pensez qu'il s'agit d'une erreur.</p>
        </div>
    @endif
</div>
@endsection
