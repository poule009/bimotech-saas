@extends('layouts.app')
@section('header', 'Déclaration TVA — '.$declaration->periode_label)

@section('content')

<div class="space-y-4">

    {{-- Header + actions --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-3 flex-wrap">
            <a href="{{ route('admin.tva-agence.index') }}" class="font-body text-sm text-bimo-text/40 hover:text-bimo-text transition-colors duration-150">← Toutes les déclarations</a>
            <span class="text-bimo-text/20">|</span>
            <h1 class="font-display font-extrabold text-lg text-bimo-text">Déclaration TVA — {{ $declaration->periode_label }}</h1>
            @if($declaration->statut === 'brouillon' && $declaration->est_en_retard)
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-body font-semibold bg-bimo-red/10 border border-bimo-red/20 text-bimo-red">⚠ En retard</span>
            @elseif($declaration->statut === 'brouillon')
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-body font-semibold bg-bimo-navy/10 border border-bimo-navy/15 text-bimo-text/70">Brouillon</span>
            @elseif($declaration->statut === 'validee')
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-body font-semibold bg-bimo-gold/10 border border-bimo-gold/20 text-bimo-gold">Validée</span>
            @elseif($declaration->statut === 'deposee')
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-body font-semibold bg-bimo-navy/10 border border-bimo-navy/15 text-bimo-text/70">✓ Déposée à la DGI</span>
            @endif
        </div>
        <div class="flex items-center gap-2 flex-wrap flex-shrink-0">
            @if($declaration->statut !== 'deposee')
            <button type="button" id="btn-recalc" data-annee="{{ $annee }}" data-mois="{{ $mois }}" onclick="recalculer(this)"
                    class="inline-flex items-center gap-2 px-4 py-2 border border-bimo-navy/15 rounded-[9px] font-body text-sm text-bimo-text/60 hover:border-bimo-gold hover:text-bimo-text transition-all duration-150 cursor-pointer bg-white">
                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
                Recalculer
            </button>
            @if($declaration->statut === 'brouillon')
            <form method="POST" action="{{ route('admin.tva-agence.valider', [$annee, $mois]) }}" class="inline">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-bimo-navy text-white font-display font-bold text-sm rounded-[9px] hover:bg-bimo-navy-dk transition-colors duration-150 cursor-pointer">
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                    Valider
                </button>
            </form>
            @endif
            @if($declaration->statut === 'validee')
            <form method="POST" action="{{ route('admin.tva-agence.deposee', [$annee, $mois]) }}" class="inline"
                  onsubmit="return confirm('Confirmer le dépôt à la DGI ? Cette action est irréversible.')">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-bimo-navy text-white font-display font-bold text-sm rounded-[9px] hover:bg-bimo-navy-dk transition-colors duration-150 cursor-pointer">
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    Marquer comme déposée
                </button>
            </form>
            @endif
            @else
            <span class="font-body text-xs text-bimo-text/40">Déposée le {{ $declaration->deposee_le->format('d/m/Y à H:i') }}</span>
            @endif
            <a href="{{ route('admin.tva-agence.pdf', [$annee, $mois]) }}" target="_blank"
               class="inline-flex items-center gap-2 px-4 py-2 bg-bimo-gold/[8%] border border-bimo-gold/25 text-bimo-gold font-display font-bold text-sm rounded-[9px] hover:bg-bimo-gold/15 transition-all duration-150">
                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                Exporter PDF
            </a>
        </div>
    </div>

    {{-- Section 1 : TVA collectée --}}
    <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
            <div>
                <span class="font-display font-bold text-sm text-bimo-text">TVA collectée — auto-calculée depuis les paiements</span>
                <div class="font-body text-xs text-bimo-text/40 mt-0.5">Taux 18% — Art. 369 CGI SN · {{ $tvaData['nombre_paiements'] }} paiement(s)</div>
            </div>
            <span class="font-display font-extrabold text-xl text-bimo-text">{{ number_format($tvaData['total_tva_collectee'],0,',','') }}<span class="font-body text-sm text-bimo-text/40 ml-1">FCFA</span></span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-bimo-navy">
                        <th class="px-4 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-white/50">Référence</th>
                        <th class="px-4 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-white/50">Locataire</th>
                        <th class="px-4 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-white/50">Bien</th>
                        <th class="px-4 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-white/50">Type</th>
                        <th class="px-4 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-white/50">Période</th>
                        <th class="px-4 py-3 text-right font-body font-medium text-[10px] uppercase tracking-widest text-white/50">Loyer HT</th>
                        <th class="px-4 py-3 text-right font-body font-medium text-[10px] uppercase tracking-widest text-white/50">TVA comm.</th>
                        <th class="px-4 py-3 text-right font-body font-medium text-[10px] uppercase tracking-widest text-white/50">TVA loyer</th>
                        <th class="px-4 py-3 text-right font-body font-medium text-[10px] uppercase tracking-widest text-white/50">TVA charges</th>
                        <th class="px-4 py-3 text-right font-body font-medium text-[10px] uppercase tracking-widest text-white/50">TVA honor.</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-bimo-navy/[5%]">
                    @forelse($tvaData['detail_par_contrat'] as $p)
                    <tr class="hover:bg-bimo-bg transition-colors duration-100">
                        <td class="px-4 py-3 font-body text-[11px] text-bimo-text/40 uppercase" style="font-family:monospace">{{ $p['reference'] }}</td>
                        <td class="px-4 py-3 font-body text-sm text-bimo-text/70">{{ $p['locataire'] }}</td>
                        <td class="px-4 py-3 font-body text-xs text-bimo-text/60 max-w-[120px] truncate">{{ $p['bien'] }}</td>
                        <td class="px-4 py-3 font-body text-xs text-bimo-text/60">{{ ucfirst($p['type_bail']) }}</td>
                        <td class="px-4 py-3 font-body text-xs text-bimo-text/50">@if($p['periode']){{ \Carbon\Carbon::parse($p['periode'])->translatedFormat('M Y') }}@else—@endif</td>
                        <td class="px-4 py-3 text-right font-display font-semibold text-sm text-bimo-text/70">{{ $p['loyer_ht'] > 0 ? number_format($p['loyer_ht'],0,',','').' F' : '—' }}</td>
                        <td class="px-4 py-3 text-right font-body text-sm text-bimo-gold">{{ $p['tva_commission'] > 0 ? number_format($p['tva_commission'],0,',','').' F' : '—' }}</td>
                        <td class="px-4 py-3 text-right font-body text-sm text-bimo-gold">{{ $p['tva_loyer'] > 0 ? number_format($p['tva_loyer'],0,',','').' F' : '—' }}</td>
                        <td class="px-4 py-3 text-right font-body text-sm text-bimo-gold">{{ $p['tva_charges'] > 0 ? number_format($p['tva_charges'],0,',','').' F' : '—' }}</td>
                        <td class="px-4 py-3 text-right font-body text-sm text-bimo-gold">{{ $p['tva_frais'] > 0 ? number_format($p['tva_frais'],0,',','').' F' : '—' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="10" class="px-4 py-8 text-center font-body text-sm text-bimo-text/30 italic">Aucun paiement enregistré pour ce mois</td></tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="bg-bimo-gold/[8%] border-t-2 border-bimo-gold/30">
                        <td colspan="6" class="px-4 py-3 font-display font-bold text-sm text-bimo-text/70">Sous-totaux TVA collectée</td>
                        <td class="px-4 py-3 text-right font-display font-bold text-sm text-bimo-gold">{{ number_format($tvaData['tva_commissions'],0,',','') }} F</td>
                        <td class="px-4 py-3 text-right font-display font-bold text-sm text-bimo-gold">{{ number_format($tvaData['tva_loyers_commerciaux'],0,',','') }} F</td>
                        <td class="px-4 py-3 text-right font-display font-bold text-sm text-bimo-gold">{{ number_format($tvaData['tva_charges_forfait'],0,',','') }} F</td>
                        <td class="px-4 py-3 text-right font-display font-bold text-sm text-bimo-gold">{{ number_format($tvaData['tva_honoraires'],0,',','') }} F</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        {{-- Récap par type --}}
        <div class="grid grid-cols-4 border-t border-bimo-navy/[5%]">
            @foreach([['Commissions',$tvaData['tva_commissions']],['Loyers commerciaux',$tvaData['tva_loyers_commerciaux']],['Charges forfait',$tvaData['tva_charges_forfait']],['Honoraires',$tvaData['tva_honoraires']]] as [$lbl,$val])
            <div class="p-4 text-center border-r last:border-r-0 border-bimo-navy/[5%]">
                <div class="font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/30 mb-1">{{ $lbl }}</div>
                <div class="font-display font-bold text-lg text-bimo-gold">{{ number_format($val,0,',','') }} F</div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Section 2 : TVA déductible --}}
    <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
            <div>
                <span class="font-display font-bold text-sm text-bimo-text">TVA déductible — saisie manuelle</span>
                <div class="font-body text-xs text-bimo-text/40 mt-0.5">Saisir uniquement la TVA figurant sur des factures au nom de l'agence avec NINEA du fournisseur</div>
            </div>
            <span class="font-display font-extrabold text-xl text-bimo-text">{{ number_format($declaration->total_tva_deductible,0,',','') }}<span class="font-body text-sm text-bimo-text/40 ml-1">FCFA</span></span>
        </div>
        <div class="px-5 py-5">
            @if($declaration->statut !== 'deposee')
            <div class="flex items-start gap-2 bg-bimo-gold/[6%] border border-bimo-gold/20 rounded-[8px] px-4 py-3 mb-5">
                <svg class="w-4 h-4 text-bimo-gold flex-shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                <p class="font-body text-xs text-bimo-gold/80">⚠ Saisir uniquement la TVA (18%) figurant sur des <strong>factures au nom de l'agence</strong> avec le <strong>NINEA du fournisseur</strong>. Montant = TVA seule, pas TTC.</p>
            </div>
            <form method="POST" action="{{ route('admin.tva-agence.update', [$annee, $mois]) }}" class="space-y-4">
                @csrf @method('PUT')
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="block font-body font-medium text-sm text-bimo-text">TVA sur achats / fournitures</label>
                        <input type="number" name="tva_achats_fournitures" value="{{ old('tva_achats_fournitures',$declaration->tva_achats_fournitures) }}" min="0" step="1" placeholder="0"
                               class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                        <p class="font-body text-xs text-bimo-text/40">Matériel, papeterie, logiciels…</p>
                    </div>
                    <div class="space-y-1.5">
                        <label class="block font-body font-medium text-sm text-bimo-text">TVA sur loyer du bureau de l'agence</label>
                        <input type="number" name="tva_loyer_bureau" value="{{ old('tva_loyer_bureau',$declaration->tva_loyer_bureau) }}" min="0" step="1" placeholder="0"
                               class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                        <p class="font-body text-xs text-bimo-text/40">Local commercial loué par l'agence</p>
                    </div>
                    <div class="space-y-1.5">
                        <label class="block font-body font-medium text-sm text-bimo-text">TVA déductible — autres</label>
                        <input type="number" name="tva_autres_deductible" value="{{ old('tva_autres_deductible',$declaration->tva_autres_deductible) }}" min="0" step="1" placeholder="0"
                               class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                        <p class="font-body text-xs text-bimo-text/40">Prestataires, conseil, autres achats sur facture NINEA</p>
                    </div>
                    <div class="space-y-1.5 sm:col-span-2">
                        <label class="block font-body font-medium text-sm text-bimo-text">Notes internes <span class="font-light text-bimo-text/40">(facultatif)</span></label>
                        <textarea name="notes" rows="2" placeholder="Détail des achats, références factures…"
                                  class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150 resize-none">{{ old('notes',$declaration->notes) }}</textarea>
                    </div>
                </div>
                <div class="flex justify-end">
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-bimo-navy text-white font-display font-bold text-sm rounded-[10px] hover:bg-bimo-navy-dk transition-colors duration-150 cursor-pointer">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/></svg>
                        Enregistrer la TVA déductible
                    </button>
                </div>
            </form>
            @else
            <div class="grid grid-cols-3 gap-3 mb-4">
                @foreach([['Achats / fournitures',$declaration->tva_achats_fournitures],['Loyer bureau agence',$declaration->tva_loyer_bureau],['Autres déductibles',$declaration->tva_autres_deductible]] as [$lbl,$val])
                <div class="bg-bimo-bg border border-bimo-navy/[8%] rounded-[9px] p-4">
                    <div class="font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40 mb-1.5">{{ $lbl }}</div>
                    <div class="font-display font-bold text-xl text-bimo-text">{{ number_format($val,0,',','') }} F</div>
                </div>
                @endforeach
            </div>
            @if($declaration->notes)
            <div class="px-4 py-3 bg-bimo-bg border border-bimo-navy/[8%] rounded-[9px] font-body text-sm text-bimo-text/60">
                <strong>Notes :</strong> {{ $declaration->notes }}
            </div>
            @endif
            @endif
        </div>
    </div>

    {{-- Section 3 : Résultat --}}
    <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
            <div>
                <span class="font-display font-bold text-sm text-bimo-text">Résultat — TVA nette à reverser</span>
                <div class="font-body text-xs text-bimo-text/40 mt-0.5">Art. 370 CGI SN · Échéance : {{ $declaration->date_echeance->translatedFormat('d F Y') }}</div>
            </div>
        </div>
        <div class="px-5 py-5 max-w-lg">
            <div class="space-y-0 divide-y divide-bimo-navy/[5%]">
                <div class="flex items-center justify-between py-3">
                    <span class="font-body text-sm text-bimo-text/70">TVA collectée</span>
                    <span class="font-display font-bold text-sm text-bimo-text">{{ number_format($declaration->total_tva_collectee,0,',','') }} FCFA</span>
                </div>
                <div class="flex items-center justify-between py-3">
                    <span class="font-body text-sm text-bimo-gold">− TVA déductible</span>
                    <span class="font-display font-bold text-sm text-bimo-gold">− {{ number_format($declaration->total_tva_deductible,0,',','') }} FCFA</span>
                </div>
                @if((float)$declaration->credit_reporte_entrant > 0)
                <div class="flex items-center justify-between py-3">
                    <span class="font-body text-sm text-bimo-gold">− Crédit reporté mois précédent</span>
                    <span class="font-display font-bold text-sm text-bimo-gold">− {{ number_format($declaration->credit_reporte_entrant,0,',','') }} FCFA</span>
                </div>
                @endif
                <div class="flex items-center justify-between py-4 border-t-2 {{ (float)$declaration->tva_nette_due > 0 ? 'border-bimo-red/30' : 'border-bimo-gold/30' }}">
                    @if((float)$declaration->tva_nette_due > 0)
                    <span class="font-display font-bold text-base text-bimo-red">TVA nette DUE</span>
                    <span class="font-display font-extrabold text-xl text-bimo-red">{{ number_format($declaration->tva_nette_due,0,',','') }} FCFA</span>
                    @else
                    <span class="font-display font-bold text-base text-bimo-gold">Crédit de TVA</span>
                    <span class="font-display font-extrabold text-xl text-bimo-gold">{{ number_format($declaration->credit_reporte_sortant,0,',','') }} FCFA</span>
                    @endif
                </div>
            </div>
        </div>
        @if((float)$declaration->tva_nette_due > 0)
        <div class="px-5 pb-5">
            <div class="flex items-start gap-2 bg-bimo-red/[5%] border border-bimo-red/15 rounded-[10px] px-4 py-3">
                <svg class="w-4 h-4 text-bimo-red flex-shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                <p class="font-body text-sm text-bimo-red leading-relaxed">⚠ <strong>{{ number_format($declaration->tva_nette_due,0,',','') }} FCFA à verser à la DGI avant le {{ $declaration->date_echeance->translatedFormat('d F Y') }}</strong> — Art. 370 CGI SN. Tout versement tardif entraîne des pénalités. <strong>Consultez votre Centre des Services Fiscaux (CSF).</strong></p>
            </div>
        </div>
        @elseif((float)$declaration->credit_reporte_sortant > 0)
        <div class="px-5 pb-5">
            <div class="flex items-start gap-2 bg-bimo-gold/[6%] border border-bimo-gold/20 rounded-[10px] px-4 py-3">
                <svg class="w-4 h-4 text-bimo-gold flex-shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <p class="font-body text-sm text-bimo-gold leading-relaxed">ℹ <strong>Crédit de TVA de {{ number_format($declaration->credit_reporte_sortant,0,',','') }} FCFA</strong> reporté automatiquement sur la déclaration de {{ \Carbon\Carbon::create($annee, $mois, 1)->addMonth()->locale('fr')->translatedFormat('F Y') }}.</p>
            </div>
        </div>
        @endif
    </div>

</div>

@push('scripts')
<script>
function recalculer(btn) {
    btn.disabled = true;
    var txt = btn.textContent;
    btn.textContent = '…';
    fetch('/admin/tva-agence/{{ $annee }}/{{ $mois }}/recalculer', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '', 'Accept': 'application/json' }
    })
    .then(function(r){ return r.json(); })
    .then(function(data){ if(data.success){ location.reload(); } else { alert(data.message); btn.disabled = false; btn.textContent = txt; } })
    .catch(function(){ alert('Erreur réseau'); btn.disabled = false; btn.textContent = txt; });
}
</script>
@endpush

@endsection
