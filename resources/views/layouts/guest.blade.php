<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Bimmo') }} — @yield('title', 'Espace agence')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Allura&family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex items-center justify-center p-8">

    <div class="stage w-full max-w-[1180px] min-h-[720px] bg-paper rounded-stage shadow-stage grid grid-cols-1 md:grid-cols-[0.85fr_1fr] overflow-hidden">

        {{-- ─────────────── Panneau de marque (gauche) ─────────────── --}}
        <aside class="brand-panel hidden md:flex flex-col justify-between text-paper px-12 py-[52px] relative">

            {{-- Wordmark A (signature) : émotion de marque sur l'écran de connexion. --}}
            <x-wordmark-signature class="text-[46px] leading-none" />


            <div class="max-w-[340px] mt-[60px]">
                <div class="text-xs tracking-[0.14em] uppercase text-gold-soft font-semibold mb-3.5">Gestion immobilière · Sénégal</div>
                <h1 class="font-display font-semibold text-[32px] leading-[1.22] mb-4">Chaque bien, chaque bail, chaque taxe — à sa place.</h1>
                <p class="text-[14.5px] leading-[1.6] text-[#D9E2DE]">La plateforme pensée pour les agences sénégalaises : TVA, BRS, NINEA et loi 81-18 gérés nativement, en français clair.</p>
            </div>

            <div class="relative h-[150px] mt-11" aria-hidden="true">
                <svg viewBox="0 0 340 150" fill="none" class="w-full h-full block">
                    <g stroke="rgb(var(--paper) / 0.14)" stroke-width="1">
                        <line x1="0" y1="30" x2="340" y2="30"/>
                        <line x1="0" y1="70" x2="340" y2="70"/>
                        <line x1="60" y1="0" x2="60" y2="150"/>
                        <line x1="150" y1="0" x2="150" y2="150"/>
                        <line x1="240" y1="0" x2="240" y2="150"/>
                    </g>
                    <g fill="rgb(var(--gold-soft))" opacity="0.9">
                        <rect x="18" y="86" width="26" height="54" rx="2"/>
                        <rect x="52" y="60" width="22" height="80" rx="2"/>
                        <rect x="82" y="100" width="30" height="40" rx="2"/>
                        <rect x="120" y="45" width="24" height="95" rx="2"/>
                        <rect x="152" y="78" width="34" height="62" rx="2"/>
                        <rect x="194" y="95" width="20" height="45" rx="2"/>
                        <rect x="222" y="55" width="26" height="85" rx="2"/>
                        <rect x="256" y="88" width="30" height="52" rx="2"/>
                        <rect x="294" y="70" width="24" height="70" rx="2"/>
                    </g>
                    <line x1="0" y1="140" x2="340" y2="140" stroke="rgb(var(--gold))" stroke-width="2"/>
                </svg>
            </div>

            <div class="flex gap-[22px] pt-5 border-t border-paper/15 text-[12.5px] text-[#B9C7C2]">
                <div><strong class="block font-display font-semibold text-[15px] text-paper">3</strong>modules fiscaux</div>
                <div><strong class="block font-display font-semibold text-[15px] text-paper">100%</strong>conforme loi 81-18</div>
                <div><strong class="block font-display font-semibold text-[15px] text-paper">FCFA</strong>tarifs locaux</div>
            </div>
        </aside>

        {{-- ─────────────── Panneau formulaire (droite) ─────────────── --}}
        <section class="flex flex-col px-7 py-10 md:px-[56px] md:py-[52px]">

            @if(request()->routeIs('login', 'agency.register'))
            <div class="flex bg-paper-dim rounded-full p-1 w-fit mb-10">
                <a href="{{ route('login') }}"
                   class="auth-tab {{ request()->routeIs('login') ? 'auth-tab-active' : '' }}">Connexion</a>
                <a href="{{ route('agency.register') }}"
                   class="auth-tab {{ request()->routeIs('agency.register') ? 'auth-tab-active' : '' }}">Créer un compte</a>
            </div>
            @endif

            @yield('content')

        </section>
    </div>

</body>
</html>
