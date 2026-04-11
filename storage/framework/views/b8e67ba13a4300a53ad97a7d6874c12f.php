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
     <?php $__env->slot('header', null, []); ?> Paramètres agence <?php $__env->endSlot(); ?>

<style>
/* ── LAYOUT ── */
.settings-grid { display:grid; grid-template-columns:1fr 300px; gap:24px; align-items:start; }

/* ── CARD ── */
.card { background:#fff; border:1px solid #e5e7eb; border-radius:14px; overflow:hidden; margin-bottom:18px; }
.card:last-child { margin-bottom:0; }
.card-hd { padding:16px 20px; border-bottom:1px solid #e5e7eb; display:flex; align-items:center; gap:10px; }
.card-icon { width:32px;height:32px;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0; }
.card-icon svg { width:16px;height:16px; }
.card-icon.gold   { background:#f5e9c9; } .card-icon.gold svg   { color:#8a6e2f; }
.card-icon.blue   { background:#dbeafe; } .card-icon.blue svg   { color:#1d4ed8; }
.card-icon.purple { background:#ede9fe; } .card-icon.purple svg { color:#7c3aed; }
.card-icon.green  { background:#dcfce7; } .card-icon.green svg  { color:#16a34a; }
.card-icon.gray   { background:#f3f4f6; } .card-icon.gray svg   { color:#6b7280; }
.card-title { font-family:'Syne',sans-serif; font-size:13px; font-weight:700; color:#0d1117; }
.card-body { padding:18px 20px; }

/* ── FORM ── */
.form-group { margin-bottom:15px; }
.form-group:last-child { margin-bottom:0; }
.form-label { display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:5px; }
.req { color:#dc2626;margin-left:2px; }
.opt { font-size:11px;font-weight:400;color:#9ca3af;margin-left:4px; }
.form-input, .form-select {
    width:100%;padding:9px 12px;border:1px solid #e5e7eb;border-radius:8px;
    font-size:13px;color:#0d1117;font-family:'DM Sans',sans-serif;
    background:#fff;outline:none;transition:border-color .15s,box-shadow .15s;
}
.form-input:focus, .form-select:focus {
    border-color:#c9a84c;box-shadow:0 0 0 3px rgba(201,168,76,.10);
}
.form-input.error { border-color:#dc2626; }
.form-row { display:grid;grid-template-columns:1fr 1fr;gap:12px; }
.form-error { font-size:12px;color:#dc2626;margin-top:4px; }
.form-hint  { font-size:11px;color:#9ca3af;margin-top:4px; }
.form-hint.warn { color:#d97706; }

/* ── LOGO UPLOAD ── */
.logo-zone {
    display:flex;align-items:center;gap:16px;
    padding:16px;background:#f9fafb;border:1.5px dashed #e5e7eb;
    border-radius:10px;transition:border-color .15s;cursor:pointer;
}
.logo-zone:hover { border-color:#c9a84c;background:#fdf8ef; }
.logo-preview {
    width:64px;height:64px;border-radius:10px;overflow:hidden;
    border:2px solid #e5e7eb;display:flex;align-items:center;justify-content:center;
    background:#fff;flex-shrink:0;
}
.logo-preview img { width:100%;height:100%;object-fit:contain; }
.logo-preview-placeholder { font-family:'Syne',sans-serif;font-size:20px;font-weight:800;color:#c9a84c; }
.logo-zone-text { font-size:13px;font-weight:500;color:#374151; }
.logo-zone-hint { font-size:11px;color:#9ca3af;margin-top:3px; }

/* ── COULEUR PICKER ── */
.color-picker-wrap { display:flex;align-items:center;gap:12px; }
.color-input-native { width:48px;height:40px;border:1px solid #e5e7eb;border-radius:8px;cursor:pointer;padding:4px;background:#fff; }
.color-swatches { display:flex;gap:6px;flex-wrap:wrap; }
.color-swatch { width:28px;height:28px;border-radius:6px;cursor:pointer;border:2px solid transparent;transition:transform .15s,border-color .15s; }
.color-swatch:hover { transform:scale(1.15);border-color:#fff;box-shadow:0 0 0 2px #c9a84c; }
.color-swatch.active { border-color:#fff;box-shadow:0 0 0 2px #0d1117; }
.color-hex-input { flex:1;padding:8px 12px;border:1px solid #e5e7eb;border-radius:8px;font-size:13px;font-family:monospace;color:#0d1117;outline:none;transition:border-color .15s; }
.color-hex-input:focus { border-color:#c9a84c; }

/* ── APERÇU SIDEBAR ── */
.preview-card { background:#0d1117;border-radius:14px;overflow:hidden;position:sticky;top:80px; }
.preview-hd { padding:14px 18px;border-bottom:1px solid rgba(255,255,255,.07); }
.preview-title { font-family:'Syne',sans-serif;font-size:12px;font-weight:700;color:rgba(255,255,255,.5);text-transform:uppercase;letter-spacing:1px; }

/* mini sidebar preview */
.mini-sidebar { background:#0d1117;border-radius:10px;overflow:hidden;margin:14px; }
.mini-sidebar-top { padding:12px 14px;border-bottom:1px solid rgba(255,255,255,.07);display:flex;align-items:center;gap:8px; }
.mini-logo-box { width:28px;height:28px;border-radius:6px;display:flex;align-items:center;justify-content:center;font-family:'Syne',sans-serif;font-size:11px;font-weight:800;color:#fff;flex-shrink:0; }
.mini-logo-img { width:100%;height:100%;object-fit:contain;border-radius:4px; }
.mini-agency-name { font-family:'Syne',sans-serif;font-size:12px;font-weight:700;color:#fff;white-space:nowrap;overflow:hidden;text-overflow:ellipsis; }
.mini-nav { padding:8px; }
.mini-nav-item { display:flex;align-items:center;gap:8px;padding:7px 10px;border-radius:6px;margin-bottom:2px; }
.mini-nav-item.active { }
.mini-nav-dot { width:6px;height:6px;border-radius:50%;background:rgba(255,255,255,.3); }
.mini-nav-dot.active { }
.mini-nav-line { height:7px;border-radius:4px;background:rgba(255,255,255,.12);flex:1; }
.mini-nav-line.short { width:40%; }

/* infos recap */
.info-row { display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid rgba(255,255,255,.05);font-size:12px; }
.info-row:last-child { border-bottom:none; }
.info-lbl { color:rgba(255,255,255,.4); }
.info-val { color:rgba(255,255,255,.8);font-weight:500;text-align:right;max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap; }
.info-val.missing { color:#f87171;font-style:italic; }

/* ── SUBMIT ── */
.submit-bar { display:flex;align-items:center;gap:10px;padding:14px 18px;border-top:1px solid #e5e7eb;background:#f9fafb; }
.btn-submit { flex:1;display:flex;align-items:center;justify-content:center;gap:7px;padding:11px 20px;background:#0d1117;color:#fff;border:none;border-radius:9px;font-size:14px;font-weight:600;font-family:'DM Sans',sans-serif;cursor:pointer;transition:opacity .15s; }
.btn-submit:hover { opacity:.88; }
.btn-submit svg { width:15px;height:15px; }
.btn-danger-sm { display:flex;align-items:center;gap:5px;padding:9px 14px;background:#fee2e2;color:#dc2626;border:1px solid #fecaca;border-radius:8px;font-size:12px;font-weight:500;font-family:'DM Sans',sans-serif;cursor:pointer;text-decoration:none;transition:background .15s; }
.btn-danger-sm:hover { background:#fecaca; }
.btn-danger-sm svg { width:13px;height:13px; }

/* ── ONBOARDING CHECKLIST ── */
.checklist { display:flex;flex-direction:column;gap:8px; }
.check-item { display:flex;align-items:center;gap:10px;padding:9px 12px;border-radius:8px;font-size:12px; }
.check-item.done    { background:#f0fdf4;color:#16a34a; }
.check-item.pending { background:#fff7ed;color:#d97706; }
.check-icon { width:18px;height:18px;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:10px;font-weight:700; }
.check-item.done    .check-icon { background:#16a34a;color:#fff; }
.check-item.pending .check-icon { background:#d97706;color:#fff; }
</style>

<div style="padding:24px 32px 48px">

    
    <div style="margin-bottom:22px">
        <h1 style="font-family:'Syne',sans-serif;font-size:22px;font-weight:700;color:#0d1117;letter-spacing:-.4px">Paramètres de l'agence</h1>
        <p style="font-size:13px;color:#6b7280;margin-top:3px">
            Configurez votre identité visuelle, vos coordonnées et vos informations légales.
        </p>
    </div>

    <form method="POST" action="<?php echo e(route('admin.agency.settings.update')); ?>" enctype="multipart/form-data" id="settings-form">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PATCH'); ?>

        <div class="settings-grid">

            
            <div>

                
                <div class="card">
                    <div class="card-hd">
                        <div class="card-icon purple">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M8 12l2 2 4-4"/></svg>
                        </div>
                        <div class="card-title">Identité visuelle</div>
                    </div>
                    <div class="card-body">

                        
                        <div class="form-group">
                            <label class="form-label">Logo de l'agence <span class="opt">(PNG, JPG · max 2 Mo)</span></label>
                            <div class="logo-zone" id="logo-zone" onclick="document.getElementById('logo-input').click()">
                                <div class="logo-preview" id="logo-preview">
                                    <?php if($agency->logo_path): ?>
                                        <img src="<?php echo e(Storage::url($agency->logo_path)); ?>" alt="<?php echo e($agency->name); ?>" id="logo-preview-img">
                                    <?php else: ?>
                                        <div class="logo-preview-placeholder" id="logo-preview-placeholder">
                                            <?php echo e(strtoupper(substr($agency->name, 0, 2))); ?>

                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <div class="logo-zone-text">Cliquer pour changer le logo</div>
                                    <div class="logo-zone-hint">Recommandé : 200×200px minimum · Fond transparent si possible</div>
                                </div>
                                <input type="file" name="logo" id="logo-input"
                                    accept="image/png,image/jpeg,image/webp"
                                    style="display:none"
                                    onchange="previewLogo(this)">
                            </div>
                            <?php $__errorArgs = ['logo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="form-error"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

                            
                            <?php if($agency->logo_path): ?>
                            <form method="POST" action="<?php echo e(route('admin.agency.logo.delete')); ?>" style="display:inline-block;margin-top:8px"
                                  onsubmit="return confirm('Supprimer le logo ?')">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="btn-danger-sm">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/></svg>
                                    Supprimer le logo actuel
                                </button>
                            </form>
                            <?php endif; ?>
                        </div>

                        
                        <div class="form-group">
                            <label class="form-label" for="couleur_primaire">Couleur principale de l'interface</label>
                            <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px">
                                <input type="color" id="color-native" value="<?php echo e(old('couleur_primaire', $agency->couleur_primaire ?? '#c9a84c')); ?>"
                                    oninput="syncColor(this.value)"
                                    style="width:44px;height:38px;border:1px solid #e5e7eb;border-radius:8px;cursor:pointer;padding:3px;background:#fff">
                                <input type="text" name="couleur_primaire" id="couleur_primaire"
                                    value="<?php echo e(old('couleur_primaire', $agency->couleur_primaire ?? '#c9a84c')); ?>"
                                    placeholder="#c9a84c"
                                    maxlength="7"
                                    class="color-hex-input <?php echo e($errors->has('couleur_primaire') ? 'error':''); ?>"
                                    oninput="syncColorFromHex(this.value)">
                            </div>
                            <div class="color-swatches">
                                <?php $__currentLoopData = ['#c9a84c','#1d4ed8','#16a34a','#dc2626','#7c3aed','#0d1117','#0891b2','#d97706','#1a3c5e','#be185d']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $color): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="color-swatch <?php echo e(($agency->couleur_primaire ?? '#c9a84c') === $color ? 'active':''); ?>"
                                     style="background:<?php echo e($color); ?>"
                                     onclick="syncColor('<?php echo e($color); ?>')"
                                     title="<?php echo e($color); ?>">
                                </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                            <?php $__errorArgs = ['couleur_primaire'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="form-error"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            <div class="form-hint">Utilisée pour la sidebar, les badges et les accents dans l'interface.</div>
                        </div>

                    </div>
                </div>

                
                <div class="card">
                    <div class="card-hd">
                        <div class="card-icon blue">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/></svg>
                        </div>
                        <div class="card-title">Informations de l'agence</div>
                    </div>
                    <div class="card-body">

                        <div class="form-group">
                            <label class="form-label" for="name">Nom de l'agence <span class="req">*</span></label>
                            <input type="text" name="name" id="name"
                                class="form-input <?php echo e($errors->has('name') ? 'error':''); ?>"
                                value="<?php echo e(old('name', $agency->name)); ?>"
                                placeholder="Ex: Immobilière Dakar"
                                oninput="updatePreview()">
                            <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="form-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label" for="email">Email <span class="req">*</span></label>
                                <input type="email" name="email" id="email"
                                    class="form-input <?php echo e($errors->has('email') ? 'error':''); ?>"
                                    value="<?php echo e(old('email', $agency->email)); ?>"
                                    placeholder="contact@agence.sn"
                                    oninput="updatePreview()">
                                <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="form-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="telephone">Téléphone</label>
                                <input type="text" name="telephone" id="telephone"
                                    class="form-input"
                                    value="<?php echo e(old('telephone', $agency->telephone)); ?>"
                                    placeholder="+221 77 XXX XX XX"
                                    oninput="updatePreview()">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="adresse">Adresse <span class="opt">(optionnel)</span></label>
                            <input type="text" name="adresse" id="adresse"
                                class="form-input"
                                value="<?php echo e(old('adresse', $agency->adresse)); ?>"
                                placeholder="Ex: 12 Avenue Cheikh Anta Diop, Dakar"
                                oninput="updatePreview()">
                        </div>

                    </div>
                </div>

                
                <div class="card">
                    <div class="card-hd">
                        <div class="card-icon gold">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </div>
                        <div class="card-title">Informations légales</div>
                    </div>
                    <div class="card-body">

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label" for="ninea">
                                    NINEA
                                    <span class="opt">(Numéro d'identification fiscal)</span>
                                </label>
                                <input type="text" name="ninea" id="ninea"
                                    class="form-input <?php echo e($errors->has('ninea') ? 'error':''); ?>"
                                    value="<?php echo e(old('ninea', $agency->ninea)); ?>"
                                    placeholder="ex: 00123456789"
                                    maxlength="30"
                                    oninput="updatePreview()">
                                <?php $__errorArgs = ['ninea'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="form-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                <?php if(!$agency->ninea): ?>
                                    <div class="form-hint warn">⚠ Requis pour les quittances conformes</div>
                                <?php endif; ?>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="rccm">
                                    RCCM
                                    <span class="opt">(Registre de commerce)</span>
                                </label>
                                <input type="text" name="rccm" id="rccm"
                                    class="form-input"
                                    value="<?php echo e(old('rccm', $agency->rccm ?? '')); ?>"
                                    placeholder="ex: SN-DKR-2024-XXX"
                                    maxlength="50">
                            </div>
                        </div>

                        <div class="form-hint">
                            Ces informations apparaissent sur les quittances PDF générées pour vos locataires.
                        </div>

                    </div>
                    <div class="submit-bar">
                        <button type="submit" class="btn-submit">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                            Sauvegarder les paramètres
                        </button>
                    </div>
                </div>

            </div>

            
            <div>

                
                <div class="preview-card">
                    <div class="preview-hd">
                        <div class="preview-title">Aperçu de la sidebar</div>
                    </div>

                    <div style="padding:14px">
                        <div class="mini-sidebar">
                            
                            <div class="mini-sidebar-top">
                                <div class="mini-logo-box" id="preview-logo-box" style="background:<?php echo e($agency->couleur_primaire ?? '#c9a84c'); ?>">
                                    <?php if($agency->logo_path): ?>
                                        <img src="<?php echo e(Storage::url($agency->logo_path)); ?>" class="mini-logo-img" id="preview-logo-img" alt="">
                                    <?php else: ?>
                                        <span id="preview-logo-initials"><?php echo e(strtoupper(substr($agency->name, 0, 2))); ?></span>
                                    <?php endif; ?>
                                </div>
                                <div style="flex:1;min-width:0">
                                    <div class="mini-agency-name" id="preview-name"><?php echo e($agency->name); ?></div>
                                    <div style="font-size:9px;color:rgba(255,255,255,.3);text-transform:uppercase;letter-spacing:.8px">Gestion Immo</div>
                                </div>
                            </div>

                            
                            <div class="mini-nav">
                                
                                <div class="mini-nav-item" id="preview-active-item" style="background:rgba(201,168,76,0.15)">
                                    <div class="mini-nav-dot" id="preview-active-dot" style="background:#c9a84c;box-shadow:0 0 0 3px rgba(201,168,76,0.2)"></div>
                                    <div class="mini-nav-line" style="background:#c9a84c;opacity:.6"></div>
                                </div>
                                
                                <?php $__currentLoopData = [1,2,3,4]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="mini-nav-item">
                                    <div class="mini-nav-dot"></div>
                                    <div class="mini-nav-line <?php echo e($i % 2 === 0 ? 'short':''); ?>"></div>
                                </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    </div>

                    
                    <div style="padding:0 14px 14px">
                        <div style="background:rgba(255,255,255,.04);border-radius:8px;padding:10px 14px">
                            <div class="info-row">
                                <div class="info-lbl">Nom</div>
                                <div class="info-val" id="info-name"><?php echo e($agency->name); ?></div>
                            </div>
                            <div class="info-row">
                                <div class="info-lbl">Email</div>
                                <div class="info-val" id="info-email"><?php echo e($agency->email ?? '—'); ?></div>
                            </div>
                            <div class="info-row">
                                <div class="info-lbl">Téléphone</div>
                                <div class="info-val <?php echo e(!$agency->telephone ? 'missing':''); ?>" id="info-tel">
                                    <?php echo e($agency->telephone ?? 'Non renseigné'); ?>

                                </div>
                            </div>
                            <div class="info-row">
                                <div class="info-lbl">NINEA</div>
                                <div class="info-val <?php echo e(!$agency->ninea ? 'missing':''); ?>" id="info-ninea">
                                    <?php echo e($agency->ninea ?? 'Non renseigné'); ?>

                                </div>
                            </div>
                            <div class="info-row">
                                <div class="info-lbl">Couleur</div>
                                <div style="display:flex;align-items:center;gap:6px">
                                    <div id="info-color-dot" style="width:12px;height:12px;border-radius:3px;background:<?php echo e($agency->couleur_primaire ?? '#c9a84c'); ?>"></div>
                                    <div class="info-val" id="info-color"><?php echo e($agency->couleur_primaire ?? '#c9a84c'); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                
                <div class="card" style="margin-top:18px">
                    <div class="card-hd">
                        <div class="card-icon green">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
                        </div>
                        <div class="card-title">Checklist configuration</div>
                    </div>
                    <div class="card-body">
                        <div class="checklist">
                            <?php
                                $checks = [
                                    ['label' => 'Nom de l\'agence', 'done' => !empty($agency->name)],
                                    ['label' => 'Email de contact', 'done' => !empty($agency->email)],
                                    ['label' => 'Téléphone',        'done' => !empty($agency->telephone)],
                                    ['label' => 'Adresse physique', 'done' => !empty($agency->adresse)],
                                    ['label' => 'NINEA fiscal',     'done' => !empty($agency->ninea)],
                                    ['label' => 'Logo uploadé',     'done' => !empty($agency->logo_path)],
                                    ['label' => 'Couleur définie',  'done' => !empty($agency->couleur_primaire)],
                                ];
                                $nbDone = collect($checks)->where('done', true)->count();
                            ?>

                            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px">
                                <span style="font-size:12px;font-weight:600;color:#0d1117"><?php echo e($nbDone); ?> / <?php echo e(count($checks)); ?> complété(s)</span>
                                <div style="display:flex;align-items:center;gap:4px">
                                    <?php for($i = 0; $i < count($checks); $i++): ?>
                                    <div style="height:4px;width:<?php echo e(floor(120/count($checks))); ?>px;border-radius:99px;background:<?php echo e($i < $nbDone ? '#16a34a':'#e5e7eb'); ?>"></div>
                                    <?php endfor; ?>
                                </div>
                            </div>

                            <?php $__currentLoopData = $checks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $check): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="check-item <?php echo e($check['done'] ? 'done':'pending'); ?>">
                                <div class="check-icon"><?php echo e($check['done'] ? '✓' : '!'); ?></div>
                                <div style="font-weight:500"><?php echo e($check['label']); ?></div>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </form>

</div>

<script>
// ── Couleur ──────────────────────────────────────────────────────────
function syncColor(hex) {
    // Normaliser
    if (!hex.startsWith('#')) hex = '#' + hex;
    document.getElementById('couleur_primaire').value = hex;
    document.getElementById('color-native').value     = hex;

    // Aperçu sidebar
    document.getElementById('preview-logo-box').style.background   = hex;
    document.getElementById('preview-active-item').style.background = hexToRgba(hex, 0.15);
    document.getElementById('preview-active-dot').style.background  = hex;
    document.getElementById('preview-active-dot').style.boxShadow   = `0 0 0 3px ${hexToRgba(hex, 0.2)}`;

    // Info couleur
    document.getElementById('info-color-dot').style.background = hex;
    document.getElementById('info-color').textContent = hex;

    // Swatches actives
    document.querySelectorAll('.color-swatch').forEach(s => {
        s.classList.toggle('active', s.style.background === hex || rgbToHex(s.style.background) === hex);
    });
}

function syncColorFromHex(val) {
    if (/^#[0-9A-Fa-f]{6}$/.test(val)) syncColor(val);
}

function hexToRgba(hex, alpha) {
    const r = parseInt(hex.slice(1,3),16);
    const g = parseInt(hex.slice(3,5),16);
    const b = parseInt(hex.slice(5,7),16);
    return `rgba(${r},${g},${b},${alpha})`;
}

function rgbToHex(rgb) {
    const m = rgb.match(/\d+/g);
    if (!m) return '';
    return '#' + m.slice(0,3).map(x => parseInt(x).toString(16).padStart(2,'0')).join('');
}

// ── Aperçu nom agence ─────────────────────────────────────────────────
function updatePreview() {
    const name  = document.getElementById('name').value || '—';
    const email = document.getElementById('email').value || '—';
    const tel   = document.getElementById('telephone').value;
    const ninea = document.getElementById('ninea').value;

    document.getElementById('preview-name').textContent  = name;
    document.getElementById('info-name').textContent     = name;
    document.getElementById('info-email').textContent    = email;

    const telEl = document.getElementById('info-tel');
    telEl.textContent = tel || 'Non renseigné';
    telEl.classList.toggle('missing', !tel);

    const nineaEl = document.getElementById('info-ninea');
    nineaEl.textContent = ninea || 'Non renseigné';
    nineaEl.classList.toggle('missing', !ninea);

    // Initiales logo
    const initEl = document.getElementById('preview-logo-initials');
    if (initEl) initEl.textContent = name.substring(0,2).toUpperCase();
}

// ── Aperçu logo ───────────────────────────────────────────────────────
function previewLogo(input) {
    if (!input.files || !input.files[0]) return;
    const reader = new FileReader();
    reader.onload = e => {
        const box = document.getElementById('preview-logo-box');
        const ph  = document.getElementById('logo-preview-placeholder');
        const zone = document.querySelector('.logo-preview');

        // Sidebar preview
        let img = document.getElementById('preview-logo-img');
        if (!img) {
            img = document.createElement('img');
            img.id = 'preview-logo-img';
            img.className = 'mini-logo-img';
            const initials = document.getElementById('preview-logo-initials');
            if (initials) initials.remove();
            box.appendChild(img);
        }
        img.src = e.target.result;

        // Zone upload preview
        let previewImg = document.getElementById('logo-preview-img');
        if (!previewImg) {
            if (ph) ph.remove();
            previewImg = document.createElement('img');
            previewImg.id = 'logo-preview-img';
            previewImg.style.cssText = 'width:100%;height:100%;object-fit:contain';
            zone.appendChild(previewImg);
        }
        previewImg.src = e.target.result;
    };
    reader.readAsDataURL(input.files[0]);
}

// Init
updatePreview();
syncColor(document.getElementById('couleur_primaire').value);
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
<?php endif; ?><?php /**PATH C:\Users\ph\bimotech-immo\resources\views/admin/agency-settings.blade.php ENDPATH**/ ?>