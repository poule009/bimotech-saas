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
     <?php $__env->slot('header', null, []); ?> Paiement — <?php echo e($paiement->reference_paiement); ?> <?php $__env->endSlot(); ?>

    
    <?php if(session('success')): ?>
        <div class="alert alert-success section-gap">✅ <?php echo e(session('success')); ?></div>
    <?php endif; ?>

    
    <div class="flex-between section-gap" style="flex-wrap:wrap;gap:12px;">
        <div style="display:flex;align-items:center;gap:12px;">
            <a href="<?php echo e(route('admin.paiements.index')); ?>"
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
                        <?php echo e($paiement->reference_paiement); ?>

                    </h1>
                    <?php
                        $sc = match($paiement->statut) {
                            'valide'     => 'badge badge-green',
                            'en_attente' => 'badge badge-amber',
                            'annule'     => 'badge badge-red',
                            default      => 'badge badge-gray',
                        };
                        $sl = match($paiement->statut) {
                            'valide'     => 'Validé',
                            'en_attente' => 'En attente',
                            'annule'     => 'Annulé',
                            default      => ucfirst($paiement->statut),
                        };
                    ?>
                    <span class="<?php echo e($sc); ?>"><?php echo e($sl); ?></span>
                </div>
                <p style="font-size:13px;color:var(--text-3);margin-top:2px;">
                    <?php echo e(\Carbon\Carbon::parse($paiement->periode)->translatedFormat('F Y')); ?>

                    — Réglé le <?php echo e(\Carbon\Carbon::parse($paiement->date_paiement)->format('d/m/Y')); ?>

                </p>
            </div>
        </div>
        <?php if($paiement->statut === 'valide'): ?>
            <a href="<?php echo e(route('admin.paiements.pdf', $paiement)); ?>" target="_blank" class="btn btn-primary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:15px;height:15px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Télécharger quittance
            </a>
        <?php endif; ?>
    </div>

    <div style="max-width:680px;">

        
        <div class="card section-gap">
            <div class="card-header">
                <span class="card-title">Parties concernées</span>
            </div>
            <div class="card-body">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;" id="parties-grid">

                    <div style="padding:14px;background:var(--bg);border-radius:var(--radius-sm);">
                        <div style="font-size:10px;font-weight:600;color:var(--text-3);text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">Propriétaire</div>
                        <div style="font-weight:700;font-size:14px;color:var(--text);"><?php echo e($paiement->contrat->bien->proprietaire->name); ?></div>
                        <div style="font-size:12px;color:var(--text-2);margin-top:2px;"><?php echo e($paiement->contrat->bien->proprietaire->telephone ?? '—'); ?></div>
                    </div>

                    <div style="padding:14px;background:var(--bg);border-radius:var(--radius-sm);">
                        <div style="font-size:10px;font-weight:600;color:var(--text-3);text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">Locataire</div>
                        <div style="font-weight:700;font-size:14px;color:var(--text);"><?php echo e($paiement->contrat->locataire->name); ?></div>
                        <div style="font-size:12px;color:var(--text-2);margin-top:2px;"><?php echo e($paiement->contrat->locataire->telephone ?? '—'); ?></div>
                    </div>

                    <div style="padding:14px;background:var(--bg);border-radius:var(--radius-sm);">
                        <div style="font-size:10px;font-weight:600;color:var(--text-3);text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">Bien</div>
                        <div style="font-weight:700;font-size:14px;color:var(--text);"><?php echo e($paiement->contrat->bien->reference); ?></div>
                        <div style="font-size:12px;color:var(--text-2);margin-top:2px;"><?php echo e($paiement->contrat->bien->adresse); ?>, <?php echo e($paiement->contrat->bien->ville); ?></div>
                    </div>

                    <div style="padding:14px;background:var(--bg);border-radius:var(--radius-sm);">
                        <div style="font-size:10px;font-weight:600;color:var(--text-3);text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">Mode de règlement</div>
                        <div style="font-weight:700;font-size:14px;color:var(--text);"><?php echo e(ucfirst(str_replace('_', ' ', $paiement->mode_paiement))); ?></div>
                        <div style="font-size:12px;color:var(--text-2);margin-top:2px;">
                            <?php echo e(\Carbon\Carbon::parse($paiement->date_paiement)->format('d/m/Y')); ?>

                        </div>
                    </div>

                </div>

                <?php if($paiement->notes): ?>
                    <div style="margin-top:14px;padding:14px;background:var(--bg);border-radius:var(--radius-sm);border-left:3px solid var(--agency);">
                        <div style="font-size:10px;font-weight:600;color:var(--text-3);text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">Notes</div>
                        <div style="font-size:13px;color:var(--text-2);"><?php echo e($paiement->notes); ?></div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        
        <div class="card section-gap">
            <div class="card-header">
                <span class="card-title">Détail des montants</span>
            </div>
            <div class="card-body">

                
                <div style="display:flex;justify-content:space-between;align-items:center;padding:12px 0;border-bottom:1px solid var(--border);">
                    <span style="font-size:13px;color:var(--text-2);">Loyer encaissé</span>
                    <span style="font-weight:700;font-size:15px;color:var(--text);" class="text-money">
                        <?php echo e(number_format($paiement->montant_encaisse, 0, ',', ' ')); ?> FCFA
                    </span>
                </div>

                
                <div style="display:flex;justify-content:space-between;align-items:center;padding:12px 0;border-bottom:1px solid var(--border);">
                    <span style="font-size:13px;color:var(--text-2);">
                        Commission HT (<?php echo e($paiement->taux_commission_applique); ?>%)
                    </span>
                    <span style="color:#d97706;font-weight:500;" class="text-money">
                        − <?php echo e(number_format($paiement->commission_agence, 0, ',', ' ')); ?> FCFA
                    </span>
                </div>

                
                <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid var(--border);">
                    <span style="font-size:12px;color:var(--text-3);">TVA 18% sur commission</span>
                    <span style="font-size:12px;color:var(--text-3);" class="text-money">
                        − <?php echo e(number_format($paiement->tva_commission, 0, ',', ' ')); ?> FCFA
                    </span>
                </div>

                
                <div style="display:flex;justify-content:space-between;align-items:center;padding:12px 0;border-bottom:1px solid var(--border);">
                    <span style="font-size:13px;color:var(--text-2);">Commission TTC</span>
                    <span style="color:#b45309;font-weight:600;" class="text-money">
                        − <?php echo e(number_format($paiement->commission_ttc, 0, ',', ' ')); ?> FCFA
                    </span>
                </div>

                
                <div style="display:flex;justify-content:space-between;align-items:center;padding:16px;background:#f0fdf4;border-radius:var(--radius-sm);margin-top:8px;">
                    <span style="font-weight:700;font-size:14px;color:#15803d;">Net propriétaire</span>
                    <span style="font-weight:800;font-size:20px;color:#16a34a;" class="text-money">
                        <?php echo e(number_format($paiement->net_proprietaire, 0, ',', ' ')); ?> FCFA
                    </span>
                </div>

                
                <?php if($paiement->est_premier_paiement && $paiement->caution_percue > 0): ?>
                    <div style="display:flex;justify-content:space-between;align-items:center;padding:12px 16px;background:#eff6ff;border-radius:var(--radius-sm);margin-top:8px;">
                        <span style="font-size:13px;color:#1d4ed8;">Caution perçue</span>
                        <span style="font-weight:700;color:#2563eb;" class="text-money">
                            <?php echo e(number_format($paiement->caution_percue, 0, ',', ' ')); ?> FCFA
                        </span>
                    </div>
                <?php endif; ?>

            </div>
        </div>

        
        <?php if($paiement->statut === 'valide'): ?>
            <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:var(--radius);padding:20px 24px;">
                <div style="font-size:14px;font-weight:700;color:#dc2626;margin-bottom:8px;">⚠️ Annuler ce paiement</div>
                <form method="POST" action="<?php echo e(route('admin.paiements.annuler', $paiement)); ?>"
                      onsubmit="return confirm('Annuler ce paiement ?')">
                    <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                    <div style="display:flex;gap:10px;align-items:center;">
                        <input type="text" name="motif"
                               placeholder="Motif de l'annulation..."
                               style="flex:1;padding:9px 13px;border:1.5px solid #fecaca;border-radius:var(--radius-sm);font-size:13px;color:var(--text);background:white;outline:none;font-family:inherit;">
                        <button type="submit" class="btn btn-danger">
                            Annuler
                        </button>
                    </div>
                </form>
            </div>
        <?php endif; ?>

    </div>

    <style>
        @media (min-width: 640px) {
            #parties-grid { grid-template-columns: repeat(2, 1fr); }
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
<?php endif; ?><?php /**PATH C:\Users\ph\bimotech-immo\resources\views/paiements/show.blade.php ENDPATH**/ ?>