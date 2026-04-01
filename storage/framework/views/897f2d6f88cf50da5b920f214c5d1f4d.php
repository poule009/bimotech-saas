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
     <?php $__env->slot('header', null, []); ?> Rapport financier <?php $__env->endSlot(); ?>

    
    <div class="flex-between section-gap" style="flex-wrap:wrap;gap:12px;">
        <div>
            <h1 style="font-size:20px;font-weight:700;color:var(--text);letter-spacing:-.3px;">Rapport financier</h1>
            <p style="font-size:13px;color:var(--text-3);margin-top:3px;"><?php echo e($debutMois->translatedFormat('F Y')); ?></p>
        </div>
        <form method="GET" action="<?php echo e(route('admin.rapports.financier')); ?>"
              style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
            <select name="mois" style="border:1.5px solid var(--border);border-radius:var(--radius-sm);padding:8px 12px;font-size:13px;color:var(--text);background:var(--surface);outline:none;font-family:inherit;cursor:pointer;">
                <?php $__currentLoopData = range(1, 12); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($m); ?>" <?php echo e($mois == $m ? 'selected' : ''); ?>>
                        <?php echo e(\Carbon\Carbon::create(null, $m)->translatedFormat('F')); ?>

                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <select name="annee" style="border:1.5px solid var(--border);border-radius:var(--radius-sm);padding:8px 12px;font-size:13px;color:var(--text);background:var(--surface);outline:none;font-family:inherit;cursor:pointer;">
                <?php $__currentLoopData = $anneesDisponibles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($a); ?>" <?php echo e($annee == $a ? 'selected' : ''); ?>><?php echo e($a); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <button type="submit" class="btn btn-primary btn-sm">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:14px;height:14px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                </svg>
                Filtrer
            </button>
        </form>
    </div>

    
    <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px;" class="section-gap" id="rapport-kpi">
        <div class="kpi">
            <div class="kpi-label">Loyers encaissés</div>
            <div class="kpi-value text-money" style="font-size:18px;"><?php echo e(number_format($kpiMois['total_loyers'], 0, ',', ' ')); ?> <span style="font-size:12px;font-weight:500;color:var(--text-3);">F</span></div>
            <div class="kpi-sub"><?php echo e($kpiMois['nb_paiements']); ?> paiements</div>
        </div>
        <div class="kpi">
            <div class="kpi-label">Commission HT</div>
            <div class="kpi-value text-money" style="color:var(--agency);font-size:18px;"><?php echo e(number_format($kpiMois['total_commission'], 0, ',', ' ')); ?> <span style="font-size:12px;font-weight:500;color:var(--text-3);">F</span></div>
        </div>
        <div class="kpi" style="border-color:#fde68a;background:#fffbeb;">
            <div class="kpi-label" style="color:#f59e0b;">TVA à reverser DGI</div>
            <div class="kpi-value text-money" style="color:#d97706;font-size:18px;"><?php echo e(number_format($kpiMois['total_tva'], 0, ',', ' ')); ?> <span style="font-size:12px;font-weight:500;color:#f59e0b;">F</span></div>
            <div class="kpi-sub" style="color:#f59e0b;">18% sur commission</div>
        </div>
        <div class="kpi">
            <div class="kpi-label">Commission TTC</div>
            <div class="kpi-value text-money" style="font-size:18px;"><?php echo e(number_format($kpiMois['total_ttc'], 0, ',', ' ')); ?> <span style="font-size:12px;font-weight:500;color:var(--text-3);">F</span></div>
        </div>
        <div class="kpi" style="border-color:#bbf7d0;background:#f0fdf4;grid-column:span 2;">
            <div class="kpi-label" style="color:#22c55e;">Net propriétaires</div>
            <div class="kpi-value text-money" style="color:#16a34a;font-size:22px;"><?php echo e(number_format($kpiMois['total_net_proprio'], 0, ',', ' ')); ?> <span style="font-size:13px;font-weight:500;color:#22c55e;">FCFA</span></div>
        </div>
    </div>

    
    <?php if($biensImpayés->isNotEmpty()): ?>
        <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:var(--radius);padding:20px 24px;" class="section-gap">
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:14px;">
                <span style="font-size:16px;">⚠️</span>
                <span style="font-size:14px;font-weight:700;color:#dc2626;">
                    <?php echo e($biensImpayés->count()); ?> contrat(s) sans paiement ce mois
                </span>
            </div>
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:10px;">
                <?php $__currentLoopData = $biensImpayés; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contrat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div style="background:white;border:1px solid #fecaca;border-radius:var(--radius-sm);padding:12px;">
                        <div style="font-weight:700;font-size:13px;color:var(--text);margin-bottom:2px;"><?php echo e($contrat->bien->reference); ?></div>
                        <div style="font-size:12px;color:var(--text-2);"><?php echo e($contrat->locataire->name); ?></div>
                        <div style="font-size:11px;color:var(--text-3);"><?php echo e($contrat->locataire->telephone ?? $contrat->locataire->email); ?></div>
                        <div style="font-weight:700;font-size:13px;color:#dc2626;margin-top:6px;" class="text-money">
                            <?php echo e(number_format($contrat->loyer_contractuel, 0, ',', ' ')); ?> F attendu
                        </div>
                        <a href="<?php echo e(route('admin.paiements.create', ['contrat_id' => $contrat->id])); ?>"
                           style="font-size:12px;color:var(--agency);font-weight:600;display:inline-block;margin-top:6px;">
                            Enregistrer paiement →
                        </a>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    <?php endif; ?>

    
    <div class="card section-gap">
        <div class="card-header">
            <span class="card-title">Évolution sur 12 mois</span>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Mois</th>
                        <th style="text-align:right;">Paiements</th>
                        <th style="text-align:right;">Loyers</th>
                        <th style="text-align:right;">Commission HT</th>
                        <th style="text-align:right;">TVA</th>
                        <th style="text-align:right;">Commission TTC</th>
                        <th style="text-align:right;">Net proprio</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $evolution; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ligne): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr style="<?php echo e($ligne->mois === now()->format('Y-m') ? 'background:#f0f4ff;' : ''); ?>">
                            <td style="font-weight:600;font-size:13px;">
                                <?php echo e($ligne->mois_label); ?>

                                <?php if($ligne->mois === now()->format('Y-m')): ?>
                                    <span class="badge badge-blue" style="margin-left:6px;font-size:10px;">En cours</span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align:right;color:var(--text-2);font-size:13px;"><?php echo e($ligne->nb_paiements); ?></td>
                            <td style="text-align:right;font-weight:600;" class="text-money"><?php echo e(number_format($ligne->loyers, 0, ',', ' ')); ?> F</td>
                            <td style="text-align:right;color:var(--agency);" class="text-money"><?php echo e(number_format($ligne->commissions, 0, ',', ' ')); ?> F</td>
                            <td style="text-align:right;color:#d97706;" class="text-money"><?php echo e(number_format($ligne->tva, 0, ',', ' ')); ?> F</td>
                            <td style="text-align:right;color:var(--text-2);" class="text-money"><?php echo e(number_format($ligne->ttc, 0, ',', ' ')); ?> F</td>
                            <td style="text-align:right;color:#16a34a;font-weight:700;" class="text-money"><?php echo e(number_format($ligne->net, 0, ',', ' ')); ?> F</td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" style="text-align:center;padding:32px;color:var(--text-3);font-size:13px;">Aucune donnée disponible</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
                <?php if($evolution->isNotEmpty()): ?>
                    <tfoot>
                        <tr style="background:#0f172a;">
                            <td style="padding:12px 16px;font-weight:700;color:white;font-size:13px;">TOTAL</td>
                            <td style="padding:12px 16px;text-align:right;font-weight:700;color:white;font-size:13px;"><?php echo e($evolution->sum('nb_paiements')); ?></td>
                            <td style="padding:12px 16px;text-align:right;font-weight:700;color:white;font-size:13px;" class="text-money"><?php echo e(number_format($evolution->sum('loyers'), 0, ',', ' ')); ?> F</td>
                            <td style="padding:12px 16px;text-align:right;font-weight:700;color:#a5b4fc;font-size:13px;" class="text-money"><?php echo e(number_format($evolution->sum('commissions'), 0, ',', ' ')); ?> F</td>
                            <td style="padding:12px 16px;text-align:right;font-weight:700;color:#fcd34d;font-size:13px;" class="text-money"><?php echo e(number_format($evolution->sum('tva'), 0, ',', ' ')); ?> F</td>
                            <td style="padding:12px 16px;text-align:right;font-weight:700;color:white;font-size:13px;" class="text-money"><?php echo e(number_format($evolution->sum('ttc'), 0, ',', ' ')); ?> F</td>
                            <td style="padding:12px 16px;text-align:right;font-weight:700;color:#6ee7b7;font-size:13px;" class="text-money"><?php echo e(number_format($evolution->sum('net'), 0, ',', ' ')); ?> F</td>
                        </tr>
                    </tfoot>
                <?php endif; ?>
            </table>
        </div>
    </div>

    
    <?php if($parProprietaire->isNotEmpty()): ?>
        <div class="card section-gap">
            <div class="card-header">
                <span class="card-title">Détail par propriétaire — <?php echo e($debutMois->translatedFormat('F Y')); ?></span>
            </div>
            <?php $__currentLoopData = $parProprietaire; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div style="padding:16px 20px;border-bottom:1px solid var(--border);">
                    <div class="flex-between" style="margin-bottom:12px;flex-wrap:wrap;gap:8px;">
                        <div>
                            <div style="font-weight:700;font-size:14px;color:var(--text);"><?php echo e($data['proprio']->name); ?></div>
                            <div style="font-size:12px;color:var(--text-3);"><?php echo e($data['proprio']->email); ?></div>
                        </div>
                        <div style="text-align:right;">
                            <div style="font-weight:800;font-size:15px;color:#16a34a;" class="text-money">
                                Net : <?php echo e(number_format($data['net'], 0, ',', ' ')); ?> F
                            </div>
                            <div style="font-size:12px;color:var(--text-3);" class="text-money">
                                Commission TTC : <?php echo e(number_format($data['commission'], 0, ',', ' ')); ?> F
                            </div>
                        </div>
                    </div>
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Bien</th>
                                    <th>Locataire</th>
                                    <th style="text-align:right;">Loyer</th>
                                    <th style="text-align:right;">Commission TTC</th>
                                    <th style="text-align:right;">Net</th>
                                    <th style="text-align:center;">PDF</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $data['paiements']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td style="font-size:13px;font-weight:500;"><?php echo e($p->contrat->bien->reference); ?></td>
                                        <td style="font-size:13px;color:var(--text-2);"><?php echo e($p->contrat->locataire->name); ?></td>
                                        <td style="text-align:right;font-weight:600;" class="text-money"><?php echo e(number_format($p->montant_encaisse, 0, ',', ' ')); ?> F</td>
                                        <td style="text-align:right;color:#d97706;" class="text-money"><?php echo e(number_format($p->commission_ttc, 0, ',', ' ')); ?> F</td>
                                        <td style="text-align:right;color:#16a34a;font-weight:700;" class="text-money"><?php echo e(number_format($p->net_proprietaire, 0, ',', ' ')); ?> F</td>
                                        <td style="text-align:center;">
                                            <a href="<?php echo e(route('admin.paiements.pdf', $p)); ?>" target="_blank"
                                               class="btn btn-secondary btn-sm">📄</a>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endif; ?>

    
    <div class="card section-gap">
        <div class="card-header">
            <span class="card-title">État général du parc</span>
        </div>
        <div class="card-body">
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-bottom:20px;" id="parc-grid">
                <div style="text-align:center;padding:14px;background:var(--bg);border-radius:var(--radius-sm);">
                    <div style="font-size:26px;font-weight:800;color:var(--text);"><?php echo e($statsGenerales['nb_biens']); ?></div>
                    <div style="font-size:11px;color:var(--text-3);margin-top:3px;">Biens total</div>
                </div>
                <div style="text-align:center;padding:14px;background:#f0fdf4;border-radius:var(--radius-sm);">
                    <div style="font-size:26px;font-weight:800;color:#16a34a;"><?php echo e($statsGenerales['nb_biens_loues']); ?></div>
                    <div style="font-size:11px;color:#22c55e;margin-top:3px;">Biens loués</div>
                </div>
                <div style="text-align:center;padding:14px;background:var(--bg);border-radius:var(--radius-sm);">
                    <?php
                        $taux = $statsGenerales['taux_occupation'];
                        $tc = $taux >= 80 ? '#16a34a' : ($taux >= 50 ? '#d97706' : '#dc2626');
                    ?>
                    <div style="font-size:26px;font-weight:800;color:<?php echo e($tc); ?>;"><?php echo e($taux); ?>%</div>
                    <div style="font-size:11px;color:var(--text-3);margin-top:3px;">Taux occupation</div>
                </div>
                <div style="text-align:center;padding:14px;background:#eff6ff;border-radius:var(--radius-sm);">
                    <div style="font-size:26px;font-weight:800;color:#2563eb;"><?php echo e($statsGenerales['nb_contrats']); ?></div>
                    <div style="font-size:11px;color:#3b82f6;margin-top:3px;">Contrats actifs</div>
                </div>
                <div style="text-align:center;padding:14px;background:#faf5ff;border-radius:var(--radius-sm);">
                    <div style="font-size:26px;font-weight:800;color:#7c3aed;"><?php echo e($statsGenerales['nb_proprietaires']); ?></div>
                    <div style="font-size:11px;color:#8b5cf6;margin-top:3px;">Propriétaires</div>
                </div>
                <div style="text-align:center;padding:14px;background:#fdf4ff;border-radius:var(--radius-sm);">
                    <div style="font-size:26px;font-weight:800;color:#c026d3;"><?php echo e($statsGenerales['nb_locataires']); ?></div>
                    <div style="font-size:11px;color:#d946ef;margin-top:3px;">Locataires</div>
                </div>
            </div>

            
            <div>
                <div style="display:flex;justify-content:space-between;font-size:12px;color:var(--text-3);margin-bottom:6px;">
                    <span>Taux d'occupation</span>
                    <span style="font-weight:600;color:<?php echo e($tc); ?>;"><?php echo e($taux); ?>%</span>
                </div>
                <div class="progress" style="height:8px;">
                    <div class="progress-bar" style="width:<?php echo e($taux); ?>%;background:<?php echo e($tc); ?>;"></div>
                </div>
            </div>
        </div>
    </div>

    <style>
        @media (min-width: 768px) {
            #rapport-kpi { grid-template-columns: repeat(5, 1fr); }
            #rapport-kpi > *:last-child { grid-column: auto; }
            #parc-grid { grid-template-columns: repeat(6, 1fr); }
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
<?php endif; ?><?php /**PATH C:\Users\ph\bimotech-immo\resources\views/rapports/financier.blade.php ENDPATH**/ ?>