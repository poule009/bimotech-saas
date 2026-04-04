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
     <?php $__env->slot('header', null, []); ?> Fiche bien <?php $__env->endSlot(); ?>

<style>
/* ── LAYOUT ── */
.page-grid { display:grid; grid-template-columns:1fr 300px; gap:20px; align-items:start; }

/* ── CARD ── */
.card { background:#fff; border:1px solid #e5e7eb; border-radius:14px; overflow:hidden; margin-bottom:18px; }
.card:last-child { margin-bottom:0; }
.card-hd { padding:16px 20px; border-bottom:1px solid #e5e7eb; display:flex; align-items:center; justify-content:space-between; }
.card-title { font-family:'Syne',sans-serif; font-size:13px; font-weight:700; color:#0d1117; }
.card-action { font-size:12px; color:#6b7280; text-decoration:none; transition:color .15s; }
.card-action:hover { color:#0d1117; }
.card-body { padding:18px 20px; }

/* ── HERO PHOTO ── */
.photo-hero {
    background:#f9fafb; border-radius:14px; overflow:hidden;
    margin-bottom:18px; position:relative; height:280px;
}
.photo-hero img { width:100%;height:100%;object-fit:cover; }
.photo-hero-placeholder {
    width:100%;height:100%;display:flex;align-items:center;justify-content:center;
    background:linear-gradient(135deg,#f5e9c9 0%,#f9fafb 100%);
}
.photo-hero-placeholder svg { width:56px;height:56px;color:#c9a84c;opacity:.5; }

.statut-pill {
    position:absolute;top:14px;left:14px;
    padding:5px 12px;border-radius:99px;font-size:12px;font-weight:600;
    backdrop-filter:blur(8px);
}
.statut-pill.loue  { background:rgba(22,163,74,.15);color:#16a34a;border:1px solid rgba(22,163,74,.25); }
.statut-pill.dispo { background:rgba(29,78,216,.15);color:#1d4ed8;border:1px solid rgba(29,78,216,.25); }
.statut-pill.trav  { background:rgba(201,168,76,.15);color:#8a6e2f;border:1px solid rgba(201,168,76,.25); }
.meuble-pill { position:absolute;top:14px;right:14px;background:rgba(0,0,0,.55);color:#fff;padding:4px 10px;border-radius:7px;font-size:10px;font-weight:700;letter-spacing:.5px; }

/* ── GALERIE THUMBS ── */
.galerie { display:flex;gap:8px;overflow-x:auto;padding-bottom:4px; }
.galerie::-webkit-scrollbar { height:4px; }
.galerie::-webkit-scrollbar-thumb { background:#e5e7eb;border-radius:99px; }
.galerie-thumb { width:64px;height:64px;border-radius:9px;overflow:hidden;flex-shrink:0;border:2px solid transparent;cursor:pointer;transition:border-color .15s; }
.galerie-thumb.active { border-color:#c9a84c; }
.galerie-thumb img { width:100%;height:100%;object-fit:cover; }

/* ── INFO GRIDS ── */
.info-grid  { display:grid;grid-template-columns:1fr 1fr;gap:12px; }
.info-grid3 { display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px; }
.info-item { }
.info-lbl { font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:.8px;color:#9ca3af;margin-bottom:4px; }
.info-val { font-size:13px;font-weight:500;color:#0d1117; }
.info-sub { font-size:11px;color:#6b7280;margin-top:2px; }
.info-val.big { font-family:'Syne',sans-serif;font-size:18px;font-weight:700; }
.info-val.green { color:#16a34a; }
.info-val.gold  { color:#8a6e2f; }

/* ── ACTIONS ── */
.actions-bar { display:flex;gap:8px;flex-wrap:wrap;margin-bottom:18px; }
.btn-action { display:flex;align-items:center;gap:6px;padding:9px 16px;border-radius:9px;font-size:12px;font-weight:500;font-family:'DM Sans',sans-serif;cursor:pointer;text-decoration:none;transition:all .15s;border:none; }
.btn-dark    { background:#0d1117;color:#fff; }
.btn-dark:hover { opacity:.85; }
.btn-outline { background:#fff;color:#374151;border:1px solid #e5e7eb; }
.btn-outline:hover { border-color:#c9a84c;color:#8a6e2f; }
.btn-danger  { background:#fee2e2;color:#dc2626;border:1px solid #fecaca; }
.btn-danger:hover { background:#fecaca; }
.btn-green   { background:#dcfce7;color:#16a34a;border:1px solid #bbf7d0; }
.btn-green:hover { background:#bbf7d0; }
.btn-action svg { width:14px;height:14px; }

/* ── CONTRAT CARD ── */
.contrat-card { background:#0d1117;border-radius:12px;padding:18px 20px;margin-bottom:18px; }
.cc-grid { display:grid;grid-template-columns:1fr 1fr;gap:14px; }
.cc-lbl { font-size:9px;font-weight:600;letter-spacing:1px;text-transform:uppercase;color:rgba(255,255,255,.35);margin-bottom:3px; }
.cc-val { font-size:13px;font-weight:600;color:#fff; }
.cc-val.green { color:#4ade80; }
.cc-val.gold  { color:#c9a84c; }

/* ── SIDEBAR STICKY ── */
.sidebar-sticky { position:sticky;top:80px; }

/* ── KPI MINI ── */
.kpi-mini { background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:16px 18px;margin-bottom:12px;position:relative;overflow:hidden; }
.kpi-mini::before { content:'';position:absolute;top:0;left:0;right:0;height:3px;border-radius:12px 12px 0 0; }
.kpi-mini.gold::before  { background:#c9a84c; }
.kpi-mini.green::before { background:#16a34a; }
.kpi-mini.blue::before  { background:#1d4ed8; }
.kpi-lbl { font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:.8px;color:#6b7280;margin-bottom:4px; }
.kpi-val { font-family:'Syne',sans-serif;font-size:20px;font-weight:700;color:#0d1117; }
.kpi-val.green { color:#16a34a; }
.kpi-val.gold  { color:#8a6e2f; }
.kpi-u  { font-size:11px;font-weight:400;color:#9ca3af;margin-left:2px; }
.kpi-s  { font-size:11px;color:#9ca3af;margin-top:4px; }

/* ── PHOTO UPLOAD ── */
.upload-zone { display:flex;flex-direction:column;align-items:center;justify-content:center;border:2px dashed #e5e7eb;border-radius:10px;padding:24px 16px;cursor:pointer;transition:all .15s;text-align:center; }
.upload-zone:hover { border-color:#c9a84c;background:#fdf8ef; }
.upload-zone svg { width:28px;height:28px;color:#c9a84c;margin-bottom:8px;opacity:.7; }
.upload-zone-text { font-size:13px;font-weight:500;color:#374151; }
.upload-zone-hint { font-size:11px;color:#9ca3af;margin-top:4px; }

/* ── TABLE ── */
.dt { width:100%;border-collapse:collapse; }
.dt th { padding:9px 16px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#9ca3af;background:#f9fafb;border-bottom:1px solid #e5e7eb;text-align:left; }
.dt td { padding:12px 16px;font-size:12px;color:#374151;border-bottom:1px solid #f3f4f6;vertical-align:middle; }
.dt tbody tr:last-child td { border-bottom:none; }
.dt tbody tr:hover { background:#f9fafb; }

/* badge */
.badge { display:inline-flex;align-items:center;gap:4px;padding:3px 9px;border-radius:99px;font-size:11px;font-weight:600; }
.badge.g { background:#dcfce7;color:#16a34a; }
.badge.r { background:#fee2e2;color:#dc2626; }
.badge.o { background:#f5e9c9;color:#8a6e2f; }
.bdot { width:5px;height:5px;border-radius:50%;background:currentColor; }

/* desc */
.description-box { background:#f9fafb;border-left:3px solid #c9a84c;border-radius:0 8px 8px 0;padding:14px 16px;font-size:13px;color:#374151;line-height:1.7; }
</style>

<div style="padding:24px 32px 48px">

    
    <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:#6b7280;margin-bottom:16px">
        <a href="<?php echo e(route('biens.index')); ?>" style="color:#6b7280;text-decoration:none">Biens</a>
        <svg style="width:12px;height:12px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
        <span style="color:#0d1117;font-weight:500"><?php echo e($bien->reference); ?></span>
    </div>

    
    <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:18px">
        <div>
            <h1 style="font-family:'Syne',sans-serif;font-size:22px;font-weight:700;color:#0d1117;letter-spacing:-.4px">
                <?php echo e(\App\Models\Bien::TYPES[$bien->type] ?? $bien->type); ?>

                <span style="font-size:14px;font-weight:400;color:#9ca3af;margin-left:6px"><?php echo e($bien->reference); ?></span>
            </h1>
            <p style="font-size:13px;color:#6b7280;margin-top:3px">
                <?php echo e($bien->adresse); ?><?php if($bien->quartier): ?>, <?php echo e($bien->quartier); ?><?php endif; ?>, <?php echo e($bien->ville); ?>

            </p>
        </div>
    </div>

    <div class="actions-bar">
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $bien)): ?>
            <a href="<?php echo e(route('biens.edit', $bien)); ?>" class="btn-action btn-dark">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                Modifier ce bien
            </a>
        <?php endif; ?>

        <?php if(!$contratActif && auth()->user()->isAdmin()): ?>
            <a href="<?php echo e(route('admin.contrats.create', ['bien_id' => $bien->id])); ?>" class="btn-action btn-green">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><line x1="12" y1="11" x2="12" y2="17"/><line x1="9" y1="14" x2="15" y2="14"/></svg>
                Créer un contrat
            </a>
        <?php endif; ?>

        <?php if($contratActif && auth()->user()->isAdmin()): ?>
            <a href="<?php echo e(route('admin.paiements.create', ['contrat_id' => $contratActif->id])); ?>" class="btn-action btn-outline">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
                Enregistrer un paiement
            </a>
        <?php endif; ?>

        <a href="<?php echo e(route('biens.index')); ?>" class="btn-action btn-outline">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
            Retour à la liste
        </a>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete', $bien)): ?>
            <form method="POST" action="<?php echo e(route('biens.destroy', $bien)); ?>"
                  onsubmit="return confirm('Supprimer ce bien ? Cette action est irréversible.')">
                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                <button type="submit" class="btn-action btn-danger">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/></svg>
                    Supprimer
                </button>
            </form>
        <?php endif; ?>
    </div>

    <div class="page-grid">

        
        <div>

            
            <div class="photo-hero" id="photo-hero">
                <?php $principale = $bien->photos->firstWhere('est_principale', true) ?? $bien->photos->first(); ?>
                <?php if($principale): ?>
                    <img src="<?php echo e(Storage::url($principale->chemin)); ?>" alt="<?php echo e($bien->reference); ?>" id="hero-img">
                <?php else: ?>
                    <div class="photo-hero-placeholder">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    </div>
                <?php endif; ?>
                <?php
                    $sl = match($bien->statut) { 'loue'=>['loue','Loué'], 'disponible'=>['dispo','Disponible'], default=>['trav','En travaux'] };
                ?>
                <span class="statut-pill <?php echo e($sl[0]); ?>"><?php echo e($sl[1]); ?></span>
                <?php if($bien->meuble): ?><span class="meuble-pill">MEUBLÉ</span><?php endif; ?>
            </div>

            
            <?php if($bien->photos->count() > 1): ?>
            <div class="galerie" style="margin-bottom:18px">
                <?php $__currentLoopData = $bien->photos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $photo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="galerie-thumb <?php echo e($photo->est_principale ? 'active':''); ?>"
                     onclick="changerPhoto('<?php echo e(Storage::url($photo->chemin)); ?>', this)">
                    <img src="<?php echo e(Storage::url($photo->chemin)); ?>" alt="Photo <?php echo e($i+1); ?>">
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php endif; ?>

            
            <div class="card">
                <div class="card-hd"><div class="card-title">Caractéristiques</div></div>
                <div class="card-body">
                    <div class="info-grid3" style="margin-bottom:16px">
                        <div class="info-item">
                            <div class="info-lbl">Type</div>
                            <div class="info-val"><?php echo e(\App\Models\Bien::TYPES[$bien->type] ?? $bien->type); ?></div>
                        </div>
                        <?php if($bien->surface_m2): ?>
                        <div class="info-item">
                            <div class="info-lbl">Surface</div>
                            <div class="info-val"><?php echo e($bien->surface_m2); ?> m²</div>
                        </div>
                        <?php endif; ?>
                        <?php if($bien->nombre_pieces): ?>
                        <div class="info-item">
                            <div class="info-lbl">Pièces</div>
                            <div class="info-val"><?php echo e($bien->nombre_pieces); ?></div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <div style="height:1px;background:#f3f4f6;margin-bottom:16px"></div>

                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-lbl">Adresse</div>
                            <div class="info-val"><?php echo e($bien->adresse); ?></div>
                            <?php if($bien->quartier): ?><div class="info-sub"><?php echo e($bien->quartier); ?></div><?php endif; ?>
                        </div>
                        <div class="info-item">
                            <div class="info-lbl">Ville / Commune</div>
                            <div class="info-val"><?php echo e($bien->ville); ?></div>
                            <?php if($bien->commune): ?><div class="info-sub"><?php echo e($bien->commune); ?></div><?php endif; ?>
                        </div>
                        <div class="info-item">
                            <div class="info-lbl">Loyer mensuel</div>
                            <div class="info-val big gold"><?php echo e(number_format($bien->loyer_mensuel, 0, ',', ' ')); ?><span style="font-size:12px;color:#9ca3af;margin-left:3px">F/mois</span></div>
                        </div>
                        <div class="info-item">
                            <div class="info-lbl">Commission agence</div>
                            <div class="info-val big" style="color:#1d4ed8"><?php echo e($bien->taux_commission); ?><span style="font-size:12px;color:#9ca3af;margin-left:2px">%</span></div>
                            <div class="info-sub">Commission TTC : <?php echo e(number_format($bien->loyer_mensuel * ($bien->taux_commission / 100) * 1.18, 0, ',', ' ')); ?> F</div>
                        </div>
                        <?php if(auth()->user()->isAdmin()): ?>
                        <div class="info-item">
                            <div class="info-lbl">Propriétaire</div>
                            <div class="info-val"><?php echo e($bien->proprietaire->name); ?></div>
                            <div class="info-sub"><?php echo e($bien->proprietaire->telephone ?? '—'); ?></div>
                        </div>
                        <?php endif; ?>
                        <div class="info-item">
                            <div class="info-lbl">Meublé</div>
                            <div class="info-val"><?php echo e($bien->meuble ? 'Oui' : 'Non'); ?></div>
                        </div>
                    </div>

                    <?php if($bien->description): ?>
                    <div style="margin-top:16px">
                        <div class="info-lbl" style="margin-bottom:8px">Description</div>
                        <div class="description-box"><?php echo e($bien->description); ?></div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            
            <?php if($contratActif): ?>
            <div class="card" style="border-color:#bbf7d0;">
                <div class="card-hd" style="background:#f0fdf4;">
                    <div class="card-title" style="color:#15803d;">Contrat actif</div>
                    <a href="<?php echo e(route('admin.contrats.show', $contratActif)); ?>" class="card-action" style="color:#16a34a">Voir le contrat →</a>
                </div>
                <div class="card-body" style="padding:16px 20px">
                    <div class="contrat-card">
                        <div class="cc-grid">
                            <div>
                                <div class="cc-lbl">Locataire</div>
                                <div class="cc-val"><?php echo e($contratActif->locataire->name); ?></div>
                                <div style="font-size:11px;color:rgba(255,255,255,.3);margin-top:2px"><?php echo e($contratActif->locataire->telephone ?? ''); ?></div>
                            </div>
                            <div>
                                <div class="cc-lbl">Loyer contractuel</div>
                                <div class="cc-val gold"><?php echo e(number_format($contratActif->loyer_contractuel, 0, ',', ' ')); ?> F/mois</div>
                            </div>
                            <div>
                                <div class="cc-lbl">Début</div>
                                <div class="cc-val"><?php echo e(\Carbon\Carbon::parse($contratActif->date_debut)->format('d/m/Y')); ?></div>
                            </div>
                            <div>
                                <div class="cc-lbl">Fin</div>
                                <div class="cc-val"><?php echo e($contratActif->date_fin ? \Carbon\Carbon::parse($contratActif->date_fin)->format('d/m/Y') : 'Indéterminée'); ?></div>
                            </div>
                            <div>
                                <div class="cc-lbl">Caution</div>
                                <div class="cc-val green"><?php echo e(number_format($contratActif->caution, 0, ',', ' ')); ?> F</div>
                            </div>
                            <div>
                                <div class="cc-lbl">Référence bail</div>
                                <div class="cc-val" style="font-family:monospace;font-size:12px"><?php echo e($contratActif->reference_bail ?? '—'); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            
            <?php if(isset($totalPaye) && $totalPaye > 0): ?>
            <div class="card">
                <div class="card-hd">
                    <div class="card-title">Historique des paiements</div>
                    <?php if($contratActif): ?>
                        <a href="<?php echo e(route('admin.paiements.index', ['contrat_id' => $contratActif->id])); ?>" class="card-action">Voir tout →</a>
                    <?php endif; ?>
                </div>
                <div style="overflow-x:auto">
                    <table class="dt">
                        <thead>
                            <tr>
                                <th>Référence</th>
                                <th>Période</th>
                                <th>Mode</th>
                                <th style="text-align:right">Montant</th>
                                <th style="text-align:right">Net proprio</th>
                                <th style="text-align:center">Statut</th>
                                <th style="text-align:center">PDF</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $bien->contrats->flatMap->paiements->sortByDesc('periode')->take(10); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td style="font-family:'Syne',sans-serif;font-size:11px;color:#9ca3af"><?php echo e($p->reference_paiement); ?></td>
                                <td style="font-weight:500"><?php echo e(\Carbon\Carbon::parse($p->periode)->translatedFormat('M Y')); ?></td>
                                <td style="color:#6b7280"><?php echo e(\App\Models\Paiement::MODES_PAIEMENT[$p->mode_paiement] ?? $p->mode_paiement); ?></td>
                                <td style="text-align:right;font-family:'Syne',sans-serif;font-weight:600;color:#0d1117"><?php echo e(number_format($p->montant_encaisse, 0, ',', ' ')); ?> F</td>
                                <td style="text-align:right;font-family:'Syne',sans-serif;font-weight:600;color:#16a34a"><?php echo e(number_format($p->net_proprietaire, 0, ',', ' ')); ?> F</td>
                                <td style="text-align:center">
                                    <?php if($p->statut === 'valide'): ?>
                                        <span class="badge g"><span class="bdot"></span>Validé</span>
                                    <?php elseif($p->statut === 'annule'): ?>
                                        <span class="badge r"><span class="bdot"></span>Annulé</span>
                                    <?php else: ?>
                                        <span class="badge o"><span class="bdot"></span><?php echo e(ucfirst($p->statut)); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align:center">
                                    <?php if($p->statut === 'valide'): ?>
                                        <a href="<?php echo e(route('admin.paiements.pdf', $p)); ?>" target="_blank"
                                           style="display:inline-flex;align-items:center;justify-content:center;width:26px;height:26px;border:1px solid #e5e7eb;border-radius:6px;color:#6b7280;text-decoration:none">
                                            <svg style="width:12px;height:12px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>

            
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $bien)): ?>
            <div class="card">
                <div class="card-hd">
                    <div class="card-title">Photos <span style="font-size:11px;font-weight:400;color:#9ca3af">(<?php echo e($bien->photos->count()); ?> / 10)</span></div>
                </div>
                <div class="card-body">
                    
                    <?php if($bien->photos->isNotEmpty()): ?>
                    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(120px,1fr));gap:10px;margin-bottom:16px">
                        <?php $__currentLoopData = $bien->photos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $photo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div style="position:relative;border-radius:9px;overflow:hidden;border:2px solid <?php echo e($photo->est_principale ? '#c9a84c' : '#e5e7eb'); ?>">
                            <img src="<?php echo e(Storage::url($photo->chemin)); ?>" style="width:100%;height:100px;object-fit:cover;display:block">
                            <div style="position:absolute;inset:0;background:rgba(0,0,0,0);transition:background .2s;display:flex;align-items:center;justify-content:center;gap:6px"
                                 onmouseover="this.style.background='rgba(0,0,0,0.4)';this.querySelectorAll('a,button').forEach(e=>e.style.opacity='1')"
                                 onmouseout="this.style.background='rgba(0,0,0,0)';this.querySelectorAll('a,button').forEach(e=>e.style.opacity='0')">
                                <?php if(!$photo->est_principale): ?>
                                <form method="POST" action="<?php echo e(route('biens.photos.principale', [$bien, $photo])); ?>">
                                    <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                    <button type="submit" title="Définir comme principale"
                                            style="opacity:0;background:rgba(201,168,76,.9);color:#fff;border:none;border-radius:6px;padding:4px 8px;font-size:10px;cursor:pointer;transition:opacity .2s">
                                        ⭐
                                    </button>
                                </form>
                                <?php endif; ?>
                                <form method="POST" action="<?php echo e(route('biens.photos.destroy', [$bien, $photo])); ?>">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button type="submit" title="Supprimer"
                                            onclick="return confirm('Supprimer cette photo ?')"
                                            style="opacity:0;background:rgba(220,38,38,.9);color:#fff;border:none;border-radius:6px;padding:4px 8px;font-size:10px;cursor:pointer;transition:opacity .2s">
                                        ✕
                                    </button>
                                </form>
                            </div>
                            <?php if($photo->est_principale): ?>
                            <div style="position:absolute;bottom:4px;left:4px;background:rgba(201,168,76,.9);color:#fff;font-size:9px;font-weight:700;padding:2px 7px;border-radius:5px">PRINCIPALE</div>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <?php endif; ?>

                    
                    <?php if($bien->photos->count() < 10): ?>
                    <form method="POST" action="<?php echo e(route('biens.photos.store', $bien)); ?>" enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>
                        <label for="photos-input" class="upload-zone">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
                            <div class="upload-zone-text">Cliquer pour ajouter des photos</div>
                            <div class="upload-zone-hint">JPG, PNG, WEBP · Max 3 Mo · <?php echo e(10 - $bien->photos->count()); ?> photo(s) restante(s)</div>
                            <input id="photos-input" type="file" name="photos[]" multiple accept="image/jpeg,image/png,image/webp" style="display:none" onchange="previewPhotos(this)">
                        </label>
                        <div id="preview-container" style="display:none;grid-template-columns:repeat(auto-fill,minmax(80px,1fr));gap:8px;margin-top:12px"></div>
                        <button type="submit" id="btn-upload" style="display:none;width:100%;margin-top:10px;padding:9px;background:#0d1117;color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:500;cursor:pointer;font-family:'DM Sans',sans-serif">
                            Uploader les photos
                        </button>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

        </div>

        
        <div class="sidebar-sticky">

            
            <?php if(isset($totalPaye)): ?>
            <div class="kpi-mini gold">
                <div class="kpi-lbl">Total encaissé</div>
                <div class="kpi-val gold"><?php echo e(number_format($totalPaye, 0, ',', ' ')); ?><span class="kpi-u">F</span></div>
                <div class="kpi-s">Tous paiements confondus</div>
            </div>
            <?php endif; ?>
            <?php if(isset($totalNet)): ?>
            <div class="kpi-mini green">
                <div class="kpi-lbl">Net reversé proprio</div>
                <div class="kpi-val green"><?php echo e(number_format($totalNet, 0, ',', ' ')); ?><span class="kpi-u">F</span></div>
                <div class="kpi-s">Après commission agence</div>
            </div>
            <?php endif; ?>
            <?php if(isset($nbPaiements)): ?>
            <div class="kpi-mini blue">
                <div class="kpi-lbl">Paiements validés</div>
                <div class="kpi-val" style="color:#1d4ed8"><?php echo e($nbPaiements); ?></div>
                <div class="kpi-s"><?php echo e($bien->contrats_count ?? 0); ?> contrat(s) au total</div>
            </div>
            <?php endif; ?>

            
            <div class="card">
                <div class="card-hd"><div class="card-title">Statut actuel</div></div>
                <div class="card-body">
                    <?php
                        $statuts = [
                            'disponible' => ['dispo', 'Disponible', '#1d4ed8', '#dbeafe'],
                            'loue'       => ['loue',  'Loué',       '#16a34a', '#dcfce7'],
                            'en_travaux' => ['trav',  'En travaux', '#d97706', '#fef3c7'],
                        ];
                        [$sClass, $sLabel, $sColor, $sBg] = $statuts[$bien->statut] ?? ['gray', $bien->statut, '#6b7280', '#f3f4f6'];
                    ?>
                    <div style="display:flex;align-items:center;gap:10px;padding:12px 14px;background:<?php echo e($sBg); ?>;border-radius:9px">
                        <div style="width:10px;height:10px;border-radius:50%;background:<?php echo e($sColor); ?>"></div>
                        <div style="font-size:13px;font-weight:600;color:<?php echo e($sColor); ?>"><?php echo e($sLabel); ?></div>
                    </div>

                    <?php if($contratActif): ?>
                    <div style="margin-top:12px;padding:12px 14px;background:#f9fafb;border-radius:9px">
                        <div style="font-size:10px;font-weight:600;color:#9ca3af;text-transform:uppercase;letter-spacing:.8px;margin-bottom:6px">Locataire actuel</div>
                        <div style="font-size:13px;font-weight:600;color:#0d1117"><?php echo e($contratActif->locataire->name); ?></div>
                        <div style="font-size:11px;color:#6b7280;margin-top:2px">Depuis <?php echo e(\Carbon\Carbon::parse($contratActif->date_debut)->format('d/m/Y')); ?></div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            
            <div class="card">
                <div class="card-hd"><div class="card-title">Simulation financière</div></div>
                <div class="card-body" style="padding:14px 18px">
                    <?php
                        $loyerNu  = $contratActif ? $contratActif->loyer_contractuel : $bien->loyer_mensuel;
                        $commHT   = $loyerNu * ($bien->taux_commission / 100);
                        $tva      = $commHT * 0.18;
                        $commTTC  = $commHT + $tva;
                        $net      = $loyerNu - $commTTC;
                    ?>
                    <div style="display:flex;flex-direction:column;gap:8px;font-size:12px">
                        <div style="display:flex;justify-content:space-between">
                            <span style="color:#6b7280">Loyer mensuel</span>
                            <span style="font-family:'Syne',sans-serif;font-weight:600;color:#0d1117"><?php echo e(number_format($loyerNu, 0, ',', ' ')); ?> F</span>
                        </div>
                        <div style="display:flex;justify-content:space-between">
                            <span style="color:#6b7280">Commission HT (<?php echo e($bien->taux_commission); ?>%)</span>
                            <span style="font-family:'Syne',sans-serif;font-weight:600;color:#8a6e2f"><?php echo e(number_format($commHT, 0, ',', ' ')); ?> F</span>
                        </div>
                        <div style="display:flex;justify-content:space-between">
                            <span style="color:#6b7280">TVA (18%)</span>
                            <span style="font-family:'Syne',sans-serif;font-weight:600;color:#d97706"><?php echo e(number_format($tva, 0, ',', ' ')); ?> F</span>
                        </div>
                        <div style="height:1px;background:#f3f4f6;margin:2px 0"></div>
                        <div style="display:flex;justify-content:space-between">
                            <span style="color:#16a34a;font-weight:600">Net propriétaire</span>
                            <span style="font-family:'Syne',sans-serif;font-weight:700;color:#16a34a;font-size:14px"><?php echo e(number_format($net, 0, ',', ' ')); ?> F</span>
                        </div>
                    </div>
                </div>
            </div>

            
            <div style="text-align:center;padding:12px;font-size:11px;color:#9ca3af">
                Bien créé le <?php echo e($bien->created_at->format('d/m/Y')); ?>

                <?php if($bien->updated_at != $bien->created_at): ?>
                    · Modifié le <?php echo e($bien->updated_at->format('d/m/Y')); ?>

                <?php endif; ?>
            </div>

        </div>

    </div>

</div>

<script>
function changerPhoto(url, thumb) {
    document.getElementById('hero-img').src = url;
    document.querySelectorAll('.galerie-thumb').forEach(t => t.classList.remove('active'));
    thumb.classList.add('active');
}

function previewPhotos(input) {
    const container = document.getElementById('preview-container');
    const btn = document.getElementById('btn-upload');
    container.innerHTML = '';
    if (input.files.length === 0) { container.style.display='none'; btn.style.display='none'; return; }
    container.style.display = 'grid';
    btn.style.display = 'block';
    [...input.files].forEach(file => {
        const reader = new FileReader();
        reader.onload = e => {
            const div = document.createElement('div');
            div.style.cssText = 'border-radius:8px;overflow:hidden;height:70px';
            div.innerHTML = `<img src="${e.target.result}" style="width:100%;height:100%;object-fit:cover">`;
            container.appendChild(div);
        };
        reader.readAsDataURL(file);
    });
}
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
<?php endif; ?><?php /**PATH C:\Users\ph\bimotech-immo\resources\views/biens/show.blade.php ENDPATH**/ ?>