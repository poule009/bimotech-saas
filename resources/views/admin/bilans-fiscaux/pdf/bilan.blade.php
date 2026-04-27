<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family:"DejaVu Sans",Arial,sans-serif; font-size:10px; color:#1a202c; background:#fff; line-height:1.5; }

/* En-tête */
.header { background:#0d1117; padding:22px 28px; }
.header-table { display:table; width:100%; }
.header-left  { display:table-cell; width:60%; vertical-align:middle; }
.header-right { display:table-cell; width:40%; vertical-align:middle; text-align:right; }
.agence-nom { font-size:18px; font-weight:bold; color:#fff; margin-bottom:3px; }
.agence-sub { font-size:8px; color:rgba(255,255,255,.4); text-transform:uppercase; letter-spacing:1.5px; }
.agence-ninea { font-size:8.5px; color:#c9a84c; font-weight:bold; margin-top:4px; }
.doc-title  { font-size:15px; font-weight:bold; color:#fff; margin-bottom:2px; }
.doc-sub    { font-size:9px; color:rgba(255,255,255,.5); }
.doc-annee  { font-size:22px; font-weight:bold; color:#c9a84c; margin-top:4px; }

/* Bandeau propriétaire */
.proprio-band { display:table; width:100%; background:#f9fafb; border-bottom:3px solid #c9a84c; padding:12px 28px; }
.proprio-left  { display:table-cell; width:60%; vertical-align:middle; }
.proprio-right { display:table-cell; width:40%; vertical-align:middle; text-align:right; }
.proprio-label { font-size:7.5px; text-transform:uppercase; letter-spacing:.8px; color:#9ca3af; margin-bottom:2px; }
.proprio-name  { font-size:14px; font-weight:bold; color:#0d1117; }
.proprio-sub   { font-size:9px; color:#6b7280; margin-top:2px; }
.ref-pill { display:inline-block; background:#f5e9c9; border:1px solid #c9a84c; border-radius:4px; padding:3px 10px; font-size:8px; font-weight:bold; color:#8a6e2f; }

/* Corps */
.body { padding:18px 28px; }
.section-title { font-size:8px; font-weight:bold; text-transform:uppercase; letter-spacing:1px; color:#6b7280; border-bottom:1px solid #e5e7eb; padding-bottom:4px; margin-bottom:10px; margin-top:16px; }
.section-title:first-child { margin-top:0; }

/* KPI grid */
.kpi-grid { display:table; width:100%; border-collapse:separate; border-spacing:6px 0; margin-bottom:16px; }
.kpi-cell { display:table-cell; width:25%; vertical-align:top; }
.kpi-box { background:#f9fafb; border:1px solid #e5e7eb; border-radius:6px; padding:10px 12px; text-align:center; }
.kpi-box.gold   { background:#fdf8ef; border-color:#f5e9c9; }
.kpi-box.green  { background:#f0fdf4; border-color:#bbf7d0; }
.kpi-box.red    { background:#fff1f2; border-color:#fecaca; }
.kpi-box.blue   { background:#eff6ff; border-color:#bfdbfe; }
.kpi-lbl { font-size:7.5px; font-weight:bold; text-transform:uppercase; letter-spacing:.5px; color:#6b7280; margin-bottom:5px; }
.kpi-val { font-size:14px; font-weight:bold; color:#0d1117; }
.kpi-val.gold  { color:#8a6e2f; }
.kpi-val.green { color:#16a34a; }
.kpi-val.red   { color:#dc2626; }
.kpi-val.blue  { color:#1d4ed8; }

/* Tables */
.calc-table { width:100%; border-collapse:collapse; font-size:10px; margin-bottom:14px; }
.calc-table th { background:#0d1117; color:#fff; padding:7px 12px; text-align:left; font-size:8px; text-transform:uppercase; letter-spacing:.5px; }
.calc-table th:last-child { text-align:right; }
.calc-table td { padding:8px 12px; border-bottom:1px solid #f3f4f6; color:#374151; }
.calc-table td:last-child { text-align:right; font-weight:bold; color:#0d1117; white-space:nowrap; }
.calc-table tr.abattement td { color:#16a34a; background:#f0fdf4; }
.calc-table tr.base td { background:#f5e9c9; font-weight:bold; font-size:11px; border-top:2px solid #c9a84c; border-bottom:2px solid #c9a84c; }
.calc-table tr.irpp td { background:#fff1f2; color:#dc2626; font-weight:bold; font-size:11px; border-top:1.5px solid #fecaca; }
.calc-table tr.cfpb td { background:#fff7ed; color:#d97706; }
.calc-table tr.sub td { font-size:9px; color:#9ca3af; font-style:italic; background:#fafafa; }

/* Barème */
.bareme-table { width:100%; border-collapse:collapse; font-size:9.5px; margin-bottom:12px; }
.bareme-table th { background:#f3f4f6; padding:6px 10px; text-align:left; font-size:8px; text-transform:uppercase; color:#6b7280; letter-spacing:.5px; }
.bareme-table th:last-child { text-align:right; }
.bareme-table td { padding:7px 10px; border-bottom:1px solid #f3f4f6; color:#374151; }
.bareme-table td:last-child { text-align:right; font-weight:bold; }
.bareme-table tr.active td { background:#f0fdf4; color:#16a34a; font-weight:bold; }
.bareme-table tr.inactive td { color:#9ca3af; }

/* Paiements */
.dt { width:100%; border-collapse:collapse; font-size:9px; }
.dt th { background:#f9fafb; padding:6px 10px; text-align:left; font-size:8px; text-transform:uppercase; letter-spacing:.5px; color:#9ca3af; border-bottom:1px solid #e5e7eb; }
.dt th.r { text-align:right; }
.dt td { padding:6px 10px; border-bottom:1px solid #f8f8f8; color:#374151; }
.dt td.r { text-align:right; font-weight:bold; }
.dt tfoot td { background:#f5e9c9; font-weight:bold; padding:7px 10px; border-top:2px solid #c9a84c; }
.dt tfoot td.r { text-align:right; }

/* Mentions */
.mentions { background:#fff7ed; border:1px solid #fed7aa; border-radius:4px; padding:9px 12px; margin:12px 28px; font-size:7.5px; color:#9a3412; line-height:1.7; }

/* Footer */
.footer { margin:0 28px 20px; padding-top:10px; border-top:1px solid #e5e7eb; display:table; width:calc(100% - 56px); }
.footer-left  { display:table-cell; width:60%; vertical-align:bottom; font-size:7.5px; color:#9ca3af; line-height:1.7; }
.footer-right { display:table-cell; width:40%; vertical-align:bottom; text-align:right; }
.sign-box { border:1.5px dashed #d1d5db; border-radius:5px; padding:10px; display:inline-block; text-align:center; min-width:100px; }
.tampon { width:52px; height:52px; border:2.5px solid #0d1117; border-radius:50%; margin:0 auto 4px; display:table; }
.tampon-inner { display:table-cell; vertical-align:middle; font-size:7px; font-weight:bold; color:#0d1117; text-align:center; line-height:1.3; text-transform:uppercase; }
.sign-label { font-size:7px; color:#9ca3af; }
</style>
</head>
<body>

{{-- EN-TÊTE --}}
<div class="header">
    <div class="header-table">
        <div class="header-left">
            <div class="agence-nom">{{ $agency?->name ?? 'Agence Immobilière' }}</div>
            <div class="agence-sub">Agence Immobilière · Gestion Locative</div>
            @if($agency?->ninea)<div class="agence-ninea">NINEA : {{ $agency->ninea }}</div>@endif
            @if($agency?->adresse)<div style="font-size:8.5px;color:rgba(255,255,255,.4);margin-top:3px">{{ $agency->adresse }}</div>@endif
        </div>
        <div class="header-right">
            <div class="doc-title">Bilan Fiscal Propriétaire</div>
            <div class="doc-sub">Déclaration revenus fonciers — CGI Sénégal</div>
            <div class="doc-annee">{{ $annee }}</div>
        </div>
    </div>
</div>

{{-- BANDEAU PROPRIÉTAIRE --}}
<div class="proprio-band">
    <div class="proprio-left">
        <div class="proprio-label">Propriétaire</div>
        <div class="proprio-name">{{ $proprietaire->name }}</div>
        <div class="proprio-sub">
            {{ $proprietaire->email }}
            @if($proprietaire->telephone) · {{ $proprietaire->telephone }} @endif
            @if($proprietaire->proprietaire?->ninea) · NINEA : {{ $proprietaire->proprietaire->ninea }} @endif
        </div>
    </div>
    <div class="proprio-right">
        <div style="margin-bottom:4px">
            <span class="ref-pill">Bilan {{ $annee }}</span>
        </div>
        <div style="font-size:8px;color:#9ca3af">
            {{ $bilan->nb_biens_geres }} bien(s) · {{ $bilan->nb_paiements }} paiement(s)<br>
            Calculé le {{ $bilan->calcule_le->format('d/m/Y à H:i') }}
        </div>
    </div>
</div>

{{-- CORPS --}}
<div class="body">

    {{-- KPI --}}
    <div class="section-title">Indicateurs annuels</div>
    <div class="kpi-grid">
        <div class="kpi-cell">
            <div class="kpi-box gold">
                <div class="kpi-lbl">Revenus bruts</div>
                <div class="kpi-val gold">{{ number_format($bilan->revenus_bruts_loyers, 0, ',', ' ') }}</div>
                <div style="font-size:8px;color:#8a6e2f;margin-top:2px">FCFA</div>
            </div>
        </div>
        <div class="kpi-cell">
            <div class="kpi-box green">
                <div class="kpi-lbl">Base imposable</div>
                <div class="kpi-val green">{{ number_format($bilan->base_imposable, 0, ',', ' ') }}</div>
                <div style="font-size:8px;color:#16a34a;margin-top:2px">Après abatt. 30%</div>
            </div>
        </div>
        <div class="kpi-cell">
            <div class="kpi-box red">
                <div class="kpi-lbl">IRPP estimé</div>
                <div class="kpi-val red">{{ number_format($bilan->irpp_estime, 0, ',', ' ') }}</div>
                <div style="font-size:8px;color:#dc2626;margin-top:2px">Art. 65 CGI SN</div>
            </div>
        </div>
        <div class="kpi-cell">
            <div class="kpi-box blue">
                <div class="kpi-lbl">Net reversé</div>
                <div class="kpi-val blue">{{ number_format($bilan->net_proprietaire_total, 0, ',', ' ') }}</div>
                <div style="font-size:8px;color:#1d4ed8;margin-top:2px">Après commissions</div>
            </div>
        </div>
    </div>

    {{-- CALCUL FISCAL --}}
    <div class="section-title">Calcul fiscal — Revenus fonciers (Art. 58-65 CGI SN)</div>
    <table class="calc-table">
        <tr><th style="width:70%">Désignation</th><th>Montant (FCFA)</th></tr>
        <tr>
            <td>Revenus bruts loyers (loyers HT annuels encaissés)</td>
            <td>{{ number_format($bilan->revenus_bruts_loyers, 0, ',', ' ') }}</td>
        </tr>
        @if($bilan->revenus_bruts_charges > 0)
        <tr class="sub">
            <td>&nbsp;&nbsp;+ Charges refacturées aux locataires (Art. 56 CGI SN)</td>
            <td>{{ number_format($bilan->revenus_bruts_charges, 0, ',', ' ') }}</td>
        </tr>
        @endif
        <tr class="abattement">
            <td>− Abattement forfaitaire 30% (Art. 58 CGI SN — frais réels présumés)</td>
            <td>− {{ number_format($bilan->abattement_forfaitaire_30, 0, ',', ' ') }}</td>
        </tr>
        <tr class="base">
            <td>= BASE IMPOSABLE (revenus nets × 70%)</td>
            <td>{{ number_format($bilan->base_imposable, 0, ',', ' ') }}</td>
        </tr>
    </table>

    {{-- BARÈME IRPP --}}
    <div class="section-title">Barème IRPP progressif (Art. 65 CGI SN)</div>
    @php
        $base = (float) $bilan->base_imposable;
        $tranches = [
            ['label' => '0 — 1 500 000',         'taux' => 0,  'min' => 0,       'max' => 1500000],
            ['label' => '1 500 001 — 4 000 000', 'taux' => 20, 'min' => 1500001, 'max' => 4000000],
            ['label' => '4 000 001 — 8 000 000', 'taux' => 30, 'min' => 4000001, 'max' => 8000000],
            ['label' => '> 8 000 000',           'taux' => 40, 'min' => 8000001, 'max' => PHP_INT_MAX],
        ];
        $totalIrpp = 0;
    @endphp
    <table class="bareme-table">
        <tr>
            <th>Tranche (FCFA)</th>
            <th>Taux</th>
            <th>Montant imposable</th>
            <th>Impôt sur tranche</th>
        </tr>
        @foreach($tranches as $t)
        @php
            $active = $base > $t['min'];
            $imposable = $active ? (min($base, $t['max'] === PHP_INT_MAX ? $base : $t['max']) - $t['min']) : 0;
            $imposable = max(0, $imposable);
            $impot = round($imposable * $t['taux'] / 100, 0);
            if($active) $totalIrpp += $impot;
        @endphp
        <tr class="{{ $active && $imposable > 0 ? 'active' : 'inactive' }}">
            <td>{{ $t['label'] }}</td>
            <td>{{ $t['taux'] }}%</td>
            <td>{{ $active && $imposable > 0 ? number_format($imposable, 0, ',', ' ').' F' : '—' }}</td>
            <td>{{ $active && $impot > 0 ? number_format($impot, 0, ',', ' ').' F' : '—' }}</td>
        </tr>
        @endforeach
    </table>

    <table class="calc-table" style="margin-bottom:14px">
        <tr><th style="width:70%">Récapitulatif</th><th>Montant</th></tr>
        <tr class="irpp">
            <td>IRPP estimé total (à déclarer — vérifier avec la DGI)</td>
            <td>{{ number_format($bilan->irpp_estime, 0, ',', ' ') }}</td>
        </tr>
        <tr class="cfpb">
            <td>CFPB estimée (Contribution Foncière — Art. 95-110 CGI SN — ~5%)</td>
            <td>{{ number_format($bilan->cfpb_estimee, 0, ',', ' ') }}</td>
        </tr>
        @if($bilan->tva_loyer_collectee > 0)
        <tr>
            <td>TVA loyer collectée à reverser DGI (Art. 355 CGI SN)</td>
            <td>{{ number_format($bilan->tva_loyer_collectee, 0, ',', ' ') }}</td>
        </tr>
        @endif
        @if($bilan->brs_retenu_total > 0)
        <tr class="sub">
            <td>&nbsp;&nbsp;BRS retenu par locataires entreprises — déjà versé DGI (Art. 196bis)</td>
            <td>{{ number_format($bilan->brs_retenu_total, 0, ',', ' ') }}</td>
        </tr>
        @endif
    </table>

    {{-- COMMISSION AGENCE --}}
    <div class="section-title">Commissions d'agence {{ $annee }}</div>
    <table class="calc-table" style="margin-bottom:16px">
        <tr><th style="width:70%">Désignation</th><th>Montant</th></tr>
        <tr>
            <td>Total commissions HT prélevées</td>
            <td>{{ number_format($bilan->commissions_agence_ht, 0, ',', ' ') }}</td>
        </tr>
        <tr class="sub">
            <td>&nbsp;&nbsp;TVA sur commissions (18%)</td>
            <td>{{ number_format($bilan->tva_commissions, 0, ',', ' ') }}</td>
        </tr>
        <tr>
            <td style="background:#f0fdf4;font-weight:bold;color:#16a34a">Net total reversé au propriétaire</td>
            <td style="background:#f0fdf4;font-weight:bold;color:#16a34a">{{ number_format($bilan->net_proprietaire_total, 0, ',', ' ') }}</td>
        </tr>
    </table>

    {{-- PAIEMENTS --}}
    <div class="section-title">Détail des paiements {{ $annee }} ({{ $bilan->nb_paiements }} paiements)</div>
    <table class="dt">
        <thead>
            <tr>
                <th>Période</th>
                <th>Bien</th>
                <th>Type</th>
                <th class="r">Loyer HT</th>
                <th class="r">TVA loyer</th>
                <th class="r">Commission</th>
                <th class="r">BRS</th>
                <th class="r">Net proprio</th>
            </tr>
        </thead>
        <tbody>
            @foreach($bilan->paiements ?? [] as $p)
            <tr>
                <td>{{ \Carbon\Carbon::parse($p->periode)->translatedFormat('M Y') }}</td>
                <td>{{ $p->bien_reference ?? '—' }}</td>
                <td>{{ ucfirst($p->type_bail ?? '—') }}{{ ($p->type_bail === 'habitation' && ($p->bien_meuble ?? false)) ? ' meublée' : '' }}</td>
                <td class="r">{{ number_format($p->loyer_ht ?? $p->loyer_nu ?? 0, 0, ',', ' ') }}</td>
                <td class="r">{{ ($p->tva_loyer ?? 0) > 0 ? number_format($p->tva_loyer, 0, ',', ' ') : '—' }}</td>
                <td class="r">{{ number_format($p->commission_ttc ?? 0, 0, ',', ' ') }}</td>
                <td class="r">{{ ($p->brs_amount ?? 0) > 0 ? number_format($p->brs_amount, 0, ',', ' ') : '—' }}</td>
                <td class="r">{{ number_format($p->net_proprietaire ?? 0, 0, ',', ' ') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3">TOTAL {{ $annee }}</td>
                <td class="r">{{ number_format($bilan->revenus_bruts_loyers, 0, ',', ' ') }}</td>
                <td class="r">{{ number_format($bilan->tva_loyer_collectee, 0, ',', ' ') }}</td>
                <td class="r">{{ number_format($bilan->commissions_agence_ht + $bilan->tva_commissions, 0, ',', ' ') }}</td>
                <td class="r">{{ number_format($bilan->brs_retenu_total, 0, ',', ' ') }}</td>
                <td class="r">{{ number_format($bilan->net_proprietaire_total, 0, ',', ' ') }}</td>
            </tr>
        </tfoot>
    </table>

</div>

{{-- MENTIONS --}}
<div class="mentions">
    <strong>⚠ Document d'estimation fiscale — République du Sénégal</strong><br>
    Ce bilan est établi sur la base des paiements de loyers enregistrés par <strong>{{ $agency?->name }}</strong>
    pour l'année <strong>{{ $annee }}</strong>. L'IRPP est calculé selon le barème progressif Art. 65 CGI SN
    après abattement forfaitaire de 30% (Art. 58 CGI SN). La CFPB est estimée à 5% de la valeur locative annuelle.
    Ce document est fourni à titre indicatif et ne constitue pas une déclaration fiscale officielle.
    <strong>Consultez un expert-comptable agréé ou la Direction Générale des Impôts et Domaines (DGID) pour toute déclaration.</strong>
    Document généré le {{ now()->format('d/m/Y à H:i') }} par {{ $agency?->name }}.
</div>

{{-- FOOTER --}}
<div class="footer">
    <div class="footer-left">
        <strong>{{ $agency?->name }}</strong><br>
        {{ $agency?->adresse }}@if($agency?->ninea) · NINEA : {{ $agency->ninea }}@endif<br>
        Tél : {{ $agency?->telephone }} · {{ $agency?->email }}
    </div>
    <div class="footer-right">
        <div class="sign-box">
            <div class="tampon">
                <div class="tampon-inner">{{ strtoupper(substr($agency?->name ?? 'BI', 0, 4)) }}<br>IMMO</div>
            </div>
            <div class="sign-label">Signature &amp; Cachet</div>
        </div>
    </div>
</div>

</body>
</html>