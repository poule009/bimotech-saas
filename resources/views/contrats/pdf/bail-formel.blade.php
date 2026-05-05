<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title>Contrat de bail — {{ $contrat->reference_bail_affichee }}</title>
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family:"DejaVu Sans",Arial,sans-serif; font-size:10px; color:#1a202c; background:#fff; line-height:1.5; }

/* ── EN-TÊTE SOMBRE ── */
.header { background:#0d1117; padding:0; }
.header-inner { display:table; width:100%; padding:18px 28px; }
.header-left  { display:table-cell; width:60%; vertical-align:middle; }
.header-right { display:table-cell; width:40%; vertical-align:middle; text-align:right; }
.agence-nom  { font-size:18px; font-weight:bold; color:#fff; letter-spacing:.3px; margin-bottom:2px; }
.agence-sub  { font-size:8px; color:rgba(255,255,255,.4); letter-spacing:2px; text-transform:uppercase; margin-bottom:5px; }
.agence-pill { display:inline-block; background:rgba(201,168,76,.15); border:1px solid rgba(201,168,76,.3); border-radius:4px; padding:2px 9px; font-size:8px; color:#c9a84c; letter-spacing:.5px; }
.agence-coords { font-size:9px; color:rgba(255,255,255,.5); line-height:1.8; margin-bottom:4px; }
.ninea-pill { display:inline-block; background:rgba(201,168,76,.12); border:1px solid rgba(201,168,76,.25); border-radius:3px; padding:2px 8px; font-size:8px; font-weight:bold; color:#c9a84c; }

/* ── BANDE TITRE ── */
.title-band { display:table; width:100%; background:#f5e9c9; border-left:5px solid #c9a84c; padding:10px 28px; }
.title-left  { display:table-cell; vertical-align:middle; }
.title-right { display:table-cell; vertical-align:middle; text-align:right; }
.doc-title { font-size:14px; font-weight:bold; text-transform:uppercase; letter-spacing:2.5px; color:#0d1117; }
.doc-type   { font-size:10px; color:#8a6e2f; margin-top:2px; font-weight:bold; }
.ref-label  { font-size:7px; text-transform:uppercase; letter-spacing:.8px; color:#8a6e2f; margin-bottom:1px; }
.ref-value  { font-size:10px; font-weight:bold; color:#0d1117; }

/* ── BANDEAU INFO BAIL ── */
.bail-band { display:table; width:100%; background:#f9fafb; border-top:1px solid #e5e7eb; border-bottom:1px solid #e5e7eb; }
.bail-cell  { display:table-cell; padding:7px 16px; border-right:1px solid #e5e7eb; vertical-align:middle; }
.bail-cell:first-child { padding-left:28px; }
.bail-cell:last-child  { border-right:none; padding-right:28px; }
.bail-label { font-size:7px; text-transform:uppercase; letter-spacing:.8px; color:#9ca3af; margin-bottom:2px; }
.bail-value { font-size:10px; font-weight:bold; color:#0d1117; }

/* ── CORPS ── */
.body { padding:16px 28px 20px; }

/* ── TITRE SECTION ── */
.section-title { font-size:8px; font-weight:bold; text-transform:uppercase; letter-spacing:1px; color:#6b7280; border-bottom:1px solid #e5e7eb; padding-bottom:4px; margin-bottom:10px; margin-top:14px; }
.section-title:first-child { margin-top:0; }

/* ── PARTIES ── */
.grid2 { display:table; width:100%; }
.col   { display:table-cell; width:50%; vertical-align:top; }
.col:first-child { padding-right:10px; }
.col:last-child  { padding-left:10px; }
.partie-box { background:#f9fafb; border:1px solid #e5e7eb; border-radius:4px; padding:10px 12px; }
.partie-role  { font-size:7.5px; font-weight:bold; text-transform:uppercase; letter-spacing:.8px; color:#c9a84c; margin-bottom:4px; }
.partie-name  { font-size:11px; font-weight:bold; color:#0d1117; margin-bottom:2px; }
.partie-sub   { font-size:9px; color:#6b7280; line-height:1.7; }
.partie-repr  { font-size:8px; color:#9ca3af; font-style:italic; margin-top:4px; }

/* Garant */
.garant-box { background:#eff6ff; border:1px solid #bfdbfe; border-radius:4px; padding:8px 12px; margin-top:10px; }
.garant-role { font-size:7.5px; font-weight:bold; text-transform:uppercase; letter-spacing:.8px; color:#1d4ed8; margin-bottom:3px; }

/* Bien loué */
.bien-box { background:#f9fafb; border:1px solid #e5e7eb; border-left:4px solid #c9a84c; border-radius:0 4px 4px 0; padding:10px 14px; margin-top:10px; }
.bien-titre { font-size:11px; font-weight:bold; color:#0d1117; margin-bottom:3px; }
.bien-adr   { font-size:9.5px; color:#374151; }

/* ── ARTICLES ── */
.article { margin-bottom:12px; }
.art-num  { font-size:8px; font-weight:bold; text-transform:uppercase; letter-spacing:1px; color:#9ca3af; margin-bottom:1px; }
.art-titre { font-size:10.5px; font-weight:bold; color:#0d1117; margin-bottom:5px; border-left:3px solid #c9a84c; padding-left:8px; }
.art-corps { font-size:9.5px; color:#374151; line-height:1.75; text-align:justify; padding-left:11px; }
.art-corps p { margin-bottom:4px; }
.art-corps p:last-child { margin-bottom:0; }

/* ── TABLEAU LOYER (article 3) ── */
.tbl-loyer { width:100%; border-collapse:collapse; margin:8px 0; font-size:9.5px; }
.tbl-loyer th { background:#0d1117; color:#fff; padding:6px 10px; text-align:left; font-size:8px; font-weight:bold; text-transform:uppercase; letter-spacing:.5px; }
.tbl-loyer th.r { text-align:right; }
.tbl-loyer td { padding:6px 10px; border-bottom:1px solid #f3f4f6; color:#374151; }
.tbl-loyer td.r { text-align:right; font-weight:bold; color:#0d1117; }
.tbl-loyer td.obs { font-size:8px; color:#9ca3af; text-align:right; }
.tbl-loyer tr.tva td { background:#fffbeb; color:#92400e; font-size:9px; }
.tbl-loyer tr.tva td.r { color:#d97706; }
.tbl-loyer tr.total td { background:#f5e9c9; font-weight:bold; font-size:10px; color:#0d1117; border-top:2px solid #c9a84c; border-bottom:2px solid #c9a84c; }
.tbl-loyer tr.total td.r { color:#8a6e2f; font-size:11px; }
.en-lettres { background:#fffbeb; border:1px solid #fde68a; border-radius:3px; padding:5px 10px; font-size:8.5px; font-style:italic; color:#78350f; margin-top:4px; }
.en-lettres-lbl { font-size:7px; text-transform:uppercase; letter-spacing:.7px; color:#d97706; margin-bottom:1px; }

/* ── CLAUSES ── */
.clause-box { background:#f9fafb; border:1px solid #e5e7eb; border-radius:4px; padding:10px 12px; font-size:9.5px; color:#374151; line-height:1.75; white-space:pre-wrap; margin-top:6px; }
.clause-part { background:#fffdf7; border-color:rgba(201,168,76,.3); }

/* ── FAIT À ── */
.fait-a { text-align:right; font-size:10px; color:#374151; font-style:italic; margin:16px 0 10px; padding-top:10px; border-top:1px solid #e5e7eb; }

/* ── SIGNATURES ── */
.sig-table { display:table; width:100%; margin-top:16px; border-spacing:0; }
.sig-cell   { display:table-cell; width:44%; vertical-align:top; border:1.5px dashed #d1d5db; border-radius:5px; padding:14px 12px; text-align:center; }
.sig-spacer { display:table-cell; width:12%; }
.sig-role   { font-size:7.5px; font-weight:bold; text-transform:uppercase; letter-spacing:.8px; color:#9ca3af; margin-bottom:3px; }
.sig-nom    { font-size:10px; font-weight:bold; color:#0d1117; }
.sig-detail { font-size:8px; color:#9ca3af; margin-top:2px; }
.sig-espace { height:44px; }
.sig-mention{ margin-top:10px; padding-top:6px; border-top:1px solid #e5e7eb; font-size:7.5px; color:#9ca3af; }
.tampon { width:56px; height:56px; border:2px solid #0d1117; border-radius:50%; margin:4px auto; display:table; }
.tampon-inner { display:table-cell; vertical-align:middle; text-align:center; font-size:7px; font-weight:bold; color:#0d1117; text-transform:uppercase; line-height:1.4; }

/* ── MENTIONS ── */
.mentions { background:#fff7ed; border:1px solid #fed7aa; border-radius:4px; padding:9px 12px; margin-top:14px; font-size:7.5px; color:#9a3412; line-height:1.75; }
.mentions-titre { font-size:7.5px; font-weight:bold; text-transform:uppercase; letter-spacing:.5px; color:#7c2d12; margin-bottom:4px; }

/* ── PIED DE PAGE FIXE ── */
.footer { position:fixed; bottom:0; left:0; right:0; padding:5px 28px; background:#f9fafb; border-top:1px solid #e5e7eb; display:table; width:100%; font-size:7.5px; color:#9ca3af; }
.footer-l { display:table-cell; }
.footer-r { display:table-cell; text-align:right; }
</style>
</head>
<body>

{{-- Pied de page fixe --}}
<div class="footer">
    <div class="footer-l">{{ $agency->name ?? 'BimoTech Immo' }} · Contrat de bail · Réf. {{ $contrat->reference_bail_affichee }}</div>
    <div class="footer-r">Établi le {{ now()->format('d/m/Y') }} · Document confidentiel — à conserver</div>
</div>

@php
    $proprietaire   = $contrat->bien?->proprietaire;
    $profilProp     = $proprietaire?->proprietaire;
    $locataire      = $contrat->locataire;
    $profilLoc      = $locataire?->locataire;

    $estEntreprise  = (bool) ($profilLoc?->est_entreprise ?? false);
    $nomProp        = $proprietaire?->name ?? '—';
    $nomLoc         = ($estEntreprise && $profilLoc?->nom_entreprise) ? $profilLoc->nom_entreprise : ($locataire?->name ?? '—');
    $cniProp        = $profilProp?->cni;
    $cniLoc         = $estEntreprise ? $profilLoc?->ninea_locataire : $profilLoc?->cni;
    $dateNaissProp  = $profilProp?->date_naissance?->format('d/m/Y');
    $dateNaissLoc   = !$estEntreprise ? $profilLoc?->date_naissance?->format('d/m/Y') : null;
    $professionLoc  = $profilLoc?->profession;

    $bien           = $contrat->bien;
    $typeBien       = \App\Models\Bien::TYPES[$bien?->type] ?? $bien?->type;
    $adresseBien    = collect([$bien?->adresse, $bien?->quartier, $bien?->commune, $bien?->ville])->filter()->implode(', ');

    $dateDebut      = $contrat->date_debut?->format('d/m/Y') ?? '—';
    $dateFin        = $contrat->date_fin?->format('d/m/Y');
    $dureeMois      = $contrat->duree_mois;
    if ($dureeMois >= 12 && $dureeMois % 12 === 0) {
        $dureeTexte = ($dureeMois / 12).' AN'.(($dureeMois / 12) > 1 ? 'S' : '');
    } elseif ($dureeMois > 0) {
        $dureeTexte = $dureeMois.' MOIS';
    } else {
        $dureeTexte = null;
    }

    $loyerTotal       = (float) $contrat->loyer_contractuel;
    $loyerEnLettres   = \App\Services\NombreEnLettres::convertir((int) round($loyerTotal));
    $cautionEnLettres = \App\Services\NombreEnLettres::convertir((int) round((float) $contrat->caution));
    $ville           = $contrat->bien?->ville ?? 'Dakar';
@endphp

{{-- ══ EN-TÊTE ══════════════════════════════════════════════════════════ --}}
<div class="header">
    <div class="header-inner">
        <div class="header-left">
            <div class="agence-nom">{{ $agency->name ?? 'BimoTech Immo' }}</div>
            <div class="agence-sub">Agence Immobilière · Gestion Locative</div>
            <div class="agence-pill">Mandataire du Propriétaire Bailleur</div>
        </div>
        <div class="header-right">
            <div class="agence-coords">
                @if($agency->adresse){{ $agency->adresse }}<br>@endif
                @if($agency->telephone)Tél. {{ $agency->telephone }}<br>@endif
                @if($agency->email){{ $agency->email }}@endif
            </div>
            @if($agency->ninea)<div class="ninea-pill">NINEA : {{ $agency->ninea }}</div>@endif
        </div>
    </div>
</div>

{{-- ══ BANDE TITRE ═══════════════════════════════════════════════════════ --}}
<div class="title-band">
    <div class="title-left">
        <div class="doc-title">Contrat de Bail</div>
        <div class="doc-type">{{ \App\Models\Contrat::TYPES_BAIL[$contrat->type_bail] ?? $contrat->type_bail }}</div>
    </div>
    <div class="title-right">
        <div class="ref-label">Référence du bail</div>
        <div class="ref-value">{{ $contrat->reference_bail_affichee }}</div>
        <div class="ref-label" style="margin-top:5px">Date d'effet</div>
        <div class="ref-value">{{ $dateDebut }}</div>
    </div>
</div>

{{-- ══ BANDEAU INFO ══════════════════════════════════════════════════════ --}}
<div class="bail-band">
    <div class="bail-cell" style="width:28%">
        <div class="bail-label">Bien loué</div>
        <div class="bail-value">{{ $typeBien }}{{ $bien?->meuble ? ' (Meublé)' : '' }}</div>
    </div>
    <div class="bail-cell" style="width:32%">
        <div class="bail-label">Adresse</div>
        <div class="bail-value" style="font-size:9px">{{ $bien?->adresse }}{{ $bien?->ville ? ', '.$bien->ville : '' }}</div>
    </div>
    <div class="bail-cell" style="width:20%">
        <div class="bail-label">Durée</div>
        <div class="bail-value">{{ $dureeTexte ?? 'Indéterminée' }}</div>
    </div>
    <div class="bail-cell" style="width:20%">
        <div class="bail-label">Loyer mensuel</div>
        <div class="bail-value" style="color:#8a6e2f">{{ number_format($loyerTotal, 0, ',', ' ') }} FCFA</div>
    </div>
</div>

{{-- ══ CORPS ══════════════════════════════════════════════════════════════ --}}
<div class="body">

    {{-- PARTIES --}}
    <div class="section-title">Entre les soussignés</div>
    <div class="grid2">
        <div class="col">
            <div class="partie-box">
                <div class="partie-role">Bailleur (Propriétaire)</div>
                <div class="partie-name">{{ $nomProp }}</div>
                <div class="partie-sub">
                    @if($proprietaire?->telephone)Tél. {{ $proprietaire->telephone }}<br>@endif
                    @if($proprietaire?->adresse){{ $proprietaire->adresse }}<br>@endif
                    @if($cniProp)CNI : {{ $cniProp }}<br>@endif
                    @if($dateNaissProp)Né(e) le {{ $dateNaissProp }}<br>@endif
                    @if($profilProp?->ninea)NINEA : {{ $profilProp->ninea }}@endif
                </div>
                <div class="partie-repr">Représenté(e) par {{ $agency->name ?? "l'agence" }}</div>
            </div>
        </div>
        <div class="col">
            <div class="partie-box">
                <div class="partie-role">{{ $estEntreprise ? 'Preneur (Personne Morale)' : 'Preneur (Locataire)' }}</div>
                <div class="partie-name">{{ $nomLoc }}</div>
                <div class="partie-sub">
                    @if($locataire?->telephone)Tél. {{ $locataire->telephone }}<br>@endif
                    @if($locataire?->adresse){{ $locataire->adresse }}<br>@endif
                    @if($cniLoc){{ $estEntreprise ? 'NINEA' : 'CNI' }} : {{ $cniLoc }}<br>@endif
                    @if($dateNaissLoc)Né(e) le {{ $dateNaissLoc }}<br>@endif
                    @if($professionLoc)Profession : {{ $professionLoc }}@endif
                    @if($estEntreprise && $profilLoc?->rccm_locataire)<br>RCCM : {{ $profilLoc->rccm_locataire }}@endif
                    @if($estEntreprise && $locataire?->name && $locataire->name !== $nomLoc)<br>Représentant : {{ $locataire->name }}@endif
                </div>
            </div>
        </div>
    </div>

    @if($contrat->garant_nom)
    <div class="garant-box">
        <div class="garant-role">Garant — Caution solidaire (Art. 783 et s. COCC SN)</div>
        <div style="display:table;width:100%">
            <div style="display:table-cell;width:50%;vertical-align:top">
                <div style="font-size:10px;font-weight:bold;color:#0d1117">{{ $contrat->garant_nom }}</div>
                <div style="font-size:9px;color:#374151;margin-top:2px">
                    @if($contrat->garant_telephone)Tél. {{ $contrat->garant_telephone }}<br>@endif
                    @if($contrat->garant_cni)CNI : {{ $contrat->garant_cni }}@endif
                </div>
            </div>
            <div style="display:table-cell;width:50%;vertical-align:top;padding-left:12px">
                <div style="font-size:9px;color:#374151">
                    @if($contrat->garant_adresse){{ $contrat->garant_adresse }}@endif
                </div>
            </div>
        </div>
        <div style="font-size:8.5px;color:#1e40af;margin-top:7px;font-style:italic;border-top:1px solid #bfdbfe;padding-top:5px">
            Le garant soussigné se porte caution solidaire du preneur pour le paiement des loyers,
            charges et accessoires, ainsi que pour toutes indemnités résultant de l'inexécution du bail,
            renonçant expressément au bénéfice de discussion et de division.
        </div>
    </div>
    @endif

    <p style="font-size:10px;font-weight:bold;text-align:center;margin:14px 0 10px;color:#0d1117">
        Il a été convenu et arrêté ce qui suit :
    </p>

    {{-- ARTICLE 1 — DÉSIGNATION --}}
    <div class="article">
        <div class="art-num">Article 1</div>
        <div class="art-titre">Désignation des lieux loués</div>
        <div class="art-corps">
            <div class="bien-box">
                <div class="bien-titre">
                    {{ $typeBien }}{{ $bien?->meuble ? ' meublé' : '' }}
                    @if($bien?->reference) — Réf. {{ $bien->reference }}@endif
                </div>
                <div class="bien-adr">
                    {{ $adresseBien ?: '—' }}
                    @if($bien?->surface_m2) · {{ $bien->surface_m2 }} m²@endif
                    @if($bien?->nombre_pieces) · {{ $bien->nombre_pieces }} pièce(s)@endif
                </div>
            </div>
            <p style="margin-top:6px">
                Le bailleur donne en location les lieux ci-dessus désignés au preneur qui accepte,
                dans l'état où ils se trouvent, au vu et su des deux parties.
            </p>
            @php
                $destinationLabel = match($contrat->type_bail) {
                    'habitation'  => "usage d'habitation principale et exclusive",
                    'commercial'  => 'usage commercial ou professionnel',
                    'mixte'       => 'usage mixte (habitation et professionnel)',
                    'saisonnier'  => 'usage d\'habitation saisonnière',
                    default       => "usage d'habitation",
                };
            @endphp
            <p>
                Les lieux loués sont destinés exclusivement à
                <strong>{{ $destinationLabel }}</strong>.
                Toute affectation à un usage différent, même partielle, est interdite sans
                l'accord écrit et préalable du bailleur, sous peine de résiliation du présent bail.
            </p>
        </div>
    </div>

    {{-- ARTICLE 2 — DURÉE --}}
    <div class="article">
        <div class="art-num">Article 2</div>
        <div class="art-titre">Durée du bail</div>
        <div class="art-corps">
            <p>
                Le présent bail est consenti pour une durée
                @if($dureeTexte)de <strong>{{ $dureeTexte }}</strong>,@else indéterminée,@endif
                à compter du <strong>{{ $dateDebut }}</strong>@if($dateFin) jusqu'au <strong>{{ $dateFin }}</strong>@endif.
            </p>
            @if($contrat->indexation_annuelle > 0)
            <p>Le loyer est révisable annuellement à hauteur de <strong>{{ number_format($contrat->indexation_annuelle, 1) }} %</strong>.</p>
            @endif
        </div>
    </div>

    {{-- ARTICLE 3 — LOYER --}}
    <div class="article">
        <div class="art-num">Article 3</div>
        <div class="art-titre">Loyer et conditions financières</div>
        <div class="art-corps">
            <p>Le présent bail est consenti moyennant un loyer mensuel payable d'avance dont le détail est le suivant :</p>
            <table class="tbl-loyer">
                <thead>
                    <tr>
                        <th style="width:55%">Désignation</th>
                        <th class="r" style="width:22%">Montant (FCFA)</th>
                        <th class="r" style="width:23%">Observations</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Loyer {{ $contrat->loyer_assujetti_tva ? 'hors taxes (HT)' : 'nu' }}</td>
                        <td class="r">{{ number_format($contrat->loyer_nu, 0, ',', ' ') }}</td>
                        <td class="obs">Base commission agence</td>
                    </tr>
                    @if($contrat->loyer_assujetti_tva)
                    <tr class="tva">
                        <td>TVA {{ $contrat->taux_tva_loyer ?? 18 }} % — Art. 355 CGI SN (bail commercial)</td>
                        <td class="r">{{ number_format($contrat->loyer_nu * (($contrat->taux_tva_loyer ?? 18)/100), 0, ',', ' ') }}</td>
                        <td class="obs"></td>
                    </tr>
                    @endif
                    @if($contrat->charges_mensuelles)
                    <tr>
                        <td>Charges récupérables</td>
                        <td class="r">{{ number_format($contrat->charges_mensuelles, 0, ',', ' ') }}</td>
                        <td class="obs">{{ $contrat->charges_assujetties_tva ? 'Forfait — TVA applicable' : 'Hors TVA' }}</td>
                    </tr>
                    @endif
                    @if($contrat->tom_amount)
                    <tr>
                        <td>TOM — Taxe sur les Ordures Ménagères</td>
                        <td class="r">{{ number_format($contrat->tom_amount, 0, ',', ' ') }}</td>
                        <td class="obs">Part preneur</td>
                    </tr>
                    @endif
                </tbody>
                <tr class="total">
                    <td><strong>Total loyer mensuel</strong></td>
                    <td class="r"><strong>{{ number_format($loyerTotal, 0, ',', ' ') }} FCFA</strong></td>
                    <td></td>
                </tr>
            </table>
            <div class="en-lettres">
                <div class="en-lettres-lbl">Montant en lettres</div>
                {{ ucfirst($loyerEnLettres) }}
            </div>
            <p style="margin-top:6px">Le loyer est payable le premier de chaque mois.</p>
        </div>
    </div>

    {{-- ARTICLE 4 — DÉPÔT DE GARANTIE --}}
    <div class="article">
        <div class="art-num">Article 4</div>
        <div class="art-titre">Dépôt de garantie</div>
        <div class="art-corps">
            <p>
                À la signature des présentes, le preneur verse la somme de
                <strong>{{ number_format($contrat->caution, 0, ',', ' ') }} FCFA</strong>
                ({{ ucfirst($cautionEnLettres) }})
                à titre de dépôt de garantie représentant {{ $contrat->nombre_mois_caution ?? 1 }} mois de loyer.
                Ce dépôt est restitué en fin de bail, déduction faite des sommes dues.
            </p>
            @if($contrat->caution_gardee_par_agence)
            <p>La caution est conservée en séquestre par {{ $agency->name ?? "l'agence" }} pendant toute la durée du bail.</p>
            @endif
            @if($contrat->frais_agence > 0)
            <p>
                Les frais d'agence s'élèvent à
                <strong>{{ number_format($contrat->frais_agence, 0, ',', ' ') }} FCFA HT</strong>,
                payables à la signature du présent contrat.
            </p>
            @endif
        </div>
    </div>

    {{-- ARTICLE 5 — OBLIGATIONS PRENEUR --}}
    <div class="article">
        <div class="art-num">Article 5</div>
        <div class="art-titre">Obligations du preneur</div>
        <div class="art-corps">
            <p>— Payer le loyer aux termes convenus et en tout lieu indiqué par le bailleur ;</p>
            <p>— User paisiblement des lieux conformément à leur destination contractuelle définie à l'Article 1 ;</p>
            <p>— Ne procéder à aucune transformation, aménagement ou installation sans l'accord écrit du bailleur ;</p>
            <p>— Ne pas sous-louer les lieux, en tout ou partie, sans autorisation préalable et écrite du bailleur ;</p>
            <p>— Ne pas céder le présent bail, ni les droits qui en découlent, sans l'accord écrit et préalable du bailleur ;</p>
            <p>— Souscrire et maintenir en cours de validité, dès la remise des clés, une assurance multirisque
               couvrant les risques locatifs (incendie, dégâts des eaux, responsabilité civile locative)
               et en justifier à toute première demande du bailleur, sous peine de résiliation ;</p>
            <p>— Effectuer les réparations locatives et l'entretien courant à sa charge ;</p>
            <p>— Restituer les lieux en bon état d'entretien et de propreté en fin de bail.</p>
        </div>
    </div>

    {{-- ARTICLE 6 — OBLIGATIONS BAILLEUR --}}
    <div class="article">
        <div class="art-num">Article 6</div>
        <div class="art-titre">Obligations du bailleur</div>
        <div class="art-corps">
            <p>— Délivrer les lieux loués en bon état d'usage et de réparations ;</p>
            <p>— Assurer au preneur la jouissance paisible des lieux pendant toute la durée du bail ;</p>
            <p>— Effectuer les grosses réparations et celles ne relevant pas des obligations du preneur.</p>
        </div>
    </div>

    {{-- ARTICLE 7 — RÉSILIATION --}}
    <div class="article">
        <div class="art-num">Article 7</div>
        <div class="art-titre">Résiliation et préavis</div>
        <div class="art-corps">
            <p>
                En cas de non-paiement du loyer ou des charges à l'échéance, ou d'inexécution par l'une des parties
                de ses obligations contractuelles, le présent bail pourra être résilié de plein droit,
                <strong>un (1) mois</strong> après mise en demeure par lettre recommandée restée sans effet.
            </p>
            @if(in_array($contrat->type_bail, ['habitation', 'saisonnier']))
            <p>
                En dehors du cas de résiliation pour faute, chaque partie peut mettre fin au bail
                en notifiant un préavis par écrit :
                <strong>trois (3) mois</strong> pour le bailleur,
                <strong>un (1) mois</strong> pour le preneur
                (loi n° 81-18 du 30 mars 1981, Art. 17 et 18).
            </p>
            @else
            <p>
                En dehors du cas de résiliation pour faute, chaque partie peut mettre fin au bail
                en notifiant un préavis par écrit :
                <strong>six (6) mois</strong> pour le bailleur,
                <strong>trois (3) mois</strong> pour le preneur
                (loi n° 81-18 du 30 mars 1981 — bail commercial/mixte).
            </p>
            @endif
            <p>
                Tout préavis doit être notifié par lettre recommandée avec accusé de réception
                ou remis en main propre contre décharge écrite.
            </p>
        </div>
    </div>

    {{-- ARTICLE 8 — ÉTAT DES LIEUX --}}
    <div class="article">
        <div class="art-num">Article 8</div>
        <div class="art-titre">État des lieux</div>
        <div class="art-corps">
            <p>
                Un état des lieux contradictoire est dressé contradictoirement à l'entrée et à la sortie du preneur.
                À défaut d'état des lieux établi à l'entrée, les lieux sont présumés remis en bon état.
            </p>
        </div>
    </div>

    {{-- ARTICLE 9 — ENREGISTREMENT DGID --}}
    <div class="article">
        <div class="art-num">Article 9</div>
        <div class="art-titre">Enregistrement obligatoire — DGID</div>
        <div class="art-corps">
            <p>
                Le présent bail est soumis à l'obligation d'enregistrement auprès de la
                <strong>Direction Générale des Impôts et Domaines (DGID)</strong> dans un délai de
                <strong>un (1) mois</strong> à compter de sa date de signature,
                conformément à l'article 442 du Code Général des Impôts du Sénégal.
            </p>
            <p>
                Les droits d'enregistrement sont à la charge du preneur, sauf accord contraire
                entre les parties porté aux clauses particulières du présent bail.
                Le défaut d'enregistrement dans le délai légal expose les parties solidairement
                aux sanctions prévues par le CGI.
            </p>
            @if($contrat->enregistrement_exonere)
            <p>
                <strong>Exonération :</strong> les parties déclarent que le présent bail
                bénéficie d'une exonération des droits d'enregistrement conformément
                aux dispositions applicables.
            </p>
            @endif
        </div>
    </div>

    {{-- ARTICLE 10 — JURIDICTION --}}
    <div class="article">
        <div class="art-num">Article 10</div>
        <div class="art-titre">Élection de domicile et juridiction compétente</div>
        <div class="art-corps">
            <p>
                Les parties élisent domicile à leurs adresses respectives indiquées ci-dessus.
                Tout litige relatif au présent contrat sera de la compétence exclusive des
                juridictions de <strong>{{ $ville }}</strong>, République du Sénégal.
            </p>
        </div>
    </div>

    {{-- CLAUSES GÉNÉRALES AGENCE --}}
    @if($agency->modele_contrat)
    <div class="article">
        <div class="art-num">Clauses générales</div>
        <div class="art-titre">Dispositions générales de {{ $agency->name }}</div>
        <div class="clause-box">{{ $agency->modele_contrat }}</div>
    </div>
    @endif

    {{-- CLAUSES PARTICULIÈRES --}}
    @if($contrat->clauses_particulieres)
    <div class="article">
        <div class="art-num">Clauses particulières</div>
        <div class="art-titre">Dispositions spécifiques au présent bail</div>
        <div class="clause-box clause-part">{{ $contrat->clauses_particulieres }}</div>
    </div>
    @endif

    {{-- DGID --}}
    @if($contrat->date_enregistrement_dgid)
    <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:4px;padding:7px 12px;margin-top:8px;font-size:8.5px;color:#16a34a">
        Bail enregistre a la DGID le <strong>{{ $contrat->date_enregistrement_dgid->format('d/m/Y') }}</strong>
        @if($contrat->numero_quittance_dgid)
        &mdash; N&deg; quittance : {{ $contrat->numero_quittance_dgid }}
        @endif
        @if($contrat->montant_droit_de_bail)
        &mdash; Droit de bail : {{ number_format($contrat->montant_droit_de_bail, 0, ',', ' ') }} FCFA
        @endif
    </div>
    @endif

    {{-- FAIT À --}}
    <div class="fait-a">
        Fait à <strong>{{ $ville }}</strong>, le <strong>{{ ($contrat->created_at ?? now())->format('d/m/Y') }}</strong>,
        en trois (3) exemplaires originaux ayant chacun force originale.
    </div>

    {{-- SIGNATURES --}}
    <div class="sig-table">
        <div class="sig-cell">
            <div class="sig-role">Le Bailleur</div>
            <div class="sig-nom">{{ $nomProp }}</div>
            <div class="sig-detail">Représenté par {{ $agency->name ?? "l'agence" }}</div>
            <div class="sig-espace"></div>
            <div class="tampon">
                <div class="tampon-inner">{{ strtoupper(mb_substr($agency->name ?? 'BIMO', 0, 5)) }}<br>IMMO</div>
            </div>
            <div class="sig-mention">Signature &amp; Cachet — Bon pour accord<br>Lu et approuvé</div>
        </div>
        <div class="sig-spacer"></div>
        <div class="sig-cell">
            <div class="sig-role">Le Preneur</div>
            <div class="sig-nom">{{ $nomLoc }}</div>
            @if($locataire?->telephone)<div class="sig-detail">Tél. {{ $locataire->telephone }}</div>@endif
            <div class="sig-espace"></div>
            <div class="sig-espace"></div>
            <div class="sig-mention">Signature — Bon pour accord<br>Lu et approuvé</div>
        </div>
    </div>

    @if($contrat->garant_nom)
    <div class="sig-table" style="margin-top:12px">
        <div class="sig-cell" style="width:44%">
            <div class="sig-role">Le Garant (Caution solidaire)</div>
            <div class="sig-nom">{{ $contrat->garant_nom }}</div>
            @if($contrat->garant_cni)<div class="sig-detail">CNI : {{ $contrat->garant_cni }}</div>@endif
            <div class="sig-espace"></div>
            <div class="sig-espace"></div>
            <div class="sig-mention">Signature — Bon pour accord<br>Lu et approuvé</div>
        </div>
    </div>
    @endif

    {{-- MENTIONS LÉGALES --}}
    <div class="mentions">
        <div class="mentions-titre">⚖ Mentions légales — République du Sénégal</div>
        Contrat régi par la loi n° 81-18 du 30 mars 1981 relative aux rapports entre bailleurs et locataires.
        @if($contrat->loyer_assujetti_tva) TVA applicable sur le loyer (Art. 355 CGI SN — bail commercial/meublé).@endif
        @if($contrat->brs_applicable) BRS applicable (Art. 201 CGI SN — locataire personne morale).@endif
        Document établi par <strong>{{ $agency->name ?? 'BimoTech Immo' }}</strong> en qualité de Mandataire.
        Toute modification manuelle annule ce document.
    </div>

</div>{{-- /body --}}

</body>
</html>
