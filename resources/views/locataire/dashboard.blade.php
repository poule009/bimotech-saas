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
    @if($contrat)
        <div class="card section-gap" style="border-color:#bbf7d0;">
            <div class="card-header" style="background:#f0fdf4;">
                <span class="card-title" style="color:#15803d;">✅ Mon logement</span>
                <span class="badge badge-green">Contrat actif</span>
            </div>
            <div class="card-body">
                <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:14px;" id="contrat-grid">

                    <div style="padding:12px;background:var(--bg);border-radius:var(--radius-sm);">
                        <div style="font-size:10px;font-weight:600;color:var(--text-3);text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Bien</div>
                        <div style="font-weight:700;font-size:14px;color:var(--text);">{{ $contrat->bien->reference }}</div>
                        <div style="font-size:12px;color:var(--text-2);margin-top:2px;">{{ $contrat->bien->adresse }}, {{ $contrat->bien->ville }}</div>
                        <div style="font-size:11px;color:var(--text-3);">
                            {{ $contrat->bien->type }}
                            @if($contrat->bien->surface_m2) · {{ $contrat->bien->surface_m2 }} m² @endif
                            @if($contrat->bien->nombre_pieces) · {{ $contrat->bien->nombre_pieces }} pièces @endif
                        </div>
                    </div>

                    <div style="padding:12px;background:var(--bg);border-radius:var(--radius-sm);">
                        <div style="font-size:10px;font-weight:600;color:var(--text-3);text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Mon bail</div>
                        <div style="font-weight:800;font-size:18px;color:var(--text);" class="text-money">
                            {{ number_format($contrat->loyer_contractuel, 0, ',', ' ') }} FCFA/mois
                        </div>
                        <div style="font-size:12px;color:var(--text-2);margin-top:2px;">
                            Depuis le {{ \Carbon\Carbon::parse($contrat->date_debut)->format('d/m/Y') }}
                        </div>
                        <div style="font-size:11px;margin-top:4px;">
                            <span style="display:inline-flex;align-items:center;gap:4px;color:#16a34a;font-weight:600;">
                                <span style="width:6px;height:6px;border-radius:50%;background:#16a34a;display:inline-block;"></span>
                                Contrat actif
                            </span>
                        </div>
                    </div>

                    <div style="padding:12px;background:var(--bg);border-radius:var(--radius-sm);">
                        <div style="font-size:10px;font-weight:600;color:var(--text-3);text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Paiements effectués</div>
                        <div style="font-weight:800;font-size:22px;color:var(--text);">{{ $stats['nb_paiements'] }}</div>
                        <div style="font-size:12px;color:var(--text-2);margin-top:2px;" class="text-money">
                            {{ number_format($stats['total_paye'], 0, ',', ' ') }} F payés
                        </div>
                    </div>

                    @if($prochainePeriode)
                        <div style="padding:12px;background:#fffbeb;border:1px solid #fde68a;border-radius:var(--radius-sm);">
                            <div style="font-size:10px;font-weight:600;color:#92400e;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Prochain loyer</div>
                            <div style="font-weight:700;font-size:16px;color:#d97706;">
                                {{ $prochainePeriode->translatedFormat('F Y') }}
                            </div>
                            <div style="font-size:12px;color:#92400e;margin-top:2px;" class="text-money">
                                {{ number_format($contrat->loyer_contractuel, 0, ',', ' ') }} FCFA attendus
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </div>

        {{-- Dernier paiement --}}
        @if($dernierPaiement)
            <div class="card section-gap">
                <div class="card-header">
                    <span class="card-title">Dernier paiement</span>
                    <a href="{{ route('locataire.paiements.pdf', $dernierPaiement) }}"
                       target="_blank" class="btn btn-secondary btn-sm">
                        📄 Quittance PDF
                    </a>
                </div>
                <div class="card-body">
                    <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px;" id="last-payment-grid">
                        <div>
                            <div style="font-size:10px;font-weight:600;color:var(--text-3);text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Référence</div>
                            <div class="text-ref">{{ $dernierPaiement->reference_paiement }}</div>
                        </div>
                        <div>
                            <div style="font-size:10px;font-weight:600;color:var(--text-3);text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Période</div>
                            <div style="font-weight:600;font-size:13px;color:var(--text);">
                                {{ \Carbon\Carbon::parse($dernierPaiement->periode)->translatedFormat('F Y') }}
                            </div>
                        </div>
                        <div>
                            <div style="font-size:10px;font-weight:600;color:var(--text-3);text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Montant</div>
                            <div style="font-weight:800;font-size:16px;color:var(--text);" class="text-money">
                                {{ number_format($dernierPaiement->montant_encaisse, 0, ',', ' ') }} FCFA
                            </div>
                        </div>
                        <div>
                            <div style="font-size:10px;font-weight:600;color:var(--text-3);text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Mode</div>
                            <div style="font-weight:600;font-size:13px;color:var(--text);">
                                {{ ucfirst(str_replace('_', ' ', $dernierPaiement->mode_paiement)) }}
                            </div>
                        </div>
                    </div>
                    <div style="margin-top:12px;padding:10px 14px;background:#f0fdf4;border-radius:var(--radius-sm);display:flex;align-items:center;gap:8px;">
                        <span style="font-size:14px;">✓</span>
                        <span style="font-size:13px;font-weight:600;color:#16a34a;">Validé</span>
                    </div>
                </div>
            </div>
        @endif

        {{-- Historique --}}
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
                            <span class="mobile-card-label">Montant</span>
                            <span class="text-money" style="font-weight:700;">{{ number_format($p->montant_encaisse, 0, ',', ' ') }} F</span>
                        </div>
                        <div class="mobile-card-row">
                            <span class="mobile-card-label">Mode</span>
                            <span class="mobile-card-value">{{ ucfirst(str_replace('_', ' ', $p->mode_paiement)) }}</span>
                        </div>
                        <div style="margin-top:10px;">
                            <a href="{{ route('locataire.paiements.pdf', $p) }}" target="_blank"
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
                            <th>Référence</th>
                            <th>Période</th>
                            <th>Mode</th>
                            <th>Date règlement</th>
                            <th style="text-align:right;">Montant</th>
                            <th style="text-align:center;">Quittance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($paiements as $p)
                            <tr>
                                <td><span class="text-ref">{{ $p->reference_paiement }}</span></td>
                                <td style="font-weight:600;font-size:13px;">
                                    {{ \Carbon\Carbon::parse($p->periode)->translatedFormat('F Y') }}
                                </td>
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
                                    <a href="{{ route('locataire.paiements.pdf', $p) }}" target="_blank"
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
        </div>

    @else
        <div class="card section-gap" style="text-align:center;padding:64px 24px;">
            <div style="font-size:56px;margin-bottom:16px;opacity:.4;">🏠</div>
            <div style="font-size:17px;font-weight:700;color:var(--text);margin-bottom:8px;">Aucun contrat actif</div>
            <div style="font-size:13px;color:var(--text-3);">Contactez votre agence pour plus d'informations.</div>
        </div>
    @endif

    <style>
        @media (min-width: 640px) {
            #contrat-grid       { grid-template-columns: repeat(4, 1fr); }
            #last-payment-grid  { grid-template-columns: repeat(4, 1fr); }
        }
    </style>

</x-app-layout>