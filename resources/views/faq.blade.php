<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="index, follow">
<link rel="canonical" href="https://immo.bimotechsn.com/faq">
<title>FAQ — Renlio</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@300;400;500&display=swap" media="print" onload="this.media='all'">
<noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@300;400;500&display=swap"></noscript>
@vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-body bg-bimo-bg text-bimo-navy">

@include('partials.public-nav', ['active' => 'faq'])

{{-- Hero --}}
<div class="pt-[120px] pb-16 px-[5%] text-center bg-bimo-bg">
    <div class="font-body font-semibold text-[11px] uppercase tracking-[2px] text-bimo-gold mb-4">FAQ</div>
    <h1 class="font-display font-extrabold text-[clamp(28px,5vw,50px)] tracking-tight leading-tight text-bimo-navy mb-4">Les réponses à vos <em class="not-italic text-bimo-gold">questions</em></h1>
    <p class="font-body font-light text-base text-bimo-navy/50 max-w-[440px] mx-auto leading-relaxed">Tout ce que vous voulez savoir avant de démarrer avec Renlio.</p>
</div>

{{-- FAQ --}}
<div class="max-w-[780px] mx-auto px-[5%] pb-24">

    @php
    $faqs = [
        'Démarrage & Tarifs' => [
            ['q' => 'Est-ce vraiment gratuit pour commencer ?', 'a' => 'Oui. Le plan Démarrage est gratuit pour toujours, jusqu\'à 5 biens. Aucune carte bancaire requise. Vous passez au plan Agence uniquement si vous en avez besoin.'],
            ['q' => 'Quels sont les tarifs après l\'essai gratuit ?', 'a' => 'Trois plans : Starter à 19 900 FCFA/mois (jusqu\'à 15 unités), Pro à 39 900 FCFA/mois (jusqu\'à 50 unités), Agence à 69 900 FCFA/mois (illimité). Formule annuelle disponible avec 2 mois offerts. Résiliation à tout moment, sans frais.'],
            ['q' => 'Combien de temps prend la mise en place ?', 'a' => 'Moins de 10 minutes pour créer votre agence. Si vous souhaitez importer un grand nombre de biens existants, notre équipe peut vous accompagner lors d\'une démo sur site à Dakar.'],
        ],
        'Conformité fiscale' => [
            ['q' => 'Renlio est-il vraiment conforme à la TVA sénégalaise à 18% ?', 'a' => 'Oui. Le calcul de TVA à 18% sur les commissions d\'agence est intégré nativement, conformément au CGI article 357. Chaque quittance affiche la décomposition HT / TVA / TTC automatiquement. Aucune configuration nécessaire.'],
            ['q' => 'Qu\'est-ce que la loi 81-18 et comment Renlio la gère ?', 'a' => 'La loi 81-18 encadre les loyers au Sénégal en fixant des plafonds selon la surface du bien. Renlio vérifie automatiquement si le loyer saisi respecte ces plafonds et affiche une alerte si ce n\'est pas le cas.'],
            ['q' => 'Les quittances générées sont-elles légalement valides ?', 'a' => 'Oui. Les quittances PDF générées par Renlio incluent le NINEA de votre agence, le détail TVA, les références du contrat et sont numérotées séquentiellement. Elles sont conformes aux exigences de la Direction des Impôts du Sénégal.'],
        ],
        'Fonctionnement' => [
            ['q' => 'Mes données sont-elles sécurisées et séparées des autres agences ?', 'a' => 'Absolument. Renlio utilise une architecture multi-tenant : les données de chaque agence sont strictement isolées. Un utilisateur d\'une agence ne peut jamais voir les données d\'une autre. Toutes les connexions sont chiffrées (HTTPS).'],
            ['q' => 'Comment mes locataires et propriétaires accèdent-ils à leur espace ?', 'a' => 'Chaque locataire et propriétaire reçoit une invitation par email avec ses identifiants de connexion. Ils accèdent ensuite à leur espace personnel depuis n\'importe quel navigateur ou smartphone. Aucune application à installer.'],
            ['q' => 'Si je veux arrêter, est-ce que je récupère mes données ?', 'a' => 'Oui. Avant toute résiliation, vous pouvez exporter l\'intégralité de vos données aux formats CSV et PDF. Vos données sont conservées 30 jours après résiliation, puis définitivement supprimées.'],
        ],
    ];
    @endphp

    @foreach($faqs as $categorie => $questions)
    <div class="flex items-center gap-3 mt-12 mb-4 font-body font-semibold text-[11px] uppercase tracking-[2px] text-bimo-gold">
        {{ $categorie }}
        <div class="flex-1 h-px bg-bimo-navy/10"></div>
    </div>
    @foreach($questions as $faq)
    <div class="faq-item bg-white border border-bimo-navy/10 rounded-[12px] mb-2 overflow-hidden hover:border-bimo-navy/20 transition-colors duration-200">
        <button class="w-full bg-transparent border-none px-5 py-4 flex items-center justify-between cursor-pointer text-left font-body font-medium text-sm text-bimo-navy gap-3 hover:bg-bimo-bg transition-colors duration-150"
                onclick="toggleFaq(this)">
            {{ $faq['q'] }}
            <div class="faq-icon w-[22px] h-[22px] bg-bimo-gold rounded-[6px] flex items-center justify-center flex-shrink-0 transition-transform duration-250">
                <svg width="10" height="10" viewBox="0 0 10 10" fill="none" stroke="white" stroke-width="2.5"><line x1="5" y1="1" x2="5" y2="9"/><line x1="1" y1="5" x2="9" y2="5"/></svg>
            </div>
        </button>
        <div class="faq-a max-h-0 overflow-hidden transition-all duration-300 ease-in-out px-5">
            <p class="font-body text-sm text-bimo-navy/60 leading-[1.8] pb-4">{{ $faq['a'] }}</p>
        </div>
    </div>
    @endforeach
    @endforeach

    {{-- CTA --}}
    <div class="bg-bimo-navy rounded-[16px] px-10 py-10 text-center mt-12">
        <h2 class="font-display font-extrabold text-xl text-white mb-3">Vous avez une autre question ?</h2>
        <p class="font-body text-sm text-white/50 mb-6 leading-relaxed">Notre équipe vous répond dans la journée, sur WhatsApp ou par email.</p>
        <div class="flex items-center justify-center gap-3 flex-wrap">
            <a href="{{ route('demo') }}" class="font-body font-bold text-sm text-bimo-navy no-underline px-6 py-3 rounded-[10px] bg-bimo-gold hover:opacity-90 transition-opacity duration-150">Réserver une démo →</a>
            <a href="{{ route('contact') }}" class="font-body font-medium text-sm text-white no-underline px-6 py-3 rounded-[10px] border border-white/15 hover:border-white/30 transition-all duration-150">Nous contacter</a>
        </div>
    </div>

</div>

<footer class="px-[5%] py-8 border-t border-bimo-navy/10 flex flex-col md:flex-row items-center justify-between gap-4 flex-wrap bg-bimo-bg2">
    <div class="font-display font-extrabold text-base text-bimo-navy">Renlio</div>
    <div class="flex flex-col md:flex-row items-center gap-4 md:gap-6">
        @foreach([[url('/'),'Accueil'],[route('contact'),'Contact'],[route('mentions-legales'),'Mentions légales']] as [$href,$lbl])
        <a href="{{ $href }}" class="font-body text-xs text-bimo-navy/50 no-underline hover:text-bimo-navy transition-colors duration-150">{{ $lbl }}</a>
        @endforeach
    </div>
    <div class="font-body text-xs text-bimo-navy/30">© {{ date('Y') }} Renlio · Dakar, Sénégal</div>
</footer>

<script>
function toggleFaq(btn) {
    var item = btn.closest('.faq-item');
    var isOpen = item.classList.contains('open');
    document.querySelectorAll('.faq-item.open').forEach(function(el){
        el.classList.remove('open');
        el.querySelector('.faq-a').style.maxHeight = '0';
        el.querySelector('.faq-icon').style.transform = '';
    });
    if (!isOpen) {
        item.classList.add('open');
        var answer = item.querySelector('.faq-a');
        answer.style.maxHeight = answer.scrollHeight + 'px';
        item.querySelector('.faq-icon').style.transform = 'rotate(45deg)';
    }
}
</script>
</body>
</html>
