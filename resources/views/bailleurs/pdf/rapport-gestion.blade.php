<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Rapport de Gestion — {{ $user->name }} — {{ $periode->translatedFormat('F Y') }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 10px; color: #1f2937; background: #fff; }

        /* ── En-tête ── */
        .header { background: #0d1117; color: #fff; padding: 20px 28px; margin-bottom: 22px; }
        .header-table { display: table; width: 100%; }
        .header-left  { display: table-cell; vertical-align: middle; }
        .header-right { display: table-cell; vertical-align: middle; text-align: right; }
        .agency-name  { font-size: 10px; color: rgba(201,168,76,.6); font-weight: 700; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 4px; }
        .header h1    { font-size: 18px; font-weight: 700; color: #fff; letter-spacing: -0.3px; }
        .header h2    { font-size: 12px; font-weight: 400; color: rgba(255,255,255,.5); margin-top: 3px; }
        .periode-badge {
            display: inline-block;
            background: rgba(201,168,76,.15);
            border: 1px solid rgba(201,168,76,.35);
            border-radius: 6px;
            padding: 6px 12px;
        }
        .periode-lbl { font-size: 9px; text-transform: uppercase; letter-spacing: 1px; color: rgba(201,168,76,.6); margin-bottom: 3px; }
        .periode-val { font-size: 15px; font-weight: 700; color: #c9a84c; }

        /* ── Bailleur ── */
        .bailleur-box {
            margin: 0 28px 18px;
            padding: 12px 16px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            display: table;
            width: calc(100% - 56px);
        }
        .bailleur-col { display: table-cell; vertical-align: top; width: 50%; }
        .bl { font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: .7px; color: #9ca3af; margin-bottom: 3px; }
        .bv { font-size: 12px; font-weight: 600; color: #0d1117; }
        .bvsub { font-size: 10px; color: #6b7280; margin-top: 2px; }

        /* ── KPI ── */
        .kpi-grid {
            display: table;
            width: calc(100% - 56px);
            margin: 0 28px 18px;
            border-collapse: separate;
            border-spacing: 8px 0;
        }
        .kpi-cell { display: table-cell; width: 25%; }
        .kpi-box { padding: 10px 12px; border-radius: 7px; border: 1px solid #e5e7eb; }
        .kpi-box.gold   { border-top: 3px solid #c9a84c; }
        .kpi-box.blue   { border-top: 3px solid #1d4ed8; }
        .kpi-box.red    { border-top: 3px solid #dc2626; }
        .kpi-box.green  { border-top: 3px solid #16a34a; }
        .kpi-lbl { font-size: 8px; font-weight: 700; text-transform: uppercase; letter-spacing: .7px; color: #9ca3af; margin-bottom: 4px; }
        .kpi-val { font-size: 14px; font-weight: 700; }
        .kpi-val.gold  { color: #c9a84c; }
        .kpi-val.blue  { color: #1d4ed8; }
        .kpi-val.red   { color: #dc2626; }
        .kpi-val.green { color: #16a34a; }

        /* ── Section titre ── */
        .section-title {
            margin: 0 28px 8px;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #9ca3af;
            padding-bottom: 4px;
            border-bottom: 1px solid #e5e7eb;
        }

        /* ── Table paiements ── */
        .dt { width: calc(100% - 56px); margin: 0 28px 18px; border-collapse: collapse; }
        .dt th {
            padding: 7px 10px;
            text-align: left;
            font-size: 8px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .6px;
            color: #6b7280;
            background: #f9fafb;
            border-bottom: 1px solid #e5e7eb;
        }
        .dt td {
            padding: 8px 10px;
            font-size: 10px;
            color: #374151;
            border-bottom: 1px solid #f3f4f6;
        }
        .dt tr:last-child td { border-bottom: none; }
        .dt .tr-total td { background: #f9fafb; font-weight: 700; border-top: 2px solid #e5e7eb; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }

        /* ── Ligne nette finale ── */
        .net-final-box {
            margin: 0 28px 22px;
            padding: 14px 18px;
            background: #0d1117;
            border-radius: 8px;
            display: table;
            width: calc(100% - 56px);
        }
        .net-left  { display: table-cell; vertical-align: middle; }
        .net-right { display: table-cell; vertical-align: middle; text-align: right; }
        .net-label { font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: .8px; color: rgba(201,168,76,.6); margin-bottom: 4px; }
        .net-equa  { font-size: 10px; color: rgba(255,255,255,.35); }
        .net-value { font-size: 22px; font-weight: 700; color: #c9a84c; }

        /* ── Pied de page ── */
        .footer {
            position: fixed;
            bottom: 0; left: 0; right: 0;
            padding: 8px 28px;
            background: #f9fafb;
            border-top: 1px solid #e5e7eb;
            display: table;
            width: 100%;
        }
        .footer-left  { display: table-cell; font-size: 8px; color: #9ca3af; vertical-align: middle; }
        .footer-right { display: table-cell; text-align: right; font-size: 8px; color: #9ca3af; vertical-align: middle; }

        .badge-periode {
            display: inline-block;
            padding: 2px 7px;
            background: #f5e9c9;
            color: #8a6e2f;
            border-radius: 4px;
            font-size: 9px;
            font-weight: 700;
        }
    </style>
</head>
<body>

{{-- Pied de page fixe --}}
<div class="footer">
    <div class="footer-left">
        {{ $agency->name ?? 'BIMO-tech' }} · Rapport de gestion isolé
        · Généré le {{ now()->format('d/m/Y à H:i') }}
    </div>
    <div class="footer-right">Document confidentiel — Usage exclusif du bailleur</div>
</div>

{{-- En-tête ──────────────────────────────────────────── --}}
<div class="header">
    <div class="header-table">
        <div class="header-left">
            <div class="agency-name">{{ $agency->name ?? 'BIMO-tech' }}</div>
            <h1>Rapport de Gestion {{ $mois !== null ? 'Mensuel' : 'Annuel' }}</h1>
            <h2>Document isolé · Données exclusives du bailleur</h2>
        </div>
        <div class="header-right">
            <div class="periode-badge">
                <div class="periode-lbl">Période</div>
                <div class="periode-val">{{ $mois !== null ? $periode->translatedFormat('F Y') : 'Année '.$annee }}</div>
            </div>
        </div>
    </div>
</div>

{{-- Bailleur ──────────────────────────────────────────── --}}
<div class="bailleur-box">
    <div class="bailleur-col">
        <div class="bl">Propriétaire / Bailleur</div>
        <div class="bv">{{ $user->name }}</div>
        <div class="bvsub">{{ $user->email }}</div>
        @if($user->telephone)
        <div class="bvsub">{{ $user->telephone }}</div>
        @endif
    </div>
    <div class="bailleur-col" style="text-align:right">
        @php $profil = $user->proprietaire; @endphp
        @if($profil?->ninea)
        <div class="bl">NINEA</div>
        <div class="bv">{{ $profil->ninea }}</div>
        @endif
        @if($profil?->mode_paiement_prefere)
        <div class="bvsub" style="margin-top:4px">Mode règlement : {{ $profil->mode_paiement_prefere }}</div>
        @endif
    </div>
</div>

{{-- KPIs ────────────────────────────────────────────── --}}
<table class="kpi-grid">
    <tr>
        <td class="kpi-cell">
            <div class="kpi-box gold">
                <div class="kpi-lbl">Loyers encaissés</div>
                <div class="kpi-val gold">{{ number_format($totalLoyers, 0, ',', ' ') }} F</div>
            </div>
        </td>
        <td class="kpi-cell">
            <div class="kpi-box blue">
                <div class="kpi-lbl">Commissions TTC</div>
                <div class="kpi-val blue">{{ number_format($totalCommissions, 0, ',', ' ') }} F</div>
            </div>
        </td>
        <td class="kpi-cell">
            <div class="kpi-box red">
                <div class="kpi-lbl">Dépenses gestion</div>
                <div class="kpi-val red">{{ number_format($totalDepenses, 0, ',', ' ') }} F</div>
            </div>
        </td>
        <td class="kpi-cell">
            <div class="kpi-box green">
                <div class="kpi-lbl">Net à verser</div>
                <div class="kpi-val green">{{ number_format($netFinal, 0, ',', ' ') }} F</div>
            </div>
        </td>
    </tr>
</table>

{{-- Détail des paiements ──────────────────────────── --}}
<div class="section-title">Détail des paiements — {{ $paiements->count() }} enregistrement(s)</div>

@if($paiements->isEmpty())
<div style="margin:0 28px 18px;padding:20px;background:#f9fafb;border:1px dashed #d1d5db;border-radius:8px;text-align:center;color:#6b7280;font-size:11px;">
    Aucun paiement validé pour {{ $mois !== null ? $periode->translatedFormat('F Y') : 'l\'année '.$annee }}.
</div>
@else
@php $parMois = $paiements->groupBy(fn($p) => \Carbon\Carbon::parse($p->periode)->format('Y-m')); @endphp

<table class="dt">
    <thead>
        <tr>
            <th>Référence</th>
            <th>Bien</th>
            @if($mois === null)<th class="text-center">Période</th>@endif
            <th class="text-right">Loyer encaissé</th>
            <th class="text-right">Commission TTC</th>
            <th class="text-right">Dépenses</th>
            <th class="text-right">Net bailleur</th>
        </tr>
    </thead>
    <tbody>
        @foreach($parMois as $cle => $groupe)
        @if($mois === null)
        <tr style="background:#f3f4f6">
            <td colspan="7" style="font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:.7px;color:#6b7280;padding:6px 10px">
                {{ \Carbon\Carbon::createFromFormat('Y-m', $cle)->translatedFormat('F Y') }}
                — {{ $groupe->count() }} paiement(s)
            </td>
        </tr>
        @endif
        @foreach($groupe as $p)
        @php
            $depMois  = (float) $p->depenses->sum('montant');
            $netB     = (float)($p->net_a_verser_proprietaire ?? 0);
            $netLigne = round($netB - $depMois, 2);
        @endphp
        <tr>
            <td style="font-size:9px;color:#9ca3af">{{ $p->reference_paiement }}</td>
            <td>{{ $p->contrat?->bien?->reference ?? '—' }}</td>
            @if($mois === null)
            <td class="text-center">
                <span class="badge-periode">{{ \Carbon\Carbon::parse($p->periode)->translatedFormat('M Y') }}</span>
            </td>
            @endif
            <td class="text-right" style="font-weight:700">{{ number_format($p->montant_encaisse, 0, ',', ' ') }} F</td>
            <td class="text-right" style="color:#1d4ed8">{{ number_format($p->commission_ttc ?? 0, 0, ',', ' ') }} F</td>
            <td class="text-right" style="color:{{ $depMois > 0 ? '#dc2626' : '#9ca3af' }}">
                {{ $depMois > 0 ? number_format($depMois, 0, ',', ' ').' F' : '—' }}
            </td>
            <td class="text-right" style="font-weight:700;color:#16a34a">{{ number_format($netLigne, 0, ',', ' ') }} F</td>
        </tr>
        @foreach($p->depenses as $dep)
        <tr style="background:#fef2f2">
            <td colspan="2" style="padding-left:20px;color:#b91c1c;font-size:9px">
                ↳ {{ $dep->libelle }}
                @if($dep->prestataire) · {{ $dep->prestataire }} @endif
            </td>
            @if($mois === null)<td></td>@endif
            <td class="text-center" style="color:#b91c1c;font-size:9px">
                {{ \Carbon\Carbon::parse($dep->date_depense)->format('d/m/Y') }}
            </td>
            <td colspan="2"></td>
            <td class="text-right" style="color:#dc2626;font-size:9px;font-weight:700">
                − {{ number_format($dep->montant, 0, ',', ' ') }} F
            </td>
        </tr>
        @endforeach
        @endforeach
        @if($mois === null)
        @php
            $sLoyers = (float)$groupe->sum('montant_encaisse');
            $sComm   = (float)$groupe->sum('commission_ttc');
            $sBrs    = (float)$groupe->sum('brs_amount');
            $sDep    = (float)$groupe->flatMap->depenses->sum('montant');
        @endphp
        <tr style="background:#f9fafb;border-top:1px solid #e5e7eb">
            <td colspan="3" style="font-size:9px;color:#9ca3af;font-style:italic">Sous-total {{ \Carbon\Carbon::createFromFormat('Y-m', $cle)->translatedFormat('F') }}</td>
            <td class="text-right" style="font-weight:600">{{ number_format($sLoyers, 0, ',', ' ') }} F</td>
            <td class="text-right" style="color:#1d4ed8;font-weight:600">{{ number_format($sComm, 0, ',', ' ') }} F</td>
            <td class="text-right" style="color:#dc2626;font-weight:600">{{ $sDep > 0 ? number_format($sDep, 0, ',', ' ').' F' : '—' }}</td>
            <td class="text-right" style="color:#16a34a;font-weight:600">{{ number_format($sLoyers - $sComm - $sBrs - $sDep, 0, ',', ' ') }} F</td>
        </tr>
        @endif
        @endforeach

        {{-- Ligne totaux ─────────────────────────────────────────── --}}
        <tr class="tr-total">
            <td colspan="{{ $mois !== null ? 3 : 4 }}" style="color:#6b7280;font-size:10px">TOTAUX {{ $mois === null ? $annee : '' }}</td>
            <td class="text-right">{{ number_format($totalLoyers, 0, ',', ' ') }} F</td>
            <td class="text-right" style="color:#1d4ed8">{{ number_format($totalCommissions, 0, ',', ' ') }} F</td>
            <td class="text-right" style="color:#dc2626">
                {{ $totalDepenses > 0 ? number_format($totalDepenses, 0, ',', ' ').' F' : '—' }}
            </td>
            <td class="text-right" style="color:#16a34a">{{ number_format($netFinal, 0, ',', ' ') }} F</td>
        </tr>
    </tbody>
</table>
@endif

{{-- Net final ───────────────────────────────────────── --}}
<div class="net-final-box">
    <div class="net-left">
        <div class="net-label">{{ $mois !== null ? 'Montant Net Final à Reverser au Bailleur' : 'Total Net à Reverser — Année '.$annee }}</div>
        <div class="net-equa">
            {{ number_format($totalLoyers, 0, ',', ' ') }} F
            − {{ number_format($totalCommissions, 0, ',', ' ') }} F commissions
            @if(($totalBrs ?? 0) > 0)
                − {{ number_format($totalBrs, 0, ',', ' ') }} F BRS (Art. 201)
            @endif
            @if($totalDepenses > 0)
                − {{ number_format($totalDepenses, 0, ',', ' ') }} F dépenses
            @endif
        </div>
    </div>
    <div class="net-right">
        <div class="net-value">{{ number_format($netFinal, 0, ',', ' ') }} F</div>
    </div>
</div>

</body>
</html>
