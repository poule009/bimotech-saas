<x-app-layout>
    <x-slot name="header">Rapport financier</x-slot>

    {{-- Header + Filtre --}}
    <div class="flex-between section-gap" style="flex-wrap:wrap;gap:12px;">
        <div>
            <h1 style="font-size:20px;font-weight:700;color:var(--text);letter-spacing:-.3px;">Rapport financier</h1>
            <p style="font-size:13px;color:var(--text-3);margin-top:3px;">{{ $debutMois->translatedFormat('F Y') }}</p>
        </div>
        <form method="GET" action="{{ route('admin.rapports.financier') }}"
              style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
            <select name="mois" style="border:1.5px solid var(--border);border-radius:var(--radius-sm);padding:8px 12px;font-size:13px;color:var(--text);background:var(--surface);outline:none;font-family:inherit;cursor:pointer;">
                @foreach(range(1, 12) as $m)
                    <option value="{{ $m }}" {{ $mois == $m ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create(null, $m)->translatedFormat('F') }}
                    </option>
                @endforeach
            </select>
            <select name="annee" style="border:1.5px solid var(--border);border-radius:var(--radius-sm);padding:8px 12px;font-size:13px;color:var(--text);background:var(--surface);outline:none;font-family:inherit;cursor:pointer;">
                @foreach($anneesDisponibles as $a)
                    <option value="{{ $a }}" {{ $annee == $a ? 'selected' : '' }}>{{ $a }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-primary btn-sm">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:14px;height:14px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                </svg>
                Filtrer
            </button>
        </form>
    </div>

    {{-- KPI du mois --}}
    <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px;" class="section-gap" id="rapport-kpi">
        <div class="kpi">
            <div class="kpi-label">Loyers encaissés</div>
            <div class="kpi-value text-money" style="font-size:18px;">{{ number_format($kpiMois['total_loyers'], 0, ',', ' ') }} <span style="font-size:12px;font-weight:500;color:var(--text-3);">F</span></div>
            <div class="kpi-sub">{{ $kpiMois['nb_paiements'] }} paiements</div>
        </div>
        <div class="kpi">
            <div class="kpi-label">Commission HT</div>
            <div class="kpi-value text-money" style="color:var(--agency);font-size:18px;">{{ number_format($kpiMois['total_commission'], 0, ',', ' ') }} <span style="font-size:12px;font-weight:500;color:var(--text-3);">F</span></div>
        </div>
        <div class="kpi" style="border-color:#fde68a;background:#fffbeb;">
            <div class="kpi-label" style="color:#f59e0b;">TVA à reverser DGI</div>
            <div class="kpi-value text-money" style="color:#d97706;font-size:18px;">{{ number_format($kpiMois['total_tva'], 0, ',', ' ') }} <span style="font-size:12px;font-weight:500;color:#f59e0b;">F</span></div>
            <div class="kpi-sub" style="color:#f59e0b;">18% sur commission</div>
        </div>
        <div class="kpi">
            <div class="kpi-label">Commission TTC</div>
            <div class="kpi-value text-money" style="font-size:18px;">{{ number_format($kpiMois['total_ttc'], 0, ',', ' ') }} <span style="font-size:12px;font-weight:500;color:var(--text-3);">F</span></div>
        </div>
        <div class="kpi" style="border-color:#bbf7d0;background:#f0fdf4;grid-column:span 2;">
            <div class="kpi-label" style="color:#22c55e;">Net propriétaires</div>
            <div class="kpi-value text-money" style="color:#16a34a;font-size:22px;">{{ number_format($kpiMois['total_net_proprio'], 0, ',', ' ') }} <span style="font-size:13px;font-weight:500;color:#22c55e;">FCFA</span></div>
        </div>
    </div>

    {{-- Alerte impayés --}}
    @if($biensImpayés->isNotEmpty())
        <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:var(--radius);padding:20px 24px;" class="section-gap">
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:14px;">
                <span style="font-size:16px;">⚠️</span>
                <span style="font-size:14px;font-weight:700;color:#dc2626;">
                    {{ $biensImpayés->count() }} contrat(s) sans paiement ce mois
                </span>
            </div>
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:10px;">
                @foreach($biensImpayés as $contrat)
                    <div style="background:white;border:1px solid #fecaca;border-radius:var(--radius-sm);padding:12px;">
                        <div style="font-weight:700;font-size:13px;color:var(--text);margin-bottom:2px;">{{ $contrat->bien->reference }}</div>
                        <div style="font-size:12px;color:var(--text-2);">{{ $contrat->locataire->name }}</div>
                        <div style="font-size:11px;color:var(--text-3);">{{ $contrat->locataire->telephone ?? $contrat->locataire->email }}</div>
                        <div style="font-weight:700;font-size:13px;color:#dc2626;margin-top:6px;" class="text-money">
                            {{ number_format($contrat->loyer_contractuel, 0, ',', ' ') }} F attendu
                        </div>
                        <a href="{{ route('admin.paiements.create', ['contrat_id' => $contrat->id]) }}"
                           style="font-size:12px;color:var(--agency);font-weight:600;display:inline-block;margin-top:6px;">
                            Enregistrer paiement →
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Évolution 12 mois --}}
    <div class="card section-gap">
        <div class="card-header">
            <span class="card-title">Évolution sur 12 mois</span>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Mois</th>
                        <th style="text-align:right;">Paiements</th>
                        <th style="text-align:right;">Loyers</th>
                        <th style="text-align:right;">Commission HT</th>
                        <th style="text-align:right;">TVA</th>
                        <th style="text-align:right;">Commission TTC</th>
                        <th style="text-align:right;">Net proprio</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($evolution as $ligne)
                        <tr style="{{ $ligne->mois === now()->format('Y-m') ? 'background:#f0f4ff;' : '' }}">
                            <td style="font-weight:600;font-size:13px;">
                                {{ $ligne->mois_label }}
                                @if($ligne->mois === now()->format('Y-m'))
                                    <span class="badge badge-blue" style="margin-left:6px;font-size:10px;">En cours</span>
                                @endif
                            </td>
                            <td style="text-align:right;color:var(--text-2);font-size:13px;">{{ $ligne->nb_paiements }}</td>
                            <td style="text-align:right;font-weight:600;" class="text-money">{{ number_format($ligne->loyers, 0, ',', ' ') }} F</td>
                            <td style="text-align:right;color:var(--agency);" class="text-money">{{ number_format($ligne->commissions, 0, ',', ' ') }} F</td>
                            <td style="text-align:right;color:#d97706;" class="text-money">{{ number_format($ligne->tva, 0, ',', ' ') }} F</td>
                            <td style="text-align:right;color:var(--text-2);" class="text-money">{{ number_format($ligne->ttc, 0, ',', ' ') }} F</td>
                            <td style="text-align:right;color:#16a34a;font-weight:700;" class="text-money">{{ number_format($ligne->net, 0, ',', ' ') }} F</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align:center;padding:32px;color:var(--text-3);font-size:13px;">Aucune donnée disponible</td>
                        </tr>
                    @endforelse
                </tbody>
                @if($evolution->isNotEmpty())
                    <tfoot>
                        <tr style="background:#0f172a;">
                            <td style="padding:12px 16px;font-weight:700;color:white;font-size:13px;">TOTAL</td>
                            <td style="padding:12px 16px;text-align:right;font-weight:700;color:white;font-size:13px;">{{ $evolution->sum('nb_paiements') }}</td>
                            <td style="padding:12px 16px;text-align:right;font-weight:700;color:white;font-size:13px;" class="text-money">{{ number_format($evolution->sum('loyers'), 0, ',', ' ') }} F</td>
                            <td style="padding:12px 16px;text-align:right;font-weight:700;color:#a5b4fc;font-size:13px;" class="text-money">{{ number_format($evolution->sum('commissions'), 0, ',', ' ') }} F</td>
                            <td style="padding:12px 16px;text-align:right;font-weight:700;color:#fcd34d;font-size:13px;" class="text-money">{{ number_format($evolution->sum('tva'), 0, ',', ' ') }} F</td>
                            <td style="padding:12px 16px;text-align:right;font-weight:700;color:white;font-size:13px;" class="text-money">{{ number_format($evolution->sum('ttc'), 0, ',', ' ') }} F</td>
                            <td style="padding:12px 16px;text-align:right;font-weight:700;color:#6ee7b7;font-size:13px;" class="text-money">{{ number_format($evolution->sum('net'), 0, ',', ' ') }} F</td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>

    {{-- Par propriétaire --}}
    @if($parProprietaire->isNotEmpty())
        <div class="card section-gap">
            <div class="card-header">
                <span class="card-title">Détail par propriétaire — {{ $debutMois->translatedFormat('F Y') }}</span>
            </div>
            @foreach($parProprietaire as $data)
                <div style="padding:16px 20px;border-bottom:1px solid var(--border);">
                    <div class="flex-between" style="margin-bottom:12px;flex-wrap:wrap;gap:8px;">
                        <div>
                            <div style="font-weight:700;font-size:14px;color:var(--text);">{{ $data['proprio']->name }}</div>
                            <div style="font-size:12px;color:var(--text-3);">{{ $data['proprio']->email }}</div>
                        </div>
                        <div style="text-align:right;">
                            <div style="font-weight:800;font-size:15px;color:#16a34a;" class="text-money">
                                Net : {{ number_format($data['net'], 0, ',', ' ') }} F
                            </div>
                            <div style="font-size:12px;color:var(--text-3);" class="text-money">
                                Commission TTC : {{ number_format($data['commission'], 0, ',', ' ') }} F
                            </div>
                        </div>
                    </div>
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Bien</th>
                                    <th>Locataire</th>
                                    <th style="text-align:right;">Loyer</th>
                                    <th style="text-align:right;">Commission TTC</th>
                                    <th style="text-align:right;">Net</th>
                                    <th style="text-align:center;">PDF</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data['paiements'] as $p)
                                    <tr>
                                        <td style="font-size:13px;font-weight:500;">{{ $p->contrat->bien->reference }}</td>
                                        <td style="font-size:13px;color:var(--text-2);">{{ $p->contrat->locataire->name }}</td>
                                        <td style="text-align:right;font-weight:600;" class="text-money">{{ number_format($p->montant_encaisse, 0, ',', ' ') }} F</td>
                                        <td style="text-align:right;color:#d97706;" class="text-money">{{ number_format($p->commission_ttc, 0, ',', ' ') }} F</td>
                                        <td style="text-align:right;color:#16a34a;font-weight:700;" class="text-money">{{ number_format($p->net_proprietaire, 0, ',', ' ') }} F</td>
                                        <td style="text-align:center;">
                                            <a href="{{ route('admin.paiements.pdf', $p) }}" target="_blank"
                                               class="btn btn-secondary btn-sm">📄</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Stats générales du parc --}}
    <div class="card section-gap">
        <div class="card-header">
            <span class="card-title">État général du parc</span>
        </div>
        <div class="card-body">
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-bottom:20px;" id="parc-grid">
                <div style="text-align:center;padding:14px;background:var(--bg);border-radius:var(--radius-sm);">
                    <div style="font-size:26px;font-weight:800;color:var(--text);">{{ $statsGenerales['nb_biens'] }}</div>
                    <div style="font-size:11px;color:var(--text-3);margin-top:3px;">Biens total</div>
                </div>
                <div style="text-align:center;padding:14px;background:#f0fdf4;border-radius:var(--radius-sm);">
                    <div style="font-size:26px;font-weight:800;color:#16a34a;">{{ $statsGenerales['nb_biens_loues'] }}</div>
                    <div style="font-size:11px;color:#22c55e;margin-top:3px;">Biens loués</div>
                </div>
                <div style="text-align:center;padding:14px;background:var(--bg);border-radius:var(--radius-sm);">
                    @php
                        $taux = $statsGenerales['taux_occupation'];
                        $tc = $taux >= 80 ? '#16a34a' : ($taux >= 50 ? '#d97706' : '#dc2626');
                    @endphp
                    <div style="font-size:26px;font-weight:800;color:{{ $tc }};">{{ $taux }}%</div>
                    <div style="font-size:11px;color:var(--text-3);margin-top:3px;">Taux occupation</div>
                </div>
                <div style="text-align:center;padding:14px;background:#eff6ff;border-radius:var(--radius-sm);">
                    <div style="font-size:26px;font-weight:800;color:#2563eb;">{{ $statsGenerales['nb_contrats'] }}</div>
                    <div style="font-size:11px;color:#3b82f6;margin-top:3px;">Contrats actifs</div>
                </div>
                <div style="text-align:center;padding:14px;background:#faf5ff;border-radius:var(--radius-sm);">
                    <div style="font-size:26px;font-weight:800;color:#7c3aed;">{{ $statsGenerales['nb_proprietaires'] }}</div>
                    <div style="font-size:11px;color:#8b5cf6;margin-top:3px;">Propriétaires</div>
                </div>
                <div style="text-align:center;padding:14px;background:#fdf4ff;border-radius:var(--radius-sm);">
                    <div style="font-size:26px;font-weight:800;color:#c026d3;">{{ $statsGenerales['nb_locataires'] }}</div>
                    <div style="font-size:11px;color:#d946ef;margin-top:3px;">Locataires</div>
                </div>
            </div>

            {{-- Barre taux occupation --}}
            <div>
                <div style="display:flex;justify-content:space-between;font-size:12px;color:var(--text-3);margin-bottom:6px;">
                    <span>Taux d'occupation</span>
                    <span style="font-weight:600;color:{{ $tc }};">{{ $taux }}%</span>
                </div>
                <div class="progress" style="height:8px;">
                    <div class="progress-bar" style="width:{{ $taux }}%;background:{{ $tc }};"></div>
                </div>
            </div>
        </div>
    </div>

    <style>
        @media (min-width: 768px) {
            #rapport-kpi { grid-template-columns: repeat(5, 1fr); }
            #rapport-kpi > *:last-child { grid-column: auto; }
            #parc-grid { grid-template-columns: repeat(6, 1fr); }
        }
    </style>

</x-app-layout>