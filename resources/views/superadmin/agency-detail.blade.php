@extends('layouts.app')
@section('title', $agency->name)
@section('breadcrumb', 'Agence — '.$agency->name)

@section('content')
<style>
.info-row { display:flex;gap:6px;align-items:flex-start;font-size:13px;color:#374151;padding:6px 0;border-bottom:1px solid #f3f4f6; }
.info-row:last-child { border-bottom:none; }
.info-lbl { font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.7px;color:#9ca3af;min-width:90px;margin-top:2px; }
.detail-grid { display:grid;grid-template-columns:280px 1fr;gap:16px;margin-bottom:20px; }
.stats-grid { display:grid;grid-template-columns:repeat(3,1fr);gap:12px; }
</style>

<div style="padding:0 0 48px">

    {{-- Breadcrumb + actions --}}
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:10px">
        <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:#6b7280">
            <a href="{{ route('superadmin.dashboard') }}" style="color:#6b7280;text-decoration:none">Agences</a>
            <svg style="width:12px;height:12px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            <span style="color:#0d1117;font-weight:600">{{ $agency->name }}</span>
        </div>
        <div style="display:flex;align-items:center;gap:8px">
            @if($agency->actif)
                <span class="badge" style="background:#dcfce7;color:#16a34a"><span class="bdot"></span>Active</span>
            @else
                <span class="badge" style="background:#fee2e2;color:#dc2626"><span class="bdot"></span>Suspendue</span>
            @endif
            <form method="POST" action="{{ route('superadmin.agencies.toggle', $agency) }}"
                  onsubmit="return confirm('{{ $agency->actif ? 'Suspendre ?' : 'Activer ?' }}')">
                @csrf @method('PATCH')
                <button type="submit"
                    style="padding:7px 16px;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;border:none;
                           background:{{ $agency->actif ? '#fee2e2' : '#dcfce7' }};
                           color:{{ $agency->actif ? '#dc2626' : '#16a34a' }}">
                    {{ $agency->actif ? 'Suspendre' : 'Activer' }}
                </button>
            </form>
        </div>
    </div>

    {{-- Identité + Stats --}}
    <div class="detail-grid">

        {{-- Carte identité --}}
        <div class="card">
            <div class="card-hd"><div class="card-title">Identité</div></div>
            <div class="card-body">
                <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px">
                    @if($agency->logo_path)
                        <img src="{{ Storage::url($agency->logo_path) }}" alt="{{ $agency->name }}"
                             style="height:48px;width:48px;object-fit:contain;border-radius:10px;border:1px solid #e5e7eb">
                    @else
                        <div style="height:48px;width:48px;border-radius:10px;background:#f3f4f6;display:flex;align-items:center;justify-content:center;font-size:22px;border:1px dashed #d1d5db">
                            {{ strtoupper(substr($agency->name, 0, 1)) }}
                        </div>
                    @endif
                    <div>
                        <div style="font-weight:700;color:#0d1117">{{ $agency->name }}</div>
                        <div style="font-size:11px;color:#9ca3af">{{ $agency->slug }}</div>
                    </div>
                </div>
                <div class="info-row"><span class="info-lbl">Email</span><span>{{ $agency->email }}</span></div>
                @if($agency->telephone)
                <div class="info-row"><span class="info-lbl">Téléphone</span><span>{{ $agency->telephone }}</span></div>
                @endif
                @if($agency->adresse)
                <div class="info-row"><span class="info-lbl">Adresse</span><span>{{ $agency->adresse }}</span></div>
                @endif
                <div class="info-row"><span class="info-lbl">TVA</span><span>{{ $agency->taux_tva }}%</span></div>
                @if($agency->couleur_primaire)
                <div class="info-row">
                    <span class="info-lbl">Couleur</span>
                    <span style="display:flex;align-items:center;gap:6px">
                        <span style="width:16px;height:16px;border-radius:50%;background:{{ $agency->couleur_primaire }};border:1px solid #e5e7eb;display:inline-block"></span>
                        {{ $agency->couleur_primaire }}
                    </span>
                </div>
                @endif
                <div class="info-row"><span class="info-lbl">Inscrite le</span><span style="font-size:11px">{{ $agency->created_at->format('d/m/Y à H:i') }}</span></div>
            </div>
        </div>

        {{-- Stats --}}
        <div class="stats-grid">
            <div class="kpi">
                <div class="kpi-lbl">Utilisateurs</div>
                <div class="kpi-val">{{ $stats['nb_users'] }}</div>
                <div class="kpi-sub">{{ $stats['nb_proprietaires'] }} proprio · {{ $stats['nb_locataires'] }} locataires</div>
            </div>
            <div class="kpi">
                <div class="kpi-lbl">Biens</div>
                <div class="kpi-val">{{ $stats['nb_biens'] }}</div>
                <div class="kpi-sub" style="color:#16a34a">{{ $stats['nb_biens_loues'] }} loués</div>
            </div>
            <div class="kpi">
                <div class="kpi-lbl">Contrats actifs</div>
                <div class="kpi-val">{{ $stats['nb_contrats'] }}</div>
            </div>
            <div class="kpi gold">
                <div class="kpi-lbl">Loyers encaissés</div>
                <div class="kpi-val" style="font-size:16px">{{ number_format($stats['total_loyers'], 0, ',', ' ') }}</div>
                <div class="kpi-sub">FCFA</div>
            </div>
            <div class="kpi blue" style="grid-column:span 2">
                <div class="kpi-lbl">Commissions générées</div>
                <div class="kpi-val" style="font-size:16px;color:#6366f1">{{ number_format($stats['total_commissions'], 0, ',', ' ') }}</div>
                <div class="kpi-sub">FCFA TTC</div>
            </div>
        </div>
    </div>

    {{-- Utilisateurs --}}
    <div class="table-card" style="margin-bottom:16px">
        <div style="padding:14px 20px;border-bottom:1px solid #e5e7eb">
            <div style="font-family:'Syne',sans-serif;font-size:13px;font-weight:700;color:#0d1117">
                Utilisateurs ({{ $users->count() }})
            </div>
        </div>
        <table class="dt">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Email</th>
                    <th style="text-align:center">Rôle</th>
                    <th style="text-align:center">Téléphone</th>
                    <th style="text-align:center">Inscrit le</th>
                    <th style="text-align:center">Statut</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                @php
                    $roleStyle = match($user->role) {
                        'admin'        => 'background:#ede9fe;color:#7c3aed',
                        'proprietaire' => 'background:#dbeafe;color:#1d4ed8',
                        'locataire'    => 'background:#dcfce7;color:#16a34a',
                        default        => 'background:#f3f4f6;color:#6b7280',
                    };
                    $roleLabel = match($user->role) {
                        'admin' => 'Admin', 'proprietaire' => 'Propriétaire', 'locataire' => 'Locataire', default => $user->role,
                    };
                @endphp
                <tr>
                    <td style="font-weight:600">{{ $user->name }}</td>
                    <td style="color:#6b7280">{{ $user->email }}</td>
                    <td style="text-align:center">
                        <span class="badge" style="{{ $roleStyle }}">{{ $roleLabel }}</span>
                    </td>
                    <td style="text-align:center;color:#6b7280">{{ $user->telephone ?? '—' }}</td>
                    <td style="text-align:center;font-size:12px;color:#9ca3af">{{ $user->created_at->format('d/m/Y') }}</td>
                    <td style="text-align:center">
                        @if($user->deleted_at)
                            <span class="badge" style="background:#fee2e2;color:#dc2626"><span class="bdot"></span>Supprimé</span>
                        @else
                            <span class="badge" style="background:#dcfce7;color:#16a34a"><span class="bdot"></span>Actif</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" style="text-align:center;padding:40px;color:#9ca3af">Aucun utilisateur.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Biens --}}
    <div class="table-card">
        <div style="padding:14px 20px;border-bottom:1px solid #e5e7eb">
            <div style="font-family:'Syne',sans-serif;font-size:13px;font-weight:700;color:#0d1117">
                Biens ({{ $biens->count() }})
            </div>
        </div>
        <table class="dt">
            <thead>
                <tr>
                    <th>Référence</th>
                    <th>Type</th>
                    <th>Adresse</th>
                    <th style="text-align:center">Propriétaire</th>
                    <th style="text-align:right">Loyer</th>
                    <th style="text-align:center">Statut</th>
                </tr>
            </thead>
            <tbody>
                @forelse($biens as $bien)
                @php
                    $bstatut = match($bien->statut) {
                        'loue'       => ['Loué',      'background:#dcfce7;color:#16a34a'],
                        'disponible' => ['Disponible','background:#dbeafe;color:#1d4ed8'],
                        'en_travaux' => ['Travaux',   'background:#fef3c7;color:#d97706'],
                        default      => [ucfirst($bien->statut), 'background:#f3f4f6;color:#6b7280'],
                    };
                @endphp
                <tr>
                    <td style="font-family:monospace;font-size:11px;color:#9ca3af">{{ $bien->reference }}</td>
                    <td>{{ $bien->type }}</td>
                    <td style="color:#6b7280">{{ $bien->adresse }}, {{ $bien->ville }}</td>
                    <td style="text-align:center;color:#374151">{{ $bien->proprietaire?->name ?? '—' }}</td>
                    <td style="text-align:right;font-weight:600">{{ number_format($bien->loyer_mensuel, 0, ',', ' ') }} F</td>
                    <td style="text-align:center">
                        <span class="badge" style="{{ $bstatut[1] }}">{{ $bstatut[0] }}</span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" style="text-align:center;padding:40px;color:#9ca3af">Aucun bien.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
