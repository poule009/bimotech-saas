<x-app-layout>
    <x-slot name="header">Mon espace locataire</x-slot>

    {{-- Header --}}
    <div class="section-gap">
        <h1 style="font-size:20px;font-weight:700;color:var(--text);letter-spacing:-.3px;">
            Bonjour, {{ auth()->user()->name }} 👋
        </h1>
        <p style="font-size:13px;color:var(--text-3);margin-top:3px;">
            Voici un aperçu de votre situation locative
        </p>
    </div>

    {{-- Contrat actif --}}
    @if($contratActif)
        <div class="card section-gap" style="border-color:#bbf7d0;">
            <div class="card-header" style="background:#f0fdf4;">
                <span class="card-title" style="color:#15803d;">✅ Contrat actif</span>
                <span class="badge badge-green">{{ $contratActif->bien->reference }}</span>
            </div>
            <div class="card-body">
                <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:14px;" id="contrat-grid">
                    <div style="padding:12px;background:var(--bg);border-radius:var(--radius-sm);">
                        <div style="font-size:10px;font-weight:600;color:var(--text-3);text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Bien loué</div>
                        <div style="font-weight:700;font-size:14px;color:var(--text);">{{ $contratActif->bien->reference }}</div>
                        <div style="font-size:12px;color:var(--text-2);margin-top:2px;">{{ $contratActif->bien->adresse }}, {{ $contratActif->bien->ville }}</div>
                        <div style="font-size:11px;color:var(--text-3);">{{ $contratActif->bien->type }}</div>
                    </div>
                    <div style="padding:12px;background:var(--bg);border-radius:var(--radius-sm);">
                        <div style="font-size:10px;font-weight:600;color:var(--text-3);text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Loyer mensuel</div>
                        <div style="font-weight:800;font-size:18px;color:var(--text);" class="text-money">
                            {{ number_format($contratActif->loyer_contractuel, 0, ',', ' ') }} FCFA
                        </div>
                    </div>
                    <div style="padding:12px;background:var(--bg);border-radius:var(--radius-sm);">
                        <div style="font-size:10px;font-weight:600;color:var(--text-3);text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Début du bail</div>
                        <div style="font-weight:700;font-size:14px;color:var(--text);">
                            {{ \Carbon\Carbon::parse($contratActif->date_debut)->format('d/m/Y') }}
                        </div>
                    </div>
                    <div style="padding:12px;background:#fffbeb;border:1px solid #fde68a;border-radius:var(--radius-sm);">
                        <div style="font-size:10px;font-weight:600;color:#92400e;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Prochain loyer</div>
                        <div style="font-weight:700;font-size:16px;color:#d97706;">
                            {{ $prochainePeriode->translatedFormat('F Y') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="card section-gap" style="text-align:center;padding:48px 24px;">
            <div style="font-size:48px;margin-bottom:12px;">🏠</div>
            <div style="font-size:16px;font-weight:600;color:var(--text);margin-bottom:6px;">Aucun contrat actif</div>
            <div style="font-size:13px;color:var(--text-3);">Contactez votre agence pour plus d'informations.</div>
        </div>
    @endif

    {{-- KPI --}}
    <div style="display:grid;grid-template-columns:repeat(1,1fr);gap:12px;" class="section-gap" id="locataire-kpi">
        <div class="kpi">
            <div class="kpi-label">Total loyers payés</div>
            <div class="kpi-value text-money" style="font-size:18px;">
                {{ number_format($stats['total_loyers'], 0, ',', ' ') }}
                <span style="font-size:12px;font-weight:500;color:var(--text-3);">FCFA</span>
            </div>
            <div class="kpi-sub">{{ $stats['nb_paiements'] }} paiement(s)</div>
        </div>
        <div class="kpi" style="border-color:#bbf7d0;background:#f0fdf4;">
            <div class="kpi-label" style="color:#22c55e;">Dernier paiement</div>
            @if($stats['dernier_paiement'])
                <div class="kpi-value text-money" style="color:#16a34a;font-size:16px;">
                    {{ number_format($stats['dernier_paiement']->montant_encaisse, 0, ',', ' ') }} F
                </div>
                <div class="kpi-sub" style="color:#22c55e;">
                    {{ \Carbon\Carbon::parse($stats['dernier_paiement']->periode)->translatedFormat('F Y') }}
                </div>
            @else
                <div style="font-size:13px;color:var(--text-3);">Aucun paiement</div>
            @endif
        </div>
        <div class="kpi">
            <div class="kpi-label">Caution versée</div>
            <div class="kpi-value text-money" style="font-size:18px;">
                {{ number_format($stats['caution'], 0, ',', ' ') }}
                <span style="font-size:12px;font-weight:500;color:var(--text-3);">FCFA</span>
            </div>
        </div>
    </div>

    {{-- Mes quittances --}}
    <div class="card section-gap">
        <div class="card-header">
            <span class="card-title">Mes quittances de loyer</span>
        </div>

        {{-- Mobile --}}
        <div class="mobile-cards" style="padding:12px;">
            @forelse($paiements as $p)
                <div class="mobile-card">
                    <div class="flex-between" style="margin-bottom:8px;">
                        <span style="font-size:13px;font-weight:600;color:var(--text);">
                            {{ \Carbon\Carbon::parse($p->periode)->translatedFormat('F Y') }}
                        </span>
                        <span class="badge badge-green">Validé</span>
                    </div>
                    <div class="mobile-card-row">
                        <span class="mobile-card-label">Bien</span>
                        <span class="mobile-card-value">{{ $p->contrat->bien->reference }}</span>
                    </div>
                    <div class="mobile-card-row">
                        <span class="mobile-card-label">Montant</span>
                        <span class="text-money" style="font-weight:700;">{{ number_format($p->montant_encaisse, 0, ',', ' ') }} F</span>
                    </div>
                    <div class="mobile-card-row">
                        <span class="mobile-card-label">Mode</span>
                        <span class="mobile-card-value">{{ ucfirst(str_replace('_', ' ', $p->mode_paiement)) }}</span>
                    </div>
                    <div style="margin-top:10px;">
                        <a href="{{ route('proprietaire.paiements.pdf', $p) }}" target="_blank"
                           class="btn btn-secondary btn-sm" style="width:100%;justify-content:center;">
                            📄 Télécharger quittance
                        </a>
                    </div>
                </div>
            @empty
                <div style="text-align:center;padding:32px;color:var(--text-3);font-size:13px;">
                    Aucune quittance disponible
                </div>
            @endforelse
        </div>

        {{-- Desktop --}}
        <div class="desktop-table table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Période</th>
                        <th>Bien</th>
                        <th>Mode de paiement</th>
                        <th>Date règlement</th>
                        <th style="text-align:right;">Montant</th>
                        <th style="text-align:center;">Quittance</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($paiements as $p)
                        <tr>
                            <td style="font-weight:600;font-size:13px;">
                                {{ \Carbon\Carbon::parse($p->periode)->translatedFormat('F Y') }}
                            </td>
                            <td style="font-size:13px;color:var(--text-2);">{{ $p->contrat->bien->reference }}</td>
                            <td>
                                <span class="badge badge-gray">
                                    {{ ucfirst(str_replace('_', ' ', $p->mode_paiement)) }}
                                </span>
                            </td>
                            <td style="font-size:13px;color:var(--text-2);">
                                {{ \Carbon\Carbon::parse($p->date_paiement)->format('d/m/Y') }}
                            </td>
                            <td style="text-align:right;font-weight:700;" class="text-money">
                                {{ number_format($p->montant_encaisse, 0, ',', ' ') }} F
                            </td>
                            <td style="text-align:center;">
                                <a href="{{ route('proprietaire.paiements.pdf', $p) }}" target="_blank"
                                   class="btn btn-secondary btn-sm">📄 PDF</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center;padding:32px;color:var(--text-3);font-size:13px;">
                                Aucune quittance disponible
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($paiements->hasPages())
            <div style="padding:16px 20px;border-top:1px solid var(--border);">
                {{ $paiements->links() }}
            </div>
        @endif
    </div>

    <style>
        @media (min-width: 640px) {
            #locataire-kpi { grid-template-columns: repeat(3, 1fr); }
            #contrat-grid  { grid-template-columns: repeat(4, 1fr); }
        }
    </style>

</x-app-layout>