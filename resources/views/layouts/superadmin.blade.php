<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Bimmo') }} — Super Admin · @yield('title', 'Dashboard')</title>

    <x-favicons />

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen">
@php
    $user    = auth()->user();
    // Section « à venir » courante (pour surligner le bon lien de la sidebar).
    $current = request()->routeIs('superadmin.a-venir') ? request()->route('section') : null;
    $saNav = [
        ['label' => 'Dashboard',                  'href' => route('superadmin.dashboard'),         'active' => request()->routeIs('superadmin.dashboard'), 'icon' => '<rect x="3" y="3" width="7" height="9" rx="1.5"/><rect x="14" y="3" width="7" height="5" rx="1.5"/><rect x="14" y="12" width="7" height="9" rx="1.5"/><rect x="3" y="16" width="7" height="5" rx="1.5"/>'],
        ['label' => 'Agences',                     'href' => route('superadmin.a-venir', 'agences'),         'active' => $current === 'agences',         'icon' => '<path d="M3 21h18M5 21V7l7-4 7 4v14M9 21v-6h6v6"/>'],
        ['label' => 'Abonnements & facturation',   'href' => route('superadmin.a-venir', 'abonnements'),     'active' => $current === 'abonnements',     'icon' => '<rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/>'],
        ['label' => 'Support / Debug',             'href' => route('superadmin.a-venir', 'support'),         'active' => $current === 'support',         'icon' => '<path d="M12 2a10 10 0 100 20 10 10 0 000-20z"/><path d="M9.1 9a3 3 0 015.8 1c0 2-3 3-3 3"/><path d="M12 17h.01"/>'],
        ['label' => 'Règles fiscales',             'href' => route('superadmin.a-venir', 'regles-fiscales'), 'active' => $current === 'regles-fiscales', 'icon' => '<path d="M4 4h16v4H4V4Z"/><path d="M5 8v11a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V8"/><path d="M9 13h6M9 17h6"/>'],
        ['label' => 'Équipe interne',              'href' => route('superadmin.a-venir', 'equipe'),          'active' => $current === 'equipe',          'icon' => '<path d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>'],
        ['label' => 'Paramètres système',          'href' => route('superadmin.a-venir', 'parametres'),      'active' => $current === 'parametres',      'icon' => '<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 11-2.83 2.83l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 11-2.83-2.83l.06-.06a1.65 1.65 0 00.33-1.82 1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 112.83-2.83l.06.06a1.65 1.65 0 001.82.33H9a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 112.83 2.83l-.06.06a1.65 1.65 0 00-.33 1.82V9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z"/>'],
    ];
@endphp

<div x-data="sidebar" class="flex min-h-screen">

    {{-- Overlay mobile --}}
    <div x-show="open" x-on:click="close" x-cloak
         class="fixed inset-0 z-30 bg-black/50 md:hidden"></div>

    {{-- ─────────────── SIDEBAR (teal-deep : distincte de l'espace agence) ─────────────── --}}
    <aside x-bind:class="panelClass"
           class="fixed md:sticky md:top-0 md:self-start md:h-screen inset-y-0 left-0 z-40 w-[248px] shrink-0 bg-teal-deep text-paper
                  flex flex-col p-4 pt-6 transition-transform duration-200 md:translate-x-0">

        <a href="{{ route('superadmin.dashboard') }}" class="block px-2 pb-5 mb-5 border-b border-paper/10">
            <x-brand tone="paper" class="text-[21px]" />
            <span class="block text-[11px] font-semibold uppercase tracking-[0.14em] text-gold-soft mt-2">Super Admin</span>
        </a>

        <nav class="flex-1 overflow-y-auto -mx-1 px-1">
            @foreach($saNav as $item)
                <a href="{{ $item['href'] }}"
                   x-on:click="close"
                   @class([
                       'flex items-center gap-3 px-3 py-2.5 rounded-lg text-[13.5px] mb-0.5 transition-colors',
                       'bg-gold/[0.18] text-paper font-semibold' => $item['active'],
                       'text-paper/70 font-medium hover:bg-paper/[0.06] hover:text-paper' => ! $item['active'],
                   ])>
                    <svg class="w-[17px] h-[17px] opacity-90 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">{!! $item['icon'] !!}</svg>
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>

        <div class="mt-auto border-t border-paper/10 pt-4 text-[12px] text-paper/50">
            Connecté — <strong class="text-paper/85 font-semibold">{{ $user?->name }}</strong><br>
            Administrateur principal
            <form method="POST" action="{{ route('logout') }}" class="mt-3">
                @csrf
                <button type="submit" class="text-paper/60 hover:text-paper font-semibold">Déconnexion</button>
            </form>
        </div>
    </aside>

    {{-- ─────────────── MAIN ─────────────── --}}
    <div class="flex-1 min-w-0 flex flex-col">

        {{-- Topbar (mobile : bouton menu) --}}
        <header class="flex items-center justify-between gap-4 px-5 md:px-10 pt-6 md:hidden">
            <button type="button" x-on:click="toggle"
                    class="w-9 h-9 rounded-lg border border-line bg-white flex items-center justify-center shrink-0"
                    aria-label="Ouvrir le menu">
                <svg class="w-5 h-5 text-ink" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12h18M3 6h18M3 18h18"/></svg>
            </button>
            <x-brand class="text-[17px]" />
        </header>

        <main class="flex-1 px-5 md:px-10 py-6 md:py-8">
            @yield('content')
        </main>
    </div>
</div>

</body>
</html>
