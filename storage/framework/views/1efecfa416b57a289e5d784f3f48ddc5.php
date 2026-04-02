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
     <?php $__env->slot('header', null, []); ?> Propriétaires <?php $__env->endSlot(); ?>

    
    <?php if(session('success')): ?>
        <div class="alert alert-success section-gap">✅ <?php echo e(session('success')); ?></div>
    <?php endif; ?>

    
    <div class="flex-between section-gap">
        <div>
            <h1 style="font-size:20px;font-weight:700;color:var(--text);letter-spacing:-.3px;">Propriétaires</h1>
            <p style="font-size:13px;color:var(--text-3);margin-top:3px;"><?php echo e($proprietaires->total()); ?> propriétaire(s) enregistré(s)</p>
        </div>
        <a href="<?php echo e(route('admin.users.create', 'proprietaire')); ?>" class="btn btn-primary">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:15px;height:15px;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nouveau propriétaire
        </a>
    </div>

    
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:10px;" class="section-gap">
        <div class="kpi" style="text-align:center;padding:16px;">
            <div class="kpi-value" style="color:var(--agency);"><?php echo e($stats['total']); ?></div>
            <div class="kpi-sub">Propriétaires</div>
        </div>
        <div class="kpi" style="text-align:center;padding:16px;">
            <div class="kpi-value"><?php echo e($stats['total_biens']); ?></div>
            <div class="kpi-sub">Biens total</div>
        </div>
        <div class="kpi" style="text-align:center;padding:16px;border-color:#bbf7d0;background:#f0fdf4;">
            <div class="kpi-value" style="color:#16a34a;"><?php echo e($stats['biens_loues']); ?></div>
            <div class="kpi-sub" style="color:#22c55e;">Biens loués</div>
        </div>
    </div>

    
    <div class="mobile-cards section-gap">
        <?php $__empty_1 = true; $__currentLoopData = $proprietaires; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="mobile-card" style="cursor:pointer;"
                 onclick="window.location='<?php echo e(route('admin.users.show', $user)); ?>'">
                <div class="flex-between" style="margin-bottom:10px;">
                    <div style="display:flex;align-items:center;gap:10px;">
                        <div class="avatar" style="width:36px;height:36px;font-size:14px;background:var(--agency-soft);color:var(--agency);">
                            <?php echo e(strtoupper(substr($user->name, 0, 1))); ?>

                        </div>
                        <div>
                            <div style="font-weight:700;font-size:13px;color:var(--text);"><?php echo e($user->name); ?></div>
                            <div style="font-size:11px;color:var(--text-3);"><?php echo e($user->created_at->format('d/m/Y')); ?></div>
                        </div>
                    </div>
                    <span class="badge <?php echo e($user->biens_count > 0 ? 'badge-blue' : 'badge-gray'); ?>">
                        <?php echo e($user->biens_count); ?> bien(s)
                    </span>
                </div>
                <div class="mobile-card-row">
                    <span class="mobile-card-label">Email</span>
                    <span class="mobile-card-value"><?php echo e($user->email); ?></span>
                </div>
                <div class="mobile-card-row">
                    <span class="mobile-card-label">Téléphone</span>
                    <span class="mobile-card-value"><?php echo e($user->telephone ?? '—'); ?></span>
                </div>
                <div class="mobile-card-row">
                    <span class="mobile-card-label">Ville</span>
                    <span class="mobile-card-value"><?php echo e($user->proprietaire?->ville ?? '—'); ?></span>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div style="text-align:center;padding:48px;color:var(--text-3);">
                <div style="font-size:40px;margin-bottom:12px;">👤</div>
                <div style="font-size:14px;margin-bottom:12px;">Aucun propriétaire enregistré</div>
                <a href="<?php echo e(route('admin.users.create', 'proprietaire')); ?>" class="btn btn-primary btn-sm">
                    Ajouter le premier propriétaire
                </a>
            </div>
        <?php endif; ?>
    </div>

    
    <div class="desktop-table card section-gap">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Téléphone</th>
                        <th style="text-align:center;">Biens</th>
                        <th>Ville</th>
                        <th style="text-align:center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $proprietaires; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr style="cursor:pointer;"
                            onclick="window.location='<?php echo e(route('admin.users.show', $user)); ?>'">
                            <td>
                                <div style="display:flex;align-items:center;gap:10px;">
                                    <div class="avatar" style="width:32px;height:32px;font-size:12px;background:var(--agency-soft);color:var(--agency);flex-shrink:0;">
                                        <?php echo e(strtoupper(substr($user->name, 0, 1))); ?>

                                    </div>
                                    <div>
                                        <div style="font-weight:600;font-size:13px;color:var(--text);"><?php echo e($user->name); ?></div>
                                        <div style="font-size:11px;color:var(--text-3);">Inscrit le <?php echo e($user->created_at->format('d/m/Y')); ?></div>
                                    </div>
                                </div>
                            </td>
                            <td style="color:var(--text-2);font-size:13px;"><?php echo e($user->email); ?></td>
                            <td style="color:var(--text-2);font-size:13px;"><?php echo e($user->telephone ?? '—'); ?></td>
                            <td style="text-align:center;">
                                <span class="badge <?php echo e($user->biens_count > 0 ? 'badge-blue' : 'badge-gray'); ?>">
                                    <?php echo e($user->biens_count); ?> bien(s)
                                </span>
                            </td>
                            <td style="color:var(--text-2);font-size:13px;"><?php echo e($user->proprietaire?->ville ?? '—'); ?></td>
                            <td style="text-align:center;" onclick="event.stopPropagation()">
                                <div style="display:flex;align-items:center;justify-content:center;gap:6px;">
                                    <a href="<?php echo e(route('admin.users.show', $user)); ?>" class="btn btn-secondary btn-sm">
                                        Voir
                                    </a>
                                    <?php if($user->biens_count === 0): ?>
                                        <form method="POST" action="<?php echo e(route('admin.users.destroy', $user)); ?>"
                                              onsubmit="return confirm('Supprimer ce propriétaire ?')">
                                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="btn btn-danger btn-sm">Supprimer</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" style="text-align:center;padding:48px;color:var(--text-3);">
                                <div style="font-size:36px;margin-bottom:12px;">👤</div>
                                <div style="font-size:14px;margin-bottom:12px;">Aucun propriétaire enregistré</div>
                                <a href="<?php echo e(route('admin.users.create', 'proprietaire')); ?>" class="btn btn-primary btn-sm">
                                    Ajouter le premier propriétaire
                                </a>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($proprietaires->hasPages()): ?>
            <div style="padding:16px 20px;border-top:1px solid var(--border);">
                <?php echo e($proprietaires->links()); ?>

            </div>
        <?php endif; ?>
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
<?php endif; ?><?php /**PATH C:\Users\ph\bimotech-immo\resources\views/users/proprietaires.blade.php ENDPATH**/ ?>