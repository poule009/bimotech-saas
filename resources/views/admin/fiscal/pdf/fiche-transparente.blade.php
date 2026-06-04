<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'DejaVu Sans', sans-serif; font-size: 9pt; color: #1a2e3b; background: #fff; }
    .page { padding: 20mm 18mm; }

    /* Header */
    .header { display: flex; justify-content: space-between; align-items: flex-start; padding-bottom: 12px; border-bottom: 2px solid #1B4F6B; margin-bottom: 14px; }
    .header-agency h1 { font-size: 14pt; font-weight: bold; color: #1B4F6B; }
    .header-agency p { font-size: 7.5pt; color: #666; margin-top: 2px; }
    .header-doc { text-align: right; }
    .header-doc h2 { font-size: 11pt; font-weight: bold; color: #1B4F6B; }
    .header-doc p { font-size: 7.5pt; color: #666; margin-top: 2px; }

    /* Bandeau propriétaire */
    .bandeau { background: #f0ede6; border: 1px solid #ddd; border-radius: 6px; padding: 10px 14px; margin-bottom: 14px; display: flex; justify-content: space-between; }
    .bandeau-col h3 { font-size: 9pt; font-weight: bold; color: #1B4F6B; margin-bottom: 4px; }
    .bandeau-col p { font-size: 8pt; color: #555; line-height: 1.5; }
    .badge-calcule { background: #C9A84C22; border: 1px solid #C9A84C; color: #9a7a30; font-size: 7pt; padding: 2px 8px; border-radius: 99px; display: inline-block; margin-top: 4px; }

    /* Étape */
    .etape { margin-bottom: 14px; }
    .etape-header { background: #1B4F6B; color: #fff; padding: 5px 10px; border-radius: 4px 4px 0 0; font-size: 8.5pt; font-weight: bold; display: flex; justify-content: space-between; align-items: center; }
    .etape-header span.num { background: #C9A84C; color: #1B4F6B; font-size: 7pt; font-weight: bold; padding: 1px 6px; border-radius: 99px; }
    .etape-body { border: 1px solid #1B4F6B22; border-top: none; border-radius: 0 0 4px 4px; overflow: hidden; }
    .etape-row { display: flex; justify-content: space-between; padding: 6px 10px; border-bottom: 1px solid #f0ede6; }
    .etape-row:last-child { border-bottom: none; }
    .etape-row.total { background: #1B4F6B08; font-weight: bold; }
    .etape-row.highlight { background: #C9A84C0d; }
    .etape-row label { color: #555; font-size: 8pt; flex: 1; }
    .etape-row .ref { font-size: 7pt; color: #999; margin-left: 6px; }
    .etape-row .val { font-weight: bold; font-size: 8.5pt; text-align: right; min-width: 90px; }
    .etape-row .val.pos { color: #1B4F6B; }
    .etape-row .val.neg { color: #555; }
    .etape-row .val.gold { color: #9a7a30; }

    /* Table paiements */
    table { width: 100%; border-collapse: collapse; font-size: 7.5pt; }
    th { background: #f0ede6; padding: 5px 6px; text-align: left; font-weight: bold; color: #555; border-bottom: 1px solid #ddd; font-size: 7pt; text-transform: uppercase; letter-spacing: 0.05em; }
    td { padding: 5px 6px; border-bottom: 1px solid #f5f5f5; color: #333; }
    tr:last-child td { border-bottom: none; }
    .tr-total td { background: #1B4F6B08; font-weight: bold; border-top: 1px solid #1B4F6B22; }
    .ta-r { text-align: right; }
    .ta-c { text-align: center; }

    /* Comparaison régimes */
    .regimes-grid { display: flex; gap: 10px; }
    .regime-card { flex: 1; border-radius: 6px; padding: 10px; }
    .regime-card.recommande { background: #C9A84C11; border: 2px solid #C9A84C; }
    .regime-card.non-recommande { background: #f5f5f5; border: 1px solid #ddd; }
    .regime-card h4 { font-size: 8pt; font-weight: bold; margin-bottom: 4px; }
    .regime-card .montant { font-size: 12pt; font-weight: bold; margin: 4px 0; }
    .badge-rec { background: #C9A84C; color: #fff; font-size: 6.5pt; padding: 2px 6px; border-radius: 99px; display: inline-block; }

    /* Disclaimer */
    .disclaimer { margin-top: 14px; padding: 8px 12px; background: #f9f8f6; border-left: 3px solid #C9A84C; border-radius: 0 4px 4px 0; }
    .disclaimer p { font-size: 7pt; color: #888; line-height: 1.5; }

    /* Footer */
    .footer { margin-top: 16px; padding-top: 10px; border-top: 1px solid #eee; display: flex; justify-content: space-between; font-size: 7pt; color: #aaa; }
    .sign-zone { margin-top: 14px; display: flex; gap: 20px; }
    .sign-box { flex: 1; border: 1px dashed #bbb; border-radius: 4px; padding: 12px; min-height: 60px; }
    .sign-box p { font-size: 7.5pt; color: #999; margin-bottom: 4px; }
</style>
</head>
<body>
<div class="page">

    {{-- Header --}}
    <div class="header">
        <div class="header-agency">
            <h1>{{ $agency->name }}</h1>
            <p>{{ $agency->adresse ?? '' }}{{ $agency->ville ? ' — '.$agency->ville : '' }}</p>
            @if($agency->ninea)<p>NINEA : {{ $agency->ninea }}</p>@endif
        </div>
        <div class="header-doc">
            <h2>Fiche Fiscale Transparente</h2>
            <p>Année {{ $annee }}</p>
            <p>Générée le {{ now()->format('d/m/Y') }}</p>
            <p style="color:#888;font-size:7pt">Document confidentiel — Usage comptable</p>
        </div>
    </div>

    {{-- Bandeau propriétaire --}}
    <div class="bandeau">
        <div class="bandeau-col">
            <h3>{{ $proprietaire->name }}</h3>
            <p>{{ $proprietaire->email ?? '' }}</p>
            @if($proprietaire->telephone ?? $proprietaire->profile?->telephone)<p>Tél : {{ $proprietaire->telephone ?? $proprietaire->profile?->telephone }}</p>@endif
            @if($proprietaire->profile?->ninea ?? null)<p>NINEA : {{ $proprietaire->profile->ninea }}</p>@endif
        </div>
        <div class="bandeau-col" style="text-align:right">
            <p>{{ $bilan->nb_biens_geres }} bien(s) géré(s)</p>
            <p>{{ $bilan->nb_paiements }} paiement(s) validés</p>
            <span class="badge-calcule">Calculé le {{ $bilan->calcule_le?->format('d/m/Y') ?? '—' }}</span>
        </div>
    </div>

    {{-- ÉTAPE 1 — Revenus bruts --}}
    <div class="etape">
        <div class="etape-header">
            <span><span class="num">1</span> &nbsp; Revenus bruts encaissés</span>
            <span style="font-size:7pt;font-weight:normal">Art. 173 CGI SN</span>
        </div>
        <div class="etape-body">
            <div class="etape-row">
                <label>Loyers encaissés (HT)</label>
                <span class="val pos">{{ number_format($bilan->revenus_bruts_loyers, 0, ',', ' ') }} F</span>
            </div>
            @if($bilan->revenus_bruts_charges > 0)
            <div class="etape-row">
                <label>Charges récupérées</label>
                <span class="val pos">{{ number_format($bilan->revenus_bruts_charges, 0, ',', ' ') }} F</span>
            </div>
            @endif
            <div class="etape-row total">
                <label>Total revenus bruts</label>
                <span class="val pos">{{ number_format($bilan->revenus_bruts_total, 0, ',', ' ') }} F</span>
            </div>
        </div>
    </div>

    {{-- ÉTAPE 2 — Déductions agence --}}
    <div class="etape">
        <div class="etape-header">
            <span><span class="num">2</span> &nbsp; Déductions agence</span>
        </div>
        <div class="etape-body">
            <div class="etape-row">
                <label>Commissions HT <span class="ref">(Art. 36 CGI)</span></label>
                <span class="val neg">-{{ number_format($bilan->commissions_agence_ht, 0, ',', ' ') }} F</span>
            </div>
            @if($bilan->brs_retenu_total > 0)
            <div class="etape-row">
                <label>BRS retenu <span class="ref">(Art. 201 §3 CGI — 5%)</span></label>
                <span class="val neg">-{{ number_format($bilan->brs_retenu_total, 0, ',', ' ') }} F</span>
            </div>
            @endif
            @if($bilan->tom_total > 0)
            <div class="etape-row">
                <label>TOM collectée</label>
                <span class="val neg">-{{ number_format($bilan->tom_total, 0, ',', ' ') }} F</span>
            </div>
            @endif
            <div class="etape-row total">
                <label>Net reversé au propriétaire</label>
                <span class="val pos">{{ number_format($bilan->net_a_verser_total, 0, ',', ' ') }} F</span>
            </div>
        </div>
    </div>

    {{-- ÉTAPE 3 — Base fiscale --}}
    <div class="etape">
        <div class="etape-header">
            <span><span class="num">3</span> &nbsp; Calcul de la base imposable</span>
            <span style="font-size:7pt;font-weight:normal">Abattement 30% — Art. 173 CGI SN</span>
        </div>
        <div class="etape-body">
            <div class="etape-row">
                <label>Revenus bruts totaux</label>
                <span class="val pos">{{ number_format($bilan->revenus_bruts_total, 0, ',', ' ') }} F</span>
            </div>
            <div class="etape-row">
                <label>Abattement forfaitaire 30%</label>
                <span class="val neg">-{{ number_format($bilan->abattement_forfaitaire_30, 0, ',', ' ') }} F</span>
            </div>
            <div class="etape-row total highlight">
                <label>Base imposable (70% des revenus)</label>
                <span class="val gold">{{ number_format($bilan->base_imposable, 0, ',', ' ') }} F</span>
            </div>
        </div>
    </div>

    {{-- ÉTAPE 4 — IRPP par tranches --}}
    <div class="etape">
        <div class="etape-header">
            <span><span class="num">4</span> &nbsp; Calcul IRPP — Barème progressif</span>
            <span style="font-size:7pt;font-weight:normal">Art. 65-68 CGI SN</span>
        </div>
        <div class="etape-body">
            <table>
                <thead>
                    <tr>
                        <th>Tranche de revenus</th>
                        <th class="ta-c">Taux</th>
                        <th class="ta-r">Base taxée</th>
                        <th class="ta-r">Impôt / tranche</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($irppDetail as $t)
                    @if($t['assiette'] > 0)
                    <tr>
                        <td>{{ number_format($t['min'], 0, ',', ' ') }} → {{ $t['max'] ? number_format($t['max'], 0, ',', ' ') : '∞' }} F</td>
                        <td class="ta-c">{{ $t['taux'] }}%</td>
                        <td class="ta-r">{{ number_format($t['assiette'], 0, ',', ' ') }} F</td>
                        <td class="ta-r" style="font-weight:bold">{{ number_format($t['impot'], 0, ',', ' ') }} F</td>
                    </tr>
                    @endif
                    @endforeach
                    <tr class="tr-total">
                        <td colspan="3">Total IRPP estimé</td>
                        <td class="ta-r" style="color:#1B4F6B">{{ number_format($bilan->irpp_estime, 0, ',', ' ') }} F</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- ÉTAPE 5 — Comparaison régimes --}}
    <div class="etape">
        <div class="etape-header">
            <span><span class="num">5</span> &nbsp; Comparaison régimes — CGF vs IRPP</span>
        </div>
        <div class="etape-body" style="padding:10px">
            @if($cgfData['applicable'])
            <div class="regimes-grid">
                <div class="regime-card {{ $regimes['regime_recommande'] === 'cgf' ? 'recommande' : 'non-recommande' }}">
                    <h4 style="color:{{ $regimes['regime_recommande'] === 'cgf' ? '#9a7a30' : '#555' }}">Contribution Globale Foncière (CGF)</h4>
                    <div class="montant" style="color:{{ $regimes['regime_recommande'] === 'cgf' ? '#9a7a30' : '#333' }}">{{ number_format($cgfData['montant'], 0, ',', ' ') }} F</div>
                    <div style="font-size:7.5pt;color:#888">Taux : {{ $cgfData['taux_applique'] }}% — {{ $cgfData['tranche_label'] ?? '' }}</div>
                    @if($regimes['regime_recommande'] === 'cgf')<span class="badge-rec">✓ RECOMMANDÉ</span>@endif
                </div>
                <div class="regime-card {{ $regimes['regime_recommande'] === 'irpp' ? 'recommande' : 'non-recommande' }}">
                    <h4 style="color:{{ $regimes['regime_recommande'] === 'irpp' ? '#9a7a30' : '#555' }}">IRPP — Barème progressif</h4>
                    <div class="montant" style="color:{{ $regimes['regime_recommande'] === 'irpp' ? '#9a7a30' : '#333' }}">{{ number_format($bilan->irpp_estime, 0, ',', ' ') }} F</div>
                    <div style="font-size:7.5pt;color:#888">Art. 65-68 CGI SN</div>
                    @if($regimes['regime_recommande'] === 'irpp')<span class="badge-rec">✓ RECOMMANDÉ</span>@endif
                </div>
            </div>
            @if($regimes['economie_potentielle'] > 0)
            <p style="margin-top:8px;font-size:8pt;color:#9a7a30;text-align:center">Économie potentielle en optant pour le régime recommandé : <strong>{{ number_format($regimes['economie_potentielle'], 0, ',', ' ') }} F</strong></p>
            @endif
            @else
            <p style="font-size:8pt;color:#888;text-align:center">Revenus > 30 000 000 F — Régime CGF non applicable. IRPP barème progressif obligatoire.</p>
            @endif
        </div>
    </div>

    {{-- ÉTAPE 6 — Obligations TVA & BRS --}}
    <div class="etape">
        <div class="etape-header">
            <span><span class="num">6</span> &nbsp; Obligations fiscales collectées</span>
        </div>
        <div class="etape-body">
            @if($bilan->tva_loyer_collectee > 0)
            <div class="etape-row">
                <label>TVA loyers collectée (18%) <span class="ref">Art. 369 CGI</span></label>
                <span class="val pos">{{ number_format($bilan->tva_loyer_collectee, 0, ',', ' ') }} F</span>
            </div>
            @endif
            @if($bilan->tva_charges_total > 0)
            <div class="etape-row">
                <label>TVA charges collectée</label>
                <span class="val pos">{{ number_format($bilan->tva_charges_total, 0, ',', ' ') }} F</span>
            </div>
            @endif
            @if($bilan->brs_retenu_total > 0)
            <div class="etape-row">
                <label>BRS retenu et versé DGI <span class="ref">Art. 201 §3</span></label>
                <span class="val pos">{{ number_format($bilan->brs_retenu_total, 0, ',', ' ') }} F</span>
            </div>
            @endif
        </div>
    </div>

    {{-- ÉTAPE 7 — Détail paiements --}}
    @if($paiements->count() > 0)
    <div class="etape">
        <div class="etape-header">
            <span><span class="num">7</span> &nbsp; Détail des paiements {{ $annee }}</span>
        </div>
        <div class="etape-body">
            <table>
                <thead>
                    <tr>
                        <th>Période</th>
                        <th>Bien</th>
                        <th>Type bail</th>
                        <th class="ta-r">Loyer HT</th>
                        <th class="ta-r">TVA</th>
                        <th class="ta-r">Commission</th>
                        <th class="ta-r">BRS</th>
                        <th class="ta-r">Net proprio</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($paiements as $p)
                    <tr>
                        <td>{{ $p->periode?->format('m/Y') ?? '—' }}</td>
                        <td>{{ $p->bien_reference ?? $p->contrat?->bien?->reference ?? '—' }}</td>
                        <td style="text-transform:capitalize">{{ $p->type_bail ?? '—' }}</td>
                        <td class="ta-r">{{ number_format($p->loyer_ht, 0, ',', ' ') }}</td>
                        <td class="ta-r">{{ number_format($p->tva_loyer ?? 0, 0, ',', ' ') }}</td>
                        <td class="ta-r">{{ number_format($p->commission_ttc, 0, ',', ' ') }}</td>
                        <td class="ta-r">{{ number_format($p->brs_amount ?? 0, 0, ',', ' ') }}</td>
                        <td class="ta-r" style="font-weight:bold">{{ number_format($p->net_a_verser_proprietaire ?? 0, 0, ',', ' ') }}</td>
                    </tr>
                    @endforeach
                    <tr class="tr-total">
                        <td colspan="3">Totaux</td>
                        <td class="ta-r">{{ number_format($paiements->sum('loyer_ht'), 0, ',', ' ') }}</td>
                        <td class="ta-r">{{ number_format($paiements->sum('tva_loyer'), 0, ',', ' ') }}</td>
                        <td class="ta-r">{{ number_format($paiements->sum('commission_ttc'), 0, ',', ' ') }}</td>
                        <td class="ta-r">{{ number_format($paiements->sum('brs_amount'), 0, ',', ' ') }}</td>
                        <td class="ta-r">{{ number_format($paiements->sum('net_a_verser_proprietaire'), 0, ',', ' ') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Zones de signature --}}
    <div class="sign-zone">
        <div class="sign-box">
            <p>Signature du directeur d'agence</p>
            <p style="margin-top:30px;font-size:7pt;color:#bbb">{{ $agency->name }}</p>
        </div>
        <div class="sign-box">
            <p>Validation expert-comptable</p>
            <p style="margin-top:4px;font-size:7pt;color:#bbb">Date :</p>
        </div>
        <div class="sign-box">
            <p>Cachet / Tampon</p>
        </div>
    </div>

    {{-- Disclaimer --}}
    <div class="disclaimer" style="margin-top:12px">
        <p>Document généré à titre indicatif par {{ $agency->name }} le {{ now()->format('d/m/Y') }}. Les montants d'IRPP et de CGF sont des <strong>estimations</strong> basées sur les données disponibles. Ce document ne constitue pas un avis fiscal officiel et doit être validé par un expert-comptable agréé. Références légales : CGI Sénégal — Art. 65-68 (IRPP), Art. 88-95 (CGF), Art. 173 (abattement), Art. 201 §3 (BRS), Art. 369 (TVA).</p>
    </div>

    <div class="footer">
        <span>{{ $agency->name }} — {{ $agency->email ?? '' }}</span>
        <span>Fiche fiscale {{ $proprietaire->name }} — {{ $annee }}</span>
    </div>

</div>
</body>
</html>
