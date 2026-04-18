<?php if (isset($component)) { $__componentOriginal69dc84650370d1d4dc1b42d016d7226b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal69dc84650370d1d4dc1b42d016d7226b = $attributes; } ?>
<?php $component = App\View\Components\GuestLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('guest-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\GuestLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <div class="auth-card">

        <div class="auth-card-title">Créer votre agence</div>
        <div class="auth-card-sub">Commencez votre essai gratuit de 30 jours — sans engagement</div>

        
        <?php if($errors->any()): ?>
            <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:10px;padding:12px 16px;margin-bottom:20px;">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div style="font-size:13px;color:#dc2626;margin-bottom:2px;">❌ <?php echo e($error); ?></div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?php echo e(route('agency.register.store')); ?>">
            <?php echo csrf_field(); ?>

            
            <div style="margin-bottom:24px;">
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:16px;padding-bottom:10px;border-bottom:1px solid #f1f5f9;">
                    <span style="font-size:16px;">🏢</span>
                    <span style="font-size:13px;font-weight:700;color:#0f172a;letter-spacing:-.2px;">Votre agence</span>
                </div>

                
                <div class="form-group">
                    <label class="form-label">Nom de l'agence <span style="color:#ef4444;">*</span></label>
                    <input type="text" name="agency_name" value="<?php echo e(old('agency_name')); ?>"
                           placeholder="Ex : Immobilier Prestige Dakar"
                           class="form-input" required>
                    <?php $__errorArgs = ['agency_name'];
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

                
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:14px;">
                    <div>
                        <label class="form-label">Email <span style="color:#ef4444;">*</span></label>
                        <input type="email" name="agency_email" value="<?php echo e(old('agency_email')); ?>"
                               placeholder="contact@monagence.sn"
                               class="form-input" required>
                        <?php $__errorArgs = ['agency_email'];
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
                    <div>
                        <label class="form-label">Téléphone</label>
                        <input type="text" name="agency_telephone" value="<?php echo e(old('agency_telephone')); ?>"
                               placeholder="+221 77 000 00 00"
                               class="form-input">
                    </div>
                </div>

                
                <div class="form-group">
                    <label class="form-label">Adresse</label>
                    <input type="text" name="agency_adresse" value="<?php echo e(old('agency_adresse')); ?>"
                           placeholder="Plateau, Dakar"
                           class="form-input">
                </div>
            </div>

            
            <div style="margin-bottom:24px;">
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:16px;padding-bottom:10px;border-bottom:1px solid #f1f5f9;">
                    <span style="font-size:16px;">👤</span>
                    <span style="font-size:13px;font-weight:700;color:#0f172a;letter-spacing:-.2px;">Votre compte administrateur</span>
                </div>

                
                <div class="form-group">
                    <label class="form-label">Nom complet <span style="color:#ef4444;">*</span></label>
                    <input type="text" name="admin_name" value="<?php echo e(old('admin_name')); ?>"
                           placeholder="Prénom et Nom"
                           class="form-input" required>
                    <?php $__errorArgs = ['admin_name'];
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
                    <label class="form-label">Email de connexion <span style="color:#ef4444;">*</span></label>
                    <input type="email" name="admin_email" value="<?php echo e(old('admin_email')); ?>"
                           placeholder="votre@email.com"
                           class="form-input" required>
                    <?php $__errorArgs = ['admin_email'];
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

                
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:14px;">
                    <div>
                        <label class="form-label">Mot de passe <span style="color:#ef4444;">*</span></label>
                        <input type="password" name="admin_password"
                               placeholder="Min. 8 caractères"
                               class="form-input" required>
                        <?php $__errorArgs = ['admin_password'];
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
                    <div>
                        <label class="form-label">Confirmer <span style="color:#ef4444;">*</span></label>
                        <input type="password" name="admin_password_confirmation"
                               placeholder="Répétez"
                               class="form-input" required>
                    </div>
                </div>
            </div>

            
            <div style="margin-bottom:20px;">
                <label style="display:flex;align-items:flex-start;gap:10px;cursor:pointer;">
                    <input type="checkbox" name="cgu" value="1"
                           style="width:16px;height:16px;margin-top:2px;accent-color:#1a3c5e;cursor:pointer;flex-shrink:0;">
                    <span style="font-size:13px;color:#64748b;line-height:1.5;">
                        J'accepte les
                        <a href="#" style="color:#1a3c5e;font-weight:600;">conditions générales d'utilisation</a>
                        de la plateforme Bimotech.
                    </span>
                </label>
                <?php $__errorArgs = ['cgu'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <div class="form-error" style="margin-top:4px;"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            
            <button type="submit" class="btn-auth">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:16px;height:16px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 00-1-1h-2a1 1 0 00-1 1v5m4 0H9"/>
                </svg>
                Créer mon agence gratuitement
            </button>

        </form>

    </div>

    
    <div style="text-align:center;margin-top:16px;">
        <span style="font-size:13px;color:#64748b;">Déjà inscrit ?</span>
        <a href="<?php echo e(route('login')); ?>" class="auth-link" style="margin-left:6px;">
            Se connecter →
        </a>
    </div>

 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal69dc84650370d1d4dc1b42d016d7226b)): ?>
<?php $attributes = $__attributesOriginal69dc84650370d1d4dc1b42d016d7226b; ?>
<?php unset($__attributesOriginal69dc84650370d1d4dc1b42d016d7226b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal69dc84650370d1d4dc1b42d016d7226b)): ?>
<?php $component = $__componentOriginal69dc84650370d1d4dc1b42d016d7226b; ?>
<?php unset($__componentOriginal69dc84650370d1d4dc1b42d016d7226b); ?>
<?php endif; ?><?php /**PATH C:\Users\ph\bimotech-immo\resources\views/auth/register-agency.blade.php ENDPATH**/ ?>