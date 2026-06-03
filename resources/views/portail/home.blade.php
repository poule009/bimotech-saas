<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>BimoPortail — Immobilier au Sénégal</title>
<meta name="description" content="Trouvez votre bien idéal au Sénégal. Appartements, villas, studios et bureaux proposés par des agences immobilières agréées.">
<meta property="og:title"       content="BimoPortail — Immobilier au Sénégal">
<meta property="og:description" content="Annonces vérifiées par des agences agréées au Sénégal.">
<meta property="og:type"        content="website">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,600;0,9..144,700;1,9..144,400&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" media="print" onload="this.media='all'">
<noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,600;0,9..144,700;1,9..144,400&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap"></noscript>
<style>
*{box-sizing:border-box;margin:0;padding:0}
:root{
  --bleu:#0D1B26;
  --rouge:#C8302A;
  --or:#C4965A;
  --or-light:#e8c99a;
  --blanc:#F2EDE6;
  --gris:#8E9BAA;
  --gris-clair:#F4F2EE;
  --gris-bord:#E8E4DE;
  --radius:14px;
  --radius-sm:10px;
  --topbar-h:52px;
}
html,body{font-family:'Plus Jakarta Sans',sans-serif;background:var(--blanc);color:var(--bleu);-webkit-font-smoothing:antialiased}

/* ── TOPBAR ── */
.topbar{
  position:sticky;top:0;z-index:200;
  height:var(--topbar-h);
  background:var(--bleu);
  display:flex;align-items:center;
  padding:0 20px;gap:10px;
}
.topbar-logo{
  font-family:'Fraunces',serif;
  font-size:20px;font-weight:700;
  color:#fff;text-decoration:none;
  display:flex;align-items:center;gap:8px;
}
.topbar-logo em{color:var(--or);font-style:normal}
.logo-dot{
  width:8px;height:8px;border-radius:50%;
  background:var(--or);flex-shrink:0;
  animation:pulse-dot 2s ease-in-out infinite;
}
@keyframes pulse-dot{
  0%,100%{transform:scale(1);opacity:1}
  50%{transform:scale(1.45);opacity:.7}
}
.topbar-spacer{flex:1}
.topbar-link{font-size:12px;color:rgba(255,255,255,.65);text-decoration:none}
.topbar-link:hover{color:#fff}

/* ── HERO ── */
.hero{background:var(--bleu);color:#fff}
.hero-content{padding:36px 20px 24px}
.hero-tag{
  display:inline-flex;align-items:center;gap:6px;
  background:rgba(255,255,255,.1);
  border:1px solid rgba(255,255,255,.18);
  border-radius:99px;
  font-size:12px;font-weight:600;
  padding:5px 12px;margin-bottom:18px;
  color:rgba(255,255,255,.9);
}
.hero-h1{
  font-family:'Fraunces',serif;
  font-size:clamp(26px,7vw,40px);
  font-weight:700;line-height:1.2;
  color:#fff;margin-bottom:10px;
}
.hero-sub{
  font-size:14px;color:rgba(255,255,255,.7);
  line-height:1.65;margin-bottom:24px;
}

/* ── SEARCH FLOAT ── */
.search-float{
  background:#fff;
  border-radius:20px 20px 0 0;
  padding:22px 20px 20px;
}
.search-label{
  display:block;
  font-size:11px;font-weight:700;
  text-transform:uppercase;letter-spacing:.6px;
  color:var(--gris);margin-bottom:8px;
}
.search-row{display:flex;gap:8px;margin-bottom:14px}
.search-input{
  flex:1;height:44px;padding:0 14px;
  border:1.5px solid var(--gris-bord);border-radius:var(--radius-sm);
  font-size:14px;font-family:inherit;color:var(--bleu);
  background:#fff;outline:none;
  transition:border-color .15s;
}
.search-input:focus{border-color:var(--or)}
.search-input::placeholder{color:var(--gris)}
.btn-search{
  height:44px;padding:0 18px;
  background:var(--rouge);color:#fff;
  border:none;border-radius:var(--radius-sm);
  font-size:14px;font-weight:700;font-family:inherit;
  cursor:pointer;white-space:nowrap;flex-shrink:0;
  transition:opacity .15s;
}
.btn-search:hover{opacity:.88}

/* type tabs */
.type-tabs{
  display:flex;gap:6px;
  overflow-x:auto;scrollbar-width:none;-webkit-overflow-scrolling:touch;
}
.type-tabs::-webkit-scrollbar{display:none}
.type-tab{
  display:inline-flex;align-items:center;
  height:32px;padding:0 14px;
  border:1.5px solid var(--gris-bord);border-radius:99px;
  background:#fff;color:var(--gris);
  font-size:12px;font-weight:600;
  white-space:nowrap;text-decoration:none;flex-shrink:0;
  transition:border-color .15s,color .15s,background .15s;
}
.type-tab:hover,.type-tab.active{
  background:var(--bleu);color:#fff;border-color:var(--bleu);
}

/* ── STATS ROW ── */
.stats-row{
  display:flex;gap:10px;
  padding:20px 20px 0;
  overflow-x:auto;scrollbar-width:none;-webkit-overflow-scrolling:touch;
}
.stats-row::-webkit-scrollbar{display:none}
.stat-pill{
  display:inline-flex;flex-direction:column;align-items:center;
  background:var(--bleu);color:#fff;
  border-radius:12px;padding:12px 20px;flex-shrink:0;
  min-width:105px;
}
.stat-val{
  font-family:'Fraunces',serif;
  font-size:22px;font-weight:700;line-height:1;
}
.stat-lbl{font-size:10px;color:rgba(255,255,255,.6);margin-top:4px;white-space:nowrap}

/* ── SECTION ── */
.section{padding:28px 0 0}
.section-head{
  display:flex;align-items:center;justify-content:space-between;
  padding:0 20px;margin-bottom:16px;
}
.section-title{
  font-family:'Fraunces',serif;
  font-size:18px;font-weight:700;color:var(--bleu);
}
.section-link{
  font-size:12px;font-weight:600;color:var(--or);
  text-decoration:none;white-space:nowrap;
}
.section-link:hover{text-decoration:underline}

/* ── CAROUSEL CARDS (mc) ── */
.hscroll{
  display:flex;gap:14px;
  overflow-x:auto;padding:0 20px 4px;
  scrollbar-width:none;-webkit-overflow-scrolling:touch;
}
.hscroll::-webkit-scrollbar{display:none}
.mc{
  flex:0 0 180px;border-radius:var(--radius-sm);overflow:hidden;
  background:#fff;border:1px solid var(--gris-bord);
  text-decoration:none;color:var(--bleu);
  display:flex;flex-direction:column;
  transition:box-shadow .2s;
}
.mc:hover{box-shadow:0 6px 20px rgba(13,27,38,.12)}
.mc-photo{
  height:120px;background:var(--gris-clair);
  overflow:hidden;position:relative;
}
.mc-photo img{width:100%;height:100%;object-fit:cover;display:block}
.mc-badge{
  position:absolute;top:7px;left:7px;
  background:rgba(13,27,38,.78);color:#fff;
  font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:.4px;
  padding:3px 7px;border-radius:6px;backdrop-filter:blur(4px);
}
.mc-body{padding:10px 12px;flex:1;display:flex;flex-direction:column;gap:3px}
.mc-prix{font-family:'Fraunces',serif;font-size:14px;font-weight:700;line-height:1}
.mc-prix small{font-size:9px;font-weight:400;color:var(--gris);font-family:'Plus Jakarta Sans',sans-serif}
.mc-titre{
  font-size:11px;font-weight:600;color:var(--bleu);
  white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-top:1px;
}
.mc-loc{
  font-size:10px;color:var(--gris);
  white-space:nowrap;overflow:hidden;text-overflow:ellipsis;
  display:flex;align-items:center;gap:3px;
}
.mc-loc svg{width:10px;height:10px;flex-shrink:0}
.empty-carousel{
  padding:28px 20px;text-align:center;
  font-size:13px;color:var(--gris);
}

/* ── QUARTIER GRID (qc) ── */
.qc-grid{
  display:grid;grid-template-columns:1fr 1fr;
  gap:10px;padding:0 20px;
}
.qc{
  position:relative;overflow:hidden;
  border-radius:var(--radius-sm);
  background:var(--bleu);min-height:110px;
  text-decoration:none;display:flex;
  flex-direction:column;justify-content:flex-end;
  transition:opacity .2s;cursor:pointer;
}
.qc:hover{opacity:.88}
.qc::before{
  content:'';position:absolute;
  width:80px;height:80px;border-radius:50%;
  border:20px solid rgba(196,150,90,.2);
  top:-20px;right:-20px;pointer-events:none;
}
.qc:nth-child(even)::before{top:auto;bottom:-20px;right:-10px}
.qc-overlay{
  padding:12px;
  background:linear-gradient(to top,rgba(0,0,0,.55) 0%,transparent 100%);
}
.qc-name{
  font-family:'Fraunces',serif;
  font-size:14px;font-weight:700;color:#fff;line-height:1.2;
}
.qc-count{font-size:11px;color:rgba(255,255,255,.7);margin-top:2px}


/* ── FOOTER ── */
footer{background:var(--bleu);padding:24px 20px;margin-top:32px}
.footer-inner{
  display:flex;justify-content:space-between;align-items:center;
  flex-wrap:wrap;gap:12px;
}
.footer-copy{font-size:11px;color:rgba(255,255,255,.4)}
.footer-links{display:flex;gap:16px;flex-wrap:wrap}
.footer-links a{font-size:11px;color:rgba(255,255,255,.5);text-decoration:none}
.footer-links a:hover{color:rgba(255,255,255,.85)}

/* ── RESPONSIVE ── */
@media(min-width:480px){
  .hero-content{padding:48px 5% 28px}
  .search-float{padding:24px 5% 22px}
  .stats-row{padding:24px 5% 0}
  .section-head{padding:0 5%}
  .hscroll{padding:0 5% 4px}
  .qc-grid{padding:0 5%}
  .qc{min-height:130px}
  footer{padding:24px 5%}
}
@media(min-width:640px){
  .qc-grid{grid-template-columns:repeat(4,1fr)}
  .mc{flex:0 0 200px}
}
</style>
</head>
<body>

{{-- TOPBAR --}}
<header class="topbar">
  <a href="{{ route('portail.home') }}" class="topbar-logo">
    <div class="logo-dot"></div>
    Bimo<em>Portail</em>
  </a>
  <div class="topbar-spacer"></div>
  <a href="{{ route('home') }}" class="topbar-link">Renlio.sn</a>
</header>

{{-- HERO --}}
<section class="hero">
  <div class="hero-content">
    <span class="hero-tag">🇸🇳 Immobilier Sénégal</span>
    <h1 class="hero-h1">Trouvez votre bien idéal, sans stress</h1>
    <p class="hero-sub">Annonces vérifiées par des agences agréées.</p>
  </div>

  <div class="search-float">
    <form action="{{ route('portail.index') }}" method="GET" id="search-form">
      <label class="search-label" for="q-quartier">Où cherchez-vous ?</label>
      <div class="search-row">
        <input class="search-input" type="text" id="q-quartier" name="quartier"
               placeholder="Quartier, commune, ville…" autocomplete="off">
        <button class="btn-search" type="submit">Chercher</button>
      </div>
    </form>
    <div class="type-tabs">
      <a href="{{ route('portail.index') }}" class="type-tab active">Tous</a>
      <a href="{{ route('portail.index', ['type' => 'appartement']) }}" class="type-tab">Appt</a>
      <a href="{{ route('portail.index', ['type' => 'villa']) }}" class="type-tab">Villa</a>
      <a href="{{ route('portail.index', ['type' => 'bureau']) }}" class="type-tab">Bureau</a>
      <a href="{{ route('portail.index', ['type' => 'terrain']) }}" class="type-tab">Terrain</a>
    </div>
  </div>
</section>

{{-- STATS ROW --}}
<div class="stats-row">
  <div class="stat-pill">
    <span class="stat-val">{{ $nbBiens }}</span>
    <span class="stat-lbl">biens dispo.</span>
  </div>
  <div class="stat-pill">
    <span class="stat-val">{{ $nbAgences }}</span>
    <span class="stat-lbl">agences</span>
  </div>
  <div class="stat-pill">
    <span class="stat-val">{{ $nbVilles }}</span>
    <span class="stat-lbl">villes</span>
  </div>
</div>

{{-- NOUVEAUX BIENS --}}
<section class="section">
  <div class="section-head">
    <h2 class="section-title">Nouveaux biens</h2>
    <a href="{{ route('portail.index') }}" class="section-link">Voir tout →</a>
  </div>

  @if($nouveaux->isNotEmpty())
  <div class="hscroll">
    @foreach($nouveaux as $bien)
    @php $photo = $bien->photos->first(); @endphp
    <a href="{{ route('portail.show', $bien->slug) }}" class="mc">
      <div class="mc-photo">
        @if($photo)
          <img src="{{ $photo->url }}" alt="{{ $bien->titre_fallback }}" loading="lazy">
        @endif
        <span class="mc-badge">{{ $bien->type_label }}</span>
      </div>
      <div class="mc-body">
        <div class="mc-prix">
          {{ number_format($bien->loyer_mensuel, 0, ',', ' ') }}
          <small>FCFA/mois</small>
        </div>
        <div class="mc-titre">{{ $bien->titre_fallback }}</div>
        <div class="mc-loc">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/>
            <circle cx="12" cy="10" r="3"/>
          </svg>
          {{ $bien->quartier }}
        </div>
      </div>
    </a>
    @endforeach
  </div>
  @else
  <div class="empty-carousel">Aucun bien disponible pour le moment.</div>
  @endif
</section>

{{-- PAR QUARTIER --}}
@if($quartiers->isNotEmpty())
<section class="section">
  <div class="section-head">
    <h2 class="section-title">Par quartier</h2>
  </div>
  <div class="qc-grid">
    @foreach($quartiers as $q)
    <a href="{{ route('portail.index', ['quartier' => $q->quartier]) }}" class="qc">
      <div class="qc-overlay">
        <div class="qc-name">{{ $q->quartier }}</div>
        <div class="qc-count">{{ $q->nb_biens }} bien{{ $q->nb_biens > 1 ? 's' : '' }}</div>
      </div>
    </a>
    @endforeach
  </div>
</section>
@endif

<div style="height:80px"></div>

<footer>
  <div class="footer-inner">
    <span class="footer-copy">© {{ date('Y') }} Renlio. Tous droits réservés.</span>
    <div class="footer-links">
      <a href="{{ route('home') }}">Renlio.sn</a>
      <a href="{{ route('pricing') }}">Tarifs</a>
      <a href="{{ route('contact') }}">Contact</a>
      <a href="{{ route('mentions-legales') }}">Mentions légales</a>
    </div>
  </div>
</footer>

@include('portail._bottomnav')

<script>
// Vide l'input quartier avant submit si aucune valeur (évite ?quartier= vide dans l'URL)
document.getElementById('search-form').addEventListener('submit', function (e) {
  var inp = document.getElementById('q-quartier');
  if (!inp.value.trim()) {
    e.preventDefault();
    window.location.href = '{{ route('portail.index') }}';
  }
});
</script>
</body>
</html>
