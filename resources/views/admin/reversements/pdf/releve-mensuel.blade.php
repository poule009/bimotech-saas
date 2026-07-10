@php
    use Illuminate\Support\Facades\Storage;

    $fmt = fn ($n) => number_format((float) $n, 0, ',', ' ');
    $periodeLabel = $periode
        ? \Carbon\Carbon::parse($periode . '-01')->locale('fr')->isoFormat('MMMM Y')
        : 'Situation globale';

    $agenceCoords = collect([$agency?->adresse, $agency?->ninea ? 'NINEA ' . $agency->ninea : null])->filter()->implode(' · ');

    $img = function ($path) {
        if ($path && Storage::disk('public')->exists($path)) {
            return 'data:image/' . pathinfo($path, PATHINFO_EXTENSION) . ';base64,' . base64_encode(Storage::disk('public')->get($path));
        }
        return null;
    };
    $logoData      = $img($agency?->logo_path);
    $signatureData = $img($agency?->signature_path);
    $cachetData    = $img($agency?->cachet_path);

    $depenses = collect($compte['paiements'])->flatMap(fn ($p) => $p->depenses);
@endphp
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8">
<style>
    @page { margin: 1.6cm 1.9cm; }
    * { box-sizing: border-box; }
    body { font-family: 'DejaVu Serif', Georgia, serif; color: #1a1a1a; font-size: 12px; line-height: 1.55; }

    .letterhead { width: 100%; border-bottom: 2px solid #1a1a1a; padding-bottom: 12px; margin-bottom: 26px; }
    .letterhead td { vertical-align: middle; }
    .logo-cell { width: 56px; }
    .logo-cell img { width: 50px; height: auto; }
    .agence-nom { font-family: 'DejaVu Sans', sans-serif; font-size: 13px; font-weight: bold; }
    .agence-detail { font-family: 'DejaVu Sans', sans-serif; font-size: 9.5px; color: #555; margin-top: 2px; }
    .ref { text-align: right; font-family: 'DejaVu Sans', sans-serif; }
    .ref .num { font-size: 11px; font-weight: bold; color: #B8892B; }
    .ref .date { font-size: 9.5px; color: #555; margin-top: 2px; }

    h1.doc-title { text-align: center; font-size: 17px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
    .doc-sub { text-align: center; font-family: 'DejaVu Sans', sans-serif; font-size: 10.5px; color: #555; margin-bottom: 24px; }
    .doc-sub strong { color: #1a1a1a; }

    .section-title { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.06em; color: #6B6255; margin: 18px 0 6px; }

    table.lines { width: 100%; border-collapse: collapse; }
    table.lines td { padding: 7px 3px; border-bottom: 1px solid #eee; font-size: 12px; font-family: 'DejaVu Sans', sans-serif; }
    table.lines td.amount { text-align: right; font-weight: bold; white-space: nowrap; }
    td.plus { color: #2D5F4C; }
    td.minus { color: #B4472E; }

    .subtotal { width: 100%; margin-top: 4px; }
    .subtotal td { padding: 6px 3px; font-family: 'DejaVu Sans', sans-serif; font-size: 12px; }
    .subtotal td.amount { text-align: right; font-weight: bold; }

    .net-box { margin-top: 16px; border: 2px solid #1a1a1a; border-radius: 6px; padding: 12px 16px; }
    .net-box table { width: 100%; }
    .net-box td { font-family: 'DejaVu Sans', sans-serif; }
    .net-box .lbl { font-size: 13px; font-weight: bold; }
    .net-box .val { text-align: right; font-size: 17px; font-weight: bold; color: #B8892B; }

    .solde-final { margin-top: 12px; }
    .solde-final td { font-family: 'DejaVu Sans', sans-serif; font-size: 13px; font-weight: bold; padding: 4px 3px; }
    .solde-final td.amount { text-align: right; }

    .sig-zone { width: 100%; margin-top: 40px; }
    .sig-zone .sig-cell { width: 240px; text-align: center; vertical-align: top; }
    .sig-role { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; color: #555; margin-bottom: 8px; }
    .sig-imgs { height: 52px; }
    .sig-imgs img.sig { height: 46px; vertical-align: bottom; }
    .sig-imgs img.cachet { height: 52px; vertical-align: bottom; margin-left: 8px; }
    .sig-line { border-top: 1px solid #1a1a1a; padding-top: 6px; font-family: 'DejaVu Sans', sans-serif; font-size: 10px; color: #555; }

    .legal-footer { margin-top: 34px; padding-top: 12px; border-top: 1px solid #ddd; font-family: 'DejaVu Sans', sans-serif; font-size: 9px; color: #999; text-align: center; }
</style>
</head>
<body>

    <table class="letterhead">
        <tr>
            @if($logoData)<td class="logo-cell"><img src="{{ $logoData }}" alt=""></td>@endif
            <td>
                <div class="agence-nom">{{ $agency?->name }}</div>
                @if($agenceCoords)<div class="agence-detail">{{ $agenceCoords }}</div>@endif
            </td>
            <td class="ref">
                <div class="num">{{ $refDoc }}</div>
                <div class="date">Édité le {{ now()->locale('fr')->isoFormat('D MMMM Y') }}</div>
            </td>
        </tr>
    </table>

    <h1 class="doc-title">Relevé de gestion locative</h1>
    <div class="doc-sub">Propriétaire : <strong>{{ $proprietaire->name }}</strong> · Période : <strong>{{ $periodeLabel }}</strong></div>

    {{-- Loyers encaissés --}}
    <div class="section-title">Loyers encaissés</div>
    <table class="lines">
        @forelse($compte['paiements'] as $p)
            <tr>
                <td>{{ $p->contrat?->bien?->titre ?: ('Bien ' . $p->contrat?->bien?->reference) }} — payé le {{ optional($p->date_paiement)->format('d/m/Y') }}</td>
                <td class="amount plus">+{{ $fmt($p->montant_encaisse) }} F</td>
            </tr>
        @empty
            <tr><td colspan="2" style="color:#999;">Aucun loyer encaissé sur la période.</td></tr>
        @endforelse
    </table>
    <table class="subtotal">
        <tr><td>Total encaissé</td><td class="amount">{{ $fmt($compte['loyers_encaisses']) }} F</td></tr>
    </table>

    {{-- Déductions --}}
    <div class="section-title">Déductions</div>
    <table class="lines">
        <tr><td>Commission de l'agence</td><td class="amount minus">−{{ $fmt($compte['commissions_deduites']) }} F</td></tr>
        @if($compte['brs_retenu'] > 0)
            <tr><td>BRS retenu</td><td class="amount minus">−{{ $fmt($compte['brs_retenu']) }} F</td></tr>
        @endif
        @foreach($depenses as $d)
            <tr><td>Dépense — {{ $d->libelle }} ({{ $d->categorie_libelle }})</td><td class="amount minus">−{{ $fmt($d->montant) }} F</td></tr>
        @endforeach
    </table>

    {{-- Net à reverser --}}
    <div class="net-box">
        <table>
            <tr><td class="lbl">Net à reverser au propriétaire</td><td class="val">{{ $fmt($compte['net_du']) }} F CFA</td></tr>
        </table>
    </div>

    {{-- Reversements + solde --}}
    @if($reversementsPeriode->isNotEmpty() || $compte['reversements_effectues'] > 0)
        <div class="section-title">Reversements effectués</div>
        <table class="lines">
            @foreach($reversementsPeriode as $r)
                <tr>
                    <td>{{ optional($r->date_reversement)->format('d/m/Y') }} — {{ $r->mode_paiement_libelle }}{{ $r->reference ? ' · ' . $r->reference : '' }}</td>
                    <td class="amount minus">−{{ $fmt($r->montant) }} F</td>
                </tr>
            @endforeach
        </table>
        <table class="solde-final">
            <tr><td>Solde restant à reverser</td><td class="amount">{{ $fmt($compte['solde_restant']) }} F CFA</td></tr>
        </table>
    @endif

    {{-- Signature --}}
    <table class="sig-zone">
        <tr>
            <td></td>
            <td class="sig-cell">
                <div class="sig-role">Pour l'agence</div>
                <div class="sig-imgs">
                    @if($signatureData)<img class="sig" src="{{ $signatureData }}" alt="">@endif
                    @if($cachetData)<img class="cachet" src="{{ $cachetData }}" alt="">@endif
                </div>
                <div class="sig-line">{{ $agency?->name }}</div>
            </td>
        </tr>
    </table>

    <div class="legal-footer">
        Relevé de gestion établi par {{ config('app.name', 'Bimothèque Immo') }} pour le compte de l'agence émettrice. Document à conserver.
    </div>

</body>
</html>
