<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="BimoTech Immo — La plateforme SaaS de gestion immobilière pour les agences sénégalaises. Conforme TVA 18%, NINEA, loi 81-18.">
<title>BimoTech Immo — Gérez votre agence immobilière au Sénégal</title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,wght@0,300;0,400;0,500;1,300&display=swap" rel="stylesheet">

<style>
*{box-sizing:border-box;margin:0;padding:0}
:root{
    --gold:#c9a84c;
    --dark:#0d1117;
    --dark2:#161b22;
    --dark3:#21262d;
    --text:#e6edf3;
    --muted:#8b949e;
    --border:rgba(255,255,255,.08);
}
html{scroll-behavior:smooth}
body{font-family:'DM Sans',sans-serif;background:var(--dark);color:var(--text);overflow-x:hidden}

/* ─── NAV ─────────────────────────────────────────────── */
nav{
    position:fixed;top:0;left:0;right:0;z-index:100;
    padding:0 5%;height:64px;
    display:flex;align-items:center;justify-content:space-between;
    background:rgba(13,17,23,.85);
    backdrop-filter:blur(12px);
    border-bottom:1px solid var(--border);
}
.nav-logo{font-family:'Syne',sans-serif;font-size:18px;font-weight:800;color:var(--gold);letter-spacing:-0.5px;text-decoration:none}
.nav-logo span{color:var(--text)}
.nav-links{display:flex;align-items:center;gap:2rem}
.nav-links a{color:var(--muted);text-decoration:none;font-size:13.5px;transition:color .2s}
.nav-links a:hover{color:var(--text)}
.nav-actions{display:flex;align-items:center;gap:10px}
.nav-login{color:var(--muted);text-decoration:none;font-size:13.5px;padding:8px 16px;border-radius:8px;transition:color .2s}
.nav-login:hover{color:var(--text)}
.nav-cta{
    background:var(--gold);color:var(--dark);
    font-family:'DM Sans',sans-serif;font-size:13px;font-weight:600;
    padding:8px 20px;border-radius:8px;text-decoration:none;
    transition:opacity .2s;
}
.nav-cta:hover{opacity:.85}

/* ─── HERO ─────────────────────────────────────────────── */
.hero{
    min-height:100vh;
    display:flex;align-items:center;justify-content:center;flex-direction:column;
    text-align:center;
    padding:120px 5% 80px;
    position:relative;overflow:hidden;
}
.hero::before{
    content:'';position:absolute;top:-20%;left:50%;transform:translateX(-50%);
    width:800px;height:800px;
    background:radial-gradient(circle,rgba(201,168,76,.07) 0%,transparent 70%);
    pointer-events:none;
}
.hero-badge{
    display:inline-flex;align-items:center;gap:8px;
    background:rgba(201,168,76,.1);border:1px solid rgba(201,168,76,.25);
    border-radius:99px;padding:6px 16px;font-size:12px;color:var(--gold);font-weight:500;
    margin-bottom:2rem;
    animation:fadeUp .6s ease both;
}
.pulse{width:6px;height:6px;background:var(--gold);border-radius:50%;animation:pulse 2s infinite}
@keyframes pulse{0%,100%{opacity:1}50%{opacity:.3}}

h1{
    font-family:'Syne',sans-serif;
    font-size:clamp(36px,6vw,72px);font-weight:800;
    line-height:1.05;letter-spacing:-2px;
    margin-bottom:1.5rem;
    animation:fadeUp .7s .1s ease both;
}
h1 em{font-style:normal;color:var(--gold)}
.hero-sub{
    font-size:clamp(15px,2vw,18px);color:var(--muted);
    max-width:560px;line-height:1.7;margin-bottom:2.5rem;font-weight:300;
    animation:fadeUp .7s .2s ease both;
}
.hero-btns{display:flex;gap:12px;justify-content:center;flex-wrap:wrap;animation:fadeUp .7s .3s ease both}

.btn-gold{
    background:var(--gold);color:var(--dark);
    font-family:'DM Sans',sans-serif;font-weight:700;font-size:14px;
    padding:13px 28px;border-radius:10px;text-decoration:none;
    transition:all .2s;display:inline-block;
}
.btn-gold:hover{opacity:.9;transform:translateY(-1px)}
.btn-outline{
    background:transparent;color:var(--text);
    font-family:'DM Sans',sans-serif;font-weight:500;font-size:14px;
    padding:13px 28px;border-radius:10px;text-decoration:none;
    border:1px solid var(--border);transition:all .2s;display:inline-block;
}
.btn-outline:hover{border-color:rgba(255,255,255,.2);background:rgba(255,255,255,.04)}

/* Mockup dashboard */
.mockup-wrap{
    margin-top:4rem;position:relative;
    animation:fadeUp .8s .4s ease both;
    max-width:900px;width:100%;
}
.mockup-glow{
    position:absolute;top:-40px;left:50%;transform:translateX(-50%);
    width:600px;height:200px;
    background:radial-gradient(ellipse,rgba(201,168,76,.12) 0%,transparent 70%);
    pointer-events:none;
}
.mockup{
    background:var(--dark2);border:1px solid var(--border);
    border-radius:16px;overflow:hidden;
    box-shadow:0 40px 80px rgba(0,0,0,.6);
}
.mockup-bar{
    background:var(--dark3);padding:10px 16px;
    display:flex;align-items:center;gap:8px;
    border-bottom:1px solid var(--border);
}
.m-dot{width:10px;height:10px;border-radius:50%}
.mockup-content{padding:20px;display:grid;grid-template-columns:180px 1fr;gap:16px;min-height:280px}
.m-sidebar{background:var(--dark);border-radius:10px;padding:14px 10px}
.m-logo{font-family:'Syne',sans-serif;font-size:12px;color:var(--gold);font-weight:700;margin-bottom:16px;padding:0 6px}
.m-item{padding:7px 10px;border-radius:6px;font-size:11px;margin-bottom:2px;display:flex;align-items:center;gap:8px}
.m-item.on{background:rgba(201,168,76,.12);color:var(--gold)}
.m-item:not(.on){color:#484f58}
.m-d{width:6px;height:6px;border-radius:50%;background:currentColor;flex-shrink:0}
.m-main{display:flex;flex-direction:column;gap:12px}
.m-kpis{display:grid;grid-template-columns:repeat(3,1fr);gap:10px}
.m-kpi{background:var(--dark);border-radius:8px;padding:12px;border-top:2px solid}
.m-kpi-l{font-size:9px;color:#484f58;margin-bottom:6px;text-transform:uppercase;letter-spacing:.5px}
.m-kpi-v{font-family:'Syne',sans-serif;font-size:16px;font-weight:700}
.m-table{background:var(--dark);border-radius:8px;padding:12px;flex:1}
.m-th{display:grid;grid-template-columns:1fr 1fr 1fr 80px;gap:8px;padding-bottom:8px;border-bottom:1px solid var(--border);font-size:9px;color:#484f58;text-transform:uppercase;letter-spacing:.5px}
.m-tr{display:grid;grid-template-columns:1fr 1fr 1fr 80px;gap:8px;padding:7px 0;border-bottom:1px solid rgba(255,255,255,.03);font-size:10px;color:var(--muted)}
.m-badge{font-size:9px;padding:2px 7px;border-radius:99px;display:inline-block}

/* ─── STATS ─────────────────────────────────────────────── */
.stats-bar{
    padding:3rem 5%;
    background:var(--dark2);
    border-top:1px solid var(--border);border-bottom:1px solid var(--border);
}
.stats-inner{max-width:900px;margin:0 auto;display:grid;grid-template-columns:repeat(4,1fr);gap:2rem;text-align:center}
.stat-num{font-family:'Syne',sans-serif;font-size:32px;font-weight:800;color:var(--gold);line-height:1}
.stat-label{font-size:12px;color:var(--muted);margin-top:6px}

/* ─── SECTIONS ─────────────────────────────────────────────── */
.section{padding:6rem 5%}
.section-inner{max-width:1000px;margin:0 auto}
.section-tag{font-size:11px;color:var(--gold);font-weight:600;letter-spacing:2px;text-transform:uppercase;margin-bottom:1rem}
h2{font-family:'Syne',sans-serif;font-size:clamp(26px,4vw,42px);font-weight:800;letter-spacing:-1px;line-height:1.1;margin-bottom:1rem}
h2 em{font-style:normal;color:var(--gold)}
.section-sub{color:var(--muted);font-size:15px;line-height:1.7;max-width:520px;font-weight:300;margin-bottom:0}

/* Features */
.features-grid{
    display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));
    gap:1.5px;margin-top:3.5rem;
    background:var(--border);border-radius:16px;overflow:hidden;border:1px solid var(--border);
}
.feat{background:var(--dark2);padding:2rem;transition:background .2s}
.feat:hover{background:var(--dark3)}
.feat-icon{width:40px;height:40px;background:rgba(201,168,76,.1);border-radius:10px;display:flex;align-items:center;justify-content:center;margin-bottom:1.25rem}
.feat-title{font-family:'Syne',sans-serif;font-size:15px;font-weight:700;margin-bottom:.5rem;color:var(--text)}
.feat-desc{font-size:13px;color:var(--muted);line-height:1.65}

/* Conformité */
.compliance{background:var(--dark3);border-radius:20px;padding:3rem;border:1px solid var(--border);position:relative;overflow:hidden;margin-top:3rem}
.compliance::before{content:'';position:absolute;top:-60px;right:-60px;width:300px;height:300px;background:radial-gradient(circle,rgba(201,168,76,.06) 0%,transparent 70%);pointer-events:none}
.comp-grid{display:grid;grid-template-columns:1fr 1fr;gap:2rem;margin-top:0}
.comp-item{display:flex;gap:12px;align-items:flex-start}
.comp-check{width:20px;height:20px;background:rgba(201,168,76,.15);border-radius:6px;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:2px}
.comp-title{font-size:13.5px;font-weight:500;color:var(--text);margin-bottom:3px}
.comp-desc{font-size:12px;color:var(--muted);line-height:1.5}

/* Steps */
.steps{display:grid;grid-template-columns:repeat(3,1fr);gap:2rem;margin-top:3rem;position:relative}
.steps::before{content:'';position:absolute;top:28px;left:calc(16.6% + 16px);right:calc(16.6% + 16px);height:1px;background:linear-gradient(90deg,var(--gold),rgba(201,168,76,.3));z-index:0}
.step{text-align:center;position:relative;z-index:1}
.step-num{width:56px;height:56px;background:var(--gold);color:var(--dark);border-radius:50%;display:flex;align-items:center;justify-content:center;font-family:'Syne',sans-serif;font-size:18px;font-weight:800;margin:0 auto 1.25rem}
.step-title{font-family:'Syne',sans-serif;font-size:15px;font-weight:700;margin-bottom:.5rem}
.step-desc{font-size:13px;color:var(--muted);line-height:1.6}

/* Pricing */
.pricing-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:1.5rem;margin-top:3rem}
.plan{background:var(--dark2);border:1px solid var(--border);border-radius:16px;padding:2rem;transition:border-color .2s;position:relative}
.plan:hover{border-color:rgba(201,168,76,.3)}
.plan.featured{border-color:var(--gold)}
.plan-badge{position:absolute;top:-12px;left:50%;transform:translateX(-50%);background:var(--gold);color:var(--dark);font-size:11px;font-weight:700;padding:3px 14px;border-radius:99px;white-space:nowrap}
.plan-name{font-family:'Syne',sans-serif;font-size:13px;font-weight:700;color:var(--muted);margin-bottom:1rem;text-transform:uppercase;letter-spacing:1px}
.plan-price{font-family:'Syne',sans-serif;font-size:34px;font-weight:800;color:var(--text);line-height:1}
.plan-price span{font-size:13px;color:var(--muted);font-weight:400}
.plan-desc{font-size:13px;color:var(--muted);margin:1rem 0 1.5rem;line-height:1.6}
.plan-features{list-style:none;display:flex;flex-direction:column;gap:8px;margin-bottom:2rem}
.plan-features li{font-size:13px;color:var(--muted);display:flex;gap:8px;align-items:flex-start}
.plan-features li::before{content:'✓';color:var(--gold);font-weight:700;flex-shrink:0}
.plan-btn{display:block;text-align:center;padding:11px;border-radius:10px;font-family:'DM Sans',sans-serif;font-size:13.5px;font-weight:600;text-decoration:none;transition:all .2s}
.plan-btn-outline{border:1px solid var(--border);color:var(--text)}
.plan-btn-outline:hover{border-color:rgba(255,255,255,.2);background:rgba(255,255,255,.04)}
.plan-btn-gold{background:var(--gold);color:var(--dark)}
.plan-btn-gold:hover{opacity:.9}

/* CTA final */
.cta-section{text-align:center;padding:6rem 5%;background:var(--dark2);border-top:1px solid var(--border)}
.cta-section p{color:var(--muted);max-width:480px;margin:1rem auto 2.5rem;line-height:1.7;font-size:15px}

/* Footer */
footer{padding:2.5rem 5%;border-top:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem}
.footer-logo{font-family:'Syne',sans-serif;font-size:15px;font-weight:800;color:var(--gold)}
.footer-links{display:flex;gap:1.5rem}
.footer-links a{font-size:12px;color:var(--muted);text-decoration:none;transition:color .2s}
.footer-links a:hover{color:var(--text)}
.footer-copy{font-size:12px;color:#484f58}

@keyframes fadeUp{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}

/* Responsive */
@media(max-width:768px){
    .nav-links{display:none}
    .stats-inner{grid-template-columns:repeat(2,1fr)}
    .comp-grid{grid-template-columns:1fr}
    .steps{grid-template-columns:1fr;gap:1.5rem}
    .steps::before{display:none}
    .mockup-content{grid-template-columns:1fr}
    .m-sidebar{display:none}
    footer{flex-direction:column;text-align:center}
}
</style>
</head>
<body>

<!-- ─── NAVIGATION ─────────────────────────────────────── -->
<?php echo $__env->make('partials.public-nav', ['active' => 'contact'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<!-- ─── HERO ──────────────────────────────────────────── -->
<section class="hero">
    <div class="hero-badge">
        <div class="pulse"></div>
        🇸🇳 Conçu pour le marché sénégalais
    </div>

    <h1>La plateforme SaaS pour<br>gérer votre <em>agence immobilière</em></h1>

    <p class="hero-sub">
        Biens, locataires, contrats, paiements et quittances — tout en un.
        Conforme TVA 18%, NINEA, loi 81-18 et CGI article 357.
    </p>

    <div class="hero-btns">
        <a href="<?php echo e(route('register')); ?>" class="btn-gold">Créer mon agence gratuitement →</a>
        <a href="<?php echo e(route('demo')); ?>" class="btn-outline">Demander une démo</a>
    </div>

    
    <div class="mockup-wrap">
        <div class="mockup-glow"></div>
        <div class="mockup">
            <div class="mockup-bar">
                <div class="m-dot" style="background:#ff5f57"></div>
                <div class="m-dot" style="background:#febc2e"></div>
                <div class="m-dot" style="background:#28c840"></div>
                <div style="margin-left:8px;font-size:11px;color:#484f58;font-family:monospace">app.bimotech.sn/dashboard</div>
            </div>
            <div class="mockup-content">
                <div class="m-sidebar">
                    <div class="m-logo">BimoTech</div>
                    <div class="m-item on"><div class="m-d"></div>Dashboard</div>
                    <div class="m-item"><div class="m-d"></div>Biens</div>
                    <div class="m-item"><div class="m-d"></div>Contrats</div>
                    <div class="m-item"><div class="m-d"></div>Paiements</div>
                    <div class="m-item"><div class="m-d"></div>Rapports</div>
                </div>
                <div class="m-main">
                    <div class="m-kpis">
                        <div class="m-kpi" style="border-color:#c9a84c">
                            <div class="m-kpi-l">Loyers du mois</div>
                            <div class="m-kpi-v" style="color:#c9a84c">2,4M</div>
                        </div>
                        <div class="m-kpi" style="border-color:#3B6D11">
                            <div class="m-kpi-l">Biens loués</div>
                            <div class="m-kpi-v" style="color:#3B6D11">12</div>
                        </div>
                        <div class="m-kpi" style="border-color:#E24B4A">
                            <div class="m-kpi-l">Impayés</div>
                            <div class="m-kpi-v" style="color:#E24B4A">2</div>
                        </div>
                    </div>
                    <div class="m-table">
                        <div class="m-th"><span>Locataire</span><span>Bien</span><span>Échéance</span><span>Statut</span></div>
                        <div class="m-tr"><span>A. Diallo</span><span>Villa Fann</span><span>05/06</span><span><span class="m-badge" style="background:rgba(59,109,17,.15);color:#3B6D11">Payé</span></span></div>
                        <div class="m-tr"><span>M. Ndiaye</span><span>Appt Plateau</span><span>05/06</span><span><span class="m-badge" style="background:rgba(186,117,23,.15);color:#854F0B">Attente</span></span></div>
                        <div class="m-tr"><span>F. Sow</span><span>Bureau Almadies</span><span>05/06</span><span><span class="m-badge" style="background:rgba(59,109,17,.15);color:#3B6D11">Payé</span></span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ─── STATS ─────────────────────────────────────────── -->
<div class="stats-bar">
    <div class="stats-inner">
        <div><div class="stat-num">150+</div><div class="stat-label">Biens gérés</div></div>
        <div><div class="stat-num">12</div><div class="stat-label">Agences actives</div></div>
        <div><div class="stat-num">98%</div><div class="stat-label">Taux de recouvrement</div></div>
        <div><div class="stat-num">0</div><div class="stat-label">Erreur fiscale</div></div>
    </div>
</div>

<!-- ─── FONCTIONNALITÉS ───────────────────────────────── -->
<section class="section" id="fonctionnalites">
    <div class="section-inner">
        <div class="section-tag">Fonctionnalités</div>
        <h2>Tout ce dont votre agence<br>a besoin, <em>rien de superflu</em></h2>
        <p class="section-sub">BimoTech n'est pas un annuaire immobilier. C'est un outil de gestion professionnelle pour les agences qui veulent structurer leur activité.</p>

        <div class="features-grid">
            <div class="feat">
                <div class="feat-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#c9a84c" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9,22 9,12 15,12 15,22"/></svg>
                </div>
                <div class="feat-title">Gestion des biens</div>
                <div class="feat-desc">Ajoutez vos biens avec photos, surface, type et loyer. Suivez l'occupation en temps réel avec des statuts clairs.</div>
            </div>
            <div class="feat">
                <div class="feat-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#c9a84c" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14,2 14,8 20,8"/></svg>
                </div>
                <div class="feat-title">Contrats de bail</div>
                <div class="feat-desc">Créez vos contrats de bail vide ou meublé. Durée, dépôt de garantie, clauses — tout est archivé et accessible.</div>
            </div>
            <div class="feat">
                <div class="feat-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#c9a84c" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                </div>
                <div class="feat-title">Encaissement des loyers</div>
                <div class="feat-desc">Espèces, virement, Wave, Orange Money. Chaque paiement enregistré génère automatiquement une quittance légale.</div>
            </div>
            <div class="feat">
                <div class="feat-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#c9a84c" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </div>
                <div class="feat-title">Espace propriétaire</div>
                <div class="feat-desc">Chaque propriétaire accède à ses biens, ses revenus nets et l'historique de ses paiements. En temps réel.</div>
            </div>
            <div class="feat">
                <div class="feat-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#c9a84c" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                </div>
                <div class="feat-title">Espace locataire</div>
                <div class="feat-desc">Vos locataires accèdent à leurs quittances, suivent leur bail et contactent l'agence via WhatsApp en un clic.</div>
            </div>
            <div class="feat">
                <div class="feat-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#c9a84c" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                </div>
                <div class="feat-title">Rapports financiers</div>
                <div class="feat-desc">Commission HT/TTC, TVA, TOM, net propriétaire. Bilans mensuels et annuels avec taux d'occupation.</div>
            </div>
        </div>
    </div>
</section>

<!-- ─── CONFORMITÉ ────────────────────────────────────── -->
<section class="section" id="conformite" style="background:var(--dark2);border-top:1px solid var(--border);border-bottom:1px solid var(--border)">
    <div class="section-inner">
        <div class="section-tag">Conformité fiscale</div>
        <h2>Le seul SaaS immobilier<br><em>conforme au droit sénégalais</em></h2>
        <p class="section-sub">BimoTech intègre nativement les obligations fiscales et légales du Sénégal. Pas de modules à activer, pas de configuration complexe.</p>

        <div class="compliance">
            <div class="comp-grid">
                <div class="comp-item">
                    <div class="comp-check"><svg width="10" height="10" viewBox="0 0 12 12" fill="none" stroke="#c9a84c" stroke-width="2.5"><polyline points="2,6 5,9 10,3"/></svg></div>
                    <div>
                        <div class="comp-title">TVA 18% — CGI article 357</div>
                        <div class="comp-desc">Calcul automatique sur les commissions d'agence, décomposition HT/TTC claire.</div>
                    </div>
                </div>
                <div class="comp-item">
                    <div class="comp-check"><svg width="10" height="10" viewBox="0 0 12 12" fill="none" stroke="#c9a84c" stroke-width="2.5"><polyline points="2,6 5,9 10,3"/></svg></div>
                    <div>
                        <div class="comp-title">Loi 81-18 — Encadrement des loyers</div>
                        <div class="comp-desc">Vérification automatique des plafonds selon la surface. Alerte si le loyer est non conforme.</div>
                    </div>
                </div>
                <div class="comp-item">
                    <div class="comp-check"><svg width="10" height="10" viewBox="0 0 12 12" fill="none" stroke="#c9a84c" stroke-width="2.5"><polyline points="2,6 5,9 10,3"/></svg></div>
                    <div>
                        <div class="comp-title">TOM — Taxe sur Opérations Mobilières</div>
                        <div class="comp-desc">Taux paramétrable par commune. Dakar 5%, autres communes selon arrêté local.</div>
                    </div>
                </div>
                <div class="comp-item">
                    <div class="comp-check"><svg width="10" height="10" viewBox="0 0 12 12" fill="none" stroke="#c9a84c" stroke-width="2.5"><polyline points="2,6 5,9 10,3"/></svg></div>
                    <div>
                        <div class="comp-title">NINEA — Identification fiscale agence</div>
                        <div class="comp-desc">Le NINEA de votre agence apparaît automatiquement sur toutes les quittances et factures.</div>
                    </div>
                </div>
                <div class="comp-item">
                    <div class="comp-check"><svg width="10" height="10" viewBox="0 0 12 12" fill="none" stroke="#c9a84c" stroke-width="2.5"><polyline points="2,6 5,9 10,3"/></svg></div>
                    <div>
                        <div class="comp-title">Quittances PDF légales</div>
                        <div class="comp-desc">Générées automatiquement après validation d'un paiement. Archivées, numérotées séquentiellement.</div>
                    </div>
                </div>
                <div class="comp-item">
                    <div class="comp-check"><svg width="10" height="10" viewBox="0 0 12 12" fill="none" stroke="#c9a84c" stroke-width="2.5"><polyline points="2,6 5,9 10,3"/></svg></div>
                    <div>
                        <div class="comp-title">Dépôt de garantie — 2 mois max</div>
                        <div class="comp-desc">Plafonnement automatique au seuil légal loi 81-18, avec avertissement si dépassement.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ─── COMMENT ÇA MARCHE ─────────────────────────────── -->
<section class="section">
    <div class="section-inner">
        <div class="section-tag">Démarrage rapide</div>
        <h2>Opérationnel en <em>moins de 10 minutes</em></h2>
        <div class="steps">
            <div class="step">
                <div class="step-num">1</div>
                <div class="step-title">Créez votre agence</div>
                <div class="step-desc">Renseignez le nom, logo, NINEA et coordonnées. Votre espace est immédiatement actif et sécurisé.</div>
            </div>
            <div class="step">
                <div class="step-num">2</div>
                <div class="step-title">Ajoutez vos biens</div>
                <div class="step-desc">Importez votre portefeuille avec photos, loyers et propriétaires. Créez vos premiers contrats de bail.</div>
            </div>
            <div class="step">
                <div class="step-num">3</div>
                <div class="step-title">Gérez en temps réel</div>
                <div class="step-desc">Paiements, quittances, rapports — tout se fait en quelques clics depuis votre tableau de bord.</div>
            </div>
        </div>
    </div>
</section>

<!-- ─── TARIFS ────────────────────────────────────────── -->
<section class="section" id="tarifs" style="background:var(--dark2);border-top:1px solid var(--border)">
    <div class="section-inner">
        <div class="section-tag">Tarifs</div>
        <h2>Simple, transparent,<br><em>sans surprise</em></h2>
        <p class="section-sub">Tous les plans incluent la conformité fiscale complète. Aucune carte bancaire requise pour démarrer.</p>

        <div class="pricing-grid">
            
            <div class="plan">
                <div class="plan-name">Démarrage</div>
                <div class="plan-price">Gratuit <span>/ toujours</span></div>
                <div class="plan-desc">Pour découvrir BimoTech et gérer vos premiers biens sans engagement.</div>
                <ul class="plan-features">
                    <li>Jusqu'à 5 biens</li>
                    <li>2 utilisateurs</li>
                    <li>Quittances PDF</li>
                    <li>Conformité fiscale SN</li>
                </ul>
                <a href="<?php echo e(route('register')); ?>" class="plan-btn plan-btn-outline">Commencer gratuitement</a>
            </div>

            
            <div class="plan featured">
                <div class="plan-badge">Le plus populaire</div>
                <div class="plan-name">Agence</div>
                <div class="plan-price">25 000 <span>FCFA / mois</span></div>
                <div class="plan-desc">Pour les agences qui veulent gérer leur activité sans limite et sans friction.</div>
                <ul class="plan-features">
                    <li>Biens illimités</li>
                    <li>Utilisateurs illimités</li>
                    <li>Rapports financiers avancés</li>
                    <li>Espace propriétaire & locataire</li>
                    <li>Logs d'activité complets</li>
                    <li>Support prioritaire WhatsApp</li>
                </ul>
                <a href="<?php echo e(route('register')); ?>" class="plan-btn plan-btn-gold">Démarrer l'essai gratuit</a>
            </div>

            
            <div class="plan">
                <div class="plan-name">Réseau</div>
                <div class="plan-price">Sur devis</div>
                <div class="plan-desc">Pour les groupes immobiliers avec plusieurs agences à gérer depuis un seul tableau de bord.</div>
                <ul class="plan-features">
                    <li>Multi-agences</li>
                    <li>Tableau de bord groupe</li>
                    <li>API & intégrations</li>
                    <li>Formation sur site à Dakar</li>
                    <li>SLA garanti</li>
                </ul>
                <a href="<?php echo e(route('contact')); ?>" class="plan-btn plan-btn-outline">Nous contacter</a>
            </div>
        </div>
    </div>
</section>

<!-- ─── CTA FINAL ─────────────────────────────────────── -->
<div class="cta-section">
    <div class="section-tag">Prêt à vous lancer ?</div>
    <h2>Rejoignez les agences qui<br>gèrent mieux avec <em>BimoTech</em></h2>
    <p>Aucune installation, aucune carte bancaire. Votre agence est en ligne en moins de 10 minutes.</p>
    <div class="hero-btns">
        <a href="<?php echo e(route('register')); ?>" class="btn-gold">Créer mon agence gratuitement →</a>
        <a href="<?php echo e(route('demo')); ?>" class="btn-outline">Demander une démo</a>
    </div>
</div>

<!-- ─── FOOTER ────────────────────────────────────────── -->
<footer>
    <div class="footer-logo">BimoTech Immo</div>
    <div class="footer-links">
        <a href="<?php echo e(route('login')); ?>">Connexion</a>
        <a href="<?php echo e(route('faq')); ?>">FAQ</a>
        <a href="<?php echo e(route('contact')); ?>">Contact</a>
        <a href="<?php echo e(route('mentions-legales')); ?>">Mentions légales</a>
        <a href="<?php echo e(route('confidentialite')); ?>">Confidentialité</a>
    </div>
    <div class="footer-copy">© <?php echo e(date('Y')); ?> BimoTech · Dakar, Sénégal</div>
</footer>

</body>
</html><?php /**PATH C:\Users\ph\bimotech-immo\resources\views/welcome.blade.php ENDPATH**/ ?>