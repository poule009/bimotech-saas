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
    </style>

    {{ $styles ?? '' }}
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