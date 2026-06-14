<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex,nofollow">
    <title>{{ auth()->user()?->agency?->name ?? config('app.name') }} — bee</title>

    {{-- PWA --}}
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#A60F1C">
    <link rel="apple-touch-icon" href="/icons/icon-192.png">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="bee">

    {{-- Polices --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
          media="print" onload="this.media='all'">
    <noscript>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap">
    </noscript>

    {{-- Tailwind + Alpine via Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Couleur agence injectée en runtime --}}
    @php
        $agencyColor = auth()->user()?->agency?->couleur_primaire ?? 'var(--ac)';
        $hex = ltrim($agencyColor, '#');
        $cr  = hexdec(substr($hex, 0, 2));
        $cg  = hexdec(substr($hex, 2, 2));
        $cb  = hexdec(substr($hex, 4, 2));
    @endphp
    <style>
        :root {
            --ac:   {{ $agencyColor }};
            --ac-r: {{ $cr }};
            --ac-g: {{ $cg }};
            --ac-b: {{ $cb }};
            /* Texte lisible sur --ac : noir ou blanc selon la luminance de la couleur agence */
            --ac-text: {{ (0.299 * $cr + 0.587 * $cg + 0.114 * $cb) > 140 ? '#111111' : '#FFFFFF' }};
        }
    </style>

    @stack('styles')
</head>

<body class="h-full bg-bimo-bg font-body antialiased">

{{-- ═══════════════════════════════════════════════
     SIDEBAR
═══════════════════════════════════════════════ --}}

{{-- Overlay mobile --}}
<div id="sidebar-overlay"
     onclick="closeSidebar()"
     class="fixed inset-0 bg-bimo-navy/50 z-30 lg:hidden"
     style="display:none">
</div>

{{-- Sidebar panel — photo de fond (Monument de la Renaissance, CC0) sous un
     dégradé rouge : le haut reste opaque pour la lisibilité du menu, le bas
     laisse transparaître le monument derrière le profil. --}}
<aside id="sidebar"
       class="fixed left-0 top-0 h-full w-64 bg-bimo-navy flex flex-col z-40
              -translate-x-full transition-transform duration-[250ms] ease-out
              lg:translate-x-0 bg-cover bg-center"
       style="background-image: linear-gradient(180deg, #A60F1C 0%, #A60F1C 30%, rgba(166,15,28,0.92) 55%, rgba(166,15,28,0.80) 78%, rgba(166,15,28,0.62) 100%), url('/images/sidebar-monument.jpg');">

    {{-- Logo + bouton fermer mobile --}}
    <div class="flex items-center gap-3 h-16 px-5 border-b border-white/10 flex-shrink-0">
        <div class="flex items-center gap-2.5 flex-1 min-w-0">
            {{-- Icône bee --}}
            <svg class="w-8 h-8 text-white flex-shrink-0" viewBox="0 0 120 110" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path d="M52 50 C56 28 84 18 90 34 C96 50 78 62 54 56 Z"/>
                <path d="M52 56 C58 36 86 28 90 46 C94 64 74 72 54 64 Z"/>
                <path d="M50 62 C56 46 78 44 80 58 C82 72 66 76 52 70 Z"/>
                <ellipse cx="38" cy="60" rx="14" ry="18" transform="rotate(-8 38 60)"/>
                <circle cx="24" cy="57" r="12"/>
                <circle cx="21" cy="54" r="4" fill="white"/>
                <line x1="18" y1="47" x2="8" y2="32" stroke="currentColor" stroke-width="4" stroke-linecap="round"/>
                <line x1="22" y1="46" x2="14" y2="28" stroke="currentColor" stroke-width="4" stroke-linecap="round"/>
                <line x1="28" y1="72" x2="16" y2="84" stroke="currentColor" stroke-width="3.5" stroke-linecap="round"/>
                <line x1="34" y1="76" x2="24" y2="90" stroke="currentColor" stroke-width="3.5" stroke-linecap="round"/>
                <line x1="40" y1="78" x2="32" y2="92" stroke="currentColor" stroke-width="3.5" stroke-linecap="round"/>
            </svg>
            <div class="min-w-0">
                <div class="font-display font-extrabold text-white text-lg leading-none">bee</div>
                <div class="font-body text-[10px] text-white/40 truncate mt-0.5">{{ auth()->user()?->agency?->name ?? 'Votre agence' }}</div>
            </div>
        </div>
        {{-- Bouton fermer — visible uniquement sur mobile --}}
        <button onclick="closeSidebar()" aria-label="Fermer le menu"
                class="lg:hidden w-8 h-8 flex items-center justify-center rounded-[8px]
                       text-white/40 hover:text-white hover:bg-white/10 transition-all duration-150 flex-shrink-0">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
        </button>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto">

        @php $route = request()->route()?->getName() ?? ''; @endphp

        @if(auth()->user()->isSuperAdmin())
        {{-- ══ NAV SUPERADMIN ══════════════════════════════════════════════ --}}

        <a href="{{ route('superadmin.dashboard') }}" onclick="closeSidebar()"
           class="flex items-center gap-3 px-3 py-2 rounded-[10px] transition-all duration-150 mb-1
                  {{ str_starts_with($route, 'superadmin.dashboard') ? 'bg-white/15 text-white' : 'text-white hover:bg-white/10' }}">
            <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
            <span class="font-display font-semibold text-sm">Tableau de bord</span>
        </a>

        <div class="font-body font-semibold text-[9.5px] uppercase tracking-[0.12em] text-white px-3 pt-4 pb-1">Plateforme</div>

        <a href="{{ route('superadmin.agencies.create') }}" onclick="closeSidebar()"
           class="flex items-center gap-3 px-3 py-2 rounded-[10px] transition-all duration-150
                  {{ str_starts_with($route, 'superadmin.agencies') ? 'bg-white/15 text-white' : 'text-white hover:bg-white/10' }}">
            <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            <span class="font-display font-semibold text-sm">Agences</span>
        </a>

        <a href="{{ route('superadmin.subscriptions') }}" onclick="closeSidebar()"
           class="flex items-center gap-3 px-3 py-2 rounded-[10px] transition-all duration-150
                  {{ str_starts_with($route, 'superadmin.subscriptions') ? 'bg-white/15 text-white' : 'text-white hover:bg-white/10' }}">
            <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
            <span class="font-display font-semibold text-sm">Abonnements</span>
        </a>

        <div class="font-body font-semibold text-[9.5px] uppercase tracking-[0.12em] text-white px-3 pt-4 pb-1">Journal</div>

        <a href="{{ route('superadmin.activity-logs.index') }}" onclick="closeSidebar()"
           class="flex items-center gap-3 px-3 py-2 rounded-[10px] transition-all duration-150
                  {{ str_starts_with($route, 'superadmin.activity-logs') ? 'bg-white/15 text-white' : 'text-white hover:bg-white/10' }}">
            <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="12" y1="17" x2="8" y2="17"/></svg>
            <span class="font-display font-semibold text-sm">Activité</span>
        </a>

        <div class="font-body font-semibold text-[9.5px] uppercase tracking-[0.12em] text-white px-3 pt-4 pb-1">Sécurité</div>

        <a href="{{ route('superadmin.2fa.setup') }}" onclick="closeSidebar()"
           class="flex items-center gap-3 px-3 py-2 rounded-[10px] transition-all duration-150
                  {{ str_starts_with($route, 'superadmin.2fa') ? 'bg-white/15 text-white' : 'text-white hover:bg-white/10' }}">
            <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
            <span class="font-display font-semibold text-sm">Authentification 2FA</span>
        </a>

        @else
        {{-- ══ NAV AGENCE ══════════════════════════════════════════════════ --}}

        @php
            $planNiveau     = auth()->user()?->agency?->subscription?->plan_niveau ?? 'starter';
            $niveauEffectif = config('plans.niveau_effectif')[$planNiveau] ?? 'starter';
            $hierarchy      = config('plans.hierarchy', ['starter','pro','agence']);
            $posActuelle    = array_search($niveauEffectif, $hierarchy);
            $_planFeatureSvc = app(\App\Services\PlanFeatureService::class);
            $canAccess = fn(string $feature) => $_planFeatureSvc->canAccess($feature);
            $planRequired = fn(string $feature) =>
                ($req = config("plans.features.{$feature}")) && $req !== 'starter'
                    ? config("plans.labels.{$req}", ucfirst($req))
                    : null;
        @endphp

        {{-- Tableau de bord --}}
        <a href="{{ route('admin.dashboard') }}" onclick="closeSidebar()"
           class="flex items-center gap-3 px-3 py-2 rounded-[10px] transition-all duration-150 mb-1
                  {{ str_starts_with($route, 'admin.dashboard') ? 'bg-white/15 text-white' : 'text-white hover:bg-white/10' }}">
            <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
            <span class="font-display font-semibold text-sm">Tableau de bord</span>
        </a>

        {{-- ── PATRIMOINE ── --}}
        <div class="font-body font-semibold text-[9.5px] uppercase tracking-[0.12em] text-white px-3 pt-4 pb-1">
            Patrimoine
        </div>
        <a href="{{ route('admin.users.proprietaires') }}" onclick="closeSidebar()"
           class="flex items-center gap-3 px-3 py-2 rounded-[10px] transition-all duration-150
                  {{ str_starts_with($route, 'admin.users.proprietaires') || str_starts_with($route, 'admin.bailleurs') ? 'bg-white/15 text-white' : 'text-white hover:bg-white/10' }}">
            <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            <span class="font-display font-semibold text-sm">Propriétaires</span>
        </a>
        <a href="{{ route('admin.biens.index') }}" onclick="closeSidebar()"
           class="flex items-center gap-3 px-3 py-2 rounded-[10px] transition-all duration-150
                  {{ str_starts_with($route, 'admin.biens') ? 'bg-white/15 text-white' : 'text-white hover:bg-white/10' }}">
            <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            <span class="font-display font-semibold text-sm">Biens</span>
        </a>
        <a href="{{ route('admin.immeubles.index') }}" onclick="closeSidebar()"
           class="flex items-center gap-3 px-3 py-2 rounded-[10px] transition-all duration-150
                  {{ str_starts_with($route, 'admin.immeubles') ? 'bg-white/15 text-white' : 'text-white hover:bg-white/10' }}">
            <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="2" y="7" width="20" height="15" rx="2"/><path d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2"/><line x1="12" y1="12" x2="12" y2="17"/><line x1="9.5" y1="14.5" x2="14.5" y2="14.5"/></svg>
            <span class="font-display font-semibold text-sm flex-1">Immeubles</span>
            @if($badge = $planRequired('immeubles'))
                @if($canAccess('immeubles'))
                    <span class="font-body font-semibold text-[9.5px] uppercase tracking-widest px-1.5 py-0.5 rounded-[4px] bg-white/10 text-white/30">{{ $badge }}</span>
                @else
                    <span class="font-body font-semibold text-[9.5px] uppercase tracking-widest px-1.5 py-0.5 rounded-[4px] bg-bimo-gold/20 text-bimo-gold flex items-center gap-1">
                        <svg class="w-2.5 h-2.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                        {{ $badge }}
                    </span>
                @endif
            @endif
        </a>

        {{-- ── RELATIONS ── --}}
        <div class="font-body font-semibold text-[9.5px] uppercase tracking-[0.12em] text-white px-3 pt-4 pb-1">
            Relations
        </div>
        <a href="{{ route('admin.users.locataires') }}" onclick="closeSidebar()"
           class="flex items-center gap-3 px-3 py-2 rounded-[10px] transition-all duration-150
                  {{ str_starts_with($route, 'admin.users.locataires') ? 'bg-white/15 text-white' : 'text-white hover:bg-white/10' }}">
            <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
            <span class="font-display font-semibold text-sm">Locataires</span>
        </a>
        <a href="{{ route('admin.contrats.index') }}" onclick="closeSidebar()"
           class="flex items-center gap-3 px-3 py-2 rounded-[10px] transition-all duration-150
                  {{ str_starts_with($route, 'admin.contrats') ? 'bg-white/15 text-white' : 'text-white hover:bg-white/10' }}">
            <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
            <span class="font-display font-semibold text-sm">Contrats</span>
        </a>

        {{-- ── CAISSE ── --}}
        <div class="font-body font-semibold text-[9.5px] uppercase tracking-[0.12em] text-white px-3 pt-4 pb-1">
            Caisse
        </div>
        <a href="{{ route('admin.paiements.index') }}" onclick="closeSidebar()"
           class="flex items-center gap-3 px-3 py-2 rounded-[10px] transition-all duration-150
                  {{ str_starts_with($route, 'admin.paiements') ? 'bg-white/15 text-white' : 'text-white hover:bg-white/10' }}">
            <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
            <span class="font-display font-semibold text-sm">Paiements & Quittances</span>
        </a>
        <a href="{{ route('admin.impayes.index') }}" onclick="closeSidebar()"
           class="flex items-center gap-3 px-3 py-2 rounded-[10px] transition-all duration-150
                  {{ str_starts_with($route, 'admin.impayes') ? 'bg-white/15 text-white' : 'text-white hover:bg-white/10' }}">
            <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            <span class="font-display font-semibold text-sm flex-1">Relances</span>
            @if(($navImpayesCount ?? 0) > 0)
            <span class="min-w-[18px] h-[18px] px-1 flex items-center justify-center rounded-full bg-bimo-red text-white font-display font-bold text-[10px] leading-none flex-shrink-0"
                  aria-label="{{ $navImpayesCount }} loyers impayés ce mois">
                {{ $navImpayesCount > 99 ? '99+' : $navImpayesCount }}
            </span>
            @endif
        </a>

        {{-- ── ANALYTIQUE ── --}}
        <div class="font-body font-semibold text-[9.5px] uppercase tracking-[0.12em] text-white px-3 pt-4 pb-1">
            Analytique
        </div>
        <a href="{{ route('admin.rapports.financier') }}" onclick="closeSidebar()"
           class="flex items-center gap-3 px-3 py-2 rounded-[10px] transition-all duration-150
                  {{ str_starts_with($route, 'admin.rapports') ? 'bg-white/15 text-white' : 'text-white hover:bg-white/10' }}">
            <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
            <span class="font-display font-semibold text-sm">Bilan mensuel</span>
        </a>

        {{-- ── COMPTABILITÉ ── --}}
        <div class="font-body font-semibold text-[9.5px] uppercase tracking-[0.12em] text-white px-3 pt-4 pb-1">
            Comptabilité
        </div>
        <a href="{{ route('admin.comptabilite.dashboard') }}" onclick="closeSidebar()"
           class="flex items-center gap-3 px-3 py-2 rounded-[10px] transition-all duration-150
                  {{ str_starts_with($route, 'admin.comptabilite') ? 'bg-white/15 text-white' : 'text-white hover:bg-white/10' }}">
            <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            <span class="font-display font-semibold text-sm flex-1">Comptabilité</span>
            @if($badge = $planRequired('comptabilite'))
                @if($canAccess('comptabilite'))
                    <span class="font-body font-semibold text-[9.5px] uppercase tracking-widest px-1.5 py-0.5 rounded-[4px] bg-white/10 text-white/30">{{ $badge }}</span>
                @else
                    <span class="font-body font-semibold text-[9.5px] uppercase tracking-widest px-1.5 py-0.5 rounded-[4px] bg-bimo-gold/20 text-bimo-gold flex items-center gap-1">
                        <svg class="w-2.5 h-2.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                        {{ $badge }}
                    </span>
                @endif
            @endif
        </a>
        <a href="{{ route('admin.charges-agence.index') }}" onclick="closeSidebar()"
           class="flex items-center gap-3 px-3 py-2 rounded-[10px] transition-all duration-150
                  {{ str_starts_with($route, 'admin.charges-agence') ? 'bg-white/15 text-white' : 'text-white hover:bg-white/10' }}">
            <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            <span class="font-display font-semibold text-sm flex-1">Charges agence</span>
            @if($badge = $planRequired('comptabilite'))
                @if(!$canAccess('comptabilite'))
                    <span class="font-body font-semibold text-[9.5px] uppercase tracking-widest px-1.5 py-0.5 rounded-[4px] bg-bimo-gold/20 text-bimo-gold flex items-center gap-1">
                        <svg class="w-2.5 h-2.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                        {{ $badge }}
                    </span>
                @endif
            @endif
        </a>
        <a href="{{ route('admin.reversements.index') }}" onclick="closeSidebar()"
           class="flex items-center gap-3 px-3 py-2 rounded-[10px] transition-all duration-150
                  {{ str_starts_with($route, 'admin.reversements') ? 'bg-white/15 text-white' : 'text-white hover:bg-white/10' }}">
            <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><polyline points="17 1 21 5 17 9"/><path d="M3 11V9a4 4 0 0 1 4-4h14"/><polyline points="7 23 3 19 7 15"/><path d="M21 13v2a4 4 0 0 1-4 4H3"/></svg>
            <span class="font-display font-semibold text-sm flex-1">Reversements</span>
            @if($badge = $planRequired('comptabilite'))
                @if(!$canAccess('comptabilite'))
                    <span class="font-body font-semibold text-[9.5px] uppercase tracking-widest px-1.5 py-0.5 rounded-[4px] bg-bimo-gold/20 text-bimo-gold flex items-center gap-1">
                        <svg class="w-2.5 h-2.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                        {{ $badge }}
                    </span>
                @endif
            @endif
        </a>
        <a href="{{ route('admin.tresorerie.index') }}" onclick="closeSidebar()"
           class="flex items-center gap-3 px-3 py-2 rounded-[10px] transition-all duration-150
                  {{ str_starts_with($route, 'admin.tresorerie') ? 'bg-white/15 text-white' : 'text-white hover:bg-white/10' }}">
            <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg>
            <span class="font-display font-semibold text-sm flex-1">Trésorerie</span>
            @if($badge = $planRequired('tresorerie'))
                @if($canAccess('tresorerie'))
                    <span class="font-body font-semibold text-[9.5px] uppercase tracking-widest px-1.5 py-0.5 rounded-[4px] bg-white/10 text-white/30">{{ $badge }}</span>
                @else
                    <span class="font-body font-semibold text-[9.5px] uppercase tracking-widest px-1.5 py-0.5 rounded-[4px] bg-bimo-gold/20 text-bimo-gold flex items-center gap-1">
                        <svg class="w-2.5 h-2.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                        {{ $badge }}
                    </span>
                @endif
            @endif
        </a>

        {{-- ── FISCALITÉ ── (masqué si FEATURE_FISCALITE=false) --}}
        @if(config('features.fiscalite'))
        <div class="font-body font-semibold text-[9.5px] uppercase tracking-[0.12em] text-white px-3 pt-4 pb-1">
            Fiscalité
        </div>
        <a href="{{ route('admin.fiscal.dashboard') }}" onclick="closeSidebar()"
           class="flex items-center gap-3 px-3 py-2 rounded-[10px] transition-all duration-150
                  {{ str_starts_with($route, 'admin.fiscal') ? 'bg-white/15 text-white' : 'text-white hover:bg-white/10' }}">
            <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            <span class="font-display font-semibold text-sm">Vue d'ensemble</span>
        </a>
        <a href="{{ route('admin.bilans-fiscaux.index') }}" onclick="closeSidebar()"
           class="flex items-center gap-3 px-3 py-2 rounded-[10px] transition-all duration-150
                  {{ str_starts_with($route, 'admin.bilans-fiscaux') ? 'bg-white/15 text-white' : 'text-white hover:bg-white/10' }}">
            <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="9" y1="15" x2="15" y2="15"/><line x1="12" y1="12" x2="12" y2="18"/></svg>
            <span class="font-display font-semibold text-sm">Bilans IRPP</span>
        </a>
        <a href="{{ route('admin.tva-agence.index') }}" onclick="closeSidebar()"
           class="flex items-center gap-3 px-3 py-2 rounded-[10px] transition-all duration-150
                  {{ str_starts_with($route, 'admin.tva-agence') ? 'bg-white/15 text-white' : 'text-white hover:bg-white/10' }}">
            <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
            <span class="font-display font-semibold text-sm">TVA mensuelle</span>
        </a>
        <a href="{{ route('admin.etats-trimestriels.index') }}" onclick="closeSidebar()"
           class="flex items-center gap-3 px-3 py-2 rounded-[10px] transition-all duration-150
                  {{ str_starts_with($route, 'admin.etats-trimestriels') ? 'bg-white/15 text-white' : 'text-white hover:bg-white/10' }}">
            <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
            <span class="font-display font-semibold text-sm">États BRS</span>
        </a>
        <a href="{{ route('admin.echeances-fiscales.index') }}" onclick="closeSidebar()"
           class="flex items-center gap-3 px-3 py-2 rounded-[10px] transition-all duration-150
                  {{ str_starts_with($route, 'admin.echeances-fiscales') ? 'bg-white/15 text-white' : 'text-white hover:bg-white/10' }}">
            <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            <span class="font-display font-semibold text-sm">Échéances</span>
        </a>
        <a href="{{ route('admin.fiscal.simulation') }}" onclick="closeSidebar()"
           class="flex items-center gap-3 px-3 py-2 rounded-[10px] transition-all duration-150
                  {{ str_starts_with($route, 'admin.fiscal.simulation') ? 'bg-white/15 text-white' : 'text-white hover:bg-white/10' }}">
            <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
            <span class="font-display font-semibold text-sm">Simulation</span>
        </a>
        @endif

        {{-- ── JOURNAL ── --}}
        <div class="font-body font-semibold text-[9.5px] uppercase tracking-[0.12em] text-white px-3 pt-4 pb-1">
            Journal
        </div>
        <a href="{{ route('admin.activity-logs.index') }}" onclick="closeSidebar()"
           class="flex items-center gap-3 px-3 py-2 rounded-[10px] transition-all duration-150
                  {{ str_starts_with($route, 'admin.activity-logs') ? 'bg-white/15 text-white' : 'text-white hover:bg-white/10' }}">
            <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="12" y1="17" x2="8" y2="17"/></svg>
            <span class="font-display font-semibold text-sm flex-1">Activité</span>
            @if($badge = $planRequired('logs_activite'))
                @if($canAccess('logs_activite'))
                    <span class="font-body font-semibold text-[9.5px] uppercase tracking-widest px-1.5 py-0.5 rounded-[4px] bg-white/10 text-white/30">{{ $badge }}</span>
                @else
                    <span class="font-body font-semibold text-[9.5px] uppercase tracking-widest px-1.5 py-0.5 rounded-[4px] bg-bimo-gold/20 text-bimo-gold flex items-center gap-1">
                        <svg class="w-2.5 h-2.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                        {{ $badge }}
                    </span>
                @endif
            @endif
        </a>

        {{-- ── AGENCE ── --}}
        <div class="font-body font-semibold text-[9.5px] uppercase tracking-[0.12em] text-white px-3 pt-4 pb-1">
            Agence
        </div>
        <a href="{{ route('admin.agency.settings') }}" onclick="closeSidebar()"
           class="flex items-center gap-3 px-3 py-2 rounded-[10px] transition-all duration-150
                  {{ str_starts_with($route, 'admin.agency') ? 'bg-white/15 text-white' : 'text-white hover:bg-white/10' }}">
            <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06-.06a2 2 0 01-2.83 0 2 2 0 010-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 010-2.83 2 2 0 012.83 0l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 0 2 2 0 010 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z"/></svg>
            <span class="font-display font-semibold text-sm">Paramètres</span>
        </a>
        <a href="{{ route('subscription.index') }}" onclick="closeSidebar()"
           class="flex items-center gap-3 px-3 py-2 rounded-[10px] transition-all duration-150
                  {{ str_starts_with($route, 'subscription') ? 'bg-white/15 text-white' : 'text-white hover:bg-white/10' }}">
            <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
            <span class="font-display font-semibold text-sm">Abonnement</span>
        </a>
        <a href="{{ route('admin.import.index') }}" onclick="closeSidebar()"
           class="flex items-center gap-3 px-3 py-2 rounded-[10px] transition-all duration-150
                  {{ str_starts_with($route, 'admin.import') ? 'bg-white/15 text-white' : 'text-white hover:bg-white/10' }}">
            <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
            <span class="font-display font-semibold text-sm flex-1">Import Excel</span>
            @if($badge = $planRequired('import_excel'))
                @if($canAccess('import_excel'))
                    <span class="font-body font-semibold text-[9.5px] uppercase tracking-widest px-1.5 py-0.5 rounded-[4px] bg-white/10 text-white/30">{{ $badge }}</span>
                @else
                    <span class="font-body font-semibold text-[9.5px] uppercase tracking-widest px-1.5 py-0.5 rounded-[4px] bg-bimo-gold/20 text-bimo-gold flex items-center gap-1">
                        <svg class="w-2.5 h-2.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                        {{ $badge }}
                    </span>
                @endif
            @endif
        </a>
        @if(auth()->user()->isOwner())
        <a href="{{ route('admin.equipe.index') }}" onclick="closeSidebar()"
           class="flex items-center gap-3 px-3 py-2 rounded-[10px] transition-all duration-150
                  {{ str_starts_with($route, 'admin.equipe') ? 'bg-white/15 text-white' : 'text-white hover:bg-white/10' }}">
            <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
            <span class="font-display font-semibold text-sm">Mon équipe</span>
        </a>
        @endif

        @endif {{-- /isSuperAdmin --}}

        <div class="pb-2"></div>

    </nav>

    {{-- Footer sidebar — profil avec dropdown --}}
    <div class="flex-shrink-0 p-3 border-t border-white/10">
        <div class="relative">

            {{-- Bouton profil --}}
            <button onclick="toggleProfileDrop()"
                    class="flex items-center gap-3 w-full px-3 py-2.5 rounded-[10px] hover:bg-white/5 transition-colors duration-150">
                <div class="w-8 h-8 rounded-[8px] flex items-center justify-center flex-shrink-0 font-display font-bold text-sm"
                     style="background: var(--ac); color: var(--ac-text)">
                    {{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 2)) }}
                </div>
                <div class="flex-1 text-left min-w-0">
                    <div class="font-display font-semibold text-white text-sm truncate">{{ auth()->user()->name }}</div>
                    <div class="font-body text-[10px] text-white/40 capitalize">{{ auth()->user()->role }}</div>
                </div>
                <svg id="profile-chevron" class="w-4 h-4 text-white/30 flex-shrink-0 transition-transform duration-150"
                     viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="6 9 12 15 18 9"/>
                </svg>
            </button>

            {{-- Dropdown (s'ouvre vers le haut) --}}
            <div id="profile-drop"
                 class="absolute bottom-full left-0 right-0 mb-2 bg-bimo-navy-dk border border-white/10 rounded-[12px] overflow-hidden shadow-xl z-50"
                 style="display:none">
                <a href="{{ route('profile.edit') }}" onclick="closeSidebar()"
                   class="flex items-center gap-3 px-4 py-3 text-white/70 hover:text-white hover:bg-white/5 transition-colors duration-150 font-body text-sm">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    Mon profil
                </a>
                @if(!auth()->user()->isSuperAdmin())
                <a href="{{ route('admin.agency.settings') }}" onclick="closeSidebar()"
                   class="flex items-center gap-3 px-4 py-3 text-white/70 hover:text-white hover:bg-white/5 transition-colors duration-150 font-body text-sm border-t border-white/10">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83 0 2 2 0 010-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 010-2.83 2 2 0 012.83 0l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 0 2 2 0 010 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z"/></svg>
                    Paramètres agence
                </a>
                @endif
                <form method="POST" action="{{ route('logout') }}" class="border-t border-white/10">
                    @csrf
                    <button type="submit"
                            class="flex items-center gap-3 w-full px-4 py-3 text-bimo-red/80 hover:text-bimo-red hover:bg-white/5 transition-colors duration-150 font-body text-sm">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                        Déconnexion
                    </button>
                </form>
            </div>
        </div>
    </div>
</aside>

{{-- ═══════════════════════════════════════════════
     CONTENU PRINCIPAL
═══════════════════════════════════════════════ --}}
<div class="lg:ml-64 flex flex-col min-h-full">

    {{-- TOPBAR MOBILE --}}
    <header x-data="mobileSearch" data-search-url="{{ route('admin.search') }}"
            class="sticky top-0 z-20 flex items-center justify-between h-14 px-4 bg-bimo-navy lg:hidden">
        {{-- Logo --}}
        <x-bee-logo variant="white" size="sm" />

        <div class="flex items-center gap-2">
            @auth
            @if(in_array(auth()->user()->role, ['admin', 'superadmin']))
            {{-- Déclencheur recherche --}}
            <button type="button" @click="openPanel"
                    aria-label="Rechercher"
                    class="w-10 h-10 rounded-[9px] flex items-center justify-center text-white/70 hover:text-white hover:bg-white/10 transition-colors duration-150">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            </button>
            @endif
            @endauth

            {{-- Avatar --}}
            <div class="w-9 h-9 rounded-[9px] flex items-center justify-center font-display font-bold text-sm flex-shrink-0"
                 style="background: var(--ac); color: var(--ac-text)">
                {{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 2)) }}
            </div>
        </div>

        {{-- Overlay recherche plein écran (mobile) --}}
        <div x-show="searchOpen" @keydown.escape.window="close"
             class="fixed inset-0 z-50 bg-bimo-bg flex flex-col" style="display:none">
            <div class="flex items-center gap-2 h-14 px-3 bg-bimo-navy flex-shrink-0">
                <button type="button" @click="close" aria-label="Fermer la recherche"
                        class="w-10 h-10 rounded-[9px] flex items-center justify-center text-white/70 hover:text-white hover:bg-white/10 flex-shrink-0">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                </button>
                <input type="text" x-ref="mSearch" x-model="q"
                       aria-label="Rechercher un bien, un locataire ou un contrat"
                       @input="search"
                       placeholder="Rechercher un bien, locataire, contrat…"
                       class="flex-1 min-w-0 bg-white/10 text-white placeholder:text-white/40 rounded-[9px] px-4 h-11 font-body text-sm focus:outline-none focus:bg-white/15">
            </div>
            <div class="flex-1 overflow-y-auto">
                <div x-show="loading" class="px-4 py-6 text-center font-body text-sm text-bimo-text/40">Recherche…</div>
                <template x-for="item in results" :key="item.url">
                    <a :href="item.url" class="flex items-center gap-3 px-4 py-4 bg-white border-b border-bimo-navy/[6%] active:bg-bimo-bg2">
                        <div class="min-w-0 flex-1">
                            <div class="font-body font-medium text-sm text-bimo-text truncate" x-text="item.label"></div>
                            <div class="font-body text-xs text-bimo-text/40 truncate" x-text="item.sub"></div>
                        </div>
                        <svg class="w-4 h-4 text-bimo-text/30 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                    </a>
                </template>
                <div x-show="noResults" class="px-4 py-10 text-center font-body text-sm text-bimo-text/40">
                    Aucun résultat pour « <span x-text="q"></span> ».
                </div>
            </div>
        </div>
    </header>

    {{-- TOPBAR DESKTOP --}}
    <header class="hidden lg:flex items-center justify-between h-14 px-8 bg-bimo-surface border-b border-bimo-navy/10 sticky top-0 z-20">
        {{-- Breadcrumb --}}
        <div class="flex items-center gap-2 font-body text-sm text-bimo-text/50">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-bimo-text transition-colors duration-150">
                {{ auth()->user()?->agency?->name ?? 'bee' }}
            </a>
            <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            <span class="text-bimo-text font-medium">{{ $header ?? 'Tableau de bord' }}</span>
        </div>

        {{-- Actions topbar droite --}}
        <div class="flex items-center gap-3">
            {{ $topbarActions ?? '' }}

            {{-- Recherche globale (admin uniquement) --}}
            @auth
            @if(in_array(auth()->user()->role, ['admin', 'superadmin']))
            <div class="relative" x-data="desktopSearch" data-search-url="{{ route('admin.search') }}"
                 @click.outside="hideKeepQuery">
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-bimo-text/30 pointer-events-none"
                         viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    <input type="text" x-model="q"
                           aria-label="Rechercher un bien, un locataire ou un contrat"
                           @input="search"
                           @keydown.escape="hide"
                           placeholder="Rechercher…"
                           class="w-48 pl-8 pr-3 py-1.5 bg-bimo-bg border border-bimo-navy/10 rounded-[8px] font-body text-sm text-bimo-text placeholder:text-bimo-text/30 focus:outline-none focus:border-bimo-gold focus:w-64 focus:bg-white transition-all duration-150">
                </div>
                <div x-show="hasResults"
                     class="absolute right-0 top-full mt-1 w-80 bg-white border border-bimo-navy/10 rounded-[12px] shadow-lg overflow-hidden z-50"
                     style="display:none">
                    <template x-for="item in results" :key="item.url">
                        <a :href="item.url"
                           class="flex items-center gap-3 px-4 py-2.5 hover:bg-bimo-bg transition-colors duration-100 border-b border-bimo-navy/5 last:border-0">
                            <div class="w-7 h-7 rounded-[6px] bg-bimo-bg2 flex items-center justify-center flex-shrink-0">
                                <svg class="w-3.5 h-3.5 text-bimo-text/40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                                </svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="font-body font-medium text-sm text-bimo-text truncate" x-text="item.label"></div>
                                <div class="font-body text-[11px] text-bimo-text/40 truncate" x-text="item.sub"></div>
                            </div>
                        </a>
                    </template>
                </div>
            </div>
            @endif
            @endauth
        </div>
    </header>

    {{-- Bannière 2FA superadmin --}}
    @auth
    @if(auth()->user()->isSuperAdmin() && !auth()->user()->hasTwoFactorEnabled())
    <div class="flex items-center justify-between gap-3 px-4 lg:px-8 py-2.5 bg-amber-50 border-b border-amber-200 flex-wrap">
        <span class="flex items-center gap-2 font-body text-xs text-amber-800">
            <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            Sécurisez votre compte superadmin en activant l'authentification à deux facteurs.
        </span>
        <a href="{{ route('superadmin.2fa.setup') }}"
           class="font-display font-bold text-xs px-3 py-1.5 bg-amber-600 text-white rounded-[6px] hover:bg-amber-700 transition-colors duration-150 whitespace-nowrap">
            Activer le 2FA →
        </a>
    </div>
    @endif
    @endauth

    {{-- Bannière impersonation --}}
    @if(session('impersonating_id'))
    <div class="flex items-center justify-between gap-3 px-4 lg:px-8 py-2.5 bg-bimo-red border-b border-bimo-red/80 flex-wrap">
        <span class="flex items-center gap-2 font-body text-xs text-white">
            <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            Impersonation active — connecté en tant que <strong class="mx-1">{{ auth()->user()->name }}</strong> ({{ auth()->user()->role }})
        </span>
        <a href="{{ route('superadmin.impersonate.stop') }}"
           class="font-display font-bold text-xs px-3 py-1.5 bg-white/20 text-white border border-white/30 rounded-[6px] hover:bg-white/30 transition-colors duration-150 whitespace-nowrap">
            ← Quitter l'impersonation
        </a>
    </div>
    @endif

    {{-- CONTENU PAGE --}}
    <main class="flex-1 px-4 py-4 md:px-6 md:py-6 lg:px-8 lg:py-8 pb-24 lg:pb-8">

        {{-- Flash success --}}
        @if(session('success'))
        <div x-data="flashMessage" data-timeout="5000" x-show="show"
             x-transition:leave="transition duration-300"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="flex items-center gap-3 bg-bimo-gold/[8%] border border-bimo-gold/25 rounded-[12px] px-4 py-3 mb-4">
            <svg class="w-4 h-4 text-bimo-gold flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
            <p class="font-body text-sm text-bimo-gold flex-1">{{ session('success') }}</p>
            <button @click="dismiss" aria-label="Fermer le message" class="text-bimo-gold/50 hover:text-bimo-gold transition-colors duration-150">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        @endif

        {{-- Flash warning --}}
        @if(session('warning'))
        <div x-data="flashMessage" data-timeout="7000" x-show="show"
             x-transition:leave="transition duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="flex items-center gap-3 bg-amber-50 border border-amber-200 rounded-[12px] px-4 py-3 mb-4">
            <svg class="w-4 h-4 text-amber-600 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/></svg>
            <p class="font-body text-sm text-amber-800 flex-1">{{ session('warning') }}</p>
            <button @click="dismiss" aria-label="Fermer le message" class="text-amber-400 hover:text-amber-600 transition-colors duration-150">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        @endif

        {{-- Flash error --}}
        @if(session('error'))
        <div x-data="flashMessage" data-timeout="12000" x-show="show"
             x-transition:leave="transition duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="flex items-center gap-3 bg-bimo-red/[5%] border border-bimo-red/20 rounded-[12px] px-4 py-3 mb-4">
            <svg class="w-4 h-4 text-bimo-red flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
            <p class="font-body text-sm text-bimo-red flex-1">{{ session('error') }}</p>
            <button @click="dismiss" aria-label="Fermer le message" class="text-bimo-red/50 hover:text-bimo-red transition-colors duration-150">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        @endif

        {{-- Flash errors validation --}}
        @if($errors->any())
        <div x-data="flashMessage" x-show="show"
             x-transition:leave="transition duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="flex items-start gap-3 bg-bimo-red/[5%] border border-bimo-red/20 rounded-[12px] px-4 py-3 mb-4">
            <svg class="w-4 h-4 text-bimo-red flex-shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <div class="flex-1">
                <p class="font-body font-medium text-sm text-bimo-red mb-1">Veuillez corriger les erreurs suivantes :</p>
                @foreach($errors->all() as $error)
                <p class="font-body text-xs text-bimo-red/80">• {{ $error }}</p>
                @endforeach
            </div>
            <button @click="dismiss" aria-label="Fermer le message" class="text-bimo-red/50 hover:text-bimo-red transition-colors duration-150 flex-shrink-0">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        @endif

        {{-- Contenu de la vue --}}
        {{ $slot ?? '' }}
        @yield('content')

    </main>

    {{-- FOOTER — desktop uniquement --}}
    <footer class="hidden lg:block border-t border-bimo-navy/10 bg-white px-8 py-4">
        <div class="flex items-center justify-between gap-4">
            <span class="font-body text-xs text-bimo-text/40">© {{ date('Y') }} bee. Tous droits réservés.</span>
            <a href="{{ route('admin.dashboard') }}" class="hover:opacity-70 transition-opacity duration-150">
                <x-bee-logo variant="navy" size="sm" />
            </a>
            <span class="font-body text-xs text-bimo-text/40 text-right">Conçu pour simplifier la gestion locative en Afrique.</span>
        </div>
    </footer>

    {{-- FAB — Enregistrer un paiement (mobile, staff autorisé) --}}
    @auth
    @if(auth()->user()->hasAgencyPermission('paiements.creer') && !request()->routeIs('admin.paiements.create', 'admin.paiements.store'))
    <a href="{{ route('admin.paiements.create') }}"
       aria-label="Enregistrer un paiement"
       class="lg:hidden fixed right-4 z-30 w-14 h-14 rounded-[18px] bg-[var(--ac)] text-bimo-navy flex items-center justify-center shadow-[0_8px_24px_rgba(0,0,0,0.28)] active:scale-95 transition-transform duration-150"
       style="bottom: calc(56px + env(safe-area-inset-bottom, 0px) + 14px)">
        <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
    </a>
    @endif
    @endauth

    {{-- BOTTOM NAV MOBILE --}}
    @php $routeNow = request()->route()?->getName() ?? ''; @endphp

    @if(auth()->user()->isSuperAdmin())
    {{-- Bottom nav superadmin --}}
    <nav class="fixed bottom-0 left-0 right-0 bg-bimo-navy border-t border-white/10 z-20 lg:hidden flex items-stretch"
         style="height: calc(56px + env(safe-area-inset-bottom, 0px)); padding-bottom: env(safe-area-inset-bottom, 0px)">

        {{-- Dashboard --}}
        <a href="{{ route('superadmin.dashboard') }}"
           class="flex flex-col items-center justify-center gap-0.5 flex-1 py-2 transition-colors duration-150
                  {{ str_starts_with($routeNow, 'superadmin.dashboard') ? 'text-white' : 'text-white/40 hover:text-white/70' }}">
            <svg class="w-[22px] h-[22px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
                <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
                <rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
            </svg>
            <span class="font-display font-semibold text-[9.5px] uppercase tracking-widest leading-none">Accueil</span>
        </a>

        {{-- Agences --}}
        <a href="{{ route('superadmin.agencies.create') }}"
           class="flex flex-col items-center justify-center gap-0.5 flex-1 py-2 transition-colors duration-150
                  {{ str_starts_with($routeNow, 'superadmin.agencies') ? 'text-white' : 'text-white/40 hover:text-white/70' }}">
            <svg class="w-[22px] h-[22px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
                <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                <polyline points="9 22 9 12 15 12 15 22"/>
            </svg>
            <span class="font-display font-semibold text-[9.5px] uppercase tracking-widest leading-none">Agences</span>
        </a>

        {{-- Abonnements --}}
        <a href="{{ route('superadmin.subscriptions') }}"
           class="flex flex-col items-center justify-center gap-0.5 flex-1 py-2 transition-colors duration-150
                  {{ str_starts_with($routeNow, 'superadmin.subscriptions') ? 'text-white' : 'text-white/40 hover:text-white/70' }}">
            <svg class="w-[22px] h-[22px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
                <rect x="1" y="4" width="22" height="16" rx="2"/>
                <line x1="1" y1="10" x2="23" y2="10"/>
            </svg>
            <span class="font-display font-semibold text-[9.5px] uppercase tracking-widest leading-none">Abonnem.</span>
        </a>

        {{-- Activité --}}
        <a href="{{ route('superadmin.activity-logs.index') }}"
           class="flex flex-col items-center justify-center gap-0.5 flex-1 py-2 transition-colors duration-150
                  {{ str_starts_with($routeNow, 'superadmin.activity-logs') ? 'text-white' : 'text-white/40 hover:text-white/70' }}">
            <svg class="w-[22px] h-[22px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
                <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                <polyline points="14 2 14 8 20 8"/>
                <line x1="16" y1="13" x2="8" y2="13"/><line x1="12" y1="17" x2="8" y2="17"/>
            </svg>
            <span class="font-display font-semibold text-[9.5px] uppercase tracking-widest leading-none">Activité</span>
        </a>

        {{-- Menu — ouvre la sidebar --}}
        <button onclick="openSidebar()"
                class="flex flex-col items-center justify-center gap-0.5 flex-1 py-2 transition-colors duration-150 border-none bg-transparent
                       text-white/40 hover:text-white/70">
            <svg class="w-[22px] h-[22px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
                <line x1="3" y1="6" x2="21" y2="6"/>
                <line x1="3" y1="12" x2="21" y2="12"/>
                <line x1="3" y1="18" x2="21" y2="18"/>
            </svg>
            <span class="font-display font-semibold text-[9.5px] uppercase tracking-widest leading-none">Menu</span>
        </button>

    </nav>
    @else
    {{-- Bottom nav agence --}}
    <nav class="fixed bottom-0 left-0 right-0 bg-bimo-navy border-t border-white/10 z-20 lg:hidden flex items-stretch"
         style="height: calc(56px + env(safe-area-inset-bottom, 0px)); padding-bottom: env(safe-area-inset-bottom, 0px)">

        {{-- Dashboard --}}
        <a href="{{ route('admin.dashboard') }}"
           class="flex flex-col items-center justify-center gap-0.5 flex-1 py-2 transition-colors duration-150
                  {{ str_starts_with($routeNow, 'admin.dashboard') ? 'text-white' : 'text-white/40 hover:text-white/70' }}">
            <svg class="w-[22px] h-[22px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
                <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
                <rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
            </svg>
            <span class="font-display font-semibold text-[9.5px] uppercase tracking-widest leading-none">Accueil</span>
        </a>

        {{-- Biens --}}
        <a href="{{ route('admin.biens.index') }}"
           class="flex flex-col items-center justify-center gap-0.5 flex-1 py-2 transition-colors duration-150
                  {{ str_starts_with($routeNow, 'admin.biens') || str_starts_with($routeNow, 'admin.contrats') ? 'text-white' : 'text-white/40 hover:text-white/70' }}">
            <svg class="w-[22px] h-[22px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
                <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                <polyline points="9 22 9 12 15 12 15 22"/>
            </svg>
            <span class="font-display font-semibold text-[9.5px] uppercase tracking-widest leading-none">Biens</span>
        </a>

        {{-- Paiements --}}
        <a href="{{ route('admin.paiements.index') }}"
           class="flex flex-col items-center justify-center gap-0.5 flex-1 py-2 transition-colors duration-150
                  {{ str_starts_with($routeNow, 'admin.paiements') ? 'text-white' : 'text-white/40 hover:text-white/70' }}">
            <svg class="w-[22px] h-[22px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
                <rect x="1" y="4" width="22" height="16" rx="2"/>
                <line x1="1" y1="10" x2="23" y2="10"/>
            </svg>
            <span class="font-display font-semibold text-[9.5px] uppercase tracking-widest leading-none">Paiements</span>
        </a>

        {{-- Impayés --}}
        <a href="{{ route('admin.impayes.index') }}"
           class="flex flex-col items-center justify-center gap-0.5 flex-1 py-2 transition-colors duration-150 relative
                  {{ str_starts_with($routeNow, 'admin.impayes') ? 'text-bimo-red' : 'text-white/40 hover:text-white/70' }}">
            <div class="relative">
                <svg class="w-[22px] h-[22px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
                    <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
                </svg>
                @if(($navImpayesCount ?? 0) > 0)
                <span class="absolute -top-1.5 -right-2 min-w-[16px] h-4 px-1 flex items-center justify-center rounded-full bg-bimo-red text-white font-display font-bold text-[9.5px] leading-none ring-2 ring-bimo-navy"
                      aria-label="{{ $navImpayesCount }} loyers impayés ce mois">
                    {{ $navImpayesCount > 99 ? '99+' : $navImpayesCount }}
                </span>
                @endif
            </div>
            <span class="font-display font-semibold text-[9.5px] uppercase tracking-widest leading-none">Relances</span>
        </a>

        {{-- Menu — ouvre la sidebar --}}
        <button onclick="openSidebar()"
                class="flex flex-col items-center justify-center gap-0.5 flex-1 py-2 transition-colors duration-150 border-none bg-transparent
                       text-white/40 hover:text-white/70">
            <svg class="w-[22px] h-[22px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
                <line x1="3" y1="6" x2="21" y2="6"/>
                <line x1="3" y1="12" x2="21" y2="12"/>
                <line x1="3" y1="18" x2="21" y2="18"/>
            </svg>
            <span class="font-display font-semibold text-[9.5px] uppercase tracking-widest leading-none">Menu</span>
        </button>

    </nav>
    @endif

</div>{{-- fin .lg:ml-64 --}}

{{-- Modale de confirmation globale --}}
<div id="g-confirm-overlay"
     x-data
     style="display:none"
     class="fixed inset-0 bg-bimo-navy/50 backdrop-blur-sm z-[9999] flex items-center justify-center p-4"
     role="dialog" aria-modal="true">
    <div id="g-confirm-box"
         class="bg-white rounded-[20px] w-full max-w-sm shadow-xl p-6">
        <div class="flex items-start gap-4 mb-5">
            <div id="g-confirm-icon-wrap" class="w-10 h-10 rounded-[10px] flex items-center justify-center flex-shrink-0">
                <svg id="g-confirm-icon" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
                </svg>
            </div>
            <div class="flex-1">
                <div id="g-confirm-title" class="font-display font-bold text-base text-bimo-text mb-1"></div>
                <div id="g-confirm-msg" class="font-body text-sm text-bimo-text/60 leading-relaxed"></div>
            </div>
        </div>
        <div class="flex justify-end gap-2">
            <button id="g-confirm-cancel"
                    class="px-4 py-2 rounded-[8px] border border-bimo-navy/15 font-body text-sm text-bimo-text/60 hover:text-bimo-text hover:border-bimo-navy/30 transition-all duration-150">
                Annuler
            </button>
            <button id="g-confirm-ok"
                    class="px-4 py-2 rounded-[8px] font-display font-bold text-sm text-white transition-all duration-150">
                Confirmer
            </button>
        </div>
    </div>
</div>

@stack('scripts')

<script>
// ── Modale de confirmation globale ──────────────────────────────────────────
(function () {
    var overlay   = document.getElementById('g-confirm-overlay');
    var titleEl   = document.getElementById('g-confirm-title');
    var msgEl     = document.getElementById('g-confirm-msg');
    var okBtn     = document.getElementById('g-confirm-ok');
    var cancelBtn = document.getElementById('g-confirm-cancel');
    var iconWrap  = document.getElementById('g-confirm-icon-wrap');
    var pendingForm = null;

    function open(title, msg, okLabel, okColor, iconBg) {
        titleEl.textContent       = title   || 'Confirmer l\'action';
        msgEl.textContent         = msg     || 'Cette action est irréversible.';
        okBtn.textContent         = okLabel || 'Confirmer';
        okBtn.style.background    = okColor || '#A60F1C';
        iconWrap.style.background = iconBg  || 'rgba(239,68,68,0.1)';
        iconWrap.querySelector('svg').style.color = okColor || '#A60F1C';
        overlay.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        okBtn.focus();
    }

    function close() {
        overlay.style.display = 'none';
        document.body.style.overflow = '';
        pendingForm = null;
    }

    cancelBtn.addEventListener('click', close);
    overlay.addEventListener('click', function (e) { if (e.target === overlay) close(); });
    document.addEventListener('keydown', function (e) {
        if (overlay.style.display !== 'flex') return;
        if (e.key === 'Escape') { close(); return; }
        // Focus-trap : maintient le focus entre Annuler et Confirmer (accessibilité).
        if (e.key === 'Tab') {
            var f = [cancelBtn, okBtn];
            var i = f.indexOf(document.activeElement);
            e.preventDefault();
            var next = e.shiftKey ? (i <= 0 ? f.length - 1 : i - 1) : (i === f.length - 1 ? 0 : i + 1);
            f[next].focus();
        }
    });
    okBtn.addEventListener('click', function () {
        if (pendingForm) { pendingForm._gConfirmed = true; pendingForm.requestSubmit(); }
        close();
    });

    document.addEventListener('submit', function (e) {
        var form = e.target;
        if (!form.dataset.confirm || form._gConfirmed) return;
        e.preventDefault();
        pendingForm = form;
        open(form.dataset.confirmTitle, form.dataset.confirm, form.dataset.confirmOk, form.dataset.confirmColor, form.dataset.confirmIconBg);
    }, true);
})();

// ── Anti double-submit ──────────────────────────────────────────────────────
document.addEventListener('submit', function (e) {
    var form = e.target;
    if (form.dataset.confirm && !form._gConfirmed) return;
    var btn = form.querySelector('button[type=submit]');
    if (!btn || btn.disabled) return;
    btn.disabled = true;
    btn.style.opacity = '0.7';
    btn.style.pointerEvents = 'none';
    btn.insertAdjacentHTML('afterbegin', '<svg style="width:14px;height:14px;margin-right:6px;animation:spin .7s linear infinite;display:inline-block;vertical-align:middle" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12a9 9 0 11-6.219-8.56"/></svg>');
});

// ── Tri de colonnes (data-sort sur les th) ─────────────────────────────────
(function () {
    var state = {};
    function parseVal(cell, type) {
        var t = cell ? cell.innerText.trim() : '';
        if (type === 'num') return parseFloat(t.replace(/\s/g, '').replace(',', '.').replace(/[^0-9.-]/g, '')) || 0;
        if (type === 'date') { var m = t.match(/(\d{2})\/(\d{2})\/(\d{4})/); return m ? m[3]+m[2]+m[1] : t; }
        return t.toLowerCase();
    }
    document.addEventListener('click', function (e) {
        var th = e.target.closest('th[data-sort]');
        if (!th) return;
        var table = th.closest('table');
        if (!table) return;
        var tid  = table.dataset.sortId || (table.dataset.sortId = 'tbl'+Math.random().toString(36).slice(2));
        var col  = parseInt(th.dataset.sort, 10);
        var type = th.dataset.sortType || 'str';
        var asc  = state[tid] && state[tid].col === col ? !state[tid].asc : true;
        state[tid] = { col, asc };
        table.querySelectorAll('th[data-sort]').forEach(function(h) { h.querySelector('.sort-arrow')?.remove(); h.style.color = ''; });
        var arrow = document.createElement('span');
        arrow.className = 'sort-arrow';
        arrow.textContent = asc ? ' ↑' : ' ↓';
        arrow.style.cssText = 'font-size:10px;opacity:.5';
        th.appendChild(arrow);
        var tbody = table.querySelector('tbody');
        var rows = Array.from(tbody.querySelectorAll('tr'));
        rows.sort(function(a,b) {
            var va = parseVal(a.cells[col], type), vb = parseVal(b.cells[col], type);
            return va < vb ? (asc ? -1 : 1) : va > vb ? (asc ? 1 : -1) : 0;
        });
        rows.forEach(function(r) { tbody.appendChild(r); });
    });
})();

// ── Alerte formulaire non sauvegardé ────────────────────────────────────────
(function () {
    var dirty = false;
    document.addEventListener('change', function(e) { var f = e.target.closest('form'); if (f && !f.dataset.noWarn && !f.dataset.confirm) dirty = true; });
    document.addEventListener('input',  function(e) { var f = e.target.closest('form'); if (f && !f.dataset.noWarn && !f.dataset.confirm) dirty = true; });
    document.addEventListener('submit', function() { dirty = false; });
    window.addEventListener('beforeunload', function(e) { if (dirty) { e.preventDefault(); e.returnValue = ''; } });
})();

// ── Copier référence ─────────────────────────────────────────────────────────
function copyRef(text, btn) {
    navigator.clipboard.writeText(text).then(function() {
        var orig = btn.innerHTML;
        btn.innerHTML = '<svg style="width:11px;height:11px;color:var(--ac)" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>';
        setTimeout(function() { btn.innerHTML = orig; }, 1500);
    });
}

// ── PWA Service Worker ───────────────────────────────────────────────────────
if ('serviceWorker' in navigator) {
    window.addEventListener('load', function() {
        navigator.serviceWorker.register('/sw.js').catch(function(){});
    });
}

// ── Sidebar mobile (vanilla JS — sans Alpine) ────────────────────────────────
function openSidebar() {
    document.getElementById('sidebar').classList.remove('-translate-x-full');
    document.getElementById('sidebar').classList.add('translate-x-0');
    document.getElementById('sidebar-overlay').style.display = 'block';
    document.body.style.overflow = 'hidden';
}
function closeSidebar() {
    document.getElementById('sidebar').classList.add('-translate-x-full');
    document.getElementById('sidebar').classList.remove('translate-x-0');
    document.getElementById('sidebar-overlay').style.display = 'none';
    document.body.style.overflow = '';
}

// ── Dropdown profil sidebar ──────────────────────────────────────────────────
function toggleProfileDrop() {
    var drop = document.getElementById('profile-drop');
    var chevron = document.getElementById('profile-chevron');
    var isOpen = drop.style.display !== 'none';
    drop.style.display = isOpen ? 'none' : 'block';
    chevron.style.transform = isOpen ? '' : 'rotate(180deg)';
}
document.addEventListener('click', function(e) {
    var drop = document.getElementById('profile-drop');
    if (!drop) return;
    if (!e.target.closest('[onclick="toggleProfileDrop()"]') && drop.style.display !== 'none') {
        drop.style.display = 'none';
        var chevron = document.getElementById('profile-chevron');
        if (chevron) chevron.style.transform = '';
    }
});

</script>
<style>@keyframes spin { to { transform: rotate(360deg); } }</style>

</body>
</html>
