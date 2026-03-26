<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: "DejaVu Sans", sans-serif;
            font-size: 11px;
            color: #1a1a2e;
            background: #fff;
        }

        /* ── En-tête ── */
        .header {
            background: #1E3A5F;
            color: white;
            padding: 24px 32px;
            margin-bottom: 0;
        }
        .header-inner {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        .logo-zone { }
        .logo-nom {
            font-size: 22px;
            font-weight: bold;
            letter-spacing: 1px;
        }
        .logo-slogan {
            font-size: 9px;
            opacity: 0.75;
            margin-top: 2px;
            letter-spacing: 2px;
            text-transform: uppercase;
        }
        .agence-info {
            font-size: 9px;
            opacity: 0.85;
            line-height: 1.8;
            text-align: right;
        }

        /* ── Bandeau titre ── */
        .titre-bandeau {
            background: #F0F4FF;
            border-left: 5px solid #1E3A5F;
            padding: 12px 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }
        .titre-bandeau h1 {
            font-size: 15px;
            font-weight: bold;
            color: #1E3A5F;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .ref-bloc {
            text-align: right;
        }
        .ref-label {
            font-size: 9px;
            color: #888;
            text-transform: uppercase;
        }
        .ref-value {
            font-size: 11px;
            font-weight: bold;
            color: #1E3A5F;
        }

        /* ── Sections ── */
        .body-content { padding: 0 32px; }
        .section { margin-bottom: 20px; }
        .section-title {
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #1E3A5F;
            border-bottom: 1.5px solid #1E3A5F;
            padding-bottom: 4px;
            margin-bottom: 10px;
        }

        /* ── Grille 2 colonnes ── */
        .grid-2 {
            display: table;
            width: 100%;
            border-collapse: separate;
            border-spacing: 12px 0;
        }
        .col {
            display: table-cell;
            width: 50%;
            background: #F8F9FC;
            border: 1px solid #E8ECF4;
            border-radius: 6px;
            padding: 12px 14px;
            vertical-align: top;
        }
        .col-label {
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #999;
            margin-bottom: 4px;
        }
        .col-value {
            font-size: 12px;
            font-weight: bold;
            color: #1a1a2e;
        }
        .col-sub {
            font-size: 9px;
            color: #666;
            margin-top: 3px;
            line-height: 1.6;
        }

        /* ── Tableau montants ── */
        .montant-table {
            width: 100%;
            border-collapse: collapse;
        }
        .montant-table tr td {
            padding: 9px 14px;
            border-bottom: 1px solid #F0F0F0;
            font-size: 11px;
        }
        .montant-table tr td:last-child {
            text-align: right;
            font-weight: bold;
        }
        .montant-table .row-tva td {
            color: #888;
            font-size: 10px;
            font-style: italic;
            background: #FAFAFA;
        }
        .montant-table .row-total td {
            background: #1E3A5F;
            color: white;
            font-size: 13px;
            font-weight: bold;
            padding: 12px 14px;
        }
        .montant-table .row-net td {
            background: #E8F5E9;
            color: #2E7D32;
            font-size: 12px;
            font-weight: bold;
        }

        /* ── Montant en lettres ── */
        .lettres-bloc {
            background: #FFF8E1;
            border: 1px solid #FFE082;
            border-radius: 6px;
            padding: 10px 14px;
            margin-top: 10px;
        }
        .lettres-label {
            font-size: 8px;
            text-transform: uppercase;
            color: #F59E0B;
            letter-spacing: 1px;
            margin-bottom: 3px;
        }
        .lettres-value {
            font-size: 11px;
            font-style: italic;
            color: #78350F;
            font-weight: bold;
        }

        /* ── Caution ── */
        .caution-bloc {
            background: #EEF2FF;
            border-left: 4px solid #6366F1;
            padding: 10px 14px;
            border-radius: 0 6px 6px 0;
            margin-top: 10px;
            font-size: 10px;
            color: #3730A3;
        }

        /* ── Pied de page ── */
        .footer {
            margin-top: 30px;
            padding: 0 32px 24px;
        }
        .footer-grid {
            display: table;
            width: 100%;
        }
        .footer-left {
            display: table-cell;
            width: 60%;
            vertical-align: bottom;
        }
        .footer-right {
            display: table-cell;
            width: 40%;
            text-align: center;
            vertical-align: bottom;
        }
        .legal {
            font-size: 8px;
            color: #aaa;
            line-height: 1.6;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }
        .signature-box {
            border: 1px dashed #ccc;
            border-radius: 6px;
            padding: 30px 20px 10px;
            text-align: center;
        }
        .signature-label {
            font-size: 9px;
            color: #999;
            margin-top: 8px;
        }
        .cachet {
            width: 80px;
            height: 80px;
            border: 2px solid #1E3A5F;
            border-radius: 50%;
            margin: 0 auto 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #1E3A5F;
            font-size: 8px;
            font-weight: bold;
            text-align: center;
            line-height: 1.4;
        }

        /* ── Watermark statut ── */
        .watermark {
            position: fixed;
            top: 45%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-35deg);
            font-size: 72px;
            font-weight: bold;
            color: rgba(30, 58, 95, 0.04);
            letter-spacing: 8px;
            text-transform: uppercase;
            white-space: nowrap;
            pointer-events: none;
        }
    </style>
</head>
<body>

<div class="watermark">BIMO-TECH</div>

{{-- ── EN-TÊTE ─────────────────────────────────────────────────────────────── --}}
<div class="header">
    <div class="header-inner">
        <div class="logo-zone">
            <div class="logo-nom">🏢 BIMO-Tech</div>
            <div class="logo-slogan">Immobilier · Gestion · Conseil</div>
        </div>
        <div class="agence-info">
            {{ $agence['adresse'] }}<br>
            Tél : {{ $agence['telephone'] }}<br>
            {{ $agence['email'] }}<br>
            {{ $agence['ninea'] }}
        </div>
    </div>
</div>

{{-- ── TITRE ────────────────────────────────────────────────────────────────── --}}
<div class="titre-bandeau">
    <h1>Quittance de Loyer</h1>
    <div class="ref-bloc">
        <div class="ref-label">Référence</div>
        <div class="ref-value">{{ $paiement->reference_paiement }}</div>
        <div class="ref-label" style="margin-top:4px">Date d'émission</div>
        <div class="ref-value">{{ now()->format('d/m/Y') }}</div>
    </div>
</div>

<div class="body-content">

    {{-- ── PARTIES ──────────────────────────────────────────────────────────── --}}
    <div class="section">
        <div class="section-title">Parties concernées</div>
        <div class="grid-2">
            <div class="col">
                <div class="col-label">Bailleur (Propriétaire)</div>
                <div class="col-value">{{ $proprietaire->name }}</div>
                <div class="col-sub">
                    {{ $proprietaire->adresse ?? 'Adresse non renseignée' }}<br>
                    {{ $proprietaire->telephone ?? '' }}
                </div>
            </div>
            <div class="col">
                <div class="col-label">Locataire</div>
                <div class="col-value">{{ $locataire->name }}</div>
                <div class="col-sub">
                    {{ $bien->adresse }}, {{ $bien->ville }}<br>
                    {{ $locataire->telephone ?? '' }}
                </div>
            </div>
        </div>
    </div>

    {{-- ── BIEN ─────────────────────────────────────────────────────────────── --}}
    <div class="section">
        <div class="section-title">Bien loué</div>
        <div class="col" style="display:block; width:auto;">
            <div class="col-value">
                {{ $bien->type }} — Réf. {{ $bien->reference }}
            </div>
            <div class="col-sub">
                {{ $bien->adresse }}, {{ $bien->ville }}
                @if($bien->surface_m2) · {{ $bien->surface_m2 }} m² @endif
                @if($bien->nombre_pieces) · {{ $bien->nombre_pieces }} pièce(s) @endif
                <br>
                Contrat du {{ \Carbon\Carbon::parse($contrat->date_debut)->format('d/m/Y') }}
                @if($contrat->date_fin)
                    au {{ \Carbon\Carbon::parse($contrat->date_fin)->format('d/m/Y') }}
                @else
                    · Durée indéterminée
                @endif
            </div>
        </div>
    </div>

    {{-- ── PÉRIODE ──────────────────────────────────────────────────────────── --}}
    <div class="section">
        <div class="section-title">
            Quittance pour la période :
            {{ \Carbon\Carbon::parse($paiement->periode)->translatedFormat('F Y') }}
        </div>

        <table class="montant-table">
            {{-- Loyer --}}
            <tr>
                <td>Loyer mensuel encaissé</td>
                <td>{{ number_format($paiement->montant_encaisse, 0, ',', ' ') }} FCFA</td>
            </tr>

            {{-- Commission HT --}}
            <tr>
                <td>Commission agence HT ({{ $paiement->taux_commission_applique }}%)</td>
                <td>- {{ number_format($paiement->commission_agence, 0, ',', ' ') }} FCFA</td>
            </tr>

            {{-- TVA --}}
            <tr class="row-tva">
                <td>&nbsp;&nbsp;&nbsp;↳ TVA sur commission (18%)</td>
                <td>- {{ number_format($paiement->tva_commission, 0, ',', ' ') }} FCFA</td>
            </tr>
            <tr class="row-tva">
                <td>&nbsp;&nbsp;&nbsp;↳ Commission TTC</td>
                <td>- {{ number_format($paiement->commission_ttc, 0, ',', ' ') }} FCFA</td>
            </tr>

            {{-- Net propriétaire --}}
            <tr class="row-net">
                <td>NET REVERSÉ AU PROPRIÉTAIRE</td>
                <td>{{ number_format($paiement->net_proprietaire, 0, ',', ' ') }} FCFA</td>
            </tr>

            {{-- Total encaissé --}}
            <tr class="row-total">
                <td>TOTAL ENCAISSÉ</td>
                <td>{{ number_format($paiement->montant_encaisse, 0, ',', ' ') }} FCFA</td>
            </tr>
        </table>

        {{-- Montant en lettres --}}
        <div class="lettres-bloc">
            <div class="lettres-label">Montant en lettres</div>
            <div class="lettres-value">{{ $montantEnLettres }}</div>
        </div>
        <div class="lettres-bloc" style="margin-top:6px; background:#F0FFF4; border-color:#6EE7B7;">
            <div class="lettres-label" style="color:#059669;">Net propriétaire en lettres</div>
            <div class="lettres-value" style="color:#065F46;">{{ $netEnLettres }}</div>
        </div>

        {{-- Caution --}}
        @if($paiement->est_premier_paiement && $paiement->caution_percue > 0)
        <div class="caution-bloc">
            <strong>Caution perçue (1er paiement) :</strong>
            {{ number_format($paiement->caution_percue, 0, ',', ' ') }} FCFA
            — Restituable en fin de bail selon les conditions du contrat.
        </div>
        @endif
    </div>

    {{-- ── MODE DE PAIEMENT ─────────────────────────────────────────────────── --}}
    <div class="section">
        <div class="section-title">Règlement</div>
        <div class="grid-2">
            <div class="col">
                <div class="col-label">Mode de paiement</div>
                <div class="col-value">
                    {{ ucfirst(str_replace('_', ' ', $paiement->mode_paiement)) }}
                </div>
            </div>
            <div class="col">
                <div class="col-label">Date de règlement</div>
                <div class="col-value">
                    {{ \Carbon\Carbon::parse($paiement->date_paiement)->format('d/m/Y') }}
                </div>
                <div class="col-sub" style="color:#16a34a; font-weight:bold;">
                    ✓ Paiement validé
                </div>
            </div>
        </div>
        @if($paiement->notes)
        <div style="margin-top:8px; font-size:9px; color:#666; font-style:italic;">
            Note : {{ $paiement->notes }}
        </div>
        @endif
    </div>

</div>

{{-- ── PIED DE PAGE ─────────────────────────────────────────────────────────── --}}
<div class="footer">
    <div class="footer-grid">
        <div class="footer-left">
            <div class="legal">
                Cette quittance est délivrée conformément aux dispositions du bail signé
                le {{ \Carbon\Carbon::parse($contrat->date_debut)->format('d/m/Y') }}.
                Le paiement du présent loyer ne vaut pas renonciation à d'éventuelles
                créances antérieures. Document généré le {{ now()->format('d/m/Y à H:i') }}
                par le système BIMO-Tech Immobilier.
            </div>
        </div>
        <div class="footer-right">
            <div class="signature-box">
                <div class="cachet">BIMO<br>TECH<br>IMMO</div>
                <div class="signature-label">Signature & Cachet de l'Agence</div>
            </div>
        </div>
    </div>
</div>

</body>
</html>