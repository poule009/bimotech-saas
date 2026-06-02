@extends('layouts.app')
@section('header', 'Contrats › ' . ($contrat->reference_bail ?? 'Détail'))

@section('content')
<div class="space-y-4 md:space-y-5">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 font-body text-sm text-bimo-navy/40">
        <a href="{{ route('admin.contrats.index') }}" class="hover:text-bimo-navy transition-colors duration-150">Contrats</a>
        <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
        <span class="text-bimo-navy font-medium">{{ $contrat->reference_bail ?? 'Contrat #'.$contrat->id }}</span>
    </div>

    {{-- Actions --}}
    <div class="flex flex-wrap gap-2">
        @if($contrat->statut === 'actif')
        <a href="{{ route('admin.paiements.create', ['contrat_id' => $contrat->id]) }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-bimo-gold/10 border border-bimo-gold/25 text-bimo-gold
                  font-display font-bold text-sm rounded-[10px] hover:bg-bimo-gold/20 transition-all duration-150">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Enregistrer un paiement
        </a>
        <a href="{{ route('admin.contrats.edit', $contrat) }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-bimo-navy text-white
                  font-display font-bold text-sm rounded-[10px] hover:bg-bimo-navy-dk transition-colors duration-150">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            Modifier
        </a>
        @endif
        @if(in_array($contrat->statut, ['actif', 'expiré']))
        <a href="{{ route('admin.contrats.create', ['from_contrat' => $contrat->id]) }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-bimo-navy/5 border border-bimo-navy/15 text-bimo-navy/70
                  font-body text-sm rounded-[10px] hover:text-bimo-navy hover:border-bimo-navy/30 transition-all duration-150">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M23 4v6h-6"/><path d="M20.49 15a9 9 0 11-2.12-9.36L23 10"/></svg>
            Renouveler
        </a>
        @endif
        <a href="{{ route('admin.biens.show', $contrat->bien) }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-bimo-navy/15 text-bimo-navy/60
                  font-body text-sm rounded-[10px] hover:text-bimo-navy hover:border-bimo-navy/30 transition-all duration-150">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
            Voir le bien
        </a>
        <a href="{{ route('admin.contrats.bail-pdf', $contrat) }}" target="_blank"
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-bimo-navy/15 text-bimo-navy/60
                  font-body text-sm rounded-[10px] hover:text-bimo-gold hover:border-bimo-gold/30 transition-all duration-150">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            Bail PDF
        </a>
        <a href="{{ route('admin.contrats.bail-formel-pdf', $contrat) }}" target="_blank"
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-bimo-gold/10 border border-bimo-gold/20 text-bimo-gold
                  font-body text-sm rounded-[10px] hover:bg-bimo-gold/20 transition-all duration-150">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
            Bail formel PDF
        </a>
        <a href="{{ route('admin.contrats.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-bimo-navy/15 text-bimo-navy/60
                  font-body text-sm rounded-[10px] hover:text-bimo-navy hover:border-bimo-navy/30 transition-all duration-150">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
            Retour
        </a>
        @if($contrat->statut === 'actif')
        <form method="POST" action="{{ route('admin.contrats.destroy', $contrat) }}"
              data-confirm="Le contrat {{ $contrat->reference_bail ?? 'BAIL-'.$contrat->id }} sera résilié et le bien repassera en Disponible."
              data-confirm-title="Résilier ce contrat ?"
              data-confirm-ok="Oui, résilier">
            @csrf @method('DELETE')
            <button type="submit"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-bimo-red/10 border border-bimo-red/20 text-bimo-red
                           font-body text-sm rounded-[10px] hover:bg-bimo-red/20 transition-all duration-150">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                Résilier
            </button>
        </form>
        @endif
    </div>

    {{-- Hero --}}
    @php
        $badgeClass = match($contrat->statut) {
            'actif'   => 'bg-bimo-gold/10 border-bimo-gold/25 text-bimo-gold',
            'resilié' => 'bg-bimo-red/10 border-bimo-red/20 text-bimo-red',
            default   => 'bg-bimo-navy/10 border-bimo-navy/15 text-bimo-navy/60',
        };
    @endphp
    <div class="bg-bimo-navy rounded-[14px] p-5 md:p-7 flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-48 h-48 rounded-full opacity-[0.06]"
             style="background: radial-gradient(circle, #C9A84C 0%, transparent 70%); transform: translate(30%, -30%)">
        </div>
        <div class="relative z-10">
            <div class="font-body font-medium text-[10px] uppercase tracking-widest text-white/30 mb-2">
                {{ $contrat->reference_bail ?? 'BAIL-'.$contrat->id }}
            </div>
            <div class="font-display font-extrabold text-xl text-white leading-tight mb-1">
                {{ \App\Models\Bien::TYPES[$contrat->bien?->type] ?? '' }}
                — {{ $contrat->bien?->adresse }}
            </div>
            <div class="font-body text-sm text-white/50 mb-3">
                {{ $contrat->bien?->quartier ? $contrat->bien->quartier.', ' : '' }}
                {{ $contrat->bien?->ville }}
                · Réf. {{ $contrat->bien?->reference }}
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full border text-xs font-body font-medium {{ $badgeClass }}">
                    <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                    {{ \App\Models\Contrat::STATUTS[$contrat->statut] ?? $contrat->statut }}
                </span>
                <span class="inline-flex items-center px-2.5 py-1 rounded-full border border-bimo-gold/25 bg-bimo-gold/10 text-xs font-body font-medium text-bimo-gold">
                    {{ \App\Models\Contrat::TYPES_BAIL[$contrat->type_bail] ?? $contrat->type_bail }}
                </span>
            </div>
        </div>
        <div class="relative z-10 text-right flex-shrink-0">
            <div class="font-body font-medium text-[9px] uppercase tracking-widest text-bimo-gold/50 mb-1">Loyer contractuel</div>
            <div class="font-display font-extrabold text-3xl text-bimo-gold leading-none">
                {{ number_format($contrat->loyer_contractuel, 0, ',', ' ') }}
                <span class="font-body font-normal text-base text-bimo-gold/40">F</span>
            </div>
            <div class="font-body text-xs text-white/30 mt-1">
                {{ $contrat->date_debut?->format('d/m/Y') }}
                → {{ $contrat->date_fin?->format('d/m/Y') ?? 'Contrat ouvert' }}
            </div>
        </div>
    </div>

    {{-- KPIs --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="bg-bimo-gold/[8%] rounded-[14px] border border-bimo-gold/25 p-4">
            <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-gold/70 mb-1">Total encaissé</div>
            <div class="font-display font-extrabold text-xl text-bimo-gold leading-none">
                {{ number_format($totalPaye, 0, ',', ' ') }}<span class="font-body font-normal text-sm text-bimo-gold/50"> F</span>
            </div>
        </div>
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-4">
            <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-navy/50 mb-1">Net propriétaire</div>
            <div class="font-display font-extrabold text-xl text-bimo-navy leading-none">
                {{ number_format($totalNet, 0, ',', ' ') }}<span class="font-body font-normal text-sm text-bimo-navy/40"> F</span>
            </div>
        </div>
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-4">
            <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-navy/50 mb-1">Paiements</div>
            <div class="font-display font-extrabold text-xl text-bimo-navy leading-none">{{ $nbPaiements }}</div>
        </div>
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-4">
            <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-navy/50 mb-1">Caution versée</div>
            <div class="font-display font-extrabold text-xl text-bimo-navy leading-none">
                {{ number_format($contrat->caution, 0, ',', ' ') }}<span class="font-body font-normal text-sm text-bimo-navy/40"> F</span>
            </div>
        </div>
    </div>

    {{-- Grid principale --}}
    <div class="grid grid-cols-1 lg:grid-cols-[1fr_300px] gap-5 items-start">

        {{-- COLONNE GAUCHE --}}
        <div class="space-y-4">

            {{-- Parties --}}
            <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
                <div class="flex items-center gap-3 px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
                    <div class="w-8 h-8 rounded-[8px] bg-bimo-gold/15 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-bimo-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
                    </div>
                    <span class="font-display font-bold text-sm text-bimo-navy">Parties au contrat</span>
                </div>
                <div class="px-5 py-5 grid grid-cols-2 gap-4">
                    <div>
                        <div class="font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40 mb-1">Locataire</div>
                        <div class="font-body font-medium text-sm text-bimo-navy">{{ $contrat->locataire?->name ?? '—' }}</div>
                        <div class="font-body text-xs text-bimo-navy/50">{{ $contrat->locataire?->email }}</div>
                        <div class="font-body text-xs text-bimo-navy/50">{{ $contrat->locataire?->telephone ?? '' }}</div>
                    </div>
                    <div>
                        <div class="font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40 mb-1">Propriétaire</div>
                        <div class="font-body font-medium text-sm text-bimo-navy">{{ $contrat->bien?->proprietaire?->name ?? '—' }}</div>
                        <div class="font-body text-xs text-bimo-navy/50">{{ $contrat->bien?->proprietaire?->email }}</div>
                        <div class="font-body text-xs text-bimo-navy/50">{{ $contrat->bien?->proprietaire?->telephone ?? '' }}</div>
                    </div>
                </div>
            </div>

            {{-- Loyer --}}
            <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
                <div class="flex items-center gap-3 px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
                    <div class="w-8 h-8 rounded-[8px] bg-bimo-gold/15 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-bimo-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
                    </div>
                    <span class="font-display font-bold text-sm text-bimo-navy">Ventilation du loyer</span>
                </div>
                <div class="px-5 py-5 grid grid-cols-2 md:grid-cols-3 gap-4">
                    @php
                        $loyerRows = [
                            ['Loyer nu',           number_format($contrat->loyer_nu, 0, ',', ' ') . ' F', ''],
                            ['Charges',            number_format($contrat->charges_mensuelles ?? 0, 0, ',', ' ') . ' F', ''],
                            ['TOM',                number_format($contrat->tom_amount ?? 0, 0, ',', ' ') . ' F', ''],
                            ['Loyer contractuel',  number_format($contrat->loyer_contractuel, 0, ',', ' ') . ' F', 'text-bimo-gold font-bold'],
                            ['Caution',            number_format($contrat->caution, 0, ',', ' ') . ' F', ''],
                            ['Frais agence',       number_format($contrat->frais_agence ?? 0, 0, ',', ' ') . ' F', ''],
                        ];
                    @endphp
                    @foreach($loyerRows as [$lbl, $val, $cls])
                    <div>
                        <div class="font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40 mb-1">{{ $lbl }}</div>
                        <div class="font-body font-medium text-sm text-bimo-navy {{ $cls }}">{{ $val }}</div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Garant --}}
            @if($contrat->garant_nom)
            <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
                <div class="flex items-center gap-3 px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
                    <div class="w-8 h-8 rounded-[8px] bg-bimo-navy/5 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-bimo-navy/40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    </div>
                    <span class="font-display font-bold text-sm text-bimo-navy">Garant</span>
                </div>
                <div class="px-5 py-5 grid grid-cols-2 gap-4">
                    <div>
                        <div class="font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40 mb-1">Nom</div>
                        <div class="font-body font-medium text-sm text-bimo-navy">{{ $contrat->garant_nom }}</div>
                        <div class="font-body text-xs text-bimo-navy/50">{{ $contrat->garant_telephone ?? '' }}</div>
                        @if($contrat->garant_cni)
                        <div class="font-body text-xs text-bimo-navy/50 mt-1">CNI : {{ $contrat->garant_cni }}</div>
                        @endif
                    </div>
                    <div>
                        <div class="font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40 mb-1">Adresse</div>
                        <div class="font-body text-sm text-bimo-navy">{{ $contrat->garant_adresse ?? '—' }}</div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Paiements --}}
            <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
                <div class="flex items-center justify-between px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-[8px] bg-bimo-navy/5 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-bimo-navy/40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                        </div>
                        <span class="font-display font-bold text-sm text-bimo-navy">Historique des paiements</span>
                    </div>
                    @if($contrat->statut === 'actif')
                    <a href="{{ route('admin.paiements.create', ['contrat_id' => $contrat->id]) }}"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-bimo-gold/10 border border-bimo-gold/20 rounded-[7px]
                              font-body font-medium text-xs text-bimo-gold hover:bg-bimo-gold/20 transition-all duration-150">
                        + Ajouter
                    </a>
                    @endif
                </div>

                @if($paiements->isEmpty())
                <div class="px-5 py-10 text-center font-body text-sm text-bimo-navy/30">
                    Aucun paiement enregistré pour ce contrat.
                </div>
                @else

                {{-- Mobile --}}
                <div class="md:hidden divide-y divide-bimo-navy/[5%]">
                    @foreach($paiements as $p)
                    <div class="px-5 py-3.5 flex items-center justify-between gap-3">
                        <div>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-display font-semibold bg-bimo-gold/10 border border-bimo-gold/20 text-bimo-gold">
                                {{ \Carbon\Carbon::parse($p->periode)->translatedFormat('M Y') }}
                            </span>
                            <div class="font-body text-xs text-bimo-navy/40 mt-1">
                                {{ $p->date_paiement ? \Carbon\Carbon::parse($p->date_paiement)->format('d/m/Y') : '—' }}
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="font-display font-bold text-sm text-bimo-gold">{{ number_format($p->montant_encaisse, 0, ',', ' ') }} F</div>
                            <div class="font-body text-xs text-bimo-navy/40">Net: {{ number_format($p->net_a_verser_proprietaire ?? $p->net_proprietaire ?? 0, 0, ',', ' ') }} F</div>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- Desktop --}}
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-bimo-navy/[5%] bg-bimo-bg">
                                <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40">Période</th>
                                <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40">Date</th>
                                <th class="px-5 py-3 text-right font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40">Montant</th>
                                <th class="px-5 py-3 text-right font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40">Net proprio</th>
                                <th class="px-5 py-3 text-right font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40">Commission</th>
                                <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40">Mode</th>
                                <th class="px-5 py-3 text-center font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40">Statut</th>
                                <th class="px-5 py-3 text-center font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40">PDF</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-bimo-navy/[5%]">
                            @foreach($paiements as $p)
                            @php
                                $pBadge = match($p->statut) {
                                    'valide'  => 'bg-bimo-gold/10 border-bimo-gold/20 text-bimo-gold',
                                    'annulé'  => 'bg-bimo-red/10 border-bimo-red/20 text-bimo-red',
                                    default   => 'bg-bimo-navy/10 border-bimo-navy/15 text-bimo-navy/60',
                                };
                            @endphp
                            <tr class="hover:bg-bimo-bg transition-colors duration-100">
                                <td class="px-5 py-3.5">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-display font-semibold bg-bimo-gold/10 border border-bimo-gold/20 text-bimo-gold">
                                        {{ \Carbon\Carbon::parse($p->periode)->translatedFormat('M Y') }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 font-body text-xs text-bimo-navy/50">
                                    {{ $p->date_paiement ? \Carbon\Carbon::parse($p->date_paiement)->format('d/m/Y') : '—' }}
                                </td>
                                <td class="px-5 py-3.5 text-right font-display font-bold text-sm text-bimo-gold">
                                    {{ number_format($p->montant_encaisse, 0, ',', ' ') }} F
                                </td>
                                <td class="px-5 py-3.5 text-right font-body font-semibold text-sm text-bimo-navy">
                                    {{ number_format($p->net_a_verser_proprietaire ?? $p->net_proprietaire ?? 0, 0, ',', ' ') }} F
                                </td>
                                <td class="px-5 py-3.5 text-right font-body text-xs text-bimo-navy/50">
                                    {{ number_format($p->commission_ttc ?? 0, 0, ',', ' ') }} F
                                </td>
                                <td class="px-5 py-3.5 font-body text-xs text-bimo-navy/50">{{ $p->mode_paiement ?? '—' }}</td>
                                <td class="px-5 py-3.5 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full border text-[11px] font-body font-medium {{ $pBadge }}">
                                        {{ ucfirst($p->statut) }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-center">
                                    <a href="{{ route('admin.paiements.pdf', $p) }}" target="_blank"
                                       class="w-7 h-7 inline-flex items-center justify-center border border-bimo-navy/10 rounded-[6px] text-bimo-navy/40 hover:text-bimo-gold hover:border-bimo-gold/30 transition-all duration-150">
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

            {{-- Observations --}}
            @if($contrat->observations)
            <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
                <div class="flex items-center gap-3 px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
                    <div class="w-8 h-8 rounded-[8px] bg-bimo-navy/5 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-bimo-navy/40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                    </div>
                    <span class="font-display font-bold text-sm text-bimo-navy">Observations</span>
                </div>
                <div class="px-5 py-5">
                    <p class="font-body text-sm text-bimo-navy/70 leading-relaxed">{{ $contrat->observations }}</p>
                </div>
            </div>
            @endif

            {{-- Clauses particulières --}}
            @if($contrat->clauses_particulieres)
            <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
                <div class="flex items-center gap-3 px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
                    <div class="w-8 h-8 rounded-[8px] bg-bimo-navy/5 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-bimo-navy/40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
                    </div>
                    <span class="font-display font-bold text-sm text-bimo-navy">Clauses particulières</span>
                </div>
                <div class="px-5 py-5">
                    <p class="font-body text-sm text-bimo-navy/70 leading-relaxed whitespace-pre-wrap">{{ $contrat->clauses_particulieres }}</p>
                </div>
            </div>
            @endif

        </div>{{-- fin col gauche --}}

        {{-- COLONNE DROITE --}}
        <div class="lg:sticky lg:top-6 space-y-4">

            {{-- Infos contrat --}}
            <div class="bg-bimo-navy rounded-[14px] overflow-hidden">
                <div class="px-5 py-4 border-b border-white/[7%]">
                    <div class="font-display font-bold text-sm text-white">Informations</div>
                </div>
                <div class="px-5 py-2 divide-y divide-white/[6%]">
                    @php
                        $sideRows = [
                            ['Référence',      $contrat->reference_bail ?? 'BAIL-'.$contrat->id, 'font-display text-xs'],
                            ['Type bail',      \App\Models\Contrat::TYPES_BAIL[$contrat->type_bail] ?? $contrat->type_bail, ''],
                            ['Début',          $contrat->date_debut?->format('d/m/Y') ?? '—', ''],
                            ['Fin',            $contrat->date_fin?->format('d/m/Y') ?? 'Ouvert', ''],
                            ['Caution',        ($contrat->nombre_mois_caution ?? 1) . ' mois', ''],
                            ['Indexation',     ($contrat->indexation_annuelle ?? 0) . ' %/an', ''],
                            ['Créé le',        $contrat->created_at?->format('d/m/Y') ?? '—', ''],
                        ];
                    @endphp
                    @foreach($sideRows as [$lbl, $val, $cls])
                    <div class="flex items-center justify-between py-2.5">
                        <span class="font-body text-xs text-white/40">{{ $lbl }}</span>
                        <span class="font-body text-xs text-white/70 {{ $cls }}">{{ $val }}</span>
                    </div>
                    @endforeach

                    @if($contrat->statut === 'actif')
                    <div class="py-3">
                        <div class="p-3.5 bg-bimo-gold/10 border border-bimo-gold/20 rounded-[9px]">
                            <div class="font-body font-medium text-[9px] uppercase tracking-widest text-bimo-gold/60 mb-1">Prochaine période</div>
                            <div class="font-display font-bold text-base text-bimo-gold">
                                {{ $prochainePeriode?->translatedFormat('F Y') ?? '—' }}
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Le bien --}}
            <div class="bg-bimo-navy rounded-[14px] overflow-hidden">
                <div class="px-5 py-4 border-b border-white/[7%]">
                    <div class="font-display font-bold text-sm text-white">Le bien</div>
                </div>
                <div class="px-5 py-2 divide-y divide-white/[6%]">
                    @foreach([
                        ['Référence', $contrat->bien?->reference ?? '—'],
                        ['Type',      \App\Models\Bien::TYPES[$contrat->bien?->type] ?? '—'],
                        ['Adresse',   $contrat->bien?->adresse ?? '—'],
                        ['Ville',     $contrat->bien?->ville ?? '—'],
                    ] as [$lbl, $val])
                    <div class="flex items-center justify-between py-2.5">
                        <span class="font-body text-xs text-white/40">{{ $lbl }}</span>
                        <span class="font-body text-xs text-white/70">{{ $val }}</span>
                    </div>
                    @endforeach
                    <div class="py-3">
                        <a href="{{ route('admin.biens.show', $contrat->bien) }}"
                           class="flex items-center justify-center gap-2 px-4 py-2.5 border border-white/10 rounded-[9px]
                                  font-body text-xs text-bimo-gold hover:text-white hover:border-white/20 transition-all duration-150">
                            Voir le bien →
                        </a>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
