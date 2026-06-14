<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Confidentialité — bee</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@300;400;500&display=swap" media="print" onload="this.media='all'">
<noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@300;400;500&display=swap"></noscript>
@vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-body bg-bimo-bg text-bimo-text">

@include('partials.public-nav', ['active' => ''])

<div class="max-w-[720px] mx-auto px-[5%] pt-[120px] pb-24">

    <div class="font-body font-semibold text-[11px] uppercase tracking-[2px] text-marketing-gold mb-4">Légal</div>
    <h1 class="font-display font-extrabold text-[clamp(26px,4vw,40px)] tracking-tight text-bimo-text mb-2">Politique de confidentialité</h1>
    <p class="font-body text-sm text-bimo-text/40 mb-12">Dernière mise à jour : {{ date('d/m/Y') }}</p>

    <div class="bg-marketing-gold/[6%] border border-marketing-gold/20 rounded-[12px] px-6 py-5 mb-8">
        <p class="font-body text-sm text-marketing-gold/80 leading-relaxed">bee s'engage à protéger vos données personnelles. Nous ne vendons jamais vos données à des tiers et nous ne faisons aucune publicité ciblée.</p>
    </div>

    @foreach([
        ['1. Qui sommes-nous ?', ['bee est une plateforme SaaS de gestion immobilière destinée aux agences immobilières sénégalaises, éditée par bee, dont le siège est à Dakar, Sénégal.', 'Pour toute question relative à vos données : <a href="mailto:contact@bimotech.sn" class="text-marketing-gold no-underline hover:underline">contact@bimotech.sn</a>']],
        ['3. Finalités du traitement', ['Vos données sont utilisées exclusivement pour fournir et améliorer le service bee, gérer votre compte, générer les quittances et assurer la sécurité de la plateforme.']],
        ['5. Durée de conservation', ['Données de compte actif pendant toute la durée de l\'abonnement. 30 jours après résiliation (export possible). Quittances et documents fiscaux : 10 ans. Logs de sécurité : 12 mois glissants.']],
        ['7. Sécurité', ['bee met en œuvre le chiffrement des mots de passe (bcrypt), la protection CSRF, l\'isolation des données par agence (multi-tenancy), les connexions HTTPS obligatoires et les logs d\'audit.']],
        ['9. Cookies', ['bee utilise uniquement des cookies strictement nécessaires : cookie de session et cookie CSRF. Aucun cookie publicitaire, analytique tiers ou de tracking n\'est utilisé.']],
        ['10. Modifications', ['bee se réserve le droit de modifier la présente politique à tout moment. En cas de modification substantielle, vous serez notifié par email au moins 15 jours avant l\'entrée en vigueur.']],
        ['11. Contact', ['Pour toute question : <a href="mailto:contact@bimotech.sn" class="text-marketing-gold no-underline hover:underline">contact@bimotech.sn</a> — bee, Dakar, Sénégal']],
    ] as [$h, $paras])
    <h2 class="font-display font-bold text-[17px] text-bimo-text mt-10 mb-3 pb-2 border-b border-bimo-navy/10">{{ $h }}</h2>
    @foreach($paras as $p)<p class="font-body text-sm text-bimo-text/60 leading-[1.8] mb-3">{!! $p !!}</p>@endforeach
    @endforeach

    <h2 class="font-display font-bold text-[17px] text-bimo-text mt-10 mb-3 pb-2 border-b border-bimo-navy/10">2. Données collectées</h2>
    <h3 class="font-body font-semibold text-sm text-bimo-text mt-5 mb-2">Lors de la création de compte</h3>
    <ul class="space-y-1 mb-4">@foreach(['Nom et prénom de l\'administrateur','Adresse email professionnelle','Mot de passe (chiffré, jamais stocké en clair)','Nom et NINEA de l\'agence','Numéro de téléphone de l\'agence'] as $item)<li class="font-body text-sm text-bimo-text/60 pl-4 relative before:absolute before:left-0 before:text-marketing-gold before:content-['·']">{{ $item }}</li>@endforeach</ul>
    <h3 class="font-body font-semibold text-sm text-bimo-text mt-5 mb-2">Dans le cadre de l'utilisation du service</h3>
    <ul class="space-y-1 mb-4">@foreach(['Informations sur les biens immobiliers gérés','Données des propriétaires et locataires','Contrats de bail et documents associés','Historique des paiements de loyer','Logs d\'activité (actions réalisées, dates, adresses IP)'] as $item)<li class="font-body text-sm text-bimo-text/60 pl-4 relative before:absolute before:left-0 before:text-marketing-gold before:content-['·']">{{ $item }}</li>@endforeach</ul>

    <h2 class="font-display font-bold text-[17px] text-bimo-text mt-10 mb-3 pb-2 border-b border-bimo-navy/10">4. Base légale du traitement</h2>
    <ul class="space-y-1 mb-4">@foreach(['<strong class="text-bimo-text font-medium">L\'exécution du contrat :</strong> pour la fourniture du service souscrit','<strong class="text-bimo-text font-medium">L\'obligation légale :</strong> conservation des données fiscales et comptables','<strong class="text-bimo-text font-medium">L\'intérêt légitime :</strong> sécurité de la plateforme et prévention des fraudes'] as $item)<li class="font-body text-sm text-bimo-text/60 pl-4 relative before:absolute before:left-0 before:text-marketing-gold before:content-['·']">{!! $item !!}</li>@endforeach</ul>

    <h2 class="font-display font-bold text-[17px] text-bimo-text mt-10 mb-3 pb-2 border-b border-bimo-navy/10">6. Partage des données</h2>
    <p class="font-body text-sm text-bimo-text/60 leading-[1.8] mb-3">bee ne vend, ne loue et ne partage jamais vos données avec des tiers à des fins commerciales.</p>
    <ul class="space-y-1 mb-4">@foreach(['<strong class="text-bimo-text font-medium">Hébergeur :</strong> uniquement pour le stockage sécurisé','<strong class="text-bimo-text font-medium">Prestataires techniques :</strong> envoi d\'emails transactionnels','<strong class="text-bimo-text font-medium">Autorités légales :</strong> uniquement sur réquisition judiciaire'] as $item)<li class="font-body text-sm text-bimo-text/60 pl-4 relative before:absolute before:left-0 before:text-marketing-gold before:content-['·']">{!! $item !!}</li>@endforeach</ul>

    <h2 class="font-display font-bold text-[17px] text-bimo-text mt-10 mb-3 pb-2 border-b border-bimo-navy/10">8. Vos droits</h2>
    <p class="font-body text-sm text-bimo-text/60 leading-[1.8] mb-3">Conformément à la loi sénégalaise n°2008-12 du 25 janvier 2008, vous disposez des droits suivants :</p>
    <ul class="space-y-1 mb-4">@foreach(['<strong class="text-bimo-text font-medium">Droit d\'accès :</strong> obtenir une copie de vos données','<strong class="text-bimo-text font-medium">Droit de rectification :</strong> corriger des données inexactes','<strong class="text-bimo-text font-medium">Droit à l\'effacement :</strong> demander la suppression','<strong class="text-bimo-text font-medium">Droit à la portabilité :</strong> exporter vos données','<strong class="text-bimo-text font-medium">Droit d\'opposition :</strong> vous opposer à certains traitements'] as $item)<li class="font-body text-sm text-bimo-text/60 pl-4 relative before:absolute before:left-0 before:text-marketing-gold before:content-['·']">{!! $item !!}</li>@endforeach</ul>
    <p class="font-body text-sm text-bimo-text/60 leading-[1.8] mb-3">Pour exercer ces droits : <a href="mailto:contact@bimotech.sn" class="text-marketing-gold no-underline hover:underline">contact@bimotech.sn</a></p>
    <p class="font-body text-sm text-bimo-text/60 leading-[1.8]">Réclamation possible auprès de la <strong class="text-bimo-text font-medium">Commission de Protection des Données Personnelles (CDP)</strong> du Sénégal : <a href="https://www.cdp.sn" target="_blank" class="text-marketing-gold no-underline hover:underline">www.cdp.sn</a></p>

</div>

<footer class="px-[5%] py-8 border-t border-bimo-navy/10 flex flex-col md:flex-row items-center justify-between gap-4 flex-wrap bg-bimo-bg2">
    <div class="font-display font-extrabold text-base text-bimo-text">bee</div>
    <div class="flex flex-col md:flex-row items-center gap-4 md:gap-6">
        @foreach([[url('/'),'Accueil'],[route('contact'),'Contact'],[route('mentions-legales'),'Mentions légales']] as [$href,$lbl])
        <a href="{{ $href }}" class="font-body text-xs text-bimo-text/50 no-underline hover:text-bimo-text transition-colors duration-150">{{ $lbl }}</a>
        @endforeach
    </div>
    <div class="font-body text-xs text-bimo-text/30">© {{ date('Y') }} bee · Dakar, Sénégal</div>
</footer>

</body>
</html>
