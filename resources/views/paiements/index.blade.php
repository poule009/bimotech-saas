<x-app-layout>
    <x-slot name="header">Paiements</x-slot>

    {{-- Alertes --}}
    @if(session('success'))
        <div class="alert alert-success section-gap">✅ {{ session('success') }}</div>
    @endif

    {{-- Header action --}}
    <div class="flex-between section-gap">
        <div>
            <h1 style="font-size:20px;font-weight:700;color:var(--text);letter-spacing:-.3px;">Paiements</h1>
            <p style="font-size:13px;color:var(--text-3);margin-top:3px;">
                {{ $paiements->total() }} paiement(s) enregistré(s)
            </p>
        </div>
        <a href="{{ route('admin.paiements.create') }}" class="btn btn-primary">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:15px;height:15px;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nouveau paiement
        </a>
    </div>

    {{-- Version mobile : cartes --}}
    <div class="mobile-cards section-gap">
        @forelse($paiements as $p)
            <div class="mobile-card">
                <div class="flex-between" style="margin-bottom:10px;">
                    <span class="text-ref">{{ $p->reference_paiement }}</span>
                    @php
                        $sc = match($p->statut) {
                            'valide'     => 'badge badge-green',
                            'en_attente' => 'badge badge-amber',
                            'annule'     => 'badge badge-red',
                            default      => 'badge badge-gray',
                        };
                        $sl = match($p->statut) {
                            'valide'     => 'Validé',
                            'en_attente' => 'En attente',
                            'annule'     => 'Annulé',
                            default      => ucfirst($p->statut),
                        };
                    @endphp
                    <span class="{{ $sc }}">{{ $sl }}</span>
                </div>
                <div class="mobile-card-row">
                    <span class="mobile-card-label">Locataire</span>
                    <span class="mobile-card-value">{{ $p->contrat->locataire->name }}</span>
                </div>
                <div class="mobile-card-row">
                    <span class="mobile-card-label">Bien</span>
                    <span class="mobile-card-value">{{ $p->contrat->bien->reference }}</span>
                </div>
                <div class="mobile-card-row">
                    <span class="mobile-card-label">Période</span>
                    <span class="mobile-card-value">{{ \Carbon\Carbon::parse($p->periode)->translatedFormat('F Y') }}</span>
                </div>
                <div class="mobile-card-row">
                    <span class="mobile-card-label">Mode</span>
                    <span class="mobile-card-value">{{ ucfirst(str_replace('_', ' ', $p->mode_paiement)) }}</span>
                </div>
                <div style="border-top:1px solid var(--border);margin-top:10px;padding-top:10px;">
                    <div class="mobile-card-row">
                        <span class="mobile-card-label">Loyer encaissé</span>
                        <span class="text-money" style="font-weight:700;font-size:14px;">{{ number_format($p->montant_encaisse, 0, ',', ' ') }} F</span>
                    </div>
                    <div class="mobile-card-row">
                        <span class="mobile-card-label">Commission TTC</span>
                        <span class="text-money" style="color:#d97706;font-weight:600;">{{ number_format($p->commission_ttc, 0, ',', ' ') }} F</span>
                    </div>
                    <div class="mobile-card-row">
                        <span class="mobile-card-label">Net propriétaire</span>
                        <span class="text-money" style="color:#16a34a;font-weight:700;">{{ number_format($p->net_proprietaire, 0, ',', ' ') }} F</span>
                    </div>
                </div>
                <div style="display:flex;gap:8px;margin-top:12px;">
                    <a href="{{ route('admin.paiements.show', $p) }}" class="btn btn-secondary btn-sm" style="flex:1;justify-content:center;">
                        Voir
                    </a>
                    @if($p->statut === 'valide')
                        <a href="{{ route('admin.paiements.pdf', $p) }}" class="btn btn-secondary btn-sm" target="_blank">
                            📄 PDF
                        </a>
                        <form method="POST" action="{{ route('admin.paiements.annuler', $p) }}"
                              onsubmit="return confirm('Annuler ce paiement ?')">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn btn-danger btn-sm">Annuler</button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div style="text-align:center;padding:48px 20px;color:var(--text-3);">
                <div style="font-size:40px;margin-bottom:12px;">💰</div>
                <div style="font-size:14px;">Aucun paiement enregistré</div>
            </div>
        @endforelse
    </div>

    {{-- Version desktop : tableau --}}
    <div class="desktop-table card section-gap">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Référence</th>
                        <th>Locataire</th>
                        <th>Bien</th>
                        <th>Période</th>
                        <th>Mode</th>
                        <th style="text-align:right;">Loyer</th>
                        <th style="text-align:right;">Commission TTC</th>
                        <th style="text-align:right;">Net proprio</th>
                        <th style="text-align:center;">Statut</th>
                        <th style="text-align:center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($paiements as $p)
                        @php
                            $sc = match($p->statut) {
                                'valide'     => 'badge badge-green',
                                'en_attente' => 'badge badge-amber',
                                'annule'     => 'badge badge-red',
                                default      => 'badge badge-gray',
                            };
                            $sl = match($p->statut) {
                                'valide'     => 'Validé',
                                'en_attente' => 'En attente',
                                'annule'     => 'Annulé',
                                default      => ucfirst($p->statut),
                            };
                        @endphp
                        <tr>
                            <td><span class="text-ref">{{ $p->reference_paiement }}</span></td>
                            <td style="font-weight:500;">{{ $p->contrat->locataire->name }}</td>
                            <td>
                                <div style="font-size:13px;color:var(--text);">{{ $p->contrat->bien->reference }}</div>
                                <div style="font-size:11px;color:var(--text-3);">{{ $p->contrat->bien->ville }}</div>
                            </td>
                            <td style="color:var(--text-2);">
                                {{ \Carbon\Carbon::parse($p->periode)->translatedFormat('F Y') }}
                            </td>
                            <td>
                                <span class="badge badge-gray">
                                    {{ ucfirst(str_replace('_', ' ', $p->mode_paiement)) }}
                                </span>
                            </td>
                            <td style="text-align:right;" class="text-money">
                                {{ number_format($p->montant_encaisse, 0, ',', ' ') }} F
                            </td>
                            <td style="text-align:right;">
                                <div class="text-money" style="color:#d97706;font-weight:600;">
                                    {{ number_format($p->commission_ttc, 0, ',', ' ') }} F
                                </div>
                                <div style="font-size:11px;color:var(--text-3);">
                                    TVA: {{ number_format($p->tva_commission, 0, ',', ' ') }} F
                                </div>
                            </td>
                            <td style="text-align:right;" class="text-money">
                                <span style="color:#16a34a;font-weight:700;">
                                    {{ number_format($p->net_proprietaire, 0, ',', ' ') }} F
                                </span>
                            </td>
                            <td style="text-align:center;">
                                <span class="{{ $sc }}">{{ $sl }}</span>
                            </td>
                            <td style="text-align:center;">
                                <div style="display:flex;align-items:center;justify-content:center;gap:6px;">
                                    <a href="{{ route('admin.paiements.show', $p) }}"
                                       class="btn btn-secondary btn-sm">
                                        Voir
                                    </a>
                                    @if($p->statut === 'valide')
                                        <a href="{{ route('admin.paiements.pdf', $p) }}"
                                           class="btn btn-secondary btn-sm"
                                           target="_blank">
                                            📄 PDF
                                        </a>
                                        <form method="POST"
                                              action="{{ route('admin.paiements.annuler', $p) }}"
                                              onsubmit="return confirm('Annuler ce paiement ?')">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                Annuler
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" style="text-align:center;padding:48px;color:var(--text-3);">
                                <div style="font-size:36px;margin-bottom:12px;">💰</div>
                                <div style="font-size:14px;margin-bottom:12px;">Aucun paiement enregistré</div>
                                <a href="{{ route('admin.paiements.create') }}" class="btn btn-primary btn-sm">
                                    Enregistrer le premier paiement
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($paiements->hasPages())
            <div style="padding:16px 20px;border-top:1px solid var(--border);">
                {{ $paiements->links() }}
            </div>
        @endif
    </div>

</x-app-layout>