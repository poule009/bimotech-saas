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
     <?php $__env->slot('header', null, []); ?> Tableau de bord <?php $__env->endSlot(); ?>

<style>
/* ── KPI ── */
.kpi-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:16px; margin-bottom:24px; }
.kpi-card { background:#fff; border:1px solid #e5e7eb; border-radius:14px; padding:22px 22px 18px; position:relative; overflow:hidden; transition:transform .2s,box-shadow .2s; }
.kpi-card:hover { transform:translateY(-2px); box-shadow:0 8px 24px -6px rgba(0,0,0,0.08); }
.kpi-card::before { content:''; position:absolute; top:0;left:0;right:0; height:3px; border-radius:14px 14px 0 0; }
.kpi-card.gold::before  { background:#c9a84c; }
.kpi-card.green::before { background:#16a34a; }
.kpi-card.red::before   { background:#dc2626; }
.kpi-card.blue::before  { background:#1d4ed8; }
.kpi-icon { width:36px;height:36px; border-radius:9px; display:flex;align-items:center;justify-content:center; margin-bottom:14px; }
.kpi-card.gold  .kpi-icon { background:#f5e9c9; }
.kpi-card.green .kpi-icon { background:#dcfce7; }
.kpi-card.red   .kpi-icon { background:#fee2e2; }
.kpi-card.blue  .kpi-icon { background:#dbeafe; }
.kpi-icon svg { width:18px;height:18px; }
.kpi-card.gold  .kpi-icon svg { color:#8a6e2f; }
.kpi-card.green .kpi-icon svg { color:#16a34a; }
.kpi-card.red   .kpi-icon svg { color:#dc2626; }
.kpi-card.blue  .kpi-icon svg { color:#1d4ed8; }
.kpi-label { font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.8px;color:#6b7280;margin-bottom:5px; }
.kpi-value { font-family:'Syne',sans-serif;font-size:24px;font-weight:700;color:#0d1117;letter-spacing:-.5px;line-height:1; }
.kpi-unit  { font-size:12px;font-weight:400;color:#9ca3af;margin-left:3px; }
.kpi-sub   { font-size:11px;color:#9ca3af;margin-top:7px; }
.t-up   { color:#16a34a;font-weight:600; }
.t-down { color:#dc2626;font-weight:600; }

/* ── BILAN ── */
.bilan { background:#0d1117;border-radius:16px;padding:28px 32px;margin-bottom:24px;display:grid;grid-template-columns:1fr 1fr 1fr;position:relative;overflow:hidden; }
.bilan::before { content:'';position:absolute;top:-60px;right:-60px;width:200px;height:200px;border-radius:50%;background:rgba(201,168,76,0.07); }
.bilan-col { padding:0 28px;border-right:1px solid rgba(255,255,255,.07);position:relative;z-index:1; }
.bilan-col:first-child { padding-left:0; }
.bilan-col:last-child  { padding-right:0;border-right:none; }
.bilan-lbl { font-size:10px;font-weight:600;letter-spacing:1.5px;text-transform:uppercase;color:rgba(255,255,255,.4);margin-bottom:10px; }
.bilan-val { font-family:'Syne',sans-serif;font-size:28px;font-weight:700;letter-spacing:-.5px;line-height:1;color:white; }
.bilan-val.g { color:#4ade80; }
.bilan-val.r { color:#f87171; }
.bilan-u { font-size:13px;color:rgba(255,255,255,.3);margin-left:4px; }
.bilan-tag { display:inline-flex;align-items:center;gap:4px;margin-top:10px;padding:3px 10px;border-radius:99px;font-size:11px;font-weight:600; }
.bilan-tag.g { background:rgba(74,222,128,.12);color:#4ade80; }
.bilan-tag.r { background:rgba(248,113,113,.12);color:#f87171; }
.bilan-tag.m { background:rgba(255,255,255,.06);color:rgba(255,255,255,.4); }

/* ── CARD ── */
.card { background:#fff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden;margin-bottom:0; }
.card-hd { padding:18px 22px;border-bottom:1px solid #e5e7eb;display:flex;align-items:center;justify-content:space-between; }
.card-title { font-family:'Syne',sans-serif;font-size:14px;font-weight:700;color:#0d1117; }
.card-act { font-size:12px;color:#6b7280;text-decoration:none;transition:color .15s; }
.card-act:hover { color:#0d1117; }

/* ── TABLE ── */
.dt { width:100%;border-collapse:collapse; }
.dt th { padding:9px 18px;text-align:left;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#9ca3af;background:#f9fafb;border-bottom:1px solid #e5e7eb; }
.dt td { padding:13px 18px;font-size:13px;color:#374151;border-bottom:1px solid #f3f4f6;vertical-align:middle; }
.dt tbody tr:last-child td { border-bottom:none; }
.dt tbody tr:hover { background:#f9fafb; }
.ref { font-family:'Syne',sans-serif;font-size:11px;font-weight:600;color:#9ca3af; }
.amt { font-family:'Syne',sans-serif;font-weight:600;color:#0d1117; }
.badge { display:inline-flex;align-items:center;gap:4px;padding:3px 9px;border-radius:99px;font-size:11px;font-weight:600; }
.badge.g { background:#dcfce7;color:#16a34a; }
.badge.r { background:#fee2e2;color:#dc2626; }
.badge.o { background:#f5e9c9;color:#8a6e2f; }
.bdot { width:5px;height:5px;border-radius:50%;background:currentColor; }

/* ── IMPAYÉS ── */
.imp-item { display:flex;align-items:center;gap:12px;padding:13px 18px;border-bottom:1px solid #f3f4f6;transition:background .1s; }
.imp-item:last-child { border-bottom:none; }
.imp-item:hover { background:#f9fafb; }
.imp-av { width:34px;height:34px;border-radius:8px;background:#fee2e2;display:flex;align-items:center;justify-content:center;font-family:'Syne',sans-serif;font-size:11px;font-weight:700;color:#dc2626;flex-shrink:0; }
.imp-ref  { font-size:13px;font-weight:500;color:#0d1117; }
.imp-name { font-size:11px;color:#6b7280; }
.imp-amt  { font-family:'Syne',sans-serif;font-size:13px;font-weight:600;color:#dc2626;white-space:nowrap; }

/* ── GRILLES ── */
.g2 { display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px; }
.g3 { display:grid;grid-template-columns:2fr 1fr;gap:20px;margin-bottom:20px; }
.g4 { display:grid;grid-template-columns:1fr 360px;gap:20px; }

/* ── TABS ── */
.tabs { display:flex;gap:3px;padding:4px;background:#f3f4f6;border-radius:10px;width:fit-content; }
.tab { padding:6px 14px;border-radius:7px;font-size:12px;font-weight:500;color:#6b7280;cursor:pointer;border:none;background:none;font-family:'DM Sans',sans-serif;transition:all .15s; }
.tab.active { background:#fff;color:#0d1117;box-shadow:0 1px 4px rgba(0,0,0,0.08); }

/* ── PROGRESS BAR ── */
.prog-wrap { height:6px;background:#f3f4f6;border-radius:99px;overflow:hidden; }
.prog-fill { height:100%;background:linear-gradient(90deg,#16a34a,#4ade80);border-radius:99px;transition:width 1s ease; }

/* Graphique */
.chart-meta { padding:18px 22px 8px;display:flex;align-items:baseline;justify-content:space-between; }
.chart-num  { font-family:'Syne',sans-serif;font-size:22px;font-weight:700;color:#0d1117;letter-spacing:-.5px; }
.chart-sub  { font-size:11px;color:#9ca3af;margin-top:2px; }
.donut-legend { display:flex;flex-wrap:wrap;gap:10px 18px;padding:10px 22px 18px; }
.donut-leg { display:flex;align-items:center;gap:6px;font-size:12px;color:#6b7280; }
.donut-dot { width:10px;height:10px;border-radius:3px;flex-shrink:0; }
</style>

<div style="padding:24px 32px 48px">

    
    <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:24px">
        <div>
            <h1 style="font-family:'Syne',sans-serif;font-size:22px;font-weight:700;color:#0d1117;letter-spacing:-.4px">Tableau de bord</h1>
            <p style="font-size:13px;color:#6b7280;margin-top:3px">
                Bilan de <?php echo e(now()->translatedFormat('F Y')); ?> · <?php echo e($currentAgency->name ?? 'Votre agence'); ?>

            </p>
        </div>
        <div style="display:flex;align-items:center;gap:10px">
            <div class="tabs">
                <button class="tab active">Ce mois</button>
                <button class="tab">Trimestre</button>
                <button class="tab">Année</button>
            </div>
            <a href="<?php echo e(route('admin.rapports.financier.export-pdf')); ?>" class="btn-outline">
                <svg style="width:13px;height:13px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Exporter PDF
            </a>
        </div>
    </div>

    
    <div class="kpi-grid">
        <div class="kpi-card gold">
            <div class="kpi-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg></div>
            <div class="kpi-label">Loyers encaissés</div>
            <div class="kpi-value"><?php echo e(number_format($statsMois['loyers'], 0, ',', ' ')); ?><span class="kpi-unit">F</span></div>
            <div class="kpi-sub"><?php echo e($statsMois['nb_payes']); ?> paiements ce mois</div>
        </div>
        <div class="kpi-card green">
            <div class="kpi-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg></div>
            <div class="kpi-label">Taux d'occupation</div>
            <div class="kpi-value"><?php echo e($stats['taux_occupation']); ?><span class="kpi-unit">%</span></div>
            <div class="kpi-sub"><?php echo e($stats['nb_biens_loues']); ?> / <?php echo e($stats['nb_biens']); ?> biens loués</div>
        </div>
        <div class="kpi-card red">
            <div class="kpi-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg></div>
            <div class="kpi-label">Impayés ce mois</div>
            <div class="kpi-value"><?php echo e($nb_impayes_mois); ?><span class="kpi-unit">contrats</span></div>
            <div class="kpi-sub"><span class="t-down"><?php echo e(number_format($montant_du_mois, 0, ',', ' ')); ?> F</span> à recouvrer</div>
        </div>
        <div class="kpi-card blue">
            <div class="kpi-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></div>
            <div class="kpi-label">Commission agence</div>
            <div class="kpi-value"><?php echo e(number_format($statsMois['commissions'], 0, ',', ' ')); ?><span class="kpi-unit">F</span></div>
            <div class="kpi-sub">TTC · <?php echo e($stats['nb_contrats']); ?> contrats actifs</div>
        </div>
    </div>

    
    <div class="bilan">
        <div class="bilan-col">
            <div class="bilan-lbl">Attendu ce mois</div>
            <div class="bilan-val"><?php echo e(number_format($bilanMois['attendu'], 0, ',', ' ')); ?><span class="bilan-u">F</span></div>
            <div class="bilan-tag m"><?php echo e($contratsActifs); ?> contrats actifs</div>
        </div>
        <div class="bilan-col">
            <div class="bilan-lbl">Encaissé</div>
            <div class="bilan-val g"><?php echo e(number_format($bilanMois['encaisse'], 0, ',', ' ')); ?><span class="bilan-u">F</span></div>
            <div class="bilan-tag g">✓ <?php echo e($bilanMois['attendu'] > 0 ? round(($bilanMois['encaisse'] / $bilanMois['attendu']) * 100) : 0); ?>% de recouvrement</div>
        </div>
        <div class="bilan-col">
            <div class="bilan-lbl">Reliquat</div>
            <div class="bilan-val <?php echo e($bilanMois['a_recouvrer'] > 0 ? 'r' : 'g'); ?>"><?php echo e(number_format($bilanMois['a_recouvrer'], 0, ',', ' ')); ?><span class="bilan-u">F</span></div>
            <?php if($bilanMois['a_recouvrer'] > 0): ?>
                <div class="bilan-tag r">⚠ <?php echo e($nb_impayes_mois); ?> contrats en défaut</div>
            <?php else: ?>
                <div class="bilan-tag g">✓ Recouvrement complet</div>
            <?php endif; ?>
        </div>
    </div>

    
    <div class="g2">

        
        <div class="card">
            <div class="card-hd">
                <div>
                    <div class="card-title">Évolution des loyers</div>
                    <div style="font-size:11px;color:#9ca3af;margin-top:2px">6 derniers mois</div>
                </div>
                <div style="display:flex;gap:14px">
                    <div style="display:flex;align-items:center;gap:5px;font-size:11px;color:#9ca3af"><div style="width:10px;height:10px;border-radius:2px;background:#c9a84c"></div>Loyers</div>
                    <div style="display:flex;align-items:center;gap:5px;font-size:11px;color:#9ca3af"><div style="width:10px;height:10px;border-radius:2px;background:#e5e7eb"></div>Commissions</div>
                </div>
            </div>
            <div class="chart-meta">
                <div>
                    <div class="chart-num"><?php echo e(number_format($statsMois['loyers'], 0, ',', ' ')); ?> F</div>
                    <div class="chart-sub">Encaissés ce mois</div>
                </div>
                <span class="badge g"><span class="bdot"></span>Ce mois</span>
            </div>
            <div style="position:relative;height:200px;padding:0 20px 20px">
                <canvas id="chartLoyers"></canvas>
            </div>
        </div>

        
        <div class="card">
            <div class="card-hd">
                <div class="card-title">Répartition des biens</div>
            </div>
            <div style="position:relative;height:180px;padding:20px 20px 0;display:flex;align-items:center;justify-content:center">
                <canvas id="chartTypes"></canvas>
            </div>
            <div class="donut-legend">
                <div class="donut-leg"><div class="donut-dot" style="background:#c9a84c"></div>Appartements</div>
                <div class="donut-leg"><div class="donut-dot" style="background:#0d1117"></div>Villas</div>
                <div class="donut-leg"><div class="donut-dot" style="background:#16a34a"></div>Bureaux</div>
                <div class="donut-leg"><div class="donut-dot" style="background:#9ca3af"></div>Autres</div>
            </div>
        </div>
    </div>

    
    <div class="g3">

        
        <div class="card">
            <div class="card-hd">
                <div class="card-title">Net reversé par propriétaire</div>
                <a href="<?php echo e(route('admin.rapports.financier')); ?>" class="card-act">Rapport détaillé →</a>
            </div>
            <div style="position:relative;height:220px;padding:16px 20px">
                <canvas id="chartProprio"></canvas>
            </div>
        </div>

        
        <div class="card">
            <div class="card-hd"><div class="card-title">Statut paiements</div></div>
            <div style="position:relative;height:140px;padding:16px 20px;display:flex;align-items:center;justify-content:center">
                <canvas id="chartStatuts"></canvas>
            </div>
            <div style="padding:4px 20px 16px;display:flex;flex-direction:column;gap:8px">
                <div style="display:flex;justify-content:space-between;align-items:center">
                    <div style="display:flex;align-items:center;gap:6px;font-size:12px;color:#6b7280"><div style="width:8px;height:8px;border-radius:2px;background:#16a34a"></div>Validés</div>
                    <span style="font-family:'Syne',sans-serif;font-size:13px;font-weight:600;color:#0d1117"><?php echo e($statsMois['nb_payes']); ?></span>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center">
                    <div style="display:flex;align-items:center;gap:6px;font-size:12px;color:#6b7280"><div style="width:8px;height:8px;border-radius:2px;background:#dc2626"></div>Impayés</div>
                    <span style="font-family:'Syne',sans-serif;font-size:13px;font-weight:600;color:#dc2626"><?php echo e($nb_impayes_mois); ?></span>
                </div>
            </div>
        </div>
    </div>

    
    <div class="g4">

        
        <div class="card">
            <div class="card-hd">
                <div class="card-title">Derniers paiements</div>
                <a href="<?php echo e(route('admin.paiements.index')); ?>" class="card-act">Voir tout →</a>
            </div>
            <table class="dt">
                <thead>
                    <tr>
                        <th>Référence</th>
                        <th>Bien / Locataire</th>
                        <th>Mode</th>
                        <th style="text-align:right">Montant</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $derniersPaiements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><span class="ref"><?php echo e($p->reference_paiement); ?></span></td>
                        <td>
                            <div style="font-weight:500;font-size:13px"><?php echo e($p->contrat?->bien?->reference ?? '—'); ?></div>
                            <div style="font-size:11px;color:#6b7280"><?php echo e($p->contrat?->locataire?->name ?? '—'); ?></div>
                        </td>
                        <td style="font-size:12px;color:#6b7280">
                            <?php echo e(\App\Models\Paiement::MODES_PAIEMENT[$p->mode_paiement] ?? $p->mode_paiement); ?>

                        </td>
                        <td style="text-align:right"><span class="amt"><?php echo e(number_format($p->montant_encaisse, 0, ',', ' ')); ?> F</span></td>
                        <td>
                            <?php if($p->statut === 'valide'): ?>
                                <span class="badge g"><span class="bdot"></span>Validé</span>
                            <?php elseif($p->statut === 'annule'): ?>
                                <span class="badge r"><span class="bdot"></span>Annulé</span>
                            <?php else: ?>
                                <span class="badge o"><span class="bdot"></span>En attente</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="5" style="text-align:center;padding:32px;color:#9ca3af;font-size:13px">
                            Aucun paiement enregistré ce mois
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        
        <div style="display:flex;flex-direction:column;gap:20px">
            <div class="card">
                <div class="card-hd">
                    <div class="card-title">Impayés urgents</div>
                    <a href="<?php echo e(route('admin.impayes.index')); ?>" class="card-act" style="color:#dc2626">Relancer →</a>
                </div>

                <?php $__empty_1 = true; $__currentLoopData = $impayes_urgents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="imp-item">
                    <div class="imp-av"><?php echo e(strtoupper(substr($c->locataire?->name ?? 'X', 0, 2))); ?></div>
                    <div style="flex:1;min-width:0">
                        <div class="imp-ref"><?php echo e($c->bien?->reference ?? '—'); ?></div>
                        <div class="imp-name"><?php echo e($c->locataire?->name ?? '—'); ?></div>
                    </div>
                    <div class="imp-amt"><?php echo e(number_format($c->loyer_contractuel, 0, ',', ' ')); ?> F</div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div style="padding:24px;text-align:center;color:#16a34a;font-size:13px">
                    ✓ Aucun impayé ce mois
                </div>
                <?php endif; ?>

                
                <div style="padding:14px 18px;border-top:1px solid #f3f4f6">
                    <?php $taux = $bilanMois['attendu'] > 0 ? round(($bilanMois['encaisse'] / $bilanMois['attendu']) * 100) : 0; ?>
                    <div style="display:flex;justify-content:space-between;margin-bottom:7px">
                        <span style="font-size:12px;color:#6b7280;font-weight:500">Taux de recouvrement</span>
                        <span style="font-family:'Syne',sans-serif;font-size:13px;font-weight:700;color:#16a34a"><?php echo e($taux); ?>%</span>
                    </div>
                    <div class="prog-wrap">
                        <div class="prog-fill" style="width:<?php echo e($taux); ?>%"></div>
                    </div>
                    <div style="display:flex;justify-content:space-between;margin-top:5px">
                        <span style="font-size:10px;color:#9ca3af"><?php echo e(number_format($bilanMois['encaisse'], 0, ',', ' ')); ?> F encaissés</span>
                        <span style="font-size:10px;color:#9ca3af"><?php echo e(number_format($bilanMois['attendu'], 0, ',', ' ')); ?> F attendus</span>
                    </div>
                </div>
            </div>

            
            <?php if($contrats_a_renouveler->isNotEmpty()): ?>
            <div class="card">
                <div class="card-hd">
                    <div class="card-title">À renouveler bientôt</div>
                </div>
                <?php $__currentLoopData = $contrats_a_renouveler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div style="display:flex;align-items:center;gap:12px;padding:12px 18px;border-bottom:1px solid #f3f4f6">
                    <div style="width:38px;height:38px;border-radius:9px;background:#fef3c7;display:flex;flex-direction:column;align-items:center;justify-content:center;flex-shrink:0">
                        <div style="font-family:'Syne',sans-serif;font-size:14px;font-weight:800;color:#d97706;line-height:1">
                            <?php echo e(\Carbon\Carbon::parse($c->date_fin)->diffInDays(now())); ?>

                        </div>
                        <div style="font-size:8px;color:#d97706;font-weight:600;letter-spacing:.5px">jours</div>
                    </div>
                    <div style="flex:1;min-width:0">
                        <div style="font-size:13px;font-weight:500;color:#0d1117"><?php echo e($c->bien?->reference ?? '—'); ?></div>
                        <div style="font-size:11px;color:#6b7280"><?php echo e($c->locataire?->name ?? '—'); ?></div>
                    </div>
                    <div style="font-size:11px;color:#9ca3af;white-space:nowrap">
                        <?php echo e(\Carbon\Carbon::parse($c->date_fin)->format('d/m/Y')); ?>

                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

</div>


<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js"></script>
<script>
Chart.defaults.font.family = "'DM Sans', sans-serif";
Chart.defaults.color = '#9ca3af';

// ── Données depuis Blade → JS ──────────────────────────────────────
const loyersData = <?php echo json_encode($loyersParMois->pluck('total'), 15, 512) ?>;
const commData   = <?php echo json_encode($loyersParMois->pluck('commission'), 15, 512) ?>;
const moisLabels = <?php echo json_encode($loyersParMois->pluck('mois'), 15, 512) ?>;

// ── Tooltip commun ─────────────────────────────────────────────────
const tooltipStyle = {
    backgroundColor: '#0d1117',
    titleColor: '#fff',
    bodyColor: '#9ca3af',
    borderColor: '#1c2333',
    borderWidth: 1,
    padding: 11,
    cornerRadius: 8,
};

// ── 1. ÉVOLUTION LOYERS (area) ─────────────────────────────────────
const ctx1 = document.getElementById('chartLoyers').getContext('2d');
const gradGold = ctx1.createLinearGradient(0, 0, 0, 180);
gradGold.addColorStop(0, 'rgba(201,168,76,0.22)');
gradGold.addColorStop(1, 'rgba(201,168,76,0.00)');
const gradGray = ctx1.createLinearGradient(0, 0, 0, 180);
gradGray.addColorStop(0, 'rgba(229,231,235,0.7)');
gradGray.addColorStop(1, 'rgba(229,231,235,0.0)');

new Chart(ctx1, {
    type: 'line',
    data: {
        labels: moisLabels,
        datasets: [
            {
                label: 'Loyers',
                data: loyersData,
                borderColor: '#c9a84c',
                backgroundColor: gradGold,
                borderWidth: 2.5,
                pointBackgroundColor: '#c9a84c',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6,
                fill: true,
                tension: 0.4,
            },
            {
                label: 'Commission',
                data: commData,
                borderColor: '#d1d5db',
                backgroundColor: gradGray,
                borderWidth: 2,
                pointBackgroundColor: '#d1d5db',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 3,
                pointHoverRadius: 5,
                fill: true,
                tension: 0.4,
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: { ...tooltipStyle, callbacks: { label: c => ' ' + Number(c.parsed.y).toLocaleString('fr-FR') + ' F' } }
        },
        scales: {
            x: { grid: { display: false }, border: { display: false }, ticks: { font: { size: 11 } } },
            y: {
                grid: { color: '#f3f4f6', drawTicks: false },
                border: { display: false },
                ticks: { font: { size: 10 }, callback: v => (v/1000000).toFixed(1) + 'M' }
            }
        }
    }
});

// ── 2. RÉPARTITION BIENS (donut) ───────────────────────────────────
// À terme : injecter les vraies données depuis le contrôleur
new Chart(document.getElementById('chartTypes'), {
    type: 'doughnut',
    data: {
        labels: ['Appartements', 'Villas', 'Bureaux', 'Autres'],
        datasets: [{
            data: [37, 25, 19, 19],
            backgroundColor: ['#c9a84c', '#0d1117', '#16a34a', '#e5e7eb'],
            borderColor: '#ffffff',
            borderWidth: 3,
            hoverOffset: 6,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '72%',
        plugins: {
            legend: { display: false },
            tooltip: { ...tooltipStyle, callbacks: { label: c => ' ' + c.parsed + '%' } }
        }
    }
});

// ── 3. NET PROPRIÉTAIRES (horizontal bars) ─────────────────────────
// Données réelles depuis le rapport — à connecter plus tard
new Chart(document.getElementById('chartProprio'), {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($loyersParMois->pluck('mois'), 15, 512) ?>,
        datasets: [{
            label: 'Loyers (F)',
            data: <?php echo json_encode($loyersParMois->pluck('total'), 15, 512) ?>,
            backgroundColor: function(ctx) {
                const alpha = 0.9 - (ctx.dataIndex * 0.12);
                return `rgba(201,168,76,${Math.max(alpha, 0.2)})`;
            },
            borderRadius: 6,
            borderSkipped: false,
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: { ...tooltipStyle, callbacks: { label: c => ' ' + Number(c.parsed.x).toLocaleString('fr-FR') + ' F' } }
        },
        scales: {
            x: {
                grid: { color: '#f3f4f6', drawTicks: false },
                border: { display: false },
                ticks: { font: { size: 10 }, callback: v => (v/1000) + 'k' }
            },
            y: {
                grid: { display: false },
                border: { display: false },
                ticks: { font: { size: 11 }, color: '#374151' }
            }
        }
    }
});

// ── 4. STATUTS PAIEMENTS (donut mini) ──────────────────────────────
new Chart(document.getElementById('chartStatuts'), {
    type: 'doughnut',
    data: {
        labels: ['Validés', 'Impayés'],
        datasets: [{
            data: [<?php echo e($statsMois['nb_payes']); ?>, <?php echo e($nb_impayes_mois); ?>],
            backgroundColor: ['#16a34a', '#dc2626'],
            borderColor: '#ffffff',
            borderWidth: 3,
            hoverOffset: 4,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '68%',
        plugins: {
            legend: { display: false },
            tooltip: { ...tooltipStyle }
        }
    }
});

// ── Tabs ───────────────────────────────────────────────────────────
document.querySelectorAll('.tab').forEach(tab => {
    tab.addEventListener('click', () => {
        document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
        tab.classList.add('active');
    });
});
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
<?php endif; ?><?php /**PATH C:\Users\ph\bimotech-immo\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>