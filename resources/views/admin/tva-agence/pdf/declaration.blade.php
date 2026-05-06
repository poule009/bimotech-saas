<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family:"DejaVu Sans",Arial,sans-serif; font-size:10px; color:#1a202c; background:#fff; line-height:1.5; }

/* En-tête */
.header { background:#0d1117; padding:22px 28px; }
.header-table { display:table; width:100%; }
.header-left  { display:table-cell; width:60%; vertical-align:middle; }
.header-right { display:table-cell; width:40%; vertical-align:middle; text-align:right; }
.agence-nom   { font-size:17px; font-weight:bold; color:#fff; margin-bottom:3px; }
.agence-sub   { font-size:8px; color:rgba(255,255,255,.4); text-transform:uppercase; letter-spacing:1.5px; }
.agence-ninea { font-size:8.5px; color:#c9a84c; font-weight:bold; margin-top:4px; }
.doc-title    { font-size:14px; font-weight:bold; color:#fff; margin-bottom:2px; }
.doc-sub      { font-size:8.5px; color:rgba(255,255,255,.5); }
.doc-periode  { font-size:20px; font-weight:bold; color:#c9a84c; margin-top:4px; }

/* Bandeau statut */
.statut-band { display:table; width:100%; background:#f9fafb; border-bottom:3px solid #c9a84c; padding:10px 28px; }
.statut-left  { display:table-cell; width:60%; vertical-align:middle; }
.statut-right { display:table-cell; width:40%; vertical-align:middle; text-align:right; }
.statut-label { font-size:7.5px; text-transform:uppercase; letter-spacing:.8px; color:#9ca3af; margin-bottom:2px; }
.statut-val   { font-size:13px; font-weight:bold; color:#0d1117; }
.pill { display:inline-block; padding:3px 9px; border-radius:4px; font-size:8px; font-weight:bold; }
.pill-b { background:#dbeafe; color:#1d4ed8; }
.pill-v { background:#fef9c3; color:#854d0e; }
.pill-d { background:#dcfce7; color:#16a34a; }

/* Corps */
.body { padding:16px 28px; }
.section-title { font-size:8px; font-weight:bold; text-transform:uppercase; letter-spacing:1px; color:#6b7280; border-bottom:1px solid #e5e7eb; padding-bottom:4px; margin-bottom:10px; margin-top:16px; }
.section-title:first-child { margin-top:0; }

/* KPI grid */
.kpi-grid { display:table; width:100%; border-collapse:separate; border-spacing:6px 0; margin-bottom:14px; }
.kpi-cell { display:table-cell; width:33.33%; vertical-align:top; }
.kpi-box  { background:#f9fafb; border:1px solid #e5e7eb; border-radius:6px; padding:9px 12px; text-align:center; }
.kpi-lbl  { font-size:7.5px; font-weight:bold; text-transform:uppercase; letter-spacing:.5px; color:#6b7280; margin-bottom:4px; }
.kpi-val  { font-size:13px; font-weight:bold; color:#0d1117; }
.kpi-box.blue  { background:#eff6ff; border-color:#bfdbfe; }
.kpi-box.green { background:#f0fdf4; border-color:#bbf7d0; }
.kpi-box.red   { background:#fff1f2; border-color:#fecaca; }
.kpi-val.blue  { color:#1d4ed8; }
.kpi-val.green { color:#16a34a; }
.kpi-val.red   { color:#dc2626; }

/* Tables */
.dt { width:100%; border-collapse:collapse; font-size:9px; margin-bottom:12px; }
.dt th { background:#0d1117; color:#fff; padding:6px 10px; text-align:left; font-size:7.5px; text-transform:uppercase; letter-spacing:.4px; }
.dt th.r { text-align:right; }
.dt td { padding:6px 10px; border-bottom:1px solid #f3f4f6; color:#374151; }
.dt td.r { text-align:right; font-weight:bold; }
.dt tfoot td { background:#f5e9c9; font-weight:bold; padding:7px 10px; border-top:2px solid #c9a84c; }
.dt tfoot td.r { text-align:right; }

/* Tableau résultat */
.res-table { width:100%; border-collapse:collapse; font-size:10px; }
.res-table td { padding:7px 12px; border-bottom:1px solid #f3f4f6; }
.res-table td:last-child { text-align:right; font-weight:bold; white-space:nowrap; }
.res-table tr.minus td { color:#16a34a; }
.res-table tr.minus td:last-child { color:#16a34a; }
.res-table tr.credit-entrant td { color:#f97316; }
.res-table tr.total-due td { background:#fef2f2; color:#dc2626; font-size:12px; font-weight:bold; border-top:2px solid #dc2626; }
.res-table tr.total-credit td { background:#f0fdf4; color:#16a34a; font-size:12px; font-weight:bold; border-top:2px solid #16a34a; }

/* Sous-totaux collectée */
.st-grid { display:table; width:100%; border-collapse:separate; border-spacing:4px 0; margin-bottom:12px; }
.st-cell { display:table-cell; width:25%; vertical-align:top; }
.st-box  { background:#f9fafb; border:1px solid #e5e7eb; border-radius:4px; padding:7px 10px; text-align:center; }
.st-lbl  { font-size:7px; text-transform:uppercase; letter-spacing:.4px; color:#9ca3af; margin-bottom:3px; }
.st-val  { font-size:11px; font-weight:bold; color:#0d1117; }

/* Alerte */
.alerte-due    { background:#fff7ed; border:1px solid #fed7aa; border-radius:4px; padding:9px 12px; margin:10px 0; font-size:8px; color:#9a3412; line-height:1.7; }
.alerte-credit { background:#eff6ff; border:1px solid #bfdbfe; border-radius:4px; padding:9px 12px; margin:10px 0; font-size:8px; color:#1e40af; line-height:1.7; }

/* Mentions & footer */
.mentions { background:#fff7ed; border:1px solid #fed7aa; border-radius:4px; padding:9px 12px; margin:10px 28px; font-size:7.5px; color:#9a3412; line-height:1.7; }
.footer { margin:0 28px 20px; padding-top:10px; border-top:1px solid #e5e7eb; display:table; width:calc(100% - 56px); }
.footer-left  { display:table-cell; width:60%; vertical-align:bottom; font-size:7.5px; color:#9ca3af; line-height:1.7; }
.footer-right { display:table-cell; width:40%; vertical-align:bottom; text-align:right; }
.sign-box { border:1.5px dashed #d1d5db; border-radius:5px; padding:10px; display:inline-block; text-align:center; min-width:100px; }
.tampon { width:48px; height:48px; border:2.5px solid #0d1117; border-radius:50%; margin:0 auto 4px; display:table; }
.tampon-inner { display:table-cell; vertical-align:middle; font-size:7px; font-weight:bold; color:#0d1117; text-align:center; line-height:1.3; text-transform:uppercase; }
.sign-label { font-size:7px; color:#9ca3af; }
</style>
</head>
<body>

{{-- EN-TÊTE --}}
<div class="header">
    <div class="header-table">
        <div class="header-left">
            <div class="agence-nom">{{ $agency?->name ?? 'Agence Immobilière' }}</div>
            <div class="agence-sub">Agence Immobilière · Gestion Locative</div>
            @if($agency?->ninea)<div class="agence-ninea">NINEA : {{ $agency->ninea }}</div>@endif
            @if($agency?->adresse)<div style="font-size:8px;color:rgba(255,255,255,.4);margin-top:2px">{{ $agency->adresse }}</div>@endif
        </div>
        <div class="header-right">
            <div class="doc-title">Déclaration TVA Mensuelle</div>
            <div class="doc-sub">Art. 370 CGI Sénégal — TVA agence immobilière</div>
            <div class="doc-periode">{{ $declaration->periode_label }}</div>
        </div>
    </div>
</div>

{{-- BANDEAU STATUT --}}
<div class="statut-band">
    <div class="statut-left">
        <div class="statut-label">Déclaration</div>
        <div class="statut-val">{{ $declaration->periode_label }}</div>
        <div style="font-size:8.5px;color:#6b7280;margin-top:2px">
            {{ $tvaData['nombre_paiements'] }} paiement(s) · Échéance : {{ $declaration->date_echeance->translatedFormat('d F Y') }}
        </div>
    </div>
    <div class="statut-right">
        <div style="margin-bottom:4px">
            @if($declaration->statut === 'brouillon')
                <span class="pill pill-b">Brouillon</span>
            @elseif($declaration->statut === 'validee')
                <span class="pill pill-v">Validée</span>
            @elseif($declaration->statut === 'deposee')
                <span class="pill pill-d">Déposée à la DGI</span>
            @endif
        </div>
        <div style="font-size:8px;color:#9ca3af">
            Généré le {{ now()->format('d/m/Y à H:i') }}<br>
            @if($agency?->ninea)NINEA : {{ $agency->ninea }}@endif
        </div>
    </div>
</div>

{{-- CORPS --}}
<div class="body">

    {{-- KPI --}}
    <div class="kpi-grid">
        <div class="kpi-cell">
            <div class="kpi-box blue">
                <div class="kpi-lbl">TVA collectée</div>
                <div class="kpi-val blue">{{ number_format($declaration->total_tva_collectee, 0, ',', ' ') }}</div>
                <div style="font-size:7px;color:#1d4ed8;margin-top:2px">Art. 369 CGI SN — 18%</div>
            </div>
        </div>
        <div class="kpi-cell">
            <div class="kpi-box green">
                <div class="kpi-lbl">TVA déductible</div>
                <div class="kpi-val green">{{ number_format($declaration->total_tva_deductible, 0, ',', ' ') }}</div>
                <div style="font-size:7px;color:#16a34a;margin-top:2px">Factures NINEA fournisseur</div>
            </div>
        </div>
        <div class="kpi-cell">
            @if((float)$declaration->tva_nette_due > 0)
            <div class="kpi-box red">
                <div class="kpi-lbl">TVA nette due</div>
                <div class="kpi-val red">{{ number_format($declaration->tva_nette_due, 0, ',', ' ') }}</div>
                <div style="font-size:7px;color:#dc2626;margin-top:2px">À verser DGI avant {{ $declaration->date_echeance->translatedFormat('d F Y') }}</div>
            </div>
            @else
            <div class="kpi-box green">
                <div class="kpi-lbl">Crédit reporté</div>
                <div class="kpi-val green">{{ number_format($declaration->credit_reporte_sortant, 0, ',', ' ') }}</div>
                <div style="font-size:7px;color:#16a34a;margin-top:2px">Reporté sur mois suivant</div>
            </div>
            @endif
        </div>
    </div>

    {{-- SECTION 1 — TVA collectée --}}
    <div class="section-title">1. TVA collectée — détail par paiement (Art. 369 CGI SN)</div>

    <table class="dt">
        <thead>
            <tr>
                <th>Référence</th>
                <th>Locataire</th>
                <th>Bien</th>
                <th>Type</th>
                <th>Période</th>
                <th class="r">Loyer HT</th>
                <th class="r">TVA commission</th>
                <th class="r">TVA loyer</th>
                <th class="r">TVA charges</th>
                <th class="r">TVA honoraires</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tvaData['detail_par_contrat'] as $p)
            <tr>
                <td style="font-size:8px;color:#6b7280">{{ $p['reference'] }}</td>
                <td>{{ $p['locataire'] }}</td>
                <td>{{ \Illuminate\Support\Str::limit($p['bien'], 20) }}</td>
                <td>{{ ucfirst($p['type_bail']) }}</td>
                <td style="color:#6b7280">
                    @if($p['periode']){{ \Carbon\Carbon::parse($p['periode'])->translatedFormat('M Y') }}@else—@endif
                </td>
                <td class="r">{{ $p['loyer_ht'] > 0 ? number_format($p['loyer_ht'], 0, ',', ' ').' F' : '—' }}</td>
                <td class="r">{{ $p['tva_commission'] > 0 ? number_format($p['tva_commission'], 0, ',', ' ').' F' : '—' }}</td>
                <td class="r">{{ $p['tva_loyer'] > 0 ? number_format($p['tva_loyer'], 0, ',', ' ').' F' : '—' }}</td>
                <td class="r">{{ $p['tva_charges'] > 0 ? number_format($p['tva_charges'], 0, ',', ' ').' F' : '—' }}</td>
                <td class="r">{{ $p['tva_frais'] > 0 ? number_format($p['tva_frais'], 0, ',', ' ').' F' : '—' }}</td>
            </tr>
            @empty
            <tr><td colspan="10" style="text-align:center;color:#9ca3af;padding:12px;font-style:italic">Aucun paiement enregistré ce mois</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="6" style="font-weight:bold">Total TVA collectée</td>
                <td class="r">{{ number_format($tvaData['tva_commissions'], 0, ',', ' ') }} F</td>
                <td class="r">{{ number_format($tvaData['tva_loyers_commerciaux'], 0, ',', ' ') }} F</td>
                <td class="r">{{ number_format($tvaData['tva_charges_forfait'], 0, ',', ' ') }} F</td>
                <td class="r">{{ number_format($tvaData['tva_honoraires'], 0, ',', ' ') }} F</td>
            </tr>
        </tfoot>
    </table>

    {{-- Sous-totaux par nature --}}
    <div class="st-grid">
        @foreach([
            ['Commissions (Art. 369)', $tvaData['tva_commissions']],
            ['Loyers commerciaux (Art. 355)', $tvaData['tva_loyers_commerciaux']],
            ['Charges forfait (Art. 364 §2a)', $tvaData['tva_charges_forfait']],
            ['Honoraires / frais entrée', $tvaData['tva_honoraires']],
        ] as [$lbl, $val])
        <div class="st-cell">
            <div class="st-box">
                <div class="st-lbl">{{ $lbl }}</div>
                <div class="st-val">{{ number_format($val, 0, ',', ' ') }} F</div>
            </div>
        </div>
        @endforeach
    </div>

    <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:4px;padding:8px 12px;text-align:right;font-size:11px;font-weight:bold;color:#0d1117;margin-bottom:14px">
        TOTAL TVA COLLECTÉE : {{ number_format($declaration->total_tva_collectee, 0, ',', ' ') }} FCFA
    </div>

    {{-- SECTION 2 — TVA déductible --}}
    <div class="section-title">2. TVA déductible — factures au nom de l'agence</div>

    <table class="dt" style="margin-bottom:14px">
        <thead>
            <tr>
                <th style="width:60%">Nature de l'achat</th>
                <th class="r">Montant TVA (FCFA)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Achats / fournitures (papeterie, matériel, logiciels…)</td>
                <td class="r">{{ number_format($declaration->tva_achats_fournitures, 0, ',', ' ') }} F</td>
            </tr>
            <tr>
                <td>Loyer bureau de l'agence (bail commercial assujetti)</td>
                <td class="r">{{ number_format($declaration->tva_loyer_bureau, 0, ',', ' ') }} F</td>
            </tr>
            <tr>
                <td>Autres achats déductibles (prestataires, conseil…)</td>
                <td class="r">{{ number_format($declaration->tva_autres_deductible, 0, ',', ' ') }} F</td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td>TOTAL TVA DÉDUCTIBLE</td>
                <td class="r">{{ number_format($declaration->total_tva_deductible, 0, ',', ' ') }} FCFA</td>
            </tr>
        </tfoot>
    </table>

    @if($declaration->notes)
    <div style="font-size:8px;color:#6b7280;margin-bottom:12px;padding:6px 10px;background:#f9fafb;border-radius:4px">
        <strong>Notes :</strong> {{ $declaration->notes }}
    </div>
    @endif

    {{-- SECTION 3 — Résultat --}}
    <div class="section-title">3. Résultat — TVA nette à reverser (Art. 370 CGI SN)</div>

    <table class="res-table">
        <tr>
            <td>TVA collectée brute (total section 1)</td>
            <td>{{ number_format($declaration->total_tva_collectee, 0, ',', ' ') }} FCFA</td>
        </tr>
        <tr class="minus">
            <td>− TVA déductible (total section 2)</td>
            <td>− {{ number_format($declaration->total_tva_deductible, 0, ',', ' ') }} FCFA</td>
        </tr>
        @if((float)$declaration->credit_reporte_entrant > 0)
        <tr class="credit-entrant">
            <td>− Crédit de TVA reporté du mois précédent</td>
            <td>− {{ number_format($declaration->credit_reporte_entrant, 0, ',', ' ') }} FCFA</td>
        </tr>
        @endif
        @if((float)$declaration->tva_nette_due > 0)
        <tr class="total-due">
            <td>= TVA NETTE DUE (à verser à la DGI)</td>
            <td>{{ number_format($declaration->tva_nette_due, 0, ',', ' ') }} FCFA</td>
        </tr>
        @else
        <tr class="total-credit">
            <td>= CRÉDIT DE TVA (reportable sur mois suivant)</td>
            <td>{{ number_format($declaration->credit_reporte_sortant, 0, ',', ' ') }} FCFA</td>
        </tr>
        @endif
    </table>

    @if((float)$declaration->tva_nette_due > 0)
    <div class="alerte-due">
        <strong>⚠ IMPORTANT :</strong> La TVA nette de <strong>{{ number_format($declaration->tva_nette_due, 0, ',', ' ') }} FCFA</strong>
        doit être versée à la Direction Générale des Impôts et Domaines (DGI) avant le
        <strong>{{ $declaration->date_echeance->translatedFormat('d F Y') }}</strong>.
        Art. 370 CGI SN — Tout retard entraîne des pénalités et intérêts de retard.
        <strong>Déposez cette déclaration à votre Centre des Services Fiscaux (CSF).</strong>
    </div>
    @else
    <div class="alerte-credit">
        ℹ Crédit de TVA de <strong>{{ number_format($declaration->credit_reporte_sortant, 0, ',', ' ') }} FCFA</strong>
        automatiquement reporté sur la déclaration de
        {{ \Carbon\Carbon::create($annee, $mois, 1)->addMonth()->locale('fr')->translatedFormat('F Y') }}.
    </div>
    @endif

</div>

{{-- MENTIONS LÉGALES --}}
<div class="mentions">
    <strong>⚠ Document fiscal — République du Sénégal</strong><br>
    Déclaration établie par <strong>{{ $agency?->name }}</strong>
    @if($agency?->ninea)(NINEA : {{ $agency->ninea }})@endif
    pour la période <strong>{{ $declaration->periode_label }}</strong>.
    TVA calculée au taux de 18% (Art. 369 CGI SN) sur commissions, loyers commerciaux, charges forfaitaires et honoraires.
    Déclaration mensuelle obligatoire — Art. 370 CGI SN.
    <strong>À déposer au Centre des Services Fiscaux avant le {{ $declaration->date_echeance->translatedFormat('d F Y') }}.</strong>
    Ce document est fourni à titre de suivi interne. Consultez un expert-comptable ou la DGID pour validation officielle.
</div>

{{-- FOOTER --}}
<div class="footer">
    <div class="footer-left">
        <strong>{{ $agency?->name }}</strong><br>
        {{ $agency?->adresse }}@if($agency?->ninea) · NINEA : {{ $agency->ninea }}@endif<br>
        @if($agency?->telephone)Tél : {{ $agency->telephone }} · @endif{{ $agency?->email }}
    </div>
    <div class="footer-right">
        <div class="sign-box">
            <div class="tampon">
                <div class="tampon-inner">{{ strtoupper(substr($agency?->name ?? 'BI', 0, 4)) }}<br>IMMO</div>
            </div>
            <div class="sign-label">Signature &amp; Cachet</div>
        </div>
    </div>
</div>

</body>
</html>
