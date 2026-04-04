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
     <?php $__env->slot('header', null, []); ?> Paiements <?php $__env->endSlot(); ?>

<style>
/* ── KPI MINI ── */
.kpi-row { display:grid; grid-template-columns:repeat(4,1fr); gap:14px; margin-bottom:22px; }
.kpi-mini { background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:18px 20px; position:relative; overflow:hidden; }
.kpi-mini::before { content:''; position:absolute; top:0;left:0;right:0; height:3px; border-radius:12px 12px 0 0; }
.kpi-mini.gold::before  { background:#c9a84c; }
.kpi-mini.green::before { background:#16a34a; }
.kpi-mini.blue::before  { background:#1d4ed8; }
.kpi-mini.red::before   { background:#dc2626; }
.kpi-lbl { font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:.8px;color:#6b7280;margin-bottom:5px; }
.kpi-val { font-family:'Syne',sans-serif;font-size:20px;font-weight:700;color:#0d1117;letter-spacing:-.3px;line-height:1; }
.kpi-u   { font-size:11px;font-weight:400;color:#9ca3af;margin-left:2px; }
.kpi-s   { font-size:11px;color:#9ca3af;margin-top:5px; }

/* ── FILTRES ── */
.filter-bar {
    background:#fff; border:1px solid #e5e7eb; border-radius:12px;
    padding:14px 18px; margin-bottom:18px;
    display:flex; align-items:center; gap:10px; flex-wrap:wrap;
}
.filter-input {
    padding:8px 13px; border:1px solid #e5e7eb; border-radius:8px;
    font-size:13px; color:#0d1117; font-family:'DM Sans',sans-serif;
    background:#f9fafb; outline:none; transition:border-color .15s;
    min-width:0;
}
.filter-input:focus { border-color:#c9a84c; background:#fff; }
.filter-select {
    padding:8px 13px; border:1px solid #e5e7eb; border-radius:8px;
    font-size:13px; color:#0d1117; font-family:'DM Sans',sans-serif;
    background:#f9fafb; outline:none; cursor:pointer;
}
.filter-btn {
    padding:8px 16px; background:#0d1117; color:#fff;
    border:none; border-radius:8px; font-size:12px; font-weight:500;
    font-family:'DM Sans',sans-serif; cursor:pointer; white-space:nowrap;
}
.filter-reset {
    padding:8px 14px; background:none; color:#6b7280;
    border:1px solid #e5e7eb; border-radius:8px; font-size:12px;
    font-family:'DM Sans',sans-serif; cursor:pointer; text-decoration:none;
    display:inline-flex; align-items:center; gap:5px; white-space:nowrap;
}
.filter-spacer { flex:1; }

/* ── TABLE CARD ── */
.table-card { background:#fff; border:1px solid #e5e7eb; border-radius:14px; overflow:hidden; }
.table-header { padding:18px 22px; border-bottom:1px solid #e5e7eb; display:flex; align-items:center; justify-content:space-between; }
.table-title { font-family:'Syne',sans-serif; font-size:14px; font-weight:700; color:#0d1117; }
.table-count { font-size:12px; color:#6b7280; margin-top:2px; }

.dt { width:100%; border-collapse:collapse; }
.dt thead tr { background:#f9fafb; }
.dt th {
    padding:10px 18px; text-align:left;
    font-size:10px; font-weight:700; text-transform:uppercase;
    letter-spacing:.8px; color:#9ca3af;
    border-bottom:1px solid #e5e7eb;
    white-space:nowrap;
}
.dt td {
    padding:14px 18px; font-size:13px; color:#374151;
    border-bottom:1px solid #f3f4f6; vertical-align:middle;
}
.dt tbody tr:last-child td { border-bottom:none; }
.dt tbody tr { transition:background .1s; }
.dt tbody tr:hover { background:#f9fafb; }

/* colonnes */
.th-r { text-align:right !important; }
.td-r  { text-align:right; }
.td-c  { text-align:center; }

.ref-tag { font-family:'Syne',sans-serif; font-size:11px; font-weight:600; color:#9ca3af; letter-spacing:.3px; }
.bien-ref { font-size:13px; font-weight:500; color:#0d1117; }
.bien-sub { font-size:11px; color:#6b7280; margin-top:1px; }
.periode-tag { display:inline-block; padding:3px 9px; background:#f5e9c9; color:#8a6e2f; border-radius:6px; font-size:11px; font-weight:600; font-family:'Syne',sans-serif; }
.mode-tag { font-size:12px; color:#6b7280; }
.montant { font-family:'Syne',sans-serif; font-weight:600; color:#0d1117; }
.net { font-family:'Syne',sans-serif; font-weight:600; color:#16a34a; }
.commission { font-size:11px; color:#9ca3af; margin-top:1px; }

/* badges */
.badge { display:inline-flex; align-items:center; gap:4px; padding:3px 9px; border-radius:99px; font-size:11px; font-weight:600; white-space:nowrap; }
.badge.g { background:#dcfce7; color:#16a34a; }
.badge.r { background:#fee2e2; color:#dc2626; }
.badge.o { background:#f5e9c9; color:#8a6e2f; }
.badge.gray { background:#f3f4f6; color:#6b7280; }
.bdot { width:5px; height:5px; border-radius:50%; background:currentColor; }

/* actions */
.act-btn {
    display:inline-flex; align-items:center; justify-content:center;
    width:30px; height:30px; border-radius:7px;
    border:1px solid #e5e7eb; background:#fff;
    color:#6b7280; text-decoration:none; transition:all .15s;
    cursor:pointer;
}
.act-btn:hover { border-color:#c9a84c; color:#8a6e2f; background:#f5e9c9; }
.act-btn svg { width:14px; height:14px; }
.act-btn.danger:hover { border-color:#dc2626; color:#dc2626; background:#fee2e2; }

/* état vide */
.empty-state { padding:56px 20px; text-align:center; }
.empty-icon { width:56px; height:56px; border-radius:14px; background:#f5e9c9; display:flex; align-items:center; justify-content:center; margin:0 auto 16px; }
.empty-icon svg { width:24px; height:24px; color:#8a6e2f; }
.empty-title { font-family:'Syne',sans-serif; font-size:15px; font-weight:700; color:#0d1117; margin-bottom:6px; }
.empty-sub { font-size:13px; color:#6b7280; }

/* pagination */
.pagination-wrap { padding:16px 22px; border-top:1px solid #e5e7eb; display:flex; align-items:center; justify-content:space-between; }
.pagination-info { font-size:12px; color:#6b7280; }
.pagination-links { display:flex; gap:4px; }
.page-btn { display:inline-flex; align-items:center; justify-content:center; min-width:32px; height:32px; padding:0 10px; border-radius:7px; border:1px solid #e5e7eb; background:#fff; color:#374151; font-size:12px; font-weight:500; text-decoration:none; transition:all .15s; }
.page-btn:hover { background:#f9fafb; border-color:#d1d5db; }
.page-btn.active { background:#0d1117; color:#fff; border-color:#0d1117; }
.page-btn.disabled { opacity:.4; pointer-events:none; }
</style>

<div style="padding:24px 32px 48px">

    
    <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:22px">
        <div>
            <h1 style="font-family:'Syne',sans-serif;font-size:22px;font-weight:700;color:#0d1117;letter-spacing:-.4px">Paiements</h1>
            <p style="font-size:13px;color:#6b7280;margin-top:3px">
                Historique complet des loyers encaissés
            </p>
        </div>
        <a href="<?php echo e(route('admin.paiements.create')); ?>" class="btn-primary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Nouveau paiement
        </a>
    </div>

    
    <?php
        $totalEncaisse  = $paiements->sum('montant_encaisse');
        $totalNet       = $paiements->sum('net_proprietaire');
        $totalCommission= $paiements->sum('commission_ttc');
        $nbValides      = $paiements->where('statut','valide')->count();
    ?>
    <div class="kpi-row">
        <div class="kpi-mini gold">
            <div class="kpi-lbl">Encaissé (page)</div>
            <div class="kpi-val"><?php echo e(number_format($totalEncaisse, 0, ',', ' ')); ?><span class="kpi-u">F</span></div>
            <div class="kpi-s"><?php echo e($paiements->count()); ?> paiements affichés</div>
        </div>
        <div class="kpi-mini green">
            <div class="kpi-lbl">Net propriétaires</div>
            <div class="kpi-val"><?php echo e(number_format($totalNet, 0, ',', ' ')); ?><span class="kpi-u">F</span></div>
            <div class="kpi-s">Après commission agence</div>
        </div>
        <div class="kpi-mini blue">
            <div class="kpi-lbl">Commission agence</div>
            <div class="kpi-val"><?php echo e(number_format($totalCommission, 0, ',', ' ')); ?><span class="kpi-u">F</span></div>
            <div class="kpi-s">TTC · TVA 18% incluse</div>
        </div>
        <div class="kpi-mini red">
            <div class="kpi-lbl">Paiements validés</div>
            <div class="kpi-val"><?php echo e($nbValides); ?><span class="kpi-u">/ <?php echo e($paiements->count()); ?></span></div>
            <div class="kpi-s">Sur cette page</div>
        </div>
    </div>

    
    <form method="GET" action="<?php echo e(route('admin.paiements.index')); ?>">
        <div class="filter-bar">
            
            <div style="position:relative;flex:1;min-width:180px">
                <svg style="position:absolute;left:10px;top:50%;transform:translateY(-50%);width:14px;height:14px;color:#9ca3af" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" name="q" value="<?php echo e(request('q')); ?>" placeholder="Référence, locataire, bien…" class="filter-input" style="padding-left:34px;width:100%">
            </div>

            
            <select name="statut" class="filter-select">
                <option value="">Tous les statuts</option>
                <option value="valide"  <?php echo e(request('statut') === 'valide'  ? 'selected' : ''); ?>>Validés</option>
                <option value="annule"  <?php echo e(request('statut') === 'annule'  ? 'selected' : ''); ?>>Annulés</option>
                <option value="en_attente" <?php echo e(request('statut') === 'en_attente' ? 'selected' : ''); ?>>En attente</option>
            </select>

            
            <select name="mode" class="filter-select">
                <option value="">Tous les modes</option>
                <?php $__currentLoopData = \App\Models\Paiement::MODES_PAIEMENT; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($key); ?>" <?php echo e(request('mode') === $key ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>

            
            <input type="month" name="periode" value="<?php echo e(request('periode')); ?>" class="filter-input" style="width:150px">

            <button type="submit" class="filter-btn">Filtrer</button>

            <?php if(request()->hasAny(['q','statut','mode','periode'])): ?>
                <a href="<?php echo e(route('admin.paiements.index')); ?>" class="filter-reset">
                    <svg style="width:12px;height:12px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    Effacer
                </a>
            <?php endif; ?>

            <div class="filter-spacer"></div>

            
            <a href="<?php echo e(route('admin.rapports.financier.export-pdf')); ?>" class="filter-reset">
                <svg style="width:12px;height:12px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Exporter PDF
            </a>
        </div>
    </form>

    
    <div class="table-card">
        <div class="table-header">
            <div>
                <div class="table-title">Historique des paiements</div>
                <div class="table-count"><?php echo e($paiements->total()); ?> paiement(s) au total · Page <?php echo e($paiements->currentPage()); ?> / <?php echo e($paiements->lastPage()); ?></div>
            </div>
            <div style="display:flex;align-items:center;gap:8px">
                <span style="font-size:12px;color:#6b7280">Trier par</span>
                <select class="filter-select" style="font-size:12px;padding:6px 10px" onchange="window.location=this.value">
                    <option value="<?php echo e(request()->fullUrlWithQuery(['sort'=>'date'])); ?>" <?php echo e(request('sort','date') === 'date' ? 'selected':''); ?>>Date</option>
                    <option value="<?php echo e(request()->fullUrlWithQuery(['sort'=>'montant'])); ?>" <?php echo e(request('sort') === 'montant' ? 'selected':''); ?>>Montant</option>
                    <option value="<?php echo e(request()->fullUrlWithQuery(['sort'=>'periode'])); ?>" <?php echo e(request('sort') === 'periode' ? 'selected':''); ?>>Période</option>
                </select>
            </div>
        </div>

        <div style="overflow-x:auto">
            <table class="dt">
                <thead>
                    <tr>
                        <th>Référence</th>
                        <th>Bien & Locataire</th>
                        <th>Période</th>
                        <th>Date paiement</th>
                        <th>Mode</th>
                        <th class="th-r">Montant encaissé</th>
                        <th class="th-r">Net propriétaire</th>
                        <th style="text-align:center">Statut</th>
                        <th style="text-align:center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $paiements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $statusMap = [
                            'valide'     => ['label' => 'Validé',     'class' => 'g'],
                            'annule'     => ['label' => 'Annulé',     'class' => 'r'],
                            'en_attente' => ['label' => 'En attente', 'class' => 'o'],
                            'impaye'     => ['label' => 'Impayé',     'class' => 'r'],
                        ];
                        $s = $statusMap[$p->statut] ?? ['label' => $p->statut, 'class' => 'gray'];
                    ?>
                    <tr>
                        
                        <td>
                            <span class="ref-tag"><?php echo e($p->reference_paiement); ?></span>
                        </td>

                        
                        <td>
                            <div class="bien-ref"><?php echo e($p->contrat?->bien?->reference ?? '—'); ?></div>
                            <div class="bien-sub"><?php echo e($p->contrat?->locataire?->name ?? '—'); ?></div>
                            <?php if($p->contrat?->bien?->ville): ?>
                                <div class="bien-sub"><?php echo e($p->contrat->bien->ville); ?></div>
                            <?php endif; ?>
                        </td>

                        
                        <td>
                            <span class="periode-tag">
                                <?php echo e(\Carbon\Carbon::parse($p->periode)->translatedFormat('M Y')); ?>

                            </span>
                        </td>

                        
                        <td>
                            <div style="font-size:13px;color:#374151"><?php echo e(\Carbon\Carbon::parse($p->date_paiement)->format('d/m/Y')); ?></div>
                        </td>

                        
                        <td>
                            <span class="mode-tag">
                                <?php echo e(\App\Models\Paiement::MODES_PAIEMENT[$p->mode_paiement] ?? $p->mode_paiement); ?>

                            </span>
                        </td>

                        
                        <td class="td-r">
                            <div class="montant"><?php echo e(number_format($p->montant_encaisse, 0, ',', ' ')); ?> F</div>
                            <?php if($p->commission_ttc): ?>
                                <div class="commission">Com. <?php echo e(number_format($p->commission_ttc, 0, ',', ' ')); ?> F</div>
                            <?php endif; ?>
                        </td>

                        
                        <td class="td-r">
                            <div class="net"><?php echo e(number_format($p->net_proprietaire, 0, ',', ' ')); ?> F</div>
                        </td>

                        
                        <td class="td-c">
                            <span class="badge <?php echo e($s['class']); ?>">
                                <span class="bdot"></span>
                                <?php echo e($s['label']); ?>

                            </span>
                        </td>

                        
                        <td class="td-c">
                            <div style="display:flex;align-items:center;justify-content:center;gap:6px">
                                
                                <a href="<?php echo e(route('admin.paiements.show', $p)); ?>" class="act-btn" title="Voir le détail">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>

                                
                                <?php if($p->statut === 'valide'): ?>
                                    <a href="<?php echo e(route('admin.paiements.pdf', $p)); ?>" target="_blank" class="act-btn" title="Télécharger quittance PDF">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                                    </a>
                                <?php endif; ?>

                                
                                <?php if($p->statut === 'valide'): ?>
                                    <form method="POST" action="<?php echo e(route('admin.paiements.annuler', $p)); ?>"
                                          onsubmit="return confirm('Annuler ce paiement ? Cette action est irréversible.')">
                                        <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                        <button type="submit" class="act-btn danger" title="Annuler le paiement">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="9">
                            <div class="empty-state">
                                <div class="empty-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
                                </div>
                                <div class="empty-title">Aucun paiement trouvé</div>
                                <div class="empty-sub">
                                    <?php if(request()->hasAny(['q','statut','mode','periode'])): ?>
                                        Aucun résultat pour ces filtres.
                                        <a href="<?php echo e(route('admin.paiements.index')); ?>" style="color:#c9a84c;font-weight:500">Effacer les filtres</a>
                                    <?php else: ?>
                                        Enregistrez le premier paiement pour commencer.
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        
        <?php if($paiements->hasPages()): ?>
        <div class="pagination-wrap">
            <div class="pagination-info">
                Affichage de <?php echo e($paiements->firstItem()); ?> à <?php echo e($paiements->lastItem()); ?> sur <?php echo e($paiements->total()); ?> résultats
            </div>
            <div class="pagination-links">
                
                <?php if($paiements->onFirstPage()): ?>
                    <span class="page-btn disabled">
                        <svg style="width:14px;height:14px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                    </span>
                <?php else: ?>
                    <a href="<?php echo e($paiements->previousPageUrl()); ?>" class="page-btn">
                        <svg style="width:14px;height:14px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                    </a>
                <?php endif; ?>

                
                <?php $__currentLoopData = $paiements->getUrlRange(max(1,$paiements->currentPage()-2), min($paiements->lastPage(),$paiements->currentPage()+2)); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e($url); ?>" class="page-btn <?php echo e($page === $paiements->currentPage() ? 'active' : ''); ?>">
                        <?php echo e($page); ?>

                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                
                <?php if($paiements->hasMorePages()): ?>
                    <a href="<?php echo e($paiements->nextPageUrl()); ?>" class="page-btn">
                        <svg style="width:14px;height:14px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                    </a>
                <?php else: ?>
                    <span class="page-btn disabled">
                        <svg style="width:14px;height:14px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                    </span>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

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
<?php endif; ?><?php /**PATH C:\Users\ph\bimotech-immo\resources\views/paiements/index.blade.php ENDPATH**/ ?>