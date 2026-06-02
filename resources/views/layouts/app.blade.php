<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex,nofollow">
    <title>{{ auth()->user()?->agency?->name ?? config('app.name') }} — Bimothèque Immo</title>

    {{-- PWA --}}
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#1B4F6B">
    <link rel="apple-touch-icon" href="/icons/icon-192.png">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="Bimothèque">

    {{-- Polices --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap"
          media="print" onload="this.media='all'">
    <noscript>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap">
    </noscript>

    {{-- Tailwind + Alpine via Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Couleur agence injectée en runtime --}}
    @php
        $agencyColor = auth()->user()?->agency?->couleur_primaire ?? '#C9A84C';
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
        }
    </style>

    @stack('styles')
</head>

<body class="h-full bg-bimo-bg font-body antialiased"
      x-data="{ sidebarOpen: false }">

{{-- ═══════════════════════════════════════════════
     SIDEBAR
═══════════════════════════════════════════════ --}}

{{-- Overlay mobile --}}
<div x-show="sidebarOpen"
     x-transition:enter="transition-opacity duration-250"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity duration-250"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     @click="sidebarOpen = false"
     class="fixed inset-0 bg-bimo-navy/40 backdrop-blur-sm z-30 lg:hidden"
     style="display:none">
</div>

{{-- Sidebar panel --}}
<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
       class="fixed left-0 top-0 h-full w-64 bg-bimo-navy flex flex-col z-40
              transition-transform duration-250 ease-out
              lg:translate-x-0">

    {{-- Logo --}}
    <div class="flex items-center gap-3 h-16 px-5 border-b border-white/10 flex-shrink-0">
        <div class="w-8 h-8 rounded-[8px] flex items-center justify-center flex-shrink-0"
             style="background: var(--ac)">
            <span class="font-display font-extrabold text-bimo-navy text-sm">B</span>
        </div>
        <div class="min-w-0">
            <div class="font-display font-bold text-white text-sm leading-tight truncate">
                {{ auth()->user()?->agency?->name ?? 'Bimothèque' }}
            </div>
            <div class="font-body text-[10px] text-white/40 uppercase tracking-widest">Immo</div>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto">

        @php $route = request()->route()?->getName() ?? ''; @endphp

        {{-- Dashboard --}}
        <a href="{{ route('admin.dashboard') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-[10px] transition-all duration-150 group
                  {{ str_starts_with($route, 'admin.dashboard') ? 'bg-white/10 text-bimo-gold' : 'text-white/60 hover:text-white hover:bg-white/5' }}">
            <svg class="w-5 h-5 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
                <rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
            </svg>
            <span class="font-display font-semibold text-sm">Tableau de bord</span>
        </a>

        {{-- Biens --}}
        <a href="{{ route('admin.biens.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-[10px] transition-all duration-150
                  {{ str_starts_with($route, 'admin.biens') || str_starts_with($route, 'admin.immeubles') ? 'bg-white/10 text-bimo-gold' : 'text-white/60 hover:text-white hover:bg-white/5' }}">
            <svg class="w-5 h-5 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                <polyline points="9 22 9 12 15 12 15 22"/>
            </svg>
            <span class="font-display font-semibold text-sm">Biens</span>
        </a>

        {{-- Contrats --}}
        <a href="{{ route('admin.contrats.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-[10px] transition-all duration-150
                  {{ str_starts_with($route, 'admin.contrats') ? 'bg-white/10 text-bimo-gold' : 'text-white/60 hover:text-white hover:bg-white/5' }}">
            <svg class="w-5 h-5 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                <polyline points="14 2 14 8 20 8"/>
                <line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>
            </svg>
            <span class="font-display font-semibold text-sm">Contrats</span>
        </a>

        {{-- Paiements --}}
        <a href="{{ route('admin.paiements.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-[10px] transition-all duration-150
                  {{ str_starts_with($route, 'admin.paiements') ? 'bg-white/10 text-bimo-gold' : 'text-white/60 hover:text-white hover:bg-white/5' }}">
            <svg class="w-5 h-5 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="1" x2="12" y2="23"/>
                <path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/>
            </svg>
            <span class="font-display font-semibold text-sm">Paiements</span>
            @php $nbImpayes = cache()->remember('impayes_count_' . auth()->id(), 60, fn() => \App\Models\Contrat::where('agency_id', auth()->user()->agency_id)->where('statut', 'actif')->whereHas('impayesActifs')->count()); @endphp
            @if($nbImpayes > 0)
            <span class="ml-auto bg-bimo-red text-white font-body font-medium text-[10px] px-2 py-0.5 rounded-full">
                {{ $nbImpayes }}
            </span>
            @endif
        </a>

        {{-- Impayés --}}
        <a href="{{ route('admin.impayes.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-[10px] transition-all duration-150
                  {{ str_starts_with($route, 'admin.impayes') ? 'bg-white/10 text-bimo-gold' : 'text-white/60 hover:text-white hover:bg-white/5' }}">
            <svg class="w-5 h-5 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
            </svg>
            <span class="font-display font-semibold text-sm">Impayés</span>
        </a>

        {{-- Bailleurs --}}
        <a href="{{ route('admin.bailleurs.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-[10px] transition-all duration-150
                  {{ str_starts_with($route, 'admin.bailleurs') ? 'bg-white/10 text-bimo-gold' : 'text-white/60 hover:text-white hover:bg-white/5' }}">
            <svg class="w-5 h-5 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/>
                <circle cx="12" cy="7" r="4"/>
            </svg>
            <span class="font-display font-semibold text-sm">Bailleurs</span>
        </a>

        {{-- Locataires --}}
        <a href="{{ route('admin.users.locataires') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-[10px] transition-all duration-150
                  {{ str_starts_with($route, 'admin.users.locataires') ? 'bg-white/10 text-bimo-gold' : 'text-white/60 hover:text-white hover:bg-white/5' }}">
            <svg class="w-5 h-5 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                <circle cx="9" cy="7" r="4"/>
                <path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>
            </svg>
            <span class="font-display font-semibold text-sm">Locataires</span>
        </a>

        {{-- Séparateur --}}
        <div class="my-2 border-t border-white/10"></div>

        {{-- Rapports --}}
        <a href="{{ route('admin.rapports.financier') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-[10px] transition-all duration-150
                  {{ str_starts_with($route, 'admin.rapports') ? 'bg-white/10 text-bimo-gold' : 'text-white/60 hover:text-white hover:bg-white/5' }}">
            <svg class="w-5 h-5 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/>
                <line x1="6" y1="20" x2="6" y2="14"/>
            </svg>
            <span class="font-display font-semibold text-sm">Rapports</span>
        </a>

    </nav>

    {{-- Footer sidebar — profil --}}
    <div class="flex-shrink-0 p-3 border-t border-white/10"
         x-data="{ dropOpen: false }">
        <div class="relative">
            <button @click="dropOpen = !dropOpen" @click.outside="dropOpen = false"
                    class="flex items-center gap-3 w-full px-3 py-2.5 rounded-[10px] hover:bg-white/5 transition-colors duration-150">
                <div class="w-8 h-8 rounded-[8px] flex items-center justify-center flex-shrink-0 font-display font-bold text-sm"
                     style="background: var(--ac); color: #1B4F6B">
                    {{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 2)) }}
                </div>
                <div class="flex-1 text-left min-w-0">
                    <div class="font-display font-semibold text-white text-sm truncate">{{ auth()->user()->name }}</div>
                    <div class="font-body text-[10px] text-white/40 capitalize">{{ auth()->user()->role }}</div>
                </div>
                <svg class="w-4 h-4 text-white/30 flex-shrink-0 transition-transform duration-150"
                     :class="dropOpen ? 'rotate-180' : ''"
                     viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="6 9 12 15 18 9"/>
                </svg>
            </button>

            {{-- Dropdown --}}
            <div x-show="dropOpen"
                 x-transition:enter="transition duration-150"
                 x-transition:enter-start="opacity-0 translate-y-1"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="absolute bottom-full left-0 right-0 mb-1 bg-bimo-navy-dk border border-white/10 rounded-[12px] overflow-hidden shadow-xl z-50"
                 style="display:none">
                <a href="{{ route('profile.edit') }}"
                   class="flex items-center gap-3 px-4 py-3 text-white/70 hover:text-white hover:bg-white/5 transition-colors duration-150 font-body text-sm">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    Mon profil
                </a>
                <a href="{{ route('admin.agency.settings') }}"
                   class="flex items-center gap-3 px-4 py-3 text-white/70 hover:text-white hover:bg-white/5 transition-colors duration-150 font-body text-sm border-t border-white/10">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-2 2 2 2 0 01-2-2v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83 0 2 2 0 010-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 01-2-2 2 2 0 012-2h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 010-2.83 2 2 0 012.83 0l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 012-2 2 2 0 012 2v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 0 2 2 0 010 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 012 2 2 2 0 01-2 2h-.09a1.65 1.65 0 00-1.51 1z"/></svg>
                    Paramètres agence
                </a>
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
    <header class="sticky top-0 z-20 flex items-center justify-between h-14 px-4 bg-bimo-navy lg:hidden">
        {{-- Hamburger --}}
        <button @click="sidebarOpen = true"
                class="w-9 h-9 flex items-center justify-center rounded-[8px] text-white/70 hover:text-white hover:bg-white/10 transition-colors duration-150">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <line x1="3" y1="6" x2="21" y2="6"/>
                <line x1="3" y1="12" x2="21" y2="12"/>
                <line x1="3" y1="18" x2="21" y2="18"/>
            </svg>
        </button>

        {{-- Logo centré --}}
        <span class="font-display font-extrabold text-white text-base">
            Bimothèque <span style="color: var(--ac)">Immo</span>
        </span>

        {{-- Avatar --}}
        <div class="w-9 h-9 rounded-[9px] flex items-center justify-center font-display font-bold text-sm flex-shrink-0"
             style="background: var(--ac); color: #1B4F6B">
            {{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 2)) }}
        </div>
    </header>

    {{-- TOPBAR DESKTOP --}}
    <header class="hidden lg:flex items-center justify-between h-14 px-8 bg-bimo-surface border-b border-bimo-navy/10 sticky top-0 z-20">
        {{-- Breadcrumb --}}
        <div class="flex items-center gap-2 font-body text-sm text-bimo-navy/50">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-bimo-navy transition-colors duration-150">
                {{ auth()->user()?->agency?->name ?? 'Bimothèque' }}
            </a>
            <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            <span class="text-bimo-navy font-medium">{{ $header ?? 'Tableau de bord' }}</span>
        </div>

        {{-- Actions topbar droite --}}
        <div class="flex items-center gap-3">
            {{ $topbarActions ?? '' }}

            {{-- Recherche globale (admin uniquement) --}}
            @auth
            @if(in_array(auth()->user()->role, ['admin', 'superadmin']))
            <div class="relative" x-data="{ q: '', results: [], show: false, timer: null }"
                 @click.outside="show = false">
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-bimo-navy/30 pointer-events-none"
                         viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    <input type="text" x-model="q"
                           @input="clearTimeout(timer); if(q.length > 1) { timer = setTimeout(() => { fetch('{{ route('admin.search') }}?q='+encodeURIComponent(q), {headers:{'X-Requested-With':'XMLHttpRequest'}}).then(r=>r.json()).then(d=>{results=d.results;show=true}) }, 250) } else { show=false }"
                           @keydown.escape="show=false; q=''"
                           placeholder="Rechercher…"
                           class="w-48 pl-8 pr-3 py-1.5 bg-bimo-bg border border-bimo-navy/10 rounded-[8px] font-body text-sm text-bimo-navy placeholder:text-bimo-navy/30 focus:outline-none focus:border-bimo-gold focus:w-64 focus:bg-white transition-all duration-150">
                </div>
                <div x-show="show && results.length > 0"
                     class="absolute right-0 top-full mt-1 w-80 bg-white border border-bimo-navy/10 rounded-[12px] shadow-lg overflow-hidden z-50"
                     style="display:none">
                    <template x-for="item in results" :key="item.url">
                        <a :href="item.url"
                           class="flex items-center gap-3 px-4 py-2.5 hover:bg-bimo-bg transition-colors duration-100 border-b border-bimo-navy/5 last:border-0">
                            <div class="w-7 h-7 rounded-[6px] bg-bimo-bg2 flex items-center justify-center flex-shrink-0">
                                <svg class="w-3.5 h-3.5 text-bimo-navy/40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                                </svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="font-body font-medium text-sm text-bimo-navy truncate" x-text="item.label"></div>
                                <div class="font-body text-[11px] text-bimo-navy/40 truncate" x-text="item.sub"></div>
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
        <div x-data="{ show: true }" x-show="show"
             x-init="setTimeout(() => show = false, 5000)"
             x-transition:leave="transition duration-300"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="flex items-center gap-3 bg-bimo-gold/[8%] border border-bimo-gold/25 rounded-[12px] px-4 py-3 mb-4">
            <svg class="w-4 h-4 text-bimo-gold flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
            <p class="font-body text-sm text-bimo-gold flex-1">{{ session('success') }}</p>
            <button @click="show = false" class="text-bimo-gold/50 hover:text-bimo-gold transition-colors duration-150">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        @endif

        {{-- Flash warning --}}
        @if(session('warning'))
        <div x-data="{ show: true }" x-show="show"
             x-init="setTimeout(() => show = false, 7000)"
             x-transition:leave="transition duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="flex items-center gap-3 bg-amber-50 border border-amber-200 rounded-[12px] px-4 py-3 mb-4">
            <svg class="w-4 h-4 text-amber-600 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/></svg>
            <p class="font-body text-sm text-amber-800 flex-1">{{ session('warning') }}</p>
            <button @click="show = false" class="text-amber-400 hover:text-amber-600 transition-colors duration-150">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        @endif

        {{-- Flash error --}}
        @if(session('error'))
        <div x-data="{ show: true }" x-show="show"
             x-init="setTimeout(() => show = false, 12000)"
             x-transition:leave="transition duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="flex items-center gap-3 bg-bimo-red/[5%] border border-bimo-red/20 rounded-[12px] px-4 py-3 mb-4">
            <svg class="w-4 h-4 text-bimo-red flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
            <p class="font-body text-sm text-bimo-red flex-1">{{ session('error') }}</p>
            <button @click="show = false" class="text-bimo-red/50 hover:text-bimo-red transition-colors duration-150">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        @endif

        {{-- Flash errors validation --}}
        @if($errors->any())
        <div x-data="{ show: true }" x-show="show"
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
            <button @click="show = false" class="text-bimo-red/50 hover:text-bimo-red transition-colors duration-150 flex-shrink-0">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        @endif

        {{-- Contenu de la vue --}}
        {{ $slot ?? '' }}
        @yield('content')

    </main>

    {{-- BOTTOM NAV MOBILE --}}
    <nav class="fixed bottom-0 left-0 right-0 h-16 bg-bimo-navy border-t border-white/10 z-20 lg:hidden
                flex items-center justify-around px-2"
         style="padding-bottom: env(safe-area-inset-bottom, 0px)">

        @php $route = request()->route()?->getName() ?? ''; @endphp

        <a href="{{ route('admin.dashboard') }}"
           class="flex flex-col items-center gap-1 px-3 py-2 transition-colors duration-150
                  {{ str_starts_with($route, 'admin.dashboard') ? 'text-bimo-gold' : 'text-white/40 hover:text-white/70' }}">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
            <span class="font-display font-semibold text-[9px] uppercase tracking-widest">Accueil</span>
        </a>

        <a href="{{ route('admin.biens.index') }}"
           class="flex flex-col items-center gap-1 px-3 py-2 transition-colors duration-150
                  {{ str_starts_with($route, 'admin.biens') ? 'text-bimo-gold' : 'text-white/40 hover:text-white/70' }}">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            <span class="font-display font-semibold text-[9px] uppercase tracking-widest">Biens</span>
        </a>

        {{-- FAB central --}}
        <div class="relative">
            <a href="{{ route('admin.biens.create') }}"
               class="flex items-center justify-center w-[52px] h-[52px] rounded-[14px] shadow-gold-md font-extrabold text-2xl text-bimo-navy transition-transform duration-150 hover:scale-105 active:scale-95"
               style="background: var(--ac)">
                +
            </a>
        </div>

        <a href="{{ route('admin.paiements.index') }}"
           class="flex flex-col items-center gap-1 px-3 py-2 transition-colors duration-150
                  {{ str_starts_with($route, 'admin.paiements') ? 'text-bimo-gold' : 'text-white/40 hover:text-white/70' }}">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
            <span class="font-display font-semibold text-[9px] uppercase tracking-widest">Caisse</span>
        </a>

        <a href="{{ route('admin.rapports.financier') }}"
           class="flex flex-col items-center gap-1 px-3 py-2 transition-colors duration-150
                  {{ str_starts_with($route, 'admin.rapports') ? 'text-bimo-gold' : 'text-white/40 hover:text-white/70' }}">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
            <span class="font-display font-semibold text-[9px] uppercase tracking-widest">Rapports</span>
        </a>
    </nav>

</div>{{-- fin .lg:ml-64 --}}

{{-- Modale de confirmation globale --}}
<div id="g-confirm-overlay"
     x-data="{}"
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
                <div id="g-confirm-title" class="font-display font-bold text-base text-bimo-navy mb-1"></div>
                <div id="g-confirm-msg" class="font-body text-sm text-bimo-navy/60 leading-relaxed"></div>
            </div>
        </div>
        <div class="flex justify-end gap-2">
            <button id="g-confirm-cancel"
                    class="px-4 py-2 rounded-[8px] border border-bimo-navy/15 font-body text-sm text-bimo-navy/60 hover:text-bimo-navy hover:border-bimo-navy/30 transition-all duration-150">
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
        okBtn.style.background    = okColor || '#EF4444';
        iconWrap.style.background = iconBg  || 'rgba(239,68,68,0.1)';
        iconWrap.querySelector('svg').style.color = okColor || '#EF4444';
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
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') close(); });
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
        btn.innerHTML = '<svg style="width:11px;height:11px;color:#C9A84C" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>';
        setTimeout(function() { btn.innerHTML = orig; }, 1500);
    });
}

// ── PWA Service Worker ───────────────────────────────────────────────────────
if ('serviceWorker' in navigator) {
    window.addEventListener('load', function() {
        navigator.serviceWorker.register('/sw.js').catch(function(){});
    });
}

@keyframes spin { to { transform: rotate(360deg); } }
</script>

</body>
</html>
