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
     <?php $__env->slot('header', null, []); ?> Locataires <?php $__env->endSlot(); ?>

    
    <?php if(session('success')): ?>
        <div class="alert alert-success section-gap">✅ <?php echo e(session('success')); ?></div>
    <?php endif; ?>

    
    <div class="flex-between section-gap">
        <div>
            <h1 style="font-size:20px;font-weight:700;color:var(--text);letter-spacing:-.3px;">Locataires</h1>
            <p style="font-size:13px;color:var(--text-3);margin-top:3px;"><?php echo e($locataires->total()); ?> locataire(s) enregistré(s)</p>
        </div>
        <a href="<?php echo e(route('admin.users.create', 'locataire')); ?>" class="btn btn-primary">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:15px;height:15px;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nouveau locataire
        </a>
    </div>

    
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:10px;" class="section-gap">
        <div class="kpi" style="text-align:center;padding:16px;">
            <div class="kpi-value" style="color:#7c3aed;"><?php echo e($stats['total']); ?></div>
            <div class="kpi-sub">Total locataires</div>
        </div>
        <div class="kpi" style="text-align:center;padding:16px;border-color:#bbf7d0;background:#f0fdf4;">
            <div class="kpi-value" style="color:#16a34a;"><?php echo e($stats['actifs']); ?></div>
            <div class="kpi-sub" style="color:#22c55e;">Avec contrat actif</div>
        </div>
        <div class="kpi" style="text-align:center;padding:16px;border-color:#fde68a;background:#fffbeb;">
            <div class="kpi-value" style="color:#d97706;"><?php echo e($stats['sans_contrat']); ?></div>
            <div class="kpi-sub" style="color:#f59e0b;">Sans contrat</div>
        </div>
    </div>

    
    <div class="mobile-cards section-gap">
        <?php $__empty_1 = true; $__currentLoopData = $locataires; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php $contratActif = $user->contrats->firstWhere('statut', 'actif'); ?>
            <div class="mobile-card">
                <div class="flex-between" style="margin-bottom:10px;">
                    <div style="display:flex;align-items:center;gap:10px;">
                        <div class="avatar" style="width:36px;height:36px;font-size:14px;background:#faf5ff;color:#7c3aed;">
                            <?php echo e(strtoupper(substr($user->name, 0, 1))); ?>

                        </div>
                        <div>
                            <div style="font-weight:700;font-size:13px;color:var(--text);"><?php echo e($user->name); ?></div>
                            <div style="font-size:11px;color:var(--text-3);"><?php echo e($user->created_at->format('d/m/Y')); ?></div>
                        </div>
                    </div>
                    <?php if($contratActif): ?>
                        <span class="badge badge-green"><?php echo e($contratActif->bien->reference); ?></span>
                    <?php else: ?>
                        <span class="badge badge-gray">Sans contrat</span>
                    <?php endif; ?>
                </div>
                <div class="mobile-card-row">
                    <span class="mobile-card-label">Email</span>
                    <span class="mobile-card-value"><?php echo e($user->email); ?></span>
                </div>
                <div class="mobile-card-row">
                    <span class="mobile-card-label">Téléphone</span>
                    <span class="mobile-card-value">
                        <?php
                            $telBrutL   = $user->telephone ?? '';
                            $telDigitsL = preg_replace('/[^0-9]/', '', $telBrutL);
                            $waMsgL     = "Bonjour {$user->name}, nous vous contactons concernant votre location. Cordialement, votre agence immobilière.";
                            $waLinkL    = $telDigitsL ? 'https://wa.me/' . $telDigitsL . '?text=' . rawurlencode($waMsgL) : null;
                        ?>
                        <?php if($telBrutL): ?>
                            <a href="tel:<?php echo e($telBrutL); ?>" style="color:var(--agency);font-weight:600;text-decoration:none;">
                                📞 <?php echo e($telBrutL); ?>

                            </a>
                            <?php if($waLinkL): ?>
                                <a href="<?php echo e($waLinkL); ?>" target="_blank"
                                   style="display:inline-flex;align-items:center;gap:4px;margin-top:4px;background:#25d366;color:#fff;padding:3px 8px;border-radius:12px;font-size:11px;font-weight:600;text-decoration:none;">
                                    <svg viewBox="0 0 24 24" fill="currentColor" style="width:11px;height:11px;"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                    WhatsApp
                                </a>
                            <?php endif; ?>
                        <?php else: ?>
                            —
                        <?php endif; ?>
                    </span>
                </div>
                <div style="display:flex;gap:8px;margin-top:12px;">
                    <?php if($contratActif): ?>
                        <a href="<?php echo e(route('admin.contrats.show', $contratActif)); ?>"
                           class="btn btn-secondary btn-sm" style="flex:1;justify-content:center;">
                            Voir contrat
                        </a>
                    <?php else: ?>
                        <a href="<?php echo e(route('admin.contrats.create', ['locataire_id' => $user->id])); ?>"
                           class="btn btn-sm" style="flex:1;justify-content:center;background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0;">
                            Créer contrat
                        </a>
                        <form method="POST" action="<?php echo e(route('admin.users.destroy', $user)); ?>"
                              onsubmit="return confirm('Supprimer ce locataire ?')">
                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="btn btn-danger btn-sm">Supprimer</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div style="text-align:center;padding:48px;color:var(--text-3);">
                <div style="font-size:40px;margin-bottom:12px;">👤</div>
                <div style="font-size:14px;margin-bottom:12px;">Aucun locataire enregistré</div>
                <a href="<?php echo e(route('admin.users.create', 'locataire')); ?>" class="btn btn-primary btn-sm">
                    Ajouter le premier locataire
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
                        <th>Profession</th>
                        <th style="text-align:center;">Contrat</th>
                        <th style="text-align:center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $locataires; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php $contratActif = $user->contrats->firstWhere('statut', 'actif'); ?>
                        <tr>
                            <td>
                                <div style="display:flex;align-items:center;gap:10px;">
                                    <div class="avatar" style="width:32px;height:32px;font-size:12px;background:#faf5ff;color:#7c3aed;flex-shrink:0;">
                                        <?php echo e(strtoupper(substr($user->name, 0, 1))); ?>

                                    </div>
                                    <div>
                                        <div style="font-weight:600;font-size:13px;color:var(--text);"><?php echo e($user->name); ?></div>
                                        <div style="font-size:11px;color:var(--text-3);"><?php echo e($user->created_at->format('d/m/Y')); ?></div>
                                    </div>
                                </div>
                            </td>
                            <td style="color:var(--text-2);font-size:13px;"><?php echo e($user->email); ?></td>
                            <td style="font-size:13px;color:var(--text-2);">
                                <?php
                                    $telBrutDT   = $user->telephone ?? '';
                                    $telDigitsDT = preg_replace('/[^0-9]/', '', $telBrutDT);
                                    $waMsgDT     = "Bonjour {$user->name}, nous vous contactons concernant votre location. Cordialement, votre agence immobilière.";
                                    $waLinkDT    = $telDigitsDT ? 'https://wa.me/' . $telDigitsDT . '?text=' . rawurlencode($waMsgDT) : null;
                                ?>
                                <?php if($telBrutDT): ?>
                                    <a href="tel:<?php echo e($telBrutDT); ?>"
                                       style="color:var(--agency);font-weight:600;text-decoration:none;display:block;white-space:nowrap;">
                                        📞 <?php echo e($telBrutDT); ?>

                                    </a>
                                    <?php if($waLinkDT): ?>
                                        <a href="<?php echo e($waLinkDT); ?>" target="_blank"
                                           style="display:inline-flex;align-items:center;gap:4px;margin-top:4px;background:#25d366;color:#fff;padding:3px 8px;border-radius:12px;font-size:11px;font-weight:600;text-decoration:none;white-space:nowrap;">
                                            <svg viewBox="0 0 24 24" fill="currentColor" style="width:11px;height:11px;"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                            WhatsApp
                                        </a>
                                    <?php endif; ?>
                                <?php else: ?>
                                    —
                                <?php endif; ?>
                            </td>
                            <td style="color:var(--text-2);font-size:13px;"><?php echo e($user->locataire?->profession ?? '—'); ?></td>
                            <td style="text-align:center;">
                                <?php if($contratActif): ?>
                                    <span class="badge badge-green"><?php echo e($contratActif->bien->reference); ?></span>
                                <?php else: ?>
                                    <span class="badge badge-gray">Sans contrat</span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align:center;">
                                <div style="display:flex;align-items:center;justify-content:center;gap:6px;">
                                    <?php if($contratActif): ?>
                                        <a href="<?php echo e(route('admin.contrats.show', $contratActif)); ?>"
                                           class="btn btn-secondary btn-sm">Voir contrat</a>
                                    <?php else: ?>
                                        <a href="<?php echo e(route('admin.contrats.create', ['locataire_id' => $user->id])); ?>"
                                           class="btn btn-sm" style="background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0;">
                                            Créer contrat
                                        </a>
                                        <form method="POST" action="<?php echo e(route('admin.users.destroy', $user)); ?>"
                                              onsubmit="return confirm('Supprimer ce locataire ?')">
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
                                <div style="font-size:14px;margin-bottom:12px;">Aucun locataire enregistré</div>
                                <a href="<?php echo e(route('admin.users.create', 'locataire')); ?>" class="btn btn-primary btn-sm">
                                    Ajouter le premier locataire
                                </a>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($locataires->hasPages()): ?>
            <div style="padding:16px 20px;border-top:1px solid var(--border);">
                <?php echo e($locataires->links()); ?>

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
<?php endif; ?><?php /**PATH C:\Users\ph\bimotech-immo\resources\views/users/locataires.blade.php ENDPATH**/ ?>