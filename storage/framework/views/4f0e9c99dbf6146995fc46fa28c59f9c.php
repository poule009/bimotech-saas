<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Administration — BimoTech</title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <style>
        :root {
            --sa-bg:      #0f172a;
            --sa-surface: #1e293b;
            --sa-border:  #334155;
            --sa-text:    #f1f5f9;
            --sa-text-2:  #94a3b8;
            --sa-accent:  #6366f1;
            --sa-green:   #10b981;
            --sa-amber:   #f59e0b;
            --sa-red:     #ef4444;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { background: var(--sa-bg); color: var(--sa-text); font-family: system-ui, sans-serif; min-height: 100vh; }

        /* ── Header ── */
        .sa-header {
            background: var(--sa-surface);
            border-bottom: 1px solid var(--sa-border);
            padding: 0 24px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 50;
        }
        .sa-logo { display: flex; align-items: center; gap: 10px; }
        .sa-logo-badge {
            width: 32px; height: 32px; border-radius: 8px;
            background: var(--sa-accent);
            display: flex; align-items: center; justify-content: center;
            font-size: 16px;
        }
        .sa-logo-name { font-size: 15px; font-weight: 700; color: var(--sa-text); }
        .sa-logo-sub  { font-size: 11px; color: var(--sa-text-2); }
        .sa-header-right { display: flex; align-items: center; gap: 12px; }
        .sa-user { font-size: 13px; color: var(--sa-text-2); }
        .sa-btn-logout {
            font-size: 12px; padding: 6px 14px;
            background: transparent; border: 1px solid var(--sa-border);
            color: var(--sa-text-2); border-radius: 6px; cursor: pointer;
            transition: all .15s;
        }
        .sa-btn-logout:hover { background: var(--sa-border); color: var(--sa-text); }

        /* ── Nav tabs ── */
        .sa-nav {
            background: var(--sa-surface);
            border-bottom: 1px solid var(--sa-border);
            padding: 0 24px;
            display: flex;
            gap: 4px;
        }
        .sa-nav a {
            font-size: 13px; font-weight: 500;
            padding: 12px 16px;
            color: var(--sa-text-2);
            text-decoration: none;
            border-bottom: 2px solid transparent;
            transition: all .15s;
        }
        .sa-nav a:hover { color: var(--sa-text); }
        .sa-nav a.active { color: var(--sa-accent); border-bottom-color: var(--sa-accent); }

        /* ── Main ── */
        .sa-main { max-width: 1280px; margin: 0 auto; padding: 28px 24px; }

        /* ── KPI grid ── */
        .sa-kpi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 14px;
            margin-bottom: 28px;
        }
        .sa-kpi {
            background: var(--sa-surface);
            border: 1px solid var(--sa-border);
            border-radius: 12px;
            padding: 18px 20px;
        }
        .sa-kpi-label { font-size: 11px; color: var(--sa-text-2); font-weight: 600; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 8px; }
        .sa-kpi-value { font-size: 26px; font-weight: 800; color: var(--sa-text); line-height: 1; }
        .sa-kpi-sub   { font-size: 12px; color: var(--sa-text-2); margin-top: 5px; }
        .sa-kpi.accent .sa-kpi-value { color: var(--sa-accent); }
        .sa-kpi.green  .sa-kpi-value { color: var(--sa-green); }
        .sa-kpi.amber  .sa-kpi-value { color: var(--sa-amber); }
        .sa-kpi.red    .sa-kpi-value { color: var(--sa-red); }

        /* ── Section header ── */
        .sa-section-header {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 16px;
        }
        .sa-section-title { font-size: 15px; font-weight: 700; color: var(--sa-text); }
        .sa-section-sub   { font-size: 12px; color: var(--sa-text-2); margin-top: 2px; }

        /* ── Buttons ── */
        .sa-btn {
            font-size: 12px; font-weight: 600; padding: 8px 16px;
            border-radius: 8px; border: none; cursor: pointer;
            text-decoration: none; display: inline-flex; align-items: center; gap: 6px;
            transition: filter .15s;
        }
        .sa-btn:hover { filter: brightness(.9); }
        .sa-btn-primary { background: var(--sa-accent); color: #fff; }
        .sa-btn-secondary { background: var(--sa-surface); color: var(--sa-text-2); border: 1px solid var(--sa-border); }

        /* ── Table ── */
        .sa-card {
            background: var(--sa-surface);
            border: 1px solid var(--sa-border);
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 24px;
        }
        .sa-table { width: 100%; border-collapse: collapse; font-size: 13px; }
        .sa-table thead th {
            padding: 11px 16px;
            text-align: left;
            font-size: 11px;
            font-weight: 700;
            color: var(--sa-text-2);
            text-transform: uppercase;
            letter-spacing: .5px;
            background: rgba(255,255,255,.03);
            border-bottom: 1px solid var(--sa-border);
        }
        .sa-table tbody tr {
            border-bottom: 1px solid rgba(255,255,255,.05);
            transition: background .1s;
        }
        .sa-table tbody tr:last-child { border-bottom: none; }
        .sa-table tbody tr:hover { background: rgba(255,255,255,.03); }
        .sa-table td { padding: 13px 16px; color: var(--sa-text); vertical-align: middle; }
        .sa-table td.dim { color: var(--sa-text-2); }
        .sa-table td.right { text-align: right; }
        .sa-table td.center { text-align: center; }

        /* ── Badges ── */
        .sa-badge {
            display: inline-flex; align-items: center; gap: 5px;
            font-size: 11px; font-weight: 600; padding: 3px 10px; border-radius: 999px;
        }
        .sa-badge::before { content: ''; width: 6px; height: 6px; border-radius: 50%; background: currentColor; opacity: .7; }
        .sa-badge-green  { background: rgba(16,185,129,.12); color: var(--sa-green); }
        .sa-badge-red    { background: rgba(239,68,68,.12);  color: var(--sa-red); }
        .sa-badge-amber  { background: rgba(245,158,11,.12); color: var(--sa-amber); }
        .sa-badge-indigo { background: rgba(99,102,241,.12); color: var(--sa-accent); }

        /* ── Alert ── */
        .sa-alert-success {
            background: rgba(16,185,129,.1);
            border: 1px solid rgba(16,185,129,.3);
            color: var(--sa-green);
            padding: 12px 16px; border-radius: 8px; font-size: 13px;
            margin-bottom: 20px;
        }

        /* ── Actions ── */
        .sa-action-btn {
            font-size: 11px; padding: 5px 12px; border-radius: 6px;
            border: 1px solid var(--sa-border); background: transparent;
            color: var(--sa-text-2); cursor: pointer; text-decoration: none;
            transition: all .15s; display: inline-block;
        }
        .sa-action-btn:hover { background: var(--sa-border); color: var(--sa-text); }
        .sa-action-btn.danger:hover { background: rgba(239,68,68,.15); border-color: var(--sa-red); color: var(--sa-red); }
        .sa-action-btn.success:hover { background: rgba(16,185,129,.15); border-color: var(--sa-green); color: var(--sa-green); }

        @media (max-width: 768px) {
            .sa-kpi-grid { grid-template-columns: repeat(2, 1fr); }
            .sa-table thead { display: none; }
            .sa-table td { display: block; padding: 8px 16px; border: none; }
            .sa-table tr { border-bottom: 1px solid var(--sa-border); padding: 8px 0; display: block; }
        }
    </style>
</head>
<body>


<header class="sa-header">
    <div class="sa-logo">
        <div class="sa-logo-badge">⚙️</div>
        <div>
            <div class="sa-logo-name">BimoTech</div>
            <div class="sa-logo-sub">Super Administration</div>
        </div>
    </div>
    <div class="sa-header-right">
        <span class="sa-user"><?php echo e(Auth::user()->name); ?></span>
        <form method="POST" action="<?php echo e(route('logout')); ?>">
            <?php echo csrf_field(); ?>
            <button class="sa-btn-logout">Déconnexion</button>
        </form>
    </div>
</header>


<nav class="sa-nav">
    <a href="<?php echo e(route('superadmin.dashboard')); ?>" class="active">🏢 Agences</a>
    <a href="<?php echo e(route('superadmin.subscriptions')); ?>">💳 Abonnements</a>
    <a href="<?php echo e(route('superadmin.activity-logs.index')); ?>">📋 Journal</a>
    <a href="<?php echo e(route('superadmin.agencies.create')); ?>">+ Nouvelle agence</a>
</nav>

<main class="sa-main">

    <?php if(session('success')): ?>
        <div class="sa-alert-success">✅ <?php echo e(session('success')); ?></div>
    <?php endif; ?>

    
    <div class="sa-kpi-grid">
        <div class="sa-kpi">
            <div class="sa-kpi-label">Agences totales</div>
            <div class="sa-kpi-value"><?php echo e($stats['nb_agences']); ?></div>
            <div class="sa-kpi-sub"><?php echo e($stats['nb_agences_actives']); ?> actives</div>
        </div>
        <div class="sa-kpi accent">
            <div class="sa-kpi-label">Abonnements actifs</div>
            <div class="sa-kpi-value"><?php echo e($stats['nb_abonnements_actifs']); ?></div>
            <div class="sa-kpi-sub"><?php echo e($stats['nb_essai']); ?> en essai</div>
        </div>
        <div class="sa-kpi">
            <div class="sa-kpi-label">Utilisateurs</div>
            <div class="sa-kpi-value"><?php echo e($stats['nb_users']); ?></div>
            <div class="sa-kpi-sub">Tous rôles</div>
        </div>
        <div class="sa-kpi">
            <div class="sa-kpi-label">Biens gérés</div>
            <div class="sa-kpi-value"><?php echo e($stats['nb_biens']); ?></div>
            <div class="sa-kpi-sub"><?php echo e($stats['nb_contrats']); ?> contrats actifs</div>
        </div>
        <div class="sa-kpi green">
            <div class="sa-kpi-label">Loyers encaissés</div>
            <div class="sa-kpi-value" style="font-size:18px;"><?php echo e(number_format($stats['total_loyers'], 0, ',', ' ')); ?></div>
            <div class="sa-kpi-sub">FCFA — toutes agences</div>
        </div>
        <div class="sa-kpi accent">
            <div class="sa-kpi-label">Commissions plateforme</div>
            <div class="sa-kpi-value" style="font-size:18px;"><?php echo e(number_format($stats['total_commissions'], 0, ',', ' ')); ?></div>
            <div class="sa-kpi-sub">FCFA TTC cumulés</div>
        </div>
        <div class="sa-kpi amber">
            <div class="sa-kpi-label">Revenus abonnements</div>
            <div class="sa-kpi-value" style="font-size:18px;"><?php echo e(number_format($stats['revenus_abonnements'], 0, ',', ' ')); ?></div>
            <div class="sa-kpi-sub">FCFA encaissés</div>
        </div>
        <div class="sa-kpi red">
            <div class="sa-kpi-label">Abonnements expirés</div>
            <div class="sa-kpi-value"><?php echo e($stats['nb_expires']); ?></div>
            <div class="sa-kpi-sub">À relancer</div>
        </div>
    </div>

    
    <div class="sa-section-header">
        <div>
            <div class="sa-section-title">🏢 Toutes les agences</div>
            <div class="sa-section-sub"><?php echo e($agences->count()); ?> agence(s) enregistrée(s)</div>
        </div>
        <a href="<?php echo e(route('superadmin.agencies.create')); ?>" class="sa-btn sa-btn-primary">
            + Nouvelle agence
        </a>
    </div>

    <div class="sa-card">
        <table class="sa-table">
            <thead>
                <tr>
                    <th>Agence</th>
                    <th class="center">Statut</th>
                    <th class="center">Admins</th>
                    <th class="center">Biens</th>
                    <th class="center">Contrats</th>
                    <th class="right">Loyers (FCFA)</th>
                    <th class="right">Commissions (FCFA)</th>
                    <th class="center">Inscrite le</th>
                    <th class="center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $agences; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agence): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr style="<?php echo e($agence->actif ? '' : 'opacity:.5;'); ?>">
                        <td>
                            <div style="font-weight:700;font-size:13px;"><?php echo e($agence->name); ?></div>
                            <div class="dim" style="font-size:11px;margin-top:2px;"><?php echo e($agence->email); ?></div>
                        </td>
                        <td class="center">
                            <?php if($agence->actif): ?>
                                <span class="sa-badge sa-badge-green">Active</span>
                            <?php else: ?>
                                <span class="sa-badge sa-badge-red">Suspendue</span>
                            <?php endif; ?>
                        </td>
                        <td class="center dim"><?php echo e($agence->nb_admins); ?></td>
                        <td class="center dim"><?php echo e($agence->biens_count); ?></td>
                        <td class="center dim"><?php echo e($agence->contrats_count); ?></td>
                        <td class="right" style="font-weight:600;"><?php echo e(number_format($agence->total_loyers, 0, ',', ' ')); ?></td>
                        <td class="right" style="color:var(--sa-accent);font-weight:700;"><?php echo e(number_format($agence->total_commissions, 0, ',', ' ')); ?></td>
                        <td class="center dim" style="font-size:11px;"><?php echo e($agence->created_at->format('d/m/Y')); ?></td>
                        <td class="center">
                            <div style="display:flex;gap:6px;justify-content:center;flex-wrap:wrap;">
                                <a href="<?php echo e(route('superadmin.agencies.show', $agence)); ?>" class="sa-action-btn">Détail</a>
                                <form method="POST" action="<?php echo e(route('superadmin.agencies.toggle', $agence)); ?>"
                                      onsubmit="return confirm('<?php echo e($agence->actif ? 'Suspendre cette agence ?' : 'Activer cette agence ?'); ?>')">
                                    <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                    <button type="submit" class="sa-action-btn <?php echo e($agence->actif ? 'danger' : 'success'); ?>">
                                        <?php echo e($agence->actif ? 'Suspendre' : 'Activer'); ?>

                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="9" style="text-align:center;padding:48px;color:var(--sa-text-2);">
                            Aucune agence enregistrée.
                            <a href="<?php echo e(route('superadmin.agencies.create')); ?>" style="color:var(--sa-accent);margin-left:8px;">Créer la première →</a>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</main>
</body>
</html><?php /**PATH C:\Users\ph\bimotech-immo\resources\views/superadmin/dashboard.blade.php ENDPATH**/ ?>