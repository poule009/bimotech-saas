<x-app-layout>
    <x-slot name="header">Mon espace propriétaire</x-slot>

    {{-- ── En-tête de bienvenue ── --}}
    <div class="section-gap" style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:12px;">
        <div>
            <h1 style="font-size:22px;font-weight:800;color:var(--text);letter-spacing:-.4px;">
                Bonjour, {{ auth()->user()->name }} 👋
            </h1>
            <p style="font-size:13px;color:var(--text-3);margin-top:4px;">
                Voici un aperçu de votre patrimoine immobilier — {{ now()->translatedFormat('d F Y') }}
            </p>
        </div>
        <a href="{{ route('biens.index') }}"
           style="display:inline-flex;align-items:center;gap:6px;font-size:13px;font-weight:600;color:var(--agency);text-decoration:none;padding:8px 16px;border:1px solid var(--agency);border-radius:var(--radius-sm);transition:background .15s;"
           onmouseenter="this.style.background='rgba(0,0,0,.04)'" onmouseleave="this.style.background='transparent'">
            🏠 Voir mes biens →
        </a>
    </div>

    {{-- ── KPIs principaux ── --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:12px;" class="section-gap">

        <div class="card" style="padding:18px 20px;text-align:center;border-top:3px solid var(--agency);">
            <div style="font-size:28px;font-weight:800;color:var(--text);">{{ $stats['nb_biens'] }}</div>
            <div style="font-size:12px;font-weight:600;color:var(--text-2);margin-top:4px;">Biens totaux</div>
            <div style="font-size:11px;color:var(--text-3);margin-top:3px;">{{ $stats['nb_biens_loues'] }} loué(s)</div>
        </div>

        <div class="card" style="padding:18px 20px;text-align:center;border-top:3px solid #10b981;">
            <div style="font-size:22px;font-weight:800;color:#10b981;" class="text-money">
                {{ number_format($stats['total_net'], 0, ',', ' ') }}
            </div>
            <div style="font-size:11px;color:#6b7280;margin-top:2px;">FCFA</div>
            <div style="font-size:12px;font-weight:600;color:var(--text-2);margin-top:4px;">Net perçu</div>
            <div style="font-size:11px;color:var(--text-3);margin-top:3px;">Après commissions agence</div>
        </div>

        <div class="card" style="padding:18px 20px;text-align:center;border-top:3px solid #3b82f6;">
            <div style="font-size:22px;font-weight:800;color:#3b82f6;" class="text-money">
                {{ number_format($stats['total_loyers'], 0, ',', ' ') }}
            </div>
            <div style="font-size:11px;color:#6b7280;margin-top:2px;">FCFA</div>
            <div style="font-size:12px;font-weight:600;color:var(--text-2);margin-top:4px;">Loyers bruts</div>
            <div style="font-size:11px;color:var(--text-3);margin-top:3px;">Total encaissé</div>
        </div>

        <div class="card" style="padding:18px 20px;text-align:center;border-top:3px solid #8b5cf6;">
            <div style="font-size:28px;font-weight:800;color:#8b5cf6;">{{ $stats['nb_paiements'] }}</div>
            <div style="font-size:12px;font-weight:600;color:var(--text-2);margin-top:4px;">Paiements reçus</div>
            <div style="font-size:11px;color:var(--text-3);margin-top:3px;">Toutes périodes</div>
        </div>

        <div class="card" style="padding:18px 20px;text-align:center;border-top:3px solid #f59e0b;">
            <div style="font-size:22px;font-weight:800;color:#f59e0b;" class="text-money">
                {{ number_format($stats['caution'], 0, ',', ' ') }}
            </div>
            <div style="font-size:11px;color:#6b7280;margin-top:2px;">FCFA</div>
            <div style="font-size:12px;font-weight:600;color:var(--text-2);margin-top:4px;">Cautions détenues</div>
            <div style="font-size:11px;color:var(--text-3);margin-top:3px;">Par l'agence</div>
        </div>

        <div class="card" style="padding:18px 20px;text-align:center;border-top:3px solid #ef4444;">
            <div style="font-size:22px;font-weight:800;color:#ef4444;" class="text-money">
                {{ number_format($stats['total_commission'], 0, ',', ' ') }}
            </div>
            <div style="font-size:11px;color:#6b7280;margin-top:2px;">FCFA</div>
            <div style="font-size:12px;font-weight:600;color:var(--text-2);margin-top:4px;">Commissions TTC</div>
            <div style="font-size:11px;color:var(--text-3);margin-top:3px;">Prélevées par l'agence</div>
        </div>

    </div>

    {{-- ── Dernier paiement reçu ── --}}
    @if($stats['dernier_paiement'])
        <div class="section-gap">
            <div style="background:linear-gradient(135deg,#f0fdf4,#dcfce7);border:1px solid #bbf7d0;border-radius:var(--radius);padding:16px 20px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
                <div style="display:flex;align-items:center;gap:12px;">
                    <div style="width:40px;height:40px;border-radius:50%;background:#10b981;display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0;">💸</div>
                    <div>
                        <div style="font-size:13px;font-weight:700;color:#15803d;">Dernier paiement reçu</div>
                        <div style="font-size:12px;color:#166534;margin-top:2px;">
                            {{ $stats['dernier_paiement']->contrat->bien->reference ?? '—' }} ·
                            {{ \Carbon\Carbon::parse($stats['dernier_paiement']->date_paiement)->format('d/m/Y') }}
                        </div>
                    </div>
                </div>
                <div style="font-size:20px;font-weight:800;color:#15803d;" class="text-money">
                    +{{ number_format($stats['dernier_paiement']->net_proprietaire, 0, ',', ' ') }} FCFA net
                </div>
            </div>
        </div>
    @endif

    {{-- ── Mes biens ── --}}
    <div class="section-gap">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
            <div>
                <div style="font-size:15px;font-weight:700;color:var(--text);">🏠 Mes biens</div>
                <div style="font-size:12px;color:var(--text-3);margin-top:2px;">{{ $biens->total() }} bien(s) enregistré(s)</div>
            </div>
            <a href="{{ route('biens.index') }}" style="font-size:12px;color:var(--agency);font-weight:600;text-decoration:none;">Voir tout →</a>
        </div>

        @forelse($biens as $bien)
            <a href="{{ route('biens.show', $bien) }}" style="text-decoration:none;display:block;margin-bottom:10px;">
                <div class="card" style="padding:16px 20px;display:flex;align-items:center;justify-content:space-between;gap:16px;transition:box-shadow .15s;cursor:pointer;"
                     onmouseenter="this.style.boxShadow='0 4px 16px rgba(0,0,0,.08)'"
                     onmouseleave="this.style.boxShadow=''">
                    <div style="display:flex;align-items:center;gap:14px;min-width:0;">
                        <div style="width:42px;height:42px;border-radius:10px;background:var(--bg);border:1px solid var(--border);display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0;">
                            {{ match($bien->type) {
                                'Villa' => '🏡',
                                'Studio' => '🛏️',
                                'Bureau' => '🏢',
                                'Terrain' => '🌍',
                                default => '🏠'
                            } }}
                        </div>
                        <div style="min-width:0;">
                            <div style="font-size:13px;font-weight:700;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                {{ $bien->reference }}
                                <span style="font-size:11px;font-weight:500;color:var(--text-3);margin-left:6px;">{{ $bien->type }}</span>
                            </div>
                            <div style="font-size:12px;color:var(--text-3);margin-top:2px;">
                                {{ $bien->adresse }}, {{ $bien->ville }}
                                @if($bien->quartier) — {{ $bien->quartier }}@endif
                            </div>
                        </div>
                    </div>
                    <div style="display:flex;align-items:center;gap:20px;flex-shrink:0;">
                        <div style="text-align:right;">
                            <div style="font-size:14px;font-weight:800;color:var(--text);" class="text-money">
                                {{ number_format($bien->loyer_mensuel, 0, ',', ' ') }}
                                <span style="font-size:10px;font-weight:500;color:var(--text-3);">FCFA/mois</span>
                            </div>
                            <div style="font-size:11px;color:var(--text-3);margin-top:1px;">Commission {{ $bien->taux_commission }}%</div>
                        </div>
                        <span style="font-size:11px;font-weight:600;padding:4px 10px;border-radius:999px;
                            {{ $bien->statut === 'loue' ? 'background:#dcfce7;color:#15803d;' : ($bien->statut === 'en_travaux' ? 'background:#fef3c7;color:#92400e;' : 'background:#eff6ff;color:#1d4ed8;') }}">
                            {{ $bien->statut === 'loue' ? 'Loué' : ($bien->statut === 'en_travaux' ? 'Travaux' : 'Disponible') }}
                        </span>
                    </div>
                </div>
            </a>
        @empty
            <div class="card" style="padding:48px;text-align:center;">
                <div style="font-size:48px;margin-bottom:12px;opacity:.4;">🏠</div>
                <div style="font-size:15px;font-weight:600;color:var(--text);margin-bottom:6px;">Aucun bien enregistré</div>
                <div style="font-size:13px;color:var(--text-3);">Contactez votre agence pour ajouter vos biens.</div>
            </div>
        @endforelse

        @if($biens->hasPages())
            <div style="margin-top:12px;">{{ $biens->links() }}</div>
        @endif
    </div>

    {{-- ── Derniers paiements ── --}}
    <div class="section-gap">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
            <div style="font-size:15px;font-weight:700;color:var(--text);">💳 Historique des paiements</div>
        </div>

        <div class="card">
            @forelse($paiements as $p)
                <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 20px;border-bottom:1px solid var(--border);gap:12px;flex-wrap:wrap;">
                    <div style="display:flex;align-items:center;gap:12px;">
                        <div style="width:36px;height:36px;border-radius:8px;background:#f0fdf4;border:1px solid #bbf7d0;display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0;">✅</div>
                        <div>
                            <div style="font-size:13px;font-weight:600;color:var(--text);">
                                {{ $p->contrat->bien->reference ?? '—' }}
                            </div>
                            <div style="font-size:11px;color:var(--text-3);margin-top:2px;">
                                {{ \Carbon\Carbon::parse($p->periode)->translatedFormat('F Y') }} ·
                                {{ \Carbon\Carbon::parse($p->date_paiement)->format('d/m/Y') }}
                            </div>
                        </div>
                    </div>
                    <div style="display:flex;align-items:center;gap:16px;">
                        <div style="text-align:right;">
                            <div style="font-size:14px;font-weight:800;color:#10b981;" class="text-money">
                                +{{ number_format($p->net_proprietaire, 0, ',', ' ') }} FCFA
                            </div>
                            <div style="font-size:11px;color:var(--text-3);">
                                Brut : {{ number_format($p->montant_encaisse, 0, ',', ' ') }} F
                            </div>
                        </div>
                        <a href="{{ route('proprietaire.paiements.pdf', $p) }}"
                           style="font-size:11px;font-weight:600;color:var(--agency);text-decoration:none;padding:5px 10px;border:1px solid var(--agency);border-radius:6px;white-space:nowrap;">
                            📄 Quittance
                        </a>
                    </div>
                </div>
            @empty
                <div style="padding:40px;text-align:center;color:var(--text-3);">
                    <div style="font-size:32px;margin-bottom:10px;opacity:.4;">💳</div>
                    <div style="font-size:14px;">Aucun paiement enregistré pour le moment.</div>
                </div>
            @endforelse

            @if($paiements->hasPages())
                <div style="padding:16px 20px;border-top:1px solid var(--border);">
                    {{ $paiements->links() }}
                </div>
            @endif
        </div>
    </div>

    <style>
        @media (max-width: 640px) {
            #locataire-kpi { grid-template-columns: 1fr 1fr !important; }
        }
    </style>

</x-app-layout>