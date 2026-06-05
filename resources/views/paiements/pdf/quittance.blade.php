<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title>Quittance {{ $paiement->reference_paiement }}</title>
<style>
/* ══════════════════════════════════════════════════════
   BASE — DomPDF : DejaVu Sans uniquement
   Aucun flexbox ni grid — display:table uniquement
══════════════════════════════════════════════════════ */
* { margin:0; padding:0; box-sizing:border-box; }

body {
    font-family: "DejaVu Sans", Arial, sans-serif;
    font-size: 10px;
    color: #1a202c;
    background: #fff;
    line-height: 1.5;
}

.watermark {
    position: fixed;
    top: 42%; left: 50%;
    transform: translate(-50%, -50%) rotate(-38deg);
    font-size: 62px; font-weight: bold;
    color: rgba(13,17,23,0.03);
    letter-spacing: 12px; text-transform: uppercase;
    white-space: nowrap; pointer-events: none; z-index: 0;
}

/* ── EN-TÊTE ── */
.header { background:#1B4F6B; padding:0; }
.header-inner { display:table; width:100%; padding:20px 28px; }
.header-left  { display:table-cell; width:60%; vertical-align:middle; }
.header-right { display:table-cell; width:40%; vertical-align:middle; text-align:right; }
.agence-nom { font-size:19px; font-weight:bold; color:#fff; letter-spacing:.3px; margin-bottom:2px; }
.agence-sub { font-size:8px; color:rgba(255,255,255,.4); letter-spacing:2px; text-transform:uppercase; margin-bottom:5px; }
.agence-pill { display:inline-block; background:rgba(201,168,76,.15); border:1px solid rgba(201,168,76,.3); border-radius:4px; padding:2px 9px; font-size:8px; color:#c9a84c; letter-spacing:.5px; }
.agence-coords { font-size:9px; color:rgba(255,255,255,.5); line-height:1.8; margin-bottom:5px; }
.ninea-pill { display:inline-block; background:rgba(201,168,76,.12); border:1px solid rgba(201,168,76,.25); border-radius:3px; padding:2px 8px; font-size:8px; font-weight:bold; color:#c9a84c; }

/* ── BANDE TITRE ── */
.title-band { display:table; width:100%; background:#f5e9c9; border-left:5px solid #c9a84c; padding:10px 28px; }
.title-left  { display:table-cell; vertical-align:middle; }
.title-right { display:table-cell; vertical-align:middle; text-align:right; }
.doc-title { font-size:13px; font-weight:bold; text-transform:uppercase; letter-spacing:2.5px; color:#1a2e3b; }
.doc-periode { font-size:10px; color:#8a6e2f; margin-top:2px; font-weight:bold; }
.ref-label { font-size:7px; text-transform:uppercase; letter-spacing:.8px; color:#8a6e2f; margin-bottom:1px; }
.ref-value { font-size:10px; font-weight:bold; color:#1a2e3b; }

/* ── BADGE RÉGIME FISCAL ── */
.regime-band { display:table; width:100%; background:#f9fafb; border-bottom:1px solid #e5e7eb; padding:7px 28px; }
.regime-badge { display:inline-block; padding:3px 10px; border-radius:99px; font-size:8px; font-weight:bold; }
.regime-habitation { background:#dcfce7; color:#16a34a; }
.regime-commercial  { background:#fef3c7; color:#d97706; }
.regime-brs         { background:#fee2e2; color:#dc2626; margin-left:6px; }

/* ── BANDEAU BAIL ── */
.bail-band { display:table; width:100%; background:#f9fafb; border-top:1px solid #e5e7eb; border-bottom:1px solid #e5e7eb; }
.bail-cell { display:table-cell; padding:7px 20px; border-right:1px solid #e5e7eb; vertical-align:middle; }
.bail-cell:first-child { padding-left:28px; }
.bail-cell:last-child  { border-right:none; padding-right:28px; }
.bail-label { font-size:7px; text-transform:uppercase; letter-spacing:.8px; color:#9ca3af; margin-bottom:2px; }
.bail-value { font-size:10px; font-weight:bold; color:#1a2e3b; }

/* ── CORPS ── */
.body { padding:18px 28px; }

/* ── SECTION ── */
.section-title { font-size:8px; font-weight:bold; text-transform:uppercase; letter-spacing:1px; color:#6b7280; border-bottom:1px solid #e5e7eb; padding-bottom:4px; margin-bottom:10px; margin-top:14px; }
.section-title:first-child { margin-top:0; }

/* ── GRILLE 2 COL ── */
.grid2 { display:table; width:100%; }
.col   { display:table-cell; width:50%; vertical-align:top; padding-right:18px; }
.col:last-child { padding-right:0; padding-left:18px; }
.col-label { font-size:8px; text-transform:uppercase; letter-spacing:.8px; color:#9ca3af; margin-bottom:2px; }
.col-value { font-size:11px; font-weight:bold; color:#1a2e3b; margin-bottom:2px; }
.col-sub   { font-size:9px; color:#6b7280; line-height:1.6; }

/* ── PARTIE (locataire / propriétaire) ── */
.partie-box { background:#f9fafb; border:1px solid #e5e7eb; border-radius:4px; padding:10px 12px; }
.partie-role  { font-size:7.5px; font-weight:bold; text-transform:uppercase; letter-spacing:.8px; color:#c9a84c; margin-bottom:4px; }
.partie-name  { font-size:11px; font-weight:bold; color:#1a2e3b; margin-bottom:2px; }
.partie-sub   { font-size:9px; color:#6b7280; line-height:1.6; }

/* ── BIEN ── */
.bien-box { background:#f9fafb; border:1px solid #e5e7eb; border-left:4px solid #c9a84c; border-radius:0 4px 4px 0; padding:10px 12px; }

/* ══════════════════════════════════════════════════════
   TABLEAU DÉCOMPTE FINANCIER
══════════════════════════════════════════════════════ */
.montant-table { width:100%; border-collapse:collapse; font-size:10px; margin-top:6px; }
.montant-table th { background:#1B4F6B; color:#fff; padding:7px 12px; text-align:left; font-size:8px; text-transform:uppercase; letter-spacing:.8px; }
.montant-table th:last-child { text-align:right; }
.montant-table td { padding:7px 12px; border-bottom:1px solid #f3f4f6; color:#374151; vertical-align:middle; }
.montant-table td:last-child { text-align:right; font-weight:bold; color:#1a2e3b; white-space:nowrap; }

.row-loyer      td { background:#fff; }
.row-tva-loyer  td { font-size:9.5px; color:#d97706; background:#fffbeb; }
.row-loyer-ttc  td { background:#fef3c7; font-weight:bold; font-size:11px; color:#92400e; border-top:1.5px solid #d97706; border-bottom:1.5px solid #d97706; }
.row-charges    td { font-size:9.5px; color:#6b7280; background:#fafafa; }
.row-tom        td { font-size:9.5px; color:#6b7280; background:#fafafa; }
.row-total      td { background:#f5e9c9; font-weight:bold; font-size:11px; color:#1a2e3b; border-top:2px solid #c9a84c; border-bottom:2px solid #c9a84c; }
.row-sep        th { background:#f3f4f6; color:#6b7280; font-size:7.5px; padding:5px 12px; letter-spacing:.5px; }
.row-comm       td { font-size:9.5px; }
.row-comm       td:last-child { color:#8a6e2f; }
.row-tva-comm   td { font-size:9px; color:#9ca3af; font-style:italic; background:#fafafa; }
.row-tva-comm   td:last-child { color:#9ca3af; font-weight:normal; }
.row-comm-ttc   td { color:#d97706; font-size:10px; }
.row-net        td { background:#1B4F6B; color:#fff; font-size:12px; font-weight:bold; padding:10px 12px; }
.row-net        td:last-child { color:#4ade80; }
.row-brs-sep    th { background:#fee2e2; color:#dc2626; font-size:7.5px; padding:5px 12px; letter-spacing:.5px; }
.row-brs        td { background:#fef2f2; font-weight:bold; font-size:10.5px; color:#dc2626; border-top:1.5px solid #fecaca; }
.row-brs        td:last-child { color:#dc2626; }
.row-net-averser td { background:#0a3d0a; color:#4ade80; font-size:12px; font-weight:bold; padding:10px 12px; border-top:none; }
.row-net-averser td:last-child { color:#6ee7b7; font-size:13px; }
.row-brs-loc    td { background:#fff1f2; font-size:9.5px; color:#dc2626; }
.row-brs-loc    td:last-child { color:#dc2626; font-weight:bold; }
.row-net-loc    td { background:#1B4F6B; color:#fff; font-size:11px; font-weight:bold; padding:9px 12px; }
.row-net-loc    td:last-child { color:#c9a84c; font-size:12px; }

/* ── MONTANTS EN LETTRES ── */
.lettres-bloc { background:#fffbeb; border:1px solid #fde68a; border-radius:4px; padding:8px 12px; margin-top:7px; }
.lettres-label { font-size:7.5px; text-transform:uppercase; letter-spacing:.8px; color:#d97706; margin-bottom:2px; }
.lettres-value { font-size:10px; font-style:italic; font-weight:bold; color:#78350f; }
.lettres-green { background:#f0fdf4; border-color:#86efac; margin-top:5px; }
.lettres-green .lettres-label { color:#16a34a; }
.lettres-green .lettres-value { color:#14532d; }
.lettres-blue { background:#f0f4ff; border-color:#c7d2fe; margin-top:5px; }
.lettres-blue .lettres-label { color:#3730a3; }
.lettres-blue .lettres-value { color:#1e1b4b; }

/* Caution */
.caution-bloc { background:#eff6ff; border-left:4px solid #3b82f6; border-radius:0 4px 4px 0; padding:9px 12px; margin-top:7px; font-size:9.5px; color:#1e3a8a; line-height:1.6; }

/* Règlement */
.reglement-bloc { display:table; width:100%; background:#f9fafb; border:1px solid #e5e7eb; border-radius:4px; padding:9px 12px; margin-top:6px; }
.reg-col { display:table-cell; width:50%; vertical-align:top; }
.valide-pill { display:inline-block; background:#dcfce7; border:1px solid #86efac; border-radius:99px; padding:2px 9px; font-size:8px; font-weight:bold; color:#16a34a; margin-top:4px; }

/* ── MENTIONS LÉGALES ── */
.mentions { background:#fff7ed; border:1px solid #fed7aa; border-radius:4px; padding:9px 12px; margin:12px 28px 0; font-size:7.5px; color:#9a3412; line-height:1.7; }
.mentions-titre { font-size:7.5px; font-weight:bold; text-transform:uppercase; letter-spacing:.5px; margin-bottom:4px; color:#7c2d12; }

/* ── PIED DE PAGE ── */
.footer { margin:12px 28px 20px; padding-top:10px; border-top:1px solid #e5e7eb; }
.footer-grid { display:table; width:100%; border-collapse:separate; border-spacing:8px 0; }
.footer-col-legal { display:table-cell; width:55%; vertical-align:bottom; }
.footer-col-doc   { display:table-cell; width:20%; vertical-align:bottom; text-align:center; }
.footer-col-sign  { display:table-cell; width:25%; vertical-align:bottom; text-align:center; }
.legal-text { font-size:7px; color:#9ca3af; line-height:1.7; }
.doc-ref-box { border:1px solid #e5e7eb; border-radius:4px; padding:8px; background:#f9fafb; }
.doc-ref-label { font-size:7px; color:#9ca3af; text-transform:uppercase; letter-spacing:.5px; margin-bottom:3px; }
.doc-ref-value { font-size:8px; font-weight:bold; color:#374151; word-break:break-all; }
.sign-box { border:1.5px dashed #d1d5db; border-radius:5px; padding:12px 8px 7px; text-align:center; }
.tampon { width:58px; height:58px; border:2.5px solid #1B4F6B; border-radius:50%; margin:0 auto 5px; display:table; }
.tampon-inner { display:table-cell; vertical-align:middle; font-size:7px; font-weight:bold; color:#1a2e3b; line-height:1.4; text-align:center; text-transform:uppercase; }
.sign-label { font-size:7px; color:#9ca3af; margin-top:4px; }

/* ── ALERTE DGID ── */
.dgid-enregistre { background:#f0fdf4; border:1px solid #bbf7d0; border-radius:4px; padding:7px 12px; margin-top:8px; font-size:8.5px; color:#16a34a; }
</style>
</head>
<body>

@php
// ═══════════════════════════════════════════════════════════════════════════
// NORMALISATION — accepte tous les appelants (PaiementController, QuittanceService)
// ═══════════════════════════════════════════════════════════════════════════

// Destinataire : locataire | proprietaire | agence (défaut)
$destinataire  = $destinataire ?? 'agence';
$pourLocataire = $destinataire === 'locataire';

// Agence : accepte objet Eloquent ($agence ou $agency) OU tableau associatif
$agenceRaw = $agence ?? $agency ?? null;
if (!$agenceRaw) {
    $agence = ['nom' => config('app.name'), 'adresse' => '', 'telephone' => '', 'email' => '', 'ninea' => '', 'rccm' => '', 'logo_path' => '', 'logo_dark_path' => ''];
} elseif (is_object($agenceRaw)) {
    $agence = [
        'nom'            => $agenceRaw->name ?? config('app.name'),
        'adresse'        => $agenceRaw->adresse ?? '',
        'telephone'      => $agenceRaw->telephone ?? '',
        'email'          => $agenceRaw->email ?? '',
        'ninea'          => $agenceRaw->ninea ?? '',
        'rccm'           => '',
        'logo_path'      => $agenceRaw->logo_path ?? '',
        'logo_dark_path' => $agenceRaw->logo_dark_path ?? '',
    ];
} else {
    $agence = $agenceRaw;
}

// Relations — fournies par le contrôleur OU dérivées du paiement
$contrat       = $contrat       ?? $paiement->contrat;
$bien          = $bien          ?? $contrat?->bien;
$locataire     = $locataire     ?? $contrat?->locataire;
$proprietaire  = $proprietaire  ?? $bien?->proprietaire;
$referenceBail = $referenceBail ?? $paiement->reference_bail ?? $contrat?->reference_bail ?? ('BAIL-' . $contrat?->id);

// Régime fiscal — calculé à partir du snapshot si non fourni
if (!isset($regime_fiscal)) {
    $snapshot = $paiement->regime_fiscal_snapshot;
    $snapshotData = $snapshot ? (is_array($snapshot) ? $snapshot : json_decode((string) $snapshot, true)) : [];
    $regimeFiscalKey = $snapshotData['regime_fiscal']
        ?? (((float)($paiement->tva_loyer ?? 0) > 0)
            ? (((float)($paiement->brs_amount ?? 0) > 0) ? 'commercial_avec_brs' : 'commercial')
            : (((float)($paiement->brs_amount ?? 0) > 0) ? 'habitation_avec_brs' : 'habitation'));
    $regimeFiscalLabels = [
        'habitation'          => 'Habitation — Exonéré TVA (Art. 355 CGI SN)',
        'habitation_avec_brs' => 'Habitation — BRS applicable (Art. 201 CGI SN)',
        'commercial'          => 'Commercial — TVA 18% (Art. 355 CGI SN)',
        'commercial_avec_brs' => 'Commercial — TVA 18% + BRS (Art. 355 & 201 CGI SN)',
    ];
    $regime_fiscal     = $regimeFiscalLabels[$regimeFiscalKey] ?? ucfirst(str_replace('_', ' ', $regimeFiscalKey));
    $loyer_assujetti   = (float)($paiement->tva_loyer ?? 0) > 0;
    $brs_applicable    = (float)($paiement->brs_amount ?? 0) > 0;
    $taux_brs_applique = (float)($paiement->taux_brs_applique ?? 0);
} else {
    $loyer_assujetti   = $loyer_assujetti   ?? false;
    $brs_applicable    = $brs_applicable    ?? false;
    $taux_brs_applique = $taux_brs_applique ?? 0.0;
}

// Montants en lettres (admin / propriétaire uniquement)
if (!$pourLocataire) {
    $montantEnLettres    = $montantEnLettres    ?? \App\Services\FiscalService::montantEnLettresFr((float) $paiement->montant_encaisse);
    $loyerNuEnLettres    = $loyerNuEnLettres    ?? \App\Services\FiscalService::montantEnLettresFr((float) ($paiement->loyer_ht ?? $paiement->loyer_nu ?? 0));
    $netAVerserEnLettres = $netAVerserEnLettres ?? \App\Services\FiscalService::montantEnLettresFr((float) ($paiement->net_a_verser_proprietaire ?? $paiement->net_proprietaire ?? 0));
    $netEnLettres        = $netEnLettres        ?? \App\Services\FiscalService::montantEnLettresFr((float) ($paiement->net_proprietaire ?? 0));
}

// Nom affiché (entreprise ou particulier)
$locataireProfile = $locataire?->locataire ?? null;
$estEntreprise    = (bool) ($locataireProfile?->est_entreprise ?? false);
$nomQuittance     = ($estEntreprise && $locataireProfile?->nom_entreprise)
                    ? $locataireProfile->nom_entreprise
                    : ($locataire?->name ?? '—');

// Logo en base64
$logoSrc    = null;
$logoInvert = false;
$dp = !empty($agence['logo_dark_path']) ? storage_path('app/public/' . $agence['logo_dark_path']) : null;
$lp = !empty($agence['logo_path'])      ? storage_path('app/public/' . $agence['logo_path'])      : null;
if ($dp && file_exists($dp)) {
    $logoSrc = 'data:' . mime_content_type($dp) . ';base64,' . base64_encode(file_get_contents($dp));
} elseif ($lp && file_exists($lp)) {
    $logoSrc    = 'data:' . mime_content_type($lp) . ';base64,' . base64_encode(file_get_contents($lp));
    $logoInvert = true;
}

$fmt = fn(float $n) => number_format($n, 0, ',', ' ');
@endphp

<div class="watermark">QUITTANCE OFFICIELLE</div>

{{-- ══ EN-TÊTE ══════════════════════════════════════════════════════════ --}}
<div class="header">
    <div class="header-inner">
        <div class="header-left">
            @if($logoSrc)
                <img src="{{ $logoSrc }}" style="height:40px;max-width:160px;object-fit:contain;display:block;margin-bottom:5px;{{ $logoInvert ? 'filter:brightness(0) invert(1);opacity:.9' : '' }}">
            @else
                <div class="agence-nom">{{ $agence['nom'] }}</div>
            @endif
            <div class="agence-sub">Agence Immobilière · Gestion Locative</div>
            <div class="agence-pill">Mandataire du Propriétaire Bailleur</div>
        </div>
        <div class="header-right">
            <div class="agence-coords">
                {{ $agence['adresse'] }}<br>
                @if($agence['telephone']) Tél : {{ $agence['telephone'] }}<br>@endif
                {{ $agence['email'] }}
            </div>
            @if($agence['ninea'])<div class="ninea-pill">NINEA : {{ $agence['ninea'] }}</div>@endif
            @if(!empty($agence['rccm']))<div style="margin-top:3px;font-size:8px;color:rgba(255,255,255,.35)">RCCM : {{ $agence['rccm'] }}</div>@endif
        </div>
    </div>
</div>

{{-- ══ BANDE TITRE ═══════════════════════════════════════════════════════ --}}
<div class="title-band">
    <div class="title-left">
        <div class="doc-title">
            @if($destinataire === 'proprietaire') Avis de versement
            @else Quittance de Loyer
            @endif
        </div>
        <div class="doc-periode">Période : {{ \Carbon\Carbon::parse($paiement->periode)->translatedFormat('F Y') }}</div>
    </div>
    <div class="title-right">
        <div class="ref-label">N° Quittance</div>
        <div class="ref-value">{{ $paiement->reference_paiement }}</div>
        <div class="ref-label" style="margin-top:5px">Date d'émission</div>
        <div class="ref-value">{{ now()->format('d/m/Y') }}</div>
    </div>
</div>

{{-- ══ BADGE RÉGIME FISCAL — masqué pour le locataire ═══════════════════ --}}
@if(!$pourLocataire)
<div class="regime-band">
    @if($loyer_assujetti)
        <span class="regime-badge regime-commercial">{{ $regime_fiscal }}</span>
    @else
        <span class="regime-badge regime-habitation">{{ $regime_fiscal }}</span>
    @endif
    @if(config('features.fiscalite') && $brs_applicable)
        <span class="regime-badge regime-brs">BRS {{ $taux_brs_applique }}% — Locataire Entreprise</span>
    @endif
</div>
@endif

{{-- ══ BANDEAU BAIL ═══════════════════════════════════════════════════════ --}}
<div class="bail-band">
    <div class="bail-cell" style="width:32%">
        <div class="bail-label">Référence du bail</div>
        <div class="bail-value">{{ $referenceBail }}</div>
    </div>
    <div class="bail-cell" style="width:22%">
        <div class="bail-label">Date de signature</div>
        <div class="bail-value">{{ \Carbon\Carbon::parse($contrat->date_debut)->format('d/m/Y') }}</div>
    </div>
    <div class="bail-cell" style="width:22%">
        <div class="bail-label">Type de bail</div>
        <div class="bail-value">{{ ucfirst($contrat->type_bail ?? 'habitation') }}</div>
    </div>
    <div class="bail-cell" style="width:24%">
        <div class="bail-label">Échéance</div>
        <div class="bail-value">{{ $contrat->date_fin ? \Carbon\Carbon::parse($contrat->date_fin)->format('d/m/Y') : 'Indéterminée' }}</div>
    </div>
</div>

{{-- ══ CORPS ══════════════════════════════════════════════════════════════ --}}
<div class="body">

    {{-- PARTIES --}}
    <div class="section-title">Parties au contrat</div>
    <div class="grid2">
        <div class="col">
            <div class="partie-box">
                <div class="partie-role">Bailleur (Propriétaire)</div>
                <div class="partie-name">{{ $proprietaire?->name ?? '—' }}</div>
                <div class="partie-sub">
                    {{ $proprietaire?->adresse ?? 'Adresse non renseignée' }}<br>
                    Tél : {{ $proprietaire?->telephone ?? 'Non renseigné' }}<br>
                    <em>Représenté par {{ $agence['nom'] }}</em>
                </div>
            </div>
        </div>
        <div class="col" style="padding-left:16px;padding-right:0">
            <div class="partie-box">
                <div class="partie-role">
                    {{ $estEntreprise ? 'Locataire (Personne Morale)' : 'Locataire (Preneur)' }}
                </div>
                <div class="partie-name">{{ $nomQuittance }}</div>
                <div class="partie-sub">
                    @if($estEntreprise && $locataireProfile?->ninea_locataire)
                        NINEA : {{ $locataireProfile->ninea_locataire }}<br>
                    @endif
                    @if($estEntreprise && $locataireProfile?->rccm_locataire)
                        RCCM : {{ $locataireProfile->rccm_locataire }}<br>
                    @endif
                    @if($estEntreprise) Représentant : {{ $locataire?->name }}<br>@endif
                    Tél : {{ $locataire?->telephone ?? 'Non renseigné' }}
                </div>
            </div>
        </div>
    </div>

    {{-- BIEN --}}
    <div class="section-title">Bien loué</div>
    <div class="bien-box">
        <div style="font-size:11px;font-weight:bold;color:#1a2e3b;margin-bottom:2px">
            {{ \App\Models\Bien::TYPES[$bien?->type] ?? $bien?->type }} — Réf. {{ $bien?->reference }}
            @if($bien?->meuble) &nbsp;·&nbsp; <em>Meublé</em> @endif
        </div>
        <div style="font-size:9.5px;color:#374151">
            {{ $bien?->adresse }}{{ $bien?->quartier ? ', '.$bien->quartier : '' }}{{ $bien?->commune ? ', '.$bien->commune : '' }}{{ $bien?->ville ? ', '.$bien->ville : '' }}
            @if($bien?->surface_m2) &nbsp;·&nbsp; {{ $bien->surface_m2 }} m²@endif
            @if($bien?->nombre_pieces) &nbsp;·&nbsp; {{ $bien->nombre_pieces }} pièce(s)@endif
        </div>
    </div>

    {{-- DÉCOMPTE FINANCIER --}}
    <div class="section-title">
        Décompte financier — {{ \Carbon\Carbon::parse($paiement->periode)->translatedFormat('F Y') }}
    </div>

    @php
        $loyerHt    = (float) ($loyer_ht       ?? $paiement->loyer_ht       ?? $paiement->loyer_nu  ?? 0);
        $tvaLoyer   = (float) ($tva_loyer      ?? $paiement->tva_loyer      ?? 0);
        $loyerTtc   = (float) ($loyer_ttc      ?? $paiement->loyer_ttc      ?? $loyerHt);
        $charges    = (float) ($charges_amount ?? $paiement->charges_amount ?? 0);
        $tvaCharges = (float) ($tva_charges    ?? $paiement->tva_charges    ?? 0);
        $tom        = (float) ($tom_amount     ?? $paiement->tom_amount     ?? 0);
        $totalEnc   = (float) $paiement->montant_encaisse;
        $commHt     = (float) ($paiement->commission_agence ?? 0);
        $tvaComm    = (float) ($paiement->tva_commission    ?? 0);
        $commTtc    = (float) ($paiement->commission_ttc    ?? 0);
        $netProprio = (float) ($paiement->net_proprietaire  ?? 0);
        $brsAmt     = (float) ($brs_amount     ?? $paiement->brs_amount     ?? 0);
        $netAVerser = (float) ($net_a_verser   ?? $paiement->net_a_verser_proprietaire ?? $netProprio);
        $brsApplic  = config('features.fiscalite') && $brsAmt > 0;
        $netLoc     = (float) ($paiement->montant_net_locataire ?? ($totalEnc - $brsAmt));
    @endphp

    <table class="montant-table">
        <tr>
            <th style="width:75%">Désignation</th>
            <th style="width:25%">Montant (FCFA)</th>
        </tr>

        {{-- Loyer HT --}}
        <tr class="row-loyer">
            <td>Loyer {{ $loyer_assujetti ? 'HT' : 'nu' }} mensuel
                @if(!$loyer_assujetti)
                    <span style="font-size:8px;color:#9ca3af;font-style:italic;font-weight:normal">(exonéré TVA — habitation nue)</span>
                @endif
            </td>
            <td>{{ $fmt($loyerHt) }}</td>
        </tr>

        {{-- TVA loyer (bail commercial/meublé uniquement) --}}
        @if($loyer_assujetti)
        <tr class="row-tva-loyer">
            <td>&nbsp;&nbsp;&nbsp;+ TVA loyer (18% — Art. 355 CGI SN — bail commercial/meublé)</td>
            <td>{{ $fmt($tvaLoyer) }}</td>
        </tr>
        <tr class="row-loyer-ttc">
            <td>= Loyer TTC</td>
            <td>{{ $fmt($loyerTtc) }}</td>
        </tr>
        @endif

        {{-- Charges --}}
        @if($charges > 0)
        <tr class="row-charges">
            <td>&nbsp;&nbsp;&nbsp;+ Charges locatives récupérables{{ $tvaCharges > 0 ? ' HT' : ' (Art. 356 al.2 — hors TVA)' }}</td>
            <td>{{ $fmt($charges) }}</td>
        </tr>
        @if($tvaCharges > 0)
        <tr class="row-tva-loyer" style="color:#92400e;background:#fff7ed">
            <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;↳ TVA sur charges (18% — forfait — Art. 357 CGI SN — bail commercial/meublé)</td>
            <td>{{ $fmt($tvaCharges) }}</td>
        </tr>
        @endif
        @endif

        {{-- TOM --}}
        @if($tom > 0)
        <tr class="row-tom">
            <td>&nbsp;&nbsp;&nbsp;+ TOM — Taxe sur les Ordures Ménagères (taxe municipale — hors TVA)</td>
            <td>{{ $fmt($tom) }}</td>
        </tr>
        @endif

        {{-- Total encaissé --}}
        <tr class="row-total">
            <td>TOTAL ENCAISSÉ PAR L'AGENCE</td>
            <td>{{ $fmt($totalEnc) }}</td>
        </tr>

        {{-- COMMISSION + NET + BRS — masqué pour le locataire --}}
        @if(!$pourLocataire)
        <tr class="row-sep">
            <th colspan="2">
                Décompte commission d'agence (assiette : loyer {{ $loyer_assujetti ? 'HT' : 'nu' }} — hors TVA loyer, hors charges, hors TOM)
            </th>
        </tr>
        <tr class="row-comm">
            <td>Commission HT ({{ $paiement->taux_commission_applique }}% × loyer {{ $loyer_assujetti ? 'HT' : 'nu' }})</td>
            <td>{{ $fmt($commHt) }}</td>
        </tr>
        <tr class="row-tva-comm">
            <td>&nbsp;&nbsp;&nbsp;↳ TVA sur commission (18% — Art. 357 CGI SN)</td>
            <td>{{ $fmt($tvaComm) }}</td>
        </tr>
        <tr class="row-comm-ttc">
            <td>&nbsp;&nbsp;&nbsp;↳ Commission TTC (HT + TVA)</td>
            <td>{{ $fmt($commTtc) }}</td>
        </tr>
        <tr class="row-net">
            <td>NET {{ $brsApplic ? 'PROPRIÉTAIRE (avant BRS)' : 'REVERSÉ AU PROPRIÉTAIRE' }}</td>
            <td>{{ $fmt($netProprio) }}</td>
        </tr>
        @if($brsApplic)
        <tr class="row-brs-sep">
            <th colspan="2">
                Retenue à la Source (BRS) — Locataire Personne Morale (Art. 201 CGI SN)
                — Retenue opérée par : {{ $nomQuittance }}
                @if($locataireProfile?->ninea_locataire) — NINEA : {{ $locataireProfile->ninea_locataire }}@endif
            </th>
        </tr>
        <tr class="row-brs">
            <td>
                BRS ({{ $taux_brs_applique }}% × {{ $tom > 0 ? '(loyer TTC + TOM)' : 'loyer TTC' }} = {{ $fmt($loyerTtc + $tom) }} FCFA — Art. 201 CGI SN)
                <span style="font-size:8px;font-style:italic;font-weight:normal">— Versée directement à la DGI par le locataire</span>
            </td>
            <td>- {{ $fmt($brsAmt) }}</td>
        </tr>
        <tr class="row-net-averser">
            <td>NET À VERSER AU PROPRIÉTAIRE (après BRS)</td>
            <td>{{ $fmt($netAVerser) }}</td>
        </tr>
        @endif

        @else
        {{-- Locataire : afficher la retenue BRS si applicable --}}
        @if($brsApplic)
        <tr class="row-brs-sep">
            <th colspan="2">Retenue à la Source (BRS) — Art. 201 CGI SN</th>
        </tr>
        <tr class="row-brs-loc">
            <td>
                BRS à retenir et verser à la DGI ({{ $taux_brs_applique }}% × loyer TTC — Art. 201 CGI SN)
            </td>
            <td>- {{ $fmt($brsAmt) }}</td>
        </tr>
        <tr class="row-net-loc">
            <td>NET À PAYER AU PROPRIÉTAIRE (après BRS)</td>
            <td>{{ $fmt($netLoc) }}</td>
        </tr>
        @endif
        @endif
    </table>

    {{-- MONTANTS EN LETTRES — admin / propriétaire uniquement --}}
    @if(!$pourLocataire)
    <div class="lettres-bloc">
        <div class="lettres-label">Total encaissé en lettres</div>
        <div class="lettres-value">{{ $montantEnLettres }}</div>
    </div>
    @if($loyer_assujetti || $charges > 0 || $tom > 0)
    <div class="lettres-bloc lettres-blue">
        <div class="lettres-label">Loyer {{ $loyer_assujetti ? 'HT' : 'nu' }} en lettres</div>
        <div class="lettres-value">{{ $loyerNuEnLettres }}</div>
    </div>
    @endif
    @if($brsApplic)
    <div class="lettres-bloc lettres-green">
        <div class="lettres-label">Net à verser au propriétaire en lettres</div>
        <div class="lettres-value">{{ $netAVerserEnLettres }}</div>
    </div>
    @else
    <div class="lettres-bloc lettres-green">
        <div class="lettres-label">Net reversé au propriétaire en lettres</div>
        <div class="lettres-value">{{ $netEnLettres }}</div>
    </div>
    @endif
    @endif

    {{-- CAUTION 1er paiement --}}
    @if($paiement->est_premier_paiement && ($paiement->caution_percue ?? 0) > 0)
    <div class="caution-bloc">
        <strong>Reçu pour solde — Caution (1er paiement) :</strong>
        {{ number_format($paiement->caution_percue, 0, ',', ' ') }} FCFA
        — Restituable en fin de bail sous réserve d'état des lieux de sortie.
    </div>
    @endif

    {{-- MODE DE RÈGLEMENT --}}
    <div class="section-title">Mode de règlement</div>
    <div class="reglement-bloc">
        <div class="reg-col">
            <div class="col-label">Mode de paiement</div>
            <div class="col-value">{{ ucfirst(str_replace('_', ' ', $paiement->mode_paiement)) }}</div>
            <div class="valide-pill">✓ Paiement validé</div>
        </div>
        <div class="reg-col">
            <div class="col-label">Date de règlement</div>
            <div class="col-value">{{ \Carbon\Carbon::parse($paiement->date_paiement)->format('d/m/Y') }}</div>
            @if($paiement->notes)
            <div class="col-sub" style="margin-top:4px;font-style:italic">Note : {{ $paiement->notes }}</div>
            @endif
        </div>
    </div>

    {{-- ENREGISTREMENT DGID — masqué pour le locataire --}}
    @if(!$pourLocataire)
    @if($contrat->date_enregistrement_dgid)
    <div class="dgid-enregistre">
        ✓ Bail enregistré à la DGID le {{ \Carbon\Carbon::parse($contrat->date_enregistrement_dgid)->format('d/m/Y') }}
        @if($contrat->numero_quittance_dgid) — N° {{ $contrat->numero_quittance_dgid }} @endif
    </div>
    @elseif(($paiement->dgid_total ?? 0) > 0)
    <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:4px;padding:7px 12px;margin-top:8px;font-size:8.5px;color:#dc2626">
        ⚠ Droits d'enregistrement DGID calculés ({{ number_format($paiement->dgid_total, 0, ',', ' ') }} FCFA) mais aucune date d'enregistrement renseignée.
        L'enregistrement à la DGID est obligatoire (Art. 442 CGI SN) — à régulariser.
    </div>
    @endif
    @endif

</div>{{-- /body --}}

{{-- ══ MENTIONS LÉGALES ═══════════════════════════════════════════════════ --}}
<div class="mentions">
    <div class="mentions-titre">⚖ Mentions légales — République du Sénégal</div>
    Quittance délivrée conformément à la loi n° 81-18 du 30/03/1981 et au bail
    <strong>{{ $referenceBail }}</strong> signé le <strong>{{ \Carbon\Carbon::parse($contrat->date_debut)->format('d/m/Y') }}</strong>.
    Atteste que <strong>{{ $nomQuittance }}</strong> s'est acquitté(e) du loyer de
    <strong>{{ \Carbon\Carbon::parse($paiement->periode)->translatedFormat('F Y') }}</strong>
    pour le bien sis <strong>{{ $bien?->adresse }}@if($bien?->quartier), {{ $bien->quartier }}@endif{{ $bien?->ville ? ', '.$bien->ville : '' }}</strong>.
    @if($loyer_assujetti) TVA collectée sur loyer (Art. 355 CGI SN) : <strong>{{ number_format($tvaLoyer, 0, ',', ' ') }} FCFA</strong>. @endif
    @if($brsApplic) BRS retenue à la source (Art. 201 CGI SN) : <strong>{{ number_format($brsAmt, 0, ',', ' ') }} FCFA</strong> — versée à la DGI par le locataire. @endif
    Ce paiement ne vaut pas renonciation aux créances antérieures.
    <strong>{{ $agence['nom'] }}</strong> agit en qualité de Mandataire du Propriétaire.
    Commission calculée sur loyer nu (hors TVA loyer, hors charges, hors TOM).
    Document généré le {{ now()->format('d/m/Y à H:i') }}.
</div>

{{-- ══ PIED DE PAGE ═══════════════════════════════════════════════════════ --}}
<div class="footer">
    <div class="footer-grid">
        <div class="footer-col-legal">
            <div class="legal-text">
                <strong>{{ $agence['nom'] }}</strong><br>
                {{ $agence['adresse'] }}@if($agence['ninea']) · {{ $agence['ninea'] }}@endif<br>
                @if($agence['telephone']) Tél : {{ $agence['telephone'] }}@endif @if($agence['email']) · {{ $agence['email'] }}@endif<br><br>
                Conservez ce document. Il fait foi devant les juridictions sénégalaises.
                Toute modification manuelle l'annule.
            </div>
        </div>
        <div class="footer-col-doc">
            <div class="doc-ref-box">
                <div class="doc-ref-label">Réf. quittance</div>
                <div class="doc-ref-value">{{ $paiement->reference_paiement }}</div>
                <div class="doc-ref-label" style="margin-top:5px">Bail</div>
                <div class="doc-ref-value">{{ $referenceBail }}</div>
                <div class="doc-ref-label" style="margin-top:5px">Émise le</div>
                <div class="doc-ref-value">{{ now()->format('d/m/Y') }}</div>
            </div>
        </div>
        <div class="footer-col-sign">
            <div class="sign-box">
                <div class="tampon">
                    <div class="tampon-inner">{{ strtoupper(mb_substr($agence['nom'], 0, 5)) }}<br>IMMO</div>
                </div>
                <div class="sign-label">Signature &amp; Cachet<br>du Mandataire</div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
