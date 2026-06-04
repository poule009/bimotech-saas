<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'DejaVu Sans', sans-serif; font-size: 9.5pt; color: #1a2e3b; background: #fff; }
    .page { padding: 22mm 20mm; }

    .header { display: flex; justify-content: space-between; align-items: flex-start; padding-bottom: 14px; border-bottom: 3px solid #1B4F6B; margin-bottom: 18px; }
    .agency-name { font-size: 15pt; font-weight: bold; color: #1B4F6B; }
    .agency-info { font-size: 8pt; color: #666; margin-top: 3px; line-height: 1.6; }
    .doc-title { text-align: right; }
    .doc-title h1 { font-size: 12pt; font-weight: bold; color: #1B4F6B; text-transform: uppercase; letter-spacing: 0.05em; }
    .doc-title p { font-size: 8pt; color: #888; margin-top: 3px; }

    .ref-box { background: #f0ede6; border-radius: 6px; padding: 10px 14px; margin-bottom: 18px; display: flex; justify-content: space-between; align-items: center; }
    .ref-box .ref-num { font-size: 8pt; color: #888; }
    .ref-box .ref-date { font-size: 8pt; color: #555; }

    .certif-block { border: 2px solid #1B4F6B22; border-radius: 8px; padding: 16px 20px; margin-bottom: 18px; background: #1B4F6B04; }
    .certif-block h2 { font-size: 10pt; font-weight: bold; color: #1B4F6B; margin-bottom: 12px; }
    .certif-block p { font-size: 9.5pt; color: #333; line-height: 1.8; }
    .certif-block strong { color: #1B4F6B; }
    .certif-block .montant-lettres { font-style: italic; color: #666; font-size: 8.5pt; margin-top: 8px; }

    table { width: 100%; border-collapse: collapse; margin-bottom: 16px; font-size: 8.5pt; }
    th { background: #1B4F6B; color: #fff; padding: 6px 8px; text-align: left; font-weight: bold; font-size: 7.5pt; text-transform: uppercase; letter-spacing: 0.05em; }
    td { padding: 6px 8px; border-bottom: 1px solid #f0ede6; color: #333; }
    tr:nth-child(even) td { background: #fafaf8; }
    tr.total td { background: #1B4F6B0d; font-weight: bold; border-top: 2px solid #1B4F6B22; }
    .ta-r { text-align: right; }
    .ta-c { text-align: center; }

    .kpi-grid { display: flex; gap: 12px; margin-bottom: 18px; }
    .kpi-card { flex: 1; border: 1px solid #1B4F6B22; border-radius: 6px; padding: 10px 12px; text-align: center; }
    .kpi-card .kpi-label { font-size: 7pt; text-transform: uppercase; letter-spacing: 0.08em; color: #888; margin-bottom: 4px; }
    .kpi-card .kpi-val { font-size: 13pt; font-weight: bold; color: #1B4F6B; }
    .kpi-card.gold { border-color: #C9A84C44; background: #C9A84C08; }
    .kpi-card.gold .kpi-val { color: #9a7a30; }

    .legal-ref { background: #f9f8f6; border-left: 3px solid #C9A84C; padding: 8px 12px; border-radius: 0 4px 4px 0; margin-bottom: 18px; }
    .legal-ref p { font-size: 7.5pt; color: #888; line-height: 1.6; }

    .sign-zone { display: flex; gap: 16px; margin-top: 20px; }
    .sign-box { flex: 1; border: 1px dashed #bbb; border-radius: 4px; padding: 12px; min-height: 70px; }
    .sign-box .sign-label { font-size: 8pt; color: #666; font-weight: bold; margin-bottom: 6px; }
    .sign-box .sign-line { border-bottom: 1px solid #ddd; margin-top: 30px; }
    .sign-box .sign-name { font-size: 7.5pt; color: #aaa; margin-top: 4px; }

    .footer { margin-top: 16px; padding-top: 10px; border-top: 1px solid #eee; display: flex; justify-content: space-between; font-size: 7pt; color: #bbb; }
</style>
</head>
<body>
<div class="page">

    {{-- Header --}}
    <div class="header">
        <div>
            <div class="agency-name">{{ $agency->name }}</div>
            <div class="agency-info">
                {{ $agency->adresse ?? '' }}{{ $agency->ville ? ', '.$agency->ville : '' }}<br>
                @if($agency->ninea)NINEA : {{ $agency->ninea }} @endif
                @if($agency->rccm) — RCCM : {{ $agency->rccm }}@endif
            </div>
        </div>
        <div class="doc-title">
            <h1>Attestation de Retenue BRS</h1>
            <p>Bordereau de Retenue à la Source</p>
            <p>Année {{ $annee }}</p>
        </div>
    </div>

    {{-- Référence --}}
    <div class="ref-box">
        <div class="ref-num">Réf : ATTEST-BRS-{{ $proprietaire->id }}-{{ $annee }}-{{ now()->format('Ymd') }}</div>
        <div class="ref-date">Dakar, le {{ now()->locale('fr')->translatedFormat('d F Y') }}</div>
    </div>

    {{-- Bloc de certification --}}
    <div class="certif-block">
        <h2>Attestation de retenue à la source (BRS)</h2>
        <p>
            Nous soussignés, <strong>{{ $agency->name }}</strong>, agence immobilière{{ $agency->ninea ? ' (NINEA : '.$agency->ninea.')' : '' }},
            certifions avoir effectué, au titre de l'année <strong>{{ $annee }}</strong>, les retenues à la source
            au titre du <strong>Bordereau de Retenue à la Source (BRS)</strong> sur les loyers versés à :
        </p>
        <p style="margin-top:12px;padding:8px 12px;background:#fff;border:1px solid #ddd;border-radius:4px">
            <strong>{{ $proprietaire->name }}</strong>
            @if($proprietaire->email) — {{ $proprietaire->email }}@endif<br>
            @if($proprietaire->profile?->ninea)NINEA propriétaire : {{ $proprietaire->profile->ninea }}<br>@endif
        </p>
        <p style="margin-top:12px">
            Conformément à l'<strong>article 201 §3 du Code Général des Impôts (CGI) du Sénégal</strong>,
            un taux de <strong>5%</strong> a été appliqué sur le montant des loyers versés à ce propriétaire personne physique.
        </p>
        <p style="margin-top:8px">
            Le montant total retenu au titre du BRS pour l'année {{ $annee }} s'élève à :
            <strong style="font-size:12pt"> {{ number_format($bilan->brs_retenu_total, 0, ',', ' ') }} FCFA</strong>
        </p>
        <p class="montant-lettres">Soit en lettres : <em>{{ $brsEnLettres }}</em></p>
    </div>

    {{-- KPIs --}}
    <div class="kpi-grid">
        <div class="kpi-card">
            <div class="kpi-label">Loyers HT base BRS</div>
            <div class="kpi-val">{{ number_format($paiements->sum('loyer_ht'), 0, ',', ' ') }} F</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-label">Taux BRS appliqué</div>
            <div class="kpi-val">5%</div>
        </div>
        <div class="kpi-card gold">
            <div class="kpi-label">Total BRS retenu</div>
            <div class="kpi-val">{{ number_format($bilan->brs_retenu_total, 0, ',', ' ') }} F</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-label">Paiements concernés</div>
            <div class="kpi-val">{{ $paiements->count() }}</div>
        </div>
    </div>

    {{-- Tableau détail --}}
    <table>
        <thead>
            <tr>
                <th>Période</th>
                <th>Bien</th>
                <th class="ta-r">Loyer HT</th>
                <th class="ta-c">Taux BRS</th>
                <th class="ta-r">BRS retenu</th>
                <th class="ta-r">Net versé au propriétaire</th>
            </tr>
        </thead>
        <tbody>
            @foreach($paiements as $p)
            <tr>
                <td>{{ $p->periode?->locale('fr')->translatedFormat('F Y') ?? '—' }}</td>
                <td>{{ $p->bien_reference ?? $p->contrat?->bien?->reference ?? '—' }}</td>
                <td class="ta-r">{{ number_format($p->loyer_ht, 0, ',', ' ') }} F</td>
                <td class="ta-c">{{ $p->taux_brs_applique ?? 5 }}%</td>
                <td class="ta-r" style="font-weight:bold;color:#9a7a30">{{ number_format($p->brs_amount ?? 0, 0, ',', ' ') }} F</td>
                <td class="ta-r">{{ number_format($p->net_a_verser_proprietaire ?? 0, 0, ',', ' ') }} F</td>
            </tr>
            @endforeach
            <tr class="total">
                <td colspan="2">Totaux {{ $annee }}</td>
                <td class="ta-r">{{ number_format($paiements->sum('loyer_ht'), 0, ',', ' ') }} F</td>
                <td class="ta-c">—</td>
                <td class="ta-r" style="color:#9a7a30">{{ number_format($paiements->sum('brs_amount'), 0, ',', ' ') }} F</td>
                <td class="ta-r">{{ number_format($paiements->sum('net_a_verser_proprietaire'), 0, ',', ' ') }} F</td>
            </tr>
        </tbody>
    </table>

    {{-- Référence légale --}}
    <div class="legal-ref">
        <p>
            <strong>Base légale :</strong> CGI Sénégal — Article 201 §3 : Retenue à la source sur les loyers versés aux propriétaires personnes physiques.
            Le taux est de 5% du montant brut des loyers. La retenue est libératoire de l'IRPP sur ces revenus fonciers pour les propriétaires soumis au régime du réel simplifié.
            Cette attestation est délivrée à la demande du propriétaire et peut être présentée à la Direction Générale des Impôts et Domaines (DGID).
        </p>
    </div>

    {{-- Zones de signature --}}
    <div class="sign-zone">
        <div class="sign-box">
            <div class="sign-label">Le Directeur de l'agence</div>
            <div class="sign-line"></div>
            <div class="sign-name">{{ $agency->name }}</div>
        </div>
        <div class="sign-box">
            <div class="sign-label">Cachet de l'agence</div>
        </div>
        <div class="sign-box">
            <div class="sign-label">Accusé de réception propriétaire</div>
            <div class="sign-line"></div>
            <div class="sign-name">{{ $proprietaire->name }}</div>
        </div>
    </div>

    <div class="footer">
        <span>{{ $agency->name }}{{ $agency->ninea ? ' — NINEA '.$agency->ninea : '' }}</span>
        <span>Attestation BRS — {{ $proprietaire->name }} — {{ $annee }}</span>
    </div>

</div>
</body>
</html>
