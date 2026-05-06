<x-app-layout>
    <x-slot name="header">Calendrier des échéances fiscales</x-slot>

<style>
.page-wrap { padding:24px 32px 48px; }

/* Synthèse */
.synth-box { background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:18px 22px;margin-bottom:22px; }
.synth-box h2 { font-size:13px;font-weight:700;color:#0d1117;margin:0 0 12px; }
.synth-grid { display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:10px; }
.synth-card { display:flex;align-items:center;gap:12px;padding:10px 14px;border-radius:9px;border:1px solid #e5e7eb;background:#fafafa; }
.synth-card.urgent  { background:#fff5f5;border-color:#fecaca; }
.synth-card.bientot { background:#fff7ed;border-color:#fed7aa; }
.synth-icon { width:34px;height:34px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0; }
.synth-icon.urgent  { background:#fee2e2; }
.synth-icon.bientot { background:#ffedd5; }
.synth-meta { flex:1;min-width:0; }
.synth-label { font-size:12.5px;font-weight:600;color:#111827;white-space:nowrap;overflow:hidden;text-overflow:ellipsis; }
.synth-date  { font-size:11px;color:#6b7280;margin-top:2px; }
.synth-empty { font-size:12.5px;color:#6b7280;padding:4px 0; }

/* Tableau */
.ech-table { width:100%;border-collapse:collapse;background:#fff;border:1px solid #e5e7eb;border-radius:12px;overflow:hidden; }
.ech-table thead th { background:#0d1117;color:#fff;padding:11px 14px;text-align:left;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;white-space:nowrap; }
.ech-table tbody td { padding:10px 14px;border-bottom:1px solid #f3f4f6;font-size:12.5px;color:#374151;vertical-align:middle; }
.ech-table tbody tr:last-child td { border-bottom:none; }
.ech-table tbody tr:hover td { background:#fafafa; }

/* Cellule mois */
td.mois-cell { font-weight:600;color:#0d1117;white-space:nowrap;min-width:110px; }

/* Badges statut */
.badge { display:inline-flex;align-items:center;gap:4px;padding:3px 9px;border-radius:99px;font-size:10.5px;font-weight:600;white-space:nowrap; }
.badge-urgent   { background:#fee2e2;color:#dc2626;border:1px solid #fecaca; }
.badge-bientot  { background:#ffedd5;color:#c2410c;border:1px solid #fed7aa; }
.badge-a_venir  { background:#f3f4f6;color:#6b7280; }
.badge-passee   { background:#f9fafb;color:#d1d5db; }
.badge-recurrent{ background:#dbeafe;color:#1d4ed8; }
.badge-hors_app { background:#f3f4f6;color:#9ca3af; }

/* Badges type */
.type-badge { display:inline-block;padding:2px 7px;border-radius:5px;font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:.3px; }
.type-decl  { background:#ede9fe;color:#5b21b6; }
.type-paie  { background:#d1fae5;color:#065f46; }
.type-emis  { background:#fef3c7;color:#92400e; }
.type-mens  { background:#e0f2fe;color:#0369a1; }

/* Boutons action */
.btn-sm { display:inline-flex;align-items:center;gap:4px;padding:5px 11px;border-radius:7px;font-size:11px;font-weight:500;font-family:'DM Sans',sans-serif;text-decoration:none;cursor:pointer;border:none;white-space:nowrap; }
.btn-dark  { background:#0d1117;color:#fff; }
.btn-light { background:#f9fafb;color:#374151;border:1px solid #e5e7eb; }
.btn-muted { background:#f3f4f6;color:#9ca3af;cursor:default; }

/* Séparateurs de section */
.section-row td { background:#f9fafb;padding:6px 14px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#9ca3af;border-bottom:1px solid #f3f4f6; }

/* Note de bas de page */
.note-bas { margin-top:18px;padding:14px 18px;background:#f8f9fa;border:1px solid #e9ecef;border-radius:10px;font-size:11.5px;color:#6b7280;line-height:1.6; }
</style>

<div class="page-wrap">

    {{-- ── Synthèse ─────────────────────────────────────────────────────── --}}
    <div class="synth-box">
        <h2>
            @if(count($echeancesUrgentes) > 0)
                {{ count($echeancesUrgentes) }} échéance{{ count($echeancesUrgentes) > 1 ? 's' : '' }}
                dans les 30 prochains jours
            @else
                Aucune échéance dans les 30 prochains jours
            @endif
        </h2>

        @if(count($echeancesUrgentes) > 0)
            <div class="synth-grid">
                @foreach($echeancesUrgentes as $e)
                    <div class="synth-card {{ $e['statut'] }}">
                        <div class="synth-icon {{ $e['statut'] }}">
                            {{ $e['statut'] === 'urgent' ? '🔴' : '🟠' }}
                        </div>
                        <div class="synth-meta">
                            <div class="synth-label">{{ $e['label'] }}</div>
                            <div class="synth-date">
                                {{ $e['date']?->format('d/m/Y') }}
                                @php
                                    $j = (int) $today->diffInDays($e['date']);
                                @endphp
                                — J-{{ $j }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="synth-empty">Toutes les prochaines échéances sont à plus de 30 jours.</p>
        @endif
    </div>

    {{-- ── Tableau principal ───────────────────────────────────────────── --}}
    <table class="ech-table">
        <thead>
            <tr>
                <th>Mois / Date</th>
                <th>Échéance</th>
                <th>Type</th>
                <th>Statut</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>

            {{-- Récurrents (mois_num = 0) d'abord dans leur section --}}
            @php
                $fixes     = array_filter($echeances, fn($e) => ! $e['recurrent'] && $e['statut'] !== 'hors_app');
                $recurrents = array_filter($echeances, fn($e) => $e['recurrent'] && $e['statut'] !== 'hors_app');
                $horsApp   = array_filter($echeances, fn($e) => $e['statut'] === 'hors_app');
            @endphp

            {{-- ── Échéances annuelles fixes ── --}}
            <tr class="section-row"><td colspan="5">Échéances annuelles</td></tr>

            @foreach($fixes as $e)
                <tr>
                    <td class="mois-cell">
                        @php
                            $moisLabels = [1=>'Janvier',2=>'Février',3=>'Mars',4=>'Avril',5=>'Mai',6=>'Juin',7=>'Juillet',8=>'Août',9=>'Septembre',10=>'Octobre',11=>'Novembre',12=>'Décembre'];
                        @endphp
                        {{ $moisLabels[$e['mois_num']] ?? '' }}
                        <span style="font-weight:400;color:#6b7280;">{{ $e['jour'] }}</span>
                    </td>
                    <td style="font-weight:500;">{{ $e['label'] }}</td>
                    <td>
                        @php
                            $typeClass = match($e['type']) {
                                'Déclaration' => 'type-decl',
                                'Paiement'    => 'type-paie',
                                'Émission'    => 'type-emis',
                                default       => 'type-mens',
                            };
                        @endphp
                        <span class="type-badge {{ $typeClass }}">{{ $e['type'] }}</span>
                    </td>
                    <td>
                        @php
                            $statutLabel = match($e['statut']) {
                                'urgent'   => '🔴 Urgent',
                                'bientot'  => '🟠 Bientôt',
                                'a_venir'  => '⬜ À venir',
                                'passee'   => '✔ Passée',
                                default    => $e['statut'],
                            };
                        @endphp
                        <span class="badge badge-{{ $e['statut'] }}">{{ $statutLabel }}</span>
                        @if($e['date'] && in_array($e['statut'], ['urgent','bientot','a_venir']))
                            <span style="font-size:10.5px;color:#9ca3af;margin-left:4px;">
                                {{ $e['date']->format('d/m/Y') }}
                            </span>
                        @endif
                    </td>
                    <td>
                        @if($e['lien'])
                            <a href="{{ $e['lien'] }}" class="btn-sm btn-dark">
                                {{ $e['lien_label'] }} →
                            </a>
                        @else
                            <span class="btn-sm btn-muted">{{ $e['lien_label'] }}</span>
                        @endif
                    </td>
                </tr>
            @endforeach

            {{-- ── Échéances récurrentes ── --}}
            <tr class="section-row"><td colspan="5">Récurrentes (mensuel / trimestriel)</td></tr>

            @foreach($recurrents as $e)
                <tr>
                    <td class="mois-cell" style="color:#6b7280;font-weight:500;">
                        @if($e['label'] === 'TVA mensuelle' || $e['label'] === 'BRS mensuel')
                            Mensuel — 15
                        @elseif($e['label'] === 'BRS trimestriel')
                            Trimestriel — 15
                        @else
                            —
                        @endif
                    </td>
                    <td style="font-weight:500;">{{ $e['label'] }}</td>
                    <td>
                        @php
                            $typeClass = match($e['type']) {
                                'Déclaration' => 'type-decl',
                                'Paiement'    => 'type-paie',
                                'Émission'    => 'type-emis',
                                default       => 'type-mens',
                            };
                        @endphp
                        <span class="type-badge {{ $typeClass }}">{{ $e['type'] }}</span>
                    </td>
                    <td>
                        @if($e['statut'] === 'recurrent')
                            <span class="badge badge-recurrent">↗ En cours</span>
                        @else
                            @php
                                $j = $e['date'] ? (int) $today->diffInDays($e['date']) : null;
                            @endphp
                            <span class="badge badge-{{ $e['statut'] }}">
                                {{ match($e['statut']) {
                                    'urgent'  => '🔴 Urgent J-' . $j,
                                    'bientot' => '🟠 Bientôt J-' . $j,
                                    'a_venir' => '⬜ À venir',
                                    default   => $e['statut'],
                                } }}
                            </span>
                        @endif
                    </td>
                    <td>
                        @if($e['lien'])
                            <a href="{{ $e['lien'] }}" class="btn-sm btn-dark">{{ $e['lien_label'] }} →</a>
                        @else
                            <span class="btn-sm btn-muted">{{ $e['lien_label'] }}</span>
                        @endif
                    </td>
                </tr>
            @endforeach

            {{-- ── Hors périmètre ── --}}
            @if(count($horsApp) > 0)
                <tr class="section-row"><td colspan="5">Hors périmètre Bimotech</td></tr>
                @foreach($horsApp as $e)
                    <tr>
                        <td class="mois-cell" style="color:#9ca3af;font-weight:400;">Annuel</td>
                        <td style="color:#9ca3af;">{{ $e['label'] }}</td>
                        <td>
                            <span class="type-badge type-mens">{{ $e['type'] }}</span>
                        </td>
                        <td>
                            <span class="badge badge-hors_app">ℹ Hors app</span>
                        </td>
                        <td>
                            <span class="btn-sm btn-muted">{{ $e['lien_label'] }}</span>
                        </td>
                    </tr>
                @endforeach
            @endif

        </tbody>
    </table>

    {{-- ── Note de bas de page ──────────────────────────────────────────── --}}
    <div class="note-bas">
        <strong>Note :</strong> Ce calendrier est fourni à titre indicatif. Les dates peuvent varier selon les décisions
        de la DGID. Les agences immobilières sont exclues de la CGU et relèvent de la CEL (Art. 320-338 CGI SN).
        Consultez un expert-comptable pour votre situation spécifique.
    </div>

</div>
</x-app-layout>
