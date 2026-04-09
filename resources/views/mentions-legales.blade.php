<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Mentions légales — BimoTech Immo</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<style>
*{box-sizing:border-box;margin:0;padding:0}
:root{--gold:#c9a84c;--dark:#0d1117;--dark2:#161b22;--text:#e6edf3;--muted:#8b949e;--border:rgba(255,255,255,.08)}
body{font-family:'DM Sans',sans-serif;background:var(--dark);color:var(--text)}
nav{position:fixed;top:0;left:0;right:0;z-index:100;padding:0 5%;height:64px;display:flex;align-items:center;justify-content:space-between;background:rgba(13,17,23,.85);backdrop-filter:blur(12px);border-bottom:1px solid var(--border)}
.nav-logo{font-family:'Syne',sans-serif;font-size:18px;font-weight:800;color:var(--gold);text-decoration:none}
.nav-logo span{color:var(--text)}
.nav-back{font-size:13px;color:var(--muted);text-decoration:none;transition:color .2s}
.nav-back:hover{color:var(--text)}

.page{max-width:720px;margin:0 auto;padding:120px 5% 6rem}
.page-tag{font-size:11px;color:var(--gold);font-weight:600;letter-spacing:2px;text-transform:uppercase;margin-bottom:1rem}
h1{font-family:'Syne',sans-serif;font-size:clamp(26px,4vw,40px);font-weight:800;letter-spacing:-1px;margin-bottom:.5rem}
.update-date{font-size:13px;color:#484f58;margin-bottom:3rem}

h2{font-family:'Syne',sans-serif;font-size:17px;font-weight:700;color:var(--text);margin:2.5rem 0 .75rem;padding-bottom:8px;border-bottom:1px solid var(--border)}
p{font-size:14px;color:var(--muted);line-height:1.8;margin-bottom:.75rem}
a{color:var(--gold);text-decoration:none}
a:hover{text-decoration:underline}
strong{color:var(--text);font-weight:500}

.info-block{background:var(--dark2);border:1px solid var(--border);border-radius:12px;padding:1.25rem 1.5rem;margin-bottom:1rem}
.info-block p{margin-bottom:.35rem}
.info-block p:last-child{margin-bottom:0}

footer{padding:2rem 5%;border-top:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem;margin-top:2rem}
.footer-logo{font-family:'Syne',sans-serif;font-size:15px;font-weight:800;color:var(--gold)}
.footer-links{display:flex;gap:1.5rem}
.footer-links a{font-size:12px;color:var(--muted);text-decoration:none}
.footer-copy{font-size:12px;color:#484f58}
</style>
</head>
<body>

<nav>
    <a href="{{ url('/') }}" class="nav-logo">Bimo<span>Tech</span></a>
    <a href="{{ url('/') }}" class="nav-back">← Retour à l'accueil</a>
</nav>

<div class="page">
    <div class="page-tag">Légal</div>
    <h1>Mentions légales</h1>
    <p class="update-date">Dernière mise à jour : {{ date('d/m/Y') }}</p>

    <h2>1. Éditeur du site</h2>
    <div class="info-block">
        <p><strong>Dénomination sociale :</strong> BimoTech Immo</p>
        <p><strong>Forme juridique :</strong> [Forme juridique — ex : SARL, SAS]</p>
        <p><strong>NINEA :</strong> [Votre NINEA]</p>
        <p><strong>Siège social :</strong> Dakar, Sénégal</p>
        <p><strong>Email :</strong> <a href="mailto:contact@bimotech.sn">contact@bimotech.sn</a></p>
        <p><strong>Directeur de la publication :</strong> [Nom du responsable légal]</p>
    </div>

    <h2>2. Hébergement</h2>
    <div class="info-block">
        <p><strong>Hébergeur :</strong> [Nom de l'hébergeur]</p>
        <p><strong>Adresse :</strong> [Adresse de l'hébergeur]</p>
        <p><strong>Site :</strong> <a href="#" target="_blank">[URL hébergeur]</a></p>
    </div>

    <h2>3. Propriété intellectuelle</h2>
    <p>L'ensemble du contenu de la plateforme BimoTech Immo — textes, graphismes, logotypes, icônes, images, code source — est la propriété exclusive de BimoTech ou de ses partenaires et est protégé par les lois sénégalaises et internationales relatives à la propriété intellectuelle.</p>
    <p>Toute reproduction, représentation, modification, publication ou adaptation de tout ou partie des éléments du site, quel que soit le moyen ou le procédé utilisé, est interdite sans autorisation écrite préalable de BimoTech.</p>

    <h2>4. Données personnelles</h2>
    <p>Les données personnelles collectées via la plateforme (nom, email, informations d'agence) sont utilisées exclusivement dans le cadre de la fourniture du service BimoTech Immo.</p>
    <p>Conformément à la loi sénégalaise n°2008-12 du 25 janvier 2008 sur la protection des données à caractère personnel et au règlement de la Commission de Protection des Données Personnelles (CDP), vous disposez d'un droit d'accès, de rectification et de suppression de vos données.</p>
    <p>Pour exercer ces droits, contactez-nous à : <a href="mailto:contact@bimotech.sn">contact@bimotech.sn</a></p>
    <p>Pour en savoir plus, consultez notre <a href="{{ route('confidentialite') }}">politique de confidentialité</a>.</p>

    <h2>5. Cookies</h2>
    <p>La plateforme BimoTech utilise des cookies strictement nécessaires au fonctionnement du service (session, sécurité CSRF). Aucun cookie publicitaire ou de tracking tiers n'est utilisé.</p>

    <h2>6. Limitation de responsabilité</h2>
    <p>BimoTech s'efforce d'assurer l'exactitude et la mise à jour des informations diffusées. Toutefois, BimoTech ne peut garantir l'exactitude, la précision ou l'exhaustivité des informations mises à disposition.</p>
    <p>BimoTech ne pourra être tenu responsable des dommages directs ou indirects résultant de l'utilisation de la plateforme ou de l'impossibilité d'y accéder.</p>

    <h2>7. Droit applicable</h2>
    <p>Les présentes mentions légales sont soumises au droit sénégalais. Tout litige relatif à l'utilisation de la plateforme BimoTech relève de la compétence exclusive des tribunaux de Dakar, Sénégal.</p>

    <h2>8. Contact</h2>
    <p>Pour toute question relative aux présentes mentions légales : <a href="mailto:contact@bimotech.sn">contact@bimotech.sn</a></p>
</div>

<footer>
    <div class="footer-logo">BimoTech Immo</div>
    <div class="footer-links">
        <a href="{{ url('/') }}">Accueil</a>
        <a href="{{ route('contact') }}">Contact</a>
        <a href="{{ route('confidentialite') }}">Confidentialité</a>
    </div>
    <div class="footer-copy">© {{ date('Y') }} BimoTech · Dakar, Sénégal</div>
</footer>

</body>
</html>