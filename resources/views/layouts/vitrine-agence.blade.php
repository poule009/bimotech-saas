<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @php
        // Métas calculées une fois puis échappées via {{ }}. On N'injecte PAS de
        // données agence/bien dans le défaut brut de @yield (qui n'échappe pas) —
        // sinon un nom/slogan contenant du HTML deviendrait un XSS stocké public.
        $vMetaTitle = trim($__env->yieldContent('meta_title'))
            ?: ($agence->name . ' — Immobilier');
        $vMetaDesc  = trim($__env->yieldContent('meta_description'))
            ?: ($agence->slogan ?: ('Découvrez les biens disponibles de ' . $agence->name . ' : villas, appartements et terrains gérés directement par notre agence.'));
    @endphp

    <title>{{ $vMetaTitle }}</title>
    <meta name="description" content="{{ $vMetaDesc }}">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ $agence->name }}">
    <meta property="og:title" content="{{ $vMetaTitle }}">
    <meta property="og:description" content="{{ $vMetaDesc }}">
    @if($agence->logo_path)
        <meta property="og:image" content="{{ \Illuminate\Support\Facades\Storage::url($agence->logo_path) }}">
        <link rel="icon" href="{{ \Illuminate\Support\Facades\Storage::url($agence->logo_path) }}">
    @endif

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,500;0,9..144,600;1,9..144,400;1,9..144,500&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

    @vite(['resources/css/vitrine-agence.css', 'resources/js/vitrine-agence.js'])
</head>
<body>

@php
    $accueilUrl = route('vitrine.home', $agence->slug);
@endphp

{{-- ─────────────── NAV ─────────────── --}}
<nav class="v-nav">
    <div class="nav-inner">
        <a href="{{ $accueilUrl }}" class="brand">
            @if($agence->logo_path)
                <img class="brand-logo" src="{{ \Illuminate\Support\Facades\Storage::url($agence->logo_path) }}" alt="{{ $agence->name }}">
            @else
                <svg class="brand-mark" viewBox="0 0 32 32" fill="none"><circle cx="16" cy="16" r="12" stroke="#1B3A3F" stroke-width="2"/><rect x="22" y="13" width="3" height="6" rx="1" fill="#8A6420"/></svg>
            @endif
            <div>
                <div class="brand-name">{{ $agence->name }}</div>
                <div class="brand-sub">Immobilier</div>
            </div>
        </a>
        <div class="nav-links">
            <a href="{{ $accueilUrl }}#biens">Biens en vedette</a>
            <a href="{{ $accueilUrl }}#catalogue">Catalogue</a>
            <a href="{{ $accueilUrl }}#quartiers">Quartiers</a>
            <a href="{{ $accueilUrl }}#engagements">L'agence</a>
        </div>
        @if($whatsappUrl)
            <a class="nav-cta" href="{{ $whatsappUrl }}" target="_blank" rel="noopener">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12c0 1.85.5 3.58 1.35 5.07L2 22l5.07-1.32A9.94 9.94 0 0012 22c5.52 0 10-4.48 10-10S17.52 2 12 2z"/></svg>
                WhatsApp
            </a>
        @endif
    </div>
</nav>

@yield('content')

{{-- ─────────────── FOOTER ─────────────── --}}
<footer class="v-footer">
    <div class="wrap">
        <div class="footer-grid">
            <div>
                <div class="footer-brand">
                    @if($agence->logo_path)
                        <img class="brand-logo" src="{{ \Illuminate\Support\Facades\Storage::url($agence->logo_path) }}" alt="{{ $agence->name }}">
                    @endif
                    <div class="brand-name">{{ $agence->name }}</div>
                </div>
                <p class="footer-desc">{{ $agence->slogan ?: 'Villas, appartements et terrains gérés directement par notre équipe.' }}</p>
            </div>
            <div class="footer-col">
                <div class="footer-col-title">Nos biens</div>
                <a href="{{ $accueilUrl }}#biens">Biens en vedette</a>
                <a href="{{ $accueilUrl }}#catalogue">Catalogue complet</a>
                <a href="{{ $accueilUrl }}#quartiers">Par quartier</a>
            </div>
            <div class="footer-col">
                <div class="footer-col-title">L'agence</div>
                <a href="{{ $accueilUrl }}#types">Types de biens</a>
                <a href="{{ $accueilUrl }}#engagements">Nos engagements</a>
                @if($whatsappUrl)
                    <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener">Nous écrire sur WhatsApp</a>
                @endif
            </div>
            <div class="footer-col">
                <div class="footer-col-title">Contact</div>
                @if($agence->telephone)<p>{{ $agence->telephone }}</p>@endif
                @if($agence->email)<a href="mailto:{{ $agence->email }}">{{ $agence->email }}</a>@endif
                @if($agence->adresse)<p>{{ $agence->adresse }}</p>@endif
            </div>
        </div>
        <div class="footer-bottom">
            <div class="footer-legal">© {{ date('Y') }} {{ $agence->name }} — Tous droits réservés</div>
            <div class="footer-powered">Vitrine propulsée par <b>Bimmo</b></div>
        </div>
    </div>
</footer>

</body>
</html>
