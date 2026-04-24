@props(['agency' => null])

@php
    $user = auth()->user();
    $role = $user->role ?? 'admin';

    $navSuperAdmin = [
        ['section' => null,              'route' => 'superadmin.dashboard',          'label' => 'Tableau de bord'],
        ['section' => 'PLATEFORME',      'route' => 'superadmin.agencies.create',    'label' => 'Nouvelle agence'],
        ['section' => null,              'route' => 'superadmin.subscriptions',      'label' => 'Abonnements'],
        ['section' => null,              'route' => 'superadmin.activity-logs.index','label' => 'Journal activité'],
    ];

    $navAdmin = [
        ['section' => null,           'route' => 'admin.dashboard',          'label' => 'Tableau de bord'],

        ['section' => 'PATRIMOINE',   'route' => 'admin.biens.index',         'label' => 'Biens'],
        ['section' => null,           'route' => 'admin.immeubles.index',     'label' => 'Immeubles'],
        ['section' => null,           'route' => 'admin.users.proprietaires', 'label' => 'Propriétaires'],
        ['section' => null,           'route' => 'admin.bailleurs.index',     'label' => 'Portefeuille Bailleurs'],

        ['section' => 'RELATIONS',    'route' => 'admin.users.locataires',    'label' => 'Locataires'],
        ['section' => null,           'route' => 'admin.contrats.index',      'label' => 'Contrats'],

        ['section' => 'CAISSE',       'route' => 'admin.impayes.index',       'label' => 'Paiements & Quittances'],

        ['section' => 'ANALYTIQUE',   'route' => 'admin.rapports.financier',  'label' => 'Rapports'],
        ['section' => null,           'route' => 'admin.activity-logs.index', 'label' => 'Activité'],

        ['section' => 'AGENCE',       'route' => 'admin.agency.settings',     'label' => 'Paramètres'],
        ['section' => null,           'route' => 'subscription.index',        'label' => 'Abonnement'],
    ];

    $navProprietaire = [
        ['section' => null,         'route' => 'proprietaire.dashboard',  'label' => 'Tableau de bord'],
        ['section' => 'MON PARC',   'route' => 'admin.biens.index',        'label' => 'Mes biens'],
        ['section' => null,         'route' => 'admin.contrats.index',     'label' => 'Mes contrats'],
        ['section' => null,         'route' => 'admin.impayes.index',      'label' => 'Paiements'],
    ];

    $navLocataire = [
        ['section' => null,       'route' => 'locataire.dashboard',    'label' => 'Mon espace'],
        ['section' => 'MON BAIL', 'route' => 'locataire.mon-contrat',  'label' => 'Mon contrat'],
        ['section' => null,       'route' => 'locataire.paiements',    'label' => 'Mes quittances'],
    ];

    $svgs = [
        'admin.dashboard'           => '<rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>',
        'proprietaire.dashboard'    => '<rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>',
        'locataire.dashboard'       => '<rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>',
        'admin.biens.index'         => '<path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>',
        'admin.immeubles.index'     => '<rect x="2" y="3" width="20" height="18" rx="2"/><line x1="9" y1="3" x2="9" y2="21"/><line x1="15" y1="3" x2="15" y2="21"/><line x1="2" y1="9" x2="22" y2="9"/><line x1="2" y1="15" x2="22" y2="15"/>',
        'admin.contrats.index'      => '<path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>',
        'admin.impayes.index'       => '<rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/>',
        'admin.users.proprietaires' => '<path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/>',
        'admin.bailleurs.index'     => '<path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/><line x1="19" y1="11" x2="19" y2="17"/><line x1="22" y1="14" x2="16" y2="14"/>',
        'admin.users.locataires'    => '<path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/>',
        'admin.rapports.financier'  => '<line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/>',
        'admin.activity-logs.index' => '<polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>',
        'admin.agency.settings'     => '<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06-.06a2 2 0 01-2.83 0 2 2 0 010-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 010-2.83 2 2 0 012.83 0l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 0 2 2 0 010 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z"/>',
        'subscription.index'        => '<rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/>',
        'locataire.paiements'       => '<rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/>',
        'locataire.mon-contrat'         => '<path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>',
        'superadmin.dashboard'          => '<rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>',
        'superadmin.agencies.create'    => '<path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><line x1="12" y1="22" x2="12" y2="12"/><line x1="9" y1="22" x2="9" y2="12"/><line x1="15" y1="22" x2="15" y2="12"/><line x1="7" y1="9" x2="17" y2="9"/>',
        'superadmin.subscriptions'      => '<rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/>',
        'superadmin.activity-logs.index'=> '<polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>',
    ];

    $nav = match($role) {
        'superadmin'   => $navSuperAdmin,
        'proprietaire' => $navProprietaire,
        'locataire'    => $navLocataire,
        default        => $navAdmin,
    };

    // Initiales agence pour le logo de fallback
    $agencyInitial = strtoupper(substr($agency?->name ?? 'B', 0, 1));
@endphp

<style>
.bm-sidebar-wrap * { box-sizing: border-box; }
.bm-sidebar-wrap {
    width: 248px;
    height: 100vh;
    background: #0d1117;
    display: flex;
    flex-direction: column;
    position: fixed;
    top: 0; left: 0;
    z-index: 100;
    border-right: 1px solid rgba(255,255,255,.05);
    overflow: hidden;
}

/* ── Logo zone ── */
.bm-logo-zone {
    padding: 20px 16px 16px;
    border-bottom: 1px solid rgba(255,255,255,.06);
    display: flex;
    align-items: center;
    gap: 10px;
}
.bm-logo-icon {
    width: 36px; height: 36px;
    border-radius: 10px;
    background: var(--ac, #c9a84c);
    display: flex; align-items: center; justify-content: center;
    font-family: 'Syne', sans-serif;
    font-size: 15px; font-weight: 800;
    color: #0d1117;
    flex-shrink: 0;
}
.bm-logo-text { overflow: hidden; }
.bm-logo-name {
    font-family: 'Syne', sans-serif;
    font-size: 13.5px; font-weight: 700;
    color: #e6edf3;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    line-height: 1.2;
}
.bm-logo-role {
    font-size: 10px;
    color: #484f58;
    margin-top: 2px;
    text-transform: uppercase;
    letter-spacing: .6px;
}

/* ── Nav ── */
.bm-nav { padding: 10px 10px; flex: 1 1 0; overflow-y: auto; min-height: 0; }
.bm-nav::-webkit-scrollbar { width: 4px; }
.bm-nav::-webkit-scrollbar-track { background: transparent; }
.bm-nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,.08); border-radius: 2px; }

.bm-section-label {
    font-size: 9.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: rgba(255,255,255,.2);
    padding: 14px 8px 4px;
}

.bm-nav-item {
    display: flex;
    align-items: center;
    gap: 9px;
    padding: 9px 10px;
    border-radius: 9px;
    margin-bottom: 1px;
    font-family: 'DM Sans', sans-serif;
    font-size: 13px;
    font-weight: 500;
    text-decoration: none;
    color: #8b949e;
    transition: all .15s;
    position: relative;
}
.bm-nav-item:hover {
    background: rgba(255,255,255,.05);
    color: #e6edf3;
}
.bm-nav-item.active {
    background: rgba(var(--ac-r, 201), var(--ac-g, 168), var(--ac-b, 76), .13);
    color: var(--ac, #c9a84c);
}
.bm-nav-item.active::before {
    content: '';
    position: absolute;
    left: 0; top: 25%; bottom: 25%;
    width: 3px;
    background: var(--ac, #c9a84c);
    border-radius: 0 3px 3px 0;
}
.bm-nav-icon {
    width: 16px; height: 16px;
    flex-shrink: 0;
    transition: color .15s;
}
.bm-nav-label { flex: 1; }

/* ── Profil footer ── */
.bm-footer {
    padding: 12px 10px;
    border-top: 1px solid rgba(255,255,255,.06);
    flex-shrink: 0;
    background: #0d1117;
}
.bm-profile {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 10px;
    border-radius: 9px;
    margin-bottom: 6px;
    transition: background .15s;
}
.bm-profile:hover { background: rgba(255,255,255,.04); }
.bm-avatar {
    width: 32px; height: 32px;
    border-radius: 50%;
    background: rgba(var(--ac-r, 201), var(--ac-g, 168), var(--ac-b, 76), .15);
    border: 1.5px solid rgba(var(--ac-r, 201), var(--ac-g, 168), var(--ac-b, 76), .3);
    display: flex; align-items: center; justify-content: center;
    font-size: 12px; font-weight: 700;
    color: var(--ac, #c9a84c);
    flex-shrink: 0;
}
.bm-profile-info { overflow: hidden; flex: 1; }
.bm-profile-name {
    font-size: 12.5px; font-weight: 600;
    color: #e6edf3;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    line-height: 1.3;
}
.bm-profile-email {
    font-size: 10.5px;
    color: #484f58;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.bm-logout {
    display: flex; align-items: center; gap: 8px;
    width: 100%;
    padding: 8px 10px;
    border-radius: 9px;
    border: none;
    background: transparent;
    color: #484f58;
    font-size: 12px;
    font-family: 'DM Sans', sans-serif;
    cursor: pointer;
    transition: all .15s;
    text-align: left;
}
.bm-logout:hover {
    background: rgba(220,38,38,.08);
    color: #dc2626;
}
</style>

<aside class="bm-sidebar-wrap">

    {{-- Logo --}}
    <div class="bm-logo-zone">
        @if($agency?->logo_path)
            <img src="{{ Storage::url($agency->logo_path) }}"
                 alt="{{ $agency->name }}"
                 style="height:36px;width:36px;object-fit:contain;border-radius:10px;flex-shrink:0">
        @else
            <div class="bm-logo-icon">{{ $agencyInitial }}</div>
        @endif
        <div class="bm-logo-text">
            <div class="bm-logo-name">{{ $agency?->name ?? config('app.name') }}</div>
            <div class="bm-logo-role">{{ ucfirst($role) }}</div>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="bm-nav">
        @foreach($nav as $item)
            @if($item['section'])
                <div class="bm-section-label">{{ $item['section'] }}</div>
            @endif
            @php
                $isActive = request()->routeIs($item['route']) || request()->routeIs($item['route'].'.*');
                try { $url = route($item['route']); } catch(\Exception $e) { $url = '#'; }
                $svg = $svgs[$item['route']] ?? '<circle cx="12" cy="12" r="3"/>';
            @endphp
            <a href="{{ $url }}" class="bm-nav-item {{ $isActive ? 'active' : '' }}">
                <svg class="bm-nav-icon" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2"
                     stroke-linecap="round" stroke-linejoin="round">
                    {!! $svg !!}
                </svg>
                <span class="bm-nav-label">{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>

    {{-- Footer profil --}}
    <div class="bm-footer">
        <div class="bm-profile">
            <div class="bm-avatar">
                {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
            </div>
            <div class="bm-profile-info">
                <div class="bm-profile-name">{{ $user->name ?? '' }}</div>
                <div class="bm-profile-email">{{ $user->email ?? '' }}</div>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="bm-logout">
                <svg style="width:14px;height:14px;flex-shrink:0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/>
                    <polyline points="16 17 21 12 16 7"/>
                    <line x1="21" y1="12" x2="9" y2="12"/>
                </svg>
                Déconnexion
            </button>
        </form>
    </div>

</aside>