@extends('layouts.app')
@section('header', 'Mon contrat')

@section('content')

@php
$modes = ['especes'=>'Espèces','virement'=>'Virement','cheque'=>'Chèque','wave'=>'Wave','orange_money'=>'Orange Money','free_money'=>'Free Money'];
@endphp

<div class="space-y-4">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 font-body text-sm text-bimo-text/40">
        <a href="{{ route('locataire.dashboard') }}" class="hover:text-bimo-text transition-colors duration-150">Mon espace</a>
        <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
        <span class="text-bimo-text font-medium">Mon contrat</span>
    </nav>

    {{-- Hero --}}
    <div class="bg-bimo-navy rounded-[14px] p-5 md:p-7 flex flex-col md:flex-row md:items-center md:justify-between gap-5 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-48 h-48 rounded-full opacity-[0.05]"
             style="background:radial-gradient(circle,#C9A84C 0%,transparent 70%);transform:translate(30%,-30%)"></div>
        <div class="relative z-10">
            <div class="font-body font-medium text-[10px] uppercase tracking-widest text-bimo-gold/60 mb-2">
                {{ $contrat->reference_bail ?? 'BAIL-'.$contrat->id }}
            </div>
            <div class="font-display font-extrabold text-xl text-white leading-tight mb-1">
                {{ \App\Models\Contrat::TYPES_BAIL[$contrat->type_bail] ?? $contrat->type_bail }}
                — {{ $contrat->bien?->adresse }}
            </div>
            <div class="font-body text-sm text-white/50 mb-3">
                {{ $contrat->bien?->ville }}
                · Réf. bien : {{ $contrat->bien?->reference }}
            </div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-bimo-gold/15 border border-bimo-gold/30 rounded-full font-body font-semibold text-xs text-bimo-gold">
                <span class="w-1.5 h-1.5 rounded-full bg-bimo-gold"></span>
                Contrat actif
            </span>
        </div>
        <div class="relative z-10 md:text-right flex-shrink-0">
            <div class="font-body font-medium text-[10px] uppercase tracking-widest text-bimo-gold/60 mb-1">Loyer mensuel</div>
            <div class="font-display font-extrabold text-3xl text-bimo-gold leading-none">
                {{ number_format($contrat->loyer_contractuel, 0, ',', ' ') }}<span class="text-sm text-white/30 ml-1">F</span>
            </div>
            <div class="font-body text-xs text-white/30 mt-1">
                {{ $contrat->date_debut?->format('d/m/Y') }} → {{ $contrat->date_fin?->format('d/m/Y') ?? 'Durée indéterminée' }}
            </div>
        </div>
    </div>

    {{-- KPIs --}}
    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
        <div class="bg-bimo-gold/[8%] rounded-[14px] border border-bimo-gold/25 p-4">
            <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-gold/70 mb-1">Total versé</div>
            <div class="font-display font-extrabold text-xl text-bimo-gold leading-none">{{ number_format($totalPaye, 0, ',', ' ') }}<span class="text-xs text-bimo-gold/60 ml-1">F</span></div>
        </div>
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-4">
            <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-text/50 mb-1">Paiements</div>
            <div class="font-display font-extrabold text-xl text-bimo-text leading-none">{{ $nbPaiements }}</div>
        </div>
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-4 col-span-2 md:col-span-1">
            <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-text/50 mb-1">Prochain loyer</div>
            <div class="font-display font-extrabold text-lg text-bimo-text leading-tight">{{ $prochainePeriode->translatedFormat('F Y') }}</div>
        </div>
    </div>

    {{-- Détails du bail --}}
    <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
        <div class="px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
            <span class="font-display font-bold text-sm text-bimo-text">Détails du bail</span>
        </div>
        <div class="px-5 py-5 grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <div class="font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40 mb-1">Type de bail</div>
                <div class="font-body font-medium text-sm text-bimo-text">{{ \App\Models\Contrat::TYPES_BAIL[$contrat->type_bail] ?? $contrat->type_bail }}</div>
            </div>
            <div>
                <div class="font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40 mb-1">Référence bail</div>
                <div class="font-body text-sm text-bimo-text/70" style="font-family:monospace">{{ $contrat->reference_bail ?? 'BAIL-'.$contrat->id }}</div>
            </div>
            <div>
                <div class="font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40 mb-1">Date de début</div>
                <div class="font-body font-medium text-sm text-bimo-text">{{ $contrat->date_debut?->format('d/m/Y') }}</div>
            </div>
            <div>
                <div class="font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40 mb-1">Date de fin</div>
                <div class="font-body font-medium text-sm text-bimo-text">{{ $contrat->date_fin?->format('d/m/Y') ?? 'Durée indéterminée' }}</div>
            </div>
            <div>
                <div class="font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40 mb-1">Loyer nu</div>
                <div class="font-body font-medium text-sm text-bimo-text">{{ number_format($contrat->loyer_nu, 0, ',', ' ') }} F</div>
            </div>
            @if($contrat->charges_mensuelles)
            <div>
                <div class="font-body font-medium text-[10px] uppercase tracking-widests text-bimo-text/40 mb-1">Charges mensuelles</div>
                <div class="font-body font-medium text-sm text-bimo-text">{{ number_format($contrat->charges_mensuelles, 0, ',', ' ') }} F</div>
            </div>
            @endif
            @if($contrat->tom_amount)
            <div>
                <div class="font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40 mb-1">TOM</div>
                <div class="font-body font-medium text-sm text-bimo-text">{{ number_format($contrat->tom_amount, 0, ',', ' ') }} F</div>
            </div>
            @endif
            <div>
                <div class="font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40 mb-1">Caution versée</div>
                <div class="font-body font-medium text-sm text-bimo-text">{{ number_format($contrat->caution, 0, ',', ' ') }} F</div>
            </div>
        </div>
    </div>

    {{-- Bien loué --}}
    <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
        <div class="px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
            <span class="font-display font-bold text-sm text-bimo-text">Bien loué</span>
        </div>
        <div class="px-5 py-5 grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <div class="font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40 mb-1">Référence</div>
                <div class="font-body font-medium text-sm text-bimo-text">{{ $contrat->bien?->reference }}</div>
            </div>
            <div>
                <div class="font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40 mb-1">Type</div>
                <div class="font-body font-medium text-sm text-bimo-text">{{ \App\Models\Bien::TYPES[$contrat->bien?->type] ?? $contrat->bien?->type }}</div>
            </div>
            <div class="sm:col-span-2">
                <div class="font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40 mb-1">Adresse</div>
                <div class="font-body font-medium text-sm text-bimo-text">{{ $contrat->bien?->adresse }}, {{ $contrat->bien?->ville }}</div>
            </div>
            @if($contrat->bien?->surface_m2)
            <div>
                <div class="font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40 mb-1">Surface</div>
                <div class="font-body font-medium text-sm text-bimo-text">{{ $contrat->bien?->surface_m2 }} m²</div>
            </div>
            @endif
            @if($contrat->bien?->nombre_pieces)
            <div>
                <div class="font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40 mb-1">Pièces</div>
                <div class="font-body font-medium text-sm text-bimo-text">{{ $contrat->bien?->nombre_pieces }}</div>
            </div>
            @endif
        </div>
    </div>

    {{-- Propriétaire --}}
    <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
        <div class="px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
            <span class="font-display font-bold text-sm text-bimo-text">Votre gestionnaire</span>
        </div>
        <div class="px-5 py-5">
            <div>
                <div class="font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40 mb-1">Propriétaire</div>
                <div class="font-body font-medium text-sm text-bimo-text">{{ $contrat->bien?->proprietaire?->name }}</div>
                @if($contrat->bien?->proprietaire?->telephone)
                <div class="font-body text-xs text-bimo-text/50 mt-0.5">{{ $contrat->bien->proprietaire->telephone }}</div>
                @endif
            </div>
        </div>
    </div>

    {{-- Historique paiements --}}
    @if($paiements->count() > 0)
    <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
            <span class="font-display font-bold text-sm text-bimo-text">Historique des paiements</span>
            <a href="{{ route('locataire.paiements') }}" class="font-body font-semibold text-xs text-bimo-gold hover:text-bimo-text transition-colors duration-150">Voir tout →</a>
        </div>
        {{-- Mobile --}}
        <div class="md:hidden divide-y divide-bimo-navy/[5%]">
            @foreach($paiements->take(6) as $p)
            <div class="px-5 py-3.5 flex items-center justify-between gap-3">
                <div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-display font-semibold bg-bimo-gold/10 border border-bimo-gold/20 text-bimo-gold">
                        {{ \Carbon\Carbon::parse($p->periode)->translatedFormat('F Y') }}
                    </span>
                    <div class="font-body text-xs text-bimo-text/40 mt-1">{{ $p->date_paiement?->format('d/m/Y') ?? '—' }}</div>
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
                        <td class="px-5 py-3.5">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-display font-semibold bg-bimo-gold/10 border border-bimo-gold/20 text-bimo-gold">
                                {{ \Carbon\Carbon::parse($p->periode)->translatedFormat('F Y') }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 font-body text-xs text-bimo-text/60">{{ $p->date_paiement?->format('d/m/Y') ?? '—' }}</td>
                        <td class="px-5 py-3.5">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-body font-medium bg-bimo-navy/10 border border-bimo-navy/15 text-bimo-text/70">{{ $modes[$p->mode_paiement] ?? $p->mode_paiement }}</span>
                        </td>
                        <td class="px-5 py-3.5 text-right font-display font-bold text-sm text-bimo-gold">{{ number_format($p->montant_encaisse, 0, ',', ' ') }} F</td>
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
    </div>
    @endif

</div>
@endsection
