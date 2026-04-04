<x-app-layout>
    <x-slot name="header">Détail paiement</x-slot>

<style>
/* ── LAYOUT ── */
.page-grid { display:grid; grid-template-columns:1fr 340px; gap:20px; }

/* ── CARDS ── */
.card { background:#fff; border:1px solid #e5e7eb; border-radius:14px; overflow:hidden; margin-bottom:20px; }
.card:last-child { margin-bottom:0; }
.card-hd { padding:16px 20px; border-bottom:1px solid #e5e7eb; display:flex; align-items:center; justify-content:space-between; }
.card-title { font-family:'Syne',sans-serif; font-size:13px; font-weight:700; color:#0d1117; }
.card-body  { padding:20px; }

/* ── HERO PAIEMENT ── */
.paiement-hero {
    background:#0d1117; border-radius:14px; padding:28px 28px 24px;
    margin-bottom:20px; position:relative; overflow:hidden;
}
.paiement-hero::before {
    content:''; position:absolute; top:-50px;right:-50px;
    width:180px;height:180px;border-radius:50%;
    background:rgba(201,168,76,0.08);
}
.paiement-hero::after {
    content:''; position:absolute; bottom:-30px;left:80px;
    width:120px;height:120px;border-radius:50%;
    background:rgba(201,168,76,0.05);
}

.hero-ref { font-family:'Syne',sans-serif; font-size:11px; font-weight:600; letter-spacing:1.5px; text-transform:uppercase; color:rgba(255,255,255,0.4); margin-bottom:8px; }
.hero-amount { font-family:'Syne',sans-serif; font-size:36px; font-weight:800; color:#fff; letter-spacing:-1px; line-height:1; }
.hero-amount-u { font-size:16px; font-weight:400; color:rgba(255,255,255,0.4); margin-left:6px; }
.hero-periode { font-size:13px; color:rgba(255,255,255,0.5); margin-top:8px; }

.hero-bottom { display:flex; align-items:center; justify-content:space-between; margin-top:22px; padding-top:18px; border-top:1px solid rgba(255,255,255,0.08); position:relative; z-index:1; }

.hero-stat { }
.hero-stat-lbl { font-size:10px; font-weight:600; letter-spacing:1px; text-transform:uppercase; color:rgba(255,255,255,0.35); margin-bottom:4px; }
.hero-stat-val { font-family:'Syne',sans-serif; font-size:16px; font-weight:700; color:#fff; }
.hero-stat-val.green { color:#4ade80; }
.hero-stat-val.gold  { color:#c9a84c; }

.hero-divider { width:1px; height:36px; background:rgba(255,255,255,0.08); }

/* badge statut */
.status-badge { display:inline-flex; align-items:center; gap:6px; padding:6px 14px; border-radius:99px; font-size:12px; font-weight:600; }
.status-badge.g    { background:rgba(74,222,128,0.12); color:#4ade80; border:1px solid rgba(74,222,128,0.2); }
.status-badge.r    { background:rgba(248,113,113,0.12); color:#f87171; border:1px solid rgba(248,113,113,0.2); }
.status-badge.o    { background:rgba(201,168,76,0.12);  color:#c9a84c; border:1px solid rgba(201,168,76,0.2); }
.status-dot { width:6px; height:6px; border-radius:50%; background:currentColor; }

/* ── DÉCOMPTE FINANCIER ── */
.decompte-table { width:100%; border-collapse:collapse; }
.decompte-table tr { border-bottom:1px solid #f3f4f6; }
.decompte-table tr:last-child { border-bottom:none; }
.decompte-table td { padding:12px 0; font-size:13px; color:#374151; vertical-align:middle; }
.decompte-table td:last-child { text-align:right; font-family:'Syne',sans-serif; font-weight:600; color:#0d1117; }

.decompte-section { background:#f9fafb; border-radius:8px; }
.decompte-section td { padding:10px 12px; font-size:12px; color:#6b7280; }
.decompte-section td:last-child { color:#6b7280; font-weight:500; }

.decompte-total { background:#0d1117 !important; border-radius:10px; }
.decompte-total td { padding:14px 12px !important; color:white !important; font-size:14px !important; font-weight:700 !important; border-bottom:none !important; }
.decompte-total td:last-child { color:#4ade80 !important; font-size:18px !important; }

.decompte-sep { background:#f5e9c9; border-radius:6px; }
.decompte-sep td { padding:8px 12px; font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:#8a6e2f; border-bottom:none !important; }

/* ── INFO ROWS ── */
.info-grid { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
.info-item { }
.info-lbl { font-size:10px; font-weight:600; text-transform:uppercase; letter-spacing:.8px; color:#9ca3af; margin-bottom:4px; }
.info-val { font-size:13px; font-weight:500; color:#0d1117; }
.info-sub { font-size:11px; color:#6b7280; margin-top:2px; }

/* ── APERÇU QUITTANCE ── */
.quittance-preview {
    background:#fff; border:1px solid #e5e7eb; border-radius:14px;
    overflow:hidden; position:sticky; top:80px;
}
.quittance-header {
    background:#0d1117; padding:20px;
    display:flex; flex-direction:column; gap:4px;
}
.quittance-agency  { font-family:'Syne',sans-serif; font-size:15px; font-weight:700; color:#fff; }
.quittance-agency-sub { font-size:10px; color:rgba(255,255,255,0.4); letter-spacing:1px; text-transform:uppercase; }
.quittance-title-row { display:flex; align-items:center; justify-content:space-between; margin-top:8px; }
.quittance-doc-title { font-family:'Syne',sans-serif; font-size:13px; font-weight:600; color:#c9a84c; text-transform:uppercase; letter-spacing:1px; }
.quittance-ref { font-size:10px; color:rgba(255,255,255,0.3); font-family:monospace; }

.quittance-body { padding:16px; }

.q-section { margin-bottom:14px; }
.q-section-title { font-size:9px; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:#9ca3af; margin-bottom:8px; border-bottom:1px solid #f3f4f6; padding-bottom:5px; }

.q-row { display:flex; justify-content:space-between; align-items:center; padding:5px 0; font-size:11px; color:#374151; border-bottom:1px solid #f9fafb; }
.q-row:last-child { border-bottom:none; }
.q-row-lbl { color:#6b7280; flex:1; }
.q-row-val { font-family:'Syne',sans-serif; font-weight:600; color:#0d1117; text-align:right; }
.q-row-val.green { color:#16a34a; }
.q-row-val.gold  { color:#8a6e2f; }

.q-total { background:#f0fdf4; border:1px solid #bbf7d0; border-radius:8px; padding:10px 12px; margin-top:10px; display:flex; justify-content:space-between; align-items:center; }
.q-total-lbl { font-size:11px; font-weight:600; color:#16a34a; }
.q-total-val { font-family:'Syne',sans-serif; font-size:15px; font-weight:700; color:#16a34a; }

.q-parties { display:grid; grid-template-columns:1fr 1fr; gap:8px; margin-bottom:14px; }
.q-party { background:#f9fafb; border-radius:8px; padding:10px 12px; }
.q-party-role { font-size:9px; font-weight:700; text-transform:uppercase; letter-spacing:.8px; color:#9ca3af; margin-bottom:4px; }
.q-party-name { font-size:12px; font-weight:600; color:#0d1117; }
.q-party-sub  { font-size:10px; color:#6b7280; margin-top:1px; }

.q-footer { padding:12px 16px; border-top:1px solid #e5e7eb; background:#f9fafb; }
.q-footer-text { font-size:9px; color:#9ca3af; line-height:1.5; }

/* ── ACTIONS ── */
.actions-bar { display:flex; gap:10px; margin-bottom:20px; flex-wrap:wrap; }
.btn-action { display:flex; align-items:center; gap:7px; padding:10px 18px; border-radius:9px; font-size:13px; font-weight:500; font-family:'DM Sans',sans-serif; cursor:pointer; text-decoration:none; transition:all .15s; border:none; }
.btn-dark    { background:#0d1117; color:#fff; }
.btn-dark:hover { opacity:.85; }
.btn-outline { background:#fff; color:#374151; border:1px solid #e5e7eb; }
.btn-outline:hover { background:#f9fafb; border-color:#d1d5db; }
.btn-danger  { background:#fee2e2; color:#dc2626; border:1px solid #fecaca; }
.btn-danger:hover { background:#fecaca; }
.btn-action svg { width:15px; height:15px; }

/* ── NOTES ── */
.notes-box { background:#fffbeb; border:1px solid #fde68a; border-radius:10px; padding:14px 16px; font-size:12px; color:#92400e; line-height:1.6; white-space:pre-wrap; }

/* ── BADGE INLINE ── */
.badge { display:inline-flex;align-items:center;gap:4px;padding:3px 9px;border-radius:99px;font-size:11px;font-weight:600; }
.badge.g { background:#dcfce7;color:#16a34a; }
.badge.r { background:#fee2e2;color:#dc2626; }
.badge.o { background:#f5e9c9;color:#8a6e2f; }
.bdot { width:5px;height:5px;border-radius:50%;background:currentColor; }
</style>

<div style="padding:24px 32px 48px">

    {{-- BREADCRUMB + ACTIONS --}}
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px">
        <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:#6b7280">
            <a href="{{ route('admin.paiements.index') }}" style="color:#6b7280;text-decoration:none">Paiements</a>
            <svg style="width:12px;height:12px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            <span style="color:#0d1117;font-weight:500">{{ $paiement->reference_paiement }}</span>
        </div>
    </div>

    {{-- ACTIONS BAR --}}
    <div class="actions-bar">
        @if($paiement->statut === 'valide')
            <a href="{{ route('admin.paiements.pdf', $paiement) }}" target="_blank" class="btn-action btn-dark">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                Télécharger la quittance PDF
            </a>
        @endif

        <a href="{{ route('admin.contrats.show', $paiement->contrat) }}" class="btn-action btn-outline">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            Voir le contrat
        </a>

        <a href="{{ route('admin.paiements.create', ['contrat_id' => $paiement->contrat_id]) }}" class="btn-action btn-outline">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Nouveau paiement (même contrat)
        </a>

        @if($paiement->statut === 'valide')
            <form method="POST" action="{{ route('admin.paiements.annuler', $paiement) }}"
                  onsubmit="return confirm('Annuler ce paiement ? Cette action est irréversible.')">
                @csrf @method('PATCH')
                <button type="submit" class="btn-action btn-danger">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                    Annuler le paiement
                </button>
            </form>
        @endif
    </div>

    <div class="page-grid">

        {{-- COLONNE GAUCHE --}}
        <div>

            {{-- HERO --}}
            <div class="paiement-hero">
                @php
                    $sl = match($paiement->statut) {
                        'valide'     => ['g', 'Validé'],
                        'annule'     => ['r', 'Annulé'],
                        'en_attente' => ['o', 'En attente'],
                        default      => ['o', ucfirst($paiement->statut)],
                    };
                @endphp
                <div style="display:flex;align-items:flex-start;justify-content:space-between;position:relative;z-index:1">
                    <div>
                        <div class="hero-ref">{{ $paiement->reference_paiement }}</div>
                        <div class="hero-amount">
                            {{ number_format($paiement->montant_encaisse, 0, ',', ' ') }}<span class="hero-amount-u">FCFA</span>
                        </div>
                        <div class="hero-periode">
                            Loyer de {{ \Carbon\Carbon::parse($paiement->periode)->translatedFormat('F Y') }}
                            · Payé le {{ \Carbon\Carbon::parse($paiement->date_paiement)->format('d/m/Y') }}
                        </div>
                    </div>
                    <span class="status-badge {{ $sl[0] }}">
                        <span class="status-dot"></span>
                        {{ $sl[1] }}
                    </span>
                </div>

                <div class="hero-bottom">
                    <div class="hero-stat">
                        <div class="hero-stat-lbl">Net propriétaire</div>
                        <div class="hero-stat-val green">{{ number_format($paiement->net_proprietaire, 0, ',', ' ') }} F</div>
                    </div>
                    <div class="hero-divider"></div>
                    <div class="hero-stat">
                        <div class="hero-stat-lbl">Commission TTC</div>
                        <div class="hero-stat-val gold">{{ number_format($paiement->commission_ttc, 0, ',', ' ') }} F</div>
                    </div>
                    <div class="hero-divider"></div>
                    <div class="hero-stat">
                        <div class="hero-stat-lbl">Mode de paiement</div>
                        <div class="hero-stat-val">{{ \App\Models\Paiement::MODES_PAIEMENT[$paiement->mode_paiement] ?? $paiement->mode_paiement }}</div>
                    </div>
                    <div class="hero-divider"></div>
                    <div class="hero-stat">
                        <div class="hero-stat-lbl">Taux commission</div>
                        <div class="hero-stat-val">{{ $paiement->taux_commission_applique ?? '—' }}%</div>
                    </div>
                </div>
            </div>

            {{-- DÉCOMPTE FINANCIER --}}
            <div class="card">
                <div class="card-hd">
                    <div class="card-title">Décompte financier ventilé</div>
                    <span style="font-size:11px;color:#9ca3af">{{ \Carbon\Carbon::parse($paiement->periode)->translatedFormat('F Y') }}</span>
                </div>
                <div class="card-body" style="padding:16px 20px">
                    <table class="decompte-table">

                        {{-- Loyer nu --}}
                        <tr>
                            <td>Loyer nu mensuel <span style="font-size:11px;color:#9ca3af">(hors charges et TOM)</span></td>
                            <td>{{ number_format($paiement->loyer_nu ?? $paiement->montant_encaisse, 0, ',', ' ') }} F</td>
                        </tr>

                        {{-- Charges --}}
                        @if(($paiement->charges_amount ?? 0) > 0)
                        <tr>
                            <td style="color:#6b7280;font-size:12px;padding-left:12px">+ Charges locatives récupérables</td>
                            <td style="color:#6b7280">{{ number_format($paiement->charges_amount, 0, ',', ' ') }} F</td>
                        </tr>
                        @endif

                        {{-- TOM --}}
                        @if(($paiement->tom_amount ?? 0) > 0)
                        <tr>
                            <td style="color:#6b7280;font-size:12px;padding-left:12px">+ TOM (Taxe Ordures Ménagères)</td>
                            <td style="color:#6b7280">{{ number_format($paiement->tom_amount, 0, ',', ' ') }} F</td>
                        </tr>
                        @endif

                        {{-- Séparateur commission --}}
                        <tr class="decompte-sep">
                            <td colspan="2">Commission d'agence — mandat de gestion locative</td>
                        </tr>

                        {{-- Commission HT --}}
                        <tr>
                            <td style="color:#6b7280;font-size:12px;padding-left:12px">
                                Commission HT ({{ $paiement->taux_commission_applique ?? 0 }}% sur loyer nu)
                            </td>
                            <td style="color:#6b7280">{{ number_format($paiement->commission_agence, 0, ',', ' ') }} F</td>
                        </tr>

                        {{-- TVA --}}
                        <tr>
                            <td style="color:#6b7280;font-size:12px;padding-left:24px">↳ TVA sur commission (18%)</td>
                            <td style="color:#6b7280">{{ number_format($paiement->tva_commission, 0, ',', ' ') }} F</td>
                        </tr>

                        {{-- Commission TTC --}}
                        <tr>
                            <td style="font-weight:500">Commission TTC (HT + TVA)</td>
                            <td style="color:#8a6e2f">{{ number_format($paiement->commission_ttc, 0, ',', ' ') }} F</td>
                        </tr>

                        {{-- Séparateur net --}}
                        <tr class="decompte-sep" style="background:#dcfce7">
                            <td colspan="2" style="color:#16a34a">Reversement propriétaire</td>
                        </tr>

                        {{-- Net propriétaire --}}
                        <tr class="decompte-total">
                            <td>NET REVERSÉ AU PROPRIÉTAIRE</td>
                            <td>{{ number_format($paiement->net_proprietaire, 0, ',', ' ') }} F</td>
                        </tr>
                    </table>

                    {{-- Caution premier paiement --}}
                    @if($paiement->est_premier_paiement && ($paiement->caution_percue ?? 0) > 0)
                    <div style="margin-top:14px;padding:12px 14px;background:#fffbeb;border:1px solid #fde68a;border-radius:9px">
                        <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#d97706;margin-bottom:4px">Premier paiement — Caution perçue</div>
                        <div style="font-family:'Syne',sans-serif;font-size:16px;font-weight:700;color:#92400e">{{ number_format($paiement->caution_percue, 0, ',', ' ') }} FCFA</div>
                        <div style="font-size:11px;color:#d97706;margin-top:2px">Restituable en fin de bail</div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- INFOS BIEN & LOCATAIRE --}}
            <div class="card">
                <div class="card-hd">
                    <div class="card-title">Bien loué</div>
                    <a href="{{ route('biens.show', $paiement->contrat->bien) }}" style="font-size:12px;color:#6b7280;text-decoration:none">Voir la fiche →</a>
                </div>
                <div class="card-body">
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-lbl">Référence</div>
                            <div class="info-val">{{ $paiement->contrat->bien->reference }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-lbl">Type</div>
                            <div class="info-val">{{ \App\Models\Bien::TYPES[$paiement->contrat->bien->type] ?? $paiement->contrat->bien->type }}</div>
                        </div>
                        <div class="info-item" style="grid-column:span 2">
                            <div class="info-lbl">Adresse</div>
                            <div class="info-val">{{ $paiement->contrat->bien->adresse }}{{ $paiement->contrat->bien->ville ? ', '.$paiement->contrat->bien->ville : '' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-lbl">Propriétaire</div>
                            <div class="info-val">{{ $paiement->contrat->bien->proprietaire->name }}</div>
                            <div class="info-sub">{{ $paiement->contrat->bien->proprietaire->telephone ?? '' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-lbl">Locataire</div>
                            <div class="info-val">{{ $paiement->contrat->locataire->name }}</div>
                            <div class="info-sub">{{ $paiement->contrat->locataire->email }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- NOTES --}}
            @if($paiement->notes)
            <div class="card">
                <div class="card-hd"><div class="card-title">Notes</div></div>
                <div class="card-body">
                    <div class="notes-box">{{ $paiement->notes }}</div>
                </div>
            </div>
            @endif

        </div>{{-- fin colonne gauche --}}

        {{-- COLONNE DROITE : APERÇU QUITTANCE --}}
        <div>
            <div class="quittance-preview">

                {{-- En-tête quittance --}}
                <div class="quittance-header">
                    <div class="quittance-agency">{{ $currentAgency->name ?? 'Agence' }}</div>
                    <div class="quittance-agency-sub">Gestion immobilière · Sénégal</div>
                    <div class="quittance-title-row">
                        <div class="quittance-doc-title">Quittance de loyer</div>
                        <div class="quittance-ref">{{ $paiement->reference_paiement }}</div>
                    </div>
                </div>

                <div class="quittance-body">

                    {{-- Période --}}
                    <div style="text-align:center;padding:10px 0 14px;border-bottom:1px solid #f3f4f6;margin-bottom:14px">
                        <div style="font-size:10px;color:#9ca3af;text-transform:uppercase;letter-spacing:1px;margin-bottom:4px">Période</div>
                        <div style="font-family:'Syne',sans-serif;font-size:16px;font-weight:700;color:#0d1117;text-transform:uppercase">
                            {{ \Carbon\Carbon::parse($paiement->periode)->translatedFormat('F Y') }}
                        </div>
                    </div>

                    {{-- Parties --}}
                    <div class="q-parties">
                        <div class="q-party">
                            <div class="q-party-role">Bailleur</div>
                            <div class="q-party-name">{{ $paiement->contrat->bien->proprietaire->name }}</div>
                            <div class="q-party-sub">Propriétaire</div>
                        </div>
                        <div class="q-party">
                            <div class="q-party-role">Locataire</div>
                            <div class="q-party-name">{{ $paiement->contrat->locataire->name }}</div>
                            <div class="q-party-sub">{{ $paiement->contrat->locataire->telephone ?? '—' }}</div>
                        </div>
                    </div>

                    {{-- Bien --}}
                    <div class="q-section">
                        <div class="q-section-title">Bien loué</div>
                        <div class="q-row">
                            <div class="q-row-lbl">Référence</div>
                            <div class="q-row-val">{{ $paiement->contrat->bien->reference }}</div>
                        </div>
                        <div class="q-row">
                            <div class="q-row-lbl">Adresse</div>
                            <div class="q-row-val" style="font-size:10px;max-width:140px;text-align:right">{{ $paiement->contrat->bien->adresse }}, {{ $paiement->contrat->bien->ville }}</div>
                        </div>
                    </div>

                    {{-- Décompte --}}
                    <div class="q-section">
                        <div class="q-section-title">Décompte financier</div>
                        <div class="q-row">
                            <div class="q-row-lbl">Loyer nu</div>
                            <div class="q-row-val">{{ number_format($paiement->loyer_nu ?? $paiement->montant_encaisse, 0, ',', ' ') }} F</div>
                        </div>
                        @if(($paiement->charges_amount ?? 0) > 0)
                        <div class="q-row">
                            <div class="q-row-lbl">Charges</div>
                            <div class="q-row-val">{{ number_format($paiement->charges_amount, 0, ',', ' ') }} F</div>
                        </div>
                        @endif
                        @if(($paiement->tom_amount ?? 0) > 0)
                        <div class="q-row">
                            <div class="q-row-lbl">TOM</div>
                            <div class="q-row-val">{{ number_format($paiement->tom_amount, 0, ',', ' ') }} F</div>
                        </div>
                        @endif
                        <div class="q-row">
                            <div class="q-row-lbl">Commission HT ({{ $paiement->taux_commission_applique }}%)</div>
                            <div class="q-row-val gold">− {{ number_format($paiement->commission_agence, 0, ',', ' ') }} F</div>
                        </div>
                        <div class="q-row">
                            <div class="q-row-lbl">TVA (18%)</div>
                            <div class="q-row-val gold">− {{ number_format($paiement->tva_commission, 0, ',', ' ') }} F</div>
                        </div>
                    </div>

                    {{-- Net --}}
                    <div class="q-total">
                        <div class="q-total-lbl">Net propriétaire</div>
                        <div class="q-total-val">{{ number_format($paiement->net_proprietaire, 0, ',', ' ') }} F</div>
                    </div>

                    {{-- Modalités --}}
                    <div class="q-section" style="margin-top:14px">
                        <div class="q-section-title">Modalités</div>
                        <div class="q-row">
                            <div class="q-row-lbl">Mode</div>
                            <div class="q-row-val">{{ \App\Models\Paiement::MODES_PAIEMENT[$paiement->mode_paiement] ?? $paiement->mode_paiement }}</div>
                        </div>
                        <div class="q-row">
                            <div class="q-row-lbl">Date</div>
                            <div class="q-row-val">{{ \Carbon\Carbon::parse($paiement->date_paiement)->format('d/m/Y') }}</div>
                        </div>
                        <div class="q-row">
                            <div class="q-row-lbl">Statut</div>
                            <div class="q-row-val">
                                @if($paiement->statut === 'valide')
                                    <span class="badge g"><span class="bdot"></span>Validé</span>
                                @elseif($paiement->statut === 'annule')
                                    <span class="badge r"><span class="bdot"></span>Annulé</span>
                                @else
                                    <span class="badge o"><span class="bdot"></span>{{ ucfirst($paiement->statut) }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                </div>{{-- /quittance-body --}}

                <div class="q-footer">
                    <div class="q-footer-text">
                        Document généré par {{ $currentAgency->name ?? 'BimoTech Immo' }}.
                        Conforme à la loi n° 81-18 du 25 juin 1981 sur les baux au Sénégal.
                        Réf. bail : {{ $paiement->reference_bail ?? $paiement->contrat->reference_bail ?? '—' }}
                    </div>

                    @if($paiement->statut === 'valide')
                    <a href="{{ route('admin.paiements.pdf', $paiement) }}" target="_blank"
                       style="display:flex;align-items:center;justify-content:center;gap:6px;margin-top:12px;padding:10px;background:#0d1117;color:#fff;border-radius:8px;font-size:12px;font-weight:500;text-decoration:none;transition:opacity .15s">
                        <svg style="width:14px;height:14px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        Télécharger la quittance PDF
                    </a>
                    @endif
                </div>

            </div>{{-- /quittance-preview --}}
        </div>{{-- fin colonne droite --}}

    </div>{{-- /page-grid --}}

</div>

</x-app-layout>