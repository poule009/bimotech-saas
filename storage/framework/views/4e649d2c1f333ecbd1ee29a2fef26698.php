<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Demander une démo — BimoTech Immo</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<style>
*{box-sizing:border-box;margin:0;padding:0}
:root{--gold:#c9a84c;--dark:#0d1117;--dark2:#161b22;--dark3:#21262d;--text:#e6edf3;--muted:#8b949e;--border:rgba(255,255,255,.08)}
body{font-family:'DM Sans',sans-serif;background:var(--dark);color:var(--text)}

nav{position:fixed;top:0;left:0;right:0;z-index:100;padding:0 5%;height:64px;display:flex;align-items:center;justify-content:space-between;background:rgba(13,17,23,.85);backdrop-filter:blur(12px);border-bottom:1px solid var(--border)}
.nav-logo{font-family:'Syne',sans-serif;font-size:18px;font-weight:800;color:var(--gold);text-decoration:none}
.nav-logo span{color:var(--text)}
.nav-back{font-size:13px;color:var(--muted);text-decoration:none;transition:color .2s}
.nav-back:hover{color:var(--text)}

.hero{padding:120px 5% 4rem;text-align:center;position:relative;overflow:hidden}
.hero::before{content:'';position:absolute;top:-20%;left:50%;transform:translateX(-50%);width:600px;height:600px;background:radial-gradient(circle,rgba(201,168,76,.06) 0%,transparent 70%);pointer-events:none}
.section-tag{font-size:11px;color:var(--gold);font-weight:600;letter-spacing:2px;text-transform:uppercase;margin-bottom:1rem}
h1{font-family:'Syne',sans-serif;font-size:clamp(28px,5vw,50px);font-weight:800;letter-spacing:-1.5px;line-height:1.1;margin-bottom:1rem}
h1 em{font-style:normal;color:var(--gold)}
.hero-sub{font-size:15px;color:var(--muted);max-width:460px;margin:0 auto;line-height:1.7;font-weight:300}

.main{max-width:920px;margin:0 auto;padding:0 5% 6rem;display:grid;grid-template-columns:1.1fr 1fr;gap:3rem;align-items:start}

/* Promesses */
.promises{display:flex;flex-direction:column;gap:1rem}
.promise{background:var(--dark2);border:1px solid var(--border);border-radius:14px;padding:1.5rem;display:flex;gap:14px}
.promise-icon{width:42px;height:42px;background:rgba(201,168,76,.1);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.promise-title{font-size:14px;font-weight:600;color:var(--text);margin-bottom:4px}
.promise-desc{font-size:13px;color:var(--muted);line-height:1.6}

.separator{border:none;border-top:1px solid var(--border);margin:1rem 0}

/* Témoignage */
.testimonial{background:var(--dark2);border:1px solid rgba(201,168,76,.15);border-radius:14px;padding:1.5rem}
.testimonial-text{font-size:14px;color:var(--muted);line-height:1.8;font-style:italic;margin-bottom:1rem}
.testimonial-author{display:flex;align-items:center;gap:10px}
.author-avatar{width:36px;height:36px;background:rgba(201,168,76,.15);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:600;color:var(--gold)}
.author-name{font-size:13px;font-weight:500;color:var(--text)}
.author-role{font-size:12px;color:#484f58}

/* Formulaire */
.form-card{background:var(--dark2);border:1px solid var(--border);border-radius:16px;padding:2.5rem;position:sticky;top:84px}
.form-title{font-family:'Syne',sans-serif;font-size:18px;font-weight:700;color:var(--text);margin-bottom:.4rem}
.form-sub{font-size:13px;color:var(--muted);margin-bottom:1.75rem;line-height:1.5}

.field{margin-bottom:1rem}
label{display:block;font-size:12.5px;font-weight:500;color:var(--muted);margin-bottom:5px;letter-spacing:.3px}
input[type=text],input[type=email],input[type=tel],select{
    width:100%;background:var(--dark);border:1px solid rgba(255,255,255,.1);border-radius:10px;
    padding:11px 14px;font-family:'DM Sans',sans-serif;font-size:14px;color:var(--text);
    outline:none;transition:border-color .2s;-webkit-appearance:none;
}
input:focus,select:focus{border-color:var(--gold);background:#1c2128}
input::placeholder{color:#484f58}
select{cursor:pointer}
select option{background:var(--dark2)}
.input-error{font-size:12px;color:#f0a0a0;margin-top:4px}

.row-2{display:grid;grid-template-columns:1fr 1fr;gap:10px}

.btn-submit{width:100%;background:var(--gold);color:var(--dark);font-family:'DM Sans',sans-serif;font-size:14px;font-weight:700;padding:13px;border-radius:10px;border:none;cursor:pointer;transition:opacity .2s;margin-top:.5rem}
.btn-submit:hover{opacity:.9}

.or-divider{display:flex;align-items:center;gap:12px;margin:1.25rem 0}
.or-divider::before,.or-divider::after{content:'';flex:1;height:1px;background:var(--border)}
.or-divider span{font-size:12px;color:#484f58}

.wa-btn{display:flex;align-items:center;justify-content:center;gap:10px;background:rgba(37,211,102,.1);border:1px solid rgba(37,211,102,.2);border-radius:10px;padding:12px;text-decoration:none;color:#25d366;font-size:13.5px;font-weight:600;transition:background .2s}
.wa-btn:hover{background:rgba(37,211,102,.15)}

.success-msg{background:rgba(59,109,17,.1);border:1px solid rgba(59,109,17,.2);border-left:3px solid #3B6D11;border-radius:8px;padding:12px 16px;font-size:13px;color:#86d066;margin-bottom:1.25rem}
.error-bag{background:rgba(226,75,74,.08);border:1px solid rgba(226,75,74,.2);border-left:3px solid #E24B4A;border-radius:8px;padding:10px 14px;margin-bottom:1.25rem}
.error-bag p{font-size:12.5px;color:#f0a0a0;line-height:1.6}

footer{padding:2rem 5%;border-top:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem}
.footer-logo{font-family:'Syne',sans-serif;font-size:15px;font-weight:800;color:var(--gold)}
.footer-links{display:flex;gap:1.5rem}
.footer-links a{font-size:12px;color:var(--muted);text-decoration:none}
.footer-copy{font-size:12px;color:#484f58}

@media(max-width:768px){
    .main{grid-template-columns:1fr}
    .form-card{position:static}
    .row-2{grid-template-columns:1fr}
    footer{flex-direction:column;text-align:center}
}
</style>
</head>
<body>

<?php echo $__env->make('partials.public-nav', ['active' => 'contact'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="hero">
    <div class="section-tag">Démo gratuite</div>
    <h1>Voyez BimoTech en action<br>pour votre <em>agence</em></h1>
    <p class="hero-sub">30 minutes de démonstration personnalisée. On configure ensemble votre espace avec vos vrais biens.</p>
</div>

<div class="main">

    <!-- Promesses + Témoignage -->
    <div>
        <div class="promises">
            <div class="promise">
                <div class="promise-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#c9a84c" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12,6 12,12 16,14"/></svg>
                </div>
                <div>
                    <div class="promise-title">30 minutes chrono</div>
                    <div class="promise-desc">Une démo ciblée sur vos besoins réels. Pas de présentation générique, on va droit au but.</div>
                </div>
            </div>
            <div class="promise">
                <div class="promise-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#c9a84c" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </div>
                <div>
                    <div class="promise-title">Démo sur votre vrai contexte</div>
                    <div class="promise-desc">On part de votre portefeuille, vos loyers, votre secteur. La démo ressemble à votre agence.</div>
                </div>
            </div>
            <div class="promise">
                <div class="promise-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#c9a84c" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.17 3.44 2 2 0 0 1 3.15 1h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.09 8.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 21 16z"/></svg>
                </div>
                <div>
                    <div class="promise-title">Par WhatsApp ou sur place</div>
                    <div class="promise-desc">Démo en ligne ou déplacement à Dakar selon votre préférence. Aucune contrainte.</div>
                </div>
            </div>
            <div class="promise">
                <div class="promise-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#c9a84c" stroke-width="2"><polyline points="20,6 9,17 4,12"/></svg>
                </div>
                <div>
                    <div class="promise-title">Zéro engagement</div>
                    <div class="promise-desc">La démo est gratuite. Aucune carte bancaire, aucun engagement de souscription.</div>
                </div>
            </div>
        </div>

        <hr class="separator" style="margin:1.5rem 0">

        <div class="testimonial">
            <p class="testimonial-text">"Avant BimoTech, je gérais tout sur Excel. Maintenant mes quittances sont générées automatiquement et mes propriétaires voient leurs revenus en temps réel. La démo m'a convaincu en 20 minutes."</p>
            <div class="testimonial-author">
                <div class="author-avatar">AD</div>
                <div>
                    <div class="author-name">Amadou D.</div>
                    <div class="author-role">Directeur, Agence Diallo Immobilier — Dakar</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulaire -->
    <div class="form-card">
        <div class="form-title">Réserver ma démo</div>
        <p class="form-sub">Réponse sous 2h en semaine · Démo planifiée sous 48h</p>

        <?php if(session('success')): ?>
            <div class="success-msg">✓ Demande reçue ! Nous vous contactons dans les 2h pour planifier la démo.</div>
        <?php endif; ?>

        <?php if($errors->any()): ?>
            <div class="error-bag">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><p><?php echo e($error); ?></p><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?php echo e(route('demo.send')); ?>">
            <?php echo csrf_field(); ?>

            <div class="row-2">
                <div class="field">
                    <label>Prénom *</label>
                    <input type="text" name="prenom" value="<?php echo e(old('prenom')); ?>" placeholder="Amadou">
                    <?php $__errorArgs = ['prenom'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="input-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div class="field">
                    <label>Nom *</label>
                    <input type="text" name="nom" value="<?php echo e(old('nom')); ?>" placeholder="Diallo">
                    <?php $__errorArgs = ['nom'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="input-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>

            <div class="field">
                <label>Nom de votre agence *</label>
                <input type="text" name="agence" value="<?php echo e(old('agence')); ?>" placeholder="Agence Immobilière Diallo">
                <?php $__errorArgs = ['agence'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="input-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="field">
                <label>Téléphone (WhatsApp) *</label>
                <input type="tel" name="telephone" value="<?php echo e(old('telephone')); ?>" placeholder="+221 77 000 00 00">
                <?php $__errorArgs = ['telephone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="input-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="field">
                <label>Email *</label>
                <input type="email" name="email" value="<?php echo e(old('email')); ?>" placeholder="amadou@agence.sn">
                <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="input-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="row-2">
                <div class="field">
                    <label>Nombre de biens</label>
                    <select name="nb_biens">
                        <option value="" <?php echo e(old('nb_biens') ? '' : 'selected'); ?> disabled>Choisir</option>
                        <option value="1-10"   <?php echo e(old('nb_biens') === '1-10'   ? 'selected' : ''); ?>>1 à 10 biens</option>
                        <option value="10-30"  <?php echo e(old('nb_biens') === '10-30'  ? 'selected' : ''); ?>>10 à 30 biens</option>
                        <option value="30-100" <?php echo e(old('nb_biens') === '30-100' ? 'selected' : ''); ?>>30 à 100 biens</option>
                        <option value="100+"   <?php echo e(old('nb_biens') === '100+'   ? 'selected' : ''); ?>>Plus de 100</option>
                    </select>
                </div>
                <div class="field">
                    <label>Ville</label>
                    <select name="ville">
                        <option value="" <?php echo e(old('ville') ? '' : 'selected'); ?> disabled>Choisir</option>
                        <option value="dakar"     <?php echo e(old('ville') === 'dakar'     ? 'selected' : ''); ?>>Dakar</option>
                        <option value="thies"     <?php echo e(old('ville') === 'thies'     ? 'selected' : ''); ?>>Thiès</option>
                        <option value="saint-louis"<?php echo e(old('ville') === 'saint-louis'? 'selected' : ''); ?>>Saint-Louis</option>
                        <option value="ziguinchor"<?php echo e(old('ville') === 'ziguinchor'? 'selected' : ''); ?>>Ziguinchor</option>
                        <option value="autre"     <?php echo e(old('ville') === 'autre'     ? 'selected' : ''); ?>>Autre</option>
                    </select>
                </div>
            </div>

            <button type="submit" class="btn-submit">Réserver ma démo gratuite →</button>
        </form>

        <div class="or-divider"><span>ou directement sur</span></div>

        <a href="https://wa.me/221XXXXXXXXX?text=Bonjour BimoTech, je souhaite réserver une démo pour mon agence." target="_blank" class="wa-btn">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#25d366"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.890-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
            Contacter sur WhatsApp
        </a>
    </div>

</div>

<footer>
    <div class="footer-logo">BimoTech Immo</div>
    <div class="footer-links">
        <a href="<?php echo e(url('/')); ?>">Accueil</a>
        <a href="<?php echo e(route('contact')); ?>">Contact</a>
        <a href="<?php echo e(route('mentions-legales')); ?>">Mentions légales</a>
    </div>
    <div class="footer-copy">© <?php echo e(date('Y')); ?> BimoTech · Dakar, Sénégal</div>
</footer>

</body>
</html><?php /**PATH C:\Users\ph\bimotech-immo\resources\views/demo.blade.php ENDPATH**/ ?>