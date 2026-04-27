<x-app-layout>
    <x-slot name="header">Bilan fiscal — {{ $proprietaire->name }}</x-slot>

<style>
/* ── Layout ── */
.page { padding:24px 32px 48px; }
.page-grid { display:grid; grid-template-columns:1fr 300px; gap:24px; align-items:start; }
.sidebar-sticky { position:sticky; top:80px; }

/* ── Cards ── */
.card { background:#fff; border:1px solid #e5e7eb; border-radius:14px; overflow:hidden; margin-bottom:18px; }
.card:last-child { margin-bottom:0; }
.card-hd { padding:14px 20px; border-bottom:1px solid #e5e7eb; display:flex; align-items:center; justify-content:space-between; }
.card-title { font-family:'Syne',sans-serif; font-size:13px; font-weight:700; color:#0d1117; display:flex; align-items:center; gap:8px; }
.card-body { padding:18px 20px; }

/* ── KPI grid ── */
.kpi-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:14px; margin-bottom:22px; }
.kpi { background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:16px 18px; position:relative; overflow:hidden; }
.kpi::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; border-radius:12px 12px 0 0; }
.kpi.gold::before  { background:#c9a84c; }
.kpi.green::before { background:#16a34a; }
.kpi.red::before   { background:#dc2626; }
.kpi.blue::before  { background:#1d4ed8; }
.kpi.orange::before{ background:#d97706; }
.kpi.purple::before{ background:#7c3aed; }
.kpi-lbl { font-size:10px; font-weight:600; text-transform:uppercase; letter-spacing:.8px; color:#6b7280; margin-bottom:6px; }
.kpi-val { font-family:'Syne',sans-serif; font-size:20px; font-weight:800; color:#0d1117; line-height:1.1; }
.kpi-u   { font-size:11px; font-weight:400; color:#9ca3af; margin-left:2px; }
.kpi-sub { font-size:11px; color:#9ca3af; margin-top:4px; }

/* ── Barème IRPP ── */
.bareme-table { width:100%; border-collapse:collapse; font-size:12px; }
.bareme-table th { background:#f9fafb; padding:8px 14px; text-align:left; font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:#6b7280; border-bottom:1px solid #e5e7eb; }
.bareme-table td { padding:10px 14px; border-bottom:1px solid #f3f4f6; color:#374151; vertical-align:middle; }
.bareme-table tr:last-child td { border-bottom:none; }
.bareme-table tr.active td { background:#f0fdf4; font-weight:600; color:#16a34a; }
.bareme-table tr.inactive td { color:#9ca3af; }
.bareme-bar { height:6px; background:#e5e7eb; border-radius:3px; overflow:hidden; }
.bareme-bar-fill { height:100%; border-radius:3px; background:#16a34a; }

/* ── Table paiements ── */
.dt { width:100%; border-collapse:collapse; font-size:12px; }
.dt th { padding:8px 14px; font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:#9ca3af; background:#f9fafb; border-bottom:1px solid #e5e7eb; text-align:left; }
.dt th.r { text-align:right; }
.dt td { padding:10px 14px; border-bottom:1px solid #f3f4f6; color:#374151; vertical-align:middle; }
.dt td.r { text-align:right; font-family:'Syne',sans-serif; font-weight:600; }
.dt tbody tr:hover { background:#f9fafb; }
.dt tbody tr:last-child td { border-bottom:none; }

/* ── Badges ── */
.badge { display:inline-flex; align-items:center; gap:4px; padding:3px 9px; border-radius:99px; font-size:10px; font-weight:700; }
.badge-green  { background:#dcfce7; color:#16a34a; }
.badge-orange { background:#fef3c7; color:#d97706; }
.badge-red    { background:#fee2e2; color:#dc2626; }
.badge-blue   { background:#dbeafe; color:#1d4ed8; }

/* ── Sidebar ── */
.proprio-card { background:#0d1117; border-radius:14px; padding:20px; }
.proprio-av { width:52px; height:52px; border-radius:12px; background:#c9a84c; display:flex; align-items:center; justify-content:center; font-family:'Syne',sans-serif; font-size:20px; font-weight:800; color:#fff; margin:0 auto 12px; }
.proprio-name { font-family:'Syne',sans-serif; font-size:15px; font-weight:700; color:#fff; text-align:center; }
.proprio-email { font-size:11px; color:rgba(255,255,255,.4); text-align:center; margin-top:3px; }
.meta-row { display:flex; justify-content:space-between; align-items:center; padding:8px 0; border-bottom:1px solid rgba(255,255,255,.06); font-size:12px; }
.meta-row:last-child { border-bottom:none; }
.meta-lbl { color:rgba(255,255,255,.4); }
.meta-val { color:rgba(255,255,255,.75); font-weight:600; }

/* ── Calcul visuel ── */
.calc-bloc { background:#f9fafb; border:1px solid #e5e7eb; border-radius:12px; padding:18px 20px; }
.calc-row { display:flex; justify-content:space-between; align-items:center; padding:9px 0; border-bottom:1px solid #f3f4f6; font-size:13px; }
.calc-row:last-child { border-bottom:none; }
.calc-lbl { color:#374151; }
.calc-val { font-family:'Syne',sans-serif; font-weight:700; color:#0d1117; }
.calc-row.total { border-top:2px solid #c9a84c; border-bottom:none; margin-top:4px; padding-top:12px; }
.calc-row.total .calc-lbl { font-weight:700; font-size:14px; }
.calc-row.total .calc-val { font-size:16px; color:#c9a84c; }
.calc-row.sub { padding:6px 0 6px 16px; border-bottom:1px dashed #f3f4f6; }
.calc-row.sub .calc-lbl { font-size:12px; color:#9ca3af; }
.calc-row.sub .calc-val { font-size:12px; color:#6b7280; }
.calc-row.highlight { background:#f0fdf4; border-radius:8px; padding:10px 12px; margin:6px -12px; border-bottom:none; }
.calc-row.highlight .calc-lbl { color:#16a34a; font-weight:600; }
.calc-row.highlight .calc-val { color:#16a34a; font-size:15px; }
.calc-row.warning { background:#fff1f2; border-radius:8px; padding:10px 12px; margin:6px -12px; border-bottom:none; }
.calc-row.warning .calc-lbl { color:#dc2626; font-weight:600; }
.calc-row.warning .calc-val { color:#dc2626; font-size:15px; }
</style>

<div class="page">

    {{-- BREADCRUMB --}}
    <div style="display:flex;align-items:center;gap:8px;font-size:12px;color:#6b7280;margin-bottom:16px">
        <a href="{{ route('admin.bilans-fiscaux.index') }}" style="color:#6b7280;text-decoration:none">Bilans fiscaux</a>
        <svg style="width:12px;height:12px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
        <span style="color:#0d1117;font-weight:500">{{ $proprietaire->name }} — {{ $annee }}</span>
    </div>

    {{-- HEADER --}}
    <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:22px">
        <div>
            <h1 style="font-family:'Syne',sans-serif;font-size:22px;font-weight:700;color:#0d1117;letter-spacing:-.4px">
                Bilan fiscal {{ $annee }} — {{ $proprietaire->name }}
            </h1>
            <p style="font-size:13px;color:#6b7280;margin-top:3px">
                Calculé le {{ $bilan->calcule_le->format('d/m/Y à H:i') }} ·
                {{ $bilan->nb_paiements }} paiement(s) · {{ $bilan->nb_biens_geres }} bien(s)
            </p>
        </div>
        <div style="display:flex;align-items:center;gap:10px">
            {{-- Recalculer --}}
            <form method="POST" action="{{ route('admin.bilans-fiscaux.calculate', $proprietaire) }}">
                @csrf
                <input type="hidden" name="annee" value="{{ $annee }}">
                <button type="submit"
                        style="display:inline-flex;align-items:center;gap:6px;padding:9px 16px;background:#fff;border:1px solid #e5e7eb;border-radius:9px;font-size:12px;font-weight:500;font-family:'DM Sans',sans-serif;color:#374151;cursor:pointer;transition:all .15s"
                        onmouseover="this.style.borderColor='#c9a84c';this.style.color='#8a6e2f'"
                        onmouseout="this.style.borderColor='#e5e7eb';this.style.color='#374151'">
                    <svg style="width:13px;height:13px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 11-2.12-9.36L23 10"/></svg>
                    Recalculer
                </button>
            </form>
            {{-- Export PDF --}}
            <a href="{{ route('admin.bilans-fiscaux.pdf', [$proprietaire, 'annee' => $annee]) }}"
               target="_blank"
               style="display:inline-flex;align-items:center;gap:6px;padding:9px 16px;background:#0d1117;color:#fff;border:none;border-radius:9px;font-size:12px;font-weight:600;font-family:'DM Sans',sans-serif;text-decoration:none;transition:opacity .15s"
               onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                <svg style="width:13px;height:13px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                Exporter PDF DGI
            </a>
        </div>
    </div>

    {{-- FILTRE ANNÉE --}}
    <form method="GET" style="margin-bottom:20px">
        <div style="display:inline-flex;align-items:center;gap:8px;background:#fff;border:1px solid #e5e7eb;border-radius:10px;padding:10px 14px">
            <span style="font-size:12px;color:#6b7280;font-weight:500">Année :</span>
            <select name="annee" onchange="this.form.submit()"
                    style="border:none;outline:none;font-size:13px;font-family:'DM Sans',sans-serif;color:#0d1117;background:transparent;cursor:pointer">
                @foreach($anneesDisponibles as $a)
                    <option value="{{ $a }}" {{ $annee == $a ? 'selected':'' }}>{{ $a }}</option>
                @endforeach
            </select>
        </div>
    </form>

    <div class="page-grid">
        <div>

            {{-- KPI --}}
            <div class="kpi-grid">
                <div class="kpi gold">
                    <div class="kpi-lbl">Revenus bruts loyers</div>
                    <div class="kpi-val">{{ number_format($bilan->revenus_bruts_loyers, 0, ',', ' ') }}<span class="kpi-u">F</span></div>
                    <div class="kpi-sub">Loyers HT annuels</div>
                </div>
                <div class="kpi green">
                    <div class="kpi-lbl">Base imposable</div>
                    <div class="kpi-val">{{ number_format($bilan->base_imposable, 0, ',', ' ') }}<span class="kpi-u">F</span></div>
                    <div class="kpi-sub">Après abattement 30%</div>
                </div>
                <div class="kpi red">
                    <div class="kpi-lbl">IRPP estimé</div>
                    <div class="kpi-val">{{ number_format($bilan->irpp_estime, 0, ',', ' ') }}<span class="kpi-u">F</span></div>
                    <div class="kpi-sub">Art. 65 CGI SN</div>
                </div>
                <div class="kpi blue">
                    <div class="kpi-lbl">Net reversé</div>
                    <div class="kpi-val">{{ number_format($bilan->net_proprietaire_total, 0, ',', ' ') }}<span class="kpi-u">F</span></div>
                    <div class="kpi-sub">Après commissions</div>
                </div>
            </div>

            {{-- CALCUL DÉTAILLÉ --}}
            <div class="card">
                <div class="card-hd">
                    <div class="card-title">
                        <svg style="width:15px;height:15px;color:#c9a84c" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
                        Calcul fiscal détaillé
                    </div>
                    <span class="badge badge-green">Art. 58-65 CGI SN</span>
                </div>
                <div class="card-body">
                    <div class="calc-bloc">
                        {{-- Revenus --}}
                        <div class="calc-row">
                            <div class="calc-lbl">Loyers HT perçus</div>
                            <div class="calc-val">{{ number_format($bilan->revenus_bruts_loyers, 0, ',', ' ') }} F</div>
                        </div>
                        @if($bilan->revenus_bruts_charges > 0)
                        <div class="calc-row sub">
                            <div class="calc-lbl">+ Charges refacturées</div>
                            <div class="calc-val">{{ number_format($bilan->revenus_bruts_charges, 0, ',', ' ') }} F</div>
                        </div>
                        @endif

                        {{-- Abattement --}}
                        <div class="calc-row" style="margin-top:6px">
                            <div class="calc-lbl">
                                Abattement forfaitaire 30%
                                <span style="font-size:11px;color:#9ca3af;font-weight:400;margin-left:4px">Art. 58 CGI SN</span>
                            </div>
                            <div class="calc-val" style="color:#16a34a">− {{ number_format($bilan->abattement_forfaitaire_30, 0, ',', ' ') }} F</div>
                        </div>

                        {{-- Base imposable --}}
                        <div class="calc-row total">
                            <div class="calc-lbl">= Base imposable (70%)</div>
                            <div class="calc-val">{{ number_format($bilan->base_imposable, 0, ',', ' ') }} F</div>
                        </div>

                        {{-- IRPP --}}
                        <div style="margin-top:16px;margin-bottom:8px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#6b7280">
                            Barème IRPP progressif (Art. 65 CGI SN)
                        </div>

                        @php
                            $base = (float) $bilan->base_imposable;
                            $tranches = [
                                ['label' => '0 — 1 500 000 F',         'taux' => 0,   'min' => 0,         'max' => 1500000],
                                ['label' => '1 500 001 — 4 000 000 F', 'taux' => 20,  'min' => 1500001,   'max' => 4000000],
                                ['label' => '4 000 001 — 8 000 000 F', 'taux' => 30,  'min' => 4000001,   'max' => 8000000],
                                ['label' => '> 8 000 000 F',           'taux' => 40,  'min' => 8000001,   'max' => PHP_INT_MAX],
                            ];
                        @endphp

                        <table class="bareme-table" style="margin-bottom:12px">
                            <tr>
                                <th>Tranche</th>
                                <th>Taux</th>
                                <th>Impôt sur tranche</th>
                                <th style="width:100px">Progression</th>
                            </tr>
                            @foreach($tranches as $t)
                            @php
                                $active = $base > $t['min'];
                                $imposable = $active ? min($base, $t['max']) - $t['min'] : 0;
                                $imposable = max(0, $imposable);
                                $impot = round($imposable * $t['taux'] / 100, 0);
                                $pct = $t['max'] === PHP_INT_MAX ? ($base > $t['min'] ? 100 : 0) : ($base >= $t['max'] ? 100 : max(0, ($base - $t['min']) / ($t['max'] - $t['min']) * 100));
                            @endphp
                            <tr class="{{ $active ? 'active' : 'inactive' }}">
                                <td>{{ $t['label'] }}</td>
                                <td>{{ $t['taux'] }}%</td>
                                <td>{{ $active && $impot > 0 ? number_format($impot, 0, ',', ' ').' F' : '—' }}</td>
                                <td>
                                    <div class="bareme-bar">
                                        <div class="bareme-bar-fill" style="width:{{ $pct }}%;background:{{ $active ? '#16a34a' : '#e5e7eb' }}"></div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </table>

                        {{-- IRPP total --}}
                        <div class="calc-row warning">
                            <div class="calc-lbl">= IRPP estimé à déclarer</div>
                            <div class="calc-val">{{ number_format($bilan->irpp_estime, 0, ',', ' ') }} F</div>
                        </div>

                        {{-- CFPB --}}
                        <div class="calc-row" style="margin-top:12px">
                            <div class="calc-lbl">
                                CFPB estimée
                                <span style="font-size:11px;color:#9ca3af;font-weight:400;margin-left:4px">Art. 95-110 CGI SN — ~5% valeur locative</span>
                            </div>
                            <div class="calc-val" style="color:#d97706">{{ number_format($bilan->cfpb_estimee, 0, ',', ' ') }} F</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TVA ET BRS --}}
            @if($bilan->tva_loyer_collectee > 0 || $bilan->brs_retenu_total > 0)
            <div class="card">
                <div class="card-hd">
                    <div class="card-title">
                        <svg style="width:15px;height:15px;color:#d97706" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        Obligations TVA & BRS
                    </div>
                </div>
                <div class="card-body">
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
                        @if($bilan->tva_loyer_collectee > 0)
                        <div style="background:#fff7ed;border:1px solid #fed7aa;border-radius:10px;padding:14px">
                            <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#d97706;margin-bottom:8px">TVA loyer collectée</div>
                            <div style="font-family:'Syne',sans-serif;font-size:22px;font-weight:800;color:#d97706">
                                {{ number_format($bilan->tva_loyer_collectee, 0, ',', ' ') }} F
                            </div>
                            <div style="font-size:11px;color:#9a3412;margin-top:6px;line-height:1.5">
                                TVA 18% sur loyers commerciaux/meublés.<br>
                                <strong>À reverser à la DGI</strong> par le propriétaire.
                            </div>
                        </div>
                        @endif
                        @if($bilan->brs_retenu_total > 0)
                        <div style="background:#fff1f2;border:1px solid #fecaca;border-radius:10px;padding:14px">
                            <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#dc2626;margin-bottom:8px">BRS retenu</div>
                            <div style="font-family:'Syne',sans-serif;font-size:22px;font-weight:800;color:#dc2626">
                                {{ number_format($bilan->brs_retenu_total, 0, ',', ' ') }} F
                            </div>
                            <div style="font-size:11px;color:#9f1239;margin-top:6px;line-height:1.5">
                                Retenu par les locataires entreprises.<br>
                                <strong>Déjà versé à la DGI</strong> par les locataires.
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            {{-- COMMISSIONS --}}
            <div class="card">
                <div class="card-hd">
                    <div class="card-title">
                        <svg style="width:15px;height:15px;color:#8a6e2f" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v16"/></svg>
                        Commissions agence {{ $annee }}
                    </div>
                </div>
                <div class="card-body">
                    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px">
                        <div style="text-align:center;padding:14px;background:#fdf8ef;border-radius:10px;border:1px solid #f5e9c9">
                            <div style="font-size:10px;color:#8a6e2f;font-weight:600;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px">Commission HT</div>
                            <div style="font-family:'Syne',sans-serif;font-size:18px;font-weight:700;color:#8a6e2f">{{ number_format($bilan->commissions_agence_ht, 0, ',', ' ') }} F</div>
                        </div>
                        <div style="text-align:center;padding:14px;background:#f9fafb;border-radius:10px;border:1px solid #e5e7eb">
                            <div style="font-size:10px;color:#6b7280;font-weight:600;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px">TVA commissions</div>
                            <div style="font-family:'Syne',sans-serif;font-size:18px;font-weight:700;color:#6b7280">{{ number_format($bilan->tva_commissions, 0, ',', ' ') }} F</div>
                        </div>
                        <div style="text-align:center;padding:14px;background:#f0fdf4;border-radius:10px;border:1px solid #bbf7d0">
                            <div style="font-size:10px;color:#16a34a;font-weight:600;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px">Net propriétaire</div>
                            <div style="font-family:'Syne',sans-serif;font-size:18px;font-weight:700;color:#16a34a">{{ number_format($bilan->net_proprietaire_total, 0, ',', ' ') }} F</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TABLEAU PAIEMENTS --}}
            <div class="card">
                <div class="card-hd">
                    <div class="card-title">
                        <svg style="width:15px;height:15px;color:#6b7280" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="5" y="2" width="14" height="20" rx="2"/><line x1="9" y1="7" x2="15" y2="7"/><line x1="9" y1="12" x2="15" y2="12"/><line x1="9" y1="17" x2="13" y2="17"/></svg>
                        Paiements {{ $annee }} ({{ $paiements->count() }})
                    </div>
                </div>
                <div style="overflow-x:auto">
                    <table class="dt">
                        <thead>
                            <tr>
                                <th>Période</th>
                                <th>Bien</th>
                                <th>Locataire</th>
                                <th>Type bail</th>
                                <th class="r">Loyer HT</th>
                                <th class="r">TVA loyer</th>
                                <th class="r">BRS</th>
                                <th class="r">Net proprio</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($paiements as $p)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($p->periode)->translatedFormat('M Y') }}</td>
                                <td>
                                    <div style="font-weight:600;color:#0d1117">{{ $p->bien_reference ?? '—' }}</div>
                                    <div style="font-size:11px;color:#9ca3af">
                                        {{ ucfirst($p->type_bail ?? '—') }}{{ ($p->type_bail === 'habitation' && $p->bien_meuble) ? ' meublée' : '' }}
                                    </div>
                                </td>
                                <td style="font-size:12px;color:#6b7280">{{ $p->contrat?->locataire?->name ?? '—' }}</td>
                                <td>
                                    @if(($p->tva_loyer ?? 0) > 0)
                                        <span class="badge badge-orange">TVA 18%</span>
                                    @else
                                        <span class="badge" style="background:#f3f4f6;color:#6b7280">Exonéré</span>
                                    @endif
                                </td>
                                <td class="r">{{ number_format($p->loyer_ht ?? $p->loyer_nu ?? 0, 0, ',', ' ') }} F</td>
                                <td class="r" style="{{ ($p->tva_loyer ?? 0) > 0 ? 'color:#d97706' : 'color:#9ca3af' }}">
                                    {{ ($p->tva_loyer ?? 0) > 0 ? number_format($p->tva_loyer, 0, ',', ' ').' F' : '—' }}
                                </td>
                                <td class="r" style="{{ ($p->brs_amount ?? 0) > 0 ? 'color:#dc2626' : 'color:#9ca3af' }}">
                                    {{ ($p->brs_amount ?? 0) > 0 ? number_format($p->brs_amount, 0, ',', ' ').' F' : '—' }}
                                </td>
                                <td class="r" style="color:#16a34a">{{ number_format($p->net_proprietaire ?? 0, 0, ',', ' ') }} F</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" style="padding:32px;text-align:center;color:#9ca3af">
                                    Aucun paiement enregistré pour {{ $annee }}
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                        @if($paiements->count() > 0)
                        <tfoot>
                            <tr style="background:#f9fafb;font-weight:700">
                                <td colspan="4" style="padding:10px 14px;font-size:12px;color:#374151">TOTAL {{ $annee }}</td>
                                <td class="r" style="padding:10px 14px;font-family:'Syne',sans-serif">{{ number_format($paiements->sum(fn($p) => $p->loyer_ht ?? $p->loyer_nu ?? 0), 0, ',', ' ') }} F</td>
                                <td class="r" style="padding:10px 14px;color:#d97706;font-family:'Syne',sans-serif">{{ number_format($paiements->sum('tva_loyer'), 0, ',', ' ') }} F</td>
                                <td class="r" style="padding:10px 14px;color:#dc2626;font-family:'Syne',sans-serif">{{ number_format($paiements->sum('brs_amount'), 0, ',', ' ') }} F</td>
                                <td class="r" style="padding:10px 14px;color:#16a34a;font-family:'Syne',sans-serif">{{ number_format($paiements->sum('net_proprietaire'), 0, ',', ' ') }} F</td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>

            {{-- AVERTISSEMENT --}}
            <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:10px;padding:14px 18px;font-size:12px;color:#92400e;line-height:1.7">
                <strong>⚠ Estimation fiscale</strong> — Ce bilan est calculé sur la base des paiements enregistrés dans BimoTech.
                L'IRPP est une estimation selon le barème Art. 65 CGI SN. La CFPB est estimée à 5% de la valeur locative.
                <strong>Consultez un comptable agréé ou la DGI avant toute déclaration officielle.</strong>
            </div>

        </div>

        {{-- SIDEBAR --}}
        <div class="sidebar-sticky">

            {{-- Carte propriétaire --}}
            <div class="proprio-card" style="margin-bottom:16px">
                <div class="proprio-av">{{ strtoupper(substr($proprietaire->name, 0, 2)) }}</div>
                <div class="proprio-name">{{ $proprietaire->name }}</div>
                <div class="proprio-email">{{ $proprietaire->email }}</div>
                <div style="height:1px;background:rgba(255,255,255,.08);margin:14px 0"></div>
                <div class="meta-row">
                    <div class="meta-lbl">NINEA</div>
                    <div class="meta-val">{{ $proprietaire->proprietaire?->ninea ?? '—' }}</div>
                </div>
                <div class="meta-row">
                    <div class="meta-lbl">Tél</div>
                    <div class="meta-val">{{ $proprietaire->telephone ?? '—' }}</div>
                </div>
                <div class="meta-row">
                    <div class="meta-lbl">Biens</div>
                    <div class="meta-val">{{ $bilan->nb_biens_geres }}</div>
                </div>
                <div class="meta-row">
                    <div class="meta-lbl">Paiements</div>
                    <div class="meta-val">{{ $bilan->nb_paiements }}</div>
                </div>
            </div>

            {{-- Résumé fiscal --}}
            <div class="card">
                <div style="padding:14px 18px;border-bottom:1px solid #e5e7eb">
                    <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.8px;color:#9ca3af">Résumé fiscal {{ $annee }}</div>
                </div>
                <div style="padding:14px 18px">
                    @php
                        $rows = [
                            ['Revenus bruts',    $bilan->revenus_bruts_loyers,      '#0d1117'],
                            ['Abattement 30%',   -$bilan->abattement_forfaitaire_30, '#16a34a'],
                            ['Base imposable',   $bilan->base_imposable,             '#0d1117'],
                            ['IRPP estimé',      $bilan->irpp_estime,               '#dc2626'],
                            ['CFPB estimée',     $bilan->cfpb_estimee,              '#d97706'],
                        ];
                        if($bilan->tva_loyer_collectee > 0)
                            $rows[] = ['TVA à reverser', $bilan->tva_loyer_collectee, '#d97706'];
                        if($bilan->brs_retenu_total > 0)
                            $rows[] = ['BRS retenu', $bilan->brs_retenu_total, '#dc2626'];
                    @endphp
                    @foreach($rows as [$lbl, $val, $clr])
                    <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid #f3f4f6;font-size:12px">
                        <span style="color:#6b7280">{{ $lbl }}</span>
                        <span style="font-family:'Syne',sans-serif;font-weight:700;color:{{ $clr }}">
                            {{ ($val < 0 ? '−' : '') }}{{ number_format(abs($val), 0, ',', ' ') }} F
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Actions --}}
            <div class="card" style="margin-top:14px">
                <div style="padding:14px 18px">
                    <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.8px;color:#9ca3af;margin-bottom:10px">Actions</div>
                    <div style="display:flex;flex-direction:column;gap:7px">
                        <a href="{{ route('admin.users.show', $proprietaire) }}"
                           style="display:flex;align-items:center;gap:8px;padding:8px 12px;border:1px solid #e5e7eb;border-radius:8px;font-size:12px;color:#374151;text-decoration:none;transition:all .15s"
                           onmouseover="this.style.borderColor='#c9a84c'" onmouseout="this.style.borderColor='#e5e7eb'">
                            <svg style="width:12px;height:12px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            Fiche propriétaire
                        </a>
                        <a href="{{ route('admin.bilans-fiscaux.index') }}"
                           style="display:flex;align-items:center;gap:8px;padding:8px 12px;border:1px solid #e5e7eb;border-radius:8px;font-size:12px;color:#374151;text-decoration:none;transition:all .15s"
                           onmouseover="this.style.borderColor='#c9a84c'" onmouseout="this.style.borderColor='#e5e7eb'">
                            <svg style="width:12px;height:12px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                            Tous les bilans
                        </a>
                        @foreach($anneesDisponibles->reject(fn($a) => $a == $annee)->take(3) as $autreAnnee)
                        <a href="{{ route('admin.bilans-fiscaux.show', [$proprietaire, 'annee' => $autreAnnee]) }}"
                           style="display:flex;align-items:center;gap:8px;padding:8px 12px;border:1px solid #e5e7eb;border-radius:8px;font-size:12px;color:#374151;text-decoration:none;transition:all .15s"
                           onmouseover="this.style.borderColor='#c9a84c'" onmouseout="this.style.borderColor='#e5e7eb'">
                            <svg style="width:12px;height:12px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/></svg>
                            Bilan {{ $autreAnnee }}
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>
</x-app-layout>