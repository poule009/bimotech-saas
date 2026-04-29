@extends('layouts.app')
@section('title', $immeuble->nom)
@section('breadcrumb', 'Immeubles › ' . $immeuble->nom)

@section('content')
<style>
.page-grid { display:grid;grid-template-columns:1fr 300px;gap:24px;align-items:start; }
.card { background:#fff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden;margin-bottom:16px; }
.card-hd { padding:14px 20px;border-bottom:1px solid #e5e7eb;display:flex;align-items:center;justify-content:space-between; }
.card-hd-left { display:flex;align-items:center;gap:10px; }
.card-icon { width:30px;height:30px;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0; }
.card-icon svg { width:15px;height:15px; }
.card-icon.gold   { background:#f5e9c9;color:#8a6e2f; }
.card-icon.blue   { background:#dbeafe;color:#1d4ed8; }
.card-icon.green  { background:#dcfce7;color:#16a34a; }
.card-title { font-family:'Syne',sans-serif;font-size:13px;font-weight:700;color:#0d1117; }
.card-body { padding:18px 20px; }
.il { font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:.8px;color:#9ca3af;margin-bottom:4px; }
.iv { font-size:13px;font-weight:500;color:#0d1117; }

/* Actions */
.actions-bar { display:flex;gap:8px;flex-wrap:wrap;margin-bottom:20px; }
.btn-act { display:flex;align-items:center;gap:6px;padding:9px 16px;border-radius:9px;font-size:12px;font-weight:500;font-family:'DM Sans',sans-serif;cursor:pointer;text-decoration:none;transition:all .15s;border:none; }
.btn-dark    { background:#0d1117;color:#fff; }
.btn-dark:hover { opacity:.85; }
.btn-gold    { background:#c9a84c;color:#0d1117; }
.btn-gold:hover { opacity:.85; }
.btn-outline { background:#fff;color:#374151;border:1px solid #e5e7eb; }
.btn-outline:hover { border-color:#c9a84c;color:#8a6e2f; }
.btn-red     { background:#fee2e2;color:#dc2626;border:1px solid #fecaca; }
.btn-red:hover { background:#fecaca; }
.btn-act svg { width:14px;height:14px; }

/* Hero */
.hero { background:linear-gradient(135deg,#0d1117,#1c2333);border-radius:14px;padding:22px 24px;margin-bottom:20px; }

/* Table */
.dt { width:100%;border-collapse:collapse; }
.dt th { padding:9px 16px;text-align:left;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.7px;color:#9ca3af;background:#f9fafb;border-bottom:1px solid #e5e7eb; }
.dt td { padding:12px 16px;font-size:13px;color:#374151;border-bottom:1px solid #f3f4f6;vertical-align:middle; }
.dt tbody tr:last-child td { border-bottom:none; }
.dt tbody tr:hover { background:#fafafa; }

/* Sidebar */
.side-card { background:#0d1117;border-radius:14px;overflow:hidden;margin-bottom:14px;position:sticky;top:24px; }
.side-hd { padding:12px 16px;border-bottom:1px solid rgba(255,255,255,.07); }
.side-title { font-family:'Syne',sans-serif;font-size:12px;font-weight:700;color:#fff; }
.side-body { padding:14px 16px; }
.side-row { display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px solid rgba(255,255,255,.06);font-size:12px; }
.side-row:last-child { border-bottom:none; }
.side-lbl { color:rgba(255,255,255,.4); }
.side-val { color:#e6edf3;font-weight:500; }
.badge { display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:99px;font-size:11px;font-weight:600; }
.badge-disponible { background:#dcfce7;color:#16a34a; }
.badge-loue       { background:#dbeafe;color:#1d4ed8; }
.badge-en_travaux { background:#fef9c3;color:#a16207; }
.badge-archive    { background:#f3f4f6;color:#6b7280; }
</style>

<div style="padding:0 0 48px">

    {{-- Breadcrumb --}}
    <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:#6b7280;margin-bottom:16px">
        <a href="{{ route('admin.immeubles.index') }}" style="color:#6b7280;text-decoration:none">Immeubles</a>
        <svg style="width:12px;height:12px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
        <span style="color:#0d1117;font-weight:500">{{ $immeuble->nom }}</span>
    </div>

    {{-- Actions --}}
    <div class="actions-bar">
        <a href="{{ route('admin.immeubles.edit', $immeuble) }}" class="btn-act btn-dark">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            Modifier
        </a>

        <a href="{{ route('admin.biens.create', ['immeuble_id' => $immeuble->id]) }}" class="btn-act btn-gold">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Ajouter une unité
        </a>

        <a href="{{ route('admin.immeubles.index') }}" class="btn-act btn-outline">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
            Retour
        </a>

        @if(!$immeuble->biens->contains(fn($b) => $b->contratActif !== null))
        <form method="POST" action="{{ route('admin.immeubles.destroy', $immeuble) }}"
              onsubmit="return confirm('Archiver cet immeuble et ses {{ $immeuble->biens->count() }} unité(s) ?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn-act btn-red">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/></svg>
                Archiver
            </button>
        </form>
        @endif
    </div>

    {{-- Hero --}}
    <div class="hero" style="margin-bottom:20px">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:16px">
            <div>
                <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:rgba(255,255,255,.3);margin-bottom:6px">
                    Immeuble
                </div>
                <div style="font-family:'Syne',sans-serif;font-size:22px;font-weight:700;color:#fff;margin-bottom:4px;letter-spacing:-.3px">
                    {{ $immeuble->nom }}
                </div>
                <div style="font-size:13px;color:rgba(255,255,255,.5)">
                    {{ $immeuble->adresse }} · {{ $immeuble->ville }}
                    @if($immeuble->nombre_niveaux)
                        · {{ $immeuble->nombre_niveaux }} niveau(x)
                    @endif
                </div>
            </div>
            <div style="text-align:right">
                <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:rgba(201,168,76,.6);margin-bottom:4px">Unités</div>
                <div style="font-family:'Syne',sans-serif;font-size:28px;font-weight:700;color:#c9a84c">
                    {{ $immeuble->biens->count() }}
                </div>
                <div style="font-size:11px;color:rgba(255,255,255,.3);margin-top:3px">
                    {{ $immeuble->biens->where('statut','loue')->count() }} louée(s)
                </div>
                @php $loyerTotal = $immeuble->biens->sum('loyer_mensuel'); @endphp
                @if($loyerTotal > 0)
                <div style="font-size:13px;font-weight:700;color:#c9a84c;margin-top:6px">
                    {{ number_format($loyerTotal, 0, ',', ' ') }} F/mois
                </div>
                <div style="font-size:10px;color:rgba(255,255,255,.25)">Loyer total mensuel</div>
                @endif
            </div>
        </div>
    </div>

    <div class="page-grid">

        {{-- ═══ COLONNE GAUCHE ═══ --}}
        <div>

            {{-- UNITÉS --}}
            <div class="card">
                <div class="card-hd">
                    <div class="card-hd-left">
                        <div class="card-icon blue">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                        </div>
                        <div class="card-title">Unités ({{ $immeuble->biens->count() }})</div>
                    </div>
                    <a href="{{ route('admin.biens.create', ['immeuble_id' => $immeuble->id]) }}"
                       style="display:inline-flex;align-items:center;gap:4px;font-size:12px;color:#1d4ed8;text-decoration:none;padding:5px 10px;background:#dbeafe;border-radius:6px">
                        + Ajouter
                    </a>
                </div>

                @if($immeuble->biens->isEmpty())
                <div style="padding:32px;text-align:center;color:#9ca3af;font-size:13px">
                    Aucune unité enregistrée pour cet immeuble.
                    <br>
                    <a href="{{ route('admin.biens.create', ['immeuble_id' => $immeuble->id]) }}"
                       style="display:inline-flex;align-items:center;gap:6px;margin-top:12px;padding:8px 16px;background:#c9a84c;color:#0d1117;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none">
                        Ajouter la première unité
                    </a>
                </div>
                @else
                <div style="overflow-x:auto">
                    <table class="dt">
                        <thead>
                            <tr>
                                <th>Référence</th>
                                <th>Unité / Type</th>
                                <th>Surface</th>
                                <th>Loyer</th>
                                <th>Statut</th>
                                <th>Locataire</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($immeuble->biens as $bien)
                            <tr>
                                <td>
                                    <span style="font-family:'Syne',sans-serif;font-size:11px;font-weight:600;color:#9ca3af">
                                        {{ $bien->reference }}
                                    </span>
                                </td>
                                <td>
                                    @if($bien->titre)
                                        <div style="font-weight:600;color:#0d1117;font-size:12px">{{ $bien->titre }}</div>
                                        <div style="font-size:11px;color:#9ca3af">{{ $bien->type_label }}</div>
                                    @else
                                        {{ $bien->type_label }}
                                    @endif
                                </td>
                                <td style="color:#6b7280">
                                    {{ $bien->surface_m2 ? $bien->surface_m2.' m²' : '—' }}
                                </td>
                                <td>
                                    <span style="font-family:'Syne',sans-serif;font-weight:700;color:#c9a84c">
                                        {{ number_format($bien->loyer_mensuel, 0, ',', ' ') }} F
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $bs = match($bien->statut) {
                                            'loue'       => 'badge-loue',
                                            'disponible' => 'badge-disponible',
                                            'en_travaux' => 'badge-en_travaux',
                                            default      => 'badge-archive',
                                        };
                                    @endphp
                                    <span class="badge {{ $bs }}">{{ $bien->statut_label }}</span>
                                </td>
                                <td style="font-size:12px;color:#6b7280">
                                    {{ $bien->contratActif?->locataire?->name ?? '—' }}
                                </td>
                                <td>
                                    <a href="{{ route('admin.biens.show', $bien) }}"
                                       style="display:inline-flex;align-items:center;gap:4px;padding:5px 10px;border:1px solid #e5e7eb;border-radius:6px;font-size:12px;color:#374151;text-decoration:none"
                                       onmouseover="this.style.borderColor='#c9a84c';this.style.color='#8a6e2f'"
                                       onmouseout="this.style.borderColor='#e5e7eb';this.style.color='#374151'">
                                        Voir
                                        <svg style="width:11px;height:11px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>

            {{-- DESCRIPTION --}}
            @if($immeuble->description)
            <div class="card">
                <div class="card-hd">
                    <div class="card-hd-left">
                        <div class="card-icon gold">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                        </div>
                        <div class="card-title">Description</div>
                    </div>
                </div>
                <div class="card-body">
                    <p style="font-size:13px;color:#374151;line-height:1.7">{{ $immeuble->description }}</p>
                </div>
            </div>
            @endif

        </div>{{-- fin colonne gauche --}}

        {{-- ═══ COLONNE DROITE ═══ --}}
        <div>

            {{-- Propriétaire --}}
            <div class="side-card">
                <div class="side-hd"><div class="side-title">Propriétaire</div></div>
                <div class="side-body">
                    <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px">
                        <div style="width:36px;height:36px;border-radius:50%;background:rgba(201,168,76,.15);border:1.5px solid rgba(201,168,76,.3);display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:700;color:#c9a84c;flex-shrink:0">
                            {{ strtoupper(substr($immeuble->proprietaire?->name ?? 'P', 0, 1)) }}
                        </div>
                        <div>
                            <div style="font-size:13px;font-weight:600;color:#e6edf3">
                                {{ $immeuble->proprietaire?->name ?? '—' }}
                            </div>
                            <div style="font-size:11px;color:#484f58">{{ $immeuble->proprietaire?->email ?? '' }}</div>
                        </div>
                    </div>
                    @if($immeuble->proprietaire?->telephone)
                    <div class="side-row">
                        <span class="side-lbl">Téléphone</span>
                        <span class="side-val">{{ $immeuble->proprietaire->telephone }}</span>
                    </div>
                    @endif
                    @if($immeuble->proprietaire)
                    <a href="{{ route('admin.users.show', $immeuble->proprietaire) }}"
                       style="display:flex;align-items:center;justify-content:center;gap:5px;margin-top:10px;padding:7px;border:1px solid rgba(255,255,255,.1);border-radius:8px;color:#c9a84c;font-size:12px;text-decoration:none">
                        Voir le profil →
                    </a>
                    @endif
                </div>
            </div>

            {{-- Récapitulatif --}}
            <div class="side-card">
                <div class="side-hd"><div class="side-title">Récapitulatif</div></div>
                <div class="side-body">
                    <div class="side-row">
                        <span class="side-lbl">Total unités</span>
                        <span class="side-val">{{ $immeuble->biens->count() }}</span>
                    </div>
                    <div class="side-row">
                        <span class="side-lbl">Louées</span>
                        <span class="side-val" style="color:#4ade80">{{ $immeuble->biens->where('statut','loue')->count() }}</span>
                    </div>
                    <div class="side-row">
                        <span class="side-lbl">Disponibles</span>
                        <span class="side-val" style="color:#60a5fa">{{ $immeuble->biens->where('statut','disponible')->count() }}</span>
                    </div>
                    <div class="side-row">
                        <span class="side-lbl">En travaux</span>
                        <span class="side-val">{{ $immeuble->biens->where('statut','en_travaux')->count() }}</span>
                    </div>
                    <div class="side-row">
                        <span class="side-lbl">Niveaux</span>
                        <span class="side-val">{{ $immeuble->nombre_niveaux ?? '—' }}</span>
                    </div>
                    <div class="side-row">
                        <span class="side-lbl">Ville</span>
                        <span class="side-val">{{ $immeuble->ville }}</span>
                    </div>
                    <div class="side-row">
                        <span class="side-lbl">Créé le</span>
                        <span class="side-val">{{ $immeuble->created_at?->format('d/m/Y') }}</span>
                    </div>
                </div>
            </div>

        </div>

    </div>{{-- /page-grid --}}

</div>
@endsection
