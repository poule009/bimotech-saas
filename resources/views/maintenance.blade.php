<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance — {{ config('app.name', 'Bimmo') }}</title>
    <x-favicons />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-paper text-ink flex items-center justify-center p-6">
    <div class="max-w-[520px] w-full text-center">
        <div class="flex justify-center mb-8">
            <x-brand class="text-[26px]" />
        </div>

        <div class="bg-white border border-line rounded-2xl px-7 py-9 md:px-10 md:py-11">
            <div class="w-14 h-14 mx-auto mb-6 rounded-2xl bg-gold/12 text-gold flex items-center justify-center">
                <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14.7 6.3a4 4 0 0 0-5.4 5.4l-6 6a2 2 0 0 0 2.8 2.8l6-6a4 4 0 0 0 5.4-5.4l-2.9 2.9-2.1-2.1 2.9-2.9Z"/>
                </svg>
            </div>

            <h1 class="font-display font-semibold text-[24px] md:text-[27px] text-ink">Maintenance en cours</h1>
            <p class="text-[14.5px] text-muted leading-relaxed mt-4">
                {{ $message ?? 'La plateforme est momentanément en maintenance. L\'accès sera rétabli dans quelques instants.' }}
            </p>
        </div>

        <p class="text-[12.5px] text-muted/80 mt-6">
            Une question ? Contactez-nous à
            <a href="mailto:{{ app(\App\Support\PlatformSettings::class)->supportEmail() }}"
               class="font-semibold text-teal border-b border-gold/60">{{ app(\App\Support\PlatformSettings::class)->supportEmail() }}</a>.
        </p>
    </div>
</body>
</html>
