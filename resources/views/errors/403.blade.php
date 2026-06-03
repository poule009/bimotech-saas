<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accès refusé — Renlio</title>
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
            background: #fee2e2;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 24px;
        }
        .icon-wrap svg { width: 32px; height: 32px; color: #dc2626; }
        .code {
            font-family: 'Syne', sans-serif;
            font-size: 72px; font-weight: 800;
            color: #dc2626; letter-spacing: -2px;
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
            border: none; background: #c9a84c;
            color: #0d1117; font-size: 13px; font-weight: 600;
            font-family: 'DM Sans', sans-serif;
            text-decoration: none; transition: opacity .15s;
        }
        .btn-home:hover { opacity: .85; }
        .brand { margin-top: 32px; font-size: 12px; color: #9ca3af; }
        .brand strong { color: #c9a84c; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon-wrap">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="11" width="18" height="11" rx="2"/>
                <path d="M7 11V7a5 5 0 0110 0v4"/>
            </svg>
        </div>

        <div class="code">403</div>
        <h1>Accès refusé</h1>
        <p>
            Vous n'avez pas les permissions nécessaires pour accéder à cette page.<br>
            Si vous pensez qu'il s'agit d'une erreur, contactez votre administrateur.
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
            <a href="{{ route('login') }}" class="btn-home">
                Se connecter
            </a>
            @endauth
        </div>

        <div class="brand"><strong>Renlio</strong> Immo — Gestion immobilière</div>
    </div>
</body>
</html>
