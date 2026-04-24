@extends('layouts.app')
@section('title', 'Mon contrat — ' . ($contrat->reference_bail ?? 'Bail'))
@section('breadcrumb', 'Mon contrat')

@section('content')
<style>
.card  { background:#fff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden;margin-bottom:16px; }
.card-hd { padding:14px 20px;border-bottom:1px solid #e5e7eb;display:flex;align-items:center;justify-content:space-between; }
.card-title { font-family:'Syne',sans-serif;font-size:13px;font-weight:700;color:#0d1117; }
.card-body { padding:18px 20px; }
.il { font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:.8px;color:#9ca3af;margin-bottom:4px; }
.iv { font-size:13px;font-weight:500;color:#0d1117; }
.iv-sub { font-size:11px;color:#6b7280;margin-top:2px; }
.info-grid { display:grid;grid-template-columns:1fr 1fr;gap:14px; }
.kpi-row { display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:16px; }
.kpi { background:#fff;border:1px solid #e5e7eb;border-radius:10px;padding:14px 16px; }
.kpi-lbl { font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#9ca3af;margin-bottom:5px; }
.kpi-val { font-family:'Syne',sans-serif;font-size:20px;font-weight:700;color:#0d1117; }
.hero { background:linear-gradient(135deg,#0d1117 0%,#1c2333 100%);border-radius:14px;padding:22px 24px;margin-bottom:20px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px; }
.dt { width:100%;border-collapse:collapse; }
.dt th { padding:9px 16px;text-align:left;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.7px;color:#9ca3af;background:#f9fafb;border-bottom:1px solid #e5e7eb; }
.dt td { padding:11px 16px;font-size:13px;color:#374151;border-bottom:1px solid #f3f4f6;vertical-align:middle; }
.dt tbody tr:last-child td { border-bottom:none; }
</style>

<div style="padding:0 0 48px">

    {{-- Breadcrumb --}}
    <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:#6b7280;margin-bottom:16px">
        <a href="{{ route('locataire.dashboard') }}" style="color:#6b7280;text-decoration:none">Mon espace</a>
        <svg style="width:12px;height:12px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
        <span style="color:#0d1117;font-weight:500">Mon contrat</span>
    </div>

    {{-- Hero --}}
    <div class="hero">
        <div>
            <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.8px;color:rgba(201,168,76,.6);margin-bottom:6px">
                {{ $contrat->reference_bail ?? 'BAIL-'.$contrat->id }}
            </div>
            <div style="font-family:'Syne',sans-serif;font-size:20px;font-weight:700;color:#fff;margin-bottom:4px">
                {{ \App\Models\Contrat::TYPES_BAIL[$contrat->type_bail] ?? $contrat->type_bail }}
                — {{ $contrat->bien?->adresse }}
            </div>
            <div style="font-size:13px;color:rgba(255,255,255,.5)">
                {{ $contrat->bien?->ville }}
                · Réf. bien : {{ $contrat->bien?->reference }}
            </div>
            <div style="margin-top:10px;display:inline-flex;align-items:center;gap:6px;background:rgba(22,163,74,.15);border:1px solid rgba(22,163,74,.3);border-radius:99px;padding:4px 12px">
                <div style="width:7px;height:7px;border-radius:50%;background:#16a34a"></div>
                <span style="font-size:11px;font-weight:600;color:#4ade80">Contrat actif</span>
            </div>
        </div>
        <div style="text-align:right">
            <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:rgba(201,168,76,.6);margin-bottom:4px">Loyer mensuel</div>
            <div style="font-family:'Syne',sans-serif;font-size:28px;font-weight:700;color:#c9a84c">
                {{ number_format($contrat->loyer_contractuel, 0, ',', ' ') }}<span style="font-size:14px;color:rgba(255,255,255,.3);margin-left:4px">F</span>
            </div>
            <div style="font-size:11px;color:rgba(255,255,255,.35);margin-top:3px">
                {{ $contrat->date_debut?->format('d/m/Y') }} → {{ $contrat->date_fin?->format('d/m/Y') ?? 'Durée indéterminée' }}
            </div>
        </div>
    </div>

    {{-- KPIs --}}
    <div class="kpi-row">
        <div class="kpi">
            <div class="kpi-lbl">Total versé</div>
            <div class="kpi-val" style="color:#c9a84c">{{ number_format($totalPaye, 0, ',', ' ') }}<span style="font-size:12px;color:#9ca3af"> F</span></div>
        </div>
        <div class="kpi">
            <div class="kpi-lbl">Paiements</div>
            <div class="kpi-val">{{ $nbPaiements }}</div>
        </div>
        <div class="kpi">
            <div class="kpi-lbl">Prochain loyer</div>
            <div class="kpi-val" style="font-size:15px">{{ $prochainePeriode->translatedFormat('F Y') }}</div>
        </div>
    </div>

    {{-- Détails du bail --}}
    <div class="card">
        <div class="card-hd"><div class="card-title">Détails du bail</div></div>
        <div class="card-body">
            <div class="info-grid">
                <div><div class="il">Type de bail</div><div class="iv">{{ \App\Models\Contrat::TYPES_BAIL[$contrat->type_bail] ?? $contrat->type_bail }}</div></div>
                <div><div class="il">Référence bail</div><div class="iv" style="font-family:monospace;font-size:12px">{{ $contrat->reference_bail ?? 'BAIL-'.$contrat->id }}</div></div>
                <div><div class="il">Date de début</div><div class="iv">{{ $contrat->date_debut?->format('d/m/Y') }}</div></div>
                <div>
                    <div class="il">Date de fin</div>
                    <div class="iv">{{ $contrat->date_fin?->format('d/m/Y') ?? 'Durée indéterminée' }}</div>
                </div>
                <div><div class="il">Loyer nu</div><div class="iv">{{ number_format($contrat->loyer_nu, 0, ',', ' ') }} F</div></div>
                @if($contrat->charges_mensuelles)
                <div><div class="il">Charges mensuelles</div><div class="iv">{{ number_format($contrat->charges_mensuelles, 0, ',', ' ') }} F</div></div>
                @endif
                @if($contrat->tom_amount)
                <div><div class="il">TOM</div><div class="iv">{{ number_format($contrat->tom_amount, 0, ',', ' ') }} F</div></div>
                @endif
                <div><div class="il">Caution versée</div><div class="iv">{{ number_format($contrat->caution, 0, ',', ' ') }} F</div></div>
            </div>
        </div>
    </div>

    {{-- Bien loué --}}
    <div class="card">
        <div class="card-hd"><div class="card-title">Bien loué</div></div>
        <div class="card-body">
            <div class="info-grid">
                <div><div class="il">Référence</div><div class="iv">{{ $contrat->bien?->reference }}</div></div>
                <div><div class="il">Type</div><div class="iv">{{ \App\Models\Bien::TYPES[$contrat->bien?->type] ?? $contrat->bien?->type }}</div></div>
                <div style="grid-column:1/-1"><div class="il">Adresse</div><div class="iv">{{ $contrat->bien?->adresse }}, {{ $contrat->bien?->ville }}</div></div>
                @if($contrat->bien?->surface_m2)
                <div><div class="il">Surface</div><div class="iv">{{ $contrat->bien?->surface_m2 }} m²</div></div>
                @endif
                @if($contrat->bien?->nombre_pieces)
                <div><div class="il">Pièces</div><div class="iv">{{ $contrat->bien?->nombre_pieces }}</div></div>
                @endif
            </div>
        </div>
    </div>

    {{-- Bailleur / Agence --}}
    <div class="card">
        <div class="card-hd"><div class="card-title">Votre gestionnaire</div></div>
        <div class="card-body">
            <div class="info-grid">
                <div>
                    <div class="il">Propriétaire</div>
                    <div class="iv">{{ $contrat->bien?->proprietaire?->name }}</div>
                    @if($contrat->bien?->proprietaire?->telephone)
                    <div class="iv-sub">{{ $contrat->bien->proprietaire->telephone }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Historique paiements --}}
    @if($paiements->count() > 0)
    <div class="card">
        <div class="card-hd">
            <div class="card-title">Historique des paiements</div>
            <a href="{{ route('locataire.paiements') }}" style="font-size:12px;color:#c9a84c;text-decoration:none;font-weight:600">Voir tout →</a>
        </div>
        <div class="card-body" style="padding:0">
            <table class="dt">
                <thead>
                    <tr>
                        <th>Période</th>
                        <th>Date</th>
                        <th>Mode</th>
                        <th style="text-align:right">Montant</th>
                        <th style="text-align:center">Quittance</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($paiements->take(6) as $p)
                    @php $modes = ['especes'=>'Espèces','virement'=>'Virement','cheque'=>'Chèque','wave'=>'Wave','orange_money'=>'Orange Money','free_money'=>'Free Money']; @endphp
                    <tr>
                        <td style="font-weight:600">{{ \Carbon\Carbon::parse($p->periode)->translatedFormat('F Y') }}</td>
                        <td style="color:#6b7280">{{ $p->date_paiement?->format('d/m/Y') ?? '—' }}</td>
                        <td>{{ $modes[$p->mode_paiement] ?? $p->mode_paiement }}</td>
                        <td style="text-align:right;font-weight:700;color:#c9a84c">{{ number_format($p->montant_encaisse, 0, ',', ' ') }} F</td>
                        <td style="text-align:center">
                            <a href="{{ route('locataire.paiements.pdf', $p) }}" target="_blank"
                               style="display:inline-flex;align-items:center;gap:4px;padding:4px 10px;background:#f5e9c9;color:#8a6e2f;border-radius:6px;font-size:11px;font-weight:600;text-decoration:none">
                                PDF
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>
@endsection
