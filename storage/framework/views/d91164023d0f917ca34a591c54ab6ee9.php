<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title>Quittance <?php echo e($paiement->reference_paiement); ?></title>
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family:'DejaVu Sans',Arial,sans-serif; font-size:10px; color:#1f2937; background:#fff; }

/* ── En-tête ── */
.header { background:#0d1117; padding:20px 28px; margin-bottom:0; }
.header-inner { display:table; width:100%; }
.header-left  { display:table-cell; vertical-align:middle; }
.header-right { display:table-cell; vertical-align:middle; text-align:right; }
.agency-name  { font-size:18px; font-weight:700; color:#c9a84c; letter-spacing:-0.3px; }
.agency-sub   { font-size:10px; color:rgba(255,255,255,.4); margin-top:3px; }
.doc-title    { font-size:22px; font-weight:700; color:#fff; letter-spacing:1px; text-transform:uppercase; }
.doc-ref      { font-size:10px; color:rgba(255,255,255,.4); margin-top:4px; }

/* ── Bande dorée ── */
.gold-bar { background:#c9a84c; height:4px; }

/* ── Section période ── */
.periode-section { background:#f5e9c9; padding:12px 28px; display:table; width:100%; }
.periode-left  { display:table-cell; vertical-align:middle; }
.periode-right { display:table-cell; vertical-align:middle; text-align:right; }
.periode-label { font-size:9px; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:#8a6e2f; }
.periode-value { font-size:16px; font-weight:700; color:#0d1117; margin-top:2px; }
.statut-badge  { display:inline-block; background:#dcfce7; color:#16a34a; padding:4px 12px; border-radius:20px; font-size:10px; font-weight:700; }

/* ── Contenu principal ── */
.main { padding:24px 28px; }

/* ── Parties ── */
.parties-grid { display:table; width:100%; margin-bottom:20px; border-collapse:separate; }
.partie-cell  { display:table-cell; width:48%; padding:14px 16px; vertical-align:top; background:#f9fafb; border:1px solid #e5e7eb; border-radius:8px; }
.partie-spacer{ display:table-cell; width:4%; }
.partie-label { font-size:8px; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:#9ca3af; margin-bottom:6px; }
.partie-name  { font-size:12px; font-weight:700; color:#0d1117; margin-bottom:3px; }
.partie-info  { font-size:9px; color:#6b7280; line-height:1.5; }

/* ── Bien ── */
.bien-section { background:#f9fafb; border:1px solid #e5e7eb; border-radius:8px; padding:14px 16px; margin-bottom:20px; display:table; width:100%; }
.bien-left    { display:table-cell; vertical-align:middle; width:70%; }
.bien-right   { display:table-cell; vertical-align:middle; text-align:right; }
.bien-label   { font-size:8px; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:#9ca3af; margin-bottom:4px; }
.bien-ref     { font-size:13px; font-weight:700; color:#0d1117; }
.bien-adresse { font-size:9px; color:#6b7280; margin-top:2px; }
.loyer-label  { font-size:8px; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:#9ca3af; margin-bottom:4px; }
.loyer-val    { font-size:20px; font-weight:700; color:#c9a84c; }
.loyer-unit   { font-size:10px; color:#9ca3af; }

/* ── Tableau décompte ── */
.section-title { font-size:9px; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:#0d1117; border-bottom:2px solid #0d1117; padding-bottom:4px; margin-bottom:10px; }
table { width:100%; border-collapse:collapse; margin-bottom:20px; }
thead th { background:#0d1117; color:#fff; padding:7px 10px; text-align:left; font-size:8px; font-weight:700; text-transform:uppercase; letter-spacing:.5px; }
thead th.right { text-align:right; }
tbody tr:nth-child(even) { background:#f9fafb; }
tbody td { padding:7px 10px; border-bottom:1px solid #f3f4f6; font-size:9px; color:#374151; vertical-align:middle; }
tbody td.right { text-align:right; font-weight:600; }
tbody td.label { color:#6b7280; }
tfoot td { padding:8px 10px; font-size:10px; font-weight:700; }
tfoot tr.total-row td { background:#0d1117; color:#fff; }
tfoot tr.total-row td.gold { color:#c9a84c; font-size:13px; }
tfoot tr.net-row td { background:#f5e9c9; color:#8a6e2f; }
tfoot tr.net-row td.gold { color:#0d1117; font-size:12px; font-weight:700; }

/* ── Infos paiement ── */
.paiement-info { display:table; width:100%; margin-bottom:20px; }
.pi-cell { display:table-cell; width:33%; padding:10px 12px; background:#f9fafb; border:1px solid #e5e7eb; vertical-align:top; }
.pi-spacer { display:table-cell; width:2%; }
.pi-label { font-size:8px; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:#9ca3af; margin-bottom:3px; }
.pi-value { font-size:11px; font-weight:600; color:#0d1117; }

/* ── Signature & cachet ── */
.signature-section { display:table; width:100%; margin-top:10px; }
.sig-cell { display:table-cell; width:48%; text-align:center; padding:14px 16px; border:1px dashed #e5e7eb; border-radius:8px; }
.sig-spacer { display:table-cell; width:4%; }
.sig-label { font-size:8px; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:#9ca3af; margin-bottom:20px; }
.sig-line  { border-top:1px solid #e5e7eb; margin-top:30px; padding-top:6px; font-size:9px; color:#9ca3af; }

/* ── Pied de page ── */
.footer { background:#f9fafb; border-top:1px solid #e5e7eb; padding:10px 28px; display:table; width:100%; margin-top:20px; }
.footer-left  { display:table-cell; vertical-align:middle; }
.footer-right { display:table-cell; vertical-align:middle; text-align:right; }
.footer-text  { font-size:8px; color:#9ca3af; }

/* ── Mention légale ── */
.mention { background:#fef9c3; border:1px solid #fde68a; border-radius:6px; padding:8px 12px; margin-bottom:16px; font-size:8px; color:#92400e; }
</style>
</head>
<body>

<?php
    $agence       = $agence ?? auth()->user()?->agency;
    $contrat      = $paiement->contrat;
    $bien         = $contrat?->bien;
    $locataire    = $contrat?->locataire;
    $proprietaire = $bien?->proprietaire;

    $loyerHt      = (float) ($paiement->loyer_ht ?? $paiement->loyer_nu ?? 0);
    $tvaLoyer     = (float) ($paiement->tva_loyer ?? 0);
    $loyerTtc     = (float) ($paiement->loyer_ttc ?? $loyerHt);
    $charges      = (float) ($paiement->charges_amount ?? 0);
    $tom          = (float) ($paiement->tom_amount ?? 0);
    $montant      = (float) $paiement->montant_encaisse;
    $commHt       = (float) ($paiement->commission_agence ?? 0);
    $tvaComm      = (float) ($paiement->tva_commission ?? 0);
    $commTtc      = (float) ($paiement->commission_ttc ?? 0);
    $netProprio   = (float) ($paiement->net_proprietaire ?? 0);
    $netAVerser   = (float) ($paiement->net_a_verser_proprietaire ?? $netProprio);
    $brs          = (float) ($paiement->brs_amount ?? 0);
    $tauxBrs      = (float) ($paiement->taux_brs_applique ?? 0);
    $tauxComm     = (float) ($paiement->taux_commission_applique ?? 0);

    // Frais d'entrée (0 pour les paiements récurrents)
    $fraisHt      = (float) ($paiement->frais_agence_ht  ?? 0);
    $tvaFrais     = (float) ($paiement->tva_frais_agence ?? 0);
    $fraisTtc     = (float) ($paiement->frais_agence_ttc ?? 0);
    $caution      = (float) ($paiement->caution_montant  ?? 0);
    $totalInitial = (float) ($paiement->total_encaissement_initial ?? $montant);
    $estPremier   = $fraisTtc > 0 || $caution > 0;

    // Nets consolidés (persistés depuis FiscalService)
    $netLocataire = (float) ($paiement->montant_net_locataire ?? ($totalInitial - $brs));
    $netBailleur  = (float) ($paiement->montant_net_bailleur  ?? $netAVerser);

    // Proratisation réelle : loyer_ht < loyer_nu du contrat (coeff < 1)
    $loyerNuContrat = (float) ($contrat?->loyer_nu ?? $loyerHt);
    $estProratise   = $loyerNuContrat > 0 && $loyerHt < $loyerNuContrat;

    $periode      = \Carbon\Carbon::parse($paiement->periode);
    $datePaiement = $paiement->date_paiement
        ? \Carbon\Carbon::parse($paiement->date_paiement)->format('d/m/Y')
        : now()->format('d/m/Y');

    $refBail = $paiement->reference_bail ?? ($contrat?->reference_bail ?? 'BAIL-'.$contrat?->id);
?>


<div class="header">
    <div class="header-inner">
        <div class="header-left">
            <div class="agency-name"><?php echo e($agence?->name ?? 'BimoTech Immo'); ?></div>
            <div class="agency-sub">
                <?php echo e($agence?->adresse ?? ''); ?>

                <?php if($agence?->telephone): ?> · <?php echo e($agence->telephone); ?> <?php endif; ?>
                <?php if($agence?->email): ?> · <?php echo e($agence->email); ?> <?php endif; ?>
            </div>
            <?php if($agence?->ninea): ?>
            <div class="agency-sub">NINEA : <?php echo e($agence->ninea); ?></div>
            <?php endif; ?>
        </div>
        <div class="header-right">
            <div class="doc-title">Quittance de loyer</div>
            <div class="doc-ref">Réf. <?php echo e($paiement->reference_paiement); ?></div>
        </div>
    </div>
</div>
<div class="gold-bar"></div>


<div class="periode-section">
    <div class="periode-left">
        <div class="periode-label">Période concernée</div>
        <div class="periode-value"><?php echo e($periode->translatedFormat('F Y')); ?></div>
    </div>
    <div class="periode-right">
        <div class="statut-badge">Paiement validé</div>
        <div style="font-size:9px;color:#6b7280;margin-top:4px">Le <?php echo e($datePaiement); ?></div>
    </div>
</div>


<div class="main">

    
    <div class="parties-grid">
        <div class="partie-cell">
            <div class="partie-label">Bailleur (Propriétaire)</div>
            <div class="partie-name"><?php echo e($proprietaire?->name ?? '—'); ?></div>
            <div class="partie-info">
                <?php echo e($proprietaire?->email ?? ''); ?><br>
                <?php echo e($proprietaire?->telephone ?? ''); ?><br>
                <?php echo e($proprietaire?->adresse ?? ''); ?>

            </div>
        </div>
        <div class="partie-spacer"></div>
        <div class="partie-cell">
            <div class="partie-label">Preneur (Locataire)</div>
            <div class="partie-name"><?php echo e($locataire?->name ?? '—'); ?></div>
            <div class="partie-info">
                <?php echo e($locataire?->email ?? ''); ?><br>
                <?php echo e($locataire?->telephone ?? ''); ?><br>
                <?php echo e($locataire?->adresse ?? ''); ?>

            </div>
        </div>
    </div>

    
    <div class="bien-section">
        <div class="bien-left">
            <div class="bien-label">Bien loué — Réf. bail : <?php echo e($refBail); ?></div>
            <div class="bien-ref">
                <?php echo e(\App\Models\Bien::TYPES[$bien?->type] ?? $bien?->type); ?>

                — <?php echo e($bien?->reference); ?>

                <?php if($bien?->meuble): ?> (Meublé) <?php endif; ?>
            </div>
            <div class="bien-adresse"><?php echo e(implode('', array_filter([
                $bien?->adresse,
                $bien?->quartier ? ', '.$bien->quartier : null,
                $bien?->commune  ? ', '.$bien->commune  : null,
                $bien?->ville    ? ' — '.$bien->ville   : null,
            ]))); ?></div>
        </div>
        <div class="bien-right">
            <div class="loyer-label">Loyer mensuel</div>
            <div class="loyer-val"><?php echo e(number_format($montant, 0, ',', ' ')); ?></div>
            <div class="loyer-unit">FCFA encaissés</div>
        </div>
    </div>

    
    <div class="section-title">Décompte du loyer</div>
    <table>
        <thead>
            <tr>
                <th>Désignation</th>
                <th class="right">Montant (FCFA)</th>
                <th class="right">Observations</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Loyer <?php echo e($tvaLoyer > 0 ? 'HT' : 'nu'); ?><?php echo e($estProratise ? ' (proratisé)' : ''); ?></td>
                <td class="right"><?php echo e(number_format($loyerHt, 0, ',', ' ')); ?></td>
                <td class="right label">Base de calcul commission</td>
            </tr>
            <?php if($tvaLoyer > 0): ?>
            <tr>
                <td>TVA sur loyer (18% — bail commercial/meublé)</td>
                <td class="right"><?php echo e(number_format($tvaLoyer, 0, ',', ' ')); ?></td>
                <td class="right label">Art. 357 CGI SN</td>
            </tr>
            <tr>
                <td style="font-weight:600">Loyer TTC</td>
                <td class="right" style="font-weight:700"><?php echo e(number_format($loyerTtc, 0, ',', ' ')); ?></td>
                <td class="right label">—</td>
            </tr>
            <?php endif; ?>
            <?php if($charges > 0): ?>
            <tr>
                <td>Charges récupérables</td>
                <td class="right"><?php echo e(number_format($charges, 0, ',', ' ')); ?></td>
                <td class="right label">Eau, électricité, etc.</td>
            </tr>
            <?php endif; ?>
            <?php if($tom > 0): ?>
            <tr>
                <td>TOM (Taxe Ordures Ménagères)</td>
                <td class="right"><?php echo e(number_format($tom, 0, ',', ' ')); ?></td>
                <td class="right label">Part locataire</td>
            </tr>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td>Loyer encaissé (mensuel)</td>
                <td class="right gold"><?php echo e(number_format($montant, 0, ',', ' ')); ?> FCFA</td>
                <td class="right"></td>
            </tr>
        </tfoot>
    </table>

    
    <?php if($estPremier): ?>
    <div class="section-title">Frais d'entrée — Premier versement</div>
    <table>
        <thead>
            <tr>
                <th>Désignation</th>
                <th class="right">Montant (FCFA)</th>
                <th class="right">Observations</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Loyer encaissé (mensuel)</td>
                <td class="right"><?php echo e(number_format($montant, 0, ',', ' ')); ?></td>
                <td class="right label">Voir décompte ci-dessus</td>
            </tr>
            <?php if($fraisTtc > 0): ?>
            <tr>
                <td>Honoraires d'agence HT</td>
                <td class="right"><?php echo e(number_format($fraisHt, 0, ',', ' ')); ?></td>
                <td class="right label">One-shot — signature</td>
            </tr>
            <tr>
                <td>TVA honoraires (18%)</td>
                <td class="right"><?php echo e(number_format($tvaFrais, 0, ',', ' ')); ?></td>
                <td class="right label">Art. 357 CGI SN</td>
            </tr>
            <?php endif; ?>
            <?php if($caution > 0): ?>
            <tr>
                <td>Dépôt de garantie (caution)</td>
                <td class="right"><?php echo e(number_format($caution, 0, ',', ' ')); ?></td>
                <td class="right label">Non taxable — restitué à la sortie</td>
            </tr>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td>Total versé à l'entrée dans les lieux</td>
                <td class="right gold"><?php echo e(number_format($totalInitial, 0, ',', ' ')); ?> FCFA</td>
                <td class="right"></td>
            </tr>
        </tfoot>
    </table>
    <?php endif; ?>

    
    <div class="section-title">Commission agence</div>
    <table>
        <thead>
            <tr>
                <th>Désignation</th>
                <th class="right">Taux</th>
                <th class="right">Montant (FCFA)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Commission agence HT</td>
                <td class="right"><?php echo e($tauxComm); ?> %</td>
                <td class="right"><?php echo e(number_format($commHt, 0, ',', ' ')); ?></td>
            </tr>
            <tr>
                <td>TVA sur commission (18%)</td>
                <td class="right">18 %</td>
                <td class="right"><?php echo e(number_format($tvaComm, 0, ',', ' ')); ?></td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2">Commission TTC</td>
                <td class="right" style="font-weight:700;color:#c9a84c"><?php echo e(number_format($commTtc, 0, ',', ' ')); ?> FCFA</td>
            </tr>
            <?php if($brs > 0): ?>
            <tr style="background:#fff1f2">
                <td colspan="2" style="color:#9f1239">BRS — Retenue à la source (<?php echo e($tauxBrs); ?>% × loyer HT — art. 196bis CGI SN)</td>
                <td class="right" style="color:#dc2626;font-weight:700">- <?php echo e(number_format($brs, 0, ',', ' ')); ?> FCFA</td>
            </tr>
            <?php endif; ?>
            <?php if($caution > 0): ?>
            <tr>
                <td colspan="2" style="color:#6b7280;font-size:9px">Net loyer (hors caution)</td>
                <td class="right" style="color:#6b7280"><?php echo e(number_format($netAVerser, 0, ',', ' ')); ?> FCFA</td>
            </tr>
            <tr>
                <td colspan="2" style="color:#6b7280;font-size:9px">+ Caution restituable au bailleur</td>
                <td class="right" style="color:#6b7280"><?php echo e(number_format($caution, 0, ',', ' ')); ?> FCFA</td>
            </tr>
            <?php endif; ?>
            <tr class="net-row">
                <td colspan="2">Net à reverser au bailleur</td>
                <td class="right gold"><?php echo e(number_format($netBailleur, 0, ',', ' ')); ?> FCFA</td>
            </tr>
        </tfoot>
    </table>

    
    <div style="background:#0d1117;border-radius:6px;padding:12px 16px;margin-bottom:20px;display:table;width:100%">
        <div style="display:table-cell;vertical-align:middle">
            <div style="font-size:8px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:rgba(201,168,76,.6);margin-bottom:3px">
                NET À PAYER PAR LE LOCATAIRE<?php echo e($brs > 0 ? ' (après retenue BRS)' : ''); ?>

            </div>
            <?php if($brs > 0): ?>
            <div style="font-size:8px;color:rgba(255,255,255,.4)">
                Le locataire retient <?php echo e(number_format($brs, 0, ',', ' ')); ?> FCFA de BRS et le verse directement à la DGI (art. 196bis CGI SN)
            </div>
            <?php endif; ?>
        </div>
        <div style="display:table-cell;vertical-align:middle;text-align:right">
            <div style="font-size:20px;font-weight:700;color:#c9a84c;font-family:Arial,sans-serif">
                <?php echo e(number_format($netLocataire, 0, ',', ' ')); ?> FCFA
            </div>
        </div>
    </div>

    
    <div class="paiement-info">
        <div class="pi-cell">
            <div class="pi-label">Mode de paiement</div>
            <div class="pi-value">
                <?php
                    $modes = [
                        'especes'      => 'Espèces',
                        'virement'     => 'Virement bancaire',
                        'cheque'       => 'Chèque',
                        'wave'         => 'Wave',
                        'orange_money' => 'Orange Money',
                        'free_money'   => 'Free Money',
                        'e_money'      => 'E-Money',
                    ];
                ?>
                <?php echo e($modes[$paiement->mode_paiement] ?? $paiement->mode_paiement); ?>

            </div>
        </div>
        <div class="pi-spacer"></div>
        <div class="pi-cell">
            <div class="pi-label">Date de paiement</div>
            <div class="pi-value"><?php echo e($datePaiement); ?></div>
        </div>
        <div class="pi-spacer"></div>
        <div class="pi-cell">
            <div class="pi-label">Référence paiement</div>
            <div class="pi-value" style="font-size:9px"><?php echo e($paiement->reference_paiement); ?></div>
        </div>
    </div>

    <?php if($paiement->notes): ?>
    <div class="mention">
        <strong>Notes :</strong> <?php echo e($paiement->notes); ?>

    </div>
    <?php endif; ?>

    
    <div class="mention">
        Quittance délivrée conformément au contrat de bail ref. <?php echo e($refBail); ?>.
        Cette quittance atteste que le loyer du mois de <?php echo e($periode->translatedFormat('F Y')); ?>

        a été intégralement acquitté. Commission calculée conformément à l'Art. 357 du CGI SN.
        Document généré le <?php echo e(now()->format('d/m/Y')); ?> par <?php echo e($agence?->name ?? 'BimoTech Immo'); ?>.
    </div>

    
    <div class="signature-section">
        <div class="sig-cell">
            <div class="sig-label">Signature du bailleur / Cachet agence</div>
            <div class="sig-line"><?php echo e($agence?->name ?? 'BimoTech Immo'); ?></div>
        </div>
        <div class="sig-spacer"></div>
        <div class="sig-cell">
            <div class="sig-label">Signature du locataire</div>
            <div class="sig-line"><?php echo e($locataire?->name ?? ''); ?></div>
        </div>
    </div>

</div>


<div class="footer">
    <div class="footer-left">
        <div class="footer-text">
            <?php echo e($agence?->name ?? 'BimoTech Immo'); ?>

            <?php if($agence?->adresse): ?> · <?php echo e($agence->adresse); ?> <?php endif; ?>
            <?php if($agence?->telephone): ?> · <?php echo e($agence->telephone); ?> <?php endif; ?>
        </div>
        <div class="footer-text" style="margin-top:2px">
            Plateforme BimoTech Immo — Gestion immobilière conforme CGI SN
        </div>
    </div>
    <div class="footer-right">
        <div class="footer-text">Réf. <?php echo e($paiement->reference_paiement); ?></div>
        <div class="footer-text"><?php echo e(now()->format('d/m/Y')); ?></div>
    </div>
</div>

</body>
</html><?php /**PATH C:\Users\ph\bimotech-immo\resources\views/paiements/pdf/quittance.blade.php ENDPATH**/ ?>