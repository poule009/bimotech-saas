<x-app-layout>
    <x-slot name="header">Contrats de bail</x-slot>

    {{-- Alertes --}}
    @if(session('success'))
        <div class="alert alert-success section-gap">✅ {{ session('success') }}</div>
    @endif

    {{-- Header action --}}
    <div class="flex-between section-gap">
        <div>
            <h1 style="font-size:20px;font-weight:700;color:var(--text);letter-spacing:-.3px;">Contrats de bail</h1>
            <p style="font-size:13px;color:var(--text-3);margin-top:3px;">
                {{ $contrats->total() }} contrat(s) enregistré(s)
            </p>
        </div>
        <a href="{{ route('admin.contrats.create') }}" class="btn btn-primary">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:15px;height:15px;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nouveau contrat
        </a>
    </div>

    {{-- Stats --}}
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:10px;" class="section-gap">
        <div class="kpi" style="padding:16px;text-align:center;">
            <div style="font-size:28px;font-weight:800;color:var(--text);">{{ $stats['total'] }}</div>
            <div style="font-size:11px;color:var(--text-3);margin-top:3px;font-weight:500;">Total</div>
        </div>
        <div class="kpi" style="padding:16px;text-align:center;border-color:#bbf7d0;background:#f0fdf4;">
            <div style="font-size:28px;font-weight:800;color:#16a34a;">{{ $stats['actifs'] }}</div>
            <div style="font-size:11px;color:#22c55e;margin-top:3px;font-weight:500;">Actifs</div>
        </div>
        <div class="kpi" style="padding:16px;text-align:center;border-color:#fecaca;background:#fef2f2;">
            <div style="font-size:28px;font-weight:800;color:#dc2626;">{{ $stats['resilies'] }}</div>
            <div style="font-size:11px;color:#ef4444;margin-top:3px;font-weight:500;">Résiliés</div>
        </div>
        <div class="kpi" style="padding:16px;text-align:center;border-color:#e2e8f0;background:#f8fafc;">
            <div style="font-size:28px;font-weight:800;color:var(--text-2);">{{ $stats['expires'] }}</div>
            <div style="font-size:11px;color:var(--text-3);margin-top:3px;font-weight:500;">Expirés</div>
        </div>
    </div>

    {{-- Version mobile : cartes --}}
    <div class="mobile-cards section-gap">
        @forelse($contrats as $contrat)
            <div class="mobile-card">
                <div class="flex-between" style="margin-bottom:10px;">
                    <div>
                        <div style="font-weight:700;font-size:14px;color:var(--text);">{{ $contrat->bien->reference }}</div>
                        <div style="font-size:11px;color:var(--text-3);">{{ $contrat->bien->ville }}</div>
                    </div>
                    @php
                        $sc = match($contrat->statut) {
                            'actif'   => 'badge badge-green',
                            'resilié' => 'badge badge-red',
                            'expiré'  => 'badge badge-gray',
                            default   => 'badge badge-gray',
                        };
                        $sl = match($contrat->statut) {
                            'actif'   => 'Actif',
                            'resilié' => 'Résilié',
                            'expiré'  => 'Expiré',
                            default   => ucfirst($contrat->statut),
                        };
                    @endphp
                    <span class="{{ $sc }}">{{ $sl }}</span>
                </div>
                <div class="mobile-card-row">
                    <span class="mobile-card-label">Locataire</span>
                    <span class="mobile-card-value">{{ $contrat->locataire->name }}</span>
                </div>
                <div class="mobile-card-row">
                    <span class="mobile-card-label">Début</span>
                    <span class="mobile-card-value">{{ \Carbon\Carbon::parse($contrat->date_debut)->format('d/m/Y') }}</span>
                </div>
                <div class="mobile-card-row">
                    <span class="mobile-card-label">Fin</span>
                    <span class="mobile-card-value">{{ $contrat->date_fin ? \Carbon\Carbon::parse($contrat->date_fin)->format('d/m/Y') : 'Indéterminé' }}</span>
                </div>
                <div style="border-top:1px solid var(--border);margin-top:10px;padding-top:10px;">
                    <div class="mobile-card-row">
                        <span class="mobile-card-label">Loyer</span>
                        <span class="text-money" style="font-weight:700;font-size:14px;">{{ number_format($contrat->loyer_contractuel, 0, ',', ' ') }} F</span>
                    </div>
                    <div class="mobile-card-row">
                        <span class="mobile-card-label">Caution</span>
                        <span class="text-money" style="color:var(--text-2);">{{ number_format($contrat->caution, 0, ',', ' ') }} F</span>
                    </div>
                </div>
                <div style="display:flex;gap:8px;margin-top:12px;">
                    <a href="{{ route('admin.contrats.show', $contrat) }}" class="btn btn-secondary btn-sm" style="flex:1;justify-content:center;">
                        Voir le détail
                    </a>
                    @if($contrat->statut === 'actif')
                        <form method="POST" action="{{ route('admin.contrats.destroy', $contrat) }}"
                              onsubmit="return confirm('Résilier ce contrat ?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Résilier</button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div style="text-align:center;padding:48px 20px;color:var(--text-3);">
                <div style="font-size:40px;margin-bottom:12px;">📄</div>
                <div style="font-size:14px;margin-bottom:12px;">Aucun contrat enregistré</div>
                <a href="{{ route('admin.contrats.create') }}" class="btn btn-primary btn-sm">
                    Créer le premier contrat
                </a>
            </div>
        @endforelse
    </div>

    {{-- Version desktop : tableau --}}
    <div class="desktop-table card section-gap">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Bien</th>
                        <th>Locataire</th>
                        <th>Début</th>
                        <th>Fin</th>
                        <th style="text-align:right;">Loyer</th>
                        <th style="text-align:right;">Caution</th>
                        <th style="text-align:center;">Statut</th>
                        <th style="text-align:center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($contrats as $contrat)
                        @php
                            $sc = match($contrat->statut) {
                                'actif'   => 'badge badge-green',
                                'resilié' => 'badge badge-red',
                                'expiré'  => 'badge badge-gray',
                                default   => 'badge badge-gray',
                            };
                            $sl = match($contrat->statut) {
                                'actif'   => 'Actif',
                                'resilié' => 'Résilié',
                                'expiré'  => 'Expiré',
                                default   => ucfirst($contrat->statut),
                            };
                        @endphp
                        <tr>
                            <td>
                                <div style="font-weight:600;font-size:13px;color:var(--text);">{{ $contrat->bien->reference }}</div>
                                <div style="font-size:11px;color:var(--text-3);">{{ $contrat->bien->ville }}</div>
                            </td>
                            <td>
                                <div style="font-size:13px;font-weight:500;color:var(--text);">{{ $contrat->locataire->name }}</div>
                                <div style="font-size:11px;color:var(--text-3);">{{ $contrat->locataire->email }}</div>
                            </td>
                            <td style="color:var(--text-2);font-size:13px;">
                                {{ \Carbon\Carbon::parse($contrat->date_debut)->format('d/m/Y') }}
                            </td>
                            <td style="color:var(--text-2);font-size:13px;">
                                {{ $contrat->date_fin ? \Carbon\Carbon::parse($contrat->date_fin)->format('d/m/Y') : '—' }}
                            </td>
                            <td style="text-align:right;" class="text-money">
                                {{ number_format($contrat->loyer_contractuel, 0, ',', ' ') }} F
                            </td>
                            <td style="text-align:right;color:var(--text-2);" class="text-money">
                                {{ number_format($contrat->caution, 0, ',', ' ') }} F
                            </td>
                            <td style="text-align:center;">
                                <span class="{{ $sc }}">{{ $sl }}</span>
                            </td>
                            <td style="text-align:center;">
                                <div style="display:flex;align-items:center;justify-content:center;gap:6px;">
                                    <a href="{{ route('admin.contrats.show', $contrat) }}"
                                       class="btn btn-secondary btn-sm">
                                        Voir
                                    </a>
                                    @if($contrat->statut === 'actif')
                                        <form method="POST"
                                              action="{{ route('admin.contrats.destroy', $contrat) }}"
                                              onsubmit="return confirm('Résilier ce contrat définitivement ?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                Résilier
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align:center;padding:48px;color:var(--text-3);">
                                <div style="font-size:36px;margin-bottom:12px;">📄</div>
                                <div style="font-size:14px;margin-bottom:12px;">Aucun contrat enregistré</div>
                                <a href="{{ route('admin.contrats.create') }}" class="btn btn-primary btn-sm">
                                    Créer le premier contrat
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($contrats->hasPages())
            <div style="padding:16px 20px;border-top:1px solid var(--border);">
                {{ $contrats->links() }}
            </div>
        @endif
    </div>

</x-app-layout>