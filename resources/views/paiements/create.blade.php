@extends('layouts.app')
@section('header', 'Paiements › Nouveau')

@section('content')

{{-- Breadcrumb --}}
<div class="flex items-center gap-2 font-body text-sm text-bimo-navy/40 mb-5">
    <a href="{{ route('admin.paiements.index') }}" class="hover:text-bimo-navy transition-colors duration-150">Paiements</a>
    <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
    <span class="text-bimo-navy font-medium">Enregistrer un paiement</span>
</div>

<div class="mb-5">
    <h1 class="font-display font-extrabold text-xl md:text-2xl text-bimo-navy tracking-tight">Enregistrer un paiement</h1>
    <p class="font-body text-sm text-bimo-navy/50 mt-1">La commission et le net propriétaire sont calculés automatiquement.</p>
</div>

<form method="POST" action="{{ route('admin.paiements.store') }}" id="form-paiement">
@csrf

<div class="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-5 items-start">

    {{-- COLONNE GAUCHE --}}
    <div class="space-y-4">

        {{-- Contrat --}}
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
            <div class="flex items-center gap-3 px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
                <div class="w-8 h-8 rounded-[8px] bg-bimo-gold/15 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-bimo-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                </div>
                <span class="font-display font-bold text-sm text-bimo-navy">Contrat</span>
            </div>
            <div class="px-5 py-5 space-y-3">

                <div class="space-y-2">
                    <label class="block font-body font-medium text-sm text-bimo-navy">Contrat concerné <span class="text-bimo-red">*</span></label>
                    {{-- Recherche --}}
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-bimo-navy/30 pointer-events-none"
                             viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>
                        <input type="text" id="contrat-search"
                               placeholder="Filtrer par locataire, bien ou référence…"
                               autocomplete="off"
                               oninput="filtrerContrats(this.value)"
                               class="w-full pl-9 pr-3 py-2.5 bg-bimo-bg border border-bimo-navy/15 rounded-[9px]
                                      font-body text-sm text-bimo-navy placeholder:text-bimo-navy/30
                                      focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15
                                      transition-all duration-150">
                    </div>
                    <select name="contrat_id" id="contrat_id" onchange="chargerContrat(this.value)"
                            class="w-full px-4 py-3 rounded-[10px] bg-white border font-body text-sm text-bimo-navy
                                   focus:outline-none focus:ring-2 transition-all duration-150 cursor-pointer
                                   @error('contrat_id') border-bimo-red focus:border-bimo-red focus:ring-bimo-red/15
                                   @else border-bimo-navy/20 focus:border-bimo-gold focus:ring-bimo-gold/15 @enderror">
                        <option value="">— Sélectionner un contrat actif —</option>
                        @foreach($contrats as $c)
                        @php
                            $locProfile   = $c->locataire?->locataire;
                            $estEntreprise = (bool) ($locProfile?->est_entreprise ?? in_array($c->type_bail ?? '', ['commercial','mixte']));
                            $tauxBrs      = $c->taux_brs_manuel ?? $locProfile?->taux_brs_override ?? 15;
                            $brsApplicable = $estEntreprise ? 1 : 0;
                            $loyerAssujetti = $c->loyer_assujetti_tva ?? (in_array($c->type_bail ?? '', ['commercial','mixte']) ? 1 : 0);
                            $tauxTva = $c->taux_tva_loyer ?? 18;
                            $locataireId = $c->locataire_id ?? 0;
                        @endphp
                        <option value="{{ $c->id }}"
                                data-loyer-nu="{{ $c->loyer_nu }}"
                                data-charges="{{ $c->charges_mensuelles ?? 0 }}"
                                data-tom="{{ $c->tom_amount ?? 0 }}"
                                data-loyer-total="{{ $c->loyer_contractuel }}"
                                data-taux-comm="{{ $c->bien?->taux_commission ?? 10 }}"
                                data-bien="{{ $c->bien?->reference }} — {{ $c->bien?->adresse }}"
                                data-locataire="{{ $c->locataire?->name }}"
                                data-ref="{{ $c->reference_bail ?? 'BAIL-'.$c->id }}"
                                data-brs-applicable="{{ $brsApplicable }}"
                                data-taux-brs="{{ $tauxBrs }}"
                                data-loyer-assujetti="{{ $loyerAssujetti }}"
                                data-taux-tva="{{ $tauxTva }}"
                                data-locataire-id="{{ $locataireId }}"
                                {{ old('contrat_id', $contrat?->id) == $c->id ? 'selected':'' }}>
                            {{ $c->reference_bail ?? 'BAIL-'.$c->id }}
                            — {{ $c->bien?->reference }}
                            — {{ $c->locataire?->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('contrat_id')<p class="mt-1 font-body text-xs text-bimo-red">{{ $message }}</p>@enderror
                </div>

                {{-- Infos contrat dynamiques --}}
                <div id="contrat-details" style="display:none" class="space-y-3">
                    <div class="bg-bimo-bg border border-bimo-navy/10 rounded-[10px] divide-y divide-bimo-navy/[5%]">
                        <div class="flex items-center justify-between px-3 py-2.5">
                            <span class="font-body text-xs text-bimo-navy/50">Bien</span>
                            <span class="font-body font-medium text-xs text-bimo-navy" id="info-bien">—</span>
                        </div>
                        <div class="flex items-center justify-between px-3 py-2.5">
                            <span class="font-body text-xs text-bimo-navy/50">Locataire</span>
                            <span class="font-body font-medium text-xs text-bimo-navy" id="info-locataire">—</span>
                        </div>
                        <div class="flex items-center justify-between px-3 py-2.5">
                            <span class="font-body text-xs text-bimo-navy/50">Loyer contractuel</span>
                            <span class="font-display font-bold text-sm text-bimo-gold" id="info-loyer">—</span>
                        </div>
                    </div>

                    {{-- Badge BRS --}}
                    <div id="brs-badge" style="display:none"
                         class="flex items-start gap-3 bg-bimo-red/[4%] border-l-4 border-bimo-red border border-bimo-red/20 rounded-r-[10px] px-4 py-3">
                        <svg class="w-4 h-4 text-bimo-red flex-shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/></svg>
                        <div class="font-body text-xs text-bimo-red leading-relaxed">
                            <strong>BRS applicable — Locataire entreprise (Art. 201 CGI SN)</strong><br>
                            Taux : <strong id="brs-taux-display">5%</strong> —
                            BRS estimé : <strong id="brs-montant-display">— F</strong> —
                            Net proprio : <strong id="brs-net-display">— F</strong>
                            <br><a href="#" id="brs-profil-link" class="font-semibold underline mt-1 inline-block">
                                → Modifier le profil du locataire
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Période & Date --}}
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
            <div class="flex items-center gap-3 px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
                <div class="w-8 h-8 rounded-[8px] bg-bimo-navy/10 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-bimo-navy/50" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                </div>
                <span class="font-display font-bold text-sm text-bimo-navy">Période & Date</span>
            </div>
            <div class="px-5 py-5 grid grid-cols-2 gap-3">
                <div class="space-y-1.5">
                    <label class="block font-body font-medium text-sm text-bimo-navy">Période concernée <span class="text-bimo-red">*</span></label>
                    <input type="month" name="periode" id="periode"
                           value="{{ old('periode', now()->format('Y-m')) }}"
                           class="w-full px-4 py-3 rounded-[10px] bg-white border font-body text-sm text-bimo-navy
                                  focus:outline-none focus:ring-2 transition-all duration-150
                                  @error('periode') border-bimo-red focus:border-bimo-red focus:ring-bimo-red/15
                                  @else border-bimo-navy/20 focus:border-bimo-gold focus:ring-bimo-gold/15 @enderror">
                    <p class="font-body text-[11px] text-bimo-navy/30">Mois du loyer, pas la date de paiement</p>
                    @error('periode')<p class="font-body text-xs text-bimo-red">{{ $message }}</p>@enderror
                </div>
                <div class="space-y-1.5">
                    <label class="block font-body font-medium text-sm text-bimo-navy">Date de paiement <span class="text-bimo-red">*</span></label>
                    <input type="date" name="date_paiement"
                           value="{{ old('date_paiement', $datePaiement) }}"
                           class="w-full px-4 py-3 rounded-[10px] bg-white border font-body text-sm text-bimo-navy
                                  focus:outline-none focus:ring-2 transition-all duration-150
                                  @error('date_paiement') border-bimo-red focus:border-bimo-red focus:ring-bimo-red/15
                                  @else border-bimo-navy/20 focus:border-bimo-gold focus:ring-bimo-gold/15 @enderror">
                    @error('date_paiement')<p class="font-body text-xs text-bimo-red">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        {{-- Montant --}}
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
            <div class="flex items-center gap-3 px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
                <div class="w-8 h-8 rounded-[8px] bg-bimo-gold/15 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-bimo-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
                </div>
                <span class="font-display font-bold text-sm text-bimo-navy">Montant</span>
            </div>
            <div class="px-5 py-5 space-y-4">
                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1.5">
                        <label class="block font-body font-medium text-sm text-bimo-navy">Montant encaissé (FCFA) <span class="text-bimo-red">*</span></label>
                        <input type="number" name="montant_encaisse" id="montant_encaisse"
                               value="{{ old('montant_encaisse') }}" min="0" step="500"
                               oninput="verifierMontant()"
                               class="w-full px-4 py-3 rounded-[10px] bg-white border font-body text-sm text-bimo-navy
                                      focus:outline-none focus:ring-2 transition-all duration-150
                                      @error('montant_encaisse') border-bimo-red focus:border-bimo-red focus:ring-bimo-red/15
                                      @else border-bimo-navy/20 focus:border-bimo-gold focus:ring-bimo-gold/15 @enderror">
                        <p id="montant-ok" style="display:none" class="font-body text-[11px] text-bimo-gold mt-1">✓ Correspond au loyer contractuel</p>
                        <p id="montant-diff" style="display:none" class="font-body text-[11px] text-amber-600 mt-1">⚠ Différent du loyer contractuel</p>
                        @error('montant_encaisse')<p class="font-body text-xs text-bimo-red">{{ $message }}</p>@enderror
                    </div>
                    <div class="space-y-1.5">
                        <label class="block font-body font-medium text-sm text-bimo-navy">Mode de paiement <span class="text-bimo-red">*</span></label>
                        <select name="mode_paiement"
                                class="w-full px-4 py-3 rounded-[10px] bg-white border font-body text-sm text-bimo-navy cursor-pointer
                                       focus:outline-none focus:ring-2 transition-all duration-150
                                       @error('mode_paiement') border-bimo-red focus:border-bimo-red focus:ring-bimo-red/15
                                       @else border-bimo-navy/20 focus:border-bimo-gold focus:ring-bimo-gold/15 @enderror">
                            <option value="">— Choisir —</option>
                            @foreach($modesPaiement as $val => $label)
                            <option value="{{ $val }}" {{ old('mode_paiement') === $val ? 'selected':'' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('mode_paiement')<p class="font-body text-xs text-bimo-red">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1.5">
                        <label class="block font-body font-medium text-sm text-bimo-navy">
                            Caution perçue <span class="font-normal text-bimo-navy/40 text-xs ml-1">(premier mois)</span>
                        </label>
                        <input type="number" name="caution_percue"
                               value="{{ old('caution_percue', 0) }}" min="0" step="500"
                               class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-navy
                                      focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                        <p class="font-body text-[11px] text-bimo-navy/30">Saisir uniquement au premier paiement</p>
                    </div>
                    <div class="space-y-1.5">
                        <label class="block font-body font-medium text-sm text-bimo-navy">
                            Notes <span class="font-normal text-bimo-navy/40 text-xs ml-1">(optionnel)</span>
                        </label>
                        <input type="text" name="notes" value="{{ old('notes') }}" placeholder="Ex: Paiement partiel…"
                               class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-navy
                                      placeholder:text-bimo-navy/30 focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                    </div>
                </div>
            </div>
        </div>

        {{-- Note + Submit --}}
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
            <div class="px-5 py-4 border-b border-bimo-navy/[5%]">
                <p class="font-body text-xs text-bimo-navy/50">
                    La commission, TVA et net propriétaire sont calculés automatiquement à partir du taux de commission du bien.
                </p>
            </div>
            <div class="sticky bottom-0 flex items-center justify-end gap-3 px-5 py-4
                        bg-white/95 backdrop-blur-sm border-t border-bimo-navy/[5%]">
                <a href="{{ route('admin.paiements.index') }}"
                   class="px-5 py-2.5 border border-bimo-navy/15 rounded-[10px]
                          font-body text-sm text-bimo-navy/60 hover:text-bimo-navy hover:border-bimo-navy/30 transition-all duration-150">
                    Annuler
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-[var(--ac)] text-white
                               font-display font-bold text-sm rounded-[10px]
                               hover:opacity-90 transition-opacity duration-150">
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    Enregistrer le paiement
                </button>
            </div>
        </div>

    </div>{{-- fin colonne gauche --}}

    {{-- COLONNE DROITE : DÉCOMPTE FISCAL --}}
    <div class="lg:sticky lg:top-6">
        <div class="bg-bimo-navy rounded-[14px] overflow-hidden">
            <div class="px-5 py-4 border-b border-white/[7%]">
                <div class="font-display font-bold text-sm text-white">Décompte fiscal</div>
            </div>
            <div class="px-5 py-4">

                <div id="recap-vide" class="text-center py-8 font-body text-xs text-white/25">
                    Sélectionnez un contrat pour voir le décompte
                </div>

                <div id="recap-content" style="display:none" class="space-y-0">

                    <div class="font-body font-medium text-[9px] uppercase tracking-widest text-white/25 mb-2">Loyer</div>
                    @foreach([
                        ['rp-loyer-nu',    'Loyer nu',    'text-white'],
                        ['rp-charges',     'Charges',     'text-white'],
                        ['rp-tom',         'TOM',         'text-white'],
                        ['rp-loyer-total', 'Total loyer', 'text-bimo-gold font-semibold'],
                    ] as [$id, $lbl, $cls])
                    <div class="flex items-center justify-between py-2 border-b border-white/[6%]">
                        <span class="font-body text-xs text-white/40">{{ $lbl }}</span>
                        <span class="font-display font-semibold text-xs {{ $cls }}" id="{{ $id }}">— F</span>
                    </div>
                    @endforeach

                    <div class="my-3 border-t border-white/[7%]"></div>
                    <div class="font-body font-medium text-[9px] uppercase tracking-widest text-white/25 mb-2">Commission agence</div>
                    @foreach([
                        ['rp-taux',     'Taux',           'text-white'],
                        ['rp-comm-ht',  'Commission HT',  'text-bimo-gold'],
                        ['rp-tva',      'TVA 18%',        'text-white'],
                        ['rp-comm-ttc', 'Commission TTC', 'text-bimo-gold'],
                    ] as [$id, $lbl, $cls])
                    <div class="flex items-center justify-between py-2 border-b border-white/[6%]">
                        <span class="font-body text-xs text-white/40">{{ $lbl }}</span>
                        <span class="font-display font-semibold text-xs {{ $cls }}" id="{{ $id }}">—</span>
                    </div>
                    @endforeach

                    <div class="mt-3 p-3.5 bg-bimo-gold/10 border border-bimo-gold/20 rounded-[9px]">
                        <div class="font-body font-medium text-[9px] uppercase tracking-widest text-bimo-gold/60 mb-1">Net propriétaire</div>
                        <div class="font-display font-extrabold text-lg text-bimo-gold" id="rp-net">— FCFA</div>
                    </div>

                    {{-- Section BRS --}}
                    <div id="rp-brs-section" style="display:none"
                         class="mt-3 p-3.5 bg-bimo-red/10 border border-bimo-red/20 rounded-[9px] space-y-1.5">
                        <div class="font-body font-medium text-[9px] uppercase tracking-widest text-bimo-red/60">BRS — Retenue à la source (Art. 201)</div>
                        @foreach([
                            ['rp-brs-taux',    'Taux BRS'],
                            ['rp-brs-base',    'Assiette'],
                            ['rp-brs-montant', 'BRS retenu'],
                        ] as [$id, $lbl])
                        <div class="flex items-center justify-between">
                            <span class="font-body text-xs text-bimo-red/60">{{ $lbl }}</span>
                            <span class="font-body font-semibold text-xs text-bimo-red" id="{{ $id }}">—</span>
                        </div>
                        @endforeach
                    </div>

                    {{-- Net à verser final --}}
                    <div class="mt-3 p-3.5 bg-white/[4%] border border-white/10 rounded-[9px]">
                        <div class="font-body font-medium text-[9px] uppercase tracking-widest text-white/30 mb-1" id="rp-net-label">Net à verser</div>
                        <div class="font-display font-extrabold text-xl text-white" id="rp-net-verser">— F</div>
                        <div class="font-body text-[10px] text-white/25 mt-0.5" id="rp-net-sublabel">Après commission TTC</div>
                    </div>

                </div>
            </div>
        </div>
    </div>

</div>
</form>

@push('scripts')
<script>
const contrats = @json($contrats->keyBy('id'));
let loyerContractuel = 0;

function fmt(n) { return Math.round(n).toLocaleString('fr-FR') + ' F'; }

function chargerContrat(id) {
    if (!id) {
        document.getElementById('contrat-details').style.display = 'none';
        document.getElementById('recap-vide').style.display = 'block';
        document.getElementById('recap-content').style.display = 'none';
        return;
    }
    const select = document.getElementById('contrat_id');
    const opt    = select.options[select.selectedIndex];

    const loyerNu       = parseFloat(opt.dataset.loyerNu)      || 0;
    const charges       = parseFloat(opt.dataset.charges)       || 0;
    const tom           = parseFloat(opt.dataset.tom)            || 0;
    const total         = parseFloat(opt.dataset.loyerTotal)    || 0;
    const tauxComm      = parseFloat(opt.dataset.tauxComm)      || 10;
    const brsApplicable = parseInt(opt.dataset.brsApplicable)   === 1;
    const tauxBrs       = parseFloat(opt.dataset.tauxBrs)       || 15;
    const loyerAssujetti = parseInt(opt.dataset.loyerAssujetti) === 1;
    const tauxTva       = parseFloat(opt.dataset.tauxTva)       || 18;
    const locataireId   = opt.dataset.locataireId || '';
    loyerContractuel = total;

    const commHt     = Math.round(loyerNu * tauxComm / 100);
    const tvaComm    = Math.round(commHt * 0.18);
    const commTtc    = commHt + tvaComm;
    const netProprio = total - commTtc;
    const loyerTtc   = loyerAssujetti ? Math.round(loyerNu * (1 + tauxTva / 100)) : loyerNu;
    const brsBase    = loyerTtc + tom;
    const brsAmt     = brsApplicable ? Math.round(brsBase * tauxBrs / 100) : 0;
    const netAVerser = netProprio - brsAmt;

    document.getElementById('info-bien').textContent      = opt.dataset.bien;
    document.getElementById('info-locataire').textContent = opt.dataset.locataire;
    document.getElementById('info-loyer').textContent     = fmt(total);
    document.getElementById('contrat-details').style.display = 'block';

    const badge = document.getElementById('brs-badge');
    if (brsApplicable) {
        badge.style.display = 'flex';
        document.getElementById('brs-taux-display').textContent   = tauxBrs + '%';
        document.getElementById('brs-montant-display').textContent = fmt(brsAmt);
        document.getElementById('brs-net-display').textContent    = fmt(netAVerser);
        if (locataireId) document.getElementById('brs-profil-link').href = '/admin/users/' + locataireId + '/edit';
    } else {
        badge.style.display = 'none';
    }

    document.getElementById('rp-loyer-nu').textContent    = fmt(loyerNu);
    document.getElementById('rp-charges').textContent     = fmt(charges);
    document.getElementById('rp-tom').textContent         = fmt(tom);
    document.getElementById('rp-loyer-total').textContent = fmt(total);
    document.getElementById('rp-taux').textContent        = tauxComm + ' %';
    document.getElementById('rp-comm-ht').textContent     = fmt(commHt);
    document.getElementById('rp-tva').textContent         = fmt(tvaComm);
    document.getElementById('rp-comm-ttc').textContent    = fmt(commTtc);
    document.getElementById('rp-net').textContent         = fmt(netProprio) + ' CFA';

    const rpBrsSection = document.getElementById('rp-brs-section');
    if (brsApplicable) {
        rpBrsSection.style.display = 'block';
        document.getElementById('rp-brs-taux').textContent    = tauxBrs + ' %';
        document.getElementById('rp-brs-base').textContent    = fmt(brsBase);
        document.getElementById('rp-brs-montant').textContent = '−' + fmt(brsAmt);
        document.getElementById('rp-net-label').textContent    = 'Net à verser (après BRS)';
        document.getElementById('rp-net-sublabel').textContent = 'Après commission TTC et BRS';
    } else {
        rpBrsSection.style.display = 'none';
        document.getElementById('rp-net-label').textContent    = 'Net à verser';
        document.getElementById('rp-net-sublabel').textContent = 'Après commission TTC';
    }
    document.getElementById('rp-net-verser').textContent = fmt(netAVerser) + ' CFA';

    document.getElementById('recap-vide').style.display    = 'none';
    document.getElementById('recap-content').style.display = 'block';

    if (!document.getElementById('montant_encaisse').value) {
        document.getElementById('montant_encaisse').value = Math.round(total);
    }

    fetch(`/admin/paiements/dernier-periode/${id}`)
        .then(r => r.json())
        .then(data => {
            if (data.periode) document.getElementById('periode').value = data.periode.substring(0, 7);
        }).catch(() => {});

    verifierMontant();
}

function verifierMontant() {
    const montant = parseFloat(document.getElementById('montant_encaisse').value) || 0;
    const ok   = document.getElementById('montant-ok');
    const diff = document.getElementById('montant-diff');
    if (!loyerContractuel || !montant) { ok.style.display='none'; diff.style.display='none'; return; }
    if (Math.abs(montant - loyerContractuel) < 1) {
        ok.style.display='block'; diff.style.display='none';
    } else {
        ok.style.display='none'; diff.style.display='block';
    }
}

function filtrerContrats(q) {
    q = q.toLowerCase().trim();
    Array.from(document.getElementById('contrat_id').options).forEach(opt => {
        if (!opt.value) return;
        var txt = opt.text.toLowerCase();
        var bien = (opt.dataset.bien || '').toLowerCase();
        var loc  = (opt.dataset.locataire || '').toLowerCase();
        opt.hidden = q !== '' && !txt.includes(q) && !bien.includes(q) && !loc.includes(q);
    });
}

@if($contrat)
    document.addEventListener('DOMContentLoaded', () => chargerContrat({{ $contrat->id }}));
@else
    const selectInit = document.getElementById('contrat_id');
    if (selectInit.value) document.addEventListener('DOMContentLoaded', () => chargerContrat(selectInit.value));
@endif
</script>
@endpush

@endsection
