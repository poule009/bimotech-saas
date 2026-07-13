<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- SEO / partage (WhatsApp & réseaux). TODO Malick : valider title/description définitifs. --}}
    <title>@yield('meta_title', 'Bimmo — Gérez votre agence immobilière, l\'esprit tranquille')</title>
    <meta name="description" content="@yield('meta_description', 'Logiciel de gestion locative pour les agences immobilières sénégalaises : biens, contrats, loyers, quittances et conformité fiscale (TVA, BRS, CGF, DGID) dans un seul outil.')">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Bimmo">
    <meta property="og:title" content="@yield('meta_title', 'Bimmo — Gérez votre agence immobilière, l\'esprit tranquille')">
    <meta property="og:description" content="@yield('meta_description', 'Biens, contrats, loyers et conformité fiscale sénégalaise dans un seul outil.')">
    <meta name="twitter:card" content="summary_large_image">
    {{-- TODO Malick : image de partage og:image (1200×630) une fois le visuel prêt. --}}

    {{-- Favicon (marque « reçu » sur carré ink) --}}
    <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Crect width='24' height='24' rx='5' fill='%2314282C'/%3E%3Cpath d='M6 6h12v3H6z' fill='none' stroke='%23F7F3EA' stroke-width='1.6' stroke-linejoin='round'/%3E%3Cpath d='M7 9v9a1 1 0 001 1h8a1 1 0 001-1V9' fill='none' stroke='%23F7F3EA' stroke-width='1.6'/%3E%3C/svg%3E">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    {{-- NB : wordmark A (signature/Allura) réservé à la connexion et — à venir — au hero.
         La vitrine (nav/footer, petits & répétés) utilise le wordmark B (Fraunces, déjà chargé).
         Recharger la famille Allura ici quand le hero signature sera créé. --}}
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300;0,9..144,400;0,9..144,500;0,9..144,600;0,9..144,700;1,9..144,400;1,9..144,500;1,9..144,600&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

    @vite(['resources/css/vitrine.css', 'resources/js/vitrine.js'])
</head>
<body>
@php
    // Liens réels de l'app. TODO Malick : numéro WhatsApp démo + email de contact.
    $inscriptionUrl = route('agency.register');
    $demoWhatsapp   = null; // TODO Malick : 'https://wa.me/221XXXXXXXXX' (démo) — à défaut on retombe sur l'inscription.
    $contactUrl     = null; // TODO Malick : email (mailto:) ou WhatsApp de contact.
    $demoUrl        = $demoWhatsapp ?? $inscriptionUrl;
    $mark = '<svg viewBox="0 0 24 24" fill="none"><path d="M4 4h16v4H4V4Z" stroke="#F7F3EA" stroke-width="1.8" stroke-linejoin="round"/><path d="M5 8v11a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V8" stroke="#F7F3EA" stroke-width="1.8"/></svg>';
@endphp

{{-- ─────────────── NAV ─────────────── --}}
<nav class="nav" id="nav">
    <div class="nav-inner">
        {{-- Wordmark B (fonctionnel) : brand répété sur chaque page, lisibilité prioritaire. --}}
        <a href="{{ route('home') }}" class="brand">
            <span class="mark">{!! $mark !!}</span> <x-wordmark />
        </a>
        <div class="nav-links">
            <a href="{{ route('home') }}#fonctionnalites">Fonctionnalités</a>
            <a href="{{ route('home') }}#conformite">Conformité</a>
            <a href="{{ route('tarifs') }}">Tarifs</a>
            <a href="{{ route('home') }}#faq">FAQ</a>
        </div>
        <div class="nav-cta">
            <a href="{{ $demoUrl }}" class="btn btn-ghost-ink btn-sm">Demander une démo</a>
            <a href="{{ $inscriptionUrl }}" class="btn btn-gold btn-sm">Essai gratuit</a>
        </div>
        <button class="nav-burger" aria-label="Menu" aria-expanded="false" aria-controls="mobile-menu">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none"><path d="M4 7h16M4 12h16M4 17h16" stroke="#14282C" stroke-width="1.8" stroke-linecap="round"/></svg>
        </button>
    </div>
</nav>

{{-- Menu mobile (déployé par le burger) --}}
<div class="mobile-menu" id="mobile-menu">
    <a class="m-link" href="{{ route('home') }}#fonctionnalites">Fonctionnalités</a>
    <a class="m-link" href="{{ route('home') }}#conformite">Conformité</a>
    <a class="m-link" href="{{ route('tarifs') }}">Tarifs</a>
    <a class="m-link" href="{{ route('home') }}#faq">FAQ</a>
    <div class="m-ctas">
        <a href="{{ $demoUrl }}" class="btn btn-ghost-ink">Demander une démo</a>
        <a href="{{ $inscriptionUrl }}" class="btn btn-gold">Essai gratuit</a>
    </div>
</div>

@yield('content')

{{-- ─────────────── FOOTER ─────────────── --}}
<footer>
    <div class="wrap">
        <div class="footer-grid">
            <div class="footer-brand">
                <a href="{{ route('home') }}" class="brand" style="color:var(--ink);">
                    <span class="mark">{!! $mark !!}</span> <x-wordmark />
                </a>
                <p>La plateforme de gestion immobilière professionnelle pour les agences sénégalaises.</p>
            </div>
            <div class="footer-col">
                <h5>Produit</h5>
                <a href="{{ route('home') }}#fonctionnalites">Fonctionnalités</a>
                <a href="{{ route('tarifs') }}">Tarifs</a>
                <a href="{{ route('home') }}#faq">FAQ</a>
                <a href="{{ $demoUrl }}">Demander une démo</a>
            </div>
            <div class="footer-col">
                <h5>Conformité</h5>
                <a href="{{ route('home') }}#conformite">TVA — CGI art. 357</a>
                <a href="{{ route('home') }}#conformite">Loi 81-18</a>
                <a href="{{ route('home') }}#conformite">Quittances légales</a>
                <a href="{{ route('home') }}#conformite">NINEA & DGID</a>
            </div>
            <div class="footer-col">
                <h5>Entreprise</h5>
                {{-- TODO Malick : lien de contact réel (email/WhatsApp). --}}
                <a href="{{ $contactUrl ?? $demoUrl }}">Contact</a>
                <a href="{{ route('mentions-legales') }}">Mentions légales</a>
                <a href="{{ route('confidentialite') }}">Confidentialité</a>
            </div>
        </div>
        <div class="footer-bottom">
            <span>© {{ date('Y') }} BIMO-tech — Dakar, Sénégal</span>
            <span>Bimmo</span>
        </div>
    </div>
</footer>

</body>
</html>
