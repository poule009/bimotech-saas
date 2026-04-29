@extends('layouts.app')
@section('title', 'Hub Bailleur — ' . $user->name)
@section('breadcrumb', 'Portefeuille Bailleurs › ' . $user->name)

@section('content')
<style>
/* ═══════════════════════════════════════════
   HUB BAILLEUR — Design System
   ═══════════════════════════════════════════ */
*, *::before, *::after { box-sizing: border-box; }

/* ── Hero ── */
.hub-hero {
    background: linear-gradient(135deg, #0d1117 0%, #1a2332 50%, #0d1117 100%);
    border-radius: 16px;
    padding: 24px 28px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 16px;
    border: 1px solid rgba(255,255,255,.06);
    position: relative;
    overflow: hidden;
}
.hub-hero::before {
    content: '';
    position: absolute;
    top: -60px; right: -60px;
    width: 200px; height: 200px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(201,168,76,.06) 0%, transparent 70%);
    pointer-events: none;
}
.hub-hero-left { display: flex; align-items: center; gap: 18px; }
.hub-avatar {
    width: 60px; height: 60px;
    border-radius: 50%;
    background: rgba(201,168,76,.12);
    border: 2px solid rgba(201,168,76,.35);
    display: flex; align-items: center; justify-content: center;
    font-family: 'Syne', sans-serif;
    font-size: 24px; font-weight: 800;
    color: #c9a84c;
    flex-shrink: 0;
}
.hub-name { font-family: 'Syne', sans-serif; font-size: 22px; font-weight: 700; color: #fff; margin-bottom: 4px; line-height: 1.2; }
.hub-meta { font-size: 12px; color: rgba(255,255,255,.3); line-height: 1.8; }
.hub-meta strong { color: rgba(255,255,255,.5); font-weight: 500; }

.hub-kpi-row { display: flex; gap: 10px; align-items: stretch; flex-wrap: wrap; }
.hub-kpi {
    background: rgba(255,255,255,.04);
    border: 1px solid rgba(255,255,255,.08);
    border-radius: 12px;
    padding: 12px 18px;
    text-align: center;
    min-width: 110px;
}
.hub-kpi-lbl { font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: .8px; color: rgba(255,255,255,.3); margin-bottom: 5px; }
.hub-kpi-val { font-family: 'Syne', sans-serif; font-size: 20px; font-weight: 700; color: #fff; line-height: 1; }
.hub-kpi-val.gold { color: #c9a84c; }
.hub-kpi-val.green { color: #4ade80; }
.hub-kpi-sub { font-size: 10px; color: rgba(255,255,255,.25); margin-top: 3px; }

/* ── Filtre période ── */
.hub-filter {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}
.hub-filter select {
    padding: 7px 12px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    font-size: 13px;
    font-family: 'DM Sans', sans-serif;
    color: #374151;
    background: #fff;
    cursor: pointer;
}
.hub-filter-reset {
    padding: 7px 14px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    font-size: 13px;
    color: #6b7280;
    text-decoration: none;
    background: #fff;
}

/* ══════════════════════════════════
   SYSTÈME D'ONGLETS
   ══════════════════════════════════ */
.hub-tabs {
    display: flex;
    gap: 2px;
    background: #f3f4f6;
    border-radius: 12px;
    padding: 4px;
    margin-bottom: 20px;
    width: fit-content;
}
.hub-tab {
    padding: 8px 18px;
    border-radius: 9px;
    border: none;
    background: transparent;
    font-family: 'DM Sans', sans-serif;
    font-size: 13px;
    font-weight: 500;
    color: #6b7280;
    cursor: pointer;
    transition: all .18s;
    display: flex;
    align-items: center;
    gap: 7px;
    white-space: nowrap;
}
.hub-tab:hover { color: #374151; background: rgba(255,255,255,.6); }
.hub-tab.active {
    background: #fff;
    color: #0d1117;
    font-weight: 600;
    box-shadow: 0 1px 4px rgba(0,0,0,.08);
}
.hub-tab-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 18px; height: 18px;
    border-radius: 50%;
    font-size: 10px;
    font-weight: 700;
    background: #e5e7eb;
    color: #6b7280;
}
.hub-tab.active .hub-tab-badge {
    background: rgba(201,168,76,.15);
    color: #8a6e2f;
}

.hub-panel { display: none; }
.hub-panel.active { display: block; }

/* ══════════════════════════════════
   PANEL 1 — SYNTHÈSE FINANCIÈRE
   ══════════════════════════════════ */
.synth-grid { display: grid; grid-template-columns: 1fr 300px; gap: 20px; align-items: start; }

.equation {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    padding: 18px 22px;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}
.eq-block { text-align: center; padding: 10px 16px; border-radius: 10px; min-width: 110px; }
.eq-block-lbl { font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: .6px; margin-bottom: 5px; }
.eq-block-val { font-family: 'Syne', sans-serif; font-size: 15px; font-weight: 700; }
.eq-op { font-family: 'Syne', sans-serif; font-size: 22px; font-weight: 700; color: #9ca3af; }
.eq-result { background: #0d1117; border-radius: 10px; padding: 12px 20px; text-align: center; }
.eq-result-lbl { font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: .6px; color: rgba(201,168,76,.6); margin-bottom: 4px; }
.eq-result-val { font-family: 'Syne', sans-serif; font-size: 18px; font-weight: 700; color: #c9a84c; }

/* Right panel dark */
.rp { background: #0d1117; border-radius: 14px; overflow: hidden; }
.rp-hd { padding: 14px 18px; border-bottom: 1px solid rgba(255,255,255,.07); display: flex; align-items: center; justify-content: space-between; }
.rp-title { font-family: 'Syne', sans-serif; font-size: 12px; font-weight: 700; color: #fff; }
.rp-body { padding: 14px 18px; }
.rp-row { display: flex; justify-content: space-between; padding: 7px 0; border-bottom: 1px solid rgba(255,255,255,.05); font-size: 12px; }
.rp-row:last-child { border-bottom: none; }
.rp-lbl { color: rgba(255,255,255,.35); }
.rp-val { color: #e6edf3; font-weight: 600; font-family: 'Syne', sans-serif; font-size: 13px; }
.rp-val.gold  { color: #c9a84c; }
.rp-val.green { color: #4ade80; }
.rp-val.red   { color: #f87171; }
.rp-sep { height: 1px; background: rgba(255,255,255,.06); margin: 8px 0; }
.rp-net { background: rgba(201,168,76,.1); border: 1px solid rgba(201,168,76,.2); border-radius: 10px; padding: 12px 14px; margin: 10px 0; }
.rp-net-lbl { font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: .8px; color: rgba(201,168,76,.6); margin-bottom: 4px; }
.rp-net-val { font-family: 'Syne', sans-serif; font-size: 22px; font-weight: 700; color: #c9a84c; }
.btn-pdf {
    display: flex; align-items: center; justify-content: center; gap: 7px;
    padding: 10px;
    border: 1px solid rgba(255,255,255,.1);
    border-radius: 9px;
    color: #c9a84c;
    font-size: 12px; font-weight: 600;
    text-decoration: none;
    transition: all .15s;
    margin-top: 10px;
}
.btn-pdf:hover { background: rgba(201,168,76,.08); border-color: rgba(201,168,76,.3); }

/* ══════════════════════════════════
   PANEL 2 — BIENS & LOCATAIRES
   ══════════════════════════════════ */
.bien-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    overflow: hidden;
    margin-bottom: 12px;
    transition: box-shadow .18s, border-color .18s;
}
.bien-card:hover { box-shadow: 0 4px 20px rgba(0,0,0,.06); border-color: #d1d5db; }

.bien-card-header {
    padding: 14px 18px;
    display: flex;
    align-items: center;
    gap: 14px;
    border-bottom: 1px solid #f3f4f6;
}
.bien-ref-block { flex: 1; }
.bien-ref { font-family: 'Syne', sans-serif; font-size: 13px; font-weight: 700; color: #0d1117; }
.bien-addr { font-size: 11px; color: #6b7280; margin-top: 2px; }
.bien-loyer { font-family: 'Syne', sans-serif; font-size: 15px; font-weight: 700; color: #0d1117; white-space: nowrap; }
.bien-loyer-sub { font-size: 10px; color: #9ca3af; text-align: right; }

.statut-badge { display: inline-flex; align-items: center; gap: 4px; padding: 3px 10px; border-radius: 99px; font-size: 10px; font-weight: 600; }
.s-loue { background: #dcfce7; color: #16a34a; }
.s-disponible { background: #f3f4f6; color: #6b7280; }
.s-travaux { background: #fef9c3; color: #a16207; }
.s-dot { width: 5px; height: 5px; border-radius: 50%; background: currentColor; display: inline-block; }

.bien-card-tenant {
    padding: 12px 18px;
    background: #fafafa;
    display: flex;
    align-items: center;
    gap: 12px;
}
.tenant-avatar {
    width: 32px; height: 32px;
    border-radius: 50%;
    background: #f0f9ff;
    border: 1.5px solid #bae6fd;
    display: flex; align-items: center; justify-content: center;
    font-size: 12px; font-weight: 700;
    color: #0284c7;
    flex-shrink: 0;
}
.tenant-name { font-size: 12.5px; font-weight: 600; color: #0d1117; }
.tenant-meta { font-size: 11px; color: #6b7280; margin-top: 1px; }
.tenant-contract-link {
    margin-left: auto;
    display: inline-flex; align-items: center; gap: 5px;
    padding: 5px 10px;
    border: 1px solid #e5e7eb;
    border-radius: 7px;
    font-size: 11px; font-weight: 600;
    color: #374151;
    text-decoration: none;
    background: #fff;
    transition: all .15s;
}
.tenant-contract-link:hover { background: #f3f4f6; border-color: #d1d5db; }

.bien-card-empty {
    padding: 16px 18px;
    background: #fafafa;
    display: flex;
    align-items: center;
    gap: 10px;
    color: #9ca3af;
    font-size: 12px;
}
.btn-assign {
    margin-left: auto;
    display: inline-flex; align-items: center; gap: 5px;
    padding: 5px 12px;
    border-radius: 7px;
    font-size: 11px; font-weight: 600;
    background: rgba(201,168,76,.1);
    color: #8a6e2f;
    text-decoration: none;
    border: 1px solid rgba(201,168,76,.2);
    transition: all .15s;
}
.btn-assign:hover { background: rgba(201,168,76,.18); }

/* ══════════════════════════════════
   PANEL 3 — HISTORIQUE PAIEMENTS
   ══════════════════════════════════ */
.card { background: #fff; border: 1px solid #e5e7eb; border-radius: 14px; overflow: hidden; }
.card-hd { padding: 13px 18px; border-bottom: 1px solid #f3f4f6; display: flex; align-items: center; gap: 10px; justify-content: space-between; }
.card-hd-left { display: flex; align-items: center; gap: 10px; }
.card-icon { width: 30px; height: 30px; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.card-icon svg { width: 14px; height: 14px; }
.card-icon.green { background: #dcfce7; color: #16a34a; }
.card-title { font-family: 'Syne', sans-serif; font-size: 12px; font-weight: 700; color: #0d1117; }
.count-pill { padding: 2px 8px; background: #f3f4f6; border-radius: 99px; font-size: 10px; color: #6b7280; }

.dt { width: 100%; border-collapse: collapse; }
.dt th { padding: 10px 14px; text-align: left; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .6px; color: #9ca3af; background: #f9fafb; border-bottom: 1px solid #e5e7eb; }
.dt td { padding: 12px 14px; font-size: 13px; color: #374151; border-bottom: 1px solid #f3f4f6; vertical-align: middle; }
.dt tbody tr:last-child td { border-bottom: none; }
.dt tbody tr:hover { background: #fafafa; }

.periode-badge { display: inline-flex; padding: 3px 9px; background: #f5e9c9; color: #8a6e2f; border-radius: 99px; font-size: 11px; font-weight: 600; font-family: 'Syne', sans-serif; }

.btn-icon {
    display: inline-flex; align-items: center; justify-content: center;
    width: 28px; height: 28px;
    border-radius: 7px;
    border: 1px solid #e5e7eb;
    background: #fff;
    color: #6b7280;
    text-decoration: none;
    transition: all .15s;
}
.btn-icon:hover { border-color: #c9a84c; color: #c9a84c; }

.empty-state { padding: 48px; text-align: center; color: #9ca3af; font-size: 13px; }
.empty-state svg { margin: 0 auto 12px; display: block; opacity: .3; }

/* ══════════════════════════════════
   PANEL 4 — FISCAL
   ══════════════════════════════════ */
.fiscal-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px; }
.fiscal-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 14px; padding: 20px; }
.fiscal-card-title { font-family: 'Syne', sans-serif; font-size: 12px; font-weight: 700; color: #0d1117; margin-bottom: 14px; display: flex; align-items: center; gap: 8px; }
.fiscal-icon { width: 24px; height: 24px; border-radius: 6px; display: flex; align-items: center; justify-content: center; }
.fiscal-icon svg { width: 12px; height: 12px; }
.fiscal-row { display: flex; justify-content: space-between; align-items: center; padding: 6px 0; border-bottom: 1px solid #f3f4f6; font-size: 12px; }
.fiscal-row:last-child { border-bottom: none; }
.fiscal-lbl { color: #6b7280; }
.fiscal-val { font-family: 'Syne', sans-serif; font-weight: 700; color: #0d1117; }
.fiscal-val.purple { color: #7c3aed; }
.fiscal-val.red { color: #dc2626; }
</style>

@php
    $moisPdf = $mois ?? ($paiements->isNotEmpty()
        ? (int)\Carbon\Carbon::parse($paiements->first()->periode)->format('n')
        : now()->month);
@endphp

<div style="padding: 0 0 48px">

    {{-- Breadcrumb + Actions --}}
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;flex-wrap:wrap;gap:10px">
        <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:#6b7280">
            <a href="{{ route('admin.bailleurs.index') }}" style="color:#6b7280;text-decoration:none">Portefeuille Bailleurs</a>
            <svg style="width:12px;height:12px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            <span style="color:#0d1117;font-weight:600">{{ $user->name }}</span>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap">
            <a href="{{ route('admin.bailleurs.export-pdf', [$user->id, 'annee' => $annee, 'mois' => $moisPdf]) }}"
               target="_blank"
               style="display:inline-flex;align-items:center;gap:7px;padding:8px 16px;background:#0d1117;color:#c9a84c;border:1px solid rgba(201,168,76,.3);border-radius:9px;font-size:12px;font-weight:600;text-decoration:none">
                <svg style="width:13px;height:13px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                Rapport mensuel
            </a>
            <a href="{{ route('admin.bailleurs.export-pdf', [$user->id, 'annee' => $annee]) }}"
               target="_blank"
               style="display:inline-flex;align-items:center;gap:7px;padding:8px 16px;background:#f9fafb;color:#374151;border:1px solid #e5e7eb;border-radius:9px;font-size:12px;font-weight:600;text-decoration:none">
                <svg style="width:13px;height:13px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                Rapport annuel {{ $annee }}
            </a>
            <a href="{{ route('admin.bailleurs.releve-pdf', [$user->id, 'annee' => $annee, 'mois' => $moisPdf]) }}"
               target="_blank"
               style="display:inline-flex;align-items:center;gap:7px;padding:8px 16px;background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0;border-radius:9px;font-size:12px;font-weight:600;text-decoration:none">
                <svg style="width:13px;height:13px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                Relevé propriétaire
            </a>
        </div>
    </div>

    {{-- ═══ HERO ════════════════════════════════ --}}
    <div class="hub-hero">
        <div class="hub-hero-left">
            <div class="hub-avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
            <div>
                <div class="hub-name">{{ $user->name }}</div>
                <div class="hub-meta">
                    @if($user->email) <strong>{{ $user->email }}</strong><br> @endif
                    @if($user->telephone) {{ $user->telephone }}<br> @endif
                    @php $profil = $user->proprietaire; @endphp
                    @if($profil?->ninea) NINEA : <strong>{{ $profil->ninea }}</strong> @endif
                </div>
            </div>
        </div>
        <div class="hub-kpi-row">
            <div class="hub-kpi">
                <div class="hub-kpi-lbl">Loyers encaissés</div>
                <div class="hub-kpi-val gold">{{ number_format($dashboard['total_loyers'], 0, ',', ' ') }}</div>
                <div class="hub-kpi-sub">F CFA — {{ $annee }}</div>
            </div>
            <div class="hub-kpi">
                <div class="hub-kpi-lbl">Net à reverser</div>
                <div class="hub-kpi-val green">{{ number_format($dashboard['net_final'], 0, ',', ' ') }}</div>
                <div class="hub-kpi-sub">F CFA net</div>
            </div>
            <div class="hub-kpi">
                <div class="hub-kpi-lbl">Biens loués</div>
                <div class="hub-kpi-val">{{ $dashboard['nb_biens_loues'] }}<span style="font-size:14px;color:rgba(255,255,255,.3)">/{{ $dashboard['nb_biens'] }}</span></div>
                <div class="hub-kpi-sub">{{ $dashboard['nb_paiements'] }} paiement(s)</div>
            </div>
        </div>
    </div>

    {{-- ═══ FILTRE PÉRIODE ════════════════════ --}}
    <form method="GET" class="hub-filter">
        <select name="annee" onchange="this.form.submit()">
            @foreach($anneesDisponibles as $a)
                <option value="{{ $a }}" @selected($a == $annee)>{{ $a }}</option>
            @endforeach
        </select>
        <select name="mois" onchange="this.form.submit()">
            <option value="">Toute l'année</option>
            @foreach(range(1,12) as $m)
                <option value="{{ $m }}" @selected($m == $mois)>
                    {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                </option>
            @endforeach
        </select>
        @if($mois)
            <a href="{{ route('admin.bailleurs.show', $user->id) }}?annee={{ $annee }}" class="hub-filter-reset">
                Voir toute l'année
            </a>
        @endif
    </form>

    {{-- ═══ ONGLETS ════════════════════════════ --}}
    <div class="hub-tabs" role="tablist">
        <button class="hub-tab active" role="tab" onclick="switchTab('synthese', this)" aria-selected="true">
            <svg style="width:13px;height:13px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
            Synthèse
        </button>
        <button class="hub-tab" role="tab" onclick="switchTab('biens', this)">
            <svg style="width:13px;height:13px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            Biens &amp; Locataires
            <span class="hub-tab-badge">{{ $biens->count() }}</span>
        </button>
        <button class="hub-tab" role="tab" onclick="switchTab('paiements', this)">
            <svg style="width:13px;height:13px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
            Historique
            <span class="hub-tab-badge">{{ $paiements->count() }}</span>
        </button>
        @if($dashboard['total_brs'] > 0 || $dashboard['total_dgid'] > 0)
        <button class="hub-tab" role="tab" onclick="switchTab('fiscal', this)">
            <svg style="width:13px;height:13px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/></svg>
            Fiscal
        </button>
        @endif
    </div>

    {{-- ═══════════════════════════════════════
         PANEL 1 — SYNTHÈSE FINANCIÈRE
         ═══════════════════════════════════════ --}}
    <div id="tab-synthese" class="hub-panel active" role="tabpanel">
        <div class="synth-grid">

            {{-- Colonne principale --}}
            <div>
                {{-- Équation financière --}}
                <div class="equation">
                    <div class="eq-block" style="background:#f5e9c9">
                        <div class="eq-block-lbl" style="color:#8a6e2f">Loyers encaissés</div>
                        <div class="eq-block-val" style="color:#c9a84c">{{ number_format($dashboard['total_loyers'], 0, ',', ' ') }} F</div>
                    </div>
                    <div class="eq-op">−</div>
                    <div class="eq-block" style="background:#dbeafe">
                        <div class="eq-block-lbl" style="color:#1d4ed8">Commissions TTC</div>
                        <div class="eq-block-val" style="color:#1d4ed8">{{ number_format($dashboard['total_commissions'], 0, ',', ' ') }} F</div>
                    </div>
                    @if(($dashboard['total_brs'] ?? 0) > 0)
                    <div class="eq-op">−</div>
                    <div class="eq-block" style="background:#fef2f2">
                        <div class="eq-block-lbl" style="color:#dc2626">BRS retenu</div>
                        <div class="eq-block-val" style="color:#dc2626">{{ number_format($dashboard['total_brs'], 0, ',', ' ') }} F</div>
                    </div>
                    @endif
                    @if($dashboard['total_depenses'] > 0)
                    <div class="eq-op">−</div>
                    <div class="eq-block" style="background:#fee2e2">
                        <div class="eq-block-lbl" style="color:#dc2626">Dépenses</div>
                        <div class="eq-block-val" style="color:#dc2626">{{ number_format($dashboard['total_depenses'], 0, ',', ' ') }} F</div>
                    </div>
                    @endif
                    <div class="eq-op" style="color:#0d1117">=</div>
                    <div class="eq-result">
                        <div class="eq-result-lbl">Net à verser</div>
                        <div class="eq-result-val">{{ number_format($dashboard['net_final'], 0, ',', ' ') }} F</div>
                    </div>
                </div>

                {{-- Biens aperçu rapide --}}
                <div class="card">
                    <div class="card-hd">
                        <div class="card-hd-left">
                            <div class="card-icon green">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                            </div>
                            <div class="card-title">Aperçu Biens</div>
                        </div>
                        <button class="hub-tab" style="padding:5px 12px;font-size:11px" onclick="switchTab('biens', document.querySelector('[onclick*=biens]'))">
                            Voir tout →
                        </button>
                    </div>
                    @foreach($biens->take(3) as $bien)
                    <div style="display:flex;align-items:center;gap:12px;padding:11px 18px;border-bottom:1px solid #f3f4f6">
                        <div style="flex:1">
                            <a href="{{ route('admin.biens.show', $bien) }}" style="font-family:'Syne',sans-serif;font-size:12px;font-weight:700;color:#0d1117;text-decoration:none">{{ $bien->reference }}</a>
                            <div style="font-size:11px;color:#6b7280">{{ $bien->adresse }}{{ $bien->ville ? ', '.$bien->ville : '' }}</div>
                        </div>
                        <span class="statut-badge s-{{ $bien->statut }}">
                            <span class="s-dot"></span>
                            {{ \App\Models\Bien::STATUTS[$bien->statut] ?? $bien->statut }}
                        </span>
                        @if($bien->contratActif)
                        <div style="text-align:right;min-width:80px">
                            <div style="font-family:'Syne',sans-serif;font-size:13px;font-weight:700;color:#0d1117">{{ number_format($bien->loyer_mensuel, 0, ',', ' ') }} F</div>
                            <div style="font-size:10px;color:#9ca3af">/mois</div>
                        </div>
                        @endif
                    </div>
                    @endforeach
                    @if($biens->count() > 3)
                    <div style="padding:10px 18px;font-size:12px;color:#9ca3af;text-align:center">
                        + {{ $biens->count() - 3 }} autre(s) bien(s) — <a href="#" onclick="switchTab('biens', document.querySelector('[onclick*=biens]'));return false" style="color:#c9a84c;text-decoration:none">voir tous</a>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Colonne droite — résumé dark --}}
            <div>
                <div class="rp">
                    <div class="rp-hd">
                        <div class="rp-title">Résumé financier</div>
                        <span style="font-size:11px;color:rgba(255,255,255,.25)">{{ $annee }}{{ $mois ? ' / M'.str_pad($mois,2,'0',STR_PAD_LEFT) : '' }}</span>
                    </div>
                    <div class="rp-body">
                        <div class="rp-row">
                            <span class="rp-lbl">Paiements</span>
                            <span class="rp-val">{{ $dashboard['nb_paiements'] }}</span>
                        </div>
                        <div class="rp-row">
                            <span class="rp-lbl">Biens loués</span>
                            <span class="rp-val">{{ $dashboard['nb_biens_loues'] }} / {{ $dashboard['nb_biens'] }}</span>
                        </div>
                        <div class="rp-sep"></div>
                        <div class="rp-row">
                            <span class="rp-lbl">Loyers encaissés</span>
                            <span class="rp-val gold">{{ number_format($dashboard['total_loyers'], 0, ',', ' ') }} F</span>
                        </div>
                        <div class="rp-row">
                            <span class="rp-lbl">Commissions TTC</span>
                            <span class="rp-val red">− {{ number_format($dashboard['total_commissions'], 0, ',', ' ') }} F</span>
                        </div>
                        @if($dashboard['total_depenses'] > 0)
                        <div class="rp-row">
                            <span class="rp-lbl">Dépenses gestion</span>
                            <span class="rp-val red">− {{ number_format($dashboard['total_depenses'], 0, ',', ' ') }} F</span>
                        </div>
                        @endif
                        @if($dashboard['total_brs'] > 0)
                        <div class="rp-sep"></div>
                        <div class="rp-row">
                            <span class="rp-lbl">BRS retenu</span>
                            <span class="rp-val red">{{ number_format($dashboard['total_brs'], 0, ',', ' ') }} F</span>
                        </div>
                        @endif
                        @if($dashboard['total_dgid'] > 0)
                        <div class="rp-row">
                            <span class="rp-lbl">DGID enregistrement</span>
                            <span class="rp-val" style="color:#a78bfa">{{ number_format($dashboard['total_dgid'], 0, ',', ' ') }} F</span>
                        </div>
                        @endif
                        <div class="rp-net">
                            <div class="rp-net-lbl">Montant final à verser</div>
                            <div class="rp-net-val">{{ number_format($dashboard['net_final'], 0, ',', ' ') }} F</div>
                        </div>
                        <a href="{{ route('admin.bailleurs.export-pdf', [$user->id, 'annee' => $annee, 'mois' => $moisPdf]) }}"
                           target="_blank" class="btn-pdf">
                            <svg style="width:13px;height:13px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                            Rapport mensuel PDF
                        </a>
                        <a href="{{ route('admin.bailleurs.export-pdf', [$user->id, 'annee' => $annee]) }}"
                           target="_blank" class="btn-pdf" style="margin-top:8px;background:#f9fafb;color:#374151;border-color:#e5e7eb">
                            <svg style="width:13px;height:13px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                            Rapport annuel {{ $annee }}
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- ═══════════════════════════════════════
         PANEL 2 — BIENS & LOCATAIRES
         ═══════════════════════════════════════ --}}
    <div id="tab-biens" class="hub-panel" role="tabpanel">

        @if($biens->isEmpty())
        <div class="empty-state">
            <svg style="width:40px;height:40px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
            <div style="font-weight:600;color:#374151;margin-bottom:4px">Aucun bien associé</div>
            <div>Ajoutez des biens à ce propriétaire depuis la gestion des biens.</div>
            <a href="{{ route('admin.biens.create') }}" style="display:inline-flex;align-items:center;gap:6px;margin-top:16px;padding:9px 18px;background:#c9a84c;color:#0d1117;border-radius:9px;font-size:13px;font-weight:600;text-decoration:none">
                + Créer un bien
            </a>
        </div>
        @else
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(360px,1fr));gap:14px">
            @foreach($biens as $bien)
            <div class="bien-card">
                <div class="bien-card-header">
                    <div class="bien-ref-block">
                        <a href="{{ route('admin.biens.show', $bien) }}" style="text-decoration:none">
                            <div class="bien-ref">{{ $bien->reference }}</div>
                        </a>
                        <div class="bien-addr">
                            {{ $bien->adresse }}{{ $bien->ville ? ', '.$bien->ville : '' }}
                            @if($bien->surface) · {{ $bien->surface }} m² @endif
                        </div>
                    </div>
                    <div>
                        <span class="statut-badge s-{{ $bien->statut }}">
                            <span class="s-dot"></span>
                            {{ \App\Models\Bien::STATUTS[$bien->statut] ?? $bien->statut }}
                        </span>
                    </div>
                    @if($bien->contratActif)
                    <div style="text-align:right">
                        <div class="bien-loyer">{{ number_format($bien->loyer_mensuel, 0, ',', ' ') }} F</div>
                        <div class="bien-loyer-sub">/mois</div>
                    </div>
                    @endif
                </div>

                @if($bien->contratActif?->locataire)
                <div class="bien-card-tenant">
                    <div class="tenant-avatar">
                        {{ strtoupper(substr($bien->contratActif->locataire->name, 0, 1)) }}
                    </div>
                    <div>
                        <div class="tenant-name">{{ $bien->contratActif->locataire->name }}</div>
                        <div class="tenant-meta">
                            Locataire actif
                            @if($bien->contratActif->date_debut)
                             · depuis {{ \Carbon\Carbon::parse($bien->contratActif->date_debut)->translatedFormat('M Y') }}
                            @endif
                        </div>
                    </div>
                    <a href="{{ route('admin.contrats.show', $bien->contratActif) }}" class="tenant-contract-link">
                        <svg style="width:11px;height:11px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                        Voir contrat
                    </a>
                </div>
                @else
                <div class="bien-card-empty">
                    <svg style="width:14px;height:14px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    Aucun locataire actif
                    <a href="{{ route('admin.contrats.create') }}?bien_id={{ $bien->id }}" class="btn-assign">
                        + Assigner un locataire
                    </a>
                </div>
                @endif
            </div>
            @endforeach
        </div>
        @endif

    </div>

    {{-- ═══════════════════════════════════════
         PANEL 3 — HISTORIQUE PAIEMENTS
         ═══════════════════════════════════════ --}}
    <div id="tab-paiements" class="hub-panel" role="tabpanel">
        <div class="card">
            <div class="card-hd">
                <div class="card-hd-left">
                    <div class="card-icon green">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                    </div>
                    <div class="card-title">
                        Paiements validés — {{ $annee }}{{ $mois ? ' / '.str_pad($mois,2,'0',STR_PAD_LEFT) : '' }}
                    </div>
                    <span class="count-pill">{{ $paiements->count() }}</span>
                </div>
                @if($paiements->count() > 0)
                <div style="font-size:11px;color:#6b7280">
                    Total : <strong style="color:#16a34a;font-family:'Syne',sans-serif">{{ number_format($dashboard['total_loyers'], 0, ',', ' ') }} F</strong> encaissés
                </div>
                @endif
            </div>

            @if($paiements->isEmpty())
            <div class="empty-state">
                <svg style="width:36px;height:36px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                Aucun paiement validé sur cette période.
            </div>
            @else
            <div style="overflow-x:auto">
                <table class="dt">
                    <thead>
                        <tr>
                            <th>Période</th>
                            <th>Bien</th>
                            <th>Locataire</th>
                            <th style="text-align:right">Loyer encaissé</th>
                            <th style="text-align:right">Commission TTC</th>
                            <th style="text-align:right">Net final</th>
                            <th style="text-align:center">PDF</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($paiements as $p)
                        @php
                            $depMois = (float) $p->depenses->sum('montant');
                            $netBailleur = (float)($p->montant_net_bailleur ?? $p->net_a_verser_proprietaire ?? 0);
                            $netFinalLigne = round($netBailleur - $depMois, 2);
                        @endphp
                        <tr>
                            <td>
                                <span class="periode-badge">
                                    {{ \Carbon\Carbon::parse($p->periode)->translatedFormat('M Y') }}
                                </span>
                            </td>
                            <td style="font-size:12px;font-weight:600;color:#374151">
                                {{ $p->contrat?->bien?->reference ?? '—' }}
                            </td>
                            <td style="font-size:12px;color:#6b7280">
                                {{ $p->contrat?->locataire?->name ?? '—' }}
                            </td>
                            <td style="text-align:right;font-family:'Syne',sans-serif;font-weight:700;color:#0d1117">
                                {{ number_format($p->montant_encaisse, 0, ',', ' ') }} F
                            </td>
                            <td style="text-align:right;color:#8a6e2f;font-size:12px">
                                {{ number_format($p->commission_ttc ?? 0, 0, ',', ' ') }} F
                            </td>
                            <td style="text-align:right;font-weight:700;color:#16a34a;font-family:'Syne',sans-serif">
                                {{ number_format($netFinalLigne, 0, ',', ' ') }} F
                            </td>
                            <td style="text-align:center">
                                <a href="{{ route('admin.paiements.show', $p) }}" class="btn-icon" title="Voir quittance">
                                    <svg style="width:12px;height:12px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>

    {{-- ═══════════════════════════════════════
         PANEL 4 — FISCAL (conditionnel)
         ═══════════════════════════════════════ --}}
    @if($dashboard['total_brs'] > 0 || $dashboard['total_dgid'] > 0)
    <div id="tab-fiscal" class="hub-panel" role="tabpanel">
        <div class="fiscal-grid">
            @if($dashboard['total_brs'] > 0)
            <div class="fiscal-card">
                <div class="fiscal-card-title">
                    <div class="fiscal-icon" style="background:#fee2e2;color:#dc2626">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    </div>
                    BRS — Retenue à la source
                </div>
                <div class="fiscal-row">
                    <span class="fiscal-lbl">Total BRS retenu</span>
                    <span class="fiscal-val red">{{ number_format($dashboard['total_brs'], 0, ',', ' ') }} F</span>
                </div>
                <div class="fiscal-row">
                    <span class="fiscal-lbl">Exercice</span>
                    <span class="fiscal-val">{{ $annee }}</span>
                </div>
                <div style="margin-top:12px;padding:10px 12px;background:#fff5f5;border-radius:8px;font-size:11px;color:#6b7280;line-height:1.6">
                    La retenue à la source BRS est déduite avant reversement au propriétaire conformément à la réglementation fiscale en vigueur.
                </div>
            </div>
            @endif

            @if($dashboard['total_dgid'] > 0)
            <div class="fiscal-card">
                <div class="fiscal-card-title">
                    <div class="fiscal-icon" style="background:#ede9fe;color:#7c3aed">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                    </div>
                    DGID — Droits d'enregistrement
                </div>
                <div class="fiscal-row">
                    <span class="fiscal-lbl">Total DGID</span>
                    <span class="fiscal-val purple">{{ number_format($dashboard['total_dgid'], 0, ',', ' ') }} F</span>
                </div>
                <div class="fiscal-row">
                    <span class="fiscal-lbl">Exercice</span>
                    <span class="fiscal-val">{{ $annee }}</span>
                </div>
                <div style="margin-top:12px;padding:10px 12px;background:#f5f3ff;border-radius:8px;font-size:11px;color:#6b7280;line-height:1.6">
                    Droits d'enregistrement des contrats de bail auprès de la DGID — Direction Générale des Impôts et Domaines.
                </div>
            </div>
            @endif
        </div>
    </div>
    @endif

</div>

<script>
function switchTab(tabId, btn) {
    // Désactiver tous les onglets
    document.querySelectorAll('.hub-tab').forEach(t => {
        t.classList.remove('active');
        t.setAttribute('aria-selected', 'false');
    });
    document.querySelectorAll('.hub-panel').forEach(p => p.classList.remove('active'));

    // Activer l'onglet sélectionné
    if (btn) {
        btn.classList.add('active');
        btn.setAttribute('aria-selected', 'true');
    }
    const panel = document.getElementById('tab-' + tabId);
    if (panel) panel.classList.add('active');
}
</script>
@endsection
