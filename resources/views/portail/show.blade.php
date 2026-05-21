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
@endphp
<title>{{ $titrePage }} — Bimothèque Immo</title>
<meta name="description" content="{{ $descPage }}">
<meta property="og:title"       content="{{ $titrePage }} — Bimothèque Immo">
<meta property="og:description" content="{{ $descPage }}">
<meta property="og:image"       content="{{ $imageOg }}">
<meta property="og:url"         content="{{ route('portail.show', $bien->slug) }}">
<meta property="og:type"        content="article">
<meta property="og:site_name"   content="Bimothèque Immo">
<meta name="twitter:card"       content="summary_large_image">
<meta name="twitter:title"      content="{{ $titrePage }}">
<meta name="twitter:image"      content="{{ $imageOg }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,wght@0,300;0,400;0,500;0,600;1,300&display=swap" media="print" onload="this.media='all'">
<noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,wght@0,300;0,400;0,500;0,600;1,300&display=swap"></noscript>
<style>
*{box-sizing:border-box;margin:0;padding:0}
:root{--bg:#f9f7f2;--surface:#fff;--text:#0d1117;--text2:#374151;--muted:#6b7280;--muted2:#9ca3af;--border:#e5e7eb;--gold:#c9a84c;--green:#16a34a;--radius:14px;--radius-sm:10px}
body{font-family:'DM Sans',sans-serif;background:var(--bg);color:var(--text);-webkit-font-smoothing:antialiased}

.show-wrap{max-width:1100px;margin:0 auto;padding:96px 5% 80px}
.breadcrumb{display:flex;align-items:center;gap:6px;font-size:12px;color:var(--muted);margin-bottom:20px;flex-wrap:wrap}
.breadcrumb a{color:var(--muted);text-decoration:none}
.breadcrumb a:hover{color:var(--text)}
.bc-sep{width:10px;height:10px}

.show-grid{display:grid;grid-template-columns:1fr 320px;gap:32px;align-items:start}
.show-aside{position:sticky;top:24px}

.galerie-main{border-radius:var(--radius);overflow:hidden;aspect-ratio:16/9;background:#f3f4f6;display:flex;align-items:center;justify-content:center;margin-bottom:10px}
.galerie-main img{width:100%;height:100%;object-fit:cover}
.galerie-placeholder{display:flex;flex-direction:column;align-items:center;gap:10px;color:var(--muted2)}
.galerie-placeholder svg{width:44px;height:44px}
.galerie-placeholder span{font-size:13px}
.galerie-thumbs{display:flex;gap:8px;margin-bottom:24px;overflow-x:auto;padding-bottom:2px}
.thumb{width:76px;height:56px;border-radius:8px;overflow:hidden;flex-shrink:0;cursor:pointer;border:2px solid transparent;transition:border-color .15s}
.thumb.active{border-color:var(--gold)}
.thumb img{width:100%;height:100%;object-fit:cover}

.show-badges{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:10px}
.badge{display:inline-flex;align-items:center;padding:3px 11px;border-radius:99px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.4px}
.badge-type{background:#f3f4f6;color:var(--text2)}
.badge-meuble{background:#fffbeb;color:#92400e;border:1px solid #fde68a}
.badge-dispo{background:#f0fdf4;color:var(--green)}

.show-title{font-family:'Syne',sans-serif;font-size:clamp(20px,3vw,26px);font-weight:800;letter-spacing:-.5px;line-height:1.25;margin-bottom:8px}
.show-loc{display:flex;align-items:center;gap:6px;font-size:14px;color:var(--muted);margin-bottom:20px}
.show-loc svg{width:15px;height:15px;flex-shrink:0}

.details-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-bottom:24px}
.detail-item{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-sm);padding:12px;text-align:center}
.detail-val{font-family:'Syne',sans-serif;font-size:16px;font-weight:700;color:var(--text);margin-bottom:2px}
.detail-lbl{font-size:10px;color:var(--muted2);text-transform:uppercase;letter-spacing:.4px}

.section-title{font-family:'Syne',sans-serif;font-size:14px;font-weight:700;margin-bottom:10px}
.description-box{font-size:14px;color:var(--text2);line-height:1.75;background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-sm);padding:16px 18px}

.contact-card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);overflow:hidden}
.cc-price{background:var(--text);padding:18px 20px}
.cc-price-lbl{font-size:10px;color:rgba(255,255,255,.4);text-transform:uppercase;letter-spacing:.6px;margin-bottom:4px}
.cc-price-val{font-family:'Syne',sans-serif;font-size:26px;font-weight:800;color:#fff;line-height:1}
.cc-price-unit{font-size:12px;font-weight:400;color:rgba(255,255,255,.4);margin-left:3px}
.cc-body{padding:16px 20px}

.btn-wa{display:flex;align-items:center;justify-content:center;gap:9px;width:100%;padding:13px;background:#25d366;color:#fff;border:none;border-radius:10px;font-size:14px;font-weight:700;text-decoration:none;font-family:'DM Sans',sans-serif;transition:opacity .15s;margin-bottom:9px}
.btn-wa:hover{opacity:.9}
.btn-wa svg{width:20px;height:20px;flex-shrink:0}
.btn-tel{display:flex;align-items:center;justify-content:center;gap:8px;width:100%;padding:11px;background:var(--bg);color:var(--text2);border:1px solid var(--border);border-radius:10px;font-size:13px;font-weight:600;text-decoration:none;font-family:'DM Sans',sans-serif;transition:background .15s}
.btn-tel:hover{background:#f3f4f6}
.btn-tel svg{width:14px;height:14px;flex-shrink:0}

.cc-sep{border:none;border-top:1px solid var(--border);margin:14px 0}
.agency-row{display:flex;align-items:center;gap:12px}
.agency-logo{width:40px;height:40px;border-radius:10px;border:1px solid var(--border);object-fit:contain;background:#f9f7f2}
.agency-logo-ph{width:40px;height:40px;border-radius:10px;border:1px solid var(--border);background:#f3f4f6;display:flex;align-items:center;justify-content:center;font-family:'Syne',sans-serif;font-size:12px;font-weight:700;color:var(--muted2);flex-shrink:0}
.agency-name{font-size:13px;font-weight:600;color:var(--text)}
.agency-label{font-size:11px;color:var(--muted2);margin-top:1px}
.ref-row{display:flex;align-items:center;justify-content:space-between;padding:10px 0 0;font-size:12px;color:var(--muted2)}
.ref-val{font-family:'Syne',sans-serif;font-size:12px;font-weight:600;color:var(--muted)}

footer{background:#0d1117;border-top:1px solid #1f2937;padding:2rem 5%}
.footer-inner{max-width:1100px;margin:0 auto;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem}
.footer-copy{font-size:12px;color:#6b7280}
.footer-links{display:flex;gap:1.5rem;flex-wrap:wrap}
.footer-links a{font-size:12px;color:#6b7280;text-decoration:none}
.footer-links a:hover{color:#9ca3af}

@media(max-width:900px){.show-grid{grid-template-columns:1fr}.show-aside{position:static}}
@media(max-width:600px){.details-grid{grid-template-columns:repeat(2,1fr)}.show-wrap{padding:20px 4% 60px}}
</style>
</head>
<body>

@include('partials.public-nav', ['active' => 'portail'])

<div class="show-wrap">

    <div class="breadcrumb">
        <a href="{{ route('portail.index') }}">Biens à louer</a>
        <svg class="bc-sep" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
        <span>{{ $titrePage }}</span>
    </div>

    <div class="show-grid">

        {{-- COLONNE GAUCHE --}}
        <div>
            <div class="galerie-main">
                @if($photoPrincipale)
                    <img src="{{ $photoPrincipale->url }}" alt="{{ $titrePage }}" id="galerie-img" loading="eager">
                @else
                    <div class="galerie-placeholder">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <rect x="3" y="3" width="18" height="18" rx="2"/>
                            <circle cx="8.5" cy="8.5" r="1.5"/>
                            <polyline points="21 15 16 10 5 21"/>
                        </svg>
                        <span>Pas de photo disponible</span>
                    </div>
                @endif
            </div>

            @if($bien->photos->count() > 1)
            <div class="galerie-thumbs">
                @foreach($bien->photos as $photo)
                <div class="thumb {{ $photo->est_principale ? 'active' : '' }}"
                     onclick="changerPhoto('{{ $photo->url }}', this)">
                    <img src="{{ $photo->url }}" alt="" loading="lazy">
                </div>
                @endforeach
            </div>
            @endif

            <div class="show-badges">
                <div class="badge badge-type">{{ $bien->type_label }}</div>
                @if($bien->meuble)<div class="badge badge-meuble">Meublé</div>@endif
                <div class="badge badge-dispo">Disponible</div>
            </div>
            <h1 class="show-title">{{ $titrePage }}</h1>
            <div class="show-loc">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/>
                    <circle cx="12" cy="10" r="3"/>
                </svg>
                {{ collect([$bien->quartier, $bien->commune, $bien->ville])->filter()->unique()->implode(', ') }}
            </div>

            <div class="details-grid">
                @if($bien->surface_m2)
                <div class="detail-item">
                    <div class="detail-val">{{ number_format($bien->surface_m2, 0) }} m²</div>
                    <div class="detail-lbl">Surface</div>
                </div>
                @endif
                @if($bien->nombre_pieces)
                <div class="detail-item">
                    <div class="detail-val">{{ $bien->nombre_pieces }}</div>
                    <div class="detail-lbl">Pièce{{ $bien->nombre_pieces > 1 ? 's' : '' }}</div>
                </div>
                @endif
                <div class="detail-item">
                    <div class="detail-val">{{ $bien->meuble ? 'Oui' : 'Non' }}</div>
                    <div class="detail-lbl">Meublé</div>
                </div>
            </div>

            @if($bien->description)
            <h2 class="section-title">Description</h2>
            <div class="description-box">{{ $bien->description }}</div>
            @endif
        </div>

        {{-- ASIDE --}}
        <div class="show-aside">
            <div class="contact-card">
                <div class="cc-price">
                    <div class="cc-price-lbl">Loyer mensuel</div>
                    <span class="cc-price-val">{{ number_format($bien->loyer_mensuel, 0, ',', ' ') }}</span>
                    <span class="cc-price-unit">FCFA</span>
                </div>
                <div class="cc-body">
                    @if($whatsappUrl)
                    <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener" class="btn-wa">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                        </svg>
                        Contacter via WhatsApp
                    </a>
                    @endif
                    @php $telBrut = $bien->agency->whatsapp ?? $bien->agency->telephone ?? null; @endphp
                    @if($telBrut)
                    <a href="tel:{{ preg_replace('/[^0-9+]/', '', $telBrut) }}" class="btn-tel">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 8.63 19.79 19.79 0 01.01 2 2 2 0 012 .01h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.09 7.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7a2 2 0 011.72 2z"/>
                        </svg>
                        {{ $telBrut }}
                    </a>
                    @endif
                    <hr class="cc-sep">
                    <div class="agency-row">
                        @if($bien->agency->logo_path)
                            <img src="{{ asset('storage/' . $bien->agency->logo_path) }}"
                                 alt="{{ $bien->agency->name }}" class="agency-logo">
                        @else
                            <div class="agency-logo-ph">{{ mb_strtoupper(mb_substr($bien->agency->name, 0, 2)) }}</div>
                        @endif
                        <div>
                            <div class="agency-name">{{ $bien->agency->name }}</div>
                            <div class="agency-label">Agence partenaire</div>
                        </div>
                    </div>
                    <div class="ref-row">
                        <span>Référence</span>
                        <span class="ref-val">{{ $bien->reference }}</span>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<footer>
    <div class="footer-inner">
        <span class="footer-copy">© {{ date('Y') }} Bimothèque Immo. Tous droits réservés.</span>
        <div class="footer-links">
            <a href="{{ route('portail.index') }}">← Tous les biens</a>
            <a href="{{ route('pricing') }}">Tarifs</a>
            <a href="{{ route('contact') }}">Contact</a>
        </div>
    </div>
</footer>

<script>
function changerPhoto(src, el) {
    document.getElementById('galerie-img').src = src;
    document.querySelectorAll('.thumb').forEach(t => t.classList.remove('active'));
    el.classList.add('active');
}
</script>
</body>
</html>
