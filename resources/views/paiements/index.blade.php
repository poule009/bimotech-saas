<x-app-layout>
    <x-slot name="header">Journal des Paiements</x-slot>

    {{-- Alertes Premium --}}
    @if(session('success'))
        <div style="background: rgba(22, 163, 74, 0.1); border: 1px solid rgba(22, 163, 74, 0.2); color: #16a34a; padding: 16px 24px; border-radius: 16px; margin-bottom: 24px; font-weight: 600; display: flex; align-items: center; gap: 12px;">
            <span style="font-size: 20px;">✓</span> {{ session('success') }}
        </div>
    @endif

    {{-- Header action : Minimalisme & Impact --}}
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 32px; gap: 20px; flex-wrap: wrap;">
        <div>
            <h1 style="font-size: 24px; font-weight: 900; color: #1a202c; letter-spacing: -1px;">Historique des Flux</h1>
            <p style="font-size: 13px; color: #b58c5a; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; margin-top: 4px;">
                {{ $paiements->total() }} transaction(s) certifiée(s)
            </p>
        </div>
        <a href="{{ route('admin.paiements.create') }}" class="btn btn-primary" style="background: #1a202c; padding: 14px 28px; border-radius: 14px; box-shadow: 0 10px 20px rgba(0,0,0,0.1);">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:18px; height:18px; margin-right: 8px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
            Nouveau Paiement
        </a>
    </div>

    {{-- Version mobile : Cartes type "Reçu" --}}
    <div class="mobile-cards section-gap">
        @forelse($paiements as $p)
            <div class="mobile-card" style="background: white; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.04); border-radius: 24px; padding: 24px; margin-bottom: 16px;">
                <div class="flex-between" style="margin-bottom: 16px;">
                    <span style="font-family: monospace; font-weight: 800; color: #a0aec0; font-size: 12px;">#{{ $p->reference_paiement }}</span>
                    @php
                        $statusStyles = match($p->statut) {
                            'valide'    => ['bg' => '#f0fdf4', 'text' => '#16a34a', 'label' => 'Validé'],
                            'en_attente'=> ['bg' => '#fffbeb', 'text' => '#d97706', 'label' => 'En attente'],
                            'annule'    => ['bg' => '#fef2f2', 'text' => '#dc2626', 'label' => 'Annulé'],
                            default     => ['bg' => '#f8fafc', 'text' => '#64748b', 'label' => ucfirst($p->statut)],
                        };
                    @endphp
                    <span style="background: {{ $statusStyles['bg'] }}; color: {{ $statusStyles['text'] }}; padding: 4px 12px; border-radius: 8px; font-size: 11px; font-weight: 800; text-transform: uppercase;">
                        {{ $statusStyles['label'] }}
                    </span>
                </div>

                <div style="margin-bottom: 20px;">
                    <div style="font-size: 16px; font-weight: 800; color: #1a202c;">{{ $p->contrat->locataire->name }}</div>
                    <div style="font-size: 13px; color: #718096; margin-top: 2px;">{{ $p->contrat->bien->reference }}</div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; background: #fcfaf5; padding: 16px; border-radius: 16px; margin-bottom: 20px;">
                    <div>
                        <div style="font-size: 10px; color: #b58c5a; font-weight: 800; text-transform: uppercase;">Période</div>
                        <div style="font-size: 13px; font-weight: 700; color: #1a202c;">{{ \Carbon\Carbon::parse($p->periode)->translatedFormat('M Y') }}</div>
                    </div>
                    <div>
                        <div style="font-size: 10px; color: #b58c5a; font-weight: 800; text-transform: uppercase;">Montant Net</div>
                        <div style="font-size: 13px; font-weight: 800; color: #16a34a;">{{ number_format($p->net_proprietaire, 0, ',', ' ') }} F</div>
                    </div>
                </div>

                <div style="display: flex; gap: 10px;">
                    <a href="{{ route('admin.paiements.show', $p) }}" class="btn btn-secondary btn-sm" style="flex: 1; justify-content: center; background: white; border: 1px solid #e2e8f0; border-radius: 12px; font-weight: 700;">Détails</a>
                    @if($p->statut === 'valide')
                        <a href="{{ route('admin.paiements.pdf', $p) }}" target="_blank" style="background: #f7f4ec; color: #b58c5a; padding: 8px 12px; border-radius: 12px; font-size: 12px; font-weight: 800;">PDF</a>
                    @endif
                </div>
            </div>
        @empty
            <div style="text-align: center; padding: 60px 20px; background: white; border-radius: 32px;">
                <div style="font-size: 40px; margin-bottom: 16px;">🏦</div>
                <p style="color: #a0aec0; font-weight: 600;">Aucun flux financier détecté.</p>
            </div>
        @endforelse
    </div>

    {{-- Version desktop : Tableau "Haute Couture" --}}
    <div class="desktop-table card" style="border: none; box-shadow: 0 20px 40px rgba(0,0,0,0.04); border-radius: 32px; overflow: hidden; background: white;">
        <div class="table-wrap">
            <table style="width: 100%; border-collapse: separate; border-spacing: 0;">
                <thead>
                    <tr style="background: #fcfaf5;">
                        <th style="padding: 20px 24px; font-size: 11px; color: #b58c5a; text-transform: uppercase; letter-spacing: 1.5px; font-weight: 800;">Référence</th>
                        <th style="padding: 20px; font-size: 11px; color: #b58c5a; text-transform: uppercase; letter-spacing: 1.5px; font-weight: 800;">Locataire & Bien</th>
                        <th style="padding: 20px; font-size: 11px; color: #b58c5a; text-transform: uppercase; letter-spacing: 1.5px; font-weight: 800;">Période</th>
                        <th style="padding: 20px; font-size: 11px; color: #b58c5a; text-transform: uppercase; letter-spacing: 1.5px; font-weight: 800; text-align: right;">Loyer Brut</th>
                        <th style="padding: 20px; font-size: 11px; color: #b58c5a; text-transform: uppercase; letter-spacing: 1.5px; font-weight: 800; text-align: right;">Net Proprio</th>
                        <th style="padding: 20px; font-size: 11px; color: #b58c5a; text-transform: uppercase; letter-spacing: 1.5px; font-weight: 800; text-align: center;">Statut</th>
                        <th style="padding: 20px 24px; font-size: 11px; color: #b58c5a; text-transform: uppercase; letter-spacing: 1.5px; font-weight: 800; text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($paiements as $p)
                        @php
                            $status = match($p->statut) {
                                'valide'    => ['bg' => '#f0fdf4', 'text' => '#16a34a', 'label' => 'Validé'],
                                'en_attente'=> ['bg' => '#fffbeb', 'text' => '#d97706', 'label' => 'En attente'],
                                'annule'    => ['bg' => '#fef2f2', 'text' => '#dc2626', 'label' => 'Annulé'],
                                default     => ['bg' => '#f8fafc', 'text' => '#64748b', 'label' => ucfirst($p->statut)],
                            };
                        @endphp
                        <tr style="border-bottom: 1px solid #f7f4ec; transition: background 0.2s;" onmouseover="this.style.background='#fcfaf5'" onmouseout="this.style.background='transparent'">
                            <td style="padding: 24px;"><span style="font-family: monospace; font-weight: 700; color: #718096; font-size: 13px;">#{{ $p->reference_paiement }}</span></td>
                            <td style="padding: 24px;">
                                <div style="font-weight: 800; color: #1a202c; font-size: 14px;">{{ $p->contrat->locataire->name }}</div>
                                <div style="font-size: 12px; color: #a0aec0; margin-top: 2px;">{{ $p->contrat->bien->reference }} • {{ $p->contrat->bien->ville }}</div>
                            </td>
                            <td style="padding: 24px;">
                                <span style="background: #f7f4ec; color: #b58c5a; padding: 6px 12px; border-radius: 10px; font-size: 12px; font-weight: 700;">
                                    {{ \Carbon\Carbon::parse($p->periode)->translatedFormat('F Y') }}
                                </span>
                            </td>
                            <td style="padding: 24px; text-align: right; font-weight: 800; color: #1a202c; font-size: 15px;">
                                {{ number_format($p->montant_encaisse, 0, ',', ' ') }} F
                            </td>
                            <td style="padding: 24px; text-align: right;">
                                <div style="color: #16a34a; font-weight: 900; font-size: 15px;">{{ number_format($p->net_proprietaire, 0, ',', ' ') }} F</div>
                                <div style="font-size: 10px; color: #a0aec0; font-weight: 600;">COM: {{ number_format($p->commission_ttc, 0, ',', ' ') }} F</div>
                            </td>
                            <td style="padding: 24px; text-align: center;">
                                <span style="background: {{ $status['bg'] }}; color: {{ $status['text'] }}; padding: 6px 14px; border-radius: 10px; font-size: 11px; font-weight: 800; text-transform: uppercase;">
                                    {{ $status['label'] }}
                                </span>
                            </td>
                            <td style="padding: 24px; text-align: center;">
                                <div style="display: flex; gap: 8px; justify-content: center;">
                                    <a href="{{ route('admin.paiements.show', $p) }}" style="background: #fff; border: 1px solid #e2e8f0; color: #1a202c; padding: 8px; border-radius: 10px; transition: 0.2s;" title="Voir">
                                        <svg style="width:16px; height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                    @if($p->statut === 'valide')
                                        <a href="{{ route('admin.paiements.pdf', $p) }}" target="_blank" style="background: #f7f4ec; border: 1px solid #fde68a; color: #b58c5a; padding: 8px; border-radius: 10px;" title="Reçu PDF">
                                            <svg style="width:16px; height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                        </a>
                                        <form method="POST" action="{{ route('admin.paiements.annuler', $p) }}" onsubmit="return confirm('Annuler ce paiement ? Cette action est irréversible.')">
                                            @csrf @method('PATCH')
                                            <button type="submit" style="background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; padding: 8px; border-radius: 10px;" title="Annuler">
                                                <svg style="width:16px; height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="padding: 100px; text-align: center;">
                                <div style="font-size: 48px; margin-bottom: 20px;">💰</div>
                                <div style="font-size: 16px; font-weight: 700; color: #a0aec0;">Aucune transaction enregistrée.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination Stylisée --}}
        @if($paiements->hasPages())
            <div style="padding: 24px; background: #fcfaf5; border-top: 1px solid #f7f4ec;">
                {{ $paiements->links() }}
            </div>
        @endif
    </div>
</x-app-layout>