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
     <?php $__env->slot('header', null, []); ?> Nouveau <?php echo e($role === 'proprietaire' ? 'propriétaire' : 'locataire'); ?> <?php $__env->endSlot(); ?>

    
    <div style="display:flex;align-items:center;gap:12px;" class="section-gap">
        <a href="<?php echo e($role === 'proprietaire' ? route('admin.users.proprietaires') : route('admin.users.locataires')); ?>"
           style="display:flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:var(--radius-sm);border:1px solid var(--border);color:var(--text-2);transition:background .15s;"
           onmouseenter="this.style.background='var(--bg)'"
           onmouseleave="this.style.background='transparent'">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:16px;height:16px;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 style="font-size:20px;font-weight:700;color:var(--text);letter-spacing:-.3px;">
                Nouveau <?php echo e($role === 'proprietaire' ? 'propriétaire' : 'locataire'); ?>

            </h1>
            <p style="font-size:13px;color:var(--text-3);margin-top:2px;">
                Renseignez les informations du <?php echo e($role === 'proprietaire' ? 'propriétaire' : 'locataire'); ?>

            </p>
        </div>
    </div>

    <div style="max-width:680px;">
        <div class="card">
            <div class="card-body">

                
                <?php if($errors->any()): ?>
                    <div class="alert alert-error" style="margin-bottom:20px;">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div>❌ <?php echo e($error); ?></div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?php echo e(route('admin.users.store')); ?>">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="role" value="<?php echo e($role); ?>">

                    
                    <div style="margin-bottom:24px;">
                        <div style="font-size:11px;font-weight:700;color:var(--text-3);text-transform:uppercase;letter-spacing:.5px;margin-bottom:16px;padding-bottom:8px;border-bottom:1px solid var(--border);">
                            👤 Informations personnelles
                        </div>

                        
                        <div style="margin-bottom:16px;">
                            <label class="form-label">Nom complet <span style="color:#ef4444;">*</span></label>
                            <input type="text" name="name" value="<?php echo e(old('name')); ?>"
                                   placeholder="Prénom et Nom" class="input" required>
                            <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div style="font-size:12px;color:#dc2626;margin-top:4px;"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:16px;">
                            <div>
                                <label class="form-label">Email <span style="color:#ef4444;">*</span></label>
                                <input type="email" name="email" value="<?php echo e(old('email')); ?>"
                                       placeholder="exemple@email.com" class="input" required>
                                <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div style="font-size:12px;color:#dc2626;margin-top:4px;"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div>
                                <label class="form-label">Téléphone</label>
                                <input type="text" name="telephone" value="<?php echo e(old('telephone')); ?>"
                                       placeholder="+221 77 000 00 00" class="input">
                            </div>
                        </div>

                        
                        <div style="margin-bottom:16px;">
                            <label class="form-label">Adresse</label>
                            <input type="text" name="adresse" value="<?php echo e(old('adresse')); ?>"
                                   placeholder="Rue, quartier..." class="input">
                        </div>

                        
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:16px;">
                            <div>
                                <label class="form-label">Mot de passe <span style="color:#ef4444;">*</span></label>
                                <input type="password" name="password"
                                       placeholder="Min. 8 caractères" class="input" required>
                                <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div style="font-size:12px;color:#dc2626;margin-top:4px;"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div>
                                <label class="form-label">Confirmer <span style="color:#ef4444;">*</span></label>
                                <input type="password" name="password_confirmation"
                                       placeholder="Répétez" class="input" required>
                            </div>
                        </div>
                    </div>

                    
                    <?php if($role === 'proprietaire'): ?>
                        <div style="margin-bottom:24px;">
                            <div style="font-size:11px;font-weight:700;color:var(--text-3);text-transform:uppercase;letter-spacing:.5px;margin-bottom:16px;padding-bottom:8px;border-bottom:1px solid var(--border);">
                                🏢 Informations propriétaire
                            </div>

                            
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:16px;">
                                <div>
                                    <label class="form-label">Ville</label>
                                    <select name="ville" class="input">
                                        <option value="">— Choisir —</option>
                                        <?php $__currentLoopData = ['Dakar', 'Thiès', 'Saint-Louis', 'Ziguinchor', 'Kaolack', 'Mbour', 'Rufisque', 'Touba']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ville): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($ville); ?>" <?php echo e(old('ville') === $ville ? 'selected' : ''); ?>><?php echo e($ville); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                                <div>
                                    <label class="form-label">NINEA</label>
                                    <input type="text" name="ninea" value="<?php echo e(old('ninea')); ?>"
                                           placeholder="Numéro fiscal" class="input">
                                </div>
                            </div>

                            
                            <div style="margin-bottom:16px;">
                                <label class="form-label">Mode de paiement préféré</label>
                                <select name="mode_paiement_prefere" class="input">
                                    <option value="">— Choisir —</option>
                                    <option value="especes"      <?php echo e(old('mode_paiement_prefere') === 'especes'      ? 'selected' : ''); ?>>Espèces</option>
                                    <option value="virement"     <?php echo e(old('mode_paiement_prefere') === 'virement'     ? 'selected' : ''); ?>>Virement bancaire</option>
                                    <option value="wave"         <?php echo e(old('mode_paiement_prefere') === 'wave'         ? 'selected' : ''); ?>>Wave</option>
                                    <option value="orange_money" <?php echo e(old('mode_paiement_prefere') === 'orange_money' ? 'selected' : ''); ?>>Orange Money</option>
                                </select>
                            </div>

                            
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:16px;">
                                <div>
                                    <label class="form-label">Numéro Wave</label>
                                    <input type="text" name="numero_wave" value="<?php echo e(old('numero_wave')); ?>"
                                           placeholder="+221 77 000 00 00" class="input">
                                </div>
                                <div>
                                    <label class="form-label">Numéro Orange Money</label>
                                    <input type="text" name="numero_om" value="<?php echo e(old('numero_om')); ?>"
                                           placeholder="+221 77 000 00 00" class="input">
                                </div>
                            </div>

                            
                            <div style="margin-bottom:16px;">
                                <label class="form-label">Banque / RIB</label>
                                <input type="text" name="banque" value="<?php echo e(old('banque')); ?>"
                                       placeholder="Ex : CBAO — SN011 01100..." class="input">
                            </div>
                        </div>
                    <?php endif; ?>

                    
                    <?php if($role === 'locataire'): ?>
                        <div style="margin-bottom:24px;">
                            <div style="font-size:11px;font-weight:700;color:var(--text-3);text-transform:uppercase;letter-spacing:.5px;margin-bottom:16px;padding-bottom:8px;border-bottom:1px solid var(--border);">
                                📋 Informations locataire
                            </div>

                            
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:16px;">
                                <div>
                                    <label class="form-label">Profession</label>
                                    <input type="text" name="profession" value="<?php echo e(old('profession')); ?>"
                                           placeholder="Ex : Enseignant" class="input">
                                </div>
                                <div>
                                    <label class="form-label">N° CNI / Passeport</label>
                                    <input type="text" name="numero_cni" value="<?php echo e(old('numero_cni')); ?>"
                                           placeholder="Ex : 1 234 567 890 12" class="input">
                                </div>
                            </div>

                            
                            <div style="margin-bottom:16px;">
                                <label class="form-label">Employeur / Garant</label>
                                <input type="text" name="employeur" value="<?php echo e(old('employeur')); ?>"
                                       placeholder="Nom de l'employeur ou garant" class="input">
                            </div>
                        </div>
                    <?php endif; ?>

                    
                    <div style="display:flex;justify-content:flex-end;gap:10px;padding-top:20px;border-top:1px solid var(--border);">
                        <a href="<?php echo e($role === 'proprietaire' ? route('admin.users.proprietaires') : route('admin.users.locataires')); ?>"
                           class="btn btn-secondary">
                            Annuler
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:15px;height:15px;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Créer le <?php echo e($role === 'proprietaire' ? 'propriétaire' : 'locataire'); ?>

                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

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