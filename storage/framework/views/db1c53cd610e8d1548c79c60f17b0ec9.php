
<?php $__env->startSection('title', 'Impayés'); ?>
<?php $__env->startSection('breadcrumb', 'Paiements › Impayés'); ?>

<?php $__env->startSection('content'); ?>
<style>
.kpi-row { display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:22px; }
.kpi { background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:16px 18px; }
.kpi.red    { border-top:3px solid #dc2626; }
.kpi.green  { border-top:3px solid #16a34a; }
.kpi.orange { border-top:3px solid #d97706; }
.kpi.blue   { border-top:3px solid #1d4ed8; }
.kpi-lbl { font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#9ca3af;margin-bottom:6px; }
.kpi-val { font-family:'Syne',sans-serif;font-size:22px;font-weight:700;color:#0d1117; }
.kpi-sub { font-size:11px;color:#9ca3af;margin-top:3px; }

/* Barre recouvrement */
.recouvrement-bar { background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:16px 20px;margin-bottom:22px; }
.bar-track { background:#f3f4f6;border-radius:99px;height:10px;overflow:hidden;margin:8px 0; }
.bar-fill  { background:linear-gradient(90deg,#16a34a,#4ade80);height:100%;border-radius:99px;transition:width .5s; }

/* Navigation mois */
.month-nav { display:flex;align-items:center;gap:8px; }
.month-btn { display:flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:7px;border:1px solid #e5e7eb;background:#fff;cursor:pointer;color:#6b7280;text-decoration:none;transition:all .15s; }
.month-btn:hover { border-color:#c9a84c;color:#8a6e2f; }
.month-btn svg { width:14px;height:14px; }
.month-current { font-family:'Syne',sans-serif;font-size:14px;font-weight:600;color:#0d1117;min-width:130px;text-align:center; }

/* Tables */
.table-card { background:#fff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden;margin-bottom:20px; }
.table-hd { padding:14px 20px;border-bottom:1px solid #e5e7eb;display:flex;align-items:center;justify-content:space-between; }
.table-title { font-family:'Syne',sans-serif;font-size:13px;font-weight:700;color:#0d1117; }
.dt { width:100%;border-collapse:collapse; }
.dt th { padding:9px 16px;text-align:left;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.7px;color:#9ca3af;background:#f9fafb;border-bottom:1px solid #e5e7eb; }
.dt td { padding:11px 16px;font-size:13px;color:#374151;border-bottom:1px solid #f3f4f6;vertical-align:middle; }
.dt tbody tr:last-child td { border-bottom:none; }
.dt tbody tr:hover { background:#fafafa; }
.dt tbody tr.urgent td { background:#fef9f9; }
.dt tbody tr.urgent:hover td { background:#fef2f2; }

/* Badges urgence */
.urgence-haute { display:inline-flex;align-items:center;gap:4px;padding:2px 8px;background:#fee2e2;color:#dc2626;border-radius:99px;font-size:10px;font-weight:700; }
.urgence-moy   { display:inline-flex;align-items:center;gap:4px;padding:2px 8px;background:#fef9c3;color:#a16207;border-radius:99px;font-size:10px;font-weight:700; }
.urgence-bas   { display:inline-flex;align-items:center;gap:4px;padding:2px 8px;background:#f3f4f6;color:#6b7280;border-radius:99px;font-size:10px;font-weight:700; }

.act-btn { display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:7px;border:1px solid #e5e7eb;background:#fff;color:#6b7280;text-decoration:none;transition:all .15s;cursor:pointer; }
.act-btn:hover { border-color:#c9a84c;color:#8a6e2f; }
.act-btn.green:hover { border-color:#bbf7d0;color:#16a34a;background:#f0fdf4; }
.btn-relance { display:inline-flex;align-items:center;gap:5px;padding:5px 12px;background:#fef9c3;color:#a16207;border:1px solid #fde68a;border-radius:7px;font-size:11px;font-weight:600;font-family:'DM Sans',sans-serif;cursor:pointer;transition:all .15s; }
.btn-relance:hover { background:#fde68a; }
</style>

<div style="padding:0 0 48px">

    
    <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:22px;flex-wrap:wrap;gap:12px">
        <div>
            <h1 style="font-family:'Syne',sans-serif;font-size:22px;font-weight:700;color:#0d1117;letter-spacing:-.4px">
                Suivi des impayés
            </h1>
            <p style="font-size:13px;color:#6b7280;margin-top:3px">
                <?php echo e($periode->translatedFormat('F Y')); ?>

                · <?php echo e($stats['nb_impayes']); ?> impayé(s) sur <?php echo e($stats['nb_impayes'] + $stats['nb_payes']); ?> contrats actifs
            </p>
        </div>

        
        <div class="month-nav">
            <?php
                $prevMois  = $mois == 1  ? 12 : $mois - 1;
                $prevAnnee = $mois == 1  ? $annee - 1 : $annee;
                $nextMois  = $mois == 12 ? 1  : $mois + 1;
                $nextAnnee = $mois == 12 ? $annee + 1 : $annee;
            ?>
            <a href="<?php echo e(route('admin.impayes.index', ['mois' => $prevMois, 'annee' => $prevAnnee])); ?>" class="month-btn">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
            </a>
            <div class="month-current"><?php echo e($periode->translatedFormat('F Y')); ?></div>
            <?php if($nextAnnee < now()->year || ($nextAnnee == now()->year && $nextMois <= now()->month)): ?>
            <a href="<?php echo e(route('admin.impayes.index', ['mois' => $nextMois, 'annee' => $nextAnnee])); ?>" class="month-btn">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            </a>
            <?php else: ?>
            <span class="month-btn" style="opacity:.3;cursor:not-allowed">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            </span>
            <?php endif; ?>
        </div>
    </div>

    
    <div class="kpi-row">
        <div class="kpi red">
            <div class="kpi-lbl">Impayés</div>
            <div class="kpi-val" style="color:#dc2626"><?php echo e($stats['nb_impayes']); ?></div>
            <div class="kpi-sub">Contrats sans paiement</div>
        </div>
        <div class="kpi green">
            <div class="kpi-lbl">Payés</div>
            <div class="kpi-val" style="color:#16a34a"><?php echo e($stats['nb_payes']); ?></div>
            <div class="kpi-sub">Paiements validés</div>
        </div>
        <div class="kpi orange">
            <div class="kpi-lbl">Montant dû</div>
            <div class="kpi-val" style="font-size:16px;color:#d97706">
                <?php echo e(number_format($stats['montant_du'], 0, ',', ' ')); ?>

            </div>
            <div class="kpi-sub">FCFA à recouvrer</div>
        </div>
        <div class="kpi blue">
            <div class="kpi-lbl">Taux recouvrement</div>
            <div class="kpi-val" style="color:#1d4ed8"><?php echo e($stats['taux_recouvrement']); ?>%</div>
            <div class="kpi-sub">Ce mois</div>
        </div>
    </div>

    
    <div class="recouvrement-bar">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px">
            <span style="font-size:12px;font-weight:600;color:#0d1117">Taux de recouvrement</span>
            <span style="font-family:'Syne',sans-serif;font-size:14px;font-weight:700;color:<?php echo e($stats['taux_recouvrement'] >= 80 ? '#16a34a' : ($stats['taux_recouvrement'] >= 50 ? '#d97706' : '#dc2626')); ?>">
                <?php echo e($stats['taux_recouvrement']); ?>%
            </span>
        </div>
        <div class="bar-track">
            <div class="bar-fill" style="width:<?php echo e($stats['taux_recouvrement']); ?>%;background:<?php echo e($stats['taux_recouvrement'] >= 80 ? 'linear-gradient(90deg,#16a34a,#4ade80)' : ($stats['taux_recouvrement'] >= 50 ? 'linear-gradient(90deg,#d97706,#fbbf24)' : 'linear-gradient(90deg,#dc2626,#f87171)')); ?>"></div>
        </div>
        <div style="display:flex;justify-content:space-between;font-size:11px;color:#9ca3af">
            <span><?php echo e($stats['nb_payes']); ?> payés</span>
            <span><?php echo e($stats['nb_impayes']); ?> impayés</span>
        </div>
    </div>

    
    <?php if($impayes->isNotEmpty()): ?>
    <div class="table-card">
        <div class="table-hd">
            <div class="table-title" style="color:#dc2626">
                <svg style="width:14px;height:14px;display:inline;margin-right:4px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                Impayés — <?php echo e($impayes->count()); ?> contrat(s)
            </div>
            <span style="font-size:11px;color:#9ca3af">Triés par retard décroissant</span>
        </div>
        <div style="overflow-x:auto">
            <table class="dt">
                <thead>
                    <tr>
                        <th>Bien</th>
                        <th>Locataire</th>
                        <th>Propriétaire</th>
                        <th style="text-align:right">Loyer dû</th>
                        <th style="text-align:center">Retard</th>
                        <th style="text-align:center">Urgence</th>
                        <th style="text-align:center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $impayes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $jr = $item['jours_retard'];
                        $urgent = $jr > 15;
                    ?>
                    <tr class="<?php echo e($urgent ? 'urgent' : ''); ?>">
                        <td>
                            <div style="font-weight:500;color:#0d1117">
                                <?php echo e($item['contrat']->bien?->reference ?? '—'); ?>

                            </div>
                            <div style="font-size:11px;color:#6b7280">
                                <?php echo e($item['contrat']->bien?->adresse); ?>, <?php echo e($item['contrat']->bien?->ville); ?>

                            </div>
                        </td>
                        <td>
                            <div style="font-size:13px;color:#0d1117">
                                <?php echo e($item['contrat']->locataire?->name ?? '—'); ?>

                            </div>
                            <div style="font-size:11px;color:#6b7280">
                                <?php echo e($item['contrat']->locataire?->telephone ?? $item['contrat']->locataire?->email ?? ''); ?>

                            </div>
                        </td>
                        <td style="font-size:12px;color:#6b7280">
                            <?php echo e($item['contrat']->bien?->proprietaire?->name ?? '—'); ?>

                        </td>
                        <td style="text-align:right;font-family:'Syne',sans-serif;font-weight:700;color:#dc2626">
                            <?php echo e(number_format($item['montant_du'], 0, ',', ' ')); ?> F
                        </td>
                        <td style="text-align:center">
                            <?php if($jr > 0): ?>
                                <span style="font-family:'Syne',sans-serif;font-size:13px;font-weight:700;color:<?php echo e($jr > 15 ? '#dc2626' : ($jr > 7 ? '#d97706' : '#6b7280')); ?>">
                                    <?php echo e($jr); ?>j
                                </span>
                            <?php else: ?>
                                <span style="font-size:11px;color:#9ca3af">Ce mois</span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align:center">
                            <?php if($jr > 15): ?>
                                <span class="urgence-haute">🔴 Haute</span>
                            <?php elseif($jr > 7): ?>
                                <span class="urgence-moy">🟡 Moyenne</span>
                            <?php else: ?>
                                <span class="urgence-bas">⚪ Faible</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div style="display:flex;align-items:center;justify-content:center;gap:4px">
                                
                                <a href="<?php echo e(route('admin.paiements.create', ['contrat_id' => $item['contrat']->id])); ?>"
                                   class="act-btn green" title="Enregistrer le paiement">
                                    <svg style="width:13px;height:13px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                </a>
                                
                                <a href="<?php echo e(route('admin.contrats.show', $item['contrat'])); ?>"
                                   class="act-btn" title="Voir le contrat">
                                    <svg style="width:13px;height:13px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                </a>
                                
                                <form method="POST"
                                      action="<?php echo e(route('admin.impayes.relance', $item['contrat'])); ?>">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="mois" value="<?php echo e($mois); ?>">
                                    <input type="hidden" name="annee" value="<?php echo e($annee); ?>">
                                    <button type="submit" class="btn-relance" title="Envoyer relance email"
                                            onclick="return confirm('Envoyer une relance à <?php echo e($item['contrat']->locataire?->name); ?> ?')">
                                        <svg style="width:11px;height:11px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                                        Relancer
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php else: ?>
    <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:12px;padding:32px;text-align:center;margin-bottom:20px">
        <svg style="width:36px;height:36px;color:#16a34a;margin:0 auto 12px;display:block" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
        <div style="font-family:'Syne',sans-serif;font-size:15px;font-weight:700;color:#16a34a;margin-bottom:4px">
            Aucun impayé ce mois !
        </div>
        <div style="font-size:13px;color:#6b7280">
            Tous les loyers de <?php echo e($periode->translatedFormat('F Y')); ?> ont été réglés.
        </div>
    </div>
    <?php endif; ?>

    
    <?php if($payes->isNotEmpty()): ?>
    <div class="table-card">
        <div class="table-hd">
            <div class="table-title" style="color:#16a34a">
                <svg style="width:14px;height:14px;display:inline;margin-right:4px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                Payés — <?php echo e($payes->count()); ?> contrat(s)
            </div>
        </div>
        <div style="overflow-x:auto">
            <table class="dt">
                <thead>
                    <tr>
                        <th>Bien</th>
                        <th>Locataire</th>
                        <th>Date paiement</th>
                        <th style="text-align:right">Montant</th>
                        <th>Mode</th>
                        <th style="text-align:center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $payes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td>
                            <div style="font-weight:500;color:#0d1117">
                                <?php echo e($item['contrat']->bien?->reference ?? '—'); ?>

                            </div>
                            <div style="font-size:11px;color:#6b7280"><?php echo e($item['contrat']->bien?->ville); ?></div>
                        </td>
                        <td style="font-size:13px;color:#0d1117">
                            <?php echo e($item['contrat']->locataire?->name ?? '—'); ?>

                        </td>
                        <td style="font-size:12px;color:#6b7280">
                            <?php echo e($item['paiement']->date_paiement
                                ? \Carbon\Carbon::parse($item['paiement']->date_paiement)->format('d/m/Y')
                                : '—'); ?>

                        </td>
                        <td style="text-align:right;font-family:'Syne',sans-serif;font-weight:700;color:#16a34a">
                            <?php echo e(number_format($item['paiement']->montant_encaisse, 0, ',', ' ')); ?> F
                        </td>
                        <td style="font-size:12px;color:#6b7280">
                            <?php
                                $modes = ['especes'=>'Espèces','virement'=>'Virement','cheque'=>'Chèque',
                                          'wave'=>'Wave','orange_money'=>'Orange Money',
                                          'free_money'=>'Free Money','e_money'=>'E-Money'];
                            ?>
                            <?php echo e($modes[$item['paiement']->mode_paiement] ?? $item['paiement']->mode_paiement); ?>

                        </td>
                        <td>
                            <div style="display:flex;align-items:center;justify-content:center;gap:4px">
                                <a href="<?php echo e(route('admin.paiements.show', $item['paiement'])); ?>"
                                   class="act-btn" title="Voir le paiement">
                                    <svg style="width:13px;height:13px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                </a>
                                <a href="<?php echo e(route('admin.paiements.pdf', $item['paiement'])); ?>"
                                   target="_blank" class="act-btn" title="Quittance PDF">
                                    <svg style="width:13px;height:13px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ph\bimotech-immo\resources\views/impayes/index.blade.php ENDPATH**/ ?>