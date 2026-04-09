@extends('layouts.app')
@section('title', 'Nouveau bien')
@section('breadcrumb', 'Biens › Nouveau')

@section('content')
<style>
.form-grid { display:grid; grid-template-columns:1fr 340px; gap:24px; align-items:start; }
.card { background:#fff; border:1px solid #e5e7eb; border-radius:14px; overflow:hidden; margin-bottom:16px; }
.card-hd { padding:14px 20px; border-bottom:1px solid #e5e7eb; display:flex; align-items:center; gap:10px; }
.card-icon { width:30px;height:30px;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0; }
.card-icon svg { width:15px;height:15px; }
.card-icon.gold   { background:#f5e9c9;color:#8a6e2f; }
.card-icon.blue   { background:#dbeafe;color:#1d4ed8; }
.card-icon.green  { background:#dcfce7;color:#16a34a; }
.card-icon.purple { background:#ede9fe;color:#7c3aed; }
.card-title { font-family:'Syne',sans-serif;font-size:13px;font-weight:700;color:#0d1117; }
.card-body { padding:18px 20px; }
.form-row { display:grid;grid-template-columns:1fr 1fr;gap:14px; }
.form-group { margin-bottom:14px; }
.form-group:last-child { margin-bottom:0; }
.form-label { display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:5px; }
.req { color:#dc2626; }
.opt { color:#9ca3af;font-weight:400; }
.form-input,.form-select,.form-textarea {
    width:100%;padding:9px 12px;border:1px solid #e5e7eb;border-radius:8px;
    font-size:13px;font-family:'DM Sans',sans-serif;color:#0d1117;background:#fff;
    outline:none;transition:border .15s;
}
.form-input:focus,.form-select:focus,.form-textarea:focus { border-color:#c9a84c;box-shadow:0 0 0 3px rgba(201,168,76,.1); }
.form-input.error,.form-select.error { border-color:#dc2626; }
.form-error { font-size:11px;color:#dc2626;margin-top:3px; }
.form-textarea { resize:vertical;min-height:80px; }
.recap-card { background:#0d1117;border-radius:14px;overflow:hidden;position:sticky;top:24px; }
.recap-hd { padding:14px 18px;border-bottom:1px solid rgba(255,255,255,.07); }
.recap-title { font-family:'Syne',sans-serif;font-size:13px;font-weight:700;color:#fff; }
.recap-body { padding:16px 18px; }
.rp-row { display:flex;justify-content:space-between;align-items:center;padding:7px 0;border-bottom:1px solid rgba(255,255,255,.06); }
.rp-row:last-child { border-bottom:none; }
.rp-lbl { font-size:12px;color:rgba(255,255,255,.45); }
.rp-val { font-family:'Syne',sans-serif;font-size:12px;font-weight:600;color:#fff; }
.rp-val.gold { color:#c9a84c; }
.rp-total { background:rgba(201,168,76,.1);border:1px solid rgba(201,168,76,.2);border-radius:9px;padding:12px 14px;margin-top:12px; }
.rp-total-lbl { font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:rgba(201,168,76,.6);margin-bottom:4px; }
.rp-total-val { font-family:'Syne',sans-serif;font-size:20px;font-weight:700;color:#c9a84c; }
.submit-bar { display:flex;justify-content:flex-end;gap:10px;padding:14px 20px;border-top:1px solid #e5e7eb;background:#f9fafb; }
.btn-cancel { padding:8px 16px;border-radius:8px;border:1px solid #e5e7eb;background:#fff;color:#6b7280;font-size:13px;font-family:'DM Sans',sans-serif;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center; }
.btn-submit { padding:8px 18px;border-radius:8px;border:none;background:#2a4a7f;color:#fff;font-size:13px;font-weight:600;font-family:'DM Sans',sans-serif;cursor:pointer;display:inline-flex;align-items:center;gap:6px; }
</style>

<div style="padding:0 0 48px">

    {{-- Breadcrumb --}}
    <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:#6b7280;margin-bottom:18px">
        <a href="{{ route('admin.biens.index') }}" style="color:#6b7280;text-decoration:none">Biens</a>
        <svg style="width:12px;height:12px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
        <span style="color:#0d1117;font-weight:500">Nouveau bien</span>
    </div>

    <div style="margin-bottom:20px">
        <h1 style="font-family:'Syne',sans-serif;font-size:22px;font-weight:700;color:#0d1117;letter-spacing:-.4px">Ajouter un bien</h1>
        <p style="font-size:13px;color:#6b7280;margin-top:3px">La référence est générée automatiquement.</p>
    </div>

    @if($errors->any())
    <div style="background:#fef2f2;border:1px solid #fecaca;border-left:3px solid #dc2626;border-radius:8px;padding:12px 16px;margin-bottom:18px;font-size:13px;color:#dc2626">
        <strong>Veuillez corriger les erreurs :</strong>
        <ul style="margin-top:4px;padding-left:16px">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('admin.biens.store') }}" id="form-bien" enctype="multipart/form-data">
        @csrf
        <div class="form-grid">

            {{-- ═══ COLONNE GAUCHE ═══ --}}
            <div>

                {{-- PROPRIÉTAIRE --}}
                <div class="card">
                    <div class="card-hd">
                        <div class="card-icon gold">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        </div>
                        <div class="card-title">Propriétaire</div>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-label">Propriétaire <span class="req">*</span></label>
                            <select name="proprietaire_id" class="form-select {{ $errors->has('proprietaire_id') ? 'error':'' }}">
                                <option value="">— Sélectionner —</option>
                                @foreach($proprietaires as $p)
                                    <option value="{{ $p->id }}" {{ old('proprietaire_id') == $p->id ? 'selected':'' }}>
                                        {{ $p->name }} — {{ $p->email }}
                                    </option>
                                @endforeach
                            </select>
                            @error('proprietaire_id')<div class="form-error">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                {{-- INFORMATIONS GÉNÉRALES --}}
                <div class="card">
                    <div class="card-hd">
                        <div class="card-icon blue">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                        </div>
                        <div class="card-title">Informations générales</div>
                    </div>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Type <span class="req">*</span></label>
                                <select name="type" class="form-select {{ $errors->has('type') ? 'error':'' }}" onchange="calcRecap()">
                                    <option value="">— Choisir —</option>
                                    @foreach(\App\Models\Bien::TYPES as $val => $label)
                                        <option value="{{ $val }}" {{ old('type') === $val ? 'selected':'' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('type')<div class="form-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="form-group">
                                <label class="form-label">Surface (m²) <span class="opt">(optionnel)</span></label>
                                <input type="number" name="surface_m2" class="form-input"
                                       value="{{ old('surface_m2') }}" min="1" step="0.5">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Nombre de pièces <span class="opt">(optionnel)</span></label>
                                <input type="number" name="nombre_pieces" class="form-input"
                                       value="{{ old('nombre_pieces') }}" min="1">
                            </div>
                            <div class="form-group" style="display:flex;align-items:center;padding-top:22px">
                                <label class="form-label" style="margin-bottom:0;display:flex;align-items:center;gap:8px;cursor:pointer">
                                    <input type="checkbox" name="meuble" value="1"
                                           {{ old('meuble') ? 'checked':'' }}
                                           style="width:16px;height:16px;accent-color:#c9a84c;cursor:pointer">
                                    Bien meublé
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- LOCALISATION --}}
                <div class="card">
                    <div class="card-hd">
                        <div class="card-icon green">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        </div>
                        <div class="card-title">Localisation</div>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-label">Adresse <span class="req">*</span></label>
                            <input type="text" name="adresse" class="form-input {{ $errors->has('adresse') ? 'error':'' }}"
                                   value="{{ old('adresse') }}" placeholder="Rue, numéro">
                            @error('adresse')<div class="form-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Quartier <span class="opt">(optionnel)</span></label>
                                <input type="text" name="quartier" class="form-input"
                                       value="{{ old('quartier') }}" placeholder="Ex: Almadies">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Commune <span class="opt">(optionnel)</span></label>
                                <input type="text" name="commune" class="form-input"
                                       value="{{ old('commune') }}" placeholder="Ex: Dakar Plateau">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Ville <span class="req">*</span></label>
                            <input type="text" name="ville" class="form-input {{ $errors->has('ville') ? 'error':'' }}"
                                   value="{{ old('ville', 'Dakar') }}" placeholder="Ex: Dakar">
                            @error('ville')<div class="form-error">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                {{-- FINANCIER --}}
                <div class="card">
                    <div class="card-hd">
                        <div class="card-icon gold">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
                        </div>
                        <div class="card-title">Informations financières</div>
                    </div>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Loyer mensuel (FCFA) <span class="req">*</span></label>
                                <input type="number" name="loyer_mensuel" id="loyer_mensuel"
                                       class="form-input {{ $errors->has('loyer_mensuel') ? 'error':'' }}"
                                       value="{{ old('loyer_mensuel') }}" min="0" step="500"
                                       oninput="calcRecap()">
                                @error('loyer_mensuel')<div class="form-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="form-group">
                                <label class="form-label">Taux commission (%) <span class="req">*</span></label>
                                <input type="number" name="taux_commission" id="taux_commission"
                                       class="form-input {{ $errors->has('taux_commission') ? 'error':'' }}"
                                       value="{{ old('taux_commission', 10) }}" min="0" max="30" step="0.5"
                                       oninput="calcRecap()">
                                @error('taux_commission')<div class="form-error">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- PHOTOS --}}
                <div class="card">
                    <div class="card-hd">
                        <div class="card-icon" style="background:#f0fdf4;color:#16a34a">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:15px;height:15px">
                                <rect x="3" y="3" width="18" height="18" rx="2"/>
                                <circle cx="8.5" cy="8.5" r="1.5"/>
                                <polyline points="21 15 16 10 5 21"/>
                            </svg>
                        </div>
                        <div class="card-title">Photos <span class="opt">(optionnel)</span></div>
                    </div>
                    <div class="card-body">
                        <div id="drop-zone" style="
                            border:2px dashed #e5e7eb;border-radius:10px;padding:28px;
                            text-align:center;cursor:pointer;transition:all .15s;background:#fafafa"
                            onclick="document.getElementById('photos-input').click()"
                            ondragover="event.preventDefault();this.style.borderColor='#c9a84c';this.style.background='#fffbeb'"
                            ondragleave="this.style.borderColor='#e5e7eb';this.style.background='#fafafa'"
                            ondrop="handleDrop(event)">
                            <svg style="width:32px;height:32px;color:#d1d5db;margin:0 auto 10px;display:block"
                                 viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/>
                                <polyline points="17 8 12 3 7 8"/>
                                <line x1="12" y1="3" x2="12" y2="15"/>
                            </svg>
                            <p style="font-size:13px;color:#6b7280;margin-bottom:4px">Cliquez ou glissez vos photos ici</p>
                            <p style="font-size:11px;color:#9ca3af">JPG, PNG, WEBP — max 3 Mo par photo</p>
                        </div>

                        <input type="file" id="photos-input" name="photos[]"
                               multiple accept="image/jpeg,image/png,image/webp"
                               style="display:none" onchange="previewPhotos(this.files)">

                        <div id="preview-grid"
                             style="display:none;grid-template-columns:repeat(auto-fill,minmax(100px,1fr));gap:10px;margin-top:14px">
                        </div>

                        <p style="font-size:11px;color:#9ca3af;margin-top:10px">
                            La première photo sélectionnée sera la photo principale.
                        </p>
                    </div>
                </div>

                {{-- DESCRIPTION --}}
                <div class="card">
                    <div class="card-hd">
                        <div class="card-icon purple">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                        </div>
                        <div class="card-title">Description <span class="opt">(optionnel)</span></div>
                    </div>
                    <div class="card-body">
                        <textarea name="description" class="form-textarea"
                            placeholder="Description du bien, équipements, état général…">{{ old('description') }}</textarea>
                    </div>
                    <div class="submit-bar">
                        <a href="{{ route('admin.biens.index') }}" class="btn-cancel">Annuler</a>
                        <button type="submit" class="btn-submit">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:13px;height:13px"><polyline points="20 6 9 17 4 12"/></svg>
                            Créer le bien
                        </button>
                    </div>
                </div>

            </div>{{-- fin colonne gauche --}}

            {{-- ═══ COLONNE DROITE : RÉCAPITULATIF ═══ --}}
            <div>
                <div class="recap-card">
                    <div class="recap-hd">
                        <div class="recap-title">Récapitulatif fiscal</div>
                    </div>
                    <div class="recap-body">
                        <div class="rp-row">
                            <div class="rp-lbl">Loyer mensuel</div>
                            <div class="rp-val" id="rp-loyer">— F</div>
                        </div>
                        <div class="rp-row">
                            <div class="rp-lbl">Commission HT</div>
                            <div class="rp-val gold" id="rp-comm">— F</div>
                        </div>
                        <div class="rp-row">
                            <div class="rp-lbl">TVA commission (18%)</div>
                            <div class="rp-val" id="rp-tva">— F</div>
                        </div>
                        <div class="rp-row">
                            <div class="rp-lbl">Commission TTC</div>
                            <div class="rp-val gold" id="rp-comm-ttc">— F</div>
                        </div>
                        <div class="rp-total">
                            <div class="rp-total-lbl">Net propriétaire</div>
                            <div class="rp-total-val" id="rp-net">— FCFA</div>
                        </div>

                        <div style="margin-top:16px;padding-top:14px;border-top:1px solid rgba(255,255,255,.07)">
                            <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:rgba(255,255,255,.25);margin-bottom:10px">Rappel fiscal</div>
                            <div style="font-size:11px;color:rgba(255,255,255,.35);line-height:1.6">
                                Commission HT × 18% = TVA (Art. 357 CGI SN)<br>
                                TVA loyer 18% si bail commercial/meublé<br>
                                BRS 15% si locataire entreprise
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>{{-- fin form-grid --}}
    </form>
</div>

<script>
function fmt(n) {
    return Math.round(n).toLocaleString('fr-FR') + ' F';
}

function calcRecap() {
    const loyer   = parseFloat(document.getElementById('loyer_mensuel').value) || 0;
    const taux    = parseFloat(document.getElementById('taux_commission').value) || 0;
    const commHt  = Math.round(loyer * taux / 100);
    const tvaComm = Math.round(commHt * 0.18);
    const commTtc = commHt + tvaComm;
    const net     = loyer - commHt;

    document.getElementById('rp-loyer').textContent    = fmt(loyer);
    document.getElementById('rp-comm').textContent     = fmt(commHt);
    document.getElementById('rp-tva').textContent      = fmt(tvaComm);
    document.getElementById('rp-comm-ttc').textContent = fmt(commTtc);
    document.getElementById('rp-net').textContent      = fmt(net) + 'CFA';
}

function previewPhotos(files) {
    const grid = document.getElementById('preview-grid');
    grid.innerHTML = '';
    if (!files || !files.length) {
        grid.style.display = 'none';
        return;
    }
    grid.style.display = 'grid';
    [...files].forEach((file, i) => {
        const reader = new FileReader();
        reader.onload = e => {
            const div = document.createElement('div');
            div.style.cssText = 'position:relative;border-radius:8px;overflow:hidden;height:90px;border:1px solid #e5e7eb';
            div.innerHTML = `
                <img src="${e.target.result}" style="width:100%;height:100%;object-fit:cover">
                ${i === 0
                    ? '<span style="position:absolute;top:4px;left:4px;background:#c9a84c;color:#0d1117;font-size:9px;font-weight:700;padding:2px 6px;border-radius:4px">Principale</span>'
                    : ''}
            `;
            grid.appendChild(div);
        };
        reader.readAsDataURL(file);
    });
    document.getElementById('drop-zone').style.borderColor = '#c9a84c';
    document.getElementById('drop-zone').style.background  = '#fffbeb';
}

function handleDrop(e) {
    e.preventDefault();
    document.getElementById('drop-zone').style.borderColor = '#e5e7eb';
    document.getElementById('drop-zone').style.background  = '#fafafa';
    const dt    = e.dataTransfer;
    const input = document.getElementById('photos-input');
    // Transfert des fichiers vers l'input (DataTransfer)
    try {
        input.files = dt.files;
    } catch(err) {
        // Fallback si navigateur bloque l'assignation directe
    }
    previewPhotos(dt.files);
}

calcRecap();
</script>
@endsection