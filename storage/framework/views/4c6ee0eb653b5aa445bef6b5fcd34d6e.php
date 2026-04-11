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
     <?php $__env->slot('header', null, []); ?> Journal d'activité <?php $__env->endSlot(); ?>

<style>
/* ── KPI ── */
.kpi-row { display:grid; grid-template-columns:repeat(3,1fr); gap:14px; margin-bottom:22px; }
.kpi-mini { background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:16px 20px; position:relative; overflow:hidden; }
.kpi-mini::before { content:''; position:absolute; top:0;left:0;right:0; height:3px; border-radius:12px 12px 0 0; }
.kpi-mini.green::before  { background:#16a34a; }
.kpi-mini.orange::before { background:#d97706; }
.kpi-mini.red::before    { background:#dc2626; }
.kpi-lbl { font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:.8px;color:#6b7280;margin-bottom:4px; }
.kpi-val { font-family:'Syne',sans-serif;font-size:22px;font-weight:700;color:#0d1117;letter-spacing:-.3px;line-height:1; }
.kpi-val.green  { color:#16a34a; }
.kpi-val.orange { color:#d97706; }
.kpi-val.red    { color:#dc2626; }
.kpi-s { font-size:11px;color:#9ca3af;margin-top:4px; }

/* ── FILTRE ── */
.filter-bar { background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:14px 18px;margin-bottom:18px;display:flex;align-items:center;gap:10px;flex-wrap:wrap; }
.filter-select { padding:8px 13px;border:1px solid #e5e7eb;border-radius:8px;font-size:13px;color:#0d1117;font-family:'DM Sans',sans-serif;background:#f9fafb;outline:none;cursor:pointer;transition:border-color .15s; }
.filter-select:focus { border-color:#c9a84c; }
.filter-input  { padding:8px 13px;border:1px solid #e5e7eb;border-radius:8px;font-size:13px;color:#0d1117;font-family:'DM Sans',sans-serif;background:#f9fafb;outline:none;transition:border-color .15s;min-width:0; }
.filter-input:focus { border-color:#c9a84c;background:#fff; }
.filter-btn   { padding:8px 16px;background:#0d1117;color:#fff;border:none;border-radius:8px;font-size:12px;font-weight:500;font-family:'DM Sans',sans-serif;cursor:pointer; }
.filter-reset { padding:8px 14px;background:none;color:#6b7280;border:1px solid #e5e7eb;border-radius:8px;font-size:12px;font-family:'DM Sans',sans-serif;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;gap:5px; }

/* ── TABLE CARD ── */
.table-card { background:#fff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden; }
.table-header { padding:16px 20px;border-bottom:1px solid #e5e7eb;display:flex;align-items:center;justify-content:space-between; }
.table-title { font-family:'Syne',sans-serif;font-size:14px;font-weight:700;color:#0d1117; }
.table-count { font-size:12px;color:#6b7280;margin-top:2px; }

/* ── TABLE ── */
.dt { width:100%;border-collapse:collapse; }
.dt th { padding:9px 16px;text-align:left;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#9ca3af;background:#f9fafb;border-bottom:1px solid #e5e7eb;white-space:nowrap; }
.dt td { padding:12px 16px;font-size:13px;color:#374151;border-bottom:1px solid #f3f4f6;vertical-align:middle; }
.dt tbody tr:last-child td { border-bottom:none; }
.dt tbody tr { transition:background .1s; }
.dt tbody tr:hover { background:#f9fafb; }

/* ── ACTION BADGE ── */
.action-badge { display:inline-flex;align-items:center;gap:5px;padding:4px 10px;border-radius:99px;font-size:11px;font-weight:700; }
.action-badge.created { background:#dcfce7;color:#16a34a; }
.action-badge.updated { background:#fef3c7;color:#d97706; }
.action-badge.deleted { background:#fee2e2;color:#dc2626; }
.action-badge.other   { background:#f3f4f6;color:#6b7280; }
.action-dot { width:5px;height:5px;border-radius:50%;background:currentColor; }

/* ── MODEL TAG ── */
.model-tag { display:inline-flex;align-items:center;gap:5px;padding:3px 9px;background:#f3f4f6;border-radius:6px;font-size:11px;font-weight:600;color:#374151; }
.model-tag .model-id { color:#9ca3af;font-weight:400;margin-left:2px; }

/* ── USER CELL ── */
.user-cell { display:flex;align-items:center;gap:8px; }
.user-av { width:26px;height:26px;border-radius:7px;background:#f5e9c9;display:flex;align-items:center;justify-content:center;font-family:'Syne',sans-serif;font-size:10px;font-weight:700;color:#8a6e2f;flex-shrink:0; }
.user-av.sys { background:#f3f4f6;color:#6b7280; }
.user-name { font-size:13px;font-weight:500;color:#0d1117; }

/* ── DESC ── */
.desc-text { font-size:12px;color:#374151;max-width:320px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap; }

/* ── IP ── */
.ip-tag { font-size:11px;font-family:monospace;color:#9ca3af; }

/* ── DATE ── */
.date-main { font-size:12px;color:#374151;white-space:nowrap; }
.date-rel  { font-size:11px;color:#9ca3af;margin-top:1px; }

/* ── ÉTAT VIDE ── */
.empty-state { padding:56px 20px;text-align:center; }
.empty-icon  { width:52px;height:52px;border-radius:14px;background:#f3f4f6;display:flex;align-items:center;justify-content:center;margin:0 auto 14px; }
.empty-icon svg { width:22px;height:22px;color:#9ca3af; }
.empty-title { font-family:'Syne',sans-serif;font-size:15px;font-weight:700;color:#0d1117;margin-bottom:6px; }
.empty-sub   { font-size:13px;color:#6b7280; }

/* ── PAGINATION ── */
.pagination-wrap { padding:14px 18px;border-top:1px solid #e5e7eb;display:flex;align-items:center;justify-content:space-between; }
.pagination-info { font-size:12px;color:#6b7280; }
.pagination-links { display:flex;gap:4px; }
.page-btn { display:inline-flex;align-items:center;justify-content:center;min-width:32px;height:32px;padding:0 10px;border-radius:7px;border:1px solid #e5e7eb;background:#fff;color:#374151;font-size:12px;font-weight:500;text-decoration:none;transition:all .15s; }
.page-btn:hover { background:#f9fafb; }
.page-btn.active { background:#0d1117;color:#fff;border-color:#0d1117; }
.page-btn.disabled { opacity:.4;pointer-events:none; }

/* ── EXPORT ── */
.btn-export { display:flex;align-items:center;gap:6px;padding:9px 16px;background:#fff;color:#374151;border:1px solid #e5e7eb;border-radius:8px;font-size:12px;font-weight:500;font-family:'DM Sans',sans-serif;cursor:pointer;text-decoration:none;transition:all .15s; }
.btn-export:hover { border-color:#c9a84c;color:#8a6e2f; }
.btn-export svg { width:13px;height:13px; }
</style>

<div style="padding:24px 32px 48px">

    
    <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:22px">
        <div>
            <h1 style="font-family:'Syne',sans-serif;font-size:22px;font-weight:700;color:#0d1117;letter-spacing:-.4px">Journal d'activité</h1>
            <p style="font-size:13px;color:#6b7280;margin-top:3px">
                Toutes les actions effectuées sur la plateforme · <?php echo e($logs->total()); ?> entrée(s)
            </p>
        </div>
    </div>

    
    <?php
        $nbCreated = $logs->getCollection()->where('action','created')->count();
        $nbUpdated = $logs->getCollection()->where('action','updated')->count();
        $nbDeleted = $logs->getCollection()->where('action','deleted')->count();
    ?>
    <div class="kpi-row">
        <div class="kpi-mini green">
            <div class="kpi-lbl">Créations (cette page)</div>
            <div class="kpi-val green"><?php echo e($nbCreated); ?></div>
            <div class="kpi-s">Nouveaux enregistrements</div>
        </div>
        <div class="kpi-mini orange">
            <div class="kpi-lbl">Modifications (cette page)</div>
            <div class="kpi-val orange"><?php echo e($nbUpdated); ?></div>
            <div class="kpi-s">Données mises à jour</div>
        </div>
        <div class="kpi-mini red">
            <div class="kpi-lbl">Suppressions (cette page)</div>
            <div class="kpi-val red"><?php echo e($nbDeleted); ?></div>
            <div class="kpi-s">Enregistrements supprimés</div>
        </div>
    </div>

    
    <form method="GET" action="<?php echo e(route('admin.activity-logs.index')); ?>">
        <div class="filter-bar">
            
            <div style="position:relative;flex:1;min-width:180px">
                <svg style="position:absolute;left:10px;top:50%;transform:translateY(-50%);width:14px;height:14px;color:#9ca3af" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" name="q" value="<?php echo e(request('q')); ?>"
                    placeholder="Description, utilisateur, modèle…"
                    class="filter-input" style="padding-left:34px;width:100%">
            </div>

            
            <select name="action" class="filter-select">
                <option value="">Toutes les actions</option>
                <option value="created" <?php echo e(request('action')==='created' ? 'selected':''); ?>>Créations</option>
                <option value="updated" <?php echo e(request('action')==='updated' ? 'selected':''); ?>>Modifications</option>
                <option value="deleted" <?php echo e(request('action')==='deleted' ? 'selected':''); ?>>Suppressions</option>
            </select>

            
            <select name="model" class="filter-select">
                <option value="">Tous les modèles</option>
                <?php $__currentLoopData = ['Paiement','Contrat','Bien','User','Agency']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($m); ?>" <?php echo e(request('model')===$m ? 'selected':''); ?>><?php echo e($m); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>

            
            <input type="date" name="date" value="<?php echo e(request('date')); ?>" class="filter-input" style="width:145px">

            <button type="submit" class="filter-btn">Filtrer</button>

            <?php if(request()->hasAny(['q','action','model','date'])): ?>
                <a href="<?php echo e(route('admin.activity-logs.index')); ?>" class="filter-reset">
                    <svg style="width:12px;height:12px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    Effacer
                </a>
            <?php endif; ?>
        </div>
    </form>

    
    <div class="table-card">
        <div class="table-header">
            <div>
                <div class="table-title">Historique des actions</div>
                <div class="table-count">
                    <?php echo e($logs->total()); ?> entrée(s) · Page <?php echo e($logs->currentPage()); ?> / <?php echo e($logs->lastPage()); ?>

                </div>
            </div>
            <div style="display:flex;align-items:center;gap:10px">
                <div style="display:flex;align-items:center;gap:12px;font-size:11px;color:#9ca3af">
                    <span style="display:flex;align-items:center;gap:4px"><span style="width:8px;height:8px;border-radius:50%;background:#16a34a;display:inline-block"></span>Créé</span>
                    <span style="display:flex;align-items:center;gap:4px"><span style="width:8px;height:8px;border-radius:50%;background:#d97706;display:inline-block"></span>Modifié</span>
                    <span style="display:flex;align-items:center;gap:4px"><span style="width:8px;height:8px;border-radius:50%;background:#dc2626;display:inline-block"></span>Supprimé</span>
                </div>
            </div>
        </div>

        <?php if($logs->isEmpty()): ?>
        <div class="empty-state">
            <div class="empty-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <div class="empty-title">Aucune activité enregistrée</div>
            <div class="empty-sub">
                <?php if(request()->hasAny(['q','action','model','date'])): ?>
                    Aucun résultat pour ces filtres.
                    <a href="<?php echo e(route('admin.activity-logs.index')); ?>" style="color:#c9a84c;font-weight:500">Effacer les filtres</a>
                <?php else: ?>
                    Les actions sur la plateforme apparaîtront ici.
                <?php endif; ?>
            </div>
        </div>

        <?php else: ?>
        <div style="overflow-x:auto">
            <table class="dt">
                <thead>
                    <tr>
                        <th>Date & heure</th>
                        <th>Action</th>
                        <th>Description</th>
                        <th>Modèle</th>
                        <th>Utilisateur</th>
                        <?php if(auth()->user()->isSuperAdmin()): ?>
                        <th>Agence</th>
                        <?php endif; ?>
                        <th>IP</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $actionClass = match($log->action) {
                            'created' => 'created',
                            'updated' => 'updated',
                            'deleted' => 'deleted',
                            default   => 'other',
                        };
                        $actionLabel = match($log->action) {
                            'created' => 'Créé',
                            'updated' => 'Modifié',
                            'deleted' => 'Supprimé',
                            default   => ucfirst($log->action),
                        };
                        $modelName = $log->model_type ? class_basename($log->model_type) : '—';
                        $modelColor = match($modelName) {
                            'Paiement' => '#16a34a',
                            'Contrat'  => '#1d4ed8',
                            'Bien'     => '#c9a84c',
                            'User'     => '#7c3aed',
                            default    => '#6b7280',
                        };
                    ?>
                    <tr>
                        
                        <td>
                            <div class="date-main"><?php echo e($log->created_at?->format('d/m/Y H:i')); ?></div>
                            <div class="date-rel"><?php echo e($log->created_at?->diffForHumans()); ?></div>
                        </td>

                        
                        <td>
                            <span class="action-badge <?php echo e($actionClass); ?>">
                                <span class="action-dot"></span>
                                <?php echo e($actionLabel); ?>

                            </span>
                        </td>

                        
                        <td>
                            <div class="desc-text" title="<?php echo e($log->description); ?>">
                                <?php echo e($log->description ?? '—'); ?>

                            </div>
                        </td>

                        
                        <td>
                            <?php if($log->model_type): ?>
                            <span class="model-tag">
                                <span style="width:6px;height:6px;border-radius:50%;background:<?php echo e($modelColor); ?>;flex-shrink:0;display:inline-block"></span>
                                <?php echo e($modelName); ?><span class="model-id">#<?php echo e($log->model_id); ?></span>
                            </span>
                            <?php else: ?>
                            <span style="color:#9ca3af;font-size:12px">—</span>
                            <?php endif; ?>
                        </td>

                        
                        <td>
                            <div class="user-cell">
                                <?php if($log->user): ?>
                                    <div class="user-av"><?php echo e(strtoupper(substr($log->user->name, 0, 2))); ?></div>
                                    <div class="user-name"><?php echo e($log->user->name); ?></div>
                                <?php else: ?>
                                    <div class="user-av sys">
                                        <svg style="width:12px;height:12px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 010 14.14M4.93 4.93a10 10 0 000 14.14"/></svg>
                                    </div>
                                    <div style="font-size:12px;color:#9ca3af;font-style:italic">Système</div>
                                <?php endif; ?>
                            </div>
                        </td>

                        
                        <?php if(auth()->user()->isSuperAdmin()): ?>
                        <td style="font-size:12px;color:#6b7280">
                            <?php echo e($log->agency->name ?? '—'); ?>

                        </td>
                        <?php endif; ?>

                        
                        <td>
                            <span class="ip-tag"><?php echo e($log->ip_address ?? '—'); ?></span>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

        
        <?php if($logs->hasPages()): ?>
        <div class="pagination-wrap">
            <div class="pagination-info">
                Affichage de <?php echo e($logs->firstItem()); ?> à <?php echo e($logs->lastItem()); ?> sur <?php echo e($logs->total()); ?> entrées
            </div>
            <div class="pagination-links">
                <?php if($logs->onFirstPage()): ?>
                    <span class="page-btn disabled"><svg style="width:14px;height:14px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg></span>
                <?php else: ?>
                    <a href="<?php echo e($logs->previousPageUrl()); ?>" class="page-btn"><svg style="width:14px;height:14px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg></a>
                <?php endif; ?>

                <?php $__currentLoopData = $logs->getUrlRange(max(1,$logs->currentPage()-2), min($logs->lastPage(),$logs->currentPage()+2)); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e($url); ?>" class="page-btn <?php echo e($page === $logs->currentPage() ? 'active':''); ?>"><?php echo e($page); ?></a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                <?php if($logs->hasMorePages()): ?>
                    <a href="<?php echo e($logs->nextPageUrl()); ?>" class="page-btn"><svg style="width:14px;height:14px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg></a>
                <?php else: ?>
                    <span class="page-btn disabled"><svg style="width:14px;height:14px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg></span>
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
<?php endif; ?><?php /**PATH C:\Users\ph\bimotech-immo\resources\views/activity-logs/index.blade.php ENDPATH**/ ?>