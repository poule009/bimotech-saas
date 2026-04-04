<x-app-layout>
    <x-slot name="header">Détail contrat</x-slot>

<style>
/* ── LAYOUT ── */
.page-grid { display:grid; grid-template-columns:1fr 300px; gap:20px; align-items:start; }

/* ── CARD ── */
.card { background:#fff; border:1px solid #e5e7eb; border-radius:14px; overflow:hidden; margin-bottom:18px; }
.card:last-child { margin-bottom:0; }
.card-hd { padding:15px 20px; border-bottom:1px solid #e5e7eb; display:flex; align-items:center; justify-content:space-between; }
.card-title { font-family:'Syne',sans-serif; font-size:13px; font-weight:700; color:#0d1117; }
.card-action { font-size:12px; color:#6b7280; text-decoration:none; transition:color .15s; }
.card-action:hover { color:#0d1117; }
.card-body { padding:18px 20px; }

/* ── HERO ── */
.contrat-hero {
    background:#0d1117; border-radius:16px; padding:28px 30px;
    margin-bottom:18px; position:relative; overflow:hidden;
}
.contrat-hero::before { content:''; position:absolute; top:-60px;right:-60px; width:200px;height:200px;border-radius:50%;background:rgba(201,168,76,0.07); }

.hero-ref { font-family:'Syne',sans-serif;font-size:11px;font-weight:600;letter-spacing:1.5px;text-transform:uppercase;color:rgba(255,255,255,.4);margin-bottom:8px;position:relative;z-index:1; }
.hero-title { font-family:'Syne',sans-serif;font-size:22px;font-weight:800;color:#fff;letter-spacing:-.5px;line-height:1;position:relative;z-index:1; }
.hero-sub { font-size:13px;color:rgba(255,255,255,.5);margin-top:5px;position:relative;z-index:1; }

.hero-bottom { display:grid;grid-template-columns:1fr 1fr 1fr 1fr;gap:0;margin-top:24px;padding-top:20px;border-top:1px solid rgba(255,255,255,.08);position:relative;z-index:1; }
.hero-stat { padding:0 20px;border-right:1px solid rgba(255,255,255,.08); }
.hero-stat:first-child { padding-left:0; }
.hero-stat:last-child  { border-right:none; }
.hs-lbl { font-size:9px;font-weight:600;letter-spacing:1px;text-transform:uppercase;color:rgba(255,255,255,.35);margin-bottom:5px; }
.hs-val { font-family:'Syne',sans-serif;font-size:15px;font-weight:700;color:#fff; }
.hs-val.green { color:#4ade80; }
.hs-val.gold  { color:#c9a84c; }
.hs-val.red   { color:#f87171; }

/* status badge hero */
.hero-status { position:absolute;top:20px;right:20px;z-index:2;display:inline-flex;align-items:center;gap:6px;padding:5px 13px;border-radius:99px;font-size:12px;font-weight:600; }
.hero-status.actif   { background:rgba(74,222,128,.12);color:#4ade80;border:1px solid rgba(74,222,128,.2); }
.hero-status.resilié { background:rgba(248,113,113,.12);color:#f87171;border:1px solid rgba(248,113,113,.2); }
.hero-status.expiré  { background:rgba(156,163,175,.12);color:#d1d5db;border:1px solid rgba(156,163,175,.2); }
.status-dot { width:6px;height:6px;border-radius:50%;background:currentColor; }

/* ── PARTIES ── */
.parties-grid { display:grid;grid-template-columns:1fr 1fr;gap:14px; }
.party-card { background:#f9fafb;border-radius:10px;padding:14px 16px; }
.party-role { font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:#9ca3af;margin-bottom:8px; }
.party-name { font-size:14px;font-weight:700;color:#0d1117;margin-bottom:4px; }
.party-info { font-size:12px;color:#6b7280;display:flex;align-items:center;gap:5px;margin-top:3px; }
.party-info svg { width:11px;height:11px;flex-shrink:0; }
.party-info a { color:#6b7280;text-decoration:none; }
.party-info a:hover { color:#c9a84c; }

/* ── DÉCOMPTE ── */
.decompte-table { width:100%;border-collapse:collapse; }
.decompte-table tr { border-bottom:1px solid #f3f4f6; }
.decompte-table tr:last-child { border-bottom:none; }
.decompte-table td { padding:10px 0;font-size:13px;color:#374151;vertical-align:middle; }
.decompte-table td:last-child { text-align:right;font-family:'Syne',sans-serif;font-weight:600;color:#0d1117; }
.decompte-sep td { padding:8px 0;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:#9ca3af;background:none; }
.decompte-total { background:#f0fdf4;border-radius:9px;padding:12px 14px;margin-top:12px;display:flex;justify-content:space-between;align-items:center; }
.decompte-total-lbl { font-size:12px;font-weight:600;color:#16a34a; }
.decompte-total-val { font-family:'Syne',sans-serif;font-size:18px;font-weight:700;color:#16a34a; }

/* ── INFO GRIDS ── */
.info-grid  { display:grid;grid-template-columns:1fr 1fr;gap:12px; }
.info-grid3 { display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px; }
.il { font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:.8px;color:#9ca3af;margin-bottom:4px; }
.iv { font-size:13px;font-weight:500;color:#0d1117; }
.iv-sub { font-size:11px;color:#6b7280;margin-top:2px; }

/* ── ACTIONS ── */
.actions-bar { display:flex;gap:8px;flex-wrap:wrap;margin-bottom:18px; }
.btn-act { display:flex;align-items:center;gap:6px;padding:9px 16px;border-radius:9px;font-size:12px;font-weight:500;font-family:'DM Sans',sans-serif;cursor:pointer;text-decoration:none;transition:all .15s;border:none; }
.btn-dark    { background:#0d1117;color:#fff; }
.btn-dark:hover { opacity:.85; }
.btn-outline { background:#fff;color:#374151;border:1px solid #e5e7eb; }
.btn-outline:hover { border-color:#c9a84c;color:#8a6e2f; }
.btn-green   { background:#dcfce7;color:#16a34a;border:1px solid #bbf7d0; }
.btn-green:hover { background:#bbf7d0; }
.btn-red     { background:#fee2e2;color:#dc2626;border:1px solid #fecaca; }
.btn-red:hover { background:#fecaca; }
.btn-act svg { width:14px;height:14px; }

/* ── TABLE ── */
.dt { width:100%;border-collapse:collapse; }
.dt th { padding:9px 16px;text-align:left;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#9ca3af;background:#f9fafb;border-bottom:1px solid #e5e7eb; }
.dt td { padding:12px 16px;font-size:13px;color:#374151;border-bottom:1px solid #f3f4f6;vertical-align:middle; }
.dt tbody tr:last-child td { border-bottom:none; }
.dt tbody tr:hover { background:#f9fafb; }
.badge { display:inline-flex;align-items:center;gap:4px;padding:3px 9px;border-radius:99px;font-size:11px;font-weight:600; }
.badge.g { background:#dcfce7;color:#16a34a; }
.badge.r { background:#fee2e2;color:#dc2626; }
.badge.o { background:#f5e9c9;color:#8a6e2f; }
.bdot { width:5px;height:5px;border-radius:50%;background:currentColor; }

/* ── SIDEBAR ── */
.sidebar-sticky { position:sticky;top:80px; }
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

/* progression durée */
.duree-bar { height:6px;background:#f3f4f6;border-radius:99px;overflow:hidden;margin-top:8px; }
.duree-fill { height:100%;border-radius:99px;background:linear-gradient(90deg,#c9a84c,#fbbf24); }

/* prochain loyer bloc */
.next-bloc { background:#fffbeb;border:1px solid #fde68a;border-radius:12px;padding:16px 18px; }
.next-lbl { font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:.8px;color:#d97706;margin-bottom:5px; }
.next-periode { font-family:'Syne',sans-serif;font-size:15px;font-weight:700;color:#92400e; }
.next-amt { font-family:'Syne',sans-serif;font-size:18px;font-weight:700;color:#92400e;margin-top:4px; }

/* observations */
.obs-box { background:#f9fafb;border-left:3px solid #c9a84c;border-radius:0 8px 8px 0;padding:12px 14px;font-size:13px;color:#374151;line-height:1.7;white-space:pre-wrap; }
</style>

<div style="padding:24px 32px 48px">

    {{-- BREADCRUMB --}}
    <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:#6b7280;margin-bottom:16px">
        <a href="{{ route('admin.contrats.index') }}" style="color:#6b7280;text-decoration:none">Contrats</a>
        <svg style="width:12px;height:12px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
        <span style="color:#0d1117;font-weight:500">{{ $contrat->reference_bail ?? 'Contrat #'.$contrat->id }}</span>
    </div>

    {{-- ACTIONS --}}
    <div class="actions-bar">
        @can('update', $contrat)
        <a href="{{ route('admin.contrats.edit', $contrat) }}" class="btn-act btn-dark">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            Modifier le contrat
        </a>
        @endcan

        @if($contrat->statut === 'actif')
        <a href="{{ route('admin.paiements.create', ['contrat_id' => $contrat->id]) }}" class="btn-act btn-green">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Enregistrer un paiement
        </a>
        @endif

        <a href="{{ route('biens.show', $contrat->bien) }}" class="btn-act btn-outline">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
            Voir le bien
        </a>

        <a href="{{ route('admin.paiements.index', ['contrat_id' => $contrat->id]) }}" class="btn-act btn-outline">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
            Tous les paiements
        </a>

        @if($contrat->statut === 'actif')
        @can('delete', $contrat)
        <form method="POST" action="{{ route('admin.contrats.destroy', $contrat) }}"
              onsubmit="return confirm('Résilier ce contrat ? Le bien repassera en Disponible.')">
            @csrf @method('DELETE')
            <button type="submit" class="btn-act btn-red">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                Résilier le contrat
            </button>
        </form>
        @endcan
        @endif
    </div>

    {{-- HERO --}}
    <div class="contrat-hero">
        @php
            $sl = match($contrat->statut) {
                'actif'   => 'actif',
                'resilié' => 'resilié',
                default   => 'expiré',
            };
            $slLabel = match($contrat->statut) {
                'actif'   => 'Actif',
                'resilié' => 'Résilié',
                default   => 'Expiré',
            };
        @endphp
        <span class="hero-status {{ $sl }}">
            <span class="status-dot"></span>{{ $slLabel }}
        </span>

        <div class="hero-ref">{{ $contrat->reference_bail ?? 'Contrat #'.$contrat->id }}</div>
        <div class="hero-title">{{ $contrat->bien->reference }}</div>
        <div class="hero-sub">
            {{ \App\Models\Contrat::TYPES_BAIL[$contrat->type_bail] ?? $contrat->type_bail }}
            · {{ $contrat->locataire->name }}
            · {{ $contrat->bien->adresse }}, {{ $contrat->bien->ville }}
        </div>

        <div class="hero-bottom">
            <div class="hero-stat">
                <div class="hs-lbl">Loyer contractuel</div>
                <div class="hs-val gold">{{ number_format($contrat->loyer_contractuel, 0, ',', ' ') }}<span style="font-size:11px;color:rgba(255,255,255,.3);margin-left:2px">F</span></div>
            </div>
            <div class="hero-stat">
                <div class="hs-lbl">Début</div>
                <div class="hs-val">{{ \Carbon\Carbon::parse($contrat->date_debut)->format('d/m/Y') }}</div>
            </div>
            <div class="hero-stat">
                <div class="hs-lbl">Fin</div>
                <div class="hs-val">{{ $contrat->date_fin ? \Carbon\Carbon::parse($contrat->date_fin)->format('d/m/Y') : 'Indéterminée' }}</div>
            </div>
            <div class="hero-stat">
                <div class="hs-lbl">Caution</div>
                <div class="hs-val green">{{ number_format($contrat->caution, 0, ',', ' ') }}<span style="font-size:11px;color:rgba(74,222,128,.4);margin-left:2px">F</span></div>
            </div>
        </div>
    </div>

    <div class="page-grid">

        {{-- ═══ COLONNE GAUCHE ═══ --}}
        <div>

            {{-- PARTIES --}}
            <div class="card">
                <div class="card-hd"><div class="card-title">Parties au contrat</div></div>
                <div class="card-body">
                    <div class="parties-grid">
                        {{-- LOCATAIRE --}}
                        <div class="party-card">
                            <div class="party-role">Locataire</div>
                            <div class="party-name">{{ $contrat->locataire->name }}</div>
                            @if($contrat->locataire->email)
                            <div class="party-info">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                                <a href="mailto:{{ $contrat->locataire->email }}">{{ $contrat->locataire->email }}</a>
                            </div>
                            @endif
                            @if($contrat->locataire->telephone)
                            <div class="party-info">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 9.81"/></svg>
                                <a href="tel:{{ $contrat->locataire->telephone }}">{{ $contrat->locataire->telephone }}</a>
                            </div>
                            @endif
                            <div style="margin-top:10px">
                                <a href="{{ route('admin.users.show', $contrat->locataire) }}"
                                   style="font-size:11px;font-weight:500;color:#c9a84c;text-decoration:none">Voir la fiche →</a>
                            </div>
                        </div>

                        {{-- PROPRIÉTAIRE (via bien) --}}
                        <div class="party-card">
                            <div class="party-role">Propriétaire (bailleur)</div>
                            <div class="party-name">{{ $contrat->bien->proprietaire->name }}</div>
                            @if($contrat->bien->proprietaire->telephone)
                            <div class="party-info">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07"/></svg>
                                <a href="tel:{{ $contrat->bien->proprietaire->telephone }}">{{ $contrat->bien->proprietaire->telephone }}</a>
                            </div>
                            @endif
                            <div style="margin-top:6px">
                                <div class="party-info">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
                                    <span>{{ $contrat->bien->reference }} · {{ $contrat->bien->ville }}</span>
                                </div>
                            </div>
                            <div style="margin-top:8px">
                                <a href="{{ route('admin.users.show', $contrat->bien->proprietaire) }}"
                                   style="font-size:11px;font-weight:500;color:#c9a84c;text-decoration:none">Voir la fiche →</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- DÉTAILS BAIL --}}
            <div class="card">
                <div class="card-hd"><div class="card-title">Détails du bail</div></div>
                <div class="card-body">
                    <div class="info-grid3">
                        <div>
                            <div class="il">Type de bail</div>
                            <div class="iv">{{ \App\Models\Contrat::TYPES_BAIL[$contrat->type_bail] ?? ucfirst($contrat->type_bail) }}</div>
                        </div>
                        <div>
                            <div class="il">Date de début</div>
                            <div class="iv">{{ \Carbon\Carbon::parse($contrat->date_debut)->format('d/m/Y') }}</div>
                            <div class="iv-sub">{{ \Carbon\Carbon::parse($contrat->date_debut)->diffForHumans() }}</div>
                        </div>
                        <div>
                            <div class="il">Date de fin</div>
                            <div class="iv">{{ $contrat->date_fin ? \Carbon\Carbon::parse($contrat->date_fin)->format('d/m/Y') : 'Indéterminée' }}</div>
                            @if($contrat->date_fin)
                            @php $jr = now()->diffInDays(\Carbon\Carbon::parse($contrat->date_fin), false); @endphp
                            <div class="iv-sub {{ $jr <= 30 ? 'color:#d97706' : '' }}">
                                {{ $jr > 0 ? $jr.'j restants' : 'Échu' }}
                            </div>
                            @endif
                        </div>
                        <div>
                            <div class="il">Caution</div>
                            <div class="iv" style="font-family:'Syne',sans-serif;font-weight:600;color:#16a34a">{{ number_format($contrat->caution, 0, ',', ' ') }} F</div>
                            @if($contrat->nombre_mois_caution)
                            <div class="iv-sub">{{ $contrat->nombre_mois_caution }} mois de loyer</div>
                            @endif
                        </div>
                        @if($contrat->frais_agence)
                        <div>
                            <div class="il">Frais d'agence</div>
                            <div class="iv">{{ number_format($contrat->frais_agence, 0, ',', ' ') }} F</div>
                        </div>
                        @endif
                        @if($contrat->indexation_annuelle)
                        <div>
                            <div class="il">Indexation annuelle</div>
                            <div class="iv">{{ $contrat->indexation_annuelle }}%</div>
                        </div>
                        @endif
                    </div>

                    {{-- Barre durée --}}
                    @if($contrat->date_fin)
                    @php
                        $d = \Carbon\Carbon::parse($contrat->date_debut);
                        $f = \Carbon\Carbon::parse($contrat->date_fin);
                        $total = max(1, $d->diffInDays($f));
                        $ecoule = min($total, $d->diffInDays(now()));
                        $prog = round(($ecoule / $total) * 100);
                    @endphp
                    <div style="margin-top:16px;padding-top:14px;border-top:1px solid #f3f4f6">
                        <div style="display:flex;justify-content:space-between;margin-bottom:6px">
                            <span style="font-size:11px;color:#6b7280">Progression du bail</span>
                            <span style="font-size:11px;font-weight:600;color:#8a6e2f">{{ $prog }}%</span>
                        </div>
                        <div class="duree-bar"><div class="duree-fill" style="width:{{ $prog }}%"></div></div>
                        <div style="display:flex;justify-content:space-between;margin-top:5px">
                            <span style="font-size:10px;color:#9ca3af">{{ $d->format('d/m/Y') }}</span>
                            <span style="font-size:10px;color:#9ca3af">{{ $f->format('d/m/Y') }}</span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- DÉCOMPTE FINANCIER --}}
            <div class="card">
                <div class="card-hd"><div class="card-title">Décompte financier mensuel</div></div>
                <div class="card-body">
                    <table class="decompte-table">
                        <tr>
                            <td>Loyer nu (hors charges)</td>
                            <td>{{ number_format($contrat->loyer_nu ?? $contrat->loyer_contractuel, 0, ',', ' ') }} F</td>
                        </tr>
                        @if(($contrat->charges_mensuelles ?? 0) > 0)
                        <tr>
                            <td style="color:#6b7280;font-size:12px;padding-left:10px">+ Charges mensuelles</td>
                            <td style="color:#6b7280">{{ number_format($contrat->charges_mensuelles, 0, ',', ' ') }} F</td>
                        </tr>
                        @endif
                        @if(($contrat->tom_amount ?? 0) > 0)
                        <tr>
                            <td style="color:#6b7280;font-size:12px;padding-left:10px">+ TOM</td>
                            <td style="color:#6b7280">{{ number_format($contrat->tom_amount, 0, ',', ' ') }} F</td>
                        </tr>
                        @endif
                    </table>
                    <div class="decompte-total">
                        <div class="decompte-total-lbl">Loyer contractuel total / mois</div>
                        <div class="decompte-total-val">{{ number_format($contrat->loyer_contractuel, 0, ',', ' ') }} F</div>
                    </div>
                </div>
            </div>

            {{-- GARANT --}}
            @if($contrat->garant_nom)
            <div class="card">
                <div class="card-hd"><div class="card-title">Garant</div></div>
                <div class="card-body">
                    <div class="info-grid">
                        <div>
                            <div class="il">Nom</div>
                            <div class="iv">{{ $contrat->garant_nom }}</div>
                        </div>
                        @if($contrat->garant_telephone)
                        <div>
                            <div class="il">Téléphone</div>
                            <div class="iv">
                                <a href="tel:{{ $contrat->garant_telephone }}" style="color:#0d1117;text-decoration:none">{{ $contrat->garant_telephone }}</a>
                            </div>
                        </div>
                        @endif
                        @if($contrat->garant_adresse)
                        <div style="grid-column:span 2">
                            <div class="il">Adresse</div>
                            <div class="iv">{{ $contrat->garant_adresse }}</div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            {{-- OBSERVATIONS --}}
            @if($contrat->observations)
            <div class="card">
                <div class="card-hd"><div class="card-title">Observations & notes</div></div>
                <div class="card-body">
                    <div class="obs-box">{{ $contrat->observations }}</div>
                </div>
            </div>
            @endif

            {{-- HISTORIQUE PAIEMENTS --}}
            <div class="card">
                <div class="card-hd">
                    <div class="card-title">Historique des paiements</div>
                    @if($contrat->statut === 'actif')
                    <a href="{{ route('admin.paiements.create', ['contrat_id' => $contrat->id]) }}" class="card-action" style="color:#16a34a">+ Paiement</a>
                    @endif
                </div>
                @if($contrat->paiements->isEmpty())
                <div style="padding:32px;text-align:center;color:#9ca3af;font-size:13px">
                    Aucun paiement enregistré pour ce contrat.
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
                                <th style="text-align:right">Net proprio</th>
                                <th style="text-align:center">Statut</th>
                                <th style="text-align:center">PDF</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($contrat->paiements->sortByDesc('periode') as $p)
                            <tr>
                                <td style="font-family:'Syne',sans-serif;font-size:11px;color:#9ca3af">{{ $p->reference_paiement }}</td>
                                <td style="font-weight:500">{{ \Carbon\Carbon::parse($p->periode)->translatedFormat('M Y') }}</td>
                                <td style="color:#6b7280">{{ \Carbon\Carbon::parse($p->date_paiement)->format('d/m/Y') }}</td>
                                <td style="color:#6b7280">{{ \App\Models\Paiement::MODES_PAIEMENT[$p->mode_paiement] ?? $p->mode_paiement }}</td>
                                <td style="text-align:right;font-family:'Syne',sans-serif;font-weight:600;color:#0d1117">{{ number_format($p->montant_encaisse, 0, ',', ' ') }} F</td>
                                <td style="text-align:right;font-family:'Syne',sans-serif;font-weight:600;color:#16a34a">{{ number_format($p->net_proprietaire, 0, ',', ' ') }} F</td>
                                <td style="text-align:center">
                                    @if($p->statut === 'valide')
                                        <span class="badge g"><span class="bdot"></span>Validé</span>
                                    @elseif($p->statut === 'annule')
                                        <span class="badge r"><span class="bdot"></span>Annulé</span>
                                    @else
                                        <span class="badge o"><span class="bdot"></span>En attente</span>
                                    @endif
                                </td>
                                <td style="text-align:center">
                                    @if($p->statut === 'valide')
                                    <a href="{{ route('admin.paiements.pdf', $p) }}" target="_blank"
                                       style="display:inline-flex;align-items:center;justify-content:center;width:26px;height:26px;border:1px solid #e5e7eb;border-radius:6px;color:#6b7280;text-decoration:none">
                                        <svg style="width:12px;height:12px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                    </a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4" style="padding:12px 16px;font-family:'Syne',sans-serif;font-weight:700;color:#0d1117;font-size:13px;background:#f9fafb">TOTAUX</td>
                                <td style="padding:12px 16px;text-align:right;font-family:'Syne',sans-serif;font-weight:700;color:#0d1117;background:#f9fafb">{{ number_format($contrat->paiements->where('statut','valide')->sum('montant_encaisse'), 0, ',', ' ') }} F</td>
                                <td style="padding:12px 16px;text-align:right;font-family:'Syne',sans-serif;font-weight:700;color:#16a34a;background:#f9fafb">{{ number_format($contrat->paiements->where('statut','valide')->sum('net_proprietaire'), 0, ',', ' ') }} F</td>
                                <td colspan="2" style="background:#f9fafb"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                @endif
            </div>

        </div>{{-- fin colonne gauche --}}

        {{-- ═══ COLONNE DROITE ═══ --}}
        <div class="sidebar-sticky">

            {{-- KPIs --}}
            <div class="kpi-mini gold">
                <div class="kpi-lbl">Total encaissé</div>
                <div class="kpi-val gold">{{ number_format($totalPaye ?? 0, 0, ',', ' ') }}<span class="kpi-u">F</span></div>
                <div class="kpi-s">{{ $nbPaiements ?? 0 }} paiement(s) validé(s)</div>
            </div>
            <div class="kpi-mini green">
                <div class="kpi-lbl">Net reversé proprio</div>
                <div class="kpi-val green">{{ number_format($totalNet ?? 0, 0, ',', ' ') }}<span class="kpi-u">F</span></div>
                <div class="kpi-s">Après commission agence</div>
            </div>
            <div class="kpi-mini blue">
                <div class="kpi-lbl">Loyer mensuel</div>
                <div class="kpi-val" style="color:#1d4ed8;font-size:18px">{{ number_format($contrat->loyer_contractuel, 0, ',', ' ') }}<span class="kpi-u">F</span></div>
                <div class="kpi-s">Caution : {{ number_format($contrat->caution, 0, ',', ' ') }} F</div>
            </div>

            {{-- PROCHAIN LOYER --}}
            @if(isset($prochainePeriode) && $contrat->statut === 'actif')
            <div class="next-bloc" style="margin-bottom:12px">
                <div class="next-lbl">Prochain loyer attendu</div>
                <div class="next-periode">{{ $prochainePeriode->translatedFormat('F Y') }}</div>
                <div class="next-amt">{{ number_format($contrat->loyer_contractuel, 0, ',', ' ') }} F</div>
                <a href="{{ route('admin.paiements.create', ['contrat_id' => $contrat->id]) }}"
                   style="display:flex;align-items:center;justify-content:center;gap:6px;margin-top:12px;padding:9px;background:#d97706;color:#fff;border-radius:8px;font-size:12px;font-weight:600;text-decoration:none">
                    <svg style="width:13px;height:13px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Enregistrer ce paiement
                </a>
            </div>
            @endif

            {{-- INFOS BIEN --}}
            <div class="card">
                <div class="card-hd">
                    <div class="card-title">Le bien</div>
                    <a href="{{ route('biens.show', $contrat->bien) }}" class="card-action">Voir →</a>
                </div>
                <div class="card-body" style="padding:14px 18px">
                    <div style="font-size:14px;font-weight:700;color:#0d1117;margin-bottom:4px">{{ $contrat->bien->reference }}</div>
                    <div style="font-size:12px;color:#6b7280;margin-bottom:8px">{{ \App\Models\Bien::TYPES[$contrat->bien->type] ?? $contrat->bien->type }}</div>
                    <div style="font-size:12px;color:#6b7280;display:flex;align-items:center;gap:4px">
                        <svg style="width:11px;height:11px;flex-shrink:0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        {{ $contrat->bien->adresse }}, {{ $contrat->bien->ville }}
                    </div>
                </div>
            </div>

            {{-- DATES CLÉS --}}
            <div class="card">
                <div class="card-hd"><div class="card-title">Dates clés</div></div>
                <div class="card-body" style="padding:14px 18px;display:flex;flex-direction:column;gap:10px">
                    <div style="display:flex;justify-content:space-between;font-size:12px">
                        <span style="color:#6b7280">Créé le</span>
                        <span style="font-weight:500;color:#0d1117">{{ $contrat->created_at->format('d/m/Y') }}</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;font-size:12px">
                        <span style="color:#6b7280">Dernière modif.</span>
                        <span style="font-weight:500;color:#0d1117">{{ $contrat->updated_at->format('d/m/Y') }}</span>
                    </div>
                    @if($contrat->reference_bail)
                    <div style="display:flex;justify-content:space-between;font-size:12px">
                        <span style="color:#6b7280">Réf. bail</span>
                        <span style="font-family:monospace;font-size:11px;color:#0d1117">{{ $contrat->reference_bail }}</span>
                    </div>
                    @endif
                </div>
            </div>

        </div>{{-- fin sidebar --}}

    </div>{{-- /page-grid --}}

</div>

</x-app-layout>