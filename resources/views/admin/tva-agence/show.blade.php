<x-app-layout>
    <x-slot name="header">Déclaration TVA — {{ $declaration->periode_label }}</x-slot>

<style>
.page-wrap { padding:24px 32px 48px; }

.section-card { background:#fff;border:1px solid #e5e7eb;border-radius:12px;overflow:hidden;margin-bottom:20px; }
.card-header  { padding:14px 20px;border-bottom:1px solid #f3f4f6;display:flex;align-items:center;justify-content:space-between;background:#f9fafb; }
.card-title   { font-family:'Syne',sans-serif;font-size:14px;font-weight:700;color:#0d1117; }
.card-sub     { font-size:11px;color:#9ca3af;margin-top:1px; }
.card-body    { padding:20px; }

.data-table { width:100%;border-collapse:collapse;font-size:12.5px; }
.data-table th { background:#0d1117;color:#fff;padding:8px 12px;text-align:left;font-size:10.5px;text-transform:uppercase;letter-spacing:.4px;white-space:nowrap; }
.data-table th.r { text-align:right; }
.data-table td { padding:8px 12px;border-bottom:1px solid #f3f4f6;color:#374151;vertical-align:middle; }
.data-table td.r { text-align:right;font-weight:600;font-variant-numeric:tabular-nums; }
.data-table tbody tr:last-child td { border-bottom:none; }
.data-table tfoot td { background:#f5e9c9;font-weight:700;padding:9px 12px;border-top:2px solid #c9a84c; }
.data-table tfoot td.r { text-align:right; }

.subtotal-row td { background:#f9fafb;font-weight:600;color:#0d1117;font-size:11.5px; }
.subtotal-row td.gold { color:#8a6e2f;background:#fdf8ef; }

.form-group { margin-bottom:16px; }
.form-label { display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px; }
.form-hint  { font-size:11px;color:#9ca3af;margin-top:3px; }
.form-input { width:100%;padding:9px 13px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;font-family:'DM Sans',sans-serif;outline:none;transition:border-color .15s; }
.form-input:focus { border-color:#0d1117; }
.form-row { display:grid;grid-template-columns:1fr 1fr;gap:16px; }

.result-row { display:flex;align-items:center;justify-content:space-between;padding:10px 0;border-bottom:1px solid #f3f4f6;font-size:13px; }
.result-row:last-child { border-bottom:none; }
.result-row.total { padding-top:14px;border-top:2px solid #0d1117;border-bottom:none;font-size:15px;font-weight:700; }
.result-row .lbl { color:#374151; }
.result-row .val { font-weight:700;font-variant-numeric:tabular-nums; }

.badge { display:inline-flex;align-items:center;gap:4px;padding:4px 10px;border-radius:99px;font-size:11px;font-weight:600; }
.badge-brouil  { background:#dbeafe;color:#1d4ed8; }
.badge-validee { background:#fef9c3;color:#854d0e; }
.badge-deposee { background:#dcfce7;color:#16a34a; }
.badge-retard  { background:#fee2e2;color:#dc2626;border:1px solid #fecaca; }

.btn { display:inline-flex;align-items:center;gap:6px;padding:9px 18px;border-radius:9px;font-size:13px;font-weight:600;font-family:'DM Sans',sans-serif;cursor:pointer;text-decoration:none;border:none; }
.btn-dark   { background:#0d1117;color:#fff; }
.btn-green  { background:#16a34a;color:#fff; }
.btn-orange { background:#ea580c;color:#fff; }
.btn-light  { background:#f9fafb;color:#374151;border:1px solid #e5e7eb; }
.btn-pdf    { background:#f5e9c9;color:#8a6e2f;border:1px solid #c9a84c; }

.alerte-due    { background:#fff7ed;border:1px solid #fed7aa;border-radius:10px;padding:14px 18px;color:#9a3412;font-size:13px;line-height:1.6; }
.alerte-credit { background:#eff6ff;border:1px solid #bfdbfe;border-radius:10px;padding:14px 18px;color:#1e40af;font-size:13px;line-height:1.6; }
@media(max-width:768px){.form-row{grid-template-columns:1fr}}
</style>

<div class="page-wrap">

    {{-- Barre de navigation + statut --}}
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:22px">
        <div style="display:flex;align-items:center;gap:12px">
            <a href="{{ route('admin.tva-agence.index') }}" style="color:#9ca3af;font-size:12px;text-decoration:none">
                ← Toutes les déclarations
            </a>
            <span style="color:#d1d5db">|</span>
            <h1 style="font-family:'Syne',sans-serif;font-size:18px;font-weight:700;color:#0d1117;letter-spacing:-.3px">
                Déclaration TVA — {{ $declaration->periode_label }}
            </h1>

            @if($declaration->statut === 'brouillon' && $declaration->est_en_retard)
                <span class="badge badge-retard">⚠ En retard</span>
            @elseif($declaration->statut === 'brouillon')
                <span class="badge badge-brouil">Brouillon</span>
            @elseif($declaration->statut === 'validee')
                <span class="badge badge-validee">Validée</span>
            @elseif($declaration->statut === 'deposee')
                <span class="badge badge-deposee">✓ Déposée à la DGI</span>
            @endif
        </div>

        <div style="display:flex;gap:8px;align-items:center">
            @if($declaration->statut !== 'deposee')
                {{-- Recalculer --}}
                <button type="button" class="btn btn-light" id="btn-recalc"
                    data-annee="{{ $annee }}" data-mois="{{ $mois }}"
                    onclick="recalculer(this)">
                    <svg style="width:13px;height:13px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
                    Recalculer
                </button>

                @if($declaration->statut === 'brouillon')
                <form method="POST" action="{{ route('admin.tva-agence.valider', [$annee, $mois]) }}" style="display:inline">
                    @csrf
                    <button type="submit" class="btn btn-dark">
                        <svg style="width:13px;height:13px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                        Valider
                    </button>
                </form>
                @endif

                @if($declaration->statut === 'validee')
                <form method="POST" action="{{ route('admin.tva-agence.deposee', [$annee, $mois]) }}" style="display:inline"
                    onsubmit="return confirm('Confirmer le dépôt à la DGI ? Cette action est irréversible.')">
                    @csrf
                    <button type="submit" class="btn btn-green">
                        <svg style="width:13px;height:13px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        Marquer comme déposée
                    </button>
                </form>
                @endif
            @else
                <span style="font-size:11px;color:#9ca3af">
                    Déposée le {{ $declaration->deposee_le->format('d/m/Y à H:i') }}
                </span>
            @endif

            <a href="{{ route('admin.tva-agence.pdf', [$annee, $mois]) }}" target="_blank" class="btn btn-pdf">
                <svg style="width:13px;height:13px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                Exporter PDF
            </a>
        </div>
    </div>

    @if(session('success'))
    <div style="margin-bottom:16px;padding:11px 16px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:9px;font-size:13px;color:#16a34a;font-weight:500">
        ✓ {{ session('success') }}
    </div>
    @endif

    @if($errors->any())
    <div style="margin-bottom:16px;padding:11px 16px;background:#fff1f2;border:1px solid #fecaca;border-radius:9px;font-size:13px;color:#dc2626">
        @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
    </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════════
         SECTION 1 — TVA COLLECTÉE (lecture seule, auto-calculée)
    ══════════════════════════════════════════════════════════════════════ --}}
    <div class="section-card">
        <div class="card-header">
            <div>
                <div class="card-title">TVA collectée — auto-calculée depuis les paiements</div>
                <div class="card-sub">Taux 18% — Art. 369 CGI SN · {{ $tvaData['nombre_paiements'] }} paiement(s) enregistré(s)</div>
            </div>
            <div style="font-size:20px;font-weight:800;font-family:'Syne',sans-serif;color:#0d1117">
                {{ number_format($tvaData['total_tva_collectee'], 0, ',', ' ') }} <span style="font-size:13px;font-weight:500;color:#9ca3af">FCFA</span>
            </div>
        </div>
        <div class="card-body" style="padding:0">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Référence</th>
                        <th>Locataire</th>
                        <th>Bien</th>
                        <th>Type</th>
                        <th>Période</th>
                        <th class="r">Loyer HT</th>
                        <th class="r">TVA commission</th>
                        <th class="r">TVA loyer</th>
                        <th class="r">TVA charges</th>
                        <th class="r">TVA honoraires</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tvaData['detail_par_contrat'] as $p)
                    <tr>
                        <td style="font-family:monospace;font-size:11px;color:#6b7280">{{ $p['reference'] }}</td>
                        <td>{{ $p['locataire'] }}</td>
                        <td style="max-width:120px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $p['bien'] }}</td>
                        <td>{{ ucfirst($p['type_bail']) }}</td>
                        <td style="font-size:11.5px;color:#6b7280">
                            @if($p['periode'])
                                {{ \Carbon\Carbon::parse($p['periode'])->translatedFormat('M Y') }}
                            @else—@endif
                        </td>
                        <td class="r">{{ $p['loyer_ht'] > 0 ? number_format($p['loyer_ht'], 0, ',', ' ').' F' : '—' }}</td>
                        <td class="r">{{ $p['tva_commission'] > 0 ? number_format($p['tva_commission'], 0, ',', ' ').' F' : '—' }}</td>
                        <td class="r">{{ $p['tva_loyer'] > 0 ? number_format($p['tva_loyer'], 0, ',', ' ').' F' : '—' }}</td>
                        <td class="r">{{ $p['tva_charges'] > 0 ? number_format($p['tva_charges'], 0, ',', ' ').' F' : '—' }}</td>
                        <td class="r">{{ $p['tva_frais'] > 0 ? number_format($p['tva_frais'], 0, ',', ' ').' F' : '—' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" style="text-align:center;color:#9ca3af;padding:24px;font-style:italic">
                            Aucun paiement enregistré pour ce mois
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="6">Sous-totaux TVA collectée</td>
                        <td class="r">{{ number_format($tvaData['tva_commissions'], 0, ',', ' ') }} F</td>
                        <td class="r">{{ number_format($tvaData['tva_loyers_commerciaux'], 0, ',', ' ') }} F</td>
                        <td class="r">{{ number_format($tvaData['tva_charges_forfait'], 0, ',', ' ') }} F</td>
                        <td class="r">{{ number_format($tvaData['tva_honoraires'], 0, ',', ' ') }} F</td>
                    </tr>
                </tfoot>
            </table>
            {{-- Récap par type --}}
            <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:1px;background:#f3f4f6;border-top:1px solid #f3f4f6">
                @foreach([
                    ['Commissions', $tvaData['tva_commissions'], '#eff6ff', '#1d4ed8'],
                    ['Loyers commerciaux', $tvaData['tva_loyers_commerciaux'], '#fff7ed', '#c2410c'],
                    ['Charges forfait', $tvaData['tva_charges_forfait'], '#f0fdf4', '#16a34a'],
                    ['Honoraires (frais entrée)', $tvaData['tva_honoraires'], '#fdf8ef', '#8a6e2f'],
                ] as [$label, $val, $bg, $color])
                <div style="background:{{ $bg }};padding:10px 14px;text-align:center">
                    <div style="font-size:10px;text-transform:uppercase;letter-spacing:.5px;color:#9ca3af;margin-bottom:3px">{{ $label }}</div>
                    <div style="font-size:14px;font-weight:700;color:{{ $color }}">{{ number_format($val, 0, ',', ' ') }} F</div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════
         SECTION 2 — TVA DÉDUCTIBLE (saisie manuelle)
    ══════════════════════════════════════════════════════════════════════ --}}
    <div class="section-card">
        <div class="card-header">
            <div>
                <div class="card-title">TVA déductible — saisie manuelle</div>
                <div class="card-sub">Saisir uniquement la TVA figurant sur des factures au nom de l'agence avec NINEA du fournisseur</div>
            </div>
            <div style="font-size:20px;font-weight:800;font-family:'Syne',sans-serif;color:#16a34a">
                {{ number_format($declaration->total_tva_deductible, 0, ',', ' ') }} <span style="font-size:13px;font-weight:500;color:#9ca3af">FCFA</span>
            </div>
        </div>
        <div class="card-body">
            @if($declaration->statut !== 'deposee')
            <form method="POST" action="{{ route('admin.tva-agence.update', [$annee, $mois]) }}">
                @csrf
                @method('PUT')

                <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:8px;padding:10px 14px;margin-bottom:18px;font-size:12px;color:#92400e">
                    ⚠ Saisir uniquement la TVA (18%) figurant sur des <strong>factures au nom de l'agence</strong>
                    avec le <strong>NINEA du fournisseur</strong>. Montant = TVA seule, pas TTC.
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">TVA sur achats / fournitures</label>
                        <input type="number" name="tva_achats_fournitures" class="form-input"
                            value="{{ old('tva_achats_fournitures', $declaration->tva_achats_fournitures) }}"
                            min="0" step="1" placeholder="0">
                        <div class="form-hint">Matériel, papeterie, logiciels… (HT + TVA 18% sur facture)</div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">TVA sur loyer du bureau de l'agence</label>
                        <input type="number" name="tva_loyer_bureau" class="form-input"
                            value="{{ old('tva_loyer_bureau', $declaration->tva_loyer_bureau) }}"
                            min="0" step="1" placeholder="0">
                        <div class="form-hint">Local commercial loué par l'agence (bail commercial assujetti)</div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">TVA déductible — autres</label>
                        <input type="number" name="tva_autres_deductible" class="form-input"
                            value="{{ old('tva_autres_deductible', $declaration->tva_autres_deductible) }}"
                            min="0" step="1" placeholder="0">
                        <div class="form-hint">Prestataires, conseil, autres achats sur facture NINEA</div>
                    </div>
                    <div class="form-group" style="grid-column:1/-1">
                        <label class="form-label">Notes internes (facultatif)</label>
                        <textarea name="notes" class="form-input" rows="2"
                            placeholder="Détail des achats, références factures…">{{ old('notes', $declaration->notes) }}</textarea>
                    </div>
                </div>

                <div style="text-align:right;margin-top:4px">
                    <button type="submit" class="btn btn-dark">
                        <svg style="width:13px;height:13px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                        Enregistrer la TVA déductible
                    </button>
                </div>
            </form>
            @else
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px">
                @foreach([
                    ['Achats / fournitures', $declaration->tva_achats_fournitures],
                    ['Loyer bureau agence', $declaration->tva_loyer_bureau],
                    ['Autres déductibles', $declaration->tva_autres_deductible],
                ] as [$lbl, $val])
                <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;padding:12px">
                    <div style="font-size:10px;text-transform:uppercase;letter-spacing:.5px;color:#9ca3af;margin-bottom:4px">{{ $lbl }}</div>
                    <div style="font-size:16px;font-weight:700;color:#16a34a">{{ number_format($val, 0, ',', ' ') }} F</div>
                </div>
                @endforeach
            </div>
            @if($declaration->notes)
            <div style="margin-top:12px;padding:10px 14px;background:#f9fafb;border-radius:8px;font-size:12px;color:#6b7280">
                <strong>Notes :</strong> {{ $declaration->notes }}
            </div>
            @endif
            @endif
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════
         SECTION 3 — RÉSULTAT
    ══════════════════════════════════════════════════════════════════════ --}}
    <div class="section-card">
        <div class="card-header">
            <div>
                <div class="card-title">Résultat — TVA nette à reverser</div>
                <div class="card-sub">Art. 370 CGI SN · Échéance : {{ $declaration->date_echeance->translatedFormat('d F Y') }}</div>
            </div>
        </div>
        <div class="card-body" style="max-width:480px">
            <div class="result-row">
                <span class="lbl">TVA collectée</span>
                <span class="val">{{ number_format($declaration->total_tva_collectee, 0, ',', ' ') }} FCFA</span>
            </div>
            <div class="result-row">
                <span class="lbl" style="color:#16a34a">− TVA déductible</span>
                <span class="val" style="color:#16a34a">− {{ number_format($declaration->total_tva_deductible, 0, ',', ' ') }} FCFA</span>
            </div>
            @if((float)$declaration->credit_reporte_entrant > 0)
            <div class="result-row">
                <span class="lbl" style="color:#f97316">− Crédit reporté mois précédent</span>
                <span class="val" style="color:#f97316">− {{ number_format($declaration->credit_reporte_entrant, 0, ',', ' ') }} FCFA</span>
            </div>
            @endif
            <div class="result-row total">
                @if((float)$declaration->tva_nette_due > 0)
                    <span class="lbl" style="color:#dc2626">TVA nette DUE</span>
                    <span class="val" style="color:#dc2626;font-size:18px">{{ number_format($declaration->tva_nette_due, 0, ',', ' ') }} FCFA</span>
                @else
                    <span class="lbl" style="color:#16a34a">Crédit de TVA</span>
                    <span class="val" style="color:#16a34a;font-size:18px">{{ number_format($declaration->credit_reporte_sortant, 0, ',', ' ') }} FCFA</span>
                @endif
            </div>
        </div>

        @if((float)$declaration->tva_nette_due > 0)
        <div class="card-body" style="padding-top:0">
            <div class="alerte-due">
                ⚠ <strong>{{ number_format($declaration->tva_nette_due, 0, ',', ' ') }} FCFA à verser à la DGI avant le
                {{ $declaration->date_echeance->translatedFormat('d F Y') }}</strong>
                — Art. 370 CGI SN. Tout versement tardif entraîne des pénalités et intérêts de retard.
                <strong>Consultez votre Centre des Services Fiscaux (CSF).</strong>
            </div>
        </div>
        @elseif((float)$declaration->credit_reporte_sortant > 0)
        <div class="card-body" style="padding-top:0">
            <div class="alerte-credit">
                ℹ <strong>Crédit de TVA de {{ number_format($declaration->credit_reporte_sortant, 0, ',', ' ') }} FCFA</strong>
                reporté automatiquement sur la déclaration de
                {{ \Carbon\Carbon::create($annee, $mois, 1)->addMonth()->locale('fr')->translatedFormat('F Y') }}.
                Ce crédit sera imputé sur la TVA due du mois suivant.
            </div>
        </div>
        @endif
    </div>

</div>

<script>
function recalculer(btn) {
    btn.disabled = true;
    const txt = btn.textContent;
    btn.textContent = '…';
    fetch(`/admin/tva-agence/{{ $annee }}/{{ $mois }}/recalculer`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
            'Accept': 'application/json',
        },
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) location.reload();
        else { alert(data.message); btn.disabled = false; btn.textContent = txt; }
    })
    .catch(() => { alert('Erreur réseau'); btn.disabled = false; btn.textContent = txt; });
}
</script>

</x-app-layout>
