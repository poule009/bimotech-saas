<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="Tarifs BimoTech Immo — Starter, Pro, Agence. Essai gratuit 30 jours sans carte bancaire. Paiement Wave, Orange Money, carte bancaire.">
<title>Tarifs — BimoTech Immo</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,wght@0,300;0,400;0,500;0,600;1,300&display=swap" media="print" onload="this.media='all'">
<noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,wght@0,300;0,400;0,500;0,600;1,300&display=swap"></noscript>
<style>
*{box-sizing:border-box;margin:0;padding:0}
:root{
    --red:#e8001d;
    --bg:#f9fafb;--surface:#fff;
    --text:#111111;--text2:#374151;--muted:#6b7280;--muted2:#9ca3af;
    --border:#e5e7eb;--border-md:#d1d5db;
    --radius:14px;--radius-sm:10px;
}
html{scroll-behavior:smooth}
body{font-family:'DM Sans',sans-serif;background:var(--bg);color:var(--text);-webkit-font-smoothing:antialiased}

/* ── PAGE ── */
.pricing-wrap{max-width:1100px;margin:0 auto;padding:80px 5% 100px}

/* ── EN-TÊTE ── */
.pricing-head{text-align:center;margin-bottom:48px}
.pricing-tag{display:inline-flex;align-items:center;gap:7px;background:#fff0f0;border:1px solid #fecaca;border-radius:99px;padding:5px 16px;font-size:12px;color:var(--red);font-weight:500;margin-bottom:20px}
.pricing-tag-dot{width:6px;height:6px;background:var(--red);border-radius:50%}
.pricing-head h1{font-family:'Syne',sans-serif;font-size:clamp(30px,5vw,52px);font-weight:800;letter-spacing:-1.5px;line-height:1.1;margin-bottom:14px}
.pricing-head h1 em{font-style:normal;color:var(--red)}
.pricing-sub{font-size:16px;color:var(--muted);max-width:480px;margin:0 auto 32px;line-height:1.7}

/* ── TOGGLE MENSUEL / ANNUEL ── */
.billing-toggle{display:inline-flex;align-items:center;gap:4px;background:#fff;border:1px solid var(--border);border-radius:99px;padding:4px;margin-bottom:48px}
.billing-btn{padding:8px 22px;border-radius:99px;font-size:13px;font-weight:500;cursor:pointer;border:none;background:none;color:var(--muted);transition:all .2s;font-family:'DM Sans',sans-serif}
.billing-btn.active{background:#111111;color:#fff;font-weight:600}
.annual-badge{display:inline-flex;align-items:center;background:#dcfce7;color:#16a34a;font-size:11px;font-weight:700;padding:2px 8px;border-radius:99px;margin-left:6px}

/* ── GRILLE PLANS ── */
.plans-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:20px;align-items:start;margin-bottom:56px}

/* ── CARTE PLAN (standard) ── */
.plan-card{background:#fff;border:1px solid var(--border);border-radius:var(--radius);padding:28px 24px;transition:box-shadow .2s}
.plan-card:hover{box-shadow:0 10px 32px rgba(0,0,0,.08)}

/* ── CARTE PRO (mise en avant) ── */
.plan-card.featured{background:#111111;border-color:#111111;box-shadow:0 16px 48px rgba(0,0,0,.18);position:relative;padding-top:44px}

/* ── BADGE RECOMMANDÉ ── */
.badge-recommended{position:absolute;top:-14px;left:50%;transform:translateX(-50%);background:var(--red);color:#fff;font-size:11px;font-weight:700;padding:4px 16px;border-radius:99px;white-space:nowrap;letter-spacing:.3px}

/* ── EN-TÊTE CARTE ── */
.plan-name{font-family:'Syne',sans-serif;font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:1px;margin-bottom:6px}
.plan-card .plan-name{color:var(--muted)}
.plan-card.featured .plan-name{color:rgba(255,255,255,.5)}
.plan-limit{font-size:12px;margin-bottom:20px}
.plan-card .plan-limit{color:var(--muted2)}
.plan-card.featured .plan-limit{color:rgba(255,255,255,.4)}
.plan-price-wrap{margin-bottom:6px}
.plan-price{font-family:'Syne',sans-serif;font-size:36px;font-weight:800;letter-spacing:-1px;line-height:1}
.plan-card .plan-price{color:#111111}
.plan-card.featured .plan-price{color:#ffffff}
.plan-price-unit{font-size:13px;font-weight:400;margin-left:2px}
.plan-card .plan-price-unit{color:var(--muted)}
.plan-card.featured .plan-price-unit{color:rgba(255,255,255,.45)}
.plan-annual-detail{font-size:12px;margin-bottom:20px;min-height:18px}
.plan-card .plan-annual-detail{color:var(--muted2)}
.plan-card.featured .plan-annual-detail{color:rgba(255,255,255,.45)}
.plan-saving{color:#16a34a;font-weight:600}
.plan-card.featured .plan-saving{color:#4ade80}

/* ── BOUTON CTA ── */
.plan-cta{display:block;width:100%;padding:12px;border-radius:var(--radius-sm);font-size:13px;font-weight:700;text-align:center;text-decoration:none;border:none;cursor:pointer;font-family:'DM Sans',sans-serif;transition:all .2s;margin-bottom:6px}
.plan-cta.dark{background:#111111;color:#fff}
.plan-cta.dark:hover{opacity:.85;transform:translateY(-1px)}
.plan-cta.red{background:var(--red);color:#fff;box-shadow:0 4px 16px rgba(232,0,29,.25)}
.plan-cta.red:hover{opacity:.9;transform:translateY(-1px);box-shadow:0 6px 20px rgba(232,0,29,.3)}
.plan-cta-note{font-size:11px;text-align:center;margin-bottom:22px}
.plan-card .plan-cta-note{color:var(--muted2)}
.plan-card.featured .plan-cta-note{color:rgba(255,255,255,.35)}

/* ── SÉPARATEUR ── */
.plan-divider{border:none;border-top:1px solid;margin:20px 0}
.plan-card .plan-divider{border-color:var(--border)}
.plan-card.featured .plan-divider{border-color:rgba(255,255,255,.1)}

/* ── FEATURES ── */
.plan-features{display:flex;flex-direction:column;gap:10px}
.plan-feature{display:flex;align-items:flex-start;gap:10px;font-size:13px;line-height:1.4}
.plan-card .plan-feature{color:var(--text2)}
.plan-card.featured .plan-feature{color:rgba(255,255,255,.75)}
.feat-check{width:16px;height:16px;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:1px}
.plan-card .feat-check{background:#dcfce7}
.plan-card.featured .feat-check{background:rgba(74,222,128,.15)}
.feat-check svg{width:9px;height:9px}
.plan-card .feat-check svg{color:#16a34a}
.plan-card.featured .feat-check svg{color:#4ade80}
.feat-extra{font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;display:inline-block;margin-top:6px;margin-bottom:2px}
.plan-card .feat-extra{color:var(--red)}
.plan-card.featured .feat-extra{color:#f87171}

/* ── SECTION PAIEMENTS ── */
.payment-section{text-align:center;margin-bottom:56px}
.payment-title{font-size:13px;color:var(--muted);margin-bottom:16px;font-weight:500}
.payment-methods{display:flex;align-items:center;justify-content:center;gap:12px;flex-wrap:wrap}
.pay-badge{display:inline-flex;align-items:center;gap:7px;padding:8px 16px;border-radius:10px;border:1px solid var(--border);background:#fff;font-size:13px;font-weight:600}
.pay-dot{width:10px;height:10px;border-radius:50%;flex-shrink:0}

/* ── GARANTIE ── */
.guarantee-section{background:#fff;border:1px solid var(--border);border-radius:var(--radius);padding:28px 32px;display:flex;align-items:flex-start;gap:20px;margin-bottom:56px}
.guarantee-icon{width:44px;height:44px;border-radius:12px;background:#f0fdf4;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.guarantee-icon svg{width:22px;height:22px;color:#16a34a}
.guarantee-body h3{font-family:'Syne',sans-serif;font-size:15px;font-weight:700;color:#111;margin-bottom:6px}
.guarantee-body p{font-size:13px;color:var(--muted);line-height:1.6}

/* ── FAQ ── */
.faq-section{margin-bottom:56px}
.faq-title{font-family:'Syne',sans-serif;font-size:20px;font-weight:700;text-align:center;margin-bottom:28px;letter-spacing:-.4px}
.faq-list{max-width:680px;margin:0 auto;display:flex;flex-direction:column;gap:0}
.faq-item{border-bottom:1px solid var(--border)}
.faq-item:first-child{border-top:1px solid var(--border)}
.faq-q{display:flex;justify-content:space-between;align-items:center;padding:16px 0;cursor:pointer;font-size:14px;font-weight:600;color:#111;gap:12px}
.faq-q:hover{color:var(--red)}
.faq-arrow{width:18px;height:18px;flex-shrink:0;transition:transform .2s;color:var(--muted)}
.faq-a{font-size:13px;color:var(--muted);line-height:1.7;padding-bottom:16px;display:none}
.faq-item.open .faq-a{display:block}
.faq-item.open .faq-arrow{transform:rotate(180deg)}

/* ── FOOTER ── */
footer{background:#1a1a1a;border-top:1px solid var(--border);padding:3rem 5% 2rem}
.footer-inner{max-width:1040px;margin:0 auto;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem}
.footer-copy{font-size:12px;color:#6b7280}
.footer-links{display:flex;gap:1.5rem;flex-wrap:wrap}
.footer-links a{font-size:12px;color:#6b7280;text-decoration:none;transition:color .15s}
.footer-links a:hover{color:#9ca3af}

/* ── RESPONSIVE ── */
@media(max-width:900px){
    .plans-grid{grid-template-columns:1fr;max-width:420px;margin-left:auto;margin-right:auto}
    .plan-card.featured{padding-top:44px}
    .guarantee-section{flex-direction:column}
}
@media(max-width:640px){
    .pricing-wrap{padding:60px 4% 80px}
    .billing-toggle{width:100%}
    .billing-btn{flex:1;text-align:center;font-size:12px}
    .footer-inner{flex-direction:column;text-align:center}
    .footer-links{justify-content:center}
}
</style>
</head>
<body>

@include('partials.public-nav', ['active' => 'pricing'])

<div class="pricing-wrap">

    {{-- EN-TÊTE --}}
    <div class="pricing-head">
        <div class="pricing-tag"><div class="pricing-tag-dot"></div>Tarifs transparents</div>
        <h1>Le bon plan pour<br>votre <em>agence</em></h1>
        <p class="pricing-sub">30 jours d'essai gratuit sur chaque plan. Aucune carte bancaire requise.</p>

        {{-- TOGGLE MENSUEL / ANNUEL --}}
        <div>
            <div class="billing-toggle">
                <button class="billing-btn active" id="btn-mensuel" onclick="setBilling('mensuel')">Mensuel</button>
                <button class="billing-btn" id="btn-annuel" onclick="setBilling('annuel')">
                    Annuel <span class="annual-badge">−17%</span>
                </button>
            </div>
        </div>
    </div>

    {{-- GRILLE 3 PLANS : Agence | Pro★ | Starter --}}
    <div class="plans-grid">

        {{-- ── AGENCE ── --}}
        <div class="plan-card">
            <div class="plan-name">Agence</div>
            <div class="plan-limit">Unités illimitées</div>
            <div class="plan-price-wrap">
                <span class="plan-price" id="price-agence">69 900</span><span class="plan-price-unit">FCFA / mois</span>
            </div>
            <div class="plan-annual-detail" id="annual-agence">&nbsp;</div>
            <a href="{{ route('agency.register') }}" class="plan-cta dark">Essai gratuit 30 jours</a>
            <p class="plan-cta-note">Sans carte bancaire</p>
            <hr class="plan-divider">
            <div class="plan-features">
                <div class="plan-feature">
                    <div class="feat-check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></div>
                    <span>Tout le plan Pro inclus</span>
                </div>
                <div class="feat-extra">Exclusif Agence</div>
                <div class="plan-feature">
                    <div class="feat-check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></div>
                    <span>Déclarations DGID prêtes (BRS, TVA, IRPP)</span>
                </div>
                <div class="plan-feature">
                    <div class="feat-check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></div>
                    <span>Bilans fiscaux PDF par propriétaire</span>
                </div>
                <div class="plan-feature">
                    <div class="feat-check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></div>
                    <span>Logs d'activité complets</span>
                </div>
                <div class="plan-feature">
                    <div class="feat-check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></div>
                    <span>Support prioritaire</span>
                </div>
            </div>
        </div>

        {{-- ── PRO (RECOMMANDÉ) ── --}}
        <div class="plan-card featured">
            <div class="badge-recommended">⭐ Recommandé</div>
            <div class="plan-name">Pro</div>
            <div class="plan-limit">Jusqu'à 50 unités</div>
            <div class="plan-price-wrap">
                <span class="plan-price" id="price-pro">39 900</span><span class="plan-price-unit">FCFA / mois</span>
            </div>
            <div class="plan-annual-detail" id="annual-pro">&nbsp;</div>
            <a href="{{ route('agency.register') }}" class="plan-cta red">Essai gratuit 30 jours</a>
            <p class="plan-cta-note">Sans carte bancaire</p>
            <hr class="plan-divider">
            <div class="plan-features">
                <div class="plan-feature">
                    <div class="feat-check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></div>
                    <span>Tout le plan Starter inclus</span>
                </div>
                <div class="feat-extra">Fonctions Pro</div>
                <div class="plan-feature">
                    <div class="feat-check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></div>
                    <span>Gestion immeubles (Immeuble → Unités)</span>
                </div>
                <div class="plan-feature">
                    <div class="feat-check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></div>
                    <span>Rapport financier mensuel PDF</span>
                </div>
                <div class="plan-feature">
                    <div class="feat-check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></div>
                    <span>Relevé PDF par propriétaire</span>
                </div>
                <div class="plan-feature">
                    <div class="feat-check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></div>
                    <span>Import Excel · Export CSV</span>
                </div>
                <div class="plan-feature">
                    <div class="feat-check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></div>
                    <span>Contrat de bail formel PDF</span>
                </div>
            </div>
        </div>

        {{-- ── STARTER ── --}}
        <div class="plan-card">
            <div class="plan-name">Starter</div>
            <div class="plan-limit">Jusqu'à 15 unités</div>
            <div class="plan-price-wrap">
                <span class="plan-price" id="price-starter">19 900</span><span class="plan-price-unit">FCFA / mois</span>
            </div>
            <div class="plan-annual-detail" id="annual-starter">&nbsp;</div>
            <a href="{{ route('agency.register') }}" class="plan-cta dark">Essai gratuit 30 jours</a>
            <p class="plan-cta-note">Sans carte bancaire</p>
            <hr class="plan-divider">
            <div class="plan-features">
                <div class="plan-feature">
                    <div class="feat-check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></div>
                    <span>Dashboard + graphiques 12 mois</span>
                </div>
                <div class="plan-feature">
                    <div class="feat-check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></div>
                    <span>Biens, contrats, paiements</span>
                </div>
                <div class="plan-feature">
                    <div class="feat-check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></div>
                    <span>Locataires + propriétaires</span>
                </div>
                <div class="plan-feature">
                    <div class="feat-check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></div>
                    <span>Suivi impayés + relances</span>
                </div>
                <div class="plan-feature">
                    <div class="feat-check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></div>
                    <span>Quittances PDF conformes CGI SN</span>
                </div>
            </div>
        </div>

    </div>{{-- /plans-grid --}}

    {{-- MÉTHODES DE PAIEMENT --}}
    <div class="payment-section">
        <p class="payment-title">Paiement sécurisé via PayTech — accepté en</p>
        <div class="payment-methods">
            <div class="pay-badge">
                <div class="pay-dot" style="background:#0066FF"></div>
                <span style="color:#0066FF">Wave</span>
            </div>
            <div class="pay-badge">
                <div class="pay-dot" style="background:#FF6600"></div>
                <span style="color:#FF6600">Orange Money</span>
            </div>
            <div class="pay-badge">
                <div class="pay-dot" style="background:#6b7280"></div>
                <span>Free Money</span>
            </div>
            <div class="pay-badge">
                <div class="pay-dot" style="background:#1a56db"></div>
                <span>Carte bancaire</span>
            </div>
        </div>
    </div>

    {{-- GARANTIE --}}
    <div class="guarantee-section">
        <div class="guarantee-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                <polyline points="9 12 11 14 15 10"/>
            </svg>
        </div>
        <div class="guarantee-body">
            <h3>Essai 30 jours — zéro risque</h3>
            <p>Accès complet au plan Pro pendant 30 jours. Aucune carte bancaire, aucun engagement. Vous choisissez votre plan seulement si vous êtes convaincu.</p>
        </div>
    </div>

    {{-- FAQ --}}
    <div class="faq-section">
        <h2 class="faq-title">Questions fréquentes</h2>
        <div class="faq-list">
            <div class="faq-item">
                <div class="faq-q" onclick="toggleFaq(this)">
                    Qu'est-ce qu'une "unité" ?
                    <svg class="faq-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                </div>
                <div class="faq-a">Une unité est un bien immobilier géré dans la plateforme — appartement, villa, studio, bureau ou commerce. Les biens archivés ne sont pas comptés. Un immeuble de 10 appartements compte pour 10 unités.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q" onclick="toggleFaq(this)">
                    Peut-on changer de plan en cours d'abonnement ?
                    <svg class="faq-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                </div>
                <div class="faq-a">Oui. Vous pouvez passer à un plan supérieur à tout moment depuis la page abonnement. Vos données et biens existants sont toujours accessibles — on ne rétrograde jamais en cours de période payée.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q" onclick="toggleFaq(this)">
                    Le tarif annuel "10+2" — comment ça marche ?
                    <svg class="faq-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                </div>
                <div class="faq-a">Vous réglez l'équivalent de 10 mois et bénéficiez de 12 mois d'accès. Les 2 mois offerts sont déduits directement du montant annuel — c'est l'économie la plus simple possible.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q" onclick="toggleFaq(this)">
                    Les déclarations DGID sont-elles prêtes à envoyer ?
                    <svg class="faq-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                </div>
                <div class="faq-a">En plan Agence, la plateforme calcule automatiquement la BRS, la TVA sur commissions, l'IRPP et génère des bilans fiscaux PDF conformes CGI SN. Ces documents sont utilisables directement pour vos déclarations DGID.</div>
            </div>
        </div>
    </div>

</div>{{-- /pricing-wrap --}}

<footer>
    <div class="footer-inner">
        <span class="footer-copy">© {{ date('Y') }} BimoTech Immo. Tous droits réservés.</span>
        <div class="footer-links">
            <a href="{{ route('home') }}">Accueil</a>
            <a href="{{ route('demo') }}">Démo</a>
            <a href="{{ route('faq') }}">FAQ</a>
            <a href="{{ route('contact') }}">Contact</a>
            <a href="{{ route('mentions-legales') }}">Mentions légales</a>
            <a href="{{ route('confidentialite') }}">Confidentialité</a>
        </div>
    </div>
</footer>

<script>
const prices = {
    mensuel: { agence: '69 900', pro: '39 900', starter: '19 900' },
    annuel:  { agence: '699 000', pro: '399 000', starter: '199 000' },
};
const savings = { agence: '139 800', pro: '79 800', starter: '39 800' };

function setBilling(mode) {
    document.getElementById('btn-mensuel').classList.toggle('active', mode === 'mensuel');
    document.getElementById('btn-annuel').classList.toggle('active', mode === 'annuel');
    ['agence','pro','starter'].forEach(plan => {
        document.getElementById('price-' + plan).textContent = prices[mode][plan];
        const detail = document.getElementById('annual-' + plan);
        detail.innerHTML = mode === 'annuel'
            ? '10 mois + 2 offerts · <span class="plan-saving">économie ' + savings[plan] + ' F</span>'
            : '&nbsp;';
    });
}

function toggleFaq(el) {
    el.parentElement.classList.toggle('open');
}
</script>
</body>
</html>
