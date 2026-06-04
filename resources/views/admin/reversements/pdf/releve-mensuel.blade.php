<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'DejaVu Sans', sans-serif; font-size: 9.5pt; color: #1a2e3b; background: #fff; }
    .page { padding: 18mm 18mm; }

    /* ── Header ── */
    .header { background: #1B4F6B; color: #fff; padding: 14px 18px; border-radius: 6px; display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 14px; }
    .header-agency h1 { font-size: 13pt; font-weight: bold; letter-spacing: 0.02em; }
    .header-agency p { font-size: 7.5pt; color: rgba(255,255,255,0.65); margin-top: 2px; line-height: 1.5; }
    .header-doc { text-align: right; }
    .header-doc h2 { font-size: 11pt; font-weight: bold; text-transform: uppercase; letter-spacing: 0.06em; color: #C9A84C; }
    .header-doc p { font-size: 7.5pt; color: rgba(255,255,255,0.65); margin-top: 2px; }

    /* ── Bande référence ── */
    .ref-band { background: #F0EDE6; border-left: 3px solid #C9A84C; padding: 8px 14px; margin-bottom: 14px; display: flex; justify-content: space-between; align-items: center; border-radius: 0 4px 4px 0; }
    .ref-band .ref { font-size: 7.5pt; color: #666; font-family: monospace; }
    .ref-band .periode-label { font-size: 9pt; font-weight: bold; color: #1B4F6B; }

    /* ── Bandeau propriétaire ── */
    .proprio-band { border: 1.5px solid #1B4F6B22; border-radius: 6px; padding: 10px 14px; margin-bottom: 16px; display: flex; justify-content: space-between; align-items: center; }
    .proprio-band .name { font-size: 11pt; font-weight: bold; color: #1B4F6B; }
    .proprio-band .detail { font-size: 8pt; color: #666; margin-top: 2px; }
    .proprio-band .badge { background: #1B4F6B; color: #fff; font-size: 7pt; padding: 3px 10px; border-radius: 99px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.05em; }

    /* ── Tableau relevé ── */
    .section-title { font-size: 8pt; font-weight: bold; text-transform: uppercase; letter-spacing: 0.08em; color: #1B4F6B; margin-bottom: 6px; margin-top: 14px; padding-bottom: 4px; border-bottom: 1px solid #1B4F6B22; }

    .releve-table { width: 100%; border-collapse: collapse; margin-bottom: 6px; }
    .releve-table tr { border-bottom: 1px solid #F0EDE6; }
    .releve-table tr:last-child { border-bottom: none; }
    .releve-table td { padding: 6px 10px; font-size: 9pt; }
    .releve-table td.label { color: #555; flex: 1; }
    .releve-table td.ref-legal { font-size: 7pt; color: #aaa; padding-left: 4px; }
    .releve-table td.montant { text-align: right; font-weight: bold; min-width: 110px; }
    .releve-table td.montant.pos { color: #1B4F6B; }
    .releve-table td.montant.neg { color: #777; }
    .releve-table tr.separator td { padding: 0; border-bottom: 2px solid #1B4F6B22; }
    .releve-table tr.total td { background: #1B4F6B08; font-weight: bold; padding: 8px 10px; }
    .releve-table tr.total td.montant { font-size: 11pt; color: #1B4F6B; }

    /* ── Reversements ── */
    .reversement-block { background: #C9A84C0d; border: 1px solid #C9A84C33; border-radius: 6px; padding: 10px 14px; margin-bottom: 14px; }
    .reversement-block .rev-title { font-size: 8pt; font-weight: bold; color: #9a7a30; text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 8px; }
    .reversement-row { display: flex; justify-content: space-between; align-items: center; padding: 5px 0; border-bottom: 1px solid #C9A84C22; }
    .reversement-row:last-child { border-bottom: none; }
    .reversement-row .rev-info p { font-size: 8.5pt; color: #333; font-weight: bold; }
    .reversement-row .rev-info span { font-size: 7.5pt; color: #888; }
    .reversement-row .rev-montant { font-size: 11pt; font-weight: bold; color: #9a7a30; }

    /* ── Solde ── */
    .solde-block { border: 2px solid #1B4F6B; border-radius: 6px; padding: 12px 16px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
    .solde-block.zero { border-color: #C9A84C; background: #C9A84C08; }
    .solde-block.positif { border-color: #EF4444; background: #EF444408; }
    .solde-block .solde-label { font-size: 9pt; font-weight: bold; color: #1B4F6B; }
    .solde-block .solde-val { font-size: 14pt; font-weight: bold; }
    .solde-block.zero .solde-val { color: #C9A84C; }
    .solde-block.positif .solde-val { color: #EF4444; }

    /* ── Zones de signature ── */
    .sign-zone { display: flex; gap: 14px; margin-top: 18px; }
    .sign-box { flex: 1; border: 1px dashed #bbb; border-radius: 4px; padding: 10px 12px; min-height: 60px; }
    .sign-box p { font-size: 8pt; color: #777; font-weight: bold; margin-bottom: 4px; }
    .sign-box .sign-name { font-size: 7.5pt; color: #aaa; margin-top: 28px; }

    /* ── Disclaimer ── */
    .disclaimer { margin-top: 14px; padding: 8px 12px; background: #F7F5F0; border-radius: 4px; }
    .disclaimer p { font-size: 7pt; color: #999; line-height: 1.6; text-align: center; }

    /* ── Footer ── */
    .footer { margin-top: 14px; padding-top: 8px; border-top: 1px solid #eee; display: flex; justify-content: space-between; font-size: 7pt; color: #bbb; }
</style>
</head>
<body>
<div class="page">

    {{-- Header --}}
    <div class="header">
        <div class="header-agency">
            <h1>{{ $agency->name }}</h1>
            <p>{{ $agency->adresse ?? '' }}{{ $agency->ville ? ' — '.$agency->ville : '' }}</p>
            @if($agency->ninea)<p>NINEA : {{ $agency->ninea }}</p>@endif
        </div>
        <div class="header-doc">
            <h2>Relevé de Gestion</h2>
            @if($periode)
            <p>{{ \Carbon\Carbon::createFromFormat('Y-m', $periode)->locale('fr')->translatedFormat('F Y') }}</p>
            @else
            <p>Toutes périodes</p>
            @endif
            <p>Généré le {{ now()->format('d/m/Y à H:i') }}</p>
        </div>
    </div>

    {{-- Bande référence --}}
    <div class="ref-band">
        <span class="ref">Réf : {{ $refDoc }}</span>
        <span class="periode-label">
            @if($periode)
            {{ \Carbon\Carbon::createFromFormat('Y-m', $periode)->locale('fr')->translatedFormat('F Y') }}
            @else
            Relevé complet
            @endif
        </span>
    </div>

    {{-- Propriétaire --}}
    <div class="proprio-band">
        <div>
            <div class="name">{{ $proprietaire->name }}</div>
            <div class="detail">{{ $proprietaire->email ?? '' }}{{ $proprietaire->telephone ? ' — '.$proprietaire->telephone : '' }}</div>
        </div>
        <span class="badge">Propriétaire</span>
    </div>

    {{-- Relevé détaillé --}}
    <div class="section-title">Détail du relevé</div>
    <table class="releve-table">
        <tr>
            <td class="label">Loyers encaissés</td>
            <td class="montant pos">+{{ number_format($compte['loyers_encaisses'], 0, ',', ' ') }} F</td>
        </tr>
        <tr>
            <td class="label">Commission agence <span style="font-size:7pt;color:#aaa">(TTC)</span></td>
            <td class="montant neg">-{{ number_format($compte['commissions_deduites'], 0, ',', ' ') }} F</td>
        </tr>
        @if($compte['brs_retenu'] > 0)
        <tr>
            <td class="label">BRS retenu <span style="font-size:7pt;color:#aaa">(Art. 201 §3 CGI — 5%)</span></td>
            <td class="montant neg">-{{ number_format($compte['brs_retenu'], 0, ',', ' ') }} F</td>
        </tr>
        @endif
        @if($compte['depenses_avancees'] > 0)
        <tr>
            <td class="label">Dépenses avancées <span style="font-size:7pt;color:#aaa">(réparations, travaux...)</span></td>
            <td class="montant neg">-{{ number_format($compte['depenses_avancees'], 0, ',', ' ') }} F</td>
        </tr>
        @endif
        <tr class="separator"><td colspan="2"></td></tr>
        <tr class="total">
            <td class="label">Net à reverser</td>
            <td class="montant">{{ number_format($compte['net_du'], 0, ',', ' ') }} F</td>
        </tr>
    </table>

    {{-- Reversements effectués --}}
    @if($reversementsPeriode->isNotEmpty())
    <div class="reversement-block">
        <div class="rev-title">Reversement(s) effectué(s)</div>
        @foreach($reversementsPeriode as $rev)
        <div class="reversement-row">
            <div class="rev-info">
                <p>{{ $rev->mode_paiement_libelle }}{{ $rev->reference ? ' — '.$rev->reference : '' }}</p>
                <span>{{ $rev->date_reversement->format('d/m/Y') }}
                @if($rev->periode_debut) · {{ $rev->periode_debut }}{{ $rev->periode_fin && $rev->periode_fin !== $rev->periode_debut ? ' → '.$rev->periode_fin : '' }}@endif</span>
            </div>
            <div class="rev-montant">+{{ number_format($rev->montant, 0, ',', ' ') }} F</div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- Solde --}}
    @php $solde = $compte['solde_restant']; @endphp
    <div class="solde-block {{ $solde == 0 ? 'zero' : 'positif' }}">
        <div class="solde-label">Solde {{ $solde == 0 ? 'soldé' : 'restant dû' }}</div>
        <div class="solde-val">{{ number_format(abs($solde), 0, ',', ' ') }} F</div>
    </div>

    {{-- Détail paiements --}}
    @if($compte['paiements']->isNotEmpty())
    <div class="section-title">Détail des paiements</div>
    <table style="width:100%;border-collapse:collapse;font-size:8pt">
        <thead>
            <tr style="background:#1B4F6B;color:#fff">
                <th style="padding:5px 8px;text-align:left;font-size:7pt;text-transform:uppercase;letter-spacing:0.05em">Période</th>
                <th style="padding:5px 8px;text-align:left;font-size:7pt;text-transform:uppercase;letter-spacing:0.05em">Bien</th>
                <th style="padding:5px 8px;text-align:right;font-size:7pt;text-transform:uppercase;letter-spacing:0.05em">Encaissé</th>
                <th style="padding:5px 8px;text-align:right;font-size:7pt;text-transform:uppercase;letter-spacing:0.05em">Commission</th>
                <th style="padding:5px 8px;text-align:right;font-size:7pt;text-transform:uppercase;letter-spacing:0.05em">BRS</th>
                <th style="padding:5px 8px;text-align:right;font-size:7pt;text-transform:uppercase;letter-spacing:0.05em">Net reversé</th>
            </tr>
        </thead>
        <tbody>
            @foreach($compte['paiements'] as $p)
            <tr style="border-bottom:1px solid #F0EDE6">
                <td style="padding:5px 8px;color:#555">{{ $p->periode?->format('m/Y') ?? '—' }}</td>
                <td style="padding:5px 8px;color:#555">{{ $p->contrat?->bien?->reference ?? '—' }}</td>
                <td style="padding:5px 8px;text-align:right;color:#1B4F6B;font-weight:bold">{{ number_format($p->montant_encaisse, 0, ',', ' ') }} F</td>
                <td style="padding:5px 8px;text-align:right;color:#777">{{ number_format($p->commission_ttc, 0, ',', ' ') }} F</td>
                <td style="padding:5px 8px;text-align:right;color:#777">{{ number_format($p->brs_amount ?? 0, 0, ',', ' ') }} F</td>
                <td style="padding:5px 8px;text-align:right;color:#C9A84C;font-weight:bold">{{ number_format($p->net_final_bailleur, 0, ',', ' ') }} F</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    {{-- Signature --}}
    <div class="sign-zone">
        <div class="sign-box">
            <p>Le Directeur de l'agence</p>
            <div class="sign-name">{{ $agency->name }}</div>
        </div>
        <div class="sign-box">
            <p>Fait à {{ $agency->ville ?? 'Dakar' }}, le {{ now()->locale('fr')->translatedFormat('d F Y') }}</p>
        </div>
        <div class="sign-box">
            <p>Accusé de réception</p>
            <div class="sign-name">{{ $proprietaire->name }}</div>
        </div>
    </div>

    {{-- Disclaimer --}}
    <div class="disclaimer">
        <p>Document confidentiel — Relevé de gestion établi par {{ $agency->name }}{{ $agency->ninea ? ' (NINEA : '.$agency->ninea.')' : '' }}. Ce document récapitule les opérations de gestion immobilière effectuées pour le compte du propriétaire désigné.</p>
    </div>

    <div class="footer">
        <span>{{ $agency->name }}{{ $agency->ninea ? ' — NINEA '.$agency->ninea : '' }}</span>
        <span>{{ $refDoc }}</span>
        <span>{{ now()->format('d/m/Y') }}</span>
    </div>

</div>
</body>
</html>
