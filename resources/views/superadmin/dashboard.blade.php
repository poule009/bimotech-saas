@extends('layouts.app')
@section('title', 'Super Administration')
@section('breadcrumb', 'Plateforme')

@section('content')
<style>
.sa-grid { display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:24px; }
.sa-kpi  { background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:16px 18px; }
.sa-kpi-lbl { font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#9ca3af;margin-bottom:6px; }
.sa-kpi-val { font-family:'Syne',sans-serif;font-size:26px;font-weight:800;color:#0d1117;line-height:1; }
.sa-kpi-sub { font-size:11px;color:#6b7280;margin-top:4px; }
.sa-kpi.indigo .sa-kpi-val { color:#6366f1; }
.sa-kpi.green  .sa-kpi-val { color:#16a34a; }
.sa-kpi.amber  .sa-kpi-val { color:#d97706; }
.sa-kpi.red    .sa-kpi-val { color:#dc2626; }

.card  { background:#fff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden;margin-bottom:20px; }
.card-hd { padding:14px 20px;border-bottom:1px solid #e5e7eb;display:flex;align-items:center;justify-content:space-between; }
.card-title { font-family:'Syne',sans-serif;font-size:13px;font-weight:700;color:#0d1117; }

.dt { width:100%;border-collapse:collapse; }
.dt th { padding:9px 16px;text-align:left;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.7px;color:#9ca3af;background:#f9fafb;border-bottom:1px solid #e5e7eb; }
.dt td { padding:11px 16px;font-size:13px;color:#374151;border-bottom:1px solid #f3f4f6;vertical-align:middle; }
.dt tbody tr:last-child td { border-bottom:none; }
.dt tbody tr:hover { background:#fafafa; }

.badge { display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:99px;font-size:11px;font-weight:600; }
.badge-green  { background:#dcfce7;color:#16a34a; }
.badge-red    { background:#fee2e2;color:#dc2626; }
.badge-amber  { background:#fef3c7;color:#d97706; }
.badge-indigo { background:#ede9fe;color:#7c3aed; }
.badge-gray   { background:#f3f4f6;color:#6b7280; }
.badge-dot { width:6px;height:6px;border-radius:50%;background:currentColor; }

.alert-warn { background:#fef3c7;border:1px solid #fcd34d;border-radius:10px;padding:12px 16px;margin-bottom:20px;font-size:13px;color:#92400e;display:flex;align-items:center;gap:10px; }
.alert-success { background:#dcfce7;border:1px solid #86efac;border-radius:10px;padding:12px 16px;margin-bottom:20px;font-size:13px;color:#166534; }
.chart-card { background:#fff;border:1px solid #e5e7eb;border-radius:14px;padding:20px;margin-bottom:20px; }
.filter-bar { display:flex;align-items:center;gap:8px;flex-wrap:wrap;padding:12px 16px;border-bottom:1px solid #e5e7eb; }
.filter-btn { padding:5px 14px;border-radius:99px;font-size:11px;font-weight:600;border:1px solid #e5e7eb;background:#f9fafb;color:#6b7280;cursor:pointer;transition:all .15s; }
.filter-btn.active,.filter-btn:hover { background:#6366f1;color:#fff;border-color:#6366f1; }
.search-input { flex:1;min-width:180px;max-width:280px;padding:6px 12px 6px 30px;border:1px solid #e5e7eb;border-radius:8px;font-size:12px;font-family:'DM Sans',sans-serif;color:#0d1117;background:#f9fafb;outline:none; }
.search-wrap { position:relative;display:flex;align-items:center; }
.search-wrap svg { position:absolute;left:9px;pointer-events:none;color:#9ca3af; }
@media(max-width:768px){.sa-grid{grid-template-columns:repeat(2,1fr)}}
@media(max-width:480px){.sa-grid{grid-template-columns:1fr}}
</style>

<div style="padding:0 0 48px">

    @if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
    @endif

    {{-- Alerte agences expirant bientôt (< 7 jours) --}}
    @php
        $expirantBientot = $agences->filter(function($a) {
            $sub = $a->subscription;
            if (!$sub) return false;
            $date = $sub->statut === 'essai' ? $sub->essai_fin : $sub->abonnement_fin;
            return $date && \Carbon\Carbon::parse($date)->diffInDays(now(), false) >= -7 && \Carbon\Carbon::parse($date)->isFuture();
        });
    @endphp
    @if($expirantBientot->count() > 0)
    <div class="alert-warn">
        <svg style="width:16px;height:16px;flex-shrink:0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
        <span><strong>{{ $expirantBientot->count() }} agence(s)</strong> arrivent à expiration dans moins de 7 jours :
            {{ $expirantBientot->pluck('name')->join(', ') }}
        </span>
    </div>
    @endif

    {{-- KPIs --}}
    <div class="sa-grid">
        <div class="sa-kpi indigo">
            <div class="sa-kpi-lbl">Agences</div>
            <div class="sa-kpi-val">{{ $stats['nb_agences'] }}</div>
            <div class="sa-kpi-sub">{{ $stats['nb_agences_actives'] }} actives</div>
        </div>
        <div class="sa-kpi green">
            <div class="sa-kpi-lbl">Abonnements actifs</div>
            <div class="sa-kpi-val">{{ $stats['nb_abonnements_actifs'] }}</div>
            <div class="sa-kpi-sub">{{ $stats['nb_essai'] }} en essai</div>
        </div>
        <div class="sa-kpi">
            <div class="sa-kpi-lbl">Biens gérés</div>
            <div class="sa-kpi-val">{{ $stats['nb_biens'] }}</div>
            <div class="sa-kpi-sub">{{ $stats['nb_contrats'] }} contrats actifs</div>
        </div>
        <div class="sa-kpi">
            <div class="sa-kpi-lbl">Utilisateurs</div>
            <div class="sa-kpi-val">{{ $stats['nb_users'] }}</div>
            <div class="sa-kpi-sub">Tous rôles confondus</div>
        </div>
        <div class="sa-kpi green">
            <div class="sa-kpi-lbl">Loyers encaissés</div>
            <div class="sa-kpi-val" style="font-size:18px">{{ number_format($stats['total_loyers'], 0, ',', ' ') }}</div>
            <div class="sa-kpi-sub">FCFA — toutes agences</div>
        </div>
        <div class="sa-kpi indigo">
            <div class="sa-kpi-lbl">Commissions plateforme</div>
            <div class="sa-kpi-val" style="font-size:18px">{{ number_format($stats['total_commissions'], 0, ',', ' ') }}</div>
            <div class="sa-kpi-sub">FCFA TTC cumulés</div>
        </div>
        <div class="sa-kpi amber">
            <div class="sa-kpi-lbl">Revenus abonnements</div>
            <div class="sa-kpi-val" style="font-size:18px">{{ number_format($stats['revenus_abonnements'], 0, ',', ' ') }}</div>
            <div class="sa-kpi-sub">FCFA encaissés</div>
        </div>
        <div class="sa-kpi {{ $stats['nb_expires'] > 0 ? 'red' : '' }}">
            <div class="sa-kpi-lbl">Expirés</div>
            <div class="sa-kpi-val">{{ $stats['nb_expires'] }}</div>
            <div class="sa-kpi-sub">{{ $stats['nb_expires'] > 0 ? 'À relancer' : 'Aucun' }}</div>
        </div>
    </div>

    {{-- Graphique revenus + nouvelles agences (12 mois) --}}
    <div class="chart-card">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;flex-wrap:wrap;gap:8px">
            <div>
                <div style="font-family:'Syne',sans-serif;font-size:13px;font-weight:700;color:#0d1117">Évolution sur 12 mois</div>
                <div style="font-size:11px;color:#9ca3af;margin-top:2px">Revenus abonnements &amp; nouvelles agences</div>
            </div>
            <div style="display:flex;align-items:center;gap:14px;font-size:11px;color:#6b7280">
                <span style="display:flex;align-items:center;gap:5px"><span style="width:12px;height:3px;background:#6366f1;border-radius:2px;display:inline-block"></span>Revenus (F)</span>
                <span style="display:flex;align-items:center;gap:5px"><span style="width:12px;height:12px;background:#e0e7ff;border-radius:2px;display:inline-block"></span>Nouvelles agences</span>
            </div>
        </div>
        <div style="position:relative;height:220px">
            <canvas id="sa-chart"></canvas>
        </div>
    </div>

    {{-- Table agences --}}
    <div class="card">
        <div class="card-hd">
            <div>
                <div class="card-title">Toutes les agences</div>
                <div style="font-size:11px;color:#9ca3af;margin-top:2px"><span id="sa-count">{{ $agences->count() }}</span> agence(s)</div>
            </div>
            <a href="{{ route('superadmin.agencies.create') }}"
               style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;background:#6366f1;color:#fff;border-radius:9px;font-size:12px;font-weight:600;text-decoration:none">
                <svg style="width:13px;height:13px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Nouvelle agence
            </a>
        </div>

        {{-- Barre de filtre --}}
        <div class="filter-bar">
            <div class="search-wrap">
                <svg style="width:13px;height:13px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" id="sa-search" class="search-input" placeholder="Rechercher une agence…" oninput="saFilter()">
            </div>
            <button class="filter-btn active" data-filter="tous"     onclick="saSetFilter(this,'tous')">Tous</button>
            <button class="filter-btn"         data-filter="essai"   onclick="saSetFilter(this,'essai')">Essai</button>
            <button class="filter-btn"         data-filter="actif"   onclick="saSetFilter(this,'actif')">Actifs</button>
            <button class="filter-btn"         data-filter="expiré"  onclick="saSetFilter(this,'expire')">Expirés</button>
            <button class="filter-btn"         data-filter="suspendu" onclick="saSetFilter(this,'suspendu')">Suspendus</button>
        </div>

        <div style="overflow-x:auto">
        <table class="dt" id="sa-table">
            <thead>
                <tr>
                    <th>Agence</th>
                    <th>Abonnement</th>
                    <th style="text-align:center">Statut</th>
                    <th style="text-align:center">Biens</th>
                    <th style="text-align:center">Contrats</th>
                    <th style="text-align:right">Loyers (F)</th>
                    <th style="text-align:right">Commissions (F)</th>
                    <th style="text-align:center">Depuis</th>
                    <th style="text-align:center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($agences as $agence)
                @php
                    $sub   = $agence->subscription;
                    $date  = $sub ? ($sub->statut === 'essai' ? $sub->essai_fin : $sub->abonnement_fin) : null;
                    $jours = $date ? (int)\Carbon\Carbon::parse($date)->diffInDays(now(), false) * -1 : null;
                    $filterStatut = ! $agence->actif ? 'suspendu' : ($sub ? $sub->statut : 'aucun');
                @endphp
                <tr data-name="{{ strtolower($agence->name) }} {{ strtolower($agence->email) }}"
                    data-statut="{{ $filterStatut }}"
                    style="{{ !$agence->actif ? 'opacity:.5' : '' }}">
                    <td>
                        <a href="{{ route('superadmin.agencies.show', $agence) }}" style="font-weight:700;color:#0d1117;text-decoration:none">{{ $agence->name }}</a>
                        <div style="font-size:11px;color:#9ca3af;margin-top:1px">{{ $agence->email }}</div>
                    </td>
                    <td>
                        @if($sub)
                            @if($sub->statut === 'essai')
                                <span class="badge badge-amber"><span class="badge-dot"></span>Essai</span>
                            @elseif($sub->statut === 'actif')
                                <span class="badge badge-green"><span class="badge-dot"></span>{{ ucfirst($sub->plan ?? 'Actif') }}</span>
                            @elseif($sub->statut === 'expiré')
                                <span class="badge badge-red"><span class="badge-dot"></span>Expiré</span>
                            @else
                                <span class="badge badge-gray">{{ $sub->statut }}</span>
                            @endif
                            @if($jours !== null)
                                <div style="font-size:10px;margin-top:3px;color:{{ $jours <= 7 ? '#dc2626' : ($jours <= 30 ? '#d97706' : '#9ca3af') }}">
                                    {{ $jours >= 0 ? $jours.' j restants' : abs($jours).' j dépassés' }}
                                </div>
                            @endif
                        @else
                            <span class="badge badge-gray">Aucun</span>
                        @endif
                    </td>
                    <td style="text-align:center">
                        @if($agence->actif)
                            <span class="badge badge-green"><span class="badge-dot"></span>Active</span>
                        @else
                            <span class="badge badge-red"><span class="badge-dot"></span>Suspendue</span>
                        @endif
                    </td>
                    <td style="text-align:center;color:#6b7280">{{ $agence->biens_count }}</td>
                    <td style="text-align:center;color:#6b7280">{{ $agence->contrats_count }}</td>
                    <td style="text-align:right;font-weight:600">{{ number_format($agence->total_loyers, 0, ',', ' ') }}</td>
                    <td style="text-align:right;font-weight:700;color:#6366f1">{{ number_format($agence->total_commissions, 0, ',', ' ') }}</td>
                    <td style="text-align:center;font-size:11px;color:#9ca3af">{{ $agence->created_at->format('d/m/Y') }}</td>
                    <td style="text-align:center">
                        <div style="display:flex;gap:6px;justify-content:center">
                            <a href="{{ route('superadmin.agencies.show', $agence) }}"
                               style="padding:5px 12px;border:1px solid #e5e7eb;border-radius:6px;font-size:11px;color:#374151;text-decoration:none;background:#fff">
                                Détail
                            </a>
                            <form method="POST" action="{{ route('superadmin.agencies.toggle', $agence) }}"
                                  data-confirm="{{ $agence->actif ? 'Suspendre '.$agence->name.' ?' : 'Activer '.$agence->name.' ?' }}">
                                @csrf @method('PATCH')
                                <button type="submit"
                                    style="padding:5px 12px;border-radius:6px;font-size:11px;cursor:pointer;border:1px solid {{ $agence->actif ? '#fecaca' : '#bbf7d0' }};background:{{ $agence->actif ? '#fee2e2' : '#dcfce7' }};color:{{ $agence->actif ? '#dc2626' : '#16a34a' }}">
                                    {{ $agence->actif ? 'Suspendre' : 'Activer' }}
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr id="sa-empty-db">
                    <td colspan="9" style="text-align:center;padding:48px;color:#9ca3af">
                        Aucune agence.
                        <a href="{{ route('superadmin.agencies.create') }}" style="color:#6366f1;margin-left:6px">Créer la première →</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        </div>
        <div id="sa-empty-filter" style="display:none;text-align:center;padding:48px;color:#9ca3af;font-size:13px">
            Aucune agence ne correspond à la recherche.
        </div>
    </div>

</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js"></script>
<script>
// ── Graphique 12 mois ──────────────────────────────────────────────────────
(function () {
    const labels   = @json($chartLabels);
    const revenus  = @json($chartRevenus);
    const agences  = @json($chartAgences);

    new Chart(document.getElementById('sa-chart'), {
        data: {
            labels,
            datasets: [
                {
                    type: 'line',
                    label: 'Revenus (F)',
                    data: revenus,
                    borderColor: '#6366f1',
                    backgroundColor: 'rgba(99,102,241,.08)',
                    borderWidth: 2,
                    pointRadius: 3,
                    pointHoverRadius: 5,
                    fill: true,
                    tension: .35,
                    yAxisID: 'yRev',
                },
                {
                    type: 'bar',
                    label: 'Nouvelles agences',
                    data: agences,
                    backgroundColor: 'rgba(99,102,241,.15)',
                    borderColor: 'rgba(99,102,241,.4)',
                    borderWidth: 1,
                    borderRadius: 4,
                    yAxisID: 'yAgc',
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => ctx.dataset.yAxisID === 'yRev'
                            ? ' ' + ctx.parsed.y.toLocaleString('fr-FR') + ' F'
                            : ' ' + ctx.parsed.y + ' agence(s)'
                    }
                }
            },
            scales: {
                x: { grid: { display: false }, ticks: { font: { size: 10 }, color: '#9ca3af' } },
                yRev: {
                    position: 'left',
                    grid: { color: '#f3f4f6' },
                    ticks: { font: { size: 10 }, color: '#9ca3af',
                        callback: v => v >= 1000 ? (v/1000).toFixed(0)+'k' : v }
                },
                yAgc: {
                    position: 'right',
                    grid: { display: false },
                    ticks: { font: { size: 10 }, color: '#9ca3af', stepSize: 1 },
                    min: 0,
                }
            }
        }
    });
})();

// ── Filtre / recherche table agences ──────────────────────────────────────
var saCurrentFilter = 'tous';

function saSetFilter(btn, filter) {
    saCurrentFilter = filter;
    document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    saFilter();
}

function saFilter() {
    var q      = document.getElementById('sa-search').value.toLowerCase().trim();
    var rows   = document.querySelectorAll('#sa-table tbody tr[data-name]');
    var visible = 0;

    rows.forEach(function (row) {
        var nameMatch   = !q || row.dataset.name.includes(q);
        var statut      = row.dataset.statut;
        var filterMatch = saCurrentFilter === 'tous'
            || (saCurrentFilter === 'expire'   && statut === 'expiré')
            || (saCurrentFilter === 'suspendu' && statut === 'suspendu')
            || statut === saCurrentFilter;

        var show = nameMatch && filterMatch;
        row.style.display = show ? '' : 'none';
        if (show) visible++;
    });

    document.getElementById('sa-count').textContent = visible;
    document.getElementById('sa-empty-filter').style.display = (visible === 0 && rows.length > 0) ? 'block' : 'none';
}
</script>
@endsection
