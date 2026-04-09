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
 <?php $__env->slot('header', null, []); ?> <?php echo e($bien->reference); ?> — <?php echo e($bien->type_label); ?> <?php $__env->endSlot(); ?>

<style>
:root {
    --gold:#c9a84c; --dark:#0d1117; --border:#e5e7eb;
    --text:#0d1117; --text2:#6b7280; --bg:#f9fafb; --green:#16a34a; --red:#dc2626;
}
.page-grid { display:grid; grid-template-columns:1fr 320px; gap:24px; align-items:start; }
.card { background:#fff; border:1px solid var(--border); border-radius:14px; overflow:hidden; margin-bottom:16px; }
.card-hd { padding:14px 20px; border-bottom:1px solid var(--border); display:flex; align-items:center; justify-content:space-between; }
.card-title { font-family:'Syne',sans-serif; font-size:14px; font-weight:700; color:var(--text); }
.card-action { font-size:12px; color:#6b7280; text-decoration:none; }
.card-action:hover { color:var(--gold); }
.card-body { padding:18px 20px; }

/* KPIs */
.kpi-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:12px; margin-bottom:20px; }
.kpi { background:#fff; border:1px solid var(--border); border-radius:12px; padding:16px; }
.kpi-lbl { font-size:10px; font-weight:600; text-transform:uppercase; letter-spacing:.8px; color:#9ca3af; margin-bottom:8px; }
.kpi-val { font-family:'Syne',sans-serif; font-size:22px; font-weight:700; color:var(--text); line-height:1; }
.kpi-sub { font-size:11px; color:#9ca3af; margin-top:4px; }

/* Statut badge */
.badge { display:inline-flex; align-items:center; gap:5px; padding:4px 12px; border-radius:99px; font-size:12px; font-weight:600; }
.badge-disponible { background:#dcfce7; color:#16a34a; }
.badge-loue { background:#dbeafe; color:#1d4ed8; }
.badge-en_travaux { background:#fef9c3; color:#a16207; }
.badge-archive { background:#f3f4f6; color:#6b7280; }

/* Actions bar */
.actions-bar { display:flex; flex-wrap:wrap; gap:8px; margin-bottom:20px; }
.btn-action { display:inline-flex; align-items:center; gap:6px; padding:9px 16px; border-radius:8px; font-size:13px; font-weight:500; font-family:'DM Sans',sans-serif; cursor:pointer; text-decoration:none; border:none; transition:all .15s; }
.btn-dark { background:#0d1117; color:#fff; }
.btn-dark:hover { background:#1c2333; }
.btn-green { background:#16a34a; color:#fff; }
.btn-green:hover { background:#15803d; }
.btn-outline { background:#fff; color:#374151; border:1px solid var(--border); }
.btn-outline:hover { background:var(--bg); }
.btn-danger { background:#fff; color:var(--red); border:1px solid #fca5a5; }
.btn-danger:hover { background:#fef2f2; }
.btn-action svg { width:14px; height:14px; }

/* Détails */
.detail-grid { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
.detail-item { }
.detail-lbl { font-size:11px; color:#9ca3af; margin-bottom:2px; }
.detail-val { font-size:14px; font-weight:500; color:var(--text); }

/* Paiements table */
.dt { width:100%; border-collapse:collapse; }
.dt th { padding:9px 14px; text-align:left; font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.6px; color:#9ca3af; background:var(--bg); border-bottom:1px solid var(--border); }
.dt td { padding:11px 14px; font-size:13px; color:#374151; border-bottom:1px solid #f3f4f6; }
.dt tbody tr:last-child td { border-bottom:none; }
.dt tbody tr:hover { background:var(--bg); }

/* Photo hero */
.photo-hero { background:var(--bg); border-radius:12px; overflow:hidden; aspect-ratio:16/9; display:flex; align-items:center; justify-content:center; margin-bottom:16px; border:1px solid var(--border); }
.photo-hero img { width:100%; height:100%; object-fit:cover; }
.photo-placeholder { display:flex; flex-direction:column; align-items:center; gap:8px; color:#d1d5db; }
.photo-placeholder svg { width:48px; height:48px; }
</style>

<div style="padding:24px 32px 48px">

    
    <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:#6b7280;margin-bottom:16px">
        
        <a href="<?php echo e(route('admin.biens.index')); ?>" style="color:#6b7280;text-decoration:none">Biens</a>
        <svg style="width:12px;height:12px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
        <span style="color:#0d1117;font-weight:500"><?php echo e($bien->reference); ?></span>
    </div>

    
    <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:20px;gap:12px;flex-wrap:wrap">
        <div>
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:4px">
                <h1 style="font-family:'Syne',sans-serif;font-size:22px;font-weight:700;color:#0d1117;letter-spacing:-.4px">
                    <?php echo e($bien->type_label); ?> — <?php echo e($bien->reference); ?>

                </h1>
                <span class="badge badge-<?php echo e($bien->statut); ?>"><?php echo e($bien->statut_label); ?></span>
                <?php if($bien->meuble): ?>
                    <span class="badge" style="background:#f5e9c9;color:#8a6e2f">Meublé</span>
                <?php endif; ?>
            </div>
            <p style="font-size:13px;color:#6b7280">
                <?php echo e($bien->adresse); ?><?php if($bien->quartier): ?>, <?php echo e($bien->quartier); ?><?php endif; ?>, <?php echo e($bien->ville); ?>

            </p>
        </div>
    </div>

    
    <div class="actions-bar">
        
        <a href="<?php echo e(route('admin.biens.edit', $bien)); ?>" class="btn-action btn-dark">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            Modifier ce bien
        </a>

        <?php if(! $bien->contratActif): ?>
            <a href="<?php echo e(route('admin.contrats.create', ['bien_id' => $bien->id])); ?>" class="btn-action btn-green">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><line x1="12" y1="11" x2="12" y2="17"/><line x1="9" y1="14" x2="15" y2="14"/></svg>
                Créer un contrat
            </a>
        <?php endif; ?>

        <?php if($bien->contratActif): ?>
            <a href="<?php echo e(route('admin.paiements.create', ['contrat_id' => $bien->contratActif->id])); ?>" class="btn-action btn-outline">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
                Enregistrer un paiement
            </a>
        <?php endif; ?>

        
        <a href="<?php echo e(route('admin.biens.index')); ?>" class="btn-action btn-outline">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
            Retour à la liste
        </a>

        <?php if(! $bien->contratActif): ?>
        
        <form method="POST" action="<?php echo e(route('admin.biens.destroy', $bien)); ?>"
              onsubmit="return confirm('Archiver ce bien ? Il ne sera plus visible dans la liste.')">
            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
            <button type="submit" class="btn-action btn-danger">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/></svg>
                Archiver
            </button>
        </form>
        <?php endif; ?>
    </div>

    
    <div class="kpi-grid">
        <div class="kpi">
            <div class="kpi-lbl">Loyer hors charges</div>
            <div class="kpi-val" style="color:var(--gold)"><?php echo e(number_format($bien->loyer_hors_charges, 0, ',', ' ')); ?> <span style="font-size:13px">F</span></div>
            <div class="kpi-sub">Commission <?php echo e($bien->taux_commission ?? 10); ?>%</div>
        </div>
        <div class="kpi">
            <div class="kpi-lbl">Charges mensuelles</div>
            <div class="kpi-val"><?php echo e(number_format($bien->charges ?? 0, 0, ',', ' ')); ?> <span style="font-size:13px">F</span></div>
            <div class="kpi-sub">Surface : <?php echo e($bien->surface); ?> m²</div>
        </div>
        <div class="kpi">
            <div class="kpi-lbl">Statut locatif</div>
            <div class="kpi-val" style="font-size:16px;color:<?php echo e($bien->contratActif ? '#16a34a' : '#6b7280'); ?>">
                <?php echo e($bien->contratActif ? 'Loué' : 'Disponible'); ?>

            </div>
            <div class="kpi-sub">
                <?php if($bien->contratActif): ?>
                    <?php echo e($bien->contratActif->locataire?->name ?? '—'); ?>

                <?php else: ?>
                    Aucun locataire actif
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="page-grid">

        
        <div>

            
            <div class="photo-hero">
                <?php $photo = $bien->photos->firstWhere('est_principale', true) ?? $bien->photos->first(); ?>
                <?php if($photo): ?>
                    <img src="<?php echo e(asset('storage/' . $photo->chemin)); ?>" alt="<?php echo e($bien->titre); ?>">
                <?php else: ?>
                    <div class="photo-placeholder">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                        <span style="font-size:13px">Aucune photo</span>
                    </div>
                <?php endif; ?>
            </div>

            
            <div class="card">
                <div class="card-hd">
                    <div class="card-title">Informations du bien</div>
                </div>
                <div class="card-body">
                    <div class="detail-grid">
                        <div class="detail-item">
                            <div class="detail-lbl">Type</div>
                            <div class="detail-val"><?php echo e($bien->type_label); ?></div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-lbl">Référence</div>
                            <div class="detail-val" style="font-family:'Syne',sans-serif"><?php echo e($bien->reference); ?></div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-lbl">Surface</div>
                            <div class="detail-val"><?php echo e($bien->surface); ?> m²</div>
                        </div>
                        <?php if($bien->nb_pieces): ?>
                        <div class="detail-item">
                            <div class="detail-lbl">Pièces</div>
                            <div class="detail-val"><?php echo e($bien->nb_pieces); ?> pièce(s)</div>
                        </div>
                        <?php endif; ?>
                        <div class="detail-item">
                            <div class="detail-lbl">Adresse</div>
                            <div class="detail-val"><?php echo e($bien->adresse); ?></div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-lbl">Quartier / Ville</div>
                            <div class="detail-val"><?php echo e($bien->quartier); ?>, <?php echo e($bien->ville); ?></div>
                        </div>
                        <?php if($bien->depot_garantie): ?>
                        <div class="detail-item">
                            <div class="detail-lbl">Dépôt de garantie</div>
                            <div class="detail-val"><?php echo e(number_format($bien->depot_garantie, 0, ',', ' ')); ?> F</div>
                        </div>
                        <?php endif; ?>
                        <div class="detail-item">
                            <div class="detail-lbl">Meublé</div>
                            <div class="detail-val"><?php echo e($bien->meuble ? 'Oui' : 'Non'); ?></div>
                        </div>
                    </div>

                    <?php if($bien->description): ?>
                    <div style="margin-top:16px;padding-top:14px;border-top:1px solid #f3f4f6">
                        <div class="detail-lbl" style="margin-bottom:6px">Description</div>
                        <p style="font-size:13px;color:#374151;line-height:1.6"><?php echo e($bien->description); ?></p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            
            <?php if($bien->contratActif): ?>
            <div class="card">
                <div class="card-hd">
                    <div class="card-title">Contrat actif</div>
                    <a href="<?php echo e(route('admin.contrats.show', $bien->contratActif)); ?>" class="card-action">Voir le contrat →</a>
                </div>
                <div class="card-body">
                    <div class="detail-grid">
                        <div class="detail-item">
                            <div class="detail-lbl">Locataire</div>
                            <div class="detail-val"><?php echo e($bien->contratActif->locataire?->name ?? '—'); ?></div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-lbl">Référence bail</div>
                            <div class="detail-val" style="font-family:'Syne',sans-serif"><?php echo e($bien->contratActif->reference_bail_affichee); ?></div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-lbl">Début</div>
                            <div class="detail-val"><?php echo e($bien->contratActif->date_debut?->format('d/m/Y')); ?></div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-lbl">Fin</div>
                            <div class="detail-val"><?php echo e($bien->contratActif->date_fin?->format('d/m/Y') ?? 'Indéterminée'); ?></div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-lbl">Loyer contractuel</div>
                            <div class="detail-val" style="color:var(--gold);font-weight:600"><?php echo e(number_format($bien->contratActif->loyer_contractuel, 0, ',', ' ')); ?> F</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-lbl">Caution</div>
                            <div class="detail-val"><?php echo e(number_format($bien->contratActif->caution, 0, ',', ' ')); ?> F</div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            
            <?php if($bien->contrats->count() > 0): ?>
            <div class="card">
                <div class="card-hd">
                    <div class="card-title">Historique des contrats</div>
                </div>
                <div style="overflow-x:auto">
                    <table class="dt">
                        <thead>
                            <tr>
                                <th>Référence</th>
                                <th>Locataire</th>
                                <th>Début</th>
                                <th>Fin</th>
                                <th>Statut</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $bien->contrats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contrat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td style="font-family:'Syne',sans-serif;font-size:11px;color:#9ca3af"><?php echo e($contrat->reference_bail_affichee); ?></td>
                                <td><?php echo e($contrat->locataire?->name ?? '—'); ?></td>
                                <td><?php echo e($contrat->date_debut?->format('d/m/Y')); ?></td>
                                <td><?php echo e($contrat->date_fin?->format('d/m/Y') ?? '—'); ?></td>
                                <td>
                                    <span class="badge badge-<?php echo e($contrat->statut); ?>" style="font-size:11px">
                                        <?php echo e(\App\Models\Contrat::STATUTS[$contrat->statut] ?? $contrat->statut); ?>

                                    </span>
                                </td>
                                <td>
                                    <a href="<?php echo e(route('admin.contrats.show', $contrat)); ?>"
                                       style="font-size:12px;color:#6b7280;text-decoration:none">Voir →</a>
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

            
            <div class="card">
                <div class="card-hd">
                    <div class="card-title">Propriétaire</div>
                    <?php if($bien->proprietaire): ?>
                    <a href="<?php echo e(route('admin.users.show', $bien->proprietaire)); ?>" class="card-action">Profil →</a>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <?php if($bien->proprietaire): ?>
                    <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px">
                        <div style="width:40px;height:40px;border-radius:50%;background:#f5e9c9;display:flex;align-items:center;justify-content:center;font-family:'Syne',sans-serif;font-size:16px;font-weight:700;color:#8a6e2f;flex-shrink:0">
                            <?php echo e(strtoupper(substr($bien->proprietaire->name, 0, 1))); ?>

                        </div>
                        <div>
                            <div style="font-size:14px;font-weight:600;color:#0d1117"><?php echo e($bien->proprietaire->name); ?></div>
                            <div style="font-size:12px;color:#6b7280"><?php echo e($bien->proprietaire->email); ?></div>
                        </div>
                    </div>
                    <?php if($bien->proprietaire->telephone): ?>
                    <div style="font-size:12px;color:#6b7280;margin-top:4px">
                        <span style="color:#9ca3af">Tél : </span><?php echo e($bien->proprietaire->telephone); ?>

                    </div>
                    <?php endif; ?>
                    <?php else: ?>
                    <p style="font-size:13px;color:#9ca3af">Propriétaire non renseigné</p>
                    <?php endif; ?>
                </div>
            </div>

            
            <div class="card">
                <div class="card-hd">
                    <div class="card-title">Résumé financier</div>
                </div>
                <div class="card-body">
                    <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #f3f4f6">
                        <span style="font-size:12px;color:#6b7280">Loyer HT</span>
                        <span style="font-size:12px;font-weight:600;color:#0d1117"><?php echo e(number_format($bien->loyer_hors_charges, 0, ',', ' ')); ?> F</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #f3f4f6">
                        <span style="font-size:12px;color:#6b7280">Charges</span>
                        <span style="font-size:12px;font-weight:600;color:#0d1117"><?php echo e(number_format($bien->charges ?? 0, 0, ',', ' ')); ?> F</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #f3f4f6">
                        <span style="font-size:12px;color:#6b7280">Taux commission</span>
                        <span style="font-size:12px;font-weight:600;color:var(--gold)"><?php echo e($bien->taux_commission ?? 10); ?> %</span>
                    </div>
                    <?php
                        $commHt  = round($bien->loyer_hors_charges * (($bien->taux_commission ?? 10) / 100));
                        $tvaComm = round($commHt * 0.18);
                        $net     = $bien->loyer_hors_charges - $commHt;
                    ?>
                    <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #f3f4f6">
                        <span style="font-size:12px;color:#6b7280">Commission HT</span>
                        <span style="font-size:12px;font-weight:600;color:var(--gold)"><?php echo e(number_format($commHt, 0, ',', ' ')); ?> F</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;padding:8px 0">
                        <span style="font-size:12px;color:#6b7280">Net propriétaire</span>
                        <span style="font-size:13px;font-weight:700;color:#16a34a"><?php echo e(number_format($net, 0, ',', ' ')); ?> F</span>
                    </div>
                </div>
            </div>

        </div>

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
<?php endif; ?><?php /**PATH C:\Users\ph\bimotech-immo\resources\views/biens/show.blade.php ENDPATH**/ ?>