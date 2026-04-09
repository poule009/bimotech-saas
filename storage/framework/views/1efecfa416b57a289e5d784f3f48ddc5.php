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
     <?php $__env->slot('header', null, []); ?> Propriétaires <?php $__env->endSlot(); ?>

<style>
:root { --gold:#c9a84c; --gold-light:#f5e9c9; --gold-dark:#8a6e2f; --dark:#0d1117; --green:#16a34a; }

.page { padding:24px 32px 48px; }

/* KPI */
.kpi-row { display:grid; grid-template-columns:repeat(3,1fr); gap:14px; margin-bottom:24px; }
.kpi { background:#fff; border:1px solid #e5e7eb; border-radius:14px; padding:18px 20px; position:relative; overflow:hidden; }
.kpi::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; border-radius:14px 14px 0 0; }
.kpi.gold::before { background:var(--gold); }
.kpi.green::before { background:var(--green); }
.kpi.dark::before { background:var(--dark); }
.kpi-lbl { font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.8px; color:#6b7280; margin-bottom:6px; }
.kpi-val { font-family:'Syne',sans-serif; font-size:28px; font-weight:800; color:#0d1117; }
.kpi-sub { font-size:11px; color:#9ca3af; margin-top:4px; }

/* Table */
.card { background:#fff; border:1px solid #e5e7eb; border-radius:16px; overflow:hidden; }
.card-hd { padding:16px 22px; border-bottom:1px solid #e5e7eb; display:flex; align-items:center; justify-content:space-between; }
.card-title { font-family:'Syne',sans-serif; font-size:14px; font-weight:700; color:#0d1117; }
.dt { width:100%; border-collapse:collapse; }
.dt th { padding:10px 18px; font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.8px; color:#9ca3af; background:#f9fafb; border-bottom:1px solid #e5e7eb; text-align:left; white-space:nowrap; }
.dt td { padding:14px 18px; border-bottom:1px solid #f3f4f6; vertical-align:middle; }
.dt tbody tr:last-child td { border-bottom:none; }
.dt tbody tr { transition:background .1s; }
.dt tbody tr:hover { background:#fafafa; }

/* Avatar */
.av { width:38px; height:38px; border-radius:11px; background:linear-gradient(135deg,var(--gold),#a07830); display:flex; align-items:center; justify-content:center; font-family:'Syne',sans-serif; font-size:14px; font-weight:800; color:#fff; flex-shrink:0; }

/* Badges */
.badge { display:inline-flex; align-items:center; gap:4px; padding:3px 9px; border-radius:99px; font-size:10px; font-weight:700; }
.badge-green { background:#dcfce7; color:var(--green); }
.badge-gray  { background:#f3f4f6; color:#6b7280; }
.badge-gold  { background:var(--gold-light); color:var(--gold-dark); }

/* Bouton add */
.btn-add { display:inline-flex; align-items:center; gap:7px; padding:9px 18px; background:var(--dark); color:#fff; border:none; border-radius:10px; font-size:13px; font-weight:600; font-family:'DM Sans',sans-serif; text-decoration:none; cursor:pointer; transition:opacity .15s; }
.btn-add:hover { opacity:.88; }
.btn-add svg { width:14px; height:14px; }

/* Action buttons */
.act { display:inline-flex; align-items:center; justify-content:center; width:30px; height:30px; border:1px solid #e5e7eb; border-radius:8px; text-decoration:none; color:#6b7280; transition:all .15s; }
.act:hover { border-color:var(--gold); color:var(--gold-dark); background:var(--gold-light); }
.act svg { width:13px; height:13px; }

/* Search */
.search-wrap { display:flex; align-items:center; gap:10px; }
.search-input { padding:8px 12px 8px 36px; border:1px solid #e5e7eb; border-radius:9px; font-size:13px; font-family:'DM Sans',sans-serif; color:#0d1117; outline:none; transition:border-color .15s; background:#f9fafb url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%239ca3af' stroke-width='2'%3E%3Ccircle cx='11' cy='11' r='8'/%3E%3Cpath d='M21 21l-4.35-4.35'/%3E%3C/svg%3E") no-repeat 10px center / 15px; }
.search-input:focus { border-color:var(--gold); background-color:#fff; }

/* Mode paiement icône */
.mode-icon { font-size:16px; }

/* Vide */
.empty { padding:64px; text-align:center; }
.empty-icon { font-size:48px; margin-bottom:12px; }
.empty-title { font-family:'Syne',sans-serif; font-size:16px; font-weight:700; color:#0d1117; margin-bottom:6px; }
.empty-sub { font-size:13px; color:#6b7280; margin-bottom:20px; }
</style>

<div class="page">

    
    <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:22px">
        <div>
            <h1 style="font-family:'Syne',sans-serif;font-size:24px;font-weight:800;color:#0d1117;letter-spacing:-.4px">Propriétaires</h1>
            <p style="font-size:13px;color:#6b7280;margin-top:3px"><?php echo e($stats['total']); ?> propriétaire(s) enregistré(s)</p>
        </div>
        <a href="<?php echo e(route('admin.users.create', 'proprietaire')); ?>" class="btn-add">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Nouveau propriétaire
        </a>
    </div>

    
    <div class="kpi-row">
        <div class="kpi gold">
            <div class="kpi-lbl">Total propriétaires</div>
            <div class="kpi-val"><?php echo e($stats['total']); ?></div>
            <div class="kpi-sub">Enregistrés dans l'agence</div>
        </div>
        <div class="kpi green">
            <div class="kpi-lbl">Biens gérés</div>
            <div class="kpi-val"><?php echo e($stats['total_biens']); ?></div>
            <div class="kpi-sub"><?php echo e($stats['biens_loues']); ?> loué(s) · <?php echo e($stats['total_biens'] - $stats['biens_loues']); ?> disponible(s)</div>
        </div>
        <div class="kpi dark">
            <div class="kpi-lbl">Taux d'occupation</div>
            <div class="kpi-val" style="color:<?php echo e($stats['total_biens'] > 0 && ($stats['biens_loues']/$stats['total_biens']*100) >= 80 ? 'var(--green)' : 'var(--gold)'); ?>">
                <?php echo e($stats['total_biens'] > 0 ? round($stats['biens_loues']/$stats['total_biens']*100) : 0); ?>%
            </div>
            <div class="kpi-sub">Biens avec locataire actif</div>
        </div>
    </div>

    
    <div class="card">
        <div class="card-hd">
            <div class="card-title">Liste des propriétaires</div>
            <div class="search-wrap">
                <input type="text" class="search-input" placeholder="Rechercher..." id="search-input"
                       oninput="filterTable(this.value)" style="width:220px">
            </div>
        </div>

        <div style="overflow-x:auto">
            <table class="dt" id="dt-proprio">
                <thead>
                    <tr>
                        <th>Propriétaire</th>
                        <th>Contact</th>
                        <th>Ville</th>
                        <th style="text-align:center">Biens</th>
                        <th>Paiement préféré</th>
                        <th>NINEA</th>
                        <th style="text-align:center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $proprietaires; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $profil = $user->proprietaire;
                        $modeIcons = [
                            'virement'     => '🏦',
                            'wave'         => '📱',
                            'orange_money' => '🟠',
                            'especes'      => '💵',
                            'cheque'       => '📝',
                            'mobile_money' => '📲',
                        ];
                        $modeLabels = [
                            'virement'     => 'Virement',
                            'wave'         => 'Wave',
                            'orange_money' => 'Orange Money',
                            'especes'      => 'Espèces',
                            'cheque'       => 'Chèque',
                            'mobile_money' => 'Mobile Money',
                        ];
                        $mode = $profil?->mode_paiement_prefere ?? 'virement';
                    ?>
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:12px">
                                <div class="av"><?php echo e(strtoupper(substr($user->name,0,2))); ?></div>
                                <div>
                                    <div style="font-size:13px;font-weight:700;color:#0d1117"><?php echo e($user->name); ?></div>
                                    <div style="font-size:11px;color:#9ca3af;margin-top:1px">
                                        Depuis <?php echo e($user->created_at->format('M Y')); ?>

                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div style="font-size:13px;color:#374151"><?php echo e($user->email); ?></div>
                            <?php if($user->telephone): ?>
                            <div style="font-size:11px;color:#6b7280;margin-top:2px"><?php echo e($user->telephone); ?></div>
                            <?php endif; ?>
                        </td>
                        <td style="font-size:13px;color:#374151"><?php echo e($profil?->ville ?? '—'); ?></td>
                        <td style="text-align:center">
                            <?php if($user->biens_count > 0): ?>
                                <span class="badge badge-gold"><?php echo e($user->biens_count); ?> bien(s)</span>
                            <?php else: ?>
                                <span class="badge badge-gray">Aucun</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($mode): ?>
                            <div style="display:flex;align-items:center;gap:6px;font-size:12px;color:#374151">
                                <span><?php echo e($modeIcons[$mode] ?? '💳'); ?></span>
                                <?php echo e($modeLabels[$mode] ?? ucfirst($mode)); ?>

                            </div>
                            <?php else: ?>
                            <span style="color:#9ca3af;font-size:12px">—</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($profil?->ninea): ?>
                                <span style="font-family:monospace;font-size:11px;background:#f3f4f6;padding:3px 7px;border-radius:5px;color:#374151"><?php echo e($profil->ninea); ?></span>
                            <?php else: ?>
                                <span style="color:#9ca3af;font-size:12px">—</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div style="display:flex;align-items:center;justify-content:center;gap:5px">
                                <a href="<?php echo e(route('admin.users.show', $user)); ?>" class="act" title="Voir la fiche">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                </a>
                                <a href="<?php echo e(route('admin.users.edit', $user)); ?>" class="act" title="Modifier">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                </a>
                                <a href="<?php echo e(route('admin.bilans-fiscaux.show', [$user, 'annee' => now()->year])); ?>"
                                   class="act" title="Bilan fiscal"
                                   style="border-color:#e5e7eb">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="7">
                            <div class="empty">
                                <div class="empty-icon">🏢</div>
                                <div class="empty-title">Aucun propriétaire enregistré</div>
                                <div class="empty-sub">Commencez par ajouter le premier propriétaire de votre agence.</div>
                                <a href="<?php echo e(route('admin.users.create', 'proprietaire')); ?>" class="btn-add" style="display:inline-flex">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:14px;height:14px"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                    Ajouter un propriétaire
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if($proprietaires->hasPages()): ?>
        <div style="padding:14px 22px;border-top:1px solid #f3f4f6">
            <?php echo e($proprietaires->links()); ?>

        </div>
        <?php endif; ?>
    </div>

</div>

<script>
function filterTable(q) {
    q = q.toLowerCase();
    document.querySelectorAll('#dt-proprio tbody tr').forEach(tr => {
        tr.style.display = tr.textContent.toLowerCase().includes(q) ? '' : 'none';
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
<?php endif; ?><?php /**PATH C:\Users\ph\bimotech-immo\resources\views/users/proprietaires.blade.php ENDPATH**/ ?>