<x-app-layout>
    <x-slot name="header">Modifier un bien</x-slot>

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
.card-icon.gray   { background:#f3f4f6; } .card-icon.gray svg   { color:#6b7280; }
.card-icon.purple { background:#ede9fe; } .card-icon.purple svg { color:#7c3aed; }
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
.form-input:focus, .form-select:focus, .form-textarea:focus {
    border-color:#c9a84c;box-shadow:0 0 0 3px rgba(201,168,76,.10);
}
.form-input.error, .form-select.error { border-color:#dc2626; }
.form-textarea { resize:vertical;min-height:90px; }
.form-select { cursor:pointer; }
.form-row  { display:grid;grid-template-columns:1fr 1fr;gap:12px; }
.form-error { font-size:12px;color:#dc2626;margin-top:4px; }
.form-hint  { font-size:11px;color:#9ca3af;margin-top:4px; }
.toggle-row { display:flex;align-items:center;gap:10px;padding:10px 12px;background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;cursor:pointer;transition:border-color .15s; }
.toggle-row:hover { border-color:#c9a84c; }
.toggle-row input[type=checkbox] { width:16px;height:16px;accent-color:#c9a84c;cursor:pointer;flex-shrink:0; }
.toggle-lbl { font-size:13px;font-weight:500;color:#374151; }
.toggle-sub { font-size:11px;color:#6b7280;margin-top:1px; }
.type-grid { display:grid;grid-template-columns:repeat(3,1fr);gap:8px; }
.type-pill { position:relative; }
.type-pill input { position:absolute;opacity:0;width:0;height:0; }
.type-pill label { display:flex;flex-direction:column;align-items:center;padding:10px 8px;border:1.5px solid #e5e7eb;border-radius:9px;cursor:pointer;font-size:11px;font-weight:500;color:#6b7280;text-align:center;gap:5px;background:#fff;transition:all .15s; }
.type-pill label svg { width:18px;height:18px; }
.type-pill input:checked + label { border-color:#c9a84c;background:#f5e9c9;color:#8a6e2f; }
.type-pill label:hover { border-color:#c9a84c;background:#fdf8ef; }
.submit-bar { display:flex;align-items:center;gap:10px;padding:14px 18px;border-top:1px solid #e5e7eb;background:#f9fafb; }
.btn-submit { flex:1;display:flex;align-items:center;justify-content:center;gap:7px;padding:11px 20px;background:#0d1117;color:#fff;border:none;border-radius:9px;font-size:14px;font-weight:600;font-family:'DM Sans',sans-serif;cursor:pointer;transition:opacity .15s; }
.btn-submit:hover { opacity:.88; }
.btn-submit svg { width:15px;height:15px; }
.btn-cancel { padding:11px 18px;background:#fff;color:#6b7280;border:1px solid #e5e7eb;border-radius:9px;font-size:13px;font-weight:500;font-family:'DM Sans',sans-serif;cursor:pointer;text-decoration:none; }
.recap-card { background:#0d1117;border-radius:14px;overflow:hidden;position:sticky;top:80px; }
.recap-hd { padding:14px 18px;border-bottom:1px solid rgba(255,255,255,.07); }
.recap-title { font-family:'Syne',sans-serif;font-size:13px;font-weight:700;color:#fff; }
.recap-body { padding:14px 18px; }
.rp-row { display:flex;justify-content:space-between;align-items:center;padding:7px 0;border-bottom:1px solid rgba(255,255,255,.05); }
.rp-row:last-child { border-bottom:none; }
.rp-lbl { font-size:12px;color:rgba(255,255,255,.5); }
.rp-val { font-family:'Syne',sans-serif;font-size:12px;font-weight:600;color:#fff; }
.rp-val.gold { color:#c9a84c; }
.rp-val.muted { color:rgba(255,255,255,.3);font-weight:400; }
.rp-block { background:rgba(201,168,76,.08);border:1px solid rgba(201,168,76,.15);border-radius:9px;padding:12px 14px;margin-top:12px; }
.rp-block-lbl { font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:.8px;color:rgba(201,168,76,.6);margin-bottom:4px; }
.rp-block-val { font-family:'Syne',sans-serif;font-size:18px;font-weight:700;color:#c9a84c;letter-spacing:-.3px; }
.rp-sep { height:1px;background:rgba(255,255,255,.08);margin:8px 0; }
/* ref bien badge */
.ref-badge { display:inline-flex;align-items:center;gap:6px;padding:6px 12px;background:#f5e9c9;border:1px solid #e9d5a0;border-radius:8px;font-family:'Syne',sans-serif;font-size:12px;font-weight:600;color:#8a6e2f; }
</style>

<div style="padding:24px 32px 48px">

    <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:#6b7280;margin-bottom:16px">
        <a href="{{ route('biens.index') }}" style="color:#6b7280;text-decoration:none">Biens</a>
        <svg style="width:12px;height:12px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
        <a href="{{ route('biens.show', $bien) }}" style="color:#6b7280;text-decoration:none">{{ $bien->reference }}</a>
        <svg style="width:12px;height:12px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
        <span style="color:#0d1117;font-weight:500">Modifier</span>
    </div>

    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:22px">
        <div>
            <h1 style="font-family:'Syne',sans-serif;font-size:22px;font-weight:700;color:#0d1117;letter-spacing:-.4px">Modifier le bien</h1>
            <p style="font-size:13px;color:#6b7280;margin-top:3px">Les modifications sont appliquées immédiatement.</p>
        </div>
        <div class="ref-badge">
            <svg style="width:13px;height:13px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/></svg>
            {{ $bien->reference }}
        </div>
    </div>

    <form method="POST" action="{{ route('biens.update', $bien) }}" id="form-bien">
        @csrf
        @method('PATCH')

        <div class="form-grid">
            <div>

                {{-- PROPRIÉTAIRE --}}
                @if(auth()->user()->isAdmin())
                <div class="card">
                    <div class="card-hd">
                        <div class="card-icon gold"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></div>
                        <div class="card-title">Propriétaire</div>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-label" for="proprietaire_id">Propriétaire <span class="req">*</span></label>
                            <select name="proprietaire_id" id="proprietaire_id" class="form-select {{ $errors->has('proprietaire_id') ? 'error':'' }}">
                                <option value="">— Sélectionner —</option>
                                @foreach($proprietaires as $p)
                                    <option value="{{ $p->id }}"
                                        {{ old('proprietaire_id', $bien->proprietaire_id) == $p->id ? 'selected':'' }}>
                                        {{ $p->name }} — {{ $p->email }}
                                    </option>
                                @endforeach
                            </select>
                            @error('proprietaire_id')<div class="form-error">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
                @endif

                {{-- TYPE --}}
                <div class="card">
                    <div class="card-hd">
                        <div class="card-icon blue"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg></div>
                        <div class="card-title">Type de bien</div>
                    </div>
                    <div class="card-body">
                        <div class="type-grid">
                            @foreach(\App\Models\Bien::TYPES as $key => $label)
                            <div class="type-pill">
                                <input type="radio" name="type" id="type_{{ $key }}" value="{{ $key }}"
                                    {{ old('type', $bien->type) === $key ? 'checked':'' }}>
                                <label for="type_{{ $key }}">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        @if(in_array($key, ['appartement','studio']))
                                            <rect x="4" y="2" width="16" height="20" rx="2"/><line x1="8" y1="6" x2="16" y2="6"/>
                                        @elseif($key === 'villa')
                                            <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                                        @elseif(in_array($key, ['bureau','local_commercial']))
                                            <rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v16"/>
                                        @elseif($key === 'maison')
                                            <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>
                                        @else
                                            <circle cx="12" cy="12" r="10"/>
                                        @endif
                                    </svg>
                                    {{ $label }}
                                </label>
                            </div>
                            @endforeach
                        </div>
                        @error('type')<div class="form-error" style="margin-top:8px">{{ $message }}</div>@enderror
                    </div>
                </div>

                {{-- LOCALISATION --}}
                <div class="card">
                    <div class="card-hd">
                        <div class="card-icon blue"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg></div>
                        <div class="card-title">Localisation</div>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-label">Adresse <span class="req">*</span></label>
                            <input type="text" name="adresse" class="form-input {{ $errors->has('adresse') ? 'error':'' }}"
                                value="{{ old('adresse', $bien->adresse) }}" placeholder="N° rue, nom de la rue">
                            @error('adresse')<div class="form-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Ville <span class="req">*</span></label>
                                <input type="text" name="ville" class="form-input {{ $errors->has('ville') ? 'error':'' }}"
                                    value="{{ old('ville', $bien->ville) }}">
                                @error('ville')<div class="form-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="form-group">
                                <label class="form-label">Quartier <span class="opt">(optionnel)</span></label>
                                <input type="text" name="quartier" class="form-input"
                                    value="{{ old('quartier', $bien->quartier) }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Commune <span class="opt">(optionnel)</span></label>
                            <input type="text" name="commune" class="form-input"
                                value="{{ old('commune', $bien->commune) }}">
                        </div>
                    </div>
                </div>

                {{-- CARACTÉRISTIQUES --}}
                <div class="card">
                    <div class="card-hd">
                        <div class="card-icon gray"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg></div>
                        <div class="card-title">Caractéristiques</div>
                    </div>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Surface <span class="opt">(m²)</span></label>
                                <input type="number" name="surface_m2" class="form-input"
                                    value="{{ old('surface_m2', $bien->surface_m2) }}" min="1">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Nombre de pièces</label>
                                <input type="number" name="nombre_pieces" class="form-input"
                                    value="{{ old('nombre_pieces', $bien->nombre_pieces) }}" min="1">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="toggle-row" for="meuble">
                                <input type="checkbox" name="meuble" id="meuble" value="1"
                                    {{ old('meuble', $bien->meuble) ? 'checked':'' }}>
                                <div>
                                    <div class="toggle-lbl">Bien meublé</div>
                                    <div class="toggle-sub">Cochez si le bien est loué avec mobilier</div>
                                </div>
                            </label>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Statut <span class="req">*</span></label>
                            <select name="statut" id="statut" class="form-select {{ $errors->has('statut') ? 'error':'' }}">
                                <option value="disponible" {{ old('statut',$bien->statut)==='disponible' ? 'selected':'' }}>Disponible</option>
                                <option value="loue"       {{ old('statut',$bien->statut)==='loue'       ? 'selected':'' }}>Loué</option>
                                <option value="en_travaux" {{ old('statut',$bien->statut)==='en_travaux' ? 'selected':'' }}>En travaux</option>
                            </select>
                            @if($bien->contratActif && old('statut', $bien->statut) !== 'loue')
                            <div class="form-hint" style="color:#d97706">
                                ⚠ Ce bien a un contrat actif — le statut "Loué" est recommandé.
                            </div>
                            @endif
                            @error('statut')<div class="form-error">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                {{-- FINANCES --}}
                <div class="card">
                    <div class="card-hd">
                        <div class="card-icon green"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg></div>
                        <div class="card-title">Finances</div>
                    </div>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Loyer mensuel <span class="req">*</span></label>
                                <input type="number" name="loyer_mensuel" id="loyer_mensuel"
                                    class="form-input {{ $errors->has('loyer_mensuel') ? 'error':'' }}"
                                    value="{{ old('loyer_mensuel', $bien->loyer_mensuel) }}" min="1" step="500"
                                    oninput="calcRecap()">
                                @error('loyer_mensuel')<div class="form-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="form-group">
                                <label class="form-label">Commission agence <span class="req">*</span> <span class="opt">(%)</span></label>
                                <input type="number" name="taux_commission" id="taux_commission"
                                    class="form-input {{ $errors->has('taux_commission') ? 'error':'' }}"
                                    value="{{ old('taux_commission', $bien->taux_commission) }}" min="1" max="20" step="0.5"
                                    oninput="calcRecap()">
                                @error('taux_commission')<div class="form-error">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- DESCRIPTION --}}
                <div class="card">
                    <div class="card-hd">
                        <div class="card-icon purple"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><line x1="16" y1="13" x2="8" y2="13"/></svg></div>
                        <div class="card-title">Description <span class="opt">(optionnel)</span></div>
                    </div>
                    <div class="card-body">
                        <textarea name="description" class="form-textarea"
                            placeholder="Description du bien, équipements…">{{ old('description', $bien->description) }}</textarea>
                    </div>
                    <div class="submit-bar">
                        <a href="{{ route('biens.show', $bien) }}" class="btn-cancel">Annuler</a>
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
                            <div class="rp-lbl">Loyer mensuel</div>
                            <div class="rp-val" id="rp-loyer">{{ number_format($bien->loyer_mensuel, 0, ',', ' ') }} F</div>
                        </div>
                        <div class="rp-row">
                            <div class="rp-lbl">Commission HT</div>
                            <div class="rp-val gold" id="rp-comm">— F</div>
                        </div>
                        <div class="rp-row">
                            <div class="rp-lbl">TVA (18%)</div>
                            <div class="rp-val" style="color:#fbbf24" id="rp-tva">— F</div>
                        </div>
                        <div class="rp-row">
                            <div class="rp-lbl">Commission TTC</div>
                            <div class="rp-val gold" id="rp-ttc">— F</div>
                        </div>
                        <div class="rp-block">
                            <div class="rp-block-lbl">Net propriétaire / mois</div>
                            <div class="rp-block-val"><span id="rp-net">—</span> F</div>
                        </div>
                        <div class="rp-sep"></div>
                        <div class="rp-row">
                            <div class="rp-lbl">Référence</div>
                            <div class="rp-val" style="font-family:monospace;font-size:11px">{{ $bien->reference }}</div>
                        </div>
                        <div class="rp-row">
                            <div class="rp-lbl">Créé le</div>
                            <div class="rp-val muted">{{ $bien->created_at->format('d/m/Y') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

</div>

<script>
function calcRecap() {
    const loyer = parseFloat(document.getElementById('loyer_mensuel').value) || 0;
    const taux  = parseFloat(document.getElementById('taux_commission').value) || 0;
    const fmt   = n => Math.round(n).toLocaleString('fr-FR');
    const commHT  = loyer * taux / 100;
    const tva     = commHT * 0.18;
    const commTTC = commHT + tva;
    const net     = loyer - commTTC;
    document.getElementById('rp-loyer').textContent = fmt(loyer) + ' F';
    document.getElementById('rp-comm').textContent  = fmt(commHT) + ' F';
    document.getElementById('rp-tva').textContent   = fmt(tva) + ' F';
    document.getElementById('rp-ttc').textContent   = fmt(commTTC) + ' F';
    document.getElementById('rp-net').textContent   = fmt(net);
}
calcRecap();
</script>

</x-app-layout>