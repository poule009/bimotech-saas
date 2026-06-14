<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex,nofollow">
    <title>Vérification 2FA — bee</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@400;500;600&display=swap" media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@400;500;600&display=swap"></noscript>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-body bg-bimo-bg min-h-screen flex items-center justify-center p-6">

<div class="bg-white border border-bimo-navy/10 rounded-[16px] p-9 w-full max-w-[400px] shadow-lg">
    <div class="flex justify-center mb-7">
        <x-bee-logo variant="navy" size="md" />
    </div>

    <div class="w-[52px] h-[52px] bg-bimo-navy/10 rounded-[14px] flex items-center justify-center mx-auto mb-5">
        <svg class="w-6 h-6 text-bimo-text/60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="5" y="11" width="14" height="10" rx="2"/>
            <path d="M8 11V7a4 4 0 018 0v4"/>
        </svg>
    </div>

    <h1 class="font-display font-extrabold text-xl text-bimo-text text-center mb-2">Vérification 2FA</h1>
    <p class="font-body text-sm text-bimo-text/50 text-center leading-relaxed mb-7">Saisissez le code à 6 chiffres de votre application d'authentification ou un code de récupération.</p>

    <form method="POST" action="{{ route('superadmin.2fa.verify') }}" class="space-y-4">
        @csrf
        <div class="space-y-1.5">
            <label class="block font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40" for="code">Code d'authentification</label>
            <input type="text" id="code" name="code"
                   class="w-full px-4 py-4 rounded-[10px] bg-bimo-bg border text-center font-bold text-2xl tracking-[8px] focus:outline-none focus:ring-2 transition-all duration-150 @error('code') border-bimo-red focus:border-bimo-red focus:ring-bimo-red/15 @else border-bimo-navy/20 focus:border-bimo-gold focus:ring-bimo-gold/15 @enderror"
                   style="font-family:'Courier New',monospace"
                   inputmode="text" autocomplete="one-time-code" maxlength="11" placeholder="000000" autofocus value="{{ old('code') }}">
            @error('code')
            <p class="font-body text-xs text-bimo-red text-center">{{ $message }}</p>
            @enderror
        </div>
        <button type="submit"
                class="w-full inline-flex items-center justify-center gap-2 py-3 bg-bimo-navy text-white font-display font-bold text-sm rounded-[10px] hover:bg-bimo-navy-dk transition-colors duration-150 cursor-pointer">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
            Vérifier
        </button>
    </form>

    <p class="font-body text-xs text-bimo-text/30 text-center mt-5 leading-relaxed">
        Code à 6 chiffres (TOTP) ou code de récupération (ex: ABCDE-12345).<br>
        Utilisez un code de récupération si vous n'avez plus accès à votre app.
    </p>
</div>

</body>
</html>
