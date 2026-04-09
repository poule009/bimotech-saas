
<?php $__env->startSection('title', $bien->reference . ' — ' . $bien->type_label); ?>
<?php $__env->startSection('breadcrumb', 'Biens › ' . $bien->reference); ?>

<?php $__env->startSection('content'); ?>
<style>
.page-grid { display:grid;grid-template-columns:1fr 300px;gap:24px;align-items:start; }
.card { background:#fff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden;margin-bottom:16px; }
.card-hd { padding:14px 20px;border-bottom:1px solid #e5e7eb;display:flex;align-items:center;justify-content:space-between; }
.card-hd-left { display:flex;align-items:center;gap:10px; }
.card-icon { width:30px;height:30px;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0; }
.card-icon svg { width:15px;height:15px; }
.card-icon.gold   { background:#f5e9c9;color:#8a6e2f; }
.card-icon.blue   { background:#dbeafe;color:#1d4ed8; }
.card-icon.green  { background:#dcfce7;color:#16a34a; }
.card-icon.purple { background:#ede9fe;color:#7c3aed; }
.card-title { font-family:'Syne',sans-serif;font-size:13px;font-weight:700;color:#0d1117; }
.card-body { padding:18px 20px; }
.il { font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:.8px;color:#9ca3af;margin-bottom:4px; }
.iv { font-size:13px;font-weight:500;color:#0d1117; }
.iv-sub { font-size:11px;color:#6b7280;margin-top:2px; }
.info-grid { display:grid;grid-template-columns:1fr 1fr;gap:14px; }
.info-grid-3 { display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px; }

/* Actions */
.actions-bar { display:flex;gap:8px;flex-wrap:wrap;margin-bottom:20px; }
.btn-act { display:flex;align-items:center;gap:6px;padding:9px 16px;border-radius:9px;font-size:12px;font-weight:500;font-family:'DM Sans',sans-serif;cursor:pointer;text-decoration:none;transition:all .15s;border:none; }
.btn-dark    { background:#0d1117;color:#fff; }
.btn-dark:hover { opacity:.85; }
.btn-green   { background:#dcfce7;color:#16a34a;border:1px solid #bbf7d0; }
.btn-green:hover { background:#bbf7d0; }
.btn-outline { background:#fff;color:#374151;border:1px solid #e5e7eb; }
.btn-outline:hover { border-color:#c9a84c;color:#8a6e2f; }
.btn-red     { background:#fee2e2;color:#dc2626;border:1px solid #fecaca; }
.btn-red:hover { background:#fecaca; }
.btn-act svg { width:14px;height:14px; }

/* Hero */
.hero { background:linear-gradient(135deg,#0d1117,#1c2333);border-radius:14px;padding:22px 24px;margin-bottom:20px;display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:16px; }

/* KPIs */
.kpi-row { display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:20px; }
.kpi { background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:14px 16px; }
.kpi.gold  { border-top:3px solid #c9a84c; }
.kpi.green { border-top:3px solid #16a34a; }
.kpi.blue  { border-top:3px solid #1d4ed8; }
.kpi-lbl { font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#9ca3af;margin-bottom:6px; }
.kpi-val { font-family:'Syne',sans-serif;font-size:22px;font-weight:700;color:#0d1117; }
.kpi-sub { font-size:11px;color:#9ca3af;margin-top:3px; }

/* Table paiements */
.dt { width:100%;border-collapse:collapse; }
.dt th { padding:9px 16px;text-align:left;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.7px;color:#9ca3af;background:#f9fafb;border-bottom:1px solid #e5e7eb; }
.dt td { padding:11px 16px;font-size:13px;color:#374151;border-bottom:1px solid #f3f4f6;vertical-align:middle; }
.dt tbody tr:last-child td { border-bottom:none; }
.dt tbody tr:hover { background:#fafafa; }

/* Galerie photos */
.photo-hero { height:280px;background:#f3f4f6;border-radius:12px;overflow:hidden;margin-bottom:12px;position:relative; }
.photo-hero img { width:100%;height:100%;object-fit:cover; }
.photo-thumbs { display:flex;gap:8px;flex-wrap:wrap; }
.photo-thumb { width:72px;height:56px;border-radius:8px;overflow:hidden;cursor:pointer;border:2px solid transparent;transition:border .15s;flex-shrink:0; }
.photo-thumb:hover { border-color:#c9a84c; }
.photo-thumb img { width:100%;height:100%;object-fit:cover; }

/* Sidebar sticky */
.side-card { background:#0d1117;border-radius:14px;overflow:hidden;margin-bottom:14px;position:sticky;top:24px; }
.side-hd { padding:12px 16px;border-bottom:1px solid rgba(255,255,255,.07); }
.side-title { font-family:'Syne',sans-serif;font-size:12px;font-weight:700;color:#fff; }
.side-body { padding:14px 16px; }
.side-row { display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px solid rgba(255,255,255,.06);font-size:12px; }
.side-row:last-child { border-bottom:none; }
.side-lbl { color:rgba(255,255,255,.4); }
.side-val { color:#e6edf3;font-weight:500; }
.side-val.gold { color:#c9a84c; }
.badge { display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:99px;font-size:11px;font-weight:600; }
.badge-disponible { background:#dcfce7;color:#16a34a; }
.badge-loue       { background:#dbeafe;color:#1d4ed8; }
.badge-en_travaux { background:#fef9c3;color:#a16207; }
.badge-archive    { background:#f3f4f6;color:#6b7280; }
</style>

<div style="padding:0 0 48px">

    
    <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:#6b7280;margin-bottom:16px">
        <a href="<?php echo e(route('admin.biens.index')); ?>" style="color:#6b7280;text-decoration:none">Biens</a>
        <svg style="width:12px;height:12px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
        <span style="color:#0d1117;font-weight:500"><?php echo e($bien->reference); ?></span>
    </div>

    
    <div class="actions-bar">
        <a href="<?php echo e(route('admin.biens.edit', $bien)); ?>" class="btn-act btn-dark">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            Modifier
        </a>

        <?php if(!$bien->contratActif): ?>
        <a href="<?php echo e(route('admin.contrats.create', ['bien_id' => $bien->id])); ?>" class="btn-act btn-green">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><line x1="12" y1="11" x2="12" y2="17"/><line x1="9" y1="14" x2="15" y2="14"/></svg>
            Créer un contrat
        </a>
        <?php endif; ?>

        <?php if($bien->contratActif): ?>
        <a href="<?php echo e(route('admin.paiements.create', ['contrat_id' => $bien->contratActif->id])); ?>" class="btn-act btn-green">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
            Enregistrer un paiement
        </a>
        <a href="<?php echo e(route('admin.contrats.show', $bien->contratActif)); ?>" class="btn-act btn-outline">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            Voir le contrat
        </a>
        <?php endif; ?>

        <a href="<?php echo e(route('admin.biens.index')); ?>" class="btn-act btn-outline">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
            Retour
        </a>

        <?php if(!$bien->contratActif): ?>
        <form method="POST" action="<?php echo e(route('admin.biens.destroy', $bien)); ?>"
              onsubmit="return confirm('Archiver ce bien ?')">
            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
            <button type="submit" class="btn-act btn-red">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/></svg>
                Archiver
            </button>
        </form>
        <?php endif; ?>
    </div>

    
    <div class="hero">
        <div>
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:rgba(255,255,255,.3);margin-bottom:6px">
                <?php echo e($bien->reference); ?>

            </div>
            <div style="font-family:'Syne',sans-serif;font-size:22px;font-weight:700;color:#fff;margin-bottom:4px;letter-spacing:-.3px">
                <?php echo e($bien->type_label); ?>

                <?php if($bien->meuble): ?> <span style="font-size:13px;color:rgba(201,168,76,.7);font-weight:400">· Meublé</span> <?php endif; ?>
            </div>
            <div style="font-size:13px;color:rgba(255,255,255,.5)">
                <?php echo e($bien->adresse); ?>

                <?php if($bien->quartier): ?> · <?php echo e($bien->quartier); ?> <?php endif; ?>
                <?php if($bien->commune): ?> · <?php echo e($bien->commune); ?> <?php endif; ?>
                · <?php echo e($bien->ville); ?>

            </div>
            <div style="margin-top:10px">
                <span class="badge badge-<?php echo e($bien->statut); ?>">
                    <span style="width:5px;height:5px;border-radius:50%;background:currentColor"></span>
                    <?php echo e($bien->statut_label); ?>

                </span>
                <?php if($bien->surface_m2): ?>
                    <span style="margin-left:8px;font-size:12px;color:rgba(255,255,255,.4)"><?php echo e($bien->surface_m2); ?> m²</span>
                <?php endif; ?>
                <?php if($bien->nombre_pieces): ?>
                    <span style="margin-left:8px;font-size:12px;color:rgba(255,255,255,.4)"><?php echo e($bien->nombre_pieces); ?> pièces</span>
                <?php endif; ?>
            </div>
        </div>
        <div style="text-align:right">
            <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:rgba(201,168,76,.6);margin-bottom:4px">Loyer mensuel</div>
            <div style="font-family:'Syne',sans-serif;font-size:28px;font-weight:700;color:#c9a84c">
                <?php echo e(number_format($bien->loyer_mensuel, 0, ',', ' ')); ?><span style="font-size:14px;color:rgba(201,168,76,.5);margin-left:4px">F</span>
            </div>
            <div style="font-size:11px;color:rgba(255,255,255,.3);margin-top:3px">
                Commission <?php echo e($bien->taux_commission ?? 10); ?>%
            </div>
        </div>
    </div>

    
    <div class="kpi-row">
        <div class="kpi gold">
            <div class="kpi-lbl">Loyer mensuel</div>
            <div class="kpi-val"><?php echo e(number_format($bien->loyer_mensuel, 0, ',', ' ')); ?></div>
            <div class="kpi-sub">FCFA / mois</div>
        </div>
        <div class="kpi green">
            <div class="kpi-lbl">Net propriétaire</div>
            <div class="kpi-val" style="color:#16a34a">
                <?php echo e(number_format($bien->loyer_mensuel * (1 - ($bien->taux_commission ?? 10) / 100), 0, ',', ' ')); ?>

            </div>
            <div class="kpi-sub">FCFA après commission</div>
        </div>
        <div class="kpi blue">
            <div class="kpi-lbl">Contrats</div>
            <div class="kpi-val" style="color:#1d4ed8"><?php echo e($bien->contrats->count()); ?></div>
            <div class="kpi-sub"><?php echo e($bien->contratActif ? '1 actif' : 'Aucun actif'); ?></div>
        </div>
    </div>

    <div class="page-grid">

        
        <div>

            
            <?php if($bien->photos->count() > 0): ?>
            <div class="card">
                <div class="card-hd">
                    <div class="card-hd-left">
                        <div class="card-icon green">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                        </div>
                        <div class="card-title">Photos (<?php echo e($bien->photos->count()); ?>)</div>
                    </div>
                    <a href="<?php echo e(route('admin.biens.edit', $bien)); ?>" style="font-size:12px;color:#6b7280;text-decoration:none">
                        Gérer les photos →
                    </a>
                </div>
                <div class="card-body">
                    <?php $principale = $bien->photos->firstWhere('est_principale', true) ?? $bien->photos->first(); ?>
                    <div class="photo-hero">
                        <img src="<?php echo e(asset('storage/'.$principale->chemin)); ?>"
                             id="photo-principale" alt="Photo principale">
                    </div>
                    <?php if($bien->photos->count() > 1): ?>
                    <div class="photo-thumbs">
                        <?php $__currentLoopData = $bien->photos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $photo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="photo-thumb <?php echo e($photo->id === $principale->id ? 'border-amber-400':''); ?>"
                             onclick="changerPhoto('<?php echo e(asset('storage/'.$photo->chemin)); ?>', this)">
                            <img src="<?php echo e(asset('storage/'.$photo->chemin)); ?>" alt="">
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            
            <div class="card">
                <div class="card-hd">
                    <div class="card-hd-left">
                        <div class="card-icon blue">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                        </div>
                        <div class="card-title">Informations</div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="info-grid-3" style="margin-bottom:16px">
                        <div>
                            <div class="il">Type</div>
                            <div class="iv"><?php echo e($bien->type_label); ?></div>
                        </div>
                        <div>
                            <div class="il">Surface</div>
                            <div class="iv"><?php echo e($bien->surface_m2 ? $bien->surface_m2.' m²' : '—'); ?></div>
                        </div>
                        <div>
                            <div class="il">Pièces</div>
                            <div class="iv"><?php echo e($bien->nombre_pieces ?? '—'); ?></div>
                        </div>
                        <div>
                            <div class="il">Meublé</div>
                            <div class="iv"><?php echo e($bien->meuble ? 'Oui' : 'Non'); ?></div>
                        </div>
                        <div>
                            <div class="il">Statut</div>
                            <div class="iv"><?php echo e($bien->statut_label); ?></div>
                        </div>
                        <div>
                            <div class="il">Référence</div>
                            <div class="iv" style="font-family:'Syne',sans-serif;font-size:12px"><?php echo e($bien->reference); ?></div>
                        </div>
                    </div>

                    <div style="padding-top:14px;border-top:1px solid #f3f4f6">
                        <div class="il" style="margin-bottom:8px">Localisation</div>
                        <div class="info-grid">
                            <div>
                                <div class="il">Adresse</div>
                                <div class="iv"><?php echo e($bien->adresse); ?></div>
                            </div>
                            <div>
                                <div class="il">Quartier</div>
                                <div class="iv"><?php echo e($bien->quartier ?? '—'); ?></div>
                            </div>
                            <div>
                                <div class="il">Commune</div>
                                <div class="iv"><?php echo e($bien->commune ?? '—'); ?></div>
                            </div>
                            <div>
                                <div class="il">Ville</div>
                                <div class="iv"><?php echo e($bien->ville); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            
            <?php if($bien->contratActif): ?>
            <div class="card">
                <div class="card-hd">
                    <div class="card-hd-left">
                        <div class="card-icon gold">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                        </div>
                        <div class="card-title">Contrat actif</div>
                    </div>
                    <a href="<?php echo e(route('admin.contrats.show', $bien->contratActif)); ?>"
                       style="font-size:12px;color:#6b7280;text-decoration:none">
                        Voir le contrat →
                    </a>
                </div>
                <div class="card-body">
                    <div class="info-grid-3">
                        <div>
                            <div class="il">Locataire</div>
                            <div class="iv"><?php echo e($bien->contratActif->locataire?->name ?? '—'); ?></div>
                            <div class="iv-sub"><?php echo e($bien->contratActif->locataire?->telephone ?? ''); ?></div>
                        </div>
                        <div>
                            <div class="il">Loyer contractuel</div>
                            <div class="iv" style="color:#c9a84c;font-weight:700">
                                <?php echo e(number_format($bien->contratActif->loyer_contractuel, 0, ',', ' ')); ?> F
                            </div>
                        </div>
                        <div>
                            <div class="il">Début</div>
                            <div class="iv"><?php echo e($bien->contratActif->date_debut?->format('d/m/Y')); ?></div>
                            <div class="iv-sub">
                                <?php echo e($bien->contratActif->date_fin?->format('d/m/Y') ?? 'Contrat ouvert'); ?>

                            </div>
                        </div>
                        <div>
                            <div class="il">Type bail</div>
                            <div class="iv">
                                <?php echo e(\App\Models\Contrat::TYPES_BAIL[$bien->contratActif->type_bail] ?? $bien->contratActif->type_bail); ?>

                            </div>
                        </div>
                        <div>
                            <div class="il">Référence bail</div>
                            <div class="iv" style="font-size:11px">
                                <?php echo e($bien->contratActif->reference_bail ?? 'BAIL-'.$bien->contratActif->id); ?>

                            </div>
                        </div>
                        <div>
                            <div class="il">Caution</div>
                            <div class="iv"><?php echo e(number_format($bien->contratActif->caution, 0, ',', ' ')); ?> F</div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            
            <div class="card">
                <div class="card-hd">
                    <div class="card-hd-left">
                        <div class="card-icon green">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                        </div>
                        <div class="card-title">Derniers paiements</div>
                    </div>
                    <?php if($bien->contratActif): ?>
                    <a href="<?php echo e(route('admin.paiements.create', ['contrat_id' => $bien->contratActif->id])); ?>"
                       style="display:inline-flex;align-items:center;gap:4px;font-size:12px;color:#16a34a;text-decoration:none;padding:5px 10px;background:#dcfce7;border-radius:6px">
                        + Paiement
                    </a>
                    <?php endif; ?>
                </div>

                <?php
                    $paiements = $bien->contratActif
                        ? \App\Models\Paiement::where('contrat_id', $bien->contratActif->id)
                            ->where('statut', 'valide')
                            ->select(['id', 'contrat_id', 'periode', 'date_paiement', 'montant_encaisse', 'net_proprietaire', 'commission_ttc', 'mode_paiement', 'statut'])
                            ->orderByDesc('periode')
                            ->limit(10)
                            ->get()
                        : collect();
                ?>

                <?php if($paiements->isEmpty()): ?>
                <div style="padding:28px;text-align:center;color:#9ca3af;font-size:13px">
                    <?php if($bien->contratActif): ?>
                        Aucun paiement enregistré pour ce contrat.
                    <?php else: ?>
                        Aucun contrat actif sur ce bien.
                    <?php endif; ?>
                </div>
                <?php else: ?>
                <div style="overflow-x:auto">
                    <table class="dt">
                        <thead>
                            <tr>
                                <th>Période</th>
                                <th>Date</th>
                                <th style="text-align:right">Montant</th>
                                <th style="text-align:right">Net proprio</th>
                                <th>Mode</th>
                                <th style="text-align:center">PDF</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $paiements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td>
                                    <span style="display:inline-flex;padding:2px 8px;background:#f5e9c9;color:#8a6e2f;border-radius:99px;font-size:11px;font-weight:600;font-family:'Syne',sans-serif">
                                        <?php echo e(\Carbon\Carbon::parse($p->periode)->translatedFormat('M Y')); ?>

                                    </span>
                                </td>
                                <td style="font-size:12px;color:#6b7280">
                                    <?php echo e($p->date_paiement ? \Carbon\Carbon::parse($p->date_paiement)->format('d/m/Y') : '—'); ?>

                                </td>
                                <td style="text-align:right;font-family:'Syne',sans-serif;font-weight:700;color:#0d1117">
                                    <?php echo e(number_format($p->montant_encaisse, 0, ',', ' ')); ?> F
                                </td>
                                <td style="text-align:right;color:#16a34a;font-weight:600">
                                    <?php echo e(number_format($p->net_proprietaire ?? 0, 0, ',', ' ')); ?> F
                                </td>
                                <td style="font-size:12px;color:#6b7280">
                                    <?php echo e(\App\Http\Controllers\PaiementController::MODES_PAIEMENT[$p->mode_paiement] ?? $p->mode_paiement); ?>

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
                <?php endif; ?>
            </div>

            
            <?php if($bien->description): ?>
            <div class="card">
                <div class="card-hd">
                    <div class="card-hd-left">
                        <div class="card-icon purple">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                        </div>
                        <div class="card-title">Description</div>
                    </div>
                </div>
                <div class="card-body">
                    <p style="font-size:13px;color:#374151;line-height:1.7"><?php echo e($bien->description); ?></p>
                </div>
            </div>
            <?php endif; ?>

        </div>

        
        <div>

            
            <div class="side-card">
                <div class="side-hd"><div class="side-title">Propriétaire</div></div>
                <div class="side-body">
                    <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px">
                        <div style="width:36px;height:36px;border-radius:50%;background:rgba(201,168,76,.15);border:1.5px solid rgba(201,168,76,.3);display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:700;color:#c9a84c;flex-shrink:0">
                            <?php echo e(strtoupper(substr($bien->proprietaire?->name ?? 'P', 0, 1))); ?>

                        </div>
                        <div>
                            <div style="font-size:13px;font-weight:600;color:#e6edf3">
                                <?php echo e($bien->proprietaire?->name ?? '—'); ?>

                            </div>
                            <div style="font-size:11px;color:#484f58"><?php echo e($bien->proprietaire?->email ?? ''); ?></div>
                        </div>
                    </div>
                    <?php if($bien->proprietaire?->telephone): ?>
                    <div class="side-row">
                        <span class="side-lbl">Téléphone</span>
                        <span class="side-val"><?php echo e($bien->proprietaire->telephone); ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if($bien->proprietaire): ?>
                    <a href="<?php echo e(route('admin.users.show', $bien->proprietaire)); ?>"
                       style="display:flex;align-items:center;justify-content:center;gap:5px;margin-top:10px;padding:7px;border:1px solid rgba(255,255,255,.1);border-radius:8px;color:#c9a84c;font-size:12px;text-decoration:none">
                        Voir le profil →
                    </a>
                    <?php endif; ?>
                </div>
            </div>

            
            <div class="side-card">
                <div class="side-hd"><div class="side-title">Récapitulatif</div></div>
                <div class="side-body">
                    <div class="side-row">
                        <span class="side-lbl">Référence</span>
                        <span class="side-val" style="font-family:'Syne',sans-serif;font-size:11px"><?php echo e($bien->reference); ?></span>
                    </div>
                    <div class="side-row">
                        <span class="side-lbl">Loyer mensuel</span>
                        <span class="side-val gold"><?php echo e(number_format($bien->loyer_mensuel, 0, ',', ' ')); ?> F</span>
                    </div>
                    <div class="side-row">
                        <span class="side-lbl">Commission</span>
                        <span class="side-val"><?php echo e($bien->taux_commission ?? 10); ?> %</span>
                    </div>
                    <div class="side-row">
                        <span class="side-lbl">Net proprio</span>
                        <span class="side-val" style="color:#4ade80">
                            <?php echo e(number_format($bien->loyer_mensuel * (1 - ($bien->taux_commission ?? 10) / 100), 0, ',', ' ')); ?> F
                        </span>
                    </div>
                    <div class="side-row">
                        <span class="side-lbl">Statut</span>
                        <span class="side-val"><?php echo e($bien->statut_label); ?></span>
                    </div>
                    <div class="side-row">
                        <span class="side-lbl">Surface</span>
                        <span class="side-val"><?php echo e($bien->surface_m2 ? $bien->surface_m2.' m²' : '—'); ?></span>
                    </div>
                    <div class="side-row">
                        <span class="side-lbl">Pièces</span>
                        <span class="side-val"><?php echo e($bien->nombre_pieces ?? '—'); ?></span>
                    </div>
                    <div class="side-row">
                        <span class="side-lbl">Meublé</span>
                        <span class="side-val"><?php echo e($bien->meuble ? 'Oui' : 'Non'); ?></span>
                    </div>
                    <div class="side-row">
                        <span class="side-lbl">Ajouté le</span>
                        <span class="side-val"><?php echo e($bien->created_at?->format('d/m/Y')); ?></span>
                    </div>
                </div>
            </div>

            
            <?php if($bien->contrats->count() > 0): ?>
            <div class="side-card">
                <div class="side-hd"><div class="side-title">Historique contrats</div></div>
                <div class="side-body">
                    <?php $__currentLoopData = $bien->contrats->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div style="padding:8px 0;border-bottom:1px solid rgba(255,255,255,.06)">
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:3px">
                            <span style="font-size:12px;color:#e6edf3;font-weight:500">
                                <?php echo e($c->locataire?->name ?? '—'); ?>

                            </span>
                            <?php
                                $cs = match($c->statut) {
                                    'actif'   => 'color:#4ade80',
                                    'resilié' => 'color:#f87171',
                                    default   => 'color:#9ca3af',
                                };
                            ?>
                            <span style="font-size:10px;font-weight:600;<?php echo e($cs); ?>">
                                <?php echo e(\App\Models\Contrat::STATUTS[$c->statut] ?? $c->statut); ?>

                            </span>
                        </div>
                        <div style="font-size:11px;color:rgba(255,255,255,.3)">
                            <?php echo e($c->date_debut?->format('d/m/Y')); ?> → <?php echo e($c->date_fin?->format('d/m/Y') ?? 'En cours'); ?>

                        </div>
                        <a href="<?php echo e(route('admin.contrats.show', $c)); ?>"
                           style="font-size:11px;color:#c9a84c;text-decoration:none">
                            Voir →
                        </a>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
            <?php endif; ?>

        </div>

    </div>

</div>

<script>
function changerPhoto(url, thumb) {
    document.getElementById('photo-principale').src = url;
    document.querySelectorAll('.photo-thumb').forEach(t => {
        t.style.borderColor = 'transparent';
    });
    thumb.style.borderColor = '#c9a84c';
}
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ph\bimotech-immo\resources\views/biens/show.blade.php ENDPATH**/ ?>