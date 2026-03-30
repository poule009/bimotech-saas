<x-app-layout>
    <x-slot name="header">{{ $user->name }}</x-slot>

    {{-- Header navigation --}}
    <div class="flex-between section-gap" style="flex-wrap:wrap;gap:12px;">
        <div style="display:flex;align-items:center;gap:12px;">
            <a href="{{ route('admin.users.proprietaires') }}"
               style="display:flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:var(--radius-sm);border:1px solid var(--border);color:var(--text-2);transition:background .15s;"
               onmouseenter="this.style.background='var(--bg)'"
               onmouseleave="this.style.background='transparent'">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:16px;height:16px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div style="display:flex;align-items:center;gap:12px;">
                <div class="avatar" style="width:42px;height:42px;font-size:16px;background:var(--agency-soft);color:var(--agency);">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div>
                    <h1 style="font-size:20px;font-weight:700;color:var(--text);letter-spacing:-.3px;">{{ $user->name }}</h1>
                    <p style="font-size:13px;color:var(--text-3);margin-top:1px;">Propriétaire · {{ $user->email }}</p>
                </div>
            </div>
        </div>
        <a href="{{ route('admin.contrats.create') }}" class="btn btn-primary">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:15px;height:15px;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Créer un contrat
        </a>
    </div>

    {{-- KPI --}}
    <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px;" class="section-gap" id="user-kpi">
        <div class="kpi">
            <div class="kpi-label">Biens</div>
            <div class="kpi-value" style="color:var(--agency);">{{ $stats['nb_biens'] }}</div>
            <div class="kpi-sub">{{ $stats['nb_biens_loues'] }} loué(s)</div>
        </div>
        <div class="kpi">
            <div class="kpi-label">Locataires actifs</div>
            <div class="kpi-value" style="color:#7c3aed;">{{ $stats['nb_locataires'] }}</div>
            <div class="kpi-sub">En cours de bail</div>
        </div>
        <div class="kpi">
            <div class="kpi-label">Loyers encaissés</div>
            <div class="kpi-value text-money" style="font-size:18px;">{{ number_format($stats['total_loyers'], 0, ',', ' ') }} F</div>
            <div class="kpi-sub">{{ $stats['nb_paiements'] }} paiements</div>
        </div>
        <div class="kpi">
            <div class="kpi-label">Net perçu</div>
            <div class="kpi-value text-money" style="color:#16a34a;font-size:18px;">{{ number_format($stats['total_net'], 0, ',', ' ') }} F</div>
            <div class="kpi-sub">Commission : {{ number_format($stats['total_commission'], 0, ',', ' ') }} F</div>
        </div>
    </div>

    {{-- Coordonnées --}}
    <div class="card section-gap">
        <div class="card-header">
            <span class="card-title">Coordonnées</span>
        </div>
        <div class="card-body">
            <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:14px;" id="coordonnees-grid">
                <div>
                    <div style="font-size:10px;font-weight:600;color:var(--text-3);text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Téléphone</div>
                    <div style="font-size:13px;font-weight:600;color:var(--text);">{{ $user->telephone ?? '—' }}</div>
                </div>
                <div>
                    <div style="font-size:10px;font-weight:600;color:var(--text-3);text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Adresse</div>
                    <div style="font-size:13px;font-weight:600;color:var(--text);">{{ $user->adresse ?? '—' }}</div>
                </div>
                <div>
                    <div style="font-size:10px;font-weight:600;color:var(--text-3);text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Ville</div>
                    <div style="font-size:13px;font-weight:600;color:var(--text);">{{ $user->proprietaire?->ville ?? '—' }}</div>
                </div>
                <div>
                    <div style="font-size:10px;font-weight:600;color:var(--text-3);text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Mode paiement préféré</div>
                    <div style="font-size:13px;font-weight:600;color:var(--text);">
                        {{ ucfirst(str_replace('_', ' ', $user->proprietaire?->mode_paiement_prefere ?? '—')) }}
                    </div>
                </div>
                @if($user->proprietaire?->numero_wave)
                    <div>
                        <div style="font-size:10px;font-weight:600;color:var(--text-3);text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Wave</div>
                        <div style="font-size:13px;font-weight:600;color:var(--text);">{{ $user->proprietaire->numero_wave }}</div>
                    </div>
                @endif
                @if($user->proprietaire?->numero_om)
                    <div>
                        <div style="font-size:10px;font-weight:600;color:var(--text-3);text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Orange Money</div>
                        <div style="font-size:13px;font-weight:600;color:var(--text);">{{ $user->proprietaire->numero_om }}</div>
                    </div>
                @endif
                @if($user->proprietaire?->banque)
                    <div>
                        <div style="font-size:10px;font-weight:600;color:var(--text-3);text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Banque</div>
                        <div style="font-size:13px;font-weight:600;color:var(--text);">{{ $user->proprietaire->banque }}</div>
                    </div>
                @endif
                @if($user->proprietaire?->ninea)
                    <div>
                        <div style="font-size:10px;font-weight:600;color:var(--text-3);text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">NINEA</div>
                        <div style="font-size:13px;font-weight:600;color:var(--text);">{{ $user->proprietaire->ninea }}</div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Biens & Locataires --}}
    <div class="card section-gap">
        <div class="card-header">
            <span class="card-title">Biens & Locataires</span>
            <a href="{{ route('biens.create') }}" class="btn btn-secondary btn-sm">+ Ajouter un bien</a>
        </div>

        @forelse($user->biens as $bien)
            @php $contratActif = $bien->contrats->firstWhere('statut', 'actif'); @endphp
            <div style="padding:16px 20px;border-bottom:1px solid var(--border);">
                <div style="display:flex;align-items:flex-start;gap:16px;flex-wrap:wrap;">

                    {{-- Infos bien --}}
                    <div style="flex:1;min-width:200px;">
                        <div style="display:flex;align-items:center;gap:8px;margin-bottom:6px;flex-wrap:wrap;">
                            <span style="font-weight:700;font-size:14px;color:var(--text);">{{ $bien->reference }}</span>
                            <span style="font-size:11px;color:var(--text-3);">{{ $bien->type }}</span>
                            @php
                                $sc = match($bien->statut) {
                                    'loue'       => 'badge badge-green',
                                    'disponible' => 'badge badge-blue',
                                    'en_travaux' => 'badge badge-amber',
                                    default      => 'badge badge-gray',
                                };
                            @endphp
                            <span class="{{ $sc }}">{{ ucfirst(str_replace('_', ' ', $bien->statut)) }}</span>
                        </div>
                        <div style="font-size:12px;color:var(--text-2);margin-bottom:4px;">
                            📍 {{ $bien->adresse }}, {{ $bien->ville }}
                            @if($bien->surface_m2) · {{ $bien->surface_m2 }} m² @endif
                        </div>
                        <div style="font-weight:700;font-size:14px;color:var(--text);" class="text-money">
                            {{ number_format($bien->loyer_mensuel, 0, ',', ' ') }} FCFA/mois
                            <span style="font-size:11px;font-weight:400;color:var(--text-3);">· Commission {{ $bien->taux_commission }}%</span>
                        </div>
                        <div style="display:flex;gap:8px;margin-top:10px;">
                            <a href="{{ route('biens.show', $bien) }}" class="btn btn-secondary btn-sm">Détail</a>
                            <a href="{{ route('biens.edit', $bien) }}" class="btn btn-secondary btn-sm">Modifier</a>
                        </div>
                    </div>

                    {{-- Locataire actif --}}
                    <div style="min-width:180px;max-width:240px;">
                        @if($contratActif)
                            <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:var(--radius-sm);padding:12px;">
                                <div style="font-size:10px;font-weight:600;color:#22c55e;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">Locataire actuel</div>
                                <div style="font-weight:700;font-size:13px;color:#15803d;">{{ $contratActif->locataire->name }}</div>
                                <div style="font-size:11px;color:#16a34a;margin-top:2px;">
                                    {{ $contratActif->locataire->telephone ?? $contratActif->locataire->email }}
                                </div>
                                <div style="font-size:11px;color:#22c55e;margin-top:2px;">
                                    Depuis {{ \Carbon\Carbon::parse($contratActif->date_debut)->format('d/m/Y') }}
                                </div>
                                <div style="display:flex;gap:8px;margin-top:8px;">
                                    <a href="{{ route('admin.contrats.show', $contratActif) }}"
                                       style="font-size:11px;color:#15803d;font-weight:600;">Voir contrat →</a>
                                    <a href="{{ route('admin.paiements.create', ['contrat_id' => $contratActif->id]) }}"
                                       style="font-size:11px;color:var(--agency);font-weight:600;">+ Paiement</a>
                                </div>
                            </div>
                        @else
                            <div style="background:var(--bg);border:1px solid var(--border);border-radius:var(--radius-sm);padding:12px;text-align:center;">
                                <div style="font-size:11px;color:var(--text-3);margin-bottom:8px;">Aucun locataire</div>
                                <a href="{{ route('admin.contrats.create', ['bien_id' => $bien->id]) }}"
                                   class="btn btn-secondary btn-sm" style="font-size:11px;">+ Créer un contrat</a>
                            </div>
                        @endif
                    </div>

                </div>
            </div>
        @empty
            <div style="text-align:center;padding:48px;color:var(--text-3);">
                <div style="font-size:36px;margin-bottom:12px;">🏠</div>
                <div style="font-size:14px;margin-bottom:12px;">Aucun bien enregistré</div>
                <a href="{{ route('biens.create') }}" class="btn btn-primary btn-sm">Ajouter un bien</a>
            </div>
        @endforelse
    </div>

    {{-- Historique paiements --}}
    <div class="card section-gap">
        <div class="card-header">
            <span class="card-title">Historique des paiements</span>
        </div>

        {{-- Mobile --}}
        <div class="mobile-cards" style="padding:12px;">
            @forelse($paiements as $p)
                <div class="mobile-card">
                    <div class="flex-between" style="margin-bottom:8px;">
                        <span class="text-ref">{{ $p->reference_paiement }}</span>
                        <span style="font-size:12px;color:var(--text-2);">{{ \Carbon\Carbon::parse($p->periode)->translatedFormat('M Y') }}</span>
                    </div>
                    <div class="mobile-card-row">
                        <span class="mobile-card-label">Bien</span>
                        <span class="mobile-card-value">{{ $p->contrat->bien->reference }}</span>
                    </div>
                    <div class="mobile-card-row">
                        <span class="mobile-card-label">Locataire</span>
                        <span class="mobile-card-value">{{ $p->contrat->locataire->name }}</span>
                    </div>
                    <div style="border-top:1px solid var(--border);margin-top:8px;padding-top:8px;">
                        <div class="mobile-card-row">
                            <span class="mobile-card-label">Loyer</span>
                            <span class="text-money" style="font-weight:700;">{{ number_format($p->montant_encaisse, 0, ',', ' ') }} F</span>
                        </div>
                        <div class="mobile-card-row">
                            <span class="mobile-card-label">Net reçu</span>
                            <span class="text-money" style="color:#16a34a;font-weight:700;">{{ number_format($p->net_proprietaire, 0, ',', ' ') }} F</span>
                        </div>
                    </div>
                    <div style="margin-top:10px;">
                        <a href="{{ route('admin.paiements.pdf', $p) }}" target="_blank"
                           class="btn btn-secondary btn-sm" style="width:100%;justify-content:center;">📄 PDF</a>
                    </div>
                </div>
            @empty
                <div style="text-align:center;padding:32px;color:var(--text-3);font-size:13px;">Aucun paiement enregistré</div>
            @endforelse
        </div>

        {{-- Desktop --}}
        <div class="desktop-table table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Référence</th>
                        <th>Bien</th>
                        <th>Locataire</th>
                        <th>Période</th>
                        <th style="text-align:right;">Loyer</th>
                        <th style="text-align:right;">Commission TTC</th>
                        <th style="text-align:right;">Net reçu</th>
                        <th style="text-align:center;">PDF</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($paiements as $p)
                        <tr>
                            <td><span class="text-ref">{{ $p->reference_paiement }}</span></td>
                            <td style="font-size:13px;">{{ $p->contrat->bien->reference }}</td>
                            <td style="color:var(--text-2);font-size:13px;">{{ $p->contrat->locataire->name }}</td>
                            <td style="color:var(--text-2);font-size:13px;">{{ \Carbon\Carbon::parse($p->periode)->translatedFormat('F Y') }}</td>
                            <td style="text-align:right;" class="text-money">{{ number_format($p->montant_encaisse, 0, ',', ' ') }} F</td>
                            <td style="text-align:right;color:#d97706;" class="text-money">{{ number_format($p->commission_ttc, 0, ',', ' ') }} F</td>
                            <td style="text-align:right;color:#16a34a;font-weight:700;" class="text-money">{{ number_format($p->net_proprietaire, 0, ',', ' ') }} F</td>
                            <td style="text-align:center;">
                                <a href="{{ route('admin.paiements.pdf', $p) }}" target="_blank" class="btn btn-secondary btn-sm">📄 PDF</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align:center;padding:32px;color:var(--text-3);font-size:13px;">Aucun paiement enregistré</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <style>
        @media (min-width: 768px) {
            #user-kpi        { grid-template-columns: repeat(4, 1fr); }
            #coordonnees-grid { grid-template-columns: repeat(4, 1fr); }
        }
    </style>

</x-app-layout>