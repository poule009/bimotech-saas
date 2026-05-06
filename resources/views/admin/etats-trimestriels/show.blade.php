<x-app-layout>
    <x-slot name="header">État trimestriel BRS — T{{ $trimestre }} {{ $annee }}</x-slot>

<style>
.page-wrap { padding:24px 32px 48px; }
.card { background:#fff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden; }
.dt  { width:100%;border-collapse:collapse; }
.dt th { padding:9px 16px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#9ca3af;background:#f9fafb;border-bottom:1px solid #e5e7eb;text-align:left; }
.dt th.r { text-align:right; }
.dt td { padding:11px 16px;font-size:13px;color:#374151;border-bottom:1px solid #f3f4f6;vertical-align:top; }
.dt tbody tr:last-child td { border-bottom:none; }
.dt tbody tr:hover { background:#fafafa; }
.dt tfoot td { background:#f5e9c9;font-weight:700;padding:11px 16px;border-top:2px solid #c9a84c; }
.dt tfoot td.r { text-align:right; }
.dt td.r { text-align:right; font-family:'Syne',sans-serif;font-weight:600; }
.warn-ninea { display:inline-flex;align-items:center;gap:4px;background:#fff7ed;border:1px solid #fed7aa;border-radius:6px;padding:2px 7px;font-size:10px;color:#c2410c;font-weight:600;margin-top:3px; }
.brs-amt { font-family:'Syne',sans-serif;font-weight:600;color:#dc2626; }
.net-amt { font-family:'Syne',sans-serif;font-weight:600;color:#374151; }
.btn-pdf { display:inline-flex;align-items:center;gap:5px;padding:8px 16px;background:#0d1117;color:#fff;border:none;border-radius:8px;font-size:12px;font-weight:500;font-family:'DM Sans',sans-serif;text-decoration:none; }
.btn-csv { display:inline-flex;align-items:center;gap:5px;padding:8px 16px;background:#fff;color:#374151;border:1px solid #e5e7eb;border-radius:8px;font-size:12px;font-weight:500;font-family:'DM Sans',sans-serif;text-decoration:none; }
.btn-back { display:inline-flex;align-items:center;gap:5px;padding:8px 16px;background:#f9fafb;color:#374151;border:1px solid #e5e7eb;border-radius:8px;font-size:12px;font-weight:500;font-family:'DM Sans',sans-serif;text-decoration:none; }
</style>

<div class="page-wrap">

    {{-- En-tête + actions --}}
    <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:20px;gap:16px">
        <div>
            <a href="{{ route('admin.etats-trimestriels.index') }}" class="btn-back" style="margin-bottom:10px;display:inline-flex">
                <svg style="width:12px;height:12px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                Retour
            </a>
            <h1 style="font-family:'Syne',sans-serif;font-size:20px;font-weight:700;color:#0d1117;letter-spacing:-.3px">
                État trimestriel BRS — T{{ $trimestre }} {{ $annee }}
            </h1>
            <p style="font-size:13px;color:#6b7280;margin-top:3px">
                Art. 200 §5 CGI Sénégal · {{ $lignes->count() }} bailleur(s) concerné(s) ·
                Date limite : <strong>{{ $dateLimite->translatedFormat('d F Y') }}</strong>
            </p>
        </div>
        <div style="display:flex;gap:8px;flex-shrink:0">
            <a href="{{ route('admin.etats-trimestriels.pdf', [$annee, $trimestre]) }}"
               target="_blank" class="btn-pdf">
                <svg style="width:12px;height:12px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                Télécharger PDF
            </a>
            <a href="{{ route('admin.etats-trimestriels.csv', [$annee, $trimestre]) }}"
               class="btn-csv">
                <svg style="width:12px;height:12px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Télécharger CSV
            </a>
        </div>
    </div>

    {{-- Avertissement global si NINEA manquants --}}
    @if($lignes->where('has_warning_ninea', true)->count() > 0)
    <div style="background:#fff7ed;border:1px solid #fed7aa;border-left:4px solid #f97316;border-radius:0 8px 8px 0;padding:10px 14px;margin-bottom:16px;font-size:12px;color:#92400e;line-height:1.7">
        <strong>⚠ {{ $lignes->where('has_warning_ninea', true)->count() }} bailleur(s) sans NINEA</strong> —
        Pour ces bailleurs, le CGI (Art. 200 §5) impose d'indiquer à la place : date et lieu de naissance + numéro de pièce d'identité.
        Mettez à jour leur fiche avant le dépôt à la DGID.
    </div>
    @endif

    {{-- Tableau principal --}}
    <div class="card">
        <div style="padding:14px 18px;border-bottom:1px solid #e5e7eb;display:flex;align-items:center;justify-content:space-between">
            <div style="font-family:'Syne',sans-serif;font-size:14px;font-weight:700;color:#0d1117">
                Détail par bailleur — T{{ $trimestre }} {{ $annee }}
            </div>
            <div style="font-size:12px;color:#6b7280">{{ $lignes->count() }} bailleur(s)</div>
        </div>

        @if($lignes->isEmpty())
        <div style="padding:40px;text-align:center;color:#9ca3af;font-size:13px">
            Aucun paiement avec BRS retenu sur ce trimestre.
        </div>
        @else
        <div style="overflow-x:auto">
            <table class="dt">
                <thead>
                    <tr>
                        <th>Bailleur</th>
                        <th>NINEA / Pièce d'identité</th>
                        <th>Adresse</th>
                        <th>Période</th>
                        <th class="r">Loyers nets versés</th>
                        <th class="r">BRS retenu</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($lignes as $ligne)
                    <tr>
                        <td>
                            <div style="font-size:13px;font-weight:600;color:#0d1117">{{ $ligne['nom_complet'] }}</div>
                            @if($ligne['email'])
                            <div style="font-size:11px;color:#6b7280">{{ $ligne['email'] }}</div>
                            @endif
                            @if($ligne['telephone'])
                            <div style="font-size:11px;color:#9ca3af">{{ $ligne['telephone'] }}</div>
                            @endif
                        </td>
                        <td>
                            @if($ligne['ninea'])
                                <div style="font-size:12px;font-weight:600;color:#0d1117;font-family:monospace">{{ $ligne['ninea'] }}</div>
                            @else
                                <div class="warn-ninea">
                                    <svg style="width:9px;height:9px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                                    NINEA manquant
                                </div>
                                @if($ligne['cni'])
                                <div style="font-size:10px;color:#6b7280;margin-top:2px">CNI : {{ $ligne['cni'] }}</div>
                                @endif
                                @if($ligne['date_naissance'])
                                <div style="font-size:10px;color:#6b7280">
                                    Né(e) le : {{ \Carbon\Carbon::parse($ligne['date_naissance'])->format('d/m/Y') }}
                                </div>
                                @endif
                                <div style="font-size:10px;color:#c2410c;font-style:italic;margin-top:2px">
                                    ⚠ NINEA manquant — ce bailleur doit fournir son NINEA avant dépôt à la DGID.
                                    Sans NINEA, indiquer date/lieu naissance + pièce d'identité.
                                </div>
                            @endif
                        </td>
                        <td>
                            <div style="font-size:12px;color:#374151">{{ $ligne['adresse'] ?: '—' }}</div>
                        </td>
                        <td>
                            <div style="font-size:12px;color:#374151">{{ $ligne['periode_label'] }}</div>
                            <div style="font-size:10px;color:#9ca3af">{{ $ligne['nb_paiements'] }} paiement(s)</div>
                        </td>
                        <td class="r">
                            <span class="net-amt">{{ number_format($ligne['loyers_verses'], 0, ',', ' ') }} F</span>
                        </td>
                        <td class="r">
                            <span class="brs-amt">{{ number_format($ligne['brs_retenu'], 0, ',', ' ') }} F</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4">TOTAL T{{ $trimestre }} {{ $annee }} — {{ $lignes->count() }} bailleur(s)</td>
                        <td class="r">{{ number_format($totalNet, 0, ',', ' ') }} F</td>
                        <td class="r" style="color:#dc2626">{{ number_format($totalBrs, 0, ',', ' ') }} F</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @endif
    </div>

    {{-- Note légale --}}
    <div style="margin-top:14px;padding:12px 16px;background:#eff6ff;border:1px solid #bfdbfe;border-radius:10px;font-size:12px;color:#1e40af;line-height:1.6">
        <strong>Art. 200 §5 CGI Sénégal :</strong>
        Cet état doit être remis au Centre des Services Fiscaux avant le <strong>{{ $dateLimite->translatedFormat('d F Y') }}</strong>.
        Il doit comporter pour chaque bailleur : prénom, nom, emploi, adresse, NINEA (ou date/lieu naissance + pièce d'identité),
        montant des loyers nets reversés, et montant du BRS retenu.
    </div>

</div>
</x-app-layout>
