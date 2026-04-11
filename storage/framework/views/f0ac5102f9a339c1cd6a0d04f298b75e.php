
<?php $__env->startSection('title', 'Paiements'); ?>
<?php $__env->startSection('breadcrumb', 'Paiements'); ?>

<?php $__env->startSection('content'); ?>
<style>
.kpi-row { display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:22px; }
.kpi { background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:16px 18px; }
.kpi.gold  { border-top:3px solid #c9a84c; }
.kpi.green { border-top:3px solid #16a34a; }
.kpi.blue  { border-top:3px solid #1d4ed8; }
.kpi.dark  { border-top:3px solid #0d1117; }
.kpi-lbl { font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#9ca3af;margin-bottom:6px; }
.kpi-val { font-family:'Syne',sans-serif;font-size:20px;font-weight:700;color:#0d1117;line-height:1.1; }
.kpi-sub { font-size:11px;color:#9ca3af;margin-top:4px; }

.table-card { background:#fff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden; }
.dt { width:100%;border-collapse:collapse; }
.dt th { padding:10px 16px;text-align:left;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.7px;color:#9ca3af;background:#f9fafb;border-bottom:1px solid #e5e7eb; }
.dt td { padding:12px 16px;font-size:13px;color:#374151;border-bottom:1px solid #f3f4f6;vertical-align:middle; }
.dt tbody tr:last-child td { border-bottom:none; }
.dt tbody tr:hover { background:#fafafa; }

.badge { display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:99px;font-size:11px;font-weight:600; }
.badge-valide  { background:#dcfce7;color:#16a34a; }
.badge-annulé  { background:#fee2e2;color:#dc2626; }
.badge-attente { background:#fef9c3;color:#a16207; }

.act-btn { display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:7px;border:1px solid #e5e7eb;background:#fff;color:#6b7280;text-decoration:none;transition:all .15s;cursor:pointer; }
.act-btn:hover { border-color:#c9a84c;color:#8a6e2f; }
.act-btn.danger:hover { border-color:#fca5a5;color:#dc2626;background:#fef2f2; }

.empty-state { padding:56px;text-align:center; }
</style>

<div style="padding:0 0 48px">

    
    <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:22px;flex-wrap:wrap;gap:12px">
        <div>
            <h1 style="font-family:'Syne',sans-serif;font-size:22px;font-weight:700;color:#0d1117;letter-spacing:-.4px">Paiements</h1>
            <p style="font-size:13px;color:#6b7280;margin-top:3px">
                <?php echo e(now()->translatedFormat('F Y')); ?> — <?php echo e($stats['nb_payes']); ?> paiement(s) validé(s)
            </p>
        </div>
        <a href="<?php echo e(route('admin.paiements.create')); ?>"
           style="display:inline-flex;align-items:center;gap:6px;padding:9px 18px;background:#c9a84c;color:#0d1117;font-family:'DM Sans',sans-serif;font-size:13px;font-weight:600;border-radius:8px;text-decoration:none"
           onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
            <svg style="width:14px;height:14px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Nouveau paiement
        </a>
    </div>

    
    <div class="kpi-row">
        <div class="kpi gold">
            <div class="kpi-lbl">Loyers encaissés</div>
            <div class="kpi-val"><?php echo e(number_format($stats['total_loyers'], 0, ',', ' ')); ?></div>
            <div class="kpi-sub">FCFA ce mois</div>
        </div>
        <div class="kpi green">
            <div class="kpi-lbl">Net propriétaires</div>
            <div class="kpi-val" style="color:#16a34a"><?php echo e(number_format($stats['total_net'], 0, ',', ' ')); ?></div>
            <div class="kpi-sub">FCFA à reverser</div>
        </div>
        <div class="kpi blue">
            <div class="kpi-lbl">Commissions TTC</div>
            <div class="kpi-val" style="color:#1d4ed8"><?php echo e(number_format($stats['total_commissions'], 0, ',', ' ')); ?></div>
            <div class="kpi-sub">FCFA agence</div>
        </div>
        <div class="kpi dark">
            <div class="kpi-lbl">Paiements validés</div>
            <div class="kpi-val"><?php echo e($stats['nb_payes']); ?></div>
            <div class="kpi-sub">Ce mois</div>
        </div>
    </div>

    
    <form method="GET" style="display:flex;gap:8px;margin-bottom:16px;flex-wrap:wrap;align-items:center">
        <input type="month" name="mois" value="<?php echo e(request('mois')); ?>"
               style="padding:8px 12px;border:1px solid #e5e7eb;border-radius:8px;font-size:13px;font-family:'DM Sans',sans-serif"
               onchange="this.form.submit()">
        <select name="statut" onchange="this.form.submit()"
                style="padding:8px 12px;border:1px solid #e5e7eb;border-radius:8px;font-size:13px;font-family:'DM Sans',sans-serif;cursor:pointer">
            <option value="">Tous les statuts</option>
            <option value="valide"  <?php if(request('statut')==='valide'): echo 'selected'; endif; ?>>Validé</option>
            <option value="annulé"  <?php if(request('statut')==='annulé'): echo 'selected'; endif; ?>>Annulé</option>
        </select>
        <?php if(request()->hasAny(['mois','statut','contrat_id'])): ?>
            <a href="<?php echo e(route('admin.paiements.index')); ?>"
               style="padding:8px 14px;border:1px solid #e5e7eb;border-radius:8px;font-size:13px;color:#6b7280;text-decoration:none;background:#fff">
                Effacer
            </a>
        <?php endif; ?>
    </form>

    
    <div class="table-card">
        <?php if($paiements->isEmpty()): ?>
            <div class="empty-state">
                <div style="width:48px;height:48px;background:#f5e9c9;border-radius:12px;display:flex;align-items:center;justify-content:center;margin:0 auto 14px">
                    <svg style="width:22px;height:22px;color:#8a6e2f" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                </div>
                <div style="font-family:'Syne',sans-serif;font-size:15px;font-weight:700;color:#0d1117;margin-bottom:6px">
                    Aucun paiement trouvé
                </div>
                <div style="font-size:13px;color:#6b7280;margin-bottom:16px">
                    <?php if(request()->hasAny(['mois','statut'])): ?>
                        Aucun résultat pour ces filtres.
                    <?php else: ?>
                        Enregistrez le premier paiement de loyer.
                    <?php endif; ?>
                </div>
                <a href="<?php echo e(route('admin.paiements.create')); ?>"
                   style="display:inline-flex;align-items:center;gap:6px;padding:9px 18px;background:#c9a84c;color:#0d1117;font-size:13px;font-weight:600;border-radius:8px;text-decoration:none">
                    + Enregistrer un paiement
                </a>
            </div>
        <?php else: ?>
        <div style="overflow-x:auto">
            <table class="dt">
                <thead>
                    <tr>
                        <th>Référence</th>
                        <th>Bien / Locataire</th>
                        <th>Période</th>
                        <th>Date paiement</th>
                        <th style="text-align:right">Montant</th>
                        <th style="text-align:right">Commission</th>
                        <th style="text-align:right">Net proprio</th>
                        <th>Mode</th>
                        <th style="text-align:center">Statut</th>
                        <th style="text-align:center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $paiements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td style="font-family:'Syne',sans-serif;font-size:11px;color:#9ca3af">
                            <?php echo e($p->reference_paiement); ?>

                        </td>
                        <td>
                            <div style="font-size:13px;font-weight:500;color:#0d1117">
                                <?php echo e($p->contrat?->bien?->reference ?? '—'); ?>

                            </div>
                            <div style="font-size:11px;color:#6b7280">
                                <?php echo e($p->contrat?->locataire?->name ?? '—'); ?>

                            </div>
                        </td>
                        <td>
                            <span style="display:inline-flex;padding:3px 9px;background:#f5e9c9;color:#8a6e2f;border-radius:99px;font-size:11px;font-weight:600;font-family:'Syne',sans-serif">
                                <?php echo e(\Carbon\Carbon::parse($p->periode)->translatedFormat('M Y')); ?>

                            </span>
                        </td>
                        <td style="font-size:12px;color:#6b7280">
                            <?php echo e($p->date_paiement ? \Carbon\Carbon::parse($p->date_paiement)->format('d/m/Y') : '—'); ?>

                        </td>
                        <td style="text-align:right;font-family:'Syne',sans-serif;font-weight:700;color:#0d1117">
                            <?php echo e(number_format($p->montant_encaisse, 0, ',', ' ')); ?> F
                        </td>
                        <td style="text-align:right;color:#8a6e2f;font-size:12px">
                            <?php echo e(number_format($p->commission_ttc ?? 0, 0, ',', ' ')); ?> F
                        </td>
                        <td style="text-align:right;color:#16a34a;font-weight:600">
                            <?php echo e(number_format($p->net_proprietaire ?? 0, 0, ',', ' ')); ?> F
                        </td>
                        <td style="font-size:12px;color:#6b7280">
                            <?php echo e(\App\Http\Controllers\PaiementController::MODES_PAIEMENT[$p->mode_paiement] ?? $p->mode_paiement); ?>

                        </td>
                        <td style="text-align:center">
                            <span class="badge badge-<?php echo e($p->statut); ?>">
                                <?php echo e(ucfirst($p->statut)); ?>

                            </span>
                        </td>
                        <td>
                            <div style="display:flex;align-items:center;justify-content:center;gap:4px">
                                <a href="<?php echo e(route('admin.paiements.show', $p)); ?>" class="act-btn" title="Voir">
                                    <svg style="width:13px;height:13px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                </a>
                                <a href="<?php echo e(route('admin.paiements.pdf', $p)); ?>" target="_blank" class="act-btn" title="PDF">
                                    <svg style="width:13px;height:13px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                </a>
                                <?php if($p->statut === 'valide'): ?>
                                <form method="POST" action="<?php echo e(route('admin.paiements.annuler', $p)); ?>"
                                      onsubmit="return confirm('Annuler ce paiement ?')">
                                    <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                    <button type="submit" class="act-btn danger" title="Annuler">
                                        <svg style="width:13px;height:13px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                                    </button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>

        
        <?php if($paiements->hasPages()): ?>
        <div style="display:flex;justify-content:space-between;align-items:center;padding:14px 18px;border-top:1px solid #f3f4f6">
            <div style="font-size:12px;color:#6b7280">
                <?php echo e($paiements->firstItem()); ?>–<?php echo e($paiements->lastItem()); ?> sur <?php echo e($paiements->total()); ?>

            </div>
            <div style="display:flex;gap:4px">
                <?php if(!$paiements->onFirstPage()): ?>
                    <a href="<?php echo e($paiements->previousPageUrl()); ?>" style="display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border:1px solid #e5e7eb;border-radius:7px;color:#6b7280;text-decoration:none">
                        <svg style="width:13px;height:13px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                    </a>
                <?php endif; ?>
                <?php $__currentLoopData = $paiements->getUrlRange(max(1,$paiements->currentPage()-2), min($paiements->lastPage(),$paiements->currentPage()+2)); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e($url); ?>" style="display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border:1px solid <?php echo e($page===$paiements->currentPage() ? '#0d1117':'#e5e7eb'); ?>;border-radius:7px;font-size:12px;color:<?php echo e($page===$paiements->currentPage() ? '#fff':'#374151'); ?>;background:<?php echo e($page===$paiements->currentPage() ? '#0d1117':'#fff'); ?>;text-decoration:none">
                        <?php echo e($page); ?>

                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php if($paiements->hasMorePages()): ?>
                    <a href="<?php echo e($paiements->nextPageUrl()); ?>" style="display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border:1px solid #e5e7eb;border-radius:7px;color:#6b7280;text-decoration:none">
                        <svg style="width:13px;height:13px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
        <?php endif; ?>
    </div>

</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ph\bimotech-immo\resources\views/paiements/index.blade.php ENDPATH**/ ?>