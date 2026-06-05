@extends('layouts.app')
@section('header', 'Déclarations TVA')

@section('content')

<div class="space-y-4 md:space-y-5">

    {{-- En-tête --}}
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
        <div>
            <h1 class="font-display font-extrabold text-xl md:text-2xl text-bimo-text tracking-tight">Déclarations TVA mensuelles</h1>
            <p class="font-body text-sm text-bimo-text/50 mt-1">TVA agence — Art. 369-370 CGI Sénégal — Année {{ $annee }}</p>
        </div>
        <div class="flex-shrink-0 bg-bimo-navy/[4%] border border-bimo-navy/10 rounded-[10px] px-4 py-3 text-right">
            <div class="font-body font-medium text-xs text-bimo-text/50">📋 Déclaration mensuelle obligatoire</div>
            <div class="font-body text-xs text-bimo-text/40 mt-0.5">À déposer avant le <strong>15 du mois M+1</strong></div>
        </div>
    </div>

    {{-- Crédit TVA reporté --}}
    @if($creditCumule > 0)
    <div class="flex items-center gap-2 bg-bimo-gold/[6%] border border-bimo-gold/20 rounded-[10px] px-4 py-3">
        <svg class="w-4 h-4 text-bimo-gold flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <span class="font-body font-semibold text-sm text-bimo-gold">Crédit TVA reporté : {{ number_format($creditCumule,0,',','') }} FCFA — Imputable sur prochaine déclaration</span>
    </div>
    @endif

    {{-- Filtre année --}}
    <form method="GET" class="flex items-center gap-3 bg-white rounded-[12px] border border-bimo-navy/10 px-5 py-3.5">
        <span class="font-body font-medium text-xs text-bimo-text/50 whitespace-nowrap">Année fiscale :</span>
        <select name="annee"
                class="px-3 py-2 border border-bimo-navy/15 rounded-[8px] font-body text-sm text-bimo-text bg-bimo-bg focus:outline-none focus:border-bimo-gold cursor-pointer transition-all duration-150">
            @foreach($anneesDisponibles as $a)
            <option value="{{ $a }}" {{ $annee == $a ? 'selected' : '' }}>{{ $a }}</option>
            @endforeach
        </select>
        <button type="submit"
                class="inline-flex items-center px-4 py-2 bg-bimo-navy text-white font-display font-bold text-sm rounded-[9px] hover:bg-bimo-navy-dk transition-colors duration-150">
            Afficher
        </button>
    </form>

    {{-- Table 12 mois --}}
    @php $totalCollectee = 0; $totalDeductible = 0; $totalNette = 0; @endphp
    <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
        {{-- Mobile --}}
        <div class="md:hidden divide-y divide-bimo-navy/[5%]">
            @foreach($mois as $m)
            @php
                $d = $m['declaration'];
                if($d) { $totalCollectee += (float)$d->total_tva_collectee; $totalDeductible += (float)$d->total_tva_deductible; $totalNette += (float)$d->tva_nette_due; }
                $rowOp = $m['statut'] === 'futur' ? 'opacity-40' : ($m['statut'] === 'en_retard' ? 'bg-bimo-red/[3%]' : '');
            @endphp
            <div class="px-4 py-3.5 {{ $rowOp }}">
                <div class="flex items-center justify-between gap-2 mb-1.5">
                    <span class="font-body font-semibold text-sm text-bimo-text">{{ $m['label'] }}</span>
                    @if($m['statut'] === 'futur') <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-body font-medium bg-bimo-navy/[5%] text-bimo-text/30">À venir</span>
                    @elseif($m['statut'] === 'non_calcule') <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-body font-medium bg-bimo-navy/[5%] text-bimo-text/40">Non calculé</span>
                    @elseif($m['statut'] === 'brouillon') <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-body font-semibold bg-bimo-navy/10 border border-bimo-navy/15 text-bimo-text/70">Brouillon</span>
                    @elseif($m['statut'] === 'validee') <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-body font-semibold bg-bimo-gold/10 border border-bimo-gold/20 text-bimo-gold">Validée</span>
                    @elseif($m['statut'] === 'deposee') <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-body font-semibold bg-bimo-navy/10 border border-bimo-navy/15 text-bimo-text/70">✓ Déposée</span>
                    @elseif($m['statut'] === 'en_retard') <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-body font-semibold bg-bimo-red/10 border border-bimo-red/20 text-bimo-red">⚠ En retard</span>
                    @endif
                </div>
                @if($d)
                <div class="flex items-center gap-3 text-xs">
                    <span class="font-body text-bimo-text/50">Col. : <strong>{{ number_format($d->total_tva_collectee,0,',','') }} F</strong></span>
                    @if($d->tva_nette_due > 0)<span class="font-body text-bimo-red">Due : {{ number_format($d->tva_nette_due,0,',','') }} F</span>@endif
                </div>
                @endif
                @if($m['statut'] !== 'futur')
                <div class="flex items-center gap-2 mt-2">
                    <a href="{{ route('admin.tva-agence.show', [$annee, $m['numero']]) }}"
                       class="inline-flex items-center gap-1 px-3 py-1 bg-bimo-navy text-white rounded-[7px] font-body text-xs">Voir</a>
                    <button type="button" class="inline-flex items-center gap-1 px-3 py-1 border border-bimo-navy/15 rounded-[7px] font-body text-xs text-bimo-text/60 hover:border-bimo-gold hover:text-bimo-gold transition-all duration-150 cursor-pointer"
                            data-annee="{{ $annee }}" data-mois="{{ $m['numero'] }}" onclick="recalculer(this)">Recalculer</button>
                </div>
                @endif
            </div>
            @endforeach
        </div>

        {{-- Desktop --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-bimo-navy">
                        <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-white/50 w-32">Mois</th>
                        <th class="px-5 py-3 text-right font-body font-medium text-[10px] uppercase tracking-widest text-white/50">TVA collectée</th>
                        <th class="px-5 py-3 text-right font-body font-medium text-[10px] uppercase tracking-widest text-white/50">TVA déductible</th>
                        <th class="px-5 py-3 text-right font-body font-medium text-[10px] uppercase tracking-widest text-white/50">Crédit entrant</th>
                        <th class="px-5 py-3 text-right font-body font-medium text-[10px] uppercase tracking-widest text-white/50">TVA nette due</th>
                        <th class="px-5 py-3 text-center font-body font-medium text-[10px] uppercase tracking-widest text-white/50 w-28">Statut</th>
                        <th class="px-5 py-3 text-center font-body font-medium text-[10px] uppercase tracking-widest text-white/50 w-44">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-bimo-navy/[5%]">
                    @foreach($mois as $m)
                    @php
                        $d = $m['declaration'];
                        if($d) { $totalCollectee += (float)$d->total_tva_collectee; $totalDeductible += (float)$d->total_tva_deductible; $totalNette += (float)$d->tva_nette_due; }
                        $rowCls = $m['statut'] === 'futur' ? 'opacity-40' : ($m['statut'] === 'en_retard' ? 'bg-bimo-red/[3%]' : 'hover:bg-bimo-bg');
                    @endphp
                    <tr class="{{ $rowCls }} transition-colors duration-100">
                        <td class="px-5 py-3.5 font-body font-semibold text-sm text-bimo-text">{{ $m['label'] }}</td>
                        <td class="px-5 py-3.5 text-right font-display font-semibold text-sm text-bimo-text/70">
                            @if($d && $d->total_tva_collectee > 0) {{ number_format($d->total_tva_collectee,0,',','') }} F
                            @elseif($m['statut'] !== 'futur') <span class="text-bimo-text/20">—</span>
                            @else <span class="text-bimo-text/10">—</span> @endif
                        </td>
                        <td class="px-5 py-3.5 text-right font-display font-semibold text-sm text-bimo-gold">
                            @if($d && $d->total_tva_deductible > 0) {{ number_format($d->total_tva_deductible,0,',','') }} F
                            @elseif($m['statut'] !== 'futur') <span class="text-bimo-text/20">—</span>
                            @else <span class="text-bimo-text/10">—</span> @endif
                        </td>
                        <td class="px-5 py-3.5 text-right font-display font-semibold text-sm text-bimo-gold">
                            @if($d && $d->credit_reporte_entrant > 0) {{ number_format($d->credit_reporte_entrant,0,',','') }} F
                            @else <span class="text-bimo-text/10">—</span> @endif
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            @if($d)
                                @if($d->tva_nette_due > 0) <span class="font-display font-bold text-sm text-bimo-red">{{ number_format($d->tva_nette_due,0,',','') }} F</span>
                                @elseif($d->credit_reporte_sortant > 0) <span class="font-body text-xs text-bimo-gold">Crédit {{ number_format($d->credit_reporte_sortant,0,',','') }} F</span>
                                @else <span class="font-display font-semibold text-sm text-bimo-text/40">0 F</span>
                                @endif
                            @else <span class="text-bimo-text/10">—</span> @endif
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            @if($m['statut'] === 'futur') <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-body font-medium bg-bimo-navy/[5%] text-bimo-text/30">À venir</span>
                            @elseif($m['statut'] === 'non_calcule') <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-body font-medium bg-bimo-navy/[5%] text-bimo-text/40">Non calculé</span>
                            @elseif($m['statut'] === 'brouillon') <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-body font-semibold bg-bimo-navy/10 border border-bimo-navy/15 text-bimo-text/70">Brouillon</span>
                            @elseif($m['statut'] === 'validee') <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-body font-semibold bg-bimo-gold/10 border border-bimo-gold/20 text-bimo-gold">Validée</span>
                            @elseif($m['statut'] === 'deposee') <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-body font-semibold bg-bimo-navy/10 border border-bimo-navy/15 text-bimo-text/70">✓ Déposée</span>
                            @elseif($m['statut'] === 'en_retard') <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-body font-semibold bg-bimo-red/10 border border-bimo-red/20 text-bimo-red">⚠ En retard</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            @if($m['statut'] !== 'futur')
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.tva-agence.show', [$annee, $m['numero']]) }}"
                                   class="inline-flex items-center gap-1 px-3 py-1.5 bg-bimo-navy text-white rounded-[7px] font-body text-xs hover:bg-bimo-navy-dk transition-colors duration-150">
                                    <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    Voir
                                </a>
                                <button type="button"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 border border-bimo-navy/15 rounded-[7px] font-body text-xs text-bimo-text/60 hover:border-bimo-gold hover:text-bimo-gold transition-all duration-150 cursor-pointer"
                                        data-annee="{{ $annee }}" data-mois="{{ $m['numero'] }}" onclick="recalculer(this)">
                                    <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
                                    Recalculer
                                </button>
                            </div>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-bimo-gold/[8%] border-t-2 border-bimo-gold/30">
                        <td class="px-5 py-3 font-display font-bold text-sm text-bimo-text/70">Total {{ $annee }}</td>
                        <td class="px-5 py-3 text-right font-display font-bold text-sm text-bimo-text/70">{{ number_format($totalCollectee,0,',','') }} F</td>
                        <td class="px-5 py-3 text-right font-display font-bold text-sm text-bimo-gold">{{ number_format($totalDeductible,0,',','') }} F</td>
                        <td class="px-5 py-3 text-right font-body text-sm text-bimo-text/30">—</td>
                        <td class="px-5 py-3 text-right font-display font-extrabold text-sm text-bimo-red">{{ number_format($totalNette,0,',','') }} F</td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="flex items-start gap-2 bg-bimo-gold/[6%] border border-bimo-gold/20 rounded-[10px] px-4 py-3">
        <svg class="w-4 h-4 text-bimo-gold flex-shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
        <p class="font-body text-xs text-bimo-gold/80 leading-relaxed">
            <strong>Art. 370 CGI Sénégal :</strong> La TVA nette due (collectée − déductible) doit être reversée à la DGI avant le <strong>15 de chaque mois M+1</strong>.
            Un crédit de TVA (déductible &gt; collectée) est reportable sur les mois suivants.
            <strong>Consultez votre Centre des Services Fiscaux (CSF) pour le dépôt officiel.</strong>
        </p>
    </div>

</div>

@push('scripts')
<script>
function recalculer(btn) {
    var annee = btn.dataset.annee, mois = btn.dataset.mois;
    btn.disabled = true; btn.textContent = '…';
    fetch('/admin/tva-agence/' + annee + '/' + mois + '/recalculer', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '', 'Accept': 'application/json' }
    })
    .then(function(r){ return r.json(); })
    .then(function(data){ if(data.success){ location.reload(); } else { alert(data.message); btn.disabled = false; btn.textContent = 'Recalculer'; } })
    .catch(function(){ alert('Erreur réseau'); btn.disabled = false; btn.textContent = 'Recalculer'; });
}
</script>
@endpush

@endsection
