<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Bimmo') }} — @yield('title', 'Espace agence')</title>

    <x-favicons />

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen">
@php
    $user   = auth()->user();
    $agency = $user?->agency;
    $nav = [
        ['section' => null],
        ['label' => 'Tableau de bord', 'route' => 'admin.dashboard',        'active' => 'admin.dashboard',      'icon' => '<rect x="3" y="3" width="7" height="9" rx="1.5"/><rect x="14" y="3" width="7" height="5" rx="1.5"/><rect x="14" y="12" width="7" height="9" rx="1.5"/><rect x="3" y="16" width="7" height="5" rx="1.5"/>'],
        ['label' => 'Propriétaires',   'route' => 'admin.users.proprietaires','active' => 'admin.users.proprietaires', 'perm' => 'proprietaires.lire', 'icon' => '<path d="M20 21v-2a4 4 0 00-3-3.87M4 21v-2a4 4 0 013-3.87M12 3a4 4 0 100 8 4 4 0 000-8z"/><circle cx="12" cy="7" r="0.5"/>'],
        ['label' => 'Biens',           'route' => 'admin.biens.index',       'active' => 'admin.biens.*',        'perm' => 'biens.lire', 'icon' => '<path d="M3 21h18M5 21V7l7-4 7 4v14M9 21v-6h6v6"/>'],
        ['label' => 'Locataires',      'route' => 'admin.users.locataires',  'active' => 'admin.users.locataires', 'perm' => 'locataires.lire', 'icon' => '<path d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 00-3-3.87"/>'],
        ['label' => 'Contrats',        'route' => 'admin.contrats.index',    'active' => 'admin.contrats.*',     'perm' => 'contrats.lire', 'icon' => '<path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><path d="M14 2v6h6M9 13h6M9 17h4"/>'],
        ['label' => 'Import de données','route' => 'admin.import.index',      'active' => 'admin.import.*',        'icon' => '<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/>'],
        ['section' => 'Finances'],
        ['label' => 'Quittances',      'route' => 'admin.paiements.index',   'active' => 'admin.paiements.*',    'perm' => 'paiements.lire', 'icon' => '<path d="M4 2v20l3-2 3 2 2-2 2 2 3-2 3 2V2l-3 2-3-2-2 2-2-2-3 2-3-2z"/><path d="M8 8h8M8 12h6"/>'],
        ['label' => 'Comptabilité',    'route' => 'admin.comptabilite.index','active' => 'admin.comptabilite.*', 'perm' => 'comptabilite.lire', 'icon' => '<rect x="4" y="2" width="16" height="20" rx="2"/><path d="M8 6h8M8 10h2M12 10h4M8 14h2M12 14h4M8 18h8"/>'],
        ['label' => 'Fiscalité',       'route' => 'admin.echeances-fiscales.index', 'active' => 'admin.echeances-fiscales.*', 'perm' => 'fiscal.lire', 'icon' => '<path d="M4 4h16v4H4V4Z"/><path d="M5 8v11a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V8"/><path d="M9 13h6M9 17h6"/>'],
        ['section' => 'Vitrine'],
        ['label' => 'Portail public',  'route' => 'portail.home',            'active' => 'portail.*',            'icon' => '<circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15 15 0 010 20 15 15 0 010-20z"/>'],
        ['section' => null],
        ['label' => "Journal d'activité", 'route' => 'admin.activity-logs.index', 'active' => 'admin.activity-logs.*', 'perm' => 'logs.lire', 'icon' => '<path d="M12 8v4l3 2"/><path d="M3.05 11a9 9 0 116.86 8.65"/><path d="M3 3v5h5"/>'],
        ['label' => 'Mon équipe',      'route' => 'admin.equipe.index',      'active' => 'admin.equipe.*',       'gate' => 'voirEquipe', 'icon' => '<path d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>'],
        ['label' => 'Abonnement',      'route' => 'subscription.index',      'active' => 'subscription.*',       'gate' => 'isOwner', 'icon' => '<rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/>'],
        ['label' => 'Paramètres',      'route' => 'admin.agency.settings',   'active' => 'admin.agency.*',       'icon' => '<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 11-2.83 2.83l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 11-2.83-2.83l.06-.06a1.65 1.65 0 00.33-1.82 1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 112.83-2.83l.06.06a1.65 1.65 0 001.82.33H9a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 112.83 2.83l-.06.06a1.65 1.65 0 00-.33 1.82V9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z"/>'],
    ];
@endphp

<div x-data="sidebar" class="flex min-h-screen">

    {{-- Overlay mobile --}}
    <div x-show="open" x-on:click="close" x-cloak
         class="fixed inset-0 z-30 bg-teal-deep/50 md:hidden"></div>

    {{-- ─────────────── SIDEBAR ─────────────── --}}
    <aside x-bind:class="panelClass"
           class="fixed md:sticky md:top-0 md:self-start md:h-screen inset-y-0 left-0 z-40 w-[238px] shrink-0 bg-teal text-paper
                  flex flex-col p-4 pt-6 transition-transform duration-200 md:translate-x-0">

        {{-- Verrou de marque « logo + nom » (sidebar teal → tone paper). --}}
        <a href="{{ route('admin.dashboard') }}" class="flex items-center px-2 mb-8 text-[22px]">
            <x-brand tone="paper" />
        </a>

        <nav class="flex-1 overflow-y-auto -mx-1 px-1">
            @foreach($nav as $item)
                @if(array_key_exists('section', $item))
                    @if($item['section'])
                        <div class="text-[10.5px] tracking-[0.1em] uppercase text-paper/50 px-2.5 mt-5 mb-2 font-semibold">{{ $item['section'] }}</div>
                    @else
                        <div class="mt-3"></div>
                    @endif
                @elseif((!isset($item['gate']) || auth()->user()?->can($item['gate'])) && (!isset($item['perm']) || auth()->user()?->hasAgencyPermission($item['perm'])))
                    @php $isActive = request()->routeIs($item['active']); @endphp
                    <a href="{{ route($item['route']) }}"
                       x-on:click="close"
                       @class([
                           'flex items-center gap-3 px-2.5 py-2.5 rounded-lg text-[13.5px] mb-0.5 transition-colors',
                           'bg-paper/10 text-paper font-semibold' => $isActive,
                           'text-paper/80 font-medium hover:bg-paper/[0.07]' => ! $isActive,
                       ])>
                        <svg class="w-[17px] h-[17px] opacity-90 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">{!! $item['icon'] !!}</svg>
                        {{ $item['label'] }}
                    </a>
                @endif
            @endforeach
        </nav>

        <div class="mt-auto border-t border-paper/10 pt-4 flex items-center gap-2.5">
            <div class="w-[34px] h-[34px] rounded-lg bg-gold-soft text-teal-deep font-bold text-[13px] flex items-center justify-center shrink-0">
                {{ mb_strtoupper(mb_substr($agency?->name ?? $user?->name ?? 'A', 0, 2)) }}
            </div>
            <div class="text-[12.5px] leading-tight min-w-0">
                <strong class="block text-[13px] text-paper truncate">{{ $agency?->name ?? $user?->name }}</strong>
                <a href="{{ route('profile.edit') }}" class="text-paper/50 hover:text-paper/80">Mon profil</a>
            </div>
        </div>
    </aside>

    {{-- ─────────────── MAIN ─────────────── --}}
    <div class="flex-1 min-w-0 flex flex-col">

        {{-- Topbar --}}
        <header class="flex items-center justify-between gap-4 px-5 md:px-9 py-5">
            <div class="flex items-center gap-3 min-w-0">
                <button type="button" x-on:click="toggle"
                        class="md:hidden w-9 h-9 rounded-lg border border-line bg-white flex items-center justify-center shrink-0"
                        aria-label="Ouvrir le menu">
                    <svg class="w-5 h-5 text-ink" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12h18M3 6h18M3 18h18"/></svg>
                </button>
                <div class="min-w-0">
                    <h1 class="font-display font-semibold text-[22px] text-ink truncate">@yield('page-title', 'Tableau de bord')</h1>
                    @hasSection('page-subtitle')
                        <div class="text-[12.5px] text-muted mt-0.5">@yield('page-subtitle')</div>
                    @endif
                </div>
            </div>
            <div class="flex items-center gap-3 shrink-0">
                @yield('topbar-actions')
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-[13px] text-muted hover:text-teal font-semibold">Déconnexion</button>
                </form>
            </div>
        </header>

        {{-- Contenu --}}
        <main class="flex-1 px-5 md:px-9 pb-12">
            @yield('content')
        </main>
    </div>
</div>

<x-support-whatsapp />

</body>
</html>
