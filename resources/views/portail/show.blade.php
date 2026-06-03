<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
@php
    use Illuminate\Support\Str;
    $titrePage = $bien->titre_fallback;
    $descPage  = $bien->description
        ? Str::limit($bien->description, 155)
        : 'Bien à louer à ' . $bien->ville
            . ($bien->surface_m2    ? ' · ' . number_format($bien->surface_m2, 0) . ' m²' : '')
            . ($bien->nombre_pieces ? ' · ' . $bien->nombre_pieces . ' pièces' : '')
            . ' · ' . number_format($bien->loyer_mensuel, 0, ',', ' ') . ' FCFA/mois.';
    $imageOg   = $photoPrincipale
        ? asset('storage/' . $photoPrincipale->chemin)
        : asset('images/portail-og-default.jpg');
    $photos    = $bien->photos;
    $nbPhotos  = $photos->count();
$amenites  = is_array($bien->amenites) ? array_filter($bien->amenites) : [];
    $telBrut   = $bien->agency->whatsapp ?? $bien->agency->telephone ?? null;
    $telClean  = $telBrut ? preg_replace('/[^0-9+]/', '', $telBrut) : null;
@endphp
<title>{{ $titrePage }} — Renlio</title>
<meta name="description" content="{{ $descPage }}">
<meta property="og:title"       content="{{ $titrePage }} — Renlio">
<meta property="og:description" content="{{ $descPage }}">
<meta property="og:image"       content="{{ $imageOg }}">
<meta property="og:url"         content="{{ route('portail.show', $bien->slug) }}">
<meta property="og:type"        content="article">
<meta property="og:site_name"   content="Renlio">
<meta name="twitter:card"       content="summary_large_image">
<meta name="twitter:title"      content="{{ $titrePage }}">
<meta name="twitter:image"      content="{{ $imageOg }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Fraunces:wght@300;600;700;900&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" media="print" onload="this.media='all'">
<noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Fraunces:wght@300;600;700;900&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap"></noscript>
<style>
*{box-sizing:border-box;margin:0;padding:0}
:root{
  --bleu:#0D1B26;--rouge:#C8302A;--or:#C4965A;--or-light:#e8c99a;
  --blanc:#F2EDE6;--gris:#8E9BAA;--gris-clair:#F4F2EE;--gris-bord:#E8E4DE;
  --vert-wa:#25D366;
}
html,body{height:100%;font-family:'Plus Jakarta Sans',sans-serif;background:var(--blanc);color:var(--bleu);-webkit-font-smoothing:antialiased}

/* ── Topbar ── */
.topbar{position:sticky;top:0;z-index:100;display:flex;align-items:center;gap:12px;padding:0 16px;height:52px;background:var(--blanc);border-bottom:1px solid var(--gris-bord)}
.back-btn{display:flex;align-items:center;justify-content:center;width:36px;height:36px;border-radius:50%;border:1.5px solid var(--gris-bord);background:#fff;color:var(--bleu);cursor:pointer;flex-shrink:0;text-decoration:none}
.back-btn svg{width:18px;height:18px}
.topbar-title{font-family:'Fraunces',serif;font-size:15px;font-weight:600;color:var(--bleu);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}

/* ── Gallery ── */
.gallery{position:relative;overflow:hidden;background:#000;aspect-ratio:4/3}
@media(min-width:480px){.gallery{aspect-ratio:16/9}}
.gallery-track{display:flex;height:100%;transition:transform .35s ease;will-change:transform}
.gallery-slide{flex:0 0 100%;height:100%}
.gallery-slide img{width:100%;height:100%;object-fit:cover;display:block}
.gallery-placeholder{width:100%;height:100%;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:10px;background:var(--gris-clair);color:var(--gris)}
.gallery-placeholder svg{width:48px;height:48px;opacity:.5}
.gallery-placeholder span{font-size:13px}
.gallery-nav{position:absolute;bottom:12px;left:50%;transform:translateX(-50%);display:flex;gap:6px;align-items:center}
.gnav-dot{width:6px;height:6px;border-radius:50%;background:rgba(255,255,255,.45);transition:background .2s,width .2s;cursor:pointer}
.gnav-dot.active{background:#fff;width:18px;border-radius:4px}
.gallery-counter{position:absolute;bottom:14px;right:14px;background:rgba(0,0,0,.55);color:#fff;font-size:11px;font-weight:600;padding:3px 8px;border-radius:20px;backdrop-filter:blur(4px)}
.gallery-btns{position:absolute;top:12px;right:12px;display:flex;gap:8px}
.gallery-btn{width:36px;height:36px;border-radius:50%;border:none;background:rgba(255,255,255,.9);backdrop-filter:blur(4px);display:flex;align-items:center;justify-content:center;cursor:pointer;color:var(--bleu)}
.gallery-btn svg{width:18px;height:18px}
.gallery-btn.fav-active svg{fill:var(--rouge);stroke:var(--rouge)}

/* ── Det body ── */
.det-body{padding:16px 16px 140px}

.det-top{margin-bottom:14px}
.det-price{font-family:'Fraunces',serif;font-size:26px;font-weight:700;color:var(--bleu);line-height:1}
.det-price-unit{font-size:13px;font-weight:400;color:var(--gris);margin-left:4px}
.det-title{font-family:'Fraunces',serif;font-size:20px;font-weight:600;line-height:1.3;margin-top:6px;color:var(--bleu)}
.det-loc{display:flex;align-items:center;gap:5px;font-size:13px;color:var(--gris);margin-top:6px}
.det-loc svg{width:14px;height:14px;flex-shrink:0}

/* ── Feats grid ── */
.feats-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:1px;background:var(--gris-bord);border:1px solid var(--gris-bord);border-radius:12px;overflow:hidden;margin-bottom:0}
.feat-cell{background:#fff;padding:12px 8px;display:flex;flex-direction:column;align-items:center;gap:4px}
.feat-icon{font-size:18px;line-height:1}
.feat-val{font-family:'Fraunces',serif;font-size:16px;font-weight:700;color:var(--bleu);line-height:1}
.feat-lbl{font-size:10px;color:var(--gris);text-transform:uppercase;letter-spacing:.4px;text-align:center}

.divider{height:1px;background:var(--gris-bord);margin:20px 0}

.sub-title{font-family:'Fraunces',serif;font-size:15px;font-weight:600;color:var(--bleu);margin-bottom:10px}
.desc-text{font-size:14px;line-height:1.75;color:#374151}

/* ── Amenities ── */
.amenities{display:flex;flex-wrap:wrap;gap:8px}
.amenity{display:inline-flex;align-items:center;gap:6px;padding:6px 12px;border-radius:20px;border:1px solid var(--gris-bord);background:#fff;font-size:12px;font-weight:500;color:var(--bleu)}
.amenity-icon{font-size:14px}

/* ── Agency strip ── */
.ag-strip{display:flex;align-items:center;gap:12px;padding:14px;background:#fff;border:1px solid var(--gris-bord);border-radius:12px;text-decoration:none}
.ag-logo{width:44px;height:44px;border-radius:10px;border:1px solid var(--gris-bord);object-fit:contain;flex-shrink:0;background:var(--gris-clair)}
.ag-logo-ph{width:44px;height:44px;border-radius:10px;border:1px solid var(--gris-bord);background:var(--gris-clair);display:flex;align-items:center;justify-content:center;font-family:'Fraunces',serif;font-size:14px;font-weight:700;color:var(--gris);flex-shrink:0}
.ag-info{flex:1;min-width:0}
.ag-nom{font-size:14px;font-weight:600;color:var(--bleu);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.ag-sub{font-size:11px;color:var(--gris);margin-top:2px;display:flex;align-items:center;gap:4px}
.ag-sub svg{width:12px;height:12px;color:var(--or)}

/* ── Share button ── */
.det-share{display:flex;align-items:center;justify-content:center;gap:10px;width:100%;padding:14px;background:var(--bleu);color:#fff;border:none;border-radius:12px;font-size:14px;font-weight:600;font-family:'Plus Jakarta Sans',sans-serif;cursor:pointer;text-decoration:none;transition:opacity .15s}
.det-share:hover{opacity:.88}
.det-share svg{width:18px;height:18px}

/* ── Similar cards carousel ── */
.hscroll{display:flex;gap:12px;overflow-x:auto;padding-bottom:4px;-webkit-overflow-scrolling:touch;scrollbar-width:none}
.hscroll::-webkit-scrollbar{display:none}
.mc{flex:0 0 160px;border-radius:10px;overflow:hidden;background:#fff;border:1px solid var(--gris-bord);text-decoration:none;color:var(--bleu);display:flex;flex-direction:column}
.mc-img{height:100px;overflow:hidden;background:var(--gris-clair);position:relative}
.mc-img img{width:100%;height:100%;object-fit:cover;display:block}
.mc-badge{position:absolute;top:6px;left:6px;background:var(--bleu);color:#fff;font-size:9px;font-weight:700;padding:2px 6px;border-radius:10px;text-transform:uppercase}
.mc-body{padding:8px 10px;flex:1;display:flex;flex-direction:column;gap:2px}
.mc-price{font-family:'Fraunces',serif;font-size:13px;font-weight:700;color:var(--bleu);line-height:1}
.mc-unit{font-size:9px;color:var(--gris);font-weight:400}
.mc-title{font-size:11px;font-weight:600;color:var(--bleu);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-top:2px}
.mc-loc{font-size:10px;color:var(--gris);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}

/* ── Fixed CTA bar ── */
.cta-bar{position:fixed;bottom:64px;left:0;right:0;z-index:200;display:flex;gap:10px;padding:12px 16px;padding-bottom:max(12px,env(safe-area-inset-bottom));background:rgba(242,237,230,.96);backdrop-filter:blur(8px);border-top:1px solid var(--gris-bord)}
.cta-wa,.cta-call{display:flex;align-items:center;justify-content:center;gap:8px;padding:14px 0;border-radius:12px;font-size:14px;font-weight:700;font-family:'Plus Jakarta Sans',sans-serif;text-decoration:none;flex:1;transition:opacity .15s}
.cta-wa{background:var(--vert-wa);color:#fff}
.cta-call{background:var(--bleu);color:#fff}
.cta-wa:hover,.cta-call:hover{opacity:.88}
.cta-wa svg,.cta-call svg{width:18px;height:18px;flex-shrink:0}
</style>
</head>
<body>

{{-- Topbar --}}
<div class="topbar">
    <a href="{{ route('portail.index') }}" class="back-btn" aria-label="Retour">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
    </a>
    <span class="topbar-title">Détail du bien</span>
</div>

{{-- Gallery --}}
<div class="gallery" id="gallery">
    @if($nbPhotos > 0)
        <div class="gallery-track" id="gtrack">
            @foreach($photos as $photo)
            <div class="gallery-slide">
                <img src="{{ asset('storage/' . $photo->chemin) }}"
                     alt="{{ $titrePage }}"
                     loading="{{ $loop->first ? 'eager' : 'lazy' }}">
            </div>
            @endforeach
        </div>
        @if($nbPhotos > 1)
        <div class="gallery-nav" id="gnav">
            @foreach($photos as $i => $p)
            <button class="gnav-dot {{ $loop->first ? 'active' : '' }}"
                    aria-label="Photo {{ $loop->iteration }}"
                    onclick="goSlide({{ $loop->index }})"></button>
            @endforeach
        </div>
        <div class="gallery-counter" id="gcounter">1/{{ $nbPhotos }}</div>
        @endif
    @else
        <div class="gallery-placeholder">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <rect x="3" y="3" width="18" height="18" rx="2"/>
                <circle cx="8.5" cy="8.5" r="1.5"/>
                <polyline points="21 15 16 10 5 21"/>
            </svg>
            <span>Pas de photo disponible</span>
        </div>
    @endif

    <div class="gallery-btns">
        <button class="gallery-btn" id="fav-btn" aria-label="Favori" onclick="toggleFav()">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/>
            </svg>
        </button>
        <button class="gallery-btn" aria-label="Partager" onclick="partager()">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/>
                <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/>
            </svg>
        </button>
    </div>
</div>

{{-- Body --}}
<div class="det-body">

    {{-- Prix + Titre + Localisation --}}
    <div class="det-top">
        <div>
            <span class="det-price">{{ number_format($bien->loyer_mensuel, 0, ',', ' ') }} FCFA</span>
            <span class="det-price-unit">/mois</span>
        </div>
        <h1 class="det-title">{{ $titrePage }}</h1>
        <div class="det-loc">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/>
                <circle cx="12" cy="10" r="3"/>
            </svg>
            {{ collect([$bien->quartier, $bien->commune, $bien->ville])->filter()->unique()->implode(', ') }}
        </div>
    </div>

    <div class="divider" style="margin-top:16px"></div>

    {{-- Grille 6 cellules --}}
    <div class="feats-grid">
        <div class="feat-cell">
            <span class="feat-icon">🛏</span>
            <span class="feat-val">{{ $bien->nombre_chambres ?? '—' }}</span>
            <span class="feat-lbl">Chambres</span>
        </div>
        <div class="feat-cell">
            <span class="feat-icon">🚿</span>
            <span class="feat-val">{{ $bien->nombre_sdb ?? '—' }}</span>
            <span class="feat-lbl">Salle de bain</span>
        </div>
        <div class="feat-cell">
            <span class="feat-icon">📐</span>
            <span class="feat-val">{{ $bien->surface_m2 ? number_format($bien->surface_m2, 0) . ' m²' : '—' }}</span>
            <span class="feat-lbl">Surface</span>
        </div>
        <div class="feat-cell">
            <span class="feat-icon">🚗</span>
            <span class="feat-val">{{ $bien->parking ? 'Oui' : 'Non' }}</span>
            <span class="feat-lbl">Parking</span>
        </div>
        <div class="feat-cell">
            <span class="feat-icon">❄️</span>
            <span class="feat-val">{{ $bien->climatise ? 'Oui' : 'Non' }}</span>
            <span class="feat-lbl">Climatisé</span>
        </div>
        <div class="feat-cell">
            <span class="feat-icon">🏢</span>
            <span class="feat-val">{{ $bien->etage ?? '—' }}</span>
            <span class="feat-lbl">Étage</span>
        </div>
    </div>

    @if($bien->description)
    <div class="divider"></div>
    <p class="sub-title">Description</p>
    <p class="desc-text">{{ $bien->description }}</p>
    @endif

    @if(count($amenites) > 0)
    <div class="divider"></div>
    <p class="sub-title">Commodités</p>
    <div class="amenities">
        @foreach($amenites as $item)
        <span class="amenity"><span class="amenity-icon">✓</span>{{ $item }}</span>
        @endforeach
    </div>
    @endif

<div class="divider"></div>

    {{-- Agence --}}
    <p class="sub-title">Agence</p>
    <a href="#" class="ag-strip">
        @if($bien->agency->logo_path)
            <img class="ag-logo" src="{{ asset('storage/' . $bien->agency->logo_path) }}" alt="{{ $bien->agency->name }}">
        @else
            <div class="ag-logo-ph">{{ mb_strtoupper(mb_substr($bien->agency->name, 0, 2)) }}</div>
        @endif
        <div class="ag-info">
            <div class="ag-nom">{{ $bien->agency->name }}</div>
            <div class="ag-sub">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                Agence vérifiée
            </div>
        </div>
    </a>

    @if($whatsappUrl)
    <div class="divider"></div>
    <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener" class="det-share">
        <svg viewBox="0 0 24 24" fill="currentColor">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
        </svg>
        Partager sur WhatsApp
    </a>
    @endif

    {{-- Biens similaires --}}
    @php
    $similaires = \App\Models\Bien::portail()
        ->where('type', $bien->type)
        ->where('ville', $bien->ville)
        ->where('id', '!=', $bien->id)
        ->limit(6)
        ->get();
    @endphp

    @if($similaires->isNotEmpty())
    <div class="divider"></div>
    <p class="sub-title">Biens similaires</p>
    <div class="hscroll">
        @foreach($similaires as $sim)
        @php $simPhoto = $sim->photos->first(); @endphp
        <a href="{{ route('portail.show', $sim->slug) }}" class="mc">
            <div class="mc-img">
                @if($simPhoto)
                    <img src="{{ asset('storage/' . $simPhoto->chemin) }}" alt="{{ $sim->titre_fallback }}" loading="lazy">
                @endif
                <span class="mc-badge">{{ $sim->type_label }}</span>
            </div>
            <div class="mc-body">
                <div>
                    <span class="mc-price">{{ number_format($sim->loyer_mensuel, 0, ',', ' ') }}</span>
                    <span class="mc-unit">FCFA/mois</span>
                </div>
                <div class="mc-title">{{ $sim->titre_fallback }}</div>
                <div class="mc-loc">{{ collect([$sim->quartier, $sim->ville])->filter()->implode(', ') }}</div>
            </div>
        </a>
        @endforeach
    </div>
    @endif

</div>

{{-- CTA fixe --}}
<div class="cta-bar">
    @if($whatsappUrl)
    <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener" class="cta-wa">
        <svg viewBox="0 0 24 24" fill="currentColor">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
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

<script>
// ── Gallery swipe ──────────────────────────────────────────────────────────
(function(){
    var track  = document.getElementById('gtrack');
    if (!track) return;
    var dots   = document.querySelectorAll('.gnav-dot');
    var ctr    = document.getElementById('gcounter');
    var total  = {{ $nbPhotos }};
    var cur    = 0;
    var startX = 0;

    function goSlide(n) {
        cur = Math.max(0, Math.min(n, total - 1));
        track.style.transform = 'translateX(-' + (cur * 100) + '%)';
        dots.forEach(function(d, i){ d.classList.toggle('active', i === cur); });
        if (ctr) ctr.textContent = (cur + 1) + '/' + total;
    }
    window.goSlide = goSlide;

    var gallery = document.getElementById('gallery');
    gallery.addEventListener('touchstart', function(e){ startX = e.touches[0].clientX; }, {passive:true});
    gallery.addEventListener('touchend', function(e){
        var dx = e.changedTouches[0].clientX - startX;
        if (Math.abs(dx) > 40) goSlide(dx < 0 ? cur + 1 : cur - 1);
    }, {passive:true});
})();

// ── Favourites ─────────────────────────────────────────────────────────────
(function(){
    var KEY  = 'bimo_favs';
    var id   = '{{ $bien->id }}';
    var btn  = document.getElementById('fav-btn');
    if (!btn) return;

    function getFavs(){ try{ return JSON.parse(localStorage.getItem(KEY)||'[]'); }catch(e){ return []; } }
    function setFavs(f){ localStorage.setItem(KEY, JSON.stringify(f)); }

    function syncBtn(){
        var active = getFavs().indexOf(id) !== -1;
        btn.classList.toggle('fav-active', active);
    }
    window.toggleFav = function(){
        var favs = getFavs();
        var idx  = favs.indexOf(id);
        if (idx === -1) favs.push(id); else favs.splice(idx, 1);
        setFavs(favs);
        syncBtn();
    };
    syncBtn();
})();

// ── Web Share / fallback ───────────────────────────────────────────────────
window.partager = function(){
    var data = { title: '{{ addslashes($titrePage) }}', url: window.location.href };
    if (navigator.share) { navigator.share(data).catch(function(){}); }
    else { navigator.clipboard && navigator.clipboard.writeText(data.url); }
};

</script>
@include('portail._bottomnav')
</body>
</html>
