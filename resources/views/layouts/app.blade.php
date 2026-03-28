<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Bimotech') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet"/>

    <style>
        /* ═══════════════════════════════════════════
           VARIABLES
        ═══════════════════════════════════════════ */
        :root {
            --agency:       #1a3c5e;
            --agency-soft:  #eef3f8;
            --sidebar-w:    250px;
            --bg:           #f8fafc;
            --surface:      #ffffff;
            --border:       #e8edf2;
            --text:         #0f172a;
            --text-2:       #64748b;
            --text-3:       #94a3b8;
            --radius:       12px;
            --radius-sm:    8px;
            --shadow:       0 1px 3px rgba(0,0,0,.07), 0 1px 2px rgba(0,0,0,.04);
            --shadow-lg:    0 10px 25px rgba(0,0,0,.1), 0 4px 10px rgba(0,0,0,.05);
        }

        @if (isset($currentAgency) && $currentAgency?->couleur_primaire)
            :root {
                --agency: {{ $currentAgency->couleur_primaire }};
                --agency-soft: color-mix(in srgb, {{ $currentAgency->couleur_primaire }} 10%, white);
            }
        @endif

        /* ═══════════════════════════════════════════
           RESET
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
           LAYOUT
        ═══════════════════════════════════════════ */
        .layout { display: flex; min-height: 100vh; }

        /* ═══════════════════════════════════════════
           SIDEBAR
        ═══════════════════════════════════════════ */
        .sidebar {
            position: fixed;
            top: 0; left: 0; bottom: 0;
            width: var(--sidebar-w);
            background: var(--surface);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            z-index: 100;
            transform: translateX(-100%);
            transition: transform .25s cubic-bezier(.4,0,.2,1), box-shadow .25s;
        }
        .sidebar.open {
            transform: translateX(0);
            box-shadow: var(--shadow-lg);
        }
        @media (min-width: 1024px) {
            .sidebar { transform: translateX(0); box-shadow: none; }
        }

        /* Logo */
        .sidebar-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            height: 60px;
            padding: 0 16px;
            border-bottom: 1px solid var(--border);
            flex-shrink: 0;
        }
        .logo-icon {
            width: 32px; height: 32px;
            border-radius: var(--radius-sm);
            background: var(--agency);
            color: white;
            font-weight: 700;
            font-size: 14px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .logo-name {
            font-weight: 700;
            font-size: 14px;
            color: var(--text);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Nav items */
        .sidebar-nav {
            flex: 1;
            padding: 10px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 2px;
        }
        .nav-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 12px;
            border-radius: var(--radius);
            font-size: 13.5px;
            font-weight: 500;
            color: var(--text-2);
            transition: all .15s;
        }
        .nav-item:hover {
            background: var(--bg);
            color: var(--text);
        }
        .nav-item.active {
            background: var(--agency-soft);
            color: var(--agency);
            font-weight: 600;
        }
        .nav-item svg { width: 17px; height: 17px; color: var(--text-3); transition: color .15s; }
        .nav-item:hover svg, .nav-item.active svg { color: var(--agency); }
        .nav-badge {
            margin-left: auto;
            background: #fef2f2;
            color: #ef4444;
            font-size: 10px;
            font-weight: 700;
            padding: 2px 6px;
            border-radius: 6px;
        }

        /* Sidebar footer */
        .sidebar-footer {
            flex-shrink: 0;
            padding: 10px;
            border-top: 1px solid var(--border);
        }
        .trial-badge {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 9px 12px;
            background: #fffbeb;
            border: 1px solid #fde68a;
            border-radius: var(--radius);
            font-size: 12px;
            color: #92400e;
            margin-bottom: 8px;
        }
        .trial-badge a {
            margin-left: auto;
            font-weight: 600;
            color: #b45309;
        }
        .profile-btn {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 12px;
            border-radius: var(--radius);
            width: 100%;
            transition: background .15s;
        }
        .profile-btn:hover { background: var(--bg); }
        .avatar {
            width: 32px; height: 32px;
            border-radius: 50%;
            background: #e2e8f0;
            display: flex; align-items: center; justify-content: center;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-2);
            flex-shrink: 0;
        }
        .profile-info { flex: 1; min-width: 0; text-align: left; }
        .profile-name { font-size: 13px; font-weight: 600; color: var(--text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .profile-role { font-size: 11px; color: var(--text-3); text-transform: capitalize; }

        /* Dropdown profil */
        .dropdown {
            position: absolute;
            bottom: calc(100% + 4px);
            left: 0; right: 0;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow-lg);
            overflow: hidden;
            display: none;
            z-index: 200;
            animation: dropUp .15s ease;
        }
        .dropdown.open { display: block; }
        @keyframes dropUp {
            from { opacity: 0; transform: translateY(4px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .dropdown-header {
            padding: 12px 16px;
            border-bottom: 1px solid var(--border);
        }
        .dropdown-header p:first-child { font-size: 11px; color: var(--text-3); }
        .dropdown-header p:last-child  { font-size: 13px; font-weight: 600; color: var(--text); margin-top: 2px; }
        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 11px 16px;
            font-size: 13px;
            color: var(--text-2);
            border-top: 1px solid var(--border);
            transition: background .12s;
            width: 100%;
            text-align: left;
        }
        .dropdown-item:hover { background: var(--bg); color: var(--text); }
        .dropdown-item svg { width: 15px; height: 15px; color: var(--text-3); }
        .dropdown-item.danger { color: #ef4444; }
        .dropdown-item.danger:hover { background: #fef2f2; }
        .dropdown-item.danger svg { color: #ef4444; }

        /* ═══════════════════════════════════════════
           OVERLAY MOBILE
        ═══════════════════════════════════════════ */
        .overlay {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 90;
            background: rgba(15,23,42,.5);
            backdrop-filter: blur(4px);
        }
        .overlay.open { display: block; animation: fadeIn .2s ease; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

        /* ═══════════════════════════════════════════
           CONTENU PRINCIPAL
        ═══════════════════════════════════════════ */
        .main {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            padding-bottom: 72px;
        }
        @media (min-width: 1024px) {
            .main { margin-left: var(--sidebar-w); padding-bottom: 0; }
        }

        /* Topbar mobile */
        .topbar {
            position: sticky;
            top: 0;
            z-index: 80;
            height: 54px;
            display: flex;
            align-items: center;
            padding: 0 16px;
            gap: 12px;
            background: rgba(255,255,255,.88);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border);
        }
        @media (min-width: 1024px) { .topbar { display: none; } }
        .topbar-btn {
            padding: 7px;
            border-radius: var(--radius-sm);
            color: var(--text-2);
            display: flex;
            transition: background .15s;
        }
        .topbar-btn:hover { background: var(--bg); }
        .topbar-btn svg { width: 20px; height: 20px; }

        /* Page header */
        .page-header {
            display: none;
            padding: 18px 28px;
            background: var(--surface);
            border-bottom: 1px solid var(--border);
        }
        @media (min-width: 1024px) { .page-header { display: block; } }
        .page-title { font-size: 17px; font-weight: 700; color: var(--text); letter-spacing: -.3px; }

        /* Contenu */
        .content { flex: 1; padding: 20px 16px; }
        @media (min-width: 1024px) { .content { padding: 28px 32px; } }

        /* ═══════════════════════════════════════════
           TAB BAR MOBILE
        ═══════════════════════════════════════════ */
        .tabbar {
            position: fixed;
            bottom: 0; left: 0; right: 0;
            z-index: 80;
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: space-around;
            padding: 0 8px;
            background: rgba(255,255,255,.92);
            backdrop-filter: blur(12px);
            border-top: 1px solid var(--border);
        }
        @media (min-width: 1024px) { .tabbar { display: none; } }
        .tab-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 3px;
            padding: 6px 10px;
            color: var(--text-3);
            font-size: 10px;
            font-weight: 500;
            border-radius: var(--radius-sm);
            transition: color .15s;
            min-width: 44px;
        }
        .tab-item svg { width: 22px; height: 22px; }
        .tab-item.active { color: var(--agency); }

        /* ═══════════════════════════════════════════
           COMPOSANTS UI
        ═══════════════════════════════════════════ */

        /* Carte */
        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
        }
        .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 20px;
            border-bottom: 1px solid var(--border);
        }
        .card-title { font-size: 14px; font-weight: 600; color: var(--text); }
        .card-body  { padding: 20px; }

        /* KPI */
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }
        @media (min-width: 640px)  { .kpi-grid { grid-template-columns: repeat(2, 1fr); } }
        @media (min-width: 1024px) { .kpi-grid { grid-template-columns: repeat(4, 1fr); } }

        .kpi {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 18px;
            box-shadow: var(--shadow);
            transition: box-shadow .2s, transform .2s;
        }
        .kpi:hover { box-shadow: var(--shadow-lg); transform: translateY(-1px); }
        .kpi-label { font-size: 11px; font-weight: 500; color: var(--text-3); text-transform: uppercase; letter-spacing: .5px; margin-bottom: 8px; }
        .kpi-value { font-size: 22px; font-weight: 800; color: var(--text); letter-spacing: -.5px; line-height: 1; }
        .kpi-sub   { font-size: 11px; color: var(--text-3); margin-top: 6px; }
        .kpi-icon  { width: 34px; height: 34px; border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; margin-bottom: 10px; font-size: 18px; }

        /* Badge */
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 3px 9px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 600;
            white-space: nowrap;
        }
        .badge-green  { background: #f0fdf4; color: #16a34a; }
        .badge-blue   { background: #eff6ff; color: #2563eb; }
        .badge-amber  { background: #fffbeb; color: #d97706; }
        .badge-red    { background: #fef2f2; color: #dc2626; }
        .badge-gray   { background: #f8fafc; color: #64748b; }
        .badge-purple { background: #faf5ff; color: #7c3aed; }

        /* Boutons */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: var(--radius);
            font-size: 13px;
            font-weight: 600;
            transition: all .15s;
            white-space: nowrap;
        }
        .btn svg { width: 15px; height: 15px; }
        .btn-primary { background: var(--agency); color: white; }
        .btn-primary:hover { filter: brightness(.9); color: white; }
        .btn-secondary { background: var(--bg); color: var(--text-2); border: 1px solid var(--border); }
        .btn-secondary:hover { background: var(--border); color: var(--text); }
        .btn-danger { background: #fef2f2; color: #dc2626; }
        .btn-danger:hover { background: #fee2e2; }
        .btn-sm { padding: 6px 12px; font-size: 12px; }

        /* Table */
        .table-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; }
        table { width: 100%; border-collapse: collapse; font-size: 13px; }
        thead th {
            padding: 10px 16px;
            text-align: left;
            font-size: 10.5px;
            font-weight: 600;
            color: var(--text-3);
            text-transform: uppercase;
            letter-spacing: .5px;
            background: var(--bg);
            border-bottom: 1px solid var(--border);
            white-space: nowrap;
        }
        tbody tr { border-bottom: 1px solid var(--border); transition: background .1s; }
        tbody tr:last-child { border-bottom: none; }
        tbody tr:hover { background: var(--bg); }
        tbody td { padding: 13px 16px; color: var(--text); vertical-align: middle; }

        /* Input */
        .input {
            width: 100%;
            padding: 9px 13px;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            font-size: 13px;
            color: var(--text);
            background: var(--surface);
            transition: border-color .15s, box-shadow .15s;
            outline: none;
            font-family: inherit;
        }
        .input:focus {
            border-color: var(--agency);
            box-shadow: 0 0 0 3px color-mix(in srgb, var(--agency) 15%, transparent);
        }
        label { display: block; font-size: 12px; font-weight: 600; color: var(--text-2); margin-bottom: 5px; }

        /* Alerte */
        .alert { display: flex; align-items: flex-start; gap: 10px; padding: 12px 16px; border-radius: var(--radius); font-size: 13px; margin-bottom: 16px; }
        .alert-success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #15803d; }
        .alert-warning { background: #fffbeb; border: 1px solid #fde68a; color: #92400e; }
        .alert-error   { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }
        .alert-info    { background: #eff6ff; border: 1px solid #bfdbfe; color: #1d4ed8; }

        /* Progress */
        .progress { height: 5px; background: var(--border); border-radius: 999px; overflow: hidden; margin-top: 8px; }
        .progress-bar { height: 100%; background: var(--agency); border-radius: 999px; transition: width .5s ease; }

        /* Mobile cards (remplacent les tableaux sur mobile) */
        .mobile-cards { display: flex; flex-direction: column; gap: 10px; }
        @media (min-width: 768px) { .mobile-cards { display: none; } }
        .desktop-table { display: none; }
        @media (min-width: 768px) { .desktop-table { display: block; } }
        .mobile-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 14px 16px;
            box-shadow: var(--shadow);
        }
        .mobile-card-row { display: flex; justify-content: space-between; align-items: center; padding: 3px 0; }
        .mobile-card-label { font-size: 11px; color: var(--text-3); font-weight: 500; }
        .mobile-card-value { font-size: 13px; color: var(--text); font-weight: 500; text-align: right; }

        /* Utilitaires */
        .flex-between { display: flex; align-items: center; justify-content: space-between; }
        .text-money { font-variant-numeric: tabular-nums; font-weight: 700; letter-spacing: -.3px; }
        .text-ref { font-family: 'SF Mono', 'Fira Code', monospace; font-size: 11px; color: var(--text-3); }
        .link { color: var(--agency); font-size: 13px; font-weight: 500; }
        .link:hover { text-decoration: underline; }
        .section-gap { margin-bottom: 20px; }
        @media (min-width: 1024px) { .section-gap { margin-bottom: 28px; } }
    </style>
</head>

<body>
    <div class="overlay" id="overlay" onclick="closeSidebar()"></div>

    {{-- ══════════════════════════════════════ --}}
    {{-- SIDEBAR                                --}}
    {{-- ══════════════════════════════════════ --}}
    <aside class="sidebar" id="sidebar">

        {{-- Logo --}}
        <div class="sidebar-logo">
            @if (isset($currentAgency))
                @if ($currentAgency?->logo_path)
                    <img src="{{ Storage::url($currentAgency->logo_path) }}"
                         alt="{{ $currentAgency->name }}"
                         style="height:30px;width:auto;object-fit:contain;">
                @else
                    <div class="logo-icon">{{ strtoupper(substr($currentAgency->name, 0, 1)) }}</div>
                    <span class="logo-name">{{ $currentAgency->name }}</span>
                @endif
            @else
                <span style="font-weight:800;font-size:17px;letter-spacing:-.4px;">Bimotech</span>
            @endif
        </div>

        {{-- Navigation --}}
        <nav class="sidebar-nav">
            @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.dashboard') }}"
                   class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Dashboard
                </a>
                <a href="{{ route('admin.paiements.index') }}"
                   class="nav-item {{ request()->routeIs('admin.paiements.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    Paiements
                </a>
                <a href="{{ route('admin.contrats.index') }}"
                   class="nav-item {{ request()->routeIs('admin.contrats.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Contrats
                </a>
                <a href="{{ route('biens.index') }}"
                   class="nav-item {{ request()->routeIs('biens.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 00-1-1h-2a1 1 0 00-1 1v5m4 0H9"/></svg>
                    Biens
                </a>
                <a href="{{ route('admin.users.proprietaires') }}"
                   class="nav-item {{ request()->routeIs('admin.users.proprietaires') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Propriétaires
                </a>
                <a href="{{ route('admin.users.locataires') }}"
                   class="nav-item {{ request()->routeIs('admin.users.locataires') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v2h5m-2 0v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Locataires
                </a>
                <a href="{{ route('admin.rapports.financier') }}"
                   class="nav-item {{ request()->routeIs('admin.rapports.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    Rapport
                </a>
                <a href="{{ route('admin.impayes.index') }}"
                   class="nav-item {{ request()->routeIs('admin.impayes.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    Impayés
                    <span class="nav-badge">!</span>
                </a>

            @elseif(auth()->user()->isProprietaire())
                <a href="{{ route('proprietaire.dashboard') }}"
                   class="nav-item {{ request()->routeIs('proprietaire.dashboard') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Dashboard
                </a>
                <a href="{{ route('biens.index') }}"
                   class="nav-item {{ request()->routeIs('biens.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/></svg>
                    Mes Biens
                </a>

            @elseif(auth()->user()->isLocataire())
                <a href="{{ route('locataire.dashboard') }}"
                   class="nav-item {{ request()->routeIs('locataire.dashboard') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Dashboard
                </a>
                <a href="{{ route('locataire.paiements') }}"
                   class="nav-item {{ request()->routeIs('locataire.paiements') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Mes Reçus
                </a>
            @endif
        </nav>

        {{-- Footer --}}
        <div class="sidebar-footer">
            @if(isset($currentAgency) && $currentAgency?->subscription?->estEnEssai())
                <div class="trial-badge">
                    <span>🕐</span>
                    <span>Essai — {{ $currentAgency->subscription->joursRestantsEssai() }}j</span>
                    <a href="{{ route('subscription.index') }}">S'abonner</a>
                </div>
            @endif

            <div style="position:relative;">
                <button class="profile-btn" onclick="toggleDropdown()">
                    <div class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                    <div class="profile-info">
                        <div class="profile-name">{{ auth()->user()->name }}</div>
                        <div class="profile-role">{{ auth()->user()->role }}</div>
                    </div>
                    <svg style="width:13px;height:13px;color:var(--text-3);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div class="dropdown" id="dropdown">
                    <div class="dropdown-header">
                        <p>Connecté en tant que</p>
                        <p>{{ auth()->user()->email }}</p>
                    </div>
                    <a href="{{ route('profile.edit') }}" class="dropdown-item">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        Mon profil
                    </a>
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.agency.settings') }}" class="dropdown-item">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            Paramètres agence
                        </a>
                        <a href="{{ route('subscription.index') }}" class="dropdown-item">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                            Abonnement
                        </a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item danger">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            Déconnexion
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </aside>

    {{-- ══════════════════════════════════════ --}}
    {{-- CONTENU PRINCIPAL                      --}}
    {{-- ══════════════════════════════════════ --}}
    <div class="main">

        {{-- Topbar mobile --}}
        <header class="topbar">
            <button class="topbar-btn" onclick="openSidebar()">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            @if (isset($currentAgency) && $currentAgency?->logo_path)
                <img src="{{ Storage::url($currentAgency->logo_path) }}"
                     style="height:26px;width:auto;object-fit:contain;" alt="">
            @else
                <span style="font-weight:700;font-size:14px;">{{ $currentAgency?->name ?? 'Bimotech' }}</span>
            @endif
            <div style="margin-left:auto;">
                <div class="avatar" style="width:30px;height:30px;font-size:12px;">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
            </div>
        </header>

        {{-- Header page --}}
        @isset($header)
            <div class="page-header">
                <div class="page-title">{{ $header }}</div>
            </div>
        @endisset

        {{-- Contenu --}}
        <main class="content">
            {{ $slot }}
        </main>
    </div>

    {{-- ══════════════════════════════════════ --}}
    {{-- TAB BAR MOBILE                         --}}
    {{-- ══════════════════════════════════════ --}}
    <nav class="tabbar">
        @if(auth()->user()->isAdmin())
            <a href="{{ route('admin.dashboard') }}" class="tab-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span>Accueil</span>
            </a>
            <a href="{{ route('biens.index') }}" class="tab-item {{ request()->routeIs('biens.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 00-1-1h-2a1 1 0 00-1 1v5m4 0H9"/></svg>
                <span>Biens</span>
            </a>
            <a href="{{ route('admin.paiements.index') }}" class="tab-item {{ request()->routeIs('admin.paiements.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                <span>Paiements</span>
            </a>
            <a href="{{ route('admin.contrats.index') }}" class="tab-item {{ request()->routeIs('admin.contrats.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Contrats</span>
            </a>
            <a href="{{ route('admin.impayes.index') }}" class="tab-item {{ request()->routeIs('admin.impayes.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span>Impayés</span>
            </a>

        @elseif(auth()->user()->isProprietaire())
            <a href="{{ route('proprietaire.dashboard') }}" class="tab-item {{ request()->routeIs('proprietaire.dashboard') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span>Accueil</span>
            </a>
            <a href="{{ route('biens.index') }}" class="tab-item {{ request()->routeIs('biens.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/></svg>
                <span>Mes Biens</span>
            </a>

        @elseif(auth()->user()->isLocataire())
            <a href="{{ route('locataire.dashboard') }}" class="tab-item {{ request()->routeIs('locataire.dashboard') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span>Accueil</span>
            </a>
            <a href="{{ route('locataire.paiements') }}" class="tab-item {{ request()->routeIs('locataire.paiements') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Mes Reçus</span>
            </a>
        @endif
    </nav>

    <script>
        function openSidebar() {
            document.getElementById('sidebar').classList.add('open');
            document.getElementById('overlay').classList.add('open');
            document.body.style.overflow = 'hidden';
        }
        function closeSidebar() {
            document.getElementById('sidebar').classList.remove('open');
            document.getElementById('overlay').classList.remove('open');
            document.body.style.overflow = '';
        }
        function toggleDropdown() {
            document.getElementById('dropdown').classList.toggle('open');
        }
        document.addEventListener('click', function(e) {
            const d = document.getElementById('dropdown');
            const b = document.querySelector('.profile-btn');
            if (d && b && !d.contains(e.target) && !b.contains(e.target)) {
                d.classList.remove('open');
            }
        });
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeSidebar();
                document.getElementById('dropdown')?.classList.remove('open');
            }
        });
    </script>
</body>
</html>