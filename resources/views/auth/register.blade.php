<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Créer mon agence — BimoTech Immo</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@300;400;500&display=swap" media="print" onload="this.media='all'">
<noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@300;400;500&display=swap"></noscript>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'DM Sans',sans-serif;background:#0d1117;min-height:100vh;display:grid;grid-template-columns:1fr 1fr}
.left{background:#161b22;border-right:1px solid rgba(255,255,255,.06);display:flex;flex-direction:column;justify-content:space-between;padding:3rem;position:relative;overflow:hidden}
.left::before{content:'';position:absolute;top:-100px;left:-100px;width:500px;height:500px;background:radial-gradient(circle,rgba(201,168,76,.06) 0%,transparent 70%);pointer-events:none}
.grid-deco{position:absolute;inset:0;background-image:linear-gradient(rgba(255,255,255,.025) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.025) 1px,transparent 1px);background-size:40px 40px;pointer-events:none}
.left-logo{font-family:'Syne',sans-serif;font-size:20px;font-weight:800;color:#c9a84c;text-decoration:none;letter-spacing:-.5px}
.left-logo span{color:#e6edf3}
.left-title{font-family:'Syne',sans-serif;font-size:clamp(22px,2.5vw,32px);font-weight:800;color:#e6edf3;line-height:1.15;letter-spacing:-1px;margin-bottom:1.25rem}
.left-title em{font-style:normal;color:#c9a84c}
.left-sub{font-size:14px;color:#8b949e;line-height:1.7;font-weight:300;max-width:320px;margin-bottom:2rem}
.perks{display:flex;flex-direction:column;gap:12px;position:relative;z-index:1}
.perk{display:flex;gap:12px;align-items:flex-start}
.perk-check{width:20px;height:20px;background:rgba(201,168,76,.12);border-radius:6px;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:1px}
.perk-title{font-size:13px;font-weight:500;color:#e6edf3;margin-bottom:2px}
.perk-desc{font-size:12px;color:#484f58;line-height:1.4}
.left-footer{font-size:12px;color:#484f58;position:relative;z-index:1}
.left-footer a{color:#c9a84c;text-decoration:none}

.right{display:flex;align-items:center;justify-content:center;padding:2.5rem 2rem;overflow-y:auto}
.form-box{width:100%;max-width:420px}
.form-title{font-family:'Syne',sans-serif;font-size:24px;font-weight:800;color:#e6edf3;letter-spacing:-.5px;margin-bottom:6px}
.form-subtitle{font-size:13.5px;color:#8b949e;margin-bottom:2rem}
.form-subtitle a{color:#c9a84c;text-decoration:none}
.form-subtitle a:hover{text-decoration:underline}

.form-section{font-size:11px;color:#484f58;font-weight:600;text-transform:uppercase;letter-spacing:1.5px;margin:1.5rem 0 1rem;padding-bottom:8px;border-bottom:1px solid rgba(255,255,255,.06)}

.field{margin-bottom:1rem}
label{display:block;font-size:12.5px;font-weight:500;color:#8b949e;margin-bottom:5px;letter-spacing:.3px}
.input-wrap{position:relative}
input[type=email],input[type=password],input[type=text],input[type=tel]{
    width:100%;background:#161b22;border:1px solid rgba(255,255,255,.1);border-radius:10px;
    padding:11px 14px;font-family:'DM Sans',sans-serif;font-size:14px;color:#e6edf3;
    outline:none;transition:border-color .2s,background .2s,box-shadow .2s;-webkit-appearance:none;
}
input:focus{border-color:#c9a84c;background:#1c2128;box-shadow:0 0 0 3px rgba(201,168,76,.08)}
input::placeholder{color:#484f58}
input.is-error{border-color:rgba(226,75,74,.5)!important}
input.is-valid{border-color:rgba(59,109,17,.5)!important}
input.has-toggle{padding-right:44px}

/* Toggle password */
.toggle-pw{position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;padding:4px;color:#484f58;transition:color .2s;border-radius:4px;display:flex;align-items:center}
.toggle-pw:hover{color:#8b949e}
.toggle-pw:focus-visible{outline:2px solid #c9a84c;outline-offset:2px}

.row-2{display:grid;grid-template-columns:1fr 1fr;gap:12px}
.input-error{font-size:12px;color:#f0a0a0;margin-top:4px}
.input-hint{font-size:12px;color:#484f58;margin-top:4px}

/* Force mot de passe */
.pw-strength{margin-top:8px}
.pw-bars{display:flex;gap:4px;margin-bottom:5px}
.pw-bar{flex:1;height:3px;border-radius:99px;background:rgba(255,255,255,.08);transition:background .3s}
.pw-label{font-size:11px;color:#484f58;transition:color .3s}
.strength-0 .pw-bar:nth-child(1){background:#E24B4A}
.strength-1 .pw-bar:nth-child(1){background:#E24B4A}
.strength-1 .pw-bar:nth-child(2){background:#E24B4A}
.strength-2 .pw-bar:nth-child(1),.strength-2 .pw-bar:nth-child(2){background:#BA7517}
.strength-2 .pw-bar:nth-child(3){background:#BA7517}
.strength-3 .pw-bar:nth-child(1),.strength-3 .pw-bar:nth-child(2),.strength-3 .pw-bar:nth-child(3){background:#3B6D11}
.strength-3 .pw-bar:nth-child(4){background:#3B6D11}
.strength-4 .pw-bar{background:#3B6D11}
.strength-0 .pw-label{color:#A32D2D}
.strength-1 .pw-label{color:#A32D2D}
.strength-2 .pw-label{color:#854F0B}
.strength-3 .pw-label,.strength-4 .pw-label{color:#3B6D11}

/* Validation temps réel */
.field-valid::after{content:'✓';position:absolute;right:12px;top:50%;transform:translateY(-50%);color:#3B6D11;font-size:14px;font-weight:700;pointer-events:none}
.field-valid input{padding-right:36px}

/* CGU */
.cgu-row{display:flex;align-items:flex-start;gap:10px;margin:1.25rem 0}
input[type=checkbox]{width:16px;height:16px;accent-color:#c9a84c;cursor:pointer;flex-shrink:0;margin-top:2px}
.cgu-label{font-size:12.5px;color:#8b949e;line-height:1.5}
.cgu-label a{color:#c9a84c;text-decoration:none}
.cgu-label a:hover{text-decoration:underline}

/* Submit */
.btn-submit{
    width:100%;background:#c9a84c;color:#0d1117;
    font-family:'DM Sans',sans-serif;font-size:14px;font-weight:700;
    padding:13px;border-radius:10px;border:none;cursor:pointer;
    transition:opacity .2s,transform .1s;letter-spacing:.2px;
    display:flex;align-items:center;justify-content:center;gap:8px;
}
.btn-submit:hover:not(:disabled){opacity:.9}
.btn-submit:active:not(:disabled){transform:scale(.99)}
.btn-submit:disabled{opacity:.6;cursor:not-allowed}
.btn-submit:focus-visible{outline:2px solid #c9a84c;outline-offset:3px}
.spinner{width:16px;height:16px;border:2px solid rgba(13,17,23,.25);border-top-color:#0d1117;border-radius:50%;animation:spin .7s linear infinite;display:none;flex-shrink:0}
@keyframes spin{to{transform:rotate(360deg)}}
.btn-submit.loading .spinner{display:block}
.btn-submit.loading .btn-text{opacity:.7}

.to-login{text-align:center;font-size:13.5px;color:#8b949e;margin-top:1.25rem}
.to-login a{color:#c9a84c;text-decoration:none;font-weight:500}
.to-login a:hover{text-decoration:underline}

.error-bag{background:rgba(226,75,74,.08);border:1px solid rgba(226,75,74,.2);border-left:3px solid #E24B4A;border-radius:8px;padding:10px 14px;margin-bottom:1.25rem;animation:slideIn .25s ease}
.error-bag p{font-size:12.5px;color:#f0a0a0;line-height:1.6}
@keyframes slideIn{from{opacity:0;transform:translateY(-6px)}to{opacity:1;transform:translateY(0)}}

a:focus-visible{outline:2px solid #c9a84c;outline-offset:2px;border-radius:4px}

@media(max-width:768px){body{grid-template-columns:1fr}.left{display:none}.row-2{grid-template-columns:1fr}}
</style>
</head>
<body>

<div class="left" role="complementary">
    <div class="grid-deco" aria-hidden="true"></div>
    <a href="{{ url('/') }}" class="left-logo" aria-label="BimoTech Immo — Retour à l'accueil">Bimo<span>Tech</span></a>
    <div style="position:relative;z-index:1">
        <h2 class="left-title">Votre agence en ligne<br>en <em>moins de 10 min</em></h2>
        <p class="left-sub">Rejoignez les agences sénégalaises qui gèrent leur activité avec BimoTech.</p>
        <div class="perks">
            <div class="perk">
                <div class="perk-check" aria-hidden="true"><svg width="10" height="10" viewBox="0 0 12 12" fill="none" stroke="#c9a84c" stroke-width="2.5"><polyline points="2,6 5,9 10,3"/></svg></div>
                <div><div class="perk-title">Conformité fiscale incluse</div><div class="perk-desc">TVA 18%, NINEA, loi 81-18, TOM — automatiquement.</div></div>
            </div>
            <div class="perk">
                <div class="perk-check" aria-hidden="true"><svg width="10" height="10" viewBox="0 0 12 12" fill="none" stroke="#c9a84c" stroke-width="2.5"><polyline points="2,6 5,9 10,3"/></svg></div>
                <div><div class="perk-title">Quittances PDF légales</div><div class="perk-desc">Générées et archivées automatiquement.</div></div>
            </div>
            <div class="perk">
                <div class="perk-check" aria-hidden="true"><svg width="10" height="10" viewBox="0 0 12 12" fill="none" stroke="#c9a84c" stroke-width="2.5"><polyline points="2,6 5,9 10,3"/></svg></div>
                <div><div class="perk-title">Gratuit jusqu'à 5 biens</div><div class="perk-desc">Aucune carte bancaire requise.</div></div>
            </div>
        </div>
    </div>
    <div class="left-footer">Déjà un compte ? <a href="{{ route('login') }}">Se connecter →</a></div>
</div>

<main class="right">
    <div class="form-box">
        <h1 class="form-title">Créer mon agence</h1>
        <p class="form-subtitle">Déjà inscrit ? <a href="{{ route('login') }}">Se connecter</a></p>

        @if($errors->any())
            <div class="error-bag" role="alert">
                @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}" id="register-form" novalidate>
            @csrf

            <div class="form-section">Votre agence</div>

            <div class="field">
                <label for="agency_nom">Nom de l'agence *</label>
                <input type="text" id="agency_nom" name="agency_nom"
                    value="{{ old('agency_nom') }}"
                    placeholder="Agence Immobilière Diallo"
                    aria-required="true"
                    class="{{ $errors->has('agency_nom') ? 'is-error' : '' }}"
                >
                @error('agency_nom')<div class="input-error" role="alert">{{ $message }}</div>@enderror
            </div>

            <div class="row-2">
                <div class="field">
                    <label for="agency_ninea">NINEA</label>
                    <input type="text" id="agency_ninea" name="agency_ninea"
                        value="{{ old('agency_ninea') }}" placeholder="1234567A"
                        class="{{ $errors->has('agency_ninea') ? 'is-error' : '' }}"
                    >
                    @error('agency_ninea')<div class="input-error" role="alert">{{ $message }}</div>@enderror
                </div>
                <div class="field">
                    <label for="agency_telephone">Téléphone</label>
                    <input type="tel" id="agency_telephone" name="agency_telephone"
                        value="{{ old('agency_telephone') }}" placeholder="+221 77 000 00 00"
                        autocomplete="tel"
                        class="{{ $errors->has('agency_telephone') ? 'is-error' : '' }}"
                    >
                    @error('agency_telephone')<div class="input-error" role="alert">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-section">Votre compte administrateur</div>

            <div class="row-2">
                <div class="field">
                    <label for="prenom">Prénom *</label>
                    <input type="text" id="prenom" name="prenom"
                        value="{{ old('prenom') }}" placeholder="Amadou"
                        autocomplete="given-name" aria-required="true"
                        class="{{ $errors->has('prenom') ? 'is-error' : '' }}"
                    >
                    @error('prenom')<div class="input-error" role="alert">{{ $message }}</div>@enderror
                </div>
                <div class="field">
                    <label for="nom">Nom *</label>
                    <input type="text" id="nom" name="nom"
                        value="{{ old('nom') }}" placeholder="Diallo"
                        autocomplete="family-name" aria-required="true"
                        class="{{ $errors->has('nom') ? 'is-error' : '' }}"
                    >
                    @error('nom')<div class="input-error" role="alert">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="field">
                <label for="email">Adresse email *</label>
                <input type="email" id="email" name="email"
                    value="{{ old('email') }}" placeholder="amadou@agence.sn"
                    autocomplete="email" aria-required="true"
                    class="{{ $errors->has('email') ? 'is-error' : '' }}"
                >
                @error('email')<div class="input-error" role="alert">{{ $message }}</div>@enderror
            </div>

            <div class="field">
                <label for="password">Mot de passe *</label>
                <div class="input-wrap">
                    <input type="password" id="password" name="password"
                        placeholder="8 caractères minimum"
                        autocomplete="new-password" aria-required="true"
                        aria-describedby="pw-strength-label"
                        class="has-toggle {{ $errors->has('password') ? 'is-error' : '' }}"
                        oninput="checkStrength(this.value)"
                    >
                    <button type="button" class="toggle-pw" id="toggle-pw1"
                        aria-label="Afficher le mot de passe" aria-pressed="false"
                        onclick="togglePw('password','toggle-pw1','eye1')">
                        <svg id="eye1" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                        </svg>
                    </button>
                </div>
                @error('password')<div class="input-error" role="alert">{{ $message }}</div>@enderror
                <div class="pw-strength" id="pw-strength" aria-live="polite">
                    <div class="pw-bars" aria-hidden="true">
                        <div class="pw-bar"></div><div class="pw-bar"></div>
                        <div class="pw-bar"></div><div class="pw-bar"></div>
                    </div>
                    <div class="pw-label" id="pw-strength-label"></div>
                </div>
            </div>

            <div class="field">
                <label for="password_confirmation">Confirmer le mot de passe *</label>
                <div class="input-wrap">
                    <input type="password" id="password_confirmation" name="password_confirmation"
                        placeholder="Répéter le mot de passe"
                        autocomplete="new-password" aria-required="true"
                        class="has-toggle"
                        oninput="checkMatch()"
                    >
                    <button type="button" class="toggle-pw" id="toggle-pw2"
                        aria-label="Afficher la confirmation" aria-pressed="false"
                        onclick="togglePw('password_confirmation','toggle-pw2','eye2')">
                        <svg id="eye2" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                        </svg>
                    </button>
                </div>
                <div id="match-hint" class="input-hint" aria-live="polite"></div>
            </div>

            <div class="cgu-row">
                <input type="checkbox" id="cgu" name="cgu" value="1" {{ old('cgu') ? 'checked' : '' }} aria-required="true">
                <label for="cgu" class="cgu-label">
                    J'accepte les <a href="{{ route('mentions-legales') }}" target="_blank">conditions d'utilisation</a>
                    et la <a href="{{ route('confidentialite') }}" target="_blank">politique de confidentialité</a>.
                </label>
            </div>
            @error('cgu')<div class="input-error" role="alert" style="margin-top:-8px;margin-bottom:10px">{{ $message }}</div>@enderror

            <button type="submit" class="btn-submit" id="submit-btn">
                <div class="spinner" aria-hidden="true"></div>
                <span class="btn-text">Créer mon agence gratuitement →</span>
            </button>
        </form>

        <p class="to-login">Déjà un compte ? <a href="{{ route('login') }}">Se connecter</a></p>
    </div>
</main>

<script>
// Toggle mot de passe
function togglePw(inputId, btnId, iconId) {
    const input = document.getElementById(inputId);
    const btn   = document.getElementById(btnId);
    const show  = input.type === 'password';
    input.type  = show ? 'text' : 'password';
    btn.setAttribute('aria-pressed', show ? 'true' : 'false');
    btn.setAttribute('aria-label', show ? 'Masquer' : 'Afficher');
    document.getElementById(iconId).innerHTML = show
        ? '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>'
        : '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
}

// Force du mot de passe
function checkStrength(pw) {
    const el = document.getElementById('pw-strength');
    const label = document.getElementById('pw-strength-label');
    if (!pw) { el.className = 'pw-strength'; label.textContent = ''; return; }
    let score = 0;
    if (pw.length >= 8)  score++;
    if (pw.length >= 12) score++;
    if (/[A-Z]/.test(pw) && /[a-z]/.test(pw)) score++;
    if (/[0-9]/.test(pw)) score++;
    if (/[^A-Za-z0-9]/.test(pw)) score++;
    score = Math.min(score, 4);
    const labels = ['', 'Trop faible', 'Faible', 'Bon', 'Excellent'];
    el.className = 'pw-strength strength-' + score;
    label.textContent = score > 0 ? labels[score] : '';
    checkMatch();
}

// Correspondance des mots de passe
function checkMatch() {
    const pw1  = document.getElementById('password').value;
    const pw2  = document.getElementById('password_confirmation').value;
    const hint = document.getElementById('match-hint');
    if (!pw2) { hint.textContent = ''; hint.style.color = ''; return; }
    if (pw1 === pw2) {
        hint.textContent = '✓ Les mots de passe correspondent';
        hint.style.color = '#3B6D11';
    } else {
        hint.textContent = 'Les mots de passe ne correspondent pas';
        hint.style.color = '#f0a0a0';
    }
}

// Loading state
document.getElementById('register-form').addEventListener('submit', function() {
    const btn = document.getElementById('submit-btn');
    btn.classList.add('loading');
    btn.disabled = true;
    btn.querySelector('.btn-text').textContent = 'Création en cours...';
});
</script>

</body>
</html>