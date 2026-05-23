<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex,nofollow">
    <title>Vérification 2FA — BimoTech Immo</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@400;500;600&display=swap" media="print" onload="this.media='all'">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'DM Sans', sans-serif;
            background: #f0eee9;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }
        .card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            padding: 36px 32px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 4px 24px rgba(0,0,0,.07);
        }
        .logo {
            font-family: 'Syne', sans-serif;
            font-size: 18px;
            font-weight: 800;
            color: #0d1117;
            margin-bottom: 28px;
            text-align: center;
        }
        .logo span { color: #c9a84c; }
        .lock-icon {
            width: 52px; height: 52px;
            background: #ede9fe;
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 20px;
        }
        .lock-icon svg { width: 26px; height: 26px; color: #7c3aed; }
        h1 {
            font-family: 'Syne', sans-serif;
            font-size: 20px;
            font-weight: 700;
            color: #0d1117;
            text-align: center;
            margin-bottom: 8px;
        }
        .subtitle {
            font-size: 13px;
            color: #6b7280;
            text-align: center;
            margin-bottom: 28px;
            line-height: 1.5;
        }
        .form-label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: .5px;
        }
        .code-input {
            width: 100%;
            padding: 14px 16px;
            border: 1.5px solid #e5e7eb;
            border-radius: 10px;
            font-size: 24px;
            font-weight: 700;
            font-family: 'Courier New', monospace;
            text-align: center;
            letter-spacing: 8px;
            color: #0d1117;
            outline: none;
            transition: border .15s, box-shadow .15s;
            background: #fafafa;
        }
        .code-input:focus {
            border-color: #7c3aed;
            box-shadow: 0 0 0 3px rgba(124,58,237,.12);
            background: #fff;
        }
        .code-input.error { border-color: #dc2626; }
        .form-error {
            font-size: 12px;
            color: #dc2626;
            margin-top: 6px;
            text-align: center;
        }
        .btn-submit {
            width: 100%;
            margin-top: 20px;
            padding: 13px;
            background: #6366f1;
            color: #fff;
            font-family: 'DM Sans', sans-serif;
            font-size: 14px;
            font-weight: 600;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: opacity .15s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
        }
        .btn-submit:hover { opacity: .88; }
        .hint {
            margin-top: 20px;
            font-size: 12px;
            color: #9ca3af;
            text-align: center;
            line-height: 1.6;
        }
    </style>
</head>
<body>
<div class="card">
    <div class="logo">BIMO<span>tech</span></div>

    <div class="lock-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="5" y="11" width="14" height="10" rx="2"/>
            <path d="M8 11V7a4 4 0 018 0v4"/>
        </svg>
    </div>

    <h1>Vérification 2FA</h1>
    <p class="subtitle">Saisissez le code à 6 chiffres de votre application d'authentification ou un code de récupération.</p>

    <form method="POST" action="{{ route('superadmin.2fa.verify') }}">
        @csrf
        <label class="form-label" for="code">Code d'authentification</label>
        <input
            type="text"
            id="code"
            name="code"
            class="code-input {{ $errors->has('code') ? 'error' : '' }}"
            inputmode="text"
            autocomplete="one-time-code"
            maxlength="11"
            placeholder="000000"
            autofocus
            value="{{ old('code') }}"
        >
        @error('code')
        <div class="form-error">{{ $message }}</div>
        @enderror

        <button type="submit" class="btn-submit">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
            Vérifier
        </button>
    </form>

    <p class="hint">
        Code à 6 chiffres (TOTP) ou code de récupération (ex: ABCDE-12345).<br>
        Utilisez un code de récupération si vous n'avez plus accès à votre app.
    </p>
</div>
</body>
</html>
