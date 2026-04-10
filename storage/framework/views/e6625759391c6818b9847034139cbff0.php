<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Connexion — BimoTech Immo</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
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
.form-subtitle{font-size:13.5px;color:#8b949e;margin-bottom:2.5rem}
.form-subtitle a{color:#c9a84c;text-decoration:none}
.form-subtitle a:hover{text-decoration:underline}

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

/* Toggle password */
.toggle-pw{position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;padding:4px;color:#484f58;transition:color .2s;border-radius:4px;display:flex;align-items:center;line-height:1}
.toggle-pw:hover{color:#8b949e}
.toggle-pw:focus-visible{outline:2px solid #c9a84c;outline-offset:2px;color:#8b949e}

/* Remember */
.remember-row{display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem}
.check-label{display:flex;align-items:center;gap:8px;font-size:13px;color:#8b949e;cursor:pointer;user-select:none}
.check-label input[type=checkbox]{width:16px;height:16px;accent-color:#c9a84c;cursor:pointer;flex-shrink:0}
.forgot{font-size:13px;color:#c9a84c;text-decoration:none}
.forgot:hover{text-decoration:underline}
.forgot:focus-visible{outline:2px solid #c9a84c;outline-offset:2px;border-radius:4px}

/* Bouton */
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

.divider{display:flex;align-items:center;gap:12px;margin:1.5rem 0}
.divider::before,.divider::after{content:'';flex:1;height:1px;background:rgba(255,255,255,.07)}
.divider span{font-size:12px;color:#484f58}
.to-register{text-align:center;font-size:13.5px;color:#8b949e}
.to-register a{color:#c9a84c;text-decoration:none;font-weight:500}
.to-register a:hover{text-decoration:underline}

.error-bag{background:rgba(226,75,74,.08);border:1px solid rgba(226,75,74,.2);border-left:3px solid #E24B4A;border-radius:8px;padding:10px 14px;margin-bottom:1.25rem;animation:slideIn .25s ease}
.error-bag p{font-size:12.5px;color:#f0a0a0;line-height:1.6}
.input-error{font-size:12px;color:#f0a0a0;margin-top:4px}
.status-msg{background:rgba(59,109,17,.1);border:1px solid rgba(59,109,17,.2);border-left:3px solid #3B6D11;border-radius:8px;padding:10px 14px;margin-bottom:1.25rem;font-size:12.5px;color:#86d066;animation:slideIn .25s ease}
@keyframes slideIn{from{opacity:0;transform:translateY(-6px)}to{opacity:1;transform:translateY(0)}}

/* Focus visible global */
a:focus-visible{outline:2px solid #c9a84c;outline-offset:2px;border-radius:4px}

@media(max-width:768px){body{grid-template-columns:1fr}.left{display:none}}
</style>
</head>
<body>

<div class="left" role="complementary" aria-label="BimoTech Immo">
    <div class="grid-deco" aria-hidden="true"></div>
    <a href="<?php echo e(url('/')); ?>" class="left-logo" aria-label="Retour à l'accueil BimoTech">Bimo<span>Tech</span></a>
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
        <h1 class="form-title">Connexion</h1>
        <p class="form-subtitle">Pas encore de compte ? <a href="<?php echo e(route('register')); ?>">Créer une agence gratuitement</a></p>

        <?php if($errors->any()): ?>
            <div class="error-bag" role="alert">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><p><?php echo e($error); ?></p><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>
        <?php if(session('status')): ?>
            <div class="status-msg" role="status"><?php echo e(session('status')); ?></div>
        <?php endif; ?>

        <form method="POST" action="<?php echo e(route('login')); ?>" id="login-form" novalidate>
            <?php echo csrf_field(); ?>

            <div class="field">
                <label for="email">Adresse email</label>
                <input type="email" id="email" name="email"
                    value="<?php echo e(old('email')); ?>"
                    placeholder="votre@agence.sn"
                    autocomplete="email" autofocus
                    aria-required="true"
                    class="<?php echo e($errors->has('email') ? 'is-error' : ''); ?>"
                >
                <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="input-error" role="alert"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="field">
                <label for="password">Mot de passe</label>
                <div class="input-wrap">
                    <input type="password" id="password" name="password"
                        placeholder="••••••••"
                        autocomplete="current-password"
                        aria-required="true"
                        class="has-toggle <?php echo e($errors->has('password') ? 'is-error' : ''); ?>"
                    >
                    <button type="button" class="toggle-pw" id="toggle-pw"
                        aria-label="Afficher le mot de passe" aria-pressed="false"
                        onclick="togglePw()">
                        <svg id="eye-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                        </svg>
                    </button>
                </div>
                <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="input-error" role="alert"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="remember-row">
                <label class="check-label">
                    <input type="checkbox" name="remember" <?php echo e(old('remember') ? 'checked' : ''); ?>>
                    Se souvenir de moi
                </label>
                <?php if(Route::has('password.request')): ?>
                    <a href="<?php echo e(route('password.request')); ?>" class="forgot">Mot de passe oublié ?</a>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn-submit" id="submit-btn">
                <div class="spinner" aria-hidden="true"></div>
                <span class="btn-text">Se connecter</span>
            </button>
        </form>

        <div class="divider"><span>ou</span></div>
        <p class="to-register">Nouvelle agence ? <a href="<?php echo e(route('register')); ?>">Créer un compte gratuit →</a></p>
    </div>
</main>

<script>
function togglePw() {
    const input = document.getElementById('password');
    const btn   = document.getElementById('toggle-pw');
    const show  = input.type === 'password';
    input.type  = show ? 'text' : 'password';
    btn.setAttribute('aria-pressed', show ? 'true' : 'false');
    btn.setAttribute('aria-label', show ? 'Masquer le mot de passe' : 'Afficher le mot de passe');
    document.getElementById('eye-icon').innerHTML = show
        ? '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>'
        : '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
}

document.getElementById('login-form').addEventListener('submit', function() {
    const btn = document.getElementById('submit-btn');
    btn.classList.add('loading');
    btn.disabled = true;
    btn.querySelector('.btn-text').textContent = 'Connexion en cours...';
});
</script>

</body>
</html><?php /**PATH C:\Users\ph\bimotech-immo\resources\views/auth/login.blade.php ENDPATH**/ ?>