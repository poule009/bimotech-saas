@extends('layouts.app')
@section('title', 'Fiche Bailleur — ' . $user->name)
@section('breadcrumb', 'Portefeuille Bailleurs › ' . $user->name)

@section('content')
<style>
.page-grid { display:grid;grid-template-columns:1fr 300px;gap:24px;align-items:start; }

/* Hero bailleur */
.hero {
    background:linear-gradient(135deg,#0d1117,#1c2333);
    border-radius:14px;
    padding:24px 28px;
    margin-bottom:22px;
    display:flex;
    align-items:center;
    justify-content:space-between;
    flex-wrap:wrap;
    gap:16px;
}
.hero-avatar {
    width:56px; height:56px;
    border-radius:50%;
    background:rgba(201,168,76,.15);
    border:2px solid rgba(201,168,76,.4);
    display:flex; align-items:center; justify-content:center;
    font-family:'Syne',sans-serif;
    font-size:22px; font-weight:700;
    color:#c9a84c;
    flex-shrink:0;
}
.hero-name { font-family:'Syne',sans-serif;font-size:20px;font-weight:700;color:#fff;margin-bottom:4px; }
.hero-sub  { font-size:12px;color:rgba(255,255,255,.35); }
.net-badge {
    text-align:right;
    background:rgba(201,168,76,.1);
    border:1px solid rgba(201,168,76,.25);
    border-radius:10px;
    padding:12px 18px;
}
.net-badge-lbl { font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:rgba(201,168,76,.6);margin-bottom:4px; }
.net-badge-val { font-family:'Syne',sans-serif;font-size:24px;font-weight:700;color:#c9a84c; }

/* Dashboard financier */
.dash-grid { display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:20px; }
.dash-card { background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:14px 16px; }
.dash-lbl { font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.7px;color:#9ca3af;margin-bottom:5px; }
.dash-val { font-family:'Syne',sans-serif;font-size:18px;font-weight:700;color:#0d1117; }
.dash-sub { font-size:11px;color:#9ca3af;margin-top:3px; }

/* Équation financière */
.equation {
    background:#fff;border:1px solid #e5e7eb;border-radius:12px;
    padding:16px 20px;margin-bottom:20px;
    display:flex;align-items:center;gap:8px;flex-wrap:wrap;
}
.eq-block {
    text-align:center;padding:8px 14px;border-radius:8px;min-width:100px;
}
.eq-block-lbl { font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;margin-bottom:4px; }
.eq-block-val { font-family:'Syne',sans-serif;font-size:14px;font-weight:700; }
.eq-op { font-family:'Syne',sans-serif;font-size:20px;font-weight:700;color:#9ca3af; }
.eq-result { background:#0d1117;border-radius:8px;padding:10px 18px;text-align:center; }
.eq-result-lbl { font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:rgba(201,168,76,.6);margin-bottom:4px; }
.eq-result-val { font-family:'Syne',sans-serif;font-size:16px;font-weight:700;color:#c9a84c; }

/* Card standard */
.card { background:#fff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden;margin-bottom:16px; }
.card-hd { padding:12px 18px;border-bottom:1px solid #f3f4f6;display:flex;align-items:center;gap:10px; }
.card-icon { width:28px;height:28px;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0; }
.card-icon svg { width:13px;height:13px; }
.card-icon.gold  { background:#f5e9c9;color:#8a6e2f; }
.card-icon.green { background:#dcfce7;color:#16a34a; }
.card-icon.blue  { background:#dbeafe;color:#1d4ed8; }
.card-icon.red   { background:#fee2e2;color:#dc2626; }
.card-title { font-family:'Syne',sans-serif;font-size:12px;font-weight:700;color:#0d1117; }

/* Table paiements */
.dt { width:100%;border-collapse:collapse; }
.dt th { padding:9px 14px;text-align:left;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:#9ca3af;background:#f9fafb;border-bottom:1px solid #e5e7eb; }
.dt td { padding:11px 14px;font-size:13px;color:#374151;border-bottom:1px solid #f3f4f6;vertical-align:middle; }
.dt tbody tr:last-child td { border-bottom:none; }
.dt tbody tr:hover { background:#fafafa; }

/* Table biens */
.bien-row { display:flex;align-items:center;gap:12px;padding:12px 18px;border-bottom:1px solid #f3f4f6; }
.bien-row:last-child { border-bottom:none; }
.bien-ref { font-family:'Syne',sans-serif;font-size:12px;font-weight:700;color:#0d1117; }
.bien-addr { font-size:11px;color:#6b7280;margin-top:2px; }
.statut-badge { display:inline-flex;align-items:center;gap:4px;padding:3px 9px;border-radius:99px;font-size:10px;font-weight:600; }
.s-loue { background:#dcfce7;color:#16a34a; }
.s-disponible { background:#f3f4f6;color:#6b7280; }
.s-travaux { background:#fef9c3;color:#a16207; }

/* Panel droit */
.right-panel { background:#0d1117;border-radius:14px;overflow:hidden; }
.rp-hd { padding:14px 16px;border-bottom:1px solid rgba(255,255,255,.07); }
.rp-title { font-family:'Syne',sans-serif;font-size:12px;font-weight:700;color:#fff; }
.rp-body { padding:14px 16px; }
.rp-row { display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px solid rgba(255,255,255,.05);font-size:12px; }
.rp-row:last-child { border-bottom:none; }
.rp-lbl { color:rgba(255,255,255,.35); }
.rp-val { color:#e6edf3;font-weight:600;font-family:'Syne',sans-serif; }
.rp-val.gold  { color:#c9a84c; }
.rp-val.green { color:#4ade80; }
.rp-val.red   { color:#f87171; }
.rp-sep { height:1px;background:rgba(255,255,255,.06);margin:8px 0; }
.rp-net { background:rgba(201,168,76,.1);border:1px solid rgba(201,168,76,.2);border-radius:8px;padding:10px 12px;margin-top:10px; }
.rp-net-lbl { font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:rgba(201,168,76,.6);margin-bottom:3px; }
.rp-net-val { font-family:'Syne',sans-serif;font-size:20px;font-weight:700;color:#c9a84c; }
</style>

<div style="padding:0 0 48px">

    {{-- Breadcrumb + actions --}}
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;flex-wrap:wrap;gap:10px">
        <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:#6b7280">
            <a href="{{ route('admin.bailleurs.index') }}" style="color:#6b7280;text-decoration:none">Portefeuille Bailleurs</a>
            <svg style="width:12px;height:12px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            <span style="color:#0d1117;font-weight:500">{{ $user->name }}</span>
        </div>
        <a href="{{ route('admin.bailleurs.export-pdf', [$user->id, 'annee' => $annee, 'mois' => $mois ?? now()->month]) }}"
           target="_blank"
           style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;background:#0d1117;color:#c9a84c;border:1px solid rgba(201,168,76,.3);border-radius:9px;font-size:12px;font-weight:600;text-decoration:none">
            <svg style="width:13px;height:13px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            Rapport de gestion PDF
        </a>
    </div>

    {{-- Hero ────────────────────────────────────── --}}
    <div class="hero">
        <div style="display:flex;align-items:center;gap:16px">
            <div class="hero-avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
            <div>
                <div class="hero-name">{{ $user->name }}</div>
                <div class="hero-sub">{{ $user->email }}</div>
                @if($user->telephone)
                <div class="hero-sub" style="margin-top:3px">{{ $user->telephone }}</div>
                @endif
                @php $profil = $user->proprietaire; @endphp
                @if($profil?->ninea)
                <div class="hero-sub" style="margin-top:3px">NINEA : {{ $profil->ninea }}</div>
                @endif
            </div>
        </div>
        <div class="net-badge">
            <div class="net-badge-lbl">Net à reverser — {{ $annee }}</div>
            <div class="net-badge-val">{{ number_format($dashboard['net_final'], 0, ',', ' ') }} F</div>
            <div style="font-size:10px;color:rgba(201,168,76,.5);margin-top:3px">
                {{ $dashboard['nb_paiements'] }} paiement(s) · {{ $dashboard['nb_biens_loues'] }}/{{ $dashboard['nb_biens'] }} biens loués
            </div>
        </div>
    </div>

    {{-- Filtre période ──────────────────────────── --}}
    <form method="GET" style="display:flex;gap:8px;margin-bottom:20px;align-items:center">
        <select name="annee" onchange="this.form.submit()"
                style="padding:8px 12px;border:1px solid #e5e7eb;border-radius:8px;font-size:13px;font-family:'DM Sans',sans-serif">
            @foreach($anneesDisponibles as $a)
                <option value="{{ $a }}" @selected($a == $annee)>{{ $a }}</option>
            @endforeach
        </select>
        <select name="mois" onchange="this.form.submit()"
                style="padding:8px 12px;border:1px solid #e5e7eb;border-radius:8px;font-size:13px;font-family:'DM Sans',sans-serif">
            <option value="">Toute l'année</option>
            @foreach(range(1,12) as $m)
                <option value="{{ $m }}" @selected($m == $mois)>
                    {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                </option>
            @endforeach
        </select>
        @if($mois)
            <a href="{{ route('admin.bailleurs.show', $user->id) }}?annee={{ $annee }}"
               style="padding:8px 14px;border:1px solid #e5e7eb;border-radius:8px;font-size:13px;color:#6b7280;text-decoration:none;background:#fff">
                Voir l'année
            </a>
        @endif
    </form>

    {{-- Équation financière ─────────────────────── --}}
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
        @if($dashboard['total_depenses'] > 0)
        <div class="eq-op">−</div>
        <div class="eq-block" style="background:#fee2e2">
            <div class="eq-block-lbl" style="color:#dc2626">Dépenses gestion</div>
            <div class="eq-block-val" style="color:#dc2626">{{ number_format($dashboard['total_depenses'], 0, ',', ' ') }} F</div>
        </div>
        @endif
        <div class="eq-op" style="color:#0d1117">=</div>
        <div class="eq-result">
            <div class="eq-result-lbl">Montant final à verser</div>
            <div class="eq-result-val">{{ number_format($dashboard['net_final'], 0, ',', ' ') }} F</div>
        </div>
        @if($dashboard['total_brs'] > 0)
        <div style="margin-left:auto;font-size:11px;color:#6b7280;text-align:right">
            <div>BRS retenu : <strong style="color:#dc2626">{{ number_format($dashboard['total_brs'], 0, ',', ' ') }} F</strong></div>
            <div>DGID : <strong style="color:#7c3aed">{{ number_format($dashboard['total_dgid'], 0, ',', ' ') }} F</strong></div>
        </div>
        @endif
    </div>

    <div class="page-grid">

        {{-- COLONNE GAUCHE ──────────────────────── --}}
        <div>

            {{-- Tableau des biens ─────────────────── --}}
            <div class="card">
                <div class="card-hd">
                    <div class="card-icon gold">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    </div>
                    <div class="card-title">Biens gérés ({{ $biens->count() }})</div>
                </div>
                @foreach($biens as $bien)
                <div class="bien-row">
                    <div style="flex:1">
                        <a href="{{ route('admin.biens.show', $bien) }}"
                           style="text-decoration:none">
                            <div class="bien-ref">{{ $bien->reference }}</div>
                        </a>
                        <div class="bien-addr">{{ $bien->adresse }}{{ $bien->ville ? ', '.$bien->ville : '' }}</div>
                        @if($bien->contratActif?->locataire)
                        <div style="font-size:11px;color:#6b7280;margin-top:2px">
                            Locataire : {{ $bien->contratActif->locataire->name }}
                        </div>
                        @endif
                    </div>
                    <div>
                        <span class="statut-badge s-{{ $bien->statut }}">
                            {{ \App\Models\Bien::STATUTS[$bien->statut] ?? $bien->statut }}
                        </span>
                    </div>
                    @if($bien->contratActif)
                    <div style="text-align:right">
                        <div style="font-family:'Syne',sans-serif;font-size:13px;font-weight:700;color:#0d1117">
                            {{ number_format($bien->loyer_mensuel, 0, ',', ' ') }} F
                        </div>
                        <div style="font-size:10px;color:#9ca3af">/mois</div>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>

            {{-- Historique des paiements ──────────── --}}
            <div class="card">
                <div class="card-hd">
                    <div class="card-icon green">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                    </div>
                    <div class="card-title">
                        Paiements validés — {{ $annee }}{{ $mois ? ' / '.str_pad($mois,2,'0',STR_PAD_LEFT) : '' }}
                        <span style="margin-left:6px;padding:2px 7px;background:#f3f4f6;border-radius:99px;font-size:10px;color:#6b7280">
                            {{ $paiements->count() }}
                        </span>
                    </div>
                </div>
                @if($paiements->isEmpty())
                    <div style="padding:40px;text-align:center;color:#9ca3af;font-size:13px">
                        Aucun paiement validé sur cette période.
                    </div>
                @else
                <div style="overflow-x:auto">
                    <table class="dt">
                        <thead>
                            <tr>
                                <th>Période</th>
                                <th>Bien</th>
                                <th style="text-align:right">Loyer encaissé</th>
                                <th style="text-align:right">Commission TTC</th>
                                <th style="text-align:right">Dépenses</th>
                                <th style="text-align:right">Net final</th>
                                <th style="text-align:center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($paiements as $p)
                            @php
                                $depMois   = (float) $p->depenses->sum('montant');
                                $netBailleur = (float)($p->montant_net_bailleur ?? $p->net_a_verser_proprietaire ?? 0);
                                $netFinalLigne = round($netBailleur - $depMois, 2);
                            @endphp
                            <tr>
                                <td>
                                    <span style="display:inline-flex;padding:3px 9px;background:#f5e9c9;color:#8a6e2f;border-radius:99px;font-size:11px;font-weight:600;font-family:'Syne',sans-serif">
                                        {{ \Carbon\Carbon::parse($p->periode)->translatedFormat('M Y') }}
                                    </span>
                                </td>
                                <td style="font-size:12px">{{ $p->contrat?->bien?->reference ?? '—' }}</td>
                                <td style="text-align:right;font-family:'Syne',sans-serif;font-weight:700;color:#0d1117">
                                    {{ number_format($p->montant_encaisse, 0, ',', ' ') }} F
                                </td>
                                <td style="text-align:right;color:#8a6e2f;font-size:12px">
                                    {{ number_format($p->commission_ttc ?? 0, 0, ',', ' ') }} F
                                </td>
                                <td style="text-align:right;font-size:12px;color:{{ $depMois > 0 ? '#dc2626' : '#9ca3af' }}">
                                    {{ $depMois > 0 ? '- '.number_format($depMois, 0, ',', ' ').' F' : '—' }}
                                </td>
                                <td style="text-align:right;font-weight:700;color:#16a34a">
                                    {{ number_format($netFinalLigne, 0, ',', ' ') }} F
                                </td>
                                <td style="text-align:center">
                                    <a href="{{ route('admin.paiements.show', $p) }}"
                                       style="display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:7px;border:1px solid #e5e7eb;background:#fff;color:#6b7280;text-decoration:none">
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

        {{-- COLONNE DROITE ──────────────────────── --}}
        <div>
            <div class="right-panel">
                <div class="rp-hd"><div class="rp-title">Résumé financier</div></div>
                <div class="rp-body">
                    <div class="rp-row">
                        <span class="rp-lbl">Bailleur</span>
                        <span class="rp-val" style="font-size:11px">{{ $user->name }}</span>
                    </div>
                    <div class="rp-row">
                        <span class="rp-lbl">Exercice</span>
                        <span class="rp-val">{{ $annee }}{{ $mois ? ' / '.str_pad($mois,2,'0',STR_PAD_LEFT) : '' }}</span>
                    </div>
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
                        <span class="rp-lbl">BRS retenu (fiscal)</span>
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

                    <div style="margin-top:12px">
                        <a href="{{ route('admin.bailleurs.export-pdf', [$user->id, 'annee' => $annee, 'mois' => $mois ?? now()->month]) }}"
                           target="_blank"
                           style="display:flex;align-items:center;justify-content:center;gap:6px;padding:9px;border:1px solid rgba(255,255,255,.1);border-radius:8px;color:#c9a84c;font-size:12px;font-weight:600;text-decoration:none">
                            <svg style="width:12px;height:12px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                            Télécharger Rapport PDF
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
