<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="index, follow">
<link rel="canonical" href="https://immo.bimotechsn.com/contact">
<title>Contact — BimoTech Immo</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<style>
*{box-sizing:border-box;margin:0;padding:0}
:root{--gold:#c9a84c;--dark:#0d1117;--dark2:#161b22;--dark3:#21262d;--text:#e6edf3;--muted:#8b949e;--border:rgba(255,255,255,.08)}
html{scroll-behavior:smooth}
body{font-family:'DM Sans',sans-serif;background:var(--dark);color:var(--text)}

nav{position:fixed;top:0;left:0;right:0;z-index:100;padding:0 5%;height:64px;display:flex;align-items:center;justify-content:space-between;background:rgba(13,17,23,.85);backdrop-filter:blur(12px);border-bottom:1px solid var(--border)}
.nav-logo{font-family:'Syne',sans-serif;font-size:18px;font-weight:800;color:var(--gold);text-decoration:none}
.nav-logo span{color:var(--text)}
.nav-back{font-size:13px;color:var(--muted);text-decoration:none;transition:color .2s}
.nav-back:hover{color:var(--text)}

.hero{padding:120px 5% 5rem;text-align:center;position:relative;overflow:hidden}
.hero::before{content:'';position:absolute;top:-20%;left:50%;transform:translateX(-50%);width:600px;height:600px;background:radial-gradient(circle,rgba(201,168,76,.06) 0%,transparent 70%);pointer-events:none}
.section-tag{font-size:11px;color:var(--gold);font-weight:600;letter-spacing:2px;text-transform:uppercase;margin-bottom:1rem}
h1{font-family:'Syne',sans-serif;font-size:clamp(30px,5vw,52px);font-weight:800;letter-spacing:-1.5px;line-height:1.1;margin-bottom:1rem}
h1 em{font-style:normal;color:var(--gold)}
.hero-sub{font-size:15px;color:var(--muted);max-width:480px;margin:0 auto;line-height:1.7;font-weight:300}

.main{max-width:1000px;margin:0 auto;padding:0 5% 6rem;display:grid;grid-template-columns:1fr 1.4fr;gap:3rem;align-items:start}

/* Infos contact */
.contact-info{display:flex;flex-direction:column;gap:1.25rem}
.info-card{background:var(--dark2);border:1px solid var(--border);border-radius:14px;padding:1.5rem;display:flex;gap:14px;align-items:flex-start;transition:border-color .2s}
.info-card:hover{border-color:rgba(201,168,76,.25)}
.info-icon{width:40px;height:40px;background:rgba(201,168,76,.1);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.info-label{font-size:11.5px;color:var(--muted);margin-bottom:4px;text-transform:uppercase;letter-spacing:.5px;font-weight:500}
.info-value{font-size:14px;color:var(--text);font-weight:500}
.info-value a{color:var(--text);text-decoration:none}
.info-value a:hover{color:var(--gold)}
.info-note{font-size:12px;color:#484f58;margin-top:3px}

/* WhatsApp CTA */
.whatsapp-card{background:rgba(37,211,102,.08);border:1px solid rgba(37,211,102,.2);border-radius:14px;padding:1.5rem;display:flex;gap:14px;align-items:center;text-decoration:none;transition:background .2s}
.whatsapp-card:hover{background:rgba(37,211,102,.12)}
.wa-icon{width:44px;height:44px;background:#25d366;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.wa-title{font-size:14px;font-weight:600;color:#25d366;margin-bottom:3px}
.wa-sub{font-size:12px;color:#8b949e}

/* Formulaire */
.form-card{background:var(--dark2);border:1px solid var(--border);border-radius:16px;padding:2.5rem}
.form-title{font-family:'Syne',sans-serif;font-size:18px;font-weight:700;color:var(--text);margin-bottom:1.75rem}

.field{margin-bottom:1.1rem}
label{display:block;font-size:12.5px;font-weight:500;color:var(--muted);margin-bottom:5px;letter-spacing:.3px}
input[type=text],input[type=email],input[type=tel],select,textarea{
    width:100%;background:var(--dark);border:1px solid rgba(255,255,255,.1);border-radius:10px;
    padding:11px 14px;font-family:'DM Sans',sans-serif;font-size:14px;color:var(--text);
    outline:none;transition:border-color .2s;-webkit-appearance:none;
}
input:focus,select:focus,textarea:focus{border-color:var(--gold);background:#1c2128}
input::placeholder,textarea::placeholder{color:#484f58}
select{cursor:pointer}
select option{background:var(--dark2)}
textarea{resize:vertical;min-height:110px;line-height:1.6}

.row-2{display:grid;grid-template-columns:1fr 1fr;gap:12px}

.btn-submit{
    width:100%;background:var(--gold);color:var(--dark);
    font-family:'DM Sans',sans-serif;font-size:14px;font-weight:700;
    padding:13px;border-radius:10px;border:none;cursor:pointer;
    transition:opacity .2s;margin-top:.5rem;letter-spacing:.2px;
}
.btn-submit:hover{opacity:.9}

.success-msg{background:rgba(59,109,17,.1);border:1px solid rgba(59,109,17,.2);border-left:3px solid #3B6D11;border-radius:8px;padding:12px 16px;font-size:13px;color:#86d066;margin-bottom:1.25rem}
.error-bag{background:rgba(226,75,74,.08);border:1px solid rgba(226,75,74,.2);border-left:3px solid #E24B4A;border-radius:8px;padding:10px 14px;margin-bottom:1.25rem}
.error-bag p{font-size:12.5px;color:#f0a0a0;line-height:1.6}
.input-error{font-size:12px;color:#f0a0a0;margin-top:4px}

footer{padding:2rem 5%;border-top:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem}
.footer-logo{font-family:'Syne',sans-serif;font-size:15px;font-weight:800;color:var(--gold)}
.footer-links{display:flex;gap:1.5rem}
.footer-links a{font-size:12px;color:var(--muted);text-decoration:none}
.footer-links a:hover{color:var(--text)}
.footer-copy{font-size:12px;color:#484f58}

@media(max-width:768px){
    .main{grid-template-columns:1fr}
    .row-2{grid-template-columns:1fr}
    footer{flex-direction:column;text-align:center}
}
</style>
</head>
<body>

@include('partials.public-nav', ['active' => 'contact'])

<div class="hero">
    <div class="section-tag">Contact</div>
    <h1>Parlons de votre <em>agence</em></h1>
    <p class="hero-sub">Une question, une démo, un devis ? On vous répond dans la journée.</p>
</div>

<div class="main">

    <!-- Infos de contact -->
    <div class="contact-info">

        <a href="https://wa.me/221XXXXXXXXX?text=Bonjour BimoTech, je souhaite en savoir plus sur votre solution." target="_blank" class="whatsapp-card">
            <div class="wa-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="white"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.890-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
            </div>
            <div>
                <div class="wa-title">Discuter sur WhatsApp</div>
                <div class="wa-sub">Réponse rapide · Lun–Sam, 8h–20h</div>
            </div>
        </a>

        <div class="info-card">
            <div class="info-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#c9a84c" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
            </div>
            <div>
                <div class="info-label">Email</div>
                <div class="info-value"><a href="mailto:contact@bimotech.sn">contact@bimotech.sn</a></div>
                <div class="info-note">Réponse sous 24h ouvrées</div>
            </div>
        </div>

        <div class="info-card">
            <div class="info-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#c9a84c" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
            </div>
            <div>
                <div class="info-label">Adresse</div>
                <div class="info-value">Dakar, Sénégal</div>
                <div class="info-note">Démos sur site disponibles à Dakar</div>
            </div>
        </div>

        <div class="info-card">
            <div class="info-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#c9a84c" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12,6 12,12 16,14"/></svg>
            </div>
            <div>
                <div class="info-label">Horaires</div>
                <div class="info-value">Lundi – Samedi</div>
                <div class="info-note">8h00 – 20h00 (GMT)</div>
            </div>
        </div>

    </div>

    <!-- Formulaire -->
    <div class="form-card">
        <div class="form-title">Envoyer un message</div>

        @if(session('success'))
            <div class="success-msg">✓ Message envoyé ! Nous vous répondrons dans les 24h.</div>
        @endif

        @if($errors->any())
            <div class="error-bag">
                @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('contact.send') }}">
            @csrf

            <div class="row-2">
                <div class="field">
                    <label>Prénom *</label>
                    <input type="text" name="prenom" value="{{ old('prenom') }}" placeholder="Amadou">
                    @error('prenom')<div class="input-error">{{ $message }}</div>@enderror
                </div>
                <div class="field">
                    <label>Nom *</label>
                    <input type="text" name="nom" value="{{ old('nom') }}" placeholder="Diallo">
                    @error('nom')<div class="input-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="field">
                <label>Nom de votre agence *</label>
                <input type="text" name="agence" value="{{ old('agence') }}" placeholder="Agence Immobilière Diallo">
                @error('agence')<div class="input-error">{{ $message }}</div>@enderror
            </div>

            <div class="row-2">
                <div class="field">
                    <label>Email *</label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="amadou@agence.sn">
                    @error('email')<div class="input-error">{{ $message }}</div>@enderror
                </div>
                <div class="field">
                    <label>Téléphone</label>
                    <input type="tel" name="telephone" value="{{ old('telephone') }}" placeholder="+221 77 000 00 00">
                </div>
            </div>

            <div class="field">
                <label>Objet *</label>
                <select name="objet">
                    <option value="" disabled {{ old('objet') ? '' : 'selected' }}>Sélectionner un objet</option>
                    <option value="demo"      {{ old('objet') === 'demo'      ? 'selected' : '' }}>Demander une démo</option>
                    <option value="tarif"     {{ old('objet') === 'tarif'     ? 'selected' : '' }}>Question sur les tarifs</option>
                    <option value="technique" {{ old('objet') === 'technique' ? 'selected' : '' }}>Question technique</option>
                    <option value="reseau"    {{ old('objet') === 'reseau'    ? 'selected' : '' }}>Plan Réseau / multi-agences</option>
                    <option value="autre"     {{ old('objet') === 'autre'     ? 'selected' : '' }}>Autre</option>
                </select>
                @error('objet')<div class="input-error">{{ $message }}</div>@enderror
            </div>

            <div class="field">
                <label>Message *</label>
                <textarea name="message" placeholder="Décrivez votre besoin, le nombre de biens que vous gérez, votre ville...">{{ old('message') }}</textarea>
                @error('message')<div class="input-error">{{ $message }}</div>@enderror
            </div>

            <button type="submit" class="btn-submit">Envoyer le message →</button>
        </form>
    </div>

</div>

<footer>
    <div class="footer-logo">BimoTech Immo</div>
    <div class="footer-links">
        <a href="{{ url('/') }}">Accueil</a>
        <a href="{{ route('mentions-legales') }}">Mentions légales</a>
        <a href="{{ route('confidentialite') }}">Confidentialité</a>
    </div>
    <div class="footer-copy">© {{ date('Y') }} BimoTech · Dakar, Sénégal</div>
</footer>

</body>
</html>