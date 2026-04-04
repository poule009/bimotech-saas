{{--
    Composant : x-fiscal.decompte-paiement
    Usage dans les vues web (pas dans les PDF DomPDF)

    Props :
      $paiement  — instance de Paiement avec les colonnes fiscales

    Ce composant centralise TOUTE la logique d'affichage du décompte fiscal.
    Modifier ce fichier met à jour automatiquement paiements/show.blade.php
    et partout où ce composant est utilisé.
--}}
@props(['paiement'])

@php
    $loyerHt    = (float) ($paiement->loyer_ht ?? $paiement->loyer_nu ?? 0);
    $tvaLoyer   = (float) ($paiement->tva_loyer ?? 0);
    $loyerTtc   = (float) ($paiement->loyer_ttc ?? $loyerHt);
    $charges    = (float) ($paiement->charges_amount ?? 0);
    $tom        = (float) ($paiement->tom_amount ?? 0);
    $total      = (float) $paiement->montant_encaisse;
    $commHt     = (float) $paiement->commission_agence;
    $tvaComm    = (float) $paiement->tva_commission;
    $commTtc    = (float) $paiement->commission_ttc;
    $netProprio = (float) $paiement->net_proprietaire;
    $brs        = (float) ($paiement->brs_amount ?? 0);
    $tauxBrs    = (float) ($paiement->taux_brs_applique ?? 0);
    $netAVerser = (float) ($paiement->net_a_verser_proprietaire ?? $netProprio);
    $loyerAssujetti = $tvaLoyer > 0;
    $brsApplicable  = $brs > 0;
    $fmt = fn(float $n) => number_format($n, 0, ',', ' ');
@endphp

<style>
.fd-table { width:100%;border-collapse:collapse;font-size:13px; }
.fd-table tr { border-bottom:1px solid #f3f4f6; }
.fd-table td { padding:10px 14px;vertical-align:middle; }
.fd-table td:last-child { text-align:right;font-family:'Syne',sans-serif;font-weight:600; }
.fd-group-header td { font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:#9ca3af;background:#f9fafb;padding:8px 14px; }
.fd-row-total td { background:#f5e9c9;font-weight:700;font-size:14px;color:#0d1117;border-top:2px solid #c9a84c; }
.fd-row-net td { background:#0d1117;color:#fff;font-size:14px;font-weight:700;border-top:none; }
.fd-row-net td:last-child { color:#4ade80; }
.fd-row-brs td { background:#fef2f2;color:#dc2626;font-size:13px; }
.fd-row-averser td { background:#f0fdf4;color:#16a34a;font-size:14px;font-weight:700;border-top:2px solid #16a34a; }
.fd-sub td { font-size:12px;color:#6b7280;padding-left:28px; }
.fd-sub td:last-child { color:#8a6e2f; }
.fd-badge { display:inline-flex;align-items:center;gap:4px;padding:2px 8px;border-radius:99px;font-size:10px;font-weight:600; }
.fd-badge-green { background:#dcfce7;color:#16a34a; }
.fd-badge-orange { background:#f5e9c9;color:#8a6e2f; }
.fd-badge-red { background:#fee2e2;color:#dc2626; }
</style>

<table class="fd-table">
    {{-- ── LOYER ─────────────────────────────────────── --}}
    <tr class="fd-group-header">
        <td colspan="2">
            Loyer
            @if($loyerAssujetti)
                <span class="fd-badge fd-badge-orange">TVA 18% applicable</span>
            @else
                <span class="fd-badge fd-badge-green">Exonéré TVA</span>
            @endif
        </td>
    </tr>

    <tr>
        <td>Loyer {{ $loyerAssujetti ? 'HT' : 'nu' }} mensuel</td>
        <td>{{ $fmt($loyerHt) }} F</td>
    </tr>

    @if($loyerAssujetti)
    <tr class="fd-sub">
        <td>+ TVA loyer (18% — bail commercial/meublé)</td>
        <td>{{ $fmt($tvaLoyer) }} F</td>
    </tr>
    <tr>
        <td style="font-weight:500">= Loyer TTC</td>
        <td>{{ $fmt($loyerTtc) }} F</td>
    </tr>
    @endif

    @if($charges > 0)
    <tr class="fd-sub">
        <td>+ Charges locatives récupérables</td>
        <td>{{ $fmt($charges) }} F</td>
    </tr>
    @endif

    @if($tom > 0)
    <tr class="fd-sub">
        <td>+ TOM — Taxe sur les Ordures Ménagères</td>
        <td>{{ $fmt($tom) }} F</td>
    </tr>
    @endif

    <tr class="fd-row-total">
        <td>TOTAL ENCAISSÉ</td>
        <td>{{ $fmt($total) }} F</td>
    </tr>

    {{-- ── COMMISSION ────────────────────────────────── --}}
    <tr class="fd-group-header">
        <td colspan="2">Commission agence — mandat de gestion</td>
    </tr>

    <tr>
        <td>Commission HT ({{ $paiement->taux_commission_applique }}% sur loyer {{ $loyerAssujetti ? 'HT' : 'nu' }})</td>
        <td style="color:#8a6e2f">{{ $fmt($commHt) }} F</td>
    </tr>
    <tr class="fd-sub">
        <td>↳ TVA sur commission (18% — art. 357 CGI SN)</td>
        <td>{{ $fmt($tvaComm) }} F</td>
    </tr>
    <tr>
        <td style="font-weight:500">Commission TTC</td>
        <td style="color:#d97706">{{ $fmt($commTtc) }} F</td>
    </tr>

    {{-- ── NET PROPRIÉTAIRE ──────────────────────────── --}}
    <tr class="fd-row-net">
        <td>NET PROPRIÉTAIRE (avant BRS)</td>
        <td>{{ $fmt($netProprio) }} F</td>
    </tr>

    {{-- ── BRS (affiché seulement si applicable) ─────── --}}
    @if($brsApplicable)
    <tr class="fd-group-header">
        <td colspan="2">
            Retenue à la Source
            <span class="fd-badge fd-badge-red">Locataire entreprise</span>
        </td>
    </tr>
    <tr class="fd-row-brs">
        <td>BRS retenue ({{ $tauxBrs }}% × loyer TTC — art. 196bis CGI SN)</td>
        <td style="font-weight:700">- {{ $fmt($brs) }} F</td>
    </tr>
    <tr class="fd-row-averser">
        <td>NET À VERSER AU PROPRIÉTAIRE</td>
        <td>{{ $fmt($netAVerser) }} F</td>
    </tr>
    @endif
</table>