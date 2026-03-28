<x-app-layout>
    <x-slot name="header">Dashboard Admin — {{ $currentAgency?->name ?? 'BIMO-Tech' }}</x-slot>

    {{-- ── KPI principaux ── --}}
    <div class="kpi-grid section-gap">

        <div class="kpi">
            <div class="kpi-icon" style="background:#eff6ff;">💰</div>
            <div class="kpi-label">Loyers encaissés</div>
            <div class="kpi-value text-money">{{ number_format($stats['total_loyers'], 0, ',', ' ') }} <span style="font-size:13px;font-weight:500;color:var(--text-3);">FCFA</span></div>
            <div class="kpi-sub">{{ $stats['nb_contrats'] }} contrat(s) actif(s)</div>
        </div>

        <div class="kpi">
            <div class="kpi-icon" style="background:#f5f3ff;">💼</div>
            <div class="kpi-label">Commission agence</div>
            <div class="kpi-value text-money" style="color:var(--agency);">{{ number_format($stats['total_commissions'], 0, ',', ' ') }} <span style="font-size:13px;font-weight:500;color:var(--text-3);">HT</span></div>
            <div class="kpi-sub">TTC : {{ number_format($stats['total_commission_ttc'], 0, ',', ' ') }} FCFA</div>
        </div>

        <div class="kpi">
            <div class="kpi-icon" style="background:#fffbeb;">🧾</div>
            <div class="kpi-label">TVA collectée 18%</div>
            <div class="kpi-value text-money" style="color:#d97706;">{{ number_format($stats['total_tva'], 0, ',', ' ') }} <span style="font-size:13px;font-weight:500;color:var(--text-3);">FCFA</span></div>
            <div class="kpi-sub">À reverser à la DGI</div>
        </div>

        <div class="kpi">
            <div class="kpi-icon" style="background:#f0fdf4;">📊</div>
            <div class="kpi-label">Taux d'occupation</div>
            <div class="kpi-value text-money" style="color:#16a34a;">{{ $stats['taux_occupation'] }}%</div>
            <div class="progress"><div class="progress-bar" style="width:{{ $stats['taux_occupation'] }}%;background:#16a34a;"></div></div>
            <div class="kpi-sub">{{ $stats['nb_biens_loues'] }}/{{ $stats['nb_biens'] }} biens loués</div>
        </div>

    </div>

    {{-- ── Stats secondaires ── --}}
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:10px;" class="section-gap">

        <div style="background:#eff6ff;border:1px solid #dbeafe;border-radius:var(--radius);padding:14px 16px;text-align:center;">
            <div style="font-size:22px;font-weight:800;color:#2563eb;">{{ $stats['nb_proprietaires'] }}</div>
            <div style="font-size:11px;color:#3b82f6;margin-top:3px;font-weight:500;">Propriétaires</div>
        </div>

        <div style="background:#faf5ff;border:1px solid #e9d5ff;border-radius:var(--radius);padding:14px 16px;text-align:center;">
            <div style="font-size:22px;font-weight:800;color:#7c3aed;">{{ $stats['nb_locataires'] }}</div>
            <div style="font-size:11px;color:#8b5cf6;margin-top:3px;font-weight:500;">Locataires</div>
        </div>

        <div style="background:#f0f9ff;border:1px solid #bae6fd;border-radius:var(--radius);padding:14px 16px;text-align:center;">
            <div style="font-size:22px;font-weight:800;color:#0284c7;">{{ $stats['nb_biens'] }}</div>
            <div style="font-size:11px;color:#0ea5e9;margin-top:3px;font-weight:500;">Biens</div>
        </div>

        <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:var(--radius);padding:14px 16px;text-align:center;">
            <div style="font-size:18px;font-weight:800;color:#16a34a;" class="text-money">{{ number_format($stats['total_net_proprio'], 0, ',', ' ') }} F</div>
            <div style="font-size:11px;color:#22c55e;margin-top:3px;font-weight:500;">Net reversé</div>
        </div>

    </div>

    {{-- ── Derniers paiements ── --}}
    <div class="card section-gap">
        <div class="card-header">
            <span class="card-title">Derniers paiements</span>
            <a href="{{ route('admin.paiements.index') }}" class="link">Voir tout →</a>
        </div>

        {{-- Version mobile : cartes --}}
        <div class="mobile-cards" style="padding:12px;">
            @forelse($derniersPaiements as $p)
                <div class="mobile-card">
                    <div class="mobile-card-row">
                        <span class="mobile-card-label">Référence</span>
                        <span class="text-ref">{{ $p->reference_paiement }}</span>
                    </div>
                    <div class="mobile-card-row">
                        <span class="mobile-card-label">Locataire</span>
                        <span class="mobile-card-value">{{ $p->contrat->locataire->name }}</span>
                    </div>
                    <div class="mobile-card-row">
                        <span class="mobile-card-label">Bien</span>
                        <span class="mobile-card-value">{{ $p->contrat->bien->reference }}</span>
                    </div>
                    <div class="mobile-card-row">
                        <span class="mobile-card-label">Période</span>
                        <span class="mobile-card-value">{{ \Carbon\Carbon::parse($p->periode)->translatedFormat('M Y') }}</span>
                    </div>
                    <div class="mobile-card-row" style="margin-top:6px;padding-top:8px;border-top:1px solid var(--border);">
                        <span class="mobile-card-label">Montant</span>
                        <span style="font-weight:700;font-size:14px;" class="text-money">{{ number_format($p->montant_encaisse, 0, ',', ' ') }} F</span>
                    </div>
                    <div class="mobile-card-row">
                        <span class="mobile-card-label">Net proprio</span>
                        <span style="font-weight:700;color:#16a34a;" class="text-money">{{ number_format($p->net_proprietaire, 0, ',', ' ') }} F</span>
                    </div>
                </div>
            @empty
                <div style="text-align:center;padding:32px;color:var(--text-3);font-size:13px;">
                    Aucun paiement enregistré
                </div>
            @endforelse
        </div>

        {{-- Version desktop : tableau --}}
        <div class="desktop-table table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Réf</th>
                        <th>Locataire</th>
                        <th>Bien</th>
                        <th>Période</th>
                        <th style="text-align:right;">Montant</th>
                        <th style="text-align:right;">Net proprio</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($derniersPaiements as $p)
                        <tr>
                            <td><span class="text-ref">{{ $p->reference_paiement }}</span></td>
                            <td>{{ $p->contrat->locataire->name }}</td>
                            <td style="color:var(--text-2);">{{ $p->contrat->bien->reference }}</td>
                            <td style="color:var(--text-2);">{{ \Carbon\Carbon::parse($p->periode)->translatedFormat('M Y') }}</td>
                            <td style="text-align:right;" class="text-money">{{ number_format($p->montant_encaisse, 0, ',', ' ') }} F</td>
                            <td style="text-align:right;color:#16a34a;" class="text-money">{{ number_format($p->net_proprietaire, 0, ',', ' ') }} F</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center;padding:32px;color:var(--text-3);">
                                Aucun paiement enregistré
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ── Graphique loyers par mois ── --}}
    @if($loyersParMois->count() > 0)
    <div class="card">
        <div class="card-header">
            <span class="card-title">Évolution des loyers — 6 derniers mois</span>
        </div>
        <div class="card-body">
            @php
                $maxVal = $loyersParMois->max('total') ?: 1;
            @endphp
            <div style="display:flex;align-items:flex-end;gap:8px;height:120px;">
                @foreach($loyersParMois as $mois)
                    @php
                        $hauteur = round(($mois->total / $maxVal) * 100);
                        $label   = \Carbon\Carbon::createFromFormat('Y-m', $mois->mois)->translatedFormat('M');
                    @endphp
                    <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:6px;height:100%;">
                        <div style="flex:1;display:flex;align-items:flex-end;width:100%;">
                            <div style="width:100%;height:{{ $hauteur }}%;background:var(--agency);border-radius:4px 4px 0 0;opacity:.85;transition:opacity .2s;"
                                 title="{{ number_format($mois->total, 0, ',', ' ') }} FCFA">
                            </div>
                        </div>
                        <span style="font-size:10px;color:var(--text-3);font-weight:500;">{{ $label }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

</x-app-layout>