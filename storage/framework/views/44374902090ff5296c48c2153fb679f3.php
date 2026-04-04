<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e(config('app.name', 'BimoTech Immo')); ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">

    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>

    
    <?php if(isset($currentAgency) && $currentAgency?->couleur_primaire): ?>
    <style>
        :root {
            --agency: <?php echo e($currentAgency->couleur_primaire); ?>;
            --gold: <?php echo e($currentAgency->couleur_primaire); ?>;
            --gold-lt: color-mix(in srgb, <?php echo e($currentAgency->couleur_primaire); ?> 12%, white);
            --gold-dk: color-mix(in srgb, <?php echo e($currentAgency->couleur_primaire); ?> 70%, black);
        }
    </style>
    <?php endif; ?>

    <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
        --ink:       #0d1117;
        --ink-2:     #1c2333;
        --muted:     #6b7280;
        --subtle:    #9ca3af;
        --border:    #e5e7eb;
        --surface:   #f9fafb;
        --white:     #ffffff;
        --gold:      #c9a84c;
        --gold-lt:   #f5e9c9;
        --gold-dk:   #8a6e2f;
        --green:     #16a34a;
        --green-lt:  #dcfce7;
        --red:       #dc2626;
        --red-lt:    #fee2e2;
        --blue:      #1d4ed8;
        --blue-lt:   #dbeafe;
        --sidebar-w: 248px;
    }

    body {
        font-family: 'DM Sans', sans-serif;
        background: var(--surface);
        color: var(--ink);
        min-height: 100vh;
        display: flex;
        font-size: 14px;
        line-height: 1.6;
        -webkit-font-smoothing: antialiased;
    }

    /* ── SIDEBAR ─────────────────────────────────── */
    .sidebar {
        width: var(--sidebar-w);
        min-height: 100vh;
        background: var(--ink);
        display: flex;
        flex-direction: column;
        position: fixed;
        top: 0; left: 0; bottom: 0;
        z-index: 100;
        transform: translateX(-100%);
        transition: transform .3s cubic-bezier(0.4,0,0.2,1);
    }
    .sidebar.open { transform: translateX(0); box-shadow: 0 20px 60px rgba(0,0,0,0.4); }
    @media (min-width: 1024px) { .sidebar { transform: translateX(0); } }

    .sidebar-logo {
        padding: 24px 20px;
        border-bottom: 1px solid rgba(255,255,255,0.06);
        display: flex; align-items: center; gap: 10px;
    }

    .logo-icon {
        width: 34px; height: 34px;
        background: var(--gold);
        border-radius: 9px;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    .logo-icon img  { height: 28px; width: auto; filter: brightness(0) invert(1); }
    .logo-icon span { font-family: 'Syne', sans-serif; font-weight: 800; font-size: 16px; color: white; }

    .logo-name { font-family: 'Syne', sans-serif; font-weight: 800; font-size: 16px; color: white; letter-spacing: -0.3px; }
    .logo-sub  { font-size: 10px; color: rgba(255,255,255,0.3); letter-spacing: 1.5px; text-transform: uppercase; margin-top: 1px; }

    .sidebar-agency {
        margin: 14px 14px 0;
        padding: 10px 13px;
        background: rgba(255,255,255,0.04);
        border: 1px solid rgba(255,255,255,0.07);
        border-radius: 10px;
        display: flex; align-items: center; gap: 8px;
    }
    .agency-dot  { width: 7px; height: 7px; border-radius: 50%; background: var(--gold); flex-shrink: 0; }
    .agency-name { font-size: 12px; font-weight: 500; color: rgba(255,255,255,0.7); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

    .sidebar-nav { flex: 1; padding: 18px 12px; display: flex; flex-direction: column; gap: 2px; overflow-y: auto; }

    .nav-section {
        font-size: 10px; font-weight: 600; letter-spacing: 1.5px;
        text-transform: uppercase; color: rgba(255,255,255,0.25);
        padding: 12px 12px 5px;
    }

    .nav-item {
        display: flex; align-items: center; gap: 11px;
        padding: 10px 12px; border-radius: 8px;
        color: rgba(255,255,255,0.5);
        text-decoration: none; font-size: 13px; font-weight: 400;
        transition: all 0.15s;
    }
    .nav-item:hover { background: rgba(255,255,255,0.06); color: rgba(255,255,255,0.85); }
    .nav-item.active { background: rgba(201,168,76,0.15); color: var(--gold); font-weight: 500; }
    .nav-item svg { width: 16px; height: 16px; flex-shrink: 0; opacity: 0.7; }
    .nav-item.active svg { opacity: 1; }

    .nav-badge {
        margin-left: auto; background: var(--red); color: white;
        font-size: 10px; font-weight: 700; padding: 2px 7px; border-radius: 99px;
    }

    .sidebar-footer { padding: 14px 12px; border-top: 1px solid rgba(255,255,255,0.06); }

    .profile-btn {
        display: flex; align-items: center; gap: 10px;
        padding: 10px 12px; border-radius: 8px; cursor: pointer;
        transition: background 0.15s; width: 100%; border: none; background: none;
    }
    .profile-btn:hover { background: rgba(255,255,255,0.05); }

    .avatar {
        width: 32px; height: 32px; border-radius: 8px;
        background: linear-gradient(135deg, var(--gold), var(--gold-dk));
        display: flex; align-items: center; justify-content: center;
        font-family: 'Syne', sans-serif; font-weight: 700; font-size: 13px;
        color: white; flex-shrink: 0;
    }

    .profile-name { font-size: 13px; font-weight: 500; color: rgba(255,255,255,0.8); text-align: left; }
    .profile-role { font-size: 10px; color: rgba(255,255,255,0.3); text-transform: uppercase; letter-spacing: 0.8px; text-align: left; }

    /* Dropdown profil */
    .dropdown {
        position: absolute; bottom: 100%; left: 0; right: 0;
        background: #1c2333; border: 1px solid rgba(255,255,255,0.1);
        border-radius: 10px; overflow: hidden;
        opacity: 0; visibility: hidden; transform: translateY(8px);
        transition: all 0.2s; margin-bottom: 6px;
    }
    .dropdown.open { opacity: 1; visibility: visible; transform: translateY(0); }
    .dropdown-item {
        padding: 12px 16px; color: rgba(255,255,255,0.6);
        display: flex; align-items: center; gap: 10px; font-size: 13px;
        text-decoration: none; width: 100%; background: none; border: none;
        cursor: pointer; font-family: 'DM Sans', sans-serif;
    }
    .dropdown-item:hover { background: rgba(255,255,255,0.05); color: white; }

    /* ── MAIN ─────────────────────────────────────── */
    .main {
        margin-left: var(--sidebar-w);
        flex: 1; display: flex; flex-direction: column; min-height: 100vh;
    }
    @media (max-width: 1023px) { .main { margin-left: 0; } }

    /* ── TOPBAR ───────────────────────────────────── */
    .topbar {
        height: 60px; background: var(--white);
        border-bottom: 1px solid var(--border);
        display: flex; align-items: center; justify-content: space-between;
        padding: 0 32px; position: sticky; top: 0; z-index: 30;
    }

    .breadcrumb { display: flex; align-items: center; gap: 6px; font-size: 13px; color: var(--muted); }
    .breadcrumb-current { color: var(--ink); font-weight: 500; }

    .topbar-right { display: flex; align-items: center; gap: 12px; }
    .top-date { font-size: 12px; color: var(--subtle); }

    .top-notif {
        width: 34px; height: 34px; border-radius: 8px;
        border: 1px solid var(--border);
        display: flex; align-items: center; justify-content: center;
        cursor: pointer; position: relative; transition: background 0.15s;
    }
    .top-notif:hover { background: var(--surface); }
    .top-notif svg { width: 16px; height: 16px; color: var(--muted); }
    .notif-dot {
        width: 7px; height: 7px; background: var(--red); border-radius: 50%;
        position: absolute; top: 7px; right: 8px; border: 1.5px solid white;
    }

    .btn-primary {
        display: flex; align-items: center; gap: 6px;
        padding: 8px 16px; background: var(--ink); color: white;
        border: none; border-radius: 8px; font-size: 12px; font-weight: 500;
        font-family: 'DM Sans', sans-serif; cursor: pointer;
        transition: opacity 0.15s; text-decoration: none;
    }
    .btn-primary:hover { opacity: 0.85; }
    .btn-primary svg { width: 14px; height: 14px; }

    .btn-outline {
        display: flex; align-items: center; gap: 6px;
        padding: 8px 16px; background: white; color: var(--ink);
        border: 1px solid var(--border); border-radius: 8px;
        font-size: 12px; font-weight: 500; font-family: 'DM Sans', sans-serif;
        cursor: pointer; text-decoration: none;
    }

    /* ── CONTENT ──────────────────────────────────── */
    .content { flex: 1; }

    /* ── OVERLAY MOBILE ───────────────────────────── */
    .overlay {
        display: none; position: fixed; inset: 0;
        background: rgba(13,17,23,0.7); backdrop-filter: blur(4px); z-index: 90;
    }
    .overlay.open { display: block; }

    /* ── ABONNEMENT BADGE ─────────────────────────── */
    .sub-warning {
        margin: 12px 12px 0; padding: 10px 12px;
        background: rgba(251,191,36,0.1); border: 1px solid rgba(251,191,36,0.3);
        border-radius: 8px; font-size: 11px; color: #d97706;
        display: flex; align-items: center; gap: 8px;
    }
    .sub-warning a { color: var(--gold); font-weight: 600; text-decoration: none; margin-left: auto; }
    </style>
</head>

<body>
    <div class="overlay" id="overlay" onclick="closeSidebar()"></div>

    <aside class="sidebar" id="sidebar">
        
        <div class="sidebar-logo">
            <?php if(isset($currentAgency) && $currentAgency?->logo_path): ?>
                <div class="logo-icon">
                    <img src="<?php echo e(Storage::url($currentAgency->logo_path)); ?>" alt="<?php echo e($currentAgency->name); ?>">
                </div>
                <div>
                    <div class="logo-name"><?php echo e($currentAgency->name); ?></div>
                    <div class="logo-sub">Immo</div>
                </div>
            <?php else: ?>
                <div class="logo-icon">
                    <span><?php echo e(strtoupper(substr($currentAgency->name ?? 'B', 0, 1))); ?></span>
                </div>
                <div>
                    <div class="logo-name"><?php echo e($currentAgency->name ?? 'BimoTech'); ?></div>
                    <div class="logo-sub">Gestion Immo</div>
                </div>
            <?php endif; ?>
        </div>

        
        <?php if(isset($currentAgency)): ?>
        <div class="sidebar-agency">
            <div class="agency-dot"></div>
            <div class="agency-name"><?php echo e($currentAgency->name); ?></div>
        </div>
        <?php endif; ?>

        
        <?php if(isset($currentAgency) && $currentAgency->subscription && !$currentAgency->subscription->aAcces()): ?>
        <div class="sub-warning">
            <svg style="width:14px;height:14px;flex-shrink:0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            Abonnement expiré
            <a href="<?php echo e(route('subscription.index')); ?>">Renouveler →</a>
        </div>
        <?php endif; ?>

        
        <nav class="sidebar-nav">

            <?php if(auth()->user()->isAdmin()): ?>
                <div class="nav-section">Principal</div>

                <a href="<?php echo e(route('admin.dashboard')); ?>" class="nav-item <?php echo e(request()->routeIs('admin.dashboard') ? 'active' : ''); ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                    Tableau de bord
                </a>

                <a href="<?php echo e(route('admin.paiements.index')); ?>" class="nav-item <?php echo e(request()->routeIs('admin.paiements.*') ? 'active' : ''); ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
                    Paiements
                </a>

                <a href="<?php echo e(route('admin.contrats.index')); ?>" class="nav-item <?php echo e(request()->routeIs('admin.contrats.*') ? 'active' : ''); ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                    Contrats
                </a>

                <a href="<?php echo e(route('biens.index')); ?>" class="nav-item <?php echo e(request()->routeIs('biens.*') ? 'active' : ''); ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    Biens
                </a>

                <a href="<?php echo e(route('admin.impayes.index')); ?>" class="nav-item <?php echo e(request()->routeIs('admin.impayes.*') ? 'active' : ''); ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    Impayés
                    <?php if(($impayes_count ?? 0) > 0): ?>
                        <span class="nav-badge"><?php echo e($impayes_count); ?></span>
                    <?php endif; ?>
                </a>

                <div class="nav-section">Gestion</div>

                <a href="<?php echo e(route('admin.users.proprietaires')); ?>" class="nav-item <?php echo e(request()->routeIs('admin.users.proprietaires') ? 'active' : ''); ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    Propriétaires
                </a>

                <a href="<?php echo e(route('admin.users.locataires')); ?>" class="nav-item <?php echo e(request()->routeIs('admin.users.locataires') ? 'active' : ''); ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
                    Locataires
                </a>

                <a href="<?php echo e(route('admin.rapports.financier')); ?>" class="nav-item <?php echo e(request()->routeIs('admin.rapports.*') ? 'active' : ''); ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                    Rapports
                </a>

                <a href="<?php echo e(route('admin.agency.settings')); ?>" class="nav-item <?php echo e(request()->routeIs('admin.agency.*') ? 'active' : ''); ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 010 14.14M4.93 4.93a10 10 0 000 14.14"/></svg>
                    Paramètres
                </a>

            <?php elseif(auth()->user()->isProprietaire()): ?>
                <a href="<?php echo e(route('proprietaire.dashboard')); ?>" class="nav-item <?php echo e(request()->routeIs('proprietaire.dashboard') ? 'active' : ''); ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                    Mon tableau de bord
                </a>
                <a href="<?php echo e(route('proprietaire.paiements.pdf', '')); ?>" class="nav-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/></svg>
                    Mes quittances
                </a>

            <?php elseif(auth()->user()->isLocataire()): ?>
                <a href="<?php echo e(route('locataire.dashboard')); ?>" class="nav-item <?php echo e(request()->routeIs('locataire.dashboard') ? 'active' : ''); ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                    Mon espace
                </a>
                <a href="<?php echo e(route('locataire.paiements')); ?>" class="nav-item <?php echo e(request()->routeIs('locataire.paiements') ? 'active' : ''); ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
                    Mes paiements
                </a>
            <?php endif; ?>

        </nav>

        
        <div class="sidebar-footer">
            <div style="position:relative">
                <button class="profile-btn" onclick="toggleDropdown()">
                    <div class="avatar"><?php echo e(strtoupper(substr(auth()->user()->name, 0, 1))); ?></div>
                    <div>
                        <div class="profile-name"><?php echo e(auth()->user()->name); ?></div>
                        <div class="profile-role"><?php echo e(auth()->user()->role); ?></div>
                    </div>
                </button>

                <div class="dropdown" id="dropdown">
                    <div style="padding:14px 16px;border-bottom:1px solid rgba(255,255,255,0.08)">
                        <div style="font-size:11px;color:rgba(255,255,255,0.3)">Connecté en tant que</div>
                        <div style="font-size:13px;font-weight:500;color:rgba(255,255,255,0.8);margin-top:2px"><?php echo e(auth()->user()->email); ?></div>
                    </div>
                    <a href="<?php echo e(route('profile.edit')); ?>" class="dropdown-item">
                        <svg style="width:14px;height:14px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        Mon profil
                    </a>
                    <form method="POST" action="<?php echo e(route('logout')); ?>">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="dropdown-item" style="color:#f87171">
                            <svg style="width:14px;height:14px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                            Déconnexion
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </aside>

    
    <div class="main">

        
        <header class="topbar">
            
            <button onclick="openSidebar()" style="display:none;background:none;border:none;cursor:pointer;margin-right:12px" id="hamburger">
                <svg style="width:20px;height:20px;color:var(--muted)" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
            </button>

            <div class="breadcrumb">
                <span>BimoTech</span>
                <svg style="width:12px;height:12px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                <?php if(isset($header)): ?>
                    <span class="breadcrumb-current"><?php echo e($header); ?></span>
                <?php endif; ?>
            </div>

            <div class="topbar-right">
                <span class="top-date"><?php echo e(now()->translatedFormat('l d F Y')); ?></span>
                <div class="top-notif">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 01-3.46 0"/></svg>
                    <?php if(($impayes_count ?? 0) > 0): ?>
                        <div class="notif-dot"></div>
                    <?php endif; ?>
                </div>
                <?php if(auth()->user()->isAdmin()): ?>
                    <a href="<?php echo e(route('admin.paiements.create')); ?>" class="btn-primary">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        Nouveau paiement
                    </a>
                <?php endif; ?>
            </div>
        </header>

        
        <?php if(session('success')): ?>
            <div style="margin:16px 32px 0;padding:12px 18px;background:#dcfce7;border:1px solid #bbf7d0;border-radius:10px;color:#16a34a;font-size:13px;font-weight:500;display:flex;align-items:center;gap:8px">
                <svg style="width:16px;height:16px;flex-shrink:0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                <?php echo e(session('success')); ?>

            </div>
        <?php endif; ?>

        <?php if(session('error') || session('warning')): ?>
            <div style="margin:16px 32px 0;padding:12px 18px;background:#fee2e2;border:1px solid #fecaca;border-radius:10px;color:#dc2626;font-size:13px;font-weight:500;display:flex;align-items:center;gap:8px">
                <svg style="width:16px;height:16px;flex-shrink:0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <?php echo e(session('error') ?? session('warning')); ?>

            </div>
        <?php endif; ?>

        
        <main class="content">
            <?php echo e($slot); ?>

        </main>
    </div>

    <script>
    function openSidebar()  { document.getElementById('sidebar').classList.add('open'); document.getElementById('overlay').classList.add('open'); }
    function closeSidebar() { document.getElementById('sidebar').classList.remove('open'); document.getElementById('overlay').classList.remove('open'); }
    function toggleDropdown() { document.getElementById('dropdown').classList.toggle('open'); }

    window.addEventListener('click', function(e) {
        const dd = document.getElementById('dropdown');
        const pb = document.querySelector('.profile-btn');
        if (dd && pb && !dd.contains(e.target) && !pb.contains(e.target)) {
            dd.classList.remove('open');
        }
    });

    // Afficher hamburger sur mobile
    if (window.innerWidth < 1024) {
        const h = document.getElementById('hamburger');
        if (h) h.style.display = 'block';
    }
    </script>
</body>
</html><?php /**PATH C:\Users\ph\bimotech-immo\resources\views/layouts/app.blade.php ENDPATH**/ ?>