<x-app-layout>
    <x-slot name="header">Modifier le contrat</x-slot>

<style>
.form-grid { display:grid; grid-template-columns:1fr 280px; gap:24px; align-items:start; }
.card { background:#fff; border:1px solid #e5e7eb; border-radius:14px; overflow:hidden; margin-bottom:18px; }
.card:last-child { margin-bottom:0; }
.card-hd { padding:15px 20px; border-bottom:1px solid #e5e7eb; display:flex; align-items:center; gap:10px; }
.card-icon { width:30px;height:30px;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0; }
.card-icon svg { width:15px;height:15px; }
.card-icon.gold   { background:#f5e9c9; } .card-icon.gold svg   { color:#8a6e2f; }
.card-icon.blue   { background:#dbeafe; } .card-icon.blue svg   { color:#1d4ed8; }
.card-icon.green  { background:#dcfce7; } .card-icon.green svg  { color:#16a34a; }
.card-icon.purple { background:#ede9fe; } .card-icon.purple svg { color:#7c3aed; }
.card-icon.gray   { background:#f3f4f6; } .card-icon.gray svg   { color:#6b7280; }
.card-title { font-family:'Syne',sans-serif; font-size:13px; font-weight:700; color:#0d1117; }
.card-body { padding:18px 20px; }
.form-group { margin-bottom:14px; }
.form-group:last-child { margin-bottom:0; }
.form-label { display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:5px; }
.req { color:#dc2626;margin-left:2px; }
.opt { font-size:11px;font-weight:400;color:#9ca3af;margin-left:4px; }
.form-input, .form-select, .form-textarea {
    width:100%;padding:9px 12px;border:1px solid #e5e7eb;border-radius:8px;
    font-size:13px;color:#0d1117;font-family:'DM Sans',sans-serif;
    background:#fff;outline:none;transition:border-color .15s,box-shadow .15s;
}
.form-input:focus,.form-select:focus,.form-textarea:focus { border-color:#c9a84c;box-shadow:0 0 0 3px rgba(201,168,76,.10); }
.form-input.error { border-color:#dc2626; }
.form-textarea { resize:vertical;min-height:90px; }
.form-select { cursor:pointer; }
.form-row  { display:grid;grid-template-columns:1fr 1fr;gap:12px; }
.form-row3 { display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px; }
.form-error { font-size:12px;color:#dc2626;margin-top:4px; }
.form-hint  { font-size:11px;color:#9ca3af;margin-top:4px; }
.form-hint.warn { color:#d97706; }

/* type bail pills */
.bail-grid { display:grid;grid-template-columns:repeat(4,1fr);gap:8px; }
.bail-pill { position:relative; }
.bail-pill input { position:absolute;opacity:0;width:0;height:0; }
.bail-pill label { display:flex;flex-direction:column;align-items:center;padding:10px 8px;border:1.5px solid #e5e7eb;border-radius:9px;cursor:pointer;font-size:11px;font-weight:500;color:#6b7280;text-align:center;gap:5px;background:#fff;transition:all .15s; }
.bail-pill label svg { width:14px;height:14px; }
.bail-pill input:checked + label { border-color:#c9a84c;background:#f5e9c9;color:#8a6e2f; }
.bail-pill label:hover { border-color:#c9a84c;background:#fdf8ef; }

/* bien/locataire readonly */
.readonly-card { background:#f9fafb;border-radius:9px;padding:12px 14px;border:1px solid #e5e7eb;display:flex;align-items:center;gap:12px; }
.readonly-icon { width:34px;height:34px;border-radius:8px;background:#f5e9c9;display:flex;align-items:center;justify-content:center;flex-shrink:0; }
.readonly-icon svg { width:15px;height:15px;color:#8a6e2f; }
.readonly-name { font-size:13px;font-weight:600;color:#0d1117; }
.readonly-sub  { font-size:11px;color:#6b7280;margin-top:2px; }
.readonly-link { font-size:11px;color:#c9a84c;text-decoration:none;margin-left:auto; }

/* submit */
.submit-bar { display:flex;align-items:center;gap:10px;padding:14px 18px;border-top:1px solid #e5e7eb;background:#f9fafb; }
.btn-submit { flex:1;display:flex;align-items:center;justify-content:center;gap:7px;padding:11px 20px;background:#0d1117;color:#fff;border:none;border-radius:9px;font-size:14px;font-weight:600;font-family:'DM Sans',sans-serif;cursor:pointer;transition:opacity .15s; }
.btn-submit:hover { opacity:.88; }
.btn-submit svg { width:15px;height:15px; }
.btn-cancel { padding:11px 18px;background:#fff;color:#6b7280;border:1px solid #e5e7eb;border-radius:9px;font-size:13px;font-weight:500;font-family:'DM Sans',sans-serif;cursor:pointer;text-decoration:none; }

/* récap sticky */
.recap-card { background:#0d1117;border-radius:14px;overflow:hidden;position:sticky;top:80px; }
.recap-hd { padding:14px 18px;border-bottom:1px solid rgba(255,255,255,.07); }
.recap-title { font-family:'Syne',sans-serif;font-size:13px;font-weight:700;color:#fff; }
.recap-body { padding:14px 18px; }
.rp-row { display:flex;justify-content:space-between;align-items:center;padding:7px 0;border-bottom:1px solid rgba(255,255,255,.05); }
.rp-row:last-child { border-bottom:none; }
.rp-lbl { font-size:12px;color:rgba(255,255,255,.5); }
.rp-val { font-family:'Syne',sans-serif;font-size:12px;font-weight:600;color:#fff; }
.rp-val.green { color:#4ade80; }
.rp-val.gold  { color:#c9a84c; }
.rp-val.muted { color:rgba(255,255,255,.3);font-weight:400;font-size:11px; }
.rp-block { background:rgba(201,168,76,.08);border:1px solid rgba(201,168,76,.15);border-radius:9px;padding:12px 14px;margin-top:12px; }
.rp-block-lbl { font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:.8px;color:rgba(201,168,76,.6);margin-bottom:4px; }
.rp-block-val { font-family:'Syne',sans-serif;font-size:18px;font-weight:700;color:#c9a84c;letter-spacing:-.3px; }
.rp-sep { height:1px;background:rgba(255,255,255,.08);margin:8px 0; }

/* ref badge */
.ref-badge { display:inline-flex;align-items:center;gap:6px;padding:5px 12px;background:#f5e9c9;border:1px solid #e9d5a0;border-radius:8px;font-family:'Syne',sans-serif;font-size:11px;font-weight:600;color:#8a6e2f; }
</style>

<div style="padding:24px 32px 48px">

    <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:#6b7280;margin-bottom:16px">
        <a href="{{ route('admin.contrats.index') }}" style="color:#6b7280;text-decoration:none">Contrats</a>
        <svg style="width:12px;height:12px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
        <a href="{{ route('admin.contrats.show', $contrat) }}" style="color:#6b7280;text-decoration:none">{{ $contrat->reference_bail ?? '#'.$contrat->id }}</a>
        <svg style="width:12px;height:12px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
        <span style="color:#0d1117;font-weight:500">Modifier</span>
    </div>

    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:22px">
        <div>
            <h1 style="font-family:'Syne',sans-serif;font-size:22px;font-weight:700;color:#0d1117;letter-spacing:-.4px">Modifier le contrat</h1>
            <p style="font-size:13px;color:#6b7280;margin-top:3px">Bien et locataire non modifiables après création.</p>
        </div>
        @if($contrat->reference_bail)
        <div class="ref-badge">
            <svg style="width:12px;height:12px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/></svg>
            {{ $contrat->reference_bail }}
        </div>
        @endif
    </div>

    <form method="POST" action="{{ route('admin.contrats.update', $contrat) }}" id="form-contrat">
        @csrf
        @method('PUT')

        <div class="form-grid">
            <div>

                {{-- BIEN & LOCATAIRE (lecture seule) --}}
                <div class="card">
                    <div class="card-hd">
                        <div class="card-icon gold"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg></div>
                        <div class="card-title">Bien & locataire</div>
                    </div>
                    <div class="card-body" style="display:flex;flex-direction:column;gap:10px">
                        <div class="readonly-card">
                            <div class="readonly-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg></div>
                            <div>
                                <div class="readonly-name">{{ $contrat->bien->reference }}</div>
                                <div class="readonly-sub">{{ $contrat->bien->adresse }}, {{ $contrat->bien->ville }}</div>
                            </div>
                            <a href="{{ route('biens.show', $contrat->bien) }}" class="readonly-link">Voir →</a>
                        </div>
                        <div class="readonly-card">
                            <div class="readonly-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></div>
                            <div>
                                <div class="readonly-name">{{ $contrat->locataire->name }}</div>
                                <div class="readonly-sub">{{ $contrat->locataire->email }}</div>
                            </div>
                            <a href="{{ route('admin.users.show', $contrat->locataire) }}" class="readonly-link">Voir →</a>
                        </div>
                        <div class="form-hint">Le bien et le locataire ne peuvent pas être modifiés. Pour changer, résiliez et recréez.</div>
                    </div>
                </div>

                {{-- TYPE BAIL & DATES --}}
                <div class="card">
                    <div class="card-hd">
                        <div class="card-icon blue"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></div>
                        <div class="card-title">Type de bail & durée</div>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-label">Type de bail <span class="req">*</span></label>
                            <div class="bail-grid">
                                @foreach(\App\Models\Contrat::TYPES_BAIL as $key => $label)
                                <div class="bail-pill">
                                    <input type="radio" name="type_bail" id="bail_{{ $key }}" value="{{ $key }}"
                                        {{ old('type_bail', $contrat->type_bail) === $key ? 'checked':'' }}>
                                    <label for="bail_{{ $key }}">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            @if($key === 'habitation') <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                                            @elseif($key === 'commercial') <rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v16"/>
                                            @elseif($key === 'mixte') <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><rect x="9" y="12" width="6" height="9"/>
                                            @else <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                                            @endif
                                        </svg>
                                        {{ $label }}
                                    </label>
                                </div>
                                @endforeach
                            </div>
                            @error('type_bail')<div class="form-error" style="margin-top:8px">{{ $message }}</div>@enderror
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Date de début <span class="req">*</span></label>
                                <input type="date" name="date_debut" class="form-input {{ $errors->has('date_debut') ? 'error':'' }}"
                                    value="{{ old('date_debut', $contrat->date_debut?->format('Y-m-d')) }}"
                                    onchange="mettreAJourRecap()">
                                @error('date_debut')<div class="form-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="form-group">
                                <label class="form-label">Date de fin <span class="opt">(vide = indéterminée)</span></label>
                                <input type="date" name="date_fin" class="form-input"
                                    value="{{ old('date_fin', $contrat->date_fin?->format('Y-m-d')) }}"
                                    onchange="mettreAJourRecap()">
                                @error('date_fin')<div class="form-error">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Référence bail <span class="opt">(optionnel)</span></label>
                            <input type="text" name="reference_bail" class="form-input"
                                value="{{ old('reference_bail', $contrat->reference_bail) }}"
                                placeholder="Ex: BAIL-DKR-2024">
                        </div>
                    </div>
                </div>

                {{-- FINANCES --}}
                <div class="card">
                    <div class="card-hd">
                        <div class="card-icon green"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg></div>
                        <div class="card-title">Conditions financières</div>
                    </div>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Loyer nu <span class="req">*</span></label>
                                <input type="number" name="loyer_nu" id="loyer_nu"
                                    class="form-input {{ $errors->has('loyer_nu') ? 'error':'' }}"
                                    value="{{ old('loyer_nu', $contrat->loyer_nu ?? $contrat->loyer_contractuel) }}"
                                    min="1" step="500" oninput="mettreAJourRecap()">
                                @error('loyer_nu')<div class="form-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="form-group">
                                <label class="form-label">Caution <span class="req">*</span></label>
                                <input type="number" name="caution" id="caution"
                                    class="form-input {{ $errors->has('caution') ? 'error':'' }}"
                                    value="{{ old('caution', $contrat->caution) }}"
                                    min="0" step="500" oninput="mettreAJourRecap()">
                                @error('caution')<div class="form-error">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Charges mensuelles <span class="opt">(optionnel)</span></label>
                                <input type="number" name="charges_mensuelles" id="charges_mensuelles"
                                    class="form-input"
                                    value="{{ old('charges_mensuelles', $contrat->charges_mensuelles ?? 0) }}"
                                    min="0" step="500" oninput="mettreAJourRecap()">
                            </div>
                            <div class="form-group">
                                <label class="form-label">TOM <span class="opt">(Taxe ordures ménagères)</span></label>
                                <input type="number" name="tom_amount" id="tom_amount"
                                    class="form-input"
                                    value="{{ old('tom_amount', $contrat->tom_amount ?? 0) }}"
                                    min="0" step="100" oninput="mettreAJourRecap()">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Frais d'agence <span class="opt">(optionnel)</span></label>
                                <input type="number" name="frais_agence" class="form-input"
                                    value="{{ old('frais_agence', $contrat->frais_agence ?? 0) }}"
                                    min="0" step="500">
                                <div class="form-hint">Perçus à la signature</div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Nombre de mois caution</label>
                                <input type="number" name="nombre_mois_caution" class="form-input"
                                    value="{{ old('nombre_mois_caution', $contrat->nombre_mois_caution ?? 1) }}"
                                    min="1" max="6">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Indexation annuelle <span class="opt">(%)</span></label>
                            <input type="number" name="indexation_annuelle" class="form-input"
                                value="{{ old('indexation_annuelle', $contrat->indexation_annuelle ?? 0) }}"
                                min="0" max="20" step="0.5">
                            <div class="form-hint">Taux de révision annuelle du loyer (0 = aucune)</div>
                        </div>
                    </div>
                </div>
{{-- Section : Paramètres fiscaux --}}
@include('admin.contrats._section-fiscal', ['contrat' => $contrat])
                {{-- GARANT --}}
                <div class="card">
                    <div class="card-hd">
                        <div class="card-icon gray"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg></div>
                        <div class="card-title">Garant <span class="opt">(optionnel)</span></div>
                    </div>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Nom du garant</label>
                                <input type="text" name="garant_nom" class="form-input"
                                    value="{{ old('garant_nom', $contrat->garant_nom) }}"
                                    placeholder="Prénom NOM">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Téléphone</label>
                                <input type="text" name="garant_telephone" class="form-input"
                                    value="{{ old('garant_telephone', $contrat->garant_telephone) }}"
                                    placeholder="+221 7X XXX XX XX">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Adresse</label>
                            <input type="text" name="garant_adresse" class="form-input"
                                value="{{ old('garant_adresse', $contrat->garant_adresse) }}"
                                placeholder="Adresse complète">
                        </div>
                    </div>
                </div>

                {{-- OBSERVATIONS --}}
                <div class="card">
                    <div class="card-hd">
                        <div class="card-icon purple"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg></div>
                        <div class="card-title">Observations <span class="opt">(optionnel)</span></div>
                    </div>
                    <div class="card-body">
                        <textarea name="observations" class="form-textarea"
                            placeholder="Conditions particulières, préavis, état des lieux…">{{ old('observations', $contrat->observations) }}</textarea>
                    </div>
                    <div class="submit-bar">
                        <a href="{{ route('admin.contrats.show', $contrat) }}" class="btn-cancel">Annuler</a>
                        <button type="submit" class="btn-submit">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                            Enregistrer les modifications
                        </button>
                    </div>
                </div>

            </div>{{-- fin colonne gauche --}}

            {{-- RÉCAPITULATIF --}}
            <div>
                <div class="recap-card">
                    <div class="recap-hd"><div class="recap-title">Récapitulatif</div></div>
                    <div class="recap-body">
                        <div class="rp-row">
                            <div class="rp-lbl">Loyer nu</div>
                            <div class="rp-val" id="rp-nu">{{ number_format($contrat->loyer_nu ?? $contrat->loyer_contractuel, 0, ',', ' ') }} F</div>
                        </div>
                        <div class="rp-row" id="rp-charges-row" style="{{ ($contrat->charges_mensuelles ?? 0) > 0 ? '':'display:none' }}">
                            <div class="rp-lbl">+ Charges</div>
                            <div class="rp-val" id="rp-charges">{{ number_format($contrat->charges_mensuelles ?? 0, 0, ',', ' ') }} F</div>
                        </div>
                        <div class="rp-row" id="rp-tom-row" style="{{ ($contrat->tom_amount ?? 0) > 0 ? '':'display:none' }}">
                            <div class="rp-lbl">+ TOM</div>
                            <div class="rp-val" id="rp-tom">{{ number_format($contrat->tom_amount ?? 0, 0, ',', ' ') }} F</div>
                        </div>
                        <div class="rp-block">
                            <div class="rp-block-lbl">Loyer contractuel / mois</div>
                            <div class="rp-block-val"><span id="rp-contractuel">{{ number_format($contrat->loyer_contractuel, 0, ',', ' ') }}</span> F</div>
                        </div>
                        <div class="rp-sep"></div>
                        <div class="rp-row">
                            <div class="rp-lbl">Caution</div>
                            <div class="rp-val green" id="rp-caution">{{ number_format($contrat->caution, 0, ',', ' ') }} F</div>
                        </div>
                        <div class="rp-row">
                            <div class="rp-lbl">Début</div>
                            <div class="rp-val" id="rp-debut">{{ \Carbon\Carbon::parse($contrat->date_debut)->format('d/m/Y') }}</div>
                        </div>
                        <div class="rp-row">
                            <div class="rp-lbl">Fin</div>
                            <div class="rp-val muted" id="rp-fin">{{ $contrat->date_fin ? \Carbon\Carbon::parse($contrat->date_fin)->format('d/m/Y') : 'Indéterminée' }}</div>
                        </div>
                        <div class="rp-sep"></div>
                        <div class="rp-row">
                            <div class="rp-lbl">Bien</div>
                            <div class="rp-val muted">{{ $contrat->bien->reference }}</div>
                        </div>
                        <div class="rp-row">
                            <div class="rp-lbl">Locataire</div>
                            <div class="rp-val muted" style="max-width:140px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $contrat->locataire->name }}</div>
                        </div>
                    </div>
                </div>
            </div>

        </div>{{-- /form-grid --}}
    </form>

</div>

<script>
function mettreAJourRecap() {
    const nu      = parseFloat(document.getElementById('loyer_nu').value)          || 0;
    const charges = parseFloat(document.getElementById('charges_mensuelles').value) || 0;
    const tom     = parseFloat(document.getElementById('tom_amount').value)          || 0;
    const caution = parseFloat(document.getElementById('caution').value)             || 0;
    const contractuel = nu + charges + tom;
    const fmt = n => Math.round(n).toLocaleString('fr-FR');

    document.getElementById('rp-nu').textContent          = fmt(nu) + ' F';
    document.getElementById('rp-contractuel').textContent = fmt(contractuel);
    document.getElementById('rp-caution').textContent     = fmt(caution) + ' F';

    document.getElementById('rp-charges-row').style.display = charges > 0 ? '' : 'none';
    document.getElementById('rp-tom-row').style.display     = tom > 0     ? '' : 'none';
    document.getElementById('rp-charges').textContent = fmt(charges) + ' F';
    document.getElementById('rp-tom').textContent     = fmt(tom) + ' F';

    const debut = document.querySelector('input[name="date_debut"]').value;
    const fin   = document.querySelector('input[name="date_fin"]').value;
    if (debut) document.getElementById('rp-debut').textContent = new Date(debut).toLocaleDateString('fr-FR');
    document.getElementById('rp-fin').textContent = fin ? new Date(fin).toLocaleDateString('fr-FR') : 'Indéterminée';
}

mettreAJourRecap();
</script>

</x-app-layout>