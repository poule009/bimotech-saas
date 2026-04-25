<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title>Bail — {{ $contrat->reference_bail ?? 'Contrat #'.$contrat->id }}</title>
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family:'DejaVu Sans',Arial,sans-serif; font-size:10px; color:#1a1a1a; line-height:1.5; }

/* En-tête */
.header { display:table; width:100%; margin-bottom:24px; padding-bottom:14px; border-bottom:2px solid #0d1117; }
.header-left  { display:table-cell; vertical-align:top; width:60%; }
.header-right { display:table-cell; vertical-align:top; text-align:right; }
.agency-name  { font-size:16px; font-weight:700; color:#0d1117; letter-spacing:-.3px; }
.agency-info  { font-size:9px; color:#6b7280; margin-top:3px; line-height:1.6; }
.bail-title   { font-size:20px; font-weight:700; color:#c9a84c; letter-spacing:-.5px; }
.bail-ref     { font-size:9px; color:#9ca3af; margin-top:4px; }

/* Sections */
.section { margin-bottom:18px; }
.section-title { font-size:9px; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:#9ca3af; border-bottom:1px solid #e5e7eb; padding-bottom:4px; margin-bottom:10px; }

/* Grille 2 colonnes */
.grid2 { display:table; width:100%; }
.col { display:table-cell; width:48%; vertical-align:top; padding:12px 14px; background:#f9fafb; border:1px solid #e5e7eb; border-radius:6px; }
.col-spacer { display:table-cell; width:4%; }
.col-label { font-size:8px; font-weight:700; text-transform:uppercase; letter-spacing:.7px; color:#9ca3af; margin-bottom:2px; }
.col-val { font-size:11px; font-weight:600; color:#0d1117; }
.col-sub { font-size:9px; color:#6b7280; margin-top:1px; }

/* Tableau loyer */
table { width:100%; border-collapse:collapse; margin-bottom:14px; }
thead th { background:#0d1117; color:#fff; padding:7px 10px; font-size:8.5px; font-weight:700; text-transform:uppercase; letter-spacing:.5px; text-align:left; }
thead th.right { text-align:right; }
tbody td { padding:7px 10px; font-size:9.5px; color:#374151; border-bottom:1px solid #f3f4f6; }
tbody td.right { text-align:right; font-weight:600; }
tfoot td { padding:8px 10px; font-size:10px; font-weight:700; background:#f5e9c9; color:#8a6e2f; }
tfoot td.right { text-align:right; color:#c9a84c; font-size:12px; }

/* Clauses */
.clause-box { background:#f9fafb; border:1px solid #e5e7eb; border-radius:6px; padding:12px 14px; margin-bottom:14px; font-size:9.5px; color:#374151; line-height:1.7; white-space:pre-wrap; }
.clause-legal { font-size:9px; color:#6b7280; line-height:1.65; }

/* Signatures */
.sig-section { display:table; width:100%; margin-top:30px; }
.sig-cell    { display:table-cell; width:45%; padding:16px 14px; border:1px dashed #d1d5db; border-radius:6px; text-align:center; }
.sig-spacer  { display:table-cell; width:10%; }
.sig-label   { font-size:8px; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:#9ca3af; margin-bottom:28px; }
.sig-line    { border-top:1px solid #e5e7eb; padding-top:5px; font-size:9px; color:#9ca3af; margin-top:20px; }

/* Footer */
.footer { position:fixed; bottom:0; left:0; right:0; padding:6px 20px; background:#f9fafb; border-top:1px solid #e5e7eb; display:table; width:100%; font-size:8px; color:#9ca3af; }
.footer-left  { display:table-cell; }
.footer-right { display:table-cell; text-align:right; }

/* Badge or */
.gold-badge { display:inline-block; background:#f5e9c9; color:#8a6e2f; border:1px solid rgba(201,168,76,.3); border-radius:4px; padding:2px 8px; font-size:8.5px; font-weight:700; }
</style>
</head>
<body>

{{-- Footer fixe --}}
<div class="footer">
    <div class="footer-left">{{ $agency->name ?? 'BimoTech Immo' }} · Contrat de bail · Réf. {{ $contrat->reference_bail ?? $contrat->id }}</div>
    <div class="footer-right">Généré le {{ now()->format('d/m/Y') }} · Document confidentiel</div>
</div>

{{-- En-tête --}}
<div class="header">
    <div class="header-left">
        <div class="agency-name">{{ $agency->name ?? 'BimoTech Immo' }}</div>
        <div class="agency-info">
            @if($agency->adresse){{ $agency->adresse }}<br>@endif
            @if($agency->telephone)Tél. {{ $agency->telephone }} · @endif
            @if($agency->email){{ $agency->email }}@endif
            @if($agency->ninea)<br>NINEA : {{ $agency->ninea }}@endif
        </div>
    </div>
    <div class="header-right">
        <div class="bail-title">CONTRAT DE BAIL</div>
        <div class="bail-ref">
            Réf. {{ $contrat->reference_bail ?? 'BAIL-'.$contrat->id }}<br>
            <span class="gold-badge">{{ \App\Models\Contrat::TYPES_BAIL[$contrat->type_bail] ?? $contrat->type_bail }}</span>
        </div>
    </div>
</div>

{{-- PARTIES --}}
<div class="section">
    <div class="section-title">Parties au contrat</div>
    <div class="grid2">
        <div class="col">
            <div class="col-label">Bailleur (Propriétaire)</div>
            <div class="col-val">{{ $contrat->bien?->proprietaire?->name ?? '—' }}</div>
            @if($contrat->bien?->proprietaire?->email)
            <div class="col-sub">{{ $contrat->bien->proprietaire->email }}</div>
            @endif
            @if($contrat->bien?->proprietaire?->telephone)
            <div class="col-sub">{{ $contrat->bien->proprietaire->telephone }}</div>
            @endif
            @if($contrat->bien?->proprietaire?->adresse)
            <div class="col-sub">{{ $contrat->bien->proprietaire->adresse }}</div>
            @endif
            <div class="col-sub" style="margin-top:4px;font-size:8px;color:#9ca3af">Représenté par {{ $agency->name ?? 'l\'agence' }}</div>
        </div>
        <div class="col-spacer"></div>
        <div class="col">
            <div class="col-label">Preneur (Locataire)</div>
            <div class="col-val">{{ $contrat->locataire?->name ?? '—' }}</div>
            @if($contrat->locataire?->email)
            <div class="col-sub">{{ $contrat->locataire->email }}</div>
            @endif
            @if($contrat->locataire?->telephone)
            <div class="col-sub">{{ $contrat->locataire->telephone }}</div>
            @endif
            @if($contrat->locataire?->adresse)
            <div class="col-sub">{{ $contrat->locataire->adresse }}</div>
            @endif
        </div>
    </div>
</div>

{{-- BIEN --}}
<div class="section">
    <div class="section-title">Bien loué</div>
    <div class="grid2">
        <div class="col">
            <div class="col-label">Description</div>
            <div class="col-val">{{ \App\Models\Bien::TYPES[$contrat->bien?->type] ?? $contrat->bien?->type }} {{ $contrat->bien?->meuble ? '(Meublé)' : '' }}</div>
            <div class="col-sub">{{ $contrat->bien?->adresse }}, {{ $contrat->bien?->ville }}</div>
            @if($contrat->bien?->surface_m2)<div class="col-sub">Surface : {{ $contrat->bien->surface_m2 }} m²</div>@endif
            @if($contrat->bien?->nombre_pieces)<div class="col-sub">{{ $contrat->bien->nombre_pieces }} pièce(s)</div>@endif
        </div>
        <div class="col-spacer"></div>
        <div class="col">
            <div class="col-label">Durée</div>
            <div class="col-val">Du {{ $contrat->date_debut?->format('d/m/Y') }}</div>
            @if($contrat->date_fin)
            <div class="col-sub">au {{ $contrat->date_fin->format('d/m/Y') }}</div>
            @else
            <div class="col-sub">Durée indéterminée</div>
            @endif
            @if($contrat->garant_nom)
            <div class="col-sub" style="margin-top:6px"><strong>Garant :</strong> {{ $contrat->garant_nom }} — {{ $contrat->garant_telephone }}</div>
            @endif
        </div>
    </div>
</div>

{{-- LOYER --}}
<div class="section">
    <div class="section-title">Conditions financières</div>
    <table>
        <thead>
            <tr>
                <th>Désignation</th>
                <th class="right">Montant (FCFA)</th>
                <th class="right">Observations</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Loyer {{ $contrat->loyer_assujetti_tva ? 'HT' : 'nu' }}</td>
                <td class="right">{{ number_format($contrat->loyer_nu, 0, ',', ' ') }}</td>
                <td class="right" style="color:#9ca3af;font-size:8.5px">Base de calcul commission</td>
            </tr>
            @if($contrat->loyer_assujetti_tva)
            <tr>
                <td>TVA {{ $contrat->taux_tva_loyer ?? 18 }}% (bail commercial/meublé)</td>
                <td class="right">{{ number_format($contrat->loyer_nu * (($contrat->taux_tva_loyer ?? 18)/100), 0, ',', ' ') }}</td>
                <td class="right" style="color:#9ca3af;font-size:8.5px">Art. 357 CGI SN</td>
            </tr>
            @endif
            @if($contrat->charges_mensuelles)
            <tr>
                <td>Charges récupérables</td>
                <td class="right">{{ number_format($contrat->charges_mensuelles, 0, ',', ' ') }}</td>
                <td class="right" style="color:#9ca3af;font-size:8.5px">{{ $contrat->charges_assujetties_tva ? 'Forfait — TVA applicable' : 'Récupérables' }}</td>
            </tr>
            @endif
            @if($contrat->tom_amount)
            <tr>
                <td>TOM (Taxe Ordures Ménagères)</td>
                <td class="right">{{ number_format($contrat->tom_amount, 0, ',', ' ') }}</td>
                <td class="right" style="color:#9ca3af;font-size:8.5px">Part locataire</td>
            </tr>
            @endif
        </tbody>
        <tfoot>
            <tr>
                <td>Loyer mensuel total</td>
                <td class="right">{{ number_format($contrat->loyer_contractuel, 0, ',', ' ') }} FCFA</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    {{-- Caution & Frais --}}
    <div class="grid2">
        <div class="col">
            <div class="col-label">Dépôt de garantie</div>
            <div class="col-val">{{ number_format($contrat->caution, 0, ',', ' ') }} FCFA</div>
            <div class="col-sub">{{ $contrat->nombre_mois_caution ?? 1 }} mois de loyer · restitué à la sortie</div>
        </div>
        <div class="col-spacer"></div>
        <div class="col">
            <div class="col-label">Frais d'agence</div>
            <div class="col-val">{{ number_format($contrat->frais_agence ?? 0, 0, ',', ' ') }} FCFA HT</div>
            <div class="col-sub">Commission agence — versé à la signature</div>
        </div>
    </div>
</div>

{{-- CLAUSES LÉGALES --}}
<div class="section">
    <div class="section-title">Clauses légales — Loi 81-18</div>
    <div class="clause-legal">
Le présent contrat est régi par la loi sénégalaise n° 81-18 du 30 mars 1981 relative aux rapports entre bailleurs et locataires.<br>
Le locataire s'engage à payer le loyer aux termes convenus, à user paisiblement des lieux conformément à leur destination, à ne pas sous-louer sans accord écrit du bailleur.<br>
Le bailleur s'engage à délivrer le bien en bon état, à en assurer la jouissance paisible et à effectuer les réparations relevant de sa charge.<br>
Tout litige sera porté devant les juridictions compétentes de Dakar, Sénégal.
    </div>
</div>

{{-- CLAUSES AGENCE --}}
@if($agency->modele_contrat)
<div class="section">
    <div class="section-title">Clauses générales de l'agence</div>
    <div class="clause-box">{{ $agency->modele_contrat }}</div>
</div>
@endif

{{-- CLAUSES PARTICULIÈRES --}}
@if($contrat->clauses_particulieres)
<div class="section">
    <div class="section-title">Clauses particulières — spécifiques à ce bail</div>
    <div class="clause-box" style="border-color:rgba(201,168,76,.3);background:#fffdf7">{{ $contrat->clauses_particulieres }}</div>
</div>
@endif

{{-- SIGNATURES --}}
<div class="sig-section">
    <div class="sig-cell">
        <div class="sig-label">Signature du bailleur<br>Lu et approuvé</div>
        <div class="sig-line">{{ $contrat->bien?->proprietaire?->name ?? '' }}</div>
        <div style="font-size:8px;color:#9ca3af;margin-top:3px">Représenté par {{ $agency->name }}</div>
    </div>
    <div class="sig-spacer"></div>
    <div class="sig-cell">
        <div class="sig-label">Signature du locataire<br>Lu et approuvé</div>
        <div class="sig-line">{{ $contrat->locataire?->name ?? '' }}</div>
        <div style="font-size:8px;color:#9ca3af;margin-top:3px">Date : _____ / _____ / _______</div>
    </div>
</div>

</body>
</html>
