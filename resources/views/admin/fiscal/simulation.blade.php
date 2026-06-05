@extends('layouts.app')
@section('header', 'Simulation fiscale')

@section('content')
<div class="space-y-4 md:space-y-6">

    <div class="flex items-center gap-3">
        <a href="{{ route('admin.fiscal.dashboard') }}" class="text-bimo-text/40 hover:text-bimo-text transition-colors duration-150">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
        </a>
        <div>
            <h1 class="font-display font-extrabold text-xl text-bimo-text tracking-tight leading-tight">Simulation fiscale</h1>
            <p class="font-body text-sm text-bimo-text/50 mt-0.5">Estimez les obligations fiscales avant de signer un contrat</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Formulaire --}}
        <div>
            <form method="GET" class="space-y-4">
                <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-5 space-y-4">
                    <h2 class="font-display font-bold text-sm text-bimo-text">Paramètres du contrat</h2>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="block font-body font-medium text-sm text-bimo-text">Loyer HT <span class="text-bimo-red">*</span></label>
                            <input name="loyer_ht" type="number" min="0" step="1000" value="{{ request('loyer_ht') }}"
                                   placeholder="ex: 200000"
                                   class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text placeholder:text-bimo-text/35 focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                        </div>
                        <div class="space-y-1.5">
                            <label class="block font-body font-medium text-sm text-bimo-text">Charges / mois</label>
                            <input name="charges" type="number" min="0" step="1000" value="{{ request('charges', 0) }}"
                                   class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="block font-body font-medium text-sm text-bimo-text">Mois occupés / an</label>
                            <select name="mois_occupes" class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150 appearance-none cursor-pointer">
                                @foreach(range(1, 12) as $m)
                                <option value="{{ $m }}" {{ request('mois_occupes', 12) == $m ? 'selected' : '' }}>{{ $m }} mois</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label class="block font-body font-medium text-sm text-bimo-text">Type de bail</label>
                            <select name="type_bail" class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150 appearance-none cursor-pointer">
                                <option value="habitation" {{ request('type_bail') === 'habitation' ? 'selected' : '' }}>Habitation</option>
                                <option value="commercial" {{ request('type_bail') === 'commercial' ? 'selected' : '' }}>Commercial</option>
                                <option value="mixte" {{ request('type_bail') === 'mixte' ? 'selected' : '' }}>Mixte</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="block font-body font-medium text-sm text-bimo-text">Commission agence (%)</label>
                            <input name="taux_commission" type="number" min="0" max="100" step="0.5" value="{{ request('taux_commission', 10) }}"
                                   class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                        </div>
                        <div class="space-y-1.5">
                            <label class="block font-body font-medium text-sm text-bimo-text">Type de propriétaire</label>
                            <select name="est_personne_morale" class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150 appearance-none cursor-pointer">
                                <option value="0" {{ request('est_personne_morale', '0') === '0' ? 'selected' : '' }}>Personne physique (BRS 5%)</option>
                                <option value="1" {{ request('est_personne_morale') === '1' ? 'selected' : '' }}>Personne morale (IS, pas BRS)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <button type="submit"
                        class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-[10px] bg-[var(--ac)] text-white font-display font-bold text-sm hover:opacity-90 transition-opacity duration-150">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
                    Simuler
                </button>
            </form>
        </div>

        {{-- Résultats --}}
        @if($resultat)
        <div class="space-y-4">

            {{-- Recommandation régime --}}
            @php $reg = $resultat['regimes']; @endphp
            <div class="rounded-[14px] border-2 p-5 {{ $reg['regime_recommande'] === 'cgf' ? 'bg-bimo-gold/[5%] border-bimo-gold/30' : 'bg-bimo-navy/[3%] border-bimo-navy/15' }}">
                <p class="font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40 mb-2">Régime recommandé</p>
                <p class="font-display font-extrabold text-xl {{ $reg['regime_recommande'] === 'cgf' ? 'text-bimo-gold' : 'text-bimo-text' }}">
                    {{ $reg['regime_recommande'] === 'cgf' ? 'CGF — Contribution Globale Foncière' : 'IRPP — Impôt sur le Revenu' }}
                </p>
                @if($reg['economie_potentielle'] > 0)
                <p class="font-body text-sm text-bimo-text/60 mt-1">Économie potentielle : <span class="font-semibold text-bimo-gold">{{ number_format($reg['economie_potentielle'], 0, ',', ' ') }} FCFA</span></p>
                @endif
            </div>

            {{-- KPIs --}}
            <div class="grid grid-cols-2 gap-3">
                <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-4">
                    <p class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-text/50 mb-1.5">Revenus annuels</p>
                    <p class="font-display font-extrabold text-lg text-bimo-text">{{ number_format($resultat['revenus'], 0, ',', ' ') }} F</p>
                </div>
                <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-4">
                    <p class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-text/50 mb-1.5">Base imposable (70%)</p>
                    <p class="font-display font-extrabold text-lg text-bimo-text">{{ number_format($resultat['base_imposable'], 0, ',', ' ') }} F</p>
                </div>
                <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-4">
                    <p class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-text/50 mb-1.5">IRPP estimé</p>
                    <p class="font-display font-extrabold text-lg text-bimo-text">{{ number_format($resultat['irpp'], 0, ',', ' ') }} F</p>
                </div>
                <div class="bg-bimo-gold/[8%] rounded-[14px] border border-bimo-gold/25 p-4">
                    <p class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-gold/70 mb-1.5">CGF {{ $resultat['cgf']['applicable'] ? '' : '(> 30M)' }}</p>
                    <p class="font-display font-extrabold text-lg text-bimo-gold">{{ $resultat['cgf']['applicable'] ? number_format($resultat['cgf']['montant'], 0, ',', ' ').' F' : 'N/A' }}</p>
                </div>
                <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-4">
                    <p class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-text/50 mb-1.5">BRS à retenir</p>
                    <p class="font-display font-extrabold text-lg text-bimo-text">{{ number_format($resultat['brs_annuel'], 0, ',', ' ') }} F</p>
                    <p class="font-body text-[10px] text-bimo-text/40 mt-1">5% × net proprio</p>
                </div>
                <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-4">
                    <p class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-text/50 mb-1.5">TVA annuelle</p>
                    <p class="font-display font-extrabold text-lg text-bimo-text">{{ $resultat['tva_applicable'] ? number_format($resultat['tva_annuelle'], 0, ',', ' ').' F' : 'Non applicable' }}</p>
                    <p class="font-body text-[10px] text-bimo-text/40 mt-1">{{ $resultat['tva_applicable'] ? 'Bail commercial 18%' : 'Bail habitation exonéré' }}</p>
                </div>
            </div>

            {{-- Barème IRPP --}}
            <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
                <div class="px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
                    <span class="font-display font-bold text-sm text-bimo-text">Barème IRPP progressif</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-bimo-navy/[5%] bg-bimo-bg">
                                <th class="px-4 py-2 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Tranche</th>
                                <th class="px-4 py-2 text-center font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Taux</th>
                                <th class="px-4 py-2 text-right font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Impôt</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-bimo-navy/[5%]">
                            @foreach($resultat['irpp_detail'] as $tranche)
                            @if($tranche['assiette'] > 0)
                            <tr>
                                <td class="px-4 py-2 font-body text-xs text-bimo-text/60">{{ number_format($tranche['min'], 0, ',', ' ') }} → {{ $tranche['max'] ? number_format($tranche['max'], 0, ',', ' ') : '∞' }} F</td>
                                <td class="px-4 py-2 text-center font-body font-medium text-xs text-bimo-text">{{ $tranche['taux'] }}%</td>
                                <td class="px-4 py-2 text-right font-display font-semibold text-xs text-bimo-text">{{ number_format($tranche['impot'], 0, ',', ' ') }} F</td>
                            </tr>
                            @endif
                            @endforeach
                            <tr class="bg-bimo-navy/[3%]">
                                <td colspan="2" class="px-4 py-2 font-display font-bold text-sm text-bimo-text">Total IRPP</td>
                                <td class="px-4 py-2 text-right font-display font-extrabold text-sm text-bimo-text">{{ number_format($resultat['irpp'], 0, ',', ' ') }} F</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <p class="font-body text-xs text-bimo-text/30 text-center">Simulation indicative — À valider avec un expert-comptable. Références : CGI Sénégal Art. 65-68 (IRPP), Art. 201 (BRS), Art. 369 (TVA).</p>
        </div>
        @else
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-12 flex flex-col items-center justify-center text-center">
            <svg class="w-12 h-12 text-bimo-text/15 mb-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
            <p class="font-display font-bold text-base text-bimo-text/30">Remplissez le formulaire</p>
            <p class="font-body text-sm text-bimo-text/20 mt-1">Le résultat de simulation apparaîtra ici</p>
        </div>
        @endif

    </div>

</div>
@endsection
