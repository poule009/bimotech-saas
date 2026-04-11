<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
     <?php $__env->slot('header', null, []); ?> 
        Nouveau <?php echo e($role === 'proprietaire' ? 'propriétaire' : 'locataire'); ?>

     <?php $__env->endSlot(); ?>

<style>
:root {
    --gold:#c9a84c; --gold-light:#f5e9c9; --gold-dark:#8a6e2f;
    --dark:#0d1117; --green:#16a34a; --red:#dc2626;
}

/* ── Layout principal ── */
.create-page { display:grid; grid-template-columns:260px 1fr; min-height:calc(100vh - 64px); }

/* ── Sidebar gauche ── */
.create-sidebar {
    background:var(--dark);
    padding:32px 24px;
    display:flex; flex-direction:column; gap:8px;
    border-right:1px solid rgba(255,255,255,.06);
    position:sticky; top:64px; height:calc(100vh - 64px); overflow-y:auto;
}
.sidebar-role {
    display:flex; align-items:center; gap:10px;
    padding:14px 16px; border-radius:12px;
    background:rgba(201,168,76,.12); border:1px solid rgba(201,168,76,.2);
    margin-bottom:20px;
}
.sidebar-role-icon { font-size:22px; }
.sidebar-role-lbl { font-size:10px; color:var(--gold); font-weight:700; text-transform:uppercase; letter-spacing:1px; }
.sidebar-role-name { font-family:'Syne',sans-serif; font-size:15px; font-weight:700; color:#fff; }

.nav-section { font-size:9px; font-weight:700; text-transform:uppercase; letter-spacing:1.2px; color:rgba(255,255,255,.25); padding:0 12px; margin-top:16px; margin-bottom:6px; }
.nav-item {
    display:flex; align-items:center; gap:10px;
    padding:10px 12px; border-radius:9px; cursor:pointer;
    font-size:12px; font-weight:500; color:rgba(255,255,255,.5);
    transition:all .15s; border:none; background:none; text-align:left; width:100%;
}
.nav-item:hover { background:rgba(255,255,255,.06); color:rgba(255,255,255,.8); }
.nav-item.active { background:rgba(201,168,76,.15); color:var(--gold); }
.nav-item-dot { width:6px; height:6px; border-radius:50%; background:rgba(255,255,255,.2); flex-shrink:0; transition:all .15s; }
.nav-item.active .nav-item-dot { background:var(--gold); }
.nav-item.done .nav-item-dot { background:var(--green); }
.nav-item.done { color:rgba(255,255,255,.4); }

.sidebar-tip {
    margin-top:auto; padding:14px 16px;
    background:rgba(255,255,255,.04); border:1px solid rgba(255,255,255,.08);
    border-radius:12px; font-size:11px; color:rgba(255,255,255,.4); line-height:1.6;
}

/* ── Zone formulaire ── */
.create-main { padding:32px 40px 60px; max-width:720px; }
.create-header { margin-bottom:32px; }
.create-title { font-family:'Syne',sans-serif; font-size:26px; font-weight:800; color:#0d1117; letter-spacing:-.5px; margin-bottom:6px; }
.create-sub { font-size:14px; color:#6b7280; }

/* ── Sections ── */
.form-section { margin-bottom:36px; }
.section-hd {
    display:flex; align-items:center; gap:12px;
    padding-bottom:14px; border-bottom:1px solid #f3f4f6; margin-bottom:20px;
}
.section-num {
    width:28px; height:28px; border-radius:8px;
    background:var(--dark); color:#fff;
    display:flex; align-items:center; justify-content:center;
    font-size:12px; font-weight:800; font-family:'Syne',sans-serif;
    flex-shrink:0;
}
.section-num.gold { background:var(--gold); color:#0d1117; }
.section-title { font-family:'Syne',sans-serif; font-size:14px; font-weight:700; color:#0d1117; }
.section-desc { font-size:12px; color:#9ca3af; margin-top:1px; }

/* ── Champs ── */
.field-group { margin-bottom:16px; }
.field-grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-bottom:16px; }
.field-grid-3 { display:grid; grid-template-columns:1fr 1fr 1fr; gap:14px; margin-bottom:16px; }
.field-label { display:block; font-size:12px; font-weight:600; color:#374151; margin-bottom:6px; }
.field-req { color:var(--red); margin-left:2px; }
.field-opt { font-size:11px; font-weight:400; color:#9ca3af; margin-left:4px; }
.field-input {
    width:100%; padding:10px 13px;
    border:1.5px solid #e5e7eb; border-radius:10px;
    font-size:13px; color:#0d1117; font-family:'DM Sans',sans-serif;
    background:#fff; outline:none; transition:all .15s;
}
.field-input:focus { border-color:var(--gold); box-shadow:0 0 0 3px rgba(201,168,76,.1); }
.field-input.error { border-color:var(--red); }
.field-select { appearance:none; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:right 12px center; background-size:14px; padding-right:36px; cursor:pointer; }
.field-hint { font-size:11px; color:#9ca3af; margin-top:5px; }
.field-error { font-size:11px; color:var(--red); margin-top:5px; }

/* ── Genre pills ── */
.genre-grid { display:grid; grid-template-columns:1fr 1fr; gap:8px; }
.genre-pill input { position:absolute; opacity:0; width:0; height:0; }
.genre-pill { position:relative; }
.genre-pill label {
    display:flex; align-items:center; justify-content:center; gap:8px;
    padding:10px; border:1.5px solid #e5e7eb; border-radius:10px;
    cursor:pointer; font-size:13px; font-weight:500; color:#6b7280;
    background:#fff; transition:all .15s;
}
.genre-pill input:checked + label { border-color:var(--gold); background:var(--gold-light); color:var(--gold-dark); }
.genre-pill label:hover { border-color:var(--gold); }

/* ── Mode paiement pills ── */
.mode-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:8px; }
.mode-pill input { position:absolute; opacity:0; width:0; height:0; }
.mode-pill { position:relative; }
.mode-pill label {
    display:flex; flex-direction:column; align-items:center; gap:4px;
    padding:10px 8px; border:1.5px solid #e5e7eb; border-radius:10px;
    cursor:pointer; font-size:11px; font-weight:500; color:#6b7280;
    background:#fff; transition:all .15s; text-align:center;
}
.mode-pill input:checked + label { border-color:var(--gold); background:var(--gold-light); color:var(--gold-dark); }
.mode-pill label:hover { border-color:var(--gold); }
.mode-emoji { font-size:18px; }

/* ── Type locataire pills ── */
.type-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:8px; margin-bottom:12px; }
.type-pill input { position:absolute; opacity:0; width:0; height:0; }
.type-pill { position:relative; }
.type-pill label {
    display:flex; flex-direction:column; align-items:center; gap:4px;
    padding:10px 8px; border:1.5px solid #e5e7eb; border-radius:10px;
    cursor:pointer; font-size:11px; font-weight:500; color:#6b7280;
    background:#fff; transition:all .15s; text-align:center;
}
.type-pill input:checked + label { border-color:var(--gold); background:var(--gold-light); color:var(--gold-dark); }

/* ── Alerte entreprise ── */
.enterprise-bloc {
    background:#fff1f2; border:1px solid #fecaca; border-radius:12px;
    padding:16px; margin-top:4px;
    display:none;
}
.enterprise-bloc.visible { display:block; }

/* ── Passwords ── */
.pwd-strength { height:4px; border-radius:2px; background:#f3f4f6; margin-top:8px; overflow:hidden; }
.pwd-fill { height:100%; border-radius:2px; transition:all .3s; }

/* ── Submit bar ── */
.submit-bar {
    position:sticky; bottom:0;
    background:rgba(255,255,255,.95); backdrop-filter:blur(8px);
    border-top:1px solid #e5e7eb; padding:16px 40px;
    display:flex; align-items:center; gap:12px; justify-content:flex-end;
    margin:0 -40px -60px;
}
.btn-submit {
    display:inline-flex; align-items:center; gap:8px;
    padding:12px 28px; background:var(--dark); color:#fff;
    border:none; border-radius:11px; font-size:14px; font-weight:600;
    font-family:'DM Sans',sans-serif; cursor:pointer; transition:all .15s;
}
.btn-submit:hover { background:#1a2332; }
.btn-submit svg { width:15px; height:15px; }
.btn-cancel {
    padding:12px 22px; background:#fff; color:#6b7280;
    border:1.5px solid #e5e7eb; border-radius:11px;
    font-size:13px; font-weight:500; font-family:'DM Sans',sans-serif;
    text-decoration:none; cursor:pointer; transition:all .15s;
}
.btn-cancel:hover { border-color:#9ca3af; }

/* ── Erreurs ── */
.error-banner {
    background:#fee2e2; border:1px solid #fecaca; border-radius:12px;
    padding:14px 18px; margin-bottom:24px;
}
.error-banner-title { font-size:13px; font-weight:600; color:var(--red); margin-bottom:6px; }
.error-banner-item { font-size:12px; color:#9f1239; display:flex; align-items:flex-start; gap:6px; margin-top:4px; }
</style>

<div class="create-page">

    
    <div class="create-sidebar">

        <div class="sidebar-role">
            <div class="sidebar-role-icon"><?php echo e($role === 'proprietaire' ? '🏢' : '👤'); ?></div>
            <div>
                <div class="sidebar-role-lbl">Création</div>
                <div class="sidebar-role-name"><?php echo e($role === 'proprietaire' ? 'Propriétaire' : 'Locataire'); ?></div>
            </div>
        </div>

        <div class="nav-section">Sections</div>

        <button class="nav-item active" onclick="scrollTo('sec-identite')">
            <div class="nav-item-dot"></div>
            Identité
        </button>
        <button class="nav-item" onclick="scrollTo('sec-acces')">
            <div class="nav-item-dot"></div>
            Accès & Mot de passe
        </button>
        <?php if($role === 'proprietaire'): ?>
        <button class="nav-item" onclick="scrollTo('sec-paiement')">
            <div class="nav-item-dot"></div>
            Mode de paiement
        </button>
        <button class="nav-item" onclick="scrollTo('sec-fiscal')">
            <div class="nav-item-dot"></div>
            Fiscal
        </button>
        <?php else: ?>
        <button class="nav-item" onclick="scrollTo('sec-type')">
            <div class="nav-item-dot"></div>
            Type & Statut fiscal
        </button>
        <button class="nav-item" onclick="scrollTo('sec-pro')">
            <div class="nav-item-dot"></div>
            Situation professionnelle
        </button>
        <button class="nav-item" onclick="scrollTo('sec-urgence')">
            <div class="nav-item-dot"></div>
            Contact d'urgence
        </button>
        <?php endif; ?>

        <div class="sidebar-tip">
            💡 Tous les champs marqués <strong style="color:var(--gold)">*</strong> sont obligatoires. Les autres peuvent être complétés plus tard depuis la fiche.
        </div>

    </div>

    
    <div class="create-main">

        <div class="create-header">
            <div class="create-title">
                Nouveau <?php echo e($role === 'proprietaire' ? 'propriétaire' : 'locataire'); ?>

            </div>
            <div class="create-sub">
                <?php echo e($role === 'proprietaire'
                    ? 'Renseignez les informations du propriétaire. Il pourra accéder à son espace depuis son email.'
                    : 'Renseignez les informations du locataire. Il pourra consulter ses quittances depuis son espace.'); ?>

            </div>
        </div>

        <?php if($errors->any()): ?>
        <div class="error-banner">
            <div class="error-banner-title">Certains champs nécessitent votre attention</div>
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="error-banner-item">
                <svg style="width:12px;height:12px;flex-shrink:0;margin-top:1px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <?php echo e($error); ?>

            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="<?php echo e(route('admin.users.store')); ?>">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="role" value="<?php echo e($role); ?>">

            
            <div class="form-section" id="sec-identite">
                <div class="section-hd">
                    <div class="section-num gold">1</div>
                    <div>
                        <div class="section-title">Identité personnelle</div>
                        <div class="section-desc">Informations de contact principales</div>
                    </div>
                </div>

                
                <div class="field-group">
                    <label class="field-label">Nom complet <span class="field-req">*</span></label>
                    <input type="text" name="name" class="field-input <?php echo e($errors->has('name') ? 'error':''); ?>"
                           value="<?php echo e(old('name')); ?>" placeholder="Ex: Moussa Diallo" autofocus>
                    <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="field-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                
                <div class="field-grid-2">
                    <div>
                        <label class="field-label">Email <span class="field-req">*</span></label>
                        <input type="email" name="email" class="field-input <?php echo e($errors->has('email') ? 'error':''); ?>"
                               value="<?php echo e(old('email')); ?>" placeholder="email@exemple.com">
                        <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="field-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div>
                        <label class="field-label">Téléphone</label>
                        <input type="text" name="telephone" class="field-input"
                               value="<?php echo e(old('telephone')); ?>" placeholder="+221 77 000 00 00">
                    </div>
                </div>

                
                <div class="field-group">
                    <label class="field-label">Adresse <span class="field-opt">(optionnel)</span></label>
                    <input type="text" name="adresse" class="field-input"
                           value="<?php echo e(old('adresse')); ?>" placeholder="Rue, quartier...">
                </div>

                
                <div class="field-group">
                    <label class="field-label">Genre</label>
                    <div class="genre-grid">
                        <div class="genre-pill">
                            <input type="radio" name="genre" id="genre_homme" value="homme"
                                   <?php echo e(old('genre') === 'homme' ? 'checked':''); ?>>
                            <label for="genre_homme">👨 Homme</label>
                        </div>
                        <div class="genre-pill">
                            <input type="radio" name="genre" id="genre_femme" value="femme"
                                   <?php echo e(old('genre') === 'femme' ? 'checked':''); ?>>
                            <label for="genre_femme">👩 Femme</label>
                        </div>
                    </div>
                </div>

                
                <div class="field-grid-3">
                    <div>
                        <label class="field-label">Date de naissance</label>
                        <input type="date" name="date_naissance" class="field-input"
                               value="<?php echo e(old('date_naissance')); ?>">
                    </div>
                    <div>
                        <label class="field-label">Nationalité</label>
                        <input type="text" name="nationalite" class="field-input"
                               value="<?php echo e(old('nationalite', 'Sénégalaise')); ?>">
                    </div>
                    <div>
                        <label class="field-label">Ville</label>
                        <select name="ville" class="field-input field-select">
                            <?php $__currentLoopData = ['Dakar','Thiès','Saint-Louis','Ziguinchor','Kaolack','Mbour','Rufisque','Touba','Diourbel','Tambacounda']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($v); ?>" <?php echo e(old('ville','Dakar') === $v ? 'selected':''); ?>><?php echo e($v); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>

                <div class="field-group">
                    <label class="field-label">CNI / Passeport <span class="field-opt">(optionnel)</span></label>
                    <input type="text" name="cni" class="field-input"
                           value="<?php echo e(old('cni')); ?>" placeholder="1 234 567 890 12">
                </div>
            </div>

            
            <div class="form-section" id="sec-acces">
                <div class="section-hd">
                    <div class="section-num">2</div>
                    <div>
                        <div class="section-title">Accès à l'espace personnel</div>
                        <div class="section-desc">Identifiants de connexion au portail <?php echo e($role); ?></div>
                    </div>
                </div>

                <div class="field-grid-2">
                    <div>
                        <label class="field-label">Mot de passe <span class="field-req">*</span></label>
                        <input type="password" name="password" id="pwd"
                               class="field-input <?php echo e($errors->has('password') ? 'error':''); ?>"
                               placeholder="Min. 8 caractères" oninput="checkPwd(this.value)">
                        <div class="pwd-strength"><div class="pwd-fill" id="pwd-bar" style="width:0%;background:var(--red)"></div></div>
                        <div class="field-hint" id="pwd-hint">Entrez un mot de passe</div>
                        <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="field-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div>
                        <label class="field-label">Confirmer le mot de passe <span class="field-req">*</span></label>
                        <input type="password" name="password_confirmation" id="pwd2"
                               class="field-input" placeholder="Répétez le mot de passe"
                               oninput="checkConfirm()">
                        <div class="field-hint" id="pwd2-hint" style="color:transparent">—</div>
                    </div>
                </div>

                <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:12px 16px;font-size:12px;color:#166534">
                    ✉️ Le <?php echo e($role === 'proprietaire' ? 'propriétaire' : 'locataire'); ?> pourra se connecter avec son email et ce mot de passe sur
                    <strong><?php echo e(config('app.url')); ?></strong>
                </div>
            </div>

            
            <?php if($role === 'proprietaire'): ?>

            
            <div class="form-section" id="sec-paiement">
                <div class="section-hd">
                    <div class="section-num">3</div>
                    <div>
                        <div class="section-title">Mode de paiement préféré</div>
                        <div class="section-desc">Comment reverser les loyers nets au propriétaire</div>
                    </div>
                </div>

                <div class="mode-grid" style="margin-bottom:16px">
                    <?php $__currentLoopData = [
                        'virement'     => ['Virement',     '🏦'],
                        'wave'         => ['Wave',          '📱'],
                        'orange_money' => ['Orange Money',  '🟠'],
                        'especes'      => ['Espèces',       '💵'],
                        'cheque'       => ['Chèque',        '📝'],
                        'mobile_money' => ['Mobile Money',  '📲'],
                    ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => [$lbl, $emoji]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="mode-pill">
                        <input type="radio" name="mode_paiement_prefere" id="mode_<?php echo e($val); ?>" value="<?php echo e($val); ?>"
                               <?php echo e(old('mode_paiement_prefere','virement') === $val ? 'checked':''); ?>>
                        <label for="mode_<?php echo e($val); ?>">
                            <span class="mode-emoji"><?php echo e($emoji); ?></span>
                            <?php echo e($lbl); ?>

                        </label>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                <div class="field-grid-2">
                    <div>
                        <label class="field-label">Numéro Wave</label>
                        <input type="text" name="numero_wave" class="field-input"
                               value="<?php echo e(old('numero_wave')); ?>" placeholder="+221 77 XXX XX XX">
                    </div>
                    <div>
                        <label class="field-label">Numéro Orange Money</label>
                        <input type="text" name="numero_om" class="field-input"
                               value="<?php echo e(old('numero_om')); ?>" placeholder="+221 77 XXX XX XX">
                    </div>
                </div>

                <div class="field-grid-2">
                    <div>
                        <label class="field-label">Banque <span class="field-opt">(optionnel)</span></label>
                        <input type="text" name="banque" class="field-input"
                               value="<?php echo e(old('banque')); ?>" placeholder="CBAO, Ecobank, BIS...">
                    </div>
                    <div>
                        <label class="field-label">Numéro de compte <span class="field-opt">(optionnel)</span></label>
                        <input type="text" name="numero_compte" class="field-input"
                               value="<?php echo e(old('numero_compte')); ?>" placeholder="RIB / IBAN">
                    </div>
                </div>
            </div>

            
            <div class="form-section" id="sec-fiscal">
                <div class="section-hd">
                    <div class="section-num">4</div>
                    <div>
                        <div class="section-title">Informations fiscales</div>
                        <div class="section-desc">NINEA et statut TVA du propriétaire</div>
                    </div>
                </div>

                <div class="field-grid-2">
                    <div>
                        <label class="field-label">NINEA <span class="field-opt">(optionnel)</span></label>
                        <input type="text" name="ninea" class="field-input"
                               value="<?php echo e(old('ninea')); ?>" placeholder="Ex: 00123456789">
                        <div class="field-hint">Numéro d'Identification National des Entreprises</div>
                    </div>
                    <div style="padding-top:26px">
                        <label style="display:flex;align-items:center;gap:10px;cursor:pointer;padding:11px 14px;border:1.5px solid #e5e7eb;border-radius:10px;transition:all .15s"
                               onmouseover="this.style.borderColor='var(--gold)'" onmouseout="this.style.borderColor='#e5e7eb'">
                            <input type="checkbox" name="assujetti_tva" value="1"
                                   <?php echo e(old('assujetti_tva') ? 'checked':''); ?>

                                   style="width:16px;height:16px;accent-color:var(--gold)">
                            <div>
                                <div style="font-size:13px;font-weight:500;color:#374151">Assujetti à la TVA</div>
                                <div style="font-size:11px;color:#9ca3af">Le propriétaire est redevable de TVA</div>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <?php endif; ?>

            
            <?php if($role === 'locataire'): ?>

            
            <div class="form-section" id="sec-type">
                <div class="section-hd">
                    <div class="section-num">3</div>
                    <div>
                        <div class="section-title">Type de locataire & Statut fiscal</div>
                        <div class="section-desc">Détermine si la Retenue à la Source (BRS) s'applique</div>
                    </div>
                </div>

                <div class="field-group">
                    <label class="field-label">Type de locataire</label>
                    <div class="type-grid">
                        <?php $__currentLoopData = [
                            'particulier' => ['Particulier',  '👤', false],
                            'entreprise'  => ['Entreprise',   '🏢', true],
                            'association' => ['Association',  '🤝', true],
                            'ambassade'   => ['Ambassade',    '🏛️', false],
                            'ong'         => ['ONG / Org.',   '🌍', false],
                        ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => [$lbl, $ico, $brs]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="type-pill">
                            <input type="radio" name="type_locataire" id="type_<?php echo e($k); ?>" value="<?php echo e($k); ?>"
                                   <?php echo e(old('type_locataire','particulier') === $k ? 'checked':''); ?>

                                   onchange="onTypeChange('<?php echo e($k); ?>', <?php echo e($brs ? 'true':'false'); ?>)">
                            <label for="type_<?php echo e($k); ?>">
                                <span style="font-size:18px"><?php echo e($ico); ?></span>
                                <?php echo e($lbl); ?>

                                <?php if($brs): ?><span style="font-size:9px;color:var(--red);font-weight:700">BRS 15%</span><?php endif; ?>
                            </label>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <input type="hidden" name="est_entreprise" id="est_entreprise"
                           value="<?php echo e(in_array(old('type_locataire','particulier'),['entreprise','association']) ? '1':'0'); ?>">
                </div>

                
                <div class="enterprise-bloc <?php echo e(in_array(old('type_locataire','particulier'),['entreprise','association']) ? 'visible':''); ?>"
                     id="bloc-entreprise">
                    <div style="font-size:12px;font-weight:700;color:var(--red);margin-bottom:12px;display:flex;align-items:center;gap:6px">
                        <svg style="width:14px;height:14px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        BRS 15% — Retenue à la source automatique sur les paiements futurs
                    </div>
                    <div class="field-group">
                        <label class="field-label">Raison sociale</label>
                        <input type="text" name="nom_entreprise" class="field-input"
                               value="<?php echo e(old('nom_entreprise')); ?>" placeholder="Nom officiel de la société">
                    </div>
                    <div class="field-grid-2">
                        <div>
                            <label class="field-label">NINEA</label>
                            <input type="text" name="ninea_locataire" class="field-input"
                                   value="<?php echo e(old('ninea_locataire')); ?>" placeholder="00123456789" maxlength="30">
                        </div>
                        <div>
                            <label class="field-label">RCCM</label>
                            <input type="text" name="rccm_locataire" class="field-input"
                                   value="<?php echo e(old('rccm_locataire')); ?>" placeholder="SN-DKR-2024-B-XXXXX" maxlength="60">
                        </div>
                    </div>
                    <div style="max-width:180px">
                        <label class="field-label">Taux BRS personnalisé <span class="field-opt">(%)</span></label>
                        <input type="number" name="taux_brs_override" class="field-input"
                               value="<?php echo e(old('taux_brs_override')); ?>" placeholder="15" min="0" max="20" step="0.5">
                        <div class="field-hint">Vide = 15% légal</div>
                    </div>
                </div>
            </div>

            
            <div class="form-section" id="sec-pro">
                <div class="section-hd">
                    <div class="section-num">4</div>
                    <div>
                        <div class="section-title">Situation professionnelle</div>
                        <div class="section-desc">Permet de calculer le taux d'effort locatif</div>
                    </div>
                </div>

                <div class="field-grid-3">
                    <div>
                        <label class="field-label">Profession</label>
                        <input type="text" name="profession" class="field-input"
                               value="<?php echo e(old('profession')); ?>" placeholder="Ex: Ingénieur">
                    </div>
                    <div>
                        <label class="field-label">Employeur</label>
                        <input type="text" name="employeur" class="field-input"
                               value="<?php echo e(old('employeur')); ?>" placeholder="Nom de l'employeur">
                    </div>
                    <div>
                        <label class="field-label">Revenu mensuel <span class="field-opt">(F)</span></label>
                        <input type="number" name="revenu_mensuel" class="field-input"
                               value="<?php echo e(old('revenu_mensuel')); ?>" placeholder="350000" min="0">
                    </div>
                </div>
            </div>

            
            <div class="form-section" id="sec-urgence">
                <div class="section-hd">
                    <div class="section-num">5</div>
                    <div>
                        <div class="section-title">Contact d'urgence <span class="field-opt">(optionnel)</span></div>
                        <div class="section-desc">Personne à contacter en cas d'urgence</div>
                    </div>
                </div>

                <div class="field-grid-3">
                    <div>
                        <label class="field-label">Nom</label>
                        <input type="text" name="contact_urgence_nom" class="field-input"
                               value="<?php echo e(old('contact_urgence_nom')); ?>" placeholder="Prénom NOM">
                    </div>
                    <div>
                        <label class="field-label">Téléphone</label>
                        <input type="text" name="contact_urgence_tel" class="field-input"
                               value="<?php echo e(old('contact_urgence_tel')); ?>" placeholder="+221 7X XXX XX XX">
                    </div>
                    <div>
                        <label class="field-label">Lien</label>
                        <input type="text" name="contact_urgence_lien" class="field-input"
                               value="<?php echo e(old('contact_urgence_lien')); ?>" placeholder="Père, Conjoint...">
                    </div>
                </div>
            </div>

            <?php endif; ?>

            
            <div class="submit-bar">
                <a href="<?php echo e($role === 'proprietaire' ? route('admin.users.proprietaires') : route('admin.users.locataires')); ?>"
                   class="btn-cancel">Annuler</a>
                <button type="submit" class="btn-submit">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    Créer le <?php echo e($role === 'proprietaire' ? 'propriétaire' : 'locataire'); ?>

                </button>
            </div>

        </form>
    </div>
</div>

<script>
// ── Scroll navigation sidebar ──────────────────────────────────────────────
function scrollTo(id) {
    document.getElementById(id)?.scrollIntoView({ behavior:'smooth', block:'start' });
    // Mettre à jour l'état actif de la nav
    document.querySelectorAll('.nav-item').forEach(b => b.classList.remove('active'));
    event.currentTarget.classList.add('active');
}

// ── Force du mot de passe ──────────────────────────────────────────────────
function checkPwd(v) {
    const bar = document.getElementById('pwd-bar');
    const hint = document.getElementById('pwd-hint');
    let score = 0;
    if (v.length >= 8) score++;
    if (/[A-Z]/.test(v)) score++;
    if (/[0-9]/.test(v)) score++;
    if (/[^A-Za-z0-9]/.test(v)) score++;

    const levels = [
        { pct:'25%', color:'#dc2626', label:'Trop faible' },
        { pct:'50%', color:'#f59e0b', label:'Faible' },
        { pct:'75%', color:'#3b82f6', label:'Correct' },
        { pct:'100%',color:'#16a34a', label:'Fort ✓' },
    ];
    const l = levels[Math.max(0, score - 1)] ?? levels[0];
    if (v.length === 0) { bar.style.width='0%'; hint.textContent='Entrez un mot de passe'; hint.style.color='#9ca3af'; return; }
    bar.style.width = l.pct;
    bar.style.background = l.color;
    hint.textContent = l.label;
    hint.style.color = l.color;
    checkConfirm();
}

function checkConfirm() {
    const pwd = document.getElementById('pwd').value;
    const pwd2 = document.getElementById('pwd2').value;
    const hint = document.getElementById('pwd2-hint');
    if (!pwd2) { hint.style.color='transparent'; return; }
    if (pwd === pwd2) { hint.textContent='✓ Correspondent'; hint.style.color='#16a34a'; }
    else { hint.textContent='✗ Ne correspondent pas'; hint.style.color='#dc2626'; }
}

// ── Type locataire entreprise ──────────────────────────────────────────────
function onTypeChange(type, isBrs) {
    document.getElementById('est_entreprise').value = isBrs ? '1' : '0';
    const bloc = document.getElementById('bloc-entreprise');
    if (bloc) bloc.classList.toggle('visible', isBrs);
}
</script>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?><?php /**PATH C:\Users\ph\bimotech-immo\resources\views/users/create.blade.php ENDPATH**/ ?>