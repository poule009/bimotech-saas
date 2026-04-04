

<?php
    // Valeurs par défaut pour create (contrat null) vs edit
    $typeBail     = old('type_bail', $contrat->type_bail ?? 'habitation');
    $estMeuble    = old('meuble_bien', $contrat->bien->meuble ?? false);
    $loyerAssujetti = \App\Services\FiscalService::loyerEstAssujetti($typeBail, (bool) $estMeuble);
    $brsApplicable = old('brs_applicable', $contrat->brs_applicable ?? false);
    $tauxBrsManuel = old('taux_brs_manuel', $contrat->taux_brs_manuel ?? null);
    $tvaLoyerOverride = old('taux_tva_loyer', $contrat->taux_tva_loyer ?? 18.0);
?>


<div style="font-size:12px;font-weight:700;color:#374151;text-transform:uppercase;letter-spacing:.5px;margin-bottom:12px;padding-bottom:8px;border-bottom:1px solid #e5e7eb;margin-top:24px">
    ⚖️ Paramètres fiscaux
    <span style="font-size:10px;font-weight:400;color:#9ca3af;text-transform:none;letter-spacing:0;margin-left:6px">
        Auto-calculés · modifiables en cas de situation particulière
    </span>
</div>


<div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:9px;padding:14px 16px;margin-bottom:12px" id="bloc-tva-loyer">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px">
        <div>
            <div style="font-size:13px;font-weight:600;color:#0d1117">TVA sur le loyer</div>
            <div style="font-size:11px;color:#6b7280;margin-top:2px">
                Art. 355-359 CGI SN · Auto selon type de bail et meublé/non meublé
            </div>
        </div>
        
        <span id="badge-tva-loyer"
              style="display:inline-flex;align-items:center;gap:5px;padding:4px 12px;border-radius:99px;font-size:11px;font-weight:700;<?php echo e($loyerAssujetti ? 'background:#fef3c7;color:#d97706' : 'background:#dcfce7;color:#16a34a'); ?>">
            <span id="badge-tva-dot" style="width:6px;height:6px;border-radius:50%;background:currentColor"></span>
            <span id="badge-tva-label"><?php echo e($loyerAssujetti ? 'TVA 18% applicable' : 'Exonéré'); ?></span>
        </span>
    </div>

    <div style="display:flex;align-items:center;gap:14px">
        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;color:#374151">
            <input type="hidden"  name="loyer_assujetti_tva" value="0">
            <input type="checkbox" name="loyer_assujetti_tva" id="loyer_assujetti_tva" value="1"
                   <?php echo e($loyerAssujetti ? 'checked' : ''); ?>

                   onchange="updateFiscalBadge()"
                   style="width:16px;height:16px;accent-color:#c9a84c">
            Loyer soumis à TVA 18%
        </label>

        <div id="champ-taux-tva-loyer" style="<?php echo e($loyerAssujetti ? '' : 'display:none'); ?>;display:flex;align-items:center;gap:6px">
            <label style="font-size:12px;color:#6b7280">Taux (%)</label>
            <input type="number" name="taux_tva_loyer" id="taux_tva_loyer"
                   value="<?php echo e($tvaLoyerOverride); ?>"
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
        <br>Les <strong>charges</strong> et la <strong>TOM</strong> ne sont <strong>jamais</strong> soumises à TVA (Art. 356 CGI SN).
    </div>
</div>


<div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:9px;padding:14px 16px;margin-bottom:12px" id="bloc-brs">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px">
        <div>
            <div style="font-size:13px;font-weight:600;color:#0d1117">Retenue à la Source (BRS)</div>
            <div style="font-size:11px;color:#6b7280;margin-top:2px">
                Art. 196bis CGI SN · Obligatoire si locataire = entreprise/personne morale
            </div>
        </div>
        <span id="badge-brs"
              style="display:inline-flex;align-items:center;gap:5px;padding:4px 12px;border-radius:99px;font-size:11px;font-weight:700;<?php echo e($brsApplicable ? 'background:#fee2e2;color:#dc2626' : 'background:#f3f4f6;color:#6b7280'); ?>">
            <span style="width:6px;height:6px;border-radius:50%;background:currentColor"></span>
            <?php echo e($brsApplicable ? 'BRS 15% applicable' : 'Non applicable'); ?>

        </span>
    </div>

    <div style="display:flex;align-items:center;gap:14px;flex-wrap:wrap">
        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;color:#374151">
            <input type="hidden"  name="brs_applicable" value="0">
            <input type="checkbox" name="brs_applicable" id="brs_applicable" value="1"
                   <?php echo e($brsApplicable ? 'checked' : ''); ?>

                   onchange="toggleBrsChamp()"
                   style="width:16px;height:16px;accent-color:#dc2626">
            Retenue à la source applicable
        </label>

        <div id="champ-taux-brs" style="<?php echo e($brsApplicable ? '' : 'display:none'); ?>;display:flex;align-items:center;gap:6px">
            <label style="font-size:12px;color:#6b7280">Taux override (%)</label>
            <input type="number" name="taux_brs_manuel" id="taux_brs_manuel"
                   value="<?php echo e($tauxBrsManuel); ?>"
                   min="0" max="20" step="0.5" placeholder="15"
                   style="width:70px;padding:6px 8px;border:1px solid #e5e7eb;border-radius:7px;font-size:13px;font-family:'DM Sans',sans-serif">
            <span style="font-size:11px;color:#9ca3af">Laisser vide = 15% légal</span>
        </div>
    </div>

    <div style="margin-top:10px;padding:8px 12px;background:#fff1f2;border-radius:7px;font-size:11px;color:#9f1239;line-height:1.6" id="note-brs"
         style="<?php echo e($brsApplicable ? '' : 'display:none'); ?>">
        <strong>BRS auto-activé si locataire = entreprise.</strong>
        Taux standard : 15% × loyer TTC. Peut être réduit (ex: 5%) par convention fiscale.
        Le BRS est retenu par le locataire et versé <strong>directement à la DGI</strong> — pas par l'agence.
    </div>
</div>


<div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:9px;padding:14px 16px;margin-bottom:12px">
    <div style="font-size:13px;font-weight:600;color:#0d1117;margin-bottom:4px">Enregistrement DGID</div>
    <div style="font-size:11px;color:#6b7280;margin-bottom:12px">
        Art. 442 CGI SN · Délai : 2 mois après signature · Sanction : nullité opposable aux tiers
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px">
        <div>
            <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:5px">
                Date d'enregistrement
            </label>
            <input type="date" name="date_enregistrement_dgid"
                   value="<?php echo e(old('date_enregistrement_dgid', $contrat->date_enregistrement_dgid?->format('Y-m-d') ?? '')); ?>"
                   style="width:100%;padding:8px 10px;border:1px solid #e5e7eb;border-radius:8px;font-size:13px;font-family:'DM Sans',sans-serif">
        </div>
        <div>
            <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:5px">
                N° quittance DGID
            </label>
            <input type="text" name="numero_quittance_dgid"
                   value="<?php echo e(old('numero_quittance_dgid', $contrat->numero_quittance_dgid ?? '')); ?>"
                   placeholder="Ex: DGI-2025-000123"
                   style="width:100%;padding:8px 10px;border:1px solid #e5e7eb;border-radius:8px;font-size:13px;font-family:'DM Sans',sans-serif">
        </div>
        <div>
            <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:5px">
                Montant payé (FCFA)
            </label>
            <input type="number" name="montant_droit_de_bail"
                   value="<?php echo e(old('montant_droit_de_bail', $contrat->montant_droit_de_bail ?? '')); ?>"
                   placeholder="Auto-calculé : 1% ou 2% loyer annuel"
                   min="0" step="500"
                   style="width:100%;padding:8px 10px;border:1px solid #e5e7eb;border-radius:8px;font-size:13px;font-family:'DM Sans',sans-serif">
        </div>
    </div>

    <div style="margin-top:10px">
        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:12px;color:#374151">
            <input type="checkbox" name="enregistrement_exonere" value="1"
                   <?php echo e(old('enregistrement_exonere', $contrat->enregistrement_exonere ?? false) ? 'checked' : ''); ?>

                   style="width:14px;height:14px;accent-color:#7c3aed">
            Exonéré d'enregistrement (bail public, diplomatique, ONG...)
        </label>
    </div>
</div>


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

function toggleBrsChamp() {
    const checked  = document.getElementById('brs_applicable').checked;
    const champ    = document.getElementById('champ-taux-brs');
    const badge    = document.getElementById('badge-brs');
    const note     = document.getElementById('note-brs');

    champ.style.display = checked ? 'flex' : 'none';
    if (note) note.style.display = checked ? '' : 'none';
    badge.style.background = checked ? '#fee2e2' : '#f3f4f6';
    badge.style.color      = checked ? '#dc2626' : '#6b7280';
    badge.querySelector('span + span').textContent = checked ? 'BRS 15% applicable' : 'Non applicable';
}
</script><?php /**PATH C:\Users\ph\bimotech-immo\resources\views/admin/contrats/_section-fiscal.blade.php ENDPATH**/ ?>