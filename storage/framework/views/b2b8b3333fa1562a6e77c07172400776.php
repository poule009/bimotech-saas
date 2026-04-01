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
     <?php $__env->slot('header', null, []); ?> Contrats de bail <?php $__env->endSlot(); ?>

    
    <?php if(session('success')): ?>
        <div class="alert alert-success section-gap">✅ <?php echo e(session('success')); ?></div>
    <?php endif; ?>

    
    <div class="flex-between section-gap">
        <div>
            <h1 style="font-size:20px;font-weight:700;color:var(--text);letter-spacing:-.3px;">Contrats de bail</h1>
            <p style="font-size:13px;color:var(--text-3);margin-top:3px;">
                <?php echo e($contrats->total()); ?> contrat(s) enregistré(s)
            </p>
        </div>
        <a href="<?php echo e(route('admin.contrats.create')); ?>" class="btn btn-primary">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:15px;height:15px;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nouveau contrat
        </a>
    </div>

    
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:10px;" class="section-gap">
        <div class="kpi" style="padding:16px;text-align:center;">
            <div style="font-size:28px;font-weight:800;color:var(--text);"><?php echo e($stats['total']); ?></div>
            <div style="font-size:11px;color:var(--text-3);margin-top:3px;font-weight:500;">Total</div>
        </div>
        <div class="kpi" style="padding:16px;text-align:center;border-color:#bbf7d0;background:#f0fdf4;">
            <div style="font-size:28px;font-weight:800;color:#16a34a;"><?php echo e($stats['actifs']); ?></div>
            <div style="font-size:11px;color:#22c55e;margin-top:3px;font-weight:500;">Actifs</div>
        </div>
        <div class="kpi" style="padding:16px;text-align:center;border-color:#fecaca;background:#fef2f2;">
            <div style="font-size:28px;font-weight:800;color:#dc2626;"><?php echo e($stats['resilies']); ?></div>
            <div style="font-size:11px;color:#ef4444;margin-top:3px;font-weight:500;">Résiliés</div>
        </div>
        <div class="kpi" style="padding:16px;text-align:center;border-color:#e2e8f0;background:#f8fafc;">
            <div style="font-size:28px;font-weight:800;color:var(--text-2);"><?php echo e($stats['expires']); ?></div>
            <div style="font-size:11px;color:var(--text-3);margin-top:3px;font-weight:500;">Expirés</div>
        </div>
    </div>

    
    <div class="mobile-cards section-gap">
        <?php $__empty_1 = true; $__currentLoopData = $contrats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contrat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="mobile-card">
                <div class="flex-between" style="margin-bottom:10px;">
                    <div>
                        <div style="font-weight:700;font-size:14px;color:var(--text);"><?php echo e($contrat->bien->reference); ?></div>
                        <div style="font-size:11px;color:var(--text-3);"><?php echo e($contrat->bien->ville); ?></div>
                    </div>
                    <?php
                        $sc = match($contrat->statut) {
                            'actif'   => 'badge badge-green',
                            'resilié' => 'badge badge-red',
                            'expiré'  => 'badge badge-gray',
                            default   => 'badge badge-gray',
                        };
                        $sl = match($contrat->statut) {
                            'actif'   => 'Actif',
                            'resilié' => 'Résilié',
                            'expiré'  => 'Expiré',
                            default   => ucfirst($contrat->statut),
                        };
                    ?>
                    <span class="<?php echo e($sc); ?>"><?php echo e($sl); ?></span>
                </div>
                <div class="mobile-card-row">
                    <span class="mobile-card-label">Locataire</span>
                    <span class="mobile-card-value"><?php echo e($contrat->locataire->name); ?></span>
                </div>
                <div class="mobile-card-row">
                    <span class="mobile-card-label">Début</span>
                    <span class="mobile-card-value"><?php echo e(\Carbon\Carbon::parse($contrat->date_debut)->format('d/m/Y')); ?></span>
                </div>
                <div class="mobile-card-row">
                    <span class="mobile-card-label">Fin</span>
                    <span class="mobile-card-value"><?php echo e($contrat->date_fin ? \Carbon\Carbon::parse($contrat->date_fin)->format('d/m/Y') : 'Indéterminé'); ?></span>
                </div>
                <div style="border-top:1px solid var(--border);margin-top:10px;padding-top:10px;">
                    <div class="mobile-card-row">
                        <span class="mobile-card-label">Loyer</span>
                        <span class="text-money" style="font-weight:700;font-size:14px;"><?php echo e(number_format($contrat->loyer_contractuel, 0, ',', ' ')); ?> F</span>
                    </div>
                    <div class="mobile-card-row">
                        <span class="mobile-card-label">Caution</span>
                        <span class="text-money" style="color:var(--text-2);"><?php echo e(number_format($contrat->caution, 0, ',', ' ')); ?> F</span>
                    </div>
                </div>
                <div style="display:flex;gap:8px;margin-top:12px;">
                    <a href="<?php echo e(route('admin.contrats.show', $contrat)); ?>" class="btn btn-secondary btn-sm" style="flex:1;justify-content:center;">
                        Voir le détail
                    </a>
                    <?php if($contrat->statut === 'actif'): ?>
                        <form method="POST" action="<?php echo e(route('admin.contrats.destroy', $contrat)); ?>"
                              onsubmit="return confirm('Résilier ce contrat ?')">
                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="btn btn-danger btn-sm">Résilier</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div style="text-align:center;padding:48px 20px;color:var(--text-3);">
                <div style="font-size:40px;margin-bottom:12px;">📄</div>
                <div style="font-size:14px;margin-bottom:12px;">Aucun contrat enregistré</div>
                <a href="<?php echo e(route('admin.contrats.create')); ?>" class="btn btn-primary btn-sm">
                    Créer le premier contrat
                </a>
            </div>
        <?php endif; ?>
    </div>

    
    <div class="desktop-table card section-gap">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Bien</th>
                        <th>Locataire</th>
                        <th>Début</th>
                        <th>Fin</th>
                        <th style="text-align:right;">Loyer</th>
                        <th style="text-align:right;">Caution</th>
                        <th style="text-align:center;">Statut</th>
                        <th style="text-align:center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $contrats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contrat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $sc = match($contrat->statut) {
                                'actif'   => 'badge badge-green',
                                'resilié' => 'badge badge-red',
                                'expiré'  => 'badge badge-gray',
                                default   => 'badge badge-gray',
                            };
                            $sl = match($contrat->statut) {
                                'actif'   => 'Actif',
                                'resilié' => 'Résilié',
                                'expiré'  => 'Expiré',
                                default   => ucfirst($contrat->statut),
                            };
                        ?>
                        <tr>
                            <td>
                                <div style="font-weight:600;font-size:13px;color:var(--text);"><?php echo e($contrat->bien->reference); ?></div>
                                <div style="font-size:11px;color:var(--text-3);"><?php echo e($contrat->bien->ville); ?></div>
                            </td>
                            <td>
                                <div style="font-size:13px;font-weight:500;color:var(--text);"><?php echo e($contrat->locataire->name); ?></div>
                                <div style="font-size:11px;color:var(--text-3);"><?php echo e($contrat->locataire->email); ?></div>
                            </td>
                            <td style="color:var(--text-2);font-size:13px;">
                                <?php echo e(\Carbon\Carbon::parse($contrat->date_debut)->format('d/m/Y')); ?>

                            </td>
                            <td style="color:var(--text-2);font-size:13px;">
                                <?php echo e($contrat->date_fin ? \Carbon\Carbon::parse($contrat->date_fin)->format('d/m/Y') : '—'); ?>

                            </td>
                            <td style="text-align:right;" class="text-money">
                                <?php echo e(number_format($contrat->loyer_contractuel, 0, ',', ' ')); ?> F
                            </td>
                            <td style="text-align:right;color:var(--text-2);" class="text-money">
                                <?php echo e(number_format($contrat->caution, 0, ',', ' ')); ?> F
                            </td>
                            <td style="text-align:center;">
                                <span class="<?php echo e($sc); ?>"><?php echo e($sl); ?></span>
                            </td>
                            <td style="text-align:center;">
                                <div style="display:flex;align-items:center;justify-content:center;gap:6px;">
                                    <a href="<?php echo e(route('admin.contrats.show', $contrat)); ?>"
                                       class="btn btn-secondary btn-sm">
                                        Voir
                                    </a>
                                    <?php if($contrat->statut === 'actif'): ?>
                                        <form method="POST"
                                              action="<?php echo e(route('admin.contrats.destroy', $contrat)); ?>"
                                              onsubmit="return confirm('Résilier ce contrat définitivement ?')">
                                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                Résilier
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="8" style="text-align:center;padding:48px;color:var(--text-3);">
                                <div style="font-size:36px;margin-bottom:12px;">📄</div>
                                <div style="font-size:14px;margin-bottom:12px;">Aucun contrat enregistré</div>
                                <a href="<?php echo e(route('admin.contrats.create')); ?>" class="btn btn-primary btn-sm">
                                    Créer le premier contrat
                                </a>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if($contrats->hasPages()): ?>
            <div style="padding:16px 20px;border-top:1px solid var(--border);">
                <?php echo e($contrats->links()); ?>

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
<?php endif; ?><?php /**PATH C:\Users\ph\bimotech-immo\resources\views/admin/contrats/index.blade.php ENDPATH**/ ?>