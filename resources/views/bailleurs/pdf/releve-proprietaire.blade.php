<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title>Relevé — {{ $user->name }}</title>
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family:'DejaVu Sans',Arial,sans-serif; font-size:10px; color:#1a1a1a; }

.header { background:#0d1117; padding:18px 24px; margin-bottom:0; }
.header-inner { display:table; width:100%; }
.header-left  { display:table-cell; vertical-align:middle; }
.header-right { display:table-cell; vertical-align:middle; text-align:right; }
.agency-name  { font-size:15px; font-weight:700; color:#c9a84c; }
.agency-sub   { font-size:9px; color:rgba(255,255,255,.4); margin-top:2px; }
.doc-title    { font-size:16px; font-weight:700; color:#fff; text-transform:uppercase; letter-spacing:.5px; }
.doc-sub      { font-size:9px; color:rgba(255,255,255,.4); margin-top:3px; }

.periode-band { background:#f5e9c9; padding:10px 24px; border-bottom:2px solid #c9a84c; display:table; width:100%; }
.periode-left  { display:table-cell; vertical-align:middle; }
.periode-right { display:table-cell; vertical-align:middle; text-align:right; }
.periode-lbl   { font-size:8px; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:#8a6e2f; }
.periode-val   { font-size:14px; font-weight:700; color:#0d1117; margin-top:2px; }

.content { padding:18px 24px; }

.proprio-box { background:#f9fafb; border:1px solid #e5e7eb; border-radius:8px; padding:12px 16px; margin-bottom:18px; display:table; width:100%; }
.proprio-left  { display:table-cell; width:70%; vertical-align:top; }
.proprio-right { display:table-cell; width:30%; vertical-align:top; text-align:right; }
.p-lbl { font-size:8px; font-weight:700; text-transform:uppercase; letter-spacing:.7px; color:#9ca3af; margin-bottom:2px; }
.p-val { font-size:12px; font-weight:700; color:#0d1117; }
.p-sub { font-size:9px; color:#6b7280; margin-top:1px; }

/* KPIs */
.kpi-row { display:table; width:100%; margin-bottom:18px; border-collapse:separate; }
.kpi-cell { display:table-cell; padding:12px 14px; background:#fff; border:1px solid #e5e7eb; border-radius:8px; text-align:center; }
.kpi-spacer { display:table-cell; width:10px; }
.kpi-lbl { font-size:8px; font-weight:700; text-transform:uppercase; letter-spacing:.7px; color:#9ca3af; margin-bottom:4px; }
.kpi-val { font-size:18px; font-weight:700; color:#0d1117; font-family:'DejaVu Sans',sans-serif; }
.kpi-sub { font-size:8px; color:#9ca3af; margin-top:2px; }

/* Table paiements */
.dt { width:100%; border-collapse:collapse; margin-bottom:18px; }
.dt th { padding:7px 10px; font-size:8px; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:#9ca3af; background:#f9fafb; border-bottom:1px solid #e5e7eb; }
.dt th.right { text-align:right; }
.dt td { padding:8px 10px; font-size:9.5px; color:#374151; border-bottom:1px solid #f3f4f6; }
.dt td.right { text-align:right; font-weight:600; }
.dt tfoot td { padding:9px 10px; background:#0d1117; color:#fff; font-weight:700; font-size:10px; }
.dt tfoot td.gold { color:#c9a84c; font-size:13px; text-align:right; }

.net-box { background:#0d1117; border-radius:10px; padding:14px 18px; display:table; width:100%; margin-bottom:18px; }
.net-left  { display:table-cell; vertical-align:middle; }
.net-right { display:table-cell; vertical-align:middle; text-align:right; }
.net-lbl   { font-size:9px; font-weight:700; text-transform:uppercase; letter-spacing:.8px; color:rgba(201,168,76,.6); margin-bottom:3px; }
.net-sub   { font-size:9px; color:rgba(255,255,255,.3); }
.net-val   { font-size:22px; font-weight:700; color:#c9a84c; }

.footer { position:fixed; bottom:0; left:0; right:0; padding:6px 24px; background:#f9fafb; border-top:1px solid #e5e7eb; display:table; width:100%; font-size:8px; color:#9ca3af; }
.footer-left  { display:table-cell; }
.footer-right { display:table-cell; text-align:right; }
</style>
</head>
<body>

<div class="footer">
    <div class="footer-left">{{ $agency->name ?? 'BimoTech Immo' }} · Relevé confidentiel · {{ $user->name }}</div>
    <div class="footer-right">Généré le {{ now()->format('d/m/Y à H:i') }}</div>
</div>

{{-- En-tête --}}
<div class="header">
    <div class="header-inner">
        <div class="header-left">
            <div class="agency-name">{{ $agency->name ?? 'BimoTech Immo' }}</div>
            <div class="agency-sub">{{ $agency->adresse ?? '' }}@if($agency->telephone) · {{ $agency->telephone }}@endif</div>
        </div>
        <div class="header-right">
            <div class="doc-title">Relevé propriétaire</div>
            <div class="doc-sub">Document confidentiel · Données exclusives</div>
        </div>
    </div>
</div>

<div class="periode-band">
    <div class="periode-left">
        <div class="periode-lbl">Période</div>
        <div class="periode-val">
            {{ $mois !== null ? $periode->translatedFormat('F Y') : 'Année '.$annee }}
        </div>
    </div>
    <div class="periode-right">
        <div class="periode-lbl">Transmis à</div>
        <div class="periode-val">{{ $user->name }}</div>
    </div>
</div>

<div class="content">

    {{-- Propriétaire --}}
    <div class="proprio-box">
        <div class="proprio-left">
            <div class="p-lbl">Propriétaire</div>
            <div class="p-val">{{ $user->name }}</div>
            <div class="p-sub">{{ $user->email }}@if($user->telephone) · {{ $user->telephone }}@endif</div>
            @if($user->adresse)<div class="p-sub">{{ $user->adresse }}</div>@endif
        </div>
        <div class="proprio-right">
            @if($user->proprietaire?->ninea)
            <div class="p-lbl">NINEA</div>
            <div class="p-val" style="font-size:11px">{{ $user->proprietaire->ninea }}</div>
            @endif
            @if($user->proprietaire?->mode_paiement_prefere)
            <div class="p-sub" style="margin-top:6px">Règlement : {{ $user->proprietaire->mode_paiement_prefere }}</div>
            @endif
        </div>
    </div>

    {{-- KPIs --}}
    @php $nbPaiements = $paiements->count(); @endphp
    <div class="kpi-row">
        <div class="kpi-cell">
            <div class="kpi-lbl">Loyers encaissés</div>
            <div class="kpi-val" style="color:#c9a84c">{{ number_format($totalLoyers, 0, ',', ' ') }}</div>
            <div class="kpi-sub">FCFA</div>
        </div>
        <div class="kpi-spacer"></div>
        <div class="kpi-cell">
            <div class="kpi-lbl">Commissions agence</div>
            <div class="kpi-val" style="color:#1d4ed8">{{ number_format($totalCommissions, 0, ',', ' ') }}</div>
            <div class="kpi-sub">FCFA TTC</div>
        </div>
        <div class="kpi-spacer"></div>
        @if($totalDepenses > 0)
        <div class="kpi-cell">
            <div class="kpi-lbl">Dépenses gestion</div>
            <div class="kpi-val" style="color:#dc2626">{{ number_format($totalDepenses, 0, ',', ' ') }}</div>
            <div class="kpi-sub">FCFA</div>
        </div>
        <div class="kpi-spacer"></div>
        @endif
        <div class="kpi-cell">
            <div class="kpi-lbl">Paiements</div>
            <div class="kpi-val">{{ $nbPaiements }}</div>
            <div class="kpi-sub">reçu(s)</div>
        </div>
    </div>

    {{-- Tableau --}}
    @if($paiements->isNotEmpty())
    <table class="dt">
        <thead>
            <tr>
                <th>Bien</th>
                <th>Période</th>
                <th class="right">Loyer encaissé</th>
                <th class="right">Commission TTC</th>
                <th class="right">Net versé</th>
            </tr>
        </thead>
        <tbody>
            @foreach($paiements as $p)
            <tr>
                <td>{{ $p->contrat?->bien?->reference ?? '—' }}</td>
                <td>{{ \Carbon\Carbon::parse($p->periode)->translatedFormat('M Y') }}</td>
                <td class="right">{{ number_format($p->montant_encaisse, 0, ',', ' ') }} F</td>
                <td class="right" style="color:#1d4ed8">{{ number_format($p->commission_ttc ?? 0, 0, ',', ' ') }} F</td>
                <td class="right" style="color:#16a34a">{{ number_format(($p->net_a_verser_proprietaire ?? $p->net_proprietaire ?? 0), 0, ',', ' ') }} F</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2">TOTAL</td>
                <td>{{ number_format($totalLoyers, 0, ',', ' ') }} F</td>
                <td>{{ number_format($totalCommissions, 0, ',', ' ') }} F</td>
                <td class="gold">{{ number_format($netFinal, 0, ',', ' ') }} F</td>
            </tr>
        </tfoot>
    </table>
    @else
    <div style="text-align:center;padding:30px;color:#9ca3af;font-style:italic">Aucun paiement validé sur cette période.</div>
    @endif

    {{-- Net final --}}
    <div class="net-box">
        <div class="net-left">
            <div class="net-lbl">Montant net à vous reverser</div>
            <div class="net-sub">
                {{ number_format($totalLoyers, 0, ',', ' ') }} F encaissés
                − {{ number_format($totalCommissions, 0, ',', ' ') }} F commissions
                @if($totalDepenses > 0) − {{ number_format($totalDepenses, 0, ',', ' ') }} F dépenses@endif
            </div>
        </div>
        <div class="net-right">
            <div class="net-val">{{ number_format($netFinal, 0, ',', ' ') }} F</div>
        </div>
    </div>

    <div style="font-size:8.5px;color:#9ca3af;border-top:1px solid #e5e7eb;padding-top:10px;line-height:1.6">
        Ce relevé est établi par {{ $agency->name ?? 'BimoTech Immo' }} conformément aux paiements enregistrés sur la période.
        Honoraires d'agence calculés conformément au mandat de gestion — TVA 18% incluse (Art. 357 CGI SN).
        Document confidentiel — réservé au propriétaire mentionné.
    </div>

</div>
</body>
</html>
