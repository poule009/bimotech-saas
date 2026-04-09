{{--
    Composant : <x-sidebar />
    Usage     : <x-sidebar :agency="$agency" />

    Remplace le bloc <aside> répété dans les 11 vues.
    Détecte automatiquement la route active via request()->routeIs().
--}}

@props(['agency' => null])

@php
    $user = auth()->user();
    $role = $user->role ?? 'admin';

    $navAdmin = [
        ['route' => 'dashboard',          'icon' => 'grid',        'label' => 'Tableau de bord'],
        ['route' => 'biens.index',        'icon' => 'home',        'label' => 'Biens'],
        ['route' => 'contrats.index',     'icon' => 'file-text',   'label' => 'Contrats'],
        ['route' => 'paiements.impayes',  'icon' => 'credit-card', 'label' => 'Paiements'],
        ['route' => 'rapports.index',     'icon' => 'bar-chart-2', 'label' => 'Rapports'],
        ['route' => 'activity-logs.index','icon' => 'activity',    'label' => 'Activité'],
        ['route' => 'settings.agency',    'icon' => 'settings',    'label' => 'Paramètres'],
    ];

    $navProprietaire = [
        ['route' => 'proprietaire.dashboard', 'icon' => 'grid',        'label' => 'Tableau de bord'],
        ['route' => 'biens.index',            'icon' => 'home',        'label' => 'Mes biens'],
        ['route' => 'contrats.index',         'icon' => 'file-text',   'label' => 'Contrats'],
        ['route' => 'paiements.impayes',      'icon' => 'credit-card', 'label' => 'Paiements'],
    ];

    $navLocataire = [
        ['route' => 'locataire.dashboard', 'icon' => 'grid',        'label' => 'Mon espace'],
        ['route' => 'contrats.index',      'icon' => 'file-text',   'label' => 'Mon contrat'],
        ['route' => 'paiements.index',     'icon' => 'credit-card', 'label' => 'Mes quittances'],
    ];

    $nav = match($role) {
        'proprietaire' => $navProprietaire,
        'locataire'    => $navLocataire,
        default        => $navAdmin,
    };
@endphp

<aside style="
    width: 240px;
    min-height: 100vh;
    background: #0d1117;
    display: flex;
    flex-direction: column;
    position: fixed;
    top: 0; left: 0;
    z-index: 100;
    padding: 0;
    flex-shrink: 0;
">
    {{-- Logo / Nom agence --}}
    <div style="padding: 1.5rem 1.25rem 1rem; border-bottom: 1px solid rgba(255,255,255,.06);">
        @if($agency?->logo)
            <img src="{{ Storage::url($agency->logo) }}"
                 alt="{{ $agency->nom }}"
                 style="height: 32px; object-fit: contain; margin-bottom: 6px;">
        @else
            <div style="
                font-family: 'Syne', sans-serif;
                font-size: 16px;
                font-weight: 700;
                color: #c9a84c;
                letter-spacing: -0.3px;
                line-height: 1.2;
            ">
                {{ $agency?->nom ?? config('app.name') }}
            </div>
        @endif
        <div style="font-size: 11px; color: #484f58; margin-top: 2px; font-family: 'DM Sans', sans-serif;">
            {{ ucfirst($role) }}
        </div>
    </div>

    {{-- Navigation --}}
    <nav style="padding: 1rem 0.75rem; flex: 1;">
        @foreach ($nav as $item)
            @php
                $isActive = request()->routeIs($item['route']) || request()->routeIs($item['route'] . '.*');
            @endphp
            <a href="{{ route($item['route']) }}"
               style="
                   display: flex;
                   align-items: center;
                   gap: 10px;
                   padding: 0.55rem 0.75rem;
                   border-radius: 8px;
                   margin-bottom: 2px;
                   font-family: 'DM Sans', sans-serif;
                   font-size: 13.5px;
                   text-decoration: none;
                   transition: background .15s;
                   background: {{ $isActive ? 'rgba(201,168,76,.12)' : 'transparent' }};
                   color: {{ $isActive ? '#c9a84c' : '#8b949e' }};
               "
               onmouseover="if(!{{ $isActive ? 'true' : 'false' }}) this.style.background='rgba(255,255,255,.04)'; this.style.color='#e6edf3';"
               onmouseout="if(!{{ $isActive ? 'true' : 'false' }}) this.style.background='transparent'; this.style.color='#8b949e';"
            >
                <x-sidebar-icon :name="$item['icon']" :active="$isActive" />
                {{ $item['label'] }}
                @if($item['route'] === 'paiements.impayes')
                    <x-sidebar-badge />
                @endif
            </a>
        @endforeach
    </nav>

    {{-- Profil utilisateur --}}
    <div style="padding: 1rem 1.25rem; border-top: 1px solid rgba(255,255,255,.06);">
        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
            <div style="
                width: 32px; height: 32px; border-radius: 50%;
                background: rgba(201,168,76,.15);
                display: flex; align-items: center; justify-content: center;
                font-size: 12px; font-weight: 600; color: #c9a84c;
                flex-shrink: 0;
            ">
                {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
            </div>
            <div style="min-width: 0;">
                <div style="font-size: 12.5px; color: #e6edf3; font-family: 'DM Sans', sans-serif; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                    {{ $user->name }}
                </div>
                <div style="font-size: 11px; color: #484f58;">
                    {{ $user->email }}
                </div>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" style="
                width: 100%;
                background: rgba(255,255,255,.04);
                border: 1px solid rgba(255,255,255,.06);
                border-radius: 6px;
                color: #8b949e;
                font-family: 'DM Sans', sans-serif;
                font-size: 12px;
                padding: 6px;
                cursor: pointer;
                transition: background .15s;
            "
            onmouseover="this.style.background='rgba(255,255,255,.08)'"
            onmouseout="this.style.background='rgba(255,255,255,.04)'"
            >
                Déconnexion
            </button>
        </form>
    </div>
</aside>