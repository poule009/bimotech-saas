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
     <?php $__env->slot('header', null, []); ?> Détails du Paiement — <?php echo e($paiement->reference_paiement); ?> <?php $__env->endSlot(); ?>

    
    <?php if(session('success')): ?>
        <div style="background: rgba(22, 163, 74, 0.1); border: 1px solid rgba(100, 224, 146, 0.2); color: #66da91; padding: 16px 24px; border-radius: 16px; margin-bottom: 24px; font-weight: 600; display: flex; align-items: center; gap: 12px;">
            <span style="font-size: 20px;">✓</span> <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    
    <div class="flex-between section-gap" style="flex-wrap:wrap; gap:20px; margin-bottom: 35px;">
        <div style="display:flex; align-items:center; gap:20px;">
            <a href="<?php echo e(route('admin.paiements.index')); ?>"
               style="display:flex; align-items:center; justify-content:center; width:44px; height:44px; border-radius:14px; border:1px solid rgba(181, 140, 90, 0.2); background:white; color:var(--agency); transition:all .2s; box-shadow: var(--shadow);"
               onmouseenter="this.style.background='#f7f4ec'; this.style.transform='translateX(-3px)'"
               onmouseleave="this.style.background='white'; this.style.transform='translateX(0)'">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:20px; height:20px; stroke-width:2.5;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <div style="display:flex; align-items:center; gap:12px;">
                    <h1 style="font-size:26px; font-weight:900; color:var(--agency); letter-spacing:-1px;">
                        <?php echo e($paiement->reference_paiement); ?>

                    </h1>
                    <?php
                        $statusStyles = match($paiement->statut) {
                            'valide'    => ['bg' => '#f0fdf4', 'text' => '#16a34a', 'label' => 'Validé'],
                            'en_attente'=> ['bg' => '#fffbeb', 'text' => '#d97706', 'label' => 'En attente'],
                            'annule'    => ['bg' => '#fef2f2', 'text' => '#dc2626', 'label' => 'Annulé'],
                            default     => ['bg' => '#f8fafc', 'text' => '#64748b', 'label' => ucfirst($paiement->statut)],
                        };
                    ?>
                    <span style="background: <?php echo e($statusStyles['bg']); ?>; color: <?php echo e($statusStyles['text']); ?>; padding: 6px 14px; border-radius: 10px; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px;">
                        <?php echo e($statusStyles['label']); ?>

                    </span>
                </div>
                <p style="font-size:13px; color:#b58c5a; font-weight:600; text-transform: uppercase; letter-spacing: 1px; margin-top: 4px;">
                    Échéance de <?php echo e(\Carbon\Carbon::parse($paiement->periode)->translatedFormat('F Y')); ?>

                </p>
            </div>
        </div>

        <?php if($paiement->statut === 'valide'): ?>
            <a href="<?php echo e(route('admin.paiements.pdf', $paiement)); ?>" target="_blank" 
               class="btn btn-primary" 
               style="background: var(--agency); color: white; padding: 14px 28px; border-radius: 16px; font-weight: 800; box-shadow: 0 10px 25px rgba(26, 32, 44, 0.2); border: none; display: flex; align-items: center; gap: 10px; transition: 0.3s;"
               onmouseenter="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 15px 30px rgba(26, 32, 44, 0.3)';"
               onmouseleave="this.style.transform='translateY(0)';">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:20px; height:20px; stroke-width:2.5;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Générer la Quittance PDF
            </a>
        <?php endif; ?>
    </div>

    <div style="max-width:720px;">

        
        <div class="card section-gap" style="background: white; border: none; border-radius: 28px; box-shadow: 0 15px 40px rgba(0,0,0,0.04); overflow: hidden; margin-bottom: 30px;">
            <div style="padding: 24px 32px; border-bottom: 1px solid #f7f4ec;">
                <span style="font-size:16px; font-weight:800; color:var(--agency);">Intervenants & Localisation</span>
            </div>
            <div class="card-body" style="padding: 32px;">
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px;" id="parties-grid">

                    <div style="padding:18px; background:#fcfaf5; border:1px solid rgba(181, 140, 90, 0.1); border-radius:16px;">
                        <div style="font-size:10px; font-weight:800; color:#b58c5a; text-transform:uppercase; letter-spacing:1px; margin-bottom:8px;">Bailleur</div>
                        <div style="font-weight:800; font-size:15px; color:var(--agency);"><?php echo e($paiement->contrat->bien->proprietaire->name); ?></div>
                        <div style="font-size:12px; color:#718096; margin-top:4px; font-weight: 500;">Tel: <?php echo e($paiement->contrat->bien->proprietaire->telephone ?? '—'); ?></div>
                    </div>

                    <div style="padding:18px; background:#fcfaf5; border:1px solid rgba(181, 140, 90, 0.1); border-radius:16px;">
                        <div style="font-size:10px; font-weight:800; color:#b58c5a; text-transform:uppercase; letter-spacing:1px; margin-bottom:8px;">Preneur (Locataire)</div>
                        <div style="font-weight:800; font-size:15px; color:var(--agency);"><?php echo e($paiement->contrat->locataire->name); ?></div>
                        <div style="font-size:12px; color:#718096; margin-top:4px; font-weight: 500;">Tel: <?php echo e($paiement->contrat->locataire->telephone ?? '—'); ?></div>
                    </div>

                    <div style="padding:18px; background:#fcfaf5; border:1px solid rgba(181, 140, 90, 0.1); border-radius:16px;">
                        <div style="font-size:10px; font-weight:800; color:#b58c5a; text-transform:uppercase; letter-spacing:1px; margin-bottom:8px;">Unité Immobilière</div>
                        <div style="font-weight:800; font-size:15px; color:var(--agency);"><?php echo e($paiement->contrat->bien->reference); ?></div>
                        <div style="font-size:12px; color:#718096; margin-top:4px; font-weight: 500;"><?php echo e($paiement->contrat->bien->adresse); ?>, <?php echo e($paiement->contrat->bien->ville); ?></div>
                    </div>

                    <div style="padding:18px; background:#1a202c; border-radius:16px; color: white;">
                        <div style="font-size:10px; font-weight:800; color:#718096; text-transform:uppercase; letter-spacing:1px; margin-bottom:8px;">Information Financière</div>
                        <div style="font-weight:800; font-size:15px; color:#b58c5a;"><?php echo e(ucfirst(str_replace('_', ' ', $paiement->mode_paiement))); ?></div>
                        <div style="font-size:12px; color:#a0aec0; margin-top:4px; font-weight: 500;">
                            Perçu le <?php echo e(\Carbon\Carbon::parse($paiement->date_paiement)->format('d/m/Y')); ?>

                        </div>
                    </div>

                </div>

                <?php if($paiement->notes): ?>
                    <div style="margin-top:20px; padding:18px; background:#f7f4ec; border-radius:16px; border-left:4px solid #b58c5a;">
                        <div style="font-size:10px; font-weight:800; color:#b58c5a; text-transform:uppercase; letter-spacing:1.5px; margin-bottom:6px;">Observations</div>
                        <div style="font-size:14px; color:var(--agency); line-height: 1.5; font-weight: 500;"><?php echo e($paiement->notes); ?></div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        
        <div class="card section-gap" style="background: white; border: none; border-radius: 28px; box-shadow: 0 15px 40px rgba(0,0,0,0.04); overflow: hidden; margin-bottom: 30px;">
            <div style="padding: 24px 32px; border-bottom: 1px solid #f7f4ec; background: #fcfaf5;">
                <span style="font-size:16px; font-weight:800; color:var(--agency);">Décomposition de l'écriture</span>
            </div>
            <div class="card-body" style="padding: 32px;">

                <div style="display:flex; justify-content:space-between; align-items:center; padding:16px 0; border-bottom:1px solid #f7f4ec;">
                    <span style="font-size:14px; color:#718096; font-weight: 600;">Loyer encaissé (Brut)</span>
                    <span style="font-weight:900; font-size:18px; color:var(--agency);" class="text-money">
                        <?php echo e(number_format($paiement->montant_encaisse, 0, ',', ' ')); ?> <small style="font-size:12px; font-weight:600; opacity:0.6;">F</small>
                    </span>
                </div>

                <div style="display:flex; justify-content:space-between; align-items:center; padding:16px 0; border-bottom:1px solid #f7f4ec;">
                    <span style="font-size:14px; color:#718096; font-weight: 600;">
                        Honoraires de Gestion HT (<?php echo e($paiement->taux_commission_applique); ?>%)
                    </span>
                    <span style="color:#d97706; font-weight:700; font-size:15px;" class="text-money">
                        − <?php echo e(number_format($paiement->commission_agence, 0, ',', ' ')); ?> F
                    </span>
                </div>

                <div style="display:flex; justify-content:space-between; align-items:center; padding:12px 0; border-bottom:1px solid #f7f4ec;">
                    <span style="font-size:12px; color:#a0aec0; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">TVA légale 18% sur honoraires</span>
                    <span style="font-size:13px; color:#a0aec0; font-weight:700;" class="text-money">
                        − <?php echo e(number_format($paiement->tva_commission, 0, ',', ' ')); ?> F
                    </span>
                </div>

                <div style="display:flex; justify-content:space-between; align-items:center; padding:16px 0; border-bottom:1px solid #f7f4ec;">
                    <span style="font-size:14px; color:#b58c5a; font-weight: 800;">TOTAL COMMISSION TTC</span>
                    <span style="color:#b45309; font-weight:900; font-size:16px;" class="text-money">
                        − <?php echo e(number_format($paiement->commission_ttc, 0, ',', ' ')); ?> F
                    </span>
                </div>

                
                <div style="display:flex; justify-content:space-between; align-items:center; padding:24px; background: linear-gradient(135deg, #16a34a, #15803d); border-radius:20px; margin-top:20px; color:white; box-shadow: 0 10px 20px rgba(22, 163, 74, 0.2);">
                    <div>
                        <p style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:1.5px; opacity:0.9;">SOLDE NET REVERSÉ</p>
                        <p style="font-size:13px; opacity:0.8; font-weight: 500;">Après déduction des commissions</p>
                    </div>
                    <span style="font-weight:900; font-size:26px; letter-spacing:-1px;" class="text-money">
                        <?php echo e(number_format($paiement->net_proprietaire, 0, ',', ' ')); ?> <small style="font-size:14px;">F</small>
                    </span>
                </div>

                
                <?php if($paiement->est_premier_paiement && $paiement->caution_percue > 0): ?>
                    <div style="display:flex; justify-content:space-between; align-items:center; padding:18px 24px; background:#eff6ff; border:1px solid #bfdbfe; border-radius:18px; margin-top:15px;">
                        <div style="display:flex; align-items:center; gap:10px;">
                            <span style="font-size:20px;">🛡️</span>
                            <span style="font-size:14px; color:#1d4ed8; font-weight: 700;">Dépôt de garantie perçu</span>
                        </div>
                        <span style="font-weight:800; color:#2563eb; font-size:16px;" class="text-money">
                            <?php echo e(number_format($paiement->caution_percue, 0, ',', ' ')); ?> F
                        </span>
                    </div>
                <?php endif; ?>

            </div>
        </div>

        
        <?php if($paiement->statut === 'valide'): ?>
            <div style="background: rgba(239, 68, 68, 0.05); border: 1px solid rgba(239, 68, 68, 0.15); border-radius: 24px; padding: 25px; margin-top: 40px;">
                <div style="font-size:15px; font-weight:800; color:#dc2626; margin-bottom:15px; display:flex; align-items:center; gap:10px;">
                    <svg style="width:20px; height:20px;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                    Procédure d'annulation
                </div>
                <form method="POST" action="<?php echo e(route('admin.paiements.annuler', $paiement)); ?>"
                      onsubmit="return confirm('Attention : l\'annulation est définitive. Confirmer ?')">
                    <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                    <div style="display:flex; gap:12px; align-items:center;">
                        <input type="text" name="motif" required
                               placeholder="Indiquez la raison de l'annulation..."
                               style="flex:1; padding:14px 18px; border:1px solid #fecaca; border-radius:14px; font-size:14px; color:var(--text); background:white; outline:none; transition: 0.3s;"
                               onfocus="this.style.borderColor='#dc2626'; this.style.boxShadow='0 0 0 4px rgba(220, 38, 38, 0.1)';">
                        <button type="submit" class="btn btn-danger" style="background:#dc2626; color:white; padding:14px 24px; border-radius:14px; font-weight:800; border:none; box-shadow: 0 4px 12px rgba(220, 38, 38, 0.2);">
                            Annuler l'écriture
                        </button>
                    </div>
                </form>
            </div>
        <?php endif; ?>

    </div>

    <style>
        /* Responsive adjustments */
        @media (max-width: 640px) {
            #parties-grid { grid-template-columns: 1fr !important; }
            .btn { width: 100%; justify-content: center; }
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