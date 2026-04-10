
<?php $__env->startSection('title', $user->name); ?>
<?php $__env->startSection('breadcrumb', ($user->isProprietaire() ? 'Propriétaires' : 'Locataires') . ' › ' . $user->name); ?>

<?php $__env->startSection('content'); ?>
<style>
.page-grid { display:grid;grid-template-columns:1fr 280px;gap:24px;align-items:start; }
.card { background:#fff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden;margin-bottom:16px; }
.card-hd { padding:14px 20px;border-bottom:1px solid #e5e7eb;display:flex;align-items:center;justify-content:space-between; }
.card-hd-left { display:flex;align-items:center;gap:10px; }
.card-icon { width:30px;height:30px;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0; }
.card-icon svg { width:15px;height:15px; }
.card-icon.gold  { background:#f5e9c9;color:#8a6e2f; }
.card-icon.blue  { background:#dbeafe;color:#1d4ed8; }
.card-icon.green { background:#dcfce7;color:#16a34a; }
.card-icon.purple{ background:#ede9fe;color:#7c3aed; }
.card-title { font-family:'Syne',sans-serif;font-size:13px;font-weight:700;color:#0d1117; }
.card-body { padding:18px 20px; }
.il { font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:.8px;color:#9ca3af;margin-bottom:4px; }
.iv { font-size:13px;font-weight:500;color:#0d1117; }
.iv-sub { font-size:11px;color:#6b7280;margin-top:2px; }
.info-grid   { display:grid;grid-template-columns:1fr 1fr;gap:14px; }
.info-grid-3 { display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px; }

.actions-bar { display:flex;gap:8px;flex-wrap:wrap;margin-bottom:20px; }
.btn-act { display:flex;align-items:center;gap:6px;padding:9px 16px;border-radius:9px;font-size:12px;font-weight:500;font-family:'DM Sans',sans-serif;cursor:pointer;text-decoration:none;transition:all .15s;border:none; }
.btn-dark    { background:#0d1117;color:#fff; }
.btn-dark:hover { opacity:.85; }
.btn-outline { background:#fff;color:#374151;border:1px solid #e5e7eb; }
.btn-outline:hover { border-color:#c9a84c;color:#8a6e2f; }
.btn-red     { background:#fee2e2;color:#dc2626;border:1px solid #fecaca; }
.btn-red:hover { background:#fecaca; }
.btn-act svg { width:14px;height:14px; }

/* Hero */
.hero { background:linear-gradient(135deg,#0d1117,#1c2333);border-radius:14px;padding:22px 24px;margin-bottom:20px;display:flex;align-items:center;gap:18px;flex-wrap:wrap; }
.hero-avatar { width:56px;height:56px;border-radius:50%;background:rgba(201,168,76,.15);border:2px solid rgba(201,168,76,.3);display:flex;align-items:center;justify-content:center;font-family:'Syne',sans-serif;font-size:22px;font-weight:700;color:#c9a84c;flex-shrink:0; }

/* KPIs */
.kpi-row { display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:20px; }
.kpi { background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:14px 16px; }
.kpi.gold  { border-top:3px solid #c9a84c; }
.kpi.green { border-top:3px solid #16a34a; }
.kpi.blue  { border-top:3px solid #1d4ed8; }
.kpi-lbl { font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#9ca3af;margin-bottom:5px; }
.kpi-val { font-family:'Syne',sans-serif;font-size:20px;font-weight:700;color:#0d1117; }
.kpi-sub { font-size:11px;color:#9ca3af;margin-top:3px; }

/* Table */
.dt { width:100%;border-collapse:collapse; }
.dt th { padding:9px 16px;text-align:left;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.7px;color:#9ca3af;background:#f9fafb;border-bottom:1px solid #e5e7eb; }
.dt td { padding:11px 16px;font-size:13px;color:#374151;border-bottom:1px solid #f3f4f6;vertical-align:middle; }
.dt tbody tr:last-child td { border-bottom:none; }
.dt tbody tr:hover { background:#fafafa; }

/* Sidebar */
.side-card { background:#0d1117;border-radius:14px;overflow:hidden;margin-bottom:14px;position:sticky;top:24px; }
.side-hd { padding:12px 16px;border-bottom:1px solid rgba(255,255,255,.07); }
.side-title { font-family:'Syne',sans-serif;font-size:12px;font-weight:700;color:#fff; }
.side-body { padding:14px 16px; }
.side-row { display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px solid rgba(255,255,255,.06);font-size:12px; }
.side-row:last-child { border-bottom:none; }
.side-lbl { color:rgba(255,255,255,.4); }
.side-val { color:#e6edf3;font-weight:500; }
.side-val.gold { color:#c9a84c; }
</style>

<div style="padding:0 0 48px">

    
    <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:#6b7280;margin-bottom:16px">
        <?php if($user->isProprietaire()): ?>
            <a href="<?php echo e(route('admin.users.proprietaires')); ?>" style="color:#6b7280;text-decoration:none">Propriétaires</a>
        <?php else: ?>
            <a href="<?php echo e(route('admin.users.locataires')); ?>" style="color:#6b7280;text-decoration:none">Locataires</a>
        <?php endif; ?>
        <svg style="width:12px;height:12px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
        <span style="color:#0d1117;font-weight:500"><?php echo e($user->name); ?></span>
    </div>

    
    <div class="actions-bar">
        <a href="<?php echo e(route('admin.users.edit', $user)); ?>" class="btn-act btn-dark">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            Modifier
        </a>
        <?php if($user->isProprietaire()): ?>
            <a href="<?php echo e(route('admin.contrats.create')); ?>" class="btn-act btn-outline">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><line x1="12" y1="11" x2="12" y2="17"/><line x1="9" y1="14" x2="15" y2="14"/></svg>
                Nouveau contrat
            </a>
        <?php endif; ?>
        <a href="<?php echo e($user->isProprietaire() ? route('admin.users.proprietaires') : route('admin.users.locataires')); ?>" class="btn-act btn-outline">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
            Retour
        </a>
        <form method="POST" action="<?php echo e(route('admin.users.destroy', $user)); ?>"
              onsubmit="return confirm('Supprimer cet utilisateur ? Cette action est irréversible.')">
            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
            <button type="submit" class="btn-act btn-red">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/></svg>
                Supprimer
            </button>
        </form>
    </div>

    
    <div class="hero">
        <div class="hero-avatar"><?php echo e(strtoupper(substr($user->name, 0, 1))); ?></div>
        <div style="flex:1">
            <div style="font-family:'Syne',sans-serif;font-size:20px;font-weight:700;color:#fff;letter-spacing:-.3px;margin-bottom:4px">
                <?php echo e($user->name); ?>

            </div>
            <div style="font-size:13px;color:rgba(255,255,255,.5);margin-bottom:6px">
                <?php echo e($user->email); ?>

                <?php if($user->telephone): ?> · <?php echo e($user->telephone); ?> <?php endif; ?>
            </div>
            <span style="display:inline-flex;align-items:center;padding:3px 12px;border-radius:99px;font-size:11px;font-weight:600;background:<?php echo e($user->isProprietaire() ? 'rgba(201,168,76,.2)' : 'rgba(29,78,216,.2)'); ?>;color:<?php echo e($user->isProprietaire() ? '#c9a84c' : '#60a5fa'); ?>">
                <?php echo e($user->isProprietaire() ? 'Propriétaire' : 'Locataire'); ?>

            </span>
        </div>
        <div style="text-align:right">
            <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:rgba(255,255,255,.3);margin-bottom:4px">
                Membre depuis
            </div>
            <div style="font-family:'Syne',sans-serif;font-size:16px;font-weight:700;color:#fff">
                <?php echo e($user->created_at?->format('d/m/Y')); ?>

            </div>
        </div>
    </div>

    
    <?php if($user->isProprietaire()): ?>

        
        <div class="kpi-row">
            <div class="kpi gold">
                <div class="kpi-lbl">Total loyers</div>
                <div class="kpi-val" style="font-size:16px;color:#c9a84c">
                    <?php echo e(number_format($stats['total_loyers'] ?? 0, 0, ',', ' ')); ?>

                </div>
                <div class="kpi-sub">FCFA encaissés</div>
            </div>
            <div class="kpi green">
                <div class="kpi-lbl">Net reversé</div>
                <div class="kpi-val" style="font-size:16px;color:#16a34a">
                    <?php echo e(number_format($stats['total_net'] ?? 0, 0, ',', ' ')); ?>

                </div>
                <div class="kpi-sub">FCFA après commission</div>
            </div>
            <div class="kpi blue">
                <div class="kpi-lbl">Biens</div>
                <div class="kpi-val" style="color:#1d4ed8"><?php echo e($stats['nb_biens'] ?? 0); ?></div>
                <div class="kpi-sub"><?php echo e($stats['nb_biens_loues'] ?? 0); ?> loué(s)</div>
            </div>
        </div>

        <div class="page-grid">
            <div>

                
                <div class="card">
                    <div class="card-hd">
                        <div class="card-hd-left">
                            <div class="card-icon gold">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
                            </div>
                            <div class="card-title">Biens (<?php echo e($stats['nb_biens'] ?? 0); ?>)</div>
                        </div>
                        <a href="<?php echo e(route('admin.biens.create')); ?>"
                           style="font-size:12px;color:#c9a84c;text-decoration:none">
                            + Ajouter un bien
                        </a>
                    </div>
                    <?php if(isset($biens) && $biens->count() > 0): ?>
                    <div style="overflow-x:auto">
                        <table class="dt">
                            <thead>
                                <tr>
                                    <th>Référence</th>
                                    <th>Adresse</th>
                                    <th style="text-align:right">Loyer</th>
                                    <th style="text-align:center">Statut</th>
                                    <th>Locataire actuel</th>
                                    <th style="text-align:center">Voir</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $biens; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bien): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td style="font-family:'Syne',sans-serif;font-size:12px;font-weight:600">
                                        <?php echo e($bien->reference); ?>

                                    </td>
                                    <td style="font-size:12px;color:#6b7280">
                                        <?php echo e($bien->adresse); ?>, <?php echo e($bien->ville); ?>

                                    </td>
                                    <td style="text-align:right;font-weight:600;color:#c9a84c;font-family:'Syne',sans-serif">
                                        <?php echo e(number_format($bien->loyer_mensuel, 0, ',', ' ')); ?> F
                                    </td>
                                    <td style="text-align:center">
                                        <?php
                                            $bs = match($bien->statut) {
                                                'loue'       => 'background:#dbeafe;color:#1d4ed8',
                                                'disponible' => 'background:#dcfce7;color:#16a34a',
                                                'en_travaux' => 'background:#fef9c3;color:#a16207',
                                                default      => 'background:#f3f4f6;color:#6b7280',
                                            };
                                        ?>
                                        <span style="display:inline-flex;padding:2px 9px;border-radius:99px;font-size:10px;font-weight:600;<?php echo e($bs); ?>">
                                            <?php echo e($bien->statut_label); ?>

                                        </span>
                                    </td>
                                    <td style="font-size:12px;color:#6b7280">
                                        <?php echo e($bien->contratActif?->locataire?->name ?? '—'); ?>

                                    </td>
                                    <td style="text-align:center">
                                        <a href="<?php echo e(route('admin.biens.show', $bien)); ?>"
                                           style="display:inline-flex;align-items:center;justify-content:center;width:26px;height:26px;border:1px solid #e5e7eb;border-radius:6px;color:#6b7280;text-decoration:none"
                                           onmouseover="this.style.borderColor='#c9a84c';this.style.color='#8a6e2f'"
                                           onmouseout="this.style.borderColor='#e5e7eb';this.style.color='#6b7280'">
                                            <svg style="width:12px;height:12px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div style="padding:28px;text-align:center;color:#9ca3af;font-size:13px">
                        Aucun bien enregistré pour ce propriétaire.
                    </div>
                    <?php endif; ?>
                </div>

                
                <?php if(isset($paiements) && $paiements->count() > 0): ?>
                <div class="card">
                    <div class="card-hd">
                        <div class="card-hd-left">
                            <div class="card-icon green">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                            </div>
                            <div class="card-title">Derniers paiements</div>
                        </div>
                    </div>
                    <div style="overflow-x:auto">
                        <table class="dt">
                            <thead>
                                <tr>
                                    <th>Bien</th>
                                    <th>Période</th>
                                    <th style="text-align:right">Montant</th>
                                    <th style="text-align:right">Net reversé</th>
                                    <th style="text-align:center">PDF</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $paiements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td style="font-size:12px;font-weight:500">
                                        <?php echo e($p->contrat?->bien?->reference ?? '—'); ?>

                                    </td>
                                    <td>
                                        <span style="display:inline-flex;padding:2px 8px;background:#f5e9c9;color:#8a6e2f;border-radius:99px;font-size:11px;font-weight:600;font-family:'Syne',sans-serif">
                                            <?php echo e(\Carbon\Carbon::parse($p->periode)->translatedFormat('M Y')); ?>

                                        </span>
                                    </td>
                                    <td style="text-align:right;font-weight:700;font-family:'Syne',sans-serif;color:#0d1117">
                                        <?php echo e(number_format($p->montant_encaisse, 0, ',', ' ')); ?> F
                                    </td>
                                    <td style="text-align:right;color:#16a34a;font-weight:600">
                                        <?php echo e(number_format($p->net_proprietaire ?? 0, 0, ',', ' ')); ?> F
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
                </div>
                <?php endif; ?>

            </div>

            
            <div>
                <div class="side-card">
                    <div class="side-hd"><div class="side-title">Informations</div></div>
                    <div class="side-body">
                        <div class="side-row">
                            <span class="side-lbl">Nom</span>
                            <span class="side-val"><?php echo e($user->name); ?></span>
                        </div>
                        <div class="side-row">
                            <span class="side-lbl">Email</span>
                            <span class="side-val" style="font-size:11px"><?php echo e($user->email); ?></span>
                        </div>
                        <?php if($user->telephone): ?>
                        <div class="side-row">
                            <span class="side-lbl">Téléphone</span>
                            <span class="side-val"><?php echo e($user->telephone); ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if($user->adresse): ?>
                        <div class="side-row">
                            <span class="side-lbl">Adresse</span>
                            <span class="side-val" style="font-size:11px"><?php echo e($user->adresse); ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if($user->proprietaire?->ninea): ?>
                        <div class="side-row">
                            <span class="side-lbl">NINEA</span>
                            <span class="side-val"><?php echo e($user->proprietaire->ninea); ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if($user->proprietaire?->mode_paiement_prefere): ?>
                        <div class="side-row">
                            <span class="side-lbl">Mode paiement</span>
                            <span class="side-val"><?php echo e(ucfirst($user->proprietaire->mode_paiement_prefere)); ?></span>
                        </div>
                        <?php endif; ?>
                        <div class="side-row">
                            <span class="side-lbl">Total loyers</span>
                            <span class="side-val gold"><?php echo e(number_format($stats['total_loyers'] ?? 0, 0, ',', ' ')); ?> F</span>
                        </div>
                        <div class="side-row">
                            <span class="side-lbl">Net reversé</span>
                            <span class="side-val" style="color:#4ade80"><?php echo e(number_format($stats['total_net'] ?? 0, 0, ',', ' ')); ?> F</span>
                        </div>
                        <div class="side-row">
                            <span class="side-lbl">Membre depuis</span>
                            <span class="side-val"><?php echo e($user->created_at?->format('d/m/Y')); ?></span>
                        </div>
                    </div>
                </div>

                
                <?php if(isset($locatairesActifs) && $locatairesActifs->count() > 0): ?>
                <div class="side-card">
                    <div class="side-hd"><div class="side-title">Locataires actifs</div></div>
                    <div class="side-body">
                        <?php $__currentLoopData = $locatairesActifs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div style="padding:8px 0;border-bottom:1px solid rgba(255,255,255,.06)">
                            <div style="font-size:12px;color:#e6edf3;font-weight:500"><?php echo e($lc->locataire?->name ?? '—'); ?></div>
                            <div style="font-size:11px;color:rgba(255,255,255,.35)"><?php echo e($lc->bien?->reference); ?></div>
                            <div style="font-size:11px;color:#c9a84c"><?php echo e(number_format($lc->loyer_contractuel, 0, ',', ' ')); ?> F/mois</div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>

    
    <?php elseif($user->isLocataire()): ?>

        <div class="page-grid">
            <div>

                
                <?php if(isset($stats['contrat_actif']) && $stats['contrat_actif']): ?>
                <?php $contrat = $stats['contrat_actif']; ?>
                <div class="card">
                    <div class="card-hd">
                        <div class="card-hd-left">
                            <div class="card-icon gold">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                            </div>
                            <div class="card-title">Contrat actif</div>
                        </div>
                        <a href="<?php echo e(route('admin.contrats.show', $contrat)); ?>"
                           style="font-size:12px;color:#6b7280;text-decoration:none">Voir →</a>
                    </div>
                    <div class="card-body">
                        <div class="info-grid-3">
                            <div>
                                <div class="il">Bien loué</div>
                                <div class="iv"><?php echo e($contrat->bien?->reference ?? '—'); ?></div>
                                <div class="iv-sub"><?php echo e($contrat->bien?->adresse); ?></div>
                                <div class="iv-sub"><?php echo e($contrat->bien?->ville); ?></div>
                            </div>
                            <div>
                                <div class="il">Loyer contractuel</div>
                                <div class="iv" style="color:#c9a84c;font-weight:700">
                                    <?php echo e(number_format($contrat->loyer_contractuel, 0, ',', ' ')); ?> F
                                </div>
                            </div>
                            <div>
                                <div class="il">Début contrat</div>
                                <div class="iv"><?php echo e($contrat->date_debut?->format('d/m/Y')); ?></div>
                                <div class="iv-sub"><?php echo e($contrat->date_fin?->format('d/m/Y') ?? 'Ouvert'); ?></div>
                            </div>
                        </div>
                    </div>
                </div>

                
                <div class="kpi-row">
                    <div class="kpi gold">
                        <div class="kpi-lbl">Total payé</div>
                        <div class="kpi-val" style="font-size:16px;color:#c9a84c">
                            <?php echo e(number_format($stats['total_paye'] ?? 0, 0, ',', ' ')); ?>

                        </div>
                        <div class="kpi-sub">FCFA versés</div>
                    </div>
                    <div class="kpi blue">
                        <div class="kpi-lbl">Paiements</div>
                        <div class="kpi-val" style="color:#1d4ed8"><?php echo e($stats['nb_paiements'] ?? 0); ?></div>
                        <div class="kpi-sub">Validés</div>
                    </div>
                    <div class="kpi green">
                        <div class="kpi-lbl">Statut</div>
                        <div class="kpi-val" style="font-size:14px;color:#16a34a">Actif</div>
                        <div class="kpi-sub">Contrat en cours</div>
                    </div>
                </div>
                <?php else: ?>
                <div style="background:#fef9c3;border:1px solid #fde68a;border-radius:12px;padding:20px;margin-bottom:20px;font-size:13px;color:#92400e">
                    <strong>Aucun contrat actif</strong> — Ce locataire n'a pas de bail en cours.
                    <a href="<?php echo e(route('admin.contrats.create')); ?>" style="color:#92400e;font-weight:600;margin-left:8px">
                        Créer un contrat →
                    </a>
                </div>
                <?php endif; ?>

            </div>

            
            <div>
                <div class="side-card">
                    <div class="side-hd"><div class="side-title">Informations</div></div>
                    <div class="side-body">
                        <div class="side-row">
                            <span class="side-lbl">Nom</span>
                            <span class="side-val"><?php echo e($user->name); ?></span>
                        </div>
                        <div class="side-row">
                            <span class="side-lbl">Email</span>
                            <span class="side-val" style="font-size:11px"><?php echo e($user->email); ?></span>
                        </div>
                        <?php if($user->telephone): ?>
                        <div class="side-row">
                            <span class="side-lbl">Téléphone</span>
                            <span class="side-val"><?php echo e($user->telephone); ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if($user->adresse): ?>
                        <div class="side-row">
                            <span class="side-lbl">Adresse</span>
                            <span class="side-val" style="font-size:11px"><?php echo e($user->adresse); ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if($user->locataire?->profession): ?>
                        <div class="side-row">
                            <span class="side-lbl">Profession</span>
                            <span class="side-val"><?php echo e($user->locataire->profession); ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if($user->locataire?->employeur): ?>
                        <div class="side-row">
                            <span class="side-lbl">Employeur</span>
                            <span class="side-val"><?php echo e($user->locataire->employeur); ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if($user->locataire?->contact_urgence_nom): ?>
                        <div class="side-row">
                            <span class="side-lbl">Contact urgence</span>
                            <span class="side-val" style="font-size:11px"><?php echo e($user->locataire->contact_urgence_nom); ?></span>
                        </div>
                        <?php endif; ?>
                        <div class="side-row">
                            <span class="side-lbl">Membre depuis</span>
                            <span class="side-val"><?php echo e($user->created_at?->format('d/m/Y')); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ph\bimotech-immo\resources\views/users/show.blade.php ENDPATH**/ ?>