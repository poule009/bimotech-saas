<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'bee') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
          media="print" onload="this.media='all'">
    <noscript>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap">
    </noscript>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-bimo-bg font-body antialiased flex items-center justify-center min-h-screen px-4 py-8">

    <div class="w-full max-w-md">

        {{-- Logo --}}
        <div class="text-center mb-8">
            <a href="{{ url('/') }}" class="inline-flex flex-col items-center gap-2">
                <x-bee-logo variant="navy" size="lg" />
            </a>
            <p class="font-body text-sm text-bimo-text/40 mt-1">Gestion immobilière professionnelle</p>
        </div>

        {{-- Contenu --}}
        {{ $slot }}

        {{-- Footer --}}
        <p class="text-center font-body text-xs text-bimo-text/30 mt-8">
            © {{ date('Y') }} bee — Conçu au Sénégal 🇸🇳
        </p>

    </div>

</body>
</html>
