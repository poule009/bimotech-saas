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
     <?php $__env->slot('header', null, []); ?> Bilans fiscaux <?php $__env->endSlot(); ?>

<style>
.kpi-row { display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-bottom:22px; }
.kpi-mini { background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:16px 20px;position:relative;overflow:hidden; }
.kpi-mini::before { content:'';position:absolute;top:0;left:0;right:0;height:3px;border-radius:12px 12px 0 0; }
.kpi-mini.gold::before  { background:#c9a84c; }
.kpi-mini.green::before { background:#16a34a; }
.kpi-mini.blue::before  { background:#1d4ed8; }
.kpi-lbl { font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:.8px;color:#6b7280;margin-bottom:4px; }
.kpi-val { font-family:'Syne',sans-serif;font-size:22px;font-weight:700;color:#0d1117; }
.kpi-u   { font-size:11px;font-weight:400;color:#9ca3af;margin-left:2px; }
.kpi-s   { font-size:11px;color:#9ca3af;margin-top:4px; }
.filter-bar { background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:14px 18px;margin-bottom:20px;display:flex;align-items:center;gap:10px; }
.filter-select { padding:8px 13px;border:1px solid #e5e7eb;border-radius:8px;font-size:13px;font-family:'DM Sans',sans-serif;background:#f9fafb;outline:none;cursor:pointer; }
.filter-btn { padding:8px 18px;background:#0d1117;color:#fff;border:none;border-radius:8px;font-size:12px;font-weight:500;font-family:'DM Sans',sans-serif;cursor:pointer; }
.card { background:#fff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden; }
.dt { width:100%;border-collapse:collapse; }
.dt th { padding:9px 16px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#9ca3af;background:#f9fafb;border-bottom:1px solid #e5e7eb;text-align:left; }
.dt td { padding:12px 16px;font-size:13px;color:#374151;border-bottom:1px solid #f3f4f6;vertical-align:middle; }
.dt tbody tr:last-child td { border-bottom:none; }
.dt tbody tr:hover { background:#f9fafb; }
.badge { display:inline-flex;align-items:center;gap:4px;padding:3px 9px;border-radius:99px;font-size:11px;font-weight:600; }
.badge.g { background:#dcfce7;color:#16a34a; }
.badge.o { background:#f5e9c9;color:#8a6e2f; }
.badge.r { background:#fee2e2;color:#dc2626; }
.amt { font-family:'Syne',sans-serif;font-weight:600;color:#0d1117; }
.amt-green { font-family:'Syne',sans-serif;font-weight:600;color:#16a34a; }
.amt-red   { font-family:'Syne',sans-serif;font-weight:600;color:#dc2626; }
</style>

<div style="padding:24px 32px 48px">

    <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:22px">
        <div>
            <h1 style="font-family:'Syne',sans-serif;font-size:22px;font-weight:700;color:#0d1117;letter-spacing:-.4px">Bilans fiscaux propriétaires</h1>
            <p style="font-size:13px;color:#6b7280;margin-top:3px">
                Abattement 30% · IRPP · CFPB · TVA collectée · BRS — Année <?php echo e($annee); ?>

            </p>
        </div>
        <div style="text-align:right;font-size:12px;color:#9ca3af;background:#f9fafb;border:1px solid #e5e7eb;border-radius:9px;padding:10px 14px;line-height:1.6">
            ⚠ Estimation fiscale<br>À vérifier avec un comptable / DGI
        </div>
    </div>

    
    <form method="GET">
        <div class="filter-bar">
            <span style="font-size:12px;color:#6b7280;font-weight:500">Année fiscale :</span>
            <select name="annee" class="filter-select">
                <?php $__currentLoopData = $anneesDisponibles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($a); ?>" <?php echo e($annee == $a ? 'selected':''); ?>><?php echo e($a); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <button type="submit" class="filter-btn">Afficher</button>
        </div>
    </form>

    
    <?php
        $totalBrut    = $bilans->sum('revenus_bruts_loyers');
        $totalIrpp    = $bilans->sum('irpp_estime');
        $totalTvaCol  = $bilans->sum('tva_loyer_collectee');
        $totalBrs     = $bilans->sum('brs_retenu_total');
    ?>
    <div class="kpi-row">
        <div class="kpi-mini gold">
            <div class="kpi-lbl">Revenus bruts totaux <?php echo e($annee); ?></div>
            <div class="kpi-val"><?php echo e(number_format($totalBrut, 0, ',', ' ')); ?><span class="kpi-u">F</span></div>
            <div class="kpi-s"><?php echo e($proprietaires->count()); ?> propriétaire(s)</div>
        </div>
        <div class="kpi-mini green">
            <div class="kpi-lbl">TVA loyer collectée</div>
            <div class="kpi-val" style="color:#d97706"><?php echo e(number_format($totalTvaCol, 0, ',', ' ')); ?><span class="kpi-u">F</span></div>
            <div class="kpi-s">À reverser à la DGI</div>
        </div>
        <div class="kpi-mini blue">
            <div class="kpi-lbl">BRS total retenu</div>
            <div class="kpi-val" style="color:#dc2626"><?php echo e(number_format($totalBrs, 0, ',', ' ')); ?><span class="kpi-u">F</span></div>
            <div class="kpi-s">Par les locataires entreprises</div>
        </div>
    </div>

    
    <div class="card">
        <div style="padding:16px 20px;border-bottom:1px solid #e5e7eb;display:flex;align-items:center;justify-content:space-between">
            <div style="font-family:'Syne',sans-serif;font-size:14px;font-weight:700;color:#0d1117">
                Propriétaires — Bilan <?php echo e($annee); ?>

            </div>
            <div style="font-size:12px;color:#6b7280"><?php echo e($proprietaires->count()); ?> propriétaire(s)</div>
        </div>
        <div style="overflow-x:auto">
            <table class="dt">
                <thead>
                    <tr>
                        <th>Propriétaire</th>
                        <th style="text-align:right">Revenus bruts</th>
                        <th style="text-align:right">Base imposable (70%)</th>
                        <th style="text-align:right">IRPP estimé</th>
                        <th style="text-align:right">TVA collectée</th>
                        <th style="text-align:right">BRS retenu</th>
                        <th style="text-align:center">Statut</th>
                        <th style="text-align:center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $proprietaires; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $proprio): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php $bilan = $bilans->get($proprio->id); ?>
                    <tr>
                        <td>
                            <div style="font-size:13px;font-weight:600;color:#0d1117"><?php echo e($proprio->name); ?></div>
                            <div style="font-size:11px;color:#6b7280"><?php echo e($proprio->email); ?></div>
                        </td>
                        <td style="text-align:right">
                            <?php if($bilan): ?>
                                <span class="amt"><?php echo e(number_format($bilan->revenus_bruts_loyers, 0, ',', ' ')); ?> F</span>
                            <?php else: ?> <span style="color:#9ca3af">—</span> <?php endif; ?>
                        </td>
                        <td style="text-align:right">
                            <?php if($bilan): ?>
                                <span class="amt"><?php echo e(number_format($bilan->base_imposable, 0, ',', ' ')); ?> F</span>
                                <div style="font-size:10px;color:#9ca3af">après abattement 30%</div>
                            <?php else: ?> <span style="color:#9ca3af">—</span> <?php endif; ?>
                        </td>
                        <td style="text-align:right">
                            <?php if($bilan): ?>
                                <span class="amt-red"><?php echo e(number_format($bilan->irpp_estime, 0, ',', ' ')); ?> F</span>
                            <?php else: ?> <span style="color:#9ca3af">—</span> <?php endif; ?>
                        </td>
                        <td style="text-align:right">
                            <?php if($bilan && $bilan->tva_loyer_collectee > 0): ?>
                                <span style="font-family:'Syne',sans-serif;font-weight:600;color:#d97706"><?php echo e(number_format($bilan->tva_loyer_collectee, 0, ',', ' ')); ?> F</span>
                            <?php elseif($bilan): ?>
                                <span style="color:#9ca3af">—</span>
                            <?php else: ?> <span style="color:#9ca3af">—</span> <?php endif; ?>
                        </td>
                        <td style="text-align:right">
                            <?php if($bilan && $bilan->brs_retenu_total > 0): ?>
                                <span style="font-family:'Syne',sans-serif;font-weight:600;color:#dc2626"><?php echo e(number_format($bilan->brs_retenu_total, 0, ',', ' ')); ?> F</span>
                            <?php elseif($bilan): ?>
                                <span style="color:#9ca3af">—</span>
                            <?php else: ?> <span style="color:#9ca3af">—</span> <?php endif; ?>
                        </td>
                        <td style="text-align:center">
                            <?php if($bilan): ?>
                                <span class="badge g">Calculé</span>
                                <div style="font-size:10px;color:#9ca3af;margin-top:2px"><?php echo e($bilan->calcule_le->format('d/m/Y')); ?></div>
                            <?php else: ?>
                                <span class="badge o">Non calculé</span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align:center">
                            <div style="display:flex;align-items:center;justify-content:center;gap:6px">
                                
                                <form method="POST" action="<?php echo e(route('admin.bilans-fiscaux.calculate', $proprio)); ?>">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="annee" value="<?php echo e($annee); ?>">
                                    <button type="submit" title="<?php echo e($bilan ? 'Recalculer' : 'Calculer'); ?>"
                                            style="display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border:1px solid #e5e7eb;border-radius:7px;background:#fff;cursor:pointer;color:#374151;transition:all .15s"
                                            onmouseover="this.style.borderColor='#c9a84c';this.style.color='#8a6e2f'"
                                            onmouseout="this.style.borderColor='#e5e7eb';this.style.color='#374151'">
                                        <svg style="width:13px;height:13px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 11-2.12-9.36L23 10"/></svg>
                                    </button>
                                </form>
                                
                                <?php if($bilan): ?>
                                <a href="<?php echo e(route('admin.bilans-fiscaux.show', [$proprio, 'annee' => $annee])); ?>"
                                   style="display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border:1px solid #e5e7eb;border-radius:7px;background:#fff;text-decoration:none;color:#374151;transition:all .15s"
                                   onmouseover="this.style.borderColor='#c9a84c';this.style.color='#8a6e2f'"
                                   onmouseout="this.style.borderColor='#e5e7eb';this.style.color='#374151'"
                                   title="Voir le détail">
                                    <svg style="width:13px;height:13px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                </a>
                                
                                <a href="<?php echo e(route('admin.bilans-fiscaux.pdf', [$proprio, 'annee' => $annee])); ?>"
                                   target="_blank"
                                   style="display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border:1px solid #e5e7eb;border-radius:7px;background:#fff;text-decoration:none;color:#374151;transition:all .15s"
                                   onmouseover="this.style.borderColor='#16a34a';this.style.color='#16a34a'"
                                   onmouseout="this.style.borderColor='#e5e7eb';this.style.color='#374151'"
                                   title="Exporter PDF">
                                    <svg style="width:13px;height:13px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>

    <div style="margin-top:14px;padding:12px 16px;background:#fffbeb;border:1px solid #fde68a;border-radius:10px;font-size:12px;color:#92400e;line-height:1.6">
        <strong>Note :</strong> Ces bilans sont des <strong>estimations</strong> basées sur les paiements enregistrés dans BimoTech.
        L'IRPP est calculé selon le barème progressif sénégalais (Art. 65 CGI SN) après abattement forfaitaire 30% (Art. 58 CGI SN).
        La CFPB est estimée à 5% de la valeur locative. <strong>Consultez un comptable ou la DGI avant toute déclaration.</strong>
    </div>

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
<?php endif; ?><?php /**PATH C:\Users\ph\bimotech-immo\resources\views/admin/bilans-fiscaux/index.blade.php ENDPATH**/ ?>