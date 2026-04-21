@extends('layouts.app')
@section('title', 'Portefeuille Bailleurs')
@section('breadcrumb', 'Portefeuille Bailleurs')

@section('content')
<style>
/* ── KPI row ── */
.kpi-row { display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:24px; }
.kpi { background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:16px 18px; }
.kpi.gold  { border-top:3px solid #c9a84c; }
.kpi.green { border-top:3px solid #16a34a; }
.kpi.blue  { border-top:3px solid #1d4ed8; }
.kpi.dark  { border-top:3px solid #0d1117; }
.kpi-lbl { font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#9ca3af;margin-bottom:6px; }
.kpi-val { font-family:'Syne',sans-serif;font-size:20px;font-weight:700;color:#0d1117;line-height:1.1; }
.kpi-sub { font-size:11px;color:#9ca3af;margin-top:4px; }

/* ── Cards bailleurs ── */
.cards-grid { display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:16px; }

.bailleur-card {
    background:#fff;
    border:1px solid #e5e7eb;
    border-radius:14px;
    overflow:hidden;
    transition:box-shadow .15s, border-color .15s;
    text-decoration:none;
    display:flex;
    flex-direction:column;
}
.bailleur-card:hover {
    box-shadow:0 4px 20px rgba(201,168,76,.15);
    border-color:#c9a84c;
}

.card-header {
    background:#0d1117;
    padding:16px 20px;
    display:flex;
    align-items:center;
    gap:12px;
}
.card-avatar {
    width:42px; height:42px;
    border-radius:50%;
    background:rgba(201,168,76,.15);
    border:2px solid rgba(201,168,76,.3);
    display:flex; align-items:center; justify-content:center;
    font-family:'Syne',sans-serif;
    font-size:16px; font-weight:700;
    color:#c9a84c;
    flex-shrink:0;
}
.card-name {
    font-family:'Syne',sans-serif;
    font-size:14px; font-weight:700;
    color:#fff;
    line-height:1.2;
}
.card-email { font-size:11px;color:rgba(255,255,255,.35);margin-top:2px; }

.card-kpis {
    display:grid;
    grid-template-columns:1fr 1fr 1fr;
    border-bottom:1px solid #f3f4f6;
}
.card-kpi {
    padding:12px 14px;
    border-right:1px solid #f3f4f6;
    text-align:center;
}
.card-kpi:last-child { border-right:none; }
.card-kpi-lbl { font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:#9ca3af;margin-bottom:4px; }
.card-kpi-val { font-family:'Syne',sans-serif;font-size:13px;font-weight:700;color:#0d1117; }

.card-financier { padding:14px 18px; flex:1; }
.fin-row { display:flex;justify-content:space-between;align-items:center;padding:5px 0;border-bottom:1px solid #f9fafb;font-size:12px; }
.fin-row:last-child { border-bottom:none; }
.fin-lbl { color:#6b7280; }
.fin-val { font-family:'Syne',sans-serif;font-weight:600;color:#0d1117; }
.fin-val.green { color:#16a34a; }
.fin-val.red   { color:#dc2626; }
.fin-val.gold  { color:#c9a84c; }

.net-final {
    margin:10px 18px 16px;
    padding:10px 14px;
    background:linear-gradient(135deg,#0d1117,#1c2333);
    border-radius:10px;
    border:1px solid rgba(201,168,76,.25);
    display:flex;
    justify-content:space-between;
    align-items:center;
}
.net-lbl { font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:rgba(201,168,76,.6); }
.net-val { font-family:'Syne',sans-serif;font-size:16px;font-weight:700;color:#c9a84c; }

</style>

<div style="padding:0 0 48px">

    {{-- Header ─────────────────────────────────── --}}
    <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:22px;flex-wrap:wrap;gap:12px">
        <div>
            <h1 style="font-family:'Syne',sans-serif;font-size:22px;font-weight:700;color:#0d1117;letter-spacing:-.4px">
                Portefeuille Bailleurs
            </h1>
            <p style="font-size:13px;color:#6b7280;margin-top:3px">
                {{ $bailleurs->count() }} propriétaire(s) — Exercice {{ now()->year }}
            </p>
        </div>
    </div>

    @php
        $totLoyers     = $bailleurs->sum('total_loyers');
        $totComm       = $bailleurs->sum('total_commissions');
        $totDepenses   = $bailleurs->sum('total_depenses');
        $totNet        = $bailleurs->sum('net_final');
    @endphp

    {{-- KPI globaux ─────────────────────────────── --}}
    <div class="kpi-row">
        <div class="kpi gold">
            <div class="kpi-lbl">Loyers encaissés</div>
            <div class="kpi-val">{{ number_format($totLoyers, 0, ',', ' ') }}</div>
            <div class="kpi-sub">FCFA — {{ now()->year }}</div>
        </div>
        <div class="kpi blue">
            <div class="kpi-lbl">Commissions TTC</div>
            <div class="kpi-val" style="color:#1d4ed8">{{ number_format($totComm, 0, ',', ' ') }}</div>
            <div class="kpi-sub">FCFA agence</div>
        </div>
        <div class="kpi" style="border-top:3px solid #dc2626">
            <div class="kpi-lbl">Dépenses gestion</div>
            <div class="kpi-val" style="color:#dc2626">{{ number_format($totDepenses, 0, ',', ' ') }}</div>
            <div class="kpi-sub">FCFA travaux & frais</div>
        </div>
        <div class="kpi green">
            <div class="kpi-lbl">Net total à reverser</div>
            <div class="kpi-val" style="color:#16a34a">{{ number_format($totNet, 0, ',', ' ') }}</div>
            <div class="kpi-sub">FCFA aux bailleurs</div>
        </div>
    </div>

    {{-- Cards ──────────────────────────────────── --}}
    @if($bailleurs->isEmpty())
        <x-empty-state
            title="Aucun bailleur trouvé"
            description="Ajoutez des biens avec des propriétaires pour les voir apparaître ici."
        />
    @else
        <div class="cards-grid">
            @foreach($bailleurs as $b)
            @php $u = $b['user']; @endphp
            <a href="{{ route('admin.bailleurs.show', $u->id) }}" class="bailleur-card">

                {{-- En-tête sombre --}}
                <div class="card-header">
                    <div class="card-avatar">{{ strtoupper(substr($u->name, 0, 1)) }}</div>
                    <div>
                        <div class="card-name">{{ $u->name }}</div>
                        <div class="card-email">{{ $u->email }}</div>
                    </div>
                    <div style="margin-left:auto;text-align:right">
                        @if($u->telephone)
                        <div style="font-size:11px;color:rgba(255,255,255,.4)">{{ $u->telephone }}</div>
                        @endif
                    </div>
                </div>

                {{-- KPIs biens --}}
                <div class="card-kpis">
                    <div class="card-kpi">
                        <div class="card-kpi-lbl">Biens</div>
                        <div class="card-kpi-val">{{ $b['nb_biens'] }}</div>
                    </div>
                    <div class="card-kpi">
                        <div class="card-kpi-lbl">Loués</div>
                        <div class="card-kpi-val" style="color:#16a34a">{{ $b['nb_biens_loues'] }}</div>
                    </div>
                    <div class="card-kpi">
                        <div class="card-kpi-lbl">Paiements</div>
                        <div class="card-kpi-val">{{ $b['nb_paiements'] }}</div>
                    </div>
                </div>

                {{-- Résumé financier --}}
                <div class="card-financier">
                    <div class="fin-row">
                        <span class="fin-lbl">Loyers encaissés</span>
                        <span class="fin-val gold">{{ number_format($b['total_loyers'], 0, ',', ' ') }} F</span>
                    </div>
                    <div class="fin-row">
                        <span class="fin-lbl">− Commissions TTC</span>
                        <span class="fin-val red">{{ number_format($b['total_commissions'], 0, ',', ' ') }} F</span>
                    </div>
                    @if($b['total_depenses'] > 0)
                    <div class="fin-row">
                        <span class="fin-lbl">− Dépenses gestion</span>
                        <span class="fin-val red">{{ number_format($b['total_depenses'], 0, ',', ' ') }} F</span>
                    </div>
                    @endif
                </div>

                {{-- Net final --}}
                <div class="net-final">
                    <div>
                        <div class="net-lbl">Net à reverser</div>
                        <div style="font-size:10px;color:rgba(255,255,255,.25);margin-top:2px">{{ now()->year }} — tous mois</div>
                    </div>
                    <div class="net-val">{{ number_format($b['net_final'], 0, ',', ' ') }} F</div>
                </div>

            </a>
            @endforeach
        </div>
    @endif

</div>
@endsection
