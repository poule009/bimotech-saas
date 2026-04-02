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
     <?php $__env->slot('header', null, []); ?> Contrat — <?php echo e($contrat->reference_bail_affichee); ?> <?php $__env->endSlot(); ?>

    
    <div style="display:flex;align-items:center;gap:12px;" class="section-gap">
        <a href="<?php echo e(route('admin.contrats.index')); ?>"
           style="display:flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:var(--radius-sm);border:1px solid var(--border);color:var(--text-2);transition:background .15s;"
           onmouseenter="this.style.background='var(--bg)'"
           onmouseleave="this.style.background='transparent'">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:16px;height:16px;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div style="flex:1;min-width:0;">
            <h1 style="font-size:20px;font-weight:700;color:var(--text);letter-spacing:-.3px;">
                <?php echo e($contrat->reference_bail_affichee); ?>

            </h1>
            <p style="font-size:13px;color:var(--text-3);margin-top:2px;">
                <?php echo e($contrat->bien->reference); ?> · <?php echo e($contrat->locataire->name); ?> ·
                <span style="<?php echo e($contrat->statut === 'actif' ? 'color:#16a34a;font-weight:600;' : 'color:#dc2626;'); ?>">
                    <?php echo e(ucfirst($contrat->statut)); ?>

                </span>
            </p>
        </div>
        <div style="display:flex;gap:8px;flex-shrink:0;">
            <?php if($contrat->statut === 'actif'): ?>
                <a href="<?php echo e(route('admin.paiements.create', ['contrat_id' => $contrat->id])); ?>"
                   class="btn btn-primary btn-sm">
                    + Paiement
                </a>
                <a href="<?php echo e(route('admin.contrats.edit', $contrat)); ?>"
                   class="btn btn-secondary btn-sm">
                    Modifier
                </a>
            <?php endif; ?>
        </div>
    </div>

    <?php if(session('success')): ?>
        <div class="alert alert-success section-gap">✅ <?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;" id="contrat-grid">

        
        <div>

            
            <div class="card section-gap" style="border-top:3px solid var(--agency);">
                <div class="card-header">
                    <span class="card-title">📋 Référence officielle</span>
                </div>
                <div class="card-body">
                    <div style="font-size:22px;font-weight:900;color:var(--agency);letter-spacing:-.5px;margin-bottom:8px;">
                        <?php echo e($contrat->reference_bail_affichee); ?>

                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;font-size:12px;">
                        <div>
                            <div style="color:var(--text-3);margin-bottom:2px;">Type de bail</div>
                            <div style="font-weight:600;color:var(--text);"><?php echo e(\App\Models\Contrat::TYPES_BAIL[$contrat->type_bail] ?? ucfirst($contrat->type_bail)); ?></div>
                        </div>
                        <div>
                            <div style="color:var(--text-3);margin-bottom:2px;">Date de signature</div>
                            <div style="font-weight:600;color:var(--text);"><?php echo e($contrat->date_debut->format('d/m/Y')); ?></div>
                        </div>
                        <div>
                            <div style="color:var(--text-3);margin-bottom:2px;">Date de début</div>
                            <div style="font-weight:600;color:var(--text);"><?php echo e($contrat->date_debut->format('d/m/Y')); ?></div>
                        </div>
                        <div>
                            <div style="color:var(--text-3);margin-bottom:2px;">Date de fin</div>
                            <div style="font-weight:600;color:var(--text);">
                                <?php echo e($contrat->date_fin ? $contrat->date_fin->format('d/m/Y') : 'Indéterminée'); ?>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="card section-gap">
                <div class="card-header">
                    <span class="card-title">💰 Décomposition du loyer mensuel</span>
                </div>
                <div class="card-body" style="padding:0;">

                    
                    <div style="display:flex;justify-content:space-between;align-items:center;padding:14px 20px;border-bottom:1px solid var(--border);">
                        <div>
                            <div style="font-size:13px;color:var(--text-2);">Loyer nu</div>
                            <div style="font-size:11px;color:var(--text-3);">Base de calcul de la commission agence</div>
                        </div>
                        <div style="font-weight:700;font-size:15px;color:var(--text);" class="text-money">
                            <?php echo e(number_format($decomposition['loyer_nu'], 0, ',', ' ')); ?> FCFA
                        </div>
                    </div>

                    
                    <div style="display:flex;justify-content:space-between;align-items:center;padding:14px 20px;border-bottom:1px solid var(--border);">
                        <div>
                            <div style="font-size:13px;color:var(--text-2);">+ Charges locatives</div>
                            <div style="font-size:11px;color:var(--text-3);">Eau, électricité, gardiennage...</div>
                        </div>
                        <div style="font-weight:700;font-size:15px;color:var(--text-2);" class="text-money">
                            <?php echo e(number_format($decomposition['charges'], 0, ',', ' ')); ?> FCFA
                        </div>
                    </div>

                    
                    <div style="display:flex;justify-content:space-between;align-items:center;padding:14px 20px;border-bottom:1px solid var(--border);">
                        <div>
                            <div style="font-size:13px;color:var(--text-2);">+ TOM</div>
                            <div style="font-size:11px;color:var(--text-3);">Taxe sur les Ordures Ménagères</div>
                        </div>
                        <div style="font-weight:700;font-size:15px;color:var(--text-2);" class="text-money">
                            <?php echo e(number_format($decomposition['tom'], 0, ',', ' ')); ?> FCFA
                        </div>
                    </div>

                    
                    <div style="display:flex;justify-content:space-between;align-items:center;padding:16px 20px;background:var(--agency-soft);">
                        <div style="font-size:13px;font-weight:700;color:var(--agency);">= TOTAL MENSUEL ENCAISSÉ</div>
                        <div style="font-weight:900;font-size:18px;color:var(--agency);" class="text-money">
                            <?php echo e(number_format($decomposition['total'], 0, ',', ' ')); ?> FCFA
                        </div>
                    </div>

                </div>
            </div>

            
            <div class="card section-gap">
                <div class="card-header">
                    <span class="card-title">🏦 Caution & Frais</span>
                </div>
                <div class="card-body">
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;font-size:12px;">
                        <div>
                            <div style="color:var(--text-3);margin-bottom:3px;">Caution</div>
                            <div style="font-weight:700;font-size:15px;color:var(--text);" class="text-money">
                                <?php echo e(number_format($contrat->caution, 0, ',', ' ')); ?> FCFA
                            </div>
                            <div style="color:var(--text-3);font-size:11px;margin-top:1px;"><?php echo e($contrat->nombre_mois_caution); ?> mois</div>
                        </div>
                        <div>
                            <div style="color:var(--text-3);margin-bottom:3px;">Frais d'agence</div>
                            <div style="font-weight:700;font-size:15px;color:var(--text);" class="text-money">
                                <?php echo e(number_format($contrat->frais_agence ?? 0, 0, ',', ' ')); ?> FCFA
                            </div>
                            <div style="color:var(--text-3);font-size:11px;margin-top:1px;">À la signature</div>
                        </div>
                        <?php if(($contrat->indexation_annuelle ?? 0) > 0): ?>
                        <div>
                            <div style="color:var(--text-3);margin-bottom:3px;">Indexation annuelle</div>
                            <div style="font-weight:700;font-size:15px;color:var(--text);"><?php echo e($contrat->indexation_annuelle); ?>%</div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </div>

        
        <div>

            
            <div class="card section-gap">
                <div class="card-header">
                    <span class="card-title">🏠 Bien loué</span>
                    <a href="<?php echo e(route('biens.show', $contrat->bien)); ?>" class="link">Voir la fiche →</a>
                </div>
                <div class="card-body">
                    <div style="font-size:15px;font-weight:700;color:var(--text);margin-bottom:4px;">
                        <?php echo e($contrat->bien->reference); ?>

                    </div>
                    <div style="font-size:13px;color:var(--text-2);"><?php echo e($contrat->bien->type); ?></div>
                    <div style="font-size:12px;color:var(--text-3);margin-top:4px;">
                        <?php echo e($contrat->bien->adresse); ?>

                        <?php if($contrat->bien->quartier): ?>, <?php echo e($contrat->bien->quartier); ?><?php endif; ?>
                        <?php if($contrat->bien->commune): ?>, <?php echo e($contrat->bien->commune); ?><?php endif; ?>
                        , <?php echo e($contrat->bien->ville); ?>

                    </div>
                    <?php if($contrat->bien->surface_m2 || $contrat->bien->nombre_pieces): ?>
                    <div style="font-size:11px;color:var(--text-3);margin-top:4px;">
                        <?php if($contrat->bien->surface_m2): ?><?php echo e($contrat->bien->surface_m2); ?> m²<?php endif; ?>
                        <?php if($contrat->bien->nombre_pieces): ?> · <?php echo e($contrat->bien->nombre_pieces); ?> pièces@endif
                        <?php if($contrat->bien->meuble): ?> · Meublé<?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            
            <div class="card section-gap">
                <div class="card-header">
                    <span class="card-title">👤 Locataire</span>
                </div>
                <div class="card-body">
                    <div style="font-size:15px;font-weight:700;color:var(--text);margin-bottom:4px;">
                        <?php echo e($contrat->locataire->name); ?>

                    </div>
                    <div style="font-size:12px;color:var(--text-3);"><?php echo e($contrat->locataire->email); ?></div>
                    <?php if($contrat->locataire->telephone): ?>
                    <div style="font-size:12px;color:var(--text-3);"><?php echo e($contrat->locataire->telephone); ?></div>
                    <?php endif; ?>
                </div>
            </div>

            
            <?php if($contrat->garant_nom): ?>
            <div class="card section-gap">
                <div class="card-header">
                    <span class="card-title">🛡️ Garant</span>
                </div>
                <div class="card-body">
                    <div style="font-size:14px;font-weight:700;color:var(--text);margin-bottom:6px;"><?php echo e($contrat->garant_nom); ?></div>
                    <div style="font-size:12px;color:var(--text-3);">
                        <?php echo e($contrat->garant_telephone ?? ''); ?>

                        <?php if($contrat->garant_adresse): ?> · <?php echo e($contrat->garant_adresse); ?> <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            
            <div class="card section-gap">
                <div class="card-header">
                    <span class="card-title">📊 Suivi des paiements</span>
                    <a href="<?php echo e(route('admin.paiements.index', ['contrat_id' => $contrat->id])); ?>" class="link">Voir tout →</a>
                </div>
                <div class="card-body">
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px;">
                        <div style="text-align:center;padding:12px;background:var(--bg);border-radius:var(--radius-sm);">
                            <div style="font-size:24px;font-weight:800;color:var(--text);"><?php echo e($nbPaiements); ?></div>
                            <div style="font-size:11px;color:var(--text-3);margin-top:2px;">Paiements reçus</div>
                        </div>
                        <div style="text-align:center;padding:12px;background:var(--bg);border-radius:var(--radius-sm);">
                            <div style="font-size:16px;font-weight:800;color:#16a34a;" class="text-money"><?php echo e(number_format($totalPaye, 0, ',', ' ')); ?></div>
                            <div style="font-size:11px;color:var(--text-3);margin-top:2px;">FCFA encaissés</div>
                        </div>
                    </div>

                    
                    <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:var(--radius-sm);padding:14px;">
                        <div style="font-size:10px;font-weight:700;color:#92400e;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">Prochain loyer attendu</div>
                        <div style="font-weight:700;font-size:16px;color:#d97706;"><?php echo e($prochainePeriode->translatedFormat('F Y')); ?></div>
                        <div style="font-size:12px;color:#92400e;margin-top:2px;" class="text-money">
                            <?php echo e(number_format($decomposition['total'], 0, ',', ' ')); ?> FCFA attendus
                        </div>
                        <?php if($contrat->statut === 'actif'): ?>
                        <a href="<?php echo e(route('admin.paiements.create', ['contrat_id' => $contrat->id])); ?>"
                           class="btn btn-primary btn-sm" style="margin-top:10px;width:100%;justify-content:center;">
                            Enregistrer ce paiement →
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            
            <?php if($contrat->observations): ?>
            <div class="card section-gap">
                <div class="card-header">
                    <span class="card-title">📝 Observations</span>
                </div>
                <div class="card-body" style="font-size:13px;color:var(--text-2);line-height:1.6;">
                    <?php echo e($contrat->observations); ?>

                </div>
            </div>
            <?php endif; ?>

        </div>
    </div>

    
    <div class="card section-gap">
        <div class="card-header">
            <span class="card-title">💳 Historique des paiements</span>
            <?php if($contrat->statut === 'actif'): ?>
                <a href="<?php echo e(route('admin.paiements.create', ['contrat_id' => $contrat->id])); ?>" class="btn btn-primary btn-sm">+ Paiement</a>
            <?php endif; ?>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Référence</th>
                        <th>Période</th>
                        <th>Date</th>
                        <th>Mode</th>
                        <th style="text-align:right;">Total encaissé</th>
                        <th style="text-align:right;">Net proprio</th>
                        <th style="text-align:center;">Statut</th>
                        <th style="text-align:center;">Quittance</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $contrat->paiements->sortByDesc('periode'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="text-ref"><?php echo e($p->reference_paiement); ?></td>
                            <td style="font-weight:600;"><?php echo e(\Carbon\Carbon::parse($p->periode)->translatedFormat('F Y')); ?></td>
                            <td style="color:var(--text-2);"><?php echo e(\Carbon\Carbon::parse($p->date_paiement)->format('d/m/Y')); ?></td>
                            <td style="color:var(--text-2);"><?php echo e(ucfirst(str_replace('_', ' ', $p->mode_paiement))); ?></td>
                            <td style="text-align:right;font-weight:700;" class="text-money">
                                <?php echo e(number_format($p->montant_encaisse, 0, ',', ' ')); ?> F
                            </td>
                            <td style="text-align:right;color:#16a34a;font-weight:700;" class="text-money">
                                <?php echo e(number_format($p->net_proprietaire, 0, ',', ' ')); ?> F
                            </td>
                            <td style="text-align:center;">
                                <?php if($p->statut === 'valide'): ?>
                                    <span class="badge badge-green">Validé</span>
                                <?php elseif($p->statut === 'annule'): ?>
                                    <span class="badge badge-red">Annulé</span>
                                <?php else: ?>
                                    <span class="badge badge-amber"><?php echo e(ucfirst($p->statut)); ?></span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align:center;">
                                <?php if($p->statut === 'valide'): ?>
                                    <a href="<?php echo e(route('admin.paiements.pdf', $p)); ?>"
                                       title="Télécharger la quittance"
                                       style="display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:6px;border:1px solid var(--border);color:var(--text-2);text-decoration:none;font-size:14px;transition:all .15s;"
                                       onmouseenter="this.style.background='var(--agency)';this.style.color='white';this.style.borderColor='var(--agency)'"
                                       onmouseleave="this.style.background='transparent';this.style.color='var(--text-2)';this.style.borderColor='var(--border)'">
                                        📄
                                    </a>
                                <?php else: ?>
                                    <span style="color:var(--text-3);">—</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="8" style="text-align:center;padding:32px;color:var(--text-3);">
                                Aucun paiement enregistré pour ce contrat.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    
    <?php if($contrat->statut === 'actif'): ?>
    <div class="card section-gap" style="border-color:#fecaca;">
        <div class="card-body" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
            <div>
                <div style="font-size:13px;font-weight:600;color:#dc2626;">Zone danger</div>
                <div style="font-size:12px;color:var(--text-3);margin-top:2px;">La résiliation est irréversible et remet le bien en statut "Disponible".</div>
            </div>
            <form method="POST" action="<?php echo e(route('admin.contrats.destroy', $contrat)); ?>"
                  onsubmit="return confirm('Résilier définitivement ce contrat ?\n\nRéf. : <?php echo e($contrat->reference_bail_affichee); ?>\nLocataire : <?php echo e($contrat->locataire->name); ?>')">
                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                <button type="submit" class="btn btn-danger">
                    Résilier le contrat
                </button>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <style>
        @media (max-width: 768px) {
            #contrat-grid { grid-template-columns: 1fr !important; }
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