<?php $__env->startSection('title', 'Abonnement'); ?>
<?php $__env->startSection('breadcrumb', 'Abonnement'); ?>

<?php $__env->startSection('content'); ?>
<?php
$plans = [
    'mensuel'     => ['label'=>'Mensuel',     'total'=>'25 000',  'mensuel'=>'25 000',  'period'=>'/ mois',    'eco'=>null,   'saving'=>null],
    'trimestriel' => ['label'=>'Trimestriel', 'total'=>'67 500',  'mensuel'=>'22 500',  'period'=>'/ 3 mois',  'eco'=>'−10%', 'saving'=>'Économie 7 500 FCFA/an'],
    'semestriel'  => ['label'=>'Semestriel',  'total'=>'127 500', 'mensuel'=>'21 250',  'period'=>'/ 6 mois',  'eco'=>'−15%', 'saving'=>'Économie 22 500 FCFA/an'],
    'annuel'      => ['label'=>'Annuel',       'total'=>'240 000', 'mensuel'=>'20 000',  'period'=>'/ an',      'eco'=>'−20%', 'saving'=>'Économie 60 000 FCFA/an'],
];
$estActif = $subscription?->estActif();
$estEssai = $subscription?->estEnEssai();
$estExpire = $subscription && !$estActif && !$estEssai;
?>

<style>
.sub-page { max-width:900px; margin:0 auto; padding-bottom:60px; }

/* ── Statut banner ── */
.sub-banner {
    border-radius:14px; padding:20px 24px;
    display:flex; align-items:center; justify-content:space-between; gap:16px;
    margin-bottom:28px; flex-wrap:wrap;
}
.sub-banner.essai  { background:rgba(201,168,76,.07); border:1px solid rgba(201,168,76,.2); }
.sub-banner.actif  { background:rgba(34,197,94,.06);  border:1px solid rgba(34,197,94,.2); }
.sub-banner.expire { background:rgba(239,68,68,.06);  border:1px solid rgba(239,68,68,.2); }
.sub-banner-left   { display:flex; align-items:center; gap:14px; }
.sub-banner-icon   {
    width:44px; height:44px; border-radius:12px;
    display:flex; align-items:center; justify-content:center; flex-shrink:0;
}
.sub-banner.essai  .sub-banner-icon { background:rgba(201,168,76,.15); }
.sub-banner.actif  .sub-banner-icon { background:rgba(34,197,94,.15); }
.sub-banner.expire .sub-banner-icon { background:rgba(239,68,68,.15); }
.sub-banner-title  { font-family:'Syne',sans-serif; font-size:14px; font-weight:700; color:#e6edf3; }
.sub-banner-sub    { font-size:12.5px; color:#8b949e; margin-top:2px; }
.sub-banner-badge  {
    display:inline-flex; align-items:center; gap:6px;
    padding:7px 16px; border-radius:8px;
    font-family:'Syne',sans-serif; font-size:12px; font-weight:700;
    white-space:nowrap;
}
.sub-banner.essai  .sub-banner-badge { background:rgba(201,168,76,.15); color:#c9a84c; }
.sub-banner.actif  .sub-banner-badge { background:rgba(34,197,94,.12);  color:#4ade80; }
.sub-banner.expire .sub-banner-badge { background:rgba(239,68,68,.12);  color:#f87171; }

/* ── Section titre ── */
.sub-section-title {
    font-family:'Syne',sans-serif; font-size:18px; font-weight:700;
    color:#e6edf3; margin-bottom:6px;
}
.sub-section-sub { font-size:13px; color:#8b949e; margin-bottom:24px; }

/* ── Plan cards ── */
.plans-row {
    display:grid; grid-template-columns:repeat(4,1fr); gap:14px;
    margin-bottom:28px;
}
.plan-card {
    background:#161b22; border:1px solid rgba(255,255,255,.07);
    border-radius:14px; padding:22px 18px;
    display:flex; flex-direction:column;
    position:relative; transition:border-color .2s, transform .2s;
    cursor:default;
}
.plan-card:hover { border-color:rgba(201,168,76,.25); transform:translateY(-2px); }
.plan-card.popular {
    border-color:#c9a84c;
    background:linear-gradient(160deg,rgba(201,168,76,.05) 0%,#161b22 60%);
}
.plan-card-badge {
    position:absolute; top:-11px; left:50%; transform:translateX(-50%);
    background:linear-gradient(135deg,#c9a84c,#e8c96a);
    color:#080c12; font-size:10px; font-weight:800;
    font-family:'Syne',sans-serif;
    padding:3px 14px; border-radius:99px; white-space:nowrap;
    box-shadow:0 3px 10px rgba(201,168,76,.3);
}
.plan-card-eco {
    display:inline-block; background:rgba(34,197,94,.12); color:#4ade80;
    font-size:10px; font-weight:700; padding:2px 8px;
    border-radius:99px; margin-bottom:10px;
}
.plan-card-name {
    font-family:'Syne',sans-serif; font-size:11px; font-weight:700;
    color:#8b949e; text-transform:uppercase; letter-spacing:1.2px; margin-bottom:10px;
}
.plan-card-price {
    font-family:'Syne',sans-serif; font-size:26px; font-weight:800;
    color:#e6edf3; line-height:1; margin-bottom:2px;
}
.plan-card-price span { font-size:12px; color:#8b949e; font-weight:400; }
.plan-card-mensuel { font-size:11px; color:#8b949e; margin-bottom:16px; min-height:16px; }
.plan-card-saving  { font-size:11px; color:#4ade80; margin-bottom:16px; min-height:16px; }
.plan-card-btn {
    margin-top:auto;
    display:block; text-align:center;
    padding:10px; border-radius:9px;
    font-family:'DM Sans',sans-serif; font-size:13px; font-weight:600;
    border:none; cursor:pointer; transition:all .2s; text-decoration:none;
    width:100%;
}
.plan-card-btn-outline {
    background:transparent; color:#e6edf3;
    border:1px solid rgba(255,255,255,.12);
}
.plan-card-btn-outline:hover { border-color:rgba(255,255,255,.25); background:rgba(255,255,255,.04); }
.plan-card-btn-gold {
    background:linear-gradient(135deg,#c9a84c,#e8c96a);
    color:#080c12;
    box-shadow:0 3px 12px rgba(201,168,76,.25);
}
.plan-card-btn-gold:hover { opacity:.9; box-shadow:0 5px 20px rgba(201,168,76,.35); }

/* ── Tableau comparatif ── */
.compare-table {
    background:#161b22; border:1px solid rgba(255,255,255,.07);
    border-radius:14px; overflow:hidden; margin-bottom:32px;
}
.compare-table table { width:100%; border-collapse:collapse; font-size:13px; }
.compare-table th {
    padding:11px 18px; text-align:right;
    font-size:10px; color:#6e7681; text-transform:uppercase;
    letter-spacing:.7px; font-weight:600;
    background:rgba(255,255,255,.02);
    border-bottom:1px solid rgba(255,255,255,.06);
}
.compare-table th:first-child { text-align:left; }
.compare-table td {
    padding:12px 18px; text-align:right;
    color:#8b949e; border-bottom:1px solid rgba(255,255,255,.04);
}
.compare-table td:first-child { text-align:left; color:#c9d1d9; font-weight:500; }
.compare-table tr:last-child td { border-bottom:none; }
.compare-table .gold { color:#c9a84c; font-family:'Syne',sans-serif; font-weight:700; }
.eco-pill {
    display:inline-block; background:rgba(34,197,94,.1); color:#4ade80;
    font-size:10px; font-weight:700; padding:2px 9px; border-radius:99px;
}

/* ── Historique ── */
.hist-card {
    background:#161b22; border:1px solid rgba(255,255,255,.07);
    border-radius:14px; overflow:hidden;
}
.hist-head {
    padding:14px 20px; border-bottom:1px solid rgba(255,255,255,.06);
    display:flex; align-items:center; justify-content:space-between;
}
.hist-head-title { font-family:'Syne',sans-serif; font-size:13px; font-weight:700; color:#e6edf3; }
.hist-table { width:100%; border-collapse:collapse; font-size:12.5px; }
.hist-table th {
    padding:9px 18px; font-size:10px; color:#6e7681;
    text-transform:uppercase; letter-spacing:.7px; font-weight:600;
    background:rgba(255,255,255,.02); border-bottom:1px solid rgba(255,255,255,.06);
    text-align:left;
}
.hist-table td {
    padding:12px 18px; color:#8b949e;
    border-bottom:1px solid rgba(255,255,255,.04); vertical-align:middle;
}
.hist-table tr:last-child td { border-bottom:none; }
.hist-table tr:hover td { background:rgba(255,255,255,.02); }
.hist-badge {
    display:inline-block; font-size:10px; font-weight:700;
    padding:2px 9px; border-radius:99px;
}
.hist-badge.paye    { background:rgba(34,197,94,.12);  color:#4ade80; }
.hist-badge.attente { background:rgba(234,179,8,.1);   color:#fbbf24; }
.hist-badge.echoue  { background:rgba(239,68,68,.1);   color:#f87171; }
.plan-pill {
    display:inline-block; padding:2px 9px;
    background:rgba(201,168,76,.1); color:#c9a84c;
    font-size:10px; font-weight:700; border-radius:6px;
}

/* ── Support ── */
.support-card {
    background:#161b22; border:1px solid rgba(255,255,255,.07);
    border-radius:14px; padding:22px 24px;
    display:flex; align-items:center; justify-content:space-between;
    gap:16px; flex-wrap:wrap; margin-top:20px;
}
.support-left { display:flex; align-items:center; gap:14px; }
.support-icon {
    width:40px; height:40px; border-radius:10px;
    background:rgba(201,168,76,.1); border:1px solid rgba(201,168,76,.15);
    display:flex; align-items:center; justify-content:center; flex-shrink:0;
}
.support-title { font-family:'Syne',sans-serif; font-size:13px; font-weight:700; color:#e6edf3; }
.support-sub   { font-size:12px; color:#8b949e; margin-top:2px; }
.support-contacts { display:flex; gap:20px; flex-wrap:wrap; }
.support-contact   { font-size:12.5px; color:#8b949e; display:flex; align-items:center; gap:6px; }

@media(max-width:860px){ .plans-row{ grid-template-columns:repeat(2,1fr); } }
@media(max-width:540px){ .plans-row{ grid-template-columns:1fr; } }
</style>

<div class="sub-page">

    
    <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:#6b7280;margin-bottom:20px">
        <a href="<?php echo e(route('admin.dashboard')); ?>" style="color:#6b7280;text-decoration:none">Tableau de bord</a>
        <svg style="width:12px;height:12px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
        <span style="color:#e6edf3;font-weight:500">Abonnement</span>
    </div>

    
    <?php if(session('success')): ?>
    <div style="background:rgba(34,197,94,.08);border:1px solid rgba(34,197,94,.2);border-radius:10px;padding:13px 18px;margin-bottom:20px;font-size:13px;color:#4ade80;display:flex;align-items:center;gap:10px">
        <svg style="width:16px;height:16px;flex-shrink:0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
        <?php echo e(session('success')); ?>

    </div>
    <?php endif; ?>
    <?php if(session('warning')): ?>
    <div style="background:rgba(234,179,8,.07);border:1px solid rgba(234,179,8,.2);border-radius:10px;padding:13px 18px;margin-bottom:20px;font-size:13px;color:#fbbf24;display:flex;align-items:center;gap:10px">
        <svg style="width:16px;height:16px;flex-shrink:0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
        <?php echo e(session('warning')); ?>

    </div>
    <?php endif; ?>
    <?php if($errors->has('general')): ?>
    <div style="background:rgba(239,68,68,.07);border:1px solid rgba(239,68,68,.2);border-radius:10px;padding:13px 18px;margin-bottom:20px;font-size:13px;color:#f87171;display:flex;align-items:center;gap:10px">
        <svg style="width:16px;height:16px;flex-shrink:0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <?php echo e($errors->first('general')); ?>

    </div>
    <?php endif; ?>

    
    <?php if($subscription): ?>
    <?php if($estEssai): ?>
    <div class="sub-banner essai">
        <div class="sub-banner-left">
            <div class="sub-banner-icon">
                <svg style="width:20px;height:20px;color:#c9a84c" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <div>
                <div class="sub-banner-title">Période d'essai en cours</div>
                <div class="sub-banner-sub">
                    Expire le <?php echo e($subscription->date_fin_essai->format('d/m/Y')); ?> —
                    <strong style="color:#c9a84c"><?php echo e($subscription->joursRestantsEssai()); ?> jours restants</strong>
                </div>
            </div>
        </div>
        <div class="sub-banner-badge">
            <svg style="width:13px;height:13px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
            Essai gratuit 30 jours
        </div>
    </div>
    <?php elseif($estActif): ?>
    <div class="sub-banner actif">
        <div class="sub-banner-left">
            <div class="sub-banner-icon">
                <svg style="width:20px;height:20px;color:#4ade80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
            </div>
            <div>
                <div class="sub-banner-title">Abonnement actif — <?php echo e(\App\Models\Subscription::LABELS[$subscription->plan] ?? ''); ?></div>
                <div class="sub-banner-sub">
                    Expire le <?php echo e($subscription->date_fin_abonnement->format('d/m/Y')); ?> —
                    <strong style="color:#4ade80"><?php echo e($subscription->joursRestantsAbonnement()); ?> jours restants</strong>
                </div>
            </div>
        </div>
        <div class="sub-banner-badge">
            <svg style="width:13px;height:13px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
            Actif
        </div>
    </div>
    <?php else: ?>
    <div class="sub-banner expire">
        <div class="sub-banner-left">
            <div class="sub-banner-icon">
                <svg style="width:20px;height:20px;color:#f87171" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
            </div>
            <div>
                <div class="sub-banner-title">Accès expiré</div>
                <div class="sub-banner-sub">Votre essai ou abonnement a expiré. Choisissez un plan pour retrouver l'accès complet.</div>
            </div>
        </div>
        <div class="sub-banner-badge">Expiré</div>
    </div>
    <?php endif; ?>
    <?php endif; ?>

    
    <div class="sub-section-title">Choisissez votre abonnement</div>
    <div class="sub-section-sub">Toutes les fonctionnalités incluses. Aucun frais caché. Conformité fiscale SN intégrée.</div>

    
    <div class="plans-row">
        <?php $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="plan-card <?php echo e($key === 'semestriel' ? 'popular' : ''); ?>">
            <?php if($key === 'semestriel'): ?>
            <div class="plan-card-badge">Le plus populaire</div>
            <?php endif; ?>
            <?php if($p['eco']): ?>
            <div><span class="plan-card-eco"><?php echo e($p['eco']); ?></span></div>
            <?php endif; ?>
            <div class="plan-card-name"><?php echo e($p['label']); ?></div>
            <div class="plan-card-price"><?php echo e($p['total']); ?> <span>FCFA <?php echo e($p['period']); ?></span></div>
            <?php if($p['mensuel'] !== $p['total']): ?>
            <div class="plan-card-mensuel">soit <?php echo e($p['mensuel']); ?> FCFA / mois</div>
            <?php else: ?>
            <div class="plan-card-mensuel">&nbsp;</div>
            <?php endif; ?>
            <?php if($p['saving']): ?>
            <div class="plan-card-saving"><?php echo e($p['saving']); ?></div>
            <?php else: ?>
            <div style="margin-bottom:16px">&nbsp;</div>
            <?php endif; ?>
            <form method="POST" action="<?php echo e(route('subscription.initier')); ?>"
                  onsubmit="return confirm('Confirmer l\'abonnement <?php echo e($p['label']); ?> à <?php echo e($p['total']); ?> FCFA ?')">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="plan" value="<?php echo e($key); ?>">
                <button type="submit" class="plan-card-btn <?php echo e($key === 'semestriel' ? 'plan-card-btn-gold' : 'plan-card-btn-outline'); ?>">
                    Choisir
                </button>
            </form>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    
    <div class="compare-table">
        <table>
            <thead>
                <tr>
                    <th>Durée</th>
                    <th>Total à payer</th>
                    <th>Équivalent / mois</th>
                    <th style="text-align:center">Économie</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($p['label']); ?></td>
                    <td class="<?php echo e($p['eco'] ? 'gold' : ''); ?>"><?php echo e($p['total']); ?> FCFA</td>
                    <td class="<?php echo e($p['eco'] ? 'gold' : ''); ?>"><?php echo e($p['mensuel']); ?> FCFA / mois</td>
                    <td style="text-align:center">
                        <?php if($p['eco']): ?>
                        <span class="eco-pill"><?php echo e($p['eco']); ?></span>
                        <?php else: ?>
                        <span style="color:#484f58;font-size:12px">—</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>

    
    <?php if($historique->count() > 0): ?>
    <div class="hist-card">
        <div class="hist-head">
            <div class="hist-head-title">Historique des paiements</div>
            <span style="background:#21262d;color:#8b949e;font-size:11px;font-weight:600;padding:3px 10px;border-radius:6px"><?php echo e($historique->count()); ?> entrée<?php echo e($historique->count() > 1 ? 's' : ''); ?></span>
        </div>
        <div style="overflow-x:auto">
            <table class="hist-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Plan</th>
                        <th>Méthode</th>
                        <th>Période</th>
                        <th style="text-align:right">Montant</th>
                        <th style="text-align:center">Statut</th>
                        <th>Référence</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $historique; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $paiement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($paiement->created_at->format('d/m/Y')); ?></td>
                        <td><span class="plan-pill"><?php echo e(\App\Models\Subscription::LABELS[$paiement->plan] ?? $paiement->plan); ?></span></td>
                        <td><?php echo e(\App\Models\SubscriptionPayment::METHODE_LABELS[$paiement->methode] ?? $paiement->methode); ?></td>
                        <td style="font-size:11px">
                            <?php if($paiement->periode_debut && $paiement->periode_fin): ?>
                                <?php echo e($paiement->periode_debut->format('d/m/Y')); ?> → <?php echo e($paiement->periode_fin->format('d/m/Y')); ?>

                            <?php else: ?> — <?php endif; ?>
                        </td>
                        <td style="text-align:right;font-family:'Syne',sans-serif;font-weight:700;color:#e6edf3">
                            <?php echo e(number_format($paiement->montant, 0, ',', ' ')); ?> F
                        </td>
                        <td style="text-align:center">
                            <?php $sc = ['payé'=>'paye','en_attente'=>'attente','échoué'=>'echoue']; ?>
                            <span class="hist-badge <?php echo e($sc[$paiement->statut] ?? 'attente'); ?>">
                                <?php echo e(\App\Models\SubscriptionPayment::STATUT_LABELS[$paiement->statut] ?? $paiement->statut); ?>

                            </span>
                        </td>
                        <td style="font-family:monospace;font-size:10px;color:#484f58"><?php echo e($paiement->reference ?? '—'); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    
    <div class="support-card">
        <div class="support-left">
            <div class="support-icon">
                <svg style="width:18px;height:18px;color:#c9a84c" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
            </div>
            <div>
                <div class="support-title">Une question sur votre abonnement ?</div>
                <div class="support-sub">Notre équipe répond sous 2h en jours ouvrables.</div>
            </div>
        </div>
        <div class="support-contacts">
            <div class="support-contact">
                <svg style="width:13px;height:13px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                support@bimotech.sn
            </div>
            <div class="support-contact">
                <svg style="width:13px;height:13px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 9.8a19.79 19.79 0 01-3.07-8.67A2 2 0 012 0h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.09 7.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 14.92z"/></svg>
                +221 33 800 00 01
            </div>
        </div>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ph\bimotech-immo\resources\views/subscription/index.blade.php ENDPATH**/ ?>