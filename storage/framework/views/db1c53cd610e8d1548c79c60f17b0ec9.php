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
     <?php $__env->slot('header', null, []); ?> Impayés <?php $__env->endSlot(); ?>

<style>
/* ── KPI ── */
.kpi-row { display:grid; grid-template-columns:repeat(4,1fr); gap:14px; margin-bottom:22px; }
.kpi-mini { background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:18px 20px; position:relative; overflow:hidden; }
.kpi-mini::before { content:''; position:absolute; top:0;left:0;right:0; height:3px; border-radius:12px 12px 0 0; }
.kpi-mini.red::before    { background:#dc2626; }
.kpi-mini.green::before  { background:#16a34a; }
.kpi-mini.orange::before { background:#d97706; }
.kpi-mini.blue::before   { background:#1d4ed8; }
.kpi-lbl { font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:.8px;color:#6b7280;margin-bottom:5px; }
.kpi-val { font-family:'Syne',sans-serif;font-size:22px;font-weight:700;color:#0d1117;letter-spacing:-.3px;line-height:1; }
.kpi-val.red   { color:#dc2626; }
.kpi-val.green { color:#16a34a; }
.kpi-u  { font-size:11px;font-weight:400;color:#9ca3af;margin-left:2px; }
.kpi-s  { font-size:11px;color:#9ca3af;margin-top:5px; }

/* ── BARRE RECOUVREMENT ── */
.recouvrement-bar {
    background:#0d1117; border-radius:14px; padding:22px 28px;
    margin-bottom:22px; display:flex; align-items:center; gap:32px;
}
.rec-label { font-size:10px;font-weight:600;letter-spacing:1.5px;text-transform:uppercase;color:rgba(255,255,255,.4);margin-bottom:6px; }
.rec-val   { font-family:'Syne',sans-serif;font-size:24px;font-weight:700;color:#fff;letter-spacing:-.5px; }
.rec-val.green { color:#4ade80; }
.rec-val.red   { color:#f87171; }
.rec-progress-wrap { flex:1; }
.rec-progress-bg { background:rgba(255,255,255,.08); border-radius:99px; height:10px; overflow:hidden; }
.rec-progress-fill { height:100%;border-radius:99px;transition:width .8s ease; }
.rec-progress-label { display:flex;justify-content:space-between;margin-top:8px;font-size:11px;color:rgba(255,255,255,.35); }

/* ── FILTRE MOIS ── */
.filter-bar { background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:14px 18px;margin-bottom:18px;display:flex;align-items:center;gap:10px;flex-wrap:wrap; }
.filter-select { padding:8px 13px;border:1px solid #e5e7eb;border-radius:8px;font-size:13px;color:#0d1117;font-family:'DM Sans',sans-serif;background:#f9fafb;outline:none;cursor:pointer; }
.filter-btn { padding:8px 16px;background:#0d1117;color:#fff;border:none;border-radius:8px;font-size:12px;font-weight:500;font-family:'DM Sans',sans-serif;cursor:pointer; }

/* ── CARDS TABLE ── */
.table-card { background:#fff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden;margin-bottom:20px; }
.table-card.danger { border-color:#fecaca; }
.table-hd { padding:16px 20px;border-bottom:1px solid #e5e7eb;display:flex;align-items:center;justify-content:space-between; }
.table-hd.danger { background:#fef2f2;border-bottom-color:#fecaca; }
.table-hd.success { background:#f0fdf4;border-bottom-color:#bbf7d0; }
.table-title { font-family:'Syne',sans-serif;font-size:14px;font-weight:700; }
.table-title.red   { color:#dc2626; }
.table-title.green { color:#16a34a; }
.table-count { font-size:12px;font-weight:600; }
.table-count.red   { color:#dc2626; }
.table-count.green { color:#16a34a; }

/* ── TABLE ── */
.dt { width:100%;border-collapse:collapse; }
.dt th { padding:10px 18px;text-align:left;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#9ca3af;background:#f9fafb;border-bottom:1px solid #e5e7eb;white-space:nowrap; }
.dt td { padding:14px 18px;font-size:13px;color:#374151;border-bottom:1px solid #f3f4f6;vertical-align:middle; }
.dt tbody tr:last-child td { border-bottom:none; }
.dt tbody tr:hover { background:#fafafa; }
.th-r { text-align:right !important; }
.td-r  { text-align:right; }
.td-c  { text-align:center; }

/* cellules */
.bien-ref  { font-size:13px;font-weight:600;color:#0d1117; }
.bien-sub  { font-size:11px;color:#6b7280;margin-top:1px; }
.loc-name  { font-size:13px;font-weight:500;color:#0d1117; }
.loc-phone { font-size:11px;color:#6b7280;margin-top:1px; }
.montant   { font-family:'Syne',sans-serif;font-weight:600;color:#dc2626; }
.montant.green { color:#16a34a; }

/* retard badge */
.retard-badge { display:inline-flex;align-items:center;gap:4px;padding:4px 10px;border-radius:99px;font-size:11px;font-weight:700; }
.retard-badge.critique { background:#fef2f2;color:#dc2626; }
.retard-badge.warning  { background:#fff7ed;color:#d97706; }
.retard-badge.light    { background:#f3f4f6;color:#6b7280; }

/* badge statut paiement */
.badge { display:inline-flex;align-items:center;gap:4px;padding:3px 9px;border-radius:99px;font-size:11px;font-weight:600; }
.badge.g { background:#dcfce7;color:#16a34a; }
.badge.o { background:#fef3c7;color:#d97706; }
.bdot { width:5px;height:5px;border-radius:50%;background:currentColor; }

/* actions */
.act-group { display:flex;align-items:center;gap:6px; }
.act-btn { display:inline-flex;align-items:center;gap:5px;padding:6px 12px;border-radius:7px;border:1px solid;font-size:11px;font-weight:600;font-family:'DM Sans',sans-serif;cursor:pointer;text-decoration:none;transition:all .15s;white-space:nowrap; }
.act-btn svg { width:12px;height:12px; }
.act-btn.payer   { background:#f0fdf4;border-color:#bbf7d0;color:#16a34a; }
.act-btn.payer:hover { background:#dcfce7; }
.act-btn.relance { background:#fff;border-color:#e5e7eb;color:#6b7280; }
.act-btn.relance:hover { border-color:#c9a84c;color:#8a6e2f;background:#f5e9c9; }
.act-btn.whatsapp { background:#dcfce7;border-color:#bbf7d0;color:#15803d; }
.act-btn.whatsapp:hover { background:#bbf7d0;border-color:#86efac; }

/* urgence row highlight */
.dt tbody tr.urgence-haute td { background:#fef9f9; }
.dt tbody tr.urgence-haute:hover td { background:#fef2f2; }

/* état vide --*/
.empty-state { padding:48px 20px;text-align:center; }
.empty-icon  { width:52px;height:52px;border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 14px; }
.empty-icon.green { background:#dcfce7; }
.empty-icon.green svg { width:24px;height:24px;color:#16a34a; }
.empty-title { font-family:'Syne',sans-serif;font-size:16px;font-weight:700;color:#0d1117;margin-bottom:6px; }
.empty-sub   { font-size:13px;color:#6b7280; }

/* nav mois */
.month-nav { display:flex;align-items:center;gap:8px; }
.month-btn { display:flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:7px;border:1px solid #e5e7eb;background:#fff;cursor:pointer;color:#6b7280;text-decoration:none;transition:all .15s; }
.month-btn:hover { border-color:#c9a84c;color:#8a6e2f; }
.month-btn svg { width:14px;height:14px; }
.month-current { font-family:'Syne',sans-serif;font-size:14px;font-weight:600;color:#0d1117;min-width:140px;text-align:center; }
</style>

<div style="padding:24px 32px 48px">

    
    <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:22px">
        <div>
            <h1 style="font-family:'Syne',sans-serif;font-size:22px;font-weight:700;color:#0d1117;letter-spacing:-.4px">Suivi des impayés</h1>
            <p style="font-size:13px;color:#6b7280;margin-top:3px">
                <?php echo e($periode->translatedFormat('F Y')); ?> · <?php echo e($stats['nb_impayes']); ?> impayé(s) sur <?php echo e($stats['nb_impayes'] + $stats['nb_payes']); ?> contrats actifs
            </p>
        </div>

        
        <div class="month-nav">
            <?php
                $prevMois  = $mois == 1  ? 12 : $mois - 1;
                $prevAnnee = $mois == 1  ? $annee - 1 : $annee;
                $nextMois  = $mois == 12 ? 1  : $mois + 1;
                $nextAnnee = $mois == 12 ? $annee + 1 : $annee;
                $isCurrentMonth = $mois == now()->month && $annee == now()->year;
            ?>
            <a href="<?php echo e(route('admin.impayes.index', ['mois' => $prevMois, 'annee' => $prevAnnee])); ?>" class="month-btn">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
            </a>
            <div class="month-current"><?php echo e($periode->translatedFormat('F Y')); ?></div>
            <?php if(!$isCurrentMonth): ?>
                <a href="<?php echo e(route('admin.impayes.index', ['mois' => $nextMois, 'annee' => $nextAnnee])); ?>" class="month-btn">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                </a>
            <?php else: ?>
                <span class="month-btn" style="opacity:.3;cursor:default">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                </span>
            <?php endif; ?>
            <?php if(!$isCurrentMonth): ?>
                <a href="<?php echo e(route('admin.impayes.index')); ?>" style="padding:6px 12px;font-size:12px;color:#6b7280;border:1px solid #e5e7eb;border-radius:7px;text-decoration:none;background:#fff">Mois actuel</a>
            <?php endif; ?>
        </div>
    </div>

    
    <div class="kpi-row">
        <div class="kpi-mini red">
            <div class="kpi-lbl">Impayés</div>
            <div class="kpi-val red"><?php echo e($stats['nb_impayes']); ?><span class="kpi-u">contrats</span></div>
            <div class="kpi-s"><?php echo e($stats['nb_impayes'] > 0 ? 'Action requise' : 'Aucun impayé'); ?></div>
        </div>
        <div class="kpi-mini orange">
            <div class="kpi-lbl">Montant dû</div>
            <div class="kpi-val" style="color:#d97706;font-size:18px"><?php echo e(number_format($stats['montant_du'], 0, ',', ' ')); ?><span class="kpi-u">F</span></div>
            <div class="kpi-s">À recouvrer ce mois</div>
        </div>
        <div class="kpi-mini green">
            <div class="kpi-lbl">Payés</div>
            <div class="kpi-val green"><?php echo e($stats['nb_payes']); ?><span class="kpi-u">contrats</span></div>
            <div class="kpi-s">Loyers encaissés</div>
        </div>
        <div class="kpi-mini blue">
            <div class="kpi-lbl">Taux de recouvrement</div>
            <div class="kpi-val" style="color:#1d4ed8"><?php echo e($stats['taux_recouvrement']); ?><span class="kpi-u">%</span></div>
            <div class="kpi-s"><?php echo e($stats['nb_payes']); ?> / <?php echo e($stats['nb_impayes'] + $stats['nb_payes']); ?> contrats</div>
        </div>
    </div>

    
    <div class="recouvrement-bar">
        <div>
            <div class="rec-label">Impayés</div>
            <div class="rec-val red"><?php echo e(number_format($stats['montant_du'], 0, ',', ' ')); ?> F</div>
        </div>
        <div class="rec-progress-wrap">
            <?php
                $taux = (float) $stats['taux_recouvrement'];
                $progressColor = $taux >= 90 ? '#4ade80' : ($taux >= 70 ? '#fbbf24' : '#f87171');
            ?>
            <div class="rec-progress-bg">
                <div class="rec-progress-fill" style="width:<?php echo e($taux); ?>%;background:<?php echo e($progressColor); ?>"></div>
            </div>
            <div class="rec-progress-label">
                <span>0%</span>
                <span style="color:<?php echo e($progressColor); ?>;font-weight:600"><?php echo e($taux); ?>% recouvré</span>
                <span>100%</span>
            </div>
        </div>
        <div>
            <div class="rec-label">Recouvré</div>
            <?php
                $totalAttendus = $impayes->sum('montant_du') + $payes->sum(fn($p) => $p['contrat']->loyer_contractuel);
                $totalRecouvre = $payes->sum(fn($p) => $p['contrat']->loyer_contractuel);
            ?>
            <div class="rec-val green"><?php echo e(number_format($totalRecouvre, 0, ',', ' ')); ?> F</div>
        </div>
    </div>

    
    <form method="GET" action="<?php echo e(route('admin.impayes.index')); ?>">
        <div class="filter-bar">
            <span style="font-size:12px;color:#6b7280;font-weight:500">Changer de période :</span>
            <select name="mois" class="filter-select">
                <?php $__currentLoopData = range(1,12); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($m); ?>" <?php echo e($mois == $m ? 'selected' : ''); ?>>
                        <?php echo e(\Carbon\Carbon::create(null, $m)->translatedFormat('F')); ?>

                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <select name="annee" class="filter-select">
                <?php $__currentLoopData = range(now()->year - 2, now()->year); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($a); ?>" <?php echo e($annee == $a ? 'selected' : ''); ?>><?php echo e($a); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <button type="submit" class="filter-btn">Afficher</button>
            <div style="flex:1"></div>
            <?php if($stats['nb_impayes'] > 0): ?>
                <span style="font-size:12px;color:#dc2626;font-weight:600;display:flex;align-items:center;gap:5px">
                    <svg style="width:14px;height:14px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    <?php echo e($stats['nb_impayes']); ?> relance(s) en attente
                </span>
            <?php endif; ?>
        </div>
    </form>

    
    <div class="table-card danger">
        <div class="table-hd danger">
            <div>
                <div class="table-title red">Loyers impayés</div>
                <div style="font-size:11px;color:#dc2626;margin-top:2px"><?php echo e($periode->translatedFormat('F Y')); ?></div>
            </div>
            <div class="table-count red"><?php echo e($stats['nb_impayes']); ?> contrat(s) · <?php echo e(number_format($stats['montant_du'], 0, ',', ' ')); ?> F dûs</div>
        </div>

        <?php if($impayes->isEmpty()): ?>
            <div class="empty-state">
                <div class="empty-icon green">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                </div>
                <div class="empty-title">Aucun impayé ce mois</div>
                <div class="empty-sub">Tous les loyers de <?php echo e($periode->translatedFormat('F Y')); ?> ont été encaissés.</div>
            </div>
        <?php else: ?>
            <div style="overflow-x:auto">
                <table class="dt">
                    <thead>
                        <tr>
                            <th>Bien</th>
                            <th>Locataire</th>
                            <th>Téléphone</th>
                            <th style="text-align:center">Retard</th>
                            <th class="th-r">Montant dû</th>
                            <th style="text-align:center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $impayes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $jours = $item['jours_retard'];
                            if ($jours >= 30) {
                                $retardClass = 'critique';
                                $rowClass    = 'urgence-haute';
                            } elseif ($jours >= 10) {
                                $retardClass = 'warning';
                                $rowClass    = '';
                            } else {
                                $retardClass = 'light';
                                $rowClass    = '';
                            }
                        ?>
                        <tr class="<?php echo e($rowClass); ?>">
                            
                            <td>
                                <div class="bien-ref"><?php echo e($item['contrat']->bien->reference); ?></div>
                                <div class="bien-sub"><?php echo e($item['contrat']->bien->adresse); ?>, <?php echo e($item['contrat']->bien->ville); ?></div>
                            </td>

                            
                            <td>
                                <div class="loc-name"><?php echo e($item['contrat']->locataire->name); ?></div>
                                <div class="loc-phone"><?php echo e($item['contrat']->locataire->email); ?></div>
                            </td>

                            
                            <td>
                                <?php if($item['contrat']->locataire->telephone): ?>
                                    <a href="tel:<?php echo e($item['contrat']->locataire->telephone); ?>"
                                       style="font-size:13px;color:#0d1117;text-decoration:none;display:flex;align-items:center;gap:5px">
                                        <svg style="width:12px;height:12px;color:#9ca3af" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 9.81a19.79 19.79 0 01-3.07-8.68A2 2 0 012 .5h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.09 8.32a16 16 0 006.29 6.29l1.18-1.18a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 15.92z"/></svg>
                                        <?php echo e($item['contrat']->locataire->telephone); ?>

                                    </a>
                                <?php else: ?>
                                    <span style="font-size:12px;color:#9ca3af">—</span>
                                <?php endif; ?>
                            </td>

                            
                            <td class="td-c">
                                <?php if($jours > 0): ?>
                                    <span class="retard-badge <?php echo e($retardClass); ?>">
                                        <?php if($retardClass === 'critique'): ?>
                                            <svg style="width:10px;height:10px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                        <?php endif; ?>
                                        <?php echo e($jours); ?>j de retard
                                    </span>
                                <?php else: ?>
                                    <span class="retard-badge light">Ce mois</span>
                                <?php endif; ?>
                            </td>

                            
                            <td class="td-r">
                                <div class="montant"><?php echo e(number_format($item['montant_du'], 0, ',', ' ')); ?> F</div>
                                <div style="font-size:10px;color:#9ca3af;margin-top:1px">loyer contractuel</div>
                            </td>

                            
                            <td class="td-c">
                                <div class="act-group" style="justify-content:center">
                                    
                                    <a href="<?php echo e(route('admin.paiements.create', ['contrat_id' => $item['contrat']->id])); ?>"
                                       class="act-btn payer">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                        Paiement
                                    </a>

                                    
                                    <form method="POST" action="<?php echo e(route('admin.impayes.relance', $item['contrat'])); ?>">
                                        <?php echo csrf_field(); ?>
                                        <input type="hidden" name="mois" value="<?php echo e($mois); ?>">
                                        <input type="hidden" name="annee" value="<?php echo e($annee); ?>">
                                        <button type="submit" class="act-btn relance"
                                                onclick="return confirm('Envoyer une relance email à <?php echo e($item['contrat']->locataire->name); ?> ?')">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                                            Relance
                                        </button>
                                    </form>

                                    
                                    <?php if($item['contrat']->locataire->telephone): ?>
                                    <?php
                                        // Formater le numéro en format international Sénégal
                                        $tel = preg_replace('/\s+|-|\(|\)/', '', $item['contrat']->locataire->telephone);
                                        if (str_starts_with($tel, '0')) $tel = substr($tel, 1);
                                        if (!str_starts_with($tel, '+') && !str_starts_with($tel, '221')) {
                                            $tel = '221' . $tel;
                                        }
                                        $tel = ltrim($tel, '+');

                                        // Message pré-rempli
                                        $agenceNom  = $currentAgency->name ?? 'BimoTech Immo';
                                        $locNom     = $item['contrat']->locataire->name;
                                        $bienRef    = $item['contrat']->bien->reference;
                                        $montant    = number_format($item['montant_du'], 0, ',', ' ');
                                        $periodeStr = $periode->translatedFormat('F Y');
                                        $jours      = $item['jours_retard'];

                                        $msg = "Bonjour {$locNom},\n\n";
                                        $msg .= "Nous vous contactons de la part de *{$agenceNom}* concernant votre loyer du bien *{$bienRef}*.\n\n";
                                        $msg .= "Votre loyer de *{$montant} FCFA* pour le mois de *{$periodeStr}* ";
                                        if ($jours > 0) {
                                            $msg .= "est en retard de *{$jours} jour(s)*.\n\n";
                                        } else {
                                            $msg .= "est à régler.\n\n";
                                        }
                                        $msg .= "Merci de procéder au règlement dans les meilleurs délais.\n\n";
                                        $msg .= "Cordialement,\n{$agenceNom}";

                                        $waUrl = 'https://wa.me/' . $tel . '?text=' . urlencode($msg);
                                    ?>
                                    <a href="<?php echo e($waUrl); ?>" target="_blank" class="act-btn whatsapp" title="Envoyer un message WhatsApp">
                                        <svg style="width:13px;height:13px" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                                            <path d="M12 0C5.373 0 0 5.373 0 12c0 2.124.558 4.118 1.532 5.847L.057 23.492a.5.5 0 00.614.65l5.82-1.527A11.945 11.945 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22a9.944 9.944 0 01-5.091-1.396l-.361-.216-3.754.984.999-3.648-.237-.374A9.944 9.944 0 012 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/>
                                        </svg>
                                        WhatsApp
                                    </a>
                                    <?php endif; ?>

                                    
                                    <a href="<?php echo e(route('admin.contrats.show', $item['contrat'])); ?>"
                                       style="display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border:1px solid #e5e7eb;border-radius:7px;background:#fff;color:#6b7280;text-decoration:none;transition:all .15s"
                                       title="Voir le contrat">
                                        <svg style="width:13px;height:13px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    
    <div class="table-card">
        <div class="table-hd success">
            <div>
                <div class="table-title green">Loyers encaissés</div>
                <div style="font-size:11px;color:#16a34a;margin-top:2px"><?php echo e($periode->translatedFormat('F Y')); ?></div>
            </div>
            <div class="table-count green"><?php echo e($stats['nb_payes']); ?> paiement(s) validé(s)</div>
        </div>

        <?php if($payes->isEmpty()): ?>
            <div class="empty-state">
                <div class="empty-icon" style="background:#f3f4f6">
                    <svg style="width:24px;height:24px;color:#9ca3af" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
                </div>
                <div class="empty-title">Aucun paiement enregistré</div>
                <div class="empty-sub">Aucun loyer n'a encore été encaissé pour <?php echo e($periode->translatedFormat('F Y')); ?>.</div>
            </div>
        <?php else: ?>
            <div style="overflow-x:auto">
                <table class="dt">
                    <thead>
                        <tr>
                            <th>Bien</th>
                            <th>Locataire</th>
                            <th>Mode</th>
                            <th>Date paiement</th>
                            <th class="th-r">Montant encaissé</th>
                            <th class="th-r">Net proprio</th>
                            <th style="text-align:center">Statut</th>
                            <th style="text-align:center">Quittance</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $payes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td>
                                <div class="bien-ref"><?php echo e($item['contrat']->bien->reference); ?></div>
                                <div class="bien-sub"><?php echo e($item['contrat']->bien->ville); ?></div>
                            </td>
                            <td>
                                <div class="loc-name"><?php echo e($item['contrat']->locataire->name); ?></div>
                                <div class="loc-phone"><?php echo e($item['contrat']->locataire->telephone ?? $item['contrat']->locataire->email); ?></div>
                            </td>
                            <td>
                                <span style="font-size:12px;color:#6b7280">
                                    <?php echo e(\App\Models\Paiement::MODES_PAIEMENT[$item['paiement']->mode_paiement] ?? $item['paiement']->mode_paiement); ?>

                                </span>
                            </td>
                            <td>
                                <span style="font-size:13px;color:#374151">
                                    <?php echo e(\Carbon\Carbon::parse($item['paiement']->date_paiement)->format('d/m/Y')); ?>

                                </span>
                            </td>
                            <td class="td-r">
                                <div class="montant green"><?php echo e(number_format($item['paiement']->montant_encaisse, 0, ',', ' ')); ?> F</div>
                            </td>
                            <td class="td-r">
                                <div style="font-family:'Syne',sans-serif;font-weight:600;color:#16a34a">
                                    <?php echo e(number_format($item['paiement']->net_proprietaire, 0, ',', ' ')); ?> F
                                </div>
                            </td>
                            <td class="td-c">
                                <?php if($item['paiement']->statut === 'valide'): ?>
                                    <span class="badge g"><span class="bdot"></span>Validé</span>
                                <?php else: ?>
                                    <span class="badge o"><span class="bdot"></span><?php echo e(ucfirst($item['paiement']->statut)); ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="td-c">
                                <?php if($item['paiement']->statut === 'valide'): ?>
                                    <a href="<?php echo e(route('admin.paiements.pdf', $item['paiement'])); ?>"
                                       target="_blank"
                                       style="display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border:1px solid #e5e7eb;border-radius:7px;background:#fff;color:#6b7280;text-decoration:none;transition:all .15s"
                                       title="Télécharger la quittance"
                                       onmouseover="this.style.borderColor='#c9a84c';this.style.color='#8a6e2f';this.style.background='#f5e9c9'"
                                       onmouseout="this.style.borderColor='#e5e7eb';this.style.color='#6b7280';this.style.background='#fff'">
                                        <svg style="width:13px;height:13px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                    </a>
                                <?php else: ?>
                                    <span style="color:#d1d5db;font-size:12px">—</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
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
<?php endif; ?><?php /**PATH C:\Users\ph\bimotech-immo\resources\views/impayes/index.blade.php ENDPATH**/ ?>