<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erreur serveur — bee</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@400;500;600&display=swap">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'DM Sans', sans-serif;
            background: #f9f7f2;
            color: #1c2128;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        .card {
            background: #fff;
            border: 1px solid #e8e3d8;
            border-radius: 20px;
            padding: 48px 40px;
            max-width: 440px;
            width: 100%;
            text-align: center;
            box-shadow: 0 4px 32px rgba(0,0,0,.06);
        }
        .icon-wrap {
            width: 72px; height: 72px;
            border-radius: 18px;
            background: #fef9c3;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 24px;
        }
        .icon-wrap svg { width: 32px; height: 32px; color: #d97706; }
        .code {
            font-family: 'Syne', sans-serif;
            font-size: 72px; font-weight: 800;
            color: #d97706; letter-spacing: -2px;
            line-height: 1; margin-bottom: 12px;
        }
        h1 {
            font-family: 'Syne', sans-serif;
            font-size: 20px; font-weight: 700;
            color: #0d1117; margin-bottom: 10px;
        }
        p { font-size: 13px; color: #6b7280; line-height: 1.6; margin-bottom: 28px; }
        .actions { display: flex; gap: 10px; justify-content: center; flex-wrap: wrap; }
        .btn-back {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 10px 20px; border-radius: 9px;
            border: 1px solid #e8e3d8; background: #f9f7f2;
            color: #57606a; font-size: 13px; font-weight: 500;
            font-family: 'DM Sans', sans-serif;
            text-decoration: none; transition: background .15s;
        }
        .btn-back:hover { background: #f0ece3; }
        .btn-home {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 10px 20px; border-radius: 9px;
            border: none; background: var(--ac);
            color: #0d1117; font-size: 13px; font-weight: 600;
            font-family: 'DM Sans', sans-serif;
            text-decoration: none; transition: opacity .15s;
        }
        .btn-home:hover { opacity: .85; }
        .brand { margin-top: 32px; font-size: 12px; color: #9ca3af; }
        .brand strong { color: var(--ac); }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon-wrap">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                <line x1="12" y1="9" x2="12" y2="13"/>
                <line x1="12" y1="17" x2="12.01" y2="17"/>
            </svg>
        </div>

        <div class="code">500</div>
        <h1>Erreur interne du serveur</h1>
        <p>
            Une erreur inattendue s'est produite.<br>
            Notre équipe a été notifiée. Veuillez réessayer dans quelques instants.
        </p>

        <div class="actions">
            <a href="javascript:history.back()" class="btn-back">
                <svg style="width:13px;height:13px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                Retour
            </a>
            @auth
            <a href="{{ route('dashboard') }}" class="btn-home">
                <svg style="width:13px;height:13px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
                Tableau de bord
            </a>
            @else
            <a href="{{ route('login') }}" class="btn-home">Se connecter</a>
            @endauth
        </div>

        <div class="brand" style="display:inline-flex;align-items:center;gap:8px;">
            <svg style="width:22px;height:22px;color:#A60F1C;flex-shrink:0;" viewBox="0 0 120 110" fill="currentColor"><path d="M52 50 C56 28 84 18 90 34 C96 50 78 62 54 56 Z"/><path d="M52 56 C58 36 86 28 90 46 C94 64 74 72 54 64 Z"/><path d="M50 62 C56 46 78 44 80 58 C82 72 66 76 52 70 Z"/><ellipse cx="38" cy="60" rx="14" ry="18" transform="rotate(-8 38 60)"/><circle cx="24" cy="57" r="12"/><circle cx="21" cy="54" r="4" fill="white"/><line x1="18" y1="47" x2="8" y2="32" stroke="currentColor" stroke-width="4" stroke-linecap="round"/><line x1="22" y1="46" x2="14" y2="28" stroke="currentColor" stroke-width="4" stroke-linecap="round"/><line x1="28" y1="72" x2="16" y2="84" stroke="currentColor" stroke-width="3.5" stroke-linecap="round"/><line x1="34" y1="76" x2="24" y2="90" stroke="currentColor" stroke-width="3.5" stroke-linecap="round"/><line x1="40" y1="78" x2="32" y2="92" stroke="currentColor" stroke-width="3.5" stroke-linecap="round"/></svg>
            <span style="font-family:'Syne',sans-serif;font-weight:800;font-size:15px;color:#A60F1C;">bee</span>
        </div>
    </div>
</body>
</html>
