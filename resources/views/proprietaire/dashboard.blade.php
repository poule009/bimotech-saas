<x-app-layout>
    <x-slot name="header">Mon espace propriétaire</x-slot>

<style>
/* ── LAYOUT ── */
.dash-grid { display:grid; grid-template-columns:1fr 300px; gap:20px; align-items:start; }

/* ── KPI GRID ── */
.kpi-row { display:grid; grid-template-columns:repeat(4,1fr); gap:14px; margin-bottom:22px; }
.kpi-card { background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:18px 20px; position:relative; overflow:hidden; transition:transform .2s,box-shadow .2s; }
.kpi-card:hover { transform:translateY(-2px); box-shadow:0 8px 24px -6px rgba(0,0,0,0.08); }
.kpi-card::before { content:''; position:absolute; top:0;left:0;right:0; height:3px; border-radius:12px 12px 0 0; }
.kpi-card.gold::before   { background:#c9a84c; }
.kpi-card.green::before  { background:#16a34a; }
.kpi-card.blue::before   { background:#1d4ed8; }
.kpi-card.purple::before { background:#7c3aed; }
.kpi-lbl { font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:.8px;color:#6b7280;margin-bottom:5px; }
.kpi-val { font-family:'Syne',sans-serif;font-size:20px;font-weight:700;color:#0d1117;letter-spacing:-.3px;line-height:1; }
.kpi-val.green { color:#16a34a; }
.kpi-val.gold  { color:#8a6e2f; }
.kpi-u  { font-size:11px;font-weight:400;color:#9ca3af;margin-left:2px; }
.kpi-s  { font-size:11px;color:#9ca3af;margin-top:5px; }

/* ── BILAN DARK ── */
.bilan { background:#0d1117;border-radius:16px;padding:26px 30px;margin-bottom:22px;display:grid;grid-template-columns:1fr 1fr 1fr;position:relative;overflow:hidden; }
.bilan::before { content:'';position:absolute;top:-60px;right:-60px;width:200px;height:200px;border-radius:50%;background:rgba(201,168,76,0.07); }
.bilan-col { padding:0 26px;border-right:1px solid rgba(255,255,255,.07);position:relative;z-index:1; }
.bilan-col:first-child { padding-left:0; }
.bilan-col:last-child  { padding-right:0;border-right:none; }
.bilan-lbl { font-size:10px;font-weight:600;letter-spacing:1.5px;text-transform:uppercase;color:rgba(255,255,255,.4);margin-bottom:8px; }
.bilan-val { font-family:'Syne',sans-serif;font-size:24px;font-weight:700;letter-spacing:-.5px;line-height:1;color:white; }
.bilan-val.green { color:#4ade80; }
.bilan-val.gold  { color:#c9a84c; }
.bilan-u { font-size:12px;color:rgba(255,255,255,.3);margin-left:3px; }
.bilan-s { font-size:11px;color:rgba(255,255,255,.3);margin-top:6px; }

/* ── CARDS ── */
.card { background:#fff; border:1px solid #e5e7eb; border-radius:14px; overflow:hidden; margin-bottom:18px; }
.card:last-child { margin-bottom:0; }
.card-hd { padding:15px 20px; border-bottom:1px solid #e5e7eb; display:flex; align-items:center; justify-content:space-between; }
.card-title { font-family:'Syne',sans-serif; font-size:13px; font-weight:700; color:#0d1117; }
.card-action { font-size:12px;color:#6b7280;text-decoration:none;transition:color .15s;display:flex;align-items:center;gap:4px; }
.card-action:hover { color:#0d1117; }

/* ── BIENS GRID ── */
.biens-grid { display:grid;grid-template-columns:1fr 1fr;gap:12px;padding:16px; }
.bien-card { border:1px solid #e5e7eb;border-radius:10px;overflow:hidden;transition:transform .15s,box-shadow .15s; }
.bien-card:hover { transform:translateY(-2px);box-shadow:0 6px 20px -4px rgba(0,0,0,0.08); }
.bien-photo { height:100px;background:#f9fafb;position:relative;overflow:hidden; }
.bien-photo img { width:100%;height:100%;object-fit:cover; }
.bien-photo-ph { width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,#f5e9c9 0%,#f9fafb 100%); }
.bien-photo-ph svg { width:28px;height:28px;color:#c9a84c;opacity:.5; }
.statut-pill { position:absolute;top:8px;left:8px;padding:3px 9px;border-radius:99px;font-size:10px;font-weight:600; }
.statut-pill.loue  { background:rgba(22,163,74,.15);color:#16a34a;border:1px solid rgba(22,163,74,.2); }
.statut-pill.dispo { background:rgba(29,78,216,.15);color:#1d4ed8;border:1px solid rgba(29,78,216,.2); }
.statut-pill.trav  { background:rgba(201,168,76,.15);color:#8a6e2f;border:1px solid rgba(201,168,76,.2); }
.bien-body { padding:12px 14px; }
.bien-ref  { font-family:'Syne',sans-serif;font-size:12px;font-weight:600;color:#0d1117; }
.bien-addr { font-size:11px;color:#6b7280;margin-top:2px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap; }
.bien-loyer { font-family:'Syne',sans-serif;font-size:13px;font-weight:700;color:#8a6e2f;margin-top:8px; }

/* ── TABLE ── */
.dt { width:100%;border-collapse:collapse; }
.dt th { padding:9px 16px;text-align:left;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#9ca3af;background:#f9fafb;border-bottom:1px solid #e5e7eb; }
.dt td { padding:12px 16px;font-size:13px;color:#374151;border-bottom:1px solid #f3f4f6;vertical-align:middle; }
.dt tbody tr:last-child td { border-bottom:none; }
.dt tbody tr:hover { background:#f9fafb; }
.periode-pill { display:inline-block;padding:3px 9px;background:#f5e9c9;color:#8a6e2f;border-radius:6px;font-size:11px;font-weight:600;font-family:'Syne',sans-serif; }
.amt-green { font-family:'Syne',sans-serif;font-weight:600;color:#16a34a; }
.badge { display:inline-flex;align-items:center;gap:4px;padding:3px 9px;border-radius:99px;font-size:11px;font-weight:600; }
.badge.g { background:#dcfce7;color:#16a34a; }
.bdot { width:5px;height:5px;border-radius:50%;background:currentColor; }

/* ── SIDEBAR ── */
.sidebar-sticky { position:sticky;top:80px; }
.kpi-mini { background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:15px 17px;margin-bottom:12px;position:relative;overflow:hidden; }
.kpi-mini::before { content:'';position:absolute;top:0;left:0;right:0;height:3px;border-radius:12px 12px 0 0; }
.kpi-mini.gold::before  { background:#c9a84c; }
.kpi-mini.green::before { background:#16a34a; }
.kpi-mini.blue::before  { background:#1d4ed8; }
.km-lbl { font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:.8px;color:#6b7280;margin-bottom:4px; }
.km-val { font-family:'Syne',sans-serif;font-size:18px;font-weight:700;color:#0d1117; }
.km-val.green { color:#16a34a; }
.km-val.gold  { color:#8a6e2f; }
.km-u   { font-size:11px;font-weight:400;color:#9ca3af;margin-left:2px; }
.km-s   { font-size:11px;color:#9ca3af;margin-top:4px; }

/* occupation ring */
.occ-ring-wrap { display:flex;flex-direction:column;align-items:center;padding:16px;gap:8px; }
.occ-ring { width:80px;height:80px;position:relative; }
.occ-ring svg { width:80px;height:80px;transform:rotate(-90deg); }
.occ-ring-bg  { fill:none;stroke:#f3f4f6;stroke-width:8; }
.occ-ring-fill { fill:none;stroke-width:8;stroke-linecap:round;transition:stroke-dasharray .8s ease; }
.occ-ring-text { position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center; }
.occ-ring-pct { font-family:'Syne',sans-serif;font-size:16px;font-weight:800;color:#0d1117;line-height:1; }
.occ-ring-sub { font-size:9px;color:#6b7280;margin-top:2px; }
.occ-label { font-size:12px;font-weight:500;color:#374151;text-align:center; }
.occ-sub { font-size:11px;color:#6b7280; }

/* agence contact */
.agence-card { background:#0d1117;border-radius:12px;padding:14px 16px; }
.ag-name { font-family:'Syne',sans-serif;font-size:13px;font-weight:700;color:#fff;margin-bottom:10px; }
.ag-row { display:flex;align-items:center;gap:8px;padding:6px 0;border-bottom:1px solid rgba(255,255,255,.06);font-size:11px;color:rgba(255,255,255,.6); }
.ag-row:last-child { border-bottom:none; }
.ag-row svg { width:12px;height:12px;color:rgba(255,255,255,.3);flex-shrink:0; }
.ag-row a { color:#c9a84c;text-decoration:none; }

/* pagination custom */
.pagination-wrap { padding:12px 16px;border-top:1px solid #e5e7eb;display:flex;align-items:center;justify-content:space-between; }
.pagination-info { font-size:12px;color:#6b7280; }
.pagination-links { display:flex;gap:4px; }
.page-btn { display:inline-flex;align-items:center;justify-content:center;min-width:30px;height:30px;padding:0 8px;border-radius:7px;border:1px solid #e5e7eb;background:#fff;color:#374151;font-size:12px;font-weight:500;text-decoration:none;transition:all .15s; }
.page-btn.active { background:#0d1117;color:#fff;border-color:#0d1117; }
.page-btn.disabled { opacity:.4;pointer-events:none; }
</style>

<div style="padding:24px 32px 48px">

    {{-- GREETING --}}
    <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:22px">
        <div>
            <h1 style="font-family:'Syne',sans-serif;font-size:22px;font-weight:700;color:#0d1117;letter-spacing:-.4px">
                Bonjour, {{ explode(' ', auth()->user()->name)[0] }} 👋
            </h1>
            <p style="font-size:13px;color:#6b7280;margin-top:3px">
                {{ now()->translatedFormat('l d F Y') }} · Aperçu de votre patrimoine immobilier
            </p>
        </div>
        <a href="{{ route('admin.biens.index') }}"
           style="display:flex;align-items:center;gap:6px;padding:9px 16px;border:1px solid #e5e7eb;border-radius:9px;font-size:12px;font-weight:500;color:#374151;text-decoration:none;background:#fff;transition:all .15s"
           onmouseover="this.style.borderColor='#c9a84c';this.style.color='#8a6e2f'"
           onmouseout="this.style.borderColor='#e5e7eb';this.style.color='#374151'">
            <svg style="width:13px;height:13px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
            Mes biens
        </a>
    </div>

    {{-- KPI ROW --}}
    @php
        $tauxOccupation = $stats['nb_biens'] > 0
            ? round(($stats['nb_biens_loues'] / $stats['nb_biens']) * 100)
            : 0;
    @endphp
    <div class="kpi-row">
        <div class="kpi-card gold">
            <div class="kpi-lbl">Total loyers encaissés</div>
            <div class="kpi-val gold">{{ number_format($stats['total_loyers'], 0, ',', ' ') }}<span class="kpi-u">F</span></div>
            <div class="kpi-s">{{ $stats['nb_paiements'] }} paiements validés</div>
        </div>
        <div class="kpi-card green">
            <div class="kpi-val green" style="font-size:26px;margin-bottom:5px">{{ number_format($stats['total_net'], 0, ',', ' ') }}<span class="kpi-u">F</span></div>
            <div class="kpi-lbl">Net reversé (après commission)</div>
            <div class="kpi-s">Votre revenu net total</div>
        </div>
        <div class="kpi-card blue">
            <div class="kpi-lbl">Biens loués</div>
            <div class="kpi-val" style="color:#1d4ed8">{{ $stats['nb_biens_loues'] }}<span class="kpi-u">/ {{ $stats['nb_biens'] }}</span></div>
            <div class="kpi-s">{{ $tauxOccupation }}% d'occupation</div>
        </div>
        <div class="kpi-card purple">
            <div class="kpi-lbl">Caution totale détenue</div>
            <div class="kpi-val" style="color:#7c3aed">{{ number_format($stats['caution'], 0, ',', ' ') }}<span class="kpi-u">F</span></div>
            <div class="kpi-s">Dépôts de garantie</div>
        </div>
    </div>

    {{-- BILAN ALL-TIME --}}
    <div class="bilan">
        <div class="bilan-col">
            <div class="bilan-lbl">Total encaissé brut</div>
            <div class="bilan-val gold">{{ number_format($stats['total_loyers'], 0, ',', ' ') }}<span class="bilan-u">F</span></div>
            <div class="bilan-s">Depuis le début</div>
        </div>
        <div class="bilan-col">
            <div class="bilan-lbl">Commission agence (TTC)</div>
            <div class="bilan-val">{{ number_format($stats['total_commission'], 0, ',', ' ') }}<span class="bilan-u">F</span></div>
            <div class="bilan-s">Déduites automatiquement</div>
        </div>
        <div class="bilan-col">
            <div class="bilan-lbl">Net reversé propriétaire</div>
            <div class="bilan-val green">{{ number_format($stats['total_net'], 0, ',', ' ') }}<span class="bilan-u">F</span></div>
            <div class="bilan-s">Votre revenu net cumulé</div>
        </div>
    </div>

    <div class="dash-grid">

        {{-- ═══ COLONNE GAUCHE ═══ --}}
        <div>

            {{-- MES BIENS --}}
            <div class="card">
                <div class="card-hd">
                    <div class="card-title">Mes biens ({{ $biens->total() }})</div>
                    <a href="{{ route('admin.biens.index') }}" class="card-action">
                        Voir tout
                        <svg style="width:12px;height:12px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                    </a>
                </div>

                @if($biens->isEmpty())
                <div style="padding:32px;text-align:center;color:#9ca3af;font-size:13px">
                    Aucun bien associé à votre compte.
                </div>
                @else
                <div class="biens-grid">
                    @foreach($biens as $bien)
                    @php
                        $sp = match($bien->statut) { 'loue'=>'loue', 'disponible'=>'dispo', default=>'trav' };
                        $sl = match($bien->statut) { 'loue'=>'Loué', 'disponible'=>'Disponible', default=>'En travaux' };
                    @endphp
                    <a href="{{ route('admin.biens.show', $bien) }}" style="text-decoration:none" class="bien-card">
                        <div class="bien-photo">
                            @php $photo = $bien->photos?->firstWhere('est_principale', true) ?? $bien->photos?->first(); @endphp
                            @if($photo)
                                <img src="{{ Storage::url($photo->chemin) }}" alt="{{ $bien->reference }}" loading="lazy">
                            @else
                                <div class="bien-photo-ph">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
                                </div>
                            @endif
                            <span class="statut-pill {{ $sp }}">{{ $sl }}</span>
                        </div>
                        <div class="bien-body">
                            <div class="bien-ref">{{ $bien->reference }}</div>
                            <div class="bien-addr">{{ $bien->adresse }}, {{ $bien->ville }}</div>
                            <div class="bien-loyer">{{ number_format($bien->loyer_mensuel, 0, ',', ' ') }} F/mois</div>
                        </div>
                    </a>
                    @endforeach
                </div>

                {{-- Pagination biens --}}
                @if($biens->hasPages())
                <div class="pagination-wrap">
                    <div class="pagination-info">{{ $biens->firstItem() }} – {{ $biens->lastItem() }} sur {{ $biens->total() }}</div>
                    <div class="pagination-links">
                        @if($biens->onFirstPage())
                            <span class="page-btn disabled"><svg style="width:12px;height:12px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg></span>
                        @else
                            <a href="{{ $biens->previousPageUrl() }}" class="page-btn"><svg style="width:12px;height:12px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg></a>
                        @endif
                        @if($biens->hasMorePages())
                            <a href="{{ $biens->nextPageUrl() }}" class="page-btn"><svg style="width:12px;height:12px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg></a>
                        @else
                            <span class="page-btn disabled"><svg style="width:12px;height:12px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg></span>
                        @endif
                    </div>
                </div>
                @endif
                @endif
            </div>

            {{-- DERNIERS PAIEMENTS --}}
            <div class="card">
                <div class="card-hd">
                    <div class="card-title">Derniers versements</div>
                    <span style="font-size:11px;color:#9ca3af">Net reversé après commission</span>
                </div>

                @if($paiements->isEmpty())
                <div style="padding:28px;text-align:center;color:#9ca3af;font-size:13px">
                    Aucun paiement enregistré pour l'instant.
                </div>
                @else
                <div style="overflow-x:auto">
                    <table class="dt">
                        <thead>
                            <tr>
                                <th>Bien</th>
                                <th>Locataire</th>
                                <th>Période</th>
                                <th>Date</th>
                                <th style="text-align:right">Loyer brut</th>
                                <th style="text-align:right">Net reversé</th>
                                <th style="text-align:center">Quittance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($paiements as $p)
                            <tr>
                                <td>
                                    <div style="font-size:13px;font-weight:500;color:#0d1117">{{ $p->contrat?->bien?->reference ?? '—' }}</div>
                                </td>
                                <td style="font-size:12px;color:#6b7280">{{ $p->contrat?->locataire?->name ?? '—' }}</td>
                                <td><span class="periode-pill">{{ \Carbon\Carbon::parse($p->periode)->translatedFormat('M Y') }}</span></td>
                                <td style="font-size:12px;color:#6b7280">{{ \Carbon\Carbon::parse($p->date_paiement)->format('d/m/Y') }}</td>
                                <td style="text-align:right;font-family:'Syne',sans-serif;font-weight:600;color:#374151">{{ number_format($p->montant_encaisse, 0, ',', ' ') }} F</td>
                                <td style="text-align:right"><span class="amt-green">{{ number_format($p->net_proprietaire, 0, ',', ' ') }} F</span></td>
                                <td style="text-align:center">
                                    <a href="{{ route('proprietaire.paiements.pdf', $p) }}" target="_blank"
                                       style="display:inline-flex;align-items:center;justify-content:center;width:27px;height:27px;border:1px solid #e5e7eb;border-radius:6px;color:#6b7280;text-decoration:none;transition:all .15s"
                                       onmouseover="this.style.borderColor='#c9a84c';this.style.color='#8a6e2f';this.style.background='#f5e9c9'"
                                       onmouseout="this.style.borderColor='#e5e7eb';this.style.color='#6b7280';this.style.background='transparent'"
                                       title="Télécharger la quittance">
                                        <svg style="width:12px;height:12px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination paiements --}}
                @if($paiements->hasPages())
                <div class="pagination-wrap">
                    <div class="pagination-info">Page {{ $paiements->currentPage() }} / {{ $paiements->lastPage() }}</div>
                    <div class="pagination-links">
                        @if($paiements->onFirstPage())
                            <span class="page-btn disabled"><svg style="width:12px;height:12px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg></span>
                        @else
                            <a href="{{ $paiements->previousPageUrl() }}" class="page-btn"><svg style="width:12px;height:12px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg></a>
                        @endif
                        @foreach($paiements->getUrlRange(max(1,$paiements->currentPage()-1), min($paiements->lastPage(),$paiements->currentPage()+1)) as $pg => $url)
                            <a href="{{ $url }}" class="page-btn {{ $pg === $paiements->currentPage() ? 'active':'' }}">{{ $pg }}</a>
                        @endforeach
                        @if($paiements->hasMorePages())
                            <a href="{{ $paiements->nextPageUrl() }}" class="page-btn"><svg style="width:12px;height:12px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg></a>
                        @else
                            <span class="page-btn disabled"><svg style="width:12px;height:12px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg></span>
                        @endif
                    </div>
                </div>
                @endif
                @endif
            </div>

        </div>{{-- fin colonne gauche --}}

        {{-- ═══ COLONNE DROITE ═══ --}}
        <div class="sidebar-sticky">

            {{-- ANNEAU OCCUPATION --}}
            <div class="card" style="margin-bottom:12px">
                <div class="card-hd"><div class="card-title">Taux d'occupation</div></div>
                <div class="occ-ring-wrap">
                    @php
                        $circumference = 2 * M_PI * 32; // r=32
                        $dash = ($tauxOccupation / 100) * $circumference;
                        $ringColor = $tauxOccupation >= 80 ? '#16a34a' : ($tauxOccupation >= 50 ? '#d97706' : '#dc2626');
                    @endphp
                    <div class="occ-ring">
                        <svg viewBox="0 0 80 80">
                            <circle class="occ-ring-bg" cx="40" cy="40" r="32"/>
                            <circle class="occ-ring-fill" cx="40" cy="40" r="32"
                                stroke="{{ $ringColor }}"
                                stroke-dasharray="{{ $dash }} {{ $circumference }}"
                                stroke-dashoffset="0"/>
                        </svg>
                        <div class="occ-ring-text">
                            <div class="occ-ring-pct" style="color:{{ $ringColor }}">{{ $tauxOccupation }}%</div>
                            <div class="occ-ring-sub">occupé</div>
                        </div>
                    </div>
                    <div class="occ-label">{{ $stats['nb_biens_loues'] }} loué(s) sur {{ $stats['nb_biens'] }} bien(s)</div>
                    <div class="occ-sub">
                        @if($stats['nb_biens'] - $stats['nb_biens_loues'] > 0)
                            {{ $stats['nb_biens'] - $stats['nb_biens_loues'] }} bien(s) disponible(s)
                        @else
                            Parc immobilier pleinement loué ✓
                        @endif
                    </div>
                </div>
            </div>

            {{-- KPIs sidebar --}}
            <div class="kpi-mini green">
                <div class="km-lbl">Net reversé (all time)</div>
                <div class="km-val green">{{ number_format($stats['total_net'], 0, ',', ' ') }}<span class="km-u">F</span></div>
                <div class="km-s">Votre revenu net cumulé</div>
            </div>

            <div class="kpi-mini gold">
                <div class="km-lbl">Caution totale</div>
                <div class="km-val gold">{{ number_format($stats['caution'], 0, ',', ' ') }}<span class="km-u">F</span></div>
                <div class="km-s">Dépôts de garantie détenus</div>
            </div>

            @if($stats['dernier_paiement'])
            <div class="kpi-mini blue">
                <div class="km-lbl">Dernier versement</div>
                <div class="km-val" style="color:#1d4ed8;font-size:15px">
                    {{ number_format($stats['dernier_paiement']->net_proprietaire, 0, ',', ' ') }}<span class="km-u">F</span>
                </div>
                <div class="km-s">{{ \Carbon\Carbon::parse($stats['dernier_paiement']->date_paiement)->format('d/m/Y') }}</div>
            </div>
            @endif

            {{-- CONTACT AGENCE --}}
            @if($currentAgency)
            <div class="card">
                <div class="card-hd"><div class="card-title">Votre agence</div></div>
                <div class="card-body" style="padding:14px 18px">
                    <div class="agence-card">
                        <div class="ag-name">{{ $currentAgency->name }}</div>
                        @if($currentAgency->telephone)
                        <div class="ag-row">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07"/></svg>
                            <a href="tel:{{ $currentAgency->telephone }}">{{ $currentAgency->telephone }}</a>
                        </div>
                        @endif
                        @if($currentAgency->email)
                        <div class="ag-row">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                            <a href="mailto:{{ $currentAgency->email }}">{{ $currentAgency->email }}</a>
                        </div>
                        @endif
                        @if($currentAgency->adresse)
                        <div class="ag-row">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/></svg>
                            {{ $currentAgency->adresse }}
                        </div>
                        @endif
                    </div>

                    {{-- WhatsApp agence --}}
                    @if($currentAgency->telephone)
                    @php
                        $tel = preg_replace('/\s+|-/', '', $currentAgency->telephone);
                        if (!str_starts_with($tel, '+') && !str_starts_with($tel, '221')) $tel = '221' . ltrim($tel, '0');
                        $tel = ltrim($tel, '+');
                        $msgWa = "Bonjour {$currentAgency->name}, je suis ".auth()->user()->name.", propriétaire géré par votre agence. Je souhaite vous contacter.";
                    @endphp
                    <a href="https://wa.me/{{ $tel }}?text={{ urlencode($msgWa) }}" target="_blank"
                       style="display:flex;align-items:center;justify-content:center;gap:6px;margin-top:12px;padding:9px;background:#dcfce7;border:1px solid #bbf7d0;color:#15803d;border-radius:9px;font-size:12px;font-weight:600;text-decoration:none;transition:background .15s"
                       onmouseover="this.style.background='#bbf7d0'"
                       onmouseout="this.style.background='#dcfce7'">
                        <svg style="width:14px;height:14px" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                            <path d="M12 0C5.373 0 0 5.373 0 12c0 2.124.558 4.118 1.532 5.847L.057 23.492a.5.5 0 00.614.65l5.82-1.527A11.945 11.945 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22a9.944 9.944 0 01-5.091-1.396l-.361-.216-3.754.984.999-3.648-.237-.374A9.944 9.944 0 012 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/>
                        </svg>
                        Contacter par WhatsApp
                    </a>
                    @endif
                </div>
            </div>
            @endif

        </div>{{-- fin sidebar --}}

    </div>{{-- /dash-grid --}}

</div>

</x-app-layout>