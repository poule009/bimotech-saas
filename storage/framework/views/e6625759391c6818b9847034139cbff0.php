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

        <div class="auth-card-title">Connexion</div>
        <div class="auth-card-sub">Accédez à votre espace de gestion</div>

        
        <?php if(session('status')): ?>
            <div class="form-status"><?php echo e(session('status')); ?></div>
        <?php endif; ?>

        
        <?php if($errors->has('email')): ?>
            <div style="background:#fef2f2;border:1px solid #fecaca;color:#991b1b;font-size:13px;padding:10px 14px;border-radius:10px;margin-bottom:16px;">
                ⚠️ <?php echo e($errors->first('email')); ?>

            </div>
        <?php endif; ?>

        <form method="POST" action="<?php echo e(route('login')); ?>">
            <?php echo csrf_field(); ?>

            
            <div class="form-group">
                <label class="form-label" for="email">Adresse email</label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="<?php echo e(old('email')); ?>"
                    class="form-input"
                    placeholder="votre@email.com"
                    required
                    autofocus
                    autocomplete="username"
                >
                <?php $__errorArgs = ['email'];
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
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px;">
                    <label class="form-label" for="password" style="margin-bottom:0;">Mot de passe</label>
                    <?php if(Route::has('password.request')): ?>
                        <a href="<?php echo e(route('password.request')); ?>" class="auth-link" style="font-size:12px;">
                            Mot de passe oublié ?
                        </a>
                    <?php endif; ?>
                </div>
                <input
                    id="password"
                    type="password"
                    name="password"
                    class="form-input"
                    placeholder="••••••••"
                    required
                    autocomplete="current-password"
                >
                <?php $__errorArgs = ['password'];
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

            
            <div class="form-check">
                <input id="remember_me" type="checkbox" name="remember">
                <label for="remember_me">Se souvenir de moi</label>
            </div>

            
            <button type="submit" class="btn-auth">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:16px;height:16px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                </svg>
                Se connecter
            </button>

        </form>

    </div>

    
    <div style="text-align:center;margin-top:16px;">
        <span style="font-size:13px;color:#64748b;">Pas encore de compte ?</span>
        <a href="<?php echo e(route('agency.register')); ?>" class="auth-link" style="margin-left:6px;">
            Créer votre agence gratuitement →
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
<?php endif; ?><?php /**PATH C:\Users\ph\bimotech-immo\resources\views/auth/login.blade.php ENDPATH**/ ?>