<x-app-layout>
    <x-slot name="header">États trimestriels BRS</x-slot>

<style>
.page-wrap   { padding:24px 32px 48px; }
.filter-bar  { background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:14px 18px;margin-bottom:22px;display:flex;align-items:center;gap:10px; }
.filter-sel  { padding:8px 13px;border:1px solid #e5e7eb;border-radius:8px;font-size:13px;font-family:'DM Sans',sans-serif;background:#f9fafb;outline:none; }
.filter-btn  { padding:8px 18px;background:#0d1117;color:#fff;border:none;border-radius:8px;font-size:12px;font-weight:500;font-family:'DM Sans',sans-serif;cursor:pointer; }

.trimestre-grid { display:grid;grid-template-columns:repeat(2,1fr);gap:16px; }
@media(max-width:900px){ .trimestre-grid { grid-template-columns:1fr; } }

.t-card { background:#fff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden;display:flex;flex-direction:column; }
.t-card-header { padding:14px 18px;border-bottom:1px solid #f3f4f6;display:flex;align-items:center;justify-content:space-between; }
.t-label { font-family:'Syne',sans-serif;font-size:16px;font-weight:700;color:#0d1117; }
.t-mois  { font-size:11px;color:#9ca3af;margin-top:1px; }
.t-body  { padding:16px 18px;flex:1; }
.t-footer { padding:12px 18px;border-top:1px solid #f3f4f6;display:flex;gap:8px;justify-content:flex-end; }

/* Statuts */
.badge-avenir   { display:inline-flex;align-items:center;gap:5px;padding:4px 10px;border-radius:99px;font-size:11px;font-weight:600;background:#f3f4f6;color:#6b7280; }
.badge-en_cours { display:inline-flex;align-items:center;gap:5px;padding:4px 10px;border-radius:99px;font-size:11px;font-weight:600;background:#dbeafe;color:#1d4ed8; }
.badge-orange   { display:inline-flex;align-items:center;gap:5px;padding:4px 10px;border-radius:99px;font-size:11px;font-weight:600;background:#fff7ed;color:#c2410c;border:1px solid #fed7aa; }
.badge-telechar { display:inline-flex;align-items:center;gap:5px;padding:4px 10px;border-radius:99px;font-size:11px;font-weight:600;background:#dcfce7;color:#16a34a; }

.kpi-row  { display:flex;gap:14px;margin-bottom:12px; }
.kpi-box  { flex:1;background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;padding:10px 12px; }
.kpi-lbl  { font-size:9px;font-weight:600;text-transform:uppercase;letter-spacing:.6px;color:#9ca3af;margin-bottom:3px; }
.kpi-val  { font-family:'Syne',sans-serif;font-size:15px;font-weight:700;color:#0d1117; }

.btn-pdf { display:inline-flex;align-items:center;gap:5px;padding:7px 13px;background:#0d1117;color:#fff;border:none;border-radius:8px;font-size:11px;font-weight:500;font-family:'DM Sans',sans-serif;text-decoration:none;cursor:pointer; }
.btn-csv { display:inline-flex;align-items:center;gap:5px;padding:7px 13px;background:#fff;color:#374151;border:1px solid #e5e7eb;border-radius:8px;font-size:11px;font-weight:500;font-family:'DM Sans',sans-serif;text-decoration:none;cursor:pointer; }
.btn-view { display:inline-flex;align-items:center;gap:5px;padding:7px 13px;background:#f9fafb;color:#374151;border:1px solid #e5e7eb;border-radius:8px;font-size:11px;font-weight:500;font-family:'DM Sans',sans-serif;text-decoration:none; }

.limite-info { font-size:11px;color:#6b7280;margin-top:6px; }
.limite-info.urgent { color:#c2410c; font-weight:600; }

.dl-info { font-size:10px;color:#16a34a;margin-top:4px; }
</style>

<div class="page-wrap">

    {{-- En-tête --}}
    <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:22px">
        <div>
            <h1 style="font-family:'Syne',sans-serif;font-size:22px;font-weight:700;color:#0d1117;letter-spacing:-.4px">
                États trimestriels BRS
            </h1>
            <p style="font-size:13px;color:#6b7280;margin-top:3px">
                Retenues à la source sur loyers — Art. 200 §5 CGI Sénégal — Année {{ $annee }}
            </p>
        </div>
        <div style="text-align:right;font-size:12px;color:#9ca3af;background:#f9fafb;border:1px solid #e5e7eb;border-radius:9px;padding:10px 14px;line-height:1.7">
            📋 À déposer au Centre des Services Fiscaux<br>
            <span style="color:#6b7280">15 Avr · 15 Juil · 15 Oct · 15 Jan N+1</span>
        </div>
    </div>

    {{-- Filtre année --}}
    <form method="GET">
        <div class="filter-bar">
            <span style="font-size:12px;color:#6b7280;font-weight:500">Année fiscale :</span>
            <select name="annee" class="filter-sel">
                @foreach($anneesDisponibles as $a)
                    <option value="{{ $a }}" {{ $annee == $a ? 'selected' : '' }}>{{ $a }}</option>
                @endforeach
            </select>
            <button type="submit" class="filter-btn">Afficher</button>
        </div>
    </form>

    {{-- 4 cartes trimestres --}}
    <div class="trimestre-grid">
        @foreach($trimestres as $t)
        @php
            $statut = $t['statut'];
            $isOrange = in_array($statut, ['a_deposer', 'en_retard']);
        @endphp
        <div class="t-card" style="border-top: 3px solid
            {{ $statut === 'telecharge' ? '#16a34a' : ($statut === 'en_cours' ? '#1d4ed8' : ($isOrange ? '#f97316' : '#e5e7eb')) }}">

            <div class="t-card-header">
                <div>
                    <div class="t-label">{{ $t['label'] }}</div>
                    <div class="t-mois">{{ $t['mois_label'] }}</div>
                </div>
                @if($statut === 'avenir')
                    <span class="badge-avenir">
                        <svg style="width:10px;height:10px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        À venir
                    </span>
                @elseif($statut === 'en_cours')
                    <span class="badge-en_cours">
                        <svg style="width:10px;height:10px" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="12" r="5"/></svg>
                        En cours
                    </span>
                @elseif($statut === 'a_deposer')
                    <span class="badge-orange">
                        <svg style="width:10px;height:10px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                        À déposer
                    </span>
                @elseif($statut === 'en_retard')
                    <span class="badge-orange" style="background:#fee2e2;color:#dc2626;border-color:#fecaca">
                        <svg style="width:10px;height:10px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        En retard
                    </span>
                @else
                    <span class="badge-telechar">
                        <svg style="width:10px;height:10px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                        Téléchargé
                    </span>
                @endif
            </div>

            <div class="t-body">
                <div class="kpi-row">
                    <div class="kpi-box" style="border-color:#fecaca">
                        <div class="kpi-lbl">BRS total retenu</div>
                        <div class="kpi-val" style="color:#dc2626">
                            {{ $t['total_brs'] > 0 ? number_format($t['total_brs'], 0, ',', ' ') . ' F' : '—' }}
                        </div>
                    </div>
                    <div class="kpi-box">
                        <div class="kpi-lbl">Bailleurs concernés</div>
                        <div class="kpi-val">{{ $t['nb_bailleurs'] }}</div>
                    </div>
                </div>

                <div class="limite-info {{ $statut === 'en_retard' ? 'urgent' : '' }}">
                    📅 Date limite de dépôt : {{ $t['date_limite']->translatedFormat('d F Y') }}
                    @if($statut === 'en_retard')
                        — <strong>Dépassée</strong>
                    @elseif($statut === 'a_deposer')
                        @php $joursRestants = now()->diffInDays($t['date_limite'], false); @endphp
                        — J-{{ $joursRestants }} restant(s)
                    @endif
                </div>

                @if($t['download'])
                <div class="dl-info">
                    ✅ Dernier téléchargement : {{ $t['download']->downloaded_at->format('d/m/Y à H:i') }}
                    ({{ strtoupper($t['download']->type) }})
                </div>
                @endif
            </div>

            <div class="t-footer">
                @if($t['total_brs'] > 0 || $t['nb_bailleurs'] > 0)
                <a href="{{ route('admin.etats-trimestriels.show', [$annee, $t['numero']]) }}"
                   class="btn-view">
                    <svg style="width:11px;height:11px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    Voir le détail
                </a>
                <a href="{{ route('admin.etats-trimestriels.pdf', [$annee, $t['numero']]) }}"
                   target="_blank" class="btn-pdf">
                    <svg style="width:11px;height:11px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                    PDF
                </a>
                <a href="{{ route('admin.etats-trimestriels.csv', [$annee, $t['numero']]) }}"
                   class="btn-csv">
                    <svg style="width:11px;height:11px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    CSV
                </a>
                @else
                <span style="font-size:11px;color:#9ca3af;font-style:italic">
                    Aucun paiement BRS sur ce trimestre
                </span>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    {{-- Note légale --}}
    <div style="margin-top:20px;padding:12px 16px;background:#fffbeb;border:1px solid #fde68a;border-radius:10px;font-size:12px;color:#92400e;line-height:1.6">
        <strong>Art. 200 §5 CGI Sénégal :</strong> Les agences immobilières, en tant que débiteurs établis au Sénégal,
        sont tenues de remettre aux services fiscaux un état trimestriel des versements effectués aux bailleurs
        <strong>personnes physiques</strong>. Seuls les paiements avec BRS &gt; 0 sont inclus.
        Les bailleurs personnes morales (IS) sont exclus. <strong>Consultez votre Centre des Services Fiscaux (CSF) pour le dépôt.</strong>
    </div>

</div>
</x-app-layout>
