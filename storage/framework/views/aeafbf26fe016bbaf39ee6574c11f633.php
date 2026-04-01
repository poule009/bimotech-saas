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
     <?php $__env->slot('header', null, []); ?> <?php echo e($bien->reference); ?> <?php $__env->endSlot(); ?>

    
    <div class="flex-between section-gap" style="flex-wrap:wrap;gap:12px;">
        <div style="display:flex;align-items:center;gap:12px;">
            <a href="<?php echo e(route('biens.index')); ?>"
               style="display:flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:var(--radius-sm);border:1px solid var(--border);color:var(--text-2);transition:background .15s;"
               onmouseenter="this.style.background='var(--bg)'"
               onmouseleave="this.style.background='transparent'">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:16px;height:16px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <div style="display:flex;align-items:center;gap:10px;">
                    <h1 style="font-size:20px;font-weight:700;color:var(--text);letter-spacing:-.3px;"><?php echo e($bien->reference); ?></h1>
                    <?php
                        $sc = match($bien->statut) {
                            'loue'       => 'badge badge-green',
                            'disponible' => 'badge badge-blue',
                            'en_travaux' => 'badge badge-amber',
                            default      => 'badge badge-gray',
                        };
                        $sl = match($bien->statut) {
                            'loue'       => 'Loué',
                            'disponible' => 'Disponible',
                            'en_travaux' => 'En travaux',
                            default      => ucfirst($bien->statut),
                        };
                    ?>
                    <span class="<?php echo e($sc); ?>"><?php echo e($sl); ?></span>
                </div>
                <p style="font-size:13px;color:var(--text-3);margin-top:2px;">
                    <?php echo e($bien->type); ?> — <?php echo e($bien->adresse); ?>, <?php echo e($bien->ville); ?>

                </p>
            </div>
        </div>
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $bien)): ?>
            <a href="<?php echo e(route('biens.edit', $bien)); ?>" class="btn btn-primary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:15px;height:15px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Modifier
            </a>
        <?php endif; ?>
    </div>

    
    <?php if(session('success')): ?>
        <div class="alert alert-success section-gap">✅ <?php echo e(session('success')); ?></div>
    <?php endif; ?>

    
    <div class="card section-gap">
        <div class="card-header">
            <span class="card-title">Informations du bien</span>
            <span style="font-size:13px;color:#16a34a;font-weight:700;" class="text-money">
                <?php echo e(number_format($totalEncaisse, 0, ',', ' ')); ?> FCFA encaissés
            </span>
        </div>
        <div class="card-body">
            <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:16px;" id="bien-infos">

                <div style="padding:14px;background:var(--bg);border-radius:var(--radius-sm);">
                    <div style="font-size:10px;font-weight:600;color:var(--text-3);text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">Type & Surface</div>
                    <div style="font-weight:700;font-size:14px;color:var(--text);"><?php echo e($bien->type); ?></div>
                    <div style="font-size:12px;color:var(--text-2);margin-top:2px;">
                        <?php if($bien->surface_m2): ?> <?php echo e($bien->surface_m2); ?> m² <?php endif; ?>
                        <?php if($bien->surface_m2 && $bien->nombre_pieces): ?> · <?php endif; ?>
                        <?php if($bien->nombre_pieces): ?> <?php echo e($bien->nombre_pieces); ?> pièces <?php endif; ?>
                    </div>
                </div>

                <div style="padding:14px;background:var(--bg);border-radius:var(--radius-sm);">
                    <div style="font-size:10px;font-weight:600;color:var(--text-3);text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">Adresse</div>
                    <div style="font-weight:700;font-size:14px;color:var(--text);"><?php echo e($bien->adresse); ?></div>
                    <div style="font-size:12px;color:var(--text-2);margin-top:2px;"><?php echo e($bien->ville); ?></div>
                </div>

                <div style="padding:14px;background:var(--bg);border-radius:var(--radius-sm);">
                    <div style="font-size:10px;font-weight:600;color:var(--text-3);text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">Loyer mensuel</div>
                    <div style="font-weight:800;font-size:18px;color:var(--text);" class="text-money">
                        <?php echo e(number_format($bien->loyer_mensuel, 0, ',', ' ')); ?> FCFA
                    </div>
                </div>

                <div style="padding:14px;background:var(--bg);border-radius:var(--radius-sm);">
                    <div style="font-size:10px;font-weight:600;color:var(--text-3);text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">Commission agence</div>
                    <div style="font-weight:700;font-size:16px;color:var(--agency);"><?php echo e($bien->taux_commission); ?>%</div>
                    <div style="font-size:12px;color:var(--text-3);margin-top:1px;">
                        TTC : <?php echo e(number_format($bien->loyer_mensuel * ($bien->taux_commission / 100) * 1.18, 0, ',', ' ')); ?> F
                    </div>
                </div>

                <?php if(auth()->user()->isAdmin()): ?>
                    <div style="padding:14px;background:var(--bg);border-radius:var(--radius-sm);">
                        <div style="font-size:10px;font-weight:600;color:var(--text-3);text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">Propriétaire</div>
                        <div style="font-weight:700;font-size:14px;color:var(--text);"><?php echo e($bien->proprietaire->name); ?></div>
                        <div style="font-size:12px;color:var(--text-2);margin-top:2px;"><?php echo e($bien->proprietaire->telephone ?? '—'); ?></div>
                    </div>
                <?php endif; ?>

            </div>

            <?php if($bien->description): ?>
                <div style="margin-top:16px;padding:14px;background:var(--bg);border-radius:var(--radius-sm);border-left:3px solid var(--agency);">
                    <div style="font-size:10px;font-weight:600;color:var(--text-3);text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">Description</div>
                    <div style="font-size:13px;color:var(--text-2);line-height:1.6;"><?php echo e($bien->description); ?></div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    
    <div class="card section-gap">
        <div class="card-header">
            <span class="card-title">
                Photos
                <span style="font-size:12px;font-weight:400;color:var(--text-3);margin-left:6px;">
                    <?php echo e($bien->photos->count()); ?> photo(s)
                </span>
            </span>
        </div>

        <?php if($bien->photos->isNotEmpty()): ?>
            <div style="padding:16px;display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:12px;">
                <?php $__currentLoopData = $bien->photos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $photo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div style="position:relative;border-radius:var(--radius-sm);overflow:hidden;border:2px solid <?php echo e($photo->est_principale ? 'var(--agency)' : 'var(--border)'); ?>;"
                         class="photo-item">
                        <img src="<?php echo e($photo->url); ?>"
                             alt="<?php echo e($bien->reference); ?>"
                             style="width:100%;height:140px;object-fit:cover;display:block;">

                        <?php if($photo->est_principale): ?>
                            <div style="position:absolute;top:6px;left:6px;background:var(--agency);color:white;font-size:10px;font-weight:600;padding:2px 8px;border-radius:999px;">
                                Principale
                            </div>
                        <?php endif; ?>

                        <div class="photo-actions" style="position:absolute;inset:0;background:rgba(0,0,0,0);display:flex;align-items:center;justify-content:center;gap:6px;opacity:0;transition:all .2s;">
                            <?php if(!$photo->est_principale): ?>
                                <form method="POST" action="<?php echo e(route('biens.photos.principale', [$bien, $photo])); ?>">
                                    <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                    <button type="submit" class="btn btn-sm" style="background:white;color:var(--agency);font-size:11px;">
                                        ⭐ Principale
                                    </button>
                                </form>
                            <?php endif; ?>
                            <form method="POST" action="<?php echo e(route('biens.photos.destroy', [$bien, $photo])); ?>"
                                  onsubmit="return confirm('Supprimer cette photo ?')">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="btn btn-sm" style="background:white;color:#dc2626;font-size:11px;">
                                    🗑
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>

        
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $bien)): ?>
            <div style="padding:16px;<?php echo e($bien->photos->isNotEmpty() ? 'border-top:1px solid var(--border);' : ''); ?>">
                <form method="POST" action="<?php echo e(route('biens.photos.store', $bien)); ?>" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>

                    <?php if($errors->has('photos') || $errors->has('photos.*')): ?>
                        <div class="alert alert-error" style="margin-bottom:12px;">
                            <?php $__currentLoopData = $errors->get('photos.*'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $messages): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php $__currentLoopData = $messages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $msg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><div>❌ <?php echo e($msg); ?></div><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php endif; ?>

                    <label for="photos-input"
                           style="display:flex;flex-direction:column;align-items:center;justify-content:center;width:100%;height:120px;border:2px dashed var(--border);border-radius:var(--radius);cursor:pointer;transition:all .15s;text-align:center;"
                           onmouseenter="this.style.borderColor='var(--agency)';this.style.background='var(--agency-soft)'"
                           onmouseleave="this.style.borderColor='var(--border)';this.style.background='transparent'">
                        <div style="font-size:28px;margin-bottom:8px;">📷</div>
                        <div style="font-size:13px;font-weight:500;color:var(--text-2);">Cliquer pour ajouter des photos</div>
                        <div style="font-size:11px;color:var(--text-3);margin-top:4px;">JPG, PNG, WEBP · Max 3 Mo · 10 photos max</div>
                        <input id="photos-input" type="file" name="photos[]" multiple
                               accept="image/jpeg,image/png,image/webp" style="display:none;"
                               onchange="previewPhotos(this)">
                    </label>

                    <div id="preview-container" style="display:none;grid-template-columns:repeat(auto-fill,minmax(100px,1fr));gap:8px;margin-top:12px;"></div>

                    <button type="submit" id="btn-upload"
                            style="display:none;margin-top:12px;width:100%;"
                            class="btn btn-primary">
                        Uploader les photos
                    </button>
                </form>
            </div>
        <?php endif; ?>
    </div>

    
    <?php if($contratActif): ?>
        <div class="card section-gap" style="border-color:#bbf7d0;">
            <div class="card-header" style="background:#f0fdf4;">
                <span class="card-title" style="color:#15803d;">✅ Contrat actif</span>
                <a href="<?php echo e(route('admin.contrats.show', $contratActif)); ?>" class="btn btn-sm" style="background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0;">
                    Voir le contrat →
                </a>
            </div>
            <div class="card-body">
                <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:14px;" id="contrat-actif-grid">
                    <div>
                        <div style="font-size:10px;font-weight:600;color:var(--text-3);text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Locataire</div>
                        <div style="font-weight:700;font-size:14px;color:var(--text);"><?php echo e($contratActif->locataire->name); ?></div>
                        <div style="font-size:12px;color:var(--text-2);"><?php echo e($contratActif->locataire->telephone ?? '—'); ?></div>
                    </div>
                    <div>
                        <div style="font-size:10px;font-weight:600;color:var(--text-3);text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Début du bail</div>
                        <div style="font-weight:700;font-size:14px;color:var(--text);"><?php echo e(\Carbon\Carbon::parse($contratActif->date_debut)->format('d/m/Y')); ?></div>
                    </div>
                    <div>
                        <div style="font-size:10px;font-weight:600;color:var(--text-3);text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Loyer contractuel</div>
                        <div style="font-weight:800;font-size:16px;color:var(--text);" class="text-money"><?php echo e(number_format($contratActif->loyer_contractuel, 0, ',', ' ')); ?> FCFA</div>
                    </div>
                    <div>
                        <div style="font-size:10px;font-weight:600;color:var(--text-3);text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Caution</div>
                        <div style="font-weight:700;font-size:14px;color:var(--text);" class="text-money"><?php echo e(number_format($contratActif->caution, 0, ',', ' ')); ?> FCFA</div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    
    <?php if($bien->contrats->isNotEmpty()): ?>
        <div class="card section-gap">
            <div class="card-header">
                <span class="card-title">Historique des paiements</span>
            </div>

            
            <div class="mobile-cards" style="padding:12px;">
                <?php $__empty_1 = true; $__currentLoopData = $bien->contrats->flatMap->paiements->sortByDesc('periode')->take(10); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="mobile-card">
                        <div class="flex-between" style="margin-bottom:8px;">
                            <span style="font-size:13px;color:var(--text-2);"><?php echo e(\Carbon\Carbon::parse($p->periode)->translatedFormat('F Y')); ?></span>
                            <span class="<?php echo e($p->statut === 'valide' ? 'badge badge-green' : 'badge badge-red'); ?>">
                                <?php echo e(ucfirst($p->statut)); ?>

                            </span>
                        </div>
                        <div class="mobile-card-row">
                            <span class="mobile-card-label">Locataire</span>
                            <span class="mobile-card-value"><?php echo e($p->contrat->locataire->name); ?></span>
                        </div>
                        <div class="mobile-card-row">
                            <span class="mobile-card-label">Montant</span>
                            <span class="text-money" style="font-weight:700;"><?php echo e(number_format($p->montant_encaisse, 0, ',', ' ')); ?> F</span>
                        </div>
                        <div class="mobile-card-row">
                            <span class="mobile-card-label">Net proprio</span>
                            <span class="text-money" style="color:#16a34a;font-weight:700;"><?php echo e(number_format($p->net_proprietaire, 0, ',', ' ')); ?> F</span>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div style="text-align:center;padding:32px;color:var(--text-3);font-size:13px;">Aucun paiement enregistré</div>
                <?php endif; ?>
            </div>

            
            <div class="desktop-table table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Période</th>
                            <th>Locataire</th>
                            <th style="text-align:right;">Montant</th>
                            <th style="text-align:right;">Net proprio</th>
                            <th style="text-align:center;">Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $bien->contrats->flatMap->paiements->sortByDesc('periode')->take(10); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td style="color:var(--text-2);"><?php echo e(\Carbon\Carbon::parse($p->periode)->translatedFormat('F Y')); ?></td>
                                <td style="font-size:13px;"><?php echo e($p->contrat->locataire->name); ?></td>
                                <td style="text-align:right;" class="text-money"><?php echo e(number_format($p->montant_encaisse, 0, ',', ' ')); ?> F</td>
                                <td style="text-align:right;color:#16a34a;font-weight:700;" class="text-money"><?php echo e(number_format($p->net_proprietaire, 0, ',', ' ')); ?> F</td>
                                <td style="text-align:center;">
                                    <span class="<?php echo e($p->statut === 'valide' ? 'badge badge-green' : 'badge badge-red'); ?>">
                                        <?php echo e(ucfirst($p->statut)); ?>

                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="5" style="text-align:center;padding:32px;color:var(--text-3);font-size:13px;">Aucun paiement enregistré</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>

    
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete', $bien)): ?>
        <?php if($bien->statut !== 'loue'): ?>
            <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:var(--radius);padding:20px 24px;">
                <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;flex-wrap:wrap;">
                    <div>
                        <div style="font-size:14px;font-weight:700;color:#dc2626;margin-bottom:4px;">⚠️ Zone dangereuse</div>
                        <div style="font-size:13px;color:#ef4444;">La suppression est irréversible. Impossible de supprimer un bien avec un contrat actif.</div>
                    </div>
                    <form method="POST" action="<?php echo e(route('biens.destroy', $bien)); ?>"
                          onsubmit="return confirm('Supprimer définitivement ce bien ?')">
                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                        <button type="submit" class="btn btn-danger">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:15px;height:15px;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Supprimer ce bien
                        </button>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <style>
        .photo-item:hover .photo-actions {
            background: rgba(0,0,0,.4) !important;
            opacity: 1 !important;
        }
        @media (min-width: 768px) {
            #bien-infos { grid-template-columns: repeat(3, 1fr); }
            #contrat-actif-grid { grid-template-columns: repeat(4, 1fr); }
        }
    </style>

    <script>
        function previewPhotos(input) {
            const container = document.getElementById('preview-container');
            const btn = document.getElementById('btn-upload');
            container.innerHTML = '';
            if (input.files.length === 0) {
                container.style.display = 'none';
                btn.style.display = 'none';
                return;
            }
            container.style.display = 'grid';
            btn.style.display = 'block';
            btn.textContent = `Uploader ${input.files.length} photo(s)`;
            Array.from(input.files).forEach(file => {
                const reader = new FileReader();
                reader.onload = e => {
                    const div = document.createElement('div');
                    div.style.cssText = 'border-radius:8px;overflow:hidden;border:1px solid var(--border);';
                    div.innerHTML = `<img src="${e.target.result}" style="width:100%;height:80px;object-fit:cover;display:block;">`;
                    container.appendChild(div);
                };
                reader.readAsDataURL(file);
            });
        }
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
<?php endif; ?><?php /**PATH C:\Users\ph\bimotech-immo\resources\views/biens/show.blade.php ENDPATH**/ ?>