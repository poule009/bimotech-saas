<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Mentions légales — BimoTech Immo</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@300;400;500&display=swap" media="print" onload="this.media='all'">
<noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@300;400;500&display=swap"></noscript>
@vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-body bg-[#0d1117] text-[#e6edf3]">

<nav class="fixed top-0 left-0 right-0 z-[100] px-[5%] h-16 flex items-center justify-between bg-[rgba(13,17,23,.85)] backdrop-blur-md border-b border-[rgba(255,255,255,.08)]">
    <a href="{{ url('/') }}" class="no-underline flex items-center" aria-label="BiMO-tech Immo — Accueil">
        <img src="/images/logo.jpeg" alt="BiMO-tech Immo" class="h-[34px] w-auto">
    </a>
    <a href="{{ url('/') }}" class="font-body text-[13px] text-[#8b949e] no-underline hover:text-[#e6edf3] transition-colors duration-200">← Retour à l'accueil</a>
</nav>

<div class="max-w-[720px] mx-auto px-[5%] pt-[120px] pb-24">

    <div class="font-body font-semibold text-[11px] uppercase tracking-[2px] text-bimo-gold mb-4">Légal</div>
    <h1 class="font-display font-extrabold text-[clamp(26px,4vw,40px)] tracking-tight mb-2">Mentions légales</h1>
    <p class="font-body text-[13px] text-[#484f58] mb-12">Dernière mise à jour : {{ date('d/m/Y') }}</p>

    @foreach([
        ['1. Éditeur du site', null, [
            ['Dénomination sociale', 'BimoTech Immo'],
            ['Forme juridique', '[Forme juridique — ex : SARL, SAS]'],
            ['NINEA', '[Votre NINEA]'],
            ['Siège social', 'Dakar, Sénégal'],
            ['Email', '<a href="mailto:contact@bimotech.sn" class="text-bimo-gold no-underline hover:underline">contact@bimotech.sn</a>'],
            ['Directeur de la publication', '[Nom du responsable légal]'],
        ]],
        ['2. Hébergement', null, [
            ['Hébergeur', '[Nom de l\'hébergeur]'],
            ['Adresse', '[Adresse de l\'hébergeur]'],
            ['Site', '[URL hébergeur]'],
        ]],
    ] as [$h, $text, $items])
    <h2 class="font-display font-bold text-[17px] text-[#e6edf3] mt-10 mb-3 pb-2 border-b border-[rgba(255,255,255,.08)]">{{ $h }}</h2>
    @if($text)<p class="font-body text-sm text-[#8b949e] leading-[1.8] mb-3">{{ $text }}</p>@endif
    @if($items)
    <div class="bg-[#161b22] border border-[rgba(255,255,255,.08)] rounded-[12px] px-6 py-5 mb-4 space-y-1.5">
        @foreach($items as [$lbl, $val])
        <p class="font-body text-sm text-[#8b949e]"><strong class="text-[#e6edf3] font-medium">{{ $lbl }} :</strong> {!! $val !!}</p>
        @endforeach
    </div>
    @endif
    @endforeach

    <h2 class="font-display font-bold text-[17px] text-[#e6edf3] mt-10 mb-3 pb-2 border-b border-[rgba(255,255,255,.08)]">3. Propriété intellectuelle</h2>
    <p class="font-body text-sm text-[#8b949e] leading-[1.8] mb-3">L'ensemble du contenu de la plateforme BimoTech Immo — textes, graphismes, logotypes, icônes, images, code source — est la propriété exclusive de BimoTech ou de ses partenaires et est protégé par les lois sénégalaises et internationales relatives à la propriété intellectuelle.</p>
    <p class="font-body text-sm text-[#8b949e] leading-[1.8] mb-3">Toute reproduction, représentation, modification, publication ou adaptation de tout ou partie des éléments du site, quel que soit le moyen ou le procédé utilisé, est interdite sans autorisation écrite préalable de BimoTech.</p>

    <h2 class="font-display font-bold text-[17px] text-[#e6edf3] mt-10 mb-3 pb-2 border-b border-[rgba(255,255,255,.08)]">4. Données personnelles</h2>
    <p class="font-body text-sm text-[#8b949e] leading-[1.8] mb-3">Les données personnelles collectées via la plateforme (nom, email, informations d'agence) sont utilisées exclusivement dans le cadre de la fourniture du service BimoTech Immo.</p>
    <p class="font-body text-sm text-[#8b949e] leading-[1.8] mb-3">Conformément à la loi sénégalaise n°2008-12 du 25 janvier 2008 sur la protection des données à caractère personnel et au règlement de la Commission de Protection des Données Personnelles (CDP), vous disposez d'un droit d'accès, de rectification et de suppression de vos données.</p>
    <p class="font-body text-sm text-[#8b949e] leading-[1.8] mb-3">Pour exercer ces droits, contactez-nous à : <a href="mailto:contact@bimotech.sn" class="text-bimo-gold no-underline hover:underline">contact@bimotech.sn</a></p>
    <p class="font-body text-sm text-[#8b949e] leading-[1.8] mb-3">Pour en savoir plus, consultez notre <a href="{{ route('confidentialite') }}" class="text-bimo-gold no-underline hover:underline">politique de confidentialité</a>.</p>

    <h2 class="font-display font-bold text-[17px] text-[#e6edf3] mt-10 mb-3 pb-2 border-b border-[rgba(255,255,255,.08)]">5. Cookies</h2>
    <p class="font-body text-sm text-[#8b949e] leading-[1.8] mb-3">La plateforme BimoTech utilise des cookies strictement nécessaires au fonctionnement du service (session, sécurité CSRF). Aucun cookie publicitaire ou de tracking tiers n'est utilisé.</p>

    <h2 class="font-display font-bold text-[17px] text-[#e6edf3] mt-10 mb-3 pb-2 border-b border-[rgba(255,255,255,.08)]">6. Limitation de responsabilité</h2>
    <p class="font-body text-sm text-[#8b949e] leading-[1.8] mb-3">BimoTech s'efforce d'assurer l'exactitude et la mise à jour des informations diffusées. Toutefois, BimoTech ne peut garantir l'exactitude, la précision ou l'exhaustivité des informations mises à disposition.</p>
    <p class="font-body text-sm text-[#8b949e] leading-[1.8] mb-3">BimoTech ne pourra être tenu responsable des dommages directs ou indirects résultant de l'utilisation de la plateforme ou de l'impossibilité d'y accéder.</p>

    <h2 class="font-display font-bold text-[17px] text-[#e6edf3] mt-10 mb-3 pb-2 border-b border-[rgba(255,255,255,.08)]">7. Droit applicable</h2>
    <p class="font-body text-sm text-[#8b949e] leading-[1.8] mb-3">Les présentes mentions légales sont soumises au droit sénégalais. Tout litige relatif à l'utilisation de la plateforme BimoTech relève de la compétence exclusive des tribunaux de Dakar, Sénégal.</p>

    <h2 class="font-display font-bold text-[17px] text-[#e6edf3] mt-10 mb-3 pb-2 border-b border-[rgba(255,255,255,.08)]">8. Contact</h2>
    <p class="font-body text-sm text-[#8b949e] leading-[1.8]">Pour toute question relative aux présentes mentions légales : <a href="mailto:contact@bimotech.sn" class="text-bimo-gold no-underline hover:underline">contact@bimotech.sn</a></p>

</div>

<footer class="px-[5%] py-8 border-t border-[rgba(255,255,255,.08)] mt-8 flex flex-col md:flex-row items-center justify-between gap-4 flex-wrap">
    <div class="font-display font-extrabold text-[15px] text-bimo-gold">BimoTech Immo</div>
    <div class="flex flex-col md:flex-row items-center gap-4 md:gap-6">
        @foreach([[url('/'),'Accueil'],[route('contact'),'Contact'],[route('confidentialite'),'Confidentialité']] as [$href,$lbl])
        <a href="{{ $href }}" class="font-body text-xs text-[#8b949e] no-underline hover:text-[#e6edf3] transition-colors duration-200">{{ $lbl }}</a>
        @endforeach
    </div>
    <div class="font-body text-xs text-[#484f58]">© {{ date('Y') }} BimoTech · Dakar, Sénégal</div>
</footer>

</body>
</html>
