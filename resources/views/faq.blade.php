<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="index, follow">
<link rel="canonical" href="https://immo.bimotechsn.com/faq">
<title>FAQ — BimoTech Immo</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@300;400;500&display=swap" media="print" onload="this.media='all'">
<noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@300;400;500&display=swap"></noscript>
<style>
*{box-sizing:border-box;margin:0;padding:0}
:root{--gold:#c9a84c;--dark:#0d1117;--dark2:#161b22;--dark3:#21262d;--text:#e6edf3;--muted:#8b949e;--border:rgba(255,255,255,.08)}
body{font-family:'DM Sans',sans-serif;background:var(--dark);color:var(--text)}

nav{position:fixed;top:0;left:0;right:0;z-index:100;padding:0 5%;height:64px;display:flex;align-items:center;justify-content:space-between;background:rgba(13,17,23,.85);backdrop-filter:blur(12px);border-bottom:1px solid var(--border)}
.nav-logo{font-family:'Syne',sans-serif;font-size:18px;font-weight:800;color:var(--gold);text-decoration:none}
.nav-logo span{color:var(--text)}
.nav-back{font-size:13px;color:var(--muted);text-decoration:none;transition:color .2s}
.nav-back:hover{color:var(--text)}

.hero{padding:120px 5% 4rem;text-align:center;position:relative;overflow:hidden}
.hero::before{content:'';position:absolute;top:-20%;left:50%;transform:translateX(-50%);width:600px;height:600px;background:radial-gradient(circle,rgba(201,168,76,.06) 0%,transparent 70%);pointer-events:none}
.section-tag{font-size:11px;color:var(--gold);font-weight:600;letter-spacing:2px;text-transform:uppercase;margin-bottom:1rem}
h1{font-family:'Syne',sans-serif;font-size:clamp(28px,5vw,50px);font-weight:800;letter-spacing:-1.5px;line-height:1.1;margin-bottom:1rem}
h1 em{font-style:normal;color:var(--gold)}
.hero-sub{font-size:15px;color:var(--muted);max-width:440px;margin:0 auto;line-height:1.7;font-weight:300}

.main{max-width:780px;margin:0 auto;padding:0 5% 6rem}

/* Catégories */
.cat-label{font-size:11px;color:var(--gold);font-weight:600;letter-spacing:2px;text-transform:uppercase;margin:3rem 0 1rem;display:flex;align-items:center;gap:10px}
.cat-label::after{content:'';flex:1;height:1px;background:var(--border)}

/* Accordion */
.faq-item{border:1px solid var(--border);border-radius:12px;margin-bottom:8px;overflow:hidden;transition:border-color .2s}
.faq-item:hover{border-color:rgba(201,168,76,.2)}
.faq-item.open{border-color:rgba(201,168,76,.3)}

.faq-q{
    width:100%;background:transparent;border:none;padding:1.1rem 1.25rem;
    display:flex;justify-content:space-between;align-items:center;
    cursor:pointer;text-align:left;
    font-family:'DM Sans',sans-serif;font-size:14px;font-weight:500;
    color:var(--text);gap:12px;
}
.faq-q:hover{background:rgba(255,255,255,.02)}
.faq-icon{
    width:22px;height:22px;background:rgba(201,168,76,.1);border-radius:6px;
    display:flex;align-items:center;justify-content:center;flex-shrink:0;
    transition:transform .25s,background .2s;
}
.faq-item.open .faq-icon{transform:rotate(45deg);background:rgba(201,168,76,.2)}

.faq-a{
    max-height:0;overflow:hidden;
    transition:max-height .3s ease,padding .3s ease;
    padding:0 1.25rem;
}
.faq-item.open .faq-a{max-height:400px;padding:0 1.25rem 1.25rem}
.faq-a p{font-size:13.5px;color:var(--muted);line-height:1.8}
.faq-a a{color:var(--gold);text-decoration:none}
.faq-a a:hover{text-decoration:underline}
.faq-a strong{color:var(--text);font-weight:500}

/* CTA final */
.cta-box{background:var(--dark2);border:1px solid var(--border);border-radius:16px;padding:2.5rem;text-align:center;margin-top:3rem}
.cta-box h2{font-family:'Syne',sans-serif;font-size:20px;font-weight:800;margin-bottom:.75rem}
.cta-box p{font-size:14px;color:var(--muted);margin-bottom:1.5rem;line-height:1.6}
.cta-btns{display:flex;gap:10px;justify-content:center;flex-wrap:wrap}
.btn-gold{background:var(--gold);color:var(--dark);font-family:'DM Sans',sans-serif;font-weight:700;font-size:13.5px;padding:11px 24px;border-radius:10px;text-decoration:none;transition:opacity .2s}
.btn-gold:hover{opacity:.9}
.btn-outline{background:transparent;color:var(--text);font-family:'DM Sans',sans-serif;font-weight:500;font-size:13.5px;padding:11px 24px;border-radius:10px;text-decoration:none;border:1px solid var(--border);transition:all .2s}
.btn-outline:hover{border-color:rgba(255,255,255,.2)}

footer{padding:2rem 5%;border-top:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem}
.footer-logo{font-family:'Syne',sans-serif;font-size:15px;font-weight:800;color:var(--gold)}
.footer-links{display:flex;gap:1.5rem}
.footer-links a{font-size:12px;color:var(--muted);text-decoration:none}
.footer-copy{font-size:12px;color:#484f58}

@media(max-width:768px){footer{flex-direction:column;text-align:center}}
</style>
</head>
<body>

@include('partials.public-nav', ['active' => 'contact'])

<div class="hero">
    <div class="section-tag">FAQ</div>
    <h1>Les réponses à vos <em>questions</em></h1>
    <p class="hero-sub">Tout ce que vous voulez savoir avant de démarrer avec BimoTech.</p>
</div>

<div class="main">

    @php
    $faqs = [
        'Démarrage & Tarifs' => [
            [
                'q' => 'Est-ce vraiment gratuit pour commencer ?',
                'a' => 'Oui. Le plan Démarrage est gratuit pour toujours, jusqu\'à 5 biens. Aucune carte bancaire requise. Vous passez au plan Agence uniquement si vous en avez besoin.'
            ],
            [
                'q' => 'Comment fonctionne l\'abonnement à 25 000 FCFA / mois ?',
                'a' => 'Le plan Agence est facturé mensuellement. Vous pouvez résilier à tout moment sans frais. Aucun engagement annuel obligatoire. Le paiement se fait par virement ou Mobile Money (Wave, Orange Money).'
            ],
            [
                'q' => 'Combien de temps prend la mise en place ?',
                'a' => 'Moins de 10 minutes pour créer votre agence. Si vous souhaitez importer un grand nombre de biens existants, notre équipe peut vous accompagner lors d\'une démo sur site à Dakar.'
            ],
        ],
        'Conformité fiscale' => [
            [
                'q' => 'BimoTech est-il vraiment conforme à la TVA sénégalaise à 18% ?',
                'a' => 'Oui. Le calcul de TVA à 18% sur les commissions d\'agence est intégré nativement, conformément au CGI article 357. Chaque quittance affiche la décomposition HT / TVA / TTC automatiquement. Aucune configuration nécessaire.'
            ],
            [
                'q' => 'Qu\'est-ce que la loi 81-18 et comment BimoTech la gère ?',
                'a' => 'La loi 81-18 encadre les loyers au Sénégal en fixant des plafonds selon la surface du bien. BimoTech vérifie automatiquement si le loyer saisi respecte ces plafonds et affiche une alerte si ce n\'est pas le cas. Le dépôt de garantie est également plafonné automatiquement à 2 mois de loyer hors charges.'
            ],
            [
                'q' => 'Les quittances générées sont-elles légalement valides ?',
                'a' => 'Oui. Les quittances PDF générées par BimoTech incluent le NINEA de votre agence, le détail TVA, les références du contrat et sont numérotées séquentiellement. Elles sont conformes aux exigences de la Direction des Impôts du Sénégal.'
            ],
        ],
        'Fonctionnement' => [
            [
                'q' => 'Mes données sont-elles sécurisées et séparées des autres agences ?',
                'a' => 'Absolument. BimoTech utilise une architecture multi-tenant : les données de chaque agence sont strictement isolées. Un utilisateur d\'une agence ne peut jamais voir les données d\'une autre, même en cas de bug. Toutes les connexions sont chiffrées (HTTPS).'
            ],
            [
                'q' => 'Est-ce que ça fonctionne sans connexion internet ?',
                'a' => 'BimoTech est une application web qui nécessite une connexion internet. Pour les zones à connexion instable, les opérations légères (consultation, saisie simple) fonctionnent avec un réseau 3G. Nous travaillons sur un mode hors-ligne partiel pour une prochaine version.'
            ],
            [
                'q' => 'Comment mes locataires et propriétaires accèdent-ils à leur espace ?',
                'a' => 'Chaque locataire et propriétaire reçoit une invitation par email avec ses identifiants de connexion. Ils accèdent ensuite à leur espace personnel depuis n\'importe quel navigateur ou smartphone. Aucune application à installer.'
            ],
            [
                'q' => 'Si je veux arrêter, est-ce que je récupère mes données ?',
                'a' => 'Oui. Avant toute résiliation, vous pouvez exporter l\'intégralité de vos données (biens, contrats, paiements, quittances) aux formats CSV et PDF. Vos données sont conservées 30 jours après résiliation, puis définitivement supprimées.'
            ],
        ],
    ];
    @endphp

    @foreach($faqs as $categorie => $questions)
        <div class="cat-label">{{ $categorie }}</div>

        @foreach($questions as $i => $faq)
            <div class="faq-item" id="faq-{{ $loop->parent->index }}-{{ $loop->index }}">
                <button class="faq-q" onclick="toggleFaq(this)">
                    {{ $faq['q'] }}
                    <div class="faq-icon">
                        <svg width="10" height="10" viewBox="0 0 10 10" fill="none" stroke="#c9a84c" stroke-width="2"><line x1="5" y1="1" x2="5" y2="9"/><line x1="1" y1="5" x2="9" y2="5"/></svg>
                    </div>
                </button>
                <div class="faq-a"><p>{{ $faq['a'] }}</p></div>
            </div>
        @endforeach
    @endforeach

    <div class="cta-box">
        <h2>Vous avez une autre question ?</h2>
        <p>Notre équipe vous répond dans la journée, sur WhatsApp ou par email.</p>
        <div class="cta-btns">
            <a href="{{ route('demo') }}" class="btn-gold">Réserver une démo →</a>
            <a href="{{ route('contact') }}" class="btn-outline">Nous contacter</a>
        </div>
    </div>

</div>

<footer>
    <div class="footer-logo">BimoTech Immo</div>
    <div class="footer-links">
        <a href="{{ url('/') }}">Accueil</a>
        <a href="{{ route('contact') }}">Contact</a>
        <a href="{{ route('mentions-legales') }}">Mentions légales</a>
    </div>
    <div class="footer-copy">© {{ date('Y') }} BimoTech · Dakar, Sénégal</div>
</footer>

<script>
function toggleFaq(btn) {
    const item = btn.closest('.faq-item');
    const isOpen = item.classList.contains('open');
    document.querySelectorAll('.faq-item.open').forEach(el => el.classList.remove('open'));
    if (!isOpen) item.classList.add('open');
}
</script>

</body>
</html>