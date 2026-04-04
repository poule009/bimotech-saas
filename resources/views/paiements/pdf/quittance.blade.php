<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<style>
/* ══════════════════════════════════════════════
   BASE — DomPDF : DejaVu Sans uniquement
   Pas de flexbox ni grid
══════════════════════════════════════════════ */
* { margin:0; padding:0; box-sizing:border-box; }

body {
    font-family: "DejaVu Sans", Arial, sans-serif;
    font-size: 10px;
    color: #1a202c;
    background: #fff;
    line-height: 1.5;
}

/* ── FILIGRANE ── */
.watermark {
    position: fixed;
    top: 40%;
    left: 50%;
    transform: translate(-50%, -50%) rotate(-38deg);
    font-size: 64px;
    font-weight: bold;
    color: rgba(13, 17, 23, 0.03);
    letter-spacing: 12px;
    text-transform: uppercase;
    white-space: nowrap;
    z-index: 0;
}

/* ══════════════════════════════════════════════
   EN-TÊTE SOMBRE
══════════════════════════════════════════════ */
.header {
    background: #0d1117;
    padding: 0;
    position: relative;
}

.header-inner {
    display: table;
    width: 100%;
    padding: 22px 30px;
}

.header-left  { display: table-cell; width: 60%; vertical-align: middle; }
.header-right { display: table-cell; width: 40%; vertical-align: middle; text-align: right; }

/* Logo / nom agence */
.agence-nom {
    font-size: 20px;
    font-weight: bold;
    color: #ffffff;
    letter-spacing: 0.3px;
    margin-bottom: 3px;
}
.agence-activite {
    font-size: 8.5px;
    color: rgba(255,255,255,0.4);
    letter-spacing: 2px;
    text-transform: uppercase;
    margin-bottom: 5px;
}
.agence-mandataire-pill {
    display: inline-block;
    background: rgba(201,168,76,0.15);
    border: 1px solid rgba(201,168,76,0.3);
    border-radius: 4px;
    padding: 2px 10px;
    font-size: 8px;
    color: #c9a84c;
    letter-spacing: 0.5px;
}

/* Coordonnées à droite */
.agence-coords {
    font-size: 9px;
    color: rgba(255,255,255,0.55);
    line-height: 1.8;
    margin-bottom: 5px;
}
.ninea-pill {
    display: inline-block;
    background: rgba(201,168,76,0.12);
    border: 1px solid rgba(201,168,76,0.25);
    border-radius: 3px;
    padding: 2px 8px;
    font-size: 8px;
    font-weight: bold;
    color: #c9a84c;
    letter-spacing: 0.5px;
}

/* ── BANDE TITRE ── */
.title-band {
    display: table;
    width: 100%;
    background: #f5e9c9;
    border-left: 5px solid #c9a84c;
    padding: 10px 30px;
}
.title-band-left  { display: table-cell; vertical-align: middle; }
.title-band-right { display: table-cell; vertical-align: middle; text-align: right; }

.doc-title {
    font-size: 14px;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 2.5px;
    color: #0d1117;
}
.doc-periode {
    font-size: 10px;
    color: #8a6e2f;
    margin-top: 2px;
    font-weight: bold;
}

.ref-label {
    font-size: 7px;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    color: #8a6e2f;
    margin-bottom: 1px;
}
.ref-value {
    font-size: 10px;
    font-weight: bold;
    color: #0d1117;
}

/* ── BANDEAU BAIL ── */
.bail-band {
    display: table;
    width: 100%;
    background: #f9fafb;
    border-top: 1px solid #e5e7eb;
    border-bottom: 1px solid #e5e7eb;
}
.bail-cell {
    display: table-cell;
    padding: 8px 30px;
    border-right: 1px solid #e5e7eb;
    vertical-align: middle;
}
.bail-cell:last-child { border-right: none; }
.bail-label {
    font-size: 7px;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    color: #9ca3af;
    margin-bottom: 2px;
}
.bail-value {
    font-size: 10px;
    font-weight: bold;
    color: #0d1117;
}

/* ══════════════════════════════════════════════
   CORPS
══════════════════════════════════════════════ */
.body {
    padding: 20px 30px;
}

/* ── SECTION TITRE ── */
.section-title {
    font-size: 8.5px;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: #6b7280;
    border-bottom: 1px solid #e5e7eb;
    padding-bottom: 4px;
    margin-bottom: 10px;
    margin-top: 16px;
}
.section-title:first-child { margin-top: 0; }

/* ── GRILLE 2 COL ── */
.grid2 {
    display: table;
    width: 100%;
    border-collapse: collapse;
}
.col {
    display: table-cell;
    width: 50%;
    padding-right: 20px;
    vertical-align: top;
}
.col:last-child { padding-right: 0; padding-left: 20px; }

.col-label {
    font-size: 8px;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    color: #9ca3af;
    margin-bottom: 2px;
}
.col-value {
    font-size: 11px;
    font-weight: bold;
    color: #0d1117;
    margin-bottom: 2px;
}
.col-sub {
    font-size: 9px;
    color: #6b7280;
    line-height: 1.6;
}

/* ── PARTIE AGENCE ── */
.agence-partie {
    display: table;
    width: 100%;
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 5px;
    padding: 10px 14px;
    margin-bottom: 5px;
}
.agence-partie-lbl {
    font-size: 8px;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    color: #c9a84c;
    margin-bottom: 3px;
    font-weight: bold;
}
.agence-partie-val {
    font-size: 10.5px;
    font-weight: bold;
    color: #0d1117;
}
.agence-partie-sub {
    font-size: 9px;
    color: #6b7280;
    line-height: 1.6;
    margin-top: 2px;
}

/* Séparateur "représenté par" */
.represente-par {
    display: table;
    width: 100%;
    text-align: center;
    font-size: 8px;
    color: #9ca3af;
    font-style: italic;
    margin: 6px 0;
}

/* ── BIEN LOUÉ ── */
.bien-bloc {
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-left: 4px solid #c9a84c;
    border-radius: 0 5px 5px 0;
    padding: 10px 14px;
}
.bien-ref {
    font-size: 12px;
    font-weight: bold;
    color: #0d1117;
    margin-bottom: 2px;
}
.bien-adresse {
    font-size: 10px;
    color: #374151;
    margin-bottom: 1px;
}
.bien-details {
    font-size: 9px;
    color: #9ca3af;
}

/* ══════════════════════════════════════════════
   TABLEAU DÉCOMPTE
══════════════════════════════════════════════ */
.montant-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 10px;
    margin-top: 6px;
}
.montant-table th {
    background: #0d1117;
    color: #fff;
    padding: 8px 14px;
    text-align: left;
    font-size: 8.5px;
    text-transform: uppercase;
    letter-spacing: 0.8px;
}
.montant-table th:last-child { text-align: right; }

.montant-table td {
    padding: 7px 14px;
    border-bottom: 1px solid #f3f4f6;
    color: #374151;
    vertical-align: middle;
}
.montant-table td:last-child {
    text-align: right;
    font-weight: bold;
    color: #0d1117;
    white-space: nowrap;
}

/* Loyer nu */
.row-loyer-nu td { background: #fff; }

/* Charges / TOM */
.row-charges td,
.row-tom td {
    font-size: 9.5px;
    color: #6b7280;
    background: #fafafa;
}
.row-charges td:last-child,
.row-tom td:last-child { color: #6b7280; font-weight: normal; }

/* Sous-total encaissé */
.row-subtotal td {
    background: #f5e9c9;
    font-weight: bold;
    font-size: 11px;
    color: #0d1117;
    border-top: 1.5px solid #c9a84c;
    border-bottom: 1.5px solid #c9a84c;
}

/* Séparateur commission */
.row-sep th {
    background: #f3f4f6;
    color: #6b7280;
    font-size: 7.5px;
    padding: 5px 14px;
    letter-spacing: 0.5px;
}

/* Commission HT */
.row-comm td { font-size: 9.5px; color: #374151; }
.row-comm td:last-child { color: #8a6e2f; }

/* TVA */
.row-tva td {
    font-size: 9px;
    color: #9ca3af;
    font-style: italic;
    background: #fafafa;
}
.row-tva td:last-child { color: #9ca3af; font-weight: normal; }

/* Commission TTC */
.row-comm-ttc td { color: #d97706; font-size: 10px; }

/* NET PROPRIÉTAIRE */
.row-net td {
    background: #0d1117;
    color: #4ade80;
    font-size: 12px;
    font-weight: bold;
    padding: 10px 14px;
    border-bottom: none;
    border-top: none;
}

/* ── MONTANTS EN LETTRES ── */
.lettres-bloc {
    background: #fffbeb;
    border: 1px solid #fde68a;
    border-radius: 4px;
    padding: 9px 13px;
    margin-top: 8px;
}
.lettres-label {
    font-size: 7.5px;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    color: #d97706;
    margin-bottom: 2px;
}
.lettres-value {
    font-size: 10px;
    font-style: italic;
    font-weight: bold;
    color: #78350f;
}

.lettres-green {
    background: #f0fdf4;
    border-color: #86efac;
    margin-top: 5px;
}
.lettres-green .lettres-label { color: #16a34a; }
.lettres-green .lettres-value { color: #14532d; }

/* Caution premier paiement */
.caution-bloc {
    background: #eff6ff;
    border-left: 4px solid #3b82f6;
    border-radius: 0 4px 4px 0;
    padding: 10px 13px;
    margin-top: 8px;
    font-size: 9.5px;
    color: #1e3a8a;
    line-height: 1.6;
}

/* ── MODE DE RÈGLEMENT ── */
.reglement-bloc {
    display: table;
    width: 100%;
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 5px;
    padding: 10px 14px;
    margin-top: 6px;
}
.reg-col { display: table-cell; width: 50%; vertical-align: top; }

.valide-chip {
    display: inline-block;
    background: #dcfce7;
    border: 1px solid #86efac;
    border-radius: 99px;
    padding: 2px 10px;
    font-size: 8.5px;
    font-weight: bold;
    color: #16a34a;
    margin-top: 4px;
}

/* ══════════════════════════════════════════════
   MENTIONS LÉGALES
══════════════════════════════════════════════ */
.mentions {
    background: #fff7ed;
    border: 1px solid #fed7aa;
    border-radius: 4px;
    padding: 10px 14px;
    margin: 14px 30px 0;
    font-size: 8px;
    color: #9a3412;
    line-height: 1.7;
}
.mentions-titre {
    font-size: 8px;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 4px;
    color: #7c2d12;
}

/* ══════════════════════════════════════════════
   PIED DE PAGE
══════════════════════════════════════════════ */
.footer {
    margin: 14px 30px 22px;
    padding-top: 12px;
    border-top: 1px solid #e5e7eb;
}
.footer-grid {
    display: table;
    width: 100%;
    border-collapse: separate;
    border-spacing: 10px 0;
}
.footer-col-legal {
    display: table-cell;
    width: 55%;
    vertical-align: bottom;
}
.footer-col-doc {
    display: table-cell;
    width: 20%;
    vertical-align: bottom;
    text-align: center;
}
.footer-col-sign {
    display: table-cell;
    width: 25%;
    vertical-align: bottom;
    text-align: center;
}

.legal-text {
    font-size: 7.5px;
    color: #9ca3af;
    line-height: 1.7;
}

/* Boîte référence */
.doc-ref-box {
    border: 1px solid #e5e7eb;
    border-radius: 5px;
    padding: 9px 10px;
    background: #f9fafb;
}
.doc-ref-label {
    font-size: 7px;
    color: #9ca3af;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 3px;
}
.doc-ref-value {
    font-size: 8.5px;
    font-weight: bold;
    color: #374151;
    word-break: break-all;
}

/* Tampon */
.sign-box {
    border: 1.5px dashed #d1d5db;
    border-radius: 6px;
    padding: 14px 10px 8px;
    text-align: center;
}
.tampon {
    width: 62px;
    height: 62px;
    border: 2.5px solid #0d1117;
    border-radius: 50%;
    margin: 0 auto 5px;
    display: table;
    vertical-align: middle;
    text-align: center;
}
.tampon-inner {
    display: table-cell;
    vertical-align: middle;
    font-size: 7px;
    font-weight: bold;
    color: #0d1117;
    line-height: 1.4;
    text-transform: uppercase;
}
.sign-label {
    font-size: 7.5px;
    color: #9ca3af;
    margin-top: 4px;
}
</style>
</head>
<body>

<div class="watermark">QUITTANCE OFFICIELLE</div>

{{-- ══════════════════
     EN-TÊTE
══════════════════ --}}
<div class="header">
    <div class="header-inner">
        <div class="header-left">
            @if(!empty($agence['logo_path']) && file_exists(storage_path('app/public/'.$agence['logo_path'])))
                <img src="{{ storage_path('app/public/'.$agence['logo_path']) }}"
                     style="height:36px;width:auto;margin-bottom:6px;display:block">
            @endif
            <div class="agence-nom">{{ $agence['nom'] }}</div>
            <div class="agence-activite">Agence Immobilière · Gestion Locative</div>
            <div class="agence-mandataire-pill">Mandataire du Propriétaire Bailleur</div>
        </div>
        <div class="header-right">
            <div class="agence-coords">
                {{ $agence['adresse'] }}<br>
                Tél : {{ $agence['telephone'] }}<br>
                {{ $agence['email'] }}
            </div>
            @if($agence['ninea'])
                <div class="ninea-pill">{{ $agence['ninea'] }}</div>
            @endif
            @if(!empty($agence['rccm']))
                <div style="margin-top:3px;font-size:8px;color:rgba(255,255,255,0.4)">RCCM : {{ $agence['rccm'] }}</div>
            @endif
        </div>
    </div>
</div>

{{-- ── BANDE TITRE ── --}}
<div class="title-band">
    <div class="title-band-left">
        <div class="doc-title">Quittance de Loyer</div>
        <div class="doc-periode">Période : {{ \Carbon\Carbon::parse($paiement->periode)->translatedFormat('F Y') }}</div>
    </div>
    <div class="title-band-right">
        <div class="ref-label">N° Quittance</div>
        <div class="ref-value">{{ $paiement->reference_paiement }}</div>
        <div class="ref-label" style="margin-top:5px">Date d'émission</div>
        <div class="ref-value">{{ now()->format('d/m/Y') }}</div>
    </div>
</div>

{{-- ── BANDEAU RÉFÉRENCE BAIL ── --}}
<div class="bail-band">
    <div class="bail-cell" style="width:33%">
        <div class="bail-label">Référence du bail</div>
        <div class="bail-value">{{ $referenceBail }}</div>
    </div>
    <div class="bail-cell" style="width:22%">
        <div class="bail-label">Date de signature</div>
        <div class="bail-value">{{ \Carbon\Carbon::parse($contrat->date_debut)->format('d/m/Y') }}</div>
    </div>
    <div class="bail-cell" style="width:22%">
        <div class="bail-label">Type de bail</div>
        <div class="bail-value">{{ ucfirst($contrat->type_bail ?? 'Habitation') }}</div>
    </div>
    <div class="bail-cell" style="width:23%">
        <div class="bail-label">Échéance</div>
        <div class="bail-value">
            {{ $contrat->date_fin ? \Carbon\Carbon::parse($contrat->date_fin)->format('d/m/Y') : 'Indéterminée' }}
        </div>
    </div>
</div>

{{-- ══════════════════
     CORPS
══════════════════ --}}
<div class="body">

    {{-- PARTIES --}}
    <div class="section-title">Parties au contrat</div>
    <div class="grid2">
        <div class="col">
            <div class="agence-partie">
                <div class="agence-partie-lbl">Bailleur (Propriétaire)</div>
                <div class="agence-partie-val">{{ $proprietaire->name }}</div>
                <div class="agence-partie-sub">
                    {{ $proprietaire->adresse ?? 'Adresse non renseignée' }}<br>
                    Tél : {{ $proprietaire->telephone ?? 'Non renseigné' }}
                </div>
            </div>
            <div class="represente-par">
                Représenté par {{ $agence['nom'] }} — Mandataire de gestion locative
            </div>
        </div>
        <div class="col" style="padding-left:20px;padding-right:0">
            <div class="agence-partie">
                <div class="agence-partie-lbl">Locataire (Preneur)</div>
                <div class="agence-partie-val">{{ $locataire->name }}</div>
                <div class="agence-partie-sub">
                    {{ $locataire->adresse ?? ($bien->adresse . ', ' . $bien->ville) }}<br>
                    Tél : {{ $locataire->telephone ?? 'Non renseigné' }}
                </div>
            </div>
        </div>
    </div>

    {{-- BIEN LOUÉ --}}
    <div class="section-title">Bien loué</div>
    <div class="bien-bloc">
        <div class="bien-ref">
            {{ $bien->type }} — Réf. {{ $bien->reference }}
            @if($bien->meuble) &nbsp;·&nbsp; <em>Meublé</em> @endif
        </div>
        <div class="bien-adresse">
            {{ $bien->adresse }}@if($bien->quartier), {{ $bien->quartier }}@endif@if($bien->commune), {{ $bien->commune }}@endif, {{ $bien->ville }}
        </div>
        @if($bien->surface_m2 || $bien->nombre_pieces)
        <div class="bien-details">
            @if($bien->surface_m2){{ $bien->surface_m2 }} m²@endif
            @if($bien->surface_m2 && $bien->nombre_pieces) &nbsp;·&nbsp; @endif
            @if($bien->nombre_pieces){{ $bien->nombre_pieces }} pièce(s)@endif
        </div>
        @endif
    </div>

    {{-- DÉCOMPTE FINANCIER --}}
    <div class="section-title">
        Décompte financier — {{ \Carbon\Carbon::parse($paiement->periode)->translatedFormat('F Y') }}
    </div>

    <table class="montant-table">
        <tr>
            <th style="width:75%">Désignation</th>
            <th style="width:25%">Montant (FCFA)</th>
        </tr>

        {{-- Loyer nu --}}
        <tr class="row-loyer-nu">
            <td>Loyer nu mensuel (hors charges et hors TOM)</td>
            <td>{{ number_format($loyer_nu, 0, ',', ' ') }}</td>
        </tr>

        {{-- Charges --}}
        @if($charges_amount > 0)
        <tr class="row-charges">
            <td>&nbsp;&nbsp;&nbsp;+ Charges locatives récupérables (eau, électricité, gardiennage...)</td>
            <td>{{ number_format($charges_amount, 0, ',', ' ') }}</td>
        </tr>
        @endif

        {{-- TOM --}}
        @if($tom_amount > 0)
        <tr class="row-tom">
            <td>&nbsp;&nbsp;&nbsp;+ TOM — Taxe sur les Ordures Ménagères (part locataire)</td>
            <td>{{ number_format($tom_amount, 0, ',', ' ') }}</td>
        </tr>
        @endif

        {{-- Total encaissé --}}
        <tr class="row-subtotal">
            <td>TOTAL ENCAISSÉ</td>
            <td>{{ number_format($total_encaisse, 0, ',', ' ') }}</td>
        </tr>

        {{-- Séparateur commission --}}
        <tr class="row-sep">
            <th colspan="2">Décompte commission d'agence — mandat de gestion locative</th>
        </tr>

        {{-- Commission HT --}}
        <tr class="row-comm">
            <td>Commission d'agence HT ({{ $paiement->taux_commission_applique }}% sur loyer nu)</td>
            <td>{{ number_format($paiement->commission_agence, 0, ',', ' ') }}</td>
        </tr>

        {{-- TVA --}}
        <tr class="row-tva">
            <td>&nbsp;&nbsp;&nbsp;↳ TVA sur commission (18% — art. 357 CGI Sénégal)</td>
            <td>{{ number_format($paiement->tva_commission, 0, ',', ' ') }}</td>
        </tr>

        {{-- Commission TTC --}}
        <tr class="row-comm-ttc">
            <td>&nbsp;&nbsp;&nbsp;↳ Commission TTC (HT + TVA)</td>
            <td>{{ number_format($paiement->commission_ttc, 0, ',', ' ') }}</td>
        </tr>

        {{-- Net propriétaire --}}
        <tr class="row-net">
            <td>NET REVERSÉ AU PROPRIÉTAIRE</td>
            <td>{{ number_format($paiement->net_proprietaire, 0, ',', ' ') }}</td>
        </tr>
    </table>

    {{-- MONTANTS EN LETTRES --}}
    <div class="lettres-bloc">
        <div class="lettres-label">Total encaissé en lettres</div>
        <div class="lettres-value">{{ $montantEnLettres }}</div>
    </div>

    @if($charges_amount > 0 || $tom_amount > 0)
    <div class="lettres-bloc" style="background:#f0f4ff;border-color:#c7d2fe;margin-top:5px">
        <div class="lettres-label" style="color:#3730a3">Loyer nu en lettres</div>
        <div class="lettres-value" style="color:#1e1b4b">{{ $loyerNuEnLettres }}</div>
    </div>
    @endif

    <div class="lettres-bloc lettres-green">
        <div class="lettres-label">Net reversé au propriétaire en lettres</div>
        <div class="lettres-value">{{ $netEnLettres }}</div>
    </div>

    {{-- Caution 1er paiement --}}
    @if($paiement->est_premier_paiement && ($paiement->caution_percue ?? 0) > 0)
    <div class="caution-bloc">
        <strong>Reçu pour solde de tout compte — Caution versée (1er paiement) :</strong><br>
        {{ number_format($paiement->caution_percue, 0, ',', ' ') }} FCFA — Restituable en fin de bail
        sous déduction des éventuels préjudices constatés lors de l'état des lieux de sortie,
        conformément aux dispositions du contrat et à la loi sénégalaise sur les baux à usage d'habitation.
    </div>
    @endif

    {{-- MODE DE RÈGLEMENT --}}
    <div class="section-title">Mode de règlement</div>
    <div class="reglement-bloc">
        <div class="reg-col">
            <div class="col-label">Mode de paiement</div>
            <div class="col-value">{{ ucfirst(str_replace('_', ' ', $paiement->mode_paiement)) }}</div>
            <div class="valide-chip">✓ Paiement validé et enregistré</div>
        </div>
        <div class="reg-col">
            <div class="col-label">Date de règlement</div>
            <div class="col-value">{{ \Carbon\Carbon::parse($paiement->date_paiement)->format('d/m/Y') }}</div>
            @if($paiement->notes)
            <div class="col-sub" style="margin-top:4px;font-style:italic;font-size:9px">
                Note : {{ $paiement->notes }}
            </div>
            @endif
        </div>
    </div>

</div>{{-- /body --}}

{{-- MENTIONS LÉGALES --}}
<div class="mentions">
    <div class="mentions-titre">⚖ Mentions légales obligatoires — République du Sénégal</div>
    La présente quittance est délivrée conformément aux dispositions de la loi n° 81-18 du 25 juin 1981
    relative aux baux à usage d'habitation au Sénégal, et du contrat de bail
    <strong>{{ $referenceBail }}</strong> signé le
    <strong>{{ \Carbon\Carbon::parse($contrat->date_debut)->format('d/m/Y') }}</strong>.
    Elle atteste que <strong>{{ $locataire->name }}</strong> s'est acquitté(e) de l'intégralité
    de son loyer pour le mois de
    <strong>{{ \Carbon\Carbon::parse($paiement->periode)->translatedFormat('F Y') }}</strong>
    concernant le bien sis : <strong>{{ $bien->adresse }}{{ $bien->quartier ? ', '.$bien->quartier : '' }}, {{ $bien->ville }}</strong>.
    Le paiement du présent loyer ne vaut pas renonciation aux créances antérieures éventuelles.
    Cette quittance ne peut être délivrée qu'après encaissement effectif du loyer.
    <strong>{{ $agence['nom'] }}</strong> agit en qualité de Mandataire du Propriétaire en vertu d'un mandat
    de gestion locative. La commission d'agence est calculée sur le loyer nu conformément à la réglementation.
    Document généré le {{ now()->format('d/m/Y à H:i') }}.
</div>

{{-- PIED DE PAGE --}}
<div class="footer">
    <div class="footer-grid">
        <div class="footer-col-legal">
            <div class="legal-text">
                <strong>{{ $agence['nom'] }}</strong><br>
                {{ $agence['adresse'] }}
                @if($agence['ninea']) · {{ $agence['ninea'] }} @endif<br>
                Tél : {{ $agence['telephone'] }} · {{ $agence['email'] }}<br><br>
                Conservez précieusement ce document. En cas de litige, cette quittance
                fait foi devant les juridictions sénégalaises compétentes.
                Toute modification manuelle annule ce document.
            </div>
        </div>
        <div class="footer-col-doc">
            <div class="doc-ref-box">
                <div class="doc-ref-label">Réf. document</div>
                <div class="doc-ref-value">{{ $paiement->reference_paiement }}</div>
                <div class="doc-ref-label" style="margin-top:5px">Bail</div>
                <div class="doc-ref-value">{{ $referenceBail }}</div>
                <div class="doc-ref-label" style="margin-top:5px">Émise le</div>
                <div class="doc-ref-value">{{ now()->format('d/m/Y') }}</div>
            </div>
        </div>
        <div class="footer-col-sign">
            <div class="sign-box">
                <div class="tampon">
                    <div class="tampon-inner">
                        {{ strtoupper(substr($agence['nom'], 0, 5)) }}<br>IMMO
                    </div>
                </div>
                <div class="sign-label">Signature &amp; Cachet<br>du Mandataire</div>
            </div>
        </div>
    </div>
</div>

</body>
</html>