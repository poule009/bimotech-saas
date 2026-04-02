<x-app-layout>
    <x-slot name="header">Tableau de Bord — {{ $currentAgency?->name ?? 'BIMO-Tech' }}</x-slot>

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- ONBOARDING : EFFET CARTE CUIVRÉE                                   --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    @if($onboarding !== null && ! ($onboarding['tout_complete'] ?? false))
    <div style="background: linear-gradient(145deg, #fffcf5, #f8f4e9); border: 1px solid rgba(181, 140, 90, 0.2); border-radius: 24px; padding: 32px; margin-bottom: 32px; box-shadow: 0 20px 40px -15px rgba(181, 140, 90, 0.1);">
        
        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:30px;">
            <div style="display:flex; align-items:center; gap:18px;">
                <div style="width:56px; height:56px; border-radius:18px; background: #b58c5a; display:flex; align-items:center; justify-content:center; color:white; font-size:24px; box-shadow: 0 10px 20px -5px rgba(181, 140, 90, 0.4);">
                    🚀
                </div>
                <div>
                    <h2 style="font-size:18px; font-weight:800; color:#1a202c; letter-spacing:-0.5px;">Guide de configuration</h2>
                    <p style="font-size:13px; color:#b58c5a; font-weight:600;">{{ $onboarding['nb_completes'] }}/4 objectifs atteints</p>
                </div>
            </div>
            <div style="text-align:right;">
                <span style="font-size:28px; font-weight:900; color:#b58c5a; opacity:0.3;">{{ round(($onboarding['nb_completes'] / 4) * 100) }}%</span>
            </div>
        </div>

        <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(240px,1fr)); gap:20px;">
            @php 
                $etapes = [
                    ['id' => 'etape1', 'titre' => 'Identité Visuelle', 'route' => 'admin.agency.settings', 'icon' => '🎨'],
                    ['id' => 'etape2', 'titre' => 'Premier Mandat', 'route' => ['admin.users.create', 'proprietaire'], 'icon' => '🤝'],
                    ['id' => 'etape3', 'titre' => 'Parc Immobilier', 'route' => 'biens.create', 'icon' => '🏠'],
                    ['id' => 'etape4', 'titre' => 'Gestion Locative', 'route' => 'admin.contrats.create', 'icon' => '📑'],
                ];
            @endphp

            @foreach($etapes as $e)
                <div style="background: {{ $onboarding[$e['id']] ? 'rgba(255,255,255,0.8)' : '#fff' }}; border: 1px solid {{ $onboarding[$e['id']] ? '#e2e8f0' : 'rgba(181, 140, 90, 0.2)' }}; border-radius:20px; padding:20px; position:relative; transition: all 0.3s ease;">
                    <div style="font-size:20px; margin-bottom:12px;">{{ $e['icon'] }}</div>
                    <div style="font-size:14px; font-weight:700; color:#1a202c;">{{ $e['titre'] }}</div>
                    
                    @if($onboarding[$e['id']])
                        <div style="margin-top:10px; color:#16a34a; font-size:12px; font-weight:700; display:flex; align-items:center; gap:4px;">
                            <svg style="width:14px;height:14px;" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/></svg> Complété
                        </div>
                    @else
                        <a href="{{ is_array($e['route']) ? route($e['route'][0], $e['route'][1]) : route($e['route']) }}" style="margin-top:10px; display:inline-block; font-size:12px; font-weight:800; color:#b58c5a; text-transform:uppercase; letter-spacing:1px;">Configurer →</a>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- ACTIONS RAPIDES : BOUTONS FLOTTANTS                                --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    <div style="display:flex; gap:16px; margin-bottom:40px;">
        <a href="{{ route('admin.paiements.create') }}" class="btn btn-primary" style="background:#1a202c; color:white; border-radius:18px; padding:16px 28px; box-shadow: 0 10px 25px rgba(0,0,0,0.15);">
            <span style="font-size:18px; margin-right:8px;">+</span> Enregistrer un Paiement
        </a>
        <a href="{{ route('admin.contrats.create') }}" class="btn btn-secondary" style="background:white; color:#1a202c; border:1px solid #e2e8f0; border-radius:18px; padding:16px 28px;">
            Nouveau Contrat
        </a>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- BILAN FINANCIER : STYLE "AUDEMARS"                                 --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    <div class="card" style="background: rgba(255, 255, 255, 0.5); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.4); border-radius:32px; padding:32px; margin-bottom:40px;">
        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:32px;">
            <div>
                <h3 style="font-size:22px; font-weight:900; color:#1a202c; letter-spacing:-1px;">Performance Mensuelle</h3>
                <p style="font-size:13px; color:#718096; font-weight:500;">Bilan arrêté au {{ now()->translatedFormat('d F Y') }}</p>
            </div>
            <a href="{{ route('admin.rapports.financier') }}" style="background:#f7f4ec; color:#b58c5a; padding:10px 20px; border-radius:12px; font-size:12px; font-weight:800; text-transform:uppercase;">Rapport détaillé</a>
        </div>

        <div style="display:grid; grid-template-columns: repeat(3, 1fr); gap:32px;">
            <div style="position:relative;">
                <p style="font-size:11px; font-weight:800; color:#a0aec0; text-transform:uppercase; letter-spacing:1.5px; margin-bottom:10px;">Attendu</p>
                <div style="font-size:32px; font-weight:900; color:#1a202c;">{{ number_format($bilanMois['attendu'], 0, ',', ' ') }}<span style="font-size:16px; margin-left:4px; opacity:0.4;">FCFA</span></div>
            </div>

            <div style="position:relative; padding-left:32px; border-left:1px solid #e2e8f0;">
                <p style="font-size:11px; font-weight:800; color:#16a34a; text-transform:uppercase; letter-spacing:1.5px; margin-bottom:10px;">Encaissé</p>
                <div style="font-size:32px; font-weight:900; color:#16a34a;">{{ number_format($bilanMois['encaisse'], 0, ',', ' ') }}<span style="font-size:16px; margin-left:4px; opacity:0.6;">FCFA</span></div>
                <div style="font-size:12px; font-weight:700; color:#16a34a; margin-top:4px;">{{ $bilanMois['attendu'] > 0 ? round(($bilanMois['encaisse'] / $bilanMois['attendu']) * 100) : 0 }}% de recouvrement</div>
            </div>

            <div style="position:relative; padding-left:32px; border-left:1px solid #e2e8f0;">
                <p style="font-size:11px; font-weight:800; color:{{ $bilanMois['a_recouvrer'] > 0 ? '#dc2626' : '#16a34a' }}; text-transform:uppercase; letter-spacing:1.5px; margin-bottom:10px;">Reliquat</p>
                <div style="font-size:32px; font-weight:900; color:{{ $bilanMois['a_recouvrer'] > 0 ? '#dc2626' : '#16a34a' }};">{{ number_format($bilanMois['a_recouvrer'], 0, ',', ' ') }}<span style="font-size:16px; margin-left:4px; opacity:0.4;">FCFA</span></div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- GRILLE DE STATS SECONDAIRES                                        --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    <div style="display:grid; grid-template-columns: repeat(4, 1fr); gap:20px; margin-bottom:40px;">
        @php
            $stats_cards = [
                ['label' => 'Loyers Encaissés', 'val' => number_format($statsMois['loyers'], 0, ',', ' ') . ' F', 'sub' => $statsMois['nb_payes'].' paiements', 'color' => '#16a34a'],
                ['label' => 'Impayés (Mois)', 'val' => $nb_impayes_mois, 'sub' => 'Alertes actives', 'color' => $nb_impayes_mois > 0 ? '#dc2626' : '#1a202c'],
                ['label' => 'Commission Agence', 'val' => number_format($statsMois['commissions'], 0, ',', ' ') . ' F', 'sub' => 'Revenu net TTC', 'color' => '#b58c5a'],
                ['label' => 'Occupation', 'val' => $stats['taux_occupation'].'%', 'sub' => $stats['nb_biens_loues'].'/'.$stats['nb_biens'].' biens', 'color' => '#2b6cb0'],
            ];
        @endphp

        @foreach($stats_cards as $sc)
            <div class="kpi" style="background:white; border:none; box-shadow: 0 10px 30px -10px rgba(0,0,0,0.05); padding:24px; border-radius:24px;">
                <div style="font-size:10px; font-weight:800; color:#a0aec0; text-transform:uppercase; letter-spacing:1px; margin-bottom:8px;">{{ $sc['label'] }}</div>
                <div style="font-size:20px; font-weight:900; color:{{ $sc['color'] }};">{{ $sc['val'] }}</div>
                <div style="font-size:11px; color:#718096; margin-top:4px; font-weight:500;">{{ $sc['sub'] }}</div>
            </div>
        @endforeach
    </div>

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- TABLEAU DES DERNIERS FLUX                                          --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    <div class="card" style="background:white; border-radius:28px; border:none; box-shadow: 0 10px 40px -15px rgba(0,0,0,0.05); overflow:hidden;">
        <div style="padding:24px 32px; border-bottom:1px solid #f7f4ec; display:flex; justify-content:space-between; align-items:center;">
            <h3 style="font-size:16px; font-weight:800; color:#1a202c;">Flux de trésorerie récents</h3>
            <a href="{{ route('admin.paiements.index') }}" style="font-size:13px; font-weight:700; color:#b58c5a;">Historique complet →</a>
        </div>
        
        <div class="table-wrap">
            <table style="width:100%; border-collapse: separate; border-spacing: 0;">
                <thead>
                    <tr style="background:#fcfaf5;">
                        <th style="padding:16px 32px; font-size:10px; color:#b58c5a; text-transform:uppercase; letter-spacing:1px;">Référence</th>
                        <th style="padding:16px; font-size:10px; color:#b58c5a; text-transform:uppercase; letter-spacing:1px;">Locataire / Bien</th>
                        <th style="padding:16px; font-size:10px; color:#b58c5a; text-transform:uppercase; letter-spacing:1px;">Période</th>
                        <th style="padding:16px 32px; text-align:right; font-size:10px; color:#b58c5a; text-transform:uppercase; letter-spacing:1px;">Montant Net</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($derniersPaiements as $p)
                        <tr style="border-bottom:1px solid #f7f4ec;">
                            <td style="padding:20px 32px;"><span style="font-family:monospace; font-weight:700; color:#718096;">#{{ $p->reference_paiement }}</span></td>
                            <td style="padding:20px 16px;">
                                <div style="font-weight:700; color:#1a202c;">{{ $p->contrat->locataire->name }}</div>
                                <div style="font-size:12px; color:#a0aec0;">{{ $p->contrat->bien->reference }}</div>
                            </td>
                            <td style="padding:20px 16px;">
                                <span style="background:#f7f4ec; color:#b58c5a; padding:4px 10px; border-radius:8px; font-size:11px; font-weight:700;">{{ \Carbon\Carbon::parse($p->periode)->translatedFormat('M Y') }}</span>
                            </td>
                            <td style="padding:20px 32px; text-align:right; font-weight:900; color:#1a202c; font-size:15px;">
                                {{ number_format($p->montant_encaisse, 0, ',', ' ') }} F
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" style="padding:40px; text-align:center; color:#a0aec0;">Aucune transaction détectée.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</x-app-layout>