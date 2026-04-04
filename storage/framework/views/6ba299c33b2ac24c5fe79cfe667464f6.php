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
     <?php $__env->slot('header', null, []); ?> Nouveau contrat <?php $__env->endSlot(); ?>

<style>
/* ── LAYOUT ── */
.form-grid { display:grid; grid-template-columns:1fr 300px; gap:24px; align-items:start; }

/* ── CARDS ── */
.card { background:#fff; border:1px solid #e5e7eb; border-radius:14px; overflow:hidden; margin-bottom:18px; }
.card:last-child { margin-bottom:0; }
.card-hd { padding:16px 20px; border-bottom:1px solid #e5e7eb; display:flex; align-items:center; gap:10px; }
.card-icon { width:32px;height:32px;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0; }
.card-icon svg { width:16px;height:16px; }
.card-icon.gold   { background:#f5e9c9; } .card-icon.gold svg   { color:#8a6e2f; }
.card-icon.blue   { background:#dbeafe; } .card-icon.blue svg   { color:#1d4ed8; }
.card-icon.green  { background:#dcfce7; } .card-icon.green svg  { color:#16a34a; }
.card-icon.purple { background:#ede9fe; } .card-icon.purple svg { color:#7c3aed; }
.card-icon.gray   { background:#f3f4f6; } .card-icon.gray svg   { color:#6b7280; }
.card-title { font-family:'Syne',sans-serif; font-size:13px; font-weight:700; color:#0d1117; }
.card-body { padding:20px; }

/* ── FORM ── */
.form-group { margin-bottom:16px; }
.form-group:last-child { margin-bottom:0; }
.form-label { display:block; font-size:12px; font-weight:600; color:#374151; margin-bottom:6px; }
.form-label .req { color:#dc2626; margin-left:2px; }
.form-label .opt { font-size:11px; font-weight:400; color:#9ca3af; margin-left:4px; }
.form-input, .form-select, .form-textarea {
    width:100%; padding:9px 13px;
    border:1px solid #e5e7eb; border-radius:8px;
    font-size:13px; color:#0d1117; font-family:'DM Sans',sans-serif;
    background:#fff; outline:none;
    transition:border-color .15s, box-shadow .15s;
}
.form-input:focus, .form-select:focus, .form-textarea:focus {
    border-color:#c9a84c; box-shadow:0 0 0 3px rgba(201,168,76,0.10);
}
.form-input.error, .form-select.error { border-color:#dc2626; }
.form-textarea { resize:vertical; min-height:80px; }
.form-select { cursor:pointer; }
.form-row   { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
.form-row-3 { display:grid; grid-template-columns:1fr 1fr 1fr; gap:14px; }
.form-error { font-size:12px;color:#dc2626;margin-top:5px;font-weight:500; }
.form-hint  { font-size:11px;color:#9ca3af;margin-top:5px; }

/* ── BIEN SÉLECTIONNÉ ── */
.bien-card {
    background:#0d1117; border-radius:10px; padding:14px 16px;
    margin-top:10px; display:none;
}
.bc-grid { display:grid; grid-template-columns:1fr 1fr; gap:10px; }
.bc-lbl { font-size:9px;font-weight:600;letter-spacing:1px;text-transform:uppercase;color:rgba(255,255,255,.35);margin-bottom:2px; }
.bc-val { font-size:13px;font-weight:600;color:#fff; }
.bc-val.gold { color:#c9a84c; }

/* ── RÉCAPITULATIF (sticky) ── */
.recap-card { background:#0d1117; border-radius:14px; overflow:hidden; position:sticky; top:80px; }
.recap-hd { padding:16px 20px; border-bottom:1px solid rgba(255,255,255,.07); }
.recap-title { font-family:'Syne',sans-serif; font-size:13px; font-weight:700; color:#fff; }
.recap-body { padding:16px 20px; }
.rp-row { display:flex; justify-content:space-between; align-items:center; padding:8px 0; border-bottom:1px solid rgba(255,255,255,.05); }
.rp-row:last-child { border-bottom:none; }
.rp-lbl { font-size:12px; color:rgba(255,255,255,.5); }
.rp-val { font-family:'Syne',sans-serif; font-size:13px; font-weight:600; color:#fff; }
.rp-val.green { color:#4ade80; }
.rp-val.gold  { color:#c9a84c; }
.rp-val.muted { color:rgba(255,255,255,.3); font-weight:400; font-size:12px; }
.rp-sep { height:1px; background:rgba(255,255,255,.08); margin:6px 0; }
.rp-block { background:rgba(201,168,76,.08); border:1px solid rgba(201,168,76,.15); border-radius:9px; padding:12px 14px; margin-top:12px; }
.rp-block-lbl { font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:.8px;color:rgba(201,168,76,.6);margin-bottom:4px; }
.rp-block-val { font-family:'Syne',sans-serif; font-size:18px; font-weight:700; color:#c9a84c; letter-spacing:-.3px; }
.rp-block-sub { font-size:11px; color:rgba(201,168,76,.5); margin-top:3px; }

/* ── TYPE BAIL PILLS ── */
.bail-grid { display:grid; grid-template-columns:repeat(2,1fr); gap:8px; }
.bail-pill { position:relative; }
.bail-pill input { position:absolute;opacity:0;width:0;height:0; }
.bail-pill label {
    display:flex;align-items:center;justify-content:center;
    padding:10px 12px;border:1.5px solid #e5e7eb;border-radius:9px;
    cursor:pointer;transition:all .15s;
    font-size:12px;font-weight:500;color:#6b7280;text-align:center;
    background:#fff; gap:6px;
}
.bail-pill label svg { width:14px;height:14px; }
.bail-pill input:checked + label { border-color:#c9a84c;background:#f5e9c9;color:#8a6e2f; }
.bail-pill label:hover { border-color:#c9a84c;background:#fdf8ef; }

/* ── SUBMIT ── */
.submit-bar { display:flex;align-items:center;gap:12px;padding:16px 20px;border-top:1px solid #e5e7eb;background:#f9fafb; }
.btn-submit { flex:1;display:flex;align-items:center;justify-content:center;gap:8px;padding:12px 24px;background:#0d1117;color:#fff;border:none;border-radius:9px;font-size:14px;font-weight:600;font-family:'DM Sans',sans-serif;cursor:pointer;transition:opacity .15s; }
.btn-submit:hover { opacity:.88; }
.btn-submit svg { width:16px;height:16px; }
.btn-cancel { padding:12px 20px;background:#fff;color:#6b7280;border:1px solid #e5e7eb;border-radius:9px;font-size:13px;font-weight:500;font-family:'DM Sans',sans-serif;cursor:pointer;text-decoration:none;transition:background .15s; }
.btn-cancel:hover { background:#f9fafb; }

/* ── NOUVEAU LOCATAIRE INLINE ── */
.nouveau-loc-btn {
    display:flex;align-items:center;gap:5px;padding:5px 10px;
    border:1px dashed #e5e7eb;border-radius:7px;
    font-size:11px;font-weight:500;color:#6b7280;
    background:none;cursor:pointer;transition:all .15s;
    margin-top:6px; width:fit-content;
}
.nouveau-loc-btn:hover { border-color:#c9a84c;color:#8a6e2f; }
.nouveau-loc-btn svg { width:12px;height:12px; }
</style>

<div style="padding:24px 32px 48px">

    
    <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:#6b7280;margin-bottom:20px">
        <a href="<?php echo e(route('admin.contrats.index')); ?>" style="color:#6b7280;text-decoration:none">Contrats</a>
        <svg style="width:12px;height:12px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
        <span style="color:#0d1117;font-weight:500">Nouveau contrat</span>
    </div>

    <div style="margin-bottom:22px">
        <h1 style="font-family:'Syne',sans-serif;font-size:22px;font-weight:700;color:#0d1117;letter-spacing:-.4px">Créer un contrat de bail</h1>
        <p style="font-size:13px;color:#6b7280;margin-top:3px">Le loyer contractuel est calculé automatiquement (loyer nu + charges + TOM).</p>
    </div>

    <form method="POST" action="<?php echo e(route('admin.contrats.store')); ?>" id="form-contrat">
        <?php echo csrf_field(); ?>

        <div class="form-grid">

            
            <div>

                
                <div class="card">
                    <div class="card-hd">
                        <div class="card-icon gold">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                        </div>
                        <div class="card-title">Bien & locataire</div>
                    </div>
                    <div class="card-body">

                        
                        <div class="form-group">
                            <label class="form-label" for="bien_id">Bien à louer <span class="req">*</span></label>
                            <select name="bien_id" id="bien_id" class="form-select <?php echo e($errors->has('bien_id') ? 'error' : ''); ?>"
                                    onchange="chargerInfosBien(this)">
                                <option value="">— Sélectionner un bien disponible —</option>
                                <?php $__currentLoopData = $biens; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bien): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($bien->id); ?>"
                                        data-loyer="<?php echo e($bien->loyer_mensuel); ?>"
                                        data-commission="<?php echo e($bien->taux_commission); ?>"
                                        data-proprietaire="<?php echo e($bien->proprietaire->name); ?>"
                                        <?php echo e(old('bien_id', $bienPreselectionne?->id) == $bien->id ? 'selected' : ''); ?>>
                                        <?php echo e($bien->reference); ?> — <?php echo e(\App\Models\Bien::TYPES[$bien->type] ?? $bien->type); ?> · <?php echo e($bien->adresse); ?>, <?php echo e($bien->ville); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php $__errorArgs = ['bien_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="form-error"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

                            
                            <div class="bien-card" id="bien-card">
                                <div class="bc-grid">
                                    <div>
                                        <div class="bc-lbl">Propriétaire</div>
                                        <div class="bc-val" id="bc-proprio">—</div>
                                    </div>
                                    <div>
                                        <div class="bc-lbl">Loyer suggéré</div>
                                        <div class="bc-val gold" id="bc-loyer">—</div>
                                    </div>
                                    <div>
                                        <div class="bc-lbl">Taux commission</div>
                                        <div class="bc-val gold" id="bc-commission">—</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        
                        <div class="form-group">
                            <label class="form-label" for="locataire_id">Locataire <span class="req">*</span></label>
                            <div style="display:flex;gap:8px;align-items:flex-start">
                                <select name="locataire_id" id="locataire_id"
                                        class="form-select <?php echo e($errors->has('locataire_id') ? 'error' : ''); ?>"
                                        style="flex:1">
                                    <option value="">— Sélectionner un locataire —</option>
                                    <?php $__currentLoopData = $locataires; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($loc->id); ?>"
                                            <?php echo e(old('locataire_id') == $loc->id ? 'selected' : ''); ?>>
                                            <?php echo e($loc->name); ?><?php if($loc->telephone): ?> — <?php echo e($loc->telephone); ?><?php endif; ?>
                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <button type="button" onclick="ouvrirModaleLocataire()"
                                        style="white-space:nowrap;padding:9px 14px;border:1px solid #e5e7eb;border-radius:8px;font-size:12px;font-weight:500;color:#374151;background:#fff;cursor:pointer;flex-shrink:0;font-family:'DM Sans',sans-serif">
                                    + Nouveau
                                </button>
                            </div>
                            <?php $__errorArgs = ['locataire_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="form-error"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                    </div>
                </div>

                
                <div class="card">
                    <div class="card-hd">
                        <div class="card-icon blue">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        </div>
                        <div class="card-title">Type de bail & durée</div>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-label">Type de bail <span class="req">*</span></label>
                            <div class="bail-grid">
                                <?php $__currentLoopData = \App\Models\Contrat::TYPES_BAIL; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="bail-pill">
                                    <input type="radio" name="type_bail" id="bail_<?php echo e($key); ?>"
                                        value="<?php echo e($key); ?>"
                                        <?php echo e(old('type_bail', 'habitation') === $key ? 'checked' : ''); ?>>
                                    <label for="bail_<?php echo e($key); ?>">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <?php if($key === 'habitation'): ?>
                                                <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                                            <?php elseif($key === 'commercial'): ?>
                                                <rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v16"/>
                                            <?php elseif($key === 'mixte'): ?>
                                                <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><rect x="9" y="12" width="6" height="9"/>
                                            <?php else: ?>
                                                <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                                            <?php endif; ?>
                                        </svg>
                                        <?php echo e($label); ?>

                                    </label>
                                </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                            <?php $__errorArgs = ['type_bail'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="form-error"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label" for="date_debut">Date de début <span class="req">*</span></label>
                                <input type="date" name="date_debut" id="date_debut"
                                    class="form-input <?php echo e($errors->has('date_debut') ? 'error' : ''); ?>"
                                    value="<?php echo e(old('date_debut', now()->format('Y-m-d'))); ?>"
                                    onchange="mettreAJourRecap()">
                                <?php $__errorArgs = ['date_debut'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="form-error"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="date_fin">Date de fin <span class="opt">(vide = indéterminée)</span></label>
                                <input type="date" name="date_fin" id="date_fin"
                                    class="form-input"
                                    value="<?php echo e(old('date_fin')); ?>"
                                    onchange="mettreAJourRecap()">
                                <?php $__errorArgs = ['date_fin'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="form-error"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="reference_bail">Référence bail <span class="opt">(auto si vide)</span></label>
                            <input type="text" name="reference_bail" id="reference_bail"
                                class="form-input"
                                value="<?php echo e(old('reference_bail')); ?>"
                                placeholder="Ex: BAIL-DKR-2024 — Sinon : BIMO-<?php echo e(now()->year); ?>-{ID}">
                            <?php $__errorArgs = ['reference_bail'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="form-error"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                </div>

                
                <div class="card">
                    <div class="card-hd">
                        <div class="card-icon green">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
                        </div>
                        <div class="card-title">Ventilation du loyer</div>
                    </div>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label" for="loyer_nu">Loyer nu <span class="req">*</span></label>
                                <input type="number" name="loyer_nu" id="loyer_nu"
                                    class="form-input <?php echo e($errors->has('loyer_nu') ? 'error' : ''); ?>"
                                    value="<?php echo e(old('loyer_nu')); ?>"
                                    placeholder="Ex: 180000" min="1" step="500"
                                    oninput="mettreAJourRecap()">
                                <?php $__errorArgs = ['loyer_nu'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="form-error"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="charges_mensuelles">Charges mensuelles <span class="opt">(optionnel)</span></label>
                                <input type="number" name="charges_mensuelles" id="charges_mensuelles"
                                    class="form-input"
                                    value="<?php echo e(old('charges_mensuelles', 0)); ?>"
                                    placeholder="0" min="0" step="500"
                                    oninput="mettreAJourRecap()">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label" for="tom_amount">TOM <span class="opt">(Taxe Ordures Ménagères)</span></label>
                                <input type="number" name="tom_amount" id="tom_amount"
                                    class="form-input"
                                    value="<?php echo e(old('tom_amount', 0)); ?>"
                                    placeholder="0" min="0" step="100"
                                    oninput="mettreAJourRecap()">
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="caution">Caution <span class="req">*</span></label>
                                <input type="number" name="caution" id="caution"
                                    class="form-input <?php echo e($errors->has('caution') ? 'error' : ''); ?>"
                                    value="<?php echo e(old('caution')); ?>"
                                    placeholder="Ex: 180000" min="0" step="500"
                                    oninput="mettreAJourRecap()">
                                <?php $__errorArgs = ['caution'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="form-error"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label" for="frais_agence">Frais d'agence <span class="opt">(optionnel)</span></label>
                                <input type="number" name="frais_agence" id="frais_agence"
                                    class="form-input"
                                    value="<?php echo e(old('frais_agence', 0)); ?>"
                                    placeholder="0" min="0" step="500">
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="nombre_mois_caution">Nombre de mois caution</label>
                                <input type="number" name="nombre_mois_caution" id="nombre_mois_caution"
                                    class="form-input"
                                    value="<?php echo e(old('nombre_mois_caution', 1)); ?>"
                                    min="1" max="6">
                            </div>
                        </div>
                    </div>
                </div>

                
                <div class="card">
                    <div class="card-hd">
                        <div class="card-icon purple">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 010 14.14M4.93 4.93a10 10 0 000 14.14"/></svg>
                        </div>
                        <div class="card-title">Options avancées</div>
                    </div>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label" for="indexation_annuelle">Indexation annuelle <span class="opt">(%)</span></label>
                                <input type="number" name="indexation_annuelle" id="indexation_annuelle"
                                    class="form-input"
                                    value="<?php echo e(old('indexation_annuelle', 0)); ?>"
                                    placeholder="0" min="0" max="20" step="0.5">
                                <div class="form-hint">Révision annuelle du loyer en %</div>
                            </div>
                        </div>

<?php echo $__env->make('admin.contrats._section-fiscal', ['contrat' => null], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        
                        <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.8px;color:#9ca3af;margin:16px 0 12px">Garant (optionnel)</div>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label" for="garant_nom">Nom du garant</label>
                                <input type="text" name="garant_nom" id="garant_nom"
                                    class="form-input" value="<?php echo e(old('garant_nom')); ?>"
                                    placeholder="Nom complet">
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="garant_telephone">Téléphone garant</label>
                                <input type="text" name="garant_telephone" id="garant_telephone"
                                    class="form-input" value="<?php echo e(old('garant_telephone')); ?>"
                                    placeholder="+221 7X XXX XX XX">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="garant_adresse">Adresse garant</label>
                            <input type="text" name="garant_adresse" id="garant_adresse"
                                class="form-input" value="<?php echo e(old('garant_adresse')); ?>"
                                placeholder="Adresse complète">
                        </div>

                        
                        <div class="form-group">
                            <label class="form-label" for="observations">Observations <span class="opt">(optionnel)</span></label>
                            <textarea name="observations" id="observations"
                                class="form-textarea"
                                placeholder="Clauses particulières, état des lieux, remarques…"><?php echo e(old('observations')); ?></textarea>
                        </div>
                    </div>

                    <div class="submit-bar">
                        <a href="<?php echo e(route('admin.contrats.index')); ?>" class="btn-cancel">Annuler</a>
                        <button type="submit" class="btn-submit">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                            Créer le contrat
                        </button>
                    </div>
                </div>

            </div>

            
            <div>
                <div class="recap-card">
                    <div class="recap-hd">
                        <div class="recap-title">Récapitulatif</div>
                    </div>
                    <div class="recap-body">
                        <div class="rp-row">
                            <div class="rp-lbl">Loyer nu</div>
                            <div class="rp-val" id="rp-loyer">— F</div>
                        </div>
                        <div class="rp-row" id="rp-charges-row" style="display:none">
                            <div class="rp-lbl">+ Charges</div>
                            <div class="rp-val" id="rp-charges">— F</div>
                        </div>
                        <div class="rp-row" id="rp-tom-row" style="display:none">
                            <div class="rp-lbl">+ TOM</div>
                            <div class="rp-val" id="rp-tom">— F</div>
                        </div>
                        <div class="rp-block">
                            <div class="rp-block-lbl">Loyer contractuel mensuel</div>
                            <div class="rp-block-val"><span id="rp-contractuel">0</span> F</div>
                            <div class="rp-block-sub">= loyer nu + charges + TOM</div>
                        </div>

                        <div class="rp-sep" style="margin:14px 0"></div>

                        <div class="rp-row">
                            <div class="rp-lbl">Caution</div>
                            <div class="rp-val green" id="rp-caution">— F</div>
                        </div>
                        <div class="rp-row">
                            <div class="rp-lbl">Durée</div>
                            <div class="rp-val" id="rp-duree">—</div>
                        </div>
                        <div class="rp-row">
                            <div class="rp-lbl">Début</div>
                            <div class="rp-val" id="rp-debut">—</div>
                        </div>
                        <div class="rp-row">
                            <div class="rp-lbl">Fin</div>
                            <div class="rp-val muted" id="rp-fin">Indéterminée</div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </form>

</div>


<div id="modale-locataire" style="display:none;position:fixed;inset:0;z-index:200;background:rgba(0,0,0,.5);display:flex;align-items:center;justify-content:center;padding:24px">
    <div style="background:#fff;border-radius:16px;width:100%;max-width:480px;overflow:hidden;box-shadow:0 24px 48px rgba(0,0,0,.2)">
        <div style="padding:20px 24px;border-bottom:1px solid #e5e7eb;display:flex;align-items:center;justify-content:space-between">
            <div style="font-family:'Syne',sans-serif;font-size:15px;font-weight:700;color:#0d1117">Nouveau locataire</div>
            <button onclick="fermerModale()" style="background:none;border:none;cursor:pointer;color:#6b7280;padding:4px">
                <svg style="width:18px;height:18px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <form id="form-locataire-rapide" style="padding:20px 24px">
            <?php echo csrf_field(); ?>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:14px">
                <div>
                    <label class="form-label">Nom complet <span class="req">*</span></label>
                    <input type="text" name="name" id="loc-name" class="form-input" placeholder="Prénom NOM">
                </div>
                <div>
                    <label class="form-label">Email <span class="req">*</span></label>
                    <input type="email" name="email" id="loc-email" class="form-input" placeholder="email@exemple.com">
                </div>
                <div>
                    <label class="form-label">Téléphone</label>
                    <input type="text" name="telephone" id="loc-telephone" class="form-input" placeholder="+221 7X XXX XX XX">
                </div>
                <div>
                    <label class="form-label">Ville</label>
                    <input type="text" name="ville" id="loc-ville" class="form-input" placeholder="Dakar">
                </div>
            </div>
            <div id="loc-error" style="display:none;background:#fee2e2;border:1px solid #fecaca;border-radius:8px;padding:10px 12px;font-size:12px;color:#dc2626;margin-bottom:12px"></div>
            <div style="display:flex;gap:10px">
                <button type="button" onclick="fermerModale()" style="flex:1;padding:10px;border:1px solid #e5e7eb;border-radius:8px;font-size:13px;background:#fff;cursor:pointer;color:#6b7280;font-family:'DM Sans',sans-serif">Annuler</button>
                <button type="button" onclick="creerLocataire()" style="flex:2;padding:10px;background:#0d1117;color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;font-family:'DM Sans',sans-serif">Créer le locataire</button>
            </div>
        </form>
    </div>
</div>

<script>
// ── Infos bien sélectionné ────────────────────────────────────────────
function chargerInfosBien(select) {
    const opt = select.options[select.selectedIndex];
    if (!opt.value) {
        document.getElementById('bien-card').style.display = 'none';
        return;
    }
    document.getElementById('bc-proprio').textContent    = opt.dataset.proprietaire || '—';
    document.getElementById('bc-loyer').textContent      = parseInt(opt.dataset.loyer).toLocaleString('fr-FR') + ' FCFA';
    document.getElementById('bc-commission').textContent = opt.dataset.commission + '%';
    document.getElementById('bien-card').style.display   = 'block';

    // Pré-remplir loyer nu
    const loyerInput = document.getElementById('loyer_nu');
    if (!loyerInput.value || loyerInput.value === '0') {
        loyerInput.value = opt.dataset.loyer;
    }
    // Pré-remplir caution = 1 mois de loyer
    const cautionInput = document.getElementById('caution');
    if (!cautionInput.value || cautionInput.value === '0') {
        cautionInput.value = opt.dataset.loyer;
    }
    mettreAJourRecap();
}

// ── Récapitulatif en temps réel ───────────────────────────────────────
function mettreAJourRecap() {
    const loyerNu  = parseFloat(document.getElementById('loyer_nu').value)          || 0;
    const charges  = parseFloat(document.getElementById('charges_mensuelles').value) || 0;
    const tom      = parseFloat(document.getElementById('tom_amount').value)          || 0;
    const caution  = parseFloat(document.getElementById('caution').value)             || 0;
    const contractuel = loyerNu + charges + tom;
    const fmt = n => Math.round(n).toLocaleString('fr-FR');

    document.getElementById('rp-loyer').textContent       = fmt(loyerNu) + ' F';
    document.getElementById('rp-contractuel').textContent = fmt(contractuel);
    document.getElementById('rp-caution').textContent     = fmt(caution) + ' F';

    const chargesRow = document.getElementById('rp-charges-row');
    const tomRow     = document.getElementById('rp-tom-row');
    chargesRow.style.display = charges > 0 ? '' : 'none';
    tomRow.style.display     = tom > 0     ? '' : 'none';
    document.getElementById('rp-charges').textContent = fmt(charges) + ' F';
    document.getElementById('rp-tom').textContent     = fmt(tom) + ' F';

    // Dates
    const debut = document.getElementById('date_debut').value;
    const fin   = document.getElementById('date_fin').value;
    if (debut) {
        document.getElementById('rp-debut').textContent = new Date(debut).toLocaleDateString('fr-FR');
    }
    if (fin) {
        document.getElementById('rp-fin').textContent = new Date(fin).toLocaleDateString('fr-FR');
        // Durée en mois
        const d = new Date(debut), f = new Date(fin);
        const mois = Math.round((f - d) / (1000 * 60 * 60 * 24 * 30.44));
        document.getElementById('rp-duree').textContent = mois + ' mois';
    } else {
        document.getElementById('rp-fin').textContent   = 'Indéterminée';
        document.getElementById('rp-duree').textContent = 'Indéterminée';
    }
}

// ── Modale nouveau locataire ──────────────────────────────────────────
function ouvrirModaleLocataire() {
    document.getElementById('modale-locataire').style.display = 'flex';
}
function fermerModale() {
    document.getElementById('modale-locataire').style.display = 'none';
}

async function creerLocataire() {
    const errDiv = document.getElementById('loc-error');
    errDiv.style.display = 'none';

    const data = {
        name:      document.getElementById('loc-name').value,
        email:     document.getElementById('loc-email').value,
        telephone: document.getElementById('loc-telephone').value,
        ville:     document.getElementById('loc-ville').value,
    };

    if (!data.name || !data.email) {
        errDiv.textContent = 'Le nom et l\'email sont obligatoires.';
        errDiv.style.display = 'block';
        return;
    }

    try {
        const r = await fetch('<?php echo e(route("admin.contrats.locataire-rapide")); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                'Accept': 'application/json',
            },
            body: JSON.stringify(data),
        });

        const res = await r.json();

        if (!r.ok) {
            const msgs = res.errors ? Object.values(res.errors).flat().join(' ') : (res.message || 'Erreur');
            errDiv.textContent = msgs;
            errDiv.style.display = 'block';
            return;
        }

        // Ajouter l'option dans le select
        const select = document.getElementById('locataire_id');
        const opt = new Option(`${res.user.name}${res.user.telephone ? ' — ' + res.user.telephone : ''}`, res.user.id, true, true);
        select.add(opt);
        fermerModale();

        // Reset modale
        ['loc-name','loc-email','loc-telephone','loc-ville'].forEach(id => {
            document.getElementById(id).value = '';
        });

    } catch(e) {
        errDiv.textContent = 'Erreur réseau. Veuillez réessayer.';
        errDiv.style.display = 'block';
    }
}

// ── Init ──────────────────────────────────────────────────────────────
mettreAJourRecap();

// Pré-remplir si bien présélectionné
<?php if($bienPreselectionne): ?>
document.getElementById('bien_id').dispatchEvent(new Event('change'));
<?php endif; ?>
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
<?php endif; ?><?php /**PATH C:\Users\ph\bimotech-immo\resources\views/admin/contrats/create.blade.php ENDPATH**/ ?>