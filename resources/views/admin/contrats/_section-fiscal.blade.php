{{--
    ════════════════════════════════════════════════════════════════════
    PARTIEL FISCAL — Section à insérer dans admin/contrats/create.blade.php
    et admin/contrats/edit.blade.php, APRÈS la section "Conditions financières"
    et AVANT la section "Garant".

    UTILISATION :
      @include('admin.contrats._section-fiscal', ['contrat' => $contrat ?? null])
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
@endphp

{{-- ─── SECTION : Paramètres fiscaux ─────────────────────────────────────── --}}
<div style="font-size:12px;font-weight:700;color:#374151;text-transform:uppercase;letter-spacing:.5px;margin-bottom:12px;padding-bottom:8px;border-bottom:1px solid #e5e7eb;margin-top:24px">
    ⚖️ Paramètres fiscaux
    <span style="font-size:10px;font-weight:400;color:#9ca3af;text-transform:none;letter-spacing:0;margin-left:6px">
        Auto-calculés · modifiables en cas de situation particulière
    </span>
</div>

{{-- ── TVA loyer ──────────────────────────────────────────────────────────── --}}
<div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:9px;padding:14px 16px;margin-bottom:12px" id="bloc-tva-loyer">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px">
        <div>
            <div style="font-size:13px;font-weight:600;color:#0d1117;display:flex;align-items:center">
                TVA sur le loyer
                <i class="tip-icon" data-tip="Taxe sur la Valeur Ajoutée (18%). Obligatoire pour les baux commerciaux, mixtes, et habitation meublée. Exonéré pour habitation non meublée. Appliquée sur loyer + TOM. Art. 355-359 CGI SN.">?</i>
            </div>
            <div style="font-size:11px;color:#6b7280;margin-top:2px">
                Art. 355-359 CGI SN · Auto selon type de bail et meublé/non meublé
            </div>
        </div>
        {{-- Badge dynamique mis à jour par JS --}}
        <span id="badge-tva-loyer"
              style="display:inline-flex;align-items:center;gap:5px;padding:4px 12px;border-radius:99px;font-size:11px;font-weight:700;{{ $loyerAssujetti ? 'background:#fef3c7;color:#d97706' : 'background:#dcfce7;color:#16a34a' }}">
            <span id="badge-tva-dot" style="width:6px;height:6px;border-radius:50%;background:currentColor"></span>
            <span id="badge-tva-label">{{ $loyerAssujetti ? 'TVA 18% applicable' : 'Exonéré' }}</span>
        </span>
    </div>

    <div style="display:flex;align-items:center;gap:14px">
        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;color:#374151">
            <input type="hidden"  name="loyer_assujetti_tva" value="0">
            <input type="checkbox" name="loyer_assujetti_tva" id="loyer_assujetti_tva" value="1"
                   {{ $loyerAssujetti ? 'checked' : '' }}
                   onchange="updateFiscalBadge()"
                   style="width:16px;height:16px;accent-color:#c9a84c">
            Loyer soumis à TVA 18%
        </label>

        <div id="champ-taux-tva-loyer" style="{{ $loyerAssujetti ? '' : 'display:none' }};display:flex;align-items:center;gap:6px">
            <label style="font-size:12px;color:#6b7280">Taux (%)</label>
            <input type="number" name="taux_tva_loyer" id="taux_tva_loyer"
                   value="{{ $tvaLoyerOverride }}"
                   min="0" max="20" step="0.5"
                   style="width:70px;padding:6px 8px;border:1px solid #e5e7eb;border-radius:7px;font-size:13px;font-family:'DM Sans',sans-serif">
            <span style="font-size:11px;color:#9ca3af">Par défaut : 18%</span>
        </div>
    </div>

    <div style="margin-top:10px;padding:8px 12px;background:#fffbeb;border-radius:7px;font-size:11px;color:#92400e;line-height:1.6" id="note-tva">
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
<div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:9px;padding:14px 16px;margin-bottom:12px" id="bloc-tva-charges">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px">
        <div>
            <div style="font-size:13px;font-weight:600;color:#0d1117;display:flex;align-items:center">
                TVA sur les charges locatives
                <i class="tip-icon" data-tip="Si les charges sont facturées en forfait fixe (non justifié), la DGI les considère comme une prestation de service → TVA 18% obligatoire. Si ce sont des débours purs (facture originale au nom du locataire), elles sont exonérées.">?</i>
            </div>
            <div style="font-size:11px;color:#6b7280;margin-top:2px">
                DGI SN — Forfait = prestation de service assujettie · Débours = hors TVA
            </div>
        </div>
        <span id="badge-tva-charges"
              style="display:inline-flex;align-items:center;gap:5px;padding:4px 12px;border-radius:99px;font-size:11px;font-weight:700;
              {{ old('charges_assujetties_tva', $contrat?->charges_assujetties_tva ?? false) ? 'background:#fef3c7;color:#d97706' : 'background:#dcfce7;color:#16a34a' }}">
            <span style="width:6px;height:6px;border-radius:50%;background:currentColor"></span>
            <span id="badge-tva-charges-label">
                {{ old('charges_assujetties_tva', $contrat?->charges_assujetties_tva ?? false) ? 'TVA 18% sur charges' : 'Charges hors TVA' }}
            </span>
        </span>
    </div>

    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;color:#374151">
        <input type="hidden"   name="charges_assujetties_tva" value="0">
        <input type="checkbox" name="charges_assujetties_tva" id="charges_assujetties_tva" value="1"
               {{ old('charges_assujetties_tva', $contrat?->charges_assujetties_tva ?? false) ? 'checked' : '' }}
               onchange="toggleTvaCharges()"
               style="width:16px;height:16px;accent-color:#c9a84c">
        Charges facturées en forfait (TVA 18% applicable)
    </label>

    <div style="margin-top:10px;padding:8px 12px;background:#fffbeb;border:1px solid #fde68a;border-radius:7px;font-size:11px;color:#92400e;line-height:1.6">
        ⚠️ <strong>DGI SN :</strong>
        Pour être exonérées de TVA, les charges doivent être des <strong>débours purs</strong> :
        facture originale au nom du locataire, refacturation à l'identique, sans marge.
        <br>Dès qu'un <strong>forfait</strong> est appliqué, la DGI exige TVA 18% sur ces montants.
        En cas de contrôle fiscal, l'absence de TVA sur un forfait expose l'agence à un redressement.
    </div>
</div>

{{-- ── BRS ────────────────────────────────────────────────────────────────── --}}
<div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:9px;padding:14px 16px;margin-bottom:12px" id="bloc-brs">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px">
        <div>
            <div style="font-size:13px;font-weight:600;color:#0d1117;display:flex;align-items:center">
                Retenue à la Source (BRS)
                <i class="tip-icon" data-tip="Retenue à la source de 5% (Art. 201 CGI SN). Obligatoire si le locataire est une société (SARL, SA, GIE…). Le locataire retient 5% du loyer TTC + TOM et le verse directement à la DGI chaque mois. Non payer expose à un redressement fiscal.">?</i>
            </div>
            <div style="font-size:11px;color:#6b7280;margin-top:2px">
                Art. 201 CGI SN · Obligatoire si locataire = entreprise/personne morale
            </div>
        </div>
        <span id="badge-brs"
              style="display:inline-flex;align-items:center;gap:5px;padding:4px 12px;border-radius:99px;font-size:11px;font-weight:700;{{ $brsApplicable ? 'background:#fee2e2;color:#dc2626' : 'background:#f3f4f6;color:#6b7280' }}">
            <span style="width:6px;height:6px;border-radius:50%;background:currentColor"></span>
            {{ $brsApplicable ? 'BRS 5% applicable' : 'Non applicable' }}
        </span>
    </div>

    <div style="display:flex;align-items:center;gap:14px;flex-wrap:wrap">
        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;color:#374151">
            <input type="hidden"  name="brs_applicable" value="0">
            <input type="checkbox" name="brs_applicable" id="brs_applicable" value="1"
                   {{ $brsApplicable ? 'checked' : '' }}
                   onchange="toggleBrsChamp()"
                   style="width:16px;height:16px;accent-color:#dc2626">
            Retenue à la source applicable
        </label>

        <div id="champ-taux-brs" style="{{ $brsApplicable ? '' : 'display:none' }};display:flex;align-items:center;gap:6px">
            <label style="font-size:12px;color:#6b7280">Taux override (%)</label>
            <input type="number" name="taux_brs_manuel" id="taux_brs_manuel"
                   value="{{ $tauxBrsManuel }}"
                   min="0" max="20" step="0.5" placeholder="15"
                   style="width:70px;padding:6px 8px;border:1px solid #e5e7eb;border-radius:7px;font-size:13px;font-family:'DM Sans',sans-serif">
            <span style="font-size:11px;color:#9ca3af">Laisser vide = 5% légal (Art. 201 CGI SN)</span>
        </div>
    </div>

    <div style="margin-top:10px;padding:8px 12px;background:#fff1f2;border-radius:7px;font-size:11px;color:#9f1239;line-height:1.6" id="note-brs"
         style="{{ $brsApplicable ? '' : 'display:none' }}">
        <strong>Art. 201 CGI SN :</strong>
        Taux légal 5% × <strong>(loyer TTC + TOM)</strong> (Art. 201 §3 CGI SN — texte officiel). Peut être modifié par convention.
        Le BRS est retenu par le locataire et versé <strong>directement à la DGI</strong> — pas par l'agence.
    </div>

    {{-- Alerte bail commercial sans BRS --}}
    <div id="alerte-brs-commercial" style="display:none;margin-top:8px;padding:8px 12px;background:#fef3c7;border:1px solid #fde68a;border-radius:7px;font-size:11px;color:#92400e;line-height:1.5">
        ⚠️ <strong>Bail commercial détecté.</strong>
        Si le locataire est une entreprise ou personne morale, la BRS est <strong>obligatoire</strong> (Art. 201 CGI SN).
        Activez la case ci-dessus pour éviter un redressement fiscal.
    </div>
</div>

{{-- ── Enregistrement DGID ─────────────────────────────────────────────────── --}}
<div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:9px;padding:14px 16px;margin-bottom:12px">
    <div style="font-size:13px;font-weight:600;color:#0d1117;margin-bottom:4px;display:flex;align-items:center">
        Enregistrement DGID
        <i class="tip-icon" data-tip="Droit de bail obligatoire (Art. 464 B + 472 IV.6 CGI SN). À déposer à la DGI dans le mois suivant l'entrée en possession. Taux : 2% du loyer annuel (habitation ET commercial — taux uniforme) + timbre fiscal. Sans enregistrement, le bail est inopposable aux tiers.">?</i>
    </div>
    <div style="font-size:11px;color:#6b7280;margin-bottom:12px">
        Art. 464 B + 472 IV.6 CGI SN · Délai : 1 mois après entrée en possession · Sanction : nullité opposable aux tiers
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px">
        <div>
            <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:5px">
                Date d'enregistrement
            </label>
            <input type="date" name="date_enregistrement_dgid"
                   value="{{ old('date_enregistrement_dgid', $contrat?->date_enregistrement_dgid?->format('Y-m-d') ?? '') }}"
                   style="width:100%;padding:8px 10px;border:1px solid #e5e7eb;border-radius:8px;font-size:13px;font-family:'DM Sans',sans-serif">
        </div>
        <div>
            <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:5px">
                N° quittance DGID
            </label>
            <input type="text" name="numero_quittance_dgid"
                   value="{{ old('numero_quittance_dgid', $contrat?->numero_quittance_dgid ?? '') }}"
                   placeholder="Ex: DGI-2025-000123"
                   style="width:100%;padding:8px 10px;border:1px solid #e5e7eb;border-radius:8px;font-size:13px;font-family:'DM Sans',sans-serif">
        </div>
        <div>
            <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:5px">
                Montant payé (FCFA)
            </label>
            <input type="number" name="montant_droit_de_bail"
                   value="{{ old('montant_droit_de_bail', $contrat?->montant_droit_de_bail ?? '') }}"
                   placeholder="Auto-calculé : 1% ou 2% loyer annuel"
                   min="0" step="500"
                   style="width:100%;padding:8px 10px;border:1px solid #e5e7eb;border-radius:8px;font-size:13px;font-family:'DM Sans',sans-serif">
        </div>
    </div>

    <div style="margin-top:10px">
        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:12px;color:#374151">
            <input type="checkbox" name="enregistrement_exonere" value="1"
                   {{ old('enregistrement_exonere', $contrat?->enregistrement_exonere ?? false) ? 'checked' : '' }}
                   style="width:14px;height:14px;accent-color:#7c3aed">
            Exonéré d'enregistrement (bail public, diplomatique, ONG...)
        </label>
    </div>
</div>

{{-- ── Politique de caution ─────────────────────────────────────────────────── --}}
<div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:9px;padding:14px 16px;margin-bottom:12px">
    <div style="font-size:13px;font-weight:600;color:#0d1117;margin-bottom:4px">Politique de la caution</div>
    <div style="font-size:11px;color:#6b7280;margin-bottom:12px">
        Détermine à qui est versé le dépôt de garantie lors du premier encaissement.
    </div>
    <label style="display:flex;align-items:center;gap:10px;cursor:pointer">
        {{-- Switch toggle --}}
        <span style="position:relative;display:inline-block;width:40px;height:22px;flex-shrink:0">
            <input type="hidden"   name="caution_gardee_par_agence" value="0">
            <input type="checkbox" name="caution_gardee_par_agence" id="caution_gardee_par_agence" value="1"
                   {{ old('caution_gardee_par_agence', $contrat?->caution_gardee_par_agence ?? false) ? 'checked' : '' }}
                   style="opacity:0;width:0;height:0;position:absolute"
                   onchange="toggleCautionLabel()">
            <span id="switch-track" onclick="document.getElementById('caution_gardee_par_agence').click()"
                  style="position:absolute;inset:0;border-radius:99px;cursor:pointer;transition:.2s;
                  background:{{ old('caution_gardee_par_agence', $contrat?->caution_gardee_par_agence ?? false) ? '#c9a84c' : '#d1d5db' }}">
                <span id="switch-knob"
                      style="position:absolute;top:3px;width:16px;height:16px;border-radius:50%;background:#fff;transition:.2s;
                      left:{{ old('caution_gardee_par_agence', $contrat?->caution_gardee_par_agence ?? false) ? '21px' : '3px' }}"></span>
            </span>
        </span>
        <span>
            <span style="font-size:13px;color:#374151;font-weight:500">Caution gardée par l'agence (séquestre)</span>
            <span id="caution-policy-label" style="display:block;font-size:11px;margin-top:1px;
                  color:{{ old('caution_gardee_par_agence', $contrat?->caution_gardee_par_agence ?? false) ? '#8a6e2f' : '#16a34a' }}">
                {{ old('caution_gardee_par_agence', $contrat?->caution_gardee_par_agence ?? false)
                    ? 'L\'agence conserve la caution — non reversée au bailleur'
                    : 'Caution reversée au bailleur lors du premier versement' }}
            </span>
        </span>
    </label>
</div>

{{-- JS fiscal ────────────────────────────────────────────────────────────── --}}
<script>
function updateFiscalBadge() {
    const checked = document.getElementById('loyer_assujetti_tva').checked;
    const badge   = document.getElementById('badge-tva-loyer');
    const label   = document.getElementById('badge-tva-label');
    const champTaux = document.getElementById('champ-taux-tva-loyer');

    label.textContent = checked ? 'TVA 18% applicable' : 'Exonéré';
    badge.style.background = checked ? '#fef3c7' : '#dcfce7';
    badge.style.color      = checked ? '#d97706'  : '#16a34a';
    champTaux.style.display = checked ? 'flex' : 'none';
}

function toggleCautionLabel() {
    const cb    = document.getElementById('caution_gardee_par_agence');
    const label = document.getElementById('caution-policy-label');
    const track = document.getElementById('switch-track');
    const knob  = document.getElementById('switch-knob');
    if (cb.checked) {
        track.style.background = '#c9a84c';
        knob.style.left        = '21px';
        label.style.color      = '#8a6e2f';
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

    champ.style.display = checked ? 'flex' : 'none';
    if (note) note.style.display = checked ? '' : 'none';
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
    alerte.style.display = (typeBail === 'commercial' || typeBail === 'mixte') && !brsOk ? 'block' : 'none';
}

// Exposée globalement pour que chargerInfosBien() puisse l'appeler
window.verifierAlerteBrsCommercial = verifierAlerteBrsCommercial;
</script>