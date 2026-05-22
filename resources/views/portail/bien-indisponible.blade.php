<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex,follow">
<title>Bien indisponible — Bimothèque Immo</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Syne:wght@400;700;800&family=DM+Sans:wght@400;500;600&display=swap" media="print" onload="this.media='all'">
<noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Syne:wght@400;700;800&family=DM+Sans:wght@400;500;600&display=swap"></noscript>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'DM Sans',sans-serif;background:#f9f7f2;color:#0d1117;-webkit-font-smoothing:antialiased;min-height:100vh;display:flex;flex-direction:column}
.page-body{flex:1;display:flex;align-items:center;justify-content:center;padding:104px 5% 40px}
.card{background:#fff;border:1px solid #e5e7eb;border-radius:14px;padding:48px 40px;max-width:460px;width:100%;text-align:center}
.icon-wrap{width:60px;height:60px;border-radius:16px;background:#f9fafb;border:1px solid #e5e7eb;display:flex;align-items:center;justify-content:center;margin:0 auto 20px}
.icon-wrap svg{width:28px;height:28px;color:#9ca3af}
h1{font-family:'Syne',sans-serif;font-size:21px;font-weight:800;letter-spacing:-.4px;margin-bottom:10px}
.bien-info{background:#f9f7f2;border-radius:8px;padding:8px 14px;font-size:12px;color:#9ca3af;margin-bottom:16px;display:inline-block}
p{font-size:14px;color:#6b7280;line-height:1.7;margin-bottom:28px}
.btn-retour{display:inline-flex;align-items:center;gap:8px;padding:12px 24px;background:#0d1117;color:#fff;border-radius:10px;font-size:13px;font-weight:600;text-decoration:none;transition:opacity .15s;font-family:'DM Sans',sans-serif}
.btn-retour:hover{opacity:.85}
footer{background:#0d1117;border-top:1px solid #1f2937;padding:1.5rem 5%}
.footer-inner{max-width:1100px;margin:0 auto;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem}
.footer-copy{font-size:12px;color:#6b7280}
@media(max-width:500px){.card{padding:36px 24px}}
</style>
</head>
<body>

@include('partials.public-nav', ['active' => 'portail'])

<div class="page-body">
    <div class="card">
        <div class="icon-wrap">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                <polyline points="9 22 9 12 15 12 15 22"/>
            </svg>
        </div>
        <h1>Ce bien n'est plus disponible</h1>
        @if(!empty($bien->titre))
        <div class="bien-info">{{ $bien->titre }}@if($bien->ville) — {{ $bien->ville }}@endif</div>
        @endif
        <p>Ce bien a été loué ou retiré du portail.<br>Consultez nos autres biens disponibles.</p>
        <a href="{{ route('portail.index') }}" class="btn-retour">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:13px;height:13px">
                <polyline points="15 18 9 12 15 6"/>
            </svg>
            Voir tous les biens
        </a>
    </div>
</div>

<footer>
    <div class="footer-inner">
        <span class="footer-copy">© {{ date('Y') }} BIMO-tech. Tous droits réservés.</span>
    </div>
</footer>
</body>
</html>
