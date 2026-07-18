{{--
    Relevé de commissions d'un collaborateur — justificatif de rémunération.

    Chaque ligne mensuelle est figée à la capture (nb agences, MRR, taux, commission) ;
    le mois courant, non encore capturé, est marqué « En cours ».
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
        table { width: 100%; border-collapse: collapse; margin-top: 6px; }
        th { text-align: left; font-size: 8.5px; text-transform: uppercase; letter-spacing: 0.5px; color: #5A6B6A;
             border-bottom: 1.5px solid #DCD3BE; padding: 8px 6px; }
        td { padding: 7px 6px; border-bottom: 1px solid #EDE7D8; font-size: 10.5px; }
        .r { text-align: right; }
        .gold { color: #B8892B; font-weight: bold; }
        .badge { display: inline-block; font-size: 8px; font-weight: bold; padding: 2px 7px; border-radius: 10px; }
        .badge-fige { background: #EDE7D8; color: #5A6B6A; }
        .badge-cours { background: #F3E7CE; color: #B8892B; }
        .foot { margin-top: 26px; padding-top: 12px; border-top: 1px solid #DCD3BE; font-size: 8.5px; color: #5A6B6A; line-height: 1.5; }
    </style>
</head>
<body>

@php $fmt = fn ($n) => number_format((float) $n, 0, ',', ' '); @endphp

<div class="head">
    <div class="brand">BIMO-tech</div>
    <div class="brand-sub">Éditeur de Bimmo — logiciel de gestion immobilière<br>Dakar, Sénégal · support@bimotech.sn</div>
    <div class="doc-title">Relevé de commissions — {{ $collaborateur->name }}</div>
    <div class="doc-meta">Émis le {{ $genereLe->locale('fr')->isoFormat('D MMMM Y') }}</div>
</div>

<table>
    <thead>
        <tr>
            <th>Mois</th>
            <th class="r">Agences</th>
            <th class="r">MRR total</th>
            <th class="r">Taux</th>
            <th class="r">Commission</th>
            <th class="r">Statut</th>
        </tr>
    </thead>
    <tbody>
        @forelse($lignes as $l)
            <tr>
                <td style="font-weight:bold;">{{ \Illuminate\Support\Str::ucfirst($l['mois']->locale('fr')->isoFormat('MMMM Y')) }}</td>
                <td class="r">{{ $fmt($l['nb_agences']) }}</td>
                <td class="r">{{ $fmt($l['mrr_total']) }} F</td>
                <td class="r">{{ rtrim(rtrim(number_format($l['taux'], 2, ',', ''), '0'), ',') }} %</td>
                <td class="r gold">{{ $fmt($l['commission']) }} F</td>
                <td class="r"><span class="badge {{ $l['fige'] ? 'badge-fige' : 'badge-cours' }}">{{ $l['fige'] ? 'Figé' : 'En cours' }}</span></td>
            </tr>
        @empty
            <tr><td colspan="6" style="text-align:center; color:#5A6B6A; padding:20px;">Aucun historique de commission.</td></tr>
        @endforelse
    </tbody>
</table>

<div class="foot">
    Base de calcul : MRR actuel des agences attribuées au collaborateur (une agence en échec de paiement compte quand même).
    Commission = taux × MRR total. Les mois figés ne sont jamais recalculés rétroactivement.
    Document interne BIMO-tech — ne vaut pas facture.
</div>

</body>
</html>
