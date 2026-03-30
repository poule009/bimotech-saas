<x-app-layout>
    <x-slot name="header">Propriétaires</x-slot>

    {{-- Alertes --}}
    @if(session('success'))
        <div class="alert alert-success section-gap">✅ {{ session('success') }}</div>
    @endif

    {{-- Header action --}}
    <div class="flex-between section-gap">
        <div>
            <h1 style="font-size:20px;font-weight:700;color:var(--text);letter-spacing:-.3px;">Propriétaires</h1>
            <p style="font-size:13px;color:var(--text-3);margin-top:3px;">{{ $proprietaires->total() }} propriétaire(s) enregistré(s)</p>
        </div>
        <a href="{{ route('admin.users.create', 'proprietaire') }}" class="btn btn-primary">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:15px;height:15px;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nouveau propriétaire
        </a>
    </div>

    {{-- Stats --}}
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:10px;" class="section-gap">
        <div class="kpi" style="text-align:center;padding:16px;">
            <div class="kpi-value" style="color:var(--agency);">{{ $stats['total'] }}</div>
            <div class="kpi-sub">Propriétaires</div>
        </div>
        <div class="kpi" style="text-align:center;padding:16px;">
            <div class="kpi-value">{{ $stats['total_biens'] }}</div>
            <div class="kpi-sub">Biens total</div>
        </div>
        <div class="kpi" style="text-align:center;padding:16px;border-color:#bbf7d0;background:#f0fdf4;">
            <div class="kpi-value" style="color:#16a34a;">{{ $stats['biens_loues'] }}</div>
            <div class="kpi-sub" style="color:#22c55e;">Biens loués</div>
        </div>
    </div>

    {{-- Mobile cards --}}
    <div class="mobile-cards section-gap">
        @forelse($proprietaires as $user)
            <div class="mobile-card" style="cursor:pointer;"
                 onclick="window.location='{{ route('admin.users.show', $user) }}'">
                <div class="flex-between" style="margin-bottom:10px;">
                    <div style="display:flex;align-items:center;gap:10px;">
                        <div class="avatar" style="width:36px;height:36px;font-size:14px;background:var(--agency-soft);color:var(--agency);">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <div>
                            <div style="font-weight:700;font-size:13px;color:var(--text);">{{ $user->name }}</div>
                            <div style="font-size:11px;color:var(--text-3);">{{ $user->created_at->format('d/m/Y') }}</div>
                        </div>
                    </div>
                    <span class="badge {{ $user->biens_count > 0 ? 'badge-blue' : 'badge-gray' }}">
                        {{ $user->biens_count }} bien(s)
                    </span>
                </div>
                <div class="mobile-card-row">
                    <span class="mobile-card-label">Email</span>
                    <span class="mobile-card-value">{{ $user->email }}</span>
                </div>
                <div class="mobile-card-row">
                    <span class="mobile-card-label">Téléphone</span>
                    <span class="mobile-card-value">{{ $user->telephone ?? '—' }}</span>
                </div>
                <div class="mobile-card-row">
                    <span class="mobile-card-label">Ville</span>
                    <span class="mobile-card-value">{{ $user->proprietaire?->ville ?? '—' }}</span>
                </div>
            </div>
        @empty
            <div style="text-align:center;padding:48px;color:var(--text-3);">
                <div style="font-size:40px;margin-bottom:12px;">👤</div>
                <div style="font-size:14px;margin-bottom:12px;">Aucun propriétaire enregistré</div>
                <a href="{{ route('admin.users.create', 'proprietaire') }}" class="btn btn-primary btn-sm">
                    Ajouter le premier propriétaire
                </a>
            </div>
        @endforelse
    </div>

    {{-- Desktop table --}}
    <div class="desktop-table card section-gap">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Téléphone</th>
                        <th style="text-align:center;">Biens</th>
                        <th>Ville</th>
                        <th style="text-align:center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($proprietaires as $user)
                        <tr style="cursor:pointer;"
                            onclick="window.location='{{ route('admin.users.show', $user) }}'">
                            <td>
                                <div style="display:flex;align-items:center;gap:10px;">
                                    <div class="avatar" style="width:32px;height:32px;font-size:12px;background:var(--agency-soft);color:var(--agency);flex-shrink:0;">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div style="font-weight:600;font-size:13px;color:var(--text);">{{ $user->name }}</div>
                                        <div style="font-size:11px;color:var(--text-3);">Inscrit le {{ $user->created_at->format('d/m/Y') }}</div>
                                    </div>
                                </div>
                            </td>
                            <td style="color:var(--text-2);font-size:13px;">{{ $user->email }}</td>
                            <td style="color:var(--text-2);font-size:13px;">{{ $user->telephone ?? '—' }}</td>
                            <td style="text-align:center;">
                                <span class="badge {{ $user->biens_count > 0 ? 'badge-blue' : 'badge-gray' }}">
                                    {{ $user->biens_count }} bien(s)
                                </span>
                            </td>
                            <td style="color:var(--text-2);font-size:13px;">{{ $user->proprietaire?->ville ?? '—' }}</td>
                            <td style="text-align:center;" onclick="event.stopPropagation()">
                                <div style="display:flex;align-items:center;justify-content:center;gap:6px;">
                                    <a href="{{ route('admin.users.show', $user) }}" class="btn btn-secondary btn-sm">
                                        Voir
                                    </a>
                                    @if($user->biens_count === 0)
                                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                              onsubmit="return confirm('Supprimer ce propriétaire ?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Supprimer</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center;padding:48px;color:var(--text-3);">
                                <div style="font-size:36px;margin-bottom:12px;">👤</div>
                                <div style="font-size:14px;margin-bottom:12px;">Aucun propriétaire enregistré</div>
                                <a href="{{ route('admin.users.create', 'proprietaire') }}" class="btn btn-primary btn-sm">
                                    Ajouter le premier propriétaire
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($proprietaires->hasPages())
            <div style="padding:16px 20px;border-top:1px solid var(--border);">
                {{ $proprietaires->links() }}
            </div>
        @endif
    </div>

</x-app-layout>