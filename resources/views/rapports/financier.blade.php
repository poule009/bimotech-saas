<x-app-layout>
    <x-slot name="header">Rapport financier</x-slot>

<style>
/* ── KPI ── */
.kpi-row { display:grid; grid-template-columns:repeat(5,1fr); gap:14px; margin-bottom:22px; }
.kpi-mini { background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:18px 20px; position:relative; overflow:hidden; }
.kpi-mini::before { content:''; position:absolute; top:0;left:0;right:0; height:3px; border-radius:12px 12px 0 0; }
.kpi-mini.gold::before   { background:#c9a84c; }
.kpi-mini.green::before  { background:#16a34a; }
.kpi-mini.blue::before   { background:#1d4ed8; }
.kpi-mini.orange::before { background:#d97706; }
.kpi-mini.red::before    { background:#dc2626; }
.kpi-lbl { font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:.8px;color:#6b7280;margin-bottom:5px; }
.kpi-val { font-family:'Syne',sans-serif;font-size:18px;font-weight:700;color:#0d1117;letter-spacing:-.3px;line-height:1; }
.kpi-val.green { color:#16a34a; } .kpi-val.gold { color:#8a6e2f; }
.kpi-val.orange { color:#d97706; } .kpi-val.red { color:#dc2626; }
.kpi-u { font-size:11px;font-weight:400;color:#9ca3af;margin-left:2px; }
.kpi-s { font-size:11px;color:#9ca3af;margin-top:5px; }

/* ── FILTRE BAR ── */
.filter-bar { background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:14px 18px;margin-bottom:22px;display:flex;align-items:center;gap:10px;flex-wrap:wrap; }
.filter-select { padding:8px 13px;border:1px solid #e5e7eb;border-radius:8px;font-size:13px;color:#0d1117;font-family:'DM Sans',sans-serif;background:#f9fafb;outline:none;cursor:pointer; }
.filter-btn { padding:8px 16px;background:#0d1117;color:#fff;border:none;border-radius:8px;font-size:12px;font-weight:500;font-family:'DM Sans',sans-serif;cursor:pointer;white-space:nowrap; }
.btn-outline { display:flex;align-items:center;gap:6px;padding:8px 16px;background:#fff;color:#374151;border:1px solid #e5e7eb;border-radius:8px;font-size:12px;font-weight:500;font-family:'DM Sans',sans-serif;cursor:pointer;text-decoration:none;transition:all .15s; }
.btn-outline:hover { border-color:#c9a84c;color:#8a6e2f; }
.btn-outline svg { width:13px;height:13px; }

/* ── BILAN DARK ── */
.bilan { background:#0d1117;border-radius:16px;padding:28px 32px;margin-bottom:24px;display:grid;grid-template-columns:1fr 1fr 1fr 1fr;position:relative;overflow:hidden; }
.bilan::before { content:'';position:absolute;top:-60px;right:-60px;width:200px;height:200px;border-radius:50%;background:rgba(201,168,76,0.07); }
.bilan-col { padding:0 24px;border-right:1px solid rgba(255,255,255,.07);position:relative;z-index:1; }
.bilan-col:first-child { padding-left:0; }
.bilan-col:last-child  { padding-right:0;border-right:none; }
.bilan-lbl { font-size:10px;font-weight:600;letter-spacing:1.5px;text-transform:uppercase;color:rgba(255,255,255,.4);margin-bottom:8px; }
.bilan-val { font-family:'Syne',sans-serif;font-size:22px;font-weight:700;letter-spacing:-.5px;line-height:1;color:white; }
.bilan-val.green { color:#4ade80; } .bilan-val.gold { color:#c9a84c; }
.bilan-val.orange { color:#fbbf24; } .bilan-val.red { color:#f87171; }
.bilan-u { font-size:12px;color:rgba(255,255,255,.3);margin-left:3px; }
.bilan-s { font-size:11px;color:rgba(255,255,255,.3);margin-top:6px; }

/* ── CARDS ── */
.card { background:#fff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden;margin-bottom:20px; }
.card:last-child { margin-bottom:0; }
.card-hd { padding:16px 22px;border-bottom:1px solid #e5e7eb;display:flex;align-items:center;justify-content:space-between; }
.card-title { font-family:'Syne',sans-serif;font-size:14px;font-weight:700;color:#0d1117; }
.card-action { font-size:12px;color:#6b7280;text-decoration:none; }

/* ── GRILLES ── */
.g2 { display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px; }
.g3 { display:grid;grid-template-columns:2fr 1fr;gap:20px;margin-bottom:20px; }

/* ── TABLE ── */
.dt { width:100%;border-collapse:collapse; }
.dt th { padding:10px 18px;text-align:left;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#9ca3af;background:#f9fafb;border-bottom:1px solid #e5e7eb;white-space:nowrap; }
.dt td { padding:13px 18px;font-size:13px;color:#374151;border-bottom:1px solid #f3f4f6;vertical-align:middle; }
.dt tbody tr:last-child td { border-bottom:none; }
.dt tbody tr:hover { background:#f9fafb; }
.dt tfoot td { padding:12px 18px;font-family:'Syne',sans-serif;font-weight:700;color:#0d1117;background:#f9fafb;border-top:2px solid #e5e7eb; }
.th-r { text-align:right !important; }
.td-r  { text-align:right; }
.td-c  { text-align:center; }

/* colonnes spécifiques */
.ref-tag { font-family:'Syne',sans-serif;font-size:11px;font-weight:600;color:#9ca3af; }
.amt-main { font-family:'Syne',sans-serif;font-weight:600;color:#0d1117; }
.amt-green { font-family:'Syne',sans-serif;font-weight:600;color:#16a34a; }
.amt-gold  { font-family:'Syne',sans-serif;font-weight:600;color:#8a6e2f; }
.amt-red   { font-family:'Syne',sans-serif;font-weight:600;color:#d97706; }
.proprio-name { font-size:13px;font-weight:600;color:#0d1117; }
.proprio-count { font-size:11px;color:#6b7280;margin-top:1px; }

/* badge */
.badge { display:inline-flex;align-items:center;gap:4px;padding:3px 9px;border-radius:99px;font-size:11px;font-weight:600; }
.badge.g { background:#dcfce7;color:#16a34a; }
.badge.o { background:#f5e9c9;color:#8a6e2f; }
.bdot { width:5px;height:5px;border-radius:50%;background:currentColor; }

/* alerte impayés */
.impaye-alert { background:#fef2f2;border:1px solid #fecaca;border-radius:14px;padding:20px;margin-bottom:20px; }
.impaye-alert-title { font-family:'Syne',sans-serif;font-size:14px;font-weight:700;color:#dc2626;margin-bottom:14px;display:flex;align-items:center;gap:8px; }
.impaye-grid { display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:10px; }
.impaye-item { background:#fff;border:1px solid #fecaca;border-radius:9px;padding:12px 14px; }
.impaye-item-ref  { font-size:13px;font-weight:600;color:#0d1117; }
.impaye-item-name { font-size:11px;color:#6b7280;margin-top:2px; }
.impaye-item-amt  { font-family:'Syne',sans-serif;font-size:13px;font-weight:700;color:#dc2626;margin-top:6px; }

/* pagination */
.pagination-wrap { padding:14px 18px;border-top:1px solid #e5e7eb;display:flex;align-items:center;justify-content:space-between; }
.pagination-info { font-size:12px;color:#6b7280; }
.pagination-links { display:flex;gap:4px; }
.page-btn { display:inline-flex;align-items:center;justify-content:center;min-width:32px;height:32px;padding:0 10px;border-radius:7px;border:1px solid #e5e7eb;background:#fff;color:#374151;font-size:12px;font-weight:500;text-decoration:none;transition:all .15s; }
.page-btn.active { background:#0d1117;color:#fff;border-color:#0d1117; }
.page-btn.disabled { opacity:.4;pointer-events:none; }

/* graphiques */
.chart-meta { padding:16px 22px 8px;display:flex;align-items:baseline;justify-content:space-between; }
.chart-num  { font-family:'Syne',sans-serif;font-size:20px;font-weight:700;color:#0d1117;letter-spacing:-.5px; }
.chart-sub  { font-size:11px;color:#9ca3af;margin-top:2px; }
.donut-legend { display:flex;flex-wrap:wrap;gap:8px 16px;padding:8px 22px 16px; }
.donut-leg { display:flex;align-items:center;gap:6px;font-size:12px;color:#6b7280; }
.donut-dot { width:10px;height:10px;border-radius:3px;flex-shrink:0; }

/* proprio accordion */
.proprio-row { border-bottom:1px solid #f3f4f6;cursor:pointer;transition:background .1s; }
.proprio-row:hover { background:#f9fafb; }
.proprio-row:last-child { border-bottom:none; }
.proprio-row td { padding:14px 18px;vertical-align:middle; }
.proprio-detail { display:none;background:#f9fafb; }
.proprio-detail.open { display:table-row-group; }
.proprio-detail td { padding:10px 18px;font-size:12px;color:#6b7280;border-bottom:1px solid #f3f4f6; }
.proprio-detail tr:last-child td { border-bottom:none; }

/* stats générales mini */
.stats-grid { display:grid;grid-template-columns:repeat(3,1fr);gap:12px;padding:20px; }
.stat-item { text-align:center;padding:14px;background:#f9fafb;border-radius:9px; }
.stat-val { font-family:'Syne',sans-serif;font-size:20px;font-weight:700;color:#0d1117; }
.stat-lbl { font-size:11px;color:#6b7280;margin-top:4px; }
</style>

<div style="padding:24px 32px 48px">

    {{-- PAGE HEADER --}}
    <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:22px">
        <div>
            <h1 style="font-family:'Syne',sans-serif;font-size:22px;font-weight:700;color:#0d1117;letter-spacing:-.4px">Rapport financier</h1>
            <p style="font-size:13px;color:#6b7280;margin-top:3px">
                {{ $debutMois->translatedFormat('F Y') }} · {{ $currentAgency->name ?? 'Votre agence' }}
            </p>
        </div>
        <a href="{{ route('admin.rapports.financier.export-pdf', ['mois' => $mois, 'annee' => $annee]) }}"
           class="btn-outline">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            Exporter PDF
        </a>
    </div>

    {{-- FILTRE --}}
    <form method="GET" action="{{ route('admin.rapports.financier') }}">
        <div class="filter-bar">
            <span style="font-size:12px;color:#6b7280;font-weight:500">Période :</span>
            <select name="mois" class="filter-select">
                @foreach(range(1,12) as $m)
                    <option value="{{ $m }}" {{ $mois == $m ? 'selected':'' }}>
                        {{ \Carbon\Carbon::create(null,$m)->translatedFormat('F') }}
                    </option>
                @endforeach
            </select>
            <select name="annee" class="filter-select">
                @foreach($anneesDisponibles as $a)
                    <option value="{{ $a }}" {{ $annee == $a ? 'selected':'' }}>{{ $a }}</option>
                @endforeach
            </select>
            <button type="submit" class="filter-btn">Afficher</button>
            <div style="flex:1"></div>
            {{-- Navigation mois --}}
            @php
                $prevM = $mois == 1 ? 12 : $mois-1;
                $prevA = $mois == 1 ? $annee-1 : $annee;
                $nextM = $mois == 12 ? 1 : $mois+1;
                $nextA = $mois == 12 ? $annee+1 : $annee;
            @endphp
            <a href="{{ route('admin.rapports.financier', ['mois'=>$prevM,'annee'=>$prevA]) }}"
               class="btn-outline">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                Mois préc.
            </a>
            @if(!($mois == now()->month && $annee == now()->year))
            <a href="{{ route('admin.rapports.financier', ['mois'=>$nextM,'annee'=>$nextA]) }}"
               class="btn-outline">
                Mois suiv.
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            </a>
            @endif
        </div>
    </form>

    {{-- KPI ROW --}}
    <div class="kpi-row">
        <div class="kpi-mini gold">
            <div class="kpi-lbl">Loyers encaissés</div>
            <div class="kpi-val gold">{{ number_format($kpiMois['total_loyers'], 0, ',', ' ') }}<span class="kpi-u">F</span></div>
            <div class="kpi-s">{{ $kpiMois['nb_paiements'] }} paiements</div>
        </div>
        <div class="kpi-mini green">
            <div class="kpi-lbl">Net propriétaires</div>
            <div class="kpi-val green">{{ number_format($kpiMois['total_net_proprio'], 0, ',', ' ') }}<span class="kpi-u">F</span></div>
            <div class="kpi-s">Après commission</div>
        </div>
        <div class="kpi-mini blue">
            <div class="kpi-lbl">Commission HT</div>
            <div class="kpi-val" style="color:#1d4ed8">{{ number_format($kpiMois['total_commission'], 0, ',', ' ') }}<span class="kpi-u">F</span></div>
            <div class="kpi-s">Revenu agence</div>
        </div>
        <div class="kpi-mini orange">
            <div class="kpi-lbl">TVA à reverser DGI</div>
            <div class="kpi-val orange">{{ number_format($kpiMois['total_tva'], 0, ',', ' ') }}<span class="kpi-u">F</span></div>
            <div class="kpi-s">18% sur commission</div>
        </div>
        <div class="kpi-mini red">
            <div class="kpi-lbl">Commission TTC</div>
            <div class="kpi-val" style="color:#0d1117">{{ number_format($kpiMois['total_ttc'], 0, ',', ' ') }}<span class="kpi-u">F</span></div>
            <div class="kpi-s">HT + TVA</div>
        </div>
    </div>

    {{-- BILAN DARK --}}
    <div class="bilan">
        <div class="bilan-col">
            <div class="bilan-lbl">Total encaissé</div>
            <div class="bilan-val gold">{{ number_format($kpiMois['total_loyers'], 0, ',', ' ') }}<span class="bilan-u">F</span></div>
            <div class="bilan-s">{{ $kpiMois['nb_paiements'] }} paiements validés</div>
        </div>
        <div class="bilan-col">
            <div class="bilan-lbl">Net reversé aux propriétaires</div>
            <div class="bilan-val green">{{ number_format($kpiMois['total_net_proprio'], 0, ',', ' ') }}<span class="bilan-u">F</span></div>
            <div class="bilan-s">Après déduction commission</div>
        </div>
        <div class="bilan-col">
            <div class="bilan-lbl">Commission TTC agence</div>
            <div class="bilan-val orange">{{ number_format($kpiMois['total_ttc'], 0, ',', ' ') }}<span class="bilan-u">F</span></div>
            <div class="bilan-s">Dont {{ number_format($kpiMois['total_tva'], 0, ',', ' ') }} F de TVA</div>
        </div>
        <div class="bilan-col">
            <div class="bilan-lbl">Impayés ce mois</div>
            <div class="bilan-val {{ $biensImpayés->isEmpty() ? 'green' : 'red' }}">
                {{ $biensImpayés->count() }}<span class="bilan-u">contrats</span>
            </div>
            <div class="bilan-s">
                @if($biensImpayés->isEmpty()) Recouvrement complet
                @else {{ number_format($biensImpayés->sum('loyer_contractuel'), 0, ',', ' ') }} F à recouvrer
                @endif
            </div>
        </div>
    </div>

    {{-- ALERTE IMPAYÉS --}}
    @if($biensImpayés->isNotEmpty())
    <div class="impaye-alert">
        <div class="impaye-alert-title">
            <svg style="width:16px;height:16px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            {{ $biensImpayés->count() }} contrat(s) sans paiement ce mois
        </div>
        <div class="impaye-grid">
            @foreach($biensImpayés as $c)
            <div class="impaye-item">
                <div class="impaye-item-ref">{{ $c->bien->reference }}</div>
                <div class="impaye-item-name">{{ $c->locataire->name }}</div>
                <div class="impaye-item-amt">{{ number_format($c->loyer_contractuel, 0, ',', ' ') }} F</div>
            </div>
            @endforeach
        </div>
        <div style="margin-top:14px">
            <a href="{{ route('admin.impayes.index', ['mois'=>$mois,'annee'=>$annee]) }}"
               style="font-size:12px;font-weight:600;color:#dc2626;text-decoration:none">
               → Gérer les impayés
            </a>
        </div>
    </div>
    @endif

    {{-- GRAPHIQUES --}}
    <div class="g2">
        {{-- Évolution 12 mois --}}
        <div class="card">
            <div class="card-hd">
                <div>
                    <div class="card-title">Évolution sur 12 mois</div>
                    <div style="font-size:11px;color:#9ca3af;margin-top:2px">Loyers encaissés vs commissions</div>
                </div>
                <div style="display:flex;gap:14px">
                    <div style="display:flex;align-items:center;gap:5px;font-size:11px;color:#9ca3af"><div style="width:10px;height:10px;border-radius:2px;background:#c9a84c"></div>Loyers</div>
                    <div style="display:flex;align-items:center;gap:5px;font-size:11px;color:#9ca3af"><div style="width:10px;height:10px;border-radius:2px;background:#e5e7eb"></div>Commissions</div>
                </div>
            </div>
            <div class="chart-meta">
                <div>
                    <div class="chart-num">{{ number_format($kpiMois['total_loyers'], 0, ',', ' ') }} F</div>
                    <div class="chart-sub">Ce mois · {{ $debutMois->translatedFormat('F Y') }}</div>
                </div>
            </div>
            <div style="position:relative;height:200px;padding:0 20px 20px">
                <canvas id="chartEvolution"></canvas>
            </div>
        </div>

        {{-- Stats générales + donut --}}
        <div class="card">
            <div class="card-hd">
                <div class="card-title">Vue d'ensemble</div>
            </div>
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-val">{{ $statsGenerales['nb_biens'] }}</div>
                    <div class="stat-lbl">Biens gérés</div>
                </div>
                <div class="stat-item">
                    <div class="stat-val" style="color:#16a34a">{{ $statsGenerales['nb_biens_loues'] }}</div>
                    <div class="stat-lbl">Biens loués</div>
                </div>
                <div class="stat-item">
                    <div class="stat-val" style="color:#1d4ed8">{{ $statsGenerales['taux_occupation'] }}%</div>
                    <div class="stat-lbl">Occupation</div>
                </div>
                <div class="stat-item">
                    <div class="stat-val">{{ $statsGenerales['nb_contrats'] }}</div>
                    <div class="stat-lbl">Contrats actifs</div>
                </div>
                <div class="stat-item">
                    <div class="stat-val">{{ $statsGenerales['nb_proprietaires'] }}</div>
                    <div class="stat-lbl">Propriétaires</div>
                </div>
                <div class="stat-item">
                    <div class="stat-val">{{ $statsGenerales['nb_locataires'] }}</div>
                    <div class="stat-lbl">Locataires</div>
                </div>
            </div>
            <div style="position:relative;height:120px;padding:0 20px;display:flex;align-items:center;justify-content:center">
                <canvas id="chartRepartition"></canvas>
            </div>
            <div class="donut-legend" style="justify-content:center">
                <div class="donut-leg"><div class="donut-dot" style="background:#4ade80"></div>Net proprio {{ $kpiMois['total_loyers'] > 0 ? round(($kpiMois['total_net_proprio']/$kpiMois['total_loyers'])*100) : 0 }}%</div>
                <div class="donut-leg"><div class="donut-dot" style="background:#c9a84c"></div>Comm. HT {{ $kpiMois['total_loyers'] > 0 ? round(($kpiMois['total_commission']/$kpiMois['total_loyers'])*100) : 0 }}%</div>
                <div class="donut-leg"><div class="donut-dot" style="background:#fbbf24"></div>TVA {{ $kpiMois['total_loyers'] > 0 ? round(($kpiMois['total_tva']/$kpiMois['total_loyers'])*100) : 0 }}%</div>
            </div>
        </div>
    </div>

    {{-- PAR PROPRIÉTAIRE --}}
    @if($parProprietaire->isNotEmpty())
    <div class="card">
        <div class="card-hd">
            <div class="card-title">Reversements par propriétaire</div>
            <div style="font-size:12px;color:#6b7280">{{ $parProprietaire->count() }} propriétaire(s) · cliquez pour détailler</div>
        </div>
        <div style="overflow-x:auto">
            <table class="dt" id="proprio-table">
                <thead>
                    <tr>
                        <th>Propriétaire</th>
                        <th class="th-r">Loyers bruts</th>
                        <th class="th-r">Commission TTC</th>
                        <th class="th-r">Net reversé</th>
                        <th style="text-align:center">Paiements</th>
                        <th style="text-align:center"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($parProprietaire as $proprioId => $data)
                    @php $toggleId = 'detail-'.$proprioId; @endphp

                    {{-- Ligne principale --}}
                    <tr class="proprio-row" onclick="toggleDetail('{{ $toggleId }}', this)">
                        <td>
                            <div class="proprio-name">{{ $data['proprio']->name }}</div>
                            <div class="proprio-count">{{ $data['nb_paiements'] }} bien(s) ce mois</div>
                        </td>
                        <td class="td-r"><span class="amt-main">{{ number_format($data['loyers'], 0, ',', ' ') }} F</span></td>
                        <td class="td-r"><span class="amt-gold">{{ number_format($data['commission'], 0, ',', ' ') }} F</span></td>
                        <td class="td-r"><span class="amt-green">{{ number_format($data['net'], 0, ',', ' ') }} F</span></td>
                        <td class="td-c">
                            <span class="badge g"><span class="bdot"></span>{{ $data['nb_paiements'] }}</span>
                        </td>
                        <td class="td-c">
                            <svg style="width:14px;height:14px;color:#9ca3af;transition:transform .2s" class="chevron-{{ $proprioId }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                        </td>
                    </tr>

                    {{-- Détail des paiements --}}
                    <tr>
                        <td colspan="6" style="padding:0;border:none">
                            <div id="{{ $toggleId }}" style="display:none;background:#f9fafb;border-top:1px solid #e5e7eb">
                                <table style="width:100%;border-collapse:collapse">
                                    <thead>
                                        <tr style="background:#f3f4f6">
                                            <th style="padding:8px 18px 8px 36px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#9ca3af;text-align:left">Bien</th>
                                            <th style="padding:8px 18px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#9ca3af;text-align:left">Locataire</th>
                                            <th style="padding:8px 18px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#9ca3af;text-align:left">Mode</th>
                                            <th style="padding:8px 18px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#9ca3af;text-align:right">Loyer</th>
                                            <th style="padding:8px 18px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#9ca3af;text-align:right">Comm. TTC</th>
                                            <th style="padding:8px 18px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#9ca3af;text-align:right">Net</th>
                                            <th style="padding:8px 18px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#9ca3af;text-align:center">PDF</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($data['paiements'] as $p)
                                        <tr style="border-bottom:1px solid #e5e7eb">
                                            <td style="padding:10px 18px 10px 36px;font-size:12px;font-weight:500;color:#0d1117">{{ $p->contrat->bien->reference }}</td>
                                            <td style="padding:10px 18px;font-size:12px;color:#6b7280">{{ $p->contrat->locataire->name }}</td>
                                            <td style="padding:10px 18px;font-size:12px;color:#6b7280">{{ \App\Models\Paiement::MODES_PAIEMENT[$p->mode_paiement] ?? $p->mode_paiement }}</td>
                                            <td style="padding:10px 18px;text-align:right;font-family:'Syne',sans-serif;font-weight:600;color:#0d1117;font-size:12px">{{ number_format($p->montant_encaisse, 0, ',', ' ') }} F</td>
                                            <td style="padding:10px 18px;text-align:right;font-family:'Syne',sans-serif;font-weight:600;color:#8a6e2f;font-size:12px">{{ number_format($p->commission_ttc, 0, ',', ' ') }} F</td>
                                            <td style="padding:10px 18px;text-align:right;font-family:'Syne',sans-serif;font-weight:600;color:#16a34a;font-size:12px">{{ number_format($p->net_proprietaire, 0, ',', ' ') }} F</td>
                                            <td style="padding:10px 18px;text-align:center">
                                                <a href="{{ route('admin.paiements.pdf', $p) }}" target="_blank"
                                                   style="display:inline-flex;align-items:center;justify-content:center;width:26px;height:26px;border:1px solid #e5e7eb;border-radius:6px;color:#6b7280;text-decoration:none">
                                                    <svg style="width:12px;height:12px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                                </a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td>TOTAL</td>
                        <td class="td-r">{{ number_format($kpiMois['total_loyers'], 0, ',', ' ') }} F</td>
                        <td class="td-r">{{ number_format($kpiMois['total_ttc'], 0, ',', ' ') }} F</td>
                        <td class="td-r" style="color:#16a34a">{{ number_format($kpiMois['total_net_proprio'], 0, ',', ' ') }} F</td>
                        <td class="td-c">{{ $kpiMois['nb_paiements'] }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    @endif

    {{-- TABLEAU DÉTAIL PAIEMENTS --}}
    <div class="card" style="margin-top:20px">
        <div class="card-hd">
            <div>
                <div class="card-title">Détail de tous les paiements</div>
                <div style="font-size:11px;color:#9ca3af;margin-top:2px">{{ $paiementsMois->total() }} paiement(s) · Page {{ $paiementsMois->currentPage() }} / {{ $paiementsMois->lastPage() }}</div>
            </div>
        </div>
        <div style="overflow-x:auto">
            <table class="dt">
                <thead>
                    <tr>
                        <th>Référence</th>
                        <th>Date</th>
                        <th>Bien</th>
                        <th>Locataire</th>
                        <th>Propriétaire</th>
                        <th>Mode</th>
                        <th class="th-r">Loyer brut</th>
                        <th class="th-r">Comm. HT</th>
                        <th class="th-r">TVA</th>
                        <th class="th-r">Net proprio</th>
                        <th style="text-align:center">PDF</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($paiementsMois as $p)
                    <tr>
                        <td><span class="ref-tag">{{ $p->reference_paiement }}</span></td>
                        <td style="font-size:12px;color:#6b7280">{{ \Carbon\Carbon::parse($p->date_paiement)->format('d/m/Y') }}</td>
                        <td>
                            <div style="font-size:13px;font-weight:500;color:#0d1117">{{ $p->contrat->bien->reference }}</div>
                            <div style="font-size:11px;color:#6b7280">{{ $p->contrat->bien->ville }}</div>
                        </td>
                        <td style="font-size:12px;color:#374151">{{ $p->contrat->locataire->name }}</td>
                        <td style="font-size:12px;color:#374151">{{ $p->contrat->bien->proprietaire->name }}</td>
                        <td style="font-size:12px;color:#6b7280">{{ \App\Models\Paiement::MODES_PAIEMENT[$p->mode_paiement] ?? $p->mode_paiement }}</td>
                        <td class="td-r"><span class="amt-main">{{ number_format($p->montant_encaisse, 0, ',', ' ') }} F</span></td>
                        <td class="td-r"><span class="amt-gold">{{ number_format($p->commission_agence, 0, ',', ' ') }} F</span></td>
                        <td class="td-r"><span class="amt-red">{{ number_format($p->tva_commission, 0, ',', ' ') }} F</span></td>
                        <td class="td-r"><span class="amt-green">{{ number_format($p->net_proprietaire, 0, ',', ' ') }} F</span></td>
                        <td class="td-c">
                            <a href="{{ route('admin.paiements.pdf', $p) }}" target="_blank"
                               style="display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border:1px solid #e5e7eb;border-radius:7px;color:#6b7280;text-decoration:none;transition:all .15s"
                               onmouseover="this.style.borderColor='#c9a84c';this.style.color='#8a6e2f';this.style.background='#f5e9c9'"
                               onmouseout="this.style.borderColor='#e5e7eb';this.style.color='#6b7280';this.style.background='transparent'">
                                <svg style="width:13px;height:13px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" style="text-align:center;padding:40px;color:#9ca3af;font-size:13px">
                            Aucun paiement enregistré pour {{ $debutMois->translatedFormat('F Y') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if($paiementsMois->count() > 0)
                <tfoot>
                    <tr>
                        <td colspan="6">TOTAUX</td>
                        <td class="td-r">{{ number_format($kpiMois['total_loyers'], 0, ',', ' ') }} F</td>
                        <td class="td-r">{{ number_format($kpiMois['total_commission'], 0, ',', ' ') }} F</td>
                        <td class="td-r">{{ number_format($kpiMois['total_tva'], 0, ',', ' ') }} F</td>
                        <td class="td-r" style="color:#16a34a">{{ number_format($kpiMois['total_net_proprio'], 0, ',', ' ') }} F</td>
                        <td></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>

        {{-- PAGINATION --}}
        @if($paiementsMois->hasPages())
        <div class="pagination-wrap">
            <div class="pagination-info">
                Affichage de {{ $paiementsMois->firstItem() }} à {{ $paiementsMois->lastItem() }} sur {{ $paiementsMois->total() }} paiements
            </div>
            <div class="pagination-links">
                @if($paiementsMois->onFirstPage())
                    <span class="page-btn disabled"><svg style="width:14px;height:14px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg></span>
                @else
                    <a href="{{ $paiementsMois->previousPageUrl() }}" class="page-btn"><svg style="width:14px;height:14px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg></a>
                @endif
                @foreach($paiementsMois->getUrlRange(max(1,$paiementsMois->currentPage()-2), min($paiementsMois->lastPage(),$paiementsMois->currentPage()+2)) as $page => $url)
                    <a href="{{ $url }}" class="page-btn {{ $page === $paiementsMois->currentPage() ? 'active':'' }}">{{ $page }}</a>
                @endforeach
                @if($paiementsMois->hasMorePages())
                    <a href="{{ $paiementsMois->nextPageUrl() }}" class="page-btn"><svg style="width:14px;height:14px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg></a>
                @else
                    <span class="page-btn disabled"><svg style="width:14px;height:14px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg></span>
                @endif
            </div>
        </div>
        @endif
    </div>

</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js"></script>
<script>
Chart.defaults.font.family = "'DM Sans', sans-serif";
Chart.defaults.color = '#9ca3af';

const tooltipStyle = {
    backgroundColor:'#0d1117', titleColor:'#fff', bodyColor:'#9ca3af',
    borderColor:'#1c2333', borderWidth:1, padding:11, cornerRadius:8,
};

// ── 1. ÉVOLUTION 12 MOIS ─────────────────────────────────────────────
const ctx1 = document.getElementById('chartEvolution').getContext('2d');
const gradGold = ctx1.createLinearGradient(0,0,0,180);
gradGold.addColorStop(0,'rgba(201,168,76,0.22)');
gradGold.addColorStop(1,'rgba(201,168,76,0.00)');
const gradGray = ctx1.createLinearGradient(0,0,0,180);
gradGray.addColorStop(0,'rgba(229,231,235,0.7)');
gradGray.addColorStop(1,'rgba(229,231,235,0.0)');

new Chart(ctx1, {
    type:'line',
    data:{
        labels: @json($evolution->pluck('mois_label')),
        datasets:[
            {
                label:'Loyers',
                data: @json($evolution->pluck('loyers')),
                borderColor:'#c9a84c', backgroundColor:gradGold,
                borderWidth:2.5, fill:true, tension:0.4,
                pointBackgroundColor:'#c9a84c', pointBorderColor:'#fff',
                pointBorderWidth:2, pointRadius:4, pointHoverRadius:6,
            },
            {
                label:'Commissions TTC',
                data: @json($evolution->pluck('ttc')),
                borderColor:'#d1d5db', backgroundColor:gradGray,
                borderWidth:2, fill:true, tension:0.4,
                pointBackgroundColor:'#d1d5db', pointBorderColor:'#fff',
                pointBorderWidth:2, pointRadius:3, pointHoverRadius:5,
            }
        ]
    },
    options:{
        responsive:true, maintainAspectRatio:false,
        plugins:{
            legend:{display:false},
            tooltip:{...tooltipStyle, callbacks:{label:c=>' '+Number(c.parsed.y).toLocaleString('fr-FR')+' F'}}
        },
        scales:{
            x:{grid:{display:false}, border:{display:false}, ticks:{font:{size:11}}},
            y:{
                grid:{color:'#f3f4f6', drawTicks:false}, border:{display:false},
                ticks:{font:{size:10}, callback:v=>(v/1000000).toFixed(1)+'M'}
            }
        }
    }
});

// ── 2. RÉPARTITION DONUT ─────────────────────────────────────────────
new Chart(document.getElementById('chartRepartition'), {
    type:'doughnut',
    data:{
        labels:['Net propriétaires','Commission HT','TVA'],
        datasets:[{
            data:[
                {{ $kpiMois['total_net_proprio'] }},
                {{ $kpiMois['total_commission'] }},
                {{ $kpiMois['total_tva'] }}
            ],
            backgroundColor:['#4ade80','#c9a84c','#fbbf24'],
            borderColor:'#ffffff', borderWidth:3, hoverOffset:6,
        }]
    },
    options:{
        responsive:true, maintainAspectRatio:false, cutout:'72%',
        plugins:{
            legend:{display:false},
            tooltip:{...tooltipStyle, callbacks:{label:c=>' '+Number(c.parsed).toLocaleString('fr-FR')+' F'}}
        }
    }
});

// ── Toggle accordéon propriétaires ───────────────────────────────────
function toggleDetail(id, row) {
    const el = document.getElementById(id);
    const isOpen = el.style.display !== 'none';
    // Fermer tous
    document.querySelectorAll('[id^="detail-"]').forEach(d => d.style.display='none');
    document.querySelectorAll('[class^="chevron-"]').forEach(c => c.style.transform='');
    // Ouvrir le cliqué
    if (!isOpen) {
        el.style.display = 'block';
        const proprioId = id.replace('detail-','');
        const chevron = document.querySelector('.chevron-'+proprioId);
        if (chevron) chevron.style.transform = 'rotate(180deg)';
    }
}
</script>

</x-app-layout>