{{--
    Composant : x-fiscal.alerte-dgid
    Affiche le statut d'enregistrement DGID d'un contrat avec alertes.

    Props :
      $contrat — instance de Contrat avec les champs DGID

    Utilisation :
      <x-fiscal.alerte-dgid :contrat="$contrat" />
--}}
@props(['contrat' => null])

{{-- Si aucun contrat n'est fourni (locataire sans bail actif, etc.), on n'affiche rien. --}}
@if($contrat)

@php
    $enregistre   = ! empty($contrat->date_enregistrement_dgid);
    $exonere      = (bool) ($contrat->enregistrement_exonere ?? false);
    $dateDebut    = \Carbon\Carbon::parse($contrat->date_debut);
    $dateLimite   = $dateDebut->copy()->addMonths(2);
    $enRetard     = ! $exonere && ! $enregistre && now()->isAfter($dateLimite);
    $joursRestants = ! $enregistre && ! $exonere ? now()->diffInDays($dateLimite, false) : null;
    $droitEstime  = \App\Services\FiscalService::droitDeBailEstime(
        (float) ($contrat->loyer_nu ?? $contrat->loyer_contractuel),
        $contrat->type_bail ?? 'habitation'
    );
@endphp

@if($exonere)
    {{-- Cas exonéré : aucune alerte --}}
    <div style="display:flex;align-items:center;gap:8px;padding:10px 14px;background:#f0fdf4;border-radius:8px;font-size:12px;color:#16a34a;border:1px solid #bbf7d0">
        <svg style="width:14px;height:14px;flex-shrink:0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        Exonéré d'enregistrement DGID
    </div>

@elseif($enregistre)
    {{-- Enregistré : affichage des infos --}}
    <div style="padding:12px 14px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;font-size:12px">
        <div style="display:flex;align-items:center;gap:6px;font-weight:600;color:#16a34a;margin-bottom:6px">
            <svg style="width:13px;height:13px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
            Enregistré à la DGID
        </div>
        <div style="color:#374151">
            Le <strong>{{ \Carbon\Carbon::parse($contrat->date_enregistrement_dgid)->format('d/m/Y') }}</strong>
            @if($contrat->numero_quittance_dgid)
                · N° {{ $contrat->numero_quittance_dgid }}
            @endif
            @if($contrat->montant_droit_de_bail)
                · {{ number_format($contrat->montant_droit_de_bail, 0, ',', ' ') }} F payés
            @endif
        </div>
    </div>

@elseif($enRetard)
    {{-- En retard : alerte rouge --}}
    <div style="padding:14px;background:#fef2f2;border:1px solid #fecaca;border-radius:8px;font-size:12px">
        <div style="display:flex;align-items:center;gap:6px;font-weight:700;color:#dc2626;margin-bottom:8px">
            <svg style="width:14px;height:14px;flex-shrink:0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            ⚠ Délai d'enregistrement DGID dépassé
        </div>
        <div style="color:#374151;line-height:1.6">
            Ce bail devait être enregistré avant le <strong>{{ $dateLimite->format('d/m/Y') }}</strong>
            (1 mois après entrée en possession — Art. 464 B CGI SN).<br>
            Droit estimé à régulariser : <strong>{{ number_format($droitEstime, 0, ',', ' ') }} F</strong>
            (2% × loyer annuel — Art. 472 IV.6, taux uniforme tous types de bail).
        </div>
        <div style="margin-top:10px">
            <a href="{{ route('admin.contrats.edit', $contrat) }}#dgid"
               style="display:inline-flex;align-items:center;gap:5px;padding:7px 14px;background:#dc2626;color:#fff;border-radius:7px;font-size:12px;font-weight:600;text-decoration:none">
                Régulariser maintenant →
            </a>
        </div>
    </div>

@else
    {{-- À enregistrer, délai non dépassé --}}
    <div style="padding:12px 14px;background:#fffbeb;border:1px solid #fde68a;border-radius:8px;font-size:12px">
        <div style="display:flex;align-items:center;gap:6px;font-weight:600;color:#d97706;margin-bottom:4px">
            <svg style="width:13px;height:13px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            À enregistrer à la DGID
        </div>
        <div style="color:#374151">
            Date limite : <strong>{{ $dateLimite->format('d/m/Y') }}</strong>
            @if($joursRestants !== null && $joursRestants > 0)
                ({{ $joursRestants }}j restants)
            @endif
            · Droit estimé : <strong>{{ number_format($droitEstime, 0, ',', ' ') }} F</strong>
        </div>
    </div>
@endif

@endif {{-- $contrat --}}