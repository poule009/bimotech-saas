<?php
// resources/views/locataire/dashboard.blade.php
?>
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
 <?php $__env->slot('header', null, []); ?> Mon espace locataire <?php $__env->endSlot(); ?>

<div class="section-gap">
    <h1 style="font-size:22px;font-weight:800;color:var(--text);">
        Bonjour, <?php echo e(auth()->user()->name); ?> 👋
    </h1>
    <p style="font-size:13px;color:var(--text-3);margin-top:4px;">
        <?php echo e(now()->translatedFormat('l d F Y')); ?>

    </p>
</div>

<?php $hasPaiements = $paiements->isNotEmpty(); ?>

<?php $hasContrat = !is_null($contrat); ?>

<?php $hasProchaine = !is_null($prochainePeriode); ?>

<?php $hasGarant = $hasContrat && !empty($contrat->garant_nom); ?>

<?php $hasCharges = $hasContrat && ($contrat->charges_mensuelles ?? 0) > 0; ?>

<?php $hasSurface = $hasContrat && (!empty($contrat->bien->surface_m2) || !empty($contrat->bien->nombre_pieces)); ?>

<div style="padding:20px;background:#1a3c5e;border-radius:12px;color:white;margin-bottom:20px;">
    <div style="font-size:11px;opacity:.6;text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px;">Mon logement</div>

    <?php
        $reference = $hasContrat ? $contrat->bien->reference : '—';
        $type      = $hasContrat ? $contrat->bien->type : '—';
        $adresse   = $hasContrat ? $contrat->bien->adresse.', '.$contrat->bien->ville : '—';
        $loyer     = $hasContrat ? number_format($contrat->loyer_contractuel, 0, ',', ' ').' FCFA/mois' : '—';
        $debut     = $hasContrat ? \Carbon\Carbon::parse($contrat->date_debut)->format('d/m/Y') : '—';
        $fin       = $hasContrat ? ($contrat->date_fin ? \Carbon\Carbon::parse($contrat->date_fin)->format('d/m/Y') : 'Indéterminée') : '—';
        $typeBail  = $hasContrat ? ucfirst($contrat->type_bail ?? 'habitation') : '—';
        $caution   = $hasContrat ? number_format($contrat->caution, 0, ',', ' ').' FCFA' : '—';
    ?>

    <div style="font-size:20px;font-weight:800;margin-bottom:4px;"><?php echo e($reference); ?></div>
    <div style="font-size:13px;opacity:.8;"><?php echo e($type); ?> · <?php echo e($adresse); ?></div>

    <?php if($hasSurface): ?>
    <div style="font-size:12px;opacity:.6;margin-top:4px;">
        <?php echo e($contrat->bien->surface_m2 ? $contrat->bien->surface_m2.' m²' : ''); ?>

        <?php echo e($contrat->bien->nombre_pieces ? '· '.$contrat->bien->nombre_pieces.' pièces' : ''); ?>

        <?php echo e($contrat->bien->meuble ? '· Meublé' : ''); ?>

    </div>
    <?php endif; ?>

    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-top:20px;padding-top:16px;border-top:1px solid rgba(255,255,255,.2);">
        <div>
            <div style="font-size:10px;opacity:.5;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Loyer</div>
            <div style="font-size:14px;font-weight:700;"><?php echo e($loyer); ?></div>
        </div>
        <div>
            <div style="font-size:10px;opacity:.5;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Début bail</div>
            <div style="font-size:14px;font-weight:700;"><?php echo e($debut); ?></div>
        </div>
        <div>
            <div style="font-size:10px;opacity:.5;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Type bail</div>
            <div style="font-size:14px;font-weight:700;"><?php echo e($typeBail); ?></div>
        </div>
        <div>
            <div style="font-size:10px;opacity:.5;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Fin bail</div>
            <div style="font-size:14px;font-weight:700;"><?php echo e($fin); ?></div>
        </div>
        <div>
            <div style="font-size:10px;opacity:.5;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Caution</div>
            <div style="font-size:14px;font-weight:700;"><?php echo e($caution); ?></div>
        </div>
        <div>
            <div style="font-size:10px;opacity:.5;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Statut</div>
            <div style="font-size:12px;font-weight:700;background:rgba(255,255,255,.15);padding:3px 10px;border-radius:999px;display:inline-block;">
                <?php echo e($hasContrat ? 'Actif' : 'Aucun contrat'); ?>

            </div>
        </div>
    </div>
</div>

<?php if($hasCharges): ?>
<div style="background:#fffbeb;border:1px solid #fde68a;border-radius:12px;padding:14px 18px;margin-bottom:20px;display:flex;align-items:center;justify-content:space-between;">
    <div style="font-size:13px;font-weight:600;color:#92400e;">Charges mensuelles</div>
    <div style="font-size:15px;font-weight:800;color:#d97706;"><?php echo e(number_format($contrat->charges_mensuelles, 0, ',', ' ')); ?> FCFA</div>
</div>
<?php endif; ?>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:20px;">
    <div class="card" style="padding:16px;text-align:center;">
        <div style="font-size:28px;font-weight:800;color:var(--text);"><?php echo e($stats['nb_paiements']); ?></div>
        <div style="font-size:12px;color:var(--text-2);margin-top:4px;font-weight:600;">Paiements effectués</div>
    </div>
    <div class="card" style="padding:16px;text-align:center;">
        <div style="font-size:18px;font-weight:800;color:#3b82f6;"><?php echo e(number_format($stats['total_paye'], 0, ',', ' ')); ?></div>
        <div style="font-size:11px;color:#6b7280;margin-top:1px;">FCFA</div>
        <div style="font-size:12px;color:var(--text-2);margin-top:3px;font-weight:600;">Total payé</div>
    </div>
</div>

<?php if($hasProchaine): ?>
<div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:12px;padding:16px 20px;margin-bottom:20px;display:flex;align-items:center;justify-content:space-between;">
    <div>
        <div style="font-size:12px;font-weight:700;color:#15803d;margin-bottom:2px;">Prochain loyer</div>
        <div style="font-size:11px;color:#16a34a;"><?php echo e(\Carbon\Carbon::parse($prochainePeriode)->translatedFormat('F Y')); ?></div>
    </div>
    <div style="font-size:18px;font-weight:800;color:#15803d;">
        <?php echo e($hasContrat ? number_format($contrat->loyer_contractuel, 0, ',', ' ').' FCFA' : '—'); ?>

    </div>
</div>
<?php endif; ?>

<?php if($hasGarant): ?>
<div class="card" style="padding:16px 20px;margin-bottom:20px;">
    <div style="font-size:12px;font-weight:700;color:var(--text-2);text-transform:uppercase;letter-spacing:.5px;margin-bottom:12px;">Garant</div>
    <div style="font-size:13px;color:var(--text);font-weight:600;"><?php echo e($contrat->garant_nom); ?></div>
    <div style="font-size:12px;color:var(--text-3);margin-top:4px;">
        <?php echo e($contrat->garant_telephone ?? ''); ?>

        <?php echo e($contrat->garant_adresse ? ' · '.$contrat->garant_adresse : ''); ?>

    </div>
</div>
<?php endif; ?>

<div style="font-size:15px;font-weight:700;color:var(--text);margin-bottom:14px;">Mes paiements</div>

<div class="card">
    <?php if($hasPaiements): ?>
        <?php $__currentLoopData = $paiements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 20px;border-bottom:1px solid var(--border);">
            <div>
                <div style="font-size:13px;font-weight:700;color:var(--text);">
                    <?php echo e(\Carbon\Carbon::parse($p->periode)->translatedFormat('F Y')); ?>

                </div>
                <div style="font-size:11px;color:var(--text-3);margin-top:2px;">
                    <?php echo e(\Carbon\Carbon::parse($p->date_paiement)->format('d/m/Y')); ?>

                    <?php echo e($p->mode_paiement ? ' · '.ucfirst(str_replace('_', ' ', $p->mode_paiement)) : ''); ?>

                </div>
            </div>
            <div style="display:flex;align-items:center;gap:12px;">
                <div style="text-align:right;">
                    <div style="font-size:14px;font-weight:800;color:var(--text);"><?php echo e(number_format($p->montant_encaisse, 0, ',', ' ')); ?> FCFA</div>
                    <div style="font-size:11px;color:var(--text-3);"><?php echo e($p->reference_paiement ?? ''); ?></div>
                </div>
                <a href="<?php echo e(route('locataire.paiements.pdf', $p)); ?>"
                   style="width:32px;height:32px;border-radius:8px;border:1px solid var(--border);background:var(--bg);display:flex;align-items:center;justify-content:center;text-decoration:none;font-size:15px;">
                    📄
                </a>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <?php else: ?>
        <div style="padding:40px;text-align:center;color:var(--text-3);">
            <div style="font-size:32px;margin-bottom:10px;opacity:.4;">💳</div>
            <div style="font-size:14px;">Aucun paiement enregistré.</div>
        </div>
    <?php endif; ?>
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
<?php endif; ?><?php /**PATH C:\Users\ph\bimotech-immo\resources\views/locataire/dashboard.blade.php ENDPATH**/ ?>