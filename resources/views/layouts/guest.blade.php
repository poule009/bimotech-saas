<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Bimothèque Immo') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap"
          media="print" onload="this.media='all'">
    <noscript>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap">
    </noscript>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-bimo-bg font-body antialiased flex items-center justify-center min-h-screen px-4 py-8">

    <div class="w-full max-w-md">

        {{-- Logo --}}
        <div class="text-center mb-8">
            <a href="{{ url('/') }}" class="inline-flex flex-col items-center gap-2">
                <div class="w-12 h-12 rounded-[12px] bg-bimo-navy flex items-center justify-center">
                    <span class="font-display font-extrabold text-bimo-gold text-lg">B</span>
                </div>
                <span class="font-display font-bold text-bimo-navy text-lg">
                    Bimothèque <span class="text-bimo-gold">Immo</span>
                </span>
            </a>
            <p class="font-body text-sm text-bimo-navy/40 mt-1">Gestion immobilière professionnelle</p>
        </div>

        {{-- Contenu --}}
        {{ $slot }}

        {{-- Footer --}}
        <p class="text-center font-body text-xs text-bimo-navy/30 mt-8">
            © {{ date('Y') }} Bimothèque — Conçu au Sénégal 🇸🇳
        </p>

    </div>

</body>
</html>
