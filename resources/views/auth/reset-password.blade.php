<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Réinitialiser le mot de passe — BimoTech Immo</title>
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
.left-title{font-family:'Syne',sans-serif;font-size:clamp(26px,3vw,36px);font-weight:800;color:#e6edf3;line-height:1.15;letter-spacing:-1px;margin-bottom:1.25rem}
.left-title em{font-style:normal;color:#c9a84c}
.left-sub{font-size:14px;color:#8b949e;line-height:1.7;max-width:340px;font-weight:300}
.left-stats{display:flex;gap:2rem;position:relative;z-index:1}
.left-stat-num{font-family:'Syne',sans-serif;font-size:24px;font-weight:800;color:#c9a84c}
.left-stat-label{font-size:12px;color:#484f58;margin-top:2px}

.right{display:flex;align-items:center;justify-content:center;padding:3rem 2rem;overflow-y:auto}
.form-box{width:100%;max-width:380px}
.form-title{font-family:'Syne',sans-serif;font-size:24px;font-weight:800;color:#e6edf3;letter-spacing:-.5px;margin-bottom:6px}
.form-subtitle{font-size:13.5px;color:#8b949e;margin-bottom:2.5rem;line-height:1.6}

.field{margin-bottom:1.15rem}
label{display:block;font-size:12.5px;font-weight:500;color:#8b949e;margin-bottom:5px;letter-spacing:.3px}
.input-wrap{position:relative}
input[type=email],input[type=password],input[type=text]{
    width:100%;background:#161b22;border:1px solid rgba(255,255,255,.1);border-radius:10px;
    padding:12px 14px;font-family:'DM Sans',sans-serif;font-size:14px;color:#e6edf3;
    outline:none;transition:border-color .2s,background .2s,box-shadow .2s;-webkit-appearance:none;
}
input:focus{border-color:#c9a84c;background:#1c2128;box-shadow:0 0 0 3px rgba(201,168,76,.08)}
input::placeholder{color:#484f58}
input.is-error{border-color:rgba(226,75,74,.5)!important}
input.is-error:focus{box-shadow:0 0 0 3px rgba(226,75,74,.08)!important}
input.has-toggle{padding-right:44px}

.toggle-pw{position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;padding:4px;color:#484f58;transition:color .2s;border-radius:4px;display:flex;align-items:center;line-height:1}
.toggle-pw:hover{color:#8b949e}
.toggle-pw:focus-visible{outline:2px solid #c9a84c;outline-offset:2px;color:#8b949e}

.btn-submit{
    width:100%;background:#c9a84c;color:#0d1117;
    font-family:'DM Sans',sans-serif;font-size:14px;font-weight:700;
    padding:13px;border-radius:10px;border:none;cursor:pointer;
    transition:opacity .2s,transform .1s;letter-spacing:.2px;
    display:flex;align-items:center;justify-content:center;gap:8px;
    margin-top:1.5rem;
}
.btn-submit:hover:not(:disabled){opacity:.9}
.btn-submit:active:not(:disabled){transform:scale(.99)}
.btn-submit:disabled{opacity:.6;cursor:not-allowed}
.btn-submit:focus-visible{outline:2px solid #c9a84c;outline-offset:3px}
.spinner{width:16px;height:16px;border:2px solid rgba(13,17,23,.25);border-top-color:#0d1117;border-radius:50%;animation:spin .7s linear infinite;display:none;flex-shrink:0}
@keyframes spin{to{transform:rotate(360deg)}}
.btn-submit.loading .spinner{display:block}
.btn-submit.loading .btn-text{opacity:.7}

.error-bag{background:rgba(226,75,74,.08);border:1px solid rgba(226,75,74,.2);border-left:3px solid #E24B4A;border-radius:8px;padding:10px 14px;margin-bottom:1.25rem;animation:slideIn .25s ease}
.error-bag p{font-size:12.5px;color:#f0a0a0;line-height:1.6}
.input-error{font-size:12px;color:#f0a0a0;margin-top:4px}
@keyframes slideIn{from{opacity:0;transform:translateY(-6px)}to{opacity:1;transform:translateY(0)}}

.strength-bar{height:3px;border-radius:2px;margin-top:6px;background:rgba(255,255,255,.06);overflow:hidden}
.strength-fill{height:100%;width:0;border-radius:2px;transition:width .3s,background .3s}
.strength-label{font-size:11.5px;color:#484f58;margin-top:4px;min-height:16px;transition:color .3s}

a:focus-visible{outline:2px solid #c9a84c;outline-offset:2px;border-radius:4px}

@media(max-width:768px){body{grid-template-columns:1fr}.left{display:none}}
</style>
</head>
<body>

<div class="left" role="complementary" aria-label="BimoTech Immo">
    <div class="grid-deco" aria-hidden="true"></div>
    <a href="{{ url('/') }}" class="left-logo" aria-label="Retour à l'accueil BimoTech">Bimo<span>Tech</span></a>
    <div style="position:relative;z-index:1">
        <h2 class="left-title">Gérez votre agence<br><em>comme un pro</em></h2>
        <p class="left-sub">Biens, contrats, paiements, quittances — tout centralisé. Conforme TVA 18%, NINEA et loi 81-18.</p>
    </div>
    <div class="left-stats">
        <div><div class="left-stat-num">150+</div><div class="left-stat-label">Biens gérés</div></div>
        <div><div class="left-stat-num">12</div><div class="left-stat-label">Agences actives</div></div>
        <div><div class="left-stat-num">98%</div><div class="left-stat-label">Recouvrement</div></div>
    </div>
</div>

<main class="right">
    <div class="form-box">
        <h1 class="form-title">Nouveau mot de passe</h1>
        <p class="form-subtitle">Choisissez un mot de passe sécurisé pour votre compte.</p>

        @if($errors->any())
            <div class="error-bag" role="alert">
                @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('password.store') }}" id="reset-form" novalidate>
            @csrf
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div class="field">
                <label for="email">Adresse email</label>
                <input type="email" id="email" name="email"
                    value="{{ old('email', $request->email) }}"
                    placeholder="votre@agence.sn"
                    autocomplete="username"
                    aria-required="true"
                    class="{{ $errors->has('email') ? 'is-error' : '' }}"
                >
                @error('email')<div class="input-error" role="alert">{{ $message }}</div>@enderror
            </div>

            <div class="field">
                <label for="password">Nouveau mot de passe</label>
                <div class="input-wrap">
                    <input type="password" id="password" name="password"
                        placeholder="••••••••"
                        autocomplete="new-password"
                        aria-required="true"
                        class="has-toggle {{ $errors->has('password') ? 'is-error' : '' }}"
                        oninput="checkStrength(this.value)"
                    >
                    <button type="button" class="toggle-pw"
                        aria-label="Afficher le mot de passe" aria-pressed="false"
                        onclick="togglePw('password','eye1')">
                        <svg id="eye1" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                        </svg>
                    </button>
                </div>
                <div class="strength-bar"><div class="strength-fill" id="strength-fill"></div></div>
                <div class="strength-label" id="strength-label"></div>
                @error('password')<div class="input-error" role="alert">{{ $message }}</div>@enderror
            </div>

            <div class="field">
                <label for="password_confirmation">Confirmer le mot de passe</label>
                <div class="input-wrap">
                    <input type="password" id="password_confirmation" name="password_confirmation"
                        placeholder="••••••••"
                        autocomplete="new-password"
                        aria-required="true"
                        class="has-toggle {{ $errors->has('password_confirmation') ? 'is-error' : '' }}"
                    >
                    <button type="button" class="toggle-pw"
                        aria-label="Afficher le mot de passe" aria-pressed="false"
                        onclick="togglePw('password_confirmation','eye2')">
                        <svg id="eye2" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                        </svg>
                    </button>
                </div>
                @error('password_confirmation')<div class="input-error" role="alert">{{ $message }}</div>@enderror
            </div>

            <button type="submit" class="btn-submit" id="submit-btn">
                <div class="spinner" aria-hidden="true"></div>
                <span class="btn-text">Réinitialiser le mot de passe</span>
            </button>
        </form>
    </div>
</main>

<script>
function togglePw(inputId, iconId) {
    const input = document.getElementById(inputId);
    const btn   = input.nextElementSibling;
    const show  = input.type === 'password';
    input.type  = show ? 'text' : 'password';
    btn.setAttribute('aria-pressed', show ? 'true' : 'false');
    btn.setAttribute('aria-label', show ? 'Masquer le mot de passe' : 'Afficher le mot de passe');
    document.getElementById(iconId).innerHTML = show
        ? '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>'
        : '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
}

function checkStrength(val) {
    const fill  = document.getElementById('strength-fill');
    const label = document.getElementById('strength-label');
    if (!val) { fill.style.width='0'; label.textContent=''; return; }
    let score = 0;
    if (val.length >= 8)  score++;
    if (/[A-Z]/.test(val)) score++;
    if (/[0-9]/.test(val)) score++;
    if (/[^A-Za-z0-9]/.test(val)) score++;
    const levels = [
        {w:'25%', bg:'#E24B4A', txt:'Trop court'},
        {w:'50%', bg:'#e07b39', txt:'Faible'},
        {w:'75%', bg:'#c9a84c', txt:'Moyen'},
        {w:'100%',bg:'#3B6D11', txt:'Fort'},
    ];
    const lvl = levels[Math.max(0, score - 1)];
    fill.style.width = lvl.w;
    fill.style.background = lvl.bg;
    label.textContent = lvl.txt;
    label.style.color = lvl.bg;
}

document.getElementById('reset-form').addEventListener('submit', function() {
    const btn = document.getElementById('submit-btn');
    btn.classList.add('loading');
    btn.disabled = true;
    btn.querySelector('.btn-text').textContent = 'Réinitialisation...';
});
</script>

</body>
</html>
