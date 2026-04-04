<x-app-layout>
    <x-slot name="header">Mon espace</x-slot>

<style>
/* ── LAYOUT ── */
.dash-grid { display:grid; grid-template-columns:1fr 300px; gap:20px; align-items:start; }

/* ── HERO BIEN ── */
.bien-hero {
    background:#0d1117; border-radius:16px; padding:28px 30px;
    margin-bottom:20px; position:relative; overflow:hidden;
}
.bien-hero::before {
    content:''; position:absolute; top:-50px; right:-50px;
    width:180px; height:180px; border-radius:50%;
    background:rgba(201,168,76,0.08);
}
.bien-hero::after {
    content:''; position:absolute; bottom:-30px; left:80px;
    width:120px; height:120px; border-radius:50%;
    background:rgba(201,168,76,0.05);
}
.hero-label { font-size:10px;font-weight:600;letter-spacing:1.5px;text-transform:uppercase;color:rgba(255,255,255,.4);margin-bottom:8px;position:relative;z-index:1; }
.hero-ref   { font-family:'Syne',sans-serif;font-size:24px;font-weight:800;color:#fff;letter-spacing:-.5px;line-height:1;position:relative;z-index:1; }
.hero-type  { font-size:13px;color:rgba(255,255,255,.5);margin-top:6px;position:relative;z-index:1; }
.hero-addr  { font-size:12px;color:rgba(255,255,255,.4);margin-top:3px;display:flex;align-items:center;gap:5px;position:relative;z-index:1; }
.hero-addr svg { width:12px;height:12px;flex-shrink:0; }

.hero-stats {
    display:grid; grid-template-columns:1fr 1fr 1fr;
    gap:0; margin-top:24px; padding-top:20px;
    border-top:1px solid rgba(255,255,255,.08);
    position:relative; z-index:1;
}
.hero-stat { padding:0 20px; border-right:1px solid rgba(255,255,255,.08); }
.hero-stat:first-child { padding-left:0; }
.hero-stat:last-child  { border-right:none; }
.hs-lbl { font-size:9px;font-weight:600;letter-spacing:1px;text-transform:uppercase;color:rgba(255,255,255,.35);margin-bottom:5px; }
.hs-val { font-family:'Syne',sans-serif;font-size:15px;font-weight:700;color:#fff;line-height:1; }
.hs-val.green { color:#4ade80; }
.hs-val.gold  { color:#c9a84c; }

/* ── CARDS ── */
.card { background:#fff; border:1px solid #e5e7eb; border-radius:14px; overflow:hidden; margin-bottom:18px; }
.card:last-child { margin-bottom:0; }
.card-hd { padding:15px 20px; border-bottom:1px solid #e5e7eb; display:flex; align-items:center; justify-content:space-between; }
.card-title { font-family:'Syne',sans-serif; font-size:13px; font-weight:700; color:#0d1117; }
.card-action { font-size:12px;color:#6b7280;text-decoration:none;transition:color .15s; }
.card-action:hover { color:#0d1117; }
.card-body { padding:18px 20px; }

/* ── PROCHAIN LOYER ── */
.next-loyer {
    background:#f0fdf4; border:1px solid #bbf7d0; border-radius:12px;
    padding:18px 20px; margin-bottom:18px;
    display:flex; align-items:center; justify-content:space-between;
}
.nl-left {}
.nl-label { font-size:11px;font-weight:600;color:#15803d;text-transform:uppercase;letter-spacing:.8px;margin-bottom:5px; }
.nl-periode { font-family:'Syne',sans-serif;font-size:16px;font-weight:700;color:#15803d; }
.nl-hint { font-size:11px;color:#16a34a;margin-top:3px; }
.nl-amount { font-family:'Syne',sans-serif;font-size:22px;font-weight:800;color:#15803d; }
.nl-unit { font-size:12px;color:#16a34a; }

/* ── TABLEAU PAIEMENTS ── */
.dt { width:100%;border-collapse:collapse; }
.dt th { padding:9px 16px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#9ca3af;background:#f9fafb;border-bottom:1px solid #e5e7eb;text-align:left; }
.dt td { padding:13px 16px;font-size:13px;color:#374151;border-bottom:1px solid #f3f4f6;vertical-align:middle; }
.dt tbody tr:last-child td { border-bottom:none; }
.dt tbody tr:hover { background:#f9fafb; }
.periode-pill { display:inline-block;padding:3px 9px;background:#f5e9c9;color:#8a6e2f;border-radius:6px;font-size:11px;font-weight:600;font-family:'Syne',sans-serif; }
.amt { font-family:'Syne',sans-serif;font-weight:600;color:#0d1117; }
.badge { display:inline-flex;align-items:center;gap:4px;padding:3px 9px;border-radius:99px;font-size:11px;font-weight:600; }
.badge.g { background:#dcfce7;color:#16a34a; }
.bdot { width:5px;height:5px;border-radius:50%;background:currentColor; }

/* ── INFOS CONTRAT ── */
.info-grid { display:grid;grid-template-columns:1fr 1fr;gap:12px; }
.info-item {}
.il { font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:.8px;color:#9ca3af;margin-bottom:4px; }
.iv { font-size:13px;font-weight:500;color:#0d1117; }
.iv-sub { font-size:11px;color:#6b7280;margin-top:1px; }

/* ── SIDEBAR STICKY ── */
.sidebar-sticky { position:sticky;top:80px; }

/* ── KPI MINI ── */
.kpi-mini { background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:16px 18px;margin-bottom:12px;position:relative;overflow:hidden; }
.kpi-mini::before { content:'';position:absolute;top:0;left:0;right:0;height:3px;border-radius:12px 12px 0 0; }
.kpi-mini.gold::before  { background:#c9a84c; }
.kpi-mini.green::before { background:#16a34a; }
.kpi-mini.blue::before  { background:#1d4ed8; }
.kpi-lbl { font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:.8px;color:#6b7280;margin-bottom:4px; }
.kpi-val { font-family:'Syne',sans-serif;font-size:20px;font-weight:700;color:#0d1117; }
.kpi-val.green { color:#16a34a; }
.kpi-val.gold  { color:#8a6e2f; }
.kpi-u  { font-size:11px;font-weight:400;color:#9ca3af;margin-left:2px; }
.kpi-s  { font-size:11px;color:#9ca3af;margin-top:4px; }

/* ── CONTACT AGENCE ── */
.agence-card { background:#0d1117;border-radius:12px;padding:16px 18px; }
.ag-name { font-family:'Syne',sans-serif;font-size:14px;font-weight:700;color:#fff;margin-bottom:12px; }
.ag-row { display:flex;align-items:center;gap:8px;padding:7px 0;border-bottom:1px solid rgba(255,255,255,.06);font-size:12px; }
.ag-row:last-child { border-bottom:none;padding-bottom:0; }
.ag-row svg { width:13px;height:13px;color:rgba(255,255,255,.3);flex-shrink:0; }
.ag-val { color:rgba(255,255,255,.65); }
.ag-val a { color:#c9a84c;text-decoration:none; }

/* ── VIDE ── */
.empty-hero { text-align:center;padding:48px 20px; }
.empty-icon { width:56px;height:56px;border-radius:14px;background:#f5e9c9;display:flex;align-items:center;justify-content:center;margin:0 auto 14px; }
.empty-icon svg { width:24px;height:24px;color:#8a6e2f; }
.empty-title { font-family:'Syne',sans-serif;font-size:16px;font-weight:700;color:#0d1117;margin-bottom:6px; }
.empty-sub { font-size:13px;color:#6b7280; }

/* ── DURÉE PROGRESS ── */
.duree-bar { height:6px;background:#f3f4f6;border-radius:99px;overflow:hidden;margin-top:8px; }
.duree-fill { height:100%;border-radius:99px;background:linear-gradient(90deg,#c9a84c,#f5e9c9); }
</style>

<div style="padding:24px 32px 48px">

    {{-- GREETING --}}
    <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:20px">
        <div>
            <h1 style="font-family:'Syne',sans-serif;font-size:22px;font-weight:700;color:#0d1117;letter-spacing:-.4px">
                Bonjour, {{ explode(' ', auth()->user()->name)[0] }} 👋
            </h1>
            <p style="font-size:13px;color:#6b7280;margin-top:3px">
                {{ now()->translatedFormat('l d F Y') }}
            </p>
        </div>
        <a href="{{ route('locataire.paiements') }}"
           style="display:flex;align-items:center;gap:6px;padding:9px 16px;border:1px solid #e5e7eb;border-radius:9px;font-size:12px;font-weight:500;color:#374151;text-decoration:none;background:#fff;transition:all .15s"
           onmouseover="this.style.borderColor='#c9a84c';this.style.color='#8a6e2f'"
           onmouseout="this.style.borderColor='#e5e7eb';this.style.color='#374151'">
            <svg style="width:13px;height:13px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
            Mes paiements
        </a>
    </div>

    @if(!$contrat)
    {{-- CAS SANS CONTRAT --}}
    <div class="card">
        <div class="empty-hero">
            <div class="empty-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            </div>
            <div class="empty-title">Aucun contrat actif</div>
            <div class="empty-sub">Votre contrat de bail n'est pas encore configuré. Contactez votre agence.</div>
        </div>
    </div>

    @else
    {{-- CAS AVEC CONTRAT --}}
    @php
        $bien         = $contrat->bien;
        $proprietaire = $bien->proprietaire ?? null;
        $agency       = $currentAgency ?? null;

        // Progression durée bail
        $debut  = \Carbon\Carbon::parse($contrat->date_debut);
        $fin    = $contrat->date_fin ? \Carbon\Carbon::parse($contrat->date_fin) : null;
        $today  = now();
        $dureeProgress = null;
        $joursRestants = null;
        if ($fin) {
            $total = max(1, $debut->diffInDays($fin));
            $ecoule = min($total, $debut->diffInDays($today));
            $dureeProgress = round(($ecoule / $total) * 100);
            $joursRestants = $today->diffInDays($fin, false);
        }
    @endphp

    {{-- PROCHAIN LOYER --}}
    @if($prochainePeriode)
    <div class="next-loyer">
        <div class="nl-left">
            <div class="nl-label">Prochain loyer à régler</div>
            <div class="nl-periode">{{ $prochainePeriode->translatedFormat('F Y') }}</div>
            @if($dernierPaiement)
                <div class="nl-hint">Dernier paiement : {{ \Carbon\Carbon::parse($dernierPaiement->periode)->translatedFormat('F Y') }}</div>
            @else
                <div class="nl-hint">Premier paiement du bail</div>
            @endif
        </div>
        <div style="text-align:right">
            <div class="nl-amount">{{ number_format($contrat->loyer_contractuel, 0, ',', ' ') }}</div>
            <div class="nl-unit">FCFA / mois</div>
        </div>
    </div>
    @endif

    <div class="dash-grid">

        {{-- ═══ COLONNE GAUCHE ═══ --}}
        <div>

            {{-- HERO MON LOGEMENT --}}
            <div class="bien-hero">
                <div class="hero-label">Mon logement</div>
                <div class="hero-ref">{{ $bien->reference }}</div>
                <div class="hero-type">
                    {{ \App\Models\Bien::TYPES[$bien->type] ?? $bien->type }}
                    @if($bien->meuble) · Meublé @endif
                    @if($bien->surface_m2) · {{ $bien->surface_m2 }} m² @endif
                    @if($bien->nombre_pieces) · {{ $bien->nombre_pieces }} pièces @endif
                </div>
                <div class="hero-addr">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    {{ $bien->adresse }}@if($bien->quartier), {{ $bien->quartier }}@endif, {{ $bien->ville }}
                </div>

                <div class="hero-stats">
                    <div class="hero-stat">
                        <div class="hs-lbl">Loyer mensuel</div>
                        <div class="hs-val gold">{{ number_format($contrat->loyer_contractuel, 0, ',', ' ') }}<span style="font-size:11px;color:rgba(255,255,255,.3);margin-left:2px">F</span></div>
                    </div>
                    <div class="hero-stat">
                        <div class="hs-lbl">Début du bail</div>
                        <div class="hs-val">{{ $debut->format('d/m/Y') }}</div>
                    </div>
                    <div class="hero-stat">
                        <div class="hs-lbl">Caution versée</div>
                        <div class="hs-val green">{{ number_format($contrat->caution, 0, ',', ' ') }}<span style="font-size:11px;color:rgba(74,222,128,.5);margin-left:2px">F</span></div>
                    </div>
                </div>
            </div>

            {{-- DÉTAILS DU CONTRAT --}}
            <div class="card">
                <div class="card-hd">
                    <div class="card-title">Détails du bail</div>
                    <span style="font-family:monospace;font-size:11px;color:#9ca3af">{{ $contrat->reference_bail ?? '—' }}</span>
                </div>
                <div class="card-body">
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="il">Type de bail</div>
                            <div class="iv">{{ \App\Models\Contrat::TYPES_BAIL[$contrat->type_bail] ?? ucfirst($contrat->type_bail) }}</div>
                        </div>
                        <div class="info-item">
                            <div class="il">Statut</div>
                            <div style="display:flex;align-items:center;gap:6px;margin-top:4px">
                                <div style="width:8px;height:8px;border-radius:50%;background:#16a34a"></div>
                                <div class="iv" style="color:#16a34a">Actif</div>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="il">Date de début</div>
                            <div class="iv">{{ $debut->format('d/m/Y') }}</div>
                            <div class="iv-sub">{{ $debut->diffForHumans() }}</div>
                        </div>
                        <div class="info-item">
                            <div class="il">Date de fin</div>
                            @if($fin)
                                <div class="iv {{ $joursRestants !== null && $joursRestants <= 60 ? 'iv--warn':'' }}">
                                    {{ $fin->format('d/m/Y') }}
                                </div>
                                @if($joursRestants !== null && $joursRestants > 0)
                                    <div class="iv-sub">{{ $joursRestants }} jours restants</div>
                                @elseif($joursRestants !== null && $joursRestants <= 0)
                                    <div class="iv-sub" style="color:#dc2626">Bail échu</div>
                                @endif
                            @else
                                <div class="iv">Durée indéterminée</div>
                            @endif
                        </div>

                        @if($contrat->charges_mensuelles)
                        <div class="info-item">
                            <div class="il">Charges mensuelles</div>
                            <div class="iv">{{ number_format($contrat->charges_mensuelles, 0, ',', ' ') }} F</div>
                        </div>
                        @endif

                        @if($contrat->tom_amount)
                        <div class="info-item">
                            <div class="il">TOM</div>
                            <div class="iv">{{ number_format($contrat->tom_amount, 0, ',', ' ') }} F</div>
                        </div>
                        @endif
                    </div>

                    {{-- Barre de progression durée --}}
                    @if($fin && $dureeProgress !== null)
                    <div style="margin-top:16px;padding-top:14px;border-top:1px solid #f3f4f6">
                        <div style="display:flex;justify-content:space-between;margin-bottom:6px">
                            <span style="font-size:11px;color:#6b7280">Progression du bail</span>
                            <span style="font-size:11px;font-weight:600;color:#8a6e2f">{{ $dureeProgress }}%</span>
                        </div>
                        <div class="duree-bar">
                            <div class="duree-fill" style="width:{{ $dureeProgress }}%"></div>
                        </div>
                        <div style="display:flex;justify-content:space-between;margin-top:5px">
                            <span style="font-size:10px;color:#9ca3af">{{ $debut->format('d/m/Y') }}</span>
                            <span style="font-size:10px;color:#9ca3af">{{ $fin->format('d/m/Y') }}</span>
                        </div>
                    </div>
                    @endif

                    {{-- Garant --}}
                    @if($contrat->garant_nom)
                    <div style="margin-top:16px;padding:12px 14px;background:#f9fafb;border-radius:9px;border-left:3px solid #e5e7eb">
                        <div style="font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:.8px;color:#9ca3af;margin-bottom:6px">Garant</div>
                        <div style="font-size:13px;font-weight:500;color:#0d1117">{{ $contrat->garant_nom }}</div>
                        @if($contrat->garant_telephone)
                            <div style="font-size:11px;color:#6b7280;margin-top:2px">{{ $contrat->garant_telephone }}</div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            {{-- HISTORIQUE PAIEMENTS --}}
            <div class="card">
                <div class="card-hd">
                    <div class="card-title">Mes derniers paiements</div>
                    <a href="{{ route('locataire.paiements') }}" class="card-action">Voir tout →</a>
                </div>

                @if($paiements->isEmpty())
                <div style="padding:32px;text-align:center">
                    <div style="font-size:13px;color:#9ca3af">Aucun paiement enregistré pour l'instant.</div>
                </div>
                @else
                <div style="overflow-x:auto">
                    <table class="dt">
                        <thead>
                            <tr>
                                <th>Référence</th>
                                <th>Période</th>
                                <th>Date</th>
                                <th>Mode</th>
                                <th style="text-align:right">Montant</th>
                                <th style="text-align:center">Quittance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($paiements->take(6) as $p)
                            <tr>
                                <td style="font-family:'Syne',sans-serif;font-size:11px;color:#9ca3af">{{ $p->reference_paiement }}</td>
                                <td><span class="periode-pill">{{ \Carbon\Carbon::parse($p->periode)->translatedFormat('M Y') }}</span></td>
                                <td style="font-size:12px;color:#6b7280">{{ \Carbon\Carbon::parse($p->date_paiement)->format('d/m/Y') }}</td>
                                <td style="font-size:12px;color:#6b7280">{{ \App\Models\Paiement::MODES_PAIEMENT[$p->mode_paiement] ?? $p->mode_paiement }}</td>
                                <td style="text-align:right"><span class="amt">{{ number_format($p->montant_encaisse, 0, ',', ' ') }} F</span></td>
                                <td style="text-align:center">
                                    <a href="{{ route('locataire.paiements.pdf', $p) }}" target="_blank"
                                       style="display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border:1px solid #e5e7eb;border-radius:7px;color:#6b7280;text-decoration:none;transition:all .15s"
                                       onmouseover="this.style.borderColor='#c9a84c';this.style.color='#8a6e2f';this.style.background='#f5e9c9'"
                                       onmouseout="this.style.borderColor='#e5e7eb';this.style.color='#6b7280';this.style.background='transparent'"
                                       title="Télécharger la quittance">
                                        <svg style="width:13px;height:13px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>

        </div>{{-- fin colonne gauche --}}

        {{-- ═══ COLONNE DROITE ═══ --}}
        <div class="sidebar-sticky">

            {{-- KPI TOTAL PAYÉ --}}
            <div class="kpi-mini gold">
                <div class="kpi-lbl">Total payé</div>
                <div class="kpi-val gold">{{ number_format($stats['total_paye'], 0, ',', ' ') }}<span class="kpi-u">F</span></div>
                <div class="kpi-s">Depuis le début du bail</div>
            </div>

            <div class="kpi-mini green">
                <div class="kpi-lbl">Paiements effectués</div>
                <div class="kpi-val green">{{ $stats['nb_paiements'] }}</div>
                <div class="kpi-s">Quittances disponibles</div>
            </div>

            @if($fin && $joursRestants !== null && $joursRestants > 0)
            <div class="kpi-mini blue">
                <div class="kpi-lbl">Jours restants</div>
                <div class="kpi-val" style="color:#1d4ed8">{{ $joursRestants }}</div>
                <div class="kpi-s">Fin le {{ $fin->format('d/m/Y') }}</div>
            </div>
            @endif

            {{-- PROPRIÉTAIRE --}}
            @if($proprietaire)
            <div class="card">
                <div class="card-hd"><div class="card-title">Propriétaire</div></div>
                <div class="card-body" style="padding:14px 18px">
                    <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px">
                        <div style="width:38px;height:38px;border-radius:10px;background:#f5e9c9;display:flex;align-items:center;justify-content:center;font-family:'Syne',sans-serif;font-size:14px;font-weight:800;color:#8a6e2f;flex-shrink:0">
                            {{ strtoupper(substr($proprietaire->name, 0, 2)) }}
                        </div>
                        <div>
                            <div style="font-size:13px;font-weight:600;color:#0d1117">{{ $proprietaire->name }}</div>
                            @if($proprietaire->telephone)
                            <a href="tel:{{ $proprietaire->telephone }}" style="font-size:11px;color:#6b7280;text-decoration:none;display:flex;align-items:center;gap:3px;margin-top:2px">
                                <svg style="width:10px;height:10px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 9.81a19.79 19.79 0 01-3.07-8.68A2 2 0 012 .5h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.09 8.32a16 16 0 006.29 6.29l1.18-1.18a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 15.92z"/></svg>
                                {{ $proprietaire->telephone }}
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- CONTACT AGENCE --}}
            @if($agency)
            <div class="card">
                <div class="card-hd"><div class="card-title">Votre agence</div></div>
                <div class="card-body" style="padding:14px 18px">
                    <div class="agence-card">
                        <div class="ag-name">{{ $agency->name }}</div>
                        @if($agency->telephone)
                        <div class="ag-row">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 9.81"/></svg>
                            <div class="ag-val"><a href="tel:{{ $agency->telephone }}">{{ $agency->telephone }}</a></div>
                        </div>
                        @endif
                        @if($agency->email)
                        <div class="ag-row">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                            <div class="ag-val"><a href="mailto:{{ $agency->email }}">{{ $agency->email }}</a></div>
                        </div>
                        @endif
                        @if($agency->adresse)
                        <div class="ag-row">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                            <div class="ag-val">{{ $agency->adresse }}</div>
                        </div>
                        @endif
                    </div>

                    {{-- WhatsApp agence --}}
                    @if($agency->telephone)
                    @php
                        $tel = preg_replace('/\s+|-/', '', $agency->telephone);
                        if (!str_starts_with($tel, '+') && !str_starts_with($tel, '221')) $tel = '221' . ltrim($tel, '0');
                        $tel = ltrim($tel, '+');
                        $msgWa = "Bonjour {$agency->name}, je suis {$contrat->locataire->name}, locataire du bien {$bien->reference}. Je souhaite vous contacter.";
                    @endphp
                    <a href="https://wa.me/{{ $tel }}?text={{ urlencode($msgWa) }}" target="_blank"
                       style="display:flex;align-items:center;justify-content:center;gap:7px;margin-top:12px;padding:10px;background:#dcfce7;border:1px solid #bbf7d0;color:#15803d;border-radius:9px;font-size:12px;font-weight:600;text-decoration:none;transition:background .15s"
                       onmouseover="this.style.background='#bbf7d0'"
                       onmouseout="this.style.background='#dcfce7'">
                        <svg style="width:15px;height:15px" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                            <path d="M12 0C5.373 0 0 5.373 0 12c0 2.124.558 4.118 1.532 5.847L.057 23.492a.5.5 0 00.614.65l5.82-1.527A11.945 11.945 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22a9.944 9.944 0 01-5.091-1.396l-.361-.216-3.754.984.999-3.648-.237-.374A9.944 9.944 0 012 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/>
                        </svg>
                        Contacter par WhatsApp
                    </a>
                    @endif
                </div>
            </div>
            @endif

        </div>{{-- fin colonne droite --}}

    </div>{{-- /dash-grid --}}
    @endif

</div>

</x-app-layout>