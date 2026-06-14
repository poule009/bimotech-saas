<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="Tarifs bee — Starter, Pro, Agence. Essai gratuit 30 jours sans carte bancaire.">
<title>Tarifs — bee</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,wght@0,300;0,400;0,500;0,600;1,300&display=swap" media="print" onload="this.media='all'">
<noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,wght@0,300;0,400;0,500;0,600;1,300&display=swap"></noscript>
@vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-body bg-bimo-bg text-bimo-text antialiased">

@include('partials.public-nav', ['active' => 'pricing'])

<div class="max-w-[1100px] mx-auto px-[5%] pt-20 pb-24">

    {{-- En-tête --}}
    <div class="text-center mb-12">
        <div class="inline-flex items-center gap-2 bg-marketing-gold/10 border border-marketing-gold/20 rounded-full px-4 py-1.5 font-body font-medium text-xs text-marketing-gold mb-5">
            <span class="w-1.5 h-1.5 rounded-full bg-marketing-gold"></span>
            Tarifs transparents
        </div>
        <h1 class="font-display font-extrabold text-[clamp(30px,5vw,52px)] tracking-tight leading-tight text-bimo-text mb-4">Le bon plan pour<br>votre <em class="not-italic text-marketing-gold">agence</em></h1>
        <p class="font-body text-base text-bimo-text/50 max-w-[480px] mx-auto mb-8 leading-relaxed">30 jours d'essai gratuit sur chaque plan. Aucune carte bancaire requise.</p>

        {{-- Toggle --}}
        <div class="inline-flex items-center gap-1 bg-bimo-bg2 border border-bimo-navy/10 rounded-full p-1">
            <button id="btn-mensuel" onclick="setBilling('mensuel')"
                    class="px-5 py-2 rounded-full font-display font-bold text-sm cursor-pointer border-none bg-bimo-navy text-white transition-all duration-200">Mensuel</button>
            <button id="btn-annuel" onclick="setBilling('annuel')"
                    class="px-5 py-2 rounded-full font-body text-sm text-bimo-text/50 cursor-pointer border-none bg-transparent transition-all duration-200 flex items-center gap-2">
                Annuel
                <span class="font-body font-bold text-[11px] bg-bimo-navy/10 text-bimo-text/60 px-2 py-0.5 rounded-full">−17%</span>
            </button>
        </div>
    </div>

    {{-- 3 plans --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 items-start mb-14">

        {{-- Agence --}}
        <div class="bg-white border border-bimo-navy/10 rounded-[14px] p-7 hover:shadow-md hover:border-bimo-navy/20 transition-all duration-200">
            <div class="font-body font-bold text-[13px] uppercase tracking-[1px] text-bimo-text/40 mb-1.5">Agence</div>
            <div class="font-body text-xs text-bimo-text/30 mb-5">Unités illimitées</div>
            <div class="mb-1.5">
                <span class="font-display font-extrabold text-[36px] tracking-tight leading-none text-bimo-text" id="price-agence">69 900</span>
                <span class="font-body text-sm text-bimo-text/40 ml-1">FCFA / mois</span>
            </div>
            <div class="font-body text-xs text-bimo-text/30 mb-5 min-h-[18px]" id="annual-agence">&nbsp;</div>
            <a href="{{ route('agency.register') }}" class="block w-full py-3 rounded-[10px] font-display font-bold text-sm text-center text-white no-underline bg-bimo-navy hover:bg-bimo-navy-dk transition-colors duration-150 mb-1.5">Essai gratuit 30 jours</a>
            <p class="font-body text-[11px] text-center text-bimo-text/30 mb-5">Sans carte bancaire</p>
            <hr class="border-none border-t border-bimo-navy/10 my-5">
            <div class="flex flex-col gap-2.5">
                @foreach(['Tout le plan Pro inclus'] as $f)
                <div class="flex items-start gap-2.5 font-body text-sm text-bimo-text/70 leading-snug">
                    <div class="w-4 h-4 rounded-full bg-marketing-gold/15 flex items-center justify-center flex-shrink-0 mt-0.5"><svg class="w-2.5 h-2.5 text-marketing-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></div>
                    <span>{{ $f }}</span>
                </div>
                @endforeach
                <div class="font-body font-semibold text-[10px] uppercase tracking-[.5px] text-marketing-gold mt-1 mb-0.5">Exclusif Agence</div>
                @foreach(['Déclarations DGID prêtes (BRS, TVA, IRPP)','Bilans fiscaux PDF par propriétaire','Logs d\'activité complets','Support prioritaire'] as $f)
                <div class="flex items-start gap-2.5 font-body text-sm text-bimo-text/70 leading-snug">
                    <div class="w-4 h-4 rounded-full bg-marketing-gold/15 flex items-center justify-center flex-shrink-0 mt-0.5"><svg class="w-2.5 h-2.5 text-marketing-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></div>
                    <span>{{ $f }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Pro (recommandé) --}}
        <div class="bg-bimo-navy border border-bimo-navy rounded-[14px] p-7 pt-11 relative shadow-xl">
            <div class="absolute -top-3.5 left-1/2 -translate-x-1/2 bg-marketing-gold text-bimo-text font-display font-extrabold text-[11px] px-4 py-1 rounded-full whitespace-nowrap">⭐ Recommandé</div>
            <div class="font-body font-bold text-[13px] uppercase tracking-[1px] text-white/40 mb-1.5">Pro</div>
            <div class="font-body text-xs text-white/30 mb-5">Jusqu'à 50 unités</div>
            <div class="mb-1.5">
                <span class="font-display font-extrabold text-[36px] tracking-tight leading-none text-white" id="price-pro">39 900</span>
                <span class="font-body text-sm text-white/40 ml-1">FCFA / mois</span>
            </div>
            <div class="font-body text-xs text-white/35 mb-5 min-h-[18px]" id="annual-pro">&nbsp;</div>
            <a href="{{ route('agency.register') }}" class="block w-full py-3 rounded-[10px] font-display font-bold text-sm text-center text-bimo-text no-underline bg-marketing-gold hover:opacity-90 transition-opacity duration-150 mb-1.5 shadow-lg">Essai gratuit 30 jours</a>
            <p class="font-body text-[11px] text-center text-white/30 mb-5">Sans carte bancaire</p>
            <hr class="border-none border-t border-white/10 my-5">
            <div class="flex flex-col gap-2.5">
                @foreach(['Tout le plan Starter inclus'] as $f)
                <div class="flex items-start gap-2.5 font-body text-sm text-white/70 leading-snug">
                    <div class="w-4 h-4 rounded-full bg-white/10 flex items-center justify-center flex-shrink-0 mt-0.5"><svg class="w-2.5 h-2.5 text-marketing-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></div>
                    <span>{{ $f }}</span>
                </div>
                @endforeach
                <div class="font-body font-semibold text-[10px] uppercase tracking-[.5px] text-marketing-gold mt-1 mb-0.5">Fonctions Pro</div>
                @foreach(['Gestion immeubles (Immeuble → Unités)','Rapport financier mensuel PDF','Relevé PDF par propriétaire','Import Excel · Export CSV','Contrat de bail formel PDF'] as $f)
                <div class="flex items-start gap-2.5 font-body text-sm text-white/70 leading-snug">
                    <div class="w-4 h-4 rounded-full bg-white/10 flex items-center justify-center flex-shrink-0 mt-0.5"><svg class="w-2.5 h-2.5 text-marketing-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></div>
                    <span>{{ $f }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Starter --}}
        <div class="bg-white border border-bimo-navy/10 rounded-[14px] p-7 hover:shadow-md hover:border-bimo-navy/20 transition-all duration-200">
            <div class="font-body font-bold text-[13px] uppercase tracking-[1px] text-bimo-text/40 mb-1.5">Starter</div>
            <div class="font-body text-xs text-bimo-text/30 mb-5">Jusqu'à 15 unités</div>
            <div class="mb-1.5">
                <span class="font-display font-extrabold text-[36px] tracking-tight leading-none text-bimo-text" id="price-starter">19 900</span>
                <span class="font-body text-sm text-bimo-text/40 ml-1">FCFA / mois</span>
            </div>
            <div class="font-body text-xs text-bimo-text/30 mb-5 min-h-[18px]" id="annual-starter">&nbsp;</div>
            <a href="{{ route('agency.register') }}" class="block w-full py-3 rounded-[10px] font-display font-bold text-sm text-center text-white no-underline bg-bimo-navy hover:bg-bimo-navy-dk transition-colors duration-150 mb-1.5">Essai gratuit 30 jours</a>
            <p class="font-body text-[11px] text-center text-bimo-text/30 mb-5">Sans carte bancaire</p>
            <hr class="border-none border-t border-bimo-navy/10 my-5">
            <div class="flex flex-col gap-2.5">
                @foreach(['Dashboard + graphiques 12 mois','Biens, contrats, paiements','Locataires + propriétaires','Suivi impayés + relances','Quittances PDF conformes CGI SN'] as $f)
                <div class="flex items-start gap-2.5 font-body text-sm text-bimo-text/70 leading-snug">
                    <div class="w-4 h-4 rounded-full bg-marketing-gold/15 flex items-center justify-center flex-shrink-0 mt-0.5"><svg class="w-2.5 h-2.5 text-marketing-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></div>
                    <span>{{ $f }}</span>
                </div>
                @endforeach
            </div>
        </div>

    </div>

    {{-- Sur mesure — composez votre offre (bande dédiée, hors grille self-service) --}}
    @php
        $waMesure = preg_replace('/[^0-9]/', '', config('services.bimotech.whatsapp', '221XXXXXXXXX'));
        $waMsgMesure = rawurlencode("Bonjour bee, je gère une agence et je souhaite un plan sur mesure (activer certains modules sans changer de plan). Pouvez-vous me rappeler ?");
    @endphp
    <div class="bg-bimo-navy rounded-[16px] px-6 py-7 md:px-9 md:py-8 mb-14 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-56 h-56 rounded-full opacity-[0.07]"
             style="background: radial-gradient(circle, var(--ac, #C9A84C) 0%, transparent 70%); transform: translate(30%, -35%)"></div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center gap-5 md:gap-8">
            <div class="flex-1">
                <div class="font-body font-bold text-[11px] uppercase tracking-[1.5px] text-marketing-gold mb-2">Sur mesure</div>
                <h3 class="font-display font-extrabold text-xl md:text-2xl text-white tracking-tight leading-tight mb-2">
                    Un module précis sans changer de plan ?
                </h3>
                <p class="font-body text-sm text-white/55 leading-relaxed max-w-xl">
                    Restez sur votre plan et activez seulement les modules dont vous avez besoin
                    (fiscalité, comptabilité, trésorerie, immeubles…). Tarif adapté à votre usage, sans engagement de palier.
                </p>
            </div>
            <div class="flex flex-col gap-2 flex-shrink-0 w-full md:w-auto">
                <a href="https://wa.me/{{ $waMesure }}?text={{ $waMsgMesure }}" target="_blank" rel="noopener"
                   class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-[10px] bg-[#25D366] text-white font-display font-bold text-sm no-underline hover:opacity-90 transition-opacity duration-150 min-h-[48px]">
                    <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.124.558 4.118 1.532 5.847L.057 23.492a.5.5 0 00.614.65l5.82-1.527A11.945 11.945 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22a9.944 9.944 0 01-5.091-1.396l-.361-.216-3.754.984.999-3.648-.237-.374A9.944 9.944 0 012 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/></svg>
                    Discuter sur WhatsApp
                </a>
                <a href="{{ route('contact') }}"
                   class="font-body text-xs text-center text-white/45 hover:text-white transition-colors duration-150">
                    ou nous écrire par email →
                </a>
            </div>
        </div>
    </div>

    {{-- Méthodes de paiement --}}
    <div class="text-center mb-14">
        <p class="font-body font-medium text-sm text-bimo-text/40 mb-4">Paiement sécurisé via PayTech — accepté en</p>
        <div class="flex items-center justify-center gap-3 flex-wrap">
            @foreach(['Wave','Orange Money','Free Money','Carte bancaire'] as $m)
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-[10px] border border-bimo-navy/10 bg-white font-body font-medium text-sm text-bimo-text/70">
                <span class="w-2 h-2 rounded-full bg-marketing-gold inline-block flex-shrink-0"></span>{{ $m }}
            </div>
            @endforeach
        </div>
    </div>

    {{-- Garantie --}}
    <div class="bg-white border border-bimo-navy/10 rounded-[14px] px-8 py-7 flex flex-col md:flex-row items-start gap-5 mb-14">
        <div class="w-11 h-11 rounded-[12px] bg-marketing-gold/10 flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-marketing-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 12 11 14 15 10"/></svg>
        </div>
        <div>
            <h3 class="font-display font-bold text-base text-bimo-text mb-1.5">Essai 30 jours — zéro risque</h3>
            <p class="font-body text-sm text-bimo-text/50 leading-relaxed">Accès complet au plan Pro pendant 30 jours. Aucune carte bancaire, aucun engagement. Vous choisissez votre plan seulement si vous êtes convaincu.</p>
        </div>
    </div>

    {{-- FAQ rapide --}}
    <div class="mb-14">
        <h2 class="font-display font-extrabold text-xl text-center text-bimo-text mb-7 tracking-tight">Questions fréquentes</h2>
        <div class="max-w-[680px] mx-auto">
            @foreach([
                ['Qu\'est-ce qu\'une "unité" ?', 'Une unité est un bien immobilier géré dans la plateforme. Les biens archivés ne sont pas comptés. Un immeuble de 10 appartements compte pour 10 unités.'],
                ['Peut-on changer de plan en cours d\'abonnement ?', 'Oui. Vous pouvez passer à un plan supérieur à tout moment depuis la page abonnement. Vos données sont toujours accessibles.'],
                ['Le tarif annuel "10+2" — comment ça marche ?', 'Vous réglez l\'équivalent de 10 mois et bénéficiez de 12 mois d\'accès. Les 2 mois offerts sont déduits directement du montant annuel.'],
                ['Les déclarations DGID sont-elles prêtes à envoyer ?', 'En plan Agence, la plateforme calcule automatiquement la BRS, la TVA sur commissions, l\'IRPP et génère des bilans fiscaux PDF conformes CGI SN.'],
            ] as [$q, $a])
            <div class="faq-item border-b border-bimo-navy/10 first:border-t first:border-bimo-navy/10">
                <div class="flex items-center justify-between py-4 cursor-pointer font-body font-semibold text-sm text-bimo-text gap-3 hover:text-marketing-gold transition-colors duration-150" onclick="toggleFaq(this)">
                    {{ $q }}
                    <svg class="faq-arrow w-[18px] h-[18px] flex-shrink-0 text-bimo-text/30 transition-transform duration-200" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                </div>
                <div class="faq-a font-body text-sm text-bimo-text/50 leading-relaxed pb-4 hidden">{{ $a }}</div>
            </div>
            @endforeach
        </div>
    </div>

</div>

<footer class="border-t border-bimo-navy/10 px-[5%] py-8 bg-bimo-bg2">
    <div class="max-w-[1040px] mx-auto flex flex-col md:flex-row items-center justify-between gap-4 flex-wrap">
        <span class="font-body text-xs text-bimo-text/30">© {{ date('Y') }} bee. Tous droits réservés.</span>
        <div class="flex flex-wrap items-center gap-5 justify-center">
            @foreach([[route('home'),'Accueil'],[route('demo'),'Démo'],[route('faq'),'FAQ'],[route('contact'),'Contact'],[route('mentions-legales'),'Mentions légales'],[route('confidentialite'),'Confidentialité']] as [$href,$lbl])
            <a href="{{ $href }}" class="font-body text-xs text-bimo-text/40 no-underline hover:text-bimo-text transition-colors duration-150">{{ $lbl }}</a>
            @endforeach
        </div>
    </div>
</footer>

<script>
var prices = {
    mensuel: { agence: '69 900', pro: '39 900', starter: '19 900' },
    annuel:  { agence: '699 000', pro: '399 000', starter: '199 000' }
};
var savings = { agence: '139 800', pro: '79 800', starter: '39 800' };
function setBilling(mode) {
    var isMensuel = mode === 'mensuel';
    document.getElementById('btn-mensuel').className = isMensuel
        ? 'px-5 py-2 rounded-full font-display font-bold text-sm cursor-pointer border-none bg-bimo-navy text-white transition-all duration-200'
        : 'px-5 py-2 rounded-full font-body text-sm text-bimo-text/50 cursor-pointer border-none bg-transparent transition-all duration-200';
    document.getElementById('btn-annuel').className = !isMensuel
        ? 'px-5 py-2 rounded-full font-display font-bold text-sm cursor-pointer border-none bg-bimo-navy text-white transition-all duration-200 flex items-center gap-2'
        : 'px-5 py-2 rounded-full font-body text-sm text-bimo-text/50 cursor-pointer border-none bg-transparent transition-all duration-200 flex items-center gap-2';
    ['agence','pro','starter'].forEach(function(plan){
        document.getElementById('price-' + plan).textContent = prices[mode][plan];
        var d = document.getElementById('annual-' + plan);
        d.innerHTML = mode === 'annuel' ? '10 mois + 2 offerts — <strong>économie ' + savings[plan] + ' F</strong>' : '&nbsp;';
    });
}
function toggleFaq(el) {
    el.parentElement.classList.toggle('open');
    var a = el.parentElement.querySelector('.faq-a');
    if (el.parentElement.classList.contains('open')) {
        a.classList.remove('hidden');
        el.querySelector('.faq-arrow').style.transform = 'rotate(180deg)';
    } else {
        a.classList.add('hidden');
        el.querySelector('.faq-arrow').style.transform = '';
    }
}
</script>
</body>
</html>
