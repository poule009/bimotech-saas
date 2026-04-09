
<?php $__env->startSection('title', 'Contrat — ' . ($contrat->reference_bail ?? 'Détail')); ?>
<?php $__env->startSection('breadcrumb', 'Contrats › Détail'); ?>

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

/* Badges */
.badge { display:inline-flex;align-items:center;gap:4px;padding:4px 12px;border-radius:99px;font-size:11px;font-weight:600; }
.badge-actif   { background:#dcfce7;color:#16a34a; }
.badge-resilié { background:#fee2e2;color:#dc2626; }
.badge-expiré  { background:#f3f4f6;color:#6b7280; }
.bdot { width:5px;height:5px;border-radius:50%;background:currentColor; }

/* Hero */
.hero { background:linear-gradient(135deg,#0d1117 0%,#1c2333 100%);border-radius:14px;padding:22px 24px;margin-bottom:20px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px; }
.hero-ref { font-family:'Syne',sans-serif;font-size:13px;font-weight:600;color:rgba(255,255,255,.4);margin-bottom:6px; }
.hero-bien { font-family:'Syne',sans-serif;font-size:20px;font-weight:700;color:#fff;letter-spacing:-.3px;margin-bottom:4px; }
.hero-sub { font-size:13px;color:rgba(255,255,255,.5); }
.hero-loyer { text-align:right; }
.hero-loyer-lbl { font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:rgba(201,168,76,.6);margin-bottom:4px; }
.hero-loyer-val { font-family:'Syne',sans-serif;font-size:28px;font-weight:700;color:#c9a84c; }
.hero-loyer-sub { font-size:11px;color:rgba(255,255,255,.35);margin-top:3px; }

/* KPIs */
.kpi-row { display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:20px; }
.kpi-mini { background:#fff;border:1px solid #e5e7eb;border-radius:10px;padding:14px 16px; }
.kpi-mini-lbl { font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#9ca3af;margin-bottom:5px; }
.kpi-mini-val { font-family:'Syne',sans-serif;font-size:20px;font-weight:700;color:#0d1117; }

/* Table paiements */
.dt { width:100%;border-collapse:collapse; }
.dt th { padding:9px 16px;text-align:left;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.7px;color:#9ca3af;background:#f9fafb;border-bottom:1px solid #e5e7eb; }
.dt td { padding:11px 16px;font-size:13px;color:#374151;border-bottom:1px solid #f3f4f6;vertical-align:middle; }
.dt tbody tr:last-child td { border-bottom:none; }
.dt tbody tr:hover { background:#fafafa; }

/* Sidebar sticky */
.sidebar-sticky { position:sticky;top:24px; }
.side-card { background:#0d1117;border-radius:14px;overflow:hidden;margin-bottom:14px; }
.side-hd { padding:12px 16px;border-bottom:1px solid rgba(255,255,255,.07); }
.side-title { font-family:'Syne',sans-serif;font-size:12px;font-weight:700;color:#fff; }
.side-body { padding:14px 16px; }
.side-row { display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px solid rgba(255,255,255,.06);font-size:12px; }
.side-row:last-child { border-bottom:none; }
.side-lbl { color:rgba(255,255,255,.4); }
.side-val { color:#e6edf3;font-weight:500; }
.side-val.gold { color:#c9a84c; }
.next-period { background:rgba(201,168,76,.1);border:1px solid rgba(201,168,76,.2);border-radius:9px;padding:12px 14px;margin-top:12px; }
.next-lbl { font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:rgba(201,168,76,.6);margin-bottom:4px; }
.next-val { font-family:'Syne',sans-serif;font-size:16px;font-weight:700;color:#c9a84c; }
</style>

<div style="padding:0 0 48px">

    
    <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:#6b7280;margin-bottom:16px">
        <a href="<?php echo e(route('admin.contrats.index')); ?>" style="color:#6b7280;text-decoration:none">Contrats</a>
        <svg style="width:12px;height:12px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
        <span style="color:#0d1117;font-weight:500"><?php echo e($contrat->reference_bail ?? 'Contrat #'.$contrat->id); ?></span>
    </div>

    
    <div class="actions-bar">
        <?php if($contrat->statut === 'actif'): ?>
        <a href="<?php echo e(route('admin.paiements.create', ['contrat_id' => $contrat->id])); ?>" class="btn-act btn-green">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Enregistrer un paiement
        </a>
        <a href="<?php echo e(route('admin.contrats.edit', $contrat)); ?>" class="btn-act btn-dark">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            Modifier
        </a>
        <?php endif; ?>
        <a href="<?php echo e(route('admin.biens.show', $contrat->bien)); ?>" class="btn-act btn-outline">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
            Voir le bien
        </a>
        <a href="<?php echo e(route('admin.contrats.index')); ?>" class="btn-act btn-outline">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
            Retour
        </a>
        <?php if($contrat->statut === 'actif'): ?>
        <form method="POST" action="<?php echo e(route('admin.contrats.destroy', $contrat)); ?>"
              onsubmit="return confirm('Résilier ce contrat ? Le bien repassera en Disponible.')">
            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
            <button type="submit" class="btn-act btn-red">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                Résilier
            </button>
        </form>
        <?php endif; ?>
    </div>

    
    <div class="hero">
        <div>
            <div class="hero-ref"><?php echo e($contrat->reference_bail ?? 'BAIL-'.$contrat->id); ?></div>
            <div class="hero-bien">
                <?php echo e(\App\Models\Bien::TYPES[$contrat->bien?->type] ?? ''); ?>

                — <?php echo e($contrat->bien?->adresse); ?>

            </div>
            <div class="hero-sub">
                <?php echo e($contrat->bien?->quartier ? $contrat->bien->quartier.', ' : ''); ?>

                <?php echo e($contrat->bien?->ville); ?>

                · Réf. <?php echo e($contrat->bien?->reference); ?>

            </div>
            <div style="margin-top:10px">
                <span class="badge badge-<?php echo e($contrat->statut); ?>">
                    <span class="bdot"></span>
                    <?php echo e(\App\Models\Contrat::STATUTS[$contrat->statut] ?? $contrat->statut); ?>

                </span>
                <span class="badge" style="background:rgba(201,168,76,.15);color:#c9a84c;margin-left:6px">
                    <?php echo e(\App\Models\Contrat::TYPES_BAIL[$contrat->type_bail] ?? $contrat->type_bail); ?>

                </span>
            </div>
        </div>
        <div class="hero-loyer">
            <div class="hero-loyer-lbl">Loyer contractuel</div>
            <div class="hero-loyer-val"><?php echo e(number_format($contrat->loyer_contractuel, 0, ',', ' ')); ?> F</div>
            <div class="hero-loyer-sub">
                <?php echo e($contrat->date_debut?->format('d/m/Y')); ?>

                → <?php echo e($contrat->date_fin?->format('d/m/Y') ?? 'Contrat ouvert'); ?>

            </div>
        </div>
    </div>

    
    <div class="kpi-row">
        <div class="kpi-mini">
            <div class="kpi-mini-lbl">Total encaissé</div>
            <div class="kpi-mini-val" style="color:#c9a84c">
                <?php echo e(number_format($totalPaye, 0, ',', ' ')); ?><span style="font-size:12px;color:#9ca3af"> F</span>
            </div>
        </div>
        <div class="kpi-mini">
            <div class="kpi-mini-lbl">Net propriétaire</div>
            <div class="kpi-mini-val" style="color:#16a34a">
                <?php echo e(number_format($totalNet, 0, ',', ' ')); ?><span style="font-size:12px;color:#9ca3af"> F</span>
            </div>
        </div>
        <div class="kpi-mini">
            <div class="kpi-mini-lbl">Paiements</div>
            <div class="kpi-mini-val"><?php echo e($nbPaiements); ?></div>
        </div>
        <div class="kpi-mini">
            <div class="kpi-mini-lbl">Caution versée</div>
            <div class="kpi-mini-val">
                <?php echo e(number_format($contrat->caution, 0, ',', ' ')); ?><span style="font-size:12px;color:#9ca3af"> F</span>
            </div>
        </div>
    </div>

    <div class="page-grid">

        
        <div>

            
            <div class="card">
                <div class="card-hd">
                    <div class="card-hd-left">
                        <div class="card-icon gold">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
                        </div>
                        <div class="card-title">Parties au contrat</div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="info-grid">
                        <div>
                            <div class="il">Locataire</div>
                            <div class="iv"><?php echo e($contrat->locataire?->name ?? '—'); ?></div>
                            <div class="iv-sub"><?php echo e($contrat->locataire?->email); ?></div>
                            <div class="iv-sub"><?php echo e($contrat->locataire?->telephone ?? ''); ?></div>
                        </div>
                        <div>
                            <div class="il">Propriétaire</div>
                            <div class="iv"><?php echo e($contrat->bien?->proprietaire?->name ?? '—'); ?></div>
                            <div class="iv-sub"><?php echo e($contrat->bien?->proprietaire?->email); ?></div>
                            <div class="iv-sub"><?php echo e($contrat->bien?->proprietaire?->telephone ?? ''); ?></div>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="card">
                <div class="card-hd">
                    <div class="card-hd-left">
                        <div class="card-icon gold">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
                        </div>
                        <div class="card-title">Ventilation du loyer</div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="info-grid-3">
                        <div>
                            <div class="il">Loyer nu</div>
                            <div class="iv"><?php echo e(number_format($contrat->loyer_nu, 0, ',', ' ')); ?> F</div>
                        </div>
                        <div>
                            <div class="il">Charges</div>
                            <div class="iv"><?php echo e(number_format($contrat->charges_mensuelles ?? 0, 0, ',', ' ')); ?> F</div>
                        </div>
                        <div>
                            <div class="il">TOM</div>
                            <div class="iv"><?php echo e(number_format($contrat->tom_amount ?? 0, 0, ',', ' ')); ?> F</div>
                        </div>
                        <div>
                            <div class="il">Loyer contractuel</div>
                            <div class="iv" style="color:#c9a84c;font-weight:700"><?php echo e(number_format($contrat->loyer_contractuel, 0, ',', ' ')); ?> F</div>
                        </div>
                        <div>
                            <div class="il">Caution</div>
                            <div class="iv"><?php echo e(number_format($contrat->caution, 0, ',', ' ')); ?> F</div>
                        </div>
                        <div>
                            <div class="il">Frais agence</div>
                            <div class="iv"><?php echo e(number_format($contrat->frais_agence ?? 0, 0, ',', ' ')); ?> F</div>
                        </div>
                    </div>
                </div>
            </div>

            
            <?php if($contrat->garant_nom): ?>
            <div class="card">
                <div class="card-hd">
                    <div class="card-hd-left">
                        <div class="card-icon purple">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        </div>
                        <div class="card-title">Garant</div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="info-grid">
                        <div>
                            <div class="il">Nom</div>
                            <div class="iv"><?php echo e($contrat->garant_nom); ?></div>
                            <div class="iv-sub"><?php echo e($contrat->garant_telephone ?? ''); ?></div>
                        </div>
                        <div>
                            <div class="il">Adresse</div>
                            <div class="iv"><?php echo e($contrat->garant_adresse ?? '—'); ?></div>
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
                        <div class="card-title">Historique des paiements</div>
                    </div>
                    <?php if($contrat->statut === 'actif'): ?>
                    <a href="<?php echo e(route('admin.paiements.create', ['contrat_id' => $contrat->id])); ?>"
                       style="display:inline-flex;align-items:center;gap:5px;padding:6px 12px;background:#dcfce7;color:#16a34a;border:1px solid #bbf7d0;border-radius:7px;font-size:12px;font-weight:600;text-decoration:none">
                        <svg style="width:12px;height:12px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        Ajouter
                    </a>
                    <?php endif; ?>
                </div>

                <?php if($paiements->isEmpty()): ?>
                <div style="padding:32px;text-align:center;color:#9ca3af;font-size:13px">
                    Aucun paiement enregistré pour ce contrat.
                </div>
                <?php else: ?>
                <div style="overflow-x:auto">
                    <table class="dt">
                        <thead>
                            <tr>
                                <th>Période</th>
                                <th>Date paiement</th>
                                <th style="text-align:right">Montant</th>
                                <th style="text-align:right">Net proprio</th>
                                <th style="text-align:right">Commission</th>
                                <th>Mode</th>
                                <th style="text-align:center">Statut</th>
                                <th style="text-align:center">PDF</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $paiements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td style="font-family:'Syne',sans-serif;font-size:12px;font-weight:600">
                                    <?php echo e(\Carbon\Carbon::parse($p->periode)->translatedFormat('M Y')); ?>

                                </td>
                                <td style="font-size:12px;color:#6b7280">
                                    <?php echo e($p->date_paiement ? \Carbon\Carbon::parse($p->date_paiement)->format('d/m/Y') : '—'); ?>

                                </td>
                                <td style="text-align:right;font-weight:600;color:#0d1117">
                                    <?php echo e(number_format($p->montant_encaisse, 0, ',', ' ')); ?> F
                                </td>
                                <td style="text-align:right;color:#16a34a;font-weight:600">
                                    <?php echo e(number_format($p->net_proprietaire ?? 0, 0, ',', ' ')); ?> F
                                </td>
                                <td style="text-align:right;color:#8a6e2f">
                                    <?php echo e(number_format($p->commission_ttc ?? 0, 0, ',', ' ')); ?> F
                                </td>
                                <td style="font-size:12px;color:#6b7280">
                                    <?php echo e($p->mode_paiement ?? '—'); ?>

                                </td>
                                <td style="text-align:center">
                                    <?php
                                        $bs = match($p->statut) {
                                            'valide'  => 'background:#dcfce7;color:#16a34a',
                                            'annulé'  => 'background:#fee2e2;color:#dc2626',
                                            default   => 'background:#f3f4f6;color:#6b7280',
                                        };
                                    ?>
                                    <span style="display:inline-flex;padding:2px 9px;border-radius:99px;font-size:10px;font-weight:600;<?php echo e($bs); ?>">
                                        <?php echo e(ucfirst($p->statut)); ?>

                                    </span>
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

            
            <?php if($contrat->observations): ?>
            <div class="card">
                <div class="card-hd">
                    <div class="card-hd-left">
                        <div class="card-icon purple">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                        </div>
                        <div class="card-title">Observations</div>
                    </div>
                </div>
                <div class="card-body">
                    <p style="font-size:13px;color:#374151;line-height:1.6"><?php echo e($contrat->observations); ?></p>
                </div>
            </div>
            <?php endif; ?>

        </div>

        
        <div class="sidebar-sticky">

            
            <div class="side-card">
                <div class="side-hd"><div class="side-title">Informations</div></div>
                <div class="side-body">
                    <div class="side-row">
                        <span class="side-lbl">Référence</span>
                        <span class="side-val" style="font-family:'Syne',sans-serif;font-size:11px">
                            <?php echo e($contrat->reference_bail ?? 'BAIL-'.$contrat->id); ?>

                        </span>
                    </div>
                    <div class="side-row">
                        <span class="side-lbl">Type bail</span>
                        <span class="side-val"><?php echo e(\App\Models\Contrat::TYPES_BAIL[$contrat->type_bail] ?? $contrat->type_bail); ?></span>
                    </div>
                    <div class="side-row">
                        <span class="side-lbl">Début</span>
                        <span class="side-val"><?php echo e($contrat->date_debut?->format('d/m/Y')); ?></span>
                    </div>
                    <div class="side-row">
                        <span class="side-lbl">Fin</span>
                        <span class="side-val"><?php echo e($contrat->date_fin?->format('d/m/Y') ?? 'Ouvert'); ?></span>
                    </div>
                    <div class="side-row">
                        <span class="side-lbl">Nb mois caution</span>
                        <span class="side-val"><?php echo e($contrat->nombre_mois_caution ?? 1); ?> mois</span>
                    </div>
                    <div class="side-row">
                        <span class="side-lbl">Indexation</span>
                        <span class="side-val"><?php echo e($contrat->indexation_annuelle ?? 0); ?> %/an</span>
                    </div>
                    <div class="side-row">
                        <span class="side-lbl">Créé le</span>
                        <span class="side-val"><?php echo e($contrat->created_at?->format('d/m/Y')); ?></span>
                    </div>

                    <?php if($contrat->statut === 'actif'): ?>
                    <div class="next-period">
                        <div class="next-lbl">Prochaine période</div>
                        <div class="next-val">
                            <?php echo e($prochainePeriode?->translatedFormat('F Y') ?? '—'); ?>

                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            
            <div class="side-card">
                <div class="side-hd"><div class="side-title">Le bien</div></div>
                <div class="side-body">
                    <div class="side-row">
                        <span class="side-lbl">Référence</span>
                        <span class="side-val"><?php echo e($contrat->bien?->reference); ?></span>
                    </div>
                    <div class="side-row">
                        <span class="side-lbl">Type</span>
                        <span class="side-val"><?php echo e(\App\Models\Bien::TYPES[$contrat->bien?->type] ?? '—'); ?></span>
                    </div>
                    <div class="side-row">
                        <span class="side-lbl">Adresse</span>
                        <span class="side-val" style="font-size:11px"><?php echo e($contrat->bien?->adresse); ?></span>
                    </div>
                    <div class="side-row">
                        <span class="side-lbl">Ville</span>
                        <span class="side-val"><?php echo e($contrat->bien?->ville); ?></span>
                    </div>
                    <a href="<?php echo e(route('admin.biens.show', $contrat->bien)); ?>"
                       style="display:flex;align-items:center;justify-content:center;gap:5px;margin-top:10px;padding:7px;border:1px solid rgba(255,255,255,.1);border-radius:8px;color:#c9a84c;font-size:12px;text-decoration:none">
                        Voir le bien →
                    </a>
                </div>
            </div>

        </div>

    </div>

</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ph\bimotech-immo\resources\views/admin/contrats/show.blade.php ENDPATH**/ ?>