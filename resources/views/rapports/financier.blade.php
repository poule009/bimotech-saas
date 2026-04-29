@extends('layouts.app')
@section('title', 'Rapport financier')
@section('breadcrumb', 'Rapports › Financier')

@section('content')
<style>
/* ── KPIs ── */
.kpi-row5 { display:grid;grid-template-columns:repeat(5,1fr);gap:14px;margin-bottom:22px; }
.kpi5 { background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:16px 18px;position:relative;overflow:hidden; }
.kpi5::before { content:'';position:absolute;top:0;left:0;right:0;height:3px;border-radius:12px 12px 0 0; }
.kpi5.gold::before   { background:#c9a84c; }
.kpi5.green::before  { background:#16a34a; }
.kpi5.blue::before   { background:#1d4ed8; }
.kpi5.purple::before { background:#7c3aed; }
.kpi5.dark::before   { background:#0d1117; }
.kpi5-lbl { font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#9ca3af;margin-bottom:6px; }
.kpi5-val { font-family:'Syne',sans-serif;font-size:20px;font-weight:700;color:#0d1117;line-height:1.1; }
.kpi5-sub { font-size:11px;color:#9ca3af;margin-top:4px; }

/* ── Tables ── */
.table-card { background:#fff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden;margin-bottom:20px; }
.table-hd { padding:14px 20px;border-bottom:1px solid #e5e7eb;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px; }
.table-title { font-family:'Syne',sans-serif;font-size:13px;font-weight:700;color:#0d1117; }
.dt { width:100%;border-collapse:collapse; }
.dt th { padding:9px 16px;text-align:left;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.7px;color:#9ca3af;background:#f9fafb;border-bottom:1px solid #e5e7eb; }
.dt td { padding:11px 16px;font-size:13px;color:#374151;border-bottom:1px solid #f3f4f6;vertical-align:middle; }
.dt tbody tr:last-child td { border-bottom:none; }
.dt tbody tr:hover { background:#fafafa; }

/* ── Navigation mois ── */
.nav-btn { display:flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:7px;border:1px solid #e5e7eb;background:#fff;color:#6b7280;text-decoration:none;transition:all .15s; }
.nav-btn:hover { border-color:#c9a84c;color:#8a6e2f; }
.nav-current { font-family:'Syne',sans-serif;font-size:14px;font-weight:600;color:#0d1117;min-width:140px;text-align:center; }

/* ── Badges mode paiement ── */
.mode-badge { display:inline-flex;align-items:center;gap:4px;padding:2px 8px;border-radius:99px;font-size:10px;font-weight:700; }
.mode-especes      { background:#f3f4f6;color:#374151; }
.mode-virement     { background:#dbeafe;color:#1d4ed8; }
.mode-cheque       { background:#ede9fe;color:#7c3aed; }
.mode-wave         { background:#fef3c7;color:#d97706; }
.mode-orange_money { background:#ffedd5;color:#ea580c; }
.mode-free_money   { background:#fce7f3;color:#db2777; }
.mode-e_money      { background:#d1fae5;color:#059669; }

/* ── Avatar initiales ── */
.ava { width:28px;height:28px;border-radius:8px;background:#f5e9c9;color:#8a6e2f;font-family:'Syne',sans-serif;font-size:10px;font-weight:700;display:inline-flex;align-items:center;justify-content:center;flex-shrink:0; }

/* ── Chart barres ── */
.chart-wrap { background:#fff;border:1px solid #e5e7eb;border-radius:14px;padding:20px 24px;margin-bottom:22px; }
.chart-bars { display:flex;align-items:flex-end;gap:8px;height:110px;margin:16px 0 8px; }
.chart-col  { flex:1;display:flex;flex-direction:column;align-items:center;gap:2px; }
.chart-bars-inner { width:100%;display:flex;align-items:flex-end;gap:2px; }
.bar-loyer  { flex:1;background:#c9a84c;border-radius:4px 4px 0 0;min-height:2px;transition:opacity .15s; }
.bar-loyer:hover { opacity:.8; }
.bar-comm   { flex:1;background:#e5e7eb;border-radius:4px 4px 0 0;min-height:2px;transition:opacity .15s; }
.bar-comm:hover { opacity:.8; }
.chart-lbl  { font-size:9px;color:#9ca3af;text-align:center;white-space:nowrap; }
.chart-lbl.active { color:#0d1117;font-weight:700; }

/* ── Barre occupation ── */
.occ-bar-bg { height:6px;background:#e5e7eb;border-radius:99px;overflow:hidden;margin-top:6px; }
.occ-bar-fill { height:100%;background:#16a34a;border-radius:99px;transition:width .4s; }

/* ── Pagination ── */
.pg-btn { display:inline-flex;align-items:center;justify-content:center;min-width:30px;height:30px;padding:0 8px;border-radius:7px;border:1px solid #e5e7eb;background:#fff;color:#374151;font-size:12px;text-decoration:none;transition:all .15s; }
.pg-btn:hover { border-color:#c9a84c;color:#8a6e2f; }
.pg-btn.active { background:#0d1117;color:#fff;border-color:#0d1117; }
.pg-btn.disabled { opacity:.35;pointer-events:none; }
</style>

@php
    $prevMois  = $mois == 1  ? 12 : $mois - 1;
    $prevAnnee = $mois == 1  ? $annee - 1 : $annee;
    $nextMois  = $mois == 12 ? 1  : $mois + 1;
    $nextAnnee = $mois == 12 ? $annee + 1 : $annee;
    $isFutur   = $nextAnnee > now()->year || ($nextAnnee == now()->year && $nextMois > now()->month);

    // Chart : max pour normaliser les barres
    $maxLoyer = $evolution->max('total_loyers') ?: 1;
@endphp

<div style="padding:0 0 48px">

{{-- ── HEADER ──────────────────────────────────────────────────────────── --}}
<div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:22px;flex-wrap:wrap;gap:12px">
    <div>
        <h1 style="font-family:'Syne',sans-serif;font-size:22px;font-weight:700;color:#0d1117;letter-spacing:-.4px">Rapport financier</h1>
        <p style="font-size:13px;color:#6b7280;margin-top:3px">
            {{ $debutMois->translatedFormat('F Y') }}
            · {{ $kpiMois['nb_paiements'] }} paiement(s) validé(s)
            @if($biensImpayes->count() > 0)
                · <span style="color:#dc2626;font-weight:600">{{ $biensImpayes->count() }} impayé(s)</span>
            @endif
        </p>
    </div>

    <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">
        {{-- Navigation période --}}
        <div style="display:flex;align-items:center;gap:6px">
            <a href="{{ route('admin.rapports.financier', ['mois' => $prevMois, 'annee' => $prevAnnee]) }}" class="nav-btn">
                <svg style="width:13px;height:13px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
            </a>
            <div class="nav-current">{{ $debutMois->translatedFormat('F Y') }}</div>
            @if(!$isFutur)
            <a href="{{ route('admin.rapports.financier', ['mois' => $nextMois, 'annee' => $nextAnnee]) }}" class="nav-btn">
                <svg style="width:13px;height:13px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            </a>
            @else
            <span class="nav-btn" style="opacity:.3;cursor:not-allowed">
                <svg style="width:13px;height:13px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            </span>
            @endif
        </div>

        {{-- Sélecteur année --}}
        @if($anneesDisponibles->count() > 1)
        <form method="GET" style="display:flex;align-items:center;gap:6px">
            <input type="hidden" name="mois" value="{{ $mois }}">
            <select name="annee" onchange="this.form.submit()"
                    style="padding:7px 10px;border:1px solid #e5e7eb;border-radius:8px;font-size:13px;font-family:'DM Sans',sans-serif;color:#0d1117;background:#fff;cursor:pointer">
                @foreach($anneesDisponibles as $a)
                <option value="{{ $a }}" {{ $a == $annee ? 'selected' : '' }}>{{ $a }}</option>
                @endforeach
            </select>
        </form>
        @endif

        {{-- Export PDF --}}
        <a href="{{ route('admin.rapports.financier.export-pdf', ['mois' => $mois, 'annee' => $annee]) }}"
           style="display:inline-flex;align-items:center;gap:6px;padding:9px 16px;background:#0d1117;color:#fff;font-family:'DM Sans',sans-serif;font-size:13px;font-weight:600;border-radius:8px;text-decoration:none;transition:opacity .15s"
           onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
            <svg style="width:14px;height:14px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            Exporter PDF
        </a>
    </div>
</div>

{{-- ── GRAPHIQUE 6 MOIS ─────────────────────────────────────────────────── --}}
@if($evolution->count() > 0)
<div class="chart-wrap">
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px">
        <div>
            <div style="font-family:'Syne',sans-serif;font-size:13px;font-weight:700;color:#0d1117">Évolution sur 6 mois</div>
            <div style="font-size:11px;color:#9ca3af;margin-top:2px">Loyers encaissés et commissions</div>
        </div>
        <div style="display:flex;align-items:center;gap:14px;font-size:11px;color:#6b7280">
            <span style="display:flex;align-items:center;gap:5px">
                <span style="width:10px;height:10px;border-radius:3px;background:#c9a84c;display:inline-block"></span> Loyers
            </span>
            <span style="display:flex;align-items:center;gap:5px">
                <span style="width:10px;height:10px;border-radius:3px;background:#e5e7eb;display:inline-block"></span> Commission
            </span>
        </div>
    </div>

    <div class="chart-bars">
        @foreach($evolution as $e)
        @php
            $hLoyer = max(4, round(($e->total_loyers / $maxLoyer) * 110));
            $hComm  = max(2, round(($e->total_commission / $maxLoyer) * 110));
            $label  = \Carbon\Carbon::createFromFormat('Y-m', $e->mois_label)->translatedFormat('M');
            $isCurrent = $e->mois_label === $debutMois->format('Y-m');
        @endphp
        <div class="chart-col" style="position:relative" title="{{ number_format($e->total_loyers, 0, ',', ' ') }} F">
            <div class="chart-bars-inner" style="height:110px">
                <div class="bar-loyer" style="height:{{ $hLoyer }}px;{{ $isCurrent ? 'background:#0d1117' : '' }}"></div>
                <div class="bar-comm"  style="height:{{ $hComm }}px;{{ $isCurrent ? 'background:#c9a84c' : '' }}"></div>
            </div>
            <div class="chart-lbl {{ $isCurrent ? 'active' : '' }}">{{ $label }}</div>
        </div>
        @endforeach
    </div>

    {{-- Valeur max en référence --}}
    <div style="font-size:10px;color:#9ca3af;text-align:right;margin-top:2px">
        Max : {{ number_format($maxLoyer, 0, ',', ' ') }} F
    </div>
</div>
@endif

{{-- ── KPIs ─────────────────────────────────────────────────────────────── --}}
<div class="kpi-row5">
    <div class="kpi5 gold">
        <div class="kpi5-lbl">Loyers encaissés</div>
        <div class="kpi5-val">{{ number_format($kpiMois['total_loyers'], 0, ',', ' ') }}</div>
        <div class="kpi5-sub">FCFA · {{ $kpiMois['nb_paiements'] }} paiements</div>
    </div>
    <div class="kpi5 green">
        <div class="kpi5-lbl">Net propriétaires</div>
        <div class="kpi5-val" style="color:#16a34a">{{ number_format($kpiMois['total_net_proprio'], 0, ',', ' ') }}</div>
        <div class="kpi5-sub">FCFA à reverser</div>
    </div>
    <div class="kpi5 blue">
        <div class="kpi5-lbl">Commission HT</div>
        <div class="kpi5-val" style="color:#1d4ed8">{{ number_format($kpiMois['total_commission'], 0, ',', ' ') }}</div>
        <div class="kpi5-sub">FCFA agence</div>
    </div>
    <div class="kpi5 purple">
        <div class="kpi5-lbl">TVA commission</div>
        <div class="kpi5-val" style="color:#7c3aed">{{ number_format($kpiMois['total_tva'], 0, ',', ' ') }}</div>
        <div class="kpi5-sub">FCFA (18%)</div>
    </div>
    <div class="kpi5 dark">
        <div class="kpi5-lbl">Commission TTC</div>
        <div class="kpi5-val">{{ number_format($kpiMois['total_ttc'], 0, ',', ' ') }}</div>
        <div class="kpi5-sub">FCFA total agence</div>
    </div>
</div>

{{-- ── STATS GÉNÉRALES ──────────────────────────────────────────────────── --}}
<div style="display:grid;grid-template-columns:repeat(6,1fr);gap:12px;margin-bottom:22px">
    @foreach([
        ['label' => 'Biens total',        'val' => $statsGenerales['nb_biens']],
        ['label' => 'Biens loués',         'val' => $statsGenerales['nb_biens_loues']],
        ['label' => 'Contrats actifs',     'val' => $statsGenerales['nb_contrats']],
        ['label' => 'Propriétaires',       'val' => $statsGenerales['nb_proprietaires']],
        ['label' => 'Locataires',          'val' => $statsGenerales['nb_locataires']],
    ] as $s)
    <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:10px;padding:12px 16px;text-align:center">
        <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#9ca3af;margin-bottom:4px">{{ $s['label'] }}</div>
        <div style="font-family:'Syne',sans-serif;font-size:18px;font-weight:700;color:#0d1117">{{ $s['val'] }}</div>
    </div>
    @endforeach

    {{-- Taux occupation avec barre --}}
    <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:10px;padding:12px 16px">
        <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#9ca3af;margin-bottom:4px">Taux d'occupation</div>
        <div style="font-family:'Syne',sans-serif;font-size:18px;font-weight:700;color:{{ $statsGenerales['taux_occupation'] >= 80 ? '#16a34a' : ($statsGenerales['taux_occupation'] >= 50 ? '#d97706' : '#dc2626') }}">
            {{ $statsGenerales['taux_occupation'] }}%
        </div>
        <div class="occ-bar-bg" style="margin-top:8px">
            <div class="occ-bar-fill" style="width:{{ $statsGenerales['taux_occupation'] }}%;background:{{ $statsGenerales['taux_occupation'] >= 80 ? '#16a34a' : ($statsGenerales['taux_occupation'] >= 50 ? '#d97706' : '#dc2626') }}"></div>
        </div>
    </div>
</div>

{{-- ── PAR PROPRIÉTAIRE ─────────────────────────────────────────────────── --}}
@if($parProprietaire->count() > 0)
<div class="table-card">
    <div class="table-hd">
        <div class="table-title">Récapitulatif par propriétaire</div>
        <div style="font-size:12px;color:#6b7280">{{ $parProprietaire->count() }} propriétaire(s)</div>
    </div>
    <div style="overflow-x:auto">
        <table class="dt">
            <thead>
                <tr>
                    <th>Propriétaire</th>
                    <th style="text-align:center">Paiements</th>
                    <th style="text-align:right">Total encaissé</th>
                    <th style="text-align:right">Commission TTC</th>
                    <th style="text-align:right">Net reversé</th>
                    <th style="text-align:right">Part (%)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($parProprietaire as $nom => $data)
                @php $part = $kpiMois['total_loyers'] > 0 ? round(($data['total_encaisse'] / $kpiMois['total_loyers']) * 100, 1) : 0; @endphp
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:9px">
                            <div class="ava">{{ strtoupper(substr($nom, 0, 2)) }}</div>
                            <span style="font-weight:500">{{ $nom }}</span>
                        </div>
                    </td>
                    <td style="text-align:center">
                        <span style="background:#f3f4f6;color:#374151;padding:2px 9px;border-radius:99px;font-size:11px;font-weight:600">{{ $data['nb_paiements'] }}</span>
                    </td>
                    <td style="text-align:right;font-weight:700;color:#c9a84c;font-family:'Syne',sans-serif">
                        {{ number_format($data['total_encaisse'], 0, ',', ' ') }} F
                    </td>
                    <td style="text-align:right;color:#8a6e2f">
                        {{ number_format($data['total_commission'], 0, ',', ' ') }} F
                    </td>
                    <td style="text-align:right;color:#16a34a;font-weight:600">
                        {{ number_format($data['total_net'], 0, ',', ' ') }} F
                    </td>
                    <td style="text-align:right">
                        <div style="display:flex;align-items:center;gap:6px;justify-content:flex-end">
                            <span style="font-size:11px;color:#9ca3af">{{ $part }}%</span>
                            <div style="width:40px;height:4px;background:#e5e7eb;border-radius:99px;overflow:hidden">
                                <div style="height:100%;width:{{ $part }}%;background:#c9a84c;border-radius:99px"></div>
                            </div>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background:#f9fafb;font-weight:700">
                    <td>Total</td>
                    <td style="text-align:center">{{ $kpiMois['nb_paiements'] }}</td>
                    <td style="text-align:right;font-family:'Syne',sans-serif;color:#c9a84c">
                        {{ number_format($kpiMois['total_loyers'], 0, ',', ' ') }} F
                    </td>
                    <td style="text-align:right;color:#8a6e2f">
                        {{ number_format($kpiMois['total_ttc'], 0, ',', ' ') }} F
                    </td>
                    <td style="text-align:right;color:#16a34a">
                        {{ number_format($kpiMois['total_net_proprio'], 0, ',', ' ') }} F
                    </td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endif

{{-- ── DÉTAIL PAIEMENTS ─────────────────────────────────────────────────── --}}
<div class="table-card">
    <div class="table-hd">
        <div class="table-title">Détail des paiements</div>
        <div style="font-size:12px;color:#6b7280">{{ $paiementsMois->total() }} paiement(s)</div>
    </div>

    @if($paiementsMois->isEmpty())
    <div style="padding:48px 20px;text-align:center">
        <div style="width:48px;height:48px;border-radius:12px;background:#f3f4f6;display:flex;align-items:center;justify-content:center;margin:0 auto 12px">
            <svg style="width:20px;height:20px;color:#9ca3af" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
        </div>
        <div style="font-family:'Syne',sans-serif;font-size:14px;font-weight:700;color:#0d1117;margin-bottom:6px">Aucun paiement ce mois</div>
        <div style="font-size:13px;color:#6b7280;margin-bottom:16px">Aucun paiement validé pour {{ $debutMois->translatedFormat('F Y') }}.</div>
        <a href="{{ route('admin.paiements.create') }}" class="btn-primary" style="display:inline-flex">
            <svg style="width:14px;height:14px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Enregistrer un paiement
        </a>
    </div>
    @else
    <div style="overflow-x:auto">
        <table class="dt">
            <thead>
                <tr>
                    <th>Référence</th>
                    <th>Bien</th>
                    <th>Locataire</th>
                    <th>Propriétaire</th>
                    <th>Mode</th>
                    <th style="text-align:right">Loyer nu</th>
                    <th style="text-align:right">Comm. TTC</th>
                    <th style="text-align:right">Net proprio</th>
                    <th style="text-align:center">PDF</th>
                </tr>
            </thead>
            <tbody>
                @foreach($paiementsMois as $p)
                @php
                    $modeLabels = [
                        'especes'      => 'Espèces',
                        'virement'     => 'Virement',
                        'cheque'       => 'Chèque',
                        'wave'         => 'Wave',
                        'orange_money' => 'Orange Money',
                        'free_money'   => 'Free Money',
                        'e_money'      => 'E-Money',
                    ];
                    $proprio = $p->contrat?->bien?->proprietaire;
                @endphp
                <tr>
                    <td style="font-family:'Syne',sans-serif;font-size:11px;color:#9ca3af;white-space:nowrap">
                        {{ $p->reference_paiement }}
                    </td>
                    <td>
                        <div style="font-weight:500;color:#0d1117;font-size:12px">{{ $p->contrat?->bien?->reference ?? '—' }}</div>
                        <div style="font-size:11px;color:#9ca3af">{{ $p->contrat?->bien?->ville }}</div>
                    </td>
                    <td style="font-size:12px">{{ $p->contrat?->locataire?->name ?? '—' }}</td>
                    <td>
                        @if($proprio)
                        <div style="display:flex;align-items:center;gap:7px">
                            <div class="ava" style="width:22px;height:22px;font-size:8px;border-radius:6px">{{ strtoupper(substr($proprio->name, 0, 2)) }}</div>
                            <span style="font-size:12px;color:#6b7280">{{ $proprio->name }}</span>
                        </div>
                        @else
                        <span style="color:#9ca3af;font-size:12px">—</span>
                        @endif
                    </td>
                    <td>
                        <span class="mode-badge mode-{{ $p->mode_paiement }}">
                            {{ $modeLabels[$p->mode_paiement] ?? $p->mode_paiement }}
                        </span>
                    </td>
                    <td style="text-align:right;font-weight:600;color:#0d1117;white-space:nowrap">
                        {{ number_format($p->loyer_nu ?? 0, 0, ',', ' ') }} F
                    </td>
                    <td style="text-align:right;color:#8a6e2f;white-space:nowrap">
                        {{ number_format($p->commission_ttc ?? 0, 0, ',', ' ') }} F
                    </td>
                    <td style="text-align:right;color:#16a34a;font-weight:600;white-space:nowrap">
                        {{ number_format($p->net_proprietaire ?? 0, 0, ',', ' ') }} F
                    </td>
                    <td style="text-align:center">
                        <a href="{{ route('admin.paiements.pdf', $p) }}" target="_blank" class="act-btn" title="Quittance PDF">
                            <svg style="width:12px;height:12px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($paiementsMois->hasPages())
    <div style="display:flex;justify-content:space-between;align-items:center;padding:14px 18px;border-top:1px solid #f3f4f6">
        <div style="font-size:12px;color:#6b7280">
            {{ $paiementsMois->firstItem() }}–{{ $paiementsMois->lastItem() }} sur {{ $paiementsMois->total() }}
        </div>
        <div style="display:flex;gap:4px">
            @if($paiementsMois->onFirstPage())
                <span class="pg-btn disabled"><svg style="width:12px;height:12px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg></span>
            @else
                <a href="{{ $paiementsMois->previousPageUrl() }}" class="pg-btn"><svg style="width:12px;height:12px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg></a>
            @endif

            @foreach($paiementsMois->getUrlRange(max(1,$paiementsMois->currentPage()-2), min($paiementsMois->lastPage(),$paiementsMois->currentPage()+2)) as $page => $url)
                <a href="{{ $url }}" class="pg-btn {{ $page == $paiementsMois->currentPage() ? 'active' : '' }}">{{ $page }}</a>
            @endforeach

            @if($paiementsMois->hasMorePages())
                <a href="{{ $paiementsMois->nextPageUrl() }}" class="pg-btn"><svg style="width:12px;height:12px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg></a>
            @else
                <span class="pg-btn disabled"><svg style="width:12px;height:12px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg></span>
            @endif
        </div>
    </div>
    @endif
    @endif
</div>

{{-- ── IMPAYÉS ───────────────────────────────────────────────────────────── --}}
@if(isset($biensImpayes) && $biensImpayes->count() > 0)
<div class="table-card">
    <div class="table-hd">
        <div style="display:flex;align-items:center;gap:8px">
            <div style="width:8px;height:8px;border-radius:50%;background:#dc2626"></div>
            <div class="table-title" style="color:#dc2626">Impayés — {{ $biensImpayes->count() }} contrat(s)</div>
        </div>
        <div style="font-size:12px;color:#dc2626;font-weight:500">
            {{ number_format($biensImpayes->sum('loyer_contractuel'), 0, ',', ' ') }} F non encaissés
        </div>
    </div>
    <div style="overflow-x:auto">
        <table class="dt">
            <thead>
                <tr>
                    <th>Bien</th>
                    <th>Locataire</th>
                    <th>Propriétaire</th>
                    <th style="text-align:right">Loyer dû</th>
                    <th style="text-align:center">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($biensImpayes as $c)
                <tr>
                    <td>
                        <div style="font-weight:500;color:#0d1117">{{ $c->bien?->reference ?? '—' }}</div>
                        <div style="font-size:11px;color:#9ca3af">{{ $c->bien?->ville }}</div>
                    </td>
                    <td>{{ $c->locataire?->name ?? '—' }}</td>
                    <td style="color:#6b7280">{{ $c->bien?->proprietaire?->name ?? '—' }}</td>
                    <td style="text-align:right;color:#dc2626;font-weight:700;font-family:'Syne',sans-serif;white-space:nowrap">
                        {{ number_format($c->loyer_contractuel, 0, ',', ' ') }} F
                    </td>
                    <td style="text-align:center">
                        <a href="{{ route('admin.paiements.create', ['contrat_id' => $c->id]) }}"
                           style="display:inline-flex;align-items:center;gap:4px;padding:5px 10px;background:#dcfce7;color:#16a34a;border:1px solid #bbf7d0;border-radius:6px;font-size:11px;font-weight:600;text-decoration:none;transition:opacity .15s"
                           onmouseover="this.style.opacity='.75'" onmouseout="this.style.opacity='1'">
                            <svg style="width:11px;height:11px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                            Paiement
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@else
<div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:16px 20px;display:flex;align-items:center;gap:10px">
    <svg style="width:18px;height:18px;color:#16a34a;flex-shrink:0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
    <span style="font-size:13px;color:#16a34a;font-weight:500">
        Aucun impayé ce mois — Taux de recouvrement 100 %
    </span>
</div>
@endif

</div>
@endsection
