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
     <?php $__env->slot('header', null, []); ?> Biens <?php $__env->endSlot(); ?>

<style>
/* ── KPI ROW ── */
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
.filter-bar { background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:14px 18px;margin-bottom:18px;display:flex;align-items:center;gap:10px;flex-wrap:wrap; }
.filter-input { padding:8px 13px;border:1px solid #e5e7eb;border-radius:8px;font-size:13px;color:#0d1117;font-family:'DM Sans',sans-serif;background:#f9fafb;outline:none;transition:border-color .15s;min-width:0; }
.filter-input:focus { border-color:#c9a84c;background:#fff; }
.filter-select { padding:8px 13px;border:1px solid #e5e7eb;border-radius:8px;font-size:13px;color:#0d1117;font-family:'DM Sans',sans-serif;background:#f9fafb;outline:none;cursor:pointer; }
.filter-btn { padding:8px 16px;background:#0d1117;color:#fff;border:none;border-radius:8px;font-size:12px;font-weight:500;font-family:'DM Sans',sans-serif;cursor:pointer;white-space:nowrap; }
.filter-reset { padding:8px 14px;background:none;color:#6b7280;border:1px solid #e5e7eb;border-radius:8px;font-size:12px;font-family:'DM Sans',sans-serif;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;gap:5px;white-space:nowrap; }
.view-toggle { display:flex;gap:2px;padding:3px;background:#f3f4f6;border-radius:8px; }
.view-btn { padding:6px 10px;border:none;background:none;border-radius:6px;cursor:pointer;display:flex;align-items:center;justify-content:center; }
.view-btn.active { background:#fff;box-shadow:0 1px 3px rgba(0,0,0,0.08); }
.view-btn svg { width:16px;height:16px;color:#6b7280; }
.view-btn.active svg { color:#0d1117; }

/* ── VUE GRILLE ── */
.biens-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:16px; }

.bien-card {
    background:#fff; border:1px solid #e5e7eb; border-radius:14px;
    overflow:hidden; transition:transform .2s, box-shadow .2s;
    display:flex; flex-direction:column;
}
.bien-card:hover { transform:translateY(-3px); box-shadow:0 12px 32px -8px rgba(0,0,0,0.10); }

.bien-photo {
    height:160px; background:#f9fafb;
    display:flex; align-items:center; justify-content:center;
    position:relative; overflow:hidden;
}
.bien-photo img { width:100%;height:100%;object-fit:cover; }
.bien-photo-placeholder {
    width:100%;height:100%;display:flex;align-items:center;justify-content:center;
    background:linear-gradient(135deg,#f5e9c9 0%,#f9fafb 100%);
}
.bien-photo-placeholder svg { width:40px;height:40px;color:#c9a84c;opacity:.6; }

.statut-pill {
    position:absolute;top:12px;left:12px;
    padding:4px 10px;border-radius:99px;font-size:11px;font-weight:600;
    backdrop-filter:blur(4px);
}
.statut-pill.loue  { background:rgba(22,163,74,.15);color:#16a34a;border:1px solid rgba(22,163,74,.2); }
.statut-pill.dispo { background:rgba(29,78,216,.15);color:#1d4ed8;border:1px solid rgba(29,78,216,.2); }
.statut-pill.trav  { background:rgba(201,168,76,.15);color:#8a6e2f;border:1px solid rgba(201,168,76,.2); }

.meuble-tag {
    position:absolute;top:12px;right:12px;
    background:rgba(0,0,0,.5);color:#fff;
    padding:3px 8px;border-radius:6px;font-size:10px;font-weight:600;letter-spacing:.5px;
}

.bien-body { padding:16px 18px;flex:1;display:flex;flex-direction:column;gap:4px; }
.bien-ref { font-family:'Syne',sans-serif;font-size:11px;font-weight:600;color:#9ca3af;letter-spacing:.5px; }
.bien-type { font-family:'Syne',sans-serif;font-size:15px;font-weight:700;color:#0d1117;letter-spacing:-.2px;margin:3px 0; }
.bien-addr { font-size:12px;color:#6b7280;display:flex;align-items:center;gap:4px; }
.bien-addr svg { width:12px;height:12px;flex-shrink:0; }

.bien-sep { height:1px;background:#f3f4f6;margin:12px 0; }

.bien-meta { display:flex;align-items:center;justify-content:space-between; }
.bien-loyer { font-family:'Syne',sans-serif;font-size:16px;font-weight:700;color:#0d1117; }
.bien-loyer-u { font-size:11px;font-weight:400;color:#9ca3af;margin-left:2px; }
.bien-proprio { font-size:12px;color:#6b7280; }

.bien-footer {
    padding:12px 18px;border-top:1px solid #f3f4f6;
    display:flex;align-items:center;justify-content:space-between;
}
.bien-contrats { font-size:11px;color:#9ca3af;display:flex;align-items:center;gap:4px; }
.bien-contrats svg { width:12px;height:12px; }
.bien-actions { display:flex;gap:5px; }

.act-btn { display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:7px;border:1px solid #e5e7eb;background:#fff;color:#6b7280;text-decoration:none;transition:all .15s;cursor:pointer; }
.act-btn:hover { border-color:#c9a84c;color:#8a6e2f;background:#f5e9c9; }
.act-btn svg { width:13px;height:13px; }
.act-btn.danger:hover { border-color:#dc2626;color:#dc2626;background:#fee2e2; }

/* ── VUE LISTE (TABLE) ── */
.table-card { background:#fff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden; }
.table-header { padding:18px 22px;border-bottom:1px solid #e5e7eb;display:flex;align-items:center;justify-content:space-between; }
.table-title { font-family:'Syne',sans-serif;font-size:14px;font-weight:700;color:#0d1117; }
.table-count { font-size:12px;color:#6b7280;margin-top:2px; }
.dt { width:100%;border-collapse:collapse; }
.dt th { padding:10px 18px;text-align:left;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#9ca3af;background:#f9fafb;border-bottom:1px solid #e5e7eb;white-space:nowrap; }
.dt td { padding:14px 18px;font-size:13px;color:#374151;border-bottom:1px solid #f3f4f6;vertical-align:middle; }
.dt tbody tr:last-child td { border-bottom:none; }
.dt tbody tr:hover { background:#f9fafb; }

/* badges */
.badge { display:inline-flex;align-items:center;gap:4px;padding:3px 9px;border-radius:99px;font-size:11px;font-weight:600;white-space:nowrap; }
.badge.g { background:#dcfce7;color:#16a34a; }
.badge.b { background:#dbeafe;color:#1d4ed8; }
.badge.o { background:#f5e9c9;color:#8a6e2f; }
.badge.gray { background:#f3f4f6;color:#6b7280; }
.bdot { width:5px;height:5px;border-radius:50%;background:currentColor; }

/* état vide */
.empty-state { padding:56px 20px;text-align:center; }
.empty-icon { width:56px;height:56px;border-radius:14px;background:#f5e9c9;display:flex;align-items:center;justify-content:center;margin:0 auto 16px; }
.empty-icon svg { width:24px;height:24px;color:#8a6e2f; }
.empty-title { font-family:'Syne',sans-serif;font-size:15px;font-weight:700;color:#0d1117;margin-bottom:6px; }
.empty-sub { font-size:13px;color:#6b7280; }

/* pagination */
.pagination-wrap { padding:16px 22px;border-top:1px solid #e5e7eb;display:flex;align-items:center;justify-content:space-between; }
.pagination-info { font-size:12px;color:#6b7280; }
.pagination-links { display:flex;gap:4px; }
.page-btn { display:inline-flex;align-items:center;justify-content:center;min-width:32px;height:32px;padding:0 10px;border-radius:7px;border:1px solid #e5e7eb;background:#fff;color:#374151;font-size:12px;font-weight:500;text-decoration:none;transition:all .15s; }
.page-btn:hover { background:#f9fafb;border-color:#d1d5db; }
.page-btn.active { background:#0d1117;color:#fff;border-color:#0d1117; }
.page-btn.disabled { opacity:.4;pointer-events:none; }

/* vue active */
#view-liste .bien-card-wrap { display:none; }
#view-grid  .table-wrap     { display:none; }
</style>

<div style="padding:24px 32px 48px">

    
    <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:22px">
        <div>
            <h1 style="font-family:'Syne',sans-serif;font-size:22px;font-weight:700;color:#0d1117;letter-spacing:-.4px">Biens immobiliers</h1>
            <p style="font-size:13px;color:#6b7280;margin-top:3px">
                Gérez votre parc immobilier — <?php echo e($biens->total()); ?> bien(s) au total
            </p>
        </div>
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create', \App\Models\Bien::class)): ?>
            <a href="<?php echo e(route('biens.create')); ?>" class="btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Ajouter un bien
            </a>
        <?php endif; ?>
    </div>

    
    <?php
        $nbTotal    = $biens->total();
        $nbLoues    = $biens->getCollection()->where('statut','loue')->count();
        $nbDispos   = $biens->getCollection()->where('statut','disponible')->count();
        $loyerTotal = $biens->getCollection()->sum('loyer_mensuel');
    ?>
    <div class="kpi-row">
        <div class="kpi-mini gold">
            <div class="kpi-lbl">Total biens</div>
            <div class="kpi-val"><?php echo e($nbTotal); ?><span class="kpi-u">biens</span></div>
            <div class="kpi-s">Dans votre agence</div>
        </div>
        <div class="kpi-mini green">
            <div class="kpi-lbl">Biens loués</div>
            <div class="kpi-val"><?php echo e($biens->getCollection()->where('statut','loue')->count()); ?></div>
            <div class="kpi-s">Contrats actifs</div>
        </div>
        <div class="kpi-mini blue">
            <div class="kpi-lbl">Disponibles</div>
            <div class="kpi-val"><?php echo e($biens->getCollection()->where('statut','disponible')->count()); ?></div>
            <div class="kpi-s">Prêts à louer</div>
        </div>
        <div class="kpi-mini red">
            <div class="kpi-lbl">Loyer potentiel</div>
            <div class="kpi-val"><?php echo e(number_format($loyerTotal/1000, 0, ',', ' ')); ?><span class="kpi-u">k F</span></div>
            <div class="kpi-s">Total mensuel (page)</div>
        </div>
    </div>

    
    <form method="GET" action="<?php echo e(route('biens.index')); ?>" id="filter-form">
        <div class="filter-bar">
            
            <div style="position:relative;flex:1;min-width:180px">
                <svg style="position:absolute;left:10px;top:50%;transform:translateY(-50%);width:14px;height:14px;color:#9ca3af" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" name="q" value="<?php echo e(request('q')); ?>" placeholder="Référence, adresse, ville…" class="filter-input" style="padding-left:34px;width:100%">
            </div>

            
            <select name="statut" class="filter-select">
                <option value="">Tous les statuts</option>
                <option value="loue"       <?php echo e(request('statut')==='loue'       ? 'selected':''); ?>>Loués</option>
                <option value="disponible" <?php echo e(request('statut')==='disponible' ? 'selected':''); ?>>Disponibles</option>
                <option value="en_travaux" <?php echo e(request('statut')==='en_travaux' ? 'selected':''); ?>>En travaux</option>
            </select>

            
            <select name="type" class="filter-select">
                <option value="">Tous les types</option>
                <?php $__currentLoopData = \App\Models\Bien::TYPES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($key); ?>" <?php echo e(request('type')===$key ? 'selected':''); ?>><?php echo e($label); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>

            <button type="submit" class="filter-btn">Filtrer</button>

            <?php if(request()->hasAny(['q','statut','type'])): ?>
                <a href="<?php echo e(route('biens.index')); ?>" class="filter-reset">
                    <svg style="width:12px;height:12px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    Effacer
                </a>
            <?php endif; ?>

            <div style="flex:1"></div>

            
            <div class="view-toggle">
                <button type="button" class="view-btn active" id="btn-grid" onclick="setView('grid')" title="Vue grille">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                </button>
                <button type="button" class="view-btn" id="btn-list" onclick="setView('list')" title="Vue liste">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
                </button>
            </div>
        </div>
    </form>

    
    <div id="view-grid">
        <?php if($biens->isEmpty()): ?>
            <div style="background:#fff;border:1px solid #e5e7eb;border-radius:14px">
                <div class="empty-state">
                    <div class="empty-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    </div>
                    <div class="empty-title">Aucun bien trouvé</div>
                    <div class="empty-sub">
                        <?php if(request()->hasAny(['q','statut','type'])): ?>
                            Aucun résultat pour ces filtres.
                            <a href="<?php echo e(route('biens.index')); ?>" style="color:#c9a84c;font-weight:500">Effacer les filtres</a>
                        <?php else: ?>
                            Ajoutez votre premier bien pour commencer.
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="biens-grid">
                <?php $__currentLoopData = $biens; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bien): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $statutClass = match($bien->statut) {
                        'loue'       => 'loue',
                        'disponible' => 'dispo',
                        default      => 'trav',
                    };
                    $statutLabel = match($bien->statut) {
                        'loue'       => 'Loué',
                        'disponible' => 'Disponible',
                        'en_travaux' => 'En travaux',
                        default      => $bien->statut,
                    };
                ?>
                <div class="bien-card">

                    
                    <div class="bien-photo">
                        <?php if($bien->photos && $bien->photos->where('est_principale',true)->first()): ?>
                            <img src="<?php echo e(Storage::url($bien->photos->where('est_principale',true)->first()->chemin)); ?>"
                                 alt="<?php echo e($bien->reference); ?>"
                                 loading="lazy">
                        <?php else: ?>
                            <div class="bien-photo-placeholder">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                            </div>
                        <?php endif; ?>

                        <span class="statut-pill <?php echo e($statutClass); ?>">
                            <?php echo e($statutLabel); ?>

                        </span>

                        <?php if($bien->meuble): ?>
                            <span class="meuble-tag">MEUBLÉ</span>
                        <?php endif; ?>
                    </div>

                    
                    <div class="bien-body">
                        <div class="bien-ref"><?php echo e($bien->reference); ?></div>
                        <div class="bien-type">
                            <?php echo e(\App\Models\Bien::TYPES[$bien->type] ?? $bien->type); ?>

                        </div>
                        <div class="bien-addr">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                            <?php echo e($bien->adresse); ?><?php if($bien->ville): ?>, <?php echo e($bien->ville); ?><?php endif; ?>
                        </div>

                        <div class="bien-sep"></div>

                        <div class="bien-meta">
                            <div>
                                <div class="bien-loyer">
                                    <?php echo e(number_format($bien->loyer_mensuel, 0, ',', ' ')); ?><span class="bien-loyer-u">F/mois</span>
                                </div>
                                <?php if($bien->contratActif): ?>
                                    <div style="font-size:11px;color:#16a34a;margin-top:2px;font-weight:500">
                                        Loyer contractuel : <?php echo e(number_format($bien->contratActif->loyer_contractuel, 0, ',', ' ')); ?> F
                                    </div>
                                <?php endif; ?>
                            </div>
                            <?php if($bien->proprietaire): ?>
                                <div style="text-align:right">
                                    <div style="font-size:11px;color:#9ca3af">Propriétaire</div>
                                    <div style="font-size:12px;font-weight:500;color:#374151"><?php echo e($bien->proprietaire->name); ?></div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    
                    <div class="bien-footer">
                        <div class="bien-contrats">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                            <?php echo e($bien->contrats_count); ?> contrat(s)
                        </div>
                        <div class="bien-actions">
                            <a href="<?php echo e(route('biens.show', $bien)); ?>" class="act-btn" title="Voir le détail">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </a>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $bien)): ?>
                                <a href="<?php echo e(route('biens.edit', $bien)); ?>" class="act-btn" title="Modifier">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                </a>
                            <?php endif; ?>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete', $bien)): ?>
                                <form method="POST" action="<?php echo e(route('biens.destroy', $bien)); ?>"
                                      onsubmit="return confirm('Supprimer ce bien ? Cette action est irréversible.')">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="act-btn danger" title="Supprimer">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 011-1h4a1 1 0 011 1v2"/></svg>
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>

                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>
    </div>

    
    <div id="view-list" style="display:none">
        <div class="table-card">
            <div class="table-header">
                <div>
                    <div class="table-title">Liste des biens</div>
                    <div class="table-count"><?php echo e($biens->total()); ?> bien(s) · Page <?php echo e($biens->currentPage()); ?> / <?php echo e($biens->lastPage()); ?></div>
                </div>
            </div>
            <div style="overflow-x:auto">
                <table class="dt">
                    <thead>
                        <tr>
                            <th>Référence</th>
                            <th>Type</th>
                            <th>Adresse</th>
                            <th>Propriétaire</th>
                            <th style="text-align:right">Loyer/mois</th>
                            <th>Commission</th>
                            <th style="text-align:center">Statut</th>
                            <th style="text-align:center">Contrats</th>
                            <th style="text-align:center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $biens; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bien): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $sl = match($bien->statut) { 'loue'=>['Loué','g'], 'disponible'=>['Disponible','b'], default=>['En travaux','o'] };
                        ?>
                        <tr>
                            <td>
                                <div style="font-family:'Syne',sans-serif;font-size:12px;font-weight:600;color:#9ca3af"><?php echo e($bien->reference); ?></div>
                                <?php if($bien->meuble): ?><div style="font-size:10px;color:#8a6e2f;margin-top:1px">Meublé</div><?php endif; ?>
                            </td>
                            <td>
                                <div style="font-weight:500;color:#0d1117"><?php echo e(\App\Models\Bien::TYPES[$bien->type] ?? $bien->type); ?></div>
                            </td>
                            <td>
                                <div style="font-size:13px;color:#374151"><?php echo e($bien->adresse); ?></div>
                                <div style="font-size:11px;color:#6b7280"><?php echo e($bien->ville); ?><?php if($bien->quartier): ?>, <?php echo e($bien->quartier); ?><?php endif; ?></div>
                            </td>
                            <td>
                                <div style="font-size:13px;font-weight:500;color:#374151"><?php echo e($bien->proprietaire?->name ?? '—'); ?></div>
                                <div style="font-size:11px;color:#9ca3af"><?php echo e($bien->proprietaire?->telephone ?? ''); ?></div>
                            </td>
                            <td style="text-align:right">
                                <div style="font-family:'Syne',sans-serif;font-weight:600;color:#0d1117"><?php echo e(number_format($bien->loyer_mensuel, 0, ',', ' ')); ?> F</div>
                            </td>
                            <td>
                                <div style="font-size:12px;color:#6b7280"><?php echo e($bien->taux_commission); ?>%</div>
                            </td>
                            <td style="text-align:center">
                                <span class="badge <?php echo e($sl[1]); ?>"><span class="bdot"></span><?php echo e($sl[0]); ?></span>
                            </td>
                            <td style="text-align:center">
                                <span style="font-family:'Syne',sans-serif;font-size:13px;font-weight:600;color:#374151"><?php echo e($bien->contrats_count); ?></span>
                            </td>
                            <td style="text-align:center">
                                <div style="display:flex;align-items:center;justify-content:center;gap:5px">
                                    <a href="<?php echo e(route('biens.show', $bien)); ?>" class="act-btn">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $bien)): ?>
                                    <a href="<?php echo e(route('biens.edit', $bien)); ?>" class="act-btn">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="9">
                                <div class="empty-state">
                                    <div class="empty-icon">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
                                    </div>
                                    <div class="empty-title">Aucun bien</div>
                                </div>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            
            <?php if($biens->hasPages()): ?>
            <div class="pagination-wrap">
                <div class="pagination-info">
                    Affichage de <?php echo e($biens->firstItem()); ?> à <?php echo e($biens->lastItem()); ?> sur <?php echo e($biens->total()); ?> résultats
                </div>
                <div class="pagination-links">
                    <?php if($biens->onFirstPage()): ?>
                        <span class="page-btn disabled"><svg style="width:14px;height:14px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg></span>
                    <?php else: ?>
                        <a href="<?php echo e($biens->previousPageUrl()); ?>" class="page-btn"><svg style="width:14px;height:14px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg></a>
                    <?php endif; ?>
                    <?php $__currentLoopData = $biens->getUrlRange(max(1,$biens->currentPage()-2), min($biens->lastPage(),$biens->currentPage()+2)); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a href="<?php echo e($url); ?>" class="page-btn <?php echo e($page === $biens->currentPage() ? 'active' : ''); ?>"><?php echo e($page); ?></a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php if($biens->hasMorePages()): ?>
                        <a href="<?php echo e($biens->nextPageUrl()); ?>" class="page-btn"><svg style="width:14px;height:14px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg></a>
                    <?php else: ?>
                        <span class="page-btn disabled"><svg style="width:14px;height:14px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg></span>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    
    <?php if($biens->hasPages() && !$biens->isEmpty()): ?>
    <div style="display:flex;align-items:center;justify-content:space-between;margin-top:20px" id="grid-pagination">
        <div style="font-size:12px;color:#6b7280">
            Affichage de <?php echo e($biens->firstItem()); ?> à <?php echo e($biens->lastItem()); ?> sur <?php echo e($biens->total()); ?> biens
        </div>
        <div class="pagination-links">
            <?php if($biens->onFirstPage()): ?>
                <span class="page-btn disabled"><svg style="width:14px;height:14px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg></span>
            <?php else: ?>
                <a href="<?php echo e($biens->previousPageUrl()); ?>" class="page-btn"><svg style="width:14px;height:14px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg></a>
            <?php endif; ?>
            <?php $__currentLoopData = $biens->getUrlRange(max(1,$biens->currentPage()-2), min($biens->lastPage(),$biens->currentPage()+2)); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e($url); ?>" class="page-btn <?php echo e($page === $biens->currentPage() ? 'active' : ''); ?>"><?php echo e($page); ?></a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php if($biens->hasMorePages()): ?>
                <a href="<?php echo e($biens->nextPageUrl()); ?>" class="page-btn"><svg style="width:14px;height:14px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg></a>
            <?php else: ?>
                <span class="page-btn disabled"><svg style="width:14px;height:14px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg></span>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

</div>

<script>
function setView(v) {
    const isGrid = v === 'grid';
    document.getElementById('view-grid').style.display = isGrid ? '' : 'none';
    document.getElementById('view-list').style.display = isGrid ? 'none' : '';
    const gp = document.getElementById('grid-pagination');
    if (gp) gp.style.display = isGrid ? '' : 'none';
    document.getElementById('btn-grid').classList.toggle('active', isGrid);
    document.getElementById('btn-list').classList.toggle('active', !isGrid);
    localStorage.setItem('biens_view', v);
}

// Restaurer la vue préférée
const savedView = localStorage.getItem('biens_view') || 'grid';
if (savedView === 'list') setView('list');
</script>

 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?><?php /**PATH C:\Users\ph\bimotech-immo\resources\views/biens/index.blade.php ENDPATH**/ ?>