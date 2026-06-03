<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Confidentialité — BimoTech Immo</title>
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
    <h1 class="font-display font-extrabold text-[clamp(26px,4vw,40px)] tracking-tight mb-2">Politique de confidentialité</h1>
    <p class="font-body text-[13px] text-[#484f58] mb-12">Dernière mise à jour : {{ date('d/m/Y') }}</p>

    <div class="bg-bimo-gold/[5%] border border-bimo-gold/15 rounded-[12px] px-6 py-5 mb-8">
        <p class="font-body text-sm text-bimo-gold/80 leading-relaxed">BimoTech Immo s'engage à protéger vos données personnelles. Nous ne vendons jamais vos données à des tiers et nous ne faisons aucune publicité ciblée.</p>
    </div>

    @foreach([
        ['1. Qui sommes-nous ?', null, [
            'BimoTech Immo est une plateforme SaaS de gestion immobilière destinée aux agences immobilières sénégalaises, éditée par BimoTech, dont le siège est à Dakar, Sénégal.',
            'Pour toute question relative à vos données : <a href="mailto:contact@bimotech.sn" class="text-bimo-gold no-underline hover:underline">contact@bimotech.sn</a>',
        ]],
    ] as [$h, $intro, $items])
    <h2 class="font-display font-bold text-[17px] text-[#e6edf3] mt-10 mb-3 pb-2 border-b border-[rgba(255,255,255,.08)]">{{ $h }}</h2>
    @foreach($items as $item)<p class="font-body text-sm text-[#8b949e] leading-[1.8] mb-3">{!! $item !!}</p>@endforeach
    @endforeach

    <h2 class="font-display font-bold text-[17px] text-[#e6edf3] mt-10 mb-3 pb-2 border-b border-[rgba(255,255,255,.08)]">2. Données collectées</h2>
    <h3 class="font-body font-semibold text-sm text-[#e6edf3] mt-5 mb-2">Lors de la création de compte</h3>
    <ul class="space-y-1 mb-4">
        @foreach(['Nom et prénom de l\'administrateur','Adresse email professionnelle','Mot de passe (chiffré, jamais stocké en clair)','Nom et NINEA de l\'agence','Numéro de téléphone de l\'agence'] as $item)
        <li class="font-body text-sm text-[#8b949e] leading-relaxed pl-4 relative before:content-['·'] before:absolute before:left-0 before:text-bimo-gold">{{ $item }}</li>
        @endforeach
    </ul>
    <h3 class="font-body font-semibold text-sm text-[#e6edf3] mt-5 mb-2">Dans le cadre de l'utilisation du service</h3>
    <ul class="space-y-1 mb-4">
        @foreach(['Informations sur les biens immobiliers gérés','Données des propriétaires et locataires (noms, contacts, pièces d\'identité)','Contrats de bail et documents associés','Historique des paiements de loyer','Logs d\'activité (actions réalisées, dates, adresses IP)'] as $item)
        <li class="font-body text-sm text-[#8b949e] leading-relaxed pl-4 relative before:content-[\'·\'] before:absolute before:left-0 before:text-bimo-gold">{{ $item }}</li>
        @endforeach
    </ul>
    <h3 class="font-body font-semibold text-sm text-[#e6edf3] mt-5 mb-2">Données techniques</h3>
    <ul class="space-y-1 mb-4">
        @foreach(['Adresse IP lors de la connexion','Navigateur et système d\'exploitation','Cookies de session (strictement nécessaires)'] as $item)
        <li class="font-body text-sm text-[#8b949e] leading-relaxed pl-4 relative before:content-[\'·\'] before:absolute before:left-0 before:text-bimo-gold">{{ $item }}</li>
        @endforeach
    </ul>

    <h2 class="font-display font-bold text-[17px] text-[#e6edf3] mt-10 mb-3 pb-2 border-b border-[rgba(255,255,255,.08)]">3. Finalités du traitement</h2>
    <p class="font-body text-sm text-[#8b949e] leading-[1.8] mb-3">Vos données sont utilisées exclusivement pour :</p>
    <ul class="space-y-1 mb-4">
        @foreach(['Fournir et améliorer le service BimoTech Immo','Gérer votre compte et authentifier vos connexions','Générer les quittances de loyer et documents légaux','Assurer la sécurité de la plateforme (logs d\'audit)','Vous contacter en cas de problème technique ou de facturation'] as $item)
        <li class="font-body text-sm text-[#8b949e] leading-relaxed pl-4 relative before:content-[\'·\'] before:absolute before:left-0 before:text-bimo-gold">{{ $item }}</li>
        @endforeach
    </ul>

    <h2 class="font-display font-bold text-[17px] text-[#e6edf3] mt-10 mb-3 pb-2 border-b border-[rgba(255,255,255,.08)]">4. Base légale du traitement</h2>
    <p class="font-body text-sm text-[#8b949e] leading-[1.8] mb-3">Le traitement de vos données est fondé sur :</p>
    <ul class="space-y-1 mb-4">
        @foreach(['<strong class="text-[#e6edf3] font-medium">L\'exécution du contrat :</strong> pour la fourniture du service souscrit','<strong class="text-[#e6edf3] font-medium">L\'obligation légale :</strong> conservation des données fiscales et comptables','<strong class="text-[#e6edf3] font-medium">L\'intérêt légitime :</strong> sécurité de la plateforme et prévention des fraudes'] as $item)
        <li class="font-body text-sm text-[#8b949e] leading-relaxed pl-4 relative before:content-[\'·\'] before:absolute before:left-0 before:text-bimo-gold">{!! $item !!}</li>
        @endforeach
    </ul>

    <h2 class="font-display font-bold text-[17px] text-[#e6edf3] mt-10 mb-3 pb-2 border-b border-[rgba(255,255,255,.08)]">5. Durée de conservation</h2>
    <ul class="space-y-1 mb-4">
        @foreach(['<strong class="text-[#e6edf3] font-medium">Données de compte actif :</strong> pendant toute la durée de l\'abonnement','<strong class="text-[#e6edf3] font-medium">Données après résiliation :</strong> 30 jours (export possible avant suppression)','<strong class="text-[#e6edf3] font-medium">Quittances et documents fiscaux :</strong> 10 ans (obligation légale)','<strong class="text-[#e6edf3] font-medium">Logs de sécurité :</strong> 12 mois glissants'] as $item)
        <li class="font-body text-sm text-[#8b949e] leading-relaxed pl-4 relative before:content-[\'·\'] before:absolute before:left-0 before:text-bimo-gold">{!! $item !!}</li>
        @endforeach
    </ul>

    <h2 class="font-display font-bold text-[17px] text-[#e6edf3] mt-10 mb-3 pb-2 border-b border-[rgba(255,255,255,.08)]">6. Partage des données</h2>
    <p class="font-body text-sm text-[#8b949e] leading-[1.8] mb-3">BimoTech ne vend, ne loue et ne partage jamais vos données avec des tiers à des fins commerciales.</p>
    <p class="font-body text-sm text-[#8b949e] leading-[1.8] mb-3">Vos données peuvent être partagées avec :</p>
    <ul class="space-y-1 mb-4">
        @foreach(['<strong class="text-[#e6edf3] font-medium">Hébergeur :</strong> uniquement pour le stockage sécurisé des données','<strong class="text-[#e6edf3] font-medium">Prestataires techniques :</strong> envoi d\'emails transactionnels','<strong class="text-[#e6edf3] font-medium">Autorités légales :</strong> uniquement sur réquisition judiciaire'] as $item)
        <li class="font-body text-sm text-[#8b949e] leading-relaxed pl-4 relative before:content-[\'·\'] before:absolute before:left-0 before:text-bimo-gold">{!! $item !!}</li>
        @endforeach
    </ul>

    <h2 class="font-display font-bold text-[17px] text-[#e6edf3] mt-10 mb-3 pb-2 border-b border-[rgba(255,255,255,.08)]">7. Sécurité</h2>
    <p class="font-body text-sm text-[#8b949e] leading-[1.8] mb-3">BimoTech met en œuvre les mesures techniques et organisationnelles suivantes :</p>
    <ul class="space-y-1 mb-4">
        @foreach(['Chiffrement des mots de passe (bcrypt)','Protection CSRF sur tous les formulaires','Isolation des données par agence (multi-tenancy)','Connexions HTTPS obligatoires','Logs d\'audit de toutes les actions sensibles','Accès aux données de production restreint'] as $item)
        <li class="font-body text-sm text-[#8b949e] leading-relaxed pl-4 relative before:content-[\'·\'] before:absolute before:left-0 before:text-bimo-gold">{{ $item }}</li>
        @endforeach
    </ul>

    <h2 class="font-display font-bold text-[17px] text-[#e6edf3] mt-10 mb-3 pb-2 border-b border-[rgba(255,255,255,.08)]">8. Vos droits</h2>
    <p class="font-body text-sm text-[#8b949e] leading-[1.8] mb-3">Conformément à la loi sénégalaise n°2008-12 du 25 janvier 2008, vous disposez des droits suivants :</p>
    <ul class="space-y-1 mb-4">
        @foreach(['<strong class="text-[#e6edf3] font-medium">Droit d\'accès :</strong> obtenir une copie de vos données','<strong class="text-[#e6edf3] font-medium">Droit de rectification :</strong> corriger des données inexactes','<strong class="text-[#e6edf3] font-medium">Droit à l\'effacement :</strong> demander la suppression de vos données','<strong class="text-[#e6edf3] font-medium">Droit à la portabilité :</strong> exporter vos données dans un format lisible','<strong class="text-[#e6edf3] font-medium">Droit d\'opposition :</strong> vous opposer à certains traitements'] as $item)
        <li class="font-body text-sm text-[#8b949e] leading-relaxed pl-4 relative before:content-[\'·\'] before:absolute before:left-0 before:text-bimo-gold">{!! $item !!}</li>
        @endforeach
    </ul>
    <p class="font-body text-sm text-[#8b949e] leading-[1.8] mb-3">Pour exercer ces droits : <a href="mailto:contact@bimotech.sn" class="text-bimo-gold no-underline hover:underline">contact@bimotech.sn</a></p>
    <p class="font-body text-sm text-[#8b949e] leading-[1.8]">Vous pouvez également adresser une réclamation à la <strong class="text-[#e6edf3] font-medium">Commission de Protection des Données Personnelles (CDP)</strong> du Sénégal : <a href="https://www.cdp.sn" target="_blank" class="text-bimo-gold no-underline hover:underline">www.cdp.sn</a></p>

    <h2 class="font-display font-bold text-[17px] text-[#e6edf3] mt-10 mb-3 pb-2 border-b border-[rgba(255,255,255,.08)]">9. Cookies</h2>
    <p class="font-body text-sm text-[#8b949e] leading-[1.8] mb-3">BimoTech utilise uniquement des cookies strictement nécessaires au fonctionnement de la plateforme :</p>
    <ul class="space-y-1 mb-4">
        @foreach(['<strong class="text-[#e6edf3] font-medium">Cookie de session :</strong> maintient votre connexion active','<strong class="text-[#e6edf3] font-medium">Cookie CSRF :</strong> protège contre les attaques de falsification de requête'] as $item)
        <li class="font-body text-sm text-[#8b949e] leading-relaxed pl-4 relative before:content-[\'·\'] before:absolute before:left-0 before:text-bimo-gold">{!! $item !!}</li>
        @endforeach
    </ul>
    <p class="font-body text-sm text-[#8b949e] leading-[1.8]">Aucun cookie publicitaire, analytique tiers ou de tracking n'est utilisé.</p>

    <h2 class="font-display font-bold text-[17px] text-[#e6edf3] mt-10 mb-3 pb-2 border-b border-[rgba(255,255,255,.08)]">10. Modifications</h2>
    <p class="font-body text-sm text-[#8b949e] leading-[1.8]">BimoTech se réserve le droit de modifier la présente politique à tout moment. En cas de modification substantielle, vous serez notifié par email au moins 15 jours avant l'entrée en vigueur des changements.</p>

    <h2 class="font-display font-bold text-[17px] text-[#e6edf3] mt-10 mb-3 pb-2 border-b border-[rgba(255,255,255,.08)]">11. Contact</h2>
    <p class="font-body text-sm text-[#8b949e] leading-[1.8]">Pour toute question relative à la protection de vos données personnelles :<br>
    <strong class="text-[#e6edf3] font-medium">Email :</strong> <a href="mailto:contact@bimotech.sn" class="text-bimo-gold no-underline hover:underline">contact@bimotech.sn</a><br>
    <strong class="text-[#e6edf3] font-medium">Adresse :</strong> BimoTech Immo, Dakar, Sénégal</p>

</div>

<footer class="px-[5%] py-8 border-t border-[rgba(255,255,255,.08)] mt-8 flex flex-col md:flex-row items-center justify-between gap-4 flex-wrap">
    <div class="font-display font-extrabold text-[15px] text-bimo-gold">BimoTech Immo</div>
    <div class="flex flex-col md:flex-row items-center gap-4 md:gap-6">
        @foreach([[url('/'),'Accueil'],[route('contact'),'Contact'],[route('mentions-legales'),'Mentions légales']] as [$href,$lbl])
        <a href="{{ $href }}" class="font-body text-xs text-[#8b949e] no-underline hover:text-[#e6edf3] transition-colors duration-200">{{ $lbl }}</a>
        @endforeach
    </div>
    <div class="font-body text-xs text-[#484f58]">© {{ date('Y') }} BimoTech · Dakar, Sénégal</div>
</footer>

</body>
</html>
