<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Bimothèque Immo') }} — @yield('title', 'Informations légales')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen">

    {{-- En-tête --}}
    <header class="border-b border-line bg-paper">
        <div class="max-w-[820px] mx-auto px-6 py-5 flex items-center justify-between">
            <a href="{{ url('/') }}" class="font-display font-semibold text-[18px] tracking-[0.01em] flex items-center gap-2.5 text-teal-deep">
                <span class="w-[26px] h-[26px] rounded-[6px] bg-gold text-teal-deep font-body font-bold text-[13px] flex items-center justify-center">B</span>
                {{ config('app.name', 'Bimothèque Immo') }}
            </a>
            <a href="{{ url('/') }}" class="text-[13px] text-muted hover:text-teal font-semibold">← Accueil</a>
        </div>
    </header>

    {{-- Contenu --}}
    <main class="max-w-[820px] mx-auto px-6 py-12">
        <h1 class="font-display font-semibold text-[30px] text-teal-deep mb-1.5">@yield('heading')</h1>
        <p class="text-[13px] text-muted mb-2">Dernière mise à jour : <span class="legal-todo">[À COMPLÉTER : date]</span></p>

        <div class="legal-prose mt-8">
            @yield('content')
        </div>
    </main>

    {{-- Pied de page --}}
    <footer class="border-t border-line mt-8">
        <div class="max-w-[820px] mx-auto px-6 py-6 flex flex-col sm:flex-row items-center justify-between gap-3 text-[12.5px] text-muted">
            <div>© {{ now()->year }} {{ config('app.name', 'Bimothèque Immo') }}. Tous droits réservés.</div>
            <nav class="flex items-center gap-4">
                <a href="{{ route('mentions-legales') }}" class="hover:text-teal font-semibold">Mentions légales</a>
                <a href="{{ route('confidentialite') }}" class="hover:text-teal font-semibold">Confidentialité</a>
                <a href="{{ route('login') }}" class="hover:text-teal font-semibold">Connexion</a>
            </nav>
        </div>
    </footer>

</body>
</html>
