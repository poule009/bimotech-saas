<?php $__env->startSection('title', 'Biens immobiliers'); ?>
<?php $__env->startSection('breadcrumb', 'Biens'); ?>

<?php $__env->startSection('content'); ?>

<div style="padding:0 0 48px">

    
    <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:22px;flex-wrap:wrap;gap:12px">
        <div>
            <h1 style="font-family:'Syne',sans-serif;font-size:22px;font-weight:700;color:#0d1117;letter-spacing:-.4px">
                Biens immobiliers
            </h1>
            <p style="font-size:13px;color:#6b7280;margin-top:3px">
                <?php echo e($biens->total()); ?> bien(s) enregistré(s)
            </p>
        </div>
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create', App\Models\Bien::class)): ?>
            
            <a href="<?php echo e(route('admin.biens.create')); ?>"
               style="display:inline-flex;align-items:center;gap:6px;padding:9px 18px;background:#c9a84c;color:#0d1117;font-family:'DM Sans',sans-serif;font-size:13px;font-weight:600;border-radius:8px;text-decoration:none;transition:opacity .15s"
               onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                <svg style="width:14px;height:14px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Nouveau bien
            </a>
        <?php endif; ?>
    </div>

    
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:14px;margin-bottom:22px">
        <div style="background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:16px 18px;border-top:3px solid #c9a84c">
            <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#9ca3af;margin-bottom:8px">Total biens</div>
            <div style="font-family:'Syne',sans-serif;font-size:26px;font-weight:700;color:#0d1117"><?php echo e($biens->total()); ?></div>
        </div>
        <div style="background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:16px 18px;border-top:3px solid #16a34a">
            <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#9ca3af;margin-bottom:8px">Loués</div>
            <div style="font-family:'Syne',sans-serif;font-size:26px;font-weight:700;color:#16a34a"><?php echo e($biens->where('statut','loue')->count()); ?></div>
        </div>
        <div style="background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:16px 18px;border-top:3px solid #1d4ed8">
            <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#9ca3af;margin-bottom:8px">Disponibles</div>
            <div style="font-family:'Syne',sans-serif;font-size:26px;font-weight:700;color:#1d4ed8"><?php echo e($biens->where('statut','disponible')->count()); ?></div>
        </div>
        <div style="background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:16px 18px;border-top:3px solid #9ca3af">
            <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#9ca3af;margin-bottom:8px">En travaux</div>
            <div style="font-family:'Syne',sans-serif;font-size:26px;font-weight:700;color:#6b7280"><?php echo e($biens->where('statut','en_travaux')->count()); ?></div>
        </div>
    </div>

    
    <form method="GET" style="display:flex;gap:8px;margin-bottom:18px;flex-wrap:wrap">
        <select name="statut" onchange="this.form.submit()"
                style="font-family:'DM Sans',sans-serif;font-size:13px;border:1px solid #d0d7de;border-radius:8px;padding:8px 12px;background:#fff;color:#1c2128;cursor:pointer">
            <option value="">Tous les statuts</option>
            <option value="disponible" <?php if(request('statut')==='disponible'): echo 'selected'; endif; ?>>Disponible</option>
            <option value="loue"       <?php if(request('statut')==='loue'): echo 'selected'; endif; ?>>Loué</option>
            <option value="en_travaux" <?php if(request('statut')==='en_travaux'): echo 'selected'; endif; ?>>En travaux</option>
            <option value="archive"    <?php if(request('statut')==='archive'): echo 'selected'; endif; ?>>Archivé</option>
        </select>
        <select name="type" onchange="this.form.submit()"
                style="font-family:'DM Sans',sans-serif;font-size:13px;border:1px solid #d0d7de;border-radius:8px;padding:8px 12px;background:#fff;color:#1c2128;cursor:pointer">
            <option value="">Tous les types</option>
            <option value="appartement" <?php if(request('type')==='appartement'): echo 'selected'; endif; ?>>Appartement</option>
            <option value="villa"       <?php if(request('type')==='villa'): echo 'selected'; endif; ?>>Villa</option>
            <option value="bureau"      <?php if(request('type')==='bureau'): echo 'selected'; endif; ?>>Bureau</option>
            <option value="commerce"    <?php if(request('type')==='commerce'): echo 'selected'; endif; ?>>Commerce</option>
            <option value="terrain"     <?php if(request('type')==='terrain'): echo 'selected'; endif; ?>>Terrain</option>
        </select>
        <?php if(request()->hasAny(['statut','type'])): ?>
            <a href="<?php echo e(route('admin.biens.index')); ?>"
               style="display:inline-flex;align-items:center;padding:8px 14px;border:1px solid #d0d7de;border-radius:8px;font-size:13px;color:#6b7280;text-decoration:none;background:#fff">
                Effacer
            </a>
        <?php endif; ?>
    </form>

    
    <?php if($biens->isEmpty()): ?>
        <?php if (isset($component)) { $__componentOriginal074a021b9d42f490272b5eefda63257c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal074a021b9d42f490272b5eefda63257c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty-state','data' => ['title' => 'Aucun bien enregistré','description' => 'Commencez par ajouter votre premier bien immobilier.','actionLabel' => 'Ajouter un bien','actionUrl' => route('admin.biens.create')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Aucun bien enregistré','description' => 'Commencez par ajouter votre premier bien immobilier.','action-label' => 'Ajouter un bien','action-url' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('admin.biens.create'))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal074a021b9d42f490272b5eefda63257c)): ?>
<?php $attributes = $__attributesOriginal074a021b9d42f490272b5eefda63257c; ?>
<?php unset($__attributesOriginal074a021b9d42f490272b5eefda63257c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal074a021b9d42f490272b5eefda63257c)): ?>
<?php $component = $__componentOriginal074a021b9d42f490272b5eefda63257c; ?>
<?php unset($__componentOriginal074a021b9d42f490272b5eefda63257c); ?>
<?php endif; ?>
    <?php else: ?>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:16px">
            <?php $__currentLoopData = $biens; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bien): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div style="background:#fff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden;transition:box-shadow .2s"
                 onmouseover="this.style.boxShadow='0 4px 20px -4px rgba(0,0,0,.1)'"
                 onmouseout="this.style.boxShadow='none'">

                
                <div style="height:160px;background:#f9fafb;display:flex;align-items:center;justify-content:center;overflow:hidden">
                    <?php $photo = $bien->photos?->firstWhere('est_principale', true) ?? $bien->photos?->first(); ?>
                    <?php if($photo): ?>
                        <img src="<?php echo e(asset('storage/'.$photo->chemin)); ?>" alt="<?php echo e($bien->titre); ?>"
                             style="width:100%;height:100%;object-fit:cover">
                    <?php else: ?>
                        <svg style="width:40px;height:40px;color:#d1d5db" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <rect x="3" y="3" width="18" height="18" rx="2"/>
                            <circle cx="8.5" cy="8.5" r="1.5"/>
                            <polyline points="21 15 16 10 5 21"/>
                        </svg>
                    <?php endif; ?>
                </div>

                
                <div style="padding:16px">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px">
                        <span style="font-family:'Syne',sans-serif;font-size:11px;font-weight:600;color:#9ca3af">
                            <?php echo e($bien->reference); ?>

                        </span>
                        <?php
                            $badgeStyle = match($bien->statut) {
                                'loue'       => 'background:#dbeafe;color:#1d4ed8',
                                'disponible' => 'background:#dcfce7;color:#16a34a',
                                'en_travaux' => 'background:#fef9c3;color:#a16207',
                                default      => 'background:#f3f4f6;color:#6b7280',
                            };
                        ?>
                        <span style="font-size:11px;font-weight:600;padding:3px 10px;border-radius:99px;<?php echo e($badgeStyle); ?>">
                            <?php echo e($bien->statut_label); ?>

                        </span>
                    </div>

                    <h3 style="font-family:'Syne',sans-serif;font-size:14px;font-weight:700;color:#0d1117;margin-bottom:4px">
                        <?php echo e($bien->type_label); ?> — <?php echo e($bien->titre ?? $bien->adresse); ?>

                    </h3>
                    <p style="font-size:12px;color:#6b7280;margin-bottom:12px">
                        <?php echo e($bien->quartier); ?>, <?php echo e($bien->ville); ?>

                    </p>

                    <div style="display:flex;justify-content:space-between;align-items:center;padding-top:12px;border-top:1px solid #f3f4f6">
                        <div>
                            <div style="font-family:'Syne',sans-serif;font-size:16px;font-weight:700;color:#c9a84c">
                                <?php echo e(number_format($bien->loyer_hors_charges, 0, ',', ' ')); ?> <span style="font-size:11px;color:#9ca3af">F/mois</span>
                            </div>
                            <?php if($bien->contratActif): ?>
                                <div style="font-size:11px;color:#6b7280;margin-top:2px">
                                    <?php echo e($bien->contratActif->locataire?->name ?? '—'); ?>

                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <a href="<?php echo e(route('admin.biens.show', $bien)); ?>"
                           style="display:inline-flex;align-items:center;gap:4px;padding:7px 14px;border:1px solid #e5e7eb;border-radius:8px;font-size:12px;font-weight:500;color:#374151;text-decoration:none;transition:all .15s"
                           onmouseover="this.style.borderColor='#c9a84c';this.style.color='#8a6e2f'"
                           onmouseout="this.style.borderColor='#e5e7eb';this.style.color='#374151'">
                            Voir
                            <svg style="width:12px;height:12px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        
        <?php if($biens->hasPages()): ?>
        <div style="display:flex;justify-content:center;gap:6px;margin-top:24px">
            <?php if(!$biens->onFirstPage()): ?>
                <a href="<?php echo e($biens->previousPageUrl()); ?>" style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border:1px solid #e5e7eb;border-radius:8px;color:#6b7280;text-decoration:none">
                    <svg style="width:14px;height:14px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                </a>
            <?php endif; ?>
            <?php $__currentLoopData = $biens->getUrlRange(max(1,$biens->currentPage()-2), min($biens->lastPage(),$biens->currentPage()+2)); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e($url); ?>" style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border:1px solid <?php echo e($page===$biens->currentPage() ? '#0d1117' : '#e5e7eb'); ?>;border-radius:8px;font-size:13px;font-weight:500;color:<?php echo e($page===$biens->currentPage() ? '#fff' : '#374151'); ?>;background:<?php echo e($page===$biens->currentPage() ? '#0d1117' : '#fff'); ?>;text-decoration:none">
                    <?php echo e($page); ?>

                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php if($biens->hasMorePages()): ?>
                <a href="<?php echo e($biens->nextPageUrl()); ?>" style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border:1px solid #e5e7eb;border-radius:8px;color:#6b7280;text-decoration:none">
                    <svg style="width:14px;height:14px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    <?php endif; ?>

</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ph\bimotech-immo\resources\views/biens/index.blade.php ENDPATH**/ ?>