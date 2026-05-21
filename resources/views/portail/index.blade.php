<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Biens à louer au Sénégal — Bimothèque Immo</title>
<meta name="description" content="Consultez les biens disponibles à la location au Sénégal. Appartements, villas, studios, bureaux proposés par les agences immobilières partenaires.">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,wght@0,300;0,400;0,500;0,600;1,300&display=swap" media="print" onload="this.media='all'">
<noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,wght@0,300;0,400;0,500;0,600;1,300&display=swap"></noscript>
<style>
*{box-sizing:border-box;margin:0;padding:0}
:root{--bg:#f9f7f2;--surface:#fff;--text:#0d1117;--text2:#374151;--muted:#6b7280;--muted2:#9ca3af;--border:#e5e7eb;--gold:#c9a84c;--radius:14px;--radius-sm:10px}
body{font-family:'DM Sans',sans-serif;background:var(--bg);color:var(--text);-webkit-font-smoothing:antialiased}

.portail-hero{background:var(--text);color:#fff;padding:48px 5% 0}
.hero-inner{max-width:1100px;margin:0 auto}
.hero-title{font-family:'Syne',sans-serif;font-size:clamp(24px,4vw,38px);font-weight:800;letter-spacing:-.6px;margin-bottom:6px}
.hero-sub{font-size:14px;color:rgba(255,255,255,.5);margin-bottom:20px}
.hero-count{display:inline-flex;align-items:center;gap:6px;background:rgba(201,168,76,.12);border:1px solid rgba(201,168,76,.25);border-radius:99px;padding:4px 14px;font-size:12px;color:var(--gold);font-weight:600;margin-bottom:24px}
.hero-count-dot{width:6px;height:6px;background:var(--gold);border-radius:50%}

.filter-bar{background:var(--text);border-top:1px solid rgba(255,255,255,.07);padding:14px 5% 20px}
.filter-inner{max-width:1100px;margin:0 auto;display:flex;flex-wrap:wrap;gap:10px;align-items:center}
.filter-input,.filter-select{height:40px;padding:0 14px;border:1px solid rgba(255,255,255,.15);border-radius:9px;background:rgba(255,255,255,.07);color:#fff;font-size:13px;font-family:'DM Sans',sans-serif;outline:none;transition:border .15s}
.filter-input{min-width:160px}
.filter-select{min-width:150px;cursor:pointer}
.filter-input::placeholder{color:rgba(255,255,255,.35)}
.filter-select option{background:#0d1117;color:#fff}
.filter-input:focus,.filter-select:focus{border-color:var(--gold)}
.filter-label{display:flex;align-items:center;gap:7px;font-size:13px;color:rgba(255,255,255,.65);cursor:pointer;padding:0 2px;white-space:nowrap}
.filter-label input{accent-color:var(--gold);width:15px;height:15px}
.filter-btn{height:40px;padding:0 22px;background:var(--gold);color:#0d1117;border:none;border-radius:9px;font-size:13px;font-weight:700;cursor:pointer;font-family:'DM Sans',sans-serif;transition:opacity .15s;white-space:nowrap}
.filter-btn:hover{opacity:.88}
.filter-reset{height:40px;padding:0 14px;background:transparent;color:rgba(255,255,255,.45);border:1px solid rgba(255,255,255,.1);border-radius:9px;font-size:13px;cursor:pointer;font-family:'DM Sans',sans-serif;text-decoration:none;display:inline-flex;align-items:center}
.filter-reset:hover{color:rgba(255,255,255,.75)}

.agence-bandeau{background:#fffbeb;border-bottom:1px solid #fde68a;padding:10px 5%}
.agence-bandeau-inner{max-width:1100px;margin:0 auto;display:flex;align-items:center;justify-content:space-between;gap:12px;font-size:13px;color:#92400e;flex-wrap:wrap}
.agence-bandeau a{color:#92400e;font-weight:600;text-decoration:underline}

.portail-wrap{max-width:1100px;margin:0 auto;padding:32px 5% 80px}
.bien-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:20px}

.empty-state{grid-column:1/-1;text-align:center;padding:80px 20px}
.empty-icon{width:52px;height:52px;margin:0 auto 16px;color:var(--muted2)}
.empty-title{font-family:'Syne',sans-serif;font-size:18px;font-weight:700;margin-bottom:8px}
.empty-sub{font-size:14px;color:var(--muted);line-height:1.6}

.bien-card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;display:flex;flex-direction:column;transition:box-shadow .2s,transform .2s;text-decoration:none;color:inherit}
.bien-card:hover{box-shadow:0 8px 32px rgba(0,0,0,.10);transform:translateY(-2px)}
.card-photo{position:relative;height:185px;background:#f3f4f6;overflow:hidden;display:block}
.card-photo img{width:100%;height:100%;object-fit:cover;transition:transform .3s}
.bien-card:hover .card-photo img{transform:scale(1.04)}
.card-placeholder{width:100%;height:100%;display:flex;align-items:center;justify-content:center}
.card-placeholder svg{width:36px;height:36px;color:#d1d5db}
.badge-type{position:absolute;top:10px;left:10px;background:rgba(13,17,23,.72);color:#fff;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;padding:3px 9px;border-radius:6px;backdrop-filter:blur(4px)}
.badge-meuble{position:absolute;top:10px;right:10px;background:var(--gold);color:#0d1117;font-size:10px;font-weight:700;padding:3px 9px;border-radius:6px}
.card-body{padding:14px 16px 16px;display:flex;flex-direction:column;flex:1}
.card-title{font-family:'Syne',sans-serif;font-size:13px;font-weight:700;color:var(--text);margin-bottom:4px;line-height:1.35;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
.card-loc{font-size:12px;color:var(--muted);display:flex;align-items:center;gap:4px;margin-bottom:8px}
.card-loc svg{width:11px;height:11px;flex-shrink:0}
.card-meta{display:flex;gap:10px;font-size:11px;color:var(--muted2);margin-bottom:10px;flex-wrap:wrap}
.card-price{font-family:'Syne',sans-serif;font-size:17px;font-weight:800;color:var(--text);margin-top:auto}
.card-price-unit{font-size:11px;font-weight:400;color:var(--muted);margin-left:2px}
.card-agency{font-size:11px;color:var(--muted2);margin-top:8px;padding-top:8px;border-top:1px solid var(--border)}

.pagination-wrap{margin-top:40px;display:flex;justify-content:center}

footer{background:#0d1117;border-top:1px solid #1f2937;padding:2rem 5%}
.footer-inner{max-width:1100px;margin:0 auto;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem}
.footer-copy{font-size:12px;color:#6b7280}
.footer-links{display:flex;gap:1.5rem;flex-wrap:wrap}
.footer-links a{font-size:12px;color:#6b7280;text-decoration:none}
.footer-links a:hover{color:#9ca3af}

@media(max-width:900px){.bien-grid{grid-template-columns:repeat(2,1fr)}}
@media(max-width:600px){
    .bien-grid{grid-template-columns:1fr}
    .filter-inner{flex-direction:column;align-items:stretch}
    .filter-input,.filter-select,.filter-btn,.filter-reset{width:100%}
    .portail-hero{padding:32px 5% 0}
}
</style>
</head>
<body>

@include('partials.public-nav', ['active' => 'portail'])

<div class="portail-hero">
    <div class="hero-inner">
        <h1 class="hero-title">Biens à louer au Sénégal</h1>
        <p class="hero-sub">Mis à jour en temps réel par les agences partenaires de Bimothèque Immo.</p>
        <div class="hero-count">
            <div class="hero-count-dot"></div>
            {{ $biens->total() }} bien{{ $biens->total() > 1 ? 's' : '' }} disponible{{ $biens->total() > 1 ? 's' : '' }}
        </div>
    </div>
</div>

<div class="filter-bar">
    <form method="GET" action="{{ route('portail.index') }}" class="filter-inner">
        @if(request('agence'))
            <input type="hidden" name="agence" value="{{ request('agence') }}">
        @endif
        <select name="type" class="filter-select">
            <option value="">Tous les types</option>
            @foreach(\App\Models\Bien::TYPES as $val => $label)
                <option value="{{ $val }}" {{ request('type') === $val ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        <input type="text" name="quartier" class="filter-input"
               placeholder="Quartier…" value="{{ request('quartier') }}">
        <select name="prix_max" class="filter-select">
            <option value="">Prix max</option>
            @foreach([100000, 150000, 200000, 300000, 500000, 1000000] as $prix)
                <option value="{{ $prix }}" {{ request('prix_max') == $prix ? 'selected' : '' }}>
                    {{ number_format($prix, 0, ',', ' ') }} FCFA
                </option>
            @endforeach
        </select>
        <label class="filter-label">
            <input type="checkbox" name="meuble" value="1" {{ request('meuble') ? 'checked' : '' }}>
            Meublé
        </label>
        <button type="submit" class="filter-btn">Filtrer</button>
        @if(request()->hasAny(['type','quartier','prix_max','meuble']))
            <a href="{{ route('portail.index', request('agence') ? ['agence' => request('agence')] : []) }}"
               class="filter-reset">Effacer</a>
        @endif
    </form>
</div>

@if($agenceFiltree)
<div class="agence-bandeau">
    <div class="agence-bandeau-inner">
        <span>Biens de <strong>{{ $agenceFiltree->name }}</strong></span>
        <a href="{{ route('portail.index') }}">Voir tous les biens</a>
    </div>
</div>
@endif

<div class="portail-wrap">
    <div class="bien-grid">
        @forelse($biens as $bien)
        @php $photo = $bien->photos->first(); @endphp
        <a href="{{ route('portail.show', $bien->slug) }}" class="bien-card">
            <div class="card-photo">
                @if($photo)
                    <img src="{{ $photo->url }}" alt="{{ $bien->titre_fallback }}" loading="lazy">
                @else
                    <div class="card-placeholder">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                            <polyline points="9 22 9 12 15 12 15 22"/>
                        </svg>
                    </div>
                @endif
                <div class="badge-type">{{ $bien->type_label }}</div>
                @if($bien->meuble)<div class="badge-meuble">Meublé</div>@endif
            </div>
            <div class="card-body">
                <div class="card-title">{{ $bien->titre_fallback }}</div>
                <div class="card-loc">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/>
                        <circle cx="12" cy="10" r="3"/>
                    </svg>
                    {{ $bien->quartier }}@if($bien->ville && $bien->ville !== $bien->quartier), {{ $bien->ville }}@endif
                </div>
                <div class="card-meta">
                    @if($bien->surface_m2)<span>{{ number_format($bien->surface_m2, 0) }} m²</span>@endif
                    @if($bien->nombre_pieces)<span>{{ $bien->nombre_pieces }} pièce{{ $bien->nombre_pieces > 1 ? 's' : '' }}</span>@endif
                </div>
                <div class="card-price">
                    {{ number_format($bien->loyer_mensuel, 0, ',', ' ') }}
                    <span class="card-price-unit">FCFA/mois</span>
                </div>
                <div class="card-agency">{{ $bien->agency->name }}</div>
            </div>
        </a>
        @empty
        <div class="empty-state">
            <svg class="empty-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                <polyline points="9 22 9 12 15 12 15 22"/>
            </svg>
            <div class="empty-title">Aucun bien disponible</div>
            <p class="empty-sub">Aucun bien ne correspond à vos critères.<br>Essayez d'élargir les filtres.</p>
        </div>
        @endforelse
    </div>

    @if($biens->hasPages())
    <div class="pagination-wrap">{{ $biens->links() }}</div>
    @endif
</div>

<footer>
    <div class="footer-inner">
        <span class="footer-copy">© {{ date('Y') }} Bimothèque Immo. Tous droits réservés.</span>
        <div class="footer-links">
            <a href="{{ route('home') }}">Accueil</a>
            <a href="{{ route('pricing') }}">Tarifs</a>
            <a href="{{ route('contact') }}">Contact</a>
            <a href="{{ route('mentions-legales') }}">Mentions légales</a>
        </div>
    </div>
</footer>
</body>
</html>
