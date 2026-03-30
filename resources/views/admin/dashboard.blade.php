<x-app-layout>
    <x-slot name="header">Dashboard Admin — {{ $currentAgency?->name ?? 'BIMO-Tech' }}</x-slot>

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- TÂCHE 1 : WIZARD D'ONBOARDING                                    --}}
    {{-- Visible tant que l'agence n'a pas complété les 4 étapes          --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    @if($onboarding !== null && ! ($onboarding['tout_complete'] ?? false))
    <div style="background:#fffbeb;border:1.5px solid #fde68a;border-radius:var(--radius);padding:20px 24px;margin-bottom:20px;">

        {{-- En-tête --}}
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:16px;">
            <div style="display:flex;align-items:center;gap:10px;">
                <div style="width:36px;height:36px;border-radius:50%;background:#fef3c7;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg fill="none" stroke="#d97706" stroke-width="2" viewBox="0 0 24 24" style="width:18px;height:18px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                    </svg>
                </div>
                <div>
                    <div style="font-size:14px;font-weight:700;color:#92400e;">🚀 Guide de démarrage — Configurez votre agence</div>
                    <div style="font-size:12px;color:#b45309;margin-top:2px;">
                        {{ $onboarding['nb_completes'] }}/4 étapes complétées
                    </div>
                </div>
            </div>
            {{-- Barre de progression --}}
            <div style="display:flex;align-items:center;gap:8px;min-width:160px;">
                <div style="flex:1;height:6px;background:#fde68a;border-radius:3px;overflow:hidden;">
                    <div style="height:100%;width:{{ ($onboarding['nb_completes'] / 4) * 100 }}%;background:#d97706;border-radius:3px;transition:width .4s;"></div>
                </div>
                <span style="font-size:11px;font-weight:700;color:#92400e;">{{ round(($onboarding['nb_completes'] / 4) * 100) }}%</span>
            </div>
        </div>

        {{-- Liste des 4 étapes --}}
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:10px;">

            {{-- Étape 1 : Paramètres agence --}}
            <div style="display:flex;align-items:flex-start;gap:10px;background:{{ $onboarding['etape1'] ? '#f0fdf4' : '#ffffff' }};border:1px solid {{ $onboarding['etape1'] ? '#bbf7d0' : '#fde68a' }};border-radius:var(--radius-sm);padding:12px;">
                <div style="width:22px;height:22px;border-radius:50%;background:{{ $onboarding['etape1'] ? '#16a34a' : '#e5e7eb' }};display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:1px;">
                    @if($onboarding['etape1'])
                        <svg fill="none" stroke="white" stroke-width="2.5" viewBox="0 0 24 24" style="width:12px;height:12px;"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    @else
                        <span style="font-size:10px;font-weight:700;color:#9ca3af;">1</span>
                    @endif
                </div>
                <div>
                    <div style="font-size:13px;font-weight:600;color:{{ $onboarding['etape1'] ? '#15803d' : '#374151' }};">Paramètres agence</div>
                    <div style="font-size:11px;color:{{ $onboarding['etape1'] ? '#16a34a' : '#6b7280' }};margin-top:2px;">Logo, couleur et NINEA configurés</div>
                    @if(! $onboarding['etape1'])
                        <a href="{{ route('admin.agency.settings') }}" style="font-size:11px;color:#d97706;font-weight:600;margin-top:4px;display:inline-block;">Configurer →</a>
                    @endif
                </div>
            </div>

            {{-- Étape 2 : Premier propriétaire --}}
            <div style="display:flex;align-items:flex-start;gap:10px;background:{{ $onboarding['etape2'] ? '#f0fdf4' : '#ffffff' }};border:1px solid {{ $onboarding['etape2'] ? '#bbf7d0' : '#fde68a' }};border-radius:var(--radius-sm);padding:12px;">
                <div style="width:22px;height:22px;border-radius:50%;background:{{ $onboarding['etape2'] ? '#16a34a' : '#e5e7eb' }};display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:1px;">
                    @if($onboarding['etape2'])
                        <svg fill="none" stroke="white" stroke-width="2.5" viewBox="0 0 24 24" style="width:12px;height:12px;"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    @else
                        <span style="font-size:10px;font-weight:700;color:#9ca3af;">2</span>
                    @endif
                </div>
                <div>
                    <div style="font-size:13px;font-weight:600;color:{{ $onboarding['etape2'] ? '#15803d' : '#374151' }};">Premier propriétaire</div>
                    <div style="font-size:11px;color:{{ $onboarding['etape2'] ? '#16a34a' : '#6b7280' }};margin-top:2px;">Au moins un propriétaire ajouté</div>
                    @if(! $onboarding['etape2'])
                        <a href="{{ route('admin.users.create', 'proprietaire') }}" style="font-size:11px;color:#d97706;font-weight:600;margin-top:4px;display:inline-block;">Ajouter →</a>
                    @endif
                </div>
            </div>

            {{-- Étape 3 : Premier bien --}}
            <div style="display:flex;align-items:flex-start;gap:10px;background:{{ $onboarding['etape3'] ? '#f0fdf4' : '#ffffff' }};border:1px solid {{ $onboarding['etape3'] ? '#bbf7d0' : '#fde68a' }};border-radius:var(--radius-sm);padding:12px;">
                <div style="width:22px;height:22px;border-radius:50%;background:{{ $onboarding['etape3'] ? '#16a34a' : '#e5e7eb' }};display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:1px;">
                    @if($onboarding['etape3'])
                        <svg fill="none" stroke="white" stroke-width="2.5" viewBox="0 0 24 24" style="width:12px;height:12px;"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    @else
                        <span style="font-size:10px;font-weight:700;color:#9ca3af;">3</span>
                    @endif
                </div>
                <div>
                    <div style="font-size:13px;font-weight:600;color:{{ $onboarding['etape3'] ? '#15803d' : '#374151' }};">Premier bien</div>
                    <div style="font-size:11px;color:{{ $onboarding['etape3'] ? '#16a34a' : '#6b7280' }};margin-top:2px;">Au moins un bien enregistré</div>
                    @if(! $onboarding['etape3'])
                        <a href="{{ route('biens.create') }}" style="font-size:11px;color:#d97706;font-weight:600;margin-top:4px;display:inline-block;">Ajouter →</a>
                    @endif
                </div>
            </div>

            {{-- Étape 4 : Premier contrat --}}
            <div style="display:flex;align-items:flex-start;gap:10px;background:{{ $onboarding['etape4'] ? '#f0fdf4' : '#ffffff' }};border:1px solid {{ $onboarding['etape4'] ? '#bbf7d0' : '#fde68a' }};border-radius:var(--radius-sm);padding:12px;">
                <div style="width:22px;height:22px;border-radius:50%;background:{{ $onboarding['etape4'] ? '#16a34a' : '#e5e7eb' }};display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:1px;">
                    @if($onboarding['etape4'])
                        <svg fill="none" stroke="white" stroke-width="2.5" viewBox="0 0 24 24" style="width:12px;height:12px;"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    @else
                        <span style="font-size:10px;font-weight:700;color:#9ca3af;">4</span>
                    @endif
                </div>
                <div>
                    <div style="font-size:13px;font-weight:600;color:{{ $onboarding['etape4'] ? '#15803d' : '#374151' }};">Premier contrat</div>
                    <div style="font-size:11px;color:{{ $onboarding['etape4'] ? '#16a34a' : '#6b7280' }};margin-top:2px;">Au moins un contrat actif signé</div>
                    @if(! $onboarding['etape4'])
                        <a href="{{ route('admin.contrats.create') }}" style="font-size:11px;color:#d97706;font-weight:600;margin-top:4px;display:inline-block;">Créer →</a>
                    @endif
                </div>
            </div>

        </div>
    </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- ZONE URGENCE — visible uniquement si alertes actives              --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    @if($nb_impayes_mois > 0 || $contrats_a_renouveler->count() > 0)
    <div style="display:flex;flex-direction:column;gap:10px;margin-bottom:20px;">

        {{-- Alerte impayés du mois --}}
        @if($nb_impayes_mois > 0)
        <div style="background:#fef2f2;border:1.5px solid #fecaca;border-radius:var(--radius);padding:14px 18px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;">
            <div style="display:flex;align-items:center;gap:12px;">
                <div style="width:36px;height:36px;border-radius:50%;background:#fee2e2;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg fill="none" stroke="#dc2626" stroke-width="2" viewBox="0 0 24 24" style="width:18px;height:18px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div>
                    <div style="font-size:14px;font-weight:700;color:#dc2626;">
                        {{ $nb_impayes_mois }} loyer{{ $nb_impayes_mois > 1 ? 's' : '' }} impayé{{ $nb_impayes_mois > 1 ? 's' : '' }} — {{ now()->translatedFormat('F Y') }}
                    </div>
                    <div style="font-size:12px;color:#ef4444;margin-top:2px;">
                        Montant dû : <strong>{{ number_format($montant_du_mois, 0, ',', ' ') }} FCFA</strong>
                        · {{ $contratsActifs }} contrat{{ $contratsActifs > 1 ? 's' : '' }} actif{{ $contratsActifs > 1 ? 's' : '' }} au total
                    </div>
                </div>
            </div>
            <a href="{{ route('admin.impayes.index') }}" class="btn btn-sm" style="background:#dc2626;color:white;white-space:nowrap;">
                Voir les impayés →
            </a>
        </div>
        @endif

        {{-- Alerte contrats à renouveler --}}
        @if($contrats_a_renouveler->count() > 0)
        <div style="background:#fffbeb;border:1.5px solid #fde68a;border-radius:var(--radius);padding:14px 18px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;">
            <div style="display:flex;align-items:center;gap:12px;">
                <div style="width:36px;height:36px;border-radius:50%;background:#fef3c7;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg fill="none" stroke="#d97706" stroke-width="2" viewBox="0 0 24 24" style="width:18px;height:18px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <div style="font-size:14px;font-weight:700;color:#d97706;">
                        {{ $contrats_a_renouveler->count() }} contrat{{ $contrats_a_renouveler->count() > 1 ? 's' : '' }} expire{{ $contrats_a_renouveler->count() > 1 ? 'nt' : '' }} dans 30 jours
                    </div>
                    <div style="font-size:12px;color:#f59e0b;margin-top:2px;">
                        @foreach($contrats_a_renouveler->take(2) as $c)
                            {{ $c->bien->reference }} ({{ \Carbon\Carbon::parse($c->date_fin)->translatedFormat('d M Y') }}){{ !$loop->last ? ' · ' : '' }}
                        @endforeach
                        @if($contrats_a_renouveler->count() > 2)
                            · +{{ $contrats_a_renouveler->count() - 2 }} autre(s)
                        @endif
                    </div>
                </div>
            </div>
            <a href="{{ route('admin.contrats.index') }}" class="btn btn-sm" style="background:#d97706;color:white;white-space:nowrap;">
                Voir les contrats →
            </a>
        </div>
        @endif

    </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- ACTIONS RAPIDES                                                    --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    <div style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:20px;">
        <a href="{{ route('admin.paiements.create') }}" class="btn btn-primary">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:15px;height:15px;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Enregistrer un paiement
        </a>
        <a href="{{ route('admin.contrats.create') }}" class="btn btn-secondary">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:15px;height:15px;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Nouveau contrat
        </a>
        <a href="{{ route('biens.create') }}" class="btn btn-secondary">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:15px;height:15px;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 00-1-1h-2a1 1 0 00-1 1v5m4 0H9"/>
            </svg>
            Ajouter un bien
        </a>
        <a href="{{ route('admin.impayes.index') }}" class="btn btn-secondary" style="{{ $nb_impayes_mois > 0 ? 'border-color:#fecaca;color:#dc2626;' : '' }}">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:15px;height:15px;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            Impayés
            @if($nb_impayes_mois > 0)
                <span style="background:#dc2626;color:white;font-size:10px;font-weight:700;padding:1px 6px;border-radius:999px;">{{ $nb_impayes_mois }}</span>
            @endif
        </a>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- TÂCHE 4 : BILAN DU MOIS EXPRESS                                   --}}
    {{-- 3 chiffres clés calculés en temps réel                            --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    <div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:16px 20px;margin-bottom:20px;box-shadow:var(--shadow);">
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">

            {{-- Titre --}}
            <div style="display:flex;align-items:center;gap:8px;">
                <span style="font-size:16px;">📊</span>
                <div>
                    <div style="font-size:13px;font-weight:700;color:var(--text);">Bilan du mois — {{ now()->translatedFormat('F Y') }}</div>
                    <div style="font-size:11px;color:var(--text-3);margin-top:1px;">Calculé en temps réel · {{ $contratsActifs }} contrat(s) actif(s)</div>
                </div>
            </div>

            {{-- Lien rapport complet --}}
            <a href="{{ route('admin.rapports.financier') }}"
               style="font-size:11px;color:var(--agency);font-weight:600;display:flex;align-items:center;gap:4px;white-space:nowrap;">
                Rapport détaillé →
            </a>
        </div>

        {{-- 3 chiffres clés --}}
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-top:14px;">

            {{-- Total attendu --}}
            <div style="text-align:center;padding:12px;background:#f8fafc;border-radius:var(--radius-sm);border:1px solid var(--border);">
                <div style="font-size:10px;font-weight:700;color:var(--text-3);text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">Total attendu</div>
                <div style="font-size:18px;font-weight:800;color:var(--text);" class="text-money">
                    {{ number_format($bilanMois['attendu'], 0, ',', ' ') }}
                    <span style="font-size:11px;font-weight:500;color:var(--text-3);">F</span>
                </div>
                <div style="font-size:10px;color:var(--text-3);margin-top:3px;">Loyers contractuels</div>
            </div>

            {{-- Encaissé --}}
            <div style="text-align:center;padding:12px;background:#f0fdf4;border-radius:var(--radius-sm);border:1px solid #bbf7d0;">
                <div style="font-size:10px;font-weight:700;color:#16a34a;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">Encaissé</div>
                <div style="font-size:18px;font-weight:800;color:#16a34a;" class="text-money">
                    {{ number_format($bilanMois['encaisse'], 0, ',', ' ') }}
                    <span style="font-size:11px;font-weight:500;color:#86efac;">F</span>
                </div>
                @php
                    $tauxRecouvrement = $bilanMois['attendu'] > 0
                        ? round(($bilanMois['encaisse'] / $bilanMois['attendu']) * 100)
                        : 0;
                @endphp
                <div style="font-size:10px;color:#16a34a;margin-top:3px;">{{ $tauxRecouvrement }}% du total attendu</div>
            </div>

            {{-- Reste à recouvrer --}}
            <div style="text-align:center;padding:12px;background:{{ $bilanMois['a_recouvrer'] > 0 ? '#fef2f2' : '#f0fdf4' }};border-radius:var(--radius-sm);border:1px solid {{ $bilanMois['a_recouvrer'] > 0 ? '#fecaca' : '#bbf7d0' }};">
                <div style="font-size:10px;font-weight:700;color:{{ $bilanMois['a_recouvrer'] > 0 ? '#dc2626' : '#16a34a' }};text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">À recouvrer</div>
                <div style="font-size:18px;font-weight:800;color:{{ $bilanMois['a_recouvrer'] > 0 ? '#dc2626' : '#16a34a' }};" class="text-money">
                    {{ number_format($bilanMois['a_recouvrer'], 0, ',', ' ') }}
                    <span style="font-size:11px;font-weight:500;color:{{ $bilanMois['a_recouvrer'] > 0 ? '#fca5a5' : '#86efac' }};">F</span>
                </div>
                <div style="font-size:10px;color:{{ $bilanMois['a_recouvrer'] > 0 ? '#ef4444' : '#16a34a' }};margin-top:3px;">
                    {{ $bilanMois['a_recouvrer'] > 0 ? $nb_impayes_mois . ' loyer(s) impayé(s)' : 'Tout est encaissé 🎉' }}
                </div>
            </div>

        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- KPI MOIS COURANT                                                   --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    <div style="margin-bottom:6px;display:flex;align-items:center;gap:8px;">
        <span style="font-size:12px;font-weight:700;color:var(--text-3);text-transform:uppercase;letter-spacing:.5px;">
            {{ now()->translatedFormat('F Y') }}
        </span>
        <div style="flex:1;height:1px;background:var(--border);"></div>
        <a href="{{ route('admin.rapports.financier') }}" style="font-size:11px;color:var(--agency);font-weight:600;">
            Rapport complet →
        </a>
    </div>
    <div class="kpi-grid section-gap">

        <div class="kpi" style="border-color:#bbf7d0;">
            <div class="kpi-icon" style="background:#f0fdf4;">💰</div>
            <div class="kpi-label">Loyers encaissés</div>
            <div class="kpi-value text-money" style="color:#16a34a;">{{ number_format($statsMois['loyers'], 0, ',', ' ') }} <span style="font-size:13px;font-weight:500;color:var(--text-3);">FCFA</span></div>
            <div class="kpi-sub">{{ $statsMois['nb_payes'] }} paiement{{ $statsMois['nb_payes'] > 1 ? 's' : '' }} reçu{{ $statsMois['nb_payes'] > 1 ? 's' : '' }}</div>
        </div>

        <div class="kpi" style="{{ $nb_impayes_mois > 0 ? 'border-color:#fecaca;' : '' }}">
            <div class="kpi-icon" style="background:{{ $nb_impayes_mois > 0 ? '#fef2f2' : '#f0fdf4' }};">
                {{ $nb_impayes_mois > 0 ? '⚠️' : '✅' }}
            </div>
            <div class="kpi-label">Impayés ce mois</div>
            <div class="kpi-value text-money" style="color:{{ $nb_impayes_mois > 0 ? '#dc2626' : '#16a34a' }};">{{ $nb_impayes_mois }}</div>
            <div class="kpi-sub" style="color:{{ $nb_impayes_mois > 0 ? '#ef4444' : 'var(--text-3)' }};">
                @if($nb_impayes_mois > 0)
                    {{ number_format($montant_du_mois, 0, ',', ' ') }} FCFA dus
                @else
                    Tous les loyers sont payés 🎉
                @endif
            </div>
        </div>

        <div class="kpi">
            <div class="kpi-icon" style="background:#f5f3ff;">💼</div>
            <div class="kpi-label">Commission TTC</div>
            <div class="kpi-value text-money" style="color:var(--agency);">{{ number_format($statsMois['commissions'], 0, ',', ' ') }} <span style="font-size:13px;font-weight:500;color:var(--text-3);">FCFA</span></div>
            <div class="kpi-sub">{{ $stats['nb_contrats'] }} contrat(s) actif(s)</div>
        </div>

        <div class="kpi">
            <div class="kpi-icon" style="background:#f0fdf4;">📊</div>
            <div class="kpi-label">Taux d'occupation</div>
            <div class="kpi-value text-money" style="color:#16a34a;">{{ $stats['taux_occupation'] }}%</div>
            <div class="progress"><div class="progress-bar" style="width:{{ $stats['taux_occupation'] }}%;background:#16a34a;"></div></div>
            <div class="kpi-sub">{{ $stats['nb_biens_loues'] }}/{{ $stats['nb_biens'] }} biens loués</div>
        </div>

    </div>

    {{-- ── KPI cumulatifs (all-time) ── --}}
    <div style="margin-bottom:6px;display:flex;align-items:center;gap:8px;">
        <span style="font-size:12px;font-weight:700;color:var(--text-3);text-transform:uppercase;letter-spacing:.5px;">
            Cumul total
        </span>
        <div style="flex:1;height:1px;background:var(--border);"></div>
    </div>
    <div class="kpi-grid section-gap">

        <div class="kpi">
            <div class="kpi-icon" style="background:#eff6ff;">💰</div>
            <div class="kpi-label">Loyers encaissés</div>
            <div class="kpi-value text-money">{{ number_format($stats['total_loyers'], 0, ',', ' ') }} <span style="font-size:13px;font-weight:500;color:var(--text-3);">FCFA</span></div>
            <div class="kpi-sub">{{ $stats['nb_contrats'] }} contrat(s) actif(s)</div>
        </div>

        <div class="kpi">
            <div class="kpi-icon" style="background:#f5f3ff;">💼</div>
            <div class="kpi-label">Commission agence</div>
            <div class="kpi-value text-money" style="color:var(--agency);">{{ number_format($stats['total_commissions'], 0, ',', ' ') }} <span style="font-size:13px;font-weight:500;color:var(--text-3);">HT</span></div>
            <div class="kpi-sub">TTC : {{ number_format($stats['total_commission_ttc'], 0, ',', ' ') }} FCFA</div>
        </div>

        <div class="kpi">
            <div class="kpi-icon" style="background:#fffbeb;">🧾</div>
            <div class="kpi-label">TVA collectée 18%</div>
            <div class="kpi-value text-money" style="color:#d97706;">{{ number_format($stats['total_tva'], 0, ',', ' ') }} <span style="font-size:13px;font-weight:500;color:var(--text-3);">FCFA</span></div>
            <div class="kpi-sub">À reverser à la DGI</div>
        </div>

        <div class="kpi">
            <div class="kpi-icon" style="background:#f0fdf4;">🏦</div>
            <div class="kpi-label">Net reversé propriétaires</div>
            <div class="kpi-value text-money" style="color:#16a34a;">{{ number_format($stats['total_net_proprio'], 0, ',', ' ') }} <span style="font-size:13px;font-weight:500;color:var(--text-3);">FCFA</span></div>
            <div class="kpi-sub">Après déduction commissions</div>
        </div>

    </div>

    {{-- ── Stats secondaires ── --}}
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:10px;" class="section-gap">

        <div style="background:#eff6ff;border:1px solid #dbeafe;border-radius:var(--radius);padding:14px 16px;text-align:center;">
            <div style="font-size:22px;font-weight:800;color:#2563eb;">{{ $stats['nb_proprietaires'] }}</div>
            <div style="font-size:11px;color:#3b82f6;margin-top:3px;font-weight:500;">Propriétaires</div>
        </div>

        <div style="background:#faf5ff;border:1px solid #e9d5ff;border-radius:var(--radius);padding:14px 16px;text-align:center;">
            <div style="font-size:22px;font-weight:800;color:#7c3aed;">{{ $stats['nb_locataires'] }}</div>
            <div style="font-size:11px;color:#8b5cf6;margin-top:3px;font-weight:500;">Locataires</div>
        </div>

        <div style="background:#f0f9ff;border:1px solid #bae6fd;border-radius:var(--radius);padding:14px 16px;text-align:center;">
            <div style="font-size:22px;font-weight:800;color:#0284c7;">{{ $stats['nb_biens'] }}</div>
            <div style="font-size:11px;color:#0ea5e9;margin-top:3px;font-weight:500;">Biens</div>
        </div>

        <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:var(--radius);padding:14px 16px;text-align:center;">
            <div style="font-size:18px;font-weight:800;color:#16a34a;" class="text-money">{{ number_format($stats['total_net_proprio'], 0, ',', ' ') }} F</div>
            <div style="font-size:11px;color:#22c55e;margin-top:3px;font-weight:500;">Net reversé</div>
        </div>

    </div>

    {{-- ── Derniers paiements ── --}}
    <div class="card section-gap">
        <div class="card-header">
            <span class="card-title">Derniers paiements</span>
            <a href="{{ route('admin.paiements.index') }}" class="link">Voir tout →</a>
        </div>

        {{-- Version mobile : cartes --}}
        <div class="mobile-cards" style="padding:12px;">
            @forelse($derniersPaiements as $p)
                <div class="mobile-card">
                    <div class="mobile-card-row">
                        <span class="mobile-card-label">Référence</span>
                        <span class="text-ref">{{ $p->reference_paiement }}</span>
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
                        <span class="mobile-card-value">{{ \Carbon\Carbon::parse($p->periode)->translatedFormat('M Y') }}</span>
                    </div>
                    <div class="mobile-card-row" style="margin-top:6px;padding-top:8px;border-top:1px solid var(--border);">
                        <span class="mobile-card-label">Montant</span>
                        <span style="font-weight:700;font-size:14px;" class="text-money">{{ number_format($p->montant_encaisse, 0, ',', ' ') }} F</span>
                    </div>
                    <div class="mobile-card-row">
                        <span class="mobile-card-label">Net proprio</span>
                        <span style="font-weight:700;color:#16a34a;" class="text-money">{{ number_format($p->net_proprietaire, 0, ',', ' ') }} F</span>
                    </div>
                </div>
            @empty
                <div style="text-align:center;padding:32px;color:var(--text-3);font-size:13px;">
                    Aucun paiement enregistré
                </div>
            @endforelse
        </div>

        {{-- Version desktop : tableau --}}
        <div class="desktop-table table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Réf</th>
                        <th>Locataire</th>
                        <th>Bien</th>
                        <th>Période</th>
                        <th style="text-align:right;">Montant</th>
                        <th style="text-align:right;">Net proprio</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($derniersPaiements as $p)
                        <tr>
                            <td><span class="text-ref">{{ $p->reference_paiement }}</span></td>
                            <td>{{ $p->contrat->locataire->name }}</td>
                            <td style="color:var(--text-2);">{{ $p->contrat->bien->reference }}</td>
                            <td style="color:var(--text-2);">{{ \Carbon\Carbon::parse($p->periode)->translatedFormat('M Y') }}</td>
                            <td style="text-align:right;" class="text-money">{{ number_format($p->montant_encaisse, 0, ',', ' ') }} F</td>
                            <td style="text-align:right;color:#16a34a;" class="text-money">{{ number_format($p->net_proprietaire, 0, ',', ' ') }} F</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center;padding:32px;color:var(--text-3);">
                                Aucun paiement enregistré
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ── Graphique loyers par mois ── --}}
    @if($loyersParMois->count() > 0)
    <div class="card">
        <div class="card-header">
            <span class="card-title">Évolution des loyers — 6 derniers mois</span>
        </div>
        <div class="card-body">
            @php
                $maxVal = $loyersParMois->max('total') ?: 1;
            @endphp
            <div style="display:flex;align-items:flex-end;gap:8px;height:120px;">
                @foreach($loyersParMois as $mois)
                    @php
                        $hauteur = round(($mois->total / $maxVal) * 100);
                        $label   = \Carbon\Carbon::createFromFormat('Y-m', $mois->mois)->translatedFormat('M');
                    @endphp
                    <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:6px;height:100%;">
                        <div style="flex:1;display:flex;align-items:flex-end;width:100%;">
                            <div style="width:100%;height:{{ $hauteur }}%;background:var(--agency);border-radius:4px 4px 0 0;opacity:.85;transition:opacity .2s;"
                                 title="{{ number_format($mois->total, 0, ',', ' ') }} FCFA">
                            </div>
                        </div>
                        <span style="font-size:10px;color:var(--text-3);font-weight:500;">{{ $label }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

</x-app-layout>