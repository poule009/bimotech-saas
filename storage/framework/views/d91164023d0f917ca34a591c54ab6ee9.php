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

    $loyerNu      = (float) ($paiement->loyer_nu ?? 0);
    $charges      = (float) ($paiement->charges_amount ?? 0);
    $tom          = (float) ($paiement->tom_amount ?? 0);
    $tvaLoyer     = (float) ($paiement->tva_loyer ?? 0);
    $loyerTtc     = (float) ($paiement->loyer_ttc ?? $loyerNu);
    $montant      = (float) $paiement->montant_encaisse;
    $commHt       = (float) ($paiement->commission_agence ?? 0);
    $tvaComm      = (float) ($paiement->tva_commission ?? 0);
    $commTtc      = (float) ($paiement->commission_ttc ?? 0);
    $netProprio   = (float) ($paiement->net_proprietaire ?? 0);
    $netAVerser   = (float) ($paiement->net_a_verser_proprietaire ?? $netProprio);
    $brs          = (float) ($paiement->brs_amount ?? 0);
    $tauxComm     = (float) ($paiement->taux_commission_applique ?? 0);

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
            <div class="bien-adresse">
                <?php echo e($bien?->adresse); ?>

                <?php if($bien?->quartier): ?>, <?php echo e($bien->quartier); ?> <?php endif; ?>
                <?php if($bien?->commune): ?>, <?php echo e($bien->commune); ?> <?php endif; ?>
                — <?php echo e($bien?->ville); ?>

            </div>
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
                <td>Loyer nu</td>
                <td class="right"><?php echo e(number_format($loyerNu, 0, ',', ' ')); ?></td>
                <td class="right label">Base de calcul commission</td>
            </tr>
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
            <?php if($tvaLoyer > 0): ?>
            <tr>
                <td>TVA sur loyer (18%)</td>
                <td class="right"><?php echo e(number_format($tvaLoyer, 0, ',', ' ')); ?></td>
                <td class="right label">Art. 357 CGI SN</td>
            </tr>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td>Total encaissé</td>
                <td class="right gold"><?php echo e(number_format($montant, 0, ',', ' ')); ?> FCFA</td>
                <td class="right"></td>
            </tr>
        </tfoot>
    </table>

    
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
            <?php if($brs > 0): ?>
            <tr>
                <td>BRS — Retenue à la source</td>
                <td class="right"><?php echo e($paiement->taux_brs_applique ?? 0); ?> %</td>
                <td class="right"><?php echo e(number_format($brs, 0, ',', ' ')); ?></td>
            </tr>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2">Commission TTC</td>
                <td class="right" style="font-weight:700;color:#c9a84c"><?php echo e(number_format($commTtc, 0, ',', ' ')); ?> FCFA</td>
            </tr>
            <tr class="net-row">
                <td colspan="2">Net à reverser au propriétaire</td>
                <td class="right gold"><?php echo e(number_format($netAVerser, 0, ',', ' ')); ?> FCFA</td>
            </tr>
        </tfoot>
    </table>

    
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