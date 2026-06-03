<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
@php
  $initiales = mb_strtoupper(mb_substr($agence->name, 0, 2));
  $waNumero  = $agence->whatsapp ? preg_replace('/[^0-9]/', '', $agence->whatsapp) : null;
  $telClean  = $agence->telephone ? preg_replace('/[^0-9+]/', '', $agence->telephone) : null;
  $waMessage = urlencode('Bonjour, je suis intéressé(e) par vos biens disponibles. Je vous contacte via BimoPortail.');
  $waUrl     = $waNumero ? 'https://wa.me/' . $waNumero . '?text=' . $waMessage : null;
  $hasCta    = $waUrl || $telClean;
@endphp
<title>{{ $agence->name }} — Profil agence — BimoPortail</title>
<meta name="description" content="{{ $agence->name }} — {{ $nbBiens }} bien{{ $nbBiens > 1 ? 's' : '' }} disponible{{ $nbBiens > 1 ? 's' : '' }} sur BimoPortail.">
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
  --vert-wa:#25D366;--blanc:#F2EDE6;
  --text:#0D1B26;--text2:#374151;--muted:#8E9BAA;--muted2:#9ca3af;
  --border:#E8E4DE;--radius:14px;--radius-sm:9px;--topbar-h:52px;
}
body{font-family:'Plus Jakarta Sans',sans-serif;background:var(--bg);color:var(--text);-webkit-font-smoothing:antialiased}

/* ── TOPBAR ── */
.topbar{position:sticky;top:0;z-index:100;display:flex;align-items:center;gap:12px;padding:0 16px;height:var(--topbar-h);background:var(--blanc);border-bottom:1px solid var(--gris-bord)}
.back-btn{display:flex;align-items:center;justify-content:center;width:36px;height:36px;border-radius:50%;border:1.5px solid var(--gris-bord);background:#fff;color:var(--bleu);text-decoration:none;flex-shrink:0}
.back-btn svg{width:18px;height:18px}
.topbar-title{font-family:'Fraunces',serif;font-size:15px;font-weight:600;color:var(--bleu)}

/* ── HERO ── */
.hero-bg{background:var(--bleu);padding:24px 0 0}
.ag-card-hero{background:#fff;border-radius:20px 20px 0 0;padding:24px 16px 20px;margin:0}
.ag-hero-logo{width:72px;height:72px;border-radius:16px;border:2px solid var(--gris-bord);object-fit:contain;background:var(--gris-clair);display:block;margin-bottom:14px}
.ag-hero-logo-ph{width:72px;height:72px;border-radius:16px;border:2px solid var(--gris-bord);background:var(--gris-clair);display:flex;align-items:center;justify-content:center;font-family:'Fraunces',serif;font-size:22px;font-weight:700;color:var(--gris);margin-bottom:14px}
.ag-hero-name{font-family:'Fraunces',serif;font-size:22px;font-weight:700;color:var(--bleu);line-height:1.2;margin-bottom:4px}
.ag-hero-loc{font-size:13px;color:var(--gris);display:flex;align-items:center;gap:4px;margin-bottom:10px}
.ag-hero-loc svg{width:13px;height:13px;flex-shrink:0}
.ag-verified{display:inline-flex;align-items:center;gap:5px;background:#f0fdf4;border:1px solid #bbf7d0;color:#16a34a;font-size:12px;font-weight:600;padding:4px 10px;border-radius:20px;margin-bottom:16px}
.ag-verified svg{width:13px;height:13px}
.stats-row{display:flex;border:1px solid var(--gris-bord);border-radius:var(--radius-sm);overflow:hidden}
.stat-cell{flex:1;padding:10px 8px;text-align:center;border-right:1px solid var(--gris-bord);background:#fff}
.stat-cell:last-child{border-right:none}
.stat-val{font-family:'Fraunces',serif;font-size:18px;font-weight:700;color:var(--bleu);line-height:1}
.stat-lbl{font-size:10px;color:var(--gris);text-transform:uppercase;letter-spacing:.4px;margin-top:3px}

/* ── TABS ── */
.tabs-bar{display:flex;background:#fff;border-bottom:1px solid var(--gris-bord);position:sticky;top:var(--topbar-h);z-index:50}
.tab-btn{flex:1;padding:13px;background:none;border:none;border-bottom:2px solid transparent;font-family:'Plus Jakarta Sans',sans-serif;font-size:14px;font-weight:500;color:var(--gris);cursor:pointer;transition:color .15s,border-color .15s}
.tab-btn.active{color:var(--bleu);border-bottom-color:var(--bleu);font-weight:600}

/* ── BIENS ── */
.bstack{display:flex;flex-direction:column;gap:16px;padding:16px 16px 150px;max-width:700px;margin:0 auto}

/* card .bc */
.bc{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;display:flex;flex-direction:column;text-decoration:none;color:inherit;transition:box-shadow .2s}
.bc:hover{box-shadow:0 6px 24px rgba(0,0,0,.09)}
.bc-photo{position:relative;height:200px;background:#f3f4f6;overflow:hidden;display:block;flex-shrink:0}
.bc-photo img{width:100%;height:100%;object-fit:cover;transition:transform .3s}
.bc:hover .bc-photo img{transform:scale(1.03)}
.bc-nophoto{width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:#f3f4f6}
.bc-nophoto svg{width:36px;height:36px;color:#d1d5db}
.badge-type{position:absolute;top:10px;left:10px;background:rgba(13,17,23,.72);color:#fff;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;padding:3px 9px;border-radius:6px;backdrop-filter:blur(4px)}
.badge-new{position:absolute;top:36px;left:10px;background:var(--or);color:var(--bleu);font-size:10px;font-weight:700;padding:3px 9px;border-radius:6px}
.bc-body{padding:14px 16px 16px;display:flex;flex-direction:column;gap:6px}
.bc-prix{font-family:'Fraunces',serif;font-size:20px;font-weight:700;color:var(--text)}
.bc-prix span{font-size:12px;font-weight:400;color:var(--muted);font-family:'Plus Jakarta Sans',sans-serif}
.bc-titre{font-size:14px;font-weight:600;color:var(--text);line-height:1.4;text-decoration:none}
.bc-loc{font-size:12px;color:var(--muted);display:flex;align-items:center;gap:4px}
.bc-loc svg{width:11px;height:11px;flex-shrink:0}
.bc-feats{display:flex;gap:10px;flex-wrap:wrap;font-size:12px;color:var(--muted2)}
.bc-feat{display:flex;align-items:center;gap:3px}

/* empty state */
.empty-state{text-align:center;padding:56px 20px}
.empty-state svg{width:44px;height:44px;color:var(--muted2);margin:0 auto 14px;display:block}
.empty-title{font-family:'Fraunces',serif;font-size:18px;font-weight:600;margin-bottom:8px}
.empty-sub{font-size:14px;color:var(--muted);line-height:1.6}
.empty-sub a{color:var(--or-dark)}

/* pagination */
.pagination-wrap{padding:8px 16px 56px;display:flex;justify-content:center;max-width:700px;margin:0 auto}

/* ── À PROPOS ── */
.apropos-wrap{padding:20px 16px 150px;max-width:700px;margin:0 auto}
.apropos-card{background:#fff;border:1px solid var(--gris-bord);border-radius:var(--radius);padding:18px;display:flex;flex-direction:column;gap:0}
.apropos-row{display:flex;align-items:flex-start;gap:12px;padding:12px 0;border-bottom:1px solid var(--gris-bord)}
.apropos-row:first-child{padding-top:0}
.apropos-row:last-child{border-bottom:none;padding-bottom:0}
.apropos-icon{width:32px;height:32px;border-radius:8px;background:var(--gris-clair);display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:1px}
.apropos-icon svg{width:15px;height:15px;color:var(--gris)}
.apropos-label{font-size:11px;color:var(--gris);text-transform:uppercase;letter-spacing:.4px;margin-bottom:3px}
.apropos-value{font-size:14px;color:var(--bleu);font-weight:500;word-break:break-word}

/* ── CTA BAR ── */
.cta-bar{position:fixed;bottom:64px;left:0;right:0;z-index:200;display:flex;gap:10px;padding:12px 16px;padding-bottom:max(12px,env(safe-area-inset-bottom));background:rgba(242,237,230,.96);backdrop-filter:blur(8px);border-top:1px solid var(--gris-bord)}
.cta-wa,.cta-call{display:flex;align-items:center;justify-content:center;gap:8px;padding:14px 0;border-radius:12px;font-size:14px;font-weight:700;font-family:'Plus Jakarta Sans',sans-serif;text-decoration:none;flex:1;transition:opacity .15s}
.cta-wa{background:var(--vert-wa);color:#fff}
.cta-call{background:var(--bleu);color:#fff}
.cta-wa:hover,.cta-call:hover{opacity:.88}
.cta-wa svg,.cta-call svg{width:18px;height:18px;flex-shrink:0}

/* ── FOOTER ── */
footer{background:var(--bleu);border-top:1px solid #1f2937;padding:2rem 16px}
.footer-inner{display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem;max-width:700px;margin:0 auto}
.footer-copy{font-size:12px;color:#6b7280}
.footer-links{display:flex;gap:1.5rem;flex-wrap:wrap}
.footer-links a{font-size:12px;color:#6b7280;text-decoration:none}
.footer-links a:hover{color:#9ca3af}

/* ── RESPONSIVE ── */
@media(min-width:600px){
  .ag-card-hero{margin:0 5%;border-radius:20px 20px 0 0}
  .bstack{padding:20px 5% 32px}
  .apropos-wrap{padding:20px 5% 100px}
  .pagination-wrap{padding:8px 5% 60px}
}
@media(min-width:900px){
  .bc{flex-direction:row}
  .bc-photo{width:240px;height:auto;min-height:160px;border-radius:0}
}
</style>
</head>
<body>

{{-- TOPBAR --}}
<div class="topbar">
  <a href="{{ route('portail.index') }}" class="back-btn" aria-label="Retour au listing">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
  </a>
  <span class="topbar-title">Profil agence</span>
</div>

{{-- HERO --}}
<div class="hero-bg">
  <div class="ag-card-hero">

    {{-- Logo ou initiales --}}
    @if($agence->logo_path)
      <img class="ag-hero-logo" src="{{ asset('storage/' . $agence->logo_path) }}" alt="{{ $agence->name }}">
    @else
      <div class="ag-hero-logo-ph">{{ $initiales }}</div>
    @endif

    <h1 class="ag-hero-name">{{ $agence->name }}</h1>

    @if($agence->adresse)
    <div class="ag-hero-loc">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/>
      </svg>
      {{ $agence->adresse }}
    </div>
    @endif

    @if($agence->actif)
    <div class="ag-verified">
      <svg viewBox="0 0 24 24" fill="currentColor">
        <path d="M9 12l2 2 4-4M20.618 5.984A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622C17.176 19.29 21 14.591 21 9a12.02 12.02 0 00-.382-3.016z"/>
      </svg>
      Agence vérifiée BimoPortail
    </div>
    @endif

    {{-- Stats row --}}
    <div class="stats-row">
      <div class="stat-cell">
        <div class="stat-val">{{ $nbBiens }}</div>
        <div class="stat-lbl">Bien{{ $nbBiens > 1 ? 's' : '' }} actif{{ $nbBiens > 1 ? 's' : '' }}</div>
      </div>
      <div class="stat-cell">
        <div class="stat-val">—</div>
        <div class="stat-lbl">Note</div>
      </div>
      <div class="stat-cell">
        <div class="stat-val">—</div>
        <div class="stat-lbl">Avis</div>
      </div>
    </div>

  </div>
</div>

{{-- TABS --}}
<div class="tabs-bar">
  <button class="tab-btn active" id="tab-btn-biens" type="button" onclick="setTab('biens')">
    Biens ({{ $nbBiens }})
  </button>
  <button class="tab-btn" id="tab-btn-apropos" type="button" onclick="setTab('apropos')">
    À propos
  </button>
</div>

{{-- SECTION BIENS --}}
<div id="tab-biens">
  <div class="bstack">
    @forelse($biens as $bien)
    @php
      $photo  = $bien->photos->first();
      $isNew  = $bien->created_at && $bien->created_at->gt(now()->subDays(30));
    @endphp
    <a href="{{ route('portail.show', $bien->slug) }}" class="bc">

      {{-- Photo --}}
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

      {{-- Corps --}}
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
      </div>

    </a>
    @empty
    <div class="empty-state">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
        <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
        <polyline points="9 22 9 12 15 12 15 22"/>
      </svg>
      <div class="empty-title">Aucun bien disponible</div>
      <p class="empty-sub">
        Cette agence n'a pas de biens disponibles pour le moment.<br>
        <a href="{{ route('portail.index') }}">Voir tous les biens</a>
      </p>
    </div>
    @endforelse
  </div>

  @if($biens->hasPages())
  <div class="pagination-wrap">{{ $biens->links() }}</div>
  @endif
</div>

{{-- SECTION À PROPOS --}}
<div id="tab-apropos" style="display:none">
  <div class="apropos-wrap">
    <div class="apropos-card">

      <div class="apropos-row">
        <div class="apropos-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
          </svg>
        </div>
        <div>
          <div class="apropos-label">Agence</div>
          <div class="apropos-value">{{ $agence->name }}</div>
        </div>
      </div>

      @if($agence->adresse)
      <div class="apropos-row">
        <div class="apropos-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/>
          </svg>
        </div>
        <div>
          <div class="apropos-label">Adresse</div>
          <div class="apropos-value">{{ $agence->adresse }}</div>
        </div>
      </div>
      @endif

      @if($agence->telephone)
      <div class="apropos-row">
        <div class="apropos-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 8.63 19.79 19.79 0 01.01 2 2 2 0 012 .01h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.09 7.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7a2 2 0 011.72 2z"/>
          </svg>
        </div>
        <div>
          <div class="apropos-label">Téléphone</div>
          <div class="apropos-value">{{ $agence->telephone }}</div>
        </div>
      </div>
      @endif

      @if($agence->email)
      <div class="apropos-row">
        <div class="apropos-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
            <polyline points="22,6 12,13 2,6"/>
          </svg>
        </div>
        <div>
          <div class="apropos-label">Email</div>
          <div class="apropos-value">{{ $agence->email }}</div>
        </div>
      </div>
      @endif

    </div>
  </div>
</div>

{{-- CTA FIXE --}}
@if($hasCta)
<div class="cta-bar">
  @if($waUrl)
  <a href="{{ $waUrl }}" target="_blank" rel="noopener" class="cta-wa">
    <svg viewBox="0 0 24 24" fill="currentColor">
      <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
    </svg>
    WhatsApp
  </a>
  @endif
  @if($telClean)
  <a href="tel:{{ $telClean }}" class="cta-call">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 8.63 19.79 19.79 0 01.01 2 2 2 0 012 .01h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.09 7.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7a2 2 0 011.72 2z"/>
    </svg>
    Appeler
  </a>
  @endif
</div>
@endif

<footer>
  <div class="footer-inner">
    <span class="footer-copy">© {{ date('Y') }} Renlio. Tous droits réservés.</span>
    <div class="footer-links">
      <a href="{{ route('home') }}">Accueil</a>
      <a href="{{ route('portail.index') }}">Biens</a>
      <a href="{{ route('pricing') }}">Tarifs</a>
      <a href="{{ route('contact') }}">Contact</a>
    </div>
  </div>
</footer>

<script>
function setTab(t) {
  document.getElementById('tab-biens').style.display    = t === 'biens'   ? 'block' : 'none';
  document.getElementById('tab-apropos').style.display  = t === 'apropos' ? 'block' : 'none';
  document.getElementById('tab-btn-biens').classList.toggle('active',   t === 'biens');
  document.getElementById('tab-btn-apropos').classList.toggle('active', t === 'apropos');
}
</script>
@include('portail._bottomnav')
</body>
</html>
