<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="BimoTech Immo — Logiciel de gestion immobilière pour agences au Sénégal. Biens, contrats, loyers, quittances conformes TVA 18%, BRS, DGID. Essai gratuit 30 jours.">
<meta name="keywords" content="gestion immobilière Sénégal, logiciel agence immobilière Dakar, quittance loyer Sénégal, gestion locataires, TVA immobilier CGI SN, logiciel gestion biens, contrat bail Sénégal">
<meta name="author" content="BimoTech">
<meta name="robots" content="index, follow">
<link rel="canonical" href="https://immo.bimotechsn.com/">
<title>BimoTech Immo — Gestion immobilière Sénégal</title>

{{-- Open Graph (Facebook, LinkedIn, WhatsApp) --}}
<meta property="og:type"        content="website">
<meta property="og:url"         content="https://immo.bimotechsn.com/">
<meta property="og:title"       content="BimoTech Immo — Gestion immobilière professionnelle au Sénégal">
<meta property="og:description" content="Gérez biens, contrats, loyers et quittances conformes TVA 18% et CGI SN. Essai gratuit 30 jours, sans carte bancaire.">
<meta property="og:image"       content="https://immo.bimotechsn.com/og-image.png">
<meta property="og:image:width"  content="1200">
<meta property="og:image:height" content="630">
<meta property="og:locale"      content="fr_SN">
<meta property="og:site_name"   content="BimoTech Immo">

{{-- Twitter Card --}}
<meta name="twitter:card"        content="summary_large_image">
<meta name="twitter:title"       content="BimoTech Immo — Logiciel gestion immobilière Sénégal">
<meta name="twitter:description" content="Biens, contrats, loyers, quittances. Conforme TVA 18%, BRS, DGID. Essai 30 jours gratuit.">
<meta name="twitter:image"       content="https://immo.bimotechsn.com/og-image.png">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,wght@0,300;0,400;0,500;0,600;1,300&display=swap" media="print" onload="this.media='all'">
<noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,wght@0,300;0,400;0,500;0,600;1,300&display=swap"></noscript>
<style>
/* ─── RESET & VARIABLES ─────────────────────────────────── */
*{box-sizing:border-box;margin:0;padding:0}
:root{
    --gold:#c9a84c;
    --gold-light:#e8c96a;
    --gold-dim:rgba(201,168,76,.12);
    --dark:#080c12;
    --dark2:#0d1117;
    --dark3:#161b22;
    --dark4:#21262d;
    /* Thème doré glacé */
    --bg:#faf9f6;           /* fond page : blanc chaud imperceptible */
    --bg2:#f5f3ee;          /* sections alternées */
    --bg3:#f0ede6;          /* sections encore plus chaudes */
    --bg-warm:#fffdf7;
    /* Card glacée dorée */
    --card-bg:linear-gradient(145deg,#fffef5 0%,#ffffff 55%,#fffef8 100%);
    --card-border:rgba(201,168,76,.16);
    --card-shadow:0 2px 16px rgba(201,168,76,.07),0 1px 3px rgba(0,0,0,.05);
    --card-shadow-hover:0 10px 32px rgba(201,168,76,.14),0 3px 8px rgba(0,0,0,.07);
    --text:#0d1117;
    --text2:#374151;
    --muted:#6b7280;
    --muted2:#9ca3af;
    --border:rgba(201,168,76,.1);
    --border2:rgba(201,168,76,.2);
    --radius:14px;
    --radius-sm:10px;
}
html{scroll-behavior:smooth}
body{font-family:'DM Sans',sans-serif;background:var(--bg);color:var(--text);overflow-x:hidden;-webkit-font-smoothing:antialiased}

/* ─── CARD GLACÉE DORÉE ─────────────────────────────────── */
.gold-card{
    background:var(--card-bg);
    border:1px solid var(--card-border);
    border-radius:var(--radius);
    box-shadow:var(--card-shadow);
    transition:all .22s cubic-bezier(.4,0,.2,1);
}
.gold-card:hover{
    box-shadow:var(--card-shadow-hover);
    border-color:rgba(201,168,76,.3);
    transform:translateY(-3px);
}

/* ─── HERO ──────────────────────────────────────────────── */
.hero{
    min-height:100vh;
    display:flex;align-items:center;justify-content:center;flex-direction:column;
    text-align:center;
    padding:100px 5% 80px;
    position:relative;overflow:hidden;
}
/* Dot grid sur fond clair */
.hero::before{
    content:'';position:absolute;inset:0;
    background-image:radial-gradient(rgba(0,0,0,.06) 1px, transparent 1px);
    background-size:32px 32px;
    pointer-events:none;
    mask-image:radial-gradient(ellipse 80% 70% at 50% 50%, black 30%, transparent 100%);
    -webkit-mask-image:radial-gradient(ellipse 80% 70% at 50% 50%, black 30%, transparent 100%);
}
/* Halo doré subtil */
.hero::after{
    content:'';position:absolute;top:-10%;left:50%;transform:translateX(-50%);
    width:900px;height:600px;
    background:radial-gradient(ellipse, rgba(201,168,76,.08) 0%, transparent 65%);
    pointer-events:none;
}
.hero-inner{position:relative;z-index:1;max-width:820px;width:100%}

.hero-badge{
    display:inline-flex;align-items:center;gap:8px;
    background:rgba(201,168,76,.08);
    border:1px solid rgba(201,168,76,.2);
    border-radius:99px;padding:6px 18px;
    font-size:12px;color:var(--gold);font-weight:500;letter-spacing:.3px;
    margin-bottom:2rem;
    animation:fadeUp .5s ease both;
}
.pulse-dot{
    width:7px;height:7px;background:var(--gold);
    border-radius:50%;flex-shrink:0;
    box-shadow:0 0 0 0 rgba(201,168,76,.5);
    animation:pulse-ring 2s infinite;
}
@keyframes pulse-ring{
    0%{box-shadow:0 0 0 0 rgba(201,168,76,.5)}
    70%{box-shadow:0 0 0 8px rgba(201,168,76,0)}
    100%{box-shadow:0 0 0 0 rgba(201,168,76,0)}
}

.hero h1{
    font-family:'Syne',sans-serif;
    font-size:clamp(38px,6.5vw,76px);
    font-weight:800;
    line-height:1.04;
    letter-spacing:-2.5px;
    margin-bottom:1.5rem;
    animation:fadeUp .6s .08s ease both;
}
.hero h1 em{
    font-style:normal;
    background:linear-gradient(135deg, var(--gold) 0%, var(--gold-light) 60%, #f0d87a 100%);
    -webkit-background-clip:text;
    -webkit-text-fill-color:transparent;
    background-clip:text;
}
.hero-sub{
    font-size:clamp(15px,2vw,18px);
    color:var(--muted2);
    max-width:540px;margin:0 auto 2.5rem;
    line-height:1.75;font-weight:400;
    animation:fadeUp .6s .16s ease both;
}
.hero-btns{
    display:flex;gap:12px;justify-content:center;flex-wrap:wrap;
    animation:fadeUp .6s .24s ease both;
    margin-bottom:1.5rem;
}
.hero-note{
    font-size:12px;color:var(--muted2);
    animation:fadeUp .6s .32s ease both;
}
.hero-note span{color:var(--gold)}

/* CTA Buttons */
.btn-gold{
    background:linear-gradient(135deg, var(--gold) 0%, var(--gold-light) 100%);
    color:#080c12;
    font-family:'DM Sans',sans-serif;font-weight:700;font-size:14px;
    padding:13px 28px;border-radius:var(--radius-sm);
    text-decoration:none;transition:all .2s;display:inline-flex;align-items:center;gap:6px;
    box-shadow:0 4px 24px rgba(201,168,76,.25);
}
.btn-gold:hover{transform:translateY(-2px);box-shadow:0 8px 32px rgba(201,168,76,.35);opacity:.95}
.btn-outline{
    background:#fff;color:var(--text);
    font-family:'DM Sans',sans-serif;font-weight:500;font-size:14px;
    padding:13px 28px;border-radius:var(--radius-sm);
    text-decoration:none;border:1px solid var(--border2);
    transition:all .2s;display:inline-flex;align-items:center;gap:6px;
    box-shadow:0 1px 4px rgba(0,0,0,.06);
}
.btn-outline:hover{border-color:var(--gold);color:var(--gold);transform:translateY(-1px);box-shadow:0 4px 16px rgba(201,168,76,.15)}

/* Mockup browser */
.mockup-wrap{
    margin-top:4rem;position:relative;
    max-width:960px;width:100%;
    animation:fadeUp .7s .4s ease both;
}
.mockup-halo{
    position:absolute;top:-60px;left:50%;transform:translateX(-50%);
    width:700px;height:280px;
    background:radial-gradient(ellipse, rgba(201,168,76,.1) 0%, transparent 70%);
    pointer-events:none;
}
.mockup-browser{
    background:var(--dark3);
    border:1px solid var(--border2);
    border-radius:16px;overflow:hidden;
    box-shadow:0 0 0 1px rgba(255,255,255,.04), 0 60px 120px rgba(0,0,0,.7), inset 0 1px 0 rgba(255,255,255,.08);
}
.browser-bar{
    background:var(--dark4);padding:12px 16px;
    display:flex;align-items:center;gap:10px;
    border-bottom:1px solid var(--border);
}
.b-dots{display:flex;gap:6px}
.b-dot{width:11px;height:11px;border-radius:50%}
.url-bar{
    flex:1;margin:0 12px;
    background:rgba(0,0,0,.3);border:1px solid var(--border);
    border-radius:6px;padding:5px 12px;
    font-size:11px;color:var(--muted2);font-family:monospace;
    display:flex;align-items:center;gap:6px;
}
.url-lock{color:var(--gold);opacity:.6}
.browser-body{
    display:grid;grid-template-columns:190px 1fr;min-height:340px;
}
/* Sidebar */
.b-sidebar{
    background:var(--dark2);
    border-right:1px solid var(--border);
    padding:16px 12px;
    display:flex;flex-direction:column;gap:2px;
}
.b-brand{
    font-family:'Syne',sans-serif;font-size:13px;font-weight:800;
    color:var(--gold);padding:4px 8px;margin-bottom:16px;
    display:flex;align-items:center;gap:6px;
}
.b-brand-dot{width:8px;height:8px;background:var(--gold);border-radius:2px}
.b-nav-item{
    padding:8px 10px;border-radius:7px;
    font-size:11.5px;display:flex;align-items:center;gap:8px;
    color:var(--muted);cursor:default;transition:all .15s;
}
.b-nav-item.active{background:rgba(201,168,76,.1);color:var(--gold);font-weight:600}
.b-nav-item svg{flex-shrink:0;opacity:.7}
.b-nav-item.active svg{opacity:1}
/* Main content */
.b-main{padding:20px;display:flex;flex-direction:column;gap:14px;background:var(--dark2)}
.b-page-title{font-family:'Syne',sans-serif;font-size:14px;font-weight:700;color:var(--text);margin-bottom:2px}
.b-page-sub{font-size:11px;color:var(--muted)}
.b-kpis{display:grid;grid-template-columns:repeat(4,1fr);gap:10px}
.b-kpi{
    background:var(--dark3);border:1px solid var(--border);
    border-radius:10px;padding:12px;
    border-top:2px solid;
}
.b-kpi-label{font-size:9.5px;color:var(--muted2);text-transform:uppercase;letter-spacing:.6px;margin-bottom:6px}
.b-kpi-val{font-family:'Syne',sans-serif;font-size:18px;font-weight:800;line-height:1}
.b-kpi-sub{font-size:9px;color:var(--muted2);margin-top:4px}
.b-table{background:var(--dark3);border:1px solid var(--border);border-radius:10px;overflow:hidden}
.b-table-head{
    display:grid;grid-template-columns:1.2fr 1.4fr .8fr .7fr 80px;
    padding:8px 14px;background:rgba(255,255,255,.02);
    border-bottom:1px solid var(--border);
    font-size:9px;color:var(--muted2);text-transform:uppercase;letter-spacing:.7px;
}
.b-table-row{
    display:grid;grid-template-columns:1.2fr 1.4fr .8fr .7fr 80px;
    padding:10px 14px;border-bottom:1px solid rgba(255,255,255,.03);
    font-size:10.5px;color:var(--muted);align-items:center;
}
.b-table-row:last-child{border-bottom:none}
.b-badge{
    display:inline-block;font-size:9px;font-weight:600;
    padding:2px 8px;border-radius:99px;
}
.b-badge.paid{background:rgba(34,197,94,.12);color:#4ade80}
.b-badge.pending{background:rgba(234,179,8,.1);color:#fbbf24}
.b-badge.late{background:rgba(239,68,68,.1);color:#f87171}

/* ─── TRUST BAR ─────────────────────────────────────────── */
.trust-bar{
    padding:2.5rem 5%;
    border-top:1px solid var(--border);
    border-bottom:1px solid var(--border);
    background:var(--bg3);
}
.trust-inner{
    max-width:1000px;margin:0 auto;
    display:flex;align-items:center;gap:3rem;
    flex-wrap:wrap;justify-content:center;
}
.trust-label{font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:1.5px;white-space:nowrap;font-weight:600}
.trust-divider{width:1px;height:28px;background:var(--border2);flex-shrink:0}
.trust-stats{
    display:flex;gap:3rem;flex-wrap:wrap;
    justify-content:center;
}
.trust-stat{text-align:center}
.trust-num{
    font-family:'Syne',sans-serif;font-size:22px;font-weight:800;
    background:linear-gradient(135deg, var(--gold), var(--gold-light));
    -webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;
    line-height:1;
}
.trust-desc{font-size:11px;color:var(--muted);margin-top:4px}

/* ─── SECTIONS COMMUNES ──────────────────────────────────── */
.section{padding:6rem 5%}
.section-inner{max-width:1040px;margin:0 auto}
.section-header{margin-bottom:3.5rem}
.section-tag{
    display:inline-flex;align-items:center;gap:6px;
    font-size:11px;color:var(--gold);font-weight:600;
    letter-spacing:2px;text-transform:uppercase;margin-bottom:1rem;
}
.section-tag::before{content:'';width:16px;height:1px;background:var(--gold)}
h2{
    font-family:'Syne',sans-serif;
    font-size:clamp(28px,4vw,46px);
    font-weight:800;letter-spacing:-1.5px;line-height:1.08;
    margin-bottom:1rem;
}
h2 em{
    font-style:normal;
    background:linear-gradient(135deg, var(--gold), var(--gold-light));
    -webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;
}
.section-sub{
    font-size:15px;color:var(--muted);
    line-height:1.75;max-width:540px;font-weight:400;
}

/* ─── FEATURES ──────────────────────────────────────────── */
.features-grid{
    display:grid;
    grid-template-columns:repeat(3, 1fr);
    gap:16px;
    border-radius:var(--radius);
    overflow:hidden;
}
.feat{
    background:var(--card-bg);
    border:1px solid var(--card-border);
    border-radius:var(--radius);
    padding:2rem 1.75rem;
    transition:all .22s cubic-bezier(.4,0,.2,1);
    position:relative;
    overflow:hidden;
    box-shadow:var(--card-shadow);
}
.feat::after{
    content:'';
    position:absolute;inset:0;
    background:linear-gradient(135deg, rgba(201,168,76,.05) 0%, transparent 55%);
    opacity:0;transition:opacity .3s;pointer-events:none;
}
.feat:hover{transform:translateY(-4px);box-shadow:var(--card-shadow-hover);border-color:rgba(201,168,76,.3)}
.feat:hover::after{opacity:1}
.feat-icon{
    width:44px;height:44px;
    background:rgba(201,168,76,.1);
    border:1px solid rgba(201,168,76,.18);
    border-radius:12px;
    display:flex;align-items:center;justify-content:center;
    margin-bottom:1.25rem;
}
.feat-title{
    font-family:'Syne',sans-serif;
    font-size:15px;font-weight:700;
    margin-bottom:.5rem;color:var(--text);
}
.feat-desc{font-size:13px;color:var(--muted);line-height:1.7}

/* ─── TESTIMONIALS ──────────────────────────────────────── */
.testimonials-section{
    padding:6rem 5%;
    background:var(--bg2);
    border-top:1px solid var(--border);
    border-bottom:1px solid var(--border);
}
.testi-grid{
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:1.25rem;
    margin-top:3rem;
}
.testi-card{
    background:var(--card-bg);
    border:1px solid var(--card-border);
    border-radius:var(--radius);
    padding:1.75rem;
    position:relative;
    transition:all .22s cubic-bezier(.4,0,.2,1);
    box-shadow:var(--card-shadow);
}
.testi-card:hover{border-color:rgba(201,168,76,.3);transform:translateY(-4px);box-shadow:var(--card-shadow-hover)}
.testi-quote{
    font-size:32px;color:var(--gold);
    opacity:.4;line-height:1;
    margin-bottom:1rem;
    font-family:Georgia,serif;
}
.testi-text{
    font-size:13.5px;color:var(--text2);
    line-height:1.75;margin-bottom:1.5rem;
    font-weight:400;font-style:italic;
}
.testi-author{display:flex;align-items:center;gap:10px}
.testi-avatar{
    width:38px;height:38px;border-radius:50%;
    background:linear-gradient(135deg, var(--gold), #8a6e2f);
    display:flex;align-items:center;justify-content:center;
    font-family:'Syne',sans-serif;font-size:14px;font-weight:700;color:#080c12;
    flex-shrink:0;
}
.testi-name{font-family:'Syne',sans-serif;font-size:13px;font-weight:700;color:var(--text)}
.testi-role{font-size:11px;color:var(--muted)}
.testi-stars{
    display:flex;gap:2px;margin-top:.75rem;
    color:var(--gold);font-size:12px;
}

/* ─── COMPLIANCE ────────────────────────────────────────── */
.compliance-card{
    background:var(--card-bg);
    border:1px solid var(--card-border);
    border-radius:20px;padding:3rem;
    position:relative;overflow:hidden;margin-top:3rem;
    box-shadow:var(--card-shadow);
}
.compliance-card::before{
    content:'';position:absolute;
    top:-80px;right:-80px;
    width:350px;height:350px;
    background:radial-gradient(circle, rgba(201,168,76,.06) 0%, transparent 70%);
    pointer-events:none;
}
.compliance-card::after{
    content:'';position:absolute;
    bottom:-60px;left:-60px;
    width:250px;height:250px;
    background:radial-gradient(circle, rgba(201,168,76,.04) 0%, transparent 70%);
    pointer-events:none;
}
.comp-grid{
    display:grid;grid-template-columns:1fr 1fr;
    gap:1.75rem;position:relative;z-index:1;
}
.comp-item{display:flex;gap:14px;align-items:flex-start}
.comp-check{
    width:24px;height:24px;
    background:rgba(201,168,76,.12);
    border:1px solid rgba(201,168,76,.2);
    border-radius:7px;
    display:flex;align-items:center;justify-content:center;
    flex-shrink:0;margin-top:1px;
}
.comp-title{font-size:14px;font-weight:600;color:var(--text);margin-bottom:4px}
.comp-desc{font-size:12.5px;color:var(--muted);line-height:1.6}

/* ─── STEPS ─────────────────────────────────────────────── */
.steps-grid{
    display:grid;grid-template-columns:repeat(3,1fr);gap:2rem;
    margin-top:3rem;position:relative;
}
.steps-line{
    position:absolute;top:27px;
    left:calc(16.7% + 12px);right:calc(16.7% + 12px);
    height:1px;
    background:linear-gradient(90deg, var(--gold) 0%, rgba(201,168,76,.15) 100%);
    z-index:0;
}
.step{text-align:center;position:relative;z-index:1}
.step-num{
    width:54px;height:54px;
    background:linear-gradient(135deg, var(--gold), var(--gold-light));
    color:#080c12;
    border-radius:50%;
    display:flex;align-items:center;justify-content:center;
    font-family:'Syne',sans-serif;font-size:18px;font-weight:800;
    margin:0 auto 1.25rem;
    box-shadow:0 8px 24px rgba(201,168,76,.3);
}
.step-title{font-family:'Syne',sans-serif;font-size:15px;font-weight:700;margin-bottom:.5rem;color:var(--text)}
.step-desc{font-size:13px;color:var(--muted);line-height:1.65}

/* ─── PRICING ────────────────────────────────────────────── */
.pricing-section{
    padding:6rem 5%;
    background:var(--bg2);
    border-top:1px solid var(--border);
}
/* Billing toggle */
.billing-toggle{
    display:inline-flex;
    background:#fff;
    border:1px solid var(--border2);
    border-radius:10px;
    padding:4px;gap:2px;
    margin:2rem auto 0;
    flex-wrap:wrap;
    justify-content:center;
    box-shadow:0 1px 4px rgba(0,0,0,.06);
}
.billing-btn{
    padding:8px 18px;
    border-radius:7px;
    border:none;cursor:pointer;
    font-family:'DM Sans',sans-serif;font-size:13px;font-weight:500;
    color:var(--muted);background:transparent;
    transition:all .2s;white-space:nowrap;
    display:flex;align-items:center;gap:6px;
}
.billing-btn.active{
    background:var(--dark2);color:#fff;
    border:1px solid transparent;
    box-shadow:0 2px 8px rgba(0,0,0,.15);
}
.billing-badge{
    background:rgba(34,197,94,.15);
    color:#4ade80;
    font-size:10px;font-weight:700;
    padding:1px 6px;border-radius:99px;
}
/* Plans grid */
.plans-grid{
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:1.25rem;
    margin-top:2.5rem;
}
.plan{
    background:var(--card-bg);
    border:1px solid var(--card-border);
    border-radius:var(--radius);
    padding:2rem;
    transition:all .22s cubic-bezier(.4,0,.2,1);
    position:relative;
    display:flex;flex-direction:column;
    box-shadow:var(--card-shadow);
}
.plan:hover{border-color:rgba(201,168,76,.32);transform:translateY(-4px);box-shadow:var(--card-shadow-hover)}
.plan.featured{
    border-color:var(--gold);
    background:linear-gradient(160deg, #fffae8 0%, #fffef5 40%, #ffffff 100%);
    box-shadow:0 0 0 1px rgba(201,168,76,.2), 0 16px 48px rgba(201,168,76,.16);
}
.plan-badge-top{
    position:absolute;top:-13px;left:50%;transform:translateX(-50%);
    background:linear-gradient(135deg, var(--gold), var(--gold-light));
    color:#080c12;
    font-size:11px;font-weight:700;font-family:'DM Sans',sans-serif;
    padding:4px 16px;border-radius:99px;white-space:nowrap;
    box-shadow:0 4px 12px rgba(201,168,76,.3);
}
.plan-name{
    font-family:'Syne',sans-serif;
    font-size:11px;font-weight:700;
    color:var(--muted);
    text-transform:uppercase;letter-spacing:1.5px;
    margin-bottom:.75rem;
}
.plan-price-wrap{margin-bottom:.5rem}
.plan-price{
    font-family:'Syne',sans-serif;
    font-size:36px;font-weight:800;color:var(--text);
    line-height:1;display:flex;align-items:baseline;gap:6px;
}
.plan-price .currency{font-size:14px;color:var(--muted);font-weight:400}
.plan-price .period{font-size:13px;color:var(--muted);font-weight:400}
.plan-price-alt{font-size:12px;color:var(--muted);margin-top:4px;min-height:18px}
.plan-economy{
    display:inline-block;
    background:rgba(22,163,74,.1);color:#16a34a;
    font-size:10px;font-weight:700;
    padding:2px 7px;border-radius:99px;
    margin-top:6px;
}
.plan-desc{
    font-size:13px;color:var(--muted);
    margin:1rem 0 1.5rem;line-height:1.65;
}
.plan-features{
    list-style:none;display:flex;flex-direction:column;gap:9px;
    margin-bottom:2rem;flex:1;
}
.plan-features li{
    font-size:13px;color:var(--text2);
    display:flex;gap:9px;align-items:flex-start;
}
.plan-features li .check{
    width:16px;height:16px;
    background:rgba(201,168,76,.12);
    border-radius:4px;
    display:flex;align-items:center;justify-content:center;
    flex-shrink:0;margin-top:1px;
}
.plan-features li.muted-feat{color:var(--muted)}
.plan-btn{
    display:block;text-align:center;
    padding:12px;border-radius:var(--radius-sm);
    font-family:'DM Sans',sans-serif;font-size:13.5px;font-weight:600;
    text-decoration:none;transition:all .2s;
    margin-top:auto;
}
.plan-btn-ghost{
    border:1px solid var(--border2);color:var(--text);
}
.plan-btn-ghost:hover{border-color:var(--gold);color:var(--gold);background:rgba(201,168,76,.04)}
.plan-btn-gold{
    background:linear-gradient(135deg, var(--gold), var(--gold-light));
    color:#080c12;font-weight:700;
    box-shadow:0 4px 16px rgba(201,168,76,.25);
}
.plan-btn-gold:hover{opacity:.9;transform:translateY(-1px);box-shadow:0 6px 24px rgba(201,168,76,.35)}

/* ─── CTA FINAL ─────────────────────────────────────────── */
.cta-section{
    padding:7rem 5%;text-align:center;
    background:var(--dark);
    border-top:1px solid var(--border);
    position:relative;overflow:hidden;
}
.cta-section::before{
    content:'';position:absolute;top:50%;left:50%;
    transform:translate(-50%,-50%);
    width:800px;height:400px;
    background:radial-gradient(ellipse, rgba(201,168,76,.06) 0%, transparent 70%);
    pointer-events:none;
}
.cta-inner{position:relative;z-index:1;max-width:600px;margin:0 auto}
.cta-inner h2{letter-spacing:-2px}
.cta-sub{
    font-size:16px;color:var(--muted);
    max-width:460px;margin:.75rem auto 2.5rem;
    line-height:1.75;font-weight:300;
}
.cta-meta{
    margin-top:1.5rem;
    display:flex;gap:1.5rem;justify-content:center;flex-wrap:wrap;
    font-size:12.5px;color:var(--muted2);
}
.cta-meta span{display:flex;align-items:center;gap:5px}
.cta-meta .dot{width:4px;height:4px;background:var(--gold);border-radius:50%;opacity:.5}

/* ─── FOOTER ────────────────────────────────────────────── */
footer{
    background:var(--dark2);
    border-top:1px solid var(--border);
    padding:3.5rem 5% 2rem;
}
.footer-top{
    display:grid;
    grid-template-columns:1.5fr repeat(3,1fr);
    gap:3rem;
    max-width:1040px;margin:0 auto;
    padding-bottom:3rem;
    border-bottom:1px solid var(--border);
}
.footer-brand{}
.footer-logo{
    font-family:'Syne',sans-serif;font-size:20px;font-weight:800;
    color:var(--gold);letter-spacing:-.5px;margin-bottom:.75rem;
    display:inline-block;
}
.footer-logo span{color:var(--text)}
.footer-tagline{font-size:13px;color:var(--muted);line-height:1.6;max-width:220px;margin-bottom:1.5rem}
.footer-badge{
    display:inline-flex;align-items:center;gap:6px;
    background:rgba(201,168,76,.07);border:1px solid rgba(201,168,76,.15);
    border-radius:6px;padding:5px 10px;
    font-size:11px;color:var(--muted);
}
.footer-col-title{
    font-family:'Syne',sans-serif;font-size:12px;font-weight:700;
    color:var(--text2);text-transform:uppercase;letter-spacing:1px;
    margin-bottom:1rem;
}
.footer-col a{
    display:block;font-size:13px;color:var(--muted);
    text-decoration:none;margin-bottom:.6rem;
    transition:color .15s;
}
.footer-col a:hover{color:var(--text)}
.footer-bottom{
    max-width:1040px;margin:2rem auto 0;
    display:flex;justify-content:space-between;align-items:center;
    flex-wrap:wrap;gap:1rem;
}
.footer-copy{font-size:12px;color:var(--muted2)}
.footer-legal{display:flex;gap:1.5rem}
.footer-legal a{font-size:12px;color:var(--muted2);text-decoration:none;transition:color .15s}
.footer-legal a:hover{color:var(--muted)}

/* ─── ANIMATIONS ─────────────────────────────────────────── */
@keyframes fadeUp{
    from{opacity:0;transform:translateY(22px)}
    to{opacity:1;transform:translateY(0)}
}

/* ─── RESPONSIVE ─────────────────────────────────────────── */
@media(max-width:900px){
    .features-grid{grid-template-columns:repeat(2,1fr)}
    .testi-grid{grid-template-columns:1fr 1fr}
    .plans-grid{grid-template-columns:1fr}
    .browser-body{grid-template-columns:1fr}
    .b-sidebar{display:none}
    .b-kpis{grid-template-columns:repeat(2,1fr)}
    .comp-grid{grid-template-columns:1fr}
    .footer-top{grid-template-columns:1fr 1fr}
}
@media(max-width:640px){
    .hero h1{letter-spacing:-1.5px}
    .features-grid{grid-template-columns:1fr}
    .testi-grid{grid-template-columns:1fr}
    .steps-grid{grid-template-columns:1fr;gap:1.5rem}
    .steps-line{display:none}
    .b-kpis{grid-template-columns:repeat(2,1fr)}
    .b-table-head .b-col3,.b-table-row .b-col3{display:none}
    .trust-stats{gap:1.5rem}
    .footer-top{grid-template-columns:1fr}
    .footer-bottom{flex-direction:column;text-align:center}
    .cta-meta{flex-direction:column;align-items:center;gap:.75rem}
    .billing-toggle{width:100%}
    .billing-btn{flex:1;justify-content:center;font-size:12px;padding:7px 10px}
}
</style>
</head>
<body>

@include('partials.public-nav', ['active' => ''])

<!-- ─── HERO ───────────────────────────────────────────── -->
<section class="hero">
<div class="hero-inner">
    <div class="hero-badge">
        <div class="pulse-dot"></div>
        🇸🇳 Conçu pour le marché sénégalais
    </div>

    <h1>Gérez votre agence<br>immobilière, <em>l'esprit<br>tranquille</em></h1>

    <p class="hero-sub">
        Biens, contrats, loyers, quittances et conformité fiscale —
        tout en un seul outil. Conforme TVA 18%, NINEA, loi 81-18 et CGI article 357.
    </p>

    <div class="hero-btns">
        <a href="{{ route('agency.register') }}" class="btn-gold">
            Créer mon agence gratuitement
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
        </a>
        <a href="{{ route('demo') }}" class="btn-outline">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="5 3 19 12 5 21 5 3"/></svg>
            Demander une démo
        </a>
    </div>
    <p class="hero-note">30 jours d'essai gratuit · <span>Aucune carte bancaire requise</span> · Accès immédiat</p>

    {{-- Mockup navigateur ──────────────────────────── --}}
    <div class="mockup-wrap">
        <div class="mockup-halo"></div>
        <div class="mockup-browser">
            <div class="browser-bar">
                <div class="b-dots">
                    <div class="b-dot" style="background:#ff5f57"></div>
                    <div class="b-dot" style="background:#febc2e"></div>
                    <div class="b-dot" style="background:#28c840"></div>
                </div>
                <div class="url-bar">
                    <span class="url-lock">
                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    </span>
                    app.bimotech.sn/dashboard
                </div>
            </div>
            <div class="browser-body">
                <div class="b-sidebar">
                    <div class="b-brand"><div class="b-brand-dot"></div>BimoTech</div>
                    <div class="b-nav-item active">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                        Dashboard
                    </div>
                    <div class="b-nav-item">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
                        Biens
                    </div>
                    <div class="b-nav-item">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/></svg>
                        Contrats
                    </div>
                    <div class="b-nav-item">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                        Paiements
                    </div>
                    <div class="b-nav-item">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                        Rapports
                    </div>
                    <div class="b-nav-item">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                        Locataires
                    </div>
                </div>
                <div class="b-main">
                    <div>
                        <div class="b-page-title">Tableau de bord</div>
                        <div class="b-page-sub">Agence Fann Immo · Avril 2026</div>
                    </div>
                    <div class="b-kpis">
                        <div class="b-kpi" style="border-color:var(--gold)">
                            <div class="b-kpi-label">Loyers encaissés</div>
                            <div class="b-kpi-val" style="color:var(--gold)">3,2M</div>
                            <div class="b-kpi-sub">FCFA ce mois</div>
                        </div>
                        <div class="b-kpi" style="border-color:#4ade80">
                            <div class="b-kpi-label">Biens occupés</div>
                            <div class="b-kpi-val" style="color:#4ade80">18/20</div>
                            <div class="b-kpi-sub">90% d'occupation</div>
                        </div>
                        <div class="b-kpi" style="border-color:#f87171">
                            <div class="b-kpi-label">Impayés</div>
                            <div class="b-kpi-val" style="color:#f87171">2</div>
                            <div class="b-kpi-sub">Relances envoyées</div>
                        </div>
                        <div class="b-kpi" style="border-color:#a78bfa">
                            <div class="b-kpi-label">Net propriétaires</div>
                            <div class="b-kpi-val" style="color:#a78bfa">2,7M</div>
                            <div class="b-kpi-sub">Après commission</div>
                        </div>
                    </div>
                    <div class="b-table">
                        <div class="b-table-head">
                            <span>Locataire</span>
                            <span>Bien</span>
                            <span>Montant</span>
                            <span>Mode</span>
                            <span>Statut</span>
                        </div>
                        <div class="b-table-row">
                            <span>A. Diallo</span>
                            <span>Villa Fann</span>
                            <span>350 000</span>
                            <span>Wave</span>
                            <span><span class="b-badge paid">Payé</span></span>
                        </div>
                        <div class="b-table-row">
                            <span>M. Ndiaye</span>
                            <span>Appt Plateau</span>
                            <span>280 000</span>
                            <span>Virement</span>
                            <span><span class="b-badge pending">Attente</span></span>
                        </div>
                        <div class="b-table-row">
                            <span>F. Sow</span>
                            <span>Bureau Almadies</span>
                            <span>520 000</span>
                            <span>Chèque</span>
                            <span><span class="b-badge paid">Payé</span></span>
                        </div>
                        <div class="b-table-row">
                            <span>I. Ba</span>
                            <span>Studio Mermoz</span>
                            <span>185 000</span>
                            <span>OM</span>
                            <span><span class="b-badge late">Retard</span></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</section>

<!-- ─── TRUST BAR ──────────────────────────────────────── -->
<div class="trust-bar">
    <div class="trust-inner">
        <div class="trust-label">Pourquoi BimoTech</div>
        <div class="trust-divider"></div>
        <div class="trust-stats">
            <div class="trust-stat">
                <div class="trust-num" style="font-size:15px;letter-spacing:-.3px">Dakar · Thiès · Régions</div>
                <div class="trust-desc">Agences partout au Sénégal</div>
            </div>
            <div class="trust-stat">
                <div class="trust-num" style="font-size:13px;letter-spacing:-.3px">Wave · Orange Money · Virement</div>
                <div class="trust-desc">Tous modes de paiement inclus</div>
            </div>
            <div class="trust-stat">
                <div class="trust-num">98%</div>
                <div class="trust-desc">Taux de recouvrement</div>
            </div>
            <div class="trust-stat">
                <div class="trust-num" style="font-size:13px;letter-spacing:-.3px">TVA · BRS · TOM · DGID</div>
                <div class="trust-desc">Conformité fiscale CGI SN</div>
            </div>
        </div>
    </div>
</div>

<!-- ─── VIDÉO DÉMO ─────────────────────────────────────── -->
<section class="section" id="demo" style="padding:5rem 5%">
    <div class="section-inner" style="max-width:860px">
        <div class="section-header" style="text-align:center">
            <div class="section-tag" style="justify-content:center">Démonstration</div>
            <h2 style="font-size:clamp(1.6rem,3vw,2.2rem);margin-bottom:.8rem">Voyez BimoTech en action</h2>
            <p class="section-sub" style="max-width:520px;margin:0 auto">De la création du contrat à la quittance PDF — regardez comment une agence gère son portefeuille en moins de 5 minutes.</p>
        </div>

        {{-- Conteneur vidéo avec fallback --}}
        <div style="position:relative;border-radius:18px;overflow:hidden;border:1px solid rgba(201,168,76,.2);box-shadow:0 0 60px rgba(201,168,76,.07);background:#0d1117;margin-top:2.5rem">

            {{-- 🔧 REMPLACEZ l'URL ci-dessous par votre lien YouTube ou vidéo MP4 --}}
            @php $videoUrl = ''; /* ex: https://www.youtube.com/embed/XXXXXXX */ @endphp

            @if($videoUrl)
            <div style="position:relative;padding-bottom:56.25%;height:0">
                <iframe src="{{ $videoUrl }}"
                    style="position:absolute;top:0;left:0;width:100%;height:100%;border:0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen></iframe>
            </div>
            @else
            {{-- Placeholder affiché tant qu'aucune vidéo n'est configurée --}}
            <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;padding:5rem 2rem;gap:1.5rem">
                <div style="width:72px;height:72px;border-radius:50%;background:rgba(201,168,76,.12);border:1.5px solid rgba(201,168,76,.3);display:flex;align-items:center;justify-content:center;cursor:pointer"
                     onclick="this.closest('section').querySelector('a').click()">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="#c9a84c" stroke="none"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                </div>
                <div style="text-align:center">
                    <div style="font-family:'Syne',sans-serif;font-size:16px;font-weight:700;color:#e6edf3;margin-bottom:6px">Démo disponible sur demande</div>
                    <div style="font-size:13px;color:#6e7681">Obtenez une démonstration personnalisée avec un expert BimoTech</div>
                </div>
                <a href="{{ route('demo') }}"
                   style="display:inline-flex;align-items:center;gap:8px;padding:11px 24px;background:rgba(201,168,76,.1);border:1px solid rgba(201,168,76,.3);border-radius:10px;color:#c9a84c;font-size:13px;font-weight:600;text-decoration:none;transition:all .15s"
                   onmouseover="this.style.background='rgba(201,168,76,.18)'"
                   onmouseout="this.style.background='rgba(201,168,76,.1)'">
                    Demander une démo gratuite →
                </a>
            </div>
            @endif
        </div>
    </div>
</section>

<!-- ─── FONCTIONNALITÉS ───────────────────────────────── -->
<section class="section" id="fonctionnalites">
    <div class="section-inner">
        <div class="section-header">
            <div class="section-tag">Fonctionnalités</div>
            <h2>Tout ce dont votre agence<br>a besoin, <em>rien de superflu</em></h2>
            <p class="section-sub">BimoTech n'est pas un annuaire immobilier. C'est un outil de gestion professionnelle pour les agences qui veulent structurer leur activité.</p>
        </div>

        <div class="features-grid">
            <div class="feat">
                <div class="feat-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#c9a84c" stroke-width="1.8"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9,22 9,12 15,12 15,22"/></svg>
                </div>
                <h3 class="feat-title" style="font-size:15px">Gestion des biens</h3>
                <div class="feat-desc">Ajoutez vos biens avec photos, surface, type et loyer. Suivez l'occupation en temps réel avec des statuts clairs.</div>
            </div>
            <div class="feat">
                <div class="feat-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#c9a84c" stroke-width="1.8"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14,2 14,8 20,8"/></svg>
                </div>
                <h3 class="feat-title" style="font-size:15px">Contrats de bail</h3>
                <div class="feat-desc">Créez vos contrats de bail vide ou meublé. Durée, dépôt de garantie, clauses — tout est archivé et accessible.</div>
            </div>
            <div class="feat">
                <div class="feat-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#c9a84c" stroke-width="1.8"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                </div>
                <h3 class="feat-title" style="font-size:15px">Encaissement des loyers</h3>
                <div class="feat-desc">Espèces, virement, Wave, Orange Money, Free Money. Chaque paiement enregistré génère automatiquement une quittance légale.</div>
            </div>
            <div class="feat">
                <div class="feat-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#c9a84c" stroke-width="1.8"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </div>
                <h3 class="feat-title" style="font-size:15px">Espace propriétaire</h3>
                <div class="feat-desc">Chaque propriétaire accède à ses biens, ses revenus nets et l'historique de ses paiements. En temps réel, sans appel.</div>
            </div>
            <div class="feat">
                <div class="feat-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#c9a84c" stroke-width="1.8"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                </div>
                <h3 class="feat-title" style="font-size:15px">Espace locataire</h3>
                <div class="feat-desc">Vos locataires accèdent à leurs quittances, suivent leur bail et contactent l'agence via WhatsApp en un clic.</div>
            </div>
            <div class="feat">
                <div class="feat-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#c9a84c" stroke-width="1.8"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                </div>
                <h3 class="feat-title" style="font-size:15px">Rapports financiers</h3>
                <div class="feat-desc">Commission HT/TTC, TVA, TOM, BRS, net propriétaire. Bilans mensuels et annuels exportables avec taux d'occupation.</div>
            </div>
        </div>
    </div>
</section>

<!-- ─── TÉMOIGNAGES ───────────────────────────────────── -->
<div class="testimonials-section">
    <div class="section-inner">
        <div class="section-header">
            <div class="section-tag">Témoignages</div>
            <h2>Ce que disent les agences<br>qui utilisent <em>BimoTech</em></h2>
        </div>
        <div class="testi-grid">
            <div class="testi-card">
                <div class="testi-quote">"</div>
                <p class="testi-text">Avant BimoTech, je passais mes lundis à relancer les locataires par téléphone. Maintenant les quittances partent automatiquement et mes propriétaires consultent leurs revenus eux-mêmes. Je récupère deux heures par semaine.</p>
                <div class="testi-author">
                    <div class="testi-avatar">AD</div>
                    <div>
                        <div class="testi-name">Aminata Dieng</div>
                        <div class="testi-role">Directrice — Agence Fann Immo, Dakar</div>
                    </div>
                </div>
                <div class="testi-stars">★★★★★</div>
            </div>
            <div class="testi-card">
                <div class="testi-quote">"</div>
                <p class="testi-text">La conformité TVA et TOM est ce qui m'a convaincu. Mon comptable a validé les calculs du premier coup. Je n'avais rien à paramétrer, tout était déjà dans la loi sénégalaise. Aucun autre logiciel ne fait ça.</p>
                <div class="testi-author">
                    <div class="testi-avatar">MF</div>
                    <div>
                        <div class="testi-name">Moustapha Fall</div>
                        <div class="testi-role">Gérant — Cabinet Thioro Patrimoine, Thiès</div>
                    </div>
                </div>
                <div class="testi-stars">★★★★★</div>
            </div>
            <div class="testi-card">
                <div class="testi-quote">"</div>
                <p class="testi-text">On gère 34 appartements à Almadies avec 2 personnes. BimoTech nous a permis de doubler notre portefeuille sans embaucher. Le tableau de bord suffit pour voir en 30 secondes ce qui est payé et ce qui ne l'est pas.</p>
                <div class="testi-author">
                    <div class="testi-avatar">SC</div>
                    <div>
                        <div class="testi-name">Sokhna Cissé</div>
                        <div class="testi-role">Associée — Immo Almadies, Dakar</div>
                    </div>
                </div>
                <div class="testi-stars">★★★★★</div>
            </div>
        </div>
    </div>
</div>

<!-- ─── CONFORMITÉ ─────────────────────────────────────── -->
<section class="section" id="conformite">
    <div class="section-inner">
        <div class="section-header">
            <div class="section-tag">Conformité fiscale</div>
            <h2>Le seul SaaS immobilier<br><em>conforme au droit sénégalais</em></h2>
            <p class="section-sub">BimoTech intègre nativement les obligations fiscales et légales du Sénégal. Pas de modules à activer, pas de configuration complexe.</p>
        </div>

        <div class="compliance-card">
            <div class="comp-grid">
                <div class="comp-item">
                    <div class="comp-check">
                        <svg width="11" height="11" viewBox="0 0 12 12" fill="none" stroke="#c9a84c" stroke-width="2.5"><polyline points="2,6 5,9 10,3"/></svg>
                    </div>
                    <div>
                        <div class="comp-title">TVA 18% — CGI article 357</div>
                        <div class="comp-desc">Calcul automatique sur les commissions d'agence, décomposition HT/TTC claire sur chaque quittance.</div>
                    </div>
                </div>
                <div class="comp-item">
                    <div class="comp-check">
                        <svg width="11" height="11" viewBox="0 0 12 12" fill="none" stroke="#c9a84c" stroke-width="2.5"><polyline points="2,6 5,9 10,3"/></svg>
                    </div>
                    <div>
                        <div class="comp-title">Loi 81-18 — Encadrement des loyers</div>
                        <div class="comp-desc">Vérification automatique des plafonds selon la surface. Alerte si le loyer saisi est non conforme.</div>
                    </div>
                </div>
                <div class="comp-item">
                    <div class="comp-check">
                        <svg width="11" height="11" viewBox="0 0 12 12" fill="none" stroke="#c9a84c" stroke-width="2.5"><polyline points="2,6 5,9 10,3"/></svg>
                    </div>
                    <div>
                        <div class="comp-title">TOM — Taxe sur Ordures Ménagères</div>
                        <div class="comp-desc">Taux paramétrable par commune. Dakar 5%, autres communes selon arrêté local.</div>
                    </div>
                </div>
                <div class="comp-item">
                    <div class="comp-check">
                        <svg width="11" height="11" viewBox="0 0 12 12" fill="none" stroke="#c9a84c" stroke-width="2.5"><polyline points="2,6 5,9 10,3"/></svg>
                    </div>
                    <div>
                        <div class="comp-title">BRS — Contribution Foncière des Propriétés Bâties</div>
                        <div class="comp-desc">Calcul et report automatique de la contribution foncière dans les bilans propriétaires.</div>
                    </div>
                </div>
                <div class="comp-item">
                    <div class="comp-check">
                        <svg width="11" height="11" viewBox="0 0 12 12" fill="none" stroke="#c9a84c" stroke-width="2.5"><polyline points="2,6 5,9 10,3"/></svg>
                    </div>
                    <div>
                        <div class="comp-title">NINEA — Identification fiscale de l'agence</div>
                        <div class="comp-desc">Le NINEA apparaît automatiquement sur toutes les quittances, factures et documents PDF.</div>
                    </div>
                </div>
                <div class="comp-item">
                    <div class="comp-check">
                        <svg width="11" height="11" viewBox="0 0 12 12" fill="none" stroke="#c9a84c" stroke-width="2.5"><polyline points="2,6 5,9 10,3"/></svg>
                    </div>
                    <div>
                        <div class="comp-title">Dépôt de garantie — 2 mois max</div>
                        <div class="comp-desc">Plafonnement automatique au seuil légal loi 81-18, avec avertissement en cas de dépassement.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ─── COMMENT ÇA MARCHE ─────────────────────────────── -->
<section class="section" style="background:var(--dark2);border-top:1px solid var(--border);border-bottom:1px solid var(--border)">
    <div class="section-inner">
        <div class="section-header">
            <div class="section-tag">Démarrage rapide</div>
            <h2>Opérationnel en <em>moins de 10 minutes</em></h2>
        </div>
        <div class="steps-grid">
            <div class="steps-line"></div>
            <div class="step">
                <div class="step-num">1</div>
                <div class="step-title">Créez votre agence</div>
                <div class="step-desc">Renseignez nom, NINEA et coordonnées. Votre espace est immédiatement actif, sécurisé et isolé.</div>
            </div>
            <div class="step">
                <div class="step-num">2</div>
                <div class="step-title">Ajoutez vos biens</div>
                <div class="step-desc">Importez votre portefeuille avec photos, loyers et propriétaires. Créez vos premiers contrats de bail.</div>
            </div>
            <div class="step">
                <div class="step-num">3</div>
                <div class="step-title">Gérez en temps réel</div>
                <div class="step-desc">Paiements, quittances PDF, rapports fiscaux — tout se génère automatiquement depuis votre tableau de bord.</div>
            </div>
        </div>
    </div>
</section>

<!-- ─── TARIFS ─────────────────────────────────────────── -->
<section class="pricing-section" id="tarifs">
    <div class="section-inner">
        <div class="section-header" style="text-align:center">
            <div class="section-tag" style="justify-content:center">Tarifs</div>
            <h2>Simple, transparent,<br><em>sans surprise</em></h2>
            <p class="section-sub" style="margin:0 auto">Tous les plans incluent la conformité fiscale complète.<br>Aucune carte bancaire requise pour démarrer.</p>

            {{-- Billing toggle --}}
            <div style="display:flex;justify-content:center">
                <div class="billing-toggle" id="billing-toggle">
                    <button class="billing-btn active" data-plan="mensuel" onclick="setBilling('mensuel', this)">Mensuel</button>
                    <button class="billing-btn" data-plan="trimestriel" onclick="setBilling('trimestriel', this)">
                        Trimestriel <span class="billing-badge">−10%</span>
                    </button>
                    <button class="billing-btn" data-plan="semestriel" onclick="setBilling('semestriel', this)">
                        Semestriel <span class="billing-badge">−15%</span>
                    </button>
                    <button class="billing-btn" data-plan="annuel" onclick="setBilling('annuel', this)">
                        Annuel <span class="billing-badge">−20%</span>
                    </button>
                </div>
            </div>
        </div>

        <div class="plans-grid">
            {{-- Plan Démarrage --}}
            <div class="plan">
                <div class="plan-name">Démarrage</div>
                <div class="plan-price-wrap">
                    <div class="plan-price">Gratuit <span class="period">/ toujours</span></div>
                    <div class="plan-price-alt">&nbsp;</div>
                </div>
                <p class="plan-desc">Pour découvrir BimoTech et gérer vos premiers biens sans engagement ni limite de durée.</p>
                <ul class="plan-features">
                    <li><span class="check"><svg width="8" height="8" viewBox="0 0 12 12" fill="none" stroke="#c9a84c" stroke-width="2.5"><polyline points="2,6 5,9 10,3"/></svg></span>Jusqu'à 5 biens</li>
                    <li><span class="check"><svg width="8" height="8" viewBox="0 0 12 12" fill="none" stroke="#c9a84c" stroke-width="2.5"><polyline points="2,6 5,9 10,3"/></svg></span>2 utilisateurs</li>
                    <li><span class="check"><svg width="8" height="8" viewBox="0 0 12 12" fill="none" stroke="#c9a84c" stroke-width="2.5"><polyline points="2,6 5,9 10,3"/></svg></span>Quittances PDF</li>
                    <li><span class="check"><svg width="8" height="8" viewBox="0 0 12 12" fill="none" stroke="#c9a84c" stroke-width="2.5"><polyline points="2,6 5,9 10,3"/></svg></span>Conformité fiscale SN</li>
                    <li class="muted-feat"><span class="check" style="background:rgba(255,255,255,.04);"><svg width="8" height="8" viewBox="0 0 12 12" fill="none" stroke="#6e7681" stroke-width="2"><line x1="3" y1="6" x2="9" y2="6"/></svg></span>Rapports avancés</li>
                    <li class="muted-feat"><span class="check" style="background:rgba(255,255,255,.04);"><svg width="8" height="8" viewBox="0 0 12 12" fill="none" stroke="#6e7681" stroke-width="2"><line x1="3" y1="6" x2="9" y2="6"/></svg></span>Espaces propriétaire & locataire</li>
                </ul>
                <a href="{{ route('agency.register') }}" class="plan-btn plan-btn-ghost">Commencer gratuitement</a>
            </div>

            {{-- Plan Agence (featured) --}}
            <div class="plan featured">
                <div class="plan-badge-top">⭐ Le plus populaire</div>
                <div class="plan-name">Agence</div>
                <div class="plan-price-wrap">
                    <div class="plan-price" id="agence-price">
                        <span id="price-amount">25 000</span>
                        <span class="currency">FCFA</span>
                        <span class="period" id="price-period">/ mois</span>
                    </div>
                    <div class="plan-price-alt" id="price-alt">&nbsp;</div>
                    <div id="price-economy" style="display:none" class="plan-economy"></div>
                </div>
                <p class="plan-desc">Pour les agences qui veulent gérer leur activité sans limite, sans friction et en toute conformité.</p>
                <ul class="plan-features">
                    <li><span class="check"><svg width="8" height="8" viewBox="0 0 12 12" fill="none" stroke="#c9a84c" stroke-width="2.5"><polyline points="2,6 5,9 10,3"/></svg></span>Biens illimités</li>
                    <li><span class="check"><svg width="8" height="8" viewBox="0 0 12 12" fill="none" stroke="#c9a84c" stroke-width="2.5"><polyline points="2,6 5,9 10,3"/></svg></span>Utilisateurs illimités</li>
                    <li><span class="check"><svg width="8" height="8" viewBox="0 0 12 12" fill="none" stroke="#c9a84c" stroke-width="2.5"><polyline points="2,6 5,9 10,3"/></svg></span>Rapports financiers avancés</li>
                    <li><span class="check"><svg width="8" height="8" viewBox="0 0 12 12" fill="none" stroke="#c9a84c" stroke-width="2.5"><polyline points="2,6 5,9 10,3"/></svg></span>Espace propriétaire & locataire</li>
                    <li><span class="check"><svg width="8" height="8" viewBox="0 0 12 12" fill="none" stroke="#c9a84c" stroke-width="2.5"><polyline points="2,6 5,9 10,3"/></svg></span>Logs d'activité complets</li>
                    <li><span class="check"><svg width="8" height="8" viewBox="0 0 12 12" fill="none" stroke="#c9a84c" stroke-width="2.5"><polyline points="2,6 5,9 10,3"/></svg></span>Support prioritaire WhatsApp</li>
                </ul>
                <a href="{{ route('agency.register') }}" class="plan-btn plan-btn-gold">
                    Démarrer — 30 jours gratuits
                </a>
            </div>

            {{-- Plan Réseau --}}
            <div class="plan">
                <div class="plan-name">Réseau</div>
                <div class="plan-price-wrap">
                    <div class="plan-price">Sur devis</div>
                    <div class="plan-price-alt">&nbsp;</div>
                </div>
                <p class="plan-desc">Pour les groupes immobiliers avec plusieurs agences à gérer depuis un seul tableau de bord.</p>
                <ul class="plan-features">
                    <li><span class="check"><svg width="8" height="8" viewBox="0 0 12 12" fill="none" stroke="#c9a84c" stroke-width="2.5"><polyline points="2,6 5,9 10,3"/></svg></span>Multi-agences</li>
                    <li><span class="check"><svg width="8" height="8" viewBox="0 0 12 12" fill="none" stroke="#c9a84c" stroke-width="2.5"><polyline points="2,6 5,9 10,3"/></svg></span>Tableau de bord groupe</li>
                    <li><span class="check"><svg width="8" height="8" viewBox="0 0 12 12" fill="none" stroke="#c9a84c" stroke-width="2.5"><polyline points="2,6 5,9 10,3"/></svg></span>API & intégrations</li>
                    <li><span class="check"><svg width="8" height="8" viewBox="0 0 12 12" fill="none" stroke="#c9a84c" stroke-width="2.5"><polyline points="2,6 5,9 10,3"/></svg></span>Formation sur site à Dakar</li>
                    <li><span class="check"><svg width="8" height="8" viewBox="0 0 12 12" fill="none" stroke="#c9a84c" stroke-width="2.5"><polyline points="2,6 5,9 10,3"/></svg></span>SLA garanti</li>
                    <li><span class="check"><svg width="8" height="8" viewBox="0 0 12 12" fill="none" stroke="#c9a84c" stroke-width="2.5"><polyline points="2,6 5,9 10,3"/></svg></span>Account manager dédié</li>
                </ul>
                <a href="{{ route('contact') }}" class="plan-btn plan-btn-ghost">Nous contacter</a>
            </div>
        </div>

        {{-- Comparaison durées --}}
        <div style="margin-top:2rem;background:var(--dark3);border:1px solid var(--border);border-radius:var(--radius);overflow:hidden">
            <table style="width:100%;border-collapse:collapse;font-size:13px">
                <thead>
                    <tr style="background:rgba(255,255,255,.02);border-bottom:1px solid var(--border)">
                        <th style="padding:12px 20px;text-align:left;font-size:11px;color:var(--muted2);text-transform:uppercase;letter-spacing:.7px;font-weight:600">Durée</th>
                        <th style="padding:12px 20px;text-align:right;font-size:11px;color:var(--muted2);text-transform:uppercase;letter-spacing:.7px;font-weight:600">Total</th>
                        <th style="padding:12px 20px;text-align:right;font-size:11px;color:var(--muted2);text-transform:uppercase;letter-spacing:.7px;font-weight:600">Équivalent / mois</th>
                        <th style="padding:12px 20px;text-align:center;font-size:11px;color:var(--muted2);text-transform:uppercase;letter-spacing:.7px;font-weight:600">Économie</th>
                    </tr>
                </thead>
                <tbody>
                    <tr style="border-bottom:1px solid rgba(255,255,255,.04)">
                        <td style="padding:13px 20px;color:var(--text2);font-weight:500">Mensuel</td>
                        <td style="padding:13px 20px;text-align:right;color:var(--muted);font-family:'Syne',sans-serif">25 000 FCFA</td>
                        <td style="padding:13px 20px;text-align:right;color:var(--muted);font-family:'Syne',sans-serif">25 000 FCFA</td>
                        <td style="padding:13px 20px;text-align:center"><span style="color:var(--muted2);font-size:12px">—</span></td>
                    </tr>
                    <tr style="border-bottom:1px solid rgba(255,255,255,.04)">
                        <td style="padding:13px 20px;color:var(--text2);font-weight:500">Trimestriel</td>
                        <td style="padding:13px 20px;text-align:right;color:var(--muted);font-family:'Syne',sans-serif">67 500 FCFA</td>
                        <td style="padding:13px 20px;text-align:right;color:var(--gold);font-family:'Syne',sans-serif;font-weight:700">22 500 FCFA</td>
                        <td style="padding:13px 20px;text-align:center"><span style="background:rgba(34,197,94,.1);color:#4ade80;font-size:11px;font-weight:700;padding:2px 10px;border-radius:99px">−10%</span></td>
                    </tr>
                    <tr style="border-bottom:1px solid rgba(255,255,255,.04)">
                        <td style="padding:13px 20px;color:var(--text2);font-weight:500">Semestriel</td>
                        <td style="padding:13px 20px;text-align:right;color:var(--muted);font-family:'Syne',sans-serif">127 500 FCFA</td>
                        <td style="padding:13px 20px;text-align:right;color:var(--gold);font-family:'Syne',sans-serif;font-weight:700">21 250 FCFA</td>
                        <td style="padding:13px 20px;text-align:center"><span style="background:rgba(34,197,94,.1);color:#4ade80;font-size:11px;font-weight:700;padding:2px 10px;border-radius:99px">−15%</span></td>
                    </tr>
                    <tr>
                        <td style="padding:13px 20px;color:var(--text2);font-weight:500">Annuel</td>
                        <td style="padding:13px 20px;text-align:right;color:var(--muted);font-family:'Syne',sans-serif">240 000 FCFA</td>
                        <td style="padding:13px 20px;text-align:right;color:var(--gold);font-family:'Syne',sans-serif;font-weight:700">20 000 FCFA</td>
                        <td style="padding:13px 20px;text-align:center"><span style="background:rgba(34,197,94,.1);color:#4ade80;font-size:11px;font-weight:700;padding:2px 10px;border-radius:99px">−20%</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</section>

<!-- ─── CTA FINAL ──────────────────────────────────────── -->
<div class="cta-section">
    <div class="cta-inner">
        <div class="section-tag" style="justify-content:center">Prêt à vous lancer ?</div>
        <h2>Rejoignez les agences qui<br>gèrent mieux avec <em>BimoTech</em></h2>
        <p class="cta-sub">Votre agence est en ligne en moins de 10 minutes. Aucune installation, aucun engagement.</p>
        <div class="hero-btns">
            <a href="{{ route('agency.register') }}" class="btn-gold">
                Créer mon agence gratuitement →
            </a>
            <a href="{{ route('demo') }}" class="btn-outline">Demander une démo</a>
        </div>
        <div class="cta-meta">
            <span><span class="dot"></span>30 jours d'essai gratuit</span>
            <span><span class="dot"></span>Sans carte bancaire</span>
            <span><span class="dot"></span>Données hébergées au Sénégal</span>
            <span><span class="dot"></span>Support WhatsApp inclus</span>
        </div>
    </div>
</div>

<!-- ─── FOOTER ─────────────────────────────────────────── -->
<footer>
    <div class="footer-top">
        <div class="footer-brand">
            <div class="footer-logo">Bimo<span>Tech</span> Immo</div>
            <p class="footer-tagline">La plateforme de gestion immobilière professionnelle pour les agences sénégalaises.</p>
            <div class="footer-badge">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#c9a84c" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                Conforme CGI · Loi 81-18 · NINEA
            </div>
            {{-- Réseaux sociaux — remplacez # par vos vrais liens --}}
            <div style="display:flex;gap:10px;margin-top:1.2rem">
                {{-- WhatsApp --}}
                <a href="https://wa.me/221781318176" target="_blank" rel="noopener"
                   style="width:34px;height:34px;border-radius:8px;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.08);display:flex;align-items:center;justify-content:center;transition:all .15s"
                   onmouseover="this.style.background='rgba(37,211,102,.15)';this.style.borderColor='rgba(37,211,102,.3)'"
                   onmouseout="this.style.background='rgba(255,255,255,.06)';this.style.borderColor='rgba(255,255,255,.08)'"
                   title="WhatsApp">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="#8b949e"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                </a>
                {{-- Facebook — remplacez # --}}
                <a href="#" target="_blank" rel="noopener"
                   style="width:34px;height:34px;border-radius:8px;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.08);display:flex;align-items:center;justify-content:center;transition:all .15s"
                   onmouseover="this.style.background='rgba(24,119,242,.15)';this.style.borderColor='rgba(24,119,242,.3)'"
                   onmouseout="this.style.background='rgba(255,255,255,.06)';this.style.borderColor='rgba(255,255,255,.08)'"
                   title="Facebook">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="#8b949e"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                </a>
                {{-- Instagram — remplacez # --}}
                <a href="#" target="_blank" rel="noopener"
                   style="width:34px;height:34px;border-radius:8px;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.08);display:flex;align-items:center;justify-content:center;transition:all .15s"
                   onmouseover="this.style.background='rgba(228,64,95,.15)';this.style.borderColor='rgba(228,64,95,.3)'"
                   onmouseout="this.style.background='rgba(255,255,255,.06)';this.style.borderColor='rgba(255,255,255,.08)'"
                   title="Instagram">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="#8b949e"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                </a>
                {{-- LinkedIn — remplacez # --}}
                <a href="#" target="_blank" rel="noopener"
                   style="width:34px;height:34px;border-radius:8px;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.08);display:flex;align-items:center;justify-content:center;transition:all .15s"
                   onmouseover="this.style.background='rgba(10,102,194,.15)';this.style.borderColor='rgba(10,102,194,.3)'"
                   onmouseout="this.style.background='rgba(255,255,255,.06)';this.style.borderColor='rgba(255,255,255,.08)'"
                   title="LinkedIn">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="#8b949e"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                </a>
            </div>
        </div>
        <div class="footer-col">
            <div class="footer-col-title">Produit</div>
            <a href="#fonctionnalites">Fonctionnalités</a>
            <a href="#tarifs">Tarifs</a>
            <a href="{{ route('faq') }}">FAQ</a>
            <a href="{{ route('demo') }}">Demander une démo</a>
        </div>
        <div class="footer-col">
            <div class="footer-col-title">Conformité</div>
            <a href="#conformite">TVA 18% — CGI 357</a>
            <a href="#conformite">Loi 81-18</a>
            <a href="#conformite">Quittances légales</a>
            <a href="#conformite">NINEA & DGID</a>
        </div>
        <div class="footer-col">
            <div class="footer-col-title">Entreprise</div>
            <a href="{{ route('contact') }}">Contact</a>
            <a href="{{ route('login') }}">Connexion</a>
            <a href="{{ route('mentions-legales') }}">Mentions légales</a>
            <a href="{{ route('confidentialite') }}">Confidentialité</a>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="footer-copy">© {{ date('Y') }} BimoTech · Dakar, Sénégal · support@bimotech.sn</div>
        <div class="footer-legal">
            <a href="{{ route('mentions-legales') }}">Mentions légales</a>
            <a href="{{ route('confidentialite') }}">Confidentialité</a>
        </div>
    </div>
</footer>

<script>
// Pricing toggle
const tarifs = {
    mensuel:     { total:'25 000',   mensuel:'25 000',  period:'/ mois',   alt:'',                          economy:null },
    trimestriel: { total:'67 500',   mensuel:'22 500',  period:'/ 3 mois', alt:'soit 22 500 FCFA / mois',   economy:'Économie : 7 500 FCFA / an' },
    semestriel:  { total:'127 500',  mensuel:'21 250',  period:'/ 6 mois', alt:'soit 21 250 FCFA / mois',   economy:'Économie : 22 500 FCFA / an' },
    annuel:      { total:'240 000',  mensuel:'20 000',  period:'/ an',     alt:'soit 20 000 FCFA / mois',   economy:'Économie : 60 000 FCFA vs mensuel' },
};

function setBilling(plan, btn) {
    // Update buttons
    document.querySelectorAll('.billing-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');

    const t = tarifs[plan];

    // Update price display
    document.getElementById('price-amount').textContent = t.total;
    document.getElementById('price-period').textContent = t.period;

    const altEl = document.getElementById('price-alt');
    altEl.textContent = t.alt || '\u00a0';

    const econEl = document.getElementById('price-economy');
    if (t.economy) {
        econEl.textContent = t.economy;
        econEl.style.display = 'inline-block';
    } else {
        econEl.style.display = 'none';
    }
}
</script>

<!-- ─── SCHEMA.ORG JSON-LD ──────────────────────────────── -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "SoftwareApplication",
      "name": "BimoTech Immo",
      "url": "https://immo.bimotechsn.com",
      "description": "Logiciel de gestion immobilière pour agences au Sénégal. Gestion des biens, contrats de bail, loyers, quittances PDF conformes TVA 18%, BRS et CGI SN.",
      "applicationCategory": "BusinessApplication",
      "operatingSystem": "Web",
      "offers": {
        "@type": "Offer",
        "price": "0",
        "priceCurrency": "XOF",
        "description": "Essai gratuit 30 jours"
      },
      "aggregateRating": {
        "@type": "AggregateRating",
        "ratingValue": "4.9",
        "reviewCount": "12"
      }
    },
    {
      "@type": "Organization",
      "name": "BimoTech",
      "url": "https://immo.bimotechsn.com",
      "logo": "https://immo.bimotechsn.com/icons/icon-512.png",
      "contactPoint": {
        "@type": "ContactPoint",
        "telephone": "+221-78-131-81-76",
        "contactType": "customer support",
        "availableLanguage": "French"
      },
      "address": {
        "@type": "PostalAddress",
        "addressLocality": "Dakar",
        "addressCountry": "SN"
      }
    },
    {
      "@type": "FAQPage",
      "mainEntity": [
        {
          "@type": "Question",
          "name": "BimoTech est-il conforme à la loi sénégalaise ?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Oui. BimoTech intègre nativement la TVA 18% (CGI SN Art. 357), le BRS (Art. 196bis), la TOM, et les obligations DGID pour l'enregistrement des baux commerciaux."
          }
        },
        {
          "@type": "Question",
          "name": "Combien coûte BimoTech Immo ?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "BimoTech propose un essai gratuit 30 jours sans carte bancaire. Le plan Agence commence à 25 000 FCFA/mois avec des réductions jusqu'à -20% en annuel."
          }
        },
        {
          "@type": "Question",
          "name": "Puis-je générer des quittances PDF avec BimoTech ?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Oui. BimoTech génère automatiquement des quittances PDF légales pour chaque paiement, avec signature agence, décompte loyer et mentions légales conformes."
          }
        }
      ]
    }
  ]
}
</script>

<!-- ─── BOUTON WHATSAPP FLOTTANT ────────────────────────── -->
<a href="https://wa.me/221781318176?text=Bonjour%2C%20je%20voudrais%20une%20d%C3%A9mo%20de%20BimoTech%20Immo"
   target="_blank" rel="noopener"
   id="wa-btn"
   title="Discuter sur WhatsApp"
   style="
       position:fixed;bottom:28px;right:28px;z-index:999;
       width:56px;height:56px;border-radius:50%;
       background:#25D366;
       display:flex;align-items:center;justify-content:center;
       box-shadow:0 4px 20px rgba(37,211,102,.4);
       text-decoration:none;
       animation:wa-pop .4s cubic-bezier(.34,1.56,.64,1) .8s both;
       transition:transform .15s,box-shadow .15s;
   "
   onmouseover="this.style.transform='scale(1.1)';this.style.boxShadow='0 6px 28px rgba(37,211,102,.55)'"
   onmouseout="this.style.transform='scale(1)';this.style.boxShadow='0 4px 20px rgba(37,211,102,.4)'">
    <svg width="28" height="28" viewBox="0 0 24 24" fill="white">
        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
    </svg>
</a>
<style>
@keyframes wa-pop {
    from { opacity:0; transform:scale(.5) translateY(20px); }
    to   { opacity:1; transform:scale(1) translateY(0); }
}
</style>

</body>
</html>
