@php
    use Illuminate\Support\Facades\Storage;

    $bailleur       = $contrat->bien?->proprietaire;
    $bailleurProfil = $bailleur?->proprietaire;
    $locataire      = $contrat->locataire;
    $locataireProfil = $locataire?->locataire;
    $bien = $contrat->bien;
    $fmt  = fn ($n) => number_format((float) $n, 0, ',', ' ');

    $debut     = $contrat->date_debut ? \Carbon\Carbon::parse($contrat->date_debut) : null;
    $dureeMois = ($debut && $contrat->date_fin) ? $debut->diffInMonths(\Carbon\Carbon::parse($contrat->date_fin)) : null;

    $typesTitre = ['habitation' => "à usage d'habitation", 'commercial' => 'à usage commercial', 'mixte' => 'à usage mixte', 'saisonnier' => 'à usage saisonnier'];
    $typeTitre  = $typesTitre[$contrat->type_bail] ?? "à usage d'habitation";
    $usage      = $contrat->type_bail === 'commercial' ? 'à usage commercial'
                : ($contrat->type_bail === 'mixte' ? 'à usage mixte' : "à usage exclusif d'habitation");

    $bailleurAdresse  = $bailleur?->adresse ?: trim(($bailleurProfil?->quartier ? $bailleurProfil->quartier . ', ' : '') . ($bailleurProfil?->ville ?? '')) ?: '________';
    $locataireAdresse = $locataire?->adresse ?: '________';
    $bienAdresse      = trim(($bien?->quartier ? $bien->quartier . ', ' : '') . ($bien?->adresse ?: '') . ' ' . ($bien?->ville ?? ''));
    $bienNom          = $bien?->titre ?: $bien?->reference;
    $cni              = $locataireProfil?->cni ?: '________';

    $agenceCoords = collect([$agency->adresse, $agency->ninea ? 'NINEA ' . $agency->ninea : null, $agency->telephone])->filter()->implode(' · ');

    // Signature de l'agence (obligatoire — le contrôleur bloque sinon)
    $signatureData = null;
    if ($agency->signature_path && Storage::disk('public')->exists($agency->signature_path)) {
        $signatureData = 'data:image/' . pathinfo($agency->signature_path, PATHINFO_EXTENSION) . ';base64,'
                       . base64_encode(Storage::disk('public')->get($agency->signature_path));
    }
    $logoData = null;
    if ($agency->logo_path && Storage::disk('public')->exists($agency->logo_path)) {
        $logoData = 'data:image/' . pathinfo($agency->logo_path, PATHINFO_EXTENSION) . ';base64,'
                  . base64_encode(Storage::disk('public')->get($agency->logo_path));
    }

    $hasClauses = ! empty(trim((string) $contrat->clauses_particulieres));
    $n = 0; // compteur d'articles — numérotation dynamique
@endphp
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8">
<style>
    @page { margin: 1.5cm 1.8cm; }
    * { box-sizing: border-box; }
    body { font-family: 'DejaVu Serif', Georgia, serif; color: #1a1a1a; font-size: 11.5px; line-height: 1.55; }
    p { margin: 0 0 7px; text-align: justify; }

    .letterhead { width: 100%; border-bottom: 2px solid #1a1a1a; padding-bottom: 10px; margin-bottom: 20px; }
    .letterhead td { vertical-align: middle; }
    .logo-cell { width: 60px; }
    .logo-cell img { width: 54px; height: auto; }
    .agence-nom { font-family: 'Helvetica', sans-serif; font-size: 13px; font-weight: bold; }
    .agence-detail { font-family: 'Helvetica', sans-serif; font-size: 9px; color: #555; margin-top: 2px; }

    h1.doc-title { text-align: center; font-size: 16px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; margin: 4px 0 3px; }
    .doc-sub { font-family: 'Helvetica', sans-serif; text-align: center; font-size: 9.5px; color: #555; margin-bottom: 22px; }

    .preamble { margin-bottom: 18px; }
    .party-block { margin: 9px 0; padding-left: 12px; border-left: 2px solid #ddd; }
    .party-label { font-family: 'Helvetica', sans-serif; font-size: 9px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; color: #B8892B; margin-bottom: 2px; }
    .center-muted { text-align: center; font-family: 'Helvetica', sans-serif; font-size: 9.5px; color: #555; }

    .article { margin-bottom: 15px; }
    .article h2 { font-size: 12px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.3px; margin-bottom: 5px; }
    .article ul { margin: 5px 0 5px 18px; padding: 0; }
    .article li { margin-bottom: 3px; text-align: justify; }

    .fill { background: #F3E9CE; padding: 0 3px; font-weight: bold; }
    .clause-libre { white-space: pre-line; display: block; margin-top: 4px; }

    .fait-a { margin-top: 26px; font-family: 'Helvetica', sans-serif; font-size: 10.5px; }

    .sig-zone { width: 100%; margin-top: 30px; }
    .sig-zone td { width: 50%; vertical-align: top; text-align: center; padding: 0 18px; }
    .sig-role { font-family: 'Helvetica', sans-serif; font-size: 10px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px; }
    .sig-img { height: 46px; }
    .sig-blank { height: 46px; }
    .sig-line { border-top: 1px solid #1a1a1a; padding-top: 5px; font-family: 'Helvetica', sans-serif; font-size: 9.5px; color: #555; }
    .sig-line-auto { color: #B8892B; font-weight: bold; }

    .legal-footer { margin-top: 34px; padding-top: 12px; border-top: 1px solid #ddd; font-family: 'Helvetica', sans-serif; font-size: 8.5px; color: #888; line-height: 1.5; text-align: center; }
</style>
</head>
<body>

    {{-- En-tête agence --}}
    <table class="letterhead">
        <tr>
            @if($logoData)<td class="logo-cell"><img src="{{ $logoData }}" alt=""></td>@endif
            <td>
                <div class="agence-nom">{{ $agency->name }}</div>
                @if($agenceCoords)<div class="agence-detail">{{ $agenceCoords }}</div>@endif
            </td>
        </tr>
    </table>

    <h1 class="doc-title">Contrat de bail {{ $typeTitre }}</h1>
    <div class="doc-sub">Établi conformément à la loi n° 88-04 du 16 juin 1988 et au Code des Obligations Civiles et Commerciales</div>

    {{-- Préambule --}}
    <div class="preamble">
        <p>Entre les soussignés :</p>
        <div class="party-block">
            <div class="party-label">Le Bailleur</div>
            <p><span class="fill">{{ $bailleur?->name ?? '________' }}</span>, demeurant à <span class="fill">{{ $bailleurAdresse }}</span>, ci-après dénommé « le Bailleur », représenté par {{ $agency->name }} en qualité de mandataire de gestion,</p>
        </div>
        <p class="center-muted">d'une part,</p>
        <div class="party-block">
            <div class="party-label">Le Locataire</div>
            <p><span class="fill">{{ $locataire?->name ?? '________' }}</span>, demeurant à <span class="fill">{{ $locataireAdresse }}</span>, titulaire de la pièce d'identité n° <span class="fill">{{ $cni }}</span>, ci-après dénommé « le Locataire »,</p>
        </div>
        <p class="center-muted">d'autre part,</p>
        <p>Il a été convenu et arrêté ce qui suit :</p>
    </div>

    <div class="article">
        <h2>Article {{ ++$n }} — Désignation du bien loué</h2>
        <p>Le Bailleur donne à bail au Locataire, qui accepte, le bien immobilier désigné ci-après : <span class="fill">{{ $bienNom }}</span>, sis à <span class="fill">{{ $bienAdresse }}</span>, {{ $usage }}.</p>
    </div>

    <div class="article">
        <h2>Article {{ ++$n }} — Durée du bail</h2>
        <p>Le présent bail est conclu pour une durée de <span class="fill">{{ $dureeMois ? $dureeMois . ' mois' : 'durée indéterminée' }}</span>, à compter du <span class="fill">{{ $debut ? $debut->locale('fr')->isoFormat('D MMMM Y') : '________' }}</span>. Conformément à la loi n° 88-04, il est reconduit par tacite reconduction pour une nouvelle période équivalente, sauf dénonciation par l'une des parties dans les conditions prévues au présent contrat.</p>
    </div>

    <div class="article">
        <h2>Article {{ ++$n }} — Loyer et modalités de paiement</h2>
        <p>Le loyer mensuel est fixé à <span class="fill">{{ $fmt($contrat->loyer_contractuel) }} F CFA</span>, payable d'avance le premier jour de chaque mois, par tout moyen convenu entre les parties.</p>
    </div>

    <div class="article">
        <h2>Article {{ ++$n }} — Dépôt de garantie</h2>
        <p>À la signature du présent contrat, le Locataire verse au Bailleur un dépôt de garantie de <span class="fill">{{ $fmt($contrat->caution) }} F CFA</span>, conformément aux usages en vigueur. Ce dépôt sera restitué au Locataire dans un délai raisonnable après son départ, déduction faite, le cas échéant, des sommes dues au titre de réparations locatives dûment justifiées.</p>
    </div>

    <div class="article">
        <h2>Article {{ ++$n }} — Obligations du Bailleur</h2>
        <ul>
            <li>Délivrer le bien loué en bon état d'usage et de réparation ;</li>
            <li>Assurer au Locataire une jouissance paisible des lieux loués ;</li>
            <li>Prendre en charge les grosses réparations qui ne résultent pas d'un défaut d'entretien du Locataire.</li>
        </ul>
    </div>

    <div class="article">
        <h2>Article {{ ++$n }} — Obligations du Locataire</h2>
        <ul>
            <li>Payer le loyer aux termes convenus ;</li>
            <li>User du bien loué en bon père de famille et le maintenir en bon état ;</li>
            <li>Répondre des dégradations survenues pendant la durée du bail, sauf celles résultant de la vétusté ou d'un cas de force majeure ;</li>
            <li>Ne pas sous-louer ni céder le bail, en tout ou partie, sans l'accord écrit préalable du Bailleur ;</li>
            <li>Acquitter sa quote-part des factures d'eau, d'électricité et autres charges locatives.</li>
        </ul>
    </div>

    <div class="article">
        <h2>Article {{ ++$n }} — Charges locatives</h2>
        <p>Les charges suivantes sont à la charge exclusive du Locataire : eau, électricité, et charges de copropriété le cas échéant, sauf stipulation contraire mentionnée en annexe.</p>
    </div>

    @if($hasClauses)
    <div class="article">
        <h2>Article {{ ++$n }} — Clauses particulières</h2>
        <p>Outre les dispositions qui précèdent, les parties conviennent des clauses particulières suivantes :</p>
        <span class="fill clause-libre">{{ $contrat->clauses_particulieres }}</span>
    </div>
    @endif

    <div class="article">
        <h2>Article {{ ++$n }} — Résiliation</h2>
        <p>Chaque partie peut mettre fin au présent bail moyennant un préavis écrit, notifié à l'autre partie, dans les délais prévus par la loi n° 88-04 et le Code des Obligations Civiles et Commerciales. En cas de manquement grave de l'une des parties à ses obligations, l'autre partie pourra saisir la juridiction compétente après mise en demeure restée sans effet.</p>
    </div>

    <div class="article">
        <h2>Article {{ ++$n }} — Élection de domicile et juridiction compétente</h2>
        <p>Pour l'exécution du présent contrat, les parties font élection de domicile à l'adresse mentionnée ci-dessus. Tout litige relatif à l'exécution ou à la résiliation du présent bail relève de la compétence des juridictions sénégalaises.</p>
    </div>

    <div class="fait-a">Fait à {{ $agency->adresse ? \Illuminate\Support\Str::of($agency->adresse)->afterLast(',')->trim() : 'Dakar' }}, le <span class="fill">{{ now()->locale('fr')->isoFormat('D MMMM Y') }}</span>, en deux exemplaires originaux, chaque partie reconnaissant avoir reçu le sien.</div>

    <table class="sig-zone">
        <tr>
            <td>
                <div class="sig-role">Le Bailleur (représenté par l'agence mandataire)</div>
                @if($signatureData)
                    <img class="sig-img" src="{{ $signatureData }}" alt="">
                    <div class="sig-line sig-line-auto">Signature enregistrée — {{ $agency->name }}</div>
                @else
                    <div class="sig-blank"></div>
                    <div class="sig-line">Signature et cachet de l'agence</div>
                @endif
            </td>
            <td>
                <div class="sig-role">Le Locataire</div>
                <div class="sig-blank"></div>
                <div class="sig-line">Signature précédée de la mention « Lu et approuvé »</div>
            </td>
        </tr>
    </table>

    <div class="legal-footer">
        Modèle établi sur la base de la loi n° 88-04 du 16 juin 1988 relative aux baux à usage d'habitation et du Code des Obligations Civiles et Commerciales du Sénégal.<br>
        Ce document est un modèle standard généré par {{ config('app.name', 'Bimmo') }} — il est recommandé de le faire valider par un professionnel du droit pour toute situation particulière, et de le faire enregistrer auprès du bureau de l'Enregistrement compétent.
    </div>

</body>
</html>
