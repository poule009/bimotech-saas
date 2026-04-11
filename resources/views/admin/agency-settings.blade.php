<x-app-layout>
    <x-slot name="header">Paramètres agence</x-slot>

<style>
/* ── LAYOUT ── */
.settings-wrap { max-width:1060px; margin:0 auto; padding-bottom:60px; }
.settings-grid { display:grid; grid-template-columns:1fr 300px; gap:22px; align-items:start; }

/* ── CARD ── */
.card {
    background:#161b22;
    border:1px solid rgba(255,255,255,.07);
    border-radius:14px; overflow:hidden; margin-bottom:16px;
}
.card:last-child { margin-bottom:0; }
.card-hd {
    padding:14px 20px;
    border-bottom:1px solid rgba(255,255,255,.06);
    display:flex; align-items:center; gap:10px;
}
.card-icon {
    width:32px; height:32px; border-radius:8px;
    display:flex; align-items:center; justify-content:center; flex-shrink:0;
}
.card-icon svg { width:15px; height:15px; }
.card-icon.gold   { background:rgba(201,168,76,.12); }  .card-icon.gold svg   { color:#c9a84c; }
.card-icon.blue   { background:rgba(59,130,246,.12); }  .card-icon.blue svg   { color:#60a5fa; }
.card-icon.purple { background:rgba(139,92,246,.12); }  .card-icon.purple svg { color:#a78bfa; }
.card-icon.green  { background:rgba(34,197,94,.12);  }  .card-icon.green svg  { color:#4ade80; }
.card-title { font-family:'Syne',sans-serif; font-size:13px; font-weight:700; color:#e6edf3; }
.card-body { padding:18px 20px; }

/* ── FORM ── */
.form-group { margin-bottom:15px; }
.form-group:last-child { margin-bottom:0; }
.form-label { display:block; font-size:12px; font-weight:600; color:#c9d1d9; margin-bottom:5px; }
.req { color:#f87171; margin-left:2px; }
.opt { font-size:11px; font-weight:400; color:#6e7681; margin-left:4px; }
.form-input, .form-select {
    width:100%; padding:9px 12px;
    border:1px solid rgba(255,255,255,.1);
    border-radius:8px;
    font-size:13px; color:#e6edf3;
    font-family:'DM Sans',sans-serif;
    background:#0d1117;
    outline:none; transition:border-color .15s, box-shadow .15s;
}
.form-input::placeholder { color:#484f58; }
.form-input:focus, .form-select:focus {
    border-color:#c9a84c;
    box-shadow:0 0 0 3px rgba(201,168,76,.1);
}
.form-input.error { border-color:#f87171; }
.form-select { color:#e6edf3; }
.form-select option { background:#161b22; }
.form-row { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
.form-error { font-size:11.5px; color:#f87171; margin-top:4px; }
.form-hint  { font-size:11.5px; color:#6e7681; margin-top:5px; line-height:1.5; }
.form-hint.warn { color:#fbbf24; }

/* ── LOGO UPLOAD ── */
.logo-zone {
    display:flex; align-items:center; gap:16px;
    padding:16px;
    background:rgba(255,255,255,.02);
    border:1.5px dashed rgba(255,255,255,.1);
    border-radius:10px; transition:all .2s; cursor:pointer;
}
.logo-zone:hover { border-color:rgba(201,168,76,.4); background:rgba(201,168,76,.03); }
.logo-preview {
    width:64px; height:64px; border-radius:10px; overflow:hidden;
    border:1px solid rgba(255,255,255,.1);
    display:flex; align-items:center; justify-content:center;
    background:rgba(255,255,255,.04); flex-shrink:0;
}
.logo-preview img { width:100%; height:100%; object-fit:contain; }
.logo-preview-placeholder {
    font-family:'Syne',sans-serif; font-size:20px; font-weight:800; color:#c9a84c;
}
.logo-zone-text  { font-size:13px; font-weight:500; color:#c9d1d9; }
.logo-zone-hint  { font-size:11px; color:#6e7681; margin-top:3px; }

/* ── COULEUR ── */
.color-hex-input {
    flex:1; padding:8px 12px;
    border:1px solid rgba(255,255,255,.1);
    border-radius:8px; font-size:13px; font-family:monospace;
    color:#e6edf3; background:#0d1117; outline:none; transition:border-color .15s;
}
.color-hex-input:focus { border-color:#c9a84c; box-shadow:0 0 0 3px rgba(201,168,76,.1); }
.color-swatch {
    width:28px; height:28px; border-radius:6px; cursor:pointer;
    border:2px solid transparent; transition:transform .15s, border-color .15s;
}
.color-swatch:hover { transform:scale(1.15); box-shadow:0 0 0 2px #c9a84c; }
.color-swatch.active { border-color:#fff; box-shadow:0 0 0 2px #c9a84c; }

/* ── APERÇU SIDEBAR ── */
.preview-card {
    background:#0d1117;
    border:1px solid rgba(255,255,255,.07);
    border-radius:14px; overflow:hidden; position:sticky; top:80px;
}
.preview-hd {
    padding:12px 16px; border-bottom:1px solid rgba(255,255,255,.06);
    display:flex; align-items:center; justify-content:space-between;
}
.preview-hd-title {
    font-family:'Syne',sans-serif; font-size:11px; font-weight:700;
    color:rgba(255,255,255,.35); text-transform:uppercase; letter-spacing:1px;
}
.live-dot {
    width:6px; height:6px; border-radius:50%; background:#4ade80;
    box-shadow:0 0 0 3px rgba(74,222,128,.15);
    animation:pulse-green 2s infinite;
}
@keyframes pulse-green {
    0%,100%{box-shadow:0 0 0 3px rgba(74,222,128,.15)}
    50%{box-shadow:0 0 0 6px rgba(74,222,128,.05)}
}

/* mini sidebar */
.mini-sidebar { background:#0d1117; border-radius:10px; overflow:hidden; margin:12px; }
.mini-sidebar-top {
    padding:11px 13px; border-bottom:1px solid rgba(255,255,255,.07);
    display:flex; align-items:center; gap:8px;
}
.mini-logo-box {
    width:26px; height:26px; border-radius:6px;
    display:flex; align-items:center; justify-content:center;
    font-family:'Syne',sans-serif; font-size:10px; font-weight:800;
    color:#fff; flex-shrink:0;
}
.mini-logo-img { width:100%; height:100%; object-fit:contain; border-radius:4px; }
.mini-agency-name {
    font-family:'Syne',sans-serif; font-size:11px; font-weight:700;
    color:#e6edf3; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
}
.mini-nav { padding:8px 6px; }
.mini-nav-item {
    display:flex; align-items:center; gap:7px;
    padding:7px 8px; border-radius:6px; margin-bottom:2px;
}
.mini-nav-dot { width:5px; height:5px; border-radius:50%; background:rgba(255,255,255,.2); flex-shrink:0; }
.mini-nav-line { height:6px; border-radius:4px; background:rgba(255,255,255,.1); flex:1; }
.mini-nav-line.short { max-width:55%; }

/* infos recap */
.info-grid {
    display:flex; flex-direction:column;
    background:rgba(255,255,255,.03); border-radius:8px;
    padding:2px 14px; margin:0 12px 12px;
}
.info-row {
    display:flex; justify-content:space-between; align-items:center;
    padding:9px 0; border-bottom:1px solid rgba(255,255,255,.04);
    font-size:11.5px;
}
.info-row:last-child { border-bottom:none; }
.info-lbl { color:rgba(255,255,255,.3); }
.info-val { color:rgba(255,255,255,.75); font-weight:500; max-width:140px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; text-align:right; }
.info-val.missing { color:#f87171; font-style:italic; }

/* ── CHECKLIST ── */
.checklist { display:flex; flex-direction:column; gap:7px; }
.check-item {
    display:flex; align-items:center; gap:10px;
    padding:9px 12px; border-radius:8px; font-size:12.5px; font-weight:500;
}
.check-item.done    { background:rgba(34,197,94,.07);  color:#4ade80; }
.check-item.pending { background:rgba(234,179,8,.06);  color:#fbbf24; }
.check-icon {
    width:18px; height:18px; border-radius:50%;
    display:flex; align-items:center; justify-content:center;
    flex-shrink:0; font-size:10px; font-weight:800;
}
.check-item.done    .check-icon { background:rgba(34,197,94,.25); color:#4ade80; }
.check-item.pending .check-icon { background:rgba(234,179,8,.2);  color:#fbbf24; }

/* ── SUBMIT ── */
.submit-bar {
    display:flex; align-items:center; gap:10px;
    padding:14px 20px; border-top:1px solid rgba(255,255,255,.06);
    background:rgba(255,255,255,.01);
}
.btn-submit {
    flex:1; display:flex; align-items:center; justify-content:center; gap:7px;
    padding:11px 20px;
    background:linear-gradient(135deg,#c9a84c,#e8c96a);
    color:#080c12; border:none; border-radius:9px;
    font-size:13.5px; font-weight:700; font-family:'DM Sans',sans-serif;
    cursor:pointer; transition:all .2s;
    box-shadow:0 4px 14px rgba(201,168,76,.25);
}
.btn-submit:hover { opacity:.9; transform:translateY(-1px); box-shadow:0 6px 20px rgba(201,168,76,.35); }
.btn-submit svg { width:15px; height:15px; }
.btn-danger-sm {
    display:inline-flex; align-items:center; gap:5px;
    padding:7px 12px;
    background:rgba(239,68,68,.08); color:#f87171;
    border:1px solid rgba(239,68,68,.2); border-radius:7px;
    font-size:12px; font-weight:500; font-family:'DM Sans',sans-serif;
    cursor:pointer; text-decoration:none; transition:all .15s;
}
.btn-danger-sm:hover { background:rgba(239,68,68,.14); border-color:rgba(239,68,68,.35); }
.btn-danger-sm svg { width:12px; height:12px; }

@media(max-width:860px){
    .settings-grid{ grid-template-columns:1fr; }
    .preview-card{ position:static; }
}
@media(max-width:540px){ .form-row{ grid-template-columns:1fr; } }
</style>

<div class="settings-wrap" style="padding:24px 32px 60px">

    {{-- Header --}}
    <div style="margin-bottom:24px">
        <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:#6b7280;margin-bottom:12px">
            <a href="{{ route('admin.dashboard') }}" style="color:#6b7280;text-decoration:none">Tableau de bord</a>
            <svg style="width:12px;height:12px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            <span style="color:#e6edf3;font-weight:500">Paramètres</span>
        </div>
        <h1 style="font-family:'Syne',sans-serif;font-size:22px;font-weight:700;color:#e6edf3;letter-spacing:-.4px">Paramètres de l'agence</h1>
        <p style="font-size:13px;color:#8b949e;margin-top:4px">Identité visuelle, coordonnées et informations légales.</p>
    </div>

    {{-- Flash --}}
    @if(session('success'))
    <div style="background:rgba(34,197,94,.08);border:1px solid rgba(34,197,94,.2);border-radius:10px;padding:13px 18px;margin-bottom:20px;font-size:13px;color:#4ade80;display:flex;align-items:center;gap:10px">
        <svg style="width:15px;height:15px;flex-shrink:0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div style="background:rgba(239,68,68,.07);border:1px solid rgba(239,68,68,.2);border-radius:10px;padding:13px 18px;margin-bottom:20px;font-size:13px;color:#f87171;display:flex;align-items:center;gap:10px">
        <svg style="width:15px;height:15px;flex-shrink:0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        {{ session('error') }}
    </div>
    @endif

    <form method="POST" action="{{ route('admin.agency.settings.update') }}" enctype="multipart/form-data" id="settings-form">
        @csrf
        @method('PATCH')

        <div class="settings-grid">

            {{-- ═══ COLONNE GAUCHE ═══ --}}
            <div>

                {{-- IDENTITÉ VISUELLE --}}
                <div class="card">
                    <div class="card-hd">
                        <div class="card-icon purple">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M8 14s1.5 2 4 2 4-2 4-2"/><line x1="9" y1="9" x2="9.01" y2="9"/><line x1="15" y1="9" x2="15.01" y2="9"/></svg>
                        </div>
                        <div class="card-title">Identité visuelle</div>
                    </div>
                    <div class="card-body">

                        {{-- LOGO --}}
                        <div class="form-group">
                            <label class="form-label">Logo <span class="opt">PNG, JPG · max 2 Mo</span></label>
                            <div class="logo-zone" id="logo-zone" onclick="document.getElementById('logo-input').click()">
                                <div class="logo-preview" id="logo-preview">
                                    @if($agency->logo_path)
                                        <img src="{{ Storage::url($agency->logo_path) }}" alt="{{ $agency->name }}" id="logo-preview-img">
                                    @else
                                        <div class="logo-preview-placeholder" id="logo-preview-placeholder">
                                            {{ strtoupper(substr($agency->name, 0, 2)) }}
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <div class="logo-zone-text">Cliquer pour changer le logo</div>
                                    <div class="logo-zone-hint">Recommandé : 200×200 px · fond transparent</div>
                                </div>
                                <input type="file" name="logo" id="logo-input"
                                    accept="image/png,image/jpeg,image/webp"
                                    style="display:none"
                                    onchange="previewLogo(this)">
                            </div>
                            @error('logo')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                            @if($agency->logo_path)
                            <button type="button" class="btn-danger-sm" style="margin-top:8px"
                                    onclick="if(confirm('Supprimer le logo ?')) document.getElementById('form-delete-logo').submit()">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/></svg>
                                Supprimer le logo
                            </button>
                            @endif
                        </div>

                        {{-- COULEUR --}}
                        <div class="form-group">
                            <label class="form-label" for="couleur_primaire">Couleur principale</label>
                            <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px">
                                <input type="color" id="color-native"
                                    value="{{ old('couleur_primaire', $agency->couleur_primaire ?? '#c9a84c') }}"
                                    oninput="syncColor(this.value)"
                                    style="width:42px;height:38px;border:1px solid rgba(255,255,255,.1);border-radius:8px;cursor:pointer;padding:3px;background:#0d1117">
                                <input type="text" name="couleur_primaire" id="couleur_primaire"
                                    value="{{ old('couleur_primaire', $agency->couleur_primaire ?? '#c9a84c') }}"
                                    placeholder="#c9a84c" maxlength="7"
                                    class="color-hex-input {{ $errors->has('couleur_primaire') ? 'error':'' }}"
                                    oninput="syncColorFromHex(this.value)">
                            </div>
                            <div style="display:flex;gap:6px;flex-wrap:wrap">
                                @foreach(['#c9a84c','#3b82f6','#22c55e','#ef4444','#8b5cf6','#0d1117','#06b6d4','#f59e0b','#1a3c5e','#ec4899'] as $color)
                                <div class="color-swatch {{ ($agency->couleur_primaire ?? '#c9a84c') === $color ? 'active':'' }}"
                                     style="background:{{ $color }}"
                                     onclick="syncColor('{{ $color }}')"
                                     title="{{ $color }}"></div>
                                @endforeach
                            </div>
                            @error('couleur_primaire')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                            <div class="form-hint">Utilisée pour la sidebar, les badges et les accents.</div>
                        </div>

                    </div>
                </div>

                {{-- INFORMATIONS GÉNÉRALES --}}
                <div class="card">
                    <div class="card-hd">
                        <div class="card-icon blue">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/></svg>
                        </div>
                        <div class="card-title">Informations de l'agence</div>
                    </div>
                    <div class="card-body">

                        <div class="form-group">
                            <label class="form-label" for="name">Nom de l'agence <span class="req">*</span></label>
                            <input type="text" name="name" id="name"
                                class="form-input {{ $errors->has('name') ? 'error':'' }}"
                                value="{{ old('name', $agency->name) }}"
                                placeholder="Ex : Immobilière Dakar"
                                oninput="updatePreview()">
                            @error('name')<div class="form-error">{{ $message }}</div>@enderror
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label" for="email">Email <span class="req">*</span></label>
                                <input type="email" name="email" id="email"
                                    class="form-input {{ $errors->has('email') ? 'error':'' }}"
                                    value="{{ old('email', $agency->email) }}"
                                    placeholder="contact@agence.sn"
                                    oninput="updatePreview()">
                                @error('email')<div class="form-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="telephone">Téléphone</label>
                                <input type="text" name="telephone" id="telephone"
                                    class="form-input"
                                    value="{{ old('telephone', $agency->telephone) }}"
                                    placeholder="+221 77 XXX XX XX"
                                    oninput="updatePreview()">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="adresse">Adresse <span class="opt">optionnel</span></label>
                            <input type="text" name="adresse" id="adresse"
                                class="form-input"
                                value="{{ old('adresse', $agency->adresse) }}"
                                placeholder="Ex : 12 Avenue Cheikh Anta Diop, Dakar">
                        </div>

                    </div>
                </div>

                {{-- INFORMATIONS LÉGALES --}}
                <div class="card">
                    <div class="card-hd">
                        <div class="card-icon gold">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        </div>
                        <div class="card-title">Informations légales</div>
                    </div>
                    <div class="card-body">

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label" for="ninea">NINEA <span class="opt">Numéro fiscal</span></label>
                                <input type="text" name="ninea" id="ninea"
                                    class="form-input {{ $errors->has('ninea') ? 'error':'' }}"
                                    value="{{ old('ninea', $agency->ninea) }}"
                                    placeholder="00123456789"
                                    maxlength="30"
                                    oninput="updatePreview()">
                                @error('ninea')<div class="form-error">{{ $message }}</div>@enderror
                                @if(!$agency->ninea)
                                    <div class="form-hint warn">⚠ Requis pour les quittances conformes</div>
                                @endif
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="rccm">RCCM <span class="opt">Registre de commerce</span></label>
                                <input type="text" name="rccm" id="rccm"
                                    class="form-input"
                                    value="{{ old('rccm', $agency->rccm ?? '') }}"
                                    placeholder="SN-DKR-2024-XXX"
                                    maxlength="50">
                            </div>
                        </div>

                        <div class="form-hint">Ces informations apparaissent sur toutes les quittances PDF.</div>

                    </div>
                    <div class="submit-bar">
                        <button type="submit" class="btn-submit">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                            Sauvegarder les paramètres
                        </button>
                    </div>
                </div>

            </div>{{-- /colonne gauche --}}

            {{-- ═══ COLONNE DROITE ═══ --}}
            <div>

                {{-- APERÇU --}}
                <div class="preview-card">
                    <div class="preview-hd">
                        <div class="preview-hd-title">Aperçu sidebar</div>
                        <div class="live-dot" title="Aperçu en direct"></div>
                    </div>

                    <div style="padding:12px">
                        <div class="mini-sidebar">
                            <div class="mini-sidebar-top">
                                <div class="mini-logo-box" id="preview-logo-box"
                                     style="background:{{ $agency->couleur_primaire ?? '#c9a84c' }}">
                                    @if($agency->logo_path)
                                        <img src="{{ Storage::url($agency->logo_path) }}"
                                             class="mini-logo-img" id="preview-logo-img" alt="">
                                    @else
                                        <span id="preview-logo-initials">{{ strtoupper(substr($agency->name, 0, 2)) }}</span>
                                    @endif
                                </div>
                                <div style="flex:1;min-width:0">
                                    <div class="mini-agency-name" id="preview-name">{{ $agency->name }}</div>
                                    <div style="font-size:9px;color:rgba(255,255,255,.25);text-transform:uppercase;letter-spacing:.7px;margin-top:1px">Admin</div>
                                </div>
                            </div>
                            <div class="mini-nav">
                                <div class="mini-nav-item" id="preview-active-item"
                                     style="background:rgba(201,168,76,.13)">
                                    <div class="mini-nav-dot" id="preview-active-dot"
                                         style="background:#c9a84c;box-shadow:0 0 0 3px rgba(201,168,76,.2)"></div>
                                    <div class="mini-nav-line" style="background:#c9a84c;opacity:.5"></div>
                                </div>
                                @foreach([0,1,2,3] as $i)
                                <div class="mini-nav-item">
                                    <div class="mini-nav-dot"></div>
                                    <div class="mini-nav-line {{ $i % 2 ? 'short':'' }}"></div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="info-grid">
                        <div class="info-row">
                            <span class="info-lbl">Nom</span>
                            <span class="info-val" id="info-name">{{ $agency->name }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-lbl">Email</span>
                            <span class="info-val" id="info-email">{{ $agency->email ?? '—' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-lbl">Téléphone</span>
                            <span class="info-val {{ !$agency->telephone ? 'missing':'' }}" id="info-tel">
                                {{ $agency->telephone ?? 'Non renseigné' }}
                            </span>
                        </div>
                        <div class="info-row">
                            <span class="info-lbl">NINEA</span>
                            <span class="info-val {{ !$agency->ninea ? 'missing':'' }}" id="info-ninea">
                                {{ $agency->ninea ?? 'Non renseigné' }}
                            </span>
                        </div>
                        <div class="info-row">
                            <span class="info-lbl">Couleur</span>
                            <div style="display:flex;align-items:center;gap:6px;justify-content:flex-end">
                                <div id="info-color-dot" style="width:11px;height:11px;border-radius:3px;background:{{ $agency->couleur_primaire ?? '#c9a84c' }}"></div>
                                <span class="info-val" id="info-color">{{ $agency->couleur_primaire ?? '#c9a84c' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CHECKLIST --}}
                <div class="card" style="margin-top:16px">
                    <div class="card-hd">
                        <div class="card-icon green">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
                        </div>
                        <div class="card-title">Configuration</div>
                    </div>
                    <div class="card-body">
                        @php
                            $checks = [
                                ['label' => 'Nom de l\'agence', 'done' => !empty($agency->name)],
                                ['label' => 'Email de contact', 'done' => !empty($agency->email)],
                                ['label' => 'Téléphone',        'done' => !empty($agency->telephone)],
                                ['label' => 'Adresse physique', 'done' => !empty($agency->adresse)],
                                ['label' => 'NINEA fiscal',     'done' => !empty($agency->ninea)],
                                ['label' => 'Logo uploadé',     'done' => !empty($agency->logo_path)],
                                ['label' => 'Couleur définie',  'done' => !empty($agency->couleur_primaire)],
                            ];
                            $nbDone = collect($checks)->where('done', true)->count();
                            $pct    = round($nbDone / count($checks) * 100);
                        @endphp

                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px">
                            <span style="font-size:12px;font-weight:600;color:#c9d1d9">{{ $nbDone }}/{{ count($checks) }} complété{{ $nbDone > 1 ? 's':'' }}</span>
                            <span style="font-size:11px;color:{{ $pct === 100 ? '#4ade80':'#fbbf24' }};font-weight:700">{{ $pct }}%</span>
                        </div>
                        {{-- Barre de progression --}}
                        <div style="height:4px;background:rgba(255,255,255,.06);border-radius:99px;margin-bottom:14px;overflow:hidden">
                            <div style="height:100%;width:{{ $pct }}%;background:{{ $pct === 100 ? '#4ade80':'#c9a84c' }};border-radius:99px;transition:width .3s"></div>
                        </div>
                        <div class="checklist">
                            @foreach($checks as $check)
                            <div class="check-item {{ $check['done'] ? 'done':'pending' }}">
                                <div class="check-icon">
                                    @if($check['done'])
                                    <svg width="9" height="9" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="2,6 5,9 10,3"/></svg>
                                    @else
                                    !
                                    @endif
                                </div>
                                {{ $check['label'] }}
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

            </div>{{-- /colonne droite --}}

        </div>{{-- /settings-grid --}}
    </form>

</div>

{{-- Form suppression logo — hors du form principal pour éviter l'imbrication HTML --}}
@if($agency->logo_path)
<form id="form-delete-logo" method="POST" action="{{ route('admin.agency.logo.delete') }}" style="display:none">
    @csrf
    @method('DELETE')
</form>
@endif

<script>
function syncColor(hex) {
    if (!hex.startsWith('#')) hex = '#' + hex;
    document.getElementById('couleur_primaire').value  = hex;
    document.getElementById('color-native').value      = hex;

    const box      = document.getElementById('preview-logo-box');
    const activeItem = document.getElementById('preview-active-item');
    const activeDot  = document.getElementById('preview-active-dot');
    const r = parseInt(hex.slice(1,3),16);
    const g = parseInt(hex.slice(3,5),16);
    const b = parseInt(hex.slice(5,7),16);

    box.style.background          = hex;
    activeItem.style.background   = `rgba(${r},${g},${b},.13)`;
    activeDot.style.background    = hex;
    activeDot.style.boxShadow     = `0 0 0 3px rgba(${r},${g},${b},.2)`;
    activeDot.nextElementSibling.style.background = hex;

    document.getElementById('info-color-dot').style.background = hex;
    document.getElementById('info-color').textContent = hex;

    document.querySelectorAll('.color-swatch').forEach(s => {
        const sw = rgbToHex(s.style.background);
        s.classList.toggle('active', sw === hex || s.style.background === hex);
    });
}

function syncColorFromHex(val) {
    if (/^#[0-9A-Fa-f]{6}$/.test(val)) syncColor(val);
}

function rgbToHex(rgb) {
    const m = rgb.match(/\d+/g);
    if (!m) return '';
    return '#' + m.slice(0,3).map(x => parseInt(x).toString(16).padStart(2,'0')).join('');
}

function updatePreview() {
    const name  = document.getElementById('name').value  || '—';
    const email = document.getElementById('email').value || '—';
    const tel   = document.getElementById('telephone').value;
    const ninea = document.getElementById('ninea').value;

    document.getElementById('preview-name').textContent = name;
    document.getElementById('info-name').textContent    = name;
    document.getElementById('info-email').textContent   = email;

    const telEl = document.getElementById('info-tel');
    telEl.textContent = tel || 'Non renseigné';
    telEl.classList.toggle('missing', !tel);

    const nineaEl = document.getElementById('info-ninea');
    nineaEl.textContent = ninea || 'Non renseigné';
    nineaEl.classList.toggle('missing', !ninea);

    const initEl = document.getElementById('preview-logo-initials');
    if (initEl) initEl.textContent = name.substring(0,2).toUpperCase();
}

function previewLogo(input) {
    if (!input.files || !input.files[0]) return;
    const reader = new FileReader();
    reader.onload = e => {
        const box  = document.getElementById('preview-logo-box');
        const zone = document.querySelector('.logo-preview');

        let sidebarImg = document.getElementById('preview-logo-img');
        if (!sidebarImg) {
            const initials = document.getElementById('preview-logo-initials');
            if (initials) initials.remove();
            sidebarImg = document.createElement('img');
            sidebarImg.id = 'preview-logo-img';
            sidebarImg.className = 'mini-logo-img';
            box.appendChild(sidebarImg);
        }
        sidebarImg.src = e.target.result;

        let zoneImg = document.getElementById('logo-preview-img');
        if (!zoneImg) {
            const ph = document.getElementById('logo-preview-placeholder');
            if (ph) ph.remove();
            zoneImg = document.createElement('img');
            zoneImg.id = 'logo-preview-img';
            zoneImg.style.cssText = 'width:100%;height:100%;object-fit:contain';
            zone.appendChild(zoneImg);
        }
        zoneImg.src = e.target.result;
    };
    reader.readAsDataURL(input.files[0]);
}

updatePreview();
syncColor(document.getElementById('couleur_primaire').value);
</script>

</x-app-layout>
