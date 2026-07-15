{{--
    Reçu d'abonnement — généré à la volée depuis les données du paiement.

    Le montant vient du snapshot `subscription_payments.montant`, figé à
    l'encaissement : le reçu reste exact même si le tarif du plan a changé depuis.
--}}
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 30px 34px; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #1B3A3F; }
        .head { border-bottom: 2px solid #B8892B; padding-bottom: 14px; margin-bottom: 22px; }
        .brand { font-size: 19px; font-weight: bold; color: #1B3A3F; }
        .brand-sub { font-size: 9.5px; color: #5A6B6A; margin-top: 3px; }
        .doc-title { font-size: 15px; font-weight: bold; margin-top: 16px; }
        .doc-meta { font-size: 9.5px; color: #5A6B6A; margin-top: 3px; }
        .box { border: 1px solid #DCD3BE; border-radius: 6px; padding: 14px 16px; margin-bottom: 16px; }
        .box-title { font-size: 9px; text-transform: uppercase; letter-spacing: 0.5px; color: #5A6B6A; font-weight: bold; margin-bottom: 8px; }
        table { width: 100%; border-collapse: collapse; }
        .lines td { padding: 7px 0; border-bottom: 1px solid #EDE7D8; }
        .lines td.label { color: #5A6B6A; width: 45%; }
        .lines td.value { text-align: right; font-weight: bold; }
        .total { background: #F7F3EA; border: 1px solid #B8892B; border-radius: 6px; padding: 12px 16px; margin-top: 4px; }
        .total td { padding: 0; }
        .total .t-label { font-size: 11px; font-weight: bold; }
        .total .t-value { text-align: right; font-size: 17px; font-weight: bold; color: #1B3A3F; }
        .paid { display: inline-block; border: 1.5px solid #3E7856; color: #3E7856; font-size: 10px; font-weight: bold;
                padding: 3px 10px; border-radius: 12px; margin-top: 10px; }
        .foot { margin-top: 26px; padding-top: 12px; border-top: 1px solid #DCD3BE; font-size: 8.5px; color: #5A6B6A; line-height: 1.5; }
    </style>
</head>
<body>

@php
    $fmt = fn ($n) => number_format((float) $n, 0, ',', ' ');
    $cycle = \App\Models\Subscription::LABELS[$payment->plan] ?? $payment->plan;
    $methode = \App\Models\SubscriptionPayment::METHODE_LABELS[$payment->methode] ?? $payment->methode;
@endphp

<div class="head">
    <div class="brand">BIMO-tech</div>
    <div class="brand-sub">Éditeur de Bimmo — logiciel de gestion immobilière<br>Dakar, Sénégal · support@bimotech.sn</div>
    <div class="doc-title">Reçu de paiement</div>
    <div class="doc-meta">
        N° {{ str_pad((string) $payment->id, 6, '0', STR_PAD_LEFT) }}
        · Émis le {{ now()->locale('fr')->isoFormat('D MMMM Y') }}
    </div>
</div>

<div class="box">
    <div class="box-title">Agence</div>
    <div style="font-weight:bold; font-size:12.5px;">{{ $payment->agency?->name ?? 'Agence #'.$payment->agency_id }}</div>
    @if($payment->agency?->adresse)
        <div style="color:#5A6B6A; margin-top:3px;">{{ $payment->agency->adresse }}</div>
    @endif
    @if($payment->agency?->email)
        <div style="color:#5A6B6A; margin-top:2px;">{{ $payment->agency->email }}</div>
    @endif
    @if($payment->agency?->ninea)
        <div style="color:#5A6B6A; margin-top:2px;">NINEA : {{ $payment->agency->ninea }}</div>
    @endif
</div>

<div class="box">
    <div class="box-title">Détail</div>
    <table class="lines">
        <tr>
            <td class="label">Abonnement Bimmo — plan</td>
            <td class="value">{{ $planLabel }}</td>
        </tr>
        <tr>
            <td class="label">Cycle de facturation</td>
            <td class="value">{{ $cycle }}</td>
        </tr>
        @if($payment->periode_debut && $payment->periode_fin)
            <tr>
                <td class="label">Période couverte</td>
                <td class="value">
                    {{ $payment->periode_debut->locale('fr')->isoFormat('D MMM Y') }}
                    →
                    {{ $payment->periode_fin->locale('fr')->isoFormat('D MMM Y') }}
                </td>
            </tr>
        @endif
        <tr>
            <td class="label">Date du paiement</td>
            <td class="value">{{ $payment->created_at->locale('fr')->isoFormat('D MMMM Y') }}</td>
        </tr>
        <tr>
            <td class="label">Méthode de paiement</td>
            <td class="value">{{ $methode }}</td>
        </tr>
        @if($payment->reference)
            <tr>
                <td class="label">Référence</td>
                <td class="value">{{ $payment->reference }}</td>
            </tr>
        @endif
        @if($payment->notes)
            <tr>
                <td class="label">Note</td>
                <td class="value">{{ $payment->notes }}</td>
            </tr>
        @endif
    </table>
</div>

<table class="total">
    <tr>
        <td class="t-label">Montant réglé</td>
        <td class="t-value">{{ $fmt($payment->montant) }} FCFA</td>
    </tr>
</table>
<div class="paid">✓ Payé</div>

<div class="foot">
    Ce reçu atteste du règlement de l'abonnement Bimmo pour la période indiquée.
    Document généré automatiquement — il ne nécessite pas de signature.<br>
    Pour toute question : support@bimotech.sn
</div>

</body>
</html>
