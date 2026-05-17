<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ auth()->user()?->agency?->name ?? config('app.name') }} — BimoTech Immo</title>

    {{-- ── PWA : manifest + icône + couleur de thème ── --}}
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#c9a84c">
    <link rel="apple-touch-icon" href="/icons/icon-192.png">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="BIMO-tech">

    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500;600&display=swap" media="print" onload="this.media='all'">
<noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500;600&display=swap"></noscript>

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: #f9f7f2;
            color: #1c2128;
            display: flex;
            min-height: 100vh;
        }

        .main-wrapper {
            margin-left: 248px;
            flex: 1;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .topbar {
            background: #fffef9;
            border-bottom: 1px solid #ede9e0;
            padding: 0 2rem;
            height: 56px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 90;
        }

        .topbar-breadcrumb {
            font-size: 13px;
            color: #8b949e;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .topbar-breadcrumb strong { color: #1c2128; font-weight: 500; }

        .page-content { padding: 2rem; flex: 1; }

        .btn-primary {
            background: #c9a84c; color: #0d1117;
            font-family: 'DM Sans', sans-serif; font-size: 13.5px; font-weight: 600;
            padding: 9px 20px; border-radius: 8px; border: none;
            cursor: pointer; text-decoration: none; display: inline-flex;
            align-items: center; gap: 6px; transition: opacity .15s;
        }
        .btn-primary:hover { opacity: .85; }

        .btn-secondary {
            background: transparent; color: #57606a;
            font-family: 'DM Sans', sans-serif; font-size: 13.5px; font-weight: 500;
            padding: 9px 20px; border-radius: 8px; border: 1px solid #d0d7de;
            cursor: pointer; text-decoration: none; display: inline-flex;
            align-items: center; gap: 6px; transition: background .15s;
        }
        .btn-secondary:hover { background: #f3f4f6; }

        .flash-success {
            background: rgba(59,109,17,.08); border: 1px solid rgba(59,109,17,.2);
            border-left: 4px solid #3B6D11; color: #3B6D11;
            padding: 12px 16px; border-radius: 8px; font-size: 13.5px; margin-bottom: 1.5rem;
        }
        .flash-warning {
            background: rgba(201,168,76,.08); border: 1px solid rgba(201,168,76,.3);
            border-left: 4px solid #c9a84c; color: #8a6e2f;
            padding: 12px 16px; border-radius: 8px; font-size: 13.5px; margin-bottom: 1.5rem;
        }
        .flash-error {
            background: rgba(226,75,74,.08); border: 1px solid rgba(226,75,74,.2);
            border-left: 4px solid #E24B4A; color: #A32D2D;
            padding: 12px 16px; border-radius: 8px; font-size: 13.5px; margin-bottom: 1.5rem;
        }

        /* ── Tables ── */
        .table-card { background:#fffef9;border:1px solid #e8e3d8;border-radius:14px;overflow:hidden; }
        .dt { width:100%;border-collapse:collapse; }
        .dt th { padding:10px 16px;text-align:left;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#9ca3af;background:#f5f2ea;border-bottom:1px solid #e8e3d8; }
        .dt td { padding:12px 16px;font-size:13px;color:#374151;border-bottom:1px solid #f0ece3;vertical-align:middle; }
        .dt tbody tr:last-child td { border-bottom:none; }
        .dt tbody tr:hover { background:#f5f2ea; }

        /* ── Badges ── */
        .badge { display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:99px;font-size:11px;font-weight:600; }
        .bdot { width:5px;height:5px;border-radius:50%;background:currentColor; }

        /* ── Boutons icône (tableaux) ── */
        .act-btn { display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:7px;border:1px solid #e8e3d8;background:#fffef9;color:#6b7280;text-decoration:none;transition:all .15s;cursor:pointer; }
        .act-btn:hover { border-color:#c9a84c;color:#8a6e2f; }
        .act-btn.danger:hover { border-color:#fca5a5;color:#dc2626;background:#fef2f2; }
        .act-btn.primary { background:#2a4a7f;border-color:#2a4a7f;color:#fff; }
        .act-btn.primary:hover { background:#1e3a6f; }

        /* ── KPI ligne (pages index) ── */
        .kpi-row { display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:22px; }
        .kpi { background:#fffef9;border:1px solid #e8e3d8;border-radius:12px;padding:16px 18px; }
        .kpi.gold  { border-top:3px solid #c9a84c; }
        .kpi.green { border-top:3px solid #16a34a; }
        .kpi.blue  { border-top:3px solid #1d4ed8; }
        .kpi.dark  { border-top:3px solid #0d1117; }
        .kpi.amber { border-top:3px solid #d97706; }
        .kpi.red   { border-top:3px solid #dc2626; }
        .kpi-lbl { font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#9ca3af;margin-bottom:6px; }
        .kpi-val { font-family:'Syne',sans-serif;font-size:20px;font-weight:700;color:#0d1117;line-height:1.1; }
        .kpi-sub { font-size:11px;color:#9ca3af;margin-top:4px; }

        /* ── Icônes de card (formulaires) ── */
        .card-icon { width:30px;height:30px;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0; }
        .card-icon svg { width:15px;height:15px; }
        .card-icon.gold   { background:#f5e9c9;color:#8a6e2f; }
        .card-icon.blue   { background:#dbeafe;color:#1d4ed8; }
        .card-icon.green  { background:#dcfce7;color:#16a34a; }
        .card-icon.purple { background:#ede9fe;color:#7c3aed; }
        .card-icon.red    { background:#fee2e2;color:#dc2626; }

        /* ── Champs de formulaire ── */
        .form-row   { display:grid;grid-template-columns:1fr 1fr;gap:14px; }
        .form-group { margin-bottom:14px; }
        .form-group:last-child { margin-bottom:0; }
        .form-label { display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:5px; }
        .req { color:#dc2626; }
        .opt { color:#9ca3af;font-weight:400; }
        .form-input,.form-select,.form-textarea { width:100%;padding:9px 12px;border:1px solid #e8e3d8;border-radius:8px;font-size:13px;font-family:'DM Sans',sans-serif;color:#0d1117;background:#fffef9;outline:none;transition:border .15s; }
        .form-input:focus,.form-select:focus,.form-textarea:focus { border-color:#c9a84c;box-shadow:0 0 0 3px rgba(201,168,76,.1); }
        .form-input.error,.form-select.error { border-color:#dc2626; }
        .form-error { font-size:11px;color:#dc2626;margin-top:3px; }
        .form-textarea { resize:vertical;min-height:80px; }

        /* ── Barre de soumission (formulaires) ── */
        .submit-bar { display:flex;justify-content:flex-end;gap:10px;padding:14px 20px;border-top:1px solid #e8e3d8;background:#f5f2ea; }
        .btn-cancel { padding:8px 16px;border-radius:8px;border:1px solid #e8e3d8;background:#fffef9;color:#6b7280;font-size:13px;font-family:'DM Sans',sans-serif;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center; }
        /* btn-submit = même couleur que btn-primary (couleur agence) — cohérence entre toutes les pages */
        .btn-submit { padding:8px 18px;border-radius:8px;border:none;background:var(--ac,#c9a84c);color:#0d1117;font-size:13px;font-weight:600;font-family:'DM Sans',sans-serif;cursor:pointer;display:inline-flex;align-items:center;gap:6px;transition:opacity .15s; }
        .btn-submit:hover { opacity:.85; }

        /* ── État vide ── */
        .empty-state { padding:56px 20px;text-align:center; }

        /* ── Tooltip ── */
        .tip-wrap { position:relative;display:inline-flex;align-items:center; }
        .tip-icon { width:15px;height:15px;border-radius:50%;background:#e5e7eb;color:#6b7280;display:inline-flex;align-items:center;justify-content:center;font-size:9px;font-weight:700;cursor:help;margin-left:5px;flex-shrink:0;font-style:normal; }
        .tip-icon::after { content:attr(data-tip);position:absolute;bottom:calc(100% + 7px);left:50%;transform:translateX(-50%);background:#0d1117;color:#fff;font-size:11px;font-weight:400;padding:7px 11px;border-radius:7px;width:230px;white-space:normal;text-align:left;z-index:200;pointer-events:none;opacity:0;transition:opacity .15s;line-height:1.5; }
        .tip-icon::before { content:'';position:absolute;bottom:calc(100% + 2px);left:50%;transform:translateX(-50%);border:5px solid transparent;border-top-color:#0d1117;opacity:0;transition:opacity .15s; }
        .tip-icon:hover::after,.tip-icon:hover::before { opacity:1; }

        /* ── Flash messages (fermeture + transition) ── */
        .flash-success,.flash-warning,.flash-error { display:flex;align-items:flex-start;justify-content:space-between;gap:12px;transition:opacity .4s,transform .4s; }
        .flash-close { background:none;border:none;cursor:pointer;opacity:.45;font-size:16px;line-height:1;padding:0;flex-shrink:0;margin-top:1px; }
        .flash-close:hover { opacity:.8; }

        /* ── Modale de confirmation ── */
        #g-confirm-overlay { display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:9999;align-items:center;justify-content:center; }
        #g-confirm-overlay.open { display:flex; }
        #g-confirm-box { background:#fffef9;border-radius:14px;padding:28px 28px 22px;max-width:400px;width:90%;box-shadow:0 20px 60px rgba(0,0,0,.25);animation:confirmIn .18s ease; }
        @keyframes confirmIn { from{opacity:0;transform:scale(.95)} to{opacity:1;transform:scale(1)} }
        #g-confirm-icon-wrap { width:42px;height:42px;border-radius:11px;display:flex;align-items:center;justify-content:center;flex-shrink:0; }
        #g-confirm-actions { display:flex;gap:10px;justify-content:flex-end;margin-top:22px; }
        .g-btn-cancel { padding:8px 18px;border-radius:8px;border:1px solid #e8e3d8;background:#fffef9;color:#6b7280;font-size:13px;font-family:'DM Sans',sans-serif;cursor:pointer; }
        .g-btn-cancel:hover { background:#f5f2ea; }
        .g-btn-ok { padding:8px 18px;border-radius:8px;border:none;color:#fff;font-size:13px;font-weight:600;font-family:'DM Sans',sans-serif;cursor:pointer; }

        /* ── Spinner anti-double-submit ── */
        @keyframes spin { to { transform: rotate(360deg); } }
        .btn-spinning { opacity:.7;pointer-events:none; }
        .btn-spin-icon { display:inline-block;width:13px;height:13px;border:2px solid rgba(255,255,255,.4);border-top-color:currentColor;border-radius:50%;animation:spin .7s linear infinite;vertical-align:middle;margin-right:5px; }

        /* ── Hamburger (mobile) ── */
        .bm-hamburger { display:none;flex-direction:column;justify-content:center;gap:5px;width:36px;height:36px;border:none;background:transparent;cursor:pointer;padding:6px;flex-shrink:0; }
        .bm-hamburger span { display:block;width:20px;height:2px;border-radius:2px;background:#374151;transition:all .22s; }
        .bm-overlay { display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:99;opacity:0;transition:opacity .25s; }
        .bm-overlay.open { display:block;opacity:1; }

        /* ── Topbar scroll shadow ── */
        .topbar { transition: box-shadow .2s ease; }
        .topbar.scrolled { box-shadow: 0 2px 20px rgba(0,0,0,.07); }

        /* ── Hamburger → X quand sidebar ouverte ── */
        .bm-hamburger.open span:nth-child(1) { transform: translateY(7px) rotate(45deg); }
        .bm-hamburger.open span:nth-child(2) { opacity: 0; transform: scaleX(0); }
        .bm-hamburger.open span:nth-child(3) { transform: translateY(-7px) rotate(-45deg); }

        /* ── Icônes dans les flash messages ── */
        .flash-icon { width:16px;height:16px;flex-shrink:0;margin-top:2px; }

        /* ── Recherche globale cachée sur très petit écran ── */
        @media (max-width: 479px) {
            #global-search-wrap { display:none !important; }
        }

        /* ── Mobile card table : état actif tactile ── */
        @media (max-width: 768px) {
            .dt tr { transition: background .12s ease; }
            .dt tr:active { background: #f0ece3 !important; }
        }

        /* ─────────────── RESPONSIVE ──────────────────────────────── */
        @media (max-width: 768px) {

            /* Sidebar masquée par défaut, slide-in depuis la gauche */
            .bm-sidebar-wrap {
                transform: translateX(-100%);
                transition: transform .25s ease;
                z-index: 110;
            }
            .bm-sidebar-wrap.open { transform: translateX(0); }

            /* Contenu principal : plus de marge gauche */
            .main-wrapper { margin-left: 0; }

            /* Topbar : hamburger visible */
            .bm-hamburger { display:flex; }

            /* Moins de padding sur les pages */
            .page-content { padding: 1rem; }

            /* ── Grilles formulaires → colonne unique ── */
            .form-row,
            .form-row-3      { grid-template-columns: 1fr !important; }

            /* Formulaires avec récap (contrats, paiements, biens) */
            .form-grid       { grid-template-columns: 1fr !important; }

            /* Récap sticky → non-sticky sur mobile (suit le formulaire) */
            .recap-card      { position:static !important; }

            /* Sidebar de création (users/create) */
            .create-page     { grid-template-columns: 1fr !important; }
            .create-sidebar  { position:static !important; height:auto !important; flex-direction:row !important; flex-wrap:wrap; gap:6px; padding:16px; }
            .nav-section     { display:none; }

            /* ── Grilles KPI → 2 colonnes ── */
            .kpi-row,
            .kpi-grid        { grid-template-columns: 1fr 1fr !important; }

            /* ── Grilles dashboard → colonne unique ── */
            .g2, .g3, .g4   { grid-template-columns: 1fr !important; }

            /* ── Submit bar → sticky en bas sur mobile ── */
            .submit-bar {
                position: sticky;
                bottom: 0;
                z-index: 50;
                flex-direction: row;
                box-shadow: 0 -2px 12px rgba(0,0,0,.08);
            }
            .btn-submit,
            .btn-cancel      { flex: 1; justify-content:center; padding:11px 16px; }

            /* ── Tables → cartes verticales ── */
            .dt thead { display:none; }
            .dt, .dt tbody { display:block; width:100%; }
            .dt tr {
                display:block;
                background:#fffef9;
                border:1px solid #e8e3d8;
                border-radius:12px;
                margin-bottom:10px;
                overflow:hidden;
            }
            .dt td {
                display:flex;
                justify-content:space-between;
                align-items:center;
                padding:10px 14px;
                border-bottom:1px solid #f0ece3;
                font-size:13px;
                text-align:right;
                gap:10px;
                min-height:36px;
            }
            .dt td:last-child { border-bottom:none; }
            .dt td::before {
                content:attr(data-label);
                font-size:10px;
                font-weight:700;
                text-transform:uppercase;
                letter-spacing:.6px;
                color:#9ca3af;
                text-align:left;
                flex-shrink:0;
                max-width:40%;
            }
            /* Colonne actions : centrée sans label */
            .dt td[data-label="Actions"],
            .dt td[data-label=""] { justify-content:center; }
            .dt td[data-label="Actions"]::before,
            .dt td[data-label=""]::before { display:none; }

            /* ── Topbar breadcrumb tronqué ── */
            .topbar-breadcrumb { max-width: calc(100vw - 120px); overflow: hidden; white-space: nowrap; text-overflow: ellipsis; }

            /* ── Séparateur recap visible sur mobile ── */
            .recap-mobile-sep { display:block !important; }
        }
    </style>

    {{ $styles ?? '' }}
    @stack('styles')

    {{-- Couleur de l'agence injectée en CSS variable --}}
    @php
        $agencyColor = auth()->user()?->agency?->couleur_primaire ?? '#c9a84c';
        $hex = ltrim($agencyColor, '#');
        $cr  = hexdec(substr($hex, 0, 2));
        $cg  = hexdec(substr($hex, 2, 2));
        $cb  = hexdec(substr($hex, 4, 2));
    @endphp
    <style>
        :root {
            --ac:   {{ $agencyColor }};
            --ac-r: {{ $cr }};
            --ac-g: {{ $cg }};
            --ac-b: {{ $cb }};
        }
        .btn-primary { background: var(--ac) !important; }
        /* ── Accent agence : décoration KPI + boutons dorés ── */
        .kpi-card.gold::before, .kpi.gold::before,
        .kpi-mini.gold::before, .kpi5.gold::before { background: var(--ac,#c9a84c) !important; }
        .btn-gold { background: var(--ac,#c9a84c) !important; }
    </style>
</head>
<body>

    {{-- Overlay mobile sidebar ── --}}
    <div class="bm-overlay" id="bm-overlay"></div>

    <x-sidebar :agency="auth()->user()?->agency" />

    <div class="main-wrapper">

        <header class="topbar">
            <div style="display:flex;align-items:center;gap:10px;min-width:0">
                <button class="bm-hamburger" id="bm-hamburger-btn" aria-label="Ouvrir le menu" aria-expanded="false">
                    <span></span><span></span><span></span>
                </button>
                <div class="topbar-breadcrumb">
                    <a href="{{ route('dashboard') }}" style="color:inherit;text-decoration:none;transition:color .15s" onmouseover="this.style.color='#c9a84c'" onmouseout="this.style.color='inherit'">{{ auth()->user()?->agency?->name ?? 'BimoTech' }}</a>
                    <span style="color:#d0d7de">›</span>
                    <strong>{{ $header ?? 'Tableau de bord' }}</strong>
                </div>
            </div>
            <div style="display:flex;align-items:center;gap:12px">
                {{ $topbarActions ?? '' }}

                @auth
                @if(auth()->user()->role === 'admin' || auth()->user()->role === 'superadmin')
                {{-- ── Recherche globale ── --}}
                <div style="position:relative" id="global-search-wrap">
                    <div style="position:relative">
                        <svg style="position:absolute;left:9px;top:50%;transform:translateY(-50%);width:13px;height:13px;color:#9ca3af;pointer-events:none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                        <input type="text" id="global-search-input"
                               placeholder="Rechercher…"
                               autocomplete="off"
                               style="width:200px;padding:7px 12px 7px 28px;border:1px solid #e8e3d8;border-radius:8px;font-size:13px;font-family:'DM Sans',sans-serif;color:#1c2128;background:#f9f7f2;outline:none;transition:all .2s"
                               onfocus="this.style.width='280px';this.style.borderColor='#c9a84c';this.style.background='#fff'"
                               onblur="if(!document.getElementById('global-search-results').matches(':hover')){this.style.width='200px';this.style.borderColor='#e8e3d8';this.style.background='#f9f7f2';setTimeout(()=>document.getElementById('global-search-results').style.display='none',150)}">
                    </div>
                    <div id="global-search-results"
                         style="display:none;position:absolute;top:calc(100% + 6px);right:0;width:340px;background:#fff;border:1px solid #e8e3d8;border-radius:12px;box-shadow:0 8px 32px rgba(0,0,0,.12);overflow:hidden;z-index:500">
                    </div>
                </div>
                @endif
                @endauth
            </div>
        </header>

        {{-- Bannière impersonation --}}
        @if(session('impersonating_id'))
        <div style="background:#dc2626;color:#fff;padding:9px 20px;font-size:12px;font-weight:600;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap">
            <span style="display:flex;align-items:center;gap:7px">
                <svg style="width:14px;height:14px;flex-shrink:0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                Impersonation active — connecté en tant que <strong style="margin:0 3px">{{ auth()->user()->name }}</strong> ({{ auth()->user()->role }})
            </span>
            <a href="{{ route('superadmin.impersonate.stop') }}"
               style="padding:5px 14px;border-radius:7px;background:rgba(255,255,255,.2);color:#fff;text-decoration:none;font-size:12px;font-weight:700;white-space:nowrap;border:1px solid rgba(255,255,255,.3)">
                ← Quitter l'impersonation
            </a>
        </div>
        @endif

        <main class="page-content">

            @if(session('success'))
                <div class="flash-success">
                    <svg class="flash-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    <span style="flex:1">{{ session('success') }}</span>
                    <button class="flash-close" onclick="this.closest('[class^=flash]').remove()">×</button>
                </div>
            @endif
            @if(session('warning'))
                <div class="flash-warning">
                    <svg class="flash-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                    <span style="flex:1">{{ session('warning') }}</span>
                    <button class="flash-close" onclick="this.closest('[class^=flash]').remove()">×</button>
                </div>
            @endif
            @if(session('error'))
                <div class="flash-error">
                    <svg class="flash-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                    <span style="flex:1">{{ session('error') }}</span>
                    <button class="flash-close" onclick="this.closest('[class^=flash]').remove()">×</button>
                </div>
            @endif
            @if($errors->any())
                <div class="flash-error">
                    <svg class="flash-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    <span style="flex:1">
                        <strong>Veuillez corriger les erreurs suivantes :</strong>
                        @foreach($errors->all() as $e)
                            <br>{{ $e }}
                        @endforeach
                    </span>
                    <button class="flash-close" onclick="this.closest('.flash-error').remove()">×</button>
                </div>
            @endif

            {{-- SLOT PRINCIPAL — contenu de la vue --}}
            {{ $slot ?? '' }}
@yield('content')

        </main>
    </div>

    {{-- ── Modale de confirmation globale ── --}}
    <div id="g-confirm-overlay" role="dialog" aria-modal="true" aria-labelledby="g-confirm-title">
        <div id="g-confirm-box">
            <div style="display:flex;align-items:flex-start;gap:14px">
                <div id="g-confirm-icon-wrap">
                    <svg id="g-confirm-icon" style="width:20px;height:20px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                        <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
                    </svg>
                </div>
                <div style="flex:1">
                    <div id="g-confirm-title" style="font-family:'Syne',sans-serif;font-size:15px;font-weight:700;color:#0d1117;margin-bottom:4px"></div>
                    <div id="g-confirm-msg" style="font-size:13px;color:#6b7280;line-height:1.55"></div>
                </div>
            </div>
            <div id="g-confirm-actions">
                <button class="g-btn-cancel" id="g-confirm-cancel">Annuler</button>
                <button class="g-btn-ok" id="g-confirm-ok">Confirmer</button>
            </div>
        </div>
    </div>

    {{ $scripts ?? '' }}

    {{-- ── PWA : enregistrement du Service Worker ── --}}
    <script>
        // ── Modale de confirmation globale ─────────────────────────────────────
        (function () {
            var overlay    = document.getElementById('g-confirm-overlay');
            var titleEl    = document.getElementById('g-confirm-title');
            var msgEl      = document.getElementById('g-confirm-msg');
            var okBtn      = document.getElementById('g-confirm-ok');
            var cancelBtn  = document.getElementById('g-confirm-cancel');
            var iconWrap   = document.getElementById('g-confirm-icon-wrap');
            var pendingForm = null;

            function open(title, msg, okLabel, okColor, iconColor) {
                titleEl.textContent    = title   || 'Confirmer l\'action';
                msgEl.textContent      = msg     || 'Cette action est irréversible.';
                okBtn.textContent      = okLabel || 'Confirmer';
                okBtn.style.background = okColor || '#dc2626';
                iconWrap.style.background = iconColor || '#fee2e2';
                iconWrap.querySelector('svg').style.color = okColor || '#dc2626';
                overlay.classList.add('open');
                document.body.style.overflow = 'hidden';
                okBtn.focus();
            }

            function close() {
                overlay.classList.remove('open');
                document.body.style.overflow = '';
                pendingForm = null;
            }

            cancelBtn.addEventListener('click', close);
            overlay.addEventListener('click', function (e) { if (e.target === overlay) close(); });
            document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && overlay.classList.contains('open')) close(); });

            okBtn.addEventListener('click', function () {
                if (pendingForm) { pendingForm._gConfirmed = true; pendingForm.requestSubmit(); }
                close();
            });

            // Intercepte tous les formulaires ayant data-confirm
            document.addEventListener('submit', function (e) {
                var form = e.target;
                if (!form.dataset.confirm) return;
                if (form._gConfirmed) return;
                e.preventDefault();
                pendingForm = form;
                open(
                    form.dataset.confirmTitle,
                    form.dataset.confirm,
                    form.dataset.confirmOk,
                    form.dataset.confirmColor,
                    form.dataset.confirmIconBg
                );
            }, true);
        })();

        // ── Anti double-submit ──────────────────────────────────────────────────
        document.addEventListener('submit', function (e) {
            var form = e.target;
            // Ne pas désactiver si la confirmation modale n'a pas encore validé
            if (form.dataset.confirm && !form._gConfirmed) return;
            var btn = form.querySelector('button[type=submit]');
            if (!btn || btn.disabled) return;
            btn.disabled = true;
            btn.classList.add('btn-spinning');
            btn.insertAdjacentHTML('afterbegin', '<span class="btn-spin-icon"></span>');
        });

        // ── Flash messages auto-dismiss ─────────────────────────────────────────
        // Succès/avertissement : 5 s — Erreurs : 12 s (plus de temps pour lire)
        document.querySelectorAll('.flash-success,.flash-warning').forEach(function (el) {
            setTimeout(function () {
                el.style.opacity = '0';
                el.style.transform = 'translateY(-6px)';
                setTimeout(function () { el.remove(); }, 420);
            }, 5000);
        });
        document.querySelectorAll('.flash-error').forEach(function (el) {
            setTimeout(function () {
                el.style.opacity = '0';
                el.style.transform = 'translateY(-6px)';
                setTimeout(function () { el.remove(); }, 420);
            }, 12000);
        });

        // ── Tri de colonnes ──────────────────────────────────────────────────────
        // Usage : <th data-sort="0"> sur les colonnes concernées, data-sort-type="num|date|str"
        (function () {
            var state = {}; // { tableId: { col, asc } }

            function parseVal(cell, type) {
                var t = cell ? cell.innerText.trim() : '';
                if (type === 'num') {
                    return parseFloat(t.replace(/\s/g, '').replace(',', '.').replace(/[^0-9.-]/g, '')) || 0;
                }
                if (type === 'date') {
                    var m = t.match(/(\d{2})\/(\d{2})\/(\d{4})/);
                    return m ? m[3] + m[2] + m[1] : t;
                }
                return t.toLowerCase();
            }

            document.addEventListener('click', function (e) {
                var th = e.target.closest('th[data-sort]');
                if (!th) return;
                var table = th.closest('table');
                if (!table) return;
                var tid   = table.dataset.sortId || (table.dataset.sortId = 'tbl' + Math.random().toString(36).slice(2));
                var col   = parseInt(th.dataset.sort, 10);
                var type  = th.dataset.sortType || 'str';
                var asc   = state[tid] && state[tid].col === col ? !state[tid].asc : true;
                state[tid] = { col, asc };

                // Reset indicators
                table.querySelectorAll('th[data-sort]').forEach(function (h) {
                    h.querySelector('.sort-arrow')?.remove();
                    h.style.color = '';
                });
                var arrow = document.createElement('span');
                arrow.className = 'sort-arrow';
                arrow.textContent = asc ? ' ↑' : ' ↓';
                arrow.style.cssText = 'font-size:10px;opacity:.6';
                th.appendChild(arrow);
                th.style.color = '#c9a84c';

                var tbody = table.querySelector('tbody');
                var rows  = Array.from(tbody.querySelectorAll('tr'));
                rows.sort(function (a, b) {
                    var va = parseVal(a.cells[col], type);
                    var vb = parseVal(b.cells[col], type);
                    if (va < vb) return asc ? -1 : 1;
                    if (va > vb) return asc ? 1 : -1;
                    return 0;
                });
                rows.forEach(function (r) { tbody.appendChild(r); });
            });

            // Style curseur sur les th triables
            document.addEventListener('DOMContentLoaded', function () {
                document.querySelectorAll('th[data-sort]').forEach(function (th) {
                    th.style.cursor = 'pointer';
                    th.title = 'Cliquer pour trier';
                });
            });
        })();

        // ── Alerte formulaire non sauvegardé ────────────────────────────────────
        (function () {
            var dirty = false;

            // Marquer comme modifié dès le premier changement dans un formulaire principal
            document.addEventListener('change', function (e) {
                var form = e.target.closest('form');
                if (form && !form.dataset.noWarn && !form.dataset.confirm) {
                    dirty = true;
                }
            });
            document.addEventListener('input', function (e) {
                var form = e.target.closest('form');
                if (form && !form.dataset.noWarn && !form.dataset.confirm) {
                    dirty = true;
                }
            });

            // Réinitialiser au submit (l'utilisateur sauvegarde)
            document.addEventListener('submit', function () { dirty = false; });

            window.addEventListener('beforeunload', function (e) {
                if (dirty) {
                    e.preventDefault();
                    e.returnValue = '';
                }
            });
        })();

        // ── Copier référence ─────────────────────────────────────────────────────
        function copyRef(text, btn) {
            navigator.clipboard.writeText(text).then(function () {
                var orig = btn.innerHTML;
                btn.innerHTML = '<svg style="width:11px;height:11px;color:#16a34a" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>';
                btn.style.borderColor = '#bbf7d0';
                setTimeout(function () { btn.innerHTML = orig; btn.style.borderColor = ''; }, 1500);
            });
        }

        // ── Tables → cartes mobile : data-label auto depuis les headers ─────────
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('table.dt').forEach(function (table) {
                var headers = Array.from(table.querySelectorAll('thead th'))
                    .map(function (th) { return th.innerText.trim(); });
                table.querySelectorAll('tbody tr').forEach(function (row) {
                    Array.from(row.cells).forEach(function (cell, i) {
                        cell.setAttribute('data-label', headers[i] || '');
                    });
                });
            });
        });

        // ── Recherche globale ────────────────────────────────────────────────────
        (function () {
            var input   = document.getElementById('global-search-input');
            var results = document.getElementById('global-search-results');
            if (!input || !results) return;

            var icons = {
                home: '<path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>',
                file: '<path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/>',
                user: '<path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/>',
            };
            var badgeColors = {
                actif: '#dcfce7;color:#16a34a',
                loue:  '#dcfce7;color:#16a34a',
                disponible: '#dbeafe;color:#1d4ed8',
                résilié: '#fee2e2;color:#dc2626',
                expiré: '#f3f4f6;color:#6b7280',
            };

            var timer = null;
            input.addEventListener('input', function () {
                clearTimeout(timer);
                var q = input.value.trim();
                if (q.length < 2) { results.style.display = 'none'; return; }
                timer = setTimeout(function () { doSearch(q); }, 250);
            });

            function doSearch(q) {
                fetch('{{ route('admin.search') }}?q=' + encodeURIComponent(q), {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(function (r) { return r.json(); })
                .then(function (data) { renderResults(data.results); })
                .catch(function () {});
            }

            function renderResults(items) {
                if (!items.length) {
                    results.innerHTML = '<div style="padding:20px;text-align:center;font-size:13px;color:#9ca3af">Aucun résultat</div>';
                    results.style.display = 'block';
                    return;
                }
                var html = '';
                var lastType = null;
                items.forEach(function (item) {
                    if (item.type !== lastType) {
                        html += '<div style="padding:6px 14px 2px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#9ca3af;background:#f9fafb">' + item.type + '</div>';
                        lastType = item.type;
                    }
                    var bc = badgeColors[item.badge] || null;
                    var badgeHtml = bc ? '<span style="display:inline-block;padding:2px 7px;border-radius:99px;font-size:10px;font-weight:600;background:' + bc + ';margin-left:6px">' + item.badge + '</span>' : '';
                    html += '<a href="' + item.url + '" style="display:flex;align-items:center;gap:10px;padding:10px 14px;text-decoration:none;border-bottom:1px solid #f3f4f6;transition:background .1s" onmouseover="this.style.background=\'#f9fafb\'" onmouseout="this.style.background=\'\'">'+
                        '<div style="width:28px;height:28px;border-radius:7px;background:#f3f4f6;display:flex;align-items:center;justify-content:center;flex-shrink:0">'+
                            '<svg style="width:13px;height:13px;color:#6b7280" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">' + (icons[item.icon] || '') + '</svg>'+
                        '</div>'+
                        '<div style="flex:1;min-width:0">'+
                            '<div style="font-size:13px;font-weight:500;color:#0d1117;display:flex;align-items:center;gap:4px">' + item.label + badgeHtml + '</div>'+
                            '<div style="font-size:11px;color:#9ca3af;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">' + item.sub + '</div>'+
                        '</div>'+
                    '</a>';
                });
                results.innerHTML = html;
                results.style.display = 'block';
            }

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') { results.style.display = 'none'; input.blur(); }
                if ((e.ctrlKey || e.metaKey) && e.key === 'k') { e.preventDefault(); input.focus(); }
            });
        })();

        // ── Sidebar mobile toggle ────────────────────────────────────────────────
        (function () {
            var sidebar  = document.querySelector('.bm-sidebar-wrap');
            var overlay  = document.getElementById('bm-overlay');
            var hamburger = document.getElementById('bm-hamburger-btn');
            if (!sidebar || !hamburger) return;

            function openSidebar() {
                sidebar.classList.add('open');
                overlay.classList.add('open');
                hamburger.classList.add('open');
                hamburger.setAttribute('aria-expanded', 'true');
                document.body.style.overflow = 'hidden';
            }
            function closeSidebar() {
                sidebar.classList.remove('open');
                overlay.classList.remove('open');
                hamburger.classList.remove('open');
                hamburger.setAttribute('aria-expanded', 'false');
                document.body.style.overflow = '';
            }

            hamburger.addEventListener('click', function () {
                sidebar.classList.contains('open') ? closeSidebar() : openSidebar();
            });
            overlay.addEventListener('click', closeSidebar);
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') closeSidebar();
            });
        })();

        // ── Ombre topbar au scroll ───────────────────────────────────────────────
        (function () {
            var tb = document.querySelector('.topbar');
            if (!tb) return;
            window.addEventListener('scroll', function () {
                tb.classList.toggle('scrolled', window.scrollY > 4);
            }, { passive: true });
        })();

        // ── PWA ─────────────────────────────────────────────────────────────────
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker
                    .register('/sw.js')
                    .then(reg => console.log('[PWA] Service Worker enregistré :', reg.scope))
                    .catch(err => console.warn('[PWA] Échec enregistrement SW :', err));
            });
        }
    </script>
    @stack('scripts')
</body>
</html>