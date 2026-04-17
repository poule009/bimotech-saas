{{--
    Composant : x-fiscal.decompte-paiement
    Usage dans les vues web (pas dans les PDF DomPDF)

    Props :
      $paiement  — instance de Paiement avec les colonnes fiscales

    Ce composant centralise TOUTE la logique d'affichage du décompte fiscal.
    Modifier ce fichier met à jour automatiquement paiements/show.blade.php
    et partout où ce composant est utilisé.
--}}
@props(['paiement'])

@php
    $loyerHt    = (float) ($paiement->loyer_ht ?? $paiement->loyer_nu ?? 0);
    $tvaLoyer   = (float) ($paiement->tva_loyer ?? 0);
    $loyerTtc   = (float) ($paiement->loyer_ttc ?? $loyerHt);
    $charges    = (float) ($paiement->charges_amount ?? 0);
    $tom        = (float) ($paiement->tom_amount ?? 0);
    $total      = (float) $paiement->montant_encaisse;
    $commHt     = (float) $paiement->commission_agence;
    $tvaComm    = (float) $paiement->tva_commission;
    $commTtc    = (float) $paiement->commission_ttc;
    $netProprio = (float) $paiement->net_proprietaire;
    $brs        = (float) ($paiement->brs_amount ?? 0);
    $tauxBrs    = (float) ($paiement->taux_brs_applique ?? 0);
    $netAVerser = (float) ($paiement->net_a_verser_proprietaire ?? $netProprio);

    // Frais d'entrée (valeurs non nulles uniquement au premier paiement)
    $fraisHt    = (float) ($paiement->frais_agence_ht   ?? 0);
    $tvaFrais   = (float) ($paiement->tva_frais_agence  ?? 0);
    $fraisTtc   = (float) ($paiement->frais_agence_ttc  ?? 0);
    $caution    = (float) ($paiement->caution_montant   ?? 0);
    $totalInitial = (float) ($paiement->total_encaissement_initial ?? $total);

    // Nets consolidés (calculés par FiscalService, jamais dans les vues)
    $netLocataire = (float) ($paiement->montant_net_locataire ?? ($totalInitial - $brs));
    $netBailleur  = (float) ($paiement->montant_net_bailleur  ?? $netAVerser);

    // DGID — droits d'enregistrement (non nuls uniquement au premier paiement)
    $dgidDroits     = (float) ($paiement->dgid_droits_enregistrement ?? 0);
    $dgidTimbre     = (float) ($paiement->dgid_timbre_fiscal         ?? 0);
    $dgidTotal      = (float) ($paiement->dgid_total                 ?? 0);
    $dgidApplicable = $dgidTotal > 0;

    // Dépenses de gestion (frais tiers engagés par l'agence pour le bailleur)
    // Chargées via paiement->depenses (HasMany DepenseGestion)
    $depenses         = $paiement->relationLoaded('depenses') ? $paiement->depenses : $paiement->depenses()->get();
    $totalDepenses    = (float) $depenses->sum('montant');
    $depensesPresente = $totalDepenses > 0;
    // Net final bailleur = montant_net_bailleur − total dépenses
    $netFinalBailleur = round($netBailleur - $totalDepenses, 2);

    $loyerAssujetti = $tvaLoyer > 0;
    $brsApplicable  = $brs > 0;
    // Premier paiement : au moins un des indicateurs d'entrée est non nul
    $estPremier     = ($paiement->est_premier_paiement ?? false)
                      || $fraisTtc > 0 || $caution > 0 || $dgidApplicable;

    $fmt = fn(float $n) => number_format($n, 0, ',', ' ');
@endphp

<style>
.fd-table { width:100%;border-collapse:collapse;font-size:13px; }
.fd-table tr { border-bottom:1px solid #f3f4f6; }
.fd-table td { padding:10px 14px;vertical-align:middle; }
.fd-table td:last-child { text-align:right;font-family:'Syne',sans-serif;font-weight:600; }
.fd-group-header td { font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:#9ca3af;background:#f9fafb;padding:8px 14px; }
.fd-row-total td { background:#f5e9c9;font-weight:700;font-size:14px;color:#0d1117;border-top:2px solid #c9a84c; }
.fd-row-net td { background:#0d1117;color:#fff;font-size:14px;font-weight:700;border-top:none; }
.fd-row-net td:last-child { color:#4ade80; }
.fd-row-brs td { background:#fef2f2;color:#dc2626;font-size:13px; }
.fd-row-averser td { background:#f0fdf4;color:#16a34a;font-size:14px;font-weight:700;border-top:2px solid #16a34a; }
.fd-row-locataire td { background:#0d1117;color:#fff;font-size:15px;font-weight:700;border-top:2px solid #c9a84c; }
.fd-row-locataire td:last-child { color:#c9a84c;font-size:17px; }
.fd-row-bailleur td { background:#f0fdf4;color:#16a34a;font-size:14px;font-weight:700; }
.fd-sub td { font-size:12px;color:#6b7280;padding-left:28px; }
.fd-sub td:last-child { color:#8a6e2f; }
.fd-row-frais td { background:#eff6ff;color:#1d4ed8;font-size:13px; }
.fd-row-caution td { background:#f5f3ff;color:#7c3aed;font-size:13px; }
.fd-badge { display:inline-flex;align-items:center;gap:4px;padding:2px 8px;border-radius:99px;font-size:10px;font-weight:600; }
.fd-badge-green { background:#dcfce7;color:#16a34a; }
.fd-badge-orange { background:#f5e9c9;color:#8a6e2f; }
.fd-badge-red { background:#fee2e2;color:#dc2626; }
.fd-badge-blue   { background:#dbeafe;color:#1d4ed8; }
.fd-badge-purple { background:#ede9fe;color:#7c3aed; }
.fd-row-dgid     td { background:#faf5ff;color:#6d28d9;font-size:13px;border-left:3px solid #c9a84c; }
.fd-row-dgid     td:last-child { color:#7c3aed;font-weight:700; }
.fd-row-dgid-total td { background:#7c3aed;color:#fff;font-size:14px;font-weight:700;border-left:3px solid #c9a84c; }
.fd-row-dgid-total td:last-child { color:#e9d5ff;font-size:15px; }
.fd-row-depense  td { background:#fff5f5;color:#b91c1c;font-size:12px;border-left:3px solid #f87171; }
.fd-row-depense  td:last-child { color:#dc2626;font-weight:700; }
.fd-row-depense-total td { background:#fee2e2;color:#991b1b;font-size:13px;font-weight:700;border-left:3px solid #dc2626;border-top:1px solid #fca5a5; }
.fd-row-depense-total td:last-child { font-size:14px; }
.fd-row-net-final td { background:#0d1117;color:#fff;font-size:15px;font-weight:700;border-top:3px solid #16a34a; }
.fd-row-net-final td:last-child { color:#4ade80;font-size:16px; }
</style>

<table class="fd-table">
    {{-- ── LOYER ─────────────────────────────────────── --}}
    <tr class="fd-group-header">
        <td colspan="2">
            Loyer
            @if($loyerAssujetti)
                <span class="fd-badge fd-badge-orange">TVA 18% applicable</span>
            @else
                <span class="fd-badge fd-badge-green">Exonéré TVA</span>
            @endif
        </td>
    </tr>

    <tr>
        <td>Loyer {{ $loyerAssujetti ? 'HT' : 'nu' }} mensuel{{ $paiement->est_premier_paiement ? ' (proratisé si applicable)' : '' }}</td>
        <td>{{ $fmt($loyerHt) }} F</td>
    </tr>

    @if($loyerAssujetti)
    <tr class="fd-sub">
        <td>+ TVA loyer (18% — bail commercial/meublé)</td>
        <td>{{ $fmt($tvaLoyer) }} F</td>
    </tr>
    <tr>
        <td style="font-weight:500">= Loyer TTC</td>
        <td>{{ $fmt($loyerTtc) }} F</td>
    </tr>
    @endif

    @if($charges > 0)
    <tr class="fd-sub">
        <td>+ Charges locatives récupérables</td>
        <td>{{ $fmt($charges) }} F</td>
    </tr>
    @endif

    @if($tom > 0)
    <tr class="fd-sub">
        <td>+ TOM — Taxe sur les Ordures Ménagères</td>
        <td>{{ $fmt($tom) }} F</td>
    </tr>
    @endif

    <tr class="fd-row-total">
        <td>LOYER ENCAISSÉ (mensuel)</td>
        <td>{{ $fmt($total) }} F</td>
    </tr>

    {{-- ── FRAIS D'ENTRÉE (premier paiement uniquement) ─── --}}
    @if($estPremier)
    <tr class="fd-group-header">
        <td colspan="2">
            Frais d'entrée
            <span class="fd-badge fd-badge-blue">Premier paiement</span>
        </td>
    </tr>

    @if($fraisTtc > 0)
    <tr class="fd-row-frais">
        <td>Honoraires d'agence HT</td>
        <td>{{ $fmt($fraisHt) }} F</td>
    </tr>
    <tr class="fd-sub" style="background:#eff6ff">
        <td>↳ TVA honoraires (18% — art. 357 CGI SN)</td>
        <td style="color:#1d4ed8">{{ $fmt($tvaFrais) }} F</td>
    </tr>
    <tr class="fd-row-frais">
        <td style="font-weight:600">= Honoraires TTC</td>
        <td style="font-weight:700">{{ $fmt($fraisTtc) }} F</td>
    </tr>
    @endif

    @if($caution > 0)
    <tr class="fd-row-caution">
        <td>Dépôt de garantie (caution)</td>
        <td>{{ $fmt($caution) }} F</td>
    </tr>
    @endif

    <tr class="fd-row-total">
        <td>TOTAL FACTURÉ AU LOCATAIRE (loyer + frais + caution)</td>
        <td>{{ $fmt($totalInitial) }} F</td>
    </tr>
    @endif

    {{-- ── DROITS D'ENREGISTREMENT DGID ────────────────── --}}
    {{-- Gardés dans un bloc séparé : obligation fiscale distincte, --}}
    {{-- non incluse dans montant_encaisse ni net_locataire.        --}}
    {{-- Condition triple garantie : avecDgid + DB + ici.          --}}
    @if($dgidApplicable)
    <tr class="fd-group-header">
        <td colspan="2">
            Droits d'enregistrement — DGID
            <span class="fd-badge fd-badge-purple">CGI SN art. 442</span>
            <span class="fd-badge" style="background:#fef3c7;color:#92400e">Premier versement</span>
        </td>
    </tr>
    <tr class="fd-row-dgid">
        <td>
            Droits d'enregistrement
            <div style="font-size:11px;color:#7c3aed;opacity:.7;margin-top:2px">
                Assiette annuelle (loyer × durée) × taux%
            </div>
        </td>
        <td>{{ $fmt($dgidDroits) }} F</td>
    </tr>
    <tr class="fd-row-dgid">
        <td>Timbre fiscal (fixe — CGI SN)</td>
        <td>{{ $fmt($dgidTimbre) }} F</td>
    </tr>
    <tr class="fd-row-dgid-total">
        <td>
            TOTAL FRAIS DGID
            <div style="font-size:11px;font-weight:400;color:rgba(255,255,255,.55);margin-top:2px">
                À régler directement à la DGID — non inclus dans le loyer
            </div>
        </td>
        <td>{{ $fmt($dgidTotal) }} F</td>
    </tr>
    @endif

    {{-- ── COMMISSION ────────────────────────────────── --}}
    <tr class="fd-group-header">
        <td colspan="2">Commission agence — mandat de gestion</td>
    </tr>

    <tr>
        <td>Commission HT ({{ $paiement->taux_commission_applique }}% sur loyer {{ $loyerAssujetti ? 'HT' : 'nu' }})</td>
        <td style="color:#8a6e2f">{{ $fmt($commHt) }} F</td>
    </tr>
    <tr class="fd-sub">
        <td>↳ TVA sur commission (18% — art. 357 CGI SN)</td>
        <td>{{ $fmt($tvaComm) }} F</td>
    </tr>
    <tr>
        <td style="font-weight:500">Commission TTC</td>
        <td style="color:#d97706">{{ $fmt($commTtc) }} F</td>
    </tr>

    {{-- ── NET PROPRIÉTAIRE (avant BRS) ──────────────── --}}
    <tr class="fd-row-net">
        <td>NET PROPRIÉTAIRE (avant BRS)</td>
        <td>{{ $fmt($netProprio) }} F</td>
    </tr>

    {{-- ── BRS (affiché seulement si applicable) ─────── --}}
    @if($brsApplicable)
    <tr class="fd-group-header">
        <td colspan="2">
            Retenue à la Source (BRS)
            <span class="fd-badge fd-badge-red">Locataire entreprise</span>
        </td>
    </tr>
    <tr class="fd-row-brs">
        <td>BRS retenue ({{ $tauxBrs }}% × loyer HT — art. 196bis CGI SN)</td>
        <td style="font-weight:700">- {{ $fmt($brs) }} F</td>
    </tr>
    <tr class="fd-row-averser">
        <td>NET À VERSER AU PROPRIÉTAIRE (loyer seul)</td>
        <td>{{ $fmt($netAVerser) }} F</td>
    </tr>
    @endif

    {{-- ── LIGNES DE SYNTHÈSE ─────────────────────────── --}}
    <tr class="fd-group-header">
        <td colspan="2">Synthèse du versement</td>
    </tr>

    {{-- Net locataire : ce que le locataire verse effectivement --}}
    <tr class="fd-row-locataire">
        <td>
            NET À PAYER PAR LE LOCATAIRE
            @if($brsApplicable)
                <div style="font-size:11px;font-weight:400;color:rgba(255,255,255,.5);margin-top:2px">
                    Après retenue BRS de {{ $fmt($brs) }} F (versé directement à la DGI)
                </div>
            @endif
        </td>
        <td>{{ $fmt($netLocataire) }} F</td>
    </tr>

    {{-- Sous-total bailleur (avant déduction des dépenses) --}}
    <tr class="fd-row-bailleur">
        <td>
            SOUS-TOTAL BAILLEUR{{ $depensesPresente ? ' (avant déductions)' : '' }}
            @if($caution > 0)
                <div style="font-size:11px;font-weight:400;color:#16a34a;opacity:.75;margin-top:2px">
                    Dont caution {{ $fmt($caution) }} F (dépôt de garantie)
                </div>
            @endif
        </td>
        <td>{{ $fmt($netBailleur) }} F</td>
    </tr>

    {{-- ── DÉPENSES & TRAVAUX ─────────────────────────── --}}
    {{-- Frais engagés par l'agence pour le compte du propriétaire.  --}}
    {{-- N'impactent JAMAIS le montant payé par le locataire.        --}}
    @if($depensesPresente)
    <tr class="fd-group-header">
        <td colspan="2">
            Dépenses & Travaux
            <span class="fd-badge fd-badge-red">Déduites sur reversement bailleur</span>
        </td>
    </tr>

    @foreach($depenses as $depense)
    <tr class="fd-row-depense">
        <td>
            {{ $depense->libelle }}
            <div style="font-size:11px;color:#f87171;opacity:.8;margin-top:2px">
                {{ \App\Models\DepenseGestion::CATEGORIES[$depense->categorie] ?? $depense->categorie }}
                @if($depense->prestataire)
                    · {{ $depense->prestataire }}
                @endif
                · {{ \Carbon\Carbon::parse($depense->date_depense)->format('d/m/Y') }}
            </div>
        </td>
        <td>- {{ $fmt((float) $depense->montant) }} F</td>
    </tr>
    @endforeach

    <tr class="fd-row-depense-total">
        <td>Total déductions tiers</td>
        <td>- {{ $fmt($totalDepenses) }} F</td>
    </tr>
    @endif

    {{-- Net final bailleur : sous-total − dépenses --}}
    <tr class="fd-row-net-final">
        <td>
            NET FINAL À REVERSER AU BAILLEUR
            @if($depensesPresente)
                <div style="font-size:11px;font-weight:400;color:rgba(255,255,255,.5);margin-top:2px">
                    {{ $fmt($netBailleur) }} F − {{ $fmt($totalDepenses) }} F déductions
                </div>
            @endif
        </td>
        <td>{{ $fmt($netFinalBailleur) }} F</td>
    </tr>

</table>
