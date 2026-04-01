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
     <?php $__env->slot('header', null, []); ?> Contrat #<?php echo e($contrat->id); ?> <?php $__env->endSlot(); ?>

    
    <?php if(session('success')): ?>
        <div class="alert alert-success section-gap">✅ <?php echo e(session('success')); ?></div>
    <?php endif; ?>

    
    <div class="flex-between section-gap" style="flex-wrap:wrap;gap:12px;">
        <div style="display:flex;align-items:center;gap:12px;">
            <a href="<?php echo e(route('admin.contrats.index')); ?>"
               style="display:flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:var(--radius-sm);border:1px solid var(--border);color:var(--text-2);transition:background .15s;"
               onmouseenter="this.style.background='var(--bg)'"
               onmouseleave="this.style.background='transparent'">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:16px;height:16px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <div style="display:flex;align-items:center;gap:10px;">
                    <h1 style="font-size:20px;font-weight:700;color:var(--text);letter-spacing:-.3px;">
                        Contrat #<?php echo e($contrat->id); ?>

                    </h1>
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
                <p style="font-size:13px;color:var(--text-3);margin-top:2px;">
                    <?php echo e($contrat->bien->reference); ?> — <?php echo e($contrat->locataire->name); ?>

                </p>
            </div>
        </div>
        <?php if($contrat->statut === 'actif'): ?>
            <a href="<?php echo e(route('admin.paiements.create', ['contrat_id' => $contrat->id])); ?>"
               class="btn btn-primary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:15px;height:15px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Enregistrer un paiement
            </a>
        <?php endif; ?>
    </div>

    
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;" class="section-gap">
        <div class="kpi" style="text-align:center;">
            <div class="kpi-value"><?php echo e($nbPaiements); ?></div>
            <div class="kpi-sub">Paiements effectués</div>
        </div>
        <div class="kpi" style="text-align:center;">
            <div class="kpi-value text-money" style="color:#16a34a;font-size:18px;">
                <?php echo e(number_format($totalPaye, 0, ',', ' ')); ?> F
            </div>
            <div class="kpi-sub">Total encaissé</div>
        </div>
        <div class="kpi" style="text-align:center;">
            <div class="kpi-value text-money" style="color:var(--agency);font-size:18px;">
                <?php echo e(number_format($totalNet, 0, ',', ' ')); ?> F
            </div>
            <div class="kpi-sub">Net propriétaire</div>
        </div>
    </div>

    
    <div class="card section-gap">
        <div class="card-header">
            <span class="card-title">Détails du contrat</span>
        </div>
        <div class="card-body">
            <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:20px;" id="contrat-details">

                
                <div style="padding:14px;background:var(--bg);border-radius:var(--radius-sm);">
                    <div style="font-size:10px;font-weight:600;color:var(--text-3);text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">Bien</div>
                    <div style="font-weight:700;font-size:14px;color:var(--text);"><?php echo e($contrat->bien->reference); ?></div>
                    <div style="font-size:12px;color:var(--text-2);margin-top:2px;"><?php echo e($contrat->bien->adresse); ?>, <?php echo e($contrat->bien->ville); ?></div>
                    <div style="font-size:12px;color:var(--text-3);margin-top:1px;"><?php echo e($contrat->bien->type); ?></div>
                </div>

                
                <div style="padding:14px;background:var(--bg);border-radius:var(--radius-sm);">
                    <div style="font-size:10px;font-weight:600;color:var(--text-3);text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">Propriétaire</div>
                    <div style="font-weight:700;font-size:14px;color:var(--text);"><?php echo e($contrat->bien->proprietaire->name); ?></div>
                    <div style="font-size:12px;color:var(--text-2);margin-top:2px;"><?php echo e($contrat->bien->proprietaire->telephone ?? '—'); ?></div>
                    <div style="font-size:12px;color:var(--text-3);margin-top:1px;"><?php echo e($contrat->bien->proprietaire->email); ?></div>
                </div>

                
                <div style="padding:14px;background:var(--bg);border-radius:var(--radius-sm);">
                    <div style="font-size:10px;font-weight:600;color:var(--text-3);text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">Locataire</div>
                    <div style="font-weight:700;font-size:14px;color:var(--text);"><?php echo e($contrat->locataire->name); ?></div>
                    <div style="font-size:12px;color:var(--text-2);margin-top:2px;"><?php echo e($contrat->locataire->telephone ?? '—'); ?></div>
                    <div style="font-size:12px;color:var(--text-3);margin-top:1px;"><?php echo e($contrat->locataire->email); ?></div>
                </div>

                
                <div style="padding:14px;background:#fffbeb;border:1px solid #fde68a;border-radius:var(--radius-sm);">
                    <div style="font-size:10px;font-weight:600;color:#92400e;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">Prochain loyer</div>
                    <div style="font-weight:700;font-size:16px;color:#d97706;"><?php echo e($prochainePeriode->translatedFormat('F Y')); ?></div>
                    <div style="font-size:12px;color:#92400e;margin-top:2px;">
                        <?php echo e(number_format($contrat->loyer_contractuel, 0, ',', ' ')); ?> FCFA attendus
                    </div>
                </div>

            </div>

            
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-top:16px;" id="contrat-finance">
                <div style="text-align:center;padding:14px;border:1px solid var(--border);border-radius:var(--radius-sm);">
                    <div style="font-size:10px;font-weight:600;color:var(--text-3);text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">Loyer contractuel</div>
                    <div style="font-weight:800;font-size:16px;color:var(--text);" class="text-money">
                        <?php echo e(number_format($contrat->loyer_contractuel, 0, ',', ' ')); ?> FCFA
                    </div>
                </div>
                <div style="text-align:center;padding:14px;border:1px solid var(--border);border-radius:var(--radius-sm);">
                    <div style="font-size:10px;font-weight:600;color:var(--text-3);text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">Caution versée</div>
                    <div style="font-weight:800;font-size:16px;color:var(--text);" class="text-money">
                        <?php echo e(number_format($contrat->caution, 0, ',', ' ')); ?> FCFA
                    </div>
                </div>
                <div style="text-align:center;padding:14px;border:1px solid var(--border);border-radius:var(--radius-sm);">
                    <div style="font-size:10px;font-weight:600;color:var(--text-3);text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">Commission agence</div>
                    <div style="font-weight:800;font-size:16px;color:var(--agency);">
                        <?php echo e($contrat->bien->taux_commission); ?>% + TVA 18%
                    </div>
                </div>
            </div>

            
            <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px;margin-top:12px;">
                <div style="display:flex;align-items:center;gap:10px;padding:12px 14px;border:1px solid var(--border);border-radius:var(--radius-sm);">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:16px;height:16px;color:var(--text-3);flex-shrink:0;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <div>
                        <div style="font-size:10px;color:var(--text-3);font-weight:600;text-transform:uppercase;">Début du bail</div>
                        <div style="font-size:13px;font-weight:600;color:var(--text);margin-top:1px;">
                            <?php echo e(\Carbon\Carbon::parse($contrat->date_debut)->format('d/m/Y')); ?>

                        </div>
                    </div>
                </div>
                <div style="display:flex;align-items:center;gap:10px;padding:12px 14px;border:1px solid var(--border);border-radius:var(--radius-sm);">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:16px;height:16px;color:var(--text-3);flex-shrink:0;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <div>
                        <div style="font-size:10px;color:var(--text-3);font-weight:600;text-transform:uppercase;">Fin du bail</div>
                        <div style="font-size:13px;font-weight:600;color:var(--text);margin-top:1px;">
                            <?php echo e($contrat->date_fin ? \Carbon\Carbon::parse($contrat->date_fin)->format('d/m/Y') : 'Durée indéterminée'); ?>

                        </div>
                    </div>
                </div>
            </div>

            
            <?php if($contrat->observations): ?>
                <div style="margin-top:16px;padding:14px;background:var(--bg);border-radius:var(--radius-sm);border-left:3px solid var(--agency);">
                    <div style="font-size:10px;font-weight:600;color:var(--text-3);text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">Observations</div>
                    <div style="font-size:13px;color:var(--text-2);line-height:1.6;"><?php echo e($contrat->observations); ?></div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    
    <div class="card section-gap">
        <div class="card-header">
            <span class="card-title">Historique des paiements</span>
            <?php if($contrat->statut === 'actif'): ?>
                <a href="<?php echo e(route('admin.paiements.create', ['contrat_id' => $contrat->id])); ?>"
                   class="btn btn-primary btn-sm">
                    + Paiement
                </a>
            <?php endif; ?>
        </div>

        
        <div class="mobile-cards" style="padding:12px;">
            <?php $__empty_1 = true; $__currentLoopData = $contrat->paiements->sortByDesc('periode'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="mobile-card">
                    <div class="flex-between" style="margin-bottom:8px;">
                        <span class="text-ref"><?php echo e($p->reference_paiement); ?></span>
                        <span class="badge badge-gray"><?php echo e(ucfirst(str_replace('_', ' ', $p->mode_paiement))); ?></span>
                    </div>
                    <div class="mobile-card-row">
                        <span class="mobile-card-label">Période</span>
                        <span class="mobile-card-value"><?php echo e(\Carbon\Carbon::parse($p->periode)->translatedFormat('F Y')); ?></span>
                    </div>
                    <div style="border-top:1px solid var(--border);margin-top:8px;padding-top:8px;">
                        <div class="mobile-card-row">
                            <span class="mobile-card-label">Montant</span>
                            <span class="text-money" style="font-weight:700;"><?php echo e(number_format($p->montant_encaisse, 0, ',', ' ')); ?> F</span>
                        </div>
                        <div class="mobile-card-row">
                            <span class="mobile-card-label">Commission TTC</span>
                            <span class="text-money" style="color:#d97706;"><?php echo e(number_format($p->commission_ttc, 0, ',', ' ')); ?> F</span>
                        </div>
                        <div class="mobile-card-row">
                            <span class="mobile-card-label">Net proprio</span>
                            <span class="text-money" style="color:#16a34a;font-weight:700;"><?php echo e(number_format($p->net_proprietaire, 0, ',', ' ')); ?> F</span>
                        </div>
                    </div>
                    <div style="margin-top:10px;">
                        <a href="<?php echo e(route('admin.paiements.pdf', $p)); ?>" target="_blank"
                           class="btn btn-secondary btn-sm" style="width:100%;justify-content:center;">
                            📄 Télécharger PDF
                        </a>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div style="text-align:center;padding:32px;color:var(--text-3);font-size:13px;">
                    Aucun paiement enregistré pour ce contrat
                </div>
            <?php endif; ?>
        </div>

        
        <div class="desktop-table table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Référence</th>
                        <th>Période</th>
                        <th>Mode</th>
                        <th style="text-align:right;">Montant</th>
                        <th style="text-align:right;">Commission TTC</th>
                        <th style="text-align:right;">Net proprio</th>
                        <th style="text-align:center;">PDF</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $contrat->paiements->sortByDesc('periode'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><span class="text-ref"><?php echo e($p->reference_paiement); ?></span></td>
                            <td style="color:var(--text-2);"><?php echo e(\Carbon\Carbon::parse($p->periode)->translatedFormat('F Y')); ?></td>
                            <td><span class="badge badge-gray"><?php echo e(ucfirst(str_replace('_', ' ', $p->mode_paiement))); ?></span></td>
                            <td style="text-align:right;" class="text-money"><?php echo e(number_format($p->montant_encaisse, 0, ',', ' ')); ?> F</td>
                            <td style="text-align:right;color:#d97706;" class="text-money"><?php echo e(number_format($p->commission_ttc, 0, ',', ' ')); ?> F</td>
                            <td style="text-align:right;color:#16a34a;font-weight:700;" class="text-money"><?php echo e(number_format($p->net_proprietaire, 0, ',', ' ')); ?> F</td>
                            <td style="text-align:center;">
                                <a href="<?php echo e(route('admin.paiements.pdf', $p)); ?>" target="_blank"
                                   class="btn btn-secondary btn-sm">📄 PDF</a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" style="text-align:center;padding:32px;color:var(--text-3);font-size:13px;">
                                Aucun paiement enregistré pour ce contrat
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    
    <?php if($contrat->statut === 'actif'): ?>
        <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:var(--radius);padding:20px 24px;" class="section-gap">
            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;flex-wrap:wrap;">
                <div>
                    <div style="font-size:14px;font-weight:700;color:#dc2626;margin-bottom:4px;">
                        ⚠️ Résilier ce contrat
                    </div>
                    <div style="font-size:13px;color:#ef4444;">
                        Le bien sera automatiquement remis en statut "disponible". Cette action est irréversible.
                    </div>
                </div>
                <form method="POST" action="<?php echo e(route('admin.contrats.destroy', $contrat)); ?>"
                      onsubmit="return confirm('Résilier définitivement ce contrat ? Cette action est irréversible.')">
                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="btn btn-danger">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:15px;height:15px;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Résilier le contrat
                    </button>
                </form>
            </div>
        </div>
    <?php endif; ?>

    <style>
        @media (min-width: 768px) {
            #contrat-details { grid-template-columns: repeat(3, 1fr); }
            #contrat-finance  { grid-template-columns: repeat(3, 1fr); }
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
<?php endif; ?><?php /**PATH C:\Users\ph\bimotech-immo\resources\views/admin/contrats/show.blade.php ENDPATH**/ ?>