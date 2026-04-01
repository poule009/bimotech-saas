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
        <?php if(auth()->user()->isAdmin()): ?> Tous les biens
        <?php else: ?> Mes biens
        <?php endif; ?>
     <?php $__env->endSlot(); ?>

    
    <?php if(session('success')): ?>
        <div class="alert alert-success section-gap">✅ <?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if($errors->has('general')): ?>
        <div class="alert alert-error section-gap">❌ <?php echo e($errors->first('general')); ?></div>
    <?php endif; ?>

    
    <div class="flex-between section-gap">
        <div>
            <h1 style="font-size:20px;font-weight:700;color:var(--text);letter-spacing:-.3px;">
                <?php if(auth()->user()->isAdmin()): ?> Tous les biens
                <?php else: ?> Mes biens
                <?php endif; ?>
            </h1>
            <p style="font-size:13px;color:var(--text-3);margin-top:3px;">
                <?php echo e($biens->total()); ?> bien(s) enregistré(s)
            </p>
        </div>
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create', App\Models\Bien::class)): ?>
            <a href="<?php echo e(route('biens.create')); ?>" class="btn btn-primary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:15px;height:15px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nouveau bien
            </a>
        <?php endif; ?>
    </div>

    
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:16px;">

        <?php $__empty_1 = true; $__currentLoopData = $biens; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bien): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;box-shadow:var(--shadow);transition:box-shadow .2s,transform .2s;"
                 onmouseenter="this.style.boxShadow='var(--shadow-lg)';this.style.transform='translateY(-2px)'"
                 onmouseleave="this.style.boxShadow='var(--shadow)';this.style.transform='translateY(0)'">

                
                <?php if($bien->photoPrincipale): ?>
                    <div style="width:100%;height:180px;overflow:hidden;">
                        <img src="<?php echo e($bien->photoPrincipale->url); ?>"
                             alt="<?php echo e($bien->reference); ?>"
                             style="width:100%;height:100%;object-fit:cover;transition:transform .3s;"
                             onmouseenter="this.style.transform='scale(1.05)'"
                             onmouseleave="this.style.transform='scale(1)'">
                    </div>
                <?php else: ?>
                    <div style="width:100%;height:160px;background:linear-gradient(135deg,#f0f4ff,#e8f0fe);display:flex;align-items:center;justify-content:center;">
                        <span style="font-size:48px;opacity:.5;">🏠</span>
                    </div>
                <?php endif; ?>

                
                <div style="padding:16px 18px;">

                    
                    <div class="flex-between" style="margin-bottom:10px;">
                        <div>
                            <div style="font-weight:700;font-size:15px;color:var(--text);"><?php echo e($bien->reference); ?></div>
                            <div style="font-size:12px;color:var(--text-3);margin-top:1px;"><?php echo e($bien->type); ?></div>
                        </div>
                        <?php
                            $badgeClass = match($bien->statut) {
                                'loue'       => 'badge badge-green',
                                'disponible' => 'badge badge-blue',
                                'en_travaux' => 'badge badge-amber',
                                default      => 'badge badge-gray',
                            };
                            $badgeLabel = match($bien->statut) {
                                'loue'       => 'Loué',
                                'disponible' => 'Disponible',
                                'en_travaux' => 'En travaux',
                                default      => ucfirst($bien->statut),
                            };
                        ?>
                        <span class="<?php echo e($badgeClass); ?>"><?php echo e($badgeLabel); ?></span>
                    </div>

                    
                    <div style="font-size:12.5px;color:var(--text-2);margin-bottom:6px;display:flex;align-items:center;gap:5px;">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:13px;height:13px;flex-shrink:0;color:var(--text-3);">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <?php echo e($bien->adresse); ?>, <?php echo e($bien->ville); ?>

                    </div>

                    
                    <?php if($bien->surface_m2 || $bien->nombre_pieces): ?>
                        <div style="font-size:12px;color:var(--text-3);margin-bottom:6px;display:flex;gap:12px;">
                            <?php if($bien->surface_m2): ?>
                                <span style="display:flex;align-items:center;gap:4px;">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:12px;height:12px;">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                                    </svg>
                                    <?php echo e($bien->surface_m2); ?> m²
                                </span>
                            <?php endif; ?>
                            <?php if($bien->nombre_pieces): ?>
                                <span style="display:flex;align-items:center;gap:4px;">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:12px;height:12px;">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                    </svg>
                                    <?php echo e($bien->nombre_pieces); ?> pièces
                                </span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    
                    <?php if(auth()->user()->isAdmin()): ?>
                        <div style="font-size:12px;color:var(--text-3);margin-bottom:12px;display:flex;align-items:center;gap:5px;">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:12px;height:12px;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <?php echo e($bien->proprietaire->name); ?>

                        </div>
                    <?php endif; ?>

                    
                    <div style="display:flex;align-items:center;justify-content:space-between;padding-top:12px;border-top:1px solid var(--border);">
                        <div>
                            <div style="font-weight:800;font-size:16px;color:var(--text);" class="text-money">
                                <?php echo e(number_format($bien->loyer_mensuel, 0, ',', ' ')); ?>

                                <span style="font-size:11px;font-weight:500;color:var(--text-3);">FCFA/mois</span>
                            </div>
                            <div style="font-size:11px;color:var(--text-3);margin-top:1px;">
                                Commission : <?php echo e($bien->taux_commission); ?>%
                            </div>
                        </div>
                        <div style="display:flex;gap:6px;">
                            <a href="<?php echo e(route('biens.show', $bien)); ?>"
                               class="btn btn-primary btn-sm">
                                Voir
                            </a>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $bien)): ?>
                                <a href="<?php echo e(route('biens.edit', $bien)); ?>"
                                   class="btn btn-secondary btn-sm">
                                    Modifier
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>

                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            
            <div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:64px 32px;text-align:center;box-shadow:var(--shadow);">
                <div style="font-size:56px;margin-bottom:16px;opacity:.4;">🏠</div>
                <div style="font-size:16px;font-weight:600;color:var(--text);margin-bottom:6px;">Aucun bien enregistré</div>
                <div style="font-size:13px;color:var(--text-3);margin-bottom:20px;">Commencez par ajouter votre premier bien immobilier.</div>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create', App\Models\Bien::class)): ?>
                    <a href="<?php echo e(route('biens.create')); ?>" class="btn btn-primary">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:15px;height:15px;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Ajouter le premier bien
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>

    </div>

    
    <?php if($biens->hasPages()): ?>
        <div style="margin-top:24px;">
            <?php echo e($biens->links()); ?>

        </div>
    <?php endif; ?>

    
    <style>
        @media (min-width: 640px) {
            #biens-grid { grid-template-columns: repeat(2, 1fr); }
        }
        @media (min-width: 1024px) {
            #biens-grid { grid-template-columns: repeat(3, 1fr); }
        }
    </style>

 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?><?php /**PATH C:\Users\ph\bimotech-immo\resources\views/biens/index.blade.php ENDPATH**/ ?>