{{--
    ════════════════════════════════════════════════════════════════════
    PARTIEL FISCAL — Section à insérer dans admin/contrats/create.blade.php
    et admin/contrats/edit.blade.php, APRÈS la section "Conditions financières"
    et AVANT la section "Garant".

    UTILISATION :
      @include('admin.contrats._section-fiscal', ['contrat' => $contrat ?? null])

    Note charte : les BADGES d'état fiscal (TVA / BRS) conservent leur
    sémantique tricolore (vert = exonéré/ok, ambre = applicable, rouge = BRS)
    comme exception documentée — signal légal. Le reste suit la charte.
    Accent de sélection = var(--ac). Logique fiscale (FiscalService, champs,
    conditions) STRICTEMENT inchangée.
    ════════════════════════════════════════════════════════════════════
--}}

@php
    // Valeurs par défaut pour create (contrat null) vs edit
    $typeBail     = old('type_bail', $contrat?->type_bail ?? 'habitation');
    $estMeuble    = old('meuble_bien', $contrat?->bien?->meuble ?? false);
    $loyerAssujetti = \App\Services\FiscalService::loyerEstAssujetti($typeBail, (bool) $estMeuble);
    $brsApplicable = old('brs_applicable', $contrat?->brs_applicable ?? false);
    $tauxBrsManuel = old('taux_brs_manuel', $contrat?->taux_brs_manuel ?? null);
    $tvaLoyerOverride = old('taux_tva_loyer', $contrat?->taux_tva_loyer ?? 18.0);
    $chargesTva   = old('charges_assujetties_tva', $contrat?->charges_assujetties_tva ?? false);
@endphp

{{-- ─── SECTION : Paramètres fiscaux ─────────────────────────────────────── --}}
<div class="font-body font-bold text-xs uppercase tracking-wider text-bimo-text/70 mb-3 pb-2 border-b border-bimo-navy/10 mt-6">
    ⚖️ Paramètres fiscaux
    <span class="font-body font-normal text-[10px] normal-case tracking-normal text-bimo-text/40 ml-1.5">
        Auto-calculés · modifiables en cas de situation particulière
    </span>
</div>

{{-- ── TVA loyer ──────────────────────────────────────────────────────────── --}}
<div class="bg-bimo-bg border border-bimo-navy/10 rounded-[9px] px-4 py-3.5 mb-3" id="bloc-tva-loyer">
    <div class="flex items-center justify-between mb-2.5">
        <div>
            <div class="font-body font-semibold text-[13px] text-bimo-text flex items-center">
                TVA sur le loyer
                <i class="tip-icon" data-tip="Taxe sur la Valeur Ajoutée (18%). Obligatoire pour les baux commerciaux, mixtes, et habitation meublée. Exonéré pour habitation non meublée. Appliquée sur loyer + TOM. Art. 355-359 CGI SN.">?</i>
            </div>
            <div class="font-body text-[11px] text-bimo-text/50 mt-0.5">
                Art. 355-359 CGI SN · Auto selon type de bail et meublé/non meublé
            </div>
        </div>
        {{-- Badge dynamique mis à jour par JS — sémantique fiscale (exception charte) --}}
        <span id="badge-tva-loyer"
              style="display:inline-flex;align-items:center;gap:5px;padding:4px 12px;border-radius:99px;font-size:11px;font-weight:700;{{ $loyerAssujetti ? 'background:#fef3c7;color:#d97706' : 'background:#dcfce7;color:#16a34a' }}">
            <span id="badge-tva-dot" style="width:6px;height:6px;border-radius:50%;background:currentColor"></span>
            <span id="badge-tva-label">{{ $loyerAssujetti ? 'TVA 18% applicable' : 'Exonéré' }}</span>
        </span>
    </div>

    <div class="flex items-center gap-3.5">
        <label class="flex items-center gap-2 cursor-pointer font-body text-[13px] text-bimo-text/70">
            <input type="hidden"  name="loyer_assujetti_tva" value="0">
            <input type="checkbox" name="loyer_assujetti_tva" id="loyer_assujetti_tva" value="1"
                   {{ $loyerAssujetti ? 'checked' : '' }}
                   onchange="updateFiscalBadge()"
                   class="w-4 h-4 rounded accent-[var(--ac)]">
            Loyer soumis à TVA 18%
        </label>

        <div id="champ-taux-tva-loyer" class="items-center gap-1.5 {{ $loyerAssujetti ? 'flex' : 'hidden' }}">
            <label class="font-body text-xs text-bimo-text/50">Taux (%)</label>
            <input type="number" name="taux_tva_loyer" id="taux_tva_loyer"
                   value="{{ $tvaLoyerOverride }}"
                   min="0" max="20" step="0.5"
                   class="w-[70px] px-2 py-1.5 rounded-[7px] bg-white border border-bimo-navy/15 font-body text-[13px] text-bimo-text focus:outline-none focus:border-[var(--ac)] focus:ring-2 focus:ring-[var(--ac)]/15 transition-all duration-150">
            <span class="font-body text-[11px] text-bimo-text/40">Par défaut : 18%</span>
        </div>
    </div>

    <div class="mt-2.5 px-3 py-2 bg-bimo-navy/[4%] rounded-[7px] font-body text-[11px] text-bimo-text/60 leading-relaxed" id="note-tva">
        <strong>Règle automatique :</strong>
        Bail commercial → TVA 18% |
        Bail mixte/saisonnier → TVA 18% |
        Habitation meublée → TVA 18% |
        Habitation nue → Exonéré.
        <br><strong>Art. 354 CGI SN :</strong> la TVA s'applique sur <strong>loyer + TOM</strong>.
        Les <strong>charges récupérables</strong> restent hors TVA.
    </div>
</div>

{{-- ── TVA sur charges ─────────────────────────────────────────────────────── --}}
<div class="bg-bimo-bg border border-bimo-navy/10 rounded-[9px] px-4 py-3.5 mb-3" id="bloc-tva-charges">
    <div class="flex items-center justify-between mb-2.5">
        <div>
            <div class="font-body font-semibold text-[13px] text-bimo-text flex items-center">
                TVA sur les charges locatives
                <i class="tip-icon" data-tip="Si les charges sont facturées en forfait fixe (non justifié), la DGI les considère comme une prestation de service → TVA 18% obligatoire. Si ce sont des débours purs (facture originale au nom du locataire), elles sont exonérées.">?</i>
            </div>
            <div class="font-body text-[11px] text-bimo-text/50 mt-0.5">
                DGI SN — Forfait = prestation de service assujettie · Débours = hors TVA
            </div>
        </div>
        {{-- Badge dynamique — sémantique fiscale (exception charte) --}}
        <span id="badge-tva-charges"
              style="display:inline-flex;align-items:center;gap:5px;padding:4px 12px;border-radius:99px;font-size:11px;font-weight:700;{{ $chargesTva ? 'background:#fef3c7;color:#d97706' : 'background:#dcfce7;color:#16a34a' }}">
            <span style="width:6px;height:6px;border-radius:50%;background:currentColor"></span>
            <span id="badge-tva-charges-label">
                {{ $chargesTva ? 'TVA 18% sur charges' : 'Charges hors TVA' }}
            </span>
        </span>
    </div>

    <label class="flex items-center gap-2 cursor-pointer font-body text-[13px] text-bimo-text/70">
        <input type="hidden"   name="charges_assujetties_tva" value="0">
        <input type="checkbox" name="charges_assujetties_tva" id="charges_assujetties_tva" value="1"
               {{ $chargesTva ? 'checked' : '' }}
               onchange="toggleTvaCharges()"
               class="w-4 h-4 rounded accent-[var(--ac)]">
        Charges facturées en forfait (TVA 18% applicable)
    </label>

    <div class="mt-2.5 px-3 py-2 bg-bimo-navy/[4%] border border-bimo-navy/10 rounded-[7px] font-body text-[11px] text-bimo-text/60 leading-relaxed">
        ⚠️ <strong>DGI SN :</strong>
        Pour être exonérées de TVA, les charges doivent être des <strong>débours purs</strong> :
        facture originale au nom du locataire, refacturation à l'identique, sans marge.
        <br>Dès qu'un <strong>forfait</strong> est appliqué, la DGI exige TVA 18% sur ces montants.
        En cas de contrôle fiscal, l'absence de TVA sur un forfait expose l'agence à un redressement.
    </div>
</div>

{{-- ── BRS ────────────────────────────────────────────────────────────────── --}}
<div class="bg-bimo-bg border border-bimo-navy/10 rounded-[9px] px-4 py-3.5 mb-3" id="bloc-brs">
    <div class="flex items-center justify-between mb-2.5">
        <div>
            <div class="font-body font-semibold text-[13px] text-bimo-text flex items-center">
                Retenue à la Source (BRS)
                <i class="tip-icon" data-tip="Retenue à la source de 5% (Art. 201 CGI SN). Obligatoire si le locataire est une société (SARL, SA, GIE…). Le locataire retient 5% du loyer TTC + TOM et le verse directement à la DGI chaque mois. Non payer expose à un redressement fiscal.">?</i>
            </div>
            <div class="font-body text-[11px] text-bimo-text/50 mt-0.5">
                Art. 201 CGI SN · Obligatoire si locataire = entreprise/personne morale
            </div>
        </div>
        {{-- Badge dynamique — sémantique fiscale (exception charte) --}}
        <span id="badge-brs"
              style="display:inline-flex;align-items:center;gap:5px;padding:4px 12px;border-radius:99px;font-size:11px;font-weight:700;{{ $brsApplicable ? 'background:#fee2e2;color:#dc2626' : 'background:#f3f4f6;color:#6b7280' }}">
            <span style="width:6px;height:6px;border-radius:50%;background:currentColor"></span>
            {{ $brsApplicable ? 'BRS 5% applicable' : 'Non applicable' }}
        </span>
    </div>

    <div class="flex items-center gap-3.5 flex-wrap">
        <label class="flex items-center gap-2 cursor-pointer font-body text-[13px] text-bimo-text/70">
            <input type="hidden"  name="brs_applicable" value="0">
            <input type="checkbox" name="brs_applicable" id="brs_applicable" value="1"
                   {{ $brsApplicable ? 'checked' : '' }}
                   onchange="toggleBrsChamp()"
                   class="w-4 h-4 rounded accent-[var(--ac)]">
            Retenue à la source applicable
        </label>

        <div id="champ-taux-brs" class="items-center gap-1.5 {{ $brsApplicable ? 'flex' : 'hidden' }}">
            <label class="font-body text-xs text-bimo-text/50">Taux override (%)</label>
            <input type="number" name="taux_brs_manuel" id="taux_brs_manuel"
                   value="{{ $tauxBrsManuel }}"
                   min="0" max="20" step="0.5" placeholder="5"
                   class="w-[70px] px-2 py-1.5 rounded-[7px] bg-white border border-bimo-navy/15 font-body text-[13px] text-bimo-text focus:outline-none focus:border-[var(--ac)] focus:ring-2 focus:ring-[var(--ac)]/15 transition-all duration-150">
            <span class="font-body text-[11px] text-bimo-text/40">Laisser vide = 5% légal (Art. 201 §3 CGI SN). Ne modifier que sur instruction écrite de la DGI.</span>
        </div>
    </div>

    <div class="mt-2.5 px-3 py-2 bg-bimo-navy/[4%] rounded-[7px] font-body text-[11px] text-bimo-text/60 leading-relaxed {{ $brsApplicable ? '' : 'hidden' }}" id="note-brs">
        <strong>Art. 201 CGI SN :</strong>
        Taux légal 5% × <strong>(loyer TTC + TOM)</strong> (Art. 201 §3 CGI SN — texte officiel). Peut être modifié par convention.
        Le BRS est retenu par le locataire et versé <strong>directement à la DGI</strong> — pas par l'agence.
    </div>

    {{-- Alerte bail commercial sans BRS — alerte critique (rouge charte) --}}
    <div id="alerte-brs-commercial" class="hidden mt-2 px-3 py-2 bg-bimo-red/10 border border-bimo-red/20 rounded-[7px] font-body text-[11px] text-bimo-red leading-relaxed">
        ⚠️ <strong>Bail commercial détecté.</strong>
        Si le locataire est une entreprise ou personne morale, la BRS est <strong>obligatoire</strong> (Art. 201 CGI SN).
        Activez la case ci-dessus pour éviter un redressement fiscal.
    </div>
</div>

{{-- ── Enregistrement DGID ─────────────────────────────────────────────────── --}}
<div class="bg-bimo-bg border border-bimo-navy/10 rounded-[9px] px-4 py-3.5 mb-3">
    <div class="font-body font-semibold text-[13px] text-bimo-text mb-1 flex items-center">
        Enregistrement DGID
        <i class="tip-icon" data-tip="Droit de bail obligatoire (Art. 464 B + 472 IV.6 CGI SN). À déposer à la DGI dans le mois suivant l'entrée en possession. Taux : 2% du loyer annuel (habitation ET commercial — taux uniforme) + timbre fiscal. Sans enregistrement, le bail est inopposable aux tiers.">?</i>
    </div>
    <div class="font-body text-[11px] text-bimo-text/50 mb-3">
        Art. 464 B + 472 IV.6 CGI SN · Délai : 1 mois après entrée en possession · Sanction : nullité opposable aux tiers
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <div>
            <label class="block font-body font-semibold text-xs text-bimo-text/70 mb-1.5">
                Date d'enregistrement
            </label>
            <input type="date" name="date_enregistrement_dgid"
                   value="{{ old('date_enregistrement_dgid', $contrat?->date_enregistrement_dgid?->format('Y-m-d') ?? '') }}"
                   class="w-full px-2.5 py-2 rounded-[8px] bg-white border border-bimo-navy/15 font-body text-[13px] text-bimo-text focus:outline-none focus:border-[var(--ac)] focus:ring-2 focus:ring-[var(--ac)]/15 transition-all duration-150">
        </div>
        <div>
            <label class="block font-body font-semibold text-xs text-bimo-text/70 mb-1.5">
                N° quittance DGID
            </label>
            <input type="text" name="numero_quittance_dgid"
                   value="{{ old('numero_quittance_dgid', $contrat?->numero_quittance_dgid ?? '') }}"
                   placeholder="Ex: DGI-2025-000123"
                   class="w-full px-2.5 py-2 rounded-[8px] bg-white border border-bimo-navy/15 font-body text-[13px] text-bimo-text placeholder:text-bimo-text/35 focus:outline-none focus:border-[var(--ac)] focus:ring-2 focus:ring-[var(--ac)]/15 transition-all duration-150">
        </div>
        <div>
            <label class="block font-body font-semibold text-xs text-bimo-text/70 mb-1.5">
                Montant payé (FCFA)
            </label>
            <input type="number" name="montant_droit_de_bail"
                   value="{{ old('montant_droit_de_bail', $contrat?->montant_droit_de_bail ?? '') }}"
                   placeholder="Auto-calculé : 1% ou 2% loyer annuel"
                   min="0" step="500"
                   class="w-full px-2.5 py-2 rounded-[8px] bg-white border border-bimo-navy/15 font-body text-[13px] text-bimo-text placeholder:text-bimo-text/35 focus:outline-none focus:border-[var(--ac)] focus:ring-2 focus:ring-[var(--ac)]/15 transition-all duration-150">
        </div>
    </div>

    <div class="mt-2.5">
        <label class="flex items-center gap-2 cursor-pointer font-body text-xs text-bimo-text/70">
            <input type="checkbox" name="enregistrement_exonere" value="1"
                   {{ old('enregistrement_exonere', $contrat?->enregistrement_exonere ?? false) ? 'checked' : '' }}
                   class="w-3.5 h-3.5 rounded accent-[var(--ac)]">
            Exonéré d'enregistrement (bail public, diplomatique, ONG...)
        </label>
    </div>
</div>

{{-- ── Politique de caution ─────────────────────────────────────────────────── --}}
@php $cautionAgence = old('caution_gardee_par_agence', $contrat?->caution_gardee_par_agence ?? false); @endphp
<div class="bg-bimo-bg border border-bimo-navy/10 rounded-[9px] px-4 py-3.5 mb-3">
    <div class="font-body font-semibold text-[13px] text-bimo-text mb-1">Politique de la caution</div>
    <div class="font-body text-[11px] text-bimo-text/50 mb-3">
        Détermine à qui est versé le dépôt de garantie lors du premier encaissement.
    </div>
    <label class="flex items-center gap-2.5 cursor-pointer">
        {{-- Switch toggle (accent = var(--ac)) --}}
        <span class="relative inline-block w-10 h-[22px] flex-shrink-0">
            <input type="hidden"   name="caution_gardee_par_agence" value="0">
            <input type="checkbox" name="caution_gardee_par_agence" id="caution_gardee_par_agence" value="1"
                   {{ $cautionAgence ? 'checked' : '' }}
                   class="opacity-0 w-0 h-0 absolute"
                   onchange="toggleCautionLabel()">
            <span id="switch-track" onclick="document.getElementById('caution_gardee_par_agence').click()"
                  class="absolute inset-0 rounded-full cursor-pointer transition-all duration-200"
                  style="background:{{ $cautionAgence ? 'var(--ac)' : '#d1d5db' }}">
                <span id="switch-knob"
                      class="absolute top-[3px] w-4 h-4 rounded-full bg-white transition-all duration-200"
                      style="left:{{ $cautionAgence ? '21px' : '3px' }}"></span>
            </span>
        </span>
        <span>
            <span class="font-body text-[13px] text-bimo-text/70 font-medium">Caution gardée par l'agence (séquestre)</span>
            <span id="caution-policy-label" class="block font-body text-[11px] mt-px"
                  style="color:{{ $cautionAgence ? 'var(--ac)' : '#16a34a' }}">
                {{ $cautionAgence
                    ? 'L\'agence conserve la caution — non reversée au bailleur'
                    : 'Caution reversée au bailleur lors du premier versement' }}
            </span>
        </span>
    </label>
</div>

{{-- JS fiscal ────────────────────────────────────────────────────────────── --}}
{{-- Les badges fiscaux gardent leur sémantique tricolore (signal légal) :
     le JS bascule leur couleur inline — exception charte documentée.
     Accent de sélection (var(--ac)) géré via classes Tailwind. --}}
<script>
function updateFiscalBadge() {
    const checked = document.getElementById('loyer_assujetti_tva').checked;
    const badge   = document.getElementById('badge-tva-loyer');
    const label   = document.getElementById('badge-tva-label');
    const champTaux = document.getElementById('champ-taux-tva-loyer');

    label.textContent = checked ? 'TVA 18% applicable' : 'Exonéré';
    badge.style.background = checked ? '#fef3c7' : '#dcfce7';
    badge.style.color      = checked ? '#d97706'  : '#16a34a';
    champTaux.classList.toggle('flex', checked);
    champTaux.classList.toggle('hidden', !checked);
}

function toggleCautionLabel() {
    const cb    = document.getElementById('caution_gardee_par_agence');
    const label = document.getElementById('caution-policy-label');
    const track = document.getElementById('switch-track');
    const knob  = document.getElementById('switch-knob');
    if (cb.checked) {
        track.style.background = 'var(--ac)';
        knob.style.left        = '21px';
        label.style.color      = 'var(--ac)';
        label.textContent      = "L'agence conserve la caution — non reversée au bailleur";
    } else {
        track.style.background = '#d1d5db';
        knob.style.left        = '3px';
        label.style.color      = '#16a34a';
        label.textContent      = 'Caution reversée au bailleur lors du premier versement';
    }
}

function toggleBrsChamp() {
    const checked  = document.getElementById('brs_applicable').checked;
    const champ    = document.getElementById('champ-taux-brs');
    const badge    = document.getElementById('badge-brs');
    const note     = document.getElementById('note-brs');

    champ.classList.toggle('flex', checked);
    champ.classList.toggle('hidden', !checked);
    if (note) note.classList.toggle('hidden', !checked);
    badge.style.background = checked ? '#fee2e2' : '#f3f4f6';
    badge.style.color      = checked ? '#dc2626' : '#6b7280';
    badge.querySelector('span + span').textContent = checked ? 'BRS 5% applicable' : 'Non applicable';
    verifierAlerteBrsCommercial();
    if (typeof mettreAJourRecap === 'function') mettreAJourRecap();
}

function toggleTvaCharges() {
    const checked = document.getElementById('charges_assujetties_tva').checked;
    const badge   = document.getElementById('badge-tva-charges');
    const label   = document.getElementById('badge-tva-charges-label');
    if (label) label.textContent = checked ? 'TVA 18% sur charges' : 'Charges hors TVA';
    if (badge) {
        badge.style.background = checked ? '#fef3c7' : '#dcfce7';
        badge.style.color      = checked ? '#d97706'  : '#16a34a';
    }
    if (typeof mettreAJourRecap === 'function') mettreAJourRecap();
}
window.toggleTvaCharges = toggleTvaCharges;

function verifierAlerteBrsCommercial() {
    const alerte   = document.getElementById('alerte-brs-commercial');
    if (!alerte) return;
    const typeBail = document.getElementById('type_bail')?.value ?? '';
    const brsOk    = document.getElementById('brs_applicable')?.checked ?? false;
    alerte.classList.toggle('hidden', !((typeBail === 'commercial' || typeBail === 'mixte') && !brsOk));
}

// Exposée globalement pour que chargerInfosBien() puisse l'appeler
window.verifierAlerteBrsCommercial = verifierAlerteBrsCommercial;
</script>
