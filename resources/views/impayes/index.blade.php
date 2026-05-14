@extends('layouts.app')
@section('title', 'Impayés')
@section('breadcrumb', 'Paiements › Impayés')

@section('content')
<style>
.kpi-row { display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:22px; }
.kpi { background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:16px 18px; }
.kpi.red    { border-top:3px solid #dc2626; }
.kpi.green  { border-top:3px solid #16a34a; }
.kpi.orange { border-top:3px solid #d97706; }
.kpi.blue   { border-top:3px solid #1d4ed8; }
.kpi-lbl { font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#9ca3af;margin-bottom:6px; }
.kpi-val { font-family:'Syne',sans-serif;font-size:22px;font-weight:700;color:#0d1117; }
.kpi-sub { font-size:11px;color:#9ca3af;margin-top:3px; }

/* Barre recouvrement */
.recouvrement-bar { background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:16px 20px;margin-bottom:22px; }
.bar-track { background:#f3f4f6;border-radius:99px;height:10px;overflow:hidden;margin:8px 0; }
.bar-fill  { background:linear-gradient(90deg,#16a34a,#4ade80);height:100%;border-radius:99px;transition:width .5s; }

/* Navigation mois */
.month-nav { display:flex;align-items:center;gap:8px; }
.month-btn { display:flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:7px;border:1px solid #e5e7eb;background:#fff;cursor:pointer;color:#6b7280;text-decoration:none;transition:all .15s; }
.month-btn:hover { border-color:#c9a84c;color:#8a6e2f; }
.month-btn svg { width:14px;height:14px; }
.month-current { font-family:'Syne',sans-serif;font-size:14px;font-weight:600;color:#0d1117;min-width:130px;text-align:center; }

/* Tables */
.table-card { background:#fff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden;margin-bottom:20px; }
.table-hd { padding:14px 20px;border-bottom:1px solid #e5e7eb;display:flex;align-items:center;justify-content:space-between; }
.table-title { font-family:'Syne',sans-serif;font-size:13px;font-weight:700;color:#0d1117; }
.dt { width:100%;border-collapse:collapse; }
.dt th { padding:9px 16px;text-align:left;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.7px;color:#9ca3af;background:#f9fafb;border-bottom:1px solid #e5e7eb; }
.dt td { padding:11px 16px;font-size:13px;color:#374151;border-bottom:1px solid #f3f4f6;vertical-align:middle; }
.dt tbody tr:last-child td { border-bottom:none; }
.dt tbody tr:hover { background:#fafafa; }
.dt tbody tr.urgent td { background:#fef9f9; }
.dt tbody tr.urgent:hover td { background:#fef2f2; }

/* Badges urgence */
.urgence-haute { display:inline-flex;align-items:center;gap:4px;padding:2px 8px;background:#fee2e2;color:#dc2626;border-radius:99px;font-size:10px;font-weight:700; }
.urgence-moy   { display:inline-flex;align-items:center;gap:4px;padding:2px 8px;background:#fef9c3;color:#a16207;border-radius:99px;font-size:10px;font-weight:700; }
.urgence-bas   { display:inline-flex;align-items:center;gap:4px;padding:2px 8px;background:#f3f4f6;color:#6b7280;border-radius:99px;font-size:10px;font-weight:700; }

.act-btn { display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:7px;border:1px solid #e5e7eb;background:#fff;color:#6b7280;text-decoration:none;transition:all .15s;cursor:pointer; }
.act-btn:hover { border-color:#c9a84c;color:#8a6e2f; }
.act-btn.green:hover { border-color:#bbf7d0;color:#16a34a;background:#f0fdf4; }
.btn-relance { display:inline-flex;align-items:center;gap:5px;padding:5px 12px;background:#fef9c3;color:#a16207;border:1px solid #fde68a;border-radius:7px;font-size:11px;font-weight:600;font-family:'DM Sans',sans-serif;cursor:pointer;transition:all .15s; }
.btn-relance:hover { background:#fde68a; }

/* Bouton export Excel */
.btn-excel { display:inline-flex;align-items:center;gap:6px;padding:7px 14px;background:#fff;border:1px solid #e5e7eb;border-radius:8px;font-size:12px;font-weight:600;color:#16a34a;text-decoration:none;font-family:'DM Sans',sans-serif;transition:all .2s;cursor:pointer;white-space:nowrap; }
.btn-excel:hover { border-color:#16a34a;background:#f0fdf4;color:#15803d; }
.btn-excel:active { transform:scale(.97); }
.btn-excel svg { flex-shrink:0;transition:transform .2s; }
.btn-excel:hover svg { transform:translateY(1px); }
/* État chargement */
.btn-excel.loading { pointer-events:none;opacity:.75; }
.btn-excel.loading .excel-icon { display:none; }
.btn-excel.loading .excel-label { display:none; }
.btn-excel.loading::after { content:'Génération…';font-size:12px; }
.btn-excel.loading::before { content:'';display:inline-block;width:14px;height:14px;border:2px solid #16a34a;border-top-color:transparent;border-radius:50%;animation:xlspin .65s linear infinite;margin-right:6px; }
@keyframes xlspin { to { transform:rotate(360deg); } }
</style>

<div style="padding:0 0 48px">

    {{-- Header --}}
    <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:22px;flex-wrap:wrap;gap:12px">
        <div>
            <h1 style="font-family:'Syne',sans-serif;font-size:22px;font-weight:700;color:#0d1117;letter-spacing:-.4px">
                Suivi des impayés
            </h1>
            <p style="font-size:13px;color:#6b7280;margin-top:3px">
                {{ $periode->translatedFormat('F Y') }}
                · {{ $stats['nb_impayes'] }} impayé(s) sur {{ $stats['nb_impayes'] + $stats['nb_payes'] }} contrats actifs
            </p>
        </div>

        {{-- Actions header : export + navigation mois --}}
        <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">

        {{-- Bouton export Excel --}}
        <a href="{{ route('admin.impayes.export', ['mois' => $mois, 'annee' => $annee]) }}"
           id="btn-export-excel"
           class="btn-excel"
           title="Télécharger le rapport Excel de {{ $periode->translatedFormat('F Y') }}"
           onclick="lancerExport(this)">
            <svg class="excel-icon" style="width:15px;height:15px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                <polyline points="14 2 14 8 20 8"/>
                <line x1="12" y1="18" x2="12" y2="12"/>
                <polyline points="9 15 12 18 15 15"/>
            </svg>
            <span class="excel-label">Exporter Excel</span>
        </a>

        {{-- Navigation mois --}}
        <div class="month-nav">
            @php
                $prevMois  = $mois == 1  ? 12 : $mois - 1;
                $prevAnnee = $mois == 1  ? $annee - 1 : $annee;
                $nextMois  = $mois == 12 ? 1  : $mois + 1;
                $nextAnnee = $mois == 12 ? $annee + 1 : $annee;
            @endphp
            <a href="{{ route('admin.impayes.index', ['mois' => $prevMois, 'annee' => $prevAnnee]) }}" class="month-btn">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
            </a>
            <div class="month-current">{{ $periode->translatedFormat('F Y') }}</div>
            @if($nextAnnee < now()->year || ($nextAnnee == now()->year && $nextMois <= now()->month))
            <a href="{{ route('admin.impayes.index', ['mois' => $nextMois, 'annee' => $nextAnnee]) }}" class="month-btn">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            </a>
            @else
            <span class="month-btn" style="opacity:.3;cursor:not-allowed">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            </span>
            @endif
        </div>

        </div>{{-- /actions header --}}
    </div>

    {{-- KPIs --}}
    <div class="kpi-row">
        <div class="kpi red">
            <div class="kpi-lbl">Impayés</div>
            <div class="kpi-val" style="color:#dc2626">{{ $stats['nb_impayes'] }}</div>
            <div class="kpi-sub">Contrats sans paiement</div>
        </div>
        <div class="kpi green">
            <div class="kpi-lbl">Payés</div>
            <div class="kpi-val" style="color:#16a34a">{{ $stats['nb_payes'] }}</div>
            <div class="kpi-sub">Paiements validés</div>
        </div>
        <div class="kpi orange">
            <div class="kpi-lbl">Montant dû</div>
            <div class="kpi-val" style="font-size:16px;color:#d97706">
                {{ number_format($stats['montant_du'], 0, ',', ' ') }}
            </div>
            <div class="kpi-sub">FCFA à recouvrer</div>
        </div>
        <div class="kpi blue">
            <div class="kpi-lbl">Taux recouvrement</div>
            <div class="kpi-val" style="color:#1d4ed8">{{ $stats['taux_recouvrement'] }}%</div>
            <div class="kpi-sub">Ce mois</div>
        </div>
    </div>

    {{-- Barre recouvrement --}}
    <div class="recouvrement-bar">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px">
            <span style="font-size:12px;font-weight:600;color:#0d1117">Taux de recouvrement</span>
            <span style="font-family:'Syne',sans-serif;font-size:14px;font-weight:700;color:{{ $stats['taux_recouvrement'] >= 80 ? '#16a34a' : ($stats['taux_recouvrement'] >= 50 ? '#d97706' : '#dc2626') }}">
                {{ $stats['taux_recouvrement'] }}%
            </span>
        </div>
        <div class="bar-track">
            <div class="bar-fill" style="width:{{ $stats['taux_recouvrement'] }}%;background:{{ $stats['taux_recouvrement'] >= 80 ? 'linear-gradient(90deg,#16a34a,#4ade80)' : ($stats['taux_recouvrement'] >= 50 ? 'linear-gradient(90deg,#d97706,#fbbf24)' : 'linear-gradient(90deg,#dc2626,#f87171)') }}"></div>
        </div>
        <div style="display:flex;justify-content:space-between;font-size:11px;color:#9ca3af">
            <span>{{ $stats['nb_payes'] }} payés</span>
            <span>{{ $stats['nb_impayes'] }} impayés</span>
        </div>
    </div>

    {{-- ═══ TABLE IMPAYÉS ═══ --}}
    @if($impayes->isNotEmpty())
    <div class="table-card">
        <div class="table-hd">
            <div class="table-title" style="color:#dc2626">
                <svg style="width:14px;height:14px;display:inline;margin-right:4px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                Impayés — {{ $impayes->count() }} contrat(s)
            </div>
            <span style="font-size:11px;color:#9ca3af">Triés par retard décroissant</span>
        </div>
        <div style="overflow-x:auto">
            <table class="dt">
                <thead>
                    <tr>
                        <th>Bien</th>
                        <th>Locataire</th>
                        <th>Propriétaire</th>
                        <th style="text-align:right">Loyer dû</th>
                        <th style="text-align:center">Retard</th>
                        <th style="text-align:center">Urgence</th>
                        <th style="text-align:center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($impayes as $item)
                    @php
                        $jr    = $item['jours_retard'];
                        $urgent = $jr > 15;
                        $loc   = $item['contrat']->locataire;
                        $tel   = $loc?->telephone ?? null;
                        // Nettoyage numéro → format international Sénégal
                        $telClean = $tel ? preg_replace('/[^0-9]/', '', $tel) : null;
                        if ($telClean && strlen($telClean) === 9) $telClean = '221' . $telClean;
                        $waMsg = rawurlencode(
                            "Bonjour " . ($loc?->name ?? '') . ", votre loyer de " .
                            number_format($item['montant_du'], 0, ',', ' ') . " FCFA pour " .
                            $periode->translatedFormat('F Y') . " n'a pas encore été reçu. " .
                            "Merci de régulariser votre situation."
                        );
                    @endphp
                    <tr class="{{ $urgent ? 'urgent' : '' }}">
                        <td>
                            <div style="font-weight:500;color:#0d1117">
                                {{ $item['contrat']->bien?->reference ?? '—' }}
                            </div>
                            <div style="font-size:11px;color:#6b7280">
                                {{ $item['contrat']->bien?->adresse }}, {{ $item['contrat']->bien?->ville }}
                            </div>
                        </td>
                        <td>
                            <div style="font-size:13px;color:#0d1117">{{ $loc?->name ?? '—' }}</div>
                            @if($tel)
                                <a href="tel:{{ $telClean ?? $tel }}"
                                   style="font-size:11px;color:#6b7280;text-decoration:none;display:inline-flex;align-items:center;gap:3px"
                                   title="Appeler">
                                    <svg style="width:10px;height:10px;flex-shrink:0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 9.81 19.79 19.79 0 01.07 1.18 2 2 0 012 0h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.09 7.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/></svg>
                                    {{ $tel }}
                                </a>
                            @else
                                <div style="font-size:11px;color:#9ca3af">{{ $loc?->email ?? '' }}</div>
                            @endif
                        </td>
                        <td style="font-size:12px;color:#6b7280">
                            {{ $item['contrat']->bien?->proprietaire?->name ?? '—' }}
                        </td>
                        <td style="text-align:right;font-family:'Syne',sans-serif;font-weight:700;color:#dc2626">
                            {{ number_format($item['montant_du'], 0, ',', ' ') }} F
                        </td>
                        <td style="text-align:center">
                            @if($jr > 0)
                                <span style="font-family:'Syne',sans-serif;font-size:13px;font-weight:700;color:{{ $jr > 15 ? '#dc2626' : ($jr > 7 ? '#d97706' : '#6b7280') }}">
                                    {{ $jr }}j
                                </span>
                            @else
                                <span style="font-size:11px;color:#9ca3af">Ce mois</span>
                            @endif
                        </td>
                        <td style="text-align:center">
                            @if($jr > 15)
                                <span class="urgence-haute">🔴 Haute</span>
                            @elseif($jr > 7)
                                <span class="urgence-moy">🟡 Moyenne</span>
                            @else
                                <span class="urgence-bas">⚪ Faible</span>
                            @endif
                        </td>
                        <td>
                            <div style="display:flex;align-items:center;justify-content:center;gap:4px;flex-wrap:wrap">
                                {{-- Paiement rapide --}}
                                <button type="button"
                                        class="act-btn green"
                                        title="Saisir le paiement rapidement"
                                        onclick="ouvrirPaiementRapide({{ $item['contrat']->id }}, '{{ addslashes($loc?->name ?? '—') }}', '{{ addslashes($item['contrat']->bien?->reference ?? '—') }}', {{ $item['montant_du'] }}, '{{ $mois }}/{{ $annee }}')">
                                    <svg style="width:13px;height:13px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                </button>
                                {{-- WhatsApp --}}
                                @if($telClean)
                                <a href="https://wa.me/{{ $telClean }}?text={{ $waMsg }}"
                                   target="_blank"
                                   class="act-btn"
                                   style="background:#25D366;border-color:#25D366;color:#fff"
                                   title="Relancer sur WhatsApp">
                                    <svg style="width:13px;height:13px" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                </a>
                                @endif
                                {{-- Voir contrat --}}
                                <a href="{{ route('admin.contrats.show', $item['contrat']) }}"
                                   class="act-btn" title="Voir le contrat">
                                    <svg style="width:13px;height:13px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                </a>
                                {{-- Relance --}}
                                <form method="POST"
                                      action="{{ route('admin.impayes.relance', $item['contrat']) }}"
                                      data-confirm="Un email de relance sera envoyé à {{ $item['contrat']->locataire?->name ?? 'ce locataire' }}."
                                      data-confirm-title="Envoyer une relance ?"
                                      data-confirm-ok="Envoyer"
                                      data-confirm-color="#d97706"
                                      data-confirm-icon-bg="#fef9c3">
                                    @csrf
                                    <input type="hidden" name="mois" value="{{ $mois }}">
                                    <input type="hidden" name="annee" value="{{ $annee }}">
                                    <button type="submit" class="btn-relance" title="Envoyer relance email">
                                        <svg style="width:11px;height:11px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                                        Relancer
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @else
    <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:12px;padding:32px;text-align:center;margin-bottom:20px">
        <svg style="width:36px;height:36px;color:#16a34a;margin:0 auto 12px;display:block" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
        <div style="font-family:'Syne',sans-serif;font-size:15px;font-weight:700;color:#16a34a;margin-bottom:4px">
            Aucun impayé ce mois !
        </div>
        <div style="font-size:13px;color:#6b7280">
            Tous les loyers de {{ $periode->translatedFormat('F Y') }} ont été réglés.
        </div>
    </div>
    @endif

    {{-- ═══ TABLE PAYÉS ═══ --}}
    @if($payes->isNotEmpty())
    <div class="table-card">
        <div class="table-hd">
            <div class="table-title" style="color:#16a34a">
                <svg style="width:14px;height:14px;display:inline;margin-right:4px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                Payés — {{ $payes->count() }} contrat(s)
            </div>
        </div>
        <div style="overflow-x:auto">
            <table class="dt">
                <thead>
                    <tr>
                        <th>Bien</th>
                        <th>Locataire</th>
                        <th>Date paiement</th>
                        <th style="text-align:right">Montant</th>
                        <th>Mode</th>
                        <th style="text-align:center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payes as $item)
                    <tr>
                        <td>
                            <div style="font-weight:500;color:#0d1117">
                                {{ $item['contrat']->bien?->reference ?? '—' }}
                            </div>
                            <div style="font-size:11px;color:#6b7280">{{ $item['contrat']->bien?->ville }}</div>
                        </td>
                        <td style="font-size:13px;color:#0d1117">
                            {{ $item['contrat']->locataire?->name ?? '—' }}
                        </td>
                        <td style="font-size:12px;color:#6b7280">
                            {{ $item['paiement']->date_paiement
                                ? \Carbon\Carbon::parse($item['paiement']->date_paiement)->format('d/m/Y')
                                : '—' }}
                        </td>
                        <td style="text-align:right;font-family:'Syne',sans-serif;font-weight:700;color:#16a34a">
                            {{ number_format($item['paiement']->montant_encaisse, 0, ',', ' ') }} F
                        </td>
                        <td style="font-size:12px;color:#6b7280">
                            @php
                                $modes = ['especes'=>'Espèces','virement'=>'Virement','cheque'=>'Chèque',
                                          'wave'=>'Wave','orange_money'=>'Orange Money',
                                          'free_money'=>'Free Money','e_money'=>'E-Money'];
                            @endphp
                            {{ $modes[$item['paiement']->mode_paiement] ?? $item['paiement']->mode_paiement }}
                        </td>
                        <td>
                            <div style="display:flex;align-items:center;justify-content:center;gap:4px">
                                <a href="{{ route('admin.paiements.show', $item['paiement']) }}"
                                   class="act-btn" title="Voir le paiement">
                                    <svg style="width:13px;height:13px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                </a>
                                <a href="{{ route('admin.paiements.pdf', $item['paiement']) }}"
                                   target="_blank" class="act-btn" title="Quittance PDF">
                                    <svg style="width:13px;height:13px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>
{{-- ══ MODALE PAIEMENT RAPIDE ══════════════════════════════════════════ --}}
<div id="modal-paiement-rapide"
     style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:200;align-items:center;justify-content:center">
    <div style="background:#fff;border-radius:14px;padding:24px;width:440px;max-width:92vw;box-shadow:0 20px 60px rgba(0,0,0,.2);animation:confirmIn .18s ease">
        {{-- Header --}}
        <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:18px">
            <div>
                <div style="font-family:'Syne',sans-serif;font-size:15px;font-weight:700;color:#0d1117" id="pr-title">Enregistrer un paiement</div>
                <div style="font-size:12px;color:#6b7280;margin-top:2px" id="pr-sub"></div>
            </div>
            <button onclick="fermerPaiementRapide()"
                    style="background:none;border:none;cursor:pointer;color:#9ca3af;font-size:20px;line-height:1;padding:0">×</button>
        </div>

        <form method="POST" action="{{ route('admin.paiements.store') }}" id="form-paiement-rapide">
            @csrf
            <input type="hidden" name="contrat_id" id="pr-contrat-id">
            <input type="hidden" name="statut" value="valide">

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px">
                <div>
                    <label class="form-label">Période <span class="req">*</span></label>
                    <input type="month" name="periode" id="pr-periode" class="form-input"
                           value="{{ now()->format('Y-m') }}" required>
                </div>
                <div>
                    <label class="form-label">Date de paiement <span class="req">*</span></label>
                    <input type="date" name="date_paiement" id="pr-date" class="form-input"
                           value="{{ now()->format('Y-m-d') }}" required>
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px">
                <div>
                    <label class="form-label">Montant encaissé (FCFA) <span class="req">*</span></label>
                    <input type="number" name="montant_encaisse" id="pr-montant" class="form-input"
                           min="0" step="500" required>
                    <div style="font-size:11px;color:#9ca3af;margin-top:3px" id="pr-montant-hint"></div>
                </div>
                <div>
                    <label class="form-label">Mode de paiement <span class="req">*</span></label>
                    <select name="mode_paiement" class="form-select" required>
                        <option value="especes">Espèces</option>
                        <option value="wave">Wave</option>
                        <option value="orange_money">Orange Money</option>
                        <option value="virement">Virement</option>
                        <option value="cheque">Chèque</option>
                        <option value="free_money">Free Money</option>
                        <option value="e_money">E-Money</option>
                    </select>
                </div>
            </div>

            <div style="margin-bottom:16px">
                <label class="form-label">Notes <span class="opt">(optionnel)</span></label>
                <textarea name="notes" class="form-textarea" rows="2" placeholder="Observations…"></textarea>
            </div>

            <div style="display:flex;justify-content:flex-end;gap:8px">
                <button type="button" onclick="fermerPaiementRapide()" class="btn-cancel">Annuler</button>
                <button type="submit" class="btn-submit" id="pr-submit-btn">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:13px;height:13px"><polyline points="20 6 9 17 4 12"/></svg>
                    Valider le paiement
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// ── Export Excel ──────────────────────────────────────────────────────────────
function lancerExport(btn) {
    btn.classList.add('loading');
    // Remettre l'état normal après 8s (le download aura commencé)
    setTimeout(function() {
        btn.classList.remove('loading');
    }, 8000);
}

// ── Paiement rapide ───────────────────────────────────────────────────────────
function ouvrirPaiementRapide(contratId, locataire, bien, montant, periode) {
    document.getElementById('pr-contrat-id').value = contratId;
    document.getElementById('pr-title').textContent  = 'Paiement — ' + bien;
    document.getElementById('pr-sub').textContent    = locataire + ' · ' + periode;
    document.getElementById('pr-montant').value      = montant;
    document.getElementById('pr-montant-hint').textContent = 'Loyer attendu : ' + Number(montant).toLocaleString('fr-FR') + ' F';

    // Convertir la période "MM/YYYY" en "YYYY-MM" pour l'input month
    var parts = periode.split('/');
    if (parts.length === 2) {
        document.getElementById('pr-periode').value = parts[1] + '-' + parts[0].padStart(2, '0');
    }

    var modal = document.getElementById('modal-paiement-rapide');
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
    document.getElementById('pr-montant').focus();
}

function fermerPaiementRapide() {
    document.getElementById('modal-paiement-rapide').style.display = 'none';
    document.body.style.overflow = '';
}

document.getElementById('modal-paiement-rapide').addEventListener('click', function(e) {
    if (e.target === this) fermerPaiementRapide();
});
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') fermerPaiementRapide();
});
</script>

@endsection