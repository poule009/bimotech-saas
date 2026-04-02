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
     <?php $__env->slot('header', null, []); ?> Journal d'activité <?php $__env->endSlot(); ?>

    <div class="card">
        <div class="card-header">
            <span class="card-title">Journal d'activité</span>
            <span style="font-size:12px;color:var(--text-3);">
                <?php echo e($logs->total()); ?> entrée(s)
            </span>
        </div>

        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;font-size:13px;">
                <thead>
                    <tr style="background:var(--bg);border-bottom:1px solid var(--border);">
                        <th style="padding:10px 16px;text-align:left;font-weight:600;color:var(--text-2);">Date</th>
                        <th style="padding:10px 16px;text-align:left;font-weight:600;color:var(--text-2);">Action</th>
                        <th style="padding:10px 16px;text-align:left;font-weight:600;color:var(--text-2);">Description</th>
                        <th style="padding:10px 16px;text-align:left;font-weight:600;color:var(--text-2);">Modèle</th>
                        <th style="padding:10px 16px;text-align:left;font-weight:600;color:var(--text-2);">Utilisateur</th>
                        <th style="padding:10px 16px;text-align:left;font-weight:600;color:var(--text-2);">Agence</th>
                        <th style="padding:10px 16px;text-align:left;font-weight:600;color:var(--text-2);">IP</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr style="border-bottom:1px solid var(--border);">
                            <td style="padding:10px 16px;color:var(--text-3);white-space:nowrap;">
                                <?php echo e($log->created_at?->format('d/m/Y H:i')); ?>

                            </td>
                            <td style="padding:10px 16px;">
                                <?php
                                    $couleur = match($log->action) {
                                        'created' => '#16a34a',
                                        'updated' => '#d97706',
                                        'deleted' => '#dc2626',
                                        default   => 'var(--text-2)',
                                    };
                                ?>
                                <span style="font-weight:700;color:<?php echo e($couleur); ?>;">
                                    <?php echo e($log->action); ?>

                                </span>
                            </td>
                            <td style="padding:10px 16px;color:var(--text-2);">
                                <?php echo e($log->description); ?>

                            </td>
                            <td style="padding:10px 16px;color:var(--text-3);font-family:monospace;font-size:11px;">
                                <?php echo e(class_basename($log->model_type)); ?> #<?php echo e($log->model_id); ?>

                            </td>
                            <td style="padding:10px 16px;color:var(--text-2);">
                                <?php echo e($log->user->name ?? 'Système'); ?>

                            </td>
                            <td style="padding:10px 16px;color:var(--text-3);">
                                <?php echo e($log->agency->name ?? '—'); ?>

                            </td>
                            <td style="padding:10px 16px;color:var(--text-3);font-size:11px;">
                                <?php echo e($log->ip_address ?? '—'); ?>

                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" style="padding:48px;text-align:center;color:var(--text-3);">
                                Aucune activité enregistrée.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if($logs->hasPages()): ?>
            <div style="padding:16px 20px;border-top:1px solid var(--border);">
                <?php echo e($logs->links()); ?>

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
<?php endif; ?><?php /**PATH C:\Users\ph\bimotech-immo\resources\views/activity-logs/index.blade.php ENDPATH**/ ?>