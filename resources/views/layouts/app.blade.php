<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ auth()->user()?->agency?->name ?? config('app.name') }} — BimoTech Immo</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: #f6f8fa;
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
            background: #fff;
            border-bottom: 1px solid #eaeef2;
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
        .table-card { background:#fff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden; }
        .dt { width:100%;border-collapse:collapse; }
        .dt th { padding:10px 16px;text-align:left;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#9ca3af;background:#f9fafb;border-bottom:1px solid #e5e7eb; }
        .dt td { padding:12px 16px;font-size:13px;color:#374151;border-bottom:1px solid #f3f4f6;vertical-align:middle; }
        .dt tbody tr:last-child td { border-bottom:none; }
        .dt tbody tr:hover { background:#f9fafb; }

        /* ── Badges ── */
        .badge { display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:99px;font-size:11px;font-weight:600; }
        .bdot { width:5px;height:5px;border-radius:50%;background:currentColor; }

        /* ── Boutons icône (tableaux) ── */
        .act-btn { display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:7px;border:1px solid #e5e7eb;background:#fff;color:#6b7280;text-decoration:none;transition:all .15s;cursor:pointer; }
        .act-btn:hover { border-color:#c9a84c;color:#8a6e2f; }
        .act-btn.danger:hover { border-color:#fca5a5;color:#dc2626;background:#fef2f2; }
        .act-btn.primary { background:#2a4a7f;border-color:#2a4a7f;color:#fff; }
        .act-btn.primary:hover { background:#1e3a6f; }

        /* ── KPI ligne (pages index) ── */
        .kpi-row { display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:22px; }
        .kpi { background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:16px 18px; }
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
        .form-input,.form-select,.form-textarea { width:100%;padding:9px 12px;border:1px solid #e5e7eb;border-radius:8px;font-size:13px;font-family:'DM Sans',sans-serif;color:#0d1117;background:#fff;outline:none;transition:border .15s; }
        .form-input:focus,.form-select:focus,.form-textarea:focus { border-color:#c9a84c;box-shadow:0 0 0 3px rgba(201,168,76,.1); }
        .form-input.error,.form-select.error { border-color:#dc2626; }
        .form-error { font-size:11px;color:#dc2626;margin-top:3px; }
        .form-textarea { resize:vertical;min-height:80px; }

        /* ── Barre de soumission (formulaires) ── */
        .submit-bar { display:flex;justify-content:flex-end;gap:10px;padding:14px 20px;border-top:1px solid #e5e7eb;background:#f9fafb; }
        .btn-cancel { padding:8px 16px;border-radius:8px;border:1px solid #e5e7eb;background:#fff;color:#6b7280;font-size:13px;font-family:'DM Sans',sans-serif;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center; }
        .btn-submit { padding:8px 18px;border-radius:8px;border:none;background:#2a4a7f;color:#fff;font-size:13px;font-weight:600;font-family:'DM Sans',sans-serif;cursor:pointer;display:inline-flex;align-items:center;gap:6px; }

        /* ── État vide ── */
        .empty-state { padding:56px 20px;text-align:center; }
    </style>

    {{ $styles ?? '' }}

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
    </style>
</head>
<body>

    <x-sidebar :agency="auth()->user()?->agency" />

    <div class="main-wrapper">

        <header class="topbar">
            <div class="topbar-breadcrumb">
                {{ auth()->user()?->agency?->name ?? 'BimoTech' }}
                <span style="color:#d0d7de">›</span>
                <strong>{{ $header ?? 'Dashboard' }}</strong>
            </div>
            <div style="display:flex;align-items:center;gap:12px">
                {{ $topbarActions ?? '' }}
            </div>
        </header>

        <main class="page-content">

            @if(session('success'))
                <div class="flash-success">{{ session('success') }}</div>
            @endif
            @if(session('warning'))
                <div class="flash-warning">{{ session('warning') }}</div>
            @endif
            @if(session('error'))
                <div class="flash-error">{{ session('error') }}</div>
            @endif
            @if($errors->any() && !$errors->hasBag('default') === false)
                {{-- Les erreurs de formulaire sont gérées dans chaque vue --}}
            @endif

            {{-- SLOT PRINCIPAL — contenu de la vue --}}
            {{ $slot ?? '' }}
@yield('content')

        </main>
    </div>

    {{ $scripts ?? '' }}
</body>
</html>