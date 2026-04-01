<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        /* ═══════════════════════════════════════════════════════════
           BASE
        ═══════════════════════════════════════════════════════════ */
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: "DejaVu Sans", Arial, sans-serif;
            font-size: 10.5px;
            color: #1C1C2E;
            background: #FFFFFF;
            line-height: 1.5;
        }

        /* ═══════════════════════════════════════════════════════════
           WATERMARK
        ═══════════════════════════════════════════════════════════ */
        .watermark {
            position: fixed;
            top: 42%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-38deg);
            font-size: 68px;
            font-weight: bold;
            color: rgba(30, 58, 95, 0.035);
            letter-spacing: 10px;
            text-transform: uppercase;
            white-space: nowrap;
            pointer-events: none;
            z-index: 0;
        }

        /* ═══════════════════════════════════════════════════════════
           EN-TÊTE
        ═══════════════════════════════════════════════════════════ */
        .header {
            background: #1E3A5F;
            color: #FFFFFF;
            padding: 22px 30px 18px;
        }
        .header-table {
            width: 100%;
            display: table;
            border-collapse: collapse;
        }
        .header-left {
            display: table-cell;
            width: 58%;
            vertical-align: middle;
        }
        .header-right {
            display: table-cell;
            width: 42%;
            vertical-align: middle;
            text-align: right;
        }
        .agence-nom {
            font-size: 21px;
            font-weight: bold;
            letter-spacing: .5px;
            margin-bottom: 2px;
        }
        .agence-activite {
            font-size: 9px;
            letter-spacing: 2.5px;
            text-transform: uppercase;
            opacity: .65;
            margin-bottom: 6px;
        }
        .agence-mandataire {
            display: inline-block;
            background: rgba(255,255,255,.12);
            border: 1px solid rgba(255,255,255,.25);
            border-radius: 4px;
            padding: 3px 9px;
            font-size: 8.5px;
            letter-spacing: .5px;
            color: #A5C8F0;
            margin-top: 2px;
        }
        .agence-coords {
            font-size: 9px;
            line-height: 1.8;
            opacity: .82;
        }
        .agence-ninea {
            display: inline-block;
            margin-top: 5px;
            font-size: 9px;
            font-weight: bold;
            background: rgba(251, 191, 36, .15);
            border: 1px solid rgba(251, 191, 36, .4);
            border-radius: 3px;
            padding: 2px 8px;
            color: #FCD34D;
            letter-spacing: .5px;
        }

        /* ═══════════════════════════════════════════════════════════
           BANDEAU TITRE
        ═══════════════════════════════════════════════════════════ */
        .titre-band {
            background: #EEF3FB;
            border-left: 5px solid #1E3A5F;
            padding: 12px 30px;
            display: table;
            width: 100%;
        }
        .titre-left  { display: table-cell; vertical-align: middle; }
        .titre-right { display: table-cell; vertical-align: middle; text-align: right; }
        .titre-h1 {
            font-size: 15px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #1E3A5F;
        }
        .titre-periode {
            font-size: 10px;
            color: #4B5563;
            margin-top: 2px;
        }
        .ref-label {
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: #9CA3AF;
        }
        .ref-value {
            font-size: 10.5px;
            font-weight: bold;
            color: #1E3A5F;
            margin-top: 1px;
        }

        /* ═══════════════════════════════════════════════════════════
           BANDEAU RÉFÉRENCE BAIL
        ═══════════════════════════════════════════════════════════ */
        .bail-band {
            background: #F0F4FF;
            border: 1px solid #C7D2FE;
            border-radius: 6px;
            margin: 14px 30px;
            padding: 10px 16px;
            display: table;
            width: calc(100% - 60px);
        }
        .bail-cell {
            display: table-cell;
            vertical-align: middle;
            padding-right: 24px;
            font-size: 9px;
            color: #374151;
        }
        .bail-cell:last-child { padding-right: 0; }
        .bail-label {
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: #6B7280;
            margin-bottom: 2px;
        }
        .bail-value {
            font-size: 10px;
            font-weight: bold;
            color: #1E3A5F;
        }

        /* ═══════════════════════════════════════════════════════════
           CORPS
        ═══════════════════════════════════════════════════════════ */
        .body { padding: 0 30px; }

        .section { margin-bottom: 16px; }
        .section-title {
            font-size: 8.5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #1E3A5F;
            border-bottom: 1.5px solid #1E3A5F;
            padding-bottom: 4px;
            margin-bottom: 10px;
        }

        /* ── Grille 2 colonnes ── */
        .grid2 {
            display: table;
            width: 100%;
            border-collapse: separate;
            border-spacing: 10px 0;
        }
        .col {
            display: table-cell;
            width: 50%;
            background: #F9FAFB;
            border: 1px solid #E5E7EB;
            border-radius: 5px;
            padding: 11px 13px;
            vertical-align: top;
        }
        .col-label  { font-size: 8px; text-transform: uppercase; letter-spacing: .8px; color: #9CA3AF; margin-bottom: 3px; }
        .col-value  { font-size: 11.5px; font-weight: bold; color: #111827; }
        .col-sub    { font-size: 8.5px; color: #6B7280; margin-top: 3px; line-height: 1.6; }

        /* ── Tableau ventilé ── */
        .montant-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #E5E7EB;
            border-radius: 6px;
            overflow: hidden;
        }
        .montant-table th,
        .montant-table td {
            padding: 8px 14px;
            font-size: 10.5px;
            text-align: left;
            border-bottom: 1px solid #F3F4F6;
        }
        .montant-table th {
            background: #1E3A5F;
            color: #FFFFFF;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: bold;
            border-bottom: none;
        }
        .montant-table th:last-child,
        .montant-table td:last-child {
            text-align: right;
            white-space: nowrap;
        }
        .row-loyer-nu td   { background: #FAFAFA; }
        .row-charges td    { color: #374151; background: #FAFAFA; font-size: 10px; }
        .row-tom td        { color: #374151; background: #FFF8F0; font-size: 10px; }
        .row-subtotal td   {
            background: #EEF3FB;
            font-weight: bold;
            font-size: 11px;
            color: #1E3A5F;
            border-top: 1.5px solid #C7D2FE;
            border-bottom: 1.5px solid #C7D2FE;
        }
        .row-sep th {
            background: #F3F4F6;
            color: #6B7280;
            font-size: 8px;
            letter-spacing: .5px;
            padding: 5px 14px;
        }
        .row-comm td       { font-size: 10px; }
        .row-tva td        { font-size: 9.5px; color: #9CA3AF; font-style: italic; background: #FAFAFA; }
        .row-comm-ttc td   { color: #D97706; font-size: 10px; }
        .row-total td {
            background: #1E3A5F;
            color: #FFFFFF;
            font-size: 12px;
            font-weight: bold;
            padding: 11px 14px;
            border-bottom: none;
        }
        .row-net td {
            background: #ECFDF5;
            color: #065F46;
            font-size: 11.5px;
            font-weight: bold;
            border-bottom: none;
            border-top: 2px solid #6EE7B7;
        }

        /* ── Montant en lettres ── */
        .lettres-bloc {
            background: #FFFBEB;
            border: 1px solid #FDE68A;
            border-radius: 5px;
            padding: 9px 13px;
            margin-top: 10px;
        }
        .lettres-label {
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: .8px;
            color: #D97706;
            margin-bottom: 2px;
        }
        .lettres-value {
            font-size: 10.5px;
            font-style: italic;
            font-weight: bold;
            color: #78350F;
        }
        .lettres-net {
            background: #F0FFF4;
            border-color: #6EE7B7;
            margin-top: 6px;
        }
        .lettres-net .lettres-label { color: #059669; }
        .lettres-net .lettres-value { color: #065F46; }

        /* ── Caution ── */
        .caution-bloc {
            background: #EEF2FF;
            border-left: 4px solid #6366F1;
            border-radius: 0 5px 5px 0;
            padding: 10px 13px;
            margin-top: 10px;
            font-size: 9.5px;
            color: #3730A3;
            line-height: 1.6;
        }

        /* ═══════════════════════════════════════════════════════════
           MENTIONS LÉGALES
        ═══════════════════════════════════════════════════════════ */
        .mentions {
            background: #FFF7ED;
            border: 1px solid #FED7AA;
            border-radius: 5px;
            padding: 11px 15px;
            margin: 14px 30px;
            font-size: 8.5px;
            color: #9A3412;
            line-height: 1.7;
        }
        .mentions-titre {
            font-size: 8.5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: .5px;
            margin-bottom: 4px;
            color: #7C2D12;
        }

        /* ═══════════════════════════════════════════════════════════
           PIED DE PAGE
        ═══════════════════════════════════════════════════════════ */
        .footer {
            padding: 0 30px 22px;
            margin-top: 18px;
        }
        .footer-grid {
            display: table;
            width: 100%;
            border-top: 1px solid #E5E7EB;
            padding-top: 12px;
            border-collapse: separate;
            border-spacing: 12px 0;
        }
        .footer-col-legal {
            display: table-cell;
            width: 55%;
            vertical-align: bottom;
        }
        .footer-col-sign {
            display: table-cell;
            width: 25%;
            text-align: center;
            vertical-align: bottom;
        }
        .footer-col-doc {
            display: table-cell;
            width: 20%;
            text-align: center;
            vertical-align: bottom;
        }
        .legal-text {
            font-size: 7.5px;
            color: #9CA3AF;
            line-height: 1.7;
        }
        .sign-box {
            border: 1px dashed #D1D5DB;
            border-radius: 6px;
            padding: 18px 12px 8px;
            text-align: center;
        }
        .tampon {
            width: 68px;
            height: 68px;
            border: 2.5px solid #1E3A5F;
            border-radius: 50%;
            margin: 0 auto 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #1E3A5F;
            font-size: 7.5px;
            font-weight: bold;
            text-align: center;
            line-height: 1.4;
        }
        .sign-label { font-size: 7.5px; color: #9CA3AF; margin-top: 4px; }
        .doc-ref-box {
            border: 1px solid #E5E7EB;
            border-radius: 5px;
            padding: 10px 8px;
            background: #F9FAFB;
        }
        .doc-ref-label { font-size: 7px; color: #9CA3AF; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 4px; }
        .doc-ref-value { font-size: 8.5px; font-weight: bold; color: #374151; word-break: break-all; }
    </style>
</head>
<body>

<div class="watermark">QUITTANCE OFFICIELLE</div>


<div class="header">
    <div class="header-table">
        <div class="header-left">
            <div class="agence-nom"><?php echo e($agence['nom']); ?></div>
            <div class="agence-activite">Agence Immobilière · Gestion Locative</div>
            <div class="agence-mandataire">Mandataire du Propriétaire Bailleur</div>
        </div>
        <div class="header-right">
            <div class="agence-coords">
                <?php echo e($agence['adresse']); ?><br>
                Tél : <?php echo e($agence['telephone']); ?><br>
                <?php echo e($agence['email']); ?>

            </div>
            <?php if($agence['ninea']): ?>
                <div class="agence-ninea"><?php echo e($agence['ninea']); ?></div>
            <?php endif; ?>
            <?php if(!empty($agence['rccm'])): ?>
                <div style="margin-top:3px;font-size:8.5px;color:#A5C8F0;">RCCM : <?php echo e($agence['rccm']); ?></div>
            <?php endif; ?>
        </div>
    </div>
</div>


<div class="titre-band">
    <div class="titre-left">
        <div class="titre-h1">Quittance de Loyer</div>
        <div class="titre-periode">
            Période : <?php echo e(\Carbon\Carbon::parse($paiement->periode)->translatedFormat('F Y')); ?>

        </div>
    </div>
    <div class="titre-right">
        <div class="ref-label">N° Quittance</div>
        <div class="ref-value"><?php echo e($paiement->reference_paiement); ?></div>
        <div class="ref-label" style="margin-top:6px;">Date d'émission</div>
        <div class="ref-value"><?php echo e(now()->format('d/m/Y')); ?></div>
    </div>
</div>


<div class="bail-band">
    <div class="bail-cell">
        <div class="bail-label">Référence du contrat de bail</div>
        <div class="bail-value"><?php echo e($referenceBail); ?></div>
    </div>
    <div class="bail-cell">
        <div class="bail-label">Date de signature</div>
        <div class="bail-value"><?php echo e(\Carbon\Carbon::parse($contrat->date_debut)->format('d/m/Y')); ?></div>
    </div>
    <div class="bail-cell">
        <div class="bail-label">Type de bail</div>
        <div class="bail-value"><?php echo e(ucfirst($contrat->type_bail ?? 'Habitation')); ?></div>
    </div>
    <div class="bail-cell">
        <div class="bail-label">Échéance</div>
        <div class="bail-value">
            <?php if($contrat->date_fin): ?>
                <?php echo e(\Carbon\Carbon::parse($contrat->date_fin)->format('d/m/Y')); ?>

            <?php else: ?>
                Indéterminée
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="body">

    
    <div class="section">
        <div class="section-title">Parties au contrat</div>
        <div class="grid2">
            <div class="col">
                <div class="col-label">Bailleur (Propriétaire)</div>
                <div class="col-value"><?php echo e($proprietaire->name); ?></div>
                <div class="col-sub">
                    <?php echo e($proprietaire->adresse ?? 'Adresse non renseignée'); ?><br>
                    Tél : <?php echo e($proprietaire->telephone ?? 'Non renseigné'); ?><br>
                    <em>Représenté par <?php echo e($agence['nom']); ?></em>
                </div>
            </div>
            <div class="col">
                <div class="col-label">Locataire</div>
                <div class="col-value"><?php echo e($locataire->name); ?></div>
                <div class="col-sub">
                    <?php echo e($locataire->adresse ?? ($bien->adresse . ', ' . $bien->ville)); ?><br>
                    Tél : <?php echo e($locataire->telephone ?? 'Non renseigné'); ?>

                </div>
            </div>
        </div>
    </div>

    
    <div class="section">
        <div class="section-title">Bien loué</div>
        <div class="col" style="display:block;width:auto;">
            <div class="col-value">
                <?php echo e($bien->type); ?> — Réf. <?php echo e($bien->reference); ?>

                <?php if($bien->meuble): ?> &nbsp;·&nbsp; <em>Meublé</em> <?php endif; ?>
            </div>
            <div class="col-sub">
                <?php echo e($bien->adresse); ?>

                <?php if($bien->quartier): ?>, <?php echo e($bien->quartier); ?><?php endif; ?>
                <?php if($bien->commune): ?>, <?php echo e($bien->commune); ?><?php endif; ?>
                , <?php echo e($bien->ville); ?>

                <?php if($bien->surface_m2): ?> &nbsp;·&nbsp; <?php echo e($bien->surface_m2); ?> m²<?php endif; ?>
                <?php if($bien->nombre_pieces): ?> &nbsp;·&nbsp; <?php echo e($bien->nombre_pieces); ?> pièce(s)<?php endif; ?>
            </div>
        </div>
    </div>

    
    <div class="section">
        <div class="section-title">
            Décompte financier — <?php echo e(\Carbon\Carbon::parse($paiement->periode)->translatedFormat('F Y')); ?>

        </div>

        <table class="montant-table">
            <tr>
                <th>Désignation</th>
                <th>Montant (FCFA)</th>
            </tr>

            
            <tr class="row-loyer-nu">
                <td>Loyer nu mensuel (hors charges et hors TOM)</td>
                <td><?php echo e(number_format($loyer_nu, 0, ',', ' ')); ?></td>
            </tr>

            
            <?php if($charges_amount > 0): ?>
            <tr class="row-charges">
                <td>+ Charges locatives récupérables (eau, électricité, gardiennage...)</td>
                <td><?php echo e(number_format($charges_amount, 0, ',', ' ')); ?></td>
            </tr>
            <?php endif; ?>

            
            <?php if($tom_amount > 0): ?>
            <tr class="row-tom">
                <td>+ TOM — Taxe sur les Ordures Ménagères (part locataire)</td>
                <td><?php echo e(number_format($tom_amount, 0, ',', ' ')); ?></td>
            </tr>
            <?php endif; ?>

            
            <tr class="row-subtotal">
                <td>TOTAL ENCAISSÉ</td>
                <td><?php echo e(number_format($total_encaisse, 0, ',', ' ')); ?></td>
            </tr>

            
            <tr class="row-sep">
                <th colspan="2">Décompte commission d'agence — mandat de gestion locative</th>
            </tr>

            
            <tr class="row-comm">
                <td>
                    Commission d'agence HT (<?php echo e($paiement->taux_commission_applique); ?>% sur loyer nu)
                </td>
                <td><?php echo e(number_format($paiement->commission_agence, 0, ',', ' ')); ?></td>
            </tr>

            
            <tr class="row-tva">
                <td>&nbsp;&nbsp;&nbsp;↳ TVA sur commission (18% — art. 357 CGI Sénégal)</td>
                <td><?php echo e(number_format($paiement->tva_commission, 0, ',', ' ')); ?></td>
            </tr>
            <tr class="row-comm-ttc">
                <td>&nbsp;&nbsp;&nbsp;↳ Commission TTC (HT + TVA)</td>
                <td><?php echo e(number_format($paiement->commission_ttc, 0, ',', ' ')); ?></td>
            </tr>

            
            <tr class="row-net">
                <td>NET REVERSÉ AU PROPRIÉTAIRE</td>
                <td><?php echo e(number_format($paiement->net_proprietaire, 0, ',', ' ')); ?></td>
            </tr>
        </table>

        
        <div class="lettres-bloc">
            <div class="lettres-label">Total encaissé en lettres</div>
            <div class="lettres-value"><?php echo e($montantEnLettres); ?></div>
        </div>

        
        <?php if($charges_amount > 0 || $tom_amount > 0): ?>
        <div class="lettres-bloc" style="margin-top:6px;background:#F0F4FF;border-color:#C7D2FE;">
            <div class="lettres-label" style="color:#3730A3;">Loyer nu en lettres</div>
            <div class="lettres-value" style="color:#1E1B4B;"><?php echo e($loyerNuEnLettres); ?></div>
        </div>
        <?php endif; ?>

        
        <div class="lettres-bloc lettres-net">
            <div class="lettres-label">Net propriétaire en lettres</div>
            <div class="lettres-value"><?php echo e($netEnLettres); ?></div>
        </div>

        
        <?php if($paiement->est_premier_paiement && $paiement->caution_percue > 0): ?>
        <div class="caution-bloc">
            <strong>Reçu pour solde de tout compte — Caution versée (1er paiement) :</strong><br>
            <?php echo e(number_format($paiement->caution_percue, 0, ',', ' ')); ?> FCFA
            — Restituable en fin de bail sous déduction des éventuels préjudices constatés
            lors de l'état des lieux de sortie, conformément aux dispositions du contrat
            et à la loi sénégalaise sur les baux à usage d'habitation.
        </div>
        <?php endif; ?>
    </div>

    
    <div class="section">
        <div class="section-title">Mode de règlement</div>
        <div class="grid2">
            <div class="col">
                <div class="col-label">Mode de paiement</div>
                <div class="col-value"><?php echo e(ucfirst(str_replace('_', ' ', $paiement->mode_paiement))); ?></div>
                <div class="col-sub" style="color:#059669;font-weight:bold;margin-top:5px;">
                    ✓ Paiement validé et enregistré
                </div>
            </div>
            <div class="col">
                <div class="col-label">Date de règlement</div>
                <div class="col-value"><?php echo e(\Carbon\Carbon::parse($paiement->date_paiement)->format('d/m/Y')); ?></div>
                <?php if($paiement->notes): ?>
                <div class="col-sub" style="margin-top:5px;font-style:italic;">
                    Note : <?php echo e($paiement->notes); ?>

                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

</div>


<div class="mentions">
    <div class="mentions-titre">⚖️ Mentions légales obligatoires — République du Sénégal</div>
    La présente quittance est délivrée conformément aux dispositions de la loi n° 81-18 du
    25 juin 1981 relative aux baux à usage d'habitation au Sénégal, et du contrat de bail
    <strong><?php echo e($referenceBail); ?></strong> signé le <strong><?php echo e(\Carbon\Carbon::parse($contrat->date_debut)->format('d/m/Y')); ?></strong>.
    Elle atteste que <strong><?php echo e($locataire->name); ?></strong> s'est acquitté(e) de l'intégralité
    de son loyer pour le mois de <strong><?php echo e(\Carbon\Carbon::parse($paiement->periode)->translatedFormat('F Y')); ?></strong>
    concernant le bien sis : <strong><?php echo e($bien->adresse); ?><?php echo e($bien->quartier ? ', '.$bien->quartier : ''); ?>, <?php echo e($bien->ville); ?></strong>.
    Le paiement du présent loyer ne vaut pas renonciation aux créances antérieures éventuelles.
    Cette quittance ne peut être délivrée qu'après encaissement effectif du loyer.
    <strong><?php echo e($agence['nom']); ?></strong> agit en qualité de Mandataire du Propriétaire
    en vertu d'un mandat de gestion locative.
    La commission d'agence est calculée sur le loyer nu conformément à la réglementation en vigueur.
    Document généré le <?php echo e(now()->format('d/m/Y à H:i')); ?>.
</div>


<div class="footer">
    <div class="footer-grid">
        <div class="footer-col-legal">
            <div class="legal-text">
                <strong><?php echo e($agence['nom']); ?></strong><br>
                <?php echo e($agence['adresse']); ?>

                <?php if($agence['ninea']): ?> · <?php echo e($agence['ninea']); ?> <?php endif; ?><br>
                Tél : <?php echo e($agence['telephone']); ?> · <?php echo e($agence['email']); ?><br><br>
                Conservez précieusement ce document. En cas de litige, cette quittance
                fait foi devant les juridictions sénégalaises compétentes.
                Toute modification manuelle annule ce document.
            </div>
        </div>
        <div class="footer-col-doc">
            <div class="doc-ref-box">
                <div class="doc-ref-label">Réf. document</div>
                <div class="doc-ref-value"><?php echo e($paiement->reference_paiement); ?></div>
                <div class="doc-ref-label" style="margin-top:6px;">Bail</div>
                <div class="doc-ref-value"><?php echo e($referenceBail); ?></div>
            </div>
        </div>
        <div class="footer-col-sign">
            <div class="sign-box">
                <div class="tampon">
                    <?php echo e(strtoupper(substr($agence['nom'], 0, 4))); ?><br>IMMO
                </div>
                <div class="sign-label">Signature &amp; Cachet<br>du Mandataire</div>
            </div>
        </div>
    </div>
</div>

</body>
</html><?php /**PATH C:\Users\ph\bimotech-immo\resources\views/paiements/pdf/quittance.blade.php ENDPATH**/ ?>