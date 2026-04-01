<?php
// resources/views/locataire/dashboard.blade.php
?>
<x-app-layout>
<x-slot name="header">Mon espace locataire</x-slot>

<div class="section-gap">
    <h1 style="font-size:22px;font-weight:800;color:var(--text);">
        Bonjour, {{ auth()->user()->name }} 👋
    </h1>
    <p style="font-size:13px;color:var(--text-3);margin-top:4px;">
        {{ now()->translatedFormat('l d F Y') }}
    </p>
</div>

@php $hasPaiements = $paiements->isNotEmpty(); @endphp

@php $hasContrat = !is_null($contrat); @endphp

@php $hasProchaine = !is_null($prochainePeriode); @endphp

@php $hasGarant = $hasContrat && !empty($contrat->garant_nom); @endphp

@php $hasCharges = $hasContrat && ($contrat->charges_mensuelles ?? 0) > 0; @endphp

@php $hasSurface = $hasContrat && (!empty($contrat->bien->surface_m2) || !empty($contrat->bien->nombre_pieces)); @endphp

<div style="padding:20px;background:#1a3c5e;border-radius:12px;color:white;margin-bottom:20px;">
    <div style="font-size:11px;opacity:.6;text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px;">Mon logement</div>

    @php
        $reference = $hasContrat ? $contrat->bien->reference : '—';
        $type      = $hasContrat ? $contrat->bien->type : '—';
        $adresse   = $hasContrat ? $contrat->bien->adresse.', '.$contrat->bien->ville : '—';
        $loyer     = $hasContrat ? number_format($contrat->loyer_contractuel, 0, ',', ' ').' FCFA/mois' : '—';
        $debut     = $hasContrat ? \Carbon\Carbon::parse($contrat->date_debut)->format('d/m/Y') : '—';
        $fin       = $hasContrat ? ($contrat->date_fin ? \Carbon\Carbon::parse($contrat->date_fin)->format('d/m/Y') : 'Indéterminée') : '—';
        $typeBail  = $hasContrat ? ucfirst($contrat->type_bail ?? 'habitation') : '—';
        $caution   = $hasContrat ? number_format($contrat->caution, 0, ',', ' ').' FCFA' : '—';
    @endphp

    <div style="font-size:20px;font-weight:800;margin-bottom:4px;">{{ $reference }}</div>
    <div style="font-size:13px;opacity:.8;">{{ $type }} · {{ $adresse }}</div>

    @if($hasSurface)
    <div style="font-size:12px;opacity:.6;margin-top:4px;">
        {{ $contrat->bien->surface_m2 ? $contrat->bien->surface_m2.' m²' : '' }}
        {{ $contrat->bien->nombre_pieces ? '· '.$contrat->bien->nombre_pieces.' pièces' : '' }}
        {{ $contrat->bien->meuble ? '· Meublé' : '' }}
    </div>
    @endif

    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-top:20px;padding-top:16px;border-top:1px solid rgba(255,255,255,.2);">
        <div>
            <div style="font-size:10px;opacity:.5;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Loyer</div>
            <div style="font-size:14px;font-weight:700;">{{ $loyer }}</div>
        </div>
        <div>
            <div style="font-size:10px;opacity:.5;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Début bail</div>
            <div style="font-size:14px;font-weight:700;">{{ $debut }}</div>
        </div>
        <div>
            <div style="font-size:10px;opacity:.5;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Type bail</div>
            <div style="font-size:14px;font-weight:700;">{{ $typeBail }}</div>
        </div>
        <div>
            <div style="font-size:10px;opacity:.5;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Fin bail</div>
            <div style="font-size:14px;font-weight:700;">{{ $fin }}</div>
        </div>
        <div>
            <div style="font-size:10px;opacity:.5;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Caution</div>
            <div style="font-size:14px;font-weight:700;">{{ $caution }}</div>
        </div>
        <div>
            <div style="font-size:10px;opacity:.5;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Statut</div>
            <div style="font-size:12px;font-weight:700;background:rgba(255,255,255,.15);padding:3px 10px;border-radius:999px;display:inline-block;">
                {{ $hasContrat ? 'Actif' : 'Aucun contrat' }}
            </div>
        </div>
    </div>
</div>

@if($hasCharges)
<div style="background:#fffbeb;border:1px solid #fde68a;border-radius:12px;padding:14px 18px;margin-bottom:20px;display:flex;align-items:center;justify-content:space-between;">
    <div style="font-size:13px;font-weight:600;color:#92400e;">Charges mensuelles</div>
    <div style="font-size:15px;font-weight:800;color:#d97706;">{{ number_format($contrat->charges_mensuelles, 0, ',', ' ') }} FCFA</div>
</div>
@endif

<div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:20px;">
    <div class="card" style="padding:16px;text-align:center;">
        <div style="font-size:28px;font-weight:800;color:var(--text);">{{ $stats['nb_paiements'] }}</div>
        <div style="font-size:12px;color:var(--text-2);margin-top:4px;font-weight:600;">Paiements effectués</div>
    </div>
    <div class="card" style="padding:16px;text-align:center;">
        <div style="font-size:18px;font-weight:800;color:#3b82f6;">{{ number_format($stats['total_paye'], 0, ',', ' ') }}</div>
        <div style="font-size:11px;color:#6b7280;margin-top:1px;">FCFA</div>
        <div style="font-size:12px;color:var(--text-2);margin-top:3px;font-weight:600;">Total payé</div>
    </div>
</div>

@if($hasProchaine)
<div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:12px;padding:16px 20px;margin-bottom:20px;display:flex;align-items:center;justify-content:space-between;">
    <div>
        <div style="font-size:12px;font-weight:700;color:#15803d;margin-bottom:2px;">Prochain loyer</div>
        <div style="font-size:11px;color:#16a34a;">{{ \Carbon\Carbon::parse($prochainePeriode)->translatedFormat('F Y') }}</div>
    </div>
    <div style="font-size:18px;font-weight:800;color:#15803d;">
        {{ $hasContrat ? number_format($contrat->loyer_contractuel, 0, ',', ' ').' FCFA' : '—' }}
    </div>
</div>
@endif

@if($hasGarant)
<div class="card" style="padding:16px 20px;margin-bottom:20px;">
    <div style="font-size:12px;font-weight:700;color:var(--text-2);text-transform:uppercase;letter-spacing:.5px;margin-bottom:12px;">Garant</div>
    <div style="font-size:13px;color:var(--text);font-weight:600;">{{ $contrat->garant_nom }}</div>
    <div style="font-size:12px;color:var(--text-3);margin-top:4px;">
        {{ $contrat->garant_telephone ?? '' }}
        {{ $contrat->garant_adresse ? ' · '.$contrat->garant_adresse : '' }}
    </div>
</div>
@endif

<div style="font-size:15px;font-weight:700;color:var(--text);margin-bottom:14px;">Mes paiements</div>

<div class="card">
    @if($hasPaiements)
        @foreach($paiements as $p)
        <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 20px;border-bottom:1px solid var(--border);">
            <div>
                <div style="font-size:13px;font-weight:700;color:var(--text);">
                    {{ \Carbon\Carbon::parse($p->periode)->translatedFormat('F Y') }}
                </div>
                <div style="font-size:11px;color:var(--text-3);margin-top:2px;">
                    {{ \Carbon\Carbon::parse($p->date_paiement)->format('d/m/Y') }}
                    {{ $p->mode_paiement ? ' · '.ucfirst(str_replace('_', ' ', $p->mode_paiement)) : '' }}
                </div>
            </div>
            <div style="display:flex;align-items:center;gap:12px;">
                <div style="text-align:right;">
                    <div style="font-size:14px;font-weight:800;color:var(--text);">{{ number_format($p->montant_encaisse, 0, ',', ' ') }} FCFA</div>
                    <div style="font-size:11px;color:var(--text-3);">{{ $p->reference_paiement ?? '' }}</div>
                </div>
                <a href="{{ route('locataire.paiements.pdf', $p) }}"
                   style="width:32px;height:32px;border-radius:8px;border:1px solid var(--border);background:var(--bg);display:flex;align-items:center;justify-content:center;text-decoration:none;font-size:15px;">
                    📄
                </a>
            </div>
        </div>
        @endforeach

    @else
        <div style="padding:40px;text-align:center;color:var(--text-3);">
            <div style="font-size:32px;margin-bottom:10px;opacity:.4;">💳</div>
            <div style="font-size:14px;">Aucun paiement enregistré.</div>
        </div>
    @endif
</div>

</x-app-layout>