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

    $tvaCharges = (float) ($paiement->tva_charges ?? 0);
    $fraisHt    = (float) ($paiement->frais_agence_ht   ?? 0);
    $tvaFrais   = (float) ($paiement->tva_frais_agence  ?? 0);
    $fraisTtc   = (float) ($paiement->frais_agence_ttc  ?? 0);
    $caution    = (float) ($paiement->caution_montant   ?? 0);
    $totalInitial = (float) ($paiement->total_encaissement_initial ?? $total);

    $netLocataire = (float) ($paiement->montant_net_locataire ?? ($totalInitial - $brs));
    $netBailleur  = (float) ($paiement->montant_net_bailleur  ?? $netAVerser);

    $dgidDroits     = (float) ($paiement->dgid_droits_enregistrement ?? 0);
    $dgidTimbre     = (float) ($paiement->dgid_timbre_fiscal         ?? 0);
    $dgidTotal      = (float) ($paiement->dgid_total                 ?? 0);
    $dgidApplicable = $dgidTotal > 0;

    $depenses         = $paiement->relationLoaded('depenses') ? $paiement->depenses : $paiement->depenses()->get();
    $totalDepenses    = (float) $depenses->sum('montant');
    $depensesPresente = $totalDepenses > 0;
    $netFinalBailleur = round($netBailleur - $totalDepenses, 2);

    $loyerAssujetti = $tvaLoyer > 0;
    $brsApplicable  = $brs > 0;
    $estPremier     = ($paiement->est_premier_paiement ?? false)
                      || $fraisTtc > 0 || $caution > 0 || $dgidApplicable;

    $fmt = fn(float $n) => number_format($n, 0, ',', ' ');
@endphp

<table class="w-full border-collapse text-sm">

    {{-- ── LOYER ─────────────────────────────────────── --}}
    <tr class="bg-bimo-bg">
        <td colspan="2" class="px-3.5 py-2 font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40">
            Loyer
            @if($loyerAssujetti)
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-body font-semibold bg-bimo-gold/10 border border-bimo-gold/20 text-bimo-gold ml-1">TVA 18% applicable</span>
            @else
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-body font-semibold bg-bimo-navy/10 border border-bimo-navy/15 text-bimo-navy/60 ml-1">Exonéré TVA</span>
            @endif
        </td>
    </tr>
    <tr class="border-b border-bimo-navy/[5%]">
        <td class="px-3.5 py-2.5 font-body text-sm text-bimo-navy/70">Loyer {{ $loyerAssujetti ? 'HT' : 'nu' }} mensuel{{ $paiement->est_premier_paiement ? ' (proratisé si applicable)' : '' }}</td>
        <td class="px-3.5 py-2.5 text-right font-display font-semibold text-sm text-bimo-navy">{{ $fmt($loyerHt) }} F</td>
    </tr>
    @if($loyerAssujetti)
    <tr class="border-b border-bimo-navy/[5%] bg-bimo-bg">
        <td class="px-3.5 py-2 pl-7 font-body text-xs text-bimo-navy/50">+ TVA loyer (18% — bail commercial/meublé)</td>
        <td class="px-3.5 py-2 text-right font-display font-semibold text-xs text-bimo-navy/50">{{ $fmt($tvaLoyer) }} F</td>
    </tr>
    <tr class="border-b border-bimo-navy/[5%]">
        <td class="px-3.5 py-2.5 font-body font-medium text-sm text-bimo-navy">= Loyer TTC</td>
        <td class="px-3.5 py-2.5 text-right font-display font-bold text-sm text-bimo-navy">{{ $fmt($loyerTtc) }} F</td>
    </tr>
    @endif
    @if($charges > 0)
    <tr class="border-b border-bimo-navy/[5%] bg-bimo-bg">
        <td class="px-3.5 py-2 pl-7 font-body text-xs text-bimo-navy/50">+ Charges locatives récupérables{{ $tvaCharges > 0 ? ' HT' : '' }}</td>
        <td class="px-3.5 py-2 text-right font-display font-semibold text-xs text-bimo-navy/50">{{ $fmt($charges) }} F</td>
    </tr>
    @if($tvaCharges > 0)
    <tr class="border-b border-bimo-navy/[5%] bg-bimo-gold/[4%]">
        <td class="px-3.5 py-2 pl-9 font-body text-xs text-bimo-gold/70">↳ TVA sur charges (18% — forfait — Art. 357 CGI SN)</td>
        <td class="px-3.5 py-2 text-right font-display font-semibold text-xs text-bimo-gold">{{ $fmt($tvaCharges) }} F</td>
    </tr>
    @endif
    @endif
    @if($tom > 0)
    <tr class="border-b border-bimo-navy/[5%] bg-bimo-bg">
        <td class="px-3.5 py-2 pl-7 font-body text-xs text-bimo-navy/50">+ TOM — Taxe sur les Ordures Ménagères</td>
        <td class="px-3.5 py-2 text-right font-display font-semibold text-xs text-bimo-navy/50">{{ $fmt($tom) }} F</td>
    </tr>
    @endif
    <tr class="border-b-2 border-bimo-gold/30 bg-bimo-gold/[8%]">
        <td class="px-3.5 py-3 font-display font-bold text-sm text-bimo-navy">LOYER ENCAISSÉ (mensuel)</td>
        <td class="px-3.5 py-3 text-right font-display font-extrabold text-sm text-bimo-gold">{{ $fmt($total) }} F</td>
    </tr>

    {{-- ── FRAIS D'ENTRÉE ─────────────────────────────── --}}
    @if($estPremier)
    <tr class="bg-bimo-bg">
        <td colspan="2" class="px-3.5 py-2 font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40">
            Frais d'entrée
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-body font-semibold bg-bimo-navy/10 border border-bimo-navy/15 text-bimo-navy/60 ml-1">Premier paiement</span>
        </td>
    </tr>
    @if($fraisTtc > 0)
    <tr class="border-b border-bimo-navy/[5%] bg-[#eff6ff]">
        <td class="px-3.5 py-2.5 font-body text-sm text-[#1d4ed8]">Honoraires d'agence HT</td>
        <td class="px-3.5 py-2.5 text-right font-display font-semibold text-sm text-[#1d4ed8]">{{ $fmt($fraisHt) }} F</td>
    </tr>
    <tr class="border-b border-bimo-navy/[5%] bg-[#eff6ff]">
        <td class="px-3.5 py-2 pl-7 font-body text-xs text-[#1d4ed8]/70">↳ TVA honoraires (18% — art. 364 + 369 CGI SN)</td>
        <td class="px-3.5 py-2 text-right font-display font-semibold text-xs text-[#1d4ed8]">{{ $fmt($tvaFrais) }} F</td>
    </tr>
    <tr class="border-b border-bimo-navy/[5%] bg-[#eff6ff]">
        <td class="px-3.5 py-2.5 font-body font-semibold text-sm text-[#1d4ed8]">= Honoraires TTC</td>
        <td class="px-3.5 py-2.5 text-right font-display font-bold text-sm text-[#1d4ed8]">{{ $fmt($fraisTtc) }} F</td>
    </tr>
    @endif
    @if($caution > 0)
    <tr class="border-b border-bimo-navy/[5%] bg-[#f5f3ff]">
        <td class="px-3.5 py-2.5 font-body text-sm text-[#7c3aed]">Dépôt de garantie (caution)</td>
        <td class="px-3.5 py-2.5 text-right font-display font-semibold text-sm text-[#7c3aed]">{{ $fmt($caution) }} F</td>
    </tr>
    @endif
    <tr class="border-b-2 border-bimo-gold/30 bg-bimo-gold/[8%]">
        <td class="px-3.5 py-3 font-display font-bold text-sm text-bimo-navy">TOTAL FACTURÉ AU LOCATAIRE (loyer + frais + caution)</td>
        <td class="px-3.5 py-3 text-right font-display font-extrabold text-sm text-bimo-gold">{{ $fmt($totalInitial) }} F</td>
    </tr>
    @endif

    {{-- ── DGID ───────────────────────────────────────── --}}
    @if($dgidApplicable)
    <tr class="bg-bimo-bg">
        <td colspan="2" class="px-3.5 py-2 font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40">
            Droits d'enregistrement — DGID
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-body font-semibold bg-[#ede9fe] text-[#7c3aed] ml-1">CGI SN art. 464 B + 472</span>
        </td>
    </tr>
    <tr class="border-b border-[#c4b5fd]/30 bg-[#faf5ff] border-l-4 border-l-bimo-gold">
        <td class="px-3.5 py-2.5">
            <div class="font-body text-sm text-[#6d28d9]">Droits d'enregistrement</div>
            <div class="font-body text-[11px] text-[#7c3aed]/60 mt-0.5">Assiette annuelle (loyer × durée) × taux%</div>
        </td>
        <td class="px-3.5 py-2.5 text-right font-display font-semibold text-sm text-[#7c3aed]">{{ $fmt($dgidDroits) }} F</td>
    </tr>
    <tr class="border-b border-[#c4b5fd]/30 bg-[#faf5ff] border-l-4 border-l-bimo-gold">
        <td class="px-3.5 py-2.5 font-body text-sm text-[#6d28d9]">Timbre fiscal (fixe — CGI SN)</td>
        <td class="px-3.5 py-2.5 text-right font-display font-semibold text-sm text-[#7c3aed]">{{ $fmt($dgidTimbre) }} F</td>
    </tr>
    <tr class="border-b-2 border-[#7c3aed] bg-[#7c3aed] border-l-4 border-l-bimo-gold">
        <td class="px-3.5 py-3">
            <div class="font-display font-bold text-sm text-white">TOTAL FRAIS DGID</div>
            <div class="font-body text-[11px] text-white/50 mt-0.5">À régler directement à la DGID — non inclus dans le loyer</div>
        </td>
        <td class="px-3.5 py-3 text-right font-display font-extrabold text-sm text-[#e9d5ff]">{{ $fmt($dgidTotal) }} F</td>
    </tr>
    @endif

    {{-- ── COMMISSION ────────────────────────────────── --}}
    <tr class="bg-bimo-bg">
        <td colspan="2" class="px-3.5 py-2 font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40">Commission agence — mandat de gestion</td>
    </tr>
    <tr class="border-b border-bimo-navy/[5%]">
        <td class="px-3.5 py-2.5 font-body text-sm text-bimo-navy/70">Commission HT ({{ $paiement->taux_commission_applique }}% sur loyer {{ $loyerAssujetti ? 'HT' : 'nu' }})</td>
        <td class="px-3.5 py-2.5 text-right font-display font-semibold text-sm text-bimo-gold">{{ $fmt($commHt) }} F</td>
    </tr>
    <tr class="border-b border-bimo-navy/[5%] bg-bimo-bg">
        <td class="px-3.5 py-2 pl-7 font-body text-xs text-bimo-navy/50">↳ TVA sur commission (18% — art. 364 + 369 CGI SN)</td>
        <td class="px-3.5 py-2 text-right font-display font-semibold text-xs text-bimo-navy/50">{{ $fmt($tvaComm) }} F</td>
    </tr>
    <tr class="border-b border-bimo-navy/[5%]">
        <td class="px-3.5 py-2.5 font-body font-medium text-sm text-bimo-navy">Commission TTC</td>
        <td class="px-3.5 py-2.5 text-right font-display font-bold text-sm text-bimo-gold">{{ $fmt($commTtc) }} F</td>
    </tr>

    {{-- ── NET PROPRIÉTAIRE ──────────────────────────── --}}
    <tr class="border-b border-white/10 bg-bimo-navy">
        <td class="px-3.5 py-3 font-display font-bold text-sm text-white">NET PROPRIÉTAIRE (avant BRS)</td>
        <td class="px-3.5 py-3 text-right font-display font-extrabold text-sm text-white">{{ $fmt($netProprio) }} F</td>
    </tr>

    {{-- ── BRS ───────────────────────────────────────── --}}
    @if($brsApplicable)
    <tr class="bg-bimo-bg">
        <td colspan="2" class="px-3.5 py-2 font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40">
            Retenue à la Source (BRS)
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-body font-semibold bg-bimo-red/10 border border-bimo-red/20 text-bimo-red ml-1">Locataire entreprise</span>
        </td>
    </tr>
    <tr class="border-b border-bimo-red/20 bg-bimo-red/[5%]">
        <td class="px-3.5 py-2.5 font-body text-sm text-bimo-red">BRS retenue ({{ $tauxBrs }}% × loyer TTC{{ $tom > 0 ? ' + TOM' : '' }} — Art. 201 CGI SN)</td>
        <td class="px-3.5 py-2.5 text-right font-display font-bold text-sm text-bimo-red">- {{ $fmt($brs) }} F</td>
    </tr>
    <tr class="border-b-2 border-bimo-navy bg-bimo-navy/[4%]">
        <td class="px-3.5 py-3 font-display font-bold text-sm text-bimo-navy">NET À VERSER AU PROPRIÉTAIRE (loyer seul)</td>
        <td class="px-3.5 py-3 text-right font-display font-extrabold text-sm text-bimo-navy">{{ $fmt($netAVerser) }} F</td>
    </tr>
    @endif

    {{-- ── SYNTHÈSE ─────────────────────────────────── --}}
    <tr class="bg-bimo-bg">
        <td colspan="2" class="px-3.5 py-2 font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40">Synthèse du versement</td>
    </tr>
    <tr class="border-b border-bimo-gold/30 bg-bimo-navy">
        <td class="px-3.5 py-3">
            <div class="font-display font-bold text-[15px] text-white">NET À PAYER PAR LE LOCATAIRE</div>
            @if($brsApplicable)
            <div class="font-body text-[11px] text-white/40 mt-1">Après retenue BRS de {{ $fmt($brs) }} F (versé directement à la DGI)</div>
            @endif
        </td>
        <td class="px-3.5 py-3 text-right font-display font-extrabold text-[17px] text-bimo-gold">{{ $fmt($netLocataire) }} F</td>
    </tr>
    <tr class="border-b border-bimo-navy/10 bg-bimo-navy/[4%]">
        <td class="px-3.5 py-3">
            <div class="font-display font-bold text-sm text-bimo-navy">SOUS-TOTAL BAILLEUR{{ $depensesPresente ? ' (avant déductions)' : '' }}</div>
            @if($caution > 0)
            <div class="font-body text-[11px] text-bimo-navy/40 mt-0.5">Dont caution {{ $fmt($caution) }} F (dépôt de garantie)</div>
            @endif
        </td>
        <td class="px-3.5 py-3 text-right font-display font-bold text-sm text-bimo-navy">{{ $fmt($netBailleur) }} F</td>
    </tr>

    {{-- ── DÉPENSES & TRAVAUX ─────────────────────────── --}}
    @if($depensesPresente)
    <tr class="bg-bimo-bg">
        <td colspan="2" class="px-3.5 py-2 font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40">
            Dépenses & Travaux
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-body font-semibold bg-bimo-red/10 border border-bimo-red/20 text-bimo-red ml-1">Déduites sur reversement bailleur</span>
        </td>
    </tr>
    @foreach($depenses as $depense)
    <tr class="border-b border-[#fca5a5]/30 bg-[#fff5f5] border-l-4 border-l-[#f87171]">
        <td class="px-3.5 py-2.5">
            <div class="font-body text-sm text-[#b91c1c]">{{ $depense->libelle }}</div>
            <div class="font-body text-[11px] text-[#f87171]/70 mt-0.5">
                {{ \App\Models\DepenseGestion::CATEGORIES[$depense->categorie] ?? $depense->categorie }}
                @if($depense->prestataire) · {{ $depense->prestataire }}@endif
                · {{ \Carbon\Carbon::parse($depense->date_depense)->format('d/m/Y') }}
            </div>
        </td>
        <td class="px-3.5 py-2.5 text-right font-display font-bold text-sm text-[#dc2626]">- {{ $fmt((float) $depense->montant) }} F</td>
    </tr>
    @endforeach
    <tr class="border-b-2 border-bimo-red bg-bimo-red/10 border-l-4 border-l-bimo-red">
        <td class="px-3.5 py-3 font-display font-bold text-sm text-[#991b1b]">Total déductions tiers</td>
        <td class="px-3.5 py-3 text-right font-display font-extrabold text-sm text-bimo-red">- {{ $fmt($totalDepenses) }} F</td>
    </tr>
    @endif

    {{-- ── NET FINAL BAILLEUR ────────────────────────── --}}
    <tr class="border-t-[3px] border-bimo-navy bg-bimo-navy">
        <td class="px-3.5 py-3.5">
            <div class="font-display font-bold text-[15px] text-white">NET FINAL À REVERSER AU BAILLEUR</div>
            @if($depensesPresente)
            <div class="font-body text-[11px] text-white/40 mt-1">{{ $fmt($netBailleur) }} F − {{ $fmt($totalDepenses) }} F déductions</div>
            @endif
        </td>
        <td class="px-3.5 py-3.5 text-right font-display font-extrabold text-[16px] text-[#4ade80]">{{ $fmt($netFinalBailleur) }} F</td>
    </tr>

</table>
