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
.agence-nom  { font-size:18px; font-weight:bold; color:#fff; margin-bottom:3px; }
.agence-sub  { font-size:8px; color:rgba(255,255,255,.4); text-transform:uppercase; letter-spacing:1.5px; }
.agence-ninea { font-size:8.5px; color:#c9a84c; font-weight:bold; margin-top:4px; }
.doc-title   { font-size:12px; font-weight:bold; color:#fff; margin-bottom:2px; }
.doc-sub     { font-size:9px; color:rgba(255,255,255,.5); }
.doc-tri     { font-size:22px; font-weight:bold; color:#c9a84c; margin-top:4px; }

/* Bandeau trimestre */
.tri-band { display:table; width:100%; background:#f9fafb; border-bottom:3px solid #c9a84c; padding:12px 28px; }
.tri-left  { display:table-cell; width:60%; vertical-align:middle; }
.tri-right { display:table-cell; width:40%; vertical-align:middle; text-align:right; }
.tri-label { font-size:7.5px; text-transform:uppercase; letter-spacing:.8px; color:#9ca3af; margin-bottom:2px; }
.tri-title { font-size:13px; font-weight:bold; color:#0d1117; }
.tri-sub   { font-size:9px; color:#6b7280; margin-top:2px; }
.ref-pill  { display:inline-block; background:#f5e9c9; border:1px solid #c9a84c; border-radius:4px; padding:3px 10px; font-size:8px; font-weight:bold; color:#8a6e2f; }

/* Corps */
.body { padding:16px 28px; }
.section-title { font-size:8px; font-weight:bold; text-transform:uppercase; letter-spacing:1px; color:#6b7280; border-bottom:1px solid #e5e7eb; padding-bottom:4px; margin-bottom:10px; margin-top:14px; }
.section-title:first-child { margin-top:0; }

/* Tableau principal */
.dt { width:100%; border-collapse:collapse; font-size:9px; }
.dt th { background:#0d1117; color:#fff; padding:7px 10px; text-align:left; font-size:8px; text-transform:uppercase; letter-spacing:.5px; }
.dt th.r { text-align:right; }
.dt td { padding:7px 10px; border-bottom:1px solid #f3f4f6; color:#374151; vertical-align:top; }
.dt td.r { text-align:right; font-weight:bold; }
.dt tfoot td { background:#f5e9c9; font-weight:bold; padding:8px 10px; border-top:2px solid #c9a84c; }
.dt tfoot td.r { text-align:right; color:#8a6e2f; }
.dt tr.warn td { background:#fff7ed; }

/* Avertissement NINEA */
.warn-badge { display:inline-block; background:#fff7ed; border:1px solid #fed7aa; border-radius:3px; padding:1px 5px; font-size:7.5px; font-weight:bold; color:#c2410c; }

/* Montants */
.brs { color:#dc2626; font-weight:bold; }
.net { color:#374151; font-weight:bold; }

/* KPIs */
.kpi-grid { display:table; width:100%; border-collapse:separate; border-spacing:6px 0; margin-bottom:14px; }
.kpi-cell { display:table-cell; width:33.33%; vertical-align:top; }
.kpi-box  { background:#f9fafb; border:1px solid #e5e7eb; border-radius:6px; padding:9px 12px; text-align:center; }
.kpi-box.red { background:#fff1f2; border-color:#fecaca; }
.kpi-lbl  { font-size:7.5px; font-weight:bold; text-transform:uppercase; letter-spacing:.5px; color:#6b7280; margin-bottom:4px; }
.kpi-val  { font-size:14px; font-weight:bold; color:#0d1117; }
.kpi-val.red { color:#dc2626; }

/* Mentions */
.mentions { background:#fff7ed; border:1px solid #fed7aa; border-radius:4px; padding:9px 12px; margin:10px 28px; font-size:7.5px; color:#9a3412; line-height:1.7; }

/* Footer */
.footer { margin:0 28px 20px; padding-top:10px; border-top:1px solid #e5e7eb; display:table; width:calc(100% - 56px); }
.footer-left  { display:table-cell; width:60%; vertical-align:bottom; font-size:7.5px; color:#9ca3af; line-height:1.7; }
.footer-right { display:table-cell; width:40%; vertical-align:bottom; text-align:right; }
.sign-box   { border:1.5px dashed #d1d5db; border-radius:5px; padding:10px; display:inline-block; text-align:center; min-width:100px; }
.tampon     { width:52px; height:52px; border:2.5px solid #0d1117; border-radius:50%; margin:0 auto 4px; display:table; }
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
            <div class="doc-title">État Trimestriel des Retenues à la Source</div>
            <div class="doc-sub">Art. 200 §5 CGI SN — BRS sur loyers versés à tiers</div>
            <div class="doc-tri">T{{ $trimestre }} {{ $annee }}</div>
        </div>
    </div>
</div>

{{-- BANDEAU TRIMESTRE --}}
@php
    $triMois = match($trimestre) {
        1 => 'Janvier — Mars',
        2 => 'Avril — Juin',
        3 => 'Juillet — Septembre',
        4 => 'Octobre — Décembre',
    };
@endphp
<div class="tri-band">
    <div class="tri-left">
        <div class="tri-label">Trimestre couvert</div>
        <div class="tri-title">T{{ $trimestre }} {{ $annee }} — {{ $triMois }} {{ $annee }}</div>
        <div class="tri-sub">
            {{ $lignes->count() }} bailleur(s) personne(s) physique(s) concerné(s)
        </div>
    </div>
    <div class="tri-right">
        <div style="margin-bottom:4px">
            <span class="ref-pill">BRS T{{ $trimestre }}/{{ $annee }}</span>
        </div>
        <div style="font-size:8px;color:#9ca3af">
            Date limite de dépôt :<br>
            <strong style="color:#c2410c">{{ $dateLimite->translatedFormat('d F Y') }}</strong>
        </div>
    </div>
</div>

{{-- CORPS --}}
<div class="body">

    {{-- KPIs --}}
    <div class="kpi-grid">
        <div class="kpi-cell">
            <div class="kpi-box">
                <div class="kpi-lbl">Bailleurs concernés</div>
                <div class="kpi-val">{{ $lignes->count() }}</div>
            </div>
        </div>
        <div class="kpi-cell">
            <div class="kpi-box">
                <div class="kpi-lbl">Loyers nets versés</div>
                <div class="kpi-val">{{ number_format($totalNet, 0, ',', ' ') }}</div>
            </div>
        </div>
        <div class="kpi-cell">
            <div class="kpi-box red">
                <div class="kpi-lbl">Total BRS retenu</div>
                <div class="kpi-val red">{{ number_format($totalBrs, 0, ',', ' ') }}</div>
            </div>
        </div>
    </div>

    {{-- TABLEAU DES BAILLEURS --}}
    <div class="section-title">État nominatif des versements — Bailleurs personnes physiques</div>
    <table class="dt">
        <thead>
            <tr>
                <th style="width:22%">Bailleur</th>
                <th style="width:16%">NINEA / Pièce identité</th>
                <th style="width:18%">Adresse</th>
                <th style="width:18%">Période</th>
                <th class="r" style="width:13%">Loyers nets versés</th>
                <th class="r" style="width:13%">BRS retenu</th>
            </tr>
        </thead>
        <tbody>
            @foreach($lignes as $ligne)
            <tr class="{{ $ligne['has_warning_ninea'] ? 'warn' : '' }}">
                <td>
                    <strong>{{ $ligne['nom_complet'] }}</strong>
                    @if($ligne['email'])<br><span style="font-size:8px;color:#6b7280">{{ $ligne['email'] }}</span>@endif
                </td>
                <td>
                    @if($ligne['ninea'])
                        <span style="font-family:monospace;font-size:9px;font-weight:bold">{{ $ligne['ninea'] }}</span>
                    @else
                        <span class="warn-badge">⚠ NINEA absent</span>
                        @if($ligne['cni'])<br><span style="font-size:8px">CNI : {{ $ligne['cni'] }}</span>@endif
                        @if($ligne['date_naissance'])<br><span style="font-size:8px">Né(e) : {{ \Carbon\Carbon::parse($ligne['date_naissance'])->format('d/m/Y') }}</span>@endif
                    @endif
                </td>
                <td style="font-size:8.5px">{{ $ligne['adresse'] ?: '—' }}</td>
                <td style="font-size:8.5px">
                    {{ $ligne['periode_label'] }}
                    <br><span style="color:#9ca3af;font-size:8px">{{ $ligne['nb_paiements'] }} paiement(s)</span>
                </td>
                <td class="r"><span class="net">{{ number_format($ligne['loyers_verses'], 0, ',', ' ') }}</span></td>
                <td class="r"><span class="brs">{{ number_format($ligne['brs_retenu'], 0, ',', ' ') }}</span></td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4"><strong>TOTAL T{{ $trimestre }} {{ $annee }}</strong> — {{ $lignes->count() }} bailleur(s)</td>
                <td class="r">{{ number_format($totalNet, 0, ',', ' ') }} FCFA</td>
                <td class="r">{{ number_format($totalBrs, 0, ',', ' ') }} FCFA</td>
            </tr>
        </tfoot>
    </table>

    {{-- AVERTISSEMENT NINEA si applicable --}}
    @if($lignes->where('has_warning_ninea', true)->count() > 0)
    <div style="background:#fff7ed;border:1px solid #fed7aa;border-left:3px solid #f97316;padding:7px 10px;margin-top:10px;font-size:8px;color:#92400e;line-height:1.6">
        <strong>⚠ NINEA manquant pour {{ $lignes->where('has_warning_ninea', true)->count() }} bailleur(s) :</strong>
        Conformément à l'Art. 200 §5 CGI SN, lorsque le NINEA n'est pas disponible, l'état doit comporter
        la date et le lieu de naissance ainsi que le numéro de la pièce d'identité du bénéficiaire.
        Veuillez compléter ces informations avant de remettre l'état à la DGID.
    </div>
    @endif

</div>

{{-- MENTIONS --}}
<div class="mentions">
    <strong>Document à remettre au Centre des Services Fiscaux avant le {{ $dateLimite->translatedFormat('d F Y') }}.</strong><br>
    Établi conformément à l'Art. 200 §5 du Code Général des Impôts (CGI) du Sénégal.
    Cet état couvre les versements effectués à des tiers (bailleurs personnes physiques) au titre du trimestre
    T{{ $trimestre }} {{ $annee }} ({{ $triMois }} {{ $annee }}).
    Seuls les paiements pour lesquels une Retenue à la Source (BRS) a été opérée sont inclus.
    Les bailleurs personnes morales (IS) sont exclus conformément à la réglementation.
    Généré le {{ now()->format('d/m/Y à H:i') }} par Renlio-SaaS — {{ $agency?->name }}.
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
