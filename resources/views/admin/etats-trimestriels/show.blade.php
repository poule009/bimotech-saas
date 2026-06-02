@extends('layouts.app')
@section('header', 'État trimestriel BRS — T'.$trimestre.' '.$annee)

@section('content')

<div class="space-y-4">

    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <a href="{{ route('admin.etats-trimestriels.index') }}"
               class="inline-flex items-center gap-1.5 font-body text-sm text-bimo-navy/40 hover:text-bimo-navy transition-colors duration-150 mb-2">
                <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                Retour
            </a>
            <h1 class="font-display font-extrabold text-xl text-bimo-navy tracking-tight">État trimestriel BRS — T{{ $trimestre }} {{ $annee }}</h1>
            <p class="font-body text-sm text-bimo-navy/50 mt-1">
                Art. 200 §5 CGI Sénégal · {{ $lignes->count() }} bailleur(s) concerné(s) · Date limite : <strong>{{ $dateLimite->translatedFormat('d F Y') }}</strong>
            </p>
        </div>
        <div class="flex items-center gap-2 flex-shrink-0">
            <a href="{{ route('admin.etats-trimestriels.pdf', [$annee, $trimestre]) }}" target="_blank"
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-bimo-navy text-white font-display font-bold text-sm rounded-[10px] hover:bg-bimo-navy-dk transition-colors duration-150">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                Télécharger PDF
            </a>
            <a href="{{ route('admin.etats-trimestriels.csv', [$annee, $trimestre]) }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-bimo-navy/15 text-bimo-navy/60 font-body text-sm rounded-[10px] hover:border-bimo-navy/30 hover:text-bimo-navy transition-all duration-150">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Télécharger CSV
            </a>
        </div>
    </div>

    {{-- Alerte NINEA --}}
    @if($lignes->where('has_warning_ninea', true)->count() > 0)
    <div class="flex items-start gap-2 bg-bimo-gold/[6%] border-l-4 border-bimo-gold border border-bimo-gold/20 rounded-r-[8px] px-4 py-3">
        <svg class="w-4 h-4 text-bimo-gold flex-shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
        <p class="font-body text-sm text-bimo-gold/80">
            <strong>⚠ {{ $lignes->where('has_warning_ninea', true)->count() }} bailleur(s) sans NINEA</strong> — Pour ces bailleurs, le CGI (Art. 200 §5) impose d'indiquer : date et lieu de naissance + numéro de pièce d'identité. Mettez à jour leur fiche avant le dépôt à la DGID.
        </p>
    </div>
    @endif

    {{-- Table --}}
    <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
            <span class="font-display font-bold text-sm text-bimo-navy">Détail par bailleur — T{{ $trimestre }} {{ $annee }}</span>
            <span class="font-body text-xs text-bimo-navy/40">{{ $lignes->count() }} bailleur(s)</span>
        </div>

        @if($lignes->isEmpty())
        <div class="px-5 py-10 text-center font-body text-sm text-bimo-navy/30">Aucun paiement avec BRS retenu sur ce trimestre.</div>
        @else

        {{-- Mobile --}}
        <div class="md:hidden divide-y divide-bimo-navy/[5%]">
            @foreach($lignes as $ligne)
            <div class="px-4 py-3.5">
                <div class="flex items-start justify-between gap-2 mb-1.5">
                    <div>
                        <div class="font-body font-semibold text-sm text-bimo-navy">{{ $ligne['nom_complet'] }}</div>
                        @if($ligne['ninea'])<div class="font-body text-xs text-bimo-navy/40" style="font-family:monospace">{{ $ligne['ninea'] }}</div>
                        @else<span class="inline-flex items-center gap-1 px-1.5 py-0.5 bg-bimo-gold/10 border border-bimo-gold/20 rounded text-[10px] font-body font-semibold text-bimo-gold">⚠ NINEA manquant</span>@endif
                    </div>
                    <span class="font-display font-bold text-sm text-bimo-red flex-shrink-0">{{ number_format($ligne['brs_retenu'],0,',','') }} F</span>
                </div>
                <div class="font-body text-xs text-bimo-navy/40">{{ $ligne['periode_label'] }} · {{ $ligne['nb_paiements'] }} paiement(s)</div>
            </div>
            @endforeach
        </div>

        {{-- Desktop --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-bimo-navy/[5%] bg-bimo-bg">
                        <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40">Bailleur</th>
                        <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40">NINEA / Pièce d'identité</th>
                        <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40">Adresse</th>
                        <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40">Période</th>
                        <th class="px-5 py-3 text-right font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40">Loyers nets versés</th>
                        <th class="px-5 py-3 text-right font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40">BRS retenu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-bimo-navy/[5%]">
                    @foreach($lignes as $ligne)
                    <tr class="hover:bg-bimo-bg transition-colors duration-100">
                        <td class="px-5 py-3.5">
                            <div class="font-body font-semibold text-sm text-bimo-navy">{{ $ligne['nom_complet'] }}</div>
                            @if($ligne['email'])<div class="font-body text-xs text-bimo-navy/50">{{ $ligne['email'] }}</div>@endif
                            @if($ligne['telephone'])<div class="font-body text-xs text-bimo-navy/30">{{ $ligne['telephone'] }}</div>@endif
                        </td>
                        <td class="px-5 py-3.5">
                            @if($ligne['ninea'])
                            <div class="font-body font-semibold text-sm text-bimo-navy" style="font-family:monospace">{{ $ligne['ninea'] }}</div>
                            @else
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-bimo-gold/10 border border-bimo-gold/20 rounded-[5px] text-[10px] font-body font-semibold text-bimo-gold">
                                <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                                NINEA manquant
                            </span>
                            @if($ligne['cni'])<div class="font-body text-xs text-bimo-navy/50 mt-1">CNI : {{ $ligne['cni'] }}</div>@endif
                            @if($ligne['date_naissance'])<div class="font-body text-xs text-bimo-navy/50">Né(e) le : {{ \Carbon\Carbon::parse($ligne['date_naissance'])->format('d/m/Y') }}</div>@endif
                            <div class="font-body text-[10px] text-bimo-gold/70 italic mt-0.5">⚠ Mettre à jour avant dépôt DGID</div>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 font-body text-sm text-bimo-navy/60">{{ $ligne['adresse'] ?: '—' }}</td>
                        <td class="px-5 py-3.5">
                            <div class="font-body text-sm text-bimo-navy/70">{{ $ligne['periode_label'] }}</div>
                            <div class="font-body text-xs text-bimo-navy/30">{{ $ligne['nb_paiements'] }} paiement(s)</div>
                        </td>
                        <td class="px-5 py-3.5 text-right font-display font-semibold text-sm text-bimo-navy/70">{{ number_format($ligne['loyers_verses'],0,',','') }} F</td>
                        <td class="px-5 py-3.5 text-right font-display font-bold text-sm text-bimo-red">{{ number_format($ligne['brs_retenu'],0,',','') }} F</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-bimo-gold/[8%] border-t-2 border-bimo-gold/30">
                        <td colspan="4" class="px-5 py-3 font-display font-bold text-sm text-bimo-navy/70">TOTAL T{{ $trimestre }} {{ $annee }} — {{ $lignes->count() }} bailleur(s)</td>
                        <td class="px-5 py-3 text-right font-display font-bold text-sm text-bimo-navy/70">{{ number_format($totalNet,0,',','') }} F</td>
                        <td class="px-5 py-3 text-right font-display font-extrabold text-sm text-bimo-red">{{ number_format($totalBrs,0,',','') }} F</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @endif
    </div>

    <div class="flex items-start gap-2 bg-bimo-navy/[4%] border border-bimo-navy/10 rounded-[10px] px-4 py-3">
        <svg class="w-4 h-4 text-bimo-navy/40 flex-shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <p class="font-body text-xs text-bimo-navy/50 leading-relaxed">
            <strong>Art. 200 §5 CGI Sénégal :</strong> Cet état doit être remis au Centre des Services Fiscaux avant le <strong>{{ $dateLimite->translatedFormat('d F Y') }}</strong>.
            Il doit comporter pour chaque bailleur : prénom, nom, emploi, adresse, NINEA (ou date/lieu naissance + pièce d'identité), montant des loyers nets reversés, et montant du BRS retenu.
        </p>
    </div>

</div>
@endsection
