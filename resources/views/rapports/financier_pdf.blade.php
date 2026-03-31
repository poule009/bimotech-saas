<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Rapport Financier — {{ $debutMois->translatedFormat('F Y') }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10px;
            color: #1f2937;
            background: #fff;
        }

        /* ── En-tête ── */
        .header {
            background: #1a3c5e;
            color: #fff;
            padding: 18px 24px;
            margin-bottom: 20px;
        }
        .header-inner {
            display: table;
            width: 100%;
        }
        .header-left  { display: table-cell; vertical-align: middle; }
        .header-right { display: table-cell; vertical-align: middle; text-align: right; }
        .header h1    { font-size: 18px; font-weight: 700; letter-spacing: -0.3px; }
        .header h2    { font-size: 13px; font-weight: 400; opacity: .85; margin-top: 3px; }
        .header .agency-name { font-size: 11px; opacity: .7; margin-top: 6px; }
        .header .periode {
            font-size: 22px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .header .generated {
            font-size: 9px;
            opacity: .65;
            margin-top: 4px;
        }

        /* ── KPI Cards ── */
        .kpi-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
            border-collapse: separate;
            border-spacing: 6px;
        }
        .kpi-cell { display: table-cell; width: 16.66%; }
        .kpi-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 10px 12px;
            text-align: center;
        }
        .kpi-card .kpi-label {
            font-size: 8px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: #64748b;
            margin-bottom: 5px;
        }
        .kpi-card .kpi-value {
            font-size: 13px;
            font-weight: 700;
            color: #1a3c5e;
        }
        .kpi-card .kpi-sub {
            font-size: 8px;
            color: #94a3b8;
            margin-top: 2px;
        }
        .kpi-card.highlight {
            background: #1a3c5e;
            border-color: #1a3c5e;
        }
        .kpi-card.highlight .kpi-label { color: #93c5fd; }
        .kpi-card.highlight .kpi-value { color: #fff; font-size: 15px; }
        .kpi-card.highlight .kpi-sub   { color: #93c5fd; }

        /* ── Sections ── */
        .section-title {
            font-size: 11px;
            font-weight: 700;
            color: #1a3c5e;
            text-transform: uppercase;
            letter-spacing: .5px;
            border-bottom: 2px solid #1a3c5e;
            padding-bottom: 5px;
            margin-bottom: 12px;
            margin-top: 20px;
        }

        /* ── Tables ── */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
        }
        thead th {
            background: #1a3c5e;
            color: #fff;
            padding: 6px 8px;
            text-align: left;
            font-weight: 700;
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: .3px;
        }
        thead th.right { text-align: right; }
        tbody tr:nth-child(even) { background: #f8fafc; }
        tbody tr:hover { background: #eff6ff; }
        tbody td {
            padding: 5px 8px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: middle;
        }
        tbody td.right { text-align: right; font-variant-numeric: tabular-nums; }
        tfoot td {
            padding: 6px 8px;
            font-weight: 700;
            background: #f1f5f9;
            border-top: 2px solid #1a3c5e;
        }
        tfoot td.right { text-align: right; }

        /* ── Badges ── */
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 10px;
            font-size: 8px;
            font-weight: 700;
        }
        .badge-green  { background: #dcfce7; color: #15803d; }
        .badge-orange { background: #fef3c7; color: #d97706; }
        .badge-red    { background: #fee2e2; color: #dc2626; }
        .badge-blue   { background: #dbeafe; color: #1d4ed8; }

        /* ── Résumé par propriétaire ── */
        .proprio-table thead th { background: #374151; }

        /* ── Impayés ── */
        .impaye-table thead th { background: #dc2626; }

        /* ── Pied de page ── */
        .footer {
            margin-top: 30px;
            padding-top: 12px;
            border-top: 1px solid #e2e8f0;
            display: table;
            width: 100%;
        }
        .footer-left  { display: table-cell; font-size: 8px; color: #94a3b8; }
        .footer-right { display: table-cell; text-align: right; font-size: 8px; color: #94a3b8; }

        /* ── Utilitaires ── */
        .text-right  { text-align: right; }
        .text-center { text-align: center; }
        .text-muted  { color: #94a3b8; }
        .font-bold   { font-weight: 700; }
        .text-green  { color: #16a34a; }
        .text-red    { color: #dc2626; }
        .text-blue   { color: #1d4ed8; }
        .mt-4        { margin-top: 16px; }
        .page-break  { page-break-after: always; }
    </style>
</head>
<body>

    {{-- ═══════════════════════════════════════════════════════════════
         EN-TÊTE
    ═══════════════════════════════════════════════════════════════ --}}
    <div class="header">
        <div class="header-inner">
            <div class="header-left">
                <div class="h1" style="font-size:18px;font-weight:700;color:#fff;">
                    {{ $agency->name ?? 'BimoTech Immo' }}
                </div>
                <div class="h2" style="font-size:11px;color:#93c5fd;margin-top:3px;">
                    Rapport Financier Mensuel
                </div>
                @if($agency->adresse ?? false)
                    <div class="agency-name" style="font-size:9px;color:#bfdbfe;margin-top:4px;">
                        {{ $agency->adresse }}
                        @if($agency->telephone) · {{ $agency->telephone }} @endif
                    </div>
                @endif
                @if($agency->ninea ?? false)
                    <div style="font-size:9px;color:#bfdbfe;margin-top:2px;">NINEA : {{ $agency->ninea }}</div>
                @endif
            </div>
            <div class="header-right">
                <div class="periode" style="font-size:20px;font-weight:700;color:#fff;text-transform:uppercase;">
                    {{ $debutMois->translatedFormat('F Y') }}
                </div>
                <div class="generated" style="font-size:9px;color:#93c5fd;margin-top:4px;">
                    Généré le {{ now()->translatedFormat('d F Y à H:i') }}
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════
         KPI DU MOIS
    ═══════════════════════════════════════════════════════════════ --}}
    <div class="section-title">📊 Indicateurs du mois</div>

    <table style="border-collapse:separate;border-spacing:6px;width:100%;margin-bottom:16px;">
        <tr>
            <td style="width:16.66%;background:#f8fafc;border:1px solid #e2e8f0;border-radius:6px;padding:10px 12px;text-align:center;">
                <div style="font-size:8px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#64748b;margin-bottom:4px;">Loyers encaissés</div>
                <div style="font-size:14px;font-weight:700;color:#1a3c5e;">{{ number_format($kpiMois['total_loyers'], 0, ',', ' ') }}</div>
                <div style="font-size:8px;color:#94a3b8;margin-top:2px;">FCFA</div>
            </td>
            <td style="width:16.66%;background:#f8fafc;border:1px solid #e2e8f0;border-radius:6px;padding:10px 12px;text-align:center;">
                <div style="font-size:8px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#64748b;margin-bottom:4px;">Commission HT</div>
                <div style="font-size:14px;font-weight:700;color:#1a3c5e;">{{ number_format($kpiMois['total_commission'], 0, ',', ' ') }}</div>
                <div style="font-size:8px;color:#94a3b8;margin-top:2px;">FCFA</div>
            </td>
            <td style="width:16.66%;background:#f8fafc;border:1px solid #e2e8f0;border-radius:6px;padding:10px 12px;text-align:center;">
                <div style="font-size:8px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#64748b;margin-bottom:4px;">TVA (18%)</div>
                <div style="font-size:14px;font-weight:700;color:#1a3c5e;">{{ number_format($kpiMois['total_tva'], 0, ',', ' ') }}</div>
                <div style="font-size:8px;color:#94a3b8;margin-top:2px;">FCFA</div>
            </td>
            <td style="width:16.66%;background:#1a3c5e;border:1px solid #1a3c5e;border-radius:6px;padding:10px 12px;text-align:center;">
                <div style="font-size:8px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#93c5fd;margin-bottom:4px;">Commission TTC</div>
                <div style="font-size:16px;font-weight:700;color:#fff;">{{ number_format($kpiMois['total_ttc'], 0, ',', ' ') }}</div>
                <div style="font-size:8px;color:#93c5fd;margin-top:2px;">FCFA</div>
            </td>
            <td style="width:16.66%;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:6px;padding:10px 12px;text-align:center;">
                <div style="font-size:8px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#15803d;margin-bottom:4px;">Net propriétaires</div>
                <div style="font-size:14px;font-weight:700;color:#15803d;">{{ number_format($kpiMois['total_net_proprio'], 0, ',', ' ') }}</div>
                <div style="font-size:8px;color:#16a34a;margin-top:2px;">FCFA</div>
            </td>
            <td style="width:16.66%;background:#f8fafc;border:1px solid #e2e8f0;border-radius:6px;padding:10px 12px;text-align:center;">
                <div style="font-size:8px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#64748b;margin-bottom:4px;">Paiements</div>
                <div style="font-size:14px;font-weight:700;color:#1a3c5e;">{{ $kpiMois['nb_paiements'] }}</div>
                <div style="font-size:8px;color:#94a3b8;margin-top:2px;">transactions</div>
            </td>
        </tr>
    </table>

    {{-- Stats générales --}}
    <table style="width:100%;border-collapse:collapse;margin-bottom:20px;">
        <tr>
            <td style="padding:6px 10px;background:#f1f5f9;border:1px solid #e2e8f0;font-size:9px;">
                🏠 <strong>{{ $statsGenerales['nb_biens'] }}</strong> biens
                (<strong>{{ $statsGenerales['nb_biens_loues'] }}</strong> loués —
                taux d'occupation : <strong>{{ $statsGenerales['taux_occupation'] }}%</strong>)
            </td>
            <td style="padding:6px 10px;background:#f1f5f9;border:1px solid #e2e8f0;font-size:9px;">
                📋 <strong>{{ $statsGenerales['nb_contrats'] }}</strong> contrats actifs
            </td>
            <td style="padding:6px 10px;background:#f1f5f9;border:1px solid #e2e8f0;font-size:9px;">
                👥 <strong>{{ $statsGenerales['nb_proprietaires'] }}</strong> propriétaires ·
                <strong>{{ $statsGenerales['nb_locataires'] }}</strong> locataires
            </td>
        </tr>
    </table>

    {{-- ═══════════════════════════════════════════════════════════════
         DÉTAIL DES PAIEMENTS
    ═══════════════════════════════════════════════════════════════ --}}
    <div class="section-title">💳 Détail des paiements du mois</div>

    @if($paiementsMois->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>Référence</th>
                    <th>Date</th>
                    <th>Bien</th>
                    <th>Locataire</th>
                    <th>Propriétaire</th>
                    <th>Mode</th>
                    <th class="right">Loyer</th>
                    <th class="right">Comm. HT</th>
                    <th class="right">TVA</th>
                    <th class="right">Comm. TTC</th>
                    <th class="right">Net proprio</th>
                </tr>
            </thead>
            <tbody>
                @foreach($paiementsMois as $p)
                    <tr>
                        <td style="font-family:monospace;font-size:8px;color:#6b7280;">{{ $p->reference_paiement }}</td>
                        <td>{{ $p->date_paiement->format('d/m/Y') }}</td>
                        <td>
                            <strong>{{ $p->contrat?->bien?->reference }}</strong><br>
                            <span style="color:#6b7280;font-size:8px;">{{ $p->contrat?->bien?->adresse }}</span>
                        </td>
                        <td>{{ $p->contrat?->locataire?->name }}</td>
                        <td>{{ $p->contrat?->bien?->proprietaire?->name }}</td>
                        <td>
                            @php
                                $modes = ['especes'=>'Espèces','virement'=>'Virement','cheque'=>'Chèque','wave'=>'Wave','orange_money'=>'Orange Money','free_money'=>'Free Money','mobile_money'=>'Mobile'];
                            @endphp
                            {{ $modes[$p->mode_paiement] ?? $p->mode_paiement }}
                        </td>
                        <td class="right">{{ number_format($p->montant_encaisse, 0, ',', ' ') }}</td>
                        <td class="right">{{ number_format($p->commission_agence, 0, ',', ' ') }}</td>
                        <td class="right">{{ number_format($p->tva_commission, 0, ',', ' ') }}</td>
                        <td class="right font-bold">{{ number_format($p->commission_ttc, 0, ',', ' ') }}</td>
                        <td class="right text-green">{{ number_format($p->net_proprietaire, 0, ',', ' ') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="6" class="font-bold">TOTAL</td>
                    <td class="right font-bold">{{ number_format($kpiMois['total_loyers'], 0, ',', ' ') }}</td>
                    <td class="right font-bold">{{ number_format($kpiMois['total_commission'], 0, ',', ' ') }}</td>
                    <td class="right font-bold">{{ number_format($kpiMois['total_tva'], 0, ',', ' ') }}</td>
                    <td class="right font-bold">{{ number_format($kpiMois['total_ttc'], 0, ',', ' ') }}</td>
                    <td class="right font-bold text-green">{{ number_format($kpiMois['total_net_proprio'], 0, ',', ' ') }}</td>
                </tr>
            </tfoot>
        </table>
    @else
        <p style="text-align:center;color:#94a3b8;padding:20px;font-style:italic;">
            Aucun paiement validé pour ce mois.
        </p>
    @endif

    {{-- ═══════════════════════════════════════════════════════════════
         PAR PROPRIÉTAIRE
    ═══════════════════════════════════════════════════════════════ --}}
    @if($parProprietaire->count() > 0)
        <div class="section-title mt-4">👤 Récapitulatif par propriétaire</div>

        <table class="proprio-table">
            <thead>
                <tr>
                    <th>Propriétaire</th>
                    <th class="right">Nb paiements</th>
                    <th class="right">Total loyers</th>
                    <th class="right">Commission TTC</th>
                    <th class="right">Net à reverser</th>
                </tr>
            </thead>
            <tbody>
                @foreach($parProprietaire as $item)
                    <tr>
                        <td><strong>{{ $item['proprio']->name }}</strong></td>
                        <td class="right">{{ $item['nb_paiements'] }}</td>
                        <td class="right">{{ number_format($item['loyers'], 0, ',', ' ') }} FCFA</td>
                        <td class="right">{{ number_format($item['commission'], 0, ',', ' ') }} FCFA</td>
                        <td class="right font-bold text-green">{{ number_format($item['net'], 0, ',', ' ') }} FCFA</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td class="font-bold">TOTAL</td>
                    <td class="right font-bold">{{ $parProprietaire->sum('nb_paiements') }}</td>
                    <td class="right font-bold">{{ number_format($parProprietaire->sum('loyers'), 0, ',', ' ') }} FCFA</td>
                    <td class="right font-bold">{{ number_format($parProprietaire->sum('commission'), 0, ',', ' ') }} FCFA</td>
                    <td class="right font-bold text-green">{{ number_format($parProprietaire->sum('net'), 0, ',', ' ') }} FCFA</td>
                </tr>
            </tfoot>
        </table>
    @endif

    {{-- ═══════════════════════════════════════════════════════════════
         BIENS IMPAYÉS
    ═══════════════════════════════════════════════════════════════ --}}
    @if($biensImpayés->count() > 0)
        <div class="section-title mt-4" style="color:#dc2626;border-color:#dc2626;">
            ⚠️ Biens sans paiement ce mois ({{ $biensImpayés->count() }})
        </div>

        <table class="impaye-table">
            <thead>
                <tr>
                    <th>Référence bien</th>
                    <th>Adresse</th>
                    <th>Locataire</th>
                    <th class="right">Loyer contractuel</th>
                    <th>Début contrat</th>
                </tr>
            </thead>
            <tbody>
                @foreach($biensImpayés as $contrat)
                    <tr>
                        <td><strong>{{ $contrat->bien?->reference }}</strong></td>
                        <td>{{ $contrat->bien?->adresse }}</td>
                        <td>{{ $contrat->locataire?->name }}</td>
                        <td class="right">{{ number_format($contrat->loyer_contractuel, 0, ',', ' ') }} FCFA</td>
                        <td>{{ $contrat->date_debut->format('d/m/Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    {{-- ═══════════════════════════════════════════════════════════════
         PIED DE PAGE
    ═══════════════════════════════════════════════════════════════ --}}
    <div class="footer">
        <div class="footer-left">
            <strong>{{ $agency->name ?? 'BimoTech Immo' }}</strong>
            @if($agency->email ?? false) · {{ $agency->email }} @endif
            @if($agency->ninea ?? false) · NINEA : {{ $agency->ninea }} @endif
        </div>
        <div class="footer-right">
            Document confidentiel — Rapport généré automatiquement par BimoTech Immo
        </div>
    </div>

</body>
</html>
