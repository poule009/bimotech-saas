<x-app-layout>
    <x-slot name="header">Déclarations TVA mensuelles</x-slot>

<style>
.page-wrap  { padding:24px 32px 48px; }
.filter-bar { background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:14px 18px;margin-bottom:22px;display:flex;align-items:center;gap:10px; }
.filter-sel { padding:8px 13px;border:1px solid #e5e7eb;border-radius:8px;font-size:13px;font-family:'DM Sans',sans-serif;background:#f9fafb;outline:none; }
.filter-btn { padding:8px 18px;background:#0d1117;color:#fff;border:none;border-radius:8px;font-size:12px;font-weight:500;font-family:'DM Sans',sans-serif;cursor:pointer; }

.tva-table { width:100%;border-collapse:collapse;background:#fff;border:1px solid #e5e7eb;border-radius:12px;overflow:hidden; }
.tva-table thead th { background:#0d1117;color:#fff;padding:11px 14px;text-align:left;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;white-space:nowrap; }
.tva-table thead th.r { text-align:right; }
.tva-table tbody td { padding:10px 14px;border-bottom:1px solid #f3f4f6;font-size:12.5px;color:#374151;vertical-align:middle; }
.tva-table tbody td.r { text-align:right;font-weight:600;font-variant-numeric:tabular-nums; }
.tva-table tbody tr:last-child td { border-bottom:none; }
.tva-table tbody tr:hover td { background:#fafafa; }
.tva-table tfoot td { background:#f5e9c9;padding:10px 14px;font-weight:700;font-size:12.5px;border-top:2px solid #c9a84c; }
.tva-table tfoot td.r { text-align:right;font-variant-numeric:tabular-nums; }

.badge { display:inline-flex;align-items:center;gap:4px;padding:3px 9px;border-radius:99px;font-size:10.5px;font-weight:600;white-space:nowrap; }
.badge-futur    { background:#f3f4f6;color:#9ca3af; }
.badge-nc       { background:#f3f4f6;color:#6b7280; }
.badge-brouil   { background:#dbeafe;color:#1d4ed8; }
.badge-validee  { background:#fef9c3;color:#854d0e; }
.badge-deposee  { background:#dcfce7;color:#16a34a; }
.badge-retard   { background:#fee2e2;color:#dc2626;border:1px solid #fecaca; }

.btn-sm { display:inline-flex;align-items:center;gap:4px;padding:5px 10px;border-radius:7px;font-size:11px;font-weight:500;font-family:'DM Sans',sans-serif;text-decoration:none;cursor:pointer;border:none; }
.btn-dark  { background:#0d1117;color:#fff; }
.btn-light { background:#f9fafb;color:#374151;border:1px solid #e5e7eb; }
.btn-gold  { background:#f5e9c9;color:#8a6e2f;border:1px solid #c9a84c; }

.credit-badge { display:inline-flex;align-items:center;gap:6px;padding:8px 14px;background:#fff7ed;border:1px solid #fed7aa;border-radius:9px;font-size:12px;font-weight:600;color:#c2410c; }

.row-futur td { color:#d1d5db; }
.row-retard td { background:#fff5f5; }
</style>

<div class="page-wrap">

    {{-- En-tête --}}
    <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:22px">
        <div>
            <h1 style="font-family:'Syne',sans-serif;font-size:22px;font-weight:700;color:#0d1117;letter-spacing:-.4px">
                Déclarations TVA mensuelles
            </h1>
            <p style="font-size:13px;color:#6b7280;margin-top:3px">
                TVA agence — Art. 369-370 CGI Sénégal — Année {{ $annee }}
            </p>
        </div>
        <div style="text-align:right;font-size:12px;color:#9ca3af;background:#f9fafb;border:1px solid #e5e7eb;border-radius:9px;padding:10px 14px;line-height:1.7">
            📋 Déclaration mensuelle obligatoire<br>
            <span style="color:#6b7280">À déposer avant le <strong>15 du mois M+1</strong></span>
        </div>
    </div>

    @if(session('success'))
    <div style="margin-bottom:16px;padding:11px 16px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:9px;font-size:13px;color:#16a34a;font-weight:500">
        ✓ {{ session('success') }}
    </div>
    @endif

    {{-- Crédit TVA reporté --}}
    @if($creditCumule > 0)
    <div style="margin-bottom:16px;display:flex;align-items:center;gap:8px">
        <span class="credit-badge">
            <svg style="width:14px;height:14px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            Crédit TVA reporté : {{ number_format($creditCumule, 0, ',', ' ') }} FCFA — Imputable sur prochaine déclaration
        </span>
    </div>
    @endif

    {{-- Filtre année --}}
    <form method="GET">
        <div class="filter-bar">
            <span style="font-size:12px;color:#6b7280;font-weight:500">Année fiscale :</span>
            <select name="annee" class="filter-sel">
                @foreach($anneesDisponibles as $a)
                    <option value="{{ $a }}" {{ $annee == $a ? 'selected' : '' }}>{{ $a }}</option>
                @endforeach
            </select>
            <button type="submit" class="filter-btn">Afficher</button>
        </div>
    </form>

    {{-- Tableau 12 mois --}}
    <div style="border-radius:12px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,.06)">
    <table class="tva-table">
        <thead>
            <tr>
                <th style="width:130px">Mois</th>
                <th class="r">TVA collectée</th>
                <th class="r">TVA déductible</th>
                <th class="r">Crédit entrant</th>
                <th class="r">TVA nette due</th>
                <th style="width:120px;text-align:center">Statut</th>
                <th style="width:180px;text-align:center">Actions</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalCollectee  = 0;
                $totalDeductible = 0;
                $totalNette      = 0;
            @endphp
            @foreach($mois as $m)
            @php
                $d = $m['declaration'];
                if ($d) {
                    $totalCollectee  += (float) $d->total_tva_collectee;
                    $totalDeductible += (float) $d->total_tva_deductible;
                    $totalNette      += (float) $d->tva_nette_due;
                }
            @endphp
            <tr class="{{ $m['statut'] === 'futur' ? 'row-futur' : ($m['statut'] === 'en_retard' ? 'row-retard' : '') }}">

                <td style="font-weight:600;color:#0d1117">{{ $m['label'] }}</td>

                <td class="r">
                    @if($d && $d->total_tva_collectee > 0)
                        {{ number_format($d->total_tva_collectee, 0, ',', ' ') }} F
                    @elseif($m['statut'] !== 'futur')
                        <span style="color:#9ca3af">—</span>
                    @else
                        <span style="color:#e5e7eb">—</span>
                    @endif
                </td>

                <td class="r">
                    @if($d && $d->total_tva_deductible > 0)
                        <span style="color:#16a34a">{{ number_format($d->total_tva_deductible, 0, ',', ' ') }} F</span>
                    @elseif($m['statut'] !== 'futur')
                        <span style="color:#9ca3af">—</span>
                    @else
                        <span style="color:#e5e7eb">—</span>
                    @endif
                </td>

                <td class="r">
                    @if($d && $d->credit_reporte_entrant > 0)
                        <span style="color:#f97316">{{ number_format($d->credit_reporte_entrant, 0, ',', ' ') }} F</span>
                    @else
                        <span style="color:#e5e7eb">—</span>
                    @endif
                </td>

                <td class="r">
                    @if($d)
                        @if($d->tva_nette_due > 0)
                            <span style="color:#dc2626;font-weight:700">{{ number_format($d->tva_nette_due, 0, ',', ' ') }} F</span>
                        @elseif($d->credit_reporte_sortant > 0)
                            <span style="color:#16a34a;font-size:11px">Crédit {{ number_format($d->credit_reporte_sortant, 0, ',', ' ') }} F</span>
                        @else
                            <span style="color:#16a34a">0 F</span>
                        @endif
                    @else
                        <span style="color:#e5e7eb">—</span>
                    @endif
                </td>

                <td style="text-align:center">
                    @if($m['statut'] === 'futur')
                        <span class="badge badge-futur">À venir</span>
                    @elseif($m['statut'] === 'non_calcule')
                        <span class="badge badge-nc">Non calculé</span>
                    @elseif($m['statut'] === 'brouillon')
                        <span class="badge badge-brouil">Brouillon</span>
                    @elseif($m['statut'] === 'validee')
                        <span class="badge badge-validee">Validée</span>
                    @elseif($m['statut'] === 'deposee')
                        <span class="badge badge-deposee">✓ Déposée</span>
                    @elseif($m['statut'] === 'en_retard')
                        <span class="badge badge-retard">⚠ En retard</span>
                    @endif
                </td>

                <td style="text-align:center">
                    @if($m['statut'] !== 'futur')
                    <div style="display:flex;gap:5px;justify-content:center;flex-wrap:wrap">
                        <a href="{{ route('admin.tva-agence.show', [$annee, $m['numero']]) }}" class="btn-sm btn-dark">
                            <svg style="width:10px;height:10px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            Voir
                        </a>
                        @if($m['statut'] !== 'futur')
                        <button type="button" class="btn-sm btn-light"
                            data-annee="{{ $annee }}" data-mois="{{ $m['numero'] }}"
                            onclick="recalculer(this)">
                            <svg style="width:10px;height:10px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
                            Recalculer
                        </button>
                        @endif
                    </div>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td>Total {{ $annee }}</td>
                <td class="r">{{ number_format($totalCollectee, 0, ',', ' ') }} F</td>
                <td class="r">{{ number_format($totalDeductible, 0, ',', ' ') }} F</td>
                <td class="r">—</td>
                <td class="r" style="color:#dc2626">{{ number_format($totalNette, 0, ',', ' ') }} F</td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
    </table>
    </div>

    {{-- Note légale --}}
    <div style="margin-top:20px;padding:12px 16px;background:#fffbeb;border:1px solid #fde68a;border-radius:10px;font-size:12px;color:#92400e;line-height:1.6">
        <strong>Art. 370 CGI Sénégal :</strong> La TVA nette due (collectée − déductible) doit être reversée à la DGI
        avant le <strong>15 de chaque mois M+1</strong>. Un crédit de TVA (déductible &gt; collectée) est reportable
        sur les mois suivants. Les montants TVA collectée sont automatiquement agrégés depuis les paiements enregistrés.
        <strong>Consultez votre Centre des Services Fiscaux (CSF) pour le dépôt officiel.</strong>
    </div>
</div>

<script>
function recalculer(btn) {
    const annee = btn.dataset.annee;
    const mois  = btn.dataset.mois;
    btn.disabled = true;
    btn.textContent = '…';

    fetch(`/admin/tva-agence/${annee}/${mois}/recalculer`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
            'Accept': 'application/json',
        },
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message);
            btn.disabled = false;
            btn.textContent = 'Recalculer';
        }
    })
    .catch(() => {
        alert('Erreur réseau');
        btn.disabled = false;
        btn.textContent = 'Recalculer';
    });
}
</script>

</x-app-layout>
