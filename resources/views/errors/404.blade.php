<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Page introuvable — BimoTech Immo</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@300;400;500&display=swap" media="print" onload="this.media='all'">
<noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@300;400;500&display=swap"></noscript>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'DM Sans',sans-serif;background:#0d1117;color:#e6edf3;min-height:100vh;display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;padding:2rem;position:relative;overflow:hidden}

/* Grille déco */
body::before{content:'';position:absolute;inset:0;background-image:linear-gradient(rgba(255,255,255,.02) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.02) 1px,transparent 1px);background-size:50px 50px;pointer-events:none}

/* Lueur centrale */
body::after{content:'';position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:600px;height:600px;background:radial-gradient(circle,rgba(201,168,76,.05) 0%,transparent 70%);pointer-events:none}

.content{position:relative;z-index:1;max-width:480px}

/* 404 géant */
.num-404{
    font-family:'Syne',sans-serif;
    font-size:clamp(100px,20vw,160px);
    font-weight:800;
    line-height:1;
    letter-spacing:-6px;
    background:linear-gradient(180deg,#c9a84c 0%,rgba(201,168,76,.2) 100%);
    -webkit-background-clip:text;
    -webkit-text-fill-color:transparent;
    background-clip:text;
    margin-bottom:.5rem;
    animation:fadeUp .6s ease both;
}

h1{
    font-family:'Syne',sans-serif;
    font-size:clamp(20px,4vw,28px);
    font-weight:800;
    color:#e6edf3;
    letter-spacing:-.5px;
    margin-bottom:.75rem;
    animation:fadeUp .6s .1s ease both;
}

p{
    font-size:15px;color:#8b949e;line-height:1.7;
    margin-bottom:2.5rem;font-weight:300;
    animation:fadeUp .6s .2s ease both;
}

.actions{
    display:flex;gap:10px;justify-content:center;flex-wrap:wrap;
    animation:fadeUp .6s .3s ease both;
}
.btn-gold{background:#c9a84c;color:#0d1117;font-family:'DM Sans',sans-serif;font-weight:700;font-size:14px;padding:12px 24px;border-radius:10px;text-decoration:none;transition:opacity .2s}
.btn-gold:hover{opacity:.9}
.btn-gold:focus-visible{outline:2px solid #c9a84c;outline-offset:3px}
.btn-outline{background:transparent;color:#e6edf3;font-family:'DM Sans',sans-serif;font-weight:500;font-size:14px;padding:12px 24px;border-radius:10px;text-decoration:none;border:1px solid rgba(255,255,255,.1);transition:all .2s}
.btn-outline:hover{border-color:rgba(255,255,255,.2);background:rgba(255,255,255,.04)}
.btn-outline:focus-visible{outline:2px solid #c9a84c;outline-offset:3px}

/* Logo en haut */
.logo{position:absolute;top:2rem;left:50%;transform:translateX(-50%);font-family:'Syne',sans-serif;font-size:16px;font-weight:800;color:#c9a84c;text-decoration:none;white-space:nowrap}
.logo span{color:#e6edf3}
.logo:focus-visible{outline:2px solid #c9a84c;outline-offset:3px;border-radius:4px}

/* Liens rapides */
.quick-links{margin-top:3rem;display:flex;flex-wrap:wrap;gap:8px;justify-content:center;animation:fadeUp .6s .4s ease both}
.quick-link{font-size:13px;color:#484f58;text-decoration:none;padding:6px 14px;border:1px solid rgba(255,255,255,.06);border-radius:99px;transition:all .2s}
.quick-link:hover{color:#8b949e;border-color:rgba(255,255,255,.12)}
.quick-link:focus-visible{outline:2px solid #c9a84c;outline-offset:2px}

@keyframes fadeUp{from{opacity:0;transform:translateY(16px)}to{opacity:1;transform:translateY(0)}}
</style>
</head>
<body>

<a href="{{ url('/') }}" class="logo" aria-label="BimoTech Immo — Retour à l'accueil">Bimo<span>Tech</span></a>

<div class="content">
    <div class="num-404" aria-hidden="true">404</div>
    <h1>Page introuvable</h1>
    <p>La page que vous cherchez n'existe pas ou a été déplacée. Revenez à l'accueil ou utilisez les liens ci-dessous.</p>

    <div class="actions">
        <a href="{{ url('/') }}" class="btn-gold">← Retour à l'accueil</a>
        <a href="{{ route('contact') }}" class="btn-outline">Nous contacter</a>
    </div>

    <nav class="quick-links" aria-label="Liens rapides">
        <a href="{{ route('faq') }}"     class="quick-link">FAQ</a>
        <a href="{{ route('demo') }}"    class="quick-link">Demander une démo</a>
        <a href="{{ route('login') }}"   class="quick-link">Connexion</a>
        <a href="{{ route('register') }}" class="quick-link">Créer un compte</a>
    </nav>
</div>

</body>
</html>