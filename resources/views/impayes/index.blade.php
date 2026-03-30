<x-app-layout>
    <x-slot name="header">Impayés</x-slot>

    {{-- Alertes --}}
    @if(session('success'))
        <div class="alert alert-success section-gap">✅ {{ session('success') }}</div>
    @endif
    @if($errors->has('general'))
        <div class="alert alert-error section-gap">❌ {{ $errors->first('general') }}</div>
    @endif

    {{-- Header + Filtre --}}
    <div class="flex-between section-gap" style="flex-wrap:wrap;gap:12px;">
        <div>
            <h1 style="font-size:20px;font-weight:700;color:var(--text);letter-spacing:-.3px;">Gestion des impayés</h1>
            <p style="font-size:13px;color:var(--text-3);margin-top:3px;">{{ $periode->translatedFormat('F Y') }}</p>
        </div>
        <form method="GET" action="{{ route('admin.impayes.index') }}"
              style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
            <select name="mois" style="border:1.5px solid var(--border);border-radius:var(--radius-sm);padding:8px 12px;font-size:13px;color:var(--text);background:var(--surface);outline:none;font-family:inherit;cursor:pointer;">
                @foreach(range(1, 12) as $m)
                    <option value="{{ $m }}" {{ $mois == $m ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create(null, $m)->translatedFormat('F') }}
                    </option>
                @endforeach
            </select>
            <select name="annee" style="border:1.5px solid var(--border);border-radius:var(--radius-sm);padding:8px 12px;font-size:13px;color:var(--text);background:var(--surface);outline:none;font-family:inherit;cursor:pointer;">
                @foreach(range(now()->year - 1, now()->year) as $a)
                    <option value="{{ $a }}" {{ $annee == $a ? 'selected' : '' }}>{{ $a }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-primary btn-sm">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:14px;height:14px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                </svg>
                Filtrer
            </button>
        </form>
    </div>

    {{-- KPI --}}
    <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:10px;" class="section-gap" id="impayes-kpi">
        <div class="kpi" style="border-color:#fecaca;background:#fef2f2;">
            <div class="kpi-label" style="color:#ef4444;">Impayés</div>
            <div class="kpi-value" style="color:#dc2626;">{{ $stats['nb_impayes'] }}</div>
            <div class="kpi-sub" style="color:#f87171;">contrats en retard</div>
        </div>
        <div class="kpi" style="border-color:#bbf7d0;background:#f0fdf4;">
            <div class="kpi-label" style="color:#22c55e;">Payés</div>
            <div class="kpi-value" style="color:#16a34a;">{{ $stats['nb_payes'] }}</div>
            <div class="kpi-sub" style="color:#4ade80;">paiements reçus</div>
        </div>
        <div class="kpi" style="border-color:#fde68a;background:#fffbeb;">
            <div class="kpi-label" style="color:#f59e0b;">Montant dû</div>
            <div class="kpi-value text-money" style="color:#d97706;font-size:18px;">
                {{ number_format($stats['montant_du'], 0, ',', ' ') }} <span style="font-size:12px;font-weight:500;">FCFA</span>
            </div>
        </div>
        <div class="kpi">
            <div class="kpi-label">Taux de recouvrement</div>
            @php
                $taux = $stats['taux_recouvrement'];
                $tauxColor = $taux >= 80 ? '#16a34a' : ($taux >= 50 ? '#d97706' : '#dc2626');
                $barColor  = $taux >= 80 ? '#22c55e' : ($taux >= 50 ? '#f59e0b' : '#ef4444');
            @endphp
            <div class="kpi-value" style="color:{{ $tauxColor }};">{{ $taux }}%</div>
            <div class="progress" style="margin-top:8px;">
                <div class="progress-bar" style="width:{{ $taux }}%;background:{{ $barColor }};"></div>
            </div>
        </div>
    </div>

    {{-- Section Impayés --}}
    @if($impayes->isNotEmpty())

        {{-- Header section --}}
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
            <div style="display:flex;align-items:center;gap:8px;">
                <div style="width:8px;height:8px;border-radius:50%;background:#ef4444;animation:pulse 2s infinite;"></div>
                <span style="font-size:15px;font-weight:700;color:#dc2626;">
                    Loyers impayés — {{ $impayes->count() }} contrat(s)
                </span>
            </div>
            <span style="font-size:13px;font-weight:600;color:#dc2626;">
                Total dû : {{ number_format($stats['montant_du'], 0, ',', ' ') }} FCFA
            </span>
        </div>

        {{-- Mobile cards --}}
        <div class="mobile-cards section-gap">
            @foreach($impayes as $item)
                <div class="mobile-card" style="border-color:#fecaca;background:#fff9f9;">
                    <div class="flex-between" style="margin-bottom:10px;">
                        <div>
                            <div style="font-weight:700;font-size:14px;color:var(--text);">{{ $item['contrat']->bien->reference }}</div>
                            <div style="font-size:11px;color:var(--text-3);">{{ $item['contrat']->bien->ville }}</div>
                        </div>
                        @if($item['jours_retard'] > 0)
                            <span class="badge {{ $item['jours_retard'] > 15 ? 'badge-red' : 'badge-amber' }}">
                                {{ $item['jours_retard'] }}j de retard
                            </span>
                        @else
                            <span class="badge badge-gray">Pas encore dû</span>
                        @endif
                    </div>
                    <div class="mobile-card-row">
                        <span class="mobile-card-label">Locataire</span>
                        <span class="mobile-card-value">{{ $item['contrat']->locataire->name }}</span>
                    </div>
                    <div class="mobile-card-row">
                        <span class="mobile-card-label">Téléphone</span>
                        <span class="mobile-card-value">
                            @php
                                $telBrut   = $item['contrat']->locataire->telephone ?? '';
                                $telDigits = preg_replace('/[^0-9]/', '', $telBrut);
                                $nomLoc    = $item['contrat']->locataire->name;
                                $moisImp   = $periode->translatedFormat('F Y');
                                $montantImp = number_format($item['montant_du'], 0, ',', ' ');
                                $waMsg     = "Bonjour {$nomLoc}, votre loyer du mois de {$moisImp} d'un montant de {$montantImp} FCFA est en attente de règlement. Merci de régulariser votre situation. Cordialement, votre agence immobilière.";
                                $waLink    = $telDigits ? 'https://wa.me/' . $telDigits . '?text=' . rawurlencode($waMsg) : null;
                            @endphp
                            @if($telBrut)
                                <a href="tel:{{ $telBrut }}" style="color:var(--agency);font-weight:600;text-decoration:none;">
                                    📞 {{ $telBrut }}
                                </a>
                            @else
                                —
                            @endif
                        </span>
                    </div>
                    @if($waLink)
                    <div class="mobile-card-row">
                        <span class="mobile-card-label">WhatsApp</span>
                        <a href="{{ $waLink }}" target="_blank"
                           style="display:inline-flex;align-items:center;gap:5px;background:#25d366;color:#fff;padding:5px 10px;border-radius:20px;font-size:12px;font-weight:600;text-decoration:none;">
                            <svg viewBox="0 0 24 24" fill="currentColor" style="width:13px;height:13px;"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                            Envoyer message
                        </a>
                    </div>
                    @endif
                    <div class="mobile-card-row">
                        <span class="mobile-card-label">Propriétaire</span>
                        <span class="mobile-card-value">{{ $item['contrat']->bien->proprietaire->name }}</span>
                    </div>
                    <div class="mobile-card-row" style="margin-top:8px;padding-top:8px;border-top:1px solid #fecaca;">
                        <span class="mobile-card-label">Montant dû</span>
                        <span style="font-weight:800;font-size:15px;color:#dc2626;" class="text-money">
                            {{ number_format($item['montant_du'], 0, ',', ' ') }} F
                        </span>
                    </div>
                    <div style="display:flex;gap:8px;margin-top:12px;">
                        <a href="{{ route('admin.paiements.create', ['contrat_id' => $item['contrat']->id]) }}"
                           class="btn btn-sm" style="flex:1;justify-content:center;background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0;">
                            + Paiement
                        </a>
                        <form method="POST" action="{{ route('admin.impayes.relance', $item['contrat']) }}">
                            @csrf
                            <input type="hidden" name="mois" value="{{ $mois }}">
                            <input type="hidden" name="annee" value="{{ $annee }}">
                            <button type="submit" class="btn btn-secondary btn-sm">📧 Relance</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Desktop table --}}
        <div class="desktop-table card section-gap" style="border-color:#fecaca;">
            <div style="padding:14px 20px;border-bottom:1px solid #fecaca;background:#fef2f2;display:flex;align-items:center;justify-content:space-between;">
                <span style="font-size:14px;font-weight:700;color:#dc2626;">⚠️ Loyers impayés — {{ $impayes->count() }} contrat(s)</span>
                <span style="font-size:13px;color:#dc2626;font-weight:600;">Total dû : {{ number_format($stats['montant_du'], 0, ',', ' ') }} FCFA</span>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Bien</th>
                            <th>Locataire</th>
                            <th>Téléphone</th>
                            <th>Propriétaire</th>
                            <th style="text-align:right;">Montant dû</th>
                            <th style="text-align:center;">Retard</th>
                            <th style="text-align:center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($impayes as $item)
                            <tr style="background:#fff9f9;" onmouseenter="this.style.background='#fef2f2'" onmouseleave="this.style.background='#fff9f9'">
                                <td>
                                    <div style="font-weight:600;font-size:13px;">{{ $item['contrat']->bien->reference }}</div>
                                    <div style="font-size:11px;color:var(--text-3);">{{ $item['contrat']->bien->ville }}</div>
                                </td>
                                <td>
                                    <div style="font-size:13px;font-weight:500;">{{ $item['contrat']->locataire->name }}</div>
                                    <div style="font-size:11px;color:var(--text-3);">{{ $item['contrat']->locataire->email }}</div>
                                </td>
                                <td style="font-size:13px;color:var(--text-2);">
                                    @php
                                        $telBrut2    = $item['contrat']->locataire->telephone ?? '';
                                        $telDigits2  = preg_replace('/[^0-9]/', '', $telBrut2);
                                        $nomLoc2     = $item['contrat']->locataire->name;
                                        $moisImp2    = $periode->translatedFormat('F Y');
                                        $montantImp2 = number_format($item['montant_du'], 0, ',', ' ');
                                        $waMsg2      = "Bonjour {$nomLoc2}, votre loyer du mois de {$moisImp2} d'un montant de {$montantImp2} FCFA est en attente de règlement. Merci de régulariser votre situation. Cordialement, votre agence immobilière.";
                                        $waLink2     = $telDigits2 ? 'https://wa.me/' . $telDigits2 . '?text=' . rawurlencode($waMsg2) : null;
                                    @endphp
                                    @if($telBrut2)
                                        <a href="tel:{{ $telBrut2 }}"
                                           style="color:var(--agency);font-weight:600;text-decoration:none;display:block;white-space:nowrap;">
                                            📞 {{ $telBrut2 }}
                                        </a>
                                        @if($waLink2)
                                            <a href="{{ $waLink2 }}" target="_blank"
                                               style="display:inline-flex;align-items:center;gap:4px;margin-top:4px;background:#25d366;color:#fff;padding:3px 8px;border-radius:12px;font-size:11px;font-weight:600;text-decoration:none;white-space:nowrap;">
                                                <svg viewBox="0 0 24 24" fill="currentColor" style="width:11px;height:11px;flex-shrink:0;"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                                WhatsApp
                                            </a>
                                        @endif
                                    @else
                                        —
                                    @endif
                                </td>
                                <td style="font-size:13px;color:var(--text-2);">
                                    {{ $item['contrat']->bien->proprietaire->name }}
                                </td>
                                <td style="text-align:right;">
                                    <span style="font-weight:800;color:#dc2626;font-size:14px;" class="text-money">
                                        {{ number_format($item['montant_du'], 0, ',', ' ') }} F
                                    </span>
                                </td>
                                <td style="text-align:center;">
                                    @if($item['jours_retard'] > 0)
                                        <span class="badge {{ $item['jours_retard'] > 15 ? 'badge-red' : 'badge-amber' }}">
                                            {{ $item['jours_retard'] }}j de retard
                                        </span>
                                    @else
                                        <span class="badge badge-gray">Pas encore dû</span>
                                    @endif
                                </td>
                                <td style="text-align:center;">
                                    <div style="display:flex;align-items:center;justify-content:center;gap:6px;">
                                        <a href="{{ route('admin.paiements.create', ['contrat_id' => $item['contrat']->id]) }}"
                                           class="btn btn-sm" style="background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0;">
                                            + Paiement
                                        </a>
                                        <form method="POST" action="{{ route('admin.impayes.relance', $item['contrat']) }}">
                                            @csrf
                                            <input type="hidden" name="mois" value="{{ $mois }}">
                                            <input type="hidden" name="annee" value="{{ $annee }}">
                                            <button type="submit" class="btn btn-secondary btn-sm">📧 Relance</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    @else
        <div class="card section-gap" style="border-color:#bbf7d0;background:#f0fdf4;text-align:center;padding:48px 20px;">
            <div style="font-size:48px;margin-bottom:12px;">🎉</div>
            <div style="font-size:17px;font-weight:700;color:#15803d;margin-bottom:6px;">Tous les loyers sont payés !</div>
            <div style="font-size:13px;color:#22c55e;">Aucun impayé pour {{ $periode->translatedFormat('F Y') }}</div>
        </div>
    @endif

    {{-- Section Payés --}}
    @if($payes->isNotEmpty())

        <div style="font-size:15px;font-weight:700;color:var(--text);margin-bottom:12px;">
            ✅ Loyers reçus — {{ $payes->count() }} paiement(s)
        </div>

        {{-- Mobile cards --}}
        <div class="mobile-cards section-gap">
            @foreach($payes as $item)
                <div class="mobile-card">
                    <div class="mobile-card-row">
                        <span class="mobile-card-label">Bien</span>
                        <span style="font-weight:700;color:var(--text);">{{ $item['contrat']->bien->reference }}</span>
                    </div>
                    <div class="mobile-card-row">
                        <span class="mobile-card-label">Locataire</span>
                        <span class="mobile-card-value">{{ $item['contrat']->locataire->name }}</span>
                    </div>
                    <div class="mobile-card-row">
                        <span class="mobile-card-label">Mode</span>
                        <span class="badge badge-gray">{{ ucfirst(str_replace('_', ' ', $item['paiement']->mode_paiement)) }}</span>
                    </div>
                    <div style="border-top:1px solid var(--border);margin-top:8px;padding-top:8px;">
                        <div class="mobile-card-row">
                            <span class="mobile-card-label">Montant</span>
                            <span style="font-weight:700;" class="text-money">{{ number_format($item['paiement']->montant_encaisse, 0, ',', ' ') }} F</span>
                        </div>
                        <div class="mobile-card-row">
                            <span class="mobile-card-label">Net proprio</span>
                            <span style="color:#16a34a;font-weight:700;" class="text-money">{{ number_format($item['paiement']->net_proprietaire, 0, ',', ' ') }} F</span>
                        </div>
                    </div>
                    <div style="margin-top:10px;">
                        <a href="{{ route('admin.paiements.pdf', $item['paiement']) }}"
                           target="_blank" class="btn btn-secondary btn-sm" style="width:100%;justify-content:center;">
                            📄 Télécharger PDF
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Desktop table --}}
        <div class="desktop-table card">
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Bien</th>
                            <th>Locataire</th>
                            <th>Mode</th>
                            <th style="text-align:right;">Montant</th>
                            <th style="text-align:right;">Net proprio</th>
                            <th style="text-align:center;">PDF</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payes as $item)
                            <tr>
                                <td style="font-weight:600;font-size:13px;">{{ $item['contrat']->bien->reference }}</td>
                                <td style="font-size:13px;color:var(--text-2);">{{ $item['contrat']->locataire->name }}</td>
                                <td>
                                    <span class="badge badge-gray">
                                        {{ ucfirst(str_replace('_', ' ', $item['paiement']->mode_paiement)) }}
                                    </span>
                                </td>
                                <td style="text-align:right;" class="text-money">
                                    {{ number_format($item['paiement']->montant_encaisse, 0, ',', ' ') }} F
                                </td>
                                <td style="text-align:right;color:#16a34a;font-weight:700;" class="text-money">
                                    {{ number_format($item['paiement']->net_proprietaire, 0, ',', ' ') }} F
                                </td>
                                <td style="text-align:center;">
                                    <a href="{{ route('admin.paiements.pdf', $item['paiement']) }}"
                                       target="_blank" class="btn btn-secondary btn-sm">
                                        📄 PDF
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    @endif

    {{-- Animation pulse --}}
    <style>
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: .4; }
        }
        @media (min-width: 768px) {
            #impayes-kpi { grid-template-columns: repeat(4, 1fr); }
        }
    </style>

</x-app-layout>