<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Biens à louer à {{ $quartier }} — BimoPortail</title>
<meta name="description" content="BimoPortail référence {{ $biens->total() }} bien{{ $biens->total() > 1 ? 's' : '' }} disponible{{ $biens->total() > 1 ? 's' : '' }} à {{ $quartier }}, Dakar. Appartements, villas, bureaux proposés par des agences agréées.">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Fraunces:wght@400;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" media="print" onload="this.media='all'">
<noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Fraunces:wght@400;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap"></noscript>
<style>
*{box-sizing:border-box;margin:0;padding:0}
:root{
  --bg:#F4F2EE;--surface:#fff;
  --bleu:#0D1B26;--or:#C4965A;--or-dark:#a8892f;
  --gris:#8E9BAA;--gris-clair:#F4F2EE;--gris-bord:#E8E4DE;
  --text:#0D1B26;--text2:#374151;--muted:#8E9BAA;--muted2:#9ca3af;
  --border:#E8E4DE;--radius:14px;--radius-sm:9px;--topbar-h:58px;
}
body{font-family:'Plus Jakarta Sans',sans-serif;background:var(--bg);color:var(--text);-webkit-font-smoothing:antialiased}

/* ── TOPBAR ── */
.topbar{position:sticky;top:0;z-index:400;height:var(--topbar-h);background:#fff;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:12px;padding:0 16px}
.back-btn{display:flex;align-items:center;justify-content:center;width:36px;height:36px;border-radius:50%;border:1.5px solid var(--gris-bord);background:#fff;color:var(--bleu);text-decoration:none;flex-shrink:0}
.back-btn svg{width:18px;height:18px}
.topbar-title{font-family:'Fraunces',serif;font-size:15px;font-weight:600;color:var(--bleu);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}

/* ── HERO ── */
.hero{height:200px;background:var(--bleu);position:relative;display:flex;flex-direction:column;justify-content:flex-end;padding:20px 16px}
.hero::after{content:'';position:absolute;inset:0;background:linear-gradient(to top,rgba(13,27,38,.85) 0%,rgba(13,27,38,.35) 60%,transparent 100%);pointer-events:none}
.hero-content{position:relative;z-index:1}
.breadcrumb{font-size:11px;color:rgba(255,255,255,.6);margin-bottom:8px;display:flex;align-items:center;gap:5px;flex-wrap:wrap}
.breadcrumb a{color:rgba(255,255,255,.6);text-decoration:none}
.breadcrumb a:hover{color:#fff}
.breadcrumb-sep{font-size:10px;opacity:.5}
.hero-h1{font-family:'Fraunces',serif;font-size:22px;font-weight:700;color:#fff;line-height:1.2;margin-bottom:6px}
.hero-sub{font-size:12px;color:rgba(255,255,255,.75);display:flex;align-items:center;gap:6px}
.hero-dot{width:6px;height:6px;border-radius:50%;background:#4ade80;flex-shrink:0;animation:pulse-dot 2s ease-in-out infinite}
@keyframes pulse-dot{0%,100%{transform:scale(1);opacity:1}50%{transform:scale(1.4);opacity:.7}}

/* ── SEO BAND ── */
.seo-band{background:#fff;border-bottom:1px solid var(--border);padding:12px 16px}
.seo-text{font-size:13px;color:var(--muted);line-height:1.6;max-width:700px;margin:0 auto}

/* ── FILTER STICKY ── */
.filter-sticky{position:sticky;top:var(--topbar-h);z-index:200;background:#fff;border-bottom:1px solid var(--border);padding:10px 16px 0}
.fscroll{display:flex;gap:8px;overflow-x:auto;padding-bottom:10px;scrollbar-width:none;-webkit-overflow-scrolling:touch}
.fscroll::-webkit-scrollbar{display:none}
.pill{display:inline-flex;align-items:center;gap:5px;height:34px;padding:0 14px;border:1px solid var(--border);border-radius:99px;background:#fff;color:var(--text2);font-size:13px;font-weight:500;white-space:nowrap;text-decoration:none;cursor:pointer;font-family:inherit;transition:border-color .15s,background .15s}
.pill:hover{border-color:var(--or);color:var(--or-dark)}
.pill.active{background:var(--bleu);color:#fff;border-color:var(--bleu)}
.pill-caret{font-size:10px;opacity:.6;margin-left:2px}
.pill-drop{position:relative}
.pill-drop-panel{display:none;position:absolute;top:calc(100% + 6px);left:0;z-index:300;background:#fff;border:1px solid var(--border);border-radius:var(--radius-sm);box-shadow:0 8px 24px rgba(0,0,0,.1);min-width:180px;padding:6px 0}
.pill-drop.open .pill-drop-panel{display:block}
.pill-drop-panel a{display:block;padding:9px 16px;font-size:13px;color:var(--text2);text-decoration:none}
.pill-drop-panel a:hover{background:var(--bg);color:var(--text)}
.pill-drop-panel a.active{color:var(--or);font-weight:600}
.result-bar{display:flex;align-items:center;gap:8px;padding:8px 0 10px}
.result-count{font-size:12px;color:var(--muted);flex:1}
.btn-sort{height:30px;padding:0 12px;border:1px solid var(--border);border-radius:99px;background:#fff;color:var(--muted);font-size:12px;font-weight:500;cursor:default;font-family:inherit;display:inline-flex;align-items:center;gap:5px}

/* ── LISTING ── */
.bstack{display:flex;flex-direction:column;gap:16px;padding:14px 16px 24px;max-width:700px;margin:0 auto}
.bc{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;display:flex;flex-direction:column;text-decoration:none;color:inherit;transition:box-shadow .2s}
.bc:hover{box-shadow:0 6px 24px rgba(0,0,0,.09)}
.bc-photo{position:relative;height:220px;background:#f3f4f6;overflow:hidden;display:block;flex-shrink:0}
.bc-photo img{width:100%;height:100%;object-fit:cover;transition:transform .3s}
.bc:hover .bc-photo img{transform:scale(1.03)}
.bc-nophoto{width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:#f3f4f6}
.bc-nophoto svg{width:40px;height:40px;color:#d1d5db}
.badge-type{position:absolute;top:10px;left:10px;background:rgba(13,17,23,.72);color:#fff;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;padding:3px 9px;border-radius:6px;backdrop-filter:blur(4px)}
.badge-new{position:absolute;top:36px;left:10px;background:var(--or);color:var(--bleu);font-size:10px;font-weight:700;padding:3px 9px;border-radius:6px}
.bc-body{padding:14px 16px 16px;display:flex;flex-direction:column;gap:6px}
.bc-prix{font-family:'Fraunces',serif;font-size:20px;font-weight:700;color:var(--text)}
.bc-prix span{font-size:12px;font-weight:400;color:var(--muted);font-family:'Plus Jakarta Sans',sans-serif}
.bc-titre{font-size:14px;font-weight:600;color:var(--text);line-height:1.4}
.bc-loc{font-size:12px;color:var(--muted);display:flex;align-items:center;gap:4px}
.bc-loc svg{width:11px;height:11px;flex-shrink:0}
.bc-feats{display:flex;gap:10px;flex-wrap:wrap;font-size:12px;color:var(--muted2)}
.bc-feat{display:flex;align-items:center;gap:3px}
.bc-foot{display:flex;align-items:center;gap:8px;padding-top:10px;border-top:1px solid var(--border);font-size:12px;color:var(--muted)}
.bc-foot-logo{width:26px;height:26px;border-radius:50%;background:var(--or);color:var(--bleu);font-size:10px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-family:'Fraunces',serif}
.bc-foot-name{font-weight:500;color:var(--text2)}
.bc-foot-verified{margin-left:auto;display:inline-flex;align-items:center;gap:3px;font-size:11px;color:#16a34a;font-weight:500}

/* empty state */
.empty-state{text-align:center;padding:60px 20px}
.empty-state svg{width:48px;height:48px;color:var(--muted2);margin:0 auto 14px;display:block}
.empty-title{font-family:'Fraunces',serif;font-size:18px;font-weight:600;margin-bottom:8px}
.empty-sub{font-size:14px;color:var(--muted);line-height:1.6}
.empty-sub a{color:var(--or-dark)}

/* pagination */
.pagination-wrap{padding:8px 16px 32px;display:flex;justify-content:center;max-width:700px;margin:0 auto}

/* ── QUARTIERS SIMILAIRES ── */
.sim-section{padding:24px 16px 40px;max-width:700px;margin:0 auto}
.sim-title{font-family:'Fraunces',serif;font-size:16px;font-weight:700;color:var(--bleu);margin-bottom:14px}
.sim-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px}
.sim-card{position:relative;height:90px;background:var(--bleu);border-radius:var(--radius-sm);overflow:hidden;text-decoration:none;display:flex;flex-direction:column;justify-content:flex-end;padding:12px;transition:opacity .15s}
.sim-card:hover{opacity:.88}
.sim-card::after{content:'';position:absolute;inset:0;background:linear-gradient(to top,rgba(13,27,38,.75) 0%,transparent 60%);pointer-events:none}
.sim-card-content{position:relative;z-index:1}
.sim-card-name{font-family:'Fraunces',serif;font-size:13px;font-weight:700;color:#fff;line-height:1.2}
.sim-card-count{font-size:11px;color:rgba(255,255,255,.65);margin-top:2px}

/* ── FOOTER ── */
footer{background:var(--bleu);border-top:1px solid #1f2937;padding:2rem 16px}
.footer-inner{display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem;max-width:700px;margin:0 auto}
.footer-copy{font-size:12px;color:#6b7280}
.footer-links{display:flex;gap:1.5rem;flex-wrap:wrap}
.footer-links a{font-size:12px;color:#6b7280;text-decoration:none}
.footer-links a:hover{color:#9ca3af}

/* ── RESPONSIVE ── */
@media(min-width:600px){
  .topbar{padding:0 5%}
  .hero{padding:20px 5%}
  .seo-band{padding:12px 5%}
  .filter-sticky{padding:10px 5% 0}
  .bstack{padding:20px 5% 32px}
  .sim-section{padding:24px 5% 48px}
  .pagination-wrap{padding:8px 5% 40px}
}
@media(min-width:900px){
  .bc{flex-direction:row}
  .bc-photo{width:260px;height:auto;min-height:180px;border-radius:0}
  .sim-grid{grid-template-columns:repeat(4,1fr)}
}
</style>
</head>
<body>

{{-- TOPBAR --}}
<div class="topbar">
  <a href="{{ route('portail.index') }}" class="back-btn" aria-label="Retour au listing">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
  </a>
  <span class="topbar-title">{{ $quartier }}</span>
</div>

{{-- HERO --}}
<div class="hero">
  <div class="hero-content">
    <div class="breadcrumb">
      <a href="{{ route('portail.home') }}">BimoPortail</a>
      <span class="breadcrumb-sep">›</span>
      <a href="{{ route('portail.index') }}">Biens</a>
      <span class="breadcrumb-sep">›</span>
      <span>{{ $quartier }}</span>
    </div>
    <h1 class="hero-h1">Biens à louer à {{ $quartier }}</h1>
    <div class="hero-sub">
      <div class="hero-dot"></div>
      {{ $biens->total() }} annonce{{ $biens->total() > 1 ? 's' : '' }} · Mise à jour en temps réel
    </div>
  </div>
</div>

{{-- SEO BAND --}}
<div class="seo-band">
  <p class="seo-text">
    <strong>{{ $quartier }}</strong> est un quartier de Dakar.
    BimoPortail y référence <strong>{{ $biens->total() }}</strong> bien{{ $biens->total() > 1 ? 's' : '' }} disponible{{ $biens->total() > 1 ? 's' : '' }}
    proposé{{ $biens->total() > 1 ? 's' : '' }} par des agences agréées.
  </p>
</div>

{{-- FILTER STICKY --}}
<div class="filter-sticky">
  <div class="fscroll">

    {{-- Pill Tous --}}
    <a href="{{ route('portail.quartier', array_filter(['quartier' => $quartier, 'chambres' => request('chambres'), 'prix_max' => request('prix_max')])) }}"
       class="pill {{ !request('type') ? 'active' : '' }}">Tous</a>

    @foreach(\App\Models\Bien::TYPES as $val => $label)
      @php $params = array_filter(['quartier' => $quartier, 'type' => $val, 'chambres' => request('chambres'), 'prix_max' => request('prix_max')]); @endphp
      <a href="{{ route('portail.quartier', $params) }}"
         class="pill {{ request('type') === $val ? 'active' : '' }}">{{ $label }}</a>
    @endforeach

    {{-- Pill Prix --}}
    <div class="pill-drop" id="drop-prix">
      <button class="pill {{ request('prix_max') ? 'active' : '' }}" type="button" onclick="toggleDrop('drop-prix')">
        Prix <span class="pill-caret">▾</span>
      </button>
      <div class="pill-drop-panel">
        @php $prixOptions = [100000,150000,200000,300000,500000,1000000]; @endphp
        <a href="{{ route('portail.quartier', array_filter(['quartier' => $quartier, 'type' => request('type'), 'chambres' => request('chambres')])) }}"
           class="{{ !request('prix_max') ? 'active' : '' }}">Tous les prix</a>
        @foreach($prixOptions as $px)
          @php $pp = array_filter(['quartier' => $quartier, 'type' => request('type'), 'chambres' => request('chambres'), 'prix_max' => $px]); @endphp
          <a href="{{ route('portail.quartier', $pp) }}"
             class="{{ request('prix_max') == $px ? 'active' : '' }}">
            ≤ {{ number_format($px, 0, ',', ' ') }} FCFA
          </a>
        @endforeach
      </div>
    </div>

    {{-- Pill Chambres --}}
    <div class="pill-drop" id="drop-ch">
      <button class="pill {{ request('chambres') ? 'active' : '' }}" type="button" onclick="toggleDrop('drop-ch')">
        Chambres <span class="pill-caret">▾</span>
      </button>
      <div class="pill-drop-panel">
        <a href="{{ route('portail.quartier', array_filter(['quartier' => $quartier, 'type' => request('type'), 'prix_max' => request('prix_max')])) }}"
           class="{{ !request('chambres') ? 'active' : '' }}">Toutes</a>
        @foreach([1,2,3,4] as $ch)
          @php $cp = array_filter(['quartier' => $quartier, 'type' => request('type'), 'prix_max' => request('prix_max'), 'chambres' => $ch]); @endphp
          <a href="{{ route('portail.quartier', $cp) }}"
             class="{{ request('chambres') == $ch ? 'active' : '' }}">
            {{ $ch }}{{ $ch === 4 ? '+' : '' }} chambre{{ $ch > 1 ? 's' : '' }}
          </a>
        @endforeach
      </div>
    </div>

  </div>

  {{-- Result bar --}}
  <div class="result-bar">
    <span class="result-count">
      {{ $biens->total() }} bien{{ $biens->total() > 1 ? 's' : '' }} à {{ $quartier }}
    </span>
    <button class="btn-sort" type="button">
      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <line x1="3" y1="6" x2="15" y2="6"/><line x1="3" y1="12" x2="9" y2="12"/>
        <polyline points="12 15 9 18 6 15"/>
      </svg>
      Trier
    </button>
  </div>
</div>

{{-- LISTING --}}
<div class="bstack">
  @forelse($biens as $bien)
  @php
    $photo     = $bien->photos->first();
    $isNew     = $bien->created_at && $bien->created_at->gt(now()->subDays(30));
    $initiales = mb_strtoupper(mb_substr($bien->agency->name ?? '?', 0, 2));
  @endphp
  <a href="{{ route('portail.show', $bien->slug) }}" class="bc">
    <div class="bc-photo">
      @if($photo)
        <img src="{{ $photo->url }}" alt="{{ $bien->titre_fallback }}" loading="lazy">
      @else
        <div class="bc-nophoto">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <rect x="3" y="3" width="18" height="18" rx="2"/>
            <circle cx="8.5" cy="8.5" r="1.5"/>
            <polyline points="21 15 16 10 5 21"/>
          </svg>
        </div>
      @endif
      <div class="badge-type">{{ $bien->type_label }}</div>
      @if($isNew)<div class="badge-new">Nouveau</div>@endif
    </div>
    <div class="bc-body">
      <div class="bc-prix">
        {{ number_format($bien->loyer_mensuel, 0, ',', ' ') }}
        <span>FCFA/mois</span>
      </div>
      <div class="bc-titre">{{ $bien->titre_fallback }}</div>
      <div class="bc-loc">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/>
          <circle cx="12" cy="10" r="3"/>
        </svg>
        {{ $bien->quartier }}@if($bien->ville && $bien->ville !== $bien->quartier), {{ $bien->ville }}@endif
      </div>
      <div class="bc-feats">
        @if($bien->nombre_chambres)
          <span class="bc-feat">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M2 9V5a2 2 0 012-2h16a2 2 0 012 2v4M2 9h20M2 9v10a2 2 0 002 2h16a2 2 0 002-2V9"/>
            </svg>
            {{ $bien->nombre_chambres }} ch.
          </span>
        @endif
        @if($bien->nombre_sdb)
          <span class="bc-feat">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M4 12h16M4 12a2 2 0 01-2-2V6a2 2 0 012-2h4M20 12a2 2 0 002-2V9a2 2 0 00-2-2h-4"/>
            </svg>
            {{ $bien->nombre_sdb }} sdb
          </span>
        @endif
        @if($bien->surface_m2)
          <span class="bc-feat">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="3" y="3" width="18" height="18" rx="2"/>
            </svg>
            {{ number_format($bien->surface_m2, 0) }} m²
          </span>
        @endif
        @if($bien->meuble)
          <span class="bc-feat">Meublé</span>
        @endif
      </div>
      <div class="bc-foot">
        <div class="bc-foot-logo">{{ $initiales }}</div>
        <span class="bc-foot-name">{{ $bien->agency->name }}</span>
        @if($bien->agency->actif)
          <span class="bc-foot-verified">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="currentColor">
              <path d="M9 12l2 2 4-4M20.618 5.984A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622C17.176 19.29 21 14.591 21 9a12.02 12.02 0 00-.382-3.016z"/>
            </svg>
            Vérifié
          </span>
        @endif
      </div>
    </div>
  </a>
  @empty
  <div class="empty-state">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
      <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
      <polyline points="9 22 9 12 15 12 15 22"/>
    </svg>
    <div class="empty-title">Aucun bien trouvé</div>
    <p class="empty-sub">
      Aucun bien ne correspond à vos critères dans ce quartier.<br>
      <a href="{{ route('portail.quartier', ['quartier' => $quartier]) }}">Effacer les filtres</a>
    </p>
  </div>
  @endforelse
</div>

{{-- PAGINATION --}}
@if($biens->hasPages())
<div class="pagination-wrap">{{ $biens->links() }}</div>
@endif

{{-- QUARTIERS SIMILAIRES --}}
@if($similaires->isNotEmpty())
<div class="sim-section">
  <div class="sim-title">Quartiers similaires</div>
  <div class="sim-grid">
    @foreach($similaires as $sim)
    <a href="{{ route('portail.quartier', ['quartier' => $sim->quartier]) }}" class="sim-card">
      <div class="sim-card-content">
        <div class="sim-card-name">{{ $sim->quartier }}</div>
        <div class="sim-card-count">{{ $sim->nb_biens }} bien{{ $sim->nb_biens > 1 ? 's' : '' }}</div>
      </div>
    </a>
    @endforeach
  </div>
</div>
@endif

<footer>
  <div class="footer-inner">
    <span class="footer-copy">© {{ date('Y') }} BIMO-tech. Tous droits réservés.</span>
    <div class="footer-links">
      <a href="{{ route('home') }}">Accueil</a>
      <a href="{{ route('portail.index') }}">Biens</a>
      <a href="{{ route('pricing') }}">Tarifs</a>
      <a href="{{ route('contact') }}">Contact</a>
    </div>
  </div>
</footer>

<script>
function toggleDrop(id) {
  var el = document.getElementById(id);
  var isOpen = el.classList.contains('open');
  document.querySelectorAll('.pill-drop.open').forEach(function(d){ d.classList.remove('open'); });
  if (!isOpen) el.classList.add('open');
}
document.addEventListener('click', function(e) {
  if (!e.target.closest('.pill-drop')) {
    document.querySelectorAll('.pill-drop.open').forEach(function(d){ d.classList.remove('open'); });
  }
});
</script>
</body>
</html>
