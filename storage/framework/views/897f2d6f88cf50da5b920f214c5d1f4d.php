
<?php $__env->startSection('title', 'Rapport financier'); ?>
<?php $__env->startSection('breadcrumb', 'Rapports › Financier'); ?>

<?php $__env->startSection('content'); ?>
<style>
.kpi-row { display:grid;grid-template-columns:repeat(5,1fr);gap:14px;margin-bottom:22px; }
.kpi { background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:16px 18px;position:relative;overflow:hidden; }
.kpi::before { content:'';position:absolute;top:0;left:0;right:0;height:3px;border-radius:12px 12px 0 0; }
.kpi.gold::before   { background:#c9a84c; }
.kpi.green::before  { background:#16a34a; }
.kpi.blue::before   { background:#1d4ed8; }
.kpi.purple::before { background:#7c3aed; }
.kpi.dark::before   { background:#0d1117; }
.kpi-lbl { font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#9ca3af;margin-bottom:6px; }
.kpi-val { font-family:'Syne',sans-serif;font-size:20px;font-weight:700;color:#0d1117;line-height:1.1; }
.kpi-sub { font-size:11px;color:#9ca3af;margin-top:4px; }

.table-card { background:#fff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden;margin-bottom:20px; }
.table-hd { padding:14px 20px;border-bottom:1px solid #e5e7eb;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px; }
.table-title { font-family:'Syne',sans-serif;font-size:13px;font-weight:700;color:#0d1117; }
.dt { width:100%;border-collapse:collapse; }
.dt th { padding:9px 16px;text-align:left;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.7px;color:#9ca3af;background:#f9fafb;border-bottom:1px solid #e5e7eb; }
.dt td { padding:11px 16px;font-size:13px;color:#374151;border-bottom:1px solid #f3f4f6;vertical-align:middle; }
.dt tbody tr:last-child td { border-bottom:none; }
.dt tbody tr:hover { background:#fafafa; }

/* Navigation mois/année */
.nav-periode { display:flex;align-items:center;gap:8px; }
.nav-btn { display:flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:7px;border:1px solid #e5e7eb;background:#fff;color:#6b7280;text-decoration:none;transition:all .15s; }
.nav-btn:hover { border-color:#c9a84c;color:#8a6e2f; }
.nav-current { font-family:'Syne',sans-serif;font-size:14px;font-weight:600;color:#0d1117;min-width:140px;text-align:center; }

/* Badges */
.badge { display:inline-flex;align-items:center;gap:4px;padding:2px 9px;border-radius:99px;font-size:11px;font-weight:600; }
.badge-green { background:#dcfce7;color:#16a34a; }
.badge-red   { background:#fee2e2;color:#dc2626; }
</style>

<div style="padding:0 0 48px">

    
    <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:22px;flex-wrap:wrap;gap:12px">
        <div>
            <h1 style="font-family:'Syne',sans-serif;font-size:22px;font-weight:700;color:#0d1117;letter-spacing:-.4px">
                Rapport financier
            </h1>
            <p style="font-size:13px;color:#6b7280;margin-top:3px">
                <?php echo e($debutMois->translatedFormat('F Y')); ?> · <?php echo e($kpiMois['nb_paiements']); ?> paiement(s) validé(s)
            </p>
        </div>

        <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">
            
            <?php
                $prevMois  = $mois == 1  ? 12 : $mois - 1;
                $prevAnnee = $mois == 1  ? $annee - 1 : $annee;
                $nextMois  = $mois == 12 ? 1  : $mois + 1;
                $nextAnnee = $mois == 12 ? $annee + 1 : $annee;
            ?>
            <div class="nav-periode">
                <a href="<?php echo e(route('admin.rapports.financier', ['mois' => $prevMois, 'annee' => $prevAnnee])); ?>" class="nav-btn">
                    <svg style="width:13px;height:13px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                </a>
                <div class="nav-current"><?php echo e($debutMois->translatedFormat('F Y')); ?></div>
                <?php if($nextAnnee < now()->year || ($nextAnnee == now()->year && $nextMois <= now()->month)): ?>
                <a href="<?php echo e(route('admin.rapports.financier', ['mois' => $nextMois, 'annee' => $nextAnnee])); ?>" class="nav-btn">
                    <svg style="width:13px;height:13px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                </a>
                <?php else: ?>
                <span class="nav-btn" style="opacity:.3;cursor:not-allowed">
                    <svg style="width:13px;height:13px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                </span>
                <?php endif; ?>
            </div>

            
            <a href="<?php echo e(route('admin.rapports.financier.export-pdf', ['mois' => $mois, 'annee' => $annee])); ?>"
               style="display:inline-flex;align-items:center;gap:6px;padding:9px 16px;background:#0d1117;color:#fff;font-family:'DM Sans',sans-serif;font-size:13px;font-weight:600;border-radius:8px;text-decoration:none"
               onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                <svg style="width:14px;height:14px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                Exporter PDF
            </a>
        </div>
    </div>

    
    <div class="kpi-row">
        <div class="kpi gold">
            <div class="kpi-lbl">Loyers encaissés</div>
            <div class="kpi-val"><?php echo e(number_format($kpiMois['total_loyers'], 0, ',', ' ')); ?></div>
            <div class="kpi-sub">FCFA · <?php echo e($kpiMois['nb_paiements']); ?> paiements</div>
        </div>
        <div class="kpi green">
            <div class="kpi-lbl">Net propriétaires</div>
            <div class="kpi-val" style="color:#16a34a"><?php echo e(number_format($kpiMois['total_net_proprio'], 0, ',', ' ')); ?></div>
            <div class="kpi-sub">FCFA à reverser</div>
        </div>
        <div class="kpi blue">
            <div class="kpi-lbl">Commission HT</div>
            <div class="kpi-val" style="color:#1d4ed8"><?php echo e(number_format($kpiMois['total_commission'], 0, ',', ' ')); ?></div>
            <div class="kpi-sub">FCFA agence</div>
        </div>
        <div class="kpi purple">
            <div class="kpi-lbl">TVA commission</div>
            <div class="kpi-val" style="color:#7c3aed"><?php echo e(number_format($kpiMois['total_tva'], 0, ',', ' ')); ?></div>
            <div class="kpi-sub">FCFA (18%)</div>
        </div>
        <div class="kpi dark">
            <div class="kpi-lbl">Commission TTC</div>
            <div class="kpi-val"><?php echo e(number_format($kpiMois['total_ttc'], 0, ',', ' ')); ?></div>
            <div class="kpi-sub">FCFA total agence</div>
        </div>
    </div>

    
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:22px">
        <?php $__currentLoopData = [
            ['label' => 'Biens total', 'val' => $statsGenerales['nb_biens']],
            ['label' => 'Biens loués', 'val' => $statsGenerales['nb_biens_loues']],
            ['label' => 'Taux occupation', 'val' => $statsGenerales['taux_occupation'].'%'],
            ['label' => 'Contrats actifs', 'val' => $statsGenerales['nb_contrats']],
        ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:10px;padding:12px 16px;text-align:center">
            <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#9ca3af;margin-bottom:4px"><?php echo e($s['label']); ?></div>
            <div style="font-family:'Syne',sans-serif;font-size:18px;font-weight:700;color:#0d1117"><?php echo e($s['val']); ?></div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    
    <?php if($parProprietaire->count() > 0): ?>
    <div class="table-card">
        <div class="table-hd">
            <div class="table-title">Récapitulatif par propriétaire</div>
        </div>
        <div style="overflow-x:auto">
            <table class="dt">
                <thead>
                    <tr>
                        <th>Propriétaire</th>
                        <th style="text-align:center">Paiements</th>
                        <th style="text-align:right">Total encaissé</th>
                        <th style="text-align:right">Commission TTC</th>
                        <th style="text-align:right">Net reversé</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $parProprietaire; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $nom => $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td style="font-weight:500"><?php echo e($nom); ?></td>
                        <td style="text-align:center"><?php echo e($data['nb_paiements']); ?></td>
                        <td style="text-align:right;font-weight:700;color:#c9a84c;font-family:'Syne',sans-serif">
                            <?php echo e(number_format($data['total_encaisse'], 0, ',', ' ')); ?> F
                        </td>
                        <td style="text-align:right;color:#8a6e2f">
                            <?php echo e(number_format($data['total_commission'], 0, ',', ' ')); ?> F
                        </td>
                        <td style="text-align:right;color:#16a34a;font-weight:600">
                            <?php echo e(number_format($data['total_net'], 0, ',', ' ')); ?> F
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
                <tfoot>
                    <tr style="background:#f9fafb;font-weight:700">
                        <td>Total</td>
                        <td style="text-align:center"><?php echo e($kpiMois['nb_paiements']); ?></td>
                        <td style="text-align:right;font-family:'Syne',sans-serif;color:#c9a84c">
                            <?php echo e(number_format($kpiMois['total_loyers'], 0, ',', ' ')); ?> F
                        </td>
                        <td style="text-align:right;color:#8a6e2f">
                            <?php echo e(number_format($kpiMois['total_ttc'], 0, ',', ' ')); ?> F
                        </td>
                        <td style="text-align:right;color:#16a34a">
                            <?php echo e(number_format($kpiMois['total_net_proprio'], 0, ',', ' ')); ?> F
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <?php endif; ?>

    
    <div class="table-card">
        <div class="table-hd">
            <div class="table-title">Détail des paiements</div>
            <div style="font-size:12px;color:#6b7280"><?php echo e($paiementsMois->total()); ?> paiement(s)</div>
        </div>

        <?php if($paiementsMois->isEmpty()): ?>
        <div style="padding:40px;text-align:center;color:#9ca3af;font-size:13px">
            Aucun paiement validé pour <?php echo e($debutMois->translatedFormat('F Y')); ?>.
        </div>
        <?php else: ?>
        <div style="overflow-x:auto">
            <table class="dt">
                <thead>
                    <tr>
                        <th>Référence</th>
                        <th>Bien</th>
                        <th>Locataire</th>
                        <th>Propriétaire</th>
                        <th>Mode</th>
                        <th style="text-align:right">Loyer nu</th>
                        <th style="text-align:right">Commission TTC</th>
                        <th style="text-align:right">Net proprio</th>
                        <th style="text-align:center">Quittance</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $paiementsMois; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td style="font-family:'Syne',sans-serif;font-size:11px;color:#9ca3af">
                            <?php echo e($p->reference_paiement); ?>

                        </td>
                        <td>
                            <div style="font-weight:500;color:#0d1117;font-size:12px">
                                <?php echo e($p->contrat?->bien?->reference ?? '—'); ?>

                            </div>
                            <div style="font-size:11px;color:#6b7280">
                                <?php echo e($p->contrat?->bien?->ville); ?>

                            </div>
                        </td>
                        <td style="font-size:12px"><?php echo e($p->contrat?->locataire?->name ?? '—'); ?></td>
                        <td style="font-size:12px;color:#6b7280">
                            <?php echo e($p->contrat?->bien?->proprietaire?->name ?? '—'); ?>

                        </td>
                        <td style="font-size:11px;color:#6b7280">
                            <?php
                                $modes = ['especes'=>'Espèces','virement'=>'Virement','cheque'=>'Chèque',
                                          'wave'=>'Wave','orange_money'=>'Orange Money',
                                          'free_money'=>'Free Money','e_money'=>'E-Money'];
                            ?>
                            <?php echo e($modes[$p->mode_paiement] ?? $p->mode_paiement); ?>

                        </td>
                        <td style="text-align:right;font-weight:600;color:#0d1117">
                            <?php echo e(number_format($p->loyer_nu ?? 0, 0, ',', ' ')); ?> F
                        </td>
                        <td style="text-align:right;color:#8a6e2f">
                            <?php echo e(number_format($p->commission_ttc ?? 0, 0, ',', ' ')); ?> F
                        </td>
                        <td style="text-align:right;color:#16a34a;font-weight:600">
                            <?php echo e(number_format($p->net_proprietaire ?? 0, 0, ',', ' ')); ?> F
                        </td>
                        <td style="text-align:center">
                            <a href="<?php echo e(route('admin.paiements.pdf', $p)); ?>" target="_blank"
                               style="display:inline-flex;align-items:center;justify-content:center;width:26px;height:26px;border:1px solid #e5e7eb;border-radius:6px;color:#6b7280;text-decoration:none"
                               onmouseover="this.style.borderColor='#c9a84c';this.style.color='#8a6e2f'"
                               onmouseout="this.style.borderColor='#e5e7eb';this.style.color='#6b7280'">
                                <svg style="width:12px;height:12px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>

        
        <?php if($paiementsMois->hasPages()): ?>
        <div style="display:flex;justify-content:space-between;align-items:center;padding:14px 18px;border-top:1px solid #f3f4f6">
            <div style="font-size:12px;color:#6b7280">
                <?php echo e($paiementsMois->firstItem()); ?>–<?php echo e($paiementsMois->lastItem()); ?> sur <?php echo e($paiementsMois->total()); ?>

            </div>
            <div style="display:flex;gap:4px">
                <?php if(!$paiementsMois->onFirstPage()): ?>
                    <a href="<?php echo e($paiementsMois->previousPageUrl()); ?>" style="display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border:1px solid #e5e7eb;border-radius:7px;color:#6b7280;text-decoration:none">
                        <svg style="width:13px;height:13px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                    </a>
                <?php endif; ?>
                <?php if($paiementsMois->hasMorePages()): ?>
                    <a href="<?php echo e($paiementsMois->nextPageUrl()); ?>" style="display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border:1px solid #e5e7eb;border-radius:7px;color:#6b7280;text-decoration:none">
                        <svg style="width:13px;height:13px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
        <?php endif; ?>
    </div>

    
    <?php if(isset($biensImpayés) && $biensImpayés->count() > 0): ?>
    <div class="table-card">
        <div class="table-hd">
            <div class="table-title" style="color:#dc2626">
                Impayés — <?php echo e($biensImpayés->count()); ?> contrat(s)
            </div>
        </div>
        <div style="overflow-x:auto">
            <table class="dt">
                <thead>
                    <tr>
                        <th>Bien</th>
                        <th>Locataire</th>
                        <th>Propriétaire</th>
                        <th style="text-align:right">Loyer dû</th>
                        <th style="text-align:center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $biensImpayés; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td style="font-weight:500"><?php echo e($c->bien?->reference ?? '—'); ?></td>
                        <td><?php echo e($c->locataire?->name ?? '—'); ?></td>
                        <td style="color:#6b7280"><?php echo e($c->bien?->proprietaire?->name ?? '—'); ?></td>
                        <td style="text-align:right;color:#dc2626;font-weight:700;font-family:'Syne',sans-serif">
                            <?php echo e(number_format($c->loyer_contractuel, 0, ',', ' ')); ?> F
                        </td>
                        <td style="text-align:center">
                            <a href="<?php echo e(route('admin.paiements.create', ['contrat_id' => $c->id])); ?>"
                               style="display:inline-flex;align-items:center;gap:4px;padding:5px 10px;background:#dcfce7;color:#16a34a;border:1px solid #bbf7d0;border-radius:6px;font-size:11px;font-weight:600;text-decoration:none">
                                + Paiement
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php else: ?>
    <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:16px 20px;display:flex;align-items:center;gap:10px">
        <svg style="width:18px;height:18px;color:#16a34a;flex-shrink:0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
        <span style="font-size:13px;color:#16a34a;font-weight:500">
            Aucun impayé ce mois — Taux de recouvrement 100%
        </span>
    </div>
    <?php endif; ?>

</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ph\bimotech-immo\resources\views/rapports/financier.blade.php ENDPATH**/ ?>