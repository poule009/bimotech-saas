<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Biens à louer au Sénégal — BimoPortail</title>
<meta name="description" content="Consultez les biens disponibles à la location au Sénégal. Appartements, villas, studios, bureaux proposés par les agences immobilières partenaires.">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,600;0,9..144,700;1,9..144,400&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" media="print" onload="this.media='all'">
<noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,600;0,9..144,700;1,9..144,400&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap"></noscript>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" media="print" onload="this.media='all'">
<style>
*{box-sizing:border-box;margin:0;padding:0}
:root{
  --bg:#f5f4f0;
  --surface:#fff;
  --text:#111827;
  --text2:#374151;
  --muted:#6b7280;
  --muted2:#9ca3af;
  --border:#e5e7eb;
  --or:#c8a84b;
  --or-dark:#a8892f;
  --noir:#0d1117;
  --radius:14px;
  --radius-sm:9px;
  --topbar-h:58px;
  --filter-h:96px;
}
body{font-family:'Plus Jakarta Sans',sans-serif;background:var(--bg);color:var(--text);-webkit-font-smoothing:antialiased}

/* ── TOPBAR ── */
.topbar{
  position:sticky;top:0;z-index:400;
  height:var(--topbar-h);
  background:#fff;
  border-bottom:1px solid var(--border);
  display:flex;align-items:center;padding:0 20px;
  gap:16px;
}
.topbar-logo{
  font-family:'Fraunces',serif;
  font-size:20px;font-weight:700;
  color:var(--noir);text-decoration:none;
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
.topbar-back{
  font-size:12px;color:var(--muted);text-decoration:none;
  display:flex;align-items:center;gap:5px;
}
.topbar-back:hover{color:var(--text)}

/* ── FILTER STICKY ── */
.filter-sticky{
  position:sticky;top:var(--topbar-h);z-index:200;
  background:#fff;border-bottom:1px solid var(--border);
  padding:10px 20px 0;
}
/* pills scrollables */
.fscroll{
  display:flex;gap:8px;
  overflow-x:auto;padding-bottom:10px;
  scrollbar-width:none;
  -webkit-overflow-scrolling:touch;
}
.fscroll::-webkit-scrollbar{display:none}
.pill{
  display:inline-flex;align-items:center;gap:5px;
  height:34px;padding:0 14px;
  border:1px solid var(--border);border-radius:99px;
  background:#fff;color:var(--text2);
  font-size:13px;font-weight:500;
  white-space:nowrap;text-decoration:none;cursor:pointer;
  font-family:inherit;transition:border-color .15s,background .15s;
  position:relative;
}
.pill:hover{border-color:var(--or);color:var(--or-dark)}
.pill.active{background:var(--noir);color:#fff;border-color:var(--noir)}
.pill-caret{font-size:10px;opacity:.6;margin-left:2px}

/* dropdown inline */
.pill-drop{position:relative}
.pill-drop-panel{
  display:none;
  position:absolute;top:calc(100% + 6px);left:0;z-index:300;
  background:#fff;border:1px solid var(--border);
  border-radius:var(--radius-sm);box-shadow:0 8px 24px rgba(0,0,0,.1);
  min-width:180px;padding:6px 0;
}
.pill-drop.open .pill-drop-panel{display:block}
.pill-drop-panel a{
  display:block;padding:9px 16px;
  font-size:13px;color:var(--text2);text-decoration:none;
}
.pill-drop-panel a:hover{background:var(--bg);color:var(--text)}
.pill-drop-panel a.active{color:var(--or);font-weight:600}

/* view-row */
.view-row{
  display:flex;align-items:center;gap:8px;
  padding:8px 0 10px;
}
.view-btn{
  height:30px;padding:0 12px;
  border:1px solid var(--border);border-radius:99px;
  background:#fff;color:var(--muted);
  font-size:12px;font-weight:500;
  cursor:pointer;font-family:inherit;
  display:inline-flex;align-items:center;gap:5px;
  text-decoration:none;transition:border-color .15s;
}
.view-btn.active{background:var(--noir);color:#fff;border-color:var(--noir)}
.view-count{font-size:12px;color:var(--muted);margin-left:auto}

/* ── AGENCE BANDEAU ── */
.agence-bandeau{background:#fffbeb;border-bottom:1px solid #fde68a;padding:10px 20px}
.agence-bandeau-inner{
  max-width:700px;margin:0 auto;
  display:flex;align-items:center;justify-content:space-between;
  gap:12px;font-size:13px;color:#92400e;flex-wrap:wrap;
}
.agence-bandeau a{color:#92400e;font-weight:600;text-decoration:underline}

/* ── VUE LISTE ── */
#vue-liste{display:block}
.bstack{
  display:flex;flex-direction:column;
  gap:16px;padding:14px 20px 24px;
  max-width:700px;margin:0 auto;
}

/* card .bc */
.bc{
  background:var(--surface);
  border:1px solid var(--border);border-radius:var(--radius);
  overflow:hidden;display:flex;flex-direction:column;
  text-decoration:none;color:inherit;
  transition:box-shadow .2s;
}
.bc:hover{box-shadow:0 6px 24px rgba(0,0,0,.09)}
.bc-photo{
  position:relative;height:220px;
  background:#f3f4f6;overflow:hidden;display:block;flex-shrink:0;
}
.bc-photo img{width:100%;height:100%;object-fit:cover;transition:transform .3s}
.bc:hover .bc-photo img{transform:scale(1.03)}
.bc-nophoto{
  width:100%;height:100%;display:flex;align-items:center;justify-content:center;
  background:#f3f4f6;
}
.bc-nophoto svg{width:40px;height:40px;color:#d1d5db}
.badge-type{
  position:absolute;top:10px;left:10px;
  background:rgba(13,17,23,.72);color:#fff;
  font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;
  padding:3px 9px;border-radius:6px;backdrop-filter:blur(4px);
}
.badge-new{
  position:absolute;top:10px;left:10px;
  margin-left:0;
  background:var(--or);color:var(--noir);
  font-size:10px;font-weight:700;
  padding:3px 9px;border-radius:6px;
}
/* si les deux badges présents, empiler badge-new sous badge-type (gauche) */
.bc-photo .badge-type + .badge-new{top:36px}
.btn-fav{
  position:absolute;top:10px;right:10px;
  width:32px;height:32px;border-radius:50%;
  background:rgba(255,255,255,.9);border:none;cursor:pointer;
  display:flex;align-items:center;justify-content:center;
  font-size:16px;transition:transform .15s;backdrop-filter:blur(4px);
  z-index:2;
}
.btn-fav:hover{transform:scale(1.15)}
.btn-fav.liked{color:#e53e3e}

.bc-body{padding:14px 16px 16px;display:flex;flex-direction:column;gap:6px}
.bc-prix{
  font-family:'Fraunces',serif;
  font-size:20px;font-weight:700;color:var(--text);
}
.bc-prix span{font-size:12px;font-weight:400;color:var(--muted);font-family:'Plus Jakarta Sans',sans-serif}
.bc-titre{font-size:14px;font-weight:600;color:var(--text);line-height:1.4}
.bc-loc{font-size:12px;color:var(--muted);display:flex;align-items:center;gap:4px}
.bc-loc svg{width:11px;height:11px;flex-shrink:0}
.bc-feats{display:flex;gap:10px;flex-wrap:wrap;font-size:12px;color:var(--muted2)}
.bc-feat{display:flex;align-items:center;gap:3px}
.bc-foot{
  display:flex;align-items:center;gap:8px;
  padding-top:10px;border-top:1px solid var(--border);
  font-size:12px;color:var(--muted);
}
.bc-foot-logo{
  width:26px;height:26px;border-radius:50%;
  background:var(--or);color:var(--noir);
  font-size:10px;font-weight:700;
  display:flex;align-items:center;justify-content:center;flex-shrink:0;
  font-family:'Fraunces',serif;
}
.bc-foot-name{font-weight:500;color:var(--text2)}
.bc-foot-verified{
  margin-left:auto;
  display:inline-flex;align-items:center;gap:3px;
  font-size:11px;color:#16a34a;font-weight:500;
}
.btn-wa{
  display:inline-flex;align-items:center;gap:6px;
  margin-top:4px;padding:7px 14px;
  background:#25d366;color:#fff;border:none;
  border-radius:var(--radius-sm);font-size:12px;font-weight:600;
  cursor:pointer;font-family:inherit;text-decoration:none;
  transition:opacity .15s;align-self:flex-start;
}
.btn-wa:hover{opacity:.88}

/* empty state */
.empty-state{text-align:center;padding:60px 20px}
.empty-state svg{width:48px;height:48px;color:var(--muted2);margin:0 auto 14px;display:block}
.empty-title{font-family:'Fraunces',serif;font-size:18px;font-weight:600;margin-bottom:8px}
.empty-sub{font-size:14px;color:var(--muted);line-height:1.6}
.empty-sub a{color:var(--or-dark)}

/* pagination */
.pagination-wrap{padding:8px 20px 48px;display:flex;justify-content:center;max-width:700px;margin:0 auto}

/* ── VUE CARTE ── */
#vue-carte{display:none}
.map-wrap{padding:14px 20px 0}
.map-box{
  border-radius:var(--radius);overflow:hidden;
  border:1px solid var(--border);height:430px;
}
#map{width:100%;height:100%}
.hscroll{
  display:flex;gap:12px;overflow-x:auto;
  padding:14px 20px 20px;
  scrollbar-width:none;-webkit-overflow-scrolling:touch;
}
.hscroll::-webkit-scrollbar{display:none}
/* mini-card carte */
.mc{
  flex-shrink:0;width:200px;
  background:#fff;border:1px solid var(--border);border-radius:var(--radius-sm);
  overflow:hidden;text-decoration:none;color:inherit;
  transition:box-shadow .2s;
}
.mc:hover{box-shadow:0 4px 16px rgba(0,0,0,.08)}
.mc-photo{height:100px;background:#f3f4f6;overflow:hidden}
.mc-photo img{width:100%;height:100%;object-fit:cover}
.mc-body{padding:8px 10px}
.mc-prix{font-family:'Fraunces',serif;font-size:14px;font-weight:700}
.mc-titre{font-size:11px;color:var(--muted);margin-top:2px;
  display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}

/* ── FOOTER ── */
footer{background:var(--noir);border-top:1px solid #1f2937;padding:2rem 20px}
.footer-inner{
  max-width:700px;margin:0 auto;
  display:flex;justify-content:space-between;align-items:center;
  flex-wrap:wrap;gap:1rem;
}
.footer-copy{font-size:12px;color:#6b7280}
.footer-links{display:flex;gap:1.5rem;flex-wrap:wrap}
.footer-links a{font-size:12px;color:#6b7280;text-decoration:none}
.footer-links a:hover{color:#9ca3af}

/* ── RESPONSIVE ── */
@media(min-width:600px){
  .bstack{padding:20px 5% 32px}
  .map-wrap{padding:20px 5% 0}
  .hscroll{padding:14px 5% 24px}
  .filter-sticky{padding:10px 5% 0}
  .topbar{padding:0 5%}
  .agence-bandeau{padding:10px 5%}
  .pagination-wrap{padding:8px 5% 60px}
}
@media(min-width:900px){
  .bstack{max-width:820px}
  .bc{flex-direction:row}
  .bc-photo{width:260px;height:auto;min-height:180px;border-radius:0}
}
</style>
</head>
<body>

{{-- TOPBAR --}}
<header class="topbar">
  <a href="{{ route('portail.index') }}" class="topbar-logo">
    <div class="logo-dot"></div>
    Bimo<em>Portail</em>
  </a>
  <div class="topbar-spacer"></div>
  <a href="{{ route('home') }}" class="topbar-back">
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
    Accueil
  </a>
</header>

{{-- FILTER STICKY --}}
<div class="filter-sticky">
  {{-- Ligne 1 : pills type + prix + chambres --}}
  <div class="fscroll">
    {{-- Pill "Tous" --}}
    <a href="{{ route('portail.index', array_filter(array_merge(request()->except(['type','page']), ['agence' => request('agence')]))) }}"
       class="pill {{ !request('type') ? 'active' : '' }}">Tous</a>

    @foreach(\App\Models\Bien::TYPES as $val => $label)
      @php
        $params = array_filter(array_merge(request()->except('page'), ['type' => $val]));
      @endphp
      <a href="{{ route('portail.index', $params) }}"
         class="pill {{ request('type') === $val ? 'active' : '' }}">{{ $label }}</a>
    @endforeach

    {{-- Pill Prix --}}
    <div class="pill-drop" id="drop-prix">
      <button class="pill {{ request('prix_max') ? 'active' : '' }}" type="button"
              onclick="toggleDrop('drop-prix')">
        Prix <span class="pill-caret">▾</span>
      </button>
      <div class="pill-drop-panel">
        @php $prixOptions = [100000,150000,200000,300000,500000,1000000]; @endphp
        <a href="{{ route('portail.index', array_filter(array_merge(request()->except(['prix_max','page'])))) }}"
           class="{{ !request('prix_max') ? 'active' : '' }}">Tous les prix</a>
        @foreach($prixOptions as $px)
          @php $pa = array_filter(array_merge(request()->except('page'), ['prix_max' => $px])); @endphp
          <a href="{{ route('portail.index', $pa) }}"
             class="{{ request('prix_max') == $px ? 'active' : '' }}">
            ≤ {{ number_format($px, 0, ',', ' ') }} FCFA
          </a>
        @endforeach
      </div>
    </div>

    {{-- Pill Chambres --}}
    <div class="pill-drop" id="drop-ch">
      <button class="pill {{ request('chambres') ? 'active' : '' }}" type="button"
              onclick="toggleDrop('drop-ch')">
        Chambres <span class="pill-caret">▾</span>
      </button>
      <div class="pill-drop-panel">
        <a href="{{ route('portail.index', array_filter(array_merge(request()->except(['chambres','page'])))) }}"
           class="{{ !request('chambres') ? 'active' : '' }}">Toutes</a>
        @foreach([1,2,3,4] as $ch)
          @php $cp = array_filter(array_merge(request()->except('page'), ['chambres' => $ch])); @endphp
          <a href="{{ route('portail.index', $cp) }}"
             class="{{ request('chambres') == $ch ? 'active' : '' }}">
            {{ $ch }}{{ $ch === 4 ? '+' : '' }} chambre{{ $ch > 1 ? 's' : '' }}
          </a>
        @endforeach
      </div>
    </div>
  </div>

  {{-- Ligne 2 : toggle vue + compteur --}}
  <div class="view-row">
    <button class="view-btn active" id="btn-liste" type="button" onclick="setVue('liste')">
      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
        <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
      </svg>
      Liste
    </button>
    <button class="view-btn" id="btn-carte" type="button" onclick="setVue('carte')">
      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/>
      </svg>
      Carte
    </button>
    <span class="view-count">{{ $biens->total() }} bien{{ $biens->total() > 1 ? 's' : '' }}</span>
  </div>
</div>

{{-- BANDEAU AGENCE --}}
@if($agenceFiltree)
<div class="agence-bandeau">
  <div class="agence-bandeau-inner">
    <span>Biens de <strong>{{ $agenceFiltree->name }}</strong></span>
    <a href="{{ route('portail.index') }}">Voir tous les biens</a>
  </div>
</div>
@endif

{{-- VUE LISTE --}}
<div id="vue-liste">
  <div class="bstack">
    @forelse($biens as $bien)
    @php
      $photo      = $bien->photos->first();
      $isNew      = $bien->created_at && $bien->created_at->gt(now()->subDays(30));
      $initiales  = strtoupper(substr($bien->agency->name ?? '?', 0, 2));
    @endphp
    <div class="bc">
      {{-- Photo --}}
      <a href="{{ route('portail.show', $bien->slug) }}" class="bc-photo">
        @if($photo)
          <img src="{{ $photo->url }}" alt="{{ $bien->titre_fallback }}" loading="lazy">
        @else
          <div class="bc-nophoto">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
              <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
              <polyline points="9 22 9 12 15 12 15 22"/>
            </svg>
          </div>
        @endif
        <div class="badge-type">{{ $bien->type_label }}</div>
        @if($isNew)<div class="badge-new">Nouveau</div>@endif
        <button class="btn-fav" type="button"
                data-id="{{ $bien->id }}"
                aria-label="Ajouter aux favoris"
                onclick="event.preventDefault();toggleFav({{ $bien->id }},this)">♡</button>
      </a>

      {{-- Corps --}}
      <div class="bc-body">
        <div class="bc-prix">
          {{ number_format($bien->loyer_mensuel, 0, ',', ' ') }}
          <span>FCFA/mois</span>
        </div>
        <a href="{{ route('portail.show', $bien->slug) }}" class="bc-titre">{{ $bien->titre_fallback }}</a>
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
        <button class="btn-wa" type="button"
                onclick="shareWA('{{ addslashes($bien->titre_fallback) }}','{{ $bien->quartier }}')">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
          </svg>
          Partager sur WhatsApp
        </button>
      </div>
    </div>
    @empty
    <div class="empty-state">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
        <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
        <polyline points="9 22 9 12 15 12 15 22"/>
      </svg>
      <div class="empty-title">Aucun bien disponible</div>
      <p class="empty-sub">
        Aucun bien ne correspond à vos critères.<br>
        <a href="{{ route('portail.index', request('agence') ? ['agence' => request('agence')] : []) }}">
          Effacer les filtres
        </a>
      </p>
    </div>
    @endforelse
  </div>

  @if($biens->hasPages())
  <div class="pagination-wrap">{{ $biens->links() }}</div>
  @endif
</div>

{{-- VUE CARTE --}}
<div id="vue-carte">
  <div class="map-wrap">
    <div class="map-box"><div id="map"></div></div>
  </div>

  {{-- Carousel mini-cards --}}
  <div class="hscroll" id="map-scroll">
    @foreach($biens as $bien)
    @php $photo = $bien->photos->first(); @endphp
    <a href="{{ route('portail.show', $bien->slug) }}" class="mc" data-id="{{ $bien->id }}">
      <div class="mc-photo">
        @if($photo)
          <img src="{{ $photo->url }}" alt="{{ $bien->titre_fallback }}" loading="lazy">
        @else
          <div style="width:100%;height:100%;background:#f3f4f6"></div>
        @endif
      </div>
      <div class="mc-body">
        <div class="mc-prix">{{ number_format($bien->loyer_mensuel, 0, ',', ' ') }} <small style="font-size:10px;font-weight:400;color:#9ca3af">F</small></div>
        <div class="mc-titre">{{ $bien->titre_fallback }} — {{ $bien->quartier }}</div>
      </div>
    </a>
    @endforeach
  </div>
</div>

<footer>
  <div class="footer-inner">
    <span class="footer-copy">© {{ date('Y') }} BIMO-tech. Tous droits réservés.</span>
    <div class="footer-links">
      <a href="{{ route('home') }}">Accueil</a>
      <a href="{{ route('pricing') }}">Tarifs</a>
      <a href="{{ route('contact') }}">Contact</a>
      <a href="{{ route('mentions-legales') }}">Mentions légales</a>
    </div>
  </div>
</footer>

{{-- Données carte pour Leaflet --}}
<script>
var BIENS_GEO = [
@foreach($biens as $bien)
@if($bien->latitude && $bien->longitude)
  {
    id: {{ $bien->id }},
    lat: {{ $bien->latitude }},
    lng: {{ $bien->longitude }},
    prix: "{{ number_format($bien->loyer_mensuel, 0, ',', ' ') }} F",
    titre: "{{ addslashes($bien->titre_fallback) }}",
    url: "{{ route('portail.show', $bien->slug) }}"
  },
@endif
@endforeach
];
</script>

<script>
// ── DROPDOWN PILLS ──
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

// ── VUE TOGGLE ──
var mapInit = false;
function setVue(v) {
  document.getElementById('vue-liste').style.display = v === 'liste' ? 'block' : 'none';
  document.getElementById('vue-carte').style.display = v === 'carte' ? 'block' : 'none';
  document.getElementById('btn-liste').classList.toggle('active', v === 'liste');
  document.getElementById('btn-carte').classList.toggle('active', v === 'carte');
  if (v === 'carte' && !mapInit) initMap();
}

// ── FAVORIS ──
function toggleFav(id, btn) {
  var favs = JSON.parse(localStorage.getItem('bimo_favs') || '[]');
  var idx = favs.indexOf(id);
  if (idx === -1) { favs.push(id); btn.classList.add('liked'); btn.textContent = '♥'; }
  else { favs.splice(idx, 1); btn.classList.remove('liked'); btn.textContent = '♡'; }
  localStorage.setItem('bimo_favs', JSON.stringify(favs));
}
document.addEventListener('DOMContentLoaded', function() {
  var favs = JSON.parse(localStorage.getItem('bimo_favs') || '[]');
  document.querySelectorAll('.btn-fav[data-id]').forEach(function(btn) {
    if (favs.indexOf(parseInt(btn.dataset.id)) !== -1) {
      btn.classList.add('liked');
      btn.textContent = '♥';
    }
  });
});

// ── WHATSAPP ──
function shareWA(titre, quartier) {
  var msg = encodeURIComponent('Bonjour, je suis intéressé(e) par ce bien : ' + titre + ' (' + quartier + ') — via BimoPortail');
  window.open('https://wa.me/?text=' + msg, '_blank');
}

// ── LEAFLET ──
function initMap() {
  if (typeof L === 'undefined') {
    var s = document.createElement('script');
    s.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
    s.onload = function() { mapInit = true; buildMap(); };
    document.head.appendChild(s);
  } else { mapInit = true; buildMap(); }
}
function buildMap() {
  var map = L.map('map').setView([14.69, -17.44], 12);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap'
  }).addTo(map);
  BIENS_GEO.forEach(function(b) {
    var m = L.marker([b.lat, b.lng]);
    m.bindPopup('<strong>' + b.prix + '</strong><br>' + b.titre +
      '<br><a href="' + b.url + '">Voir le bien →</a>');
    m.addTo(map);
    m.on('click', function() {
      var card = document.querySelector('#map-scroll .mc[data-id="' + b.id + '"]');
      if (card) card.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
    });
  });
}
</script>
</body>
</html>
