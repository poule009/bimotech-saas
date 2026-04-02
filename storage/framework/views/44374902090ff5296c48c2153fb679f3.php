<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo e(config('app.name', 'Bimotech')); ?></title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet"/>

    <style>
        /* ═══════════════════════════════════════════
            VARIABLES PREMIUM "DAKAR CHIC"
        ═══════════════════════════════════════════ */
        :root {
            --agency:       #1a202c; /* Bleu Nuit Profond */
            --agency-accent: #b58c5a; /* Or / Cuivré chaud */
            --agency-soft:  rgba(181, 140, 90, 0.1);
            --sidebar-w:    260px;
            --bg:           #f7f4ec; /* Crème blanchi (effet papier mat) */
            --surface:      rgba(255, 255, 255, 0.7); /* Pour le glassmorphism */
            --border:       rgba(181, 140, 90, 0.15); /* Bordure cuivrée très légère */
            --text:         #1a202c;
            --text-2:       #4a5568;
            --text-3:       #a0aec0;
            --radius:       16px;
            --radius-sm:    10px;
            --shadow:       0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            --shadow-lg:    0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        /* ═══════════════════════════════════════════
            RESET & BASE
        ═══════════════════════════════════════════ */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', -apple-system, sans-serif;
            background: var(--bg);
            color: var(--text);
            -webkit-font-smoothing: antialiased;
            min-height: 100vh;
        }
        a { text-decoration: none; color: inherit; }
        button { cursor: pointer; border: none; background: none; font-family: inherit; }
        svg { display: block; flex-shrink: 0; }

        /* ═══════════════════════════════════════════
            SIDEBAR (NIGHT MODE BY DEFAULT)
        ═══════════════════════════════════════════ */
        .sidebar {
            position: fixed;
            top: 0; left: 0; bottom: 0;
            width: var(--sidebar-w);
            background: #1a202c; /* Sidebar Sombre */
            display: flex;
            flex-direction: column;
            z-index: 100;
            transform: translateX(-100%);
            transition: transform .3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .sidebar.open { transform: translateX(0); box-shadow: var(--shadow-lg); }
        @media (min-width: 1024px) { .sidebar { transform: translateX(0); } }

        /* Logo Premium */
        .sidebar-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            height: 80px;
            padding: 0 24px;
            flex-shrink: 0;
        }
        .logo-icon {
            width: 36px; height: 36px;
            border-radius: var(--radius-sm);
            background: linear-gradient(135deg, var(--agency-accent), #8e6d43);
            color: white;
            font-weight: 800;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 4px 10px rgba(181, 140, 90, 0.3);
        }
        .logo-name {
            font-weight: 700;
            font-size: 16px;
            color: #ffffff;
            letter-spacing: -0.5px;
        }

        /* Nav items (Or sur fond sombre) */
        .sidebar-nav {
            flex: 1;
            padding: 10px 16px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            border-radius: var(--radius-sm);
            font-size: 14px;
            font-weight: 500;
            color: #94a3b8;
            transition: all .2s;
        }
        .nav-item svg { width: 20px; height: 20px; color: #4a5568; transition: color .2s; }
        
        .nav-item:hover {
            background: rgba(255,255,255, 0.05);
            color: #ffffff;
        }
        .nav-item:hover svg { color: var(--agency-accent); }

        .nav-item.active {
            background: rgba(181, 140, 90, 0.15);
            color: var(--agency-accent);
            font-weight: 600;
        }
        .nav-item.active svg { color: var(--agency-accent); }

        .nav-badge {
            margin-left: auto;
            background: var(--agency-accent);
            color: #ffffff;
            font-size: 10px;
            padding: 2px 8px;
            border-radius: 99px;
            font-weight: 700;
        }

        /* Sidebar footer */
        .sidebar-footer {
            padding: 20px 16px;
            background: rgba(0,0,0, 0.2);
        }
        .profile-btn {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px;
            border-radius: var(--radius);
            width: 100%;
            background: rgba(255,255,255, 0.03);
            border: 1px solid rgba(255,255,255, 0.05);
            transition: background .2s;
        }
        .profile-btn:hover { background: rgba(255,255,255, 0.08); }
        .avatar {
            width: 38px; height: 38px;
            border-radius: 12px;
            background: var(--agency-accent);
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; color: #fff;
        }
        .profile-name { font-size: 14px; font-weight: 600; color: #fff; text-align: left; }
        .profile-role { font-size: 11px; color: #64748b; text-transform: uppercase; letter-spacing: 1px; text-align: left; }

        /* ═══════════════════════════════════════════
            CONTENU (GLASSMORPHISM)
        ═══════════════════════════════════════════ */
        .main {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        @media (min-width: 1024px) { .main { margin-left: var(--sidebar-w); } }

        .page-header {
            padding: 24px 32px;
            background: transparent;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .page-title { font-size: 22px; font-weight: 800; color: var(--agency); letter-spacing: -0.8px; }

        .content { flex: 1; padding: 0 32px 32px 32px; }

        /* Composant Card - Glassmorphism */
        .card {
            background: rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: 24px;
            box-shadow: 0 10px 30px -10px rgba(26, 32, 44, 0.05);
            overflow: hidden;
        }

        /* ═══════════════════════════════════════════
            BOUTONS PREMIUM
        ═══════════════════════════════════════════ */
        .btn {
            padding: 12px 24px;
            border-radius: 14px;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-primary {
            background: var(--agency);
            color: white;
            box-shadow: 0 10px 20px -5px rgba(26, 32, 44, 0.3);
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 25px -5px rgba(26, 32, 44, 0.4);
            background: #2d3748;
        }
        .btn-secondary {
            background: #ffffff;
            color: var(--text);
            border: 1px solid var(--border);
        }

        /* Dropdown Custom */
        .dropdown {
            position: absolute;
            bottom: calc(100% + 12px);
            left: 0; right: 0;
            background: #1a202c;
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: var(--radius);
            box-shadow: var(--shadow-lg);
            display: none;
            overflow: hidden;
            z-index: 200;
        }
        .dropdown.open { display: block; animation: slideUp 0.3s ease; }
        @keyframes slideUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .dropdown-item {
            padding: 12px 16px;
            color: #94a3b8;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
        }
        .dropdown-item:hover { background: rgba(255,255,255,0.05); color: #fff; }

        /* Overlay Mobile */
        .overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(26, 32, 44, 0.6);
            backdrop-filter: blur(4px);
            z-index: 90;
        }
        .overlay.open { display: block; }
    </style>

    
    <?php if(isset($currentAgency) && $currentAgency?->couleur_primaire): ?>
    <style>
        :root {
            --agency: <?php echo e($currentAgency->couleur_primaire); ?>;
            --agency-soft: color-mix(in srgb, <?php echo e($currentAgency->couleur_primaire); ?> 10%, white);
        }
    </style>
    <?php endif; ?>

    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
</head>

<body>
    <div class="overlay" id="overlay" onclick="closeSidebar()"></div>

    <aside class="sidebar" id="sidebar">
        <div class="sidebar-logo">
            <?php if(isset($currentAgency)): ?>
                <?php if($currentAgency?->logo_path): ?>
                    <img src="<?php echo e(Storage::url($currentAgency->logo_path)); ?>" alt="<?php echo e($currentAgency->name); ?>" style="height:35px; width:auto; filter: brightness(0) invert(1);">
                <?php else: ?>
                    <div class="logo-icon"><?php echo e(strtoupper(substr($currentAgency->name, 0, 1))); ?></div>
                    <span class="logo-name"><?php echo e($currentAgency->name); ?></span>
                <?php endif; ?>
            <?php else: ?>
                <div class="logo-icon">B</div>
                <span class="logo-name">BimoTech</span>
            <?php endif; ?>
        </div>

        <nav class="sidebar-nav">
            <?php if(auth()->user()->isAdmin()): ?>
                <a href="<?php echo e(route('admin.dashboard')); ?>" class="nav-item <?php echo e(request()->routeIs('admin.dashboard') ? 'active' : ''); ?>">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Dashboard
                </a>
                <a href="<?php echo e(route('admin.paiements.index')); ?>" class="nav-item <?php echo e(request()->routeIs('admin.paiements.*') ? 'active' : ''); ?>">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    Paiements
                </a>
                <a href="<?php echo e(route('admin.contrats.index')); ?>" class="nav-item <?php echo e(request()->routeIs('admin.contrats.*') ? 'active' : ''); ?>">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Contrats
                </a>
                <a href="<?php echo e(route('biens.index')); ?>" class="nav-item <?php echo e(request()->routeIs('biens.*') ? 'active' : ''); ?>">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/></svg>
                    Biens
                </a>
                <a href="<?php echo e(route('admin.users.proprietaires')); ?>" class="nav-item <?php echo e(request()->routeIs('admin.users.proprietaires') ? 'active' : ''); ?>">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Propriétaires
                </a>
                <a href="<?php echo e(route('admin.users.locataires')); ?>" class="nav-item <?php echo e(request()->routeIs('admin.users.locataires') ? 'active' : ''); ?>">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v2h5"/></svg>
                    Locataires
                </a>
                <a href="<?php echo e(route('admin.impayes.index')); ?>" class="nav-item <?php echo e(request()->routeIs('admin.impayes.*') ? 'active' : ''); ?>">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    Impayés
                    <?php if(($impayes_count ?? 0) > 0): ?>
                        <span class="nav-badge"><?php echo e($impayes_count); ?></span>
                    <?php endif; ?>
                </a>
            <?php endif; ?>
        </nav>

        <div class="sidebar-footer">
            <div style="position:relative;">
                <button class="profile-btn" onclick="toggleDropdown()">
                    <div class="avatar"><?php echo e(strtoupper(substr(auth()->user()->name, 0, 1))); ?></div>
                    <div class="profile-info">
                        <div class="profile-name"><?php echo e(auth()->user()->name); ?></div>
                        <div class="profile-role"><?php echo e(auth()->user()->role); ?></div>
                    </div>
                </button>

                <div class="dropdown" id="dropdown">
                    <div style="padding: 16px; border-bottom: 1px solid rgba(255,255,255,0.1)">
                        <p style="color:#64748b; font-size:11px">Email</p>
                        <p style="color:#fff; font-size:13px; font-weight:600"><?php echo e(auth()->user()->email); ?></p>
                    </div>
                    <a href="<?php echo e(route('profile.edit')); ?>" class="dropdown-item">Mon profil</a>
                    <form method="POST" action="<?php echo e(route('logout')); ?>">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="dropdown-item" style="color:#ef4444; width:100%">Déconnexion</button>
                    </form>
                </div>
            </div>
        </div>
    </aside>

    <div class="main">
        <?php if(isset($header)): ?>
            <header class="page-header">
                <h1 class="page-title"><?php echo e($header); ?></h1>
                <div style="display: flex; gap: 12px;">
                    </div>
            </header>
        <?php endif; ?>

        <main class="content">
            <?php echo e($slot); ?>

        </main>
    </div>

    <script>
        function openSidebar() {
            document.getElementById('sidebar').classList.add('open');
            document.getElementById('overlay').classList.add('open');
        }
        function closeSidebar() {
            document.getElementById('sidebar').classList.remove('open');
            document.getElementById('overlay').classList.remove('open');
        }
        function toggleDropdown() {
            document.getElementById('dropdown').classList.toggle('open');
        }
        window.addEventListener('click', function(e) {
            if (!document.getElementById('dropdown').contains(e.target) && !document.querySelector('.profile-btn').contains(e.target)) {
                document.getElementById('dropdown').classList.remove('open');
            }
        });
    </script>
</body>
</html><?php /**PATH C:\Users\ph\bimotech-immo\resources\views/layouts/app.blade.php ENDPATH**/ ?>