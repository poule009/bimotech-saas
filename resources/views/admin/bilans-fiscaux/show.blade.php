@extends('layouts.app')
@section('header', 'Bilan fiscal — '.$proprietaire->name)

@section('content')

<div class="space-y-4 md:space-y-5">

    {{-- Breadcrumb + actions --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <nav class="flex items-center gap-2 font-body text-sm text-bimo-navy/40 mb-1">
                <a href="{{ route('admin.bilans-fiscaux.index') }}" class="hover:text-bimo-navy transition-colors duration-150">Bilans fiscaux</a>
                <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                <span class="text-bimo-navy font-medium">{{ $proprietaire->name }} — {{ $annee }}</span>
            </nav>
            <h1 class="font-display font-extrabold text-xl md:text-2xl text-bimo-navy tracking-tight">Bilan fiscal {{ $annee }} — {{ $proprietaire->name }}</h1>
            <p class="font-body text-sm text-bimo-navy/50 mt-1">Calculé le {{ $bilan->calcule_le->format('d/m/Y à H:i') }} · {{ $bilan->nb_paiements }} paiement(s) · {{ $bilan->nb_biens_geres }} bien(s)</p>
        </div>
        <div class="flex flex-wrap gap-2 flex-shrink-0">
            <form method="POST" action="{{ route('admin.bilans-fiscaux.calculate', $proprietaire) }}">
                @csrf
                <input type="hidden" name="annee" value="{{ $annee }}">
                <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-bimo-navy/15 rounded-[10px] font-body text-sm text-bimo-navy/60 hover:border-bimo-gold hover:text-bimo-navy transition-all duration-150 cursor-pointer">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 11-2.12-9.36L23 10"/></svg>
                    Recalculer
                </button>
            </form>
            <a href="{{ route('admin.bilans-fiscaux.pdf', [$proprietaire, 'annee' => $annee]) }}" target="_blank"
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-bimo-navy text-white font-display font-bold text-sm rounded-[10px] hover:bg-bimo-navy-dk transition-colors duration-150">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                Exporter PDF DGI
            </a>
        </div>
    </div>

    {{-- Filtre année --}}
    <form method="GET" class="inline-flex items-center gap-3 bg-white rounded-[10px] border border-bimo-navy/10 px-4 py-2.5">
        <span class="font-body text-xs text-bimo-navy/50">Année :</span>
        <select name="annee" onchange="this.form.submit()"
                class="border-none outline-none font-body text-sm text-bimo-navy bg-transparent cursor-pointer">
            @foreach($anneesDisponibles as $a)
            <option value="{{ $a }}" {{ $annee == $a ? 'selected':'' }}>{{ $a }}</option>
            @endforeach
        </select>
    </form>

    <div class="grid grid-cols-1 lg:grid-cols-[1fr_280px] gap-5 items-start">
        <div class="space-y-4">

            {{-- KPIs --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <div class="bg-bimo-gold/[8%] rounded-[14px] border border-bimo-gold/25 p-4">
                    <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-gold/70 mb-1.5">Revenus bruts loyers</div>
                    <div class="font-display font-extrabold text-xl text-bimo-gold leading-none">{{ number_format($bilan->revenus_bruts_loyers,0,',','') }}<span class="font-body text-xs text-bimo-gold/60 ml-1">F</span></div>
                    <div class="font-body text-[10.5px] text-bimo-gold/60 mt-1">Loyers HT annuels</div>
                </div>
                <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-4">
                    <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-navy/50 mb-1.5">Base imposable</div>
                    <div class="font-display font-extrabold text-xl text-bimo-navy leading-none">{{ number_format($bilan->base_imposable,0,',','') }}<span class="font-body text-xs text-bimo-navy/40 ml-1">F</span></div>
                    <div class="font-body text-[10.5px] text-bimo-navy/40 mt-1">Après abattement 30%</div>
                </div>
                <div class="bg-white rounded-[14px] border border-bimo-red/20 p-4">
                    <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-red/70 mb-1.5">IRPP estimé</div>
                    <div class="font-display font-extrabold text-xl text-bimo-red leading-none">{{ number_format($bilan->irpp_estime,0,',','') }}<span class="font-body text-xs text-bimo-red/60 ml-1">F</span></div>
                    <div class="font-body text-[10.5px] text-bimo-red/60 mt-1">Art. 65 CGI SN</div>
                </div>
                <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-4">
                    <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-navy/50 mb-1.5">Net reversé</div>
                    <div class="font-display font-extrabold text-xl text-bimo-navy leading-none">{{ number_format($bilan->net_a_verser_total ?? $bilan->net_proprietaire_total,0,',','') }}<span class="font-body text-xs text-bimo-navy/40 ml-1">F</span></div>
                    <div class="font-body text-[10.5px] text-bimo-navy/40 mt-1">Après commissions{{ ($bilan->brs_retenu_total ?? 0) > 0 ? ' + BRS' : '' }}</div>
                </div>
            </div>

            {{-- Calcul fiscal --}}
            <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
                <div class="flex items-center justify-between px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-bimo-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
                        <span class="font-display font-bold text-sm text-bimo-navy">Calcul fiscal détaillé</span>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-body font-medium bg-bimo-navy/10 border border-bimo-navy/15 text-bimo-navy/70">Art. 58-65 CGI SN</span>
                </div>
                <div class="px-5 py-5">
                    <div class="bg-bimo-bg rounded-[10px] border border-bimo-navy/[8%] p-4 space-y-0 divide-y divide-bimo-navy/[5%]">
                        <div class="flex items-center justify-between py-2.5">
                            <span class="font-body text-sm text-bimo-navy/70">Loyers HT perçus</span>
                            <span class="font-display font-bold text-sm text-bimo-navy">{{ number_format($bilan->revenus_bruts_loyers,0,',','') }} F</span>
                        </div>
                        @if($bilan->revenus_bruts_charges > 0)
                        <div class="flex items-center justify-between py-2 pl-4">
                            <span class="font-body text-xs text-bimo-navy/50">+ Charges refacturées <span class="text-bimo-navy/30">(Art. 56 CGI SN)</span></span>
                            <span class="font-display font-semibold text-sm text-bimo-navy/70">{{ number_format($bilan->revenus_bruts_charges,0,',','') }} F</span>
                        </div>
                        @endif
                        <div class="flex items-center justify-between py-2.5">
                            <span class="font-body text-sm text-bimo-navy/70">Abattement forfaitaire 30% <span class="text-xs text-bimo-navy/30">(Art. 58 CGI SN)</span></span>
                            <span class="font-display font-bold text-sm text-bimo-gold">− {{ number_format($bilan->abattement_forfaitaire_30,0,',','') }} F</span>
                        </div>
                        <div class="flex items-center justify-between py-3 mt-1">
                            <span class="font-display font-bold text-sm text-bimo-navy">= Base imposable (70%)</span>
                            <span class="font-display font-extrabold text-base text-bimo-gold">{{ number_format($bilan->base_imposable,0,',','') }} F</span>
                        </div>
                    </div>

                    {{-- Barème IRPP --}}
                    <div class="mt-4">
                        <div class="font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40 mb-2">Barème IRPP progressif (Art. 65 CGI SN)</div>
                        @php
                            $irppTranches = $bilan->irpp_detail ?? \App\Services\FiscalService::calculerIRPPDetail((float)$bilan->base_imposable);
                            $labels = ['0 — 1 500 000 F', '1 500 001 — 4 000 000 F', '4 000 001 — 8 000 000 F', '> 8 000 000 F'];
                        @endphp
                        <div class="overflow-x-auto">
                            <table class="w-full text-xs">
                                <thead>
                                    <tr class="border-b border-bimo-navy/[5%] bg-bimo-bg">
                                        <th class="px-4 py-2.5 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40">Tranche</th>
                                        <th class="px-4 py-2.5 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40">Taux</th>
                                        <th class="px-4 py-2.5 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40">Impôt sur tranche</th>
                                        <th class="px-4 py-2.5 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40 w-24">Progression</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-bimo-navy/[5%]">
                                    @foreach($irppTranches as $i => $t)
                                    @php
                                        $active = ($t['assiette'] ?? 0) > 0;
                                        $impot  = $t['impot'] ?? 0;
                                        $maxVal = $t['max'] ?? PHP_INT_MAX;
                                        $minVal = $t['min'] ?? 0;
                                        $pct = $maxVal >= PHP_INT_MAX ? ($active ? 100 : 0)
                                             : ($minVal >= (float)$bilan->base_imposable ? 0
                                                : min(100, max(0, ((float)$bilan->base_imposable - $minVal) / ($maxVal - $minVal) * 100)));
                                    @endphp
                                    <tr class="{{ $active ? 'bg-bimo-gold/[4%]' : '' }}">
                                        <td class="px-4 py-2.5 font-body {{ $active ? 'font-semibold text-bimo-gold' : 'text-bimo-navy/30' }}">{{ $labels[$i] ?? ($minVal.' — '.$maxVal.' F') }}</td>
                                        <td class="px-4 py-2.5 font-body {{ $active ? 'text-bimo-gold' : 'text-bimo-navy/30' }}">{{ $t['taux'] }}%</td>
                                        <td class="px-4 py-2.5 font-display font-bold {{ $active ? 'text-bimo-gold' : 'text-bimo-navy/20' }}">{{ $active && $impot > 0 ? number_format($impot,0,',','').' F' : '—' }}</td>
                                        <td class="px-4 py-2.5">
                                            <div class="h-1.5 bg-bimo-navy/10 rounded-full overflow-hidden">
                                                <div class="h-full rounded-full {{ $active ? 'bg-bimo-gold' : 'bg-bimo-navy/10' }}" style="width:{{ $pct }}%"></div>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="mt-4 flex items-center justify-between py-3 px-4 bg-bimo-red/[5%] border border-bimo-red/15 rounded-[9px]">
                        <span class="font-display font-bold text-sm text-bimo-red">= IRPP estimé à déclarer</span>
                        <span class="font-display font-extrabold text-base text-bimo-red">{{ number_format($bilan->irpp_estime,0,',','') }} F</span>
                    </div>

                    <div class="mt-3 flex items-start justify-between py-2.5 border-t border-bimo-navy/[5%]">
                        <div>
                            <span class="font-body text-sm text-bimo-navy/70">CFPB estimée</span>
                            <div class="font-body text-[10px] text-bimo-navy/30 mt-0.5">Estimation indicative — assiette réelle = valeur locative cadastrale (Art. 290-291 CGI SN)</div>
                        </div>
                        <span class="font-display font-bold text-sm text-bimo-gold ml-4 flex-shrink-0">{{ number_format($bilan->cfpb_estimee,0,',','') }} F</span>
                    </div>
                </div>
            </div>

            {{-- Comparaison régimes --}}
            <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
                <div class="flex items-center justify-between px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
                    <span class="font-display font-bold text-sm text-bimo-navy">Comparaison des régimes — CGF vs IRPP</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-body font-medium bg-bimo-navy/10 border border-bimo-navy/15 text-bimo-navy/70">Art. 77-94 CGI SN</span>
                </div>
                <div class="px-5 py-5">
                    @if($regimes['regime_recommande'] === 'hors_cgf')
                    <div class="bg-bimo-gold/[6%] border border-bimo-gold/20 rounded-[10px] px-4 py-3 mb-4 font-body text-sm text-bimo-gold">
                        ⚠ <strong>CGF non applicable</strong> — Revenus bruts ({{ number_format($bilan->revenus_bruts_total,0,',','') }} F) supérieurs à 30 000 000 FCFA. Le régime réel <strong>IRPP</strong> est obligatoire (Art. 77 CGI SN).
                    </div>
                    <div class="bg-bimo-red/[5%] border border-bimo-red/15 rounded-[12px] p-5 text-center">
                        <div class="font-body font-medium text-[10px] uppercase tracking-widest text-bimo-red/70 mb-2">IRPP estimé (Art. 65 CGI SN)</div>
                        <div class="font-display font-extrabold text-3xl text-bimo-red">{{ number_format($bilan->irpp_estime,0,',','') }} F</div>
                        <div class="font-body text-xs text-bimo-red/60 mt-2">Base imposable {{ number_format($bilan->base_imposable,0,',','') }} F (70% des revenus)</div>
                    </div>
                    @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-4">
                        @php $cgfRecommande = $regimes['regime_recommande'] === 'cgf'; $irppRecommande = $regimes['regime_recommande'] === 'irpp'; @endphp
                        <div class="border-2 {{ $cgfRecommande ? 'border-bimo-gold bg-bimo-gold/[4%]' : 'border-bimo-navy/10' }} rounded-[12px] p-5 relative">
                            @if($cgfRecommande)
                            <div class="absolute -top-3 left-1/2 -translate-x-1/2 bg-bimo-gold text-bimo-navy text-[10px] font-display font-bold px-3 py-0.5 rounded-full whitespace-nowrap">✓ RECOMMANDÉ</div>
                            @endif
                            <div class="font-body font-medium text-[10px] uppercase tracking-widest {{ $cgfRecommande ? 'text-bimo-gold/70' : 'text-bimo-navy/40' }} mb-2">Régime CGF (Art. 80 CGI SN)</div>
                            <div class="font-display font-extrabold text-2xl {{ $cgfRecommande ? 'text-bimo-gold' : 'text-bimo-navy' }}">{{ number_format($cgfData['montant'],0,',','') }} F</div>
                            <div class="font-body text-xs text-bimo-navy/50 mt-2 leading-relaxed">
                                Taux : <strong>{{ $cgfData['taux_applique'] }}%</strong><br>
                                Tranche : {{ $cgfData['tranche_label'] }}<br>
                                <span class="text-bimo-navy/30">Calcul sur revenus bruts (sans abattement)</span>
                            </div>
                        </div>
                        <div class="border-2 {{ $irppRecommande ? 'border-bimo-gold bg-bimo-gold/[4%]' : 'border-bimo-navy/10' }} rounded-[12px] p-5 relative">
                            @if($irppRecommande)
                            <div class="absolute -top-3 left-1/2 -translate-x-1/2 bg-bimo-gold text-bimo-navy text-[10px] font-display font-bold px-3 py-0.5 rounded-full whitespace-nowrap">✓ RECOMMANDÉ</div>
                            @endif
                            <div class="font-body font-medium text-[10px] uppercase tracking-widest {{ $irppRecommande ? 'text-bimo-gold/70' : 'text-bimo-navy/40' }} mb-2">Régime IRPP (Art. 65 CGI SN)</div>
                            <div class="font-display font-extrabold text-2xl {{ $irppRecommande ? 'text-bimo-gold' : 'text-bimo-navy' }}">{{ number_format($bilan->irpp_estime,0,',','') }} F</div>
                            <div class="font-body text-xs text-bimo-navy/50 mt-2 leading-relaxed">
                                Base imposable : <strong>{{ number_format($bilan->base_imposable,0,',','') }} F</strong><br>
                                Après abattement 30% (Art. 68 §c)<br>
                                <span class="text-bimo-navy/30">Calcul sur 70% des revenus bruts</span>
                            </div>
                        </div>
                    </div>
                    @if($regimes['economie_potentielle'] > 0)
                    <div class="flex items-start gap-2 bg-bimo-gold/[6%] border border-bimo-gold/20 rounded-[10px] px-4 py-3">
                        <svg class="w-4 h-4 text-bimo-gold flex-shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                        <p class="font-body text-sm text-bimo-gold">
                            En optant pour le régime <strong>{{ strtoupper($regimes['regime_recommande']) }}</strong>,
                            ce propriétaire économise <strong>{{ number_format($regimes['economie_potentielle'],0,',','') }} FCFA</strong> sur l'année {{ $annee }}.
                        </p>
                    </div>
                    @endif
                    @endif
                    <p class="font-body text-xs text-bimo-navy/30 mt-3 pt-3 border-t border-bimo-navy/[5%] leading-relaxed">
                        Ces estimations sont indicatives. La déclaration CGF est prévisionnelle et doit être déposée avant le 30 avril. <strong>Consultez la DGID ou un comptable agréé avant toute déclaration officielle.</strong>
                    </p>
                </div>
            </div>

            {{-- TVA & BRS --}}
            @if($bilan->tva_loyer_collectee > 0 || ($bilan->tva_charges_total ?? 0) > 0 || $bilan->brs_retenu_total > 0)
            <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
                <div class="px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
                    <span class="font-display font-bold text-sm text-bimo-navy">Obligations TVA & BRS</span>
                </div>
                <div class="px-5 py-5 grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @if($bilan->tva_loyer_collectee > 0)
                    <div class="bg-bimo-gold/[6%] border border-bimo-gold/20 rounded-[10px] p-4">
                        <div class="font-body font-medium text-[10px] uppercase tracking-widest text-bimo-gold/70 mb-2">TVA loyer collectée</div>
                        <div class="font-display font-extrabold text-2xl text-bimo-gold">{{ number_format($bilan->tva_loyer_collectee,0,',','') }} F</div>
                        <div class="font-body text-xs text-bimo-gold/60 mt-2 leading-relaxed">TVA 18% sur loyers commerciaux/meublés.<br><strong>À reverser à la DGI</strong> par le propriétaire.</div>
                    </div>
                    @endif
                    @if(($bilan->tva_charges_total ?? 0) > 0)
                    <div class="bg-bimo-gold/[6%] border border-bimo-gold/20 rounded-[10px] p-4">
                        <div class="font-body font-medium text-[10px] uppercase tracking-widests text-bimo-gold/70 mb-2">TVA charges forfaitaires (Art. 357)</div>
                        <div class="font-display font-extrabold text-2xl text-bimo-gold">{{ number_format($bilan->tva_charges_total,0,',','') }} F</div>
                        <div class="font-body text-xs text-bimo-gold/60 mt-2 leading-relaxed">TVA 18% sur charges forfait bail commercial.<br><strong>À reverser à la DGI.</strong></div>
                    </div>
                    @endif
                    @if($bilan->brs_retenu_total > 0)
                    <div class="bg-bimo-red/[5%] border border-bimo-red/15 rounded-[10px] p-4">
                        <div class="font-body font-medium text-[10px] uppercase tracking-widest text-bimo-red/70 mb-2">BRS retenu</div>
                        <div class="font-display font-extrabold text-2xl text-bimo-red">{{ number_format($bilan->brs_retenu_total,0,',','') }} F</div>
                        <div class="font-body text-xs text-bimo-red/60 mt-2 leading-relaxed">Retenu par les locataires entreprises.<br><strong>Déjà versé à la DGI</strong> par les locataires.</div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- Commissions --}}
            <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
                <div class="px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
                    <span class="font-display font-bold text-sm text-bimo-navy">Commissions agence {{ $annee }}</span>
                </div>
                <div class="px-5 py-5 grid grid-cols-3 gap-3">
                    <div class="text-center p-4 bg-bimo-gold/[6%] border border-bimo-gold/20 rounded-[10px]">
                        <div class="font-body font-medium text-[10px] uppercase tracking-widest text-bimo-gold/70 mb-2">Commission HT</div>
                        <div class="font-display font-extrabold text-xl text-bimo-gold">{{ number_format($bilan->commissions_agence_ht,0,',','') }} F</div>
                    </div>
                    <div class="text-center p-4 bg-bimo-bg border border-bimo-navy/[8%] rounded-[10px]">
                        <div class="font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40 mb-2">TVA commissions</div>
                        <div class="font-display font-extrabold text-xl text-bimo-navy/60">{{ number_format($bilan->tva_commissions,0,',','') }} F</div>
                    </div>
                    <div class="text-center p-4 bg-bimo-navy/[4%] border border-bimo-navy/10 rounded-[10px]">
                        <div class="font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/50 mb-2">Net versé propriétaire{{ ($bilan->brs_retenu_total ?? 0) > 0 ? ' (après BRS)' : '' }}</div>
                        <div class="font-display font-extrabold text-xl text-bimo-navy">{{ number_format($bilan->net_a_verser_total ?? $bilan->net_proprietaire_total,0,',','') }} F</div>
                    </div>
                </div>
            </div>

            {{-- Paiements --}}
            <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
                <div class="px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
                    <span class="font-display font-bold text-sm text-bimo-navy">Paiements {{ $annee }} ({{ $paiements->count() }})</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-bimo-navy/[5%] bg-bimo-bg">
                                <th class="px-4 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40">Période</th>
                                <th class="px-4 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40">Bien</th>
                                <th class="px-4 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40">Locataire</th>
                                <th class="px-4 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40">Type bail</th>
                                <th class="px-4 py-3 text-right font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40">Loyer HT</th>
                                <th class="px-4 py-3 text-right font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40">TVA loyer</th>
                                <th class="px-4 py-3 text-right font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40">BRS</th>
                                <th class="px-4 py-3 text-right font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40">Net proprio</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-bimo-navy/[5%]">
                            @forelse($paiements as $p)
                            <tr class="hover:bg-bimo-bg transition-colors duration-100">
                                <td class="px-4 py-3 font-body text-xs text-bimo-navy/70">{{ \Carbon\Carbon::parse($p->periode)->translatedFormat('M Y') }}</td>
                                <td class="px-4 py-3">
                                    <div class="font-body font-semibold text-sm text-bimo-navy">{{ $p->bien_reference ?? '—' }}</div>
                                    <div class="font-body text-[10px] text-bimo-navy/30">{{ ucfirst($p->type_bail ?? '—') }}{{ ($p->type_bail === 'habitation' && $p->bien_meuble) ? ' meublée' : '' }}</div>
                                </td>
                                <td class="px-4 py-3 font-body text-xs text-bimo-navy/60">{{ $p->contrat?->locataire?->name ?? '—' }}</td>
                                <td class="px-4 py-3">
                                    @if(($p->tva_loyer ?? 0) > 0)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-body font-semibold bg-bimo-gold/10 border border-bimo-gold/20 text-bimo-gold">TVA 18%</span>
                                    @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-body font-medium bg-bimo-navy/[5%] text-bimo-navy/40">Exonéré</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right font-display font-semibold text-sm text-bimo-navy/70">{{ number_format($p->loyer_ht ?? $p->loyer_nu ?? 0,0,',','') }} F</td>
                                <td class="px-4 py-3 text-right font-display font-semibold text-sm {{ ($p->tva_loyer ?? 0) > 0 ? 'text-bimo-gold' : 'text-bimo-navy/20' }}">{{ ($p->tva_loyer ?? 0) > 0 ? number_format($p->tva_loyer,0,',','').' F' : '—' }}</td>
                                <td class="px-4 py-3 text-right font-display font-semibold text-sm {{ ($p->brs_amount ?? 0) > 0 ? 'text-bimo-red' : 'text-bimo-navy/20' }}">{{ ($p->brs_amount ?? 0) > 0 ? number_format($p->brs_amount,0,',','').' F' : '—' }}</td>
                                <td class="px-4 py-3 text-right font-display font-bold text-sm text-bimo-gold">{{ number_format($p->net_a_verser_proprietaire ?? $p->net_proprietaire ?? 0,0,',','') }} F</td>
                            </tr>
                            @empty
                            <tr><td colspan="8" class="px-4 py-8 text-center font-body text-sm text-bimo-navy/30">Aucun paiement enregistré pour {{ $annee }}</td></tr>
                            @endforelse
                        </tbody>
                        @if($paiements->count() > 0)
                        <tfoot>
                            <tr class="bg-bimo-gold/[6%] border-t-2 border-bimo-gold/30">
                                <td colspan="4" class="px-4 py-3 font-display font-bold text-xs text-bimo-navy/70">TOTAL {{ $annee }}</td>
                                <td class="px-4 py-3 text-right font-display font-bold text-sm text-bimo-navy/70">{{ number_format($paiements->sum(fn($p) => $p->loyer_ht ?? $p->loyer_nu ?? 0),0,',','') }} F</td>
                                <td class="px-4 py-3 text-right font-display font-bold text-sm text-bimo-gold">{{ number_format($paiements->sum('tva_loyer'),0,',','') }} F</td>
                                <td class="px-4 py-3 text-right font-display font-bold text-sm text-bimo-red">{{ number_format($paiements->sum('brs_amount'),0,',','') }} F</td>
                                <td class="px-4 py-3 text-right font-display font-extrabold text-sm text-bimo-gold">{{ number_format($paiements->sum(fn($p) => $p->net_a_verser_proprietaire ?? $p->net_proprietaire ?? 0),0,',','') }} F</td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>

            <div class="flex items-start gap-2 bg-bimo-gold/[6%] border border-bimo-gold/20 rounded-[10px] px-4 py-3">
                <svg class="w-4 h-4 text-bimo-gold flex-shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                <p class="font-body text-xs text-bimo-gold/80 leading-relaxed">⚠ <strong>Estimation fiscale</strong> — Ce bilan est calculé sur la base des paiements enregistrés dans Renlio. L'IRPP est une estimation selon le barème Art. 65 CGI SN. La CFPB est une estimation indicative — assiette réelle = valeur locative cadastrale fixée par la DGID (Art. 290-291 CGI SN). <strong>Consultez un comptable agréé ou la DGI avant toute déclaration officielle.</strong></p>
            </div>

        </div>

        {{-- Sidebar --}}
        <div class="lg:sticky lg:top-6 space-y-4">

            {{-- Carte proprio --}}
            <div class="bg-bimo-navy rounded-[14px] overflow-hidden">
                <div class="p-5 text-center">
                    <div class="w-14 h-14 rounded-[12px] bg-bimo-gold flex items-center justify-center font-display font-extrabold text-xl text-bimo-navy mx-auto mb-3">{{ mb_strtoupper(mb_substr($proprietaire->name,0,2)) }}</div>
                    <div class="font-display font-bold text-base text-white">{{ $proprietaire->name }}</div>
                    <div class="font-body text-xs text-white/40 mt-1">{{ $proprietaire->email }}</div>
                </div>
                <div class="border-t border-white/[7%] px-4 py-3 space-y-0 divide-y divide-white/[5%]">
                    @foreach([['NINEA',$proprietaire->proprietaire?->ninea ?? '—'],['Tél',$proprietaire->telephone ?? '—'],['Biens',$bilan->nb_biens_geres],['Paiements',$bilan->nb_paiements]] as [$lbl,$val])
                    <div class="flex items-center justify-between py-2">
                        <span class="font-body text-xs text-white/40">{{ $lbl }}</span>
                        <span class="font-body font-semibold text-xs text-white/70">{{ $val }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Résumé fiscal --}}
            <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
                <div class="px-4 py-3 border-b border-bimo-navy/[5%] bg-bimo-bg2">
                    <span class="font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40">Résumé fiscal {{ $annee }}</span>
                </div>
                <div class="px-4 py-3 space-y-0 divide-y divide-bimo-navy/[5%]">
                    @php
                        $rows = [
                            ['Revenus bruts',$bilan->revenus_bruts_loyers,'text-bimo-navy'],
                            ['Abattement 30%',-$bilan->abattement_forfaitaire_30,'text-bimo-gold'],
                            ['Base imposable',$bilan->base_imposable,'text-bimo-navy'],
                            ['IRPP estimé',$bilan->irpp_estime,'text-bimo-red'],
                            ['CFPB estimée',$bilan->cfpb_estimee,'text-bimo-gold'],
                        ];
                        if($bilan->tva_loyer_collectee > 0) $rows[] = ['TVA à reverser',$bilan->tva_loyer_collectee,'text-bimo-gold'];
                        if($bilan->brs_retenu_total > 0) $rows[] = ['BRS retenu',$bilan->brs_retenu_total,'text-bimo-red'];
                    @endphp
                    @foreach($rows as [$lbl,$val,$cls])
                    <div class="flex items-center justify-between py-2.5">
                        <span class="font-body text-xs text-bimo-navy/50">{{ $lbl }}</span>
                        <span class="font-display font-bold text-xs {{ $cls }}">{{ ($val < 0 ? '−' : '') }}{{ number_format(abs($val),0,',','') }} F</span>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Actions --}}
            <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
                <div class="px-4 py-3 border-b border-bimo-navy/[5%] bg-bimo-bg2">
                    <span class="font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40">Actions</span>
                </div>
                <div class="px-4 py-3 space-y-2">
                    @foreach([
                        [route('admin.users.show',$proprietaire),'Fiche propriétaire','<path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/>'],
                        [route('admin.bilans-fiscaux.index'),'Tous les bilans','<polyline points="15 18 9 12 15 6"/>'],
                    ] as [$href,$lbl,$icon])
                    <a href="{{ $href }}" class="flex items-center gap-2 px-3 py-2.5 border border-bimo-navy/15 rounded-[8px] font-body text-sm text-bimo-navy/60 hover:border-bimo-gold hover:text-bimo-navy transition-all duration-150">
                        <svg class="w-3.5 h-3.5 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">{!! $icon !!}</svg>
                        {{ $lbl }}
                    </a>
                    @endforeach
                    @foreach($anneesDisponibles->reject(fn($a) => $a == $annee)->take(3) as $autreAnnee)
                    <a href="{{ route('admin.bilans-fiscaux.show', [$proprietaire, 'annee' => $autreAnnee]) }}" class="flex items-center gap-2 px-3 py-2.5 border border-bimo-navy/15 rounded-[8px] font-body text-sm text-bimo-navy/60 hover:border-bimo-gold hover:text-bimo-navy transition-all duration-150">
                        <svg class="w-3.5 h-3.5 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/></svg>
                        Bilan {{ $autreAnnee }}
                    </a>
                    @endforeach
                </div>
            </div>

        </div>
    </div>

</div>
@endsection
