@php
    use Illuminate\Support\Facades\Storage;

    $fmt = fn ($n) => number_format((float) $n, 0, ',', ' ');
    $periode = $paiement->periode ? \Carbon\Carbon::parse($paiement->periode) : now();
    $datePaie = $paiement->date_paiement ? \Carbon\Carbon::parse($paiement->date_paiement) : now();

    // Ventilation fiscale (colonnes figées à la génération par FiscalService)
    $loyerHt    = (float) ($paiement->loyer_ht ?? $paiement->loyer_nu ?? 0);
    $tvaLoyer   = (float) ($paiement->tva_loyer ?? 0);
    $loyerTtc   = (float) ($paiement->loyer_ttc ?? ($loyerHt + $tvaLoyer));
    $charges    = (float) ($paiement->charges_amount ?? 0);
    $tvaCharges = (float) ($paiement->tva_charges ?? 0);
    $tom        = (float) ($paiement->tom_amount ?? 0);
    $total      = (float) ($paiement->montant_encaisse ?? ($loyerTtc + $charges + $tvaCharges + $tom));
    $commHt     = (float) ($paiement->commission_agence ?? 0);
    $tvaComm    = (float) ($paiement->tva_commission ?? 0);
    $commTtc    = (float) ($paiement->commission_ttc ?? ($commHt + $tvaComm));
    $netProprio = (float) ($paiement->net_a_verser_proprietaire ?? 0);

    // Taux affichés déduits des montants figés (assiette loyer = loyer HT + TOM)
    $assietteLoyer = $loyerHt + $tom;
    $tauxLoyer   = $assietteLoyer > 0 ? round($tvaLoyer / $assietteLoyer * 100) : 0;
    $tauxCharges = $charges > 0 ? round($tvaCharges / $charges * 100) : 0;

    // Détail agence : réservé aux reçus propriétaire / agence (jamais sur le reçu locataire)
    $showAgence = in_array($destinataire ?? 'agence', ['proprietaire', 'agence'], true);

    $modes = [
        'especes' => 'Espèces', 'virement' => 'Virement bancaire', 'cheque' => 'Chèque',
        'wave' => 'Wave', 'orange_money' => 'Orange Money', 'free_money' => 'Free Money', 'e_money' => 'E-Money',
    ];
    $modeLabel = $modes[$paiement->mode_paiement] ?? ucfirst((string) $paiement->mode_paiement);

    $numQuittance = $paiement->reference_paiement ?: ('Q-' . $periode->format('Y') . '-' . str_pad((string) $paiement->id, 4, '0', STR_PAD_LEFT));
    $bienNom      = $bien?->titre ?: $bien?->reference;
    $bienAdresse  = trim(($bien?->quartier ? $bien->quartier . ', ' : '') . ($bien?->adresse ?: '') . ' ' . ($bien?->ville ?? ''));
    $agenceCoords = collect([$agence?->adresse, $agence?->ninea ? 'NINEA ' . $agence->ninea : null])->filter()->implode(' · ');

    $img = function ($path) use ($agence) {
        if ($path && Storage::disk('public')->exists($path)) {
            return 'data:image/' . pathinfo($path, PATHINFO_EXTENSION) . ';base64,' . base64_encode(Storage::disk('public')->get($path));
        }
        return null;
    };
    $signatureData = $img($agence?->signature_path);
    $cachetData    = $img($agence?->cachet_path);
    $logoData      = $img($agence?->logo_path);
@endphp
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8">
<style>
    @page { margin: 1.6cm 1.9cm; }
    * { box-sizing: border-box; }
    body { font-family: 'DejaVu Serif', Georgia, serif; color: #1a1a1a; font-size: 12px; line-height: 1.6; }
    .sans { font-family: 'DejaVu Sans', sans-serif; }

    .letterhead { width: 100%; border-bottom: 2px solid #1a1a1a; padding-bottom: 12px; margin-bottom: 26px; }
    .letterhead td { vertical-align: middle; }
    .logo-cell { width: 56px; }
    .logo-cell img { width: 50px; height: auto; }
    .agence-nom { font-family: 'DejaVu Sans', sans-serif; font-size: 13px; font-weight: bold; }
    .agence-detail { font-family: 'DejaVu Sans', sans-serif; font-size: 9.5px; color: #555; margin-top: 2px; }
    .quit-ref { text-align: right; font-family: 'DejaVu Sans', sans-serif; }
    .quit-ref .num { font-size: 11.5px; font-weight: bold; color: #B8892B; }
    .quit-ref .date { font-size: 9.5px; color: #555; margin-top: 2px; }

    h1.doc-title { text-align: center; font-size: 18px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
    .doc-sub { text-align: center; font-family: 'DejaVu Sans', sans-serif; font-size: 10.5px; color: #555; margin-bottom: 28px; }
    .doc-sub strong { color: #1a1a1a; }

    .receipt-body { font-size: 13px; text-align: justify; margin-bottom: 8px; }
    .receipt-body p { margin-bottom: 13px; }
    .beneficiaire { text-align: center; font-size: 15px; font-weight: bold; margin: 6px 0 13px; }
    .fill { background: #F3E9CE; padding: 0 4px; font-weight: bold; }

    .detail-table { width: 100%; border-collapse: collapse; margin: 22px 0; font-family: 'DejaVu Sans', sans-serif; }
    .detail-table td { padding: 9px 3px; border-bottom: 1px solid #eee; font-size: 12px; }
    .detail-table td:first-child { color: #555; font-weight: bold; width: 210px; }
    .detail-table td:last-child { font-weight: bold; text-align: right; }
    .detail-table tr.total td { border-top: 2px solid #1a1a1a; border-bottom: none; padding-top: 14px; font-size: 13.5px; }
    .detail-table tr.total td:last-child { color: #B8892B; font-size: 15px; }

    .mode-paiement { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #555; margin-bottom: 30px; }
    .mode-paiement strong { color: #1a1a1a; }

    .sig-zone { width: 100%; margin-top: 34px; }
    .sig-zone .sig-cell { width: 240px; text-align: center; vertical-align: top; }
    .sig-role { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; color: #555; margin-bottom: 8px; }
    .sig-imgs { height: 52px; }
    .sig-imgs img.sig { height: 46px; vertical-align: bottom; }
    .sig-imgs img.cachet { height: 52px; vertical-align: bottom; margin-left: 8px; }
    .sig-line { border-top: 1px solid #1a1a1a; padding-top: 6px; font-family: 'DejaVu Sans', sans-serif; font-size: 10px; color: #555; }

    .legal-footer { margin-top: 42px; padding-top: 12px; border-top: 1px solid #ddd; font-family: 'DejaVu Sans', sans-serif; font-size: 9px; color: #999; text-align: center; line-height: 1.6; }
</style>
</head>
<body>

    {{-- En-tête --}}
    <table class="letterhead">
        <tr>
            @if($logoData)<td class="logo-cell"><img src="{{ $logoData }}" alt=""></td>@endif
            <td>
                <div class="agence-nom">{{ $agence?->name }}</div>
                @if($agenceCoords)<div class="agence-detail">{{ $agenceCoords }}</div>@endif
            </td>
            <td class="quit-ref">
                <div class="num">Quittance n° {{ $numQuittance }}</div>
                <div class="date">Émise le {{ $datePaie->locale('fr')->isoFormat('D MMMM Y') }}</div>
            </td>
        </tr>
    </table>

    <h1 class="doc-title">Quittance de loyer</h1>
    <div class="doc-sub">Mois de <strong>{{ $periode->locale('fr')->isoFormat('MMMM Y') }}</strong></div>

    {{-- Corps --}}
    <div class="receipt-body">
        <p>Je soussigné(e), <span class="fill">{{ $agence?->name }}</span>, agissant en qualité de mandataire de gestion pour le compte du Bailleur <span class="fill">{{ $proprietaire?->name ?? '—' }}</span>, reconnais avoir reçu de :</p>
        <div class="beneficiaire">{{ $locataire?->name ?? '—' }}</div>
        <p>la somme ci-dessous, au titre du loyer du bien désigné, et lui en donne quittance, sous réserve de tous mes droits.</p>
    </div>

    <table class="detail-table">
        <tr><td>Bien loué</td><td>{{ $bienNom }}{{ $bienAdresse ? ' — ' . $bienAdresse : '' }}</td></tr>
        <tr><td>Période concernée</td><td>{{ $periode->copy()->startOfMonth()->locale('fr')->isoFormat('D MMMM Y') }} au {{ $periode->copy()->endOfMonth()->locale('fr')->isoFormat('D MMMM Y') }}</td></tr>
        <tr><td>Loyer hors taxes</td><td>{{ $fmt($loyerHt) }} F CFA</td></tr>
        @if($tvaLoyer > 0)<tr><td>TVA sur loyer ({{ $tauxLoyer }} %)</td><td>{{ $fmt($tvaLoyer) }} F CFA</td></tr>@endif
        <tr><td>Loyer TTC</td><td>{{ $fmt($loyerTtc) }} F CFA</td></tr>
        @if($charges > 0)
            <tr><td>Charges</td><td>{{ $fmt($charges) }} F CFA</td></tr>
            @if($tvaCharges > 0)<tr><td>TVA sur charges ({{ $tauxCharges }} %)</td><td>{{ $fmt($tvaCharges) }} F CFA</td></tr>@endif
        @else
            <tr><td>Charges</td><td>Incluses</td></tr>
        @endif
        @if($tom > 0)<tr><td>TOM refacturée au locataire</td><td>{{ $fmt($tom) }} F CFA</td></tr>@endif
        <tr class="total"><td>Montant reçu</td><td>{{ $fmt($total) }} F CFA</td></tr>
    </table>

    @if($showAgence && ($commTtc > 0 || $netProprio > 0))
        <table class="detail-table">
            <tr><td>Commission agence HT</td><td>{{ $fmt($commHt) }} F CFA</td></tr>
            @if($tvaComm > 0)<tr><td>TVA sur commission (18 %)</td><td>{{ $fmt($tvaComm) }} F CFA</td></tr>@endif
            <tr><td>Commission agence TTC</td><td>{{ $fmt($commTtc) }} F CFA</td></tr>
            <tr class="total"><td>Net reversé au propriétaire</td><td>{{ $fmt($netProprio) }} F CFA</td></tr>
        </table>
    @endif

    <div class="mode-paiement">Réglé par <strong>{{ $modeLabel }}</strong> le {{ $datePaie->locale('fr')->isoFormat('D MMMM Y') }}.</div>

    {{-- Signature agence --}}
    <table class="sig-zone">
        <tr>
            <td></td>
            <td class="sig-cell">
                <div class="sig-role">Pour l'agence</div>
                <div class="sig-imgs">
                    @if($signatureData)<img class="sig" src="{{ $signatureData }}" alt="">@endif
                    @if($cachetData)<img class="cachet" src="{{ $cachetData }}" alt="">@endif
                </div>
                <div class="sig-line">{{ $agence?->name }}</div>
            </td>
        </tr>
    </table>

    <div class="legal-footer">
        Ce document fait foi de paiement pour la période mentionnée ci-dessus et doit être conservé par le Locataire.
        Document généré par {{ config('app.name', 'Bimothèque Immo') }} pour le compte de l'agence émettrice.
    </div>

</body>
</html>
